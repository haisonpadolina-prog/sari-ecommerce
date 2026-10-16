@extends('layouts.logistics')

@section('title', 'Logistics Dashboard | SARI')
@section('page-title', 'Dashboard Overview')

@section('content')

<div
    id="logisticsDashboard"
    class="mx-auto w-full max-w-[1800px] space-y-4"
>

    {{-- =====================================================
        SUCCESS / ERROR NOTICE
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
        WELCOME PANEL
    ====================================================== --}}

    <section
        class="
            relative overflow-hidden
            rounded-[20px]
            border border-[#e8dfd1]
            bg-white
            shadow-[0_6px_24px_rgba(51,39,25,.04)]
        "
    >
        <div
            class="
                pointer-events-none
                absolute -right-[100px] -top-[120px]
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
                px-5 py-4
                sm:px-6 sm:py-5
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
                        stroke-width="1.7"
                    >
                        <path d="M3 6h11v10H3z"></path>
                        <path d="M14 10h4l3 3v3h-7"></path>
                        <circle cx="7" cy="18" r="2"></circle>
                        <circle cx="18" cy="18" r="2"></circle>
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
                        SARI Logistics Operations
                    </p>

                    <h2
                        class="
                            mt-1
                            text-[19px]
                            font-bold
                            tracking-[-.035em]
                            text-[#211d17]
                            sm:text-[21px]
                        "
                    >
                        Welcome back, {{ $logisticsName }}
                    </h2>

                    <p
                        class="
                            mt-1
                            max-w-[760px]
                            text-[8.5px]
                            leading-[18px]
                            text-[#807668]
                            sm:text-[9px]
                        "
                    >
                        Monitor Seller pickups, hub intake, sorting,
                        inter-hub transfers, and last-mile deliveries
                        from one operations workspace.
                    </p>

                </div>
            </div>

            <div
                class="
                    grid grid-cols-2
                    gap-2
                    sm:flex
                    sm:items-center
                "
            >

                <a
                    href="{{ route('logistics.pickup-requests') }}"
                    class="
                        inline-flex h-[38px]
                        items-center justify-center
                        gap-2
                        rounded-[10px]
                        border border-[#e4dac9]
                        bg-white
                        px-4
                        text-[8.5px]
                        font-semibold
                        text-[#605649]
                        transition
                        hover:border-[#d6b97d]
                        hover:bg-[#fffaf0]
                        hover:text-[#a96e05]
                    "
                >
                    Pickup Queue
                </a>

                <a
                    href="{{ route('logistics.hub-transfers') }}"
                    class="
                        inline-flex h-[38px]
                        items-center justify-center
                        gap-2
                        rounded-[10px]
                        bg-[#d9930a]
                        px-4
                        text-[8.5px]
                        font-semibold
                        text-white
                        shadow-[0_6px_16px_rgba(217,147,10,.16)]
                        transition
                        hover:bg-[#c48608]
                    "
                >
                    Hub Transfers
                </a>

            </div>

        </div>
    </section>


    {{-- =====================================================
        MAIN METRICS
    ====================================================== --}}

    <section
        class="
            grid gap-3
            sm:grid-cols-2
            xl:grid-cols-4
        "
    >

        {{-- Active Riders --}}
        <a
            href="{{ route('logistics.rider-management') }}"
            class="
                group rounded-[16px]
                border border-[#e8dfd1]
                bg-white
                p-4
                shadow-[0_4px_18px_rgba(51,39,25,.035)]
                transition
                hover:-translate-y-[1px]
                hover:border-[#d9c393]
                hover:shadow-[0_7px_22px_rgba(51,39,25,.06)]
            "
        >
            <div class="flex items-start justify-between gap-3">

                <div>
                    <p
                        class="
                            text-[7px]
                            font-bold
                            uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Active Riders
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
                        {{ $stats['active_riders'] }}
                    </p>

                    <p class="mt-0.5 text-[7.5px] text-[#918779]">
                        {{ $stats['online_riders'] }} online now
                    </p>
                </div>

                <span
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-[11px]
                        bg-[#f2f7fd]
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
                        <circle cx="8" cy="8" r="3"></circle>
                        <circle cx="17" cy="9" r="2.5"></circle>
                        <path d="M2.5 20c.5-4 2.5-6 5.5-6"></path>
                        <path d="M13 15c3.2 0 5.2 1.7 5.7 5"></path>
                    </svg>
                </span>

            </div>
        </a>


        {{-- Active Facilities --}}
        <a
            href="{{ route('logistics.facilities.index') }}"
            class="
                group rounded-[16px]
                border border-[#e8dfd1]
                bg-white
                p-4
                shadow-[0_4px_18px_rgba(51,39,25,.035)]
                transition
                hover:-translate-y-[1px]
                hover:border-[#d9c393]
                hover:shadow-[0_7px_22px_rgba(51,39,25,.06)]
            "
        >
            <div class="flex items-start justify-between gap-3">

                <div>
                    <p
                        class="
                            text-[7px]
                            font-bold
                            uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Active Facilities
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
                        {{ $stats['active_facilities'] }}
                    </p>

                    <p class="mt-0.5 text-[7.5px] text-[#918779]">
                        Sorting & transfer network
                    </p>
                </div>

                <span
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-[11px]
                        bg-[#fff7e8]
                        text-[#b97805]
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
                        <path d="M8 12h3"></path>
                        <path d="M13 12h3"></path>
                        <path d="M8 16h8"></path>
                    </svg>
                </span>

            </div>
        </a>


        {{-- Active Deliveries --}}
        <a
            href="{{ route('logistics.delivery-monitoring') }}"
            class="
                group rounded-[16px]
                border border-[#e8dfd1]
                bg-white
                p-4
                shadow-[0_4px_18px_rgba(51,39,25,.035)]
                transition
                hover:-translate-y-[1px]
                hover:border-[#d9c393]
                hover:shadow-[0_7px_22px_rgba(51,39,25,.06)]
            "
        >
            <div class="flex items-start justify-between gap-3">

                <div>
                    <p
                        class="
                            text-[7px]
                            font-bold
                            uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Active Deliveries
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
                        {{ $stats['active_deliveries'] }}
                    </p>

                    <p class="mt-0.5 text-[7.5px] text-[#918779]">
                        Across pickup and last mile
                    </p>
                </div>

                <span
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-[11px]
                        bg-[#f5f2fb]
                        text-[#7967a5]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[17px] w-[17px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M3 6h11v10H3z"></path>
                        <path d="M14 10h4l3 3v3h-7"></path>
                        <circle cx="7" cy="18" r="2"></circle>
                        <circle cx="18" cy="18" r="2"></circle>
                    </svg>
                </span>

            </div>
        </a>


        {{-- Delivered Today --}}
        <a
            href="{{ route('logistics.reports') }}"
            class="
                group rounded-[16px]
                border border-[#e8dfd1]
                bg-white
                p-4
                shadow-[0_4px_18px_rgba(51,39,25,.035)]
                transition
                hover:-translate-y-[1px]
                hover:border-[#d9c393]
                hover:shadow-[0_7px_22px_rgba(51,39,25,.06)]
            "
        >
            <div class="flex items-start justify-between gap-3">

                <div>
                    <p
                        class="
                            text-[7px]
                            font-bold
                            uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Delivered Today
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
                        {{ $stats['delivered_today'] }}
                    </p>

                    <p class="mt-0.5 text-[7.5px] text-[#918779]">
                        {{ $stats['total_delivered'] }} total completed
                    </p>
                </div>

                <span
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-[11px]
                        bg-[#f1faf5]
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
        </a>

    </section>


    {{-- =====================================================
        OPERATIONS PIPELINE
    ====================================================== --}}

    <section
        class="
            overflow-hidden
            rounded-[20px]
            border border-[#e8dfd1]
            bg-white
            shadow-[0_4px_20px_rgba(51,39,25,.035)]
        "
    >
        <div
            class="
                flex flex-wrap
                items-center justify-between
                gap-3
                border-b border-[#eee5d8]
                px-5 py-3.5
            "
        >
            <div>
                <p
                    class="
                        text-[7.5px]
                        font-bold
                        uppercase
                        tracking-[.12em]
                        text-[#b97805]
                    "
                >
                    Operations Pipeline
                </p>

                <h3
                    class="
                        mt-1
                        text-[13px]
                        font-bold
                        text-[#2e2821]
                    "
                >
                    Parcel lifecycle overview
                </h3>

                <p
                    class="
                        mt-0.5
                        text-[8px]
                        text-[#918779]
                    "
                >
                    Live workload across each Logistics stage.
                </p>
            </div>

            <div
                class="
                    rounded-full
                    border border-[#e9dfd1]
                    bg-[#fcfaf6]
                    px-3 py-1.5
                    text-[7.5px]
                    font-semibold
                    text-[#746a5d]
                "
            >
                8 stages
            </div>
        </div>

        <div
            class="
                grid
                sm:grid-cols-2
                xl:grid-cols-4
            "
        >

            @foreach($pipeline as $index => $stage)

                <a
                    href="{{ route($stage['route']) }}"
                    class="
                        group relative
                        min-h-[108px]
                        border-b border-[#eee5d8]
                        p-3.5
                        transition
                        hover:bg-[#fffaf2]

                        sm:[&:nth-child(odd)]:border-r
                        xl:border-r
                        xl:[&:nth-child(4n)]:border-r-0
                        xl:[&:nth-last-child(-n+4)]:border-b-0
                    "
                >
                    <div
                        class="
                            flex items-start
                            justify-between gap-3
                        "
                    >
                        <span
                            class="
                                text-[7px]
                                font-bold
                                tracking-[.08em]
                                text-[#b7ad9f]
                            "
                        >
                            {{ $stage['step'] }}
                        </span>

                        <svg
                            viewBox="0 0 24 24"
                            class="
                                h-[13px] w-[13px]
                                text-[#c2b7a7]
                                transition
                                group-hover:translate-x-[2px]
                                group-hover:text-[#b97805]
                            "
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="m9 18 6-6-6-6"></path>
                        </svg>
                    </div>

                    <p
                        class="
                            mt-2.5
                            text-[20px]
                            font-bold
                            tracking-[-.04em]
                            text-[#29231c]
                        "
                    >
                        {{ $stage['value'] }}
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[8.5px]
                            font-semibold
                            text-[#51483d]
                        "
                    >
                        {{ $stage['label'] }}
                    </p>

                    <p
                        class="
                            mt-1
                            text-[7px]
                            leading-[14px]
                            text-[#93897b]
                        "
                    >
                        {{ $stage['description'] }}
                    </p>
                </a>

            @endforeach

        </div>
    </section>


    {{-- =====================================================
        LOWER CONTENT GRID
    ====================================================== --}}

    <div
        class="
            grid gap-4
            xl:grid-cols-[minmax(0,1.65fr)_minmax(300px,.72fr)]
        "
    >

        {{-- =================================================
            RECENT ORDERS
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
            <div
                class="
                    flex items-center
                    justify-between gap-4
                    border-b border-[#eee5d8]
                    px-5 py-3.5
                "
            >
                <div>
                    <p
                        class="
                            text-[7.5px]
                            font-bold
                            uppercase
                            tracking-[.12em]
                            text-[#b97805]
                        "
                    >
                        Live Activity
                    </p>

                    <h3
                        class="
                            mt-1
                            text-[13px]
                            font-bold
                            text-[#2e2821]
                        "
                    >
                        Recent logistics orders
                    </h3>

                    <p
                        class="
                            mt-0.5
                            text-[8px]
                            text-[#918779]
                        "
                    >
                        Latest orders moving across the Logistics network.
                    </p>
                </div>

                <a
                    href="{{ route('logistics.delivery-monitoring') }}"
                    class="
                        hidden
                        rounded-[9px]
                        border border-[#e4dac9]
                        bg-white
                        px-3 py-2
                        text-[7.5px]
                        font-semibold
                        text-[#62584b]
                        transition
                        hover:border-[#d6b97d]
                        hover:text-[#a96e05]
                        sm:inline-flex
                    "
                >
                    View Monitoring
                </a>
            </div>

            <div class="overflow-x-auto">

                <table
                    class="
                        w-full
                        min-w-[900px]
                        text-left
                    "
                >
                    <thead
                        class="
                            bg-[#fcfaf6]
                            text-[7px]
                            uppercase
                            tracking-[.08em]
                            text-[#978d80]
                        "
                    >
                        <tr>
                            <th class="px-5 py-3 font-semibold">
                                Order
                            </th>

                            <th class="px-4 py-3 font-semibold">
                                Seller / Buyer
                            </th>

                            <th class="px-4 py-3 font-semibold">
                                Pickup Rider
                            </th>

                            <th class="px-4 py-3 font-semibold">
                                Parcel Stage
                            </th>

                            <th class="px-4 py-3 font-semibold">
                                Last Mile
                            </th>

                            <th class="px-5 py-3 font-semibold">
                                Updated
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#f0e9df]">

                        @forelse($recentOrders as $order)

                            @php
                                $lastMile =
                                    $lastMileByOrder->get(
                                        $order->id
                                    );

                                $parcelStatus =
                                    $order->logisticsParcel
                                        ? strtoupper(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $order
                                                    ->logisticsParcel
                                                    ->status
                                            )
                                        )
                                        : '—';

                                $orderStatus =
                                    strtolower(
                                        (string) $order->status
                                    );

                                $statusClass =
                                    match($orderStatus) {
                                        'delivered' =>
                                            'bg-[#edf8f1] text-[#327a53]',

                                        'arrived_buyer',
                                        'in_transit' =>
                                            'bg-[#eef5fb] text-[#447398]',

                                        default =>
                                            'bg-[#fff6e5] text-[#a96e05]',
                                    };
                            @endphp

                            <tr
                                class="
                                    text-[8px]
                                    text-[#61584d]
                                    transition
                                    hover:bg-[#fffdf9]
                                "
                            >
                                <td class="px-5 py-3">

                                    <p
                                        class="
                                            font-bold
                                            text-[#332c24]
                                        "
                                    >
                                        {{ $order->order_number }}
                                    </p>

                                    <span
                                        class="
                                            mt-1 inline-flex
                                            rounded-full
                                            px-2 py-1
                                            text-[6.5px]
                                            font-semibold
                                            {{ $statusClass }}
                                        "
                                    >
                                        {{ strtoupper(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $order->status
                                            )
                                        ) }}
                                    </span>

                                </td>

                                <td class="px-4 py-3">

                                    <p
                                        class="
                                            max-w-[180px]
                                            truncate
                                            font-semibold
                                            text-[#443c32]
                                        "
                                    >
                                        {{ $order->seller?->store_name ?: 'SARI Seller' }}
                                    </p>

                                    <p
                                        class="
                                            mt-1
                                            max-w-[180px]
                                            truncate
                                            text-[7px]
                                            text-[#978d80]
                                        "
                                    >
                                        Buyer: {{ $order->buyer_name }}
                                    </p>

                                </td>

                                <td class="px-4 py-3">

                                    <p
                                        class="
                                            max-w-[135px]
                                            truncate
                                            font-medium
                                        "
                                    >
                                        {{ $order->courier_name ?: 'Unassigned' }}
                                    </p>

                                </td>

                                <td class="px-4 py-3">

                                    <span
                                        class="
                                            rounded-full
                                            bg-[#f7f3ec]
                                            px-2.5 py-1
                                            text-[6.5px]
                                            font-semibold
                                            text-[#756b5f]
                                        "
                                    >
                                        {{ $parcelStatus }}
                                    </span>

                                </td>

                                <td class="px-4 py-3">

                                    @if($lastMile)

                                        <p
                                            class="
                                                max-w-[145px]
                                                truncate
                                                font-semibold
                                                text-[#51483d]
                                            "
                                        >
                                            {{ trim(
                                                $lastMile->first_name
                                                . ' '
                                                . $lastMile->last_name
                                            ) }}
                                        </p>

                                        <p
                                            class="
                                                mt-1
                                                text-[6.5px]
                                                text-[#978d80]
                                            "
                                        >
                                            {{ $lastMile->delivery_area }}
                                            ·
                                            {{ strtoupper(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $lastMile->status
                                                )
                                            ) }}
                                        </p>

                                    @else

                                        <span
                                            class="
                                                text-[7px]
                                                text-[#aaa095]
                                            "
                                        >
                                            —
                                        </span>

                                    @endif

                                </td>

                                <td class="px-5 py-3">

                                    <span
                                        class="
                                            whitespace-nowrap
                                            text-[7px]
                                            text-[#918779]
                                        "
                                    >
                                        {{ $order->updated_at?->diffForHumans() }}
                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="6"
                                    class="
                                        px-5 py-10
                                        text-center
                                        text-[8.5px]
                                        text-[#948a7c]
                                    "
                                >
                                    No logistics orders yet.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>

            </div>
        </section>


        {{-- =================================================
            RIGHT COLUMN
        ================================================== --}}

        <div class="space-y-4">

            {{-- Network Status --}}
            <section
                class="
                    rounded-[20px]
                    border border-[#e8dfd1]
                    bg-white
                    p-4
                    shadow-[0_4px_20px_rgba(51,39,25,.035)]
                "
            >
                <div>
                    <p
                        class="
                            text-[7.5px]
                            font-bold
                            uppercase
                            tracking-[.12em]
                            text-[#b97805]
                        "
                    >
                        Network Status
                    </p>

                    <h3
                        class="
                            mt-1
                            text-[13px]
                            font-bold
                            text-[#2e2821]
                        "
                    >
                        Operations summary
                    </h3>
                </div>

                <div
                    class="
                        mt-3
                        divide-y divide-[#eee5d8]
                        overflow-hidden
                        rounded-[13px]
                        border border-[#eee5d8]
                        bg-[#fffdf9]
                    "
                >

                    <a
                        href="{{ route('logistics.rider-applications') }}"
                        class="
                            flex items-center
                            justify-between
                            px-3.5 py-3
                            transition
                            hover:bg-[#fffaf2]
                        "
                    >
                        <div>
                            <p
                                class="
                                    text-[7.5px]
                                    font-semibold
                                    text-[#51483d]
                                "
                            >
                                Rider Applications
                            </p>

                            <p
                                class="
                                    mt-0.5
                                    text-[6.5px]
                                    text-[#978d80]
                                "
                            >
                                Pending approval
                            </p>
                        </div>

                        <strong
                            class="
                                text-[13px]
                                text-[#2e2821]
                            "
                        >
                            {{ $stats['pending_rider_applications'] }}
                        </strong>
                    </a>

                    <a
                        href="{{ route('logistics.hub-transfers') }}"
                        class="
                            flex items-center
                            justify-between
                            px-3.5 py-3
                            transition
                            hover:bg-[#fffaf2]
                        "
                    >
                        <div>
                            <p
                                class="
                                    text-[7.5px]
                                    font-semibold
                                    text-[#51483d]
                                "
                            >
                                Hub Transfers
                            </p>

                            <p
                                class="
                                    mt-0.5
                                    text-[6.5px]
                                    text-[#978d80]
                                "
                            >
                                In transit
                            </p>
                        </div>

                        <strong
                            class="
                                text-[13px]
                                text-[#2e2821]
                            "
                        >
                            {{ $stats['hub_transfer_in_transit'] }}
                        </strong>
                    </a>

                    <a
                        href="{{ route('logistics.last-mile-assignment') }}"
                        class="
                            flex items-center
                            justify-between
                            px-3.5 py-3
                            transition
                            hover:bg-[#fffaf2]
                        "
                    >
                        <div>
                            <p
                                class="
                                    text-[7.5px]
                                    font-semibold
                                    text-[#51483d]
                                "
                            >
                                Last Mile
                            </p>

                            <p
                                class="
                                    mt-0.5
                                    text-[6.5px]
                                    text-[#978d80]
                                "
                            >
                                Out for delivery
                            </p>
                        </div>

                        <strong
                            class="
                                text-[13px]
                                text-[#2e2821]
                            "
                        >
                            {{ $stats['last_mile_out_for_delivery'] }}
                        </strong>
                    </a>

                </div>
            </section>


            {{-- Recent Hub Transfers --}}
            <section
                class="
                    rounded-[20px]
                    border border-[#e8dfd1]
                    bg-white
                    p-4
                    shadow-[0_4px_20px_rgba(51,39,25,.035)]
                "
            >
                <div
                    class="
                        flex items-center
                        justify-between gap-3
                    "
                >
                    <div>
                        <p
                            class="
                                text-[7.5px]
                                font-bold
                                uppercase
                                tracking-[.12em]
                                text-[#b97805]
                            "
                        >
                            Line-haul
                        </p>

                        <h3
                            class="
                                mt-1
                                text-[13px]
                                font-bold
                                text-[#2e2821]
                            "
                        >
                            Recent hub transfers
                        </h3>
                    </div>

                    <a
                        href="{{ route('logistics.hub-transfers') }}"
                        class="
                            text-[7.5px]
                            font-semibold
                            text-[#a96e05]
                            hover:underline
                        "
                    >
                        View all
                    </a>
                </div>

                <div class="mt-3 space-y-2.5">

                    @forelse($recentTransfers as $transfer)

                        @php
                            $transferStatusClass =
                                match($transfer->status) {
                                    'received' =>
                                        'bg-[#edf8f1] text-[#327a53]',

                                    'in_transit' =>
                                        'bg-[#eef5fb] text-[#447398]',

                                    default =>
                                        'bg-[#fff6e5] text-[#a96e05]',
                                };
                        @endphp

                        <div
                            class="
                                rounded-[13px]
                                border border-[#eee5d8]
                                bg-[#fffdf9]
                                p-3
                            "
                        >
                            <div
                                class="
                                    flex items-start
                                    justify-between gap-3
                                "
                            >
                                <div class="min-w-0">

                                    <p
                                        class="
                                            truncate
                                            text-[8px]
                                            font-bold
                                            text-[#3f372e]
                                        "
                                    >
                                        {{ $transfer->transfer_code }}
                                    </p>

                                    <p
                                        class="
                                            mt-1
                                            text-[7px]
                                            leading-[14px]
                                            text-[#817769]
                                        "
                                    >
                                        {{ $transfer->origin_name }}
                                        →
                                        {{ $transfer->destination_name }}
                                    </p>

                                </div>

                                <span
                                    class="
                                        shrink-0
                                        rounded-full
                                        px-2 py-1
                                        text-[6px]
                                        font-bold
                                        uppercase
                                        {{ $transferStatusClass }}
                                    "
                                >
                                    {{ str_replace(
                                        '_',
                                        ' ',
                                        $transfer->status
                                    ) }}
                                </span>

                            </div>

                            <div
                                class="
                                    mt-2.5
                                    flex flex-wrap
                                    gap-x-3 gap-y-1
                                    text-[6.5px]
                                    text-[#978d80]
                                "
                            >
                                <span>
                                    {{ $transfer->vehicle_type }}
                                </span>

                                @if($transfer->vehicle_plate)
                                    <span>
                                        {{ $transfer->vehicle_plate }}
                                    </span>
                                @endif

                                <span>
                                    {{ $transfer->parcel_count }}
                                    {{ $transfer->parcel_count === 1
                                        ? 'parcel'
                                        : 'parcels'
                                    }}
                                </span>
                            </div>
                        </div>

                    @empty

                        <div
                            class="
                                rounded-[13px]
                                border border-dashed
                                border-[#ddd2c1]
                                p-5
                                text-center
                            "
                        >
                            <p
                                class="
                                    text-[7.5px]
                                    text-[#918677]
                                "
                            >
                                No hub transfers yet.
                            </p>
                        </div>

                    @endforelse

                </div>
            </section>

        </div>

    </div>

</div>

@endsection