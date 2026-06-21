<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use App\Models\Booking;
use App\Models\HotelBooking;
use App\Models\Favorite;
use App\Models\Payment;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
   public function index()
    {
        $user = Auth::user();

        // Wallet Balance
        $walletService = app(WalletService::class);
        $wallet = $walletService->getOrCreateWallet($user->id);
        $walletBalance = $wallet->balance;

        // Booking stats (Trips)
        $totalTripBookings     = TripBooking::where('user_id', $user->id)->count();
        $pendingTripBookings   = TripBooking::where('user_id', $user->id)->where('status', 'pending')->count();
        $confirmedTripBookings = TripBooking::where('user_id', $user->id)->where('status', 'confirmed')->count();

        // Booking stats (Flights)
        $totalFlightBookings     = Booking::where('user_id', $user->id)->count();
        $pendingFlightBookings   = Booking::where('user_id', $user->id)->where('status', 'pending')->count();
        $confirmedFlightBookings = Booking::where('user_id', $user->id)->where('status', 'confirmed')->count();

        // Booking stats (Hotels)
        $totalHotelBookings     = HotelBooking::where('user_id', $user->id)->count();
        $pendingHotelBookings   = HotelBooking::where('user_id', $user->id)->where('status', 'pending')->count();
        $confirmedHotelBookings = HotelBooking::where('user_id', $user->id)->where('status', 'confirmed')->count();

        $totalBookings     = $totalTripBookings + $totalFlightBookings + $totalHotelBookings;
        $pendingBookings   = $pendingTripBookings + $pendingFlightBookings + $pendingHotelBookings;
        $confirmedBookings = $confirmedTripBookings + $confirmedFlightBookings + $confirmedHotelBookings;

        // Favorites count
        $favoritesCount = Favorite::where('user_id', $user->id)->count();

        // Stats array for Blade
        $stats = [
            [
                'label' => __('Total Bookings'),
                'value' => $totalBookings,
                'icon'  => 'fas fa-ticket-alt',
                'color' => 'stat-icon-blue',
            ],
            [
                'label' => __('Pending'),
                'value' => $pendingBookings,
                'icon'  => 'fas fa-clock',
                'color' => 'stat-icon-orange',
            ],
            [
                'label' => __('Confirmed'),
                'value' => $confirmedBookings,
                'icon'  => 'fas fa-check-circle',
                'color' => 'stat-icon-green',
            ],
            [
                'label' => __('Favorites'),
                'value' => $favoritesCount,
                'icon'  => 'fas fa-heart',
                'color' => 'stat-icon-purple',
            ],
        ];

        // Upcoming bookings (closest 3 confirmed/pending)
        $upcomingTrips = TripBooking::with(['trip.images'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->latest()
            ->limit(3)
            ->get();

        $upcomingFlights = Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->latest()
            ->limit(3)
            ->get();

        $upcomingHotels = HotelBooking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->latest()
            ->limit(3)
            ->get();

        $upcomingBookings = $upcomingTrips->merge($upcomingFlights)->merge($upcomingHotels)->sortByDesc('created_at')->take(3);

        // Recent payments (direct query via user_id, eager loading payable)
        $recentPayments = Payment::where('user_id', $user->id)
            ->with(['payable'])
            ->latest()
            ->limit(3)
            ->get();

        return view('frontend.customer.dashboard', compact(
            'stats',
            'upcomingBookings',
            'recentPayments',
            'walletBalance'
        ));
    }
}
