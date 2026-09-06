<?php

namespace App\Http\Controllers;

use App\Models\BuyerCartItem;
use App\Services\BuyerCartService;
use App\Services\BuyerIdentityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuyerCartController extends Controller
{
    public function __construct(
        private readonly BuyerCartService $cart,
        private readonly BuyerIdentityService $identity,
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        $items = $this->cart->items($request);

        return view('buyer.cart', [
            'items' => $items,
            'subtotal' => $this->cart->subtotal($items),
            'deliveryFee' => $this->cart->deliveryFee($items),
            'cart' => $this->cart,
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->identity->guard($request);

        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:seller_products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:seller_product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $item = $this->cart->add(
            $request,
            (int) $validated['product_id'],
            isset($validated['variant_id']) ? (int) $validated['variant_id'] : null,
            (int) $validated['quantity'],
        );

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Item added to cart.',
                'cart_count' => $this->cart->count($request),
                'item_id' => $item->id,
            ]);
        }

        return back()->with('success', 'Item added to cart.');
    }

    public function update(Request $request, BuyerCartItem $item): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $item = $this->cart->update($request, $item, (int) $validated['quantity']);

        if ($request->expectsJson()) {
            $items = $this->cart->items($request);

            return response()->json([
                'ok' => true,
                'message' => 'Cart updated.',
                'cart_count' => $this->cart->count($request),
                'line_total' => $this->cart->lineTotal($item),
                'subtotal' => $this->cart->subtotal($items),
            ]);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(Request $request, BuyerCartItem $item): JsonResponse|RedirectResponse
    {
        $this->cart->remove($request, $item);

        if ($request->expectsJson()) {
            $items = $this->cart->items($request);

            return response()->json([
                'ok' => true,
                'message' => 'Item removed from cart.',
                'cart_count' => $this->cart->count($request),
                'subtotal' => $this->cart->subtotal($items),
            ]);
        }

        return back()->with('success', 'Item removed from cart.');
    }

    public function summary(Request $request): JsonResponse
    {
        $this->identity->guard($request);

        return response()->json([
            'count' => $this->cart->count($request),
        ]);
    }
}
