@extends('layouts.logistics')

@section('title', 'Last Mile Assignment — SARI Logistics')
@section('page-title', 'Last Mile Assignment')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/logistics/last-mile-assignment.css') }}?v=20261015-1"
    >
@endpush


@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Page Summary
    |--------------------------------------------------------------------------
    */

    $readyParcelCount =
        $deliveryGroups
            ->sum(
                fn ($group) =>
                    $group->count()
            );

    $deliveryAreaCount =
        $deliveryGroups->count();


    /*
    |--------------------------------------------------------------------------
    | Riders already carrying an active Last-Mile batch
    |--------------------------------------------------------------------------
    */

    $busyRiderIds =
        $assignments
            ->whereIn(
                'status',
                [
                    'assigned',
                    'out_for_delivery',
                ]
            )
            ->pluck(
                'courier_account_id'
            )
            ->map(
                fn ($id) =>
                    (int) $id
            )
            ->unique();


    $availableRiderCount =
        $riders
            ->reject(
                fn ($rider) =>
                    $busyRiderIds->contains(
                        (int) $rider->id
                    )
            )
            ->count();


    $activeAssignmentCount =
        $assignments
            ->whereIn(
                'status',
                [
                    'assigned',
                    'out_for_delivery',
                ]
            )
            ->count();


    $completedAssignmentCount =
        $assignments
            ->where(
                'status',
                'completed'
            )
            ->count();


    $summaryCards = [
        [
            'Ready parcels',
            $readyParcelCount,
            'At destination hubs',
            'amber',
        ],
        [
            'Delivery areas',
            $deliveryAreaCount,
            'Barangay dispatch groups',
            'neutral',
        ],
        [
            'Available Riders',
            $availableRiderCount,
            'Online and without active batch',
            'blue',
        ],
        [
            'Active batches',
            $activeAssignmentCount,
            'Assigned or out for delivery',
            'green',
        ],
    ];
@endphp


<div
    class="lastmile-page"
    data-lastmile-workspace
