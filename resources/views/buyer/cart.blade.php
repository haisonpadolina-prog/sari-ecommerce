@extends('layouts.buyer')

@section('title', 'Shopping Cart — SARI')
@section('page-title', 'Shopping Cart')

@section('content')

@include('components.buyer.header')

@php
    $feePerSeller = $cart->deliveryFeePerSeller();
    $itemGroups = $items->groupBy(fn ($item) => (int) ($item->product?->seller_account_id ?? 0));
    $availabilityByItem = $items->mapWithKeys(
        fn ($item) => [(int) $item->id => $cart->availability($item)]
    );
    $selectableCount = $availabilityByItem->filter(fn ($availability) => $availability['selectable'])->count();
@endphp

<div
    data-sari-cart-page
    class="mx-auto w-full max-w-[1500px] px-4 py-5 sm:px-6 lg:px-8"
    style="font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;"
>
    <style>
        .sari-cart-select { accent-color:#c98d19; }

        .sari-cart-row {
            transition:
                border-color .16s ease,
                background-color .16s ease,
                box-shadow .16s ease,
                transform .16s ease;
        }

        .sari-cart-row[data-selected="true"] {
            border-color:#dda62f;
            background:#fffdfa;
            box-shadow:
                0 8px 20px rgba(188,125,9,.055),
                inset 3px 0 0 #d29115;
        }

        .sari-cart-check-shell {
            transition:
                border-color .16s ease,
                background-color .16s ease,
                box-shadow .16s ease;
        }

        .sari-cart-row[data-selected="true"] .sari-cart-check-shell {
            border-color:#d29115;
            background:#d29115;
            box-shadow:0 5px 12px rgba(190,127,12,.15);
        }

        .sari-cart-check-mark {
            opacity:0;
            transform:scale(.72);
            transition:opacity .14s ease, transform .14s ease;
        }

        .sari-cart-row[data-selected="true"] .sari-cart-check-mark {
            opacity:1;
            transform:scale(1);
        }

        .sari-cart-img {
            transition:transform .2s ease;
        }

        .sari-summary-disabled {
            cursor:not-allowed !important;
            background:#d7d3cc !important;
            box-shadow:none !important;
        }

        .sari-cart-summary {
            box-shadow:
                0 14px 34px rgba(43,34,24,.055),
                0 3px 10px rgba(43,34,24,.022);
        }

        @media (hover:hover) and (pointer:fine) {
            .sari-cart-row:hover {
                border-color:#e1d5c2;
                box-shadow:0 7px 18px rgba(43,34,24,.035);
            }

            .sari-cart-row:hover .sari-cart-img {
                transform:scale(1.025);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .sari-cart-row,
            .sari-cart-img,
            .sari-cart-check-shell,
            .sari-cart-check-mark {
                transition:none !important;
                transform:none !important;
            }
        }
    
        /* ============================================================
           SARI CART — FUNCTIONAL + REFINED NEUMORPHISM
           Subtle only: clean white surfaces, soft depth, no gradients.
        ============================================================ */
        .sari-cart-toolbar,
        .sari-seller-shell,
        .sari-cart-count-pill,
        .sari-btn-secondary,
        .sari-cart-summary {
            box-shadow:
                0 8px 20px rgba(58, 45, 30, .045),
                0 2px 6px rgba(58, 45, 30, .022),
                inset 0 1px 0 rgba(255,255,255,.98),
                inset 0 -1px 0 rgba(228,219,207,.42);
        }

        .sari-seller-shell {
            border-color:#e7ded2 !important;
        }

        .sari-cart-row {
            box-shadow:
                0 5px 14px rgba(51,40,28,.026),
                inset 0 1px 0 rgba(255,255,255,.98);
        }

        .sari-cart-row[data-selected="true"] {
            box-shadow:
                0 10px 24px rgba(188,125,9,.075),
                0 2px 7px rgba(188,125,9,.035),
                inset 3px 0 0 #d29115,
                inset 0 1px 0 rgba(255,255,255,.98);
        }

        .sari-cart-count-pill {
            background:#fffaf0 !important;
        }

        .sari-btn-secondary,
        .sari-btn-update,
        .sari-btn-remove,
        .sari-btn-checkout {
            transition:
                transform .15s ease,
                border-color .15s ease,
                background-color .15s ease,
                color .15s ease,
                box-shadow .15s ease;
        }

        .sari-btn-secondary {
            border-color:#ded4c7 !important;
            background:#fff !important;
        }

        .sari-btn-update {
            border:1px solid #d9bd80 !important;
            background:#fffaf0 !important;
            color:#92610d !important;
            box-shadow:
                0 5px 12px rgba(170,112,13,.055),
                inset 0 1px 0 rgba(255,255,255,.98);
        }

        .sari-btn-remove {
            border:1px solid #efd9d7;
            background:#fff8f7 !important;
            color:#a85650 !important;
            box-shadow:
                0 4px 10px rgba(168,86,80,.035),
                inset 0 1px 0 rgba(255,255,255,.98);
        }

        .sari-btn-checkout {
            background:#c98d19 !important;
            box-shadow:
                0 9px 18px rgba(187,125,13,.17),
                inset 0 1px 0 rgba(255,255,255,.16);
        }

        .sari-qty-control {
            box-shadow:
                inset 0 2px 5px rgba(46,35,24,.045),
                0 3px 9px rgba(46,35,24,.025);
        }

        .sari-update-state {
            min-width:56px;
        }

        .sari-cart-feedback {
            position:fixed;
            right:22px;
            bottom:22px;
            z-index:220;
            max-width:320px;
            border:1px solid #d9e6dd;
            border-radius:12px;
            background:#f5fbf7;
            padding:10px 13px;
            color:#4f7d61;
            font-size:8px;
            font-weight:600;
            box-shadow:0 12px 30px rgba(39,32,24,.11);
        }

        .sari-cart-feedback.is-error {
            border-color:#edd1d1;
            background:#fff6f6;
            color:#a65353;
        }

        @media (hover:hover) and (pointer:fine) {
            .sari-btn-secondary:hover,
            .sari-btn-update:hover,
            .sari-btn-remove:hover,
            .sari-btn-checkout:not(:disabled):hover {
                transform:translateY(-1px);
            }

            .sari-btn-secondary:hover {
                border-color:#d5bd8c !important;
                background:#fffaf1 !important;
                color:#936313 !important;
                box-shadow:0 8px 16px rgba(126,88,22,.075);
            }

            .sari-btn-update:hover {
                border-color:#cda65a !important;
                background:#fff5df !important;
                box-shadow:0 8px 16px rgba(170,112,13,.09);
            }

            .sari-btn-remove:hover {
                border-color:#e8c4c1 !important;
                background:#fff0ef !important;
                box-shadow:0 7px 14px rgba(168,86,80,.07);
            }

            .sari-btn-checkout:not(:disabled):hover {
                background:#b87d10 !important;
                box-shadow:0 12px 22px rgba(175,116,11,.21);
            }
        }

        .sari-btn-secondary:active,
        .sari-btn-update:active,
        .sari-btn-remove:active,
        .sari-btn-checkout:not(:disabled):active {
            transform:scale(.975);
        }

        .sari-btn-update:disabled {
            cursor:wait;
            opacity:.72;
        }

        @media (prefers-reduced-motion: reduce) {
            .sari-btn-secondary,
            .sari-btn-update,
            .sari-btn-remove,
            .sari-btn-checkout {
                transition:none !important;
                transform:none !important;
            }
        }


        /* ============================================================
           SARI CART — NEUMORPHISM V4
           Stronger depth, still clean and formal.
        ============================================================ */

        [data-sari-cart-page] {
            background:#fbfaf7;
        }

        .sari-cart-toolbar,
        .sari-seller-shell,
        .sari-cart-summary,
        .sari-cart-count-pill,
        .sari-btn-secondary {
            box-shadow:
                10px 10px 24px rgba(75, 59, 39, .055),
                -8px -8px 20px rgba(255, 255, 255, .92),
                inset 0 1px 0 rgba(255,255,255,.98),
                inset 0 -1px 0 rgba(225,216,204,.48);
        }

        .sari-cart-toolbar {
            background:#fefdfb !important;
            border-color:#e6ddd1 !important;
        }

        .sari-seller-shell {
            background:#fff !important;
            border-color:#e5dbce !important;
            box-shadow:
                12px 12px 28px rgba(68, 52, 33, .055),
                -8px -8px 22px rgba(255,255,255,.96),
                inset 0 1px 0 rgba(255,255,255,.98),
                inset 0 -1px 0 rgba(226,217,205,.42);
        }

        .sari-cart-summary {
            background:#fff !important;
            box-shadow:
                14px 14px 34px rgba(68, 52, 33, .065),
                -10px -10px 26px rgba(255,255,255,.96),
                inset 0 1px 0 rgba(255,255,255,.98),
                inset 0 -1px 0 rgba(226,217,205,.44);
        }

        .sari-cart-row {
            border-color:#e9e1d7 !important;
            background:#fff !important;
            box-shadow:
                7px 7px 18px rgba(69, 54, 35, .045),
                -5px -5px 14px rgba(255,255,255,.96),
                inset 0 1px 0 rgba(255,255,255,.98),
                inset 0 -1px 0 rgba(228,220,209,.34);
        }

        .sari-cart-row[data-selected="true"] {
            border-color:#d9aa4c !important;
            background:#fffdf8 !important;
            box-shadow:
                10px 10px 24px rgba(182, 120, 14, .09),
                -7px -7px 18px rgba(255,255,255,.96),
                inset 3px 0 0 #d29115,
                inset 0 1px 0 rgba(255,255,255,.98);
        }

        .sari-cart-check-shell {
            box-shadow:
                inset 2px 2px 5px rgba(86,68,46,.08),
                inset -2px -2px 5px rgba(255,255,255,.95);
        }

        .sari-cart-row[data-selected="true"] .sari-cart-check-shell {
            box-shadow:
                0 5px 12px rgba(190,127,12,.18),
                inset 0 1px 0 rgba(255,255,255,.18);
        }

        .sari-cart-count-pill {
            border-color:#e6d6b8 !important;
            background:#fff9ee !important;
            box-shadow:
                6px 6px 14px rgba(129,90,22,.055),
                -5px -5px 12px rgba(255,255,255,.95),
                inset 0 1px 0 rgba(255,255,255,.98);
        }

        .sari-qty-control {
            border-color:#ddd3c6 !important;
            background:#fdfcf9 !important;
            box-shadow:
                inset 4px 4px 8px rgba(78,61,40,.06),
                inset -4px -4px 8px rgba(255,255,255,.95),
                0 3px 8px rgba(59,46,31,.025);
        }

        .sari-btn-secondary {
            box-shadow:
                6px 6px 14px rgba(74,58,39,.05),
                -5px -5px 12px rgba(255,255,255,.96),
                inset 0 1px 0 rgba(255,255,255,.98);
        }

        .sari-btn-update {
            border-color:#d6b36d !important;
            background:#fffaf0 !important;
            box-shadow:
                6px 6px 14px rgba(149,99,13,.07),
                -5px -5px 12px rgba(255,255,255,.96),
                inset 0 1px 0 rgba(255,255,255,.98);
        }

        .sari-btn-remove {
            border-color:#ebd3d0 !important;
            background:#fff8f7 !important;
            box-shadow:
                5px 5px 12px rgba(155,77,71,.05),
                -4px -4px 10px rgba(255,255,255,.95),
                inset 0 1px 0 rgba(255,255,255,.98);
        }

        .sari-btn-checkout {
            box-shadow:
                0 10px 22px rgba(183,119,10,.19),
                inset 0 1px 0 rgba(255,255,255,.2),
                inset 0 -2px 0 rgba(122,76,0,.08);
        }

        .sari-summary-disabled {
            box-shadow:
                inset 3px 3px 7px rgba(105,98,88,.08),
                inset -3px -3px 7px rgba(255,255,255,.55) !important;
        }

        /* Main product image frame gets gentle raised depth. */
        [data-selectable-card] > div > a {
            box-shadow:
                5px 5px 12px rgba(74,58,39,.045),
                -4px -4px 10px rgba(255,255,255,.95),
                inset 0 1px 0 rgba(255,255,255,.98);
        }

        /* Summary info panel looks softly pressed. */
        .sari-cart-summary .bg-\[\#f4f8fd\] {
            box-shadow:
                inset 3px 3px 7px rgba(78,95,114,.045),
                inset -3px -3px 7px rgba(255,255,255,.75);
        }

        @media (hover:hover) and (pointer:fine) {
            .sari-cart-row:hover {
                transform:translateY(-1px);
                border-color:#dfd2bf !important;
                box-shadow:
                    10px 10px 22px rgba(70,54,35,.055),
                    -7px -7px 16px rgba(255,255,255,.96),
                    inset 0 1px 0 rgba(255,255,255,.98);
            }

            .sari-btn-update:hover {
                box-shadow:
                    8px 8px 16px rgba(149,99,13,.09),
                    -6px -6px 13px rgba(255,255,255,.96),
                    inset 0 1px 0 rgba(255,255,255,.98);
            }

            .sari-btn-remove:hover {
                box-shadow:
                    7px 7px 14px rgba(155,77,71,.07),
                    -5px -5px 11px rgba(255,255,255,.95),
                    inset 0 1px 0 rgba(255,255,255,.98);
            }

            .sari-btn-secondary:hover {
                box-shadow:
                    8px 8px 16px rgba(99,73,35,.07),
                    -6px -6px 13px rgba(255,255,255,.96),
                    inset 0 1px 0 rgba(255,255,255,.98);
            }

            .sari-btn-checkout:not(:disabled):hover {
                box-shadow:
                    0 13px 26px rgba(173,112,8,.23),
                    inset 0 1px 0 rgba(255,255,255,.22);
            }
        }


        /* ============================================================
           SARI CART — V5 CLEAN BUTTONS + STRONGER CONTAINER DEPTH
        ============================================================ */

        /* More depth on the bordered sections, not on the buttons. */
        .sari-cart-toolbar {
            box-shadow:
                0 10px 24px rgba(61,47,31,.065),
                0 3px 8px rgba(61,47,31,.028),
                inset 0 1px 0 rgba(255,255,255,.98) !important;
        }

        .sari-seller-shell {
            box-shadow:
                0 14px 30px rgba(61,47,31,.068),
                0 4px 10px rgba(61,47,31,.028),
                inset 0 1px 0 rgba(255,255,255,.98) !important;
        }

        .sari-cart-row {
            box-shadow:
                0 8px 18px rgba(61,47,31,.052),
                0 2px 7px rgba(61,47,31,.022),
                inset 0 1px 0 rgba(255,255,255,.98) !important;
        }

        .sari-cart-row[data-selected="true"] {
            box-shadow:
                0 11px 24px rgba(178,116,13,.10),
                0 3px 8px rgba(178,116,13,.035),
                inset 3px 0 0 #d29115,
                inset 0 1px 0 rgba(255,255,255,.98) !important;
        }

        .sari-cart-summary {
            box-shadow:
                0 16px 34px rgba(61,47,31,.075),
                0 4px 11px rgba(61,47,31,.028),
                inset 0 1px 0 rgba(255,255,255,.98) !important;
        }

        [data-selectable-card] > div > a {
            box-shadow:
                0 6px 14px rgba(61,47,31,.045),
                inset 0 1px 0 rgba(255,255,255,.98) !important;
        }

        /* Flat, simple buttons — no shadow. */
        .sari-btn-secondary,
        .sari-btn-update,
        .sari-btn-remove,
        .sari-btn-checkout {
            box-shadow:none !important;
            transform:none !important;
        }

        .sari-btn-secondary {
            border:1px solid #d9bd80 !important;
            background:#fff !important;
            color:#966719 !important;
        }

        .sari-btn-update {
            border:1px solid #d5ae5b !important;
            background:#fffaf0 !important;
            color:#8f5e09 !important;
        }

        .sari-btn-remove {
            border:1px solid #e3d4b5 !important;
            background:#fffdf8 !important;
            color:#8d641d !important;
        }

        .sari-btn-checkout {
            border:1px solid #c98d19 !important;
            background:#c98d19 !important;
            color:#fff !important;
            box-shadow:none !important;
        }

        .sari-summary-disabled {
            border-color:#d9d4cc !important;
            background:#d9d4cc !important;
            color:#fff !important;
            box-shadow:none !important;
        }

        /* Quantity should stay crisp and simple. */
        .sari-qty-control {
            box-shadow:none !important;
            background:#fff !important;
        }

        @media (hover:hover) and (pointer:fine) {
            .sari-btn-secondary:hover {
                border-color:#cfa34d !important;
                background:#fffaf0 !important;
                color:#875909 !important;
                box-shadow:none !important;
                transform:none !important;
            }

            .sari-btn-update:hover {
                border-color:#c9952d !important;
                background:#fff5df !important;
                color:#845506 !important;
                box-shadow:none !important;
                transform:none !important;
            }

            .sari-btn-remove:hover {
                border-color:#d4bd8c !important;
                background:#fff8e9 !important;
                color:#80550d !important;
                box-shadow:none !important;
                transform:none !important;
            }

            .sari-btn-checkout:not(:disabled):hover {
                border-color:#b77d10 !important;
                background:#b77d10 !important;
                box-shadow:none !important;
                transform:none !important;
            }

            .sari-cart-row:hover {
                box-shadow:
                    0 10px 22px rgba(61,47,31,.065),
                    0 3px 8px rgba(61,47,31,.025),
                    inset 0 1px 0 rgba(255,255,255,.98) !important;
            }
        }

        .sari-btn-secondary:active,
        .sari-btn-update:active,
        .sari-btn-remove:active,
        .sari-btn-checkout:not(:disabled):active {
            transform:none !important;
        }

</style>

    @if (session('success'))
        <div class="mb-4 rounded-[14px] border border-[#cfe4d7] bg-[#f1f8f4] px-4 py-3 text-[9px] font-medium text-[#4F7D63]">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-[14px] border border-[#efcece] bg-[#fff5f5] px-4 py-3 text-[9px] font-medium text-[#a65353]">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <section class="border-b border-[#eee8df] pb-4">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-[7px] font-bold uppercase tracking-[.16em] text-[#b57912]">SARI Marketplace</p>
                <h1 class="mt-1 text-[27px] font-bold tracking-[-.045em] text-[#241f1a] sm:text-[31px]">
                    Shopping <span class="text-[#c98d19]">Cart</span>
                </h1>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('buyer.products') }}"
                    class="sari-cart-continue-text"
                >
                    Continue Shopping <span class="sari-cart-continue-arrow" aria-hidden="true">&gt;</span>
                </a>
            </div>
        </div>
    </section>

    @if ($items->isEmpty())
        <section class="sari-cart-empty-state mt-5 rounded-[20px] border border-dashed border-[#ded5c9] bg-white px-6 py-16 text-center">
            <span class="sari-cart-empty-icon mx-auto grid h-14 w-14 place-items-center">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path d="M3 4h2l2 11h11l2-8H6"></path>
                    <circle cx="9" cy="19" r="1.4"></circle>
                    <circle cx="18" cy="19" r="1.4"></circle>
                </svg>
            </span>

            <h2 class="sari-cart-empty-title mt-4">Your cart is empty</h2>
            <p class="sari-cart-empty-copy mt-1.5">
                Start exploring products from approved SARI sellers.
            </p>

            <a
                href="{{ route('buyer.products') }}"
                class="sari-cart-empty-button mt-6 inline-flex items-center justify-center"
            >
                Browse Products
            </a>
        </section>
    @else
        <div class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1fr)_365px]">

            {{-- =====================================================
                 LEFT — CART ITEMS
            ====================================================== --}}
            <div class="min-w-0 space-y-4">

                {{-- SELECT ALL --}}
                <section class="sari-cart-toolbar flex flex-col gap-3 rounded-[16px] border border-[#e8e0d6] bg-white px-4 py-3.5 sm:flex-row sm:items-center sm:justify-between">
                    <label class="inline-flex cursor-pointer items-center gap-3">
                        <span class="sari-select-all-check-wrap">
                            <input
                                id="cartSelectAll"
                                type="checkbox"
                                class="sari-cart-select sr-only"
                            >
                            <span class="sari-select-all-check-shell" aria-hidden="true">
                                <svg viewBox="0 0 24 24" class="sari-select-all-check-mark" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="m6 12 4 4 8-8"></path>
                                </svg>
                            </span>
                        </span>
                        <span>
                            <span class="block text-[9px] font-semibold text-[#403930]">Select all products</span>
                            <span class="mt-0.5 block text-[7px] text-[#9a9187]">
                                {{ $items->count() }} item{{ $items->count() === 1 ? '' : 's' }} in your cart
                            </span>
                        </span>
                    </label>

                    <div class="flex items-center gap-2.5">
                        <span class="text-[7px] text-[#a29a90]">Click a product card to select</span>
                    </div>
                </section>

                @foreach ($itemGroups as $sellerId => $sellerItems)
                    @php
                        $seller = $sellerItems->first()?->product?->seller;
                        $storeName = $seller?->store_name ?: 'SARI Seller Store';

                        $storeLogoUrl = filled($seller?->store_logo_path)
                            ? \Illuminate\Support\Facades\Storage::disk('public')->url($seller->store_logo_path)
                            : null;

                        $storeInitials = collect(preg_split('/\s+/', trim($storeName)))
                            ->filter()
                            ->take(2)
                            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                            ->implode('');

                        $storeInitials = $storeInitials !== '' ? $storeInitials : 'S';
                        $storeUrl = (int) $sellerId > 0
                            ? route('buyer.shop', ['shop' => 'seller-' . (int) $sellerId])
                            : null;

                        $sellerShippingOffer = $cart->shippingOfferForGroup($sellerItems);
                        $sellerShippingActive = (bool) ($sellerShippingOffer['active'] ?? false);
                        $sellerShippingMinimum = (float) ($sellerShippingOffer['minimum_order_amount'] ?? 0);
                        $sellerShippingMaximum = (float) ($sellerShippingOffer['maximum_shipping_discount'] ?? 0);
                    @endphp

                    <section class="sari-seller-shell overflow-hidden rounded-[18px] border border-[#e8e0d6] bg-white">
                        {{-- SELLER HEADER --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#eee8df] bg-[#fcfbf8] px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                @if ($storeUrl)
                                    <a
                                        href="{{ $storeUrl }}"
                                        class="sari-store-profile"
                                        aria-label="Visit {{ $storeName }}"
                                        title="{{ $storeName }}"
                                    >
                                        @if ($storeLogoUrl)
                                            <img
                                                src="{{ $storeLogoUrl }}"
                                                alt="{{ $storeName }} store profile"
                                                class="sari-store-profile-image"
                                                loading="lazy"
                                                decoding="async"
                                                onerror="this.hidden=true; this.nextElementSibling.hidden=false;"
                                            >
                                            <span class="sari-store-profile-fallback" hidden aria-hidden="true">{{ $storeInitials }}</span>
                                        @else
                                            <span class="sari-store-profile-fallback" aria-hidden="true">{{ $storeInitials }}</span>
                                        @endif
                                    </a>
                                @else
                                    <span class="sari-store-profile" title="{{ $storeName }}">
                                        @if ($storeLogoUrl)
                                            <img
                                                src="{{ $storeLogoUrl }}"
                                                alt="{{ $storeName }} store profile"
                                                class="sari-store-profile-image"
                                                loading="lazy"
                                                decoding="async"
                                                onerror="this.hidden=true; this.nextElementSibling.hidden=false;"
                                            >
                                            <span class="sari-store-profile-fallback" hidden aria-hidden="true">{{ $storeInitials }}</span>
                                        @else
                                            <span class="sari-store-profile-fallback" aria-hidden="true">{{ $storeInitials }}</span>
                                        @endif
                                    </span>
                                @endif

                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($storeUrl)
                                        <a href="{{ $storeUrl }}" class="sari-store-name-link">
                                            {{ $storeName }}
                                        </a>
                                    @else
                                        <h2 class="sari-store-name-link">{{ $storeName }}</h2>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="sari-seller-product-count font-medium text-[#9b9287]">
                                    {{ $sellerItems->count() }} product{{ $sellerItems->count() === 1 ? '' : 's' }}
                                </span>
                                @if ((int) $sellerId > 0)
                                    <a
                                        href="{{ route('buyer.shop', ['shop' => 'seller-' . (int) $sellerId]) }}"
                                        class="sari-view-seller-link"
                                    >
                                        <span>View Seller</span>
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M7 17 17 7"></path>
                                            <path d="M9 7h8v8"></path>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if ($sellerShippingActive)
                            <div
                                class="sari-shipping-footer"
                                data-shipping-offer-seller="{{ (int) $sellerId }}"
                                data-shipping-offer-minimum="{{ number_format($sellerShippingMinimum, 2, '.', '') }}"
                            >
                                <div class="sari-shipping-footer-copy">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M3 7h11v9H3z"></path>
                                        <path d="M14 10h4l3 3v3h-7z"></path>
                                        <circle cx="7" cy="18" r="1.4"></circle>
                                        <circle cx="18" cy="18" r="1.4"></circle>
                                    </svg>
                                    <div>
                                        <p class="sari-shipping-footer-title">Free Shipping Promotion</p>
                                        <p class="sari-shipping-footer-description">
                                            Spend ₱{{ number_format($sellerShippingMinimum, 2) }} from this seller · up to ₱{{ number_format($sellerShippingMaximum, 2) }} shipping off
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="sari-seller-items space-y-2.5 bg-[#faf9f7] p-2.5">
                            @foreach ($sellerItems as $item)
                                @php
                                    $unit = $cart->unitPrice($item);
                                    $line = $cart->lineTotal($item);
                                    $availability = $availabilityByItem->get((int) $item->id, []);
                                    $selectable = (bool) ($availability['selectable'] ?? false);
                                    $quantityEditable = (bool) ($availability['quantity_editable'] ?? false);
                                    $productVisible = (bool) ($availability['product_visible'] ?? false);
                                    $availabilityReason = $availability['reason'] ?? null;
                                    $maxStock = (int) ($availability['max_stock'] ?? 0);
                                @endphp

                                <article
                                    class="sari-cart-row rounded-[15px] border border-[#ece5dc] bg-white p-3.5 sm:p-4"
                                    data-cart-item
                                    data-selectable-card
                                    data-selected="false"
                                    data-line-total="{{ number_format($line, 2, '.', '') }}"
                                    data-seller-id="{{ (int) ($item->product?->seller_account_id ?? 0) }}"
                                    data-shipping-promo-active="{{ $sellerShippingActive ? '1' : '0' }}"
                                    data-shipping-minimum="{{ number_format($sellerShippingMinimum, 2, '.', '') }}"
                                    data-shipping-max-discount="{{ number_format($sellerShippingMaximum, 2, '.', '') }}"
                                    data-cart-selectable="{{ $selectable ? '1' : '0' }}"
                                    @if (!$selectable) aria-disabled="true" @endif
                                >
                                    <div class="grid grid-cols-[26px_106px_minmax(0,1fr)] gap-3 sm:grid-cols-[28px_120px_minmax(0,1fr)]">

                                        {{-- CHECKBOX --}}
                                        <div class="flex justify-center pt-1">
                                            <label class="{{ $selectable ? 'cursor-pointer' : 'cursor-not-allowed opacity-45' }}" title="{{ $selectable ? 'Select for checkout' : $availabilityReason }}">
                                                <input
                                                    form="selectedCheckoutForm"
                                                    name="items[]"
                                                    value="{{ $item->id }}"
                                                    type="checkbox"
                                                    class="sari-cart-select sr-only"
                                                    data-cart-checkbox
                                                    @disabled(!$selectable)
                                                >
                                                <span class="sari-cart-check-shell grid h-5 w-5 place-items-center rounded-[6px] border border-[#d8d0c5] bg-white text-white">
                                                    <svg viewBox="0 0 24 24" class="sari-cart-check-mark h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.4">
                                                        <path d="m6 12 4 4 8-8"></path>
                                                    </svg>
                                                </span>
                                            </label>
                                        </div>

                                        {{-- IMAGE + STOCK --}}
                                        <div class="sari-cart-media-column">
                                            @if ($productVisible)
                                                <a
                                                    href="{{ route('buyer.product.details', $item->product->id) }}"
                                                    class="sari-cart-media-link"
                                                >
                                                    <img
                                                        src="{{ route('buyer.product.image', $item->product->id) }}"
                                                        alt="{{ $item->product->name }}"
                                                        class="sari-cart-img h-full w-full object-cover"
                                                        onerror="this.style.display='none'"
                                                    >
                                                </a>
                                            @else
                                                <div class="sari-cart-media-link sari-cart-media-unavailable">
                                                    Unavailable
                                                </div>
                                            @endif

                                            <p class="sari-cart-stock-text {{ $maxStock > 0 ? '' : 'is-out' }}">
                                                <span class="sari-cart-stock-dot" aria-hidden="true"></span>
                                                {{ max(0, $maxStock) }} in stock
                                            </p>
                                        </div>

                                        {{-- DETAILS --}}
                                        <div class="min-w-0">
                                            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                                <div class="min-w-0">
                                                    @if (!$selectable)
                                                        <p class="sari-cart-unavailable-text">Unavailable</p>
                                                    @endif

                                                    @if ($productVisible)
                                                        <a
                                                            href="{{ route('buyer.product.details', $item->product->id) }}"
                                                            class="sari-cart-product-name mt-2 block text-[12px] font-bold leading-5 text-[#2f2923] transition hover:text-[#a86e11]"
                                                        >
                                                            {{ $item->product->name }}
                                                        </a>
                                                    @else
                                                        <p class="sari-cart-product-name mt-2 block text-[12px] font-bold leading-5 text-[#746c64]">{{ $item->product->name }}</p>
                                                    @endif

                                                    @if ($availabilityReason)
                                                        <p class="mt-1.5 text-[7px] font-medium leading-4 text-[#a65353]">{{ $availabilityReason }}</p>
                                                    @endif

                                                    <p class="sari-cart-variant-text mt-1 text-[7.5px] leading-4 text-[#8f867a]">
                                                        {{ $cart->variantLabel($item) }}
                                                    </p>

                                                    <p class="sari-cart-unit-price mt-2 text-[7px] text-[#a0968b]">
                                                        ₱{{ number_format($unit, 2) }} each
                                                    </p>
                                                </div>

                                                <div class="shrink-0 lg:text-right">
                                                    <p class="sari-cart-line-total text-[17px] font-bold tracking-[-.035em] text-[#c17d0c]" data-line-total-text>
                                                        ₱{{ number_format($line, 2) }}
                                                    </p>
                                                    <p class="sari-cart-line-total-label mt-1 text-[6.5px] font-medium text-[#a1988e]">Item total</p>
                                                </div>
                                            </div>

                                            <div class="mt-3.5 flex flex-col gap-3 border-t border-[#eee8df] pt-3 sm:flex-row sm:items-end sm:justify-between">
                                                <form
                                                    method="POST"
                                                    action="{{ route('buyer.cart.items.update', $item) }}"
                                                    class="flex flex-wrap items-end gap-2"
                                                    data-cart-update-form
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <div>
                                                        <label class="sari-cart-quantity-label mb-1.5 block text-[6px] font-bold uppercase tracking-[.1em] text-[#91887d]">
                                                            Quantity
                                                        </label>

                                                        <div class="sari-qty-control inline-flex h-9 items-center overflow-hidden rounded-[10px] border border-[#dfd7cd] bg-white">
                                                            <button
                                                                type="button"
                                                                data-qty-minus
                                                                class="grid h-full w-9 place-items-center text-[13px] text-[#73695f] transition hover:bg-[#fffaf1] hover:text-[#9a6817]"
                                                                aria-label="Decrease quantity"
                                                                @disabled(!$quantityEditable)
                                                            >−</button>

                                                            <input
                                                                name="quantity"
                                                                type="number"
                                                                min="1"
                                                                max="{{ max(1, $maxStock) }}"
                                                                value="{{ $item->quantity }}"
                                                                class="h-full w-12 border-x border-[#e8e1d8] text-center text-[8.5px] font-semibold text-[#433c35] outline-none"
                                                                data-qty-input
                                                                @disabled(!$quantityEditable)
                                                            >

                                                            <button
                                                                type="button"
                                                                data-qty-plus
                                                                class="grid h-full w-9 place-items-center text-[13px] text-[#73695f] transition hover:bg-[#fffaf1] hover:text-[#9a6817]"
                                                                aria-label="Increase quantity"
                                                                @disabled(!$quantityEditable)
                                                            >+</button>
                                                        </div>
                                                    </div>

                                                    <span
                                                        class="sari-qty-save-status"
                                                        data-qty-save-status
                                                        aria-live="polite"
                                                    ></span>
                                                </form>

                                                <form
                                                    method="POST"
                                                    action="{{ route('buyer.cart.items.destroy', $item) }}"
                                                    onsubmit="return confirm('Remove this item from your cart?')"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        class="sari-btn-remove inline-flex h-9 items-center gap-1.5 rounded-[10px] px-3 font-semibold"
                                                    >
                                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                            <path d="M5 7h14"></path>
                                                            <path d="M9 7V5h6v2"></path>
                                                            <path d="M8 7l1 12h6l1-12"></path>
                                                        </svg>
                                                        <span class="sari-remove-label" style="color:#ef4444 !important;">Remove</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                    </section>
                @endforeach
            </div>

            {{-- =====================================================
                 RIGHT — ORDER SUMMARY
            ====================================================== --}}
            <aside class="sari-cart-summary h-fit overflow-hidden rounded-[18px] border border-[#e7dfd4] bg-white xl:sticky xl:top-24">
                <div class="border-b border-[#eee8df] px-5 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-[17px] font-bold tracking-[-.025em] text-[#302a24]">Order Summary</h2>
                        </div>

                        <span
                            id="cartSelectedSummaryCount"
                            class="sari-summary-selected-count"
                        >
                            0 selected
                        </span>
                    </div>
                </div>

                <div class="p-5">
                    <div class="space-y-3.5 text-[8px]">
                        <div class="flex justify-between gap-4">
                            <span class="text-[#8d8479]">Selected items</span>
                            <span id="cartSelectedSubtotal" class="font-semibold text-[#4c453d]">₱0.00</span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-[#8d8479]">Estimated delivery</span>
                            <span id="cartSelectedDelivery" class="font-semibold text-[#4c453d]">₱0.00</span>
                        </div>

                        <div id="cartSelectedShippingDiscountRow" class="hidden justify-between gap-4 text-[#4f8065]">
                            <span>Free Shipping discount</span>
                            <span id="cartSelectedShippingDiscount" class="font-semibold">-₱0.00</span>
                        </div>

                        <div class="border-t border-[#eee8df] pt-4">
                            <div class="flex items-end justify-between gap-4">
                                <span class="text-[9px] font-semibold text-[#514a42]">Estimated Total</span>
                                <span id="cartSelectedTotal" class="text-[24px] font-bold tracking-[-.045em] text-[#b97805]">₱0.00</span>
                            </div>
                        </div>
                    </div>

                    <form
                        id="selectedCheckoutForm"
                        method="GET"
                        action="{{ route('buyer.checkout') }}"
                        class="mt-5"
                    >
                        <input type="hidden" name="selection" value="1">

                        <button
                            id="selectedCheckoutButton"
                            type="submit"
                            disabled
                            class="sari-btn-checkout sari-summary-disabled flex h-11 w-full items-center justify-center rounded-[11px] px-4 text-[9px] font-bold text-white"
                        >
                            Checkout
                        </button>
                    </form>

                    <div class="mt-3 rounded-[12px] border border-[#dbe6f3] bg-[#f4f8fd] px-3.5 py-3">
                        <div class="flex items-start gap-2.5">
                            <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full border border-[#b8d0ea] text-[#55799e]">
                                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="8"></circle>
                                    <path d="M12 11v5"></path>
                                    <path d="M12 8h.01"></path>
                                </svg>
                            </span>
                            <p id="cartSelectionHint" class="text-[6.5px] leading-4 text-[#67809a]">
                                Only checked products will continue to checkout. Items you do not select stay safely in your cart.
                            </p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    @endif
</div>

@if ($items->isNotEmpty())
<script>
(function () {
    function initSariCartPage() {
        const page = document.querySelector('[data-sari-cart-page]');
        if (!page) return;

        window.__SARI_CART_PAGE_ABORT__?.abort();

        const controller = new AbortController();
        window.__SARI_CART_PAGE_ABORT__ = controller;
        const signal = controller.signal;

        const feePerSeller = Number(@json($feePerSeller));
        const selectAll = page.querySelector('#cartSelectAll');
        const checkboxes = Array.from(page.querySelectorAll('[data-cart-checkbox]:not(:disabled)'));
        const cards = Array.from(page.querySelectorAll('[data-selectable-card]'));
        const updateForms = Array.from(page.querySelectorAll('[data-cart-update-form]'));

        const topCount = page.querySelector('#cartSelectedTopCount');
        const summaryCount = page.querySelector('#cartSelectedSummaryCount');
        const subtotalNode = page.querySelector('#cartSelectedSubtotal');
        const deliveryNode = page.querySelector('#cartSelectedDelivery');
        const shippingDiscountRow = page.querySelector('#cartSelectedShippingDiscountRow');
        const shippingDiscountNode = page.querySelector('#cartSelectedShippingDiscount');
        const totalNode = page.querySelector('#cartSelectedTotal');
        const hintNode = page.querySelector('#cartSelectionHint');
        const checkoutButton = page.querySelector('#selectedCheckoutButton');

        let feedbackTimer = null;
        const quantitySaveState = new WeakMap();

        function peso(value) {
            return '₱' + Number(value || 0).toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        function showFeedback(message, isError = false) {
            document.querySelector('.sari-cart-feedback')?.remove();

            const toast = document.createElement('div');
            toast.className = `sari-cart-feedback${isError ? ' is-error' : ''}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            window.clearTimeout(feedbackTimer);
            feedbackTimer = window.setTimeout(() => toast.remove(), 2200);
        }

        function selectedEntries() {
            return checkboxes
                .filter((checkbox) => checkbox.checked)
                .map((checkbox) => {
                    const card = checkbox.closest('[data-selectable-card]');

                    return {
                        checkbox,
                        card,
                        line: Number(card?.dataset.lineTotal || 0),
                        seller: String(card?.dataset.sellerId || ''),
                        promotionActive: String(card?.dataset.shippingPromoActive || '0') === '1',
                        shippingMinimum: Number(card?.dataset.shippingMinimum || 0),
                        shippingMaxDiscount: Number(card?.dataset.shippingMaxDiscount || 0),
                    };
                });
        }

        function calculateShipping(entries) {
            const groups = new Map();

            entries.forEach((entry) => {
                if (!groups.has(entry.seller)) groups.set(entry.seller, []);
                groups.get(entry.seller).push(entry);
            });

            let original = 0;
            let discount = 0;
            let payable = 0;

            groups.forEach((group) => {
                original += feePerSeller;

                const sellerSubtotal = group.reduce((sum, entry) => sum + entry.line, 0);
                const terms = group[0] || {};
                const qualified = Boolean(terms.promotionActive)
                    && sellerSubtotal >= Number(terms.shippingMinimum || 0);
                const sellerDiscount = qualified
                    ? Math.min(feePerSeller, Math.max(0, Number(terms.shippingMaxDiscount || 0)))
                    : 0;

                discount += sellerDiscount;
                payable += Math.max(0, feePerSeller - sellerDiscount);
            });

            return { original, discount, payable };
        }

        function sync() {
            const entries = selectedEntries();

            const selectedCount = entries.length;
            const subtotal = entries.reduce((sum, entry) => sum + entry.line, 0);
            const shipping = calculateShipping(entries);
            const delivery = shipping.payable;
            const total = subtotal + delivery;

            cards.forEach((card) => {
                const checkbox = card.querySelector('[data-cart-checkbox]');
                card.dataset.selected = checkbox?.checked ? 'true' : 'false';
            });

            if (selectAll) {
                selectAll.checked = selectedCount > 0 && selectedCount === checkboxes.length;
                selectAll.indeterminate = selectedCount > 0 && selectedCount < checkboxes.length;
            }

            const countText = `${selectedCount} selected`;

            if (topCount) topCount.textContent = countText;
            if (summaryCount) summaryCount.textContent = countText;
            if (subtotalNode) subtotalNode.textContent = peso(subtotal);
            if (deliveryNode) deliveryNode.textContent = peso(delivery);
            if (shippingDiscountNode) shippingDiscountNode.textContent = '-' + peso(shipping.discount);

            if (shippingDiscountRow) {
                shippingDiscountRow.classList.toggle('hidden', shipping.discount <= 0);
                shippingDiscountRow.classList.toggle('flex', shipping.discount > 0);
            }

            if (totalNode) totalNode.textContent = peso(total);

            if (hintNode) {
                hintNode.textContent = selectedCount
                    ? `Only these ${selectedCount} selected product${selectedCount === 1 ? '' : 's'} will continue to checkout.`
                    : 'Only checked products will continue to checkout. Items you do not select stay safely in your cart.';
            }

            if (checkoutButton) {
                checkoutButton.disabled = selectedCount === 0;
                checkoutButton.classList.toggle('sari-summary-disabled', selectedCount === 0);
            }
        }

        function getQuantitySaveState(form) {
            if (!quantitySaveState.has(form)) {
                const input = form.querySelector('[data-qty-input]');

                quantitySaveState.set(form, {
                    timer: null,
                    saving: false,
                    dirty: false,
                    lastSaved: String(input?.value ?? ''),
                    clearStatusTimer: null,
                });
            }

            return quantitySaveState.get(form);
        }

        function setQuantitySaveStatus(form, text = '', tone = '') {
            const status = form.querySelector('[data-qty-save-status]');
            if (!status) return;

            status.textContent = text;
            status.dataset.tone = tone;
        }

        function clampQuantity(input) {
            const min = Number(input.min || 1);
            const max = Number(input.max || 999999);
            const raw = Number(input.value || min);
            const next = Math.min(max, Math.max(min, Number.isFinite(raw) ? raw : min));

            input.value = String(Math.trunc(next));
            return input.value;
        }

        function scheduleQuantitySave(form, delay = 320) {
            const state = getQuantitySaveState(form);
            state.dirty = true;

            window.clearTimeout(state.timer);
            state.timer = window.setTimeout(() => persistQuantity(form), delay);
        }

        async function persistQuantity(form) {
            const state = getQuantitySaveState(form);
            const card = form.closest('[data-selectable-card]');
            const input = form.querySelector('[data-qty-input]');
            const lineTotalText = card?.querySelector('[data-line-total-text]');

            if (!input || !card) return;

            const currentValue = clampQuantity(input);

            if (state.saving) {
                state.dirty = true;
                return;
            }

            if (!state.dirty && state.lastSaved === currentValue) return;

            state.saving = true;
            state.dirty = false;
            window.clearTimeout(state.clearStatusTimer);
            setQuantitySaveStatus(form, 'Saving…', 'saving');

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
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

                    throw new Error(validationMessage || data?.message || 'Unable to update this cart item.');
                }

                const newLineTotal = Number(data?.line_total || 0);
                card.dataset.lineTotal = newLineTotal.toFixed(2);
                state.lastSaved = String(input.value);

                if (lineTotalText) {
                    lineTotalText.textContent = peso(newLineTotal);
                }

                document.dispatchEvent(new CustomEvent('sari:cart-updated', {
                    detail: {
                        count: Number(data?.cart_count || 0),
                    },
                }));

                sync();
                setQuantitySaveStatus(form, 'Saved', 'saved');

                state.clearStatusTimer = window.setTimeout(() => {
                    if (!state.saving && !state.dirty) {
                        setQuantitySaveStatus(form, '', '');
                    }
                }, 1100);
            } catch (error) {
                if (error?.name === 'AbortError') return;

                if (state.lastSaved !== '') {
                    input.value = state.lastSaved;
                }

                setQuantitySaveStatus(form, 'Not saved', 'error');
                showFeedback(error?.message || 'Unable to update this cart item.', true);
            } finally {
                state.saving = false;

                if (state.dirty) {
                    window.clearTimeout(state.timer);
                    state.timer = window.setTimeout(() => persistQuantity(form), 120);
                }
            }
        }

        selectAll?.addEventListener('change', function () {
            checkboxes.forEach((checkbox) => {
                checkbox.checked = selectAll.checked;
            });

            sync();
        }, { signal });

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', sync, { signal });
        });

        cards.forEach((card) => {
            card.addEventListener('click', function (event) {
                if (event.target.closest('a,button,input,label,form,select,textarea')) return;

                const checkbox = card.querySelector('[data-cart-checkbox]');
                if (!checkbox || checkbox.disabled) return;

                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change', { bubbles: true }));
            }, { signal });

            const minus = card.querySelector('[data-qty-minus]');
            const plus = card.querySelector('[data-qty-plus]');
            const input = card.querySelector('[data-qty-input]');
            const form = input?.closest('[data-cart-update-form]');

            minus?.addEventListener('click', function () {
                if (!input || !form) return;

                const before = String(input.value);
                const min = Number(input.min || 1);
                input.value = String(Math.max(min, Number(input.value || min) - 1));

                if (String(input.value) !== before) {
                    scheduleQuantitySave(form, 220);
                }
            }, { signal });

            plus?.addEventListener('click', function () {
                if (!input || !form) return;

                const before = String(input.value);
                const max = Number(input.max || 999999);
                input.value = String(Math.min(max, Number(input.value || 1) + 1));

                if (String(input.value) !== before) {
                    scheduleQuantitySave(form, 220);
                }
            }, { signal });

            input?.addEventListener('input', function () {
                if (!form) return;
                scheduleQuantitySave(form, 500);
            }, { signal });

            input?.addEventListener('change', function () {
                if (!form) return;
                scheduleQuantitySave(form, 140);
            }, { signal });

            input?.addEventListener('blur', function () {
                if (!form) return;
                scheduleQuantitySave(form, 0);
            }, { signal });
        });

        updateForms.forEach((form) => {
            getQuantitySaveState(form);

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                const state = getQuantitySaveState(form);
                state.dirty = true;
                persistQuantity(form);
            }, { signal });
        });

        sync();
    }

    document.addEventListener('livewire:navigated', initSariCartPage);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSariCartPage, { once: true });
    } else {
        initSariCartPage();
    }
})();
</script>
@endif



<style>
/* ============================================================
   SARI BUYER CART — FINAL CLEAN RESPONSIVE DESIGN
   - readable at 100% browser zoom
   - balanced desktop/laptop scale
   - responsive tablet/mobile
   - preserves existing Laravel + JS functionality
   ============================================================ */

[data-sari-cart-page] {
    max-width: 1240px !important;
    padding: 28px 24px 48px !important;
    background: transparent !important;
}

/* PAGE HEADER */
[data-sari-cart-page] > section:first-of-type {
    padding-bottom: 20px !important;
}

[data-sari-cart-page] > section:first-of-type p:first-child {
    font-size: 10px !important;
    letter-spacing: .14em !important;
}

[data-sari-cart-page] > section:first-of-type h1 {
    margin-top: 4px !important;
    font-size: clamp(30px, 3vw, 38px) !important;
    line-height: 1.08 !important;
}

[data-sari-cart-page] > section:first-of-type h1 + p {
    margin-top: 7px !important;
    font-size: 13px !important;
    line-height: 1.6 !important;
}

.sari-cart-count-pill,
.sari-btn-secondary {
    height: 42px !important;
    padding-inline: 15px !important;
    font-size: 11px !important;
    border-radius: 10px !important;
}

/* MAIN GRID */
[data-sari-cart-page] > .mt-5.grid {
    margin-top: 22px !important;
    gap: 22px !important;
    grid-template-columns: minmax(0, 1fr) 330px !important;
}

/* SELECT ALL TOOLBAR */
.sari-cart-toolbar {
    padding: 14px 16px !important;
    border-radius: 14px !important;
    box-shadow: 0 8px 22px rgba(56, 43, 28, .055) !important;
}

.sari-cart-toolbar label > span > span:first-child {
    font-size: 12px !important;
}

.sari-cart-toolbar label > span > span:last-child {
    margin-top: 2px !important;
    font-size: 10px !important;
}

#cartSelectedTopCount {
    padding: 5px 10px !important;
    font-size: 9px !important;
}

/* SELLER GROUP */
.sari-seller-shell {
    border-radius: 16px !important;
    box-shadow:
        0 10px 28px rgba(56, 43, 28, .06),
        0 2px 7px rgba(56, 43, 28, .025) !important;
}

.sari-seller-shell > div:first-child {
    padding: 13px 16px !important;
}

.sari-seller-shell > div:first-child h2 {
    font-size: 13px !important;
}

.sari-seller-shell > div:first-child .rounded-full {
    font-size: 8.5px !important;
}

.sari-seller-shell > div:first-child > span {
    font-size: 9.5px !important;
}

/* PRODUCT ROW */
.sari-cart-row {
    padding: 14px !important;
    border-radius: 14px !important;
    box-shadow:
        0 5px 15px rgba(56, 43, 28, .045),
        inset 0 1px 0 rgba(255,255,255,.96) !important;
}

.sari-cart-row > div {
    grid-template-columns: 28px 110px minmax(0, 1fr) !important;
    gap: 14px !important;
}

.sari-cart-row > div > a {
    width: 110px !important;
    height: 110px !important;
    padding: 8px !important;
    border-radius: 12px !important;
}

.sari-cart-row .text-\[12px\] {
    font-size: 14px !important;
}

.sari-cart-row .text-\[7\.5px\] {
    font-size: 11px !important;
    line-height: 1.45 !important;
}

.sari-cart-row .text-\[7px\] {
    font-size: 10px !important;
}

.sari-cart-row .text-\[17px\] {
    font-size: 19px !important;
}

.sari-cart-row .text-\[6\.5px\] {
    font-size: 9px !important;
}

.sari-cart-row .text-\[6px\] {
    font-size: 8.5px !important;
}

/* QUANTITY + ACTIONS */
.sari-qty-control {
    height: 38px !important;
    border-radius: 9px !important;
}

.sari-qty-control button {
    width: 38px !important;
    font-size: 14px !important;
}

.sari-qty-control input {
    width: 48px !important;
    font-size: 11px !important;
}

.sari-btn-update,
.sari-btn-remove {
    height: 38px !important;
    padding-inline: 13px !important;
    border-radius: 9px !important;
    font-size: 10px !important;
}

/* ORDER SUMMARY */
.sari-cart-summary {
    border-radius: 16px !important;
    box-shadow:
        0 12px 30px rgba(56, 43, 28, .07),
        0 3px 8px rgba(56, 43, 28, .025) !important;
}

.sari-cart-summary > div:first-child {
    padding: 16px 18px !important;
}

.sari-cart-summary > div:first-child h2 {
    font-size: 18px !important;
}

#cartSelectedSummaryCount {
    font-size: 9px !important;
    padding: 5px 9px !important;
}

.sari-cart-summary > .p-5 {
    padding: 18px !important;
}

.sari-cart-summary .space-y-3\.5 {
    gap: 12px !important;
    font-size: 12px !important;
}

.sari-cart-summary .space-y-3\.5 > div span:first-child,
.sari-cart-summary .space-y-3\.5 > div span:last-child {
    font-size: 12px !important;
}

#cartSelectedTotal {
    font-size: 25px !important;
}

.sari-btn-checkout {
    height: 46px !important;
    border-radius: 10px !important;
    font-size: 12px !important;
}

#cartSelectionHint {
    font-size: 10px !important;
    line-height: 1.5 !important;
}

.sari-cart-summary .grid.grid-cols-3 p {
    font-size: 8.5px !important;
    line-height: 1.35 !important;
}

.sari-cart-feedback {
    font-size: 11px !important;
    padding: 11px 14px !important;
}

/* COMMON LAPTOP */
@media (max-width: 1600px) and (max-height: 950px) {
    [data-sari-cart-page] {
        max-width: 1160px !important;
        padding: 20px 20px 36px !important;
    }

    [data-sari-cart-page] > section:first-of-type h1 {
        font-size: 32px !important;
    }

    [data-sari-cart-page] > section:first-of-type h1 + p {
        font-size: 12px !important;
    }

    [data-sari-cart-page] > .mt-5.grid {
        gap: 18px !important;
        grid-template-columns: minmax(0, 1fr) 310px !important;
    }

    .sari-cart-row {
        padding: 12px !important;
    }

    .sari-cart-row > div {
        grid-template-columns: 26px 98px minmax(0, 1fr) !important;
        gap: 12px !important;
    }

    .sari-cart-row > div > a {
        width: 98px !important;
        height: 98px !important;
    }

    .sari-cart-row .text-\[12px\] {
        font-size: 13px !important;
    }

    .sari-cart-row .text-\[17px\] {
        font-size: 17px !important;
    }

    .sari-cart-summary > .p-5 {
        padding: 16px !important;
    }
}

/* TABLET / NARROW LAPTOP */
@media (max-width: 1100px) {
    [data-sari-cart-page] > .mt-5.grid {
        grid-template-columns: 1fr !important;
    }

    .sari-cart-summary {
        position: static !important;
        width: 100% !important;
    }
}

/* MOBILE */
@media (max-width: 640px) {
    [data-sari-cart-page] {
        padding: 16px 14px 28px !important;
    }

    [data-sari-cart-page] > section:first-of-type h1 {
        font-size: 29px !important;
    }

    [data-sari-cart-page] > section:first-of-type h1 + p {
        font-size: 11.5px !important;
    }

    .sari-cart-count-pill,
    .sari-btn-secondary {
        height: 40px !important;
        font-size: 10px !important;
    }

    .sari-cart-toolbar {
        padding: 12px 13px !important;
    }

    .sari-cart-row {
        padding: 12px !important;
    }

    .sari-cart-row > div {
        grid-template-columns: 24px 82px minmax(0, 1fr) !important;
        gap: 10px !important;
    }

    .sari-cart-row > div > a {
        width: 82px !important;
        height: 82px !important;
        padding: 6px !important;
    }

    .sari-cart-row .text-\[12px\] {
        font-size: 12.5px !important;
    }

    .sari-cart-row .text-\[17px\] {
        font-size: 16px !important;
    }

    .sari-cart-row .text-\[7\.5px\] {
        font-size: 10px !important;
    }

    .sari-btn-update,
    .sari-btn-remove {
        font-size: 9.5px !important;
    }

    .sari-cart-summary > div:first-child {
        padding: 14px 15px !important;
    }

    .sari-cart-summary > .p-5 {
        padding: 15px !important;
    }
}


/* ============================================================
   SARI BUYER CART — ENTERPRISE FLOATING DESIGN SYSTEM
   Final authoritative visual pass.
   Functionality/backend/JS hooks are intentionally untouched.
============================================================ */

[data-sari-cart-page] {
    --cart-page: #f5f6f8;
    --cart-panel: #ffffff;
    --cart-border: #e2e6ea;
    --cart-border-soft: #eceff2;
    --cart-text: #303740;
    --cart-muted: #7b8490;
    --cart-gold: #c88912;
    --cart-gold-dark: #a96f0e;

    width: 100% !important;
    max-width: 1400px !important;
    margin-inline: auto !important;
    padding: 22px 24px 42px !important;
    background: transparent !important;
    color: var(--cart-text);
}

/* PAGE HEADER — open, compact, same visual scale as reference */
[data-sari-cart-page] > section:first-of-type {
    border-bottom: 0 !important;
    padding: 2px 2px 14px !important;
}

[data-sari-cart-page] > section:first-of-type > div {
    gap: 16px !important;
}

[data-sari-cart-page] > section:first-of-type p:first-child {
    color: #a87316 !important;
    font-size: 6.6px !important;
    font-weight: 800 !important;
    letter-spacing: .15em !important;
}

[data-sari-cart-page] > section:first-of-type h1 {
    margin-top: 5px !important;
    color: #2e343b !important;
    font-size: clamp(27px, 2.25vw, 31px) !important;
    font-weight: 760 !important;
    line-height: 1.04 !important;
    letter-spacing: -.035em !important;
}

[data-sari-cart-page] > section:first-of-type h1 span {
    color: #c88912 !important;
}

[data-sari-cart-page] > section:first-of-type h1 + p {
    max-width: 660px;
    margin-top: 7px !important;
    color: #818995 !important;
    font-size: 8px !important;
    line-height: 1.6 !important;
}

/* HEADER ACTIONS */
.sari-cart-count-pill,
.sari-btn-secondary {
    height: 38px !important;
    border-radius: 9px !important;
    padding-inline: 13px !important;
    font-size: 7.4px !important;
    font-weight: 700 !important;
}

.sari-cart-count-pill {
    border: 1px solid #eadfc9 !important;
    background: #fffaf1 !important;
    color: #95620e !important;
    box-shadow: none !important;
}

.sari-btn-secondary {
    border: 1px solid #dfe3e8 !important;
    background: #fff !important;
    color: #4f5862 !important;
    box-shadow: none !important;
}

.sari-btn-secondary:hover {
    border-color: #d0d6dc !important;
    background: #f8f9fa !important;
    color: #2d343b !important;
}

/* MAIN LAYOUT */
[data-sari-cart-page] > .mt-5.grid {
    margin-top: 10px !important;
    gap: 16px !important;
    grid-template-columns: minmax(0, 1fr) 340px !important;
}

/* Shared floating surfaces */
.sari-cart-toolbar,
.sari-seller-shell,
.sari-cart-summary {
    border: 1px solid var(--cart-border) !important;
    background: var(--cart-panel) !important;
    box-shadow:
        0 12px 30px rgba(31,41,55,.052),
        0 2px 7px rgba(31,41,55,.022) !important;
}

/* SELECT ALL */
.sari-cart-toolbar {
    padding: 11px 13px !important;
    border-radius: 13px !important;
}

.sari-cart-toolbar label {
    gap: 10px !important;
}

.sari-cart-toolbar label > span > span:first-child {
    color: #3b434c !important;
    font-size: 8.6px !important;
    font-weight: 700 !important;
}

.sari-cart-toolbar label > span > span:last-child {
    margin-top: 2px !important;
    color: #9098a1 !important;
    font-size: 6.7px !important;
}

#cartSelectedTopCount {
    border-color: #e7dcc8 !important;
    background: #fffaf1 !important;
    padding: 4px 8px !important;
    color: #91600d !important;
    font-size: 6.8px !important;
}

.sari-cart-toolbar .text-\[7px\] {
    color: #939aa3 !important;
    font-size: 6.6px !important;
}

/* SELLER GROUP */
.sari-seller-shell {
    overflow: hidden !important;
    border-radius: 14px !important;
}

.sari-seller-shell > div:first-child {
    border-bottom: 1px solid var(--cart-border-soft) !important;
    background: #fff !important;
    padding: 11px 13px !important;
}

.sari-seller-shell > div:first-child > div:first-child {
    gap: 10px !important;
}

.sari-seller-shell > div:first-child > div:first-child > span {
    width: 32px !important;
    height: 32px !important;
    border: 1px solid #e7dcc8 !important;
    border-radius: 9px !important;
    background: #fffaf1 !important;
    color: #aa7010 !important;
}

.sari-seller-shell > div:first-child h2 {
    color: #343b43 !important;
    font-size: 9px !important;
    font-weight: 750 !important;
}

.sari-seller-shell > div:first-child .rounded-full {
    font-size: 5.9px !important;
}

.sari-seller-shell > div:first-child .text-\[7px\] {
    color: #8e969f !important;
    font-size: 6.3px !important;
}

.sari-seller-shell > div:first-child a {
    border-color: #e0e4e8 !important;
    border-radius: 8px !important;
    background: #fff !important;
    color: #8f5e0d !important;
}

.sari-seller-shell > div:nth-child(2) {
    background: #f7f8fa !important;
    padding: 8px !important;
}

/* PRODUCT ROW — compact floating item card */
.sari-cart-row {
    border: 1px solid #e4e8ec !important;
    border-radius: 12px !important;
    background: #fff !important;
    padding: 11px !important;
    box-shadow:
        0 5px 14px rgba(31,41,55,.032),
        0 1px 4px rgba(31,41,55,.014) !important;
    transform: none !important;
}

.sari-cart-row:hover {
    border-color: #d8dde2 !important;
    box-shadow:
        0 8px 18px rgba(31,41,55,.045),
        0 2px 6px rgba(31,41,55,.018) !important;
    transform: none !important;
}

.sari-cart-row[data-selected="true"] {
    border-color: #d7b066 !important;
    background: #fffefa !important;
    box-shadow:
        0 7px 18px rgba(179,119,14,.055),
        inset 3px 0 0 #c88912 !important;
}

.sari-cart-row > div {
    grid-template-columns: 24px 96px minmax(0, 1fr) !important;
    gap: 11px !important;
}

.sari-cart-row > div > a {
    width: 96px !important;
    height: 96px !important;
    border: 1px solid #e6eaee !important;
    border-radius: 10px !important;
    background: #f8f9fa !important;
    padding: 6px !important;
    box-shadow: none !important;
}

.sari-cart-check-shell {
    width: 18px !important;
    height: 18px !important;
    border-color: #d8dde2 !important;
    border-radius: 5px !important;
    background: #fff !important;
    box-shadow: none !important;
}

.sari-cart-row[data-selected="true"] .sari-cart-check-shell {
    border-color: #c88912 !important;
    background: #c88912 !important;
    box-shadow: none !important;
}

/* Product badges */
.sari-cart-row .rounded-full {
    border-radius: 999px;
}

.sari-cart-row .bg-\[\#f1f8f4\] {
    border-color: #d8e8de !important;
    background: #f5faf7 !important;
    color: #4d765d !important;
}

.sari-cart-row .bg-\[\#f6f3ef\] {
    background: #f3f5f7 !important;
    color: #68717c !important;
}

/* Product typography */
.sari-cart-row .text-\[12px\] {
    color: #2f363e !important;
    font-size: 10px !important;
    font-weight: 750 !important;
    line-height: 1.45 !important;
}

.sari-cart-row .text-\[7\.5px\] {
    color: #818a94 !important;
    font-size: 6.8px !important;
    line-height: 1.45 !important;
}

.sari-cart-row .text-\[7px\] {
    color: #8d959e !important;
    font-size: 6.5px !important;
}

.sari-cart-row .text-\[17px\] {
    color: #b6760b !important;
    font-size: 14px !important;
    font-weight: 800 !important;
}

.sari-cart-row .text-\[6\.5px\] {
    color: #949ba3 !important;
    font-size: 6px !important;
}

.sari-cart-row .text-\[6px\] {
    font-size: 5.8px !important;
}

/* Product lower action divider */
.sari-cart-row .border-t {
    border-color: var(--cart-border-soft) !important;
}

/* Quantity */
.sari-qty-control {
    height: 34px !important;
    overflow: hidden !important;
    border: 1px solid #dfe3e7 !important;
    border-radius: 8px !important;
    background: #fff !important;
    box-shadow: none !important;
}

.sari-qty-control button {
    width: 32px !important;
    color: #626b75 !important;
    font-size: 12px !important;
}

.sari-qty-control button:hover {
    background: #f5f6f8 !important;
    color: #303740 !important;
}

.sari-qty-control input {
    width: 42px !important;
    border-color: #e6e9ed !important;
    color: #3d454e !important;
    font-size: 7.5px !important;
}

/* Flat formal action buttons */
.sari-btn-update,
.sari-btn-remove {
    height: 34px !important;
    border-radius: 8px !important;
    padding-inline: 11px !important;
    font-size: 6.6px !important;
    font-weight: 700 !important;
    box-shadow: none !important;
}

.sari-btn-update {
    border: 1px solid #d9c28d !important;
    background: #fffaf1 !important;
    color: #8e5d0d !important;
}

.sari-btn-update:hover {
    border-color: #cfa957 !important;
    background: #fff6e6 !important;
}

.sari-btn-remove {
    border: 1px solid #e2e5e8 !important;
    background: #fff !important;
    color: #69727c !important;
}

.sari-btn-remove:hover {
    border-color: #d6dade !important;
    background: #f7f8f9 !important;
    color: #444c55 !important;
}

/* ORDER SUMMARY — strongest floating card */
.sari-cart-summary {
    overflow: hidden !important;
    border-radius: 14px !important;
    box-shadow:
        0 16px 36px rgba(31,41,55,.065),
        0 3px 9px rgba(31,41,55,.025) !important;
}

.sari-cart-summary > div:first-child {
    border-bottom: 1px solid var(--cart-border-soft) !important;
    background: #fff !important;
    padding: 13px 15px !important;
}

.sari-cart-summary > div:first-child h2 {
    color: #303740 !important;
    font-size: 13px !important;
    font-weight: 760 !important;
}

#cartSelectedSummaryCount {
    border-color: #e7dcc8 !important;
    background: #fffaf1 !important;
    color: #8e5d0d !important;
    font-size: 6.5px !important;
    padding: 4px 8px !important;
}

.sari-cart-summary > .p-5 {
    padding: 15px !important;
}

.sari-cart-summary .space-y-3\.5 {
    gap: 10px !important;
    font-size: 7.5px !important;
}

.sari-cart-summary .space-y-3\.5 > div span:first-child {
    color: #7d8690 !important;
    font-size: 7.2px !important;
}

.sari-cart-summary .space-y-3\.5 > div span:last-child {
    color: #404850 !important;
    font-size: 7.4px !important;
}

.sari-cart-summary .space-y-3\.5 .text-\[9px\] {
    color: #444c55 !important;
    font-size: 8px !important;
}

#cartSelectedTotal {
    color: #b6760b !important;
    font-size: 21px !important;
    font-weight: 800 !important;
}

.sari-btn-checkout {
    height: 41px !important;
    border: 1px solid #c88912 !important;
    border-radius: 9px !important;
    background: #c88912 !important;
    color: #fff !important;
    font-size: 8px !important;
    font-weight: 750 !important;
    box-shadow: none !important;
}

.sari-btn-checkout:not(:disabled):hover {
    border-color: #ad760f !important;
    background: #ad760f !important;
}

.sari-summary-disabled {
    border-color: #d7dce0 !important;
    background: #d7dce0 !important;
    color: #fff !important;
}

/* Summary info hint */
.sari-cart-summary .bg-\[\#f4f8fd\] {
    border-color: #e1e7ee !important;
    border-radius: 10px !important;
    background: #f7f9fb !important;
    box-shadow: none !important;
}

#cartSelectionHint {
    color: #75808b !important;
    font-size: 6.3px !important;
    line-height: 1.5 !important;
}

.sari-cart-summary .grid.grid-cols-3 {
    border-color: var(--cart-border-soft) !important;
    gap: 6px !important;
}

.sari-cart-summary .grid.grid-cols-3 > div > span {
    width: 29px !important;
    height: 29px !important;
    border-radius: 8px !important;
    background: #f6f7f8 !important;
}

.sari-cart-summary .grid.grid-cols-3 p {
    color: #747d87 !important;
    font-size: 5.6px !important;
    line-height: 1.35 !important;
}

/* Empty cart */
[data-sari-cart-page] > section.mt-5.rounded-\[20px\] {
    border: 1px solid var(--cart-border) !important;
    border-radius: 14px !important;
    background: #fff !important;
    box-shadow:
        0 12px 30px rgba(31,41,55,.05),
        0 2px 7px rgba(31,41,55,.02) !important;
}

/* Toast */
.sari-cart-feedback {
    border-radius: 10px !important;
    font-size: 7.5px !important;
    box-shadow: 0 12px 28px rgba(31,41,55,.12) !important;
}

/* Responsive */
@media (max-width: 1180px) {
    [data-sari-cart-page] > .mt-5.grid {
        grid-template-columns: 1fr !important;
    }

    .sari-cart-summary {
        position: static !important;
        width: 100% !important;
    }
}

@media (max-width: 767px) {
    [data-sari-cart-page] {
        padding: 16px 14px 28px !important;
    }

    [data-sari-cart-page] > section:first-of-type {
        padding-bottom: 12px !important;
    }

    [data-sari-cart-page] > section:first-of-type h1 {
        font-size: 25px !important;
    }

    .sari-cart-row {
        padding: 10px !important;
    }

    .sari-cart-row > div {
        grid-template-columns: 22px 82px minmax(0,1fr) !important;
        gap: 9px !important;
    }

    .sari-cart-row > div > a {
        width: 82px !important;
        height: 82px !important;
    }

    .sari-cart-count-pill,
    .sari-btn-secondary {
        height: 36px !important;
    }
}

@media (max-width: 520px) {
    .sari-cart-row > div {
        grid-template-columns: 20px 72px minmax(0,1fr) !important;
    }

    .sari-cart-row > div > a {
        width: 72px !important;
        height: 72px !important;
    }
}



/* ============================================================
   STORE PROFILE — REAL SELLER LOGO
   Uses SellerAccount::store_logo_path with initials fallback.
============================================================ */
.sari-store-profile {
    position: relative;
    display: grid;
    width: 34px;
    height: 34px;
    flex: 0 0 34px;
    place-items: center;
    overflow: hidden;
    border: 1px solid #dde2e7;
    border-radius: 10px;
    background: #f7f8fa;
    text-decoration: none;
    box-shadow:
        0 3px 8px rgba(31,41,55,.035),
        inset 0 1px 0 rgba(255,255,255,.9);
    transition:
        border-color .16s ease,
        box-shadow .16s ease,
        transform .16s ease;
}

a.sari-store-profile:hover,
a.sari-store-profile:focus-visible {
    border-color: #d0b16e;
    box-shadow:
        0 6px 14px rgba(31,41,55,.055),
        0 0 0 3px rgba(200,137,18,.08);
    transform: translateY(-1px);
    outline: none;
}

.sari-store-profile-image {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.sari-store-profile-fallback {
    display: grid;
    width: 100%;
    height: 100%;
    place-items: center;
    background: #2f343b;
    color: #ffffff;
    font-size: 8px;
    font-weight: 800;
    letter-spacing: .03em;
    text-transform: uppercase;
}

.sari-store-profile-fallback[hidden] {
    display: none !important;
}

.sari-store-name-link {
    color: #343b43 !important;
    font-size: 9px !important;
    font-weight: 750 !important;
    line-height: 1.25;
    text-decoration: none;
    transition: color .16s ease;
}

a.sari-store-name-link:hover,
a.sari-store-name-link:focus-visible {
    color: #9b6710 !important;
    outline: none;
}

@media (max-width: 640px) {
    .sari-store-profile {
        width: 32px;
        height: 32px;
        flex-basis: 32px;
        border-radius: 9px;
    }

    .sari-store-profile-fallback {
        font-size: 7.5px;
    }
}



/* ============================================================
   CART HEADER FINAL POLISH
   Text-only availability + SARI gold Continue Shopping button.
============================================================ */
.sari-cart-availability-text {
    display: inline-flex;
    align-items: center;
    min-height: 38px;
    color: #5f6873;
    font-size: 7.6px;
    font-weight: 700;
    line-height: 1.35;
    white-space: nowrap;
}

.sari-cart-availability-text::before {
    content: "";
    width: 6px;
    height: 6px;
    flex: 0 0 6px;
    margin-right: 7px;
    border-radius: 999px;
    background: #c88912;
}

.sari-btn-continue-shopping {
    border: 1px solid #c88912 !important;
    background: #c88912 !important;
    color: #ffffff !important;
    box-shadow: none !important;
}

.sari-btn-continue-shopping svg {
    color: #ffffff !important;
    stroke: currentColor !important;
}

.sari-btn-continue-shopping:hover,
.sari-btn-continue-shopping:focus-visible {
    border-color: #ad760f !important;
    background: #ad760f !important;
    color: #ffffff !important;
    box-shadow: none !important;
    transform: none !important;
}

.sari-btn-continue-shopping:focus-visible {
    outline: 3px solid rgba(200,137,18,.12);
    outline-offset: 2px;
}

@media (max-width: 640px) {
    .sari-cart-availability-text {
        min-height: 34px;
        font-size: 7.2px;
        white-space: normal;
    }
}



/* ============================================================
   CART READABILITY + DANGER ACTION + CHECKBOX POLISH
============================================================ */

/* Stronger readability across cart toolbar, seller headers and item rows */
.sari-cart-toolbar label > span > span:first-child {
    font-size: 10px !important;
    font-weight: 800 !important;
    color: #303740 !important;
}

.sari-cart-toolbar label > span > span:last-child {
    font-size: 7.6px !important;
    color: #7e8791 !important;
}

#cartSelectedTopCount {
    font-size: 7.4px !important;
    font-weight: 800 !important;
}

.sari-cart-toolbar .text-\[7px\] {
    font-size: 7.2px !important;
    color: #7f8892 !important;
}

.sari-store-name-link,
.sari-seller-shell > div:first-child h2 {
    font-size: 10.2px !important;
    font-weight: 800 !important;
    color: #303740 !important;
}

.sari-seller-shell > div:first-child .rounded-full {
    font-size: 6.4px !important;
    font-weight: 700 !important;
}

.sari-seller-shell > div:first-child .text-\[7px\] {
    font-size: 7px !important;
    color: #7f8892 !important;
}

.sari-seller-shell > div:first-child a:not(.sari-store-profile):not(.sari-store-name-link) {
    font-size: 7px !important;
    font-weight: 700 !important;
}

.sari-cart-row .text-\[12px\] {
    font-size: 11.5px !important;
    line-height: 1.45 !important;
}

.sari-cart-row .text-\[7\.5px\] {
    font-size: 7.7px !important;
}

.sari-cart-row .text-\[7px\] {
    font-size: 7.2px !important;
}

.sari-cart-row .text-\[17px\] {
    font-size: 16px !important;
}

.sari-cart-row .text-\[6\.5px\] {
    font-size: 6.8px !important;
}

.sari-cart-row .text-\[6px\] {
    font-size: 6.4px !important;
}

.sari-cart-row label.mb-1\.5 {
    font-size: 6.6px !important;
}

.sari-qty-control input {
    font-size: 8.2px !important;
    font-weight: 700 !important;
}

.sari-btn-update,
.sari-btn-remove {
    font-size: 7.1px !important;
    font-weight: 750 !important;
}

/* Bright red Remove treatment */
.sari-btn-remove {
    border: 1px solid #ef4444 !important;
    background: #ffffff !important;
    color: #dc2626 !important;
}

.sari-btn-remove svg {
    color: #dc2626 !important;
    stroke: currentColor !important;
}

.sari-btn-remove:hover,
.sari-btn-remove:focus-visible {
    border-color: #dc2626 !important;
    background: #fff5f5 !important;
    color: #b91c1c !important;
    box-shadow: none !important;
}

.sari-btn-remove:hover svg,
.sari-btn-remove:focus-visible svg {
    color: #b91c1c !important;
}

.sari-btn-remove:focus-visible {
    outline: 3px solid rgba(239,68,68,.11);
    outline-offset: 2px;
}

/* Item checkbox: gold box, guaranteed white check */
.sari-cart-row[data-selected="true"] .sari-cart-check-shell {
    border-color: #c88912 !important;
    background: #c88912 !important;
    color: #ffffff !important;
}

.sari-cart-row[data-selected="true"] .sari-cart-check-mark {
    color: #ffffff !important;
    stroke: #ffffff !important;
    opacity: 1 !important;
}

/* Select-all checkbox: custom visual so the check is white */
.sari-select-all-check-wrap {
    position: relative;
    display: inline-grid;
    width: 18px;
    height: 18px;
    flex: 0 0 18px;
    place-items: center;
}

.sari-select-all-check-shell {
    display: grid;
    width: 18px;
    height: 18px;
    place-items: center;
    border: 1px solid #d5dbe0;
    border-radius: 5px;
    background: #ffffff;
    color: #ffffff;
    cursor: pointer;
    transition:
        border-color .14s ease,
        background-color .14s ease,
        box-shadow .14s ease;
}

.sari-select-all-check-mark {
    width: 12px;
    height: 12px;
    color: #ffffff;
    stroke: #ffffff;
    opacity: 0;
    transform: scale(.72);
    transition: opacity .14s ease, transform .14s ease;
}

#cartSelectAll:checked + .sari-select-all-check-shell,
#cartSelectAll:indeterminate + .sari-select-all-check-shell {
    border-color: #c88912;
    background: #c88912;
    box-shadow: 0 4px 10px rgba(200,137,18,.14);
}

#cartSelectAll:checked + .sari-select-all-check-shell .sari-select-all-check-mark {
    opacity: 1;
    transform: scale(1);
}

#cartSelectAll:indeterminate + .sari-select-all-check-shell .sari-select-all-check-mark {
    opacity: 1;
    transform: scale(1);
}

#cartSelectAll:focus-visible + .sari-select-all-check-shell {
    outline: 3px solid rgba(200,137,18,.12);
    outline-offset: 2px;
}

@media (max-width: 640px) {
    .sari-cart-toolbar label > span > span:first-child {
        font-size: 9.6px !important;
    }

    .sari-cart-row .text-\[12px\] {
        font-size: 11px !important;
    }

    .sari-cart-row .text-\[17px\] {
        font-size: 15px !important;
    }
}



/* ============================================================
   FINAL CART READABILITY PASS
   Larger hierarchy + forced bright-red Remove text/icon.
============================================================ */

/* Top availability + Continue Shopping */
.sari-cart-availability-text {
    min-height: 40px !important;
    color: #4f5964 !important;
    font-size: 9px !important;
    font-weight: 750 !important;
}

.sari-cart-availability-text::before {
    width: 7px !important;
    height: 7px !important;
    flex-basis: 7px !important;
}

.sari-btn-continue-shopping {
    height: 40px !important;
    padding-inline: 15px !important;
    font-size: 9px !important;
    font-weight: 800 !important;
}

/* Select-all toolbar */
.sari-cart-toolbar {
    padding: 13px 15px !important;
}

.sari-cart-toolbar label > span > span:first-child {
    font-size: 11px !important;
    font-weight: 800 !important;
}

.sari-cart-toolbar label > span > span:last-child {
    font-size: 8.5px !important;
    line-height: 1.45 !important;
}

#cartSelectedTopCount {
    padding: 5px 9px !important;
    font-size: 8.2px !important;
    font-weight: 800 !important;
}

.sari-cart-toolbar .text-\[7px\] {
    font-size: 8px !important;
    color: #737d87 !important;
}

/* Seller area */
.sari-store-name-link,
.sari-seller-shell > div:first-child h2 {
    font-size: 11.2px !important;
    font-weight: 800 !important;
}

.sari-seller-shell > div:first-child .rounded-full {
    font-size: 7px !important;
    font-weight: 750 !important;
}

.sari-seller-shell > div:first-child .text-\[7px\] {
    font-size: 7.8px !important;
}

.sari-seller-shell > div:first-child a:not(.sari-store-profile):not(.sari-store-name-link) {
    font-size: 7.8px !important;
    font-weight: 750 !important;
}

/* Product content */
.sari-cart-row .text-\[12px\] {
    font-size: 13px !important;
    line-height: 1.45 !important;
}

.sari-cart-row .text-\[7\.5px\] {
    font-size: 8.8px !important;
    line-height: 1.5 !important;
}

.sari-cart-row .text-\[7px\] {
    font-size: 8.2px !important;
}

.sari-cart-row .text-\[17px\] {
    font-size: 18px !important;
}

.sari-cart-row .text-\[6\.5px\] {
    font-size: 7.6px !important;
}

.sari-cart-row .text-\[6px\] {
    font-size: 7.2px !important;
}

.sari-cart-row label.mb-1\.5 {
    font-size: 7.4px !important;
    font-weight: 800 !important;
}

.sari-qty-control {
    height: 36px !important;
}

.sari-qty-control button {
    font-size: 14px !important;
}

.sari-qty-control input {
    font-size: 9px !important;
    font-weight: 750 !important;
}

.sari-btn-update,
.sari-btn-remove {
    height: 36px !important;
    padding-inline: 12px !important;
    font-size: 8px !important;
    font-weight: 800 !important;
}

/* Force Remove icon + word bright red */
.sari-btn-remove,
.sari-btn-remove *,
.sari-btn-remove span,
.sari-btn-remove svg {
    color: #ef4444 !important;
    stroke: #ef4444 !important;
}

.sari-btn-remove {
    border: 1px solid #ef4444 !important;
    background: #ffffff !important;
}

.sari-btn-remove:hover,
.sari-btn-remove:focus-visible {
    border-color: #dc2626 !important;
    background: #fff5f5 !important;
}

.sari-btn-remove:hover,
.sari-btn-remove:hover *,
.sari-btn-remove:hover svg,
.sari-btn-remove:focus-visible,
.sari-btn-remove:focus-visible *,
.sari-btn-remove:focus-visible svg {
    color: #dc2626 !important;
    stroke: #dc2626 !important;
}

/* Order Summary */
.sari-cart-summary > div:first-child h2 {
    font-size: 16px !important;
    font-weight: 800 !important;
}

#cartSelectedSummaryCount {
    padding: 5px 9px !important;
    font-size: 8px !important;
    font-weight: 800 !important;
}

.sari-cart-summary .space-y-3\.5 {
    gap: 12px !important;
    font-size: 9px !important;
}

.sari-cart-summary .space-y-3\.5 > div span:first-child {
    font-size: 9px !important;
    color: #6f7984 !important;
}

.sari-cart-summary .space-y-3\.5 > div span:last-child {
    font-size: 9.2px !important;
    font-weight: 750 !important;
}

.sari-cart-summary .space-y-3\.5 .text-\[9px\] {
    font-size: 10px !important;
    font-weight: 800 !important;
}

#cartSelectedTotal {
    font-size: 25px !important;
    font-weight: 850 !important;
}

.sari-btn-checkout {
    height: 44px !important;
    font-size: 10px !important;
    font-weight: 800 !important;
}

#cartSelectionHint {
    font-size: 7.8px !important;
    line-height: 1.55 !important;
    color: #68737f !important;
}

.sari-cart-summary .grid.grid-cols-3 p {
    font-size: 6.8px !important;
    font-weight: 700 !important;
    line-height: 1.4 !important;
}

/* Slightly larger trust icons for visual balance */
.sari-cart-summary .grid.grid-cols-3 > div > span {
    width: 32px !important;
    height: 32px !important;
}

/* Mobile keeps readability without becoming oversized */
@media (max-width: 640px) {
    .sari-cart-availability-text {
        font-size: 8.5px !important;
    }

    .sari-btn-continue-shopping {
        font-size: 8.5px !important;
    }

    .sari-cart-toolbar label > span > span:first-child {
        font-size: 10.5px !important;
    }

    .sari-cart-row .text-\[12px\] {
        font-size: 12.2px !important;
    }

    .sari-cart-row .text-\[17px\] {
        font-size: 17px !important;
    }

    .sari-cart-summary > div:first-child h2 {
        font-size: 15px !important;
    }
}



/* Absolute final override for Remove label */
.sari-btn-remove .sari-remove-label {
    color: #ef4444 !important;
    -webkit-text-fill-color: #ef4444 !important;
    opacity: 1 !important;
}

.sari-btn-remove:hover .sari-remove-label,
.sari-btn-remove:focus-visible .sari-remove-label {
    color: #dc2626 !important;
    -webkit-text-fill-color: #dc2626 !important;
}



/* ============================================================
   EMPTY CART + HEADER ACTIONS — FINAL MINIMAL POLISH
   Plain light-charcoal header actions and a softer empty state.
============================================================ */

/* Header helper sentence is intentionally removed in markup. */
[data-sari-cart-page] > section:first-of-type > div > div:last-child {
    gap: 18px !important;
}

/* Availability is plain text only — no dot/pill/container treatment. */
.sari-cart-availability-text {
    display: inline !important;
    min-height: 0 !important;
    color: #59636e !important;
    font-size: 9px !important;
    font-weight: 650 !important;
    line-height: 1.4 !important;
    white-space: normal !important;
}

.sari-cart-availability-text::before {
    display: none !important;
    content: none !important;
}

/* Continue Shopping — larger plain text link with gold-only hover. */
.sari-cart-continue-text {
    position: relative !important;
    top: -12px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    padding: 0 !important;
    color: #59636e !important;
    font-size: 11.5px !important;
    font-weight: 750 !important;
    line-height: 1.35 !important;
    text-decoration: none !important;
    box-shadow: none !important;
    transition: color .14s ease !important;
}

.sari-cart-continue-arrow {
    display: inline-block;
    margin-left: 0 !important;
    color: currentColor;
    font-size: 18px !important;
    font-weight: 800 !important;
    line-height: .8 !important;
    transform: translateY(-1px);
}

.sari-cart-continue-text:hover,
.sari-cart-continue-text:focus-visible {
    color: #c88912 !important;
    background: transparent !important;
    outline: none !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

/* Empty cart: soft neutral presentation, no cream icon treatment. */
.sari-cart-empty-state {
    border: 1px solid #e3e7eb !important;
    border-radius: 16px !important;
    background: #ffffff !important;
    padding-top: 72px !important;
    padding-bottom: 72px !important;
    box-shadow: 0 10px 28px rgba(31,41,55,.045) !important;
}

.sari-cart-empty-icon {
    width: 92px !important;
    height: 92px !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    color: #b4bbc3 !important;
    box-shadow: none !important;
}

.sari-cart-empty-icon svg {
    width: 58px !important;
    height: 58px !important;
    color: #b4bbc3 !important;
    stroke: currentColor !important;
    stroke-width: 1.45 !important;
}

.sari-cart-empty-title {
    margin-top: 18px !important;
    color: #9ca4ad !important;
    font-size: 22px !important;
    font-weight: 750 !important;
    line-height: 1.2 !important;
    letter-spacing: -.025em !important;
}

.sari-cart-empty-copy {
    margin-top: 7px !important;
    color: #b0b7bf !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    line-height: 1.55 !important;
}

.sari-cart-empty-button {
    min-width: 170px !important;
    height: 48px !important;
    border: 1px solid #c88912 !important;
    border-radius: 11px !important;
    background: #c88912 !important;
    padding: 0 26px !important;
    color: #ffffff !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    text-decoration: none !important;
    box-shadow: none !important;
    transition: background-color .14s ease, border-color .14s ease !important;
}

.sari-cart-empty-button:hover,
.sari-cart-empty-button:focus-visible {
    border-color: #ad760f !important;
    background: #ad760f !important;
    color: #ffffff !important;
    outline: none !important;
}

/* Larger eyebrow label for cart page header. */
[data-sari-cart-page] > section:first-of-type p:first-child {
    color: #a87316 !important;
    font-size: 10.5px !important;
    font-weight: 800 !important;
    letter-spacing: .14em !important;
    line-height: 1.2 !important;
}

@media (max-width: 640px) {
    [data-sari-cart-page] > section:first-of-type p:first-child {
        font-size: 9.5px !important;
    }

    [data-sari-cart-page] > section:first-of-type > div > div:last-child {
        gap: 12px !important;
    }

    .sari-cart-availability-text {
        font-size: 8.5px !important;
    }

    .sari-cart-continue-text {
        top: -8px !important;
        font-size: 10.5px !important;
    }

    .sari-cart-continue-arrow {
        font-size: 16px !important;
    }

    .sari-cart-empty-state {
        padding: 56px 20px !important;
    }

    .sari-cart-empty-icon {
        width: 64px !important;
        height: 64px !important;
    }

    .sari-cart-empty-icon svg {
        width: 40px !important;
        height: 40px !important;
    }

    .sari-cart-empty-title {
        font-size: 20px !important;
    }

    .sari-cart-empty-copy {
        font-size: 11px !important;
    }

    .sari-cart-empty-button {
        min-width: 160px !important;
        height: 46px !important;
        font-size: 10.5px !important;
    }
}


/* ============================================================
   CART SELLER / STOCK / SHIPPING / REALTIME QUANTITY — FINAL PASS
============================================================ */
.sari-seller-shell {
    display: flex !important;
    flex-direction: column !important;
}

.sari-seller-shell > div:first-child {
    order: 1;
}

.sari-seller-items {
    order: 2;
    background: #f8f9fb !important;
    padding: 12px !important;
}

.sari-shipping-footer {
    order: 3;
}

.sari-view-seller-link {
    display: inline-flex !important;
    min-height: 36px !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 7px !important;
    border: 1px solid #d8b66e !important;
    border-radius: 10px !important;
    background: #ffffff !important;
    padding: 0 13px !important;
    color: #8f5d0b !important;
    font-size: 9.5px !important;
    font-weight: 750 !important;
    line-height: 1 !important;
    text-decoration: none !important;
    box-shadow: none !important;
    transition: border-color .14s ease, background-color .14s ease, color .14s ease !important;
}

.sari-view-seller-link svg {
    width: 13px !important;
    height: 13px !important;
    fill: none !important;
    stroke: currentColor !important;
    stroke-width: 1.9 !important;
    stroke-linecap: round !important;
    stroke-linejoin: round !important;
}

.sari-view-seller-link:hover,
.sari-view-seller-link:focus-visible {
    border-color: #c88912 !important;
    background: #c88912 !important;
    color: #ffffff !important;
    outline: none !important;
}

.sari-cart-media-column {
    display: flex;
    min-width: 0;
    flex-direction: column;
    align-items: flex-start;
}

.sari-cart-media-link {
    display: flex !important;
    width: 100% !important;
    aspect-ratio: 1 / 1 !important;
    align-items: center !important;
    justify-content: center !important;
    overflow: hidden !important;
    border: 1px solid #e5e9ed !important;
    border-radius: 12px !important;
    background: #f8f9fb !important;
    padding: 8px !important;
    box-shadow: none !important;
}

.sari-cart-media-unavailable {
    color: #8c96a1 !important;
    font-size: 9px !important;
    font-weight: 650 !important;
    text-align: center !important;
}

.sari-cart-stock-text {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin: 8px 0 0 2px;
    color: #5d7d68;
    font-size: 9px;
    font-weight: 650;
    line-height: 1.3;
    white-space: nowrap;
}

.sari-cart-stock-dot {
    width: 6px;
    height: 6px;
    flex: 0 0 6px;
    border-radius: 999px;
    background: #56a16f;
}

.sari-cart-stock-text.is-out {
    color: #a76666;
}

.sari-cart-stock-text.is-out .sari-cart-stock-dot {
    background: #d27b7b;
}

.sari-cart-unavailable-text {
    margin-bottom: 6px;
    color: #b25f5f;
    font-size: 9px;
    font-weight: 700;
}

.sari-shipping-footer {
    display: flex;
    min-height: 64px;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    border-top: 1px solid #e7ece9;
    border-bottom: 0 !important;
    background: #f7faf8;
    padding: 13px 16px;
}

.sari-shipping-footer-copy {
    display: flex;
    min-width: 0;
    align-items: center;
    gap: 11px;
}

.sari-shipping-footer-copy > svg {
    width: 22px;
    height: 22px;
    flex: 0 0 22px;
    fill: none;
    stroke: #4f8065;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.sari-shipping-footer-title {
    color: #334a3c;
    font-size: 10.5px;
    font-weight: 800;
    line-height: 1.3;
}

.sari-shipping-footer-description {
    margin-top: 3px;
    color: #718078;
    font-size: 8.5px;
    font-weight: 500;
    line-height: 1.5;
}

.sari-shipping-status {
    display: inline-flex;
    min-height: 32px;
    flex: 0 0 auto;
    align-items: center;
    justify-content: center;
    border: 1px solid #d7e3db;
    border-radius: 999px;
    background: #ffffff;
    padding: 0 12px;
    color: #68786f;
    font-size: 8.5px;
    font-weight: 750;
    white-space: nowrap;
}

.sari-shipping-status[data-qualified="true"] {
    border-color: #cde2d3;
    background: #eef8f1;
    color: #397451;
}

.sari-qty-save-status {
    display: inline-flex;
    min-width: 48px;
    min-height: 36px;
    align-items: center;
    color: #8b949e;
    font-size: 8px;
    font-weight: 650;
    line-height: 1;
}

.sari-qty-save-status[data-tone="saving"] {
    color: #8b949e;
}

.sari-qty-save-status[data-tone="saved"] {
    color: #4f8065;
}

.sari-qty-save-status[data-tone="error"] {
    color: #c45151;
}

/* The quantity is persisted automatically; no manual Update control remains. */
.sari-btn-update {
    display: none !important;
}

/* The media column now owns the product image dimensions. */
.sari-cart-row > div > a {
    width: auto !important;
    height: auto !important;
}

@media (max-width: 767px) {
    .sari-view-seller-link {
        min-height: 34px !important;
        padding-inline: 11px !important;
        font-size: 9px !important;
    }

    .sari-cart-stock-text {
        margin-top: 6px;
        font-size: 8.2px;
    }

    .sari-shipping-footer {
        align-items: flex-start;
        flex-direction: column;
        gap: 9px;
        padding: 12px 13px;
    }

    .sari-shipping-footer-title {
        font-size: 10px;
    }

    .sari-shipping-footer-description {
        font-size: 8px;
    }

    .sari-shipping-status {
        min-height: 30px;
        font-size: 8px;
    }
}



/* ============================================================
   ORDER SUMMARY + SELECT TOOLBAR — FINAL READABILITY POLISH
============================================================ */
/* Top toolbar: selected count intentionally removed from markup. */
.sari-cart-toolbar > div:last-child {
    margin-left: auto !important;
}

.sari-cart-toolbar > div:last-child .text-\[7px\] {
    color: #66717d !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
}

/* Order Summary selected count: text only, no pill/container. */
.sari-summary-selected-count {
    display: inline !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    padding: 0 !important;
    color: #222831 !important;
    font-size: 11px !important;
    font-weight: 750 !important;
    line-height: 1.35 !important;
    box-shadow: none !important;
    white-space: nowrap !important;
}

/* Bigger, clearer summary hierarchy. */
.sari-cart-summary > div:first-child {
    padding: 18px 20px !important;
}

.sari-cart-summary > div:first-child h2 {
    color: #252c34 !important;
    font-size: 19px !important;
    font-weight: 800 !important;
    line-height: 1.2 !important;
}

.sari-cart-summary > .p-5 {
    padding: 20px !important;
}

.sari-cart-summary .space-y-3\.5 {
    gap: 14px !important;
}

.sari-cart-summary .space-y-3\.5 > div span:first-child {
    color: #65707b !important;
    font-size: 11px !important;
    font-weight: 600 !important;
}

.sari-cart-summary .space-y-3\.5 > div span:last-child {
    color: #2f3740 !important;
    font-size: 11.5px !important;
    font-weight: 750 !important;
}

.sari-cart-summary .space-y-3\.5 .text-\[9px\] {
    color: #343c45 !important;
    font-size: 12px !important;
    font-weight: 750 !important;
}

#cartSelectedTotal {
    color: #b6760b !important;
    font-size: 28px !important;
    font-weight: 850 !important;
}

.sari-btn-checkout {
    height: 48px !important;
    font-size: 12px !important;
    font-weight: 800 !important;
}

#cartSelectionHint {
    color: #687480 !important;
    font-size: 9.5px !important;
    line-height: 1.6 !important;
}

.sari-cart-summary .grid.grid-cols-3 p {
    color: #66717d !important;
    font-size: 8.5px !important;
    font-weight: 700 !important;
    line-height: 1.35 !important;
}

.sari-cart-summary .grid.grid-cols-3 > div > span {
    width: 36px !important;
    height: 36px !important;
}

.sari-cart-summary .grid.grid-cols-3 > div > span svg {
    width: 18px !important;
    height: 18px !important;
}

@media (max-width: 640px) {
    .sari-cart-toolbar > div:last-child .text-\[7px\] {
        font-size: 9.5px !important;
    }

    .sari-summary-selected-count {
        font-size: 10px !important;
    }

    .sari-cart-summary > div:first-child h2 {
        font-size: 17px !important;
    }

    .sari-cart-summary .space-y-3\.5 > div span:first-child,
    .sari-cart-summary .space-y-3\.5 > div span:last-child {
        font-size: 10.5px !important;
    }

    #cartSelectedTotal {
        font-size: 25px !important;
    }
}


/* ============================================================
   PRODUCT CARD TEXT + IMAGE FILL — FINAL POLISH
   Larger product information and full-bleed product imagery.
============================================================ */
.sari-store-name-link,
.sari-seller-shell > div:first-child h2 {
    color: #202a35 !important;
    font-size: 15px !important;
    font-weight: 800 !important;
    line-height: 1.2 !important;
    letter-spacing: -.02em !important;
}

.sari-verified-seller-badge {
    min-height: 27px !important;
    padding: 0 10px !important;
    font-size: 10px !important;
    font-weight: 750 !important;
}

.sari-verified-seller-badge svg {
    width: 13px !important;
    height: 13px !important;
}

.sari-seller-product-count {
    color: #68737f !important;
    font-size: 10.5px !important;
    font-weight: 650 !important;
}

.sari-view-seller-link {
    min-height: 38px !important;
    padding-inline: 14px !important;
    font-size: 10.5px !important;
    font-weight: 750 !important;
}

.sari-view-seller-link svg {
    width: 14px !important;
    height: 14px !important;
}

.sari-shipping-footer {
    min-height: 72px !important;
    padding: 15px 18px !important;
}

.sari-shipping-footer-title {
    color: #2f4939 !important;
    font-size: 12.5px !important;
    font-weight: 800 !important;
}

.sari-shipping-footer-description {
    margin-top: 4px !important;
    color: #65766c !important;
    font-size: 10.5px !important;
    font-weight: 550 !important;
    line-height: 1.45 !important;
}

.sari-shipping-status {
    min-height: 36px !important;
    padding-inline: 14px !important;
    font-size: 10.5px !important;
    font-weight: 750 !important;
}

/* Image fills the full square regardless of source dimensions. */
.sari-cart-media-link {
    position: relative !important;
    width: 100% !important;
    aspect-ratio: 1 / 1 !important;
    overflow: hidden !important;
    border-radius: 13px !important;
    background: #eef1f4 !important;
    padding: 0 !important;
}

.sari-cart-media-link .sari-cart-img {
    display: block !important;
    width: 100% !important;
    height: 100% !important;
    min-width: 100% !important;
    min-height: 100% !important;
    object-fit: cover !important;
    object-position: center !important;
    transform: none !important;
}

.sari-cart-row:hover .sari-cart-img {
    transform: scale(1.025) !important;
}

.sari-cart-stock-text {
    margin-top: 9px !important;
    color: #527460 !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
}

.sari-cart-stock-dot {
    width: 7px !important;
    height: 7px !important;
    flex-basis: 7px !important;
}

.sari-cart-product-name {
    margin-top: 2px !important;
    color: #202a35 !important;
    font-size: 16px !important;
    font-weight: 800 !important;
    line-height: 1.35 !important;
    letter-spacing: -.015em !important;
}

/* Variant details intentionally get stronger readability. */
.sari-cart-variant-text {
    margin-top: 7px !important;
    color: #5d6976 !important;
    font-size: 12.5px !important;
    font-weight: 650 !important;
    line-height: 1.5 !important;
}

.sari-cart-unit-price {
    margin-top: 5px !important;
    color: #6f7a86 !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    line-height: 1.45 !important;
}

.sari-cart-line-total {
    color: #b6760b !important;
    font-size: 22px !important;
    font-weight: 850 !important;
    line-height: 1.1 !important;
}

.sari-cart-line-total-label {
    margin-top: 4px !important;
    color: #7e8994 !important;
    font-size: 10px !important;
    font-weight: 650 !important;
}

.sari-cart-quantity-label {
    margin-bottom: 8px !important;
    color: #697581 !important;
    font-size: 10px !important;
    font-weight: 800 !important;
    letter-spacing: .12em !important;
}

.sari-qty-control {
    height: 42px !important;
}

.sari-qty-control button {
    width: 40px !important;
    font-size: 17px !important;
}

.sari-qty-control input {
    width: 50px !important;
    font-size: 11.5px !important;
    font-weight: 750 !important;
}

.sari-btn-remove {
    min-height: 42px !important;
    padding-inline: 14px !important;
    font-size: 10.5px !important;
    font-weight: 750 !important;
}

.sari-btn-remove svg {
    width: 15px !important;
    height: 15px !important;
}

.sari-qty-save-status {
    min-height: 42px !important;
    font-size: 9.5px !important;
}

@media (max-width: 767px) {
    .sari-store-name-link,
    .sari-seller-shell > div:first-child h2 {
        font-size: 13.5px !important;
    }

    .sari-verified-seller-badge,
    .sari-seller-product-count,
    .sari-view-seller-link {
        font-size: 9.5px !important;
    }

    .sari-shipping-footer-title {
        font-size: 11.5px !important;
    }

    .sari-shipping-footer-description,
    .sari-shipping-status {
        font-size: 9.5px !important;
    }

    .sari-cart-stock-text {
        font-size: 9.5px !important;
    }

    .sari-cart-product-name {
        font-size: 14px !important;
    }

    .sari-cart-variant-text {
        font-size: 11.5px !important;
    }

    .sari-cart-unit-price {
        font-size: 11px !important;
    }

    .sari-cart-line-total {
        font-size: 19px !important;
    }

    .sari-cart-line-total-label,
    .sari-cart-quantity-label,
    .sari-btn-remove {
        font-size: 9.5px !important;
    }
}



/* ============================================================
   FINAL MICRO POLISH — PRODUCT READABILITY + SELECTED GLOW
============================================================ */

/* Selected product: stay white, glow only. */
.sari-cart-row[data-selected="true"] {
    border-color: #d7a53a !important;
    background: #ffffff !important;
    box-shadow:
        0 0 0 2px rgba(200,137,18,.10),
        0 10px 26px rgba(200,137,18,.12) !important;
}

/* Stronger product content hierarchy. */
.sari-cart-product-name {
    color: #18212b !important;
    font-size: 18.5px !important;
    font-weight: 800 !important;
    line-height: 1.32 !important;
    letter-spacing: -.02em !important;
}

.sari-cart-variant-text {
    margin-top: 8px !important;
    color: #465361 !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    line-height: 1.5 !important;
}

.sari-cart-unit-price {
    margin-top: 6px !important;
    color: #596675 !important;
    font-size: 13.5px !important;
    font-weight: 650 !important;
    line-height: 1.45 !important;
}

.sari-cart-stock-text {
    font-size: 11.5px !important;
    font-weight: 700 !important;
}

/* Seller product count is easier to scan. */
.sari-seller-product-count {
    color: #4f5b67 !important;
    font-size: 12.5px !important;
    font-weight: 700 !important;
}

/* Full SARI-gold View Seller action. */
.sari-view-seller-link,
.sari-seller-shell > div:first-child a.sari-view-seller-link {
    min-height: 40px !important;
    border: 1px solid #c88912 !important;
    border-radius: 11px !important;
    background: #c88912 !important;
    padding: 0 15px !important;
    color: #ffffff !important;
    font-size: 11.5px !important;
    font-weight: 800 !important;
    box-shadow: 0 6px 14px rgba(200,137,18,.15) !important;
}

.sari-view-seller-link svg,
.sari-seller-shell > div:first-child a.sari-view-seller-link svg {
    width: 15px !important;
    height: 15px !important;
    color: #ffffff !important;
    stroke: #ffffff !important;
}

.sari-view-seller-link:hover,
.sari-view-seller-link:focus-visible,
.sari-seller-shell > div:first-child a.sari-view-seller-link:hover,
.sari-seller-shell > div:first-child a.sari-view-seller-link:focus-visible {
    border-color: #ad760f !important;
    background: #ad760f !important;
    color: #ffffff !important;
    box-shadow: 0 8px 18px rgba(173,118,15,.18) !important;
}

/* Brighter verified seller badge. */
.sari-verified-seller-badge,
.sari-seller-shell > div:first-child .sari-verified-seller-badge {
    min-height: 31px !important;
    border: 1px solid #16a34a !important;
    border-radius: 999px !important;
    background: #22c55e !important;
    padding: 0 12px !important;
    color: #ffffff !important;
    font-size: 11.5px !important;
    font-weight: 800 !important;
    box-shadow: 0 5px 12px rgba(34,197,94,.16) !important;
}

.sari-verified-seller-badge svg {
    width: 15px !important;
    height: 15px !important;
    color: #ffffff !important;
    stroke: #ffffff !important;
}

/* Legacy shipping status styling (unused after status UI removal). */
.sari-shipping-status[data-qualified="true"] {
    min-height: 38px !important;
    border-color: #16a34a !important;
    background: #22c55e !important;
    padding-inline: 15px !important;
    color: #ffffff !important;
    font-size: 11.5px !important;
    font-weight: 800 !important;
    box-shadow: 0 5px 12px rgba(34,197,94,.14) !important;
}

@media (max-width: 767px) {
    .sari-cart-product-name {
        font-size: 16px !important;
    }

    .sari-cart-variant-text {
        font-size: 12.5px !important;
    }

    .sari-cart-unit-price {
        font-size: 12px !important;
    }

    .sari-seller-product-count {
        font-size: 11px !important;
    }

    .sari-view-seller-link,
    .sari-seller-shell > div:first-child a.sari-view-seller-link,
    .sari-verified-seller-badge,
    .sari-seller-shell > div:first-child .sari-verified-seller-badge,
    .sari-shipping-status[data-qualified="true"] {
        font-size: 10.5px !important;
    }
}



/* ============================================================
   FINAL BADGE + SELLER ACTION POLISH
   Outline-only green badges; larger seller actions/count.
============================================================ */
.sari-verified-seller-badge,
.sari-seller-shell > div:first-child .sari-verified-seller-badge {
    min-height: 34px !important;
    border: 1.5px solid #16a34a !important;
    border-radius: 999px !important;
    background: transparent !important;
    padding: 0 13px !important;
    color: #16a34a !important;
    font-size: 12.5px !important;
    font-weight: 800 !important;
    box-shadow: none !important;
}

.sari-verified-seller-badge svg {
    width: 16px !important;
    height: 16px !important;
    color: #16a34a !important;
    stroke: #16a34a !important;
}

.sari-shipping-status[data-qualified="true"] {
    min-height: 38px !important;
    gap: 7px !important;
    border: 1.5px solid #16a34a !important;
    background: transparent !important;
    padding-inline: 15px !important;
    color: #16a34a !important;
    font-size: 12.5px !important;
    font-weight: 800 !important;
    box-shadow: none !important;
}

.sari-shipping-status-icon {
    width: 16px !important;
    height: 16px !important;
    flex: 0 0 16px !important;
    fill: none !important;
    stroke: currentColor !important;
    stroke-width: 2 !important;
    stroke-linecap: round !important;
    stroke-linejoin: round !important;
}

.sari-shipping-status-icon[hidden] {
    display: none !important;
}

.sari-seller-product-count {
    color: #37424d !important;
    font-size: 14px !important;
    font-weight: 800 !important;
}

.sari-view-seller-link,
.sari-seller-shell > div:first-child a.sari-view-seller-link {
    min-height: 42px !important;
    padding-inline: 17px !important;
    font-size: 13px !important;
    font-weight: 800 !important;
}

.sari-view-seller-link svg,
.sari-seller-shell > div:first-child a.sari-view-seller-link svg {
    width: 16px !important;
    height: 16px !important;
}

@media (max-width: 767px) {
    .sari-verified-seller-badge,
    .sari-seller-shell > div:first-child .sari-verified-seller-badge,
    .sari-shipping-status[data-qualified="true"] {
        font-size: 11.5px !important;
    }

    .sari-seller-product-count {
        font-size: 12.5px !important;
    }

    .sari-view-seller-link,
    .sari-seller-shell > div:first-child a.sari-view-seller-link {
        min-height: 38px !important;
        padding-inline: 14px !important;
        font-size: 11.5px !important;
    }
}


/* ============================================================
   FINAL CLEANUP — SELLER ACTIONS + REMOVE BUTTON
   Verification/status/trust UI removed from markup.
============================================================ */
.sari-seller-product-count {
    color: #303943 !important;
    font-size: 15px !important;
    font-weight: 800 !important;
    line-height: 1.2 !important;
}

.sari-view-seller-link,
.sari-seller-shell > div:first-child a.sari-view-seller-link {
    min-height: 46px !important;
    border-radius: 12px !important;
    padding-inline: 20px !important;
    font-size: 14px !important;
    font-weight: 800 !important;
    gap: 8px !important;
}

.sari-view-seller-link svg,
.sari-seller-shell > div:first-child a.sari-view-seller-link svg {
    width: 17px !important;
    height: 17px !important;
}

.sari-btn-remove {
    min-height: 44px !important;
    padding-inline: 18px !important;
    border-radius: 10px !important;
    font-size: 12.5px !important;
    font-weight: 800 !important;
    gap: 7px !important;
}

.sari-btn-remove svg {
    width: 17px !important;
    height: 17px !important;
}

/* With the qualification badge removed, keep shipping copy left aligned. */
.sari-shipping-footer {
    justify-content: flex-start !important;
}

@media (max-width: 767px) {
    .sari-seller-product-count {
        font-size: 13px !important;
    }

    .sari-view-seller-link,
    .sari-seller-shell > div:first-child a.sari-view-seller-link {
        min-height: 40px !important;
        padding-inline: 15px !important;
        font-size: 12px !important;
    }

    .sari-btn-remove {
        min-height: 40px !important;
        padding-inline: 14px !important;
        font-size: 11.5px !important;
    }
}


/* ============================================================
   ABSOLUTE FINAL BUTTON TYPOGRAPHY FIX
   Beats legacy high-specificity !important rules.
============================================================ */
[data-sari-cart-page] .sari-seller-shell > div:first-child > div:last-child .sari-seller-product-count {
    color: #303943 !important;
    font-size: 12.5px !important;
    font-weight: 750 !important;
    line-height: 1.2 !important;
}

[data-sari-cart-page] .sari-seller-shell > div:first-child > div:last-child a.sari-view-seller-link {
    min-height: 40px !important;
    padding-inline: 16px !important;
    gap: 7px !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
}

[data-sari-cart-page] .sari-seller-shell > div:first-child > div:last-child a.sari-view-seller-link > span {
    font-size: 12px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
}

[data-sari-cart-page] .sari-seller-shell > div:first-child > div:last-child a.sari-view-seller-link > svg {
    width: 15px !important;
    height: 15px !important;
    flex: 0 0 15px !important;
}

[data-sari-cart-page] .sari-cart-row .sari-btn-remove {
    min-height: 40px !important;
    padding-inline: 15px !important;
    gap: 6px !important;
    font-size: 11.5px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
}

[data-sari-cart-page] .sari-cart-row .sari-btn-remove .sari-remove-label {
    color: #ef4444 !important;
    -webkit-text-fill-color: #ef4444 !important;
    font-size: 11.5px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
}

[data-sari-cart-page] .sari-cart-row .sari-btn-remove > svg {
    width: 15px !important;
    height: 15px !important;
    flex: 0 0 15px !important;
}

@media (max-width: 767px) {
    [data-sari-cart-page] .sari-seller-shell > div:first-child > div:last-child .sari-seller-product-count {
        font-size: 11.5px !important;
    }

    [data-sari-cart-page] .sari-seller-shell > div:first-child > div:last-child a.sari-view-seller-link,
    [data-sari-cart-page] .sari-seller-shell > div:first-child > div:last-child a.sari-view-seller-link > span {
        font-size: 11px !important;
    }

    [data-sari-cart-page] .sari-seller-shell > div:first-child > div:last-child a.sari-view-seller-link {
        min-height: 38px !important;
        padding-inline: 14px !important;
    }

    [data-sari-cart-page] .sari-cart-row .sari-btn-remove,
    [data-sari-cart-page] .sari-cart-row .sari-btn-remove .sari-remove-label {
        font-size: 10.5px !important;
    }

    [data-sari-cart-page] .sari-cart-row .sari-btn-remove {
        min-height: 38px !important;
        padding-inline: 13px !important;
    }
}



/* Balanced final sizes after button typography tuning. */
[data-sari-cart-page] .sari-summary-selected-count {
    color: #222831 !important;
    font-size: 12.5px !important;
    font-weight: 750 !important;
}

@media (max-width: 767px) {
    [data-sari-cart-page] .sari-summary-selected-count {
        font-size: 11.5px !important;
    }
}


/* ============================================================
   FINAL PRODUCT INFO + SELLER HEADER SPACING POLISH
   Stronger readability without changing cart behavior.
============================================================ */
[data-sari-cart-page] .sari-cart-row .sari-cart-product-name {
    color: #1f2933 !important;
    font-size: 15.5px !important;
    font-weight: 650 !important;
    line-height: 1.32 !important;
    letter-spacing: -.018em !important;
}

[data-sari-cart-page] .sari-cart-row .sari-cart-variant-text {
    margin-top: 8px !important;
    color: #4d5a68 !important;
    font-size: 12.5px !important;
    font-weight: 500 !important;
    line-height: 1.5 !important;
}

[data-sari-cart-page] .sari-cart-row .sari-cart-unit-price {
    margin-top: 6px !important;
    color: #687482 !important;
    font-size: 11.5px !important;
    font-weight: 500 !important;
    line-height: 1.45 !important;
}

/* Cleaner seller identity: stronger name, neutral text, gold only on interaction. */
[data-sari-cart-page] .sari-seller-shell > div:first-child .sari-store-name-link {
    color: #27313b !important;
    font-size: 14.5px !important;
    font-weight: 650 !important;
    line-height: 1.2 !important;
    letter-spacing: -.018em !important;
}

[data-sari-cart-page] .sari-seller-shell > div:first-child a.sari-store-name-link:hover,
[data-sari-cart-page] .sari-seller-shell > div:first-child a.sari-store-name-link:focus-visible {
    color: #a96f0e !important;
}

/* Give the product count and View Seller action deliberate breathing room. */
[data-sari-cart-page] .sari-seller-shell > div:first-child > div:last-child {
    gap: 18px !important;
    margin-left: 18px !important;
}

[data-sari-cart-page] .sari-seller-shell > div:first-child > div:last-child .sari-seller-product-count {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
}

[data-sari-cart-page] .sari-seller-shell > div:first-child > div:last-child a.sari-view-seller-link {
    min-height: 40px !important;
    padding-inline: 15px !important;
}

@media (max-width: 767px) {
    [data-sari-cart-page] .sari-cart-row .sari-cart-product-name {
        font-size: 14.5px !important;
    }

    [data-sari-cart-page] .sari-cart-row .sari-cart-variant-text {
        font-size: 11.5px !important;
    }

    [data-sari-cart-page] .sari-cart-row .sari-cart-unit-price {
        font-size: 10.8px !important;
    }

    [data-sari-cart-page] .sari-seller-shell > div:first-child .sari-store-name-link {
        font-size: 13.5px !important;
    }

    [data-sari-cart-page] .sari-seller-shell > div:first-child > div:last-child {
        gap: 11px !important;
        margin-left: 0 !important;
    }
}
</style>


@endsection

@push('scripts')
    @vite('resources/js/buyer-cart.js')
@endpush
