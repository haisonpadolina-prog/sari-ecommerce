
@extends('layouts.logistics')

@section('title', 'Parcel Sorting — SARI Logistics')
@section('page-title', 'Parcel Sorting')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/logistics/parcel-sorting.css') }}?v=20261015-1">

<style>
/*
|--------------------------------------------------------------------------
| SARI LOGISTICS — PARCEL SORTING
| Seller-style visual refinement
|--------------------------------------------------------------------------
| Scoped to this Blade only.
| The original parcel-sorting.css remains unchanged.
*/

#lgParcelSorting {
    --sorting-gold: #d9950b;
    --sorting-line: #e8e2d9;
    --sorting-ink: #25211c;

    width: 100%;
    max-width: 1620px;
    font-size: 10px;
}

#lgParcelSorting [hidden] {
    display: none !important;
}

/* =========================================================
   PAGE HEADING
========================================================= */

#lgParcelSorting .sorting-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;

    min-height: 0;
    margin-bottom: 14px;
    padding: 5px 2px 17px;

    overflow: visible;

    border: 0;
    border-bottom: 1px solid #e4e1dc;
    border-radius: 0;

    background: transparent;
    box-shadow: none;
}

#lgParcelSorting .sorting-page-header::after {
    display: none;
}

#lgParcelSorting .sorting-eyebrow {
    margin-bottom: 5px;
    color: #a76f11;
    font-size: 9px;
    font-weight: 700;
    line-height: 1.4;
    letter-spacing: .14em;
    text-transform: uppercase;
}

#lgParcelSorting .sorting-page-header h1 {
    color: #24201b;
    font-size: clamp(23px, 2.15vw, 30px);
    font-weight: 750;
    line-height: 1.2;
    letter-spacing: -.045em;
}

#lgParcelSorting .sorting-page-header h1 span {
    color: #d9950b;
}

#lgParcelSorting .sorting-subtitle {
    max-width: 720px;
    margin-top: 6px;
    color: #897f75;
    font-size: 10px;
    line-height: 1.7;
}

/* =========================================================
   HEADER ACTIONS
========================================================= */

#lgParcelSorting .sorting-header-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

#lgParcelSorting .sorting-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;

    min-height: 39px;
    padding: 9px 14px;

    border: 1px solid #e4d8c6;
    border-radius: 10px;

    background: #fff;
    color: #776a5a;

    font-size: 10px;
    font-weight: 650;
    line-height: 1.4;

    box-shadow: none;

    transition:
        transform .16s ease,
        border-color .16s ease,
        background .16s ease,
        color .16s ease;
}

#lgParcelSorting .sorting-page-header
.sorting-button:not(.sorting-button-primary) {
    border: 1px solid #e4d8c6;
    background: #fff;
    color: #776a5a;
}

#lgParcelSorting .sorting-page-header
.sorting-button:not(.sorting-button-primary):hover {
    border-color: #d5bb87;
    background: #fff9ed;
    color: #a36c0a;
}

#lgParcelSorting .sorting-button-primary {
    border-color: #d9950b;
    background: #d9950b;
    color: #fff;
}

#lgParcelSorting .sorting-button-primary:hover {
    border-color: #c88708;
    background: #c88708;
    color: #fff;
}

/* =========================================================
   ALERTS
========================================================= */

#lgParcelSorting .sorting-alert {
    margin-bottom: 14px;
    border-radius: 12px;
}

#lgParcelSorting .sorting-alert strong {
    font-size: 10px;
    font-weight: 700;
}

#lgParcelSorting .sorting-alert p,
#lgParcelSorting .sorting-alert ul {
    font-size: 9px;
    line-height: 1.6;
}

/* =========================================================
   KPI CARDS
========================================================= */

#lgParcelSorting .sorting-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}

#lgParcelSorting .sorting-stat {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;

    min-width: 0;
    min-height: 98px;
    padding: 14px;

    border: 1px solid #e8e2d9;
    border-radius: 16px;
    background: #fff;

    box-shadow: 0 6px 18px rgba(33, 24, 14, .035);

    transition:
        transform .18s cubic-bezier(.22, 1, .36, 1),
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgParcelSorting .sorting-stat::after {
    display: none;
}

#lgParcelSorting .sorting-stat p {
    color: #756b60;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: normal;
    text-transform: none;
}

#lgParcelSorting .sorting-stat strong,
#lgParcelSorting .sorting-stat-amber strong,
#lgParcelSorting .sorting-stat-green strong,
#lgParcelSorting .sorting-stat-blue strong {
    display: block;
    margin: 8px 0 5px;
    color: #211d18;
    font-size: 25px;
    font-weight: 750;
    line-height: 1.1;
    letter-spacing: -.045em;
}

#lgParcelSorting .sorting-stat span:not(.sorting-stat-icon) {
    color: #8d8378;
    font-size: 9px;
    line-height: 1.5;
}

#lgParcelSorting .sorting-stat-icon {
    display: grid;
    place-items: center;
    flex: 0 0 41px;

    width: 41px;
    height: 41px;

    border-radius: 11px;
    background: #f1f6fd;
    color: #587ead;
}

