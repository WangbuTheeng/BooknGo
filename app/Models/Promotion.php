<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'promo_code',
        'operator_id',
        'discount_percent',
        'min_amount',
        'max_uses',
        'user_limit',
        'valid_from',
        'valid_till',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_till' => 'date',
    ];

    /**
     * Get the operator that created this promotion
     */
    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * Check if promotion is currently valid
     */
    public function isValid()
    {
        $now = now()->toDateString();
        
        return (!$this->valid_from || $this->valid_from <= $now) &&
               (!$this->valid_till || $this->valid_till >= $now);
    }

    /**
     * Check if promotion can be used for a specific amount
     */
    public function canBeUsedForAmount($amount)
    {
        return !$this->min_amount || $amount >= $this->min_amount;
    }

    /**
     * Calculate discount amount for given total
     */
    public function calculateDiscount($amount)
    {
        if (!$this->canBeUsedForAmount($amount)) {
            return 0;
        }

        return ($amount * $this->discount_percent) / 100;
    }
}
