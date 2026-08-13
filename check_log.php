<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fb = \App\Models\FlightBooking::latest()->first();
$log = \App\Models\FlightApiLog::where('booking_id', $fb->booking_id)->where('action', 'createBooking')->first();
$req = $log->request_payload;
echo json_encode($req, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
