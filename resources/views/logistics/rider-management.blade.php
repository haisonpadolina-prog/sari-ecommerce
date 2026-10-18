
@extends('layouts.logistics')

@section('title', 'Rider Management | SARI Logistics')
@section('page-title', 'Rider Management')

@push('styles')
<style>
/*
|--------------------------------------------------------------------------
| SARI LOGISTICS — RIDER MANAGEMENT
| Unified Current Work UI
|--------------------------------------------------------------------------
|
| All Riders use the same Current Work component.
| Only the content and subtle status indicator change.
|
*/

#lgRiderManagement {
    --rm-amber: #d9950b;
    --rm-ink: #25211c;
    --rm-muted: #8a8177;
    --rm-border: #e8e2d9;
    --rm-shadow: 0 6px 18px rgba(33,24,14,.035);

    width: 100%;
    max-width: 1620px;
    margin-inline: auto;
    color: var(--rm-ink);
    font-family: Poppins, ui-sans-serif, system-ui, sans-serif;
}

#lgRiderManagement [hidden] {
    display: none !important;
}

/*
|--------------------------------------------------------------------------
| HEADING
|--------------------------------------------------------------------------
*/

#lgRiderManagement .rm-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    padding: 5px 2px 17px;
    border-bottom: 1px solid #e4e1dc;
}

#lgRiderManagement .rm-eyebrow {
    color: #a76f11;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
}

#lgRiderManagement .rm-page-title {
    margin-top: 5px;
    color: #24201b;
    font-size: clamp(23px,2.15vw,30px);
    font-weight: 750;
    line-height: 1.2;
    letter-spacing: -.045em;
}

#lgRiderManagement .rm-page-title span {
    color: var(--rm-amber);
}

#lgRiderManagement .rm-intro {
    max-width: 710px;
    margin-top: 6px;
    color: var(--rm-muted);
    font-size: 10px;
    line-height: 1.7;
}

#lgRiderManagement .rm-coverage {
    display: flex;
    flex-wrap: wrap;
    gap: 9px;
}

#lgRiderManagement .rm-coverage-item {
    min-width: 118px;
    padding: 10px 13px;
    border: 1px solid var(--rm-border);
    border-radius: 11px;
    background: #fff;
    box-shadow: var(--rm-shadow);
}

#lgRiderManagement .rm-coverage-item small {
    display: block;
    color: #8f8579;
    font-size: 8px;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
}

#lgRiderManagement .rm-coverage-item strong {
    display: block;
    margin-top: 4px;
    color: #28221c;
    font-size: 18px;
    font-weight: 750;
    line-height: 1.2;
}

/*
|--------------------------------------------------------------------------
| KPI CARDS
|--------------------------------------------------------------------------
*/

#lgRiderManagement .rm-kpis {
    display: grid;
    grid-template-columns: repeat(4,minmax(0,1fr));
    gap: 12px;
}

#lgRiderManagement .rm-kpi {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    min-width: 0;
    min-height: 98px;
    gap: 10px;
    padding: 14px;
    border: 1px solid var(--rm-border);
    border-radius: 16px;
    background: #fff;
    box-shadow: var(--rm-shadow);

    transition:
        transform .18s cubic-bezier(.22,1,.36,1),
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgRiderManagement .rm-kpi-label {
    color: #756b60;
    font-size: 10px;
    font-weight: 600;
}

#lgRiderManagement .rm-kpi-value {
    margin-top: 8px;
    color: #211d18;
    font-size: 25px;
    font-weight: 750;
    line-height: 1.15;
    letter-spacing: -.045em;
}

#lgRiderManagement .rm-kpi-note {
    margin-top: 7px;
    color: #8d8378;
    font-size: 9px;
    line-height: 1.5;
}

#lgRiderManagement .rm-kpi-icon {
    display: grid;
    place-items: center;
    flex: 0 0 41px;
    width: 41px;
    height: 41px;
    border-radius: 11px;
    background: #f0f6ff;
    color: #507cab;
}

#lgRiderManagement .rm-kpi-icon svg {
    width: 18px;
    height: 18px;
}

#lgRiderManagement .rm-kpi-icon.online {
    background: #eff8f2;
    color: #47845c;
}

#lgRiderManagement .rm-kpi-icon.busy {
    background: #fff7e9;
    color: #bd8420;
}

#lgRiderManagement .rm-kpi-icon.available {
    background: #f5f2fb;
    color: #7864a2;
}

/*
|--------------------------------------------------------------------------
| RIDER DIRECTORY PANEL
|--------------------------------------------------------------------------
*/

#lgRiderManagement .rm-panel {
    min-width: 0;
    overflow: hidden;
    border: 1px solid var(--rm-border);
    border-radius: 17px;
    background: #fff;
    box-shadow: var(--rm-shadow);
}

