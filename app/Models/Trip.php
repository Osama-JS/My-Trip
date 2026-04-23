<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Trip extends Model
{
   use SoftDeletes;

    protected $fillable = [
       'title_ar',
       'title_en',
       'tickets',
       'description_ar',
       'description_en',
       'includes_ar',
       'includes_en',
       'excludes_ar',
       'excludes_en',
       'children_policy_ar',
       'children_policy_en',
       'is_public',
       'company_id',
       'user_id',
       'is_ad',
       'duration',
       'price',
       'price_before_discount',
       'expiry_date',
       'personnel_capacity',
       'from_country_id',
       'from_city_id',
       'to_country_id',
       'to_city_id',
       'admin_id',
       'profit',
       'percentage_profit_margin',
       'active',
       'page_visits',
       'base_capacity',
       'extra_passenger_price',
       'is_featured',
    ];

    protected $casts = [
        'active' => 'boolean',
        'expiry_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function fromCountry() {
        return $this->belongsTo(Country::class, 'from_country_id');
    }

    public function toCountry() {
        return $this->belongsTo(Country::class, 'to_country_id');
    }

    public function toCity() {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    public function fromCity() {
        return $this->belongsTo(City::class, 'from_city_id');
    }

     /**
     * Get the user (agent/admin) who created the trip.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDeactivateExpired($query)
    {
        return $query->where('expiry_date', '<', now())
                    ->where('active', true)
                    ->update(['active' => false]);
    }

    /**
     * Get the trip images.
     */
    public function images()
    {
        return $this->hasMany(TripImage::class);
    }

    public function banner()
    {
        return $this->hasMany(Banner::class, 'trip_id');
    }

    /**
     * Get the trip rates/reviews.
     */
    public function rates()
    {
        return $this->hasMany(TripRate::class);
    }

    public function itineraries()
    {
        return $this->hasMany(TripItinerary::class)->orderBy('day_number');
    }

    public function categories()
    {
        return $this->belongsToMany(TripCategory::class, 'trip_category_trip');
    }

     public function bookings()
    {
        return $this->hasMany(TripBooking::class, 'trip_id');
    }

    // ─── New Package System ────────────────────────────────────────

    public function seasons()
    {
        return $this->hasMany(TripSeason::class)->orderBy('start_date');
    }

    public function packages()
    {
        return $this->hasMany(TripPackage::class)->orderBy('sort_order');
    }

    public function addons()
    {
        return $this->hasMany(TripAddon::class);
    }


    /**
     * Get the trip page visits.
     */
    public function pageVisits()
    {
        return $this->hasMany(TripPageVisit::class);
    }

    /**
     * Get image URL.
     */
    public function getImageUrlAttribute()
    {
        $image = $this->images()->first();
        if ($image && $image->image_path) {
            if (Str::startsWith($image->image_path, ['http://', 'https://'])) {
                return $image->image_path;
            }
            return asset('storage/' . $image->image_path);
        }
        return asset('images/trip-placeholder.png');
    }

    /**
     * Get the title based on current locale.
     */
    public function getTitleAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"title_{$locale}"} ?? $this->title_en ?? $this->title_ar;
    }

    /**
     * Get the description based on current locale.
     */
    public function getDescriptionAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"description_{$locale}"} ?? $this->description_en ?? $this->description_ar;
    }

    /**
     * Scope for active trips
     */
    public function scopeActive($query)
    {
        return $query->where('active', true)
                     ->where('is_public', true)
                     ->where(function ($q) {
                         $q->whereNull('expiry_date')
                           ->orWhere('expiry_date', '>=', now()->toDateString());
                     });
    }

    /**
     * Localized Title Accessor
     */
    public function getTitleAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->title_ar ?? $this->title_en) : ($this->title_en ?? $this->title_ar);
    }

    /**
     * Localized Description Accessor
     */
    public function getDescriptionAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->description_ar ?? $this->description_en) : ($this->description_en ?? $this->description_ar);
    }

    /**
     * Starting Price Accessor (new package system-aware)
     * Returns the minimum price across all packages, or falls back to legacy price.
     */
    public function getStartingPriceAttribute(): ?float
    {
        $packageMin = $this->packages()->with('prices')->get()
            ->flatMap(fn($pkg) => $pkg->prices)->min('price');

        return $packageMin ?? $this->price;
    }

    /**
     * Localized Includes Accessor
     */
    public function getIncludesAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->includes_ar ?? $this->includes_en) : ($this->includes_en ?? $this->includes_ar);
    }

    /**
     * Localized Excludes Accessor
     */
    public function getExcludesAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->excludes_ar ?? $this->excludes_en) : ($this->excludes_en ?? $this->excludes_ar);
    }

    /**
     * Localized Children Policy Accessor
     */
    public function getChildrenPolicyAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->children_policy_ar ?? $this->children_policy_en) : ($this->children_policy_en ?? $this->children_policy_ar);
    }

}
