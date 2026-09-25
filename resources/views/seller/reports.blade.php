@extends('layouts.seller')

@section('title', 'Generate Report — SARI Seller')
@section('page-title', 'Generate Report')

@section('content')
@php
    $showSales = in_array($reportType, ['all', 'sales', 'financial'], true);
    $showOrders = in_array($reportType, ['all', 'orders'], true);
    $showProducts = in_array($reportType, ['all', 'products'], true);

    $downloadUrl = route('seller.reports.download', [
        'from' => $from->format('Y-m-d'),
        'to' => $to->format('Y-m-d'),
        'type' => $reportType,
    ]);

    $statusTone = fn (string $status) => match ($status) {
        'delivered' => 'border-[#cfe4d7] bg-[#f1f8f4] text-[#4f7d63]',
        'cancelled' => 'border-[#efcece] bg-[#fff5f5] text-[#a65353]',
        'new' => 'border-[#eadfc9] bg-[#fff9ef] text-[#a8731f]',
        'preparing' => 'border-[#d5e2ed] bg-[#f3f8fc] text-[#5d7f9d]',
        'ready_for_pickup' => 'border-[#e5d9b9] bg-[#fff9ec] text-[#a67820]',
        default => 'border-[#d7e1eb] bg-[#f4f8fb] text-[#52728c]',
    };
@endphp

