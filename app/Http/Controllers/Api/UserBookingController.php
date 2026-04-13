<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HotelBooking;
use App\Services\TraveloproService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserBookingController extends Controller
{
    protected $traveloproService;

    public function __construct(TraveloproService $traveloproService)
    {
        $this->traveloproService = $traveloproService;
    }

    #[OA\Get(
        path: "/api/user/bookings",
        summary: "حجوزات المستخدم (User Bookings)",
        operationId: "getUserBookings",
        description: "عرض قائمة حجوزات المستخدم الحالي مع تفاصيل الحالة وأرقام التذاكر. (Get current user's bookings with status and ticket details).",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 10)),
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "تم استرجاع الحجوزات بنجاح",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Bookings retrieved successfully."),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "reference", type: "string", example: "TR123456"),
                                new OA\Property(property: "status", type: "string", example: "confirmed"),
                                new OA\Property(property: "ticket_status", type: "string", example: "ticketed"),
                                new OA\Property(property: "total_amount", type: "number", example: 1500.00),
                                new OA\Property(property: "currency", type: "string", example: "USD"),
                                new OA\Property(property: "pnr_date", type: "string", format: "date-time"),
                                new OA\Property(property: "passengers_count", type: "integer", example: 2),
                                new OA\Property(property: "invoice_url", type: "string", example: "https://mysite.com/invoice/1")
                            ]
                        )),
                        new OA\Property(property: "pagination", type: "object", properties: [
                            new OA\Property(property: "pageNumber", type: "integer"),
                            new OA\Property(property: "pageSize", type: "integer"),
                            new OA\Property(property: "count", type: "integer"),
                            new OA\Property(property: "totalPages", type: "integer"),
                            new OA\Property(property: "hasNextPage", type: "boolean"),
                            new OA\Property(property: "hasPreviousPage", type: "boolean"),
                            new OA\Property(property: "nextPage", type: "string", nullable: true),
                            new OA\Property(property: "previousPage", type: "string", nullable: true)
                        ])
                    ]
                )
            )
        ]
    )]
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $bookings = Booking::where('user_id', $request->user()->id)
            ->latest()
            ->paginate($perPage);

        $data = $bookings->getCollection()->transform(function ($booking) {
            return [
                'id' => $booking->id,
                'reference' => $booking->booking_reference, // UniqueId
                'status' => $booking->status,
                'ticket_status' => $booking->ticket_status,
                'total_amount' => $booking->total_amount,
                'currency' => $booking->currency,
                'pnr_date' => $booking->pnr_created_at,
                'passengers_count' => $booking->passengers->count(),
                'route_summary' => $booking->flightBooking ? ($booking->flightBooking->origin . ' → ' . $booking->flightBooking->destination) : null,
                'invoice_url' => $booking->ticket_status === 'ticketed' ? route('admin.bookings.invoice', $booking->id) : null,
            ];
        });

        return $this->apiResponse(
            false,
            __('Bookings retrieved successfully.'),
            $data,
            $this->formatPagination($bookings)
        );
    }

    #[OA\Get(
        path: "/api/user/bookings/{reference}",
        summary: "تفاصيل حجز المستخدم (User Booking Details)",
        operationId: "getUserBookingDetails",
        description: "عرض تفاصيل حجز معين باستخدام المرجع (Reference/UniqueId) وجلب التفاصيل الحية من Travelopro. (Get specific booking details and live status).",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en")),
            new OA\Parameter(name: "reference", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Success")
        ]
    )]
    public function show(Request $request, $reference)
    {
        $booking = Booking::where('user_id', $request->user()->id)
            ->where(function ($query) use ($reference) {
                $query->where('booking_reference', $reference)
                    ->orWhere('id', $reference);
            })
            ->with(['passengers', 'flightBooking'])
            ->firstOrFail();


        // Fetch live details from Travelopro to sync status (Important for real-time status)
        $liveDetails = null;
        try {
            $liveDetails = $this->traveloproService->getTripDetails($reference, $booking->id);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch live trip details: ' . $e->getMessage());
        }

        $formattedData = [
            'booking_id' => $booking->id,
            'reference' => $booking->booking_reference,
            'status' => $booking->status,
            'ticket_status' => $booking->ticket_status,
            'total_amount' => $booking->total_amount,
            'currency' => $booking->currency,
            'created_at' => $booking->created_at->format('Y-m-d H:i'),
            
            'itinerary' => $booking->flightBooking ? [
                'origin' => $booking->flightBooking->origin,
                'destination' => $booking->flightBooking->destination,
                'departure_date' => $booking->flightBooking->departure_date,
                'return_date' => $booking->flightBooking->return_date,
                'flight_class' => $booking->flightBooking->flight_class,
                'passengers_counts' => [
                    'adults' => $booking->flightBooking->adults,
                    'childs' => $booking->flightBooking->childs,
                    'infants' => $booking->flightBooking->infants,
                ]
            ] : null,

            'passengers' => $booking->passengers->map(function($p) {
                return [
                    'name' => $p->name ?? ($p->first_name . ' ' . $p->last_name),
                    'type' => $p->passenger_type,
                    'passport' => $p->passport_number,
                    'dob' => $p->dob ? $p->dob->format('Y-m-d') : null,
                ];
            }),

            'live_details' => $liveDetails
        ];

        return response()->json([
            'error' => false,
            'message' => __('Booking details retrieved successfully.'),
            'data' => $formattedData
        ]);
    }

    #[OA\Get(
        path: "/api/user/hotel-bookings",
        summary: "حجوزات الفنادق للمستخدم (User Hotel Bookings)",
        operationId: "getUserHotelBookings",
        description: "عرض قائمة حجوزات الفنادق للمستخدم الحالي. (Get current user's hotel bookings).",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 10)),
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Hotel bookings retrieved successfully."),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer"),
                                new OA\Property(property: "hotel_name", type: "string"),
                                new OA\Property(property: "city", type: "string"),
                                new OA\Property(property: "country", type: "string"),
                                new OA\Property(property: "check_in", type: "string", format: "date"),
                                new OA\Property(property: "check_out", type: "string", format: "date"),
                                new OA\Property(property: "status", type: "string"),
                                new OA\Property(property: "total_price", type: "number"),
                                new OA\Property(property: "currency", type: "string"),
                                new OA\Property(property: "reference_num", type: "string"),
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function hotelBookings(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $bookings = HotelBooking::where('user_id', $request->user()->id)
            ->latest()
            ->paginate($perPage);

        $data = $bookings->getCollection()->transform(function ($booking) {
            return [
                'id' => $booking->id,
                'hotel_name' => $booking->hotel_name,
                'city' => $booking->city_name,
                'country' => $booking->country_name,
                'check_in' => $booking->check_in->format('Y-m-d'),
                'check_out' => $booking->check_out->format('Y-m-d'),
                'status' => $booking->status,
                'total_price' => $booking->total_price,
                'currency' => $booking->currency,
                'reference_num' => $booking->reference_num ?? $booking->id,
            ];
        });

        return $this->apiResponse(
            false,
            __('Hotel bookings retrieved successfully.'),
            $data,
            $this->formatPagination($bookings)
        );
    }

    #[OA\Get(
        path: "/api/user/hotel-bookings/{id}",
        summary: "تفاصيل حجز الفندق (User Hotel Booking Details)",
        operationId: "getUserHotelBookingDetails",
        description: "عرض التفاصيل الكاملة لحجز فندق معين. (Get specific hotel booking details).",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en")),
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Success")
        ]
    )]
    public function hotelBookingDetails(Request $request, $id)
    {
        $booking = HotelBooking::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        // Calculate nights
        $nights = $booking->check_in->diffInDays($booking->check_out);

        $data = [
            'hero' => [
                'hotel_name' => $booking->hotel_name,
                'location' => [
                    'city' => $booking->city_name,
                    'country' => $booking->country_name,
                ],
                'status' => $booking->status,
                'reference_num' => $booking->reference_num ?? $booking->id,
                'supplier_confirmation_num' => $booking->supplier_confirmation_num,
            ],
            'stay' => [
                'check_in' => $booking->check_in->format('D, d M Y'),
                'check_out' => $booking->check_out->format('D, d M Y'),
                'nights' => $nights,
            ],
            'accommodation' => [
                'room_name' => $booking->room_name ?? 'N/A',
                'board_type' => $booking->board_type ?? 'N/A',
                'guests' => [
                    'adults' => $booking->adults,
                    'childs' => $booking->childs,
                ],
                'rooms_count' => $booking->rooms,
            ],
            'guests' => $booking->passengers,

            'payment' => [
                'total_price' => $booking->total_price,
                'currency' => $booking->currency,
                'method' => $booking->payment_method ?? 'N/A',
            ],
            'timeline' => [
                'created_at' => $booking->created_at->format('d M Y, H:i'),
                'payment_verified' => in_array($booking->status, ['paid', 'confirmed']),
                'finalized' => $booking->status === 'confirmed',
            ],
            'voucher_url' => $booking->status === 'confirmed' ? route('customer.bookings.hotels.voucher', $booking->id) : null,
        ];

        return $this->apiResponse(false, __('Hotel booking details retrieved successfully.'), $data);
    }
}
