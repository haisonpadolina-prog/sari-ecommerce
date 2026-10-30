<?php

namespace App\Models\Platform;

use Illuminate\Database\Eloquent\Model;

class BuyerHomepageBanner extends Model
{
    protected $fillable = [
        'slot', 'image_path', 'kicker', 'title', 'description', 'button_text',
        'button_url', 'font_family', 'title_size', 'description_size', 'button_size',
        'text_color', 'button_bg', 'button_color', 'text_x', 'text_y',
        'button_x', 'button_y',
    ];

    protected $casts = [
        'title_size' => 'integer',
        'description_size' => 'integer',
        'button_size' => 'integer',
        'text_x' => 'integer',
        'text_y' => 'integer',
        'button_x' => 'integer',
        'button_y' => 'integer',
    ];
}
