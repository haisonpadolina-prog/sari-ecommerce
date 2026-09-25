@extends('layouts.buyer')

@section('title', 'My Orders — SARI')
@section('page-title', 'My Orders')

@section('content')

@include('components.buyer.header')

@php
    $statusSteps = [
        ['key' => 'new', 'label' => 'Order Placed', 'icon' => 'shopping_bag'],
        ['key' => 'preparing', 'label' => 'Preparing', 'icon' => 'inventory_2'],
        ['key' => 'courier_accepted', 'label' => 'Courier Assigned', 'icon' => 'local_shipping'],
        ['key' => 'in_transit', 'label' => 'Out for Delivery', 'icon' => 'two_wheeler'],
        ['key' => 'delivered', 'label' => 'Delivered', 'icon' => 'home'],
    ];

    $statusIndex = function ($status) {
        return match ($status) {
            'new' => 0,
            'preparing', 'ready_for_pickup' => 1,
            'courier_accepted', 'heading_pickup', 'arrived_pickup' => 2,
            'in_transit', 'arrived_buyer' => 3,
            'delivered' => 4,
            default => 0,
        };
    };

    $featuredOrder = method_exists($orders, 'first') ? $orders->first() : null;
    $featuredStep = $featuredOrder ? $statusIndex($featuredOrder->status) : 0;

    $toShipCount = $orders->filter(fn($o) => in_array($o->status, ['new', 'preparing', 'ready_for_pickup']))->count();
    $inTransitCount = $orders->filter(fn($o) => in_array($o->status, ['courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit', 'arrived_buyer']))->count();
    $deliveredCount = $orders->where('status', 'delivered')->count();
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,400,0,0" rel="stylesheet">

