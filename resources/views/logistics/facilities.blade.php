@extends('layouts.logistics')

@section('title', 'Sorting Facilities | SARI Logistics')
@section('page-title', 'Sorting Facilities')

@push('styles')
<link
    rel="stylesheet"
    href="https://unpkg.com/maplibre-gl@5.7.1/dist/maplibre-gl.css"
>

<style>
    #sariFacilityMap {
        width: 100%;
        height: 270px;
        border-radius: 14px;
        overflow: hidden;
        background: #eef1ed;
    }

    #sariFacilityMap .maplibregl-canvas {
        outline: none;
    }

    #sariFacilityMap .maplibregl-ctrl-group {
        overflow: hidden;
        border: 1px solid #e7ddcf;
        border-radius: 9px;
        box-shadow: 0 7px 20px rgba(64, 50, 34, .08);
    }

    #sariFacilityMap .maplibregl-ctrl-attrib {
        font-size: 6px;
    }

    .sari-facility-pin {
        display: grid;
        width: 28px;
        height: 28px;
        place-items: center;
        border: 3px solid #fff;
        border-radius: 999px;
        background: #d9930a;
        color: #fff;
        font-size: 8px;
        font-weight: 800;
        box-shadow: 0 7px 18px rgba(67, 49, 22, .24);
    }
</style>
@endpush


@section('content')

<div
    class="
        mx-auto w-full
        max-w-[1800px]
        space-y-4
    "
