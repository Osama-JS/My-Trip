<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TraveloproService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\FlightBooking;
use App\Models\HotelBooking;
use App\Models\Booking;

class BookingController extends Controller
{
    protected $traveloproService;

    public function __construct(TraveloproService $traveloproService)
    {
        $this->traveloproService = $traveloproService;
    }

    public function index()
    {
        $stats = [
            'flights' => FlightBooking::count(),
            'hotels' => HotelBooking::count(),
            'trips' => \App\Models\TripBooking::count(),
        ];
        return view('admin.bookings.index', compact('stats'));
    }

    // Flights
    public function flightBookings()
    {
        $stats = [
            'total' => FlightBooking::count(),
            'pending' => FlightBooking::whereHas('booking', function ($q) {
                $q->where('status', 'pending'); })->count(),
            'confirmed' => FlightBooking::whereHas('booking', function ($q) {
                $q->where('status', 'confirmed'); })->count(),
            'cancelled' => FlightBooking::whereHas('booking', function ($q) {
                $q->where('status', 'cancelled'); })->count(),
        ];
        return view('admin.bookings.flights.index', compact('stats'));
    }

    public function getFlightData()
    {
        $bookings = FlightBooking::with(['user', 'booking'])->latest()->get();
        $data = $bookings->map(function ($fb) {
            return [
                'id' => $fb->id,
                'user' => $fb->user->full_name ?? __('Guest'),
                'route' => $fb->origin . ' -> ' . $fb->destination,
                'dates' => $fb->departure_date->format('Y-m-d') . ($fb->return_date ? ' / ' . $fb->return_date->format('Y-m-d') : ''),
                'amount' => number_format($fb->total_amount, 2) . ' ' . $fb->currency,
                'status' => $fb->booking->status ?? 'pending',
                'created_at' => $fb->created_at->format('Y-m-d H:i'),
                'actions' => view('admin.bookings.partials.actions', [
                    'id' => $fb->id,
                    'show_route' => 'admin.bookings.flights.show',
                    'invoice_route' => 'admin.bookings.invoice'
                ])->render()
            ];
        });
        return response()->json(['data' => $data]);
    }


    public function availableFlights()
    {
        // This page is removed from sidebar but keeping method for now as a fallback
        $stats = [
            'total_routes' => 245,
            'airlines' => 15,
            'today_searches' => 120
        ];
        return view('admin.bookings.flights.available', compact('stats'));
    }

