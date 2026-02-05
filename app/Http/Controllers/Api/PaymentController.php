<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HyperPayService;
use App\Services\TabbyPaymentService;
use App\Services\TamaraPaymentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    use ApiResponseTrait;

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
     * Get Available Payment Methods
     */
    #[OA\Get(
        path: "/api/payment/methods",
        summary: "Get available payment methods",
        operationId: "getPaymentMethods",
        description: "Returns a list of active payment gateways and their configuration for the UI.",
        tags: ["Payment"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Payment methods retrieved."),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "key", type: "string", example: "visa_master"),
                                new OA\Property(property: "name", type: "string", example: "Visa / MasterCard"),
                                new OA\Property(property: "type", type: "string", example: "card"),
                                new OA\Property(property: "icon", type: "string", example: "url_to_icon")
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function methods()
    {
        $methods = [
            [
                'key' => 'mada',
                'name' => __('Mada'),
                'type' => 'card',
                'icon' => asset('assets/img/payments/mada.png')
            ],
            [
                'key' => 'visa_master',
                'name' => __('Visa / MasterCard'),
                'type' => 'card',
                'icon' => asset('assets/img/payments/visa.png')
            ],
            [
                'key' => 'tabby',
                'name' => __('Tabby (Installments)'),
                'type' => 'redirect',
                'icon' => asset('assets/img/payments/tabby.png')
            ],
            [
                'key' => 'tamara',
                'name' => __('Tamara'),
                'type' => 'redirect',
                'icon' => asset('assets/img/payments/tamara.png')
            ],
            [
                'key' => 'apple_pay',
                'name' => __('Apple Pay'),
                'type' => 'digital_wallet',
                'icon' => asset('assets/img/payments/apple-pay.png')
            ]
        ];

        return $this->apiResponse(false, __('Payment methods retrieved successfully.'), $methods);
    }

    /**
     * Initiate Payment Checkout
     */
    #[OA\Post(
        path: "/api/payment/initiate",
        summary: "Initiate payment checkout (HyperPay, Tabby, Tamara)",
        operationId: "initiatePayment",
        description: "Initializes a payment session. For HyperPay, returns a Checkout ID. For Tabby/Tamara, returns a redirect URL.",
        tags: ["Payment"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["amount", "payment_type"],
                properties: [
                    new OA\Property(property: "amount", type: "number", format: "float", example: 100.50),
                    new OA\Property(property: "payment_type", type: "string", enum: ["mada", "visa_master", "apple_pay", "tabby", "tamara"], example: "visa_master"),
                    new OA\Property(property: "order_id", type: "string", example: "ORDER-12345"),
                    // Additional fields for Tabby/Tamara
                    new OA\Property(property: "first_name", type: "string", example: "John"),
                    new OA\Property(property: "last_name", type: "string", example: "Doe"),
                    new OA\Property(property: "phone", type: "string", example: "966500000000"),
                    new OA\Property(property: "email", type: "string", example: "john@example.com"),
                    new OA\Property(property: "city", type: "string", example: "Riyadh"),
                    new OA\Property(property: "address", type: "string", example: "123 Main St"),
                    new OA\Property(property: "callback_url", type: "string", example: "https://mysite.com/payment/callback"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Checkout initialized successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Checkout initialized successfully."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation Error"),
            new OA\Response(response: 500, description: "Payment Gateway Error")
        ]
    )]
    public function initiate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'payment_type' => 'required|string|in:mada,visa_master,apple_pay,tabby,stc_pay,tamara', // Added stc_pay if needed for hyperpay later, but focus on requested
            'order_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            $user = $request->user();
            $paymentType = $request->payment_type;

            // Handle Tabby
            if ($paymentType === 'tabby') {
                return $this->initiateTabby($request, $user);
            }

            // Handle Tamara
            if ($paymentType === 'tamara') {
                return $this->initiateTamara($request, $user);
            }

            // Handle HyperPay (Default)
            return $this->initiateHyperPay($request, $user);

        } catch (\Exception $e) {
            Log::error("Payment Initiation Error: " . $e->getMessage());
            return $this->apiResponse(true, __('Failed to initialize payment: ') . $e->getMessage(), null, null, 500);
        }
    }

    protected function initiateHyperPay(Request $request, $user)
    {
        $params = [];
        if ($request->order_id) {
            $params['merchantTransactionId'] = $request->order_id;
        }

        if ($user->email) {
            $params['customer.email'] = $user->email;
        }

        $result = $this->hyperPayService->prepareCheckout(
            $request->amount,
            $request->payment_type,
            $params
        );

        if ($result && isset($result['id'])) {
            return $this->apiResponse(false, __('Checkout initialized successfully.'), $result);
        }

        throw new \Exception('HyperPay service returned error.');
    }

    protected function initiateTabby(Request $request, $user)
    {
        $data = [
            'amount' => $request->amount,
            // 'currency' => 'SAR', // Service defaults to SAR
            'customer_name' => ($request->first_name && $request->last_name)
                                ? $request->first_name . ' ' . $request->last_name
                                : $user->name,
            'customer_email' => $request->email ?? $user->email,
            'customer_phone' => $request->phone ?? $user->phone, // Ensure user model has phone or request has it
            'order_id' => $request->order_id ?? uniqid('ORD-'),
            'callback_url' => route('payment.callback', ['gateway' => 'tabby']),
            'items' => [], // Add logic to pass items if available
            // Add other optional fields
            'city' => $request->city,
            'address' => $request->address,
            'zip' => $request->zip,
        ];

        // Basic validation for Tabby requirements
        if (!$data['customer_email'] || !$data['customer_phone'] || !$data['customer_name']) {
             throw new \Exception('Missing required customer data for Tabby (name, email, phone).');
        }

        $result = $this->tabbyService->initiateCheckout($data);

        return $this->apiResponse(false, __('Tabby checkout initialized.'), $result);
    }

    protected function initiateTamara(Request $request, $user)
    {
        $data = [
            'amount' => $request->amount,
            'customer_email' => $request->email ?? $user->email,
            'customer_phone' => $request->phone ?? $user->phone,
            'first_name' => $request->first_name ?? explode(' ', $user->name)[0],
            'last_name' => $request->last_name ?? (explode(' ', $user->name)[1] ?? 'User'),
            'order_id' => $request->order_id ?? uniqid('ORD-'),
            'callback_url' => route('payment.callback', ['gateway' => 'tamara']),
            'items' => [], // Add items logic
             // Add other optional fields
            'city' => $request->city,
            'address' => $request->address,
        ];

         if (!$data['customer_email'] || !$data['customer_phone']) {
             throw new \Exception('Missing required customer data for Tamara (email, phone).');
        }

        $result = $this->tamaraService->initiateCheckout($data);

        return $this->apiResponse(false, __('Tamara checkout initialized.'), $result);
    }

    /**
     * Verify Payment Status
     */
    #[OA\Post(
        path: "/api/payment/verify",
        summary: "Verify payment status",
        operationId: "verifyPayment",
        description: "Checks the status of a payment.",
        tags: ["Payment"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["payment_type"], // checkout_id might be payment_id for others
                properties: [
                    new OA\Property(property: "payment_type", type: "string", enum: ["mada", "visa_master", "apple_pay", "tabby", "tamara"]),
                    new OA\Property(property: "checkout_id", type: "string", description: "Required for HyperPay"),
                    new OA\Property(property: "payment_id", type: "string", description: "Required for Tabby/Tamara"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Payment status retrieved"),
            new OA\Response(response: 400, description: "Payment Failed")
        ]
    )]
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_type' => 'required|string|in:mada,visa_master,apple_pay,tabby,tamara',
            'checkout_id' => 'required_if:payment_type,mada,visa_master,apple_pay', // HyperPay
            'payment_id' => 'required_if:payment_type,tabby,tamara', // Tabby/Tamara ID
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            $type = $request->payment_type;

            if ($type === 'tabby') {
                $result = $this->tabbyService->verifyPayment($request->payment_id);
                $status = $result['status'] ?? 'unknown';

                if ($status == 'authorized' || $status == 'closed') {
                     // Tabby returns order.reference_id
                     $bookingRef = $result['order']['reference_id'] ?? null;
                     $this->updateBookingStatus($bookingRef);

                     return $this->apiResponse(false, __('Payment successful.'), $result);
                }
                return $this->apiResponse(true, __('Payment failed or pending.'), $result, null, 400);
            }

            if ($type === 'tamara') {
                $result = $this->tamaraService->verifyPayment($request->payment_id);
                $status = $result['status'] ?? 'unknown';

                 if ($status == 'authorised' || $status == 'fully_captured') {
                     // Tamara returns order_reference_id
                     $bookingRef = $result['order_reference_id'] ?? null;
                     $this->updateBookingStatus($bookingRef);

                     return $this->apiResponse(false, __('Payment successful.'), $result);
                }
                return $this->apiResponse(true, __('Payment failed or pending.'), $result, null, 400);
            }

            // HyperPay Logic
            return $this->verifyHyperPay($request);

        } catch (\Exception $e) {
             return $this->apiResponse(true, $e->getMessage(), null, null, 500);
        }
    }

    protected function verifyHyperPay(Request $request)
    {
        $result = $this->hyperPayService->getPaymentStatus($request->checkout_id, $request->payment_type);

        if ($result && isset($result['result']['code'])) {
            $isSuccess = $this->hyperPayService->isSuccessful($result['result']['code']);

            if ($isSuccess) {
                // HyperPay returns merchantTransactionId
                $bookingRef = $result['merchantTransactionId'] ?? null;
                $this->updateBookingStatus($bookingRef);

                return $this->apiResponse(false, __('Payment successful.'), $result);
            }

            return $this->apiResponse(true, $result['result']['description'] ?? __('Payment failed.'), $result, null, 400);
        }

        throw new \Exception('HyperPay verification failed.');
    }

    protected function updateBookingStatus($bookingRef)
    {
        if ($bookingRef) {
            $booking = \App\Models\Booking::where('booking_reference', $bookingRef)->first();
            if ($booking) {
                $booking->update(['status' => 'paid', 'updated_at' => now()]);
                Log::info("Booking {$bookingRef} marked as PAID via Payment Gateway");
            }
        }
    }

    /**
     * Centralized callback handler that provides a server-side landing page
     */
    public function handleCallback(Request $request)
    {
        $gateway = $request->gateway;
        $status = $request->status;

        // For Tabby, the payment ID is payment_id. For Tamara, it might be tap_id or similar.
        // We will pass identifying info in the URL so the app can read it.
        $paymentId = $request->payment_id ?? $request->tap_id ?? $request->id;

        return response("
            <!DOCTYPE html>
            <html lang='ar' dir='rtl'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>معالجة الدفع...</title>
                <style>
                    body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f8fafc; color: #1e293b; text-align: center; }
                    .card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
                    .loader { border: 4px solid #f3f3f3; border-top: 4px solid #4f46e5; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 20px auto; }
                    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                </style>
            </head>
            <body>
                <div class='card'>
                    <div class='loader'></div>
                    <h2>يتم معالجة عملية الدفع...</h2>
                    <p>يرجى الانتظار، سيتم توجيهك إلى التطبيق تلقائياً.</p>
                    <p style='font-size: 0.8rem; color: #64748b;'>ID: {$paymentId}</p>
                </div>
                <script>
                    // This script is a fallback. The mobile app should intercept the URL before/at this point.
                    console.log('Payment ID identified: {$paymentId}');
                </script>
            </body>
            </html>
        ")->header('Content-Type', 'text/html');
    }
}
