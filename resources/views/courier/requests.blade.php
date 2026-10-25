@extends('layouts.courier')

@section('title', 'Pickup Assignments')
@section('header-title', 'Pickup Assignments')
@section('header-subtitle', 'Dispatched by SARI Logistics')

@section('content')

<div class="mx-auto max-w-[1620px] space-y-4">

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


    {{-- =====================================================
        INTRO
    ====================================================== --}}

    <section
        class="
            flex flex-col
            justify-between
            gap-4
            rounded-[20px]
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
                Seller Pickup Dispatch
            </span>


            <h2
                class="
                    mt-3
                    text-[20px]
                    font-bold
                    text-[#211d17]
                "
            >
                Pickup Assignments
            </h2>


            <p
                class="
                    mt-2
                    max-w-[700px]
                    text-[9px]
                    leading-5
                    text-[#817769]
                "
            >
                Orders assigned to you for collection from
                Sellers appear here. Logistics-managed parcels
                are handed over to the origin hub after pickup.
            </p>

        </div>


        <a
            href="{{ route('courier.last-mile') }}"
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
                transition
                hover:border-[#d7bd8b]
                hover:text-[#a96e05]
            "
        >
            Go to Last-Mile Deliveries →
        </a>

    </section>


    {{-- =====================================================
        ACTIVE PICKUP
    ====================================================== --}}

    @if($activeOrder)

        @php
            $activeIsLogistics =
                (bool) $activeOrder->logisticsParcel;
        @endphp

        <section
            class="
                overflow-hidden
                rounded-[18px]
                border border-[#d2dfe9]
                bg-[#f5f9fc]
            "
        >

            <div
                class="
                    flex flex-col
                    justify-between
                    gap-4
                    p-5
                    sm:flex-row
                    sm:items-center
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

                        <span
                            class="
                                rounded-full
                                bg-[#e7f0f6]
                                px-2.5 py-1
                                text-[7px]
                                font-bold
                                uppercase
                                text-[#3C6E91]
                            "
                        >
                            Active
                        </span>


                        <span
                            class="
                                rounded-full
                                border border-[#d8e3ea]
                                bg-white
                                px-2.5 py-1
                                text-[7px]
                                font-semibold
                                uppercase
                                text-[#657986]
                            "
                        >
                            {{
                                $activeIsLogistics
                                    ? 'Seller Pickup'
                                    : 'Direct Delivery'
                            }}
                        </span>

                    </div>


                    <h3
                        class="
                            mt-3
                            text-[13px]
                            font-bold
                            text-[#25333c]
                        "
                    >
                        {{ $activeOrder->order_number }}
                    </h3>


                    <p
                        class="
                            mt-1
                            text-[8px]
                            text-[#6f8190]
                        "
                    >
                        {{ $activeOrder->statusLabel() }}
                    </p>


                    @if(
                        $activeIsLogistics
                        &&
                        $activeOrder->status
                            === 'in_transit'
                    )

                        <p
                            class="
                                mt-2
                                max-w-[650px]
                                text-[8px]
                                leading-4
                                text-[#607987]
                            "
                        >
                            Pickup completed. Bring the parcel
                            to the assigned origin hub for
                            physical intake by SARI Logistics.
                        </p>

                    @endif

                </div>


                <a
                    href="{{
                        route(
                            'courier.delivery.show',
                            [
                                'order' =>
                                    $activeOrder->id,
                            ]
                        )
                    }}"
                    class="
                        inline-flex
                        items-center
                        justify-center
                        rounded-xl
                        bg-[#3C6E91]
                        px-5 py-3
                        text-[9px]
                        font-semibold
                        text-white
                    "
                >
                    Continue Pickup Workflow →
                </a>

            </div>

        </section>

    @endif


    {{-- =====================================================
        ASSIGNED QUEUE
    ====================================================== --}}

    <section
        class="
            overflow-hidden
            rounded-[20px]
            border border-[#eadfc9]
            bg-[#fffdf8]
        "
    >

        <div
            class="
                flex flex-col
                justify-between
                gap-3
                border-b border-[#eadfc9]
                px-5 py-4
                sm:flex-row
                sm:items-center
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
                    Assigned Pickup Queue
                </h3>

                <p
                    class="
                        mt-1
                        text-[8px]
                        text-[#918677]
                    "
                >
                    Start with the Seller pickup assigned
                    by SARI Logistics.
                </p>

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
                {{ $orders->count() }}
                {{
                    $orders->count() === 1
                        ? 'assignment'
                        : 'assignments'
                }}
            </span>

        </div>


        <div
            class="
                grid
                gap-4
                p-5
                lg:grid-cols-2
            "
        >

            @forelse($orders as $order)

                @php
                    $isLogisticsPickup =
                        (bool) $order
                            ->logisticsParcel;
                @endphp

                <article
                    class="
                        rounded-[17px]
                        border border-[#eadfc9]
                        bg-white
                        p-5
                    "
                >

                    <div
                        class="
                            flex
                            items-start
                            justify-between
                            gap-4
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-[11px]
                                    font-bold
                                    text-[#29241e]
                                "
                            >
                                {{ $order->order_number }}
                            </p>


                            <p
                                class="
                                    mt-1
                                    text-[8px]
                                    text-[#918677]
                                "
                            >
                                {{
                                    $order
                                        ->seller
                                        ?->store_name
                                    ?: $order
                                        ->pickup_name
                                    ?: 'SARI Seller'
                                }}
                            </p>

                        </div>


                        <span
                            class="
                                rounded-full
                                border border-[#eadfc9]
                                bg-[#fff6e1]
                                px-2.5 py-1
                                text-[7px]
                                font-bold
                                text-[#a56b00]
                            "
                        >
                            PICKUP ASSIGNED
                        </span>

                    </div>


                    <div
                        class="
                            mt-4
                            grid
                            gap-3
                            sm:grid-cols-2
                        "
                    >

                        <div
                            class="
                                rounded-xl
                                border border-[#eee5d6]
                                bg-[#fffdf9]
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
                                Seller Pickup
                            </p>


                            <p
                                class="
                                    mt-1
                                    text-[9px]
                                    font-semibold
                                    text-[#2a251f]
                                "
                            >
                                {{
                                    $order->pickup_name
                                    ?: 'Seller'
                                }}
                            </p>


                            <p
                                class="
                                    mt-1
                                    text-[7px]
                                    leading-4
                                    text-[#8a8073]
                                "
                            >
                                {{
                                    $order->pickup_address
                                    ?: 'Pickup address unavailable'
                                }}
                            </p>

                        </div>


                        <div
                            class="
                                rounded-xl
                                border border-[#eee5d6]
                                bg-[#fffdf9]
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
                                Next Handoff
                            </p>


                            <p
                                class="
                                    mt-1
                                    text-[9px]
                                    font-semibold
                                    text-[#2a251f]
                                "
                            >
                                {{
                                    $isLogisticsPickup
                                        ? 'Origin Sorting Hub'
                                        : (
                                            $order
                                                ->buyer_name
                                            ?: 'Buyer'
                                        )
                                }}
                            </p>


                            <p
                                class="
                                    mt-1
                                    text-[7px]
                                    leading-4
                                    text-[#8a8073]
                                "
                            >
                                {{
                                    $isLogisticsPickup
                                        ? 'SARI Logistics intake'
                                        : (
                                            $order
                                                ->buyer_address
                                            ?: 'Buyer delivery'
                                        )
                                }}
                            </p>

                        </div>

                    </div>


                    @if($isLogisticsPickup)

                        <div
                            class="
                                mt-4
                                rounded-xl
                                border border-[#e1e8ed]
                                bg-[#f5f9fc]
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
                                Pickup-only assignment
                            </p>


                            <p
                                class="
                                    mt-1
                                    text-[7px]
                                    leading-4
                                    text-[#708693]
                                "
                            >
                                The Marketplace delivery fee
                                belongs to the final-mile Rider,
                                not the Seller pickup Rider.
                            </p>

                        </div>

                    @endif


                    <div
                        class="
                            mt-4
                            flex
                            justify-end
                            border-t border-[#eee5d6]
                            pt-4
                        "
                    >

                        <a
                            href="{{
                                route(
                                    'courier.delivery.show',
                                    [
                                        'order' =>
                                            $order->id,
                                    ]
                                )
                            }}"
                            class="
                                rounded-xl
                                bg-[#D99600]
                                px-5 py-3
                                text-[9px]
                                font-semibold
                                text-white
                            "
                        >
                            Start Pickup Workflow →
                        </a>

                    </div>

                </article>

            @empty

                <div
                    class="
                        lg:col-span-2
                        rounded-[18px]
                        border border-dashed
                        border-[#ddd2c1]
                        bg-white
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
                        No Seller pickup assignments waiting.
                    </p>


                    <p
                        class="
                            mt-2
                            text-[8px]
                            text-[#918677]
                        "
                    >
                        New Seller pickup jobs dispatched by
                        SARI Logistics will appear here.
                    </p>


                    <a
                        href="{{ route('courier.last-mile') }}"
                        class="
                            mt-4
                            inline-flex
                            rounded-xl
                            border border-[#eadfc9]
                            bg-[#fffdf8]
                            px-5 py-3
                            text-[9px]
                            font-semibold
                            text-[#685e51]
                        "
                    >
                        Check Last-Mile Deliveries
                    </a>

                </div>

            @endforelse

        </div>

    </section>

</div>


@push('scripts')

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        let currentCount =
            {{ $orders->count() }};


        async function checkAssignments() {

            if (document.hidden) {
                return;
            }


            try {

                const response =
                    await fetch(
                        @json(
                            route(
                                'courier.orders.live-state'
                            )
                        ),
                        {
                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },

                            credentials:
                                'same-origin',

                            cache:
                                'no-store',
                        }
                    );


                if (! response.ok) {
                    return;
                }


                const data =
                    await response.json();


                if (
                    Number(
                        data.assigned_count
                    )
                    !==
                    Number(
                        currentCount
                    )
                ) {
                    window.location.reload();
                }

            } catch (error) {

                console.debug(
                    'Rider assignment polling temporarily unavailable.'
                );

            }

        }


        window.setInterval(
            checkAssignments,
            3000
        );

    }
);
</script>

@endpush

@endsection