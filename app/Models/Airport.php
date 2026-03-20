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
        'city_code',
        'city_name',
        'country_code',
        'country_name',
    ];
}
