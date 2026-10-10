@extends('layouts.seller')

@section('title', 'Returns & Refunds — SARI Seller')

@section('page-title', 'Returns & Refunds')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/returns.css') }}?v=20261007-1">



@endpush

@section('content')

@php

    $returnStatusClass = fn (?string $value) => match ($value) {

        'requested' => 'seller-return-status--requested',

        'approved' => 'seller-return-status--approved',

        'returned' => 'seller-return-status--returned',

        'refunded' => 'seller-return-status--refunded',

        'rejected' => 'seller-return-status--rejected',

        default => 'seller-return-status--requested',

    };

    $summaryCards = [

        [

            'label' => 'All Returns',

            'value' => (int) $stats->total,

            'note' => 'Complete return history',

            'icon' => 'all',

        ],

        [

            'label' => 'Requested',

            'value' => (int) $stats->requested,

            'note' => 'Waiting for Seller review',

            'icon' => 'requested',

        ],

        [

            'label' => 'Approved',

            'value' => (int) $stats->approved,

            'note' => 'Authorized for return',

            'icon' => 'approved',

        ],

        [

            'label' => 'Refunded',

            'value' => (int) $stats->refunded,

            'note' => 'Refund ledger recorded',

            'icon' => 'refunded',

        ],

    ];

    $statusTabs = [

        'all' => 'All',

        'requested' => 'Requested',

        'approved' => 'Approved',

        'returned' => 'Returned',

        'refunded' => 'Refunded',

        'rejected' => 'Rejected',

    ];

@endphp

