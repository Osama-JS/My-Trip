<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Country;
use App\Models\City;
use App\Models\Banner;
use App\Models\Question;
use App\Models\Setting;
use App\Models\TripCategory;
use App\Models\TripRate;
use App\Models\TripBooking;
use App\Models\Company;
use App\Models\User;
use App\Models\Airport;
use App\Services\TraveloproService;
use App\Services\TraveloproHotelService;

class FrontendController extends Controller
{
    protected $traveloproService;
    protected $hotelService;

    public function __construct(TraveloproService $traveloproService, TraveloproHotelService $hotelService)
    {
        $this->traveloproService = $traveloproService;
        $this->hotelService = $hotelService;
    }
    /**
     * Homepage
     */
    public function home()
    {
        $banners = Banner::active()->ordered()->get();
        
        $featuredTrips = Trip::active()
            ->where('is_featured', true)
            ->with(['images', 'fromCountry', 'toCountry', 'toCity', 'company', 'rates', 'categories'])
            ->latest()
            ->take(6)
            ->get();

        $destinations = Country::active()
            ->whereHas('cities')
            ->withCount(['cities' => function($q) {
                $q->where('active', true);
            }])
            ->take(8)
            ->get();

        // Get destination trip counts
        foreach ($destinations as $dest) {
            $dest->trips_count = Trip::active()->where('to_country_id', $dest->id)->count();
        }

        $questions = Question::where('active', true)->take(4)->get();

        $stats = [
            'trips' => Trip::active()->count(),
            'destinations' => Country::active()->whereHas('cities')->count(),
            'customers' => User::where('user_type', 'customer')->count(),
            'rating' => round(TripRate::avg('rate') ?? 4.8, 1),
        ];

        $latestTrips = Trip::active()
            ->with(['images', 'fromCountry', 'toCountry', 'toCity', 'company', 'rates'])
            ->latest()
            ->take(6)
            ->get();

        $categories = TripCategory::withCount(['trips' => function($q) {
            $q->active();
        }])->get();

        $countries = Country::active()->get();

        return view('frontend.home', compact(
            'banners', 'featuredTrips', 'destinations', 'questions',
            'stats', 'latestTrips', 'categories', 'countries'
        ));
    }

