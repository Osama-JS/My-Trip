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
        $airportCodes = $bookings->pluck('origin')->merge($bookings->pluck('destination'))->filter(function($code) {
            return !empty($code) && $code !== 'N/A';
        })->unique();
        $airports = \App\Models\Airport::whereIn('airport_code', $airportCodes)->get()->keyBy('airport_code');
        
        $data = $bookings->map(function ($fb) use ($airports) {
            $originName = $fb->origin ?? 'N/A';
            if (isset($airports[$fb->origin])) {
                $originName = app()->getLocale() == 'ar' ? $airports[$fb->origin]->airport_name_ar : $airports[$fb->origin]->airport_name;
            }
            $destName = $fb->destination ?? 'N/A';
            if (isset($airports[$fb->destination])) {
                $destName = app()->getLocale() == 'ar' ? $airports[$fb->destination]->airport_name_ar : $airports[$fb->destination]->airport_name;
            }
            
            return [
                'id' => $fb->id,
                'reference' => '<strong>' . ($fb->booking->booking_reference ?? 'N/A') . '</strong>',
                'user' => $fb->user->full_name ?? __('Guest'),
                'route' => $originName . ' <i class="fas fa-arrow-right mx-1 text-muted"></i> ' . $destName,
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

    public function flightAnalytics(Request $request)
    {
        $query = FlightBooking::with('booking')->has('booking');

        // Apply Filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('booking', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }
        if ($request->filled('airline')) {
            $airline = $request->airline;
            $query->where(function($q) use ($airline) {
                $q->whereHas('booking', function($b) use ($airline) {
                    $b->where('airline_name', 'like', "%{$airline}%");
                })->orWhere('itinerary_data', 'like', "%{$airline}%");
            });
        }

        $allFiltered = $query->get();

        // KPIs
        $totalBookings = $allFiltered->count();
        $totalRevenue = $allFiltered->filter(function($fb) {
            return in_array(optional($fb->booking)->status, ['confirmed', 'paid']);
        })->sum('total_amount');
        $pendingBookings = $allFiltered->filter(function($fb) {
            return optional($fb->booking)->status === 'pending';
        })->count();
        $confirmedBookings = $allFiltered->filter(function($fb) {
            return in_array(optional($fb->booking)->status, ['confirmed', 'paid']);
        })->count();
        $cancelledBookings = $allFiltered->filter(function($fb) {
            return optional($fb->booking)->status === 'cancelled';
        })->count();
        $totalPassengers = $allFiltered->sum(function($fb) {
            return ($fb->adults ?? 0) + ($fb->childs ?? 0) + ($fb->infants ?? 0);
        });

        // Line Chart: Bookings over time (grouped by month or day based on range)
        // For simplicity, let's group by Month-Year if no date filter, or Day if filtered.
        $groupByFormat = $request->filled('date_from') && $request->filled('date_to') ? 'Y-m-d' : 'Y-m';
        $trendData = $allFiltered->groupBy(function($fb) use ($groupByFormat) {
            return $fb->created_at->format($groupByFormat);
        })->map->count();

        $chartLabels = $trendData->keys()->toArray();
        $chartDataValues = $trendData->values()->toArray();

        // Doughnut 1: Top Airlines (using Booking->airline_name if available)
        $airlinesData = $allFiltered->map(function($fb) {
            return optional($fb->booking)->airline_name ?: __('Unknown');
        })->countBy()->sortDesc()->take(5);

        // Doughnut 2: Status Distribution
        $statusData = $allFiltered->map(function($fb) {
            $status = optional($fb->booking)->status ?: 'pending';
            return __(ucfirst($status));
        })->countBy();

        // Bar Chart: Top Destinations
        $destinationsData = $allFiltered->map(function($fb) {
            return $fb->destination ?: __('Unknown');
        })->countBy()->sortDesc()->take(5);

        $stats = [
            'total' => $totalBookings,
            'revenue' => $totalRevenue,
            'pending' => $pendingBookings,
            'confirmed' => $confirmedBookings,
            'cancelled' => $cancelledBookings,
            'passengers' => $totalPassengers,
            'chartLabels' => $chartLabels,
            'chartData' => $chartDataValues,
            'airlinesLabels' => $airlinesData->keys()->toArray(),
            'airlinesData' => $airlinesData->values()->toArray(),
            'statusLabels' => $statusData->keys()->toArray(),
            'statusData' => $statusData->values()->toArray(),
            'destinationsLabels' => $destinationsData->keys()->toArray(),
            'destinationsData' => $destinationsData->values()->toArray(),
        ];

        // Top 10 Customers with confirmed/paid bookings
        $topCustomers = $allFiltered->filter(function($fb) {
            $status = optional($fb->booking)->status;
            return $status === 'confirmed' || $status === 'paid';
        })->groupBy(function($fb) {
            return optional($fb->user)->id ?: ($fb->email ?: 'guest_' . $fb->id);
        })->map(function($userBookings) {
            $first = $userBookings->first();
            $name = optional($first->user)->full_name ?: trim(($first->first_name ?? '') . ' ' . ($first->last_name ?? ''));
            return (object) [
                'name' => $name ?: __('Guest'),
                'email' => optional($first->user)->email ?: ($first->email ?: __('N/A')),
                'bookings_count' => $userBookings->count(),
                'total_spent' => $userBookings->sum('total_amount'),
                'currency' => $first->currency
            ];
        })->sortByDesc('bookings_count')->take(10);

        $recentBookings = $allFiltered->sortByDesc('created_at')->take(10);
        return view('admin.bookings.flights.analytics', compact('stats', 'recentBookings', 'topCustomers'));
    }    

    public function flightProfits(Request $request)
    {
        $query = Booking::whereNotNull('platform_profit')->where('platform_profit', '>', 0)
            ->whereHas('flightBooking')
            ->whereIn('status', ['confirmed', 'paid']);
            
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $totalProfit = $query->sum('platform_profit');
        
        return view('admin.bookings.flights.profits', compact('totalProfit'));
    }

    public function getFlightProfitsData(Request $request)
    {
        $query = Booking::whereNotNull('platform_profit')->where('platform_profit', '>', 0)
            ->whereHas('flightBooking')
            ->whereIn('status', ['confirmed', 'paid'])
            ->with(['user', 'flightBooking']);
            
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $bookings = $query->latest()->get();
        $data = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'reference' => '<strong>' . $booking->booking_reference . '</strong>',
                'customer' => optional($booking->user)->full_name ?? $booking->contact_email,
                'date' => $booking->created_at->format('Y-m-d H:i'),
                'base_price' => number_format($booking->provider_price, 2) . ' ' . $booking->currency,
                'total_amount' => number_format($booking->total_amount, 2) . ' ' . $booking->currency,
                'profit' => '<span class="text-success fw-bold">+' . number_format($booking->platform_profit, 2) . ' ' . $booking->currency . '</span>',
                'actions' => '<a href="' . route('admin.bookings.flights.show', $booking->flightBooking->id ?? 0) . '" class="btn btn-primary shadow btn-xs sharp" title="' . __('View') . '"><i class="fas fa-eye"></i></a>'
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
     */
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
            'total' => HotelBooking::count(),
            'pending' => HotelBooking::where('status', 'pending')->count(),
            'confirmed' => HotelBooking::whereIn('status', ['confirmed', 'paid'])->count(),
            'cancelled' => HotelBooking::where('status', 'cancelled')->count(),
        ];
        return view('admin.bookings.hotels.index', compact('stats'));
    }

    public function hotelProfits(Request $request)
    {
        $query = HotelBooking::whereNotNull('platform_profit')->where('platform_profit', '>', 0)
            ->whereIn('status', ['confirmed', 'paid']);
            
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $totalProfit = $query->sum('platform_profit');
        
        return view('admin.bookings.hotels.profits', compact('totalProfit'));
    }

    public function getHotelProfitsData(Request $request)
    {
        $query = HotelBooking::whereNotNull('platform_profit')->where('platform_profit', '>', 0)
            ->whereIn('status', ['confirmed', 'paid'])
            ->with('user');
            
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $bookings = $query->latest()->get();
        $data = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'reference' => '<strong>' . $booking->reference_num . '</strong>',
                'hotel' => $booking->hotel_name,
                'customer' => optional($booking->user)->full_name ?? $booking->contact_email,
                'date' => $booking->created_at->format('Y-m-d H:i'),
                'base_price' => number_format($booking->provider_price, 2) . ' ' . $booking->currency,
                'total_amount' => number_format($booking->total_price, 2) . ' ' . $booking->currency,
                'profit' => '<span class="text-success fw-bold">+' . number_format($booking->platform_profit, 2) . ' ' . $booking->currency . '</span>',
                'actions' => '<a href="' . route('admin.bookings.hotels.show', $booking->id) . '" class="btn btn-primary shadow btn-xs sharp" title="' . __('View') . '"><i class="fas fa-eye"></i></a>'
            ];
        });

        return response()->json(['data' => $data]);
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
                    'show_route'    => 'admin.bookings.hotels.show',
                    'invoice_route' => 'admin.bookings.hotels.invoice'
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
                    'show_route' => 'admin.bookings.hotels.show',
                    'invoice_route' => 'admin.bookings.hotels.invoice'
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
