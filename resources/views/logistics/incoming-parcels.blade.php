
@extends('layouts.logistics')

@section('title', 'Incoming Parcels — SARI Logistics')
@section('page-title', 'Incoming Parcels')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/logistics/incoming-parcels.css') }}?v=20261015-1">

<style>
/*
|--------------------------------------------------------------------------
| SARI LOGISTICS — ORIGIN HUB INTAKE
| Seller-style visual refinement
|--------------------------------------------------------------------------
| Page-scoped overrides only.
| The original incoming-parcels.css remains unchanged.
*/

#lgIncomingParcels {
    max-width: 1620px;
    --intake-gold: #d9950b;
    --intake-line: #e8e2d9;
    --intake-ink: #25211c;
    font-size: 10px;
}

#lgIncomingParcels [hidden] {
    display: none !important;
}

/* PAGE HEADING */

#lgIncomingParcels .intake-page-header {
    min-height: 0;
    padding: 5px 2px 17px;
    margin-bottom: 14px;
    gap: 16px;
    overflow: visible;
    border: 0;
    border-bottom: 1px solid #e4e1dc;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
    flex-wrap: wrap;
}

#lgIncomingParcels .intake-page-header::after {
    display: none;
}

#lgIncomingParcels .intake-eyebrow {
    color: #a76f11;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
}

#lgIncomingParcels .intake-page-header h1 {
    color: #24201b;
    font-size: clamp(23px, 2.15vw, 30px);
    font-weight: 750;
    letter-spacing: -.045em;
    line-height: 1.2;
}

#lgIncomingParcels .intake-page-header h1 span {
    color: #d9950b;
}

#lgIncomingParcels .intake-subtitle {
    max-width: 720px;
    margin-top: 6px;
    color: #897f75;
    font-size: 10px;
    line-height: 1.7;
}

/* HEADER ACTIONS */

#lgIncomingParcels .intake-header-actions {
    flex-wrap: wrap;
    gap: 8px;
}

#lgIncomingParcels .intake-page-header
.intake-button:not(.intake-button-primary) {
    min-height: 39px;
    padding: 0 14px;
    border: 1px solid #e4d8c6;
    border-radius: 10px;
    background: #fff;
    color: #776a5a;
    font-size: 10px;
    box-shadow: none;
}

#lgIncomingParcels .intake-page-header
.intake-button:not(.intake-button-primary):hover {
    background: #fff9ed;
    border-color: #d5bb87;
    color: #a36c0a;
}

#lgIncomingParcels .intake-button {
    min-height: 39px;
    padding: 9px 13px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 650;
}

#lgIncomingParcels .intake-button-primary {
    background: #d9950b;
    border-color: #d9950b;
    color: #fff;
}

#lgIncomingParcels .intake-button-primary:hover {
    background: #c88708;
    border-color: #c88708;
    color: #fff;
}

/* ALERTS */

#lgIncomingParcels .intake-alert strong {
    font-size: 10px;
}

#lgIncomingParcels .intake-alert p,
#lgIncomingParcels .intake-alert ul {
    font-size: 9px;
    line-height: 1.6;
}

/* KPI CARDS */

#lgIncomingParcels .intake-stats {
    gap: 12px;
}

#lgIncomingParcels .intake-stat {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    min-height: 98px;
    padding: 14px;
    border: 1px solid #e8e2d9;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 6px 18px rgba(33, 24, 14, .035);

    transition:
        transform .18s ease,
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgIncomingParcels .intake-stat::after {
    display: none;
}

#lgIncomingParcels .intake-stat p {
    color: #756b60;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: normal;
    text-transform: none;
}

#lgIncomingParcels .intake-stat strong,
#lgIncomingParcels .intake-stat-amber strong,
#lgIncomingParcels .intake-stat-green strong,
#lgIncomingParcels .intake-stat-blue strong {
    margin: 8px 0 5px;
    color: #211d18;
    font-size: 25px;
    font-weight: 750;
    line-height: 1.1;
}

#lgIncomingParcels .intake-stat span:not(.intake-stat-icon) {
    color: #8d8378;
    font-size: 9px;
    line-height: 1.5;
}

#lgIncomingParcels .intake-stat .intake-stat-icon {
    display: grid;
    place-items: center;
    flex: 0 0 41px;
    width: 41px;
    height: 41px;
    border-radius: 11px;
    background: #f1f6fd;
    color: #587ead;
}

