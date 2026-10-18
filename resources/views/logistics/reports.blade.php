
@extends('layouts.logistics')
@section('title', 'Logistics Reports | SARI')
@section('page-title', 'Generate Report')

@php
    $show = fn (string $sections): bool =>
        $reportType === 'all'
        || in_array($reportType, explode(' ', $sections), true);

    $isHubOperator = (bool) $isHubOperator;

    $trend = $deliveryTrend->values();
    $trendTotal = $trend->sum('count');
    $trendFees = $trend->sum('fees');
    $maxTrend = max(1, (int) $trend->max('count'));
    $pointCount = $trend->count();

    $points = $trend->map(function (
        $day,
        $index
    ) use ($maxTrend, $pointCount) {
        return [
            'x' => round(
                $pointCount > 1
                    ? 54 + $index * 690 / ($pointCount - 1)
                    : 399,
                2
            ),

            'y' => round(
                207 - ((int) $day['count'] / $maxTrend) * 155,
                2
            ),

            'count' => (int) $day['count'],
            'fees' => (float) $day['fees'],
            'label' => $day['short_date'],
        ];
    });

    $line = $points
        ->map(fn ($p) => $p['x'].','.$p['y'])
        ->implode(' ');

    $area = $points->isNotEmpty()
        ? 'M '.$points->first()['x'].' 207 L '
            .$points->map(
                fn ($p) => $p['x'].' '.$p['y']
            )->implode(' L ')
            .' L '.$points->last()['x'].' 207 Z'
        : '';

    $cards = [
        [
            'Completed Deliveries',
            $periodDelivered,
            'Completed within selected dates',
            'green',
            'number',
        ],
        [
            'Delivery Fees',
            '₱'.number_format($periodFees, 2),
            'Total fees on completed orders in range',
            'amber',
            'money',
        ],
        [
            'Average Delivery Fee',
            '₱'.number_format($stats['average_delivery_fee'], 2),
            'Per completed order in selected range',
            'purple',
            'average',
        ],
        [
            'Active Deliveries',
            $stats['active_deliveries'],
            'Current live workload, not historical',
            'blue',
            'active',
        ],
    ];

    $performance = [
        [
            'Waiting Pickup Assignment',
            $stats['waiting_assignment'],
            'origin',
        ],
        [
            'Origin Hub Processed',
            $stats['origin_processed'],
            'origin',
        ],
        [
            'Ready for Linehaul',
            $stats['ready_linehaul'],
            'origin',
        ],
        [
            'Destination Hub Ready',
            $stats['destination_ready'],
            'destination',
        ],
        [
            'Active Last Mile',
            $stats['last_mile_active'],
            'destination',
        ],
    ];

    $performanceMax = max(
        1,
        collect($performance)->max(
            fn ($row) => $row[1]
        )
    );

    /*
    |--------------------------------------------------------------------------
    | CSV data
    |--------------------------------------------------------------------------
    |
    | Only data already secured by the controller is exported.
    */

    $csvRows = [
        ['tag', 'section', 'metric', 'value', 'details'],

        [
            'all',
            'Report',
            'Coverage',
            $isHubOperator
                ? ($hubFacility?->name ?: 'Assigned Hub')
                : 'Entire Logistics network',
            '',
        ],

        ['all', 'Report', 'From', $from, ''],
        ['all', 'Report', 'To', $to, ''],

        [
            'deliveries,fees',
            'Selected Period',
            'Completed Deliveries',
            $periodDelivered,
            '',
        ],

        [
            'deliveries,fees',
            'Selected Period',
            'Delivery Fees',
            number_format($periodFees, 2, '.', ''),
            'PHP',
        ],

        [
            'deliveries,fees',
            'Selected Period',
            'Average Fee',
            number_format(
                $stats['average_delivery_fee'],
                2,
                '.',
                ''
            ),
            'PHP',
        ],
    ];

    foreach ($deliveryTrend as $day) {
        $csvRows[] = [
            'deliveries,fees',
            'Recent Trend',
            $day['short_date'],
            $day['count'],
            'Fees PHP '.number_format(
                $day['fees'],
                2,
                '.',
                ''
            ),
        ];
    }

    foreach ($pipeline as $stage) {
        $csvRows[] = [
            'pipeline',
            'Current Pipeline',
            $stage['label'],
            $stage['count'],
            $stage['description'],
        ];
    }

    foreach ($topLastMileRiders as $rider) {
        $csvRows[] = [
            'riders',
            'Last Mile Rider',
            trim(
                $rider->first_name.' '.$rider->last_name
            ),
            $rider->delivered_count,
            'Completed in date range',
        ];
    }

    foreach ($recentDelivered as $order) {
        $csvRows[] = [
            'deliveries,fees',
            'Recent Completed Order',
            $order->order_number,
            number_format(
                (float) $order->delivery_fee,
                2,
                '.',
                ''
            ),
            'PHP; '
                .$order->delivered_at
                    ?->timezone('Asia/Manila')
                    ->format('Y-m-d H:i'),
        ];
    }
