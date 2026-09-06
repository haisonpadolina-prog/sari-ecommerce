<?php

namespace App\Http\Middleware;

use App\Models\SellerAccount;
use App\Services\SellerAccountStatusService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerAccountAccessible
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

        return $next($request);
    }

    private function resolveSellerOnce(Request $request): ?SellerAccount
    {
        /*
        |--------------------------------------------------------------------------
        | Reuse the resolved SellerAccount for the rest of this HTTP request
        |--------------------------------------------------------------------------
        |
        | Controllers and the Seller layout can read:
        |
        | request()->attributes->get('sellerAccount')
        |
        | This prevents the same seller row from being queried and refreshed
        | again by the middleware, controller, and Blade layout.
        |
        */

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
        | Keep your existing status-service business rules intact, but run
        | them only once during this request.
        */
        $seller = $this->statusService->refresh($seller);

        /*
        | Token generation only writes to MySQL when the token is missing.
        | Normal page navigation performs no write here.
        */
        if (!$seller->realtime_token) {
            $seller->ensureRealtimeToken();
        }

        $request->attributes->set('sellerAccount', $seller);

        return $seller;
    }
}
