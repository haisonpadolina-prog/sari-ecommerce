<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUserActivity extends Model
{
    protected $fillable = [
        'admin_account_id',
        'user_role',
        'user_id',
        'action',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
