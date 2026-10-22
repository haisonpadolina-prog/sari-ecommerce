<?php

namespace App\Models\Promotions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformCampaignCategory extends Model
{
    protected $fillable = ['platform_campaign_id', 'category'];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(PlatformCampaign::class, 'platform_campaign_id');
    }
}
