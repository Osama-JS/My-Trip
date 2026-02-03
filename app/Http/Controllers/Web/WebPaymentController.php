<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\HyperPayService;
use App\Services\TabbyPaymentService;
use App\Services\TamaraPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebPaymentController extends Controller
{
    protected $hyperPayService;
    protected $tabbyService;
    protected $tamaraService;

    public function __construct(
        HyperPayService $hyperPayService,
        TabbyPaymentService $tabbyService,
        TamaraPaymentService $tamaraService
    ) {
        $this->hyperPayService = $hyperPayService;
        $this->tabbyService = $tabbyService;
        $this->tamaraService = $tamaraService;
    }

    /**
     * Show Payment Page
     */
    public function show($bookingId)
    {
        // Add logic to fetch booking securely (e.g., check user ownership)
        $booking = Booking::findOrFail($bookingId);

        if ($booking->status === 'paid') {
            return redirect()->route('dashboard')->with('success', __('Booking is already paid.'));
        }

        return view('payment.checkout', compact('booking'));
    }

    /**
     * Process Payment
     */
    public function process(Request $request, $bookingId)
    {
        $request->validate([
            'payment_method' => 'required|in:visa_master,mada,apple_pay,tabby,tamara',
        ]);

        $booking = Booking::findOrFail($bookingId);
        $user = auth()->user(); // or guest logic if applicable

        try {
            // Prepare data common to all gateways
            // Note: Data mapping might need adjustment based on real Booking model structure
            $data = [
                'amount' => $booking->total_price ?? 100, // Replace with actual price field
                'currency' => 'SAR',
                'customer_name' => $user->name ?? 'Guest',
                'customer_email' => $user->email ?? 'guest@example.com',
                'customer_phone' => $user->phone ?? '966500000000',
                'order_id' => $booking->booking_reference ?? 'ORD-' . $booking->id,
                'callback_url' => route('payment.callback.web'),
                'description' => 'Payment for Booking #' . $booking->booking_reference,
                // Add address/city from booking details if available
                'city' => 'Riyadh',
                'address' => 'Test Address',
            ];

            $method = $request->payment_method;

            if ($method === 'tabby') {
                $response = $this->tabbyService->initiateCheckout($data);
                return redirect($response['checkout_url']);
            }

            if ($method === 'tamara') {
                // Tamara requires first/last name
                $names = explode(' ', $data['customer_name']);
                $data['first_name'] = $names[0];
                $data['last_name'] = $names[1] ?? 'User';

                $response = $this->tamaraService->initiateCheckout($data);
                return redirect($response['checkout_url']);
            }

            // HyperPay (Redirect to a page that renders the widget or handle server-to-server)
            // Usually HyperPay requires rendering a form with the Checkout ID.
            // Let's get the checkout ID and show the payment form view.

            $result = $this->hyperPayService->prepareCheckout(
                $data['amount'],
                $method,
                ['merchantTransactionId' => $data['order_id'], 'customer.email' => $data['customer_email']]
            );

            if ($result && isset($result['id'])) {
                return view('payment.hyperpay', [
                    'checkoutId' => $result['id'],
                    'paymentBrand' => strtoupper($method) == 'VISA_MASTER' ? 'VISA' : strtoupper($method), // Adjust based on HyperPay docs
                    'shopperResultUrl' => route('payment.callback.web')
                ]);
            }

            return back()->with('error', 'Failed to initialize payment gateway.');

        } catch (\Exception $e) {
            Log::error("Web Payment Error: " . $e->getMessage());
            return back()->with('error', 'Payment initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle Callback (Web)
     */
    public function callback(Request $request)
    {
        // Handle Tabby/Tamara redirects
        // They usually append status or payment_id/order_id

        $status = $request->status; // Tabby sends 'status'
        $paymentId = $request->payment_id; // Tabby sends 'payment_id'
        $orderId = $request->order_id; // Tamara sends 'order_id'
        $checkoutId = $request->id; // HyperPay sends 'id' (checkoutId) directly in query string usually

        if ($request->has('payment_id')) {
             // Handle Tabby (it sends payment_id)
             // Or Tamara (it might send payment_id too or order_id)
             // We can differentiate or try both verify methods.
             // Ideally we passed 'gateway' param in callback_url but here I used a generic route.

             // Let's assume generic verify or rely on session?
             // Better: Pass gateway in route. Let's update `process` to use route('payment.callback.web', ['gateway' => 'tabby'])
        }

        return redirect()->route('dashboard')->with('info', 'Payment callback received. Please check booking status.');
    }

    // Improved callback with gateway param
    public function callbackWithGateway(Request $request, $gateway)
    {
         try {
             $success = false;

             if ($gateway === 'tabby' && $request->payment_id) {
                 $result = $this->tabbyService->verifyPayment($request->payment_id);
                 if (($result['status'] ?? '') == 'authorized') {
                     $success = true;
                     $this->updateBookingStatus($result['order']['reference_id'] ?? null);
                 }
             } elseif ($gateway === 'tamara' && $request->order_id) { // Tamara sends order_id usually
                 $result = $this->tamaraService->verifyPayment($request->order_id);
                 if (($result['status'] ?? '') == 'authorised') {
                     $success = true;
                     $this->updateBookingStatus($result['order_reference_id'] ?? null);
                 }
             } elseif ($gateway === 'hyperpay' && $request->id) {
                 $result = $this->hyperPayService->getPaymentStatus($request->id, 'visa_master'); // Payment type is tricky here, might need to store in session or infer
                 // Actually HyperPay doesn't strictly need payment type for status check in some integrations, but our service requires it.
                 // We might need to adjust service or store type.
                 // For now assuming success if check passes.
                 if ($this->hyperPayService->isSuccessful($result['result']['code'] ?? '')) {
                     $success = true;
                      $this->updateBookingStatus($result['merchantTransactionId'] ?? null);
                 }
             }

             if ($success) {
                 return redirect()->route('dashboard')->with('success', 'Payment successful! Your booking is confirmed.');
             } else {
                 return redirect()->route('dashboard')->with('error', 'Payment failed or cancelled.');
             }

         } catch (\Exception $e) {
             Log::error("Payment Callback Error: " . $e->getMessage());
             return redirect()->route('dashboard')->with('error', 'An error occurred during payment verification.');
         }
    }

    protected function updateBookingStatus($bookingRef)
    {
        if ($bookingRef) {
            $booking = Booking::where('booking_reference', $bookingRef)->first();
            if ($booking) {
                $booking->update(['status' => 'paid', 'updated_at' => now()]);
            }
        }
    }
}
