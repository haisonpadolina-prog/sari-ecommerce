@extends('layouts.courier')

@section('title', 'COD Cash Remittance')
@section('header-title', 'COD Cash Remittance')
@section('header-subtitle', 'Physical Hub Turnover')

@push('styles')
<style>
@verbatim
body:has(#sariCodRemittance),
body:has(#sariCodRemittance) #courierMain {
    background: #f4f5f7 !important;
    color: #2f3742;
}

body:has(#sariCodRemittance) #courierSidebar {
    background: #fff !important;
    border-right-color: #e5e7eb !important;
}

body:has(#sariCodRemittance) #courierMain > header {
    background: rgba(255,255,255,.97) !important;
    border-bottom-color: #e5e7eb !important;
    box-shadow: none !important;
}

#courierMain > main:has(#sariCodRemittance) {
    background: transparent !important;
    padding: 22px clamp(14px,2.2vw,30px) 42px !important;
}

#sariCodRemittance {
    --cr-gold: #c88912;
    --cr-gold-hover: #af750d;
    --cr-ink: #2f3742;
    --cr-muted: #7a8593;
    --cr-border: #e5e7eb;
    --cr-panel: #ffffff;
    --cr-panel-soft: #f8fafc;
    --cr-hover: #f3f4f6;
    --cr-shadow:
        0 2px 5px rgba(15,23,42,.045),
        0 10px 24px rgba(15,23,42,.075),
        0 24px 54px rgba(15,23,42,.095),
        inset 0 1px 0 rgba(255,255,255,.92);
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    display: grid;
    gap: 18px;
    color: var(--cr-ink);
    font: 400 13px/1.65 'Poppins', ui-sans-serif, system-ui, sans-serif;
}

#sariCodRemittance,
#sariCodRemittance *,
#sariCodRemittance *::before,
#sariCodRemittance *::after { box-sizing: border-box; }

#sariCodRemittance button,
#sariCodRemittance input,
#sariCodRemittance select,
#sariCodRemittance textarea { font: inherit; }

#sariCodRemittance a { text-decoration: none; }
#sariCodRemittance button,
#sariCodRemittance a { touch-action: manipulation; }

#sariCodRemittance :is(a,button,input,select,textarea):focus-visible {
    outline: 2px solid #94a3b8;
    outline-offset: 3px;
}

#sariCodRemittance svg {
    width: 20px;
    height: 20px;
    stroke-width: 1.8;
    flex: none;
}

#sariCodRemittance .cr-headline {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px 20px;
    padding: 0 0 6px;
}

#courierMain #sariCodRemittance .cr-kicker {
    margin: 0 0 6px;
    font-size: 10.5px;
    line-height: 1.4;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--cr-gold) !important;
}

#courierMain #sariCodRemittance .cr-title {
    margin: 0;
    font-size: clamp(28px,2.35vw,33px);
    font-weight: 760;
    letter-spacing: -.04em;
    line-height: 1.05;
    color: var(--cr-ink);
}

#sariCodRemittance .cr-title-accent { color: var(--cr-gold); }

#sariCodRemittance .cr-back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    flex: none;
    position: relative;
    top: 12px;
    padding: 4px 0;
    color: #667180;
    font-size: 11.5px;
    font-weight: 750;
    white-space: nowrap;
    transition: color .16s ease;
}

#sariCodRemittance .cr-back-link:hover,
#sariCodRemittance .cr-back-link:focus-visible { color: #374151; }

#sariCodRemittance .cr-back-arrow {
    font-size: 18px;
    line-height: 1;
    font-weight: 800;
}

#sariCodRemittance .cr-card {
    min-width: 0;
    background: var(--cr-panel);
    border: 1px solid var(--cr-border);
    border-radius: 18px;
    box-shadow: var(--cr-shadow);
}

#sariCodRemittance .cr-section { padding: 23px; }

#sariCodRemittance .cr-main-grid {
    display: grid;
    grid-template-columns: minmax(0,1.35fr) minmax(320px,.85fr);
    align-items: start;
    gap: 18px;
}

