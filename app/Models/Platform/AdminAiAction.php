<?php

namespace App\Models\Platform;

use Illuminate\Database\Eloquent\Model;

class AdminAiAction extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'admin_account_id', 'type', 'payload', 'status', 'result', 'expires_at'];

    protected $casts = ['payload' => 'encrypted:array', 'result' => 'encrypted:array', 'expires_at' => 'datetime'];
}
