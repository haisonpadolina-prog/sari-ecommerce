<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Model;

class AccountEmailBan extends Model
{
    protected $fillable = [
        'email', 'role', 'account_id', 'banned_by_admin_account_id',
        'reason', 'banned_at', 'unbanned_at',
    ];

    protected $casts = [
        'banned_at' => 'datetime',
        'unbanned_at' => 'datetime',
    ];
}
