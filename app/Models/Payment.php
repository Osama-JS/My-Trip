<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_booking_id', // Keep for backward compatibility/during transition if needed
        'payable_id',
        'payable_type',
        'user_id',
        'payment_gateway',
        'payment_method',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'raw_response',
        'invoice_path',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the parent payable model (TripBooking, HotelBooking, or Booking/FlightBooking).
     */
    public function payable()
    {
        return $this->morphTo();
    }

    public function booking()
    {
        return $this->belongsTo(TripBooking::class, 'trip_booking_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
