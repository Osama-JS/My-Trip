<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsuranceApiLog extends Model
{
    protected $fillable = [
        'user_id',
        'policy_id',
        'action',
        'endpoint',
        'method',
        'request_payload',
        'response_payload',
        'status_code',
        'execution_time',
        'ip_address',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'execution_time' => 'decimal:4',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(InsurancePolicy::class, 'policy_id');
    }
}