#lgIncomingParcels .intake-stat-icon svg {
    width: 18px;
    height: 18px;
}

#lgIncomingParcels .intake-stat-amber .intake-stat-icon {
    background: #fff7e9;
    color: #bc8420;
}

#lgIncomingParcels .intake-stat-green .intake-stat-icon {
    background: #eef8f2;
    color: #4c8663;
}

#lgIncomingParcels .intake-stat-blue .intake-stat-icon {
    background: #eff5fa;
    color: #587ead;
}

/* WORKFLOW STEPPER */

#lgIncomingParcels .intake-flow {
    gap: 4px;
    margin: 14px 0;
    padding: 4px;
    border-radius: 10px;
}

#lgIncomingParcels .intake-flow :is(a, span) {
    min-height: 32px;
    padding: 5px 10px;
    border-radius: 7px;
    font-size: 9px;
}

#lgIncomingParcels .intake-flow b {
    flex: 0 0 21px;
    width: 21px;
    height: 21px;
    font-size: 9px;
}

/* WORKSPACE */

#lgIncomingParcels .intake-workspace {
    min-width: 0;
    border: 1px solid #e8e2d9;
    border-radius: 17px;
    background: #fff;
    box-shadow: 0 6px 18px rgba(33, 24, 14, .035);
}

#lgIncomingParcels .intake-toolbar {
    gap: 14px;
    padding: 15px 17px;
}

#lgIncomingParcels .intake-toolbar h2 {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.035em;
    color: #29231d;
}

#lgIncomingParcels .intake-toolbar p {
    margin-top: 4px;
    font-size: 9px;
    line-height: 1.55;
}

/* SEARCH */

#lgIncomingParcels .intake-search {
    width: min(315px, 100%);
}

#lgIncomingParcels .intake-search input {
    height: 39px;
    padding: 0 12px;
    border: 1px solid #dfe2e6;
    border-radius: 10px;
    font-size: 10px;
}

#lgIncomingParcels .intake-search input:focus,
#lgIncomingParcels .intake-receive-form textarea:focus {
    border-color: #d9950b;
    box-shadow: 0 0 0 3px rgba(217, 149, 11, .09);
}

/* FILTERS */

#lgIncomingParcels .intake-filter-row {
    gap: 10px;
    padding: 0 17px 13px;
}

#lgIncomingParcels .intake-tabs {
    gap: 4px;
    padding: 4px;
    border: 1px solid #e6e1d9;
    border-radius: 10px;
    background: #f7f8fa;
}

#lgIncomingParcels .intake-tabs button {
    min-height: 30px;
    padding: 5px 10px;
    border-radius: 7px;
    font-size: 9px;
}

#lgIncomingParcels .intake-tabs button[aria-pressed="true"] {
    border-color: #e9dfce;
    background: #fff;
    color: #a36c0a;
    box-shadow: 0 2px 5px rgba(35, 29, 22, .035);
}

#lgIncomingParcels .intake-tabs span {
    min-width: 21px;
    height: 19px;
    font-size: 8px;
}

#lgIncomingParcels .intake-text-link {
    color: #a66d0b;
    font-size: 9px;
    font-weight: 650;
}

/* DIRECTORY TABLE */

#lgIncomingParcels .intake-table-scroll {
    max-width: 100%;
    overflow-x: auto;
}

#lgIncomingParcels .intake-table {
    width: 100%;
    min-width: 1020px;
    table-layout: fixed;
    font-size: 10px;
}

#lgIncomingParcels .intake-table th:nth-child(1) {
    width: 21%;
}

#lgIncomingParcels .intake-table th:nth-child(2) {
    width: 15%;
}

#lgIncomingParcels .intake-table th:nth-child(3) {
    width: 16%;
}

#lgIncomingParcels .intake-table th:nth-child(4) {
    width: 13%;
}

#lgIncomingParcels .intake-table th:nth-child(5) {
    width: 14%;
}

#lgIncomingParcels .intake-table th:nth-child(6) {
    width: 21%;
}

#lgIncomingParcels .intake-table thead {
    background: #f7f8fa;
}

#lgIncomingParcels .intake-table th {
    padding: 12px 11px;
    color: #81776d;
    font-size: 8.5px;
    letter-spacing: .05em;
}

