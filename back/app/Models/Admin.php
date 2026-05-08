<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admin extends Model
{
    protected $hidden = [
        'password_hash',
    ];

    protected $fillable = [
        'name',
        'phone',
        'image',
        'password_hash',
        'is_active',
        'last_login_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function authTokens(): HasMany
    {
        return $this->hasMany(AdminAuthToken::class, 'admin_id');
    }
}
