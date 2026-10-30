@extends('layouts.courier')

@section('title', 'Active Deliveries')
@section('header-title', 'Active Deliveries')
@section('header-subtitle', 'Direct Delivery Operations')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/courier/deliveries.css') }}?v=20261029-2"
    >
@endpush

@section('content')
@php
    $activeDeliveryCount = $orders->count();
    $inTransitCount = $orders->where('status', 'in_transit')->count();
    $atBuyerCount = $orders->where('status', 'arrived_buyer')->count();
@endphp

<div
    id="riderActiveDeliveries"
    class="courier-deliveries-page courier-workspace"
>
    <section class="ad-heading" aria-labelledby="activeDeliveriesTitle">
        <div class="ad-heading-copy">
            <span class="ad-eyebrow">Direct Delivery</span>

            <h2 id="activeDeliveriesTitle" class="ad-title">
                Active <span>Deliveries</span>
            </h2>

            <p class="ad-description">
                Track Seller-to-Buyer direct deliveries through final handoff.
            </p>
        </div>

        <div class="ad-heading-actions">
            <a
                href="{{ route('courier.requests') }}"
                class="ad-button"
            >
                Pickup Assignments
            </a>

            <a
                href="{{ route('courier.last-mile') }}"
                class="ad-button ad-button-gold"
            >
                Last-Mile Deliveries
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </section>

    <section class="ad-summary" aria-label="Active direct-delivery summary">
        <div class="ad-stat">
            <div class="ad-stat-copy">
                <p class="ad-stat-label">Active Direct Deliveries</p>

                <p class="ad-stat-value">
                    {{ $activeDeliveryCount }}
                </p>

                <p class="ad-stat-note">
                    Seller-to-Buyer delivery jobs
                </p>
            </div>

            <span class="ad-stat-icon" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="m12 3 9 5-9 5-9-5 9-5Z"></path>
                    <path d="M3 8v9l9 5 9-5V8M12 13v9"></path>
                </svg>
            </span>
        </div>

        <div class="ad-stat ad-stat-blue">
            <div class="ad-stat-copy">
                <p class="ad-stat-label">In Transit</p>

                <p class="ad-stat-value">
                    {{ $inTransitCount }}
                </p>

                <p class="ad-stat-note">
                    On the way to the Buyer
                </p>
            </div>

            <span class="ad-stat-icon" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M3 6h11v11H3z"></path>
                    <path d="M14 10h4l3 4v3h-7"></path>
                    <circle cx="7" cy="18" r="2"></circle>
                    <circle cx="18" cy="18" r="2"></circle>
                </svg>
            </span>
        </div>

        <div class="ad-stat ad-stat-green">
            <div class="ad-stat-copy">
                <p class="ad-stat-label">At Buyer</p>

                <p class="ad-stat-value">
                    {{ $atBuyerCount }}
                </p>

                <p class="ad-stat-note">
                    Awaiting final handoff
                </p>
            </div>

            <span class="ad-stat-icon" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M12 21s7-6 7-12a7 7 0 1 0-14 0c0 6 7 12 7 12Z"></path>
                    <circle cx="12" cy="9" r="2.5"></circle>
                </svg>
            </span>
        </div>
    </section>

    <section
        class="ad-panel"
        aria-labelledby="directDeliveryQueueTitle"
    >
        <div class="ad-panel-head">
            <div class="ad-panel-heading">
                <span class="ad-heading-icon" aria-hidden="true">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M3 6h11v11H3z"></path>
                        <path d="M14 10h4l3 4v3h-7"></path>
                        <circle cx="7" cy="18" r="2"></circle>
                        <circle cx="18" cy="18" r="2"></circle>
                    </svg>
                </span>

                <div>
                    <h3
                        id="directDeliveryQueueTitle"
                        class="ad-panel-title"
                    >
                        Direct Delivery Queue
                    </h3>

                    <p class="ad-panel-description">
                        Buyer destinations and current delivery stages.
                    </p>
                </div>
            </div>

            <span class="ad-count">
                <strong>{{ $activeDeliveryCount }}</strong>
                {{ $activeDeliveryCount === 1 ? 'delivery' : 'deliveries' }}
            </span>
        </div>

        <div class="ad-delivery-list">
            @forelse($orders as $order)
                <article
                    class="ad-delivery"
                    aria-labelledby="direct-delivery-{{ $order->id }}"
                >
                    <div class="ad-delivery-main">
                        <div class="ad-identity">
                            <h4
                                id="direct-delivery-{{ $order->id }}"
                                class="ad-order-number"
                            >
                                {{ $order->order_number }}
                            </h4>

                            <div class="ad-badges">
                                <span class="ad-badge">
                                    Direct Delivery
                                </span>

                                <span
                                    class="ad-badge {{ $order->status === 'arrived_buyer' ? 'ad-badge-green' : 'ad-badge-blue' }}"
                                >
                                    {{ $order->statusLabel() }}
                                </span>
                            </div>

                            <p class="ad-route">
                                {{ $order->pickup_name ?: 'Seller' }}

                                <span class="ad-route-arrow">→</span>

                                {{ $order->buyer_name ?: 'Buyer' }}
                            </p>
                        </div>

                        <dl class="ad-destination">
                            <dt class="ad-field-label">
                                Buyer Destination
                            </dt>

                            <dd class="ad-field-value">
                                {{ $order->buyer_name ?: 'Buyer' }}

                                <span class="ad-address">
                                    {{
                                        $order->buyer_address
                                            ?: 'Delivery address unavailable'
                                    }}
                                </span>
                            </dd>
                        </dl>

                        <dl class="ad-fee">
                            <dt class="ad-field-label">
                                Delivery Fee
                            </dt>

                            <dd class="ad-fee-value">
                                ₱{{ number_format($order->courierDeliveryFee(), 2) }}
                            </dd>
                        </dl>
                    </div>

                    <div class="ad-delivery-footer">
                        <div class="ad-current-stage">
                            <span>Current stage</span>
                            <strong>{{ $order->statusLabel() }}</strong>
                        </div>

                        <a
                            href="{{ route('courier.delivery.show', ['order' => $order->id]) }}"
                            class="ad-button ad-button-blue"
                            aria-label="Continue direct delivery for {{ $order->order_number }}"
                        >
                            Continue Direct Delivery
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </article>
            @empty
                <div class="ad-empty">
                    <span class="ad-empty-icon" aria-hidden="true">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.6"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M3 6h11v11H3z"></path>
                            <path d="M14 10h4l3 4v3h-7"></path>
                            <circle cx="7" cy="18" r="2"></circle>
                            <circle cx="18" cy="18" r="2"></circle>
                        </svg>
                    </span>

                    <h4 class="ad-empty-title">
                        No active direct deliveries
                    </h4>

                    <p class="ad-empty-description">
                        Check Pickup Assignments for Seller collections,
                        or Last-Mile Deliveries for destination-hub batches.
                    </p>

                    <div class="ad-empty-links">
                        <a
                            href="{{ route('courier.requests') }}"
                            class="ad-text-link"
                        >
                            Pickup assignments
                            <span aria-hidden="true">→</span>
                        </a>

                        <a
                            href="{{ route('courier.last-mile') }}"
                            class="ad-text-link"
                        >
                            Last-mile deliveries
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="ad-panel-footer">
            <p class="ad-footer-copy">
                @if($activeDeliveryCount > 0)
                    Showing {{ $activeDeliveryCount }}
                    {{
                        $activeDeliveryCount === 1
                            ? 'active direct delivery.'
                            : 'active direct deliveries.'
                    }}
                @else
                    Seller-to-Buyer direct delivery workflow.
                @endif
            </p>

            <span class="ad-footer-copy">
                Logistics hub deliveries appear under Last-Mile.
            </span>
        </div>
    </section>
</div>
@endsection