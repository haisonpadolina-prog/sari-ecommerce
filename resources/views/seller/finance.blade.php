@extends('layouts.seller')

@section('title', 'Finance & Earnings — SARI Seller')

@section('page-title', 'Finance & Earnings')

@push('styles')

<style id="sariSellerFinanceProfessionalStyles">

    .seller-finance-page {

        --finance-gold: #d59617;

        --finance-gold-soft: #fffaf0;

        --finance-ink: #111827;

        --finance-text: #344054;

        --finance-muted: #7c8592;

        --finance-soft-muted: #98a2b3;

        --finance-line: #e5e7eb;

        width: 100%;

        max-width: 1440px;

        margin-inline: auto;

        padding-bottom: 24px;

        color: var(--finance-ink);

        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;

    }

    .seller-finance-page *,

    .seller-finance-page *::before,

    .seller-finance-page *::after {

        box-sizing: border-box;

    }

    .seller-finance-page button,

    .seller-finance-page input,

    .seller-finance-page select,

    .seller-finance-page a {

        font-family: inherit;

    }

    /* Feedback */

    .seller-finance-alert {

        display: flex;

        align-items: flex-start;

        gap: 9px;

        margin-bottom: 10px;

        border: 1px solid;

        border-radius: 12px;

        padding: 9px 11px;

        font-size: 9px;

        font-weight: 500;

        line-height: 1.5;

    }

    .seller-finance-alert--success {

        border-color: #d5e7dc;

        background: #f5faf7;

        color: #4f7d63;

    }

    .seller-finance-alert--error {

        border-color: #efdada;

        background: #fff7f7;

        color: #a65353;

    }

    .seller-finance-alert-icon {

        display: grid;

        width: 27px;

        height: 27px;

        flex: 0 0 27px;

        place-items: center;

        border: 1px solid currentColor;

        border-radius: 8px;

        background: #fff;

        opacity: .82;

    }

    .seller-finance-alert-icon svg {

        width: 13px;

        height: 13px;

    }

    /* Header */

    .seller-finance-header {

        min-height: 66px;

        padding: 2px 2px 13px;

        border-bottom: 1px solid #e7ebf0;

    }

    .seller-finance-eyebrow {

        margin: 0;

        color: #b87605;

        font-size: 8px;

        font-weight: 800;

        line-height: 1;

        letter-spacing: .16em;

        text-transform: uppercase;

    }

    .seller-finance-title {

        margin: 5px 0 0;

        color: var(--finance-ink);

        font-size: clamp(29px, 2.2vw, 36px);

        font-weight: 650;

        line-height: 1;

        letter-spacing: -.035em;

    }

    .seller-finance-title span:last-child {

        margin-left: .18em;

        color: #C9890B;

    }

    .seller-finance-subtitle {

        max-width: 820px;

        margin: 7px 0 0;

        color: var(--finance-muted);

        font-size: 10px;

        line-height: 1.5;

    }

    /* Summary cards */

    .seller-finance-summary {

        display: grid;

        grid-template-columns: repeat(5, minmax(0, 1fr));

        gap: 9px;

        margin-top: 12px;

    }

    .seller-finance-stat {

        min-width: 0;

        min-height: 80px;

        border: 1px solid var(--finance-line);

        border-radius: 13px;

        background: #fff;

        padding: 10px 12px;

        box-shadow: 0 5px 16px rgba(15, 23, 42, .028);

    }

    .seller-finance-stat-inner {

        display: flex;

        height: 100%;

        align-items: flex-start;

        justify-content: space-between;

        gap: 9px;

    }

    .seller-finance-stat-copy {

        min-width: 0;

    }

    .seller-finance-stat-label {

        color: #667085;

        font-size: 9.2px;

        font-weight: 600;

        line-height: 1.3;

    }

    .seller-finance-stat-value {

        margin-top: 5px;

        overflow: hidden;

        color: var(--finance-ink);

        font-size: clamp(16px, 1.45vw, 20px);

        font-weight: 720;

        line-height: 1.05;

        letter-spacing: -.035em;

        text-overflow: ellipsis;

        white-space: nowrap;

    }

    .seller-finance-stat-note {

        margin-top: 5px;

        color: var(--finance-soft-muted);

        font-size: 7.9px;

        line-height: 1.35;

    }

    .seller-finance-stat-icon {

        display: grid;

        width: 32px;

        height: 32px;

        flex: 0 0 32px;

        place-items: center;

        border: 1px solid rgba(17, 24, 39, .05);

        border-radius: 9px;

    }

    .seller-finance-stat-icon svg {

        width: 14px;

        height: 14px;

    }

    .seller-finance-stat-icon--gross {

        background: #f3f7fb;

        color: #52789b;

    }

    .seller-finance-stat-icon--commission {

        background: #fff8e9;

        color: #b97805;

    }

    .seller-finance-stat-icon--net {

        background: #f1f8f4;

        color: #4f7d63;

    }

    .seller-finance-stat-icon--pending {

        background: #fff9ef;

        color: #a8731f;

    }

    .seller-finance-stat-icon--paid {

        background: #f2f8fc;

        color: #4d7393;

    }

    /* Ledger */

    .seller-finance-ledger {

        margin-top: 10px;

        overflow: hidden;

        border: 1px solid var(--finance-line);

        border-radius: 13px;

        background: #fff;

        box-shadow: 0 6px 18px rgba(15, 23, 42, .03);

    }

    .seller-finance-ledger-head {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 12px;

        border-bottom: 1px solid #edf0f3;

        padding: 11px 13px;

        background: #fff;

    }

    .seller-finance-ledger-title-group {

        display: flex;

        min-width: 0;

        align-items: center;

        gap: 8px;

    }

    .seller-finance-ledger-icon {

        display: grid;

        width: 28px;

        height: 28px;

        flex: 0 0 28px;

        place-items: center;

        border: 1px solid #efe3c7;

        border-radius: 8px;

        background: var(--finance-gold-soft);

        color: #b97805;

    }

    .seller-finance-ledger-icon svg {

        width: 13px;

        height: 13px;

    }

    .seller-finance-ledger-title {

        margin: 0;

        color: #1f2937;

        font-size: 11px;

        font-weight: 700;

        line-height: 1.3;

    }

    .seller-finance-ledger-subtitle {

        margin: 2px 0 0;

        color: var(--finance-soft-muted);

        font-size: 8px;

        line-height: 1.35;

    }

    .seller-finance-ledger-count {

        display: inline-flex;

        min-height: 23px;

        align-items: center;

        border: 1px solid #e5e7eb;

        border-radius: 999px;

        background: #fafbfc;

        padding: 0 8px;

        color: #667085;

        font-size: 8px;

        font-weight: 700;

        white-space: nowrap;

    }

    .seller-finance-table-wrap {

        overflow-x: auto;

        background: #fff;

        scrollbar-width: thin;

        scrollbar-color: #d8dee6 transparent;

    }

    .seller-finance-table {

        width: 100%;

        min-width: 940px;

        border-collapse: collapse;

        text-align: left;

    }

    .seller-finance-table thead {

        background: #fafbfc;

    }

    .seller-finance-table th {

        height: 36px;

        border-bottom: 1px solid #e8ebef;

        padding: 0 12px;

        color: #667085;

        font-size: 8.3px;

        font-weight: 700;

        letter-spacing: .05em;

        text-transform: uppercase;

        white-space: nowrap;

    }

    .seller-finance-table td {

        height: 50px;

        border-bottom: 1px solid #edf0f3;

        padding: 7px 12px;

        color: #475467;

        font-size: 9px;

        font-weight: 500;

        line-height: 1.4;

        white-space: nowrap;

    }

    .seller-finance-table tbody tr:last-child td {

        border-bottom: 0;

    }

    .seller-finance-table tbody tr {

        transition: background-color .14s ease;

    }

    .seller-finance-table tbody tr:hover {

        background: #fffdf8;

    }

    .seller-finance-order {

        color: #1f2937 !important;

        font-size: 9.5px !important;

        font-weight: 700 !important;

    }

    .seller-finance-money {

        color: #344054 !important;

        font-variant-numeric: tabular-nums;

    }

    .seller-finance-money--muted {

        color: #667085 !important;

    }

    .seller-finance-money--net {

        color: #4f7d63 !important;

        font-weight: 700 !important;

    }

    .seller-finance-status {

        display: inline-flex;

        min-height: 22px;

        align-items: center;

        gap: 5px;

        border: 1px solid;

        border-radius: 999px;

        padding: 0 8px;

        font-size: 7.7px;

        font-weight: 700;

        text-transform: capitalize;

    }

    .seller-finance-status::before {

        content: "";

        width: 5px;

        height: 5px;

        border-radius: 999px;

        background: currentColor;

    }

    .seller-finance-status--paid,

    .seller-finance-status--released,

    .seller-finance-status--completed {

        border-color: #cfe4d7;

        background: #f1f8f4;

        color: #4f7d63;

    }

    .seller-finance-status--pending,

    .seller-finance-status--eligible {

        border-color: #eadfc9;

        background: #fff9ef;

        color: #a8731f;

    }

    .seller-finance-status--default {

        border-color: #e1e4e8;

        background: #f8fafc;

        color: #667085;

    }

    .seller-finance-empty {

        height: 140px !important;

        padding: 28px 18px !important;

        color: var(--finance-soft-muted) !important;

        font-size: 9px !important;

        text-align: center;

    }

    .seller-finance-pagination {

        border-top: 1px solid #edf0f3;

        padding: 10px 13px;

        background: #fff;

    }

    @media (max-width: 1180px) {

        .seller-finance-summary {

            grid-template-columns: repeat(3, minmax(0, 1fr));

        }

    }

    @media (max-width: 760px) {

        .seller-finance-title {

            font-size: clamp(28px, 8vw, 34px);

        }

        .seller-finance-summary {

            grid-template-columns: repeat(2, minmax(0, 1fr));

        }

        .seller-finance-ledger-head {

            align-items: flex-start;

            flex-direction: column;

        }

    }

    @media (max-width: 520px) {

        .seller-finance-summary {

            grid-template-columns: 1fr;

        }

        .seller-finance-stat {

            min-height: 74px;

        }

        .seller-finance-stat-value {

            font-size: 19px;

        }

    }

    @media (prefers-reduced-motion: reduce) {

        .seller-finance-page * {

            transition-duration: .01ms !important;

            scroll-behavior: auto !important;

        }

    }

