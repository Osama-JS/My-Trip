<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hotel_name',
        'hotel_id',
        'city_name',
        'country_name',
        'check_in',
        'check_out',
        'rooms',
        'adults',
        'childs',
        'total_price',
        'currency',
        'status',
        'reference_num',
        'supplier_confirmation_num',
        'session_id',
        'product_id',
        'token_id',
        'pax_details',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'pax_details' => 'array',
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }
}
