<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    use HasFactory;

    protected $fillable = [
        'airport_code',
        'airport_name',
        'airport_name_ar',
        'city_code',
        'city_name',
        'city_name_ar',
        'country_code',
        'country_name',
        'country_name_ar',
    ];
}
