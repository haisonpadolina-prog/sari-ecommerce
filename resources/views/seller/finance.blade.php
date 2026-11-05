@extends('layouts.seller')

@section('title', 'Finance & Earnings — SARI Seller')

@section('page-title', 'Finance & Earnings')

@push('styles')
<style id="sariSellerEmbeddedCss-finance" data-sari-inline-css="finance">
@verbatim
/* Seller Finance & Earnings — unified SARI clean dashboard. Scoped to this view. */
.seller-finance-page {
    --finance-gold: #C88912;
    --finance-gold-hover: #B47B0E;
    --finance-ink: #2F3742;
    --finance-text: #475569;
    --finance-muted: #758297;
    --finance-soft-muted: #94A3B8;
    --finance-line: #E5E7EB;
    --finance-soft: #F8FAFC;
    width: 100%;
    max-width: 1520px;
    margin-inline: auto;
    padding-bottom: 28px;
    color: var(--finance-ink);
    font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
}
.seller-finance-page *,
.seller-finance-page *::before,
.seller-finance-page *::after { box-sizing: border-box; }
.seller-finance-page :is(button, input, select, a) { font-family: inherit; }

/* Page heading */
.seller-finance-header {
    min-height: 82px;
    border-bottom: 1px solid #E6EAF0;
    padding: 2px 2px 17px;
}
.seller-finance-eyebrow {
    margin: 0;
    color: #A87316;
    font-size: 10.5px;
    font-weight: 800;
    line-height: 1.3;
    letter-spacing: .12em;
    text-transform: uppercase;
}
.seller-finance-title {
    margin: 6px 0 0;
    color: #2F3742;
    font-size: clamp(27px, 2.2vw, 31px);
    font-weight: 760;
    letter-spacing: -.04em;
    line-height: 1.16;
}
.seller-finance-title span:last-child { margin-left: .18em; color: var(--finance-gold); }
.seller-finance-subtitle {
    max-width: 920px;
    margin: 8px 0 0;
    color: #738094;
    font-size: 11.5px;
    font-weight: 450;
    line-height: 1.65;
}

/* Financial KPIs — open SVG icons rather than colored icon containers */
.seller-finance-summary {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
    margin-top: 22px;
}
.seller-finance-stat {
    min-width: 0;
    min-height: 110px;
    border: 1px solid #E5E7EB;
    border-radius: 18px;
    background: #FFF;
    padding: 17px 18px;
    box-shadow: 0 2px 6px rgba(15,23,42,.035), 0 10px 24px rgba(15,23,42,.07);
}
.seller-finance-stat-inner {
    display: flex;
    height: 100%;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}
.seller-finance-stat-copy { flex: 1 1 auto; min-width: 0; }
.seller-finance-stat-label {
    color: #5A6879;
    font-size: 12.5px;
    font-weight: 650;
    line-height: 1.42;
}
.seller-finance-stat-value {
    margin-top: 8px;
    color: #273442;
    font-size: clamp(18px, 1.55vw, 24px);
    font-weight: 750;
    letter-spacing: -.035em;
    line-height: 1.18;
    font-variant-numeric: tabular-nums;
    white-space: normal;
    overflow-wrap: anywhere;
}
.seller-finance-stat-note {
    margin-top: 7px;
    color: #8793A3;
    font-size: 10.5px;
    line-height: 1.45;
}
.seller-finance-stat-icon,
.seller-finance-ledger-icon {
    display: grid;
    width: 27px;
    height: 27px;
    flex: 0 0 27px;
    place-items: center;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}
