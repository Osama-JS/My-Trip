<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$log = \App\Models\FlightApiLog::where('action', 'createBooking')->latest()->first();
$req = $log->request_payload ?? [];
echo json_encode($req['flightBookingInfo'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
