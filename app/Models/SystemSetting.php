<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     */
    public static function setValue($key, $value, $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'description' => $description]
        );
    }

    /**
     * Get festival fare multiplier
     */
    public static function getFestivalFareMultiplier()
    {
        return (float) static::getValue('festival_fare_multiplier', 1.5);
    }

    /**
     * Check if festival mode is enabled
     */
    public static function isFestivalModeEnabled()
    {
        return (bool) static::getValue('festival_mode_enabled', false);
    }
}