#lgRiderManagement .rm-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    padding: 15px 17px;
    border-bottom: 1px solid #eee8df;
}

#lgRiderManagement .rm-panel-title {
    margin-top: 4px;
    color: #29231d;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.035em;
}

#lgRiderManagement .rm-panel-desc {
    margin-top: 4px;
    color: #93897e;
    font-size: 9px;
    line-height: 1.5;
}

/*
|--------------------------------------------------------------------------
| TOOLBAR
|--------------------------------------------------------------------------
*/

#lgRiderManagement .rm-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 11px 16px;
    border-bottom: 1px solid #eee8df;
    background: #fff;
}

#lgRiderManagement .rm-search-wrap {
    position: relative;
    flex: 1 1 220px;
    max-width: 350px;
}

#lgRiderManagement .rm-search-wrap svg {
    position: absolute;
    top: 50%;
    left: 12px;
    width: 15px;
    height: 15px;
    transform: translateY(-50%);
    color: #a3988c;
    pointer-events: none;
}

#lgRiderManagement .rm-search {
    width: 100%;
    height: 39px;
    padding: 0 12px 0 36px;
    border: 1px solid #dfe2e6;
    border-radius: 10px;
    outline: none;
    background: #fff;
    color: #50483e;
    font: inherit;
    font-size: 10px;

    transition:
        border-color .15s ease,
        box-shadow .15s ease;
}

#lgRiderManagement .rm-search:focus {
    border-color: var(--rm-amber);
    box-shadow: 0 0 0 3px rgba(217,149,11,.09);
}

#lgRiderManagement .rm-toolbar-right {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

#lgRiderManagement .rm-filters {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 3px;
    padding: 4px;
    border: 1px solid #e6e1d9;
    border-radius: 10px;
    background: #f7f8fa;
}

#lgRiderManagement .rm-filter {
    min-height: 30px;
    padding: 0 11px;
    border: 1px solid transparent;
    border-radius: 7px;
    background: transparent;
    color: #756d63;
    font: inherit;
    font-size: 9px;
    font-weight: 550;
    white-space: nowrap;
    cursor: pointer;

    transition:
        background .15s ease,
        border-color .15s ease,
        color .15s ease,
        box-shadow .15s ease;
}

#lgRiderManagement .rm-filter:hover {
    background: #fff9ee;
    color: #a46e10;
}

#lgRiderManagement .rm-filter[aria-pressed="true"] {
    border-color: #e9dfce;
    background: #fff;
    color: #a36c0a;
    font-weight: 700;
    box-shadow: 0 2px 5px rgba(35,29,22,.035);
}

#lgRiderManagement .rm-reset {
    height: 36px;
    padding: 0 11px;
    border: 1px solid #e6e1d9;
    border-radius: 9px;
    background: #fff;
    color: #897b6a;
    font-size: 9px;
    font-weight: 600;

    transition:
        background .15s ease,
        color .15s ease;
}

#lgRiderManagement .rm-reset:hover {
    background: #fff9ef;
    color: #a36c0a;
}

/*
|--------------------------------------------------------------------------
| RIDER DIRECTORY TABLE
|--------------------------------------------------------------------------
*/

#lgRiderManagement .rm-table-wrap {
    width: 100%;
    overflow-x: auto;
}

#lgRiderManagement .rm-table {
    width: 100%;
    min-width: 920px;
    border-collapse: collapse;
    table-layout: fixed;
    text-align: left;
}

#lgRiderManagement .rm-table th:nth-child(1) {
    width: 20%;
}

#lgRiderManagement .rm-table th:nth-child(2) {
    width: 15%;
}

#lgRiderManagement .rm-table th:nth-child(3) {
    width: 15%;
}

#lgRiderManagement .rm-table th:nth-child(4) {
    width: 10%;
}

#lgRiderManagement .rm-table th:nth-child(5) {
    width: 11%;
}

#lgRiderManagement .rm-table th:nth-child(6) {
    width: 29%;
}

#lgRiderManagement .rm-table thead {
    background: #f7f8fa;
}

#lgRiderManagement .rm-table th {
    padding: 12px 11px;
    color: #81776d;
    font-size: 8.5px;
    font-weight: 650;
    letter-spacing: .05em;
    text-transform: uppercase;
}

#lgRiderManagement .rm-table td {
    min-width: 0;
    padding: 11px;
    border-top: 1px solid #f0ebe5;
    color: #61584e;
    font-size: 10px;
    line-height: 1.5;
    vertical-align: middle;
}

#lgRiderManagement .rm-rider-row {
    transition: background-color .16s ease;
}

