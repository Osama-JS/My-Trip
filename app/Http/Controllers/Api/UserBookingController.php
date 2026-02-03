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
            ->where('booking_reference', $reference)
            ->with(['passengers'])
            ->firstOrFail();

        // Fetch live details from Travelopro to sync status (Important for real-time status)
        try {
            $liveDetails = $this->traveloproService->getTripDetails($reference, $booking->id);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch live trip details: ' . $e->getMessage());
            $liveDetails = null;
        }

        return response()->json([
            'error' => false,
            'message' => __('Booking details retrieved successfully.'),
            'data' => [
                'local_details' => $booking,
                'live_details' => $liveDetails
            ]
        ]);
    }
}
