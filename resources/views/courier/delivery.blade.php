@extends('layouts.courier')

@section('title', 'Pickup Workflow')
@section('header-title', 'Pickup Workflow')
@section('header-subtitle', 'Dedicated Rider Job Workspace')

@section('content')

@php
    $isLogisticsPickup =
        (bool) $order->logisticsParcel;

    $status =
        (string) $order->status;

    $sellerName =
        $order->pickup_name
        ?: $order->seller?->store_name
        ?: 'Seller';

    $pickupAddress =
        $order->pickup_address
        ?: 'Pickup address not available';

    $destinationLabel =
        $isLogisticsPickup
            ? 'Assigned Origin Hub'
            : (
                $order->buyer_name
                ?: 'Buyer'
            );

    $destinationDetail =
        $isLogisticsPickup
            ? 'Hand the parcel to SARI Logistics for origin-hub intake.'
            : (
                $order->buyer_address
                ?: 'Buyer address not available'
            );

    $statusIndex = match ($status) {
        'courier_accepted' => 1,
        'heading_pickup' => 2,
        'arrived_pickup' => 3,
        'in_transit' => 4,
        'arrived_buyer' => 5,
        'delivered' => 6,
        default => 0,
    };

    $steps =
        $isLogisticsPickup
            ? [
                ['Assigned', 1],
                ['Heading to Seller', 2],
                ['At Seller', 3],
                ['To Origin Hub', 4],
            ]
            : [
                ['Assigned', 1],
                ['Heading to Seller', 2],
                ['At Seller', 3],
                ['In Transit', 4],
                ['At Buyer', 5],
                ['Delivered', 6],
            ];

    $workflowRecord = [
        [
            'Assigned',
            $order->accepted_at,
        ],
        [
            'Started Pickup',
            $order->pickup_started_at,
        ],
        [
            'Arrived at Seller',
            $order->arrived_pickup_at,
        ],
        [
            'Picked Up',
            $order->picked_up_at,
        ],
    ];

    if (! $isLogisticsPickup) {
        $workflowRecord[] = [
            'Arrived at Buyer',
            $order->arrived_buyer_at,
        ];

        $workflowRecord[] = [
            'Delivered',
            $order->delivered_at,
        ];
    }
@endphp