#lgIncomingParcels .intake-table td {
    min-width: 0;
    padding: 13px 11px;
    font-size: 10px;
    line-height: 1.55;
    vertical-align: top;
    overflow-wrap: anywhere;
}

#lgIncomingParcels .intake-table td strong {
    color: #443c33;
    font-size: 10px;
    font-weight: 650;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgIncomingParcels .intake-table td p {
    margin-top: 4px;
    color: #91887e;
    font-size: 9px;
    line-height: 1.55;
    overflow-wrap: anywhere;
}

#lgIncomingParcels .intake-table tbody tr:hover {
    background: #fffaf2;
}

/* PICKUP RIDER */

#lgIncomingParcels .intake-rider {
    min-width: 0;
    align-items: flex-start;
    gap: 8px;
}

#lgIncomingParcels .intake-rider > div {
    min-width: 0;
}

#lgIncomingParcels .intake-rider-avatar {
    display: grid;
    place-items: center;
    flex: 0 0 34px;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 700;
}

/* ROUTE */

#lgIncomingParcels .intake-route {
    flex-wrap: wrap;
    min-width: 0;
    gap: 4px;
}

#lgIncomingParcels .intake-route span {
    font-size: 8px;
    letter-spacing: .03em;
}

#lgIncomingParcels .intake-route-destination {
    max-width: 100%;
    font-size: 9px !important;
}

/* BADGES */

#lgIncomingParcels .intake-badge {
    min-height: 24px;
    padding: 5px 9px;
    font-size: 8.5px;
    font-weight: 650;
}

/* PARCEL DETAILS */

#lgIncomingParcels .intake-details {
    max-width: 100%;
    margin-top: 9px;
}

#lgIncomingParcels .intake-details summary {
    max-width: 100%;
    gap: 7px;
    font-size: 9px;
    font-weight: 650;
}

#lgIncomingParcels .intake-detail-body {
    width: 100%;
    min-width: 0;
    margin-top: 8px;
    padding: 10px;
    border: 1px solid #e8e2d9;
    border-radius: 9px;
    background: #fff;
}

#lgIncomingParcels .intake-detail-grid {
    grid-template-columns: 1fr;
    gap: 10px;
}

#lgIncomingParcels .intake-detail-grid strong {
    font-size: 9px !important;
}

#lgIncomingParcels .intake-detail-grid p {
    font-size: 9px !important;
}

#lgIncomingParcels .intake-label {
    margin-bottom: 5px;
    font-size: 8px;
    letter-spacing: .06em;
}

#lgIncomingParcels .intake-products li {
    gap: 8px;
    font-size: 9px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

/* INTAKE CONTROLS */

#lgIncomingParcels .intake-receive-form,
#lgIncomingParcels .intake-received-info {
    width: 100%;
    max-width: none;
}

#lgIncomingParcels .intake-receive-form {
    margin-top: 10px;
}

#lgIncomingParcels .intake-receive-form textarea {
    min-height: 65px;
    padding: 9px 10px;
    border: 1px solid #dfe2e6;
    border-radius: 9px;
    background: #fff;
    font-size: 9px;
    line-height: 1.5;
}

#lgIncomingParcels .intake-receive-form .intake-button {
    min-height: 36px;
    margin-top: 8px;
    padding: 7px;
    width: 100%;
    font-size: 9px;
    white-space: normal;
    line-height: 1.4;
}

/* RECEIVED STATE */

#lgIncomingParcels .intake-received-info {
    margin: 10px 0 9px;
    padding: 11px;
    border: 1px solid #e8e2d9;
    border-radius: 9px;
    background: #fbfbfc;
}

#lgIncomingParcels .intake-received-info strong {
    color: #51483e !important;
    font-size: 9px !important;
}

#lgIncomingParcels .intake-received-info p {
    color: #897f74 !important;
    font-size: 9px !important;
}

#lgIncomingParcels .intake-received-info .intake-note {
    color: #82776a !important;
    border-top-color: #e8e2d9;
}

#lgIncomingParcels .intake-status-note {
    max-width: none;
    font-size: 9px !important;
}

/* EMPTY STATES AND FOOTER */

#lgIncomingParcels .intake-empty {
    padding: 40px 20px;
}

#lgIncomingParcels .intake-empty-icon {
    width: 43px;
    height: 43px;
    border-radius: 12px;
    background: #fff7e8;
}

