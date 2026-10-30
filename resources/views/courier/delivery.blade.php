@extends('layouts.courier')

@section('title', 'Pickup Workflow')
@section('header-title', 'Pickup Workflow')
@section('header-subtitle', 'Dedicated Rider Job Workspace')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/courier/delivery.css') }}?v=20261030-1"
    >
@endpush

@section('content')
@php
    $isLogisticsPickup = (bool) $order->logisticsParcel;
    $status = (string) $order->status;

    $sellerName = $order->pickup_name
        ?: $order->seller?->store_name
        ?: 'Seller';

    $pickupAddress = $order->pickup_address
        ?: 'Pickup address not available';

    $destinationLabel = $isLogisticsPickup
        ? 'Assigned Origin Hub'
        : ($order->buyer_name ?: 'Buyer');

    $destinationDetail = $isLogisticsPickup
        ? 'Hand the parcel to SARI Logistics for origin-hub intake.'
        : ($order->buyer_address ?: 'Buyer address not available');

    $statusIndex = match ($status) {
        'courier_accepted' => 1,
        'heading_pickup' => 2,
        'arrived_pickup' => 3,
        'in_transit' => 4,
        'arrived_buyer' => 5,
        'delivered' => 6,
        default => 0,
    };

    $steps = $isLogisticsPickup
        ? [
            ['Assigned', 1],
            ['Heading to Seller', 2],
            ['At Seller', 3],
            ['To Origin Hub', 4],
        ]
        : [
            ['Assigned', 1],
            ['Heading to Seller', 2],
            ['At Seller', 3],
            ['In Transit', 4],
            ['At Buyer', 5],
            ['Delivered', 6],
        ];

    $workflowRecord = [
        ['Assigned', $order->accepted_at],
        ['Started Pickup', $order->pickup_started_at],
        ['Arrived at Seller', $order->arrived_pickup_at],
        ['Picked Up', $order->picked_up_at],
    ];

    if (! $isLogisticsPickup) {
        $workflowRecord[] = ['Arrived at Buyer', $order->arrived_buyer_at];
        $workflowRecord[] = ['Delivered', $order->delivered_at];
    }

    $actionTitle = match (true) {
        $status === 'courier_accepted' => 'Start the Seller pickup',
        $status === 'heading_pickup' => 'Arrive at the Seller',
        $status === 'arrived_pickup' => 'Confirm parcel handoff',
        $status === 'in_transit' && $isLogisticsPickup => 'Deliver parcel to origin hub',
        $status === 'in_transit' => 'Continue to Buyer',
        $status === 'arrived_buyer' => 'Complete Buyer handoff',
        default => 'Review current job status',
    };
@endphp