#lgRiderManagement .rm-rider-row:hover {
    background: #fffaf2;
}

/*
|--------------------------------------------------------------------------
| RIDER IDENTITY
|--------------------------------------------------------------------------
*/

#lgRiderManagement .rm-person {
    display: flex;
    align-items: center;
    min-width: 0;
    gap: 10px;
}

#lgRiderManagement .rm-avatar {
    display: grid;
    place-items: center;
    flex: 0 0 38px;
    width: 38px;
    height: 38px;
    border: 1px solid #eee3ce;
    border-radius: 11px;
    background: #fff5e1;
    color: #a97312;
    font-size: 11px;
    font-weight: 700;
}

#lgRiderManagement .rm-person-name {
    color: #342d25;
    font-size: 10.5px;
    font-weight: 700;
    overflow-wrap: anywhere;
}

#lgRiderManagement .rm-secondary {
    margin-top: 3px;
    color: #908579;
    font-size: 9px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgRiderManagement .rm-cell-title {
    color: #51483e;
    font-size: 10px;
    font-weight: 650;
    overflow-wrap: anywhere;
}

/*
|--------------------------------------------------------------------------
| VEHICLE
|--------------------------------------------------------------------------
*/

#lgRiderManagement .rm-vehicle {
    display: flex;
    align-items: center;
    min-width: 0;
    gap: 8px;
}

#lgRiderManagement .rm-vehicle-icon {
    display: grid;
    place-items: center;
    flex: 0 0 30px;
    width: 30px;
    height: 30px;
    border-radius: 9px;
    background: #f6f2eb;
    color: #756b5f;
}

#lgRiderManagement .rm-vehicle-icon svg {
    width: 16px;
    height: 16px;
}

/*
|--------------------------------------------------------------------------
| ACCOUNT AND AVAILABILITY BADGES
|--------------------------------------------------------------------------
*/

#lgRiderManagement .rm-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    max-width: 100%;
    padding: 5px 8px;
    border: 1px solid transparent;
    border-radius: 999px;
    font-size: 8.5px;
    font-weight: 650;
    line-height: 1.3;
    white-space: nowrap;
}

#lgRiderManagement .rm-badge-dot {
    width: 6px;
    height: 6px;
    flex: 0 0 6px;
    border-radius: 50%;
    background: currentColor;
}

#lgRiderManagement .rm-badge.active,
#lgRiderManagement .rm-badge.available {
    border-color: #d5e9da;
    background: #eff9f2;
    color: #408056;
}

#lgRiderManagement .rm-badge.busy {
    border-color: #efdfc0;
    background: #fff8e9;
    color: #a87415;
}

#lgRiderManagement .rm-badge.offline {
    border-color: #e7e2dc;
    background: #f4f3f1;
    color: #817970;
}

#lgRiderManagement .rm-badge.inactive {
    border-color: #ecd5d5;
    background: #fff2f2;
    color: #aa6060;
}

/*
|--------------------------------------------------------------------------
| UNIFIED CURRENT WORK COMPONENT
|--------------------------------------------------------------------------
|
| IMPORTANT:
|
| Available, Busy, Offline, Inactive, Pickup and Last Mile
| ALL use the same surface, dimensions and hierarchy.
|
| No oversized colored work cards.
|
*/

#lgRiderManagement .rm-work-list {
    display: grid;
    width: 100%;
    min-width: 0;
    gap: 6px;
}

/* Shared visual surface for every Rider state */

#lgRiderManagement .rm-workbox {
    display: grid;
    grid-template-columns: 27px minmax(0,1fr);
    align-items: center;
    gap: 9px;

    width: 100%;
    min-width: 0;
    min-height: 62px;

    padding: 9px 10px;

    border: 1px solid #e9e5df;
    border-radius: 10px;

    background: #fbfbfc;
    color: inherit;

    text-decoration: none;

    box-shadow: none;

    transition:
        background-color .15s ease,
        border-color .15s ease,
        box-shadow .15s ease;
}

/* Clickable work uses the same surface */

#lgRiderManagement a.rm-workbox {
    cursor: pointer;
}

#lgRiderManagement a.rm-workbox:hover {
    background: #fffaf2;
    border-color: #d9c8a8;
    box-shadow: 0 3px 10px rgba(43,33,21,.035);
}

/* Every icon has identical geometry */

#lgRiderManagement .rm-work-icon {
    display: grid;
    place-items: center;

    width: 27px;
    height: 27px;

    border-radius: 8px;

    background: #f0efec;
    color: #898077;
}

#lgRiderManagement .rm-work-icon svg {
    width: 14px;
    height: 14px;

    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* Subtle icon distinctions only */

#lgRiderManagement .rm-work-icon.available {
    background: #eaf6ee;
    color: #4b8b63;
}

