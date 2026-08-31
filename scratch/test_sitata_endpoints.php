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

echo "=== TESTING SITATA API V2 ENDPOINTS ===\n";

$headers = [
    'Organization'  => $orgId,
    'Authorization' => 'TKN ' . $apiKey,
    'Content-Type'  => 'application/json',
    'Accept'        => 'application/json'
];

$endpoints = [
    '/insurance/quotes',
    '/insurance/quote',
    '/quotes',
    '/plans',
    '/insurance/plans',
    '/coverages',
    '/insurance/coverages',
    '/products',
    '/insurance/products',
    '/trips',
];

foreach ($endpoints as $ep) {
    try {
        $res = Http::withHeaders($headers)->get($apiUrl . $ep);
        echo "GET {$ep} -> Status: " . $res->status() . "\n";
        if ($res->status() != 404) {
            echo "   Body: " . substr($res->body(), 0, 200) . "\n";
        }
    } catch (\Exception $e) {
        echo "GET {$ep} -> Exception: " . $e->getMessage() . "\n";
    }
}