>

    {{-- =====================================================
        FLASH MESSAGES
    ====================================================== --}}

    @if(session('success'))
        <div
            class="
                rounded-[16px]
                border border-[#bfe0cd]
                bg-[#f1faf5]
                px-5 py-4
                text-[9px]
                font-medium
                text-[#327a53]
            "
        >
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div
            class="
                rounded-[16px]
                border border-[#efc6c6]
                bg-[#fff4f4]
                px-5 py-4
                text-[9px]
                font-medium
                text-[#b53e3e]
            "
        >
            {{ $errors->first() }}
        </div>
    @endif


    {{-- =====================================================
        HERO
    ====================================================== --}}

    <section
        class="
            relative overflow-hidden
            rounded-[20px]
            border border-[#e8dfd1]
            bg-white
            shadow-[0_5px_22px_rgba(51,39,25,.035)]
        "
    >
        <div
            class="
                pointer-events-none
                absolute -right-[100px] -top-[130px]
                h-[280px] w-[280px]
                rounded-full
                bg-[#f4d58b]/20
                blur-2xl
            "
        ></div>

        <div
            class="
                relative
                flex flex-col gap-4
                px-5 py-5
                sm:px-6
                xl:flex-row
                xl:items-center
                xl:justify-between
            "
        >

            <div class="flex min-w-0 items-start gap-3.5">

                <div
                    class="
                        grid h-[42px] w-[42px]
                        shrink-0 place-items-center
                        rounded-[13px]
                        bg-[#fff6e5]
                        text-[#b97805]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[20px] w-[20px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M3 21h18"></path>
                        <path d="M5 21V8l7-4 7 4v13"></path>
                        <path d="M8 12h3"></path>
                        <path d="M13 12h3"></path>
                        <path d="M8 16h8"></path>
                    </svg>
                </div>

                <div class="min-w-0">

                    <p
                        class="
                            text-[7.5px]
                            font-bold
                            uppercase
                            tracking-[.14em]
                            text-[#b97805]
                        "
                    >
                        Logistics Network
                    </p>

                    <h2
                        class="
                            mt-1
                            text-[20px]
                            font-bold
                            tracking-[-.035em]
                            text-[#211d17]
                        "
                    >
                        Manage sorting facilities
                    </h2>

                    <p
                        class="
                            mt-1
                            max-w-[760px]
                            text-[8.5px]
                            leading-[18px]
                            text-[#807668]
                        "
                    >
                        Register and maintain the physical hubs used by
                        {{ $logistics->displayName() }} for parcel intake,
                        sorting, inter-hub movement, and delivery routing.
                    </p>

                </div>

            </div>


            {{-- Routing status --}}
            <div
                class="
                    flex items-center gap-3
                    rounded-[13px]
                    border
                    px-4 py-3

                    {{
                        $stats['routing_ready']
                            ? 'border-[#c9dfd0] bg-[#f4faf6]'
                            : 'border-[#ead7ae] bg-[#fff9ec]'
                    }}
                "
            >

                <span
                    class="
                        grid h-8 w-8
                        place-items-center
                        rounded-[9px]

                        {{
                            $stats['routing_ready']
                                ? 'bg-[#e6f4eb] text-[#4f7c60]'
                                : 'bg-[#fff0cf] text-[#aa7413]'
                        }}
                    "
                >
                    @if($stats['routing_ready'])

                        <svg
                            viewBox="0 0 24 24"
                            class="h-[15px] w-[15px]"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="m7 12 3 3 7-7"></path>
                        </svg>

                    @else

                        <svg
                            viewBox="0 0 24 24"
                            class="h-[15px] w-[15px]"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 8v5"></path>
                            <path d="M12 16h.01"></path>
                        </svg>

                    @endif
                </span>

                <div>

                    <p
                        class="
                            text-[6.5px]
                            font-bold
                            uppercase
                            tracking-[.1em]

                            {{
                                $stats['routing_ready']
                                    ? 'text-[#6d8f79]'
                                    : 'text-[#a88342]'
                            }}
                        "
                    >
                        Network Status
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[9px]
                            font-bold

                            {{
                                $stats['routing_ready']
                                    ? 'text-[#416a52]'
                                    : 'text-[#9b6b17]'
                            }}
                        "
                    >
                        {{
                            $stats['routing_ready']
                                ? 'Routing Ready'
                                : 'Needs 2 Active Hubs'
                        }}
                    </p>

                </div>

            </div>

        </div>
    </section>


    {{-- =====================================================
        METRICS
    ====================================================== --}}

    <section
        class="
            grid gap-3
            sm:grid-cols-2
            xl:grid-cols-4
        "
    >

        {{-- Total --}}
        <div
            class="
                rounded-[16px]
                border border-[#e8dfd1]
                bg-white
                p-4
                shadow-[0_4px_18px_rgba(51,39,25,.035)]
            "
        >
            <div class="flex items-start justify-between">

                <div>

                    <p
                        class="
                            text-[7px]
                            font-bold uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Total Facilities
                    </p>

                    <p
                        class="
                            mt-1.5
                            text-[23px]
                            font-bold
                            tracking-[-.04em]
                            text-[#272119]
                        "
                    >
                        {{ $stats['total'] }}
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[7.5px]
                            text-[#918779]
                        "
                    >
                        Registered hubs
                    </p>

                </div>

                <span
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-[11px]
                        bg-[#f4f1ed]
                        text-[#756b5f]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[17px] w-[17px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M3 21h18"></path>
                        <path d="M5 21V8l7-4 7 4v13"></path>
                    </svg>
                </span>

            </div>
        </div>


        {{-- Active --}}
        <div
            class="
                rounded-[16px]
                border border-[#e8dfd1]
                bg-white
                p-4
                shadow-[0_4px_18px_rgba(51,39,25,.035)]
            "
        >
            <div class="flex items-start justify-between">

                <div>

                    <p
                        class="
                            text-[7px]
                            font-bold uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Active Hubs
                    </p>

                    <p
                        class="
                            mt-1.5
                            text-[23px]
                            font-bold
                            tracking-[-.04em]
                            text-[#272119]
                        "
                    >
                        {{ $stats['active'] }}
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[7.5px]
                            text-[#918779]
                        "
                    >
                        Eligible for new routes
                    </p>

                </div>

                <span
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-[11px]
                        bg-[#edf8f1]
                        text-[#327a53]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[17px] w-[17px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="m8 12 2.5 2.5L16 9"></path>
                    </svg>
                </span>

            </div>
        </div>


        {{-- Used --}}
        <div
            class="
                rounded-[16px]
                border border-[#e8dfd1]
                bg-white
                p-4
                shadow-[0_4px_18px_rgba(51,39,25,.035)]
            "
        >
            <div class="flex items-start justify-between">

                <div>

                    <p
                        class="
                            text-[7px]
                            font-bold uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Used in Routes
                    </p>

                    <p
                        class="
                            mt-1.5
                            text-[23px]
                            font-bold
                            tracking-[-.04em]
                            text-[#272119]
                        "
                    >
                        {{ $stats['used'] }}
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[7.5px]
                            text-[#918779]
                        "
                    >
                        Hubs with route history
                    </p>

                </div>

                <span
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-[11px]
                        bg-[#eef5fb]
                        text-[#4d79a1]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[17px] w-[17px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="6" cy="18" r="2"></circle>
                        <circle cx="18" cy="6" r="2"></circle>
                        <path d="M8 18h3a3 3 0 0 0 3-3V9a3 3 0 0 1 3-3"></path>
                    </svg>
                </span>

            </div>
        </div>


        {{-- Availability --}}
        <div
            class="
                rounded-[16px]
                border border-[#e8dfd1]
                bg-white
                p-4
                shadow-[0_4px_18px_rgba(51,39,25,.035)]
            "
        >
            <div class="flex items-start justify-between">

                <div>

                    <p
                        class="
                            text-[7px]
                            font-bold uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Network Readiness
                    </p>

                    <p
                        class="
                            mt-1.5
                            text-[15px]
                            font-bold
                            tracking-[-.02em]

                            {{
                                $stats['routing_ready']
                                    ? 'text-[#327a53]'
                                    : 'text-[#a96e05]'
                            }}
                        "
                    >
                        {{
                            $stats['routing_ready']
                                ? 'Ready'
                                : 'Setup Needed'
                        }}
                    </p>

                    <p
                        class="
                            mt-1
                            text-[7.5px]
                            text-[#918779]
                        "
                    >
                        {{
                            $stats['routing_ready']
                                ? 'Network can route orders'
                                : 'Add another active hub'
                        }}
                    </p>

                </div>

                <span
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-[11px]

                        {{
                            $stats['routing_ready']
                                ? 'bg-[#edf8f1] text-[#327a53]'
                                : 'bg-[#fff6e5] text-[#b97805]'
                        }}
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[17px] w-[17px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M4 7h12"></path>
                        <path d="m13 4 3 3-3 3"></path>
                        <path d="M20 17H8"></path>
                        <path d="m11 14-3 3 3 3"></path>
                    </svg>
                </span>

            </div>
        </div>

    </section>


    {{-- =====================================================
        ROUTING WARNING
    ====================================================== --}}

    @unless($stats['routing_ready'])

        <section
            class="
                flex items-start gap-3
                rounded-[15px]
                border border-[#ead7ae]
                bg-[#fff9ec]
                px-4 py-3.5
            "
        >

            <span
                class="
                    grid h-8 w-8
                    shrink-0 place-items-center
                    rounded-[9px]
                    bg-[#fff0cf]
                    text-[#aa7413]
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-[15px] w-[15px]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 8v5"></path>
                    <path d="M12 16h.01"></path>
                </svg>
            </span>

            <div>

                <p
                    class="
                        text-[8px]
                        font-bold
                        text-[#936719]
                    "
                >
                    Facility network is not routing-ready
                </p>

                <p
                    class="
                        mt-0.5
                        text-[7px]
                        leading-[14px]
                        text-[#9f7d44]
                    "
                >
                    The current routing engine requires at least two active
                    sorting facilities before new orders can be routed through
                    separate origin and destination hubs.
                </p>

            </div>

        </section>

    @endunless


    {{-- =====================================================
        NETWORK + FORM GRID
    ====================================================== --}}

    <div
        class="
            grid gap-4
            xl:grid-cols-[minmax(0,1.45fr)_minmax(350px,.75fr)]
        "
    >

        {{-- =================================================
            FACILITY NETWORK
        ================================================== --}}

        <section
            class="
                min-w-0
                overflow-hidden
                rounded-[20px]
                border border-[#e8dfd1]
                bg-white
                shadow-[0_4px_20px_rgba(51,39,25,.035)]
            "
        >

            {{-- Header --}}
            <div
                class="
                    flex flex-col gap-3
                    border-b border-[#eee5d8]
                    px-5 py-4
                    sm:flex-row
                    sm:items-center
                    sm:justify-between
                "
            >

                <div>

                    <p
                        class="
                            text-[7.5px]
                            font-bold uppercase
                            tracking-[.12em]
                            text-[#b97805]
                        "
                    >
                        Facility Network
                    </p>

                    <h3
                        class="
                            mt-1
                            text-[13px]
                            font-bold
                            text-[#2e2821]
                        "
                    >
                        Registered sorting hubs
                    </h3>

                    <p
                        class="
                            mt-0.5
                            text-[8px]
                            text-[#918779]
                        "
                    >
                        Manage active status, location and historical usage.
                    </p>

                </div>


                <div
                    class="
                        flex items-center gap-2
                    "
                >

                    <div class="relative">

                        <svg
                            viewBox="0 0 24 24"
                            class="
                                pointer-events-none
                                absolute left-3 top-1/2
                                h-[13px] w-[13px]
                                -translate-y-1/2
                                text-[#aaa094]
                            "
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg>

                        <input
                            id="facilitySearchInput"
                            type="search"
                            placeholder="Search hub..."
                            class="
                                h-[36px]
                                w-[190px]
                                rounded-[10px]
                                border border-[#e5dccf]
                                bg-white
                                pl-9 pr-3
                                text-[8px]
                                outline-none
                                placeholder:text-[#aaa094]
                                focus:border-[#d9a03d]
                                focus:ring-4
                                focus:ring-[#d9a03d]/10
                            "
                        >

                    </div>

                    <span
                        class="
                            inline-flex h-[36px]
                            items-center
                            rounded-[10px]
                            bg-[#fff6e5]
                            px-3
                            text-[7px]
                            font-bold
                            text-[#9d6d18]
                        "
                    >
                        {{ $facilities->count() }}
                        HUB{{ $facilities->count() === 1 ? '' : 'S' }}
                    </span>

                </div>

            </div>


            {{-- Facility cards --}}
            <div
                id="facilityNetworkList"
                class="
                    grid gap-3
                    p-4
                "
            >

                @forelse($facilities as $facility)

                    @php
                        $facilitySearch =
                            strtolower(
                                implode(
                                    ' ',
                                    [
                                        $facility->name,
                                        $facility->code,
                                        $facility->address,
                                        $facility->province_name,
                                        $facility->municipality_name,
                                        $facility->barangay_name,
                                    ]
                                )
                            );
                    @endphp

                    <article
                        data-facility-card
                        data-search="{{ $facilitySearch }}"
                        class="
                            rounded-[16px]
                            border border-[#e9e0d3]
                            bg-[#fffdf9]
                            p-4
                            transition
                            hover:border-[#dcc9a7]
                            hover:shadow-[0_6px_20px_rgba(51,39,25,.04)]
                        "
                    >

                        <div
                            class="
                                flex flex-col gap-4
                                lg:flex-row
                                lg:items-start
                                lg:justify-between
                            "
                        >

                            {{-- Details --}}
                            <div
                                class="
                                    flex min-w-0
                                    items-start gap-3
                                "
                            >

                                <div
                                    class="
                                        grid h-10 w-10
                                        shrink-0 place-items-center
                                        rounded-[11px]

                                        {{
                                            $facility->is_active
                                                ? 'bg-[#edf8f1] text-[#327a53]'
                                                : 'bg-[#f4f1ed] text-[#82796e]'
                                        }}
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-[18px] w-[18px]"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M3 21h18"></path>
                                        <path d="M5 21V8l7-4 7 4v13"></path>
                                        <path d="M8 12h3"></path>
                                        <path d="M13 12h3"></path>
                                    </svg>
                                </div>


                                <div class="min-w-0">

                                    <div
                                        class="
                                            flex flex-wrap
                                            items-center gap-2
                                        "
                                    >

                                        <h4
                                            class="
                                                truncate
                                                text-[10.5px]
                                                font-bold
                                                text-[#312a22]
                                            "
                                        >
                                            {{ $facility->name }}
                                        </h4>

                                        <span
                                            class="
                                                inline-flex
                                                rounded-full
                                                px-2 py-1
                                                text-[6.5px]
                                                font-bold

                                                {{
                                                    $facility->is_active
                                                        ? 'bg-[#edf8f1] text-[#327a53]'
                                                        : 'bg-[#f1efec] text-[#847d74]'
                                                }}
                                            "
                                        >
                                            {{
                                                $facility->is_active
                                                    ? 'ACTIVE'
                                                    : 'INACTIVE'
                                            }}
                                        </span>

                                    </div>


                                    <p
                                        class="
                                            mt-1.5
                                            max-w-[720px]
                                            text-[7.5px]
                                            leading-[15px]
                                            text-[#746b61]
                                        "
                                    >
                                        {{ $facility->address }}
                                    </p>


                                    {{-- Information row --}}
                                    <div
                                        class="
                                            mt-3
                                            flex flex-wrap
                                            gap-x-4 gap-y-2
                                        "
                                    >

                                        {{-- Area --}}
                                        <div
                                            class="
                                                flex items-center
                                                gap-1.5
                                                text-[6.5px]
                                                text-[#8e857a]
                                            "
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                class="h-[11px] w-[11px]"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path d="M12 21s6-5.1 6-11a6 6 0 1 0-12 0c0 5.9 6 11 6 11Z"></path>
                                                <circle cx="12" cy="10" r="2"></circle>
                                            </svg>

                                            <span>
                                                {{ $facility->municipality_name }},
                                                {{ $facility->province_name }}
                                            </span>
                                        </div>


                                        {{-- Coordinates --}}
                                        <div
                                            class="
                                                flex items-center
                                                gap-1.5
                                                text-[6.5px]
                                                text-[#8e857a]
                                            "
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                class="h-[11px] w-[11px]"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <circle cx="12" cy="12" r="9"></circle>
                                                <path d="M3 12h18"></path>
                                                <path d="M12 3a15 15 0 0 1 0 18"></path>
                                                <path d="M12 3a15 15 0 0 0 0 18"></path>
                                            </svg>

                                            <span>
                                                {{ number_format((float) $facility->latitude, 5) }},
                                                {{ number_format((float) $facility->longitude, 5) }}
                                            </span>
                                        </div>


                                        {{-- Route count --}}
                                        <div
                                            class="
                                                flex items-center
                                                gap-1.5
                                                text-[6.5px]
                                                text-[#8e857a]
                                            "
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                class="h-[11px] w-[11px]"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <circle cx="6" cy="18" r="2"></circle>
                                                <circle cx="18" cy="6" r="2"></circle>
                                                <path d="M8 18h3a3 3 0 0 0 3-3V9a3 3 0 0 1 3-3"></path>
                                            </svg>

                                            <span>
                                                {{ $facility->route_stops_count }}
                                                route use{{ $facility->route_stops_count === 1 ? '' : 's' }}
                                            </span>
                                        </div>

                                    </div>


                                    {{-- Code --}}
                                    <p
                                        class="
                                            mt-2
                                            inline-flex
                                            rounded-[7px]
                                            bg-[#f7f3ec]
                                            px-2 py-1
                                            font-mono
                                            text-[6px]
                                            font-semibold
                                            text-[#82786c]
                                        "
                                    >
                                        {{ $facility->code }}
                                    </p>

                                </div>

                            </div>


                            {{-- Actions --}}
                            <div
                                class="
                                    flex shrink-0
                                    flex-wrap gap-2
                                    lg:justify-end
                                "
                            >

                                <a
                                    href="{{ route(
                                        'logistics.facilities.edit',
                                        $facility
                                    ) }}"
                                    class="
                                        inline-flex h-[34px]
                                        items-center gap-1.5
                                        rounded-[9px]
                                        border border-[#e2d8c9]
                                        bg-white
                                        px-3
                                        text-[7px]
                                        font-semibold
                                        text-[#63594f]
                                        transition
                                        hover:border-[#d4b97e]
                                        hover:bg-[#fffaf0]
                                        hover:text-[#9b680d]
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-[12px] w-[12px]"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M4 20h4l11-11-4-4L4 16v4Z"></path>
                                        <path d="m13 7 4 4"></path>
                                    </svg>

                                    Edit
                                </a>


                                <form
                                    method="POST"
                                    action="{{ route(
                                        'logistics.facilities.toggle',
                                        $facility
                                    ) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="
                                            inline-flex h-[34px]
                                            items-center gap-1.5
                                            rounded-[9px]
                                            border px-3
                                            text-[7px]
                                            font-semibold
                                            transition

                                            {{
                                                $facility->is_active
                                                    ? 'border-[#ead8ad] bg-[#fff9ed] text-[#9d711f] hover:bg-[#fff4dc]'
                                                    : 'border-[#cde0d3] bg-[#f3faf5] text-[#4f7c60] hover:bg-[#ebf6ee]'
                                            }}
                                        "
                                    >
                                        @if($facility->is_active)

                                            <svg
                                                viewBox="0 0 24 24"
                                                class="h-[12px] w-[12px]"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <circle cx="12" cy="12" r="9"></circle>
                                                <path d="M8 12h8"></path>
                                            </svg>

                                            Deactivate

                                        @else

                                            <svg
                                                viewBox="0 0 24 24"
                                                class="h-[12px] w-[12px]"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <circle cx="12" cy="12" r="9"></circle>
                                                <path d="m8 12 2.5 2.5L16 9"></path>
                                            </svg>

                                            Activate

                                        @endif
                                    </button>
                                </form>


                                @if((int) $facility->route_stops_count === 0)

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'logistics.facilities.destroy',
                                            $facility
                                        ) }}"
                                        onsubmit="return confirm('Delete this unused sorting facility?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="
                                                inline-flex h-[34px]
                                                items-center gap-1.5
                                                rounded-[9px]
                                                border border-[#ead5d5]
                                                bg-[#fff7f7]
                                                px-3
                                                text-[7px]
                                                font-semibold
                                                text-[#a65d5d]
                                                transition
                                                hover:bg-[#fff0f0]
                                            "
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                class="h-[12px] w-[12px]"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path d="M4 7h16"></path>
                                                <path d="M9 7V4h6v3"></path>
                                                <path d="M7 7l1 13h8l1-13"></path>
                                            </svg>

                                            Delete
                                        </button>
                                    </form>

                                @endif

                            </div>

                        </div>

                    </article>

                @empty

                    <div
                        class="
                            rounded-[16px]
                            border border-dashed
                            border-[#ddd2c1]
                            bg-[#fffdf9]
                            px-6 py-12
                            text-center
                        "
                    >

                        <div
                            class="
                                mx-auto
                                grid h-11 w-11
                                place-items-center
                                rounded-full
                                bg-[#fff7e8]
                                text-[#b97805]
                            "
                        >
                            <svg
                                viewBox="0 0 24 24"
                                class="h-[19px] w-[19px]"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M3 21h18"></path>
                                <path d="M5 21V7l7-4 7 4v14"></path>
                                <path d="M9 10h2"></path>
                                <path d="M13 10h2"></path>
                                <path d="M9 14h2"></path>
                                <path d="M13 14h2"></path>
                            </svg>
                        </div>

                        <p
                            class="
                                mt-3
                                text-[9px]
                                font-semibold
                                text-[#51483d]
                            "
                        >
                            No sorting facilities yet
                        </p>

                        <p
                            class="
                                mt-1
                                text-[7.5px]
                                text-[#918677]
                            "
                        >
                            Register your first physical hub using the facility form.
                        </p>

                    </div>

                @endforelse


                {{-- Search empty --}}
                <div
                    id="facilitySearchEmpty"
                    class="
                        hidden
                        rounded-[16px]
                        border border-dashed
                        border-[#ddd2c1]
                        px-6 py-10
                        text-center
                    "
                >
                    <p
                        class="
                            text-[9px]
                            font-semibold
                            text-[#51483d]
                        "
                    >
                        No matching facility
                    </p>

                    <p
                        class="
                            mt-1
                            text-[7.5px]
                            text-[#918677]
                        "
                    >
                        Try a different hub name, city, address, or facility code.
                    </p>
                </div>

            </div>

        </section>


        {{-- =================================================
            ADD / EDIT FACILITY
        ================================================== --}}

        <aside
            class="
                self-start
                overflow-hidden
                rounded-[20px]
                border border-[#e8dfd1]
                bg-white
                shadow-[0_4px_20px_rgba(51,39,25,.035)]
                xl:sticky
                xl:top-[88px]
            "
        >

            {{-- Form header --}}
            <div
                class="
                    flex items-start
                    justify-between gap-3
                    border-b border-[#eee5d8]
                    px-4 py-4
                "
            >

                <div>

                    <p
                        class="
                            text-[7px]
                            font-bold uppercase
                            tracking-[.12em]
                            text-[#b97805]
                        "
                    >
                        {{
                            $editing
                                ? 'Edit Facility'
                                : 'New Facility'
                        }}
                    </p>

                    <h3
                        class="
                            mt-1
                            text-[14px]
                            font-bold
                            text-[#302920]
                        "
                    >
                        {{
                            $editing
                                ? $editing->name
                                : 'Register sorting hub'
                        }}
                    </h3>

                    <p
                        class="
                            mt-0.5
                            text-[7px]
                            leading-[14px]
                            text-[#918779]
                        "
                    >
                        Enter the hub address and select its exact location on the map.
                    </p>

                </div>


                @if($editing)

                    <a
                        href="{{ route('logistics.facilities.index') }}"
                        class="
                            shrink-0
                            rounded-[8px]
                            border border-[#e4dacd]
                            bg-white
                            px-2.5 py-1.5
                            text-[6.5px]
                            font-semibold
                            text-[#81786c]
                            transition
                            hover:bg-[#faf7f1]
                            hover:text-[#a96e05]
                        "
                    >
                        Cancel Edit
                    </a>

                @endif

            </div>


            {{-- Form --}}
            <form
                id="sariFacilityForm"
                method="POST"
                action="{{
                    $editing
                        ? route(
                            'logistics.facilities.update',
                            $editing
                        )
                        : route(
                            'logistics.facilities.store'
                        )
                }}"
                class="p-4"
            >
                @csrf

                @if($editing)
                    @method('PATCH')
                @endif


                <div class="space-y-3">


                    {{-- Facility name --}}
                    <label class="block">

                        <span
                            class="
                                text-[7px]
                                font-semibold
                                text-[#5f574e]
                            "
                        >
                            Facility Name
                        </span>

                        <input
                            name="name"
                            value="{{ old(
                                'name',
                                $editing?->name
                            ) }}"
                            maxlength="140"
                            required
                            placeholder="e.g. SARI Bacoor Sorting Hub"
                            class="
                                mt-1.5 h-[38px]
                                w-full
                                rounded-[10px]
                                border border-[#e5ddd1]
                                bg-white
                                px-3
                                text-[8px]
                                text-[#403a33]
                                outline-none
                                transition
                                placeholder:text-[#aaa198]
                                focus:border-[#d3ad63]
                                focus:ring-4
                                focus:ring-[#d3ad63]/10
                            "
                        >

                    </label>


                    {{-- Address --}}
                    <label class="block">

                        <span
                            class="
                                text-[7px]
                                font-semibold
                                text-[#5f574e]
                            "
                        >
                            Full Address / Landmark
                        </span>

                        <textarea
                            name="address"
                            rows="2"
                            maxlength="1200"
                            required
                            placeholder="Building, street, landmark, barangay..."
                            class="
                                mt-1.5
                                min-h-[66px]
                                w-full
                                resize-none
                                rounded-[10px]
                                border border-[#e5ddd1]
                                bg-white
                                px-3 py-2.5
                                text-[8px]
                                leading-[15px]
                                text-[#403a33]
                                outline-none
                                transition
                                placeholder:text-[#aaa198]
                                focus:border-[#d3ad63]
                                focus:ring-4
                                focus:ring-[#d3ad63]/10
                            "
                        >{{ old(
                            'address',
                            $editing?->address
                        ) }}</textarea>

                    </label>


                    {{-- Province / City --}}
                    <div
                        class="
                            grid gap-2.5
                            sm:grid-cols-2
                        "
                    >

                        <label class="block">

                            <span
                                class="
                                    text-[7px]
                                    font-semibold
                                    text-[#5f574e]
                                "
                            >
                                Province / Region
                            </span>

                            <input
                                name="province_name"
                                value="{{ old(
                                    'province_name',
                                    $editing?->province_name
                                ) }}"
                                required
                                placeholder="Laguna"
                                class="
                                    mt-1.5 h-[38px]
                                    w-full
                                    rounded-[10px]
                                    border border-[#e5ddd1]
                                    bg-white
                                    px-3
                                    text-[8px]
                                    text-[#403a33]
                                    outline-none
                                    placeholder:text-[#aaa198]
                                    focus:border-[#d3ad63]
                                    focus:ring-4
                                    focus:ring-[#d3ad63]/10
                                "
                            >

                        </label>


                        <label class="block">

                            <span
                                class="
                                    text-[7px]
                                    font-semibold
                                    text-[#5f574e]
                                "
                            >
                                City / Municipality
                            </span>

                            <input
                                name="municipality_name"
                                value="{{ old(
                                    'municipality_name',
                                    $editing?->municipality_name
                                ) }}"
                                required
                                placeholder="Santa Cruz"
                                class="
                                    mt-1.5 h-[38px]
                                    w-full
                                    rounded-[10px]
                                    border border-[#e5ddd1]
                                    bg-white
                                    px-3
                                    text-[8px]
                                    text-[#403a33]
                                    outline-none
                                    placeholder:text-[#aaa198]
                                    focus:border-[#d3ad63]
                                    focus:ring-4
                                    focus:ring-[#d3ad63]/10
                                "
                            >

                        </label>

                    </div>


                    {{-- Barangay --}}
                    <label class="block">

                        <span
                            class="
                                text-[7px]
                                font-semibold
                                text-[#5f574e]
                            "
                        >
                            Barangay

                            <span
                                class="
                                    font-normal
                                    text-[#aaa198]
                                "
                            >
                                (optional)
                            </span>
                        </span>

                        <input
                            name="barangay_name"
                            value="{{ old(
                                'barangay_name',
                                $editing?->barangay_name
                            ) }}"
                            placeholder="Bubukal"
                            class="
                                mt-1.5 h-[38px]
                                w-full
                                rounded-[10px]
                                border border-[#e5ddd1]
                                bg-white
                                px-3
                                text-[8px]
                                text-[#403a33]
                                outline-none
                                placeholder:text-[#aaa198]
                                focus:border-[#d3ad63]
                                focus:ring-4
                                focus:ring-[#d3ad63]/10
                            "
                        >

                    </label>


                    {{-- Map --}}
                    <div>

                        <div
                            class="
                                mb-2
                                flex items-end
                                justify-between gap-3
                            "
                        >

                            <div>

                                <p
                                    class="
                                        text-[7px]
                                        font-semibold
                                        text-[#5f574e]
                                    "
                                >
                                    Exact Map Location
                                </p>

                                <p
                                    class="
                                        mt-0.5
                                        text-[6.5px]
                                        text-[#9a9084]
                                    "
                                >
                                    Click the map where the facility is located.
                                </p>

                            </div>

                            <span
                                id="sariFacilityMapStatus"
                                class="
                                    rounded-full
                                    bg-[#fff6e5]
                                    px-2 py-1
                                    text-[6px]
                                    font-bold
                                    text-[#a96e05]
                                "
                            >
                                {{
                                    old(
                                        'latitude',
                                        $editing?->latitude
                                    )
                                    &&
                                    old(
                                        'longitude',
                                        $editing?->longitude
                                    )
                                        ? 'PIN SET'
                                        : 'SELECT PIN'
                                }}
                            </span>

                        </div>

                        <div id="sariFacilityMap"></div>

                    </div>


                    {{-- Coordinates --}}
                    <div
                        class="
                            grid grid-cols-2 gap-2.5
                        "
                    >

                        <label class="block">

                            <span
                                class="
                                    text-[6.5px]
                                    font-semibold
                                    text-[#83796d]
                                "
                            >
                                Latitude
                            </span>

                            <input
                                id="sariFacilityLatitude"
                                name="latitude"
                                type="number"
                                step="0.0000001"
                                min="4"
                                max="22.5"
                                value="{{ old(
                                    'latitude',
                                    $editing?->latitude
                                ) }}"
                                readonly
                                required
                                class="
                                    mt-1 h-[36px]
                                    w-full
                                    rounded-[9px]
                                    border border-[#e7dfd4]
                                    bg-[#faf8f4]
                                    px-3
                                    text-[7px]
                                    text-[#71685e]
                                "
                            >

                        </label>


                        <label class="block">

                            <span
                                class="
                                    text-[6.5px]
                                    font-semibold
                                    text-[#83796d]
                                "
                            >
                                Longitude
                            </span>

                            <input
                                id="sariFacilityLongitude"
                                name="longitude"
                                type="number"
                                step="0.0000001"
                                min="116"
                                max="127.5"
                                value="{{ old(
                                    'longitude',
                                    $editing?->longitude
                                ) }}"
                                readonly
                                required
                                class="
                                    mt-1 h-[36px]
                                    w-full
                                    rounded-[9px]
                                    border border-[#e7dfd4]
                                    bg-[#faf8f4]
                                    px-3
                                    text-[7px]
                                    text-[#71685e]
                                "
                            >

                        </label>

                    </div>


                    {{-- Active --}}
                    <label
                        class="
                            flex cursor-pointer
                            items-start gap-3
                            rounded-[11px]
                            border border-[#eee4d3]
                            bg-[#fffdf8]
                            p-3
                        "
                    >

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{
                                old(
                                    'is_active',
                                    $editing?->is_active ?? true
                                )
                                    ? 'checked'
                                    : ''
                            }}
                            class="
                                mt-0.5
                                h-4 w-4
                                rounded
                                border-[#d9cdbb]
                                text-[#c98913]
                                focus:ring-[#d8b770]
                            "
                        >

                        <span>

                            <strong
                                class="
                                    block
                                    text-[7.5px]
                                    text-[#51493f]
                                "
                            >
                                Active for new routes
                            </strong>

                            <small
                                class="
                                    mt-0.5
                                    block
                                    text-[6.5px]
                                    leading-[13px]
                                    text-[#93897e]
                                "
                            >
                                Inactive facilities remain available in
                                historical route records but are excluded
                                from new routing.
                            </small>

                        </span>

                    </label>


                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="
                            h-[40px]
                            w-full
                            rounded-[10px]
                            bg-[#d9930a]
                            px-5
                            text-[8px]
                            font-bold
                            text-white
                            shadow-[0_7px_18px_rgba(217,147,10,.17)]
                            transition
                            hover:bg-[#c48608]
                            focus:outline-none
                            focus:ring-4
                            focus:ring-[#d9930a]/15
                        "
                    >
                        {{
                            $editing
                                ? 'Save Facility Changes'
                                : 'Add Sorting Facility'
                        }}
                    </button>

                </div>

            </form>


            {{-- Account reminder --}}
            <div
                class="
                    border-t border-[#eee5d8]
                    bg-[#fcfaf7]
                    px-4 py-3
                "
            >

                <div
                    class="
                        flex items-start gap-2.5
                    "
                >

                    <span
                        class="
                            grid h-7 w-7
                            shrink-0 place-items-center
                            rounded-[8px]
                            bg-[#f4f1eb]
                            text-[#81786c]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-[13px] w-[13px]"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 11v5"></path>
                            <path d="M12 8h.01"></path>
                        </svg>
                    </span>

                    <div>

                        <p
                            class="
                                text-[6.5px]
                                font-bold
                                text-[#696056]
                            "
                        >
                            Register actual hubs only
                        </p>

                        <p
                            class="
                                mt-0.5
                                text-[6.5px]
                                leading-[13px]
                                text-[#948a7e]
                            "
                        >
                            Your Logistics account address is
                            {{ $logistics->street_address }},
                            {{ $logistics->municipality_name }},
                            {{ $logistics->province_name }}.
                            It is not automatically treated as a sorting facility.
                        </p>

                    </div>

                </div>

            </div>

        </aside>

    </div>

</div>

@endsection


@push('scripts')

<script src="https://unpkg.com/maplibre-gl@5.7.1/dist/maplibre-gl.js"></script>

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | Facility Search
        |--------------------------------------------------------------------------
        */

        const searchInput =
            document.getElementById(
                'facilitySearchInput'
            );

        const facilityCards =
            Array.from(
                document.querySelectorAll(
                    '[data-facility-card]'
                )
            );

        const searchEmpty =
            document.getElementById(
                'facilitySearchEmpty'
            );


        function normalize(value) {
            return String(value || '')
                .toLowerCase()
                .trim();
        }


        function filterFacilities() {

            const keyword =
                normalize(
                    searchInput?.value
                );

            let visible = 0;

            facilityCards.forEach(
                function (card) {

                    const searchable =
                        normalize(
                            card.dataset.search
                        );

                    const show =
                        ! keyword
                        || searchable.includes(
                            keyword
                        );

                    card.classList.toggle(
                        'hidden',
                        ! show
                    );

                    if (show) {
                        visible++;
                    }
                }
            );


            if (
                facilityCards.length > 0
                && searchEmpty
            ) {
                searchEmpty.classList.toggle(
                    'hidden',
                    visible !== 0
                );
            }
        }


        searchInput?.addEventListener(
            'input',
            filterFacilities
        );


        /*
        |--------------------------------------------------------------------------
        | Facility Map
        |--------------------------------------------------------------------------
        */

        const mapNode =
            document.getElementById(
                'sariFacilityMap'
            );

        const latInput =
            document.getElementById(
                'sariFacilityLatitude'
            );

        const lngInput =
            document.getElementById(
                'sariFacilityLongitude'
            );

        const statusNode =
            document.getElementById(
                'sariFacilityMapStatus'
            );


        if (
            ! mapNode
            || ! latInput
            || ! lngInput
            || ! window.maplibregl
        ) {
            return;
        }


        const PH_BOUNDS = [
            [116.0, 4.0],
            [127.5, 22.5],
        ];


        const initialLat =
            Number(
                latInput.value
            );

        const initialLng =
            Number(
                lngInput.value
            );


        const hasInitial =
            Number.isFinite(
                initialLat
            )
            &&
            Number.isFinite(
                initialLng
            )
            &&
            initialLat >= 4
            &&
            initialLat <= 22.5
            &&
            initialLng >= 116
            &&
            initialLng <= 127.5;


        const style = {
            version: 8,

            sources: {
                'osm-streets': {
                    type: 'raster',

                    tiles: [
                        'https://tile.openstreetmap.org/{z}/{x}/{y}.png'
                    ],

                    tileSize: 256,
                    minzoom: 0,
                    maxzoom: 19,

                    attribution:
                        '© OpenStreetMap contributors',
                },
            },

            layers: [
                {
                    id: 'osm',
                    type: 'raster',
                    source: 'osm-streets',
                },
            ],
        };


        const map =
            new maplibregl.Map({
                container: mapNode,

                style,

                center:
                    hasInitial
                        ? [
                            initialLng,
                            initialLat
                        ]
                        : [
                            121.0,
                            12.4
                        ],

                zoom:
                    hasInitial
                        ? 13
                        : 5.3,

                maxBounds: PH_BOUNDS,

                dragRotate: false,

                pitchWithRotate: false,

                attributionControl: true,
            });


        map.addControl(
            new maplibregl.NavigationControl({
                showCompass: false,
            }),
            'top-right'
        );


        let marker = null;


        function setPin(
            lng,
            lat
        ) {

            if (
                lat < 4
                || lat > 22.5
                || lng < 116
                || lng > 127.5
            ) {
                return;
            }


            latInput.value =
                Number(lat)
                    .toFixed(7);

            lngInput.value =
                Number(lng)
                    .toFixed(7);


            if (statusNode) {

                statusNode.textContent =
                    'PIN SET';

                statusNode.classList.remove(
                    'bg-[#fff6e5]',
                    'text-[#a96e05]'
                );

                statusNode.classList.add(
                    'bg-[#edf8f1]',
                    'text-[#327a53]'
                );
            }


            if (! marker) {

                const element =
                    document.createElement(
                        'div'
                    );

                element.className =
                    'sari-facility-pin';

                element.textContent =
                    'H';


                marker =
                    new maplibregl.Marker({
                        element,
                        anchor: 'center',
                    })
                        .setLngLat([
                            lng,
                            lat
                        ])
                        .addTo(map);

            } else {

                marker.setLngLat([
                    lng,
                    lat
                ]);
            }
        }


        if (hasInitial) {
            setPin(
                initialLng,
                initialLat
            );
        }


        map.on(
            'click',
            function (event) {

                setPin(
                    event.lngLat.lng,
                    event.lngLat.lat
                );
            }
        );


        window.setTimeout(
            function () {
                map.resize();
            },
            150
        );
    }
);
</script>

@endpush