@extends('layouts.logistics')

@section('title', 'Logistics Dashboard | SARI')
@section('page-title', 'Dashboard Overview')

@php
    $deliveryTrend = $deliveryTrend ?? [];
    $transferReceiptTrend = $transferReceiptTrend ?? [];
    $flowBreakdown = $flowBreakdown ?? [];
    $flowTotal = (int) ($flowTotal ?? 0);
    $recentTransfers = $recentTransfers ?? collect();
    $recentOrders = $recentOrders ?? collect();
    $pipeline = $pipeline ?? [];

    $branchName = $hubFacility?->name ?: $logisticsName;
    $branchLocation = collect([
        $hubFacility?->municipality_name,
        $hubFacility?->province_name,
    ])->filter()->implode(', ');
    $isMainBranch = (bool) ($hubFacility?->is_main_branch ?? false);

    $flowPalette = [
        '#D79A18',
        '#2F6FA3',
        '#6E8B74',
        '#8767A8',
        '#D06D56',
        '#3C4B57',
    ];

    $flowSegments = [];
    $flowCursor = 0.0;
    $flowLegend = collect();

    foreach ($flowBreakdown as $index => $item) {
        $value = max(0, (int) ($item['value'] ?? 0));
        $percentage = $flowTotal > 0 ? ($value / $flowTotal) * 100 : 0;
        $start = $flowCursor;
        $end = $flowCursor + ($percentage * 3.6);
        $flowCursor = $end;
        $color = $flowPalette[$index % count($flowPalette)];

        if ($flowTotal > 0) {
            $flowSegments[] = sprintf('%s %.2fdeg %.2fdeg', $color, $start, $end);
        }

        $flowLegend->push([
            'label' => (string) ($item['label'] ?? 'Stage'),
            'value' => $value,
            'percentage' => $percentage,
            'color' => $color,
        ]);
    }

    $flowGradient = count($flowSegments)
        ? 'conic-gradient(' . implode(', ', $flowSegments) . ')'
        : 'conic-gradient(#e8e9ec 0deg 360deg)';

    $maxPipeline = max(1, collect($pipeline)->max('value') ?? 1);
@endphp

