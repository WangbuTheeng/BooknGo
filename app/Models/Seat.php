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
        'seat_type',
        'row_number',
        'column_number',
        'side',
        'is_available_for_booking',
        'price_multiplier',
        'properties',
    ];

    /**
     * Get the bus this seat belongs to
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    protected $casts = [
        'properties' => 'array',
        'is_available_for_booking' => 'boolean',
        'price_multiplier' => 'decimal:2',
    ];

    /**
     * Get all booking seats for this seat
     */
    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class);
    }

    /**
     * Check if seat is available for booking
     */
    public function isAvailableForBooking()
    {
        return $this->is_available_for_booking &&
               in_array($this->seat_type, ['passenger', 'vip']);
    }

    /**
     * Check if seat is a special type
     */
    public function isSpecialSeat()
    {
        return in_array($this->seat_type, ['vip', 'blocked', 'conductor', 'driver']);
    }

    /**
     * Get seat display name
     */
    public function getDisplayName()
    {
        $name = $this->seat_number;

        if ($this->seat_type === 'vip') {
            $name .= ' (VIP)';
        } elseif ($this->seat_type === 'blocked') {
            $name .= ' (Blocked)';
        } elseif ($this->seat_type === 'conductor') {
            $name .= ' (Conductor)';
        } elseif ($this->seat_type === 'driver') {
            $name .= ' (Driver)';
        }

        return $name;
    }

    /**
     * Get seat CSS classes for styling
     */
    public function getCssClasses()
    {
        $classes = ['seat'];

        switch ($this->seat_type) {
            case 'vip':
                $classes[] = 'seat-vip';
                break;
            case 'blocked':
                $classes[] = 'seat-blocked';
                break;
            case 'conductor':
                $classes[] = 'seat-conductor';
                break;
            case 'driver':
                $classes[] = 'seat-driver';
                break;
            default:
                $classes[] = 'seat-passenger';
        }

        if (!$this->is_available_for_booking) {
            $classes[] = 'seat-unavailable';
        }

        return implode(' ', $classes);
    }

    /**
     * Check if seat is available for a specific trip
     */
    public function isAvailableForTrip($tripId)
    {
        return !$this->bookingSeats()
            ->whereHas('booking', function ($query) use ($tripId) {
                $query->where('trip_id', $tripId)
                      ->whereIn('status', ['pending', 'booked'])
                      ->where(function ($q) {
                          // Include bookings that haven't expired or are already booked
                          $q->where('expires_at', '>', now())
                            ->orWhere('status', 'booked');
                      });
            })->exists();
    }
}
