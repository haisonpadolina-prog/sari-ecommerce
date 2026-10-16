@extends('layouts.logistics')

@section('title', 'Incoming Parcels — SARI Logistics')
@section('page-title', 'Incoming Parcels')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/logistics/incoming-parcels.css') }}?v=20261015-1"
    >
@endpush


@section('content')

<div
    class="intake-page"
    data-intake-workspace
>

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}

    <header class="intake-page-header">

        <div>

            <p class="intake-eyebrow">
                ORIGIN HUB OPERATIONS
            </p>

            <h1>
                Incoming <span>Parcels</span>
            </h1>

            <p class="intake-subtitle">
                Receive parcels delivered by Pickup Riders at their assigned
                origin sorting facility before they enter the sorting queue.
            </p>

        </div>


        <div class="intake-header-actions">

            <a
                href="{{ route('logistics.delivery-assignment') }}"
                class="intake-button"
            >
                <span aria-hidden="true">←</span>
                Pickup Assignment
            </a>


            <a
                href="{{ route('logistics.parcel-sorting') }}"
                class="intake-button intake-button-primary"
            >
                Parcel Sorting
                <span aria-hidden="true">→</span>
            </a>

        </div>

    </header>


    {{-- =====================================================
        FLASH
    ====================================================== --}}

    @if(session('success'))

        <div
            class="intake-alert intake-alert-success"
            role="status"
        >
            <strong>Hub intake updated</strong>

            <p>
                {{ session('success') }}
            </p>
        </div>

    @endif


    @if($errors->any())

        <div
            class="intake-alert intake-alert-danger"
            role="alert"
        >
            <strong>Could not update parcel intake</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif


    {{-- =====================================================
        PAGE DATA
    ====================================================== --}}

    @php
        $waitingCount = $orders
            ->filter(function ($order) use ($parcels) {
                $parcel = $parcels->get($order->id);

                return ! $parcel
                    || $parcel->status === 'awaiting_intake';
            })
            ->count();

        $receivedCount = $orders
            ->filter(function ($order) use ($parcels) {
                return $parcels
                    ->get($order->id)
                    ?->status === 'received';
            })
            ->count();

        $pickupRiderCount = $orders
            ->pluck('courier_email')
            ->filter()
            ->map(
                fn ($email) => mb_strtolower(
                    trim((string) $email)
                )
            )
            ->unique()
            ->count();

        $originHubCount = $orders
            ->map(function ($order) {
                return $order
                    ->deliveryRouteStops
                    ->firstWhere(
                        'stop_key',
                        'origin_facility'
                    )
                    ?->logistics_facility_id;
            })
            ->filter()
            ->unique()
            ->count();

        $summaryCards = [
            [
                'Incoming parcels',
                $orders->count(),
                'Pickup Rider handoffs',
                'neutral',
            ],
            [
                'Awaiting intake',
                $waitingCount,
                'Needs hub confirmation',
                'amber',
            ],
            [
                'Received',
                $receivedCount,
                'Ready for sorting',
                'green',
            ],
            [
                'Pickup Riders',
                $pickupRiderCount,
                $originHubCount.' active origin '
                    .($originHubCount === 1 ? 'hub' : 'hubs'),
                'blue',
            ],
        ];
    @endphp


    {{-- =====================================================
        SUMMARY
    ====================================================== --}}

    <div class="intake-stats">

        @foreach($summaryCards as [$label, $value, $note, $tone])

            <article class="intake-stat intake-stat-{{ $tone }}">

                <p>
                    {{ $label }}
                </p>

                <strong>
                    {{ $value }}
                </strong>

                <span>
                    {{ $note }}
                </span>

            </article>

        @endforeach

    </div>


    {{-- =====================================================
        WORKFLOW
    ====================================================== --}}

    <nav
        class="intake-flow"
        aria-label="Pickup workflow"
    >

        <a href="{{ route('logistics.pickup-requests') }}">
            <b>1</b>
            Review & verify
        </a>


        <a href="{{ route('logistics.delivery-assignment') }}">
            <b>2</b>
            Assign Pickup Rider
        </a>


        <span
            class="is-current"
            aria-current="step"
        >
            <b>3</b>
            Origin hub intake
        </span>


        <a href="{{ route('logistics.parcel-sorting') }}">
            <b>4</b>
            Parcel sorting
        </a>

    </nav>


    {{-- =====================================================
        WORKSPACE
    ====================================================== --}}

    <section
        class="intake-workspace"
        aria-labelledby="incoming-parcels-heading"
    >

        {{-- =================================================
            TOOLBAR
        ================================================== --}}

        <div class="intake-toolbar">

            <div>

                <h2 id="incoming-parcels-heading">
                    Origin facility intake
                </h2>

                <p>
                    Confirm physical handoff from Pickup Rider before
                    parcels become available for sorting.
                </p>

            </div>


            <label class="intake-search">

                <span class="sr-only">
                    Search incoming parcels
                </span>

                <input
                    type="search"
                    data-intake-search
                    placeholder="Search order, Seller, Rider, or hub…"
                >

            </label>

        </div>


        {{-- =================================================
            FILTERS
        ================================================== --}}

        <div class="intake-filter-row">

            <div
                class="intake-tabs"
                role="group"
                aria-label="Filter incoming parcels"
            >

                <button
                    type="button"
                    data-intake-filter="all"
                    aria-pressed="true"
                >
                    All
                    <span>{{ $orders->count() }}</span>
                </button>


                <button
                    type="button"
                    data-intake-filter="waiting"
                    aria-pressed="false"
                >
                    Awaiting intake
                    <span>{{ $waitingCount }}</span>
                </button>


                <button
                    type="button"
                    data-intake-filter="received"
                    aria-pressed="false"
                >
                    Received
                    <span>{{ $receivedCount }}</span>
                </button>

            </div>


            <button
                type="button"
                class="intake-text-link"
                data-intake-clear
            >
                Clear filters
            </button>

        </div>


        {{-- =================================================
            TABLE
        ================================================== --}}

        <div class="intake-table-scroll">

            <table class="intake-table">

                <thead>

                    <tr>
                        <th>Order / Seller</th>
                        <th>Pickup Rider</th>
                        <th>Origin Hub</th>
                        <th>Route</th>
                        <th>Pickup Time</th>
                        <th>Hub Intake</th>
                    </tr>

                </thead>


                <tbody>

                    @foreach($orders as $order)

                        @php
                            $parcel =
                                $parcels->get(
                                    $order->id
                                );

                            $routeStops =
                                $order
                                    ->deliveryRouteStops
                                    ->keyBy(
                                        'stop_key'
                                    );

                            $originFacility =
                                $routeStops->get(
                                    'origin_facility'
                                );

                            $destinationFacility =
                                $routeStops->get(
                                    'destination_facility'
                                );

                            $isWaiting =
                                ! $parcel
                                || $parcel->status
                                    === 'awaiting_intake';

                            $isReceived =
                                $parcel?->status
                                === 'received';

                            $state =
                                $isWaiting
                                    ? 'waiting'
                                    : (
                                        $isReceived
                                            ? 'received'
                                            : 'advanced'
                                    );

                            $sellerName =
                                $order->seller
                                    ?->store_name
                                ?: $order->pickup_name
                                ?: 'SARI Seller';

                            $searchText =
                                implode(
                                    ' ',
                                    array_filter([
                                        $order->order_number,
                                        $sellerName,
                                        $order->buyer_name,
                                        $order->courier_name,
                                        $order->courier_email,
                                        $order->pickup_address,
                                        $originFacility
                                            ?->facility
                                            ?->name
                                            ?: $originFacility
                                                ?->name,
                                        $destinationFacility
                                            ?->facility
                                            ?->name
                                            ?: $destinationFacility
                                                ?->name,
                                    ])
                                );
                        @endphp


                        <tr
                            data-intake-row
                            data-state="{{ $state }}"
                            data-search="{{ $searchText }}"
                        >

                            {{-- =================================
                                ORDER / SELLER
                            ================================== --}}

                            <td>

                                <strong class="intake-order-number">
                                    {{ $order->order_number }}
                                </strong>

                                <p class="intake-primary-text">
                                    {{ $sellerName }}
                                </p>

                                <p>
                                    Buyer:
                                    {{ $order->buyer_name }}
                                </p>


                                <details class="intake-details">

                                    <summary>
                                        Parcel details
                                        <span aria-hidden="true">
                                            +
                                        </span>
                                    </summary>


                                    <div class="intake-detail-body">

                                        <div class="intake-detail-grid">

                                            <div>

                                                <span class="intake-label">
                                                    Seller pickup
                                                </span>

                                                <strong>
                                                    {{
                                                        $order->pickup_name
                                                        ?: $sellerName
                                                    }}
                                                </strong>

                                                <p>
                                                    {{
                                                        $order->pickup_address
                                                        ?: 'No pickup address recorded.'
                                                    }}
                                                </p>

                                            </div>


                                            <div>

                                                <span class="intake-label">
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

                                                <span class="intake-label">
                                                    Payment
                                                </span>

                                                <strong>
                                                    {{
                                                        $order->payment_method
                                                        ?: 'Not recorded'
                                                    }}
                                                </strong>

                                                <p>
                                                    {{
                                                        ucfirst(
                                                            $order->payment_status
                                                            ?: 'pending'
                                                        )
                                                    }}
                                                </p>

                                            </div>


                                            <div>

                                                <span class="intake-label">
                                                    Order value
                                                </span>

                                                <strong>
                                                    ₱{{ number_format(
                                                        (float) $order->total,
                                                        2
                                                    ) }}
                                                </strong>

                                                <p>
                                                    Marketplace order total
                                                </p>

                                            </div>

                                        </div>


                                        <ul class="intake-products">

                                            @forelse(
                                                ($order->items ?? [])
                                                as $item
                                            )

                                                <li>

                                                    <span>
                                                        {{
                                                            $item['name']
                                                            ?? 'Product'
                                                        }}

                                                        @if(
                                                            !empty(
                                                                $item[
                                                                    'variant_label'
                                                                ]
                                                            )
                                                        )
                                                            ·
                                                            {{
                                                                $item[
                                                                    'variant_label'
                                                                ]
                                                            }}
                                                        @endif
                                                    </span>

                                                    <strong>
                                                        ×
                                                        {{
                                                            $item['qty']
                                                            ?? $item['quantity']
                                                            ?? 1
                                                        }}
                                                    </strong>

                                                </li>

                                            @empty

                                                <li>
                                                    <span>
                                                        Product details unavailable.
                                                    </span>
                                                </li>

                                            @endforelse

                                        </ul>

                                    </div>

                                </details>

                            </td>


                            {{-- =================================
                                PICKUP RIDER
                            ================================== --}}

                            <td>

                                <div class="intake-rider">

                                    <span
                                        class="intake-rider-avatar"
                                        aria-hidden="true"
                                    >
                                        {{
                                            mb_strtoupper(
                                                mb_substr(
                                                    trim(
                                                        (string) (
                                                            $order->courier_name
                                                            ?: 'R'
                                                        )
                                                    ),
                                                    0,
                                                    1
                                                )
                                            )
                                        }}
                                    </span>


                                    <div>

                                        <strong>
                                            {{
                                                $order->courier_name
                                                ?: 'Pickup Rider'
                                            }}
                                        </strong>

                                        <p>
                                            {{
                                                $order->courier_email
                                                ?: 'No Rider email'
                                            }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            {{-- =================================
                                ORIGIN HUB
                            ================================== --}}

                            <td>

                                <strong>
                                    {{
                                        $originFacility
                                            ?->facility
                                            ?->name
                                        ?: $originFacility
                                            ?->name
                                        ?: 'Origin hub pending'
                                    }}
                                </strong>

                                <p>
                                    {{
                                        $originFacility
                                            ?->facility
                                            ?->municipality_name
                                        ?: 'Origin sorting facility'
                                    }}
                                </p>

                            </td>


                            {{-- =================================
                                ROUTE
                            ================================== --}}

                            <td>

                                <div class="intake-route">

                                    <span>
                                        ORIGIN
                                    </span>

                                    <b aria-hidden="true">
                                        →
                                    </b>

                                    <span>
                                        DESTINATION
                                    </span>

                                </div>


                                <p class="intake-route-destination">
                                    {{
                                        $destinationFacility
                                            ?->facility
                                            ?->name
                                        ?: $destinationFacility
                                            ?->name
                                        ?: 'Destination pending'
                                    }}
                                </p>

                            </td>


                            {{-- =================================
                                PICKUP TIME
                            ================================== --}}

                            <td>

                                <strong>
                                    {{
                                        $order->picked_up_at
                                            ?->diffForHumans()
                                        ?: 'In transit'
                                    }}
                                </strong>

                                @if($order->picked_up_at)

                                    <p>
                                        {{
                                            $order
                                                ->picked_up_at
                                                ->timezone(
                                                    'Asia/Manila'
                                                )
                                                ->format(
                                                    'M d, Y · g:i A'
                                                )
                                        }}
                                    </p>

                                @endif

                            </td>


                            {{-- =================================
                                INTAKE
                            ================================== --}}

                            <td>

                                @if($isWaiting)

                                    <span class="intake-badge intake-badge-waiting">
                                        Awaiting intake
                                    </span>


                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'logistics.incoming-parcels.receive',
                                            $order
                                        ) }}"
                                        class="intake-receive-form"
                                        data-intake-receive-form
                                        data-order-number="{{ $order->order_number }}"
                                    >

                                        @csrf


                                        <label>

                                            <span class="sr-only">
                                                Optional intake note
                                            </span>

                                            <textarea
                                                name="notes"
                                                rows="2"
                                                maxlength="1000"
                                                placeholder="Optional intake note…"
                                            ></textarea>

                                        </label>


                                        <button
                                            type="submit"
                                            class="intake-button intake-button-primary"
                                        >
                                            Receive at Origin Hub
                                        </button>

                                    </form>

                                @elseif($isReceived)

                                    <span class="intake-badge intake-badge-received">
                                        Received
                                    </span>


                                    <div class="intake-received-info">

                                        <strong>
                                            Hub intake confirmed
                                        </strong>

                                        <p>
                                            {{
                                                $parcel
                                                    ->received_at
                                                    ?->timezone(
                                                        'Asia/Manila'
                                                    )
                                                    ->format(
                                                        'M d, Y · g:i A'
                                                    )
                                                ?: 'Received'
                                            }}
                                        </p>

                                        @if($parcel->notes)

                                            <p class="intake-note">
                                                {{ $parcel->notes }}
                                            </p>

                                        @endif

                                    </div>


                                    <a
                                        href="{{ route(
                                            'logistics.parcel-sorting'
                                        ) }}"
                                        class="intake-text-link"
                                    >
                                        Continue to sorting →
                                    </a>

                                @else

                                    <span class="intake-badge intake-badge-active">
                                        {{
                                            strtoupper(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $parcel?->status
                                                    ?: 'IN PROCESS'
                                                )
                                            )
                                        }}
                                    </span>


                                    <p class="intake-status-note">
                                        This parcel has already moved
                                        beyond origin hub intake.
                                    </p>

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        {{-- =================================================
            NO ORDERS
        ================================================== --}}

        @if($orders->isEmpty())

            <div class="intake-empty">

                <span
                    class="intake-empty-icon"
                    aria-hidden="true"
                >
                    ✓
                </span>


                <h3>
                    No parcels waiting for origin hub intake
                </h3>

                <p>
                    Parcels will appear here after a Pickup Rider
                    collects them from a Seller.
                </p>


                <a
                    href="{{ route(
                        'logistics.delivery-assignment'
                    ) }}"
                    class="intake-button intake-button-primary"
                >
                    View Pickup Assignment
                </a>

            </div>

        @endif


        {{-- =================================================
            SEARCH EMPTY
        ================================================== --}}

        <div
            class="intake-empty"
            data-intake-empty
            hidden
        >

            <h3>
                No matching incoming parcels
            </h3>

            <p>
                Try another order number, Seller, Rider,
                origin hub, or status.
            </p>


            <button
                type="button"
                class="intake-text-link"
                data-intake-clear
            >
                Clear filters
            </button>

        </div>


        {{-- =================================================
            FOOTER
        ================================================== --}}

        <footer class="intake-list-footer">

            <span
                data-intake-visible-count
                aria-live="polite"
            >
                {{ $orders->count() }}
                parcels shown
            </span>


            <span>
                Intake times shown in Philippine time
            </span>

        </footer>

    </section>

