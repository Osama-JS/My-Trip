<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HotelBooking;
use App\Models\TripBooking;
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
        summary: "قائمة حجوزات الطيران (Flight Bookings)",
        operationId: "getUserFlightBookings",
        description: "عرض قائمة حجوزات الطيران للمستخدم الحالي مع إمكانية الفلترة حسب الحالة. (Get user's flight bookings with status filter).",
        tags: ["User Bookings"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en")),
            new OA\Parameter(name: "status", in: "query", description: "Filter by status (pending, confirmed, cancelled)", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "reference", in: "query", description: "Filter by booking reference", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "passenger", in: "query", description: "Filter by passenger name or passport", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "departure_date", in: "query", description: "Filter by departure date (Y-m-d)", required: false, schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "airline_code", in: "query", description: "Filter by airline code", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "flight_number", in: "query", description: "Filter by flight number", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 10)),
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1))
        ],
        responses: [
            new OA\Response(response: 200, description: "Success")
        ]
    )]
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $status = $request->input('status');
        $reference = $request->input('reference');
        $passenger = $request->input('passenger');
        $departureDate = $request->input('departure_date');
        $airlineCode = $request->input('airline_code');
        $flightNumber = $request->input('flight_number');

        $query = Booking::with(['passengers', 'flightBooking'])
            ->where('user_id', $request->user()->id);

        if ($status) {
            $query->where('status', $status);
        }

        if ($reference) {
            $query->where('booking_reference', 'like', "%{$reference}%");
        }

        if ($passenger) {
            $query->whereHas('passengers', function ($q) use ($passenger) {
                $q->where('name', 'like', "%{$passenger}%")
                  ->orWhere('first_name', 'like', "%{$passenger}%")
                  ->orWhere('last_name', 'like', "%{$passenger}%")
                  ->orWhere('passport_number', 'like', "%{$passenger}%");
            });
        }

        if ($departureDate) {
            $query->whereHas('flightBooking', function ($q) use ($departureDate) {
                $q->whereDate('departure_date', $departureDate);
            });
        }

        if ($airlineCode) {
            $query->where('airline_code', $airlineCode);
        }

        if ($flightNumber) {
            $query->whereHas('flightBooking', function ($q) use ($flightNumber) {
                $q->where('flight_number', 'like', "%{$flightNumber}%");
            });
        }

        $bookings = $query->latest()->paginate($perPage);

        $data = $bookings->getCollection()->transform(function ($booking) {
            return [
                'id' => $booking->id,
                'reference' => $booking->booking_reference,
                'status' => $booking->status,
                'ticket_status' => $booking->ticket_status,
                'total_amount' => $booking->total_amount,
                'currency' => $booking->currency,
                'booking_date' => $booking->created_at->format('Y-m-d'),
                'passengers_count' => $booking->passengers->count(),
                'summary' => $booking->flightBooking ? ($booking->flightBooking->origin . ' → ' . $booking->flightBooking->destination) : __('Flight Booking'),
                'service_type' => 'flight',
                'invoice_url' => $booking->status === 'confirmed' ? route('customer.bookings.invoice', $booking->id) : null,
            ];
        });

        return $this->apiResponse(
            false,
            __('Flight bookings retrieved successfully.'),
            $data,
            $this->formatPagination($bookings)
        );
    }

    /**
     * Get Trip Bookings (Packages)
     */
    #[OA\Get(
        path: "/api/user/trip-bookings",
        summary: "حجوزات البرامج السياحية (Trip/Package Bookings)",
        operationId: "getUserTripBookings",
        description: "عرض قائمة حجوزات البرامج السياحية للمستخدم الحالي. (Get user's trip package bookings).",
        tags: ["User Bookings"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en")),
            new OA\Parameter(name: "status", in: "query", description: "Filter by status (pending, confirmed, cancelled)", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 10)),
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1))
        ],
        responses: [
            new OA\Response(response: 200, description: "Success")
        ]
    )]
    public function tripBookings(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $status = $request->input('status');

        $query = TripBooking::with(['trip.toCountry', 'trip.toCity'])
            ->where('user_id', $request->user()->id);

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->latest()->paginate($perPage);

        $data = $bookings->getCollection()->transform(function ($booking) {
            $trip = $booking->trip;
            return [
                'id' => $booking->id,
                'reference' => 'TR-' . $booking->id,
                'status' => $booking->status,
                'booking_state' => $booking->booking_state,
                'total_amount' => $booking->total_price,
                'currency' => 'SAR', // Trip bookings default to SAR
                'booking_date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : $booking->created_at->format('Y-m-d'),
                'passengers_count' => $booking->tickets_count,
                'summary' => $trip ? (app()->getLocale() == 'ar' ? $trip->title_ar : $trip->title_en) : __('Trip Booking'),
                'image' => $trip ? $trip->image_url : null,
                'service_type' => 'trip',
                'invoice_url' => $booking->status === 'confirmed' ? route('customer.bookings.invoice', $booking->id) : null,
            ];
        });

        return $this->apiResponse(
            false,
            __('Trip bookings retrieved successfully.'),
            $data,
            $this->formatPagination($bookings)
        );
    }

    #[OA\Get(
        path: "/api/user/bookings/{id}",
        summary: "تفاصيل حجز الطيران (Flight Booking Details)",
        operationId: "getUserFlightBookingDetails",
        description: "عرض تفاصيل حجز طيران معين. (Get specific flight booking details).",
        tags: ["User Bookings"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en")),
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Success")
        ]
    )]
    public function show(Request $request, $id)
    {
        $booking = Booking::with(['passengers', 'flightBooking', 'payments'])
            ->where('user_id', $request->user()->id)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)->orWhere('booking_reference', $id);
            })
            ->firstOrFail();

        $adultsCount = $booking->flightBooking->adults ?? $booking->passengers()->where('passenger_type', 'adult')->count();
        $childsCount = $booking->flightBooking->childs ?? $booking->passengers()->where('passenger_type', 'child')->count();
        $infantsCount = $booking->flightBooking->infants ?? $booking->passengers()->where('passenger_type', 'infant')->count();

        $formattedData = [
            'id' => $booking->id,
            'reference' => $booking->booking_reference,
            'status' => $booking->status,
            'ticket_status' => $booking->ticket_status,
            'total_amount' => $booking->total_amount,
            'currency' => $booking->currency,
            'booking_date' => $booking->created_at->format('Y-m-d H:i'),
            'service_type' => 'flight',

            'flight_details' => [
                'origin' => $booking->flightBooking->origin ?? 'N/A',
                'destination' => $booking->flightBooking->destination ?? 'N/A',
                'departure_date' => $booking->flightBooking->departure_date ?? 'N/A',
                'return_date' => $booking->flightBooking->return_date ?? null,
                'flight_class' => $booking->flightBooking->flight_class ?? 'Economy',
                'airline' => $booking->flightBooking->airline_name ?? 'Unknown',
                'passengers_counts' => [
                    'adults' => $adultsCount > 0 ? $adultsCount : 1,
                    'childs' => $childsCount > 0 ? $childsCount : 0,
                    'infants' => $infantsCount > 0 ? $infantsCount : 0,
                ]
            ],

            'passengers' => $booking->passengers->map(function ($p) {
                return [
                    'name' => $p->name ?? ($p->first_name . ' ' . $p->last_name),
                    'type' => $p->passenger_type,
                    'passport' => $p->passport_number,
                    'nationality' => $p->nationality,
                ];
            }),
            
            'invoice_url' => $booking->status === 'confirmed' ? route('customer.bookings.invoice', $booking->id) : null,
        ];

        return $this->apiResponse(false, __('Flight booking details retrieved successfully.'), $formattedData);
    }

    /**
     * Get Trip Booking Details
     */
    #[OA\Get(
        path: "/api/user/trip-bookings/{id}",
        summary: "تفاصيل حجز البرنامج السياحي (Trip Booking Details)",
        operationId: "getUserTripBookingDetails",
        description: "عرض تفاصيل حجز برنامج سياحي معين. (Get specific trip package booking details).",
        tags: ["User Bookings"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en")),
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Success")
        ]
    )]
    public function tripBookingDetails(Request $request, $id)
    {
        $booking = TripBooking::with(['trip.images', 'trip.toCountry', 'trip.toCity', 'passengers', 'payments'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        $trip = $booking->trip;

        $formattedData = [
            'id' => $booking->id,
            'reference' => 'TR-' . $booking->id,
            'status' => $booking->status,
            'booking_state' => $booking->booking_state,
            'total_amount' => $booking->total_price,
            'currency' => 'SAR',
            'booking_date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : $booking->created_at->format('Y-m-d'),
            'service_type' => 'trip',

            'trip_details' => $trip ? [
                'id' => $trip->id,
                'title' => app()->getLocale() == 'ar' ? $trip->title_ar : $trip->title_en,
                'duration' => $trip->duration,
                'image' => $trip->image_url,
                'location' => [
                    'country' => $trip->toCountry ? $trip->toCountry->name : null,
                    'city' => $trip->toCity ? $trip->toCity->name : null,
                ]
            ] : null,

            'passengers' => $booking->passengers->map(function ($p) {
                return [
                    'name' => $p->name,
                    'phone' => $p->phone,
                    'nationality' => $p->nationality,
                    'passport' => $p->passport_number,
                ];
            }),

            'invoice_url' => $booking->status === 'confirmed' ? route('customer.bookings.invoice', $booking->id) : null,
            'ticket_url' => $booking->ticket_url,
        ];

        return $this->apiResponse(false, __('Trip booking details retrieved successfully.'), $formattedData);
    }

    #[OA\Get(
        path: "/api/user/hotel-bookings",
        summary: "حجوزات الفنادق للمستخدم (User Hotel Bookings)",
        operationId: "getUserHotelBookings",
        description: "عرض قائمة حجوزات الفنادق للمستخدم الحالي مع إمكانية الفلترة. (Get user's hotel bookings).",
        tags: ["User Bookings"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en")),
            new OA\Parameter(name: "status", in: "query", description: "Filter by status (pending, confirmed, cancelled)", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "reference", in: "query", description: "Filter by booking reference or ID", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "guest", in: "query", description: "Filter by guest name", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "hotel", in: "query", description: "Filter by hotel name", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "city", in: "query", description: "Filter by city name", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 10)),
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1))
        ],
        responses: [
            new OA\Response(response: 200, description: "Success")
        ]
    )]
    public function hotelBookings(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $status = $request->input('status');
        $reference = $request->input('reference');
        $guest = $request->input('guest');
        $hotel = $request->input('hotel');
        $city = $request->input('city');

        $query = HotelBooking::with(['passengers'])
            ->where('user_id', $request->user()->id);

        if ($status) {
            $query->where('status', $status);
        }

        if ($reference) {
            $query->where(function ($q) use ($reference) {
                $q->where('id', $reference)
                  ->orWhere('reference_num', 'like', "%{$reference}%");
            });
        }

        if ($guest) {
            $query->whereHas('passengers', function ($q) use ($guest) {
                $q->where('name', 'like', "%{$guest}%")
                  ->orWhere('first_name', 'like', "%{$guest}%")
                  ->orWhere('last_name', 'like', "%{$guest}%");
            });
        }

        if ($hotel) {
            $query->where('hotel_name', 'like', "%{$hotel}%");
        }

        if ($city) {
            $query->where('city_name', 'like', "%{$city}%");
        }

        $bookings = $query->latest()->paginate($perPage);

        $data = $bookings->getCollection()->transform(function ($booking) {
            return [
                'id' => $booking->id,
                'reference' => $booking->reference_num ?? $booking->id,
                'status' => $booking->status,
                'total_amount' => $booking->total_price,
                'currency' => $booking->currency,
                'booking_date' => $booking->created_at->format('Y-m-d'),
                'hotel_name' => $booking->hotel_name,
                'location' => $booking->city_name . ', ' . $booking->country_name,
                'summary' => $booking->hotel_name,
                'service_type' => 'hotel',
                'invoice_url' => $booking->status === 'confirmed' ? route('customer.bookings.hotels.voucher', $booking->id) : null,
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
        tags: ["User Bookings"],
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
        ];

        return $this->apiResponse(false, __('Hotel booking details retrieved successfully.'), $data);
    }

    #[OA\Get(
        path: "/api/user/hotel-bookings/{id}/voucher",
        summary: "تحميل قسيمة حجز الفندق (Download Hotel Voucher)",
        operationId: "downloadHotelVoucher",
        description: "يقوم بتوليد وإرجاع ملف PDF يحتوي على قسيمة الحجز الفندقي. (Returns a PDF voucher for a confirmed hotel booking).",
        tags: ["User Bookings"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Returns PDF file"),
            new OA\Response(response: 404, description: "Not Found or Not Confirmed")
        ]
    )]
    public function downloadHotelVoucher(Request $request, $id, \App\Services\InvoiceService $invoiceService)
    {
        $booking = HotelBooking::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($booking->status !== 'confirmed') {
            return $this->apiResponse(true, __('Voucher is only available for confirmed bookings.'), null, null, 403);
        }

        $filePath = $invoiceService->generateHotelVoucher($booking);

        if (!$filePath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            return $this->apiResponse(true, __('Failed to generate voucher.'), null, null, 500);
        }

        $fileUrl = asset('storage/' . $filePath);
        return $this->apiResponse(false, __('Voucher retrieved successfully'), ['voucher_url' => $fileUrl]);
    }

    #[OA\Get(
        path: "/api/user/bookings/{id}/invoice",
        summary: "تحميل فاتورة حجز الطيران",
        operationId: "downloadFlightInvoice",
        description: "يقوم بتوليد وإرجاع رابط لفاتورة الحجز.",
        tags: ["User Bookings"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Returns URL")
        ]
    )]
    public function downloadFlightInvoice(Request $request, $id, \App\Services\InvoiceService $invoiceService)
    {
        $booking = Booking::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        if (!in_array($booking->status, ['confirmed', 'ticketed', 'completed'])) {
            return $this->apiResponse(true, __('Invoice is only available for confirmed bookings.'), null, null, 403);
        }

        $filePath = $invoiceService->generateInvoice($booking);

        if (!$filePath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            return $this->apiResponse(true, __('Failed to generate invoice.'), null, null, 500);
        }

        $fileUrl = asset('storage/' . $filePath);
        return $this->apiResponse(false, __('Invoice retrieved successfully'), ['invoice_url' => $fileUrl]);
    }
}
