@extends('layouts.logistics')

@section('title', 'Pickup Requests — SARI Logistics')
@section('page-title', 'Pickup Requests')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/logistics/pickup.css') }}?v=20261015-2"
    >
@endpush

@section('content')
<div
    class="pickup-page"
    data-pickup-workspace="requests"
    data-old-ids="{{ json_encode(array_map('intval', (array) old('order_ids', []))) }}"
>
    <header class="pickup-page-header">
        <div>
            <p class="pickup-eyebrow">SELLER COLLECTIONS</p>

            <h1>Pickup <span>Requests</span></h1>

            <p class="pickup-subtitle">
                Review Seller requests, verify pickup details,
                and prepare parcels for Rider assignment.
            </p>
        </div>

        <a
            class="pickup-button pickup-button-primary"
            href="{{ route('logistics.delivery-assignment') }}"
        >
            Pickup Assignment
            <span aria-hidden="true">→</span>
        </a>
    </header>

    @if (session('success'))
        <div
            class="pickup-alert pickup-alert-success"
            role="status"
        >
            <strong>Queue updated</strong>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div
            class="pickup-alert pickup-alert-danger"
            role="alert"
        >
            <strong>Could not verify requests</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $summaryCards = [
            [
                'All requests',
                $stats['total'],
                'Unassigned Seller parcels',
                'neutral',
            ],
            [
                'Awaiting verification',
                $stats['pending'],
                'Review before dispatch',
                'amber',
            ],
            [
                'Ready to assign',
                $stats['verified'],
                $stats['batches'].' Seller pickup batches',
                'green',
            ],
            [
                'Needs attention',
                $stats['attention'],
                'Pickup details or route issues',
                'red',
            ],
        ];

        $filters = [
            'all' => ['All', $stats['total']],
            'pending' => ['Awaiting verification', $stats['pending']],
            'verified' => ['Verified', $stats['verified']],
            'attention' => ['Needs attention', $stats['attention']],
        ];

        $stateLabels = [
            'pending' => 'Awaiting verification',
            'verified' => 'Verified',
            'attention' => 'Needs attention',
        ];
    @endphp

    <div class="pickup-stats">
        @foreach ($summaryCards as [$label, $value, $note, $tone])
            <article class="pickup-stat pickup-stat-{{ $tone }}">
                <p>{{ $label }}</p>
                <strong>{{ $value }}</strong>
                <span>{{ $note }}</span>
            </article>
        @endforeach
    </div>

    <nav
        class="pickup-flow"
        aria-label="Pickup workflow"
    >
        <span
            class="is-current"
            aria-current="step"
        >
            <b>1</b>
            Review & verify
        </span>

        <a href="{{ route('logistics.delivery-assignment') }}">
            <b>2</b>
            Assign Pickup Rider
        </a>

        <a href="{{ route('logistics.incoming-parcels') }}">
            <b>3</b>
            Origin hub intake
        </a>
    </nav>

    <section
        class="pickup-workspace"
        aria-labelledby="requests-title"
    >
        <div class="pickup-toolbar">
            <div>
                <h2 id="requests-title">
                    Seller pickup queue
                </h2>

                <p>
                    Oldest requests appear first.
                    Verification does not assign a Rider.
                </p>
            </div>

            <label class="pickup-search">
                <span class="sr-only">
                    Search pickup requests
                </span>

                <input
                    type="search"
                    data-pickup-search
                    placeholder="Search order, Seller, address, or hub…"
                >
            </label>
        </div>

        <div class="pickup-filter-row">
            <div
                class="pickup-tabs"
                role="group"
                aria-label="Filter requests"
            >
                @foreach ($filters as $filter => [$label, $count])
                    <button
                        type="button"
                        data-pickup-filter="{{ $filter }}"
                        aria-pressed="{{ $filter === 'all' ? 'true' : 'false' }}"
                    >
                        {{ $label }}
                        <span>{{ $count }}</span>
                    </button>
                @endforeach
            </div>

            <button
                type="button"
                class="pickup-text-link"
                data-pickup-clear
            >
                Clear filters
            </button>
        </div>

        <form
            id="pickupVerifyForm"
            method="POST"
            action="{{ route('logistics.pickup-requests.batch-verify') }}"
            data-pickup-verify-form
        >
            @csrf
        </form>

        <div class="pickup-selection-bar">
            <label>
                <input
                    type="checkbox"
                    data-pickup-select-visible
                >

                Select visible requests
            </label>

            <span
                data-pickup-selected-count
                aria-live="polite"
            >
                0 selected
            </span>

            <button
                form="pickupVerifyForm"
                type="submit"
                class="pickup-button pickup-button-primary"
                data-pickup-verify-button
                disabled
            >
                Verify selected
            </button>
        </div>

        <div class="pickup-table-scroll">
            <table class="pickup-table">
                <thead>
                    <tr>
                        <th>
                            <span class="sr-only">Select</span>
                        </th>
                        <th>Order / Seller</th>
                        <th>Pickup location</th>
                        <th>Origin hub</th>
                        <th>Requested</th>
                        <th>Status / Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($orders as $order)
                        @php
                            $origin = $order->deliveryRouteStops->firstWhere(
                                'stop_key',
                                'origin_facility'
                            );

                            $sellerName = $order->seller?->store_name
                                ?: $order->pickup_name
                                ?: 'Seller Pickup';

                            $state = $order->getAttribute(
                                'pickup_queue_state'
                            );

                            $issues = $order->getAttribute(
                                'pickup_issues'
                            );

                            $search = implode(' ', [
                                $order->order_number,
                                $sellerName,
                                $order->pickup_address,
                                $order->buyer_name,
                                $origin?->facility?->name,
                                $origin?->name,
                            ]);
                        @endphp

                        <tr
                            data-pickup-item
                            data-state="{{ $state }}"
                            data-search="{{ $search }}"
                            data-order-id="{{ $order->id }}"
                        >
                            <td>
                                <input
                                    type="checkbox"
                                    form="pickupVerifyForm"
                                    name="order_ids[]"
                                    value="{{ $order->id }}"
                                    data-pickup-select
                                    @disabled($state !== 'pending')
                                    aria-label="Select {{ $order->order_number }}"
                                >
                            </td>

                            <td>
                                <strong>
                                    {{ $order->order_number }}
                                </strong>

                                <p>{{ $sellerName }}</p>

                                <details class="pickup-details">
                                    <summary>
                                        View parcel details
                                        <span aria-hidden="true">+</span>
                                    </summary>

                                    <div class="pickup-detail-body">
                                        <div class="pickup-detail-grid">
                                            <div>
                                                <span class="pickup-label">
                                                    Buyer
                                                </span>

                                                <strong>
                                                    {{ $order->buyer_name }}
                                                </strong>

                                                <p>
                                                    {{ $order->buyer_address }}
                                                </p>
                                            </div>

                                            <div>
                                                <span class="pickup-label">
                                                    Payment
                                                </span>

                                                <strong>
                                                    {{ $order->payment_method }}
                                                    ·
                                                    {{ ucfirst($order->payment_status) }}
                                                </strong>

                                                <p>
                                                    Order value
                                                    ₱{{ number_format((float) $order->total, 2) }}
                                                </p>
                                            </div>

                                            <div>
                                                <span class="pickup-label">
                                                    Seller contact
                                                </span>

                                                <strong>
                                                    {{
                                                        $order->seller?->store_phone
                                                        ?: $order->seller?->contact_no
                                                        ?: 'Not provided'
                                                    }}
                                                </strong>

                                                <p>
                                                    {{
                                                        $order->seller?->pickup_instructions
                                                        ?: 'No pickup instructions provided.'
                                                    }}
                                                </p>
                                            </div>
                                        </div>

                                        <ul class="pickup-products">
                                            @forelse (($order->items ?? []) as $item)
                                                <li>
                                                    <span>
                                                        {{ $item['name'] ?? 'Product' }}

                                                        @if (!empty($item['variant_label']))
                                                            · {{ $item['variant_label'] }}
                                                        @endif
                                                    </span>

                                                    <strong>
                                                        × {{ $item['qty'] ?? $item['quantity'] ?? 1 }}
                                                    </strong>
                                                </li>
                                            @empty
                                                <li>
                                                    Product details are not
                                                    available for this parcel.
                                                </li>
                                            @endforelse
                                        </ul>

                                        @if ($order->logisticsParcel || $order->status === 'ready_for_pickup')
                                            <a
                                                class="pickup-text-link"
                                                href="{{ route('logistics.delivery-routes.edit', $order) }}"
                                            >
                                                Review delivery route →
                                            </a>
                                        @endif
                                    </div>
                                </details>
                            </td>

                            <td>
                                <strong>
                                    {{ $order->pickup_name ?: $sellerName }}
                                </strong>

                                <p class="pickup-address">
                                    {{
                                        $order->pickup_address
                                        ?: 'Pickup address missing'
                                    }}
                                </p>
                            </td>

                            <td>
                                <strong>
                                    {{ $origin?->facility?->name ?: 'Not routed' }}
                                </strong>

                                <p>
                                    {{
                                        $origin?->facility?->municipality_name
                                        ?: 'Origin sorting facility'
                                    }}
                                </p>
                            </td>

                            <td>
                                <strong>
                                    {{
                                        $order->ready_at?->diffForHumans()
                                        ?: 'Not recorded'
                                    }}
                                </strong>

                                <p>
                                    {{
                                        $order->ready_at
                                            ?->timezone('Asia/Manila')
                                            ->format('M d, Y · g:i A')
                                    }}
                                </p>
                            </td>

                            <td>
                                <span class="pickup-badge pickup-badge-{{ $state }}">
                                    {{ $stateLabels[$state] }}
                                </span>

                                @if ($state === 'pending')
                                    <form
                                        method="POST"
                                        action="{{ route('logistics.pickup-requests.verify', $order) }}"
                                        data-pickup-single-verify
                                    >
                                        @csrf

                                        <button
                                            class="pickup-text-link"
                                            type="submit"
                                        >
                                            Verify request →
                                        </button>
                                    </form>
                                @elseif ($state === 'verified')
                                    <a
                                        class="pickup-text-link"
                                        href="{{ route('logistics.delivery-assignment', ['order' => $order->id]) }}"
                                    >
                                        Assign Pickup Rider →
                                    </a>
                                @else
                                    <ul class="pickup-issues">
                                        @foreach ($issues as $issue)
                                            <li>{{ $issue }}</li>
                                        @endforeach
                                    </ul>

                                    <a
                                        class="pickup-text-link"
                                        href="{{ route('logistics.delivery-routes.edit', $order) }}"
                                    >
                                        Review route →
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($orders->isEmpty())
            <div class="pickup-empty">
                <span
                    class="pickup-empty-icon"
                    aria-hidden="true"
                >
                    ✓
                </span>

                <h3>No pickup requests waiting</h3>

                <p>
                    Requests appear here when Sellers mark their orders
                    ready for pickup and they are routed to your
                    Logistics network.
                </p>
            </div>
        @endif

        <div
            class="pickup-empty"
            data-pickup-search-empty
            hidden
        >
            <h3>No matching requests</h3>

            <p>
                Try another search or clear the status filter.
            </p>

            <button
                type="button"
                class="pickup-text-link"
                data-pickup-clear
            >
                Clear filters
            </button>
        </div>

        <footer class="pickup-list-footer">
            <span
                data-pickup-visible-count
                aria-live="polite"
            >
                {{ $orders->count() }} requests shown
            </span>

            <span>
                Request times shown in Philippine time
            </span>
        </footer>
    </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
    'use strict';

    function initialize() {
        document.querySelectorAll(
            '[data-pickup-workspace="requests"]'
        ).forEach((root) => {
            if (root.dataset.initialized) {
                return;
            }

            root.dataset.initialized = 'true';

            const items = [
                ...root.querySelectorAll('[data-pickup-item]')
            ];

            const search = root.querySelector(
                '[data-pickup-search]'
            );

            const empty = root.querySelector(
                '[data-pickup-search-empty]'
            );

            const tabs = [
                ...root.querySelectorAll('[data-pickup-filter]')
            ];

            const selectVisible = root.querySelector(
                '[data-pickup-select-visible]'
            );

            const verifyButton = root.querySelector(
                '[data-pickup-verify-button]'
            );

            const verifyForm = root.querySelector(
                '[data-pickup-verify-form]'
            );

            let filter = 'all';

            const normalize = (value) => {
                return String(value || '')
                    .toLocaleLowerCase()
                    .trim();
            };

            const selectedRequests = () => {
                return [
                    ...root.querySelectorAll('[data-pickup-select]')
                ].filter((input) => {
                    return input.checked && !input.disabled;
                });
            };

            function updateSelection() {
                const eligible = [
                    ...root.querySelectorAll('[data-pickup-select]')
                ].filter((input) => {
                    return !input.disabled
                        && !input.closest('[data-pickup-item]').hidden;
                });

                const count = selectedRequests().length;

                root.querySelector(
                    '[data-pickup-selected-count]'
                ).textContent = `${count} selected`;

                verifyButton.disabled = count === 0;

                selectVisible.disabled = eligible.length === 0;

                selectVisible.checked = eligible.length > 0
                    && eligible.every((input) => input.checked);

                selectVisible.indeterminate = eligible.some(
                    (input) => input.checked
                ) && !selectVisible.checked;
            }

            function applyFilters() {
                const keyword = normalize(search?.value);

                let visible = 0;

                items.forEach((item) => {
                    const matchesState = filter === 'all'
                        || item.dataset.state === filter;

                    const matchesSearch = normalize(
                        item.dataset.search
                    ).includes(keyword);

                    item.hidden = !matchesState || !matchesSearch;

                    if (item.hidden) {
                        item.querySelector(
                            '[data-pickup-select]'
                        ).checked = false;
                    } else {
                        visible++;
                    }
                });

                if (empty) {
                    empty.hidden = items.length === 0 || visible > 0;
                }

                root.querySelector(
                    '[data-pickup-visible-count]'
                ).textContent = `${visible} of ${items.length} requests shown`;

                tabs.forEach((tab) => {
                    tab.setAttribute(
                        'aria-pressed',
                        String(tab.dataset.pickupFilter === filter)
                    );
                });

                updateSelection();
            }

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    filter = tab.dataset.pickupFilter;
                    applyFilters();
                });
            });

            search?.addEventListener('input', applyFilters);

            root.querySelectorAll(
                '[data-pickup-clear]'
            ).forEach((button) => {
                button.addEventListener('click', () => {
                    filter = 'all';

                    if (search) {
                        search.value = '';
                    }

                    applyFilters();
                });
            });

            const oldIds = new Set(
                JSON.parse(root.dataset.oldIds || '[]').map(String)
            );

            root.querySelectorAll(
                '[data-pickup-select]'
            ).forEach((input) => {
                input.checked = !input.disabled
                    && oldIds.has(input.value);

                input.addEventListener('change', updateSelection);
            });

            selectVisible.addEventListener('change', (event) => {
                const checked = event.target.checked;

                let count = 0;

                root.querySelectorAll(
                    '[data-pickup-select]'
                ).forEach((input) => {
                    const visible = !input.closest(
                        '[data-pickup-item]'
                    ).hidden;

                    if (!input.disabled && visible) {
                        input.checked = checked && count < 200;

                        if (input.checked) {
                            count++;
                        }
                    }
                });

                updateSelection();
            });

            verifyForm.addEventListener('submit', (event) => {
                const count = selectedRequests().length;

                if (!count || count > 200) {
                    event.preventDefault();

                    if (count > 200) {
                        window.alert(
                            'Select up to 200 requests per verification.'
                        );
                    }

                    return;
                }

                const confirmed = window.confirm(
                    `Verify ${count} selected pickup `
                    + `${count === 1 ? 'request' : 'requests'}? `
                    + 'These parcels will become available for '
                    + 'Pickup Rider assignment.'
                );

                if (!confirmed) {
                    event.preventDefault();
                    return;
                }

                verifyButton.disabled = true;
            });

            root.querySelectorAll(
                '[data-pickup-single-verify]'
            ).forEach((form) => {
                form.addEventListener('submit', (event) => {
                    const confirmed = window.confirm(
                        'Verify this Seller pickup request and make '
                        + 'it available for Rider assignment?'
                    );

                    if (!confirmed) {
                        event.preventDefault();
                        return;
                    }

                    form.querySelector('button').disabled = true;
                });
            });

            applyFilters();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }

    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            window.location.reload();
        }
    });
})();
</script>
@endpush