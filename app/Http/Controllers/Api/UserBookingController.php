<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HotelBooking;
use App\Models\TripBooking;
use App\Models\Notification;
use App\Services\TraveloproService;
use App\Services\WalletService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

            'insurance' => [
                'has_insurance' => ($booking->insurance_amount ?? 0) > 0 || !empty($booking->insurance_policy_id) || (bool)$booking->insurancePolicy,
                'amount' => (float)($booking->insurance_amount ?? 0),
                'policy_number' => $booking->insurancePolicy?->policy_number,
                'status' => $booking->insurancePolicy?->status ?? 'active',
                'coverage_amount' => $booking->insurancePolicy?->coverage_amount ?? 500000,
                'certificate_url' => $booking->insurancePolicy ? route('customer.insurances.certificate', $booking->insurancePolicy->id) : null,
            ],
            
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

            'insurance' => [
                'has_insurance' => ($booking->insurance_amount ?? 0) > 0 || !empty($booking->insurance_policy_id) || (bool)$booking->insurancePolicy,
                'amount' => (float)($booking->insurance_amount ?? 0),
                'policy_number' => $booking->insurancePolicy?->policy_number,
                'status' => $booking->insurancePolicy?->status ?? 'active',
                'coverage_amount' => $booking->insurancePolicy?->coverage_amount ?? 500000,
                'certificate_url' => $booking->insurancePolicy ? route('customer.insurances.certificate', $booking->insurancePolicy->id) : null,
            ],

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
        $booking = HotelBooking::with(['passengers', 'payments'])
            ->where('user_id', $request->user()->id)
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
            'insurance' => [
                'has_insurance' => ($booking->insurance_amount ?? 0) > 0 || !empty($booking->insurance_policy_id) || (bool)$booking->insurancePolicy,
                'amount' => (float)($booking->insurance_amount ?? 0),
                'policy_number' => $booking->insurancePolicy?->policy_number,
                'status' => $booking->insurancePolicy?->status ?? 'active',
                'coverage_amount' => $booking->insurancePolicy?->coverage_amount ?? 500000,
                'certificate_url' => $booking->insurancePolicy ? route('customer.insurances.certificate', $booking->insurancePolicy->id) : null,
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

    #[OA\Get(
        path: "/api/user/bookings/{id}/insurance-certificate",
        summary: "تحميل وثيقة/شهادة التأمين لحجز الطيران",
        operationId: "downloadFlightInsuranceCertificate",
        description: "يقوم بتوليد وإرجاع رابط لشهادة التأمين الرسمية.",
        tags: ["User Bookings"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Returns URL")
        ]
    )]
    public function downloadFlightInsuranceCertificate(Request $request, $id, \App\Services\InvoiceService $invoiceService)
    {
        $booking = Booking::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $policy = $booking->insurancePolicy ?? \App\Models\InsurancePolicy::where('booking_id', $booking->id)->first();
        if (!$policy) {
            return $this->apiResponse(true, __('No insurance policy found for this booking.'), null, null, 404);
        }

        $filePath = $policy->pdf_path;
        if (!$filePath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            $filePath = $invoiceService->generateInsurancePolicyPdf($policy);
            if ($filePath) {
                $policy->update(['pdf_path' => $filePath]);
            }
        }

        if (!$filePath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            return $this->apiResponse(true, __('Failed to generate insurance certificate.'), null, null, 500);
        }

        $fileUrl = asset('storage/' . $filePath);
        return $this->apiResponse(false, __('Insurance certificate retrieved successfully'), [
            'certificate_url' => $fileUrl,
            'pdf_url' => $fileUrl,
            'policy_number' => $policy->policy_number,
        ]);
    }

    #[OA\Get(
        path: "/api/user/hotel-bookings/{id}/insurance-certificate",
        summary: "تحميل وثيقة/شهادة التأمين لحجز الفندق",
        operationId: "downloadHotelInsuranceCertificate",
        description: "يقوم بتوليد وإرجاع رابط لشهادة التأمين الرسمية للفندق.",
        tags: ["User Bookings"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Returns URL")
        ]
    )]
    public function downloadHotelInsuranceCertificate(Request $request, $id, \App\Services\InvoiceService $invoiceService)
    {
        $booking = HotelBooking::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $policy = $booking->insurancePolicy ?? \App\Models\InsurancePolicy::where('hotel_booking_id', $booking->id)->first();
        if (!$policy) {
            return $this->apiResponse(true, __('No insurance policy found for this booking.'), null, null, 404);
        }

        $filePath = $policy->pdf_path;
        if (!$filePath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            $filePath = $invoiceService->generateInsurancePolicyPdf($policy);
            if ($filePath) {
                $policy->update(['pdf_path' => $filePath]);
            }
        }

        if (!$filePath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            return $this->apiResponse(true, __('Failed to generate insurance certificate.'), null, null, 500);
        }

        $fileUrl = asset('storage/' . $filePath);
        return $this->apiResponse(false, __('Insurance certificate retrieved successfully'), [
            'certificate_url' => $fileUrl,
            'pdf_url' => $fileUrl,
            'policy_number' => $policy->policy_number,
        ]);
    }

    #[OA\Get(
        path: "/api/user/trip-bookings/{id}/insurance-certificate",
        summary: "تحميل وثيقة/شهادة التأمين لحجز البكج السياحي",
        operationId: "downloadTripInsuranceCertificate",
        description: "يقوم بتوليد وإرجاع رابط لشهادة التأمين الرسمية للبرنامج السياحي.",
        tags: ["User Bookings"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Returns URL")
        ]
    )]
    public function downloadTripInsuranceCertificate(Request $request, $id, \App\Services\InvoiceService $invoiceService)
    {
        $booking = \App\Models\TripBooking::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $policy = $booking->insurancePolicy ?? \App\Models\InsurancePolicy::where('trip_booking_id', $booking->id)->first();
        if (!$policy) {
            return $this->apiResponse(true, __('No insurance policy found for this booking.'), null, null, 404);
        }

        $filePath = $policy->pdf_path;
        if (!$filePath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            $filePath = $invoiceService->generateInsurancePolicyPdf($policy);
            if ($filePath) {
                $policy->update(['pdf_path' => $filePath]);
            }
        }

        if (!$filePath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            return $this->apiResponse(true, __('Failed to generate insurance certificate.'), null, null, 500);
        }

        $fileUrl = asset('storage/' . $filePath);
        return $this->apiResponse(false, __('Insurance certificate retrieved successfully'), [
            'certificate_url' => $fileUrl,
            'pdf_url' => $fileUrl,
            'policy_number' => $policy->policy_number,
        ]);
    }

    /**
     * Get Flight Cancellation Quote (Penalties & Net Refund Amount)
     */
    #[OA\Get(
        path: "/api/user/bookings/flights/{id}/cancel-quote",
        summary: "استعلام تكلفة وغرامة إلغاء حجز الطيران (Flight Cancellation Quote)",
        operationId: "cancelFlightQuote",
        description: "الاستعلام عن شروط وغرامات إلغاء حجز الطيران والمبلغ الصافي المسترد قبل تأكيد الإلغاء. في حال كان الحجز معلقاً أو غير مصدر التذاكر يكون الإلغاء فورياً ومجانياً بالكامل. وفي حال كانت التذاكر مصدرة، يتم الاستعلام من شركة الطيران (GDS) عن الغرامة ومبلغ الاسترداد.",
        tags: ["User Bookings"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en")),
            new OA\Parameter(name: "id", in: "path", description: "Booking ID", required: true, schema: new OA\Schema(type: "integer", example: 123))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "تفاصيل غرامة واسترداد الحجز بنجاح",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Cancellation quote retrieved successfully."),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "booking_id", type: "integer", example: 123),
                                new OA\Property(property: "is_pending", type: "boolean", description: "هل الحجز معلق ولم تصدر تذاكره بعد؟", example: false),
                                new OA\Property(property: "refundable", type: "boolean", description: "هل التذكرة قابلة للاسترداد؟", example: true),
                                new OA\Property(property: "is_partial", type: "boolean", description: "هل الاسترداد جزئي (استرداد ضرائب ومطارات فقط)؟", example: false),
                                new OA\Property(property: "badge_text", type: "string", example: "تذكرة قابلة للاسترداد (REFUNDABLE)"),
                                new OA\Property(property: "penalty", type: "number", format: "float", description: "غرامة الإلغاء المقررة من شركة الطيران", example: 150.00),
                                new OA\Property(property: "refund_amount", type: "number", format: "float", description: "المبلغ الصافي المسترد الذي سيعاد لمحفظة العميل", example: 850.00),
                                new OA\Property(property: "currency", type: "string", example: "SAR"),
                                new OA\Property(property: "message", type: "string", example: "التذكرة قابلة للاسترداد وفق سياسة شركة الطيران بعد خصم الغرامة الموضحة.")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: "الحجز ملغى بالفعل"),
            new OA\Response(response: 404, description: "الحجز غير موجود أو لا يتبع للمستخدم")
        ]
    )]
    public function cancelFlightQuote(Request $request, $id)
    {
        $booking = Booking::with('passengers')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($booking->status === 'cancelled') {
            return $this->apiResponse(true, __('هذا الحجز ملغى بالفعل.'), [
                'booking_id' => $booking->id,
                'status' => 'cancelled'
            ], null, 400);
        }

        // If booking is pending or un-ticketed: cancellation is immediate & free
        if ($booking->status === 'pending' || empty($booking->ticket_numbers)) {
            return $this->apiResponse(false, __('الحجز ما زال قيد الانتظار ولم يتم إصدار التذاكر بعد. يمكنك إلغاء الحجز فوراً وبدون أي غرامات.'), [
                'booking_id' => $booking->id,
                'is_pending' => true,
                'refundable' => true,
                'is_partial' => false,
                'badge_text' => __('تذكرة غير مصدرة - إلغاء مجاني فوراً'),
                'penalty' => 0.0,
                'refund_amount' => (float)$booking->total_amount,
                'currency' => $booking->currency ?? 'SAR',
                'message' => __('الحجز ما زال قيد الانتظار ولم يتم إصدار التذاكر بعد. يمكنك إلغاء الحجز فوراً وبدون أي غرامات.')
            ], null, 200);
        }

        // If ticketed: query Travelopro refundQuote
        try {
            $paxDetails = [];
            foreach ($booking->passengers as $pax) {
                $rawType = strtolower((string)($pax->passenger_type ?? ''));
                $typeCode = 'ADT';
                if (str_starts_with($rawType, 'ch')) {
                    $typeCode = 'CHD';
                } elseif (str_starts_with($rawType, 'inf')) {
                    $typeCode = 'INF';
                }

                $paxDetails[] = [
                    'type' => $typeCode,
                    'title' => $pax->title ?: 'Mr',
                    'firstName' => $pax->first_name ?: 'Passenger',
                    'lastName' => $pax->last_name ?: 'Name',
                    'eTicket' => $pax->e_ticket_no ?: ($booking->ticket_numbers[0] ?? 'TKT' . $booking->id)
                ];
            }

            $quoteResult = $this->traveloproService->refundQuote([
                'uniqueId' => $booking->booking_reference,
                'paxDetails' => $paxDetails
            ], $booking->id);

            // If quote returned ptrUniqueID
            $ptrId = $quoteResult['RefundQuoteResponse']['RefundQuoteResult']['ptrUniqueID'] ?? null;
            if ($ptrId) {
                $statusResult = $this->traveloproService->getPostTicketStatus([
                    'UniqueID' => $booking->booking_reference,
                    'ptrUniqueID' => $ptrId
                ]);
                
                $ptrDetails = $statusResult['PtrResponse']['PtrResult']['PtrDetails'] ?? null;
                if (isset($ptrDetails[0])) {
                    $ptrDetails = $ptrDetails[0];
                }
                $paxDetailsNode = $ptrDetails['PaxDetails'] ?? null;
                if (isset($paxDetailsNode[0])) {
                    $paxDetailsNode = $paxDetailsNode[0];
                }
                $quotedFares = $paxDetailsNode['QuotedFares'] ?? null;

                if ($quotedFares) {
                    $totalFare = (float)($quotedFares['TotalFare']['Amount'] ?? $booking->total_amount);
                    $penalty = (float)($quotedFares['TotalRefundCharges']['Amount'] ?? $quotedFares['CancellationCharge']['Amount'] ?? 0);
                    $refundAmount = isset($quotedFares['TotalRefundAmount']['Amount'])
                        ? (float)$quotedFares['TotalRefundAmount']['Amount']
                        : max(0, $totalFare - $penalty);

                    $isFullyNonRefundable = ($refundAmount <= 0 && $penalty >= $totalFare);
                    $isPartialTaxRefund = (!$isFullyNonRefundable && $penalty > 0);

                    $badgeText = $isFullyNonRefundable 
                        ? __('تذكرة غير قابلة للاسترداد (NON-REFUNDABLE)')
                        : ($isPartialTaxRefund 
                            ? __('استرداد جزئي / استرداد ضرائب (PARTIAL TAX REFUND)')
                            : __('تذكرة قابلة للاسترداد (REFUNDABLE)'));

                    $message = !$isFullyNonRefundable
                        ? ($isPartialTaxRefund
                            ? __('التذكرة تتبع فئة غير قابلة لاسترداد السعر الأساسي، ولكن وفق لوائح الطيران يُسترد للعميل مبلغ ضرائب ورسوم المطارات غير المستخدمة بعد خصم الغرامة المقررة.')
                            : __('التذكرة قابلة للاسترداد وفق سياسة شركة الطيران بعد خصم الغرامة الموضحة.'))
                        : __('التذكرة تتبع فئة غير قابلة للاسترداد وفق شروط شركة الطيران. الإلغاء سيؤدي لفقدان قيمة التذكرة.');

                    return $this->apiResponse(false, __('Cancellation quote retrieved successfully.'), [
                        'booking_id' => $booking->id,
                        'is_pending' => false,
                        'refundable' => !$isFullyNonRefundable,
                        'is_partial' => $isPartialTaxRefund,
                        'badge_text' => $badgeText,
                        'ptr_id' => $ptrId,
                        'penalty' => $penalty,
                        'refund_amount' => max(0, $refundAmount),
                        'currency' => $quotedFares['TotalFare']['CurrencyCode'] ?? ($booking->currency ?? 'SAR'),
                        'message' => $message
                    ], null, 200);
                }
            }

            $isError = isset($quoteResult['status']) && $quoteResult['status'] === 'error';
            return $this->apiResponse(false, __('Cancellation quote retrieved successfully.'), [
                'booking_id' => $booking->id,
                'is_pending' => false,
                'refundable' => !$isError,
                'is_partial' => false,
                'badge_text' => $isError ? __('غير قابلة للاسترداد') : __('قابلة للاسترداد'),
                'penalty' => $isError ? (float)$booking->total_amount : 0.0,
                'refund_amount' => $isError ? 0.0 : (float)$booking->total_amount,
                'currency' => $booking->currency ?? 'SAR',
                'message' => $isError 
                    ? __('التذكرة تتبع فئة غير قابلة للاسترداد وفق شروط شركة الطيران. الإلغاء سيؤدي لفقدان قيمة التذكرة.')
                    : __('تم التحقق من إمكانية استرداد التذكرة. سيتم مراجعة المبلغ المسترد واعتماده.')
            ], null, 200);

        } catch (\Exception $e) {
            Log::error('Api Cancel flight quote error: ' . $e->getMessage());
            return $this->apiResponse(false, __('سيتم تقديم طلب استرداد التذكرة ومراجعة غرامات شركة الطيران.'), [
                'booking_id' => $booking->id,
                'is_pending' => false,
                'refundable' => true,
                'is_partial' => false,
                'badge_text' => __('طلب استرداد'),
                'penalty' => 0.0,
                'refund_amount' => (float)$booking->total_amount,
                'currency' => $booking->currency ?? 'SAR',
                'message' => __('سيتم تقديم طلب استرداد التذكرة ومراجعة غرامات شركة الطيران.')
            ], null, 200);
        }
    }

    /**
     * Confirm Flight Booking Cancellation (Process with Airline, Update DB & Credit Wallet)
     */
    #[OA\Post(
        path: "/api/user/bookings/flights/{id}/cancel",
        summary: "تأكيد إلغاء حجز الطيران واسترداد المبلغ للمحفظة (Confirm Flight Cancellation)",
        operationId: "confirmCancelFlight",
        description: "تأكيد وإتمام إلغاء حجز الطيران نهائياً؛ يتم إلغاء التذاكر عبر مزود الطيران، وتحديث حالة الحجز إلى ملغى ومسترد، وإيداع المبلغ المسترد فوراً في محفظة العميل في المنصة، مع إرسال إشعار فوري للعميل.",
        tags: ["User Bookings"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en")),
            new OA\Parameter(name: "id", in: "path", description: "Booking ID", required: true, schema: new OA\Schema(type: "integer", example: 123))
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "reason", type: "string", description: "سبب الإلغاء (اختياري)", example: "تغيير في خطة السفر")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "تم إلغاء الحجز وإيداع المسترد بنجاح",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "تم إلغاء حجز الطيران بنجاح وتم إيداع المبلغ المسترد (850.00 SAR) في محفظتك في المنصة."),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "booking_id", type: "integer", example: 123),
                                new OA\Property(property: "status", type: "string", example: "cancelled"),
                                new OA\Property(property: "ticket_status", type: "string", example: "refunded"),
                                new OA\Property(property: "refund_amount", type: "number", format: "float", example: 850.00),
                                new OA\Property(property: "cancellation_fee", type: "number", format: "float", example: 150.00),
                                new OA\Property(property: "currency", type: "string", example: "SAR")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: "الحجز ملغى بالفعل"),
            new OA\Response(response: 404, description: "الحجز غير موجود أو لا يتبع للمستخدم")
        ]
    )]
    public function cancelFlight(Request $request, $id, WalletService $walletService, NotificationService $notificationService)
    {
        $booking = Booking::with('passengers')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($booking->status === 'cancelled') {
            return $this->apiResponse(true, __('هذا الحجز ملغى بالفعل.'), [
                'booking_id' => $booking->id,
                'status' => 'cancelled'
            ], null, 400);
        }

        $wasPaid = in_array($booking->status, ['paid', 'confirmed', 'ticketed', 'completed']);
        $totalAmount = (float)$booking->total_amount;
        $refundAmount = 0.0;
        $penalty = 0.0;
        $cancelReason = $request->input('reason', __('إلغاء التذكرة بناءً على طلب العميل'));

        // 1. If pending or un-ticketed: direct cancellation with Travelopro
        if ($booking->status === 'pending' || empty($booking->ticket_numbers)) {
            if ($booking->booking_reference) {
                try {
                    $this->traveloproService->cancelBooking(['uniqueId' => $booking->booking_reference], $booking->id);
                } catch (\Exception $e) {
                    Log::warning("Supplier cancelBooking exception for #{$booking->id}: " . $e->getMessage());
                }
            }

            $booking->update([
                'status' => 'cancelled',
                'ticket_status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $cancelReason
            ]);

            if ($wasPaid) {
                $refundAmount = $totalAmount;
                $newWalletBalance = null;
                try {
                    $wallet = $walletService->getOrCreateWallet($booking->user_id);
                    $tx = $walletService->credit(
                        $wallet->id,
                        $refundAmount,
                        __('استرداد قيمة حجز طيران ملغى: :ref (:airline)', [
                            'ref' => $booking->booking_reference ?? $booking->pnr_code ?? '#' . $booking->id,
                            'airline' => $booking->airline_name ?? 'Flight'
                        ]),
                        \App\Models\Booking::class,
                        $booking->id
                    );
                    $newWalletBalance = $tx->balance_after;
                    Log::info("Credited {$refundAmount} to wallet for user #{$booking->user_id} on un-ticketed Flight #{$booking->id} cancellation via API.");
                } catch (\Exception $e) {
                    Log::error("Failed to credit wallet on un-ticketed flight cancel via API: " . $e->getMessage());
                }

                // Send Email to Customer
                try {
                    app(\App\Services\MailService::class)->sendBookingCancelledRefundedEmail(
                        $request->user(),
                        $booking,
                        $refundAmount,
                        0.0,
                        $newWalletBalance,
                        'flight',
                        $cancelReason ?: __('إلغاء الحجز قبل إصدار التذاكر واسترداد المبلغ بالكامل')
                    );
                } catch (\Exception $e) {
                    Log::warning('Failed to send un-ticketed cancellation refund email via API: ' . $e->getMessage());
                }
            }

            $successMsg = ($wasPaid && $refundAmount > 0)
                ? __('تم إلغاء حجز الطيران بنجاح وتم إيداع المبلغ المسترد (:amount :curr) في محفظتك في المنصة.', [
                    'amount' => number_format($refundAmount, 2),
                    'curr' => $booking->currency ?? 'SAR'
                ])
                : __('تم إلغاء حجز الطيران بنجاح.');

            // Notification
            try {
                $notificationService->sendToUser(
                    $request->user(),
                    Notification::TYPE_BOOKING_CANCELLED,
                    __('Booking Cancelled'),
                    $successMsg,
                    ['booking_id' => $booking->id, 'type' => 'flight']
                );
            } catch (\Exception $e) {
                Log::warning('Failed to send notification via API cancel: ' . $e->getMessage());
            }

            return $this->apiResponse(false, $successMsg, [
                'booking_id' => $booking->id,
                'status' => 'cancelled',
                'ticket_status' => 'cancelled',
                'refund_amount' => $refundAmount,
                'cancellation_fee' => 0.0,
                'currency' => $booking->currency ?? 'SAR'
            ], null, 200);
        }

        // 2. If ticketed: call Travelopro refund/void or cancel
        try {
            $paxDetails = [];
            foreach ($booking->passengers as $pax) {
                $rawType = strtolower((string)($pax->passenger_type ?? ''));
                $typeCode = 'ADT';
                if (str_starts_with($rawType, 'ch')) {
                    $typeCode = 'CHD';
                } elseif (str_starts_with($rawType, 'inf')) {
                    $typeCode = 'INF';
                }

                $paxDetails[] = [
                    'type' => $typeCode,
                    'title' => $pax->title ?: 'Mr',
                    'firstName' => $pax->first_name ?: 'Passenger',
                    'lastName' => $pax->last_name ?: 'Name',
                    'eTicket' => $pax->e_ticket_no ?: ($booking->ticket_numbers[0] ?? 'TKT' . $booking->id)
                ];
            }

            $quote = $this->traveloproService->refundQuote([
                'uniqueId' => $booking->booking_reference,
                'paxDetails' => $paxDetails
            ], $booking->id);

            $ptrId = $quote['RefundQuoteResponse']['RefundQuoteResult']['ptrUniqueID'] ?? null;
            if ($ptrId) {
                try {
                    $statusResult = $this->traveloproService->getPostTicketStatus([
                        'UniqueID' => $booking->booking_reference,
                        'ptrUniqueID' => $ptrId
                    ]);
                    $ptrDetails = $statusResult['PtrResponse']['PtrResult']['PtrDetails'] ?? null;
                    if (isset($ptrDetails[0])) {
                        $ptrDetails = $ptrDetails[0];
                    }
                    $paxDetailsNode = $ptrDetails['PaxDetails'] ?? null;
                    if (isset($paxDetailsNode[0])) {
                        $paxDetailsNode = $paxDetailsNode[0];
                    }
                    $quotedFares = $paxDetailsNode['QuotedFares'] ?? null;

                    if ($quotedFares) {
                        $totalFare = (float)($quotedFares['TotalFare']['Amount'] ?? $booking->total_amount);
                        $penalty = (float)($quotedFares['TotalRefundCharges']['Amount'] ?? $quotedFares['CancellationCharge']['Amount'] ?? 0);
                        $refundAmount = isset($quotedFares['TotalRefundAmount']['Amount'])
                            ? (float)$quotedFares['TotalRefundAmount']['Amount']
                            : max(0, $totalFare - $penalty);
                    }
                } catch (\Exception $e) {
                    Log::warning("Error parsing quote fares in cancelFlight via API: " . $e->getMessage());
                }

                $this->traveloproService->refundTicket([
                    'uniqueId' => $booking->booking_reference,
                    'ptrUniqueID' => $ptrId,
                    'paxDetails' => $paxDetails
                ], $booking->id);
            } else {
                $this->traveloproService->cancelBooking(['uniqueId' => $booking->booking_reference], $booking->id);
            }
        } catch (\Exception $e) {
            Log::error("Flight refund/cancel exception for #{$booking->id} via API: " . $e->getMessage());
        }

        // Update local booking status
        $booking->update([
            'status' => 'cancelled',
            'ticket_status' => 'refunded',
            'cancellation_fee' => $penalty ?? 0,
            'refund_amount' => $refundAmount ?? 0,
            'cancelled_at' => now(),
            'cancellation_reason' => $cancelReason
        ]);

        // Credit wallet if refund amount > 0
        if ($wasPaid && $refundAmount > 0) {
            $newWalletBalance = null;
            try {
                $wallet = $walletService->getOrCreateWallet($booking->user_id);
                $tx = $walletService->credit(
                    $wallet->id,
                    $refundAmount,
                    __('استرداد قيمة تذكرة طيران ملغاة: :ref (:airline)', [
                        'ref' => $booking->booking_reference ?? $booking->pnr_code ?? '#' . $booking->id,
                        'airline' => $booking->airline_name ?? 'Flight'
                    ]),
                    \App\Models\Booking::class,
                    $booking->id
                );
                $newWalletBalance = $tx->balance_after;
                Log::info("Credited {$refundAmount} to wallet for user #{$booking->user_id} on Flight #{$booking->id} cancellation via API.");
            } catch (\Exception $e) {
                Log::error("Failed to credit wallet on flight cancellation via API: " . $e->getMessage());
            }

            // Send Email to Customer
            try {
                app(\App\Services\MailService::class)->sendBookingCancelledRefundedEmail(
                    $request->user(),
                    $booking,
                    $refundAmount,
                    $penalty ?? 0.0,
                    $newWalletBalance,
                    'flight',
                    $cancelReason
                );
            } catch (\Exception $e) {
                Log::warning('Failed to send ticketed cancellation refund email via API: ' . $e->getMessage());
            }
        }

        $successMsg = ($wasPaid && $refundAmount > 0)
            ? __('تم إلغاء حجز الطيران بنجاح وتم إيداع المبلغ المسترد (:amount :curr) في محفظتك في المنصة.', [
                'amount' => number_format($refundAmount, 2),
                'curr' => $booking->currency ?? 'SAR'
            ])
            : __('تم إلغاء حجز الطيران بنجاح.');

        // Notification
        try {
            $notificationService->sendToUser(
                $request->user(),
                Notification::TYPE_BOOKING_CANCELLED,
                __('Booking Cancelled'),
                $successMsg,
                ['booking_id' => $booking->id, 'type' => 'flight']
            );
        } catch (\Exception $e) {
            Log::warning('Failed to send notification via API cancel: ' . $e->getMessage());
        }

        return $this->apiResponse(false, $successMsg, [
            'booking_id' => $booking->id,
            'status' => 'cancelled',
            'ticket_status' => 'refunded',
            'refund_amount' => $refundAmount,
            'cancellation_fee' => $penalty,
            'currency' => $booking->currency ?? 'SAR'
        ], null, 200);
    }
}
