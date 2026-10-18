@extends('layouts.logistics')

@section('title', 'Logistics Dashboard | SARI')
@section('page-title', 'Dashboard Overview')

@push('styles')

<link
    rel="stylesheet"
    href="https://cdn-uicons.flaticon.com/uicons-thin-straight/css/uicons-thin-straight.css"
>

<style>
/* =========================================================
   SARI LOGISTICS DASHBOARD
   Seller Dashboard Visual Parity
========================================================= */

#logisticsDashboard.lg-dashboard {
    font-family: Poppins, ui-sans-serif, sans-serif;
    max-width: 1700px;
    color: #24201b;
}

/* HERO */

#logisticsDashboard .lg-hero {
    position: relative;
    display: block;
    min-height: 0;
    padding: 18px 24px;
    overflow: hidden;
    border: 1px solid #31302e;
    border-radius: 19px;
    background: #20201f;
    box-shadow:
        0 13px 30px rgba(20,18,16,.12),
        inset 0 1px 0 rgba(255,255,255,.035);
}

#logisticsDashboard .lg-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
        radial-gradient(
            circle at 5% 50%,
            rgba(220,158,23,.05),
            transparent 23%
        ),
        linear-gradient(
            90deg,
            rgba(255,255,255,.018),
            transparent 30%
        );
}

#logisticsDashboard .lg-hero-layout {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns:
        minmax(300px,1.18fr)
        1px
        minmax(480px,1.42fr);
    align-items: center;
    gap: 22px;
    min-height: 102px;
}

#logisticsDashboard .lg-hero-title {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin: 0;
    font-size: clamp(21px,1.42vw,29px);
    line-height: 1.1;
    font-weight: 700;
    letter-spacing: -.045em;
    color: white;
}

/* Exact Seller-style Flaticon wave icon */

#logisticsDashboard .lg-wave {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 27px;
    height: 27px;
    flex: 0 0 27px;
    padding: 0;
    margin: 0;
    color: #e6a61e;
    line-height: 1;
}

#logisticsDashboard .lg-wave .fi {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 27px;
    height: 27px;
    font-size: 24px;
    font-style: normal;
    line-height: 1;
}

#logisticsDashboard .lg-wave .fi::before {
    display: block;
    margin: 0;
    line-height: 1;
}

#logisticsDashboard .lg-hero-sub {
    margin: 8px 0 0;
    color: rgba(255,255,255,.74);
    font-size: 9.6px;
    line-height: 1.55;
}

#logisticsDashboard .lg-hero-status {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-top: 12px;
    color: rgba(255,255,255,.65);
    font-size: 7.4px;
    font-weight: 500;
}

#logisticsDashboard .lg-dot {
    width: 6px;
    height: 6px;
    flex: 0 0 6px;
    border-radius: 50%;
    background: #57c875;
    box-shadow: 0 0 0 3px rgba(87,200,117,.08);
}

#logisticsDashboard .lg-status-separator {
    height: 13px;
    width: 1px;
    background: rgba(255,255,255,.14);
}

#logisticsDashboard .lg-hero-divider {
    height: 76px;
    width: 1px;
    background: linear-gradient(
        to bottom,
        transparent,
        rgba(255,255,255,.13) 18%,
        rgba(255,255,255,.13) 82%,
        transparent
    );
}

#logisticsDashboard .lg-hero-meta {
    display: grid;
    grid-template-columns: repeat(3,minmax(0,1fr));
    align-items: center;
    min-width: 0;
}

#logisticsDashboard .lg-hero-stat {
    min-width: 0;
    padding: 8px 22px;
}

#logisticsDashboard .lg-hero-stat:first-child {
    padding-left: 0;
}

#logisticsDashboard .lg-hero-stat:last-child {
    padding-right: 0;
}

#logisticsDashboard .lg-hero-stat + .lg-hero-stat {
    border-left: 1px solid rgba(255,255,255,.11);
}

#logisticsDashboard .lg-hero-stat-label {
    display: flex;
    gap: 7px;
    align-items: center;
    color: rgba(255,255,255,.43);
    font-size: 6.4px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
}

#logisticsDashboard .lg-hero-stat-label svg {
    width: 13px;
    height: 13px;
    color: #dca01c;
}

#logisticsDashboard
.lg-hero-stat:nth-child(3)
.lg-hero-stat-label svg {
    color: #9da9b4;
}

#logisticsDashboard .lg-hero-stat-copy {
    margin: 7px 0 0 20px;
}

