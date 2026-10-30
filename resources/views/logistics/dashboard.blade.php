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

    /* Clean KPI surfaces: keep the existing links and SVG icons, without icon tiles. */
    .ld-kpi {
        position: relative;
        min-height: 106px;
        overflow: hidden;
        border: 1px solid var(--ld-line);
        border-radius: 17px;
        background: #fff;
        padding: 16px 17px;
        box-shadow: 0 6px 18px rgba(25, 31, 38, .035);
        transition: border-color .16s ease, box-shadow .16s ease;
    }

    .ld-kpi:hover {
        border-color: #d7dce2;
        box-shadow: 0 8px 20px rgba(25, 31, 38, .05);
    }

    .ld-kpi:focus-visible {
        outline: 2px solid #bf8c28;
        outline-offset: 3px;
    }

    /* Larger SVG icons in the original compact icon slot, with no tiles. */
    .logistics-dashboard-shell .ld-kpi-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 22px;
        width: 22px;
        height: 22px;
        margin-top: 0;
        padding: 0;
        border: 0;
        border-radius: 0;
        background: transparent !important;
        box-shadow: none !important;
        color: #647080;
    }

    .logistics-dashboard-shell .ld-kpi-icon > svg {
        width: 30px !important;
        height: 30px !important;
    }

    .ld-kpi-icon.gold { color: #ad7b1e; }
    .ld-kpi-icon.blue { color: #61819f; }
    .ld-kpi-icon.green { color: #4f846a; }
    .ld-kpi-icon.red { color: #a97566; }

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


    /* Dashboard-only charcoal hero.
       The shared buyer-parity.css loads after @stack('styles') and forces a
       white background with #logisticsContent specificity + !important.
       Include that scope and an extra class here so these declarations win,
       regardless of load order, without changing any shared logistics styles. */
    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell .ld-exec-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid #101317;
        border-radius: 18px;
        padding: 18px 22px;
        background: #15181c !important;
        background-image: none !important;
        border-color: #101317 !important;
        color: #f9fafb !important;
        box-shadow: 0 10px 24px rgba(14, 17, 22, .17) !important;
    }

    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell .ld-exec-hero::before,
    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell .ld-exec-hero::after {
        content: none !important;
        display: none !important;
    }

    /* Keep text, status and numbers legible on the dark hero. */
    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell .ld-exec-title,
    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell .ld-exec-stat-value {
        color: #f9fafb !important;
    }
    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell .ld-exec-kicker {
        color: #e8bd61 !important;
    }
    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell .ld-exec-main-badge {
        color: #e8bd61 !important;
        border-color: rgba(232, 189, 97, .3) !important;
        background: rgba(232, 189, 97, .09) !important;
    }
    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell :is(.ld-exec-status, .ld-exec-stat-sub),
    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell .ld-exec-rider-value small {
        color: #afb9c4 !important;
    }
    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell .ld-exec-stat-label {
        color: #9ea9b5 !important;
    }
    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell .ld-exec-online {
        color: #7adba5 !important;
    }
    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell .ld-exec-stat + .ld-exec-stat {
        border-left-color: rgba(255, 255, 255, .13) !important;
    }
    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell .ld-exec-stat-icon {
        background: rgba(232, 189, 97, .11) !important;
        color: #e8bd61 !important;
    }
    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell .ld-exec-stat-icon.is-rider {
        background: rgba(122, 178, 220, .11) !important;
        color: #a2c7e7 !important;
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
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: #e4bb62;
    }

    .ld-exec-main-badge {
        display: inline-flex;
        align-items: center;
        min-height: 21px;
        border: 1px solid rgba(229,191,98,.24);
        border-radius: 999px;
        background: rgba(229,191,98,.08);
        padding: 0 8px;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #e4bb62;
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
        font-size: 10.5px;
        line-height: 1.5;
        color: rgba(255,255,255,.74);
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
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .09em;
        text-transform: uppercase;
        color: rgba(255,255,255,.62);
    }

    .ld-exec-stat-row {
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
    }

    /* Header stat icons remain, but no longer sit inside decorative circles. */
    .ld-exec-stat-icon {
        display: inline-grid;
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
        place-items: center;
        border: 0;
        border-radius: 0;
        background: transparent;
        box-shadow: none;
        color: #e1bb6a;
    }

    .ld-exec-stat-icon.is-rider { color: #8ab3d5; }

    /* Larger glyphs only — preserve the original icon slots and hero dimensions. */
    #logisticsContent .sari-logistics-ui.logistics-dashboard-shell .ld-exec-stat-icon > svg {
        width: 24px !important;
        height: 24px !important;
        min-width: 24px;
        max-width: none;
        overflow: visible;
    }
    .ld-exec-stat-row { gap: 10px; }

    .ld-exec-stat-value {
        min-width: 0;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.15;
        color: #fff;
        white-space: nowrap;
    }

    .ld-exec-stat-sub {
        margin-top: 2px;
        font-size: 9.5px;
        line-height: 1.4;
        color: rgba(255,255,255,.68);
        white-space: nowrap;
    }

    .ld-exec-rider-value {
        display: flex;
        align-items: baseline;
        gap: 4px;
        white-space: nowrap;
    }

    .ld-exec-rider-value small {
        font-size: 9.5px;
        font-weight: 500;
        color: rgba(255,255,255,.65);
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

    /* Compact pipeline + capacity visuals — source: existing dashboard data, no chart library. */
    .ld-insight-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(0, .85fr);
        gap: 16px;
        align-items: stretch;
    }
    .ld-insight-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        border-bottom: 1px solid #edf0f2;
        padding: 16px 20px;
    }
    .ld-insight-title {
        color: #20242b;
        font-size: 15px;
        font-weight: 750;
        line-height: 1.3;
        letter-spacing: -.025em;
    }
    .ld-insight-subtitle { margin-top: 3px; color: #89929f; font-size: 10.5px; line-height: 1.5; }
    .ld-insight-badge {
        flex: 0 0 auto;
        border-radius: 99px;
        background: #f4f6f8;
        color: #6e7886;
        padding: 5px 10px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .06em;
        white-space: nowrap;
    }
    .ld-flow-track { overflow-x: auto; padding: 17px 14px 13px; scrollbar-width: thin; scrollbar-color: #d7dce2 transparent; }
    .ld-flow-stages { display: grid; grid-template-columns: repeat(8, minmax(0, 1fr)); gap: 5px; min-width: 600px; }
    .ld-flow-stage {
        position: relative;
        display: flex;
        align-items: center;
        flex-direction: column;
        gap: 7px;
        padding: 3px 1px 4px;
        border-radius: 10px;
        color: #536174;
        text-decoration: none;
        outline-offset: 2px;
    }
    .ld-flow-stage:not(:last-child)::after {
        content: '›';
        position: absolute;
        top: 12px;
        right: -7px;
        color: #c4ccd5;
        font-size: 20px;
        font-weight: 400;
        line-height: 1;
        pointer-events: none;
    }
    .ld-flow-count {
        display: grid;
        width: 43px;
        height: 43px;
        flex: 0 0 43px;
        place-items: center;
        border: 1px solid #e6ebef;
        border-radius: 50%;
        background: #f4f6f8;
        color: #435064;
        font-size: 15px;
        font-weight: 750;
        font-variant-numeric: tabular-nums;
    }
    .ld-flow-stage.has-work .ld-flow-count {
        border-color: #f4c76e;
        background: #f9e8bd;
        color: #9b670b;
    }
    .ld-flow-stage.has-work .ld-flow-label { color: #332919; font-weight: 700; }
    .ld-flow-label {
        display: block;
        max-width: 85px;
        min-height: 29px;
        text-align: center;
        color: #697586;
        font-size: 10px;
        font-weight: 550;
        line-height: 1.4;
    }
    .ld-flow-stage:hover { background: #faf7ef; }
    .ld-flow-stage:focus-visible { outline: 2px solid #c58d25; }
    .ld-stage-chart-card { margin: 0 17px 17px; padding: 14px 15px 10px; border: 1px solid #e9edf1; border-radius: 15px; background: #fff; }
    .ld-stage-chart-head { display: flex; align-items: baseline; justify-content: space-between; gap: 10px; }
    .ld-stage-chart-head h4 { color: #29313b; font-size: 12px; font-weight: 750; }
    .ld-stage-chart-head span { color: #8c96a1; font-size: 9px; }
    .ld-stage-chart { display: flex; gap: 8px; height: 162px; margin-top: 12px; }
    .ld-stage-chart-scale { display: flex; flex: 0 0 21px; flex-direction: column; justify-content: space-between; height: 123px; padding: 0 0 2px; color: #9ca5b0; font-size: 9px; text-align: right; font-variant-numeric: tabular-nums; }
    .ld-stage-chart-plot { position: relative; flex: 1; min-width: 0; }
    .ld-stage-chart-grid {
        position: absolute;
        inset: 0 0 auto;
        height: 123px;
        border-bottom: 1px solid #dce2e8;
        background: linear-gradient(to bottom,
            #edf0f2 0, #edf0f2 1px,
            transparent 1px, transparent calc(50% - 1px),
            #edf0f2 calc(50% - 1px), #edf0f2 50%,
            transparent 50%);
        pointer-events: none;
    }
    .ld-stage-chart-columns { position: relative; z-index: 1; display: grid; grid-template-columns: repeat(8, minmax(0,1fr)); height: 100%; }
    .ld-stage-chart-column { display: flex; min-width: 0; flex-direction: column; align-items: center; text-decoration: none; outline-offset: 1px; }
    .ld-stage-chart-column:focus-visible { outline: 2px solid #c58d25; border-radius: 4px; }
    .ld-stage-chart-barbox { position: relative; display: block; width: 100%; height: 123px; }
    .ld-stage-chart-bar {
        position: absolute;
        bottom: 1px;
        left: 50%;
        width: min(45%, 34px);
        transform: translateX(-50%);
        border-radius: 6px 6px 0 0;
        background: #e2b051;
    }
    .ld-stage-chart-column:nth-child(3) .ld-stage-chart-bar { background: #d99513; }
    .ld-stage-chart-column:hover .ld-stage-chart-bar { filter: brightness(.92); }
    .ld-stage-chart-value {
        position: absolute;
        bottom: calc(100% + 3px);
        left: 50%;
        transform: translateX(-50%);
        color: #354054;
        font-size: 10px;
        font-weight: 750;
        font-variant-numeric: tabular-nums;
    }
    .ld-stage-chart-zero { position: absolute; bottom: 5px; left: 50%; transform: translateX(-50%); color: #a9b0b9; font-size: 10px; }
    .ld-stage-chart-x { margin-top: 6px; color: #7e8997; font-size: 10px; font-weight: 600; }
    .ld-stage-chart-column:hover .ld-stage-chart-x { color: #ad730e; }
    .ld-capacity-gauges { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 11px; padding: 17px 17px 12px; }
    .ld-capacity-gauge { display: flex; flex-direction: column; align-items: center; border: 1px solid #e9edf1; border-radius: 15px; background: #fff; padding: 13px 8px; text-align: center; }
    .ld-capacity-gauge h4 { min-height: 18px; color: #2b3440; font-size: 11px; font-weight: 750; }
    .ld-donut { display: grid; width: 100px; height: 100px; flex: 0 0 100px; place-items: center; margin-top: 11px; border-radius: 50%; }
    .ld-donut-inner { display: flex; width: 75px; height: 75px; align-items: center; justify-content: center; flex-direction: column; gap: 2px; border-radius: 50%; background: #fff; }
    .ld-donut-value { color: #232d3b; font-size: 20px; font-weight: 760; line-height: 1.1; letter-spacing: -.04em; font-variant-numeric: tabular-nums; }
    .ld-donut-detail { color: #9ca4ae; font-size: 9px; line-height: 1.2; }
    .ld-capacity-footnote { margin-top: 8px; color: #82909e; font-size: 10px; line-height: 1.4; }
    .ld-capacity-transfers { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); padding: 12px 4px; margin: 0 17px; border: 1px solid #e9edf1; border-radius: 15px; text-align: center; }
    .ld-capacity-transfers > div { min-width: 0; padding: 3px 5px; }
    .ld-capacity-transfers > div + div { border-left: 1px solid #edf0f2; }
    .ld-capacity-transfer-number { color: #222d3a; font-size: 19px; font-weight: 750; font-variant-numeric: tabular-nums; }
    .ld-capacity-transfer-number.is-gold { color: #bc7f13; }
    .ld-capacity-transfer-number.is-green { color: #448766; }
    .ld-capacity-transfer-label { margin-top: 2px; color: #8d98a5; font-size: 9px; font-weight: 650; letter-spacing: .05em; text-transform: uppercase; }
    .ld-capacity-footer { margin: 13px 17px 17px; }
    .ld-capacity-action { display: flex; align-items: center; justify-content: space-between; gap: 12px; border: 1px solid #f0e4c8; border-radius: 12px; background: #fffcf6; padding: 12px 14px; color: #8d620e; font-size: 11px; font-weight: 700; text-decoration: none; }
    .ld-capacity-action:hover { border-color: #d9b76e; background: #fff7e6; }
    .ld-capacity-action:focus-visible { outline: 2px solid #bd8b28; outline-offset: 2px; }
    @media (max-width: 1279px) { .ld-insight-layout { grid-template-columns: minmax(0,1fr); } }
    @media (max-width: 640px) {
        .ld-insight-head { padding: 14px 15px; }
        .ld-flow-track { padding: 14px 11px 10px; }
        .ld-stage-chart-card { margin: 0 11px 12px; padding: 12px 11px 9px; }
        .ld-capacity-gauges { padding: 12px 11px; gap: 8px; }
        .ld-capacity-gauge { padding: 10px 5px; }
        .ld-donut { width: 84px; height: 84px; flex-basis: 84px; }
        .ld-donut-inner { width: 63px; height: 63px; }
        .ld-donut-value { font-size: 17px; }
        .ld-capacity-transfers { margin: 0 11px; }
        .ld-capacity-footer { margin: 11px; }
    }


    /* Compact recent panels: link looks like text, never like a button. */
    .logistics-dashboard-shell .ld-recent-view-all {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        gap: 5px;
        width: auto;
        min-height: 0;
        height: auto;
        margin: 0;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        color: #aa720d !important;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.5;
        text-decoration: none;
        white-space: nowrap;
        transition: color .15s ease;
    }
    .logistics-dashboard-shell .ld-recent-view-all:hover {
        color: #7e5308 !important;
        text-decoration: underline;
        text-underline-offset: 3px;
    }
    .logistics-dashboard-shell .ld-recent-view-all:focus-visible {
        outline: 2px solid #c18b22;
        outline-offset: 4px;
    }

    /* Recent activity: layout adapts to its actual panel width, never scrolls sideways. */
    .ld-recent-activity { container-type: inline-size; }
    .ld-activity-gridhead,
    .ld-activity-row {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr) minmax(0, .95fr) minmax(0, 1.05fr) minmax(0, .7fr);
        gap: 11px;
        align-items: center;
    }
    .ld-activity-gridhead {
        padding: 12px 18px;
        background: #fafbfc;
        border-bottom: 1px solid #edf0f2;
        color: #9098a2;
        font-size: 10px;
        font-weight: 650;
        letter-spacing: .055em;
        text-transform: uppercase;
    }
    .ld-activity-row {
        min-width: 0;
        padding: 14px 18px;
        border-bottom: 1px solid #edf0f2;
        color: #616871;
        font-size: 10.5px;
        line-height: 1.55;
        transition: background-color .15s ease;
    }
    .ld-activity-row:last-child { border-bottom: 0; }
    .ld-activity-row:hover { background: #fafbfc; }
    .ld-activity-row > * { min-width: 0; }
    .ld-activity-order-code {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        color: #30353a;
        overflow-wrap: anywhere;
    }
    .ld-activity-small { display: block; margin-top: 2px; font-size: 10px; color: #8f97a1; overflow-wrap: anywhere; }
    .ld-activity-primary { display: block; font-weight: 550; color: #505861; overflow-wrap: anywhere; }
    .ld-activity-status {
        display: inline-flex;
        max-width: 100%;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.3;
    }
    .ld-activity-stage { display: block; font-size: 10px; font-weight: 600; color: #505861; overflow-wrap: anywhere; }
    .ld-activity-updated { display: block; color: #8f97a1; overflow-wrap: anywhere; }
    .ld-activity-mobile-label { display: none; }
    @container (max-width: 740px) {
        .ld-activity-gridhead { display: none; }
        .ld-activity-list { padding: 10px; }
        .ld-activity-row {
            grid-template-columns: minmax(0, 1fr) minmax(115px, .85fr);
            gap: 9px 12px;
            align-items: start;
            border: 1px solid #eaedf1;
            border-radius: 13px;
            padding: 12px 14px;
            margin-bottom: 9px;
            background: #fff;
        }
        .ld-activity-row:last-child { margin-bottom: 0; border-bottom: 1px solid #eaedf1; }
        .ld-activity-order { grid-column: 1; grid-row: 1; }
        .ld-activity-status-wrap { grid-column: 2; grid-row: 1; justify-self: end; }
        .ld-activity-people { grid-column: 1; grid-row: 2; }
        .ld-activity-parcel { grid-column: 2; grid-row: 2; }
        .ld-activity-time { grid-column: 1 / -1; grid-row: 3; }
        .ld-activity-mobile-label {
            display: block;
            font-size: 9px;
            color: #9aa1a9;
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 650;
            margin-bottom: 3px;
        }
        .ld-activity-time .ld-activity-mobile-label { display: inline; margin: 0 7px 0 0; }
        .ld-activity-time .ld-activity-updated { display: inline; }
    }
    @media (prefers-reduced-motion: reduce) {
        .ld-activity-row { transition: none; }
    }
</style>
@endpush

@section('content')
<div class="sari-logistics-ui logistics-dashboard-shell mx-auto w-full max-w-[1900px] space-y-4 sm:space-y-5">

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

    {{-- SECONDARY ANALYTICS — COMPACT PIPELINE CHART + CAPACITY DONUTS --}}
    <section class="ld-insight-layout">
        {{-- OPERATIONAL PIPELINE: actual stage values and routes supplied by the controller. --}}
        <article class="ld-panel overflow-hidden">
            <header class="ld-insight-head">
                <div class="min-w-0">
                    <h3 class="ld-insight-title">Operational Pipeline</h3>
                    <p class="ld-insight-subtitle">Live workload from Seller pickup to last-mile staging.</p>
                </div>
                <span class="ld-insight-badge">{{ count($pipeline) }} STAGES</span>
            </header>

            {{-- All stage links are retained; scrollable only when the viewport is narrow. --}}
            <nav class="ld-flow-track" aria-label="Logistics pipeline stages">
                <div class="ld-flow-stages">
                    @foreach($pipeline as $stage)
                        @php $stageValue = max(0, (int) ($stage['value'] ?? 0)); @endphp
                        <a href="{{ route($stage['route']) }}"
                           class="ld-flow-stage {{ $stageValue > 0 ? 'has-work' : '' }}"
                           title="{{ $stage['description'] }}"
                           aria-label="{{ $stage['label'] }}: {{ number_format($stageValue) }}. {{ $stage['description'] }}">
                            <span class="ld-flow-count">{{ number_format($stageValue) }}</span>
                            <span class="ld-flow-label">{{ $stage['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </nav>

            {{-- Lightweight CSS bar chart: snapshot, not fabricated history or growth. --}}
            <div class="ld-stage-chart-card">
                <div class="ld-stage-chart-head">
                    <h4>Workload by Stage</h4>
                    <span>Current snapshot</span>
                </div>
                <div class="ld-stage-chart" role="group" aria-label="Current workload by logistics stage">
                    <div class="ld-stage-chart-scale" aria-hidden="true">
                        <span>{{ number_format($maxPipeline) }}</span>
                        <span>{{ number_format((int) ceil($maxPipeline / 2)) }}</span>
                        <span>0</span>
                    </div>
                    <div class="ld-stage-chart-plot">
                        <div class="ld-stage-chart-grid" aria-hidden="true"></div>
                        <div class="ld-stage-chart-columns">
                            @foreach($pipeline as $stage)
                                @php
                                    $stageValue = max(0, (int) ($stage['value'] ?? 0));
                                    $barHeight = min(100, ($stageValue / $maxPipeline) * 100);
                                @endphp
                                <a href="{{ route($stage['route']) }}"
                                   class="ld-stage-chart-column"
                                   title="{{ $stage['label'] }}: {{ number_format($stageValue) }}"
                                   aria-label="{{ $stage['label'] }}: {{ number_format($stageValue) }}; open stage">
                                    <span class="ld-stage-chart-barbox" aria-hidden="true">
                                        @if($stageValue > 0)
                                            <span class="ld-stage-chart-bar" style="height: {{ $barHeight }}%">
                                                <span class="ld-stage-chart-value">{{ number_format($stageValue) }}</span>
                                            </span>
                                        @else
                                            <span class="ld-stage-chart-zero">0</span>
                                        @endif
                                    </span>
                                    <span class="ld-stage-chart-x">{{ $stage['step'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </article>

        {{-- BRANCH CAPACITY: percentage rings use the existing backend statistics. --}}
        @php
            $onlineRate = min(100, max(0, (float) ($stats['online_rate'] ?? 0)));
            $utilizationRate = min(100, max(0, (float) ($stats['rider_utilization'] ?? 0)));
        @endphp
        <article class="ld-panel overflow-hidden">
            <header class="ld-insight-head">
                <div class="min-w-0">
                    <h3 class="ld-insight-title">Branch Capacity</h3>
                    <p class="ld-insight-subtitle">Rider availability and current linehaul health.</p>
                </div>
            </header>

            <div class="ld-capacity-gauges">
                <div class="ld-capacity-gauge">
                    <h4>Riders Online</h4>
                    <div class="ld-donut"
                         role="img"
                         aria-label="{{ $onlineRate }} percent of active riders online"
                         style="background: conic-gradient(#51a37d 0 {{ $onlineRate }}%, #edf0f2 {{ $onlineRate }}% 100%)">
                        <div class="ld-donut-inner">
                            <span class="ld-donut-value">{{ $onlineRate }}%</span>
                            <span class="ld-donut-detail">Online</span>
                        </div>
                    </div>
                    <p class="ld-capacity-footnote">{{ number_format($stats['online_riders'] ?? 0) }} of {{ number_format($stats['active_riders'] ?? 0) }} active riders</p>
                </div>
                <div class="ld-capacity-gauge">
                    <h4>Rider Utilization</h4>
                    <div class="ld-donut"
                         role="img"
                         aria-label="{{ $utilizationRate }} percent rider utilization"
                         style="background: conic-gradient(#6091c7 0 {{ $utilizationRate }}%, #edf0f2 {{ $utilizationRate }}% 100%)">
                        <div class="ld-donut-inner">
                            <span class="ld-donut-value">{{ $utilizationRate }}%</span>
                            <span class="ld-donut-detail">Assigned</span>
                        </div>
                    </div>
                    <p class="ld-capacity-footnote">{{ number_format($stats['busy_riders'] ?? 0) }} rider(s) on last-mile work</p>
                </div>
            </div>

            <div class="ld-capacity-transfers" aria-label="Hub transfer statuses">
                <div>
                    <p class="ld-capacity-transfer-number">{{ number_format($stats['hub_transfer_preparing'] ?? 0) }}</p>
                    <p class="ld-capacity-transfer-label">Preparing</p>
                </div>
                <div>
                    <p class="ld-capacity-transfer-number is-gold">{{ number_format($stats['hub_transfer_in_transit'] ?? 0) }}</p>
                    <p class="ld-capacity-transfer-label">In Transit</p>
                </div>
                <div>
                    <p class="ld-capacity-transfer-number is-green">{{ number_format($stats['hub_transfer_received_today'] ?? 0) }}</p>
                    <p class="ld-capacity-transfer-label">Received Today</p>
                </div>
            </div>

            <div class="ld-capacity-footer">
                <a href="{{ route('logistics.hub-transfers') }}" class="ld-capacity-action">
                    <span>Open Hub Transfers</span><span aria-hidden="true">→</span>
                </a>
            </div>
        </article>
    </section>

    {{-- RECENT OPERATIONS --}}
    <section class="grid items-stretch gap-4 xl:grid-cols-[.92fr_1.08fr]">
        <article class="ld-panel overflow-hidden flex h-full flex-col">
            <header class="flex flex-col gap-3 border-b border-[#eceef1] px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h3 class="text-[15px] font-bold tracking-[-.02em] text-[#202328]">Recent Hub Transfers</h3>
                    <p class="mt-1 text-[11px] text-[#8b929b]">Latest linehaul manifests related to {{ $branchName }}.</p>
                </div>
                <a href="{{ route('logistics.hub-transfers') }}" class="ld-recent-view-all">View all <span aria-hidden="true">→</span></a>
            </header>

            <div class="flex-1 space-y-2.5 p-3 sm:p-4">
                @forelse(collect($recentTransfers)->take(3) as $transfer)
                    @php
                        $isOutgoing = $hubFacility && (int) $transfer->origin_facility_id === (int) $hubFacility->id;
                        $transferStatus = strtolower((string) $transfer->status);
                        $statusClass = match($transferStatus) {
                            'received' => 'bg-[#edf7f1] text-[#4e8063]',
                            'in_transit' => 'bg-[#fff5df] text-[#9b680d]',
                            default => 'bg-[#f2f4f6] text-[#68707a]',
                        };
                    @endphp
                    <article class="rounded-[13px] border border-[#e9ecef] bg-[#fcfcfd] px-3.5 py-3 transition hover:border-[#dfe4ea] hover:bg-white">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    <p class="truncate text-[11px] font-bold text-[#262b30]">{{ $transfer->transfer_code }}</p>
                                    <span class="text-[10px] text-[#949aa2]">{{ $transfer->parcel_count }} parcel(s)</span>
                                </div>
                            </div>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-[.07em] {{ $statusClass }}">{{ str_replace('_', ' ', $transferStatus) }}</span>
                        </div>

                        <div class="mt-2 grid gap-x-3 gap-y-1.5 text-[10.5px] text-[#616871] sm:grid-cols-[84px_1fr] sm:items-start">
                            <span class="font-semibold uppercase tracking-[.08em] text-[#9aa0a8]">Route</span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-[#49515a]">{{ $transfer->origin_name }}</p>
                                <p class="truncate text-[#8f96a0]">to {{ $transfer->destination_name }}</p>
                            </div>

                            <span class="font-semibold uppercase tracking-[.08em] text-[#9aa0a8]">Perspective</span>
                            <p class="font-semibold {{ $isOutgoing ? 'text-[#a86e05]' : 'text-[#4f7fa8]' }}">{{ $isOutgoing ? 'Outbound' : 'Inbound' }}</p>
                        </div>
                    </article>
                @empty
                    <div class="grid min-h-[130px] place-items-center rounded-[16px] border border-dashed border-[#dfe3e7] px-5 py-8 text-center">
                        <div>
                            <p class="text-[11px] font-semibold text-[#717983]">No branch transfer activity yet.</p>
                            <p class="mt-1 text-[10px] text-[#9aa0a8]">Transfers will appear after sorted parcels are grouped into linehaul manifests.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </article>

        <article class="ld-panel overflow-hidden flex h-full flex-col">
            <header class="flex flex-col gap-3 border-b border-[#eceef1] px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h3 class="text-[15px] font-bold tracking-[-.02em] text-[#202328]">Recent Logistics Activity</h3>
                    <p class="mt-1 text-[11px] text-[#8b929b]">Latest marketplace orders moving through the selected branch.</p>
                </div>
                <a href="{{ route('logistics.delivery-monitoring') }}" class="ld-recent-view-all">View all <span aria-hidden="true">→</span></a>
            </header>

            <div class="ld-recent-activity flex-1 min-w-0">
                <div class="ld-activity-gridhead" aria-hidden="true">
                    <span>Order</span>
                    <span>Buyer / Rider</span>
                    <span>Order Status</span>
                    <span>Parcel Stage</span>
                    <span>Updated</span>
                </div>
                <div class="ld-activity-list" role="list" aria-label="Recent logistics orders">
                    @forelse(collect($recentOrders)->take(3) as $order)
                        @php
                            $orderStatusText = strtolower((string) $order->statusLabel());
                            $orderStatusClass = match(true) {
                                str_contains($orderStatusText, 'deliver') => 'bg-[#edf7f1] text-[#4e8063]',
                                str_contains($orderStatusText, 'transit') => 'bg-[#fff6e5] text-[#a56b06]',
                                default => 'bg-[#f3f5f7] text-[#68717b]',
                            };
                            $parcelStage = $order->logisticsParcel ? strtoupper(str_replace('_', ' ', $order->logisticsParcel->status)) : '—';
                        @endphp
                        <article class="ld-activity-row" role="listitem">
                            <div class="ld-activity-order">
                                <span class="ld-activity-order-code">{{ $order->order_number }}</span>
                                <span class="ld-activity-small">{{ $order->seller?->store_name ?: 'SARI Seller' }}</span>
                            </div>
                            <div class="ld-activity-people">
                                <span class="ld-activity-mobile-label">Buyer / Rider</span>
                                <span class="ld-activity-primary">{{ $order->buyer_name }}</span>
                                <span class="ld-activity-small">{{ $order->courier_name ?: 'Unassigned' }}</span>
                            </div>
                            <div class="ld-activity-status-wrap">
                                <span class="ld-activity-status {{ $orderStatusClass }}">{{ $order->statusLabel() }}</span>
                            </div>
                            <div class="ld-activity-parcel">
                                <span class="ld-activity-mobile-label">Parcel Stage</span>
                                <span class="ld-activity-stage">{{ $parcelStage }}</span>
                            </div>
                            <div class="ld-activity-time">
                                <span class="ld-activity-mobile-label">Updated</span>
                                <span class="ld-activity-updated">{{ $order->updated_at?->diffForHumans() }}</span>
                            </div>
                        </article>
                    @empty
                        <div class="px-5 py-10 text-center">
                            <p class="text-[11px] font-semibold text-[#737b84]">No logistics orders yet.</p>
                            <p class="mt-1 text-[10px] text-[#9ba1a9]">Orders assigned to this branch will appear here.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </article>
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
