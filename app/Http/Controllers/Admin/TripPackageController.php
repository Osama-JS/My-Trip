<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripPackage;
use App\Models\TripPackagePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripPackageController extends Controller
{
    /**
     * Store a new package for a trip.
     */
    public function store(Request $request, Trip $trip)
    {
        $data = $request->validate([
            'name_ar'     => 'required|string|max:255',
            'name_en'     => 'required|string|max:255',
            'hotel_name'  => 'nullable|string',
            'hotel_stars' => 'required|integer|between:1,5',
            'hotel_website' => 'nullable|url|max:500',
            'tier'        => 'required|in:economy,gold,vip',
            'sort_order'  => 'nullable|integer',
            // Nested prices array: prices[season_id|default][occupancy_type] = price
            'prices'      => 'nullable|array',
        ]);

        try {
            DB::transaction(function () use ($data, $trip, $request) {
                $package = $trip->packages()->create([
                    'name_ar'     => $data['name_ar'],
                    'name_en'     => $data['name_en'],
                    'hotel_name'  => $data['hotel_name'] ?? null,
                    'hotel_stars' => $data['hotel_stars'],
                    'hotel_website' => $data['hotel_website'] ?? null,
                    'tier'        => $data['tier'],
                    'sort_order'  => $data['sort_order'] ?? 0,
                ]);

                // Save prices matrix
                $this->syncPrices($package, $request->input('prices', []));
            });

            return response()->json(['success' => true, 'message' => __('Package created successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update an existing package.
     */
    public function update(Request $request, Trip $trip, TripPackage $package)
    {
        // If it's a partial price update from the grid
        if ($request->has('prices') && !$request->has('tier')) {
            $this->syncPrices($package, $request->input('prices', []), true);
            return response()->json(['success' => true, 'message' => __('Price updated successfully')]);
        }

        $data = $request->validate([
            'name_ar'     => 'required|string|max:255',
            'name_en'     => 'required|string|max:255',
            'hotel_name'  => 'nullable|string',
            'hotel_stars' => 'required|integer|between:0,5',
            'hotel_website' => 'nullable|url|max:500',
            'tier'        => 'required|in:economy,gold,vip',
            'sort_order'  => 'nullable|integer',
            'prices'      => 'nullable|array',
        ]);

        try {
            DB::transaction(function () use ($data, $package, $request) {
                $package->update([
                    'name_ar'     => $data['name_ar'],
                    'name_en'     => $data['name_en'],
                    'hotel_name'  => $data['hotel_name'] ?? null,
                    'hotel_stars' => $data['hotel_stars'],
                    'hotel_website' => $data['hotel_website'] ?? null,
                    'tier'        => $data['tier'],
                    'sort_order'  => $data['sort_order'] ?? $package->sort_order,
                ]);

                // Full sync for modal update
                $this->syncPrices($package, $request->input('prices', []), false);
            });

            return response()->json(['success' => true, 'message' => __('Package updated successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a package.
     */
    public function destroy(Trip $trip, TripPackage $package)
    {
        // Guard: Check for existing bookings with this package
        if (\App\Models\TripBooking::where('package_id', $package->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('Cannot delete this package because there are active customer bookings linked to it.'),
            ], 422);
        }

        $package->prices()->delete();
        $package->delete();

        return response()->json(['success' => true, 'message' => __('Package deleted successfully')]);
    }

    /**
     * Sync prices for a package from the submitted matrix.
     * Input format: [ 'season_id|default' => [ 'occupancy_type' => price ] ]
     */
    private function syncPrices(TripPackage $package, array $pricesMatrix, bool $isPartial = false): void
    {
        if (!$isPartial) {
            $package->prices()->delete();
        }

        $validOccupancies = ['single', 'double', 'triple', 'quadruple', 'quintuple'];

        foreach ($pricesMatrix as $seasonKey => $occupancies) {
            $seasonId = ($seasonKey === 'default' || !is_numeric($seasonKey)) ? null : (int) $seasonKey;

            foreach ($occupancies as $occupancyType => $price) {
                if (!in_array($occupancyType, $validOccupancies)) continue;
                
                if ($price === null || $price === '') {
                    if ($isPartial) {
                        $package->prices()->where('season_id', $seasonId)->where('occupancy_type', $occupancyType)->delete();
                    }
                    continue;
                }

                $package->prices()->updateOrCreate(
                    [
                        'season_id' => $seasonId,
                        'occupancy_type' => $occupancyType,
                    ],
                    [
                        'price' => (float) $price,
                    ]
                );
            }
        }
    }
}
