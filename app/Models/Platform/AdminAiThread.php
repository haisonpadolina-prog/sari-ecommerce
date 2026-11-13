<?php

namespace App\Models\Platform;

use Illuminate\Database\Eloquent\Model;

class AdminAiThread extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'admin_account_id', 'messages'];

    protected $casts = ['messages' => 'encrypted:array'];
}
