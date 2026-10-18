@extends('layouts.logistics')

@section('title', 'Pickup Requests — SARI Logistics')
@section('page-title', 'Pickup Requests')

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('css/logistics/pickup.css') }}?v=20261015-2"
>

<style>
/*
|--------------------------------------------------------------------------
| SARI LOGISTICS — PICKUP REQUESTS
| Seller-style visual refinement
|--------------------------------------------------------------------------
| This styling only applies to Pickup Requests.
| The shared pickup.css remains unchanged for Pickup Assignment.
*/

#lgPickupRequests {
    --pickup-gold: #d9950b;
    --pickup-gold-dark: #c88708;
    --pickup-ink: #25211c;
    --pickup-line: #e8e2d9;
    --pickup-shadow: 0 6px 18px rgba(33,24,14,.035);

    max-width: 1620px;
    font-size: 10px;
}

/* PAGE HEADING */

#lgPickupRequests .pickup-page-header {
    min-height: 0;
    margin-bottom: 14px;
    padding: 5px 2px 17px;
    overflow: visible;

    border: 0;
    border-bottom: 1px solid #e4e1dc;
    border-radius: 0;

    background: transparent;
    box-shadow: none;
    gap: 16px;
}

#lgPickupRequests .pickup-page-header::before,
#lgPickupRequests .pickup-page-header::after {
    display: none;
}

#lgPickupRequests .pickup-page-header .pickup-eyebrow {
    margin-bottom: 5px;
    color: #a76f11;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
}

#lgPickupRequests .pickup-page-header h1 {
    color: #24201b;
    font-size: clamp(23px,2.15vw,30px);
    font-weight: 750;
    line-height: 1.2;
    letter-spacing: -.045em;
}

#lgPickupRequests .pickup-page-header h1 span {
    color: #d9950b;
}

#lgPickupRequests .pickup-subtitle {
    margin-top: 6px;
    max-width: 720px;
    color: #897f75;
    font-size: 10px;
    line-height: 1.7;
}

#lgPickupRequests .pickup-page-header .pickup-button {
    min-height: 39px;
    padding: 0 16px;
    flex-shrink: 0;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 650;
}

#lgPickupRequests .pickup-page-header .pickup-button-primary {
    background: #d9950b;
    border-color: #d9950b;
    color: white;
}

#lgPickupRequests .pickup-page-header .pickup-button-primary:hover {
    background: #c88708;
    border-color: #c88708;
}

/* KPI CARDS */

#lgPickupRequests .pickup-stats {
    gap: 12px;
}

#lgPickupRequests .pickup-stat {
    min-height: 97px;
    padding: 14px;

    display: flex;
    justify-content: space-between;
    gap: 10px;
    overflow: visible;

    border: 1px solid #e8e2d9;
    border-radius: 16px;
    background: white;

    box-shadow: 0 6px 18px rgba(33,24,14,.035);

    transition:
        transform .18s cubic-bezier(.22,1,.36,1),
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgPickupRequests .pickup-stat::after {
    display: none;
}

#lgPickupRequests .pickup-stat p {
    color: #756b60;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: normal;
    text-transform: none;
}

#lgPickupRequests .pickup-stat strong {
    margin: 7px 0 4px;
    color: #211d18;
    font-size: 25px;
    font-weight: 750;
    line-height: 1.15;
    letter-spacing: -.045em;
}

#lgPickupRequests .pickup-stat span:not(.pickup-stat-icon) {
    color: #8d8378;
    font-size: 9px;
    line-height: 1.45;
}

#lgPickupRequests .pickup-stat-icon {
    display: grid;
    place-items: center;
    flex: 0 0 41px;

    width: 41px;
    height: 41px;

    border-radius: 11px;
    background: #f1f6fd;
    color: #587ead;
}

#lgPickupRequests .pickup-stat-icon svg {
    width: 18px;
    height: 18px;
}

#lgPickupRequests .pickup-stat-amber .pickup-stat-icon {
    background: #fff7e9;
    color: #bc8420;
}

#lgPickupRequests .pickup-stat-green .pickup-stat-icon {
    background: #eef8f2;
    color: #4c8663;
}

#lgPickupRequests .pickup-stat-red .pickup-stat-icon {
    background: #fff1f1;
    color: #bd6d66;
}

/* WORKFLOW STEPPER */

