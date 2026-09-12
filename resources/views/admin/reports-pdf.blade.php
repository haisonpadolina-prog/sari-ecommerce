<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SARI Platform Management Report</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 14mm 12mm 15mm 12mm;
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
            font-family: Arial, Helvetica, sans-serif;
            color: #222222;
            font-size: 9px;
            line-height: 1.38;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Screen-only controls. They are hidden in the printed/PDF document. */
        .print-toolbar {
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 0 0 14px;
            border: 1px solid #dedede;
            background: #ffffff;
            padding: 10px 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,.08);
        }

        .print-toolbar strong {
            display: block;
            font-size: 11px;
            color: #1c1c1c;
        }

        .print-toolbar span {
            display: block;
            margin-top: 2px;
            color: #707070;
            font-size: 8px;
        }

        .toolbar-actions {
            display: flex;
            gap: 7px;
        }

        .toolbar-btn {
            height: 34px;
            border: 1px solid #d3d3d3;
            background: #ffffff;
            padding: 0 12px;
            color: #333333;
            font: inherit;
            font-size: 8px;
            font-weight: 700;
            cursor: pointer;
        }

        .toolbar-btn.primary {
            border-color: #a97012;
            background: #c58d20;
            color: #ffffff;
        }


        @media screen {
            body {
                max-width: 210mm;
                margin: 0 auto;
                padding: 10mm 12mm;
                box-shadow: 0 0 30px rgba(0,0,0,.08);
            }
        }

        @media print {
            .print-toolbar {
                display: none !important;
            }
        }

        /* Document header */
        .document-header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .document-header td {
            vertical-align: top;
        }

        .company-name {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: .8px;
            color: #17130e;
        }

        .company-name .gold {
            color: #b77b16;
        }

        .document-type {
            margin-top: 3px;
            color: #4b4b4b;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
        }

        .document-title {
            margin-top: 8px;
            color: #17130e;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.15;
        }

        .document-subtitle {
            margin-top: 3px;
            color: #6f6a62;
            font-size: 8px;
        }

        .header-meta {
            width: 220px;
            text-align: right;
        }

        .classification {
            display: inline-block;
            border: 1px solid #d8c7a5;
            padding: 4px 7px;
            color: #7c5a1f;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
        }

        .generated-at {
            margin-top: 8px;
            color: #77716a;
            font-size: 7.5px;
        }

        .gold-rule {
            height: 2px;
            margin: 9px 0 10px;
            background: #b77b16;
        }

        /* Report metadata */
        .report-meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .report-meta th,
        .report-meta td {
            border: 1px solid #d8d8d8;
            padding: 6px 8px;
            text-align: left;
            vertical-align: middle;
        }

        .report-meta th {
            width: 12%;
            background: #f3f3f3;
            color: #4f4f4f;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .report-meta td {
            width: 21%;
            color: #222222;
            font-size: 8px;
            font-weight: 600;
        }

        .section {
            margin-top: 13px;
        }

        .section-heading {
            margin: 0 0 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #bdbdbd;
            color: #202020;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .section-note {
            margin: -2px 0 6px;
            color: #777777;
            font-size: 7px;
        }

        /* Flat corporate summary table — no cards */
        .management-summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        .management-summary th,
        .management-summary td {
            border: 1px solid #d9d9d9;
            padding: 6px 8px;
        }

        .management-summary th {
            background: #f2f2f2;
            color: #444444;
            font-size: 7px;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: .35px;
        }

        .management-summary td {
            color: #222222;
            font-size: 8px;
        }

        .management-summary td.value {
            font-size: 10px;
            font-weight: 700;
            text-align: right;
            white-space: nowrap;
        }

        .management-summary td.change {
            width: 15%;
            text-align: right;
            font-size: 7.5px;
            white-space: nowrap;
        }

        .change-positive {
            color: #4f6e52;
        }

        .change-negative {
            color: #9d5147;
        }

        .change-neutral {
            color: #777777;
        }

        .plain-table,
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .plain-table th,
        .plain-table td {
            border: 1px solid #dcdcdc;
            padding: 5px 7px;
            text-align: left;
        }

        .plain-table th {
            background: #f2f2f2;
            color: #4f4f4f;
            font-size: 7px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .plain-table td {
            color: #2c2c2c;
            font-size: 8px;
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
            border: 1px solid #cfcfcf;
            background: #eeeeee;
            padding: 5px 5px;
            color: #3f3f3f;
            font-size: 6.5px;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: .25px;
        }

        .data-table td {
            border: 1px solid #dddddd;
            padding: 5px 5px;
            color: #333333;
            font-size: 6.7px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .data-table tbody tr:nth-child(even) td {
            background: #fafafa;
        }

        .data-table .right {
            text-align: right;
        }

        .data-table .center {
            text-align: center;
        }

        .status-text {
            font-weight: 700;
        }

        .status-delivered {
            color: #4f6e52;
        }

        .status-cancelled {
            color: #9d5147;
        }

        .status-active {
            color: #536d69;
        }

        .page-break {
            page-break-before: always;
        }

        .empty {
            border: 1px solid #d9d9d9;
            padding: 14px;
            color: #777777;
            font-size: 8px;
            text-align: center;
        }

        .signoff {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            page-break-inside: avoid;
        }

        .signoff td {
            width: 33.333%;
            padding-right: 18px;
            vertical-align: bottom;
        }

        .sign-line {
            margin-top: 24px;
            border-top: 1px solid #555555;
            padding-top: 4px;
            color: #555555;
            font-size: 7px;
            text-align: center;
        }

        .footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -9mm;
            border-top: 1px solid #cfcfcf;
            padding-top: 4px;
            color: #777777;
            font-size: 6.5px;
        }

        .footer-left {
            float: left;
        }

        .footer-right {
            float: right;
        }

        .page-number:after {
            content: counter(page);
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
        <button type="button" class="toolbar-btn" onclick="window.close()">Close</button>
        <button type="button" class="toolbar-btn primary" onclick="window.print()">Save / Print PDF</button>
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
    window.addEventListener('load', function () {
        window.setTimeout(function () {
            window.print();
        }, 350);
    });
</script>
</body>
</html>
