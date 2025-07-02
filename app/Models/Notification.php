<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'status',
        'sent_at',
        'channel_response',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'channel_response' => 'array',
    ];

    /**
     * Get the user this notification belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as sent
     */
    public function markAsSent($response = null)
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'channel_response' => $response,
        ]);
    }

    /**
     * Mark notification as failed
     */
    public function markAsFailed($response = null)
    {
        $this->update([
            'status' => 'failed',
            'channel_response' => $response,
        ]);
    }
}
