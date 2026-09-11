@extends('layouts.admin')

@section('title','Commissions — SARI Admin')
@section('page-title','Commissions')

@section('content')
<style>
    :root {
        --commission-brand: #d99a00;
        --commission-brand-strong: #bd8205;
        --commission-brand-soft: #fff7e6;
        --commission-ink: #101828;
        --commission-text: #344054;
        --commission-muted: #667085;
        --commission-subtle: #98a2b3;
        --commission-line: #e8edf3;
        --commission-line-strong: #dde3ea;
        --commission-surface: #ffffff;
        --commission-canvas: #f8fafc;
    }

    .commissions-page {
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        color: var(--commission-ink);
    }

    .commission-surface {
        background: var(--commission-surface);
        border: 1px solid var(--commission-line);
        border-radius: 16px;
        box-shadow: 0 1px 2px rgba(16,24,40,.02), 0 8px 24px rgba(16,24,40,.045);
    }

    /* Header */
    .commission-header-main {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .commission-header-icon {
        display: grid;
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        place-items: center;
        border: 1px solid #f2dfb7;
        border-radius: 14px;
        background: #fff9ed;
        color: #c98500;
        box-shadow: 0 6px 18px rgba(202,133,0,.08);
    }

    .commission-header-icon svg {
        width: 20px;
        height: 20px;
    }

    .commission-eyebrow {
        color: #7d8898;
        font-size: 9px;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .commission-title {
        margin-top: 4px;
        color: var(--commission-ink);
        font-size: clamp(1.65rem, 1.48rem + .55vw, 2.05rem);
        font-weight: 700;
        line-height: 1.08;
        letter-spacing: -.04em;
    }

    .commission-subtitle {
        max-width: 920px;
        margin-top: 6px;
        color: var(--commission-muted);
        font-size: clamp(.72rem, .69rem + .08vw, .79rem);
        line-height: 1.65;
    }

    /* Summary cards */
    .commission-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0,1fr));
        gap: 12px;
    }

    .commission-stat {
        position: relative;
        min-height: 104px;
        overflow: hidden;
        padding: 16px;
        border: 1px solid var(--commission-line);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 1px 2px rgba(16,24,40,.02), 0 7px 22px rgba(16,24,40,.04);
        transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    .commission-stat:hover {
        transform: translateY(-2px);
        border-color: #dbe2ea;
        box-shadow: 0 2px 4px rgba(16,24,40,.03), 0 12px 28px rgba(16,24,40,.065);
    }

    .commission-stat-icon {
        display: grid;
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        place-items: center;
        border: 1px solid transparent;
        border-radius: 13px;
    }

    .commission-stat-icon svg {
        width: 21px;
        height: 21px;
    }

    .commission-stat-icon.rate {
        border-color: #f2dfb9;
        background: #fff7e7;
        color: #d59000;
    }

    .commission-stat-icon.orders {
        border-color: #dce9f6;
        background: #f0f7ff;
        color: #3978a9;
    }

    .commission-stat-icon.sales {
        border-color: #e5e0f5;
        background: #f7f4fc;
        color: #7159a8;
    }

    .commission-stat-icon.earned {
        border-color: #d9ebe0;
        background: #f0f8f3;
        color: #3e8060;
    }

    .commission-stat-label {
        color: #667085;
        font-size: 10px;
        font-weight: 500;
        line-height: 1.35;
    }

    .commission-stat-value {
        margin-top: 4px;
        color: #101828;
        font-size: 24px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -.035em;
    }

    .commission-stat-helper {
        margin-top: 7px;
        color: #98a2b3;
        font-size: 8.5px;
        line-height: 1.4;
    }

    /* Analytics */
    .commission-analytics-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 14px;
    }

    .commission-panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .commission-panel-title {
        color: #1d2939;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.3;
    }

    .commission-panel-copy {
        margin-top: 4px;
        color: #98a2b3;
        font-size: 8.5px;
        line-height: 1.5;
    }

    /* Modern line / area chart */
    .commission-chart-panel {
        min-height: 310px;
        padding: 18px 18px 14px;
    }

    .commission-range-tabs {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px;
        border: 1px solid var(--commission-line);
        border-radius: 10px;
        background: #f9fafb;
    }

    .commission-range-button {
        height: 29px;
        min-width: 40px;
        border: 0;
        border-radius: 7px;
        background: transparent;
        padding: 0 9px;
        color: #667085;
        font-size: 9px;
        font-weight: 600;
        transition: background-color .16s ease, color .16s ease, box-shadow .16s ease;
    }

    .commission-range-button:hover {
        color: #344054;
        background: #fff;
    }

    .commission-range-button.is-active {
        background: #fff5dc;
        color: #b87500;
        box-shadow: inset 0 0 0 1px #f0d69e, 0 1px 2px rgba(16,24,40,.05);
    }

    .commission-chart-wrap {
        position: relative;
        min-height: 236px;
        margin-top: 14px;
        overflow: hidden;
        border-radius: 12px;
    }

    .commission-chart-svg {
        display: block;
        width: 100%;
        height: 236px;
        overflow: visible;
    }

    .commission-chart-tooltip {
        position: absolute;
        z-index: 5;
        min-width: 96px;
        pointer-events: none;
        transform: translate(-50%, -115%);
        border: 1px solid #e4e7ec;
        border-radius: 10px;
        background: rgba(255,255,255,.97);
        padding: 8px 10px;
        box-shadow: 0 8px 22px rgba(16,24,40,.11);
        opacity: 0;
        transition: opacity .12s ease;
    }

    .commission-chart-tooltip.is-visible {
        opacity: 1;
    }

    .commission-chart-tooltip-date {
        color: #667085;
        font-size: 8px;
        font-weight: 500;
    }

    .commission-chart-tooltip-value {
        margin-top: 2px;
        color: #101828;
        font-size: 10px;
        font-weight: 700;
    }

    .commission-chart-legend {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #667085;
        font-size: 8.5px;
        font-weight: 500;
    }

    .commission-chart-legend::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: #d99a00;
        box-shadow: 0 0 0 3px rgba(217,154,0,.10);
    }

    .commission-chart-empty {
        display: grid;
        min-height: 224px;
        place-items: center;
        color: #98a2b3;
        font-size: 9px;
        text-align: center;
    }

    /* Donut card */
    .commission-donut-wrap {
        min-height: 310px;
        padding: 18px;
    }

    .commission-donut {
        position: relative;
        display: grid;
        width: 142px;
        height: 142px;
        place-items: center;
        border-radius: 999px;
        background: conic-gradient(
            #d99a00 0deg var(--commission-deg),
            #f0f1f3 var(--commission-deg) 360deg
        );
        box-shadow: inset 0 0 0 1px rgba(16,24,40,.025);
    }

    .commission-donut::before {
        content: "";
        position: absolute;
        width: 104px;
        height: 104px;
        border-radius: 999px;
        background: #fff;
        box-shadow: 0 2px 10px rgba(16,24,40,.035), inset 0 0 0 1px #edf0f3;
    }

    .commission-donut-center {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .commission-donut-value {
        color: #101828;
        font-size: 25px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .commission-donut-label {
        margin-top: 6px;
        color: #528166;
        font-size: 8.5px;
        font-weight: 600;
    }

    .commission-rate-meta {
        margin-top: 20px;
        border-top: 1px solid #eef1f4;
    }

    .commission-rate-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-height: 42px;
        border-bottom: 1px solid #f1f3f5;
    }

    .commission-rate-row:last-child {
        border-bottom: 0;
    }

    .commission-rate-row-label {
        color: #344054;
        font-size: 9px;
        font-weight: 600;
    }

    .commission-rate-row-copy {
        margin-top: 2px;
        color: #98a2b3;
        font-size: 7.5px;
    }

    .commission-rate-row-value {
        color: #1d2939;
        font-size: 9px;
        font-weight: 700;
        white-space: nowrap;
    }

    /* Search/filter */
    .commission-filter {
        padding: 10px;
    }

    .commission-filter-grid {
        display: grid;
        grid-template-columns: minmax(360px,1fr) 190px 124px 82px;
        gap: 10px;
    }

    .commission-search {
        position: relative;
    }

    .commission-search svg {
        position: absolute;
        top: 50%;
        left: 15px;
        width: 15px;
        height: 15px;
        color: #98a2b3;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .commission-control {
        width: 100%;
        height: 42px;
        border: 1px solid #e4e7ec;
        border-radius: 10px;
        background: #fff;
        color: #344054;
        font-size: 10px;
        box-shadow: 0 1px 2px rgba(16,24,40,.025);
    }

    .commission-control::placeholder {
        color: #a7afbd;
    }

    .commission-control:focus {
        outline: none;
        border-color: #e1b75b;
        box-shadow: 0 0 0 3px rgba(217,154,0,.10);
    }

    .commission-search input {
        padding: 0 15px 0 41px;
    }

    .commission-select-wrap {
        position: relative;
    }

    .commission-select-wrap select {
        appearance: none;
        padding: 0 36px 0 13px;
        cursor: pointer;
    }

    .commission-select-chevron {
        position: absolute;
        top: 50%;
        right: 13px;
        width: 14px;
        height: 14px;
        color: #98a2b3;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .commission-filter-apply,
    .commission-filter-reset {
        display: inline-flex;
        height: 42px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 600;
        transition: background-color .15s ease, border-color .15s ease, color .15s ease, transform .15s ease;
    }

    .commission-filter-apply {
        border: 1px solid #cf9007;
        background: #d99a00;
        color: #fff;
        box-shadow: 0 6px 14px rgba(217,154,0,.16);
    }

    .commission-filter-apply:hover {
        background: #c98c00;
        transform: translateY(-1px);
    }

    .commission-filter-reset {
        border: 1px solid #e4e7ec;
        background: #fff;
        color: #475467;
    }

    .commission-filter-reset:hover {
        background: #f9fafb;
    }

    /* Section head / table */
    .commission-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 16px 18px;
        border-bottom: 1px solid #edf0f3;
    }

    .commission-table {
        width: 100%;
        min-width: 920px;
        border-collapse: collapse;
        text-align: left;
    }

    .commission-table thead {
        background: #fbfcfd;
    }

    .commission-table th {
        padding: 12px 16px;
        border-bottom: 1px solid #edf0f3;
        color: #7b8492;
        font-size: 8px;
        font-weight: 700;
        line-height: 1.35;
        letter-spacing: .055em;
        text-transform: uppercase;
    }

    .commission-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f0f2f4;
        color: #475467;
        font-size: 9px;
        line-height: 1.45;
    }

    .commission-table tbody tr {
        transition: background-color .14s ease;
    }

    .commission-table tbody tr:hover {
        background: #fcfcfd;
    }

    .commission-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .commission-order {
        color: #1d2939;
        font-size: 9.5px;
        font-weight: 700;
    }

    .commission-amount {
        color: #1d2939;
        font-size: 9.5px;
        font-weight: 700;
    }

    .commission-earned {
        color: #9a6800;
    }

    .commission-delivered-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border: 1px solid #d7eadf;
        border-radius: 999px;
        background: #f1f8f4;
        padding: 4px 8px;
        color: #3b7957;
        font-size: 8px;
        font-weight: 600;
    }

    .commission-delivered-badge::before {
        content: "";
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: currentColor;
    }

    .commission-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 11px 16px;
        border-top: 1px solid #edf0f3;
        color: #7b8492;
        font-size: 8.5px;
    }

    /* Payouts */
    .payout-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .payout-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #e4e7ec;
        border-radius: 999px;
        background: #fff;
        padding: 5px 9px;
        color: #667085;
        font-size: 8px;
        font-weight: 600;
    }

    .payout-chip::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: #98a2b3;
    }

    .payout-chip.pending::before { background: #d99a00; }
    .payout-chip.approved::before { background: #3978a9; }
    .payout-chip.paid::before { background: #3e8060; }

    .payout-list {
        padding: 0;
    }

    .payout-row {
        display: grid;
        grid-template-columns: minmax(220px,1.4fr) 150px 140px minmax(280px,1.5fr);
        gap: 16px;
        align-items: center;
        min-height: 74px;
        padding: 13px 18px;
        border-bottom: 1px solid #f0f2f4;
        background: #fff;
    }

    .payout-row:last-child {
        border-bottom: 0;
    }

    .payout-row:hover {
        background: #fcfcfd;
    }

    .payout-rider {
        color: #1d2939;
        font-size: 9.5px;
        font-weight: 700;
    }

    .payout-date {
        margin-top: 4px;
        color: #98a2b3;
        font-size: 8px;
    }

    .payout-amount {
        color: #1d2939;
        font-size: 9.5px;
        font-weight: 700;
    }

    .payout-status {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 5px 9px;
        font-size: 8px;
        font-weight: 600;
    }

    .payout-status::before {
        content: "";
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: currentColor;
    }

    .payout-status-pending {
        border: 1px solid #edd8a9;
        background: #fff8e8;
        color: #a87100;
    }

    .payout-status-approved {
        border: 1px solid #d3e2ee;
        background: #f3f8fb;
        color: #3978a9;
    }

    .payout-status-paid {
        border: 1px solid #d7eadf;
        background: #f1f8f4;
        color: #3e8060;
    }

    .payout-status-rejected {
        border: 1px solid #eed9d4;
        background: #fff5f3;
        color: #a65d4c;
    }

    .payout-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .payout-reason {
        height: 38px;
        min-width: 140px;
        flex: 1;
        border: 1px solid #e4e7ec;
        border-radius: 9px;
        background: #fff;
        padding: 0 10px;
        color: #344054;
        font-size: 8.5px;
    }

    .payout-reason:focus {
        outline: none;
        border-color: #e1b75b;
        box-shadow: 0 0 0 3px rgba(217,154,0,.09);
    }

    .payout-action-button {
        display: inline-flex;
        height: 38px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: 1px solid #e4e7ec;
        border-radius: 9px;
        background: #fff;
        padding: 0 11px;
        color: #344054;
        font-size: 8.5px;
        font-weight: 600;
        white-space: nowrap;
        transition: color .15s ease, border-color .15s ease, background-color .15s ease, transform .15s ease;
    }

    .payout-action-button svg {
        width: 15px;
        height: 15px;
        color: currentColor;
    }

    .payout-action-button:hover {
        transform: translateY(-1px);
    }

    .payout-approve:hover {
        border-color: #b9dfc7;
        background: #f4fbf6;
        color: #2d7d4e;
    }

    .payout-reject:hover {
        border-color: #efc5bd;
        background: #fff7f5;
        color: #b4513e;
    }

    .payout-paid:hover {
        border-color: #c7dceb;
        background: #f5faff;
        color: #3978a9;
    }

    .commission-empty {
        padding: 40px 20px;
        color: #98a2b3;
        font-size: 8.5px;
        text-align: center;
    }

    .commission-empty-state {
        display: flex;
        min-height: 140px;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 26px 18px;
        text-align: left;
    }

    .commission-empty-icon {
        display: grid;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        place-items: center;
        border: 1px solid #e4e7ec;
        border-radius: 12px;
        background: #f9fafb;
        color: #98a2b3;
    }

    .commission-empty-icon svg {
        width: 19px;
        height: 19px;
    }

    .commission-empty-title {
        color: #344054;
        font-size: 9.5px;
        font-weight: 700;
    }

    .commission-empty-copy {
        margin-top: 3px;
        color: #98a2b3;
        font-size: 8px;
    }

    @media (max-width: 1199px) {
        .commission-summary-grid {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }

        .commission-analytics-grid {
            grid-template-columns: 1fr;
        }

        .commission-donut-wrap {
            min-height: auto;
        }

        .commission-filter-grid {
            grid-template-columns: minmax(0,1fr) 180px;
        }

        .commission-search {
            grid-column: 1 / -1;
        }

        .payout-row {
            grid-template-columns: 1fr 130px;
        }

        .payout-row > div:nth-child(3),
        .payout-row > div:nth-child(4) {
            grid-column: 1 / -1;
        }

        .payout-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 639px) {
        .commission-header-main {
            align-items: flex-start;
            gap: 11px;
        }

        .commission-summary-grid,
        .commission-filter-grid {
            grid-template-columns: 1fr;
        }

        .commission-search {
            grid-column: auto;
        }

        .commission-stat {
            min-height: 98px;
            padding: 14px;
        }

        .commission-panel-head {
            flex-direction: column;
            align-items: stretch;
        }

        .commission-range-tabs {
            width: fit-content;
        }

        .commission-chart-panel,
        .commission-donut-wrap {
            padding: 15px;
        }

        .commission-chart-svg {
            height: 210px;
        }

        .commission-section-head,
        .commission-table-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .payout-row {
            grid-template-columns: 1fr;
        }

        .payout-row > div:nth-child(3),
        .payout-row > div:nth-child(4) {
            grid-column: auto;
        }

        .payout-actions,
        .payout-actions form {
            width: 100%;
        }

        .payout-actions form {
            display: flex;
        }

        .payout-reason {
            min-width: 0;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .commission-stat,
        .commission-range-button,
        .commission-filter-apply,
        .payout-action-button,
        .commission-chart-tooltip {
            transition: none !important;
        }
    }
</style>

@php
    $commissionRate = max(0, min(100, (float) ($stats['rate'] ?? 0)));
    $commissionDegrees = $commissionRate * 3.6;

    $deliveredOrderCount = (int) ($stats['delivered_orders'] ?? 0);
    $grossSales = (float) ($stats['gross_sales'] ?? 0);
    $totalCommission = (float) ($stats['commission'] ?? 0);

    /* Keep the chart grounded in the same delivered-order rows already used by this page. */
    $chartPayload = collect($rows ?? [])
        ->filter(fn ($row) => isset($row['order']) && optional($row['order']->delivered_at)->timestamp)
        ->map(fn ($row) => [
            'date' => optional($row['order']->delivered_at)->format('Y-m-d'),
            'commission' => round((float) ($row['commission'] ?? 0), 2),
        ])
        ->values();

    $payoutCollection = collect($payoutRequests ?? []);
    $pendingPayouts = $payoutCollection->where('status', 'pending')->count();
    $approvedPayouts = $payoutCollection->where('status', 'approved')->count();
    $paidPayouts = $payoutCollection->where('status', 'paid')->count();
    $rejectedPayouts = $payoutCollection->where('status', 'rejected')->count();

    $payoutTone = fn ($status) => match(strtolower((string) $status)) {
        'approved' => 'payout-status-approved',
        'paid' => 'payout-status-paid',
        'rejected' => 'payout-status-rejected',
        default => 'payout-status-pending',
    };
@endphp

<div class="commissions-page mx-auto w-full max-w-[1880px] pb-8">
    @if(session('success'))
        <div class="mb-4 flex items-start gap-3 rounded-[12px] border border-[#d7eadf] bg-[#f3faf5] px-4 py-3 shadow-[0_5px_16px_rgba(16,24,40,.035)]">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white text-[#3e8060]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="m8 12 2.5 2.5L16 9"></path>
                </svg>
            </span>
            <div>
                <p class="text-[9px] font-bold text-[#356b50]">Success</p>
                <p class="mt-0.5 text-[9px] text-[#667a6e]">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <section class="commission-header-main">
        <span class="commission-header-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <ellipse cx="10" cy="6" rx="5" ry="2.5"></ellipse>
                <path d="M5 6v4c0 1.4 2.2 2.5 5 2.5s5-1.1 5-2.5V6"></path>
                <path d="M5 10v4c0 1.4 2.2 2.5 5 2.5 1 0 2-.15 2.8-.42"></path>
                <circle cx="17" cy="15" r="4"></circle>
                <path d="M17 13.2v3.6M15.7 14.2h2.1c.7 0 1.1.35 1.1.85s-.4.85-1.1.85h-1.6"></path>
            </svg>
        </span>

        <div class="min-w-0">
            <p class="commission-eyebrow">Platform Finance</p>
            <h2 class="commission-title">Platform Commissions</h2>
            <p class="commission-subtitle">
                Monitor delivered-order revenue, track SARI commission earnings, and manage rider payout requests from one finance workspace.
            </p>
        </div>
    </section>

    {{-- SUMMARY --}}
    <section class="commission-summary-grid mt-4">
        <article class="commission-stat">
            <div class="flex h-full items-center gap-4">
                <span class="commission-stat-icon rate" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round">
                        <path d="M7 17 17 7"></path>
                        <circle cx="8" cy="8" r="2.1"></circle>
                        <circle cx="16" cy="16" r="2.1"></circle>
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="commission-stat-label">Commission Rate</p>
                    <p class="commission-stat-value">{{ rtrim(rtrim(number_format($commissionRate, 2), '0'), '.') }}%</p>
                    <p class="commission-stat-helper">Applied to delivered merchandise subtotal</p>
                </div>
            </div>
        </article>

        <article class="commission-stat">
            <div class="flex h-full items-center gap-4">
                <span class="commission-stat-icon orders" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 6h2l1.5 8.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 9H7"></path>
                        <circle cx="10" cy="19" r="1.3"></circle>
                        <circle cx="18" cy="19" r="1.3"></circle>
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="commission-stat-label">Delivered Orders</p>
                    <p class="commission-stat-value">{{ number_format($deliveredOrderCount) }}</p>
                    <p class="commission-stat-helper">Orders included in commission totals</p>
                </div>
            </div>
        </article>

        <article class="commission-stat">
            <div class="flex h-full items-center gap-4">
                <span class="commission-stat-icon sales" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19h16"></path>
                        <path d="M6 16v-4h3v4"></path>
                        <path d="M11 16V8h3v8"></path>
                        <path d="M16 16V5h3v11"></path>
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="commission-stat-label">Gross Sales</p>
                    <p class="commission-stat-value">₱{{ number_format($grossSales, 2) }}</p>
                    <p class="commission-stat-helper">Delivered merchandise subtotal</p>
                </div>
            </div>
        </article>

        <article class="commission-stat">
            <div class="flex h-full items-center gap-4">
                <span class="commission-stat-icon earned" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M7 7h10l2 3v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7l2-3Z"></path>
                        <path d="M9 7c0-1.7 1.3-3 3-3s3 1.3 3 3"></path>
                        <path d="M12 10.5v5"></path>
                        <path d="M10.5 12h2.2c.8 0 1.3.4 1.3 1s-.5 1-1.3 1h-1.9"></path>
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="commission-stat-label">SARI Commission</p>
                    <p class="commission-stat-value">₱{{ number_format($totalCommission, 2) }}</p>
                    <p class="commission-stat-helper">Platform earnings from delivered orders</p>
                </div>
            </div>
        </article>
    </section>

    {{-- ANALYTICS --}}
    <section class="commission-analytics-grid mt-4">
        <article class="commission-surface commission-chart-panel">
            <div class="commission-panel-head">
                <div>
                    <h3 class="commission-panel-title">Commission Performance</h3>
                    <p class="commission-panel-copy">Daily platform commission earnings from delivered orders.</p>
                </div>

                <div class="commission-range-tabs" aria-label="Commission chart range">
                    <button type="button" class="commission-range-button" data-commission-range="7">7D</button>
                    <button type="button" class="commission-range-button is-active" data-commission-range="30">30D</button>
                    <button type="button" class="commission-range-button" data-commission-range="90">90D</button>
                    <button type="button" class="commission-range-button" data-commission-range="365">1Y</button>
                </div>
            </div>

            @if($chartPayload->isNotEmpty())
                <div class="commission-chart-wrap" id="commissionChartWrap">
                    <svg
                        id="commissionChartSvg"
                        class="commission-chart-svg"
                        viewBox="0 0 1000 236"
                        role="img"
                        aria-label="Commission earnings trend"
                    ></svg>

                    <div id="commissionChartTooltip" class="commission-chart-tooltip" aria-hidden="true">
                        <p id="commissionChartTooltipDate" class="commission-chart-tooltip-date"></p>
                        <p id="commissionChartTooltipValue" class="commission-chart-tooltip-value"></p>
                    </div>
                </div>

                <div class="mt-2 flex items-center justify-between gap-3">
                    <span class="commission-chart-legend">Commission (₱)</span>
                    <span class="text-[8px] text-[#98a2b3]">Hover the chart to inspect a day</span>
                </div>
            @else
                <div class="commission-chart-empty">
                    <div>
                        <p class="font-semibold text-[#475467]">No chart data yet</p>
                        <p class="mt-1">Delivered orders will appear here once commission records are available.</p>
                    </div>
                </div>
            @endif
        </article>

        <article class="commission-surface commission-donut-wrap">
            <div class="commission-panel-head">
                <div>
                    <h3 class="commission-panel-title">Commission Rate</h3>
                    <p class="commission-panel-copy">Current platform percentage applied to eligible delivered sales.</p>
                </div>
            </div>

            <div class="mt-5 flex justify-center">
                <div
                    class="commission-donut"
                    style="--commission-deg: {{ $commissionDegrees }}deg"
                    role="img"
                    aria-label="Commission rate {{ $commissionRate }} percent"
                >
                    <div class="commission-donut-center">
                        <p class="commission-donut-value">{{ rtrim(rtrim(number_format($commissionRate, 2), '0'), '.') }}%</p>
                        <p class="commission-donut-label">Commission</p>
                    </div>
                </div>
            </div>

            <div class="commission-rate-meta">
                <div class="commission-rate-row">
                    <div>
                        <p class="commission-rate-row-label">Applied rate</p>
                        <p class="commission-rate-row-copy">Delivered merchandise only</p>
                    </div>
                    <span class="commission-rate-row-value">{{ rtrim(rtrim(number_format($commissionRate, 2), '0'), '.') }}%</span>
                </div>
                <div class="commission-rate-row">
                    <div>
                        <p class="commission-rate-row-label">Transaction basis</p>
                        <p class="commission-rate-row-copy">Applied per delivered order</p>
                    </div>
                    <span class="commission-rate-row-value">Subtotal only</span>
                </div>
            </div>
        </article>
    </section>

    {{-- LEDGER FILTER --}}
    <section class="commission-surface commission-filter mt-4">
        <div class="commission-filter-grid">
            <div class="commission-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.4-3.4"></path>
                </svg>
                <input id="commissionSearch" type="search" class="commission-control" placeholder="Search order number or seller...">
            </div>

            <div class="commission-select-wrap">
                <select id="commissionSort" class="commission-control">
                    <option value="default">Default Order</option>
                    <option value="commission_desc">Highest Commission</option>
                    <option value="subtotal_desc">Highest Subtotal</option>
                    <option value="latest">Latest Delivered</option>
                </select>
                <svg viewBox="0 0 24 24" class="commission-select-chevron" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="m7 10 5 5 5-5"></path>
                </svg>
            </div>

            <button id="commissionApplyFilter" type="button" class="commission-filter-apply">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="M4 6h16"></path>
                    <path d="M7 12h10"></path>
                    <path d="M10 18h4"></path>
                </svg>
                Apply Filter
            </button>

            <button id="commissionResetFilter" type="button" class="commission-filter-reset">Reset</button>
        </div>
    </section>

    {{-- COMMISSION LEDGER --}}
    <section class="commission-surface mt-4 overflow-hidden">
        <div class="commission-section-head">
            <div>
                <h3 class="commission-panel-title">Commission Ledger</h3>
                <p class="commission-panel-copy">Delivered orders included in platform commission calculations.</p>
            </div>
            <span class="text-[8px] text-[#98a2b3]">Showing delivered orders only</span>
        </div>

        <div class="overflow-x-auto">
            <table class="commission-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Seller</th>
                        <th>Subtotal</th>
                        <th>Commission</th>
                        <th>Delivered At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="commissionRows">
                    @forelse($rows as $row)
                        @php
                            $order = $row['order'];
                            $sellerName = (string) ($row['seller'] ?? 'Seller');
                            $subtotalValue = (float) ($row['subtotal'] ?? 0);
                            $commissionValue = (float) ($row['commission'] ?? 0);
                            $deliveredTimestamp = optional($order->delivered_at)->timestamp ?? 0;
                        @endphp
                        <tr
                            data-commission-row
                            data-order="{{ strtolower((string) $order->order_number) }}"
                            data-seller="{{ strtolower($sellerName) }}"
                            data-subtotal="{{ $subtotalValue }}"
                            data-commission="{{ $commissionValue }}"
                            data-delivered="{{ $deliveredTimestamp }}"
                        >
                            <td><span class="commission-order">{{ $order->order_number }}</span></td>
                            <td>{{ $sellerName }}</td>
                            <td class="commission-amount">₱{{ number_format($subtotalValue, 2) }}</td>
                            <td class="commission-amount commission-earned">₱{{ number_format($commissionValue, 2) }}</td>
                            <td>{{ $order->delivered_at?->format('M d, Y') ?: '—' }}</td>
                            <td><span class="commission-delivered-badge">Delivered</span></td>
                        </tr>
                    @empty
                        <tr data-empty-row>
                            <td colspan="6" class="commission-empty">No delivered orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="commission-table-footer">
            <p id="commissionResultCount">Showing {{ count($rows) }} of {{ count($rows) }} delivered orders</p>
            <p>Commission is recognized only for paid, delivered orders and is calculated from the merchandise subtotal. Delivery fees are tracked separately as rider earnings.</p>
        </div>
    </section>

    {{-- RIDER PAYOUTS --}}
    <section class="commission-surface mt-4 overflow-hidden">
        <div class="commission-section-head">
            <div>
                <h3 class="commission-panel-title">Rider Payout Requests</h3>
                <p class="commission-panel-copy">Requests reserve exact available rider-earning ledger entries; rejected requests release them back to the rider balance.</p>
            </div>

            <div class="payout-summary">
                <span class="payout-chip pending">Pending {{ $pendingPayouts }}</span>
                <span class="payout-chip approved">Approved {{ $approvedPayouts }}</span>
                <span class="payout-chip paid">Paid {{ $paidPayouts }}</span>
                @if($rejectedPayouts > 0)
                    <span class="payout-chip">Rejected {{ $rejectedPayouts }}</span>
                @endif
            </div>
        </div>

        <div class="payout-list">
            @forelse($payoutRequests as $payout)
                @php
                    $riderName = trim(($payout->courier?->first_name ?? '').' '.($payout->courier?->last_name ?? '')) ?: 'Rider';
                    $status = strtolower((string) $payout->status);
                @endphp

                <article class="payout-row">
                    <div>
                        <p class="payout-rider">{{ $riderName }}</p>
                        <p class="payout-date">{{ $payout->created_at?->format('M d, Y h:i A') ?: '—' }}</p>
                    </div>

                    <div>
                        <p class="payout-amount">₱{{ number_format((float) $payout->amount, 2) }}</p>
                    </div>

                    <div>
                        <span class="payout-status {{ $payoutTone($status) }}">{{ ucfirst($status) }}</span>
                    </div>

                    <div class="payout-actions">
                        @if($status === 'pending')
                            <form method="POST" action="{{ route('admin.commissions.payouts.approve', $payout) }}">
                                @csrf
                                <button class="payout-action-button payout-approve">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <path d="m8 12 2.5 2.5L16 9"></path>
                                        <circle cx="12" cy="12" r="9"></circle>
                                    </svg>
                                    Approve
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.commissions.payouts.reject', $payout) }}" class="flex min-w-0 flex-1 gap-2">
                                @csrf
                                <input name="admin_note" required placeholder="Reason for rejection" class="payout-reason">
                                <button class="payout-action-button payout-reject">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="m9 9 6 6"></path>
                                        <path d="m15 9-6 6"></path>
                                    </svg>
                                    Reject
                                </button>
                            </form>
                        @elseif($status === 'approved')
                            <form method="POST" action="{{ route('admin.commissions.payouts.paid', $payout) }}">
                                @csrf
                                <button class="payout-action-button payout-paid">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <path d="M4 7h16v10H4z"></path>
                                        <path d="M8 12h8"></path>
                                    </svg>
                                    Mark Paid
                                </button>
                            </form>
                        @else
                            <span class="text-[8.5px] text-[#98a2b3]">No action required</span>
                        @endif
                    </div>
                </article>
            @empty
                <div class="commission-empty-state">
                    <span class="commission-empty-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 3h8l4 4v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"></path>
                            <path d="M15 3v5h5"></path>
                            <path d="M9 14h6"></path>
                            <path d="M9 17h4"></path>
                        </svg>
                    </span>
                    <div>
                        <p class="commission-empty-title">No payout requests yet.</p>
                        <p class="commission-empty-copy">Rider payout requests will appear here once created.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>
</div>

<script>
(function () {
    /* Ledger filtering */
    const body = document.getElementById('commissionRows');
    const rows = Array.from(document.querySelectorAll('[data-commission-row]'));
    const search = document.getElementById('commissionSearch');
    const sort = document.getElementById('commissionSort');
    const apply = document.getElementById('commissionApplyFilter');
    const reset = document.getElementById('commissionResetFilter');
    const count = document.getElementById('commissionResultCount');

    const normalize = value => (value || '').toString().trim().toLowerCase();

    function applyLedgerFilter() {
        const query = normalize(search?.value);
        const sortMode = sort?.value || 'default';

        const visibleRows = rows.filter(row => {
            const haystack = `${row.dataset.order || ''} ${row.dataset.seller || ''}`;
            return !query || normalize(haystack).includes(query);
        });

        const sortedRows = [...visibleRows];

        if (sortMode === 'commission_desc') {
            sortedRows.sort((a,b) => Number(b.dataset.commission || 0) - Number(a.dataset.commission || 0));
        } else if (sortMode === 'subtotal_desc') {
            sortedRows.sort((a,b) => Number(b.dataset.subtotal || 0) - Number(a.dataset.subtotal || 0));
        } else if (sortMode === 'latest') {
            sortedRows.sort((a,b) => Number(b.dataset.delivered || 0) - Number(a.dataset.delivered || 0));
        }

        rows.forEach(row => {
            row.hidden = !visibleRows.includes(row);
        });

        if (body && sortMode !== 'default') {
            sortedRows.forEach(row => body.appendChild(row));
        }

        if (count) {
            count.textContent = `Showing ${visibleRows.length} of ${rows.length} delivered orders`;
        }
    }

    search?.addEventListener('input', applyLedgerFilter);
    sort?.addEventListener('change', applyLedgerFilter);
    apply?.addEventListener('click', applyLedgerFilter);

    search?.addEventListener('keydown', event => {
        if (event.key === 'Enter') {
            event.preventDefault();
            applyLedgerFilter();
        }
    });

    reset?.addEventListener('click', () => {
        if (search) search.value = '';
        if (sort) sort.value = 'default';

        rows.forEach(row => {
            row.hidden = false;
            body?.appendChild(row);
        });

        if (count) {
            count.textContent = `Showing ${rows.length} of ${rows.length} delivered orders`;
        }
    });

    applyLedgerFilter();

    /* Commission line chart — no external chart dependency. */
    const rawData = @json($chartPayload);
    const chartSvg = document.getElementById('commissionChartSvg');
    const chartWrap = document.getElementById('commissionChartWrap');
    const tooltip = document.getElementById('commissionChartTooltip');
    const tooltipDate = document.getElementById('commissionChartTooltipDate');
    const tooltipValue = document.getElementById('commissionChartTooltipValue');
    const rangeButtons = Array.from(document.querySelectorAll('[data-commission-range]'));

    if (!chartSvg || !chartWrap || !Array.isArray(rawData) || rawData.length === 0) return;

    const SVG_NS = 'http://www.w3.org/2000/svg';
    const width = 1000;
    const height = 236;
    const margin = { top: 18, right: 18, bottom: 34, left: 54 };
    const innerWidth = width - margin.left - margin.right;
    const innerHeight = height - margin.top - margin.bottom;
    const phpCurrency = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });
    const shortCurrency = value => `₱${Number(value || 0).toLocaleString('en-PH', { maximumFractionDigits: 0 })}`;

    function toLocalDate(dateText) {
        const [year, month, day] = String(dateText).split('-').map(Number);
        return new Date(year, (month || 1) - 1, day || 1, 12, 0, 0);
    }

    function dateKey(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function addDays(date, amount) {
        const next = new Date(date);
        next.setDate(next.getDate() + amount);
        return next;
    }

    function niceMaximum(value) {
        const max = Math.max(1, Number(value || 0));
        const magnitude = 10 ** Math.floor(Math.log10(max));
        const normalized = max / magnitude;
        let niceNormalized;

        if (normalized <= 1) niceNormalized = 1;
        else if (normalized <= 2) niceNormalized = 2;
        else if (normalized <= 5) niceNormalized = 5;
        else niceNormalized = 10;

        return niceNormalized * magnitude;
    }

    function buildSeries(days) {
        const grouped = rawData.reduce((map, item) => {
            const key = String(item.date || '');
            if (!key) return map;
            map.set(key, (map.get(key) || 0) + Number(item.commission || 0));
            return map;
        }, new Map());

        const today = new Date();
        today.setHours(12, 0, 0, 0);

        const latestDataDate = rawData.reduce((latest, item) => {
            const value = toLocalDate(item.date);
            return !latest || value > latest ? value : latest;
        }, null);

        const endDate = latestDataDate && latestDataDate > today ? latestDataDate : today;
        const startDate = addDays(endDate, -(days - 1));
        const series = [];

        for (let cursor = new Date(startDate); cursor <= endDate; cursor = addDays(cursor, 1)) {
            const key = dateKey(cursor);
            series.push({
                date: new Date(cursor),
                key,
                value: Number(grouped.get(key) || 0),
            });
        }

        return series;
    }

    function createSvgElement(tag, attributes = {}) {
        const el = document.createElementNS(SVG_NS, tag);
        Object.entries(attributes).forEach(([key, value]) => el.setAttribute(key, value));
        return el;
    }

    function smoothLinePath(points) {
        if (!points.length) return '';
        if (points.length === 1) return `M ${points[0].x} ${points[0].y}`;

        let path = `M ${points[0].x} ${points[0].y}`;

        for (let i = 0; i < points.length - 1; i += 1) {
            const p0 = points[i - 1] || points[i];
            const p1 = points[i];
            const p2 = points[i + 1];
            const p3 = points[i + 2] || p2;

            const cp1x = p1.x + (p2.x - p0.x) / 6;
            const cp1y = p1.y + (p2.y - p0.y) / 6;
            const cp2x = p2.x - (p3.x - p1.x) / 6;
            const cp2y = p2.y - (p3.y - p1.y) / 6;

            path += ` C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${p2.x} ${p2.y}`;
        }

        return path;
    }

    function renderChart(days) {
        const series = buildSeries(days);
        const maxValue = niceMaximum(Math.max(...series.map(item => item.value), 1));
        const xFor = index => margin.left + (series.length <= 1 ? 0 : (index / (series.length - 1)) * innerWidth);
        const yFor = value => margin.top + innerHeight - (Number(value || 0) / maxValue) * innerHeight;
        const points = series.map((item, index) => ({ ...item, x: xFor(index), y: yFor(item.value) }));
        const linePath = smoothLinePath(points);
        const areaPath = `${linePath} L ${points[points.length - 1].x} ${margin.top + innerHeight} L ${points[0].x} ${margin.top + innerHeight} Z`;

        chartSvg.replaceChildren();

        /* Grid + Y labels */
        for (let i = 0; i <= 4; i += 1) {
            const ratio = i / 4;
            const y = margin.top + innerHeight - ratio * innerHeight;
            const value = maxValue * ratio;

            chartSvg.appendChild(createSvgElement('line', {
                x1: margin.left,
                y1: y,
                x2: width - margin.right,
                y2: y,
                stroke: '#edf0f3',
                'stroke-width': '1',
                'stroke-dasharray': i === 0 ? '0' : '3 4',
            }));

            const label = createSvgElement('text', {
                x: margin.left - 12,
                y: y + 3,
                fill: '#98a2b3',
                'font-size': '9',
                'font-family': 'Poppins, sans-serif',
                'text-anchor': 'end',
            });
            label.textContent = shortCurrency(value);
            chartSvg.appendChild(label);
        }

        /* X labels */
        const labelCount = Math.min(6, series.length);
        const usedIndexes = new Set();
        for (let i = 0; i < labelCount; i += 1) {
            const index = labelCount === 1 ? 0 : Math.round((i / (labelCount - 1)) * (series.length - 1));
            if (usedIndexes.has(index)) continue;
            usedIndexes.add(index);

            const point = points[index];
            const label = createSvgElement('text', {
                x: point.x,
                y: height - 10,
                fill: '#98a2b3',
                'font-size': '9',
                'font-family': 'Poppins, sans-serif',
                'text-anchor': index === 0 ? 'start' : index === series.length - 1 ? 'end' : 'middle',
            });
            label.textContent = point.date.toLocaleDateString('en-PH', { month: 'short', day: 'numeric' });
            chartSvg.appendChild(label);
        }

        /* Area */
        chartSvg.appendChild(createSvgElement('path', {
            d: areaPath,
            fill: '#d99a00',
            'fill-opacity': '.08',
            stroke: 'none',
        }));

        /* Line */
        chartSvg.appendChild(createSvgElement('path', {
            d: linePath,
            fill: 'none',
            stroke: '#d99a00',
            'stroke-width': '2.4',
            'stroke-linecap': 'round',
            'stroke-linejoin': 'round',
        }));

        /* Visible points only where there is commission data. */
        points.filter(point => point.value > 0).forEach(point => {
            chartSvg.appendChild(createSvgElement('circle', {
                cx: point.x,
                cy: point.y,
                r: '3.8',
                fill: '#ffffff',
                stroke: '#d99a00',
                'stroke-width': '2.2',
            }));
        });

        /* Hover state */
        const hoverLine = createSvgElement('line', {
            x1: margin.left,
            y1: margin.top,
            x2: margin.left,
            y2: margin.top + innerHeight,
            stroke: '#d99a00',
            'stroke-width': '1',
            'stroke-dasharray': '3 4',
            opacity: '0',
        });
        const hoverDot = createSvgElement('circle', {
            cx: margin.left,
            cy: margin.top + innerHeight,
            r: '5',
            fill: '#ffffff',
            stroke: '#d99a00',
            'stroke-width': '2.5',
            opacity: '0',
        });
        const hoverTarget = createSvgElement('rect', {
            x: margin.left,
            y: margin.top,
            width: innerWidth,
            height: innerHeight,
            fill: 'transparent',
            style: 'cursor:crosshair',
        });

        chartSvg.appendChild(hoverLine);
        chartSvg.appendChild(hoverDot);
        chartSvg.appendChild(hoverTarget);

        const showPoint = point => {
            hoverLine.setAttribute('x1', point.x);
            hoverLine.setAttribute('x2', point.x);
            hoverLine.setAttribute('opacity', '1');
            hoverDot.setAttribute('cx', point.x);
            hoverDot.setAttribute('cy', point.y);
            hoverDot.setAttribute('opacity', '1');

            if (tooltip && tooltipDate && tooltipValue) {
                tooltipDate.textContent = point.date.toLocaleDateString('en-PH', {
                    month: 'short', day: 'numeric', year: 'numeric'
                });
                tooltipValue.textContent = phpCurrency.format(point.value);
                tooltip.style.left = `${(point.x / width) * 100}%`;
                tooltip.style.top = `${(point.y / height) * 100}%`;
                tooltip.classList.add('is-visible');
                tooltip.setAttribute('aria-hidden', 'false');
            }
        };

        hoverTarget.addEventListener('mousemove', event => {
            const rect = chartSvg.getBoundingClientRect();
            const relativeX = ((event.clientX - rect.left) / rect.width) * width;
            const normalized = Math.max(0, Math.min(1, (relativeX - margin.left) / innerWidth));
            const index = Math.round(normalized * (points.length - 1));
            showPoint(points[index]);
        });

        hoverTarget.addEventListener('mouseleave', () => {
            hoverLine.setAttribute('opacity', '0');
            hoverDot.setAttribute('opacity', '0');
            tooltip?.classList.remove('is-visible');
            tooltip?.setAttribute('aria-hidden', 'true');
        });

        /* On first render, highlight the latest non-zero point for a polished default state. */
        const latestNonZero = [...points].reverse().find(point => point.value > 0);
        if (latestNonZero) showPoint(latestNonZero);
    }

    rangeButtons.forEach(button => {
        button.addEventListener('click', () => {
            rangeButtons.forEach(item => item.classList.toggle('is-active', item === button));
            renderChart(Number(button.dataset.commissionRange || 30));
        });
    });

    renderChart(30);
})();
</script>
@endsection
