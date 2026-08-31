<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\FlightBooking;
use App\Models\TripBooking;
use App\Models\HotelBooking;
use App\Models\InsurancePolicy;
use App\Services\SitataInsuranceService;

echo "=== LATEST BOOKINGS ===\n";
$latestFlight = Booking::with('flightBooking', 'passengers')->latest()->first();
if ($latestFlight) {
    echo "Latest Flight Booking: ID={$latestFlight->id}, Ref={$latestFlight->booking_reference}, Status={$latestFlight->status}, Total={$latestFlight->total_amount}, InsAmount={$latestFlight->insurance_amount}\n";
    if ($latestFlight->flightBooking) {
        echo "   FlightDetails: {$latestFlight->flightBooking->origin} -> {$latestFlight->flightBooking->destination}, Depart={$latestFlight->flightBooking->departure_date}\n";
    }
}

$latestTrip = TripBooking::latest()->first();
if ($latestTrip) {
    echo "Latest Trip Booking: ID={$latestTrip->id}, Status={$latestTrip->status}, Total={$latestTrip->total_price}, InsAmount={$latestTrip->insurance_amount}\n";
}

$latestPolicies = InsurancePolicy::latest()->get();
echo "\nTotal Insurance Policies in DB: " . $latestPolicies->count() . "\n";
foreach ($latestPolicies as $p) {
    echo "Policy #{$p->policy_number}: Status={$p->status}, UserID={$p->user_id}, Selling={$p->selling_price} SAR, Profit={$p->platform_profit} SAR\n";
}