.seller-finance-stat-icon svg { width: 27px; height: 27px; }
.seller-finance-stat-icon--gross { color: #94A3B8; }
.seller-finance-stat-icon--commission { color: #C88912; }
.seller-finance-stat-icon--net { color: #059669; }
.seller-finance-stat-icon--pending { color: #C88912; }
.seller-finance-stat-icon--paid { color: #059669; }

/* Settlement ledger card and hierarchy */
.seller-finance-ledger {
    margin-top: 22px;
    overflow: hidden;
    border: 1px solid #E5E7EB;
    border-radius: 18px;
    background: #FFF;
    box-shadow: 0 2px 6px rgba(15,23,42,.035), 0 12px 28px rgba(15,23,42,.085);
}
.seller-finance-ledger-head {
    display: flex;
    min-height: 92px;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    border-bottom: 1px solid #E9EDF2;
    padding: 19px 24px;
    background: #FFF;
}
.seller-finance-ledger-title-group {
    display: flex;
    min-width: 0;
    align-items: flex-start;
    gap: 12px;
}
.seller-finance-ledger-icon { margin-top: 1px; color: #C88912; }
.seller-finance-ledger-icon svg { width: 23px; height: 23px; }
.seller-finance-ledger-title {
    margin: 0;
    color: #263443;
    font-size: 18px;
    font-weight: 750;
    line-height: 1.3;
    letter-spacing: -.025em;
}
.seller-finance-ledger-subtitle {
    margin: 5px 0 0;
    color: #8793A3;
    font-size: 11.5px;
    line-height: 1.5;
}
.seller-finance-ledger-count {
    display: inline-flex;
    min-height: 30px;
    flex: 0 0 auto;
    align-items: center;
    border: 1px solid #E2E8F0;
    border-radius: 999px;
    background: #F8FAFC;
    padding: 0 12px;
    color: #64748B;
    font-size: 11px;
    font-weight: 650;
    white-space: nowrap;
}

/* Table keeps its seven financial fields and original order */
.seller-finance-table-wrap {
    width: 100%;
    overflow-x: auto;
    background: #FFF;
    scrollbar-width: thin;
    scrollbar-color: #D8DEE6 transparent;
}
.seller-finance-table {
    width: 100%;
    min-width: 1000px;
    border-collapse: collapse;
    text-align: left;
}
.seller-finance-table thead { background: #F8FAFC; }
.seller-finance-table th {
    height: 50px;
    border-bottom: 1px solid #E5EAF0;
    padding: 0 16px;
    color: #75849A;
    font-size: 10.5px;
    font-weight: 750;
    letter-spacing: .065em;
    text-transform: uppercase;
    white-space: nowrap;
}
.seller-finance-table td {
    min-height: 62px;
    border-bottom: 1px solid #EDF0F4;
    padding: 18px 16px;
    color: #546273;
    font-size: 12px;
    font-weight: 500;
    line-height: 1.5;
    white-space: nowrap;
}
.seller-finance-table th:first-child,
.seller-finance-table td:first-child { padding-left: 24px; }
.seller-finance-table th:last-child,
.seller-finance-table td:last-child { padding-right: 24px; }
.seller-finance-table tbody tr:last-child td { border-bottom: 0; }
.seller-finance-table tbody tr { transition: background-color .14s ease; }
.seller-finance-table tbody tr:hover { background: #F8FAFC; }
.seller-finance-order { color: #293746 !important; font-size: 12.5px !important; font-weight: 750 !important; }
.seller-finance-money {
    color: #344354 !important;
    font-size: 12.5px !important;
    font-variant-numeric: tabular-nums;
}
.seller-finance-money--muted { color: #667589 !important; }
.seller-finance-money--net { color: #13804B !important; font-weight: 750 !important; }
.seller-finance-status {
    display: inline-flex;
    min-height: 28px;
    align-items: center;
    gap: 7px;
    border: 1px solid;
    border-radius: 999px;
    padding: 0 11px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: capitalize;
    white-space: nowrap;
}
.seller-finance-status::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}
.seller-finance-status--paid,
.seller-finance-status--released,
.seller-finance-status--completed {
    border-color: #BBF7D0;
    background: #ECFDF3;
    color: #166534;
}
.seller-finance-status--pending,
.seller-finance-status--eligible {
    border-color: #E2E8F0;
    background: #F8FAFC;
    color: #A56D0A;
}
.seller-finance-status--default {
    border-color: #E2E8F0;
    background: #F8FAFC;
    color: #64748B;
}

/* Empty ledger should read clearly inside the original table */
.seller-finance-table td.seller-finance-empty {
    height: 228px;
    padding: 30px 18px !important;
    text-align: center;
    white-space: normal;
}
.seller-finance-empty-content {
    display: grid;
    justify-items: center;
    gap: 7px;
    color: #94A3B8;
}
.seller-finance-empty-content svg { width: 54px; height: 54px; margin-bottom: 4px; color: #94A3B8; }
.seller-finance-empty-content strong { color: #334155; font-size: 17px; font-weight: 750; }
.seller-finance-empty-content span { color: #8793A3; font-size: 11.5px; line-height: 1.55; }

/* Paginator stays inside the ledger card */
.seller-finance-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    min-height: 74px;
    border-top: 1px solid #E9EDF2;
    background: #FFF;
    padding: 15px 22px;
}
.seller-finance-page-count { margin: 0; color: #718096; font-size: 11.5px; font-weight: 500; }
.seller-finance-pagination nav { margin-left: auto; max-width: 100%; }
.seller-finance-pagination nav :is(a, span) { font-size: 11.5px; }
.seller-finance-pagination nav a:focus-visible { outline: 2px solid var(--finance-gold); outline-offset: 2px; }
.seller-finance-pagination nav a:hover { color: #A56D0A; background: #F8FAFC; }
.seller-finance-pagination nav [aria-current='page'] > span {
    border-color: #C88912 !important;
    background: #C88912 !important;
    color: #FFF !important;
    font-weight: 750;
}

/* Success/error messages */
.seller-finance-alert {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 14px;
    border: 1px solid;
    border-radius: 14px;
    padding: 14px 16px;
    font-size: 12px;
    line-height: 1.5;
}
.seller-finance-alert--success { border-color: #BBF7D0; background: #F0FDF4; color: #166534; }
.seller-finance-alert--error { border-color: #FECACA; background: #FEF2F2; color: #B91C1C; }
.seller-finance-alert-icon {
    display: grid;
    width: 22px;
    height: 22px;
    flex: 0 0 22px;
    place-items: center;
    color: inherit;
}
.seller-finance-alert-icon svg { width: 20px; height: 20px; }

@media (max-width: 1350px) {
    .seller-finance-summary { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .seller-finance-stat-value { font-size: 23px; }
}
@media (max-width: 900px) {
    .seller-finance-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .seller-finance-ledger-head { padding: 18px; }
    .seller-finance-table { min-width: 980px; }
}
@media (max-width: 640px) {
    .seller-finance-page { padding-bottom: 20px; }
    .seller-finance-header { min-height: unset; padding-bottom: 15px; }
    .seller-finance-eyebrow { font-size: 9.5px; }
    .seller-finance-title { font-size: 25px; }
    .seller-finance-subtitle { font-size: 11px; }
    .seller-finance-summary { gap: 10px; margin-top: 16px; }
    .seller-finance-stat { min-height: 110px; padding: 14px 13px; border-radius: 16px; }
    .seller-finance-stat-inner { gap: 5px; }
    .seller-finance-stat-label { font-size: 11.5px; }
    .seller-finance-stat-value { font-size: 18px; }
    .seller-finance-stat-note { font-size: 9.5px; }
    .seller-finance-stat-icon,
    .seller-finance-stat-icon svg { width: 22px; height: 22px; }
    .seller-finance-stat-icon { flex-basis: 22px; }
    .seller-finance-ledger { margin-top: 16px; border-radius: 16px; }
    .seller-finance-ledger-head { gap: 9px; align-items: flex-start; padding: 17px 15px; }
    .seller-finance-ledger-title-group { gap: 8px; }
    .seller-finance-ledger-icon { width: 22px; height: 22px; flex-basis: 22px; }
    .seller-finance-ledger-icon svg { width: 20px; height: 20px; }
    .seller-finance-ledger-title { font-size: 16px; }
    .seller-finance-ledger-subtitle { font-size: 10.5px; }
    .seller-finance-ledger-count { min-height: 25px; padding: 0 8px; font-size: 9.5px; }
    .seller-finance-pagination { padding: 14px; min-height: 66px; }
    .seller-finance-pagination nav { margin-left: 0; }
}
@media (max-width: 360px) {
    .seller-finance-stat { padding: 12px 10px; }
    .seller-finance-stat-value { font-size: 16px; }
}

/* Selectable ledger rows; gold inputs with an explicitly white checkmark. */
.seller-finance-ledger .seller-finance-checkbox-cell {
    width: 52px;
    min-width: 52px;
    padding: 0 8px 0 23px !important;
    vertical-align: middle;
}
.seller-finance-checkbox {
    -webkit-appearance: none;
    appearance: none;
    display: inline-grid;
    position: relative;
    width: 18px;
    height: 18px;
    margin: 0;
    border: 1.5px solid #CBD5E1;
    border-radius: 5px;
    background: #FFFFFF;
    place-content: center;
    vertical-align: middle;
    cursor: pointer;
    transition: background-color .12s ease, border-color .12s ease;
}
.seller-finance-checkbox:hover { border-color: #C88912; }
.seller-finance-checkbox:checked,
.seller-finance-checkbox:indeterminate { background: #C88912; border-color: #C88912; }
.seller-finance-checkbox:checked::after {
    content: '';
    position: absolute;
    left: 5px;
    top: 2px;
    width: 5px;
    height: 9px;
    border-right: 2px solid #FFFFFF;
    border-bottom: 2px solid #FFFFFF;
    transform: rotate(45deg);
}
.seller-finance-checkbox:indeterminate::after {
    content: '';
    position: absolute;
    top: 7px;
    left: 4px;
    width: 8px;
    height: 2px;
    border-radius: 1px;
    background: #FFFFFF;
}
.seller-finance-checkbox:focus-visible { outline: 3px solid rgba(200,137,18,.28); outline-offset: 3px; }
.seller-finance-table .seller-finance-ledger-row:has(.seller-finance-checkbox:checked) { background: #FAFBFC; }
.seller-finance-selection-bar[hidden],
.seller-finance-selection-error[hidden] { display: none !important; }
.seller-finance-selection-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 12px 24px;
    border-bottom: 1px solid #E9EDF2;
    background: #F8FAFC;
}
.seller-finance-selected-label { display: inline-flex; align-items: center; gap: 8px; color: #334155; font-size: 11.5px; }
.seller-finance-selected-label strong { font-weight: 750; }
.seller-finance-selected-bullet { display: inline-block; width: 7px; height: 7px; border-radius: 999px; background: #C88912; }
.seller-finance-selected-hint { color: #8290A1; font-size: 10.5px; }
.seller-finance-selection-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.seller-finance-print-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 38px;
    padding: 0 14px;
    border: 1px solid #C88912;
    border-radius: 10px;
    background: #C88912;
    color: #FFF;
    font-size: 11px;
    font-weight: 750;
    cursor: pointer;
    transition: background-color .14s ease;
}
.seller-finance-print-btn:hover { background: #B47B0E; }
.seller-finance-print-btn svg { width: 16px; height: 16px; }
.seller-finance-clear-btn {
    min-height: 38px;
    padding: 0 8px;
    border: 0;
    border-radius: 9px;
    background: transparent;
    color: #64748B;
    font-size: 11px;
    font-weight: 650;
    cursor: pointer;
}
.seller-finance-clear-btn:hover { color: #A56D0A; background: #FFF; }
.seller-finance-print-btn:focus-visible,
.seller-finance-clear-btn:focus-visible { outline: 3px solid rgba(200,137,18,.24); outline-offset: 2px; }
.seller-finance-selection-error { margin: 0; padding: 10px 24px; color: #B91C1C; background: #FEF2F2; font-size: 11.5px; }
.seller-finance-table { min-width: 1080px; }
@media (max-width: 640px) {
    .seller-finance-selection-bar { align-items: stretch; padding: 12px 15px; }
    .seller-finance-selection-actions { width: 100%; }
    .seller-finance-print-btn { flex: 1; }
    .seller-finance-selection-error { padding: 10px 15px; }
}

@media (prefers-reduced-motion: reduce) {
    .seller-finance-page * { transition-duration: .01ms !important; scroll-behavior: auto !important; }
}
@endverbatim
</style>



@endpush

@section('content')

@php

    $summaryCards = [

        [

            'label' => 'Gross Sales',

            'value' => $stats['gross'],

            'note' => 'Eligible merchandise sales',

            'icon' => 'gross',

        ],

        [

            'label' => 'Platform Commission',

            'value' => $stats['commission'],

            'note' => 'Platform commission recorded',

            'icon' => 'commission',

        ],

        [

            'label' => 'Net Earnings',

            'value' => $stats['net'],

            'note' => 'Seller net after deductions',

            'icon' => 'net',

        ],

        [

            'label' => 'Pending Settlement',

            'value' => $stats['pending'],

            'note' => 'Not yet released in ledger',

            'icon' => 'pending',

        ],

        [

            'label' => 'Paid / Released',

            'value' => $stats['paid'],

            'note' => 'Marked released internally',

            'icon' => 'paid',

        ],

    ];

    $statusClass = fn (?string $value) => match (strtolower((string) $value)) {

        'paid', 'released', 'completed' => 'seller-finance-status--paid',

        'pending', 'eligible' => 'seller-finance-status--pending',

        default => 'seller-finance-status--default',

    };

@endphp

<div class="seller-finance-page">

    @if(session('success'))

        <div class="seller-finance-alert seller-finance-alert--success">

            <span class="seller-finance-alert-icon" aria-hidden="true">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">

                    <circle cx="12" cy="12" r="8"></circle>

                    <path d="m8 12 2.5 2.5L16 9"></path>

                </svg>

            </span>

            <div>{{ session('success') }}</div>

        </div>

    @endif

    @if($errors->any())

        <div class="seller-finance-alert seller-finance-alert--error">

            <span class="seller-finance-alert-icon" aria-hidden="true">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">

                    <path d="M12 3 3 20h18L12 3Z"></path>

                    <path d="M12 9v5"></path>

                    <path d="M12 17h.01"></path>

                </svg>

            </span>

            <div>{{ $errors->first() }}</div>

        </div>

    @endif

    <header class="seller-finance-header">

        <p class="seller-finance-eyebrow">Business Finance</p>

        <h1 class="seller-finance-title">

            <span>Finance</span><span>& Earnings</span>

        </h1>

        <p class="seller-finance-subtitle">

            Delivered-and-paid orders only. Settlements are internal ledger records, not proof of an external bank transfer.

        </p>

    </header>

    <section class="seller-finance-summary" aria-label="Finance summary">

        @foreach($summaryCards as $card)

            <article class="seller-finance-stat">

                <div class="seller-finance-stat-inner">

                    <div class="seller-finance-stat-copy">

                        <div class="seller-finance-stat-label">{{ $card['label'] }}</div>

                        <div class="seller-finance-stat-value">

                            ₱{{ number_format((float) $card['value'], 2) }}

                        </div>

                        <div class="seller-finance-stat-note">{{ $card['note'] }}</div>

                    </div>

                    <span class="seller-finance-stat-icon seller-finance-stat-icon--{{ $card['icon'] }}" aria-hidden="true">

                        @if($card['icon'] === 'gross')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <path d="M4 18V9"></path>

                                <path d="M10 18V5"></path>

                                <path d="M16 18v-7"></path>

                                <path d="M3 19h18"></path>

                            </svg>

                        @elseif($card['icon'] === 'commission')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <circle cx="8" cy="8" r="2"></circle>

                                <circle cx="16" cy="16" r="2"></circle>

                                <path d="M7 17 17 7"></path>

                            </svg>

                        @elseif($card['icon'] === 'net')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <path d="M4 12h16"></path>

                                <path d="m14 6 6 6-6 6"></path>

                                <path d="M4 7v10"></path>

                            </svg>

                        @elseif($card['icon'] === 'pending')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <circle cx="12" cy="12" r="8"></circle>

                                <path d="M12 8v4l3 2"></path>

                            </svg>

                        @else

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <circle cx="12" cy="12" r="8"></circle>

                                <path d="m8 12 2.5 2.5L16 9"></path>

                            </svg>

                        @endif

                    </span>

                </div>

            </article>

        @endforeach

    </section>

    <section class="mt-6 space-y-4" aria-label="Seller earnings wallet">
        <header class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-[#273442]">Seller Earnings Wallet</h2>
                <p class="mt-1 text-[11px] text-[#738094]">Available only after Main Branch reconciliation and SARI Admin confirmation of the full physical COD cash turnover. A wallet credit is not an external payment.</p>
            </div>
            <div class="flex flex-wrap gap-2 text-[11px]">
                <span class="rounded-xl border border-[#e5e7eb] bg-white px-4 py-2 text-[#475569]">Reserved: <strong>₱{{ number_format((float) $wallet->reserved_balance, 2) }}</strong></span>
                <span class="rounded-xl border border-[#e5e7eb] bg-white px-4 py-2 text-[#475569]">Externally paid (recorded): <strong>₱{{ number_format((float) $wallet->lifetime_payouts, 2) }}</strong></span>
            </div>
        </header>

        @include('finance.payout-form', [
            'payoutAction' => route('seller.finance.payout'),
            'wallet' => $wallet,
            'payoutTitle' => 'Withdraw Seller Earnings',
        ])

        <div class="grid gap-4 xl:grid-cols-2">
            <article class="rounded-2xl border border-[#e5e7eb] bg-white p-5 shadow-sm">
                <h3 class="text-[13px] font-bold text-[#273442]">Wallet Activity</h3>
                <p class="mt-1 text-[10px] text-[#738094]">Latest credits, reservations and completed payouts.</p>
                <div class="mt-4 space-y-2">
                    @forelse($walletEntries as $entry)
                        <div class="flex items-center justify-between gap-4 rounded-xl border border-[#edf0f3] px-3 py-3 text-[11px]">
                            <div>
                                <strong class="block text-[#273442]">{{ ucwords(str_replace('_', ' ', $entry->category)) }}</strong>
                                <span class="mt-1 block text-[#738094]">{{ $entry->occurred_at?->format('M d, Y h:i A') }}</span>
                            </div>
                            <div class="text-right text-[#273442]">
                                <strong class="block">{{ (float) $entry->available_delta >= 0 ? '+' : '−' }}₱{{ number_format(abs((float) $entry->available_delta), 2) }}</strong>
                                @if((float) $entry->reserved_delta != 0)
                                    <span class="block text-[9px] text-[#738094]">Reserved {{ (float) $entry->reserved_delta > 0 ? '+' : '−' }}₱{{ number_format(abs((float) $entry->reserved_delta), 2) }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="rounded-xl bg-[#f8fafc] p-4 text-[11px] text-[#738094]">No wallet movements yet. COD cash must be reconciled at Main Branch and physically received by SARI Admin first.</p>
                    @endforelse
                </div>
            </article>
            <article class="rounded-2xl border border-[#e5e7eb] bg-white p-5 shadow-sm">
                <h3 class="text-[13px] font-bold text-[#273442]">Recent Withdrawal Requests</h3>
                <p class="mt-1 text-[10px] text-[#738094]">An Admin confirms payment only after funds are physically released or transferred.</p>
                <div class="mt-4 space-y-2">
                    @forelse($payoutRequests as $payout)
                        <div class="flex items-center justify-between gap-4 rounded-xl border border-[#edf0f3] px-3 py-3 text-[11px]">
                            <div>
                                <strong class="block text-[#273442]">{{ $payout->request_code }}</strong>
                                <span class="mt-1 block text-[#738094]">{{ $payout->requested_at?->format('M d, Y') }} · {{ strtoupper($payout->status) }}</span>
                            </div>
                            <strong class="text-[#273442]">₱{{ number_format((float) $payout->amount, 2) }}</strong>
                        </div>
                    @empty
                        <p class="rounded-xl bg-[#f8fafc] p-4 text-[11px] text-[#738094]">No withdrawal requests yet.</p>
                    @endforelse
                </div>
            </article>
        </div>
    </section>

    <section class="seller-finance-ledger">

        <div class="seller-finance-ledger-head">

            <div class="seller-finance-ledger-title-group">

                <span class="seller-finance-ledger-icon" aria-hidden="true">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                        <path d="M5 4h14v16H5z"></path>

                        <path d="M8 8h8"></path>

                        <path d="M8 12h8"></path>

                        <path d="M8 16h5"></path>

                    </svg>

                </span>

                <div>

                    <h2 class="seller-finance-ledger-title">Settlement Ledger</h2>

                    <p class="seller-finance-ledger-subtitle">

                        Internal settlement records for eligible Seller orders.

                    </p>

                </div>

            </div>


        </div>

        <div class="seller-finance-selection-bar" data-finance-selection-bar hidden>
            <div class="seller-finance-selected-label" aria-live="polite">
                <span class="seller-finance-selected-bullet" aria-hidden="true"></span>
                <strong data-finance-selected-count>0 selected</strong>
                <span class="seller-finance-selected-hint">Across all pages</span>
            </div>
            <div class="seller-finance-selection-actions">
                <button type="button" class="seller-finance-print-btn" data-finance-print-selected>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="M7 8V4h10v4M7 18h10v3H7z"></path><rect x="4" y="8" width="16" height="10" rx="2"></rect><path d="M16 12h2"></path></svg>
                    Print Selected Earnings
                </button>
                <button type="button" class="seller-finance-clear-btn" data-finance-clear-selected>Clear selection</button>
            </div>
        </div>
        <p class="seller-finance-selection-error" data-finance-selection-error role="alert" hidden></p>

        <div class="seller-finance-table-wrap">

            <table class="seller-finance-table">

                <thead>

                    <tr>
                        <th class="seller-finance-checkbox-cell">
                            <input type="checkbox" class="seller-finance-checkbox" data-finance-select-page aria-label="Select all settlements on this page">
                        </th>

                        <th>Order</th>

                        <th>Eligible</th>

                        <th>Gross</th>

                        <th>Commission</th>

                        <th>Withholding</th>

                        <th>Net</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($settlements as $row)

                        <tr class="seller-finance-ledger-row"
                            data-finance-settlement-id="{{ $row->getKey() }}"
                            data-finance-order="{{ $row->order?->order_number ?: 'Order unavailable' }}"
                            data-finance-eligible="{{ $row->eligible_at?->format('M d, Y') ?: '—' }}"
                            data-finance-gross="{{ (float) $row->merchandise_amount }}"
                            data-finance-commission="{{ (float) $row->platform_commission_amount }}"
                            data-finance-withholding="{{ (float) $row->withholding_tax_amount }}"
                            data-finance-net="{{ (float) $row->seller_net_amount }}"
                            data-finance-status="{{ ucfirst((string) $row->status) }}">
                            <td class="seller-finance-checkbox-cell">
                                <input type="checkbox" class="seller-finance-checkbox" data-finance-select-row aria-label="Select settlement for order {{ $row->order?->order_number ?: 'unavailable' }}">
                            </td>

                            <td class="seller-finance-order">

                                {{ $row->order?->order_number ?: 'Order unavailable' }}

                            </td>

                            <td>

                                {{ $row->eligible_at?->format('M d, Y') ?: '—' }}

                            </td>

                            <td class="seller-finance-money">

                                ₱{{ number_format((float) $row->merchandise_amount, 2) }}

                            </td>

                            <td class="seller-finance-money seller-finance-money--muted">

                                ₱{{ number_format((float) $row->platform_commission_amount, 2) }}

                            </td>

                            <td class="seller-finance-money seller-finance-money--muted">

                                ₱{{ number_format((float) $row->withholding_tax_amount, 2) }}

                            </td>

                            <td class="seller-finance-money seller-finance-money--net">

                                ₱{{ number_format((float) $row->seller_net_amount, 2) }}

                            </td>

                            <td>

                                <span class="seller-finance-status {{ $statusClass($row->status) }}">

                                    {{ ucfirst($row->status) }}

                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8" class="seller-finance-empty">
                                <div class="seller-finance-empty-content">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                                        <rect x="4" y="3.5" width="16" height="17" rx="2"></rect>
                                        <path d="M8 8h8M8 12h8M8 16h5"></path>
                                    </svg>
                                    <strong>No settlements yet</strong>
                                    <span>Eligible settlement records will appear here once available.</span>
                                </div>
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="seller-finance-pagination">
            <p class="seller-finance-page-count">
                Showing {{ $settlements->firstItem() ?? 0 }}–{{ $settlements->lastItem() ?? 0 }} of {{ number_format($settlements->total()) }} settlements
            </p>
            @if($settlements->hasPages())
                {{ $settlements->links() }}
            @endif
        </div>

    </section>

</div>

@endsection

@push('scripts')
<script>
(() => {
    window.__SARI_SELLER_FINANCE_SELECTION__?.abort();
    const lifecycle = new AbortController();
    window.__SARI_SELLER_FINANCE_SELECTION__ = lifecycle;
    const { signal } = lifecycle;

    const root = document.querySelector('.seller-finance-page');
    if (!root) return;
    const selectPage = root.querySelector('[data-finance-select-page]');
    const rowCheckboxes = [...root.querySelectorAll('[data-finance-select-row]')];
    const toolbar = root.querySelector('[data-finance-selection-bar]');
    const selectedCount = root.querySelector('[data-finance-selected-count]');
    const clearButton = root.querySelector('[data-finance-clear-selected]');
    const printButton = root.querySelector('[data-finance-print-selected]');
    const errorElement = root.querySelector('[data-finance-selection-error]');
    const storageKey = 'sari-seller-finance-selection-v1:' + @json((string) ($seller->id ?? 'seller'));
    const selected = new Map();

    const showError = (message = '') => {
        if (!errorElement) return;
        errorElement.textContent = message;
        errorElement.hidden = !message;
    };
    const validRecord = (record) =>
        record && typeof record === 'object' &&
        /^\d+$/.test(String(record.id || '')) &&
        ['order','eligible','status'].every((key) => typeof record[key] === 'string') &&
        ['gross','commission','withholding','net'].every((key) => Number.isFinite(Number(record[key])));

    try {
        const saved = JSON.parse(sessionStorage.getItem(storageKey) || '[]');
        if (Array.isArray(saved)) saved.forEach((record) => {
            if (validRecord(record) && selected.size < 300) selected.set(String(record.id), record);
        });
    } catch (_) { /* Storage can be disabled; current-page selection still works. */ }

    const save = () => {
        try {
            if (selected.size) sessionStorage.setItem(storageKey, JSON.stringify([...selected.values()]));
            else sessionStorage.removeItem(storageKey);
        } catch (_) { /* Selection for the current page remains available. */ }
    };
    const recordFor = (checkbox) => {
        const data = checkbox.closest('[data-finance-settlement-id]')?.dataset;
        if (!data) return null;
        return {
            id: String(data.financeSettlementId || ''),
            order: String(data.financeOrder || ''),
            eligible: String(data.financeEligible || ''),
            gross: Number(data.financeGross),
            commission: Number(data.financeCommission),
            withholding: Number(data.financeWithholding),
            net: Number(data.financeNet),
            status: String(data.financeStatus || '')
        };
    };
    const sync = () => {
        rowCheckboxes.forEach((checkbox) => {
            const id = checkbox.closest('[data-finance-settlement-id]')?.dataset.financeSettlementId;
            checkbox.checked = Boolean(id && selected.has(String(id)));
        });
        const checked = rowCheckboxes.filter((checkbox) => checkbox.checked).length;
        if (selectPage) {
            selectPage.checked = rowCheckboxes.length > 0 && checked === rowCheckboxes.length;
            selectPage.indeterminate = checked > 0 && checked < rowCheckboxes.length;
            selectPage.disabled = rowCheckboxes.length === 0;
        }
        if (toolbar) toolbar.hidden = selected.size === 0;
        if (selectedCount) selectedCount.textContent = `${selected.size} selected`;
        if (printButton) printButton.disabled = selected.size === 0;
    };
    const changeRow = (checkbox) => {
        const record = recordFor(checkbox);
        if (!validRecord(record)) return;
        if (checkbox.checked) selected.set(record.id, record);
        else selected.delete(record.id);
        save(); sync(); showError();
    };

    rowCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => changeRow(checkbox), { signal });
    });
    selectPage?.addEventListener('change', () => {
        rowCheckboxes.forEach((checkbox) => {
            const record = recordFor(checkbox);
            if (!validRecord(record)) return;
            if (selectPage.checked) selected.set(record.id, record);
            else selected.delete(record.id);
        });
        save(); sync(); showError();
    }, { signal });
    clearButton?.addEventListener('click', () => {
        selected.clear(); save(); sync(); showError();
    }, { signal });

    const safe = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
        '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'
    })[character]);
    const peso = (value) => `₱${Number(value || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2, maximumFractionDigits: 2
    })}`;
    const renderReport = (records) => {
        const sum = (key) => records.reduce((total, record) => total + Number(record[key] || 0), 0);
        const generated = new Intl.DateTimeFormat('en-PH', {
            dateStyle: 'medium', timeStyle: 'short', timeZone: 'Asia/Manila'
        }).format(new Date());
        const rows = records.map((record) => `<tr>
            <td>${safe(record.order)}</td><td>${safe(record.eligible)}</td>
            <td class="amount">${peso(record.gross)}</td>
            <td class="amount">${peso(record.commission)}</td>
            <td class="amount">${peso(record.withholding)}</td>
            <td class="amount net">${peso(record.net)}</td>
            <td>${safe(record.status)}</td>
        </tr>`).join('');
        return `<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
            <title>SARI Selected Earnings</title><style>
            @page { size: A4 landscape; margin: 13mm; }
            * { box-sizing: border-box; }
            body { margin: 0; color: #263443; font-family: Arial, sans-serif; font-size: 10px; }
            .top { display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:15px; border-bottom:2px solid #C88912; }
            .brand { color:#C88912; font-weight:800; font-size:12px; letter-spacing:.13em; }
            h1 { margin:8px 0 7px; font-size:23px; letter-spacing:-.025em; }
            .sub { color:#64748B; font-size:10px; line-height:1.5; }
            .meta { color:#475569; text-align:right; line-height:1.8; }
            .meta strong { display:block; font-size:15px; color:#293746; }
            table { width:100%; border-collapse:collapse; margin-top:18px; table-layout:auto; }
            th { padding:11px 8px; background:#F8FAFC; color:#596779; text-align:left; font-size:9px; text-transform:uppercase; border-bottom:1px solid #CBD5E1; }
            td { padding:11px 8px; border-bottom:1px solid #E5E7EB; line-height:1.5; white-space:nowrap; }
            td:first-child { font-weight:700; }
            td.amount { text-align:right; font-variant-numeric:tabular-nums; }
            td.net { color:#13804B; font-weight:700; }
            tfoot td { border-top:2px solid #D8DFE8; border-bottom:0; background:#F8FAFC; font-weight:700; }
            .foot { margin-top:20px; border-top:1px solid #E5E7EB; padding-top:12px; color:#64748B; font-size:9px; }
            .print-action { margin-top:18px; border:0; background:#C88912; color:white; padding:10px 16px; border-radius:8px; font-size:12px; cursor:pointer; }
            @media print { .print-action { display:none; } thead { display:table-header-group; } tr { page-break-inside:avoid; break-inside:avoid; } }
            </style></head><body>
            <div class="top"><div><div class="brand">SARI SELLER</div><h1>Selected Earnings</h1>
            <div class="sub">Settlement ledger export · Selected records only</div></div>
            <div class="meta"><strong>${records.length} ${records.length === 1 ? 'settlement' : 'settlements'}</strong>
            Generated ${safe(generated)}<br>Philippine time (Asia/Manila)</div></div>
            <table><thead><tr><th>Order</th><th>Eligible</th><th style="text-align:right">Gross</th>
            <th style="text-align:right">Commission</th><th style="text-align:right">Withholding</th>
            <th style="text-align:right">Net</th><th>Status</th></tr></thead>
            <tbody>${rows}</tbody><tfoot><tr><td colspan="2">TOTAL SELECTED</td>
            <td class="amount">${peso(sum('gross'))}</td><td class="amount">${peso(sum('commission'))}</td>
            <td class="amount">${peso(sum('withholding'))}</td><td class="amount net">${peso(sum('net'))}</td><td>—</td>
            </tr></tfoot></table>
            <div class="foot">Internal settlement records only. This document is not proof of an external bank transfer. Values reflect the entries as displayed when selected.</div>
            <button type="button" class="print-action" onclick="window.print()">Print / Save as PDF</button>
            </body></html>`;
    };

    printButton?.addEventListener('click', () => {
        showError();
        const rows = [...selected.values()].filter(validRecord);
        if (!rows.length) return;
        const popup = window.open('', '_blank');
        if (!popup) {
            showError('The browser blocked the print window. Allow pop-ups for this website, then try again.');
            return;
        }
        popup.opener = null;
        popup.document.open();
        popup.document.write(renderReport(rows));
        popup.document.close();
        popup.setTimeout(() => { try { popup.focus(); popup.print(); } catch (_) {} }, 350);
    }, { signal });

    document.addEventListener('livewire:navigating', () => lifecycle.abort(), { once: true, signal });
    window.addEventListener('beforeunload', () => lifecycle.abort(), { once: true, signal });
    sync();
})();
</script>
@endpush