@endphp

@push('styles')
<style>
#lgReports {
    --lr-amber: #d9950b;
    --lr-border: #e6e1d9;
    --lr-muted: #918578;
    --lr-ink: #332b23;

    color: var(--lr-ink);
    font-family: Poppins, ui-sans-serif, system-ui, sans-serif;
    font-size: 10px;

    max-width: 1620px;
    margin: 0 auto;
    padding-bottom: 35px;
}

#lgReports *,
#lgReports *:before,
#lgReports *:after {
    box-sizing: border-box;
}

#lgReports .lr-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;

    border-bottom: 1px solid var(--lr-border);
    padding: 5px 2px 17px;
    margin-bottom: 17px;
}

#lgReports .lr-eyebrow {
    color: #a57520;
    font-size: 9px;
    letter-spacing: .12em;
    font-weight: 750;
    text-transform: uppercase;
    margin: 0 0 5px;
}

#lgReports h1 {
    font-size: clamp(23px, 2.15vw, 30px);
    line-height: 1.25;
    letter-spacing: -.04em;
    font-weight: 750;
    margin: 0;
}

#lgReports h1 span {
    color: var(--lr-amber);
}

#lgReports .lr-subtitle {
    font-size: 10px;
    line-height: 1.7;
    color: var(--lr-muted);
    margin: 6px 0 0;
    max-width: 720px;
}

#lgReports .lr-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

#lgReports .lr-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 10px 14px;
    min-height: 39px;

    border: 1px solid #dededb;
    border-radius: 10px;
    background: #fff;

    font: 600 10px Poppins, system-ui, sans-serif;
    color: #685d51;
    cursor: pointer;
    text-decoration: none;
}

#lgReports .lr-btn:hover {
    border-color: #d4b77d;
    background: #fff9ed;
}

#lgReports .lr-primary {
    background: var(--lr-amber);
    border-color: var(--lr-amber);
    color: #fff;
}

#lgReports .lr-primary:hover {
    background: #bd7e07;
    border-color: #bd7e07;
    color: #fff;
}

#lgReports .lr-btn svg {
    width: 15px;
    height: 15px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
}

#lgReports .lr-panel,
#lgReports .lr-filter,
#lgReports .lr-kpi,
#lgReports .lr-strip {
    border: 1px solid var(--lr-border);
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 2px 8px rgba(37, 29, 20, .032);
    min-width: 0;
    transform: none !important;
}

/* Seller-style filter */

#lgReports .lr-filter {
    padding: 15px;
    margin-bottom: 18px;
}

#lgReports .lr-filter-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1.1fr 110px;
    gap: 9px;
    align-items: end;
}

#lgReports .lr-field {
    min-width: 0;
}

#lgReports .lr-field label {
    display: block;
    margin-bottom: 6px;
    color: #74685b;
    font-size: 9px;
    font-weight: 700;
}

#lgReports .lr-input {
    width: 100%;
    min-width: 0;
    height: 40px;
    padding: 0 11px;

    border: 1px solid #dfe0e4;
    border-radius: 10px;
    background: #fff;
    color: #494137;

    font: 550 10px Poppins, system-ui, sans-serif;
    outline: 0;
}

#lgReports .lr-input:focus {
    border-color: var(--lr-amber);
    box-shadow: 0 0 0 3px rgba(217, 149, 11, .09);
}

#lgReports .lr-quick {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
    align-items: center;
    margin-top: 11px;
}

#lgReports .lr-quick > span {
    color: #958a7f;
    font-size: 9px;
    margin-right: 5px;
}

#lgReports .lr-quick button {
    border: 1px solid #e1e2e5;
    background: #fff;
    border-radius: 999px;
    padding: 6px 11px;
    color: #665b4e;
    font: 550 9px Poppins, system-ui, sans-serif;
    cursor: pointer;
}

#lgReports .lr-quick button[aria-pressed=true],
#lgReports .lr-quick button:hover {
    border-color: #ebd6b3;
    background: #fff8eb;
    color: #a36b0e;
}

#lgReports .lr-error {
    color: #a64242;
    font-size: 10px;
    margin: 0 0 12px;
    padding: 10px 12px;
    border: 1px solid #efcdcd;
    border-radius: 9px;
    background: #fff4f4;
}

#lgReports .lr-coverage {
    display: flex;
    gap: 9px;
    flex-wrap: wrap;
    margin: -3px 0 15px;
    color: #876b43;
    font-size: 9px;
}

#lgReports .lr-coverage span {
    padding: 6px 10px;
    border-radius: 999px;
    background: #fff7e9;
    border: 1px solid #eedfc2;
}

/* KPI cards */

#lgReports .lr-kpis {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 13px;
    margin-bottom: 18px;
}

#lgReports .lr-kpi {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    min-height: 103px;
    padding: 14px;
}

