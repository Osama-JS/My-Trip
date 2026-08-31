<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsurancePolicy extends Model
{
    protected $fillable = [
        'user_id',
        'insurance_quote_id',
        'booking_id',
        'trip_booking_id',
        'hotel_booking_id',
        'booking_type',
        'policy_number',
        'external_policy_id',
        'certificate_number',
        'status',
        'coverage_type',
        'destination_country',
        'departure_date',
        'return_date',
        'duration_days',
        'insured_passengers',
        'net_cost',
        'selling_price',
        'platform_profit',
        'currency',
        'pdf_url',
        'pdf_path',
        'emergency_phone',
        'raw_policy_data',
        'issued_at',
        'cancelled_at',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'insured_passengers' => 'array',
        'raw_policy_data' => 'array',
        'issued_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'net_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'platform_profit' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(InsuranceQuote::class, 'insurance_quote_id');
    }

    public function flightBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function tripBooking(): BelongsTo
    {
        return $this->belongsTo(TripBooking::class, 'trip_booking_id');
    }

    public function hotelBooking(): BelongsTo
    {
        return $this->belongsTo(HotelBooking::class, 'hotel_booking_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(InsuranceApiLog::class, 'policy_id');
    }

    public function getDestinationCountryNameAttribute()
    {
        if (!$this->destination_country) return __('Global');
        $country = Country::where('iso', strtoupper($this->destination_country))->first();
        if ($country) {
            return app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en;
        }
        return strtoupper($this->destination_country);
    }

    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'active':
                return '<span class="badge bg-success" style="background:#10b981!important;">' . __('Active') . '</span>';
            case 'pending':
                return '<span class="badge bg-warning" style="background:#f59e0b!important;">' . __('Pending') . '</span>';
            case 'cancelled':
                return '<span class="badge bg-danger" style="background:#ef4444!important;">' . __('Cancelled') . '</span>';
            case 'expired':
                return '<span class="badge bg-secondary" style="background:#64748b!important;">' . __('Expired') . '</span>';
            default:
                return '<span class="badge bg-info">' . ucfirst($this->status) . '</span>';
        }
    }
}
