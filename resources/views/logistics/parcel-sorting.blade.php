@extends('layouts.logistics')

@section('title', 'Parcel Sorting — SARI Logistics')
@section('page-title', 'Parcel Sorting')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/logistics/parcel-sorting.css') }}?v=20261015-1"
    >
@endpush


@section('content')

<div
    class="sorting-page"
    data-sorting-workspace
>

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}

    <header class="sorting-page-header">

        <div>

            <p class="sorting-eyebrow">
                ORIGIN HUB OPERATIONS
            </p>

            <h1>
                Parcel <span>Sorting</span>
            </h1>

            <p class="sorting-subtitle">
                Organize received parcels into sorting zones or racks,
                confirm their destination route, and clear them for
                hub-to-hub movement.
            </p>

        </div>


        <div class="sorting-header-actions">

            <a
                href="{{ route('logistics.incoming-parcels') }}"
                class="sorting-button"
            >
                <span aria-hidden="true">←</span>
                Incoming Parcels
            </a>


            <a
                href="{{ route('logistics.hub-transfers') }}"
                class="sorting-button sorting-button-primary"
            >
                Hub Transfers
                <span aria-hidden="true">→</span>
            </a>

        </div>

    </header>


    {{-- =====================================================
        FLASH MESSAGES
    ====================================================== --}}

    @if(session('success'))

        <div
            class="sorting-alert sorting-alert-success"
            role="status"
        >
            <strong>Sorting queue updated</strong>

            <p>
                {{ session('success') }}
            </p>
        </div>

    @endif


    @if($errors->any())

        <div
            class="sorting-alert sorting-alert-danger"
            role="alert"
        >
            <strong>Could not sort parcel</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif


    {{-- =====================================================
        SUMMARY DATA
    ====================================================== --}}

    @php
        $receivedCount = $parcels
            ->where('status', 'received')
            ->count();

        $sortedCount = $parcels
            ->where('status', 'sorted')
            ->count();

        $originHubCount = $parcels
            ->map(function ($parcel) {
                return $parcel
                    ->order
                    ?->deliveryRouteStops
                    ?->firstWhere(
                        'stop_key',
                        'origin_facility'
                    )
                    ?->logistics_facility_id;
            })
            ->filter()
            ->unique()
            ->count();

        $destinationHubCount = $parcels
            ->map(function ($parcel) {
                return $parcel
                    ->order
                    ?->deliveryRouteStops
                    ?->firstWhere(
                        'stop_key',
                        'destination_facility'
                    )
                    ?->logistics_facility_id;
            })
            ->filter()
            ->unique()
            ->count();

        $zoneCount = $parcels
            ->where('status', 'sorted')
            ->pluck('sorting_zone')
            ->filter()
            ->map(
                fn ($zone) => mb_strtolower(
                    trim((string) $zone)
                )
            )
            ->unique()
            ->count();

        $summaryCards = [
            [
                'Sorting queue',
                $parcels->count(),
                'Received and sorted parcels',
                'neutral',
            ],
            [
                'Needs sorting',
                $receivedCount,
                'Waiting for zone assignment',
                'amber',
            ],
            [
                'Sorted',
                $sortedCount,
                'Ready for transfer planning',
                'green',
            ],
            [
                'Sorting zones',
                $zoneCount,
                $destinationHubCount.' destination '
                    .($destinationHubCount === 1 ? 'hub' : 'hubs'),
                'blue',
            ],
        ];
    @endphp


    {{-- =====================================================
        SUMMARY CARDS
    ====================================================== --}}

    <div class="sorting-stats">

        @foreach($summaryCards as [$label, $value, $note, $tone])

            <article class="sorting-stat sorting-stat-{{ $tone }}">

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
        OPERATIONS FLOW
    ====================================================== --}}

    <nav
        class="sorting-flow"
        aria-label="Origin hub workflow"
    >

        <a href="{{ route('logistics.pickup-requests') }}">
            <b>1</b>
            Review & verify
        </a>


        <a href="{{ route('logistics.delivery-assignment') }}">
            <b>2</b>
            Pickup Rider
        </a>


        <a href="{{ route('logistics.incoming-parcels') }}">
            <b>3</b>
            Origin intake
        </a>


        <span
            class="is-current"
            aria-current="step"
        >
            <b>4</b>
            Parcel sorting
        </span>


        <a href="{{ route('logistics.hub-transfers') }}">
            <b>5</b>
            Hub transfer
        </a>

    </nav>


    {{-- =====================================================
        WORKSPACE
    ====================================================== --}}

    <section
        class="sorting-workspace"
        aria-labelledby="sorting-queue-heading"
    >

        {{-- =================================================
            TOOLBAR
        ================================================== --}}

        <div class="sorting-toolbar">

            <div>

                <h2 id="sorting-queue-heading">
                    Origin hub sorting queue
                </h2>

                <p>
                    Assign the physical sorting zone before a parcel
                    can move to the next Logistics leg.
                </p>

            </div>


            <label class="sorting-search">

                <span class="sr-only">
                    Search parcel sorting queue
                </span>

                <input
                    type="search"
                    data-sorting-search
                    placeholder="Search order, Seller, hub, or zone…"
                >

            </label>

        </div>


        {{-- =================================================
            FILTERS
        ================================================== --}}

        <div class="sorting-filter-row">

            <div
                class="sorting-tabs"
                role="group"
                aria-label="Filter sorting queue"
            >

                <button
                    type="button"
                    data-sorting-filter="all"
                    aria-pressed="true"
                >
                    All

                    <span>
                        {{ $parcels->count() }}
                    </span>
                </button>


                <button
                    type="button"
                    data-sorting-filter="received"
                    aria-pressed="false"
                >
                    Needs sorting

                    <span>
                        {{ $receivedCount }}
                    </span>
                </button>


                <button
                    type="button"
                    data-sorting-filter="sorted"
                    aria-pressed="false"
                >
                    Sorted

                    <span>
                        {{ $sortedCount }}
                    </span>
                </button>

            </div>


            <button
                type="button"
                class="sorting-text-link"
                data-sorting-clear
            >
                Clear filters
            </button>

        </div>


        {{-- =================================================
            TABLE
        ================================================== --}}

        <div class="sorting-table-scroll">

            <table class="sorting-table">

                <thead>

                    <tr>
                        <th>Order / Seller</th>
                        <th>Origin Hub</th>
                        <th>Destination Hub</th>
                        <th>Received</th>
                        <th>Sorting Zone</th>
                        <th>Status / Action</th>
                    </tr>

                </thead>


                <tbody>

                    @foreach($parcels as $parcel)

                        @php
                            $order =
                                $parcel->order;

                            $routeStops =
                                $order
                                    ?->deliveryRouteStops
                                    ?->keyBy('stop_key')
                                ?? collect();

                            $originStop =
                                $routeStops->get(
                                    'origin_facility'
                                );

                            $destinationStop =
                                $routeStops->get(
                                    'destination_facility'
                                );

                            $originName =
                                $originStop
                                    ?->facility
                                    ?->name
                                ?: $originStop
                                    ?->name
                                ?: 'Origin hub pending';

                            $destinationName =
                                $destinationStop
                                    ?->facility
                                    ?->name
                                ?: $destinationStop
                                    ?->name
                                ?: 'Destination pending';

                            $originMunicipality =
                                $originStop
                                    ?->facility
                                    ?->municipality_name;

                            $destinationMunicipality =
                                $destinationStop
                                    ?->facility
                                    ?->municipality_name;

                            $sellerName =
                                $order
                                    ?->seller
                                    ?->store_name
                                ?: $order
                                    ?->pickup_name
                                ?: 'SARI Seller';

                            $state =
                                $parcel->status === 'sorted'
                                    ? 'sorted'
                                    : 'received';

                            $searchText =
                                implode(
                                    ' ',
                                    array_filter([
                                        $order?->order_number,
                                        $sellerName,
                                        $order?->buyer_name,
                                        $order?->pickup_address,
                                        $originName,
                                        $originMunicipality,
                                        $destinationName,
                                        $destinationMunicipality,
                                        $parcel->sorting_zone,
                                        $parcel->status,
                                    ])
                                );
                        @endphp


                        <tr
                            data-sorting-row
                            data-state="{{ $state }}"
                            data-search="{{ $searchText }}"
                        >

                            {{-- =================================
                                ORDER
                            ================================== --}}

                            <td>

                                <strong class="sorting-order-number">
                                    {{ $order?->order_number }}
                                </strong>


                                <p class="sorting-primary-text">
                                    {{ $sellerName }}
                                </p>


                                <p>
                                    Buyer:
                                    {{ $order?->buyer_name }}
                                </p>


                                <details class="sorting-details">

                                    <summary>
                                        Parcel details

                                        <span aria-hidden="true">
                                            +
                                        </span>
                                    </summary>


                                    <div class="sorting-detail-body">

                                        <div class="sorting-detail-grid">

                                            <div>

                                                <span class="sorting-label">
                                                    Seller pickup
                                                </span>

                                                <strong>
                                                    {{
                                                        $order?->pickup_name
                                                        ?: $sellerName
                                                    }}
                                                </strong>

                                                <p>
                                                    {{
                                                        $order?->pickup_address
                                                        ?: 'No pickup address recorded.'
                                                    }}
                                                </p>

                                            </div>


                                            <div>

                                                <span class="sorting-label">
                                                    Buyer destination
                                                </span>

                                                <strong>
                                                    {{ $order?->buyer_name }}
                                                </strong>

                                                <p>
                                                    {{ $order?->buyer_address }}
                                                </p>

                                            </div>


                                            <div>

                                                <span class="sorting-label">
                                                    Pickup Rider
                                                </span>

                                                <strong>
                                                    {{
                                                        $order?->courier_name
                                                        ?: 'Pickup Rider'
                                                    }}
                                                </strong>

                                                <p>
                                                    {{
                                                        $order?->courier_email
                                                        ?: 'No Rider email recorded'
                                                    }}
                                                </p>

                                            </div>


                                            <div>

                                                <span class="sorting-label">
                                                    Order value
                                                </span>

                                                <strong>
                                                    ₱{{ number_format(
                                                        (float) (
                                                            $order?->total
                                                            ?? 0
                                                        ),
                                                        2
                                                    ) }}
                                                </strong>

                                                <p>
                                                    Marketplace order total
                                                </p>

                                            </div>

                                        </div>


                                        @if(
                                            $parcel->notes
                                            && $parcel->status === 'received'
                                        )

                                            <div class="sorting-existing-note">

                                                <span class="sorting-label">
                                                    Intake note
                                                </span>

                                                <p>
                                                    {{ $parcel->notes }}
                                                </p>

                                            </div>

                                        @endif


                                        <ul class="sorting-products">

                                            @forelse(
                                                ($order?->items ?? [])
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
                                ORIGIN HUB
                            ================================== --}}

                            <td>

                                <div class="sorting-hub">

                                    <span
                                        class="sorting-hub-icon sorting-hub-origin"
                                        aria-hidden="true"
                                    >
                                        O
                                    </span>


                                    <div>

                                        <strong>
                                            {{ $originName }}
                                        </strong>


                                        <p>
                                            {{
                                                $originMunicipality
                                                ?: 'Origin sorting facility'
                                            }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            {{-- =================================
                                DESTINATION HUB
                            ================================== --}}

                            <td>

                                <div class="sorting-hub">

                                    <span
                                        class="sorting-hub-icon sorting-hub-destination"
                                        aria-hidden="true"
                                    >
                                        D
                                    </span>


                                    <div>

                                        <strong>
                                            {{ $destinationName }}
                                        </strong>


                                        <p>
                                            {{
                                                $destinationMunicipality
                                                ?: 'Destination sorting facility'
                                            }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            {{-- =================================
                                RECEIVED
                            ================================== --}}

                            <td>

                                <strong>
                                    {{
                                        $parcel->received_at
                                            ?->diffForHumans()
                                        ?: 'Not recorded'
                                    }}
                                </strong>


                                @if($parcel->received_at)

                                    <p>
                                        {{
                                            $parcel
                                                ->received_at
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
                                SORTING ZONE
                            ================================== --}}

                            <td>

                                @if($parcel->status === 'sorted')

                                    <div class="sorting-zone-display">

                                        <span class="sorting-zone-code">
                                            {{ $parcel->sorting_zone }}
                                        </span>


                                        <p>
                                            Assigned sorting location
                                        </p>

                                    </div>

                                @else

                                    <span class="sorting-zone-unassigned">
                                        Not assigned
                                    </span>

                                @endif

                            </td>


                            {{-- =================================
                                STATUS / ACTION
                            ================================== --}}

                            <td>

                                @if($parcel->status === 'received')

                                    <span class="sorting-badge sorting-badge-received">
                                        Needs sorting
                                    </span>


                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'logistics.parcel-sorting.sort',
                                            $parcel
                                        ) }}"
                                        class="sorting-form"
                                        data-sorting-form
                                        data-order-number="{{ $order?->order_number }}"
                                    >

                                        @csrf


                                        <label>

                                            <span class="sorting-label">
                                                Zone / rack
                                            </span>


                                            <input
                                                type="text"
                                                name="sorting_zone"
                                                required
                                                maxlength="80"
                                                placeholder="e.g. STA-CRUZ-01"
                                                autocomplete="off"
                                            >

                                        </label>


                                        <label>

                                            <span class="sorting-label">
                                                Sorting note
                                            </span>


                                            <textarea
                                                name="notes"
                                                rows="2"
                                                maxlength="1000"
                                                placeholder="Optional note…"
                                            >{{ old('notes') }}</textarea>

                                        </label>


                                        <button
                                            type="submit"
                                            class="sorting-button sorting-button-primary"
                                        >
                                            Mark Parcel Sorted
                                        </button>

                                    </form>

                                @else

                                    <span class="sorting-badge sorting-badge-sorted">
                                        Sorted
                                    </span>


                                    <div class="sorting-completed">

                                        <strong>
                                            Ready for next leg
                                        </strong>


                                        <p>
                                            {{
                                                $parcel
                                                    ->sorted_at
                                                    ?->timezone(
                                                        'Asia/Manila'
                                                    )
                                                    ->format(
                                                        'M d, Y · g:i A'
                                                    )
                                                ?: 'Sorting completed'
                                            }}
                                        </p>


                                        @if($parcel->notes)

                                            <p class="sorting-note">
                                                {{ $parcel->notes }}
                                            </p>

                                        @endif

                                    </div>


                                    <a
                                        href="{{ route(
                                            'logistics.hub-transfers'
                                        ) }}"
                                        class="sorting-text-link"
                                    >
                                        Continue to Hub Transfer →
                                    </a>

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        {{-- =================================================
            NO PARCELS
        ================================================== --}}

        @if($parcels->isEmpty())

            <div class="sorting-empty">

                <span
                    class="sorting-empty-icon"
                    aria-hidden="true"
                >
                    ✓
                </span>


                <h3>
                    Sorting queue is clear
                </h3>


                <p>
                    Parcels appear here after their physical receipt
                    is confirmed at the origin sorting hub.
                </p>


                <a
                    href="{{ route(
                        'logistics.incoming-parcels'
                    ) }}"
                    class="sorting-button sorting-button-primary"
                >
                    View Incoming Parcels
                </a>

            </div>

        @endif


        {{-- =================================================
            FILTER EMPTY
        ================================================== --}}

        <div
            class="sorting-empty"
            data-sorting-empty
            hidden
        >

            <h3>
                No matching parcels
            </h3>


            <p>
                Try another order number, Seller, sorting zone,
                origin hub, or destination hub.
            </p>


            <button
                type="button"
                class="sorting-text-link"
                data-sorting-clear
            >
                Clear filters
            </button>

        </div>


        {{-- =================================================
            FOOTER
        ================================================== --}}

        <footer class="sorting-list-footer">

            <span
                data-sorting-visible-count
                aria-live="polite"
            >
                {{ $parcels->count() }}
                parcels shown
            </span>


            <span>
                {{ $originHubCount }}
                origin
                {{ $originHubCount === 1 ? 'hub' : 'hubs' }}
                ·
                {{ $destinationHubCount }}
                destination
                {{ $destinationHubCount === 1 ? 'hub' : 'hubs' }}
            </span>

        </footer>

    </section>

