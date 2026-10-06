<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Models\Setting;

class SystemHealthService
{
    /**
     * Get complete health snapshot of the application and server.
     */
    public function getFullHealthReport(): array
    {
        return [
            'overview'       => $this->getGlobalStatus(),
            'server'         => $this->getServerMetrics(),
            'database'       => $this->getDatabaseHealth(),
            'cache'          => $this->getCacheHealth(),
            'storage'        => $this->getStorageHealth(),
            'queue'          => $this->getQueueHealth(),
            'services'       => $this->getBusinessServicesHealth(),
            'environment'    => $this->getEnvironmentInfo(),
            'maintenance'    => $this->getMaintenanceModeStatus(),
            'logs_summary'   => $this->getLogsSummary(),
        ];
    }

    /**
     * Compute overall system status (Healthy, Warning, Critical).
     */
    public function getGlobalStatus(): array
    {
        $db = $this->getDatabaseHealth();
        $storage = $this->getStorageHealth();
        $queue = $this->getQueueHealth();
        $isDown = app()->isDownForMaintenance();

        if ($isDown) {
            return [
                'status'  => 'maintenance',
                'label'   => __('Maintenance Mode Active'),
                'badge'   => 'bg-warning text-dark',
                'icon'    => 'fas fa-tools',
                'message' => __('The system is currently in maintenance mode for regular users.'),
            ];
        }

        if (!$db['connected'] || $storage['free_percent'] < 5) {
            return [
                'status'  => 'critical',
                'label'   => __('Critical Issues Detected'),
                'badge'   => 'bg-danger text-white',
                'icon'    => 'fas fa-exclamation-triangle',
                'message' => __('One or more core components are failing or storage is critically low.'),
            ];
        }

        if ($storage['free_percent'] < 15 || $queue['failed_count'] > 20 || $db['latency_ms'] > 500) {
            return [
                'status'  => 'warning',
                'label'   => __('Degraded Performance / Attention Needed'),
                'badge'   => 'bg-warning text-dark',
                'icon'    => 'fas fa-exclamation-circle',
                'message' => __('System is functional but some metrics require administrator attention.'),
            ];
        }

        return [
            'status'  => 'healthy',
            'label'   => __('All Systems Operational'),
            'badge'   => 'bg-success text-white',
            'icon'    => 'fas fa-check-circle',
            'message' => __('All core services, database, cache, and storage are operating normally.'),
        ];
    }

    /**
     * Server CPU, RAM & Disk Metrics.
     */
    public function getServerMetrics(): array
    {
        // Disk metrics
        $diskPath = base_path();
        $totalBytes = @disk_total_space($diskPath) ?: 1;
        $freeBytes  = @disk_free_space($diskPath) ?: 0;
        $usedBytes  = $totalBytes - $freeBytes;
        $usedPercent = round(($usedBytes / $totalBytes) * 100, 1);
        $freePercent = 100 - $usedPercent;

        // Memory metrics
        $memoryUsage = memory_get_usage(true);
        $peakMemory  = memory_get_peak_usage(true);
        $memoryLimit = ini_get('memory_limit');

        // CPU load if available (Linux) or Windows fallback
        $cpuLoad = null;
        if (function_exists('sys_getloadavg') && is_array(sys_getloadavg())) {
            $loads = sys_getloadavg();
            $cpuLoad = round($loads[0], 2);
        }

        return [
            'disk_total'       => $this->formatBytes($totalBytes),
            'disk_free'        => $this->formatBytes($freeBytes),
            'disk_used'        => $this->formatBytes($usedBytes),
            'disk_used_pct'    => $usedPercent,
            'disk_free_pct'    => $freePercent,
            'memory_used'      => $this->formatBytes($memoryUsage),
            'memory_peak'      => $this->formatBytes($peakMemory),
            'memory_limit'     => $memoryLimit,
            'cpu_load'         => $cpuLoad,
            'php_version'      => PHP_VERSION,
            'server_software'  => $_SERVER['SERVER_SOFTWARE'] ?? 'CLI / Standalone',
            'os'               => PHP_OS . ' (' . php_uname('m') . ')',
            'server_time'      => now()->format('Y-m-d H:i:s T'),
            'timezone'         => config('app.timezone'),
        ];
    }

