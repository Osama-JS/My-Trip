<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use App\Models\HotelBooking;
use App\Models\Booking as FlightBooking;
use App\Models\BankTransfer;
use App\Services\HyperPayService;
use App\Services\TabbyPaymentService;
use App\Services\TamaraPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\PaymentLogTrait;

class PaymentWebController extends Controller
{
   use PaymentLogTrait;

    protected $hyperPayService;
    protected $tabbyService;
    protected $tamaraService;
    protected $tapService;

    public function __construct(
        HyperPayService $hyperPayService,
        TabbyPaymentService $tabbyService,
        TamaraPaymentService $tamaraService,
        \App\Services\TapPaymentService $tapService
    ) {
        $this->hyperPayService = $hyperPayService;
        $this->tabbyService = $tabbyService;
        $this->tamaraService = $tamaraService;
        $this->tapService = $tapService;
    }

    /**
     * Show the unified checkout page
     */
    public function checkout(Request $request, $booking_id, $method)
    {
        try {
            $type = $request->get('type', 'trip');
            $booking = $this->resolveBooking($booking_id, $type);

            if (!$booking) {
                return redirect()->route('payments.web.failure', ['error' => 'Invalid booking type or ID']);
            }

            // Basic validation
            if ($booking->status === 'confirmed') {
                return redirect()->route('payments.web.success', ['booking_id' => $booking_id]);
            }

            $user = $booking->user;

            // Prepare dynamic data for the view
            $data = [
                'booking' => $booking,
                'booking_type' => $type,
                'user' => $user,
                'method' => $method,
                'amount' => $type === 'flight' ? $booking->total_amount : $booking->total_price,
                'source' => $request->source,
            ];

            // Add specific details for the view
            if ($type === 'trip') {
                $data['title'] = $booking->trip->title ?? 'Trip Booking';
            } elseif ($type === 'hotel') {
                $data['title'] = $booking->hotel_name ?? 'Hotel Booking';
            } elseif ($type === 'flight') {
                $data['title'] = 'Flight Booking #' . $booking->booking_reference;
            }

            // If HyperPay, we might need a checkout_id immediately to load the widget
            if (in_array($method, ['mada', 'visa_master', 'apple_pay'])) {
                // Simulation mode for local dev (XAMPP cannot reach oppwa.com)
                if (config('app.env') === 'local' || env('PAYMENT_SIMULATION', false)) {
                    $fakeId = 'SIM-' . strtoupper(uniqid());
                    $data['checkout_id']  = null;           // No real widget
                    $data['sim_mode']     = true;
                    $data['sim_ref']      = $fakeId;
                    Log::info("Payment Simulation Mode — skipping HyperPay. Ref: {$fakeId}");
                } else {
                    $checkoutResult = $this->prepareHyperPayCheckout($booking, $method, $request, $type);
                    $checkoutId = $checkoutResult['id'] ?? null;
                    $data['checkout_id'] = $checkoutId;

                    if ($checkoutId) {
                        $this->logPendingPayment($booking, 'hyperpay', $method, $checkoutId, $data['amount'], $checkoutResult);
                    }
                }
            }

            return view('payments.checkout', $data);

        } catch (\Exception $e) {
            Log::error("Web Checkout Error: " . $e->getMessage());
            return redirect()->route('payments.web.failure', ['error' => $e->getMessage()]);
        }
    }

    protected function resolveBooking($id, $type)
    {
        switch ($type) {
            case 'hotel':
                return HotelBooking::with('user')->find($id);
            case 'flight':
                return FlightBooking::with('user')->find($id);
            case 'trip':
            default:
                return TripBooking::with(['trip', 'user'])->find($id);
        }
    }

