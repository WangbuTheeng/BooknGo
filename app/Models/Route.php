<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_city_id',
        'to_city_id',
        'estimated_km',
        'estimated_time',
        'operator_id',
    ];

    protected $casts = [
        'estimated_time' => 'datetime:H:i:s',
    ];

    /**
     * Get the city where this route starts
     */
    public function fromCity()
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    /**
     * Get the city where this route ends
     */
    public function toCity()
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    /**
     * Get the origin city for the route.
     */
    public function origin()
    {
        return $this->fromCity();
    }

    /**
     * Get the destination city for the route.
     */
    public function destination()
    {
        return $this->toCity();
    }

    /**
     * Get all trips for this route
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Get route name as "From City - To City"
     */
    public function getRouteNameAttribute()
    {
        return $this->fromCity->name . ' - ' . $this->toCity->name;
    }

    /**
     * Get the operator that owns this route
     */
    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}
