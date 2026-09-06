<?php

namespace App\Http\Middleware;

use App\Models\SellerAccount;
use App\Services\SellerAccountStatusService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerNotRestricted
{
    public function __construct(private SellerAccountStatusService $statusService) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->get('is_seller')) {
            return redirect()->route('login');
        }

        $seller = $this->resolveSellerOnce($request);

        if (!$seller) {
            $request->session()->forget([
                'is_seller',
                'seller_account_id',
            ]);

            return redirect()->route('login');
        }

        if ($this->statusService->isBanned($seller)) {
            $request->session()->forget([
                'is_seller',
                'seller_account_id',
            ]);

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'This seller account has been banned. Please contact the SARI Administrator if you need a review.',
                ]);
        }

        if ($this->statusService->isDeactivated($seller)) {
            $request->session()->forget([
                'is_seller',
                'seller_account_id',
            ]);

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'This seller account has been deactivated by the administrator.',
                ]);
        }

        $restricted =
            (int) ($seller->warning_count ?? 0) >= 3
            || $this->statusService->isSuspended($seller);

        if ($restricted) {
            return redirect()
                ->route('seller.dashboard')
                ->with(
                    'restricted_notice',
                    'Seller access is temporarily restricted. Please message the administrator from the dashboard.'
                );
        }

        return $next($request);
    }

    private function resolveSellerOnce(Request $request): ?SellerAccount
    {
        $resolved = $request->attributes->get('sellerAccount');

        if ($resolved instanceof SellerAccount) {
            return $resolved;
        }

        $sellerId = (int) $request->session()->get('seller_account_id');

        if ($sellerId < 1) {
            return null;
        }

        $seller = SellerAccount::find($sellerId);

        if (!$seller) {
            return null;
        }

        /*
        | Keep the existing SellerAccountStatusService behavior, but avoid
        | repeating the same refresh again inside controllers/layouts.
        */
        $seller = $this->statusService->refresh($seller);

        if (!$seller->realtime_token) {
            $seller->ensureRealtimeToken();
        }

        $request->attributes->set('sellerAccount', $seller);

        return $seller;
    }
}
