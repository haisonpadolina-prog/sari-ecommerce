@extends('layouts.buyer')

@section('title', 'Recent Orders — SARI')
@section('page-title', 'Recent Orders')

@section('content')
@include('components.buyer.header')

@php
    $activeCount = $recentOrders->whereNotIn('status', ['delivered', 'cancelled'])->count();
    $deliveredCount = $recentOrders->where('status', 'delivered')->count();
    $cancelledCount = $recentOrders->where('status', 'cancelled')->count();

    $statusClass = static function (?string $status): string {
        return match ($status) {
            'delivered' => 'is-delivered',
            'cancelled' => 'is-cancelled',
            'in_transit', 'arrived_buyer', 'courier_accepted', 'heading_pickup', 'arrived_pickup' => 'is-shipping',
            default => 'is-active',
        };
    };
@endphp

<style>
    .sari-recent-page {
        --gold: #c88912;
        --gold-dark: #a96f0e;
        --ink: #202832;
        --muted: #7d8793;
        --line: #e1e6ea;
        --soft: #f6f8fa;
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        color: var(--ink);
    }

    .sari-recent-shell {
        width: min(1380px, calc(100% - 32px));
        margin: 0 auto;
        padding: 24px 0 42px;
    }

    .sari-recent-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 18px;
    }

    .sari-recent-eyebrow {
        margin: 0 0 5px;
        color: #a87316;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .sari-recent-title {
        margin: 0;
        color: #202832;
        font-size: clamp(30px, 3vw, 38px);
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -.04em;
    }

    .sari-recent-link {
        display: inline-flex;
        align-items: center;
        min-height: 38px;
        color: #333b45;
        font-size: 11px;
        font-weight: 650;
        text-decoration: none;
    }

    .sari-recent-link:hover { color: var(--gold-dark); }

    .sari-recent-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        overflow: hidden;
        margin-bottom: 16px;
        border: 1px solid var(--line);
        border-radius: 14px;
        background: #fff;
    }

    .sari-recent-stat {
        position: relative;
        min-height: 78px;
        padding: 15px 18px;
    }

    .sari-recent-stat + .sari-recent-stat::before {
        content: '';
        position: absolute;
        left: 0;
        top: 16px;
        bottom: 16px;
        width: 1px;
        background: #e8ecef;
    }

    .sari-recent-stat span {
        display: block;
        color: #7b8692;
        font-size: 9px;
        font-weight: 750;
        letter-spacing: .07em;
        text-transform: uppercase;
    }

    .sari-recent-stat strong {
        display: block;
        margin-top: 9px;
        color: #1f2937;
        font-size: 23px;
        font-weight: 800;
        line-height: 1;
    }

    .sari-recent-list {
        overflow: hidden;
        border: 1px solid var(--line);
        border-radius: 14px;
        background: #fff;
    }

    .sari-recent-order {
        display: grid;
        grid-template-columns: minmax(210px, 1.2fr) minmax(250px, 1.7fr) 130px 130px 90px;
        align-items: center;
        gap: 18px;
        min-height: 102px;
        padding: 16px 18px;
    }

    .sari-recent-order + .sari-recent-order { border-top: 1px solid #edf0f3; }

    .sari-order-number {
        color: #252e38;
        font-size: 11.5px;
        font-weight: 800;
    }

    .sari-order-meta {
        margin-top: 4px;
        color: #87919c;
        font-size: 9.5px;
        line-height: 1.45;
    }

    .sari-order-product {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 12px;
    }

    .sari-order-thumb {
        width: 58px;
        height: 58px;
        flex: 0 0 58px;
        overflow: hidden;
        border: 1px solid #e2e7eb;
        border-radius: 11px;
        background: #f6f8fa;
    }

    .sari-order-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .sari-order-thumb-fallback { display: grid; width: 100%; height: 100%; place-items: center; color: #a0a8b1; font-size: 11px; font-weight: 800; }

    .sari-order-product-name {
        overflow: hidden;
        color: #2a333d;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.35;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sari-order-product-meta { margin-top: 5px; color: #8a949f; font-size: 9px; }
    .sari-order-total { color: var(--gold-dark); font-size: 12px; font-weight: 800; }

    .sari-order-status {
        font-size: 10px;
        font-weight: 700;
        line-height: 1.35;
    }

    .sari-order-status.is-active { color: #9a6811; }
    .sari-order-status.is-shipping { color: #3d6480; }
    .sari-order-status.is-delivered { color: #3f7a57; }
    .sari-order-status.is-cancelled { color: #df3434; }

    .sari-order-open {
        color: #303943;
        font-size: 10px;
        font-weight: 700;
        text-decoration: none;
        text-align: right;
    }

    .sari-order-open:hover { color: var(--gold-dark); }

    .sari-recent-empty {
        min-height: 380px;
        display: grid;
        place-items: center;
        padding: 42px 20px;
        text-align: center;
    }

    .sari-recent-empty h2 { margin: 0; color: #4f5964; font-size: 17px; font-weight: 750; }
    .sari-recent-empty p { margin: 7px 0 20px; color: #98a0a9; font-size: 10px; }
    .sari-recent-empty a { display: inline-flex; min-height: 40px; align-items: center; border-radius: 9px; background: var(--gold); padding: 0 16px; color: #fff; font-size: 10px; font-weight: 700; text-decoration: none; }

    @media (max-width: 1050px) {
        .sari-recent-order { grid-template-columns: minmax(190px, 1fr) minmax(220px, 1.4fr) 110px 110px; }
        .sari-order-open { grid-column: 4; text-align: left; }
    }

    @media (max-width: 760px) {
        .sari-recent-shell { width: min(100% - 24px, 1380px); padding-top: 18px; }
        .sari-recent-head { align-items: flex-start; }
        .sari-recent-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .sari-recent-stat:nth-child(3)::before { display: none; }
        .sari-recent-stat:nth-child(n+3) { border-top: 1px solid #e8ecef; }
        .sari-recent-order { grid-template-columns: 1fr; gap: 11px; padding: 16px; }
        .sari-order-open { grid-column: auto; text-align: left; }
    }
</style>

<div class="sari-recent-page">
    <div class="sari-recent-shell">
        <header class="sari-recent-head">
            <div>
                <p class="sari-recent-eyebrow">Order history</p>
                <h1 class="sari-recent-title">Recent Orders</h1>
            </div>
            <a href="{{ route('buyer.orders') }}" class="sari-recent-link" wire:navigate.hover>View all orders →</a>
        </header>

        <section class="sari-recent-stats" aria-label="Recent order summary">
            <div class="sari-recent-stat"><span>Recent</span><strong>{{ $recentOrders->count() }}</strong></div>
            <div class="sari-recent-stat"><span>Active</span><strong>{{ $activeCount }}</strong></div>
            <div class="sari-recent-stat"><span>Delivered</span><strong>{{ $deliveredCount }}</strong></div>
            <div class="sari-recent-stat"><span>Cancelled</span><strong>{{ $cancelledCount }}</strong></div>
        </section>

        <section class="sari-recent-list">
            @forelse ($recentOrders as $order)
                @php
                    $items = collect($order->items ?? [])->values();
                    $firstItem = $items->first() ?? [];
                    $productId = (int) ($firstItem['product_id'] ?? 0);
                    $productName = trim((string) ($firstItem['name'] ?? 'Ordered product')) ?: 'Ordered product';
                    $productQty = max(1, (int) ($firstItem['qty'] ?? 1));
                    $variantLabel = trim((string) ($firstItem['variant_label'] ?? ''));
                    $moreItems = max(0, $items->count() - 1);
                    $storeName = trim((string) ($order->seller?->store_name ?? 'SARI Seller')) ?: 'SARI Seller';
                @endphp

                <article class="sari-recent-order">
                    <div>
                        <div class="sari-order-number">{{ $order->order_number }}</div>
                        <div class="sari-order-meta">{{ $storeName }} · {{ optional($order->created_at)->format('M d, Y · h:i A') }}</div>
                    </div>

                    <div class="sari-order-product">
                        <div class="sari-order-thumb">
                            @if ($productId > 0)
                                <img src="{{ route('buyer.product.image', ['product' => $productId]) }}" alt="{{ $productName }}" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='grid';">
                                <span class="sari-order-thumb-fallback" style="display:none;">SARI</span>
                            @else
                                <span class="sari-order-thumb-fallback">SARI</span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="sari-order-product-name">{{ $productName }}</div>
                            <div class="sari-order-product-meta">
                                Qty {{ $productQty }}@if ($variantLabel !== '') · {{ $variantLabel }}@endif
                                @if ($moreItems > 0) · +{{ $moreItems }} more @endif
                            </div>
                        </div>
                    </div>

                    <div class="sari-order-total">₱{{ number_format((float) $order->total, 2) }}</div>
                    <div class="sari-order-status {{ $statusClass($order->status) }}">{{ $order->statusLabel() }}</div>
                    <a href="{{ route('buyer.orders.show', $order) }}" class="sari-order-open" wire:navigate.hover>View order</a>
                </article>
            @empty
                <div class="sari-recent-empty">
                    <div>
                        <h2>No recent orders yet</h2>
                        <p>Your latest purchases will appear here after checkout.</p>
                        <a href="{{ route('buyer.products') }}" wire:navigate.hover>Browse Products</a>
                    </div>
                </div>
            @endforelse
        </section>
    </div>
</div>
@endsection
