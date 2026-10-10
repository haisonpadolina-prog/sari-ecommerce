@props([
    'product',
    'priority' => false,
    'interactive' => false,
    'showPromotions' => true,
    'showStock' => true,
    'showQuickAdd' => true,
    'featured' => false,
])

@php
    $productId = (int) ($product['id'] ?? 0);
    $productName = trim((string) ($product['name'] ?? 'Product'));
    $category = trim((string) ($product['category'] ?? 'General'));
    $brand = trim((string) ($product['brand'] ?? ''));

    $rating = max(0, min(5, (float) ($product['rating'] ?? 0)));
    $sold = max(0, (int) ($product['sold'] ?? 0));
    $stock = max(0, (int) ($product['stock'] ?? 0));

    $image = $product['image'] ?? null;
    $variations = $product['variations'] ?? [];
    $voucher = $product['voucher'] ?? null;
    $freeShipping = !empty($product['free_shipping']);
    $hasVariants = !empty($product['has_variants']);
    $codAvailable = !empty($product['cod'])
        || !empty($product['cash_on_delivery'])
        || !empty($product['is_cod'])
        || !empty($product['payment_cod'])
        || strtolower((string) ($product['delivery_mode'] ?? '')) === 'cod';

    $finalPrice = max(0, (float) ($product['price'] ?? 0));
    $oldPrice = isset($product['old_price']) && $product['old_price'] !== null
        ? max(0, (float) $product['old_price'])
        : 0;

    if ($oldPrice > $finalPrice && $oldPrice > 0) {
        $originalPrice = $oldPrice;
        $discountAmount = round($originalPrice - $finalPrice, 2);
        $discount = round(($discountAmount / $originalPrice) * 100);
    } else {
        $originalPrice = $finalPrice;
        $discountAmount = 0;
        $discount = 0;
    }

    $productUrl = route('buyer.product.details', ['product' => $productId]);

    $searchableName = strtolower($productName);
    $searchableCategory = strtolower($category);
    $searchableVariations = strtolower(implode('|', is_array($variations) ? $variations : []));

    $hasVoucher = is_array($voucher) && !empty($voucher['id']);
    $voucherId = $hasVoucher ? ($voucher['id'] ?? '') : '';
    $voucherMinimum = $hasVoucher ? ($voucher['minimum'] ?? '') : '';
    $voucherValue = $hasVoucher ? ($voucher['value'] ?? '') : '';

    $stockTone = match (true) {
        $stock <= 0 => 'text-[#c75353]',
        $stock <= 5 => 'text-[#a97120]',
        default => 'text-[#3f7e5a]',
    };

    $isNew = $rating <= 0 && $sold <= 0 && $stock > 0;
    $showOfferCluster = $showPromotions && ($discount > 0 || $codAvailable || $freeShipping);
@endphp

