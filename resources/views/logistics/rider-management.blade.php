@extends('layouts.logistics')

@section('title', 'Rider Management | SARI Logistics')
@section('page-title', 'Rider Management')

@section('content')

<div
    class="
        mx-auto w-full
        max-w-[1800px]
        space-y-4
    "
>

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
                absolute -right-[90px] -top-[120px]
                h-[260px] w-[260px]
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
                        bg-[#f2f7fd]
                        text-[#4d79a1]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[20px] w-[20px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="8" cy="8" r="3"></circle>
                        <circle cx="17" cy="9" r="2.5"></circle>

                        <path
                            d="M2.5 20c.5-4 2.5-6 5.5-6"
                        ></path>

                        <path
                            d="M13 15c3.2 0 5.2 1.7 5.7 5"
                        ></path>
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
                        Rider Network
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
                        Manage your delivery workforce
                    </h2>

                    <p
                        class="
                            mt-1
                            max-w-[720px]
                            text-[8.5px]
                            leading-[18px]
                            text-[#807668]
                        "
                    >
                        Monitor Rider availability, assigned pickup batches,
                        last-mile work, service areas, and vehicle information
                        across your Logistics network.
                    </p>

                </div>
            </div>


            {{-- Coverage --}}
            <div
                class="
                    grid grid-cols-2 gap-2
                    sm:flex
                "
            >

                <div
                    class="
                        min-w-[120px]
                        rounded-[12px]
                        border border-[#e7dece]
                        bg-[#fffdf9]
                        px-4 py-3
                    "
                >
                    <p
                        class="
                            text-[6.5px]
                            font-bold
                            uppercase
                            tracking-[.1em]
                            text-[#9a8f80]
                        "
                    >
                        Municipalities
                    </p>

                    <p
                        class="
                            mt-1
                            text-[18px]
                            font-bold
                            text-[#2b251e]
                        "
                    >
                        {{ $stats['municipalities'] }}
                    </p>
                </div>

                <div
                    class="
                        min-w-[120px]
                        rounded-[12px]
                        border border-[#e7dece]
                        bg-[#fffdf9]
                        px-4 py-3
                    "
                >
                    <p
                        class="
                            text-[6.5px]
                            font-bold
                            uppercase
                            tracking-[.1em]
                            text-[#9a8f80]
                        "
                    >
                        Barangays
                    </p>

                    <p
                        class="
                            mt-1
                            text-[18px]
                            font-bold
                            text-[#2b251e]
                        "
                    >
                        {{ $stats['barangays'] }}
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

        {{-- Approved --}}
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
                            font-bold
                            uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Approved Riders
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
                        {{ $stats['active'] }} active accounts
                    </p>

                </div>

                <div
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
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M5 21c.6-4.5 3-7 7-7s6.4 2.5 7 7"></path>
                    </svg>
                </div>

            </div>
        </div>


        {{-- Online --}}
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
                            font-bold
                            uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Online Riders
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
                        {{ $stats['online'] }}
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[7.5px]
                            text-[#918779]
                        "
                    >
                        Ready or currently working
                    </p>

                </div>

                <div
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
                </div>

            </div>
        </div>


        {{-- Busy --}}
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
                            font-bold
                            uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Busy Now
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
                        {{ $stats['busy'] }}
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[7.5px]
                            text-[#918779]
                        "
                    >
                        Active pickup or last-mile work
                    </p>

                </div>

                <div
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-[11px]
                        bg-[#fff6e5]
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
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 7v5l3 2"></path>
                    </svg>
                </div>

            </div>
        </div>


        {{-- Available --}}
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
                            font-bold
                            uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Available Now
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
                        {{ $stats['available'] }}
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[7.5px]
                            text-[#918779]
                        "
                    >
                        Online with no active work
                    </p>

                </div>

                <div
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
                        <path d="M4 12h16"></path>
                        <path d="m15 7 5 5-5 5"></path>
                    </svg>
                </div>

            </div>
        </div>

    </section>


    {{-- =====================================================
        RIDER DIRECTORY
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

        {{-- Header --}}
        <div
            class="
                flex flex-col gap-3
                border-b border-[#eee5d8]
                px-5 py-4
                xl:flex-row
                xl:items-center
                xl:justify-between
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
                    Rider Directory
                </p>

                <h3
                    class="
                        mt-1
                        text-[13px]
                        font-bold
                        text-[#2e2821]
                    "
                >
                    Approved Logistics Riders
                </h3>

                <p
                    class="
                        mt-0.5
                        text-[8px]
                        text-[#918779]
                    "
                >
                    Current service area, availability and assignment status.
                </p>

            </div>


            {{-- Search --}}
            <div
                class="
                    flex flex-col gap-2
                    sm:flex-row
                    sm:items-center
                "
            >

                <div class="relative">

                    <svg
                        viewBox="0 0 24 24"
                        class="
                            pointer-events-none
                            absolute left-3 top-1/2
                            h-[14px] w-[14px]
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
                        id="riderManagementSearch"
                        type="search"
                        placeholder="Search Rider..."
                        class="
                            h-[36px]
                            w-full
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
                            sm:w-[210px]
                        "
                    >

                </div>

                <div
                    class="
                        flex overflow-hidden
                        rounded-[10px]
                        border border-[#e5dccf]
                        bg-white
                    "
                >

                    <button
                        type="button"
                        data-rider-filter="all"
                        class="
                            rider-filter-button
                            bg-[#fff6e5]
                            px-3 py-2
                            text-[7.5px]
                            font-semibold
                            text-[#a96e05]
                        "
                    >
                        All
                    </button>

                    <button
                        type="button"
                        data-rider-filter="available"
                        class="
                            rider-filter-button
                            px-3 py-2
                            text-[7.5px]
                            font-medium
                            text-[#756b5f]
                            transition
                            hover:bg-[#faf6ee]
                        "
                    >
                        Available
                    </button>

                    <button
                        type="button"
                        data-rider-filter="busy"
                        class="
                            rider-filter-button
                            px-3 py-2
                            text-[7.5px]
                            font-medium
                            text-[#756b5f]
                            transition
                            hover:bg-[#faf6ee]
                        "
                    >
                        Busy
                    </button>

                    <button
                        type="button"
                        data-rider-filter="offline"
                        class="
                            rider-filter-button
                            px-3 py-2
                            text-[7.5px]
                            font-medium
                            text-[#756b5f]
                            transition
                            hover:bg-[#faf6ee]
                        "
                    >
                        Offline
                    </button>

                </div>

            </div>

        </div>


        {{-- Table --}}
        <div class="overflow-x-auto">

            <table
                class="
                    w-full
                    min-w-[1100px]
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
                            Rider
                        </th>

                        <th class="px-4 py-3 font-semibold">
                            Service Area
                        </th>

                        <th class="px-4 py-3 font-semibold">
                            Vehicle
                        </th>

                        <th class="px-4 py-3 font-semibold">
                            Account
                        </th>

                        <th class="px-4 py-3 font-semibold">
                            Availability
                        </th>

                        <th class="px-4 py-3 font-semibold">
                            Current Work
                        </th>

                    </tr>
                </thead>


                <tbody
                    id="riderManagementTableBody"
                    class="divide-y divide-[#f0e9df]"
                >

                    @forelse($riderRows as $row)

                        @php
                            $rider =
                                $row['rider'];

                            $name =
                                trim(
                                    $rider->first_name
                                    . ' '
                                    . $rider->last_name
                                );

                            $initials =
                                strtoupper(
                                    mb_substr(
                                        $rider->first_name ?: '',
                                        0,
                                        1
                                    )
                                    .
                                    mb_substr(
                                        $rider->last_name ?: '',
                                        0,
                                        1
                                    )
                                );

                            $searchText =
                                strtolower(
                                    implode(
                                        ' ',
                                        [
                                            $name,
                                            $rider->email,
                                            $rider->contact_no,
                                            $rider->municipality_name,
                                            $rider->barangay_name,
                                            $rider->vehicle_type,
                                            $rider->vehicle_model,
                                            $rider->plate_number,
                                        ]
                                    )
                                );

                            $state =
                                $row['state'];
                        @endphp

                        <tr
                            data-rider-row
                            data-state="{{ $state }}"
                            data-search="{{ $searchText }}"
                            class="
                                text-[8px]
                                text-[#61584d]
                                transition
                                hover:bg-[#fffdf9]
                            "
                        >

                            {{-- Rider --}}
                            <td class="px-5 py-3.5">

                                <div
                                    class="
                                        flex items-center
                                        gap-3
                                    "
                                >

                                    <div
                                        class="
                                            grid h-9 w-9
                                            shrink-0 place-items-center
                                            rounded-full
                                            bg-[#fff3da]
                                            text-[8px]
                                            font-bold
                                            text-[#aa7209]
                                        "
                                    >
                                        {{ $initials ?: 'R' }}
                                    </div>

                                    <div class="min-w-0">

                                        <p
                                            class="
                                                max-w-[180px]
                                                truncate
                                                font-bold
                                                text-[#332c24]
                                            "
                                        >
                                            {{ $name }}
                                        </p>

                                        <p
                                            class="
                                                mt-0.5
                                                max-w-[180px]
                                                truncate
                                                text-[6.5px]
                                                text-[#92877a]
                                            "
                                        >
                                            {{ $rider->email }}
                                        </p>

                                        <p
                                            class="
                                                mt-0.5
                                                text-[6.5px]
                                                text-[#aaa094]
                                            "
                                        >
                                            {{ $rider->contact_no ?: 'No contact number' }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            {{-- Service Area --}}
                            <td class="px-4 py-3.5">

                                <p
                                    class="
                                        font-semibold
                                        text-[#51483d]
                                    "
                                >
                                    {{ $rider->barangay_name ?: 'Unassigned Barangay' }}
                                </p>

                                <p
                                    class="
                                        mt-1
                                        text-[6.5px]
                                        text-[#92877a]
                                    "
                                >
                                    {{ $rider->municipality_name ?: '—' }}

                                    @if($rider->province_name)
                                        · {{ $rider->province_name }}
                                    @endif
                                </p>

                            </td>


                            {{-- Vehicle --}}
                            <td class="px-4 py-3.5">

                                <div
                                    class="
                                        inline-flex
                                        items-center gap-2
                                    "
                                >

                                    <span
                                        class="
                                            grid h-7 w-7
                                            place-items-center
                                            rounded-[8px]
                                            bg-[#f6f2eb]
                                            text-[#756b5f]
                                        "
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            class="h-[14px] w-[14px]"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <circle cx="6" cy="17" r="2"></circle>
                                            <circle cx="18" cy="17" r="2"></circle>
                                            <path d="M8 17h8"></path>
                                            <path d="M6 15 8 9h7l3 6"></path>
                                        </svg>
                                    </span>

                                    <div>

                                        <p
                                            class="
                                                font-semibold
                                                text-[#51483d]
                                            "
                                        >
                                            {{ $rider->vehicle_type ?: 'Vehicle' }}
                                        </p>

                                        <p
                                            class="
                                                mt-0.5
                                                text-[6.5px]
                                                text-[#92877a]
                                            "
                                        >
                                            @if($rider->vehicle_model)
                                                {{ $rider->vehicle_model }}
                                                ·
                                            @endif

                                            {{ $rider->plate_number ?: 'No plate' }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            {{-- Account --}}
                            <td class="px-4 py-3.5">

                                @if($row['is_active'])

                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            gap-1.5
                                            rounded-full
                                            bg-[#edf8f1]
                                            px-2.5 py-1
                                            text-[6.5px]
                                            font-semibold
                                            text-[#327a53]
                                        "
                                    >
                                        <span
                                            class="
                                                h-1.5 w-1.5
                                                rounded-full
                                                bg-[#4ca36e]
                                            "
                                        ></span>

                                        Active
                                    </span>

                                @else

                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            gap-1.5
                                            rounded-full
                                            bg-[#fff1f1]
                                            px-2.5 py-1
                                            text-[6.5px]
                                            font-semibold
                                            text-[#a65d5d]
                                        "
                                    >
                                        <span
                                            class="
                                                h-1.5 w-1.5
                                                rounded-full
                                                bg-[#c96666]
                                            "
                                        ></span>

                                        Inactive
                                    </span>

                                @endif

                            </td>


                            {{-- Availability --}}
                            <td class="px-4 py-3.5">

                                @if($state === 'available')

                                    <span
                                        class="
                                            inline-flex
                                            rounded-full
                                            bg-[#eef7f1]
                                            px-2.5 py-1
                                            text-[6.5px]
                                            font-semibold
                                            text-[#56816a]
                                        "
                                    >
                                        Available
                                    </span>

                                @elseif($state === 'busy')

                                    <span
                                        class="
                                            inline-flex
                                            rounded-full
                                            bg-[#fff6e5]
                                            px-2.5 py-1
                                            text-[6.5px]
                                            font-semibold
                                            text-[#a96e05]
                                        "
                                    >
                                        Busy
                                    </span>

                                @elseif($state === 'offline')

                                    <span
                                        class="
                                            inline-flex
                                            rounded-full
                                            bg-[#f4f1ed]
                                            px-2.5 py-1
                                            text-[6.5px]
                                            font-semibold
                                            text-[#756b5f]
                                        "
                                    >
                                        Offline
                                    </span>

                                @else

                                    <span
                                        class="
                                            inline-flex
                                            rounded-full
                                            bg-[#fff1f1]
                                            px-2.5 py-1
                                            text-[6.5px]
                                            font-semibold
                                            text-[#a65d5d]
                                        "
                                    >
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            {{-- Current Work --}}
                            <td class="px-4 py-3">

                                @if(!empty($row['work_items']))

                                    <div
                                        class="
                                            min-w-[300px]
                                            space-y-2
                                        "
                                    >

                                        @foreach($row['work_items'] as $work)

                                            @php
                                                $isPickup =
                                                    $work['type']
                                                    === 'pickup';

                                                $workUrl =
                                                    $isPickup
                                                        ? route(
                                                            'logistics.delivery-monitoring'
                                                        )
                                                        : route(
                                                            'logistics.last-mile-assignment'
                                                        );
                                            @endphp

                                            <a
                                                href="{{ $workUrl }}"
                                                class="
                                                    block
                                                    rounded-[11px]
                                                    border
                                                    px-3 py-2.5
                                                    transition

                                                    {{
                                                        $isPickup
                                                            ? 'border-[#f0dfba] bg-[#fffaf0] hover:border-[#deb96b]'
                                                            : 'border-[#dce4ed] bg-[#f6f9fc] hover:border-[#aec5d9]'
                                                    }}
                                                "
                                            >

                                                <div
                                                    class="
                                                        flex items-center
                                                        justify-between gap-3
                                                    "
                                                >

                                                    <p
                                                        class="
                                                            text-[6.5px]
                                                            font-bold
                                                            uppercase
                                                            tracking-[.08em]

                                                            {{
                                                                $isPickup
                                                                    ? 'text-[#a96e05]'
                                                                    : 'text-[#4d79a1]'
                                                            }}
                                                        "
                                                    >
                                                        {{ $work['label'] }}
                                                    </p>

                                                    <span
                                                        class="
                                                            text-[6.5px]
                                                            font-semibold
                                                            text-[#817769]
                                                        "
                                                    >
                                                        {{ $work['title'] }}
                                                    </span>

                                                </div>

                                                <p
                                                    class="
                                                        mt-1
                                                        max-w-[300px]
                                                        truncate
                                                        text-[7.5px]
                                                        font-semibold
                                                        text-[#443c32]
                                                    "
                                                >
                                                    {{ $work['route'] }}
                                                </p>

                                                <p
                                                    class="
                                                        mt-1
                                                        text-[6.5px]
                                                        text-[#92877a]
                                                    "
                                                >
                                                    {{ $work['status'] }}
                                                </p>

                                            </a>

                                        @endforeach

                                    </div>

                                @else

                                    <div
                                        class="
                                            flex items-center gap-2
                                        "
                                    >

                                        <span
                                            class="
                                                grid h-6 w-6
                                                place-items-center
                                                rounded-full

                                                {{
                                                    $state === 'available'
                                                        ? 'bg-[#eef7f1] text-[#56816a]'
                                                        : 'bg-[#f4f1ed] text-[#92877a]'
                                                }}
                                            "
                                        >
                                            @if($state === 'available')

                                                <svg
                                                    viewBox="0 0 24 24"
                                                    class="h-[12px] w-[12px]"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path d="m7 12 3 3 7-7"></path>
                                                </svg>

                                            @else

                                                <span
                                                    class="
                                                        h-1.5 w-1.5
                                                        rounded-full
                                                        bg-current
                                                    "
                                                ></span>

                                            @endif
                                        </span>

                                        <div>

                                            <p
                                                class="
                                                    text-[7.5px]
                                                    font-semibold
                                                    text-[#61584d]
                                                "
                                            >
                                                {{
                                                    $state === 'available'
                                                        ? 'Ready for assignment'
                                                        : (
                                                            $state === 'offline'
                                                                ? 'Rider is offline'
                                                                : 'No active work'
                                                        )
                                                }}
                                            </p>

                                            @if($state === 'available')

                                                <p
                                                    class="
                                                        mt-0.5
                                                        text-[6.5px]
                                                        text-[#aaa094]
                                                    "
                                                >
                                                    Available for Logistics dispatch
                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="6"
                                class="
                                    px-5 py-12
                                    text-center
                                "
                            >
                                <div
                                    class="
                                        mx-auto
                                        grid h-11 w-11
                                        place-items-center
                                        rounded-full
                                        bg-[#faf5ec]
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
                                        <circle cx="8" cy="8" r="3"></circle>
                                        <circle cx="17" cy="9" r="2.5"></circle>
                                        <path d="M2.5 20c.5-4 2.5-6 5.5-6"></path>
                                        <path d="M13 15c3.2 0 5.2 1.7 5.7 5"></path>
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
                                    No approved Riders yet.
                                </p>

                                <p
                                    class="
                                        mt-1
                                        text-[7.5px]
                                        text-[#918677]
                                    "
                                >
                                    Approved Rider applications will appear here.
                                </p>
                            </td>
                        </tr>

                    @endforelse

                    {{-- Search/filter empty state --}}
                    <tr
                        id="riderFilterEmptyState"
                        class="hidden"
                    >
                        <td
                            colspan="6"
                            class="
                                px-5 py-12
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
                                No Riders match your search.
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-[7.5px]
                                    text-[#918677]
                                "
                            >
                                Try another name, area, vehicle, or status.
                            </p>
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </section>

</div>


@push('scripts')

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const searchInput =
            document.getElementById(
                'riderManagementSearch'
            );

        const rows =
            Array.from(
                document.querySelectorAll(
                    '[data-rider-row]'
                )
            );

        const filterButtons =
            Array.from(
                document.querySelectorAll(
                    '[data-rider-filter]'
                )
            );

        const emptyState =
            document.getElementById(
                'riderFilterEmptyState'
            );

        let activeFilter = 'all';


        function normalize(value) {
            return String(value || '')
                .toLowerCase()
                .trim();
        }


        function renderRows() {

            const keyword =
                normalize(
                    searchInput?.value
                );

            let visibleCount = 0;

            rows.forEach(function (row) {

                const searchText =
                    normalize(
                        row.dataset.search
                    );

                const state =
                    normalize(
                        row.dataset.state
                    );

                const matchesSearch =
                    ! keyword
                    || searchText.includes(
                        keyword
                    );

                const matchesFilter =
                    activeFilter === 'all'
                    || state === activeFilter;

                const visible =
                    matchesSearch
                    && matchesFilter;

                row.classList.toggle(
                    'hidden',
                    ! visible
                );

                if (visible) {
                    visibleCount++;
                }

            });

            emptyState?.classList.toggle(
                'hidden',
                visibleCount !== 0
            );
        }


        searchInput?.addEventListener(
            'input',
            renderRows
        );


        filterButtons.forEach(
            function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        activeFilter =
                            button.dataset
                                .riderFilter
                            || 'all';

                        filterButtons.forEach(
                            function (item) {

                                const isActive =
                                    item === button;

                                item.classList.toggle(
                                    'bg-[#fff6e5]',
                                    isActive
                                );

                                item.classList.toggle(
                                    'text-[#a96e05]',
                                    isActive
                                );

                                item.classList.toggle(
                                    'font-semibold',
                                    isActive
                                );

                                item.classList.toggle(
                                    'text-[#756b5f]',
                                    ! isActive
                                );

                                item.classList.toggle(
                                    'font-medium',
                                    ! isActive
                                );
                            }
                        );

                        renderRows();
                    }
                );
            }
        );

    }
);
</script>

@endpush

@endsection