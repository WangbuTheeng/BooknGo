<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'method',
        'payment_status',
        'transaction_id',
        'payment_time',
        'response_log',
        'processed_by_user_id',
        'confirmed_by_user_id',
        'confirmed_at',
    ];

    protected $casts = [
        'payment_time' => 'datetime',
        'confirmed_at' => 'datetime',
        'response_log' => 'array',
    ];

    /**
     * Get the booking this payment belongs to
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user who processed this payment
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }

    /**
     * Get the user who confirmed this payment
     */
    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by_user_id');
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful()
    {
        return $this->payment_status === 'success';
    }

    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return $this->payment_status === 'pending';
    }
}
