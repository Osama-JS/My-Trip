<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TraveloproService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class FlightController extends Controller
{
    protected $traveloproService;

    public function __construct(TraveloproService $traveloproService)
    {
        $this->traveloproService = $traveloproService;
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
                        new OA\Property(property: "message", type: "string", example: "Booking created successfully."),
                        new OA\Property(property: "data", type: "object")
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

        return $this->apiResponse(false, __('Booking created successfully.'), $result, null, 200);
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

        $result = $this->traveloproService->orderTicket($request->uniqueId);

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? $result['error'], null, 500);
        }

        return $this->apiResponse(false, __('Ticket ordered successfully.'), $result, null, 200);
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

        $result = $this->traveloproService->getTripDetails($request->uniqueId);

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? $result['error'], null, 500);
        }

        return $this->apiResponse(false, __('Trip details retrieved successfully.'), $result, null, 200);
    }
}
