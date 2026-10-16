@extends('layouts.logistics')

@section('title', 'Delivery Monitoring — SARI Logistics')
@section('page-title', 'Delivery Monitoring')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/logistics/delivery-monitoring.css') }}?v=20261015-1"
    >
@endpush


@section('content')

<div
    class="monitor-page"
    data-monitor-workspace
>

    {{-- =====================================================
        HERO
    ====================================================== --}}

    <header class="monitor-page-header">

        <div>

            <p class="monitor-eyebrow">
                END-TO-END OPERATIONS
            </p>

            <h1>
                Delivery <span>Monitoring</span>
            </h1>

            <p class="monitor-subtitle">
                Follow every active parcel from Seller pickup through
                origin hub processing, linehaul, destination hub dispatch,
                Last-Mile delivery, and final Buyer handoff.
            </p>

        </div>


        <div class="monitor-header-actions">

            <a
                href="{{ route('logistics.last-mile-assignment') }}"
                class="monitor-button"
            >
                <span aria-hidden="true">←</span>
                Last Mile
            </a>


            <a
                href="{{ route('logistics.dashboard') }}"
                class="monitor-button monitor-button-primary"
            >
                Dashboard
            </a>

        </div>

    </header>


    {{-- =====================================================
        SUMMARY
    ====================================================== --}}

    @php
        $summaryCards = [
            [
                'Active Deliveries',
                $stats['active'],
                'Across all delivery legs',
                'neutral',
            ],
            [
                'At Hubs',
                $stats['at_hub'],
                'Sorting or dispatch operations',
                'amber',
            ],
            [
                'In Linehaul',
                $stats['linehaul'],
                'Moving between hubs',
                'blue',
            ],
            [
                'Out for Delivery',
                $stats['out_for_delivery'],
                'With local Riders',
                'green',
            ],
            [
                'Delivered Today',
                $stats['delivered_today'],
                'Completed Buyer handoffs',
                'green',
            ],
        ];
    @endphp


    <div class="monitor-stats">

        @foreach(
            $summaryCards
            as [$label, $value, $note, $tone]
        )

            <article
                class="
                    monitor-stat
                    monitor-stat-{{ $tone }}
                "
            >

                <p>
                    {{ $label }}
                </p>

                <strong>
                    {{ $value }}
                </strong>

                <span>
                    {{ $note }}
                </span>

            </article>

        @endforeach

    </div>


    {{-- =====================================================
        FLOW
    ====================================================== --}}

    <nav
        class="monitor-flow"
        aria-label="Delivery operations workflow"
    >

        <a href="{{ route('logistics.pickup-requests') }}">
            <b>1</b>
            Seller Pickup
        </a>


        <a href="{{ route('logistics.incoming-parcels') }}">
            <b>2</b>
            Origin Hub
        </a>


        <a href="{{ route('logistics.parcel-sorting') }}">
            <b>3</b>
            Sorting
        </a>


        <a href="{{ route('logistics.hub-transfers') }}">
            <b>4</b>
            Linehaul
        </a>


        <a href="{{ route('logistics.last-mile-assignment') }}">
            <b>5</b>
            Last Mile
        </a>


        <span
            class="is-current"
            aria-current="step"
        >
            <b>6</b>
            Monitoring
        </span>

    </nav>


    {{-- =====================================================
        ACTIVE DELIVERIES
    ====================================================== --}}

    <section
        class="monitor-workspace"
        aria-labelledby="active-deliveries-title"
    >

        {{-- Toolbar --}}
        <div class="monitor-toolbar">

            <div>

                <h2 id="active-deliveries-title">
                    Active parcel journeys
                </h2>

                <p>
                    Current operational stage is derived from the
                    Marketplace Order and Logistics Parcel states.
                </p>

            </div>


            <label class="monitor-search">

                <span class="sr-only">
                    Search active deliveries
                </span>

                <input
                    type="search"
                    data-monitor-search
                    placeholder="Search order, Buyer, Seller, Rider, hub…"
                >

            </label>

        </div>


        {{-- Filters --}}
        @php
            $pickupCount =
                $activeOrders
                    ->where(
                        'monitor_stage_group',
                        'pickup'
                    )
                    ->count();

            $hubCount =
                $activeOrders
                    ->where(
                        'monitor_stage_group',
                        'hub'
                    )
                    ->count();

            $linehaulCount =
                $activeOrders
                    ->where(
                        'monitor_stage_group',
                        'linehaul'
                    )
                    ->count();

            $lastMileCount =
                $activeOrders
                    ->where(
                        'monitor_stage_group',
                        'last_mile'
                    )
                    ->count();
        @endphp


        <div class="monitor-filter-row">

            <div
                class="monitor-tabs"
                role="group"
                aria-label="Filter delivery stage"
            >

                <button
                    type="button"
                    data-monitor-filter="all"
                    aria-pressed="true"
                >
                    All

                    <span>
                        {{ $activeOrders->count() }}
                    </span>
                </button>


                <button
                    type="button"
                    data-monitor-filter="pickup"
                    aria-pressed="false"
                >
                    Pickup

                    <span>
                        {{ $pickupCount }}
                    </span>
                </button>


                <button
                    type="button"
                    data-monitor-filter="hub"
                    aria-pressed="false"
                >
                    At Hub

                    <span>
                        {{ $hubCount }}
                    </span>
                </button>


                <button
                    type="button"
                    data-monitor-filter="linehaul"
                    aria-pressed="false"
                >
                    Linehaul

                    <span>
                        {{ $linehaulCount }}
                    </span>
                </button>


                <button
                    type="button"
                    data-monitor-filter="last_mile"
                    aria-pressed="false"
                >
                    Last Mile

                    <span>
                        {{ $lastMileCount }}
                    </span>
                </button>

            </div>


            <button
                type="button"
                class="monitor-text-link"
                data-monitor-clear
            >
                Clear filters
            </button>

        </div>


        {{-- =================================================
            ACTIVE ORDER CARDS
        ================================================== --}}

        <div class="monitor-order-list">

            @forelse($activeOrders as $order)

                @php
                    $routeStops =
                        $order
                            ->deliveryRouteStops
                            ->keyBy('stop_key');

                    $originStop =
                        $routeStops->get(
                            'origin_facility'
                        );

                    $destinationStop =
                        $routeStops->get(
                            'destination_facility'
                        );

                    $originName =
                        $originStop
                            ?->facility
                            ?->name
                        ?: $originStop
                            ?->name
                        ?: 'Origin hub pending';

                    $destinationName =
                        $destinationStop
                            ?->facility
                            ?->name
                        ?: $destinationStop
                            ?->name
                        ?: 'Destination hub pending';

                    $lastMile =
                        $order->getAttribute(
                            'monitor_last_mile'
                        );

                    $transfer =
                        $order->getAttribute(
                            'monitor_transfer'
                        );

                    $currentStep =
                        (int) $order->getAttribute(
                            'monitor_stage_step'
                        );

                    $stageGroup =
                        $order->getAttribute(
                            'monitor_stage_group'
                        );

                    $stageTone =
                        $order->getAttribute(
                            'monitor_stage_tone'
                        );

                    $localRiderName =
                        $lastMile
                            ? trim(
                                $lastMile->first_name
                                .' '
                                .$lastMile->last_name
                            )
                            : null;

                    $searchText =
                        implode(
                            ' ',
                            array_filter([
                                $order->order_number,
                                $order->buyer_name,
                                $order->buyer_address,
                                $order->seller?->store_name,
                                $order->pickup_name,
                                $order->courier_name,
                                $order->courier_email,
                                $localRiderName,
                                $lastMile?->delivery_area,
                                $originName,
                                $destinationName,
                                $transfer?->transfer_code,
                                $transfer?->driver_name,
                                $order->getAttribute(
                                    'monitor_stage_label'
                                ),
                            ])
                        );

                    $journeySteps = [
                        [
                            'Seller Pickup',
                            'Pickup Rider',
                        ],
                        [
                            'Origin Hub',
                            'Hub Intake',
                        ],
                        [
                            'Sorting',
                            'Sort Zone',
                        ],
                        [
                            'Hub Transfer',
                            'Linehaul',
                        ],
                        [
                            'Destination Hub',
                            'Hub Receipt',
                        ],
                        [
                            'Local Rider',
                            'Last Mile',
                        ],
                        [
                            'Buyer',
                            'Delivery',
                        ],
                    ];
                @endphp


                <article
                    class="monitor-order-card"
                    data-monitor-order
                    data-stage-group="{{ $stageGroup }}"
                    data-search="{{ $searchText }}"
                >

                    {{-- =====================================
                        ORDER HEADER
                    ====================================== --}}

                    <header class="monitor-order-header">

                        <div class="monitor-order-identity">

                            <span
                                class="monitor-order-icon"
                                aria-hidden="true"
                            >
                                {{
                                    mb_strtoupper(
                                        mb_substr(
                                            trim(
                                                (string) (
                                                    $order->buyer_name
                                                    ?: 'O'
                                                )
                                            ),
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </span>


                            <div>

                                <p class="monitor-eyebrow">
                                    MARKETPLACE ORDER
                                </p>


                                <h3>
                                    {{ $order->order_number }}
                                </h3>


                                <p>
                                    {{ $order->seller?->store_name ?: 'SARI Seller' }}
                                    <span aria-hidden="true">·</span>
                                    {{ $order->buyer_name }}
                                </p>

                            </div>

                        </div>


                        <div class="monitor-order-status">

                            <span
                                class="
                                    monitor-status
                                    monitor-status-{{ $stageTone }}
                                "
                            >
                                {{
                                    $order->getAttribute(
                                        'monitor_stage_label'
                                    )
                                }}
                            </span>


                            <small>
                                Updated
                                {{ $order->updated_at?->diffForHumans() }}
                            </small>

                        </div>

                    </header>


                    {{-- =====================================
                        CURRENT STATE
                    ====================================== --}}

                    <div class="monitor-current-state">

                        <div>

                            <span class="monitor-label">
                                Current Operation
                            </span>


                            <strong>
                                {{
                                    $order->getAttribute(
                                        'monitor_stage_label'
                                    )
                                }}
                            </strong>


                            <p>
                                {{
                                    $order->getAttribute(
                                        'monitor_stage_note'
                                    )
                                }}
                            </p>

                        </div>


                        <div class="monitor-current-handler">

                            <span
                                class="monitor-handler-avatar"
                                aria-hidden="true"
                            >
                                {{
                                    mb_strtoupper(
                                        mb_substr(
                                            trim(
                                                (string) $order
                                                    ->getAttribute(
                                                        'monitor_handler_name'
                                                    )
                                            ),
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </span>


                            <div>

                                <span class="monitor-label">
                                    Current Handler
                                </span>


                                <strong>
                                    {{
                                        $order->getAttribute(
                                            'monitor_handler_name'
                                        )
                                    }}
                                </strong>


                                <p>
                                    {{
                                        $order->getAttribute(
                                            'monitor_handler_role'
                                        )
                                    }}
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- =====================================
                        JOURNEY TIMELINE
                    ====================================== --}}

                    <div class="monitor-journey-scroll">

                        <div class="monitor-journey">

                            @foreach(
                                $journeySteps
                                as $stepIndex => [$stepName, $stepNote]
                            )

                                @php
                                    $stepNumber =
                                        $stepIndex + 1;

                                    $stepState =
                                        $stepNumber < $currentStep
                                            ? 'complete'
                                            : (
                                                $stepNumber === $currentStep
                                                    ? 'current'
                                                    : 'pending'
                                            );
                                @endphp


                                <div
                                    class="
                                        monitor-journey-step
                                        is-{{ $stepState }}
                                    "
                                >

                                    <div class="monitor-step-marker">

                                        @if(
                                            $stepState
                                            === 'complete'
                                        )
                                            ✓
                                        @else
                                            {{ $stepNumber }}
                                        @endif

                                    </div>


                                    <div>

                                        <strong>
                                            {{ $stepName }}
                                        </strong>

                                        <span>
                                            {{ $stepNote }}
                                        </span>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>


                    {{-- =====================================
                        OPERATION DETAILS
                    ====================================== --}}

                    <div class="monitor-detail-grid">

                        {{-- Pickup Rider --}}
                        <div class="monitor-detail">

                            <span class="monitor-label">
                                Pickup Rider
                            </span>


                            <strong>
                                {{
                                    $order->courier_name
                                    ?: 'Not assigned'
                                }}
                            </strong>


                            <p>
                                {{
                                    $order->courier_email
                                    ?: 'No pickup Rider email'
                                }}
                            </p>

                        </div>


                        {{-- Origin Hub --}}
                        <div class="monitor-detail">

                            <span class="monitor-label">
                                Origin Hub
                            </span>


                            <strong>
                                {{ $originName }}
                            </strong>


                            <p>
                                {{
                                    $order
                                        ->logisticsParcel
                                        ?->sorting_zone
                                    ? 'Zone '
                                        .$order
                                            ->logisticsParcel
                                            ->sorting_zone
                                    : 'Sorting zone not assigned'
                                }}
                            </p>

                        </div>


                        {{-- Transfer --}}
                        <div class="monitor-detail">

                            <span class="monitor-label">
                                Hub Transfer
                            </span>


                            @if($transfer)

                                <strong>
                                    {{ $transfer->transfer_code }}
                                </strong>


                                <p>
                                    {{ $transfer->vehicle_type }}

                                    @if($transfer->vehicle_plate)
                                        ·
                                        {{ $transfer->vehicle_plate }}
                                    @endif
                                </p>


                                <p>
                                    Driver:
                                    {{ $transfer->driver_name }}
                                </p>

                            @else

                                <strong>
                                    Not yet assigned
                                </strong>


                                <p>
                                    Awaiting linehaul stage
                                </p>

                            @endif

                        </div>


                        {{-- Destination --}}
                        <div class="monitor-detail">

                            <span class="monitor-label">
                                Destination Hub
                            </span>


                            <strong>
                                {{ $destinationName }}
                            </strong>


                            <p>
                                {{
                                    $lastMile?->delivery_area
                                    ? 'Delivery area: Barangay '
                                        .$lastMile->delivery_area
                                    : 'Last-mile area pending'
                                }}
                            </p>

                        </div>


                        {{-- Local Rider --}}
                        <div class="monitor-detail">

                            <span class="monitor-label">
                                Local Rider
                            </span>


                            @if($lastMile)

                                <strong>
                                    {{ $localRiderName }}
                                </strong>


                                <p>
                                    {{
                                        $lastMile->vehicle_type
                                        ?: 'Vehicle not recorded'
                                    }}

                                    @if($lastMile->plate_number)
                                        ·
                                        {{ $lastMile->plate_number }}
                                    @endif
                                </p>


                                <p>
                                    {{ $lastMile->assignment_code }}
                                </p>

                            @else

                                <strong>
                                    Not yet assigned
                                </strong>


                                <p>
                                    Destination dispatch pending
                                </p>

                            @endif

                        </div>


                        {{-- Buyer --}}
                        <div class="monitor-detail">

                            <span class="monitor-label">
                                Buyer Destination
                            </span>


                            <strong>
                                {{ $order->buyer_name }}
                            </strong>


                            <p>
                                {{ $order->buyer_address }}
                            </p>

                        </div>

                    </div>


                    {{-- =====================================
                        FOOTER
                    ====================================== --}}

                    <footer class="monitor-order-footer">

                        <div>

                            <span>
                                Parcel state:
                            </span>


                            <strong>
                                {{
                                    ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $order
                                                ->logisticsParcel
                                                ?->status
                                            ?: $order->status
                                        )
                                    )
                                }}
                            </strong>

                        </div>


                        <a
                            href="{{ route(
                                'logistics.delivery-routes.edit',
                                $order
                            ) }}"
                            class="monitor-text-link"
                        >
                            View Delivery Route →
                        </a>

                    </footer>

                </article>

            @empty

                <div class="monitor-empty">

                    <span
                        class="monitor-empty-icon"
                        aria-hidden="true"
                    >
                        ✓
                    </span>


                    <h3>
                        No active deliveries
                    </h3>


                    <p>
                        Active Seller pickup, hub, linehaul, and
                        Last-Mile operations will appear here.
                    </p>

                </div>

            @endforelse


            <div
                class="monitor-empty"
                data-monitor-empty
                hidden
            >

                <h3>
                    No matching active deliveries
                </h3>


                <p>
                    Try another order, Buyer, Seller, Rider,
                    hub, or delivery stage.
                </p>


                <button
                    type="button"
                    class="monitor-text-link"
                    data-monitor-clear
                >
                    Clear filters
                </button>

            </div>

        </div>


        <footer class="monitor-list-footer">

            <span
                data-monitor-visible-count
                aria-live="polite"
            >
                {{ $activeOrders->count() }}
                active deliveries shown
            </span>


            <span>
                Seller → Pickup → Hubs → Last Mile → Buyer
            </span>

        </footer>

    </section>


    {{-- =====================================================
        DELIVERED TODAY
    ====================================================== --}}

    <section
        class="
            monitor-workspace
            monitor-delivered-workspace
        "
    >

        <div class="monitor-toolbar">

            <div>

                <h2>
                    Delivered today
                </h2>

                <p>
                    Completed Buyer handoffs for the current day.
                </p>

            </div>


            <span class="monitor-delivered-total">
                {{ $deliveredToday->count() }}
                completed
            </span>

        </div>


        <div class="monitor-delivered-grid">

            @forelse($deliveredToday as $order)

                @php
                    $lastMile =
                        $order->getAttribute(
                            'monitor_last_mile'
                        );

                    $localRiderName =
                        $lastMile
                            ? trim(
                                $lastMile->first_name
                                .' '
                                .$lastMile->last_name
                            )
                            : null;
                @endphp


                <article class="monitor-delivered-card">

                    <div class="monitor-delivered-header">

                        <div>

                            <span class="monitor-label">
                                Delivered Order
                            </span>


                            <strong>
                                {{ $order->order_number }}
                            </strong>

                        </div>


                        <span class="monitor-delivered-check">
                            ✓
                        </span>

                    </div>


                    <div class="monitor-delivered-body">

                        <div>

                            <span class="monitor-label">
                                Buyer
                            </span>

                            <strong>
                                {{ $order->buyer_name }}
                            </strong>

                        </div>


                        <div>

                            <span class="monitor-label">
                                Local Rider
                            </span>

                            <strong>
                                {{
                                    $localRiderName
                                    ?: $order->courier_name
                                    ?: 'Rider'
                                }}
                            </strong>

                        </div>


                        <div>

                            <span class="monitor-label">
                                Completed
                            </span>

                            <strong>
                                {{
                                    $order->delivered_at
                                        ?->timezone(
                                            'Asia/Manila'
                                        )
                                        ->format(
                                            'g:i A'
                                        )
                                    ?: 'Delivered'
                                }}
                            </strong>

                        </div>

                    </div>


                    <a
                        href="{{ route(
                            'logistics.delivery-routes.edit',
                            $order
                        ) }}"
                        class="monitor-text-link"
                    >
                        View completed journey →
                    </a>

                </article>

            @empty

                <div class="monitor-empty monitor-empty-small">

                    <h3>
                        No completed deliveries today
                    </h3>


                    <p>
                        Completed Last-Mile deliveries will appear here.
                    </p>

                </div>

            @endforelse

        </div>

    </section>

