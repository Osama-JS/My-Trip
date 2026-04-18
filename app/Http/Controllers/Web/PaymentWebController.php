<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use App\Models\HotelBooking;
use App\Models\Booking as FlightBooking;
use App\Models\BankTransfer;
use App\Models\BankAccount;
use App\Services\HyperPayService;
use App\Services\TabbyPaymentService;
use App\Services\TamaraPaymentService;
use App\Traits\HotelBookingFinalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\PaymentLogTrait;
use App\Services\NotificationService;

class PaymentWebController extends Controller
{
   use PaymentLogTrait, HotelBookingFinalizer;

    protected $hyperPayService;
    protected $tabbyService;
    protected $tamaraService;
    protected $tapService;
    protected $notificationService;

    public function __construct(
        HyperPayService $hyperPayService,
        TabbyPaymentService $tabbyService,
        TamaraPaymentService $tamaraService,
        \App\Services\TapPaymentService $tapService,
        NotificationService $notificationService
    ) {
        $this->hyperPayService = $hyperPayService;
        $this->tabbyService = $tabbyService;
        $this->tamaraService = $tamaraService;
        $this->tapService = $tapService;
        $this->notificationService = $notificationService;
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
                'bankAccounts' => $method === 'bank_transfer' ? BankAccount::where('is_active', true)->get() : [],
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
                // Simulation mode ONLY when PAYMENT_SIMULATION=true is explicitly set
                // APP_ENV=local does NOT trigger simulation, so real keys work locally too
                if (env('PAYMENT_SIMULATION', false)) {
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

            $method = $request->input('method');
            $user = $booking->user;

            // Simulation mode ONLY when PAYMENT_SIMULATION=true is explicitly set in .env
            // This allows Tamara/Tabby to work with real API keys even in local environment
            if (env('PAYMENT_SIMULATION', false)) {
                $fakeRef = 'SIM-' . strtoupper($method) . '-' . strtoupper(uniqid());
                Log::info("Payment {$method} Simulation Mode — Ref: {$fakeRef}");
                
                return response()->json([
                    'checkout_url' => route('payments.web.success', [
                        'booking_id' => $booking->id,
                        'transaction_id' => $fakeRef,
                        'source' => 'simulation',
                        'type' => $type
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
                'source'       => $request->source,
                'type'         => $type,
                'booking_id'   => $booking->id,  // ← so callback_processing knows which booking to confirm
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

    /**
     * Public payment verification endpoint (called from browser callback_processing page).
     * No Sanctum token required — session CSRF only.
     */
    public function webVerify(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|string|in:mada,visa_master,apple_pay,tabby,tamara,tap',
            'payment_id'   => 'nullable|string',
            'checkout_id'  => 'nullable|string',
            'booking_id'   => 'nullable',
            'type'         => 'nullable|string|in:trip,hotel,flight',
        ]);

        try {
            $paymentType = $request->payment_type;
            $type        = $request->get('type', 'trip');
            $bookingId   = $request->booking_id;

            // ── Tamara ────────────────────────────────────────────────────────
            if ($paymentType === 'tamara') {
                $orderId = $request->payment_id ?? $request->order_id;
                if (!$orderId) {
                    return response()->json(['error' => true, 'message' => 'Missing Tamara order_id'], 422);
                }

                $result = $this->tamaraService->verifyPayment($orderId);
                $status = $result['status'] ?? 'unknown';

                if (in_array($status, ['authorised', 'fully_captured'])) {
                    // Update booking if bookingId was passed
                    if ($bookingId) {
                        $booking = $this->resolveBooking($bookingId, $type);
                        if ($booking) {
                            $booking->update(['status' => 'paid']);
                            Log::info("Tamara: Booking #{$bookingId} ({$type}) marked as PAID. Order: {$orderId}");
                            
                            // AUTO-FINALIZE HOTELS
                            if ($type === 'hotel') {
                                $this->finalizeHotelSupplierBooking($booking);
                            }
                        }
                    }
                    return response()->json(['error' => false, 'message' => 'Payment successful', 'status' => $status, 'booking_id' => $bookingId, 'type' => $type]);
                }

                Log::warning("Tamara: Unexpected status '{$status}' for order {$orderId}");
                return response()->json(['error' => true, 'message' => 'Payment not authorised. Status: ' . $status], 400);
            }

            // ── Tabby ─────────────────────────────────────────────────────────
            if ($paymentType === 'tabby') {
                $paymentId = $request->payment_id;
                if (!$paymentId) {
                    return response()->json(['error' => true, 'message' => 'Missing Tabby payment_id'], 422);
                }

                $result = $this->tabbyService->verifyPayment($paymentId);
                $status = $result['status'] ?? 'unknown';

                if (in_array($status, ['authorized', 'closed'])) {
                    if ($bookingId) {
                        $booking = $this->resolveBooking($bookingId, $type);
                        if ($booking) {
                            $booking->update(['status' => 'paid']);
                            Log::info("Tabby: Booking #{$bookingId} ({$type}) marked as PAID.");

                            // AUTO-FINALIZE HOTELS
                            if ($type === 'hotel') {
                                $this->finalizeHotelSupplierBooking($booking);
                            }
                        }
                    }
                    return response()->json(['error' => false, 'message' => 'Payment successful', 'status' => $status, 'booking_id' => $bookingId, 'type' => $type]);
                }

                return response()->json(['error' => true, 'message' => 'Payment not authorized. Status: ' . $status], 400);
            }

            // ── Tap ───────────────────────────────────────────────────────────
            if ($paymentType === 'tap') {
                $paymentId = $request->payment_id;
                if (!$paymentId) {
                    return response()->json(['error' => true, 'message' => 'Missing Tap payment_id'], 422);
                }

                $result = $this->tapService->verifyPayment($paymentId);
                $status = strtoupper($result['status'] ?? 'UNKNOWN');

                if (in_array($status, ['CAPTURED', 'AUTHORIZED'])) {
                    if ($bookingId) {
                        $booking = $this->resolveBooking($bookingId, $type);
                        if ($booking) {
                            $booking->update(['status' => 'paid']);
                            Log::info("Tap: Booking #{$bookingId} ({$type}) marked as PAID.");

                            // AUTO-FINALIZE HOTELS
                            if ($type === 'hotel') {
                                $this->finalizeHotelSupplierBooking($booking);
                            }
                        }
                    }
                    return response()->json(['error' => false, 'message' => 'Payment successful', 'status' => $status, 'booking_id' => $bookingId, 'type' => $type]);
                }

                return response()->json(['error' => true, 'message' => 'Payment not captured. Status: ' . $status], 400);
            }

            // ── HyperPay ──────────────────────────────────────────────────────
            $checkoutId = $request->checkout_id;
            if (!$checkoutId) {
                return response()->json(['error' => true, 'message' => 'Missing checkout_id for HyperPay'], 422);
            }

            $result   = $this->hyperPayService->getPaymentStatus($checkoutId, $paymentType);
            $code     = $result['result']['code'] ?? '';
            $isSuccess = $this->hyperPayService->isSuccessful($code);

            if ($isSuccess) {
                if ($bookingId) {
                    $booking = $this->resolveBooking($bookingId, $type);
                    if ($booking) {
                        $booking->update(['status' => 'paid']);
                        Log::info("HyperPay: Booking #{$bookingId} ({$type}) marked as PAID.");

                        // AUTO-FINALIZE HOTELS
                        if ($type === 'hotel') {
                            $this->finalizeHotelSupplierBooking($booking);
                        }

                        // SEND NOTIFICATION
                        $this->notificationService->sendToUser(
                            $booking->user,
                            \App\Models\Notification::TYPE_PAYMENT_SUCCESS,
                            __('Payment Successful'),
                            __('Your payment for booking #:id via HyperPay has been confirmed.', ['id' => $bookingId]),
                            ['booking_id' => $bookingId, 'type' => $type]
                        );
                    }
                }
                return response()->json(['error' => false, 'message' => 'Payment successful', 'booking_id' => $bookingId, 'type' => $type]);
            }

            return response()->json(['error' => true, 'message' => $result['result']['description'] ?? 'Payment failed.'], 400);

        } catch (\Exception $e) {
            Log::error('webVerify Exception: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
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
            'type' => 'required|string|in:trip', // STRICTLY Trip only
            'bank_account_id' => 'required|exists:bank_accounts,id',
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
                'bank_account_id' => $request->bank_account_id,
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
        Log::info("Payment Success Page Hit", $request->all());
        $booking = null;
        if ($request->booking_id) {
            $type = $request->get('type');
            if (!$type) {
                // Try to guess from ID if missing (fallback)
                $type = $request->get('booking_type', 'trip');
            }
            $booking = $this->resolveBooking($request->booking_id, $type);
            
            // Update status and finalize supplier booking
            if ($booking && in_array($booking->status, ['pending', 'paid'])) {
                Log::info("Processing success page finalization for {$type} Booking ID: {$booking->id}");
                
                if ($type === 'hotel') {
                    // Update locally first
                    $newStatus = !empty($booking->supplier_confirmation_num) ? 'confirmed' : 'paid';
                    if ($booking->status !== 'confirmed') {
                        $booking->update(['status' => $newStatus]);
                    }
                    
                    // Trigger actual supplier finalization (Trait handles if already confirmed)
                    $this->finalizeHotelSupplierBooking($booking);
                } else if ($type === 'flight') {
                    if ($booking->status === 'pending') {
                         $booking->update(['status' => 'paid']);
                    }
                } else {
                    if ($booking->status === 'pending') {
                        $booking->update(['status' => 'paid']);
                    }
                }
            }
        }

        return view('payments.success', [
            'booking' => $booking,
            'booking_id' => $request->booking_id,
            'transaction_id' => $request->transaction_id,
            'source' => $request->source,
            'booking_type' => $request->get('type', 'trip'),
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