/* ======================================================================
   FINANCE & EARNINGS — DASHBOARD FLOATING CONTAINERS FINAL PASS
   Front-end/CSS only. Finance stats, settlement records, status mapping,
   pagination, database and backend behavior remain unchanged.
   ====================================================================== */

.seller-finance-page {
    --finance-dashboard-border: #E5E7EB;
    --finance-dashboard-border-strong: #D1D5DB;
    --finance-dashboard-divider: #ECEFF2;

    /* Dashboard-style layered elevation:
       contact shadow + middle separation + broad ambient float. */
    --finance-dashboard-shadow:
        0 2px 5px rgba(15, 23, 42, .045),
        0 10px 24px rgba(15, 23, 42, .075),
        0 24px 54px rgba(15, 23, 42, .095),
        inset 0 1px 0 rgba(255, 255, 255, .92);

    --finance-dashboard-shadow-soft:
        0 1px 3px rgba(15, 23, 42, .035),
        0 7px 18px rgba(15, 23, 42, .06),
        0 15px 32px rgba(15, 23, 42, .065),
        inset 0 1px 0 rgba(255, 255, 255, .90);

    --finance-dashboard-shadow-inner:
        0 1px 2px rgba(15, 23, 42, .025),
        0 5px 14px rgba(15, 23, 42, .045);
}

