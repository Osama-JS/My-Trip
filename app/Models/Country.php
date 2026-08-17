<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     */
    protected $table = 'countries';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name_ar',
        'name_en',
        'iso',
        'iso3',
        'numcode',
        'phonecode',
        'flag',
        'landmark_image',
        'active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'active' => 'boolean',
    ];


    /**
     * Get localized name based on current locale.
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' 
            ? ($this->name_ar ?? $this->name_en ?? '') 
            : ($this->name_en ?? $this->name_ar ?? '');
    }

    /**
     * Get the cities for this country.
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }


      /**
     * Scope a query to only include active countries.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Get flag URL.
     */
    public function getFlagUrlAttribute(): string
    {
        if ($this->flag) {
            return asset('storage/' . $this->flag);
        }
        return asset('images/flags/default.svg');
    }

     /**
     * Get landmark image URL.
     */
    public function getLandmarkImageUrlAttribute(): string
    {
        if ($this->landmark_image) {
            $imagePath = $this->landmark_image;
            
            // If it's already a full URL, return it
            if (Str::startsWith($imagePath, ['http://', 'https://'])) {
                return $imagePath;
            }

            // If it's an absolute file path, try to make it relative to storage
            $imagePath = str_replace('\\', '/', $imagePath);
            $storagePublicPath = str_replace('\\', '/', storage_path('app/public/'));
            $publicPath = str_replace('\\', '/', public_path('storage/'));
            
            $imagePath = str_replace($storagePublicPath, '', $imagePath);
            $imagePath = str_replace($publicPath, '', $imagePath);

            return asset('storage/' . ltrim($imagePath, '/'));
        }

        // Fallback to ISO-based image if available, otherwise placeholder
        if ($this->iso) {
            $path = 'images/destinations/' . strtolower($this->iso) . '.jpg';
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return asset('images/demo/destination-placeholder.svg');
    }

    /**
     * Get the tourist trips going to this country.
     */
    public function toTrips(): HasMany
    {
        return $this->hasMany(Trip::class, 'to_country_id');
    }
}
