<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TraveloproHotelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;
use App\Models\HotelBooking;
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
        summary: "الخطوة 1: البحث عن الفنادق (Search)",
        operationId: "searchHotels",
        description: "هذه هي الخطوة الأولى في عملية الحجز. \n\n**ماذا ترسل:** تاريخ الدخول، تاريخ الخروج، المدينة، وتوزيع النزلاء (auto أو manual).\n**ماذا تستقبل:** قائمة بالفنادق المتاحة. \n\n**هام جداً:** يجب استخراج وحفظ الـ `sessionId` والـ `tokenId` من نتيجة هذه الدالة، حيث ستحتاج لإرسالهما في كل الخطوات التالية.",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["checkIn", "checkOut", "rooms"],
                properties: [
                    new OA\Property(property: "cityName", type: "string", description: "اسم المدينة بالإنجليزية", example: "Dubai"),
                    new OA\Property(property: "countryName", type: "string", description: "اسم الدولة بالإنجليزية", example: "United Arab Emirates"),
                    new OA\Property(property: "checkIn", type: "string", format: "date", description: "تاريخ الدخول (YYYY-MM-DD)", example: "2025-12-01"),
                    new OA\Property(property: "checkOut", type: "string", format: "date", description: "تاريخ الخروج (YYYY-MM-DD)", example: "2025-12-10"),
                    new OA\Property(property: "rooms", type: "integer", description: "إجمالي عدد الغرف", example: 1),
                    new OA\Property(property: "adults", type: "integer", description: "إجمالي البالغين (لنمط auto)", example: 2),
                    new OA\Property(property: "childs", type: "integer", description: "إجمالي الأطفال (لنمط auto)", example: 0),
                    new OA\Property(property: "childAge", type: "array", items: new OA\Items(type: "integer"), description: "أعمار الأطفال (لنمط auto)", example: []),
                    new OA\Property(property: "distribution_mode", type: "string", description: "auto أو manual", example: "auto"),
                    new OA\Property(property: "occupancy", type: "array", description: "توزيع الغرف (لنمط manual فقط)", items: new OA\Items(
                        properties: [
                            new OA\Property(property: "adult", type: "integer", description: "بالغين هذه الغرفة", example: 2),
                            new OA\Property(property: "child", type: "integer", description: "أطفال هذه الغرفة", example: 1),
                            new OA\Property(property: "child_age", type: "array", items: new OA\Items(type: "integer"), description: "أعمار أطفال هذه الغرفة", example: [5])
                        ]
                    ))
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "ناجح: استخرج الـ sessionId والـ tokenId من هنا لاستخدامهما في الخطوة 2."
            )
        ]
    )]
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cityName' => 'required_without:latitude|string',
            'countryName' => 'required_without:latitude|string',
            'cityCode' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius' => 'nullable|integer',
            'checkIn' => 'required|date_format:Y-m-d|after_or_equal:today',
            'checkOut' => 'required|date_format:Y-m-d|after:checkIn',
            'rooms' => 'required|integer|min:1',
            'adults' => 'required_if:distribution_mode,auto|integer|min:1',
            'childs' => 'nullable|integer|min:0',
            'childAge' => 'nullable|array',
            'distribution_mode' => 'nullable|string|in:auto,manual',
            'occupancy' => 'required_if:distribution_mode,manual|array',
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
        summary: "الخطوة 2: جلب أسعار الغرف (Room Rates)",
        operationId: "getRoomRates",
        description: "بعدما يختار المستخدم فندقاً معيناً من نتائج البحث، استخدم هذه الدالة لجلب أنواع الغرف المتاحة بداخل هذا الفندق وأسعارها.\n\n**ماذا ترسل:** الـ `hotelId` من الفندق المختار بالإضافة للـ `sessionId` والـ `tokenId` من الخطوة 1.\n**ماذا تستقبل:** قائمة بجميع أنواع الغرف (مثلاً: Standard, Deluxe, etc) مع أسعارها.\n\n**هام جداً:** لكل غرفة في النتيجة يوجد حقل يسمى `rateBasisId` وحقل يسمى `productId`؛ يجب حفظهما لاستخدامهما في الخطوات التالية.",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["hotelId", "sessionId", "productId", "tokenId"],
                properties: [
                    new OA\Property(property: "hotelId", type: "string", description: "معرف الفندق المختار", example: "H12345"),
                    new OA\Property(property: "sessionId", type: "string", description: "من خطوة البحث", example: "sess-abc-123"),
                    new OA\Property(property: "productId", type: "string", description: "من خطوة البحث"),
                    new OA\Property(property: "tokenId", type: "string", description: "من خطوة البحث")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "ناجح: اختر غرفة واستخلص منها الـ rateBasisId والـ productId للخطوة 3."
            )
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
        summary: "الخطوة 3: تأكيد السعر والشروط (Revalidate)",
        operationId: "checkRoomRates",
        description: "قبل أن تطلب من المستخدم تعبئة بياناته، يجب استدعاء هذه الدالة للتأكد من أن السعر لا يزال متاحاً ولم يتغير (Revalidate).\n\n**ماذا ترسل:** الـ `rateBasisId` للغرفة المختارة + الـ IDs الأصلية.\n**ماذا تستقبل:** السعر النهائي المؤكد وشروط الإلغاء (Cancellation Policy).\n\n**هام:** لا تنتقل لخطوة الحجز إلا إذا أعادت هذه الدالة حالة نجاح واستعرض للمستخدم شروط الإلغاء.",
        tags: ["Hotels"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["rateBasisId", "sessionId", "productId", "tokenId"],
                properties: [
                    new OA\Property(property: "rateBasisId", type: "string", description: "معرف الغرفة من الخطوة 2", example: "RB123"),
                    new OA\Property(property: "sessionId", type: "string", description: "معرف الجلسة", example: "sess-abc-123"),
                    new OA\Property(property: "productId", type: "string", description: "معرف المنتج"),
                    new OA\Property(property: "tokenId", type: "string", description: "التوكن")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "ناجح: السعر مؤكد، والآن يمكنك عرض نموذج تعبئة بيانات النزلاء للمستخدم."
            )
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
        summary: "الخطوة الأخيرة: إتمام الحجز (Book)",
        operationId: "bookHotel",
        description: "هذه الخطوة النهائية لإنشاء الحجز في النظام.\n\n**ماذا ترسل:** \n1. كافة الـ IDs المجمعة سابقاً.\n2. إجمالي السعر المؤكد والعملة.\n3. بيانات النزلاء (paxDetails) بهيكلية دقيقة.\n4. البريد والهاتف للمشتري.\n\n**ماذا تستقبل:** \n1. `booking_id`: رقم الحجز في قاعدة بياناتنا.\n2. `payment_info`: كائن يحتوي على روابط الدفع لكل وسيلة (Visa, Mada, Apple Pay, Tamara, Tabby).\n\n**كيف يتم التمييز أن الدفع للفندق؟**\n- الرابط يحتوي على بارامتر `type=hotel`. هذا يخبر نظام الدفع بالبحث داخل جدول حجوزات الفنادق وليس الرحلات أو الطيران.\n\n**كيفية التعامل مع الدفع (للمبرمج):**\n- اختر رابط الوسيلة التي حددها المستخدم من كائن `payment_info` وافتحه في **WebView**.\n- عند انتهاء الدفع بنجاح، سيقوم النظام بالخلفية بتأكيد الحجز مع المورد وإصدار الـ Voucher.\n- لمتابعة حالة الحجز، يمكنك استدعاء دالة 'عرض تفاصيل الحجز' وتفقد حقل الـ `status`.",
        tags: ["Hotels"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["rateBasisId", "sessionId", "productId", "tokenId", "customerEmail", "customerPhone", "clientRef", "bookingNote", "total_price", "currency", "paxDetails", "checkIn", "checkOut", "hotelId", "hotelName"],
                properties: [
                    new OA\Property(property: "rateBasisId", type: "string", description: "من خطوة revalidate", example: "MTU3"),
                    new OA\Property(property: "sessionId", type: "string", description: "معرف الجلسة", example: "sess-abc-123"),
                    new OA\Property(property: "productId", type: "string", description: "معرف المورد"),
                    new OA\Property(property: "tokenId", type: "string", description: "التوكن"),
                    new OA\Property(property: "clientRef", type: "string", description: "رقم مرجع من عندك", example: "MYTRIP-001"),
                    new OA\Property(property: "customerEmail", type: "string", format: "email", example: "guest@example.com"),
                    new OA\Property(property: "customerPhone", type: "string", example: "966500000000"),
                    new OA\Property(property: "bookingNote", type: "string", description: "ملاحظة", example: "Hotel Booking"),
                    new OA\Property(property: "hotelId", type: "string", description: "معرف الفندق"),
                    new OA\Property(property: "hotelName", type: "string", description: "اسم الفندق"),
                    new OA\Property(property: "checkIn", type: "string", format: "date", description: "تاريخ الدخول", example: "2025-12-01"),
                    new OA\Property(property: "checkOut", type: "string", format: "date", description: "تاريخ الخروج", example: "2025-12-10"),
                    new OA\Property(property: "rooms", type: "integer", description: "عدد الغرف", example: 1),
                    new OA\Property(property: "total_price", type: "number", description: "السعر المؤكد"),
                    new OA\Property(property: "currency", type: "string", description: "العملة", example: "SAR"),
                    new OA\Property(
                        property: "paxDetails",
                        type: "array",
                        description: "بيانات النزلاء لكل غرفة. مثال: [{room_no:1, adult:{title:[Mr], firstName:[John], lastName:[Doe]}}]",
                        items: new OA\Items(
                            required: ["room_no", "adult"],
                            properties: [
                                new OA\Property(property: "room_no", type: "integer", example: 1),
                                new OA\Property(
                                    property: "adult",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "title", type: "array", items: new OA\Items(type: "string"), example: ["Mr"]),
                                        new OA\Property(property: "firstName", type: "array", items: new OA\Items(type: "string"), example: ["John"]),
                                        new OA\Property(property: "lastName", type: "array", items: new OA\Items(type: "string"), example: ["Doe"])
                                    ]
                                )
                            ]
                        )
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "ناجح: استخدم الروابط في payment_info لفتح بوابة الدفع المناسبة."
            )
        ]
    )]
    public function book(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rateBasisId' => 'required|string',
            'sessionId' => 'required|string',
            'productId' => 'required|string',
            'tokenId' => 'required|string',
            'hotelId' => 'required|string',
            'hotelName' => 'required|string',
            'checkIn' => 'required|date_format:Y-m-d',
            'checkOut' => 'required|date_format:Y-m-d',
            'total_price' => 'required|numeric',
            'currency' => 'required|string|size:3',
            'customerEmail' => 'required|email',
            'customerPhone' => 'required|string',
            'paxDetails' => 'required|array|min:1',
            'bookingNote' => 'nullable|string',
            'clientRef' => 'nullable|string',
        ], [
            'hotelId.required' => __('Hotel ID is required.'),
            'paxDetails.required' => __('Passenger details are required for all rooms.'),
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $referenceNum = 'HTL-' . strtoupper(uniqid());

        // Skip early supplier booking to avoid liability for failed payments.
        // The actual booking with Travelopro will happen in the post-payment finalization.
        try {
            $hotelBooking = HotelBooking::create([
                'user_id' => Auth::id(),
                'hotel_name' => $request->hotelName ?? 'Hotel Reservation',
                'hotel_id' => $request->hotelId,
                'city_name' => $request->cityName,
                'country_name' => $request->countryName,
                'check_in' => $request->checkIn ?? now(),
                'check_out' => $request->checkOut ?? now(),
                'rooms' => (int) $request->rooms,
                'adults' => (int) $request->adults,
                'childs' => (int) $request->childs,
                'total_price' => $request->total_price ?? 0,
                'currency' => $request->currency ?? 'SAR',
                'status' => 'pending',
                'reference_num' => $referenceNum,
                'supplier_confirmation_num' => null,
                'session_id' => $request->sessionId,
                'product_id' => $request->productId,
                'token_id' => $request->tokenId,
                'rate_basis_id' => $request->rateBasisId,
                'room_name' => $request->roomName ? str_replace('|t|', ' & ', $request->roomName) : null,
                'board_type' => $request->boardType,
                'pax_details' => $request->paxDetails,
            ]);

            $result = [
                'status' => 'success',
                'message' => 'Hotel booking initiated locally.',
                'reference_num' => $referenceNum,
                'total_price' => $hotelBooking->total_price,
                'currency' => $hotelBooking->currency,
            ];

            // Save detailed guests to booking_passengers table for unified access
            foreach ($request->paxDetails as $room) {
                $paxList = [];

                // Handle official Travelopro format: adult/child as objects with arrays
                if (!empty($room['adult']) && isset($room['adult']['firstName'])) {
                    $adults = $room['adult'];
                    foreach (($adults['firstName'] ?? []) as $index => $firstName) {
                        $paxList[] = [
                            'type' => 'AD',
                            'Title' => $adults['title'][$index] ?? 'Mr',
                            'FirstName' => $firstName,
                            'LastName' => $adults['lastName'][$index] ?? '',
                        ];
                    }
                }
                if (!empty($room['child']) && isset($room['child']['firstName'])) {
                    $children = $room['child'];
                    foreach (($children['firstName'] ?? []) as $index => $firstName) {
                        $paxList[] = [
                            'type' => 'CH',
                            'Title' => $children['title'][$index] ?? 'Master',
                            'FirstName' => $firstName,
                            'LastName' => $children['lastName'][$index] ?? '',
                        ];
                    }
                }

                // Also support internal flat pax[] format (from web frontend or old apps)
                if (empty($paxList) && !empty($room['pax']) && is_array($room['pax'])) {
                    foreach ($room['pax'] as $pax) {
                        $paxList[] = [
                            'type' => $pax['type'] ?? 'AD',
                            'Title' => $pax['Title'] ?? $pax['title'] ?? 'Mr',
                            'FirstName' => $pax['FirstName'] ?? $pax['firstName'] ?? '',
                            'LastName' => $pax['LastName'] ?? $pax['lastName'] ?? '',
                        ];
                    }
                }

                foreach ($paxList as $pax) {
                    $type = (isset($pax['type']) && strtoupper($pax['type']) === 'CH') ? 'child' : 'adult';
                    $title = $pax['Title'] ?? 'Mr';
                    $fName = $pax['FirstName'] ?? '';
                    $lName = $pax['LastName'] ?? '';

                    if (!empty($fName) && !empty($lName)) {
                        $hotelBooking->passengers()->create([
                            'name' => "{$title} {$fName} {$lName}",
                            'first_name' => $fName,
                            'last_name' => $lName,
                            'title' => $title,
                            'passenger_type' => $type,
                        ]);
                    }
                }
            }


            $result['payment_info'] = [
                'default_url' => route('payments.web.checkout', ['booking_id' => $hotelBooking->id, 'method' => 'visa_master', 'type' => 'hotel']),
                'methods' => [
                    'visa_master' => [
                        'name' => 'Visa / Mastercard',
                        'url' => route('payments.web.checkout', ['booking_id' => $hotelBooking->id, 'method' => 'visa_master', 'type' => 'hotel']),
                        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/800px-Mastercard-logo.svg.png'
                    ],
                    'mada' => [
                        'name' => 'Mada',
                        'url' => route('payments.web.checkout', ['booking_id' => $hotelBooking->id, 'method' => 'mada', 'type' => 'hotel']),
                        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/f/fb/Mada_Logo.svg'
                    ],
                    'apple_pay' => [
                        'name' => 'Apple Pay',
                        'url' => route('payments.web.checkout', ['booking_id' => $hotelBooking->id, 'method' => 'apple_pay', 'type' => 'hotel']),
                        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b0/Apple_Pay_logo.svg/1024px-Apple_Pay_logo.svg.png'
                    ],
                    'tamara' => [
                        'name' => 'Tamara',
                        'url' => route('payments.web.checkout', ['booking_id' => $hotelBooking->id, 'method' => 'tamara', 'type' => 'hotel']),
                        'logo' => 'https://cdn.tamara.co/assets/svg/tamara-logo-badge-en.svg'
                    ],
                    'tabby' => [
                        'name' => 'Tabby',
                        'url' => route('payments.web.checkout', ['booking_id' => $hotelBooking->id, 'method' => 'tabby', 'type' => 'hotel']),
                        'logo' => 'https://www.pfgrowth.com/wp-content/uploads/2023/03/tabby-logo-1.png'
                    ],
                    'bank_transfer' => [
                        'name' => 'Bank Transfer',
                        'url' => route('payments.web.checkout', ['booking_id' => $hotelBooking->id, 'method' => 'bank_transfer', 'type' => 'hotel']),
                        'logo' => 'https://cdn-icons-png.flaticon.com/512/2830/2830284.png'
                    ],
                ]
            ];
            $result['booking_id'] = $hotelBooking->id;

        } catch (\Exception $e) {
            Log::error('Failed to persist hotel booking: ' . $e->getMessage());
        }

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
        description: "جلب قائمة المدن والبلدان المدعومة من قاعدة البيانات المحلية. يجب استخدام `city_name` و `country_name` الناتجة في دالة البحث.",
        tags: ["Hotels"],
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
                description: "Search keyword for city or country name",
                required: false,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "تم استرجاع قائمة المدن بنجاح",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Cities retrieved successfully."),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "city_name", type: "string", example: "Dubai"),
                                new OA\Property(property: "country_name", type: "string", example: "United Arab Emirates"),
                                new OA\Property(property: "display_name", type: "string", example: "دبي, الإمارات العربية المتحدة")
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function getCities(Request $request)
    {
        $q = $request->get('q', '');
        $locale = app()->getLocale();

        $cities = \App\Models\HotelCity::where('is_active', true)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('city_name_en', 'like', "%{$q}%")
                        ->orWhere('city_name_ar', 'like', "%{$q}%")
                        ->orWhere('country_name_en', 'like', "%{$q}%")
                        ->orWhere('country_name_ar', 'like', "%{$q}%")
                        ->orWhere('city_code', 'like', "%{$q}%");
                });
            })
            ->orderBy($locale === 'ar' ? 'city_name_ar' : 'city_name_en', 'asc')
            ->get();

        $formatted = $cities->map(function ($city) use ($locale) {
            $isAr = ($locale === 'ar');

            return [
                'id' => $city->id,
                'city_code' => $city->city_code,
                'city_name_en' => $city->city_name_en,
                'city_name_ar' => $city->city_name_ar,
                'country_name_en' => $city->country_name_en,
                'country_name_ar' => $city->country_name_ar,
                'country_code' => $city->country_code,
                'latitude' => $city->latitude,
                'longitude' => $city->longitude,
                // Localized fields for convenience
                'city_name' => $isAr ? ($city->city_name_ar ?: $city->city_name_en) : $city->city_name_en,
                'country_name' => $isAr ? ($city->country_name_ar ?: $city->country_name_en) : $city->country_name_en,
                'display_name' => $isAr
                    ? ($city->city_name_ar ? "{$city->city_name_ar}, {$city->country_name_ar}" : "{$city->city_name_en}, {$city->country_name_en}")
                    : "{$city->city_name_en}, {$city->country_name_en}",
                'name' => $isAr ? ($city->city_name_ar ?: $city->city_name_en) : $city->city_name_en,
            ];
        });

        return $this->apiResponse(false, __('Cities retrieved successfully.'), $formatted, null, 200);
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

    public function getCancelCharge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplierConfirmationNum' => 'required|string',
            'referenceNum' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->hotelService->getCancelCharge($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? null, null, 500);
        }

        return $this->apiResponse(false, __('Cancellation charge retrieved successfully.'), $result, null, 200);
    }
}
