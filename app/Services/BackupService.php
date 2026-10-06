<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;
use ZipArchive;

class BackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Get list of all existing backup files with metadata.
     */
    public function getBackupsList(): array
    {
        if (!File::exists($this->backupDir)) {
            return [];
        }

        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $extension = strtolower($file->getExtension());

            // Determine backup type
            $type = 'unknown';
            if (str_starts_with($filename, 'db_backup_') || $extension === 'sql') {
                $type = 'database';
            } elseif (str_starts_with($filename, 'storage_backup_') || $extension === 'zip') {
                $type = 'storage';
            }

            $sizeBytes = $file->getSize();
            $createdAt = $file->getMTime();

            $backups[] = [
                'filename'       => $filename,
                'type'           => $type,
                'size_bytes'     => $sizeBytes,
                'size_formatted' => $this->formatBytes($sizeBytes),
                'created_at'     => date('Y-m-d H:i:s', $createdAt),
                'created_human'  => \Carbon\Carbon::createFromTimestamp($createdAt)->diffForHumans(),
                'timestamp'      => $createdAt,
            ];
        }

        // Sort newest first
        usort($backups, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $backups;
    }

    /**
     * Get summary metrics for the backup hub.
     */
    public function getBackupStats(): array
    {
        $list = $this->getBackupsList();
        $totalSize = array_sum(array_column($list, 'size_bytes'));
        $lastBackup = $list[0] ?? null;

        return [
            'total_count'    => count($list),
            'total_size'     => $this->formatBytes($totalSize),
            'last_backup'    => $lastBackup,
            'last_backup_at' => $lastBackup ? $lastBackup['created_at'] : null,
            'last_backup_human' => $lastBackup ? $lastBackup['created_human'] : __('No backups created yet'),
        ];
    }

    /**
     * Generate an instant, standalone Database Backup (.sql file).
     */
    public function createDatabaseBackup(): array
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "db_backup_{$database}_{$timestamp}.sql";
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        // Try using pure PHP PDO database exporter
        $pdo = DB::connection()->getPdo();
        $tables = $this->getAllTables();

        $handle = fopen($filepath, 'w+');
        if (!$handle) {
            throw new \Exception(__('Failed to create backup file on disk.'));
        }

        // Write SQL Header
        $header = "-- ========================================================\n";
        $header .= "-- Fly Vio / My-Trip Database Backup\n";
        $header .= "-- Generated at: " . date('Y-m-d H:i:s') . "\n";
        $header .= "-- Database: {$database}\n";
        $header .= "-- Laravel Version: " . app()->version() . "\n";
        $header .= "-- PHP Version: " . PHP_VERSION . "\n";
        $header .= "-- ========================================================\n\n";
        $header .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $header .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $header .= "SET time_zone = \"+00:00\";\n\n";
        fwrite($handle, $header);

        foreach ($tables as $table) {
            // Get CREATE TABLE definition
            $createTableQuery = DB::select("SHOW CREATE TABLE `{$table}`");
            if (!empty($createTableQuery)) {
                $createTableSql = $createTableQuery[0]->{'Create Table'} ?? '';
                fwrite($handle, "\n-- --------------------------------------------------------\n");
                fwrite($handle, "-- Table structure for table `{$table}`\n");
                fwrite($handle, "-- --------------------------------------------------------\n\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                fwrite($handle, $createTableSql . ";\n\n");
            }

            // Dump Data in chunks
            $totalRows = DB::table($table)->count();
            if ($totalRows > 0) {
                fwrite($handle, "-- Dumping data for table `{$table}` ({$totalRows} rows)\n");

                DB::table($table)->orderByRaw('1')->chunk(500, function ($rows) use ($handle, $table) {
                    $insertSql = "INSERT INTO `{$table}` VALUES \n";
                    $valuesArr = [];

                    foreach ($rows as $row) {
                        $values = [];
                        foreach ((array) $row as $val) {
                            if (is_null($val)) {
                                $values[] = "NULL";
                            } elseif (is_numeric($val)) {
                                $values[] = $val;
                            } else {
                                $escaped = addslashes((string) $val);
                                $escaped = str_replace(["\r", "\n"], ["\\r", "\\n"], $escaped);
                                $values[] = "'{$escaped}'";
                            }
                        }
                        $valuesArr[] = "(" . implode(', ', $values) . ")";
                    }

                    $insertSql .= implode(",\n", $valuesArr) . ";\n\n";
                    fwrite($handle, $insertSql);
                });
            }
        }

        // SQL Footer
        $footer = "\nSET FOREIGN_KEY_CHECKS=1;\n";
        $footer .= "-- End of Database Backup\n";
        fwrite($handle, $footer);
        fclose($handle);

        $fileSize = filesize($filepath);
        Setting::set('last_db_backup_at', now()->toDateTimeString());

        return [
            'success'   => true,
            'filename'  => $filename,
            'size'      => $this->formatBytes($fileSize),
            'tables'    => count($tables),
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    /**
     * Create a Storage Files backup (.zip).
     */
    public function createStorageBackup(): array
    {
        if (!class_exists('ZipArchive')) {
            throw new \Exception(__('ZipArchive PHP extension is not installed on this server.'));
        }

        $timestamp = date('Y-m-d_H-i-s');
        $filename = "storage_backup_{$timestamp}.zip";
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        $zip = new ZipArchive();
        if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception(__('Cannot create zip archive.'));
        }

        $storagePublicPath = storage_path('app/public');
        if (File::exists($storagePublicPath)) {
            $files = File::allFiles($storagePublicPath);
            foreach ($files as $file) {
                $relativePath = 'public/' . $file->getRelativePathname();
                $zip->addFile($file->getRealPath(), $relativePath);
            }
        }

        $zip->close();
        $fileSize = filesize($filepath);

        return [
            'success'   => true,
            'filename'  => $filename,
            'size'      => $this->formatBytes($fileSize),
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    /**
     * Delete a backup file.
     */
    public function deleteBackup(string $filename): bool
    {
        // Prevent directory traversal
        $filename = basename($filename);
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        if (File::exists($filepath)) {
            return File::delete($filepath);
        }

        return false;
    }

    /**
     * Get absolute path for download.
     */
    public function getBackupPath(string $filename): ?string
    {
        $filename = basename($filename);
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        return File::exists($filepath) ? $filepath : null;
    }

    /**
     * Retrieve all database tables.
     */
    protected function getAllTables(): array
    {
        $tables = [];
        $driver = config('database.default');

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $dbName = DB::connection()->getDatabaseName();
            $results = DB::select('SHOW TABLES');
            $key = "Tables_in_{$dbName}";

            foreach ($results as $res) {
                if (isset($res->$key)) {
                    $tables[] = $res->$key;
                } else {
                    $arr = (array) $res;
                    $tables[] = reset($arr);
                }
            }
        } elseif ($driver === 'sqlite') {
            $results = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($results as $res) {
                $tables[] = $res->name;
            }
        }

        return $tables;
    }

    /**
     * Format bytes to readable unit.
     */
    protected function formatBytes(float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
