@extends('layouts.seller')

@section('title', 'Promotions & Vouchers — SARI Seller')

@section('page-title', 'Promotions & Vouchers')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/vouchers.css') }}?v=20261007-1">


<style>
.seller-shipping-promo-section{margin:0 0 22px}.seller-shipping-promo-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:12px}.seller-shipping-promo-head h2{margin:0;color:#252a31;font-size:15px;font-weight:700}.seller-shipping-promo-head p{margin:4px 0 0;color:#858d97;font-size:8px;line-height:1.5}.seller-shipping-promo-grid{display:grid;grid-template-columns:minmax(0,.9fr) minmax(0,1.1fr);gap:14px}.seller-shipping-promo-card{border:1px solid #e5e8ec;border-radius:14px;background:#fff;padding:16px;box-shadow:0 5px 16px rgba(26,34,44,.035)}.seller-shipping-promo-card h3{margin:0;color:#303740;font-size:11px;font-weight:700}.seller-shipping-promo-note{margin-top:5px;color:#8b939d;font-size:7.5px;line-height:1.5}.seller-shipping-promo-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:14px}.seller-shipping-promo-field{min-width:0}.seller-shipping-promo-field label{display:block;margin-bottom:5px;color:#58616c;font-size:7.5px;font-weight:650}.seller-shipping-promo-field input{width:100%;height:38px;border:1px solid #dfe4e9;border-radius:9px;background:#fff;padding:0 10px;color:#343b44;font-size:8.5px;outline:none}.seller-shipping-promo-field input:focus{border-color:#c88912;box-shadow:0 0 0 3px rgba(200,137,18,.07)}.seller-shipping-promo-submit{display:inline-flex;height:38px;align-items:center;justify-content:center;margin-top:12px;border:0;border-radius:9px;background:#c88912;padding:0 14px;color:#fff;font-size:8.5px;font-weight:700;cursor:pointer}.seller-shipping-promo-funder{margin-top:10px;padding:9px 10px;border:1px solid #e5e8ec;border-radius:9px;background:#f8f9fa;color:#6f7883;font-size:7.4px;line-height:1.5}.seller-shipping-promo-list{display:grid;gap:9px;margin-top:13px}.seller-shipping-promo-item{border:1px solid #e5e8ec;border-radius:11px;background:#fafbfc;padding:12px}.seller-shipping-promo-item-head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px}.seller-shipping-promo-terms{color:#343b44;font-size:9px;font-weight:700}.seller-shipping-promo-meta{margin-top:4px;color:#89929d;font-size:7.2px;line-height:1.5}.seller-shipping-promo-status{display:inline-flex;min-height:23px;align-items:center;border-radius:999px;padding:0 8px;font-size:6.8px;font-weight:750}.seller-shipping-promo-status.is-active{background:#edf8f1;color:#3f7e5a}.seller-shipping-promo-status.is-scheduled{background:#fff7e8;color:#9a6815}.seller-shipping-promo-status.is-paused{background:#f2f3f5;color:#727b86}.seller-shipping-promo-status.is-ended{background:#fff1f1;color:#a65a5a}.seller-shipping-promo-actions{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px}.seller-shipping-promo-actions form{margin:0}.seller-shipping-promo-action,.seller-shipping-promo-edit summary{display:inline-flex;min-height:30px;align-items:center;justify-content:center;border:1px solid #dfe4e9;border-radius:8px;background:#fff;padding:0 9px;color:#5e6874;font-size:7.2px;font-weight:650;cursor:pointer;list-style:none}.seller-shipping-promo-action.is-danger{border-color:#efcece;color:#a65353}.seller-shipping-promo-edit{margin-top:9px}.seller-shipping-promo-edit summary::-webkit-details-marker{display:none}.seller-shipping-promo-edit[open] summary{border-color:#d6af62;color:#97630e}.seller-shipping-promo-edit-form{margin-top:9px;padding-top:10px;border-top:1px solid #e8ebef}.seller-shipping-promo-empty{margin-top:13px;border:1px dashed #d9dee5;border-radius:10px;padding:18px;text-align:center;color:#8b939d;font-size:8px}.seller-shipping-promo-tz{color:#a06b12;font-weight:650}@media(max-width:900px){.seller-shipping-promo-grid{grid-template-columns:1fr}}@media(max-width:600px){.seller-shipping-promo-fields{grid-template-columns:1fr}}
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

        <p class="seller-vouchers-subtitle">

            Create server-validated Seller vouchers that Buyers can apply during checkout.

        </p>

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
