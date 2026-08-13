<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$log = \App\Models\FlightApiLog::where('action', 'extraServices')->orWhere('action', 'validateFare')->latest()->first();
if ($log) {
    echo "Action: " . $log->action . "\n";
    echo substr($log->response_payload ?? '', 0, 2000);
}
