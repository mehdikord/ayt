<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAuthToken extends Model
{
    protected $fillable = [
        'user_id',
        'token_hash',
        'token_type',
        'expires_at',
        'revoked_at',
        'device_name',
        'device_id',
        'user_agent',
        'ip_address',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