@push('styles')
<link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-thin-straight/css/uicons-thin-straight.css">
<style>
    .logistics-dashboard-shell {
        --ld-ink: #17191c;
        --ld-muted: #7c838e;
        --ld-line: #e6e9ed;
        --ld-soft: #f6f7f8;
        --ld-gold: #d69a17;
        --ld-gold-dark: #a86e05;
        --ld-green: #4d8b6a;
        --ld-blue: #4f82b6;
        --ld-red: #c86655;
        --ld-card-shadow: 0 14px 34px rgba(25, 31, 38, .055);
    }

    .ld-panel {
        border: 1px solid var(--ld-line);
        background: #fff;
        border-radius: 22px;
        box-shadow: var(--ld-card-shadow);
    }

    .ld-kpi {
        position: relative;
        min-height: 106px;
        overflow: hidden;
        border: 1px solid var(--ld-line);
        border-radius: 20px;
        background: #fff;
        padding: 16px 17px;
        box-shadow: 0 10px 26px rgba(25, 31, 38, .045);
        transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    .ld-kpi:hover {
        transform: translateY(-2px);
        border-color: #d8dce2;
        box-shadow: 0 16px 32px rgba(25, 31, 38, .075);
    }

    .ld-kpi-icon {
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        border-radius: 13px;
        background: #f7f7f7;
        color: #59616c;
    }

    .ld-kpi-icon.gold { background: #fff8e9; color: #b67b0e; }
    .ld-kpi-icon.blue { background: #f0f6fb; color: #4b7ba7; }
    .ld-kpi-icon.green { background: #f0f7f3; color: #4c8465; }
    .ld-kpi-icon.red { background: #fbf2f0; color: #b65e4e; }

    .ld-chart-empty {
        display: grid;
        min-height: 250px;
        place-items: center;
        color: #a0a6ae;
        font-size: 13px;
    }

    .ld-chart-tooltip {
        position: absolute;
        z-index: 5;
        display: none;
        min-width: 96px;
        pointer-events: none;
        border: 1px solid #e4e7eb;
        border-radius: 10px;
        background: rgba(20, 22, 25, .96);
        padding: 8px 10px;
        color: white;
        font-size: 11px;
        box-shadow: 0 12px 26px rgba(0,0,0,.16);
        transform: translate(-50%, -112%);
    }

    .ld-chart-toggle[aria-pressed="true"] {
        border-color: #d8dde4;
        background: #fff;
        color: #1d2228;
        box-shadow: 0 2px 7px rgba(18,24,31,.06);
    }

    .ld-table-row {
        transition: background .16s ease;
    }

    .ld-table-row:hover {
        background: #fafbfc;
    }


    /* Admin-matched compact executive hero */
    .ld-exec-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid #3f3a2c;
        border-radius: 18px;
        background: linear-gradient(112deg, #161913 0%, #202318 42%, #403a2a 74%, #756c51 100%);
        box-shadow: 0 12px 30px rgba(31, 29, 23, .10);
        padding: 18px 22px;
    }

    .ld-exec-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 88% 16%, rgba(231, 209, 163, .20), transparent 22%),
            radial-gradient(circle at 14% 85%, rgba(176, 142, 49, .10), transparent 24%);
        pointer-events: none;
    }

    .ld-exec-grid {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 18px;
        align-items: center;
    }

    .ld-exec-kicker-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .ld-exec-kicker {
        font-size: 7.5px;
        font-weight: 700;
        letter-spacing: .20em;
        text-transform: uppercase;
        color: #e5bf62;
    }

    .ld-exec-main-badge {
        display: inline-flex;
        align-items: center;
        min-height: 21px;
        border: 1px solid rgba(229,191,98,.24);
        border-radius: 999px;
        background: rgba(229,191,98,.08);
        padding: 0 8px;
        font-size: 6.5px;
        font-weight: 700;
        letter-spacing: .11em;
        text-transform: uppercase;
        color: #e5bf62;
    }

    .ld-exec-title {
        margin-top: 8px;
        display: inline-flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 7px;
        max-width: 100%;
        font-size: 24px;
        font-weight: 700;
        line-height: 1.12;
        letter-spacing: -.04em;
        color: #fff;
    }

    /* Exact Seller Flaticon wave implementation */
    .seller-greeting-wave.logistics-greeting-wave {
        display: inline-flex !important;
        width: 24px !important;
        height: 24px !important;
        flex: 0 0 24px !important;
        align-items: center !important;
        justify-content: center !important;
        align-self: center !important;
        margin: 0 0 0 1px !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        color: #efb43a !important;
        line-height: 1 !important;
        position: relative !important;
        top: 1px !important;
        overflow: visible !important;
    }

    .seller-greeting-wave.logistics-greeting-wave .fi {
        display: inline-flex !important;
        width: 24px !important;
        height: 24px !important;
        flex: 0 0 24px !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 !important;
        padding: 0 !important;
        font-size: 22px !important;
        font-style: normal !important;
        line-height: 1 !important;
        transform: none !important;
    }

    .seller-greeting-wave.logistics-greeting-wave .fi::before {
        display: block !important;
        margin: 0 !important;
        line-height: 1 !important;
    }

    .ld-exec-status {
        margin-top: 9px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 11px;
        font-size: 8px;
        line-height: 1.45;
        color: rgba(255,255,255,.64);
    }

    .ld-exec-status-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
    }

    .ld-exec-status-dot {
        width: 6px;
        height: 6px;
        flex: 0 0 6px;
        border-radius: 50%;
        background: #57c47c;
        box-shadow: 0 0 0 3px rgba(87,196,124,.10);
    }

    .ld-exec-side {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        align-items: center;
        gap: 0;
        width: 100%;
    }

    .ld-exec-stat {
        min-width: 0;
        padding: 5px 14px;
    }

    .ld-exec-stat:first-child { padding-left: 0; }
    .ld-exec-stat + .ld-exec-stat { border-left: 1px solid rgba(255,255,255,.12); }

    .ld-exec-stat-label {
        font-size: 7px;
        font-weight: 700;
        letter-spacing: .11em;
        text-transform: uppercase;
        color: rgba(255,255,255,.50);
    }

    .ld-exec-stat-row {
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
    }

    .ld-exec-stat-icon {
        display: inline-grid;
        width: 24px;
        height: 24px;
        flex: 0 0 24px;
        place-items: center;
        border-radius: 999px;
        background: rgba(201,149,36,.14);
        color: #ebc35d;
    }

    .ld-exec-stat-icon.is-rider {
        background: rgba(89,149,202,.12);
        color: #80b9e8;
    }

    .ld-exec-stat-icon svg { width: 11px; height: 11px; }

    .ld-exec-stat-value {
        min-width: 0;
        font-size: 12.5px;
        font-weight: 700;
        line-height: 1.1;
        color: #fff;
        white-space: nowrap;
    }

    .ld-exec-stat-sub {
        margin-top: 2px;
        font-size: 7px;
        line-height: 1.35;
        color: rgba(255,255,255,.52);
        white-space: nowrap;
    }

    .ld-exec-rider-value {
        display: flex;
        align-items: baseline;
        gap: 4px;
        white-space: nowrap;
    }

    .ld-exec-rider-value small {
        font-size: 7px;
        font-weight: 500;
        color: rgba(255,255,255,.50);
    }

    .ld-exec-online { color: #80d2a2; }

    @media (min-width: 1280px) {
        .ld-exec-grid { grid-template-columns: minmax(0, 1.05fr) minmax(480px, .95fr); }
    }

    @media (min-width: 768px) and (max-width: 1279px) {
        .ld-exec-hero { padding: 18px 20px; }
    }

    @media (max-width: 767px) {
        .ld-exec-hero { padding: 18px 16px; }
        .ld-exec-title { font-size: 21px; gap: 6px; }
         .seller-greeting-wave.logistics-greeting-wave,
        .seller-greeting-wave.logistics-greeting-wave .fi {
            width: 22px !important;
            height: 22px !important;
            flex-basis: 22px !important;
        }
        .seller-greeting-wave.logistics-greeting-wave .fi { font-size: 20px !important; }
        .ld-exec-side { grid-template-columns: 1fr; gap: 0; margin-top: 2px; }
        .ld-exec-stat, .ld-exec-stat:first-child { padding: 9px 0; }
        .ld-exec-stat + .ld-exec-stat { border-left: 0; border-top: 1px solid rgba(255,255,255,.10); }
        .ld-exec-stat-row { margin-top: 4px; }
    }
</style>
@endpush

@section('content')
<div class="logistics-dashboard-shell mx-auto w-full max-w-[1900px] space-y-4 sm:space-y-5">

    {{-- EXECUTIVE HEADER — ADMIN-MATCHED COMPACT DESIGN --}}
    <section class="ld-exec-hero text-white">
        <div class="ld-exec-grid">
            <div class="min-w-0">
                <div class="ld-exec-kicker-row">
                    <p class="ld-exec-kicker">SARI Logistics</p>
                    @if($isMainBranch)
                        <span class="ld-exec-main-badge">Main Branch</span>
                    @endif
                </div>

                <h2 class="ld-exec-title">
                    <span id="logisticsDashboardGreeting">Good morning, Logistics!</span>
                    <span class="seller-greeting-wave logistics-greeting-wave" aria-hidden="true">
                        <i class="fi fi-ts-hand-wave"></i>
                    </span>
                </h2>

                <div class="ld-exec-status">
                    <span class="ld-exec-status-item">
                        <span class="ld-exec-status-dot"></span>
                        Branch operations active
                    </span>
                    @if($branchLocation)
                        <span class="ld-exec-status-item">
                            <svg viewBox="0 0 24 24" class="h-3 w-3 text-[#e0aa31]" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M12 21s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12Z"></path>
                                <circle cx="12" cy="9" r="2.2"></circle>
                            </svg>
                            {{ $branchLocation }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="ld-exec-side">
                <div class="ld-exec-stat">
                    <p class="ld-exec-stat-label">Local Time</p>
                    <div class="ld-exec-stat-row">
                        <span class="ld-exec-stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="12" cy="12" r="8"></circle>
                                <path d="M12 7v5l3 2"></path>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p id="logisticsDashboardClock" class="ld-exec-stat-value">--:-- --</p>
                            <p class="ld-exec-stat-sub">Asia/Manila</p>
                        </div>
                    </div>
                </div>

                <div class="ld-exec-stat">
                    <p class="ld-exec-stat-label">Date</p>
                    <div class="ld-exec-stat-row">
                        <span class="ld-exec-stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="4" y="5" width="16" height="15" rx="2"></rect>
                                <path d="M8 3v4M16 3v4M4 9h16"></path>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p id="logisticsDashboardDay" class="ld-exec-stat-value">—</p>
                            <p id="logisticsDashboardDate" class="ld-exec-stat-sub">—</p>
                        </div>
                    </div>
                </div>

                <div class="ld-exec-stat">
                    <p class="ld-exec-stat-label">Rider Availability</p>
                    <div class="ld-exec-stat-row">
                        <span class="ld-exec-stat-icon is-rider">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="12" cy="8" r="3"></circle>
                                <path d="M5 20a7 7 0 0 1 14 0"></path>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <div class="ld-exec-rider-value">
                                <span class="ld-exec-stat-value">{{ $stats['online_riders'] ?? 0 }}</span>
                                <small>of {{ $stats['active_riders'] ?? 0 }} active</small>
                            </div>
                            <p class="ld-exec-stat-sub ld-exec-online">{{ $stats['online_rate'] ?? 0 }}% online</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- NETWORK SETUP WARNING --}}
    @if(! $isHubOperator && ($stats['network_facilities'] ?? 0) < 2)
        <section class="flex flex-col gap-4 rounded-[18px] border border-[#f0d695] bg-[#fffaf0] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[12px] font-bold text-[#7f5710]">Complete the sorting network before routed hub-to-hub operations</p>
                <p class="mt-1 text-[10.5px] leading-4 text-[#9a762f]">
                    Register at least two active facilities so SARI can select a distinct origin hub and destination hub.
                </p>
            </div>
            <a href="{{ route('logistics.facilities.index') }}" class="inline-flex h-9 shrink-0 items-center justify-center rounded-[10px] bg-[#202224] px-4 text-[10px] font-semibold text-white transition hover:bg-black">
                Manage network
            </a>
        </section>
    @endif

    {{-- KPI ROW --}}
    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        <a href="{{ route('logistics.rider-management') }}" class="ld-kpi group">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold text-[#665f57]">Active Riders</p>
                    <p class="mt-2 text-[26px] font-bold leading-none tracking-[-.04em] text-[#16181b]">{{ number_format($stats['active_riders'] ?? 0) }}</p>
                    <p class="mt-2 text-[10px] text-[#7a838d]">{{ $stats['online_riders'] ?? 0 }} online · {{ $stats['busy_riders'] ?? 0 }} assigned</p>
                </div>
                <span class="ld-kpi-icon blue">
                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3"/><path d="M5 20a7 7 0 0 1 14 0"/></svg>
                </span>
            </div>
        </a>

        <a href="{{ route('logistics.delivery-monitoring') }}" class="ld-kpi group">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold text-[#665f57]">Active Deliveries</p>
                    <p class="mt-2 text-[26px] font-bold leading-none tracking-[-.04em] text-[#16181b]">{{ number_format($stats['active_deliveries'] ?? 0) }}</p>
                    <p class="mt-2 text-[10px] text-[#7a838d]">Live pickup and delivery jobs</p>
                </div>
                <span class="ld-kpi-icon green">
                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 17h2l2-6h10l2 6h2"/><circle cx="8" cy="18" r="2"/><circle cx="17" cy="18" r="2"/><path d="M8 11V7h7l2 4"/></svg>
                </span>
            </div>
        </a>

        <a href="{{ route('logistics.incoming-parcels') }}" class="ld-kpi group">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold text-[#665f57]">Hub Intake</p>
                    <p class="mt-2 text-[26px] font-bold leading-none tracking-[-.04em] text-[#16181b]">{{ number_format($stats['awaiting_intake'] ?? 0) }}</p>
                    <p class="mt-2 text-[10px] text-[#7a838d]">Parcels waiting for intake scan</p>
                </div>
                <span class="ld-kpi-icon gold">
                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7 12 3l8 4-8 4-8-4Z"/><path d="m4 7 8 4v10l-8-4V7Zm16 0-8 4v10l8-4V7Z"/></svg>
                </span>
            </div>
        </a>

        <a href="{{ route('logistics.parcel-sorting') }}" class="ld-kpi group">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold text-[#665f57]">Sorting Queue</p>
                    <p class="mt-2 text-[26px] font-bold leading-none tracking-[-.04em] text-[#16181b]">{{ number_format($stats['sorting_queue'] ?? 0) }}</p>
                    <p class="mt-2 text-[10px] text-[#7a838d]">{{ $stats['ready_for_transfer'] ?? 0 }} ready for linehaul</p>
                </div>
                <span class="ld-kpi-icon blue">
                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 6h14M5 12h14M5 18h14"/><circle cx="8" cy="6" r="1" fill="currentColor"/><circle cx="14" cy="12" r="1" fill="currentColor"/><circle cx="10" cy="18" r="1" fill="currentColor"/></svg>
                </span>
            </div>
        </a>

        <a href="{{ route('logistics.hub-transfers') }}" class="ld-kpi group">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold text-[#665f57]">Linehaul Active</p>
                    <p class="mt-2 text-[26px] font-bold leading-none tracking-[-.04em] text-[#16181b]">{{ number_format($stats['hub_transfer_active'] ?? 0) }}</p>
                    <p class="mt-2 text-[10px] text-[#7a838d]">{{ $stats['hub_transfer_received_today'] ?? 0 }} received today</p>
                </div>
                <span class="ld-kpi-icon red">
                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></svg>
                </span>
            </div>
        </a>

        <a href="{{ route('logistics.reports') }}" class="ld-kpi group">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold text-[#665f57]">Delivered Today</p>
                    <p class="mt-2 text-[26px] font-bold leading-none tracking-[-.04em] text-[#16181b]">{{ number_format($stats['delivered_today'] ?? 0) }}</p>
                    <p class="mt-2 text-[10px] text-[#4d8b6a]">{{ number_format($stats['total_delivered'] ?? 0) }} lifetime deliveries</p>
                </div>
                <span class="ld-kpi-icon green">
                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>
                </span>
            </div>
        </a>
    </section>

    {{-- PRIMARY ANALYTICS --}}
    <section class="grid gap-4 xl:grid-cols-[.88fr_1.12fr]">
        {{-- FLOW BREAKDOWN --}}
        <article class="ld-panel overflow-hidden">
            <header class="flex items-start justify-between gap-4 border-b border-[#eceef1] px-5 py-4 sm:px-6">
                <div>
                    <h3 class="text-[16px] font-bold tracking-[-.025em] text-[#1b1e22]">Parcel Flow Breakdown</h3>
                    <p class="mt-1 text-[11px] text-[#8b929b]">Current workload across the selected branch journey.</p>
                </div>
                <a href="{{ route('logistics.parcel-scanner') }}" class="inline-flex h-8 items-center gap-1.5 rounded-[9px] border border-[#e5e8ec] bg-white px-3 text-[10px] font-semibold text-[#555d67] transition hover:border-[#d7aa4d] hover:text-[#9a670d]">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 8V4h4M16 4h4v4M20 16v4h-4M8 20H4v-4"/><path d="M8 8h8v8H8z"/></svg>
                    Scan Parcel
                </a>
            </header>

            <div class="grid gap-6 px-5 py-6 sm:grid-cols-[230px_1fr] sm:px-6">
                <div class="flex items-center justify-center">
                    <div class="relative h-[210px] w-[210px] rounded-full shadow-[0_14px_34px_rgba(18,24,31,.05)]" style="background: {{ $flowGradient }};">
                        <div class="absolute inset-[38px] grid place-items-center rounded-full bg-white text-center shadow-[inset_0_0_0_1px_#eef0f2]">
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-[.13em] text-[#9aa0a8]">Active Flow</p>
                                <p class="mt-1 text-[30px] font-bold tracking-[-.05em] text-[#181b1f]">{{ number_format($flowTotal) }}</p>
                                <p class="mt-1 text-[10px] text-[#8b929b]">parcels / batches</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col justify-center">
                    <p class="text-[10.5px] font-bold uppercase tracking-[.14em] text-[#b47a0d]">Current workload</p>
                    <h4 class="mt-1 text-[14px] font-bold text-[#25292e]">Where operations are concentrated</h4>

                    <div class="mt-4 space-y-3">
                        @foreach($flowLegend as $row)
                            <div>
                                <div class="flex items-center justify-between gap-3 text-[10.5px]">
                                    <span class="inline-flex items-center gap-2 font-medium text-[#59616a]">
                                        <span class="h-2 w-2 rounded-full" style="background: {{ $row['color'] }}"></span>
                                        {{ $row['label'] }}
                                    </span>
                                    <span class="font-bold text-[#2b3035]">{{ $row['value'] }}</span>
                                </div>
                                <div class="mt-1.5 h-[4px] overflow-hidden rounded-full bg-[#f0f2f4]">
                                    <div class="h-full rounded-full" style="width: {{ $flowTotal > 0 ? max(2, $row['percentage']) : 0 }}%; background: {{ $row['color'] }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <footer class="flex items-center justify-between border-t border-[#eef0f2] px-5 py-3 text-[10px] text-[#8d949d] sm:px-6">
                <span>Counts reflect live branch-scoped operational states.</span>
                <a href="{{ route('logistics.delivery-monitoring') }}" class="font-semibold text-[#9a680d] hover:text-[#7f5205]">View operations →</a>
            </footer>
        </article>

        {{-- TREND CHART --}}
        <article class="ld-panel overflow-hidden">
            <header class="flex flex-col gap-3 border-b border-[#eceef1] px-5 py-4 sm:flex-row sm:items-start sm:justify-between sm:px-6">
                <div>
                    <h3 class="text-[16px] font-bold tracking-[-.025em] text-[#1b1e22]">Operations Performance</h3>
                    <p class="mt-1 text-[11px] text-[#8b929b]">Seven-day delivered parcel and hub-receipt activity.</p>
                </div>
                <div class="inline-flex w-fit rounded-[10px] bg-[#f4f5f6] p-1">
                    <button type="button" class="ld-chart-toggle h-8 rounded-[8px] border border-transparent px-3 text-[10px] font-semibold text-[#8a9098]" data-chart-mode="deliveries" aria-pressed="true">Delivered</button>
                    <button type="button" class="ld-chart-toggle h-8 rounded-[8px] border border-transparent px-3 text-[10px] font-semibold text-[#8a9098]" data-chart-mode="transfers" aria-pressed="false">Hub Receipts</button>
                </div>
            </header>

            <div class="grid grid-cols-3 divide-x divide-[#edf0f2] border-b border-[#edf0f2] px-5 py-4 sm:px-6">
                <div class="pr-3">
                    <p class="text-[10px] uppercase tracking-[.1em] text-[#9aa0a8]">Delivered Today</p>
                    <p class="mt-1 text-[16px] font-bold text-[#1f2328]">{{ number_format($stats['delivered_today'] ?? 0) }}</p>
                </div>
                <div class="px-3">
                    <p class="text-[10px] uppercase tracking-[.1em] text-[#9aa0a8]">Hub Receipts</p>
                    <p class="mt-1 text-[16px] font-bold text-[#1f2328]">{{ number_format($stats['hub_transfer_received_today'] ?? 0) }}</p>
                </div>
                <div class="pl-3">
                    <p class="text-[10px] uppercase tracking-[.1em] text-[#9aa0a8]">Orders Today</p>
                    <p class="mt-1 text-[16px] font-bold text-[#1f2328]">{{ number_format($stats['orders_today'] ?? 0) }}</p>
                </div>
            </div>

            <div class="relative px-3 pb-3 pt-5 sm:px-5">
                <div id="logisticsPerformanceTooltip" class="ld-chart-tooltip"></div>
                <div id="logisticsPerformanceChart" class="min-h-[270px] w-full"></div>
            </div>
        </article>
    </section>

    {{-- SECONDARY ANALYTICS --}}
    <section class="grid gap-4 xl:grid-cols-[1.08fr_.92fr]">
        {{-- PIPELINE --}}
        <article class="ld-panel overflow-hidden">
            <header class="border-b border-[#eceef1] px-5 py-4 sm:px-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-[15px] font-bold tracking-[-.02em] text-[#202328]">Operational Pipeline</h3>
                        <p class="mt-1 text-[11px] text-[#8b929b]">Live workload from Seller pickup to last-mile staging.</p>
                    </div>
                    <span class="rounded-full bg-[#f6f7f8] px-2.5 py-1 text-[10.5px] font-semibold uppercase tracking-[.08em] text-[#737b84]">8 stages</span>
                </div>
            </header>

            <div class="grid gap-x-5 gap-y-4 px-5 py-5 sm:grid-cols-2 sm:px-6">
                @foreach($pipeline as $stage)
                    @php
                        $stageValue = (int) ($stage['value'] ?? 0);
                        $barWidth = ($stageValue / $maxPipeline) * 100;
                    @endphp
                    <a href="{{ route($stage['route']) }}" class="group rounded-[14px] border border-[#edf0f2] p-3.5 transition hover:border-[#e1c98f] hover:bg-[#fffdf8]">
                        <div class="flex items-start gap-3">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-[10px] bg-[#f6f7f8] text-[10px] font-bold text-[#7d848d] group-hover:bg-[#fff3d9] group-hover:text-[#a96f08]">{{ $stage['step'] }}</span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="truncate text-[11px] font-bold text-[#34393f]">{{ $stage['label'] }}</p>
                                    <p class="text-[15px] font-bold text-[#1c2025]">{{ number_format($stageValue) }}</p>
                                </div>
                                <div class="mt-2 h-[4px] overflow-hidden rounded-full bg-[#eef0f2]">
                                    <div class="h-full rounded-full bg-[#d79a18] transition-all" style="width: {{ $stageValue > 0 ? max(4, $barWidth) : 0 }}%"></div>
                                </div>
                                <p class="mt-2 line-clamp-2 text-[10.5px] leading-4 text-[#9298a0]">{{ $stage['description'] }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </article>

        {{-- RIDER + TRANSFER HEALTH --}}
        <article class="ld-panel overflow-hidden">
            <header class="border-b border-[#eceef1] px-5 py-4 sm:px-6">
                <h3 class="text-[15px] font-bold tracking-[-.02em] text-[#202328]">Branch Capacity</h3>
                <p class="mt-1 text-[11px] text-[#8b929b]">Rider availability and current linehaul health.</p>
            </header>

            <div class="grid gap-3 p-5 sm:grid-cols-2 sm:p-6">
                <div class="rounded-[16px] border border-[#edf0f2] bg-[#fbfcfc] p-4">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-[10px] font-semibold uppercase tracking-[.08em] text-[#818892]">Riders Online</p>
                        <span class="text-[13px] font-bold text-[#4d8b6a]">{{ $stats['online_rate'] ?? 0 }}%</span>
                    </div>
                    <div class="mt-3 h-[7px] overflow-hidden rounded-full bg-[#e8ece9]">
                        <div class="h-full rounded-full bg-[#5a9674]" style="width: {{ min(100, max(0, $stats['online_rate'] ?? 0)) }}%"></div>
                    </div>
                    <p class="mt-3 text-[10px] leading-4 text-[#8b929b]">{{ $stats['online_riders'] ?? 0 }} online from {{ $stats['active_riders'] ?? 0 }} active Rider accounts.</p>
                </div>

                <div class="rounded-[16px] border border-[#edf0f2] bg-[#fbfcfc] p-4">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-[10px] font-semibold uppercase tracking-[.08em] text-[#818892]">Rider Utilization</p>
                        <span class="text-[13px] font-bold text-[#4f82b6]">{{ $stats['rider_utilization'] ?? 0 }}%</span>
                    </div>
                    <div class="mt-3 h-[7px] overflow-hidden rounded-full bg-[#e7edf2]">
                        <div class="h-full rounded-full bg-[#5b88b4]" style="width: {{ min(100, max(0, $stats['rider_utilization'] ?? 0)) }}%"></div>
                    </div>
                    <p class="mt-3 text-[10px] leading-4 text-[#8b929b]">{{ $stats['busy_riders'] ?? 0 }} Rider(s) currently assigned to last-mile work.</p>
                </div>

                <div class="rounded-[16px] border border-[#edf0f2] p-4 sm:col-span-2">
                    <div class="grid grid-cols-3 divide-x divide-[#edf0f2] text-center">
                        <div class="px-2">
                            <p class="text-[20px] font-bold tracking-[-.04em] text-[#22262b]">{{ $stats['hub_transfer_preparing'] ?? 0 }}</p>
                            <p class="mt-1 text-[10px] uppercase tracking-[.08em] text-[#949aa2]">Preparing</p>
                        </div>
                        <div class="px-2">
                            <p class="text-[20px] font-bold tracking-[-.04em] text-[#c17f0b]">{{ $stats['hub_transfer_in_transit'] ?? 0 }}</p>
                            <p class="mt-1 text-[10px] uppercase tracking-[.08em] text-[#949aa2]">In Transit</p>
                        </div>
                        <div class="px-2">
                            <p class="text-[20px] font-bold tracking-[-.04em] text-[#4f8b69]">{{ $stats['hub_transfer_received_today'] ?? 0 }}</p>
                            <p class="mt-1 text-[10px] uppercase tracking-[.08em] text-[#949aa2]">Received Today</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-[#edf0f2] px-5 py-3 sm:px-6">
                <a href="{{ route('logistics.hub-transfers') }}" class="text-[10px] font-semibold text-[#9a680d] hover:text-[#7f5205]">Open Hub Transfers →</a>
            </div>
        </article>
    </section>

    {{-- RECENT TRANSFERS --}}
    <section class="ld-panel overflow-hidden">
        <header class="flex flex-col gap-3 border-b border-[#eceef1] px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-[15px] font-bold tracking-[-.02em] text-[#202328]">Recent Hub Transfers</h3>
                <p class="mt-1 text-[11px] text-[#8b929b]">Latest linehaul manifests related to {{ $branchName }}.</p>
            </div>
            <a href="{{ route('logistics.hub-transfers') }}" class="inline-flex h-8 items-center rounded-[9px] border border-[#e4e7eb] px-3 text-[10px] font-semibold text-[#59616a] transition hover:border-[#d7aa4d] hover:text-[#98660b]">View all transfers</a>
        </header>

        <div class="grid gap-3 p-4 sm:grid-cols-2 xl:grid-cols-4 sm:p-5">
            @forelse($recentTransfers as $transfer)
                @php
                    $isOutgoing = $hubFacility && (int) $transfer->origin_facility_id === (int) $hubFacility->id;
                    $transferStatus = strtolower((string) $transfer->status);
                    $statusClass = match($transferStatus) {
                        'received' => 'bg-[#edf7f1] text-[#4e8063]',
                        'in_transit' => 'bg-[#fff5df] text-[#9b680d]',
                        default => 'bg-[#f2f4f6] text-[#68707a]',
                    };
                @endphp
                <article class="rounded-[16px] border border-[#e9ecef] bg-[#fcfcfd] p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate text-[11px] font-bold text-[#262b30]">{{ $transfer->transfer_code }}</p>
                            <p class="mt-1 text-[10.5px] text-[#949aa2]">{{ $transfer->parcel_count }} parcel(s)</p>
                        </div>
                        <span class="rounded-full px-2 py-1 text-[10px] font-bold uppercase tracking-[.07em] {{ $statusClass }}">{{ str_replace('_', ' ', $transferStatus) }}</span>
                    </div>
                    <div class="mt-4 space-y-2 text-[10px]">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-[#9aa0a8]">Origin</span>
                            <span class="truncate font-semibold text-[#555d66]">{{ $transfer->origin_name }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-[#9aa0a8]">Destination</span>
                            <span class="truncate font-semibold text-[#555d66]">{{ $transfer->destination_name }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-[#9aa0a8]">Perspective</span>
                            <span class="font-semibold {{ $isOutgoing ? 'text-[#a86e05]' : 'text-[#4f7fa8]' }}">{{ $isOutgoing ? 'Outbound' : 'Inbound' }}</span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-[16px] border border-dashed border-[#dfe3e7] px-5 py-8 text-center">
                    <p class="text-[11px] font-semibold text-[#717983]">No branch transfer activity yet.</p>
                    <p class="mt-1 text-[10px] text-[#9aa0a8]">Transfers will appear after sorted parcels are grouped into linehaul manifests.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- RECENT ACTIVITY TABLE --}}
    <section class="ld-panel overflow-hidden">
        <header class="flex flex-col gap-3 border-b border-[#eceef1] px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-[15px] font-bold tracking-[-.02em] text-[#202328]">Recent Logistics Activity</h3>
                <p class="mt-1 text-[11px] text-[#8b929b]">Latest marketplace orders moving through the selected branch.</p>
            </div>
            <a href="{{ route('logistics.delivery-monitoring') }}" class="text-[10px] font-semibold text-[#9a680d] hover:text-[#7f5205]">View monitoring →</a>
        </header>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-left">
                <thead class="bg-[#fafbfc] text-[10.5px] uppercase tracking-[.08em] text-[#9198a1]">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Order</th>
                        <th class="px-5 py-3 font-semibold">Seller</th>
                        <th class="px-5 py-3 font-semibold">Buyer</th>
                        <th class="px-5 py-3 font-semibold">Rider</th>
                        <th class="px-5 py-3 font-semibold">Order Status</th>
                        <th class="px-5 py-3 font-semibold">Parcel Stage</th>
                        <th class="px-5 py-3 font-semibold">Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eef0f2]">
                    @forelse($recentOrders as $order)
                        <tr class="ld-table-row text-[10.5px] text-[#616871]">
                            <td class="px-5 py-3.5 font-bold text-[#30353a]">{{ $order->order_number }}</td>
                            <td class="px-5 py-3.5">{{ $order->seller?->store_name ?: 'SARI Seller' }}</td>
                            <td class="px-5 py-3.5">{{ $order->buyer_name }}</td>
                            <td class="px-5 py-3.5">{{ $order->courier_name ?: 'Unassigned' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="rounded-full bg-[#fff6e5] px-2.5 py-1 text-[10px] font-semibold text-[#a56b06]">{{ $order->statusLabel() }}</span>
                            </td>
                            <td class="px-5 py-3.5 font-medium text-[#505861]">{{ $order->logisticsParcel ? strtoupper(str_replace('_', ' ', $order->logisticsParcel->status)) : '—' }}</td>
                            <td class="px-5 py-3.5 text-[#9399a1]">{{ $order->updated_at?->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center">
                                <p class="text-[11px] font-semibold text-[#737b84]">No logistics orders yet.</p>
                                <p class="mt-1 text-[10px] text-[#9ba1a9]">Orders assigned to this branch will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const deliveryData = @json($deliveryTrend);
    const transferData = @json($transferReceiptTrend);
    const chartRoot = document.getElementById('logisticsPerformanceChart');
    const tooltip = document.getElementById('logisticsPerformanceTooltip');
    const toggles = Array.from(document.querySelectorAll('[data-chart-mode]'));

    function updateClock() {
        const now = new Date();
        const time = new Intl.DateTimeFormat('en-PH', {
            timeZone: 'Asia/Manila',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        }).format(now);
        const day = new Intl.DateTimeFormat('en-PH', {
            timeZone: 'Asia/Manila',
            weekday: 'long',
        }).format(now);
        const date = new Intl.DateTimeFormat('en-PH', {
            timeZone: 'Asia/Manila',
            month: 'long',
            day: 'numeric',
            year: 'numeric',
        }).format(now);

        const greetingHour = Number(new Intl.DateTimeFormat('en-PH', {
            timeZone: 'Asia/Manila',
            hour: '2-digit',
            hourCycle: 'h23',
        }).format(now));

        let greeting = 'Good evening';
        if (greetingHour >= 5 && greetingHour < 12) {
            greeting = 'Good morning';
        } else if (greetingHour >= 12 && greetingHour < 18) {
            greeting = 'Good afternoon';
        }

        const greetingNode = document.getElementById('logisticsDashboardGreeting');
        const clockNode = document.getElementById('logisticsDashboardClock');
        const dayNode = document.getElementById('logisticsDashboardDay');
        const dateNode = document.getElementById('logisticsDashboardDate');
        if (greetingNode) greetingNode.textContent = `${greeting}, Logistics!`;
        if (clockNode) clockNode.textContent = time;
        if (dayNode) dayNode.textContent = day;
        if (dateNode) dateNode.textContent = date;
    }

    updateClock();
    window.setInterval(updateClock, 30000);

    function svgNode(name, attrs = {}) {
        const el = document.createElementNS('http://www.w3.org/2000/svg', name);
        Object.entries(attrs).forEach(([key, value]) => el.setAttribute(key, String(value)));
        return el;
    }

    function renderChart(dataset, mode) {
        if (!chartRoot) return;
        chartRoot.innerHTML = '';

        const rows = Array.isArray(dataset) ? dataset : [];
        const width = 820;
        const height = 270;
        const left = 52;
        const right = 24;
        const top = 22;
        const bottom = 44;
        const plotW = width - left - right;
        const plotH = height - top - bottom;
        const values = rows.map(row => Number(row.value || 0));
        const maxValue = Math.max(1, ...values);
        const niceMax = Math.max(1, Math.ceil(maxValue / 5) * 5);
        const color = mode === 'transfers' ? '#4F82B6' : '#D49312';
        const fill = mode === 'transfers' ? 'rgba(79,130,182,.10)' : 'rgba(212,147,18,.10)';

        const svg = svgNode('svg', {
            viewBox: `0 0 ${width} ${height}`,
            width: '100%',
            height: '270',
            role: 'img',
            'aria-label': mode === 'transfers' ? 'Seven-day hub receipt chart' : 'Seven-day delivered parcel chart',
        });
        svg.style.display = 'block';

        for (let i = 0; i <= 4; i += 1) {
            const y = top + (plotH / 4) * i;
            const grid = svgNode('line', {
                x1: left,
                y1: y,
                x2: width - right,
                y2: y,
                stroke: '#edf0f2',
                'stroke-width': 1,
                'stroke-dasharray': '4 5',
            });
            svg.appendChild(grid);

            const tick = Math.round(niceMax - (niceMax / 4) * i);
            const text = svgNode('text', {
                x: left - 12,
                y: y + 3,
                'text-anchor': 'end',
                fill: '#9299a2',
                'font-size': 9,
                'font-family': 'Poppins, sans-serif',
            });
            text.textContent = tick;
            svg.appendChild(text);
        }

        const points = rows.map((row, index) => {
            const x = rows.length <= 1
                ? left + plotW / 2
                : left + (plotW / (rows.length - 1)) * index;
            const value = Number(row.value || 0);
            const y = top + plotH - (value / niceMax) * plotH;
            return { x, y, value, row };
        });

        if (points.length) {
            const linePath = points.map((point, index) => `${index === 0 ? 'M' : 'L'} ${point.x} ${point.y}`).join(' ');
            const areaPath = `${linePath} L ${points[points.length - 1].x} ${top + plotH} L ${points[0].x} ${top + plotH} Z`;

            const area = svgNode('path', { d: areaPath, fill, stroke: 'none' });
            svg.appendChild(area);

            const line = svgNode('path', {
                d: linePath,
                fill: 'none',
                stroke: color,
                'stroke-width': 2.2,
                'stroke-linecap': 'round',
                'stroke-linejoin': 'round',
            });
            svg.appendChild(line);

            points.forEach((point) => {
                const dot = svgNode('circle', {
                    cx: point.x,
                    cy: point.y,
                    r: 4.2,
                    fill: '#fff',
                    stroke: color,
                    'stroke-width': 2,
                    tabindex: 0,
                });
                dot.style.cursor = 'pointer';

                const showTooltip = () => {
                    if (!tooltip) return;
                    tooltip.innerHTML = `<strong>${point.value}</strong><br><span style="color:rgba(255,255,255,.64)">${point.row.date || point.row.label}</span>`;
                    tooltip.style.display = 'block';
                    tooltip.style.left = `${(point.x / width) * 100}%`;
                    tooltip.style.top = `${(point.y / height) * 100}%`;
                };
                const hideTooltip = () => {
                    if (tooltip) tooltip.style.display = 'none';
                };
                dot.addEventListener('mouseenter', showTooltip);
                dot.addEventListener('focus', showTooltip);
                dot.addEventListener('mouseleave', hideTooltip);
                dot.addEventListener('blur', hideTooltip);
                svg.appendChild(dot);
            });
        }

        rows.forEach((row, index) => {
            const x = rows.length <= 1
                ? left + plotW / 2
                : left + (plotW / (rows.length - 1)) * index;
            const label = svgNode('text', {
                x,
                y: height - 18,
                'text-anchor': 'middle',
                fill: '#8e959e',
                'font-size': 9,
                'font-family': 'Poppins, sans-serif',
            });
            label.textContent = row.label || '';
            svg.appendChild(label);
        });

        chartRoot.appendChild(svg);
    }

    function setMode(mode) {
        toggles.forEach((button) => {
            button.setAttribute('aria-pressed', button.dataset.chartMode === mode ? 'true' : 'false');
        });
        renderChart(mode === 'transfers' ? transferData : deliveryData, mode);
    }

    toggles.forEach((button) => {
        button.addEventListener('click', () => setMode(button.dataset.chartMode));
    });

    setMode('deliveries');
})();
</script>
@endpush