</div>

@endsection


@push('scripts')

<script>
(() => {
    'use strict';


    function initializeParcelSorting() {

        document.querySelectorAll(
            '[data-sorting-workspace]'
        ).forEach((root) => {

            if (root.dataset.initialized) {
                return;
            }


            root.dataset.initialized = 'true';


            const rows = [
                ...root.querySelectorAll(
                    '[data-sorting-row]'
                )
            ];


            const search =
                root.querySelector(
                    '[data-sorting-search]'
                );


            const tabs = [
                ...root.querySelectorAll(
                    '[data-sorting-filter]'
                )
            ];


            const empty =
                root.querySelector(
                    '[data-sorting-empty]'
                );


            const visibleCount =
                root.querySelector(
                    '[data-sorting-visible-count]'
                );


            let filter = 'all';


            /*
            |--------------------------------------------------------------------------
            | Normalize
            |--------------------------------------------------------------------------
            */

            const normalize = (value) => {
                return String(value || '')
                    .toLocaleLowerCase()
                    .trim();
            };


            /*
            |--------------------------------------------------------------------------
            | Search / filter
            |--------------------------------------------------------------------------
            */

            function applyFilters() {

                const keyword =
                    normalize(
                        search?.value
                    );


                let shown = 0;


                rows.forEach((row) => {

                    const matchesState =
                        filter === 'all'
                        || row.dataset.state
                            === filter;


                    const matchesSearch =
                        normalize(
                            row.dataset.search
                        ).includes(keyword);


                    row.hidden =
                        !matchesState
                        || !matchesSearch;


                    if (!row.hidden) {
                        shown++;
                    }

                });


                tabs.forEach((tab) => {

                    tab.setAttribute(
                        'aria-pressed',
                        String(
                            tab.dataset.sortingFilter
                            === filter
                        )
                    );

                });


                if (empty) {

                    empty.hidden =
                        rows.length === 0
                        || shown > 0;
                }


                if (visibleCount) {

                    visibleCount.textContent =
                        `${shown} of ${rows.length} `
                        + `${
                            rows.length === 1
                                ? 'parcel'
                                : 'parcels'
                        } shown`;
                }

            }


            /*
            |--------------------------------------------------------------------------
            | Filter buttons
            |--------------------------------------------------------------------------
            */

            tabs.forEach((tab) => {

                tab.addEventListener(
                    'click',
                    () => {

                        filter =
                            tab.dataset.sortingFilter
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
                '[data-sorting-clear]'
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
            | Sort confirmation
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-sorting-form]'
            ).forEach((form) => {

                form.addEventListener(
                    'submit',
                    (event) => {

                        const zoneInput =
                            form.querySelector(
                                'input[name="sorting_zone"]'
                            );


                        const zone =
                            String(
                                zoneInput?.value || ''
                            ).trim();


                        if (!zone) {
                            return;
                        }


                        const orderNumber =
                            form.dataset.orderNumber
                            || 'this parcel';


                        const confirmed =
                            window.confirm(
                                `Mark ${orderNumber} as sorted `
                                + `under zone "${zone}"?`
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
                                'Saving sorting result…';
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
            initializeParcelSorting
        );

    } else {

        initializeParcelSorting();
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