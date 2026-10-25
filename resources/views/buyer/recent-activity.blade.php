@extends('layouts.buyer')

@section('title', 'Recent Activity — SARI')
@section('page-title', 'Recent Activity')

@section('content')
@include('components.buyer.header')

@php
    $eventTone = static function (?string $type, ?string $status): string {
        $key = $type ?: $status;

        return match ($key) {
            'delivered' => 'is-delivered',
            'cancelled' => 'is-cancelled',
            'in_transit', 'arrived_buyer', 'courier_accepted', 'heading_pickup', 'arrived_pickup', 'ready_for_pickup' => 'is-shipping',
            default => 'is-active',
        };
    };

    $friendlyTitle = static function ($event): string {
        return match ($event->type) {
            'new' => 'Order placed',
            'preparing' => 'Order is being prepared',
            'ready_for_pickup' => 'Ready for pickup',
            'courier_accepted' => 'Courier assigned',
            'heading_pickup' => 'Courier heading to seller',
            'arrived_pickup' => 'Courier arrived at seller',
            'in_transit' => 'Order is in transit',
            'arrived_buyer' => 'Courier arrived at your location',
            'delivered' => 'Order delivered',
            'cancelled' => 'Order cancelled',
            default => trim((string) $event->title) ?: 'Order updated',
        };
    };

    $friendlyMessage = static function ($event): string {
        $orderNumber = $event->order?->order_number ?: 'your order';
        $store = trim((string) ($event->seller?->store_name ?? 'the seller')) ?: 'the seller';

        return match ($event->type) {
            'new' => "{$orderNumber} was placed successfully.",
            'preparing' => "{$store} started preparing {$orderNumber}.",
            'ready_for_pickup' => "{$orderNumber} is ready for courier pickup.",
            'courier_accepted' => "A courier was assigned to {$orderNumber}.",
            'heading_pickup' => "The courier is heading to {$store} for {$orderNumber}.",
            'arrived_pickup' => "The courier arrived at {$store} for {$orderNumber}.",
            'in_transit' => "{$orderNumber} is on the way to your delivery address.",
            'arrived_buyer' => "The courier has arrived for {$orderNumber}.",
            'delivered' => "{$orderNumber} was marked as delivered.",
            'cancelled' => "{$orderNumber} was cancelled.",
            default => trim((string) $event->message) ?: "{$orderNumber} was updated.",
        };
    };
@endphp

<div
    data-sari-activity-page
    class="mx-auto w-full"
    style="font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;"