</div>

@endsection


@push('scripts')

<script>
(() => {
    'use strict';


    function initializeIncomingParcels() {

        document.querySelectorAll(
            '[data-intake-workspace]'
        ).forEach((root) => {

            if (root.dataset.initialized) {
                return;
            }

            root.dataset.initialized = 'true';


            const rows = [
                ...root.querySelectorAll(
                    '[data-intake-row]'
                )
            ];


            const search =
                root.querySelector(
                    '[data-intake-search]'
                );


            const tabs = [
                ...root.querySelectorAll(
                    '[data-intake-filter]'
                )
            ];


            const empty =
                root.querySelector(
                    '[data-intake-empty]'
                );


            const countNode =
                root.querySelector(
                    '[data-intake-visible-count]'
                );


            let filter = 'all';


            const normalize = (value) => {
                return String(value || '')
                    .toLocaleLowerCase()
                    .trim();
            };


            /*
            |--------------------------------------------------------------------------
            | Filter rows
            |--------------------------------------------------------------------------
            */

            function applyFilters() {

                const keyword =
                    normalize(
                        search?.value
                    );


                let visible = 0;


                rows.forEach((row) => {

                    const rowState =
                        row.dataset.state;


                    const stateMatches =
                        filter === 'all'
                        || rowState === filter;


                    const searchMatches =
                        normalize(
                            row.dataset.search
                        ).includes(keyword);


                    row.hidden =
                        !stateMatches
                        || !searchMatches;


                    if (!row.hidden) {
                        visible++;
                    }

                });


                tabs.forEach((tab) => {

                    tab.setAttribute(
                        'aria-pressed',
                        String(
                            tab.dataset.intakeFilter
                            === filter
                        )
                    );

                });


                if (empty) {

                    empty.hidden =
                        rows.length === 0
                        || visible > 0;
                }


                if (countNode) {

                    countNode.textContent =
                        `${visible} of ${rows.length} `
                        + `${
                            rows.length === 1
                                ? 'parcel'
                                : 'parcels'
                        } shown`;
                }

            }


            /*
            |--------------------------------------------------------------------------
            | Status tabs
            |--------------------------------------------------------------------------
            */

            tabs.forEach((tab) => {

                tab.addEventListener(
                    'click',
                    () => {

                        filter =
                            tab.dataset.intakeFilter
                            || 'all';

                        applyFilters();
                    }
                );

            });


            /*
            |--------------------------------------------------------------------------
            | Search
            |--------------------------------------------------------------------------
            */

            search?.addEventListener(
                'input',
                applyFilters
            );


            /*
            |--------------------------------------------------------------------------
            | Clear
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-intake-clear]'
            ).forEach((button) => {

                button.addEventListener(
                    'click',
                    () => {

                        filter = 'all';

                        if (search) {
                            search.value = '';
                        }

                        applyFilters();
                    }
                );

            });


            /*
            |--------------------------------------------------------------------------
            | Receive confirmation
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-intake-receive-form]'
            ).forEach((form) => {

                form.addEventListener(
                    'submit',
                    (event) => {

                        const orderNumber =
                            form.dataset.orderNumber
                            || 'this parcel';


                        const confirmed =
                            window.confirm(
                                `Confirm physical receipt of `
                                + `${orderNumber} at the `
                                + 'origin sorting hub?'
                            );


                        if (!confirmed) {
                            event.preventDefault();
                            return;
                        }


                        const button =
                            form.querySelector(
                                'button[type="submit"]'
                            );


                        if (button) {

                            button.disabled = true;

                            button.textContent =
                                'Receiving parcel…';
                        }

                    }
                );

            });


            applyFilters();

        });

    }


    if (
        document.readyState === 'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            initializeIncomingParcels
        );

    } else {

        initializeIncomingParcels();
    }


    window.addEventListener(
        'pageshow',
        (event) => {

            if (event.persisted) {
                window.location.reload();
            }

        }
    );

})();
</script>

@endpush