#lgPickupRequests .pickup-flow {
    gap: 4px;
    margin: 14px 0;
    padding: 4px;
    border-radius: 10px;
    box-shadow: none;
}

#lgPickupRequests .pickup-flow :is(a,span) {
    min-height: 32px;
    padding: 5px 10px;
    font-size: 9px;
    border-radius: 7px;
}

#lgPickupRequests .pickup-flow b {
    width: 21px;
    height: 21px;
    flex-basis: 21px;
    font-size: 9px;
}

/* MAIN WORKSPACE */

#lgPickupRequests .pickup-workspace {
    border-radius: 17px;
    border-color: #e8e2d9;
    background: white;
    box-shadow: 0 6px 18px rgba(33,24,14,.035);
}

#lgPickupRequests .pickup-toolbar {
    padding: 15px 17px;
    gap: 14px;
}

#lgPickupRequests .pickup-toolbar h2 {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.035em;
    color: #29231d;
}

#lgPickupRequests .pickup-toolbar p {
    margin-top: 4px;
    color: #93897e;
    font-size: 9px;
    line-height: 1.55;
}

/* SEARCH */

#lgPickupRequests .pickup-search {
    width: min(315px,100%);
}

#lgPickupRequests .pickup-search input {
    height: 39px;
    padding: 0 12px;
    border-color: #dfe2e6;
    border-radius: 10px;
    font-size: 10px;
    background: white;
}

#lgPickupRequests .pickup-search input:focus {
    border-color: #d9950b;
    box-shadow: 0 0 0 3px rgba(217,149,11,.09);
}

/* STATUS FILTERS */

#lgPickupRequests .pickup-filter-row {
    padding: 0 17px 13px;
    gap: 10px;
}

#lgPickupRequests .pickup-tabs {
    gap: 4px;
    padding: 4px;
    border: 1px solid #e6e1d9;
    border-radius: 10px;
    background: #f7f8fa;
}

#lgPickupRequests .pickup-tabs button {
    min-height: 30px;
    padding: 5px 10px;
    font-size: 9px;
    border-radius: 7px;
}

#lgPickupRequests .pickup-tabs button[aria-pressed="true"] {
    border-color: #e9dfce;
    background: white;
    color: #a36c0a;
    box-shadow: 0 2px 5px rgba(35,29,22,.035);
}

#lgPickupRequests .pickup-tabs span {
    min-width: 21px;
    height: 18px;
    font-size: 8px;
}

#lgPickupRequests .pickup-text-link {
    color: #a66d0b;
    font-size: 9px;
    font-weight: 650;
}

/* BULK SELECTION */

#lgPickupRequests .pickup-selection-bar {
    min-height: 49px;
    padding: 9px 17px;
    gap: 11px;
    background: #fbfbfc;
}

#lgPickupRequests .pickup-selection-bar label,
#lgPickupRequests .pickup-selection-bar > span {
    font-size: 9px;
}

#lgPickupRequests .pickup-selection-bar .pickup-button {
    min-height: 35px;
    font-size: 9px;
    padding: 7px 13px;
}

/* REQUEST TABLE */

#lgPickupRequests .pickup-table {
    min-width: 970px;
    font-size: 10px;
    table-layout: fixed;
}

#lgPickupRequests .pickup-table th:nth-child(1) {
    width: 5%;
}

#lgPickupRequests .pickup-table th:nth-child(2) {
    width: 21%;
}

#lgPickupRequests .pickup-table th:nth-child(3) {
    width: 22%;
}

#lgPickupRequests .pickup-table th:nth-child(4) {
    width: 17%;
}

#lgPickupRequests .pickup-table th:nth-child(5) {
    width: 15%;
}

#lgPickupRequests .pickup-table th:nth-child(6) {
    width: 20%;
}

#lgPickupRequests .pickup-table thead {
    background: #f7f8fa;
}

#lgPickupRequests .pickup-table th {
    padding: 12px;
    color: #81776d;
    font-size: 8.5px;
    font-weight: 650;
    letter-spacing: .05em;
}

#lgPickupRequests .pickup-table td {
    min-width: 0;
    padding: 14px 12px;
    vertical-align: top;
    font-size: 10px;
    overflow-wrap: anywhere;
}

#lgPickupRequests .pickup-table td:first-child:has(input[type="checkbox"]) {
    min-width: 0;
    width: auto;
    padding-right: 0;
}

