@extends('layouts.buyer')

@section('title', ($product['name'] ?? 'Product') . ' — SARI')
@section('page-title', 'Product Details')

@section('content')
@include('components.buyer.header')

@php
    /*
    |--------------------------------------------------------------------------
    | BUYER PRODUCT DETAILS
    |--------------------------------------------------------------------------
    | This page receives:
    | - $product  : buyer-safe product array from BuyerCatalogService::find()
    | - $products : approved buyer catalog used for related products
    |
    | IMPORTANT:
    | This view is dedicated to the selected marketplace product.
    */
    $product = is_array($product ?? null) ? $product : [];
    $products = is_array($products ?? null) ? $products : [];

    $productId = (int) ($product['id'] ?? 0);
    $productName = trim((string) ($product['name'] ?? 'Product'));
    $category = trim((string) ($product['category'] ?? 'General'));
    $brand = trim((string) ($product['brand'] ?? ''));
    $sku = trim((string) ($product['sku'] ?? ''));
    $description = trim((string) ($product['description'] ?? ''));

    $price = (float) ($product['price'] ?? 0);
    $oldPrice = isset($product['old_price']) && $product['old_price'] !== null
        ? (float) $product['old_price']
        : null;

    $discountPercent = (float) ($product['discount_percent'] ?? 0);
    $stock = max(0, (int) ($product['stock'] ?? 0));
    $rating = (float) ($product['rating'] ?? 0);
    $ratingCount = max(0, (int) ($product['rating_count'] ?? 0));
    $sold = max(0, (int) ($product['sold'] ?? 0));

    $hasVariants = (bool) ($product['has_variants'] ?? false);
    $variants = collect($product['variants'] ?? [])->values();
    $reviews = collect($product['reviews'] ?? [])->values();
    $optionGroups = is_array($product['option_groups'] ?? null)
        ? $product['option_groups']
        : [];

    $shopName = trim((string) ($product['shop_name'] ?? $product['store_name'] ?? 'SARI Seller Store'));
    $shopSlug = trim((string) ($product['shop_slug'] ?? ''));
    $sellerId = (int) ($product['seller_account_id'] ?? 0);
    $sellerAvatarRaw = trim((string) ($product['seller_profile_image'] ?? $product['shop_logo'] ?? ''));

    $freeShippingPromotion = is_array($product['free_shipping_promotion'] ?? null)
        ? $product['free_shipping_promotion']
        : null;

    // Keep the detail-page promo badges aligned with the shared product card.
    // Checkout remains authoritative for final promotion eligibility.
    $freeShipping = $freeShippingPromotion !== null || (bool) ($product['free_shipping'] ?? false);
    $freeShippingMinimum = max(
        0,
        (float) ($freeShippingPromotion['minimum_order_amount'] ?? 0)
    );

    $codAvailable = !empty($product['cod'])
        || !empty($product['cash_on_delivery'])
        || !empty($product['is_cod'])
        || !empty($product['payment_cod'])
        || strtolower((string) ($product['delivery_mode'] ?? '')) === 'cod';

    $showOfferCluster = $discountPercent > 0 || $codAvailable || $freeShipping;

    $condition = trim((string) ($product['condition'] ?? ''));
    $preparationDays = $product['preparation_days'] ?? null;
    $package = is_array($product['package'] ?? null) ? $product['package'] : [];

    $imageUrl = function ($path) {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    };

    $sellerAvatar = $sellerAvatarRaw !== '' ? $imageUrl($sellerAvatarRaw) : null;
    $sellerInitial = strtoupper(mb_substr($shopName !== '' ? $shopName : 'S', 0, 1));

    $gallery = collect($product['images'] ?? [])
        ->filter(fn ($item) => trim((string) $item) !== '')
        ->map(fn ($item) => $imageUrl($item))
        ->filter()
        ->unique()
        ->values();

    $mainImage = $imageUrl($product['image'] ?? null);

    if ($mainImage && !$gallery->contains($mainImage)) {
        $gallery->prepend($mainImage);
    }

    $mainImage = $gallery->first() ?: $mainImage;

    /*
     * Variant selector data is kept structured instead of rendering every
     * Size × Color combination as one flat button. Size is intentionally
     * first, Color second, while any additional seller option groups follow.
     */
    $variantGroupPosition = 0;
    $variantOptionGroups = collect($optionGroups)
        ->map(function ($values, $name) use (&$variantGroupPosition) {
            $groupName = trim((string) $name);
            $normalized = strtolower($groupName);
            $position = $variantGroupPosition++;

            $priority = match (true) {
                $normalized === 'size' || str_contains($normalized, 'size') => 10,
                $normalized === 'color' || $normalized === 'colour'
                    || str_contains($normalized, 'color')
                    || str_contains($normalized, 'colour') => 20,
                default => 100 + $position,
            };

            return [
                'name' => $groupName,
                'values' => collect((array) $values)
                    ->map(fn ($value) => trim((string) $value))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all(),
                'priority' => $priority,
                'position' => $position,
            ];
        })
        ->filter(fn (array $group) => $group['name'] !== '' && !empty($group['values']))
        ->sortBy(fn (array $group) => sprintf('%04d-%04d', $group['priority'], $group['position']))
        ->values();

    $variantMatrix = $variants
        ->map(function ($variant) use ($imageUrl) {
            $options = is_array($variant['options'] ?? null)
                ? $variant['options']
                : [];

            return [
                'id' => (int) ($variant['id'] ?? 0),
                'label' => trim((string) ($variant['label'] ?? '')),
                'options' => collect($options)
                    ->mapWithKeys(fn ($value, $key) => [
                        trim((string) $key) => trim((string) $value),
                    ])
                    ->filter(fn ($value, $key) => $key !== '' && $value !== '')
                    ->all(),
                'price' => (float) ($variant['price'] ?? 0),
                'old_price' => isset($variant['old_price']) && $variant['old_price'] !== null
                    ? (float) $variant['old_price']
                    : null,
                'stock' => max(0, (int) ($variant['stock'] ?? 0)),
                'available' => (bool) ($variant['available'] ?? false),
                'image' => $imageUrl($variant['image'] ?? null),
            ];
        })
        ->filter(fn (array $variant) => $variant['id'] > 0)
        ->values();

    $firstVariantGroupName = trim((string) ($variantOptionGroups->first()['name'] ?? 'option'));
    $variantOptionGroupsPayload = $variantOptionGroups
        ->map(fn (array $group) => [
            'name' => $group['name'],
            'values' => $group['values'],
        ])
        ->values()
        ->all();

    /*
     * Buyer color options are rendered as real visual swatches. Values are
     * resolved through a safe allow-list (plus HEX colors) so seller-entered
     * option text never becomes arbitrary CSS.
     */
    $isColorOptionGroup = static function ($name): bool {
        $normalized = strtolower(trim((string) $name));

        return $normalized === 'color'
            || $normalized === 'colour'
            || str_contains($normalized, 'color')
            || str_contains($normalized, 'colour');
    };

    $resolveColorSwatch = static function ($value): string {
        $raw = trim((string) $value);

        if (preg_match('/^#[0-9a-fA-F]{3,8}$/', $raw) === 1) {
            return $raw;
        }

        $token = strtolower(preg_replace('/[^a-z0-9]+/i', '', $raw) ?? '');

        $colors = [
            'black' => '#111827',
            'white' => '#ffffff',
            'gray' => '#9ca3af',
            'grey' => '#9ca3af',
            'silver' => '#c0c0c0',
            'red' => '#ef4444',
            'maroon' => '#800000',
            'orange' => '#f97316',
            'yellow' => '#facc15',
            'gold' => '#d4a017',
            'green' => '#22c55e',
            'darkgreen' => '#166534',
            'lightgreen' => '#86efac',
            'forestgreen' => '#228b22',
            'mintgreen' => '#98ff98',
            'lime' => '#84cc16',
            'olive' => '#808000',
            'blue' => '#2563eb',
            'darkblue' => '#1e3a8a',
            'lightblue' => '#60a5fa',
            'skyblue' => '#38bdf8',
            'royalblue' => '#4169e1',
            'navy' => '#000080',
            'teal' => '#0d9488',
            'cyan' => '#06b6d4',
            'aqua' => '#06b6d4',
            'purple' => '#9333ea',
            'violet' => '#8b5cf6',
            'indigo' => '#4f46e5',
            'pink' => '#ec4899',
            'babypink' => '#f9a8d4',
            'rose' => '#f43f5e',
            'brown' => '#92400e',
            'beige' => '#e8d8b0',
            'tan' => '#d2b48c',
            'cream' => '#fffdd0',
            'khaki' => '#c3b091',
            'multicolor' => 'conic-gradient(#ef4444, #facc15, #22c55e, #2563eb, #9333ea, #ef4444)',
            'multicolour' => 'conic-gradient(#ef4444, #facc15, #22c55e, #2563eb, #9333ea, #ef4444)',
            'rainbow' => 'conic-gradient(#ef4444, #facc15, #22c55e, #2563eb, #9333ea, #ef4444)',
        ];

        return $colors[$token] ?? '#d1d5db';
    };

    $relatedProducts = collect($products)
        ->filter(fn ($item) => is_array($item) && (int) ($item['id'] ?? 0) !== $productId)
        ->sortByDesc(fn ($item) => strtolower((string) ($item['category'] ?? '')) === strtolower($category))
        ->take(4)
        ->values();

    $packageParts = [];
    if (($package['weight_kg'] ?? null) !== null) {
        $packageParts[] = rtrim(rtrim(number_format((float) $package['weight_kg'], 2, '.', ''), '0'), '.') . ' kg';
    }

    $dimensions = [];
    foreach (['length_cm', 'width_cm', 'height_cm'] as $key) {
        if (($package[$key] ?? null) !== null) {
            $dimensions[] = rtrim(rtrim(number_format((float) $package[$key], 1, '.', ''), '0'), '.');
        }
    }

    if (count($dimensions) === 3) {
        $packageParts[] = implode(' × ', $dimensions) . ' cm';
    }

    $formatPeso = fn ($value) => '₱' . number_format((float) $value, 2);
@endphp

