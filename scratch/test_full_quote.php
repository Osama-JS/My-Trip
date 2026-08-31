<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use App\Models\Setting;

$orgId = Setting::get('sitata_organization_id', 'd745d42c-4e0b-4be4-829f-b0d30dad006f');
$apiKey = Setting::get('sitata_api_key', '453d1ba9-7ee8-4cc8-b091-5c1cf705dd2c');
$apiUrl = 'https://staging.sitata.com/api/v2';

$headers = [
    'Organization'  => $orgId,
    'Authorization' => 'TKN ' . $apiKey,
    'Content-Type'  => 'application/json',
    'Accept'        => 'application/json'
];

$productId = '8f6a19a0-c04d-4a81-9501-941853e6067a';

// Fetch countries to get Saudi and Turkey IDs
$countriesRes = Http::withHeaders($headers)->get($apiUrl . '/countries');
$countries = $countriesRes->json();

$turkey = null;
$saudi = null;

foreach ($countries as $c) {
    if (isset($c['country_code']) && ($c['country_code'] === 'TR' || $c['country_code'] === 'TUR')) {
        $turkey = $c;
    }
    if (isset($c['country_code']) && ($c['country_code'] === 'SA' || $c['country_code'] === 'SAU')) {
        $saudi = $c;
    }
}

echo "Turkey Country Object:\n" . json_encode($turkey, JSON_PRETTY_PRINT) . "\n";
echo "Saudi Country Object:\n" . json_encode($saudi, JSON_PRETTY_PRINT) . "\n";

// Now test quote with country ID and destinations
$payload = [
    'trip' => [
        'departure_date' => '2026-06-01',
        'return_date' => '2026-06-10',
        'origin_country_id' => $saudi['id'] ?? null,
        'destinations' => [
            [
                'country_id' => $turkey['id'] ?? null,
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-10',
            ]
        ],
        'total_cost' => 1500
    ],
    'travellers' => [
        [
            'birth_date' => '1990-05-15',
            'residence_country_id' => $saudi['id'] ?? null,
        ]
    ]
];

$res = Http::withHeaders($headers)->post($apiUrl . "/products/{$productId}/quote", $payload);
echo "Quote Request with destinations -> Status: " . $res->status() . "\n";
echo "Response Body:\n" . json_encode($res->json(), JSON_PRETTY_PRINT) . "\n";
