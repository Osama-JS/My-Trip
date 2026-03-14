<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TraveloproHotelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class HotelController extends Controller
{
    protected $hotelService;

    public function __construct(TraveloproHotelService $hotelService)
    {
        $this->hotelService = $hotelService;
    }

    #[OA\Post(
        path: "/api/hotels/search",
        summary: "البحث عن الفنادق (Search for hotels)",
        operationId: "searchHotels",
        description: "البحث عن الفنادق المتاحة عبر Travelopro API مع دعم التصفية المتقدمة. (Search for hotel availability).",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["checkIn", "checkOut", "rooms", "adults"],
                properties: [
                    new OA\Property(property: "cityName", type: "string", example: "Dubai"),
                    new OA\Property(property: "countryName", type: "string", example: "United Arab Emirates"),
                    new OA\Property(property: "checkIn", type: "string", format: "date", example: "2024-12-01"),
                    new OA\Property(property: "checkOut", type: "string", format: "date", example: "2024-12-10"),
                    new OA\Property(property: "rooms", type: "integer", example: 1),
                    new OA\Property(property: "adults", type: "integer", example: 1),
                    new OA\Property(property: "childs", type: "integer", example: 0),
                    new OA\Property(property: "childAge", type: "array", items: new OA\Items(type: "integer"), example: []),
                    new OA\Property(property: "requiredCurrency", type: "string", example: "SAR"),
                    new OA\Property(property: "residentNationality", type: "string", example: "SA"),
                    new OA\Property(property: "requiredLanguage", type: "string", example: "ARA")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "تم استرجاع الفنادق بنجاح")
        ]
    )]
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cityName' => 'required_without:latitude|string',
            'countryName' => 'required_without:latitude|string',
            'latitude' => 'required_without:cityName|numeric',
            'longitude' => 'required_without:cityName|numeric',
            'radius' => 'nullable|integer',
            'checkIn' => 'required|date_format:Y-m-d|after_or_equal:today',
            'checkOut' => 'required|date_format:Y-m-d|after:checkIn',
            'rooms' => 'required|integer|min:1',
            'adults' => 'required|integer|min:1',
            'childs' => 'nullable|integer|min:0',
            'childAge' => 'required_if:childs,>0|array',
            'requiredCurrency' => 'nullable|string|size:3',
            'requiredLanguage' => 'nullable|string|size:3',
        ], [
            'checkIn.after_or_equal' => __('تاريخ الدخول يجب أن يكون اليوم أو تاريخاً مستقبلياً.'),
            'checkOut.after' => __('تاريخ الخروج يجب أن يكون بعد تاريخ الدخول.'),
            'cityName.required_without' => __('يجب إدخال اسم المدينة أو الإحداثيات.'),
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->hotelService->search($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Hotels retrieved successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/hotels/room-rates",
        summary: "جلب أسعار الغرف (Get room rates)",
        operationId: "getRoomRates",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["hotelId", "sessionId", "productId", "tokenId"],
                properties: [
                    new OA\Property(property: "hotelId", type: "string", example: "H12345"),
                    new OA\Property(property: "sessionId", type: "string", example: "sess-abc-123"),
                    new OA\Property(property: "productId", type: "string"),
                    new OA\Property(property: "tokenId", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "تم استرجاع أسعار الغرف بنجاح")
        ]
    )]
    public function roomRates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotelId' => 'required|string',
            'sessionId' => 'required|string',
            'productId' => 'required|string',
            'tokenId' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->hotelService->getRoomRates($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Room rates retrieved successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/hotels/check-rates",
        summary: "التحقق من السعر قبل الحجز (Check room rates)",
        operationId: "checkRoomRates",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["rateBasisId", "sessionId", "productId", "tokenId"],
                properties: [
                    new OA\Property(property: "rateBasisId", type: "string", example: "RB123"),
                    new OA\Property(property: "sessionId", type: "string", example: "sess-abc-123"),
                    new OA\Property(property: "productId", type: "string"),
                    new OA\Property(property: "tokenId", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "تم التحقق من السعر بنجاح")
        ]
    )]
    public function checkRates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rateBasisId' => 'required|string',
            'sessionId' => 'required|string',
            'productId' => 'required|string',
            'tokenId' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->hotelService->checkRoomRates($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Rates verified successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/hotels/book",
        summary: "حجز فندق (Book a hotel)",
        operationId: "bookHotel",
        tags: ["Hotels"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["rateBasisId", "sessionId", "productId", "tokenId", "customerEmail", "customerPhone", "paxDetails"],
                properties: [
                    new OA\Property(property: "rateBasisId", type: "string", example: "RB123"),
                    new OA\Property(property: "sessionId", type: "string", example: "sess-abc-123"),
                    new OA\Property(property: "productId", type: "string"),
                    new OA\Property(property: "tokenId", type: "string"),
                    new OA\Property(property: "customerEmail", type: "string", format: "email", example: "guest@example.com"),
                    new OA\Property(property: "customerPhone", type: "string", example: "966500000000"),
                    new OA\Property(property: "paxDetails", type: "array", items: new OA\Items(type: "object"))
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "تم طلب الحجز بنجاح")
        ]
    )]
    public function book(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rateBasisId' => 'required|string',
            'sessionId' => 'required|string',
            'productId' => 'required|string',
            'tokenId' => 'required|string',
            'customerEmail' => 'required|email',
            'customerPhone' => 'required|string',
            'paxDetails' => 'required|array|min:1',
            'bookingNote' => 'nullable|string',
            'clientRef' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->hotelService->book($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        // Logic for persisting booking could follow FlightController's model...
        // For brevity, we return the supplier's result first.

        return $this->apiResponse(false, __('Hotel booking initiated successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/hotels/cancel",
        summary: "إلغاء حجز فندق (Cancel hotel booking)",
        operationId: "cancelHotelBooking",
        tags: ["Hotels"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["supplierConfirmationNumber", "referenceNumber"],
                properties: [
                    new OA\Property(property: "supplierConfirmationNumber", type: "string", example: "SUP123"),
                    new OA\Property(property: "referenceNumber", type: "string", example: "REF456")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "تم إلغاء الحجز بنجاح")
        ]
    )]
    #[OA\Post(
        path: "/api/hotels/next-page",
        summary: "جلب المزيد من الفنادق (Pagination)",
        operationId: "hotelNextPage",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["sessionId", "nextToken"],
                properties: [
                    new OA\Property(property: "sessionId", type: "string"),
                    new OA\Property(property: "nextToken", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "تم استرجاع الصفحة التالية بنجاح")
        ]
    )]
    public function nextToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sessionId' => 'required|string',
            'nextToken' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->hotelService->nextToken($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Next page retrieved successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/hotels/filter",
        summary: "تصفية الفنادق (Filter hotels)",
        operationId: "filterHotels",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["sessionId"],
                properties: [
                    new OA\Property(property: "sessionId", type: "string"),
                    new OA\Property(property: "hotelName", type: "string"),
                    new OA\Property(property: "minPrice", type: "number"),
                    new OA\Property(property: "maxPrice", type: "number"),
                    new OA\Property(property: "starRating", type: "string"),
                    new OA\Property(property: "requiredLanguage", type: "string", example: "ARA")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "تم تصفية النتائج بنجاح")
        ]
    )]
    public function filter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sessionId' => 'required|string',
            'requiredLanguage' => 'nullable|string|size:3',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->hotelService->filter($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Hotels filtered successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/hotels/content",
        summary: "جلب محتوى الفندق (Hotel content)",
        operationId: "getHotelContent",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["hotelId", "sessionId", "productId", "tokenId"],
                properties: [
                    new OA\Property(property: "hotelId", type: "string"),
                    new OA\Property(property: "sessionId", type: "string"),
                    new OA\Property(property: "productId", type: "string"),
                    new OA\Property(property: "tokenId", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "تم استرجاع محتوى الفندق بنجاح")
        ]
    )]
    public function getHotelContent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotelId' => 'required|string',
            'sessionId' => 'required|string',
            'productId' => 'required|string',
            'tokenId' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->hotelService->getHotelContent($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Hotel content retrieved successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/hotels/booking-details",
        summary: "تفاصيل الحجز (Booking details)",
        operationId: "getHotelBookingDetails",
        tags: ["Hotels"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["bookingReference"],
                properties: [
                    new OA\Property(property: "bookingReference", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "تم استرجاع تفاصيل الحجز بنجاح")
        ]
    )]
    public function getBookingDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplierConfirmationNum' => 'required|string',
            'referenceNum' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->hotelService->getBookingDetails($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Booking details retrieved successfully.'), $result, null, 200);
    }

    #[OA\Get(
        path: "/api/hotels/cities",
        summary: "قائمة المدن (Cities list)",
        operationId: "getHotelCities",
        tags: ["Hotels"],
        responses: [
            new OA\Response(response: 200, description: "تم استرجاع قائمة المدن بنجاح")
        ]
    )]
    public function getCities(Request $request)
    {
        $result = $this->hotelService->getCities($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Cities retrieved successfully.'), $result, null, 200);
    }

    #[OA\Get(
        path: "/api/hotels/languages",
        summary: "قائمة اللغات (Languages list)",
        operationId: "getHotelLanguages",
        tags: ["Hotels"],
        responses: [
            new OA\Response(response: 200, description: "تم استرجاع قائمة اللغات بنجاح")
        ]
    )]
    public function getLanguages(Request $request)
    {
        $result = $this->hotelService->getLanguages($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Languages retrieved successfully.'), $result, null, 200);
    }

    public function cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplierConfirmationNum' => 'required|string',
            'referenceNum' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->hotelService->cancel($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Hotel booking cancelled successfully.'), $result, null, 200);
    }
}