#lgParcelSorting .sorting-stat-icon svg {
    width: 18px;
    height: 18px;
}

#lgParcelSorting .sorting-stat-amber .sorting-stat-icon {
    background: #fff7e9;
    color: #bc8420;
}

#lgParcelSorting .sorting-stat-green .sorting-stat-icon {
    background: #eef8f2;
    color: #4c8663;
}

#lgParcelSorting .sorting-stat-blue .sorting-stat-icon {
    background: #eff5fa;
    color: #587ead;
}

/* =========================================================
   WORKFLOW STEPPER
========================================================= */

#lgParcelSorting .sorting-flow {
    gap: 4px;
    margin: 14px 0;
    padding: 4px;

    border: 1px solid #e8e2d9;
    border-radius: 10px;

    background: #fff;
}

#lgParcelSorting .sorting-flow :is(a, span) {
    min-height: 32px;
    padding: 5px 10px;
    border-radius: 7px;
    font-size: 9px;
}

#lgParcelSorting .sorting-flow b {
    display: grid;
    place-items: center;
    flex: 0 0 21px;

    width: 21px;
    height: 21px;
    font-size: 9px;
}

/* =========================================================
   WORKSPACE
========================================================= */

#lgParcelSorting .sorting-workspace {
    min-width: 0;
    overflow: hidden;

    border: 1px solid #e8e2d9;
    border-radius: 17px;

    background: #fff;
    box-shadow: 0 6px 18px rgba(33, 24, 14, .035);
}

#lgParcelSorting .sorting-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    padding: 15px 17px;
}

#lgParcelSorting .sorting-toolbar h2 {
    margin: 4px 0 0;
    color: #29231d;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.035em;
}

#lgParcelSorting .sorting-toolbar p:not(.sorting-eyebrow) {
    margin-top: 4px;
    color: #93897e;
    font-size: 9px;
    line-height: 1.55;
}

/* =========================================================
   SEARCH
========================================================= */

#lgParcelSorting .sorting-search {
    flex-shrink: 0;
    width: min(315px, 100%);
}

#lgParcelSorting .sorting-search input {
    width: 100%;
    height: 39px;
    padding: 0 12px;

    border: 1px solid #dfe2e6;
    border-radius: 10px;

    background: #fff;
    color: #50483e;
    font-size: 10px;

    transition:
        border-color .15s ease,
        box-shadow .15s ease;
}

#lgParcelSorting .sorting-search input:focus,
#lgParcelSorting .sorting-form input:focus,
#lgParcelSorting .sorting-form textarea:focus {
    border-color: #d9950b;
    box-shadow: 0 0 0 3px rgba(217, 149, 11, .09);
}

/* =========================================================
   FILTERS
========================================================= */

#lgParcelSorting .sorting-filter-row {
    gap: 10px;
    padding: 0 17px 13px;
}

#lgParcelSorting .sorting-tabs {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
    padding: 4px;

    border: 1px solid #e6e1d9;
    border-radius: 10px;

    background: #f7f8fa;
}

#lgParcelSorting .sorting-tabs button {
    min-height: 30px;
    padding: 5px 10px;

    border: 1px solid transparent;
    border-radius: 7px;
    background: transparent;

    color: #756d63;
    font-size: 9px;
    font-weight: 550;
}

#lgParcelSorting .sorting-tabs button:hover {
    background: #fff9ee;
    color: #a36c0a;
}

#lgParcelSorting .sorting-tabs button[aria-pressed="true"] {
    border-color: #e9dfce;
    background: #fff;
    color: #a36c0a;
    font-weight: 700;
    box-shadow: 0 2px 5px rgba(35, 29, 22, .035);
}

#lgParcelSorting .sorting-tabs span {
    min-width: 21px;
    height: 19px;
    border-radius: 6px;
    font-size: 8px;
}

#lgParcelSorting .sorting-text-link {
    color: #a66d0b;
    font-size: 9px;
    font-weight: 650;
    line-height: 1.5;
}

/* =========================================================
   TABLE
========================================================= */

#lgParcelSorting .sorting-table-scroll {
    width: 100%;
    overflow-x: auto;
}

#lgParcelSorting .sorting-table {
    width: 100%;
    min-width: 1000px;
    table-layout: fixed;
    border-collapse: collapse;
    text-align: left;
    font-size: 10px;
}

#lgParcelSorting .sorting-table th:nth-child(1) {
    width: 20%;
}

#lgParcelSorting .sorting-table th:nth-child(2) {
    width: 17%;
}

#lgParcelSorting .sorting-table th:nth-child(3) {
    width: 17%;
}

#lgParcelSorting .sorting-table th:nth-child(4) {
    width: 12%;
}

#lgParcelSorting .sorting-table th:nth-child(5) {
    width: 14%;
}

#lgParcelSorting .sorting-table th:nth-child(6) {
    width: 20%;
}

#lgParcelSorting .sorting-table thead {
    background: #f7f8fa;
}

#lgParcelSorting .sorting-table th {
    padding: 12px 11px;
    color: #81776d;
    font-size: 8.5px;
    font-weight: 650;
    letter-spacing: .05em;
    text-transform: uppercase;
    white-space: nowrap;
}

