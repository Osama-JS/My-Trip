<?php

use Illuminate\Support\Facades\Http;
use App\Services\TraveloproService;
use App\Models\Booking;

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "🚀 Starting Payment Integration Scenario Test\n";
echo "--------------------------------------------------\n";

// 1. Mock Booking Creation
echo "\n1️⃣  Creating a Mock Booking...\n";
$bookingRef = 'TEST-SCN-' . time();
$booking = Booking::create([
    'user_id' => 1,
    'booking_reference' => $bookingRef,
    'supplier_session_id' => 'mock-session-id',
    'status' => 'pending',
    'ticket_status' => 'booked',
    'total_amount' => 150.00,
    'currency' => 'SAR',
    'contact_email' => 'scenario-test@example.com',
    'contact_phone' => '966500000000',
    'pnr_created_at' => now(),
]);

echo "✅ Booking created: ID {$booking->id} (Ref: {$bookingRef})\n";

// 2. Simulate Payment Initiation (Tabby)
echo "\n2️⃣  Initiating Tabby Payment...\n";
$paymentController = app(\App\Http\Controllers\Api\PaymentController::class);

$requestDict = [
    'amount' => 150.00,
    'payment_type' => 'tabby',
    'order_id' => $bookingRef,
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'scenario-test@example.com',
    'phone' => '966500000000',
    'callback_url' => 'http://localhost/payment/callback/tabby'
];

$req = Illuminate\Http\Request::create('/api/payment/initiate', 'POST', $requestDict);
$req->setUserResolver(function () {
    return new \App\Models\User(['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com', 'phone' => '966500000000']);
});

try {
    $response = $paymentController->initiate($req);
    $data = $response->getData(true);

    if (!$data['error']) {
        echo "✅ Payment Initiated! Checkout URL: " . ($data['data']['checkout_url'] ?? 'N/A') . "\n";
        echo "ℹ️  Session ID: " . ($data['data']['session_id'] ?? 'N/A') . "\n";
        $paymentId = $data['data']['session_id'] ?? 'mock_payment_id';
    } else {
        echo "❌ Payment Initiation Failed: " . $data['message'] . "\n";
        exit;
    }
} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    // For test continuity if config missing
    $paymentId = 'mock_payment_id';
}


// 3. Simulate Payment Verification (Callback)
echo "\n3️⃣  Simulating Payment Callback/Verification...\n";

// We can't easily validly verify a fake payment ID against real Tabby API without manual interaction.
// So we will MOCK the verification success to test the logic flow of updating booking.

// Mocking the Service or bypassing verification logic for this test script context?
// Let's force update the booking to 'paid' manually to proceed to step 4, simulating a successful webhook.
$booking->update(['status' => 'paid']);
echo "✅ (Simulated) Payment Verified. Booking status set to 'paid'.\n";


// 4. Order Ticket (The critical step)
echo "\n4️⃣  Ordering Ticket (Issuance)...\n";

$flightController = app(\App\Http\Controllers\Api\FlightController::class);
$orderReq = Illuminate\Http\Request::create('/api/flights/order-ticket', 'POST', ['uniqueId' => $bookingRef]);

// We need to Mock TraveloproService here because we don't have a real valid PNR on Travelopro from step 1
$mockTravelopro = Mockery::mock(TraveloproService::class);
$mockTravelopro->shouldReceive('orderTicket')
    ->once()
    ->with($bookingRef)
    ->andReturn(['status' => 'success', 'message' => 'Ticket Issued', 'data' => ['TicketNumber' => '123456789']]);

// Swap the service instance
app()->instance(TraveloproService::class, $mockTravelopro);
// Re-instantiate controller to pick up mock
$flightController = app(\App\Http\Controllers\Api\FlightController::class);

$response = $flightController->orderTicket($orderReq);
$data = $response->getData(true);

if (!$data['error']) {
    echo "✅ Ticket Ordered Successfully!\n";
    echo "📄 Response: " . json_encode($data['data']) . "\n";
} else {
    echo "❌ Ticket Ordering Failed: " . $data['message'] . "\n";
}

echo "\n--------------------------------------------------\n";
echo "🎉 Scenario Test Completed.\n";
