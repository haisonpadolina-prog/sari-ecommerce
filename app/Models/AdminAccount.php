<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminAccount extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'can_manage_platform_settings',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'can_manage_platform_settings' => 'boolean',
    ];

    public function canManagePlatformSettings(): bool
    {
        return $this->role === 'super_admin'
            || (bool) $this->can_manage_platform_settings;
    }
}
