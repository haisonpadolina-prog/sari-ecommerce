@extends('layouts.seller')

@section('title', 'Promotions & Vouchers — SARI Seller')

@section('page-title', 'Promotions & Vouchers')

@push('styles')
<style id="sariSellerVouchersCleanUi">
@verbatim
/* SARI SELLER — PROMOTIONS & VOUCHERS | Unified clean layout */
.seller-vouchers-page {
    --voucher-gold:#C88912;
    --voucher-gold-hover:#AD730F;
    --voucher-ink:#293440;
    --voucher-text:#475569;
    --voucher-muted:#8190A1;
    --voucher-line:#E5E9EE;
    --voucher-soft:#F8FAFC;
    width:100%; max-width:1520px; margin-inline:auto; padding-bottom:36px;
    font-family:"Poppins",ui-sans-serif,system-ui,sans-serif;
    color:var(--voucher-ink);
}
.seller-vouchers-page *, .seller-vouchers-page *::before, .seller-vouchers-page *::after {box-sizing:border-box}
.seller-vouchers-page :is(button,input,select,summary,a) {font-family:inherit}
.seller-vouchers-page :is(button,input,select,summary,a):focus-visible {outline:2px solid rgba(200,137,18,.46);outline-offset:2px}
.seller-vouchers-page :is(button,a,summary) {transition:background-color .15s ease,border-color .15s ease,color .15s ease}
.seller-vouchers-header {padding:0 2px 17px;border-bottom:1px solid var(--voucher-line)}
.seller-vouchers-eyebrow {margin:0;color:#A87316;font-size:10.5px;font-weight:800;line-height:1.35;letter-spacing:.11em;text-transform:uppercase}
.seller-vouchers-title {margin:5px 0 0;color:#2E343B;font-size:clamp(27px,2.3vw,31px);font-weight:760;line-height:1.14;letter-spacing:-.03em}
.seller-vouchers-title span:last-child {margin-left:.2em;color:var(--voucher-gold)}
.seller-vouchers-subtitle {max-width:810px;margin:8px 0 0;color:#7B8897;font-size:11.5px;line-height:1.65}
/* Consistent white/gray notification treatment */
.seller-voucher-alert {display:flex;align-items:flex-start;gap:10px;margin-bottom:13px;padding:12px 14px;border:1px solid;border-radius:13px;font-size:11.5px;line-height:1.55;font-weight:500}
.seller-voucher-alert--success {border-color:#BFE7CA;background:#F0FDF4;color:#166534}
.seller-voucher-alert--error {border-color:#FECACA;background:#FEF2F2;color:#B91C1C}
.seller-voucher-alert-icon {display:grid;flex:0 0 22px;width:22px;height:22px;place-items:center;border:0;background:transparent}
.seller-voucher-alert-icon svg {width:19px;height:19px}
/* Major headings and floating work surfaces */
.seller-shipping-promo-section {margin:24px 0 22px}
.seller-shipping-promo-head {display:flex;align-items:flex-end;justify-content:space-between;gap:14px;margin-bottom:13px}
.seller-shipping-promo-head h2 {margin:0;color:#293440;font-size:18px;font-weight:750;letter-spacing:-.025em;line-height:1.35}
.seller-shipping-promo-head p {margin:5px 0 0;color:#8190A1;font-size:11px;line-height:1.6}
.seller-shipping-promo-tz {color:#A56D0A;font-weight:650}
.seller-shipping-promo-grid {display:grid;grid-template-columns:minmax(330px,.88fr) minmax(0,1.12fr);gap:16px;align-items:stretch}
.seller-shipping-promo-card, .seller-voucher-panel {
    min-width:0;border:1px solid #E5E7EB;border-radius:18px;background:#FFF;
    box-shadow:0 2px 5px rgba(15,23,42,.025),0 10px 24px rgba(15,23,42,.07);
}
.seller-shipping-promo-card {padding:21px 22px}
.seller-shipping-promo-card h3 {margin:0;color:#293440;font-size:16px;font-weight:750;line-height:1.4}
.seller-shipping-promo-note {margin:6px 0 0;color:#8491A0;font-size:11px;line-height:1.6}
.seller-shipping-promo-fields {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px;margin-top:18px}
.seller-shipping-promo-field {min-width:0}
.seller-shipping-promo-field label, .seller-voucher-label {display:block;margin:0 0 7px;color:#586473;font-size:11px;line-height:1.4;font-weight:650}
.seller-voucher-label small {color:#94A3B8;font-size:10px;font-weight:500}
.seller-shipping-promo-field input, .seller-voucher-input, .seller-voucher-select {
    display:block;width:100%;height:48px;min-height:48px;min-width:0;border:1px solid #DCE2E8;
    border-radius:12px;background:#FFF;padding:0 14px;color:#344054;font-size:11.5px;font-weight:550;
    box-shadow:none;outline:0;transition:border-color .15s ease,box-shadow .15s ease;
}
.seller-shipping-promo-field input::placeholder, .seller-voucher-input::placeholder {color:#9BA6B3;font-weight:400}
.seller-shipping-promo-field input:hover, .seller-voucher-input:hover, .seller-voucher-select:hover {border-color:#C9D1DB}
.seller-shipping-promo-field input:focus, .seller-voucher-input:focus, .seller-voucher-select:focus {
    border-color:var(--voucher-gold);box-shadow:0 0 0 3px rgba(200,137,18,.09);outline:0;
}
.seller-shipping-promo-submit, .seller-voucher-submit {
    display:inline-flex;align-items:center;justify-content:center;gap:9px;min-height:48px;
    border:1px solid var(--voucher-gold);border-radius:12px;background:var(--voucher-gold);
    padding:0 18px;color:#FFF;font-size:12px;font-weight:700;cursor:pointer;box-shadow:none;
}
.seller-shipping-promo-submit:hover, .seller-voucher-submit:hover {background:var(--voucher-gold-hover);border-color:var(--voucher-gold-hover)}
.seller-shipping-promo-submit {margin-top:16px;max-width:100%}
.seller-shipping-promo-funder {margin-top:17px;padding:12px 14px;border:1px solid var(--voucher-line);border-radius:12px;background:var(--voucher-soft);color:#6D7988;font-size:10.5px;line-height:1.65}
.seller-shipping-promo-funder strong {color:#455261;font-weight:750}
.seller-shipping-promo-list {display:grid;gap:0;margin-top:17px}
.seller-shipping-promo-item {padding:15px 0;border:0;border-bottom:1px solid #EDF0F3;border-radius:0;background:transparent}
.seller-shipping-promo-item:first-child {padding-top:0}
.seller-shipping-promo-item:last-child {border-bottom:0;padding-bottom:0}
.seller-shipping-promo-item-head {display:flex;align-items:flex-start;justify-content:space-between;gap:14px}
.seller-shipping-promo-terms {color:#303D4C;font-size:12px;font-weight:700;line-height:1.5}
.seller-shipping-promo-meta {margin-top:5px;color:#8794A2;font-size:10.5px;line-height:1.6}
.seller-shipping-promo-status, .seller-voucher-status {display:inline-flex;flex-shrink:0;align-items:center;justify-content:center;min-height:27px;border:1px solid;border-radius:999px;padding:3px 11px;font-size:10px;font-weight:700;line-height:1.3;white-space:nowrap}
.seller-shipping-promo-status.is-active, .seller-voucher-status--active {border-color:#BBF7D0;background:#ECFDF3;color:#166534}
.seller-shipping-promo-status.is-scheduled {border-color:#BFDBFE;background:#EFF6FF;color:#1D4ED8}
.seller-shipping-promo-status.is-paused, .seller-voucher-status--inactive {border-color:#E2E8F0;background:#F8FAFC;color:#64748B}
.seller-shipping-promo-status.is-ended {border-color:#FECACA;background:#FEF2F2;color:#B91C1C}
.seller-shipping-promo-actions {display:flex;flex-wrap:wrap;gap:9px;margin-top:11px}
.seller-shipping-promo-actions form {margin:0}
.seller-shipping-promo-action, .seller-shipping-promo-edit summary, .seller-voucher-action-button {
    display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:36px;
    border:1px solid #DCE2E8;border-radius:10px;background:#FFF;padding:0 13px;
    color:#526071;font-size:11px;font-weight:650;line-height:1.3;cursor:pointer;text-decoration:none;list-style:none;
}
.seller-shipping-promo-action:hover, .seller-shipping-promo-edit summary:hover, .seller-voucher-action-button--toggle:hover {border-color:#C9D1DB;background:#F8FAFC;color:#344054}
.seller-shipping-promo-action.is-danger, .seller-voucher-action-button--delete {border-color:#FECACA;color:#B91C1C;background:#FFF}
.seller-shipping-promo-action.is-danger:hover, .seller-voucher-action-button--delete:hover {background:#FEF2F2;border-color:#FCA5A5;color:#991B1B}
.seller-shipping-promo-edit {margin-top:10px}
.seller-shipping-promo-edit summary::-webkit-details-marker {display:none}
.seller-shipping-promo-edit[open] summary {border-color:#D1A24E;color:#A56D0A}
.seller-shipping-promo-edit-form {margin-top:13px;border-top:1px solid var(--voucher-line);padding-top:2px}
.seller-shipping-promo-empty {display:grid;min-height:190px;place-items:center;margin-top:16px;padding:24px;text-align:center;border:1px dashed #DCE2E8;border-radius:12px;background:#FAFBFC;color:#8491A0;font-size:12px;line-height:1.6}
/* Voucher create and listing panels */
.seller-vouchers-layout {display:grid;grid-template-columns:minmax(360px,420px) minmax(0,1fr);align-items:start;gap:16px;margin-top:23px}
.seller-voucher-panel {overflow:hidden}
.seller-voucher-panel-head {display:flex;align-items:center;justify-content:space-between;gap:12px;padding:21px 22px 17px;border-bottom:1px solid var(--voucher-line);background:#FFF}
.seller-voucher-panel-title-group {display:flex;align-items:center;min-width:0;gap:11px}
.seller-voucher-panel-icon {display:grid;place-items:center;flex:0 0 27px;width:27px;height:27px;border:0;border-radius:0;background:transparent;color:var(--voucher-gold);box-shadow:none}
.seller-voucher-panel-icon svg {width:27px;height:27px}
.seller-voucher-panel-title {margin:0;color:#293440;font-size:18px;font-weight:750;line-height:1.3;letter-spacing:-.025em}
.seller-voucher-panel-subtitle {margin:5px 0 0;color:#8491A0;font-size:11px;line-height:1.55}
.seller-voucher-count {display:inline-flex;align-items:center;min-height:28px;padding:3px 10px;border:1px solid #E2E8F0;border-radius:999px;background:#F8FAFC;color:#64748B;font-size:10.5px;font-weight:700;white-space:nowrap}
.seller-voucher-form {padding:21px 22px 22px}
.seller-voucher-form-grid {display:grid;gap:16px}
.seller-voucher-field-grid {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.seller-voucher-field {min-width:0}
.seller-voucher-code {font-weight:700;letter-spacing:.04em;text-transform:uppercase}
.seller-voucher-form-divider {height:1px;margin:2px 0 0;background:var(--voucher-line)}
.seller-voucher-section-label {color:#A56D0A;font-size:10px;font-weight:800;line-height:1.35;letter-spacing:.09em;text-transform:uppercase}
.seller-voucher-submit {width:100%;margin-top:3px}
.seller-voucher-submit svg {width:16px;height:16px}
.seller-voucher-form-note {margin:0;color:#8A96A4;font-size:10px;line-height:1.6;text-align:center}
.seller-voucher-select {appearance:none;-webkit-appearance:none;cursor:pointer;padding-right:38px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2'%3E%3Cpath d='m7 10 5 5 5-5'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;background-size:17px}
/* The enhancement leaves native select functional when JS is unavailable. */
.seller-voucher-discount-dropdown {position:relative}
.seller-voucher-discount-dropdown.is-enhanced .seller-voucher-select {display:none}
.seller-voucher-discount-trigger {display:flex;width:100%;min-height:48px;align-items:center;justify-content:space-between;gap:9px;padding:0 14px;border:1px solid #DCE2E8;border-radius:12px;background:#FFF;color:#344054;font-size:11.5px;font-weight:600;cursor:pointer;text-align:left}
.seller-voucher-discount-trigger:hover {border-color:#C9D1DB;background:#F8FAFC}
.seller-voucher-discount-trigger[aria-expanded="true"] {border-color:var(--voucher-gold);box-shadow:0 0 0 2px rgba(200,137,18,.10)}
.seller-voucher-discount-trigger svg {width:17px;height:17px;flex-shrink:0;color:#64748B}
.seller-voucher-discount-menu {position:absolute;top:calc(100% + 7px);left:0;right:0;z-index:120;padding:7px;border:1px solid #E2E8F0;border-radius:13px;background:#FFF;box-shadow:0 15px 34px rgba(15,23,42,.12),0 3px 8px rgba(15,23,42,.04)}
.seller-voucher-discount-menu[hidden], .seller-voucher-discount-trigger[hidden] {display:none!important}
.seller-voucher-discount-option {display:block;width:100%;min-height:39px;padding:9px 11px;border:0;border-radius:9px;background:transparent;color:#475569;text-align:left;font-size:11.5px;font-weight:600;cursor:pointer}
.seller-voucher-discount-option:hover, .seller-voucher-discount-option:focus-visible {outline:0;background:#F8FAFC;color:#A56D0A}
.seller-voucher-discount-option.is-selected {background:#F8FAFC;color:#293440;font-weight:750}
/* Flat, scannable voucher records */
.seller-voucher-list {display:grid;background:#FFF}
.seller-voucher-row {display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:18px;padding:19px 22px;border-bottom:1px solid #EDF0F3;background:#FFF}
.seller-voucher-row:last-child {border-bottom:0}
.seller-voucher-row:hover {background:#F8FAFC}
.seller-voucher-main {min-width:0}
.seller-voucher-heading {display:flex;flex-wrap:wrap;align-items:center;gap:8px}
.seller-voucher-code-badge {display:inline-flex;align-items:center;min-height:30px;padding:0 10px;border:1px solid #E5E7EB;border-radius:9px;background:#F8FAFC;color:#A56D0A;font-size:11px;font-weight:800;letter-spacing:.035em}
.seller-voucher-name {color:#293440;font-size:12.5px;font-weight:750;line-height:1.4;overflow-wrap:anywhere}
.seller-voucher-meta {display:flex;flex-wrap:wrap;align-items:center;gap:5px 9px;margin-top:9px;color:#8491A0;font-size:11px;line-height:1.5}
.seller-voucher-meta strong {color:#475569;font-weight:700}
.seller-voucher-meta-separator {color:#CBD5E1}
.seller-voucher-actions {display:flex;flex-wrap:wrap;align-items:center;justify-content:flex-end;gap:8px}
.seller-voucher-actions form {margin:0}
.seller-voucher-action-button svg {width:15px;height:15px}
.seller-voucher-action-button--toggle {color:#526071}
.seller-voucher-empty {display:grid;min-height:230px;place-items:center;padding:38px 22px;text-align:center;background:#FFF}
.seller-voucher-empty-icon {display:grid;place-items:center;width:74px;height:74px;margin-inline:auto;color:#94A3B8;background:transparent;border:0;border-radius:0}
.seller-voucher-empty-icon svg {width:48px;height:48px}
.seller-voucher-empty-title {margin-top:12px;color:#344054;font-size:17px;font-weight:750}
.seller-voucher-empty-copy {max-width:320px;margin:7px auto 0;color:#8A96A4;font-size:11.5px;line-height:1.65}
/* Paginator remains inside the primary white panel */
.seller-voucher-pagination {padding:14px 20px;border-top:1px solid var(--voucher-line);background:#FFF}
.seller-voucher-pagination nav {font-family:inherit;font-size:11px}
.seller-voucher-pagination nav :is(a,span) {border-color:#E2E8F0}
.seller-voucher-pagination nav [aria-current="page"] span {border-color:var(--voucher-gold)!important;background:var(--voucher-gold)!important;color:#FFF!important}
@media(max-width:1140px) {
    .seller-shipping-promo-grid {grid-template-columns:minmax(0,1fr) minmax(0,1fr)}
    .seller-vouchers-layout {grid-template-columns:minmax(300px,380px) minmax(0,1fr)}
    .seller-voucher-row {grid-template-columns:1fr;gap:12px}
    .seller-voucher-actions {justify-content:flex-start}
}
@media(max-width:900px) {
    .seller-shipping-promo-grid, .seller-vouchers-layout {grid-template-columns:1fr}
    .seller-voucher-row {grid-template-columns:minmax(0,1fr) auto}
    .seller-voucher-actions {justify-content:flex-end}
}
@media(max-width:640px) {
    .seller-vouchers-page {padding-bottom:24px}
    .seller-vouchers-eyebrow {font-size:9.5px}
    .seller-vouchers-title {font-size:27px}
    .seller-vouchers-subtitle {font-size:11px}
    .seller-shipping-promo-section {margin:18px 0}
    .seller-shipping-promo-head h2 {font-size:17px}
    .seller-shipping-promo-grid, .seller-vouchers-layout {gap:12px}
    .seller-shipping-promo-card {padding:17px}
    .seller-shipping-promo-fields {grid-template-columns:1fr;gap:12px}
    .seller-shipping-promo-item-head {flex-direction:column;gap:7px}
    .seller-voucher-panel-head {padding:17px;align-items:flex-start}
    .seller-voucher-panel-title {font-size:17px}
    .seller-voucher-form {padding:17px}
    .seller-voucher-field-grid {grid-template-columns:1fr}
    .seller-voucher-row {grid-template-columns:1fr;padding:17px;gap:12px}
    .seller-voucher-actions {justify-content:flex-start}
    .seller-voucher-action-button {min-height:40px}
    .seller-shipping-promo-submit {width:100%}
    .seller-voucher-pagination {padding:12px 14px}
}
@media(prefers-reduced-motion:reduce) {
    .seller-vouchers-page :is(button,input,select,a,summary) {transition:none}
}

@endverbatim
</style>
@endpush

@section('content')

<div class="seller-vouchers-page">

    @if(session('success'))

        <div class="seller-voucher-alert seller-voucher-alert--success">

            <span class="seller-voucher-alert-icon" aria-hidden="true">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">

                    <circle cx="12" cy="12" r="8"></circle>

                    <path d="m8 12 2.5 2.5L16 9"></path>

                </svg>

            </span>

            <div>{{ session('success') }}</div>

        </div>

    @endif

    @if($errors->any())

        <div class="seller-voucher-alert seller-voucher-alert--error">

            <span class="seller-voucher-alert-icon" aria-hidden="true">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">

                    <path d="M12 3 3 20h18L12 3Z"></path>

                    <path d="M12 9v5"></path>

                    <path d="M12 17h.01"></path>

                </svg>

            </span>

            <div>{{ $errors->first() }}</div>

        </div>

    @endif

    <header class="seller-vouchers-header">

        <p class="seller-vouchers-eyebrow">Marketing</p>

        <h1 class="seller-vouchers-title">

            <span>Promotions</span><span>& Vouchers</span>

        </h1>


    </header>

    <section class="seller-shipping-promo-section" aria-labelledby="sellerFreeShippingTitle">
        <div class="seller-shipping-promo-head">
            <div>
                <h2 id="sellerFreeShippingTitle">Free Shipping Promotions</h2>
                <p>Schedule seller-funded shipping discounts. Times are interpreted in <span class="seller-shipping-promo-tz">Asia/Manila</span>.</p>
            </div>
        </div>

        <div class="seller-shipping-promo-grid">
            <div class="seller-shipping-promo-card">
                <h3>Create promotion</h3>
                <p class="seller-shipping-promo-note">Buyers qualify only while the promotion is active and their subtotal from your store reaches the minimum spend.</p>

                <form method="POST" action="{{ route('seller.free-shipping-promotions.store') }}">
                    @csrf
                    <div class="seller-shipping-promo-fields">
                        <div class="seller-shipping-promo-field">
                            <label for="shipping-minimum">Minimum order amount</label>
                            <input id="shipping-minimum" name="minimum_order_amount" type="number" min="0" step="0.01" required value="{{ old('minimum_order_amount') }}" placeholder="499.00">
                        </div>

                        <div class="seller-shipping-promo-field">
                            <label for="shipping-maximum">Maximum shipping discount</label>
                            <input id="shipping-maximum" name="maximum_shipping_discount" type="number" min="0.01" step="0.01" required value="{{ old('maximum_shipping_discount') }}" placeholder="80.00">
                        </div>

                        <div class="seller-shipping-promo-field">
                            <label for="shipping-start">Starts at</label>
                            <input id="shipping-start" name="starts_at" type="datetime-local" required value="{{ old('starts_at') }}">
                        </div>

                        <div class="seller-shipping-promo-field">
                            <label for="shipping-end">Ends at</label>
                            <input id="shipping-end" name="ends_at" type="datetime-local" required value="{{ old('ends_at') }}">
                        </div>
                    </div>

                    <button type="submit" class="seller-shipping-promo-submit">Create Free Shipping Promotion</button>
                </form>

                <div class="seller-shipping-promo-funder">
                    <strong>Funding:</strong> Seller-funded. The courier keeps the original delivery fee; only the Buyer-facing shipping charge is discounted. The applied discount is deducted from your settlement after delivery.
                </div>
            </div>

            <div class="seller-shipping-promo-card">
                <h3>Promotion history</h3>
                <p class="seller-shipping-promo-note">Only one active or scheduled promotion may overlap a given time period. Paused and ended promotions never apply at checkout.</p>

                @if(($freeShippingPromotions ?? collect())->isEmpty())
                    <div class="seller-shipping-promo-empty">No Free Shipping promotions created yet.</div>
                @else
                    <div class="seller-shipping-promo-list">
                        @foreach($freeShippingPromotions as $promotion)
                            @php
                                $displayStatus = $promotion->displayStatus();
                                $statusClass = 'is-' . strtolower($displayStatus);
                                $startsManila = $promotion->startsAtManila();
                                $endsManila = $promotion->endsAtManila();
                            @endphp

                            <article class="seller-shipping-promo-item">
                                <div class="seller-shipping-promo-item-head">
                                    <div>
                                        <div class="seller-shipping-promo-terms">
                                            Spend ₱{{ number_format((float) $promotion->minimum_order_amount, 2) }} · up to ₱{{ number_format((float) $promotion->maximum_shipping_discount, 2) }} shipping off
                                        </div>
                                        <div class="seller-shipping-promo-meta">
                                            {{ $startsManila?->format('M d, Y g:i A') }} → {{ $endsManila?->format('M d, Y g:i A') }} · Asia/Manila
                                        </div>
                                    </div>
                                    <span class="seller-shipping-promo-status {{ $statusClass }}">{{ $displayStatus }}</span>
                                </div>

                                @if($displayStatus !== 'Ended')
                                    <div class="seller-shipping-promo-actions">
                                        @if($promotion->status === \App\Models\Promotions\SellerFreeShippingPromotion::STATUS_PAUSED)
                                            <form method="POST" action="{{ route('seller.free-shipping-promotions.resume', $promotion) }}">
                                                @csrf
                                                <button type="submit" class="seller-shipping-promo-action">Resume</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('seller.free-shipping-promotions.pause', $promotion) }}">
                                                @csrf
                                                <button type="submit" class="seller-shipping-promo-action">Pause</button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('seller.free-shipping-promotions.end', $promotion) }}">
                                            @csrf
                                            <button type="submit" class="seller-shipping-promo-action is-danger">End promotion</button>
                                        </form>
                                    </div>

                                    <details class="seller-shipping-promo-edit">
                                        <summary>Edit settings</summary>
                                        <form method="POST" action="{{ route('seller.free-shipping-promotions.update', $promotion) }}" class="seller-shipping-promo-edit-form">
                                            @csrf
                                            @method('PATCH')
                                            <div class="seller-shipping-promo-fields">
                                                <div class="seller-shipping-promo-field">
                                                    <label>Minimum order amount</label>
                                                    <input name="minimum_order_amount" type="number" min="0" step="0.01" required value="{{ number_format((float) $promotion->minimum_order_amount, 2, '.', '') }}">
                                                </div>
                                                <div class="seller-shipping-promo-field">
                                                    <label>Maximum shipping discount</label>
                                                    <input name="maximum_shipping_discount" type="number" min="0.01" step="0.01" required value="{{ number_format((float) $promotion->maximum_shipping_discount, 2, '.', '') }}">
                                                </div>
                                                <div class="seller-shipping-promo-field">
                                                    <label>Starts at</label>
                                                    <input name="starts_at" type="datetime-local" required value="{{ $startsManila?->format('Y-m-d\\TH:i') }}">
                                                </div>
                                                <div class="seller-shipping-promo-field">
                                                    <label>Ends at</label>
                                                    <input name="ends_at" type="datetime-local" required value="{{ $endsManila?->format('Y-m-d\\TH:i') }}">
                                                </div>
                                            </div>
                                            <button type="submit" class="seller-shipping-promo-submit">Save changes</button>
                                        </form>
                                    </details>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <div class="seller-vouchers-layout">

        <section class="seller-voucher-panel">

            <div class="seller-voucher-panel-head">

                <div class="seller-voucher-panel-title-group">

                    <span class="seller-voucher-panel-icon" aria-hidden="true">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                            <path d="M4 7h16v10H4z"></path>

                            <path d="M8 7V5h8v2"></path>

                            <path d="M12 10v4"></path>

                            <path d="M10 12h4"></path>

                        </svg>

                    </span>

                    <div>

                        <h2 class="seller-voucher-panel-title">Create Voucher</h2>

                        <p class="seller-voucher-panel-subtitle">Configure a checkout discount campaign.</p>

                    </div>

                </div>

            </div>

            <form method="POST" action="{{ route('seller.vouchers.store') }}" class="seller-voucher-form">

                @csrf

                <div class="seller-voucher-form-grid">

                    <div class="seller-voucher-field">

                        <label class="seller-voucher-label" for="voucher-code">Voucher code</label>

                        <input

                            id="voucher-code"

                            name="code"

                            required

                            value="{{ old('code') }}"

                            placeholder="e.g. SARI10"

                            autocomplete="off"

                            class="seller-voucher-input seller-voucher-code"

                        >

                    </div>

                    <div class="seller-voucher-field">

                        <label class="seller-voucher-label" for="voucher-name">Campaign name</label>

                        <input

                            id="voucher-name"

                            name="name"

                            required

                            value="{{ old('name') }}"

                            placeholder="e.g. October Payday Sale"

                            class="seller-voucher-input"

                        >

                    </div>

                    <div class="seller-voucher-form-divider"></div>

                    <div class="seller-voucher-section-label">Discount</div>

                    <div class="seller-voucher-field-grid">

                        <div class="seller-voucher-field">

                            <label class="seller-voucher-label" for="voucher-discount-type">Discount type</label>

                            
                            <div class="seller-voucher-discount-dropdown" data-voucher-discount>
                                <select

                                id="voucher-discount-type"

                                name="discount_type"

                                class="seller-voucher-select"

                            >

                                <option value="percentage" @selected(old('discount_type', 'percentage') === 'percentage')>

                                    Percentage

                                </option>

                                <option value="fixed" @selected(old('discount_type') === 'fixed')>

                                    Fixed Amount

                                </option>

                            </select>
                                <button type="button" class="seller-voucher-discount-trigger" data-voucher-discount-trigger aria-label="Discount type" aria-haspopup="listbox" aria-expanded="false" hidden>
                                    <span data-voucher-discount-label>Percentage</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
                                </button>
                                <div class="seller-voucher-discount-menu" role="listbox" aria-label="Discount type" data-voucher-discount-menu hidden>
                                    <button type="button" class="seller-voucher-discount-option" role="option" aria-selected="false" data-voucher-discount-option="percentage">Percentage</button>
                                    <button type="button" class="seller-voucher-discount-option" role="option" aria-selected="false" data-voucher-discount-option="fixed">Fixed Amount</button>
                                </div>
                            </div>

                        </div>

                        <div class="seller-voucher-field">

                            <label class="seller-voucher-label" for="voucher-discount-value">Discount value</label>

                            <input

                                id="voucher-discount-value"

                                name="discount_value"

                                type="number"

                                step=".01"

                                min=".01"

                                required

                                value="{{ old('discount_value') }}"

                                placeholder="Value"

                                class="seller-voucher-input"

                            >

                        </div>

                    </div>

                    <div class="seller-voucher-field-grid">

                        <div class="seller-voucher-field">

                            <label class="seller-voucher-label" for="voucher-minimum-spend">

                                Minimum spend <small>optional</small>

                            </label>

                            <input

                                id="voucher-minimum-spend"

                                name="minimum_spend"

                                type="number"

                                step=".01"

                                min="0"

                                value="{{ old('minimum_spend') }}"

                                placeholder="0.00"

                                class="seller-voucher-input"

                            >

                        </div>

                        <div class="seller-voucher-field">

                            <label class="seller-voucher-label" for="voucher-max-discount">

                                Max discount <small>optional</small>

                            </label>

                            <input

                                id="voucher-max-discount"

                                name="maximum_discount"

                                type="number"

                                step=".01"

                                min="0"

                                value="{{ old('maximum_discount') }}"

                                placeholder="0.00"

                                class="seller-voucher-input"

                            >

                        </div>

                    </div>

                    <div class="seller-voucher-field">

                        <label class="seller-voucher-label" for="voucher-usage-limit">

                            Usage limit <small>optional</small>

                        </label>

                        <input

                            id="voucher-usage-limit"

                            name="usage_limit"

                            type="number"

                            min="1"

                            value="{{ old('usage_limit') }}"

                            placeholder="Unlimited if left blank"

                            class="seller-voucher-input"

                        >

                    </div>

                    <div class="seller-voucher-form-divider"></div>

                    <div class="seller-voucher-section-label">Schedule</div>

                    <div class="seller-voucher-field-grid">

                        <div class="seller-voucher-field">

                            <label class="seller-voucher-label" for="voucher-starts-at">

                                Starts at <small>optional</small>

                            </label>

                            <input

                                id="voucher-starts-at"

                                name="starts_at"

                                type="datetime-local"

                                value="{{ old('starts_at') }}"

                                class="seller-voucher-input"

                            >

                        </div>

                        <div class="seller-voucher-field">

                            <label class="seller-voucher-label" for="voucher-ends-at">

                                Ends at <small>optional</small>

                            </label>

                            <input

                                id="voucher-ends-at"

                                name="ends_at"

                                type="datetime-local"

                                value="{{ old('ends_at') }}"

                                class="seller-voucher-input"

                            >

                        </div>

                    </div>

                    <button type="submit" class="seller-voucher-submit">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">

                            <path d="M12 5v14"></path>

                            <path d="M5 12h14"></path>

                        </svg>

                        Create Voucher

                    </button>

                    <p class="seller-voucher-form-note">

                        Voucher rules are validated by the server before they become available at checkout.

                    </p>

                </div>

            </form>

        </section>

        <section class="seller-voucher-panel">

            <div class="seller-voucher-panel-head">

                <div class="seller-voucher-panel-title-group">

                    <span class="seller-voucher-panel-icon" aria-hidden="true">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                            <path d="M4 7h16v10H4z"></path>

                            <path d="M9 10h6"></path>

                            <path d="M9 14h4"></path>

                        </svg>

                    </span>

                    <div>

                        <h2 class="seller-voucher-panel-title">Seller Vouchers</h2>

                        <p class="seller-voucher-panel-subtitle">Manage active and inactive checkout campaigns.</p>

                    </div>

                </div>

                <span class="seller-voucher-count">

                    {{ number_format($vouchers->total()) }} total

                </span>

            </div>

            <div class="seller-voucher-list">

                @forelse($vouchers as $voucher)

                    <article class="seller-voucher-row">

                        <div class="seller-voucher-main">

                            <div class="seller-voucher-heading">

                                <span class="seller-voucher-code-badge">{{ $voucher->code }}</span>

                                <span class="seller-voucher-name">{{ $voucher->name }}</span>

                                <span class="seller-voucher-status {{ $voucher->is_active ? 'seller-voucher-status--active' : 'seller-voucher-status--inactive' }}">

                                    {{ $voucher->is_active ? 'Active' : 'Inactive' }}

                                </span>

                            </div>

                            <div class="seller-voucher-meta">

                                <span>

                                    <strong>

                                        {{ $voucher->discount_type === 'percentage'

                                            ? rtrim(rtrim(number_format((float) $voucher->discount_value, 2, '.', ''), '0'), '.') . '%'

                                            : '₱' . number_format((float) $voucher->discount_value, 2) }}

                                    </strong>

                                    off

                                </span>

                                <span class="seller-voucher-meta-separator">•</span>

                                <span>

                                    Used

                                    <strong>

                                        {{ number_format((int) $voucher->used_count) }}{{ $voucher->usage_limit ? '/'.number_format((int) $voucher->usage_limit) : '' }}

                                    </strong>

                                </span>

                            </div>

                        </div>

                        <div class="seller-voucher-actions">

                            <form method="POST" action="{{ route('seller.vouchers.toggle', $voucher) }}">

                                @csrf

                                <button type="submit" class="seller-voucher-action-button seller-voucher-action-button--toggle">

                                    @if($voucher->is_active)

                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">

                                            <circle cx="12" cy="12" r="8"></circle>

                                            <path d="M8 12h8"></path>

                                        </svg>

                                        Deactivate

                                    @else

                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">

                                            <circle cx="12" cy="12" r="8"></circle>

                                            <path d="m8 12 2.5 2.5L16 9"></path>

                                        </svg>

                                        Activate

                                    @endif

                                </button>

                            </form>

                            <form method="POST" action="{{ route('seller.vouchers.destroy', $voucher) }}">

                                @csrf

                                @method('DELETE')

                                <button type="submit" class="seller-voucher-action-button seller-voucher-action-button--delete">

                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">

                                        <path d="M5 7h14"></path>

                                        <path d="M9 7V5h6v2"></path>

                                        <path d="M8 10v7"></path>

                                        <path d="M12 10v7"></path>

                                        <path d="M16 10v7"></path>

                                        <path d="M7 7l1 12h8l1-12"></path>

                                    </svg>

                                    Delete

                                </button>

                            </form>

                        </div>

                    </article>

                @empty

                    <div class="seller-voucher-empty">

                        <div>

                            <span class="seller-voucher-empty-icon" aria-hidden="true">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                    <path d="M4 7h16v10H4z"></path>

                                    <path d="M8 7V5h8v2"></path>

                                    <path d="M12 10v4"></path>

                                    <path d="M10 12h4"></path>

                                </svg>

                            </span>

                            <div class="seller-voucher-empty-title">No vouchers created yet</div>

                            <div class="seller-voucher-empty-copy">

                                Create your first campaign using the voucher form.

                            </div>

                        </div>

                    </div>

                @endforelse

            </div>

            @if($vouchers->hasPages())

                <div class="seller-voucher-pagination">

                    {{ $vouchers->links() }}

                </div>

            @endif

        </section>

    </div>

</div>

@endsection

@push('scripts')
<script>
(() => {
    const root = document.querySelector('[data-voucher-discount]');
    if (!root || root.dataset.initialized === '1') return;
    const native = root.querySelector('select[name="discount_type"]');
    const trigger = root.querySelector('[data-voucher-discount-trigger]');
    const label = root.querySelector('[data-voucher-discount-label]');
    const menu = root.querySelector('[data-voucher-discount-menu]');
    const options = Array.from(root.querySelectorAll('[data-voucher-discount-option]'));
    if (!native || !trigger || !label || !menu || !options.length) return;
    const controller = new AbortController();
    const {signal} = controller;
    const sync = () => {
        label.textContent = native.selectedOptions[0]?.textContent?.trim() || 'Percentage';
        options.forEach(opt => {
            const selected = opt.dataset.voucherDiscountOption === native.value;
            opt.classList.toggle('is-selected', selected);
            opt.setAttribute('aria-selected', String(selected));
        });
    };
    const close = () => {
        menu.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
    };
    const open = () => {
        menu.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
    };
    root.dataset.initialized = '1';
    sync();
    trigger.hidden = false;
    root.classList.add('is-enhanced');
    trigger.addEventListener('click', () => menu.hidden ? open() : close(), {signal});
    options.forEach(option => option.addEventListener('click', () => {
        native.value = option.dataset.voucherDiscountOption;
        native.dispatchEvent(new Event('change', {bubbles:true}));
        sync();
        close();
        trigger.focus();
    }, {signal}));
    native.addEventListener('change', sync, {signal});
    document.addEventListener('pointerdown', event => {
        if (!root.contains(event.target)) close();
    }, {signal});
    root.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !menu.hidden) {
            close();
            trigger.focus();
        } else if (!menu.hidden && ['ArrowDown','ArrowUp'].includes(event.key)) {
            event.preventDefault();
            const focused = options.indexOf(document.activeElement);
            options[(focused + (event.key === 'ArrowDown' ? 1 : -1) + options.length) % options.length].focus();
        } else if (!menu.hidden && event.key === 'Tab') {
            close();
        }
    }, {signal});
    window.addEventListener('beforeunload', () => controller.abort(), {once:true,signal});
    document.addEventListener('livewire:navigating', () => controller.abort(), {once:true,signal});
})();
</script>
@endpush
