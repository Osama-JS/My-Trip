<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
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
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en"))
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
                                new OA\Property(property: "created_at", type: "string", format: "date-time")
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function index(Request $request)
    {
        $bookings = Booking::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'error' => false,
            'message' => __('Bookings retrieved successfully.'),
            'data' => $bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'reference' => $booking->booking_reference, // UniqueId
                    'status' => $booking->status,
                    'ticket_status' => $booking->ticket_status,
                    'total_amount' => $booking->total_amount,
                    'currency' => $booking->currency,
                    'pnr_date' => $booking->pnr_created_at,
                    'passengers_count' => $booking->passengers->count(),
                    'invoice_url' => $booking->ticket_status === 'ticketed' ? route('admin.bookings.invoice', $booking->id) : null,
                ];
            })
        ]);
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
            ->where('booking_reference', $reference)
            ->with('passengers')
            ->firstOrFail();

        // Optional: Fetch live details from Travelopro if needed to sync status
        // $liveDetails = $this->traveloproService->getTripDetails($reference);

        return response()->json([
            'error' => false,
            'message' => __('Booking details retrieved successfully.'),
            'data' => [
                'local_details' => $booking,
                // 'live_details' => $liveDetails // Uncomment if live fetch is desired
            ]
        ]);
    }
}