    /**
     * Search for flights via AJAX/POST
     */
    public function searchFlights(Request $request)
    {
        try {
            $results = $this->traveloproService->searchFlights($request->all());

            if (isset($results['status']) && $results['status'] === 'error') {
                return response()->json(['error' => true, 'message' => $results['message']], 500);
            }

            return response()->json(['error' => false, 'data' => $results]);
        } catch (\Exception $e) {
            Log::error('Admin Flight Search Error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => __('An error occurred while searching for flights.')], 500);
        }
    }

    /**
     * Validate the selected flight fare
     */
    public function validateFare(Request $request)
    {
        try {
            $result = $this->traveloproService->validateFare($request->all());

            if (isset($result['status']) && $result['status'] === 'error') {
                return response()->json(['error' => true, 'message' => $result['message']], 500);
            }

            // Check if IsValid is true in response
            $isValid = $result['AirRevalidateResponse']['AirRevalidateResult']['IsValid'] ?? false;
            if ($isValid !== true && $isValid !== 'true') {
                return response()->json(['error' => true, 'message' => __('Fare is no longer valid or available.')], 422);
            }

            return response()->json(['error' => false, 'data' => $result]);
        } catch (\Exception $e) {
            Log::error('Admin Flight Validate Error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => __('An error occurred while validating the fare.')], 500);
        }
    }

    /**
     * Create actual booking (PNR)
     */
    public function createBooking(Request $request)
    {
        try {
            $result = $this->traveloproService->createBooking($request->all());

            if (isset($result['status']) && $result['status'] === 'error') {
                return response()->json(['error' => true, 'message' => $result['message']], 500);
            }

            return response()->json(['error' => false, 'data' => $result]);
        } catch (\Exception $e) {
            Log::error('Admin Flight Booking Error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => __('An error occurred while creating the booking.')], 500);
        }
    }

    public function flightRequests()
    {
        $stats = [
            'total' => FlightBooking::whereHas('booking', function ($q) {
                $q->where('status', 'pending'); })->count(),
            'pending' => FlightBooking::whereHas('booking', function ($q) {
                $q->where('status', 'pending'); })->count(),
            'confirmed' => FlightBooking::whereHas('booking', function ($q) {
                $q->where('status', 'confirmed'); })->count(),
            'cancelled' => FlightBooking::whereHas('booking', function ($q) {
                $q->where('status', 'cancelled'); })->count(),
        ];
        return view('admin.bookings.flights.requests', compact('stats'));
    }

    public function getFlightRequestsData()
    {
        $bookings = FlightBooking::with(['user', 'booking'])
            ->whereHas('booking', function ($q) {
                $q->where('status', 'pending');
            })
            ->latest()->get();

        $data = $bookings->map(function ($fb) {
            return [
                'id' => $fb->booking_reference ?? $fb->id,
                'passenger' => optional($fb->user)->full_name ?? __('Guest'),
                'flight' => $fb->flight_class ?? 'N/A',
                'route' => ($fb->origin ?? '') . ' â†’ ' . ($fb->destination ?? ''),
                'price' => number_format($fb->total_amount, 2) . ' ' . $fb->currency,
                'status' => optional($fb->booking)->status ?? 'pending',
                'actions' => view('admin.bookings.partials.actions', [
                    'id' => $fb->id,
                    'show_route' => 'admin.bookings.flights.show',
                    'invoice_route' => 'admin.bookings.invoice'
                ])->render()
            ];
        });
        return response()->json(['data' => $data]);
    }



    public function ongoingFlights()
    {
        $stats = [
            'active_flights' => 12,
            'in_air' => 8,
            'on_ground' => 4,
            'delayed' => 1
        ];
        return view('admin.bookings.flights.ongoing', compact('stats'));
    }

    /**
     * Utility endpoints for UI (Airports/Airlines)
        public function getAirports()
    {
        return response()->json($this->traveloproService->getAirportList());
    }

    public function getAirlines()
    {
        return response()->json($this->traveloproService->getAirlineList());
    }

    // Hotels
    public function hotelBookings()
    {
        $stats = [
            'total'     => HotelBooking::count(),
            'pending'   => HotelBooking::where('status', 'pending')->count(),
            'confirmed' => HotelBooking::where('status', 'confirmed')->count(),
            'cancelled' => HotelBooking::where('status', 'cancelled')->count(),
        ];
        return view('admin.bookings.hotels.index', compact('stats'));
    }

    public function getHotelData()
    {
        $bookings = HotelBooking::with('user')->latest()->get();
        $data = $bookings->map(function($hb) {
            $requiresAction = ($hb->status === 'paid' && empty($hb->supplier_confirmation_num));
            return [
                'id'              => $hb->id,
                'user'            => optional($hb->user)->full_name ?? __('Guest'),
                'hotel'           => $hb->hotel_name . '<br><small>' . ($hb->city_name ?? '') . '</small>',
                'dates'           => optional($hb->check_in)->format('Y-m-d') . ' / ' . optional($hb->check_out)->format('Y-m-d'),
                'amount'          => number_format($hb->total_price, 2) . ' ' . $hb->currency,
                'status'          => $hb->status ?? 'pending',
                'requires_action' => $requiresAction,
                'created_at'      => optional($hb->created_at)->format('Y-m-d H:i'),
                'actions'         => view('admin.bookings.partials.actions', [
                    'id'            => $hb->id,
                    'show_route'    => 'admin.bookings.hotels.show_detail',
                    'invoice_route' => 'admin.bookings.hotels.invoice_detail'
                ])->render()
            ];
        });
        return response()->json(['data' => $data]);
    }



    public function hotelList()
    {
        // Removed from sidebar
        $stats = [
            'total_hotels' => 45,
            'featured' => 12,
            'top_rated' => 8
        ];
        return view('admin.bookings.hotels.list', compact('stats'));
    }

    public function hotelRequests()
    {
        $stats = [
            'total'     => HotelBooking::where('status', 'pending')->count(),
            'pending'   => HotelBooking::where('status', 'pending')->count(),
            'confirmed' => HotelBooking::where('status', 'confirmed')->count(),
            'cancelled' => HotelBooking::where('status', 'cancelled')->count(),
        ];
        return view('admin.bookings.hotels.requests', compact('stats'));
    }

    public function getHotelRequestsData()
    {
        $bookings = HotelBooking::with('user')
            ->where('status', 'pending')
            ->latest()->get();

        $data = $bookings->map(function($hb) {
            return [
                'id'      => $hb->reference_num ?? $hb->id,
                'user'    => optional($hb->user)->full_name ?? __('Guest'),
                'hotel'   => $hb->hotel_name . '<br><small>' . ($hb->city_name ?? '') . '</small>',
                'dates'   => optional($hb->check_in)->format('Y-m-d') . ' / ' . optional($hb->check_out)->format('Y-m-d'),
                'amount'  => number_format($hb->total_price, 2) . ' ' . $hb->currency,
                'status'  => $hb->status ?? 'pending',
                'actions' => view('admin.bookings.partials.actions', [
                    'id' => $hb->id,
                    'show_route' => 'admin.bookings.hotels.show_detail',
                    'invoice_route' => 'admin.bookings.hotels.invoice_detail'
                ])->render()
            ];
        });
        return response()->json(['data' => $data]);
    }



    public function show($id)
    {
        // Legacy fallback or generic search
        $booking = Booking::with(['user', 'flightBooking'])->find($id);
        if ($booking && $booking->flightBooking) {
            return $this->showFlight($booking->flightBooking->id);
        }

        $hotel = HotelBooking::find($id);
        if ($hotel) {
            return $this->showHotel($id);
        }

        abort(404);
    }

    public function showFlight($id)
    {
        $flightBooking = FlightBooking::with(['user', 'booking.passengers', 'booking.flightApiLogs'])->findOrFail($id);
        $booking = $flightBooking->booking;
        return view('admin.bookings.show', compact('booking', 'flightBooking'));
    }

    public function showHotel($id)
    {
        $hotelBooking = HotelBooking::with('user')->findOrFail($id);
        return view('admin.bookings.hotels.show', compact('hotelBooking'));
    }

    public function invoice($id)
    {
        // Try master booking first (Flights)
        $booking = Booking::with(['user', 'flightBooking'])->find($id);
        if ($booking && $booking->flightBooking) {
            return view('admin.bookings.invoice', compact('booking'));
        }

        // Try standalone Hotel
        $hotel = HotelBooking::with('user')->findOrFail($id);
        return view('admin.bookings.hotels.invoice', compact('hotel'));
    }

    // =========================================================================
    // HOTEL FALLBACK MANAGEMENT
    // For hotel bookings where payment succeeded but supplier session expired
    // =========================================================================

    /**
     * List all 'paid' hotel bookings awaiting supplier confirmation.
     */
    public function getPaidHotelBookings()
    {
        $bookings = HotelBooking::with('user')
            ->where('status', 'paid')
            ->whereNull('supplier_confirmation_num')
            ->latest()
            ->get();

        return response()->json([
            'count' => $bookings->count(),
            'data' => $bookings->map(fn($b) => [
                'id' => $b->id,
                'hotel_name' => $b->hotel_name,
                'user' => optional($b->user)->full_name ?? 'Guest',
                'user_email' => optional($b->user)->email ?? 'N/A',
                'total_price' => $b->total_price . ' ' . $b->currency,
                'created_at' => $b->created_at->format('Y-m-d H:i'),
                'has_session' => !empty($b->session_id),
            ])
        ]);
    }

    /**
     * Admin manually triggers a Travelopro retry for a specific 'paid' hotel booking.
     */
    public function retryHotelSupplierBooking(Request $request, $id)
    {
        $booking = HotelBooking::with('user')->findOrFail($id);

        if ($booking->status === 'confirmed' && !empty($booking->supplier_confirmation_num)) {
            return response()->json(['success' => false, 'message' => 'الحجز مؤكد بالفعل.']);
        }

        Log::info("Admin manual retry for HotelBooking #{$id} by admin #" . auth()->id());

        try {
            $hotelService = app(\App\Services\TraveloproHotelService::class);
            $invoiceService = app(\App\Services\InvoiceService::class);

            $bookingData = [
                'sessionId' => $booking->session_id,
                'productId' => $booking->product_id,
                'tokenId' => $booking->token_id,
                'rateBasisId' => $booking->rate_basis_id,
                'clientRef' => $booking->reference_num ?? ('HTL-' . $booking->id . '-' . time()),
                'customerEmail' => optional($booking->user)->email ?? 'guest@mytrip.com',
                'customerPhone' => optional($booking->user)->phone ?? '0000000000',
                'bookingNote' => 'Manual admin retry â€” Booking #' . $booking->id,
                'paxDetails' => $booking->pax_details ?? [],
            ];

            $result = $hotelService->book($bookingData);
            $supplierRef = $result['supplierConfirmationNum']
                ?? $result['referenceNum']
                ?? $result['bookingId']
                ?? null;

            if ($supplierRef) {
                $booking->update(['status' => 'confirmed', 'supplier_confirmation_num' => $supplierRef]);

                try {
                    $voucherPath = $invoiceService->generateHotelVoucher($booking);
                    if ($voucherPath) {
                        $booking->update(['invoice_path' => $voucherPath]);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Admin retry voucher failed: " . $e->getMessage());
                }

                if ($booking->user) {
                    app(\App\Services\NotificationService::class)->sendToUser(
                        $booking->user,
                        \App\Models\Notification::TYPE_BOOKING_CONFIRMED,
                        'Hotel Booking Confirmed',
                        'Your hotel booking at ' . $booking->hotel_name . ' has been confirmed! Reference: ' . $supplierRef,
                        ['booking_id' => $booking->id, 'type' => 'hotel']
                    );
                }

                return response()->json(['success' => true, 'message' => 'ØªÙ… ØªØ£ÙƒÙŠØ¯ Ø§Ù„Ø­Ø¬Ø² Ù…Ø¹ Travelopro Ø¨Ù†Ø¬Ø§Ø­!', 'supplier_ref' => $supplierRef]);
            }

            \App\Jobs\RetryHotelSupplierBookingJob::dispatch($booking->id)->onQueue('critical');

            return response()->json(['success' => false, 'queued' => true, 'message' => 'Ø§Ù„Ø¬Ù„Ø³Ø© Ù…Ù†ØªÙ‡ÙŠØ©. ØªÙ… Ø¬Ø¯ÙˆÙ„Ø© Ø¥Ø¹Ø§Ø¯Ø© Ù…Ø­Ø§ÙˆÙ„Ø© ÙÙŠ Ø§Ù„Ø®Ù„ÙÙŠØ©.']);

        } catch (\Exception $e) {
            Log::error("Admin retry FAILED for HotelBooking #{$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'ÙØ´Ù„Øª Ø§Ù„Ù…Ø­Ø§ÙˆÙ„Ø©: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Force-confirm a hotel booking manually with a supplier reference from Travelopro support.
     * Use ONLY after verbal/email confirmation from Travelopro.
     */
    public function forceConfirmHotelBooking(Request $request, $id)
    {
        $request->validate(['supplier_ref' => 'required|string|max:100']);

        $booking = HotelBooking::with('user')->findOrFail($id);

        if ($booking->status === 'confirmed') {
            return response()->json(['success' => false, 'message' => 'Ø§Ù„Ø­Ø¬Ø² Ù…Ø¤ÙƒØ¯ Ø¨Ø§Ù„ÙØ¹Ù„.']);
        }

        $booking->update([
            'status' => 'confirmed',
            'supplier_confirmation_num' => $request->supplier_ref,
        ]);

        try {
            $voucherPath = app(\App\Services\InvoiceService::class)->generateHotelVoucher($booking);
            if ($voucherPath) {
                $booking->update(['invoice_path' => $voucherPath]);
            }
        } catch (\Exception $e) {
            Log::warning("Force-confirm voucher failed: " . $e->getMessage());
        }

        if ($booking->user) {
            app(\App\Services\NotificationService::class)->sendToUser(
                $booking->user,
                \App\Models\Notification::TYPE_BOOKING_CONFIRMED,
                'Hotel Booking Confirmed',
                'Your hotel booking at ' . $booking->hotel_name . ' has been confirmed! Reference: ' . $request->supplier_ref,
                ['booking_id' => $booking->id, 'type' => 'hotel']
            );
        }

        Log::info("FORCE CONFIRM HotelBooking #{$id} by admin #" . auth()->id() . ". Ref: " . $request->supplier_ref);

        return response()->json(['success' => true, 'message' => 'ØªÙ… ØªØ£ÙƒÙŠØ¯ Ø§Ù„Ø­Ø¬Ø² ÙŠØ¯ÙˆÙŠØ§Ù‹ ÙˆØ¥Ø´Ø¹Ø§Ø± Ø§Ù„Ø¹Ù…ÙŠÙ„.']);
    }
}