#lgIncomingParcels .intake-empty h3 {
    font-size: 11px;
}

#lgIncomingParcels .intake-empty p {
    font-size: 9px;
    line-height: 1.6;
}

#lgIncomingParcels .intake-list-footer {
    padding: 12px 16px;
    background: #fff;
    font-size: 9px;
}

/* HOVER ANIMATION */

@media (hover: hover) and (pointer: fine) {
    #lgIncomingParcels .intake-stat:hover {
        transform: translateY(-2px);
        border-color: #d8c9b2;
        box-shadow: 0 13px 28px rgba(43, 33, 21, .06);
    }
}

/* RESPONSIVE */

@media (max-width: 900px) {
    #lgIncomingParcels .intake-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767px) {
    #lgIncomingParcels .intake-page-header,
    #lgIncomingParcels .intake-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    #lgIncomingParcels .intake-filter-row {
        flex-direction: column;
        align-items: stretch;
        padding: 0 14px 13px;
    }

    #lgIncomingParcels .intake-search {
        width: 100%;
    }

    #lgIncomingParcels .intake-header-actions {
        width: 100%;
    }

    #lgIncomingParcels .intake-header-actions .intake-button {
        flex: 1;
    }
}

@media (max-width: 520px) {
    #lgIncomingParcels .intake-header-actions {
        flex-direction: column;
    }

    #lgIncomingParcels .intake-stat {
        padding: 12px;
    }

    #lgIncomingParcels .intake-stat strong {
        font-size: 22px;
    }

    #lgIncomingParcels .intake-stat .intake-stat-icon {
        width: 34px;
        height: 34px;
        flex-basis: 34px;
    }

    #lgIncomingParcels .intake-detail-grid {
        grid-template-columns: 1fr;
    }
}

@media (prefers-reduced-motion: reduce) {
    #lgIncomingParcels .intake-stat:hover {
        transform: none !important;
    }
}
</style>
@endpush

@section('content')

<div
    id="lgIncomingParcels"
    class="intake-page"
    data-intake-workspace