#lgReports .lr-kpi-label {
    font-size: 10px;
    font-weight: 600;
    color: #665a4d;
}

#lgReports .lr-kpi-value {
    display: block;
    font-size: clamp(19px, 1.8vw, 25px);
    line-height: 1.2;
    font-weight: 750;
    margin-top: 8px;
    letter-spacing: -.035em;
    overflow-wrap: anywhere;
}

#lgReports .lr-kpi-note {
    font-size: 9px;
    color: var(--lr-muted);
    margin-top: 7px;
    line-height: 1.5;
}

#lgReports .lr-icon {
    display: grid;
    place-items: center;
    flex: 0 0 38px;
    width: 38px;
    height: 38px;
    border-radius: 11px;
    background: #fff5e5;
    color: #b77d15;
}

#lgReports .lr-icon svg {
    width: 18px;
    height: 18px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
}

#lgReports .lr-icon.green {
    background: #eff8f2;
    color: #4e8662;
}

#lgReports .lr-icon.blue {
    background: #eff5fc;
    color: #4f7fab;
}

#lgReports .lr-icon.purple {
    background: #f6f2fa;
    color: #816b99;
}

/* Main analysis */

#lgReports .lr-analysis {
    display: grid;
    grid-template-columns: minmax(0, 1.6fr) minmax(285px, .8fr);
    gap: 18px;
    margin-bottom: 18px;
    align-items: stretch;
}

#lgReports .lr-analysis.single {
    grid-template-columns: 1fr;
}

#lgReports .lr-panel {
    overflow: hidden;
    margin-bottom: 18px;
}

#lgReports .lr-analysis .lr-panel {
    margin-bottom: 0;
}

#lgReports .lr-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 9px;
    padding: 15px 16px;
    border-bottom: 1px solid #eee9e2;
}

#lgReports .lr-panel-head h2 {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #352d25;
}

#lgReports .lr-panel-head p {
    margin: 4px 0 0;
    font-size: 9px;
    color: var(--lr-muted);
    line-height: 1.5;
}

#lgReports .lr-chip {
    font-size: 9px;
    font-weight: 650;
    background: #fff8e9;
    color: #a16c11;
    border: 1px solid #eadcc2;
    border-radius: 999px;
    padding: 6px 9px;
}

#lgReports .lr-chart {
    padding: 16px;
    min-height: 260px;
}

#lgReports .lr-chart svg {
    display: block;
    width: 100%;
    height: auto;
}

#lgReports .lr-axis {
    fill: #928779;
    font-size: 10px;
}

#lgReports .lr-gridline {
    stroke: #eee8df;
    stroke-dasharray: 4 5;
}

#lgReports .lr-line {
    fill: none;
    stroke: var(--lr-amber);
    stroke-width: 3;
    stroke-linejoin: round;
    stroke-linecap: round;
}

#lgReports .lr-dot {
    fill: #fff;
    stroke: var(--lr-amber);
    stroke-width: 2.5;
}

#lgReports .lr-chart-foot {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 7px;
    padding: 11px 16px;
    border-top: 1px solid #eee9e2;
    color: var(--lr-muted);
    font-size: 9px;
}

#lgReports .lr-chart-foot strong {
    color: #504438;
}

#lgReports .lr-current {
    margin: 0 15px 12px;
    padding: 9px 11px;
    border-radius: 9px;
    border: 1px solid #e7e9ef;
    background: #fafbfc;
    color: #84796d;
    font-size: 9px;
    line-height: 1.6;
}

#lgReports .lr-perf {
    padding: 5px 15px;
}

#lgReports .lr-perf-item {
    padding: 11px 0;
    border-bottom: 1px solid #f0ebe5;
}

#lgReports .lr-perf-item:last-child {
    border: 0;
}

#lgReports .lr-perf-top {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    font-size: 9px;
    font-weight: 650;
}

#lgReports .lr-perf-top strong {
    font-size: 11px;
    color: #aa741a;
}

#lgReports .lr-progress {
    height: 6px;
    background: #eff0f2;
    border-radius: 99px;
    overflow: hidden;
    margin-top: 9px;
}

#lgReports .lr-progress span {
    display: block;
    height: 100%;
    border-radius: 99px;
    background: #d3a348;
}

/* Pipeline and Rider sections */

#lgReports .lr-stage-grid,
#lgReports .lr-rider-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 11px;
    padding: 15px;
}

#lgReports .lr-stage,
#lgReports .lr-rider {
    min-width: 0;
    border: 1px solid #ebe6de;
    border-radius: 11px;
    background: #fbfbfc;
    padding: 13px;
}

#lgReports .lr-stage small,
#lgReports .lr-rider small {
    font-size: 9px;
    color: var(--lr-muted);
}

#lgReports .lr-stage strong {
    display: block;
    font-size: 20px;
    font-weight: 750;
    margin-top: 7px;
}

