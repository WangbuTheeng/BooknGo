<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trip_id',
        'booking_reference',
        'passenger_name',
        'passenger_phone',
        'passenger_email',
        'booking_code',
        'status',
        'total_amount',
        'payment_status',
        'cancellation_reason',
        'expires_at',
        'booking_type',
        'notes',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = strtoupper(Str::random(10));
            }
        });
    }

    /**
     * Get the user who made this booking
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the trip for this booking
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Get all booked seats for this booking
     */
    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class);
    }

    /**
     * Get the payment for this booking
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get all payments for this booking (alias for consistency)
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get seat numbers as a comma-separated string
     */
    public function getSeatNumbersAttribute()
    {
        return $this->bookingSeats->pluck('seat_number')->implode(', ');
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'booked']) &&
               $this->trip->departure_datetime > now()->addHours(2);
    }

    /**
     * Check if booking has expired
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at < now();
    }

    /**
     * Check if booking can be deleted by user
     */
    public function canBeDeleted()
    {
        return in_array($this->status, ['pending', 'booked']) &&
               $this->payment_status === 'pending' &&
               !$this->isExpired();
    }

    /**
     * Get remaining time until expiry
     */
    public function getRemainingTimeAttribute()
    {
        if (!$this->expires_at || $this->isExpired()) {
            return null;
        }

        return $this->expires_at->diffForHumans();
    }

    /**
     * Get remaining minutes until expiry
     */
    public function getRemainingMinutesAttribute()
    {
        if (!$this->expires_at || $this->isExpired()) {
            return 0;
        }

        return max(0, now()->diffInMinutes($this->expires_at, false));
    }
}
