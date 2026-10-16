@extends('layouts.logistics')

@section('title', 'Hub Transfers — SARI Logistics')
@section('page-title', 'Hub Transfers')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/logistics/hub-transfers.css') }}?v=20261015-1"
    >
@endpush


@section('content')

<div
    class="transfer-page"
    data-transfer-workspace
>

    {{-- =====================================================
        HERO
    ====================================================== --}}

    <header class="transfer-page-header">

        <div>

            <p class="transfer-eyebrow">
                LINEHAUL OPERATIONS
            </p>

            <h1>
                Hub <span>Transfers</span>
            </h1>

            <p class="transfer-subtitle">
                Build linehaul manifests from sorted parcels, assign the
                transport vehicle and driver, dispatch between sorting hubs,
                and confirm destination receipt.
            </p>

        </div>


        <div class="transfer-header-actions">

            <a
                href="{{ route('logistics.parcel-sorting') }}"
                class="transfer-button"
            >
                ← Parcel Sorting
            </a>


            <a
                href="{{ route('logistics.last-mile-assignment') }}"
                class="transfer-button transfer-button-primary"
            >
                Last Mile →
            </a>

        </div>

    </header>


    {{-- =====================================================
        FLASH
    ====================================================== --}}

    @if(session('success'))

        <div
            class="transfer-alert transfer-alert-success"
            role="status"
        >
            <strong>Hub transfer updated</strong>

            <p>
                {{ session('success') }}
            </p>
        </div>

    @endif


    @if($errors->any())

        <div
            class="transfer-alert transfer-alert-danger"
            role="alert"
        >
            <strong>Could not update Hub Transfer</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif


    {{-- =====================================================
        STATS
    ====================================================== --}}

    @php
        $summaryCards = [
            [
                'Ready for linehaul',
                $stats['ready_parcels'],
                $stats['ready_routes'].' route groups',
                'amber',
            ],
            [
                'Preparing',
                $stats['preparing'],
                'Manifests awaiting departure',
                'neutral',
            ],
            [
                'In transit',
                $stats['in_transit'],
                'Vehicles currently moving',
                'blue',
            ],
            [
                'Received today',
                $stats['received_today'],
                'Destination handoffs completed',
                'green',
            ],
        ];
    @endphp


    <div class="transfer-stats">

        @foreach($summaryCards as [$label, $value, $note, $tone])

            <article class="transfer-stat transfer-stat-{{ $tone }}">

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
        FLOW
    ====================================================== --}}

    <nav
        class="transfer-flow"
        aria-label="Logistics workflow"
    >

        <a href="{{ route('logistics.incoming-parcels') }}">
            <b>1</b>
            Origin intake
        </a>


        <a href="{{ route('logistics.parcel-sorting') }}">
            <b>2</b>
            Parcel sorting
        </a>


        <span
            class="is-current"
            aria-current="step"
        >
            <b>3</b>
            Hub transfer
        </span>


        <a href="{{ route('logistics.last-mile-assignment') }}">
            <b>4</b>
            Last mile
        </a>

    </nav>


    {{-- =====================================================
        READY MANIFESTS
    ====================================================== --}}

    <section class="transfer-workspace">

        <div class="transfer-toolbar">

            <div>

                <h2>
                    Ready for linehaul
                </h2>

                <p>
                    Sorted parcels are automatically grouped by
                    origin and destination hub.
                </p>

            </div>


            <label class="transfer-search">

                <span class="sr-only">
                    Search transfer routes
                </span>

                <input
                    type="search"
                    data-ready-search
                    placeholder="Search hub, order, Seller…"
                >

            </label>

        </div>


        <div class="transfer-ready-groups">

            @forelse($readyGroups as $group)

                @php
                    $search = collect([
                        $group['origin_name'],
                        $group['origin_municipality'],
                        $group['destination_name'],
                        $group['destination_municipality'],
                    ]);

                    foreach($group['orders'] as $order) {
                        $search->push(
                            $order->order_number
                        );

                        $search->push(
                            $order->buyer_name
                        );

                        $search->push(
                            $order->seller?->store_name
                        );
                    }
                @endphp


                <article
                    class="transfer-ready-card"
                    data-ready-group
                    data-search="{{ $search->filter()->implode(' ') }}"
                >

                    <div class="transfer-route-header">

                        <div>

                            <p class="transfer-eyebrow">
                                LINEHAUL ROUTE
                            </p>

                            <div class="transfer-route-title">

                                <strong>
                                    {{ $group['origin_name'] }}
                                </strong>

                                <span aria-hidden="true">
                                    →
                                </span>

                                <strong>
                                    {{ $group['destination_name'] }}
                                </strong>

                            </div>

                        </div>


                        <span class="transfer-count-badge">
                            {{ $group['parcel_count'] }}
                            {{
                                $group['parcel_count'] === 1
                                    ? 'parcel'
                                    : 'parcels'
                            }}
                        </span>

                    </div>


                    <div class="transfer-ready-layout">

                        {{-- Parcels --}}
                        <div class="transfer-manifest-preview">

                            <div class="transfer-hub-route">

                                <div>

                                    <span class="transfer-label">
                                        Origin Hub
                                    </span>

                                    <strong>
                                        {{ $group['origin_name'] }}
                                    </strong>

                                    <p>
                                        {{
                                            $group['origin_municipality']
                                            ?: 'Origin facility'
                                        }}
                                    </p>

                                </div>


                                <b aria-hidden="true">
                                    →
                                </b>


                                <div>

                                    <span class="transfer-label">
                                        Destination Hub
                                    </span>

                                    <strong>
                                        {{ $group['destination_name'] }}
                                    </strong>

                                    <p>
                                        {{
                                            $group['destination_municipality']
                                            ?: 'Destination facility'
                                        }}
                                    </p>

                                </div>

                            </div>


                            <div class="transfer-parcel-list">

                                @foreach($group['parcels'] as $parcel)

                                    @php
                                        $order =
                                            $parcel->order;
                                    @endphp


                                    <div class="transfer-parcel-row">

                                        <div>

                                            <strong>
                                                {{ $order?->order_number }}
                                            </strong>

                                            <p>
                                                {{ $order?->buyer_name }}
                                                ·
                                                Zone
                                                {{
                                                    $parcel->sorting_zone
                                                    ?: '—'
                                                }}
                                            </p>

                                        </div>


                                        <span>
                                            ₱{{ number_format(
                                                (float) (
                                                    $order?->total
                                                    ?? 0
                                                ),
                                                2
                                            ) }}
                                        </span>

                                    </div>

                                @endforeach

                            </div>

                        </div>


                        {{-- Create manifest --}}
                        <aside class="transfer-create-panel">

                            <h3>
                                Create Transfer Manifest
                            </h3>

                            <p>
                                Assign the linehaul vehicle and driver
                                before dispatch.
                            </p>


                            <form
                                method="POST"
                                action="{{ route(
                                    'logistics.hub-transfers.store'
                                ) }}"
                                data-create-transfer
                            >

                                @csrf


                                @foreach($group['parcels'] as $parcel)

                                    <input
                                        type="hidden"
                                        name="parcel_ids[]"
                                        value="{{ $parcel->id }}"
                                    >

                                @endforeach


                                <label>

                                    <span class="transfer-label">
                                        Vehicle Type
                                    </span>

                                    <select
                                        name="vehicle_type"
                                        required
                                    >
                                        <option value="">
                                            Select vehicle
                                        </option>

                                        <option value="Motorcycle">
                                            Motorcycle
                                        </option>

                                        <option value="Multicab">
                                            Multicab
                                        </option>

                                        <option value="L300">
                                            L300
                                        </option>

                                        <option value="Van">
                                            Van
                                        </option>

                                        <option value="Truck">
                                            Truck
                                        </option>
                                    </select>

                                </label>


                                <label>

                                    <span class="transfer-label">
                                        Plate Number
                                    </span>

                                    <input
                                        type="text"
                                        name="vehicle_plate"
                                        maxlength="40"
                                        placeholder="e.g. DEMO-1234"
                                    >

                                </label>


                                <label>

                                    <span class="transfer-label">
                                        Driver
                                    </span>

                                    <input
                                        type="text"
                                        name="driver_name"
                                        maxlength="140"
                                        required
                                        placeholder="Driver name"
                                    >

                                </label>


                                <label>

                                    <span class="transfer-label">
                                        Manifest Note
                                    </span>

                                    <textarea
                                        name="notes"
                                        rows="2"
                                        maxlength="1500"
                                        placeholder="Optional note…"
                                    ></textarea>

                                </label>


                                <div class="transfer-manifest-total">

                                    <span>
                                        Manifest load
                                    </span>

                                    <strong>
                                        {{ $group['parcel_count'] }}
                                    </strong>

                                </div>


                                <button
                                    type="submit"
                                    class="transfer-button transfer-button-primary"
                                >
                                    Create Manifest
                                </button>

                            </form>

                        </aside>

                    </div>

                </article>

            @empty

                <div class="transfer-empty">

                    <span class="transfer-empty-icon">
                        ✓
                    </span>

                    <h3>
                        No parcels waiting for linehaul
                    </h3>

                    <p>
                        Parcels appear here after sorting is completed
                        at the origin hub.
                    </p>

                    <a
                        href="{{ route('logistics.parcel-sorting') }}"
                        class="transfer-button transfer-button-primary"
                    >
                        View Parcel Sorting
                    </a>

                </div>

            @endforelse


            <div
                class="transfer-empty"
                data-ready-empty
                hidden
            >
                <h3>No matching route groups</h3>

                <p>
                    Try another hub, order number, Seller, or Buyer.
                </p>
            </div>

        </div>

    </section>


    {{-- =====================================================
        TRANSFER MANIFESTS
    ====================================================== --}}

    <section class="transfer-workspace transfer-active">

        <div class="transfer-toolbar">

            <div>

                <h2>
                    Transfer manifests
                </h2>

                <p>
                    Track preparing, in-transit, and completed
                    Hub Transfer operations.
                </p>

            </div>


            <label class="transfer-search">

                <span class="sr-only">
                    Search transfer manifests
                </span>

                <input
                    type="search"
                    data-transfer-search
                    placeholder="Search transfer, driver, vehicle…"
                >

            </label>

        </div>


        <div class="transfer-filter-row">

            <div
                class="transfer-tabs"
                role="group"
            >

                <button
                    type="button"
                    data-transfer-filter="all"
                    aria-pressed="true"
                >
                    All

                    <span>
                        {{ $transfers->count() }}
                    </span>
                </button>


                <button
                    type="button"
                    data-transfer-filter="preparing"
                    aria-pressed="false"
                >
                    Preparing

                    <span>
                        {{ $stats['preparing'] }}
                    </span>
                </button>


                <button
                    type="button"
                    data-transfer-filter="in_transit"
                    aria-pressed="false"
                >
                    In Transit

                    <span>
                        {{ $stats['in_transit'] }}
                    </span>
                </button>


                <button
                    type="button"
                    data-transfer-filter="received"
                    aria-pressed="false"
                >
                    Received
                </button>

            </div>

        </div>


        <div class="transfer-manifest-list">

            @forelse($transfers as $transfer)

                @php
                    $items =
                        $transferParcels->get(
                            $transfer->id,
                            collect()
                        );

                    $search =
                        implode(
                            ' ',
                            [
                                $transfer->transfer_code,
                                $transfer->origin_name,
                                $transfer->destination_name,
                                $transfer->driver_name,
                                $transfer->vehicle_type,
                                $transfer->vehicle_plate,
                            ]
                        );
                @endphp


                <article
                    class="transfer-manifest"
                    data-transfer-row
                    data-state="{{ $transfer->status }}"
                    data-search="{{ $search }}"
                >

                    <header class="transfer-manifest-header">

                        <div>

                            <p class="transfer-eyebrow">
                                TRANSFER MANIFEST
                            </p>

                            <h3>
                                {{ $transfer->transfer_code }}
                            </h3>

                        </div>


                        <span
                            class="
                                transfer-status
                                transfer-status-{{ $transfer->status }}
                            "
                        >
                            {{
                                strtoupper(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $transfer->status
                                    )
                                )
                            }}
                        </span>

                    </header>


                    <div class="transfer-manifest-body">

                        <div class="transfer-manifest-route">

                            <div>

                                <span class="transfer-label">
                                    Origin
                                </span>

                                <strong>
                                    {{ $transfer->origin_name }}
                                </strong>

                                <p>
                                    {{ $transfer->origin_municipality }}
                                </p>

                            </div>


                            <span class="transfer-linehaul-arrow">
                                →
                            </span>


                            <div>

                                <span class="transfer-label">
                                    Destination
                                </span>

                                <strong>
                                    {{ $transfer->destination_name }}
                                </strong>

                                <p>
                                    {{ $transfer->destination_municipality }}
                                </p>

                            </div>

                        </div>


                        <div class="transfer-meta-grid">

                            <div>

                                <span class="transfer-label">
                                    Vehicle
                                </span>

                                <strong>
                                    {{ $transfer->vehicle_type }}
                                </strong>

                                <p>
                                    {{
                                        $transfer->vehicle_plate
                                        ?: 'No plate recorded'
                                    }}
                                </p>

                            </div>


                            <div>

                                <span class="transfer-label">
                                    Driver
                                </span>

                                <strong>
                                    {{ $transfer->driver_name }}
                                </strong>

                            </div>


                            <div>

                                <span class="transfer-label">
                                    Parcels
                                </span>

                                <strong>
                                    {{ $items->count() }}
                                </strong>

                            </div>


                            <div>

                                <span class="transfer-label">
                                    Created
                                </span>

                                <strong>
                                    {{
                                        \Illuminate\Support\Carbon::parse(
                                            $transfer->created_at
                                        )
                                            ->timezone(
                                                'Asia/Manila'
                                            )
                                            ->format(
                                                'M d · g:i A'
                                            )
                                    }}
                                </strong>

                            </div>

                        </div>


                        <details class="transfer-details">

                            <summary>
                                View manifest parcels
                                <span>+</span>
                            </summary>


                            <div class="transfer-detail-body">

                                @foreach($items as $item)

                                    <div class="transfer-detail-row">

                                        <div>

                                            <strong>
                                                {{ $item->order_number }}
                                            </strong>

                                            <p>
                                                {{ $item->buyer_name }}
                                                ·
                                                {{ $item->sorting_zone }}
                                            </p>

                                        </div>


                                        <span>
                                            {{
                                                strtoupper(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $item->parcel_status
                                                    )
                                                )
                                            }}
                                        </span>

                                    </div>

                                @endforeach

                            </div>

                        </details>


                        @if($transfer->notes)

                            <div class="transfer-note">

                                <span class="transfer-label">
                                    Transfer Note
                                </span>

                                <p>
                                    {{ $transfer->notes }}
                                </p>

                            </div>

                        @endif

                    </div>


                    <footer class="transfer-manifest-footer">

                        <div>

                            @if($transfer->status === 'preparing')

                                <span>
                                    Manifest ready for departure
                                </span>

                            @elseif($transfer->status === 'in_transit')

                                <span>
                                    Dispatched
                                    {{
                                        \Illuminate\Support\Carbon::parse(
                                            $transfer->dispatched_at
                                        )->diffForHumans()
                                    }}
                                </span>

                            @else

                                <span>
                                    Destination receipt completed
                                </span>

                            @endif

                        </div>


                        @if($transfer->status === 'preparing')

                            <form
                                method="POST"
                                action="{{ route(
                                    'logistics.hub-transfers.dispatch',
                                    $transfer->id
                                ) }}"
                                data-dispatch-transfer
                                data-transfer-code="{{ $transfer->transfer_code }}"
                            >

                                @csrf


                                <button
                                    type="submit"
                                    class="transfer-button transfer-button-primary"
                                >
                                    Dispatch Transfer
                                </button>

                            </form>


                        @elseif($transfer->status === 'in_transit')

                            <form
                                method="POST"
                                action="{{ route(
                                    'logistics.hub-transfers.receive',
                                    $transfer->id
                                ) }}"
                                data-receive-transfer
                                data-transfer-code="{{ $transfer->transfer_code }}"
                            >

                                @csrf


                                <button
                                    type="submit"
                                    class="transfer-button transfer-button-primary"
                                >
                                    Confirm Destination Receipt
                                </button>

                            </form>


                        @elseif($transfer->status === 'received')

                            <a
                                href="{{ route(
                                    'logistics.last-mile-assignment'
                                ) }}"
                                class="transfer-text-link"
                            >
                                Continue to Last Mile →
                            </a>

                        @endif

                    </footer>

                </article>

            @empty

                <div class="transfer-empty">

                    <h3>
                        No Hub Transfer manifests yet
                    </h3>

                    <p>
                        Create a manifest from sorted parcels above.
                    </p>

                </div>

            @endforelse


            <div
                class="transfer-empty"
                data-transfer-empty
                hidden
            >
                <h3>
                    No matching transfer manifests
                </h3>
            </div>

        </div>


        <footer class="transfer-list-footer">

            <span
                data-transfer-visible-count
                aria-live="polite"
            >
                {{ $transfers->count() }}
                transfers shown
            </span>


            <span>
                Preparing → In Transit → Received
            </span>

        </footer>

    </section>

