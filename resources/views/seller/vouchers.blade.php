@extends('layouts.seller')

@section('title', 'Promotions & Vouchers — SARI Seller')

@section('page-title', 'Promotions & Vouchers')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/vouchers.css') }}?v=20261007-1">



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
