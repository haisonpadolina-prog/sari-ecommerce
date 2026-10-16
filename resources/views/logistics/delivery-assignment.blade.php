@extends('layouts.logistics')

@section('title', 'Pickup Assignment — SARI Logistics')
@section('page-title', 'Pickup Assignment')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/logistics/pickup.css') }}?v=20261015-3"
    >
@endpush


@section('content')

<div
    class="pickup-page"
    data-pickup-workspace="assignment"
    data-focus-order="{{ (int) request('order', 0) }}"
>

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}

    <header class="pickup-page-header">

        <div>

            <p class="pickup-eyebrow">
                SELLER COLLECTIONS
            </p>

            <h1>
                Pickup <span>Assignment</span>
            </h1>

            <p class="pickup-subtitle">
                Dispatch one available Rider to each verified Seller pickup
                batch. Orders from the same Seller, pickup location, and
                origin hub are collected together.
            </p>

        </div>


        <a
            href="{{ route('logistics.pickup-requests') }}"
            class="pickup-button"
        >
            <span aria-hidden="true">←</span>
            Pickup Requests
        </a>

    </header>


    {{-- =====================================================
        FLASH MESSAGES
    ====================================================== --}}

    @if(session('success'))

        <div
            class="pickup-alert pickup-alert-success"
            role="status"
        >
            <strong>Pickup Rider assigned</strong>

            <p>
                {{ session('success') }}
            </p>
        </div>

    @endif


    @if($errors->any())

        <div
            class="pickup-alert pickup-alert-danger"
            role="alert"
        >
            <strong>Could not assign pickup batch</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif


    {{-- =====================================================
        SUMMARY
    ====================================================== --}}

    @php
        $summaryCards = [
            [
                'Verified parcels',
                $stats['verified_orders'],
                'Waiting for Pickup Rider',
                'green',
            ],
            [
                'Pickup batches',
                $stats['pickup_groups'],
                'Seller collection stops',
                'amber',
            ],
            [
                'Available Riders',
                $stats['available_riders'],
                'Online with no active work',
                'blue',
            ],
            [
                'Multi-parcel batches',
                $stats['multi_order_groups'],
                'One stop, multiple parcels',
                'neutral',
            ],
        ];
    @endphp


    <div class="pickup-stats">

        @foreach($summaryCards as [$label, $value, $note, $tone])

            <article class="pickup-stat pickup-stat-{{ $tone }}">

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
        WORKFLOW
    ====================================================== --}}

    <nav
        class="pickup-flow"
        aria-label="Pickup workflow"
    >

        <a href="{{ route('logistics.pickup-requests') }}">
            <b>1</b>
            Review & verify
        </a>


        <span
            class="is-current"
            aria-current="step"
        >
            <b>2</b>
            Assign Pickup Rider
        </span>


        <a href="{{ route('logistics.incoming-parcels') }}">
            <b>3</b>
            Origin hub intake
        </a>

    </nav>


    {{-- =====================================================
        ASSIGNMENT WORKSPACE
    ====================================================== --}}

    <section
        class="pickup-workspace"
        aria-labelledby="pickup-assignment-title"
    >

        {{-- =================================================
            TOOLBAR
        ================================================== --}}

        <div class="pickup-toolbar">

            <div>

                <h2 id="pickup-assignment-title">
                    Verified pickup batches
                </h2>

                <p>
                    One Rider handles the entire Seller collection batch.
                </p>

            </div>


            <label class="pickup-search">

                <span class="sr-only">
                    Search pickup batches
                </span>

                <input
                    type="search"
                    data-assignment-search
                    placeholder="Search Seller, order, Buyer, or hub…"
                >

            </label>

        </div>


        {{-- =================================================
            FILTERS
        ================================================== --}}

        <div class="pickup-filter-row">

            <div
                class="pickup-tabs"
                role="group"
                aria-label="Filter pickup batches"
            >

                <button
                    type="button"
                    data-assignment-filter="all"
                    aria-pressed="true"
                >
                    All

                    <span>
                        {{ $stats['pickup_groups'] }}
                    </span>
                </button>


                <button
                    type="button"
                    data-assignment-filter="batch"
                    aria-pressed="false"
                >
                    Multi-parcel

                    <span>
                        {{ $stats['multi_order_groups'] }}
                    </span>
                </button>


                <button
                    type="button"
                    data-assignment-filter="single"
                    aria-pressed="false"
                >
                    Single parcel

                    <span>
                        {{
                            max(
                                0,
                                $stats['pickup_groups']
                                - $stats['multi_order_groups']
                            )
                        }}
                    </span>
                </button>

            </div>


            <button
                type="button"
                class="pickup-text-link"
                data-assignment-clear
            >
                Clear filters
            </button>

        </div>


        {{-- =================================================
            BATCH CARDS
        ================================================== --}}

        <div class="pickup-batches">

            @foreach($pickupGroups as $group)

                @php
                    $sellerName =
                        $group['seller_name']
                        ?: 'Seller Pickup';

                    $sellerInitial =
                        mb_strtoupper(
                            mb_substr(
                                trim($sellerName),
                                0,
                                1
                            )
                        );

                    $batchType =
                        $group['order_count'] > 1
                            ? 'batch'
                            : 'single';

                    $orderIds =
                        $group['orders']
                            ->pluck('id')
                            ->map(fn ($id) => (int) $id)
                            ->values();

                    $searchValues = [
                        $sellerName,
                        $group['pickup_name'],
                        $group['pickup_address'],
                        $group['origin_facility_name'],
                    ];

                    foreach($group['orders'] as $searchOrder) {
                        $searchValues[] =
                            $searchOrder->order_number;

                        $searchValues[] =
                            $searchOrder->buyer_name;

                        $destinationStop =
                            $searchOrder
                                ->deliveryRouteStops
                                ->firstWhere(
                                    'stop_key',
                                    'destination_facility'
                                );

                        $searchValues[] =
                            $destinationStop
                                ?->facility
                                ?->name
                            ?: $destinationStop
                                ?->name;
                    }

                    $searchText =
                        implode(
                            ' ',
                            array_filter($searchValues)
                        );
                @endphp


                <article
                    class="pickup-batch"
                    data-assignment-batch
                    data-batch-type="{{ $batchType }}"
                    data-search="{{ $searchText }}"
                    data-order-ids="{{ $orderIds->implode(',') }}"
                >

                    {{-- =====================================
                        BATCH HEADER
                    ====================================== --}}

                    <header class="pickup-batch-header">

                        <div class="pickup-seller-heading">

                            <span
                                class="pickup-avatar"
                                aria-hidden="true"
                            >
                                {{ $sellerInitial ?: 'S' }}
                            </span>


                            <div>

                                <p class="pickup-eyebrow">
                                    SELLER PICKUP
                                </p>

                                <h3>
                                    {{ $sellerName }}
                                </h3>

                            </div>

                        </div>


                        <div class="pickup-batch-tags">

                            <span>
                                {{ $group['order_count'] }}
                                {{
                                    $group['order_count'] === 1
                                        ? 'parcel'
                                        : 'parcels'
                                }}
                            </span>


                            <span>
                                {{ $group['buyer_count'] }}
                                {{
                                    $group['buyer_count'] === 1
                                        ? 'buyer'
                                        : 'buyers'
                                }}
                            </span>


                            <span>
                                {{ $group['destination_count'] }}
                                {{
                                    $group['destination_count'] === 1
                                        ? 'destination'
                                        : 'destinations'
                                }}
                            </span>

                        </div>

                    </header>


                    {{-- =====================================
                        BATCH BODY
                    ====================================== --}}

                    <div class="pickup-batch-layout">


                        {{-- =================================
                            LEFT — PARCELS
                        ================================== --}}

                        <div class="pickup-batch-main">


                            {{-- Route summary --}}
                            <div class="pickup-route-summary">

                                <div>

                                    <span class="pickup-label">
                                        Seller pickup
                                    </span>

                                    <strong>
                                        {{
                                            $group['pickup_name']
                                            ?: $sellerName
                                        }}
                                    </strong>

                                    <p>
                                        {{ $group['pickup_address'] }}
                                    </p>

                                </div>


                                <span
                                    class="pickup-route-arrow"
                                    aria-hidden="true"
                                >
                                    →
                                </span>


                                <div>

                                    <span class="pickup-label">
                                        Origin sorting hub
                                    </span>

                                    <strong>
                                        {{ $group['origin_facility_name'] }}
                                    </strong>

                                    <p>
                                        Rider delivers the entire pickup
                                        batch to this facility.
                                    </p>

                                </div>

                            </div>


                            {{-- Parcel heading --}}
                            <div class="pickup-parcel-heading">

                                <div>

                                    <h4>
                                        Parcels in this collection
                                    </h4>

                                    <p
                                        style="
                                            margin: 2px 0 0;
                                            color: #978d80;
                                            font-size: 6.5px;
                                        "
                                    >
                                        Buyers and destination hubs may differ.
                                    </p>

                                </div>


                                <span class="pickup-count">
                                    {{ $group['order_count'] }}
                                </span>

                            </div>


                            {{-- Parcel list --}}
                            <div class="pickup-batch-parcels">

                                @foreach($group['orders'] as $order)

                                    @php
                                        $destination =
                                            $order
                                                ->deliveryRouteStops
                                                ->firstWhere(
                                                    'stop_key',
                                                    'destination_facility'
                                                );

                                        $destinationName =
                                            $destination
                                                ?->facility
                                                ?->name
                                            ?: $destination
                                                ?->name
                                            ?: 'Destination pending';
                                    @endphp


                                    <article class="pickup-parcel">

                                        <div>

                                            <strong>
                                                {{ $order->order_number }}
                                            </strong>

                                            <small>
                                                {{ $order->buyer_name }}
                                                ·
                                                {{ $destinationName }}
                                            </small>

                                        </div>


                                        <span class="pickup-parcel-value">
                                            ₱{{ number_format(
                                                (float) $order->total,
                                                2
                                            ) }}
                                        </span>


                                        <details class="pickup-details">

                                            <summary>
                                                Parcel details
                                                <span aria-hidden="true">
                                                    +
                                                </span>
                                            </summary>


                                            <div class="pickup-detail-body">

                                                <div class="pickup-detail-grid">

                                                    <div>

                                                        <span class="pickup-label">
                                                            Buyer
                                                        </span>

                                                        <strong>
                                                            {{ $order->buyer_name }}
                                                        </strong>

                                                        <p>
                                                            {{ $order->buyer_address }}
                                                        </p>

                                                    </div>


                                                    <div>

                                                        <span class="pickup-label">
                                                            Destination hub
                                                        </span>

                                                        <strong>
                                                            {{ $destinationName }}
                                                        </strong>

                                                        <p>
                                                            Delivery routing continues
                                                            after origin hub sorting.
                                                        </p>

                                                    </div>


                                                    <div>

                                                        <span class="pickup-label">
                                                            Payment
                                                        </span>

                                                        <strong>
                                                            {{
                                                                $order->payment_method
                                                                ?: 'Not recorded'
                                                            }}
                                                        </strong>

                                                        <p>
                                                            {{
                                                                ucfirst(
                                                                    $order->payment_status
                                                                    ?: 'pending'
                                                                )
                                                            }}
                                                        </p>

                                                    </div>


                                                    <div>

                                                        <span class="pickup-label">
                                                            Order value
                                                        </span>

                                                        <strong>
                                                            ₱{{ number_format(
                                                                (float) $order->total,
                                                                2
                                                            ) }}
                                                        </strong>

                                                        <p>
                                                            Marketplace order total
                                                        </p>

                                                    </div>

                                                </div>


                                                <ul class="pickup-products">

                                                    @forelse(
                                                        ($order->items ?? [])
                                                        as $item
                                                    )

                                                        <li>

                                                            <span>
                                                                {{
                                                                    $item['name']
                                                                    ?? 'Product'
                                                                }}

                                                                @if(
                                                                    !empty(
                                                                        $item[
                                                                            'variant_label'
                                                                        ]
                                                                    )
                                                                )
                                                                    ·
                                                                    {{
                                                                        $item[
                                                                            'variant_label'
                                                                        ]
                                                                    }}
                                                                @endif
                                                            </span>

                                                            <strong>
                                                                ×
                                                                {{
                                                                    $item['qty']
                                                                    ?? $item['quantity']
                                                                    ?? 1
                                                                }}
                                                            </strong>

                                                        </li>

                                                    @empty

                                                        <li>
                                                            <span>
                                                                Product details unavailable.
                                                            </span>
                                                        </li>

                                                    @endforelse

                                                </ul>

                                            </div>

                                        </details>

                                    </article>

                                @endforeach

                            </div>


                            {{-- Destination summary --}}
                            @if(
                                $group['destination_names']
                                    ->isNotEmpty()
                            )

                                <div
                                    style="
                                        display: flex;
                                        flex-wrap: wrap;
                                        align-items: center;
                                        gap: 5px;
                                        margin-top: 12px;
                                    "
                                >

                                    <span class="pickup-label">
                                        Future destination hubs
                                    </span>


                                    @foreach(
                                        $group['destination_names']
                                        as $destinationName
                                    )

                                        <span class="pickup-badge pickup-badge-active">
                                            {{ $destinationName }}
                                        </span>

                                    @endforeach

                                </div>

                            @endif

                        </div>


                        {{-- =================================
                            RIGHT — RIDER DISPATCH
                        ================================== --}}

                        <aside class="pickup-dispatch-panel">

                            <h4>
                                Assign Pickup Rider
                            </h4>

                            <p class="pickup-dispatch-note">
                                Choose one approved, active and online
                                Rider for this entire Seller collection.
                            </p>


                            <form
                                method="POST"
                                action="{{ route(
                                    'logistics.delivery-assignment.batch-assign'
                                ) }}"
                                data-pickup-dispatch-form
                            >

                                @csrf


                                @foreach(
                                    $group['orders']
                                    as $order
                                )

                                    <input
                                        type="hidden"
                                        name="order_ids[]"
                                        value="{{ $order->id }}"
                                    >

                                @endforeach


                                <label>

                                    <span class="pickup-label">
                                        Pickup Rider
                                    </span>


                                    <select
                                        name="rider_id"
                                        required
                                        data-rider-select
                                    >

                                        <option value="">
                                            Select available Rider
                                        </option>


                                        @foreach($riders as $rider)

                                            @php
                                                $busy =
                                                    (bool) $rider
                                                        ->getAttribute(
                                                            'is_dispatch_busy'
                                                        );

                                                $riderName =
                                                    trim(
                                                        $rider->first_name
                                                        .' '
                                                        .$rider->last_name
                                                    );

                                                $riderArea =
                                                    collect([
                                                        $rider->barangay_name,
                                                        $rider->municipality_name,
                                                    ])
                                                        ->filter()
                                                        ->implode(', ');

                                                $vehicle =
                                                    collect([
                                                        $rider->vehicle_type,
                                                        $rider->vehicle_model,
                                                    ])
                                                        ->filter()
                                                        ->implode(' · ');
                                            @endphp


                                            <option
                                                value="{{ $rider->id }}"

                                                data-rider-name="{{ $riderName }}"

                                                data-rider-area="{{ $riderArea }}"

                                                data-rider-vehicle="{{ $vehicle }}"

                                                data-rider-plate="{{ $rider->plate_number }}"

                                                @disabled($busy)
                                            >
                                                {{ $riderName }}

                                                @if($rider->vehicle_type)
                                                    — {{ $rider->vehicle_type }}
                                                @endif

                                                @if($rider->plate_number)
                                                    · {{ $rider->plate_number }}
                                                @endif

                                                @if($busy)
                                                    — Busy
                                                @endif
                                            </option>

                                        @endforeach

                                    </select>

                                </label>


                                {{-- Rider preview --}}
                                <div
                                    class="pickup-rider-preview"
                                    data-rider-preview
                                    hidden
                                >

                                    <strong data-rider-preview-name></strong>

                                    <p data-rider-preview-area></p>

                                    <p data-rider-preview-vehicle></p>

                                </div>


                                {{-- Total --}}
                                <div class="pickup-dispatch-total">

                                    <span>
                                        Parcels to collect
                                    </span>

                                    <strong>
                                        {{ $group['order_count'] }}
                                    </strong>

                                </div>


                                <button
                                    type="submit"
                                    class="pickup-button pickup-button-primary"
                                    @disabled(
                                        $stats['available_riders'] === 0
                                    )
                                >
                                    Assign Entire Batch
                                    <span aria-hidden="true">→</span>
                                </button>


                                <p class="pickup-dispatch-footnote">
                                    The selected Rider receives all
                                    {{ $group['order_count'] }}
                                    {{
                                        $group['order_count'] === 1
                                            ? 'parcel'
                                            : 'parcels'
                                    }}
                                    from this Seller pickup stop.
                                </p>

                            </form>


                            @if($stats['available_riders'] === 0)

                                <div
                                    class="pickup-alert pickup-alert-warning"
                                    style="margin-top: 12px; margin-bottom: 0;"
                                >
                                    <strong>No Rider available</strong>

                                    <p>
                                        All online Riders currently have
                                        active Logistics work.
                                    </p>
                                </div>

                            @endif

                        </aside>

                    </div>

                </article>

            @endforeach

        </div>


        {{-- =================================================
            EMPTY STATE
        ================================================== --}}

        @if($pickupGroups->isEmpty())

            <div class="pickup-empty">

                <span
                    class="pickup-empty-icon"
                    aria-hidden="true"
                >
                    ✓
                </span>

                <h3>
                    No pickup batches waiting
                </h3>

                <p>
                    Verified Seller requests will appear here once they
                    are ready for Pickup Rider assignment.
                </p>


                <a
                    href="{{ route('logistics.pickup-requests') }}"
                    class="pickup-button pickup-button-primary"
                >
                    View Pickup Requests
                </a>

            </div>

        @endif


        {{-- =================================================
            SEARCH EMPTY
        ================================================== --}}

        <div
            class="pickup-empty"
            data-assignment-empty
            hidden
        >

            <h3>
                No matching pickup batches
            </h3>

            <p>
                Try another Seller, order number, Buyer,
                location, or hub.
            </p>


            <button
                type="button"
                class="pickup-text-link"
                data-assignment-clear
            >
                Clear filters
            </button>

        </div>


        {{-- =================================================
            FOOTER
        ================================================== --}}

        <footer class="pickup-list-footer">

            <span
                data-assignment-visible-count
                aria-live="polite"
            >
                {{ $pickupGroups->count() }}
                pickup batches shown
            </span>


            <span>
                {{ $stats['available_riders'] }}
                available Rider{{
                    $stats['available_riders'] === 1
                        ? ''
                        : 's'
                }}
            </span>

        </footer>

    </section>