>

    {{-- =====================================================
        HERO
    ====================================================== --}}

    <header class="lastmile-page-header">

        <div>

            <p class="lastmile-eyebrow">
                DESTINATION HUB DISPATCH
            </p>

            <h1>
                Last Mile <span>Assignment</span>
            </h1>

            <p class="lastmile-subtitle">
                Dispatch destination-hub parcels to local Riders by
                municipality and barangay. Orders within the same delivery
                area can be assigned together as one Rider batch.
            </p>

        </div>


        <div class="lastmile-header-actions">

            <a
                href="{{ route('logistics.hub-transfers') }}"
                class="lastmile-button"
            >
                <span aria-hidden="true">←</span>
                Hub Transfers
            </a>


            <a
                href="{{ route('logistics.delivery-monitoring') }}"
                class="lastmile-button lastmile-button-primary"
            >
                Delivery Monitoring
                <span aria-hidden="true">→</span>
            </a>

        </div>

    </header>


    {{-- =====================================================
        FLASH MESSAGES
    ====================================================== --}}

    @if(session('success'))

        <div
            class="lastmile-alert lastmile-alert-success"
            role="status"
        >
            <strong>Last-mile assignment updated</strong>

            <p>
                {{ session('success') }}
            </p>
        </div>

    @endif


    @if($errors->any())

        <div
            class="lastmile-alert lastmile-alert-danger"
            role="alert"
        >
            <strong>Could not assign Last-Mile Rider</strong>

            <ul>
                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach
            </ul>
        </div>

    @endif


    {{-- =====================================================
        SUMMARY
    ====================================================== --}}

    <div class="lastmile-stats">

        @foreach(
            $summaryCards
            as [$label, $value, $note, $tone]
        )

            <article
                class="
                    lastmile-stat
                    lastmile-stat-{{ $tone }}
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
        OPERATIONS FLOW
    ====================================================== --}}

    <nav
        class="lastmile-flow"
        aria-label="Destination hub workflow"
    >

        <a
            href="{{ route('logistics.parcel-sorting') }}"
        >
            <b>1</b>
            Origin sorting
        </a>


        <a
            href="{{ route('logistics.hub-transfers') }}"
        >
            <b>2</b>
            Hub transfer
        </a>


        <span
            class="is-current"
            aria-current="step"
        >
            <b>3</b>
            Last-mile assignment
        </span>


        <a
            href="{{ route('logistics.delivery-monitoring') }}"
        >
            <b>4</b>
            Delivery monitoring
        </a>

    </nav>


    {{-- =====================================================
        DISPATCH QUEUE
    ====================================================== --}}

    <section
        class="lastmile-workspace"
        aria-labelledby="lastmile-queue-heading"
    >

        {{-- Toolbar --}}
        <div class="lastmile-toolbar">

            <div>

                <h2 id="lastmile-queue-heading">
                    Barangay dispatch queue
                </h2>

                <p>
                    Destination-hub parcels are grouped by local
                    service area before Rider assignment.
                </p>

            </div>


            <label class="lastmile-search">

                <span class="sr-only">
                    Search Last-Mile dispatch queue
                </span>

                <input
                    type="search"
                    data-area-search
                    placeholder="Search barangay, hub, order, or Buyer…"
                >

            </label>

        </div>


        {{-- Filters --}}
        @php
            $serviceableGroupCount = 0;
            $unservedGroupCount = 0;

            foreach($deliveryGroups as $group) {
                $first = $group->first();

                $groupEligibleRiders =
                    $riders
                        ->filter(
                            function ($rider) use (
                                $first,
                                $busyRiderIds
                            ) {
                                $municipalityMatches =
                                    mb_strtolower(
                                        trim(
                                            (string) $rider
                                                ->municipality_name
                                        )
                                    )
                                    ===
                                    mb_strtolower(
                                        trim(
                                            (string) $first
                                                ->destination_municipality
                                        )
                                    );

                                $barangayMatches =
                                    mb_strtolower(
                                        trim(
                                            (string) $rider
                                                ->barangay_name
                                        )
                                    )
                                    ===
                                    mb_strtolower(
                                        trim(
                                            (string) $first
                                                ->barangay_name
                                        )
                                    );

                                $available =
                                    ! $busyRiderIds
                                        ->contains(
                                            (int) $rider->id
                                        );

                                return
                                    $municipalityMatches
                                    &&
                                    $barangayMatches
                                    &&
                                    $available;
                            }
                        );

                if ($groupEligibleRiders->isNotEmpty()) {
                    $serviceableGroupCount++;
                } else {
                    $unservedGroupCount++;
                }
            }
        @endphp


        <div class="lastmile-filter-row">

            <div
                class="lastmile-tabs"
                role="group"
                aria-label="Filter delivery areas"
            >

                <button
                    type="button"
                    data-area-filter="all"
                    aria-pressed="true"
                >
                    All Areas

                    <span>
                        {{ $deliveryAreaCount }}
                    </span>
                </button>


                <button
                    type="button"
                    data-area-filter="serviceable"
                    aria-pressed="false"
                >
                    Rider Available

                    <span>
                        {{ $serviceableGroupCount }}
                    </span>
                </button>


                <button
                    type="button"
                    data-area-filter="unserved"
                    aria-pressed="false"
                >
                    Needs Rider

                    <span>
                        {{ $unservedGroupCount }}
                    </span>
                </button>

            </div>


            <button
                type="button"
                class="lastmile-text-link"
                data-area-clear
            >
                Clear filters
            </button>

        </div>


        {{-- =================================================
            DELIVERY GROUPS
        ================================================== --}}

        <div class="lastmile-area-list">

            @forelse(
                $deliveryGroups
                as $groupKey => $group
            )

                @php
                    $first =
                        $group->first();


                    /*
                    |--------------------------------------------------------------------------
                    | Exact local Rider matching
                    |--------------------------------------------------------------------------
                    */

                    $eligibleRiders =
                        $riders
                            ->filter(
                                function ($rider) use (
                                    $first,
                                    $busyRiderIds
                                ) {
                                    $municipalityMatches =
                                        mb_strtolower(
                                            trim(
                                                (string) $rider
                                                    ->municipality_name
                                            )
                                        )
                                        ===
                                        mb_strtolower(
                                            trim(
                                                (string) $first
                                                    ->destination_municipality
                                            )
                                        );

                                    $barangayMatches =
                                        mb_strtolower(
                                            trim(
                                                (string) $rider
                                                    ->barangay_name
                                            )
                                        )
                                        ===
                                        mb_strtolower(
                                            trim(
                                                (string) $first
                                                    ->barangay_name
                                            )
                                        );

                                    $available =
                                        ! $busyRiderIds
                                            ->contains(
                                                (int) $rider->id
                                            );

                                    return
                                        $municipalityMatches
                                        &&
                                        $barangayMatches
                                        &&
                                        $available;
                                }
                            )
                            ->values();


                    $allAreaRiders =
                        $riders
                            ->filter(
                                function ($rider) use (
                                    $first
                                ) {
                                    return
                                        mb_strtolower(
                                            trim(
                                                (string) $rider
                                                    ->municipality_name
                                            )
                                        )
                                        ===
                                        mb_strtolower(
                                            trim(
                                                (string) $first
                                                    ->destination_municipality
                                            )
                                        )
                                        &&
                                        mb_strtolower(
                                            trim(
                                                (string) $rider
                                                    ->barangay_name
                                            )
                                        )
                                        ===
                                        mb_strtolower(
                                            trim(
                                                (string) $first
                                                    ->barangay_name
                                            )
                                        );
                                }
                            );


                    $busyAreaRiderCount =
                        $allAreaRiders
                            ->filter(
                                fn ($rider) =>
                                    $busyRiderIds
                                        ->contains(
                                            (int) $rider->id
                                        )
                            )
                            ->count();


                    $areaState =
                        $eligibleRiders->isNotEmpty()
                            ? 'serviceable'
                            : 'unserved';


                    $searchValues = [
                        $first->barangay_name,
                        $first->destination_facility_name,
                        $first->destination_municipality,
                    ];


                    foreach($group as $parcel) {
                        $searchValues[] =
                            $parcel->order_number;

                        $searchValues[] =
                            $parcel->buyer_name;

                        $searchValues[] =
                            $parcel->buyer_address;

                        $searchValues[] =
                            $parcel->pickup_rider_name;
                    }


                    $searchText =
                        implode(
                            ' ',
                            array_filter(
                                $searchValues
                            )
                        );
                @endphp


                <article
                    class="lastmile-area-card"
                    data-area-card
                    data-area-state="{{ $areaState }}"
                    data-search="{{ $searchText }}"
                >

                    {{-- =====================================
                        AREA HEADER
                    ====================================== --}}

                    <header class="lastmile-area-header">

                        <div class="lastmile-area-title">

                            <span
                                class="lastmile-area-icon"
                                aria-hidden="true"
                            >
                                {{
                                    mb_strtoupper(
                                        mb_substr(
                                            trim(
                                                (string) $first
                                                    ->barangay_name
                                            ),
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </span>


                            <div>

                                <p class="lastmile-eyebrow">
                                    LOCAL DELIVERY AREA
                                </p>


                                <h3>
                                    {{ $first->barangay_name }}
                                </h3>


                                <p>
                                    {{ $first->destination_facility_name }}
                                    <span aria-hidden="true">·</span>
                                    {{ $first->destination_municipality }}
                                </p>

                            </div>

                        </div>


                        <div class="lastmile-area-tags">

                            <span>
                                {{ $group->count() }}
                                {{
                                    $group->count() === 1
                                        ? 'parcel'
                                        : 'parcels'
                                }}
                            </span>


                            @if($eligibleRiders->isNotEmpty())

                                <span class="is-serviceable">
                                    {{ $eligibleRiders->count() }}
                                    {{
                                        $eligibleRiders->count() === 1
                                            ? 'Rider'
                                            : 'Riders'
                                    }}
                                    available
                                </span>

                            @else

                                <span class="is-unserved">
                                    No Rider available
                                </span>

                            @endif

                        </div>

                    </header>


                    {{-- =====================================
                        AREA BODY
                    ====================================== --}}

                    <div class="lastmile-area-layout">


                        {{-- =================================
                            PARCELS
                        ================================== --}}

                        <div class="lastmile-area-main">

                            <div class="lastmile-route-summary">

                                <div>

                                    <span class="lastmile-label">
                                        Destination Hub
                                    </span>

                                    <strong>
                                        {{ $first->destination_facility_name }}
                                    </strong>

                                    <p>
                                        {{
                                            $first->destination_municipality
                                        }}
                                    </p>

                                </div>


                                <span
                                    class="lastmile-route-arrow"
                                    aria-hidden="true"
                                >
                                    →
                                </span>


                                <div>

                                    <span class="lastmile-label">
                                        Local Delivery Area
                                    </span>

                                    <strong>
                                        Barangay
                                        {{ $first->barangay_name }}
                                    </strong>

                                    <p>
                                        One local Rider may deliver
                                        the entire batch.
                                    </p>

                                </div>

                            </div>


                            <div class="lastmile-parcel-heading">

                                <div>

                                    <h4>
                                        Parcels in this delivery batch
                                    </h4>

                                    <p>
                                        Different Buyers can share the same
                                        local Rider when the barangay matches.
                                    </p>

                                </div>


                                <span>
                                    {{ $group->count() }}
                                </span>

                            </div>


                            <div class="lastmile-parcel-list">

                                @foreach(
                                    $group
                                    as $parcel
                                )

                                    <input
                                        form="lastmile-form-{{ md5($groupKey) }}"
                                        type="hidden"
                                        name="order_ids[]"
                                        value="{{ $parcel->order_id }}"
                                    >


                                    <article class="lastmile-parcel">

                                        <div class="lastmile-parcel-main">

                                            <strong>
                                                {{ $parcel->order_number }}
                                            </strong>


                                            <p>
                                                {{ $parcel->buyer_name }}
                                            </p>

                                        </div>


                                        <span class="lastmile-parcel-status">
                                            DESTINATION RECEIVED
                                        </span>


                                        <div class="lastmile-parcel-address">

                                            <span class="lastmile-label">
                                                Delivery Address
                                            </span>


                                            <p>
                                                {{ $parcel->buyer_address }}
                                            </p>

                                        </div>


                                        <details class="lastmile-details">

                                            <summary>
                                                Handoff details

                                                <span aria-hidden="true">
                                                    +
                                                </span>
                                            </summary>


                                            <div class="lastmile-detail-body">

                                                <div class="lastmile-detail-grid">

                                                    <div>

                                                        <span class="lastmile-label">
                                                            Buyer
                                                        </span>

                                                        <strong>
                                                            {{ $parcel->buyer_name }}
                                                        </strong>

                                                        <p>
                                                            {{ $parcel->buyer_address }}
                                                        </p>

                                                    </div>


                                                    <div>

                                                        <span class="lastmile-label">
                                                            Pickup Rider Snapshot
                                                        </span>

                                                        <strong>
                                                            {{
                                                                $parcel->pickup_rider_name
                                                                ?: 'Pickup Rider'
                                                            }}
                                                        </strong>

                                                        <p>
                                                            {{
                                                                $parcel->pickup_rider_email
                                                                ?: 'No pickup Rider email'
                                                            }}
                                                        </p>

                                                    </div>


                                                    <div>

                                                        <span class="lastmile-label">
                                                            Destination Hub
                                                        </span>

                                                        <strong>
                                                            {{
                                                                $parcel
                                                                    ->destination_facility_name
                                                            }}
                                                        </strong>

                                                        <p>
                                                            {{
                                                                $parcel
                                                                    ->destination_municipality
                                                            }}
                                                        </p>

                                                    </div>


                                                    <div>

                                                        <span class="lastmile-label">
                                                            Last-Mile Area
                                                        </span>

                                                        <strong>
                                                            Barangay
                                                            {{ $parcel->barangay_name }}
                                                        </strong>

                                                        <p>
                                                            Local Rider service area
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>

                                        </details>

                                    </article>

                                @endforeach

                            </div>

                        </div>


                        {{-- =================================
                            RIDER ASSIGNMENT PANEL
                        ================================== --}}

                        <aside class="lastmile-dispatch-panel">

                            <h4>
                                Assign Local Rider
                            </h4>


                            <p class="lastmile-dispatch-note">
                                Only Riders assigned to
                                {{ $first->barangay_name }},
                                {{ $first->destination_municipality }}
                                can receive this batch.
                            </p>


                            <form
                                id="lastmile-form-{{ md5($groupKey) }}"
                                method="POST"
                                action="{{ route(
                                    'logistics.last-mile-assignment.batch-assign'
                                ) }}"
                                data-lastmile-form
                                data-area-name="{{ $first->barangay_name }}"
                            >

                                @csrf


                                <label>

                                    <span class="lastmile-label">
                                        Local Rider
                                    </span>


                                    <select
                                        name="rider_id"
                                        required
                                        data-lastmile-rider
                                        @disabled(
                                            $eligibleRiders->isEmpty()
                                        )
                                    >

                                        <option value="">
                                            Select local Rider
                                        </option>


                                        @foreach(
                                            $eligibleRiders
                                            as $rider
                                        )

                                            @php
                                                $riderName =
                                                    trim(
                                                        $rider->first_name
                                                        .' '
                                                        .$rider->last_name
                                                    );
                                            @endphp


                                            <option
                                                value="{{ $rider->id }}"
                                                data-rider-name="{{ $riderName }}"
                                                data-rider-area="{{ $rider->barangay_name }}, {{ $rider->municipality_name }}"
                                                data-rider-vehicle="{{ $rider->vehicle_type }}"
                                            >
                                                {{ $riderName }}

                                                @if($rider->vehicle_type)
                                                    —
                                                    {{ $rider->vehicle_type }}
                                                @endif
                                            </option>

                                        @endforeach

                                    </select>

                                </label>


                                <div
                                    class="lastmile-rider-preview"
                                    data-rider-preview
                                    hidden
                                >

                                    <span
                                        class="lastmile-rider-avatar"
                                        data-rider-initial
                                    >
                                        R
                                    </span>


                                    <div>

                                        <strong
                                            data-rider-preview-name
                                        ></strong>

                                        <p
                                            data-rider-preview-area
                                        ></p>

                                        <p
                                            data-rider-preview-vehicle
                                        ></p>

                                    </div>

                                </div>


                                <div class="lastmile-dispatch-total">

                                    <span>
                                        Delivery batch
                                    </span>

                                    <strong>
                                        {{ $group->count() }}
                                    </strong>

                                </div>


                                <button
                                    type="submit"
                                    class="
                                        lastmile-button
                                        lastmile-button-primary
                                    "
                                    @disabled(
                                        $eligibleRiders->isEmpty()
                                    )
                                >
                                    Assign
                                    {{ $group->count() }}
                                    {{
                                        $group->count() === 1
                                            ? 'Parcel'
                                            : 'Parcels'
                                    }}
                                </button>


                                <p class="lastmile-dispatch-footnote">
                                    This creates one Last-Mile Assignment
                                    for the selected local Rider.
                                </p>

                            </form>


                            @if($eligibleRiders->isEmpty())

                                <div class="lastmile-rider-warning">

                                    <strong>
                                        No available Rider
                                    </strong>


                                    <p>
                                        No free, online Rider is currently
                                        available for
                                        {{ $first->barangay_name }}.
                                    </p>


                                    @if($busyAreaRiderCount > 0)

                                        <p>
                                            {{ $busyAreaRiderCount }}
                                            local
                                            {{
                                                $busyAreaRiderCount === 1
                                                    ? 'Rider is'
                                                    : 'Riders are'
                                            }}
                                            currently handling another
                                            Last-Mile batch.
                                        </p>

                                    @endif

                                </div>

                            @endif

                        </aside>

                    </div>

                </article>

            @empty

                <div class="lastmile-empty">

                    <span
                        class="lastmile-empty-icon"
                        aria-hidden="true"
                    >
                        ✓
                    </span>


                    <h3>
                        Last-mile dispatch queue is clear
                    </h3>


                    <p>
                        Parcels appear here after a Hub Transfer is
                        physically received at its destination facility.
                    </p>


                    <a
                        href="{{ route(
                            'logistics.hub-transfers'
                        ) }}"
                        class="
                            lastmile-button
                            lastmile-button-primary
                        "
                    >
                        View Hub Transfers
                    </a>

                </div>

            @endforelse


            <div
                class="lastmile-empty"
                data-area-empty
                hidden
            >

                <h3>
                    No matching delivery areas
                </h3>


                <p>
                    Try another barangay, destination hub,
                    order number, or Buyer.
                </p>


                <button
                    type="button"
                    class="lastmile-text-link"
                    data-area-clear
                >
                    Clear filters
                </button>

            </div>

        </div>


        <footer class="lastmile-list-footer">

            <span
                data-area-visible-count
                aria-live="polite"
            >
                {{ $deliveryAreaCount }}
                delivery areas shown
            </span>


            <span>
                Exact municipality + barangay Rider matching
            </span>

        </footer>

    </section>


    {{-- =====================================================
        EXISTING ASSIGNMENTS
    ====================================================== --}}

    <section
        class="
            lastmile-workspace
            lastmile-assignment-history
        "
    >

        <div class="lastmile-toolbar">

            <div>

                <h2>
                    Last-Mile Assignments
                </h2>

                <p>
                    Track Rider batches after destination-hub dispatch.
                </p>

            </div>


            <label class="lastmile-search">

                <span class="sr-only">
                    Search Last-Mile assignments
                </span>

                <input
                    type="search"
                    data-assignment-search
                    placeholder="Search assignment, Rider, or area…"
                >

            </label>

        </div>


        @php
            $assignedCount =
                $assignments
                    ->where(
                        'status',
                        'assigned'
                    )
                    ->count();

            $outForDeliveryCount =
                $assignments
                    ->where(
                        'status',
                        'out_for_delivery'
                    )
                    ->count();
        @endphp


        <div class="lastmile-filter-row">

            <div
                class="lastmile-tabs"
                role="group"
                aria-label="Filter Last-Mile assignments"
            >

                <button
                    type="button"
                    data-assignment-filter="all"
                    aria-pressed="true"
                >
                    All

                    <span>
                        {{ $assignments->count() }}
                    </span>
                </button>


                <button
                    type="button"
                    data-assignment-filter="assigned"
                    aria-pressed="false"
                >
                    Assigned

                    <span>
                        {{ $assignedCount }}
                    </span>
                </button>


                <button
                    type="button"
                    data-assignment-filter="out_for_delivery"
                    aria-pressed="false"
                >
                    Out for Delivery

                    <span>
                        {{ $outForDeliveryCount }}
                    </span>
                </button>


                <button
                    type="button"
                    data-assignment-filter="completed"
                    aria-pressed="false"
                >
                    Completed

                    <span>
                        {{ $completedAssignmentCount }}
                    </span>
                </button>

            </div>


            <button
                type="button"
                class="lastmile-text-link"
                data-assignment-clear
            >
                Clear filters
            </button>

        </div>


        <div class="lastmile-assignment-list">

            @forelse(
                $assignments
                as $assignment
            )

                @php
                    $assignmentSearch =
                        implode(
                            ' ',
                            array_filter([
                                $assignment
                                    ->assignment_code,

                                $assignment
                                    ->facility_name,

                                $assignment
                                    ->delivery_area,

                                $assignment
                                    ->first_name,

                                $assignment
                                    ->last_name,

                                $assignment
                                    ->vehicle_type,

                                $assignment
                                    ->status,
                            ])
                        );


                    $assignmentStatusLabel =
                        ucwords(
                            str_replace(
                                '_',
                                ' ',
                                $assignment->status
                            )
                        );
                @endphp


                <article
                    class="lastmile-assignment-card"
                    data-assignment-card
                    data-assignment-state="{{ $assignment->status }}"
                    data-search="{{ $assignmentSearch }}"
                >

                    <div class="lastmile-assignment-code">

                        <span class="lastmile-label">
                            Assignment
                        </span>

                        <strong>
                            {{ $assignment->assignment_code }}
                        </strong>

                    </div>


                    <div class="lastmile-assignment-route">

                        <div>

                            <span class="lastmile-label">
                                Destination Hub
                            </span>

                            <strong>
                                {{ $assignment->facility_name }}
                            </strong>

                        </div>


                        <span
                            class="lastmile-mini-arrow"
                            aria-hidden="true"
                        >
                            →
                        </span>


                        <div>

                            <span class="lastmile-label">
                                Delivery Area
                            </span>

                            <strong>
                                Barangay
                                {{ $assignment->delivery_area }}
                            </strong>

                        </div>

                    </div>


                    <div class="lastmile-assignment-rider">

                        <span
                            class="lastmile-rider-avatar"
                            aria-hidden="true"
                        >
                            {{
                                mb_strtoupper(
                                    mb_substr(
                                        trim(
                                            (string) $assignment
                                                ->first_name
                                        ),
                                        0,
                                        1
                                    )
                                )
                            }}
                        </span>


                        <div>

                            <span class="lastmile-label">
                                Local Rider
                            </span>

                            <strong>
                                {{ $assignment->first_name }}
                                {{ $assignment->last_name }}
                            </strong>

                            <p>
                                {{
                                    $assignment->vehicle_type
                                    ?: 'Vehicle not recorded'
                                }}
                            </p>

                        </div>

                    </div>


                    <div class="lastmile-assignment-load">

                        <span class="lastmile-label">
                            Batch Load
                        </span>

                        <strong>
                            {{ $assignment->order_count }}
                        </strong>

                        <p>
                            {{
                                $assignment->order_count === 1
                                    ? 'parcel'
                                    : 'parcels'
                            }}
                        </p>

                    </div>


                    <div class="lastmile-assignment-status">

                        <span
                            class="
                                lastmile-status
                                lastmile-status-{{ $assignment->status }}
                            "
                        >
                            {{ $assignmentStatusLabel }}
                        </span>

                    </div>

                </article>

            @empty

                <div class="lastmile-empty">

                    <h3>
                        No Last-Mile assignments yet
                    </h3>

                    <p>
                        Assigned Rider batches will appear here.
                    </p>

                </div>

            @endforelse


            <div
                class="lastmile-empty"
                data-assignment-empty
                hidden
            >

                <h3>
                    No matching Last-Mile assignments
                </h3>

                <p>
                    Try another assignment code, Rider,
                    hub, or delivery area.
                </p>

            </div>

        </div>


        <footer class="lastmile-list-footer">

            <span
                data-assignment-visible-count
                aria-live="polite"
            >
                {{ $assignments->count() }}
                assignments shown
            </span>


            <a
                href="{{ route(
                    'logistics.delivery-monitoring'
                ) }}"
                class="lastmile-text-link"
            >
                Open Delivery Monitoring →
            </a>

        </footer>

    </section>

