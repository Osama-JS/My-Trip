<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripPackagePrice extends Model
{
    protected $fillable = [
        'package_id',
        'season_id',
        'occupancy_type',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    const OCCUPANCY_LABELS = [
        'single' => ['ar' => 'غرفة مفردة', 'en' => 'Single Room'],
        'double' => ['ar' => 'غرفة مزدوجة', 'en' => 'Double Room'],
        'triple' => ['ar' => 'غرفة ثلاثية', 'en' => 'Triple Room'],
        'child'  => ['ar' => 'طفل',         'en' => 'Child'],
    ];

    // ─── Relationships ──────────────────────────────

    public function package()
    {
        return $this->belongsTo(TripPackage::class, 'package_id');
    }

    public function season()
    {
        return $this->belongsTo(TripSeason::class, 'season_id');
    }

    // ─── Accessors ────────────────────────────────

    public function getOccupancyLabelAttribute(): string
    {
        $locale = app()->getLocale();
        return self::OCCUPANCY_LABELS[$this->occupancy_type][$locale]
            ?? self::OCCUPANCY_LABELS[$this->occupancy_type]['en']
            ?? $this->occupancy_type;
    }
}