    /**
     * Trips listing page
     */
    public function trips(Request $request)
    {
        $query = Trip::active()
            ->with(['images', 'fromCountry', 'toCountry', 'toCity', 'company', 'rates', 'categories']);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('trip_categories.id', $request->category);
            });
        }

        // Filter by destination country
        if ($request->filled('destination')) {
            $query->where('to_country_id', $request->destination);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by duration
        if ($request->filled('duration')) {
            $query->where('duration', '<=', $request->duration);
        }

        // Search by keyword
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function(\Illuminate\Database\Eloquent\Builder $q) use ($search) {
                $q->where('title_ar', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('description_ar', 'like', "%{$search}%")
                  ->orWhere('description_en', 'like', "%{$search}%");
            });
        }

        // Sorting
        switch ($request->get('sort', 'latest')) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->withAvg('rates', 'rate')->orderByDesc('rates_avg_rate');
                break;
            default:
                $query->latest();
        }

        $trips = $query->paginate(12);
        $categories = TripCategory::withCount(['trips' => fn($q) => $q->active()])->get();
        $countries = Country::active()->get();

        return view('frontend.trips.index', compact('trips', 'categories', 'countries'));
    }

    /**
     * Trip details page
     */
    public function tripDetails($id)
    {
        $trip = Trip::active()
            ->with(['images', 'fromCountry', 'toCountry', 'toCity', 'fromCity', 'company', 'rates.user', 'categories', 'itineraries'])
            ->findOrFail($id);

        // Increment page visits
        $trip->increment('page_visits');

        // Related trips
        $relatedTrips = Trip::active()
            ->where('id', '!=', $trip->id)
            ->where(function($q) use ($trip) {
                $q->where('to_country_id', $trip->to_country_id)
                  ->orWhereHas('categories', function($cq) use ($trip) {
                      $cq->whereIn('trip_categories.id', $trip->categories->pluck('id'));
                  });
            })
            ->with(['images', 'toCountry', 'toCity', 'rates'])
            ->take(3)
            ->get();

        $avgRating = $trip->rates->avg('rate') ?? 0;

        return view('frontend.trips.show', compact('trip', 'relatedTrips', 'avgRating'));
    }

    /**
     * Flights search page
     */
    public function flights()
    {
        return view('frontend.flights');
    }

    /**
     * Hotels search page
     */
    public function hotels()
    {
        return view('frontend.hotels');
    }

    /**
     * Hotel Search Results (AJAX)
     */
    public function hotelResults(Request $request)
    {
        $data = $request->all();
        $results = $this->hotelService->search($data);

        if ($request->ajax()) {
            return view('frontend.hotels.results_partial', [
                'results' => $results,
                'searchParams' => $data
            ]);
        }

        return view('frontend.hotels', [
            'results' => $results,
            'searchParams' => $data
        ]);
    }

    /**
     * Get Hotel Room Rates (AJAX)
     */
    public function hotelRoomRates(Request $request)
    {
        $data = $request->all();
        $result = $this->hotelService->getRoomRates($data);
        
        $rawOptions = $result['roomRates']['perBookingRates'] ?? $result['roomRates']['RoomResults'] ?? [];
        $options = [];
        
        foreach ($rawOptions as $opt) {
            $options[] = [
                'room_name' => $opt['roomType'] ?? $opt['room_type'] ?? __('Standard Room'),
                'board_type' => $opt['boardType'] ?? $opt['board_type'] ?? __('Room Only'),
                'net_price' => $opt['netPrice'] ?? $opt['net_price'] ?? 0,
                'rate_basis_id' => $opt['rateBasisId'] ?? $opt['rate_basis_id'] ?? '',
                'cancel_policy' => $opt['cancellationPolicy'] ?? $opt['cancellation_policy'] ?? null,
            ];
        }

        $formatted = [
            'options' => $options,
            'currency' => $result['roomRates']['currency'] ?? $result['currency'] ?? 'SAR',
            'hotel_name' => $request->get('hotelName', '')
        ];

        return view('frontend.hotels.room_rates_partial', [
            'rooms' => $formatted,
            'hotelDetails' => $data 
        ]);
    }

    /**
     * Hotel Revalidate (Check Rates)
     */
    public function hotelRevalidate(Request $request)
    {
        $data = $request->all();
        $result = $this->hotelService->checkRoomRates($data);
        
        return response()->json($result);
    }

    /**
     * Search Cities for Hotel Search (Select2)
     */
    public function searchHotelCities(Request $request)
    {
        $q = strtolower($request->get('q', ''));
        $cities = $this->hotelService->getCities(['q' => $q]);
        
        $rawCities = $cities['cities'] ?? $cities['Cities'] ?? [];
        $formatted = [];

        foreach ($rawCities as $city) {
            $cityName = $city['CityName'] ?? $city['city_name'] ?? '';
            $countryName = $city['CountryName'] ?? $city['country_name'] ?? '';
            
            // Local filtering if API doesn't support 'q'
            if ($q && !str_contains(strtolower($cityName), $q) && !str_contains(strtolower($countryName), $q)) {
                continue;
            }

            $formatted[] = [
                'id' => $cityName,
                'text' => "{$cityName}, {$countryName}",
                'city_name' => $cityName,
                'country_name' => $countryName
            ];

            if (count($formatted) >= 20) break; // Limit results
        }

        return response()->json(['results' => $formatted]);
    }

    /**
     * Hotel Booking Form
     */
    public function hotelBookingForm(Request $request)
    {
        return view('frontend.hotels.booking', [
            'details' => $request->all()
        ]);
    }

    /**
     * Process Hotel Booking
     */
    public function processHotelBooking(Request $request)
    {
        $paxDetails = [];
        $rooms = $request->get('rooms', 1);
        
        for ($i = 1; $i <= $rooms; $i++) {
            $roomPax = [
                'room_no' => $i,
                'adult' => [
                    'title' => $request->input("pax.{$i}.adult.title"),
                    'firstName' => $request->input("pax.{$i}.adult.firstName"),
                    'lastName' => $request->input("pax.{$i}.adult.lastName"),
                ]
            ];
            
            if ($request->has("pax.{$i}.child")) {
                $roomPax['child'] = [
                    'title' => $request->input("pax.{$i}.child.title"),
                    'firstName' => $request->input("pax.{$i}.child.firstName"),
                    'lastName' => $request->input("pax.{$i}.child.lastName"),
                ];
            }
            $paxDetails[] = $roomPax;
        }

        $bookingData = [
            'sessionId' => $request->get('sessionId'),
            'productId' => $request->get('productId'),
            'tokenId' => $request->get('tokenId'),
            'rateBasisId' => $request->get('rateBasisId'),
            'clientRef' => 'HTL-' . strtoupper(uniqid()),
            'customerEmail' => $request->get('customerEmail'),
            'customerPhone' => $request->get('customerPhone'),
            'bookingNote' => 'Hotel Booking',
            'paxDetails' => $paxDetails
        ];

        $result = $this->hotelService->book($bookingData);

        if (isset($result['status']) && $result['status'] === 'error') {
            return back()->with('error', $result['message'])->withInput();
        }

        try {
            $hotelBooking = \App\Models\HotelBooking::create([
                'user_id' => auth()->id(),
                'hotel_name' => $request->get('hotelName', 'Hotel Booking'),
                'hotel_id' => $request->get('hotelId', 'N/A'),
                'city_name' => $request->get('cityName'),
                'country_name' => $request->get('countryName'),
                'check_in' => $request->get('checkIn'),
                'check_out' => $request->get('checkOut'),
                'rooms' => $rooms,
                'adults' => $request->get('adults', 1),
                'childs' => $request->get('childs', 0),
                'total_price' => $request->get('total_amount', 0),
                'currency' => $request->get('currency', 'SAR'),
                'status' => 'pending',
                'reference_num' => $result['referenceNum'] ?? null,
                'supplier_confirmation_num' => $result['supplierConfirmationNum'] ?? null,
                'session_id' => $request->get('sessionId'),
                'product_id' => $request->get('productId'),
                'token_id' => $request->get('tokenId'),
                'pax_details' => $paxDetails,
            ]);

            return redirect()->route('payments.web.checkout', [
                'booking_id' => $hotelBooking->id, 
                'method' => 'visa_master', 
                'type' => 'hotel'
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Local Hotel Save Error: ' . $e->getMessage());
            return back()->with('error', __('Local save failed. Ref: ') . ($result['referenceNum'] ?? 'N/A'));
        }
    }

    /**
     * Flight Search Results
     */
    public function flightResults(Request $request)
    {
        // Prepare search request for Travelopro
        $searchData = [
            'journeyType' => $request->get('journeyType', 'OneWay'),
            'class' => $request->get('class', 'Economy'),
            'adults' => $request->get('adults', 1),
            'childs' => $request->get('childs', 0),
            'infants' => $request->get('infants', 0),
            'OriginDestinationInfo' => [
                [
                    'departureDate' => $request->get('departDate'),
                    'airportOriginCode' => $request->get('from'),
                    'airportDestinationCode' => $request->get('to'),
                ]
            ]
        ];

        if ($request->get('journeyType') === 'Return') {
             $searchData['OriginDestinationInfo'][0]['returnDate'] = $request->get('returnDate');
        }

        $results = $this->traveloproService->searchFlights($searchData);

        // If AJAX request, return partial view only
        if ($request->ajax() || $request->get('ajax') == '1') {
            return view('frontend.flights.results_partial', [
                'results' => $results,
                'searchParams' => $request->all()
            ]);
        }

        return view('frontend.flights.results', [
            'results' => $results,
            'searchParams' => $request->all()
        ]);
    }

    /**
     * Flight Revalidate & Details before booking
     */
    public function flightRevalidate(Request $request)
    {
        $data = $request->all();
        $revalidate = $this->traveloproService->validateFare($data);
        
        return response()->json($revalidate);
    }

    /**
     * Flight Booking Form
     */
    public function flightBookingForm(Request $request)
    {
        // Expecting flight details in session/request to show summary
        return view('frontend.flights.booking', [
            'details' => $request->all()
        ]);
    }

    /**
     * Process Flight Booking
     */
    public function processFlightBooking(Request $request)
    {
        // 1. Call Travelopro Create Booking
        $result = $this->traveloproService->createBooking($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return back()->with('error', $result['message'])->withInput();
        }

        // 2. Persist in local DB (Booking model)
        // Migration might be missing but we try to save if possible
        try {
            $booking = \App\Models\Booking::create([
                'user_id' => auth()->id(),
                'pnr_number' => $result['AirBookingResponse']['AirBookingResult']['Pnrs']['Pnr'] ?? 'PENDING',
                'booking_reference' => 'FLIGHT-' . strtoupper(uniqid()),
                'total_price' => $request->get('total_amount'), // From revalidate
                'status' => 'pending',
                'pax_count' => $request->get('adults', 1) + $request->get('childs', 0) + $request->get('infants', 0),
                'origin' => $request->get('from'),
                'destination' => $request->get('to'),
                'departure_date' => $request->get('departDate'),
            ]);

            // Redirect to unified payment flow
            return redirect()->route('payments.web.checkout', ['booking_id' => $booking->id, 'method' => 'visa_master', 'type' => 'flight']);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Local Flight Save Error: ' . $e->getMessage());
            return back()->with('error', __('Booking saved on provider but failed locally. Contact support. Reference: ') . ($result['AirBookingResponse']['AirBookingResult']['Pnrs']['Pnr'] ?? 'N/A'));
        }
    }

    /**
     * Destinations page
     */
    public function destinations()
    {
        $destinations = Country::active()
            ->withCount(['cities' => fn($q) => $q->where('active', true)])
            ->get()
            ->map(function($dest) {
                $dest->trips_count = Trip::active()->where('to_country_id', $dest->id)->count();
                return $dest;
            })
            ->sortByDesc('trips_count');

        return view('frontend.destinations', compact('destinations'));
    }

    /**
     * About page
     */
    public function about()
    {
        $stats = [
            'trips' => Trip::active()->count(),
            'destinations' => Country::active()->count(),
            'customers' => User::where('user_type', 'customer')->count(),
            'rating' => round(TripRate::avg('rate') ?? 4.8, 1),
        ];

        $questions = Question::where('active', true)->get();

        return view('frontend.about', compact('stats', 'questions'));
    }

    /**
     * Search page
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $trips = Trip::active()
            ->with(['images', 'toCountry', 'toCity', 'rates'])
            ->where(function(\Illuminate\Database\Eloquent\Builder $q) use ($query) {
                $q->where('title_ar', 'like', "%{$query}%")
                  ->orWhere('title_en', 'like', "%{$query}%")
                  ->orWhere('description_ar', 'like', "%{$query}%")
                  ->orWhere('description_en', 'like', "%{$query}%");
            })
            ->paginate(12);

        return view('frontend.search', compact('trips', 'query'));
    }

    /**
     * Book a trip
     */
    public function bookTrip(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'tickets_count' => 'required|integer|min:1',
            'booking_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:500',
        ]);

        $trip = Trip::active()->findOrFail($request->trip_id);

        $totalPrice = $trip->price * $request->tickets_count;

        // Extra passenger pricing
        if ($trip->base_capacity && $request->tickets_count > $trip->base_capacity && $trip->extra_passenger_price) {
            $extraPassengers = $request->tickets_count - $trip->base_capacity;
            $totalPrice = ($trip->price * $trip->base_capacity) + ($trip->extra_passenger_price * $extraPassengers);
        }

        $booking = TripBooking::create([
            'user_id' => auth()->id(),
            'trip_id' => $trip->id,
            'status' => 'pending',
            'booking_state' => TripBooking::STATE_RECEIVED,
            'total_price' => $totalPrice,
            'booking_date' => $request->booking_date,
            'tickets_count' => $request->tickets_count,
            'notes' => $request->notes,
        ]);

        return redirect()->route('customer.bookings.show', $booking->id)
            ->with('success', __('Booking created successfully! Please proceed with payment.'));
    }
        /**
     * Search Airports locally (for Select2)
     */
    public function searchAirports(Request $request)
    {
        $q = $request->get('q');
        
        $airports = Airport::where('airport_name', 'like', "%{$q}%")
            ->orWhere('airport_code', 'like', "%{$q}%")
            ->orWhere('city_name', 'like', "%{$q}%")
            ->latest()
            ->limit(20)
            ->get(['airport_code as id', 'airport_name', 'city_name', 'airport_code']);

        $formatted = $airports->map(function($item) {
            return [
                'id' => $item->id,
                'airport_name' => $item->airport_name,
                'city_name' => $item->city_name,
                'airport_code' => $item->airport_code,
                'text' => "{$item->airport_name} ({$item->airport_code}) - {$item->city_name}"
            ];
        });

        return response()->json(['results' => $formatted]);
    }

    /**
     * Sync Airports from Travelopro
     */
    public function syncAirports()
    {
        $result = $this->traveloproService->syncAirports();
        return response()->json($result);
    }
}
