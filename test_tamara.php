<?php

// Load Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

if (config('services.tamara.api_token') === null) {
    echo "⚠️  Update .env with TAMARA credentials first!\n";
    exit;
}

$service = new \App\Services\TamaraPaymentService();

$data = [
    'amount' => 150.00,
    'currency' => 'SAR',
    'first_name' => 'Test',
    'last_name' => 'User',
    'customer_email' => 'test@example.com',
    'customer_phone' => '966500000000',
    'order_id' => 'TEST-' . time(),
    'callback_url' => 'https://example.com/callback',
    'description' => 'Test Tamara Order',
    'city' => 'Riyadh',
    'address' => 'Test Street 123'
];

echo "Initiating Tamara Checkout...\n";
try {
    $result = $service->initiateCheckout($data);
    print_r($result);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
