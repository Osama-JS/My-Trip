<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightSearchLog extends Model
{
    protected $fillable = [
        'user_id',
        'origin_code',
        'destination_code',
        'departure_date',
        'return_date',
        'adults',
        'children',
        'infants',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