#lgRiderManagement .rm-work-icon.pickup {
    background: #fff4df;
    color: #ae791b;
}

#lgRiderManagement .rm-work-icon.last-mile {
    background: #edf3fa;
    color: #5b7ea1;
}

#lgRiderManagement .rm-work-icon.offline,
#lgRiderManagement .rm-work-icon.inactive {
    background: #f0efec;
    color: #898077;
}

/* Same typography across all states */

#lgRiderManagement .rm-work-content {
    min-width: 0;
}

#lgRiderManagement .rm-work-top {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 3px 7px;
    min-width: 0;
}

#lgRiderManagement .rm-work-type {
    color: #807568;
    font-size: 8px;
    line-height: 1.4;
    font-weight: 700;
    letter-spacing: .035em;
    text-transform: uppercase;
}

#lgRiderManagement .rm-work-count {
    color: #a08a68;
    font-size: 8px;
    font-weight: 600;
}

/* Full route, no clipping */

#lgRiderManagement .rm-work-primary {
    display: block;
    margin-top: 4px;

    color: #453d33;
    font-size: 9.5px;
    font-weight: 650;
    line-height: 1.55;

    white-space: normal;
    overflow-wrap: anywhere;
}

/* Supporting status */

#lgRiderManagement .rm-work-detail {
    display: block;
    margin-top: 3px;

    color: #91877c;
    font-size: 8.5px;
    font-weight: 400;
    line-height: 1.5;

    overflow-wrap: anywhere;
}

/* No special colored card for busy Riders */

#lgRiderManagement .rm-workbox[data-work-kind="pickup"],
#lgRiderManagement .rm-workbox[data-work-kind="last-mile"] {
    background: #fbfbfc;
}

/*
|--------------------------------------------------------------------------
| EMPTY STATE
|--------------------------------------------------------------------------
*/

#lgRiderManagement .rm-empty {
    padding: 42px 20px !important;
    text-align: center;
}

#lgRiderManagement .rm-empty-icon {
    display: grid;
    place-items: center;
    width: 44px;
    height: 44px;
    margin: 0 auto;
    border-radius: 12px;
    background: #fff7e8;
    color: #ba8526;
}

#lgRiderManagement .rm-empty-icon svg {
    width: 21px;
    height: 21px;
}

#lgRiderManagement .rm-empty strong {
    display: block;
    margin-top: 11px;
    font-size: 11px;
    color: #51483e;
}

#lgRiderManagement .rm-empty p {
    margin-top: 5px;
    color: #948a7e;
    font-size: 9px;
}

/*
|--------------------------------------------------------------------------
| FOOTER
|--------------------------------------------------------------------------
*/

#lgRiderManagement .rm-footer {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 9px;
    padding: 12px 16px;
    border-top: 1px solid #eee8df;
    background: #fff;
    color: #978d80;
    font-size: 9px;
}

/*
|--------------------------------------------------------------------------
| ACCESSIBILITY AND RESPONSIVE
|--------------------------------------------------------------------------
*/

#lgRiderManagement
:is(button,input,a):focus-visible {
    outline: 2px solid rgba(217,149,11,.6);
    outline-offset: 2px;
}

@media (hover:hover) and (pointer:fine) {
    #lgRiderManagement .rm-kpi:hover {
        transform: translateY(-2px);
        border-color: #d8c9b2;
        box-shadow: 0 13px 28px rgba(43,33,21,.06);
    }
}

@media (max-width:1199px) {
    #lgRiderManagement .rm-kpis {
        grid-template-columns: repeat(2,minmax(0,1fr));
    }
}

@media (max-width:767px) {
    #lgRiderManagement .rm-toolbar {
        align-items: stretch;
    }

    #lgRiderManagement .rm-search-wrap {
        max-width: none;
        flex-basis: 100%;
    }

    #lgRiderManagement .rm-toolbar-right {
        width: 100%;
    }
}

@media (max-width:480px) {
    #lgRiderManagement .rm-coverage {
        width: 100%;
    }

    #lgRiderManagement .rm-coverage-item {
        flex: 1;
    }

    #lgRiderManagement .rm-kpis {
        gap: 9px;
    }

    #lgRiderManagement .rm-kpi {
        padding: 12px;
    }

    #lgRiderManagement .rm-kpi-value {
        font-size: 22px;
    }

    #lgRiderManagement .rm-kpi-icon {
        width: 34px;
        height: 34px;
        flex-basis: 34px;
    }

    #lgRiderManagement .rm-filters {
        width: 100%;
    }

    #lgRiderManagement .rm-filter {
        flex: 1;
        padding-inline: 5px;
    }
}

