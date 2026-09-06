@extends('layouts.seller')

@section('title', 'Order Management — SARI Seller')
@section('page-title', 'Order Management')

@section('content')
@php
    $statusTone = fn (string $status) => match ($status) {
        'new' => 'border-[#eadfc9] bg-[#fff9ef] text-[#a8731f]',
        'preparing' => 'border-[#d5e2ed] bg-[#f3f8fc] text-[#5d7f9d]',
        'ready_for_pickup' => 'border-[#e5d9b9] bg-[#fff9ec] text-[#a67820]',
        'courier_accepted', 'heading_pickup', 'arrived_pickup' => 'border-[#ddd5e7] bg-[#f7f4f9] text-[#75618a]',
        'in_transit', 'arrived_buyer' => 'border-[#cfdeea] bg-[#f1f7fb] text-[#3c6e91]',
        'delivered' => 'border-[#cfe4d7] bg-[#f1f8f4] text-[#4f7d63]',
        'cancelled' => 'border-[#efcece] bg-[#fff5f5] text-[#a65353]',
        default => 'border-[#e5dfd7] bg-[#f7f5f2] text-[#756d63]',
    };

    $progressValue = fn (string $status) => match ($status) {
        'new' => 10,
        'preparing' => 25,
        'ready_for_pickup' => 40,
        'courier_accepted' => 52,
        'heading_pickup' => 60,
        'arrived_pickup' => 68,
        'in_transit' => 80,
        'arrived_buyer' => 92,
        'delivered' => 100,
        default => 0,
    };

    $totalOrders = $orders->count();
    $sellerOrderRealtimeToken = (string) ($seller->realtime_token ?? '');
    $sellerOrderLatestUpdatedAt = $orders->max('updated_at');
    $sellerOrderRevision = $sellerOrderLatestUpdatedAt
        ? \Illuminate\Support\Carbon::parse($sellerOrderLatestUpdatedAt)->format('Y-m-d H:i:s')
        : '';
@endphp

