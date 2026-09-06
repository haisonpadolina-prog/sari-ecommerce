<?php

namespace App\Http\Controllers;

use App\Jobs\DispatchAdminSellerRealtime;
use App\Models\ComplianceMessage;
use App\Models\SellerAccount;
use App\Services\SellerAccountStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminSellerAccountStatusController extends Controller
{
    public function __construct(
        private SellerAccountStatusService $statusService
    ) {}

    private function guard(Request $request): void
    {
        abort_unless(
            $request->session()->get('is_admin'),
            403,
            'Administrator session required.'
        );
    }

    /**
     * Realtime/network work is queued so Admin enforcement actions return
     * immediately after the database changes have been saved.
     */
    private function queueBroadcast(
        SellerAccount $seller,
        string $action,
        string $message
    ): void {
        try {
            DispatchAdminSellerRealtime::dispatch(
                sellerId: (int) $seller->id,
                kind: 'account-status',
                payload: [
                    'action' => $action,
                    'message' => $message,
                ]
            );
        } catch (\Throwable $e) {
            Log::warning(
                'SARI seller account-status event could not be queued.',
                [
                    'seller_id' => $seller->id,
                    'action' => $action,
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    private function logAdminAction(
        SellerAccount $seller,
        string $message
    ): void {
        $record = new ComplianceMessage();

        $record->forceFill([
            'seller_account_id' => $seller->id,
            'sender_role' => 'admin',
            'message' => $message,
        ]);

        $record->save();
    }

    public function index(Request $request)
    {
        $this->guard($request);

        /*
        |--------------------------------------------------------------------------
        | DO NOT REFRESH EVERY SELLER ON EVERY PAGE LOAD
        |--------------------------------------------------------------------------
        |
        | Only possibly-expired suspensions need normalization. The old
        | controller loaded every seller, refreshed each record, then queried
        | the whole table a second time.
        */
        SellerAccount::query()
            ->whereNotNull('suspended_until')
            ->where('suspended_until', '<=', now())
            ->get()
            ->each(function (SellerAccount $seller) {
                $this->statusService->refresh($seller);
            });

        $sellers = SellerAccount::query()
            ->latest('id')
            ->get();

        $now = now();

        $stats = [
            'total' => $sellers->count(),

            'active' => $sellers->filter(function ($seller) use ($now) {
                $status = $seller->account_status ?: 'active';

                $activeSuspension =
                    $seller->suspended_until &&
                    $now->lt($seller->suspended_until);

                return $status === 'active'
                    && !$activeSuspension
                    && (int) ($seller->warning_count ?? 0) < 3;
            })->count(),

            'suspended' => $sellers->filter(function ($seller) use ($now) {
                $status = $seller->account_status ?: 'active';

                $activeSuspension =
                    $seller->suspended_until &&
                    $now->lt($seller->suspended_until);

                return $status === 'active'
                    && (
                        $activeSuspension ||
                        (int) ($seller->warning_count ?? 0) >= 3
                    );
            })->count(),

            'banned' => $sellers
                ->where('account_status', 'banned')
                ->count(),

            'deactivated' => $sellers
                ->where('account_status', 'deactivated')
                ->count(),
        ];

        return view(
            'admin.seller-account-control',
            compact('sellers', 'stats')
        );
    }

    public function suspend30(
        Request $request,
        SellerAccount $seller
    ) {
        $this->guard($request);

        if ($this->statusService->isTerminated($seller)) {
            return $this->error(
                $request,
                'seller',
                'Banned or deactivated accounts cannot be suspended. Restore/unban the account first.'
            );
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:1500'],
        ]);

        $seller = $this->statusService->suspend30(
            $seller,
            $validated['reason'] ?? null
        );

        $this->logAdminAction(
            $seller,
            'The administrator suspended this seller account for 30 days. Reason: ' .
            (
                $seller->suspension_reason
                ?: 'Administrator enforcement action.'
            )
        );

        $this->queueBroadcast(
            $seller,
            'suspended',
            'Your seller account was suspended for 30 days. Please contact the administrator from the locked dashboard.'
        );

        return $this->success(
            $request,
            'Seller suspended for 30 days.'
        );
    }

    public function liftSuspension(
        Request $request,
        SellerAccount $seller
    ) {
        $this->guard($request);

        if ($this->statusService->isTerminated($seller)) {
            return $this->error(
                $request,
                'seller',
                'Unban or restore the account before lifting a suspension.'
            );
        }

        $seller = $this->statusService->liftSuspension(
            $seller
        );

        $this->logAdminAction(
            $seller,
            'The administrator lifted the seller suspension. Active warnings were reset to 0 / 3.'
        );

        $this->queueBroadcast(
            $seller,
            'suspension_lifted',
            'Your suspension has been lifted. Your active warning counter is now 0 / 3.'
        );

        return $this->success(
            $request,
            'Suspension lifted and active warnings reset to 0 / 3.'
        );
    }

    public function ban(
        Request $request,
        SellerAccount $seller
    ) {
        $this->guard($request);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:1500'],
        ]);

        if (
            ($seller->account_status ?: 'active') ===
            'deactivated'
        ) {
            return $this->error(
                $request,
                'seller',
                'Restore the deactivated account before banning it.'
            );
        }

        if (
            ($seller->account_status ?: 'active') ===
            'banned'
        ) {
            return $this->error(
                $request,
                'seller',
                'This seller is already banned.'
            );
        }

        $seller = $this->statusService->ban(
            $seller,
            $validated['reason']
        );

        $this->logAdminAction(
            $seller,
            'Seller account banned. Reason: ' .
            $validated['reason']
        );

        $this->queueBroadcast(
            $seller,
            'banned',
            'Your seller account has been banned by the SARI Administrator.'
        );

        return $this->success(
            $request,
            'Seller account banned.'
        );
    }

    public function unban(
        Request $request,
        SellerAccount $seller
    ) {
        $this->guard($request);

        if (
            ($seller->account_status ?: 'active') !==
            'banned'
        ) {
            return $this->error(
                $request,
                'seller',
                'Only banned seller accounts can be unbanned.'
            );
        }

        $seller = $this->statusService->unban(
            $seller
        );

        $this->logAdminAction(
            $seller,
            'Seller account unbanned. Active warnings were reset to 0 / 3. Previously removed products remain subject to administrator review.'
        );

        $this->queueBroadcast(
            $seller,
            'unbanned',
            'Your seller account has been restored. Your active warning counter is now 0 / 3.'
        );

        return $this->success(
            $request,
            'Seller unbanned and active warnings reset to 0 / 3.'
        );
    }

    public function deactivate(
        Request $request,
        SellerAccount $seller
    ) {
        $this->guard($request);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:1500'],
            'confirmation' => ['required', 'in:DELETE'],
        ]);

        if (
            ($seller->account_status ?: 'active') ===
            'deactivated'
        ) {
            return $this->error(
                $request,
                'seller',
                'This seller account is already deactivated.'
            );
        }

        $seller = $this->statusService->deactivate(
            $seller,
            $validated['reason']
        );

        $this->logAdminAction(
            $seller,
            'Seller account deactivated. Reason: ' .
            $validated['reason'] .
            ' Product, warning, AI moderation, and compliance history were preserved.'
        );

        $this->queueBroadcast(
            $seller,
            'deactivated',
            'Your seller account has been deactivated by the SARI Administrator.'
        );

        return $this->success(
            $request,
            'Seller account deactivated. Compliance history was preserved.'
        );
    }

    public function restore(
        Request $request,
        SellerAccount $seller
    ) {
        $this->guard($request);

        if (
            ($seller->account_status ?: 'active') !==
            'deactivated'
        ) {
            return $this->error(
                $request,
                'seller',
                'Only deactivated seller accounts can be restored.'
            );
        }

        $seller = $this->statusService->restore(
            $seller
        );

        $this->logAdminAction(
            $seller,
            'Seller account restored. Active warnings were reset to 0 / 3. Previously removed products remain subject to administrator review.'
        );

        $this->queueBroadcast(
            $seller,
            'restored',
            'Your seller account has been restored. Your active warning counter is now 0 / 3.'
        );

        return $this->success(
            $request,
            'Seller account restored and active warnings reset to 0 / 3.'
        );
    }

    private function success(
        Request $request,
        string $message
    ) {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    private function error(
        Request $request,
        string $key,
        string $message
    ) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'errors' => [
                    $key => [$message],
                ],
            ], 422);
        }

        return back()->withErrors([
            $key => $message,
        ]);
    }
}
