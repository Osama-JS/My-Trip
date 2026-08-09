<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripAddon;
use Illuminate\Http\Request;

class TripAddonController extends Controller
{
    /**
     * Store a new add-on for a trip.
     */
    public function store(Request $request, Trip $trip)
    {
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
            \Log::channel('single')->error('Addon store error: ' . $e->getMessage());
            file_put_contents(storage_path('logs/custom_error.log'), $e->getMessage() . "\n", FILE_APPEND);
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update an existing add-on.
     */
    public function update(Request $request, Trip $trip, TripAddon $addon)
    {
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
            \Log::channel('single')->error('Addon update error: ' . $e->getMessage());
            file_put_contents(storage_path('logs/custom_error.log'), $e->getMessage() . "\n", FILE_APPEND);
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete an add-on.
     */
    public function destroy(Trip $trip, TripAddon $addon)
    {
        $addon->delete();
        return response()->json(['success' => true, 'message' => __('Add-on deleted successfully')]);
    }
}
