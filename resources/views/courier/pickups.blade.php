@extends('layouts.courier')

@section('title', 'Seller Pickups')
@section('header-title', 'Seller Pickups')
@section('header-subtitle', 'Seller Collection Workflow')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/courier/pickups.css') }}?v=20261029-2"
    >
@endpush

@section('content')
@php
    $pickupStages = [
        'courier_accepted' => 'Assigned',
        'heading_pickup' => 'Heading to Seller',
        'arrived_pickup' => 'At Seller',
    ];

    $pickupTotal = $orders->count();
    $pickupAssigned = $orders->where('status', 'courier_accepted')->count();
    $pickupHeading = $orders->where('status', 'heading_pickup')->count();
    $pickupArrived = $orders->where('status', 'arrived_pickup')->count();
@endphp

<div
    id="riderSellerPickups"
    class="courier-pickups-page courier-workspace"
>
    <section class="sp-heading" aria-labelledby="sellerPickupsTitle">
        <div class="sp-heading-copy">
            <span class="sp-eyebrow">Pickup Operations</span>

            <h2 id="sellerPickupsTitle" class="sp-title">
                Seller <span>Pickups</span>
            </h2>

            <p class="sp-description">
                Track Seller collections from assignment to pickup confirmation.
            </p>
        </div>

        <div class="sp-heading-actions">
            <a
                href="{{ route('courier.parcel-scanner') }}"
                class="sp-button"
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
                    <path d="M8 3H3v5M16 3h5v5M3 16v5h5M21 16v5h-5"></path>
                    <path d="M7 7h3v3H7zM14 7h3v3h-3zM7 14h3v3H7z"></path>
                    <path d="M14 14h3v3h-3z"></path>
                </svg>

                Scan Parcel QR
            </a>

            <a
                href="{{ route('courier.requests') }}"
                class="sp-button sp-button-primary"
            >
                Pickup Assignments
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </section>

    <section class="sp-summary" aria-label="Seller pickup summary">
        <div class="sp-stat">
            <div class="sp-stat-copy">
                <p class="sp-stat-label">Total Pickups</p>
                <p class="sp-stat-value">{{ $pickupTotal }}</p>
                <p class="sp-stat-note">Jobs at collection stage</p>
            </div>

            <span class="sp-stat-icon" aria-hidden="true">
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

        <div class="sp-stat sp-stat-gold">
            <div class="sp-stat-copy">
                <p class="sp-stat-label">Assigned</p>
                <p class="sp-stat-value">{{ $pickupAssigned }}</p>
                <p class="sp-stat-note">Waiting to start pickup</p>
            </div>

            <span class="sp-stat-icon" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M8 5H5v16h14V5h-3"></path>
                    <path d="M9 3h6v4H9zM8 12h8M8 16h5"></path>
                </svg>
            </span>
        </div>

        <div class="sp-stat sp-stat-blue">
            <div class="sp-stat-copy">
                <p class="sp-stat-label">Heading to Seller</p>
                <p class="sp-stat-value">{{ $pickupHeading }}</p>
                <p class="sp-stat-note">On the way to collection</p>
            </div>

            <span class="sp-stat-icon" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="m21 3-6 18-4-8-8-4 18-6Z"></path>
                    <path d="m11 13 5-5"></path>
                </svg>
            </span>
        </div>

        <div class="sp-stat sp-stat-green">
            <div class="sp-stat-copy">
                <p class="sp-stat-label">At Seller</p>
                <p class="sp-stat-value">{{ $pickupArrived }}</p>
                <p class="sp-stat-note">Awaiting pickup confirmation</p>
            </div>

            <span class="sp-stat-icon" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M3 10h18l-2-6H5l-2 6Z"></path>
                    <path d="M5 10v10h14V10M9 20v-6h6v6"></path>
                </svg>
            </span>
        </div>
    </section>

    <section class="sp-panel" aria-labelledby="pickupStageTitle">
        <div class="sp-panel-head">
            <div class="sp-panel-heading">
                <span class="sp-heading-icon" aria-hidden="true">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M4 8h16v12H4zM8 8V5h8v3"></path>
                        <path d="M9 13h6"></path>
                    </svg>
                </span>

                <div>
                    <h3 id="pickupStageTitle" class="sp-panel-title">
                        Pickup Stage
                    </h3>

                    <p class="sp-panel-description">
                        Follow each collection through its current stage.
                    </p>
                </div>
            </div>

            <span class="sp-count">
                <strong>{{ $pickupTotal }}</strong>
                {{ $pickupTotal === 1 ? 'pickup' : 'pickups' }}
            </span>
        </div>

        <div class="sp-pickup-list">
            @forelse($orders as $order)
                @php
                    $isLogisticsPickup = (bool) $order->logisticsParcel;

                    $currentStage = array_search(
                        $order->status,
                        array_keys($pickupStages),
                        true
                    );

                    $statusClass = match ($order->status) {
                        'heading_pickup' => 'sp-badge-blue',
                        'arrived_pickup' => 'sp-badge-green',
                        default => '',
                    };
                @endphp

                <article
                    class="sp-pickup"
                    aria-labelledby="seller-pickup-{{ $order->id }}"
                >
                    <div class="sp-pickup-main">
                        <div class="sp-identity">
                            <div class="sp-order-heading">
                                <h4
                                    id="seller-pickup-{{ $order->id }}"
                                    class="sp-order-number"
                                >
                                    {{ $order->order_number }}
                                </h4>

                                <span class="sp-badge {{ $statusClass }}">
                                    {{ $order->statusLabel() }}
                                </span>
                            </div>

                            <p class="sp-seller-name">
                                {{ $order->pickup_name ?: 'SARI Seller' }}
                            </p>

                            @if($currentStage !== false)
                                <ol
                                    class="sp-stages"
                                    aria-label="Pickup stages for {{ $order->order_number }}"
                                >
                                    @foreach($pickupStages as $stageStatus => $stageLabel)
                                        <li
                                            class="sp-stage {{ $loop->index < $currentStage ? 'is-complete' : '' }} {{ $loop->index === $currentStage ? 'is-current' : '' }}"
                                            @if($order->status === $stageStatus)
                                                aria-current="step"
                                            @endif
                                        >
                                            <span
                                                class="sp-stage-marker"
                                                aria-hidden="true"
                                            >
                                                {{ $loop->iteration }}
                                            </span>

                                            <span class="sp-stage-label">
                                                {{ $stageLabel }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif
                        </div>

                        <dl class="sp-details">
                            <div class="sp-detail">
                                <dt>Pickup Address</dt>

                                <dd>
                                    {{
                                        $order->pickup_address
                                            ?: 'Pickup address unavailable'
                                    }}
                                </dd>
                            </div>

                            <div class="sp-detail">
                                <dt>After Pickup</dt>

                                <dd>
                                    <span class="sp-handoff-name">
                                        {{
                                            $isLogisticsPickup
                                                ? 'Origin Hub Intake'
                                                : 'Direct Buyer Delivery'
                                        }}
                                    </span>

                                    <span
                                        class="sp-badge {{ $isLogisticsPickup ? 'sp-badge-blue' : 'sp-badge-green' }}"
                                    >
                                        {{ $isLogisticsPickup ? 'Logistics' : 'Direct' }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="sp-pickup-footer">
                        <p class="sp-footer-note">
                            @if($isLogisticsPickup)
                                Pickup work ends after physical acceptance
                                at the origin hub.
                            @else
                                Continue to Buyer delivery after
                                Seller pickup confirmation.
                            @endif
                        </p>

                        <a
                            href="{{ route('courier.delivery.show', ['order' => $order->id]) }}"
                            class="sp-button sp-button-primary"
                            aria-label="Open pickup workflow for {{ $order->order_number }}"
                        >
                            Open Pickup Workflow
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </article>
            @empty
                <div class="sp-empty">
                    <span class="sp-empty-icon" aria-hidden="true">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.6"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M3 10h18l-2-6H5l-2 6Z"></path>
                            <path d="M5 10v10h14V10"></path>
                            <path d="M9 20v-6h6v6"></path>
                        </svg>
                    </span>

                    <h4 class="sp-empty-title">
                        No Seller pickups in progress
                    </h4>

                    <p class="sp-empty-description">
                        Check Pickup Assignments for newly
                        dispatched Seller collections.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="sp-panel-footer">
            <p class="sp-footer-copy">
                @if($pickupTotal > 0)
                    Showing {{ $pickupTotal }}
                    {{ $pickupTotal === 1 ? 'pickup at collection stage.' : 'pickups at collection stage.' }}
                @else
                    Seller collection workflow.
                @endif
            </p>

            <a
                href="{{ route('courier.requests') }}"
                class="sp-text-link"
            >
                View pickup assignments
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </section>
</div>
@endsection