<style>
    .sari-product-card {
        min-height: 0;
        overflow: hidden;
        border: 1px solid #e8e2da !important;
        border-radius: 20px !important;
        background: #ffffff !important;
        padding: 0;
        box-shadow:
            0 14px 32px rgba(43,34,24,.085),
            0 4px 10px rgba(43,34,24,.038),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    .sari-buyer-card-media-button,
    .sari-buyer-card-media-link {
        display: block;
        width: 100%;
        border: 0;
        padding: 0;
        margin: 0;
        background: transparent;
        text-align: left;
        cursor: pointer;
        color: inherit;
        text-decoration: none;
    }

    .sari-buyer-card-media {
        position: relative;
        width: 100%;
        height: 196px;
        overflow: hidden;
        border: 0 !important;
        border-radius: 19px 19px 12px 12px;
        background: #f2f0ec;
    }

    .sari-buyer-card-media img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform .28s ease;
    }

    .sari-buyer-card-badges {
        position: absolute;
        top: 12px;
        left: 12px;
        right: 12px;
        z-index: 3;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        pointer-events: none;
    }

    .sari-buyer-card-badge-group {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 7px;
    }

    .sari-buyer-card-badge {
        display: inline-flex;
        min-height: 27px;
        align-items: center;
        justify-content: center;
        gap: 5px;
        border-radius: 999px;
        padding: 0 11px;
        font-size: 8px;
        line-height: 1;
        font-weight: 700;
        letter-spacing: -.015em;
        white-space: nowrap;
        box-shadow: 0 4px 12px rgba(25,22,18,.10);
    }

    .sari-buyer-card-badge--active {
        min-height: 23px;
        padding: 0 9px;
        border: 1px solid rgba(255,255,255,.06);
        background: #2b2a28;
        color: #e0a11a;
        font-size: 7.2px;
        font-weight: 800;
        box-shadow: 0 4px 10px rgba(28,25,22,.12);
    }

    .sari-buyer-discount-corner {
        position: absolute;
        top: -1px;
        right: -1px;
        z-index: 5;
        display: inline-flex;
        min-width: 56px;
        height: 30px;
        align-items: center;
        justify-content: center;
        padding: 0 10px 1px 11px;
        border: 0;
        border-radius: 0 19px 0 17px;
        background: #dda00b;
        color: #ffffff;
        font-size: 10px;
        line-height: 1;
        font-weight: 800;
        letter-spacing: -.025em;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
        box-shadow: 0 4px 10px rgba(126,84,7,.14);
        pointer-events: none;
    }

    .sari-buyer-discount-corner::before {
        content: none;
    }

    .sari-buyer-card-badge--out {
        border: 1px solid #f0c8c8;
        background: rgba(255,255,255,.96);
        color: #bf4d4d;
    }

    .sari-buyer-card-offer-cluster {
        position: absolute;
        left: 0;
        bottom: 0;
        z-index: 4;
        display: inline-flex;
        width: auto;
        max-width: 76%;
        align-items: stretch;
        gap: 0;
        filter: drop-shadow(0 5px 11px rgba(22,20,18,.16));
        pointer-events: none;
        isolation: isolate;
    }

    .sari-buyer-card-offer-item {
        --offer-bg: #333;
        position: relative;
        z-index: 1;
        display: inline-flex;
        min-width: 0;
        height: 28px;
        align-items: center;
        padding: 0 9px;
        color: #fff;
        white-space: nowrap;
        flex: 0 0 auto;
        background: transparent;
    }

    .sari-buyer-card-offer-item + .sari-buyer-card-offer-item {
        margin-left: -11px;
        padding-left: 15px;
    }

    .sari-buyer-card-offer-item::before {
        content: '';
        position: absolute;
        inset: 0 -2px;
        z-index: -1;
        background: var(--offer-bg);
        transform: skewX(-20deg);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.13);
    }

    .sari-buyer-card-offer-item:first-child::before {
        left: -7px;
        border-radius: 11px 0 0 0;
    }

    .sari-buyer-card-offer-item:last-child::before {
        right: -7px;
        border-radius: 0 10px 0 0;
    }

    .sari-buyer-card-offer-item--discount {
        --offer-bg: linear-gradient(135deg, #8f1730 0%, #b92f49 100%);
        width: 68px;
        justify-content: center;
    }

    .sari-buyer-card-offer-item--cod {
        --offer-bg: linear-gradient(135deg, #f6cf66 0%, #e2a91d 100%);
        width: 104px;
        padding-left: 19px !important;
        padding-right: 13px;
        color: #201b13;
    }

    .sari-buyer-card-offer-item--cod .sari-buyer-card-offer-main {
        font-size: 6.55px;
        letter-spacing: -.02em;
    }

    .sari-buyer-card-offer-item--shipping {
        --offer-bg: linear-gradient(135deg, #16283b 0%, #24384d 100%);
        width: 96px;
    }

    .sari-buyer-card-offer-item:first-child {
        padding-left: 9px;
    }

    .sari-buyer-card-offer-stack {
        display: flex;
        flex-direction: column;
        justify-content: center;
        line-height: 1;
    }

    .sari-buyer-card-offer-kicker {
        display: block;
        font-size: 6.4px;
        font-weight: 800;
        letter-spacing: .045em;
        text-transform: uppercase;
        opacity: .98;
    }

    .sari-buyer-card-offer-main {
        display: block;
        margin-top: 2px;
        overflow: hidden;
        font-size: 6.8px;
        font-weight: 700;
        letter-spacing: -.015em;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sari-buyer-card-offer-item--discount .sari-buyer-card-offer-main {
        margin-top: 1px;
        font-size: 6.9px;
        font-weight: 750;
    }

    .sari-buyer-card-body {
        display: flex;
        min-width: 0;
        flex: 1 1 auto;
        flex-direction: column;
        padding: 10px 14px 12px;
    }

    .sari-buyer-card-heading {
        display: grid;
        grid-template-columns: minmax(0,1fr) auto;
        align-items: start;
        gap: 12px;
    }

    .sari-buyer-card-title-button,
    .sari-buyer-card-title-link {
        display: block;
        width: 100%;
        min-width: 0;
        border: 0;
        padding: 0;
        background: transparent;
        color: inherit;
        text-align: left;
        text-decoration: none;
        cursor: pointer;
    }

    .sari-buyer-card-title {
        display: -webkit-box;
        overflow: hidden;
        margin: 0;
        color: #26211d;
        font-size: 13.5px;
        line-height: 1.32;
        font-weight: 650;
        letter-spacing: -.03em;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    .sari-buyer-card-meta {
        overflow: hidden;
        margin: 5px 0 0;
        color: #91887e;
        font-size: 8.7px;
        line-height: 1.35;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sari-buyer-card-price-wrap {
        min-width: 88px;
        text-align: right;
    }

    .sari-buyer-card-price {
        display: block;
        color: #bd7b00;
        font-size: 14.5px;
        line-height: 1.05;
        font-weight: 800;
        letter-spacing: -.035em;
    }

    .sari-buyer-card-old-price {
        display: block;
        margin-top: 5px;
        color: #aca49a;
        font-size: 7.5px;
        line-height: 1;
        text-decoration: line-through;
    }

    .sari-buyer-card-promo {
        position: relative;
        display: grid;
        grid-template-columns: .95fr 1.05fr;
        width: min(212px, 100%);
        height: 29px;
        min-height: 29px;
        max-width: 100%;
        align-self: flex-start;
        align-items: stretch;
        gap: 0;
        margin-top: 6px;
        overflow: visible;
        border: 0;
        border-radius: 999px;
        background-color: #202329;
        background-image: url('{{ asset('images/seller/flash-sale-strip.png') }}');
        background-repeat: no-repeat;
        background-position: center;
        background-size: 100% 100%;
        padding: 0;
        box-shadow: none;
        isolation: isolate;
    }

    .sari-buyer-card-promo-label,
    .sari-buyer-card-promo-copy {
        position: relative;
        z-index: 2;
        display: inline-flex;
        width: 100%;
        min-width: 0;
        height: 29px;
        min-height: 29px;
        align-items: center;
        border: 0;
        border-radius: 0;
        background: transparent;
        padding-block: 0;
        color: #fff;
        line-height: 1;
        white-space: nowrap;
        box-shadow: none;
        text-shadow: 0 1px 2px rgba(0,0,0,.34);
    }

    .sari-buyer-card-promo-label {
        justify-content: flex-start;
        padding-left: 12px;
        padding-right: 16px;
        font-size: 8.5px;
        font-weight: 800;
        letter-spacing: .002em;
    }

    .sari-buyer-card-promo-copy {
        justify-content: center;
        overflow: hidden;
        padding-left: 14px;
        padding-right: 9px;
        font-variant-numeric: tabular-nums;
        font-size: 7px;
        font-weight: 800;
        letter-spacing: .02em;
        text-overflow: ellipsis;
    }

    .sari-buyer-card-promo-copy[data-flash-sale-countdown] {
        font-feature-settings: 'tnum' 1, 'lnum' 1;
    }

    .sari-buyer-card-promo-bolt {
        display: none;
    }

    .sari-buyer-card-divider {
        width: 100%;
        height: 1px;
        margin: 8px 0 9px;
        background: #ebe5dd;
    }

    .sari-buyer-card-footer {
        display: flex;
        min-width: 0;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
    }

    .sari-buyer-card-stock {
        margin: 0;
        font-size: 10px;
        line-height: 1.2;
        font-weight: 700;
    }

    .sari-buyer-card-secondary {
        overflow: hidden;
        margin: 5px 0 0;
        color: #9b9389;
        font-size: 7.5px;
        line-height: 1.3;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sari-buyer-card-actions {
        display: flex;
        flex: 0 0 auto;
        align-items: center;
        gap: 7px;
    }

    .sari-buyer-card-icon-action {
        position: relative;
        display: inline-flex;
        width: 31px;
        height: 31px;
        flex: 0 0 31px;
        align-items: center;
        justify-content: center;
        border: 1px solid #e5ddd4;
        border-radius: 8px;
        background: #fff;
        color: #d68f08;
        text-decoration: none;
        box-shadow: 0 3px 9px rgba(45,37,28,.035);
        transition: transform .16s ease,border-color .16s ease,color .16s ease,background .16s ease,box-shadow .16s ease;
    }

    .sari-buyer-card-icon-action svg {
        width: 16px;
        height: 16px;
    }

    .sari-buyer-card-buy {
        display: inline-flex;
        height: 31px;
        min-width: 84px;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 8px;
        background: #d68f08;
        padding: 0 12px;
        color: #fff;
        font-size: 8.5px;
        line-height: 1;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 7px 16px rgba(205,137,11,.16);
        transition: transform .16s ease,background .16s ease,box-shadow .16s ease;
    }

    .sari-buyer-card-view-only {
        display: inline-flex;
        height: 38px;
        align-items: center;
        justify-content: center;
        border: 1px solid #e3cfaa;
        border-radius: 11px;
        background: #fffaf0;
        padding: 0 14px;
        color: #a86d08;
        font-size: 7.5px;
        line-height: 1;
        font-weight: 800;
        text-decoration: none;
    }

    .sari-buyer-card-icon-action:disabled,
    .sari-buyer-card-buy:disabled {
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }

    .sari-buyer-card-icon-action:disabled {
        border-color: #ddd9d3;
        background: #f4f2ef;
        color: #aaa59d;
    }

    .sari-buyer-card-buy:disabled {
        background: #d3d0cb;
        color: #fff;
    }

    .sari-buyer-card-media-button:focus-visible,
    .sari-buyer-card-media-link:focus-visible,
    .sari-buyer-card-title-button:focus-visible,
    .sari-buyer-card-title-link:focus-visible,
    .sari-buyer-card-icon-action:focus-visible,
    .sari-buyer-card-buy:focus-visible {
        outline: 2px solid rgba(214,143,8,.35);
        outline-offset: 2px;
    }

    @media (hover:hover) and (pointer:fine) {
        .sari-product-card:hover {
            transform: translateY(-3px);
            border-color: #ded5c9 !important;
            box-shadow:
                0 20px 38px rgba(43,34,24,.10),
                0 6px 14px rgba(43,34,24,.045) !important;
        }

        .sari-product-card:hover .sari-buyer-card-media img {
            transform: scale(1.018);
        }

        .sari-buyer-card-title-link:hover .sari-buyer-card-title {
            color: #a86d08;
        }

        .sari-buyer-card-icon-action:hover {
            transform: translateY(-1px);
            border-color: #d8a63f;
            background: #fffaf0;
            color: #b67508;
            box-shadow: 0 6px 13px rgba(201,137,14,.10);
        }

        .sari-buyer-card-buy:hover {
            transform: translateY(-1px);
            background: #c58105;
            box-shadow: 0 9px 18px rgba(193,123,5,.20);
        }
    }

    @media (min-width: 1024px) {
        .sari-product-card {
            height: 356px;
            min-height: 356px;
            max-height: 356px;
        }

        .sari-buyer-card-media {
            height: 194px;
        }
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        .sari-product-card {
            height: 346px;
            min-height: 346px;
            max-height: 346px;
            padding: 0;
            border-radius: 17px !important;
        }

        .sari-buyer-card-media {
            height: 184px;
            border-radius: 16px 16px 11px 11px;
        }

        .sari-buyer-card-body {
            padding: 9px 13px 10px;
        }
    }

    @media (max-width: 639px) {
        .sari-product-card {
            padding: 0;
            border-radius: 18px !important;
        }

        .sari-buyer-card-media {
            height: 196px;
            border-radius: 17px 17px 11px 11px;
        }

        .sari-buyer-card-offer-item {
            height: 26px;
            padding-inline: 8px;
        }

        .sari-buyer-card-offer-item + .sari-buyer-card-offer-item {
            margin-left: -10px;
            padding-left: 14px;
        }

        .sari-buyer-card-offer-item--discount {
            width: 64px;
        }

        .sari-buyer-card-offer-item--cod {
            width: 80px;
        }

        .sari-buyer-card-offer-item--shipping {
            width: 90px;
        }

        .sari-buyer-card-offer-kicker {
            font-size: 5.9px;
        }

        .sari-buyer-card-offer-main,
        .sari-buyer-card-offer-item--discount .sari-buyer-card-offer-main {
            font-size: 6.25px;
        }

        .sari-buyer-card-body {
            padding: 11px 13px 11px;
        }

        .sari-buyer-card-heading {
            gap: 10px;
        }

        .sari-buyer-card-title {
            font-size: 13.5px;
        }

        .sari-buyer-card-price {
            font-size: 14.5px;
        }

        .sari-buyer-card-price-wrap {
            min-width: 76px;
        }

        .sari-buyer-card-promo {
            width: min(198px, 100%);
            height: 28px;
            min-height: 28px;
        }

        .sari-buyer-card-promo-label,
        .sari-buyer-card-promo-copy {
            height: 28px;
            min-height: 28px;
        }

        .sari-buyer-card-promo-label {
            padding-left: 11px;
            padding-right: 15px;
            font-size: 8px;
        }

        .sari-buyer-card-promo-copy {
            padding-left: 14px;
            padding-right: 8px;
            font-size: 7.55px;
        }

        .sari-buyer-card-icon-action {
            width: 29px;
            height: 29px;
            flex-basis: 29px;
        }

        .sari-buyer-card-buy {
            height: 29px;
            min-width: 78px;
            padding: 0 10px;
        }
    }

</style>

<article
    style="font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;"
    data-product-card
    data-product-id="{{ $productId }}"
    data-product-name="{{ $searchableName }}"
    data-product-category="{{ $searchableCategory }}"
    data-product-price="{{ $finalPrice }}"
    data-product-original-price="{{ $originalPrice }}"
    data-product-discount="{{ $discount }}"
    data-product-rating="{{ $rating }}"
    data-product-sold="{{ $sold }}"
    data-product-stock="{{ $stock }}"
    data-product-voucher="{{ $voucherId }}"
    data-product-voucher-minimum="{{ $voucherMinimum }}"
    data-product-voucher-value="{{ $voucherValue }}"
    data-product-free-shipping="{{ $freeShipping ? '1' : '0' }}"
    data-product-variations="{{ $searchableVariations }}"
    data-product-has-variants="{{ $hasVariants ? '1' : '0' }}"
    data-product-url="{{ $productUrl }}"
    {{ $attributes->merge([
        'class' => '
            sari-product-card group flex h-full min-w-0 flex-col overflow-hidden rounded-[13px]
            border border-[#e7e1da] bg-white
            shadow-[0_6px_16px_rgba(43,34,24,.028)]
            transition-[border-color,box-shadow,transform] duration-200
            hover:border-[#ded2c1] hover:shadow-[0_10px_24px_rgba(43,34,24,.05)]
        ',
    ]) }}
>
    @if ($interactive)
        <button
            type="button"
            data-open-product="{{ $productId }}"
            class="sari-buyer-card-media-button"
            aria-label="View {{ $productName }}"
        >
    @else
        <a href="{{ $productUrl }}" class="sari-buyer-card-media-link" aria-label="View {{ $productName }}">
    @endif
        <div class="sari-product-media sari-buyer-card-media">
            @if ($image)
                <img
                    src="{{ asset($image) }}"
                    alt="{{ $productName }}"
                    loading="{{ $priority ? 'eager' : 'lazy' }}"
                    fetchpriority="{{ $priority ? 'high' : 'auto' }}"
                    decoding="{{ $priority ? 'sync' : 'async' }}"
                    draggable="false"
                >
            @else
                <div class="grid h-full w-full place-items-center bg-[#f2f0ec] text-[#aaa198]">
                    <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.45" aria-hidden="true">
                        <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                        <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                    </svg>
                </div>
            @endif

            <div class="sari-buyer-card-badges">
                <div class="sari-buyer-card-badge-group">
                    @if ($stock > 0)
                        <span class="sari-buyer-card-badge sari-buyer-card-badge--active">MALL</span>
                    @else
                        <span class="sari-buyer-card-badge sari-buyer-card-badge--out">Out of Stock</span>
                    @endif
                </div>

            </div>

            @if ($discount > 0)
                <span class="sari-buyer-discount-corner" aria-label="{{ number_format($discount, 0) }} percent discount">
                    -{{ number_format($discount, 0) }}%
                </span>
            @endif

            @if ($showOfferCluster)
                <div class="sari-buyer-card-offer-cluster" aria-label="Product offer badges">
                    @if ($discount > 0)
                        <span class="sari-buyer-card-offer-item sari-buyer-card-offer-item--discount">
                            <span class="sari-buyer-card-offer-stack">
                                <span class="sari-buyer-card-offer-kicker">MEGA</span>
                                <span class="sari-buyer-card-offer-main">Discount</span>
                            </span>
                        </span>
                    @endif

                    @if ($codAvailable)
                        <span class="sari-buyer-card-offer-item sari-buyer-card-offer-item--cod">
                            <span class="sari-buyer-card-offer-stack">
                                <span class="sari-buyer-card-offer-kicker">COD</span>
                                <span class="sari-buyer-card-offer-main">Cash on Delivery</span>
                            </span>
                        </span>
                    @endif

                    @if ($freeShipping)
                        <span class="sari-buyer-card-offer-item sari-buyer-card-offer-item--shipping">
                            <span class="sari-buyer-card-offer-stack">
                                <span class="sari-buyer-card-offer-kicker">XTRA</span>
                                <span class="sari-buyer-card-offer-main">Free Shipping</span>
                            </span>
                        </span>
                    @endif
                </div>
            @endif
        </div>
    @if ($interactive)
        </button>
    @else
        </a>
    @endif

    <div class="sari-buyer-card-body">
        <div class="sari-buyer-card-heading">
            <div class="min-w-0">
                @if ($interactive)
                    <button
                        type="button"
                        data-open-product="{{ $productId }}"
                        class="sari-buyer-card-title-button"
                    >
                        <span class="sari-buyer-card-title">{{ $productName }}</span>
                    </button>
                @else
                    <a href="{{ $productUrl }}" class="sari-buyer-card-title-link">
                        <span class="sari-buyer-card-title">{{ $productName }}</span>
                    </a>
                @endif

                <p class="sari-buyer-card-meta">
                    {{ $category }}@if ($brand !== '') · {{ $brand }}@endif
                </p>
            </div>

            <div class="sari-buyer-card-price-wrap">
                <strong class="sari-buyer-card-price">₱{{ number_format($finalPrice, 2) }}</strong>
                @if ($originalPrice > $finalPrice)
                    <span class="sari-buyer-card-old-price">₱{{ number_format($originalPrice, 2) }}</span>
                @endif
            </div>
        </div>

        @if ($showPromotions && $discount > 0 && !empty($product['flash_sale_ends_at']))
            <div
                class="sari-buyer-card-promo"
                data-buyer-flash-sale
                data-flash-sale-ends-at="{{ $product['flash_sale_ends_at'] }}"
                aria-label="Flash Sale ends at {{ $product['flash_sale_ends_at'] }}"
                title="{{ number_format($discount, 0) }}% off · Flash Sale countdown"
            >
                <span class="sari-buyer-card-promo-label">Flash Sale</span>
                <span class="sari-buyer-card-promo-bolt" aria-hidden="true">⚡</span>
                <span class="sari-buyer-card-promo-copy" data-flash-sale-countdown role="timer" aria-live="off">--:--:--</span>
            </div>
        @endif

        <div class="mt-auto">
            <div class="sari-buyer-card-divider"></div>

            <div class="sari-buyer-card-footer">
                <div class="min-w-0 flex-1">
                    @if ($showStock)
                        <p class="sari-buyer-card-stock {{ $stockTone }}">
                            {{ $stock > 0 ? number_format($stock) . ' in stock' : 'Out of Stock' }}
                        </p>
                    @endif

                    <p class="sari-buyer-card-secondary">
                        @if ($rating > 0)
                            <span class="font-semibold text-[#c98a10]">★ {{ number_format($rating, 1) }}</span>
                        @endif
                        @if ($rating > 0 && $sold > 0)
                            <span class="mx-1 text-[#d4cec5]">·</span>
                        @endif
                        @if ($sold > 0)
                            {{ number_format($sold) }} sold
                        @elseif ($stock > 0)
                            Available now
                        @else
                            Currently unavailable
                        @endif
                    </p>
                </div>

                @if (!$showQuickAdd)
                    <a href="{{ $productUrl }}" class="sari-buyer-card-view-only">View</a>
                @endif

                @if ($showQuickAdd)
                    <div class="sari-buyer-card-actions">
                        @if ($stock > 0)
                            <button
                                type="button"
                                data-quick-add="{{ $productId }}"
                                aria-label="Add {{ $productName }} to cart"
                                title="Add to Cart"
                                class="sari-buyer-card-icon-action"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h8.7a2 2 0 0 0 2-1.6L21 8H7"></path>
                                    <circle cx="9" cy="20" r="1.4"></circle>
                                    <circle cx="18" cy="20" r="1.4"></circle>
                                </svg>
                            </button>

                            <a
                                href="{{ $productUrl }}?buy_now=1"
                                aria-label="Buy {{ $productName }} now"
                                class="sari-buyer-card-buy"
                            >
                                Buy Now
                            </a>
                        @else
                            <button
                                type="button"
                                disabled
                                aria-label="{{ $productName }} is out of stock"
                                class="sari-buyer-card-icon-action"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h8.7a2 2 0 0 0 2-1.6L21 8H7"></path>
                                    <path d="m7 7 11 11"></path>
                                </svg>
                            </button>

                            <button type="button" disabled class="sari-buyer-card-buy">Out of Stock</button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</article>

@once
    <script>
        (() => {
            const TIMER_KEY = '__sariBuyerProductFlashSaleClock';

            const formatRemaining = (milliseconds) => {
                const totalSeconds = Math.max(0, Math.floor(milliseconds / 1000));
                const days = Math.floor(totalSeconds / 86400);
                const hours = Math.floor((totalSeconds % 86400) / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;
                const pad = (value) => String(value).padStart(2, '0');
                const clock = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;

                return days > 0 ? `${days}d ${clock}` : clock;
            };

            const syncBuyerFlashSales = () => {
                document.querySelectorAll('[data-buyer-flash-sale]').forEach((saleNode) => {
                    const countdown = saleNode.querySelector('[data-flash-sale-countdown]');
                    const endMs = Date.parse(saleNode.dataset.flashSaleEndsAt || '');
                    if (!countdown || !Number.isFinite(endMs)) {
                        if (countdown) countdown.textContent = 'Limited time';
                        return;
                    }

                    const remaining = endMs - Date.now();
                    countdown.textContent = remaining > 0 ? formatRemaining(remaining) : 'Ended';
                    saleNode.classList.toggle('is-ended', remaining <= 0);
                });
            };

            const startBuyerFlashSaleClock = () => {
                if (window[TIMER_KEY]) {
                    window.clearInterval(window[TIMER_KEY]);
                }

                syncBuyerFlashSales();
                window[TIMER_KEY] = window.setInterval(syncBuyerFlashSales, 1000);
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', startBuyerFlashSaleClock, { once: true });
            } else {
                startBuyerFlashSaleClock();
            }

            document.addEventListener('livewire:navigated', startBuyerFlashSaleClock);
        })();
    </script>
@endonce