/* Header stays flat; only the divider is neutral. */
.seller-finance-page .seller-finance-header {
    border-bottom-color: var(--finance-dashboard-divider) !important;
}

/* ------------------------------------------------------------------
   SUMMARY CARDS
   Medium dashboard elevation so the five finance metrics read as
   distinct floating surfaces without feeling too heavy.
   ------------------------------------------------------------------ */
.seller-finance-page .seller-finance-stat {
    border: 1px solid var(--finance-dashboard-border) !important;
    border-radius: 14px !important;
    background: #FFFFFF !important;
    box-shadow: var(--finance-dashboard-shadow-soft) !important;
    transform: none !important;
}

.seller-finance-page .seller-finance-stat:hover {
    border-color: var(--finance-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--finance-dashboard-shadow-soft) !important;
    transform: none !important;
}

/* Icon shells keep semantic colors but use neutral structure. */
.seller-finance-page .seller-finance-stat-icon {
    border-color: var(--finance-dashboard-border) !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, .035) !important;
}

/* ------------------------------------------------------------------
   SETTLEMENT LEDGER
   Strongest elevation because it is the primary work surface.
   ------------------------------------------------------------------ */
.seller-finance-page .seller-finance-ledger {
    border: 1px solid var(--finance-dashboard-border) !important;
    border-radius: 14px !important;
    background: #FFFFFF !important;
    box-shadow: var(--finance-dashboard-shadow) !important;
    transform: none !important;
}