</div>

@endsection


@push('scripts')

<script>
(() => {
    'use strict';


    function initializeHubTransfers() {

        const root =
            document.querySelector(
                '[data-transfer-workspace]'
            );


        if (
            !root
            || root.dataset.initialized
        ) {
            return;
        }


        root.dataset.initialized =
            'true';


        const normalize = (value) => {
            return String(value || '')
                .toLocaleLowerCase()
                .trim();
        };


        /*
        |--------------------------------------------------------------------------
        | Ready routes search
        |--------------------------------------------------------------------------
        */

        const readySearch =
            root.querySelector(
                '[data-ready-search]'
            );


        const readyGroups = [
            ...root.querySelectorAll(
                '[data-ready-group]'
            )
        ];


        const readyEmpty =
            root.querySelector(
                '[data-ready-empty]'
            );


        function filterReady() {

            const keyword =
                normalize(
                    readySearch?.value
                );


            let visible = 0;


            readyGroups.forEach((card) => {

                const show =
                    normalize(
                        card.dataset.search
                    ).includes(keyword);


                card.hidden =
                    !show;


                if (show) {
                    visible++;
                }

            });


            if (readyEmpty) {

                readyEmpty.hidden =
                    readyGroups.length === 0
                    || visible > 0;
            }

        }


        readySearch?.addEventListener(
            'input',
            filterReady
        );


        /*
        |--------------------------------------------------------------------------
        | Transfer history search/filter
        |--------------------------------------------------------------------------
        */

        const transferSearch =
            root.querySelector(
                '[data-transfer-search]'
            );


        const transferRows = [
            ...root.querySelectorAll(
                '[data-transfer-row]'
            )
        ];


        const tabs = [
            ...root.querySelectorAll(
                '[data-transfer-filter]'
            )
        ];


        const transferEmpty =
            root.querySelector(
                '[data-transfer-empty]'
            );


        const visibleCount =
            root.querySelector(
                '[data-transfer-visible-count]'
            );


        let filter =
            'all';


        function filterTransfers() {

            const keyword =
                normalize(
                    transferSearch?.value
                );


            let visible =
                0;


            transferRows.forEach((row) => {

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
                    visible++;
                }

            });


            tabs.forEach((tab) => {

                tab.setAttribute(
                    'aria-pressed',
                    String(
                        tab.dataset.transferFilter
                        === filter
                    )
                );

            });


            if (transferEmpty) {

                transferEmpty.hidden =
                    transferRows.length === 0
                    || visible > 0;
            }


            if (visibleCount) {

                visibleCount.textContent =
                    `${visible} of ${transferRows.length} `
                    + 'transfers shown';
            }

        }


        tabs.forEach((tab) => {

            tab.addEventListener(
                'click',
                () => {

                    filter =
                        tab.dataset.transferFilter
                        || 'all';


                    filterTransfers();
                }
            );

        });


        transferSearch?.addEventListener(
            'input',
            filterTransfers
        );


        /*
        |--------------------------------------------------------------------------
        | Create manifest confirmation
        |--------------------------------------------------------------------------
        */

        root.querySelectorAll(
            '[data-create-transfer]'
        ).forEach((form) => {

            form.addEventListener(
                'submit',
                (event) => {

                    const count =
                        form.querySelectorAll(
                            'input[name="parcel_ids[]"]'
                        ).length;


                    const confirmed =
                        window.confirm(
                            `Create a Hub Transfer manifest `
                            + `containing ${count} `
                            + `${
                                count === 1
                                    ? 'parcel'
                                    : 'parcels'
                            }?`
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
                            'Creating manifest…';
                    }

                }
            );

        });


        /*
        |--------------------------------------------------------------------------
        | Dispatch
        |--------------------------------------------------------------------------
        */

        root.querySelectorAll(
            '[data-dispatch-transfer]'
        ).forEach((form) => {

            form.addEventListener(
                'submit',
                (event) => {

                    const code =
                        form.dataset.transferCode
                        || 'this transfer';


                    if (
                        !window.confirm(
                            `Dispatch ${code} from the origin hub?`
                        )
                    ) {
                        event.preventDefault();
                    }

                }
            );

        });


        /*
        |--------------------------------------------------------------------------
        | Receive
        |--------------------------------------------------------------------------
        */

        root.querySelectorAll(
            '[data-receive-transfer]'
        ).forEach((form) => {

            form.addEventListener(
                'submit',
                (event) => {

                    const code =
                        form.dataset.transferCode
                        || 'this transfer';


                    if (
                        !window.confirm(
                            `Confirm physical receipt of ${code} `
                            + 'at the destination hub?'
                        )
                    ) {
                        event.preventDefault();
                    }

                }
            );

        });


        filterReady();
        filterTransfers();

    }


    if (
        document.readyState === 'loading'
    ) {
        document.addEventListener(
            'DOMContentLoaded',
            initializeHubTransfers
        );
    } else {
        initializeHubTransfers();
    }

})();
</script>

@endpush