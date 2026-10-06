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

        $notifications = SellerNotification::query()
            ->where('seller_account_id', $seller->id)
            ->latest()
            ->paginate(30);

        $unread = $this->unreadCount($seller);

        return view(
            'seller.notifications',
            compact('seller', 'notifications', 'unread')
        );
    }

    public function read(
        Request $request,
        SellerNotification $notification
    ): RedirectResponse|JsonResponse {
        $seller = $this->seller($request);

        abort_unless(
            (int) $notification->seller_account_id
                === (int) $seller->id,
            404
        );

        DB::transaction(function () use (
            $notification,
            $seller
        ): void {
            SellerNotification::query()
                ->whereKey($notification->id)
                ->where(
                    'seller_account_id',
                    $seller->id
                )
                ->whereNull('read_at')
                ->update([
                    'read_at' => now(),
                ]);
        });

        if ($request->expectsJson()) {
            return response()
                ->json([
                    'ok' => true,
                    'notification_id' =>
                        (int) $notification->id,
                    'unread_count' =>
                        $this->unreadCount($seller),
                ])
                ->header(
                    'Cache-Control',
                    'no-store, no-cache, must-revalidate'
                );
        }

        return back();
    }

    public function readAll(
        Request $request
    ): RedirectResponse|JsonResponse {
        $seller = $this->seller($request);

        DB::transaction(function () use ($seller): void {
            SellerNotification::query()
                ->where(
                    'seller_account_id',
                    $seller->id
                )
                ->whereNull('read_at')
                ->update([
                    'read_at' => now(),
                ]);
        });

        /*
         * Query again instead of assuming zero. If a genuinely new
         * notification was inserted concurrently, it should remain unread.
         */
        $unreadCount =
            $this->unreadCount($seller);

        if ($request->expectsJson()) {
            return response()
                ->json([
                    'ok' => true,
                    'unread_count' => $unreadCount,
                ])
                ->header(
                    'Cache-Control',
                    'no-store, no-cache, must-revalidate'
                );
        }

        return back()->with(
            'success',
            'Notifications marked as read.'
        );
    }

    private function unreadCount(
        SellerAccount $seller
    ): int {
        return SellerNotification::query()
            ->where(
                'seller_account_id',
                $seller->id
            )
            ->whereNull('read_at')
            ->count();
    }

    private function seller(
        Request $request
    ): SellerAccount {
        $seller =
            $request->attributes->get(
                'sellerAccount'
            );

        if ($seller instanceof SellerAccount) {
            return $seller;
        }

        return SellerAccount::query()
            ->findOrFail(
                (int) $request->session()->get(
                    'seller_account_id'
                )
            );
    }
}