@push('styles')
<style>
    :root {
        --report-gold: #d99500;
        --report-gold-hover: #bf8304;
        --report-gold-soft: #fff8eb;
        --report-border: #e7ddd1;
        --report-border-soft: #eee8df;
        --report-text: #211c17;
        --report-muted: #83796f;
        --report-canvas: #fbfaf7;
    }

    .seller-report-page {
        width: 100%;
        max-width: none !important;
        margin: 0;
        padding: 0 0 18px;
        color: var(--report-text);
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        font-weight: 400;
    }

    #sellerReportContent {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    /* Header — matched to the recent Order Management / Archive pages */
    .seller-report-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 14px;
    }

    .seller-report-heading {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 13px;
    }

    .seller-report-heading-icon {
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        place-items: center;
        border: 1px solid #eadfc9;
        border-radius: 12px;
        background: var(--report-gold-soft);
        color: #b77c18;
        box-shadow: 0 4px 12px rgba(75, 54, 25, .045);
    }

    .seller-report-heading-icon svg {
        width: 17px;
        height: 17px;
    }

    .seller-report-eyebrow {
        margin: 0;
        color: #9a6f23;
        font-size: 8px;
        font-weight: 700;
        line-height: 1.15;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .seller-report-title {
        margin: 5px 0 0;
        color: #17130f;
        font-size: 29px;
        font-weight: 700;
        line-height: 1.02;
        letter-spacing: -.045em;
    }

    .seller-report-title-accent {
        margin-left: 4px;
        color: var(--report-gold);
    }

    .seller-report-subtitle {
        max-width: 820px;
        margin: 7px 0 0;
        color: #7f756a;
        font-size: 10.5px;
        line-height: 1.5;
    }

    .seller-report-header-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }

    .seller-report-header-button {
        display: inline-flex;
        height: 40px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border-radius: 10px;
        padding: 0 13px;
        font-size: 8.5px;
        font-weight: 600;
        transition: border-color .14s ease, background-color .14s ease, color .14s ease;
    }

    .seller-report-header-button svg {
        width: 13px;
        height: 13px;
    }

    .seller-report-header-button--secondary {
        border: 1px solid #e6ddd2;
        background: #fff;
        color: #5f574e;
        box-shadow: 0 4px 10px rgba(63, 49, 32, .04);
    }

    .seller-report-header-button--secondary:hover {
        border-color: #d8c4a3;
        background: #fffaf2;
        color: #a8731f;
    }

    .seller-report-header-button--primary {
        border: 1px solid var(--report-gold);
        background: var(--report-gold);
        color: #fff;
        box-shadow: 0 5px 14px rgba(217,149,0,.14);
    }

    .seller-report-header-button--primary:hover {
        border-color: var(--report-gold-hover);
        background: var(--report-gold-hover);
    }

    /* Compact report controls — no oversized filter card */
    .seller-report-filter-panel {
        margin-bottom: 11px;
        border: 1px solid var(--report-border);
        border-radius: 14px;
        background: #fff;
        padding: 9px;
        box-shadow: 0 8px 24px rgba(61, 43, 22, .05);
    }

    .seller-report-filter-grid {
        display: grid;
        grid-template-columns: minmax(150px, .8fr) minmax(150px, .8fr) minmax(170px, .9fr) 110px;
        gap: 8px;
        align-items: end;
    }

    .seller-report-filter-field label {
        display: block;
        margin: 0 0 5px 2px;
        color: #70675d;
        font-size: 8px;
        font-weight: 600;
    }

    .seller-report-filter-field input,
    .seller-report-filter-field select,
    .seller-report-generate {
        width: 100%;
        height: 38px;
        min-height: 38px;
        border-radius: 9px;
        font-family: inherit;
        font-size: 9px;
    }

    .seller-report-filter-field input,
    .seller-report-filter-field select {
        border: 1px solid #e8e0d5;
        background: #fff;
        padding: 0 10px;
        color: #403930;
        outline: none;
        box-shadow: 0 1px 2px rgba(61,43,22,.018), 0 4px 10px rgba(61,43,22,.025);
        transition: border-color .14s ease, box-shadow .14s ease;
    }

    .seller-report-filter-field input:focus,
    .seller-report-filter-field select:focus {
        border-color: #d4af67;
        box-shadow: 0 0 0 3px rgba(217, 148, 0, .07);
    }

    .seller-report-generate {
        border: 1px solid var(--report-gold);
        background: var(--report-gold);
        color: #fff;
        font-weight: 700;
        box-shadow: 0 5px 14px rgba(217,149,0,.13);
    }

    .seller-report-generate:hover {
        border-color: var(--report-gold-hover);
        background: var(--report-gold-hover);
    }

    .seller-report-ranges {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #f0ebe4;
    }

    .seller-report-ranges > span {
        margin-right: 2px;
        color: #92887d;
        font-size: 8px;
        font-weight: 600;
    }

    .seller-report-ranges button {
        min-height: 27px;
        border: 1px solid #e8e0d5;
        border-radius: 999px;
        background: #fff;
        padding: 4px 9px;
        color: #766d63;
        font-size: 7.5px;
        font-weight: 600;
        transition: border-color .12s ease, background-color .12s ease, color .12s ease;
    }

    .seller-report-ranges button:hover {
        border-color: #dcc79e;
        background: #fff8ea;
        color: #9b6c1c;
    }

    /* Sales KPI cards are still useful, but much denser */
    .seller-report-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 9px;
        margin-bottom: 11px;
    }

    .seller-report-kpi-grid .seller-report-card {
        min-height: 84px;
        border-radius: 13px !important;
        padding: 11px 13px !important;
        box-shadow: 0 8px 24px rgba(61,43,22,.05);
        transform: none !important;
    }

    .seller-report-kpi-grid .seller-report-card:hover {
        transform: none !important;
        box-shadow: 0 8px 24px rgba(61,43,22,.05);
    }

    .seller-report-kpi-grid .seller-report-card p:first-child {
        font-size: 9.5px !important;
    }

    .seller-report-kpi-grid .seller-report-card p:nth-child(2) {
        margin-top: 5px !important;
        font-size: 21px !important;
        line-height: 1 !important;
    }

    .seller-report-kpi-grid .seller-report-card p:nth-child(3) {
        margin-top: 6px !important;
        font-size: 8px !important;
        line-height: 1.35 !important;
    }

    .seller-report-kpi-grid .seller-report-card span.grid {
        width: 34px !important;
        height: 34px !important;
        border-radius: 9px !important;
    }

    .seller-report-kpi-grid .seller-report-card span.grid svg {
        width: 14px !important;
        height: 14px !important;
    }

    /* Orders are shown as a slim status strip instead of four large cards */
    .seller-report-order-strip {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        overflow: hidden;
        margin-bottom: 11px;
        border: 1px solid var(--report-border);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(61,43,22,.05);
    }

    .seller-report-order-stat {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px;
        padding: 10px 13px;
        border-right: 1px solid #eee8df;
    }

    .seller-report-order-stat:last-child {
        border-right: 0;
    }

    .seller-report-order-stat-icon {
        display: grid;
        width: 31px;
        height: 31px;
        flex: 0 0 31px;
        place-items: center;
        border-radius: 9px;
    }

    .seller-report-order-stat-icon svg {
        width: 13px;
        height: 13px;
    }

    .seller-report-order-stat-copy {
        min-width: 0;
    }

    .seller-report-order-stat-label {
        margin: 0;
        color: #746b61;
        font-size: 8px;
        font-weight: 600;
    }

    .seller-report-order-stat-value {
        display: inline-block;
        margin-top: 2px;
        color: #211c17;
        font-size: 18px;
        font-weight: 700;
        line-height: 1;
    }

    .seller-report-order-stat-desc {
        margin-left: 5px;
        color: #9a9187;
        font-size: 7.5px;
        white-space: nowrap;
    }

    /* Main report panels */
    .seller-report-panel {
        overflow: hidden;
        border: 1px solid var(--report-border) !important;
        border-radius: 14px !important;
        background: #fff !important;
        box-shadow: 0 8px 24px rgba(61,43,22,.05) !important;
    }

    .seller-report-panel-heading {
        font-size: 15px !important;
        font-weight: 700 !important;
        letter-spacing: -.025em !important;
    }

    .seller-report-panel-subtitle {
        margin-top: 3px !important;
        font-size: 8.5px !important;
        line-height: 1.45 !important;
    }

    .seller-report-chart-shell {
        position: relative;
        min-height: 238px;
    }

    #sellerReportChart {
        height: 238px !important;
    }

    .seller-report-chart-empty {
        position: absolute;
        inset: 0;
        display: none;
        place-items: center;
        pointer-events: none;
    }

    .seller-report-chart-empty.is-visible {
        display: grid;
    }

    .seller-report-chart-path {
        vector-effect: non-scaling-stroke;
    }

    .seller-report-table-row {
        transition: background-color .14s ease;
    }

    .seller-report-table-row:hover {
        background: #fffdfa;
    }

    /* Compact common report sections using their existing markup */
    .seller-report-analysis-grid {
        gap: 10px !important;
        margin-top: 0 !important;
        margin-bottom: 11px;
    }

    .seller-report-analysis-grid > div {
        border-radius: 14px !important;
        padding: 14px !important;
        box-shadow: 0 8px 24px rgba(61,43,22,.05) !important;
    }

    .seller-report-products-section,
    .seller-report-financial-section {
        margin-top: 0 !important;
        margin-bottom: 11px;
        border-radius: 14px !important;
        box-shadow: 0 8px 24px rgba(61,43,22,.05) !important;
    }

    .seller-report-products-section > div:first-child,
    .seller-report-financial-section > div:first-child {
        padding: 12px 14px !important;
    }

    .seller-report-products-section > div:first-child h2,
    .seller-report-financial-section > div:first-child h2 {
        font-size: 15px !important;
    }

    .seller-report-products-section > div:first-child p,
    .seller-report-financial-section > div:first-child p {
        margin-top: 3px !important;
        font-size: 8.5px !important;
    }

    .seller-report-products-section > div:nth-child(2) {
        gap: 8px !important;
        padding: 10px !important;
    }

    .seller-report-products-section article {
        border-radius: 11px !important;
        padding: 10px !important;
    }

    .seller-report-financial-section table th {
        padding-top: 9px !important;
        padding-bottom: 9px !important;
        font-size: 8px !important;
    }

    .seller-report-financial-section table td {
        padding-top: 9px !important;
        padding-bottom: 9px !important;
    }

    @media (max-width: 1279px) {
        .seller-report-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .seller-report-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .seller-report-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .seller-report-header-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .seller-report-order-strip {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .seller-report-order-stat:nth-child(2) {
            border-right: 0;
        }

        .seller-report-order-stat:nth-child(-n+2) {
            border-bottom: 1px solid #eee8df;
        }
    }

    @media (max-width: 639px) {
        .seller-report-title {
            font-size: 24px;
        }

        .seller-report-header-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .seller-report-filter-grid {
            grid-template-columns: 1fr;
        }

        .seller-report-kpi-grid {
            grid-template-columns: 1fr 1fr;
        }

        .seller-report-order-strip {
            grid-template-columns: 1fr;
        }

        .seller-report-order-stat {
            border-right: 0;
            border-bottom: 1px solid #eee8df;
        }

        .seller-report-order-stat:last-child {
            border-bottom: 0;
        }
    }

    @media print {
        .seller-report-no-print,
        #sellerSidebar,
        #sellerHeader,
        #sellerMobileOverlay {
            display: none !important;
        }

        #sellerContent {
            padding-left: 0 !important;
        }

        body {
            background: #fff !important;
        }

        .seller-report-page {
            max-width: none !important;
        }

        .seller-report-card,
        .seller-report-print-section,
        .seller-report-panel,
        .seller-report-order-strip {
            box-shadow: none !important;
            break-inside: avoid;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-report-card,
        .seller-report-table-row,
        .seller-report-header-button,
        .seller-report-generate {
            transition: none !important;
        }
    }
