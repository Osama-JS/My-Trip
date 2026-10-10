<?php

namespace App\Http\Controllers\Admin\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\HotelBooking;
use App\Models\TripBooking;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the Admin v2 Modern Dashboard with live database queries.
     */
    public function index(Request $request)
    {
        // 1. Gather comprehensive live stats across all modules
        $flightBookings = Booking::count();
        $hotelBookings = HotelBooking::count();
        $tripBookings = TripBooking::count();
        
        $flightRevenue = (float) Booking::whereIn('status', ['confirmed', 'ticketed', 'completed', 'paid'])->sum('total_amount');
        $hotelRevenue = (float) HotelBooking::whereIn('status', ['confirmed', 'voucher_issued', 'completed', 'paid'])->sum('total_price');
        $tripRevenue = (float) TripBooking::where('booking_state', TripBooking::STATE_COMPLETED)->sum('total_price');
        $totalRevenue = $flightRevenue + $hotelRevenue + $tripRevenue;

        $stats = [
            'total_revenue' => $totalRevenue,
            'flight_count'  => $flightBookings,
            'hotel_count'   => $hotelBookings,
            'trip_count'    => $tripBookings,
            'users_count'   => User::count(),
            'flight_revenue'=> $flightRevenue,
            'hotel_revenue' => $hotelRevenue,
            'trip_revenue'  => $tripRevenue,
        ];

        // 2. Monthly Revenue for current year (Jan - Dec)
        $currentYear = date('Y');
        
        $monthlyFlights = Booking::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->whereYear('created_at', $currentYear)
            ->whereIn('status', ['confirmed', 'ticketed', 'completed', 'paid'])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyHotels = HotelBooking::selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
            ->whereYear('created_at', $currentYear)
            ->whereIn('status', ['confirmed', 'voucher_issued', 'completed', 'paid'])
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $flightSeries = [];
        $hotelSeries = [];
        for ($m = 1; $m <= 12; $m++) {
            $flightSeries[] = round((float)($monthlyFlights[$m] ?? 0), 2);
            $hotelSeries[] = round((float)($monthlyHotels[$m] ?? 0), 2);
        }

        // Donut Share by count of bookings
        $shareSeries = [$flightBookings, $hotelBookings, $tripBookings];

        // 3. Fetch recent bookings
        $recentFlightBookings = Booking::with('user')->latest()->take(10)->get();

        return view('admin_v2.dashboard.index', compact(
            'stats',
            'recentFlightBookings',
            'flightSeries',
            'hotelSeries',
            'shareSeries'
        ));
    }
}
