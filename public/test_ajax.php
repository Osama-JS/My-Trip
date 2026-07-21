<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $logs = \App\Models\FlightApiLog::with('user')->latest()->limit(5)->get();
    $data = $logs->map(function($log) {
        return [
            'id' => $log->id,
            'endpoint' => $log->endpoint,
            'user' => $log->user->name ?? 'Guest',
            'status' => $log->status_code == 200 ? '<span class="badge badge-success">200</span>' : '<span class="badge badge-danger">'.$log->status_code.'</span>',
            'time' => $log->created_at->format('Y-m-d H:i:s'),
            'action' => '<button class="btn btn-xs btn-info" onclick="viewLogPayload('.$log->id.')">View</button>'
        ];
    });
    echo json_encode($data);
} catch (\Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine();
}
