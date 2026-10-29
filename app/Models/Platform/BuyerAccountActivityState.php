<?php

namespace App\Models\Platform;

use Illuminate\Database\Eloquent\Model;

class BuyerAccountActivityState extends Model
{
    protected $fillable = [
        'buyer_key',
        'admin_user_activity_id',
        'read_at',
        'archived_at',
        'deleted_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'archived_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
