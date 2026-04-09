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
            'pending' => FlightBooking::whereHas('booking', function($q) { $q->where('status', 'pending'); })->count(),
            'confirmed' => FlightBooking::whereHas('booking', function($q) { $q->where('status', 'confirmed'); })->count(),
            'cancelled' => FlightBooking::whereHas('booking', function($q) { $q->where('status', 'cancelled'); })->count(),
        ];
        return view('admin.bookings.flights.index', compact('stats'));
    }

    public function getFlightData()
    {
        $bookings = FlightBooking::with(['user', 'booking'])->latest()->get();
        $data = $bookings->map(function($fb) {
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
            'total'     => FlightBooking::whereHas('booking', function($q) { $q->where('status', 'pending'); })->count(),
            'pending'   => FlightBooking::whereHas('booking', function($q) { $q->where('status', 'pending'); })->count(),
            'confirmed' => FlightBooking::whereHas('booking', function($q) { $q->where('status', 'confirmed'); })->count(),
            'cancelled' => FlightBooking::whereHas('booking', function($q) { $q->where('status', 'cancelled'); })->count(),
        ];
        return view('admin.bookings.flights.requests', compact('stats'));
    }

    public function getFlightRequestsData()
    {
        $bookings = FlightBooking::with(['user', 'booking'])
            ->whereHas('booking', function($q) {
                $q->where('status', 'pending');
            })
            ->latest()->get();

        $data = $bookings->map(function($fb) {
            return [
                'id'        => $fb->booking_reference ?? $fb->id,
                'passenger' => optional($fb->user)->full_name ?? __('Guest'),
                'flight'    => $fb->flight_class ?? 'N/A',
                'route'     => ($fb->origin ?? '') . ' → ' . ($fb->destination ?? ''),
                'price'     => number_format($fb->total_amount, 2) . ' ' . $fb->currency,
                'status'    => optional($fb->booking)->status ?? 'pending',
                'actions'   => view('admin.bookings.partials.actions', [
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
            return [
                'id'         => $hb->id,
                'user'       => optional($hb->user)->full_name ?? __('Guest'),
                'hotel'      => $hb->hotel_name . '<br><small>' . ($hb->city_name ?? '') . '</small>',
                'dates'      => optional($hb->check_in)->format('Y-m-d') . ' / ' . optional($hb->check_out)->format('Y-m-d'),
                'amount'     => number_format($hb->total_price, 2) . ' ' . $hb->currency,
                'status'     => $hb->status ?? 'pending',
                'created_at' => optional($hb->created_at)->format('Y-m-d H:i'),
                'actions'    => view('admin.bookings.partials.actions', [
                    'id' => $hb->id,
                    'show_route' => 'admin.bookings.hotels.show_detail',
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
}


