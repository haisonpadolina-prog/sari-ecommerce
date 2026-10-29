<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Models\Orders\SellerReturnEvent;
use App\Models\Orders\SellerReturnRequest;
use App\Models\Platform\SellerNotification;
use App\Services\Buyer\BuyerIdentityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BuyerReturnRequestController extends Controller
{
    public function __construct(private readonly BuyerIdentityService $identity)
    {
    }

    public function store(Request $request, MarketplaceOrder $order): RedirectResponse
    {
        $this->guardOwned($request, $order);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:120'],
            'details' => ['nullable', 'string', 'max:1200'],
        ]);

        DB::transaction(function () use ($order, $validated): void {
            $lockedOrder = MarketplaceOrder::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedOrder->status !== 'delivered') {
                throw ValidationException::withMessages([
                    'return' => 'Only delivered orders can be submitted for return or refund review.',
                ]);
            }

            if (SellerReturnRequest::query()->where('marketplace_order_id', $lockedOrder->id)->exists()) {
                throw ValidationException::withMessages([
                    'return' => 'A return or refund request already exists for this order.',
                ]);
            }

            $returnRequest = SellerReturnRequest::query()->create([
                'marketplace_order_id' => $lockedOrder->id,
                'seller_account_id' => $lockedOrder->seller_account_id,
                'buyer_account_id' => $lockedOrder->buyer_account_id,
                'buyer_social_account_id' => $lockedOrder->buyer_social_account_id,
                'reason' => $validated['reason'],
                'details' => trim((string) ($validated['details'] ?? '')) ?: null,
                'requested_amount' => (float) $lockedOrder->subtotal,
                'status' => 'requested',
            ]);

            SellerReturnEvent::query()->create([
                'seller_return_request_id' => $returnRequest->id,
                'actor_role' => 'buyer',
                'actor_id' => $lockedOrder->buyer_account_id ?: $lockedOrder->buyer_social_account_id,
                'status' => 'requested',
                'title' => 'Return Requested',
                'message' => $returnRequest->details ?: $returnRequest->reason,
            ]);

            MarketplaceOrderEvent::query()->create([
                'marketplace_order_id' => $lockedOrder->id,
                'seller_account_id' => $lockedOrder->seller_account_id,
                'audience' => 'buyer',
                'type' => 'return_requested',
                'title' => 'Return / refund request submitted',
                'message' => "Your request for {$lockedOrder->order_number} is waiting for Seller review.",
                'status' => 'return_requested',
            ]);

            SellerNotification::query()->create([
                'seller_account_id' => $lockedOrder->seller_account_id,
                'type' => 'return_request',
                'title' => 'New return request',
                'message' => "Buyer requested a return for {$lockedOrder->order_number}.",
                'action_url' => route('seller.returns.index'),
                'data' => [
                    'order_id' => $lockedOrder->id,
                    'return_request_id' => $returnRequest->id,
                ],
            ]);
        }, 3);

        return back()->with('success', 'Return / refund request submitted to the Seller.');
    }

    private function guardOwned(Request $request, MarketplaceOrder $order): void
    {
        $this->identity->guard($request);

        $owned = $this->identity->accountId($request)
            ? (int) $order->buyer_account_id === (int) $this->identity->accountId($request)
            : (int) $order->buyer_social_account_id === (int) $this->identity->socialId($request);

        abort_unless($owned, 403);
    }
}