#lgReports .lr-stage p {
    font-size: 9px;
    color: var(--lr-muted);
    line-height: 1.5;
    margin: 5px 0 0;
}

#lgReports .lr-rider {
    display: flex;
    align-items: center;
    gap: 9px;
}

#lgReports .lr-rank {
    color: #a78144;
    font-size: 10px;
    font-weight: 700;
    flex-shrink: 0;
}

#lgReports .lr-avatar {
    display: grid;
    place-items: center;
    width: 34px;
    height: 34px;
    flex: 0 0 34px;
    border-radius: 9px;
    background: #fff3df;
    color: #aa7314;
    font-size: 11px;
    font-weight: 700;
}

#lgReports .lr-rider-name {
    min-width: 0;
    flex: 1;
}

#lgReports .lr-rider-name strong {
    display: block;
    font-size: 10px;
    overflow-wrap: anywhere;
}

#lgReports .lr-rider-name small {
    display: block;
    margin-top: 4px;
    line-height: 1.4;
    overflow-wrap: anywhere;
}

#lgReports .lr-rider-count {
    text-align: right;
    font-size: 18px;
    font-weight: 750;
    color: #4a8260;
}

#lgReports .lr-rider-count small {
    display: block;
    font-size: 8px;
    font-weight: 500;
}

/* Operational strip */

#lgReports .lr-strip {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    overflow: hidden;
    margin-bottom: 18px;
}

#lgReports .lr-strip > div {
    padding: 15px;
    border-right: 1px solid #eee9e3;
}

#lgReports .lr-strip > div:last-child {
    border: 0;
}

#lgReports .lr-strip small {
    font-size: 9px;
    color: var(--lr-muted);
}

#lgReports .lr-strip strong {
    display: block;
    margin-top: 6px;
    font-size: 20px;
    font-weight: 750;
}

/* Transaction table */

#lgReports .lr-table-wrap {
    overflow-x: auto;
}

#lgReports table {
    border-collapse: collapse;
    min-width: 800px;
    width: 100%;
    text-align: left;
}

#lgReports thead {
    background: #f8f8fa;
}

#lgReports th {
    font-size: 8.5px;
    color: #80756a;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 12px 13px;
    text-align: left;
}

#lgReports td {
    font-size: 9px;
    color: #6a6054;
    padding: 12px 13px;
    border-top: 1px solid #f0ebe4;
    vertical-align: middle;
}

#lgReports td strong {
    font-size: 10px;
    color: #46392d;
}

#lgReports td small {
    display: block;
    font-size: 8.5px;
    color: var(--lr-muted);
    margin-top: 4px;
}

#lgReports .lr-empty {
    padding: 28px 15px;
    text-align: center;
    color: var(--lr-muted);
    font-size: 10px;
    line-height: 1.7;
}

#lgReports .lr-badge {
    display: inline-block;
    font-size: 8.5px;
    font-weight: 700;
    color: #4b805c;
    background: #eef8f0;
    border: 1px solid #d3e5d8;
    padding: 5px 8px;
    border-radius: 99px;
}

#lgReports [hidden] {
    display: none !important;
}

/* Responsive */

@media (max-width: 1100px) {
    #lgReports .lr-kpis {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    #lgReports .lr-analysis {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 760px) {
    #lgReports .lr-header {
        align-items: stretch;
        flex-direction: column;
    }

    #lgReports .lr-filter-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    #lgReports .lr-stage-grid,
    #lgReports .lr-rider-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 500px) {
    #lgReports .lr-filter-grid {
        grid-template-columns: 1fr;
    }

    #lgReports .lr-stage-grid,
    #lgReports .lr-rider-grid {
        grid-template-columns: 1fr;
    }

    #lgReports .lr-strip {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    #lgReports .lr-strip > div:nth-child(2) {
        border-right: 0;
    }

    #lgReports .lr-strip > div:nth-child(-n+2) {
        border-bottom: 1px solid #eee9e3;
    }
}

/* Print */

@media print {
    @page {
        size: A4;
        margin: 13mm;
    }

    body * {
        visibility: hidden !important;
    }

    #lgReports,
    #lgReports * {
        visibility: visible !important;
    }

    #lgReports {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        max-width: none;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    #lgReports .lr-no-print {
        display: none !important;
    }

    #lgReports .lr-analysis {
        grid-template-columns: 1.5fr 1fr;
    }

    #lgReports .lr-panel,
    #lgReports .lr-kpi,
    #lgReports .lr-strip {
        box-shadow: none !important;
        break-inside: avoid;
    }

    #lgReports tr {
        break-inside: avoid;
    }
}
</style>
@endpush