<style>
    .seller-orders-page {
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        font-weight: 400;
    }

    .seller-orders-page button,
    .seller-orders-page input,
    .seller-orders-page select,
    .seller-orders-page a {
        font-family: inherit;
    }

    .seller-order-row {
        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            transform .18s ease,
            background-color .18s ease;
    }

    .seller-order-row:hover {
        transform: translateY(-1px);
        border-color: #dfd1bd;
        background: #fffefa;
        box-shadow: 0 12px 26px rgba(48, 37, 24, .045);
    }

    .seller-order-modal-panel {
        animation: sellerOrderModalIn .14s ease-out both;
    }

    @keyframes sellerOrderModalIn {
        from {
            opacity: 0;
            transform: translateY(5px) scale(.995);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .seller-order-progress {
        background: linear-gradient(90deg, #d59618, #e4ae3f);
    }

    .seller-order-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #d8cdbf transparent;
    }

    .seller-order-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .seller-order-scrollbar::-webkit-scrollbar-thumb {
        background: #d8cdbf;
        border-radius: 999px;
    }


    .seller-order-image-frame {
        position: relative;
        isolation: isolate;
        background: #f1eee9;
    }

    .seller-order-image-frame::before {
        content: "";
        position: absolute;
        inset: 0;
        z-index: 1;
        background: #ebe7e1;
        animation: sellerOrderSoftPulse 1.15s ease-in-out infinite;
        opacity: 1;
        transition: opacity .16s ease;
        pointer-events: none;
    }

    .seller-order-image-frame.is-loaded::before {
        opacity: 0;
        animation: none;
    }

    .seller-order-image-frame > img {
        opacity: 0;
        transition: opacity .16s ease;
    }

    .seller-order-image-frame.is-loaded > img {
        opacity: 1;
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPLE SELLER ORDERS SKELETON
    |--------------------------------------------------------------------------
    | No shimmer / glossy sweep. Only a soft opacity pulse.
    */
    .seller-orders-stage {
        position: relative;
    }

    .seller-orders-skeleton {
        display: block;
    }

    .seller-orders-content {
        display: none;
    }

    .seller-orders-stage.is-ready .seller-orders-skeleton {
        display: none;
    }

    .seller-orders-stage.is-ready .seller-orders-content {
        display: block;
    }

    .seller-orders-skeleton-block {
        display: block;
        border-radius: 10px;
        background: #e9e5df;
        animation: sellerOrderSoftPulse 1.15s ease-in-out infinite;
    }

    @keyframes sellerOrderSoftPulse {
        0%, 100% {
            opacity: .56;
        }
        50% {
            opacity: .94;
        }
    }

    .seller-orders-skeleton-card {
        border: 1px solid #eee8e1;
        background: #fff;
    }


    .seller-order-live-pulse {
        box-shadow: 0 0 0 0 rgba(79,125,99,.24);
        animation: sellerOrderLivePulse 2s ease-out infinite;
    }

    @keyframes sellerOrderLivePulse {
        0% { box-shadow: 0 0 0 0 rgba(79,125,99,.24); }
        70%, 100% { box-shadow: 0 0 0 7px rgba(79,125,99,0); }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-order-modal-panel,
        .seller-order-row,
        .seller-order-image-frame::before,
        .seller-order-live-pulse,
        .seller-orders-skeleton-block {
            animation: none !important;
            transition: none !important;
        }
    }
</style>

<div class="seller-orders-page mx-auto w-full max-w-[1800px]">
<div id="sellerOrdersStage" class="seller-orders-stage">

    {{-- =========================================================
        SIMPLE SKELETON — NO SHIMMER
    ========================================================== --}}
    <div id="sellerOrdersSkeleton" class="seller-orders-skeleton" aria-hidden="true">
        {{-- Header skeleton --}}
        <section class="flex flex-col gap-4 px-1 pt-1 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex items-center gap-3">
                <span class="seller-orders-skeleton-block h-12 w-12 shrink-0 rounded-[14px]"></span>

                <div>
                    <span class="seller-orders-skeleton-block h-8 w-[310px] max-w-[70vw]"></span>
                    <span class="seller-orders-skeleton-block mt-2.5 h-3 w-[430px] max-w-[78vw]"></span>
                </div>
            </div>

            <div class="flex gap-2">
                <span class="seller-orders-skeleton-block h-11 w-[118px]"></span>
                <span class="seller-orders-skeleton-block h-11 w-[132px]"></span>
            </div>
        </section>

        {{-- Summary skeleton --}}
        <section class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-5">
            @for ($i = 0; $i < 5; $i++)
                <div class="seller-orders-skeleton-card rounded-[17px] p-4 sm:p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <span class="seller-orders-skeleton-block h-3 w-[90px]"></span>
                            <span class="seller-orders-skeleton-block mt-3 h-7 w-[46px]"></span>
                            <span class="seller-orders-skeleton-block mt-4 h-2.5 w-[120px] max-w-full"></span>
                        </div>

                        <span class="seller-orders-skeleton-block h-11 w-11 rounded-[12px]"></span>
                    </div>
                </div>
            @endfor
        </section>

        {{-- Orders workspace skeleton --}}
        <section class="mt-4 overflow-hidden rounded-[20px] border border-[#e9e2d9] bg-white">
            <div class="flex flex-col gap-4 border-b border-[#eee8df] px-5 py-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <span class="seller-orders-skeleton-block h-5 w-[140px]"></span>
                    <span class="seller-orders-skeleton-block mt-2 h-3 w-[390px] max-w-[75vw]"></span>
                    <span class="seller-orders-skeleton-block mt-3 h-2.5 w-[120px]"></span>
                </div>

                <div class="flex gap-2">
                    <span class="seller-orders-skeleton-block h-11 w-[310px] max-w-[55vw]"></span>
                    <span class="seller-orders-skeleton-block h-11 w-[165px]"></span>
                </div>
            </div>

            <div class="hidden grid-cols-[minmax(260px,1.4fr)_minmax(180px,.85fr)_150px_130px_160px_72px] gap-4 border-b border-[#eee8df] px-5 py-3 lg:grid">
                @for ($i = 0; $i < 6; $i++)
                    <span class="seller-orders-skeleton-block h-3 w-[76px]"></span>
                @endfor
            </div>

            <div class="space-y-2.5 p-3 sm:p-4">
                @for ($i = 0; $i < 4; $i++)
                    <div class="seller-orders-skeleton-card rounded-[16px] p-3.5">
                        <div class="grid gap-3 lg:grid-cols-[minmax(260px,1.4fr)_minmax(180px,.85fr)_150px_130px_160px_72px] lg:items-center lg:gap-4">
                            <div class="flex items-center gap-3">
                                <span class="seller-orders-skeleton-block h-[74px] w-[74px] shrink-0 rounded-[12px]"></span>

                                <div class="min-w-0 flex-1">
                                    <span class="seller-orders-skeleton-block h-3.5 w-[150px]"></span>
                                    <span class="seller-orders-skeleton-block mt-2.5 h-3 w-[112px]"></span>
                                    <span class="seller-orders-skeleton-block mt-2 h-2.5 w-[95px]"></span>
                                </div>
                            </div>

                            <div>
                                <span class="seller-orders-skeleton-block h-3 w-[110px]"></span>
                                <span class="seller-orders-skeleton-block mt-2 h-2.5 w-[145px]"></span>
                            </div>

                            <span class="seller-orders-skeleton-block h-7 w-[88px] rounded-full"></span>
                            <span class="seller-orders-skeleton-block h-3.5 w-[74px]"></span>
                            <div>
                                <span class="seller-orders-skeleton-block h-3 w-[88px]"></span>
                                <span class="seller-orders-skeleton-block mt-2 h-2.5 w-[58px]"></span>
                            </div>
                            <span class="seller-orders-skeleton-block h-10 w-10 rounded-[11px]"></span>
                        </div>
                    </div>
                @endfor
            </div>
        </section>
    </div>

    <div id="sellerOrdersContent" class="seller-orders-content">


    @if (session('success'))
        <div class="mb-4 flex items-start gap-3 rounded-[15px] border border-[#cfe4d7] bg-[#f3faf5] px-4 py-3.5 text-[#4f7d63]">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-[10px] border border-[#d5e7dc] bg-white">
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="m8 12 2.5 2.5L16 9"></path>
                </svg>
            </span>
            <div>
                <p class="text-[11px] font-medium">Order updated</p>
                <p class="mt-1 text-[9px] leading-4 text-[#6f8778]">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 flex items-start gap-3 rounded-[15px] border border-[#efcece] bg-[#fff6f6] px-4 py-3.5 text-[#a65353]">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-[10px] border border-[#efdada] bg-white">
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 3 3 20h18L12 3Z"></path>
                    <path d="M12 9v5"></path>
                    <path d="M12 17h.01"></path>
                </svg>
            </span>
            <div>
                <p class="text-[11px] font-medium">Action unavailable</p>
                <p class="mt-1 text-[9px] leading-4 text-[#906363]">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif

    {{-- =========================================================
        HEADER — LARGE / LIGHT / NO OUTER CARD
    ========================================================== --}}
    <section class="flex flex-col gap-4 px-1 pt-1 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-[14px] border border-[#eadfc9] bg-[#fffaf2] text-[#bd8011] shadow-[0_4px_14px_rgba(173,117,16,.05)]">
                    <svg viewBox="0 0 24 24" class="h-[21px] w-[21px]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M6 5h12v15H6z"></path>
                        <path d="M9 5V3h6v2"></path>
                        <path d="M9 10h6"></path>
                        <path d="M9 14h6"></path>
                    </svg>
                </span>

                <div>
                    <h1 class="text-[32px] font-semibold leading-none tracking-[-.045em] sm:text-[36px]">
                        <span class="text-[#1f1b17]">Seller Order</span>
                        <span class="text-[#d39116]">Management</span>
                    </h1>
                    <p class="mt-2 text-[10.5px] font-normal text-[#8c8379]">
                        Track, prepare, and fulfill Buyer orders from one clean workspace.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a
                href="{{ route('seller.buyer-messages') }}"
                wire:navigate
                class="inline-flex h-11 items-center gap-2.5 rounded-[11px] border border-[#e4ddd3] bg-white px-4 text-[9px] font-medium text-[#62594f] shadow-[0_3px_10px_rgba(44,34,23,.025)] transition hover:border-[#d4c1a3] hover:bg-[#fffdf8] hover:text-[#996815]"
            >
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 11.5a7.5 7.5 0 0 1-7.5 7.5H9l-4 2v-4A7.5 7.5 0 1 1 21 11.5Z"></path>
                </svg>
                Buyer Inbox
            </a>

            <a
                href="{{ route('seller.reviews') }}"
                wire:navigate
                class="inline-flex h-11 items-center gap-2.5 rounded-[11px] border border-[#e4ddd3] bg-white px-4 text-[9px] font-medium text-[#62594f] shadow-[0_3px_10px_rgba(44,34,23,.025)] transition hover:border-[#d4c1a3] hover:bg-[#fffdf8] hover:text-[#996815]"
            >
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="m12 3 2.7 5.5 6 .9-4.3 4.2 1 5.9L12 16.7 6.6 19.5l1-5.9-4.3-4.2 6-.9L12 3Z"></path>
                </svg>
                Product Reviews
            </a>
        </div>
    </section>

    <div
        id="sellerOrdersDynamicRoot"
        data-revision="{{ $sellerOrderRevision }}"
        data-realtime-channel="{{ $sellerOrderRealtimeToken ? 'sari.seller.' . $sellerOrderRealtimeToken : '' }}"
    >
    {{-- =========================================================
        SUMMARY CARDS — ARCHIVE-LIKE LAYOUT
    ========================================================== --}}
    <section class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-5">
        @php
            $summaryCards = [
                [
                    'label' => 'Total Orders',
                    'value' => $totalOrders,
                    'sub' => 'All seller order records',
                    'border' => 'border-[#d8e4f0]',
                    'iconBg' => 'bg-[#f0f6fb]',
                    'iconText' => 'text-[#4e80aa]',
                    'icon' => 'history',
                ],
                [
                    'label' => 'New Orders',
                    'value' => $stats['new'],
                    'sub' => 'Awaiting preparation',
                    'border' => 'border-[#eadbbd]',
                    'iconBg' => 'bg-[#fff7e9]',
                    'iconText' => 'text-[#b77c16]',
                    'icon' => 'bag',
                ],
                [
                    'label' => 'Preparing',
                    'value' => $stats['preparing'],
                    'sub' => 'Currently being packed',
                    'border' => 'border-[#dce7ef]',
                    'iconBg' => 'bg-[#f2f8fc]',
                    'iconText' => 'text-[#5a7d99]',
                    'icon' => 'box',
                ],
                [
                    'label' => 'With Courier',
                    'value' => $stats['courier'],
                    'sub' => 'Pickup or delivery flow',
                    'border' => 'border-[#ddd8e8]',
                    'iconBg' => 'bg-[#f6f3f9]',
                    'iconText' => 'text-[#75618a]',
                    'icon' => 'truck',
                ],
                [
                    'label' => 'Delivered',
                    'value' => $stats['delivered'],
                    'sub' => 'Completed orders',
                    'border' => 'border-[#d4e6db]',
                    'iconBg' => 'bg-[#f1f8f4]',
                    'iconText' => 'text-[#4f7d63]',
                    'icon' => 'check',
                ],
            ];
        @endphp

        @foreach ($summaryCards as $card)
            <div class="rounded-[17px] border {{ $card['border'] }} bg-white px-4 py-5 shadow-[0_5px_18px_rgba(39,30,20,.02)] sm:px-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium text-[#5f574f]">{{ $card['label'] }}</p>
                        <p class="mt-2 text-[31px] font-semibold leading-none tracking-[-.04em] text-[#211d18]">{{ $card['value'] }}</p>
                        <p class="mt-3 truncate text-[9.5px] font-normal text-[#8f867c]">{{ $card['sub'] }}</p>
                    </div>

                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-[12px] {{ $card['iconBg'] }} {{ $card['iconText'] }}">
                        @if ($card['icon'] === 'history')
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="8"></circle><path d="M12 8v5l3 2"></path>
                            </svg>
                        @elseif ($card['icon'] === 'bag')
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M5 7h14l-1 13H6L5 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path>
                            </svg>
                        @elseif ($card['icon'] === 'box')
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 6h16v13H4z"></path><path d="M8 6V4h8v2"></path><path d="M8 11h8"></path>
                            </svg>
                        @elseif ($card['icon'] === 'truck')
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 7h11v9H3z"></path><path d="M14 10h4l3 3v3h-7z"></path><circle cx="7" cy="18" r="1.5"></circle><circle cx="18" cy="18" r="1.5"></circle>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="8"></circle><path d="m8 12 2.5 2.5L16 9"></path>
                            </svg>
                        @endif
                    </span>
                </div>
            </div>
        @endforeach
    </section>

    {{-- =========================================================
        ORDERS TABLE
    ========================================================== --}}
    <section class="mt-4 overflow-hidden rounded-[20px] border border-[#e9e2d9] bg-white shadow-[0_8px_25px_rgba(47,37,25,.025)]">
        <div class="flex flex-col gap-4 border-b border-[#eee8df] px-5 py-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-[20px] font-semibold tracking-[-.025em] text-[#24201b]">Buyer Orders</h2>
                <p class="mt-1 text-[10.5px] font-normal text-[#887f75]">
                    Review order records, open complete details, and continue the fulfillment flow.
                </p>
                <p id="sellerOrderResultCount" class="mt-3 text-[9.5px] font-medium text-[#9b6c1c]">
                    Showing {{ $orders->count() }} of {{ $orders->count() }} orders
                </p>
            </div>

            <div class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto">
                <label class="relative block sm:w-[310px]">
                    <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[#9e958a]">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path>
                        </svg>
                    </span>
                    <input
                        id="sellerOrderSearch"
                        type="search"
                        placeholder="Search order ID, buyer, product..."
                        autocomplete="off"
                        class="h-12 w-full rounded-[11px] border border-[#e3dbd0] bg-white pl-10 pr-4 text-[10.5px] font-normal text-[#3d3730] outline-none transition placeholder:text-[#aaa198] focus:border-[#d4b069] focus:ring-4 focus:ring-[#d89a19]/[.07]"
                    >
                </label>

                <select
                    id="sellerOrderStatusFilter"
                    class="h-12 min-w-[175px] rounded-[11px] border border-[#e3dbd0] bg-white px-3 text-[10.5px] font-medium text-[#4f473f] outline-none transition focus:border-[#d4b069] focus:ring-4 focus:ring-[#d89a19]/[.07]"
                >
                    <option value="all">All Orders</option>
                    <option value="new">New</option>
                    <option value="preparing">Preparing</option>
                    <option value="ready_for_pickup">Ready Pickup</option>
                    <option value="courier">With Courier</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        {{-- DESKTOP TABLE HEADER --}}
        <div class="hidden grid-cols-[minmax(260px,1.4fr)_minmax(180px,.85fr)_150px_130px_160px_72px] items-center gap-4 border-b border-[#eee8df] bg-[#fdfbf8] px-5 py-3.5 lg:grid">
            <span class="text-[9.5px] font-medium text-[#786f65]">Order</span>
            <span class="text-[9.5px] font-medium text-[#786f65]">Buyer</span>
            <span class="text-[9.5px] font-medium text-[#786f65]">Status</span>
            <span class="text-[9.5px] font-medium text-[#786f65]">Total</span>
            <span class="text-[9.5px] font-medium text-[#786f65]">Date</span>
            <span class="text-right text-[9.5px] font-medium text-[#786f65]">Action</span>
        </div>

        <div id="sellerOrderRows" class="space-y-2.5 p-3 sm:p-4">
            @forelse ($orders as $order)
                @php
                    $items = is_array($order->items) ? $order->items : [];
                    $firstItem = $items[0] ?? [];
                    $firstProductId = (int) ($firstItem['product_id'] ?? 0);
                    $firstImage = $firstProductId > 0
                        ? route('seller.products.image', $firstProductId)
                        : null;

                    $searchText = strtolower(implode(' ', array_filter([
                        $order->order_number,
                        $order->buyer_name,
                        $order->buyer_email ?? null,
                        collect($items)->pluck('name')->implode(' '),
                    ])));

                    $thumbnailIsPriority = $loop->index < 4;
                    $thumbnailIsEager = $loop->index < 6;

                    $filterStatus = in_array($order->status, [
                        'courier_accepted',
                        'heading_pickup',
                        'arrived_pickup',
                        'in_transit',
                        'arrived_buyer',
                    ], true)
                        ? 'courier'
                        : $order->status;
                @endphp

                <article
                    class="seller-order-row rounded-[16px] border border-[#e9e2d9] bg-white"
                    data-order-row
                    data-search="{{ $searchText }}"
                    data-status="{{ $filterStatus }}"
                >
                    <div class="grid gap-3 px-3.5 py-4 lg:grid-cols-[minmax(260px,1.4fr)_minmax(180px,.85fr)_150px_130px_160px_72px] lg:items-center lg:gap-4 lg:px-4">

                        {{-- ORDER / PRODUCT --}}
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="seller-order-image-frame {{ $firstImage ? '' : 'is-loaded' }} relative h-[74px] w-[74px] shrink-0 overflow-hidden rounded-[12px] border border-[#e8e1d8] bg-[#f7f5f1]">
                                @if ($firstImage)
                                    <img
                                        src="{{ $firstImage }}"
                                        alt="{{ $firstItem['name'] ?? 'Ordered product' }}"
                                        width="74"
                                        height="74"
                                        loading="{{ $thumbnailIsEager ? 'eager' : 'lazy' }}"
                                        fetchpriority="{{ $thumbnailIsPriority ? 'high' : 'auto' }}"
                                        decoding="async"
                                        class="h-full w-full object-cover"
                                        onload="this.parentElement.classList.add('is-loaded')"
                                        onerror="this.parentElement.classList.add('is-loaded'); this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                    >
                                @endif

                                <div class="{{ $firstImage ? 'hidden' : '' }} grid h-full w-full place-items-center text-[#aaa197]">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                                        <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                        <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                                    </svg>
                                </div>

                                @if (count($items) > 1)
                                    <span class="absolute bottom-1 right-1 grid h-5 min-w-[20px] place-items-center rounded-full border border-white/80 bg-[#28231e]/85 px-1 text-[7.5px] font-medium text-white">
                                        +{{ count($items) - 1 }}
                                    </span>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-[12px] font-semibold text-[#2c2721]">{{ $order->order_number }}</p>
                                <p class="mt-1 truncate text-[10px] font-medium text-[#5a524a]">{{ $firstItem['name'] ?? 'Order items' }}</p>
                                <p class="mt-1 text-[9px] font-normal text-[#91887e]">
                                    {{ count($items) }} {{ count($items) === 1 ? 'item' : 'items' }}
                                    <span class="mx-1 text-[#d2cbc2]">•</span>
                                    {{ $order->payment_method }}
                                </p>
                            </div>
                        </div>

                        {{-- BUYER --}}
                        <div class="min-w-0 lg:block">
                            <p class="mb-1 text-[8.8px] font-medium uppercase tracking-[.08em] text-[#91887e] lg:hidden">Buyer</p>
                            <p class="truncate text-[10.5px] font-medium text-[#454038]">{{ $order->buyer_name }}</p>
                            <p class="mt-1 truncate text-[9px] font-normal text-[#91887e]">{{ $order->buyer_email ?? 'Buyer account' }}</p>
                        </div>

                        {{-- STATUS --}}
                        <div>
                            <p class="mb-1 text-[8.8px] font-medium uppercase tracking-[.08em] text-[#91887e] lg:hidden">Status</p>
                            <span class="inline-flex rounded-full border px-2.5 py-1.5 text-[9px] font-medium {{ $statusTone($order->status) }}">
                                {{ $order->statusLabel() }}
                            </span>
                        </div>

                        {{-- TOTAL --}}
                        <div>
                            <p class="mb-1 text-[8.8px] font-medium uppercase tracking-[.08em] text-[#91887e] lg:hidden">Total</p>
                            <p class="text-[11px] font-semibold text-[#302a24]">₱{{ number_format((float) $order->total, 2) }}</p>
                        </div>

                        {{-- DATE --}}
                        <div>
                            <p class="mb-1 text-[8.8px] font-medium uppercase tracking-[.08em] text-[#91887e] lg:hidden">Date</p>
                            <p class="text-[10px] font-medium text-[#4a443d]">{{ $order->created_at?->format('M d, Y') }}</p>
                            <p class="mt-1 text-[8.8px] font-normal text-[#91887e]">{{ $order->created_at?->format('h:i A') }}</p>
                        </div>

                        {{-- ACTION --}}
                        <div class="flex items-center justify-end gap-1.5">
                            <button
                                type="button"
                                class="seller-order-view-button grid h-11 w-11 place-items-center rounded-[11px] border border-[#eadfc9] bg-[#fffaf2] text-[#b87b12] transition hover:border-[#d7b56f] hover:bg-[#fff7e5] hover:text-[#98630b]"
                                data-order-template="sellerOrderTemplate-{{ $order->id }}"
                                aria-label="View {{ $order->order_number }}"
                                title="View Order"
                            >
                                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                </article>

                {{-- =====================================================
                    PRE-RENDERED ORDER DETAIL TEMPLATE
                    Modal opens instantly: no fetch / no API delay.
                ====================================================== --}}
                <template id="sellerOrderTemplate-{{ $order->id }}">
                    @php
                        $progress = $progressValue($order->status);
                    @endphp

                    <div class="seller-order-modal-panel w-full max-w-[1180px] overflow-hidden rounded-[22px] border border-[#e7dfd5] bg-white shadow-[0_28px_80px_rgba(32,24,16,.18)]">
                        {{-- MODAL HEADER --}}
                        <div class="flex items-start justify-between gap-4 border-b border-[#eee8df] bg-white px-5 py-4.5 sm:px-6">
                            <div class="flex min-w-0 items-center gap-3.5">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-[12px] border border-[#eadfc9] bg-[#fffaf2] text-[#bd8011]">
                                    <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M6 5h12v15H6z"></path>
                                        <path d="M9 5V3h6v2"></path>
                                        <path d="M9 10h6"></path>
                                        <path d="M9 14h6"></path>
                                    </svg>
                                </span>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-[18px] font-semibold tracking-[-.03em] text-[#26211c]">Order Details</h3>
                                        <span class="rounded-full border px-2.5 py-1.5 text-[9px] font-medium {{ $statusTone($order->status) }}">
                                            {{ $order->statusLabel() }}
                                        </span>
                                    </div>

                                    <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-[8.5px] font-normal text-[#91887d]">
                                        <span>{{ $order->order_number }}</span>
                                        <span class="text-[#d5cdc4]">•</span>
                                        <span>{{ $order->created_at?->format('M d, Y · h:i A') }}</span>
                                        <span class="text-[#d5cdc4]">•</span>
                                        <span class="font-medium text-[#b57810]">₱{{ number_format((float) $order->total, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <button
                                type="button"
                                data-close-order-modal
                                class="grid h-10 w-10 shrink-0 place-items-center rounded-[10px] border border-[#e7dfd6] bg-white text-[#82796f] transition hover:bg-[#faf8f5] hover:text-[#3f3932]"
                                aria-label="Close order details"
                            >
                                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="m7 7 10 10"></path>
                                    <path d="M17 7 7 17"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="seller-order-scrollbar max-h-[80vh] overflow-y-auto bg-[#fcfbf8]">
                            {{-- FORMAL 3 COLUMNS × 2 ROWS --}}
                            <div class="grid auto-rows-fr grid-cols-1 gap-3.5 p-4 sm:p-5 lg:grid-cols-3 lg:p-5.5">

                                {{-- 1. ORDERED PRODUCTS --}}
                                <section class="flex min-h-[250px] flex-col rounded-[16px] border border-[#e8e1d8] bg-white p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2.5">
                                            <span class="grid h-9 w-9 place-items-center rounded-[10px] border border-[#eee6dc] bg-[#fcfaf7] text-[#8d8378]">
                                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M5 7h14l-1 13H6L5 7Z"></path>
                                                    <path d="M9 7a3 3 0 0 1 6 0"></path>
                                                </svg>
                                            </span>
                                            <div>
                                                <h4 class="text-[11px] font-semibold text-[#37312b]">Ordered Products</h4>
                                                <p class="mt-0.5 text-[8px] font-normal text-[#958c81]">{{ count($items) }} {{ count($items) === 1 ? 'item' : 'items' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="seller-order-scrollbar mt-3 max-h-[190px] flex-1 space-y-2 overflow-y-auto pr-1">
                                        @forelse ($items as $item)
                                            @php
                                                $itemProductId = (int) ($item['product_id'] ?? 0);
                                                $itemImage = $itemProductId > 0
                                                    ? route('seller.products.image', $itemProductId)
                                                    : null;
                                                $itemQty = max(1, (int) ($item['qty'] ?? 1));
                                                $itemPrice = (float) ($item['price'] ?? 0);
                                                $itemLineTotal = (float) ($item['line_total'] ?? ($itemPrice * $itemQty));
                                            @endphp

                                            <div class="flex items-center gap-2.5 rounded-[12px] border border-[#ebe4da] bg-[#fdfbf9] p-2.5">
                                                <div class="seller-order-image-frame {{ $itemImage ? '' : 'is-loaded' }} relative h-[58px] w-[58px] shrink-0 overflow-hidden rounded-[10px] border border-[#e8e1d8] bg-[#f7f5f1]">
                                                    @if ($itemImage)
                                                        <img
                                                            src="{{ $itemImage }}"
                                                            alt="{{ $item['name'] ?? 'Ordered product' }}"
                                                            width="58"
                                                            height="58"
                                                            loading="lazy"
                                                            fetchpriority="low"
                                                            decoding="async"
                                                            class="h-full w-full object-cover"
                                                            onload="this.parentElement.classList.add('is-loaded')"
                                                            onerror="this.parentElement.classList.add('is-loaded'); this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                                        >
                                                    @endif

                                                    <div class="{{ $itemImage ? 'hidden' : '' }} grid h-full w-full place-items-center text-[#aaa197]">
                                                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.6">
                                                            <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                                            <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                                                        </svg>
                                                    </div>
                                                </div>

                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-[9px] font-medium text-[#39332d]">{{ $item['name'] ?? 'Product' }}</p>
                                                    <p class="mt-1 truncate text-[7.8px] font-normal text-[#91887d]">
                                                        Qty {{ $itemQty }}
                                                        @if (!empty($item['variant_label']))
                                                            · {{ $item['variant_label'] }}
                                                        @endif
                                                    </p>
                                                    <p class="mt-1 text-[8px] font-medium text-[#b57912]">₱{{ number_format($itemLineTotal, 2) }}</p>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="rounded-[12px] border border-dashed border-[#dfd7cd] bg-[#fcfbf8] p-5 text-center text-[8.5px] text-[#91887d]">
                                                No item detail available.
                                            </div>
                                        @endforelse
                                    </div>
                                </section>

                                {{-- 2. BUYER INFORMATION --}}
                                <section class="min-h-[250px] rounded-[16px] border border-[#e8e1d8] bg-white p-4">
                                    <div class="flex items-center gap-2.5">
                                        <span class="grid h-9 w-9 place-items-center rounded-[10px] border border-[#eee6dc] bg-[#fcfaf7] text-[#8d8378]">
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <circle cx="12" cy="8" r="3"></circle>
                                                <path d="M5 20a7 7 0 0 1 14 0"></path>
                                            </svg>
                                        </span>
                                        <h4 class="text-[11px] font-semibold text-[#37312b]">Buyer Information</h4>
                                    </div>

                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <p class="text-[7.8px] font-medium uppercase tracking-[.08em] text-[#9c9388]">Buyer Name</p>
                                            <p class="mt-1.5 text-[9.5px] font-medium text-[#474039]">{{ $order->buyer_name }}</p>
                                        </div>

                                        <div>
                                            <p class="text-[7.8px] font-medium uppercase tracking-[.08em] text-[#9c9388]">Email</p>
                                            <p class="mt-1.5 break-all text-[8.5px] font-normal leading-4 text-[#6e655c]">{{ $order->buyer_email ?: 'Buyer account' }}</p>
                                        </div>

                                        <div>
                                            <p class="text-[7.8px] font-medium uppercase tracking-[.08em] text-[#9c9388]">Shipping Address</p>
                                            <p class="mt-1.5 text-[8.5px] font-normal leading-5 text-[#6e655c]">{{ $order->buyer_address }}</p>
                                        </div>
                                    </div>
                                </section>

                                {{-- 3. COURIER + PAYMENT --}}
                                <section class="min-h-[250px] rounded-[16px] border border-[#e8e1d8] bg-white p-4">
                                    <div class="flex items-center gap-2.5">
                                        <span class="grid h-9 w-9 place-items-center rounded-[10px] border border-[#eee6dc] bg-[#fcfaf7] text-[#8d8378]">
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M3 7h11v9H3z"></path>
                                                <path d="M14 10h4l3 3v3h-7z"></path>
                                                <circle cx="7" cy="18" r="1.5"></circle>
                                                <circle cx="18" cy="18" r="1.5"></circle>
                                            </svg>
                                        </span>
                                        <h4 class="text-[11px] font-semibold text-[#37312b]">Courier & Payment</h4>
                                    </div>

                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <p class="text-[7.8px] font-medium uppercase tracking-[.08em] text-[#9c9388]">Courier</p>
                                            <p class="mt-1.5 text-[9px] font-medium text-[#474039]">{{ $order->courier_name ?: 'Waiting for courier' }}</p>
                                            <p class="mt-1 break-all text-[8px] font-normal leading-4 text-[#8f867c]">{{ $order->courier_email ?: 'Courier information appears after pickup assignment.' }}</p>
                                        </div>

                                        <div class="border-t border-[#eee8df] pt-3.5">
                                            <p class="text-[7.8px] font-medium uppercase tracking-[.08em] text-[#9c9388]">Payment Method</p>
                                            <p class="mt-1.5 text-[9px] font-medium text-[#474039]">{{ $order->payment_method }}</p>
                                        </div>

                                        <div class="border-t border-[#eee8df] pt-3.5">
                                            <p class="text-[7.8px] font-medium uppercase tracking-[.08em] text-[#9c9388]">Order Date</p>
                                            <p class="mt-1.5 text-[8.5px] font-medium text-[#474039]">{{ $order->created_at?->format('M d, Y · h:i A') }}</p>
                                        </div>
                                    </div>
                                </section>

                                {{-- 4. ORDER SUMMARY --}}
                                <section class="min-h-[230px] rounded-[16px] border border-[#e8e1d8] bg-white p-4">
                                    <div class="flex items-center gap-2.5">
                                        <span class="grid h-9 w-9 place-items-center rounded-[10px] border border-[#eee6dc] bg-[#fcfaf7] text-[#8d8378]">
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M6 3h12v18H6z"></path>
                                                <path d="M9 8h6"></path>
                                                <path d="M9 12h6"></path>
                                            </svg>
                                        </span>
                                        <h4 class="text-[11px] font-semibold text-[#37312b]">Order Summary</h4>
                                    </div>

                                    <div class="mt-4 space-y-3 text-[9px]">
                                        <div class="flex items-center justify-between gap-4">
                                            <span class="font-normal text-[#8f867c]">Subtotal</span>
                                            <span class="font-medium text-[#4b443d]">₱{{ number_format((float) $order->subtotal, 2) }}</span>
                                        </div>

                                        <div class="flex items-center justify-between gap-4">
                                            <span class="font-normal text-[#8f867c]">Delivery Fee</span>
                                            <span class="font-medium text-[#4b443d]">₱{{ number_format((float) $order->delivery_fee, 2) }}</span>
                                        </div>

                                        <div class="border-t border-[#eee8df] pt-3.5">
                                            <div class="flex items-end justify-between gap-4">
                                                <span class="text-[10px] font-semibold text-[#312b25]">Total</span>
                                                <span class="text-[17px] font-semibold tracking-[-.035em] text-[#d39116]">₱{{ number_format((float) $order->total, 2) }}</span>
                                            </div>
                                        </div>

                                        @if ($order->delivered_at)
                                            <div class="rounded-[10px] border border-[#d5e7dc] bg-[#f3faf5] px-3 py-2.5">
                                                <p class="text-[8px] font-medium text-[#4f7d63]">Delivered</p>
                                                <p class="mt-1 text-[7.8px] font-normal text-[#71877a]">{{ $order->delivered_at?->format('M d, Y · h:i A') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </section>

                                {{-- 5. FULFILLMENT PROGRESS --}}
                                <section class="min-h-[230px] rounded-[16px] border border-[#e8e1d8] bg-white p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2.5">
                                            <span class="grid h-9 w-9 place-items-center rounded-[10px] border border-[#eee6dc] bg-[#fcfaf7] text-[#8d8378]">
                                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <circle cx="12" cy="12" r="8"></circle>
                                                    <path d="M12 8v4l3 2"></path>
                                                </svg>
                                            </span>
                                            <h4 class="text-[11px] font-semibold text-[#37312b]">Fulfillment Progress</h4>
                                        </div>
                                        <span class="text-[9px] font-medium text-[#9a6e1e]">{{ $progress }}%</span>
                                    </div>

                                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-[#eee8df]">
                                        <div class="seller-order-progress h-full rounded-full" style="width: {{ $progress }}%"></div>
                                    </div>

                                    <div class="mt-5 grid grid-cols-5 gap-1 text-center">
                                        @foreach ([
                                            ['Placed', 10],
                                            ['Prepare', 25],
                                            ['Pickup', 40],
                                            ['Transit', 80],
                                            ['Done', 100],
                                        ] as [$step, $stepProgress])
                                            <div>
                                                <span class="mx-auto grid h-7 w-7 place-items-center rounded-full border {{ $progress >= $stepProgress ? 'border-[#dfbd73] bg-[#fff8e9] text-[#a97416]' : 'border-[#e6dfd6] bg-[#faf8f5] text-[#aaa198]' }}">
                                                    @if ($progress >= $stepProgress)
                                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="m7 12 3 3 7-7"></path>
                                                        </svg>
                                                    @else
                                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                                    @endif
                                                </span>
                                                <p class="mt-2 text-[7.5px] font-normal {{ $progress >= $stepProgress ? 'text-[#71675b]' : 'text-[#aaa198]' }}">{{ $step }}</p>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-4 rounded-[10px] border {{ $statusTone($order->status) }} px-3 py-2.5 text-center text-[8px] font-medium">
                                        {{ $order->statusLabel() }}
                                    </div>
                                </section>

                                {{-- 6. ORDER ACTIONS --}}
                                <section class="min-h-[230px] rounded-[16px] border border-[#e8e1d8] bg-white p-4">
                                    <div class="flex items-center gap-2.5">
                                        <span class="grid h-9 w-9 place-items-center rounded-[10px] border border-[#eee6dc] bg-[#fcfaf7] text-[#8d8378]">
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M12 3v4"></path>
                                                <path d="M12 17v4"></path>
                                                <path d="M3 12h4"></path>
                                                <path d="M17 12h4"></path>
                                                <circle cx="12" cy="12" r="4"></circle>
                                            </svg>
                                        </span>
                                        <h4 class="text-[11px] font-semibold text-[#37312b]">Order Actions</h4>
                                    </div>

                                    <div class="mt-4 grid gap-2.5">
                                        <a
                                            href="{{ route('seller.orders.waybill', $order) }}"
                                            target="_blank"
                                            class="inline-flex h-10 items-center justify-center gap-2 rounded-[10px] border border-[#e1d8cc] bg-white px-3 text-[8.5px] font-medium text-[#62594f] transition hover:border-[#cfb98e] hover:bg-[#fffdf9] hover:text-[#8f6116]"
                                        >
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M6 9V3h12v6"></path>
                                                <path d="M6 18H4V9h16v9h-2"></path>
                                                <path d="M7 14h10v7H7z"></path>
                                            </svg>
                                            Print Waybill
                                        </a>

                                        @if ($order->buyer_account_id || $order->buyer_social_account_id)
                                            <a
                                                href="{{ route('seller.buyer-messages', ['buyer' => $order->buyer_account_id ? 'account-' . $order->buyer_account_id : 'social-' . $order->buyer_social_account_id]) }}"
                                                wire:navigate
                                                class="inline-flex h-10 items-center justify-center gap-2 rounded-[10px] border border-[#eadfc9] bg-[#fffaf2] px-3 text-[8.5px] font-medium text-[#9a6e1e] transition hover:border-[#d6b66f] hover:bg-[#fff7e5]"
                                            >
                                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M21 11.5a7.5 7.5 0 0 1-7.5 7.5H9l-4 2v-4A7.5 7.5 0 1 1 21 11.5Z"></path>
                                                </svg>
                                                Message Buyer
                                            </a>
                                        @endif

                                        @if ($order->status === 'new')
                                            <form method="POST" action="{{ route('seller.orders.prepare', $order) }}">
                                                @csrf
                                                <button class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-[10px] bg-[#d89412] px-3 text-[8.5px] font-medium text-white shadow-[0_6px_14px_rgba(216,148,18,.12)] transition hover:bg-[#c9870f]">
                                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="M4 6h16v13H4z"></path>
                                                        <path d="M8 6V4h8v2"></path>
                                                        <path d="M8 11h8"></path>
                                                    </svg>
                                                    Prepare Order
                                                </button>
                                            </form>
                                        @elseif ($order->status === 'preparing')
                                            <form method="POST" action="{{ route('seller.orders.ready-pickup', $order) }}">
                                                @csrf
                                                <button class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-[10px] bg-[#527d63] px-3 text-[8.5px] font-medium text-white shadow-[0_6px_14px_rgba(82,125,99,.11)] transition hover:bg-[#466d56]">
                                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="M4 6h16v12H4z"></path>
                                                        <path d="m8 12 2.5 2.5L16 9"></path>
                                                    </svg>
                                                    Mark Ready for Pickup
                                                </button>
                                            </form>
                                        @else
                                            <div class="rounded-[10px] border {{ $statusTone($order->status) }} px-3 py-3 text-center text-[8.5px] font-medium">
                                                {{ $order->statusLabel() }}
                                            </div>
                                        @endif
                                    </div>
                                </section>
                            </div>
                        </div>

                        <div class="flex items-center justify-end border-t border-[#eee8df] bg-white px-5 py-3.5 sm:px-6">
                            <button
                                type="button"
                                data-close-order-modal
                                class="inline-flex h-10 min-w-[120px] items-center justify-center rounded-[10px] bg-[#d89412] px-4 text-[8.5px] font-medium text-white transition hover:bg-[#c9870f]"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </template>
            @empty
                <div class="rounded-[17px] border border-dashed border-[#ded5c9] bg-[#fcfbf8] px-6 py-12 text-center">
                    <div class="mx-auto grid h-11 w-11 place-items-center rounded-[14px] border border-[#ebe3d8] bg-white text-[#9b9185]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M5 7h14l-1 13H6L5 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path>
                        </svg>
                    </div>
                    <p class="mt-3 text-[11px] font-medium text-[#514a42]">No buyer orders yet.</p>
                    <p class="mt-1 text-[9px] font-normal text-[#91887d]">New Buyer checkout orders will automatically appear here.</p>
                </div>
            @endforelse
        </div>

        <div id="sellerOrderNoResults" class="hidden border-t border-[#eee8df] px-6 py-10 text-center">
            <p class="text-[10px] font-medium text-[#595149]">No matching orders.</p>
            <p class="mt-1 text-[8.5px] font-normal text-[#958c82]">Try another search or status filter.</p>
        </div>
    </section>
    </div>
</div>
    </div>
</div>


{{-- =============================================================
    SHARED CENTER MODAL
============================================================= --}}
<div
    id="sellerOrderDetailModal"
    class="fixed inset-0 z-[120] hidden items-center justify-center bg-[#211b14]/35 p-3 backdrop-blur-[2px] sm:p-5"
    aria-hidden="true"
>
    <button
        type="button"
        data-close-order-modal
        class="absolute inset-0 cursor-default"
        aria-label="Close order modal backdrop"
    ></button>

    <div id="sellerOrderDetailModalContent" class="relative z-[1] flex w-full justify-center"></div>
</div>

@push('scripts')
<script>
function resetSellerOrderManagementPage() {
    window.clearInterval(window.__SARI_SELLER_ORDER_SYNC_INTERVAL__);

    window.clearTimeout(window.__SARI_SELLER_ORDERS_SKELETON_TIMER__);
    window.__SARI_SELLER_ORDERS_SKELETON_TIMER__ = null;
    window.__SARI_SELLER_ORDER_SYNC_INTERVAL__ = null;

    window.clearTimeout(window.__SARI_SELLER_ORDER_REFRESH_TIMER__);
    window.__SARI_SELLER_ORDER_REFRESH_TIMER__ = null;

    if (window.__SARI_SELLER_ORDER_ABORT__) {
        window.__SARI_SELLER_ORDER_ABORT__.abort();
    }

    window.__SARI_SELLER_ORDER_ABORT__ = new AbortController();
}

function initSellerOrderManagementPage() {
    const signal = window.__SARI_SELLER_ORDER_ABORT__?.signal;

    const sellerOrdersStage = document.getElementById('sellerOrdersStage');

    window.clearTimeout(window.__SARI_SELLER_ORDERS_SKELETON_TIMER__);
    window.__SARI_SELLER_ORDERS_SKELETON_TIMER__ = window.setTimeout(() => {
        sellerOrdersStage?.classList.add('is-ready');
    }, 1250);
    const modal = document.getElementById('sellerOrderDetailModal');
    const modalContent = document.getElementById('sellerOrderDetailModalContent');
    const realtimeChannelName = @json($sellerOrderRealtimeToken ? 'sari.seller.' . $sellerOrderRealtimeToken : null);
    const ordersPageUrl = @json(route('seller.orders'));
    const liveStateUrl = @json(route('seller.orders.live-state'));

    let currentRevision = document.getElementById('sellerOrdersDynamicRoot')?.dataset.revision || @json($sellerOrderRevision);
    let refreshBusy = false;
    let refreshQueued = false;
    let subscribedChannel = null;

    function markCompletedImages(scope = document) {
        scope.querySelectorAll('.seller-order-image-frame img').forEach((image) => {
            if (image.complete) {
                image.parentElement?.classList.add('is-loaded');
            }
        });
    }

    function closeOrderModal() {
        if (!modal || !modalContent) return;

        modal.dataset.openTemplate = '';
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
        modalContent.innerHTML = '';
        document.documentElement.classList.remove('overflow-hidden');
    }

    function openOrderModal(templateId, preserveFocus = false) {
        if (!modal || !modalContent) return;

        const template = document.getElementById(templateId);
        if (!(template instanceof HTMLTemplateElement)) return;

        modal.dataset.openTemplate = templateId;
        modalContent.innerHTML = '';
        modalContent.appendChild(template.content.cloneNode(true));

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.documentElement.classList.add('overflow-hidden');
        markCompletedImages(modalContent);

        if (!preserveFocus) {
            window.requestAnimationFrame(() => {
                modalContent.querySelector('[data-close-order-modal]')?.focus({ preventScroll: true });
            });
        }
    }

    function applyOrderFilters() {
        const searchInput = document.getElementById('sellerOrderSearch');
        const statusFilter = document.getElementById('sellerOrderStatusFilter');
        const resultCount = document.getElementById('sellerOrderResultCount');
        const noResults = document.getElementById('sellerOrderNoResults');
        const rows = Array.from(document.querySelectorAll('[data-order-row]'));

        const query = (searchInput?.value || '').trim().toLowerCase();
        const selectedStatus = statusFilter?.value || 'all';
        let visibleCount = 0;

        rows.forEach((row) => {
            const matchesSearch = !query || (row.dataset.search || '').includes(query);
            const matchesStatus = selectedStatus === 'all' || row.dataset.status === selectedStatus;
            const visible = matchesSearch && matchesStatus;

            row.classList.toggle('hidden', !visible);
            if (visible) visibleCount++;
        });

        if (resultCount) {
            resultCount.textContent = `Showing ${visibleCount} of ${rows.length} orders`;
        }

        noResults?.classList.toggle('hidden', visibleCount !== 0);
    }

    function captureUiState() {
        return {
            search: document.getElementById('sellerOrderSearch')?.value || '',
            status: document.getElementById('sellerOrderStatusFilter')?.value || 'all',
            openTemplate: modal && !modal.classList.contains('hidden')
                ? (modal.dataset.openTemplate || '')
                : '',
        };
    }

    function restoreUiState(state) {
        const searchInput = document.getElementById('sellerOrderSearch');
        const statusFilter = document.getElementById('sellerOrderStatusFilter');

        if (searchInput) searchInput.value = state.search;
        if (statusFilter) statusFilter.value = state.status;

        applyOrderFilters();
        markCompletedImages(document.getElementById('sellerOrdersDynamicRoot') || document);

        if (state.openTemplate && document.getElementById(state.openTemplate)) {
            openOrderModal(state.openTemplate, true);
        } else if (state.openTemplate) {
            closeOrderModal();
        }
    }

    async function refreshOrderRegion(reason = 'realtime') {
        if (document.hidden) {
            refreshQueued = true;
            return;
        }

        if (refreshBusy) {
            refreshQueued = true;
            return;
        }

        const currentRoot = document.getElementById('sellerOrdersDynamicRoot');
        if (!currentRoot) return;

        refreshBusy = true;
        refreshQueued = false;
        const uiState = captureUiState();

        try {
            const response = await fetch(ordersPageUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-SARI-Partial-Refresh': reason,
                },
                credentials: 'same-origin',
                cache: 'no-store',
                signal,
            });

            if (!response.ok) return;

            const html = await response.text();
            const parsed = new DOMParser().parseFromString(html, 'text/html');
            const incomingRoot = parsed.getElementById('sellerOrdersDynamicRoot');

            if (!incomingRoot) return;

            currentRoot.replaceWith(incomingRoot);
            currentRevision = incomingRoot.dataset.revision || currentRevision;
            restoreUiState(uiState);

            window.dispatchEvent(new CustomEvent('sari:seller-orders-refreshed', {
                detail: { reason, revision: currentRevision }
            }));
        } catch (error) {
            if (error?.name !== 'AbortError') {
                console.debug('Seller order background refresh temporarily unavailable.');
            }
        } finally {
            refreshBusy = false;

            if (refreshQueued && !signal?.aborted) {
                refreshQueued = false;
                window.clearTimeout(window.__SARI_SELLER_ORDER_REFRESH_TIMER__);
                window.__SARI_SELLER_ORDER_REFRESH_TIMER__ = window.setTimeout(
                    () => refreshOrderRegion('queued'),
                    180
                );
            }
        }
    }

    function scheduleRegionRefresh(reason = 'realtime') {
        window.clearTimeout(window.__SARI_SELLER_ORDER_REFRESH_TIMER__);
        window.__SARI_SELLER_ORDER_REFRESH_TIMER__ = window.setTimeout(
            () => refreshOrderRegion(reason),
            140
        );
    }

    async function pollOrderRevision() {
        if (document.hidden || refreshBusy) return;

        try {
            const response = await fetch(liveStateUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                cache: 'no-store',
                signal,
            });

            if (!response.ok) return;

            const data = await response.json();
            const revision = data.revision ? String(data.revision) : '';

            if (revision && currentRevision && revision !== String(currentRevision)) {
                currentRevision = revision;
                scheduleRegionRefresh('fallback-poll');
                return;
            }

            if (revision) currentRevision = revision;
        } catch (error) {
            if (error?.name !== 'AbortError') {
                console.debug('Seller order fallback sync temporarily unavailable.');
            }
        }
    }

    function subscribeRealtime(attempt = 0) {
        if (!realtimeChannelName || signal?.aborted) return;

        if (!window.Echo) {
            if (attempt < 24) {
                window.setTimeout(() => subscribeRealtime(attempt + 1), 250);
            }
            return;
        }

        try {
            subscribedChannel = window.Echo.channel(realtimeChannelName);
            subscribedChannel.stopListening('.seller.order.updated');
            subscribedChannel.listen('.seller.order.updated', (event) => {
                if (event?.revision) {
                    currentRevision = String(event.revision);
                }

                window.dispatchEvent(new CustomEvent('sari:seller-order-updated', {
                    detail: event || {}
                }));

                scheduleRegionRefresh('reverb');
            });
        } catch (_) {
            // The 20-second fallback poll below keeps this page functional.
        }
    }

    document.addEventListener('click', function (event) {
        const viewButton = event.target.closest('.seller-order-view-button');
        if (viewButton) {
            openOrderModal(viewButton.dataset.orderTemplate || '');
            return;
        }

        if (event.target.closest('[data-close-order-modal]')) {
            closeOrderModal();
        }
    }, { signal });

    document.addEventListener('input', function (event) {
        if (event.target?.id === 'sellerOrderSearch') {
            applyOrderFilters();
        }
    }, { signal });

    document.addEventListener('change', function (event) {
        if (event.target?.id === 'sellerOrderStatusFilter') {
            applyOrderFilters();
        }
    }, { signal });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeOrderModal();
        }
    }, { signal });

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden && refreshQueued) {
            scheduleRegionRefresh('visibility-resume');
        }
    }, { signal });

    markCompletedImages(document);
    applyOrderFilters();
    subscribeRealtime();

    // Reverb is primary. Polling is only a quiet fallback when realtime is unavailable.
    window.__SARI_SELLER_ORDER_SYNC_INTERVAL__ = window.setInterval(pollOrderRevision, 20000);
    window.setTimeout(pollOrderRevision, 6000);

    signal?.addEventListener('abort', function () {
        if (subscribedChannel) {
            try {
                subscribedChannel.stopListening('.seller.order.updated');
            } catch (_) {}
        }
    }, { once: true });
}

resetSellerOrderManagementPage();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSellerOrderManagementPage, { once: true });
} else {
    initSellerOrderManagementPage();
}

document.addEventListener('livewire:navigating', resetSellerOrderManagementPage);
document.addEventListener('livewire:navigated', function () {
    resetSellerOrderManagementPage();
    initSellerOrderManagementPage();
});
</script>
@endpush
@endsection