</div>

@endsection


@push('scripts')

<script>
(() => {
    'use strict';


    function initializePickupAssignment() {

        document.querySelectorAll(
            '[data-pickup-workspace="assignment"]'
        ).forEach((root) => {

            if (root.dataset.initialized) {
                return;
            }

            root.dataset.initialized = 'true';


            const batches = [
                ...root.querySelectorAll(
                    '[data-assignment-batch]'
                )
            ];


            const search =
                root.querySelector(
                    '[data-assignment-search]'
                );


            const tabs = [
                ...root.querySelectorAll(
                    '[data-assignment-filter]'
                )
            ];


            const empty =
                root.querySelector(
                    '[data-assignment-empty]'
                );


            const visibleCount =
                root.querySelector(
                    '[data-assignment-visible-count]'
                );


            let filter = 'all';


            /*
            |--------------------------------------------------------------------------
            | Normalize
            |--------------------------------------------------------------------------
            */

            const normalize = (value) => {
                return String(value || '')
                    .toLocaleLowerCase()
                    .trim();
            };


            /*
            |--------------------------------------------------------------------------
            | Search + filter
            |--------------------------------------------------------------------------
            */

            function applyFilters() {

                const keyword =
                    normalize(
                        search?.value
                    );


                let shown = 0;


                batches.forEach((batch) => {

                    const matchesType =
                        filter === 'all'
                        || batch.dataset.batchType
                            === filter;


                    const matchesSearch =
                        normalize(
                            batch.dataset.search
                        ).includes(keyword);


                    batch.hidden =
                        !matchesType
                        || !matchesSearch;


                    if (!batch.hidden) {
                        shown++;
                    }

                });


                tabs.forEach((tab) => {

                    tab.setAttribute(
                        'aria-pressed',
                        String(
                            tab.dataset.assignmentFilter
                            === filter
                        )
                    );

                });


                if (empty) {
                    empty.hidden =
                        batches.length === 0
                        || shown > 0;
                }


                if (visibleCount) {

                    visibleCount.textContent =
                        `${shown} of ${batches.length} `
                        + `pickup ${
                            batches.length === 1
                                ? 'batch'
                                : 'batches'
                        } shown`;
                }

            }


            /*
            |--------------------------------------------------------------------------
            | Filter buttons
            |--------------------------------------------------------------------------
            */

            tabs.forEach((tab) => {

                tab.addEventListener(
                    'click',
                    () => {

                        filter =
                            tab.dataset
                                .assignmentFilter
                            || 'all';

                        applyFilters();
                    }
                );

            });


            /*
            |--------------------------------------------------------------------------
            | Search
            |--------------------------------------------------------------------------
            */

            search?.addEventListener(
                'input',
                applyFilters
            );


            /*
            |--------------------------------------------------------------------------
            | Clear filters
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-assignment-clear]'
            ).forEach((button) => {

                button.addEventListener(
                    'click',
                    () => {

                        filter = 'all';

                        if (search) {
                            search.value = '';
                        }

                        applyFilters();
                    }
                );

            });


            /*
            |--------------------------------------------------------------------------
            | Rider preview
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-rider-select]'
            ).forEach((select) => {

                const form =
                    select.closest(
                        '[data-pickup-dispatch-form]'
                    );


                const preview =
                    form?.querySelector(
                        '[data-rider-preview]'
                    );


                const nameNode =
                    form?.querySelector(
                        '[data-rider-preview-name]'
                    );


                const areaNode =
                    form?.querySelector(
                        '[data-rider-preview-area]'
                    );


                const vehicleNode =
                    form?.querySelector(
                        '[data-rider-preview-vehicle]'
                    );


                function renderRider() {

                    const option =
                        select.options[
                            select.selectedIndex
                        ];


                    if (
                        !option
                        || !option.value
                    ) {

                        if (preview) {
                            preview.hidden = true;
                        }

                        return;
                    }


                    const riderName =
                        option.dataset.riderName
                        || option.textContent.trim();


                    const riderArea =
                        option.dataset.riderArea
                        || 'Service area not specified';


                    const vehicle =
                        option.dataset.riderVehicle
                        || 'Vehicle not specified';


                    const plate =
                        option.dataset.riderPlate;


                    if (nameNode) {
                        nameNode.textContent =
                            riderName;
                    }


                    if (areaNode) {
                        areaNode.textContent =
                            riderArea;
                    }


                    if (vehicleNode) {

                        vehicleNode.textContent =
                            plate
                                ? `${vehicle} · ${plate}`
                                : vehicle;
                    }


                    if (preview) {
                        preview.hidden = false;
                    }

                }


                select.addEventListener(
                    'change',
                    renderRider
                );


                renderRider();

            });


            /*
            |--------------------------------------------------------------------------
            | Dispatch confirmation
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-pickup-dispatch-form]'
            ).forEach((form) => {

                form.addEventListener(
                    'submit',
                    (event) => {

                        const riderSelect =
                            form.querySelector(
                                '[data-rider-select]'
                            );


                        if (
                            !riderSelect
                            || !riderSelect.value
                        ) {
                            return;
                        }


                        const riderOption =
                            riderSelect.options[
                                riderSelect.selectedIndex
                            ];


                        const riderName =
                            riderOption
                                ?.dataset
                                ?.riderName
                            || 'this Rider';


                        const parcelCount =
                            form.querySelectorAll(
                                'input[name="order_ids[]"]'
                            ).length;


                        const confirmed =
                            window.confirm(
                                `Assign ${parcelCount} `
                                + `${
                                    parcelCount === 1
                                        ? 'parcel'
                                        : 'parcels'
                                } to ${riderName} `
                                + 'for this Seller pickup?'
                            );


                        if (!confirmed) {
                            event.preventDefault();
                            return;
                        }


                        const submitButton =
                            form.querySelector(
                                'button[type="submit"]'
                            );


                        if (submitButton) {

                            submitButton.disabled = true;

                            submitButton.textContent =
                                'Assigning batch…';
                        }

                    }
                );

            });


            /*
            |--------------------------------------------------------------------------
            | Focus requested order
            |--------------------------------------------------------------------------
            |
            | Pickup Requests links to:
            | /delivery-assignment?order=123
            |
            | Find the batch containing that order and scroll it into view.
            |
            */

            const focusOrder =
                String(
                    root.dataset.focusOrder
                    || ''
                );


            if (
                focusOrder
                && focusOrder !== '0'
            ) {

                const target =
                    batches.find((batch) => {

                        const ids =
                            String(
                                batch.dataset.orderIds
                                || ''
                            )
                                .split(',')
                                .map(
                                    (value) =>
                                        value.trim()
                                );

                        return ids.includes(
                            focusOrder
                        );
                    });


                if (target) {

                    target.dataset.focused =
                        'true';


                    window.setTimeout(
                        () => {

                            target.scrollIntoView({
                                behavior:
                                    window.matchMedia(
                                        '(prefers-reduced-motion: reduce)'
                                    ).matches
                                        ? 'auto'
                                        : 'smooth',

                                block: 'center',
                            });

                        },
                        120
                    );


                    window.setTimeout(
                        () => {
                            delete target.dataset.focused;
                        },
                        3500
                    );
                }

            }


            applyFilters();

        });

    }


    if (
        document.readyState === 'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            initializePickupAssignment
        );

    } else {

        initializePickupAssignment();
    }


    window.addEventListener(
        'pageshow',
        (event) => {

            if (event.persisted) {
                window.location.reload();
            }

        }
    );

})();
</script>

@endpush