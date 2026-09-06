@extends('layouts.buyer')

@section('title', 'Shopping Cart — SARI')
@section('page-title', 'Shopping Cart')

@section('content')

@include('components.buyer.header')

@php
    $feePerSeller = (float) config('sari_buyer.delivery_fee_per_seller', 80);
    $itemGroups = $items->groupBy(fn ($item) => (int) ($item->product?->seller_account_id ?? 0));
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
                <p class="mt-1.5 text-[9px] leading-5 text-[#8d8479]">
                    Choose the products you want to checkout. Only checked items will continue.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <span class="sari-cart-count-pill inline-flex h-10 items-center gap-2 rounded-[12px] border border-[#eadfc9] bg-[#fffaf1] px-4 text-[8px] font-semibold text-[#966719]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M3 4h2l2 11h11l2-8H6"></path>
                        <circle cx="9" cy="19" r="1.4"></circle>
                        <circle cx="18" cy="19" r="1.4"></circle>
                    </svg>
                    {{ $items->count() }} item{{ $items->count() === 1 ? '' : 's' }} in your cart
                </span>

                <a
                    href="{{ route('buyer.products') }}"
                    class="sari-btn-secondary inline-flex h-10 items-center justify-center gap-2 rounded-[12px] border border-[#dfd5c8] bg-white px-4 text-[8px] font-semibold text-[#62594f]"
                >
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="m14 7-5 5 5 5"></path>
                    </svg>
                    Continue Shopping
                </a>
            </div>
        </div>
    </section>

    @if ($items->isEmpty())
        <section class="mt-5 rounded-[20px] border border-dashed border-[#ded5c9] bg-white px-6 py-16 text-center">
            <span class="mx-auto grid h-14 w-14 place-items-center rounded-[16px] border border-[#eadfc9] bg-[#fffaf1] text-[#b97812]">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path d="M3 4h2l2 11h11l2-8H6"></path>
                    <circle cx="9" cy="19" r="1.4"></circle>
                    <circle cx="18" cy="19" r="1.4"></circle>
                </svg>
            </span>

            <h2 class="mt-4 text-[15px] font-bold text-[#403a33]">Your cart is empty</h2>
            <p class="mt-1.5 text-[8.5px] text-[#91887d]">
                Start exploring products from approved SARI sellers.
            </p>

            <a
                href="{{ route('buyer.products') }}"
                class="mt-5 inline-flex h-10 items-center rounded-xl bg-[#c98d19] px-5 text-[8px] font-semibold text-white shadow-[0_7px_16px_rgba(201,141,25,.14)] transition hover:bg-[#b77d10]"
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
                        <input
                            id="cartSelectAll"
                            type="checkbox"
                            class="sari-cart-select h-4 w-4 rounded border-[#d9d0c4]"
                        >
                        <span>
                            <span class="block text-[9px] font-semibold text-[#403930]">Select all products</span>
                            <span class="mt-0.5 block text-[7px] text-[#9a9187]">
                                {{ $items->count() }} item{{ $items->count() === 1 ? '' : 's' }} in your cart
                            </span>
                        </span>
                    </label>

                    <div class="flex items-center gap-2.5">
                        <span
                            id="cartSelectedTopCount"
                            class="rounded-full border border-[#eadfc9] bg-[#fffaf1] px-2.5 py-1 text-[7px] font-semibold text-[#9a6817]"
                        >
                            0 selected
                        </span>
                        <span class="text-[7px] text-[#a29a90]">Click a product card to select</span>
                    </div>
                </section>

                @foreach ($itemGroups as $sellerId => $sellerItems)
                    @php
                        $seller = $sellerItems->first()?->product?->seller;
                        $storeName = $seller?->store_name ?: 'SARI Seller Store';
                    @endphp

                    <section class="sari-seller-shell overflow-hidden rounded-[18px] border border-[#e8e0d6] bg-white">
                        {{-- SELLER HEADER --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#eee8df] bg-[#fcfbf8] px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-[10px] border border-[#ead9b8] bg-[#fff8e9] text-[#b97913]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 10h16"></path>
                                        <path d="M5 10 7 5h10l2 5"></path>
                                        <path d="M6 10v9h12v-9"></path>
                                        <path d="M9 19v-5h6v5"></path>
                                    </svg>
                                </span>

                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-[10.5px] font-bold text-[#39322b]">{{ $storeName }}</h2>
                                    <span class="inline-flex items-center gap-1 rounded-full border border-[#cfe3d6] bg-[#f1f8f4] px-2 py-1 text-[6px] font-semibold text-[#4f7d63]">
                                        <svg viewBox="0 0 24 24" class="h-2.5 w-2.5" fill="none" stroke="currentColor" stroke-width="1.9">
                                            <path d="M12 3l7 3v5c0 4.5-2.9 8.2-7 9-4.1-.8-7-4.5-7-9V6l7-3Z"></path>
                                            <path d="m9 12 2 2 4-4"></path>
                                        </svg>
                                        Verified Seller
                                    </span>
                                </div>
                            </div>

                            <span class="text-[7px] font-medium text-[#9b9287]">
                                {{ $sellerItems->count() }} product{{ $sellerItems->count() === 1 ? '' : 's' }}
                            </span>
                        </div>

                        <div class="space-y-2.5 bg-[#faf9f7] p-2.5">
                            @foreach ($sellerItems as $item)
                                @php
                                    $unit = $cart->unitPrice($item);
                                    $line = $cart->lineTotal($item);
                                    $maxStock = $item->variant
                                        ? (int) $item->variant->stock
                                        : (int) $item->product->stock;
                                    $freeShipping = (bool) ($item->product?->free_shipping ?? false);
                                @endphp

                                <article
                                    class="sari-cart-row rounded-[15px] border border-[#ece5dc] bg-white p-3.5 sm:p-4"
                                    data-cart-item
                                    data-selectable-card
                                    data-selected="false"
                                    data-line-total="{{ number_format($line, 2, '.', '') }}"
                                    data-seller-id="{{ (int) ($item->product?->seller_account_id ?? 0) }}"
                                    data-free-shipping="{{ $freeShipping ? '1' : '0' }}"
                                >
                                    <div class="grid grid-cols-[26px_106px_minmax(0,1fr)] gap-3 sm:grid-cols-[28px_120px_minmax(0,1fr)]">

                                        {{-- CHECKBOX --}}
                                        <div class="flex justify-center pt-1">
                                            <label class="cursor-pointer" title="Select for checkout">
                                                <input
                                                    form="selectedCheckoutForm"
                                                    name="items[]"
                                                    value="{{ $item->id }}"
                                                    type="checkbox"
                                                    class="sari-cart-select sr-only"
                                                    data-cart-checkbox
                                                >
                                                <span class="sari-cart-check-shell grid h-5 w-5 place-items-center rounded-[6px] border border-[#d8d0c5] bg-white text-white">
                                                    <svg viewBox="0 0 24 24" class="sari-cart-check-mark h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.4">
                                                        <path d="m6 12 4 4 8-8"></path>
                                                    </svg>
                                                </span>
                                            </label>
                                        </div>

                                        {{-- IMAGE --}}
                                        <a
                                            href="{{ route('buyer.product.details', $item->product->id) }}"
                                            class="flex h-[106px] w-[106px] items-center justify-center overflow-hidden rounded-[12px] border border-[#ebe3d8] bg-[#f8f5ef] p-2 sm:h-[120px] sm:w-[120px]"
                                        >
                                            <img
                                                src="{{ route('buyer.product.image', $item->product->id) }}"
                                                alt="{{ $item->product->name }}"
                                                class="sari-cart-img h-full w-full object-contain"
                                                onerror="this.style.display='none'"
                                            >
                                        </a>

                                        {{-- DETAILS --}}
                                        <div class="min-w-0">
                                            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                                <div class="min-w-0">
                                                    <div class="flex flex-wrap items-center gap-1.5">
                                                        @if ($freeShipping)
                                                            <span class="inline-flex items-center gap-1 rounded-full border border-[#cfe3d6] bg-[#f1f8f4] px-2 py-1 text-[6px] font-semibold text-[#4f7d63]">
                                                                <svg viewBox="0 0 24 24" class="h-2.5 w-2.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                                    <path d="M3 7h11v9H3z"></path><path d="M14 10h4l3 3v3h-7z"></path>
                                                                </svg>
                                                                Free Shipping
                                                            </span>
                                                        @endif

                                                        <span class="rounded-full bg-[#f6f3ef] px-2 py-1 text-[6px] font-semibold text-[#756c62]">
                                                            {{ max(0, $maxStock) }} in stock
                                                        </span>
                                                    </div>

                                                    <a
                                                        href="{{ route('buyer.product.details', $item->product->id) }}"
                                                        class="mt-2 block text-[12px] font-bold leading-5 text-[#2f2923] transition hover:text-[#a86e11]"
                                                    >
                                                        {{ $item->product->name }}
                                                    </a>

                                                    <p class="mt-1 text-[7.5px] leading-4 text-[#8f867a]">
                                                        {{ $cart->variantLabel($item) }}
                                                    </p>

                                                    <p class="mt-2 text-[7px] text-[#a0968b]">
                                                        ₱{{ number_format($unit, 2) }} each
                                                    </p>
                                                </div>

                                                <div class="shrink-0 lg:text-right">
                                                    <p class="text-[17px] font-bold tracking-[-.035em] text-[#c17d0c]" data-line-total-text>
                                                        ₱{{ number_format($line, 2) }}
                                                    </p>
                                                    <p class="mt-1 text-[6.5px] font-medium text-[#a1988e]">Item total</p>
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
                                                        <label class="mb-1.5 block text-[6px] font-bold uppercase tracking-[.1em] text-[#91887d]">
                                                            Quantity
                                                        </label>

                                                        <div class="sari-qty-control inline-flex h-9 items-center overflow-hidden rounded-[10px] border border-[#dfd7cd] bg-white">
                                                            <button
                                                                type="button"
                                                                data-qty-minus
                                                                class="grid h-full w-9 place-items-center text-[13px] text-[#73695f] transition hover:bg-[#fffaf1] hover:text-[#9a6817]"
                                                                aria-label="Decrease quantity"
                                                            >−</button>

                                                            <input
                                                                name="quantity"
                                                                type="number"
                                                                min="1"
                                                                max="{{ max(1, $maxStock) }}"
                                                                value="{{ $item->quantity }}"
                                                                class="h-full w-12 border-x border-[#e8e1d8] text-center text-[8.5px] font-semibold text-[#433c35] outline-none"
                                                                data-qty-input
                                                            >

                                                            <button
                                                                type="button"
                                                                data-qty-plus
                                                                class="grid h-full w-9 place-items-center text-[13px] text-[#73695f] transition hover:bg-[#fffaf1] hover:text-[#9a6817]"
                                                                aria-label="Increase quantity"
                                                            >+</button>
                                                        </div>
                                                    </div>

                                                    <button
                                                        class="sari-btn-update sari-update-state h-9 rounded-[10px] px-3.5 text-[7px] font-semibold"
                                                    >
                                                        Update
                                                    </button>
                                                </form>

                                                <form
                                                    method="POST"
                                                    action="{{ route('buyer.cart.items.destroy', $item) }}"
                                                    onsubmit="return confirm('Remove this item from your cart?')"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        class="sari-btn-remove inline-flex h-9 items-center gap-1.5 rounded-[10px] px-3 text-[7px] font-semibold"
                                                    >
                                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                            <path d="M5 7h14"></path>
                                                            <path d="M9 7V5h6v2"></path>
                                                            <path d="M8 7l1 12h6l1-12"></path>
                                                        </svg>
                                                        Remove
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
                            class="rounded-full border border-[#eadfc9] bg-[#fffaf1] px-2.5 py-1 text-[6.5px] font-semibold text-[#966719]"
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

                    <div class="mt-4 grid grid-cols-3 gap-2 border-t border-[#eee8df] pt-4">
                        <div class="text-center">
                            <span class="mx-auto grid h-8 w-8 place-items-center rounded-[9px] bg-[#f2f8f4] text-[#4f8065]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M12 3l7 3v5c0 4.5-2.9 8.2-7 9-4.1-.8-7-4.5-7-9V6l7-3Z"></path>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg>
                            </span>
                            <p class="mt-1.5 text-[5.8px] font-semibold text-[#756d63]">Buyer Protection</p>
                        </div>

                        <div class="text-center">
                            <span class="mx-auto grid h-8 w-8 place-items-center rounded-[9px] bg-[#fff7e8] text-[#b87912]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="4" y="6" width="16" height="12" rx="2"></rect>
                                    <path d="M4 10h16"></path>
                                </svg>
                            </span>
                            <p class="mt-1.5 text-[5.8px] font-semibold text-[#756d63]">Secure Checkout</p>
                        </div>

                        <div class="text-center">
                            <span class="mx-auto grid h-8 w-8 place-items-center rounded-[9px] bg-[#f3f6fa] text-[#577899]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M3 7h11v9H3z"></path><path d="M14 10h4l3 3v3h-7z"></path>
                                </svg>
                            </span>
                            <p class="mt-1.5 text-[5.8px] font-semibold text-[#756d63]">Verified Sellers</p>
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
        const checkboxes = Array.from(page.querySelectorAll('[data-cart-checkbox]'));
        const cards = Array.from(page.querySelectorAll('[data-selectable-card]'));
        const updateForms = Array.from(page.querySelectorAll('[data-cart-update-form]'));

        const topCount = page.querySelector('#cartSelectedTopCount');
        const summaryCount = page.querySelector('#cartSelectedSummaryCount');
        const subtotalNode = page.querySelector('#cartSelectedSubtotal');
        const deliveryNode = page.querySelector('#cartSelectedDelivery');
        const totalNode = page.querySelector('#cartSelectedTotal');
        const hintNode = page.querySelector('#cartSelectionHint');
        const checkoutButton = page.querySelector('#selectedCheckoutButton');

        let feedbackTimer = null;

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
                        freeShipping: String(card?.dataset.freeShipping || '0') === '1',
                    };
                });
        }

        function calculateDelivery(entries) {
            const groups = new Map();

            entries.forEach((entry) => {
                if (!groups.has(entry.seller)) groups.set(entry.seller, []);
                groups.get(entry.seller).push(entry);
            });

            let fee = 0;

            groups.forEach((group) => {
                const allShipFree = group.every((entry) => entry.freeShipping);
                if (!allShipFree) fee += feePerSeller;
            });

            return fee;
        }

        function sync() {
            const entries = selectedEntries();
            const selectedCount = entries.length;
            const subtotal = entries.reduce((sum, entry) => sum + entry.line, 0);
            const delivery = calculateDelivery(entries);
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
                if (!checkbox) return;

                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change', { bubbles: true }));
            }, { signal });

            const minus = card.querySelector('[data-qty-minus]');
            const plus = card.querySelector('[data-qty-plus]');
            const input = card.querySelector('[data-qty-input]');

            minus?.addEventListener('click', function () {
                if (!input) return;

                const min = Number(input.min || 1);
                input.value = Math.max(min, Number(input.value || min) - 1);
            }, { signal });

            plus?.addEventListener('click', function () {
                if (!input) return;

                const max = Number(input.max || 999);
                input.value = Math.min(max, Number(input.value || 1) + 1);
            }, { signal });
        });

        updateForms.forEach((form) => {
            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                const card = form.closest('[data-selectable-card]');
                const button = form.querySelector('.sari-btn-update');
                const input = form.querySelector('[data-qty-input]');
                const lineTotalText = card?.querySelector('[data-line-total-text]');

                if (!button || !input || !card) return;

                const oldText = button.textContent;
                button.disabled = true;
                button.textContent = 'Saving...';

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

                    if (lineTotalText) {
                        lineTotalText.textContent = peso(newLineTotal);
                    }

                    document.dispatchEvent(new CustomEvent('sari:cart-updated', {
                        detail: {
                            count: Number(data?.cart_count || 0),
                        },
                    }));

                    sync();

                    button.textContent = 'Saved';
                    showFeedback(data?.message || 'Cart updated.');

                    window.setTimeout(() => {
                        if (button.isConnected) button.textContent = oldText;
                    }, 900);
                } catch (error) {
                    if (error?.name === 'AbortError') return;

                    button.textContent = oldText;
                    showFeedback(error?.message || 'Unable to update this cart item.', true);
                } finally {
                    if (button.isConnected) button.disabled = false;
                }
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

@endsection

@push('scripts')
    @vite('resources/js/buyer-cart.js')
@endpush