#lgParcelSorting .sorting-table td {
    min-width: 0;
    padding: 13px 11px;

    border-bottom: 1px solid #f0ebe5;

    color: #61584e;
    font-size: 10px;
    line-height: 1.55;
    vertical-align: top;

    overflow-wrap: anywhere;
}

#lgParcelSorting .sorting-table td strong {
    display: block;
    color: #443c33;
    font-size: 10px;
    font-weight: 650;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgParcelSorting .sorting-table td p {
    margin-top: 4px;
    color: #91887e;
    font-size: 9px;
    line-height: 1.55;
    overflow-wrap: anywhere;
}

#lgParcelSorting .sorting-table tbody tr {
    transition: background-color .16s ease;
}

#lgParcelSorting .sorting-table tbody tr:hover {
    background: #fffaf2;
}

/* =========================================================
   HUB DISPLAY
========================================================= */

#lgParcelSorting .sorting-hub {
    display: flex;
    align-items: flex-start;
    min-width: 0;
    gap: 8px;
}

#lgParcelSorting .sorting-hub > div {
    min-width: 0;
}

#lgParcelSorting .sorting-hub-icon {
    display: grid;
    place-items: center;
    flex: 0 0 32px;
    width: 32px;
    height: 32px;
    border-radius: 9px;
    font-size: 9px;
    font-weight: 700;
}

#lgParcelSorting .sorting-hub-origin {
    background: #fff5df;
    color: #ad781b;
}

#lgParcelSorting .sorting-hub-destination {
    background: #f1f6fb;
    color: #587b99;
}

/* =========================================================
   ZONE DISPLAY
========================================================= */

#lgParcelSorting .sorting-zone-display {
    min-width: 0;
}

#lgParcelSorting .sorting-zone-code {
    display: inline-flex;
    max-width: 100%;
    min-height: 27px;
    padding: 6px 9px;

    border: 1px solid #d9e5dc;
    border-radius: 8px;

    background: #f2f8f4;
    color: #4f795a;

    font-size: 9px;
    font-weight: 700;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgParcelSorting .sorting-zone-unassigned {
    padding: 6px 9px;
    border: 1px solid #e8e2db;
    border-radius: 8px;
    background: #f6f5f3;
    color: #8b8175;
    font-size: 9px;
}

/* =========================================================
   STATUS BADGES
========================================================= */

#lgParcelSorting .sorting-badge {
    min-height: 24px;
    padding: 5px 9px;
    border-radius: 999px;
    font-size: 8.5px;
    font-weight: 650;
}

/* =========================================================
   UNIFIED ACTION AREA
========================================================= */

#lgParcelSorting .ps-action-panel {
    width: 100%;
    min-width: 0;
    margin-top: 9px;
    padding: 10px;

    border: 1px solid #e9e5df;
    border-radius: 10px;

    background: #fbfbfc;
}

/* SORTING FORM */

#lgParcelSorting .sorting-form {
    width: 100%;
    max-width: none;
    margin: 0;
}

#lgParcelSorting .sorting-form label {
    display: block;
}

#lgParcelSorting .sorting-form label + label {
    margin-top: 9px;
}

#lgParcelSorting .sorting-label {
    display: block;
    margin-bottom: 5px;
    color: #958879;
    font-size: 8px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
}

#lgParcelSorting .sorting-form input,
#lgParcelSorting .sorting-form textarea {
    display: block;
    width: 100%;
    padding: 9px 10px;

    border: 1px solid #dfe2e6;
    border-radius: 9px;

    background: #fff;
    color: #50483e;
    font-size: 9px;
    line-height: 1.5;
}

#lgParcelSorting .sorting-form input {
    height: 37px;
}

#lgParcelSorting .sorting-form textarea {
    min-height: 65px;
    resize: vertical;
}

#lgParcelSorting .sorting-form .sorting-button {
    width: 100%;
    min-height: 37px;
    margin-top: 10px;
    padding: 8px;
    border-radius: 9px;
    font-size: 9px;
    font-weight: 700;
    white-space: normal;
    line-height: 1.5;
}

/* SORTED INFORMATION */

#lgParcelSorting .sorting-completed {
    width: 100%;
    max-width: none;
    margin: 0;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
}

#lgParcelSorting .sorting-completed strong {
    color: #51483e !important;
    font-size: 9px !important;
    font-weight: 700 !important;
}

#lgParcelSorting .sorting-completed p {
    color: #897f74 !important;
    font-size: 9px !important;
    line-height: 1.6;
}

#lgParcelSorting .sorting-completed .sorting-note {
    border-top-color: #e8e2d9;
    color: #82776a !important;
}

#lgParcelSorting .ps-action-panel > .sorting-text-link {
    display: inline-flex;
    margin-top: 10px;
}

/* =========================================================
   EXPANDABLE PARCEL DETAILS
========================================================= */

#lgParcelSorting .sorting-details {
    max-width: 100%;
    margin-top: 9px;
}

