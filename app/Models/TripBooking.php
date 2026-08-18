<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class TripBooking extends Model
{
    use HasFactory;

    // Booking states
    public const STATE_AWAITING_PAYMENT = 'awaiting_payment';
    public const STATE_RECEIVED = 'awaiting_payment';
    public const STATE_PREPARING = 'preparing';
    public const STATE_CONFIRMED = 'confirmed';
    public const STATE_ISSUING_TICKETS = 'issuing_tickets';
    public const STATE_TICKETS_UPLOADED = 'tickets_uploaded';
    public const STATE_TICKETS_SENT = 'tickets_sent';
    public const STATE_COMPLETED = 'completed';
    public const STATE_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'trip_id',
        'company_id',
        'package_id',
        'season_id',
        'occupancy',
        'status', // pending, confirmed, cancelled
        'total_price',
        'commission_type',
        'commission_value',
        'platform_profit',
        'provider_price',
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
        'commission_value' => 'decimal:2',
        'platform_profit' => 'decimal:2',
        'provider_price' => 'decimal:2',
        'addons' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(TripPackage::class, 'package_id');
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(TripSeason::class, 'season_id');
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(BookingPassenger::class);
    }

    /**
     * Get all of the booking's payments.
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function oldPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'trip_booking_id');
    }

    public function bankTransfers(): HasMany
    {
        return $this->hasMany(BankTransfer::class, 'trip_booking_id');
    }

    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable')->latest();
    }


    public function histories(): HasMany
    {
        return $this->hasMany(BookingHistory::class, 'trip_booking_id');
    }

    /**
     * Check if the booking can be deleted by admin (unconfirmed and unpaid).
     */
    public function canBeDeletedByAdmin(): bool
    {
        // Cannot delete if status is confirmed
        if ($this->status === 'confirmed') {
            return false;
        }

        // Cannot delete if in confirmed/issued states
        $confirmedStates = [
            self::STATE_CONFIRMED,
            self::STATE_PREPARING,
            self::STATE_ISSUING_TICKETS,
            self::STATE_TICKETS_UPLOADED,
            self::STATE_TICKETS_SENT,
            self::STATE_COMPLETED,
        ];
        if (in_array($this->booking_state, $confirmedStates)) {
            return false;
        }

        // Cannot delete if there is a successful/approved payment
        $hasPaidPayment = $this->payments()
            ->where(function ($q) {
                $q->whereIn('status', ['paid', 'completed', 'success'])
                  ->orWhereIn('payment_status', ['paid', 'completed', 'success']);
            })
            ->exists();

        if ($hasPaidPayment) {
            return false;
        }

        $hasApprovedTransfer = $this->bankTransfers()
            ->where('status', 'approved')
            ->exists();

        if ($hasApprovedTransfer) {
            return false;
        }

        return true;
    }
}
