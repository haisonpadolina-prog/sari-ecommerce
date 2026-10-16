@extends('layouts.courier')

@section('title', 'Rider Dashboard')
@section('header-title', 'Rider Dashboard')
@section('header-subtitle', 'Logistics Assigned Operations')

@section('content')

@php
    $hasMarketplaceWork =
        $currentDelivery
        instanceof
        \App\Models\Orders\MarketplaceOrder;

    $isLogisticsPickup =
        $hasMarketplaceWork
        && (bool) $currentDelivery
            ->logisticsParcel;

    $marketplaceStatus =
        $hasMarketplaceWork
            ? $currentDelivery->status
            : null;
@endphp


<div class="mx-auto max-w-[1620px] space-y-4">

    @if(session('success'))
        <div
            class="
                rounded-2xl
                border border-emerald-200
                bg-emerald-50
                px-5 py-4
                text-[10px] font-semibold
                text-emerald-700
            "
        >
            {{ session('success') }}
        </div>
    @endif


    @if($errors->any())
        <div
            class="
                rounded-2xl
                border border-rose-200
                bg-rose-50
                px-5 py-4
                text-[10px] font-semibold
                text-rose-700
            "
        >
            {{ $errors->first() }}
        </div>
    @endif


    {{-- =====================================================
        PAGE INTRO
    ====================================================== --}}

    <section
        class="
            flex flex-col
            justify-between
            gap-4
            border-b border-[#eadfc9]
            pb-5
            lg:flex-row
            lg:items-end
        "
    >

        <div>

            <div
                class="
                    flex flex-wrap
                    items-center
                    gap-2
                "
            >

                <h2
                    class="
                        text-[15px]
                        font-bold
                        text-[#211d17]
                    "
                >
                    Rider Operations
                </h2>


                <span
                    class="
                        rounded-full
                        border border-[#eadfc9]
                        bg-white
                        px-2.5 py-1
                        text-[8px]
                        font-semibold
                        text-[#8a7f70]
                    "
                >
                    SARI Logistics Network
                </span>

            </div>


            <p
                class="
                    mt-2
                    max-w-[760px]
                    text-[9px]
                    leading-5
                    text-[#817769]
                "
            >
                Seller pickup work and destination-hub
                last-mile delivery are tracked separately
                so every Rider sees the correct job,
                workflow, and earnings.
            </p>

        </div>


        <div class="flex flex-wrap gap-2">

            <a
                href="{{ route('courier.requests') }}"
                class="
                    rounded-xl
                    border border-[#e6dccb]
                    bg-white
                    px-4 py-2.5
                    text-[9px]
                    font-semibold
                    text-[#62594d]
                    transition
                    hover:border-[#d6bd8e]
                    hover:text-[#a96e05]
                "
            >
                Pickup Assignments
            </a>


            <a
                href="{{ route('courier.last-mile') }}"
                class="
                    rounded-xl
                    bg-[#D99600]
                    px-4 py-2.5
                    text-[9px]
                    font-semibold
                    text-white
                    shadow-[0_8px_20px_rgba(217,150,0,.14)]
                "
            >
                Last-Mile Deliveries →
            </a>

        </div>

    </section>


    {{-- =====================================================
        MAIN STATS
    ====================================================== --}}

    <section
        class="
            grid
            grid-cols-2
            gap-3
            xl:grid-cols-4
        "
    >

        @foreach([
            [
                'Waiting to Start',
                $stats['assigned_waiting'],
                'bg-[#fff2d8] text-[#bb7b00]',
            ],
            [
                'Active Work',
                $stats['active_deliveries'],
                'bg-[#eaf2f7] text-[#3C6E91]',
            ],
            [
                'Completed Today',
                $stats['completed_today'],
                'bg-[#eaf3ed] text-[#4F7D63]',
            ],
            [
                "Today's Earnings",
                '₱'.number_format(
                    $stats['earnings_today'],
                    2
                ),
                'bg-[#f0ecf6] text-[#715A86]',
            ],
        ] as [$label, $value, $tone])

            <article
                class="
                    rounded-2xl
                    border border-[#eadfc9]
                    bg-[#fffdf8]
                    p-4
                "
            >

                <div
                    class="
                        grid
                        h-9 w-9
                        place-items-center
                        rounded-xl
                        {{ $tone }}
                    "
                >
                    <span class="text-[9px] font-bold">
                        •
                    </span>
                </div>


                <p
                    class="
                        mt-3
                        text-[8px]
                        text-[#817769]
                    "
                >
                    {{ $label }}
                </p>


                <p
                    class="
                        mt-1
                        text-[20px]
                        font-bold
                        tracking-[-.035em]
                        text-[#201c16]
                    "
                >
                    {{ $value }}
                </p>

            </article>

        @endforeach

    </section>


    {{-- =====================================================
        OPERATION BREAKDOWN
    ====================================================== --}}

    <section
        class="
            grid
            gap-3
            sm:grid-cols-2
            xl:grid-cols-4
        "
    >

        <article
            class="
                rounded-2xl
                border border-[#eadfc9]
                bg-white
                p-4
            "
        >

            <p
                class="
                    text-[7px]
                    font-semibold
                    uppercase
                    tracking-[.11em]
                    text-[#a09484]
                "
            >
                Pickup Queue
            </p>

            <p
                class="
                    mt-2
                    text-[18px]
                    font-bold
                    text-[#29241e]
                "
            >
                {{ $stats['pickup_waiting'] }}
            </p>

            <p
                class="
                    mt-1
                    text-[8px]
                    text-[#918677]
                "
            >
                Seller pickup jobs waiting to start.
            </p>

        </article>


        <article
            class="
                rounded-2xl
                border border-[#eadfc9]
                bg-white
                p-4
            "
        >

            <p
                class="
                    text-[7px]
                    font-semibold
                    uppercase
                    tracking-[.11em]
                    text-[#a09484]
                "
            >
                Pickup Active
            </p>

            <p
                class="
                    mt-2
                    text-[18px]
                    font-bold
                    text-[#29241e]
                "
            >
                {{ $stats['pickup_active'] }}
            </p>

            <p
                class="
                    mt-1
                    text-[8px]
                    text-[#918677]
                "
            >
                Pickup or direct jobs currently moving.
            </p>

        </article>


        <article
            class="
                rounded-2xl
                border border-[#eadfc9]
                bg-white
                p-4
            "
        >

            <p
                class="
                    text-[7px]
                    font-semibold
                    uppercase
                    tracking-[.11em]
                    text-[#a09484]
                "
            >
                Last-Mile Waiting
            </p>

            <p
                class="
                    mt-2
                    text-[18px]
                    font-bold
                    text-[#29241e]
                "
            >
                {{ $stats['last_mile_waiting'] }}
            </p>

            <p
                class="
                    mt-1
                    text-[8px]
                    text-[#918677]
                "
            >
                Destination-hub batches assigned to you.
            </p>

        </article>


        <article
            class="
                rounded-2xl
                border border-[#eadfc9]
                bg-white
                p-4
            "
        >

            <p
                class="
                    text-[7px]
                    font-semibold
                    uppercase
                    tracking-[.11em]
                    text-[#a09484]
                "
            >
                Last-Mile Active
            </p>

            <p
                class="
                    mt-2
                    text-[18px]
                    font-bold
                    text-[#29241e]
                "
            >
                {{ $stats['last_mile_active'] }}
            </p>

            <p
                class="
                    mt-1
                    text-[8px]
                    text-[#918677]
                "
            >
                Batches currently out for delivery.
            </p>

        </article>

    </section>


    {{-- =====================================================
        CURRENT WORK
    ====================================================== --}}

    <section
        class="
            grid
            gap-4
            xl:grid-cols-2
        "
    >

        {{-- PICKUP / DIRECT --}}
        <article
            class="
                overflow-hidden
                rounded-[20px]
                border border-[#eadfc9]
                bg-[#fffdf8]
            "
        >

            <div
                class="
                    flex
                    items-center
                    justify-between
                    gap-3
                    border-b border-[#eadfc9]
                    px-5 py-4
                "
            >

                <div>

                    <h3
                        class="
                            text-[13px]
                            font-bold
                            text-[#211d17]
                        "
                    >
                        Pickup / Direct Work
                    </h3>

                    <p
                        class="
                            mt-1
                            text-[8px]
                            text-[#918677]
                        "
                    >
                        Seller pickup leg or legacy direct delivery.
                    </p>

                </div>


                <a
                    href="{{ route('courier.requests') }}"
                    class="
                        text-[8px]
                        font-semibold
                        text-[#a96e05]
                    "
                >
                    View pickup jobs →
                </a>

            </div>


            @if($hasMarketplaceWork)

                <div class="p-5">

                    <div
                        class="
                            flex flex-col
                            gap-4
                            sm:flex-row
                            sm:items-start
                            sm:justify-between
                        "
                    >

                        <div>

                            <div
                                class="
                                    flex flex-wrap
                                    items-center
                                    gap-2
                                "
                            >

                                <h4
                                    class="
                                        text-[13px]
                                        font-bold
                                        text-[#201c16]
                                    "
                                >
                                    {{ $currentDelivery->order_number }}
                                </h4>


                                <span
                                    class="
                                        rounded-full
                                        border border-[#d2dfe9]
                                        bg-[#f2f7fb]
                                        px-2.5 py-1
                                        text-[7px]
                                        font-bold
                                        text-[#3C6E91]
                                    "
                                >
                                    {{
                                        strtoupper(
                                            $currentDelivery
                                                ->statusLabel()
                                        )
                                    }}
                                </span>


                                <span
                                    class="
                                        rounded-full
                                        border border-[#eadfc9]
                                        bg-white
                                        px-2.5 py-1
                                        text-[7px]
                                        font-semibold
                                        text-[#7d7264]
                                    "
                                >
                                    {{
                                        $isLogisticsPickup
                                            ? 'SELLER PICKUP'
                                            : 'DIRECT DELIVERY'
                                    }}
                                </span>

                            </div>


                            <p
                                class="
                                    mt-2
                                    text-[8px]
                                    leading-4
                                    text-[#817769]
                                "
                            >
                                {{
                                    $currentDelivery->pickup_name
                                    ?: 'Seller'
                                }}
                                →
                                {{
                                    $isLogisticsPickup
                                        ? 'Origin Hub'
                                        : (
                                            $currentDelivery->buyer_name
                                            ?: 'Buyer'
                                        )
                                }}
                            </p>

                        </div>


                        @if(!$isLogisticsPickup)

                            <div
                                class="
                                    rounded-xl
                                    border border-[#eadfc9]
                                    bg-white
                                    px-4 py-2.5
                                "
                            >

                                <p
                                    class="
                                        text-[7px]
                                        text-[#918677]
                                    "
                                >
                                    Delivery Fee
                                </p>

                                <p
                                    class="
                                        mt-1
                                        text-[12px]
                                        font-bold
                                        text-[#201c16]
                                    "
                                >
                                    ₱{{
                                        number_format(
                                            $currentDelivery
                                                ->courierDeliveryFee(),
                                            2
                                        )
                                    }}
                                </p>

                            </div>

                        @else

                            <div
                                class="
                                    rounded-xl
                                    border border-[#eadfc9]
                                    bg-white
                                    px-4 py-2.5
                                "
                            >

                                <p
                                    class="
                                        text-[7px]
                                        text-[#918677]
                                    "
                                >
                                    Role
                                </p>

                                <p
                                    class="
                                        mt-1
                                        text-[9px]
                                        font-bold
                                        text-[#201c16]
                                    "
                                >
                                    Operational Pickup
                                </p>

                            </div>

                        @endif

                    </div>


                    <div
                        class="
                            mt-5
                            rounded-[16px]
                            border border-[#eadfc9]
                            bg-white
                            p-4
                        "
                    >

                        @if(
                            $marketplaceStatus
                            === 'courier_accepted'
                        )

                            <p
                                class="
                                    text-[8px]
                                    font-semibold
                                    text-[#8a6400]
                                "
                            >
                                SARI Logistics assigned this
                                Seller pickup to you.
                            </p>

                            <form
                                method="POST"
                                action="{{
                                    route(
                                        'courier.orders.proceed-pickup',
                                        $currentDelivery
                                    )
                                }}"
                                class="mt-3"
                            >
                                @csrf

                                <button
                                    class="
                                        w-full
                                        rounded-xl
                                        bg-[#D99600]
                                        px-4 py-3
                                        text-[9px]
                                        font-semibold
                                        text-white
                                    "
                                >
                                    Proceed to Pickup →
                                </button>
                            </form>


                        @elseif(
                            $marketplaceStatus
                            === 'heading_pickup'
                        )

                            <p
                                class="
                                    text-[8px]
                                    font-semibold
                                    text-[#8a6400]
                                "
                            >
                                You are heading to the Seller
                                pickup point.
                            </p>

                            <form
                                method="POST"
                                action="{{
                                    route(
                                        'courier.orders.arrived-pickup',
                                        $currentDelivery
                                    )
                                }}"
                                class="mt-3"
                            >
                                @csrf

                                <button
                                    class="
                                        w-full
                                        rounded-xl
                                        bg-[#D99600]
                                        px-4 py-3
                                        text-[9px]
                                        font-semibold
                                        text-white
                                    "
                                >
                                    I Arrived at Pickup ✓
                                </button>
                            </form>


                        @elseif(
                            $marketplaceStatus
                            === 'arrived_pickup'
                        )

                            <p
                                class="
                                    text-[8px]
                                    font-semibold
                                    text-[#8a6400]
                                "
                            >
                                Check the parcel, then confirm
                                the Seller handoff.
                            </p>

                            <form
                                method="POST"
                                action="{{
                                    route(
                                        'courier.orders.confirm-pickup',
                                        $currentDelivery
                                    )
                                }}"
                                class="mt-3"
                            >
                                @csrf

                                <button
                                    class="
                                        w-full
                                        rounded-xl
                                        bg-[#3C6E91]
                                        px-4 py-3
                                        text-[9px]
                                        font-semibold
                                        text-white
                                    "
                                >
                                    Confirm Item Pickup →
                                </button>
                            </form>


                        @elseif(
                            $marketplaceStatus
                            === 'in_transit'
                            && $isLogisticsPickup
                        )

                            <div
                                class="
                                    rounded-xl
                                    border border-blue-100
                                    bg-blue-50
                                    px-4 py-3
                                "
                            >

                                <p
                                    class="
                                        text-[8px]
                                        font-semibold
                                        text-[#3C6E91]
                                    "
                                >
                                    Parcel picked up. Bring it to
                                    the assigned origin hub for
                                    Logistics intake.
                                </p>

                                <p
                                    class="
                                        mt-1
                                        text-[7px]
                                        leading-4
                                        text-[#6f8190]
                                    "
                                >
                                    Final Buyer delivery belongs
                                    to the destination barangay
                                    last-mile Rider.
                                </p>

                            </div>


                        @elseif(
                            $marketplaceStatus
                            === 'in_transit'
                        )

                            <p
                                class="
                                    text-[8px]
                                    font-semibold
                                    text-[#3C6E91]
                                "
                            >
                                Direct delivery is in transit.
                                Confirm only when you reach
                                the Buyer.
                            </p>

                            <form
                                method="POST"
                                action="{{
                                    route(
                                        'courier.orders.arrived-buyer',
                                        $currentDelivery
                                    )
                                }}"
                                class="mt-3"
                            >
                                @csrf

                                <button
                                    class="
                                        w-full
                                        rounded-xl
                                        bg-[#3C6E91]
                                        px-4 py-3
                                        text-[9px]
                                        font-semibold
                                        text-white
                                    "
                                >
                                    Confirm Arrived at Buyer →
                                </button>
                            </form>


                        @elseif(
                            $marketplaceStatus
                            === 'arrived_buyer'
                            && !$isLogisticsPickup
                        )

                            <p
                                class="
                                    text-[8px]
                                    font-semibold
                                    text-[#4F7D63]
                                "
                            >
                                Buyer location reached.
                                Complete the direct handoff.
                            </p>

                            <form
                                method="POST"
                                action="{{
                                    route(
                                        'courier.orders.complete',
                                        $currentDelivery
                                    )
                                }}"
                                class="mt-3"
                            >
                                @csrf

                                <button
                                    class="
                                        w-full
                                        rounded-xl
                                        bg-[#4F7D63]
                                        px-4 py-3
                                        text-[9px]
                                        font-semibold
                                        text-white
                                    "
                                >
                                    Confirm Delivery Complete ✓
                                </button>
                            </form>

                        @endif

                    </div>

                </div>

            @else

                <div class="p-10 text-center">

                    <p
                        class="
                            text-[10px]
                            font-semibold
                            text-[#51483d]
                        "
                    >
                        No active pickup or direct-delivery job.
                    </p>

                    <p
                        class="
                            mt-2
                            text-[8px]
                            text-[#918677]
                        "
                    >
                        Pickup work disappears here after the
                        origin hub accepts the parcel.
                    </p>

                </div>

            @endif

        </article>


        {{-- LAST MILE --}}
        <article
            class="
                overflow-hidden
                rounded-[20px]
                border border-[#eadfc9]
                bg-[#fffdf8]
            "
        >

            <div
                class="
                    flex
                    items-center
                    justify-between
                    gap-3
                    border-b border-[#eadfc9]
                    px-5 py-4
                "
            >

                <div>

                    <h3
                        class="
                            text-[13px]
                            font-bold
                            text-[#211d17]
                        "
                    >
                        Last-Mile Work
                    </h3>

                    <p
                        class="
                            mt-1
                            text-[8px]
                            text-[#918677]
                        "
                    >
                        Destination hub → assigned barangay → Buyer.
                    </p>

                </div>


                <a
                    href="{{ route('courier.last-mile') }}"
                    class="
                        text-[8px]
                        font-semibold
                        text-[#a96e05]
                    "
                >
                    Open last mile →
                </a>

            </div>


            @if($currentLastMile)

                <div class="p-5">

                    <div
                        class="
                            flex flex-wrap
                            items-start
                            justify-between
                            gap-4
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-[8px]
                                    font-bold
                                    uppercase
                                    tracking-[.1em]
                                    text-[#b97805]
                                "
                            >
                                {{
                                    $currentLastMile
                                        ->assignment_code
                                }}
                            </p>

                            <h4
                                class="
                                    mt-2
                                    text-[13px]
                                    font-bold
                                    text-[#201c16]
                                "
                            >
                                {{
                                    $currentLastMile
                                        ->facility_name
                                }}
                                →
                                {{
                                    $currentLastMile
                                        ->delivery_area
                                }}
                            </h4>

                            <p
                                class="
                                    mt-2
                                    text-[8px]
                                    text-[#817769]
                                "
                            >
                                {{
                                    $currentLastMile
                                        ->parcel_count
                                }}
                                {{
                                    $currentLastMile
                                        ->parcel_count
                                    === 1
                                        ? 'parcel'
                                        : 'parcels'
                                }}
                            </p>

                        </div>


                        <span
                            class="
                                rounded-full
                                border border-[#eadfc9]
                                bg-[#fff6e1]
                                px-3 py-1.5
                                text-[7px]
                                font-bold
                                uppercase
                                text-[#a56b00]
                            "
                        >
                            {{
                                str_replace(
                                    '_',
                                    ' ',
                                    $currentLastMile
                                        ->status
                                )
                            }}
                        </span>

                    </div>


                    <div
                        class="
                            mt-4
                            grid gap-3
                            sm:grid-cols-2
                        "
                    >

                        <div
                            class="
                                rounded-xl
                                border border-[#eee5d6]
                                bg-white
                                p-3
                            "
                        >

                            <p
                                class="
                                    text-[7px]
                                    uppercase
                                    tracking-[.1em]
                                    text-[#a09484]
                                "
                            >
                                Potential Delivery Fee
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-[12px]
                                    font-bold
                                    text-[#29241e]
                                "
                            >
                                ₱{{
                                    number_format(
                                        (float)
                                        $currentLastMile
                                            ->delivery_fee_total,
                                        2
                                    )
                                }}
                            </p>

                        </div>


                        <div
                            class="
                                rounded-xl
                                border border-[#eee5d6]
                                bg-white
                                p-3
                            "
                        >

                            <p
                                class="
                                    text-[7px]
                                    uppercase
                                    tracking-[.1em]
                                    text-[#a09484]
                                "
                            >
                                Destination Hub
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-[9px]
                                    font-semibold
                                    text-[#29241e]
                                "
                            >
                                {{
                                    $currentLastMile
                                        ->facility_name
                                }}
                            </p>

                        </div>

                    </div>


                    <a
                        href="{{ route('courier.last-mile') }}"
                        class="
                            mt-4
                            inline-flex
                            w-full
                            items-center
                            justify-center
                            rounded-xl
                            bg-[#D99600]
                            px-5 py-3
                            text-[9px]
                            font-semibold
                            text-white
                        "
                    >
                        Continue Last-Mile Workflow →
                    </a>

                </div>

            @else

                <div class="p-10 text-center">

                    <p
                        class="
                            text-[10px]
                            font-semibold
                            text-[#51483d]
                        "
                    >
                        No active last-mile batch.
                    </p>

                    <p
                        class="
                            mt-2
                            text-[8px]
                            text-[#918677]
                        "
                    >
                        Destination-hub assignments will appear
                        here when Logistics dispatches a local
                        batch to you.
                    </p>

                    <a
                        href="{{ route('courier.last-mile') }}"
                        class="
                            mt-4
                            inline-flex
                            rounded-xl
                            border border-[#eadfc9]
                            bg-white
                            px-5 py-3
                            text-[9px]
                            font-semibold
                            text-[#6e6254]
                        "
                    >
                        Open Last-Mile Deliveries
                    </a>

                </div>

            @endif

        </article>

    </section>


    {{-- =====================================================
        LATEST WORK
    ====================================================== --}}

    <section
        class="
            grid
            gap-4
            xl:grid-cols-2
        "
    >

        <article
            class="
                rounded-[20px]
                border border-[#eadfc9]
                bg-[#fffdf8]
                p-5
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

                <div>

                    <h3
                        class="
                            text-[12px]
                            font-bold
                            text-[#211d17]
                        "
                    >
                        Latest Pickup Assignments
                    </h3>

                    <p
                        class="
                            mt-1
                            text-[8px]
                            text-[#918677]
                        "
                    >
                        Seller pickup jobs waiting to start.
                    </p>

                </div>


                <a
                    href="{{ route('courier.requests') }}"
                    class="
                        text-[8px]
                        font-semibold
                        text-[#a96e05]
                    "
                >
                    View all →
                </a>

            </div>


            <div class="mt-4 space-y-2">

                @forelse($assignments as $order)

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            gap-3
                            rounded-xl
                            border border-[#eadfc9]
                            bg-white
                            p-3
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-[9px]
                                    font-bold
                                    text-[#2a251f]
                                "
                            >
                                {{ $order->order_number }}
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-[7px]
                                    text-[#84796c]
                                "
                            >
                                {{
                                    $order->pickup_name
                                    ?: $order->seller
                                        ?->store_name
                                    ?: 'Seller'
                                }}
                            </p>

                        </div>


                        <span
                            class="
                                rounded-full
                                bg-[#fff2d8]
                                px-2.5 py-1
                                text-[7px]
                                font-bold
                                text-[#aa6d00]
                            "
                        >
                            ASSIGNED
                        </span>

                    </div>

                @empty

                    <div
                        class="
                            rounded-xl
                            border border-dashed
                            border-[#ddd2c1]
                            p-5
                            text-center
                            text-[8px]
                            text-[#918677]
                        "
                    >
                        No pickup assignments waiting.
                    </div>

                @endforelse

            </div>

        </article>


        <article
            class="
                rounded-[20px]
                border border-[#eadfc9]
                bg-[#fffdf8]
                p-5
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

                <div>

                    <h3
                        class="
                            text-[12px]
                            font-bold
                            text-[#211d17]
                        "
                    >
                        Active Last-Mile Batches
                    </h3>

                    <p
                        class="
                            mt-1
                            text-[8px]
                            text-[#918677]
                        "
                    >
                        Local delivery work from the
                        destination hub.
                    </p>

                </div>


                <a
                    href="{{ route('courier.last-mile') }}"
                    class="
                        text-[8px]
                        font-semibold
                        text-[#a96e05]
                    "
                >
                    View all →
                </a>

            </div>


            <div class="mt-4 space-y-2">

                @forelse(
                    $lastMileAssignments
                    as $assignment
                )

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            gap-3
                            rounded-xl
                            border border-[#eadfc9]
                            bg-white
                            p-3
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-[9px]
                                    font-bold
                                    text-[#2a251f]
                                "
                            >
                                {{
                                    $assignment
                                        ->assignment_code
                                }}
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-[7px]
                                    text-[#84796c]
                                "
                            >
                                {{
                                    $assignment
                                        ->facility_name
                                }}
                                →
                                {{
                                    $assignment
                                        ->delivery_area
                                }}
                            </p>

                        </div>


                        <span
                            class="
                                rounded-full
                                bg-[#eaf2f7]
                                px-2.5 py-1
                                text-[7px]
                                font-bold
                                uppercase
                                text-[#3C6E91]
                            "
                        >
                            {{
                                str_replace(
                                    '_',
                                    ' ',
                                    $assignment->status
                                )
                            }}
                        </span>

                    </div>

                @empty

                    <div
                        class="
                            rounded-xl
                            border border-dashed
                            border-[#ddd2c1]
                            p-5
                            text-center
                            text-[8px]
                            text-[#918677]
                        "
                    >
                        No active last-mile batches.
                    </div>

                @endforelse

            </div>

        </article>

    </section>

</div>

@endsection