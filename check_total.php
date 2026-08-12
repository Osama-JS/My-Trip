<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$log = \App\Models\FlightApiLog::where('action', 'createBooking')->latest()->first();
$req = $log->request_payload ?? [];
echo "Total Amount in Log: " . ($req['total_amount'] ?? 'Not Found') . "\n";
