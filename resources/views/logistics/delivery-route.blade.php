@extends('layouts.logistics')

@section('title', 'Delivery Route | SARI Logistics')
@section('page-title', 'Delivery Route')

@php
    $isCompleted = $order->status === 'delivered';

    $routeStops = $stops
        ->sortBy('sequence')
        ->values();

    $routeStopsForMap = $routeStops
        ->map(function ($stop) {
            return [
                'sequence' => $stop->sequence,
                'key' => $stop->stop_key,
                'name' => $stop->name,
                'type' => $stop->type,
                'address' => $stop->address_snapshot,
                'lat' => $stop->latitude,
                'lng' => $stop->longitude,
            ];
        })
        ->values()
        ->all();

    $routeStopsJson = json_encode(
        $routeStopsForMap,
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_INVALID_UTF8_SUBSTITUTE
    );

    if ($routeStopsJson === false) {
        $routeStopsJson = '[]';
    }
@endphp


@push('styles')

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css"
>

<style>
    .sari-route-map {
        width: 100%;
        height: 540px;
        overflow: hidden;
        border-radius: 16px;
        background: #f3f2ef;
    }

    .sari-route-stop-card {
        position: relative;
        border: 1px solid #e9e2d8;
        border-radius: 14px;
        background: #fff;
        padding: 14px;
    }

    .sari-route-stop-card + .sari-route-stop-card::before {
        content: '';
        position: absolute;
        left: 28px;
        top: -17px;
        width: 1px;
        height: 17px;
        background: #dca640;
    }

    .sari-route-index {
        display: grid;
        width: 28px;
        height: 28px;
        flex: 0 0 28px;
        place-items: center;
        border-radius: 999px;
        background: #c98913;
        color: #fff;
        font-size: 8px;
        font-weight: 800;
    }

    .sari-route-stop-completed .sari-route-index {
        background: #c98913;
    }

    .sari-route-map-pin {
        display: grid;
        width: 30px;
        height: 30px;
        place-items: center;
        border: 3px solid #fff;
        border-radius: 999px;
        background: #c98913;
        color: #fff;
        font-size: 8px;
        font-weight: 800;
        box-shadow: 0 6px 16px rgba(45, 35, 22, .2);
    }

    .sari-route-map-pin.is-buyer {
        background: #2f2b27;
    }

    .sari-route-map-pin.is-origin {
        background: #c98913;
    }

    .sari-route-map-pin.is-destination {
        background: #477a58;
    }

    .leaflet-container img {
        max-width: none !important;
    }

    .sari-route-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        margin-bottom: 12px;
    }

    .sari-route-summary-card {
        min-width: 0;
        border: 1px solid #ece3d7;
        border-radius: 12px;
        background: #fffdf9;
        padding: 11px 12px;
    }

    .sari-route-summary-label {
        font-size: 6.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .09em;
        color: #9b9185;
    }

    .sari-route-summary-value {
        margin-top: 4px;
        font-size: 11px;
        font-weight: 800;
        color: #342e28;
    }

    .sari-route-summary-note {
        margin-top: 2px;
        font-size: 6.5px;
        color: #9b9185;
    }

    .sari-route-status {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 9px;
        border: 1px solid #eadfcf;
        border-radius: 10px;
        background: #fffaf1;
        padding: 9px 11px;
        font-size: 7px;
        line-height: 1.5;
        color: #8d7448;
    }

    .sari-route-status-dot {
        width: 7px;
        height: 7px;
        flex: 0 0 7px;
        border-radius: 999px;
        background: #d9930a;
    }

    .sari-route-status.is-ready {
        border-color: #cfe2d5;
        background: #f3faf5;
        color: #4b7359;
    }

    .sari-route-status.is-ready .sari-route-status-dot {
        background: #4f8a61;
    }

    .sari-route-status.is-error {
        border-color: #eccdcd;
        background: #fff5f5;
        color: #a55a5a;
    }

    .sari-route-status.is-error .sari-route-status-dot {
        background: #c96363;
    }

    .sari-leg-metric {
        margin-top: 9px;
        border-radius: 9px;
        background: #faf7f1;
        padding: 8px 10px;
        font-size: 6.5px;
        line-height: 1.5;
        color: #897f74;
    }

    .sari-leg-metric strong {
        color: #5f574e;
    }

    .sari-history-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 8px;
        border: 1px solid #eadfcf;
        background: #fff8ea;
        padding: 7px 10px;
        font-size: 7px;
        font-weight: 700;
        color: #9d6810;
    }

    .sari-history-badge::before {
        content: '✓';
        font-weight: 900;
    }

    .sari-completed-banner {
        border: 1px solid #eadfcf;
        border-radius: 12px;
        background: #fffaf1;
        padding: 12px 14px;
    }

    @media (max-width: 768px) {
        .sari-route-map {
            height: 420px;
        }

        .sari-route-summary {
            grid-template-columns: 1fr;
        }
    }
