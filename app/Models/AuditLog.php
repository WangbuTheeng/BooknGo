<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'details',
        'ip_address',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public $timestamps = true;
    const UPDATED_AT = null; // Only use created_at

    /**
     * Get the user who performed this action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an action
     */
    public static function logAction($action, $module = null, $details = null, $userId = null, $ipAddress = null)
    {
        return static::create([
            'user_id' => $userId ?: auth()->id(),
            'action' => $action,
            'module' => $module,
            'details' => $details,
            'ip_address' => $ipAddress ?: request()->ip(),
        ]);
    }
}
