<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InsuranceQuote extends Model
{
    protected $fillable = [
        'user_id',
        'quote_reference',
        'external_quote_id',
        'booking_type',
        'destination_country',
        'departure_date',
        'return_date',
        'duration_days',
        'trip_cost',
        'passengers_count',
        'passengers_ages',
        'coverage_type',
        'net_cost',
        'selling_price',
        'platform_profit',
        'currency',
        'raw_quote_data',
        'expires_at',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'passengers_ages' => 'array',
        'raw_quote_data' => 'array',
        'expires_at' => 'datetime',
        'trip_cost' => 'decimal:2',
        'net_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'platform_profit' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function policy(): HasOne
    {
        return $this->hasOne(InsurancePolicy::class, 'insurance_quote_id');
    }
}