<div class="seller-returns-page">

    @if(session('success'))

        <div class="seller-returns-alert seller-returns-alert--success">

            <span class="seller-returns-alert-icon" aria-hidden="true">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">

                    <circle cx="12" cy="12" r="8"></circle>

                    <path d="m8 12 2.5 2.5L16 9"></path>

                </svg>

            </span>

            <div>{{ session('success') }}</div>

        </div>

    @endif

    @if($errors->any())

        <div class="seller-returns-alert seller-returns-alert--error">

            <span class="seller-returns-alert-icon" aria-hidden="true">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">

                    <path d="M12 3 3 20h18L12 3Z"></path>

                    <path d="M12 9v5"></path>

                    <path d="M12 17h.01"></path>

                </svg>

            </span>

            <div>{{ $errors->first() }}</div>

        </div>

    @endif

    <header class="seller-returns-header">

        <p class="seller-returns-eyebrow">Orders & Fulfillment</p>

        <h1 class="seller-returns-title">

            <span>Returns</span><span>& Refunds</span>

        </h1>

        <p class="seller-returns-subtitle">

            Review Buyer return requests, record returned parcels, and post internal refund ledger entries.

        </p>

    </header>

    <section class="seller-returns-summary" aria-label="Return statistics">

        @foreach($summaryCards as $card)

            <article class="seller-returns-stat">

                <div class="seller-returns-stat-inner">

                    <div class="min-w-0">

                        <div class="seller-returns-stat-label">{{ $card['label'] }}</div>

                        <div class="seller-returns-stat-value">{{ number_format($card['value']) }}</div>

                        <div class="seller-returns-stat-note">{{ $card['note'] }}</div>

                    </div>

                    <span class="seller-returns-stat-icon seller-returns-stat-icon--{{ $card['icon'] }}" aria-hidden="true">

                        @if($card['icon'] === 'all')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <path d="M5 5h14v14H5z"></path>

                                <path d="M8 9h8"></path>

                                <path d="M8 13h8"></path>

                            </svg>

                        @elseif($card['icon'] === 'requested')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <circle cx="12" cy="12" r="8"></circle>

                                <path d="M12 8v4l3 2"></path>

                            </svg>

                        @elseif($card['icon'] === 'approved')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <path d="M5 5h14v14H5z"></path>

                                <path d="m8 12 2.5 2.5L16 9"></path>

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

    <section class="seller-returns-filter-panel" aria-label="Return filters">

        <form method="GET" action="{{ route('seller.returns.index') }}" class="seller-returns-filter-form">

            <label class="seller-returns-search-field">

                <span class="sr-only">Search return requests</span>

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">

                    <circle cx="11" cy="11" r="7"></circle>

                    <path d="m20 20-4-4"></path>

                </svg>

                <input

                    type="search"

                    name="q"

                    value="{{ $search }}"

                    placeholder="Search order ID, buyer, reason..."

                    autocomplete="off"

                    class="seller-returns-search-input"

                >

            </label>

            <div class="seller-returns-status-dropdown" data-returns-status-dropdown>

                <select

                    name="status"

                    class="seller-returns-status-native"

                    aria-hidden="true"

                    tabindex="-1"

                    data-returns-status-native

                >

                    @foreach($statusTabs as $tab => $label)

                        <option value="{{ $tab }}" @selected($status === $tab)>{{ $label }} Status</option>

                    @endforeach

                </select>

                <button

                    type="button"

                    class="seller-returns-status-button"

                    aria-haspopup="listbox"

                    aria-expanded="false"

                    data-returns-status-button

                >

                    <span class="seller-returns-status-button-main">

                        <span class="seller-returns-status-button-icon" aria-hidden="true">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <path d="M4 7h16"></path>

                                <path d="M7 12h10"></path>

                                <path d="M10 17h4"></path>

                            </svg>

                        </span>

                        <span data-returns-status-label>All Status</span>

                    </span>

                    <svg class="seller-returns-status-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">

                        <path d="m7 10 5 5 5-5"></path>

                    </svg>

                </button>

                <div class="seller-returns-status-menu" role="listbox" hidden data-returns-status-menu>

                    @foreach([

                        ['all', 'All Status', 'Show every return request'],

                        ['requested', 'Requested', 'Waiting for Seller review'],

                        ['approved', 'Approved', 'Authorized for return'],

                        ['returned', 'Returned', 'Parcel received by Seller'],

                        ['refunded', 'Refunded', 'Refund ledger recorded'],

                        ['rejected', 'Rejected', 'Declined return requests'],

                    ] as [$value, $label, $description])

                        <button

                            type="button"

                            class="seller-returns-status-option"

                            data-returns-status-value="{{ $value }}"

                        >

                            <span class="seller-returns-status-option-copy">

                                <strong>{{ $label }}</strong>

                                <small>{{ $description }}</small>

                            </span>

                            <svg class="seller-returns-status-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">

                                <path d="m6 12 4 4 8-8"></path>

                            </svg>

                        </button>

                    @endforeach

                </div>

            </div>

            <button type="submit" class="seller-returns-apply-filter">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">

                    <path d="M4 7h16"></path>

                    <path d="M7 12h10"></path>

                    <path d="M10 17h4"></path>

                </svg>

                Apply Filter

            </button>

            <a href="{{ route('seller.returns.index') }}" class="seller-returns-reset-filter">

                Reset

            </a>

        </form>

    </section>

    <section class="seller-returns-list">

        @forelse($returns as $return)

            <article class="seller-return-card">

                <div class="seller-return-card-main">

                    <div class="min-w-0">

                        <div class="seller-return-order-row">

                            <p class="seller-return-order-number">

                                {{ $return->order?->order_number ?: 'Order unavailable' }}

                            </p>

                            <span class="seller-return-status {{ $returnStatusClass($return->status) }}">

                                {{ ucfirst($return->status) }}

                            </span>

                        </div>

                        <p class="seller-return-reason">{{ $return->reason }}</p>

                        <p class="seller-return-details">

                            {{ $return->details ?: 'No additional details.' }}

                        </p>

                    </div>

                    <div class="seller-return-amount">

                        <div class="seller-return-amount-label">Requested refund</div>

                        <div class="seller-return-amount-value">

                            ₱{{ number_format((float) $return->requested_amount, 2) }}

                        </div>

                    </div>

                </div>

                @if($return->status === 'requested')

                    <div class="seller-return-action">

                        <form

                            method="POST"

                            action="{{ route('seller.returns.review', $return) }}"

                            class="seller-return-review-form"

                        >

                            @csrf

                            <input

                                name="seller_response"

                                required

                                placeholder="Seller response / decision notes"

                                class="seller-return-input"

                            >

                            <input

                                name="approved_amount"

                                type="number"

                                step=".01"

                                min="0"

                                max="{{ $return->requested_amount }}"

                                value="{{ $return->requested_amount }}"

                                class="seller-return-input"

                                aria-label="Approved refund amount"

                            >

                            <select

                                name="decision"

                                class="seller-return-select"

                                aria-label="Return decision"

                            >

                                <option value="approve">Approve</option>

                                <option value="reject">Reject</option>

                            </select>

                            <button type="submit" class="seller-return-button seller-return-button--primary">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">

                                    <path d="m6 12 4 4 8-8"></path>

                                </svg>

                                Submit Review

                            </button>

                        </form>

                    </div>

                @elseif($return->status === 'approved')

                    <div class="seller-return-action">

                        <form method="POST" action="{{ route('seller.returns.returned', $return) }}">

                            @csrf

                            <button type="submit" class="seller-return-button seller-return-button--primary">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">

                                    <path d="M4 7h16"></path>

                                    <path d="M6 7v12h12V7"></path>

                                    <path d="M9 11h6"></path>

                                </svg>

                                Confirm Returned Item Received

                            </button>

                        </form>

                    </div>

                @elseif($return->status === 'returned')

                    <div class="seller-return-action">

                        <form method="POST" action="{{ route('seller.returns.refund', $return) }}">

                            @csrf

                            <button type="submit" class="seller-return-button seller-return-button--success">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">

                                    <path d="M6 5h12v14H6z"></path>

                                    <path d="M9 9h6"></path>

                                    <path d="m9 13 2 2 4-4"></path>

                                </svg>

                                Record Refund

                            </button>

                            <p class="seller-return-action-note">

                                Records an internal refund ledger transaction only.

                            </p>

                        </form>

                    </div>

                @elseif($return->seller_response)

                    <div class="seller-return-action">

                        <div class="seller-return-response">

                            {{ $return->seller_response }}

                        </div>

                    </div>

                @endif

            </article>

        @empty

            <div class="seller-returns-empty">

                <div>

                    <span class="seller-returns-empty-icon" aria-hidden="true">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                            <path d="M5 7h14v12H5z"></path>

                            <path d="m9 11-3 3 3 3"></path>

                            <path d="M6 14h7a4 4 0 0 0 4-4V8"></path>

                        </svg>

                    </span>

                    <div class="seller-returns-empty-title">No return requests found</div>

                    <div class="seller-returns-empty-copy">

                        Return requests matching the selected status will appear here.

                    </div>

                </div>

            </div>

        @endforelse

    </section>

    @if($returns->hasPages())

        <div class="seller-returns-pagination">

            {{ $returns->links() }}

        </div>

    @endif