<style>
    .buyer-product-page,
    .buyer-product-page * {
        font-family: "Poppins", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .buyer-product-page {
        color: #2f2923;
    }

    .buyer-product-card {
        border: 1px solid #e8e0d5;
        background: #fff;
        box-shadow:
            0 12px 28px rgba(45,34,22,.045),
            0 2px 8px rgba(45,34,22,.018);
    }

    /* ============================================================
       HERO
    ============================================================ */
    .sari-v7-hero-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        align-items: start;
        width: 100%;
    }

    /* ============================================================
       GALLERY — IMAGE FIRST, NO BIG OUTER CARD
    ============================================================ */
    .sari-v7-gallery-area,
    .sari-v7-gallery-layout,
    .sari-v7-gallery-main {
        width: 100%;
        min-width: 0;
        margin: 0;
        padding: 0;
        border: 0;
        background: transparent;
        box-shadow: none;
    }

    .sari-v7-gallery-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        align-items: start;
    }

    .sari-v7-gallery-thumbs {
        order: 2;
        display: flex;
        gap: 9px;
        overflow-x: auto;
        padding: 2px 1px 4px;
        scrollbar-width: thin;
        scrollbar-color: #d8c6a3 transparent;
    }

    .sari-v7-gallery-thumb {
        flex: 0 0 auto;
        width: 66px;
        height: 66px;
        padding: 0;
        border: 0;
        border-radius: 11px;
        background: transparent;
        cursor: pointer;
    }

    .sari-v7-gallery-thumb img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: 1px solid #e4ddd3;
        border-radius: 11px;
        background: #f7f4ef;
        transition:
            border-color .16s ease,
            box-shadow .16s ease,
            opacity .16s ease;
    }

    .sari-v7-gallery-thumb:hover img {
        border-color: #d3b26e;
    }

    .sari-v7-gallery-thumb.is-active img {
        border-color: #c98b13;
        box-shadow: 0 0 0 2px rgba(201,139,19,.14);
    }

    .sari-v7-gallery-main {
        order: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .sari-v7-main-photo {
        display: block;
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 610px;
        object-fit: contain;
        object-position: center;
        border: 0;
        border-radius: 16px;
        background: transparent;
        box-shadow: none;
        cursor: zoom-in;
    }

    .sari-v7-main-photo-placeholder {
        display: grid;
        width: 100%;
        min-height: 410px;
        place-items: center;
        border: 1px dashed #ddd4c8;
        border-radius: 16px;
        background: #fcfbf8;
        color: #aaa198;
    }

    .sari-v7-main-photo-placeholder.hidden {
        display: none !important;
    }

    .sari-v7-gallery-meta {
        display: flex;
        width: 100%;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 9px;
        margin-top: 12px;
    }

    .sari-v7-gallery-meta-left,
    .sari-v7-gallery-meta-right {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 7px;
    }

    /* Gallery status + View image are intentionally plain text controls. */
    .sari-v7-gallery-meta-right {
        gap:12px;
    }

    .sari-v7-gallery-meta-right .sari-v7-gallery-count {
        min-height:0 !important;
        border:0 !important;
        border-radius:0 !important;
        background:transparent !important;
        padding:0 !important;
        color:#364152 !important;
        box-shadow:none !important;
        font-size:9.5px !important;
        line-height:1.35 !important;
        font-weight:650 !important;
    }

    #buyerProductZoomButton {
        cursor:pointer;
        transition:color 120ms ease !important;
    }

    #buyerProductZoomButton:hover {
        color:#171f2b !important;
        text-decoration:underline;
        text-underline-offset:3px;
    }

    .sari-v7-shipping-pill,
    .sari-v7-gallery-count,
    .sari-v7-gallery-discount {
        display: inline-flex;
        min-height: 31px;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 0 11px;
        font-size: 9px;
        font-weight: 700;
    }

    .sari-v7-shipping-pill {
        border: 1px solid #bedec8;
        background: #eff9f2;
        color: #347451;
    }

    .sari-v7-gallery-count {
        border: 1px solid #e2dbd1;
        background: #fff;
        color: #6f665d;
    }

    .sari-v7-gallery-discount {
        border: 1px solid #ead09a;
        background: #fff4d7;
        color: #9b6208;
    }

    /* ============================================================
       BUYING PANEL
    ============================================================ */
    .sari-v7-buying-panel {
        width: 100%;
        min-width: 0;
        border: 1px solid #e7dfd4;
        border-radius: 20px;
        background: #fff;
        padding: 22px;
        box-shadow:
            0 14px 34px rgba(45,34,22,.055),
            0 3px 10px rgba(45,34,22,.02);
    }

    .sari-v7-eyebrow {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .sari-v7-badge {
        display: inline-flex;
        min-height: 30px;
        align-items: center;
        border-radius: 999px;
        padding: 0 11px;
        font-size: 9px;
        font-weight: 700;
    }

    .sari-v7-badge.gold {
        border: 1px solid #e7d3aa;
        background: #fff8e9;
        color: #956313;
    }

    .sari-v7-badge.neutral {
        border: 1px solid #e2ddd6;
        background: #faf8f5;
        color: #716960;
    }

    .sari-v7-title {
        margin-top: 13px;
        font-size: 30px;
        line-height: 1.12;
        letter-spacing: -.04em;
        font-weight: 700;
        color: #231f1b;
    }

    .sari-v7-meta-row {
        margin-top: 14px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 9px;
        padding: 11px 0;
        border-top: 1px solid #eee8df;
        border-bottom: 1px solid #eee8df;
        font-size: 10px;
        color: #81786f;
    }

    .sari-v7-price-box {
        margin-top: 18px;
        padding: 16px 17px;
        border: 1px solid #efe2c4;
        border-radius: 15px;
        background: #fffaf0;
    }

    .sari-v7-price {
        font-size: 34px;
        line-height: 1;
        letter-spacing: -.045em;
        font-weight: 700;
        color: #c7840c;
    }

    .sari-v7-section {
        margin-top: 18px;
        padding-top: 17px;
        border-top: 1px solid #eee8df;
    }

    .sari-v7-section-title {
        font-size: 10px;
        font-weight: 700;
        color: #49413a;
    }

    .sari-v7-section-help {
        margin-top: 3px;
        font-size: 9px;
        line-height: 1.6;
        color: #91887d;
    }

    .buyer-variant-button {
        border: 1px solid #ddd6cc;
        background: #fff;
        color: #655d54;
        transition:
            border-color .16s ease,
            background-color .16s ease,
            color .16s ease;
    }

    .buyer-variant-button:hover {
        border-color: #d2b16e;
        background: #fffaf1;
        color: #8d6115;
    }

    .buyer-variant-button.is-selected {
        border-color: #c98b13;
        background: #fff8e9;
        color: #8e5f11;
        box-shadow: 0 0 0 2px rgba(201,139,19,.08);
    }

    .buyer-variant-button:disabled {
        cursor: not-allowed;
        opacity: .42;
        background: #f7f5f2;
    }

    [data-buyer-option-group] + [data-buyer-option-group] {
        padding-top: 2px;
    }

    [data-buyer-option-group-status].is-selected {
        color: #9a6817;
        font-weight: 700;
    }

    /* Color choices are bare circular swatches — no outer pill/card container. */
    .buyer-product-page .buyer-color-swatch-button {
        display: inline-grid !important;
        width: 30px !important;
        height: 30px !important;
        min-width: 30px !important;
        min-height: 30px !important;
        padding: 0 !important;
        place-items: center !important;
        border: 0 !important;
        border-radius: 999px !important;
        background: transparent !important;
        box-shadow: none !important;
        outline: none !important;
    }

    .buyer-product-page .buyer-color-swatch {
        display: block;
        width: 24px;
        height: 24px;
        border: 1px solid rgba(31,41,55,.22);
        border-radius: 999px;
        background: var(--buyer-color-swatch, #d1d5db);
        box-shadow:
            inset 0 0 0 1px rgba(255,255,255,.20),
            0 1px 2px rgba(15,23,42,.10);
        transition: transform 120ms ease, box-shadow 120ms ease, opacity 120ms ease;
    }

    .buyer-product-page .buyer-color-swatch-button:hover:not(:disabled) {
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .buyer-product-page .buyer-color-swatch-button:hover:not(:disabled) .buyer-color-swatch {
        transform: scale(1.08);
    }

    .buyer-product-page .buyer-color-swatch-button.is-selected {
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .buyer-product-page .buyer-color-swatch-button.is-selected .buyer-color-swatch {
        box-shadow:
            0 0 0 2px #fff,
            0 0 0 4px #c88912,
            inset 0 0 0 1px rgba(255,255,255,.20) !important;
    }

    .buyer-product-page .buyer-color-swatch-button:disabled {
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        opacity: .38 !important;
    }

    .buyer-product-page .buyer-color-swatch-button:disabled .buyer-color-swatch {
        filter: saturate(.45);
    }

    .buyer-product-page .buyer-color-swatch-button:focus-visible {
        outline: 2px solid rgba(200,137,18,.45) !important;
        outline-offset: 3px !important;
    }

    [data-buyer-variant-hint].is-ready {
        color: #4f8065;
        font-weight: 600;
    }

    .sari-v7-qty button:disabled,
    .sari-v7-qty input:disabled {
        cursor: not-allowed;
        opacity: .48;
        background: #f4f6f8;
    }

    .sari-v7-availability {
        display: grid;
        grid-template-columns: minmax(0,1fr) 150px;
        gap: 14px;
        align-items: end;
        margin-top: 18px;
        padding: 14px;
        border: 1px solid #e8e0d6;
        border-radius: 14px;
        background: #fcfbf8;
    }

    .sari-v7-qty {
        display: flex;
        height: 42px;
        align-items: center;
        overflow: hidden;
        border: 1px solid #ddd6cd;
        border-radius: 10px;
        background: #fff;
    }

    .sari-v7-qty button {
        display: grid;
        height: 100%;
        width: 40px;
        place-items: center;
        font-size: 15px;
        color: #766d63;
        transition: background-color .15s ease, color .15s ease;
    }

    .sari-v7-qty button:hover {
        background: #fff8e9;
        color: #9a6817;
    }

    .sari-v7-qty input {
        height: 100%;
        min-width: 0;
        flex: 1;
        border-left: 1px solid #e6e0d9;
        border-right: 1px solid #e6e0d9;
        background: #fff;
        text-align: center;
        font-size: 10px;
        font-weight: 700;
        color: #403930;
        outline: none;
    }

    .buyer-product-primary,
    .buyer-product-secondary {
        transition:
            background-color .16s ease,
            color .16s ease,
            border-color .16s ease;
    }

    .buyer-product-primary {
        border: 1px solid #c98b13;
        background: #c98b13;
        color: #fff;
    }

    .buyer-product-primary:hover {
        border-color: #b77d10;
        background: #b77d10;
    }

    .buyer-product-secondary {
        border: 1px solid #c98b13;
        background: #fff;
        color: #a46b0c;
    }

    .buyer-product-secondary:hover {
        border-color: #b77d10;
        background: #fff9ec;
        color: #8d5c08;
    }

    .buyer-product-primary:disabled,
    .buyer-product-secondary:disabled {
        cursor: not-allowed;
        opacity: .45;
    }

    .sari-v7-trust {
        margin-top: 16px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0,1fr));
        gap: 8px;
        padding-top: 15px;
        border-top: 1px solid #eee8df;
    }

    .sari-v7-trust-item {
        display: flex;
        min-width: 0;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 10px;
        background: #faf8f5;
        padding: 10px 8px;
        text-align: center;
        font-size: 8px;
        font-weight: 700;
        color: #746b61;
    }

    .sari-v7-trust-item svg {
        flex: 0 0 auto;
        color: #b9780b;
    }

    .sari-v7-seller-card {
        margin-top: 14px;
        border: 1px solid #e8e0d6;
        border-radius: 14px;
        background: #fff;
        padding: 14px;
    }

    /* ============================================================
       LOWER SECTIONS
    ============================================================ */
    .sari-v7-info-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        margin-top: 22px;
    }

    .sari-v7-section-card {
        border: 1px solid #e8e0d5;
        border-radius: 18px;
        background: #fff;
        padding: 20px;
        box-shadow: 0 8px 20px rgba(45,34,22,.035);
    }

    .sari-v7-section-kicker {
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .11em;
        text-transform: uppercase;
        color: #ae7a20;
    }

    .sari-v7-section-heading {
        margin-top: 4px;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -.025em;
        color: #302a24;
    }

    .buyer-product-lightbox {
        opacity: 0;
        pointer-events: none;
        transition: opacity .18s ease;
    }

    .buyer-product-lightbox.is-open {
        opacity: 1;
        pointer-events: auto;
    }

    .buyer-product-related {
        transition: border-color .16s ease, box-shadow .16s ease;
    }

    .buyer-product-related:hover {
        border-color: #dac59b;
        box-shadow: 0 9px 20px rgba(45,34,22,.05);
    }

    @media (min-width: 640px) {
        .sari-v7-gallery-layout.has-thumbnails {
            grid-template-columns: 72px minmax(0,1fr);
            gap: 15px;
        }

        .sari-v7-gallery-thumbs {
            order: 1;
            max-height: 610px;
            flex-direction: column;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .sari-v7-gallery-main {
            order: 2;
        }

        .sari-v7-gallery-thumb {
            width: 68px;
            height: 68px;
        }
    }

    @media (min-width: 1024px) {
        .sari-v7-hero-grid {
            grid-template-columns: minmax(0,55%) minmax(420px,45%);
            gap: 28px;
        }

        .sari-v7-buying-panel {
            position: sticky;
            top: 92px;
            padding: 26px;
        }

        .sari-v7-info-grid {
            grid-template-columns: minmax(0,1.18fr) minmax(330px,.82fr);
        }
    }

    @media (min-width: 1280px) {
        .sari-v7-hero-grid {
            grid-template-columns: minmax(0,56%) minmax(440px,44%);
            gap: 32px;
        }

        .sari-v7-gallery-layout.has-thumbnails {
            grid-template-columns: 74px minmax(0,1fr);
        }

        .sari-v7-gallery-thumb {
            width: 70px;
            height: 70px;
        }

        .sari-v7-main-photo {
            max-height: 640px;
        }

        .sari-v7-title {
            font-size: 34px;
        }
    }

    @media (max-width: 639px) {
        .sari-v7-availability {
            grid-template-columns: 1fr;
        }

        .sari-v7-trust {
            grid-template-columns: 1fr;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .buyer-product-primary,
        .buyer-product-secondary,
        .buyer-variant-button,
        .sari-v7-gallery-thumb img,
        .buyer-product-related,
        .buyer-product-lightbox {
            transition: none !important;
        }
    }

    /* ============================================================
       SARI PRODUCT DETAILS — V8 LIGHT NEUMORPHISM
       Performance-first:
       - static shadows only
       - no filter / backdrop-filter
       - no box-shadow animation
       - short 120ms UI transitions
    ============================================================ */

    :root {
        --sari-v8-bg: #fbfaf7;
        --sari-v8-surface: #fffdfa;
        --sari-v8-border: #e8dfd3;
        --sari-v8-gold: #c98b13;
        --sari-v8-gold-dark: #b77d10;
        --sari-v8-gold-soft: #fff8e9;
        --sari-v8-text: #2f2923;
        --sari-v8-muted: #847a70;
        --sari-v8-shadow-dark: rgba(73, 56, 37, .075);
        --sari-v8-shadow-light: rgba(255, 255, 255, .92);
    }

    .buyer-product-page {
        background: var(--sari-v8-bg);
    }

    /* Keep interaction animation cheap and fast. */
    .buyer-product-page button,
    .buyer-product-page a,
    .buyer-variant-button,
    .sari-v7-gallery-thumb img {
        transition:
            background-color 120ms ease,
            border-color 120ms ease,
            color 120ms ease,
            transform 120ms ease,
            opacity 120ms ease !important;
    }

    /* Main buying card — clear but restrained raised effect. */
    .sari-v7-buying-panel {
        border-color: var(--sari-v8-border) !important;
        background: #fff !important;
        box-shadow:
            8px 8px 20px var(--sari-v8-shadow-dark),
            -6px -6px 16px var(--sari-v8-shadow-light) !important;
    }

    /* Price area — softly inset, not floating. */
    .sari-v7-price-box {
        border-color: #eadfc8 !important;
        background: #fffaf0 !important;
        box-shadow:
            inset 3px 3px 7px rgba(115, 82, 28, .055),
            inset -3px -3px 7px rgba(255, 255, 255, .82) !important;
    }

    /* Availability / quantity container gets a gentle inset surface. */
    .sari-v7-availability {
        border-color: #e5dccf !important;
        background: #fcfbf8 !important;
        box-shadow:
            inset 3px 3px 7px rgba(73, 56, 37, .045),
            inset -3px -3px 7px rgba(255, 255, 255, .86) !important;
    }

    .sari-v7-qty {
        border-color: #ded4c7 !important;
        background: #fff !important;
        box-shadow:
            inset 2px 2px 5px rgba(73, 56, 37, .05),
            inset -2px -2px 5px rgba(255, 255, 255, .9) !important;
    }

    /* Trust and seller areas feel softly raised without looking bulky. */
    .sari-v7-trust-item {
        border: 1px solid #ebe3d9;
        background: #fcfbf8 !important;
        box-shadow:
            4px 4px 9px rgba(73, 56, 37, .04),
            -3px -3px 8px rgba(255, 255, 255, .9);
    }

    .sari-v7-seller-card {
        border-color: #e7ded2 !important;
        background: #fff !important;
        box-shadow:
            5px 5px 12px rgba(73, 56, 37, .045),
            -4px -4px 10px rgba(255, 255, 255, .92) !important;
    }

    /* Lower information cards get the same visual language. */
    .sari-v7-section-card,
    .buyer-product-card {
        border-color: #e7ded2 !important;
        background: #fff !important;
        box-shadow:
            7px 7px 17px rgba(73, 56, 37, .055),
            -5px -5px 14px rgba(255, 255, 255, .9) !important;
    }

    /* Related cards stay lightweight — less shadow than large sections. */
    .buyer-product-related {
        box-shadow:
            4px 4px 10px rgba(73, 56, 37, .045),
            -3px -3px 8px rgba(255, 255, 255, .9) !important;
    }

    /* Thumbnails: subtle raised image, active state remains gold. */
    .sari-v7-gallery-thumb img {
        box-shadow:
            3px 3px 8px rgba(73, 56, 37, .045),
            -2px -2px 6px rgba(255, 255, 255, .9);
    }

    .sari-v7-gallery-thumb.is-active img {
        border-color: var(--sari-v8-gold) !important;
        box-shadow:
            0 0 0 2px rgba(201, 139, 19, .12),
            3px 3px 8px rgba(73, 56, 37, .045) !important;
    }

    /* Badges / pills — slight surface depth only. */
    .sari-v7-badge,
    .sari-v7-shipping-pill,
    .sari-v7-gallery-count,
    .sari-v7-gallery-discount {
        box-shadow:
            2px 2px 6px rgba(73, 56, 37, .035),
            -2px -2px 5px rgba(255, 255, 255, .88);
    }

    /* Buttons remain simple and responsive — no heavy shadow animation. */
    .buyer-product-primary,
    .buyer-product-secondary {
        box-shadow: none !important;
        transform: translateZ(0);
    }

    .buyer-product-primary {
        background: var(--sari-v8-gold) !important;
        border-color: var(--sari-v8-gold) !important;
    }

    .buyer-product-primary:hover {
        background: var(--sari-v8-gold-dark) !important;
        border-color: var(--sari-v8-gold-dark) !important;
        transform: translateY(-1px);
    }

    .buyer-product-secondary:hover {
        transform: translateY(-1px);
    }

    .buyer-product-primary:active,
    .buyer-product-secondary:active,
    .buyer-variant-button:active,
    .sari-v7-gallery-thumb:active {
        transform: translateY(0) scale(.985);
    }

    /* Variant buttons use a soft surface instead of a floating effect. */
    .buyer-variant-button {
        box-shadow: none !important;
    }

    .buyer-variant-button.is-selected {
        box-shadow:
            inset 0 0 0 1px rgba(201, 139, 19, .08) !important;
    }

    /* Avoid expensive hover shadow recalculation. */
    .buyer-product-related:hover,
    .sari-v7-section-card:hover,
    .sari-v7-buying-panel:hover {
        box-shadow: inherit;
    }

    /* GPU-friendly large image display without unnecessary animation. */
    .sari-v7-main-photo {
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    /* Reduce paint work where possible. */
    .sari-v7-buying-panel,
    .sari-v7-section-card,
    .buyer-product-related,
    .sari-v7-seller-card {
        contain: paint;
    }

    @media (max-width: 767px) {
        /* Slightly lighter shadows on smaller devices. */
        .sari-v7-buying-panel,
        .sari-v7-section-card,
        .buyer-product-card {
            box-shadow:
                5px 5px 13px rgba(73, 56, 37, .045),
                -4px -4px 11px rgba(255, 255, 255, .88) !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .buyer-product-page button,
        .buyer-product-page a,
        .buyer-variant-button,
        .sari-v7-gallery-thumb img {
            transition: none !important;
            transform: none !important;
        }
    }


/* ============================================================
   SARI PRODUCT DETAILS — V9 CLEAN MODERN COMMERCE
   UI-only: existing routes, forms, IDs and JavaScript preserved.
============================================================ */
.buyer-product-page{
    --gold:#c88912;--gold-dark:#a96f0e;--gold-soft:#fff8e8;
    --ink:#182230;--text:#3f4a58;--muted:#7f8a98;
    --line:#e5e9ee;--soft:#f7f8fa;--white:#fff;
    max-width:1460px!important;padding-top:18px!important;padding-bottom:44px!important;
    color:var(--ink)!important;background:transparent!important;
}
.buyer-product-page>nav{margin-bottom:18px!important;gap:7px!important;font-size:11px!important;font-weight:500!important;color:#8b96a3!important}
.buyer-product-page>nav a{color:#697586!important}.buyer-product-page>nav a:hover{color:var(--gold-dark)!important}
.buyer-product-page>nav span:last-child{color:#3d4754!important;font-weight:600!important}
#buyerProductNotice,.buyer-product-page>.mb-4.rounded-\[14px\]{border-radius:12px!important;padding:12px 14px!important;font-size:11px!important;line-height:1.45!important;font-weight:500!important;box-shadow:none!important}

.sari-v7-hero-grid{grid-template-columns:minmax(0,1fr)!important;gap:26px!important;align-items:start!important}
.sari-v7-gallery-layout{gap:12px!important}
.sari-v7-gallery-thumbs{gap:10px!important;padding:1px!important;scrollbar-color:#cfd6de transparent!important}
.sari-v7-gallery-thumb{width:70px!important;height:70px!important;border-radius:12px!important;background:#fff!important}
.sari-v7-gallery-thumb img{border:1px solid var(--line)!important;border-radius:12px!important;background:#fff!important;box-shadow:none!important;opacity:.88!important}
.sari-v7-gallery-thumb:hover img{border-color:#c7ced6!important;opacity:1!important}
.sari-v7-gallery-thumb.is-active img{border-color:var(--gold)!important;box-shadow:0 0 0 2px rgba(200,137,18,.12)!important;opacity:1!important}
.sari-v7-main-photo{width:100%!important;max-width:100%!important;max-height:660px!important;min-height:440px!important;padding:26px!important;object-fit:contain!important;border:1px solid var(--line)!important;border-radius:18px!important;background:#fff!important;box-shadow:0 8px 24px rgba(23,32,51,.035)!important;transform:none!important}
.sari-v7-main-photo-placeholder{min-height:440px!important;border:1px dashed #d6dde5!important;border-radius:18px!important;background:#fafbfc!important;color:#a0a9b4!important}
.sari-v7-gallery-meta{margin-top:12px!important;gap:10px!important}
.sari-v7-shipping-pill,.sari-v7-gallery-count,.sari-v7-gallery-discount{min-height:32px!important;padding:0 10px!important;border-radius:9px!important;box-shadow:none!important;font-size:9.5px!important;font-weight:600!important}
.sari-v7-shipping-pill{border-color:#cfe5d6!important;background:#f3faf5!important;color:#3f7453!important}
.sari-v7-gallery-count{border-color:var(--line)!important;background:#fff!important;color:#657180!important}
.sari-v7-gallery-discount{border-color:#ead8ad!important;background:var(--gold-soft)!important;color:#96630d!important}

.sari-v7-buying-panel{width:100%!important;padding:25px!important;border:1px solid var(--line)!important;border-radius:18px!important;background:#fff!important;box-shadow:0 10px 28px rgba(23,32,51,.055)!important;contain:none!important}
.sari-v7-eyebrow{gap:7px!important}
.sari-v7-badge{min-height:28px!important;padding:0 10px!important;border-radius:8px!important;box-shadow:none!important;font-size:9px!important;font-weight:600!important}
.sari-v7-badge.gold{border-color:#ead8ad!important;background:var(--gold-soft)!important;color:#93600b!important}
.sari-v7-badge.neutral{border-color:var(--line)!important;background:#f8f9fa!important;color:#687382!important}
.sari-v7-title{margin-top:14px!important;color:var(--ink)!important;font-size:clamp(25px,2.3vw,34px)!important;font-weight:650!important;line-height:1.18!important;letter-spacing:-.03em!important}
.sari-v7-title+.mt-2{margin-top:8px!important;color:#8a95a2!important;font-size:10px!important}
.sari-v7-meta-row{margin-top:15px!important;padding:12px 0!important;gap:10px!important;border-color:#edf0f3!important;color:#707b89!important;font-size:11px!important}
.sari-v7-price-box{margin-top:18px!important;padding:16px 0 5px!important;border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important}
.sari-v7-price{color:var(--gold-dark)!important;font-size:clamp(31px,3vw,39px)!important;font-weight:700!important;letter-spacing:-.035em!important}
#buyerProductOldPrice{color:#a4acb5!important;font-size:11px!important}
.sari-v7-buying-panel>p.mt-4{max-width:none!important;margin-top:16px!important;color:#667281!important;font-size:11.5px!important;line-height:1.75!important}

.sari-v7-section{margin-top:20px!important;padding-top:18px!important;border-color:#edf0f3!important}
.sari-v7-section-title{color:#394452!important;font-size:11px!important;font-weight:650!important}
.sari-v7-section-help{margin-top:4px!important;color:#8b96a3!important;font-size:9.5px!important;line-height:1.5!important}
#buyerSelectedVariantLabel{color:var(--gold-dark)!important;font-size:9px!important;font-weight:600!important}
.buyer-variant-button{min-height:38px!important;padding:0 14px!important;border:1px solid #dfe4ea!important;border-radius:9px!important;background:#fff!important;box-shadow:none!important;color:#556170!important;font-size:10px!important;font-weight:550!important}
.buyer-variant-button:hover{border-color:#d1b36e!important;background:#fffaf0!important;color:#875b10!important}
.buyer-variant-button.is-selected{border-color:var(--gold)!important;background:var(--gold-soft)!important;color:#875b10!important;box-shadow:0 0 0 2px rgba(200,137,18,.08)!important}

.sari-v7-availability{grid-template-columns:minmax(0,1fr) 155px!important;gap:16px!important;margin-top:20px!important;padding:14px!important;border:1px solid var(--line)!important;border-radius:12px!important;background:#f8f9fa!important;box-shadow:none!important}
.sari-v7-availability p{font-size:10px!important;font-weight:600!important}
#buyerProductStock{margin-top:4px!important;font-size:12px!important;font-weight:650!important}
.sari-v7-qty{height:42px!important;border:1px solid #dce2e8!important;border-radius:9px!important;background:#fff!important;box-shadow:none!important}
.sari-v7-qty button{width:40px!important;color:#586473!important;font-size:16px!important}.sari-v7-qty button:hover{background:#f7f8fa!important;color:var(--gold-dark)!important}
.sari-v7-qty input{border-color:#e5e9ee!important;color:#303a47!important;font-size:11px!important;font-weight:600!important}

#buyerAddToCartForm{margin-top:16px!important}#buyerAddToCartForm>.grid{gap:10px!important}
.buyer-product-primary,.buyer-product-secondary{height:48px!important;border-radius:10px!important;box-shadow:none!important;font-size:11px!important;font-weight:650!important;transform:none!important}
.buyer-product-primary{border-color:var(--gold)!important;background:var(--gold)!important;color:#fff!important}
.buyer-product-primary:hover{border-color:var(--gold-dark)!important;background:var(--gold-dark)!important;transform:none!important}
.buyer-product-secondary{border-color:#d7aa4b!important;background:#fff!important;color:#97630d!important}
.buyer-product-secondary:hover{border-color:var(--gold)!important;background:#fffaf0!important;color:#80540d!important;transform:none!important}
#buyerAddToCartForm>a{margin-top:10px!important;color:#7a8592!important;font-size:9.5px!important;font-weight:550!important}

.sari-v7-trust{grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:0!important;margin-top:18px!important;padding-top:16px!important;border-color:#edf0f3!important}
.sari-v7-trust-item{min-height:42px!important;padding:6px 10px!important;border:0!important;border-right:1px solid #edf0f3!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;color:#6f7a88!important;font-size:8.8px!important;font-weight:600!important}
.sari-v7-trust-item:last-child{border-right:0!important}.sari-v7-trust-item svg{color:var(--gold)!important}

.sari-v7-seller-card{margin-top:16px!important;padding:14px!important;border:1px solid var(--line)!important;border-radius:12px!important;background:#f9fafb!important;box-shadow:none!important;contain:none!important}
.sari-v7-seller-card a{height:36px!important;border-radius:9px!important;padding-inline:12px!important;font-size:9px!important;font-weight:600!important;line-height:36px!important}

.sari-v7-info-grid{grid-template-columns:1fr!important;gap:16px!important;margin-top:26px!important}
.sari-v7-section-card,.buyer-product-card{border:1px solid var(--line)!important;border-radius:16px!important;background:#fff!important;box-shadow:0 6px 20px rgba(23,32,51,.035)!important;contain:none!important}
.sari-v7-section-card{padding:22px!important}
.sari-v7-section-card>.border-b{padding-bottom:14px!important;border-color:#edf0f3!important}
.sari-v7-section-kicker{color:#a06a10!important;font-size:8.5px!important;font-weight:650!important}
.sari-v7-section-heading{margin-top:4px!important;color:#293442!important;font-size:19px!important;font-weight:650!important;letter-spacing:-.02em!important}
.sari-v7-section-card .whitespace-pre-line{color:#667281!important;font-size:11.5px!important;line-height:1.85!important}
.sari-v7-section-card dl>div{min-height:46px!important;padding-block:12px!important;border-color:#edf0f3!important}
.sari-v7-section-card dt{color:#8b96a3!important;font-size:10px!important;font-weight:500!important}
.sari-v7-section-card dd{color:#44505d!important;font-size:10.5px!important;font-weight:600!important}
.sari-v7-section-card article{border:1px solid var(--line)!important;border-radius:12px!important;background:#fbfcfd!important;padding:15px!important}
.sari-v7-section-card article p{color:#647180!important;font-size:10.5px!important;line-height:1.65!important}

.buyer-product-page>section.mt-6{margin-top:28px!important}
.buyer-product-page>section.mt-6 h2{color:#26313e!important;font-size:21px!important;font-weight:650!important}
.buyer-product-related{display:block!important;overflow:hidden!important;border-radius:14px!important;box-shadow:none!important;transition:border-color 140ms ease,transform 140ms ease!important}
.buyer-product-related:hover{border-color:#d6b66f!important;box-shadow:none!important;transform:translateY(-2px)!important}
.buyer-product-related>div:first-child{height:180px!important;min-height:180px!important;border-right:0!important;border-bottom:1px solid var(--line)!important;background:#f8f9fa!important}
.buyer-product-related>div:last-child{min-height:112px!important;padding:14px!important}
.buyer-product-related h3{color:#303b48!important;font-size:11.5px!important;font-weight:650!important}
.buyer-product-related h3+p{color:#8a95a2!important;font-size:8.8px!important}
.buyer-product-related strong{color:var(--gold-dark)!important;font-size:14px!important;font-weight:700!important}

.buyer-product-page button:focus-visible,.buyer-product-page a:focus-visible,.buyer-product-page input:focus-visible{outline:2px solid rgba(200,137,18,.45)!important;outline-offset:2px!important}
@media(min-width:1024px){
 .sari-v7-hero-grid{grid-template-columns:minmax(0,57%) minmax(390px,43%)!important;gap:34px!important}
 .sari-v7-buying-panel{position:sticky!important;top:88px!important}
 .sari-v7-info-grid{grid-template-columns:minmax(0,1.18fr) minmax(320px,.82fr)!important}
}
@media(min-width:1280px){
 .sari-v7-hero-grid{grid-template-columns:minmax(0,58%) minmax(420px,42%)!important;gap:38px!important}
 .sari-v7-gallery-layout.has-thumbnails{grid-template-columns:72px minmax(0,1fr)!important;gap:14px!important}
}
@media(max-width:767px){
 .buyer-product-page{padding-inline:14px!important;padding-top:12px!important;padding-bottom:28px!important}
 .sari-v7-main-photo,.sari-v7-main-photo-placeholder{min-height:320px!important}
 .sari-v7-main-photo{padding:18px!important;border-radius:14px!important}
 .sari-v7-gallery-thumb{width:62px!important;height:62px!important}
 .sari-v7-buying-panel{padding:19px!important;border-radius:15px!important}
 .sari-v7-title{font-size:25px!important}.sari-v7-price{font-size:32px!important}
 .sari-v7-availability{grid-template-columns:1fr!important}
 .sari-v7-trust{grid-template-columns:1fr!important}
 .sari-v7-trust-item{justify-content:flex-start!important;border-right:0!important;border-bottom:1px solid #edf0f3!important}
 .sari-v7-trust-item:last-child{border-bottom:0!important}
 .sari-v7-section-card{padding:17px!important;border-radius:14px!important}
 .buyer-product-related>div:first-child{height:160px!important;min-height:160px!important}
}


    /* ============================================================
       PRODUCT INFORMATION — MERGED + ATTACHED TO GALLERY COLUMN
       Fixes the large dead space caused by placing this content
       below the taller right-side buying panel.
    ============================================================ */
    .sari-product-info-combined {
        width:100% !important;
        margin-top:18px !important;
        overflow:hidden !important;

        border:1px solid #e5e9ee !important;
        border-radius:16px !important;
        background:#ffffff !important;

        box-shadow:0 6px 20px rgba(23,32,51,.035) !important;
    }

    .sari-product-info-combined__header {
        display:flex !important;
        align-items:flex-end !important;
        justify-content:space-between !important;
        gap:16px !important;

        padding:18px 20px 15px !important;
        border-bottom:1px solid #edf0f3 !important;
    }

    .sari-product-info-combined__kicker {
        margin:0 !important;

        color:#a06a10 !important;
        font-size:8.5px !important;
        font-weight:700 !important;
        line-height:1.2 !important;
        letter-spacing:.1em !important;
        text-transform:uppercase !important;
    }

    .sari-product-info-combined__title {
        margin:4px 0 0 !important;

        color:#293442 !important;
        font-size:20px !important;
        font-weight:650 !important;
        line-height:1.25 !important;
        letter-spacing:-.025em !important;
    }

    .sari-product-info-combined__description {
        padding:17px 20px 18px !important;
    }

    .sari-product-info-combined__label {
        margin:0 0 7px !important;

        color:#4d5866 !important;
        font-size:10px !important;
        font-weight:650 !important;
        line-height:1.3 !important;
    }

    .sari-product-info-combined__copy {
        margin:0 !important;

        color:#667281 !important;
        font-size:11.5px !important;
        font-weight:400 !important;
        line-height:1.75 !important;
    }

    .sari-product-info-combined__copy.is-muted {
        color:#919ba6 !important;
    }

    .sari-product-info-combined__details {
        display:grid !important;
        grid-template-columns:repeat(2,minmax(0,1fr)) !important;

        margin:0 !important;
        border-top:1px solid #edf0f3 !important;
    }

    .sari-product-info-combined__detail {
        display:flex !important;
        min-width:0 !important;
        min-height:58px !important;
        align-items:center !important;
        justify-content:space-between !important;
        gap:16px !important;

        padding:13px 20px !important;
        border-bottom:1px solid #edf0f3 !important;
    }

    .sari-product-info-combined__detail:nth-child(odd) {
        border-right:1px solid #edf0f3 !important;
    }

    .sari-product-info-combined__details
    .sari-product-info-combined__detail:nth-last-child(-n+2) {
        border-bottom:0 !important;
    }

    .sari-product-info-combined__detail--wide {
        grid-column:1 / -1 !important;
        border-right:0 !important;
    }

    .sari-product-info-combined__detail dt {
        flex:0 0 auto !important;

        color:#8b96a3 !important;
        font-size:9.5px !important;
        font-weight:500 !important;
        line-height:1.35 !important;
    }

    .sari-product-info-combined__detail dd {
        min-width:0 !important;
        margin:0 !important;

        overflow-wrap:anywhere !important;
        color:#44505d !important;
        font-size:10.5px !important;
        font-weight:600 !important;
        line-height:1.45 !important;
        text-align:right !important;
    }

    .sari-product-info-combined__detail dd.is-positive {
        color:#3f7e5a !important;
    }

    @media (max-width:767px) {
        .sari-product-info-combined {
            margin-top:14px !important;
            border-radius:14px !important;
        }

        .sari-product-info-combined__header {
            padding:16px 16px 13px !important;
        }

        .sari-product-info-combined__description {
            padding:15px 16px 16px !important;
        }

        .sari-product-info-combined__details {
            grid-template-columns:1fr !important;
        }

        .sari-product-info-combined__detail,
        .sari-product-info-combined__detail:nth-child(odd),
        .sari-product-info-combined__detail--wide {
            grid-column:auto !important;
            min-height:54px !important;
            padding:12px 16px !important;

            border-right:0 !important;
            border-bottom:1px solid #edf0f3 !important;
        }

        .sari-product-info-combined__details
        .sari-product-info-combined__detail:last-child {
            border-bottom:0 !important;
        }
    }


    /* ============================================================
       FINAL BUYING PANEL — CLEAN PROFESSIONAL
       Matches the approved minimal mockup.
    ============================================================ */
    .sari-v7-buying-panel {
        padding:28px 28px 24px !important;
        border:1px solid #e3e8ee !important;
        border-radius:18px !important;
        background:#ffffff !important;
        box-shadow:0 10px 30px rgba(21,30,45,.05) !important;
    }

    .sari-v7-title {
        margin-top:0 !important;
        max-width:760px !important;
        color:#172033 !important;
        font-size:clamp(28px,2.5vw,38px) !important;
        font-weight:700 !important;
        line-height:1.13 !important;
        letter-spacing:-.035em !important;
    }

    .sari-v7-title + .mt-2 {
        margin-top:12px !important;
        gap:8px 12px !important;
        color:#8190a1 !important;
        font-size:10.5px !important;
    }

    .sari-v7-title + .mt-2 strong {
        color:#4e5968 !important;
        font-weight:600 !important;
    }

    .sari-v7-meta-row {
        margin-top:18px !important;
        padding:14px 0 16px !important;
        border-top:0 !important;
        border-bottom:1px solid #e7ebf0 !important;
        color:#708091 !important;
        font-size:11px !important;
    }

    .sari-v7-price-box {
        margin-top:0 !important;
        padding:26px 0 25px !important;
        border:0 !important;
        border-bottom:1px solid #e7ebf0 !important;
        border-radius:0 !important;
        background:transparent !important;
        box-shadow:none !important;
    }

    .sari-v7-price {
        color:#bb7600 !important;
        font-size:clamp(38px,3.3vw,48px) !important;
        font-weight:700 !important;
        line-height:1 !important;
        letter-spacing:-.04em !important;
    }

    #buyerProductOldPrice {
        color:#a7b0ba !important;
        font-size:11px !important;
    }

    .sari-v7-price-box .rounded-full {
        border:0 !important;
        border-radius:8px !important;
        background:#fff7e5 !important;
        color:#9e680e !important;
        box-shadow:none !important;
        font-size:9px !important;
        font-weight:600 !important;
    }

    .sari-v7-section {
        margin-top:0 !important;
        padding-top:25px !important;
        border-top:0 !important;
    }

    .sari-v7-section-title {
        color:#263343 !important;
        font-size:12px !important;
        font-weight:650 !important;
    }

    .sari-v7-section-help {
        margin-top:4px !important;
        color:#8290a0 !important;
        font-size:10px !important;
        line-height:1.45 !important;
    }

    #buyerSelectedVariantLabel {
        color:#b27408 !important;
        font-size:9px !important;
        font-weight:600 !important;
    }

    .buyer-variant-button {
        min-height:42px !important;
        padding:0 15px !important;
        border:1px solid #d9e0e7 !important;
        border-radius:9px !important;
        background:#fff !important;
        color:#344050 !important;
        box-shadow:none !important;
        font-size:10px !important;
        font-weight:550 !important;
    }

    .buyer-variant-button:hover {
        border-color:#c8a155 !important;
        background:#fffaf0 !important;
        color:#84580d !important;
    }

    .buyer-variant-button.is-selected {
        border-color:#c88912 !important;
        background:#fff8e8 !important;
        color:#84580d !important;
        box-shadow:0 0 0 2px rgba(200,137,18,.08) !important;
    }

    .sari-v7-availability {
        grid-template-columns:minmax(0,1fr) 190px !important;
        gap:24px !important;
        align-items:center !important;
        margin-top:24px !important;
        padding:18px 20px !important;
        border:1px solid #dfe5eb !important;
        border-radius:12px !important;
        background:#f8fafb !important;
        box-shadow:none !important;
    }

    .sari-v7-availability p {
        font-size:10px !important;
        font-weight:600 !important;
    }

    #buyerProductStock {
        margin-top:5px !important;
        color:#3f8459 !important;
        font-size:14px !important;
        font-weight:700 !important;
    }

    .sari-v7-qty {
        height:46px !important;
        border:1px solid #d8dfe6 !important;
        border-radius:9px !important;
        background:#fff !important;
        box-shadow:none !important;
    }

    .sari-v7-qty button {
        width:46px !important;
        color:#334051 !important;
        background:#fff !important;
        font-size:17px !important;
        font-weight:500 !important;
    }

    .sari-v7-qty button:hover {
        background:#f5f7f9 !important;
        color:#9b670e !important;
    }

    .sari-v7-qty input {
        border-color:#e2e7ec !important;
        color:#202b38 !important;
        font-size:11px !important;
        font-weight:650 !important;
    }

    #buyerAddToCartForm {
        margin-top:14px !important;
    }

    #buyerAddToCartForm > .grid {
        gap:12px !important;
    }

    .buyer-product-primary,
    .buyer-product-secondary {
        height:52px !important;
        border-radius:10px !important;
        box-shadow:none !important;
        font-size:11px !important;
        font-weight:650 !important;
        transform:none !important;
    }

    .buyer-product-primary {
        border:1px solid #c88912 !important;
        background:#c88912 !important;
        color:#fff !important;
    }

    .buyer-product-primary:hover {
        border-color:#ad730f !important;
        background:#ad730f !important;
        transform:none !important;
    }

    .buyer-product-secondary {
        border:1px solid #c88912 !important;
        background:#fff !important;
        color:#a46b0c !important;
    }

    .buyer-product-secondary:hover {
        border-color:#ad730f !important;
        background:#fffaf0 !important;
        color:#88590b !important;
        transform:none !important;
    }

    #buyerAddToCartForm > a {
        margin-top:11px !important;
        color:#7b8795 !important;
        font-size:9.5px !important;
        font-weight:550 !important;
    }

    .sari-v7-seller-card {
        margin-top:26px !important;
        padding:16px 18px !important;
        border:1px solid #e0e5eb !important;
        border-radius:12px !important;
        background:#fafbfc !important;
        box-shadow:none !important;
    }

    .sari-v7-seller-card .grid.h-9.w-9 {
        width:42px !important;
        height:42px !important;
        border-color:#f0d9a5 !important;
        border-radius:10px !important;
        background:#fff7e7 !important;
        color:#b9780d !important;
    }

    .sari-v7-seller-card .text-\[7px\] {
        color:#a56d0e !important;
        font-size:8px !important;
        font-weight:700 !important;
    }

    .sari-v7-seller-card .text-\[10\.5px\] {
        color:#273240 !important;
        font-size:12px !important;
        font-weight:700 !important;
    }

    .sari-v7-seller-card .text-\[7\.5px\] {
        color:#8b96a3 !important;
        font-size:9px !important;
    }

    .sari-v7-seller-card a {
        height:38px !important;
        padding-inline:14px !important;
        border-radius:9px !important;
        font-size:9px !important;
        font-weight:600 !important;
        line-height:38px !important;
    }

    /* Explicitly suppress removed legacy sections if stale compiled markup appears. */
    .sari-v7-eyebrow,
    .sari-v7-trust {
        display:none !important;
    }

    @media (max-width:767px) {
        .sari-v7-buying-panel {
            padding:20px !important;
        }

        .sari-v7-title {
            font-size:27px !important;
        }

        .sari-v7-price {
            font-size:37px !important;
        }

        .sari-v7-availability {
            grid-template-columns:1fr !important;
            gap:14px !important;
        }

        .sari-v7-seller-card {
            margin-top:20px !important;
        }
    }



    /* ============================================================
       PRODUCT MEDIA — COVER + PRODUCT GALLERY ONLY
       - horizontal thumbnails below the main photo
       - overlay previous/next arrows
       - variant photos are preview-only after exact selection
    ============================================================ */
    .sari-v7-gallery-layout,
    .sari-v7-gallery-layout.has-thumbnails {
        display:block !important;
        grid-template-columns:1fr !important;
    }

    .sari-v7-gallery-main {
        display:flex !important;
        width:100% !important;
        flex-direction:column !important;
        align-items:stretch !important;
    }

    .sari-product-gallery-stage {
        position:relative;
        width:100%;
        min-width:0;
    }

    .sari-product-gallery-stage .sari-v7-main-photo,
    .sari-product-gallery-stage .sari-v7-main-photo-placeholder {
        width:100% !important;
    }

    .sari-product-gallery-nav {
        position:absolute;
        top:50%;
        z-index:5;
        display:grid;
        width:42px;
        height:42px;
        place-items:center;
        border:1px solid rgba(225,230,236,.95);
        border-radius:999px;
        background:rgba(255,255,255,.94);
        color:#344050;
        box-shadow:0 5px 18px rgba(23,32,51,.10);
        transform:translateY(-50%);
        transition:background-color 120ms ease,border-color 120ms ease,color 120ms ease,transform 120ms ease !important;
    }

    .sari-product-gallery-nav:hover {
        border-color:#d7b46b;
        background:#fffaf0;
        color:#9a650d;
        transform:translateY(-50%) scale(1.04);
    }

    .sari-product-gallery-nav:active {
        transform:translateY(-50%) scale(.97);
    }

    .sari-product-gallery-nav--prev { left:14px; }
    .sari-product-gallery-nav--next { right:14px; }

    .sari-v7-gallery-thumbs,
    .sari-v7-gallery-layout.has-thumbnails .sari-v7-gallery-thumbs {
        order:initial !important;
        display:flex !important;
        width:100% !important;
        max-width:100% !important;
        max-height:none !important;
        flex-direction:row !important;
        gap:9px !important;
        margin-top:12px !important;
        padding:2px 1px 5px !important;
        overflow-x:auto !important;
        overflow-y:hidden !important;
        scroll-behavior:smooth;
        scrollbar-width:thin;
        scrollbar-color:#cfd6de transparent !important;
    }

    .sari-v7-gallery-thumb {
        flex:0 0 66px !important;
        width:66px !important;
        height:66px !important;
    }

    .sari-v7-gallery-thumb.is-active img {
        opacity:1 !important;
    }

    #buyerGalleryCounter.is-variant-preview {
        color:#364152 !important;
    }

    @media (max-width:767px) {
        .sari-product-gallery-nav {
            width:38px;
            height:38px;
        }

        .sari-product-gallery-nav--prev { left:9px; }
        .sari-product-gallery-nav--next { right:9px; }

        .sari-v7-gallery-thumb {
            flex-basis:58px !important;
            width:58px !important;
            height:58px !important;
        }
    }

    /* ============================================================
       MAIN PRODUCT PHOTO — FULL-BLEED COVER
       Keeps one stable gallery frame regardless of source image ratio.
       The lightbox remains object-contain so the original full image
       is still available through View image.
    ============================================================ */
    .sari-product-gallery-stage {
        aspect-ratio:3 / 2;
        overflow:hidden;
        border:1px solid #e5e9ee;
        border-radius:18px;
        background:#f7f8fa;
        box-shadow:0 8px 24px rgba(23,32,51,.035);
    }

    .sari-product-gallery-stage .sari-v7-main-photo {
        display:block !important;
        width:100% !important;
        height:100% !important;
        min-height:0 !important;
        max-height:none !important;
        padding:0 !important;
        border:0 !important;
        border-radius:0 !important;
        background:#f7f8fa !important;
        box-shadow:none !important;
        object-fit:cover !important;
        object-position:center center !important;
    }

    .sari-product-gallery-stage .sari-v7-main-photo-placeholder {
        width:100% !important;
        height:100% !important;
        min-height:0 !important;
        border:0 !important;
        border-radius:0 !important;
    }

    @media (max-width:767px) {
        .sari-product-gallery-stage {
            aspect-ratio:4 / 3;
            border-radius:14px;
        }
    }

    /* ============================================================
       PRODUCT-CARD OFFER BADGES — DETAIL PAGE
       Reuses the same visual language as the shared buyer card:
       MALL top-left, discount corner top-right, and MEGA/COD/XTRA
       offer strip attached to the bottom edge of the main image.
    ============================================================ */
    .sari-detail-media-badges {
        position:absolute;
        top:14px;
        left:14px;
        z-index:7;
        display:flex;
        align-items:center;
        gap:7px;
        pointer-events:none;
    }

    .sari-detail-media-badge {
        display:inline-flex;
        min-height:28px;
        align-items:center;
        justify-content:center;
        border:1px solid rgba(255,255,255,.07);
        border-radius:999px;
        background:#2b2a28;
        padding:0 12px;
        color:#e0a11a;
        font-size:8px;
        line-height:1;
        font-weight:800;
        letter-spacing:-.015em;
        white-space:nowrap;
        box-shadow:0 5px 14px rgba(28,25,22,.16);
    }

    .sari-detail-media-badge--out {
        border-color:#f0c8c8;
        background:rgba(255,255,255,.96);
        color:#bf4d4d;
    }

    .sari-detail-discount-corner {
        position:absolute;
        top:-1px;
        right:-1px;
        z-index:8;
        display:inline-flex;
        min-width:76px;
        height:42px;
        align-items:center;
        justify-content:center;
        padding:0 14px 1px 15px;
        border:0;
        border-radius:0 18px 0 20px;
        background:#dda00b;
        color:#fff;
        font-size:14px;
        line-height:1;
        font-weight:800;
        letter-spacing:-.025em;
        font-variant-numeric:tabular-nums;
        white-space:nowrap;
        box-shadow:0 5px 14px rgba(126,84,7,.18);
        pointer-events:none;
    }

    .sari-detail-offer-cluster {
        position:absolute;
        left:0;
        bottom:0;
        z-index:7;
        display:inline-flex;
        width:auto;
        max-width:calc(100% - 18px);
        align-items:stretch;
        gap:0;
        filter:drop-shadow(0 6px 13px rgba(22,20,18,.18));
        pointer-events:none;
        isolation:isolate;
    }

    .sari-detail-offer-item {
        --offer-bg:#333;
        position:relative;
        z-index:1;
        display:inline-flex;
        min-width:0;
        height:44px;
        align-items:center;
        padding:0 15px;
        color:#fff;
        white-space:nowrap;
        flex:0 0 auto;
        background:transparent;
    }

    .sari-detail-offer-item + .sari-detail-offer-item {
        margin-left:-13px;
        padding-left:22px;
    }

    .sari-detail-offer-item::before {
        content:'';
        position:absolute;
        inset:0 -2px;
        z-index:-1;
        background:var(--offer-bg);
        transform:skewX(-20deg);
        box-shadow:inset 0 1px 0 rgba(255,255,255,.13);
    }

    .sari-detail-offer-item:first-child::before {
        left:-8px;
        border-radius:12px 0 0 0;
    }

    .sari-detail-offer-item:last-child::before {
        right:-8px;
        border-radius:0 12px 0 0;
    }

    .sari-detail-offer-item--discount {
        --offer-bg:linear-gradient(135deg,#8f1730 0%,#b92f49 100%);
        width:104px;
        justify-content:center;
    }

    .sari-detail-offer-item--cod {
        --offer-bg:linear-gradient(135deg,#f6cf66 0%,#e2a91d 100%);
        width:154px;
        padding-left:22px !important;
        padding-right:15px;
        color:#201b13;
    }

    .sari-detail-offer-item--shipping {
        --offer-bg:linear-gradient(135deg,#16283b 0%,#24384d 100%);
        width:174px;
    }

    .sari-detail-offer-stack {
        display:flex;
        min-width:0;
        flex-direction:column;
        justify-content:center;
        line-height:1;
    }

    .sari-detail-offer-kicker {
        display:block;
        font-size:8px;
        font-weight:800;
        letter-spacing:.045em;
        text-transform:uppercase;
        opacity:.98;
    }

    .sari-detail-offer-main {
        display:block;
        margin-top:3px;
        overflow:hidden;
        font-size:9px;
        font-weight:700;
        letter-spacing:-.015em;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    .sari-detail-offer-item--discount .sari-detail-offer-main {
        margin-top:2px;
        font-size:9px;
        font-weight:750;
    }

    @media (max-width:767px) {
        .sari-detail-media-badges {
            top:10px;
            left:10px;
        }

        .sari-detail-media-badge {
            min-height:24px;
            padding:0 9px;
            font-size:7px;
        }

        .sari-detail-discount-corner {
            min-width:62px;
            height:34px;
            padding-inline:11px;
            border-radius:0 14px 0 16px;
            font-size:11px;
        }

        .sari-detail-offer-cluster {
            max-width:94%;
            transform:scale(.82);
            transform-origin:left bottom;
        }
    }



    /* ============================================================
       AVAILABILITY + QUANTITY — CLEAN, NO OUTER CARD
       Variant stock stays hidden until an exact variant is selected.
    ============================================================ */
    .sari-v7-availability {
        display:flex !important;
        width:100% !important;
        align-items:flex-end !important;
        justify-content:space-between !important;
        gap:28px !important;
        margin-top:24px !important;
        padding:0 !important;
        border:0 !important;
        border-radius:0 !important;
        background:transparent !important;
        box-shadow:none !important;
    }

    .sari-v7-stock-block,
    .sari-v7-quantity-block {
        min-width:0;
        padding:0 !important;
        border:0 !important;
        background:transparent !important;
        box-shadow:none !important;
    }

    .sari-v7-stock-block.hidden {
        display:none !important;
    }

    .sari-v7-quantity-block {
        width:190px;
        margin-left:auto;
    }

    @media (max-width:767px) {
        .sari-v7-availability {
            align-items:stretch !important;
            gap:16px !important;
        }

        .sari-v7-quantity-block {
            width:100%;
            max-width:190px;
        }
    }


    /* ============================================================
       FINAL RIGHT BUYING PANEL — CLEAN / COMPACT / PROFESSIONAL
       Keeps all buying behavior while reducing visual noise.
    ============================================================ */
    .sari-v7-buying-panel,
    .sari-v7-buying-panel * {
        font-family:"Poppins",ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif !important;
    }

    .sari-v7-buying-panel {
        padding:26px 28px 24px !important;
    }

    .sari-v7-title {
        margin:0 !important;
        font-size:clamp(28px,2.35vw,36px) !important;
        line-height:1.13 !important;
        font-weight:700 !important;
        letter-spacing:-.035em !important;
        color:#182230 !important;
    }

    .sari-v7-title + .mt-2 {
        margin-top:10px !important;
        gap:7px 10px !important;
        color:#7f8a98 !important;
        font-size:11px !important;
        line-height:1.45 !important;
    }

    .sari-v7-title + .mt-2 strong {
        color:#465261 !important;
        font-weight:650 !important;
    }

    .sari-v7-meta-row {
        margin-top:14px !important;
        padding:11px 0 14px !important;
        gap:8px !important;
        border-top:0 !important;
        border-bottom:1px solid #e9edf1 !important;
        color:#74808d !important;
        font-size:11px !important;
        line-height:1.35 !important;
    }

    .sari-v7-price-box {
        margin:0 !important;
        padding:20px 0 20px !important;
        border:0 !important;
        border-bottom:1px solid #e9edf1 !important;
        border-radius:0 !important;
        background:transparent !important;
        box-shadow:none !important;
    }

    .sari-v7-price {
        color:#bb7600 !important;
        font-size:clamp(36px,3vw,44px) !important;
        line-height:1 !important;
        font-weight:750 !important;
        letter-spacing:-.045em !important;
    }

    #buyerProductOldPrice {
        color:#a6afb9 !important;
        font-size:11px !important;
        font-weight:500 !important;
    }

    .sari-v7-section {
        margin:0 !important;
        padding-top:19px !important;
        border-top:0 !important;
    }

    .sari-v7-section-title {
        color:#263343 !important;
        font-size:12px !important;
        line-height:1.35 !important;
        font-weight:700 !important;
    }

    .sari-v7-section-help {
        margin-top:4px !important;
        color:#8591a0 !important;
        font-size:10.5px !important;
        line-height:1.5 !important;
        font-weight:450 !important;
    }

    [data-buyer-option-groups] {
        margin-top:14px !important;
    }

    [data-buyer-option-group] > p {
        color:#394654 !important;
        font-size:11px !important;
        font-weight:650 !important;
    }

    .buyer-variant-button:not(.buyer-color-swatch-button) {
        min-height:40px !important;
        padding:0 15px !important;
        border-radius:9px !important;
        font-size:11px !important;
        font-weight:600 !important;
    }

    .sari-v7-availability {
        margin-top:18px !important;
        gap:22px !important;
        padding-top:17px !important;
        border-top:1px solid #edf0f3 !important;
    }

    .sari-v7-stock-block > p:first-child,
    .sari-v7-quantity-block > p:first-child {
        margin:0 0 6px !important;
        color:#687483 !important;
        font-size:10px !important;
        line-height:1.3 !important;
        font-weight:650 !important;
    }

    #buyerProductStock {
        margin:0 !important;
        font-size:13px !important;
        line-height:1.35 !important;
        font-weight:700 !important;
    }

    .sari-v7-quantity-block {
        width:176px !important;
    }

    .sari-v7-qty {
        height:42px !important;
        border-radius:9px !important;
    }

    .sari-v7-qty button {
        width:42px !important;
        font-size:16px !important;
    }

    .sari-v7-qty input {
        font-size:11.5px !important;
        font-weight:650 !important;
    }

    #buyerAddToCartForm {
        margin-top:15px !important;
    }

    #buyerAddToCartForm > .grid {
        gap:10px !important;
    }

    .buyer-product-primary,
    .buyer-product-secondary {
        height:46px !important;
        border-radius:10px !important;
        font-size:11px !important;
        font-weight:700 !important;
    }

    #buyerAddToCartForm > a {
        margin-top:9px !important;
        color:#7c8794 !important;
        font-size:9.5px !important;
        line-height:1.3 !important;
        font-weight:600 !important;
    }

    /* Seller information stays visible, but no card/container. */
    .sari-v7-seller-card {
        display:none !important;
    }

    .sari-detail-seller-inline {
        display:flex;
        width:100%;
        align-items:center;
        justify-content:space-between;
        gap:18px;
        margin-top:18px;
        padding-top:17px;
        border-top:1px solid #e9edf1;
        background:transparent;
    }

    .sari-detail-seller-identity {
        display:flex;
        min-width:0;
        align-items:center;
        gap:11px;
    }

    .sari-detail-seller-avatar {
        display:grid;
        width:40px;
        height:40px;
        flex:0 0 40px;
        place-items:center;
        overflow:hidden;
        border:1px solid #e3e7eb;
        border-radius:50%;
        background:#f3f4f6;
        color:#66717e;
        font-size:13px;
        line-height:1;
        font-weight:750;
        letter-spacing:-.02em;
    }

    .sari-detail-seller-avatar img {
        display:block;
        width:100%;
        height:100%;
        object-fit:cover;
        object-position:center;
    }

    .sari-detail-seller-label {
        margin:0;
        color:#9b690f;
        font-size:8px;
        line-height:1.2;
        font-weight:750;
        letter-spacing:.08em;
        text-transform:uppercase;
    }

    .sari-detail-seller-name {
        margin:3px 0 0;
        overflow:hidden;
        color:#273240;
        font-size:12px;
        line-height:1.35;
        font-weight:700;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    .sari-detail-seller-status {
        margin:2px 0 0;
        color:#87929e;
        font-size:9px;
        line-height:1.35;
        font-weight:450;
    }

    .sari-detail-seller-actions {
        display:flex;
        flex:0 0 auto;
        align-items:center;
        gap:8px;
    }

    /* Both public seller actions stay inside SARI's gold palette. */
    .sari-detail-seller-link {
    display:inline-flex;
    min-height:35px;
    align-items:center;
    justify-content:center;
    border:1px solid #d9dee5;
    border-radius:9px;
    background:#f3f4f6;
    padding:0 12px;
    color:#4b5563;
    font-size:9.5px;
    line-height:1;
    font-weight:650;
    text-decoration:none;
    transition:border-color 120ms ease,background-color 120ms ease,color 120ms ease !important;
}

.sari-detail-seller-link:hover {
    border-color:#cbd2da;
    background:#e9ecef;
    color:#374151;
}

    .sari-detail-seller-link:hover {
        border-color:#c88912;
        background:#fff1ce;
        color:#7f5209;
    }

    .sari-detail-seller-link--accent {
        border-color:#c88912;
        background:#c88912;
        color:#fff;
    }

    .sari-detail-seller-link--accent:hover {
        border-color:#ad730f;
        background:#ad730f;
        color:#fff;
    }

    @media (max-width:767px) {
        .sari-v7-buying-panel {
            padding:20px !important;
        }

        .sari-v7-availability {
            align-items:flex-end !important;
            gap:14px !important;
        }

        .sari-v7-quantity-block {
            width:150px !important;
        }

        .sari-detail-seller-inline {
            align-items:flex-start;
            flex-direction:column;
            gap:11px;
        }

        .sari-detail-seller-actions {
            width:100%;
        }

        .sari-detail-seller-link {
            flex:1 1 0;
        }
    }


    /* ============================================================
       BUYER REVIEWS — READABLE TYPOGRAPHY + SOLID GOLD SIZE CHOICE
       UI only; review data, image links, and variant logic unchanged.
    ============================================================ */
    .buyer-product-page .sari-buyer-reviews .sari-v7-section-kicker {
        font-size: 11px !important;
        font-weight: 750 !important;
        color: #a36b0e !important;
        line-height: 1.4 !important;
    }
    .buyer-product-page .sari-buyer-reviews .sari-v7-section-heading {
        font-size: 22px !important;
        font-weight: 700 !important;
        line-height: 1.3 !important;
    }
    .buyer-product-page .sari-buyer-reviews .sari-review-overall {
        font-size: 13px !important;
        font-weight: 550 !important;
        color: #596575 !important;
        line-height: 1.5 !important;
    }
    .buyer-product-page .sari-buyer-reviews .sari-review-overall > span {
        font-size: 14.5px !important;
        font-weight: 750 !important;
        color: #c88912 !important;
    }
    .buyer-product-page .sari-buyer-reviews .sari-review-stars {
        font-size: 18px !important;
        line-height: 1.25 !important;
    }
    .buyer-product-page .sari-buyer-reviews .sari-review-date {
        font-size: 11px !important;
        font-weight: 500 !important;
        line-height: 1.4 !important;
        color: #798492 !important;
    }
    .buyer-product-page .sari-buyer-reviews .sari-review-comment {
        font-size: 14px !important;
        font-weight: 450 !important;
        line-height: 1.75 !important;
        color: #475362 !important;
        overflow-wrap: anywhere;
    }
    .buyer-product-page .sari-buyer-reviews .sari-review-seller-label {
        font-size: 10.5px !important;
        font-weight: 750 !important;
    }
    .buyer-product-page .sari-buyer-reviews .sari-review-seller-date {
        font-size: 10.5px !important;
        color: #798492 !important;
    }
    .buyer-product-page .sari-buyer-reviews .sari-review-seller-comment {
        font-size: 12.5px !important;
        line-height: 1.7 !important;
        color: #505c69 !important;
    }
    .buyer-product-page .sari-buyer-reviews .text-center > p.mt-3 {
        font-size: 13.5px !important;
    }
    .buyer-product-page .sari-buyer-reviews .text-center > p.mt-1 {
        font-size: 11.5px !important;
        line-height: 1.5 !important;
    }

    /* Solid SARI gold only for the selected Size option; color swatches untouched. */
    .buyer-product-page .buyer-variant-button[data-buyer-option][data-option-group="Size" i].is-selected:not(.buyer-color-swatch-button),
    .buyer-product-page .buyer-variant-button[data-buyer-option][data-option-group="Size" i].is-selected:not(.buyer-color-swatch-button):hover,
    .buyer-product-page .buyer-variant-button[data-buyer-option][data-option-group="Size" i].is-selected:not(.buyer-color-swatch-button):focus-visible {
        border-color: #c88912 !important;
        background: #c88912 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 7px rgba(200, 137, 18, .16) !important;
    }

    /* Product size options — readable, restrained typography only. */
    .buyer-product-page [data-buyer-variant-selector] .sari-v7-section-title {
        font-size: 14px !important;
        font-weight: 700 !important;
        line-height: 1.4 !important;
    }
    .buyer-product-page [data-buyer-variant-selector] .sari-v7-section-help {
        font-size: 12px !important;
        font-weight: 450 !important;
        line-height: 1.55 !important;
        color: #697686 !important;
    }
    .buyer-product-page [data-buyer-option-group][data-option-group-name="Size" i] > p {
        font-size: 13px !important;
        font-weight: 700 !important;
        line-height: 1.45 !important;
        color: #354253 !important;
    }
    .buyer-product-page .buyer-variant-button[data-buyer-option][data-option-group="Size" i]:not(.buyer-color-swatch-button) {
        font-size: 13px !important;
        font-weight: 650 !important;
        line-height: 1.35 !important;
    }

</style>

<!-- SARI_PRODUCT_DETAILS_V8_LIGHT_NEUMORPHISM -->
<div data-sari-gallery-version="v9-clean" data-product-ui-version="clean-buying-panel-v2-2026" class="buyer-product-page mx-auto w-full max-w-[1500px] px-4 py-5 sm:px-6 lg:px-8 lg:py-7">

    {{-- BREADCRUMB --}}
    <nav class="mb-4 flex flex-wrap items-center gap-2 text-[10px] text-[#91887e]">
        <a href="{{ route('buyer.home') }}" class="transition hover:text-[#9a6817]">Home</a>
        <span class="text-[#c6beb5]">/</span>
        <a href="{{ route('buyer.products') }}" class="transition hover:text-[#9a6817]">Products</a>
        <span class="text-[#c6beb5]">/</span>
        <span class="font-semibold text-[#5c544c]">{{ $productName }}</span>
    </nav>

    {{-- PRODUCT HERO
         IMPORTANT: gallery and buying panel are direct siblings.
         Do not wrap both sides in one card/container. --}}
    <div class="sari-v7-hero-grid">

            {{-- LEFT: PRODUCT PHOTOS — NO MAIN IMAGE CONTAINER --}}
            <div class="sari-v7-gallery-area">
                <div class="sari-v7-gallery-layout {{ $gallery->count() > 1 ? 'has-thumbnails' : '' }}">
                    <div class="sari-v7-gallery-main">
                        <div class="sari-product-gallery-stage" data-product-gallery-stage>
                            @if ($mainImage)
                                <img
                                    id="buyerProductMainImage"
                                    src="{{ $mainImage }}"
                                    alt="{{ $productName }}"
                                    class="sari-v7-main-photo"
                                    title="Click to enlarge"
                                    onerror="this.classList.add('hidden'); document.getElementById('buyerProductImageFallback')?.classList.remove('hidden');"
                                >
                                <div id="buyerProductImageFallback" class="sari-v7-main-photo-placeholder hidden">
                                    <div class="text-center">
                                        <svg viewBox="0 0 24 24" class="mx-auto h-10 w-10" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                            <circle cx="9" cy="9" r="1.5"></circle>
                                            <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                                        </svg>
                                        <p class="mt-2 text-[10px]">Image unavailable</p>
                                    </div>
                                </div>
                            @else
                                <img
                                    id="buyerProductMainImage"
                                    src=""
                                    alt="{{ $productName }}"
                                    class="sari-v7-main-photo hidden"
                                >

                                <div id="buyerProductImagePlaceholder" class="sari-v7-main-photo-placeholder">
                                    <div class="text-center">
                                        <svg viewBox="0 0 24 24" class="mx-auto h-10 w-10" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                            <circle cx="9" cy="9" r="1.5"></circle>
                                            <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                                        </svg>
                                        <p class="mt-2 text-[10px]">No product image</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Product-card style marketplace badges. --}}
                            <div class="sari-detail-media-badges" aria-hidden="true">
                                @if ($stock > 0)
                                    <span class="sari-detail-media-badge">MALL</span>
                                @else
                                    <span class="sari-detail-media-badge sari-detail-media-badge--out">Out of Stock</span>
                                @endif
                            </div>

                            @if ($discountPercent > 0)
                                <span
                                    class="sari-detail-discount-corner"
                                    aria-label="{{ rtrim(rtrim(number_format($discountPercent, 2, '.', ''), '0'), '.') }} percent discount"
                                >
                                    -{{ rtrim(rtrim(number_format($discountPercent, 2, '.', ''), '0'), '.') }}%
                                </span>
                            @endif

                            @if ($showOfferCluster)
                                <div class="sari-detail-offer-cluster" aria-label="Product offer badges">
                                    @if ($discountPercent > 0)
                                        <span class="sari-detail-offer-item sari-detail-offer-item--discount">
                                            <span class="sari-detail-offer-stack">
                                                <span class="sari-detail-offer-kicker">MEGA</span>
                                                <span class="sari-detail-offer-main">Discount</span>
                                            </span>
                                        </span>
                                    @endif

                                    @if ($codAvailable)
                                        <span class="sari-detail-offer-item sari-detail-offer-item--cod">
                                            <span class="sari-detail-offer-stack">
                                                <span class="sari-detail-offer-kicker">COD</span>
                                                <span class="sari-detail-offer-main">Cash on Delivery</span>
                                            </span>
                                        </span>
                                    @endif

                                    @if ($freeShipping)
                                        <span class="sari-detail-offer-item sari-detail-offer-item--shipping">
                                            <span class="sari-detail-offer-stack">
                                                <span class="sari-detail-offer-kicker">XTRA</span>
                                                <span class="sari-detail-offer-main">
                                                    {{ $freeShippingMinimum > 0
                                                        ? 'Free Shipping ₱' . number_format($freeShippingMinimum, 0) . '+'
                                                        : 'Free Shipping' }}
                                                </span>
                                            </span>
                                        </span>
                                    @endif
                                </div>
                            @endif

                            @if ($gallery->count() > 1)
                                <button
                                    id="buyerGalleryPrev"
                                    type="button"
                                    class="sari-product-gallery-nav sari-product-gallery-nav--prev"
                                    aria-label="Previous product image"
                                >
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m15 18-6-6 6-6"></path>
                                    </svg>
                                </button>

                                <button
                                    id="buyerGalleryNext"
                                    type="button"
                                    class="sari-product-gallery-nav sari-product-gallery-nav--next"
                                    aria-label="Next product image"
                                >
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m9 18 6-6-6-6"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        <div class="sari-v7-gallery-meta">
                            {{-- Promo badges now live on the product image, matching the shared product card. --}}
                            <div class="sari-v7-gallery-meta-left"></div>

                            <div class="sari-v7-gallery-meta-right">
                                @if ($gallery->isNotEmpty())
                                    <span id="buyerGalleryCounter" class="sari-v7-gallery-count">
                                        1 / {{ $gallery->count() }}
                                    </span>
                                @endif

                                @if ($mainImage)
                                    <button
                                        id="buyerProductZoomButton"
                                        type="button"
                                        class="sari-v7-gallery-count cursor-pointer"
                                    >
                                        View image
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if ($gallery->count() > 1)
                            <div class="sari-v7-gallery-thumbs" aria-label="Product gallery">
                                @foreach ($gallery as $index => $galleryImage)
                                    <button
                                        type="button"
                                        class="sari-v7-gallery-thumb {{ $index === 0 ? 'is-active' : '' }}"
                                        data-product-thumb
                                        data-image="{{ $galleryImage }}"
                                        data-image-index="{{ $index }}"
                                        aria-label="View product image {{ $index + 1 }}"
                                    >
                                        <img
                                            src="{{ $galleryImage }}"
                                            alt="{{ $productName }} image {{ $index + 1 }}"
                                            loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                            onerror="this.closest('button')?.classList.add('hidden')"
                                        >
                                    </button>
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>

                {{-- PRODUCT INFORMATION — directly under gallery --}}
                <section class="sari-product-info-combined" aria-labelledby="sariProductInformationHeading">
                    <header class="sari-product-info-combined__header">
                        <div>
                            <p class="sari-product-info-combined__kicker">Product information</p>
                            <h2 id="sariProductInformationHeading" class="sari-product-info-combined__title">
                                Product details
                            </h2>
                        </div>
                    </header>

                    <div class="sari-product-info-combined__description">
                        <p class="sari-product-info-combined__label">Description</p>

                        @if ($description !== '')
                            <p class="sari-product-info-combined__copy whitespace-pre-line">
                                {{ $description }}
                            </p>
                        @else
                            <p class="sari-product-info-combined__copy is-muted">
                                The seller did not provide a description for this product.
                            </p>
                        @endif
                    </div>

                    <dl class="sari-product-info-combined__details">
                        <div class="sari-product-info-combined__detail">
                            <dt>Category</dt>
                            <dd>{{ $category }}</dd>
                        </div>

                        @if ($brand !== '')
                            <div class="sari-product-info-combined__detail">
                                <dt>Brand</dt>
                                <dd>{{ $brand }}</dd>
                            </div>
                        @endif

                        @if ($condition !== '')
                            <div class="sari-product-info-combined__detail">
                                <dt>Condition</dt>
                                <dd>{{ ucwords(str_replace('_', ' ', $condition)) }}</dd>
                            </div>
                        @endif

                        @if ($preparationDays !== null)
                            <div class="sari-product-info-combined__detail">
                                <dt>Preparation</dt>
                                <dd>
                                    {{ (int) $preparationDays }}
                                    day{{ (int) $preparationDays === 1 ? '' : 's' }}
                                </dd>
                            </div>
                        @endif

                        @if (!empty($packageParts))
                            <div class="sari-product-info-combined__detail sari-product-info-combined__detail--wide">
                                <dt>Package</dt>
                                <dd>{{ implode(' · ', $packageParts) }}</dd>
                            </div>
                        @endif

                        <div class="sari-product-info-combined__detail">
                            <dt>Shipping</dt>
                            <dd class="{{ $freeShipping ? 'is-positive' : '' }}">
                                {{ $freeShipping ? 'Free Shipping' : 'Standard Shipping' }}
                            </dd>
                        </div>
                    </dl>
                </section>
            </div>

            {{-- RIGHT: PREMIUM BUYING PANEL --}}
            <div class="sari-v7-buying-panel">
                <h1 class="sari-v7-title">
                    {{ $productName }}
                </h1>

                @if ($brand !== '' || $sku !== '')
                    <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-[9px] text-[#8d857c]">
                        @if ($brand !== '')
                            <span>Brand: <strong class="font-semibold text-[#5c544c]">{{ $brand }}</strong></span>
                        @endif

                        @if ($sku !== '')
                            @if ($brand !== '')
                                <span class="text-[#c9c2ba]">•</span>
                            @endif
                            <span>SKU: <strong class="font-semibold text-[#5c544c]">{{ $sku }}</strong></span>
                        @endif
                    </div>
                @endif

                <div class="sari-v7-meta-row">
                    <span class="font-semibold text-[#5f574f]">
                        <span class="text-[#d89b11]">★</span>
                        {{ $ratingCount > 0 ? number_format($rating, 1) : 'No rating' }}
                    </span>
                    <span class="text-[#cdc6bd]">|</span>
                    <span class="text-[#81786f]">{{ number_format($ratingCount) }} review{{ $ratingCount === 1 ? '' : 's' }}</span>
                    <span class="text-[#cdc6bd]">|</span>
                    <span class="text-[#81786f]">{{ number_format($sold) }} sold</span>
                </div>

                <div class="sari-v7-price-box">
                    <div class="flex flex-wrap items-end justify-between gap-3">
                        <div class="flex flex-wrap items-end gap-2.5">
                            <strong id="buyerProductPrice" class="sari-v7-price">
                                {{ $formatPeso($price) }}
                            </strong>

                            <span id="buyerProductOldPrice" class="{{ $oldPrice ? '' : 'hidden' }} pb-0.5 text-[10px] text-[#aaa198] line-through">
                                {{ $oldPrice ? $formatPeso($oldPrice) : '' }}
                            </span>
                        </div>

                    </div>
                </div>

@if ($hasVariants && $variants->isNotEmpty())
                    <div class="sari-v7-section" data-buyer-variant-selector>
                        <div>
                            <p class="sari-v7-section-title">Choose your variant</p>
                            <p class="sari-v7-section-help">
                                @if ($variantOptionGroups->isNotEmpty())
                                    Select {{ $variantOptionGroups->pluck('name')->implode(' first, then ') }}.
                                @else
                                    Select your preferred variant.
                                @endif
                            </p>
                        </div>

                        @if ($variantOptionGroups->isNotEmpty())
                            <div class="mt-4 space-y-4" data-buyer-option-groups>
                                @foreach ($variantOptionGroups as $groupIndex => $group)
                                    @php
                                        $isColorGroup = $isColorOptionGroup($group['name']);
                                    @endphp

                                    <div
                                        data-buyer-option-group
                                        data-option-group-name="{{ $group['name'] }}"
                                        data-option-group-index="{{ $groupIndex }}"
                                        data-option-group-kind="{{ $isColorGroup ? 'color' : 'standard' }}"
                                    >
                                        <p class="text-[10px] font-semibold text-[#344050]">
                                            {{ $group['name'] }}
                                        </p>

                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @foreach ((array) $group['values'] as $value)
                                                @php
                                                    $swatch = $isColorGroup ? $resolveColorSwatch($value) : null;
                                                @endphp
                                                <button
                                                    type="button"
                                                    class="buyer-variant-button {{ $isColorGroup ? 'buyer-color-swatch-button' : '' }} rounded-[9px] px-4 py-2.5 text-[9px] font-semibold"
                                                    data-buyer-option
                                                    data-option-group="{{ $group['name'] }}"
                                                    data-option-value="{{ $value }}"
                                                    data-option-index="{{ $groupIndex }}"
                                                    aria-label="{{ $isColorGroup ? 'Color ' . $value : $group['name'] . ' ' . $value }}"
                                                    title="{{ $value }}"
                                                    aria-pressed="false"
                                                    @disabled($groupIndex > 0)
                                                >
                                                    @if ($isColorGroup)
                                                        <span
                                                            class="buyer-color-swatch"
                                                            style="--buyer-color-swatch: {{ $swatch }};"
                                                            aria-hidden="true"
                                                        ></span>
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        @else
                            {{-- Legacy fallback for old variant rows that do not contain structured option values. --}}
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($variantMatrix as $variant)
                                    <button
                                        type="button"
                                        class="buyer-variant-button rounded-[9px] px-4 py-2.5 text-[9px] font-semibold"
                                        data-buyer-variant-direct
                                        data-id="{{ $variant['id'] }}"
                                        @disabled(!$variant['available'])
                                    >
                                        {{ $variant['label'] !== '' ? $variant['label'] : ('Variant #' . $variant['id']) }}
                                        @if (!$variant['available'])
                                            <span class="ml-1 text-[#aa7a7a]">· Out</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @elseif (!empty($optionGroups))
                    <div class="mt-5 border-t border-[#eee8df] pt-4">
                        @foreach ($optionGroups as $groupName => $values)
                            <div class="{{ !$loop->first ? 'mt-4' : '' }}">
                                <p class="text-[8.5px] font-semibold text-[#5e554c]">{{ $groupName }}</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ((array) $values as $value)
                                        <span class="rounded-full border border-[#ddd6cc] bg-white px-4 py-2.5 text-[8px] font-medium text-[#6e655c]">
                                            {{ $value }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="sari-v7-availability">
                    <div
                        id="buyerAvailabilityBlock"
                        class="sari-v7-stock-block {{ $hasVariants && $variants->isNotEmpty() ? 'hidden' : '' }}"
                    >
                        <p class="text-[9px] font-semibold text-[#776e64]">Availability</p>
                        <p id="buyerProductStock" class="mt-1.5 text-[11px] font-bold {{ $stock > 0 ? 'text-[#4f8065]' : 'text-[#b65e5e]' }}">
                            {{ $stock > 0 ? number_format($stock) . ' in stock' : 'Out of stock' }}
                        </p>
                    </div>

                    <div class="sari-v7-quantity-block">
                        <p class="mb-1.5 text-[9px] font-semibold text-[#6a6259]">Quantity</p>
                        <div class="sari-v7-qty">
                            <button id="buyerQtyMinus" type="button" aria-label="Decrease quantity" @disabled($hasVariants && $variants->isNotEmpty())>−</button>
                            <input id="buyerQuantity" type="number" min="1" max="{{ max(1, $stock) }}" value="1" @disabled($hasVariants && $variants->isNotEmpty())>
                            <button id="buyerQtyPlus" type="button" aria-label="Increase quantity" @disabled($hasVariants && $variants->isNotEmpty())>+</button>
                        </div>
                    </div>
                </div>

                <form id="buyerAddToCartForm" method="POST" action="{{ route('buyer.cart.items.store') }}" class="mt-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $productId }}">
                    <input id="buyerVariantId" type="hidden" name="variant_id" value="">
                    <input id="buyerQuantityHidden" type="hidden" name="quantity" value="1">

                    <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                        <button
                            id="buyerAddToCartButton"
                            type="submit"
                            class="buyer-product-secondary inline-flex h-12 items-center justify-center gap-2 rounded-[11px] px-4 text-[10px] font-bold"
                            @disabled($stock <= 0 || ($hasVariants && $variants->isNotEmpty()))
                        >
                            <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.9">
                                <circle cx="9" cy="20" r="1"></circle>
                                <circle cx="18" cy="20" r="1"></circle>
                                <path d="M3 4h2l2 11h11l2-8H6"></path>
                            </svg>
                            <span id="buyerAddToCartLabel">{{ $hasVariants && $variants->isNotEmpty() ? 'Select options' : ($stock > 0 ? 'Add to Cart' : 'Out of Stock') }}</span>
                        </button>

                        <button
                            id="buyerBuyNowButton"
                            type="button"
                            class="buyer-product-primary inline-flex h-12 items-center justify-center gap-2 rounded-[11px] px-5 text-[10px] font-bold"
                            @disabled($stock <= 0 || ($hasVariants && $variants->isNotEmpty()))
                        >
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="m13 2-7 12h6l-1 8 7-12h-6l1-8Z"></path>
                            </svg>
                            Buy Now
                        </button>
                    </div>

                    <a
                        href="{{ route('buyer.cart') }}"
                        class="mt-2.5 inline-flex text-[8px] font-semibold text-[#8c7f70] transition hover:text-[#a76c0c]"
                    >
                        View Cart
                    </a>
                </form>

<div class="sari-detail-seller-inline">
                    <div class="sari-detail-seller-identity">
                        <span class="sari-detail-seller-avatar" aria-hidden="true">
                            @if ($sellerAvatar)
                                <img src="{{ $sellerAvatar }}" alt="" loading="lazy">
                            @else
                                {{ $sellerInitial }}
                            @endif
                        </span>

                        <div class="min-w-0">
                            <p class="sari-detail-seller-label">Sold by</p>
                            <p class="sari-detail-seller-name">{{ $shopName }}</p>
                            <p class="sari-detail-seller-status">Approved SARI marketplace seller</p>
                        </div>
                    </div>

                    <div class="sari-detail-seller-actions">
                        @if ($shopSlug !== '')
                            <a href="{{ route('buyer.shop', ['shop' => $shopSlug]) }}" class="sari-detail-seller-link sari-detail-seller-link--accent">
                                Visit Shop
                            </a>
                        @endif

                        @if ($sellerId > 0)
                            <a href="{{ route('buyer.messages', ['seller' => $sellerId]) }}" class="sari-detail-seller-link">
                                Message Seller
                            </a>
                        @endif
                    </div>
                </div>
            </div>
    </div>

    {{-- REVIEWS --}}
    <section class="sari-v7-section-card sari-buyer-reviews mt-5">
        <div class="flex flex-col gap-3 border-b border-[#f0ebe5] pb-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="sari-v7-section-kicker">Buyer feedback</p>
                <h2 class="sari-v7-section-heading">Ratings & reviews</h2>
            </div>

            <p class="sari-review-overall text-[9px] text-[#81786f]">
                <span class="font-bold text-[#d89b11]">★ {{ $ratingCount > 0 ? number_format($rating, 1) : '—' }}</span>
                · {{ number_format($ratingCount) }} review{{ $ratingCount === 1 ? '' : 's' }}
            </p>
        </div>

        @if ($reviews->isNotEmpty())
            <div class="mt-4 grid grid-cols-1 gap-3 lg:grid-cols-2">
                @foreach ($reviews as $review)
                    <article class="sari-review-entry rounded-[14px] border border-[#ebe5dd] bg-white p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="sari-review-stars flex items-center gap-1 text-[#e0a00e]">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= (int) ($review['rating'] ?? 0) ? '' : 'opacity-25' }}">★</span>
                                @endfor
                            </div>

                            <span class="sari-review-date text-[7px] text-[#aaa198]">{{ $review['created_at'] ?? '' }}</span>
                        </div>

                        <p class="sari-review-comment mt-3 text-[9px] leading-5 text-[#6f665d]">
                            {{ trim((string) ($review['comment'] ?? '')) !== '' ? $review['comment'] : 'Rating submitted without a written comment.' }}
                        </p>

                        @if (!empty($review['image_urls']))
                            <div class="mt-3 grid grid-cols-4 gap-2 sm:grid-cols-5" aria-label="Review photos">
                                @foreach (array_slice((array) $review['image_urls'], 0, 5) as $reviewImageUrl)
                                    <a
                                        href="{{ $reviewImageUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="block aspect-square overflow-hidden rounded-[9px] border border-[#e5e8eb] bg-[#f4f5f6]"
                                    >
                                        <img src="{{ $reviewImageUrl }}" alt="Buyer review photo" class="h-full w-full object-cover" loading="lazy">
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        @if (!empty($review['seller_reply']['reply']))
                            <div class="mt-3 rounded-[11px] border border-[#eadfcd] bg-[#fffbf3] px-3 py-2.5">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="sari-review-seller-label text-[7px] font-bold uppercase tracking-[.08em] text-[#a8731f]">Seller reply</span>
                                    <span class="sari-review-seller-date text-[7px] text-[#aaa198]">{{ $review['seller_reply']['created_at'] ?? '' }}</span>
                                </div>
                                <p class="sari-review-seller-comment mt-1.5 text-[8px] leading-4 text-[#6f665d]">{{ $review['seller_reply']['reply'] }}</p>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        @else
            <div
                class="mt-4 rounded-[14px] px-5 py-8 text-center"
                style="border:1px solid #e3e7eb; background:#f6f7f8; box-shadow:none;"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="mx-auto h-9 w-9"
                    style="color:#a6adb5;"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>
                    <path d="m12 7 .9 1.8 2 .3-1.45 1.4.35 2-1.8-.95-1.8.95.35-2L9.1 9.1l2-.3L12 7z"></path>
                </svg>
                <p class="mt-3 text-[10px] font-semibold text-[#46515d]">No reviews yet</p>
                <p class="mt-1 text-[8.5px] text-[#7f8994]">Buyer reviews will appear here after completed orders.</p>
            </div>
        @endif
    </section>

    {{-- RELATED PRODUCTS — REUSE THE SAME SHARED BUYER PRODUCT CARD --}}
    @if ($relatedProducts->isNotEmpty())
        <section class="mt-7" data-related-products-shared-card>
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <p class="text-[8px] font-bold uppercase tracking-[.11em] text-[#ae7a20]">Discover more</p>
                    <h2 class="mt-1 text-[21px] font-bold tracking-[-.03em] text-[#302a24]">You may also like</h2>
                </div>

                <a
                    href="{{ route('buyer.products') }}"
                    class="text-[9px] font-semibold text-[#966719] hover:underline"
                >
                    View all products
                </a>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($relatedProducts as $related)
                    <x-buyer.product-card
                        :product="$related"
                        :priority="false"
                        :interactive="false"
                        :show-promotions="true"
                        :show-stock="true"
                        :show-quick-add="true"
                    />
                @endforeach
            </div>
        </section>
    @endif

</div>

<div
    id="buyerProductLightbox"
    class="buyer-product-lightbox fixed inset-0 z-[120] flex items-center justify-center bg-black/72 p-4"
    aria-hidden="true"
>
    <button
        id="buyerProductLightboxClose"
        type="button"
        class="absolute right-5 top-5 grid h-10 w-10 place-items-center rounded-full border border-white/20 bg-black/45 text-white"
        aria-label="Close image preview"
    >
        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="m7 7 10 10M17 7 7 17"></path>
        </svg>
    </button>

    <img
        id="buyerProductLightboxImage"
        src="{{ $mainImage ?? '' }}"
        alt="{{ $productName }} enlarged image"
        class="max-h-[88vh] max-w-[92vw] rounded-[14px] bg-white object-contain p-2 shadow-2xl"
    >
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('livewire:navigated', function () {
    window.__SARI_BUYER_PRODUCT_ABORT__?.abort();

    const pageAbort = new AbortController();
    window.__SARI_BUYER_PRODUCT_ABORT__ = pageAbort;
    const signal = pageAbort.signal;

    const hasVariants = @json($hasVariants && $variants->isNotEmpty());
    const initialProductStock = Number(@json($stock));
    const initialProductPrice = Number(@json($price));
    const initialOldPrice = Number(@json($oldPrice ?? 0));
    const defaultImage = @json($mainImage);
    const galleryImages = @json($gallery->values()->all());
    const variantMatrix = @json($variantMatrix->values()->all());
    const variantOptionGroups = @json($variantOptionGroupsPayload);
    const cartUrl = @json(route('buyer.cart.items.store'));
    const checkoutUrl = @json(route('buyer.checkout'));
    const buyNowMode = new URLSearchParams(window.location.search).get('buy_now') === '1';

    const mainImage = document.getElementById('buyerProductMainImage');
    const imagePlaceholder = document.getElementById('buyerProductImagePlaceholder');
    const thumbButtons = Array.from(document.querySelectorAll('[data-product-thumb]'));
    const optionButtons = Array.from(document.querySelectorAll('[data-buyer-option]'));
    const optionGroupElements = Array.from(document.querySelectorAll('[data-buyer-option-group]'));
    const directVariantButtons = Array.from(document.querySelectorAll('[data-buyer-variant-direct]'));
    const variantHint = document.querySelector('[data-buyer-variant-hint]');
    const galleryCounter = document.getElementById('buyerGalleryCounter');
    const galleryPrev = document.getElementById('buyerGalleryPrev');
    const galleryNext = document.getElementById('buyerGalleryNext');
    const galleryStage = document.querySelector('[data-product-gallery-stage]');
    const zoomButton = document.getElementById('buyerProductZoomButton');
    const lightbox = document.getElementById('buyerProductLightbox');
    const lightboxImage = document.getElementById('buyerProductLightboxImage');
    const lightboxClose = document.getElementById('buyerProductLightboxClose');

    const variantInput = document.getElementById('buyerVariantId');
    const selectedVariantLabel = document.getElementById('buyerSelectedVariantLabel');

    const priceText = document.getElementById('buyerProductPrice');
    const oldPriceText = document.getElementById('buyerProductOldPrice');
    const stockText = document.getElementById('buyerProductStock');
    const availabilityBlock = document.getElementById('buyerAvailabilityBlock');

    const qtyInput = document.getElementById('buyerQuantity');
    const qtyHidden = document.getElementById('buyerQuantityHidden');
    const qtyMinus = document.getElementById('buyerQtyMinus');
    const qtyPlus = document.getElementById('buyerQtyPlus');

    const addToCartForm = document.getElementById('buyerAddToCartForm');
    const addToCartButton = document.getElementById('buyerAddToCartButton');
    const addToCartLabel = document.getElementById('buyerAddToCartLabel');
    const buyNowButton = document.getElementById('buyerBuyNowButton');
    const notice = document.getElementById('buyerProductNotice');

    let activeStock = hasVariants ? 0 : initialProductStock;
    let activeGalleryIndex = 0;
    let showingVariantImage = false;
    let touchStartX = null;
    const selectedOptions = {};

    function peso(value) {
        return '₱' + Number(value || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function showNotice(message, danger = false) {
        window.SariBuyerToast?.show(String(message || ''), danger ? 'error' : 'success');
    }

    function renderMainImage(src) {
        if (!mainImage || !src) return;

        mainImage.src = src;
        mainImage.classList.remove('hidden');

        if (lightboxImage) {
            lightboxImage.src = src;
        }

        imagePlaceholder?.classList.add('hidden');
        imagePlaceholder?.classList.remove('grid');
        document.getElementById('buyerProductImageFallback')?.classList.add('hidden');
    }

    function updateGalleryUi() {
        thumbButtons.forEach((button, index) => {
            const isActive = !showingVariantImage && index === activeGalleryIndex;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-current', isActive ? 'true' : 'false');
        });

        if (galleryCounter) {
            if (showingVariantImage) {
                galleryCounter.textContent = 'Variant image';
                galleryCounter.classList.add('is-variant-preview');
            } else if (galleryImages.length > 0) {
                galleryCounter.textContent = `${activeGalleryIndex + 1} / ${galleryImages.length}`;
                galleryCounter.classList.remove('is-variant-preview');
            }
        }
    }

    function showGalleryImage(index, { scrollThumb = true } = {}) {
        if (!galleryImages.length) return;

        const length = galleryImages.length;
        activeGalleryIndex = ((Number(index) % length) + length) % length;
        showingVariantImage = false;

        renderMainImage(galleryImages[activeGalleryIndex]);
        updateGalleryUi();

        if (scrollThumb) {
            thumbButtons[activeGalleryIndex]?.scrollIntoView({
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
                block: 'nearest',
                inline: 'nearest',
            });
        }
    }

    function showVariantImage(src) {
        if (!src) {
            showGalleryImage(activeGalleryIndex, { scrollThumb: false });
            return;
        }

        showingVariantImage = true;
        renderMainImage(src);
        updateGalleryUi();
    }

    function stepGallery(direction) {
        if (!galleryImages.length) return;

        // A resolved variant may temporarily replace the main photo. The
        // gallery arrows always return to and navigate only the seller's
        // Cover Image + Product Gallery collection.
        const offset = showingVariantImage ? 0 : Number(direction || 0);
        const nextIndex = showingVariantImage
            ? activeGalleryIndex
            : activeGalleryIndex + offset;

        showGalleryImage(nextIndex);
    }

    thumbButtons.forEach((button, index) => {
        button.addEventListener('click', function () {
            const explicitIndex = Number(this.dataset.imageIndex);
            showGalleryImage(Number.isFinite(explicitIndex) ? explicitIndex : index);
        }, { signal });
    });

    galleryPrev?.addEventListener('click', function (event) {
        event.stopPropagation();
        stepGallery(-1);
    }, { signal });

    galleryNext?.addEventListener('click', function (event) {
        event.stopPropagation();
        stepGallery(1);
    }, { signal });

    galleryStage?.addEventListener('touchstart', function (event) {
        touchStartX = event.touches?.[0]?.clientX ?? null;
    }, { signal, passive: true });

    galleryStage?.addEventListener('touchend', function (event) {
        if (touchStartX === null) return;

        const touchEndX = event.changedTouches?.[0]?.clientX ?? touchStartX;
        const deltaX = touchEndX - touchStartX;
        touchStartX = null;

        if (Math.abs(deltaX) < 45 || galleryImages.length < 2) return;
        stepGallery(deltaX > 0 ? -1 : 1);
    }, { signal, passive: true });

    function openLightbox() {
        if (!lightbox || !mainImage?.src) return;
        if (lightboxImage) lightboxImage.src = mainImage.src;
        lightbox.classList.add('is-open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }

    function closeLightbox() {
        if (!lightbox) return;
        lightbox.classList.remove('is-open');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    }

    zoomButton?.addEventListener('click', openLightbox, { signal });
    mainImage?.addEventListener('click', openLightbox, { signal });
    lightboxClose?.addEventListener('click', closeLightbox, { signal });

    lightbox?.addEventListener('click', function (event) {
        if (event.target === lightbox) closeLightbox();
    }, { signal });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeLightbox();
            return;
        }

        if (lightbox?.classList.contains('is-open')) return;

        if (event.key === 'ArrowLeft' && galleryImages.length > 1) {
            stepGallery(-1);
        }

        if (event.key === 'ArrowRight' && galleryImages.length > 1) {
            stepGallery(1);
        }
    }, { signal });

    function syncQuantity() {
        if (!qtyInput) return;

        const max = Math.max(1, Number(activeStock || 0));
        let quantity = Math.max(1, Number(qtyInput.value || 1));

        quantity = Math.min(quantity, max);
        qtyInput.value = String(quantity);

        if (qtyHidden) {
            qtyHidden.value = String(quantity);
        }
    }

    qtyMinus?.addEventListener('click', function () {
        if (!qtyInput) return;
        qtyInput.value = String(Math.max(1, Number(qtyInput.value || 1) - 1));
        syncQuantity();
    }, { signal });

    qtyPlus?.addEventListener('click', function () {
        if (!qtyInput) return;
        qtyInput.value = String(Number(qtyInput.value || 1) + 1);
        syncQuantity();
    }, { signal });

    qtyInput?.addEventListener('input', syncQuantity, { signal });

    function normalizeOptionToken(value) {
        return String(value ?? '').trim().toLocaleLowerCase();
    }

    function variantOptionValue(variant, groupName) {
        const options = variant && typeof variant.options === 'object' && variant.options !== null
            ? variant.options
            : {};
        const wanted = normalizeOptionToken(groupName);

        for (const [key, value] of Object.entries(options)) {
            if (normalizeOptionToken(key) === wanted) {
                return String(value ?? '').trim();
            }
        }

        return '';
    }

    function variantMatchesSelections(variant, selections) {
        return Object.entries(selections).every(([groupName, selectedValue]) => {
            return normalizeOptionToken(variantOptionValue(variant, groupName))
                === normalizeOptionToken(selectedValue);
        });
    }

    function inStockVariant(variant) {
        return Boolean(variant)
            && Number(variant.stock || 0) > 0
            && variant.available !== false;
    }

    function orderedSelectionValues() {
        return variantOptionGroups
            .map((group) => selectedOptions[group.name])
            .filter((value) => String(value ?? '').trim() !== '');
    }

    function nextUnselectedGroup() {
        return variantOptionGroups.find((group) => !selectedOptions[group.name]) || null;
    }

    function setQuantityEnabled(enabled) {
        if (qtyInput) qtyInput.disabled = !enabled;
        if (qtyMinus) qtyMinus.disabled = !enabled;
        if (qtyPlus) qtyPlus.disabled = !enabled;
    }

    function setStockMessage(message, available = null) {
        if (!stockText) return;

        const cleanMessage = String(message || '').trim();
        stockText.textContent = cleanMessage;
        stockText.classList.toggle('text-[#4f8065]', available === true);
        stockText.classList.toggle('text-[#b65e5e]', available === false);
        stockText.classList.remove('text-[#7b8795]');

        if (availabilityBlock) {
            availabilityBlock.classList.toggle('hidden', cleanMessage === '');
        }
    }

    function restoreBaseVariantDisplay(message = '') {
        if (variantInput) variantInput.value = '';

        activeStock = 0;

        if (priceText) {
            priceText.textContent = peso(initialProductPrice);
        }

        if (oldPriceText) {
            oldPriceText.textContent = initialOldPrice > 0 ? peso(initialOldPrice) : '';
            oldPriceText.classList.toggle('hidden', initialOldPrice <= 0);
        }

        if (qtyInput) {
            qtyInput.max = '1';
            qtyInput.value = '1';
        }

        if (qtyHidden) qtyHidden.value = '1';

        setQuantityEnabled(false);

        if (addToCartButton) addToCartButton.disabled = true;
        if (buyNowButton) buyNowButton.disabled = true;
        if (addToCartLabel) addToCartLabel.textContent = 'Select options';

        // Do not show instructional stock text while options are incomplete.
        // Availability appears only after an exact variant is resolved.
        setStockMessage('', null);

        if (defaultImage) {
            showGalleryImage(0, { scrollThumb: false });
        }
    }

    function applyResolvedVariant(variant) {
        if (!variant) return;

        const variantId = String(variant.id || '');
        const price = Number(variant.price || 0);
        const oldPrice = Number(variant.old_price || 0);
        const stock = Math.max(0, Number(variant.stock || 0));
        const image = String(variant.image || '');
        const selectionValues = orderedSelectionValues();
        const fallbackLabel = String(variant.label || 'Selected variant');
        const label = selectionValues.length ? selectionValues.join(' • ') : fallbackLabel;

        if (variantInput) variantInput.value = variantId;
        if (selectedVariantLabel) selectedVariantLabel.textContent = label;

        if (priceText) priceText.textContent = peso(price);

        if (oldPriceText) {
            oldPriceText.textContent = oldPrice > 0 ? peso(oldPrice) : '';
            oldPriceText.classList.toggle('hidden', oldPrice <= 0);
        }

        activeStock = stock;
        setStockMessage(
            stock > 0 ? `${stock.toLocaleString('en-PH')} in stock` : 'Out of stock',
            stock > 0
        );

        if (qtyInput) {
            qtyInput.max = String(Math.max(1, stock));
            qtyInput.value = '1';
        }

        if (qtyHidden) qtyHidden.value = '1';

        const purchasable = stock > 0;
        setQuantityEnabled(purchasable);

        if (addToCartButton) addToCartButton.disabled = !purchasable;
        if (buyNowButton) buyNowButton.disabled = !purchasable;
        if (addToCartLabel) {
            addToCartLabel.textContent = purchasable ? 'Add to Cart' : 'Out of Stock';
        }

        if (image) {
            showVariantImage(image);
        } else if (defaultImage) {
            showGalleryImage(0, { scrollThumb: false });
        }

        if (variantHint) {
            variantHint.textContent = purchasable
                ? 'Variant selected. Price, image and stock are updated automatically.'
                : 'The selected variant is currently out of stock.';
            variantHint.classList.toggle('is-ready', purchasable);
        }

        syncQuantity();
    }

    function updateGroupStatuses() {
        optionGroupElements.forEach((groupElement, groupIndex) => {
            const groupName = String(groupElement.dataset.optionGroupName || '');
            const status = groupElement.querySelector('[data-buyer-option-group-status]');
            const selectedValue = selectedOptions[groupName];

            if (!status) return;

            if (selectedValue) {
                status.textContent = String(selectedValue);
                status.classList.add('is-selected');
                return;
            }

            status.classList.remove('is-selected');

            if (groupIndex === 0) {
                status.textContent = 'Choose one';
                return;
            }

            const previousGroup = variantOptionGroups[groupIndex - 1];
            status.textContent = previousGroup && selectedOptions[previousGroup.name]
                ? 'Choose one'
                : `Select ${previousGroup?.name || 'previous option'} first`;
        });
    }

    function refreshOptionAvailability() {
        optionButtons.forEach((button) => {
            const groupIndex = Number(button.dataset.optionIndex || 0);
            const groupName = String(button.dataset.optionGroup || '');
            const optionValue = String(button.dataset.optionValue || '');
            const previousGroups = variantOptionGroups.slice(0, groupIndex);
            const previousComplete = previousGroups.every((group) => Boolean(selectedOptions[group.name]));

            const candidateSelections = {};
            previousGroups.forEach((group) => {
                if (selectedOptions[group.name]) {
                    candidateSelections[group.name] = selectedOptions[group.name];
                }
            });
            candidateSelections[groupName] = optionValue;

            const available = previousComplete && variantMatrix.some((variant) => {
                return inStockVariant(variant)
                    && variantMatchesSelections(variant, candidateSelections);
            });

            button.disabled = !available;
            button.setAttribute('aria-disabled', available ? 'false' : 'true');

            const selected = normalizeOptionToken(selectedOptions[groupName])
                === normalizeOptionToken(optionValue);
            button.classList.toggle('is-selected', selected);
            button.setAttribute('aria-pressed', selected ? 'true' : 'false');
        });

        updateGroupStatuses();
    }

    function resolveSelectedVariant() {
        const complete = variantOptionGroups.length > 0
            && variantOptionGroups.every((group) => Boolean(selectedOptions[group.name]));

        if (!complete) {
            const nextGroup = nextUnselectedGroup();
            const chosen = orderedSelectionValues();
            const prompt = nextGroup ? `Select ${nextGroup.name}` : 'Select options';

            if (selectedVariantLabel) {
                selectedVariantLabel.textContent = chosen.length
                    ? `${chosen.join(' • ')} · ${prompt}`
                    : prompt;
            }

            restoreBaseVariantDisplay();

            if (variantHint) {
                variantHint.textContent = nextGroup
                    ? `Choose ${nextGroup.name}. Only available combinations can be selected.`
                    : 'Choose the required options.';
                variantHint.classList.remove('is-ready');
            }

            return;
        }

        const variant = variantMatrix.find((item) => variantMatchesSelections(item, selectedOptions));

        if (!variant) {
            restoreBaseVariantDisplay('This combination is unavailable.');
            if (selectedVariantLabel) selectedVariantLabel.textContent = 'Combination unavailable';
            if (variantHint) {
                variantHint.textContent = 'This option combination does not exist. Choose another combination.';
                variantHint.classList.remove('is-ready');
            }
            return;
        }

        applyResolvedVariant(variant);
    }

    optionButtons.forEach((button) => {
        button.addEventListener('click', function () {
            if (this.disabled) return;

            const groupIndex = Number(this.dataset.optionIndex || 0);
            const groupName = String(this.dataset.optionGroup || '');
            const optionValue = String(this.dataset.optionValue || '');

            // Changing an earlier option invalidates every later choice. This
            // keeps Size -> Color selection deterministic and prevents a stale
            // color from silently selecting the wrong variant.
            variantOptionGroups.slice(groupIndex).forEach((group) => {
                delete selectedOptions[group.name];
            });

            selectedOptions[groupName] = optionValue;

            refreshOptionAvailability();
            resolveSelectedVariant();
        }, { signal });
    });

    directVariantButtons.forEach((button) => {
        button.addEventListener('click', function () {
            if (this.disabled) return;

            directVariantButtons.forEach((item) => {
                item.classList.remove('is-selected');
                item.setAttribute('aria-pressed', 'false');
            });

            this.classList.add('is-selected');
            this.setAttribute('aria-pressed', 'true');

            const variantId = Number(this.dataset.id || 0);
            const variant = variantMatrix.find((item) => Number(item.id || 0) === variantId);

            if (variant) {
                applyResolvedVariant(variant);
            }
        }, { signal });
    });

    if (galleryImages.length > 0) {
        showGalleryImage(0, { scrollThumb: false });
    }

    if (hasVariants) {
        if (variantOptionGroups.length > 0) {
            refreshOptionAvailability();
            const firstGroup = variantOptionGroups[0];
            restoreBaseVariantDisplay();
            if (selectedVariantLabel) {
                selectedVariantLabel.textContent = `Select ${firstGroup?.name || 'option'}`;
            }
        } else {
            restoreBaseVariantDisplay();
        }
    } else {
        setQuantityEnabled(initialProductStock > 0);
    }

    async function submitCart({ buyNow = false } = {}) {
        if (hasVariants && !variantInput?.value) {
            showNotice('Choose a product variant before continuing.', true);
            return;
        }

        syncQuantity();

        if (activeStock <= 0) {
            showNotice('This product option is currently out of stock.', true);
            return;
        }

        const targetButton = buyNow ? buyNowButton : addToCartButton;
        if (!targetButton || !addToCartForm) return;

        const originalHtml = targetButton.innerHTML;
        targetButton.disabled = true;
        targetButton.innerHTML = `
            <svg viewBox="0 0 24 24" class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 3a9 9 0 1 1-6.36 2.64"></path>
            </svg>
            ${buyNow ? 'Preparing...' : 'Adding...'}
        `;

        try {
            const response = await fetch(cartUrl, {
                method: 'POST',
                body: new FormData(addToCartForm),
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal,
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                const validationMessage = data?.errors
                    ? Object.values(data.errors).flat().find(Boolean)
                    : null;

                throw new Error(validationMessage || data?.message || 'Unable to continue with this product.');
            }

            document.dispatchEvent(new CustomEvent('sari:cart-updated', {
                detail: {
                    count: Number(data?.cart_count || 0),
                },
            }));

            if (buyNow) {
                const itemId = Number(data?.item_id || 0);

                if (!itemId) {
                    throw new Error('Buy Now could not prepare the checkout item.');
                }

                window.location.href = `${checkoutUrl}?buy_now=${encodeURIComponent(itemId)}`;
                return;
            }

            showNotice(data?.message || 'Item added to cart.');
        } catch (error) {
            if (error?.name === 'AbortError') return;
            showNotice(error?.message || 'Unable to continue with this product.', true);
        } finally {
            targetButton.disabled = activeStock <= 0;
            targetButton.innerHTML = originalHtml;
        }
    }

    addToCartForm?.addEventListener('submit', function (event) {
        event.preventDefault();
        submitCart({ buyNow: false });
    }, { signal });

    buyNowButton?.addEventListener('click', function () {
        submitCart({ buyNow: true });
    }, { signal });

    if (buyNowMode) {
        buyNowButton?.scrollIntoView({
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
            block: 'center',
        });
    }

    syncQuantity();
});
</script>
@endpush
