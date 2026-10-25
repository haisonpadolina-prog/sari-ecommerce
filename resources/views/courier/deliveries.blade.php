@extends('layouts.courier')

@section('title', 'Active Deliveries')
@section('header-title', 'Active Deliveries')
@section('header-subtitle', 'Direct Delivery Operations')

@section('content')

<div class="mx-auto max-w-[1620px] space-y-4">

    {{-- =====================================================
        PAGE INTRO
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
                Direct Delivery
            </span>


            <h2
                class="
                    mt-3
                    text-[20px]
                    font-bold
                    text-[#211d17]
                "
            >
                Active Deliveries
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
                Active Seller-to-Buyer direct deliveries appear here.

                Logistics Seller pickup work is handled separately,
                while destination-hub assignments are managed through
                Last-Mile Deliveries.
            </p>

        </div>


        <div class="flex flex-wrap gap-2">

            <a
                href="{{ route('courier.requests') }}"
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
                Pickup Assignments
            </a>


            <a
                href="{{ route('courier.last-mile') }}"
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
                    shadow-[0_8px_20px_rgba(217,150,0,.14)]
                "
            >
                Last-Mile Deliveries →
            </a>

        </div>

    </section>


    {{-- =====================================================
        ACTIVE DIRECT DELIVERY LIST
    ====================================================== --}}

    <section
        class="
            grid
            gap-4
            lg:grid-cols-2
        "
    >

        @forelse($orders as $order)

            <article
                class="
                    overflow-hidden
                    rounded-[18px]
                    border border-[#d2dfe9]
                    bg-[#f6f9fb]
                "
            >

                {{-- HEADER --}}

                <div
                    class="
                        flex
                        items-start
                        justify-between
                        gap-3
                        border-b border-[#dfe8ee]
                        px-5 py-4
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

                            <p
                                class="
                                    text-[12px]
                                    font-bold
                                    text-[#26333c]
                                "
                            >
                                {{ $order->order_number }}
                            </p>


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
                                Direct Delivery
                            </span>

                        </div>


                        <p
                            class="
                                mt-2
                                text-[8px]
                                leading-4
                                text-[#72808c]
                            "
                        >
                            {{
                                $order->buyer_name
                                ?: 'Buyer'
                            }}
                        </p>

                    </div>


                    <span
                        class="
                            shrink-0
                            rounded-full
                            bg-white
                            px-2.5 py-1
                            text-[7px]
                            font-bold
                            uppercase
                            text-[#3C6E91]
                        "
                    >
                        {{ $order->statusLabel() }}
                    </span>

                </div>


                {{-- BODY --}}

                <div class="p-5">

                    <div
                        class="
                            grid
                            gap-3
                            sm:grid-cols-2
                        "
                    >

                        {{-- ROUTE --}}

                        <div
                            class="
                                rounded-xl
                                border border-[#dfe8ee]
                                bg-white
                                p-3
                            "
                        >

                            <p
                                class="
                                    text-[7px]
                                    font-semibold
                                    uppercase
                                    tracking-[.1em]
                                    text-[#899ba7]
                                "
                            >
                                Route
                            </p>


                            <p
                                class="
                                    mt-1
                                    text-[8px]
                                    font-semibold
                                    text-[#3d515d]
                                "
                            >
                                {{
                                    $order->pickup_name
                                    ?: 'Seller'
                                }}
                                →
                                {{
                                    $order->buyer_name
                                    ?: 'Buyer'
                                }}
                            </p>

                        </div>


                        {{-- DELIVERY FEE --}}

                        <div
                            class="
                                rounded-xl
                                border border-[#dfe8ee]
                                bg-white
                                p-3
                            "
                        >

                            <p
                                class="
                                    text-[7px]
                                    font-semibold
                                    uppercase
                                    tracking-[.1em]
                                    text-[#899ba7]
                                "
                            >
                                Delivery Fee
                            </p>


                            <p
                                class="
                                    mt-1
                                    text-[10px]
                                    font-bold
                                    text-[#26333c]
                                "
                            >
                                ₱{{
                                    number_format(
                                        $order->courierDeliveryFee(),
                                        2
                                    )
                                }}
                            </p>

                        </div>

                    </div>


                    {{-- BUYER ADDRESS --}}

                    <div
                        class="
                            mt-3
                            rounded-xl
                            border border-[#dfe8ee]
                            bg-white
                            p-3
                        "
                    >

                        <p
                            class="
                                text-[7px]
                                font-semibold
                                uppercase
                                tracking-[.1em]
                                text-[#899ba7]
                            "
                        >
                            Buyer Destination
                        </p>


                        <p
                            class="
                                mt-1
                                text-[8px]
                                leading-4
                                text-[#596c78]
                            "
                        >
                            {{
                                $order->buyer_address
                                ?: 'Delivery address unavailable'
                            }}
                        </p>

                    </div>


                    {{-- CURRENT STAGE --}}

                    <div
                        class="
                            mt-3
                            flex
                            items-center
                            justify-between
                            gap-3
                            rounded-xl
                            border border-[#dfe8ee]
                            bg-[#eef5f9]
                            px-4 py-3
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-[7px]
                                    font-semibold
                                    uppercase
                                    tracking-[.1em]
                                    text-[#78909f]
                                "
                            >
                                Current Stage
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-[9px]
                                    font-bold
                                    text-[#315f7c]
                                "
                            >
                                {{ $order->statusLabel() }}
                            </p>

                        </div>


                        <span
                            class="
                                grid
                                h-9 w-9
                                shrink-0
                                place-items-center
                                rounded-xl
                                bg-white
                                text-[#3C6E91]
                            "
                        >

                            <svg
                                viewBox="0 0 24 24"
                                class="h-[17px] w-[17px]"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M3 7h11v10H3z"></path>
                                <path d="M14 10h4l3 3v4h-7z"></path>
                                <circle cx="7" cy="18" r="2"></circle>
                                <circle cx="18" cy="18" r="2"></circle>
                            </svg>

                        </span>

                    </div>


                    {{-- ACTION --}}

                    <a
                        href="{{
                            route(
                                'courier.delivery.show',
                                [
                                    'order' => $order->id,
                                ]
                            )
                        }}"
                        class="
                            mt-4
                            inline-flex
                            w-full
                            items-center
                            justify-center
                            rounded-xl
                            bg-[#3C6E91]
                            px-4 py-3
                            text-[9px]
                            font-semibold
                            text-white
                            shadow-[0_8px_20px_rgba(60,110,145,.12)]
                        "
                    >
                        Continue Direct Delivery →
                    </a>

                </div>

            </article>


        @empty

            {{-- =================================================
                EMPTY STATE
            ================================================== --}}

            <div
                class="
                    lg:col-span-2
                    rounded-[20px]
                    border border-dashed
                    border-[#ddd2c1]
                    bg-[#fffdf8]
                    p-12
                    text-center
                "
            >

                <div
                    class="
                        mx-auto
                        grid
                        h-12 w-12
                        place-items-center
                        rounded-2xl
                        bg-[#eef5f9]
                        text-[#3C6E91]
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M3 7h11v10H3z"></path>
                        <path d="M14 10h4l3 3v4h-7z"></path>
                        <circle cx="7" cy="18" r="2"></circle>
                        <circle cx="18" cy="18" r="2"></circle>
                    </svg>

                </div>


                <p
                    class="
                        mt-4
                        text-[10px]
                        font-semibold
                        text-[#51483d]
                    "
                >
                    No active direct delivery.
                </p>


                <p
                    class="
                        mx-auto
                        mt-2
                        max-w-[520px]
                        text-[8px]
                        leading-4
                        text-[#918677]
                    "
                >
                    Seller pickup jobs are available under
                    Pickup Assignments.

                    Logistics destination-hub deliveries are
                    handled separately under Last-Mile Deliveries.
                </p>


                <div
                    class="
                        mt-5
                        flex flex-wrap
                        justify-center
                        gap-2
                    "
                >

                    <a
                        href="{{ route('courier.requests') }}"
                        class="
                            rounded-xl
                            border border-[#eadfc9]
                            bg-white
                            px-4 py-2.5
                            text-[8px]
                            font-semibold
                            text-[#655b4e]
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
                            text-[8px]
                            font-semibold
                            text-white
                        "
                    >
                        Last-Mile Deliveries
                    </a>

                </div>

            </div>

        @endforelse

    </section>

</div>

@endsection