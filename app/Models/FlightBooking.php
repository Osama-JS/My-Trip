<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlightBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_id',
        'origin',
        'destination',
        'departure_date',
        'return_date',
        'adults',
        'childs',
        'infants',
        'flight_class',
        'flight_number',
        'itinerary_data',
        'total_amount',
        'currency',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'itinerary_data' => 'array',
        'total_amount' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
