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

<style>
    .sari-activity-page {
        --gold: #c88912;
        --gold-dark: #a96f0e;
        --ink: #202832;
        --muted: #7d8793;
        --line: #e1e6ea;
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        color: var(--ink);
    }

    .sari-activity-shell {
        width: min(1180px, calc(100% - 32px));
        margin: 0 auto;
        padding: 24px 0 42px;
    }

    .sari-activity-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 18px;
    }

    .sari-activity-eyebrow {
        margin: 0 0 5px;
        color: #a87316;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .sari-activity-title {
        margin: 0;
        color: #202832;
        font-size: clamp(30px, 3vw, 38px);
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -.04em;
    }

    .sari-activity-link {
        color: #333b45;
        font-size: 11px;
        font-weight: 650;
        text-decoration: none;
    }

    .sari-activity-link:hover { color: var(--gold-dark); }

    .sari-activity-panel {
        overflow: hidden;
        border: 1px solid var(--line);
        border-radius: 14px;
        background: #fff;
    }

    .sari-activity-row {
        position: relative;
        display: grid;
        grid-template-columns: 22px minmax(0, 1fr) 170px;
        gap: 15px;
        padding: 18px 20px;
    }

    .sari-activity-row + .sari-activity-row { border-top: 1px solid #edf0f3; }

    .sari-activity-rail {
        position: relative;
        min-height: 48px;
    }

    .sari-activity-dot {
        position: absolute;
        top: 4px;
        left: 5px;
        z-index: 2;
        width: 10px;
        height: 10px;
        border: 2px solid #c88912;
        border-radius: 50%;
        background: #fff;
    }

    .sari-activity-dot.is-shipping { border-color: #5d7f98; }
    .sari-activity-dot.is-delivered { border-color: #4f8663; }
    .sari-activity-dot.is-cancelled { border-color: #ef4444; }

    .sari-activity-row:not(:last-child) .sari-activity-rail::after {
        content: '';
        position: absolute;
        left: 9.5px;
        top: 17px;
        bottom: -35px;
        width: 1px;
        background: #e2e7eb;
    }

    .sari-activity-main { min-width: 0; }

    .sari-activity-event-title {
        margin: 0;
        color: #29323c;
        font-size: 12px;
        font-weight: 750;
        line-height: 1.35;
    }

    .sari-activity-event-message {
        margin: 5px 0 0;
        color: #737e8a;
        font-size: 10px;
        line-height: 1.55;
    }

    .sari-activity-event-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 5px 10px;
        margin-top: 9px;
        color: #929aa3;
        font-size: 9px;
    }

    .sari-activity-side {
        display: flex;
        align-items: flex-end;
        flex-direction: column;
        justify-content: space-between;
        gap: 12px;
        text-align: right;
    }

    .sari-activity-time { color: #8a949f; font-size: 9px; line-height: 1.4; }
    .sari-activity-open { color: #303943; font-size: 10px; font-weight: 700; text-decoration: none; }
    .sari-activity-open:hover { color: var(--gold-dark); }

    .sari-activity-empty {
        min-height: 380px;
        display: grid;
        place-items: center;
        padding: 42px 20px;
        text-align: center;
    }

    .sari-activity-empty h2 { margin: 0; color: #4f5964; font-size: 17px; font-weight: 750; }
    .sari-activity-empty p { margin: 7px 0 0; color: #98a0a9; font-size: 10px; }

    @media (max-width: 700px) {
        .sari-activity-shell { width: min(100% - 24px, 1180px); padding-top: 18px; }
        .sari-activity-head { align-items: flex-start; }
        .sari-activity-row { grid-template-columns: 20px minmax(0, 1fr); padding: 16px; }
        .sari-activity-side { grid-column: 2; align-items: flex-start; flex-direction: row; text-align: left; }
    }
</style>

<div class="sari-activity-page">
    <div class="sari-activity-shell">
        <header class="sari-activity-head">
            <div>
                <p class="sari-activity-eyebrow">Order timeline</p>
                <h1 class="sari-activity-title">Recent Activity</h1>
            </div>
            <a href="{{ route('buyer.notifications') }}" class="sari-activity-link" wire:navigate.hover>Notifications →</a>
        </header>

        <section class="sari-activity-panel">
            @forelse ($recentActivity as $event)
                @php
                    $order = $event->order;
                    $storeName = trim((string) ($event->seller?->store_name ?? 'SARI Seller')) ?: 'SARI Seller';
                @endphp

                <article class="sari-activity-row">
                    <div class="sari-activity-rail" aria-hidden="true">
                        <span class="sari-activity-dot {{ $eventTone($event->type, $event->status) }}"></span>
                    </div>

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
                        <time class="sari-activity-time">{{ optional($event->created_at)->format('M d, Y') }}<br>{{ optional($event->created_at)->format('h:i A') }}</time>
                        @if ($order)
                            <a href="{{ route('buyer.orders.show', $order) }}" class="sari-activity-open" wire:navigate.hover>View order</a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="sari-activity-empty">
                    <div>
                        <h2>No recent activity yet</h2>
                        <p>Order updates will appear here as your purchases move through fulfillment and delivery.</p>
                    </div>
                </div>
            @endforelse
        </section>
    </div>
</div>
@endsection