#lgPickupRequests .pickup-table td:last-child {
    min-width: 0;
}

#lgPickupRequests .pickup-table td strong {
    font-size: 10px;
    font-weight: 650;
    color: #443c33;
}

#lgPickupRequests .pickup-table td p {
    margin-top: 4px;
    font-size: 9px;
    line-height: 1.6;
    color: #91887e;
}

#lgPickupRequests .pickup-table td .pickup-address {
    min-width: 0;
    max-width: none;
    font-size: 9px;
}

#lgPickupRequests .pickup-table tbody tr:hover {
    background: #fffaf2;
}

/* STATUS AND ISSUES */

#lgPickupRequests .pickup-badge {
    min-height: 24px;
    padding: 5px 9px;
    font-size: 8.5px;
    font-weight: 650;
}

#lgPickupRequests .pickup-issues {
    max-width: none;
    font-size: 9px;
    line-height: 1.5;
}

/* PARCEL DETAILS */

#lgPickupRequests .pickup-details {
    margin-top: 8px;
}

#lgPickupRequests .pickup-details summary {
    font-size: 9px;
    font-weight: 600;
}

#lgPickupRequests .pickup-detail-body {
    min-width: 0;
    padding: 11px;
    background: #fbfbfc;
    border-color: #e9e3db;
}

#lgPickupRequests .pickup-detail-grid strong {
    font-size: 9px;
}

#lgPickupRequests .pickup-detail-grid p,
#lgPickupRequests .pickup-products li {
    font-size: 9px;
}

#lgPickupRequests .pickup-label {
    font-size: 8px;
}

/* EMPTY STATES */

#lgPickupRequests .pickup-empty {
    padding: 39px 20px;
}

#lgPickupRequests .pickup-empty-icon {
    width: 43px;
    height: 43px;
    background: #fff7e8;
    border-radius: 12px;
}

#lgPickupRequests .pickup-empty h3 {
    font-size: 11px;
}

#lgPickupRequests .pickup-empty p {
    font-size: 9px;
}

/* FOOTER */

#lgPickupRequests .pickup-list-footer {
    padding: 12px 16px;
    background: white;
    font-size: 9px;
}

/* ALERTS */

#lgPickupRequests .pickup-alert strong {
    font-size: 10px;
}

#lgPickupRequests .pickup-alert p,
#lgPickupRequests .pickup-alert ul {
    font-size: 9px;
}

/* SUBTLE HOVER */

@media (hover:hover) and (pointer:fine) {
    #lgPickupRequests .pickup-stat:hover {
        transform: translateY(-2px);
        border-color: #d8c9b2;
        box-shadow: 0 13px 28px rgba(43,33,21,.06);
    }
}

/* RESPONSIVE */

@media (max-width:900px) {
    #lgPickupRequests .pickup-stats {
        grid-template-columns: repeat(2,minmax(0,1fr));
    }
}

@media (max-width:767px) {
    #lgPickupRequests .pickup-page-header,
    #lgPickupRequests .pickup-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    #lgPickupRequests .pickup-page-header .pickup-button {
        align-self: flex-start;
    }

    #lgPickupRequests .pickup-filter-row {
        flex-direction: column;
        align-items: stretch;
        padding-inline: 14px;
    }

    #lgPickupRequests .pickup-toolbar {
        padding: 14px;
    }

    #lgPickupRequests .pickup-search {
        width: 100%;
    }

    #lgPickupRequests .pickup-tabs {
        width: 100%;
    }

    #lgPickupRequests .pickup-selection-bar {
        padding-inline: 12px;
    }
}

@media (max-width:480px) {
    #lgPickupRequests .pickup-page-header .pickup-button {
        width: 100%;
    }

    #lgPickupRequests .pickup-stat {
        padding: 12px;
    }

    #lgPickupRequests .pickup-stat strong {
        font-size: 22px;
    }

    #lgPickupRequests .pickup-stat-icon {
        width: 34px;
        height: 34px;
        flex-basis: 34px;
    }

    #lgPickupRequests .pickup-flow {
        width: 100%;
    }
}

@media (prefers-reduced-motion:reduce) {
    #lgPickupRequests .pickup-stat:hover {
        transform: none !important;
    }
}
</style>
@endpush

@section('content')

