<?php

namespace App\Http\Controllers;

use App\Models\BuyerCartItem;
use App\Models\MarketplaceOrder;
use App\Models\PlatformSetting;
use App\Models\MarketplaceOrderEvent;
use App\Models\SellerAccount;
use App\Models\SellerProduct;
use App\Services\BuyerCartService;
use App\Services\BuyerIdentityService;
use App\Services\ProductVariantInventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BuyerCheckoutController extends Controller
{
    public function __construct(
        private readonly BuyerCartService $cart,
        private readonly BuyerIdentityService $identity,
        private readonly ProductVariantInventoryService $inventory,
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        $buyNowItemId = $request->integer('buy_now') ?: null;
        $selectionRequested = $request->boolean('selection');
        $selectedItemIds = collect((array) $request->input('items', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $items = $this->cart->items($request);

        if ($buyNowItemId) {
            $items = $items
                ->filter(fn (BuyerCartItem $item) => (int) $item->id === (int) $buyNowItemId)
                ->values();
        } elseif ($selectionRequested) {
            if ($selectedItemIds->isEmpty()) {
                return redirect()->route('buyer.cart')->withErrors([
                    'cart' => 'Select at least one product to checkout.',
                ]);
            }

            $items = $items
                ->filter(fn (BuyerCartItem $item) => $selectedItemIds->contains((int) $item->id))
                ->values();
        }

        if ($items->isEmpty()) {
            return redirect()->route('buyer.cart')->withErrors([
                'cart' => $buyNowItemId
                    ? 'The Buy Now item is no longer available in your cart.'
                    : ($selectionRequested
                        ? 'The selected cart products are no longer available.'
                        : 'Your cart is empty.'),
            ]);
        }

        $snapshot = $this->identity->snapshot($request);
        $sellerGroups = $this->cart->groupedBySeller($items);
        $deliveryFee = $this->cart->deliveryFee($items);
        $subtotal = $this->cart->subtotal($items);

        return view('buyer.checkout', [
            'items' => $items,
            'groups' => $sellerGroups,
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'grandTotal' => round($subtotal + $deliveryFee, 2),
            'profile' => $snapshot,
            'cart' => $this->cart,
            'buyNowItemId' => $buyNowItemId,
            'selectedItemIds' => $buyNowItemId ? collect() : $items->pluck('id')->map(fn ($id) => (int) $id)->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->identity->guard($request);

        $validated = $request->validate([
            'buyer_name' => ['required', 'string', 'max:150'],
            'buyer_email' => ['required', 'email', 'max:255'],
            'buyer_phone' => ['required', 'string', 'max:30'],
            'buyer_address' => ['required', 'string', 'max:1200'],
            'payment_method' => ['required', 'in:COD'],
            'buy_now_item_id' => ['nullable', 'integer', 'exists:buyer_cart_items,id'],
            'checkout_item_ids' => ['nullable', 'array'],
            'checkout_item_ids.*' => ['integer', 'exists:buyer_cart_items,id'],
        ]);

        $createdOrders = DB::transaction(function () use ($request, $validated) {
            $cartQuery = $this->identity->apply(BuyerCartItem::query(), $request);

            if (!empty($validated['buy_now_item_id'])) {
                $cartQuery->whereKey((int) $validated['buy_now_item_id']);
            } elseif (!empty($validated['checkout_item_ids'])) {
                $cartQuery->whereIn(
                    'id',
                    collect($validated['checkout_item_ids'])
                        ->map(fn ($id) => (int) $id)
                        ->filter(fn ($id) => $id > 0)
                        ->unique()
                        ->values()
                        ->all()
                );
            }

            $items = $cartQuery
                ->lockForUpdate()
                ->with(['product.seller', 'variant'])
                ->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Your cart is empty or was already checked out.',
                ]);
            }

            $identityColumns = $this->identity->columns($request);
            $checkoutReference = 'CHK-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5));
            $feePerSeller = (float) PlatformSetting::valueOf('delivery_fee_per_seller', config('sari_buyer.delivery_fee_per_seller', 80));
            $created = collect();

            foreach ($items->groupBy(fn (BuyerCartItem $item) => (int) $item->product->seller_account_id) as $sellerId => $group) {
                $seller = SellerAccount::query()->findOrFail((int) $sellerId);
                $orderItems = [];
                $subtotal = 0.0;

                $groupShipsFree = $group->every(
                    fn (BuyerCartItem $item) => (bool) ($item->product?->free_shipping ?? false)
                );

                $sellerDeliveryFee = $groupShipsFree ? 0.0 : $feePerSeller;

                foreach ($group as $cartItem) {
                    $product = SellerProduct::query()
                        ->with('activeVariants')
                        ->whereKey($cartItem->seller_product_id)
                        ->where('moderation_status', 'approved')
                        ->whereNull('archived_at')
                        ->lockForUpdate()
                        ->first();

                    if (!$product) {
                        throw ValidationException::withMessages([
                            'cart' => 'One of the products in your cart is no longer available.',
                        ]);
                    }

                    $this->inventory->assertAvailable(
                        $product,
                        $cartItem->seller_product_variant_id,
                        (int) $cartItem->quantity
                    );

                    $unitPrice = $this->cart->unitPrice($cartItem);
                    $lineTotal = round($unitPrice * (int) $cartItem->quantity, 2);
                    $variantOptions = is_array($cartItem->variant?->option_values)
                        ? $cartItem->variant->option_values
                        : [];

                    $orderItems[] = [
                        'product_id' => (int) $product->id,
                        'variant_id' => $cartItem->seller_product_variant_id ? (int) $cartItem->seller_product_variant_id : null,
                        'name' => (string) $product->name,
                        'sku' => (string) ($cartItem->variant?->sku ?: $product->sku ?: ''),
                        'variant_options' => $variantOptions,
                        'variant_label' => $this->cart->variantLabel($cartItem),
                        'qty' => (int) $cartItem->quantity,
                        'price' => $unitPrice,
                        'line_total' => $lineTotal,
                        'free_shipping' => (bool) ($product->free_shipping ?? false),
                    ];

                    $subtotal += $lineTotal;
                }

                foreach ($group as $cartItem) {
                    $this->inventory->deductForOrder(
                        $cartItem->product,
                        $cartItem->seller_product_variant_id,
                        (int) $cartItem->quantity
                    );
                }

                $pickupAddress = collect([
                    $seller->street_address ?? null,
                    $seller->barangay_name ?? null,
                    $seller->municipality_name ?? null,
                    $seller->province_name ?? null,
                    'Philippines',
                ])->filter(fn ($part) => filled($part))->implode(', ');

                $order = MarketplaceOrder::create(array_merge($identityColumns, [
                    'order_number' => $this->newOrderNumber(),
                    'checkout_reference' => $checkoutReference,
                    'seller_account_id' => $seller->id,
                    'buyer_name' => $validated['buyer_name'],
                    'buyer_email' => strtolower(trim($validated['buyer_email'])),
                    'buyer_phone' => $validated['buyer_phone'],
                    'buyer_address' => $validated['buyer_address'],
                    'payment_method' => 'COD',
                    'payment_status' => 'pending',
                    'items' => $orderItems,
                    'subtotal' => round($subtotal, 2),
                    'delivery_fee' => $sellerDeliveryFee,
                    'total' => round($subtotal + $sellerDeliveryFee, 2),
                    'pickup_name' => $seller->store_name ?: 'SARI Seller Store',
                    'pickup_address' => $pickupAddress !== '' ? $pickupAddress : 'Seller pickup address to be confirmed',
                    'status' => 'new',
                ]));

                MarketplaceOrderEvent::create([
                    'marketplace_order_id' => $order->id,
                    'seller_account_id' => $seller->id,
                    'audience' => 'both',
                    'type' => 'new',
                    'title' => 'New Order Received',
                    'message' => 'Buyer checkout created order ' . $order->order_number . '.',
                    'status' => 'new',
                ]);

                $created->push($order);
            }

            $cartQuery->delete();

            return $created;
        }, 3);

        return redirect()
            ->route('buyer.orders')
            ->with('success', $createdOrders->count() === 1
                ? 'Order placed successfully. The seller can now prepare it.'
                : $createdOrders->count() . ' seller orders were created successfully.');
    }

    private function newOrderNumber(): string
    {
        do {
            $number = 'SARI-' . now()->format('ymd') . '-' . Str::upper(Str::random(6));
        } while (MarketplaceOrder::query()->where('order_number', $number)->exists());

        return $number;
    }
}
