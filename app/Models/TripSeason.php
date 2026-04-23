<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripSeason extends Model
{
    protected $fillable = [
        'trip_id',
        'name_ar',
        'name_en',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    // ─── Relationships ──────────────────────────────

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function prices()
    {
        return $this->hasMany(TripPackagePrice::class, 'season_id');
    }

    // ─── Accessors ────────────────────────────────

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar'
            ? ($this->name_ar ?? $this->name_en ?? '')
            : ($this->name_en ?? $this->name_ar ?? '');
    }

    public function getLabelAttribute(): string
    {
        $start = $this->start_date?->format('Y-m-d') ?? '';
        $end   = $this->end_date?->format('Y-m-d')   ?? '';
        $name  = $this->name ?? '';
        return $name ? "{$name} ({$start} → {$end})" : "{$start} → {$end}";
    }

    // ─── Scopes ───────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }
}
