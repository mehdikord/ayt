<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpSession extends Model
{
    protected $fillable = [
        'mobile',
        'otp_code_hash',
        'expires_at',
        'resend_available_at',
        'attempt_count',
        'max_attempts',
        'is_verified',
        'verified_at',
        'consumed_at',
        'request_ip',
        'request_user_agent',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'resend_available_at' => 'datetime',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
            'consumed_at' => 'datetime',
        ];
    }
}
