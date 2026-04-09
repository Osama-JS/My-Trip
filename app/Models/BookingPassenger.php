<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPassenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_booking_id',
        'booking_id',
        'hotel_booking_id',
        'name',
        'first_name',
        'last_name',
        'title',
        'dob',
        'passenger_type',
        'phone',
        'passport_number',
        'passport_expiry',
        'nationality',
    ];



    protected $casts = [
        'passport_expiry' => 'date',
        'dob' => 'date',
    ];

    public function tripBooking()
    {
        return $this->belongsTo(TripBooking::class, 'trip_booking_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function hotelBooking()
    {
        return $this->belongsTo(HotelBooking::class, 'hotel_booking_id');
    }


}
