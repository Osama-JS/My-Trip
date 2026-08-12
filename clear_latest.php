<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fb = \App\Models\FlightBooking::latest()->first();
if ($fb) {
    $fb->update(['itinerary_data' => null]);
    echo "Cleared itinerary_data";
}