</style>
@endpush

<div id="sellerReportStage" class="seller-report-page">

    <div id="sellerReportContent">

        {{-- HEADER — MATCHED TO ORDER MANAGEMENT --}}
        <section class="seller-report-no-print seller-report-header">
            <div class="seller-report-heading">
                <span class="seller-report-heading-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 20h16"></path>
                        <path d="M7 17v-5"></path>
                        <path d="M12 17V8"></path>
                        <path d="M17 17V4"></path>
                    </svg>
                </span>

                <div class="min-w-0">
                    <p class="seller-report-eyebrow">REPORTING & ANALYTICS</p>
                    <h1 class="seller-report-title">
                        <span>Seller</span><span class="seller-report-title-accent">Reports</span>
                    </h1>
                    <p class="seller-report-subtitle">
                        Review sales, commission, orders, and product performance from real marketplace records.
                    </p>
                </div>
            </div>

            <div class="seller-report-header-actions">
                <a href="{{ $downloadUrl }}" class="seller-report-header-button seller-report-header-button--secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 3v12"></path>
                        <path d="m7 10 5 5 5-5"></path>
                        <path d="M5 21h14"></path>
                    </svg>
                    Download CSV
                </a>

                <button
                    id="printSellerReport"
                    type="button"
                    class="seller-report-header-button seller-report-header-button--primary"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M6 9V3h12v6"></path>
                        <path d="M6 18H4V9h16v9h-2"></path>
                        <path d="M7 14h10v7H7z"></path>
                    </svg>
                    Print Report
                </button>
            </div>
        </section>

        {{-- FILTERS — COMPACT CONTROL BAR --}}
        <section class="seller-report-no-print seller-report-filter-panel">
            <form
                id="sellerReportFilterForm"
                method="GET"
                action="{{ route('seller.reports') }}"
                class="seller-report-filter-grid"
            >
                <div class="seller-report-filter-field">
                    <label for="reportFromDate">From Date</label>
                    <input
                        id="reportFromDate"
                        name="from"
                        type="date"
                        value="{{ $from->format('Y-m-d') }}"
                    >
                </div>

                <div class="seller-report-filter-field">
                    <label for="reportToDate">To Date</label>
                    <input
                        id="reportToDate"
                        name="to"
                        type="date"
                        value="{{ $to->format('Y-m-d') }}"
                    >
                </div>

                <div class="seller-report-filter-field">
                    <label for="reportType">Report Type</label>
                    <select name="type" id="reportType">
                        <option value="all" @selected($reportType === 'all')>All Reports</option>
                        <option value="sales" @selected($reportType === 'sales')>Sales Report</option>
                        <option value="financial" @selected($reportType === 'financial')>Financial Report</option>
                        <option value="orders" @selected($reportType === 'orders')>Order Performance</option>
                        <option value="products" @selected($reportType === 'products')>Product Performance</option>
                    </select>
                </div>

                <button type="submit" class="seller-report-generate">Generate</button>
            </form>

            <div class="seller-report-ranges">
                <span>Quick range</span>
                @foreach ([
                    'today' => 'Today',
                    '7days' => 'Last 7 Days',
                    '30days' => 'Last 30 Days',
                    'month' => 'This Month',
                    'year' => 'This Year',
                ] as $range => $label)
                    <button type="button" data-report-range="{{ $range }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </section>

        {{-- SALES / FINANCIAL KPI METRICS --}}
        @if ($showSales)
            <section class="seller-report-print-section seller-report-kpi-grid">
                <article class="seller-report-card rounded-[17px] border border-[#d4e6db] bg-white px-4 py-5 sm:px-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-medium text-[#5f574f]">Gross Sales</p>
                            <p class="mt-2.5 text-[28px] font-semibold tracking-[-.04em] text-[#211d18]">
                                ₱{{ number_format($summary['gross_sales'], 2) }}
                            </p>
                            <p class="mt-3 text-[9.5px] font-normal {{ $summary['sales_change'] >= 0 ? 'text-[#4f7d63]' : 'text-[#a65353]' }}">
                                {{ $summary['sales_change'] >= 0 ? '▲' : '▼' }}
                                {{ number_format(abs($summary['sales_change']), 1) }}% vs previous period
                            </p>
                        </div>
                        <span class="grid h-11 w-11 place-items-center rounded-[12px] bg-[#f1f8f4] text-[#4f7d63]">
                            <span class="text-[18px] font-medium">₱</span>
                        </span>
                    </div>
                </article>

                <article class="seller-report-card rounded-[17px] border border-[#eadbbd] bg-white px-4 py-5 sm:px-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-medium text-[#5f574f]">Platform Commission</p>
                            <p class="mt-2.5 text-[28px] font-semibold tracking-[-.04em] text-[#211d18]">
                                ₱{{ number_format($summary['platform_commission'], 2) }}
                            </p>
                            <p class="mt-3 text-[9.5px] font-normal text-[#9b6c1c]">
                                {{ number_format($summary['commission_rate'], 0) }}% on delivered merchandise sales
                            </p>
                        </div>
                        <span class="grid h-11 w-11 place-items-center rounded-[12px] bg-[#fff7e9] text-[#b77c16]">
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="8"></circle>
                                <path d="M9 9h5a2 2 0 1 1 0 4h-4a2 2 0 1 0 0 4h5"></path>
                            </svg>
                        </span>
                    </div>
                </article>

                <article class="seller-report-card rounded-[17px] border border-[#d8e4f0] bg-white px-4 py-5 sm:px-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-medium text-[#5f574f]">Net Revenue</p>
                            <p class="mt-2.5 text-[28px] font-semibold tracking-[-.04em] text-[#211d18]">
                                ₱{{ number_format($summary['net_revenue'], 2) }}
                            </p>
                            <p class="mt-3 text-[9.5px] font-normal text-[#71879a]">
                                Revenue after SARI commission
                            </p>
                        </div>
                        <span class="grid h-11 w-11 place-items-center rounded-[12px] bg-[#f0f6fb] text-[#4e80aa]">
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 18 9 13l3 3 7-8"></path>
                                <path d="M15 8h4v4"></path>
                            </svg>
                        </span>
                    </div>
                </article>

                <article class="seller-report-card rounded-[17px] border border-[#ddd8e8] bg-white px-4 py-5 sm:px-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-medium text-[#5f574f]">Average Order Value</p>
                            <p class="mt-2.5 text-[28px] font-semibold tracking-[-.04em] text-[#211d18]">
                                ₱{{ number_format($summary['average_order_value'], 2) }}
                            </p>
                            <p class="mt-3 text-[9.5px] font-normal text-[#7b6e88]">
                                Based on delivered orders
                            </p>
                        </div>
                        <span class="grid h-11 w-11 place-items-center rounded-[12px] bg-[#f6f3f9] text-[#75618a]">
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 20h16"></path>
                                <path d="M7 16v-4"></path>
                                <path d="M12 16V8"></path>
                                <path d="M17 16V5"></path>
                            </svg>
                        </span>
                    </div>
                </article>
            
            </section>
        @endif

        @if ($showSales)
            {{-- CHART + PERFORMANCE --}}
            <section class="seller-report-print-section seller-report-analysis-grid grid grid-cols-1 xl:grid-cols-[1.3fr_.7fr]">
                <div class="seller-report-panel rounded-[14px] border bg-white p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="seller-report-panel-heading text-[#24201b]">Sales & Revenue Trend</h2>
                            <p class="seller-report-panel-subtitle text-[#887f75]">
                                Delivered merchandise sales from {{ $from->format('M d, Y') }} to {{ $to->format('M d, Y') }}.
                            </p>
                        </div>
                        <div class="flex gap-3 text-[9px] text-[#756d63]">
                            <span class="inline-flex items-center gap-1.5"><span class="h-2 w-5 rounded-full bg-[#d89412]"></span>Sales</span>
                            <span class="inline-flex items-center gap-1.5"><span class="h-2 w-5 rounded-full bg-[#8fa5b6]"></span>Net Revenue</span>
                        </div>
                    </div>

                    <div class="seller-report-chart-shell mt-4 overflow-x-auto rounded-[14px] border border-[#eee8df] bg-[#fdfbf8] p-3">
                        <svg
                            id="sellerReportChart"
                            viewBox="0 0 860 310"
                            class="block h-[238px] min-w-[700px] w-full"
                            preserveAspectRatio="xMidYMid meet"
                            aria-label="Seller report sales chart"
                        >
                            <defs>
                                <linearGradient id="sellerReportAreaFill" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#d89412" stop-opacity=".14"></stop>
                                    <stop offset="100%" stop-color="#d89412" stop-opacity=".015"></stop>
                                </linearGradient>
                            </defs>

                            <g id="sellerReportGrid"></g>
                            <path id="sellerReportSalesArea" fill="url(#sellerReportAreaFill)"></path>
                            <path id="sellerReportSalesLine" class="seller-report-chart-path" fill="none" stroke="#d89412" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path id="sellerReportNetLine" class="seller-report-chart-path" fill="none" stroke="#8fa5b6" stroke-width="2.3" stroke-dasharray="7 6" stroke-linecap="round" stroke-linejoin="round"></path>
                            <g id="sellerReportSalesPoints"></g>
                            <g id="sellerReportXAxis"></g>
                        </svg>

                        <div id="sellerReportChartEmpty" class="seller-report-chart-empty">
                            <div class="rounded-[12px] border border-[#ebe4da] bg-white/90 px-4 py-3 text-center shadow-[0_6px_20px_rgba(47,37,25,.04)]">
                                <p class="text-[9.5px] font-medium text-[#5a5249]">No delivered sales in this period.</p>
                                <p class="mt-1 text-[8px] text-[#91887d]">Try a wider date range to see the sales trend.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="seller-report-panel rounded-[14px] border bg-white p-4">
                    <h2 class="seller-report-panel-heading text-[#24201b]">Performance Summary</h2>
                    <p class="seller-report-panel-subtitle text-[#887f75]">Calculated from real seller order records.</p>

                    <div class="mt-4 space-y-3">
                        @foreach ([
                            ['Order Completion Rate', $summary['completion_rate'], '#4f7d63'],
                            ['Cancellation Rate', $summary['cancellation_rate'], '#a65353'],
                        ] as [$label, $value, $color])
                            <div class="rounded-[13px] border border-[#eee8df] bg-[#fdfbf8] p-3.5">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-[10px] font-medium text-[#514a42]">{{ $label }}</span>
                                    <span class="text-[11px] font-semibold" style="color:{{ $color }}">{{ number_format($value, 1) }}%</span>
                                </div>
                                <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-[#ebe6df]">
                                    <div class="h-full rounded-full" style="width:{{ min(100, $value) }}%;background:{{ $color }}"></div>
                                </div>
                            </div>
                        @endforeach

                        <div class="rounded-[13px] border border-[#eee8df] bg-[#fdfbf8] p-3.5">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[10px] font-medium text-[#514a42]">Customer Rating</span>
                                <span class="text-[11px] font-semibold text-[#a8731f]">
                                    {{ $summary['customer_rating'] !== null ? number_format($summary['customer_rating'], 1) . ' / 5' : 'No ratings yet' }}
                                </span>
                            </div>
                            <p class="mt-2 text-[8.5px] text-[#91887d]">Uses real product reviews when available.</p>
                        </div>

                        <div class="rounded-[13px] border border-[#eee8df] bg-[#fdfbf8] p-3.5">
                            <p class="text-[10px] font-medium text-[#514a42]">Revenue basis</p>
                            <p class="mt-2 text-[8.5px] leading-4 text-[#91887d]">
                                Gross sales include delivered merchandise subtotals only. Delivery fees are excluded. Product cost is not currently tracked, so this page reports net revenue rather than accounting profit.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        @if ($showProducts)
            {{-- TOP PRODUCTS --}}
            <section class="seller-report-print-section seller-report-products-section overflow-hidden border border-[#e9e2d9] bg-white">
                <div class="border-b border-[#eee8df] px-5 py-5">
                    <h2 class="text-[20px] font-semibold tracking-[-.025em] text-[#24201b]">Top Selling Products</h2>
                    <p class="mt-1 text-[10.5px] text-[#887f75]">Ranked using real quantities and line totals from delivered order snapshots.</p>
                </div>

                <div class="grid grid-cols-1 gap-3 p-4 md:grid-cols-2 xl:grid-cols-5">
                    @forelse ($topProducts as $index => $product)
                        @php
                            $maxProductSales = max(1, (float) collect($topProducts)->max('sales'));
                            $width = min(100, (($product['sales'] / $maxProductSales) * 100));
                        @endphp
                        <article class="rounded-[15px] border border-[#eee8df] bg-[#fdfbf8] p-4">
                            <div class="flex items-center justify-between">
                                <span class="grid h-9 w-9 place-items-center rounded-[10px] border border-[#eadfc9] bg-white text-[10px] font-semibold text-[#a8731f]">{{ $index + 1 }}</span>
                                <span class="text-[9px] font-medium text-[#8f867c]">{{ $product['quantity'] }} sold</span>
                            </div>
                            <p class="mt-3 truncate text-[11px] font-semibold text-[#39332d]">{{ $product['name'] }}</p>
                            <p class="mt-2 text-[13px] font-semibold text-[#d39116]">₱{{ number_format($product['sales'], 2) }}</p>
                            <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-[#ebe6df]">
                                <div class="h-full rounded-full bg-[#d7aa4f]" style="width:{{ $width }}%"></div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-[15px] border border-dashed border-[#ded5c9] bg-[#fcfbf8] px-6 py-9 text-center">
                            <p class="text-[10px] font-medium text-[#514a42]">No delivered product sales in this period.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        @endif

        @if ($showOrders)
            {{-- ORDER STATUS OVERVIEW — compact strip, not summary cards --}}
            <section class="seller-report-print-section seller-report-order-strip" aria-label="Order status overview">
                <div class="seller-report-order-stat">
                    <span class="seller-report-order-stat-icon bg-[#f0f6fb] text-[#4e80aa]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="8"></circle>
                            <path d="M12 8v5l3 2"></path>
                        </svg>
                    </span>
                    <div class="seller-report-order-stat-copy">
                        <p class="seller-report-order-stat-label">Total Orders</p>
                        <div>
                            <strong class="seller-report-order-stat-value">{{ $summary['total_orders'] }}</strong>
                            <span class="seller-report-order-stat-desc">All orders</span>
                        </div>
                    </div>
                </div>

                <div class="seller-report-order-stat">
                    <span class="seller-report-order-stat-icon bg-[#f1f8f4] text-[#4f7d63]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="8"></circle>
                            <path d="m8 12 2.5 2.5L16 9"></path>
                        </svg>
                    </span>
                    <div class="seller-report-order-stat-copy">
                        <p class="seller-report-order-stat-label">Delivered</p>
                        <div>
                            <strong class="seller-report-order-stat-value">{{ $summary['completed_orders'] }}</strong>
                            <span class="seller-report-order-stat-desc">Completed</span>
                        </div>
                    </div>
                </div>

                <div class="seller-report-order-stat">
                    <span class="seller-report-order-stat-icon bg-[#fff7e9] text-[#b77c16]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 6h16v13H4z"></path>
                            <path d="M8 6V4h8v2"></path>
                            <path d="M8 11h8"></path>
                        </svg>
                    </span>
                    <div class="seller-report-order-stat-copy">
                        <p class="seller-report-order-stat-label">Active</p>
                        <div>
                            <strong class="seller-report-order-stat-value">{{ $summary['active_orders'] }}</strong>
                            <span class="seller-report-order-stat-desc">In fulfillment</span>
                        </div>
                    </div>
                </div>

                <div class="seller-report-order-stat">
                    <span class="seller-report-order-stat-icon bg-[#fff6f6] text-[#aa5a5a]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="8"></circle>
                            <path d="m9 9 6 6"></path>
                            <path d="m15 9-6 6"></path>
                        </svg>
                    </span>
                    <div class="seller-report-order-stat-copy">
                        <p class="seller-report-order-stat-label">Cancelled</p>
                        <div>
                            <strong class="seller-report-order-stat-value">{{ $summary['cancelled_orders'] }}</strong>
                            <span class="seller-report-order-stat-desc">Cancelled</span>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- FINANCIAL / ORDER TRANSACTIONS --}}
        @if ($reportType !== 'products')
            <section class="seller-report-print-section seller-report-financial-section overflow-hidden border border-[#e9e2d9] bg-white">
                <div class="flex flex-col gap-3 border-b border-[#eee8df] px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-[20px] font-semibold tracking-[-.025em] text-[#24201b]">Financial Breakdown</h2>
                        <p class="mt-1 text-[10.5px] text-[#887f75]">COD marketplace orders from the selected report period.</p>
                    </div>
                    <span class="rounded-full border border-[#e8dfd1] bg-[#fffaf2] px-3 py-1.5 text-[9px] font-medium text-[#9b6c1c]">
                        Showing latest {{ $transactions->count() }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[980px] text-left">
                        <thead>
                            <tr class="border-b border-[#eee8df] bg-[#fdfbf8]">
                                @foreach (['Order', 'Buyer', 'Date', 'Status', 'Gross Sales', 'Commission', 'Net Revenue', 'Payment'] as $head)
                                    <th class="px-5 py-3.5 text-[9.5px] font-medium text-[#786f65]">{{ $head }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr class="seller-report-table-row border-b border-[#f1ece5] last:border-b-0">
                                    <td class="px-5 py-4 text-[10px] font-semibold text-[#302a24]">{{ $transaction['order_number'] }}</td>
                                    <td class="px-5 py-4 text-[9.5px] font-medium text-[#4a443d]">{{ $transaction['buyer_name'] }}</td>
                                    <td class="px-5 py-4 text-[9px] text-[#91887e]">{{ $transaction['date']?->format('M d, Y') }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full border px-2.5 py-1.5 text-[9px] font-medium {{ $statusTone($transaction['status']) }}">
                                            {{ $transaction['status_label'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-[10px] font-semibold text-[#302a24]">₱{{ number_format($transaction['gross'], 2) }}</td>
                                    <td class="px-5 py-4 text-[9.5px] font-medium text-[#9b6c1c]">₱{{ number_format($transaction['commission'], 2) }}</td>
                                    <td class="px-5 py-4 text-[10px] font-semibold text-[#4f7d63]">₱{{ number_format($transaction['net'], 2) }}</td>
                                    <td class="px-5 py-4">
                                        <p class="text-[9.5px] font-medium text-[#4a443d]">COD</p>
                                        <p class="mt-1 text-[8.5px] text-[#91887e]">{{ $transaction['payment_status'] }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-[10px] text-[#91887d]">No orders in this report period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    /*
    |--------------------------------------------------------------------------
    | SELLER REPORT PAGE — STABLE NAVIGATION LIFECYCLE
    |--------------------------------------------------------------------------
    | The lifecycle listeners are bound once globally. On every Livewire
    | navigation the object below is replaced with the newest report data,
    | preventing duplicated event handlers and slowdowns over time.
    */
    window.SariSellerReportPage = {
        chartData: @json($chart),
        abortController: null,

        reset() {
            if (this.abortController) {
                this.abortController.abort();
            }

            this.abortController = new AbortController();

        },

        init() {
            this.reset();

            const signal = this.abortController.signal;
            const form = document.getElementById('sellerReportFilterForm');
            const fromInput = document.getElementById('reportFromDate');
            const toInput = document.getElementById('reportToDate');
            const generateButton = form?.querySelector('button[type="submit"]');

            const dateValue = (date) => {
                const y = date.getFullYear();
                const m = String(date.getMonth() + 1).padStart(2, '0');
                const d = String(date.getDate()).padStart(2, '0');

                return `${y}-${m}-${d}`;
            };

            const navigateToReport = () => {
                if (!form) return;

                const params = new URLSearchParams(new FormData(form));
                const url = `${form.action}?${params.toString()}`;

                if (generateButton) {
                    generateButton.disabled = true;
                    generateButton.classList.add('opacity-70', 'cursor-wait');
                }

                if (window.Livewire?.navigate) {
                    window.Livewire.navigate(url);
                } else {
                    window.location.assign(url);
                }
            };

            form?.addEventListener('submit', (event) => {
                event.preventDefault();

                if (!fromInput?.value || !toInput?.value) return;

                if (fromInput.value > toInput.value) {
                    const temp = fromInput.value;
                    fromInput.value = toInput.value;
                    toInput.value = temp;
                }

                navigateToReport();
            }, { signal });

            document.querySelectorAll('[data-report-range]').forEach((button) => {
                button.addEventListener('click', () => {
                    const type = button.dataset.reportRange;
                    const now = new Date();
                    let start = new Date(now);
                    let end = new Date(now);

                    if (type === '7days') {
                        start.setDate(now.getDate() - 6);
                    } else if (type === '30days') {
                        start.setDate(now.getDate() - 29);
                    } else if (type === 'month') {
                        start = new Date(now.getFullYear(), now.getMonth(), 1);
                    } else if (type === 'year') {
                        start = new Date(now.getFullYear(), 0, 1);
                    }

                    if (fromInput) fromInput.value = dateValue(start);
                    if (toInput) toInput.value = dateValue(end);

                    navigateToReport();
                }, { signal });
            });

            document.getElementById('printSellerReport')
                ?.addEventListener('click', () => window.print(), { signal });

            this.renderChart();
        },

        renderChart() {
            const data = this.chartData || {};
            const svg = document.getElementById('sellerReportChart');
            const grid = document.getElementById('sellerReportGrid');
            const salesArea = document.getElementById('sellerReportSalesArea');
            const salesLine = document.getElementById('sellerReportSalesLine');
            const netLine = document.getElementById('sellerReportNetLine');
            const pointsGroup = document.getElementById('sellerReportSalesPoints');
            const xAxis = document.getElementById('sellerReportXAxis');
            const emptyState = document.getElementById('sellerReportChartEmpty');

            if (!svg || !grid || !salesArea || !salesLine || !netLine || !pointsGroup || !xAxis) {
                return;
            }

            grid.innerHTML = '';
            pointsGroup.innerHTML = '';
            xAxis.innerHTML = '';
            salesArea.setAttribute('d', '');
            salesLine.setAttribute('d', '');
            netLine.setAttribute('d', '');

            const labels = Array.isArray(data.labels) ? data.labels : [];
            const sales = Array.isArray(data.sales) ? data.sales.map(Number) : [];
            const net = Array.isArray(data.net) ? data.net.map(Number) : [];

            const count = Math.max(labels.length, sales.length, net.length);
            const hasSales = Math.max(0, ...sales, ...net) > 0;

            emptyState?.classList.toggle('is-visible', !hasSales);

            const chart = {
                left: 72,
                right: 830,
                top: 24,
                bottom: 246,
            };

            const width = chart.right - chart.left;
            const height = chart.bottom - chart.top;

            const maxRaw = Math.max(0, ...sales, ...net);
            const maxValue = this.niceMaximum(maxRaw);

            for (let index = 0; index <= 4; index++) {
                const ratio = index / 4;
                const y = chart.top + height * ratio;
                const value = maxValue * (1 - ratio);

                grid.appendChild(this.svgElement('line', {
                    x1: chart.left,
                    y1: y,
                    x2: chart.right,
                    y2: y,
                    stroke: index === 4 ? '#dcd4ca' : '#eee8e0',
                    'stroke-dasharray': index === 4 ? '0' : '4 5',
                }));

                grid.appendChild(this.svgElement('text', {
                    x: 7,
                    y: y + 4,
                    fill: '#8f867c',
                    'font-size': 9.5,
                    'font-family': 'Poppins, sans-serif',
                    'font-weight': 400,
                }, this.compactPeso(value)));
            }

            if (!count) {
                return;
            }

            const salesPoints = this.makeChartPoints(sales, count, chart, maxValue);
            const netPoints = this.makeChartPoints(net, count, chart, maxValue);

            /*
            | Smooth Catmull-Rom → cubic Bézier curves.
            | This keeps every real data point while avoiding the harsh zig-zag
            | appearance of straight line segments.
            */
            const salesPath = this.smoothPath(salesPoints);
            const netPath = this.smoothPath(netPoints);

            salesLine.setAttribute('d', salesPath);
            netLine.setAttribute('d', netPath);

            if (salesPoints.length) {
                const first = salesPoints[0];
                const last = salesPoints[salesPoints.length - 1];

                salesArea.setAttribute(
                    'd',
                    `${salesPath} L${last.x.toFixed(2)} ${chart.bottom} L${first.x.toFixed(2)} ${chart.bottom} Z`
                );
            }

            /*
            | Keep labels readable. All points remain in the graph; only axis
            | labels are thinned when there are many buckets.
            */
            const maxVisibleLabels = 8;
            const labelStep = Math.max(1, Math.ceil(count / maxVisibleLabels));

            salesPoints.forEach((point, index) => {
                const circle = this.svgElement('circle', {
                    cx: point.x,
                    cy: point.y,
                    r: index === salesPoints.length - 1 ? 4.8 : 3.8,
                    fill: '#fff',
                    stroke: '#d89412',
                    'stroke-width': 2,
                });

                circle.appendChild(
                    this.svgElement(
                        'title',
                        {},
                        `${labels[index] || 'Period'} — Sales ${this.peso(point.value)}`
                    )
                );

                pointsGroup.appendChild(circle);

                const shouldShowLabel =
                    index === 0 ||
                    index === count - 1 ||
                    index % labelStep === 0;

                if (shouldShowLabel) {
                    xAxis.appendChild(this.svgElement('text', {
                        x: point.x,
                        y: 284,
                        fill: '#81786e',
                        'font-size': 8.8,
                        'font-family': 'Poppins, sans-serif',
                        'font-weight': 400,
                        'text-anchor': 'middle',
                    }, labels[index] || ''));
                }
            });
        },

        makeChartPoints(values, count, chart, maxValue) {
            const width = chart.right - chart.left;
            const height = chart.bottom - chart.top;

            return Array.from({ length: count }, (_, index) => {
                const x = count === 1
                    ? chart.left + width / 2
                    : chart.left + width * index / (count - 1);

                const value = Math.max(0, Number(values[index] || 0));
                const y = chart.bottom - (value / maxValue) * height;

                return { x, y, value };
            });
        },

        smoothPath(points) {
            if (!points.length) return '';
            if (points.length === 1) {
                return `M${points[0].x.toFixed(2)} ${points[0].y.toFixed(2)}`;
            }
            if (points.length === 2) {
                return `M${points[0].x.toFixed(2)} ${points[0].y.toFixed(2)} L${points[1].x.toFixed(2)} ${points[1].y.toFixed(2)}`;
            }

            const tension = 0.16;
            let path = `M${points[0].x.toFixed(2)} ${points[0].y.toFixed(2)}`;

            for (let i = 0; i < points.length - 1; i++) {
                const p0 = points[i - 1] || points[i];
                const p1 = points[i];
                const p2 = points[i + 1];
                const p3 = points[i + 2] || p2;

                const cp1x = p1.x + (p2.x - p0.x) * tension;
                const cp1y = p1.y + (p2.y - p0.y) * tension;
                const cp2x = p2.x - (p3.x - p1.x) * tension;
                const cp2y = p2.y - (p3.y - p1.y) * tension;

                path += ` C${cp1x.toFixed(2)} ${cp1y.toFixed(2)}, ${cp2x.toFixed(2)} ${cp2y.toFixed(2)}, ${p2.x.toFixed(2)} ${p2.y.toFixed(2)}`;
            }

            return path;
        },

        niceMaximum(value) {
            const raw = Number(value || 0);
            if (raw <= 0) return 1000;

            const roughStep = raw / 4;
            const magnitude = Math.pow(10, Math.floor(Math.log10(roughStep)));
            const normalized = roughStep / magnitude;

            let niceStep;
            if (normalized <= 1) niceStep = 1;
            else if (normalized <= 2) niceStep = 2;
            else if (normalized <= 5) niceStep = 5;
            else niceStep = 10;

            niceStep *= magnitude;

            return Math.max(niceStep * 4, raw);
        },

        svgElement(name, attrs = {}, content = '') {
            const element = document.createElementNS('http://www.w3.org/2000/svg', name);

            Object.entries(attrs).forEach(([key, value]) => {
                element.setAttribute(key, String(value));
            });

            if (content !== '') {
                element.textContent = content;
            }

            return element;
        },

        compactPeso(value) {
            const amount = Number(value || 0);
            const absolute = Math.abs(amount);

            if (absolute >= 1000000) {
                return '₱' + (amount / 1000000).toFixed(absolute >= 10000000 ? 0 : 1).replace(/\.0$/, '') + 'M';
            }

            if (absolute >= 1000) {
                return '₱' + (amount / 1000).toFixed(absolute >= 100000 ? 0 : 1).replace(/\.0$/, '') + 'K';
            }

            return '₱' + Math.round(amount).toLocaleString('en-PH');
        },

        peso(value) {
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(Number(value || 0)).replace('PHP', '₱').replace(/\s/g, '');
        },
    };

    /*
    | Bind the Livewire lifecycle only once, even if this Blade script is
    | evaluated again after navigation.
    */
    if (!window.__SARI_REPORT_LIFECYCLE_BOUND__) {
        /* The page script itself runs on arrival. Keep only the persistent
           cleanup hook; otherwise Reports initializes twice per visit. */
        document.addEventListener('livewire:navigating', () => {
            window.SariSellerReportPage?.reset();
        });

        window.__SARI_REPORT_LIFECYCLE_BOUND__ = true;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.SariSellerReportPage?.init();
        }, { once: true });
    } else {
        window.SariSellerReportPage.init();
    }
})();
</script>
@endpush
