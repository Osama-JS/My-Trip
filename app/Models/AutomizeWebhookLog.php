<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomizeWebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
