<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Accounts\SellerAccount;
use App\Models\Platform\SellerNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SellerNotificationCenterController extends Controller
{
    public function index(Request $request): View
    {
        $seller = $this->seller($request);
        $showArchived = $request->query('filter') === 'archived';

        $notifications = SellerNotification::query()
            ->where('seller_account_id', $seller->id)
            ->whereNull('deleted_at')
            ->when(
                $showArchived,
                fn ($query) => $query->whereNotNull('archived_at'),
                fn ($query) => $query->whereNull('archived_at')
            )
            ->orderByDesc($showArchived ? 'archived_at' : 'created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $unread = $this->unreadCount($seller);

        return view('seller.notifications', compact('seller', 'notifications', 'unread', 'showArchived'));
    }

    public function read(Request $request, SellerNotification $notification): RedirectResponse|JsonResponse
    {
        $seller = $this->seller($request);
        $this->assertOwnVisibleNotification($seller, $notification);
        abort_if($notification->archived_at !== null, 404);

        DB::transaction(function () use ($notification, $seller): void {
            SellerNotification::query()
                ->whereKey($notification->id)
                ->where('seller_account_id', $seller->id)
                ->whereNull('archived_at')
                ->whereNull('deleted_at')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'notification_id' => (int) $notification->id,
                'unread_count' => $this->unreadCount($seller),
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        }

        return back();
    }

    public function readAll(Request $request): RedirectResponse|JsonResponse
    {
        $seller = $this->seller($request);

        DB::transaction(function () use ($seller): void {
            SellerNotification::query()
                ->where('seller_account_id', $seller->id)
                ->whereNull('archived_at')
                ->whereNull('deleted_at')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        });

        $unreadCount = $this->unreadCount($seller);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'unread_count' => $unreadCount,
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        }

        return back()->with('success', 'Notifications marked as read.');
    }

    public function archive(Request $request, SellerNotification $notification): RedirectResponse
    {
        $seller = $this->seller($request);
        $this->assertOwnVisibleNotification($seller, $notification);
        abort_if($notification->archived_at !== null, 404);

        SellerNotification::query()
            ->whereKey($notification->id)
            ->where('seller_account_id', $seller->id)
            ->whereNull('archived_at')
            ->whereNull('deleted_at')
            ->update([
                'archived_at' => now(),
                'read_at' => $notification->read_at ?? now(),
            ]);

        return back()->with('success', 'Notification archived.');
    }

    public function restore(Request $request, SellerNotification $notification): RedirectResponse
    {
        $seller = $this->seller($request);
        $this->assertOwnVisibleNotification($seller, $notification);
        abort_if($notification->archived_at === null, 404);

        SellerNotification::query()
            ->whereKey($notification->id)
            ->where('seller_account_id', $seller->id)
            ->whereNotNull('archived_at')
            ->whereNull('deleted_at')
            ->update(['archived_at' => null]);

        return back()->with('success', 'Notification restored to Recent.');
    }

    public function destroy(Request $request, SellerNotification $notification): RedirectResponse
    {
        $seller = $this->seller($request);
        $this->assertOwnVisibleNotification($seller, $notification);

        // Remove from this seller's inbox/history, not from business records or audits.
        // A deletion timestamp prevents the item from returning to either folder.
        SellerNotification::query()
            ->whereKey($notification->id)
            ->where('seller_account_id', $seller->id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now(),
                'read_at' => $notification->read_at ?? now(),
            ]);

        return back()->with('success', 'Notification deleted from your history.');
    }

    private function assertOwnVisibleNotification(SellerAccount $seller, SellerNotification $notification): void
    {
        abort_unless(
            (int) $notification->seller_account_id === (int) $seller->id
                && $notification->deleted_at === null,
            404
        );
    }

    private function unreadCount(SellerAccount $seller): int
    {
        return SellerNotification::query()
            ->where('seller_account_id', $seller->id)
            ->whereNull('archived_at')
            ->whereNull('deleted_at')
            ->whereNull('read_at')
            ->count();
    }

    private function seller(Request $request): SellerAccount
    {
        $seller = $request->attributes->get('sellerAccount');
        if ($seller instanceof SellerAccount) return $seller;

        return SellerAccount::query()->findOrFail(
            (int) $request->session()->get('seller_account_id')
        );
    }
}