>
    <style>
        [data-sari-activity-page] {
            --activity-page:#f5f6f8;
            --activity-panel:#ffffff;
            --activity-border:#e2e6ea;
            --activity-border-soft:#eceff2;
            --activity-text:#303740;
            --activity-muted:#7b8490;
            --activity-gold:#c88912;
            --activity-gold-dark:#a96f0e;

            width:100%;
            max-width:1400px;
            margin-inline:auto;
            padding:22px 24px 42px;
            background:transparent;
            color:var(--activity-text);
        }

        /* CART-MATCHED OPEN HEADER */
        .sari-activity-header {
            padding:2px 2px 14px;
        }

        .sari-activity-header-inner {
            display:flex;
            align-items:flex-end;
            justify-content:space-between;
            gap:16px;
        }

        .sari-activity-eyebrow {
            margin:0;
            color:#a87316;
            font-size:10.5px;
            font-weight:800;
            letter-spacing:.14em;
            line-height:1.2;
            text-transform:uppercase;
        }

        .sari-activity-title {
            margin:5px 0 0;
            color:#2e343b;
            font-size:clamp(27px,2.25vw,31px);
            font-weight:760;
            line-height:1.04;
            letter-spacing:-.035em;
        }

        .sari-activity-title span {
            color:var(--activity-gold);
        }

        .sari-activity-notifications-link {
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

        .sari-activity-notifications-link span {
            color:currentColor;
            font-size:18px;
            font-weight:800;
            line-height:.8;
            transform:translateY(-1px);
        }

        .sari-activity-notifications-link:hover,
        .sari-activity-notifications-link:focus-visible {
            color:var(--activity-gold);
            outline:none;
        }

        /* EMPTY STATE — SAME SCALE AS CART */
        .sari-activity-empty-state {
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

        .sari-activity-empty-icon {
            display:grid;
            width:92px;
            height:92px;
            margin:0 auto;
            place-items:center;
            color:#b4bbc3;
        }

        .sari-activity-empty-icon svg {
            width:58px;
            height:58px;
            fill:none;
            stroke:currentColor;
            stroke-width:1.45;
            stroke-linecap:round;
            stroke-linejoin:round;
        }

        .sari-activity-empty-title {
            margin:18px 0 0;
            color:#9ca4ad;
            font-size:22px;
            font-weight:750;
            line-height:1.2;
            letter-spacing:-.025em;
        }

        .sari-activity-empty-copy {
            max-width:520px;
            margin:7px auto 0;
            color:#b0b7bf;
            font-size:12px;
            font-weight:500;
            line-height:1.55;
        }

        .sari-activity-empty-button {
            display:inline-flex;
            min-width:170px;
            height:48px;
            align-items:center;
            justify-content:center;
            margin-top:24px;
            border:1px solid var(--activity-gold);
            border-radius:11px;
            background:var(--activity-gold);
            padding:0 26px;
            color:#fff;
            font-size:11px;
            font-weight:800;
            line-height:1;
            text-decoration:none;
            box-shadow:none;
            transition:background-color .14s ease,border-color .14s ease;
        }

        .sari-activity-empty-button:hover,
        .sari-activity-empty-button:focus-visible {
            border-color:#ad760f;
            background:#ad760f;
            color:#fff;
            outline:none;
        }

        /* ACTIVITY CONTAINER — CART SELLER-SHELL LANGUAGE */
        .sari-activity-shell {
            overflow:hidden;
            margin-top:10px;
            border:1px solid var(--activity-border);
            border-radius:14px;
            background:#fff;
            box-shadow:
                0 12px 30px rgba(31,41,55,.052),
                0 2px 7px rgba(31,41,55,.022);
        }

        .sari-activity-toolbar {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            border-bottom:1px solid var(--activity-border-soft);
            background:#fff;
            padding:14px 16px;
        }

        .sari-activity-toolbar h2 {
            margin:0;
            color:#303740;
            font-size:16px;
            font-weight:800;
            line-height:1.2;
        }

        .sari-activity-count {
            color:#59636e;
            font-size:10.5px;
            font-weight:650;
            line-height:1.3;
        }

        .sari-activity-items {
            display:grid;
            gap:10px;
            background:#f8f9fb;
            padding:12px;
        }

        .sari-activity-row {
            display:grid;
            grid-template-columns:42px minmax(0,1fr) 170px;
            gap:14px;
            align-items:center;
            border:1px solid #e4e8ec;
            border-radius:12px;
            background:#fff;
            padding:14px 15px;
            box-shadow:
                0 5px 14px rgba(31,41,55,.032),
                0 1px 4px rgba(31,41,55,.014);
            transition:border-color .16s ease,box-shadow .16s ease;
        }

        .sari-activity-row:hover {
            border-color:#d8dde2;
            box-shadow:
                0 8px 18px rgba(31,41,55,.045),
                0 2px 6px rgba(31,41,55,.018);
        }

        .sari-activity-status {
            display:grid;
            width:38px;
            height:38px;
            place-items:center;
            border:1px solid #e0e4e8;
            border-radius:10px;
            background:#f7f8fa;
            color:#9b6710;
        }

        .sari-activity-status svg {
            width:18px;
            height:18px;
            fill:none;
            stroke:currentColor;
            stroke-width:1.8;
            stroke-linecap:round;
            stroke-linejoin:round;
        }

        .sari-activity-status.is-shipping {
            border-color:#d9e2e8;
            background:#f6f9fb;
            color:#60798d;
        }

        .sari-activity-status.is-delivered {
            border-color:#d7e5dc;
            background:#f5faf7;
            color:#4f8065;
        }

        .sari-activity-status.is-cancelled {
            border-color:#f0d6d6;
            background:#fff7f7;
            color:#dc4c4c;
        }

        .sari-activity-main {
            min-width:0;
        }

        .sari-activity-event-title {
            margin:0;
            color:#1f2933;
            font-size:14px;
            font-weight:700;
            line-height:1.35;
            letter-spacing:-.012em;
        }

        .sari-activity-event-message {
            margin:5px 0 0;
            color:#66727e;
            font-size:11px;
            font-weight:500;
            line-height:1.55;
        }

        .sari-activity-event-meta {
            display:flex;
            flex-wrap:wrap;
            gap:5px 12px;
            margin-top:8px;
            color:#8a939d;
            font-size:9.5px;
            font-weight:550;
            line-height:1.4;
        }

        .sari-activity-side {
            display:flex;
            min-width:0;
            align-items:flex-end;
            flex-direction:column;
            justify-content:space-between;
            gap:12px;
            text-align:right;
        }

        .sari-activity-time {
            color:#7f8994;
            font-size:9.5px;
            font-weight:550;
            line-height:1.45;
        }

        .sari-activity-open {
            display:inline-flex;
            min-height:36px;
            align-items:center;
            justify-content:center;
            border:1px solid #d8b66e;
            border-radius:10px;
            background:#fff;
            padding:0 13px;
            color:#8f5d0b;
            font-size:9.5px;
            font-weight:750;
            line-height:1;
            text-decoration:none;
            transition:border-color .14s ease,background-color .14s ease,color .14s ease;
        }

        .sari-activity-open:hover,
        .sari-activity-open:focus-visible {
            border-color:var(--activity-gold);
            background:var(--activity-gold);
            color:#fff;
            outline:none;
        }

        @media (max-width:767px) {
            [data-sari-activity-page] {
                padding:16px 14px 28px;
            }

            .sari-activity-header {
                padding-bottom:12px;
            }

            .sari-activity-header-inner {
                align-items:flex-start;
                flex-direction:column;
            }

            .sari-activity-eyebrow {
                font-size:9.5px;
            }

            .sari-activity-title {
                font-size:25px;
            }

            .sari-activity-notifications-link {
                top:0;
                font-size:10.5px;
            }

            .sari-activity-empty-state {
                min-height:360px;
                padding:56px 20px;
            }

            .sari-activity-empty-icon {
                width:64px;
                height:64px;
            }

            .sari-activity-empty-icon svg {
                width:40px;
                height:40px;
            }

            .sari-activity-empty-title {
                font-size:20px;
            }

            .sari-activity-empty-copy {
                font-size:11px;
            }

            .sari-activity-empty-button {
                min-width:160px;
                height:46px;
                font-size:10.5px;
            }

            .sari-activity-toolbar {
                padding:13px 14px;
            }

            .sari-activity-toolbar h2 {
                font-size:15px;
            }

            .sari-activity-row {
                grid-template-columns:36px minmax(0,1fr);
                gap:12px;
                padding:13px;
            }

            .sari-activity-status {
                width:34px;
                height:34px;
                border-radius:9px;
            }

            .sari-activity-side {
                grid-column:2;
                align-items:center;
                flex-direction:row;
                text-align:left;
            }

            .sari-activity-event-title {
                font-size:13px;
            }

            .sari-activity-event-message {
                font-size:10.5px;
            }
        }
    </style>

    <section class="sari-activity-header">
        <div class="sari-activity-header-inner">
            <div>
                <p class="sari-activity-eyebrow">SARI Marketplace</p>
                <h1 class="sari-activity-title">Recent <span>Activity</span></h1>
            </div>

            <a
                href="{{ route('buyer.notifications') }}"
                class="sari-activity-notifications-link"
                wire:navigate.hover
            >
                View Notifications <span aria-hidden="true">&gt;</span>
            </a>
        </div>
    </section>

    @if ($recentActivity->isEmpty())
        <section class="sari-activity-empty-state">
            <div>
                <span class="sari-activity-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 8v5l3 2"></path>
                        <path d="M3.05 11a9 9 0 1 0 .5-3"></path>
                        <path d="M3 4v4h4"></path>
                    </svg>
                </span>

                <h2 class="sari-activity-empty-title">No recent activity yet</h2>
                <p class="sari-activity-empty-copy">
                    Order updates will appear here as your purchases move through fulfillment and delivery.
                </p>

                <a
                    href="{{ route('buyer.products') }}"
                    class="sari-activity-empty-button"
                    wire:navigate.hover
                >
                    Browse Products
                </a>
            </div>
        </section>
    @else
        <section class="sari-activity-shell">
            <header class="sari-activity-toolbar">
                <h2>Order Activity</h2>
                <span class="sari-activity-count">
                    {{ $recentActivity->count() }} update{{ $recentActivity->count() === 1 ? '' : 's' }}
                </span>
            </header>

            <div class="sari-activity-items">
                @foreach ($recentActivity as $event)
                    @php
                        $order = $event->order;
                        $storeName = trim((string) ($event->seller?->store_name ?? 'SARI Seller')) ?: 'SARI Seller';
                        $tone = $eventTone($event->type, $event->status);
                    @endphp

                    <article class="sari-activity-row">
                        <span class="sari-activity-status {{ $tone }}" aria-hidden="true">
                            @if ($tone === 'is-delivered')
                                <svg viewBox="0 0 24 24"><path d="m5 12 4 4 10-10"></path></svg>
                            @elseif ($tone === 'is-cancelled')
                                <svg viewBox="0 0 24 24"><path d="m7 7 10 10"></path><path d="m17 7-10 10"></path></svg>
                            @elseif ($tone === 'is-shipping')
                                <svg viewBox="0 0 24 24"><path d="M3 7h11v9H3z"></path><path d="M14 10h4l3 3v3h-7z"></path><circle cx="7" cy="18" r="1.4"></circle><circle cx="18" cy="18" r="1.4"></circle></svg>
                            @else
                                <svg viewBox="0 0 24 24"><path d="M6 7h12"></path><path d="M7 4h10l1 16H6z"></path></svg>
                            @endif
                        </span>

                        <div class="sari-activity-main">
                            <h2 class="sari-activity-event-title">{{ $friendlyTitle($event) }}</h2>
                            <p class="sari-activity-event-message">{{ $friendlyMessage($event) }}</p>

                            <div class="sari-activity-event-meta">
                                @if ($order)
                                    <span>{{ $order->order_number }}</span>
                                @endif
                                <span>{{ $storeName }}</span>
                                @if ($event->status)
                                    <span>{{ str($event->status)->replace('_', ' ')->title() }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="sari-activity-side">
                            <time class="sari-activity-time">
                                {{ optional($event->created_at)->format('M d, Y') }}<br>
                                {{ optional($event->created_at)->format('h:i A') }}
                            </time>

                            @if ($order)
                                <a
                                    href="{{ route('buyer.orders.show', $order) }}"
                                    class="sari-activity-open"
                                    wire:navigate.hover
                                >
                                    View order
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
