<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\InsurancePolicy;
use App\Services\SitataInsuranceService;

$booking = Booking::with('passengers', 'flightBooking', 'user')->find(18);
if (!$booking) {
    echo "Booking 18 not found.\n";
    exit;
}

echo "Found Booking #{$booking->id} for user " . ($booking->user->name ?? 'User') . "\n";

// Update insurance_amount to reflect the insurance purchase
$booking->update([
    'insurance_amount' => 45.00
]);

$insuranceService = app(SitataInsuranceService::class);

$passengers = [];
foreach ($booking->passengers as $p) {
    $passengers[] = [
        'first_name' => $p->first_name ?: ($p->name ?: 'Traveler'),
        'last_name'  => $p->last_name ?: '',
        'passport_no'=> $p->passport_number ?: 'REG',
        'nationality'=> $p->nationality ?: 'SA',
        'dob'        => $p->dob,
        'type'       => $p->passenger_type ?: 'adult',
    ];
}

if (empty($passengers)) {
    $passengers[] = [
        'first_name' => $booking->user->name ?? 'Traveler',
        'last_name'  => '',
        'passport_no'=> 'REG123456',
        'nationality'=> 'SA',
        'dob'        => '1995-01-01',
        'type'       => 'adult',
    ];
}

$dest = $booking->flightBooking->destination ?? 'GLOBAL';
$origin = $booking->flightBooking->origin ?? 'SA';
$depDate = $booking->flightBooking->departure_date ?? now()->addDays(1);
$retDate = $booking->flightBooking->return_date ?? now()->addDays(8);

$quoteResult = $insuranceService->getQuote([
    'origin_country'      => $origin,
    'destination_country' => $dest,
    'departure_date'      => $depDate,
    'return_date'         => $retDate,
    'trip_cost'           => $booking->total_amount,
    'passengers_count'    => count($passengers),
    'coverage_type'       => 'comprehensive',
    'booking_type'        => 'flight',
    'user_id'             => $booking->user_id,
]);

$quote = \App\Models\InsuranceQuote::find($quoteResult['quote_id']);
$policy = $insuranceService->issuePolicy($quote, $passengers, $booking, 'flight');

echo "SUCCESS! Issued Insurance Policy #{$policy->policy_number} for Booking #{$booking->id}!\n";
echo "Selling Price: {$policy->selling_price} SAR, Platform Profit: {$policy->platform_profit} SAR\n";
