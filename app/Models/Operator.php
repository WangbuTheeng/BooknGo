<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'license_number',
        'contact_info',
        'address',
        'logo_url',
        'verified',
    ];

    protected $casts = [
        'contact_info' => 'array',
        'verified' => 'boolean',
    ];

    /**
     * Get the user that owns this operator profile
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all buses owned by this operator
     */
    public function buses()
    {
        return $this->hasMany(Bus::class);
    }

    /**
     * Get all routes for this operator
     */
    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    /**
     * Get all promotions created by this operator
     */
    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    /**
     * Get all trips for this operator's buses
     */
    public function trips()
    {
        return $this->hasManyThrough(Trip::class, Bus::class);
    }

    /**
     * Get all bookings for this operator's trips
     */
    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Trip::class, 'bus_id', 'trip_id', 'id', 'id')
                    ->whereHas('trip.bus', function ($query) {
                        $query->where('operator_id', $this->id);
                    });
    }
}