#sariCodRemittance .cr-section-head {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 20px;
}

#courierMain #sariCodRemittance .cr-section-title {
    margin: 0;
    color: var(--cr-ink);
    font-size: 17px;
    line-height: 1.4;
    font-weight: 750;
    letter-spacing: -.025em;
}

#sariCodRemittance .cr-section-sub {
    margin: 5px 0 0;
    font-size: 11.5px;
    line-height: 1.75;
    color: var(--cr-muted);
}

#sariCodRemittance .cr-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    color: #6b7280;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
}

#sariCodRemittance .cr-orders {
    overflow: hidden;
    border: 1px solid #e8ebef;
    border-radius: 14px;
    background: #fff;
}

#sariCodRemittance .cr-order-head,
#sariCodRemittance .cr-order {
    display: grid;
    grid-template-columns: 30px minmax(0,1.6fr) minmax(0,1fr) minmax(105px,.9fr);
    gap: 13px;
    align-items: center;
}

#sariCodRemittance .cr-order-head {
    padding: 12px 15px;
    border-bottom: 1px solid #e9edf1;
    background: #f8fafc;
    color: #8a94a3;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
}

#sariCodRemittance .cr-order {
    padding: 14px 15px;
    min-width: 0;
    cursor: pointer;
    border-top: 1px solid #edf0f3;
    background: #fff;
    transition: background-color .16s ease, border-color .16s ease;
}

#sariCodRemittance .cr-order-head + .cr-order { border-top: 0; }
#sariCodRemittance .cr-order:hover { background: var(--cr-hover); }
#sariCodRemittance .cr-order:has(input:checked) {
    background: #f9fafb;
}
#sariCodRemittance .cr-order:has(input:checked):hover { background: #f3f4f6; }

#sariCodRemittance .cr-checkbox-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
}

#sariCodRemittance .cr-check {
    appearance: none;
    -webkit-appearance: none;
    width: 18px;
    height: 18px;
    margin: 0;
    border-radius: 6px;
    border: 1.5px solid #cbd5e1;
    background: #fff;
    display: grid;
    place-items: center;
    cursor: pointer;
    transition: background-color .16s ease, border-color .16s ease, box-shadow .16s ease;
}

#sariCodRemittance .cr-check::after {
    content: '';
    width: 8px;
    height: 5px;
    border-left: 2px solid #fff;
    border-bottom: 2px solid #fff;
    transform: rotate(-45deg) scale(0);
    transform-origin: center;
    transition: transform .14s ease;
    margin-top: -1px;
}

#sariCodRemittance .cr-check:hover { border-color: var(--cr-gold); }
#sariCodRemittance .cr-check:checked {
    background: var(--cr-gold);
    border-color: var(--cr-gold);
}
#sariCodRemittance .cr-check:checked::after { transform: rotate(-45deg) scale(1); }
#sariCodRemittance .cr-check:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px rgba(200,137,18,.20);
    border-color: var(--cr-gold);
}

#sariCodRemittance .cr-order-id {
    display: block;
    overflow-wrap: anywhere;
    color: #303844;
    font-size: 11.5px;
    font-weight: 700;
}

#sariCodRemittance .cr-order-date {
    display: block;
    margin-top: 2px;
    color: #9aa4af;
    font-size: 10px;
}

#sariCodRemittance .cr-order-buyer {
    overflow-wrap: anywhere;
    color: #6f7c8b;
    font-size: 11.5px;
}

#sariCodRemittance .cr-order-cash { min-width: 0; }

#sariCodRemittance .cr-order-amount {
    color: #293440;
    font-size: 12.5px;
    font-weight: 750;
    font-variant-numeric: tabular-nums;
}

#sariCodRemittance .cr-empty {
    display: grid;
    justify-items: center;
    gap: 9px;
    padding: 38px 18px;
    text-align: center;
    color: #93a0ad;
}

