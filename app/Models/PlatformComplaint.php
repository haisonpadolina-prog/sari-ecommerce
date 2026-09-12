<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformComplaint extends Model
{
    protected $fillable = [
        'reporter_role', 'reporter_identifier', 'subject', 'description', 'status', 'admin_note', 'resolved_at',
    ];

    protected $casts = ['resolved_at' => 'datetime'];
}