#logisticsDashboard .lg-hero-stat-value {
    margin: 0;
    color: #fff;
    font-size: 15px;
    line-height: 1;
    font-weight: 700;
    letter-spacing: -.03em;
    white-space: nowrap;
}

#logisticsDashboard .lg-hero-stat-sub {
    margin: 5px 0 0;
    font-size: 6.4px;
    color: rgba(255,255,255,.5);
    white-space: nowrap;
}

/* KPI CARDS */

#logisticsDashboard .lg-kpis {
    display: grid;
    grid-template-columns: repeat(5,minmax(0,1fr));
    gap: 12px;
    margin-top: 14px;
}

#logisticsDashboard .lg-kpi {
    position: relative;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    min-width: 0;
    min-height: 84px;
    padding: 13px;
    border: 1px solid #e8e1d8;
    border-radius: 17px;
    background: #fff;
    box-shadow: 0 5px 18px rgba(37,29,20,.025);
    transition:
        transform .18s cubic-bezier(.22,1,.36,1),
        border-color .18s ease,
        box-shadow .18s ease;
}

#logisticsDashboard .lg-kpi:hover {
    transform: translateY(-2px);
    border-color: #d8ccb9;
    box-shadow: 0 14px 30px rgba(43,33,21,.055);
}

#logisticsDashboard .lg-kpi-name {
    font-size: 10.5px;
    line-height: 1.4;
    font-weight: 600;
    color: #6f675e;
}

#logisticsDashboard .lg-kpi-value {
    margin: 9px 0 0;
    font-size: 24px;
    line-height: 1;
    font-weight: 700;
    letter-spacing: -.045em;
    color: #211d18;
}

#logisticsDashboard .lg-kpi-sub {
    margin: 10px 0 0;
    font-size: 9px;
    line-height: 1.45;
    color: #85807a;
}

#logisticsDashboard .lg-kpi-icon {
    display: grid;
    place-items: center;
    flex: 0 0 42px;
    width: 42px;
    height: 42px;
    border-radius: 12px;
}

#logisticsDashboard .lg-kpi-icon svg {
    height: 18px;
    width: 18px;
}

/* CONTENT PANELS */

#logisticsDashboard .lg-panel {
    overflow: hidden;
    min-width: 0;
    border: 1px solid #e8dfd2;
    border-radius: 17px;
    background: #fff;
    box-shadow: 0 6px 18px rgba(33,24,14,.03);
}

#logisticsDashboard .lg-panel-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 13px 15px;
    border-bottom: 1px solid #eee8df;
}

#logisticsDashboard .lg-panel-title {
    font-size: 17px;
    line-height: 1.25;
    font-weight: 650;
    letter-spacing: -.03em;
    color: #28221b;
}

#logisticsDashboard .lg-panel-sub {
    margin-top: 4px;
    font-size: 10px;
    line-height: 1.5;
    color: #887f75;
}

#logisticsDashboard .lg-panel-pill {
    flex-shrink: 0;
    border: 1px solid #eee0c6;
    border-radius: 999px;
    background: #fffaf1;
    padding: 7px 12px;
    color: #a47119;
    font-size: 9px;
    font-weight: 600;
}

/* OPERATIONS PIPELINE */

#logisticsDashboard .lg-main-panels {
    display: grid;
    grid-template-columns: repeat(2,minmax(0,1fr));
    gap: 12px;
    margin-top: 13px;
    align-items: stretch;
}

#logisticsDashboard .lg-stage-grid {
    display: grid;
    grid-template-columns: repeat(2,minmax(0,1fr));
    gap: 8px;
    padding: 13px;
}

#logisticsDashboard .lg-stage {
    display: flex;
    gap: 10px;
    align-items: center;
    min-width: 0;
    padding: 10px;
    border: 1px solid #eee8df;
    border-radius: 10px;
    background: #fff;
    transition:
        background .15s ease,
        border-color .15s ease,
        transform .15s ease;
}

#logisticsDashboard .lg-stage:hover {
    background: #fffaf1;
    border-color: #ddcbab;
    transform: translateY(-1px);
}

#logisticsDashboard .lg-stage-step {
    display: grid;
    place-items: center;
    flex: 0 0 28px;
    width: 28px;
    height: 28px;
    border-radius: 9px;
    background: #fff5e5;
    color: #ad7516;
    font-size: 8px;
    font-weight: 700;
}

#logisticsDashboard .lg-stage-name {
    font-size: 9px;
    font-weight: 600;
    color: #51483d;
    line-height: 1.4;
}

#logisticsDashboard .lg-stage-desc {
    margin-top: 3px;
    font-size: 7.5px;
    line-height: 1.35;
    color: #9d9286;
}