>

    {{-- PAGE HEADER --}}

    <header class="intake-page-header">

        <div class="min-w-0">

            <p class="intake-eyebrow">
                ORIGIN HUB OPERATIONS
            </p>

            <h1>
                Incoming <span>Parcels</span>
            </h1>

            <p class="intake-subtitle">
                Confirm the physical handoff of parcels delivered by
                Pickup Riders at their assigned origin sorting hub
                before they enter the sorting queue.
            </p>

        </div>

        <div class="intake-header-actions">

            <a
                href="{{ route('logistics.delivery-assignment') }}"
                class="intake-button"
            >
                <span aria-hidden="true">←</span>
                Pickup Assignment
            </a>

            <a
                href="{{ route('logistics.parcel-sorting') }}"
                class="intake-button intake-button-primary"
            >
                Parcel Sorting
                <span aria-hidden="true">→</span>
            </a>

        </div>

    </header>

    {{-- FLASH MESSAGES --}}

    @if(session('success'))

        <div
            class="intake-alert intake-alert-success"
            role="status"
        >
            <strong>Hub intake updated</strong>
            <p>{{ session('success') }}</p>
        </div>

    @endif

    @if($errors->any())

        <div
            class="intake-alert intake-alert-danger"
            role="alert"
        >
            <strong>Could not update parcel intake</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif

    {{-- PAGE DATA --}}

    @php
        $waitingCount = $orders->filter(function ($order) use ($parcels) {

            $parcel = $parcels->get($order->id);

            return ! $parcel
                || $parcel->status === 'awaiting_intake';

        })->count();

        $receivedCount = $orders->filter(function ($order) use ($parcels) {

            return $parcels
                ->get($order->id)
                ?->status === 'received';

        })->count();

        $pickupRiderCount = $orders
            ->pluck('courier_email')
            ->filter()
            ->map(
                fn ($email) => mb_strtolower(
                    trim((string) $email)
                )
            )
            ->unique()
            ->count();

        $originHubCount = $orders->map(function ($order) {

            return $order
                ->deliveryRouteStops
                ->firstWhere('stop_key', 'origin_facility')
                ?->logistics_facility_id;

        })->filter()->unique()->count();

        $summaryCards = [
            [
                'Incoming Parcels',
                $orders->count(),
                'Pickup Rider handoffs',
                'neutral',
            ],
            [
                'Awaiting Intake',
                $waitingCount,
                'Needs physical confirmation',
                'amber',
            ],
            [
                'Received',
                $receivedCount,
                'Ready for sorting',
                'green',
            ],
            [
                'Pickup Riders',
                $pickupRiderCount,
                $originHubCount . ' origin '
                    . ($originHubCount === 1 ? 'hub' : 'hubs'),
                'blue',
            ],
        ];
    @endphp

    {{-- KPI CARDS --}}

    <section
        class="intake-stats"
        aria-label="Origin Hub Intake overview"
    >

        @foreach($summaryCards as [$label, $value, $note, $tone])

            <article class="intake-stat intake-stat-{{ $tone }}">

                <div class="min-w-0">

                    <p>{{ $label }}</p>

                    <strong>
                        {{ number_format($value) }}
                    </strong>

                    <span>{{ $note }}</span>

                </div>

                <span
                    class="intake-stat-icon"
                    aria-hidden="true"
                >

                    @switch($tone)

                        @case('amber')

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v5l3 2"/>
                            </svg>

                            @break

                        @case('green')

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m8 12 2.5 2.5L16 9"/>
                            </svg>

                            @break

                        @case('blue')

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="8" cy="8" r="3"/>
                                <circle cx="17" cy="9" r="2.5"/>
                                <path d="M2.5 20c.5-4 2.5-6 5.5-6M13 15c3.2 0 5.2 1.7 5.7 5"/>
                            </svg>

                            @break

                        @default

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M4 5h16v14H4zM8 9h8M8 13h8"/>
                            </svg>

                    @endswitch

                </span>

            </article>

        @endforeach

    </section>

    {{-- WORKFLOW STEPPER --}}

    <nav
        class="intake-flow"
        aria-label="Pickup workflow"
    >

        <a href="{{ route('logistics.pickup-requests') }}">
            <b>1</b>
            Review &amp; Verify
        </a>

        <a href="{{ route('logistics.delivery-assignment') }}">
            <b>2</b>
            Assign Pickup Rider
        </a>

        <span
            class="is-current"
            aria-current="step"
        >
            <b>3</b>
            Origin Hub Intake
        </span>

        <a href="{{ route('logistics.parcel-sorting') }}">
            <b>4</b>
            Parcel Sorting
        </a>

    </nav>

    {{-- ORIGIN HUB INTAKE WORKSPACE --}}

    <section
        class="intake-workspace"
        aria-labelledby="incoming-parcels-heading"
    >

        <header class="intake-toolbar">

            <div>

                <p class="intake-eyebrow">
                    HUB HANDOFF QUEUE
                </p>

                <h2 id="incoming-parcels-heading">
                    Origin Facility Intake
                </h2>

                <p>
                    Confirm Rider handoff before parcels become available
                    for sorting.
                </p>

            </div>

            <label class="intake-search">

                <span class="sr-only">
                    Search incoming parcels
                </span>

                <input
                    type="search"
                    data-intake-search
                    autocomplete="off"
                    placeholder="Search order, Seller, Rider, or hub…"
                >

            </label>

        </header>

        {{-- STATUS FILTERS --}}

        <div class="intake-filter-row">

            <div
                class="intake-tabs"
                role="group"
                aria-label="Filter incoming parcels"
            >

                <button
                    type="button"
                    data-intake-filter="all"
                    aria-pressed="true"
                >
                    All
                    <span>{{ $orders->count() }}</span>
                </button>

                <button
                    type="button"
                    data-intake-filter="waiting"
                    aria-pressed="false"
                >
                    Awaiting Intake
                    <span>{{ $waitingCount }}</span>
                </button>

                <button
                    type="button"
                    data-intake-filter="received"
                    aria-pressed="false"
                >
                    Received
                    <span>{{ $receivedCount }}</span>
                </button>

            </div>

            <button
                type="button"
                class="intake-text-link"
                data-intake-clear
            >
                Clear filters
            </button>

        </div>

        {{-- PARCEL TABLE --}}

        <div
            class="intake-table-scroll"
            @if($orders->isEmpty()) hidden @endif
        >

            <table class="intake-table">

                <thead>
                    <tr>
                        <th scope="col">Order / Seller</th>
                        <th scope="col">Pickup Rider</th>
                        <th scope="col">Origin Hub</th>
                        <th scope="col">Route</th>
                        <th scope="col">Pickup Time</th>
                        <th scope="col">Hub Intake</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($orders as $order)

                        @php
                            $parcel = $parcels->get($order->id);

                            $routeStops = $order
                                ->deliveryRouteStops
                                ->keyBy('stop_key');

                            $originFacility = $routeStops->get(
                                'origin_facility'
                            );

                            $destinationFacility = $routeStops->get(
                                'destination_facility'
                            );

                            $isWaiting = ! $parcel
                                || $parcel->status === 'awaiting_intake';

                            $isReceived = $parcel?->status === 'received';

                            $state = $isWaiting
                                ? 'waiting'
                                : ($isReceived ? 'received' : 'advanced');

                            $sellerName = $order->seller?->store_name
                                ?: $order->pickup_name
                                ?: 'SARI Seller';

                            $originName = $originFacility?->facility?->name
                                ?: $originFacility?->name
                                ?: 'Origin hub pending';

                            $destinationName = $destinationFacility?->facility?->name
                                ?: $destinationFacility?->name
                                ?: 'Destination pending';

                            $searchText = implode(' ', array_filter([
                                $order->order_number,
                                $sellerName,
                                $order->buyer_name,
                                $order->courier_name,
                                $order->courier_email,
                                $order->pickup_address,
                                $originName,
                                $destinationName,
                            ]));
                        @endphp

                        <tr
                            data-intake-row
                            data-state="{{ $state }}"
                            data-search="{{ $searchText }}"
                        >

                            {{-- ORDER AND SELLER --}}

                            <td>

                                <strong class="intake-order-number">
                                    {{ $order->order_number }}
                                </strong>

                                <p class="intake-primary-text">
                                    {{ $sellerName }}
                                </p>

                                <p>
                                    Buyer: {{ $order->buyer_name }}
                                </p>

                                {{-- EXPANDABLE PARCEL DETAILS --}}

                                <details class="intake-details">

                                    <summary>
                                        Parcel details
                                        <span aria-hidden="true">+</span>
                                    </summary>

                                    <div class="intake-detail-body">

                                        <div class="intake-detail-grid">

                                            <div>

                                                <span class="intake-label">
                                                    Seller Pickup
                                                </span>

                                                <strong>
                                                    {{ $order->pickup_name ?: $sellerName }}
                                                </strong>

                                                <p>
                                                    {{
                                                        $order->pickup_address
                                                        ?: 'No pickup address recorded.'
                                                    }}
                                                </p>

                                            </div>

                                            <div>

                                                <span class="intake-label">
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

                                                <span class="intake-label">
                                                    Payment
                                                </span>

                                                <strong>
                                                    {{ $order->payment_method ?: 'Not recorded' }}
                                                </strong>

                                                <p>
                                                    {{ ucfirst($order->payment_status ?: 'pending') }}
                                                </p>

                                            </div>

                                            <div>

                                                <span class="intake-label">
                                                    Order Value
                                                </span>

                                                <strong>
                                                    ₱{{ number_format((float) $order->total, 2) }}
                                                </strong>

                                                <p>
                                                    Marketplace order total
                                                </p>

                                            </div>

                                        </div>

                                        {{-- PRODUCTS --}}

                                        <ul class="intake-products">

                                            @forelse(($order->items ?? []) as $item)

                                                <li>

                                                    <span>
                                                        {{ $item['name'] ?? 'Product' }}

                                                        @if(!empty($item['variant_label']))
                                                            · {{ $item['variant_label'] }}
                                                        @endif
                                                    </span>

                                                    <strong>
                                                        × {{ $item['qty'] ?? $item['quantity'] ?? 1 }}
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

                            </td>

                            {{-- PICKUP RIDER --}}

                            <td>

                                <div class="intake-rider">

                                    <span
                                        class="intake-rider-avatar"
                                        aria-hidden="true"
                                    >
                                        {{
                                            mb_strtoupper(
                                                mb_substr(
                                                    trim(
                                                        (string) (
                                                            $order->courier_name ?: 'R'
                                                        )
                                                    ),
                                                    0,
                                                    1
                                                )
                                            )
                                        }}
                                    </span>

                                    <div>

                                        <strong>
                                            {{ $order->courier_name ?: 'Pickup Rider' }}
                                        </strong>

                                        <p>
                                            {{ $order->courier_email ?: 'No Rider email' }}
                                        </p>

                                    </div>

                                </div>

                            </td>

                            {{-- ORIGIN HUB --}}

                            <td>

                                <strong>
                                    {{ $originName }}
                                </strong>

                                <p>
                                    {{
                                        $originFacility?->facility?->municipality_name
                                        ?: 'Origin sorting facility'
                                    }}
                                </p>

                            </td>

                            {{-- ROUTE --}}

                            <td>

                                <div class="intake-route">

                                    <span>ORIGIN</span>

                                    <b aria-hidden="true">→</b>

                                    <span>DESTINATION</span>

                                </div>

                                <p class="intake-route-destination">
                                    {{ $destinationName }}
                                </p>

                            </td>

                            {{-- PICKUP TIME --}}

                            <td>

                                <strong>
                                    {{
                                        $order->picked_up_at?->diffForHumans()
                                        ?: 'In transit'
                                    }}
                                </strong>

                                @if($order->picked_up_at)

                                    <p>
                                        {{
                                            $order->picked_up_at
                                                ->timezone('Asia/Manila')
                                                ->format('M d, Y · g:i A')
                                        }}
                                    </p>

                                @endif

                            </td>

                            {{-- HUB INTAKE --}}

                            <td>

                                @if($isWaiting)

                                    <span class="intake-badge intake-badge-waiting">
                                        Awaiting Intake
                                    </span>

                                    <form
                                        method="POST"
                                        action="{{ route('logistics.incoming-parcels.receive', $order) }}"
                                        class="intake-receive-form"
                                        data-intake-receive-form
                                        data-order-number="{{ $order->order_number }}"
                                    >

                                        @csrf

                                        <label>

                                            <span class="sr-only">
                                                Optional intake note
                                            </span>

                                            <textarea
                                                name="notes"
                                                rows="2"
                                                maxlength="1000"
                                                placeholder="Optional intake note…"
                                            ></textarea>

                                        </label>

                                        <button
                                            type="submit"
                                            class="intake-button intake-button-primary"
                                        >
                                            Receive at Origin Hub
                                        </button>

                                    </form>

                                @elseif($isReceived)

                                    <span class="intake-badge intake-badge-received">
                                        Received
                                    </span>

                                    <div class="intake-received-info">

                                        <strong>
                                            Hub intake confirmed
                                        </strong>

                                        <p>
                                            {{
                                                $parcel->received_at
                                                    ?->timezone('Asia/Manila')
                                                    ->format('M d, Y · g:i A')
                                                ?: 'Received'
                                            }}
                                        </p>

                                        @if($parcel->notes)

                                            <p class="intake-note">
                                                {{ $parcel->notes }}
                                            </p>

                                        @endif

                                    </div>

                                    <a
                                        href="{{ route('logistics.parcel-sorting') }}"
                                        class="intake-text-link"
                                    >
                                        Continue to sorting →
                                    </a>

                                @else

                                    <span class="intake-badge intake-badge-active">
                                        {{
                                            strtoupper(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $parcel?->status ?: 'IN PROCESS'
                                                )
                                            )
                                        }}
                                    </span>

                                    <p class="intake-status-note">
                                        This parcel has already moved beyond
                                        origin hub intake.
                                    </p>

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        {{-- NO INCOMING PARCELS --}}

        @if($orders->isEmpty())

            <div class="intake-empty">

                <span
                    class="intake-empty-icon"
                    aria-hidden="true"
                >
                    ✓
                </span>

                <h3>
                    No parcels waiting for origin hub intake
                </h3>

                <p>
                    Parcels will appear here after a Pickup Rider
                    collects them from a Seller.
                </p>

                <a
                    href="{{ route('logistics.delivery-assignment') }}"
                    class="intake-button intake-button-primary"
                >
                    View Pickup Assignment
                </a>

            </div>

        @endif

        {{-- SEARCH EMPTY STATE --}}

        <div
            class="intake-empty"
            data-intake-empty
            hidden
        >

            <h3>
                No matching incoming parcels
            </h3>

            <p>
                Try another order number, Seller, Rider,
                origin hub, or status.
            </p>

            <button
                type="button"
                class="intake-text-link"
                data-intake-clear
            >
                Clear filters
            </button>

        </div>

        {{-- FOOTER --}}

        <footer class="intake-list-footer">

            <span
                data-intake-visible-count
                aria-live="polite"
            >
                {{ $orders->count() }} parcels shown
            </span>

            <span>
                Intake times shown in Philippine time
            </span>

        </footer>

    </section>

