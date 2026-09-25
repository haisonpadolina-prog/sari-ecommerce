<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SARI Platform Management Report</title>

    <style>
        /* ============================================================
           SARI PLATFORM MANAGEMENT REPORT — ENTERPRISE V2
           A4 portrait • formal management-report hierarchy
           Poppins-first • print-safe • low visual overhead
           ============================================================ */

        @page {
            size: A4 portrait;
            margin: 13mm 11mm 15mm 11mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        body {
            color: #2b2722;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
            font-size: 8.35px;
            line-height: 1.42;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            text-rendering: optimizeLegibility;
        }

        /* ---------------- SCREEN TOOLBAR ---------------- */
        .print-toolbar {
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: 0 0 14px;
            border: 1px solid #e4ddd3;
            border-radius: 12px;
            background: rgba(255,255,255,.98);
            padding: 10px 12px;
            box-shadow: 0 9px 24px rgba(61,43,22,.07);
        }

        .print-toolbar strong {
            display: block;
            color: #28231e;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.3;
        }

        .print-toolbar span {
            display: block;
            margin-top: 2px;
            color: #8b8278;
            font-size: 7px;
            line-height: 1.4;
        }

        .toolbar-actions {
            display: flex;
            gap: 7px;
        }

        .toolbar-btn {
            display: inline-flex;
            height: 34px;
            align-items: center;
            justify-content: center;
            border: 1px solid #dfd7cd;
            border-radius: 8px;
            background: #fff;
            padding: 0 11px;
            color: #665e55;
            font: inherit;
            font-size: 7.5px;
            font-weight: 650;
            cursor: pointer;
        }

        .toolbar-btn:hover {
            border-color: #d1c2ae;
            background: #faf8f4;
        }

        .toolbar-btn.primary {
            border-color: #c58d20;
            background: #c58d20;
            color: #fff;
        }

        .toolbar-btn.primary:hover {
            border-color: #ad7615;
            background: #ad7615;
        }

        @media screen {
            body {
                max-width: 210mm;
                margin: 0 auto;
                padding: 9mm 10mm;
                background: #f7f5f0;
                box-shadow: 0 0 30px rgba(40,32,23,.075);
            }
        }

        @media print {
            .print-toolbar {
                display: none !important;
            }

            html,
            body {
                background: #fff !important;
            }
        }

        /* ---------------- DOCUMENT HEADER ---------------- */
        .document-header {
            width: 100%;
            margin-bottom: 7px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .document-header td {
            vertical-align: top;
        }

        .company-name {
            color: #17130e;
            font-size: 18px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: 1.15px;
        }

        .company-name .gold {
            color: #b77b16;
        }

        .document-type {
            margin-top: 5px;
            color: #8b7449;
            font-size: 6.5px;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: 1.05px;
            text-transform: uppercase;
        }

        .document-title {
            margin-top: 9px;
            color: #28231e;
            font-size: 15.5px;
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -.18px;
        }

        .document-subtitle {
            max-width: 500px;
            margin-top: 4px;
            color: #756e66;
            font-size: 7.25px;
            line-height: 1.5;
        }

        .header-meta {
            width: 205px;
            padding-top: 1px;
            text-align: right;
        }

        .classification {
            display: inline-block;
            border: 1px solid #dcccae;
            border-radius: 4px;
            background: #fffbf3;
            padding: 4px 7px;
            color: #805f25;
            font-size: 6.25px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: .58px;
            text-transform: uppercase;
        }

        .generated-at {
            margin-top: 8px;
            color: #8b8278;
            font-size: 6.55px;
            line-height: 1.4;
        }

        .gold-rule {
            height: 2px;
            margin: 8px 0 9px;
            background: linear-gradient(90deg,#b77b16 0%,#d5b46e 42%,#eee7da 100%);
        }

        /* ---------------- REPORT META ---------------- */
        .report-meta {
            width: 100%;
            margin-bottom: 10px;
            border: 1px solid #ded8cf;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 6px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .report-meta th,
        .report-meta td {
            border: 0;
            border-right: 1px solid #e5dfd7;
            padding: 5px 7px;
            text-align: left;
            vertical-align: middle;
        }

        .report-meta th:last-child,
        .report-meta td:last-child {
            border-right: 0;
        }

        .report-meta th {
            width: 12%;
            background: #f7f5f1;
            color: #81776b;
            font-size: 6.05px;
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: .42px;
            text-transform: uppercase;
        }

        .report-meta td {
            width: 21%;
            background: #fff;
            color: #3d3832;
            font-size: 7.15px;
            font-weight: 600;
        }

        /* ---------------- SECTIONS ---------------- */
        .section {
            margin-top: 11px;
        }

        .section-heading {
            margin: 0 0 5px;
            padding-bottom: 4px;
            border-bottom: 1px solid #cbc3b9;
            color: #2f2a25;
            font-size: 8.8px;
            font-weight: 700;
            line-height: 1.3;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .section-note {
            margin: -1px 0 5px;
            color: #8c8378;
            font-size: 6.35px;
            line-height: 1.45;
        }

        /* ---------------- MANAGEMENT SUMMARY ---------------- */
        .management-summary {
            width: 100%;
            margin-bottom: 2px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .management-summary th,
        .management-summary td {
            border: 1px solid #ddd7cf;
            padding: 5px 7px;
        }

        .management-summary th {
            background: #f5f2ed;
            color: #6f665b;
            font-size: 6.15px;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: .34px;
        }

        .management-summary td {
            color: #403a34;
            font-size: 7.05px;
        }

        .management-summary tbody tr:nth-child(even) td {
            background: #fcfbf9;
        }

        .management-summary td.value {
            color: #28231e;
            font-size: 8.75px;
            font-weight: 700;
            text-align: right;
            white-space: nowrap;
        }

        .management-summary td.change {
            width: 15%;
            text-align: right;
            font-size: 6.75px;
            font-weight: 600;
            white-space: nowrap;
        }

        .change-positive { color: #4f6e52; }
        .change-negative { color: #9d5147; }
        .change-neutral { color: #888077; }

        /* ---------------- TABLES ---------------- */
        .plain-table,
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .plain-table {
            page-break-inside: avoid;
        }

        .plain-table th,
        .plain-table td {
            border: 1px solid #ddd8d1;
            padding: 4px 6px;
            text-align: left;
        }

        .plain-table th {
            background: #f5f2ed;
            color: #6f665b;
            font-size: 6.05px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .plain-table td {
            color: #403a34;
            font-size: 6.95px;
        }

        .plain-table td.right,
        .plain-table th.right {
            text-align: right;
        }

        .data-table {
            table-layout: fixed;
            page-break-inside: auto;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .data-table th {
            border: 1px solid #d4cec6;
            background: #f3f0eb;
            padding: 4px 5px;
            color: #635c54;
            font-size: 5.85px;
            font-weight: 700;
            text-align: left;
            line-height: 1.3;
            text-transform: uppercase;
            letter-spacing: .2px;
        }

        .data-table td {
            border: 1px solid #e0dbd4;
            padding: 4px 5px;
            color: #403a34;
            font-size: 6.15px;
            line-height: 1.4;
            vertical-align: top;
            word-wrap: break-word;
        }

        .data-table tbody tr:nth-child(even) td {
            background: #fbfaf8;
        }

        .data-table .right { text-align: right; }
        .data-table .center { text-align: center; }

        .status-text {
            font-weight: 700;
        }

        .status-delivered { color: #4f6e52; }
        .status-cancelled { color: #9d5147; }
        .status-active { color: #536d69; }

        .page-break {
            page-break-before: always;
        }

        .empty {
            border: 1px solid #ddd7cf;
            border-radius: 4px;
            background: #fbfaf8;
            padding: 12px;
            color: #8c8378;
            font-size: 7px;
            text-align: center;
        }

        /* ---------------- SIGN-OFF ---------------- */
        .signoff {
            width: 100%;
            margin-top: 17px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .signoff td {
            width: 33.333%;
            padding-right: 18px;
            vertical-align: bottom;
        }

        .signoff td:last-child {
            padding-right: 0;
        }

        .sign-line {
            margin-top: 22px;
            border-top: 1px solid #756d63;
            padding-top: 4px;
            color: #6f665b;
            font-size: 6.3px;
            font-weight: 600;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        /* ---------------- FOOTER ---------------- */
        .footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -9mm;
            border-top: 1px solid #d5cec5;
            padding-top: 4px;
            color: #8c8378;
            font-size: 5.85px;
            line-height: 1.2;
        }

        .footer-left { float: left; }
        .footer-right { float: right; }

        .page-number:after {
            content: counter(page);
        }

        @media print {
            .section,
            .management-summary,
            .plain-table,
            .signoff {
                orphans: 3;
                widows: 3;
            }

            .data-table {
                break-inside: auto;
            }

            .data-table tr {
                break-inside: avoid;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .toolbar-btn {
                transition: none !important;
            }
        }
    </style>
</head>

<body>
<div class="print-toolbar">
    <div>
        <strong>SARI Platform Management Report</strong>
        <span>A4 corporate print layout. Choose “Save as PDF” in the print destination.</span>
    </div>
    <div class="toolbar-actions">
        <button type="button" class="toolbar-btn" data-report-close>Close</button>
        <button type="button" class="toolbar-btn primary" data-report-print>Save / Print PDF</button>
    </div>
</div>

@php
    $money = fn ($value) => 'PHP '.number_format((float) $value, 2);
    $statusTotal = max(1, (int) collect($statusBreakdown)->sum());

    $summaryRows = [
        ['key'=>'orders','label'=>'Total Orders','value'=>number_format((int) $stats['orders'])],
        ['key'=>'delivered','label'=>'Delivered Orders','value'=>number_format((int) $stats['delivered'])],
        ['key'=>'active','label'=>'Active Orders','value'=>number_format((int) $stats['active'])],
        ['key'=>'cancelled','label'=>'Cancelled Orders','value'=>number_format((int) $stats['cancelled'])],
        ['key'=>'gmv','label'=>'Gross Merchandise Value','value'=>$money($stats['gmv'])],
        ['key'=>'delivery_fees','label'=>'Delivery Fees','value'=>$money($stats['delivery_fees'])],
        ['key'=>'registrations','label'=>'Registrations','value'=>number_format((int) $stats['registrations'])],
        ['key'=>'pending_registrations','label'=>'Pending Registrations','value'=>number_format((int) $stats['pending_registrations'])],
    ];
@endphp

<div class="footer">
    <span class="footer-left">SARI Logistics • Platform Management Report • Internal Use</span>
    <span class="footer-right">Page <span class="page-number"></span></span>
</div>

<table class="document-header">
    <tr>
        <td>
            <div class="company-name">SARI <span class="gold">LOGISTICS</span></div>
            <div class="document-type">Management Information Report</div>
            <div class="document-title">Platform Operations Report</div>
            <div class="document-subtitle">
                Orders, delivery performance, financial activity, and registration records for the selected reporting period.
            </div>
        </td>

        <td class="header-meta">
            <span class="classification">Internal Use</span>
            <div class="generated-at">
                Generated: {{ $generatedAt->format('F j, Y · h:i A') }}
            </div>
        </td>
    </tr>
</table>

<div class="gold-rule"></div>

<table class="report-meta">
    <tr>
        <th>Report Period</th>
        <td>{{ $period['from']->format('F j, Y') }} to {{ $period['to']->format('F j, Y') }}</td>

        <th>Reporting Days</th>
        <td>{{ number_format((int) $period['days']) }}</td>

        <th>Prepared By</th>
        <td>SARI Admin System</td>
    </tr>
</table>

<section class="section">
    <h2 class="section-heading">1. Management Summary</h2>
    <div class="section-note">Consolidated platform figures for the selected reporting period.</div>

    <table class="management-summary">
        <thead>
            <tr>
                <th style="width:44%;">Metric</th>
                <th style="width:24%;" class="right">Current Period</th>
                <th style="width:32%;" class="right">Change vs Previous Period</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summaryRows as $row)
                @php
                    $delta = $comparisons[$row['key']] ?? null;
                    $changeClass = $delta === null || abs($delta) < .05
                        ? 'change-neutral'
                        : ($delta > 0 ? 'change-positive' : 'change-negative');
                @endphp
                <tr>
                    <td>{{ $row['label'] }}</td>
                    <td class="value">{{ $row['value'] }}</td>
                    <td class="change {{ $changeClass }}">
                        @if($delta === null)
                            New
                        @elseif(abs($delta) < .05)
                            0.0%
                        @else
                            {{ $delta > 0 ? '+' : '' }}{{ number_format($delta,1) }}%
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>

<section class="section">
    <h2 class="section-heading">2. Order Status Distribution</h2>
    <table class="plain-table">
        <thead>
            <tr>
                <th>Status</th>
                <th class="right">Orders</th>
                <th class="right">Share</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statusBreakdown as $status => $count)
                <tr>
                    <td>{{ str($status)->replace('_',' ')->title() }}</td>
                    <td class="right">{{ number_format((int) $count) }}</td>
                    <td class="right">{{ number_format(((int) $count / $statusTotal) * 100, 1) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No order activity recorded for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>

<section class="section">
    <h2 class="section-heading">3. Report Control Information</h2>
    <table class="plain-table">
        <tbody>
            <tr>
                <th style="width:30%;">Period Start</th>
                <td>{{ $period['from']->format('F j, Y') }}</td>
            </tr>
            <tr>
                <th>Period End</th>
                <td>{{ $period['to']->format('F j, Y') }}</td>
            </tr>
            <tr>
                <th>Total Orders</th>
                <td>{{ number_format((int) $stats['orders']) }}</td>
            </tr>
            <tr>
                <th>Total Registrations</th>
                <td>{{ number_format((int) $stats['registrations']) }}</td>
            </tr>
        </tbody>
    </table>
</section>

<section class="section">
    <h2 class="section-heading">4. Order Register</h2>
    <div class="section-note">Detailed order records created within the selected reporting period.</div>

    @if($orders->isNotEmpty())
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:17%;">Order Number</th>
                    <th style="width:18%;">Buyer</th>
                    <th style="width:13%;">Status</th>
                    <th style="width:12%;">Payment</th>
                    <th style="width:13%;" class="right">Total</th>
                    <th style="width:14%;">Created</th>
                    <th style="width:13%;">Delivered</th>
                </tr>
            </thead>

            <tbody>
                @foreach($orders as $order)
                    @php
                        $statusKey = strtolower((string) $order->status);
                        $statusClass = $statusKey === 'delivered'
                            ? 'status-delivered'
                            : ($statusKey === 'cancelled' ? 'status-cancelled' : 'status-active');
                    @endphp

                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>{{ $order->buyer_name ?: 'Buyer' }}</td>
                        <td><span class="status-text {{ $statusClass }}">{{ $order->statusLabel() }}</span></td>
                        <td>{{ str((string) $order->payment_status)->replace('_',' ')->title() }}</td>
                        <td class="right"><strong>{{ $money($order->total) }}</strong></td>
                        <td>{{ $order->created_at?->format('M j, Y') ?: '-' }}</td>
                        <td>{{ $order->delivered_at?->format('M j, Y') ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">No order records were created during this report period.</div>
    @endif
</section>

<div class="page-break"></div>

<section class="section">
    <h2 class="section-heading">5. Registration Register</h2>
    <div class="section-note">Registration applications submitted within the selected reporting period.</div>

    @if($applications->isNotEmpty())
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:8%;">ID</th>
                    <th style="width:13%;">Role</th>
                    <th style="width:29%;">Applicant</th>
                    <th style="width:15%;">Status</th>
                    <th style="width:18%;">Submitted</th>
                    <th style="width:17%;">Reviewed</th>
                </tr>
            </thead>

            <tbody>
                @foreach($applications as $application)
                    <tr>
                        <td>{{ $application->id }}</td>
                        <td>{{ str((string) $application->role)->title() }}</td>
                        <td>
                            <strong>{{ $application->fullName() }}</strong><br>
                            <span style="color:#777777;font-size:6.5px;">{{ $application->email }}</span>
                        </td>
                        <td>{{ str((string) $application->status)->title() }}</td>
                        <td>{{ $application->created_at?->format('M j, Y') ?: '-' }}</td>
                        <td>{{ $application->reviewed_at?->format('M j, Y') ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">No registration records were submitted during this report period.</div>
    @endif
</section>

<table class="signoff">
    <tr>
        <td><div class="sign-line">Prepared By</div></td>
        <td><div class="sign-line">Reviewed By</div></td>
        <td><div class="sign-line">Approved By</div></td>
    </tr>
</table>

<script>
    (function () {
        let autoPrintStarted = false;

        function printReportOnce() {
            if (autoPrintStarted) return;
            autoPrintStarted = true;
            window.print();
        }

        document.addEventListener('click', function (event) {
            if (event.target.closest('[data-report-close]')) {
                window.close();
                return;
            }

            if (event.target.closest('[data-report-print]')) {
                window.print();
            }
        });

        window.addEventListener('load', function () {
            const fontsReady = document.fonts?.ready || Promise.resolve();

            fontsReady.then(function () {
                requestAnimationFrame(function () {
                    if ('requestIdleCallback' in window) {
                        requestIdleCallback(printReportOnce, { timeout: 700 });
                    } else {
                        window.setTimeout(printReportOnce, 120);
                    }
                });
            });
        }, { once: true });
    })();
</script>
</body>
</html>
