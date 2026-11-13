<?php

namespace App\Services\Admin;

use App\Events\AdminNotificationCreated;
use App\Models\Platform\AdminNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class AdminNotificationService
{
    private ?bool $available = null;

    public function publish(string $key, string $category, string $title, string $message, string $route, array $parameters = []): ?AdminNotification
    {
        try {
            if (!($this->available ??= Schema::hasTable('admin_notifications'))) {
                return null;
            }
            $notification = AdminNotification::query()->firstOrCreate(['source_key' => $key], [
                'category' => Str::limit($category, 40, ''),
                'title' => Str::limit($title, 160, ''),
                'message' => Str::limit($message, 500, ''),
                'route_name' => $route,
                'route_parameters' => $parameters,
                'created_at' => now(),
            ]);
            if ($notification->wasRecentlyCreated) {
                $id = (int) $notification->id;
                DB::afterCommit(static function () use ($id): void {
                    try {
                        AdminNotificationCreated::dispatch($id);
                    } catch (Throwable $exception) {
                        // A websocket outage must not fail checkout or registration.
                        report($exception);
                    }
                });
            }
            return $notification;
        } catch (Throwable $exception) {
            report($exception);
            return null;
        }
    }
}
