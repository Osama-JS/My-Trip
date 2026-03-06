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
                    new OA\Property(property: "residentNationality", type: "string", example: "SA")
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
            'checkIn' => 'required|date_format:Y-m-d',
            'checkOut' => 'required|date_format:Y-m-d|after:checkIn',
            'rooms' => 'required|integer|min:1',
            'adults' => 'required|integer|min:1',
            'childs' => 'nullable|integer|min:0',
            'childAge' => 'required_if:childs,>0|array',
            'requiredCurrency' => 'nullable|string|size:3',
            'residentNationality' => 'nullable|string|size:2',
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
                required: ["hotelId", "sessionId"],
                properties: [
                    new OA\Property(property: "hotelId", type: "string", example: "H12345"),
                    new OA\Property(property: "sessionId", type: "string", example: "sess-abc-123")
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
                required: ["rateBasisId", "sessionId"],
                properties: [
                    new OA\Property(property: "rateBasisId", type: "string", example: "RB123"),
                    new OA\Property(property: "sessionId", type: "string", example: "sess-abc-123")
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
                required: ["rateBasisId", "sessionId", "customerEmail", "customerPhone", "paxDetails"],
                properties: [
                    new OA\Property(property: "rateBasisId", type: "string", example: "RB123"),
                    new OA\Property(property: "sessionId", type: "string", example: "sess-abc-123"),
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
            'customerEmail' => 'required|email',
            'customerPhone' => 'required|string',
            'paxDetails' => 'required|array|min:1',
            'bookingNote' => 'nullable|string',
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
    public function cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplierConfirmationNumber' => 'required|string',
            'referenceNumber' => 'required|string',
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
