<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripSeason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentTripSeasonController extends Controller
{
    /**
     * Store a new season for a trip.
     */
    public function store(Request $request, Trip $trip)
    {
        $this->authorizeAgent($trip);

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
    public function update(Request $request, TripSeason $season)
    {
        $trip = $season->trip;
        $this->authorizeAgent($trip);

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
    public function destroy(TripSeason $season)
    {
        $trip = $season->trip;
        $this->authorizeAgent($trip);

        $season->prices()->delete();
        $season->delete();

        return response()->json(['success' => true, 'message' => __('Season deleted successfully')]);
    }

    /**
     * Ensure the agent owns this trip.
     */
    protected function authorizeAgent(Trip $trip)
    {
        if ($trip->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
