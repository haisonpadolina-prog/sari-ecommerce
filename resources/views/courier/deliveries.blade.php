@extends('layouts.courier')

@section('title', 'Active Deliveries')
@section('header-title', 'Active Deliveries')
@section('header-subtitle', 'Delivery Operations')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Temporary protection for the legacy route closure
    |--------------------------------------------------------------------------
    |
    | /courier/deliveries still retrieves rows from courier_email.
    |
    | For Logistics-managed orders that field is intentionally the historical
    | Seller-pickup Rider snapshot, so they MUST NOT appear as direct active
    | deliveries here.
    |
    | Last-Mile work has its own page and assignment table.
    */

    $directOrders =
        $orders
            ->filter(
                fn ($order) =>
                    ! $order->logisticsParcel
            )
            ->values();
@endphp


<div class="mx-auto max-w-[1620px] space-y-4">

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
                Rider Operations
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
                Direct Seller-to-Buyer deliveries appear here.
                Logistics destination-hub work is handled from
                the dedicated Last-Mile Deliveries page.
            </p>

        </div>


        <a
            href="{{ route('courier.last-mile') }}"
            class="
                rounded-xl
                bg-[#D99600]
                px-4 py-2.5
                text-[9px]
                font-semibold
                text-white
            "
        >
            Open Last-Mile Deliveries →
        </a>

    </section>


    <section
        class="
            grid
            gap-4
            lg:grid-cols-2
        "
    >

        @forelse($directOrders as $order)

            <article
                class="
                    rounded-[18px]
                    border border-[#d2dfe9]
                    bg-[#f6f9fb]
                    p-5
                "
            >

                <div
                    class="
                        flex
                        items-start
                        justify-between
                        gap-3
                    "
                >

                    <div>

                        <p
                            class="
                                text-[11px]
                                font-bold
                                text-[#26333c]
                            "
                        >
                            {{ $order->order_number }}
                        </p>


                        <p
                            class="
                                mt-1
                                text-[8px]
                                leading-4
                                text-[#72808c]
                            "
                        >
                            {{
                                $order->buyer_name
                                ?: 'Buyer'
                            }}
                            ·
                            {{
                                $order->buyer_address
                                ?: 'Delivery address unavailable'
                            }}
                        </p>

                    </div>


                    <span
                        class="
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
                            border border-[#dfe8ee]
                            bg-white
                            p-3
                        "
                    >

                        <p
                            class="
                                text-[7px]
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
                                    $order
                                        ->courierDeliveryFee(),
                                    2
                                )
                            }}
                        </p>

                    </div>

                </div>


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
                    "
                >
                    Continue Direct Delivery →
                </a>

            </article>

        @empty

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

                <p
                    class="
                        text-[10px]
                        font-semibold
                        text-[#51483d]
                    "
                >
                    No active direct delivery.
                </p>


                <p
                    class="
                        mt-2
                        text-[8px]
                        text-[#918677]
                    "
                >
                    Seller pickup jobs are tracked separately,
                    while destination-hub deliveries appear
                    under Last-Mile Deliveries.
                </p>


                <div
                    class="
                        mt-4
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