<div
    id="lgPickupRequests"
    class="pickup-page"
    data-pickup-workspace="requests"
    data-old-ids="{{ json_encode(array_map('intval', (array) old('order_ids', []))) }}"
>

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}

    <header class="pickup-page-header">

        <div class="min-w-0">

            <p class="pickup-eyebrow">
                SELLER COLLECTIONS
            </p>

            <h1>
                Pickup <span>Requests</span>
            </h1>

            <p class="pickup-subtitle">
                Review Seller requests, verify pickup details,
                and prepare parcels for Rider assignment.
            </p>

        </div>

        <a
            class="pickup-button pickup-button-primary"
            href="{{ route('logistics.delivery-assignment') }}"
        >
            Pickup Assignment

            <span aria-hidden="true">
                →
            </span>
        </a>

    </header>

    {{-- =====================================================
        FLASH MESSAGES
    ====================================================== --}}

    @if (session('success'))

        <div
            class="pickup-alert pickup-alert-success"
            role="status"
        >
            <strong>
                Queue updated
            </strong>

            <p>
                {{ session('success') }}
            </p>
        </div>

    @endif

    @if ($errors->any())

        <div
            class="pickup-alert pickup-alert-danger"
            role="alert"
        >

            <strong>
                Could not verify requests
            </strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif

    {{-- =====================================================
        PAGE DATA
    ====================================================== --}}

    @php
        $summaryCards = [
            [
                'All requests',
                $stats['total'],
                'Unassigned Seller parcels',
                'neutral',
            ],
            [
                'Awaiting verification',
                $stats['pending'],
                'Review before dispatch',
                'amber',
            ],
            [
                'Ready to assign',
                $stats['verified'],
                $stats['batches'].' Seller pickup batches',
                'green',
            ],
            [
                'Needs attention',
                $stats['attention'],
                'Pickup details or route issues',
                'red',
            ],
        ];

        $filters = [
            'all' => ['All', $stats['total']],
            'pending' => ['Awaiting verification', $stats['pending']],
            'verified' => ['Verified', $stats['verified']],
            'attention' => ['Needs attention', $stats['attention']],
        ];

        $stateLabels = [
            'pending' => 'Awaiting verification',
            'verified' => 'Verified',
            'attention' => 'Needs attention',
        ];
    @endphp

    {{-- =====================================================
        SELLER-STYLE KPI CARDS
    ====================================================== --}}

    <section
        class="pickup-stats"
        aria-label="Pickup request overview"
    >

        @foreach ($summaryCards as [$label, $value, $note, $tone])

            <article class="pickup-stat pickup-stat-{{ $tone }}">

                <div class="min-w-0">

                    <p>
                        {{ $label }}
                    </p>

                    <strong>
                        {{ number_format($value) }}
                    </strong>

                    <span>
                        {{ $note }}
                    </span>

                </div>

                <span
                    class="pickup-stat-icon"
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

                        @case('red')

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v6M12 17h.01"/>
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

    {{-- =====================================================
        PICKUP WORKFLOW
    ====================================================== --}}

    <nav
        class="pickup-flow"
        aria-label="Pickup workflow"
    >

        <span
            class="is-current"
            aria-current="step"
        >
            <b>1</b>
            Review &amp; Verify
        </span>

        <a href="{{ route('logistics.delivery-assignment') }}">
            <b>2</b>
            Assign Pickup Rider
        </a>

        <a href="{{ route('logistics.incoming-parcels') }}">
            <b>3</b>
            Origin Hub Intake
        </a>

    </nav>

    {{-- =====================================================
        PICKUP REQUEST WORKSPACE
    ====================================================== --}}

    <section
        class="pickup-workspace"
        aria-labelledby="requests-title"
    >

        {{-- WORKSPACE HEADER --}}

        <div class="pickup-toolbar">

            <div>

                <p class="pickup-eyebrow">
                    PICKUP QUEUE
                </p>

                <h2 id="requests-title">
                    Seller Pickup Requests
                </h2>

                <p>
                    Oldest requests appear first.
                    Verification does not assign a Rider.
                </p>

            </div>

            <label class="pickup-search">

                <span class="sr-only">
                    Search pickup requests
                </span>

                <input
                    type="search"
                    data-pickup-search
                    placeholder="Search order, Seller, address, or hub…"
                    autocomplete="off"
                >

            </label>

        </div>

        {{-- STATUS FILTERS --}}

        <div class="pickup-filter-row">

            <div
                class="pickup-tabs"
                role="group"
                aria-label="Filter requests"
            >

                @foreach ($filters as $filter => [$label, $count])

                    <button
                        type="button"
                        data-pickup-filter="{{ $filter }}"
                        aria-pressed="{{ $filter === 'all' ? 'true' : 'false' }}"
                    >
                        {{ $label }}

                        <span>
                            {{ $count }}
                        </span>
                    </button>

                @endforeach

            </div>

            <button
                type="button"
                class="pickup-text-link"
                data-pickup-clear
            >
                Clear filters
            </button>

        </div>

        {{-- =================================================
            BULK VERIFICATION FORM
        ================================================== --}}

        <form
            id="pickupVerifyForm"
            method="POST"
            action="{{ route('logistics.pickup-requests.batch-verify') }}"
            data-pickup-verify-form
        >
            @csrf
        </form>

        {{-- BULK ACTIONS --}}

        <div
            class="pickup-selection-bar"
            @if($orders->isEmpty()) hidden @endif
        >

            <label>

                <input
                    type="checkbox"
                    data-pickup-select-visible
                >

                Select visible requests

            </label>

            <span
                data-pickup-selected-count
                aria-live="polite"
            >
                0 selected
            </span>

            <button
                form="pickupVerifyForm"
                type="submit"
                class="pickup-button pickup-button-primary"
                data-pickup-verify-button
                disabled
            >
                Verify selected
            </button>

        </div>

        {{-- =================================================
            REQUEST TABLE
        ================================================== --}}

        <div
            class="pickup-table-scroll"
            @if($orders->isEmpty()) hidden @endif
        >

            <table class="pickup-table">

                <thead>
                    <tr>

                        <th scope="col">
                            <span class="sr-only">
                                Select
                            </span>
                        </th>

                        <th scope="col">
                            Order / Seller
                        </th>

                        <th scope="col">
                            Pickup Location
                        </th>

                        <th scope="col">
                            Origin Hub
                        </th>

                        <th scope="col">
                            Requested
                        </th>

                        <th scope="col">
                            Status / Action
                        </th>

                    </tr>
                </thead>

                <tbody>

                    @foreach ($orders as $order)

                        @php
                            $origin = $order->deliveryRouteStops->firstWhere(
                                'stop_key',
                                'origin_facility'
                            );

                            $sellerName = $order->seller?->store_name
                                ?: $order->pickup_name
                                ?: 'Seller Pickup';

                            $state = $order->getAttribute(
                                'pickup_queue_state'
                            );

                            $issues = $order->getAttribute(
                                'pickup_issues'
                            );

                            $search = implode(' ', [
                                $order->order_number,
                                $sellerName,
                                $order->pickup_address,
                                $order->buyer_name,
                                $origin?->facility?->name,
                                $origin?->name,
                            ]);
                        @endphp

                        <tr
                            data-pickup-item
                            data-state="{{ $state }}"
                            data-search="{{ $search }}"
                            data-order-id="{{ $order->id }}"
                        >

                            {{-- SELECT REQUEST --}}

                            <td>

                                <input
                                    type="checkbox"
                                    form="pickupVerifyForm"
                                    name="order_ids[]"
                                    value="{{ $order->id }}"
                                    data-pickup-select
                                    @disabled($state !== 'pending')
                                    aria-label="Select {{ $order->order_number }}"
                                >

                            </td>

                            {{-- ORDER AND SELLER --}}

                            <td>

                                <strong>
                                    {{ $order->order_number }}
                                </strong>

                                <p>
                                    {{ $sellerName }}
                                </p>

                                <details class="pickup-details">

                                    <summary>

                                        View parcel details

                                        <span aria-hidden="true">
                                            +
                                        </span>

                                    </summary>

                                    <div class="pickup-detail-body">

                                        <div class="pickup-detail-grid">

                                            {{-- BUYER --}}

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

                                            {{-- PAYMENT --}}

                                            <div>

                                                <span class="pickup-label">
                                                    Payment
                                                </span>

                                                <strong>
                                                    {{ $order->payment_method }}
                                                    ·
                                                    {{ ucfirst($order->payment_status) }}
                                                </strong>

                                                <p>
                                                    Order value
                                                    ₱{{ number_format((float) $order->total, 2) }}
                                                </p>

                                            </div>

                                            {{-- SELLER CONTACT --}}

                                            <div>

                                                <span class="pickup-label">
                                                    Seller Contact
                                                </span>

                                                <strong>

                                                    {{
                                                        $order->seller?->store_phone
                                                        ?: $order->seller?->contact_no
                                                        ?: 'Not provided'
                                                    }}

                                                </strong>

                                                <p>

                                                    {{
                                                        $order->seller?->pickup_instructions
                                                        ?: 'No pickup instructions provided.'
                                                    }}

                                                </p>

                                            </div>

                                        </div>

                                        {{-- PRODUCTS --}}

                                        <ul class="pickup-products">

                                            @forelse (($order->items ?? []) as $item)

                                                <li>

                                                    <span>

                                                        {{ $item['name'] ?? 'Product' }}

                                                        @if (!empty($item['variant_label']))
                                                            · {{ $item['variant_label'] }}
                                                        @endif

                                                    </span>

                                                    <strong>

                                                        × {{
                                                            $item['qty']
                                                            ?? $item['quantity']
                                                            ?? 1
                                                        }}

                                                    </strong>

                                                </li>

                                            @empty

                                                <li>
                                                    Product details are not
                                                    available for this parcel.
                                                </li>

                                            @endforelse

                                        </ul>

                                        {{-- ROUTE DETAILS --}}

                                        @if (
                                            ! \App\Support\CurrentLogisticsHub::isHubOperator(request()) &&
                                            ($order->logisticsParcel || $order->status === 'ready_for_pickup')
                                        )

                                            <a
                                                class="pickup-text-link"
                                                href="{{ route('logistics.delivery-routes.edit', $order) }}"
                                            >
                                                Review delivery route →
                                            </a>

                                        @endif

                                    </div>

                                </details>

                            </td>

                            {{-- PICKUP LOCATION --}}

                            <td>

                                <strong>
                                    {{ $order->pickup_name ?: $sellerName }}
                                </strong>

                                <p class="pickup-address">

                                    {{
                                        $order->pickup_address
                                        ?: 'Pickup address missing'
                                    }}

                                </p>

                            </td>

                            {{-- ORIGIN HUB --}}

                            <td>

                                <strong>

                                    {{
                                        $origin?->facility?->name
                                        ?: 'Not routed'
                                    }}

                                </strong>

                                <p>

                                    {{
                                        $origin?->facility?->municipality_name
                                        ?: 'Origin sorting facility'
                                    }}

                                </p>

                            </td>

                            {{-- REQUESTED DATE --}}

                            <td>

                                <strong>

                                    {{
                                        $order->ready_at?->diffForHumans()
                                        ?: 'Not recorded'
                                    }}

                                </strong>

                                <p>

                                    {{
                                        $order->ready_at
                                            ?->timezone('Asia/Manila')
                                            ->format('M d, Y · g:i A')
                                    }}

                                </p>

                            </td>

                            {{-- STATUS AND ACTION --}}

                            <td>

                                <span
                                    class="pickup-badge pickup-badge-{{ $state }}"
                                >
                                    {{ $stateLabels[$state] }}
                                </span>

                                @if ($state === 'pending')

                                    <form
                                        method="POST"
                                        action="{{ route('logistics.pickup-requests.verify', $order) }}"
                                        data-pickup-single-verify
                                    >

                                        @csrf

                                        <button
                                            class="pickup-text-link"
                                            type="submit"
                                        >
                                            Verify request →
                                        </button>

                                    </form>

                                @elseif ($state === 'verified')

                                    <a
                                        class="pickup-text-link"
                                        href="{{ route('logistics.delivery-assignment', ['order' => $order->id]) }}"
                                    >
                                        Assign Pickup Rider →
                                    </a>

                                @else

                                    <ul class="pickup-issues">

                                        @foreach ($issues as $issue)

                                            <li>
                                                {{ $issue }}
                                            </li>

                                        @endforeach

                                    </ul>

                                    @if(! \App\Support\CurrentLogisticsHub::isHubOperator(request()))
                                    <a
                                        class="pickup-text-link"
                                        href="{{ route('logistics.delivery-routes.edit', $order) }}"
                                    >
                                        Review route →
                                    </a>
                                    @else
                                        <span class="pickup-subtext">Ask Main Logistics to review the route.</span>
                                    @endif

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        {{-- =================================================
            NO REQUESTS
        ================================================== --}}

        @if ($orders->isEmpty())

            <div class="pickup-empty">

                <span
                    class="pickup-empty-icon"
                    aria-hidden="true"
                >
                    ✓
                </span>

                <h3>
                    No pickup requests waiting
                </h3>

                <p>
                    Requests appear here when Sellers mark their orders
                    ready for pickup and they are routed to your
                    Logistics network.
                </p>

            </div>

        @endif

        {{-- =================================================
            SEARCH EMPTY STATE
        ================================================== --}}

        <div
            class="pickup-empty"
            data-pickup-search-empty
            hidden
        >

            <h3>
                No matching requests
            </h3>

            <p>
                Try another search or clear the status filter.
            </p>

            <button
                type="button"
                class="pickup-text-link"
                data-pickup-clear
            >
                Clear filters
            </button>

        </div>

        {{-- =================================================
            FOOTER
        ================================================== --}}

        <footer class="pickup-list-footer">

            <span
                data-pickup-visible-count
                aria-live="polite"
            >
                {{ $orders->count() }} requests shown
            </span>

            <span>
                Request times shown in Philippine time
            </span>

        </footer>

    </section>

