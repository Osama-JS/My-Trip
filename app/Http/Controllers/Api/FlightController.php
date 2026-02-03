<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TraveloproService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FlightController extends Controller
{
    protected $traveloproService;
    protected $invoiceService;

    public function __construct(TraveloproService $traveloproService, \App\Services\InvoiceService $invoiceService)
    {
        $this->traveloproService = $traveloproService;
        $this->invoiceService = $invoiceService;
    }

    #[OA\Post(
        path: "/api/flights/search",
        summary: "البحث عن الرحلات (Search for flights)",
        operationId: "searchFlights",
        description: "البحث عن رحلات الطيران المتاحة عبر Travelopro API مع عرض كافة التفاصيل والأسعار. (Search for flight availability using Travelopro API).",
        tags: ["Flights"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "لغة الاستجابة (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["journeyType", "OriginDestinationInfo", "class", "adults"],
                properties: [
                    new OA\Property(property: "journeyType", type: "string", enum: ["OneWay", "Return", "Circle"], example: "OneWay"),
                    new OA\Property(
                        property: "OriginDestinationInfo",
                        type: "array",
                        items: new OA\Items(
                            type: "object",
                            required: ["departureDate", "airportOriginCode", "airportDestinationCode"],
                            properties: [
                                new OA\Property(property: "departureDate", type: "string", format: "date", example: "2024-12-01"),
                                new OA\Property(property: "returnDate", type: "string", format: "date", example: "2024-12-10", description: "Required if journeyType is Return"),
                                new OA\Property(property: "airportOriginCode", type: "string", example: "DXB"),
                                new OA\Property(property: "airportDestinationCode", type: "string", example: "LHR")
                            ]
                        )
                    ),
                    new OA\Property(property: "class", type: "string", enum: ["First", "Business", "Economy", "PremiumEconomy"], example: "Economy"),
                    new OA\Property(property: "adults", type: "integer", example: 1),
                    new OA\Property(property: "childs", type: "integer", example: 0),
                    new OA\Property(property: "infants", type: "integer", example: 0),
                    new OA\Property(property: "requiredCurrency", type: "string", example: "SAR"),
                    new OA\Property(property: "airlineCode", type: "string", example: "", description: "Two letter code of the preferred airline"),
                    new OA\Property(property: "directFlight", type: "boolean", example: false, description: "True for only direct flights, false for all (or 1/0)")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم استرجاع الرحلات بنجاح",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Flights retrieved successfully."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "خطأ في التحقق من البيانات",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'journeyType' => 'required|string|in:OneWay,Return,Circle',
            'OriginDestinationInfo' => 'required|array|min:1',
            'OriginDestinationInfo.*.departureDate' => 'required|date_format:Y-m-d',
            'OriginDestinationInfo.*.returnDate' => 'nullable|date_format:Y-m-d|after_or_equal:OriginDestinationInfo.*.departureDate',
            'OriginDestinationInfo.*.airportOriginCode' => 'required|string|size:3',
            'OriginDestinationInfo.*.airportDestinationCode' => 'required|string|size:3',
            'class' => 'required|string|in:First,Business,Economy,PremiumEconomy',
            'adults' => 'required|integer|min:1',
            'childs' => 'nullable|integer|min:0',
            'infants' => 'nullable|integer|min:0',
            'requiredCurrency' => 'nullable|string|size:3',
            'airlineCode' => 'nullable|string|size:2',
            'directFlight' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $data = $request->all();
        // Cast directFlight to integer 0 or 1 as expected by Travelopro
        if ($request->has('directFlight')) {
            $data['directFlight'] = filter_var($request->directFlight, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }

        $result = $this->traveloproService->searchFlights($data);

        if (isset($result['status']) && $result['status'] === 'error') {
             $message = $result['message'] ?? __('Failed to fetch flight data');
             return $this->apiResponse(true, $message, $result, null, 500);
        }

        return $this->apiResponse(false, __('Flights retrieved successfully.'), $result, null, 200);
    }

    #[OA\Get(
        path: "/api/flights/airports",
        summary: "Get list of airports",
        operationId: "getAirports",
        description: "Retrieve a list of supported airports from Travelopro.",
        tags: ["Flights"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful retrieval",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Airports retrieved successfully."),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "AirportCode", type: "string", example: "DXB"),
                                new OA\Property(property: "AirportName", type: "string", example: "Dubai International Airport"),
                                new OA\Property(property: "City", type: "string", example: "Dubai"),
                                new OA\Property(property: "Country", type: "string", example: "United Arab Emirates")
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function getAirports()
    {
        $airports = $this->traveloproService->getAirportList();
        return $this->apiResponse(false, __('Airports retrieved successfully.'), $airports, null, 200);
    }

    #[OA\Get(
        path: "/api/flights/airlines",
        summary: "Get list of airlines",
        operationId: "getAirlines",
        description: "Retrieve a list of supported airlines from Travelopro.",
        tags: ["Flights"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful retrieval",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Airlines retrieved successfully."),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "AirLineCode", type: "string", example: "EK"),
                                new OA\Property(property: "AirLineName", type: "string", example: "Emirates"),
                                new OA\Property(property: "AirLineLogo", type: "string", example: "https://travelnext.works/api/airlines/EK.gif")
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function getAirlines()
    {
        $airlines = $this->traveloproService->getAirlineList();
        return $this->apiResponse(false, __('Airlines retrieved successfully.'), $airlines, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/validate-fare",
        summary: "التحقق من صحة السعر (Validate flight fare)",
        operationId: "validateFare",
        description: "التحقق مما إذا كان سعر الرحلة المحدد لا يزال متاحاً وصحيحاً قبل البدء بعملية الحجز. (Verify if the selected flight fare is still available).",
        tags: ["Flights"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "لغة الاستجابة (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["session_id", "fare_source_code"],
                properties: [
                    new OA\Property(property: "session_id", type: "string", example: "7906efba-09db-4481-8c60-0d7f5b5e6c44"),
                    new OA\Property(property: "fare_source_code", type: "string", example: "MTY2ODE2Njg2Ml8yNjA5Mzk"),
                    new OA\Property(property: "fare_source_code_inbound", type: "string", example: "", description: "Required for Indian Domestic RoundTrip")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم التحقق من السعر بنجاح",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Fare is valid."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "السعر غير متاح أو خطأ في البيانات",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Fare is no longer valid or available."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function validateFare(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
            'fare_source_code' => 'required|string',
            'fare_source_code_inbound' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->traveloproService->validateFare($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? $result['error'], null, 500);
        }

        // Check if IsValid is true in response
        $isValid = $result['AirRevalidateResponse']['AirRevalidateResult']['IsValid'] ?? false;

        if ($isValid === true || $isValid === 'true' || $isValid === 'True') {
            return $this->apiResponse(false, __('Fare is valid.'), $result, null, 200);
        }

        return $this->apiResponse(true, __('Fare is no longer valid or available.'), $result, null, 422);
    }

    #[OA\Post(
        path: "/api/flights/book",
        summary: "إنشاء سجل حجز PNR (Create flight booking)",
        operationId: "bookFlight",
        description: "إنشاء سجل حجز (PNR) مبدئي وحفظ بيانات المسافرين قبل عملية الدفع وإصدار التذكرة. (Create a PNR using passenger details).",
        tags: ["Flights"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "لغة الاستجابة (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["flight_session_id", "fare_source_code", "customerEmail", "customerPhone", "passengers"],
                properties: [
                    new OA\Property(property: "flight_session_id", type: "string", example: "7906efba-09db-4481-8c60-0d7f5b5e6c44"),
                    new OA\Property(property: "fare_source_code", type: "string", example: "MTY2ODE2Njg2Ml8yNjA5Mzk"),
                    new OA\Property(property: "fare_source_code_inbound", type: "string", example: ""),
                    new OA\Property(property: "customerEmail", type: "string", format: "email", example: "test@example.com"),
                    new OA\Property(property: "customerPhone", type: "string", example: "966500000000"),
                    new OA\Property(property: "areaCode", type: "string", example: "080"),
                    new OA\Property(property: "countryCode", type: "string", example: "966"),
                    new OA\Property(property: "fareType", type: "string", example: "Private"),
                    new OA\Property(property: "bookingNote", type: "string", example: ""),
                    new OA\Property(
                        property: "passengers",
                        type: "array",
                        items: new OA\Items(
                            type: "object",
                            required: ["type", "title", "first_name", "last_name", "dob", "nationality"],
                            properties: [
                                new OA\Property(property: "type", type: "string", enum: ["adult", "child", "infant"], example: "adult"),
                                new OA\Property(property: "title", type: "string", enum: ["Mr", "Mrs", "Miss", "Master"], example: "Mr"),
                                new OA\Property(property: "first_name", type: "string", example: "John"),
                                new OA\Property(property: "last_name", type: "string", example: "Doe"),
                                new OA\Property(property: "dob", type: "string", format: "date", example: "1990-01-01"),
                                new OA\Property(property: "nationality", type: "string", example: "SA"),
                                new OA\Property(property: "passport_no", type: "string", example: "A1234567"),
                                new OA\Property(property: "passport_issue_country", type: "string", example: "SA"),
                                new OA\Property(property: "passport_expiry_date", type: "string", format: "date", example: "2030-01-01")
                            ]
                        )
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم إنشاء سجل الحجز بنجاح",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Booking created successfully. Please proceed to payment."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "payment_url", type: "string", example: "https://mysite.com/payment/1"),
                            new OA\Property(property: "payment_api_url", type: "string", example: "https://mysite.com/api/payment/initiate"),
                            new OA\Property(property: "booking_id", type: "integer", example: 1),
                            new OA\Property(property: "CreateBookingResponse", type: "object")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "خطأ في بيانات المسافرين أو الطلب",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function book(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'flight_session_id' => 'required|string',
            'fare_source_code' => 'required|string',
            'fare_source_code_inbound' => 'nullable|string',
            'customerEmail' => 'required|email',
            'customerPhone' => 'required|string',
            'areaCode' => 'nullable|string',
            'countryCode' => 'nullable|string',
            'fareType' => 'nullable|string',
            'bookingNote' => 'nullable|string',
            'passengers' => 'required|array|min:1',
            'passengers.*.type' => 'required|string|in:adult,child,infant',
            'passengers.*.title' => 'required|string|in:Mr,Mrs,Miss,Master,Mstr',
            'passengers.*.first_name' => 'required|string',
            'passengers.*.last_name' => 'required|string',
            'passengers.*.dob' => 'required|date_format:Y-m-d',
            'passengers.*.nationality' => 'required|string|size:2',
            'passengers.*.passport_no' => 'nullable|string',
            'passengers.*.passport_issue_country' => 'nullable|string|size:2',
            'passengers.*.passport_expiry_date' => 'nullable|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->traveloproService->createBooking($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? $result['error'], null, 500);
        }

        // Persist booking to local database
        try {
            // Extract UniqueID from response - Adjust path based on actual response structure
            $uniqueId = $result['CreateBookingResponse']['CreateBookingResult']['UniqueID'] ?? null;
            // Also try to get Price if available in response, otherwise 0
            $totalAmount = $result['CreateBookingResponse']['CreateBookingResult']['TotalAmount'] ?? 0;

            if ($uniqueId) {
                $booking = Booking::create([
                    'user_id' => Auth::id() ?? 1, // Fallback to 1 or nullable if allowed
                    'booking_reference' => $uniqueId,
                    'supplier_session_id' => $request->flight_session_id,
                    'status' => 'pending', // PNR created but not ticketed
                    'ticket_status' => 'booked',
                    'total_amount' => $totalAmount,
                    'currency' => 'SAR', // Adjust if dynamic
                    'contact_email' => $request->customerEmail,
                    'contact_phone' => $request->customerPhone,
                    'pnr_created_at' => now(),
                ]);

                foreach ($request->passengers as $pax) {
                    $booking->passengers()->create([
                        'title' => $pax['title'],
                        'first_name' => $pax['first_name'],
                        'last_name' => $pax['last_name'],
                        'type' => $pax['type'],
                        'dob' => $pax['dob'],
                        'nationality' => $pax['nationality'],
                        'passport_no' => $pax['passport_no'] ?? null,
                    ]);
                }

                // Add Payment URL to response
                $result['payment_url'] = route('payment.show', $booking->id);
                $result['payment_api_url'] = url('/api/payment/initiate'); // Hint for API users
                $result['booking_id'] = $booking->id;
            }
        } catch (\Exception $e) {
            Log::error('Failed to persist booking', ['error' => $e->getMessage()]);
        }

        return $this->apiResponse(false, __('Booking created successfully. Please proceed to payment.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/order-ticket",
        summary: "إصدار التذكرة (Order ticket / Issuance)",
        operationId: "orderTicket",
        description: "إصدار التذكرة النهائية وحجزها بشكل قطعي بعد عملية الحجز المبدئي (PNR) والتأكد من الدفع. (Issue ticket for a confirmed booking).",
        tags: ["Flights"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "لغة الاستجابة (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["uniqueId"],
                properties: [
                    new OA\Property(property: "uniqueId", type: "string", example: "TR123456", description: "The UniqueID returned from the booking response")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم إصدار التذكرة بنجاح",
                 content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Ticket ordered successfully."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "خطأ في البيانات المرسلة",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function orderTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uniqueId' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        // 1. Verify Payment Status
        $booking = Booking::where('booking_reference', $request->uniqueId)->first();

        if (!$booking) {
             return $this->apiResponse(true, __('Booking not found.'), null, null, 404);
        }

        // Bypass check to allow testing IF needed, otherwise enforce strict check
        // Ideally: if ($booking->status !== 'paid') ....
        if ($booking->status !== 'paid') {
             return $this->apiResponse(true, __('Payment required before ticket issuance.'), null, null, 402);
        }

        $result = $this->traveloproService->orderTicket($request->uniqueId, $booking->id);

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? $result['error'], null, 500);
        }

        // Update local booking status
        $invoiceContent = null;
        try {
            if ($booking) {
                $booking->update([
                    'status' => 'confirmed',
                    'ticket_status' => 'ticketed',
                    'updated_at' => now()
                ]);

                // Generate Invoice
                $pdfContent = $this->invoiceService->generateInvoice($booking);
                if ($pdfContent) {
                    $invoiceContent = base64_encode($pdfContent);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to update booking status or generate invoice', ['error' => $e->getMessage()]);
        }

        $response = $result;
        if ($invoiceContent) {
            $response['invoice_pdf_base64'] = $invoiceContent;
        }

        return $this->apiResponse(false, __('Ticket ordered successfully.'), $response, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/trip-details",
        summary: "تفاصيل الرحلة المحجوزة (Get trip details)",
        operationId: "getTripDetails",
        description: "استرجاع كافة تفاصيل الرحلة، بما في ذلك أرقام التذاكر وحالة الحجز النهائية. (Get full details of a trip including ticket numbers).",
        tags: ["Flights"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "لغة الاستجابة (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["uniqueId"],
                properties: [
                     new OA\Property(property: "uniqueId", type: "string", example: "TR123456", description: "The UniqueID returned from the booking response")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم استرجاع تفاصيل الرحلة بنجاح",
                 content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Trip details retrieved successfully."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "خطأ في البيانات المرسلة",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function getTripDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uniqueId' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $booking = Booking::where('booking_reference', $request->uniqueId)->first();

        if (!$booking) {
             return $this->apiResponse(true, __('Booking not found.'), null, null, 404);
        }

        $result = $this->traveloproService->getTripDetails($request->uniqueId, $booking->id);

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? $result['error'], null, 500);
        }

        return $this->apiResponse(false, __('Trip details retrieved successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/booking-notes",
        summary: "إضافة ملاحظات للحجز (Add booking notes)",
        operationId: "addBookingNotes",
        description: "إضافة ملاحظات نصية على الحجز الحالي. (Add textual remarks to an existing booking).",
        tags: ["Flights"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["uniqueId", "notes"],
                properties: [
                    new OA\Property(property: "uniqueId", type: "string", example: "TR123456"),
                    new OA\Property(property: "notes", type: "string", example: "Wheel chair needed at airport.")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم إضافة الملاحظات بنجاح",
                content: new OA\JsonContent(properties: [new OA\Property(property: "error", type: "boolean", example: false)])
            )
        ]
    )]
    public function addBookingNotes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uniqueId' => 'required|string',
            'notes' => 'required|string'
        ]);

        if ($validator->fails()) return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

        $result = $this->traveloproService->addBookingNotes($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? ($result['error'] ?? null), null, 500);
        }

        return $this->apiResponse(false, __('Booking notes added successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/cancel",
        summary: "إلغاء الحجز (Cancel booking)",
        operationId: "cancelBooking",
        description: "إلغاء حجز (PNR) قبل إصدار التذكرة. (Cancel a PNR before ticket issuance).",
        tags: ["Flights"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["uniqueId"],
                properties: [
                    new OA\Property(property: "uniqueId", type: "string", example: "TR123456")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم إلغاء الحجز بنجاح",
                content: new OA\JsonContent(properties: [new OA\Property(property: "error", type: "boolean", example: false)])
            )
        ]
    )]
    public function cancelBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uniqueId' => 'required|string',
        ]);

        if ($validator->fails()) return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

        $result = $this->traveloproService->cancelBooking($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        // Update local DB
        try {
            $booking = Booking::where('booking_reference', $request->uniqueId)->first();
            if ($booking) {
                $booking->update([
                    'status' => 'cancelled',
                    'updated_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update booking status on cancel', ['error' => $e->getMessage()]);
        }

        return $this->apiResponse(false, __('Booking cancelled successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/extra-services",
        summary: "الخدمات الإضافية (Get extra services)",
        operationId: "getExtraServices",
        description: "استعراض الخدمات الإضافية المتاحة (كالأمتعة والوجبات) للحجز. (Retrieve available extra services like baggage and meals).",
        tags: ["Flights"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["session_id", "fare_source_code"],
                properties: [
                    new OA\Property(property: "session_id", type: "string", example: "7906efba-09db..."),
                    new OA\Property(property: "fare_source_code", type: "string", example: "MTY2ODE2Njg2Ml8yNjA5Mzk")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم استرجاع الخدمات الإضافية بنجاح",
                content: new OA\JsonContent(properties: [new OA\Property(property: "error", type: "boolean", example: false)])
            )
        ]
    )]
    public function getExtraServices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
            'fare_source_code' => 'required|string',
        ]);

        if ($validator->fails()) return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

        $result = $this->traveloproService->getExtraServices($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Extra services retrieved successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/fare-rules",
        summary: "شروط السعر (Get fare rules)",
        operationId: "getFareRules",
        description: "عرض شروط وأحكام السعر المختار (مثل غرامات التغيير والإلغاء). (Get fare rules and conditions).",
        tags: ["Flights"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["session_id", "fare_source_code"],
                properties: [
                    new OA\Property(property: "session_id", type: "string", example: "7906efba-09db..."),
                    new OA\Property(property: "fare_source_code", type: "string", example: "MTY2ODE2Njg2Ml8yNjA5Mzk"),
                    new OA\Property(property: "fare_source_code_inbound", type: "string", example: "")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم استرجاع شروط السعر بنجاح",
                content: new OA\JsonContent(properties: [new OA\Property(property: "error", type: "boolean", example: false)])
            )
        ]
    )]
    public function getFareRules(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
            'fare_source_code' => 'required|string',
            'fare_source_code_inbound' => 'nullable|string',
        ]);

        if ($validator->fails()) return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

        $result = $this->traveloproService->getFareRules($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Fare rules retrieved successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/refund-quote",
        summary: "طلب عرض سعر استرجاع (Refund Quote)",
        operationId: "refundQuote",
        description: "الحصول على عرض سعر وقيمة المبلغ المسترد قبل تنفيذ عملية الاسترجاع. (Get a quote for refund amount).",
        tags: ["Flights"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["uniqueId", "paxDetails"],
                properties: [
                    new OA\Property(property: "uniqueId", type: "string", example: "TR123456"),
                    new OA\Property(property: "remark", type: "string", example: "Refund requested due to illness"),
                    new OA\Property(
                        property: "paxDetails",
                        type: "array",
                        items: new OA\Items(
                             required: ["type", "title", "firstName", "lastName", "eTicket"],
                             properties: [
                                 new OA\Property(property: "type", type: "string", example: "ADT"),
                                 new OA\Property(property: "title", type: "string", example: "Mr"),
                                 new OA\Property(property: "firstName", type: "string", example: "John"),
                                 new OA\Property(property: "lastName", type: "string", example: "Doe"),
                                 new OA\Property(property: "eTicket", type: "string", example: "TKT123456789")
                             ]
                        )
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم استرجاع عرض الاسترجاع بنجاح"
            )
        ]
    )]
    public function refundQuote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uniqueId' => 'required|string',
            'paxDetails' => 'required|array|min:1',
            'paxDetails.*.type' => 'required|string',
            'paxDetails.*.title' => 'required|string',
            'paxDetails.*.firstName' => 'required|string',
            'paxDetails.*.lastName' => 'required|string',
            'paxDetails.*.eTicket' => 'required|string',
        ]);

        if ($validator->fails()) return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

        $result = $this->traveloproService->refundQuote($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Refund quote retrieved successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/refund-ticket",
        summary: "تنفيذ استرجاع التذكرة (Refund Ticket)",
        operationId: "refundTicket",
        description: "تنفيذ عملية استرجاع التذكرة فعلياً. (Process ticket refund).",
        tags: ["Flights"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["uniqueId", "paxDetails"],
                properties: [
                    new OA\Property(property: "uniqueId", type: "string", example: "TR123456"),
                    new OA\Property(property: "remark", type: "string", example: "Process refund"),
                    new OA\Property(
                        property: "paxDetails",
                        type: "array",
                         items: new OA\Items(
                             required: ["type", "title", "firstName", "lastName", "eTicket"],
                             properties: [
                                 new OA\Property(property: "type", type: "string", example: "ADT"),
                                 new OA\Property(property: "title", type: "string", example: "Mr"),
                                 new OA\Property(property: "firstName", type: "string", example: "John"),
                                 new OA\Property(property: "lastName", type: "string", example: "Doe"),
                                 new OA\Property(property: "eTicket", type: "string", example: "TKT123456789")
                             ]
                        )
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم طلب الاسترجاع بنجاح"
            )
        ]
    )]
    public function refundTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uniqueId' => 'required|string',
            'paxDetails' => 'required|array|min:1',
        ]);

        if ($validator->fails()) return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

        $result = $this->traveloproService->refundTicket($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Refund request processed successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/reissue-quote",
        summary: "طلب عرض سعر تعديل (Reissue Quote)",
        operationId: "reissueQuote",
        description: "الحصول على تكلفة تعديل الحجز. (Get a quote for ticket reissue/change).",
        tags: ["Flights"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["uniqueId", "paxDetails", "OriginDestinationInfo"],
                properties: [
                    new OA\Property(property: "uniqueId", type: "string", example: "TR123456"),
                     new OA\Property(
                        property: "paxDetails",
                        type: "array",
                        items: new OA\Items(
                             required: ["type", "title", "firstName", "lastName", "eTicket"],
                             properties: [
                                 new OA\Property(property: "type", type: "string", example: "ADT"),
                                 new OA\Property(property: "title", type: "string", example: "Mr"),
                                 new OA\Property(property: "firstName", type: "string", example: "John"),
                                 new OA\Property(property: "lastName", type: "string", example: "Doe"),
                                 new OA\Property(property: "eTicket", type: "string", example: "TKT123456789")
                             ]
                        )
                    ),
                    new OA\Property(
                        property: "OriginDestinationInfo",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "departureDate", type: "string", format: "date"),
                                new OA\Property(property: "airportOriginCode", type: "string"),
                                new OA\Property(property: "airportDestinationCode", type: "string")
                            ]
                        )
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم استرجاع عرض التعديل بنجاح"
            )
        ]
    )]
    public function reissueQuote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uniqueId' => 'required|string',
            'paxDetails' => 'required|array|min:1',
            'OriginDestinationInfo' => 'required|array|min:1'
        ]);

        if ($validator->fails()) return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

        $result = $this->traveloproService->reissueQuote($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Reissue quote retrieved successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/reissue-ticket",
        summary: "تنفيذ تعديل التذكرة (Reissue Ticket)",
        operationId: "reissueTicket",
        description: "تنفيذ عملية تعديل التذكرة فعلياً بناءً على العرض المختار. (Process ticket reissue).",
        tags: ["Flights"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["uniqueId", "ptrUniqueID", "PreferenceOption"],
                properties: [
                    new OA\Property(property: "uniqueId", type: "string", example: "TR123456"),
                    new OA\Property(property: "ptrUniqueID", type: "string", example: "9154"),
                    new OA\Property(property: "PreferenceOption", type: "string", example: "1"),
                    new OA\Property(property: "remark", type: "string", example: "Reissue please")
                ]
            )
        ),
        responses: [
             new OA\Response(
                response: 200,
                description: "تم طلب التعديل بنجاح"
            )
        ]
    )]
    public function reissueTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uniqueId' => 'required|string',
            'ptrUniqueID' => 'required|string',
            'PreferenceOption' => 'required',
        ]);

        if ($validator->fails()) return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

        $result = $this->traveloproService->reissueTicket($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Reissue request processed successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/void-quote",
        summary: "طلب عرض تكلفة الإلغاء (Void Quote)",
        operationId: "voidQuote",
        description: "الحصول على تكلفة إلغاء التذكرة (Void) قبل تنفيذها. (Get void ticket quote).",
        tags: ["Flights"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["uniqueId", "paxDetails"],
                properties: [
                    new OA\Property(property: "uniqueId", type: "string", example: "TR123456"),
                    new OA\Property(
                        property: "paxDetails",
                        type: "array",
                        items: new OA\Items(
                             required: ["type", "title", "firstName", "lastName", "eTicket"],
                             properties: [
                                 new OA\Property(property: "type", type: "string", example: "ADT"),
                                 new OA\Property(property: "title", type: "string", example: "Mr"),
                                 new OA\Property(property: "firstName", type: "string", example: "John"),
                                 new OA\Property(property: "lastName", type: "string", example: "Doe"),
                                 new OA\Property(property: "eTicket", type: "string", example: "TKT123456789")
                             ]
                        )
                    ),
                ]
            )
        ),
        responses: [
             new OA\Response(
                response: 200,
                description: "تم استرجاع عرض الإلغاء بنجاح"
            )
        ]
    )]
    public function voidQuote(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'uniqueId' => 'required|string',
            'paxDetails' => 'required|array|min:1',
        ]);

        if ($validator->fails()) return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

        $result = $this->traveloproService->voidQuote($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Void quote retrieved successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/void-ticket",
        summary: "تنفيذ إلغاء التذكرة (Void Ticket)",
        operationId: "voidTicket",
        description: "إلغاء التذكرة (Void) في نفس يوم الإصدار. (Void ticket within same day).",
        tags: ["Flights"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["uniqueId", "paxDetails"],
                properties: [
                    new OA\Property(property: "uniqueId", type: "string", example: "TR123456"),
                     new OA\Property(property: "remark", type: "string", example: "Voiding ticket"),
                    new OA\Property(
                        property: "paxDetails",
                        type: "array",
                        items: new OA\Items(
                             required: ["type", "title", "firstName", "lastName", "eTicket"],
                             properties: [
                                 new OA\Property(property: "type", type: "string", example: "ADT"),
                                 new OA\Property(property: "title", type: "string", example: "Mr"),
                                 new OA\Property(property: "firstName", type: "string", example: "John"),
                                 new OA\Property(property: "lastName", type: "string", example: "Doe"),
                                 new OA\Property(property: "eTicket", type: "string", example: "TKT123456789")
                             ]
                        )
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم طلب الإلغاء بنجاح"
            )
        ]
    )]
    public function voidTicket(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'uniqueId' => 'required|string',
            'paxDetails' => 'required|array|min:1',
        ]);

        if ($validator->fails()) return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

        $result = $this->traveloproService->voidTicket($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Void request processed successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/post-ticket-status",
        summary: "حالة طلب ما بعد التذكرة (Post-ticket status)",
        operationId: "searchPostTicketStatus",
        description: "الاستعلام عن حالة طلبات الاسترجاع، الإلغاء، أو التعديل. (Check status of Refund/Void/Reissue requests).",
        tags: ["Flights"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["uniqueId", "ptrUniqueID"],
                properties: [
                    new OA\Property(property: "uniqueId", type: "string", example: "TR123456"),
                    new OA\Property(property: "ptrUniqueID", type: "string", example: "9154")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم استرجاع حالة الطلب بنجاح"
            )
        ]
    )]
    public function searchPostTicketStatus(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'uniqueId' => 'required|string',
            'ptrUniqueID' => 'required|string'
        ]);

        if ($validator->fails()) return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

        $result = $this->traveloproService->searchPostTicketStatus($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Status retrieved successfully.'), $result, null, 200);
    }
}
