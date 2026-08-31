<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== TESTING LIVE SITATA STAGING API CONNECTION ===\n";

$orgId = config('insurance.organization_id') ?: 'd745d42c-4e0b-4be4-829f-b0d30dad006f';
$apiKey = config('insurance.api_key') ?: '453d1ba9-7ee8-4cc8-b091-5c1cf705dd2c';
$apiUrl = config('insurance.api_url') ?: 'https://staging.sitata.com/api/v2';

echo "URL: {$apiUrl}\n";
echo "Org: {$orgId}\n";
echo "Auth Token: {$apiKey}\n\n";

// Let's test a simple quote or ping endpoint
try {
    $response = Http::withHeaders([
        'Organization'  => $orgId,
        'Authorization' => 'TKN ' . $apiKey,
        'Content-Type'  => 'application/json',
        'Accept'        => 'application/json'
    ])->get($apiUrl . '/countries'); // Or test ping/quotes

    echo "HTTP Status Code: " . $response->status() . "\n";
    echo "Response Body:\n";
    print_r($response->json() ?: $response->body());
} catch (\Exception $e) {
    echo "Connection Error: " . $e->getMessage() . "\n";
}
