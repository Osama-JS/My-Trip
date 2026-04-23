<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripBooking extends Model
{
    use HasFactory;

    // Booking states
    public const STATE_AWAITING_PAYMENT = 'awaiting_payment';
    public const STATE_RECEIVED = 'awaiting_payment';
    public const STATE_PREPARING = 'preparing';
    public const STATE_CONFIRMED = 'confirmed';
    public const STATE_ISSUING_TICKETS = 'issuing_tickets';
    public const STATE_TICKETS_SENT = 'tickets_uploaded';
    public const STATE_TICKETS_UPLOADED = 'tickets_uploaded';
    public const STATE_TICKETS_SENT = 'tickets_sent';
    public const STATE_COMPLETED = 'completed';
    public const STATE_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'trip_id',
        'package_id',
        'season_id',
        'occupancy',
        'status', // pending, confirmed, cancelled
        'total_price',
        'booking_date',
        'tickets_count',
        'notes',
        'cancellation_reason',
        'ticket_file_path',
        'booking_state',
        'addons',
    ];

    /**
     * Get the full URL to the uploaded ticket file.
     *
     * @return string|null
     */
    public function getTicketUrlAttribute()
    {
        return $this->ticket_file_path ? asset('storage/' . $this->ticket_file_path) : null;
    }

    protected $casts = [
        'booking_date' => 'date',
        'tickets_count' => 'integer',
        'total_price' => 'decimal:2',
        'addons' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function package()
    {
        return $this->belongsTo(TripPackage::class, 'package_id');
    }

    public function season()
    {
        return $this->belongsTo(TripSeason::class, 'season_id');
    }

    public function passengers()
    {
        return $this->hasMany(BookingPassenger::class);
    }

    /**
     * Get all of the booking's payments.
     */
    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function oldPayments()
    {
        return $this->hasMany(Payment::class, 'trip_booking_id');
    }

    public function bankTransfers()
    {
        return $this->hasMany(BankTransfer::class, 'trip_booking_id');
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable')->latest();
    }


    public function histories()
    {
        return $this->hasMany(BookingHistory::class, 'trip_booking_id');
    }
}
