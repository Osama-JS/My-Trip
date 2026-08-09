<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
     /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    // User Types Constants
    const TYPE_ADMIN = 'admin';
    const TYPE_CUSTOMER = 'customer';
    const TYPE_AGENT = 'agent';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'user_type',
        'company_id',
        'profile_photo',
        'phone',
        'country_code',
        'country',
        'city',
        'date_of_birth',
        'gender',
        'address',
        'status',
        'otp_code',
        'otp_expires_at',
        'phone_verified_at',
        'fcm_token',
        'device_type',
        'is_guest',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if the user profile is complete.
     * Guest users (who only verified phone via OTP) will have a @guest.flyvio.com email.
     */
    public function isProfileComplete(): bool
    {
        return !$this->is_guest;
    }

    /**
     * Get profile photo URL
     */
    public function getProfilePhotoUrlAttribute()
    {
        if (!$this->profile_photo) {
            return asset('images/profile/pic1.jpg');
        }

        // Check if it's already a full URL (e.g. from social login or external storage)
        if (filter_var($this->profile_photo, FILTER_VALIDATE_URL)) {
            return $this->profile_photo;
        }

        // Ensure it's an absolute URL
        return url('storage/' . $this->profile_photo);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'is_guest' => 'boolean',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === self::TYPE_ADMIN;
    }

    /**
     * Check if user is customer
     */
    /**
     * Get user trip bookings
     */
    public function tripBookings(): HasMany
    {
        return $this->hasMany(TripBooking::class);
    }

    /**
     * Get user flight bookings
     */
    public function flightBookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get user hotel bookings
     */
    public function hotelBookings(): HasMany
    {
        return $this->hasMany(HotelBooking::class);
    }

    /**
     * Get user bookings (Legacy/Trip)
     */
    public function bookings(): HasMany
    {
        return $this->tripBookings();
    }

    /**
     * Get user notifications
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    /**
     * Get unread notifications
     */
    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->where('is_read', false)->latest();
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }


    public function isCustomer(): bool
    {
        return $this->user_type === self::TYPE_CUSTOMER;
    }

    /**
     * Check if user is agent
     */
    public function isAgent(): bool
    {
        return $this->user_type === self::TYPE_AGENT;
    }

    /**
     * Get user company
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get user favorites.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get user bank transfers.
     */
    public function bankTransfers(): HasMany
    {
        return $this->hasMany(BankTransfer::class);
    }

    /**
     * Get user support tickets (as client)
     */
    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'user_id');
    }

    /**
     * Get assigned support tickets (as admin)
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to');
    }
}