<style>
    .sari-orders-page {
        --sari-gold: #d99a18;
        --sari-gold-dark: #b97905;
        --sari-gold-soft: #fff6df;
        --sari-ink: #1b1b1b;
        --sari-muted: #77736b;
        --sari-line: #eee8dc;
        --sari-surface: #ffffff;
        --sari-page: #ffffff;
    }

    .sari-orders-page {
        background: var(--sari-page);
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /*
     * Soft elevation system.
     * The page stays white; panels get a restrained lift through layered
     * shadows and a subtle inner highlight instead of heavy neumorphism.
     */
    .sari-card {
        background: var(--sari-surface);
        border: 1px solid #ece7df;
        box-shadow:
            0 16px 36px rgba(44, 39, 33, .060),
            0 4px 12px rgba(44, 39, 33, .030),
            inset 0 1px 0 rgba(255, 255, 255, .96);
    }

    .sari-soft-panel {
        box-shadow:
            0 14px 32px rgba(44, 39, 33, .055),
            0 3px 10px rgba(44, 39, 33, .028),
            inset 0 1px 0 rgba(255, 255, 255, .96);
    }

    .sari-soft-panel-sm {
        box-shadow:
            0 10px 24px rgba(44, 39, 33, .050),
            0 2px 8px rgba(44, 39, 33, .025),
            inset 0 1px 0 rgba(255, 255, 255, .95);
    }

    .sari-order-card {
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }

    @media (hover: hover) and (pointer: fine) {
        .sari-order-card:hover {
            transform: translateY(-2px);
            box-shadow:
                0 19px 42px rgba(44, 39, 33, .072),
                0 5px 14px rgba(44, 39, 33, .035),
                inset 0 1px 0 rgba(255, 255, 255, .98);
            border-color: #eadfcf;
        }

        .sari-soft-panel,
        .sari-soft-panel-sm,
        .sari-sidebar-card,
        .sari-summary-strip {
            transition: box-shadow .22s ease, border-color .22s ease;
        }

        .sari-sidebar-card:hover,
        .sari-soft-panel:hover {
            box-shadow:
                0 16px 36px rgba(44, 39, 33, .065),
                0 4px 12px rgba(44, 39, 33, .032),
                inset 0 1px 0 rgba(255, 255, 255, .98);
        }
    }

    .sari-gold-btn {
        background: linear-gradient(135deg, #e5ad32 0%, #cf8e0d 100%);
        box-shadow: 0 7px 18px rgba(201, 137, 0, .18);
    }

    .sari-gold-btn:hover {
        background: linear-gradient(135deg, #dca129 0%, #bb7905 100%);
        box-shadow: 0 9px 22px rgba(201, 137, 0, .24);
    }

    .sari-step-line {
        position: absolute;
        top: 19px;
        height: 2px;
        background: #e9e2d6;
        left: 10%;
        right: 10%;
    }

    .sari-step-progress {
        position: absolute;
        top: 19px;
        height: 2px;
        left: 10%;
        background: linear-gradient(90deg, #e4ab2c, #c98605);
        transition: width .45s ease;
    }

    .sari-step {
        position: relative;
        z-index: 2;
        width: 92px;
        flex: 0 0 92px;
    }

    .sari-step-dot {
        width: 40px;
        height: 40px;
        margin-inline: auto;
        border-radius: 9999px;
        display: grid;
        place-items: center;
        border: 2px solid #ddd6ca;
        background: #fff;
        color: #aaa49a;
        transition: .2s ease;
    }

    .sari-step-dot.is-complete {
        border-color: var(--sari-gold);
        background: var(--sari-gold);
        color: #fff;
        box-shadow: 0 0 0 6px rgba(217, 154, 24, .10);
    }

    .sari-step-dot.is-current {
        box-shadow: 0 0 0 6px rgba(217, 154, 24, .12), 0 7px 15px rgba(217, 154, 24, .16);
    }

    .sari-mini-divider {
        width: 1px;
        height: 42px;
        background: var(--sari-line);
    }

    .sari-sidebar {
        position: sticky;
        top: 20px;
    }

    .sari-sidebar-card {
        background: #fff;
        border: 1px solid #ece7df;
        box-shadow:
            0 13px 30px rgba(44, 39, 33, .052),
            0 3px 9px rgba(44, 39, 33, .026),
            inset 0 1px 0 rgba(255, 255, 255, .96);
    }

    .sari-tracking-row {
        position: relative;
        display: flex;
        gap: 12px;
    }

    .sari-tracking-row:not(:last-child)::after {
        content: "";
        position: absolute;
        left: 8px;
        top: 22px;
        width: 1px;
        height: 34px;
        background: #e8e1d5;
    }

    .sari-tracking-row.is-complete:not(:last-child)::after {
        background: #dfaa2d;
    }

    .sari-tracking-icon {
        width: 17px;
        height: 17px;
        flex: 0 0 17px;
        margin-top: 2px;
        border-radius: 9999px;
        border: 1.5px solid #d6d0c5;
        background: #fff;
        color: transparent;
        display: grid;
        place-items: center;
    }

    .sari-tracking-row.is-complete .sari-tracking-icon,
    .sari-tracking-row.is-current .sari-tracking-icon {
        border-color: var(--sari-gold);
        background: var(--sari-gold);
        color: #fff;
    }

    .sari-tracking-row.is-current .sari-tracking-icon {
        box-shadow: 0 0 0 4px rgba(217, 154, 24, .10);
    }

    .sari-fade-in {
        animation: sariFadeIn .35s ease both;
    }

    @keyframes sariFadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }


    .sari-summary-strip {
        background: #fff;
        border: 1px solid #ece7df;
        box-shadow:
            0 13px 30px rgba(44, 39, 33, .052),
            0 3px 9px rgba(44, 39, 33, .026),
            inset 0 1px 0 rgba(255, 255, 255, .96);
    }

    .sari-summary-item {
        position: relative;
        min-width: 115px;
    }

    .sari-summary-item + .sari-summary-item::before {
        content: "";
        position: absolute;
        left: 0;
        top: 50%;
        width: 1px;
        height: 28px;
        transform: translateY(-50%);
        background: #eee8dc;
    }

    .sari-search {
        outline: none;
        transition: .2s ease;
    }

    .sari-search:focus {
        border-color: #d9a33a;
        box-shadow: 0 0 0 4px rgba(217,154,24,.09);
    }

    .sari-filter.is-active {
        background: #e2a52b;
        color: #fff;
        box-shadow: 0 4px 12px rgba(201,137,0,.16);
    }

    .sari-product-strip {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .sari-product-thumb {
        width: 45px;
        height: 45px;
        flex: 0 0 45px;
        display: grid;
        place-items: center;
        border: 1px solid #eee8dc;
        border-radius: 12px;
        background: linear-gradient(145deg, #fffdf9, #f5f0e7);
        color: #9b958b;
        overflow: hidden;
    }

    .sari-product-thumb:nth-child(2) {
        background: linear-gradient(145deg, #fafafa, #ebe7df);
    }

    .sari-product-more {
        height: 45px;
        min-width: 42px;
        padding: 0 8px;
        display: grid;
        place-items: center;
        border: 1px dashed #d9d1c4;
        border-radius: 12px;
        background: #fffdf9;
        color: #8a8479;
        font-size: 10px;
        font-weight: 700;
    }

    .sari-price-box {
        border-left: 1px solid #eee8dc;
        padding-left: 22px;
        text-align: right;
    }

    .sari-live-dot {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: #42a66b;
        box-shadow: 0 0 0 5px rgba(66,166,107,.10);
        animation: sariPulse 1.8s ease-in-out infinite;
    }

    @keyframes sariPulse {
        0%, 100% { box-shadow: 0 0 0 4px rgba(66,166,107,.08); }
        50% { box-shadow: 0 0 0 7px rgba(66,166,107,.16); }
    }

    .sari-mini-map {
        position: relative;
        height: 158px;
        overflow: hidden;
        border: 1px solid #e9e3d9;
        border-radius: 16px;
        box-shadow:
            0 8px 20px rgba(44, 39, 33, .045),
            inset 0 1px 0 rgba(255, 255, 255, .90);
        background:
            linear-gradient(28deg, transparent 48%, rgba(222,214,199,.75) 49%, rgba(222,214,199,.75) 51%, transparent 52%),
            linear-gradient(112deg, transparent 48%, rgba(232,225,213,.9) 49%, rgba(232,225,213,.9) 51%, transparent 52%),
            linear-gradient(#fffdf8, #f7f3ea);
    }

    .sari-mini-map::before,
    .sari-mini-map::after {
        content: "";
        position: absolute;
        border-radius: 999px;
        border: 1px solid rgba(217,154,24,.15);
    }

    .sari-mini-map::before {
        width: 180px;
        height: 180px;
        left: 18%;
        top: -75px;
    }

    .sari-mini-map::after {
        width: 110px;
        height: 110px;
        right: -20px;
        bottom: -55px;
    }

    .sari-route {
        position: absolute;
        left: 23%;
        top: 53%;
        width: 54%;
        height: 2px;
        transform: rotate(-22deg);
        transform-origin: left center;
        background: repeating-linear-gradient(90deg, #dca42a 0 7px, transparent 7px 12px);
    }

    .sari-map-pin {
        position: absolute;
        z-index: 2;
        display: grid;
        place-items: center;
        width: 30px;
        height: 30px;
        border-radius: 999px;
        box-shadow: 0 7px 15px rgba(45,35,15,.14);
    }

    .sari-map-pin.courier {
        left: 48%;
        top: 40%;
        background: #191919;
        color: #e5ad32;
    }

    .sari-map-pin.home {
        right: 13%;
        bottom: 17%;
        background: #e1a52a;
        color: #fff;
    }

    .sari-map-label {
        position: absolute;
        z-index: 3;
        padding: 5px 7px;
        border: 1px solid #eee8dc;
        border-radius: 7px;
        background: rgba(255,255,255,.94);
        font-size: 8px;
        font-weight: 700;
        color: #6f6a61;
        box-shadow: 0 4px 10px rgba(45,35,15,.05);
    }

    .sari-map-label.courier {
        left: 41%;
        top: 15%;
    }

    .sari-map-label.home {
        right: 4%;
        bottom: 2%;
    }

    .sari-live-panel {
        border: 1px solid #ece6dc;
        border-radius: 16px;
        background: #fff;
        box-shadow:
            0 9px 22px rgba(44, 39, 33, .045),
            0 2px 7px rgba(44, 39, 33, .022),
            inset 0 1px 0 rgba(255, 255, 255, .96);
    }

    .sari-order-card.is-hidden {
        display: none;
    }

    .sari-empty-search {
        display: none;
    }

    .sari-delivered-box {
        border: 1px solid #e5eadf;
        background: #f8fbf5;
    }

    .sari-star-row {
        letter-spacing: 2px;
        color: #d6d0c6;
    }

    .sari-star-row span {
        cursor: pointer;
        transition: .15s ease;
    }

    .sari-star-row span:hover {
        color: #d99a18;
        transform: translateY(-1px);
    }

    @media (max-width: 900px) {
        .sari-sidebar {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .sari-step {
            width: 64px;
            flex-basis: 64px;
        }

        .sari-step-line,
        .sari-step-progress {
            left: 8%;
            right: 8%;
        }

        .sari-step-label {
            font-size: 10px;
        }
    }
</style>

<div class="sari-orders-page min-h-full">
    <div class="mx-auto w-full max-w-[1420px] px-4 py-5 sm:px-6 lg:px-8 lg:py-7">

        @if(session('success'))
            <div class="sari-fade-in mb-5 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                <span class="material-symbols-rounded text-[20px]">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- PREMIUM PAGE HEADING --}}
        <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-2">
                    <span class="h-1 w-7 rounded-full bg-[#d99a18]"></span>
                    <span class="text-[10px] font-bold uppercase tracking-[.2em] text-[#b87908]">SARI Purchase Center</span>
                </div>

                <h1 class="text-[28px] font-bold tracking-[-.035em] text-[#181818] sm:text-[34px]">
                    My Orders
                </h1>

                <p class="mt-1 text-sm text-[#77736b]">
                    Track purchases, deliveries, and order updates in real-time.
                </p>
            </div>

            <a href="{{ route('buyer.products') }}"
               class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-[#191919] px-5 text-sm font-semibold text-white transition hover:bg-black hover:-translate-y-0.5">
                <span class="material-symbols-rounded text-[19px]">shopping_bag</span>
                Continue Shopping
            </a>
        </div>

        {{-- ORDER SUMMARY --}}
        <div class="sari-summary-strip mb-5 grid grid-cols-2 overflow-hidden rounded-2xl sm:grid-cols-4">
            <div class="sari-summary-item flex items-center gap-3 px-4 py-4 sm:px-5">
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-[#fff5dc] text-[#c7890b]">
                    <span class="material-symbols-rounded text-[19px]">receipt_long</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[.1em] text-[#a09b91]">Total Orders</p>
                    <p class="mt-0.5 text-lg font-bold text-[#272521]">{{ $orders->count() }}</p>
                </div>
            </div>

            <div class="sari-summary-item flex items-center gap-3 px-4 py-4 sm:px-5">
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-[#fff7e8] text-[#c7890b]">
                    <span class="material-symbols-rounded text-[19px]">inventory_2</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[.1em] text-[#a09b91]">To Ship</p>
                    <p class="mt-0.5 text-lg font-bold text-[#272521]">{{ $toShipCount }}</p>
                </div>
            </div>

            <div class="sari-summary-item flex items-center gap-3 px-4 py-4 sm:px-5">
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-[#fff7e8] text-[#c7890b]">
                    <span class="material-symbols-rounded text-[19px]">local_shipping</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[.1em] text-[#a09b91]">In Transit</p>
                    <p class="mt-0.5 text-lg font-bold text-[#272521]">{{ $inTransitCount }}</p>
                </div>
            </div>

            <div class="sari-summary-item flex items-center gap-3 px-4 py-4 sm:px-5">
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-[#f1f8ef] text-[#4c9361]">
                    <span class="material-symbols-rounded text-[19px]">task_alt</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[.1em] text-[#a09b91]">Delivered</p>
                    <p class="mt-0.5 text-lg font-bold text-[#272521]">{{ $deliveredCount }}</p>
                </div>
            </div>
        </div>

        <div class="grid items-start gap-5 xl:grid-cols-[minmax(0,1fr)_310px]">

            {{-- MAIN ORDERS --}}
            <main class="min-w-0">
                {{-- SEARCH + ORDER FILTERS --}}
                <div class="sari-card mb-5 rounded-2xl p-3">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                        <div class="relative min-w-0 flex-1">
                            <span class="material-symbols-rounded pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[19px] text-[#aaa49a]">search</span>
                            <input id="orderSearch"
                                   type="search"
                                   placeholder="Search order number or status..."
                                   class="sari-search h-10 w-full rounded-xl border border-[#e9e2d6] bg-[#fffdf9] pl-10 pr-4 text-xs font-medium text-[#333] placeholder:text-[#aaa49a]">
                        </div>

                        <div class="flex flex-wrap gap-1.5" id="orderFilters">
                            <button type="button" data-filter="all" class="sari-filter is-active rounded-xl px-3.5 py-2 text-[11px] font-semibold transition">All <span class="opacity-70">{{ $orders->count() }}</span></button>
                            <button type="button" data-filter="to-ship" class="sari-filter rounded-xl px-3.5 py-2 text-[11px] font-medium text-[#77736b] transition hover:bg-[#faf7ef]">To Ship</button>
                            <button type="button" data-filter="in-transit" class="sari-filter rounded-xl px-3.5 py-2 text-[11px] font-medium text-[#77736b] transition hover:bg-[#faf7ef]">In Transit</button>
                            <button type="button" data-filter="delivered" class="sari-filter rounded-xl px-3.5 py-2 text-[11px] font-medium text-[#77736b] transition hover:bg-[#faf7ef]">Delivered</button>
                            <button type="button" data-filter="cancelled" class="sari-filter rounded-xl px-3.5 py-2 text-[11px] font-medium text-[#77736b] transition hover:bg-[#faf7ef]">Cancelled</button>
                        </div>
                    </div>
                </div>

                <section class="space-y-5">
                    @forelse($orders as $order)
                        @php
                            $currentStep = $statusIndex($order->status);
                        @endphp

                        @php
                            $filterGroup = match (true) {
                                $order->status === 'delivered' => 'delivered',
                                $order->status === 'cancelled' => 'cancelled',
                                in_array($order->status, ['courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit', 'arrived_buyer']) => 'in-transit',
                                default => 'to-ship',
                            };

                            $displayTotal = $order->total_amount ?? $order->total ?? null;
                        @endphp

                        <article class="sari-card sari-order-card sari-fade-in overflow-hidden rounded-[20px]"
                                 data-status="{{ $filterGroup }}"
                                 data-search="{{ strtolower($order->order_number . ' ' . $order->statusLabel()) }}">

                            {{-- ORDER TOP --}}
                            <div class="border-b border-[#eee8dc] px-5 py-5 sm:px-6">
                                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                                    <div class="flex min-w-0 gap-4">
                                        <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-[#fff5dc] text-[#c7890b]">
                                            <span class="material-symbols-rounded text-[27px]">shopping_bag</span>
                                        </div>

                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h2 class="truncate text-[17px] font-bold tracking-[-.01em] text-[#202020]">
                                                    {{ $order->order_number }}
                                                </h2>

                                                <button type="button"
                                                        class="copy-order-number rounded-lg border border-[#e7e0d5] px-2 py-1 text-[11px] font-medium text-[#77736b] transition hover:border-[#d7b25e] hover:bg-[#fffaf0]"
                                                        data-order-number="{{ $order->order_number }}">
                                                    Copy
                                                </button>

                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#fff4d9] px-2.5 py-1 text-[11px] font-semibold text-[#ad7204]">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-[#d99a18]"></span>
                                                    {{ $order->statusLabel() }}
                                                </span>
                                            </div>

                                            <p class="mt-1 text-[11px] text-[#929087]">
                                                Placed {{ $order->created_at?->format('M d, Y • h:i A') }}
                                            </p>

                                            <p class="mt-3 text-sm font-semibold text-[#33312d]">
                                                Your order is being processed.
                                            </p>

                                            <p class="mt-0.5 text-xs text-[#77736b]">
                                                Expected delivery: May 24, 2026
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-5 lg:pr-1">
                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-[.12em] text-[#a09b91]">Delivery</p>
                                            <p class="mt-1 text-[15px] font-bold text-[#c7890b]">May 24</p>
                                            <p class="text-[11px] text-[#817d75]">End of day</p>
                                        </div>

                                        <div class="sari-mini-divider"></div>

                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-[.12em] text-[#a09b91]">Payment</p>
                                            <p class="mt-1 text-sm font-bold uppercase text-[#35332f]">{{ $order->payment_method }}</p>
                                        </div>

                                        <button type="button"
                                                class="hidden h-9 w-9 place-items-center rounded-lg text-[#8e887d] transition hover:bg-[#faf7ef] hover:text-[#333] sm:grid"
                                                aria-label="More order options">
                                            <span class="material-symbols-rounded text-[21px]">more_vert</span>
                                        </button>
                                    </div>
                                </div>

                            {{-- PRODUCT PREVIEW + ORDER TOTAL --}}
                            <div class="mt-5 flex flex-col gap-4 border-t border-dashed border-[#eee8dc] pt-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <p class="mb-2 text-[9px] font-bold uppercase tracking-[.14em] text-[#aaa49a]">Items in this order</p>
                                    <div class="sari-product-strip">
                                        <div class="sari-product-thumb">
                                            <span class="material-symbols-rounded text-[22px]">shopping_bag</span>
                                        </div>
                                        <div class="sari-product-thumb">
                                            <span class="material-symbols-rounded text-[22px]">checkroom</span>
                                        </div>
                                        <div class="sari-product-more">2+ items</div>
                                        <div class="ml-1 min-w-0">
                                            <p class="truncate text-xs font-semibold text-[#4b4842]">Your selected products</p>
                                            <p class="mt-0.5 text-[10px] text-[#9a958c]">View details to see all items</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="sari-price-box">
                                    <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#aaa49a]">Order Total</p>
                                    @if($displayTotal !== null)
                                        <p class="mt-1 text-lg font-bold text-[#26231f]">₱{{ number_format((float) $displayTotal, 2) }}</p>
                                    @else
                                        <p class="mt-1 text-sm font-semibold text-[#77736b]">See order details</p>
                                    @endif
                                    <p class="mt-0.5 text-[10px] text-[#9a958c]">{{ $order->payment_method }}</p>
                                </div>
                            </div>
                        </div>

                            {{-- TRACKING STEPS --}}
                            <div class="overflow-x-auto px-4 py-7 sm:px-6">
                                <div class="relative mx-auto flex min-w-[390px] justify-between">
                                    <div class="sari-step-line"></div>

                                    <div class="sari-step-progress"
                                         style="width: {{ ($currentStep / 4) * 80 }}%;">
                                    </div>

                                    @foreach($statusSteps as $index => $step)
                                        <div class="sari-step flex flex-col items-center">
                                            <div class="sari-step-dot {{ $index <= $currentStep ? 'is-complete' : '' }} {{ $index === $currentStep ? 'is-current' : '' }}">
                                                <span class="material-symbols-rounded text-[19px]">
                                                    {{ $index <= $currentStep ? 'check' : $step['icon'] }}
                                                </span>
                                            </div>

                                            <p class="sari-step-label mt-3 text-center text-[11px] font-semibold leading-tight {{ $index === $currentStep ? 'text-[#c7890b]' : 'text-[#69655d]' }}">
                                                {{ $step['label'] }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- ORDER INFORMATION --}}
                            <div class="grid border-t border-[#eee8dc] lg:grid-cols-3">

                                <div class="px-5 py-5 sm:px-6">
                                    <p class="text-[10px] font-bold uppercase tracking-[.14em] text-[#a09b91]">Latest Update</p>

                                    <div class="mt-3 flex gap-3">
                                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-[#d99a18] ring-4 ring-[#fff5dc]"></span>

                                        <div>
                                            <p class="text-xs font-bold text-[#34322f]">May 22, 2026 • 08:40 AM</p>
                                            <p class="mt-1 text-xs leading-5 text-[#77736b]">
                                                Your parcel has been accepted by the courier.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-[#eee8dc] px-5 py-5 sm:px-6 lg:border-l lg:border-t-0">
                                    <p class="text-[10px] font-bold uppercase tracking-[.14em] text-[#a09b91]">Courier</p>

                                    <div class="mt-3 flex items-center gap-3">
                                        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#fff3d8] text-xs font-bold text-[#c7890b] ring-4 ring-[#fffaf0]">
                                            JD
                                        </div>

                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-[#282622]">Juan Dela Cruz</p>
                                            <p class="mt-0.5 text-[11px] text-[#817d75]">
                                                <span class="font-bold text-[#d99a18]">★</span>
                                                4.9 • 120 deliveries
                                            </p>
                                        </div>

                                        <div class="ml-auto flex gap-1.5">
                                            <button type="button" class="grid h-8 w-8 place-items-center rounded-lg border border-[#e8e0d3] text-[#b47b0b] transition hover:bg-[#fff8e9]" aria-label="Call courier">
                                                <span class="material-symbols-rounded text-[18px]">call</span>
                                            </button>
                                            <button type="button" class="grid h-8 w-8 place-items-center rounded-lg border border-[#e8e0d3] text-[#b47b0b] transition hover:bg-[#fff8e9]" aria-label="Chat with courier">
                                                <span class="material-symbols-rounded text-[18px]">chat</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-[#eee8dc] px-5 py-5 sm:px-6 lg:border-l lg:border-t-0">
                                    <p class="text-[10px] font-bold uppercase tracking-[.14em] text-[#a09b91]">Delivery Address</p>

                                    <div class="mt-3 flex gap-2">
                                        <span class="material-symbols-rounded shrink-0 text-[19px] text-[#c7890b]">location_on</span>
                                        <p class="text-xs leading-5 text-[#625f58]">
                                            123 Rizal St., Brgy. San Isidro<br>
                                            Santa Cruz, Laguna 4000
                                        </p>
                                    </div>

                                    <button type="button" class="mt-2 text-[11px] font-bold text-[#bd7e06] hover:underline">
                                        View Map →
                                    </button>
                                </div>
                            </div>

                            {{-- ACTIONS --}}
                            <div class="grid gap-2.5 border-t border-[#eee8dc] bg-[#fffefa] p-4 sm:grid-cols-3 sm:p-5">
                                <a href="{{ route('buyer.messages', ['seller' => $order->seller_account_id]) }}"
                                   class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[#dca62d] text-xs font-bold text-[#b87905] transition hover:bg-[#fff8e9]">
                                    <span class="material-symbols-rounded text-[18px]">chat</span>
                                    Message Seller
                                </a>

                                <a href="{{ route('buyer.orders.show', $order) }}"
                                   class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[#dca62d] text-xs font-bold text-[#b87905] transition hover:bg-[#fff8e9]">
                                    <span class="material-symbols-rounded text-[18px]">receipt_long</span>
                                    View Details
                                </a>

                                <button type="button"
                                        class="track-order sari-gold-btn inline-flex h-10 items-center justify-center gap-2 rounded-xl text-xs font-bold text-white transition hover:-translate-y-0.5"
                                        data-order-id="{{ $order->id ?? '' }}">
                                    <span class="material-symbols-rounded text-[18px]">location_searching</span>
                                    Track Order
                                </button>
                            </div>

                            @if($order->status === 'delivered')
                                <div class="border-t border-[#eee8dc] bg-[#fffefa] px-4 pb-4 sm:px-5 sm:pb-5">
                                    @if($order->returnRequest)
                                        <div class="rounded-xl border border-[#eadfc9] bg-[#fffaf2] px-4 py-3">
                                            <p class="text-[9px] font-bold text-[#9a680b]">Return request: {{ ucwords(str_replace('_',' ',$order->returnRequest->status)) }}</p>
                                            <p class="mt-1 text-[8px] leading-4 text-[#7d7468]">{{ $order->returnRequest->seller_response ?: 'Waiting for Seller review.' }}</p>
                                        </div>
                                    @else
                                        <details class="group rounded-xl border border-[#e8dfd0] bg-white">
                                            <summary class="cursor-pointer list-none px-4 py-3 text-[9px] font-bold text-[#a96f06]">Request Return / Refund</summary>
                                            <form method="POST" action="{{ route('buyer.orders.return-request',$order) }}" class="grid gap-2 border-t border-[#eee8dc] p-4 sm:grid-cols-[180px_1fr_auto]">
                                                @csrf
                                                <select name="reason" required class="h-10 rounded-xl border border-[#e4ddd3] px-3 text-[8px]">
                                                    <option value="Damaged item">Damaged item</option>
                                                    <option value="Wrong item">Wrong item</option>
                                                    <option value="Missing item">Missing item</option>
                                                    <option value="Item not as described">Item not as described</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                                <input name="details" maxlength="1200" placeholder="Describe the issue..." class="h-10 rounded-xl border border-[#e4ddd3] px-3 text-[8px]">
                                                <button class="h-10 rounded-xl bg-[#202124] px-4 text-[8px] font-semibold text-white">Submit Request</button>
                                            </form>
                                        </details>
                                    @endif
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="sari-card rounded-[20px] p-12 text-center">
                            <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-[#fff5dc] text-[#c7890b]">
                                <span class="material-symbols-rounded text-[30px]">shopping_bag</span>
                            </div>
                            <h2 class="mt-4 text-lg font-bold text-[#33312d]">No Orders Yet</h2>
                            <p class="mt-1 text-sm text-[#77736b]">Your purchased products will appear here.</p>
                            <a href="{{ route('buyer.products') }}"
                               class="sari-gold-btn mt-5 inline-flex h-10 items-center rounded-xl px-5 text-sm font-bold text-white">
                                Start Shopping
                            </a>
                        </div>
                    @endforelse
                </section>

                {{-- QUICK FEEDBACK --}}
                @if($featuredOrder && $featuredOrder->status === 'delivered')
                    <section class="sari-delivered-box sari-soft-panel-sm mt-5 rounded-[18px] p-5">
                        <div class="flex items-start gap-3">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white text-[#d99a18] shadow-sm">
                                <span class="material-symbols-rounded text-[21px]">star</span>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[.13em] text-[#8e967f]">Order Complete</p>
                                <h3 class="mt-1 text-sm font-bold text-[#34382f]">How was your experience?</h3>
                                <div class="sari-star-row mt-2 text-lg" aria-label="Rate your order">
                                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

                {{-- BUYER PROTECTION --}}
                <section class="sari-soft-panel-sm mt-5 flex flex-col gap-4 rounded-[18px] border border-[#f0dfb7] bg-[#fffaf0] p-5 sm:flex-row sm:items-center">
                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white text-[#d99a18] shadow-sm">
                        <span class="material-symbols-rounded">verified_user</span>
                    </div>

                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-[#282622]">Buyer Protection</h3>
                        <p class="mt-0.5 text-xs text-[#716d65]">
                            Your order is protected by SARI Buyer Protection.
                        </p>
                    </div>

                    <a href="#" class="text-xs font-bold text-[#b87905] hover:underline">
                        Learn More →
                    </a>
                </section>
            </main>

            {{-- RIGHT SIDEBAR --}}
            <aside class="sari-sidebar space-y-4">

                {{-- ORDER TRACKING --}}
                <div class="sari-sidebar-card overflow-hidden rounded-[20px]">
                    <div class="border-b border-[#f0ebe2] px-5 py-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-[16px] font-bold text-[#25231f]">Order Tracking</h2>
                                <p class="mt-1 text-[11px] text-[#89847b]">Live updates on your delivery</p>
                            </div>

                            <div class="grid h-10 w-10 place-items-center rounded-xl bg-[#fff7e4] text-[#d99a18]">
                                <span class="material-symbols-rounded text-[22px]">location_on</span>
                            </div>
                        </div>
                    </div>

                    @if($featuredOrder)
                        <div class="px-5 py-5">
                            <div class="mb-4 rounded-xl bg-[#fffaf0] px-3 py-2.5">
                                <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#a39c90]">Current Order</p>
                                <p class="mt-1 truncate text-xs font-bold text-[#34312c]">{{ $featuredOrder->order_number }}</p>
                            </div>

                            <div class="space-y-3">
                                @foreach($statusSteps as $index => $step)
                                    <div class="sari-tracking-row {{ $index <= $featuredStep ? 'is-complete' : '' }} {{ $index === $featuredStep ? 'is-current' : '' }}">
                                        <div class="sari-tracking-icon">
                                            <span class="material-symbols-rounded text-[12px]">check</span>
                                        </div>

                                        <div class="min-w-0 pb-2">
                                            <p class="text-xs font-semibold {{ $index === $featuredStep ? 'text-[#a96e03]' : ($index < $featuredStep ? 'text-[#39362f]' : 'text-[#8d887f]') }}">
                                                {{ $step['label'] }}
                                            </p>

                                            @if($index <= $featuredStep)
                                                <p class="mt-0.5 text-[9px] text-[#9a958c]">
                                                    {{ $index === $featuredStep ? 'Current status' : 'Completed' }}
                                                </p>
                                            @else
                                                <p class="mt-0.5 text-[9px] text-[#b1aca3]">Pending</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- LIVE DELIVERY MAP --}}
                            @if($featuredStep >= 2 && $featuredStep < 4)
                                <div class="sari-live-panel mt-5 p-3">
                                    <div class="mb-3 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="sari-live-dot"></span>
                                            <span class="text-[10px] font-bold uppercase tracking-[.13em] text-[#5f5a51]">Live Delivery</span>
                                        </div>
                                        <span class="text-[9px] font-semibold text-[#9b958b]">Updated now</span>
                                    </div>

                                    <div class="sari-mini-map">
                                        <div class="sari-route"></div>

                                        <div class="sari-map-label courier">Courier</div>
                                        <div class="sari-map-label home">Your location</div>

                                        <div class="sari-map-pin courier">
                                            <span class="material-symbols-rounded text-[16px]">two_wheeler</span>
                                        </div>

                                        <div class="sari-map-pin home">
                                            <span class="material-symbols-rounded text-[16px]">home</span>
                                        </div>
                                    </div>

                                    <div class="mt-3 flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-bold text-[#34312d]">Juan is on the way</p>
                                            <p class="mt-0.5 text-[10px] text-[#8e887f]">Estimated arrival today</p>
                                        </div>
                                        <a href="#" class="text-[10px] font-bold text-[#b87905] hover:underline">Full Tracking →</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="px-5 py-8 text-center">
                            <span class="material-symbols-rounded text-3xl text-[#d4b36a]">local_shipping</span>
                            <p class="mt-2 text-xs text-[#77736b]">Your tracking updates will appear here.</p>
                        </div>
                    @endif
                </div>

                {{-- HELP --}}
                <div class="sari-sidebar-card overflow-hidden rounded-[20px]">
                    <div class="px-5 py-5">
                        <div class="flex items-center gap-3">
                            <div class="grid h-11 w-11 place-items-center rounded-xl bg-[#fff5dc] text-[#c7890b]">
                                <span class="material-symbols-rounded text-[23px]">headset_mic</span>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#292722]">Need Help?</h3>
                                <p class="mt-0.5 text-[10px] text-[#89847b]">We're here for you!</p>
                            </div>
                        </div>

                        <div class="mt-4 divide-y divide-[#f0ebe2]">
                            <a href="#" class="flex items-center justify-between py-3 text-xs font-semibold text-[#555149] transition hover:text-[#b87905]">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-rounded text-[17px] text-[#9a9489]">chat_bubble_outline</span>
                                    Chat with Support
                                </span>
                                <span class="material-symbols-rounded text-[17px] text-[#aaa49a]">chevron_right</span>
                            </a>

                            <a href="#" class="flex items-center justify-between py-3 text-xs font-semibold text-[#555149] transition hover:text-[#b87905]">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-rounded text-[17px] text-[#9a9489]">assignment_return</span>
                                    Return & Refund Policy
                                </span>
                                <span class="material-symbols-rounded text-[17px] text-[#aaa49a]">chevron_right</span>
                            </a>

                            <a href="#" class="flex items-center justify-between py-3 text-xs font-semibold text-[#555149] transition hover:text-[#b87905]">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-rounded text-[17px] text-[#9a9489]">report_problem</span>
                                    Report an Issue
                                </span>
                                <span class="material-symbols-rounded text-[17px] text-[#aaa49a]">chevron_right</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- VOUCHER --}}
                <div class="relative overflow-hidden rounded-[20px] bg-[#191919] p-5 text-white shadow-[0_12px_30px_rgba(0,0,0,.10)]">
                    <div class="absolute -right-7 -bottom-9 h-28 w-28 rounded-full border border-[#dca62d]/30"></div>
                    <div class="absolute -right-2 -bottom-4 h-14 w-14 rounded-full border border-[#dca62d]/20"></div>

                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[.16em] text-[#e6b849]">Special Offer</p>
                                <h3 class="mt-2 text-[17px] font-bold">Get 10% Off</h3>
                                <p class="mt-0.5 text-[11px] text-white/55">on your next order.</p>
                            </div>

                            <span class="material-symbols-rounded text-[34px] text-[#e6b849]">confirmation_number</span>
                        </div>

                        <a href="#" class="mt-4 inline-flex h-9 items-center rounded-lg bg-[#e1a62b] px-3.5 text-[11px] font-bold text-white transition hover:bg-[#c88b12]">
                            View Vouchers
                            <span class="material-symbols-rounded ml-1 text-[15px]">arrow_forward</span>
                        </a>
                    </div>
                </div>

            </aside>
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/buyer-orders.js')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Copy order number
            document.querySelectorAll('.copy-order-number').forEach(button => {
                button.addEventListener('click', async () => {
                    const number = button.dataset.orderNumber || '';
                    if (!number) return;

                    try {
                        await navigator.clipboard.writeText(number);
                        const original = button.textContent;
                        button.textContent = '✓ Copied';
                        button.classList.add('bg-[#fff8e9]', 'text-[#b87905]');

                        setTimeout(() => {
                            button.textContent = original;
                            button.classList.remove('bg-[#fff8e9]', 'text-[#b87905]');
                        }, 1200);
                    } catch (error) {}
                });
            });

            // Client-side order search + filtering
            const search = document.getElementById('orderSearch');
            const filters = document.querySelectorAll('.sari-filter');
            const cards = document.querySelectorAll('.sari-order-card');

            let activeFilter = 'all';

            const applyOrderFilter = () => {
                const query = (search?.value || '').trim().toLowerCase();

                cards.forEach(card => {
                    const matchesFilter = activeFilter === 'all' || card.dataset.status === activeFilter;
                    const matchesSearch = !query || (card.dataset.search || '').includes(query);

                    card.classList.toggle('is-hidden', !(matchesFilter && matchesSearch));
                });
            };

            filters.forEach(button => {
                button.addEventListener('click', () => {
                    activeFilter = button.dataset.filter || 'all';

                    filters.forEach(item => {
                        item.classList.toggle('is-active', item === button);
                        if (item !== button) {
                            item.classList.add('text-[#77736b]');
                        } else {
                            item.classList.remove('text-[#77736b]');
                        }
                    });

                    applyOrderFilter();
                });
            });

            search?.addEventListener('input', applyOrderFilter);
        });
    </script>
@endpush

@endsection