#sariCodRemittance .cr-empty-icon {
    display: grid;
    place-items: center;
    width: 48px;
    height: 48px;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    color: #6b7280;
}

#sariCodRemittance .cr-empty-icon svg {
    width: 24px;
    height: 24px;
}

#sariCodRemittance .cr-empty strong {
    color: #5f6b78;
    font-size: 13px;
    font-weight: 700;
}

#sariCodRemittance .cr-empty p {
    margin: 0;
    color: #93a0ad;
    font-size: 11.5px;
}

#sariCodRemittance .cr-total {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-top: 18px;
    padding: 17px;
    border-radius: 13px;
    border: 1px solid #e9edf1;
    background: #f8fafc;
}

#sariCodRemittance .cr-eyebrow {
    color: #7f8b99;
    font-size: 11px;
    font-weight: 700;
}

#sariCodRemittance .cr-total-note {
    max-width: 460px;
    margin: 4px 0 0;
    color: #86929f;
    font-size: 11px;
    line-height: 1.7;
}

#sariCodRemittance .cr-total strong {
    color: #27313d;
    font-size: 23px;
    letter-spacing: -.04em;
    font-weight: 750;
    font-variant-numeric: tabular-nums;
}

#sariCodRemittance .cr-fields {
    display: grid;
    gap: 16px;
    margin-top: 19px;
}

#sariCodRemittance .cr-field {
    display: grid;
    gap: 8px;
    min-width: 0;
}

#sariCodRemittance .cr-field-label {
    color: #465363;
    font-size: 11.5px;
    font-weight: 700;
}

#sariCodRemittance .cr-required { color: #4b5563; }
#sariCodRemittance .cr-optional { color: #93a0ad; font-weight: 400; }

#sariCodRemittance .cr-control {
    width: 100%;
    min-width: 0;
    min-height: 46px;
    padding: 12px 14px;
    border: 1px solid #d9e0e7;
    border-radius: 12px;
    background: #fff;
    color: #33404d;
    font-size: 11.5px;
    transition: border-color .16s ease, box-shadow .16s ease, background-color .16s ease;
}

#sariCodRemittance select.cr-control {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    padding-right: 44px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 16px;
    cursor: pointer;
}

#sariCodRemittance select.cr-control:hover,
#sariCodRemittance .cr-control:hover {
    border-color: #cbd5df;
    background-color: #fbfcfd;
}

#sariCodRemittance .cr-control:focus {
    border-color: #94a3b8;
    outline: 0;
    box-shadow: 0 0 0 4px rgba(148,163,184,.16);
    background-color: #fff;
}

#sariCodRemittance .cr-control::placeholder { color: #a3acb6; }
#sariCodRemittance .cr-hub-warning { color: #ab3549; font-size: 11px; }

#sariCodRemittance .cr-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    min-height: 45px;
    padding: 11px 16px;
    border: 1px solid #e1e5eb;
    border-radius: 12px;
    background: #fff;
    color: #485665;
    font-size: 11.5px;
    font-weight: 700;
    text-align: center;
    cursor: pointer;
    box-shadow: none;
    transition: background-color .16s ease, border-color .16s ease, color .16s ease;
}

#sariCodRemittance .cr-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #293440;
}

#sariCodRemittance .cr-btn svg { width: 17px; height: 17px; }

#sariCodRemittance .cr-btn--primary {
    background: #334155;
    border-color: #334155;
    color: #fff;
}

#sariCodRemittance .cr-btn--primary:hover {
    background: #1f2937;
    border-color: #1f2937;
    color: #fff;
}

#sariCodRemittance .cr-btn--gold {
    background: var(--cr-gold);
    border-color: var(--cr-gold);
    color: #fff;
}

#sariCodRemittance .cr-btn--gold:hover {
    background: var(--cr-gold-hover);
    border-color: var(--cr-gold-hover);
    color: #fff;
}

#sariCodRemittance .cr-btn--wide { width: 100%; }
#sariCodRemittance .cr-btn:disabled { cursor: not-allowed; opacity: .45; }

