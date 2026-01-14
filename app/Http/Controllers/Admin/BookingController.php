<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // Flights
    public function availableFlights()
    {
        return view('admin.bookings.flights.available');
    }

    public function flightRequests()
    {
        return view('admin.bookings.flights.requests');
    }

    public function ongoingFlights()
    {
        return view('admin.bookings.flights.ongoing');
    }

    // Hotels
    public function hotelList()
    {
        return view('admin.bookings.hotels.index');
    }

    public function hotelRequests()
    {
        return view('admin.bookings.hotels.requests');
    }
}
