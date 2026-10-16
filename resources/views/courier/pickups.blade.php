@extends('layouts.courier')

@section('title', 'Seller Pickups')
@section('header-title', 'Seller Pickups')
@section('header-subtitle', 'Seller Collection Workflow')

@section('content')

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
                Pickup Operations
            </span>


            <h2
                class="
                    mt-3
                    text-[20px]
                    font-bold
                    text-[#211d17]
                "
            >
                Seller Pickups
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
                Track jobs that are still at the Seller
                collection stage. Logistics pickup work ends
                after the parcel is physically accepted by
                the origin hub.
            </p>

        </div>


        <a
            href="{{ route('courier.requests') }}"
            class="
                rounded-xl
                bg-[#D99600]
                px-4 py-2.5
                text-[9px]
                font-semibold
                text-white
            "
        >
            View Pickup Assignments →
        </a>

    </section>


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
                        text-[12px]
                        font-bold
                        text-[#211d17]
                    "
                >
                    Pickup Stage
                </h3>

                <p
                    class="
                        mt-1
                        text-[8px]
                        text-[#918677]
                    "
                >
                    Assigned, heading to Seller, or waiting
                    for Seller handoff confirmation.
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
                            gap-3
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
                                    $order->pickup_name
                                    ?: 'SARI Seller'
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
                                text-[#a56b00]
                            "
                        >
                            {{
                                strtoupper(
                                    $order->statusLabel()
                                )
                            }}
                        </span>

                    </div>


                    <div
                        class="
                            mt-4
                            rounded-xl
                            border border-[#eee5d6]
                            bg-[#fffdf9]
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
                            Pickup Address
                        </p>


                        <p
                            class="
                                mt-2
                                text-[8px]
                                leading-4
                                text-[#817769]
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
                            mt-3
                            flex
                            items-center
                            justify-between
                            gap-3
                            rounded-xl
                            border border-[#eee5d6]
                            bg-white
                            px-4 py-3
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-[7px]
                                    uppercase
                                    tracking-[.1em]
                                    text-[#a09484]
                                "
                            >
                                After Pickup
                            </p>


                            <p
                                class="
                                    mt-1
                                    text-[8px]
                                    font-semibold
                                    text-[#51483d]
                                "
                            >
                                {{
                                    $isLogisticsPickup
                                        ? 'Origin Hub Intake'
                                        : 'Direct Buyer Delivery'
                                }}
                            </p>

                        </div>


                        <span
                            class="
                                rounded-full
                                px-2.5 py-1
                                text-[7px]
                                font-bold

                                {{
                                    $isLogisticsPickup
                                        ? 'bg-[#eaf2f7] text-[#3C6E91]'
                                        : 'bg-[#eaf3ed] text-[#4F7D63]'
                                }}
                            "
                        >
                            {{
                                $isLogisticsPickup
                                    ? 'LOGISTICS'
                                    : 'DIRECT'
                            }}
                        </span>

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
                            bg-[#D99600]
                            px-4 py-3
                            text-[9px]
                            font-semibold
                            text-white
                        "
                    >
                        Open Pickup Workflow →
                    </a>

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
                        No Seller pickup work in progress.
                    </p>


                    <p
                        class="
                            mt-2
                            text-[8px]
                            text-[#918677]
                        "
                    >
                        Check Pickup Assignments for newly
                        dispatched Seller collections.
                    </p>

                </div>

            @endforelse

        </div>

    </section>

</div>

@endsection