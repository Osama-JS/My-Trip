<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TripBooking;
use App\Models\Booking; // Flight bookings
use App\Models\HotelBooking;
use App\Models\Setting;

class CommissionController extends Controller
{
    public function index()
    {
        // 1. Fetch Global Margin Settings
        $flightMargin = floatval(Setting::get('flight_margin', 0));
        $flightMarginType = Setting::get('flight_margin_type', 'percentage');
        
        $hotelMargin = floatval(Setting::get('hotel_margin', 0));
        $hotelMarginType = Setting::get('hotel_margin_type', 'percentage');

        // 2. Calculate Flight Profits (Using successful bookings)
        // Using 'confirmed', 'paid', 'completed', 'ticketed' as successful states
        $flightBookings = Booking::with('user')->whereIn('status', ['confirmed', 'paid', 'completed', 'ticketed'])->get();
        $totalFlightProfit = 0;
        
        foreach ($flightBookings as $booking) {
            $totalAmount = floatval($booking->total_amount);
            if ($flightMarginType === 'fixed') {
                $profit = $flightMargin;
            } else {
                $net = $totalAmount / (1 + ($flightMargin / 100));
                $profit = $totalAmount - $net;
            }
            $booking->profit = round($profit, 2);
            $totalFlightProfit += $profit;
        }

        // 3. Calculate Hotel Profits
        $hotelBookings = HotelBooking::with('user')->whereIn('status', ['confirmed', 'paid', 'completed'])->get();
        $totalHotelProfit = 0;

        foreach ($hotelBookings as $booking) {
            $totalAmount = floatval($booking->total_price);
            if ($hotelMarginType === 'fixed') {
                $profit = $hotelMargin;
            } else {
                $net = $totalAmount / (1 + ($hotelMargin / 100));
                $profit = $totalAmount - $net;
            }
            $booking->profit = round($profit, 2);
            $totalHotelProfit += $profit;
        }

        // 4. Calculate Trip Profits (Platform commission from Company)
        $tripBookings = TripBooking::with(['trip.company', 'user'])
            ->whereIn('booking_state', [
                TripBooking::STATE_CONFIRMED, 
                TripBooking::STATE_ISSUING_TICKETS, 
                TripBooking::STATE_TICKETS_UPLOADED, 
                TripBooking::STATE_TICKETS_SENT, 
                TripBooking::STATE_COMPLETED
            ])->get();

        $totalTripProfit = 0;

        foreach ($tripBookings as $booking) {
            $totalAmount = floatval($booking->total_price);
            $commissionType = 'percentage';
            $commissionVal = 0;

            if ($booking->trip && $booking->trip->company) {
                $company = $booking->trip->company;
                $commissionType = $company->commission_type ?? 'percentage';
                $commissionVal = floatval($company->commission_value ?? $company->commission_rate ?? 0);
            }
            
            if ($commissionType === 'fixed') {
                $profit = $commissionVal;
            } else {
                $profit = $totalAmount * ($commissionVal / 100);
            }

            $booking->profit = round($profit, 2);
            $booking->commission_type = $commissionType;
            $booking->commission_value = $commissionVal;
            $totalTripProfit += $profit;
        }

        // 5. Total overall profit
        $totalOverallProfit = $totalFlightProfit + $totalHotelProfit + $totalTripProfit;

        return view('admin.commissions.index', compact(
            'flightBookings', 
            'hotelBookings', 
            'tripBookings',
            'totalFlightProfit',
            'totalHotelProfit',
            'totalTripProfit',
            'totalOverallProfit',
            'flightMargin',
            'flightMarginType',
            'hotelMargin',
            'hotelMarginType'
        ));
    }
}