</div>

@endsection

@push('scripts')

<script>

(function () {

    window.__SARI_SELLER_RETURNS_LIFECYCLE__?.abort();

    const lifecycle = new AbortController();
    const { signal } = lifecycle;

    window.__SARI_SELLER_RETURNS_LIFECYCLE__ = lifecycle;

    function initReturnsStatusDropdown() {

        document.querySelectorAll('[data-returns-status-dropdown]').forEach((root) => {

            if (root.dataset.dropdownReady === '1') return;

            root.dataset.dropdownReady = '1';

            const nativeSelect = root.querySelector('[data-returns-status-native]');

            const button = root.querySelector('[data-returns-status-button]');

            const label = root.querySelector('[data-returns-status-label]');

            const menu = root.querySelector('[data-returns-status-menu]');

            const options = Array.from(root.querySelectorAll('[data-returns-status-value]'));

            if (!nativeSelect || !button || !label || !menu || !options.length) return;

            const getLabel = (value) => {

                const selected = Array.from(nativeSelect.options).find((option) => option.value === value);

                return selected?.textContent?.trim() || 'All Status';

            };

            const sync = (value) => {

                nativeSelect.value = value;

                label.textContent = getLabel(value);

                options.forEach((option) => {

                    const selected = (option.dataset.returnsStatusValue || '') === value;

                    option.classList.toggle('is-selected', selected);

                    option.setAttribute('aria-selected', selected ? 'true' : 'false');

                });

            };

            const close = () => {

                menu.hidden = true;

                button.setAttribute('aria-expanded', 'false');

            };

            const open = () => {

                menu.hidden = false;

                button.setAttribute('aria-expanded', 'true');

            };

            button.addEventListener('click', (event) => {

                event.preventDefault();

                event.stopPropagation();

                menu.hidden ? open() : close();

            });

            options.forEach((option) => {

                option.addEventListener('click', () => {

                    sync(option.dataset.returnsStatusValue || 'all');

                    close();

                    button.focus();

                });

            });

            document.addEventListener('click', (event) => {

                if (!root.contains(event.target)) close();

            }, { signal });

            document.addEventListener('keydown', (event) => {

                if (event.key === 'Escape' && !menu.hidden) {

                    close();

                    button.focus();

                }

            }, { signal });

            sync(nativeSelect.value || 'all');

        });

    }

    if (window.__SARI_SELLER_AFTER_PAINT__) {

        window.__SARI_SELLER_AFTER_PAINT__(initReturnsStatusDropdown);

    } else if (document.readyState === 'loading') {

        document.addEventListener('DOMContentLoaded', initReturnsStatusDropdown, { once: true, signal });

    } else {

        initReturnsStatusDropdown();

    }

    const cleanup = () => {
        lifecycle.abort();

        if (window.__SARI_SELLER_RETURNS_LIFECYCLE__ === lifecycle) {
            window.__SARI_SELLER_RETURNS_LIFECYCLE__ = null;
        }
    };

    document.addEventListener('livewire:navigating', cleanup, { once: true, signal });
    window.addEventListener('beforeunload', cleanup, { once: true, signal });

})();

</script>

@endpush