.seller-finance-page .seller-finance-ledger:hover {
    border-color: var(--finance-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--finance-dashboard-shadow) !important;
    transform: none !important;
}

/* Ledger header stays visually attached to the floating shell. */
.seller-finance-page .seller-finance-ledger-head {
    border-bottom-color: var(--finance-dashboard-divider) !important;
    background: #FFFFFF !important;
}

/* Neutral icon shell; gold remains only as icon/text accent. */
.seller-finance-page .seller-finance-ledger-icon {
    border-color: var(--finance-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, .035) !important;
}

/* Count chip is a secondary informational surface. */
.seller-finance-page .seller-finance-ledger-count {
    border-color: var(--finance-dashboard-border) !important;
    background: #F8FAFC !important;
    box-shadow: 0 2px 7px rgba(15, 23, 42, .025) !important;
}

/* ------------------------------------------------------------------
   TABLE
   The ledger container floats; rows stay flat for financial scanability.
   ------------------------------------------------------------------ */
.seller-finance-page .seller-finance-table-wrap {
    background: #FFFFFF !important;
}

.seller-finance-page .seller-finance-table thead {
    background: #F8FAFC !important;
}

.seller-finance-page .seller-finance-table th {
    border-bottom-color: var(--finance-dashboard-divider) !important;
}

.seller-finance-page .seller-finance-table td {
    border-bottom-color: var(--finance-dashboard-divider) !important;
}

/* Replace the old cream row hover with a neutral light gray. */
.seller-finance-page .seller-finance-table tbody tr:hover {
    background: #F8FAFC !important;
}

/* Status badges retain semantic paid/pending colors. */
.seller-finance-page .seller-finance-status {
    box-shadow: 0 2px 7px rgba(15, 23, 42, .02) !important;
}

/* ------------------------------------------------------------------
   ALERTS / EMPTY / PAGINATION
   ------------------------------------------------------------------ */
.seller-finance-page .seller-finance-alert {
    box-shadow: var(--finance-dashboard-shadow-soft) !important;
    transform: none !important;
}

.seller-finance-page .seller-finance-empty {
    background: #FFFFFF !important;
}

.seller-finance-page .seller-finance-pagination {
    border-top-color: var(--finance-dashboard-divider) !important;
    background: #FFFFFF !important;
}

.seller-finance-page .seller-finance-pagination nav a,
.seller-finance-page .seller-finance-pagination nav span {
    border-color: var(--finance-dashboard-border) !important;
}

/* Structural surfaces never get colored outlines. */
.seller-finance-page .seller-finance-stat,
.seller-finance-page .seller-finance-ledger,
.seller-finance-page .seller-finance-alert {
    outline: 0 !important;
}

/* No physical hover motion anywhere on structural finance containers. */
.seller-finance-page .seller-finance-stat,
.seller-finance-page .seller-finance-ledger,
.seller-finance-page .seller-finance-alert {
    transition:
        border-color .14s ease,
        box-shadow .14s ease,
        background-color .14s ease !important;
}

/* Mobile: retain floating depth while reducing ambient spread. */
@media (max-width: 640px) {
    .seller-finance-page .seller-finance-stat {
        box-shadow:
            0 1px 3px rgba(15, 23, 42, .03),
            0 6px 14px rgba(15, 23, 42, .05),
            0 12px 26px rgba(15, 23, 42, .055) !important;
    }

    .seller-finance-page .seller-finance-ledger {
        box-shadow:
            0 2px 4px rgba(15, 23, 42, .035),
            0 8px 18px rgba(15, 23, 42, .06),
            0 18px 38px rgba(15, 23, 42, .07) !important;
    }

    .seller-finance-page .seller-finance-alert {
        box-shadow:
            0 1px 3px rgba(15, 23, 42, .03),
            0 6px 14px rgba(15, 23, 42, .05) !important;
    }
}
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

            <span class="seller-finance-ledger-count">

                {{ number_format($settlements->total()) }} records

            </span>

        </div>

        <div class="seller-finance-table-wrap">

            <table class="seller-finance-table">

                <thead>

                    <tr>

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

                        <tr>

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

                            <td colspan="7" class="seller-finance-empty">

                                No financial settlements yet.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($settlements->hasPages())

            <div class="seller-finance-pagination">

                {{ $settlements->links() }}

            </div>

        @endif

    </section>

</div>

@endsection
