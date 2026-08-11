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
        description: "Retrieve a list of supported airports from local database.",
        tags: ["Flights"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            ),
            new OA\Parameter(
                name: "q",
                in: "query",
                description: "Search keyword for airport name, code, or city",
                required: false,
                schema: new OA\Schema(type: "string")
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
    public function getAirports(Request $request)
    {
        $q = $request->get('q');
        $lang = $request->get('lang');

        if ($lang) {
            app()->setLocale($lang);
        }

        $query = \App\Models\Airport::query();

        if ($q) {
            $query->where(function ($query) use ($q) {
                $query->where('airport_name', 'like', "%{$q}%")
                    ->orWhere('airport_name_ar', 'like', "%{$q}%")
                    ->orWhere('airport_code', 'like', "%{$q}%")
                    ->orWhere('city_name', 'like', "%{$q}%")
                    ->orWhere('city_name_ar', 'like', "%{$q}%");
            });
        }

        $locale = app()->getLocale();
        $isArabic = ($locale === 'ar');

        $airports = $query->orderBy('airport_name')
            ->get()
            ->map(function ($airport) {
                return [
                    'AirportCode' => $airport->airport_code,
                    'AirportName' => $airport->airport_name,
                    'AirportNameAr' => $airport->airport_name_ar ?? $airport->airport_name,
                    'City' => $airport->city_name,
                    'CityAr' => $airport->city_name_ar ?? $airport->city_name,
                    'Country' => $airport->country_name,
                    'CountryAr' => $airport->country_name_ar ?? $airport->country_name,
                ];
            });

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

        $isValid = $result['AirRevalidateResponse']['AirRevalidateResult']['IsValid'] ?? false;
        
        $apiResult = $result['AirRevalidateResponse']['AirRevalidateResult'] ?? [];
        $isPassport = $apiResult['FareItineraries']['FareItinerary']['IsPassportMandatory'] ?? null;
        if ($isPassport === null && isset($apiResult['FareItineraries']['FareItinerary'][0]['IsPassportMandatory'])) {
            $isPassport = $apiResult['FareItineraries']['FareItinerary'][0]['IsPassportMandatory'];
        }
        
        // Manual fallback check for international flights
        if ($isPassport === null && isset($request->origin) && isset($request->destination)) {
            $originAirport = \App\Models\Airport::where('code', $request->origin)->first();
            $destAirport = \App\Models\Airport::where('code', $request->destination)->first();
            
            if ($originAirport && $destAirport) {
                if ($originAirport->country_code !== $destAirport->country_code) {
                    $isPassport = true;
                } else {
                    $isPassport = false;
                }
            }
        }
        
        // Inject into the response root for easy access by Flutter
        if (!isset($result['is_passport_mandatory'])) {
            $result['is_passport_mandatory'] = $isPassport ?? false;
            // Also inject into result data
            $result['AirRevalidateResponse']['AirRevalidateResult']['IsPassportMandatory'] = $isPassport ?? false;
        }

        if ($isValid === true || $isValid === 'true' || $isValid === 'True') {
            return $this->apiResponse(false, __('Fare is valid.'), $result, null, 200);
        }

        return $this->apiResponse(true, __('Fare is no longer valid or available.'), $result, null, 422);
    }

    #[OA\Post(
        path: "/api/flights/extra-services",
        summary: "الخدمات الإضافية (Extra Services: Baggage, Meals, Seats)",
        operationId: "getExtraServices",
        description: "Fetch extra services (DynamicBaggage, DynamicMeal, DynamicSeat) for a specific flight session.",
        tags: ["Flights"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["session_id", "fare_source_code"],
                properties: [
                    new OA\Property(property: "session_id", type: "string"),
                    new OA\Property(property: "fare_source_code", type: "string")
                ]
            )
        )
    )]
    public function getExtraServices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
            'fare_source_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->traveloproService->getExtraServices($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? $result['error'], null, 500);
        }

        $formattedServices = [];
        $extraServicesData = $result['ExtraServicesData'] ?? $result['ExtraServicesResponse']['ExtraServicesResult']['ExtraServicesData'] ?? $result['ExtraServicesResponse']['ExtraServicesData'] ?? $result['ExtraServicesResponse'] ?? [];

        // Parse Baggage
        $baggages = $extraServicesData['DynamicBaggage'] ?? [];
        foreach ($baggages as $baggageGroup) {
            $flightType = str_contains($baggageGroup['Behavior'] ?? '', 'INBOUND') ? 'inbound' : 'outbound';
            $services = $baggageGroup['Services'] ?? [];
            if (isset($services[0]) && is_array($services[0]) && !isset($services[0]['ServiceId'])) {
                $services = $services[0];
            }
            foreach ($services as $svc) {
                $formattedServices[] = [
                    'type' => 'baggage',
                    'flight_type' => $flightType,
                    'code' => $svc['ServiceId'] ?? '',
                    'price' => $svc['ServiceCost']['Amount'] ?? 0,
                    'currency' => $svc['ServiceCost']['CurrencyCode'] ?? 'SAR',
                    'description' => $svc['Description'] ?? '',
                ];
            }
        }

        // Parse Meals
        $meals = $extraServicesData['DynamicMeal'] ?? [];
        foreach ($meals as $mealGroup) {
            $flightType = str_contains($mealGroup['Behavior'] ?? '', 'INBOUND') ? 'inbound' : 'outbound';
            $services = $mealGroup['Services'] ?? [];
            if (isset($services[0]) && is_array($services[0]) && !isset($services[0]['ServiceId'])) {
                $services = $services[0];
            }
            foreach ($services as $svc) {
                $formattedServices[] = [
                    'type' => 'meal',
                    'flight_type' => $flightType,
                    'code' => $svc['ServiceId'] ?? '',
                    'price' => $svc['ServiceCost']['Amount'] ?? 0,
                    'currency' => $svc['ServiceCost']['CurrencyCode'] ?? 'SAR',
                    'description' => $svc['Description'] ?? '',
                ];
            }
        }

        // Parse Seats
        $seats = $extraServicesData['DynamicSeat'] ?? [];
        foreach ($seats as $seatGroup) {
            // Wait, seats structure has DeckSeats -> RowSeats -> Seats
            $decks = $seatGroup['DeckSeats'] ?? [];
            foreach ($decks as $deck) {
                $rows = $deck['RowSeats'] ?? [];
                foreach ($rows as $row) {
                    $rowSeats = $row['Seats'] ?? [];
                    foreach ($rowSeats as $seat) {
                        // Only add available seats (Code 1 = Open usually, let's include if it has price)
                        $avail = $seat['AvailablityType']['Code'] ?? '';
                        if ($avail == '1') { // 1 = Open
                            $formattedServices[] = [
                                'type' => 'seat',
                                'flight_type' => 'outbound', // Simplification, need to check segment
                                'code' => $seat['ServiceId'] ?? '',
                                'price' => $seat['Fare']['Amount'] ?? 0,
                                'currency' => $seat['Fare']['CurrencyCode'] ?? 'SAR',
                                'description' => 'Seat ' . ($seat['SeatCode'] ?? ''),
                                'row' => $seat['RowNo'] ?? '',
                                'seat' => $seat['SeatNo'] ?? '',
                                'type_text' => $seat['SeatType']['Text'] ?? '',
                            ];
                        }
                    }
                }
            }
        }

        return $this->apiResponse(false, __('Extra services retrieved successfully.'), $formattedServices, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/book",
        summary: "إنشاء سجل حجز PNR (Create flight booking)",
        operationId: "bookFlight",
        description: "إنشاء سجل حجز (PNR) مبدئي وحفظ بيانات المسافرين قبل عملية الدفع وإصدار التذكرة. (Create a PNR using passenger details).",
        tags: ["Flights"],
        security: [["bearerAuth" => []]],
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
                    new OA\Property(property: "airline_code", type: "string", example: "XY"),
                    new OA\Property(property: "airline_name", type: "string", example: "Flynas"),
                    new OA\Property(property: "flight_number", type: "string", example: "XY123"),
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
                            new OA\Property(property: "BookFlightResponse", type: "object")
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
        $user = auth()->user();
        if ($user && !$user->isProfileComplete()) {
            return $this->apiResponse(true, __('PROFILE_INCOMPLETE'), null, null, 403);
        }

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
            // ✅ P1: Collect passport issue date — sent when IsPassportFullDetailsMandatory=true
            'passengers.*.passport_issue_date' => 'nullable|date_format:Y-m-d',
            'passengers.*.passport_image' => 'nullable',
            // Ancillaries
            'passengers.*.extra_services_outbound' => 'nullable|array',
            'passengers.*.extra_services_inbound' => 'nullable|array',
            'passengers.*.seat_outbound' => 'nullable|array',
            'passengers.*.seat_inbound' => 'nullable|array',
            'airline_code' => 'nullable|string',
            'airline_name' => 'nullable|string',
            'flight_number' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        // Check if a booking already exists for this session to prevent duplicate requests
        $existingBooking = \App\Models\Booking::where('user_id', Auth::id())
            ->where('supplier_session_id', $request->flight_session_id)
            ->first();

        if ($existingBooking) {
            Log::info('Duplicate Booking Request Prevented', ['booking_id' => $existingBooking->id]);
            
            $payment_info = [
                'booking_id' => $existingBooking->id,
                'amount' => $existingBooking->total_amount,
                'currency' => $existingBooking->currency,
                'methods' => \App\Helpers\PaymentHelper::getAvailableMethods($existingBooking->id, 'flight')
            ];

            return $this->apiResponse(false, __('Booking already exists. Redirecting...'), [
                'payment_info' => $payment_info,
                'payment_url' => $payment_info['methods'][0]['url'] ?? '',
                'payment_api_url' => url('/api/payment/initiate'),
                'booking_id' => $existingBooking->id,
            ], null, 200);
        }

        Log::info('Initiating Travelopro Booking Request', ['user_id' => Auth::id()]);
        $result = $this->traveloproService->createBooking($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            Log::error('Travelopro Booking Service Error', ['error' => $result]);
            return $this->apiResponse(true, $result['message'] ?? 'Error', $result['details'] ?? ($result['error'] ?? null), null, 500);
        }

        // Persist booking to local database
        try {
            Log::info('Travelopro API Response Received', ['keys' => array_keys($result)]);

            // Handle both possible response keys: BookFlightResponse (Docs) or CreateBookingResponse (Current Code potentially)
            $bookingData = $result['BookFlightResponse'] ?? $result['CreateBookingResponse'] ?? null;

            if (!$bookingData) {
                Log::error('Booking response structure unknown', ['full_response' => $result]);
            }

            $bookingResult = $bookingData['BookFlightResult'] ?? $bookingData['CreateBookingResult'] ?? null;
            if ($bookingResult) {
                $isSuccess = $bookingResult['Success'] ?? false;
                if (is_string($isSuccess)) {
                    $isSuccess = (strtolower($isSuccess) === 'true');
                }

                if (!$isSuccess) {
                    $errorMessage = $bookingResult['Errors']['Error']['ErrorMessage'] ?? $bookingResult['Errors']['ErrorMessage'] ?? __('Booking failed at provider.');
                    Log::warning('Travelopro API returned success=false', ['message' => $errorMessage]);
                    return $this->apiResponse(true, $errorMessage, $result, null, 400);
                }
            }

            $uniqueId = $bookingData['BookFlightResult']['UniqueID'] ?? $bookingData['BookFlightResult']['uniqueID'] ?? $bookingData['CreateBookingResult']['UniqueID'] ?? null;
            $totalAmount = $request->input('total_amount') 
                            ?? $bookingData['BookFlightResult']['TotalAmount'] 
                            ?? $bookingData['CreateBookingResult']['TotalAmount'] 
                            ?? 0;

            Log::info('Extracted Booking Identity', ['uniqueId' => $uniqueId, 'totalAmount' => $totalAmount]);

            // Calculate Profit Margin
            $margin = floatval(\App\Models\Setting::get('flight_margin', 0));
            $marginType = \App\Models\Setting::get('flight_margin_type', 'percentage');
            $profit = 0;
            $providerPrice = $totalAmount;
            if ($margin > 0) {
                if ($marginType === 'fixed') {
                    $profit = $margin;
                    $providerPrice = $totalAmount - $profit;
                } else {
                    $providerPrice = $totalAmount / (1 + ($margin / 100));
                    $profit = $totalAmount - $providerPrice;
                }
            }

            if ($uniqueId) {
                $itinerary = $bookingResult['Itineraries']['Itinerary'][0] ?? null;
                $booking = Booking::create([
                    'user_id' => Auth::id(),
                    'booking_reference' => $uniqueId,
                    'supplier_session_id' => $request->flight_session_id,
                    'status' => 'pending',
                    'ticket_status' => 'booked',
                    'total_amount' => $totalAmount,
                    'provider_price' => $providerPrice,
                    'platform_profit' => $profit,
                    'currency' => 'SAR',
                    'contact_email' => $request->customerEmail,
                    'contact_phone' => $request->customerPhone,
                    'pnr_created_at' => now(),
                    'ticketing_time_limit' => isset($bookingResult['TicketingTimeLimit']) 
                        ? \Carbon\Carbon::parse($bookingResult['TicketingTimeLimit']) 
                        : now()->addMinutes(3),
                    'airline_code' => $request->airline_code ?? ($itinerary['ValidatingAirlineCode'] ?? null),
                    'airline_name' => $request->airline_name ?? ($itinerary['ValidatingAirlineCode'] ?? null),
                ]);

                Log::info('Local Booking Record Created', ['booking_id' => $booking->id]);

                // ── Save FlightBooking details (route, class, pax counts) ──────
                try {
                    $originDest = $request->OriginDestinationInfo[0] ?? [];
                    
                    // Fallback to extract route from result if OriginDestinationInfo is missing or incomplete
                    $extractedOrigin = null;
                    $extractedDest = null;
                    if ($itinerary && isset($itinerary['OriginDestinationOptions']['OriginDestinationOption'])) {
                        $options = $itinerary['OriginDestinationOptions']['OriginDestinationOption'];
                        if (!isset($options[0])) $options = [$options]; // Normalize
                        $firstSegs = $options[0]['FlightSegment'] ?? [];
                        if (!isset($firstSegs[0])) $firstSegs = [$firstSegs];
                        $extractedOrigin = $firstSegs[0]['DepartureAirportLocationCode'] ?? null;
                        
                        $lastSegs = end($options)['FlightSegment'] ?? [];
                        if (!isset($lastSegs[0])) $lastSegs = [$lastSegs];
                        $extractedDest = end($lastSegs)['ArrivalAirportLocationCode'] ?? null;
                    }
                    
                    \App\Models\FlightBooking::create([
                        'user_id'        => Auth::id(),
                        'booking_id'     => $booking->id,
                        'origin'         => $request->origin ?? ($originDest['airportOriginCode'] ?? ($extractedOrigin ?? 'N/A')),
                        'destination'    => $request->destination ?? ($originDest['airportDestinationCode'] ?? ($extractedDest ?? 'N/A')),
                        'departure_date' => $request->departure_date ?? ($originDest['departureDate'] ?? now()->toDateString()),
                        'return_date'    => $request->return_date ?? ($originDest['returnDate'] ?? null),
                        'adults'         => (int)($request->adults ?? 1),
                        'childs'         => (int)($request->childs ?? 0),
                        'infants'        => (int)($request->infants ?? 0),
                        'flight_class'   => $request->class ?? 'Economy',
                        'flight_number' => $request->flight_number,
                        'airline_code'   => $request->airline_code ?? ($itinerary['ValidatingAirlineCode'] ?? null),
                        'airline_name'   => $request->airline_name ?? ($itinerary['ValidatingAirlineCode'] ?? null),
                        'itinerary_data' => $bookingResult, // Save full result for fallback display logic
                        'total_amount'   => $totalAmount,
                        'currency'       => 'SAR',
                    ]);
                    Log::info('FlightBooking details saved.', ['booking_id' => $booking->id]);
                } catch (\Exception $e) {
                    Log::warning('Could not save FlightBooking details: ' . $e->getMessage());
                }
                // ─────────────────────────────────────────────────────────────

                foreach ($request->passengers as $index => $pax) {
                    $imagePath = null;
                    $uploadedImage = $request->file("passengers.{$index}.passport_image") ?? ($pax['passport_image'] ?? null);

                    if ($uploadedImage instanceof \Illuminate\Http\UploadedFile) {
                        $imagePath = $uploadedImage->store('passports', 'public');
                    } elseif (is_string($uploadedImage) && preg_match('/^data:image\/(\w+);base64,/', $uploadedImage)) {
                        $imageParts = explode(';base64,', $uploadedImage);
                        $imageBase64 = base64_decode($imageParts[1]);
                        $fileName = uniqid() . '.png';
                        $imagePath = 'passports/' . $fileName;
                        \Illuminate\Support\Facades\Storage::disk('public')->put($imagePath, $imageBase64);
                    } elseif (is_string($uploadedImage) && !empty($uploadedImage)) {
                        $imagePath = $uploadedImage;
                    }

                    $passenger = $booking->passengers()->create([
                        'name'           => ($pax['title'] ?? '') . ' ' . ($pax['first_name'] ?? '') . ' ' . ($pax['last_name'] ?? ''),
                        'title'          => $pax['title'],
                        'first_name'     => $pax['first_name'],
                        'last_name'      => $pax['last_name'],
                        'passenger_type' => $pax['type'],
                        'dob'            => $pax['dob'],
                        'nationality'    => $pax['nationality'],
                        'passport_number' => $pax['passport_no'] ?? null,
                        'passport_expiry' => $pax['passport_expiry_date'] ?? null,
                        'passport_issue_country' => $pax['passport_issue_country'] ?? null,
                        'passport_image' => $imagePath,
                    ]);
                    Log::info('Passenger Saved', ['passenger_id' => $passenger->id]);
                }

                // Add Payment and Tracking details to response
                $payment_info = [
                    'booking_id' => $booking->id,
                    'amount' => $totalAmount,
                    'currency' => 'SAR',
                    'methods' => \App\Helpers\PaymentHelper::getAvailableMethods($booking->id, 'flight')
                ];

                $result['payment_info'] = $payment_info;
                $result['payment_url'] = $payment_info['methods'][0]['url']; // Legacy support
                $result['payment_api_url'] = url('/api/payment/initiate');
                $result['booking_id']      = $booking->id;

                if (isset($result['_api_log_id'])) {
                    \App\Models\FlightApiLog::where('id', $result['_api_log_id'])->update(['booking_id' => $booking->id]);
                }

                Log::info('Booking Persistence Successfully Completed', ['booking_id' => $booking->id]);
            } else {
                Log::warning('No UniqueID found in booking response, persistence skipped.');
            }
        } catch (\Exception $e) {
            Log::error('CRITICAL: Failed to persist booking to database', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $this->apiResponse(false, __('Booking created successfully. Please proceed to payment.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/order-ticket",
        summary: "إصدار التذكرة (Order ticket / Issuance)",
        operationId: "orderTicket",
        description: "إصدار التذكرة النهائية وحجزها بشكل قطعي بعد عملية الحجز المبدئي (PNR) والتأكد من الدفع. (Issue ticket for a confirmed booking).",
        tags: ["Flights"],
        security: [["bearerAuth" => []]],
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

        Log::info('Initiating Travelopro Order Ticket Request', ['uniqueId' => $request->uniqueId, 'booking_id' => $booking->id]);
        $result = $this->traveloproService->orderTicket($request->uniqueId, $booking->id);

        if (isset($result['status']) && $result['status'] === 'error') {
            Log::error('Travelopro Order Ticket Service Error', ['error' => $result, 'booking_id' => $booking->id]);
            return $this->apiResponse(true, $result['message'], $result['details'] ?? $result['error'], null, 500);
        }

        try {
            if ($booking) {
                $booking->update([
                    'status' => 'confirmed',
                    'ticket_status' => 'ticketed',
                    'updated_at' => now()
                ]);
                Log::info('Booking Status Updated to Confirmed', ['booking_id' => $booking->id]);

                // Generate Invoice
                $pdfContent = $this->invoiceService->generateInvoice($booking);
                if ($pdfContent) {
                    $invoiceContent = base64_encode($pdfContent);
                    Log::info('Invoice Generated Successfully', ['booking_id' => $booking->id]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to update booking status or generate invoice', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id
            ]);
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
        security: [["bearerAuth" => []]],
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
        $result['local_booking_status'] = $booking->status;
        $result['local_booking_id'] = $booking->id;
        
        $booking->load(['passengers', 'flightBooking']);
        $result['local_passengers'] = $booking->passengers;
        
        $originCode = $booking->flightBooking->origin ?? null;
        $destCode = $booking->flightBooking->destination ?? null;
        $airportNames = [];
        $codesToFetch = [];
        
        if ($originCode && $originCode !== 'N/A') {
            $codesToFetch[] = $originCode;
        }
        if ($destCode && $destCode !== 'N/A') {
            $codesToFetch[] = $destCode;
        }

        // Extract from Travelopro result as fallback
        $resItems = $result['TripDetailsResponse']['TripDetailsResult']['TravelItinerary']['ItineraryInfo']['ReservationItems'] ?? [];
        // Handle single item or array of items
        if (isset($resItems['ReservationItem'])) {
            $resItems = [$resItems];
        } elseif (isset($resItems[0]) && !isset($resItems[0]['ReservationItem'])) {
            $resItems = array_map(function($i) { return ['ReservationItem' => $i]; }, $resItems);
        }
        foreach ($resItems as $item) {
            $rItem = $item['ReservationItem'] ?? $item;
            if (isset($rItem['DepartureAirport']['LocationCode'])) {
                $codesToFetch[] = $rItem['DepartureAirport']['LocationCode'];
            }
            if (isset($rItem['ArrivalAirport']['LocationCode'])) {
                $codesToFetch[] = $rItem['ArrivalAirport']['LocationCode'];
            }
        }
        
        $codesToFetch = array_unique($codesToFetch);
        
        if (!empty($codesToFetch)) {
            $apts = \App\Models\Airport::whereIn('airport_code', $codesToFetch)->get();
            foreach ($apts as $apt) {
                $airportNames[$apt->airport_code] = $apt->city_name . ' - ' . $apt->airport_name;
            }
        }
        
        $result['airport_names'] = empty($airportNames) ? new \stdClass() : $airportNames;
        $result['invoice_url'] = route('customer.bookings.invoice', ['id' => $booking->id, 'type' => 'flight']);

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

        if ($validator->fails())
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

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

        if ($validator->fails())
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

        $booking = Booking::where('booking_reference', $request->uniqueId)->first();
        $result = $this->traveloproService->cancelBooking($request->all(), $booking ? $booking->id : null);

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

        if ($validator->fails())
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

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

        if ($validator->fails())
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

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

        if ($validator->fails())
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

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

        if ($validator->fails())
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

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

        if ($validator->fails())
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

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

        if ($validator->fails())
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

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

        if ($validator->fails())
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

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

        if ($validator->fails())
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

        $result = $this->traveloproService->searchPostTicketStatus($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Status retrieved successfully.'), $result, null, 200);
    }

    /**
     * Get post-ticket booking status (polling endpoint for P2 implementation).
     */
    public function getPostTicketStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UniqueID' => 'required|string',
        ]);

        if ($validator->fails())
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);

        $result = $this->traveloproService->getPostTicketStatus($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], null, null, 500);
        }

        return $this->apiResponse(false, __('Post-ticket status retrieved.'), $result, null, 200);
    }

}
