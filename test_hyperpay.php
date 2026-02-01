<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

// Load Laravel App
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$baseUrl = Config::get('hyperpay.base_url');
$token = Config::get('hyperpay.access_token');
$entityId = Config::get('hyperpay.entity_ids.visa_master');

echo "Testing HyperPay Connectivity...\n";
echo "Base URL: " . $baseUrl . "\n";
echo "Entity ID (Visa): " . ($entityId ? "Set" : "NOT SET") . "\n";
echo "Access Token: " . ($token ? "Set" : "NOT SET") . "\n\n";

if (!$token || !$entityId) {
    echo "❌ ERROR: Missing credentials in .env file.\n";
    exit(1);
}

// Attempt to create a dummy checkout to verify credentials
$url = $baseUrl . 'checkouts';
$params = [
    'entityId' => $entityId,
    'amount' => '10.00',
    'currency' => 'SAR',
    'paymentType' => 'DB'
];

try {
    $response = Http::withToken($token)
        ->asForm()
        ->post($url, $params);

    if ($response->successful()) {
        echo "✅ SUCCESS: Credentials are valid!\n";
        echo "Checkout ID received: " . $response->json()['id'] . "\n";
    } else {
        echo "❌ FAILURE: API responded with error.\n";
        echo "Status: " . $response->status() . "\n";
        print_r($response->json());
    }
} catch (\Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
}