@media (prefers-reduced-motion:reduce) {
    #lgRiderManagement *,
    #lgRiderManagement *::before,
    #lgRiderManagement *::after {
        transition-duration: .01ms !important;
        animation-duration: .01ms !important;
    }

    #lgRiderManagement .rm-kpi:hover {
        transform: none !important;
    }
}
</style>
@endpush

@section('content')

<div id="lgRiderManagement" class="w-full space-y-4">

    {{-- PAGE HEADING --}}

    <header class="rm-heading">

        <div class="min-w-0">

            <p class="rm-eyebrow">
                Riders &amp; Network
            </p>

            <h2 class="rm-page-title">
                Rider <span>Management</span>
            </h2>

            <p class="rm-intro">
                Monitor Rider availability, pickup batches, last-mile
                assignments, service areas and vehicle information
                across your Logistics network.
            </p>

        </div>

        <div class="rm-coverage">

            <div class="rm-coverage-item">
                <small>Municipalities</small>

                <strong>
                    {{ number_format($stats['municipalities']) }}
                </strong>
            </div>

            <div class="rm-coverage-item">
                <small>Barangays</small>

                <strong>
                    {{ number_format($stats['barangays']) }}
                </strong>
            </div>

        </div>

    </header>

    {{-- KPI CARDS --}}

    @php
        $riderMetrics = [
            [
                'label' => 'Approved Riders',
                'value' => $stats['total'],
                'note' => $stats['active'].' active accounts',
                'tone' => '',
                'icon' => 'person',
            ],
            [
                'label' => 'Online Riders',
                'value' => $stats['online'],
                'note' => 'Ready or currently working',
                'tone' => 'online',
                'icon' => 'check',
            ],
            [
                'label' => 'Busy Now',
                'value' => $stats['busy'],
                'note' => 'Active pickup or last-mile work',
                'tone' => 'busy',
                'icon' => 'clock',
            ],
            [
                'label' => 'Available Now',
                'value' => $stats['available'],
                'note' => 'Online with no active work',
                'tone' => 'available',
                'icon' => 'arrow',
            ],
        ];
    @endphp

    <section class="rm-kpis" aria-label="Rider overview">

        @foreach($riderMetrics as $metric)

            <article class="rm-kpi">

                <div class="min-w-0">

                    <p class="rm-kpi-label">
                        {{ $metric['label'] }}
                    </p>

                    <p class="rm-kpi-value">
                        {{ number_format($metric['value']) }}
                    </p>

                    <p class="rm-kpi-note">
                        {{ $metric['note'] }}
                    </p>

                </div>

                <span
                    class="rm-kpi-icon {{ $metric['tone'] }}"
                    aria-hidden="true"
                >

                    @switch($metric['icon'])

                        @case('person')
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="8" r="4"/>
                                <path d="M5 21c.6-4.5 3-7 7-7s6.4 2.5 7 7"/>
                            </svg>
                            @break

                        @case('check')
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

                        @case('clock')
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

                        @default
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M4 12h16m-5-5 5 5-5 5"/>
                            </svg>

                    @endswitch

                </span>

            </article>

        @endforeach

    </section>

    {{-- RIDER DIRECTORY --}}

    <section
        class="rm-panel"
        aria-labelledby="rmDirectoryHeading"
    >

        <header class="rm-panel-head">

            <div>

                <p class="rm-eyebrow">
                    Rider Directory
                </p>

                <h3
                    id="rmDirectoryHeading"
                    class="rm-panel-title"
                >
                    Approved Logistics Riders
                </h3>

                <p class="rm-panel-desc">
                    Current service area, availability and assignment status.
                </p>

            </div>

            <span class="rm-badge active">
                <span
                    class="rm-badge-dot"
                    aria-hidden="true"
                ></span>

                Network Directory
            </span>

        </header>

        {{-- SEARCH AND FILTER TOOLBAR --}}

        <div class="rm-toolbar">

            <label class="rm-search-wrap">

                <span class="sr-only">
                    Search Riders
                </span>

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-4-4"/>
                </svg>

                <input
                    id="riderManagementSearch"
                    class="rm-search"
                    type="search"
                    autocomplete="off"
                    placeholder="Search Rider, barangay, vehicle..."
                >

            </label>

            <div class="rm-toolbar-right">

                <div
                    class="rm-filters"
                    role="group"
                    aria-label="Filter Rider availability"
                >

                    <button
                        type="button"
                        class="rm-filter"
                        data-rider-filter="all"
                        aria-pressed="true"
                    >
                        All
                    </button>

                    <button
                        type="button"
                        class="rm-filter"
                        data-rider-filter="available"
                        aria-pressed="false"
                    >
                        Available
                    </button>

                    <button
                        type="button"
                        class="rm-filter"
                        data-rider-filter="busy"
                        aria-pressed="false"
                    >
                        Busy
                    </button>

                    <button
                        type="button"
                        class="rm-filter"
                        data-rider-filter="offline"
                        aria-pressed="false"
                    >
                        Offline
                    </button>

                    <button
                        type="button"
                        class="rm-filter"
                        data-rider-filter="inactive"
                        aria-pressed="false"
                    >
                        Inactive
                    </button>

                </div>

                <button
                    id="rmResetFilters"
                    type="button"
                    class="rm-reset"
                >
                    Reset
                </button>

            </div>

        </div>

        {{-- DIRECTORY TABLE --}}

        <div class="rm-table-wrap">

            <table class="rm-table">

                <thead>
                    <tr>
                        <th scope="col">Rider</th>
                        <th scope="col">Service Area</th>
                        <th scope="col">Vehicle</th>
                        <th scope="col">Account</th>
                        <th scope="col">Availability</th>
                        <th scope="col">Current Work</th>
                    </tr>
                </thead>

                <tbody id="riderManagementTableBody">

                    @forelse($riderRows as $row)

                        @php
                            $rider = $row['rider'];

                            $name = trim(
                                ($rider->first_name ?? '') .
                                ' ' .
                                ($rider->last_name ?? '')
                            );

                            $initials = strtoupper(
                                mb_substr(
                                    $rider->first_name ?: '',
                                    0,
                                    1
                                )
                                .
                                mb_substr(
                                    $rider->last_name ?: '',
                                    0,
                                    1
                                )
                            );

                            $searchText = strtolower(
                                implode(' ', [
                                    $name,
                                    $rider->email,
                                    $rider->contact_no,
                                    $rider->municipality_name,
                                    $rider->barangay_name,
                                    $rider->province_name,
                                    $rider->vehicle_type,
                                    $rider->vehicle_model,
                                    $rider->plate_number,
                                ])
                            );

                            $state = $row['state'];

                            $isActive = (bool) $row['is_active'];
                        @endphp

                        <tr
                            class="rm-rider-row"
                            data-rider-row
                            data-state="{{ $state }}"
                            data-search="{{ $searchText }}"
                        >

                            {{-- RIDER IDENTITY --}}

                            <td>

                                <div class="rm-person">

                                    <span
                                        class="rm-avatar"
                                        aria-hidden="true"
                                    >
                                        {{ $initials ?: 'R' }}
                                    </span>

                                    <div class="min-w-0">

                                        <p class="rm-person-name">
                                            {{ $name ?: 'Unnamed Rider' }}
                                        </p>

                                        <p class="rm-secondary">
                                            {{ $rider->email }}
                                        </p>

                                        <p class="rm-secondary">
                                            {{
                                                $rider->contact_no
                                                ?: 'No contact number'
                                            }}
                                        </p>

                                    </div>

                                </div>

                            </td>

                            {{-- SERVICE AREA --}}

                            <td>

                                <p class="rm-cell-title">
                                    {{
                                        $rider->barangay_name
                                        ?: 'Unassigned Barangay'
                                    }}
                                </p>

                                <p class="rm-secondary">

                                    {{
                                        $rider->municipality_name
                                        ?: '—'
                                    }}

                                    @if($rider->province_name)
                                        · {{ $rider->province_name }}
                                    @endif

                                </p>

                            </td>

                            {{-- VEHICLE --}}

                            <td>

                                <div class="rm-vehicle">

                                    <span
                                        class="rm-vehicle-icon"
                                        aria-hidden="true"
                                    >

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <circle cx="6" cy="17" r="2"/>
                                            <circle cx="18" cy="17" r="2"/>
                                            <path d="M8 17h8M6 15 8 9h7l3 6"/>
                                        </svg>

                                    </span>

                                    <div class="min-w-0">

                                        <p class="rm-cell-title">
                                            {{
                                                $rider->vehicle_type
                                                ?: 'Vehicle not specified'
                                            }}
                                        </p>

                                        <p class="rm-secondary">

                                            @if($rider->vehicle_model)
                                                {{ $rider->vehicle_model }} ·
                                            @endif

                                            {{
                                                $rider->plate_number
                                                ?: 'No plate'
                                            }}

                                        </p>

                                    </div>

                                </div>

                            </td>

                            {{-- ACCOUNT --}}

                            <td>

                                <span
                                    class="rm-badge {{ $isActive ? 'active' : 'inactive' }}"
                                >

                                    <span
                                        class="rm-badge-dot"
                                        aria-hidden="true"
                                    ></span>

                                    {{ $isActive ? 'Active' : 'Inactive' }}

                                </span>

                            </td>

                            {{-- AVAILABILITY --}}

                            <td>

                                @php
                                    $stateTone = in_array(
                                        $state,
                                        [
                                            'available',
                                            'busy',
                                            'offline',
                                        ],
                                        true
                                    )
                                        ? $state
                                        : 'inactive';
                                @endphp

                                <span class="rm-badge {{ $stateTone }}">

                                    <span
                                        class="rm-badge-dot"
                                        aria-hidden="true"
                                    ></span>

                                    {{ ucfirst($state) }}

                                </span>

                            </td>

                            {{-- UNIFIED CURRENT WORK --}}

                            <td>

                                <div class="rm-work-list">

                                    @if(!empty($row['work_items']))

                                        {{--
                                            Active work still comes from
                                            the existing controller.

                                            Pickup and Last Mile now use
                                            the SAME neutral component.
                                        --}}

                                        @foreach($row['work_items'] as $work)

                                            @php
                                                $isPickup =
                                                    $work['type'] === 'pickup';

                                                $workKind = $isPickup
                                                    ? 'pickup'
                                                    : 'last-mile';

                                                $workUrl = $isPickup
                                                    ? route(
                                                        'logistics.delivery-monitoring'
                                                    )
                                                    : route(
                                                        'logistics.last-mile-assignment'
                                                    );
                                            @endphp

                                            <a
                                                href="{{ $workUrl }}"
                                                class="rm-workbox"
                                                data-work-kind="{{ $workKind }}"
                                                aria-label="{{
                                                    $work['label']
                                                    . ': '
                                                    . $work['title']
                                                    . '. '
                                                    . $work['route']
                                                    . '. '
                                                    . $work['status']
                                                }}"
                                            >

                                                <span
                                                    class="rm-work-icon {{ $workKind }}"
                                                    aria-hidden="true"
                                                >

                                                    @if($isPickup)

                                                        <svg viewBox="0 0 24 24">

                                                            <path d="M3 7h12v10H3z"/>

                                                            <path d="M15 10h3l3 3v4h-6"/>

                                                            <circle cx="7" cy="18" r="1.7"/>

                                                            <circle cx="18" cy="18" r="1.7"/>

                                                        </svg>

                                                    @else

                                                        <svg viewBox="0 0 24 24">

                                                            <path d="M4 7h16v13H4z"/>

                                                            <path d="M8 7V4h8v3"/>

                                                            <path d="m9 13 2.5 2.5L16 11"/>

                                                        </svg>

                                                    @endif

                                                </span>

                                                <div class="rm-work-content">

                                                    <div class="rm-work-top">

                                                        <span class="rm-work-type">
                                                            {{ $work['label'] }}
                                                        </span>

                                                        <span class="rm-work-count">
                                                            · {{ $work['title'] }}
                                                        </span>

                                                    </div>

                                                    <span class="rm-work-primary">
                                                        {{ $work['route'] }}
                                                    </span>

                                                    <span class="rm-work-detail">
                                                        {{ $work['status'] }}
                                                    </span>

                                                </div>

                                            </a>

                                        @endforeach

                                    @else

                                        {{--
                                            No assignment uses the exact
                                            same component and dimensions.
                                        --}}

                                        <div
                                            class="rm-workbox"
                                            data-work-kind="{{ $state }}"
                                        >

                                            <span
                                                class="rm-work-icon {{ $state }}"
                                                aria-hidden="true"
                                            >

                                                @if($state === 'available')

                                                    <svg viewBox="0 0 24 24">
                                                        <circle cx="12" cy="12" r="9"/>
                                                        <path d="m8 12 2.5 2.5L16 9"/>
                                                    </svg>

                                                @elseif($state === 'offline')

                                                    <svg viewBox="0 0 24 24">
                                                        <circle cx="12" cy="12" r="9"/>
                                                        <path d="M8 8l8 8m0-8-8 8"/>
                                                    </svg>

                                                @elseif($state === 'inactive')

                                                    <svg viewBox="0 0 24 24">
                                                        <circle cx="12" cy="12" r="9"/>
                                                        <path d="M12 7v6M12 17h.01"/>
                                                    </svg>

                                                @else

                                                    <svg viewBox="0 0 24 24">
                                                        <circle cx="12" cy="12" r="9"/>
                                                        <path d="M12 7v5l3 2"/>
                                                    </svg>

                                                @endif

                                            </span>

                                            <div class="rm-work-content">

                                                <div class="rm-work-top">

                                                    <span class="rm-work-type">
                                                        No Current Assignment
                                                    </span>

                                                </div>

                                                <span class="rm-work-primary">

                                                    @switch($state)

                                                        @case('available')
                                                            Ready for assignment
                                                            @break

                                                        @case('offline')
                                                            Rider is offline
                                                            @break

                                                        @case('inactive')
                                                            Account inactive
                                                            @break

                                                        @default
                                                            No active work

                                                    @endswitch

                                                </span>

                                                <span class="rm-work-detail">

                                                    @switch($state)

                                                        @case('available')
                                                            Available for Logistics dispatch
                                                            @break

                                                        @case('offline')
                                                            Not accepting assignments
                                                            @break

                                                        @case('inactive')
                                                            Account currently unavailable
                                                            @break

                                                        @default
                                                            No active assignment recorded

                                                    @endswitch

                                                </span>

                                            </div>

                                        </div>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        {{-- NO APPROVED RIDERS --}}

                        <tr>

                            <td colspan="6" class="rm-empty">

                                <span
                                    class="rm-empty-icon"
                                    aria-hidden="true"
                                >

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

                                </span>

                                <strong>
                                    No approved Riders yet
                                </strong>

                                <p>
                                    Approved Rider applications will appear here.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                    {{-- FILTER EMPTY STATE --}}

                    <tr id="riderFilterEmptyState" hidden>

                        <td colspan="6" class="rm-empty">

                            <span
                                class="rm-empty-icon"
                                aria-hidden="true"
                            >

                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <circle cx="11" cy="11" r="7"/>
                                    <path d="m20 20-4-4"/>
                                </svg>

                            </span>

                            <strong>
                                No matching Riders
                            </strong>

                            <p>
                                Try another name, barangay, vehicle
                                or availability filter.
                            </p>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        {{-- FOOTER --}}

        <footer class="rm-footer">

            <span id="rmVisibleCount">

                {{ count($riderRows) }}

                {{
                    count($riderRows) === 1
                        ? 'Rider'
                        : 'Riders'
                }}

                shown

            </span>

            <span>
                Live network availability
            </span>

        </footer>

    </section>