    /**
     * Database connectivity, latency, size, and tables.
     */
    public function getDatabaseHealth(): array
    {
        $start = microtime(true);
        $connected = false;
        $error = null;
        $dbSize = 'Unknown';
        $tablesCount = 0;
        $driver = config('database.default');

        try {
            DB::connection()->getPdo();
            $connected = true;

            // Simple fast query for latency
            DB::select('SELECT 1');
            $latency = round((microtime(true) - $start) * 1000, 2);

            // MySQL-specific extra stats
            if ($driver === 'mysql' || $driver === 'mariadb') {
                $dbName = DB::connection()->getDatabaseName();
                $tables = DB::select('SHOW TABLES');
                $tablesCount = count($tables);

                $sizeResult = DB::select("
                    SELECT SUM(data_length + index_length) AS size 
                    FROM information_schema.TABLES 
                    WHERE table_schema = ?
                ", [$dbName]);

                if (!empty($sizeResult) && isset($sizeResult[0]->size)) {
                    $dbSize = $this->formatBytes((float) $sizeResult[0]->size);
                }
            } elseif ($driver === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                if (file_exists($dbPath)) {
                    $dbSize = $this->formatBytes(filesize($dbPath));
                }
            }
        } catch (\Throwable $e) {
            $connected = false;
            $latency = 0;
            $error = $e->getMessage();
        }

        return [
            'connected'    => $connected,
            'latency_ms'   => $latency ?? 0,
            'driver'       => $driver,
            'database'     => config("database.connections.{$driver}.database") ?? 'N/A',
            'tables_count' => $tablesCount,
            'size'         => $dbSize,
            'error'        => $error,
            'status'       => $connected ? ($latency < 200 ? 'good' : 'warning') : 'critical',
        ];
    }

    /**
     * Cache driver and read/write test.
     */
    public function getCacheHealth(): array
    {
        $driver = config('cache.default');
        $start = microtime(true);
        $writable = false;
        $readable = false;
        $error = null;
        $testKey = 'system_health_probe_' . time();

        try {
            Cache::put($testKey, 'health_ok', 10);
            $writable = true;

            $value = Cache::get($testKey);
            $readable = ($value === 'health_ok');
            Cache::forget($testKey);

            $latency = round((microtime(true) - $start) * 1000, 2);
        } catch (\Throwable $e) {
            $latency = 0;
            $error = $e->getMessage();
        }

        return [
            'driver'     => $driver,
            'writable'   => $writable,
            'readable'   => $readable,
            'latency_ms' => $latency ?? 0,
            'error'      => $error,
            'status'     => ($writable && $readable) ? 'good' : 'critical',
        ];
    }

    /**
     * Storage paths and symlinks health.
     */
    public function getStorageHealth(): array
    {
        $diskPath = base_path();
        $totalBytes = @disk_total_space($diskPath) ?: 1;
        $freeBytes  = @disk_free_space($diskPath) ?: 0;
        $usedBytes  = $totalBytes - $freeBytes;
        $freePercent = round(($freeBytes / $totalBytes) * 100, 1);

        $storageWritable   = is_writable(storage_path());
        $bootstrapWritable = is_writable(base_path('bootstrap/cache'));
        $publicStorageLink = public_path('storage');
        $symlinkValid      = is_link($publicStorageLink) || is_dir($publicStorageLink);

        return [
            'free_percent'       => $freePercent,
            'storage_writable'   => $storageWritable,
            'bootstrap_writable' => $bootstrapWritable,
            'symlink_valid'      => $symlinkValid,
            'status'             => ($storageWritable && $bootstrapWritable && $freePercent > 10) ? 'good' : 'warning',
        ];
    }

    /**
     * Queue workers & failed jobs metrics.
     */
    public function getQueueHealth(): array
    {
        $driver = config('queue.default');
        $failedCount = 0;
        $pendingCount = 0;

        try {
            if (DB::getSchemaBuilder()->hasTable('failed_jobs')) {
                $failedCount = DB::table('failed_jobs')->count();
            }
            if ($driver === 'database' && DB::getSchemaBuilder()->hasTable('jobs')) {
                $pendingCount = DB::table('jobs')->count();
            }
        } catch (\Throwable $e) {
            // ignore if tables not present
        }

        return [
            'driver'        => $driver,
            'failed_count'  => $failedCount,
            'pending_count' => $pendingCount,
            'status'        => $failedCount > 0 ? ($failedCount > 20 ? 'critical' : 'warning') : 'good',
        ];
    }

    /**
     * Business & Platform Specific Services Health (Payments, Mail, SMS, Insurance).
     */
    public function getBusinessServicesHealth(): array
    {
        // 1. Mail Service Check
        $mailHost = config('mail.mailers.smtp.host');
        $mailPort = config('mail.mailers.smtp.port');
        $mailDriver = config('mail.default');
        $mailStatus = (!empty($mailHost) || $mailDriver === 'log' || $mailDriver === 'array') ? 'configured' : 'not_configured';

        // 2. Payments Gateways
        $madaEnabled         = (bool) Setting::get('payment_mada_enabled', true);
        $visaEnabled         = (bool) Setting::get('payment_visa_master_enabled', true);
        $applePayEnabled     = (bool) Setting::get('payment_apple_pay_enabled', true);
        $tabbyEnabled        = (bool) Setting::get('payment_tabby_enabled', false);
        $tamaraEnabled       = (bool) Setting::get('payment_tamara_enabled', false);
        $bankTransferEnabled = (bool) Setting::get('payment_bank_transfer_enabled', true);

        // 3. Travel Insurance API check
        $insuranceApiConfigured = !empty(config('services.insurance.api_key')) 
            || !empty(Setting::get('insurance_api_key'))
            || !empty(Setting::get('insurance_client_id'));

        return [
            'mail' => [
                'driver' => $mailDriver,
                'host'   => $mailHost ?: 'Local Log / Array',
                'port'   => $mailPort ?: '25/587',
                'status' => $mailStatus === 'configured' ? 'good' : 'warning',
            ],
            'payments' => [
                'mada'          => $madaEnabled,
                'visa_master'   => $visaEnabled,
                'apple_pay'     => $applePayEnabled,
                'tabby'         => $tabbyEnabled,
                'tamara'        => $tamaraEnabled,
                'bank_transfer' => $bankTransferEnabled,
                'active_count'  => count(array_filter([$madaEnabled, $visaEnabled, $applePayEnabled, $tabbyEnabled, $tamaraEnabled, $bankTransferEnabled])),
            ],
            'insurance_api' => [
                'configured' => $insuranceApiConfigured,
                'status'     => $insuranceApiConfigured ? 'good' : 'info',
            ],
        ];
    }

    /**
     * Environment information.
     */
    public function getEnvironmentInfo(): array
    {
        return [
            'app_name'        => config('app.name'),
            'app_env'         => config('app.env'),
            'app_debug'       => config('app.debug'),
            'app_url'         => config('app.url'),
            'laravel_version' => app()->version(),
            'php_version'     => PHP_VERSION,
            'max_execution'   => ini_get('max_execution_time') . 's',
            'upload_max'      => ini_get('upload_max_filesize'),
            'post_max'        => ini_get('post_max_size'),
            'extensions'      => [
                'pdo'       => extension_loaded('pdo'),
                'mbstring'  => extension_loaded('mbstring'),
                'openssl'   => extension_loaded('openssl'),
                'curl'      => extension_loaded('curl'),
                'gd'        => extension_loaded('gd'),
                'zip'       => extension_loaded('zip'),
                'fileinfo'  => extension_loaded('fileinfo'),
                'redis'     => extension_loaded('redis'),
                'bcmath'    => extension_loaded('bcmath'),
            ],
        ];
    }

    /**
     * Maintenance mode detailed status.
     */
    public function getMaintenanceModeStatus(): array
    {
        $isDown = app()->isDownForMaintenance();
        $downData = [];

        $downFile = storage_path('framework/down');
        if (file_exists($downFile)) {
            $content = @file_get_contents($downFile);
            $downData = json_decode($content, true) ?: [];
        }

        return [
            'is_down'       => $isDown,
            'secret'        => $downData['secret'] ?? Setting::get('auth_maintenance_secret', null),
            'message'       => $downData['message'] ?? null,
            'retry'         => $downData['retry'] ?? null,
            'allowed_ips'   => $downData['allowed'] ?? [],
            'activated_at'  => isset($downData['time']) ? date('Y-m-d H:i:s', $downData['time']) : null,
        ];
    }

    /**
     * Log file size and error count summary.
     */
    public function getLogsSummary(): array
    {
        $logFile = storage_path('logs/laravel.log');
        $exists = file_exists($logFile);
        $size = $exists ? filesize($logFile) : 0;
        $lastModified = $exists ? filemtime($logFile) : null;

        $errorCount = 0;
        if ($exists && $size > 0 && $size < 10485760) { // If less than 10MB, count occurrences
            $content = @file_get_contents($logFile);
            $errorCount = substr_count($content, '.ERROR:') + substr_count($content, '.CRITICAL:');
        }

        return [
            'exists'        => $exists,
            'size'          => $this->formatBytes($size),
            'raw_size'      => $size,
            'last_modified' => $lastModified ? date('Y-m-d H:i:s', $lastModified) : 'N/A',
            'error_count'   => $errorCount,
        ];
    }

    /**
     * Parse recent log entries for the log viewer.
     */
    public function getParsedLogs(int $limit = 60, ?string $filterLevel = null): array
    {
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            return [];
        }

        // Read last 2MB or whole file
        $fileSize = filesize($logFile);
        $readBytes = min($fileSize, 2097152); // max 2MB
        $fp = fopen($logFile, 'r');
        if ($fileSize > $readBytes) {
            fseek($fp, $fileSize - $readBytes);
        }
        $content = fread($fp, $readBytes);
        fclose($fp);

        // Regex to extract standard Laravel log entries: [YYYY-MM-DD HH:MM:SS] env.LEVEL: message
        $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+([a-zA-Z0-9_\-]+)\.([A-Z]+):\s+(.*?)(?=\n\[\d{4}-\d{2}-\d{2}|\Z)/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $logs = [];
        foreach (array_reverse($matches) as $match) {
            $timestamp = $match[1];
            $env       = $match[2];
            $level     = strtoupper($match[3]);
            $body      = trim($match[4]);

            if ($filterLevel && $level !== strtoupper($filterLevel)) {
                continue;
            }

            // Split first line (message) and stack trace
            $lines = explode("\n", $body, 2);
            $message = $lines[0] ?? '';
            $stackTrace = $lines[1] ?? '';

            $logs[] = [
                'timestamp'   => $timestamp,
                'environment' => $env,
                'level'       => $level,
                'message'     => $message,
                'trace'       => $stackTrace,
                'badge'       => match ($level) {
                    'EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR' => 'badge-danger',
                    'WARNING' => 'badge-warning',
                    'NOTICE', 'INFO' => 'badge-info',
                    'DEBUG' => 'badge-secondary',
                    default => 'badge-light',
                },
            ];

            if (count($logs) >= $limit) {
                break;
            }
        }

        return $logs;
    }

    /**
     * Format bytes to human readable format.
     */
    public function formatBytes(float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
