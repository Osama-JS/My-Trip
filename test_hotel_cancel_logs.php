<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$logs = App\Models\HotelApiLog::where('action', 'like', '%cancel%')
    ->orWhere('endpoint', 'like', '%cancel%')
    ->latest()
    ->take(5)
    ->get(['id', 'booking_id', 'action', 'endpoint', 'status_code', 'response_payload', 'created_at']);

foreach ($logs as $l) {
    echo "====================================\n";
    echo "ID: {$l->id} | Booking: {$l->booking_id} | Action: {$l->action} | Status: {$l->status_code} | Date: {$l->created_at}\n";
    echo "Endpoint: {$l->endpoint}\n";
    echo json_encode($l->response_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
