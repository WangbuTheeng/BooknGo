<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'registration_number',
        'name',
        'type',
        'total_seats',
        'layout_config',
        'features',
    ];

    protected $casts = [
        'layout_config' => 'array',
        'features' => 'array',
    ];

    /**
     * Get the operator that owns this bus
     */
    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * Get all seats in this bus
     */
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * Get all trips for this bus
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Get available seats for a specific trip
     */
    public function getAvailableSeats($tripId)
    {
        $bookedSeatIds = BookingSeat::whereHas('booking', function ($query) use ($tripId) {
            $query->where('trip_id', $tripId)
                  ->where('status', 'booked');
        })->pluck('seat_id');

        return $this->seats()->whereNotIn('id', $bookedSeatIds)->get();
    }
}
