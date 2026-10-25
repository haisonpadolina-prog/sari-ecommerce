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

<div
    data-sari-recent-orders-page
    class="mx-auto w-full"
    style="font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;"
>
    <style>
        [data-sari-recent-orders-page] {
            --orders-panel:#ffffff;
            --orders-border:#e2e6ea;
            --orders-border-soft:#eceff2;
            --orders-text:#303740;
            --orders-muted:#7b8490;
            --orders-gold:#c88912;
            --orders-gold-dark:#a96f0e;

            width:100%;
            max-width:1400px;
            margin-inline:auto;
            padding:22px 24px 42px;
            background:transparent;
            color:var(--orders-text);
        }

        /* HEADER — MATCHES CURRENT CART */
        .sari-recent-orders-header {
            padding:2px 2px 14px;
        }

        .sari-recent-orders-header-inner {
            display:flex;
            align-items:flex-end;
            justify-content:space-between;
            gap:16px;
        }

        .sari-recent-orders-eyebrow {
            margin:0;
            color:#a87316;
            font-size:10.5px;
            font-weight:800;
            letter-spacing:.14em;
            line-height:1.2;
            text-transform:uppercase;
        }

        .sari-recent-orders-title {
            margin:5px 0 0;
            color:#2e343b;
            font-size:clamp(27px,2.25vw,31px);
            font-weight:760;
            line-height:1.04;
            letter-spacing:-.035em;
        }

        .sari-recent-orders-title span {
            color:var(--orders-gold);
        }

        .sari-recent-orders-link {
            position:relative;
            top:-12px;
            display:inline-flex;
            align-items:center;
            gap:8px;
            color:#59636e;
            font-size:11.5px;
            font-weight:750;
            line-height:1.35;
            text-decoration:none;
            transition:color .14s ease;
        }

        .sari-recent-orders-link .arrow {
            color:currentColor;
            font-size:18px;
            font-weight:800;
            line-height:.8;
            transform:translateY(-1px);
        }

        .sari-recent-orders-link:hover,
        .sari-recent-orders-link:focus-visible {
            color:var(--orders-gold);
            outline:none;
        }

        /* SUMMARY — FLOATING CART SURFACES */
        .sari-recent-orders-stats {
            display:grid;
            grid-template-columns:repeat(4,minmax(0,1fr));
            gap:12px;
            margin-top:10px;
        }

        .sari-recent-orders-stat {
            border:1px solid var(--orders-border);
            border-radius:13px;
            background:#fff;
            padding:14px 16px;
            box-shadow:
                0 8px 22px rgba(31,41,55,.042),
                0 2px 6px rgba(31,41,55,.018);
        }

        .sari-recent-orders-stat span {
            display:block;
            color:#7d8690;
            font-size:9px;
            font-weight:700;
            line-height:1.3;
        }

        .sari-recent-orders-stat strong {
            display:block;
            margin-top:7px;
            color:#252c34;
            font-size:24px;
            font-weight:850;
            line-height:1;
            letter-spacing:-.035em;
        }

        .sari-recent-orders-stat.is-active strong { color:#a96f0e; }
        .sari-recent-orders-stat.is-delivered strong { color:#4f8065; }
        .sari-recent-orders-stat.is-cancelled strong { color:#dc4b4b; }

        /* ORDER LIST — SAME SELLER-SHELL / INNER-CARD LANGUAGE AS CART */
        .sari-recent-orders-shell {
            overflow:hidden;
            margin-top:14px;
            border:1px solid var(--orders-border);
            border-radius:14px;
            background:#fff;
            box-shadow:
                0 12px 30px rgba(31,41,55,.052),
                0 2px 7px rgba(31,41,55,.022);
        }

        .sari-recent-orders-toolbar {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            border-bottom:1px solid var(--orders-border-soft);
            background:#fff;
            padding:14px 16px;
        }

        .sari-recent-orders-toolbar h2 {
            margin:0;
            color:#303740;
            font-size:16px;
            font-weight:800;
            line-height:1.2;
        }

        .sari-recent-orders-count {
            color:#59636e;
            font-size:10.5px;
            font-weight:650;
            line-height:1.3;
        }

        .sari-recent-orders-list {
            display:grid;
            gap:10px;
            background:#f8f9fb;
            padding:12px;
        }

        .sari-recent-order-card {
            display:grid;
            grid-template-columns:minmax(190px,1.1fr) minmax(300px,1.7fr) 130px 150px 100px;
            align-items:center;
            gap:16px;
            min-height:104px;
            border:1px solid #e4e8ec;
            border-radius:12px;
            background:#fff;
            padding:14px 15px;
            box-shadow:
                0 5px 14px rgba(31,41,55,.032),
                0 1px 4px rgba(31,41,55,.014);
            transition:border-color .14s ease,box-shadow .14s ease;
        }

        .sari-recent-order-card:hover {
            border-color:#d8dde2;
            box-shadow:
                0 8px 18px rgba(31,41,55,.045),
                0 2px 6px rgba(31,41,55,.018);
        }

        .sari-order-number {
            color:#27313b;
            font-size:13px;
            font-weight:800;
            line-height:1.35;
        }

        .sari-order-meta {
            margin-top:5px;
            color:#7f8994;
            font-size:10px;
            font-weight:500;
            line-height:1.5;
        }

        .sari-order-product {
            display:flex;
            min-width:0;
            align-items:center;
            gap:13px;
        }

        .sari-order-thumb {
            width:72px;
            height:72px;
            flex:0 0 72px;
            overflow:hidden;
            border:1px solid #e5e9ed;
            border-radius:11px;
            background:#eef1f4;
        }

        .sari-order-thumb img {
            display:block;
            width:100%;
            height:100%;
            object-fit:cover;
            object-position:center;
        }

        .sari-order-thumb-fallback {
            display:grid;
            width:100%;
            height:100%;
            place-items:center;
            color:#9ba4ae;
            font-size:10px;
            font-weight:800;
            letter-spacing:.05em;
        }

        .sari-order-product-name {
            overflow:hidden;
            color:#1f2933;
            font-size:14px;
            font-weight:700;
            line-height:1.4;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        .sari-order-product-meta {
            margin-top:6px;
            color:#66727f;
            font-size:10.5px;
            font-weight:500;
            line-height:1.45;
        }

        .sari-order-total {
            color:#b6760b;
            font-size:16px;
            font-weight:850;
            line-height:1.15;
            white-space:nowrap;
        }

        .sari-order-status {
            font-size:11px;
            font-weight:750;
            line-height:1.4;
        }

        .sari-order-status.is-active { color:#9a6811; }
        .sari-order-status.is-shipping { color:#4f7189; }
        .sari-order-status.is-delivered { color:#4f8065; }
        .sari-order-status.is-cancelled { color:#dc4b4b; }

        .sari-order-open {
            display:inline-flex;
            align-items:center;
            justify-content:flex-end;
            gap:6px;
            color:#303943;
            font-size:10.5px;
            font-weight:750;
            line-height:1.3;
            text-decoration:none;
            white-space:nowrap;
            transition:color .14s ease;
        }

        .sari-order-open span {
            color:currentColor;
            font-size:15px;
            line-height:.8;
        }

        .sari-order-open:hover,
        .sari-order-open:focus-visible {
            color:var(--orders-gold);
            outline:none;
        }

        /* EMPTY STATE — MATCHES CART */
        .sari-recent-orders-empty-state {
            display:grid;
            min-height:430px;
            margin-top:10px;
            place-items:center;
            border:1px solid #e3e7eb;
            border-radius:16px;
            background:#fff;
            padding:72px 24px;
            text-align:center;
            box-shadow:
                0 10px 28px rgba(31,41,55,.045),
                0 2px 7px rgba(31,41,55,.018);
        }

        .sari-recent-orders-empty-icon {
            display:grid;
            width:92px;
            height:92px;
            margin:0 auto;
            place-items:center;
            color:#b4bbc3;
        }

        .sari-recent-orders-empty-icon svg {
            width:58px;
            height:58px;
            fill:none;
            stroke:currentColor;
            stroke-width:1.45;
            stroke-linecap:round;
            stroke-linejoin:round;
        }

        .sari-recent-orders-empty-title {
            margin:18px 0 0;
            color:#9ca4ad;
            font-size:22px;
            font-weight:750;
            line-height:1.2;
            letter-spacing:-.025em;
        }

        .sari-recent-orders-empty-copy {
            max-width:520px;
            margin:7px auto 0;
            color:#b0b7bf;
            font-size:12px;
            font-weight:500;
            line-height:1.55;
        }

        .sari-recent-orders-empty-button {
            display:inline-flex;
            min-width:170px;
            height:48px;
            align-items:center;
            justify-content:center;
            margin-top:24px;
            border:1px solid var(--orders-gold);
            border-radius:11px;
            background:var(--orders-gold);
            padding:0 26px;
            color:#fff;
            font-size:11px;
            font-weight:800;
            line-height:1;
            text-decoration:none;
            box-shadow:none;
            transition:background-color .14s ease,border-color .14s ease;
        }

        .sari-recent-orders-empty-button:hover,
        .sari-recent-orders-empty-button:focus-visible {
            border-color:#ad760f;
            background:#ad760f;
            color:#fff;
            outline:none;
        }

        @media (max-width:1120px) {
            .sari-recent-order-card {
                grid-template-columns:minmax(180px,1fr) minmax(260px,1.5fr) 120px 130px;
            }

            .sari-order-open {
                grid-column:4;
                justify-content:flex-start;
            }
        }

        @media (max-width:820px) {
            .sari-recent-orders-stats {
                grid-template-columns:repeat(2,minmax(0,1fr));
            }

            .sari-recent-order-card {
                grid-template-columns:1fr;
                gap:12px;
            }

            .sari-order-open {
                grid-column:auto;
                justify-content:flex-start;
            }
        }

        @media (max-width:640px) {
            [data-sari-recent-orders-page] {
                padding:16px 14px 28px;
            }

            .sari-recent-orders-header {
                padding-bottom:12px;
            }

            .sari-recent-orders-header-inner {
                align-items:flex-start;
                flex-direction:column;
                gap:12px;
            }

            .sari-recent-orders-eyebrow {
                font-size:9.5px;
            }

            .sari-recent-orders-title {
                font-size:25px;
            }

            .sari-recent-orders-link {
                top:0;
                font-size:10.5px;
            }

            .sari-recent-orders-stats {
                gap:9px;
            }

            .sari-recent-orders-stat {
                padding:12px 13px;
            }

            .sari-recent-orders-stat strong {
                font-size:21px;
            }

            .sari-recent-orders-toolbar {
                padding:13px 14px;
            }

            .sari-recent-orders-toolbar h2 {
                font-size:15px;
            }

            .sari-recent-orders-list {
                padding:10px;
            }

            .sari-recent-order-card {
                padding:12px;
            }

            .sari-order-thumb {
                width:64px;
                height:64px;
                flex-basis:64px;
            }

            .sari-order-product-name {
                font-size:13px;
            }

            .sari-recent-orders-empty-state {
                min-height:380px;
                padding:56px 20px;
            }

            .sari-recent-orders-empty-icon {
                width:64px;
                height:64px;
            }

            .sari-recent-orders-empty-icon svg {
                width:40px;
                height:40px;
            }

            .sari-recent-orders-empty-title {
                font-size:20px;
            }

            .sari-recent-orders-empty-copy {
                font-size:11px;
            }

            .sari-recent-orders-empty-button {
                min-width:160px;
                height:46px;
                font-size:10.5px;
            }
        }
    </style>

    <header class="sari-recent-orders-header">
        <div class="sari-recent-orders-header-inner">
            <div>
                <p class="sari-recent-orders-eyebrow">SARI Marketplace</p>
                <h1 class="sari-recent-orders-title">Recent <span>Orders</span></h1>
            </div>

            <a href="{{ route('buyer.orders') }}" class="sari-recent-orders-link" wire:navigate.hover>
                View all orders <span class="arrow" aria-hidden="true">&gt;</span>
            </a>
        </div>
    </header>

    @if ($recentOrders->isEmpty())
        <section class="sari-recent-orders-empty-state">
            <div>
                <span class="sari-recent-orders-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M5 7h14"></path>
                        <path d="M7 7l1 12h8l1-12"></path>
                        <path d="M9 4h6l1 3H8z"></path>
                        <path d="M9.5 11h5"></path>
                        <path d="M9.5 14h5"></path>
                    </svg>
                </span>

                <h2 class="sari-recent-orders-empty-title">No recent orders yet</h2>
                <p class="sari-recent-orders-empty-copy">Your latest purchases will appear here after checkout.</p>

                <a href="{{ route('buyer.products') }}" class="sari-recent-orders-empty-button" wire:navigate.hover>
                    Browse Products
                </a>
            </div>
        </section>
    @else
        <section class="sari-recent-orders-stats" aria-label="Recent order summary">
            <div class="sari-recent-orders-stat">
                <span>Recent</span>
                <strong>{{ $recentOrders->count() }}</strong>
            </div>
            <div class="sari-recent-orders-stat is-active">
                <span>Active</span>
                <strong>{{ $activeCount }}</strong>
            </div>
            <div class="sari-recent-orders-stat is-delivered">
                <span>Delivered</span>
                <strong>{{ $deliveredCount }}</strong>
            </div>
            <div class="sari-recent-orders-stat is-cancelled">
                <span>Cancelled</span>
                <strong>{{ $cancelledCount }}</strong>
            </div>
        </section>

        <section class="sari-recent-orders-shell">
            <header class="sari-recent-orders-toolbar">
                <h2>Latest purchases</h2>
                <span class="sari-recent-orders-count">{{ $recentOrders->count() }} order{{ $recentOrders->count() === 1 ? '' : 's' }}</span>
            </header>

            <div class="sari-recent-orders-list">
                @foreach ($recentOrders as $order)
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

                    <article class="sari-recent-order-card">
                        <div>
                            <div class="sari-order-number">{{ $order->order_number }}</div>
                            <div class="sari-order-meta">
                                {{ $storeName }} · {{ optional($order->created_at)->format('M d, Y · h:i A') }}
                            </div>
                        </div>

                        <div class="sari-order-product">
                            <div class="sari-order-thumb">
                                @if ($productId > 0)
                                    <img
                                        src="{{ route('buyer.product.image', ['product' => $productId]) }}"
                                        alt="{{ $productName }}"
                                        loading="lazy"
                                        onerror="this.style.display='none';this.nextElementSibling.style.display='grid';"
                                    >
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

                        <div class="sari-order-status {{ $statusClass($order->status) }}">
                            {{ $order->statusLabel() }}
                        </div>

                        <a href="{{ route('buyer.orders.show', $order) }}" class="sari-order-open" wire:navigate.hover>
                            View order <span aria-hidden="true">&gt;</span>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