#sariCodRemittance .cr-batches { display: grid; gap: 12px; }

#sariCodRemittance .cr-batch {
    min-width: 0;
    padding: 16px;
    border: 1px solid #e8ebef;
    border-radius: 13px;
    background: #fff;
    box-shadow: none;
}

#sariCodRemittance .cr-batch-top {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
}

#sariCodRemittance .cr-batch-code {
    color: #2e3945;
    font-size: 12px;
    font-weight: 750;
    overflow-wrap: anywhere;
}

#sariCodRemittance .cr-batch-detail {
    margin-top: 6px;
    color: #84909c;
    font-size: 11px;
    line-height: 1.7;
    overflow-wrap: anywhere;
}

#sariCodRemittance .cr-batch-summary {
    flex: none;
    text-align: right;
}

#sariCodRemittance .cr-batch-amount {
    color: #293440;
    font-size: 17px;
    font-weight: 750;
    letter-spacing: -.035em;
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
}

#sariCodRemittance .cr-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 5px;
    padding: 4px 9px;
    border-radius: 999px;
    background: #f3f4f6;
    color: #647384;
    font-size: 10px;
    font-weight: 650;
    text-transform: capitalize;
}

#sariCodRemittance .cr-status::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

#sariCodRemittance .cr-status--verified {
    background: #eef2f7;
    color: #3f4e61;
}

#sariCodRemittance .cr-status--discrepancy {
    background: #f6f6f6;
    color: #7a3f47;
}

#sariCodRemittance .cr-batch-action {
    margin-top: 13px;
}

#sariCodRemittance .cr-help {
    color: #909ba7;
    font-size: 11px;
    line-height: 1.7;
}

#sariCodRemittance .cr-qr-panel {
    margin-top: 15px;
    padding: 15px;
    border: 1px solid #e8ebef;
    border-radius: 12px;
    background: #f8fafc;
}

#sariCodRemittance .cr-qr-inner {
    display: grid;
    grid-template-columns: 180px minmax(0,1fr);
    gap: 18px;
    align-items: center;
}

#sariCodRemittance .cr-qr-image {
    display: grid;
    place-items: center;
    width: 180px;
    height: 180px;
    padding: 12px;
    background: #fff;
    border: 1px solid #e5e8ed;
    border-radius: 12px;
    overflow-wrap: anywhere;
    text-align: center;
    font-size: 10px;
}

#sariCodRemittance .cr-qr-heading {
    margin: 0;
    color: #293440;
    font-size: 12px;
    font-weight: 750;
    overflow-wrap: anywhere;
}

#sariCodRemittance .cr-qr-meta {
    display: grid;
    grid-template-columns: repeat(2,minmax(0,1fr));
    gap: 9px;
    margin: 11px 0;
}

#sariCodRemittance .cr-qr-stat {
    min-width: 0;
    padding: 10px;
    border: 1px solid #e7ebef;
    border-radius: 10px;
    background: #fff;
}

#sariCodRemittance .cr-qr-stat span {
    display: block;
    margin-bottom: 3px;
    color: #8b97a3;
    font-size: 10px;
}

#sariCodRemittance .cr-qr-stat strong {
    display: block;
    color: #293440;
    font-size: 14px;
    font-weight: 750;
    overflow-wrap: anywhere;
}

#sariCodRemittance .cr-qr-caption {
    margin: 7px 0 0;
    color: #84909c;
    font-size: 10.5px;
    line-height: 1.7;
}

#sariCodRemittance .cr-alert {
    padding: 12px 15px;
    border: 1px solid #e5e8ed;
    border-radius: 11px;
    background: #fff;
    font-size: 11.5px;
}

#sariCodRemittance .cr-alert--success {
    border-color: #d8dee6;
    background: #f8fafc;
    color: #334155;
}

#sariCodRemittance .cr-alert--error {
    border-color: #ead6da;
    background: #fbf7f8;
    color: #8c4651;
}

#sariCodRemittance [hidden],
#sariCodRemittance .hidden { display: none !important; }