#lgParcelSorting .sorting-details summary {
    max-width: 100%;
    gap: 7px;
    color: #a47118;
    font-size: 9px;
    font-weight: 650;
}

#lgParcelSorting .sorting-detail-body {
    width: 100%;
    min-width: 0;
    margin-top: 8px;
    padding: 10px;

    border: 1px solid #e8e2d9;
    border-radius: 9px;

    background: #fff;
}

#lgParcelSorting .sorting-detail-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
}

#lgParcelSorting .sorting-detail-grid strong {
    font-size: 9px !important;
}

#lgParcelSorting .sorting-detail-grid p {
    font-size: 9px !important;
}

#lgParcelSorting .sorting-existing-note {
    border-top-color: #e8e2d9;
}

#lgParcelSorting .sorting-existing-note p {
    font-size: 9px !important;
    line-height: 1.6;
}

#lgParcelSorting .sorting-products {
    border-top-color: #e8e2d9;
}

#lgParcelSorting .sorting-products li {
    gap: 8px;
    color: #6f665d;
    font-size: 9px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

/* =========================================================
   EMPTY STATES
========================================================= */

#lgParcelSorting .sorting-empty {
    padding: 40px 20px;
}

#lgParcelSorting .sorting-empty-icon {
    width: 43px;
    height: 43px;
    border-radius: 12px;
    background: #fff7e8;
}

#lgParcelSorting .sorting-empty h3 {
    font-size: 11px;
    font-weight: 700;
}

#lgParcelSorting .sorting-empty p {
    font-size: 9px;
    line-height: 1.6;
}

#lgParcelSorting .sorting-list-footer {
    padding: 12px 16px;
    background: #fff;
    font-size: 9px;
}

/* =========================================================
   HOVER / RESPONSIVE
========================================================= */

@media (hover: hover) and (pointer: fine) {
    #lgParcelSorting .sorting-stat:hover {
        transform: translateY(-2px);
        border-color: #d8c9b2;
        box-shadow: 0 13px 28px rgba(43, 33, 21, .06);
    }
}

@media (max-width: 900px) {
    #lgParcelSorting .sorting-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767px) {
    #lgParcelSorting .sorting-page-header,
    #lgParcelSorting .sorting-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    #lgParcelSorting .sorting-search {
        width: 100%;
    }

    #lgParcelSorting .sorting-header-actions {
        width: 100%;
    }

    #lgParcelSorting .sorting-header-actions .sorting-button {
        flex: 1;
    }

    #lgParcelSorting .sorting-filter-row {
        flex-direction: column;
        align-items: stretch;
        padding: 0 14px 13px;
    }

    #lgParcelSorting .sorting-flow {
        max-width: 100%;
        overflow-x: auto;
    }

    #lgParcelSorting .sorting-flow :is(a, span) {
        flex-shrink: 0;
        font-size: 9px;
    }
}

@media (max-width: 520px) {
    #lgParcelSorting .sorting-header-actions {
        flex-direction: column;
    }

    #lgParcelSorting .sorting-header-actions .sorting-button {
        width: 100%;
    }

    #lgParcelSorting .sorting-stat {
        padding: 12px;
    }

    #lgParcelSorting .sorting-stat strong {
        font-size: 22px;
    }

    #lgParcelSorting .sorting-stat-icon {
        flex-basis: 34px;
        width: 34px;
        height: 34px;
    }
}

@media (prefers-reduced-motion: reduce) {
    #lgParcelSorting .sorting-stat:hover {
        transform: none !important;
    }
}
</style>
@endpush

@section('content')

<div
    id="lgParcelSorting"
    class="sorting-page"
    data-sorting-workspace
