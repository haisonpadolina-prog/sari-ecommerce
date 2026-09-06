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

    $description = trim((string) ($product['description'] ?? ''));
    $featureCopy = $description !== ''
        ? \Illuminate\Support\Str::limit($description, 72)
        : 'A perfect blend of style, quality, and everyday convenience.';
@endphp


<style>
    .sari-product-card {
        box-shadow:
            0 7px 18px rgba(43,34,24,.034),
            inset 0 1px 0 rgba(255,255,255,.98);
    }

    .sari-product-card .sari-product-media {
        border-bottom: 1px solid #eee8df;
    }

    @media (hover:hover) and (pointer:fine) {
        .sari-product-card:hover {
            box-shadow:
                0 10px 24px rgba(43,34,24,.05),
                inset 0 1px 0 rgba(255,255,255,.98);
        }
    }
</style>

<article
    style="font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;"
    @if ($interactive)
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
    @endif
    {{ $attributes->merge([
        'class' => ($featured ? 'sari-featured-product-card ' : '') . '
            sari-product-card group flex h-full min-w-0 flex-col overflow-hidden rounded-[13px]
            border border-[#e7e1da] bg-white
            shadow-[0_6px_16px_rgba(43,34,24,.028)]
            transition-[border-color,box-shadow] duration-200
            hover:border-[#ded2c1] hover:shadow-[0_10px_24px_rgba(43,34,24,.05)]
        ',
    ]) }}
>
    @if ($featured)
        {{-- FEATURED CARD --}}
        <div class="relative min-h-[340px] flex-1 overflow-hidden bg-[#181512]">
            @if ($image)
                <img
                    src="{{ asset($image) }}"
                    alt="{{ $productName }}"
                    loading="{{ $priority ? 'eager' : 'lazy' }}"
                    fetchpriority="{{ $priority ? 'high' : 'auto' }}"
                    decoding="{{ $priority ? 'sync' : 'async' }}"
                    draggable="false"
                    class="absolute inset-0 h-full w-full object-cover"
                >
            @else
                <div class="absolute inset-0 grid place-items-center bg-[#27231f] text-white/40">
                    <svg viewBox="0 0 24 24" class="h-9 w-9" fill="none" stroke="currentColor" stroke-width="1.4">
                        <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                        <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                    </svg>
                </div>
            @endif

            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(13,11,9,.82)_0%,rgba(13,11,9,.46)_48%,rgba(13,11,9,.05)_80%,rgba(13,11,9,0)_100%)]"></div>
            <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>

            <div class="absolute left-3 top-3 z-20">
                <span class="inline-flex h-7 items-center gap-1.5 rounded-full bg-[#cf8b0e] px-3 text-[7px] font-bold text-white shadow-sm">
                    <svg viewBox="0 0 24 24" class="h-2.5 w-2.5" fill="currentColor">
                        <path d="M12 3.2 14.2 8.2 19.7 8.8 15.5 12.4 16.7 17.8 12 15 7.3 17.8 8.5 12.4 4.3 8.8 9.8 8.2 12 3.2Z"></path>
                    </svg>
                    SARI Mall
                </span>
            </div>

            <div class="absolute right-3 top-3 z-20">
                @if ($stock <= 0)
                    <span class="inline-flex h-7 items-center rounded-full border border-[#efb6b6] bg-white/95 px-3 text-[7px] font-bold text-[#bd4848]">
                        Out of Stock
                    </span>
                @elseif ($discount > 0)
                    <span class="inline-flex h-7 items-center rounded-full border border-[#ead29b] bg-[#fff2cf] px-3 text-[7px] font-bold text-[#a76708]">
                        -{{ number_format($discount, 0) }}%
                    </span>
                @elseif ($isNew)
                    <span class="inline-flex h-7 items-center rounded-full bg-[#3f8b68] px-3 text-[7px] font-bold text-white">
                        New
                    </span>
                @endif
            </div>

            <div class="relative z-10 flex h-full min-h-[340px] max-w-[72%] flex-col justify-end p-5">
                <p class="text-[7px] font-bold uppercase tracking-[.16em] text-[#efc36b]">{{ $category }}</p>
                <h3 class="mt-1.5 text-[22px] font-bold leading-[1.06] tracking-[-.04em] text-white">
                    {{ $productName }}
                </h3>
                <p class="mt-2 max-w-[320px] text-[8px] leading-4 text-white/80">
                    {{ $featureCopy }}
                </p>
            </div>
        </div>

        <div class="border-t border-[#eee8e1] px-4 py-4">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-baseline gap-2">
                        <strong class="text-[18px] font-bold leading-none tracking-[-.04em] text-[#c67f08]">
                            ₱{{ number_format($finalPrice, 2) }}
                        </strong>

                        @if ($originalPrice > $finalPrice)
                            <span class="text-[8px] text-[#aaa198] line-through">
                                ₱{{ number_format($originalPrice, 2) }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @if ($showPromotions && $freeShipping)
                        <span class="inline-flex min-h-[28px] items-center gap-1.5 rounded-full border border-[#bfe0ca] bg-[#eff9f2] px-2.5 text-[7.8px] font-bold text-[#347451]">
                            <span class="grid h-4.5 w-4.5 place-items-center rounded-full bg-white text-[#347451] shadow-[0_1px_3px_rgba(52,116,81,.10)]">
                                <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M3 7h11v9H3z"></path>
                                    <path d="M14 10h4l3 3v3h-7z"></path>
                                    <circle cx="7" cy="18" r="1.25"></circle>
                                    <circle cx="18" cy="18" r="1.25"></circle>
                                </svg>
                            </span>
                            Free Shipping
                        </span>
                    @endif

                    @if ($showStock)
                        <span class="rounded-full bg-[#f7f4ef] px-2 py-1 text-[7.5px] font-semibold {{ $stockTone }}">
                            {{ $stock > 0 ? 'Stock: ' . number_format($stock) : 'Out of Stock' }}
                        </span>
                    @endif
                </div>
            </div>

            @if ($showQuickAdd)
                <div class="mt-3 flex w-full items-center gap-2">
                    @if ($stock > 0)
                        @if ($interactive)
                            <button
                                type="button"
                                data-quick-add="{{ $productId }}"
                                aria-label="Add {{ $productName }} to cart"
                                title="Add to Cart"
                                class="group/cart relative grid h-10 w-10 shrink-0 place-items-center rounded-[10px] border border-[#dda62f] bg-[#fffdf8] text-[#bc7808] transition duration-150 hover:-translate-y-0.5 hover:border-[#cf8b0e] hover:bg-[#fff5df] hover:shadow-[0_6px_14px_rgba(201,137,14,.11)] active:scale-95"
                            >
                                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h8.7a2 2 0 0 0 2-1.6L21 8H7"></path>
                                    <circle cx="9" cy="20" r="1.4"></circle>
                                    <circle cx="18" cy="20" r="1.4"></circle>
                                </svg>
                                <span class="pointer-events-none absolute bottom-[calc(100%+8px)] left-1/2 z-30 hidden -translate-x-1/2 whitespace-nowrap rounded-[7px] bg-[#27231f] px-2 py-1.5 text-[6px] font-semibold text-white group-hover/cart:block">
                                    Add to Cart
                                </span>
                            </button>

                            <a
                                href="{{ $productUrl }}?buy_now=1"
                                aria-label="Buy {{ $productName }} now"
                                class="inline-flex h-10 min-w-0 flex-1 items-center justify-center rounded-[10px] bg-[#d68f08] px-4 text-[9.5px] font-bold text-white shadow-[0_7px_16px_rgba(205,137,11,.14)] transition duration-150 hover:-translate-y-0.5 hover:bg-[#c58105] hover:shadow-[0_9px_18px_rgba(193,123,5,.19)] active:scale-[.98]"
                            >
                                Buy Now
                            </a>
                        @else
                            <a
                                href="{{ $productUrl }}"
                                title="Add to Cart"
                                aria-label="View {{ $productName }}"
                                class="grid h-10 w-10 shrink-0 place-items-center rounded-[10px] border border-[#dda62f] bg-[#fffdf8] text-[#bc7808]"
                            >
                                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h8.7a2 2 0 0 0 2-1.6L21 8H7"></path>
                                    <circle cx="9" cy="20" r="1.4"></circle>
                                    <circle cx="18" cy="20" r="1.4"></circle>
                                </svg>
                            </a>

                            <a
                                href="{{ $productUrl }}"
                                class="inline-flex h-10 min-w-0 flex-1 items-center justify-center rounded-[10px] bg-[#d68f08] px-4 text-[9.5px] font-bold text-white"
                            >
                                Buy Now
                            </a>
                        @endif
                    @else
                        <button
                            type="button"
                            disabled
                            aria-label="{{ $productName }} is out of stock"
                            class="grid h-10 w-10 shrink-0 cursor-not-allowed place-items-center rounded-[10px] border border-[#ddd9d3] bg-[#f4f2ef] text-[#aaa59d]"
                        >
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h8.7a2 2 0 0 0 2-1.6L21 8H7"></path>
                                <path d="m7 7 11 11"></path>
                            </svg>
                        </button>

                        <button
                            type="button"
                            disabled
                            class="inline-flex h-10 min-w-0 flex-1 cursor-not-allowed items-center justify-center rounded-[10px] bg-[#d3d0cb] px-4 text-[9px] font-bold text-white"
                        >
                            Out of Stock
                        </button>
                    @endif
                </div>
            @endif
        </div>
    @else
        {{-- COMPACT CARD --}}
        @if ($interactive)
            <button
                type="button"
                data-open-product="{{ $productId }}"
                class="block w-full text-left focus:outline-none"
            >
        @else
            <a href="{{ $productUrl }}" class="block w-full">
        @endif
            <div class="sari-product-media relative h-[178px] overflow-hidden bg-[#f7f3ec] sm:h-[184px]">
                @if ($image)
                    <img
                        src="{{ asset($image) }}"
                        alt="{{ $productName }}"
                        loading="{{ $priority ? 'eager' : 'lazy' }}"
                        fetchpriority="{{ $priority ? 'high' : 'auto' }}"
                        decoding="{{ $priority ? 'sync' : 'async' }}"
                        draggable="false"
                        class="h-full w-full object-cover object-center"
                    >
                @else
                    <div class="grid h-full w-full place-items-center bg-[#f6f2eb] text-[#aaa197]">
                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                            <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                        </svg>
                    </div>
                @endif

                <span class="absolute left-2.5 top-2.5 inline-flex h-7 items-center gap-1 rounded-full bg-[#cf8b0e] px-2.5 text-[6.5px] font-bold text-white shadow-sm">
                    <svg viewBox="0 0 24 24" class="h-2.5 w-2.5" fill="currentColor">
                        <path d="M12 3.2 14.2 8.2 19.7 8.8 15.5 12.4 16.7 17.8 12 15 7.3 17.8 8.5 12.4 4.3 8.8 9.8 8.2 12 3.2Z"></path>
                    </svg>
                    SARI Mall
                </span>

                <span class="absolute right-2.5 top-2.5">
                    @if ($stock <= 0)
                        <span class="inline-flex h-7 items-center rounded-full border border-[#efb6b6] bg-white/95 px-2.5 text-[6.5px] font-bold text-[#c04747]">
                            Out of Stock
                        </span>
                    @elseif ($discount > 0)
                        <span class="inline-flex h-7 items-center rounded-full border border-[#ecd69f] bg-[#fff2d2] px-2.5 text-[6.5px] font-bold text-[#ab6908]">
                            -{{ number_format($discount, 0) }}%
                        </span>
                    @elseif ($isNew)
                        <span class="inline-flex h-7 items-center rounded-full bg-[#3f8b68] px-2.5 text-[6.5px] font-bold text-white">
                            New
                        </span>
                    @endif
                </span>
            </div>
        @if ($interactive)
            </button>
        @else
            </a>
        @endif

        <div class="flex flex-1 flex-col px-4 pb-4 pt-3.5">
            <p class="text-[8px] font-bold uppercase tracking-[.09em] text-[#8f857a]">
                {{ $category }}
            </p>

            @if ($interactive)
                <button
                    type="button"
                    data-open-product="{{ $productId }}"
                    class="mt-1 block w-full text-left focus:outline-none"
                >
                    <span class="block min-h-[30px] overflow-hidden text-[12.5px] font-bold leading-[17px] text-[#29231e] [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:2]">
                        {{ $productName }}
                    </span>
                </button>
            @else
                <a
                    href="{{ $productUrl }}"
                    class="mt-1 block min-h-[30px] overflow-hidden text-[12.5px] font-bold leading-[17px] text-[#29231e] [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:2]"
                >
                    {{ $productName }}
                </a>
            @endif

            <div class="mt-2 flex flex-wrap items-baseline gap-2">
                <strong class="text-[18px] font-bold leading-none tracking-[-.035em] text-[#c67f08]">
                    ₱{{ number_format($finalPrice, 2) }}
                </strong>

                @if ($originalPrice > $finalPrice)
                    <span class="text-[8px] text-[#aaa198] line-through">
                        ₱{{ number_format($originalPrice, 2) }}
                    </span>
                @endif
            </div>

            <div class="mt-auto pt-3.5">
                <div class="flex min-w-0 flex-wrap items-center justify-between gap-2.5 border-t border-[#eee8df] pt-3">
                    <div class="flex min-w-0 flex-wrap items-center gap-x-2 gap-y-1.5">
                        @if ($showPromotions && $freeShipping)
                            <span class="inline-flex min-h-[27px] items-center gap-1.5 rounded-full border border-[#bfe0ca] bg-[#eff9f2] px-2.5 text-[7.5px] font-bold text-[#347451]">
                                <span class="grid h-4.5 w-4.5 place-items-center rounded-full bg-white text-[#347451] shadow-[0_1px_3px_rgba(52,116,81,.10)]">
                                    <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <path d="M3 7h11v9H3z"></path>
                                        <path d="M14 10h4l3 3v3h-7z"></path>
                                        <circle cx="7" cy="18" r="1.25"></circle>
                                        <circle cx="18" cy="18" r="1.25"></circle>
                                    </svg>
                                </span>
                                Free Shipping
                            </span>
                        @endif

                        @if ($showStock)
                            <span class="text-[7px] font-semibold {{ $stockTone }}">
                                {{ $stock > 0 ? 'Stock: ' . number_format($stock) : 'Out of Stock' }}
                            </span>
                        @endif
                    </div>

                    @if ($rating > 0 || $sold > 0)
                        <span class="text-[7.5px] font-medium text-[#817970]">
                            @if ($rating > 0)
                                <span class="text-[#d7920b]">★</span>
                                {{ number_format($rating, 1) }}
                            @endif

                            @if ($rating > 0 && $sold > 0)
                                <span class="mx-1 text-[#d4cec5]">|</span>
                            @endif

                            @if ($sold > 0)
                                {{ number_format($sold) }} sold
                            @endif
                        </span>
                    @endif
                </div>

                @if ($showQuickAdd)
                    <div class="mt-3 flex w-full items-center gap-2">
                        @if ($stock > 0)
                            @if ($interactive)
                                <button
                                    type="button"
                                    data-quick-add="{{ $productId }}"
                                    aria-label="Add {{ $productName }} to cart"
                                    title="Add to Cart"
                                    class="group/cart relative grid h-10 w-10 shrink-0 place-items-center rounded-[10px] border border-[#dda62f] bg-[#fffdf8] text-[#bc7808] transition duration-150 hover:-translate-y-0.5 hover:border-[#cf8b0e] hover:bg-[#fff5df] hover:shadow-[0_6px_14px_rgba(201,137,14,.11)] active:scale-95"
                                >
                                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h8.7a2 2 0 0 0 2-1.6L21 8H7"></path>
                                        <circle cx="9" cy="20" r="1.4"></circle>
                                        <circle cx="18" cy="20" r="1.4"></circle>
                                    </svg>
                                    <span class="pointer-events-none absolute bottom-[calc(100%+8px)] left-1/2 z-30 hidden -translate-x-1/2 whitespace-nowrap rounded-[7px] bg-[#27231f] px-2 py-1.5 text-[6px] font-semibold text-white group-hover/cart:block">
                                        Add to Cart
                                    </span>
                                </button>

                                <a
                                    href="{{ $productUrl }}?buy_now=1"
                                    aria-label="Buy {{ $productName }} now"
                                    class="inline-flex h-10 min-w-0 flex-1 items-center justify-center rounded-[10px] bg-[#d68f08] px-4 text-[9px] font-bold text-white shadow-[0_7px_16px_rgba(205,137,11,.14)] transition duration-150 hover:-translate-y-0.5 hover:bg-[#c58105] hover:shadow-[0_9px_18px_rgba(193,123,5,.19)] active:scale-[.98]"
                                >
                                    Buy Now
                                </a>
                            @else
                                <a
                                    href="{{ $productUrl }}"
                                    title="Add to Cart"
                                    aria-label="View {{ $productName }}"
                                    class="grid h-10 w-10 shrink-0 place-items-center rounded-[10px] border border-[#dda62f] bg-[#fffdf8] text-[#bc7808]"
                                >
                                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h8.7a2 2 0 0 0 2-1.6L21 8H7"></path>
                                        <circle cx="9" cy="20" r="1.4"></circle>
                                        <circle cx="18" cy="20" r="1.4"></circle>
                                    </svg>
                                </a>

                                <a
                                    href="{{ $productUrl }}?buy_now=1"
                                    class="inline-flex h-10 min-w-0 flex-1 items-center justify-center rounded-[10px] bg-[#d68f08] px-4 text-[9px] font-bold text-white"
                                >
                                    Buy Now
                                </a>
                            @endif
                        @else
                            <button
                                type="button"
                                disabled
                                aria-label="{{ $productName }} is out of stock"
                                class="grid h-10 w-10 shrink-0 cursor-not-allowed place-items-center rounded-[10px] border border-[#ddd9d3] bg-[#f4f2ef] text-[#aaa59d]"
                            >
                                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h8.7a2 2 0 0 0 2-1.6L21 8H7"></path>
                                    <path d="m7 7 11 11"></path>
                                </svg>
                            </button>

                            <button
                                type="button"
                                disabled
                                class="inline-flex h-10 min-w-0 flex-1 cursor-not-allowed items-center justify-center rounded-[10px] bg-[#d3d0cb] px-4 text-[9px] font-bold text-white"
                            >
                                Out of Stock
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
</article>
