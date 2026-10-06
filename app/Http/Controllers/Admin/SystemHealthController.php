<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SystemHealthService;
use App\Services\BackupService;
use App\Services\ThirdPartyBenchmarkService;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SystemHealthController extends Controller
{
    protected SystemHealthService $healthService;
    protected BackupService $backupService;
    protected ThirdPartyBenchmarkService $benchmarkService;

    public function __construct(
        SystemHealthService $healthService, 
        BackupService $backupService,
        ThirdPartyBenchmarkService $benchmarkService
    ) {
        $this->healthService = $healthService;
        $this->backupService = $backupService;
        $this->benchmarkService = $benchmarkService;
    }

    /**
     * Display the System Health & Maintenance Hub dashboard.
     */
    public function index(Request $request)
    {
        $health = $this->healthService->getFullHealthReport();
        $logs = $this->healthService->getParsedLogs(30, $request->get('log_level'));
        $backups = $this->backupService->getBackupsList();
        $backupStats = $this->backupService->getBackupStats();
        $benchmarks = $this->benchmarkService->benchmarkAll();

        $failedJobs = [];
        try {
            if (DB::getSchemaBuilder()->hasTable('failed_jobs')) {
                $failedJobs = DB::table('failed_jobs')
                    ->latest('failed_at')
                    ->limit(15)
                    ->get();
            }
        } catch (\Throwable $e) {
            // failed jobs table might not exist
        }

        return view('admin.system.health', compact('health', 'logs', 'failedJobs', 'backups', 'backupStats', 'benchmarks'));
    }

    /**
     * Real-time AJAX endpoint for live status metrics.
     */
    public function statusData()
    {
        return response()->json([
            'success' => true,
            'data'    => $this->healthService->getFullHealthReport(),
            'time'    => now()->format('H:i:s'),
        ]);
    }

    /**
     * Run Third-Party APIs benchmark probe on-demand.
     */
    public function runBenchmark()
    {
        try {
            $benchmarks = $this->benchmarkService->benchmarkAll();

            return response()->json([
                'success'    => true,
                'message'    => __('All third-party suppliers and gateways probed successfully.'),
                'benchmarks' => $benchmarks,
                'probed_at'  => now()->format('H:i:s'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Benchmark execution failed: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Execute quick maintenance & optimization actions.
     */
    public function executeAction(Request $request)
    {
        $action = $request->input('action');
        $output = '';

        try {
            switch ($action) {
                case 'clear-cache':
                    Artisan::call('cache:clear');
                    $output = Artisan::output() ?: __('Application cache cleared successfully.');
                    break;

                case 'clear-config':
                    Artisan::call('config:clear');
                    $output = Artisan::output() ?: __('Configuration cache cleared successfully.');
                    break;

                case 'cache-config':
                    Artisan::call('config:cache');
                    $output = Artisan::output() ?: __('Configuration cached for high performance.');
                    break;

                case 'clear-route':
                    Artisan::call('route:clear');
                    $output = Artisan::output() ?: __('Route cache cleared.');
                    break;

                case 'cache-route':
                    Artisan::call('route:cache');
                    $output = Artisan::output() ?: __('Routes cached successfully.');
                    break;

                case 'clear-view':
                    Artisan::call('view:clear');
                    $output = Artisan::output() ?: __('Compiled Blade views cleared.');
                    break;

                case 'optimize':
                    Artisan::call('optimize');
                    $output = Artisan::output() ?: __('Application optimized successfully (Config, Routes, and Views cached).');
                    break;

                case 'optimize-clear':
                    Artisan::call('optimize:clear');
                    $output = Artisan::output() ?: __('All cache, routes, config, and views flushed successfully.');
                    break;

                case 'storage-link':
                    Artisan::call('storage:link');
                    $output = Artisan::output() ?: __('Public storage link created/verified successfully.');
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => __('Invalid or unrecognized action.')
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => trim($output),
                'action'  => $action,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Action execution failed: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle Smart Maintenance Mode.
     */
    public function toggleMaintenance(Request $request)
    {
        $request->validate([
            'status'  => 'required|in:up,down',
            'secret'  => 'nullable|string|max:100',
            'message' => 'nullable|string|max:500',
            'retry'   => 'nullable|numeric|min:0',
        ]);

        $status = $request->input('status');

        try {
            if ($status === 'down') {
                $secret = $request->input('secret') ?: Str::random(16);
                $message = $request->input('message') ?: __('We are performing scheduled maintenance. We will be back shortly.');
                $retry = $request->input('retry') ?: 60;

                $params = [
                    '--secret' => $secret,
                    '--status' => 503,
                    '--retry'  => (int) $retry,
                ];

                Artisan::call('down', $params);

                // Save secret in settings for easy admin retrieval
                Setting::set('auth_maintenance_mode', '1');
                Setting::set('auth_maintenance_secret', $secret);

                $bypassUrl = url('/' . $secret);

                return response()->json([
                    'success'    => true,
                    'is_down'    => true,
                    'secret'     => $secret,
                    'bypass_url' => $bypassUrl,
                    'message'    => __('Maintenance mode activated successfully.'),
                ]);
            } else {
                Artisan::call('up');
                Setting::set('auth_maintenance_mode', '0');

                return response()->json([
                    'success' => true,
                    'is_down' => false,
                    'message' => __('Application is now LIVE and fully accessible.'),
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to toggle maintenance mode: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get real-time parsed log entries.
     */
    public function logs(Request $request)
    {
        $level = $request->get('level');
        $logs = $this->healthService->getParsedLogs(60, $level);

        return response()->json([
            'success' => true,
            'logs'    => $logs,
            'count'   => count($logs),
        ]);
    }

    /**
     * Clear application log file.
     */
    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');

        try {
            if (file_exists($logFile)) {
                File::put($logFile, '');
            }

            return response()->json([
                'success' => true,
                'message' => __('System error log has been wiped successfully.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to clear logs: ') . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retry all failed jobs.
     */
    public function retryFailedJobs()
    {
        try {
            Artisan::call('queue:retry', ['id' => ['all']]);
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'message' => trim($output) ?: __('All failed jobs queued for retry.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to retry jobs: ') . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Flush / delete all failed jobs.
     */
    public function flushFailedJobs()
    {
        try {
            Artisan::call('queue:flush');

            return response()->json([
                'success' => true,
                'message' => __('All failed jobs records have been deleted.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to flush jobs: ') . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new Database or Storage backup.
     */
    public function createBackup(Request $request)
    {
        $type = $request->input('type', 'database');

        try {
            if ($type === 'database') {
                $result = $this->backupService->createDatabaseBackup();
                $message = __('Database backup created successfully: ') . $result['filename'] . " ({$result['size']})";
            } else {
                $result = $this->backupService->createStorageBackup();
                $message = __('Storage files backup created successfully: ') . $result['filename'] . " ({$result['size']})";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'backup'  => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Backup creation failed: ') . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download a specific backup file.
     */
    public function downloadBackup(string $filename)
    {
        $path = $this->backupService->getBackupPath($filename);

        if (!$path || !file_exists($path)) {
            abort(404, __('Backup file not found.'));
        }

        return response()->download($path, basename($filename));
    }

    /**
     * Delete a backup file.
     */
    public function deleteBackup(string $filename)
    {
        try {
            $deleted = $this->backupService->deleteBackup($filename);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => __('Backup file deleted successfully.'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('Backup file not found or could not be removed.'),
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete backup: ') . $e->getMessage(),
            ], 500);
        }
    }
}
