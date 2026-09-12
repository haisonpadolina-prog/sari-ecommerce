<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformSettingAudit extends Model
{
    protected $fillable = [
        'platform_setting_version_id',
        'admin_account_id',
        'setting_key',
        'action',
        'old_value',
        'new_value',
        'reason',
        'effective_at',
        'metadata',
    ];

    protected $casts = [
        'effective_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function version(): BelongsTo
    {
        return $this->belongsTo(
            PlatformSettingVersion::class,
            'platform_setting_version_id'
        );
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(
            AdminAccount::class,
            'admin_account_id'
        );
    }
}
