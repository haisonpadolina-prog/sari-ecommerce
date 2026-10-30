@extends('layouts.seller')

@section('title', 'Order Management — SARI Seller')
@section('page-title', 'Order Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/orders.css') }}?v=20261007-1">
{{-- Cacheable order-management stylesheet; backend/JS/routes preserved. --}}

@endpush

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


<div class="seller-orders-page mx-auto w-full max-w-[1800px]">
<div id="sellerOrdersStage" class="seller-orders-stage">

    {{-- Server-rendered orders are visible immediately; no loading skeleton. --}}

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
    <section class="seller-orders-header">
        <div class="seller-orders-heading-group">
            <div class="min-w-0">
                <p class="seller-orders-eyebrow">ORDER MANAGEMENT</p>
                <h1 class="seller-orders-title">
                    <span>Seller</span><span class="seller-orders-title-accent">Orders</span>
                </h1>
                <p class="seller-orders-subtitle">
                    Track, prepare, and fulfill Buyer orders from one clean workspace.
                </p>
            </div>
        </div>

        <div class="seller-orders-header-actions">
            <a href="{{ route('seller.messages') }}" class="seller-orders-header-action">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 11.5a7.5 7.5 0 0 1-7.5 7.5H9l-4 2v-4A7.5 7.5 0 1 1 21 11.5Z"></path>
                </svg>
                Buyer Inbox
            </a>

            <a href="{{ route('seller.reviews') }}" wire:navigate class="seller-orders-header-action">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
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
    <section class="seller-orders-summary mt-5 grid grid-cols-2 gap-3 md:grid-cols-5">
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
            <div class="seller-orders-summary-card">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium text-[#5f574f]">{{ $card['label'] }}</p>
                        <p class="mt-2 text-[31px] font-semibold leading-none tracking-[-.04em] text-[#211d18]">{{ $card['value'] }}</p>
                        <p class="mt-3 truncate text-[9.5px] font-normal text-[#8f867c]">{{ $card['sub'] }}</p>
                    </div>

                    <span class="seller-orders-summary-icon {{ $card['iconBg'] }} {{ $card['iconText'] }}">
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
    <section class="seller-orders-filter-panel" aria-label="Order filters">
        <label class="seller-orders-search-field">
            <span aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.5-3.5"></path>
                </svg>
            </span>
            <input
                id="sellerOrderSearch"
                type="search"
                placeholder="Search order ID, buyer, product..."
                autocomplete="off"
            >
        </label>

        <div class="seller-orders-status-dropdown" data-order-status-dropdown>
            {{-- Preserve the original select as the filter state source.
                 It stays synchronized with the custom dropdown below. --}}
            <select
                id="sellerOrderStatusFilter"
                class="seller-orders-filter-select seller-orders-status-native"
                tabindex="-1"
                aria-hidden="true"
            >
                <option value="all">All Status</option>
                <option value="new">New</option>
                <option value="preparing">Preparing</option>
                <option value="ready_for_pickup">Ready Pickup</option>
                <option value="courier">With Courier</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>

            <button
                type="button"
                class="seller-orders-status-trigger"
                data-order-status-trigger
                aria-haspopup="listbox"
                aria-expanded="false"
                aria-controls="sellerOrderStatusMenu"
            >
                <span class="seller-orders-status-trigger-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 6h16"></path>
                        <path d="M7 12h10"></path>
                        <path d="M10 18h4"></path>
                    </svg>
                </span>

                <span class="seller-orders-status-trigger-label" data-order-status-label>All Status</span>

                <span class="seller-orders-status-trigger-chevron" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m7 10 5 5 5-5"></path>
                    </svg>
                </span>
            </button>

            <div
                id="sellerOrderStatusMenu"
                class="seller-orders-status-menu"
                data-order-status-menu
                role="listbox"
                aria-label="Filter orders by status"
                hidden
            >
                @foreach ([
                    'all' => 'All Status',
                    'new' => 'New',
                    'preparing' => 'Preparing',
                    'ready_for_pickup' => 'Ready Pickup',
                    'courier' => 'With Courier',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ] as $statusValue => $statusLabel)
                    <button
                        type="button"
                        class="seller-orders-status-option"
                        data-order-status-option
                        data-value="{{ $statusValue }}"
                        role="option"
                        aria-selected="{{ $statusValue === 'all' ? 'true' : 'false' }}"
                    >
                        <span class="seller-orders-status-option-dot" aria-hidden="true"></span>
                        <span class="seller-orders-status-option-label">{{ $statusLabel }}</span>
                        <span class="seller-orders-status-option-check" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m7 12 3 3 7-7"></path>
                            </svg>
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <button id="sellerOrderApplyFilter" type="button" class="seller-orders-apply-filter">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M4 5h16"></path>
                <path d="M7 10h10"></path>
                <path d="M10 15h4"></path>
            </svg>
            Apply Filter
        </button>

        <button id="sellerOrderResetFilter" type="button" class="seller-orders-reset-filter">Reset</button>
    </section>

    <section class="seller-orders-workspace">
        {{-- DESKTOP TABLE HEADER --}}
        <div class="seller-orders-table-head">
            <span>#</span>
            <span>Order</span>
            <span>Buyer</span>
            <span>Status</span>
            <span>Total</span>
            <span>Date</span>
            <span class="text-right">Action</span>
        </div>

        <div id="sellerOrderRows" class="seller-orders-table-body">
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
                    class="seller-order-row"
                    data-order-row
                    data-search="{{ $searchText }}"
                    data-status="{{ $filterStatus }}"
                >
                    <div class="seller-order-grid">
                        <div class="seller-order-index">{{ $loop->iteration }}</div>

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
                                class="seller-order-view-button"
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
                        $workflowSteps = [
                            ['Order placed', 10],
                            ['Preparing', 25],
                            ['Ready for pickup', 40],
                            ['In transit', 80],
                            ['Delivered', 100],
                        ];
                        $nextActionLabel = match ($order->status) {
                            'new' => 'Prepare this order',
                            'preparing' => 'Mark ready for pickup',
                            'ready_for_pickup' => 'Waiting for courier',
                            'courier_accepted', 'heading_pickup', 'arrived_pickup' => 'Courier pickup in progress',
                            'in_transit', 'arrived_buyer' => 'Delivery in progress',
                            'delivered' => 'Order completed',
                            'cancelled' => 'Order cancelled',
                            default => $order->statusLabel(),
                        };
                    @endphp

                    <div class="seller-order-modal-panel seller-order-workflow-panel">
                        <header class="seller-order-workflow-header">
                            <div class="seller-order-workflow-header-main">
                                <span class="seller-order-workflow-header-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M6 5h12v15H6z"></path>
                                        <path d="M9 5V3h6v2"></path>
                                        <path d="M9 10h6"></path>
                                        <path d="M9 14h6"></path>
                                    </svg>
                                </span>

                                <div class="min-w-0">
                                    <div class="seller-order-workflow-title-row">
                                        <h3>Order Details</h3>
                                        <span class="seller-order-workflow-status {{ $statusTone($order->status) }}">
                                            {{ $order->statusLabel() }}
                                        </span>
                                    </div>

                                    <div class="seller-order-workflow-meta">
                                        <span>{{ $order->order_number }}</span>
                                        <span aria-hidden="true">•</span>
                                        <span>{{ $order->created_at?->format('M d, Y · h:i A') }}</span>
                                        <span aria-hidden="true">•</span>
                                        <strong>₱{{ number_format((float) $order->total, 2) }}</strong>
                                    </div>
                                </div>
                            </div>

                            <button type="button" data-close-order-modal class="seller-order-workflow-close" aria-label="Close order details">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="m7 7 10 10"></path>
                                    <path d="M17 7 7 17"></path>
                                </svg>
                            </button>
                        </header>

                        <div class="seller-order-workflow-body">
                            <div class="seller-order-workflow-main">
                                <section class="seller-order-workflow-card seller-order-workflow-products">
                                    <div class="seller-order-workflow-section-head">
                                        <div class="seller-order-workflow-section-title">
                                            <span class="seller-order-workflow-section-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M5 7h14l-1 13H6L5 7Z"></path>
                                                    <path d="M9 7a3 3 0 0 1 6 0"></path>
                                                </svg>
                                            </span>
                                            <div>
                                                <h4>Ordered Products</h4>
                                                <p>{{ count($items) }} {{ count($items) === 1 ? 'item' : 'items' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="seller-order-workflow-product-list seller-order-scrollbar">
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

                                            <div class="seller-order-workflow-product-row">
                                                <div class="seller-order-workflow-product-image seller-order-image-frame {{ $itemImage ? '' : 'is-loaded' }}">
                                                    @if ($itemImage)
                                                        <img
                                                            src="{{ $itemImage }}"
                                                            alt="{{ $item['name'] ?? 'Ordered product' }}"
                                                            width="54"
                                                            height="54"
                                                            loading="lazy"
                                                            fetchpriority="low"
                                                            decoding="async"
                                                            class="h-full w-full object-contain"
                                                            onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                                        >
                                                    @endif

                                                    <div class="{{ $itemImage ? 'hidden' : '' }} seller-order-workflow-image-fallback">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                                            <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                                            <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                                                        </svg>
                                                    </div>
                                                </div>

                                                <div class="seller-order-workflow-product-copy">
                                                    <p class="seller-order-workflow-product-name">{{ $item['name'] ?? 'Product' }}</p>
                                                    <p class="seller-order-workflow-product-meta">
                                                        Qty {{ $itemQty }}
                                                        @if (!empty($item['variant_label']))
                                                            · {{ $item['variant_label'] }}
                                                        @endif
                                                    </p>
                                                </div>

                                                <p class="seller-order-workflow-product-price">₱{{ number_format($itemLineTotal, 2) }}</p>
                                            </div>
                                        @empty
                                            <div class="seller-order-workflow-empty">No item detail available.</div>
                                        @endforelse
                                    </div>
                                </section>

                                <div class="seller-order-workflow-info-grid">
                                    <section class="seller-order-workflow-card seller-order-workflow-info-card">
                                        <div class="seller-order-workflow-section-title">
                                            <span class="seller-order-workflow-section-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <circle cx="12" cy="8" r="3"></circle>
                                                    <path d="M5 20a7 7 0 0 1 14 0"></path>
                                                </svg>
                                            </span>
                                            <div>
                                                <h4>Buyer & Delivery</h4>
                                                <p>Customer and shipping details</p>
                                            </div>
                                        </div>

                                        <dl class="seller-order-workflow-details">
                                            <div>
                                                <dt>Buyer</dt>
                                                <dd>{{ $order->buyer_name }}</dd>
                                            </div>
                                            <div>
                                                <dt>Email</dt>
                                                <dd class="break-all">{{ $order->buyer_email ?: 'Buyer account' }}</dd>
                                            </div>
                                            <div class="seller-order-workflow-detail-wide">
                                                <dt>Shipping address</dt>
                                                <dd>{{ $order->buyer_address }}</dd>
                                            </div>
                                        </dl>
                                    </section>

                                    <section class="seller-order-workflow-card seller-order-workflow-info-card">
                                        <div class="seller-order-workflow-section-title">
                                            <span class="seller-order-workflow-section-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M3 7h11v9H3z"></path>
                                                    <path d="M14 10h4l3 3v3h-7z"></path>
                                                    <circle cx="7" cy="18" r="1.5"></circle>
                                                    <circle cx="18" cy="18" r="1.5"></circle>
                                                </svg>
                                            </span>
                                            <div>
                                                <h4>Delivery & Payment</h4>
                                                <p>Courier and transaction method</p>
                                            </div>
                                        </div>

                                        <dl class="seller-order-workflow-details">
                                            <div>
                                                <dt>Courier</dt>
                                                <dd>{{ $order->courier_name ?: 'Waiting for courier' }}</dd>
                                            </div>
                                            <div>
                                                <dt>Payment</dt>
                                                <dd>{{ $order->payment_method }}</dd>
                                            </div>
                                            <div class="seller-order-workflow-detail-wide">
                                                <dt>Order date</dt>
                                                <dd>{{ $order->created_at?->format('M d, Y · h:i A') }}</dd>
                                            </div>
                                        </dl>
                                    </section>
                                </div>
                            </div>

                            <aside class="seller-order-workflow-sidebar seller-order-scrollbar">
                                <section class="seller-order-workflow-card seller-order-workflow-summary">
                                    <div class="seller-order-workflow-section-title">
                                        <span class="seller-order-workflow-section-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M6 3h12v18H6z"></path>
                                                <path d="M9 8h6"></path>
                                                <path d="M9 12h6"></path>
                                            </svg>
                                        </span>
                                        <div>
                                            <h4>Order Summary</h4>
                                            <p>Payment breakdown</p>
                                        </div>
                                    </div>

                                    <div class="seller-order-workflow-money-lines">
                                        <div><span>Subtotal</span><strong>₱{{ number_format((float) $order->subtotal, 2) }}</strong></div>
                                        <div><span>Delivery fee</span><strong>₱{{ number_format((float) $order->delivery_fee, 2) }}</strong></div>
                                    </div>

                                    <div class="seller-order-workflow-total">
                                        <span>Total</span>
                                        <strong>₱{{ number_format((float) $order->total, 2) }}</strong>
                                    </div>
                                </section>

                                <section class="seller-order-workflow-card seller-order-workflow-progress-card">
                                    <div class="seller-order-workflow-section-head">
                                        <div class="seller-order-workflow-section-title">
                                            <span class="seller-order-workflow-section-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <circle cx="12" cy="12" r="8"></circle>
                                                    <path d="M12 8v4l3 2"></path>
                                                </svg>
                                            </span>
                                            <div>
                                                <h4>Fulfillment</h4>
                                                <p>Order progress</p>
                                            </div>
                                        </div>
                                        <strong class="seller-order-workflow-progress-percent">{{ $progress }}%</strong>
                                    </div>

                                    <div class="seller-order-workflow-progress-bar" aria-hidden="true">
                                        <span style="width: {{ $progress }}%"></span>
                                    </div>

                                    <ol class="seller-order-workflow-timeline">
                                        @foreach ($workflowSteps as [$step, $stepProgress])
                                            @php $complete = $progress >= $stepProgress; @endphp
                                            <li class="{{ $complete ? 'is-complete' : '' }}">
                                                <span class="seller-order-workflow-timeline-dot">
                                                    @if ($complete)
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="m7 12 3 3 7-7"></path>
                                                        </svg>
                                                    @else
                                                        <span></span>
                                                    @endif
                                                </span>
                                                <span>{{ $step }}</span>
                                            </li>
                                        @endforeach
                                    </ol>
                                </section>

                                <section class="seller-order-workflow-card seller-order-workflow-actions">
                                    <div class="seller-order-workflow-action-heading">
                                        <span class="seller-order-workflow-action-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                <path d="m13 2-8 11h6l-1 9 9-12h-6l0-8Z"></path>
                                            </svg>
                                        </span>
                                        <div>
                                            <p>Next Action</p>
                                            <h4>{{ $nextActionLabel }}</h4>
                                            <small>{{ $order->status === 'new' ? 'Start preparing the items for pickup.' : ($order->status === 'preparing' ? 'Finish packing and hand the order to the courier.' : 'Continue with the current fulfillment stage.') }}</small>
                                        </div>
                                    </div>

                                    @if ($order->status === 'new')
                                        <form method="POST" action="{{ route('seller.orders.prepare', $order) }}">
                                            @csrf
                                            <button class="seller-order-workflow-primary-action">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
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
                                            <button class="seller-order-workflow-primary-action seller-order-workflow-primary-action--success">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M4 6h16v12H4z"></path>
                                                    <path d="m8 12 2.5 2.5L16 9"></path>
                                                </svg>
                                                Mark Ready for Pickup
                                            </button>
                                        </form>
                                    @else
                                        <div class="seller-order-workflow-passive-action {{ $statusTone($order->status) }}">
                                            {{ $order->statusLabel() }}
                                        </div>
                                    @endif

                                    <div class="seller-order-workflow-secondary-actions">
                                        @if ($order->buyer_account_id || $order->buyer_social_account_id)
                                            <a
                                                href="{{ route('seller.messages', ['buyer' => $order->buyer_account_id ? 'account-' . $order->buyer_account_id : 'social-' . $order->buyer_social_account_id]) }}"
                                                data-message-buyer
                                                aria-label="Message buyer for order {{ $order->order_number }}"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M21 11.5a7.5 7.5 0 0 1-7.5 7.5H9l-4 2v-4A7.5 7.5 0 1 1 21 11.5Z"></path>
                                                </svg>
                                                Message Buyer
                                            </a>
                                        @endif

                                        <a href="{{ route('seller.orders.waybill', $order) }}" target="_blank">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M6 9V3h12v6"></path>
                                                <path d="M6 18H4V9h16v9h-2"></path>
                                                <path d="M7 14h10v7H7z"></path>
                                            </svg>
                                            Print Waybill
                                        </a>
                                    </div>
                                </section>
                            </aside>
                        </div>
                    </div>
                </template>
            @empty
                <div class="seller-orders-empty-state">
                    <div class="seller-orders-empty-icon">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M5 7h14l-1 13H6L5 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path>
                        </svg>
                    </div>
                    <p class="mt-3 text-[11px] font-medium text-[#514a42]">No buyer orders yet.</p>
                    <p class="mt-1 text-[9px] font-normal text-[#91887d]">New Buyer checkout orders will automatically appear here.</p>
                </div>
            @endforelse
        </div>

        @if ($orders->isNotEmpty())
            <div id="sellerOrderNoResults" class="hidden border-t border-[#eee8df] px-6 py-10 text-center">
                <p class="text-[10px] font-medium text-[#595149]">No matching orders.</p>
                <p class="mt-1 text-[8.5px] font-normal text-[#958c82]">Try another search or status filter.</p>
            </div>
        @endif

        <div class="seller-orders-table-footer">
            <p id="sellerOrderResultCount">Showing {{ $orders->count() }} of {{ $orders->count() }} orders</p>
            <div class="seller-orders-pagination" aria-label="Current order page">
                <button type="button" disabled aria-label="Previous page">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"></path></svg>
                </button>
                <span>1</span>
                <button type="button" disabled aria-label="Next page">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 18 6-6-6-6"></path></svg>
                </button>
            </div>
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

    const modal = document.getElementById('sellerOrderDetailModal');
    const modalContent = document.getElementById('sellerOrderDetailModalContent');
    const realtimeChannelName = @json($sellerOrderRealtimeToken ? 'sari.seller.' . $sellerOrderRealtimeToken : null);
    const ordersPageUrl = @json(route('seller.orders'));
    const liveStateUrl = @json(route('seller.orders.live-state'));

    let currentRevision = document.getElementById('sellerOrdersDynamicRoot')?.dataset.revision || @json($sellerOrderRevision);
    let refreshBusy = false;
    let refreshQueued = false;
    let subscribedChannel = null;

    function getStatusDropdownElements() {
        const root = document.querySelector('[data-order-status-dropdown]');

        return {
            root,
            select: document.getElementById('sellerOrderStatusFilter'),
            trigger: root?.querySelector('[data-order-status-trigger]') || null,
            label: root?.querySelector('[data-order-status-label]') || null,
            menu: root?.querySelector('[data-order-status-menu]') || null,
            options: root ? Array.from(root.querySelectorAll('[data-order-status-option]')) : [],
        };
    }

    function syncStatusDropdownUi() {
        const { select, label, options } = getStatusDropdownElements();
        if (!select) return;

        const selectedValue = select.value || 'all';
        const selectedText = select.options[select.selectedIndex]?.text || 'All Status';

        if (label) label.textContent = selectedText;

        options.forEach((option) => {
            option.setAttribute(
                'aria-selected',
                option.dataset.value === selectedValue ? 'true' : 'false'
            );
        });
    }

    function closeStatusDropdown({ focusTrigger = false } = {}) {
        const { trigger, menu } = getStatusDropdownElements();
        if (!trigger || !menu) return;

        menu.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');

        if (focusTrigger) {
            trigger.focus({ preventScroll: true });
        }
    }

    function openStatusDropdown() {
        const { trigger, menu, options } = getStatusDropdownElements();
        if (!trigger || !menu) return;

        menu.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');

        const selected = options.find((option) => option.getAttribute('aria-selected') === 'true');
        window.requestAnimationFrame(() => {
            (selected || options[0])?.focus({ preventScroll: true });
        });
    }

    function toggleStatusDropdown() {
        const { trigger, menu } = getStatusDropdownElements();
        if (!trigger || !menu) return;

        if (menu.hidden) {
            openStatusDropdown();
        } else {
            closeStatusDropdown({ focusTrigger: true });
        }
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

        const hasOrderRows = rows.length > 0;
        noResults?.classList.toggle('hidden', !hasOrderRows || visibleCount !== 0);

        syncStatusDropdownUi();
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
        const statusTrigger = event.target.closest('[data-order-status-trigger]');
        if (statusTrigger) {
            event.preventDefault();
            toggleStatusDropdown();
            return;
        }

        const statusOption = event.target.closest('[data-order-status-option]');
        if (statusOption) {
            event.preventDefault();

            const { select } = getStatusDropdownElements();
            if (select) {
                select.value = statusOption.dataset.value || 'all';
                syncStatusDropdownUi();
                closeStatusDropdown({ focusTrigger: true });
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }

            return;
        }

        if (!event.target.closest('[data-order-status-dropdown]')) {
            closeStatusDropdown();
        }

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
            syncStatusDropdownUi();
            applyOrderFilters();
        }
    }, { signal });

    document.getElementById('sellerOrderApplyFilter')?.addEventListener('click', function () {
        applyOrderFilters();
    }, { signal });

    document.getElementById('sellerOrderResetFilter')?.addEventListener('click', function () {
        const searchInput = document.getElementById('sellerOrderSearch');
        const statusFilter = document.getElementById('sellerOrderStatusFilter');
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = 'all';
        syncStatusDropdownUi();
        closeStatusDropdown();
        applyOrderFilters();
        searchInput?.focus({ preventScroll: true });
    }, { signal });

    document.addEventListener('keydown', function (event) {
        const { trigger, menu, options } = getStatusDropdownElements();
        const dropdownOpen = Boolean(menu && !menu.hidden);
        const targetIsTrigger = event.target === trigger;
        const optionIndex = options.indexOf(event.target);

        if (targetIsTrigger && (event.key === 'ArrowDown' || event.key === 'ArrowUp')) {
            event.preventDefault();
            openStatusDropdown();

            const index = event.key === 'ArrowUp' ? options.length - 1 : 0;
            options[index]?.focus({ preventScroll: true });
            return;
        }

        if (dropdownOpen && optionIndex >= 0 && (event.key === 'ArrowDown' || event.key === 'ArrowUp')) {
            event.preventDefault();

            const direction = event.key === 'ArrowDown' ? 1 : -1;
            const nextIndex = (optionIndex + direction + options.length) % options.length;
            options[nextIndex]?.focus({ preventScroll: true });
            return;
        }

        if (dropdownOpen && optionIndex >= 0 && event.key === 'Home') {
            event.preventDefault();
            options[0]?.focus({ preventScroll: true });
            return;
        }

        if (dropdownOpen && optionIndex >= 0 && event.key === 'End') {
            event.preventDefault();
            options[options.length - 1]?.focus({ preventScroll: true });
            return;
        }

        if (event.key === 'Escape' && dropdownOpen) {
            event.preventDefault();
            closeStatusDropdown({ focusTrigger: true });
            return;
        }

        if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeOrderModal();
        }
    }, { signal });

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden && refreshQueued) {
            scheduleRegionRefresh('visibility-resume');
        }
    }, { signal });

    applyOrderFilters();

    /*
    | The persistent Seller shell already owns the seller order Echo channel and
    | its fallback poll. Reuse that single source instead of opening a second
    | subscription + timer every time Order Management is visited.
    */
    const sellerShellOwnsOrderSync = Boolean(window.__SARI_SELLER_SHELL_STATE__);

    window.addEventListener('sari:seller-order-update', function (event) {
        if (event?.detail?.revision) {
            currentRevision = String(event.detail.revision);
        }

        scheduleRegionRefresh('shell-realtime');
    }, { signal });

    /* Standalone safety only if this view is ever rendered without seller.blade.php. */
    if (!sellerShellOwnsOrderSync) {
        subscribeRealtime();
        window.__SARI_SELLER_ORDER_SYNC_INTERVAL__ = window.setInterval(pollOrderRevision, 45000);
        window.setTimeout(pollOrderRevision, 10000);
    }

    signal?.addEventListener('abort', function () {
        if (!sellerShellOwnsOrderSync && subscribedChannel) {
            try {
                subscribedChannel.stopListening('.seller.order.updated');
            } catch (_) {}
        }
    }, { once: true });
}

resetSellerOrderManagementPage();

function scheduleSellerOrderManagementPage() {
    initSellerOrderManagementPage();
}

/*
| Body scripts are evaluated when Livewire lands on this page, so initialize
| exactly once here. Cleanup is one-shot on the NEXT navigation away.
*/
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', scheduleSellerOrderManagementPage, { once: true });
} else {
    scheduleSellerOrderManagementPage();
}

document.addEventListener('livewire:navigating', resetSellerOrderManagementPage, { once: true });
</script>
@endpush
@endsection
