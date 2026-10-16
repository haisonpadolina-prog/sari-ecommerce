@extends('layouts.courier')

@section('title', 'Last-Mile Deliveries')
@section('header-title', 'Last-Mile Deliveries')
@section('header-subtitle', 'Destination Hub → Buyer')

@section('content')

@php
    $activeAssignments =
        $assignments
            ->whereIn(
                'status',
                [
                    'assigned',
                    'out_for_delivery',
                ]
            );

    $completedAssignments =
        $assignments
            ->where(
                'status',
                'completed'
            );

    $activeParcelCount =
        $activeAssignments
            ->sum(
                fn ($assignment) =>
                    $assignment->orders->count()
            );
@endphp


<div class="mx-auto max-w-[1620px] space-y-4">

    @if(session('success'))

        <div
            class="
                rounded-2xl
                border border-emerald-200
                bg-emerald-50
                px-5 py-4
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
                rounded-2xl
                border border-rose-200
                bg-rose-50
                px-5 py-4
                text-[9px]
                font-semibold
                text-rose-700
            "
        >
            {{ $errors->first() }}
        </div>

    @endif


    {{-- =====================================================
        INTRO
    ====================================================== --}}

    <section
        class="
            flex flex-col
            justify-between
            gap-5
            rounded-[22px]
            border border-[#eadfc9]
            bg-[#fffdf8]
            p-5
            sm:p-6
            lg:flex-row
            lg:items-end
        "
    >

        <div>

            <span
                class="
                    inline-flex
                    rounded-full
                    border border-[#eadfc9]
                    bg-white
                    px-3 py-1.5
                    text-[8px]
                    font-semibold
                    uppercase
                    tracking-[.13em]
                    text-[#8a7f70]
                "
            >
                Last-Mile Operations
            </span>


            <h2
                class="
                    mt-3
                    text-[20px]
                    font-bold
                    text-[#211d17]
                "
            >
                Destination Hub Deliveries
            </h2>


            <p
                class="
                    mt-2
                    max-w-[720px]
                    text-[9px]
                    leading-5
                    text-[#817769]
                "
            >
                Collect assigned parcels from the destination
                hub and complete final delivery inside your
                assigned barangay.
            </p>

        </div>


        <a
            href="{{ route('courier.dashboard') }}"
            class="
                rounded-xl
                border border-[#e5dac9]
                bg-white
                px-4 py-2.5
                text-[9px]
                font-semibold
                text-[#665c4f]
                transition
                hover:border-[#d7bd8b]
                hover:text-[#a96e05]
            "
        >
            Back to Dashboard
        </a>

    </section>


    {{-- =====================================================
        SUMMARY
    ====================================================== --}}

    <section
        class="
            grid
            gap-3
            sm:grid-cols-3
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
                    uppercase
                    tracking-[.11em]
                    text-[#9a8f80]
                "
            >
                Active Batches
            </p>


            <p
                class="
                    mt-2
                    text-[20px]
                    font-bold
                    text-[#211d17]
                "
            >
                {{ $activeAssignments->count() }}
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
                    uppercase
                    tracking-[.11em]
                    text-[#9a8f80]
                "
            >
                Active Parcels
            </p>


            <p
                class="
                    mt-2
                    text-[20px]
                    font-bold
                    text-[#211d17]
                "
            >
                {{ $activeParcelCount }}
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
                    uppercase
                    tracking-[.11em]
                    text-[#9a8f80]
                "
            >
                Completed Batches
            </p>


            <p
                class="
                    mt-2
                    text-[20px]
                    font-bold
                    text-[#211d17]
                "
            >
                {{ $completedAssignments->count() }}
            </p>

        </article>

    </section>


    {{-- =====================================================
        WORKFLOW
    ====================================================== --}}

    <section
        class="
            rounded-[18px]
            border border-[#eadfc9]
            bg-white
            px-4 py-3
        "
    >

        <div
            class="
                grid
                gap-2
                sm:grid-cols-3
            "
        >

            <div
                class="
                    flex items-center
                    gap-3
                    rounded-xl
                    bg-[#fff8ea]
                    px-4 py-3
                "
            >

                <span
                    class="
                        grid h-7 w-7
                        place-items-center
                        rounded-full
                        bg-[#D99600]
                        text-[8px]
                        font-bold
                        text-white
                    "
                >
                    1
                </span>


                <div>

                    <p
                        class="
                            text-[8px]
                            font-bold
                            text-[#51483d]
                        "
                    >
                        Destination Hub
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[7px]
                            text-[#918677]
                        "
                    >
                        Collect assigned batch
                    </p>

                </div>

            </div>


            <div
                class="
                    flex items-center
                    gap-3
                    rounded-xl
                    bg-[#eef5f9]
                    px-4 py-3
                "
            >

                <span
                    class="
                        grid h-7 w-7
                        place-items-center
                        rounded-full
                        bg-[#3C6E91]
                        text-[8px]
                        font-bold
                        text-white
                    "
                >
                    2
                </span>


                <div>

                    <p
                        class="
                            text-[8px]
                            font-bold
                            text-[#51483d]
                        "
                    >
                        Local Delivery
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[7px]
                            text-[#918677]
                        "
                    >
                        Deliver within barangay
                    </p>

                </div>

            </div>


            <div
                class="
                    flex items-center
                    gap-3
                    rounded-xl
                    bg-emerald-50
                    px-4 py-3
                "
            >

                <span
                    class="
                        grid h-7 w-7
                        place-items-center
                        rounded-full
                        bg-emerald-600
                        text-[8px]
                        font-bold
                        text-white
                    "
                >
                    3
                </span>


                <div>

                    <p
                        class="
                            text-[8px]
                            font-bold
                            text-[#51483d]
                        "
                    >
                        Buyer Handoff
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[7px]
                            text-[#918677]
                        "
                    >
                        Confirm final delivery
                    </p>

                </div>

            </div>

        </div>

    </section>


    {{-- =====================================================
        ASSIGNMENTS
    ====================================================== --}}

    @forelse($assignments as $assignment)

        @php
            $isAssigned =
                $assignment->status
                === 'assigned';

            $isOut =
                $assignment->status
                === 'out_for_delivery';

            $isCompleted =
                $assignment->status
                === 'completed';
        @endphp


        <article
            class="
                overflow-hidden
                rounded-[22px]
                border border-[#eadfc9]
                bg-[#fffdf8]
            "
        >

            {{-- Header --}}
            <div
                class="
                    flex flex-col
                    justify-between
                    gap-4
                    border-b border-[#eadfc9]
                    p-5
                    sm:flex-row
                    sm:items-start
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
                            $assignment
                                ->assignment_code
                        }}
                    </p>


                    <h3
                        class="
                            mt-2
                            text-[15px]
                            font-bold
                            text-[#211d17]
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
                    </h3>


                    <p
                        class="
                            mt-1
                            text-[8px]
                            text-[#817769]
                        "
                    >
                        {{
                            $assignment
                                ->orders
                                ->count()
                        }}
                        {{
                            $assignment
                                ->orders
                                ->count()
                            === 1
                                ? 'parcel'
                                : 'parcels'
                        }}
                    </p>

                </div>


                <span
                    class="
                        rounded-full
                        border
                        px-3 py-1.5
                        text-[7px]
                        font-bold
                        uppercase

                        {{
                            $isCompleted
                                ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                : (
                                    $isOut
                                        ? 'border-blue-200 bg-blue-50 text-[#3C6E91]'
                                        : 'border-amber-200 bg-[#fff2d8] text-[#a56b00]'
                                )
                        }}
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


            {{-- Assignment details --}}
            <div
                class="
                    grid
                    gap-3
                    border-b border-[#eadfc9]
                    p-5
                    md:grid-cols-2
                "
            >

                <div
                    class="
                        rounded-xl
                        border border-[#eee5d6]
                        bg-white
                        p-4
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
                            mt-2
                            text-[9px]
                            font-semibold
                            text-[#29241e]
                        "
                    >
                        {{
                            $assignment
                                ->facility_name
                        }}
                    </p>


                    <p
                        class="
                            mt-1
                            text-[7px]
                            leading-4
                            text-[#918677]
                        "
                    >
                        {{
                            $assignment
                                ->facility_address
                            ?: 'Hub address unavailable'
                        }}
                    </p>

                </div>


                <div
                    class="
                        rounded-xl
                        border border-[#eee5d6]
                        bg-white
                        p-4
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
                        Delivery Area
                    </p>


                    <p
                        class="
                            mt-2
                            text-[9px]
                            font-semibold
                            text-[#29241e]
                        "
                    >
                        {{
                            $assignment
                                ->delivery_area
                        }}
                    </p>


                    <p
                        class="
                            mt-1
                            text-[7px]
                            leading-4
                            text-[#918677]
                        "
                    >
                        Assigned barangay for this batch.
                    </p>

                </div>

            </div>


            {{-- Start --}}
            @if($isAssigned)

                <div class="p-5">

                    <div
                        class="
                            rounded-xl
                            border border-amber-200
                            bg-amber-50
                            px-4 py-3
                        "
                    >

                        <p
                            class="
                                text-[8px]
                                font-semibold
                                text-[#8b6200]
                            "
                        >
                            This batch is ready for destination-hub pickup.
                        </p>


                        <p
                            class="
                                mt-1
                                text-[7px]
                                leading-4
                                text-[#987a37]
                            "
                        >
                            Start the route only after you physically receive
                            the assigned parcels from the destination hub.
                        </p>

                    </div>


                    <form
                        method="POST"
                        action="{{
                            route(
                                'courier.last-mile.start',
                                $assignment->id
                            )
                        }}"
                        class="mt-4"
                    >
                        @csrf

                        <button
                            class="
                                h-11
                                w-full
                                rounded-xl
                                bg-[#D99600]
                                px-6
                                text-[9px]
                                font-bold
                                text-white
                            "
                        >
                            Start Last-Mile Route →
                        </button>

                    </form>

                </div>

            @endif


            {{-- Parcels --}}
            <div class="space-y-3 p-5">

                @foreach(
                    $assignment->orders
                    as $order
                )

                    @php
                        $parcelStatus =
                            $order->parcel_status
                            ?? $order->order_status;

                        $isDelivered =
                            $parcelStatus
                            === 'delivered';
                    @endphp


                    <div
                        class="
                            rounded-[17px]
                            border border-[#eee5d6]
                            bg-white
                            p-4
                        "
                    >

                        <div
                            class="
                                flex flex-col
                                justify-between
                                gap-3
                                sm:flex-row
                                sm:items-start
                            "
                        >

                            <div>

                                <p
                                    class="
                                        text-[10px]
                                        font-bold
                                        text-[#29241e]
                                    "
                                >
                                    {{ $order->order_number }}
                                </p>


                                <p
                                    class="
                                        mt-1
                                        text-[9px]
                                        font-semibold
                                        text-[#51483d]
                                    "
                                >
                                    {{ $order->buyer_name }}
                                </p>


                                <p
                                    class="
                                        mt-1
                                        text-[8px]
                                        leading-4
                                        text-[#817769]
                                    "
                                >
                                    {{ $order->buyer_address }}
                                </p>


                                @if($order->buyer_phone)

                                    <p
                                        class="
                                            mt-1
                                            text-[8px]
                                            text-[#817769]
                                        "
                                    >
                                        {{ $order->buyer_phone }}
                                    </p>

                                @endif

                            </div>


                            <span
                                class="
                                    rounded-full
                                    border
                                    px-3 py-1.5
                                    text-[7px]
                                    font-bold
                                    uppercase

                                    {{
                                        $isDelivered
                                            ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                            : (
                                                $parcelStatus
                                                === 'arrived_buyer'
                                                    ? 'border-blue-200 bg-blue-50 text-[#3C6E91]'
                                                    : 'border-[#eee5d6] bg-[#f7f3eb] text-[#6f6559]'
                                            )
                                    }}
                                "
                            >
                                {{
                                    str_replace(
                                        '_',
                                        ' ',
                                        $parcelStatus
                                    )
                                }}
                            </span>

                        </div>


                        @if(
                            $isOut
                            &&
                            $order->parcel_status
                                === 'out_for_delivery'
                        )

                            <div
                                class="
                                    mt-4
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
                                    Next step: reach the Buyer location.
                                </p>

                            </div>


                            <form
                                method="POST"
                                action="{{
                                    route(
                                        'courier.last-mile.arrived',
                                        [
                                            'assignment' =>
                                                $assignment->id,

                                            'order' =>
                                                $order->id,
                                        ]
                                    )
                                }}"
                                class="mt-3"
                            >
                                @csrf

                                <button
                                    class="
                                        h-10
                                        w-full
                                        rounded-xl
                                        bg-[#3C6E91]
                                        px-5
                                        text-[8px]
                                        font-bold
                                        text-white
                                    "
                                >
                                    Confirm Arrived at Buyer →
                                </button>

                            </form>


                        @elseif(
                            $isOut
                            &&
                            $order->parcel_status
                                === 'arrived_buyer'
                        )

                            <div
                                class="
                                    mt-4
                                    rounded-xl
                                    border border-emerald-100
                                    bg-emerald-50
                                    px-4 py-3
                                "
                            >

                                <p
                                    class="
                                        text-[8px]
                                        font-semibold
                                        text-emerald-700
                                    "
                                >
                                    Buyer location confirmed.
                                    Complete the final handoff.
                                </p>

                            </div>


                            <form
                                method="POST"
                                action="{{
                                    route(
                                        'courier.last-mile.delivered',
                                        [
                                            'assignment' =>
                                                $assignment->id,

                                            'order' =>
                                                $order->id,
                                        ]
                                    )
                                }}"
                                class="mt-3"
                            >
                                @csrf

                                <button
                                    class="
                                        h-10
                                        w-full
                                        rounded-xl
                                        bg-emerald-600
                                        px-5
                                        text-[8px]
                                        font-bold
                                        text-white
                                    "
                                >
                                    Confirm Delivery Complete ✓
                                </button>

                            </form>


                        @elseif($isDelivered)

                            <div
                                class="
                                    mt-4
                                    rounded-xl
                                    border border-emerald-100
                                    bg-emerald-50
                                    px-4 py-3
                                    text-center
                                "
                            >

                                <p
                                    class="
                                        text-[8px]
                                        font-bold
                                        text-emerald-700
                                    "
                                >
                                    Delivered Successfully
                                </p>

                            </div>

                        @endif

                    </div>

                @endforeach

            </div>

        </article>

    @empty

        <section
            class="
                rounded-[22px]
                border border-dashed
                border-[#dcd2c3]
                bg-[#fffdf8]
                p-12
                text-center
            "
        >

            <p
                class="
                    text-[10px]
                    font-semibold
                    text-[#51483d]
                "
            >
                No Last-Mile assignments.
            </p>


            <p
                class="
                    mt-2
                    text-[8px]
                    text-[#918677]
                "
            >
                Destination-hub batches assigned by
                SARI Logistics will appear here.
            </p>

        </section>

    @endforelse

</div>

@endsection