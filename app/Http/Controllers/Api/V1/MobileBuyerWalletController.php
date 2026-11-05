<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Finance\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileBuyerWalletController extends Controller
{
    public function __construct(
        private readonly WalletService $wallets
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        /** @var BuyerAccount $buyer */
        $buyer = $request->user();
        $wallet = $this->wallets->wallet('buyer', (int) $buyer->id);

        $base = MarketplaceOrder::query()
            ->where('buyer_account_id', $buyer->id)
            ->whereRaw('UPPER(payment_method) = ?', ['COD']);

        return response()->json([
            'success' => true,
            'data' => [
                'currency' => 'PHP',
                'available_balance' => (float) $wallet->available_balance,
                'reserved_balance' => (float) $wallet->reserved_balance,
                'lifetime_credits' => (float) $wallet->lifetime_credits,
                'lifetime_payouts' => (float) $wallet->lifetime_payouts,
                'cod_spending' => (float) (clone $base)
                    ->where('status', 'delivered')
                    ->whereRaw('LOWER(payment_status) = ?', ['paid'])
                    ->sum('total'),
                'recent_entries' => $wallet->entries()
                    ->latest('occurred_at')
                    ->limit(20)
                    ->get()
                    ->map(fn ($entry): array => [
                        'category' => $entry->category,
                        'amount' => (float) $entry->amount,
                        'available_delta' => (float) $entry->available_delta,
                        'occurred_at' => $entry->occurred_at?->toIso8601String(),
                    ])->values(),
                'cod_only' => true,
                'wallet_checkout_enabled' => false,
                'wallet_withdrawal_enabled' => false,
            ],
        ])->header('Cache-Control', 'no-store');
    }
}
