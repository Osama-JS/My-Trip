<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Country;
use App\Models\City;
use App\Models\Company;
use App\Models\Trip;
use App\Models\TripImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\TripItinerary;

class TripsController extends Controller
{
    public function index()
    {
        $trips = Trip::all();
        $companies = Company::all();
        $countries = Country::all();
        $cities = City::all();

        $stats = [
            'total' => Trip::count(),
            'active' => Trip::active()->count(),
            'inactive' => Trip::where('active', false)->count(),
            'expired' => Trip::where('expiry_date', '<', now()->toDateString())->count(),
        ];

        return view('admin.trips.index', compact('companies', 'countries', 'trips', 'stats','cities'));
    }

    public function itinerary(Trip $trip)
    {
        return view('admin.trips.itinerary', compact('trip'));
    }

    public function storeItinerary(Request $request, Trip $trip)
    {
        $request->validate([
            'day_number' => 'required|integer',
            'sort_order' => 'nullable|integer',
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $trip->itineraries()->create($request->all());

        return redirect()->back()->with('success', __('Itinerary added successfully'));
    }

    public function destroyItinerary(TripItinerary $itinerary)
    {
        $itinerary->delete();
        return redirect()->back()->with('success', __('Itinerary deleted successfully'));
    }

    public function updateItinerary(Request $request, TripItinerary $itinerary)
    {
        $request->validate([
            'day_number'  => 'required|integer',
            'title'       => 'required|string',
            'description' => 'nullable|string',
        ]);
        $itinerary->update($request->all());
        return response()->json(['success' => true, 'message' => __('Itinerary updated successfully')]);
    }

    public function reorderItinerary(Request $request)
    {
        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'exists:trip_itineraries,id',
        ]);
        foreach ($request->order as $index => $id) {
            TripItinerary::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        return response()->json(['success' => true, 'message' => __('Itinerary reordered successfully')]);
    }

    public function getData(Request $request)
    {
         $query = Trip::with(['company','fromCountry','toCountry', 'fromCity', 'toCity']);

         if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->from_country_id) {
            $query->where('from_country_id', $request->from_country_id);
        }

        if ($request->to_country_id) {
            $query->where('to_country_id', $request->to_country_id);
        }

        if ($request->expiry_date) {
           $query->whereDate('expiry_date', '>=', $request->expiry_date);
        }

        $trips = $query->latest()->get();

        return response()->json([
            'data' => $trips->map(function ($trip) {
                $isExpired = $trip->expiry_date && $trip->expiry_date < now()->format('Y-m-d');

                $actionButtons = '<div class="d-flex align-items-center gap-1">
                        <a href="'.route('admin.trips.edit', $trip->id).'" class="act-action-btn" title="'.__('Edit').'">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="'.route('admin.trips.itinerary', $trip->id).'" class="act-action-btn" style="color: #0ea5e9; background: rgba(14,165,233,0.1);" title="'.__('Itinerary').'">
                            <i class="fas fa-list-ul"></i>
                        </a>
                        <button class="act-action-btn" style="color: #64748b; background: rgba(100,116,139,0.1); border:none;" onclick="openImageUpload('.$trip->id.', \''.addslashes($trip->title).'\')" title="'.__('Upload Images').'">
                            <i class="fas fa-camera"></i>
                        </button>
                        <a href="'.route('admin.trips.stats', $trip->id).'" class="act-action-btn" style="color: #8b5cf6; background: rgba(139,92,246,0.1);" title="'.__('Statistics').'">
                            <i class="fas fa-chart-line"></i>
                        </a>
                        <a href="'.route('admin.trips.pricing', $trip->id).'" class="act-action-btn act-action-btn--gold" title="'.__('Pricing & Packages').'">
                            <i class="fas fa-tags"></i>
                        </a>';

                if ($isExpired) {
                    $actionButtons .= '
                        <button class="act-action-btn" style="color: #10b981; background: rgba(16,185,129,0.1); border:none;" onclick="renewTrip('.$trip->id.')" title="'.__('Renew Trip').'">
                            <i class="fas fa-sync-alt"></i>
                        </button>';
                } else {
                    $actionButtons .= '
                        <button class="act-action-btn" style="color: #f59e0b; background: rgba(245,158,11,0.1); border:none;" onclick="toggleTripStatus('.$trip->id.')" title="'.__('Toggle Status').'">
                            <i class="fas fa-ban"></i>
                        </button>';
                }

                $actionButtons .= '
                        <button class="act-action-btn" style="color: #ef4444; background: rgba(239,68,68,0.1); border:none;" onclick="deleteTrip('.$trip->id.')" title="'.__('Delete').'">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>';

                return [
                    'title_ar' => $trip->title_ar ?? '---',
                    'title_en' => $trip->title_en ?? '---',
                    'company' => $trip->company
                          ?  '<span>'. $trip->company->name .'</span>' : '...',
                    'fromCountry' => $trip->fromCountry
                          ?  '<span>'. $trip->fromCountry->name .'</span>' : '...',
                    'fromCity' => $trip->fromCity
                          ?  '<span>'. $trip->fromCity->name .'</span>' : '...',
                    'toCountry' => $trip->toCountry
                          ?  '<span>'. $trip->toCountry->name .'</span>' : '...',
                    'toCity' => $trip->toCity
                          ?  '<span>'. $trip->toCity->name .'</span>' : '...',
                    'price'    => $trip->price,
                    'expiry_date' => $trip->expiry_date,
                    'status'      => $isExpired
                                    ? '<span class="badge-state badge-state--default">' . __('Expired') . '</span>'
                                    : ($trip->active ? '<span class="badge-state badge-state--green">' . __('Active') . '</span>' : '<span class="badge-state badge-state--red">' . __('Inactive') . '</span>'),
                    'actions' => $actionButtons,
                ];
            })
        ]);
    }