</div>

@endsection


@push('scripts')

<script>
(() => {
    'use strict';


    function initializeLastMileAssignment() {

        const root =
            document.querySelector(
                '[data-lastmile-workspace]'
            );


        if (
            !root
            || root.dataset.initialized
        ) {
            return;
        }


        root.dataset.initialized =
            'true';


        const normalize = (value) => {
            return String(value || '')
                .toLocaleLowerCase()
                .trim();
        };


        /*
        |--------------------------------------------------------------------------
        | Delivery Area Search + Filter
        |--------------------------------------------------------------------------
        */

        const areaCards = [
            ...root.querySelectorAll(
                '[data-area-card]'
            )
        ];


        const areaSearch =
            root.querySelector(
                '[data-area-search]'
            );


        const areaTabs = [
            ...root.querySelectorAll(
                '[data-area-filter]'
            )
        ];


        const areaEmpty =
            root.querySelector(
                '[data-area-empty]'
            );


        const areaVisibleCount =
            root.querySelector(
                '[data-area-visible-count]'
            );


        let areaFilter =
            'all';


        function applyAreaFilters() {

            const keyword =
                normalize(
                    areaSearch?.value
                );


            let shown =
                0;


            areaCards.forEach(
                (card) => {

                    const stateMatches =
                        areaFilter === 'all'
                        ||
                        card.dataset.areaState
                            === areaFilter;


                    const searchMatches =
                        normalize(
                            card.dataset.search
                        ).includes(
                            keyword
                        );


                    card.hidden =
                        !stateMatches
                        ||
                        !searchMatches;


                    if (!card.hidden) {
                        shown++;
                    }

                }
            );


            areaTabs.forEach(
                (tab) => {

                    tab.setAttribute(
                        'aria-pressed',
                        String(
                            tab.dataset.areaFilter
                            === areaFilter
                        )
                    );

                }
            );


            if (areaEmpty) {

                areaEmpty.hidden =
                    areaCards.length === 0
                    ||
                    shown > 0;
            }


            if (areaVisibleCount) {

                areaVisibleCount.textContent =
                    `${shown} of ${areaCards.length} `
                    + `${
                        areaCards.length === 1
                            ? 'delivery area'
                            : 'delivery areas'
                    } shown`;
            }

        }


        areaTabs.forEach(
            (tab) => {

                tab.addEventListener(
                    'click',
                    () => {

                        areaFilter =
                            tab.dataset.areaFilter
                            || 'all';


                        applyAreaFilters();
                    }
                );

            }
        );


        areaSearch?.addEventListener(
            'input',
            applyAreaFilters
        );


        root.querySelectorAll(
            '[data-area-clear]'
        ).forEach(
            (button) => {

                button.addEventListener(
                    'click',
                    () => {

                        areaFilter =
                            'all';


                        if (areaSearch) {
                            areaSearch.value = '';
                        }


                        applyAreaFilters();
                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Rider Preview
        |--------------------------------------------------------------------------
        */

        root.querySelectorAll(
            '[data-lastmile-rider]'
        ).forEach(
            (select) => {

                const form =
                    select.closest(
                        '[data-lastmile-form]'
                    );


                const preview =
                    form?.querySelector(
                        '[data-rider-preview]'
                    );


                const initial =
                    form?.querySelector(
                        '[data-rider-initial]'
                    );


                const name =
                    form?.querySelector(
                        '[data-rider-preview-name]'
                    );


                const area =
                    form?.querySelector(
                        '[data-rider-preview-area]'
                    );


                const vehicle =
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
                        ||
                        !option.value
                    ) {

                        if (preview) {
                            preview.hidden = true;
                        }

                        return;
                    }


                    const riderName =
                        option.dataset.riderName
                        ||
                        option.textContent.trim();


                    if (name) {
                        name.textContent =
                            riderName;
                    }


                    if (area) {
                        area.textContent =
                            option.dataset.riderArea
                            || 'Local delivery area';
                    }


                    if (vehicle) {
                        vehicle.textContent =
                            option.dataset.riderVehicle
                            || 'Vehicle not recorded';
                    }


                    if (initial) {

                        initial.textContent =
                            riderName
                                .trim()
                                .charAt(0)
                                .toUpperCase()
                            || 'R';
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

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Assignment confirmation
        |--------------------------------------------------------------------------
        */

        root.querySelectorAll(
            '[data-lastmile-form]'
        ).forEach(
            (form) => {

                form.addEventListener(
                    'submit',
                    (event) => {

                        const select =
                            form.querySelector(
                                '[data-lastmile-rider]'
                            );


                        if (
                            !select
                            ||
                            !select.value
                        ) {
                            return;
                        }


                        const option =
                            select.options[
                                select.selectedIndex
                            ];


                        const riderName =
                            option.dataset.riderName
                            ||
                            'this Rider';


                        const area =
                            form.dataset.areaName
                            ||
                            'this delivery area';


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
                                } in ${area} `
                                + `to ${riderName}?`
                            );


                        if (!confirmed) {

                            event.preventDefault();

                            return;
                        }


                        const button =
                            form.querySelector(
                                'button[type="submit"]'
                            );


                        if (button) {

                            button.disabled =
                                true;


                            button.textContent =
                                'Assigning Rider…';
                        }

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Assignment History Search + Filter
        |--------------------------------------------------------------------------
        */

        const assignmentCards = [
            ...root.querySelectorAll(
                '[data-assignment-card]'
            )
        ];


        const assignmentSearch =
            root.querySelector(
                '[data-assignment-search]'
            );


        const assignmentTabs = [
            ...root.querySelectorAll(
                '[data-assignment-filter]'
            )
        ];


        const assignmentEmpty =
            root.querySelector(
                '[data-assignment-empty]'
            );


        const assignmentVisibleCount =
            root.querySelector(
                '[data-assignment-visible-count]'
            );


        let assignmentFilter =
            'all';


        function applyAssignmentFilters() {

            const keyword =
                normalize(
                    assignmentSearch?.value
                );


            let shown =
                0;


            assignmentCards.forEach(
                (card) => {

                    const stateMatches =
                        assignmentFilter
                            === 'all'
                        ||
                        card.dataset
                            .assignmentState
                            === assignmentFilter;


                    const searchMatches =
                        normalize(
                            card.dataset.search
                        ).includes(
                            keyword
                        );


                    card.hidden =
                        !stateMatches
                        ||
                        !searchMatches;


                    if (!card.hidden) {
                        shown++;
                    }

                }
            );


            assignmentTabs.forEach(
                (tab) => {

                    tab.setAttribute(
                        'aria-pressed',
                        String(
                            tab.dataset
                                .assignmentFilter
                            === assignmentFilter
                        )
                    );

                }
            );


            if (assignmentEmpty) {

                assignmentEmpty.hidden =
                    assignmentCards.length
                        === 0
                    ||
                    shown > 0;
            }


            if (assignmentVisibleCount) {

                assignmentVisibleCount.textContent =
                    `${shown} of ${assignmentCards.length} `
                    + `${
                        assignmentCards.length === 1
                            ? 'assignment'
                            : 'assignments'
                    } shown`;
            }

        }


        assignmentTabs.forEach(
            (tab) => {

                tab.addEventListener(
                    'click',
                    () => {

                        assignmentFilter =
                            tab.dataset
                                .assignmentFilter
                            || 'all';


                        applyAssignmentFilters();
                    }
                );

            }
        );


        assignmentSearch?.addEventListener(
            'input',
            applyAssignmentFilters
        );


        root.querySelectorAll(
            '[data-assignment-clear]'
        ).forEach(
            (button) => {

                button.addEventListener(
                    'click',
                    () => {

                        assignmentFilter =
                            'all';


                        if (assignmentSearch) {
                            assignmentSearch.value = '';
                        }


                        applyAssignmentFilters();
                    }
                );

            }
        );


        applyAreaFilters();
        applyAssignmentFilters();

    }


    if (
        document.readyState === 'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            initializeLastMileAssignment
        );

    } else {

        initializeLastMileAssignment();
    }

})();
</script>

@endpush