#logisticsDashboard .lg-stage-value {
    margin-left: auto;
    flex-shrink: 0;
    font-size: 17px;
    font-weight: 700;
    color: #211d18;
}

/* NETWORK PERFORMANCE */

#logisticsDashboard .lg-overview {
    padding: 13px;
}

#logisticsDashboard .lg-overview-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 12px 10px;
    border-bottom: 1px solid #eee8df;
    transition: background .15s ease;
}

#logisticsDashboard .lg-overview-row:hover {
    background: #fffaf2;
}

#logisticsDashboard .lg-overview-row:last-child {
    border-bottom: 0;
}

#logisticsDashboard .lg-overview-row strong {
    font-size: 15px;
    color: #29231c;
}

#logisticsDashboard .lg-overview-row p {
    font-size: 9px;
    font-weight: 600;
    color: #51483d;
}

#logisticsDashboard .lg-overview-row small {
    display: block;
    margin-top: 3px;
    font-size: 7.5px;
    color: #968b80;
}

/* LOWER PANELS */

#logisticsDashboard .lg-secondary-panels {
    display: grid;
    grid-template-columns:
        minmax(0,1.35fr)
        minmax(300px,.65fr);
    gap: 12px;
    margin-top: 13px;
}

#logisticsDashboard .lg-table-wrap {
    overflow-x: auto;
}

#logisticsDashboard .lg-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 690px;
    text-align: left;
}

#logisticsDashboard .lg-table th {
    background: #f7f8fa;
    padding: 11px 13px;
    color: #837b73;
    font-size: 8px;
    font-weight: 600;
    letter-spacing: .05em;
    text-transform: uppercase;
}

#logisticsDashboard .lg-table td {
    padding: 12px 13px;
    border-top: 1px solid #f0ebe4;
    color: #62594e;
    font-size: 9px;
    vertical-align: middle;
}

#logisticsDashboard .lg-table tr {
    transition: background .15s ease;
}

#logisticsDashboard .lg-table tbody tr:hover {
    background: #fffaf1;
}

#logisticsDashboard .lg-small-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 5px 8px;
    background: #fff6e5;
    color: #a96e05;
    font-size: 7px;
    font-weight: 650;
    text-transform: capitalize;
}

/* HUB TRANSFERS */

#logisticsDashboard .lg-transfer-list {
    padding: 13px;
    display: grid;
    gap: 9px;
}

#logisticsDashboard .lg-transfer {
    padding: 12px;
    border: 1px solid #eee8df;
    border-radius: 11px;
    background: #fff;
    transition:
        background .15s ease,
        border-color .15s ease;
}

#logisticsDashboard .lg-transfer:hover {
    background: #fffaf1;
    border-color: #ddcbab;
}

#logisticsDashboard .lg-text-link {
    font-size: 9px;
    color: #a96e05;
    font-weight: 650;
    transition: color .15s ease;
}

#logisticsDashboard .lg-text-link:hover {
    color: #825204;
    text-decoration: underline;
}

/* RESPONSIVE */

@media (max-width:1199px) {
    #logisticsDashboard .lg-kpis {
        grid-template-columns: repeat(3,minmax(0,1fr));
    }

    #logisticsDashboard .lg-main-panels,
    #logisticsDashboard .lg-secondary-panels {
        grid-template-columns: 1fr;
    }
}

@media (max-width:1099px) {
    #logisticsDashboard .lg-hero-layout {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    #logisticsDashboard .lg-hero-divider {
        display: none;
    }

    #logisticsDashboard .lg-hero-meta {
        padding-top: 13px;
        border-top: 1px solid rgba(255,255,255,.09);
    }
}

@media (max-width:639px) {
    #logisticsDashboard .lg-hero {
        padding: 17px 15px;
    }

    #logisticsDashboard .lg-hero-title {
        font-size: 21px;
    }

    #logisticsDashboard .lg-wave,
    #logisticsDashboard .lg-wave .fi {
        width: 22px;
        height: 22px;
        flex-basis: 22px;
        font-size: 20px;
    }

    #logisticsDashboard .lg-hero-meta {
        grid-template-columns: repeat(3,minmax(0,1fr));
    }

    #logisticsDashboard .lg-hero-stat {
        padding: 8px;
    }

    #logisticsDashboard .lg-hero-stat-copy {
        margin-left: 0;
    }

    #logisticsDashboard .lg-hero-stat-value {
        font-size: 12px;
    }

    #logisticsDashboard .lg-kpis {
        grid-template-columns: repeat(2,minmax(0,1fr));
    }

    #logisticsDashboard .lg-kpi {
        padding: 12px;
    }

    #logisticsDashboard .lg-kpi-name {
        font-size: 9px;
    }

    #logisticsDashboard .lg-kpi-value {
        font-size: 21px;
    }

    #logisticsDashboard .lg-kpi-icon {
        width: 33px;
        height: 33px;
        flex-basis: 33px;
    }

    #logisticsDashboard .lg-stage-grid {
        grid-template-columns: 1fr;
    }
}

