<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'booking_reference',
        'supplier_session_id',
        'status',
        'ticket_status',
        'total_amount',
        'currency',
        'contact_email',
        'contact_phone',
        'pnr_created_at',
    ];

    protected $casts = [
        'pnr_created_at' => 'datetime',
    ];

    public function user()

    {
        return $this->belongsTo(User::class);
    }

    public function passengers()
    {
        return $this->hasMany(BookingPassenger::class);
    }

    public function flightApiLogs()
    {
        return $this->hasMany(FlightApiLog::class);
    }

    /**
     * Get the flight booking associated with the booking.
     */
    public function flightBooking()
    {
        return $this->hasOne(FlightBooking::class);
    }

    /**
     * Get all of the booking's payments.
     */
    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }
}


