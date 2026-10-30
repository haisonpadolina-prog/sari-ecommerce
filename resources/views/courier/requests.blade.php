@extends('layouts.courier')

@section('title', 'Pickup Assignments')
@section('header-title', 'Pickup Assignments')
@section('header-subtitle', 'Dispatched by SARI Logistics')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/courier/requests.css') }}?v=20261029-2"
    >
@endpush

@section('content')
<div
    id="riderPickupAssignments"
    class="courier-requests-page courier-workspace"
>
    @if($errors->any())
        <div class="rp-alert rp-alert-error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('success'))
        <div class="rp-alert rp-alert-success" role="status">
            {{ session('success') }}
        </div>
    @endif

    <section class="rp-heading" aria-labelledby="pickupPageTitle">
        <div class="rp-heading-copy">
            <span class="rp-eyebrow">Seller Pickup Dispatch</span>

            <h2 id="pickupPageTitle" class="rp-title">
                Pickup <span>Assignments</span>
            </h2>

            <p class="rp-description">
                Collect assigned Seller orders and continue to the next handoff.
            </p>
        </div>

        <a
            href="{{ route('courier.last-mile') }}"
            class="rp-button"
        >
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.7"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M3 6h11v11H3z"></path>
                <path d="M14 10h4l3 4v3h-7"></path>
                <circle cx="7" cy="18" r="2"></circle>
                <circle cx="18" cy="18" r="2"></circle>
            </svg>

            Last-Mile Deliveries
            <span aria-hidden="true">→</span>
        </a>
    </section>

    @if($activeOrder)
        @php
            $activeIsLogistics = (bool) $activeOrder->logisticsParcel;
        @endphp

        <section
            class="rp-panel rp-active"
            aria-labelledby="currentPickupTitle"
        >
            <div class="rp-panel-head">
                <div class="rp-panel-heading">
                    <span class="rp-heading-icon" aria-hidden="true">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M13 3 5 14h6l-1 7 9-12h-6l1-6"></path>
                        </svg>
                    </span>

                    <div>
                        <h3 id="currentPickupTitle" class="rp-panel-title">
                            Current Assignment
                        </h3>

                        <p class="rp-panel-description">
                            Continue your assigned pickup workflow.
                        </p>
                    </div>
                </div>

                <span class="rp-badge rp-badge-blue">
                    {{ $activeOrder->statusLabel() }}
                </span>
            </div>

            <div class="rp-active-main">
                <div class="rp-active-copy">
                    <p class="rp-order-number">
                        {{ $activeOrder->order_number }}
                    </p>

                    <p class="rp-active-status">
                        {{
                            $activeIsLogistics
                                ? 'Seller pickup · Handoff at origin hub'
                                : 'Direct delivery · Handoff to Buyer'
                        }}
                    </p>

                    @if($activeIsLogistics && $activeOrder->status === 'in_transit')
                        <p class="rp-active-note">
                            Pickup completed. Bring the parcel to the assigned
                            origin hub for physical intake by SARI Logistics.
                        </p>
                    @endif
                </div>

                <span class="rp-badge">
                    {{ $activeIsLogistics ? 'Seller Pickup' : 'Direct Delivery' }}
                </span>
            </div>

            <div class="rp-assignment-footer">
                <p class="rp-footer-copy">
                    Review the current stage before continuing.
                </p>

                <a
                    href="{{ route('courier.delivery.show', ['order' => $activeOrder->id]) }}"
                    class="rp-button rp-button-primary"
                >
                    Continue Pickup Workflow
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        </section>
    @endif

    <section
        class="rp-panel"
        aria-labelledby="pickupQueueTitle"
    >
        <div class="rp-panel-head">
            <div class="rp-panel-heading">
                <span class="rp-heading-icon" aria-hidden="true">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M8 5H5v16h14V5h-3"></path>
                        <path d="M9 3h6v4H9z"></path>
                        <path d="M8 12h8M8 16h5"></path>
                    </svg>
                </span>

                <div>
                    <h3 id="pickupQueueTitle" class="rp-panel-title">
                        Assigned Pickup Queue
                    </h3>

                    <p class="rp-panel-description">
                        Seller pickups assigned by SARI Logistics.
                    </p>
                </div>
            </div>

            <span class="rp-count">
                <strong>{{ $orders->count() }}</strong>
                {{ $orders->count() === 1 ? 'assignment' : 'assignments' }}
            </span>
        </div>

        <div class="rp-assignment-list">
            @forelse($orders as $order)
                @php
                    $isLogisticsPickup = (bool) $order->logisticsParcel;
                @endphp

                <article
                    class="rp-assignment"
                    aria-labelledby="pickup-order-{{ $order->id }}"
                >
                    <div class="rp-assignment-main">
                        <div class="rp-order-identity">
                            <p
                                id="pickup-order-{{ $order->id }}"
                                class="rp-order-number"
                            >
                                {{ $order->order_number }}
                            </p>

                            <p class="rp-seller-name">
                                {{
                                    $order->seller?->store_name
                                        ?: $order->pickup_name
                                        ?: 'SARI Seller'
                                }}
                            </p>

                            <div class="rp-badges">
                                <span class="rp-badge rp-badge-gold">
                                    Pickup Assigned
                                </span>

                                <span class="rp-badge">
                                    {{
                                        $isLogisticsPickup
                                            ? 'Seller Pickup'
                                            : 'Direct Delivery'
                                    }}
                                </span>
                            </div>
                        </div>

                        <dl class="rp-locations">
                            <div class="rp-location">
                                <dt>
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        <path d="M3 10h18l-2-6H5l-2 6Z"></path>
                                        <path d="M5 10v10h14V10"></path>
                                        <path d="M9 20v-6h6v6"></path>
                                    </svg>

                                    Seller Pickup
                                </dt>

                                <dd>
                                    <span class="rp-location-name">
                                        {{ $order->pickup_name ?: 'Seller' }}
                                    </span>

                                    <span class="rp-location-address">
                                        {{
                                            $order->pickup_address
                                                ?: 'Pickup address unavailable'
                                        }}
                                    </span>
                                </dd>
                            </div>

                            <div class="rp-location">
                                <dt>
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        <path d="M12 21s7-6 7-12a7 7 0 1 0-14 0c0 6 7 12 7 12Z"></path>
                                        <circle cx="12" cy="9" r="2.5"></circle>
                                    </svg>

                                    Next Handoff
                                </dt>

                                <dd>
                                    <span class="rp-location-name">
                                        {{
                                            $isLogisticsPickup
                                                ? 'Origin Sorting Hub'
                                                : ($order->buyer_name ?: 'Buyer')
                                        }}
                                    </span>

                                    <span class="rp-location-address">
                                        {{
                                            $isLogisticsPickup
                                                ? 'SARI Logistics intake'
                                                : ($order->buyer_address ?: 'Buyer delivery')
                                        }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rp-assignment-footer">
                        @if($isLogisticsPickup)
                            <p class="rp-assignment-note">
                                <strong>Pickup-only assignment</strong>
                                The Marketplace delivery fee belongs to the
                                final-mile Rider, not the Seller pickup Rider.
                            </p>
                        @else
                            <p class="rp-assignment-note">
                                <strong>Direct-delivery assignment</strong>
                                Collect from the Seller and continue to the Buyer.
                            </p>
                        @endif

                        <a
                            href="{{ route('courier.delivery.show', ['order' => $order->id]) }}"
                            class="rp-button rp-button-primary"
                            aria-label="Start pickup workflow for {{ $order->order_number }}"
                        >
                            Start Pickup Workflow
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </article>
            @empty
                <div class="rp-empty">
                    <span class="rp-empty-icon" aria-hidden="true">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.6"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="m12 3 9 5-9 5-9-5 9-5Z"></path>
                            <path d="M3 8v9l9 5 9-5V8"></path>
                            <path d="M12 13v9M7.5 5.5l9 5"></path>
                        </svg>
                    </span>

                    <h4 class="rp-empty-title">
                        No pickup assignments waiting
                    </h4>

                    <p class="rp-empty-description">
                        New Seller pickups will appear here when
                        SARI Logistics assigns them to you.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="rp-panel-footer">
            <p class="rp-footer-copy">
                @if($orders->count() > 0)
                    Showing {{ $orders->count() }}
                    {{ $orders->count() === 1 ? 'assigned pickup.' : 'assigned pickups.' }}
                @else
                    Assigned by SARI Logistics.
                @endif
            </p>

            <a
                href="{{ route('courier.last-mile') }}"
                class="rp-text-link"
            >
                View last-mile deliveries
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentCount = {{ $orders->count() }};

    async function checkAssignments() {
        if (document.hidden) {
            return;
        }

        try {
            const response = await fetch(
                @json(route('courier.orders.live-state')),
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    cache: 'no-store',
                }
            );

            if (!response.ok) {
                return;
            }

            const data = await response.json();

            if (Number(data.assigned_count) !== Number(currentCount)) {
                window.location.reload();
            }
        } catch (error) {
            console.debug(
                'Rider assignment polling temporarily unavailable.'
            );
        }
    }

    window.setInterval(checkAssignments, 3000);
});
</script>
@endpush