    public function create()
    {
        $companies = Company::active()->get();
        $countries = Country::active()->get();
        $cities = collect(); // Load via AJAX to improve performance
        $categories = \App\Models\TripCategory::all();

        return view('admin.trips.create', compact('companies', 'countries', 'cities', 'categories'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title_ar'              => 'required|string|max:255',
            'title_en'              => 'required|string|max:255',
            'tickets'               => 'nullable|string',
            'description_ar'        => 'required|string',
            'description_en'        => 'required|string',
            'includes_ar'           => 'nullable|string',
            'includes_en'           => 'nullable|string',
            'excludes_ar'           => 'nullable|string',
            'excludes_en'           => 'nullable|string',
            'children_policy_ar'    => 'nullable|string',
            'children_policy_en'    => 'nullable|string',
            'company_id'            => 'required|exists:companies,id',
            'from_country_id'       => 'required|exists:countries,id',
            'from_city_id'          => 'required|exists:cities,id',
            'to_country_id'         => 'required|exists:countries,id',
            'to_city_id'            => 'required|exists:cities,id',
            'duration'              => 'nullable|string|max:100',
            'price'                 => 'nullable|numeric|min:0',  // now optional (packages may define price)
            'price_before_discount' => 'nullable|numeric|min:0',
            'expiry_date'           => 'nullable|date',
            'personnel_capacity'    => 'nullable|integer|min:1',
            'is_public'             => 'nullable|boolean',
            'is_featured'           => 'nullable|boolean',
            'base_capacity'         => 'nullable|integer|min:0',
            'extra_passenger_price' => 'nullable|numeric|min:0',
            'category_ids'          => 'nullable|array',
            'category_ids.*'        => 'exists:trip_categories,id',
            'is_ad'                 => 'nullable|boolean',
            'active'                => 'nullable|boolean',
        ]);

        // Handle checkboxes
        $data['is_public']   = $request->boolean('is_public');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_ad']       = $request->boolean('is_ad');
        $data['active']      = $request->boolean('active');

        // Admin ID
        $data['admin_id'] = auth()->id();

        // Calculate profit (only when legacy price is set)
        $data['profit'] = 0;
        if (!empty($data['price_before_discount']) && !empty($data['price'])) {
            $data['profit'] = max(0, $data['price_before_discount'] - $data['price']);
        }

        // Create Trip
        $trip = Trip::create($data);

        // Sync categories
        if (!empty($data['category_ids'])) {
            $trip->categories()->sync($data['category_ids']);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Trip created successfully'),
                'data'    => $trip
            ]);
        }

        return redirect()->route('admin.trips.index')->with('success', __('Trip created successfully'));
    }

    /**
     * Display the specified resource.
     */
      public function show(Trip $trip)
    {
        return response()->json([
            'success' => true,
            'Trip' => $trip->load('categories'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trip $trip)
    {
        $companies = Company::active()->get();
        $countries = Country::active()->get();
        // Load only cities strictly needed for the initial view
        $cities = City::whereIn('country_id', [$trip->from_country_id, $trip->to_country_id])->active()->get();
        $categories = \App\Models\TripCategory::all();

        return view('admin.trips.edit', compact('trip', 'companies', 'countries', 'cities', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, Trip $trip)
    {
        $data = $request->validate([
            'title_ar'                 => 'required|string|max:255',
            'title_en'                 => 'required|string|max:255',
            'tickets'                  => 'nullable|string',
            'description_ar'           => 'required|string',
            'description_en'           => 'required|string',
            'includes_ar'              => 'nullable|string',
            'includes_en'              => 'nullable|string',
            'excludes_ar'              => 'nullable|string',
            'excludes_en'              => 'nullable|string',
            'children_policy_ar'       => 'nullable|string',
            'children_policy_en'       => 'nullable|string',
            'company_id'               => 'required|exists:companies,id',
            'from_country_id'          => 'required|exists:countries,id',
            'from_city_id'             => 'required|exists:cities,id',
            'to_country_id'            => 'required|exists:countries,id',
            'to_city_id'               => 'required|exists:cities,id',
            'duration'                 => 'nullable|string|max:100',
            'price'                    => 'nullable|numeric|min:0',
            'price_before_discount'    => 'nullable|numeric|min:0',
            'expiry_date'              => 'nullable|date',
            'personnel_capacity'       => 'nullable|integer|min:1',
            'base_capacity'            => 'nullable|integer|min:0',
            'extra_passenger_price'    => 'nullable|numeric|min:0',
            'is_public'                => 'nullable|boolean',
            'is_featured'              => 'nullable|boolean',
            'is_ad'                    => 'nullable|boolean',
            'active'                   => 'nullable|boolean',
            'category_ids'             => 'nullable|array',
            'category_ids.*'           => 'exists:trip_categories,id',
        ]);

        // Checkbox handling
        $data['is_public']   = $request->boolean('is_public');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_ad']       = $request->boolean('is_ad');
        $data['active']      = $request->boolean('active');

        // Recalculate profit
        if (!empty($data['price_before_discount']) && !empty($data['price'])) {
            $data['profit'] = max(0, $data['price_before_discount'] - $data['price']);
            $data['percentage_profit_margin'] =
                $data['price_before_discount'] > 0
                    ? round(($data['profit'] / $data['price_before_discount']) * 100, 2)
                    : 0;
        } else {
            $data['profit'] = 0;
            $data['percentage_profit_margin'] = 0;
        }

        $trip->update($data);

        if ($request->has('category_ids')) {
            $trip->categories()->sync($request->category_ids);
        } else {
            $trip->categories()->detach();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Trip updated successfully'),
            ]);
        }

        return redirect()->route('admin.trips.index')->with('success', __('Trip updated successfully'));
    }
    public function toggleStatus(Trip $trip)
    {
        $trip->active = ! $trip->active;
        $trip->save();

        return response()->json([
            'success' => true,
            'message' => __('Trip status updated successfully'),
            'status'  => $trip->active ? 'Active' : 'Inactive'
        ]);
    }

    public function renew(Request $request, $id)
    {
        $request->validate([
            'expiry_date' => 'required|date|after:today',
        ]);

        $trip = Trip::findOrFail($id);
        $trip->update([
            'expiry_date' => $request->expiry_date,
            'active'      => true // إعادة تفعيلها تلقائياً عند التجديد
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Trip deleted successfully'),

        ]);
    }

    public function destroy(Trip $trip)
    {
        if ($trip->bookings()->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('Cannot delete trip because it has existing bookings and customer reservations.'),
            ], 422);
        }

        try {
            DB::transaction(function () use ($trip) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists('trips/' . $trip->id)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->deleteDirectory('trips/' . $trip->id);
                }

                $trip->images()->delete();
                $trip->itineraries()->delete();
                $trip->addons()->delete();
                foreach ($trip->packages as $pkg) {
                    $pkg->prices()->delete();
                    $pkg->delete();
                }
                foreach ($trip->seasons as $season) {
                    $season->prices()->delete();
                    $season->delete();
                }
                $trip->delete();
            });

            return response()->json([
                'success' => true,
                'message' => __('Trip deleted successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error occurred while deleting the trip: ') . $e->getMessage(),
            ], 500);
        }
    }

    public function imagestore(Request $request, $trip_id) // نمرر الـ ID مباشرة
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,jfif|max:5120',
        ]);

        try {
            return DB::transaction(function () use ($request, $trip_id) {

                if (!$request->hasFile('file')) {
                    throw new \Exception('File not found');
                }

                $file = $request->file('file');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // تخزين في مجلد خاص بكل رحلة
                $path = $file->storeAs('trips/' . $trip_id, $fileName, 'public');

                // حفظ السجل
                $newImage = TripImage::create([
                    'trip_id' => $trip_id,
                    'image_path' => $path,
                ]);

                return response()->json([
                    'success' => true,
                    'id' => $newImage->id, // نرجع ID السجل الجديد
                    'url' => asset('storage/' . $path),
                    'message' => __('Trip created successfully'),
                ], 201);
            });

        } catch (\Exception $e) {
            Log::error("__('The trip photo upload failed'){$trip_id}: " . $e->getMessage());
            return response()->json(['error' => __('An error occurred during processing')], 500);
        }
    }

    public function imagedestroy(TripImage $image)
    {
        try {


            return DB::transaction(function () use ($image) {

                $path = $image->image_path;
                $image->delete();

                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }

                return response()->json([
                    'success' => true,
                    'message' => __('Trip deleted successfully'),
                ]);
            });

        } catch (\Exception $e) {
            Log::error("__('Error while deleting the image') ID {$image->id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

   public function getImages($trip_id)
    {

        $images = TripImage::where('trip_id', $trip_id)->get();

        $data = $images->map(function ($image) {
            $path = storage_path('app/public/' . $image->image_path);
            return [
                'id'   => $image->id,
                'name' => basename($image->image_path),
                'size' => file_exists($path) ? filesize($path) : 0,
                'url'  => asset('storage/' . $image->image_path),
            ];
        });

        return response()->json($data);
    }

    /**
     * Display the pricing & packages management view for a trip.
     */
    public function pricing(Trip $trip)
    {
        $trip->load(['seasons', 'packages.prices', 'addons']);
        
        return view('admin.trips.pricing', compact('trip'));
    }

    public function stats(Trip $trip)
    {
        $trip->load([
            'company', 
            'fromCountry', 'fromCity', 
            'toCountry', 'toCity', 
            'categories',
            'itineraries',
            'bookings.user', 
            'bookings.passengers'
        ])->loadCount('pageVisits');
        
        $bookings = $trip->bookings;
        
        // Advanced Analytics
        $confirmedBookings = $bookings->where('status', 'confirmed');
        $occupiedSeats = $confirmedBookings->sum('tickets_count');
        $totalCapacity = (int) ($trip->personnel_capacity ?: 0);
        $remainingSeats = $totalCapacity > 0 ? max(0, $totalCapacity - $occupiedSeats) : __('Unlimited');
        $occupancyRate = $totalCapacity > 0 ? round(($occupiedSeats / $totalCapacity) * 100, 1) : 0;

        $stats = [
            'total_bookings' => $bookings->count(),
            'confirmed_bookings' => $confirmedBookings->count(),
            'cancelled_bookings' => $bookings->where('status', 'cancelled')->count(),
            'total_revenue' => $confirmedBookings->sum('total_price'),
            'total_passengers' => $bookings->sum(function($b) {
                return $b->passengers->count() ?: $b->tickets_count;
            }),
            'pending_revenue' => $bookings->where('status', 'pending')->sum('total_price'),
            'occupied_seats' => $occupiedSeats,
            'remaining_seats' => $remainingSeats,
            'occupancy_rate' => $occupancyRate,
            'page_views' => $trip->page_visits_count,
        ];

        $recentBookings = $trip->bookings()->latest()->with(['user', 'passengers'])->limit(20)->get();
        
        // Group bookings by date for a simple trend (last 30 days)
        $trends = $trip->bookings()
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.trips.stats', compact('trip', 'stats', 'recentBookings', 'trends'));
    }

}
