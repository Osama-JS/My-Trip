<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripAddon extends Model
{
    protected $fillable = [
        'trip_id',
        'name_ar',
        'name_en',
        'extra_cost',
        'currency',
        'is_replacement',
        'pricing_type',
    ];

    protected $casts = [
        'extra_cost'     => 'decimal:2',
        'is_replacement' => 'boolean',
    ];

    // ─── Relationships ──────────────────────────────

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    // ─── Accessors ────────────────────────────────

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar'
            ? ($this->name_ar ?? $this->name_en ?? '')
            : ($this->name_en ?? $this->name_ar ?? '');
    }

    public function getFormattedCostAttribute(): string
    {
        return number_format($this->extra_cost, 0) . ' ' . $this->currency;
    }
}
