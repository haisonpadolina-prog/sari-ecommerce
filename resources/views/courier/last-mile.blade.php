@extends('layouts.courier')

@section('title', 'Last-Mile Deliveries')
@section('header-title', 'Last-Mile Deliveries')
@section('header-subtitle', 'Destination Hub → Buyer')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/courier/last-mile.css') }}?v=20261029-2"
    >
@endpush

@section('content')
@php
    $activeAssignments = $assignments->whereIn(
        'status',
        ['assigned', 'out_for_delivery']
    );

    $completedAssignments = $assignments->where('status', 'completed');

    $activeParcelCount = $activeAssignments->sum(
        fn ($assignment) => $assignment->orders->count()
    );
@endphp

<div
    id="riderLastMile"
    class="courier-last-mile-page courier-workspace"
>
    @if(session('success'))
        <div class="lm-alert lm-alert-success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="lm-alert lm-alert-error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="lm-heading" aria-labelledby="lastMilePageTitle">
        <div class="lm-heading-copy">
            <span class="lm-eyebrow">Last-Mile Operations</span>

            <h2 id="lastMilePageTitle" class="lm-title">
                Last-Mile <span>Deliveries</span>
            </h2>

            <p class="lm-description">
                Collect hub-assigned parcels and deliver within your assigned barangay.
            </p>
        </div>

        <div class="lm-heading-actions">
            <a
                href="{{ route('courier.dashboard') }}"
                class="lm-button"
            >
                Back to Dashboard
            </a>

            <a
                href="{{ route('courier.parcel-scanner') }}"
                class="lm-button lm-button-gold"
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
                    <path d="M7 7h3v3H7zM14 7h3v3h-3z"></path>
                    <path d="M7 14h3v3H7zM14 14h3v3h-3z"></path>
                </svg>

                Scan Hub Parcel QR
            </a>
        </div>
    </section>

    <section class="lm-summary" aria-label="Last-mile delivery summary">
        <div class="lm-stat">
            <div class="lm-stat-copy">
                <p class="lm-stat-label">Active Batches</p>

                <p class="lm-stat-value">
                    {{ $activeAssignments->count() }}
                </p>

                <p class="lm-stat-note">
                    Assigned or out for delivery
                </p>
            </div>

            <span class="lm-stat-icon" aria-hidden="true">
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

        <div class="lm-stat lm-stat-blue">
            <div class="lm-stat-copy">
                <p class="lm-stat-label">Parcels in Active Batches</p>

                <p class="lm-stat-value">
                    {{ $activeParcelCount }}
                </p>

                <p class="lm-stat-note">
                    All parcels linked to active batches
                </p>
            </div>

            <span class="lm-stat-icon" aria-hidden="true">
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

        <div class="lm-stat lm-stat-green">
            <div class="lm-stat-copy">
                <p class="lm-stat-label">Completed Batches</p>

                <p class="lm-stat-value">
                    {{ $completedAssignments->count() }}
                </p>

                <p class="lm-stat-note">
                    Batches marked completed
                </p>
            </div>

            <span class="lm-stat-icon" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="m8 12 3 3 5-6"></path>
                </svg>
            </span>
        </div>
    </section>

    <section class="lm-guide" aria-label="Last-mile workflow guide">
        <ol class="lm-guide-list">
            <li class="lm-guide-step">
                <span class="lm-guide-number" aria-hidden="true">1</span>

                <div>
                    <p class="lm-guide-title">Destination Hub</p>
                    <p class="lm-guide-copy">Collect assigned parcels</p>
                </div>
            </li>

            <li class="lm-guide-step">
                <span class="lm-guide-number" aria-hidden="true">2</span>

                <div>
                    <p class="lm-guide-title">Local Delivery</p>
                    <p class="lm-guide-copy">Reach the Buyer location</p>
                </div>
            </li>

            <li class="lm-guide-step">
                <span class="lm-guide-number" aria-hidden="true">3</span>

                <div>
                    <p class="lm-guide-title">Buyer Handoff</p>
                    <p class="lm-guide-copy">Confirm final delivery</p>
                </div>
            </li>
        </ol>
    </section>

    @forelse($assignments as $assignment)
        @php
            $isAssigned = $assignment->status === 'assigned';
            $isOut = $assignment->status === 'out_for_delivery';
            $isCompleted = $assignment->status === 'completed';

            $batchStatusClass = $isCompleted
                ? 'lm-badge-green'
                : ($isOut ? 'lm-badge-blue' : 'lm-badge-gold');
        @endphp

        <article
            class="lm-panel"
            aria-labelledby="last-mile-batch-{{ $assignment->id }}"
        >
            <div class="lm-batch-head">
                <div class="lm-batch-identity">
                    <p class="lm-batch-code">
                        {{ $assignment->assignment_code }}
                    </p>

                    <h3
                        id="last-mile-batch-{{ $assignment->id }}"
                        class="lm-batch-title"
                    >
                        {{ $assignment->facility_name }}
                        <span class="lm-route-arrow">→</span>
                        {{ $assignment->delivery_area }}
                    </h3>

                    <p class="lm-batch-copy">
                        {{ $assignment->orders->count() }}
                        {{ $assignment->orders->count() === 1 ? 'parcel' : 'parcels' }}
                        in this batch
                    </p>
                </div>

                <span class="lm-badge {{ $batchStatusClass }}">
                    {{ str_replace('_', ' ', $assignment->status) }}
                </span>
            </div>

            <dl class="lm-details">
                <div class="lm-detail">
                    <dt>Destination Hub</dt>

                    <dd>
                        <span class="lm-detail-value">
                            {{ $assignment->facility_name }}
                        </span>

                        <span class="lm-detail-copy">
                            {{ $assignment->facility_address ?: 'Hub address unavailable' }}
                        </span>
                    </dd>
                </div>

                <div class="lm-detail">
                    <dt>Delivery Area</dt>

                    <dd>
                        <span class="lm-detail-value">
                            {{ $assignment->delivery_area }}
                        </span>

                        <span class="lm-detail-copy">
                            Assigned barangay for this batch.
                        </span>
                    </dd>
                </div>
            </dl>

            @if($isAssigned)
                <div class="lm-start">
                    <div class="lm-start-copy">
                        <p class="lm-start-title">
                            Ready for destination-hub pickup
                        </p>

                        <p class="lm-start-description">
                            Start the route only after you physically receive
                            the assigned parcels from the destination hub.
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('courier.last-mile.start', $assignment->id) }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="lm-button lm-button-gold"
                            aria-label="Start last-mile route for {{ $assignment->assignment_code }}"
                        >
                            Start Last-Mile Route
                            <span aria-hidden="true">→</span>
                        </button>
                    </form>
                </div>
            @endif

            <div class="lm-parcels-heading">
                <h4 class="lm-parcels-title">Batch Parcels</h4>

                <span class="lm-parcels-count">
                    {{ $assignment->orders->count() }}
                    {{ $assignment->orders->count() === 1 ? 'parcel' : 'parcels' }}
                </span>
            </div>

            <div class="lm-parcel-list">
                @forelse($assignment->orders as $order)
                    @php
                        $parcelStatus = $order->parcel_status ?? $order->order_status;
                        $isDelivered = $parcelStatus === 'delivered';

                        $parcelStatusClass = $isDelivered
                            ? 'lm-badge-green'
                            : ($parcelStatus === 'arrived_buyer' ? 'lm-badge-blue' : '');
                    @endphp

                    <div class="lm-parcel">
                        <div class="lm-parcel-main">
                            <div class="lm-parcel-identity">
                                <p class="lm-order-number">
                                    {{ $order->order_number }}
                                </p>

                                <span class="lm-badge {{ $parcelStatusClass }}">
                                    {{ str_replace('_', ' ', $parcelStatus) }}
                                </span>
                            </div>

                            <div class="lm-buyer">
                                <p class="lm-buyer-label">Buyer & Delivery</p>

                                <p class="lm-buyer-name">
                                    {{ $order->buyer_name }}
                                </p>

                                <p class="lm-buyer-address">
                                    {{ $order->buyer_address }}
                                </p>

                                @if($order->buyer_phone)
                                    <p class="lm-buyer-phone">
                                        {{ $order->buyer_phone }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        @if($isOut && $order->parcel_status === 'out_for_delivery')
                            <div class="lm-parcel-action">
                                <p class="lm-action-note">
                                    Next step: reach the Buyer location.
                                </p>

                                <form
                                    method="POST"
                                    action="{{ route('courier.last-mile.arrived', [
                                        'assignment' => $assignment->id,
                                        'order' => $order->id,
                                    ]) }}"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="lm-button lm-button-blue"
                                        aria-label="Confirm arrived at Buyer for {{ $order->order_number }}"
                                    >
                                        Confirm Arrived at Buyer
                                        <span aria-hidden="true">→</span>
                                    </button>
                                </form>
                            </div>
                        @elseif($isOut && $order->parcel_status === 'arrived_buyer')
                            <div class="lm-parcel-action lm-action-success">
                                <p class="lm-action-note">
                                    Buyer location confirmed. Complete the final handoff.
                                </p>

                                <form
                                    method="POST"
                                    action="{{ route('courier.last-mile.delivered', [
                                        'assignment' => $assignment->id,
                                        'order' => $order->id,
                                    ]) }}"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="lm-button lm-button-green"
                                        aria-label="Confirm delivery complete for {{ $order->order_number }}"
                                    >
                                        Confirm Delivery Complete
                                        <span aria-hidden="true">✓</span>
                                    </button>
                                </form>
                            </div>
                        @elseif($isDelivered)
                            <p class="lm-delivered">
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="m8 12 3 3 5-6"></path>
                                </svg>

                                Delivered Successfully
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="lm-empty-parcels">
                        No parcels are listed in this batch.
                    </p>
                @endforelse
            </div>
        </article>
    @empty
        <section class="lm-panel">
            <div class="lm-empty">
                <span class="lm-empty-icon" aria-hidden="true">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M12 21s7-6 7-12a7 7 0 1 0-14 0c0 6 7 12 7 12Z"></path>
                        <circle cx="12" cy="9" r="2.5"></circle>
                    </svg>
                </span>

                <h3 class="lm-empty-title">
                    No last-mile assignments yet
                </h3>

                <p class="lm-empty-description">
                    Destination-hub batches will appear here
                    when SARI Logistics assigns them to you.
                </p>
            </div>
        </section>
    @endforelse
</div>
@endsection