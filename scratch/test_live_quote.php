<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\SitataInsuranceService;

$svc = app(SitataInsuranceService::class);

echo "=== 1. TEST JEDDAH -> RIYADH (Domestic 1 Day, 500 SAR) ===\n";
$q1 = $svc->getQuote([
    'origin_country' => 'JED',
    'destination_country' => 'RUH',
    'passengers_count' => 1,
    'departure_date' => '2026-06-01',
    'return_date' => '2026-06-02',
    'trip_cost' => 500,
    'booking_type' => 'flight'
]);
echo "Selling Price: " . $q1['selling_price'] . " SAR (Net Cost: " . $q1['net_cost'] . " SAR, Profit: " . $q1['platform_profit'] . " SAR)\n\n";

echo "=== 2. TEST ISTANBUL -> RIYADH (International Turkey 7 Days, 1500 SAR) ===\n";
$q2 = $svc->getQuote([
    'origin_country' => 'IST',
    'destination_country' => 'RUH',
    'passengers_count' => 1,
    'departure_date' => '2026-06-01',
    'return_date' => '2026-06-08',
    'trip_cost' => 1500,
    'booking_type' => 'flight'
]);
echo "Selling Price: " . $q2['selling_price'] . " SAR (Net Cost: " . $q2['net_cost'] . " SAR, Profit: " . $q2['platform_profit'] . " SAR)\n\n";

echo "=== 3. TEST RIYADH -> PARIS (Schengen Europe 10 Days, 2800 SAR) ===\n";
$q3 = $svc->getQuote([
    'origin_country' => 'RUH',
    'destination_country' => 'CDG',
    'passengers_count' => 1,
    'departure_date' => '2026-06-01',
    'return_date' => '2026-06-11',
    'trip_cost' => 2800,
    'booking_type' => 'flight'
]);
echo "Selling Price: " . $q3['selling_price'] . " SAR (Net Cost: " . $q3['net_cost'] . " SAR, Profit: " . $q3['platform_profit'] . " SAR)\n\n";
