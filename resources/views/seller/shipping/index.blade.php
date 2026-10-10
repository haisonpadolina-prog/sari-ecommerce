@extends('layouts.seller')

@section('title', 'Shipping — SARI Seller')

@section('page-title', 'Shipping')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/shipping.css') }}?v=20261007-1">



@endpush

@section('content')

@php

    $toneClasses = [

        'success' => 'border-[#cfe4d7] bg-[#f1f8f4] text-[#4f7d63]',

        'info' => 'border-[#cfdeea] bg-[#f1f7fb] text-[#3c6e91]',

        'hub' => 'border-[#d8d8ea] bg-[#f5f5fb] text-[#63638d]',

        'warning' => 'border-[#eadfc9] bg-[#fff9ef] text-[#a8731f]',

    ];

@endphp

<div class="seller-shipping-page">

    <section class="seller-shipping-header">

        <div class="seller-shipping-heading">

            <p class="seller-shipping-eyebrow">

                Fulfillment & Logistics

            </p>

            <h1 class="seller-shipping-title">Shipping <span>Tracking</span></h1>

            <p class="seller-shipping-subtitle">

                Follow Seller parcels from pickup verification to Logistics hub intake,

                sorting, final delivery, and completion.

            </p>

        </div>

    </section>

    <section class="seller-shipping-summary">

        @foreach([

            ['Active Shipments', $stats['active'], 'Truck in fulfillment', 'text-[#b97805]', 'bg-[#fff7e6]', 'truck'],

            ['Awaiting Pickup', $stats['awaiting_pickup'], 'Seller / Rider handoff', 'text-[#a8731f]', 'bg-[#fff9ef]', 'package'],

            ['In Transit', $stats['in_transit'], 'Hub and final-mile movement', 'text-[#3c6e91]', 'bg-[#f1f7fb]', 'route'],

            ['Delivered', $stats['delivered'], 'Completed shipments', 'text-[#4f7d63]', 'bg-[#f1f8f4]', 'check'],

        ] as [$label, $value, $caption, $textTone, $iconTone, $icon])

            <article class="seller-shipping-stat">

                <div class="flex items-start justify-between gap-3">

                    <div>

                        <p class="text-[9px] font-semibold text-[#7b8089]">{{ $label }}</p>

                        <p class="mt-2 text-[25px] font-semibold tracking-[-.04em] text-[#202124]">

                            {{ number_format($value) }}

                        </p>

                        <p class="mt-2 text-[8px] leading-4 text-[#9aa0a6]">{{ $caption }}</p>

                    </div>

                    <span class="seller-shipping-stat-icon {{ $iconTone }} {{ $textTone }}" aria-hidden="true">

                        @if ($icon === 'truck')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <path d="M3 7h11v10H3z"></path>

                                <path d="M14 10h3l4 4v3h-7z"></path>

                                <circle cx="7" cy="18" r="1.5"></circle>

                                <circle cx="18" cy="18" r="1.5"></circle>

                            </svg>

                        @elseif ($icon === 'package')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <path d="m4 7 8-4 8 4-8 4-8-4Z"></path>

                                <path d="M4 7v10l8 4 8-4V7"></path>

                                <path d="M12 11v10"></path>

                            </svg>

                        @elseif ($icon === 'route')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <circle cx="6" cy="18" r="2"></circle>

                                <circle cx="18" cy="6" r="2"></circle>

                                <path d="M8 18h3a3 3 0 0 0 3-3v-3a3 3 0 0 1 3-3h1"></path>

                                <path d="m15 6 3-3 3 3"></path>

                            </svg>

                        @else

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <circle cx="12" cy="12" r="9"></circle>

                                <path d="m8 12 2.5 2.5L16 9"></path>

                            </svg>

                        @endif

                    </span>

                </div>

            </article>

        @endforeach

    </section>

    <section class="seller-shipping-filter-panel" aria-label="Shipment filters">

        <form method="GET" action="{{ route('seller.shipping.index') }}" class="seller-shipping-filter-form">

            <label class="seller-shipping-search-field">

                <span class="sr-only">Search shipments</span>

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">

                    <circle cx="11" cy="11" r="7"></circle>

                    <path d="m20 20-4-4"></path>

                </svg>

                <input

                    type="search"

                    name="q"

                    value="{{ $search }}"

                    placeholder="Search order ID, buyer, product..."

                    autocomplete="off"

                    class="seller-shipping-search-input"

                >

            </label>

            <div class="seller-shipping-status-dropdown" data-shipping-status-dropdown>

                <select

                    name="status"

                    class="seller-shipping-status-native"

                    aria-hidden="true"

                    tabindex="-1"

                    data-shipping-status-native

                >

                    <option value="all" @selected($filter === 'all')>All Status</option>

                    <option value="awaiting_pickup" @selected($filter === 'awaiting_pickup')>Awaiting Pickup</option>

                    <option value="in_transit" @selected($filter === 'in_transit')>In Transit</option>

                    <option value="delivered" @selected($filter === 'delivered')>Delivered</option>

                </select>

                <button

                    type="button"

                    class="seller-shipping-status-button"

                    aria-haspopup="listbox"

                    aria-expanded="false"

                    data-shipping-status-button

                >

                    <span class="seller-shipping-status-button-main">

                        <span class="seller-shipping-status-button-icon" aria-hidden="true">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <path d="M4 7h16"></path>

                                <path d="M7 12h10"></path>

                                <path d="M10 17h4"></path>

                            </svg>

                        </span>

                        <span data-shipping-status-label>All Status</span>

                    </span>

                    <svg class="seller-shipping-status-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">

                        <path d="m7 10 5 5 5-5"></path>

                    </svg>

                </button>

                <div

                    class="seller-shipping-status-menu"

                    role="listbox"

                    hidden

                    data-shipping-status-menu

                >

                    <button type="button" class="seller-shipping-status-option" data-shipping-status-value="all">

                        <span class="seller-shipping-status-option-copy">

                            <strong>All Status</strong>

                            <small>Show every shipment</small>

                        </span>

                        <svg class="seller-shipping-status-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">

                            <path d="m6 12 4 4 8-8"></path>

                        </svg>

                    </button>

                    <button type="button" class="seller-shipping-status-option" data-shipping-status-value="awaiting_pickup">

                        <span class="seller-shipping-status-option-copy">

                            <strong>Awaiting Pickup</strong>

                            <small>Seller and rider handoff</small>

                        </span>

                        <svg class="seller-shipping-status-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">

                            <path d="m6 12 4 4 8-8"></path>

                        </svg>

                    </button>

                    <button type="button" class="seller-shipping-status-option" data-shipping-status-value="in_transit">

                        <span class="seller-shipping-status-option-copy">

                            <strong>In Transit</strong>

                            <small>Moving through delivery</small>

                        </span>

                        <svg class="seller-shipping-status-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">

                            <path d="m6 12 4 4 8-8"></path>

                        </svg>

                    </button>

                    <button type="button" class="seller-shipping-status-option" data-shipping-status-value="delivered">

                        <span class="seller-shipping-status-option-copy">

                            <strong>Delivered</strong>

                            <small>Completed shipments</small>

                        </span>

                        <svg class="seller-shipping-status-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">

                            <path d="m6 12 4 4 8-8"></path>

                        </svg>

                    </button>

                </div>

            </div>

            <button type="submit" class="seller-shipping-apply-filter">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">

                    <path d="M4 7h16"></path>

                    <path d="M7 12h10"></path>

                    <path d="M10 17h4"></path>

                </svg>

                Apply Filter

            </button>

            <a href="{{ route('seller.shipping.index') }}" class="seller-shipping-reset-filter">

                Reset

            </a>

        </form>

    </section>

    <section class="seller-shipping-workspace">

        <div class="seller-shipping-list">

            @forelse($orders as $order)

                @php

                    $state = $shippingStates[$order->id];

                    $tone = $toneClasses[$state['tone']] ?? $toneClasses['warning'];

                    $parcel = $order->logisticsParcel;

                    $itemCount = collect($order->items ?? [])->sum(fn ($item) => (int) ($item['qty'] ?? 1));

                @endphp

                <article class="seller-shipping-card">

                    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">

                        <div class="min-w-0 flex-1">

                            <div class="flex flex-wrap items-center gap-2">

                                <a

                                    href="{{ route('seller.shipping.show', $order) }}"

                                    class="seller-shipping-order-number"

                                >

                                    {{ $order->order_number }}

                                </a>

                                <span class="inline-flex rounded-full border px-2.5 py-1 text-[7px] font-bold uppercase tracking-[.08em] {{ $tone }}">

                                    {{ $state['label'] }}

                                </span>

                            </div>

                            <p class="mt-2 max-w-[760px] text-[9px] leading-5 text-[#7b8089]">

                                {{ $state['detail'] }}

                            </p>

                            <div class="mt-4">

                                <div class="flex items-center justify-between gap-4">

                                    <span class="seller-shipping-progress-label">Shipment progress</span>

                                    <span class="text-[8px] font-bold text-[#b97805]">{{ $state['progress'] }}%</span>

                                </div>

                                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#eceff2]">

                                    <div

                                        class="h-full rounded-full bg-[#d89b10] transition-all duration-300"

                                        style="width: {{ $state['progress'] }}%"

                                    ></div>

                                </div>

                                <div class="mt-2 grid grid-cols-5 gap-1 text-center text-[7px] text-[#9aa0a6]">

                                    <span>Ready</span>

                                    <span>Pickup</span>

                                    <span>Hub</span>

                                    <span>Final Mile</span>

                                    <span>Delivered</span>

                                </div>

                            </div>

                        </div>

                        <div class="grid min-w-0 gap-3 sm:grid-cols-3 xl:w-[560px]">

                            <div class="seller-shipping-meta-item">

                                <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Buyer</p>

                                <p class="mt-1.5 truncate text-[9px] font-semibold text-[#374151]">

                                    {{ $order->buyer_name ?: 'SARI Buyer' }}

                                </p>

                                <p class="mt-1 text-[8px] text-[#8a919b]">

                                    {{ $itemCount }} item{{ $itemCount === 1 ? '' : 's' }}

                                </p>

                            </div>

                            <div class="seller-shipping-meta-item">

                                <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Assigned Rider</p>

                                <p class="mt-1.5 truncate text-[9px] font-semibold text-[#374151]">

                                    {{ $order->courier_name ?: 'Not assigned yet' }}

                                </p>

                                <p class="mt-1 truncate text-[8px] text-[#8a919b]">

                                    {{ $order->courier_email ?: 'Waiting for Logistics' }}

                                </p>

                            </div>

                            <div class="seller-shipping-meta-item">

                                <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Logistics Hub</p>

                                <p class="mt-1.5 truncate text-[9px] font-semibold text-[#374151]">

                                    {{ $parcel ? ucwords(str_replace('_', ' ', $parcel->status)) : 'Not received yet' }}

                                </p>

                                <p class="mt-1 truncate text-[8px] text-[#8a919b]">

                                    {{ $parcel?->sorting_zone ?: 'No sorting zone yet' }}

                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="seller-shipping-card-footer flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[8px] text-[#8a919b]">

                            <span>

                                Updated

                                <strong class="font-semibold text-[#606771]">{{ $order->updated_at?->diffForHumans() }}</strong>

                            </span>

                            @if($order->picked_up_at)

                                <span>

                                    Picked up

                                    <strong class="font-semibold text-[#606771]">{{ $order->picked_up_at->format('M d, h:i A') }}</strong>

                                </span>

                            @endif

                            @if($order->delivered_at)

                                <span>

                                    Delivered

                                    <strong class="font-semibold text-[#606771]">{{ $order->delivered_at->format('M d, h:i A') }}</strong>

                                </span>

                            @endif

                        </div>

                        <div class="flex items-center gap-2">

                            @if(Route::has('seller.orders.waybill'))

                                <a

                                    href="{{ route('seller.orders.waybill', $order) }}"

                                    class="seller-shipping-waybill-button"

                                >

                                    Waybill

                                </a>

                            @endif

                            <a

                                href="{{ route('seller.shipping.show', $order) }}"

                                class="seller-shipping-track-button"

                            >

                                Track Shipment

                                <span>→</span>

                            </a>

                        </div>

                    </div>

                </article>

            @empty

                <div class="seller-shipping-empty">

                    <span class="seller-shipping-empty-icon">

                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">

                            <path d="M3 7h11v10H3z"></path>

                            <path d="M14 10h3l4 4v3h-7z"></path>

                            <circle cx="7" cy="18" r="1.5"></circle>

                            <circle cx="18" cy="18" r="1.5"></circle>

                        </svg>

                    </span>

                    <h3 class="mt-4 text-[12px] font-semibold text-[#374151]">No shipments found</h3>

                    <p class="mt-2 text-[9px] leading-5 text-[#8a919b]">

                        Orders appear here after the Seller marks them ready for pickup.

                    </p>

                </div>

            @endforelse

        </div>

        @if($orders->hasPages())

            <div class="border-t border-[#eceef1] bg-white px-4 py-4 sm:px-5">

                {{ $orders->links() }}

            </div>

        @endif

    </section>