@section('content')
<div id="lgReports" data-reports>

    {{-- Page heading --}}

    <header class="lr-header">
        <div>
            <p class="lr-eyebrow">
                {{ $isHubOperator
                    ? 'Assigned Hub · Reporting'
                    : 'Network · Reporting & Analytics' }}
            </p>

            <h1>
                Logistics <span>Reports</span>
            </h1>

            <p class="lr-subtitle">
                @if($isHubOperator)
                    Review deliveries involving your assigned hub
                    and your own hub operations.
                @else
                    Review completed deliveries, delivery fees
                    and operating performance across your
                    Logistics network.
                @endif
            </p>
        </div>

        <div class="lr-actions lr-no-print">
            <button
                class="lr-btn"
                id="lrDownload"
                type="button"
            >
                <svg viewBox="0 0 24 24">
                    <path d="M12 3v12m-5-5 5 5 5-5M5 21h14"/>
                </svg>
                Download CSV
            </button>

            <button
                class="lr-btn lr-primary"
                id="lrPrint"
                type="button"
            >
                <svg viewBox="0 0 24 24">
                    <path d="M6 9V3h12v6M6 18H4V9h16v9h-2M7 14h10v7H7z"/>
                </svg>
                Print Report
            </button>
        </div>
    </header>

    {{-- Assigned hub context --}}

    @if($isHubOperator)
        <div class="lr-coverage">
            <span>
                Assigned Hub:
                <strong>
                    {{ $hubFacility?->name ?: 'Assigned facility' }}
                </strong>
            </span>

            <span>
                {{ $hubFacility?->municipality_name }},
                {{ $hubFacility?->province_name }}
            </span>

            <span>
                Scoped access · Read-only reports
            </span>
        </div>
    @endif

    {{-- Server validation errors --}}

    @if($errors->any())
        <div class="lr-error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Seller-style Report Filter --}}

    <section
        class="lr-filter lr-no-print"
        aria-label="Generate report filters"
    >
        <form
            id="lrReportForm"
            method="GET"
            action="{{ route('logistics.reports') }}"
        >
            <div class="lr-filter-grid">

                <div class="lr-field">
                    <label for="lrFrom">
                        From Date
                    </label>

                    <input
                        required
                        class="lr-input"
                        type="date"
                        id="lrFrom"
                        name="from"
                        value="{{ $from }}"
                    >
                </div>

                <div class="lr-field">
                    <label for="lrTo">
                        To Date
                    </label>

                    <input
                        required
                        class="lr-input"
                        type="date"
                        id="lrTo"
                        name="to"
                        value="{{ $to }}"
                    >
                </div>

                <div class="lr-field">
                    <label for="lrType">
                        Report Type
                    </label>

                    <select
                        class="lr-input"
                        id="lrType"
                        name="type"
                    >
                        @foreach([
                            'all' => 'All Reports',
                            'deliveries' => 'Delivery Report',
                            'pipeline' => 'Parcel Pipeline',
                            'riders' => 'Rider Performance',
                            'fees' => 'Delivery Fee Report',
                        ] as $key => $name)
                            <option
                                value="{{ $key }}"
                                @selected($reportType === $key)
                            >
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lr-field">
                    <label for="lrGenerate">
                        &nbsp;
                    </label>

                    <button
                        class="lr-btn lr-primary"
                        id="lrGenerate"
                        type="submit"
                        style="width:100%;height:40px"
                    >
                        Generate
                    </button>
                </div>
            </div>

            <div class="lr-quick">
                <span>Quick range</span>

                @foreach([
                    'today' => 'Today',
                    'last7' => 'Last 7 Days',
                    'last30' => 'Last 30 Days',
                    'month' => 'This Month',
                    'year' => 'This Year',
                ] as $key => $label)
                    <button
                        type="button"
                        data-range="{{ $key }}"
                        aria-pressed="false"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </form>
    </section>

    {{-- Report period description --}}

    <div class="lr-coverage">
        <span>
            Selected period:
            <strong>
                {{ \Carbon\Carbon::parse($from)->format('M d, Y') }}
                –
                {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}
            </strong>
        </span>

        <span>
            Live pipeline values are a current snapshot;
            date filters apply to delivered orders, fees
            and Rider rankings.
        </span>
    </div>

    {{-- KPI cards --}}

    <section
        class="lr-kpis"
        aria-label="Report overview"
    >
        @foreach($cards as [$label, $value, $note, $tone, $icon])
            <article class="lr-kpi">
                <div>
                    <div class="lr-kpi-label">
                        {{ $label }}
                    </div>

                    <strong class="lr-kpi-value">
                        {{ is_numeric($value)
                            ? number_format($value)
                            : $value }}
                    </strong>

                    <p class="lr-kpi-note">
                        {{ $note }}
                    </p>
                </div>

                <span
                    class="lr-icon {{ $tone }}"
                    aria-hidden="true"
                >
                    @if($icon === 'money')
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M15 8h-5a2 2 0 0 0 0 4h4a2 2 0 0 1 0 4H9m3-10v12"/>
                        </svg>
                    @elseif($icon === 'average')
                        <svg viewBox="0 0 24 24">
                            <path d="M4 20h16M7 16v-4m5 4V8m5 8V5"/>
                        </svg>
                    @elseif($icon === 'active')
                        <svg viewBox="0 0 24 24">
                            <path d="M4 17 9 12l3 3 7-8m-4 0h4v4"/>
                        </svg>
                    @else
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="m8 12 2.5 2.5L16 9"/>
                        </svg>
                    @endif
                </span>
            </article>
        @endforeach
    </section>

    {{-- Delivery trend and performance summary --}}

    @if($show('deliveries fees') || $show('pipeline'))
        <div class="lr-analysis {{ !$show('deliveries fees') || !$show('pipeline') ? 'single' : '' }}">

            @if($show('deliveries fees'))
                <section class="lr-panel">
                    <header class="lr-panel-head">
                        <div>
                            <h2>
                                Delivery &amp; Fee Trend
                            </h2>

                            <p>
                                Last {{ $pointCount }}
                                calendar
                                {{ $pointCount === 1 ? 'day' : 'days' }}
                                within selected date range
                            </p>
                        </div>

                        <span class="lr-chip">
                            {{ number_format($trendTotal) }} delivered
                        </span>
                    </header>

                    <div class="lr-chart">
                        <svg
                            viewBox="0 0 800 255"
                            role="img"
                            aria-label="Completed delivery trend"
                        >
                            <defs>
                                <linearGradient
                                    id="lrTrendFill"
                                    x1="0"
                                    y1="0"
                                    x2="0"
                                    y2="1"
                                >
                                    <stop
                                        offset="0%"
                                        stop-color="#d9950b"
                                        stop-opacity=".14"
                                    />

                                    <stop
                                        offset="100%"
                                        stop-color="#d9950b"
                                        stop-opacity=".01"
                                    />
                                </linearGradient>
                            </defs>

                            @for($i = 0; $i < 5; $i++)
                                <line
                                    class="lr-gridline"
                                    x1="54"
                                    x2="744"
                                    y1="{{ 52 + $i * 38.75 }}"
                                    y2="{{ 52 + $i * 38.75 }}"
                                />

                                <text
                                    class="lr-axis"
                                    x="43"
                                    y="{{ 56 + $i * 38.75 }}"
                                    text-anchor="end"
                                >
                                    {{ round($maxTrend * (4 - $i) / 4, 1) }}
                                </text>
                            @endfor

                            @if($points->isNotEmpty())
                                <path
                                    d="{{ $area }}"
                                    fill="url(#lrTrendFill)"
                                />

                                <polyline
                                    class="lr-line"
                                    points="{{ $line }}"
                                />

                                @foreach($points as $point)
                                    <circle
                                        class="lr-dot"
                                        cx="{{ $point['x'] }}"
                                        cy="{{ $point['y'] }}"
                                        r="4"
                                    >
                                        <title>
                                            {{ $point['label'] }}:
                                            {{ $point['count'] }} delivered;
                                            ₱{{ number_format($point['fees'], 2) }} fees
                                        </title>
                                    </circle>

                                    <text
                                        class="lr-axis"
                                        x="{{ $point['x'] }}"
                                        y="235"
                                        text-anchor="middle"
                                    >
                                        {{ $point['label'] }}
                                    </text>
                                @endforeach
                            @endif
                        </svg>

                        @if(!$trendTotal)
                            <p class="lr-empty">
                                No completed deliveries in these
                                recent trend dates.
                            </p>
                        @endif
                    </div>

                    <div class="lr-chart-foot">
                        <span>
                            Recent trend:
                            <strong>
                                {{ number_format($trendTotal) }}
                                delivered
                            </strong>
                        </span>

                        <span>
                            Fees:
                            <strong>
                                ₱{{ number_format($trendFees, 2) }}
                            </strong>
                        </span>
                    </div>
                </section>
            @endif

            @if($show('pipeline'))
                <section class="lr-panel">
                    <header class="lr-panel-head">
                        <div>
                            <h2>
                                Performance Summary
                            </h2>

                            <p>
                                Current hub or network operations
                            </p>
                        </div>

                        <span class="lr-chip">
                            Live snapshot
                        </span>
                    </header>

                    <div class="lr-perf">
                        @foreach($performance as [$label, $value, $scope])
                            <div class="lr-perf-item">
                                <div class="lr-perf-top">
                                    <span>
                                        {{ $label }}
                                    </span>

                                    <strong>
                                        {{ number_format($value) }}
                                    </strong>
                                </div>

                                <div class="lr-progress">
                                    <span
                                        style="width:{{ $value / $performanceMax * 100 }}%"
                                    ></span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <p class="lr-current">
                        These figures describe current workload,
                        not the historical selected period.
                        Origin work is scoped to the origin hub;
                        destination work to the destination hub.
                    </p>
                </section>
            @endif
        </div>
    @endif

    {{-- Parcel Pipeline --}}

    @if($show('pipeline'))
        <section class="lr-panel">
            <header class="lr-panel-head">
                <div>
                    <h2>
                        Current Parcel Pipeline
                    </h2>

                    <p>
                        Live operational snapshot of
                        {{ $isHubOperator ? 'your hub' : 'the network' }}
                    </p>
                </div>

                <span class="lr-chip">
                    Current
                </span>
            </header>

            <div class="lr-stage-grid">
                @foreach($pipeline as $stage)
                    <article class="lr-stage">
                        <small>
                            {{ $stage['label'] }}
                        </small>

                        <strong>
                            {{ number_format($stage['count']) }}
                        </strong>

                        <p>
                            {{ $stage['description'] }}
                        </p>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Rider Rankings --}}

    @if($show('riders'))
        <section class="lr-panel">
            <header class="lr-panel-head">
                <div>
                    <h2>
                        Top Last-Mile Riders
                    </h2>

                    <p>
                        Completed deliveries within selected dates ·
                        {{ $isHubOperator
                            ? 'assigned destination hub only'
                            : 'Logistics network' }}
                    </p>
                </div>

                <span class="lr-chip">
                    Top 5
                </span>
            </header>

            @if($topLastMileRiders->isEmpty())
                <p class="lr-empty">
                    No completed Last-Mile deliveries for this period.
                </p>
            @else
                <div class="lr-rider-grid">
                    @foreach($topLastMileRiders as $i => $rider)
                        @php
                            $name = trim(
                                $rider->first_name.' '.$rider->last_name
                            );
                        @endphp

                        <article class="lr-rider">
                            <span class="lr-rank">
                                #{{ $i + 1 }}
                            </span>

                            <span class="lr-avatar">
                                {{ mb_strtoupper(
                                    mb_substr($name ?: 'R', 0, 1)
                                ) }}
                            </span>

                            <div class="lr-rider-name">
                                <strong>
                                    {{ $name ?: 'Rider' }}
                                </strong>

                                <small>
                                    {{ collect([
                                        $rider->barangay_name,
                                        $rider->municipality_name,
                                        $rider->vehicle_type,
                                    ])->filter()->implode(' · ') }}
                                </small>
                            </div>

                            <div class="lr-rider-count">
                                {{ number_format($rider->delivered_count) }}
                                <small>
                                    delivered
                                </small>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    {{-- Operational count strip --}}

    @if($show('deliveries pipeline'))
        <section
            class="lr-strip"
            aria-label="Operational counts"
        >
            @foreach([
                [
                    'Waiting Assignment',
                    $stats['waiting_assignment'],
                    'Current',
                ],
                [
                    'Active Deliveries',
                    $stats['active_deliveries'],
                    'Current',
                ],
                [
                    'Delivered Today',
                    $stats['delivered_today'],
                    'Today',
                ],
                [
                    'Delivered in Range',
                    $periodDelivered,
                    'Selected dates',
                ],
            ] as [$label, $value, $note])
                <div>
                    <small>
                        {{ $label }}
                    </small>

                    <strong>
                        {{ number_format($value) }}
                    </strong>

                    <small>
                        {{ $note }}
                    </small>
                </div>
            @endforeach
        </section>
    @endif

    {{-- Filtered completed orders --}}

    @if($show('deliveries fees'))
        <section class="lr-panel">
            <header class="lr-panel-head">
                <div>
                    <h2>
                        Financial Breakdown
                    </h2>

                    <p>
                        Latest 15 completed orders from the
                        selected date range
                    </p>
                </div>

                <span class="lr-chip">
                    {{ number_format($recentDelivered->count()) }} shown
                </span>
            </header>

            <div class="lr-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Seller / Buyer</th>
                            <th>Pickup Rider</th>
                            <th>Hub Route</th>
                            <th>Delivery Fee</th>
                            <th>Delivered</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($recentDelivered as $order)
                            @php
                                $origin = $order
                                    ->deliveryRouteStops
                                    ->firstWhere(
                                        'stop_key',
                                        'origin_facility'
                                    );

                                $destination = $order
                                    ->deliveryRouteStops
                                    ->firstWhere(
                                        'stop_key',
                                        'destination_facility'
                                    );
                            @endphp

                            <tr>
                                <td>
                                    <strong>
                                        {{ $order->order_number }}
                                    </strong>
                                </td>

                                <td>
                                    <strong>
                                        {{ $order->seller?->store_name ?: 'SARI Seller' }}
                                    </strong>

                                    <small>
                                        Buyer: {{ $order->buyer_name }}
                                    </small>
                                </td>

                                <td>
                                    {{ $order->courier_name ?: 'Not recorded' }}
                                </td>

                                <td>
                                    {{ $origin?->facility?->name ?: $origin?->name ?: 'Origin' }}
                                    →
                                    {{ $destination?->facility?->name ?: $destination?->name ?: 'Destination' }}
                                </td>

                                <td>
                                    <strong>
                                        ₱{{ number_format(
                                            (float) $order->delivery_fee,
                                            2
                                        ) }}
                                    </strong>
                                </td>

                                <td>
                                    <span class="lr-badge">
                                        Delivered
                                    </span>

                                    <small>
                                        {{ $order->delivered_at
                                            ?->timezone('Asia/Manila')
                                            ->format('M d, Y · g:i A')
                                            ?: 'Not recorded' }}
                                    </small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="6"
                                    class="lr-empty"
                                >
                                    No completed orders for the
                                    selected date range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const root = document.querySelector('[data-reports]');

    if (!root || root.dataset.initialized === '1') {
        return;
    }

    root.dataset.initialized = '1';

    const form = root.querySelector('#lrReportForm');
    const fromInput = root.querySelector('#lrFrom');
    const toInput = root.querySelector('#lrTo');

    const buttons = [
        ...root.querySelectorAll('[data-range]')
    ];

    const serverToday = @json(now('Asia/Manila')->format('Y-m-d'));

    const csvRows = {{ \Illuminate\Support\Js::from($csvRows) }};

    const reportType = @json($reportType);
    const reportFrom = @json($from);
    const reportTo = @json($to);

    function asDate(text) {
        const [y, m, d] = text.split('-').map(Number);
        return new Date(y, m - 1, d);
    }

    function asText(date) {
        return [
            date.getFullYear(),
            String(date.getMonth() + 1).padStart(2, '0'),
            String(date.getDate()).padStart(2, '0')
        ].join('-');
    }

    /*
    |--------------------------------------------------------------------------
    | Quick date ranges
    |--------------------------------------------------------------------------
    */

    function startOfRange(key) {
        const end = asDate(serverToday);
        const start = new Date(end);

        if (key === 'last7') {
            start.setDate(start.getDate() - 6);
        } else if (key === 'last30') {
            start.setDate(start.getDate() - 29);
        } else if (key === 'month') {
            start.setDate(1);
        } else if (key === 'year') {
            start.setMonth(0);
            start.setDate(1);
        } else if (key !== 'today') {
            return;
        }

        fromInput.value = asText(start);
        toInput.value = asText(end);

        buttons.forEach(button => {
            button.setAttribute(
                'aria-pressed',
                String(button.dataset.range === key)
            );
        });
    }

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            startOfRange(button.dataset.range);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Client-side validation before real GET submission
    |--------------------------------------------------------------------------
    */

    function checkDates(event) {
        if (
            !fromInput.value
            || !toInput.value
            || fromInput.value > toInput.value
        ) {
            event.preventDefault();

            toInput.setCustomValidity(
                'To Date must be on or after From Date.'
            );

            toInput.reportValidity();
        } else {
            toInput.setCustomValidity('');
        }
    }

    form?.addEventListener('submit', checkDates);

    [fromInput, toInput].forEach(input => {
        input?.addEventListener('input', () => {
            toInput.setCustomValidity('');

            buttons.forEach(button => {
                button.setAttribute(
                    'aria-pressed',
                    'false'
                );
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Restore correct quick-range selected state
    |--------------------------------------------------------------------------
    */

    const selectedButton = buttons.find(button => {
        const savedFrom = fromInput.value;
        const savedTo = toInput.value;

        startOfRange(button.dataset.range);

        const matched =
            fromInput.value === savedFrom
            && toInput.value === savedTo;

        fromInput.value = savedFrom;
        toInput.value = savedTo;

        return matched;
    });

    buttons.forEach(button => {
        button.setAttribute(
            'aria-pressed',
            String(button === selectedButton)
        );
    });

    /*
    |--------------------------------------------------------------------------
    | CSV export of server-filtered data
    |--------------------------------------------------------------------------
    */

    root.querySelector('#lrDownload')?.addEventListener(
        'click',
        () => {
            const escapeCsv = value => {
                let s = String(value ?? '');

                if (/^[=+\-@\t\r]/.test(s)) {
                    s = "'" + s;
                }

                return '"' + s.replace(/"/g, '""') + '"';
            };

            const rows = csvRows.filter((row, i) => {
                return i === 0
                    || reportType === 'all'
                    || row[0] === 'all'
                    || String(row[0])
                        .split(',')
                        .includes(reportType);
            });

            const csv = rows
                .map(
                    row => row
                        .slice(1)
                        .map(escapeCsv)
                        .join(',')
                )
                .join('\r\n');

            const url = URL.createObjectURL(
                new Blob(
                    ['\uFEFF', csv],
                    {
                        type: 'text/csv;charset=utf-8;'
                    }
                )
            );

            const link = document.createElement('a');

            link.href = url;

            link.download =
                `sari-logistics-${reportType}-${reportFrom}-to-${reportTo}.csv`;

            document.body.appendChild(link);
            link.click();
            link.remove();

            setTimeout(() => {
                URL.revokeObjectURL(url);
            }, 1000);
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Print
    |--------------------------------------------------------------------------
    */

    root.querySelector('#lrPrint')?.addEventListener(
        'click',
        () => window.print()
    );
});
</script>
@endpush
