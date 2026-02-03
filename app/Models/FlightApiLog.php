<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightApiLog extends Model
{
    protected $fillable = [
        'user_id',
        'booking_id',
        'action',
        'endpoint',
        'method',
        'request_payload',
        'response_payload',
        'status_code',
        'ip_address',
        'user_agent',
        'execution_time',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
