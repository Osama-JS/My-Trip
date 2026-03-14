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
        description: "البحث عن الفنادق المتاحة. يجب استخدامcityName و countryName من دالة /api/hotels/cities. (Search for hotels. Use cityName and countryName from /api/hotels/cities).",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["checkIn", "checkOut", "rooms", "adults"],
                properties: [
                    new OA\Property(property: "cityName", type: "string", description: "اسم المدينة من دالة المدن (City name from cities API)", example: "Dubai"),
                    new OA\Property(property: "countryName", type: "string", description: "اسم الدولة من دالة المدن (Country name from cities API)", example: "United Arab Emirates"),
                    new OA\Property(property: "checkIn", type: "string", format: "date", description: "تاريخ الدخول YYYY-MM-DD", example: "2024-12-01"),
                    new OA\Property(property: "checkOut", type: "string", format: "date", description: "تاريخ الخروج YYYY-MM-DD", example: "2024-12-10"),
                    new OA\Property(property: "rooms", type: "integer", description: "عدد الغرف", example: 1),
                    new OA\Property(property: "adults", type: "integer", description: "عدد البالغين", example: 1),
                    new OA\Property(property: "childs", type: "integer", description: "عدد الأطفال", example: 0),
                    new OA\Property(property: "childAge", type: "array", items: new OA\Items(type: "integer"), description: "أعمار الأطفال", example: []),
                    new OA\Property(property: "requiredCurrency", type: "string", description: "العملة (ISO 3 letters)", example: "SAR"),
                    new OA\Property(property: "residentNationality", type: "string", description: "جنسية المقيم (ISO 2 letters)", example: "SA"),
                    new OA\Property(property: "requiredLanguage", type: "string", description: "اللغة من دالة اللغات (Language code from languages API)", example: "ARA")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "تم استرجاع الفنادق بنجاح. لاحظ وجود sessionId و nextToken و moreResults في النتيجة.")
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
        description: "جلب قائمة الغرف والأسعار لفندق معين. البيانات تأتي من نتيجة البحث للفندق المختار. (Get room rates for a selected hotel. Data comes from search results).",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["hotelId", "sessionId", "productId", "tokenId"],
                properties: [
                    new OA\Property(property: "hotelId", type: "string", description: "معرف الفندق من نتيجة البحث (hotelId from search)", example: "H12345"),
                    new OA\Property(property: "sessionId", type: "string", description: "معرف الجلسة من نتيجة البحث (sessionId from search)", example: "sess-abc-123"),
                    new OA\Property(property: "productId", type: "string", description: "معرف المنتج من نتيجة البحث (productId from search)"),
                    new OA\Property(property: "tokenId", type: "string", description: "التوكن من نتيجة البحث (tokenId from search)")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "تم استرجاع أسعار الغرف بنجاح. ابحث عن rateBasisId في النتيجة للمتابعة.")
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
        summary: "التحقق من السعر والشروط (Check/Revalidate rates)",
        operationId: "checkRoomRates",
        description: "التحقق من السعر النهائي وشروط الإلغاء قبل الحجز. (Revalidate rates and rules before booking).",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["rateBasisId", "sessionId", "productId", "tokenId"],
                properties: [
                    new OA\Property(property: "rateBasisId", type: "string", description: "معرف الغرفة من دالة room-rates", example: "RB123"),
                    new OA\Property(property: "sessionId", type: "string", description: "معرف الجلسة", example: "sess-abc-123"),
                    new OA\Property(property: "productId", type: "string", description: "معرف المنتج"),
                    new OA\Property(property: "tokenId", type: "string", description: "التوكن")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "تم التأكد من توفر السعر والشروط بنجاح.")
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
        summary: "إتمام الحجز (Book a hotel)",
        operationId: "bookHotel",
        description: "إنشاء حجز فندق حقيقي. يتطلب rateBasisId من دالة التحقق السابقة. (Create actual reservation. Requires rateBasisId from check-rates).",
        tags: ["Hotels"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["rateBasisId", "sessionId", "productId", "tokenId", "customerEmail", "customerPhone", "paxDetails"],
                properties: [
                    new OA\Property(property: "rateBasisId", type: "string", description: "معرف الغرفة المؤكد من check-rates", example: "RB123"),
                    new OA\Property(property: "sessionId", type: "string", example: "sess-abc-123"),
                    new OA\Property(property: "productId", type: "string"),
                    new OA\Property(property: "tokenId", type: "string"),
                    new OA\Property(property: "customerEmail", type: "string", format: "email", example: "guest@example.com"),
                    new OA\Property(property: "customerPhone", type: "string", example: "966500000000"),
                    new OA\Property(property: "bookingNote", type: "string", description: "ملاحظات إضافية", example: "Quiet room please"),
                    new OA\Property(property: "clientRef", type: "string", description: "مرجع خاص بنظامك (Unique Ref)", example: "MYTRIP-789"),
                    new OA\Property(property: "paxDetails", type: "array", description: "تفاصيل الركاب لكل غرفة", items: new OA\Items(
                        properties: [
                            new OA\Property(property: "room_no", type: "integer", example: 1),
                            new OA\Property(property: "adult", type: "object", properties: [
                                new OA\Property(property: "title", type: "array", items: new OA\Items(type: "string"), example: ["Mr"]),
                                new OA\Property(property: "firstName", type: "array", items: new OA\Items(type: "string"), example: ["John"]),
                                new OA\Property(property: "lastName", type: "array", items: new OA\Items(type: "string"), example: ["Doe"])
                            ]),
                            new OA\Property(property: "child", type: "object", properties: [
                                new OA\Property(property: "title", type: "array", items: new OA\Items(type: "string"), example: ["Mr"]),
                                new OA\Property(property: "firstName", type: "array", items: new OA\Items(type: "string"), example: ["Boy"]),
                                new OA\Property(property: "lastName", type: "array", items: new OA\Items(type: "string"), example: ["Doe"])
                            ])
                        ]
                    ))
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "تم طلب الحجز بنجاح. ابحث عن supplierConfirmationNum و referenceNum في النتيجة.")
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
        summary: "إلغاء الحجز (Cancel Reservation)",
        operationId: "cancelHotelBooking",
        description: "إلغاء حجز فندق موجود باستخدام أرقام المرجع. (Cancel a booking using reference numbers).",
        tags: ["Hotels"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["supplierConfirmationNum", "referenceNum"],
                properties: [
                    new OA\Property(property: "supplierConfirmationNum", type: "string", description: "رقم تأكيد المورد من دالة الحجز", example: "SUP123"),
                    new OA\Property(property: "referenceNum", type: "string", description: "رقم مرجع الحجز من دالة الحجز", example: "212")
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
        description: "يستخدم عندما يكون `moreResults` هو `true` في نتائج البحث أو الفلترة. (Use when `moreResults` is `true` in search/filter results).",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["sessionId", "nextToken"],
                properties: [
                    new OA\Property(property: "sessionId", type: "string", description: "معرف الجلسة من نتيجة البحث"),
                    new OA\Property(property: "nextToken", type: "string", description: "التوكن للصفحة التالية من نتيجة البحث")
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
        description: "تصفية نتائج البحث الحالية بناءً على الاسم، الأسعار، أو التقييم. (Filter search results by name, price, or stars).",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["sessionId"],
                properties: [
                    new OA\Property(property: "sessionId", type: "string", description: "معرف الجلسة الحالي"),
                    new OA\Property(property: "hotelName", type: "string", description: "جزء من اسم الفندق"),
                    new OA\Property(property: "minPrice", type: "number", description: "السعر الأدنى"),
                    new OA\Property(property: "maxPrice", type: "number", description: "السعر الأعلى"),
                    new OA\Property(property: "starRating", type: "string", description: "النجوم مفصولة بفواصل (1,2,3,4,5)", example: "4,5"),
                    new OA\Property(property: "requiredLanguage", type: "string", description: "لغة المحتوى", example: "ARA")
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
        summary: "جلب محتوى الفندق (Hotel static content)",
        operationId: "getHotelContent",
        description: "جلب الصور، الأوصاف، والمرافق الخاصة بالفندق المختار. (Get hotel images, description, and facilities).",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["hotelId", "sessionId", "productId", "tokenId"],
                properties: [
                    new OA\Property(property: "hotelId", type: "string", description: "معرف الفندق"),
                    new OA\Property(property: "sessionId", type: "string", description: "معرف الجلسة"),
                    new OA\Property(property: "productId", type: "string", description: "معرف المنتج المورد"),
                    new OA\Property(property: "tokenId", type: "string", description: "التوكن الخاص بالنتيجة")
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
        summary: "تفاصيل الحجز (Get reservation details)",
        operationId: "getHotelBookingDetails",
        description: "جلب حالة وتفاصيل حجز فندق سابق. (Get status and details for an existing booking).",
        tags: ["Hotels"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["supplierConfirmationNum", "referenceNum"],
                properties: [
                    new OA\Property(property: "supplierConfirmationNum", type: "string", description: "رقم تأكيد المورد من دالة الحجز", example: "SUP123"),
                    new OA\Property(property: "referenceNum", type: "string", description: "رقم مرجع الحجز من دالة الحجز", example: "212")
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
        summary: "قائمة المدن المدعومة (Supported cities list)",
        operationId: "getHotelCities",
        description: "جلب قائمة المدن والبلدان المدعومة. يجب استخدام `city_name` و `country_name` الناتجة من هنا في دالة البحث.",
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
        description: "قائمة لغات المحتوى المدعومة. تستخدم أكواد اللغات الناتجة مثل (ARA, ENG) في دالة البحث والفلترة كـ `requiredLanguage`.",
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
