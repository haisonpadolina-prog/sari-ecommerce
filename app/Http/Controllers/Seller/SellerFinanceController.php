<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Accounts\SellerAccount;
use App\Models\Finance\FinancePayoutRequest;
use App\Models\Finance\SellerSettlement;
use App\Services\Finance\WalletService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerFinanceController extends Controller
{
    public function __construct(
        private readonly WalletService $wallets
    ) {
    }

    public function index(Request $request): View
    {
        $seller = $this->seller($request);
        $wallet = $this->wallets->wallet('seller', $seller->id);

        $settlements = SellerSettlement::query()
            ->with(['order', 'commission'])
            ->where('seller_account_id', $seller->id)
            ->latest('eligible_at')
            ->paginate(20);

        $base = SellerSettlement::query()->where('seller_account_id', $seller->id);
        $stats = [
            'gross' => (float) (clone $base)->sum('merchandise_amount'),
            'commission' => (float) (clone $base)->sum('platform_commission_amount'),
            'net' => (float) (clone $base)->sum('seller_net_amount'),
            'pending' => (float) (clone $base)
                ->whereIn('status', ['payable', 'pending_remittance'])
                ->sum('seller_net_amount'),
            'available' => (float) $wallet->available_balance,
            'reserved' => (float) $wallet->reserved_balance,
            'paid' => (float) $wallet->lifetime_payouts,
        ];

        $payoutRequests = FinancePayoutRequest::query()
            ->where('owner_type', 'seller')
            ->where('owner_id', $seller->id)
            ->latest('requested_at')
            ->limit(20)
            ->get();

        $walletEntries = $wallet->entries()
            ->latest('occurred_at')
            ->limit(30)
            ->get();

        return view('seller.finance', compact(
            'seller',
            'settlements',
            'stats',
            'wallet',
            'payoutRequests',
            'walletEntries'
        ));
    }

    private function seller(Request $request): SellerAccount
    {
        $seller = $request->attributes->get('sellerAccount');

        if ($seller instanceof SellerAccount) {
            return $seller;
        }

        return SellerAccount::query()->findOrFail(
            (int) $request->session()->get('seller_account_id')
        );
    }
}
