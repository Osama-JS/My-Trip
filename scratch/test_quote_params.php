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

// Test various payload formats
$tests = [
    'test1' => [
        'departure_date' => '2026-06-01',
        'return_date' => '2026-06-10',
        'country_id' => 'TR',
        'destinations' => ['TR'],
        'origin' => 'SA',
        'num_travelers' => 1,
        'travellers' => [['age' => 30]],
        'currency_code' => 'USD'
    ],
    'test2' => [
        'start_date' => '2026-06-01',
        'end_date' => '2026-06-10',
        'destination_country_id' => 'TR',
        'origin_country_id' => 'SA',
        'travellers' => [['age' => 30]]
    ],
    'test3' => [
        'trip' => [
            'departure_date' => '2026-06-01',
            'return_date' => '2026-06-10',
            'destination_country_ids' => ['TUR'],
            'origin_country_id' => 'SAU',
            'total_cost' => 1000
        ],
        'travellers' => [
            ['birth_date' => '1995-01-01']
        ]
    ]
];

foreach ($tests as $name => $payload) {
    $res = Http::withHeaders($headers)->post($apiUrl . "/products/{$productId}/quote", $payload);
    echo "{$name} -> Status: " . $res->status() . "\n";
    echo "   Response: " . $res->body() . "\n\n";
}