</div>

@endsection

@push('scripts')

<script>
(() => {

    'use strict';

    /*
    |--------------------------------------------------------------------------
    | INITIALIZATION
    |--------------------------------------------------------------------------
    */

    function initialize() {

        document.querySelectorAll(
            '[data-pickup-workspace="requests"]'
        ).forEach((root) => {

            if (root.dataset.initialized) {
                return;
            }

            root.dataset.initialized = 'true';

            /*
            |--------------------------------------------------------------------------
            | REFERENCES
            |--------------------------------------------------------------------------
            */

            const items = [
                ...root.querySelectorAll('[data-pickup-item]')
            ];

            const search = root.querySelector(
                '[data-pickup-search]'
            );

            const empty = root.querySelector(
                '[data-pickup-search-empty]'
            );

            const tabs = [
                ...root.querySelectorAll('[data-pickup-filter]')
            ];

            const selectVisible = root.querySelector(
                '[data-pickup-select-visible]'
            );

            const verifyButton = root.querySelector(
                '[data-pickup-verify-button]'
            );

            const verifyForm = root.querySelector(
                '[data-pickup-verify-form]'
            );

            let filter = 'all';

            /*
            |--------------------------------------------------------------------------
            | NORMALIZATION
            |--------------------------------------------------------------------------
            */

            const normalize = (value) => {

                return String(value || '')
                    .toLocaleLowerCase()
                    .trim();

            };

            /*
            |--------------------------------------------------------------------------
            | SELECTED REQUESTS
            |--------------------------------------------------------------------------
            */

            const selectedRequests = () => {

                return [
                    ...root.querySelectorAll('[data-pickup-select]')
                ].filter((input) => {

                    return input.checked && !input.disabled;

                });

            };

            /*
            |--------------------------------------------------------------------------
            | UPDATE BULK SELECTION
            |--------------------------------------------------------------------------
            */

            function updateSelection() {

                const eligible = [
                    ...root.querySelectorAll('[data-pickup-select]')
                ].filter((input) => {

                    return !input.disabled
                        && !input.closest('[data-pickup-item]').hidden;

                });

                const count = selectedRequests().length;

                root.querySelector(
                    '[data-pickup-selected-count]'
                ).textContent = `${count} selected`;

                verifyButton.disabled = count === 0;

                selectVisible.disabled = eligible.length === 0;

                selectVisible.checked = eligible.length > 0
                    && eligible.every((input) => input.checked);

                selectVisible.indeterminate = eligible.some(
                    (input) => input.checked
                ) && !selectVisible.checked;

            }

            /*
            |--------------------------------------------------------------------------
            | APPLY SEARCH + STATUS FILTERS
            |--------------------------------------------------------------------------
            */

            function applyFilters() {

                const keyword = normalize(search?.value);

                let visible = 0;

                items.forEach((item) => {

                    const matchesState = filter === 'all'
                        || item.dataset.state === filter;

                    const matchesSearch = normalize(
                        item.dataset.search
                    ).includes(keyword);

                    item.hidden = !matchesState || !matchesSearch;

                    if (item.hidden) {

                        item.querySelector(
                            '[data-pickup-select]'
                        ).checked = false;

                    } else {

                        visible++;

                    }

                });

                if (empty) {

                    empty.hidden = items.length === 0 || visible > 0;

                }

                root.querySelector(
                    '[data-pickup-visible-count]'
                ).textContent = `${visible} of ${items.length} requests shown`;

                tabs.forEach((tab) => {

                    tab.setAttribute(
                        'aria-pressed',
                        String(tab.dataset.pickupFilter === filter)
                    );

                });

                updateSelection();

            }

            /*
            |--------------------------------------------------------------------------
            | STATUS FILTER BUTTONS
            |--------------------------------------------------------------------------
            */

            tabs.forEach((tab) => {

                tab.addEventListener('click', () => {

                    filter = tab.dataset.pickupFilter;

                    applyFilters();

                });

            });

            /*
            |--------------------------------------------------------------------------
            | LIVE SEARCH
            |--------------------------------------------------------------------------
            */

            search?.addEventListener(
                'input',
                applyFilters
            );

            /*
            |--------------------------------------------------------------------------
            | RESET FILTERS
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-pickup-clear]'
            ).forEach((button) => {

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
            | RESTORE SELECTED ORDER IDS AFTER VALIDATION ERROR
            |--------------------------------------------------------------------------
            */

            const oldIds = new Set(
                JSON.parse(root.dataset.oldIds || '[]').map(String)
            );

            root.querySelectorAll(
                '[data-pickup-select]'
            ).forEach((input) => {

                input.checked = !input.disabled
                    && oldIds.has(input.value);

                input.addEventListener(
                    'change',
                    updateSelection
                );

            });

            /*
            |--------------------------------------------------------------------------
            | SELECT VISIBLE PENDING REQUESTS
            |--------------------------------------------------------------------------
            */

            selectVisible.addEventListener('change', (event) => {

                const checked = event.target.checked;

                let count = 0;

                root.querySelectorAll(
                    '[data-pickup-select]'
                ).forEach((input) => {

                    const visible = !input.closest(
                        '[data-pickup-item]'
                    ).hidden;

                    if (!input.disabled && visible) {

                        input.checked = checked && count < 200;

                        if (input.checked) {
                            count++;
                        }

                    }

                });

                updateSelection();

            });

            /*
            |--------------------------------------------------------------------------
            | BULK VERIFICATION
            |--------------------------------------------------------------------------
            */

            verifyForm.addEventListener('submit', (event) => {

                const count = selectedRequests().length;

                if (!count || count > 200) {

                    event.preventDefault();

                    if (count > 200) {

                        window.alert(
                            'Select up to 200 requests per verification.'
                        );

                    }

                    return;

                }

                const confirmed = window.confirm(
                    `Verify ${count} selected pickup `
                    + `${count === 1 ? 'request' : 'requests'}? `
                    + 'These parcels will become available for '
                    + 'Pickup Rider assignment.'
                );

                if (!confirmed) {

                    event.preventDefault();
                    return;

                }

                verifyButton.disabled = true;

            });

            /*
            |--------------------------------------------------------------------------
            | SINGLE REQUEST VERIFICATION
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-pickup-single-verify]'
            ).forEach((form) => {

                form.addEventListener('submit', (event) => {

                    const confirmed = window.confirm(
                        'Verify this Seller pickup request and make '
                        + 'it available for Rider assignment?'
                    );

                    if (!confirmed) {

                        event.preventDefault();
                        return;

                    }

                    form.querySelector('button').disabled = true;

                });

            });

            /*
            |--------------------------------------------------------------------------
            | INITIAL RENDER
            |--------------------------------------------------------------------------
            */

            applyFilters();

        });

    }

    if (document.readyState === 'loading') {

        document.addEventListener(
            'DOMContentLoaded',
            initialize
        );

    } else {

        initialize();

    }

    /*
    |--------------------------------------------------------------------------
    | RESTORE PAGE AFTER BROWSER BACK/FORWARD CACHE
    |--------------------------------------------------------------------------
    */

    window.addEventListener('pageshow', (event) => {

        if (event.persisted) {

            window.location.reload();

        }

    });

})();
</script>

@endpush