<div class="mx-auto max-w-[1380px] space-y-5">

    @if(session('success'))
        <div
            class="
                rounded-2xl
                border border-emerald-200
                bg-emerald-50
                px-5 py-4
                text-[10px]
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
                rounded-2xl
                border border-rose-200
                bg-rose-50
                px-5 py-4
                text-[10px]
                font-semibold
                text-rose-700
            "
        >
            {{ $errors->first() }}
        </div>
    @endif


    {{-- =====================================================
        WORKFLOW HEADER
    ====================================================== --}}

    <section
        class="
            overflow-hidden
            rounded-[22px]
            border border-[#eadfc9]
            bg-[#fffdf8]
        "
    >

        <div
            class="
                border-b border-[#eadfc9]
                px-5 py-4
                sm:px-6
            "
        >

            <div
                class="
                    flex flex-col
                    gap-4
                    lg:flex-row
                    lg:items-center
                    lg:justify-between
                "
            >

                <div class="min-w-0">

                    <div
                        class="
                            flex flex-wrap
                            items-center
                            gap-2
                        "
                    >

                        <a
                            href="{{ route('courier.requests') }}"
                            class="
                                inline-flex
                                items-center
                                gap-1
                                text-[8px]
                                font-semibold
                                text-[#8a7f70]
                                transition
                                hover:text-[#a96e05]
                            "
                        >
                            ← Pickup Assignments
                        </a>

                        <span class="text-[8px] text-[#c6b8a3]">
                            /
                        </span>

                        <span
                            class="
                                text-[8px]
                                font-semibold
                                text-[#3C6E91]
                            "
                        >
                            {{ $order->order_number }}
                        </span>

                    </div>


                    <div
                        class="
                            mt-3
                            flex flex-wrap
                            items-center
                            gap-2
                        "
                    >

                        <h2
                            class="
                                text-[22px]
                                font-bold
                                tracking-[-.035em]
                                text-[#211d17]
                            "
                        >
                            {{
                                $isLogisticsPickup
                                    ? 'Seller Pickup Workflow'
                                    : 'Direct Delivery Workflow'
                            }}
                        </h2>

                        <span
                            class="
                                rounded-full
                                border border-[#d2dfe9]
                                bg-[#f2f7fb]
                                px-3 py-1.5
                                text-[8px]
                                font-bold
                                uppercase
                                text-[#3C6E91]
                            "
                        >
                            {{ $order->statusLabel() }}
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

                        @if($isLogisticsPickup)

                            This page is dedicated to the Seller pickup leg.
                            Your responsibility ends after SARI Logistics
                            accepts the parcel at the origin hub.

                        @else

                            Complete each direct-delivery step here in order,
                            from Seller pickup to Buyer handoff.

                        @endif

                    </p>

                </div>


                <div class="flex flex-wrap gap-2">

                    <a
                        href="{{ route('courier.dashboard') }}"
                        class="
                            inline-flex
                            items-center
                            justify-center
                            rounded-xl
                            border border-[#e5dac9]
                            bg-white
                            px-4 py-2.5
                            text-[9px]
                            font-semibold
                            text-[#665c4f]
                        "
                    >
                        Dashboard
                    </a>

                    <a
                        href="{{ route('courier.requests') }}"
                        class="
                            inline-flex
                            items-center
                            justify-center
                            rounded-xl
                            bg-[#D99600]
                            px-4 py-2.5
                            text-[9px]
                            font-semibold
                            text-white
                        "
                    >
                        Pickup Queue
                    </a>

                </div>

            </div>

        </div>


        {{-- =====================================================
            PROGRESS
        ====================================================== --}}

        <div
            class="
                overflow-x-auto
                px-5 py-5
                sm:px-6
            "
        >

            <div
                class="grid min-w-[620px] gap-3"
                style="
                    grid-template-columns:
                    repeat(
                        {{ count($steps) }},
                        minmax(0, 1fr)
                    );
                "
            >

                @foreach($steps as [$label, $step])

                    @php
                        $done =
                            $statusIndex > $step;

                        $current =
                            $statusIndex === $step;
                    @endphp

                    <div
                        class="
                            rounded-[15px]
                            border
                            p-3
                            {{
                                $done
                                    ? 'border-emerald-200 bg-emerald-50'
                                    : (
                                        $current
                                            ? 'border-[#d7c18f] bg-[#fff7e7]'
                                            : 'border-[#e8e0d5] bg-white'
                                    )
                            }}
                        "
                    >

                        <div
                            class="
                                grid
                                h-8 w-8
                                place-items-center
                                rounded-full
                                text-[8px]
                                font-bold
                                {{
                                    $done
                                        ? 'bg-[#4F7D63] text-white'
                                        : (
                                            $current
                                                ? 'bg-[#D99600] text-white'
                                                : 'bg-[#eee8df] text-[#8d8274]'
                                        )
                                }}
                            "
                        >
                            {{ $done ? '✓' : $step }}
                        </div>


                        <p
                            class="
                                mt-2
                                text-[8px]
                                font-semibold
                                text-[#443d35]
                            "
                        >
                            {{ $label }}
                        </p>


                        <p
                            class="
                                mt-1
                                text-[7px]
                                text-[#918677]
                            "
                        >
                            {{
                                $done
                                    ? 'Completed'
                                    : (
                                        $current
                                            ? 'Current stage'
                                            : 'Upcoming'
                                    )
                            }}
                        </p>

                    </div>

                @endforeach

            </div>

        </div>

    </section>


    {{-- =====================================================
        MAIN WORKSPACE
    ====================================================== --}}

    <section
        class="
            grid
            items-start
            gap-5
            xl:grid-cols-[minmax(0,1.35fr)_minmax(330px,.65fr)]
        "
    >

        {{-- =================================================
            CURRENT ACTION
        ================================================== --}}

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
                    border-b border-[#eadfc9]
                    px-5 py-4
                "
            >

                <div
                    class="
                        flex flex-wrap
                        items-start
                        justify-between
                        gap-3
                    "
                >

                    <div>

                        <p
                            class="
                                text-[8px]
                                font-semibold
                                uppercase
                                tracking-[.12em]
                                text-[#a8731f]
                            "
                        >
                            Current Action
                        </p>


                        <h3
                            class="
                                mt-1
                                text-[14px]
                                font-bold
                                text-[#211d17]
                            "
                        >

                            @if($status === 'courier_accepted')

                                Start the Seller pickup

                            @elseif($status === 'heading_pickup')

                                Arrive at the Seller

                            @elseif($status === 'arrived_pickup')

                                Confirm parcel handoff

                            @elseif(
                                $status === 'in_transit'
                                && $isLogisticsPickup
                            )

                                Deliver parcel to origin hub

                            @elseif($status === 'in_transit')

                                Continue to Buyer

                            @elseif($status === 'arrived_buyer')

                                Complete Buyer handoff

                            @else

                                Review current job status

                            @endif

                        </h3>

                    </div>


                    <span
                        class="
                            rounded-full
                            border border-[#eadfc9]
                            bg-white
                            px-3 py-1.5
                            text-[8px]
                            font-semibold
                            text-[#756a5c]
                        "
                    >
                        {{
                            $isLogisticsPickup
                                ? 'Operational Pickup'
                                : 'Direct Delivery'
                        }}
                    </span>

                </div>

            </div>


            <div class="p-5 sm:p-6">

                {{-- ASSIGNED --}}

                @if($status === 'courier_accepted')

                    <div
                        class="
                            rounded-[17px]
                            border border-[#ead7a9]
                            bg-[#fff8e7]
                            p-5
                        "
                    >

                        <p
                            class="
                                text-[10px]
                                font-bold
                                text-[#775410]
                            "
                        >
                            Pickup assignment accepted
                        </p>


                        <p
                            class="
                                mt-2
                                text-[8px]
                                leading-5
                                text-[#8a6b2c]
                            "
                        >
                            Proceed to the Seller pickup location.
                            Use the button below only when you are ready
                            to begin traveling to the Seller.
                        </p>


                        <form
                            method="POST"
                            action="{{
                                route(
                                    'courier.orders.proceed-pickup',
                                    $order
                                )
                            }}"
                            class="mt-5"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="
                                    inline-flex
                                    w-full
                                    items-center
                                    justify-center
                                    rounded-xl
                                    bg-[#D99600]
                                    px-5 py-3.5
                                    text-[9px]
                                    font-semibold
                                    text-white
                                    shadow-[0_10px_22px_rgba(217,150,0,.16)]
                                "
                            >
                                Proceed to Seller Pickup →
                            </button>

                        </form>

                    </div>


                {{-- HEADING TO SELLER --}}

                @elseif($status === 'heading_pickup')

                    <div
                        class="
                            rounded-[17px]
                            border border-[#d5e2eb]
                            bg-[#f3f8fb]
                            p-5
                        "
                    >

                        <p
                            class="
                                text-[10px]
                                font-bold
                                text-[#315f7c]
                            "
                        >
                            Heading to the Seller
                        </p>


                        <p
                            class="
                                mt-2
                                text-[8px]
                                leading-5
                                text-[#617d8e]
                            "
                        >
                            Confirm arrival only after you physically
                            reach the Seller pickup point.
                        </p>


                        <form
                            method="POST"
                            action="{{
                                route(
                                    'courier.orders.arrived-pickup',
                                    $order
                                )
                            }}"
                            class="mt-5"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="
                                    inline-flex
                                    w-full
                                    items-center
                                    justify-center
                                    rounded-xl
                                    bg-[#3C6E91]
                                    px-5 py-3.5
                                    text-[9px]
                                    font-semibold
                                    text-white
                                "
                            >
                                I Arrived at the Seller ✓
                            </button>

                        </form>

                    </div>


                {{-- ARRIVED AT SELLER --}}

                @elseif($status === 'arrived_pickup')

                    <div
                        class="
                            rounded-[17px]
                            border border-[#d5e2eb]
                            bg-[#f3f8fb]
                            p-5
                        "
                    >

                        <p
                            class="
                                text-[10px]
                                font-bold
                                text-[#315f7c]
                            "
                        >
                            Verify the parcel before pickup
                        </p>


                        <p
                            class="
                                mt-2
                                text-[8px]
                                leading-5
                                text-[#617d8e]
                            "
                        >
                            Check the parcel and Seller handoff
                            before confirming that it is now in your custody.
                        </p>


                        <form
                            method="POST"
                            action="{{
                                route(
                                    'courier.orders.confirm-pickup',
                                    $order
                                )
                            }}"
                            class="mt-5"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="
                                    inline-flex
                                    w-full
                                    items-center
                                    justify-center
                                    rounded-xl
                                    bg-[#3C6E91]
                                    px-5 py-3.5
                                    text-[9px]
                                    font-semibold
                                    text-white
                                "
                            >
                                Confirm Item Pickup →
                            </button>

                        </form>

                    </div>


                {{-- LOGISTICS PICKUP COMPLETED --}}

                @elseif(
                    $status === 'in_transit'
                    && $isLogisticsPickup
                )

                    <div
                        class="
                            rounded-[17px]
                            border border-emerald-200
                            bg-emerald-50
                            p-5
                        "
                    >

                        <div
                            class="
                                flex
                                items-start
                                gap-3
                            "
                        >

                            <span
                                class="
                                    grid
                                    h-10 w-10
                                    shrink-0
                                    place-items-center
                                    rounded-xl
                                    bg-[#4F7D63]
                                    text-white
                                "
                            >

                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-[18px] w-[18px]"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path d="m6 12 4 4 8-8"></path>
                                </svg>

                            </span>


                            <div>

                                <p
                                    class="
                                        text-[10px]
                                        font-bold
                                        text-[#355f47]
                                    "
                                >
                                    Seller pickup completed
                                </p>


                                <p
                                    class="
                                        mt-2
                                        text-[8px]
                                        leading-5
                                        text-[#5b7967]
                                    "
                                >
                                    Bring this parcel to the assigned
                                    origin hub.

                                    There is no Buyer-delivery action
                                    on this page because the destination
                                    last-mile Rider owns final delivery.
                                </p>

                            </div>

                        </div>


                        <div
                            class="
                                mt-4
                                rounded-xl
                                border border-emerald-200
                                bg-white
                                px-4 py-3
                            "
                        >

                            <p
                                class="
                                    text-[7px]
                                    font-semibold
                                    uppercase
                                    tracking-[.1em]
                                    text-[#6d8d79]
                                "
                            >
                                Next handoff
                            </p>


                            <p
                                class="
                                    mt-1
                                    text-[9px]
                                    font-bold
                                    text-[#355f47]
                                "
                            >
                                SARI Logistics · Origin Hub Intake
                            </p>

                        </div>


                        <p
                            class="
                                mt-4
                                text-[7px]
                                leading-4
                                text-[#6e8174]
                            "
                        >
                            This pickup job remains active until
                            Logistics physically receives the parcel
                            at the origin hub.
                        </p>


                        <a
                            href="{{ route('courier.requests') }}"
                            class="
                                mt-4
                                inline-flex
                                w-full
                                items-center
                                justify-center
                                rounded-xl
                                border border-emerald-200
                                bg-white
                                px-5 py-3.5
                                text-[9px]
                                font-semibold
                                text-[#4F7D63]
                            "
                        >
                            Back to Pickup Assignments
                        </a>

                    </div>


                {{-- DIRECT DELIVERY IN TRANSIT --}}

                @elseif($status === 'in_transit')

                    <div
                        class="
                            rounded-[17px]
                            border border-[#d5e2eb]
                            bg-[#f3f8fb]
                            p-5
                        "
                    >

                        <p
                            class="
                                text-[10px]
                                font-bold
                                text-[#315f7c]
                            "
                        >
                            Direct delivery in transit
                        </p>


                        <p
                            class="
                                mt-2
                                text-[8px]
                                leading-5
                                text-[#617d8e]
                            "
                        >
                            Continue to the Buyer.
                            Confirm arrival only when you are
                            physically at the delivery location.
                        </p>


                        <form
                            method="POST"
                            action="{{
                                route(
                                    'courier.orders.arrived-buyer',
                                    $order
                                )
                            }}"
                            class="mt-5"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="
                                    inline-flex
                                    w-full
                                    items-center
                                    justify-center
                                    rounded-xl
                                    bg-[#3C6E91]
                                    px-5 py-3.5
                                    text-[9px]
                                    font-semibold
                                    text-white
                                "
                            >
                                Confirm Arrived at Buyer →
                            </button>

                        </form>

                    </div>


                {{-- DIRECT DELIVERY AT BUYER --}}

                @elseif(
                    $status === 'arrived_buyer'
                    && ! $isLogisticsPickup
                )

                    <div
                        class="
                            rounded-[17px]
                            border border-emerald-200
                            bg-emerald-50
                            p-5
                        "
                    >

                        <p
                            class="
                                text-[10px]
                                font-bold
                                text-[#355f47]
                            "
                        >
                            Buyer location reached
                        </p>


                        <p
                            class="
                                mt-2
                                text-[8px]
                                leading-5
                                text-[#5b7967]
                            "
                        >
                            Complete the direct-delivery handoff
                            when the parcel has been successfully
                            turned over to the Buyer.
                        </p>


                        <form
                            method="POST"
                            action="{{
                                route(
                                    'courier.orders.complete',
                                    $order
                                )
                            }}"
                            class="mt-5"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="
                                    inline-flex
                                    w-full
                                    items-center
                                    justify-center
                                    rounded-xl
                                    bg-[#4F7D63]
                                    px-5 py-3.5
                                    text-[9px]
                                    font-semibold
                                    text-white
                                "
                            >
                                Confirm Delivery Complete ✓
                            </button>

                        </form>

                    </div>


                {{-- FALLBACK --}}

                @else

                    <div
                        class="
                            rounded-[17px]
                            border border-[#eadfc9]
                            bg-white
                            p-5
                        "
                    >

                        <p
                            class="
                                text-[10px]
                                font-bold
                                text-[#51483d]
                            "
                        >
                            No action available for this state.
                        </p>


                        <p
                            class="
                                mt-2
                                text-[8px]
                                leading-5
                                text-[#918677]
                            "
                        >
                            Return to your assignment queue
                            or dashboard to review current work.
                        </p>

                    </div>

                @endif

            </div>

        </article>


        {{-- =================================================
            RIGHT SIDEBAR
        ================================================== --}}

        <aside class="space-y-4">

            {{-- JOB DETAILS --}}

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

                        <p
                            class="
                                text-[8px]
                                font-semibold
                                uppercase
                                tracking-[.12em]
                                text-[#9a8f80]
                            "
                        >
                            Job Details
                        </p>


                        <h3
                            class="
                                mt-1
                                text-[13px]
                                font-bold
                                text-[#211d17]
                            "
                        >
                            {{ $order->order_number }}
                        </h3>

                    </div>


                    <span
                        class="
                            rounded-full
                            bg-[#f3efe8]
                            px-3 py-1.5
                            text-[7px]
                            font-bold
                            uppercase
                            text-[#776b5e]
                        "
                    >
                        {{
                            $isLogisticsPickup
                                ? 'Pickup Leg'
                                : 'Direct'
                        }}
                    </span>

                </div>


                <dl class="mt-4 space-y-3">

                    <div
                        class="
                            rounded-xl
                            border border-[#eadfc9]
                            bg-white
                            p-3
                        "
                    >

                        <dt
                            class="
                                text-[7px]
                                font-semibold
                                uppercase
                                tracking-[.1em]
                                text-[#9a8f80]
                            "
                        >
                            Seller
                        </dt>

                        <dd
                            class="
                                mt-1
                                text-[9px]
                                font-bold
                                text-[#302a23]
                            "
                        >
                            {{ $sellerName }}
                        </dd>

                    </div>


                    <div
                        class="
                            rounded-xl
                            border border-[#eadfc9]
                            bg-white
                            p-3
                        "
                    >

                        <dt
                            class="
                                text-[7px]
                                font-semibold
                                uppercase
                                tracking-[.1em]
                                text-[#9a8f80]
                            "
                        >
                            Pickup Address
                        </dt>

                        <dd
                            class="
                                mt-1
                                text-[8px]
                                leading-4
                                text-[#62594f]
                            "
                        >
                            {{ $pickupAddress }}
                        </dd>

                    </div>


                    <div
                        class="
                            rounded-xl
                            border border-[#eadfc9]
                            bg-white
                            p-3
                        "
                    >

                        <dt
                            class="
                                text-[7px]
                                font-semibold
                                uppercase
                                tracking-[.1em]
                                text-[#9a8f80]
                            "
                        >
                            Next Destination
                        </dt>

                        <dd
                            class="
                                mt-1
                                text-[9px]
                                font-bold
                                text-[#302a23]
                            "
                        >
                            {{ $destinationLabel }}
                        </dd>

                        <dd
                            class="
                                mt-1
                                text-[8px]
                                leading-4
                                text-[#756b60]
                            "
                        >
                            {{ $destinationDetail }}
                        </dd>

                    </div>


                    @if(! $isLogisticsPickup)

                        <div
                            class="
                                rounded-xl
                                border border-[#eadfc9]
                                bg-white
                                p-3
                            "
                        >

                            <dt
                                class="
                                    text-[7px]
                                    font-semibold
                                    uppercase
                                    tracking-[.1em]
                                    text-[#9a8f80]
                                "
                            >
                                Delivery Fee
                            </dt>

                            <dd
                                class="
                                    mt-1
                                    text-[11px]
                                    font-bold
                                    text-[#302a23]
                                "
                            >
                                ₱{{
                                    number_format(
                                        $order->courierDeliveryFee(),
                                        2
                                    )
                                }}
                            </dd>

                        </div>

                    @endif

                </dl>

            </article>


            {{-- WORKFLOW RECORD --}}

            <article
                class="
                    rounded-[20px]
                    border border-[#eadfc9]
                    bg-white
                    p-5
                "
            >

                <p
                    class="
                        text-[8px]
                        font-semibold
                        uppercase
                        tracking-[.12em]
                        text-[#9a8f80]
                    "
                >
                    Workflow Record
                </p>


                <div class="mt-4 space-y-3">

                    @foreach($workflowRecord as [$label, $time])

                        <div
                            class="
                                flex
                                items-center
                                justify-between
                                gap-3
                            "
                        >

                            <div
                                class="
                                    flex
                                    items-center
                                    gap-2
                                "
                            >

                                <span
                                    class="
                                        h-2 w-2
                                        rounded-full
                                        {{
                                            $time
                                                ? 'bg-[#4F7D63]'
                                                : 'bg-[#ddd4c8]'
                                        }}
                                    "
                                ></span>

                                <span
                                    class="
                                        text-[8px]
                                        text-[#756b60]
                                    "
                                >
                                    {{ $label }}
                                </span>

                            </div>


                            <span
                                class="
                                    text-right
                                    text-[7px]
                                    font-semibold
                                    text-[#918677]
                                "
                            >
                                {{
                                    $time
                                        ? $time->format(
                                            'M d · h:i A'
                                        )
                                        : 'Pending'
                                }}
                            </span>

                        </div>

                    @endforeach

                </div>

            </article>


            {{-- LOGISTICS RULE --}}

            @if($isLogisticsPickup)

                <article
                    class="
                        rounded-[20px]
                        border border-[#d9e5dc]
                        bg-[#f5faf7]
                        p-5
                    "
                >

                    <p
                        class="
                            text-[8px]
                            font-semibold
                            uppercase
                            tracking-[.12em]
                            text-[#5d8169]
                        "
                    >
                        Logistics Pickup Rule
                    </p>


                    <p
                        class="
                            mt-2
                            text-[8px]
                            leading-5
                            text-[#617668]
                        "
                    >
                        This Rider performs the Seller pickup
                        and origin-hub handoff only.

                        Delivery-fee earnings belong to the
                        final last-mile Rider after successful
                        Buyer delivery.
                    </p>

                </article>

            @endif

        </aside>

    </section>

</div>

@endsection