<div id="riderWorkflow" class="courier-delivery-page">
    @if(session('success'))
        <div class="dw-alert dw-alert-success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="dw-alert dw-alert-error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <header>
        <nav class="dw-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('courier.requests') }}">
                Pickup Assignments
            </a>

            <span aria-hidden="true">/</span>

            <strong aria-current="page">
                {{ $order->order_number }}
            </strong>
        </nav>

        <div class="dw-header-row">
            <div class="dw-heading-copy">
                <p class="dw-eyebrow">Rider Workspace</p>

                <h2 class="dw-page-title">
                    {{ $isLogisticsPickup ? 'Seller Pickup' : 'Direct Delivery' }}
                    <span>Workflow</span>
                </h2>

                <p class="dw-description">
                    @if($isLogisticsPickup)
                        Pick up the parcel from the Seller and turn it over
                        to Logistics at the origin hub.
                    @else
                        Follow each step from Seller pickup to the final
                        Buyer handoff.
                    @endif
                </p>
            </div>

            <div class="dw-header-actions">
                <a
                    href="{{ route('courier.dashboard') }}"
                    class="dw-button"
                >
                    Dashboard
                </a>

                <a
                    href="{{ route('courier.requests') }}"
                    class="dw-button dw-button-gold"
                >
                    Pickup Queue
                </a>
            </div>
        </div>
    </header>

    {{-- WORKFLOW PROGRESS --}}
    <section class="dw-panel" aria-labelledby="workflowProgressTitle">
        <div class="dw-panel-header">
            <div>
                <h3 id="workflowProgressTitle" class="dw-panel-title">
                    Job Progress
                </h3>

                <p class="dw-panel-subtitle">
                    {{ $order->order_number }}
                </p>
            </div>

            <span class="dw-badge dw-badge-blue">
                {{ $order->statusLabel() }}
            </span>
        </div>

        <ol class="dw-steps {{ $isLogisticsPickup ? 'dw-steps-four' : 'dw-steps-six' }}">
            @foreach($steps as [$label, $step])
                @php
                    $done = $statusIndex > $step;
                    $current = $statusIndex === $step;
                @endphp

                <li
                    class="dw-step {{ $done ? 'dw-step-done' : ($current ? 'dw-step-current' : '') }}"
                    @if($current) aria-current="step" @endif
                >
                    <span class="dw-step-number" aria-hidden="true">
                        {{ $done ? '✓' : $step }}
                    </span>

                    <strong class="dw-step-name">{{ $label }}</strong>

                    <p class="dw-step-note">
                        {{ $done ? 'Completed' : ($current ? 'Current stage' : 'Upcoming') }}
                    </p>
                </li>
            @endforeach
        </ol>
    </section>

    <div class="dw-main">
        {{-- CURRENT ACTION --}}
        <section class="dw-panel" aria-labelledby="workflowActionTitle">
            <div class="dw-panel-header">
                <div>
                    <p class="dw-eyebrow">Current Action</p>

                    <h3 id="workflowActionTitle" class="dw-panel-title">
                        {{ $actionTitle }}
                    </h3>
                </div>

                <span class="dw-badge">
                    {{ $isLogisticsPickup ? 'Pickup Leg' : 'Direct Delivery' }}
                </span>
            </div>

            <div class="dw-action-body">
                @if($status === 'courier_accepted')
                    <div class="dw-action-state dw-action-gold">
                        <h4 class="dw-action-title">
                            Pickup assignment accepted
                        </h4>

                        <p class="dw-action-copy">
                            Proceed to the Seller pickup location.
                            Start this step when you are ready to travel
                            to the Seller.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('courier.orders.proceed-pickup', $order) }}"
                            class="dw-action-form"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="dw-button dw-button-gold dw-button-full"
                            >
                                Proceed to Seller Pickup →
                            </button>
                        </form>
                    </div>

                @elseif($status === 'heading_pickup')
                    <div class="dw-action-state dw-action-blue">
                        <h4 class="dw-action-title">
                            Heading to the Seller
                        </h4>

                        <p class="dw-action-copy">
                            Confirm arrival only after you physically reach
                            the Seller pickup point.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('courier.orders.arrived-pickup', $order) }}"
                            class="dw-action-form"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="dw-button dw-button-blue dw-button-full"
                            >
                                I Arrived at the Seller
                            </button>
                        </form>
                    </div>

                @elseif($status === 'arrived_pickup')
                    <div class="dw-action-state dw-action-blue">
                        <h4 class="dw-action-title">
                            Verify the parcel before pickup
                        </h4>

                        <p class="dw-action-copy">
                            Check the parcel and Seller handoff before
                            confirming that it is now in your custody.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('courier.orders.confirm-pickup', $order) }}"
                            class="dw-action-form"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="dw-button dw-button-blue dw-button-full"
                            >
                                Confirm Item Pickup →
                            </button>
                        </form>
                    </div>

                @elseif($status === 'in_transit' && $isLogisticsPickup)
                    <div class="dw-action-state dw-action-green">
                        <h4 class="dw-action-title">
                            Seller pickup completed
                        </h4>

                        <p class="dw-action-copy">
                            Bring this parcel to the assigned origin hub.
                            The destination last-mile Rider handles the
                            final Buyer delivery.
                        </p>

                        <div class="dw-handoff">
                            <p class="dw-handoff-label">Next Handoff</p>
                            <strong>SARI Logistics · Origin Hub Intake</strong>
                        </div>

                        <p class="dw-action-copy">
                            This pickup job remains active until Logistics
                            physically receives the parcel at the origin hub.
                        </p>

                        <a
                            href="{{ route('courier.requests') }}"
                            class="dw-button dw-button-full dw-action-link"
                        >
                            Back to Pickup Assignments
                        </a>
                    </div>

                @elseif($status === 'in_transit')
                    <div class="dw-action-state dw-action-blue">
                        <h4 class="dw-action-title">
                            Direct delivery in transit
                        </h4>

                        <p class="dw-action-copy">
                            Continue to the Buyer. Confirm arrival only when
                            you are physically at the delivery location.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('courier.orders.arrived-buyer', $order) }}"
                            class="dw-action-form"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="dw-button dw-button-blue dw-button-full"
                            >
                                Confirm Arrived at Buyer →
                            </button>
                        </form>
                    </div>

                @elseif($status === 'arrived_buyer' && ! $isLogisticsPickup)
                    <div class="dw-action-state dw-action-green">
                        <h4 class="dw-action-title">
                            Buyer location reached
                        </h4>

                        <p class="dw-action-copy">
                            Complete the direct-delivery handoff only after
                            the parcel has been successfully turned over
                            to the Buyer.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('courier.orders.complete', $order) }}"
                            class="dw-action-form"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="dw-button dw-button-green dw-button-full"
                            >
                                Confirm Delivery Complete
                            </button>
                        </form>
                    </div>

                @else
                    <div class="dw-action-state">
                        <h4 class="dw-action-title">
                            No action available for this state
                        </h4>

                        <p class="dw-action-copy">
                            Return to your assignment queue or dashboard
                            to review current work.
                        </p>
                    </div>
                @endif
            </div>
        </section>

        <aside class="dw-sidebar">
            {{-- JOB DETAILS --}}
            <section class="dw-panel" aria-labelledby="workflowDetailsTitle">
                <div class="dw-panel-header">
                    <div>
                        <h3 id="workflowDetailsTitle" class="dw-panel-title">
                            Job Details
                        </h3>

                        <p class="dw-panel-subtitle">
                            {{ $order->order_number }}
                        </p>
                    </div>

                    <span class="dw-badge">
                        {{ $isLogisticsPickup ? 'Pickup Leg' : 'Direct' }}
                    </span>
                </div>

                <dl class="dw-details">
                    <div class="dw-detail">
                        <dt>Seller</dt>
                        <dd class="dw-detail-strong">{{ $sellerName }}</dd>
                    </div>

                    <div class="dw-detail">
                        <dt>Pickup Address</dt>
                        <dd>{{ $pickupAddress }}</dd>
                    </div>

                    <div class="dw-detail">
                        <dt>Next Destination</dt>

                        <dd class="dw-detail-strong">
                            {{ $destinationLabel }}
                        </dd>

                        <dd>{{ $destinationDetail }}</dd>
                    </div>

                    @if(! $isLogisticsPickup)
                        <div class="dw-detail">
                            <dt>Delivery Fee</dt>

                            <dd class="dw-fee">
                                ₱{{ number_format($order->courierDeliveryFee(), 2) }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </section>

            {{-- WORKFLOW RECORD --}}
            <section class="dw-panel" aria-labelledby="workflowRecordTitle">
                <div class="dw-panel-header">
                    <h3 id="workflowRecordTitle" class="dw-panel-title">
                        Workflow Record
                    </h3>
                </div>

                <ol class="dw-records">
                    @foreach($workflowRecord as [$label, $time])
                        <li class="dw-record {{ $time ? 'dw-record-done' : '' }}">
                            <span class="dw-record-label">
                                <span class="dw-record-dot" aria-hidden="true"></span>
                                {{ $label }}
                            </span>

                            @if($time)
                                <time
                                    class="dw-record-time"
                                    datetime="{{ $time->toIso8601String() }}"
                                >
                                    {{ $time->format('M d · h:i A') }}
                                </time>
                            @else
                                <span class="dw-record-time">Pending</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </section>

            @if($isLogisticsPickup)
                <section class="dw-note" aria-label="Pickup responsibility">
                    <strong>Seller Pickup Responsibility</strong>

                    <p>
                        This job covers Seller pickup and origin-hub handoff.
                        Your pickup responsibility ends when Logistics accepts
                        the parcel at the origin hub.
                    </p>
                </section>
            @endif
        </aside>
    </div>
</div>
@endsection