</div>

@endsection

@push('scripts')

<script>
(() => {

    'use strict';

    function initializeIncomingParcels() {

        document.querySelectorAll(
            '[data-intake-workspace]'
        ).forEach(root => {

            if (root.dataset.initialized) {
                return;
            }

            root.dataset.initialized = 'true';

            /*
            |--------------------------------------------------------------------------
            | ELEMENTS
            |--------------------------------------------------------------------------
            */

            const rows = [
                ...root.querySelectorAll('[data-intake-row]')
            ];

            const search = root.querySelector(
                '[data-intake-search]'
            );

            const tabs = [
                ...root.querySelectorAll('[data-intake-filter]')
            ];

            const empty = root.querySelector(
                '[data-intake-empty]'
            );

            const countNode = root.querySelector(
                '[data-intake-visible-count]'
            );

            let filter = 'all';

            const normalize = value =>
                String(value || '').toLocaleLowerCase().trim();

            /*
            |--------------------------------------------------------------------------
            | SEARCH AND FILTERING
            |--------------------------------------------------------------------------
            */

            function applyFilters() {

                const keyword = normalize(search?.value);

                let visible = 0;

                rows.forEach(row => {

                    const stateMatches =
                        filter === 'all'
                        || row.dataset.state === filter;

                    const searchMatches =
                        normalize(row.dataset.search).includes(keyword);

                    row.hidden = !stateMatches || !searchMatches;

                    if (!row.hidden) {
                        visible++;
                    }

                });

                tabs.forEach(tab => {

                    tab.setAttribute(
                        'aria-pressed',
                        String(
                            tab.dataset.intakeFilter === filter
                        )
                    );

                });

                if (empty) {
                    empty.hidden = rows.length === 0 || visible > 0;
                }

                if (countNode) {

                    countNode.textContent =
                        `${visible} of ${rows.length} `
                        + `${rows.length === 1 ? 'parcel' : 'parcels'} shown`;

                }

            }

            /*
            |--------------------------------------------------------------------------
            | STATUS TABS
            |--------------------------------------------------------------------------
            */

            tabs.forEach(tab => {

                tab.addEventListener('click', () => {

                    filter = tab.dataset.intakeFilter || 'all';

                    applyFilters();

                });

            });

            /*
            |--------------------------------------------------------------------------
            | SEARCH
            |--------------------------------------------------------------------------
            */

            search?.addEventListener('input', applyFilters);

            /*
            |--------------------------------------------------------------------------
            | CLEAR FILTERS
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-intake-clear]'
            ).forEach(button => {

                button.addEventListener('click', () => {

                    filter = 'all';

                    if (search) {
                        search.value = '';
                    }

                    applyFilters();

                });

            });

            /*
            |--------------------------------------------------------------------------
            | RECEIVE CONFIRMATION
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-intake-receive-form]'
            ).forEach(form => {

                form.addEventListener('submit', event => {

                    const number =
                        form.dataset.orderNumber || 'this parcel';

                    const confirmed = window.confirm(
                        `Confirm physical receipt of ${number} `
                        + 'at the origin sorting hub?'
                    );

                    if (!confirmed) {

                        event.preventDefault();
                        return;

                    }

                    const button = form.querySelector(
                        'button[type="submit"]'
                    );

                    if (button) {

                        button.disabled = true;
                        button.textContent = 'Receiving parcel…';

                    }

                });

            });

            applyFilters();

        });

    }

    /*
    |--------------------------------------------------------------------------
    | INITIALIZATION
    |--------------------------------------------------------------------------
    */

    if (document.readyState === 'loading') {

        document.addEventListener(
            'DOMContentLoaded',
            initializeIncomingParcels
        );

    } else {

        initializeIncomingParcels();

    }

    /*
    |--------------------------------------------------------------------------
    | RESTORE FROM BROWSER CACHE
    |--------------------------------------------------------------------------
    */

    window.addEventListener('pageshow', event => {

        if (event.persisted) {
            window.location.reload();
        }

    });

})();
</script>

@endpush