@media (prefers-reduced-motion: reduce) {
    #logisticsDashboard .lg-kpi,
    #logisticsDashboard .lg-stage,
    #logisticsDashboard .lg-transfer {
        transition: none;
    }

    #logisticsDashboard .lg-kpi:hover,
    #logisticsDashboard .lg-stage:hover {
        transform: none;
    }
}
</style>
@endpush

@section('content')

<div
    id="logisticsDashboard"
    class="lg-dashboard mx-auto w-full space-y-4"
>

    @if(session('success'))
        <div
            class="rounded-xl border border-[#bfe0cd] bg-[#f1faf5] px-4 py-3 text-[10px] text-[#327a53]"
            role="status"
        >
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div
            class="rounded-xl border border-[#efc6c6] bg-[#fff4f4] px-4 py-3 text-[10px] text-[#b53e3e]"
            role="alert"
        >
            {{ $errors->first() }}
        </div>
    @endif

    {{-- SELLER-STYLE GREETING HERO --}}

    <section
        class="lg-hero"
        aria-label="Logistics dashboard welcome"
    >
        <div class="lg-hero-layout">

            <div class="min-w-0">

                <h2 class="lg-hero-title">
                    <span id="lgGreeting">
                        Good day, {{ $isHubOperator ? 'Hub Operator' : 'Logistics Team' }}!
                    </span>

                    <span class="lg-wave" aria-hidden="true">
                        <i class="fi fi-ts-hand-wave"></i>
                    </span>
                </h2>

                <p class="lg-hero-sub">
                    Here’s what’s happening across your Logistics network today.
                </p>

                <div class="lg-hero-status">
                    <span class="lg-dot" aria-hidden="true"></span>

                    <span>
                        {{ $isHubOperator ? 'Hub Operations Active' : 'Network Active' }}
                    </span>

                    <span
                        class="lg-status-separator"
                        aria-hidden="true"
                    ></span>

                    <span>{{ $logisticsName }}</span>
                </div>

            </div>

            <div class="lg-hero-divider" aria-hidden="true"></div>

            {{-- TIME / DATE / WEATHER --}}

            <div class="lg-hero-meta">

                {{-- TIME --}}

                <div class="lg-hero-stat">
                    <div class="lg-hero-stat-label">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                            aria-hidden="true"
                        >
                            <circle cx="12" cy="12" r="8.5"/>
                            <path d="M12 7.5v5l3 2"/>
                        </svg>
                        Time
                    </div>

                    <div class="lg-hero-stat-copy">
                        <p
                            id="lgCurrentTime"
                            class="lg-hero-stat-value"
                        >
                            {{ now('Asia/Manila')->format('h:i A') }}
                        </p>

                        <p class="lg-hero-stat-sub">
                            Asia/Manila
                        </p>
                    </div>
                </div>

                {{-- DATE --}}

                <div class="lg-hero-stat">
                    <div class="lg-hero-stat-label">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                            aria-hidden="true"
                        >
                            <path d="M7 3v3M17 3v3"/>
                            <rect x="4" y="5" width="16" height="15" rx="2"/>
                            <path d="M4 10h16"/>
                        </svg>
                        Date
                    </div>

                    <div class="lg-hero-stat-copy">
                        <p
                            id="lgCurrentDay"
                            class="lg-hero-stat-value"
                        >
                            {{ now('Asia/Manila')->format('l') }}
                        </p>

                        <p
                            id="lgCurrentDate"
                            class="lg-hero-stat-sub"
                        >
                            {{ now('Asia/Manila')->format('F d, Y') }}
                        </p>
                    </div>
                </div>

                {{-- LIVE WEATHER --}}

                <div class="lg-hero-stat">

                    <div class="lg-hero-stat-label">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                            aria-hidden="true"
                        >
                            <path
                                d="M7.5 17.5h9.5a4.5 4.5 0 0 0 .2-9 6.2 6.2 0 0 0-11.6 1.6A3.8 3.8 0 0 0 7.5 17.5Z"
                            />
                        </svg>

                        Weather
                    </div>

                    <div
                        class="lg-hero-stat-copy"
                        aria-live="polite"
                    >
                        <p
                            id="lgWeatherTemp"
                            class="lg-hero-stat-value"
                        >
                            —
                        </p>

                        <p
                            id="lgWeatherCondition"
                            class="lg-hero-stat-sub"
                        >
                            Loading weather…
                        </p>
                    </div>

                </div>

            </div>

        </div>
    </section>

    {{-- KPI CARDS --}}

    @php
        $kpis = [
            [
                'name' => 'Active Riders',
                'value' => $stats['active_riders'],
                'sub' => $stats['online_riders'].' online now',
                'href' => route('logistics.rider-management'),
                'icon' => 'rider',
                'bg' => '#f4f8fd',
                'color' => '#3f78ad',
            ],
            [
                'name' => 'Active Facilities',
                'value' => $stats['active_facilities'],
                'sub' => 'Municipal sorting hubs',
                'href' => route($isHubOperator ? 'logistics.hub-transfers' : 'logistics.facilities.index'),
                'icon' => 'hub',
                'bg' => '#fff9ef',
                'color' => '#c3881f',
            ],
            [
                'name' => 'Active Deliveries',
                'value' => $stats['active_deliveries'],
                'sub' => 'Pickup and last mile',
                'href' => route('logistics.delivery-monitoring'),
                'icon' => 'truck',
                'bg' => '#f4faf7',
                'color' => '#4b8666',
            ],
            [
                'name' => 'Delivered Today',
                'value' => $stats['delivered_today'],
                'sub' => $stats['total_delivered'].' total completed',
                'href' => route('logistics.reports'),
                'icon' => 'check',
                'bg' => '#f4faf7',
                'color' => '#4b8666',
            ],
            [
                'name' => 'Rider Applications',
                'value' => $stats['pending_rider_applications'],
                'sub' => 'Awaiting approval',
                'href' => route('logistics.rider-applications'),
                'icon' => 'application',
                'bg' => '#fffaf1',
                'color' => '#c3881f',
            ],
        ];

        if ($isHubOperator) {
            $kpis = array_values(array_filter($kpis, fn ($kpi) => $kpi['icon'] !== 'application'));
        }
    @endphp

    <section class="lg-kpis" aria-label="Logistics KPIs">

        @foreach($kpis as $kpi)

            <a href="{{ $kpi['href'] }}" class="lg-kpi group">

                <div class="min-w-0">
                    <p class="lg-kpi-name">
                        {{ $kpi['name'] }}
                    </p>

                    <p class="lg-kpi-value">
                        {{ number_format($kpi['value']) }}
                    </p>

                    <p class="lg-kpi-sub">
                        {{ $kpi['sub'] }}
                    </p>
                </div>

                <span
                    class="lg-kpi-icon"
                    style="background:{{ $kpi['bg'] }};color:{{ $kpi['color'] }};"
                    aria-hidden="true"
                >

                    @switch($kpi['icon'])

                        @case('rider')
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

                        @case('hub')
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M3 21h18M5 21V8l7-4 7 4v13M8 12h3M13 12h3M8 16h8"/>
                            </svg>
                            @break

                        @case('truck')
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M3 6h11v10H3zM14 10h4l3 3v3h-7"/>
                                <circle cx="7" cy="18" r="2"/>
                                <circle cx="18" cy="18" r="2"/>
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

                        @default
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect x="5" y="4" width="14" height="16" rx="2"/>
                                <path d="M9 9h6M9 13h6M9 17h4"/>
                            </svg>

                    @endswitch

                </span>

            </a>

        @endforeach

    </section>

    {{-- MAIN ANALYTICS PANELS --}}

    <div class="lg-main-panels">

        {{-- OPERATIONS PIPELINE --}}

        <section class="lg-panel">

            <header class="lg-panel-head">

                <div>
                    <h3 class="lg-panel-title">
                        Operations Pipeline
                    </h3>

                    <p class="lg-panel-sub">
                        Real-time parcel workload across every Logistics stage.
                    </p>
                </div>

                <span class="lg-panel-pill">
                    8 Stages
                </span>

            </header>

            <div class="lg-stage-grid">

                @foreach($pipeline as $stage)

                    <a
                        class="lg-stage"
                        href="{{ route($stage['route']) }}"
                        title="{{ $stage['description'] }}"
                    >

                        <span class="lg-stage-step">
                            {{ $stage['step'] }}
                        </span>

                        <div class="min-w-0">
                            <p class="lg-stage-name">
                                {{ $stage['label'] }}
                            </p>

                            <p class="lg-stage-desc">
                                {{ $stage['description'] }}
                            </p>
                        </div>

                        <span class="lg-stage-value">
                            {{ number_format($stage['value']) }}
                        </span>

                    </a>

                @endforeach

            </div>

        </section>

        {{-- NETWORK PERFORMANCE --}}

        <section class="lg-panel">

            <header class="lg-panel-head">

                <div>
                    <h3 class="lg-panel-title">
                        Network Performance
                    </h3>

                    <p class="lg-panel-sub">
                        Today's fulfillment and municipal hub activity.
                    </p>
                </div>

                <span class="lg-panel-pill">
                    Live Operations
                </span>

            </header>

            <div class="lg-overview">

                <a
                    class="lg-overview-row"
                    href="{{ route('logistics.delivery-monitoring') }}"
                >
                    <div>
                        <p>Active Deliveries</p>
                        <small>Across pickup and last-mile stages</small>
                    </div>

                    <strong>
                        {{ number_format($stats['active_deliveries']) }}
                    </strong>
                </a>

                <a
                    class="lg-overview-row"
                    href="{{ route('logistics.reports') }}"
                >
                    <div>
                        <p>Delivered Today</p>

                        <small>
                            {{ number_format($stats['total_delivered']) }}
                            deliveries completed overall
                        </small>
                    </div>

                    <strong>
                        {{ number_format($stats['delivered_today']) }}
                    </strong>
                </a>

                <a
                    class="lg-overview-row"
                    href="{{ route('logistics.hub-transfers') }}"
                >
                    <div>
                        <p>Inter-Hub Transfers</p>
                        <small>Currently in transit</small>
                    </div>

                    <strong>
                        {{ number_format($stats['hub_transfer_in_transit']) }}
                    </strong>
                </a>

                <a
                    class="lg-overview-row"
                    href="{{ route('logistics.last-mile-assignment') }}"
                >
                    <div>
                        <p>Last-Mile Deliveries</p>
                        <small>Currently out for delivery</small>
                    </div>

                    <strong>
                        {{ number_format($stats['last_mile_out_for_delivery']) }}
                    </strong>
                </a>

                @if(! $isHubOperator)
                <a
                    class="lg-overview-row"
                    href="{{ route('logistics.rider-applications') }}"
                >
                    <div>
                        <p>Pending Rider Applications</p>
                        <small>Awaiting network approval</small>
                    </div>

                    <strong>
                        {{ number_format($stats['pending_rider_applications']) }}
                    </strong>
                </a>
                @endif

            </div>

        </section>

    </div>

    {{-- RECENT ACTIVITY PANELS --}}

    <div class="lg-secondary-panels">

        {{-- RECENT ORDERS --}}

        <section class="lg-panel">

            <header class="lg-panel-head">

                <div>
                    <h3 class="lg-panel-title">
                        Recent Logistics Orders
                    </h3>

                    <p class="lg-panel-sub">
                        Latest orders moving through the network.
                    </p>
                </div>

                <a
                    class="lg-text-link"
                    href="{{ route('logistics.delivery-monitoring') }}"
                >
                    View Monitoring →
                </a>

            </header>

            <div class="lg-table-wrap">

                <table class="lg-table">

                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Seller / Buyer</th>
                            <th>Pickup Rider</th>
                            <th>Parcel Stage</th>
                            <th>Last Mile</th>
                            <th>Updated</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($recentOrders as $order)

                            @php
                                $lastMile = $lastMileByOrder->get($order->id);

                                $parcelStatus =
                                    $order->logisticsParcel?->status;

                                $lastMileName = $lastMile
                                    ? trim(
                                        $lastMile->first_name
                                        . ' '
                                        . $lastMile->last_name
                                    )
                                    : null;
                            @endphp

                            <tr>

                                <td>
                                    <strong class="text-[#29231c]">
                                        {{ $order->order_number }}
                                    </strong>

                                    <div class="mt-1">
                                        <span class="lg-small-badge">
                                            {{ str_replace('_', ' ', $order->status) }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <strong class="text-[#51483d]">
                                        {{ $order->seller?->store_name ?: 'SARI Seller' }}
                                    </strong>

                                    <div class="mt-1 text-[#918779]">
                                        Buyer: {{ $order->buyer_name }}
                                    </div>
                                </td>

                                <td>
                                    {{ $order->courier_name ?: 'Unassigned' }}
                                </td>

                                <td>
                                    <span class="lg-small-badge">
                                        {{
                                            $parcelStatus
                                                ? str_replace('_', ' ', $parcelStatus)
                                                : '—'
                                        }}
                                    </span>
                                </td>

                                <td>
                                    {{ $lastMileName ?: '—' }}

                                    @if($lastMile)
                                        <div class="mt-1 text-[#918779]">
                                            {{ str_replace('_', ' ', $lastMile->status) }}
                                        </div>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap">
                                    {{ $order->updated_at?->diffForHumans() }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="6"
                                    class="py-10 text-center text-[#918779]"
                                >
                                    No Logistics orders yet.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

        {{-- HUB TRANSFERS --}}

        <section class="lg-panel">

            <header class="lg-panel-head">

                <div>
                    <h3 class="lg-panel-title">
                        Recent Hub Transfers
                    </h3>

                    <p class="lg-panel-sub">
                        Latest line-haul activity.
                    </p>
                </div>

                <a
                    class="lg-text-link"
                    href="{{ route('logistics.hub-transfers') }}"
                >
                    View all →
                </a>

            </header>

            <div class="lg-transfer-list">

                @forelse($recentTransfers as $transfer)

                    <article class="lg-transfer">

                        <div class="flex items-start justify-between gap-2">

                            <strong class="text-[10px] text-[#3f372e]">
                                {{ $transfer->transfer_code }}
                            </strong>

                            <span class="lg-small-badge">
                                {{ str_replace('_', ' ', $transfer->status) }}
                            </span>

                        </div>

                        <p class="mt-2 text-[9px] text-[#817769]">
                            {{ $transfer->origin_name }}
                            →
                            {{ $transfer->destination_name }}
                        </p>

                        <p class="mt-2 text-[8px] text-[#978d80]">

                            {{ $transfer->vehicle_type }}

                            @if($transfer->vehicle_plate)
                                · {{ $transfer->vehicle_plate }}
                            @endif

                            · {{ $transfer->parcel_count }}

                            {{
                                $transfer->parcel_count == 1
                                    ? 'parcel'
                                    : 'parcels'
                            }}

                        </p>

                    </article>

                @empty

                    <p
                        class="rounded-xl border border-dashed border-[#e6ddd0] px-4 py-9 text-center text-[9px] text-[#918677]"
                    >
                        No hub transfers yet.
                    </p>

                @endforelse

            </div>

        </section>

    </div>

</div>

@endsection

@push('scripts')

<script>
(() => {
    'use strict';

    /*
    |--------------------------------------------------------------------------
    | LIVE PHILIPPINE DATE / TIME
    |--------------------------------------------------------------------------
    */

    const time = document.getElementById('lgCurrentTime');
    const day = document.getElementById('lgCurrentDay');
    const date = document.getElementById('lgCurrentDate');
    const greeting = document.getElementById('lgGreeting');

    const weatherTemp = document.getElementById('lgWeatherTemp');
    const weatherCondition = document.getElementById('lgWeatherCondition');

    const zone = 'Asia/Manila';

    const operator = {{ $isHubOperator ? 'true' : 'false' }};

    if (time && day && date && greeting) {

        const timeFormatter = new Intl.DateTimeFormat('en-US', {
            timeZone: zone,
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });

        const dayFormatter = new Intl.DateTimeFormat('en-US', {
            timeZone: zone,
            weekday: 'long'
        });

        const dateFormatter = new Intl.DateTimeFormat('en-US', {
            timeZone: zone,
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });

        const hourFormatter = new Intl.DateTimeFormat('en-GB', {
            timeZone: zone,
            hour: 'numeric',
            hourCycle: 'h23'
        });

        function refreshClock() {

            const now = new Date();

            time.textContent = timeFormatter.format(now);
            day.textContent = dayFormatter.format(now);
            date.textContent = dateFormatter.format(now);

            const hour = Number(hourFormatter.format(now));

            const salutation = hour < 12
                ? 'Good morning'
                : hour < 18
                    ? 'Good afternoon'
                    : 'Good evening';

            greeting.textContent =
                salutation
                + (
                    operator
                        ? ', Hub Operator!'
                        : ', Logistics Team!'
                );
        }

        refreshClock();

        window.setInterval(refreshClock, 60000);
    }

    /*
    |--------------------------------------------------------------------------
    | LIVE WEATHER — SAME SOURCE AS SELLER
    |--------------------------------------------------------------------------
    |
    | Open-Meteo API.
    | Uses browser location when allowed.
    | Fallback location: Manila, Philippines.
    | Cache lifetime: 10 minutes.
    */

    const weatherDescriptions = {
        0: 'Clear sky',
        1: 'Mainly clear',
        2: 'Partly cloudy',
        3: 'Overcast',
        45: 'Foggy',
        48: 'Rime fog',
        51: 'Light drizzle',
        53: 'Drizzle',
        55: 'Heavy drizzle',
        56: 'Freezing drizzle',
        57: 'Heavy freezing drizzle',
        61: 'Light rain',
        63: 'Rain',
        65: 'Heavy rain',
        66: 'Freezing rain',
        67: 'Heavy freezing rain',
        71: 'Light snow',
        73: 'Snow',
        75: 'Heavy snow',
        77: 'Snow grains',
        80: 'Rain showers',
        81: 'Heavy showers',
        82: 'Violent showers',
        85: 'Snow showers',
        86: 'Heavy snow showers',
        95: 'Thunderstorm',
        96: 'Storm with hail',
        99: 'Severe storm'
    };

    const cacheKey = 'sari:logistics-dashboard-weather-v1';

    const cacheAge = 10 * 60 * 1000;

    const manila = {
        latitude: 14.5995,
        longitude: 120.9842
    };

    /*
     * Update weather UI.
     */

    function showWeather(snapshot) {

        if (!weatherTemp || !weatherCondition) {
            return;
        }

        weatherTemp.textContent =
            `${snapshot.temperature}°C`;

        weatherCondition.textContent = snapshot.label
            ? `${snapshot.label} · ${snapshot.condition}`
            : snapshot.condition;
    }

    /*
     * Read valid session cache.
     */

    function readWeatherCache() {

        try {

            const stored = window.sessionStorage.getItem(cacheKey);

            if (!stored) {
                return null;
            }

            const snapshot = JSON.parse(stored);

            if (
                !Number.isFinite(Number(snapshot?.temperature))
                || !snapshot?.condition
                || !snapshot?.label
                || !Number.isFinite(Number(snapshot?.savedAt))
                || Date.now() - Number(snapshot.savedAt) > cacheAge
                || Number(snapshot.savedAt) > Date.now()
            ) {
                return null;
            }

            return snapshot;

        } catch (_) {

            return null;

        }
    }

    /*
     * Request actual weather data.
     */

    async function fetchWeather(latitude, longitude, label) {

        const controller = new AbortController();

        const timeout = window.setTimeout(
            () => controller.abort(),
            8000
        );

        try {

            const parameters = new URLSearchParams({
                latitude: String(latitude),
                longitude: String(longitude),
                current: 'temperature_2m,weather_code',
                timezone: 'auto'
            });

            const response = await fetch(
                `https://api.open-meteo.com/v1/forecast?${parameters}`,
                {
                    signal: controller.signal
                }
            );

            if (!response.ok) {
                throw new Error('Weather request failed');
            }

            const result = await response.json();

            const temperature = Number(
                result?.current?.temperature_2m
            );

            const code = Number(
                result?.current?.weather_code
            );

            if (
                !Number.isFinite(temperature)
                || !Number.isFinite(code)
                || !(code in weatherDescriptions)
            ) {
                throw new Error('Invalid current weather data');
            }

            const snapshot = {
                temperature: Math.round(temperature),
                condition: weatherDescriptions[code],
                label,
                savedAt: Date.now()
            };

            showWeather(snapshot);

            try {

                window.sessionStorage.setItem(
                    cacheKey,
                    JSON.stringify(snapshot)
                );

            } catch (_) {
                // Storage may be unavailable in private browsing.
            }

        } catch (_) {

            weatherTemp.textContent = '—';

            weatherCondition.textContent =
                'Weather unavailable';

        } finally {

            window.clearTimeout(timeout);

        }
    }

    /*
     * Initialize weather on page load.
     */

    function initializeWeather() {

        if (!weatherTemp || !weatherCondition) {
            return;
        }

        const cached = readWeatherCache();

        if (cached) {
            showWeather(cached);
            return;
        }

        /*
         * Fallback to Manila if geolocation isn't supported.
         */

        if (!navigator.geolocation) {

            fetchWeather(
                manila.latitude,
                manila.longitude,
                'Manila'
            );

            return;
        }

        /*
         * Browser will ask for location permission if needed.
         */

        navigator.geolocation.getCurrentPosition(

            position => {

                fetchWeather(
                    position.coords.latitude,
                    position.coords.longitude,
                    'Your area'
                );

            },

            () => {

                fetchWeather(
                    manila.latitude,
                    manila.longitude,
                    'Manila'
                );

            },

            {
                enableHighAccuracy: false,
                timeout: 3000,
                maximumAge: 30 * 60 * 1000
            }

        );
    }

    initializeWeather();

})();
</script>

@endpush