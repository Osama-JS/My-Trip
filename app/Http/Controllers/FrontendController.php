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

class FrontendController extends Controller
{
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
            $query->where(function($q) use ($search) {
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
            ->where(function($q) use ($query) {
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
}