    /**
     * Handle Tamara/Tabby redirection initiation from the web page
     */
    public function initiateRedirect(Request $request)
    {
        $request->validate([
            'booking_id' => 'required',
            'method' => 'required|string|in:tamara,tabby,tap',
            'type' => 'nullable|string|in:trip,hotel,flight',
            'source' => 'nullable|string',
        ]);

        try {
            $type = $request->get('type', 'trip');
            $booking = $this->resolveBooking($request->booking_id, $type);
            
            if (!$booking) {
                return response()->json(['error' => true, 'message' => 'Booking not found'], 404);
            }

            $method = $request->method;
            $user = $booking->user;

            // Simulation mode for local dev (XAMPP cannot reach payment APIs)
            if (config('app.env') === 'local' || env('PAYMENT_SIMULATION', false)) {
                $fakeRef = 'SIM-' . strtoupper($method) . '-' . strtoupper(uniqid());
                Log::info("Payment {$method} Simulation Mode — Ref: {$fakeRef}");
                
                return response()->json([
                    'checkout_url' => route('payments.web.success', [
                        'booking_id' => $booking->id,
                        'transaction_id' => $fakeRef,
                        'source' => 'simulation'
                    ]),
                    'id' => $fakeRef,
                    'status' => 'initiated'
                ]);
            }

            if ($method === 'tabby') {
                return $this->initiateTabby($booking, $user, $request, $type);
            }

            if ($method === 'tamara') {
                return $this->initiateTamara($booking, $user, $request, $type);
            }

            if ($method === 'tap') {
                return $this->initiateTap($booking, $user, $request, $type);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    protected function prepareHyperPayCheckout($booking, $method, $request, $type)
    {
        $amount = $type === 'flight' ? $booking->total_amount : $booking->total_price;
        $params = [
            'merchantTransactionId' => strtoupper($type) . '-BOOKING-' . $booking->id . '-' . time(),
        ];

        $customerParams = $this->hyperPayService->buildCustomerParams([
            'email' => $booking->user->email ?? $booking->contact_email,
            'first_name' => $booking->user->first_name ?? $booking->user->full_name ?? 'Guest',
            'last_name' => $booking->user->last_name ?? 'User',
            'street' => $booking->user->address ?? 'Saudi Arabia',
            'city' => $booking->user->city ?? 'Riyadh',
            'state' => $booking->user->state ?? 'Riyadh',
            'country' => $booking->user->country_code ?? 'SA',
            'postcode' => $booking->user->postcode ?? '12345',
        ]);

        $params = array_merge($params, $customerParams);

        return $this->hyperPayService->prepareCheckout(
            $amount,
            $method,
            $params
        );
    }

    protected function initiateTabby($booking, $user, $request, $type)
    {
        $amount = $type === 'flight' ? $booking->total_amount : $booking->total_price;
        $title = 'Booking';
        if ($type === 'trip') $title = $booking->trip->title ?? 'Trip';
        elseif ($type === 'hotel') $title = $booking->hotel_name ?? 'Hotel';
        elseif ($type === 'flight') $title = 'Flight #' . $booking->booking_reference;

        $data = [
            'amount' => $amount,
            'customer_name' => $user->full_name ?? ($user->first_name . ' ' . $user->last_name),
            'customer_email' => $user->email ?? $booking->contact_email,
            'customer_phone' => $user->phone ?? $booking->contact_phone,
            'order_id' => strtoupper($type) . '-BOOKING-' . $booking->id . '-' . time(),
            'callback_url' => route('payments.web.callback', [
                'payment_type' => 'tabby',
                'source' => $request->source,
                'type' => $type
            ]),
            'items' => [
                [
                    'title' => $title,
                    'quantity' => 1,
                    'unit_price' => $amount,
                ]
            ],
            'city' => $user->city ?? 'Riyadh',
            'address' => $user->address ?? 'Saudi Arabia',
        ];

        $result = $this->tabbyService->initiateCheckout($data);

        if ($result['payment_id'] ?? null) {
            $this->logPendingPayment($booking, 'tabby', 'installments', $result['payment_id'], $amount, $result);
        }

        return response()->json($result);
    }

    protected function initiateTamara($booking, $user, $request, $type)
    {
        $amount = $type === 'flight' ? $booking->total_amount : $booking->total_price;
        $title = 'Booking';
        if ($type === 'trip') $title = $booking->trip->title ?? 'Trip';
        elseif ($type === 'hotel') $title = $booking->hotel_name ?? 'Hotel';
        elseif ($type === 'flight') $title = 'Flight #' . $booking->booking_reference;

        $data = [
            'amount' => $amount,
            'customer_email' => $user->email ?? $booking->contact_email,
            'customer_phone' => $user->phone ?? $booking->contact_phone,
            'first_name' => $user->first_name ?? $user->full_name ?? 'Guest',
            'last_name' => $user->last_name ?? 'User',
            'order_id' => strtoupper($type) . '-BOOKING-' . $booking->id . '-' . time(),
            'callback_url' => route('payments.web.callback', [
                'payment_type' => 'tamara',
                'source' => $request->source,
                'type' => $type
            ]),
            'items' => [
                [
                    'name' => $title,
                    'quantity' => 1,
                    'total_amount' => [
                        'amount' => $amount,
                        'currency' => 'SAR'
                    ],
                    'type' => ucfirst($type),
                    'reference_id' => (string) $booking->id
                ]
            ],
            'city' => $user->city ?? 'Riyadh',
            'address' => $user->address ?? 'Saudi Arabia',
        ];

        $result = $this->tamaraService->initiateCheckout($data);

        if ($result['order_id'] ?? null) {
            $this->logPendingPayment($booking, 'tamara', 'installments', $result['order_id'], $amount, $result);
        }

        return response()->json($result);
    }

    protected function initiateTap($booking, $user, $request, $type)
    {
        $amount = $type === 'flight' ? $booking->total_amount : $booking->total_price;
        $title = 'Booking';
        if ($type === 'trip') $title = $booking->trip->title ?? 'Trip';
        elseif ($type === 'hotel') $title = $booking->hotel_name ?? 'Hotel';
        elseif ($type === 'flight') $title = 'Flight #' . $booking->booking_reference;

        $data = [
            'booking_id' => $booking->id,
            'booking_type' => $type,
            'amount' => $amount,
            'customer_email' => $user->email ?? $booking->contact_email,
            'customer_phone' => $user->phone ?? $booking->contact_phone,
            'first_name' => $user->first_name ?? $user->full_name ?? 'Guest',
            'last_name' => $user->last_name ?? 'User',
            'order_id' => strtoupper($type) . '-BOOKING-' . $booking->id . '-' . time(),
            'callback_url' => route('payments.web.callback', [
                'payment_type' => 'tap',
                'source' => $request->source,
                'type' => $type
            ]),
            'description' => 'Booking #' . $booking->id . ' - ' . $title,
        ];

        $result = $this->tapService->initiateCheckout($data);

        if ($result['id'] ?? null) {
            $this->logPendingPayment($booking, 'tap', 'card', $result['id'], $amount, $result);
        }

        return response()->json($result);
    }

    public function submitBankTransfer(Request $request)
    {
        $request->validate([
            'booking_id' => 'required',
            'type' => 'required|string|in:trip', // STRICTLY Trip only
            'receipt_image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'sender_name' => 'required|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            if ($request->type !== 'trip') {
                return response()->json(['error' => true, 'message' => __('Bank transfer is only available for Trips.')], 403);
            }

            $booking = TripBooking::with('user')->findOrFail($request->booking_id);

            // Check if already paid or under review
            if (in_array($booking->status, ['confirmed'])) {
                return response()->json(['error' => true, 'message' => __('Booking is already confirmed.')], 400);
            }

            // Handle File Upload
            $path = $request->file('receipt_image')->store('bank_transfers', 'public');

            // Create record
            BankTransfer::create([
                'trip_booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'receipt_number' => $request->receipt_number,
                'sender_name' => $request->sender_name,
                'receipt_image' => $path,
                'notes' => $request->notes,
                'status' => 'pending'
            ]);

            if (class_exists(\App\Models\BookingHistory::class)) {
                \App\Models\BookingHistory::create([
                    'trip_booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'action' => 'bank_transfer_submitted',
                    'description' => __('Bank transfer receipt uploaded and pending review.'),
                    'previous_state' => null,
                    'new_state' => \App\Models\TripBooking::STATE_RECEIVED,
                ]);
            }

            return response()->json([
                'error' => false,
                'message' => __('Bank transfer submitted successfully. It will be reviewed by admin soon.')
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function success(Request $request)
    {
        return view('payments.success', [
            'booking_id' => $request->booking_id,
            'transaction_id' => $request->transaction_id,
            'source' => $request->source
        ]);
    }

    public function failure(Request $request)
    {
        return view('payments.failure', [
            'error' => $request->error ?? __('Payment failed or was cancelled.'),
            'source' => $request->source
        ]);
    }
}
