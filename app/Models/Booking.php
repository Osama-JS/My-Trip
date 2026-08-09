<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'booking_reference',
        'supplier_session_id',
        'status',
        'ticket_status',
        'ticket_numbers',       // JSON array of eTicket numbers from Travelopro
        'pnr_code',             // Airline PNR code for the passenger
        'airline_name',         // e.g. "Saudia"
        'airline_code',         // e.g. "SV"
        'total_amount',
        'currency',
        'contact_email',
        'contact_phone',
        'pnr_created_at',
    ];

    protected $casts = [
        'pnr_created_at'  => 'datetime',
        'ticket_numbers'  => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(BookingPassenger::class);
    }

    public function flightApiLogs(): HasMany
    {
        return $this->hasMany(FlightApiLog::class);
    }

    /**
     * Get the flight booking associated with the booking.
     */
    public function flightBooking(): HasOne
    {
        return $this->hasOne(FlightBooking::class);
    }

    /**
     * Get all of the booking's payments.
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }
}


