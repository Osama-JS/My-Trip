<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HotelBooking;
use App\Models\TripBooking;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        // 1. Search Bookings (Universal)
        $flightBookings = Booking::where('booking_reference', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($b) {
                return [
                    'type' => 'booking',
                    'category' => __('Flight Bookings'),
                    'title' => $b->booking_reference,
                    'url' => route('admin.bookings.show', $b->id),
                    'icon' => 'fa fa-plane',
                    'badge' => $b->status
                ];
            });

        $hotelBookings = HotelBooking::where('reference_num', 'LIKE', "%{$query}%")
            ->orWhere('hotel_name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($b) {
                return [
                    'type' => 'booking',
                    'category' => __('Hotel Bookings'),
                    'title' => $b->reference_num . ' - ' . $b->hotel_name,
                    'url' => route('admin.bookings.hotels.show_detail', $b->id),
                    'icon' => 'fa fa-hotel',
                    'badge' => $b->status
                ];
            });

        $tripBookings = TripBooking::where('id', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($b) {
                return [
                    'type' => 'booking',
                    'category' => __('Tour Bookings'),
                    'title' => '#' . $b->id . ' - ' . ($b->trip ? ($b->trip->title_ar ?? $b->trip->title_en ?? '') : ''),
                    'url' => route('admin.trip-bookings.show', $b->id),
                    'icon' => 'fa fa-map-marker-alt',
                    'badge' => $b->status
                ];
            });

        // 2. Search Subscribers/Users
        $users = User::where('first_name', 'LIKE', "%{$query}%")
            ->orWhere('last_name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($u) {
                return [
                    'type' => 'user',
                    'category' => __('Subscribers'),
                    'title' => $u->first_name . ' ' . $u->last_name,
                    'subtitle' => $u->email,
                    'url' => route('admin.users.activity', $u->id),
                    'icon' => 'fa fa-user',
                    'badge' => $u->user_type ?? ''
                ];
            });

        // 3. Search Tour Packages
        $trips = Trip::where('title_ar', 'LIKE', "%{$query}%")
            ->orWhere('title_en', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($t) {
                return [
                    'type' => 'trip',
                    'category' => __('Tour Packages'),
                    'title' => $t->title_ar ?: $t->title_en,
                    'url' => route('admin.trips.edit', $t->id),
                    'icon' => 'fa fa-suitcase',
                    'badge' => $t->active ? __('Active') : __('Inactive')
                ];
            });

        // Merge results
        $results = [
            'bookings' => $flightBookings->concat($hotelBookings)->concat($tripBookings),
            'users' => $users,
            'trips' => $trips,
        ];

        return response()->json($results);
    }
}