</div>

@endsection


@push('scripts')

<script>
(() => {
    'use strict';


    function initializeDeliveryMonitoring() {

        const root =
            document.querySelector(
                '[data-monitor-workspace]'
            );


        if (
            !root
            || root.dataset.initialized
        ) {
            return;
        }


        root.dataset.initialized =
            'true';


        const cards = [
            ...root.querySelectorAll(
                '[data-monitor-order]'
            )
        ];


        const search =
            root.querySelector(
                '[data-monitor-search]'
            );


        const tabs = [
            ...root.querySelectorAll(
                '[data-monitor-filter]'
            )
        ];


        const empty =
            root.querySelector(
                '[data-monitor-empty]'
            );


        const visibleCount =
            root.querySelector(
                '[data-monitor-visible-count]'
            );


        let filter =
            'all';


        const normalize = (value) => {
            return String(value || '')
                .toLocaleLowerCase()
                .trim();
        };


        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        function applyFilters() {

            const keyword =
                normalize(
                    search?.value
                );


            let shown =
                0;


            cards.forEach(
                (card) => {

                    const matchesStage =
                        filter === 'all'
                        ||
                        card.dataset.stageGroup
                            === filter;


                    const matchesSearch =
                        normalize(
                            card.dataset.search
                        ).includes(
                            keyword
                        );


                    card.hidden =
                        !matchesStage
                        ||
                        !matchesSearch;


                    if (!card.hidden) {
                        shown++;
                    }

                }
            );


            tabs.forEach(
                (tab) => {

                    tab.setAttribute(
                        'aria-pressed',
                        String(
                            tab.dataset.monitorFilter
                            === filter
                        )
                    );

                }
            );


            if (empty) {

                empty.hidden =
                    cards.length === 0
                    ||
                    shown > 0;
            }


            if (visibleCount) {

                visibleCount.textContent =
                    `${shown} of ${cards.length} `
                    + `${
                        cards.length === 1
                            ? 'active delivery'
                            : 'active deliveries'
                    } shown`;
            }

        }


        tabs.forEach(
            (tab) => {

                tab.addEventListener(
                    'click',
                    () => {

                        filter =
                            tab.dataset.monitorFilter
                            || 'all';


                        applyFilters();
                    }
                );

            }
        );


        search?.addEventListener(
            'input',
            applyFilters
        );


        root.querySelectorAll(
            '[data-monitor-clear]'
        ).forEach(
            (button) => {

                button.addEventListener(
                    'click',
                    () => {

                        filter =
                            'all';


                        if (search) {
                            search.value = '';
                        }


                        applyFilters();
                    }
                );

            }
        );


        applyFilters();

    }


    if (
        document.readyState === 'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            initializeDeliveryMonitoring
        );

    } else {

        initializeDeliveryMonitoring();
    }

})();
</script>

@endpush