</style>

@endpush


@section('content')

<div class="mx-auto max-w-[1500px] space-y-4">

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div
        class="
            rounded-[20px]
            border border-[#ebe4da]
            bg-white
            p-5
        "
    >

        <a
            href="{{ route('logistics.delivery-monitoring') }}"
            class="
                text-[8px]
                font-semibold
                text-[#948a80]
                hover:text-[#a96f09]
            "
        >
            ← Back to Delivery Monitoring
        </a>


        <div
            class="
                mt-3
                flex flex-col gap-4
                lg:flex-row
                lg:items-end
                lg:justify-between
            "
        >

            <div>

                <div class="flex flex-wrap items-center gap-2">

                    <p
                        class="
                            text-[8px]
                            font-bold
                            uppercase
                            tracking-[.12em]
                            text-[#a96f09]
                        "
                    >
                        {{ $isCompleted
                            ? 'Completed Delivery Journey'
                            : 'Automatic Road Routing' }}
                    </p>


                    @if($isCompleted)

                        <span
                            class="
                                rounded-full
                                bg-[#eaf7ee]
                                px-2.5 py-1
                                text-[6px]
                                font-bold
                                uppercase
                                tracking-wide
                                text-[#357750]
                            "
                        >
                            ✓ Completed
                        </span>

                    @endif

                </div>


                <h1
                    class="
                        mt-1
                        text-[20px]
                        font-bold
                        tracking-[-.025em]
                        text-[#2f2923]
                    "
                >
                    {{ $order->order_number }}
                </h1>


                <p
                    class="
                        mt-1
                        text-[8.5px]
                        text-[#8d8379]
                    "
                >
                    Seller
                    →
                    Origin Hub
                    →
                    Destination Hub
                    →
                    Buyer
                </p>

            </div>


            @if(!$isCompleted)

                <form
                    method="POST"
                    action="{{
                        route(
                            'logistics.delivery-routes.update',
                            $order
                        )
                    }}"
                >
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="
                            h-10
                            rounded-xl
                            border border-[#e1c58e]
                            bg-[#fff9ed]
                            px-4
                            text-[8px]
                            font-bold
                            text-[#97630b]
                            transition
                            hover:bg-[#fff3dc]
                        "
                    >
                        Refresh nearest facilities
                    </button>

                </form>

            @else

                <div
                    class="
                        rounded-xl
                        border border-[#cfe4d6]
                        bg-[#f3faf5]
                        px-4 py-3
                        text-right
                    "
                >
                    <p
                        class="
                            text-[7px]
                            font-bold
                            uppercase
                            tracking-wide
                            text-[#49815c]
                        "
                    >
                        Historical Route
                    </p>

                    <p
                        class="
                            mt-1
                            text-[8px]
                            font-semibold
                            text-[#596d60]
                        "
                    >
                        Read-only after delivery
                    </p>
                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
        COMPLETED JOURNEY NOTICE
    ========================================================== --}}

    @if($isCompleted)

        <div class="sari-completed-banner">

            <div class="flex items-start gap-3">

                <div
                    class="
                        grid
                        h-8 w-8
                        shrink-0
                        place-items-center
                        rounded-full
                        bg-[#dff3e5]
                        text-[11px]
                        font-bold
                        text-[#347650]
                    "
                >
                    ✓
                </div>

                <div>

                    <p
                        class="
                            text-[9px]
                            font-bold
                            text-[#356b4a]
                        "
                    >
                        Delivery journey completed
                    </p>

                    <p
                        class="
                            mt-1
                            text-[7.5px]
                            leading-4
                            text-[#6e8374]
                        "
                    >
                        This route is preserved as a historical logistics
                        record. Facility selection and milestone actions
                        are locked after final Buyer delivery.
                    </p>

                </div>

            </div>

        </div>

    @endif


    {{-- =========================================================
        MESSAGES
    ========================================================== --}}

    @if(session('success'))

        <div
            class="
                rounded-xl
                border border-emerald-200
                bg-emerald-50
                px-4 py-3
                text-[9px]
                font-semibold
                text-emerald-700
            "
        >
            {{ session('success') }}
        </div>

    @endif


    @if($errors->any())

        <div
            class="
                rounded-xl
                border border-red-200
                bg-red-50
                px-4 py-3
                text-[9px]
                text-red-700
            "
        >
            {{ $errors->first() }}
        </div>

    @endif


    {{-- =========================================================
        CONTENT
    ========================================================== --}}

    <div
        class="
            grid gap-4
            xl:grid-cols-[390px_minmax(0,1fr)]
        "
    >

        {{-- =====================================================
            HANDOFF STOPS
        ====================================================== --}}

        <section
            class="
                rounded-[20px]
                border border-[#ebe4da]
                bg-[#fcfbf8]
                p-4
            "
        >

            <div
                class="
                    flex
                    items-center
                    justify-between
                    gap-3
                "
            >

                <p
                    class="
                        text-[8px]
                        font-bold
                        uppercase
                        tracking-[.1em]
                        text-[#a57115]
                    "
                >
                    Delivery handoff
                </p>


                @if($isCompleted)

                    <span
                        class="
                            rounded-full
                            bg-[#ecf8f0]
                            px-2.5 py-1
                            text-[6px]
                            font-bold
                            text-[#367750]
                        "
                    >
                        ROUTE COMPLETE
                    </span>

                @endif

            </div>


            <div class="mt-3 space-y-4">

                @forelse($routeStops as $stop)

                    <article
                        class="
                            sari-route-stop-card
                            {{ $stop->reached_at ? 'sari-route-stop-completed' : '' }}
                        "
                        data-route-stop-card="{{ $stop->sequence }}"
                    >

                        <div class="flex items-start gap-3">

                            <span class="sari-route-index">
                                {{ $stop->sequence }}
                            </span>


                            <div class="min-w-0 flex-1">

                                <div
                                    class="
                                        flex flex-wrap
                                        items-center
                                        gap-2
                                    "
                                >

                                    <p
                                        class="
                                            text-[9.5px]
                                            font-bold
                                            text-[#3c3630]
                                        "
                                    >
                                        {{ $stop->name }}
                                    </p>


                                    @if($stop->stop_key === 'seller')

                                        <span
                                            class="
                                                rounded-full
                                                bg-[#f6f1e8]
                                                px-2 py-1
                                                text-[6px]
                                                font-bold
                                                text-[#796b5b]
                                            "
                                        >
                                            SELLER
                                        </span>

                                    @elseif($stop->stop_key === 'origin_facility')

                                        <span
                                            class="
                                                rounded-full
                                                bg-[#fff4dc]
                                                px-2 py-1
                                                text-[6px]
                                                font-bold
                                                text-[#9d6810]
                                            "
                                        >
                                            ORIGIN HUB
                                        </span>

                                    @elseif($stop->stop_key === 'destination_facility')

                                        <span
                                            class="
                                                rounded-full
                                                bg-[#eaf5ed]
                                                px-2 py-1
                                                text-[6px]
                                                font-bold
                                                text-[#4c795a]
                                            "
                                        >
                                            DESTINATION HUB
                                        </span>

                                    @elseif($stop->stop_key === 'buyer')

                                        <span
                                            class="
                                                rounded-full
                                                bg-[#efefef]
                                                px-2 py-1
                                                text-[6px]
                                                font-bold
                                                text-[#5e5852]
                                            "
                                        >
                                            BUYER
                                        </span>

                                    @endif

                                </div>


                                <p
                                    class="
                                        mt-1
                                        text-[7.5px]
                                        leading-4
                                        text-[#8f857b]
                                    "
                                >
                                    {{
                                        $stop->address_snapshot
                                            ?: 'Location is being resolved.'
                                    }}
                                </p>


                                @if(
                                    $stop->latitude !== null
                                    &&
                                    $stop->longitude !== null
                                )

                                    <p
                                        class="
                                            mt-1
                                            text-[6.5px]
                                            text-[#aaa097]
                                        "
                                    >
                                        {{
                                            number_format(
                                                (float) $stop->latitude,
                                                5
                                            )
                                        }},
                                        {{
                                            number_format(
                                                (float) $stop->longitude,
                                                5
                                            )
                                        }}
                                    </p>

                                @endif


                                {{-- =====================================
                                    ROAD LEG
                                ====================================== --}}

                                @if(!$loop->last)

                                    <div
                                        id="sariLegMetric{{ $loop->index }}"
                                        class="sari-leg-metric"
                                    >
                                        <strong>Road leg:</strong>
                                        Calculating route...
                                    </div>

                                @endif


                                {{-- =====================================
                                    MILESTONE
                                ====================================== --}}

                                <div class="mt-3">

                                    @if($stop->reached_at)

                                        <span class="sari-history-badge">
                                            Reached
                                            {{
                                                $stop
                                                    ->reached_at
                                                    ->format(
                                                        'M d · h:i A'
                                                    )
                                            }}
                                        </span>

                                    @elseif(
                                        !$isCompleted
                                        &&
                                        $stop->type === 'sorting_facility'
                                    )

                                        <form
                                            method="POST"
                                            action="{{
                                                route(
                                                    'logistics.delivery-routes.stops.reached',
                                                    [
                                                        $order,
                                                        $stop,
                                                    ]
                                                )
                                            }}"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="
                                                    rounded-lg
                                                    bg-[#2f2b27]
                                                    px-3 py-2
                                                    text-[7px]
                                                    font-bold
                                                    text-white
                                                    transition
                                                    hover:bg-black
                                                "
                                            >
                                                Mark facility reached
                                            </button>

                                        </form>

                                    @elseif($isCompleted)

                                        <span
                                            class="
                                                inline-flex
                                                rounded-lg
                                                bg-[#f4f2ef]
                                                px-3 py-2
                                                text-[7px]
                                                font-semibold
                                                text-[#80786f]
                                            "
                                        >
                                            Historical milestone
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </article>

                @empty

                    <div
                        class="
                            rounded-[14px]
                            border border-dashed
                            border-[#e4dbce]
                            bg-white
                            px-4 py-8
                            text-center
                        "
                    >

                        <p
                            class="
                                text-[9px]
                                font-semibold
                                text-[#5d554c]
                            "
                        >
                            No delivery route stops found
                        </p>

                    </div>

                @endforelse

            </div>


            {{-- =================================================
                RIDER MODEL
            ================================================== --}}

            <div
                class="
                    mt-4
                    rounded-xl
                    border border-[#eee5d8]
                    bg-white
                    p-3
                "
            >

                <p
                    class="
                        text-[8px]
                        font-bold
                        text-[#51483f]
                    "
                >
                    Rider handoff model
                </p>

                <p
                    class="
                        mt-1
                        text-[7.5px]
                        leading-4
                        text-[#91877d]
                    "
                >
                    Pickup rider:
                    Seller → Origin Facility
                    <br>

                    Transfer vehicle:
                    Origin → Destination Facility
                    <br>

                    Final-mile rider:
                    Destination Facility → Buyer
                </p>

            </div>

        </section>


        {{-- =====================================================
            ROAD ROUTE MAP
        ====================================================== --}}

        <section
            class="
                rounded-[20px]
                border border-[#ebe4da]
                bg-white
                p-3
            "
        >

            <div
                class="
                    mb-3
                    flex flex-col gap-3
                    px-1
                    md:flex-row
                    md:items-center
                    md:justify-between
                "
            >

                <div>

                    <p
                        class="
                            text-[10px]
                            font-bold
                            text-[#37312c]
                        "
                    >
                        {{
                            $isCompleted
                                ? 'Completed road journey'
                                : 'Drivable road route'
                        }}
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[7.5px]
                            text-[#958b81]
                        "
                    >
                        {{
                            $isCompleted
                                ? 'Historical road geometry for the completed logistics journey.'
                                : 'Road geometry is calculated through the road network while preserving the SARI logistics handoff sequence.'
                        }}
                    </p>

                </div>


                <span
                    class="
                        self-start
                        rounded-full
                        bg-[#fff7e7]
                        px-3 py-1.5
                        text-[7px]
                        font-bold
                        text-[#9b670c]
                        md:self-auto
                    "
                >
                    {{
                        $routeStops
                            ->where(
                                'type',
                                'sorting_facility'
                            )
                            ->count()
                    }}
                    FACILITIES
                </span>

            </div>


            {{-- ROUTE SUMMARY --}}

            <div class="sari-route-summary">

                <div class="sari-route-summary-card">

                    <p class="sari-route-summary-label">
                        Road Distance
                    </p>

                    <p
                        id="sariRouteDistance"
                        class="sari-route-summary-value"
                    >
                        Calculating...
                    </p>

                    <p class="sari-route-summary-note">
                        Total drivable route
                    </p>

                </div>


                <div class="sari-route-summary-card">

                    <p class="sari-route-summary-label">
                        Estimated Travel
                    </p>

                    <p
                        id="sariRouteDuration"
                        class="sari-route-summary-value"
                    >
                        Calculating...
                    </p>

                    <p class="sari-route-summary-note">
                        Road routing estimate
                    </p>

                </div>


                <div class="sari-route-summary-card">

                    <p class="sari-route-summary-label">
                        Route Stops
                    </p>

                    <p class="sari-route-summary-value">
                        {{ $routeStops->count() }}
                    </p>

                    <p class="sari-route-summary-note">
                        Seller, hubs and Buyer
                    </p>

                </div>

            </div>


            <div
                id="sariRouteStatus"
                class="sari-route-status"
            >

                <span class="sari-route-status-dot"></span>

                <span>
                    Calculating actual drivable route...
                </span>

            </div>


            <div
                id="sariRoutePlannerMap"
                class="sari-route-map mt-3"
                data-route-stops="{{ $routeStopsJson }}"
            ></div>

        </section>

    </div>

</div>

@endsection


@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const mapNode =
            document.getElementById(
                'sariRoutePlannerMap'
            );

        const statusNode =
            document.getElementById(
                'sariRouteStatus'
            );

        const distanceNode =
            document.getElementById(
                'sariRouteDistance'
            );

        const durationNode =
            document.getElementById(
                'sariRouteDuration'
            );


        if (
            !mapNode
            ||
            !window.L
        ) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Parse Stops
        |--------------------------------------------------------------------------
        */

        let stops = [];


        try {

            const parsed =
                JSON.parse(
                    mapNode.dataset.routeStops
                    || '[]'
                );


            if (Array.isArray(parsed)) {

                stops =
                    parsed.filter(
                        function (stop) {

                            const lat =
                                Number(stop.lat);

                            const lng =
                                Number(stop.lng);


                            return (
                                Number.isFinite(lat)
                                &&
                                Number.isFinite(lng)
                                &&
                                lat >= 4
                                &&
                                lat <= 22.5
                                &&
                                lng >= 116
                                &&
                                lng <= 127.5
                            );

                        }
                    );
            }

        } catch (error) {

            console.error(
                'Unable to parse SARI delivery route stops.',
                error
            );

            stops = [];
        }


        const PH_BOUNDS =
            L.latLngBounds(
                [4, 116],
                [22.5, 127.5]
            );


        const map =
            L.map(
                mapNode,
                {
                    zoomControl: true,

                    maxBounds:
                        PH_BOUNDS,

                    maxBoundsViscosity:
                        1,

                    minZoom:
                        5,
                }
            )
            .setView(
                [
                    12.8797,
                    121.7740,
                ],
                6
            );


        L.tileLayer(
            'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,

                attribution:
                    '&copy; OpenStreetMap contributors',
            }
        ).addTo(map);


        /*
        |--------------------------------------------------------------------------
        | Helpers
        |--------------------------------------------------------------------------
        */

        function markerLabel(stop) {

            if (stop.key === 'seller') {
                return 'S';
            }

            if (stop.key === 'origin_facility') {
                return 'O';
            }

            if (stop.key === 'destination_facility') {
                return 'D';
            }

            if (stop.key === 'buyer') {
                return 'B';
            }

            return String(
                stop.sequence
                || '•'
            );
        }


        function markerClass(stop) {

            if (stop.key === 'buyer') {
                return 'is-buyer';
            }

            if (stop.key === 'origin_facility') {
                return 'is-origin';
            }

            if (stop.key === 'destination_facility') {
                return 'is-destination';
            }

            return '';
        }


        function formatDistance(meters) {

            const value =
                Number(meters);


            if (!Number.isFinite(value)) {
                return '—';
            }


            if (value < 1000) {
                return Math.round(value) + ' m';
            }


            return (
                (value / 1000).toFixed(1)
                + ' km'
            );
        }


        function formatDuration(seconds) {

            const value =
                Math.max(
                    0,
                    Number(seconds) || 0
                );


            const minutes =
                Math.round(
                    value / 60
                );


            if (minutes < 60) {
                return minutes + ' min';
            }


            const hours =
                Math.floor(
                    minutes / 60
                );


            const remainder =
                minutes % 60;


            if (remainder === 0) {

                return (
                    hours
                    +
                    (
                        hours === 1
                            ? ' hr'
                            : ' hrs'
                    )
                );
            }


            return (
                hours
                +
                (
                    hours === 1
                        ? ' hr '
                        : ' hrs '
                )
                +
                remainder
                +
                ' min'
            );
        }


        function setRouteStatus(
            message,
            state
        ) {

            if (!statusNode) {
                return;
            }


            statusNode.classList.remove(
                'is-ready',
                'is-error'
            );


            if (state === 'ready') {
                statusNode.classList.add('is-ready');
            }


            if (state === 'error') {
                statusNode.classList.add('is-error');
            }


            const text =
                statusNode.querySelector(
                    'span:last-child'
                );


            if (text) {
                text.textContent = message;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Add Stop Markers
        |--------------------------------------------------------------------------
        */

        const waypointBounds = [];


        stops.forEach(
            function (stop) {

                const lat =
                    Number(stop.lat);

                const lng =
                    Number(stop.lng);


                waypointBounds.push(
                    [
                        lat,
                        lng,
                    ]
                );


                const marker =
                    L.marker(
                        [
                            lat,
                            lng,
                        ],
                        {
                            icon:
                                L.divIcon(
                                    {
                                        className: '',

                                        html:
                                            '<span class="sari-route-map-pin '
                                            +
                                            markerClass(stop)
                                            +
                                            '">'
                                            +
                                            markerLabel(stop)
                                            +
                                            '</span>',

                                        iconSize:
                                            [
                                                30,
                                                30,
                                            ],

                                        iconAnchor:
                                            [
                                                15,
                                                15,
                                            ],
                                    }
                                ),
                        }
                    )
                    .addTo(map);


                const tooltipLines =
                    [
                        stop.name || 'Route stop',
                        stop.address || '',
                    ]
                    .filter(Boolean)
                    .join('<br>');


                marker.bindTooltip(
                    tooltipLines,
                    {
                        direction: 'top',

                        offset:
                            [
                                0,
                                -12,
                            ],
                    }
                );

            }
        );


        if (waypointBounds.length >= 2) {

            map.fitBounds(
                L.latLngBounds(
                    waypointBounds
                ),
                {
                    padding:
                        [
                            50,
                            50,
                        ],
                }
            );

        } else if (
            waypointBounds.length === 1
        ) {

            map.setView(
                waypointBounds[0],
                13
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Per-Leg Information
        |--------------------------------------------------------------------------
        */

        function updateLegMetrics(legs) {

            if (!Array.isArray(legs)) {
                return;
            }


            legs.forEach(
                function (
                    leg,
                    index
                ) {

                    const node =
                        document.getElementById(
                            'sariLegMetric'
                            + index
                        );


                    if (!node) {
                        return;
                    }


                    const from =
                        stops[index];

                    const to =
                        stops[index + 1];


                    node.innerHTML =
                        '<strong>'
                        +
                        (from?.name || 'Stop')
                        +
                        ' → '
                        +
                        (to?.name || 'Next stop')
                        +
                        '</strong>'
                        +
                        '<br>'
                        +
                        formatDistance(
                            leg.distance
                        )
                        +
                        ' · '
                        +
                        formatDuration(
                            leg.duration
                        );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Fallback Route
        |--------------------------------------------------------------------------
        */

        function drawFallbackRoute() {

            if (waypointBounds.length < 2) {
                return;
            }


            L.polyline(
                waypointBounds,
                {
                    color: '#b7a88f',

                    weight: 3,

                    opacity: .75,

                    dashArray: '8, 8',

                    lineCap: 'round',

                    lineJoin: 'round',
                }
            ).addTo(map);


            distanceNode.textContent =
                'Unavailable';

            durationNode.textContent =
                'Unavailable';


            setRouteStatus(
                'Road routing service is unavailable. Showing straight-line fallback.',
                'error'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | OSRM Road Route
        |--------------------------------------------------------------------------
        */

        async function loadRoadRoute() {

            if (stops.length < 2) {

                setRouteStatus(
                    'At least two valid route stops are required.',
                    'error'
                );

                return;
            }


            const coordinates =
                stops
                    .map(
                        function (stop) {

                            return (
                                Number(stop.lng)
                                +
                                ','
                                +
                                Number(stop.lat)
                            );
                        }
                    )
                    .join(';');


            const url =
                'https://router.project-osrm.org/route/v1/driving/'
                +
                coordinates
                +
                '?overview=full'
                +
                '&geometries=geojson'
                +
                '&steps=false'
                +
                '&alternatives=false';


            try {

                const response =
                    await fetch(
                        url,
                        {
                            method: 'GET',

                            headers:
                                {
                                    Accept:
                                        'application/json',
                                },
                        }
                    );


                if (!response.ok) {

                    throw new Error(
                        'Routing request failed with HTTP '
                        +
                        response.status
                    );
                }


                const payload =
                    await response.json();


                if (
                    payload.code !== 'Ok'
                    ||
                    !Array.isArray(
                        payload.routes
                    )
                    ||
                    !payload.routes.length
                ) {

                    throw new Error(
                        payload.message
                        ||
                        'No drivable route returned.'
                    );
                }


                const route =
                    payload.routes[0];


                const geometry =
                    route.geometry;


                if (
                    !geometry
                    ||
                    geometry.type !== 'LineString'
                    ||
                    !Array.isArray(
                        geometry.coordinates
                    )
                ) {

                    throw new Error(
                        'Invalid road geometry returned.'
                    );
                }


                const roadPoints =
                    geometry.coordinates
                        .map(
                            function (
                                coordinate
                            ) {

                                return [
                                    Number(
                                        coordinate[1]
                                    ),
                                    Number(
                                        coordinate[0]
                                    ),
                                ];
                            }
                        )
                        .filter(
                            function (
                                coordinate
                            ) {

                                return (
                                    Number.isFinite(
                                        coordinate[0]
                                    )
                                    &&
                                    Number.isFinite(
                                        coordinate[1]
                                    )
                                );
                            }
                        );


                if (roadPoints.length < 2) {

                    throw new Error(
                        'Road geometry contained too few points.'
                    );
                }


                const roadLine =
                    L.polyline(
                        roadPoints,
                        {
                            color: '#c98913',

                            weight: 5,

                            opacity: .95,

                            lineCap: 'round',

                            lineJoin: 'round',
                        }
                    )
                    .addTo(map);


                map.fitBounds(
                    roadLine.getBounds(),
                    {
                        padding:
                            [
                                45,
                                45,
                            ],
                    }
                );


                distanceNode.textContent =
                    formatDistance(
                        route.distance
                    );


                durationNode.textContent =
                    formatDuration(
                        route.duration
                    );


                updateLegMetrics(
                    route.legs
                );


                setRouteStatus(
                    @json(
                        $isCompleted
                            ? 'Completed journey road route loaded successfully.'
                            : 'Actual drivable road route loaded successfully.'
                    ),
                    'ready'
                );

            } catch (error) {

                console.error(
                    'SARI road routing error:',
                    error
                );


                drawFallbackRoute();
            }
        }


        loadRoadRoute();

    }
);
</script>

@endpush