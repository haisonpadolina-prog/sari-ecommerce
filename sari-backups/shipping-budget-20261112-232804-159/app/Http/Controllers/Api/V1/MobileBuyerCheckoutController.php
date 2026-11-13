<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Catalog\SellerProductVariant;
use App\Models\Orders\BuyerCartItem;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Models\Promotions\SellerVoucherRedemption;
use App\Services\Buyer\BuyerCartService;
use App\Services\Messaging\BuyerSellerOrderConfirmationService;
use App\Services\Products\ProductVariantInventoryService;
use App\Services\Promotions\PlatformCampaignService;
use App\Services\Promotions\SellerFreeShippingPromotionService;
use App\Services\Seller\SellerVoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MobileBuyerCheckoutController extends Controller
{
    public function __construct(
        private readonly BuyerCartService $cart,
        private readonly ProductVariantInventoryService $inventory,
        private readonly SellerVoucherService $vouchers,
        private readonly SellerFreeShippingPromotionService $freeShippingPromotions,
        private readonly PlatformCampaignService $platformCampaigns,
        private readonly BuyerSellerOrderConfirmationService $orderConfirmation,
    ) {}

    public function preview(Request $request): JsonResponse
    {
        $buyer = $this->buyer($request);
        $validated = $request->validate([
            'item_ids' => ['nullable', 'array'],
            'item_ids.*' => ['integer', 'exists:buyer_cart_items,id'],
        ]);

        $items = BuyerCartItem::query()
            ->where('buyer_account_id', $buyer->id)
            ->whereNull('buyer_social_account_id')
            ->when(!empty($validated['item_ids']), fn ($query) => $query->whereIn('id', $validated['item_ids']))
            ->with(['product.seller', 'variant'])
            ->get()
            ->filter(fn (BuyerCartItem $item) => $item->product)
            ->values();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }

        $unavailable = $items
            ->map(fn (BuyerCartItem $item) => $this->cart->availability($item))
            ->first(fn (array $availability) => !$availability['selectable']);

        if ($unavailable) {
            throw ValidationException::withMessages([
                'cart' => $unavailable['reason'] ?: 'One or more cart items are unavailable.',
            ]);
        }

        $shipping = $this->cart->shippingSummary($items);
        $subtotal = $this->cart->subtotal($items);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items->map(fn (BuyerCartItem $item) => [
                    'id' => (int) $item->id,
                    'product_id' => (int) $item->seller_product_id,
                    'name' => (string) $item->product->name,
                    'variant_id' => $item->seller_product_variant_id ? (int) $item->seller_product_variant_id : null,
                    'variant_label' => $this->cart->variantLabel($item),
                    'quantity' => (int) $item->quantity,
                    'unit_price' => $this->cart->unitPrice($item),
                    'line_total' => $this->cart->lineTotal($item),
                    'store_name' => (string) ($item->product->seller?->store_name ?? ''),
                ])->values(),
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'grand_total' => round($subtotal + (float) $shipping['delivery_fee'], 2),
                'addresses' => $buyer->addresses()
                    ->orderByDesc('is_default')
                    ->orderByDesc('updated_at')
                    ->get()
                    ->map(fn ($address) => [
                        'id' => (int) $address->id,
                        'label' => $address->label,
                        'recipient_name' => $address->recipient_name,
                        'contact_no' => $address->contact_no,
                        'full_address' => $address->fullAddress(),
                        'is_default' => (bool) $address->is_default,
                    ])->values(),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function store(Request $request): JsonResponse
    {
        $buyer = $this->buyer($request);

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

        $createdOrders = DB::transaction(function () use ($buyer, $validated) {
            $cartQuery = BuyerCartItem::query()
                ->where('buyer_account_id', $buyer->id)
                ->whereNull('buyer_social_account_id');

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

            $identityColumns = [
                'buyer_account_id' => $buyer->id,
                'buyer_social_account_id' => null,
            ];
            $checkoutReference = 'CHK-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5));
            $feePerSeller = $this->cart->deliveryFeePerSeller();
            $voucherCode = strtoupper(trim((string) ($validated['voucher_code'] ?? '')));
            $preparedGroups = collect();
            $platformVoucherLines = collect();

            foreach ($items->groupBy(fn (BuyerCartItem $item) => (int) $item->product->seller_account_id) as $sellerId => $group) {
                $seller = SellerAccount::query()->findOrFail((int) $sellerId);

                if (!$seller->isBuyerAvailable()) {
                    throw ValidationException::withMessages([
                        'cart' => ($seller->store_name ?: 'This Seller').' is temporarily not accepting orders.',
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
                        'buyer_account_id' => $buyer->id,
                        'buyer_social_account_id' => null,
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
                    'message' => 'Buyer checkout created order '.$order->order_number.'.',
                    'status' => 'new',
                ]);

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

        return response()->json([
            'success' => true,
            'message' => $createdOrders->count() === 1
                ? 'Order placed successfully.'
                : $createdOrders->count().' seller orders were created successfully.',
            'data' => [
                'orders' => $createdOrders->map(fn (MarketplaceOrder $order) => [
                    'id' => (int) $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'total' => (float) $order->total,
                ])->values(),
            ],
        ], 201);
    }

    private function buyer(Request $request): BuyerAccount
    {
        /** @var BuyerAccount $buyer */
        $buyer = $request->user();
        return $buyer;
    }

    private function newOrderNumber(): string
    {
        do {
            $number = 'SARI-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (MarketplaceOrder::query()->where('order_number', $number)->exists());

        return $number;
    }
}
