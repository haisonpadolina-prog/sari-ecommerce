<?php

namespace App\Providers;

use App\Http\Controllers\Admin\AdminNotificationController;
use App\Models\Accounts\AdminAccount;
use App\Models\Messaging\ChatMessage;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Platform\AdminUserActivity;
use App\Models\Platform\PlatformComplaint;
use App\Models\Registration\RegistrationApplication;
use App\Services\Admin\AdminNotificationService;
use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AdminNotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(AdminNotificationService::class);
        config(['auth.guards.sari-admin-notifications' => ['driver' => 'sari-admin-notifications']]);
    }

    public function boot(): void
    {
        Auth::viaRequest('sari-admin-notifications', static function (Request $request): ?GenericUser {
            $id = (int) $request->session()->get('admin_account_id', 0);
            return $request->session()->get('is_admin') && $id > 0 && AdminAccount::query()->whereKey($id)->exists()
                ? new GenericUser(['id' => 'admin-' . $id]) : null;
        });
        Broadcast::channel('sari.admin.notifications', static fn ($user): bool => str_starts_with((string) $user->getAuthIdentifier(), 'admin-'), [
            'guards' => ['sari-admin-notifications'],
        ]);
        if (!$this->app->routesAreCached()) {
            Route::middleware('web')->prefix('admin/notifications')->name('admin.notifications.')->group(function (): void {
                Route::get('/feed', [AdminNotificationController::class, 'index'])->name('feed');
                Route::post('/read-all', [AdminNotificationController::class, 'readAll'])->name('read-all');
                Route::post('/{notification}/read', [AdminNotificationController::class, 'read'])->name('read');
            });
        }

        ChatMessage::created(static function (ChatMessage $message): void {
            if ($message->sender_role !== 'seller') return;
            app(AdminNotificationService::class)->publish('chat:' . $message->id, 'messages',
                'New Seller message', 'A Seller sent a new message.', 'admin.messages', ['seller' => $message->seller_account_id]);
        });
        RegistrationApplication::created(static function (RegistrationApplication $application): void {
            app(AdminNotificationService::class)->publish('registration:' . $application->id, 'registrations',
                'New registration', Str::headline((string) $application->role) . ' application submitted.',
                $application->role === 'buyer' ? 'admin.users' : 'admin.registrations');
        });
        PlatformComplaint::created(static function (PlatformComplaint $complaint): void {
            app(AdminNotificationService::class)->publish('complaint:' . $complaint->id, 'complaints',
                'New complaint', 'A marketplace complaint needs review.', 'admin.complaints');
        });
        MarketplaceOrder::created(static function (MarketplaceOrder $order): void {
            app(AdminNotificationService::class)->publish('order:new:' . $order->id, 'orders',
                'New order', 'Order #' . $order->id . ' was created.', 'admin.reports');
        });
        MarketplaceOrder::updated(static function (MarketplaceOrder $order): void {
            if (!$order->wasChanged(['status', 'payment_status'])) return;
            // A UUID distinguishes genuine repeat transitions without leaking payment details.
            app(AdminNotificationService::class)->publish('order:update:' . $order->id . ':' . Str::uuid(), 'orders',
                'Order updated', 'Order #' . $order->id . ' status or payment status changed.', 'admin.reports');
        });
        AdminUserActivity::created(static function (AdminUserActivity $activity): void {
            $metadata = $activity->metadata ?? [];
            if (($metadata['source'] ?? 'user') !== 'user' || (int) ($metadata['response_status'] ?? 200) >= 400) return;
            $route = (string) ($metadata['route_name'] ?? '');
            if (preg_match('/(?:^|[._-])(?:read|read_all|summary|heartbeat|typing|seen|notifications)(?:$|[._-])/i', $route)) return;
            $action = Str::headline(str_replace(['.', '-'], '_', $route ?: $activity->action));
            app(AdminNotificationService::class)->publish('activity:' . $activity->id, 'activity',
                'User activity', Str::headline((string) $activity->user_role) . ' #' . $activity->user_id . ': ' . $action,
                'admin.user-activity');
        });
    }
}
