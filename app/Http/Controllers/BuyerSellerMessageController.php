<?php

namespace App\Http\Controllers;

use App\Models\BuyerSellerMessage;
use App\Models\MarketplaceOrder;
use App\Models\SellerAccount;
use App\Models\SellerProduct;
use App\Services\BuyerIdentityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BuyerSellerMessageController extends Controller
{
    public function __construct(private readonly BuyerIdentityService $identity)
    {
    }

    public function buyerIndex(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        $this->identity->guard($request);

        $sellerIds = collect();

        $sellerIds = $sellerIds->merge(
            $this->identity->apply(MarketplaceOrder::query(), $request)
                ->pluck('seller_account_id')
        );

        $sellerIds = $sellerIds->merge(
            $this->identity->apply(BuyerSellerMessage::query(), $request)
                ->pluck('seller_account_id')
        );

        // Let buyers start a conversation before ordering from any approved shop.
        $sellerIds = $sellerIds->merge(
            SellerProduct::query()
                ->where('moderation_status', 'approved')
                ->whereNull('archived_at')
                ->pluck('seller_account_id')
        )->filter()->unique()->values();

        $sellers = SellerAccount::query()
            ->whereIn('id', $sellerIds)
            ->orderBy('store_name')
            ->get();

        $selectedSellerId = (int) $request->query('seller', $sellers->first()?->id ?? 0);
        $selectedSeller = $sellers->firstWhere('id', $selectedSellerId);
        $messages = collect();

        if ($selectedSeller) {
            $messages = $this->identity
                ->apply(BuyerSellerMessage::query(), $request)
                ->where('seller_account_id', $selectedSeller->id)
                ->orderBy('id')
                ->get();

            $this->identity
                ->apply(BuyerSellerMessage::query(), $request)
                ->where('seller_account_id', $selectedSeller->id)
                ->where('sender_role', 'seller')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return view('buyer.messages', compact('sellers', 'selectedSeller', 'messages'));
    }

    public function buyerSend(Request $request, SellerAccount $seller): RedirectResponse
    {
        $this->identity->guard($request);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:3000'],
            'order_id' => ['nullable', 'integer', 'exists:marketplace_orders,id'],
        ]);

        $orderId = null;

        if (!empty($validated['order_id'])) {
            $ownedOrder = $this->identity
                ->apply(MarketplaceOrder::query(), $request)
                ->whereKey((int) $validated['order_id'])
                ->where('seller_account_id', $seller->id)
                ->first();

            $orderId = $ownedOrder?->id;
        }

        BuyerSellerMessage::create(array_merge(
            $this->identity->columns($request),
            [
                'seller_account_id' => $seller->id,
                'marketplace_order_id' => $orderId,
                'sender_role' => 'buyer',
                'body' => trim($validated['body']),
            ]
        ));

        return redirect()
            ->route('buyer.messages', ['seller' => $seller->id])
            ->with('success', 'Message sent to seller.');
    }
}
