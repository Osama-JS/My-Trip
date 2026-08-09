<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class HotelBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hotel_name',
        'hotel_id',
        'city_name',
        'country_name',
        'check_in',
        'check_out',
        'rooms',
        'adults',
        'childs',
        'total_price',
        'currency',
        'status',
        'reference_num',
        'supplier_confirmation_num',
        'cancellation_policy',
        'provider_price',
        'platform_profit',
        'session_id',
        'product_id',
        'token_id',
        'pax_details',
        'room_name',
        'board_type',
        'rate_basis_id',
        'payment_method',
        'invoice_path',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'pax_details' => 'array',
        'total_price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(BookingPassenger::class, 'hotel_booking_id');
    }
}

