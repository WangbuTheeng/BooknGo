<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'route_id',
        'departure_datetime',
        'arrival_time',
        'price',
        'is_festival_fare',
        'status',
    ];

    protected $casts = [
        'departure_datetime' => 'datetime',
        'arrival_time' => 'datetime',
        'is_festival_fare' => 'boolean',
    ];

    /**
     * Get the bus for this trip
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    /**
     * Get the route for this trip
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Get all bookings for this trip
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get available seats count for this trip
     */
    public function getAvailableSeatsCountAttribute()
    {
        $totalSeats = $this->bus->total_seats;
        $bookedSeatsCount = \DB::table('booking_seats')
            ->join('bookings', 'booking_seats.booking_id', '=', 'bookings.id')
            ->where('bookings.trip_id', $this->id)
            ->whereIn('bookings.status', ['pending', 'booked']) // Include both pending and booked
            ->count();

        return $totalSeats - $bookedSeatsCount;
    }

    /**
     * Check if trip is bookable
     */
    public function isBookable()
    {
        return $this->status === 'active' &&
               $this->departure_datetime > now() &&
               $this->available_seats_count > 0;
    }

    /**
     * Check if trip has finished (departure time has passed)
     */
    public function isFinished()
    {
        return $this->departure_datetime < now();
    }

    /**
     * Check if trip is active (not finished and status is active)
     */
    public function isActive()
    {
        return $this->status === 'active' && !$this->isFinished();
    }

    /**
     * Get trip status display text
     */
    public function getStatusDisplayAttribute()
    {
        if ($this->isFinished()) {
            return 'Finished';
        }

        if ($this->status === 'active') {
            return 'Active';
        }

        return ucfirst($this->status);
    }

    /**
     * Get trip status color for UI
     */
    public function getStatusColorAttribute()
    {
        if ($this->isFinished()) {
            return 'red';
        }

        if ($this->status === 'active') {
            return 'green';
        }

        return 'gray';
    }
}
