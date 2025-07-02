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
    ];

    protected $casts = [
        'payment_time' => 'datetime',
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
