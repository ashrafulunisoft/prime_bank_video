<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoCallOtp extends Model
{
    protected $table = 'video_call_otps';
    protected $fillable = [
        'user_id',
        'otp_hash',
        'channel',
        'expires_at',
        'verified_at',
        'attempts',
        'is_active'
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'verified_at' => 'datetime',
        'is_active'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* =========================
       Helper Methods
       ==========================*/

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }

    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }
}
