<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoCallChat extends Model
{
    use HasFactory;

    protected $table = 'video_call_chats';

    protected $fillable = [
        'call_session_id',
        'sender_id',
        'sender_type', // 'customer' or 'agent'
        'message',
        'is_read',
        'created_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Get the sender (User model)
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the call session
     */
    public function callSession()
    {
        return $this->belongsTo(CallSession::class, 'call_session_id');
    }

    /**
     * Get messages for a specific call session
     */
    public static function forSession($sessionId)
    {
        return self::where('call_session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get unread count for a session
     */
    public static function unreadCount($sessionId, $excludeSenderId = null)
    {
        $query = self::where('call_session_id', $sessionId)
            ->where('is_read', false);

        if ($excludeSenderId) {
            $query->where('sender_id', '!=', $excludeSenderId);
        }

        return $query->count();
    }

    /**
     * Mark all messages as read for a session
     */
    public static function markAllAsRead($sessionId, $userId)
    {
        return self::where('call_session_id', $sessionId)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}
