<?php

namespace App\Models\Platform;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class AdminNotification extends Model
{
    public $timestamps = false;

    protected $fillable = ['source_key', 'category', 'title', 'message', 'route_name', 'route_parameters', 'created_at'];

    protected $casts = ['route_parameters' => 'array', 'created_at' => 'datetime'];

    public function payload(): array
    {
        return [
            'id' => (int) $this->id,
            'category' => $this->category,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->route_name && Route::has($this->route_name)
                ? route($this->route_name, $this->route_parameters ?? [], false)
                : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
