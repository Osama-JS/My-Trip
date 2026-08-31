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

$testEndpoints = [
    '/sales_quotes',
    '/quotes',
    '/products/8f6a19a0-c04d-4a81-9501-941853e6067a/quote',
    '/products/8f6a19a0-c04d-4a81-9501-941853e6067a/price',
    '/prices',
    '/pricing',
];

$payload = [
    'product_id' => '8f6a19a0-c04d-4a81-9501-941853e6067a',
    'destination_country' => 'TR',
    'departure_date' => date('Y-m-d', strtotime('+3 days')),
    'return_date' => date('Y-m-d', strtotime('+10 days')),
    'travellers' => [
        ['age' => 30]
    ]
];

foreach ($testEndpoints as $ep) {
    try {
        $res = Http::withHeaders($headers)->post($apiUrl . $ep, $payload);
        echo "POST {$ep} -> Status: " . $res->status() . "\n";
        echo "   Body: " . substr($res->body(), 0, 300) . "\n\n";
    } catch (\Exception $e) {
        echo "POST {$ep} -> Error: " . $e->getMessage() . "\n\n";
    }
}
