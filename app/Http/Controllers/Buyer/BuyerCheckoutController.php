<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Catalog\SellerProductVariant;
use App\Models\Orders\BuyerCartItem;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Models\Promotions\SellerVoucherRedemption;
use App\Services\Buyer\BuyerCartService;
use App\Services\Buyer\BuyerIdentityService;
use App\Services\Messaging\BuyerSellerOrderConfirmationService;
use App\Services\Products\ProductVariantInventoryService;
use App\Services\Promotions\PlatformCampaignService;
use App\Services\Promotions\SellerFreeShippingPromotionService;
use App\Services\Seller\SellerVoucherService;
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
        private readonly SellerVoucherService $vouchers,
        private readonly SellerFreeShippingPromotionService $freeShippingPromotions,
        private readonly PlatformCampaignService $platformCampaigns,
        private readonly BuyerSellerOrderConfirmationService $orderConfirmation,
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

        $unavailable = $items
            ->map(fn (BuyerCartItem $item) => $this->cart->availability($item))
            ->first(fn (array $availability) => !$availability['selectable']);

        if ($unavailable) {
            return redirect()->route('buyer.cart')->withErrors([
                'cart' => $unavailable['reason']
                    ?: 'One of the selected products is no longer available for checkout.',
            ]);
        }

        $snapshot = $this->identity->snapshot($request);
        $registeredProfile = $snapshot;
        $savedAddresses = collect();
        $selectedAddressId = null;

        if ($buyer = $this->identity->account($request)) {
            $savedAddresses = $buyer->addresses()
                ->orderByDesc('is_default')
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->get();

            $defaultAddress = $savedAddresses->firstWhere('is_default', true);

            if ($defaultAddress) {
                $snapshot['name'] = $defaultAddress->recipient_name;
                $snapshot['phone'] = $defaultAddress->contact_no;
                $snapshot['address'] = $defaultAddress->fullAddress();
                $selectedAddressId = (int) $defaultAddress->id;
            }
        }
        $sellerGroups = $this->cart->groupedBySeller($items);
        $shipping = $this->cart->shippingSummary($items);
        $deliveryFee = (float) $shipping['delivery_fee'];
        $subtotal = $this->cart->subtotal($items);
        $sellerSubtotal = round(
            $items->sum(fn (BuyerCartItem $item) => $this->cart->sellerLineTotal($item)),
            2
        );
        $platformSaleDiscount = round(max(0, $sellerSubtotal - $subtotal), 2);

        $appliedVoucherCode = strtoupper(trim((string) $request->query('voucher_code', '')));
        $platformVoucherPreview = null;
        $platformVoucherDiscount = 0.0;
        $voucherPreviewError = null;

        if ($appliedVoucherCode !== '') {
            $previewLines = $items->map(function (BuyerCartItem $item): array {
                return [
                    'product' => $item->product,
                    'seller_id' => (int) $item->product->seller_account_id,
                    'buyer_line_total' => $this->cart->lineTotal($item),
                ];
            });

            try {
                $platformVoucherPreview = $this->platformCampaigns->previewVoucherForCheckout(
                    $appliedVoucherCode,
                    $previewLines,
                    $this->identity->columns($request)
                );

                if ($platformVoucherPreview !== null) {
                    $platformVoucherDiscount = round(
                        (float) $platformVoucherPreview['discount'],
                        2
                    );
                } else {
                    $voucherPreviewError = 'This is not an active SARI event voucher for the selected products. Seller vouchers are still validated when you place the order.';
                }
            } catch (ValidationException $exception) {
                $voucherPreviewError = collect($exception->errors())
                    ->flatten()
                    ->first() ?: 'This voucher could not be applied.';
            }
        }

        return view('buyer.checkout', [
            'items' => $items,
            'groups' => $sellerGroups,
            'subtotal' => $subtotal,
            'sellerSubtotal' => $sellerSubtotal,
            'platformSaleDiscount' => $platformSaleDiscount,
            'platformVoucherPreview' => $platformVoucherPreview,
            'platformVoucherDiscount' => $platformVoucherDiscount,
            'appliedVoucherCode' => $appliedVoucherCode,
            'voucherPreviewError' => $voucherPreviewError,
            'platformVoucherOffers' => $this->platformCampaigns->voucherOffersForItems(
                $items,
                $this->identity->columns($request)
            ),
            'deliveryFee' => $deliveryFee,
            'originalDeliveryFee' => (float) $shipping['original_delivery_fee'],
            'shippingDiscount' => (float) $shipping['shipping_discount'],
            'shippingBySeller' => $shipping['groups'],
            'grandTotal' => round(max(0, $subtotal - $platformVoucherDiscount) + $deliveryFee, 2),
            'profile' => $snapshot,
            'registeredProfile' => $registeredProfile,
            'savedAddresses' => $savedAddresses,
            'selectedAddressId' => $selectedAddressId,
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
            'voucher_code' => ['nullable', 'string', 'max:64'],
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
            $feePerSeller = $this->cart->deliveryFeePerSeller();
            $voucherCode = strtoupper(trim((string) ($validated['voucher_code'] ?? '')));
            $preparedGroups = collect();
            $platformVoucherLines = collect();

            foreach ($items->groupBy(fn (BuyerCartItem $item) => (int) $item->product->seller_account_id) as $sellerId => $group) {
                $seller = SellerAccount::query()->findOrFail((int) $sellerId);

                if (!$seller->isBuyerAvailable()) {
                    throw ValidationException::withMessages([
                        'cart' => ($seller->store_name ?: 'This Seller') . ' is temporarily not accepting orders.',
                    ]);
                }

                $orderItems = [];
                $lockedProducts = [];
                $sellerSubtotal = 0.0;
                $buyerSaleSubtotal = 0.0;
                $automaticCampaigns = [];

                foreach ($group as $cartItem) {
                    $product = SellerProduct::query()
                        ->whereKey($cartItem->seller_product_id)
                        ->buyerVisible()
                        ->lockForUpdate()
                        ->first();

                    if (!$product) {
                        throw ValidationException::withMessages([
                            'cart' => 'One of the products in your cart is no longer available.',
                        ]);
                    }

                    $lockedProducts[(int) $cartItem->id] = $product;
                    $lockedVariant = null;

                    if ($cartItem->seller_product_variant_id) {
                        $lockedVariant = SellerProductVariant::query()
                            ->whereKey((int) $cartItem->seller_product_variant_id)
                            ->where('seller_product_id', $product->id)
                            ->where('is_active', true)
                            ->lockForUpdate()
                            ->first();

                        if (!$lockedVariant) {
                            throw ValidationException::withMessages([
                                'cart' => 'One of the selected product variations is no longer available.',
                            ]);
                        }
                    }

                    $this->inventory->assertAvailable(
                        $product,
                        $cartItem->seller_product_variant_id,
                        (int) $cartItem->quantity
                    );

                    $sellerUnitPrice = $this->cart->sellerUnitPriceFor($product, $lockedVariant);
                    $platformSale = $this->cart->platformSaleQuoteFor($product, $lockedVariant);
                    $buyerUnitPrice = (float) $platformSale['buyer_price'];
                    $quantity = (int) $cartItem->quantity;
                    $sellerLineTotal = round($sellerUnitPrice * $quantity, 2);
                    $buyerLineTotal = round($buyerUnitPrice * $quantity, 2);
                    $platformLineDiscount = round(max(0, $sellerLineTotal - $buyerLineTotal), 2);
                    $variantOptions = is_array($lockedVariant?->option_values)
                        ? $lockedVariant->option_values
                        : [];

                    if (!empty($platformSale['snapshot']['id'])) {
                        $automaticCampaigns[(int) $platformSale['snapshot']['id']] = $platformSale['snapshot'];
                    }

                    $orderItems[] = [
                        'product_id' => (int) $product->id,
                        'variant_id' => $cartItem->seller_product_variant_id ? (int) $cartItem->seller_product_variant_id : null,
                        'name' => (string) $product->name,
                        'sku' => (string) ($lockedVariant?->sku ?: $product->sku ?: ''),
                        'variant_options' => $variantOptions,
                        'variant_label' => $this->cart->variantLabelFor($lockedVariant),
                        'qty' => $quantity,
                        'price' => $buyerUnitPrice,
                        'line_total' => $buyerLineTotal,
                        'seller_price' => $sellerUnitPrice,
                        'seller_line_total' => $sellerLineTotal,
                        'platform_discount_amount' => $platformLineDiscount,
                        'platform_campaign' => $platformSale['snapshot'],
                    ];

                    $sellerSubtotal += $sellerLineTotal;
                    $buyerSaleSubtotal += $buyerLineTotal;

                    $platformVoucherLines->push([
                        'product' => $product,
                        'seller_id' => (int) $seller->id,
                        'buyer_line_total' => $buyerLineTotal,
                    ]);
                }

                $preparedGroups->put((int) $sellerId, [
                    'seller' => $seller,
                    'cart_items' => $group,
                    'locked_products' => $lockedProducts,
                    'order_items' => $orderItems,
                    'seller_subtotal' => round($sellerSubtotal, 2),
                    'buyer_sale_subtotal' => round($buyerSaleSubtotal, 2),
                    'automatic_campaigns' => array_values($automaticCampaigns),
                ]);
            }

            $platformVoucher = $this->platformCampaigns->resolveVoucherForCheckout(
                $voucherCode,
                $platformVoucherLines,
                $identityColumns
            );

            $created = collect();
            $sellerVoucherApplied = false;

            foreach ($preparedGroups as $sellerId => $prepared) {
                /** @var SellerAccount $seller */
                $seller = $prepared['seller'];
                $sellerSubtotal = (float) $prepared['seller_subtotal'];
                $buyerSaleSubtotal = (float) $prepared['buyer_sale_subtotal'];
                $platformSaleDiscount = round(max(0, $sellerSubtotal - $buyerSaleSubtotal), 2);

                $sellerVoucherResult = null;
                if ($platformVoucher === null) {
                    $sellerVoucherResult = $this->vouchers->resolveForSeller(
                        (int) $seller->id,
                        $voucherCode,
                        $buyerSaleSubtotal
                    );
                }

                $sellerVoucherDiscount = (float) ($sellerVoucherResult['discount'] ?? 0);
                $sellerVoucher = $sellerVoucherResult['voucher'] ?? null;
                $sellerReceivableSubtotal = round(max(0, $sellerSubtotal - $sellerVoucherDiscount), 2);

                if ($sellerVoucher) {
                    $sellerVoucherApplied = true;
                }

                $platformVoucherDiscount = round(
                    (float) ($platformVoucher['allocations'][(int) $sellerId] ?? 0),
                    2
                );

                $buyerSubtotal = round(max(
                    0,
                    $buyerSaleSubtotal - $sellerVoucherDiscount - $platformVoucherDiscount
                ), 2);

                $shippingQuote = $this->freeShippingPromotions->checkoutQuote(
                    (int) $seller->id,
                    $sellerSubtotal,
                    $feePerSeller,
                    true
                );
                $originalDeliveryFee = round($feePerSeller, 2);
                $shippingDiscountAmount = round((float) ($shippingQuote['shipping_discount'] ?? 0), 2);
                $sellerDeliveryFee = round(max(0, $originalDeliveryFee - $shippingDiscountAmount), 2);

                foreach ($prepared['cart_items'] as $cartItem) {
                    $this->inventory->deductForOrder(
                        $prepared['locked_products'][(int) $cartItem->id],
                        $cartItem->seller_product_variant_id,
                        (int) $cartItem->quantity
                    );
                }

                $pickupAddress = trim((string) ($seller->pickup_address ?? ''));
                if ($pickupAddress === '') {
                    $pickupAddress = collect([
                        $seller->street_address ?? null,
                        $seller->barangay_name ?? null,
                        $seller->municipality_name ?? null,
                        $seller->province_name ?? null,
                        'Philippines',
                    ])->filter(fn ($part) => filled($part))->implode(', ');
                }

                $campaignSnapshot = [
                    'automatic_sales' => $prepared['automatic_campaigns'],
                    'voucher' => $platformVoucher['snapshot'] ?? null,
                ];

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
                    'items' => $prepared['order_items'],
                    'original_subtotal' => $sellerSubtotal,
                    'discount_amount' => $sellerVoucherDiscount,
                    'seller_receivable_subtotal' => $sellerReceivableSubtotal,
                    'voucher_code' => $sellerVoucher?->code,
                    'seller_voucher_id' => $sellerVoucher?->id,
                    'platform_sale_discount_amount' => $platformSaleDiscount,
                    'platform_voucher_discount_amount' => $platformVoucherDiscount,
                    'platform_discount_amount' => round($platformSaleDiscount + $platformVoucherDiscount, 2),
                    'platform_voucher_code' => $platformVoucher ? $voucherCode : null,
                    'platform_campaign_snapshot' => $campaignSnapshot,
                    'subtotal' => $buyerSubtotal,
                    'original_delivery_fee' => $originalDeliveryFee,
                    'shipping_discount_amount' => $shippingDiscountAmount,
                    'seller_free_shipping_promotion_id' => $shippingQuote['promotion_id'] ?? null,
                    'shipping_discount_funder' => $shippingDiscountAmount > 0
                        ? SellerFreeShippingPromotionService::FUNDER
                        : null,
                    'delivery_fee' => $sellerDeliveryFee,
                    'total' => round($buyerSubtotal + $sellerDeliveryFee, 2),
                    'pickup_name' => $seller->store_name ?: 'SARI Seller Store',
                    'pickup_address' => $pickupAddress !== '' ? $pickupAddress : 'Seller pickup address to be confirmed',
                    'status' => 'new',
                ]));

                if ($sellerVoucher) {
                    SellerVoucherRedemption::create([
                        'seller_voucher_id' => $sellerVoucher->id,
                        'marketplace_order_id' => $order->id,
                        'buyer_account_id' => $order->buyer_account_id,
                        'buyer_social_account_id' => $order->buyer_social_account_id,
                        'discount_amount' => $sellerVoucherDiscount,
                    ]);
                    $sellerVoucher->increment('used_count');
                }

                MarketplaceOrderEvent::create([
                    'marketplace_order_id' => $order->id,
                    'seller_account_id' => $seller->id,
                    'audience' => 'both',
                    'type' => 'new',
                    'title' => 'New Order Received',
                    'message' => 'Buyer checkout created order ' . $order->order_number . '.',
                    'status' => 'new',
                ]);

                // Open the buyer–seller confirmation thread atomically with checkout.
                $this->orderConfirmation->createForOrder($order);

                $created->push($order);
            }

            if ($voucherCode !== '' && $platformVoucher === null && !$sellerVoucherApplied) {
                throw ValidationException::withMessages([
                    'voucher_code' => 'That voucher code does not apply to this checkout.',
                ]);
            }

            if ($platformVoucher !== null) {
                $this->platformCampaigns->recordVoucherRedemption(
                    $platformVoucher['campaign'],
                    $checkoutReference,
                    $identityColumns,
                    (float) $platformVoucher['discount'],
                    $created->pluck('id')->map(fn ($id) => (int) $id)->all()
                );
            }

            $cartQuery->delete();

            return $created;
        }, 3);

        return redirect()
            ->route('buyer.orders')
            ->with('order_confirmation_url', route('buyer.messages', [
                'seller' => (int) $createdOrders->first()->seller_account_id,
            ]))
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
