<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPassenger extends Model
{
    protected $fillable = [
        'booking_id',
        'title',
        'first_name',
        'last_name',
        'type',
        'ticket_number',
        'passport_no',
        'nationality',
        'dob',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
