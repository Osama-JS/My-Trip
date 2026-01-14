<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    // User Types Constants
    const TYPE_ADMIN = 'admin';
    const TYPE_CUSTOMER = 'customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'profile_photo',
        'phone',
        'country',
        'date_of_birth',
        'gender',
        'address',
        'otp_code',
        'otp_expires_at',
        'phone_verified_at',
    ];

    /**
     * Get profile photo URL
     */
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo
            ? asset('storage/' . $this->profile_photo)
            : asset('images/profile/pic1.jpg');
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
    public function isCustomer(): bool
    {
        return $this->user_type === self::TYPE_CUSTOMER;
    }
}