>

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}

    <header class="sorting-page-header">

        <div class="min-w-0">

            <p class="sorting-eyebrow">
                ORIGIN HUB OPERATIONS
            </p>

            <h1>
                Parcel <span>Sorting</span>
            </h1>

            <p class="sorting-subtitle">
                Organize received parcels into sorting zones or racks,
                confirm their destination route, and clear them for
                hub-to-hub movement.
            </p>

        </div>

        <div class="sorting-header-actions">

            <a
                href="{{ route('logistics.incoming-parcels') }}"
                class="sorting-button"
            >
                <span aria-hidden="true">←</span>
                Incoming Parcels
            </a>

            <a
                href="{{ route('logistics.hub-transfers') }}"
                class="sorting-button sorting-button-primary"
            >
                Hub Transfers
                <span aria-hidden="true">→</span>
            </a>

        </div>

    </header>

    {{-- =====================================================
        FLASH MESSAGES
    ====================================================== --}}

    @if(session('success'))

        <div
            class="sorting-alert sorting-alert-success"
            role="status"
        >
            <strong>
                Sorting queue updated
            </strong>

            <p>
                {{ session('success') }}
            </p>
        </div>

    @endif

    @if($errors->any())

        <div
            class="sorting-alert sorting-alert-danger"
            role="alert"
        >
            <strong>
                Could not sort parcel
            </strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif

    {{-- =====================================================
        SUMMARY DATA
    ====================================================== --}}

    @php
        $receivedCount = $parcels
            ->where('status', 'received')
            ->count();

        $sortedCount = $parcels
            ->where('status', 'sorted')
            ->count();

        $originHubCount = $parcels
            ->map(function ($parcel) {

                return $parcel
                    ->order
                    ?->deliveryRouteStops
                    ?->firstWhere(
                        'stop_key',
                        'origin_facility'
                    )
                    ?->logistics_facility_id;

            })
            ->filter()
            ->unique()
            ->count();

        $destinationHubCount = $parcels
            ->map(function ($parcel) {

                return $parcel
                    ->order
                    ?->deliveryRouteStops
                    ?->firstWhere(
                        'stop_key',
                        'destination_facility'
                    )
                    ?->logistics_facility_id;

            })
            ->filter()
            ->unique()
            ->count();

        $zoneCount = $parcels
            ->where('status', 'sorted')
            ->pluck('sorting_zone')
            ->filter()
            ->map(
                fn ($zone) => mb_strtolower(
                    trim((string) $zone)
                )
            )
            ->unique()
            ->count();

        $summaryCards = [
            [
                'Sorting Queue',
                $parcels->count(),
                'Received and sorted parcels',
                'neutral',
            ],
            [
                'Needs Sorting',
                $receivedCount,
                'Waiting for zone assignment',
                'amber',
            ],
            [
                'Sorted',
                $sortedCount,
                'Ready for transfer planning',
                'green',
            ],
            [
                'Sorting Zones',
                $zoneCount,
                $destinationHubCount . ' destination '
                    . ($destinationHubCount === 1 ? 'hub' : 'hubs'),
                'blue',
            ],
        ];
    @endphp

    {{-- =====================================================
        KPI CARDS
    ====================================================== --}}

    <section
        class="sorting-stats"
        aria-label="Parcel sorting overview"
    >

        @foreach($summaryCards as [$label, $value, $note, $tone])

            <article class="sorting-stat sorting-stat-{{ $tone }}">

                <div class="min-w-0">

                    <p>{{ $label }}</p>

                    <strong>
                        {{ number_format($value) }}
                    </strong>

                    <span>{{ $note }}</span>

                </div>

                <span
                    class="sorting-stat-icon"
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
                                <rect x="3" y="5" width="8" height="7" rx="1"/>
                                <rect x="13" y="5" width="8" height="7" rx="1"/>
                                <rect x="8" y="14" width="8" height="7" rx="1"/>
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
        WORKFLOW STEPPER
    ====================================================== --}}

    <nav
        class="sorting-flow"
        aria-label="Origin hub workflow"
    >

        <a href="{{ route('logistics.pickup-requests') }}">
            <b>1</b>
            Review &amp; Verify
        </a>

        <a href="{{ route('logistics.delivery-assignment') }}">
            <b>2</b>
            Pickup Rider
        </a>

        <a href="{{ route('logistics.incoming-parcels') }}">
            <b>3</b>
            Origin Intake
        </a>

        <span
            class="is-current"
            aria-current="step"
        >
            <b>4</b>
            Parcel Sorting
        </span>

        <a href="{{ route('logistics.hub-transfers') }}">
            <b>5</b>
            Hub Transfer
        </a>

    </nav>

    {{-- =====================================================
        SORTING WORKSPACE
    ====================================================== --}}

    <section
        class="sorting-workspace"
        aria-labelledby="sorting-queue-heading"
    >

        {{-- TOOLBAR --}}

        <header class="sorting-toolbar">

            <div>

                <p class="sorting-eyebrow">
                    SORTING QUEUE
                </p>

                <h2 id="sorting-queue-heading">
                    Origin Hub Sorting Queue
                </h2>

                <p>
                    Assign the physical sorting zone before a parcel
                    can move to the next Logistics leg.
                </p>

            </div>

            <label class="sorting-search">

                <span class="sr-only">
                    Search parcel sorting queue
                </span>

                <input
                    type="search"
                    data-sorting-search
                    autocomplete="off"
                    placeholder="Search order, Seller, hub, or zone…"
                >

            </label>

        </header>

        {{-- =================================================
            FILTERS
        ================================================== --}}

        <div class="sorting-filter-row">

            <div
                class="sorting-tabs"
                role="group"
                aria-label="Filter sorting queue"
            >

                <button
                    type="button"
                    data-sorting-filter="all"
                    aria-pressed="true"
                >
                    All
                    <span>{{ $parcels->count() }}</span>
                </button>

                <button
                    type="button"
                    data-sorting-filter="received"
                    aria-pressed="false"
                >
                    Needs Sorting
                    <span>{{ $receivedCount }}</span>
                </button>

                <button
                    type="button"
                    data-sorting-filter="sorted"
                    aria-pressed="false"
                >
                    Sorted
                    <span>{{ $sortedCount }}</span>
                </button>

            </div>

            <button
                type="button"
                class="sorting-text-link"
                data-sorting-clear
            >
                Clear filters
            </button>

        </div>

        {{-- =================================================
            SORTING TABLE
        ================================================== --}}

        <div
            class="sorting-table-scroll"
            @if($parcels->isEmpty()) hidden @endif
        >

            <table class="sorting-table">

                <thead>
                    <tr>
                        <th scope="col">Order / Seller</th>
                        <th scope="col">Origin Hub</th>
                        <th scope="col">Destination Hub</th>
                        <th scope="col">Received</th>
                        <th scope="col">Sorting Zone</th>
                        <th scope="col">Status / Action</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($parcels as $parcel)

                        @php
                            $order = $parcel->order;

                            $routeStops = $order
                                ?->deliveryRouteStops
                                ?->keyBy('stop_key')
                                ?? collect();

                            $originStop = $routeStops->get(
                                'origin_facility'
                            );

                            $destinationStop = $routeStops->get(
                                'destination_facility'
                            );

                            $originName = $originStop?->facility?->name
                                ?: $originStop?->name
                                ?: 'Origin hub pending';

                            $destinationName = $destinationStop?->facility?->name
                                ?: $destinationStop?->name
                                ?: 'Destination pending';

                            $originMunicipality = $originStop
                                ?->facility
                                ?->municipality_name;

                            $destinationMunicipality = $destinationStop
                                ?->facility
                                ?->municipality_name;

                            $sellerName = $order?->seller?->store_name
                                ?: $order?->pickup_name
                                ?: 'SARI Seller';

                            $state = $parcel->status === 'sorted'
                                ? 'sorted'
                                : 'received';

                            $searchText = implode(' ', array_filter([
                                $order?->order_number,
                                $sellerName,
                                $order?->buyer_name,
                                $order?->pickup_address,
                                $originName,
                                $originMunicipality,
                                $destinationName,
                                $destinationMunicipality,
                                $parcel->sorting_zone,
                                $parcel->status,
                            ]));
                        @endphp

                        <tr
                            data-sorting-row
                            data-state="{{ $state }}"
                            data-search="{{ $searchText }}"
                        >

                            {{-- ORDER / SELLER --}}

                            <td>

                                <strong class="sorting-order-number">
                                    {{ $order?->order_number }}
                                </strong>

                                <p class="sorting-primary-text">
                                    {{ $sellerName }}
                                </p>

                                <p>
                                    Buyer: {{ $order?->buyer_name }}
                                </p>

                                {{-- EXPANDABLE PARCEL DETAILS --}}

                                <details class="sorting-details">

                                    <summary>
                                        Parcel details
                                        <span aria-hidden="true">+</span>
                                    </summary>

                                    <div class="sorting-detail-body">

                                        <div class="sorting-detail-grid">

                                            {{-- SELLER PICKUP --}}

                                            <div>

                                                <span class="sorting-label">
                                                    Seller Pickup
                                                </span>

                                                <strong>
                                                    {{
                                                        $order?->pickup_name
                                                        ?: $sellerName
                                                    }}
                                                </strong>

                                                <p>
                                                    {{
                                                        $order?->pickup_address
                                                        ?: 'No pickup address recorded.'
                                                    }}
                                                </p>

                                            </div>

                                            {{-- BUYER DESTINATION --}}

                                            <div>

                                                <span class="sorting-label">
                                                    Buyer Destination
                                                </span>

                                                <strong>
                                                    {{ $order?->buyer_name }}
                                                </strong>

                                                <p>
                                                    {{ $order?->buyer_address }}
                                                </p>

                                            </div>

                                            {{-- PICKUP RIDER --}}

                                            <div>

                                                <span class="sorting-label">
                                                    Pickup Rider
                                                </span>

                                                <strong>
                                                    {{
                                                        $order?->courier_name
                                                        ?: 'Pickup Rider'
                                                    }}
                                                </strong>

                                                <p>
                                                    {{
                                                        $order?->courier_email
                                                        ?: 'No Rider email recorded'
                                                    }}
                                                </p>

                                            </div>

                                            {{-- ORDER VALUE --}}

                                            <div>

                                                <span class="sorting-label">
                                                    Order Value
                                                </span>

                                                <strong>
                                                    ₱{{ number_format(
                                                        (float) ($order?->total ?? 0),
                                                        2
                                                    ) }}
                                                </strong>

                                                <p>
                                                    Marketplace order total
                                                </p>

                                            </div>

                                        </div>

                                        {{-- INTAKE NOTE --}}

                                        @if(
                                            $parcel->notes
                                            && $parcel->status === 'received'
                                        )

                                            <div class="sorting-existing-note">

                                                <span class="sorting-label">
                                                    Intake Note
                                                </span>

                                                <p>
                                                    {{ $parcel->notes }}
                                                </p>

                                            </div>

                                        @endif

                                        {{-- PRODUCTS --}}

                                        <ul class="sorting-products">

                                            @forelse(($order?->items ?? []) as $item)

                                                <li>

                                                    <span>

                                                        {{
                                                            $item['name']
                                                            ?? 'Product'
                                                        }}

                                                        @if(!empty($item['variant_label']))
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
                                                    <span>
                                                        Product details unavailable.
                                                    </span>
                                                </li>

                                            @endforelse

                                        </ul>

                                    </div>

                                </details>

                            </td>

                            {{-- =================================================
                                ORIGIN HUB
                            ================================================== --}}

                            <td>

                                <div class="sorting-hub">

                                    <span
                                        class="sorting-hub-icon sorting-hub-origin"
                                        aria-hidden="true"
                                    >
                                        O
                                    </span>

                                    <div>

                                        <strong>
                                            {{ $originName }}
                                        </strong>

                                        <p>
                                            {{
                                                $originMunicipality
                                                ?: 'Origin sorting facility'
                                            }}
                                        </p>

                                    </div>

                                </div>

                            </td>

                            {{-- =================================================
                                DESTINATION HUB
                            ================================================== --}}

                            <td>

                                <div class="sorting-hub">

                                    <span
                                        class="sorting-hub-icon sorting-hub-destination"
                                        aria-hidden="true"
                                    >
                                        D
                                    </span>

                                    <div>

                                        <strong>
                                            {{ $destinationName }}
                                        </strong>

                                        <p>
                                            {{
                                                $destinationMunicipality
                                                ?: 'Destination sorting facility'
                                            }}
                                        </p>

                                    </div>

                                </div>

                            </td>

                            {{-- =================================================
                                RECEIVED TIME
                            ================================================== --}}

                            <td>

                                <strong>
                                    {{
                                        $parcel->received_at?->diffForHumans()
                                        ?: 'Not recorded'
                                    }}
                                </strong>

                                @if($parcel->received_at)

                                    <p>
                                        {{
                                            $parcel->received_at
                                                ->timezone('Asia/Manila')
                                                ->format('M d, Y · g:i A')
                                        }}
                                    </p>

                                @endif

                            </td>

                            {{-- =================================================
                                SORTING ZONE
                            ================================================== --}}

                            <td>

                                @if($parcel->status === 'sorted')

                                    <div class="sorting-zone-display">

                                        <span class="sorting-zone-code">
                                            {{
                                                $parcel->sorting_zone
                                                ?: 'Not recorded'
                                            }}
                                        </span>

                                        <p>
                                            Assigned sorting location
                                        </p>

                                    </div>

                                @else

                                    <span class="sorting-zone-unassigned">
                                        Not assigned
                                    </span>

                                @endif

                            </td>

                            {{-- =================================================
                                STATUS / ACTION
                            ================================================== --}}

                            <td>

                                @if($parcel->status === 'received')

                                    <span class="sorting-badge sorting-badge-received">
                                        Needs Sorting
                                    </span>

                                    <div class="ps-action-panel">

                                        <form
                                            method="POST"
                                            action="{{ route('logistics.parcel-sorting.sort', $parcel) }}"
                                            class="sorting-form"
                                            data-sorting-form
                                            data-order-number="{{ $order?->order_number }}"
                                        >

                                            @csrf

                                            {{-- ZONE / RACK --}}

                                            <label>

                                                <span class="sorting-label">
                                                    Zone / Rack
                                                </span>

                                                <input
                                                    type="text"
                                                    name="sorting_zone"
                                                    required
                                                    maxlength="80"
                                                    placeholder="e.g. STA-CRUZ-01"
                                                    autocomplete="off"
                                                >

                                            </label>

                                            {{-- SORTING NOTE --}}

                                            <label>

                                                <span class="sorting-label">
                                                    Sorting Note
                                                </span>

                                                <textarea
                                                    name="notes"
                                                    rows="2"
                                                    maxlength="1000"
                                                    placeholder="Optional note…"
                                                >{{ old('notes') }}</textarea>

                                            </label>

                                            {{-- SORT BUTTON --}}

                                            <button
                                                type="submit"
                                                class="sorting-button sorting-button-primary"
                                            >
                                                Mark Parcel Sorted
                                            </button>

                                        </form>

                                    </div>

                                @else

                                    <span class="sorting-badge sorting-badge-sorted">
                                        Sorted
                                    </span>

                                    <div class="ps-action-panel">

                                        <div class="sorting-completed">

                                            <strong>
                                                Ready for Next Leg
                                            </strong>

                                            <p>
                                                {{
                                                    $parcel->sorted_at
                                                        ?->timezone('Asia/Manila')
                                                        ->format('M d, Y · g:i A')
                                                    ?: 'Sorting completed'
                                                }}
                                            </p>

                                            @if($parcel->notes)

                                                <p class="sorting-note">
                                                    {{ $parcel->notes }}
                                                </p>

                                            @endif

                                        </div>

                                        <a
                                            href="{{ route('logistics.hub-transfers') }}"
                                            class="sorting-text-link"
                                        >
                                            Continue to Hub Transfer →
                                        </a>

                                    </div>

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        {{-- =================================================
            NO PARCELS
        ================================================== --}}

        @if($parcels->isEmpty())

            <div class="sorting-empty">

                <span
                    class="sorting-empty-icon"
                    aria-hidden="true"
                >
                    ✓
                </span>

                <h3>
                    Sorting Queue Is Clear
                </h3>

                <p>
                    Parcels appear here after their physical receipt
                    is confirmed at the origin sorting hub.
                </p>

                <a
                    href="{{ route('logistics.incoming-parcels') }}"
                    class="sorting-button sorting-button-primary"
                >
                    View Incoming Parcels
                </a>

            </div>

        @endif

        {{-- =================================================
            FILTER EMPTY STATE
        ================================================== --}}

        <div
            class="sorting-empty"
            data-sorting-empty
            hidden
        >

            <h3>
                No Matching Parcels
            </h3>

            <p>
                Try another order number, Seller, sorting zone,
                origin hub, or destination hub.
            </p>

            <button
                type="button"
                class="sorting-text-link"
                data-sorting-clear
            >
                Clear filters
            </button>

        </div>

        {{-- =================================================
            FOOTER
        ================================================== --}}

        <footer class="sorting-list-footer">

            <span
                data-sorting-visible-count
                aria-live="polite"
            >
                {{ $parcels->count() }} parcels shown
            </span>

            <span>

                {{ $originHubCount }}
                origin
                {{ $originHubCount === 1 ? 'hub' : 'hubs' }}

                ·

                {{ $destinationHubCount }}
                destination
                {{ $destinationHubCount === 1 ? 'hub' : 'hubs' }}

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
    | INITIALIZE PARCEL SORTING
    |--------------------------------------------------------------------------
    */

    function initializeParcelSorting() {

        document.querySelectorAll(
            '[data-sorting-workspace]'
        ).forEach(root => {

            if (root.dataset.initialized) {
                return;
            }

            root.dataset.initialized = 'true';

            /*
            |--------------------------------------------------------------------------
            | ELEMENT REFERENCES
            |--------------------------------------------------------------------------
            */

            const rows = [
                ...root.querySelectorAll('[data-sorting-row]')
            ];

            const search = root.querySelector(
                '[data-sorting-search]'
            );

            const tabs = [
                ...root.querySelectorAll('[data-sorting-filter]')
            ];

            const empty = root.querySelector(
                '[data-sorting-empty]'
            );

            const visibleCount = root.querySelector(
                '[data-sorting-visible-count]'
            );

            let filter = 'all';

            /*
            |--------------------------------------------------------------------------
            | NORMALIZATION
            |--------------------------------------------------------------------------
            */

            const normalize = value => {

                return String(value || '')
                    .toLocaleLowerCase()
                    .trim();

            };

            /*
            |--------------------------------------------------------------------------
            | SEARCH AND FILTER
            |--------------------------------------------------------------------------
            */

            function applyFilters() {

                const keyword = normalize(
                    search?.value
                );

                let shown = 0;

                rows.forEach(row => {

                    const matchesState =
                        filter === 'all'
                        || row.dataset.state === filter;

                    const matchesSearch =
                        normalize(row.dataset.search)
                            .includes(keyword);

                    row.hidden =
                        !matchesState || !matchesSearch;

                    if (!row.hidden) {
                        shown++;
                    }

                });

                tabs.forEach(tab => {

                    tab.setAttribute(
                        'aria-pressed',
                        String(
                            tab.dataset.sortingFilter === filter
                        )
                    );

                });

                if (empty) {

                    empty.hidden =
                        rows.length === 0 || shown > 0;

                }

                if (visibleCount) {

                    visibleCount.textContent =
                        `${shown} of ${rows.length} `
                        + `${
                            rows.length === 1
                                ? 'parcel'
                                : 'parcels'
                        } shown`;

                }

            }

            /*
            |--------------------------------------------------------------------------
            | FILTER BUTTONS
            |--------------------------------------------------------------------------
            */

            tabs.forEach(tab => {

                tab.addEventListener('click', () => {

                    filter =
                        tab.dataset.sortingFilter || 'all';

                    applyFilters();

                });

            });

            /*
            |--------------------------------------------------------------------------
            | SEARCH
            |--------------------------------------------------------------------------
            */

            search?.addEventListener(
                'input',
                applyFilters
            );

            /*
            |--------------------------------------------------------------------------
            | CLEAR FILTERS
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-sorting-clear]'
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
            | SORTING CONFIRMATION
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-sorting-form]'
            ).forEach(form => {

                form.addEventListener('submit', event => {

                    const zoneInput = form.querySelector(
                        'input[name="sorting_zone"]'
                    );

                    const zone = String(
                        zoneInput?.value || ''
                    ).trim();

                    /*
                     * Reject whitespace-only zone values.
                     */

                    if (!zone) {

                        event.preventDefault();

                        if (zoneInput) {
                            zoneInput.focus();
                        }

                        return;

                    }

                    const orderNumber =
                        form.dataset.orderNumber
                        || 'this parcel';

                    const confirmed = window.confirm(
                        `Mark ${orderNumber} as sorted `
                        + `under zone "${zone}"?`
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
                        button.textContent =
                            'Saving sorting result…';

                    }

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

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT INITIALIZATION
    |--------------------------------------------------------------------------
    */

    if (document.readyState === 'loading') {

        document.addEventListener(
            'DOMContentLoaded',
            initializeParcelSorting
        );

    } else {

        initializeParcelSorting();

    }

    /*
    |--------------------------------------------------------------------------
    | BROWSER BACK/FORWARD CACHE
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
