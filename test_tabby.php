<?php

// Load Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

if (config('services.tabby.public_key') === null) {
    echo "⚠️  Update .env with TABBY credentials first!\n";
    exit;
}

$service = new \App\Services\TabbyPaymentService();

$data = [
    'amount' => 100.00,
    'currency' => 'SAR',
    'customer_name' => 'Test User',
    'customer_email' => 'test@example.com',
    'customer_phone' => '966500000000',
    'order_id' => 'TEST-' . time(),
    'callback_url' => 'https://example.com/callback',
    'description' => 'Test Order'
];

echo "Initiating Tabby Checkout...\n";
try {
    $result = $service->initiateCheckout($data);
    print_r($result);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
