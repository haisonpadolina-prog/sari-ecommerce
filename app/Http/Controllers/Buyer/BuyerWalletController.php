<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Buyer\BuyerIdentityService;
use App\Services\Finance\WalletService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuyerWalletController extends Controller
{
    public function __construct(
        private readonly BuyerIdentityService $identity,
        private readonly WalletService $wallets
    ) {
    }

    public function index(Request $request): View
    {
        $this->identity->guard($request);

        // Registered and social Buyers have separate, session-owned ledgers.
        // COD checkout never spends this balance and does not credit a Buyer.
        $ownerType = $this->identity->accountId($request) ? 'buyer' : 'social_buyer';
        $ownerId = $this->identity->accountId($request)
            ?? $this->identity->socialId($request);

        $wallet = $this->wallets->wallet($ownerType, (int) $ownerId);
        $walletEntries = $wallet->entries()
            ->latest('occurred_at')
            ->limit(30)
            ->get();

        $base = $this->identity->apply(MarketplaceOrder::query(), $request)
            ->whereRaw('UPPER(payment_method) = ?', ['COD']);

        $codCollected = (float) (clone $base)
            ->where('status', 'delivered')
            ->whereRaw('LOWER(payment_status) = ?', ['paid'])
            ->sum('total');

        $codOrders = (clone $base)
            ->latest('created_at')
            ->paginate(10);

        return view('buyer.wallet', compact(
            'wallet',
            'walletEntries',
            'codCollected',
            'codOrders'
        ));
    }
}
