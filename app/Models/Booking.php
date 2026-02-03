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
}
