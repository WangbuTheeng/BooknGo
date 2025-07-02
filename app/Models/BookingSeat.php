<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingSeat extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'seat_id',
        'seat_number',
    ];

    /**
     * Get the booking this seat belongs to
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the seat details
     */
    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
