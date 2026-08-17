<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripSeason;
use Illuminate\Http\Request;

class TripSeasonController extends Controller
{
    /**
     * Store a new season for a trip.
     */
    public function store(Request $request, Trip $trip)
    {
        $data = $request->validate([
            'name_ar'    => 'nullable|string|max:255',
            'name_en'    => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'is_active'  => 'nullable|boolean',
        ]);

        $season = $trip->seasons()->create([
            'name_ar'    => $data['name_ar'] ?? null,
            'name_en'    => $data['name_en'] ?? null,
            'start_date' => $data['start_date'],
            'end_date'   => $data['end_date'],
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Season created successfully'),
            'season'  => $season,
        ]);
    }

    /**
     * Update an existing season.
     */
    public function update(Request $request, Trip $trip, TripSeason $season)
    {
        $data = $request->validate([
            'name_ar'    => 'nullable|string|max:255',
            'name_en'    => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'is_active'  => 'nullable|boolean',
        ]);

        $season->update([
            'name_ar'    => $data['name_ar'] ?? null,
            'name_en'    => $data['name_en'] ?? null,
            'start_date' => $data['start_date'],
            'end_date'   => $data['end_date'],
            'is_active'  => $request->boolean('is_active', $season->is_active),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Season updated successfully'),
            'season'  => $season->fresh(),
        ]);
    }

    /**
     * Delete a season and all its associated prices.
     */
    public function destroy(Trip $trip, TripSeason $season)
    {
        // Guard: Check for existing bookings with this season
        if (\App\Models\TripBooking::where('season_id', $season->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('Cannot delete this season because there are active customer bookings linked to it.'),
            ], 422);
        }

        $season->prices()->delete();
        $season->delete();

        return response()->json(['success' => true, 'message' => __('Season deleted successfully')]);
    }
}
