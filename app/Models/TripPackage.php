<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripPackage extends Model
{
    protected $fillable = [
        'trip_id',
        'name_ar',
        'name_en',
        'hotel_name',
        'hotel_stars',
        'hotel_website',
        'tier',
        'sort_order',
    ];

    protected $casts = [
        'hotel_stars' => 'integer',
        'sort_order'  => 'integer',
    ];

    const TIER_LABELS = [
        'economy' => ['ar' => 'الاقتصادي', 'en' => 'Economy'],
        'gold'    => ['ar' => 'الذهبي',    'en' => 'Gold'],
        'vip'     => ['ar' => 'VIP',        'en' => 'VIP'],
    ];

    // ─── Relationships ──────────────────────────────

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function prices()
    {
        return $this->hasMany(TripPackagePrice::class, 'package_id');
    }

    public function seasonPrices(int $seasonId = null)
    {
        return $this->hasMany(TripPackagePrice::class, 'package_id')
                    ->when($seasonId, fn($q) => $q->where('season_id', $seasonId));
    }

    // ─── Accessors ────────────────────────────────

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar'
            ? ($this->name_ar ?? $this->name_en ?? '')
            : ($this->name_en ?? $this->name_ar ?? '');
    }

    public function getStarsHtmlAttribute(): string
    {
        return str_repeat('★', $this->hotel_stars)
             . str_repeat('☆', max(0, 5 - $this->hotel_stars));
    }

    /**
     * Get the minimum price across all occupancy types and seasons.
     */
    public function getLowestPriceAttribute(): ?float
    {
        return $this->prices()->min('price');
    }

    /**
     * Get prices grouped by season_id then occupancy_type.
     * Returns: [ season_id => [ occupancy_type => price ] ]
     */
    public function getPriceMatrixAttribute(): array
    {
        $matrix = [];
        foreach ($this->prices as $p) {
            $key = $p->season_id ?? 'default';
            $matrix[$key][$p->occupancy_type] = $p->price;
        }
        return $matrix;
    }
}
