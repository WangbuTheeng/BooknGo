<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'province',
        'region',
    ];

    public $timestamps = false;

    /**
     * Get routes that start from this city
     */
    public function routesFrom()
    {
        return $this->hasMany(Route::class, 'from_city_id');
    }

    /**
     * Get routes that end at this city
     */
    public function routesTo()
    {
        return $this->hasMany(Route::class, 'to_city_id');
    }
}
