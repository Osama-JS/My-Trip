<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Company;
use App\Models\Country;
use App\Models\City;
use App\Models\TripCategory;
use App\Models\TripImage;
use App\Models\TripItinerary;
use App\Models\TripAddon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AgentTripController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Trip::where('company_id', $user->company_id);

        // Stats (unfiltered)
        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('active', true)->count(),
            'inactive' => (clone $query)->where('active', false)->count(),
            'expired' => (clone $query)->where('expiry_date', '<', now()->toDateString())->count(),
        ];

        // Apply Filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title_ar', 'like', '%' . $request->search . '%')
                  ->orWhere('title_en', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('country_id')) {
            $query->where('to_country_id', $request->country_id);
        }
        if ($request->filled('city_id')) {
            $query->where('to_city_id', $request->city_id);
        }
        if ($request->filled('status')) {
            $status = $request->status === 'active' ? true : false;
            $query->where('active', $status);
        }

        $trips = $query->with(['fromCountry', 'toCountry', 'company'])
            ->latest()
            ->paginate(10);

        $countries = Country::active()->get();
        $cities = City::active()->get();

        return view('frontend.agent.trips.index', compact('trips', 'stats', 'countries', 'cities'));
    }


    public function create()
    {
        $countries = Country::active()->get();
        $cities = City::active()->get();
        $categories = TripCategory::all();

        return view('frontend.agent.trips.create', compact('countries', 'cities', 'categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'title'                 => 'nullable|string|max:255',
            'title_ar'              => 'nullable|string|max:255',
            'title_en'              => 'nullable|string|max:255',
            'description'           => 'nullable|string',
            'description_ar'        => 'nullable|string',
            'description_en'        => 'nullable|string',
            'from_country_id'       => 'required|exists:countries,id',
            'from_city_id'          => 'required|exists:cities,id',
            'to_country_id'         => 'required|exists:countries,id',
            'to_city_id'            => 'required|exists:cities,id',
            'duration'              => 'nullable|string|max:100',
            'price'                 => 'required|numeric|min:0',
            'price_before_discount' => 'nullable|numeric|min:0',
            'expiry_date'           => 'nullable|date|after_or_equal:today',
            'personnel_capacity'    => 'nullable|integer|min:1',
            'tickets'               => 'nullable|string',
            'base_capacity'         => 'nullable|integer|min:0',
            'extra_passenger_price' => 'required|numeric|min:0',
            'is_public'             => 'nullable|boolean',
            'active'                => 'nullable|boolean',
            'category_ids'          => 'nullable|array',
            'category_ids.*'        => 'exists:trip_categories,id',
            'thumbnail'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,jfif|max:5120',
            'images'                => 'nullable|array',
            'images.*'              => 'image|mimes:jpeg,png,jpg,gif,webp,jfif|max:5120',
        ]);

        // Require at least one title and one description
        $titleAr = $request->filled('title_ar') ? $request->title_ar : ($request->filled('title') ? $request->title : $request->title_en);
        $titleEn = $request->filled('title_en') ? $request->title_en : $titleAr;

        $descAr = $request->filled('description_ar') ? $request->description_ar : ($request->filled('description') ? $request->description : $request->description_en);
        $descEn = $request->filled('description_en') ? $request->description_en : $descAr;

        if (empty($titleAr) && empty($titleEn)) {
            return back()->withErrors(['title_ar' => __('Please provide a trip title.')])->withInput();
        }
        if (empty($descAr) && empty($descEn)) {
            return back()->withErrors(['description_ar' => __('Please provide a trip description.')])->withInput();
        }

        $data['title_ar'] = $titleAr;
        $data['title_en'] = $titleEn ?? $titleAr;
        $data['description_ar'] = $descAr;
        $data['description_en'] = $descEn ?? $descAr;

        $data['extra_passenger_price'] = $request->filled('extra_passenger_price') ? (float) $request->extra_passenger_price : 0.00;
        $data['base_capacity'] = $request->filled('base_capacity') ? (int) $request->base_capacity : 1;

        $data['company_id'] = $user->company_id;
        $data['user_id']    = $user->id;

        // Checkbox handling
        $data['is_public']   = $request->boolean('is_public');
        $data['active']      = $request->has('active') ? $request->boolean('active') : true;

        // Default Admin fields for Agents
        $data['is_featured'] = false;
        $data['is_ad']       = false;

        // Clean arrays from fillable attributes
        unset($data['thumbnail'], $data['images'], $data['title'], $data['description']);

        $trip = Trip::create($data);

        if ($request->has('category_ids')) {
            $trip->categories()->sync($request->category_ids);
        }

        // Handle Thumbnail Upload
        if ($request->hasFile('thumbnail')) {
            try {
                $file = $request->file('thumbnail');
                $fileName = time() . '_thumb_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('trips/' . $trip->id, $fileName, 'public');

                TripImage::create([
                    'trip_id'    => $trip->id,
                    'image_path' => $path,
                ]);
            } catch (\Exception $e) {
                Log::error("Agent thumbnail upload failed for trip {$trip->id}: " . $e->getMessage());
            }
        }

        // Handle Additional Gallery Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                try {
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('trips/' . $trip->id, $fileName, 'public');

                    TripImage::create([
                        'trip_id'    => $trip->id,
                        'image_path' => $path,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Agent gallery upload failed for trip {$trip->id}: " . $e->getMessage());
                }
            }
        }

        return redirect()->route('agent.trips.index')->with('success', __('Trip created successfully'));
    }

    public function edit(Trip $trip)
    {
        $this->authorizeAgent($trip);

        $countries = Country::active()->get();
        $cities = City::active()->get();
        $categories = TripCategory::all();

        return view('frontend.agent.trips.edit', compact('trip', 'countries', 'cities', 'categories'));
    }

    public function update(Request $request, Trip $trip)
    {
        $this->authorizeAgent($trip);

        $data = $request->validate([
            'title'                 => 'nullable|string|max:255',
            'title_ar'              => 'nullable|string|max:255',
            'title_en'              => 'nullable|string|max:255',
            'description'           => 'nullable|string',
            'description_ar'        => 'nullable|string',
            'description_en'        => 'nullable|string',
            'from_country_id'       => 'required|exists:countries,id',
            'from_city_id'          => 'required|exists:cities,id',
            'to_country_id'         => 'required|exists:countries,id',
            'to_city_id'            => 'required|exists:cities,id',
            'duration'              => 'nullable|string|max:100',
            'price'                 => 'required|numeric|min:0',
            'price_before_discount' => 'nullable|numeric|min:0',
            'expiry_date'           => 'nullable|date',
            'personnel_capacity'    => 'nullable|integer|min:1',
            'tickets'               => 'nullable|string',
            'base_capacity'         => 'nullable|integer|min:0',
            'extra_passenger_price' => 'required|numeric|min:0',
            'is_public'             => 'nullable|boolean',
            'active'                => 'nullable|boolean',
            'category_ids'          => 'nullable|array',
            'category_ids.*'        => 'exists:trip_categories,id',
            'thumbnail'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,jfif|max:5120',
            'images'                => 'nullable|array',
            'images.*'              => 'image|mimes:jpeg,png,jpg,gif,webp,jfif|max:5120',
        ]);

        $titleAr = $request->filled('title_ar') ? $request->title_ar : ($request->filled('title') ? $request->title : $trip->title_ar);
        $titleEn = $request->filled('title_en') ? $request->title_en : ($request->filled('title') ? $request->title : ($titleAr ?? $trip->title_en));

        $descAr = $request->filled('description_ar') ? $request->description_ar : ($request->filled('description') ? $request->description : $trip->description_ar);
        $descEn = $request->filled('description_en') ? $request->description_en : ($request->filled('description') ? $request->description : ($descAr ?? $trip->description_en));

        $data['title_ar'] = $titleAr;
        $data['title_en'] = $titleEn ?? $titleAr;
        $data['description_ar'] = $descAr;
        $data['description_en'] = $descEn ?? $descAr;

        $data['extra_passenger_price'] = $request->filled('extra_passenger_price') ? (float) $request->extra_passenger_price : 0.00;
        $data['base_capacity'] = $request->filled('base_capacity') ? (int) $request->base_capacity : 1;

        // Checkbox handling
        $data['is_public']   = $request->boolean('is_public');
        $data['active']      = $request->boolean('active');

        unset($data['thumbnail'], $data['images'], $data['title'], $data['description']);

        $trip->update($data);

        if ($request->has('category_ids')) {
            $trip->categories()->sync($request->category_ids);
        }

        // Handle Thumbnail Upload if provided
        if ($request->hasFile('thumbnail')) {
            try {
                $file = $request->file('thumbnail');
                $fileName = time() . '_thumb_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('trips/' . $trip->id, $fileName, 'public');

                TripImage::create([
                    'trip_id'    => $trip->id,
                    'image_path' => $path,
                ]);
            } catch (\Exception $e) {
                Log::error("Agent thumbnail update upload failed for trip {$trip->id}: " . $e->getMessage());
            }
        }

        // Handle Additional Gallery Images if provided
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                try {
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('trips/' . $trip->id, $fileName, 'public');

                    TripImage::create([
                        'trip_id'    => $trip->id,
                        'image_path' => $path,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Agent gallery upload on update failed for trip {$trip->id}: " . $e->getMessage());
                }
            }
        }

        return redirect()->route('agent.trips.index')->with('success', __('Trip updated successfully'));
    }

    public function destroy(Trip $trip)
    {
        $this->authorizeAgent($trip);

        // Deletion Guard: Check for bookings
        if ($trip->bookings()->exists()) {
            return redirect()->route('agent.trips.index')->with('error', __('Cannot delete trip because it has existing bookings and customer reservations.'));
        }

        try {
            DB::transaction(function () use ($trip) {
                if (Storage::disk('public')->exists('trips/' . $trip->id)) {
                    Storage::disk('public')->deleteDirectory('trips/' . $trip->id);
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

            return redirect()->route('agent.trips.index')->with('success', __('Trip deleted successfully'));
        } catch (\Exception $e) {
            return redirect()->route('agent.trips.index')->with('error', __('Error deleting trip: ') . $e->getMessage());
        }
    }

    public function show(Trip $trip)
    {
        $this->authorizeAgent($trip);
        $trip->load(['images', 'itineraries' => function($q) {
            $q->orderBy('sort_order');
        }, 'addons', 'bookings.user', 'fromCountry', 'toCountry', 'fromCity', 'toCity', 'company']);

        return view('frontend.agent.trips.show', compact('trip'));
    }

    // ─── Image Management ────────────────────────────────────────

    public function imageStore(Request $request, $trip_id)
    {
        $trip = Trip::findOrFail($trip_id);
        $this->authorizeAgent($trip);

        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,jfif|max:5120',
        ]);

        try {
            return DB::transaction(function () use ($request, $trip_id) {
                $file = $request->file('file');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('trips/' . $trip_id, $fileName, 'public');

                $newImage = TripImage::create([
                    'trip_id'    => $trip_id,
                    'image_path' => $path,
                ]);

                return response()->json([
                    'success' => true,
                    'id'      => $newImage->id,
                    'url'     => asset('storage/' . $path),
                    'message' => __('Image uploaded successfully'),
                ], 201);
            });
        } catch (\Exception $e) {
            Log::error("Agent image upload failed for trip {$trip_id}: " . $e->getMessage());
            return response()->json(['error' => __('An error occurred during processing')], 500);
        }
    }

    public function imageDestroy(TripImage $image)
    {
        $this->authorizeAgent($image->trip);
        try {
            return DB::transaction(function () use ($image) {
                $path = $image->image_path;
                $image->delete();
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
                return response()->json(['success' => true, 'message' => __('Image deleted successfully')]);
            });
        } catch (\Exception $e) {
            Log::error("Agent image delete failed ID {$image->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('Sorry, an error occurred while trying to delete.')], 500);
        }
    }

    public function setPrimaryImage(Trip $trip, TripImage $image)
    {
        $this->authorizeAgent($trip);
        try {
            if ($image->trip_id !== $trip->id) {
                return response()->json([
                    'success' => false,
                    'message' => __('Image does not belong to this trip.')
                ], 403);
            }

            DB::transaction(function () use ($trip, $image) {
                TripImage::where('trip_id', $trip->id)->update(['is_primary' => false]);
                $image->update(['is_primary' => true]);
            });

            return response()->json([
                'success' => true,
                'message' => __('Image set as main cover successfully.'),
                'image_id' => $image->id,
                'url' => asset('storage/' . $image->image_path)
            ]);
        } catch (\Exception $e) {
            Log::error("Agent set primary image failed for trip {$trip->id}, image {$image->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while updating the primary image.')
            ], 500);
        }
    }

    public function getImages($trip_id)
    {
        $trip = Trip::findOrFail($trip_id);
        $this->authorizeAgent($trip);

        $images = TripImage::where('trip_id', $trip_id)->orderByDesc('is_primary')->orderBy('id')->get();
        $data = $images->map(function ($image) {
            $path = storage_path('app/public/' . $image->image_path);
            return [
                'id'         => $image->id,
                'name'       => basename($image->image_path),
                'size'       => file_exists($path) ? filesize($path) : 0,
                'url'        => asset('storage/' . $image->image_path),
                'is_primary' => (bool)$image->is_primary,
            ];
        });
        return response()->json($data);
    }

    // ─── Itinerary Management ─────────────────────────────────────

    public function storeItinerary(Request $request, Trip $trip)
    {
        $this->authorizeAgent($trip);
        $request->validate([
            'day_number'  => 'required|integer',
            'title'       => 'required|string',
            'description' => 'nullable|string',
        ]);
        $lastOrder = $trip->itineraries()->max('sort_order') ?? 0;
        $trip->itineraries()->create(array_merge($request->all(), ['sort_order' => $lastOrder + 1]));
        return redirect()->back()->with('success', __('Itinerary added successfully'));
    }

    public function updateItinerary(Request $request, TripItinerary $itinerary)
    {
        $this->authorizeAgent($itinerary->trip);
        $request->validate([
            'day_number'  => 'required|integer',
            'title'       => 'required|string',
            'description' => 'nullable|string',
        ]);
        $itinerary->update($request->all());
        return response()->json(['success' => true, 'message' => __('Itinerary updated successfully')]);
    }

    public function destroyItinerary(TripItinerary $itinerary)
    {
        $this->authorizeAgent($itinerary->trip);
        $itinerary->delete();
        return redirect()->back()->with('success', __('Itinerary deleted successfully'));
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

    // ─────────────────────────────────────────────────────────────

    // ─────────────────────────────────────────────────────────────
    // Add-ons
    // ─────────────────────────────────────────────────────────────
    public function storeAddon(Request $request, Trip $trip)
    {
        $this->authorizeAgent($trip);
        $data = $request->validate([
            'title_ar'     => 'required|string|max:255',
            'title_en'     => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'type'         => 'required|in:addition,replacement',
            'pricing_type' => 'required|in:per_person,fixed_per_booking',
        ]);

        try {
            $addon = $trip->addons()->create([
                'name_ar'        => $data['title_ar'],
                'name_en'        => $data['title_en'],
                'extra_cost'     => $data['price'],
                'currency'       => config('services.hyperpay.currency', 'SAR'),
                'is_replacement' => $data['type'] === 'replacement',
                'pricing_type'   => $data['pricing_type'],
            ]);
            return response()->json(['success' => true, 'message' => __('Add-on created successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateAddon(Request $request, TripAddon $addon)
    {
        $this->authorizeAgent($addon->trip);
        $data = $request->validate([
            'title_ar'     => 'required|string|max:255',
            'title_en'     => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'type'         => 'required|in:addition,replacement',
            'pricing_type' => 'required|in:per_person,fixed_per_booking',
        ]);

        try {
            $addon->update([
                'name_ar'        => $data['title_ar'],
                'name_en'        => $data['title_en'],
                'extra_cost'     => $data['price'],
                'is_replacement' => $data['type'] === 'replacement',
                'pricing_type'   => $data['pricing_type'],
            ]);
            return response()->json(['success' => true, 'message' => __('Add-on updated successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroyAddon(TripAddon $addon)
    {
        $this->authorizeAgent($addon->trip);
        $addon->delete();
        return response()->json(['success' => true, 'message' => __('Add-on deleted successfully')]);
    }

    // ─────────────────────────────────────────────────────────────
    // Pricing & Packages
    // ─────────────────────────────────────────────────────────────
    public function pricing(Trip $trip)
    {
        $this->authorizeAgent($trip);
        $trip->load(['seasons', 'packages.prices']);
        
        return view('frontend.agent.trips.pricing', compact('trip'));
    }

    // ─────────────────────────────────────────────────────────────

    protected function authorizeAgent(Trip $trip)
    {
        if ($trip->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
