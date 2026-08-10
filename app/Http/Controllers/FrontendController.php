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
            ->with([
                'images', 'fromCountry', 'toCountry', 'toCity', 'fromCity', 
                'company', 'rates.user', 'categories', 'itineraries',
                'seasons', 'packages.prices', 'addons'
            ])
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
     * Load More Hotels (Pagination AJAX)
     */
    public function hotelLoadMore(Request $request)
    {
        $data = $request->all();
        $results = $this->hotelService->nextToken($data);

        return response()->json([
            'html' => view('frontend.hotels.results_partial', [
                'results' => $results,
                'searchParams' => $data
            ])->render(),
            'nextToken' => $results['nextToken'] ?? null,
            'hasMore' => isset($results['moreResults']) && $results['moreResults']
        ]);
    }

    /**
     * Get Hotel Details Page.
     * Uses session-cached hotel data + tokenId to fetch room rates.
     */
    public function hotelDetails(Request $request, $hotelId)
    {
        $data = $request->all();
        $data['hotelId'] = $hotelId;

        $locale   = app()->getLocale();
        $langCode = ($locale === 'ar') ? 'ARA' : 'ENG';

        // 1. Hotel descriptive data from session (saved during search)
        $hotelMap         = session('hotel_search_results', []);
        $hotelFromSession = $hotelMap[$hotelId] ?? null;

        // 2. Build room-rates payload using tokenId/productId/sessionId from session
        $tokenId   = $hotelFromSession['tokenId']   ?? $data['tokenId']   ?? null;
        $productId = $hotelFromSession['productId'] ?? $data['productId'] ?? null;
        $sessionId = $hotelFromSession['sessionId'] ?? session('hotel_search_session_id') ?? null;

        $roomPayload = [
            'hotelId'          => $hotelId,
            'tokenId'          => $tokenId,
            'productId'        => $productId,
            'sessionId'        => $sessionId,
            'checkIn'          => $data['checkIn']  ?? null,
            'checkOut'         => $data['checkOut'] ?? null,
            'adults'           => $data['adults']   ?? 1,
            'childs'           => $data['childs']   ?? 0,
            'requiredLanguage' => $langCode,
            'requiredCurrency' => 'SAR',
        ];

        \Illuminate\Support\Facades\Log::info('Hotel Details - fetching room rates', $roomPayload);
        $roomsResult = $this->hotelService->getRoomRates($roomPayload);

        // 3. Fetch full hotel metadata (images, full description, amenities)
        $contentResult = $this->hotelService->getHotelContent($roomPayload);
        
        // Merge contentResult into hotel object
        $hotelDetails = $contentResult['hotelDetails'] ?? $contentResult['hotel'] ?? [];
        if ($hotelFromSession) {
            $hotelFromSession = array_merge($hotelFromSession, $hotelDetails);
            
            // Map 'images' or 'hotelImages' from API to normalized key if needed
            if (!empty($hotelDetails['hotelImages'])) {
                $hotelFromSession['hotelImages'] = $hotelDetails['hotelImages'];
            } elseif (!empty($hotelDetails['images'])) {
                // Normalize images array
                $imgs = [];
                foreach ($hotelDetails['images'] as $img) {
                    $imgs [] = ['url' => is_array($img) ? ($img['url'] ?? $img['Image'] ?? '') : $img];
                }
                $hotelFromSession['hotelImages'] = $imgs;
            }

            // Fallback: Merge roomImages from roomResults if they exist and are unique
            $roomResults = $roomsResult['roomRates']['perBookingRates'] ?? $roomsResult['roomRates']['RoomResults'] ?? [];
            foreach ($roomResults as $room) {
                if (!empty($room['roomImages'])) {
                    foreach ($room['roomImages'] as $rImg) {
                        $url = is_array($rImg) ? ($rImg['url'] ?? $rImg['Image'] ?? '') : $rImg;
                        if ($url && !collect($hotelFromSession['hotelImages'] ?? [])->contains('url', $url)) {
                            $hotelFromSession['hotelImages'][] = ['url' => $url];
                        }
                    }
                }
            }
        }

        \Illuminate\Support\Facades\Log::info('Hotel Details - Room Rates full response', [
            'response' => $roomsResult,
        ]);

        $checkIn = \Carbon\Carbon::parse($request->get('checkIn', now()));
        $checkOut = \Carbon\Carbon::parse($request->get('checkOut', now()->addDay()));
        $nights = max(1, $checkIn->diffInDays($checkOut));

        return view('frontend.hotels.show', [
            'hotel'        => $hotelFromSession ?? [],
            'rooms'        => $roomsResult,
            'searchParams' => $data,
            'nights'       => $nights
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

        $checkIn = \Carbon\Carbon::parse($request->get('checkIn', now()));
        $checkOut = \Carbon\Carbon::parse($request->get('checkOut', now()->addDay()));
        $nights = max(1, $checkIn->diffInDays($checkOut));

        return view('frontend.hotels.room_rates_partial', [
            'rooms' => $formatted,
            'hotelDetails' => $data,
            'nights' => $nights
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
        $q = $request->get('q', '');
        $isArabic = app()->getLocale() === 'ar';
        
        $cities = \App\Models\HotelCity::where('is_active', true)
            ->where(function($query) use ($q) {
                $query->where('city_name_en', 'like', "%{$q}%")
                      ->orWhere('city_name_ar', 'like', "%{$q}%")
                      ->orWhere('country_name_en', 'like', "%{$q}%")
                      ->orWhere('country_name_ar', 'like', "%{$q}%");
            })
            ->limit(50)
            ->get();
            
        $formatted = $cities->map(function ($city) use ($isArabic) {
            $name = ($isArabic && $city->city_name_ar) ? $city->city_name_ar : $city->city_name_en;
            $country = ($isArabic && $city->country_name_ar) ? $city->country_name_ar : $city->country_name_en;
            
            return [
                'id' => $city->city_name_en, // Value sent to search
                'text' => "{$name}, {$country}",
                'city_name' => $city->city_name_en,
                'city_name_ar' => $city->city_name_ar,
                'country_name' => $city->country_name_en,
                'country_name_ar' => $city->country_name_ar
            ];
        });

        return response()->json(['results' => $formatted]);
    }

    /**
     * Hotel Booking Form
     */
    public function hotelBookingForm(Request $request)
    {
        if (auth()->check() && !auth()->user()->isProfileComplete()) {
            session()->put('url.intended', url()->full());
            return redirect()->route('profile.complete.form');
        }

        $countries = \App\Models\Country::all();
        $details = $request->all();

        // Enrich with session data if hotel info is missing from URL params
        $hotelId = $details['hotelId'] ?? null;
        if ($hotelId) {
            $hotelMap  = session('hotel_search_results', []);
            $sessionH  = $hotelMap[$hotelId] ?? null;

            if ($sessionH) {
                $details['hotelName']   = $details['hotelName']   ?? $sessionH['name']    ?? $sessionH['hotelName'] ?? 'Hotel';
                $details['cityName']    = $details['cityName']    ?? $sessionH['city']    ?? $sessionH['address']   ?? '';
                $details['countryName'] = $details['countryName'] ?? $sessionH['country'] ?? '';
                $details['tokenId']     = $details['tokenId']     ?? $sessionH['tokenId']   ?? null;
                $details['productId']   = $details['productId']   ?? $sessionH['productId'] ?? null;
                $details['sessionId']   = $details['sessionId']   ?? $sessionH['sessionId'] ?? session('hotel_search_session_id');
            }
        }

        return view('frontend.hotels.booking', ['details' => $details]);
    }

    /**
     * Process Hotel Booking
     */
    public function processHotelBooking(Request $request)
    {
        // 1. Validate Input
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'hotelId' => 'required',
            'hotelName' => 'required',
            'checkIn' => 'required|date',
            'checkOut' => 'required|date',
            'total_amount' => 'required|numeric',
            'customerEmail' => 'required|email',
            'customerPhone' => 'required',
            'pax' => 'required|array',
        ], [
            'hotelId.required' => __('Hotel ID is missing.'),
            'pax.required' => __('Please fill all passenger details.'),
            'customerEmail.required' => __('Email is required.'),
            'customerPhone.required' => __('Phone number is required.'),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $rooms = (int) $request->get('rooms', 1);
        $paxInput = $request->get('pax', []);
        $paxDetails = [];

        for ($i = 1; $i <= $rooms; $i++) {
            $roomPax = [
                'room_no' => $i,
                'pax' => []
            ];

            // Capture all adults for this room
            $roomAdults = $paxInput[$i]['adult'] ?? [];
            foreach ($roomAdults as $adult) {
                if (empty($adult['firstName']) || empty($adult['lastName'])) {
                     return back()->with('error', __('Please enter first and last name for all adults in Room :n', ['n' => $i]))->withInput();
                }
                $roomPax['pax'][] = [
                    'type' => 'AD',
                    'Title' => $adult['title'] ?? 'Mr',
                    'FirstName' => $adult['firstName'] ?? '',
                    'LastName' => $adult['lastName'] ?? '',
                ];
            }

            // Capture all children for this room
            $roomChildren = $paxInput[$i]['child'] ?? [];
            foreach ($roomChildren as $child) {
                if (empty($child['firstName']) || empty($child['lastName'])) {
                     return back()->with('error', __('Please enter first and last name for all children in Room :n', ['n' => $i]))->withInput();
                }
                $roomPax['pax'][] = [
                    'type' => 'CH',
                    'Title' => $child['title'] ?? 'Mr',
                    'FirstName' => $child['firstName'] ?? '',
                    'LastName' => $child['lastName'] ?? '',
                    'Age' => (int) ($child['age'] ?? 0),
                ];
            }

            // Fallback if no pax provided for this room - though validation should handle this
            if (empty($roomPax['pax'])) {
                return back()->with('error', __('No passenger details found for Room :n', ['n' => $i]))->withInput();
            }

            $paxDetails[] = $roomPax;
        }

        $referenceNum = 'HTL-' . strtoupper(uniqid());

        $bookingData = [
            'sessionId'    => $request->get('sessionId'),
            'productId'    => $request->get('productId'),
            'tokenId'      => $request->get('tokenId'),
            'rateBasisId'  => $request->get('rateBasisId'),
            'clientRef'    => $referenceNum,
            'customerEmail' => $request->get('customerEmail'),
            'customerPhone' => $request->get('customerPhone'),
            'bookingNote'  => 'Hotel Booking - ' . $request->get('hotelName'),
            'paxDetails'   => $paxDetails,
        ];

        // ── ACTUAL FIX: DO NOT call hotel_book before payment ──
        // This avoids financial liability for abandoned or failed payments.
        // Confirmation will happen in HotelBookingFinalizer after successful payment.
        $supplierConfirmationNum = null;
        $initialStatus = 'pending';

        try {
            $hotelBooking = \App\Models\HotelBooking::create([
                'user_id'   => auth()->id(),
                'hotel_name'  => $request->get('hotelName', 'Hotel Booking'),
                'hotel_id'    => $request->get('hotelId', 'N/A'),
                'city_name'   => $request->get('cityName'),
                'country_name' => $request->get('countryName'),
                'check_in'    => $request->get('checkIn'),
                'check_out'   => $request->get('checkOut'),
                'rooms'       => $rooms,
                'adults'      => (int) $request->get('adults', 1),
                'childs'      => (int) $request->get('childs', 0),
                'total_price' => $request->get('total_amount', 0),
                'currency'    => $request->get('currency', 'SAR'),
                'status'      => $initialStatus,
                'reference_num' => $referenceNum,
                'supplier_confirmation_num' => $supplierConfirmationNum,
                'session_id'  => $request->get('sessionId'),
                'product_id'  => $request->get('productId'),
                'token_id'    => $request->get('tokenId'),
                'rate_basis_id' => $request->get('rateBasisId'),
                'pax_details' => $paxDetails,
                'room_name'   => $request->get('roomName'),
                'board_type'  => $request->get('boardType'),
            ]);

            // Save individual passengers
            foreach ($paxDetails as $room) {
                $roomPax = $room['pax'] ?? [];
                foreach ($roomPax as $pax) {
                    $type = (isset($pax['type']) && $pax['type'] == 'CH') ? 'child' : 'adult';
                    \App\Models\BookingPassenger::create([
                        'hotel_booking_id' => $hotelBooking->id,
                        'name'             => ($pax['Title'] ?? 'Mr') . ' ' . ($pax['FirstName'] ?? '') . ' ' . ($pax['LastName'] ?? ''),
                        'first_name'       => $pax['FirstName'] ?? '',
                        'last_name'        => $pax['LastName'] ?? '',
                        'title'            => $pax['Title'] ?? 'Mr',
                        'passenger_type'   => $type,
                    ]);
                }
            }

            return redirect()->route('hotels.payment.select', ['booking_id' => $hotelBooking->id]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Local Hotel Save Error: ' . $e->getMessage());
            return back()->with('error', __('Local save failed: :msg', ['msg' => $e->getMessage()]))->withInput();
        }
    }

    /**
     * Hotel Payment Method Selection Page
     */
    public function hotelSelectPayment(Request $request, $booking_id)
    {
        $booking = \App\Models\HotelBooking::where('id', $booking_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('frontend.hotels.payment_select', compact('booking'));
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
            'adults' => (int)$request->get('adults', 1),
            'childs' => (int)$request->get('childs', 0),
            'infants' => (int)$request->get('infants', 0),
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

        $itineraries = $results['AirSearchResponse']['AirSearchResult']['FareItineraries'] ?? [];
        // If it's a single object instead of array, wrap it
        if (!empty($itineraries) && !isset($itineraries[0])) {
            $itineraries = [$itineraries];
        }

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
        if (auth()->check() && !auth()->user()->isProfileComplete()) {
            session()->put('url.intended', url()->full());
            return redirect()->route('profile.complete.form');
        }

        $countries = \App\Models\Country::all();
        // Expecting flight details in session/request to show summary
        return view('frontend.flights.booking', [
            'details' => $request->all(),
            'countries' => $countries
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

        // Calculate Profit Margin
        $margin = floatval(\App\Models\Setting::get('flight_margin', 0));
        $marginType = \App\Models\Setting::get('flight_margin_type', 'percentage');
        $totalAmount = floatval($request->get('total_amount'));
        
        $profit = 0;
        $providerPrice = $totalAmount;
        if ($margin > 0) {
            if ($marginType === 'fixed') {
                $profit = $margin;
                $providerPrice = $totalAmount - $profit;
            } else {
                // total = base * (1 + margin/100) => base = total / (1 + margin/100)
                $providerPrice = $totalAmount / (1 + ($margin / 100));
                $profit = $totalAmount - $providerPrice;
            }
        }

        // 2. Persist in local DB (Booking model)
        try {
            $uniqueId = $result['AirBookingResponse']['AirBookingResult']['UniqueID'] ?? ('FLIGHT-' . strtoupper(uniqid()));
            $booking = \App\Models\Booking::create([
                'user_id' => auth()->id(),
                'booking_reference' => $uniqueId,
                'supplier_session_id' => $result['AirBookingResponse']['AirBookingResult']['SessionId'] ?? ($request->get('flight_session_id') ?? 'N/A'),
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'provider_price' => $providerPrice,
                'platform_profit' => $profit,
                'currency' => 'SAR',
                'contact_email' => $request->get('customerEmail'),
                'contact_phone' => $request->get('customerPhone'),
                'airline_code' => $request->get('airline') ?? ($result['AirBookingResponse']['AirBookingResult']['Itineraries']['Itinerary'][0]['ValidatingAirlineCode'] ?? null),
                'airline_name' => $request->get('airline') ?? ($result['AirBookingResponse']['AirBookingResult']['Itineraries']['Itinerary'][0]['ValidatingAirlineCode'] ?? null),
                'pnr_created_at' => now(),
                'ticketing_time_limit' => isset($result['AirBookingResponse']['AirBookingResult']['TicketingTimeLimit']) 
                    ? \Carbon\Carbon::parse($result['AirBookingResponse']['AirBookingResult']['TicketingTimeLimit']) 
                    : now()->addMinutes(3),
            ]);

            // Link API Log
            if (isset($result['_api_log_id'])) {
                \App\Models\FlightApiLog::where('id', $result['_api_log_id'])->update(['booking_id' => $booking->id]);
            }

            // 3. Save Flight Specific Details
            \App\Models\FlightBooking::create([
                'user_id' => auth()->id(),
                'booking_id' => $booking->id,
                'origin' => $request->get('from'),
                'destination' => $request->get('to'),
                'departure_date' => $request->get('departDate'),
                'return_date' => $request->get('returnDate'),
                'flight_number' => $request->get('flight_number'),
                'flight_class' => $request->get('class', 'Economy'),
                'adults' => (int)$request->get('adults', 1),
                'childs' => (int)$request->get('childs', 0),
                'infants' => (int)$request->get('infants', 0),
                'flight_class' => $request->get('class', 'Economy'),
                'itinerary_data' => $result['AirBookingResponse']['AirBookingResult'] ?? null,
                'total_amount' => $request->get('total_amount'),
                'currency' => 'SAR',
            ]);

            // 4. Save Passengers for detailed view and ticketing
            $passengersInput = $request->get('passengers', []);
            foreach ($passengersInput as $index => $pax) {
                $passportImagePath = null;
                if ($request->hasFile("passengers.{$index}.passport_image")) {
                    $file = $request->file("passengers.{$index}.passport_image");
                    $passportImagePath = $file->store('passports', 'public');
                }

                \App\Models\BookingPassenger::create([
                    'booking_id'      => $booking->id,
                    'hotel_booking_id' => null,
                    'passenger_type'  => $pax['type'] ?? 'adult',
                    'title'           => $pax['title'] ?? 'Mr',
                    'first_name'      => $pax['first_name'] ?? '',
                    'last_name'       => $pax['last_name'] ?? '',
                    'name'            => ($pax['title'] ?? 'Mr') . ' ' . ($pax['first_name'] ?? '') . ' ' . ($pax['last_name'] ?? ''),
                    'dob'             => $pax['dob'] ?? null,
                    'passport_number' => $pax['passport_no'] ?? null,
                    'nationality'      => $pax['nationality'] ?? null,
                    'passport_issue_country' => $pax['passport_issue_country'] ?? null,
                    'passport_expiry'   => $pax['passport_expiry_date'] ?? null,
                    'passport_image'   => $passportImagePath ?? ($pax['passport_image'] ?? null),
                ]);
            }

            // 5. Redirect to flight-specific payment selection page
            return redirect()->route('flights.payment.select', ['booking_id' => $booking->id]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Local Flight Save Error: ' . $e->getMessage());
            return back()->with('error', __('Booking saved on provider but failed locally. Contact support. Reference: ') . ($result['AirBookingResponse']['AirBookingResult']['Pnrs']['Pnr'] ?? 'N/A'));
        }
    }

    /**
     * Flight-specific Payment Selection Page
     */
    public function flightSelectPayment(Request $request, $booking_id)
    {
        $booking = \App\Models\FlightBooking::with('user')->where('booking_id', $booking_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('frontend.flights.payment_select', compact('booking'));
    }

    /**
     * Trip-specific Payment Selection Page
     */
    public function tripSelectPayment(Request $request, $booking_id)
    {
        $booking = \App\Models\TripBooking::with(['trip', 'user'])->where('id', $booking_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('frontend.trips.payment_select', compact('booking'));
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
     * Display dynamic page
     */
    public function showPage($slug)
    {
        $page = \App\Models\Page::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        return view('frontend.pages.show', compact('page'));
    }

    /**
     * Show passenger details form before booking a trip
     */
    public function tripBookingForm(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'tickets_count' => 'required|integer|min:1',
            'package_id' => 'nullable|exists:trip_packages,id',
            'season_id' => 'nullable|exists:trip_seasons,id',
            'occupancy_type' => 'nullable|in:single,double,triple,child',
            'booking_date' => 'nullable|date',
        ]);

        $trip = Trip::active()->with(['seasons', 'packages.prices'])->findOrFail($request->trip_id);
        
        $selectedPackage = $request->package_id ? \App\Models\TripPackage::find($request->package_id) : null;
        $selectedSeason = $request->season_id ? \App\Models\TripSeason::find($request->season_id) : null;

        $unitPrice = $trip->price;
        if ($selectedPackage && $selectedSeason && $request->occupancy_type) {
            $priceRecord = \App\Models\TripPackagePrice::where([
                'package_id' => $request->package_id,
                'season_id' => $request->season_id,
                'occupancy_type' => $request->occupancy_type
            ])->first();
            if ($priceRecord) {
                $unitPrice = $priceRecord->price;
            }
        }

        // Handle Add-ons
        $selectedAddons = [];
        if ($request->has('addons') && is_array($request->addons)) {
            $selectedAddons = \App\Models\TripAddon::whereIn('id', $request->addons)->get();
            foreach ($selectedAddons as $addon) {
                $unitPrice += $addon->extra_cost;
            }
        }

        return view('frontend.trips.booking', [
            'trip' => $trip,
            'tickets_count' => $request->tickets_count,
            'package_id' => $request->package_id,
            'season_id' => $request->season_id,
            'occupancy_type' => $request->occupancy_type,
            'selectedPackage' => $selectedPackage,
            'selectedSeason' => $selectedSeason,
            'selectedAddons' => $selectedAddons,
            'unitPrice' => $unitPrice,
            'booking_date' => $request->booking_date,
            'notes' => $request->notes
        ]);
    }

    /**
     * Book a trip (After passing through passenger form)
     */
    public function bookTrip(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'tickets_count' => 'required|integer|min:1',
            'package_id' => 'nullable|exists:trip_packages,id',
            'season_id' => 'nullable|exists:trip_seasons,id',
            'occupancy_type' => 'nullable|in:single,double,triple,child',
            'addons' => 'nullable|array',
            'addons.*' => 'exists:trip_addons,id',
            'passengers' => 'required|array|min:1',
            'passengers.*.first_name' => 'required|string|max:255',
            'passengers.*.last_name' => 'required|string|max:255',
            'passengers.*.title' => 'nullable|string|max:10',
            'passengers.*.dob' => 'nullable|date',
            'passengers.*.passport_issue_country' => 'nullable|string|max:50',
            'passengers.*.phone' => 'required|string|max:50',
            'passengers.*.nationality' => 'required|string|max:100',
            'passengers.*.passport_number' => 'required|string|max:100',
            'passengers.*.passport_expiry' => 'required|date',
            'passengers.*.passport_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $trip = Trip::active()->findOrFail($request->trip_id);

        // Price calculation logic
        $unitPrice = $trip->price; // Default to legacy price

        if ($request->package_id && $request->season_id && $request->occupancy_type) {
            // New Tiered Pricing
            $priceRecord = \App\Models\TripPackagePrice::where([
                'package_id' => $request->package_id,
                'season_id' => $request->season_id,
                'occupancy_type' => $request->occupancy_type
            ])->first();

            if ($priceRecord) {
                $unitPrice = $priceRecord->price;
            }
        }

        // Handle Add-ons Snapshot and Cost
        $addonsSnapshot = [];
        $addonsCostPerPax = 0;
        if ($request->has('addons') && is_array($request->addons)) {
            $selectedAddons = \App\Models\TripAddon::whereIn('id', $request->addons)->get();
            foreach ($selectedAddons as $addon) {
                $addonsCostPerPax += $addon->extra_cost;
                $addonsSnapshot[] = [
                    'id'    => $addon->id,
                    'name'  => $addon->name, // uses accessor
                    'price' => $addon->extra_cost,
                ];
            }
        }

        $totalPrice = ($unitPrice + $addonsCostPerPax) * $request->tickets_count;

        // Legacy Extra passenger pricing (Only if not using packages or as a fallback)
        if (!$request->package_id && $trip->base_capacity && $request->tickets_count > $trip->base_capacity && $trip->extra_passenger_price) {
            $extraPassengers = $request->tickets_count - $trip->base_capacity;
            $baseTotal = ($trip->price * $trip->base_capacity) + ($trip->extra_passenger_price * $extraPassengers);
            $totalPrice = $baseTotal + ($addonsCostPerPax * $request->tickets_count);
        }

        $booking = TripBooking::create([
            'user_id' => auth()->id(),
            'trip_id' => $trip->id,
            'package_id' => $request->package_id,
            'season_id' => $request->season_id,
            'occupancy' => $request->occupancy_type,
            'status' => 'pending',
            'booking_state' => TripBooking::STATE_RECEIVED,
            'total_price' => $totalPrice,
            'booking_date' => $request->booking_date ?? $trip->expiry_date ?? now()->addDay(),
            'tickets_count' => $request->tickets_count,
            'notes' => $request->notes,
            'addons' => $addonsSnapshot,
        ]);

        // Process Passengers
        foreach ($request->passengers as $index => $pax) {
            $passportImagePath = null;
            if ($request->hasFile("passengers.{$index}.passport_image")) {
                $file = $request->file("passengers.{$index}.passport_image");
                $passportImagePath = $file->store('passports', 'public');
            }

            $fullName = trim(($pax['first_name'] ?? '') . ' ' . ($pax['last_name'] ?? ''));

            \App\Models\BookingPassenger::create([
                'trip_booking_id' => $booking->id,
                'name' => $fullName,
                'first_name' => $pax['first_name'] ?? '',
                'last_name' => $pax['last_name'] ?? '',
                'title' => $pax['title'] ?? 'Mr',
                'dob' => $pax['dob'] ?? null,
                'passport_issue_country' => $pax['passport_issue_country'] ?? null,
                'phone' => $pax['phone'],
                'nationality' => $pax['nationality'],
                'passport_number' => $pax['passport_number'],
                'passport_expiry' => $pax['passport_expiry'],
                'passport_image' => $passportImagePath,
            ]);
        }

        // Redirect to the trip-specific payment selection page
        return redirect()->route('trips.payment.select', ['booking_id' => $booking->id])
            ->with('success', __('Booking created successfully! Please proceed with payment.'));
    }
        /**
     * Search Airports locally (for Select2)
     */
    public function searchAirports(Request $request)
    {
        $q = $request->get('q');
        $isArabic = app()->getLocale() === 'ar';
        
        $airports = Airport::where(function($query) use ($q) {
                $query->where('airport_name', 'like', "%{$q}%")
                    ->orWhere('airport_name_ar', 'like', "%{$q}%")
                    ->orWhere('airport_code', 'like', "%{$q}%")
                    ->orWhere('city_name', 'like', "%{$q}%")
                    ->orWhere('city_name_ar', 'like', "%{$q}%")
                    ->orWhere('country_name_ar', 'like', "%{$q}%");
            })
            ->orderBy('airport_name')
            ->limit(50)
            ->get();

        $formatted = $airports->map(function($item) use ($isArabic) {
            $name = ($isArabic && $item->airport_name_ar) ? $item->airport_name_ar : $item->airport_name;
            $city = ($isArabic && $item->city_name_ar) ? $item->city_name_ar : $item->city_name;
            
            return [
                'id' => $item->airport_code,
                'airport_name' => $item->airport_name,
                'airport_name_ar' => $item->airport_name_ar,
                'city_name' => $item->city_name,
                'city_name_ar' => $item->city_name_ar,
                'airport_code' => $item->airport_code,
                'text' => "{$name} ({$item->airport_code}) - {$city}"
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
