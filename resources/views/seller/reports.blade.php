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
<link rel="stylesheet" href="{{ asset('css/seller/reports.css') }}?v=20261007-1">

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
                    <label id="reportTypeLabel" for="reportType">Report Type</label>

                    <div class="seller-report-type-dropdown" data-report-type-dropdown>
                        <select
                            name="type"
                            id="reportType"
                            class="seller-report-type-native"
                            aria-hidden="true"
                            tabindex="-1"
                            data-report-type-native
                        >
                            <option value="all" @selected($reportType === 'all')>All Reports</option>
                            <option value="sales" @selected($reportType === 'sales')>Sales Report</option>
                            <option value="financial" @selected($reportType === 'financial')>Financial Report</option>
                            <option value="orders" @selected($reportType === 'orders')>Order Performance</option>
                            <option value="products" @selected($reportType === 'products')>Product Performance</option>
                        </select>

                        <button
                            id="reportTypeButton"
                            type="button"
                            class="seller-report-type-button"
                            aria-labelledby="reportTypeLabel reportTypeButtonValue"
                            aria-haspopup="listbox"
                            aria-expanded="false"
                            aria-controls="reportTypeMenu"
                            data-report-type-button
                        >
                            <span class="seller-report-type-button-main">
                                <span class="seller-report-type-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M5 5h14v4H5z"></path>
                                        <path d="M5 11h6v8H5z"></path>
                                        <path d="M13 11h6v3h-6z"></path>
                                        <path d="M13 16h6v3h-6z"></path>
                                    </svg>
                                </span>

                                <span id="reportTypeButtonValue" class="seller-report-type-current" data-report-type-label>
                                    All Reports
                                </span>
                            </span>

                            <svg class="seller-report-type-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="m7 10 5 5 5-5"></path>
                            </svg>
                        </button>

                        <div
                            id="reportTypeMenu"
                            class="seller-report-type-menu"
                            role="listbox"
                            aria-labelledby="reportTypeLabel"
                            hidden
                            data-report-type-menu
                        >
                            @foreach ([
                                ['all', 'All Reports', 'Sales, orders, products, and finance'],
                                ['sales', 'Sales Report', 'Revenue trend and sales performance'],
                                ['financial', 'Financial Report', 'Commission and net revenue'],
                                ['orders', 'Order Performance', 'Fulfillment and order status'],
                                ['products', 'Product Performance', 'Top products and quantities sold'],
                            ] as [$value, $label, $description])
                                <button
                                    type="button"
                                    class="seller-report-type-option"
                                    role="option"
                                    data-report-type-value="{{ $value }}"
                                >
                                    <span class="seller-report-type-option-copy">
                                        <strong>{{ $label }}</strong>
                                        <small>{{ $description }}</small>
                                    </span>

                                    <svg class="seller-report-type-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="m6 12 4 4 8-8"></path>
                                    </svg>
                                </button>
                            @endforeach
                        </div>
                    </div>
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
            <section class="seller-report-print-section seller-report-analysis-grid seller-report-analytics-layout">
                <article class="seller-report-panel seller-report-trend-panel">
                    <div class="seller-report-analytics-head">
                        <div class="seller-report-analytics-heading">
                            <div>
                                <h2 class="seller-report-panel-heading">Sales & Revenue Trend</h2>
                                <p class="seller-report-panel-subtitle">
                                    Delivered merchandise sales · {{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="seller-report-chart-legend" aria-label="Chart legend">
                            <span class="seller-report-legend-item">
                                <span class="seller-report-legend-line seller-report-legend-line--sales" aria-hidden="true"></span>
                                Sales
                            </span>
                            <span class="seller-report-legend-item">
                                <span class="seller-report-legend-line seller-report-legend-line--net" aria-hidden="true"></span>
                                Net Revenue
                            </span>
                        </div>
                    </div>

                    <div class="seller-report-chart-shell seller-report-chart-shell--modern">
                        <svg
                            id="sellerReportChart"
                            viewBox="0 0 860 310"
                            class="block min-w-[700px] w-full"
                            preserveAspectRatio="xMidYMid meet"
                            role="img"
                            aria-label="Sales and net revenue trend for the selected report period"
                        >
                            <defs>
                                <linearGradient id="sellerReportAreaFill" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#d59617" stop-opacity=".12"></stop>
                                    <stop offset="100%" stop-color="#d59617" stop-opacity=".012"></stop>
                                </linearGradient>
                            </defs>

                            <g id="sellerReportGrid"></g>
                            <path id="sellerReportSalesArea" fill="url(#sellerReportAreaFill)"></path>
                            <path id="sellerReportSalesLine" class="seller-report-chart-path" fill="none" stroke="#d59617" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path id="sellerReportNetLine" class="seller-report-chart-path" fill="none" stroke="#7d93a5" stroke-width="2.2" stroke-dasharray="7 6" stroke-linecap="round" stroke-linejoin="round"></path>
                            <g id="sellerReportSalesPoints"></g>
                            <g id="sellerReportXAxis"></g>
                        </svg>

                        <div id="sellerReportChartEmpty" class="seller-report-chart-empty">
                            <div class="seller-report-chart-empty-card">
                                <span class="seller-report-chart-empty-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 18 9 13l3 3 7-8"></path>
                                        <path d="M15 8h4v4"></path>
                                    </svg>
                                </span>
                                <p>No delivered sales in this period.</p>
                                <small>Try a wider date range to see the trend.</small>
                            </div>
                        </div>
                    </div>
                </article>

                <aside class="seller-report-panel seller-report-performance-panel">
                    <div class="seller-report-performance-head">
                        <div>
                            <h2 class="seller-report-panel-heading">Performance Summary</h2>
                            <p class="seller-report-panel-subtitle">Calculated from real Seller order records.</p>
                        </div>
                    </div>

                    <div class="seller-report-performance-list">
                        @foreach ([
                            ['Order Completion Rate', $summary['completion_rate'], 'completed', '#4f7d63'],
                            ['Cancellation Rate', $summary['cancellation_rate'], 'cancelled', '#a65353'],
                        ] as [$label, $value, $tone, $color])
                            <div class="seller-report-performance-row">
                                <div class="seller-report-performance-row-head">
                                    <span class="seller-report-performance-label">{{ $label }}</span>
                                    <strong class="seller-report-performance-value seller-report-performance-value--{{ $tone }}">
                                        {{ number_format($value, 1) }}%
                                    </strong>
                                </div>

                                <div
                                    class="seller-report-performance-track"
                                    role="progressbar"
                                    aria-label="{{ $label }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100"
                                    aria-valuenow="{{ number_format(min(100, max(0, $value)), 1, '.', '') }}"
                                >
                                    <span style="width: {{ min(100, max(0, $value)) }}%; background: {{ $color }};"></span>
                                </div>
                            </div>
                        @endforeach

                        <div class="seller-report-performance-row seller-report-performance-row--rating">
                            <div class="seller-report-performance-row-head">
                                <span class="seller-report-performance-label">Customer Rating</span>

                                <strong class="seller-report-rating-value">
                                    {{ $summary['customer_rating'] !== null
                                        ? number_format($summary['customer_rating'], 1) . ' / 5'
                                        : 'No ratings yet' }}
                                </strong>
                            </div>

                            <div class="seller-report-rating-note">
                                <span class="seller-report-rating-star" aria-hidden="true">★</span>
                                Uses real product reviews when available.
                            </div>
                        </div>
                    </div>

                    <div class="seller-report-revenue-note">
                        <span class="seller-report-revenue-note-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="8"></circle>
                                <path d="M12 11v5"></path>
                                <path d="M12 8h.01"></path>
                            </svg>
                        </span>

                        <div>
                            <strong>Revenue basis</strong>
                            <p>
                                Gross sales use delivered merchandise subtotals only. Delivery fees are excluded.
                                Product cost is not tracked yet, so this report shows net revenue rather than accounting profit.
                            </p>
                        </div>
                    </div>
                </aside>
            </section>
        @endif

        @if ($showProducts)
            {{-- TOP PRODUCTS --}}
            <section class="seller-report-print-section seller-report-products-section border bg-white">
                <div>
                    <h2>Top Selling Products</h2>
                    <p>Ranked using real quantities and line totals from delivered order snapshots.</p>
                </div>

                <div class="seller-report-product-grid">
                    @php
                        $maxProductSales = max(1, (float) collect($topProducts)->max('sales'));
                    @endphp

                    @forelse ($topProducts as $index => $product)
                        @php
                            $width = min(100, (((float) $product['sales'] / $maxProductSales) * 100));
                            $imagePath = $product['image_path'] ?? null;
                            $imageUrl = null;

                            if ($imagePath) {
                                $imageUrl = \Illuminate\Support\Str::startsWith(
                                    $imagePath,
                                    ['http://', 'https://', '/storage/', '/images/', '/uploads/']
                                )
                                    ? $imagePath
                                    : asset('storage/' . ltrim($imagePath, '/'));
                            }
                        @endphp

                        <article class="seller-report-product-card">
                            <div class="seller-report-product-media">
                                @if($imageUrl)
                                    <img
                                        src="{{ $imageUrl }}"
                                        alt="{{ $product['name'] }}"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                @else
                                    <div class="seller-report-product-placeholder" aria-label="No product image available">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <rect x="3" y="4" width="18" height="16" rx="3"></rect>
                                            <path d="m7 15 3-3 3 3 4-5 3 4"></path>
                                            <circle cx="9" cy="9" r="1.2" fill="currentColor" stroke="none"></circle>
                                        </svg>
                                    </div>
                                @endif

                                <span class="seller-report-product-rank">#{{ $index + 1 }}</span>
                            </div>

                            <div class="seller-report-product-body">
                                <div class="seller-report-product-topline">
                                    <span class="seller-report-product-sold">
                                        {{ number_format((int) $product['quantity']) }} sold
                                    </span>

                                    <span class="seller-report-product-share">
                                        {{ number_format($width, 0) }}% of top
                                    </span>
                                </div>

                                <p class="seller-report-product-name" title="{{ $product['name'] }}">
                                    {{ $product['name'] }}
                                </p>

                                <p class="seller-report-product-sales">
                                    ₱{{ number_format((float) $product['sales'], 2) }}
                                </p>

                                <div class="seller-report-product-progress" aria-hidden="true">
                                    <span style="width: {{ $width }}%"></span>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-[12px] border border-dashed border-[#dfe3e8] bg-white px-6 py-9 text-center">
                            <p class="text-[9px] font-medium text-[#667085]">No delivered product sales in this period.</p>
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

            this.initReportTypeDropdown(signal);

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

        initReportTypeDropdown(signal) {
            const root = document.querySelector('[data-report-type-dropdown]');
            if (!root) return;

            const nativeSelect = root.querySelector('[data-report-type-native]');
            const button = root.querySelector('[data-report-type-button]');
            const label = root.querySelector('[data-report-type-label]');
            const menu = root.querySelector('[data-report-type-menu]');
            const options = Array.from(root.querySelectorAll('[data-report-type-value]'));

            if (!nativeSelect || !button || !label || !menu || !options.length) return;

            const getLabel = (value) => {
                const option = Array.from(nativeSelect.options).find((item) => item.value === value);
                return option?.textContent?.trim() || 'All Reports';
            };

            const sync = (value) => {
                nativeSelect.value = value;
                label.textContent = getLabel(value);

                options.forEach((option) => {
                    const selected = (option.dataset.reportTypeValue || '') === value;
                    option.classList.toggle('is-selected', selected);
                    option.setAttribute('aria-selected', selected ? 'true' : 'false');
                });
            };

            const close = (restoreFocus = false) => {
                menu.hidden = true;
                button.setAttribute('aria-expanded', 'false');
                if (restoreFocus) button.focus();
            };

            const focusSelectedOrFirst = () => {
                const selected = options.find((option) => option.classList.contains('is-selected')) || options[0];
                selected?.focus();
            };

            const open = (focusOption = false) => {
                menu.hidden = false;
                button.setAttribute('aria-expanded', 'true');
                if (focusOption) requestAnimationFrame(focusSelectedOrFirst);
            };

            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                menu.hidden ? open(false) : close(false);
            }, { signal });

            button.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                    event.preventDefault();
                    open(true);
                }
            }, { signal });

            options.forEach((option, index) => {
                option.addEventListener('click', () => {
                    sync(option.dataset.reportTypeValue || 'all');
                    close(true);
                }, { signal });

                option.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        event.preventDefault();
                        close(true);
                        return;
                    }

                    if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') return;

                    event.preventDefault();
                    const direction = event.key === 'ArrowDown' ? 1 : -1;
                    const nextIndex = (index + direction + options.length) % options.length;
                    options[nextIndex]?.focus();
                }, { signal });
            });

            document.addEventListener('click', (event) => {
                if (!root.contains(event.target)) close(false);
            }, { signal });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !menu.hidden) {
                    close(true);
                }
            }, { signal });

            sync(nativeSelect.value || 'all');
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
