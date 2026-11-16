<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$cols = Illuminate\Support\Facades\Schema::getColumnListing('hotel_bookings');
dump($cols);

$hb = \App\Models\HotelBooking::latest()->first();
if ($hb) {
    dump($hb->toArray());
}