</div>

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    'use strict';

    const root = document.getElementById(
        'lgRiderManagement'
    );

    if (!root) {
        return;
    }

    const searchInput = root.querySelector(
        '#riderManagementSearch'
    );

    const rows = Array.from(
        root.querySelectorAll('[data-rider-row]')
    );

    const filterButtons = Array.from(
        root.querySelectorAll('[data-rider-filter]')
    );

    const emptyState = root.querySelector(
        '#riderFilterEmptyState'
    );

    const visibleCount = root.querySelector(
        '#rmVisibleCount'
    );

    const resetButton = root.querySelector(
        '#rmResetFilters'
    );

    let activeFilter = 'all';

    /*
    |--------------------------------------------------------------------------
    | NORMALIZATION
    |--------------------------------------------------------------------------
    */

    function normalize(value) {

        return String(value || '')
            .toLocaleLowerCase()
            .trim();

    }

    /*
    |--------------------------------------------------------------------------
    | DIRECTORY FILTERING
    |--------------------------------------------------------------------------
    */

    function renderRows() {

        const keyword = normalize(
            searchInput?.value
        );

        let shown = 0;

        rows.forEach(function (row) {

            const searchable = normalize(
                row.dataset.search
            );

            const state = normalize(
                row.dataset.state
            );

            const matchesSearch =
                !keyword ||
                searchable.includes(keyword);

            const matchesFilter =
                activeFilter === 'all' ||
                state === activeFilter;

            const visible =
                matchesSearch &&
                matchesFilter;

            row.hidden = !visible;

            if (visible) {
                shown++;
            }

        });

        if (emptyState) {

            emptyState.hidden =
                rows.length === 0 ||
                shown !== 0;

        }

        if (visibleCount) {

            visibleCount.textContent =
                `${shown} of ${rows.length} ` +
                `${rows.length === 1 ? 'Rider' : 'Riders'} shown`;

        }

        filterButtons.forEach(function (button) {

            const selected =
                button.dataset.riderFilter === activeFilter;

            button.setAttribute(
                'aria-pressed',
                String(selected)
            );

        });

    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH EVENT
    |--------------------------------------------------------------------------
    */

    searchInput?.addEventListener(
        'input',
        renderRows
    );

    /*
    |--------------------------------------------------------------------------
    | FILTER EVENTS
    |--------------------------------------------------------------------------
    */

    filterButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            activeFilter =
                button.dataset.riderFilter || 'all';

            renderRows();

        });

    });

    /*
    |--------------------------------------------------------------------------
    | RESET FILTERS
    |--------------------------------------------------------------------------
    */

    resetButton?.addEventListener('click', function () {

        activeFilter = 'all';

        if (searchInput) {
            searchInput.value = '';
        }

        renderRows();

    });

    /*
    |--------------------------------------------------------------------------
    | INITIALIZE
    |--------------------------------------------------------------------------
    */

    renderRows();

});
</script>

@endpush
