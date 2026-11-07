<?php
namespace App\Models\Platform;

use Illuminate\Database\Eloquent\Model;

class SellerNotification extends Model
{
    protected $fillable = [
        'seller_account_id', 'type', 'title', 'message', 'action_url',
        'data', 'read_at', 'archived_at', 'deleted_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'archived_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