</div>

@endsection

@push('scripts')

<script>

(function () {

    window.__SARI_SELLER_SHIPPING_INDEX_LIFECYCLE__?.abort();

    const lifecycle = new AbortController();
    const { signal } = lifecycle;

    window.__SARI_SELLER_SHIPPING_INDEX_LIFECYCLE__ = lifecycle;

    function initShippingStatusDropdown() {

        document.querySelectorAll('[data-shipping-status-dropdown]').forEach((root) => {

            if (root.dataset.dropdownReady === '1') return;

            root.dataset.dropdownReady = '1';

            const nativeSelect = root.querySelector('[data-shipping-status-native]');

            const button = root.querySelector('[data-shipping-status-button]');

            const label = root.querySelector('[data-shipping-status-label]');

            const menu = root.querySelector('[data-shipping-status-menu]');

            const options = Array.from(root.querySelectorAll('[data-shipping-status-value]'));

            if (!nativeSelect || !button || !label || !menu || !options.length) return;

            const getLabel = (value) => {

                const match = Array.from(nativeSelect.options).find((option) => option.value === value);

                return match?.textContent?.trim() || 'All Status';

            };

            const sync = (value) => {

                nativeSelect.value = value;

                label.textContent = getLabel(value);

                options.forEach((option) => {

                    const selected = (option.dataset.shippingStatusValue || '') === value;

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

                    sync(option.dataset.shippingStatusValue || 'all');

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

        window.__SARI_SELLER_AFTER_PAINT__(initShippingStatusDropdown);

    } else if (document.readyState === 'loading') {

        document.addEventListener('DOMContentLoaded', initShippingStatusDropdown, { once: true, signal });

    } else {

        initShippingStatusDropdown();

    }

    const cleanup = () => {
        lifecycle.abort();

        if (window.__SARI_SELLER_SHIPPING_INDEX_LIFECYCLE__ === lifecycle) {
            window.__SARI_SELLER_SHIPPING_INDEX_LIFECYCLE__ = null;
        }
    };

    document.addEventListener('livewire:navigating', cleanup, { once: true, signal });
    window.addEventListener('beforeunload', cleanup, { once: true, signal });

})();

</script>

@endpush

