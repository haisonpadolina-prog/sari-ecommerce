<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\ProductReview;
use App\Services\BuyerIdentityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BuyerReviewController extends Controller
{
    public function __construct(private readonly BuyerIdentityService $identity)
    {
    }

    public function store(Request $request, MarketplaceOrder $order): RedirectResponse
    {
        $this->guardOwned($request, $order);

        if ($order->status !== 'delivered') {
            throw ValidationException::withMessages([
                'review' => 'Reviews can only be submitted after delivery.',
            ]);
        }

        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:seller_products,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1500'],
        ]);

        $belongsToOrder = collect((array) $order->items)
            ->contains(fn ($item) => (int) ($item['product_id'] ?? 0) === (int) $validated['product_id']);

        if (!$belongsToOrder) {
            abort(422, 'That product does not belong to this order.');
        }

        ProductReview::query()->updateOrCreate(
            [
                'marketplace_order_id' => $order->id,
                'seller_product_id' => (int) $validated['product_id'],
            ],
            array_merge($this->identity->columns($request), [
                'seller_account_id' => $order->seller_account_id,
                'rating' => (int) $validated['rating'],
                'comment' => trim((string) ($validated['comment'] ?? '')) ?: null,
            ])
        );

        return back()->with('success', 'Thank you. Your product review was saved.');
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
