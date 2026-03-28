<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelCity extends Model
{
    protected $fillable = [
        'city_code',
        'city_name_en',
        'city_name_ar',
        'country_code',
        'country_name_en',
        'country_name_ar',
        'is_active',
    ];
}