@media (max-width: 1180px) {
    #sariCodRemittance .cr-main-grid { grid-template-columns: minmax(0,1fr); }
    #sariCodRemittance .cr-batches { grid-template-columns: repeat(2,minmax(0,1fr)); }
}

@media (max-width: 900px) {
    #sariCodRemittance .cr-batches { grid-template-columns: 1fr; }
}

@media (max-width: 767px) {
    #courierMain > main:has(#sariCodRemittance) { padding: 16px 14px 28px !important; }
    #sariCodRemittance { gap: 14px; }
    #courierMain #sariCodRemittance .cr-title { font-size: 25px; }
    #sariCodRemittance .cr-kicker { font-size: 9.5px; }
    #sariCodRemittance .cr-section { padding: 17px; }
    #sariCodRemittance .cr-card {
        box-shadow:
            0 1px 3px rgba(15,23,42,.035),
            0 6px 16px rgba(15,23,42,.055),
            0 13px 28px rgba(15,23,42,.065),
            inset 0 1px 0 rgba(255,255,255,.90);
    }
    #sariCodRemittance .cr-back-link { top: 0; font-size: 10.5px; }
    #sariCodRemittance .cr-back-arrow { font-size: 16px; }
}

@media (max-width: 640px) {
    #sariCodRemittance .cr-order-head { display: none; }
    #sariCodRemittance .cr-order {
        grid-template-columns: 24px minmax(0,1fr) auto;
        gap: 5px 10px;
        padding: 13px;
    }
    #sariCodRemittance .cr-order-buyer { grid-column: 2; grid-row: 2; }
    #sariCodRemittance .cr-order-cash { grid-column: 3; grid-row: 1 / 3; text-align: right; }
    #sariCodRemittance .cr-qr-inner { grid-template-columns: 1fr; justify-items: center; }
    #sariCodRemittance .cr-qr-info { width: 100%; min-width: 0; }
    #sariCodRemittance .cr-total strong { font-size: 22px; }
    #sariCodRemittance .cr-batch-summary { max-width: 100%; }
}

@media (prefers-reduced-motion: reduce) {
    #sariCodRemittance * { transition: none !important; scroll-behavior: auto !important; }
}
@endverbatim
</style>
@endpush

