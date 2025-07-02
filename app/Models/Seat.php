<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'seat_number',
        'position',
    ];

    /**
     * Get the bus this seat belongs to
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    /**
     * Get all booking seats for this seat
     */
    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class);
    }

    /**
     * Check if seat is available for a specific trip
     */
    public function isAvailableForTrip($tripId)
    {
        return !$this->bookingSeats()
            ->whereHas('booking', function ($query) use ($tripId) {
                $query->where('trip_id', $tripId)
                      ->where('status', 'booked');
            })->exists();
    }
}