@section('content')
{{-- SARI_COD_CUSTODY_POLICY_V1 --}}
@include('finance.partials.rider-cod-warning')
<div id="sariCodRemittance">
    @if(session('success'))
        <div class="cr-alert cr-alert--success" role="status">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="cr-alert cr-alert--error" role="alert">{{ $errors->first() }}</div>
    @endif

    <header class="cr-headline">
        <div>
            <p class="cr-kicker">SARI Marketplace</p>
            <h1 class="cr-title">COD Cash <span class="cr-title-accent">Remittance</span></h1>
        </div>
        <a href="{{ route('courier.dashboard') }}" class="cr-back-link">
            Back to Dashboard <span class="cr-back-arrow" aria-hidden="true">&gt;</span>
        </a>
    </header>

    <div class="cr-main-grid">
        <form method="POST" action="{{ route('courier.remittances.store') }}" class="cr-card cr-section" id="sariCashBatchForm">
            @csrf

            <div class="cr-section-head">
                <div>
                    <h2 class="cr-section-title">Prepare Cash Batch</h2>
                    <p class="cr-section-sub">Delivered, paid COD parcels not yet remitted.</p>
                </div>
                <span class="cr-pill">Physical cash only</span>
            </div>

            <div class="cr-orders" aria-label="Eligible COD parcels">
                @if($eligibleOrders->isNotEmpty())
                    <div class="cr-order-head" aria-hidden="true">
                        <span></span><span>Order / Delivered</span><span>Buyer</span><span>COD amount</span>
                    </div>
                @endif

                @forelse($eligibleOrders as $order)
                    <label class="cr-order">
                        <span class="cr-checkbox-wrap">
                            <input class="cr-check" type="checkbox" name="order_ids[]" value="{{ $order->id }}" checked aria-label="Select COD order {{ $order->order_number }}">
                        </span>
                        <span>
                            <span class="cr-order-id">{{ $order->order_number }}</span>
                            <span class="cr-order-date">{{ $order->delivered_at?->format('M d, Y · h:i A') }}</span>
                        </span>
                        <span class="cr-order-buyer">{{ $order->buyer_name ?: 'Buyer' }}</span>
                        <span class="cr-order-cash"><span class="cr-order-amount">₱{{ number_format((float) $order->total, 2) }}</span></span>
                    </label>
                @empty
                    <div class="cr-empty">
                        <span class="cr-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7l9-4 9 4v10l-9 4-9-4V7zM3 7l9 4 9-4M12 11v10"/></svg></span>
                        <strong>All caught up!</strong>
                        <p>No COD cash is waiting for physical remittance.</p>
                    </div>
                @endforelse
            </div>

            <div class="cr-total">
                <div>
                    <div class="cr-eyebrow">Total eligible COD cash</div>
                    <p class="cr-total-note">Shown for all eligible parcels. The submitted batch amount is calculated on the server from your checked orders; rider earnings are not deducted.</p>
                </div>
                <strong>₱{{ number_format($expectedTotal, 2) }}</strong>
            </div>

            <div class="cr-fields">
                <label class="cr-field">
                    <span class="cr-field-label">Physical turnover hub <span class="cr-required" aria-hidden="true">*</span></span>
                    <select name="logistics_facility_id" required class="cr-control">
                        <option value="">Choose the hub where you will bring the cash</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}" @selected(old('logistics_facility_id') == $facility->id)>{{ $facility->name }}{{ $facility->municipality_name ? ' · '.$facility->municipality_name : '' }}</option>
                        @endforeach
                    </select>
                    @if($facilities->isEmpty())
                        <span class="cr-hub-warning">No active Logistics hub is configured for your provider. Please contact Logistics.</span>
                    @endif
                </label>

                <label class="cr-field">
                    <span class="cr-field-label">Rider note <span class="cr-optional">(optional)</span></span>
                    <input name="rider_note" maxlength="1000" value="{{ old('rider_note') }}" class="cr-control" placeholder="Optional details about the cash turnover">
                </label>

                <button type="submit" @disabled($eligibleOrders->isEmpty() || $facilities->isEmpty()) class="cr-btn cr-btn--gold cr-btn--wide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    Prepare Physical Cash Remittance
                </button>
            </div>
        </form>

        <section class="cr-card cr-section" aria-labelledby="remittanceBatchesHeading">
            <div class="cr-section-head">
                <div>
                    <h2 class="cr-section-title" id="remittanceBatchesHeading">Cash Remittance Batches</h2>
                    <p class="cr-section-sub">Open batches require a QR and the full physical cash.</p>
                </div>
                <span class="cr-pill">{{ $batches->count() }} {{ $batches->count() === 1 ? 'batch' : 'batches' }}</span>
            </div>

            <div class="cr-batches">
                @forelse($batches as $batch)
                    @php
                        $isOpen = in_array($batch->status, ['submitted', 'discrepancy'], true);
                        $scanUrl = $isOpen
                            ? \Illuminate\Support\Facades\URL::temporarySignedRoute('logistics.remittances.show', now()->addHours(24), ['batch' => $batch->id], false)
                            : null;
                    @endphp

                    <article class="cr-batch">
                        <div class="cr-batch-top">
                            <div style="min-width:0;flex:1">
                                <div class="cr-batch-code">{{ $batch->batch_code }}</div>
                                <div class="cr-batch-detail">
                                    {{ $batch->items_count }} {{ $batch->items_count == 1 ? 'parcel' : 'parcels' }} · {{ $batch->hub_name_snapshot ?: $batch->facility?->name ?: 'Logistics Hub' }}
                                    @if($batch->hub_address_snapshot)
                                        <span style="display:block">{{ $batch->hub_address_snapshot }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="cr-batch-summary">
                                <div class="cr-batch-amount">₱{{ number_format((float) $batch->expected_amount, 2) }}</div>
                                <span class="cr-status {{ $batch->status === 'verified' ? 'cr-status--verified' : ($batch->status === 'discrepancy' ? 'cr-status--discrepancy' : '') }}">{{ str_replace('_', ' ', (string) $batch->status) }}</span>
                            </div>
                        </div>

                        @if($isOpen && $scanUrl)
                            <div class="cr-batch-action">
                                <button type="button" class="cr-btn cr-btn--primary" data-remittance-qr-toggle="{{ $batch->id }}" aria-expanded="false" aria-controls="remittanceQrPanel{{ $batch->id }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM18 18h3v3h-3zM18 14h3M14 18v3"/></svg>
                                    <span data-remittance-qr-label>Show Remittance QR</span>
                                </button>

                                <div id="remittanceQrPanel{{ $batch->id }}" class="cr-qr-panel hidden" data-remittance-qr-panel="{{ $batch->id }}">
                                    <div class="cr-qr-inner">
                                        <div id="remittanceQr{{ $batch->id }}" class="cr-qr-image" data-qr-url="{{ $scanUrl }}" data-qr-rendered="false" aria-label="Remittance QR for {{ $batch->batch_code }}"></div>
                                        <div class="cr-qr-info">
                                            <p class="cr-qr-heading">{{ $batch->batch_code }}</p>
                                            <div class="cr-qr-meta">
                                                <div class="cr-qr-stat"><span>Physical cash</span><strong>₱{{ number_format((float) $batch->expected_amount, 2) }}</strong></div>
                                                <div class="cr-qr-stat"><span>Parcels</span><strong>{{ $batch->items_count }}</strong></div>
                                            </div>
                                            <p class="cr-qr-caption">Present this QR with the full cash amount at {{ $batch->hub_name_snapshot ?: $batch->facility?->name ?: 'the Logistics hub' }}. Logistics will physically count the cash before verification.</p>
                                            @if($batch->status === 'discrepancy')
                                                <div class="cr-alert cr-alert--error" style="margin-top:10px">Previous cash-count difference: ₱{{ number_format(abs((float) $batch->difference_amount), 2) }}. Resolve the discrepancy before verification.</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif(!$isOpen)
                            <div class="cr-batch-action cr-help">This batch is closed. No QR is required.</div>
                        @endif
                    </article>
                @empty
                    <div class="cr-empty">
                        <span class="cr-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="4" y="5" width="16" height="14" rx="2"/><path d="M8 9h8M8 13h5"/></svg></span>
                        <strong>No batches yet</strong>
                        <p>Prepared remittance batches will appear here.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('sariCodRemittance');
    if (!root) return;

    const renderQr = (node) => {
        if (!node || node.dataset.qrRendered === 'true') return true;

        if (typeof QRCode === 'undefined') {
            node.textContent = 'QR library could not load. Check your internet connection and reload this page.';
            return false;
        }

        node.replaceChildren();
        new QRCode(node, {
            text: node.dataset.qrUrl,
            width: 156,
            height: 156,
            colorDark: '#151515',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
        node.dataset.qrRendered = 'true';
        return true;
    };

    root.querySelectorAll('[data-remittance-qr-toggle]').forEach(button => {
        button.addEventListener('click', () => {
            const batchId = button.dataset.remittanceQrToggle;
            const panel = root.querySelector(`[data-remittance-qr-panel="${batchId}"]`);
            if (!panel) return;

            const opening = panel.classList.contains('hidden');
            panel.classList.toggle('hidden', !opening);
            button.setAttribute('aria-expanded', opening ? 'true' : 'false');

            const label = button.querySelector('[data-remittance-qr-label]');
            if (label) label.textContent = opening ? 'Hide Remittance QR' : 'Show Remittance QR';

            if (opening) {
                renderQr(panel.querySelector('[data-qr-url]'));
                panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    });
});
</script>
@endpush
