@extends('layouts.courier')

@section('title', 'Rider Dashboard')
@section('header-title', 'Rider Dashboard')
@section('header-subtitle', 'Logistics Assigned Deliveries')

@push('styles')
<style>
    .dashboard-card {
        border: 1px solid #eee4d3;
        background: #fffdf9;
        box-shadow: 0 8px 24px rgba(75, 59, 30, .035);
    }

    .dashboard-hover {
        transition:
            transform .22s ease,
            border-color .22s ease,
            box-shadow .22s ease;
    }

    .dashboard-hover:hover {
        transform: translateY(-2px);
        border-color: #dbc89f;
        box-shadow: 0 14px 32px rgba(75, 59, 30, .07);
    }

    .dashboard-grid {
        background-image:
            linear-gradient(
                to right,
                rgba(225, 213, 192, .14) 1px,
                transparent 1px
            ),
            linear-gradient(
                to bottom,
                rgba(225, 213, 192, .14) 1px,
                transparent 1px
            );

        background-size: 28px 28px;
    }
</style>
@endpush


@section('content')
<div class="mx-auto max-w-[1620px]">

    {{-- =========================================================
         SUCCESS MESSAGE
    ========================================================== --}}
    @if (session('success'))

        <div
            class="
                mb-5 flex items-start gap-3
                rounded-2xl
                border border-emerald-200
                bg-emerald-50
                px-4 py-3.5
                text-emerald-700
            "
        >
            <div
                class="
                    mt-0.5
                    grid h-6 w-6
                    shrink-0 place-items-center
                    rounded-full
                    bg-emerald-600
                    text-white
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-3.5 w-3.5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m5 12 4 4L19 6"></path>
                </svg>
            </div>

            <p class="text-[10px] font-semibold leading-5">
                {{ session('success') }}
            </p>
        </div>

    @endif


    {{-- =========================================================
         ERROR MESSAGE
    ========================================================== --}}
    @if ($errors->any())

        <div
            class="
                mb-5 flex items-start gap-3
                rounded-2xl
                border border-rose-200
                bg-rose-50
                px-4 py-3.5
                text-rose-700
            "
        >
            <div
                class="
                    mt-0.5
                    grid h-6 w-6
                    shrink-0 place-items-center
                    rounded-full
                    bg-rose-600
                    text-white
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-3.5 w-3.5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 8v5"></path>
                    <path d="M12 16.5h.01"></path>
                </svg>
            </div>

            <p class="text-[10px] font-semibold leading-5">
                {{ $errors->first() }}
            </p>
        </div>

    @endif


    {{-- =========================================================
         PAGE INTRO
    ========================================================== --}}
    <section
        class="
            reveal
            flex flex-col justify-between gap-5
            border-b border-[#eee4d3]
            pb-5
            lg:flex-row lg:items-end
        "
    >

        <div class="max-w-2xl">

            <div class="flex flex-wrap items-center gap-2.5">

                <span
                    class="
                        inline-flex items-center gap-2
                        rounded-full
                        border border-[#eadfc9]
                        bg-[#fffaf1]
                        px-3 py-1.5
                        text-[9px] font-bold uppercase
                        tracking-[.1em]
                        text-[#a66d08]
                    "
                >
                    <span
                        class="
                            h-1.5 w-1.5
                            rounded-full
                            bg-[#d9930a]
                        "
                    ></span>

                    Rider Operations
                </span>


                @if (
                    $currentDelivery
                    instanceof \App\Models\MarketplaceOrder
                )

                    <span
                        class="
                            inline-flex items-center gap-2
                            rounded-full
                            border border-[#f0d49a]
                            bg-[#fff4d8]
                            px-3 py-1.5
                            text-[9px] font-semibold
                            text-[#a56b00]
                        "
                    >
                        <span
                            class="
                                h-1.5 w-1.5
                                rounded-full
                                bg-[#d9930a]
                            "
                        ></span>

                        Active assignment
                    </span>

                @else

                    <span
                        class="
                            inline-flex items-center gap-2
                            rounded-full
                            border border-[#e4ded4]
                            bg-white
                            px-3 py-1.5
                            text-[9px] font-semibold
                            text-[#817769]
                        "
                    >
                        <span
                            class="
                                h-1.5 w-1.5
                                rounded-full
                                bg-[#b8afa1]
                            "
                        ></span>

                        Waiting for assignment
                    </span>

                @endif

            </div>


            <h2
                class="
                    mt-3
                    text-[20px] font-bold
                    tracking-[-.025em]
                    text-[#211d17]
                    sm:text-[22px]
                "
            >
                Your delivery day at a glance
            </h2>


            <p
                class="
                    mt-1.5
                    max-w-xl
                    text-[10px] leading-5
                    text-[#817769]
                    sm:text-[11px]
                "
            >
                Review your assigned orders, continue active deliveries,
                and monitor today's rider activity.
            </p>

        </div>


        <a
            href="{{ route('courier.requests') }}"
            class="
                inline-flex min-h-10
                items-center justify-center gap-2
                self-start
                rounded-xl
                bg-[#d9930a]
                px-4 py-2.5
                text-[10px] font-semibold
                text-white
                shadow-[0_8px_18px_rgba(217,147,10,.15)]
                transition
                hover:bg-[#c98505]
                active:scale-[.98]
                lg:self-auto
            "
        >
            View My Assignments

            <svg
                viewBox="0 0 24 24"
                class="h-3.5 w-3.5"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="M5 12h14"></path>
                <path d="m14 7 5 5-5 5"></path>
            </svg>
        </a>

    </section>


    {{-- =========================================================
         KPI CARDS
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-2 gap-3
            xl:grid-cols-4
        "
    >

        {{-- WAITING --}}
        <article
            class="
                reveal dashboard-card dashboard-hover
                relative overflow-hidden
                rounded-2xl
                p-4
            "
        >
            <div
                class="
                    absolute inset-x-0 top-0
                    h-[3px]
                    bg-[#d9930a]
                "
            ></div>


            <div
                class="
                    grid h-10 w-10
                    place-items-center
                    rounded-xl
                    bg-[#fff2d8]
                    text-[#b77900]
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-[18px] w-[18px]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 7v5l3 2"></path>
                </svg>
            </div>


            <p
                class="
                    mt-3
                    text-[9px] font-medium
                    text-[#817769]
                "
            >
                Waiting to Start
            </p>


            <p
                class="
                    mt-1
                    text-[22px] font-bold
                    tracking-[-.04em]
                    text-[#211d17]
                "
            >
                {{ $stats['assigned_waiting'] }}
            </p>


            <p
                class="
                    mt-1
                    text-[9px]
                    text-[#a09587]
                "
            >
                Assigned deliveries
            </p>
        </article>


        {{-- ACTIVE --}}
        <article
            class="
                reveal dashboard-card dashboard-hover
                relative overflow-hidden
                rounded-2xl
                p-4
            "
        >
            <div
                class="
                    absolute inset-x-0 top-0
                    h-[3px]
                    bg-[#c58c1c]
                "
            ></div>


            <div
                class="
                    grid h-10 w-10
                    place-items-center
                    rounded-xl
                    bg-[#fff4df]
                    text-[#ad7810]
                "
            >
                <svg
                    viewBox="0 0 28 24"
                    class="h-[18px] w-[20px]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="7" cy="18" r="3"></circle>
                    <circle cx="21" cy="18" r="3"></circle>
                    <path d="M7 18h6l4-8h4"></path>
                    <path d="M13 18 9 9h5"></path>
                    <path d="m17 10 4 8"></path>
                </svg>
            </div>


            <p
                class="
                    mt-3
                    text-[9px] font-medium
                    text-[#817769]
                "
            >
                Active Deliveries
            </p>


            <p
                class="
                    mt-1
                    text-[22px] font-bold
                    tracking-[-.04em]
                    text-[#211d17]
                "
            >
                {{ $stats['active_deliveries'] }}
            </p>


            <p
                class="
                    mt-1
                    text-[9px]
                    text-[#a09587]
                "
            >
                Currently in progress
            </p>
        </article>


        {{-- COMPLETED --}}
        <article
            class="
                reveal dashboard-card dashboard-hover
                relative overflow-hidden
                rounded-2xl
                p-4
            "
        >
            <div
                class="
                    absolute inset-x-0 top-0
                    h-[3px]
                    bg-[#4F7D63]
                "
            ></div>


            <div
                class="
                    grid h-10 w-10
                    place-items-center
                    rounded-xl
                    bg-[#eef5f0]
                    text-[#4F7D63]
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-[18px] w-[18px]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="m7.5 12 3 3 6-7"></path>
                </svg>
            </div>


            <p
                class="
                    mt-3
                    text-[9px] font-medium
                    text-[#817769]
                "
            >
                Completed Today
            </p>


            <p
                class="
                    mt-1
                    text-[22px] font-bold
                    tracking-[-.04em]
                    text-[#211d17]
                "
            >
                {{ $stats['completed_today'] }}
            </p>


            <p
                class="
                    mt-1
                    text-[9px]
                    text-[#a09587]
                "
            >
                Successful deliveries
            </p>
        </article>


        {{-- EARNINGS --}}
        <article
            class="
                reveal dashboard-card dashboard-hover
                relative overflow-hidden
                rounded-2xl
                p-4
            "
        >
            <div
                class="
                    absolute inset-x-0 top-0
                    h-[3px]
                    bg-[#b79a67]
                "
            ></div>


            <div
                class="
                    grid h-10 w-10
                    place-items-center
                    rounded-xl
                    bg-[#f4eee5]
                    text-[#8a7553]
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-[18px] w-[18px]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M12 3v18"></path>
                    <path d="M16.5 7c0-1.5-2-2.7-4.5-2.7S7.5 5.5 7.5 7s2 2.7 4.5 2.7 4.5 1.2 4.5 2.7-2 2.7-4.5 2.7-4.5-1.2-4.5-2.7"></path>
                </svg>
            </div>


            <p
                class="
                    mt-3
                    text-[9px] font-medium
                    text-[#817769]
                "
            >
                Today's Earnings
            </p>


            <p
                class="
                    mt-1
                    text-[22px] font-bold
                    tracking-[-.04em]
                    text-[#211d17]
                "
            >
                ₱{{ number_format(
                    $stats['earnings_today'],
                    2
                ) }}
            </p>


            <p
                class="
                    mt-1
                    text-[9px]
                    text-[#a09587]
                "
            >
                Credited delivery fees
            </p>
        </article>

    </section>


    {{-- =========================================================
         MAIN DASHBOARD AREA
    ========================================================== --}}
    <section
        class="
            mt-5 grid gap-5
            xl:grid-cols-[minmax(0,1.55fr)_minmax(320px,.45fr)]
        "
    >

        {{-- =====================================================
             CURRENT DELIVERY
        ====================================================== --}}
        <article
            class="
                reveal dashboard-card
                overflow-hidden
                rounded-[20px]
            "
        >

            <div
                class="
                    flex items-center justify-between gap-4
                    border-b border-[#eee4d3]
                    px-5 py-4
                "
            >

                <div class="flex items-center gap-3">

                    <div
                        class="
                            grid h-9 w-9
                            shrink-0 place-items-center
                            rounded-xl
                            bg-[#fff2d8]
                            text-[#b77900]
                        "
                    >
                        <svg
                            viewBox="0 0 28 24"
                            class="h-[17px] w-[19px]"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="7" cy="18" r="3"></circle>
                            <circle cx="21" cy="18" r="3"></circle>
                            <path d="M7 18h6l4-8h4"></path>
                            <path d="M13 18 9 9h5"></path>
                        </svg>
                    </div>


                    <div>

                        <h3
                            class="
                                text-[13px] font-bold
                                text-[#211d17]
                            "
                        >
                            Current Delivery
                        </h3>


                        <p
                            class="
                                mt-1
                                text-[9px]
                                text-[#918677]
                            "
                        >
                            Your current rider workflow and next action.
                        </p>

                    </div>

                </div>


                @if (
                    $currentDelivery
                    instanceof \App\Models\MarketplaceOrder
                )

                    <span
                        class="
                            hidden
                            items-center gap-2
                            rounded-full
                            border border-[#f0d49a]
                            bg-[#fff4d8]
                            px-3 py-1.5
                            text-[8px] font-semibold
                            text-[#a56b00]
                            sm:inline-flex
                        "
                    >
                        <span
                            class="
                                h-1.5 w-1.5
                                rounded-full
                                bg-[#d9930a]
                            "
                        ></span>

                        Active
                    </span>

                @endif

            </div>


            @if (
                $currentDelivery
                instanceof \App\Models\MarketplaceOrder
            )

                @php
                    $status = $currentDelivery->status;

                    $pickupDone = in_array(
                        $status,
                        [
                            'in_transit',
                            'arrived_buyer',
                            'delivered'
                        ],
                        true
                    );

                    $transitDone = in_array(
                        $status,
                        [
                            'arrived_buyer',
                            'delivered'
                        ],
                        true
                    );

                    $deliveredDone =
                        $status === 'delivered';
                @endphp


                <div class="p-5">

                    {{-- =============================================
                         ORDER SUMMARY
                    ============================================== --}}
                    <div
                        class="
                            flex flex-col gap-4
                            sm:flex-row
                            sm:items-start
                            sm:justify-between
                        "
                    >

                        <div>

                            <div
                                class="
                                    flex flex-wrap
                                    items-center gap-2
                                "
                            >
                                <h4
                                    class="
                                        text-[14px] font-bold
                                        text-[#211d17]
                                    "
                                >
                                    {{ $currentDelivery->order_number }}
                                </h4>


                                <span
                                    class="
                                        rounded-full
                                        border
                                        px-2.5 py-1
                                        text-[8px] font-bold uppercase
                                        tracking-[.05em]

                                        {{ $status === 'delivered'
                                            ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                            : 'border-[#f0d49a] bg-[#fff4d8] text-[#a56b00]'
                                        }}
                                    "
                                >
                                    {{ strtoupper(
                                        $currentDelivery->statusLabel()
                                    ) }}
                                </span>
                            </div>


                            <div
                                class="
                                    mt-3
                                    flex flex-col gap-2
                                    text-[10px]
                                    text-[#817769]
                                    sm:flex-row
                                    sm:items-center
                                "
                            >
                                <span>
                                    {{ $currentDelivery->pickup_name }}
                                </span>

                                <svg
                                    viewBox="0 0 24 24"
                                    class="
                                        hidden h-3.5 w-3.5
                                        text-[#b6ab9c]
                                        sm:block
                                    "
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M5 12h14"></path>
                                    <path d="m14 7 5 5-5 5"></path>
                                </svg>

                                <span>
                                    {{ $currentDelivery->buyer_name }}
                                </span>
                            </div>

                        </div>


                        <div
                            class="
                                rounded-xl
                                border border-[#eadfc9]
                                bg-[#fffaf1]
                                px-4 py-3
                            "
                        >
                            <p
                                class="
                                    text-[9px]
                                    text-[#918677]
                                "
                            >
                                Delivery Fee
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-[14px] font-bold
                                    text-[#211d17]
                                "
                            >
                                ₱{{ number_format(
                                    (float)
                                    $currentDelivery->delivery_fee,
                                    2
                                ) }}
                            </p>
                        </div>

                    </div>


                    {{-- =============================================
                         DELIVERY PROGRESS
                    ============================================== --}}
                    <div
                        class="
                            mt-5
                            rounded-2xl
                            border border-[#eee5d6]
                            bg-[#fbf8f2]
                            p-4
                        "
                    >

                        <div
                            class="
                                mb-4
                                flex items-center
                                justify-between gap-3
                            "
                        >
                            <div>

                                <p
                                    class="
                                        text-[10px] font-semibold
                                        text-[#51483d]
                                    "
                                >
                                    Delivery Progress
                                </p>

                                <p
                                    class="
                                        mt-1
                                        text-[9px]
                                        text-[#918677]
                                    "
                                >
                                    Follow each delivery stage in order.
                                </p>

                            </div>

                        </div>


                        <div class="grid grid-cols-3 gap-2 sm:gap-3">

                            {{-- PICKUP --}}
                            <div
                                class="
                                    rounded-xl border
                                    p-3 text-center

                                    {{ $pickupDone
                                        ? 'border-emerald-200 bg-emerald-50'
                                        : 'border-[#f0d49a] bg-[#fff8e8]'
                                    }}
                                "
                            >

                                <div
                                    class="
                                        mx-auto
                                        grid h-8 w-8
                                        place-items-center
                                        rounded-full
                                        text-[10px] font-bold
                                        text-white

                                        {{ $pickupDone
                                            ? 'bg-[#4F7D63]'
                                            : 'bg-[#d9930a]'
                                        }}
                                    "
                                >
                                    {{ $pickupDone ? '✓' : '1' }}
                                </div>


                                <p
                                    class="
                                        mt-2
                                        text-[9px] font-semibold
                                        text-[#413a31]
                                    "
                                >
                                    Picked Up
                                </p>

                            </div>


                            {{-- TRANSIT --}}
                            <div
                                class="
                                    rounded-xl border
                                    p-3 text-center

                                    {{ $transitDone
                                        ? 'border-emerald-200 bg-emerald-50'
                                        : (
                                            $status === 'in_transit'
                                                ? 'border-[#f0d49a] bg-[#fff8e8]'
                                                : 'border-[#e8dfd2] bg-white'
                                        )
                                    }}
                                "
                            >

                                <div
                                    class="
                                        mx-auto
                                        grid h-8 w-8
                                        place-items-center
                                        rounded-full
                                        text-[10px] font-bold
                                        text-white

                                        {{ $transitDone
                                            ? 'bg-[#4F7D63]'
                                            : (
                                                $status === 'in_transit'
                                                    ? 'bg-[#d9930a]'
                                                    : 'bg-[#d8cfc1]'
                                            )
                                        }}
                                    "
                                >
                                    {{ $transitDone ? '✓' : '2' }}
                                </div>


                                <p
                                    class="
                                        mt-2
                                        text-[9px] font-semibold
                                        text-[#413a31]
                                    "
                                >
                                    In Transit
                                </p>

                            </div>


                            {{-- DELIVERED --}}
                            <div
                                class="
                                    rounded-xl border
                                    p-3 text-center

                                    {{ $deliveredDone
                                        ? 'border-emerald-200 bg-emerald-50'
                                        : 'border-[#e8dfd2] bg-white'
                                    }}
                                "
                            >

                                <div
                                    class="
                                        mx-auto
                                        grid h-8 w-8
                                        place-items-center
                                        rounded-full
                                        text-[10px] font-bold
                                        text-white

                                        {{ $deliveredDone
                                            ? 'bg-[#4F7D63]'
                                            : 'bg-[#d8cfc1]'
                                        }}
                                    "
                                >
                                    {{ $deliveredDone ? '✓' : '3' }}
                                </div>


                                <p
                                    class="
                                        mt-2
                                        text-[9px] font-semibold
                                        text-[#413a31]
                                    "
                                >
                                    Delivered
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- =============================================
                         NEXT ACTION
                    ============================================== --}}
                    <div
                        class="
                            mt-4
                            rounded-2xl
                            border border-[#eee5d6]
                            bg-white
                            p-4
                        "
                    >

                        <p
                            class="
                                text-[9px] font-bold uppercase
                                tracking-[.09em]
                                text-[#9a9082]
                            "
                        >
                            Next Rider Action
                        </p>


                        @if ($status === 'courier_accepted')

                            <p
                                class="
                                    mt-2
                                    text-[10px] font-semibold
                                    text-[#6f5520]
                                "
                            >
                                SARI Logistics assigned this delivery to you.
                            </p>


                            <form
                                method="POST"
                                action="{{ route(
                                    'courier.orders.proceed-pickup',
                                    $currentDelivery
                                ) }}"
                                class="mt-3"
                            >
                                @csrf

                                <button
                                    class="
                                        inline-flex w-full
                                        min-h-11
                                        items-center justify-center gap-2
                                        rounded-xl
                                        bg-[#d9930a]
                                        px-4 py-3
                                        text-[10px] font-semibold
                                        text-white
                                        transition
                                        hover:bg-[#c98505]
                                    "
                                >
                                    Proceed to Pickup

                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="M5 12h14"></path>
                                        <path d="m14 7 5 5-5 5"></path>
                                    </svg>
                                </button>
                            </form>


                        @elseif ($status === 'heading_pickup')

                            <p
                                class="
                                    mt-2
                                    text-[10px] font-semibold
                                    text-[#6f5520]
                                "
                            >
                                You are heading to the Seller pickup point.
                            </p>


                            <form
                                method="POST"
                                action="{{ route(
                                    'courier.orders.arrived-pickup',
                                    $currentDelivery
                                ) }}"
                                class="mt-3"
                            >
                                @csrf

                                <button
                                    class="
                                        inline-flex w-full
                                        min-h-11
                                        items-center justify-center gap-2
                                        rounded-xl
                                        bg-[#d9930a]
                                        px-4 py-3
                                        text-[10px] font-semibold
                                        text-white
                                        transition
                                        hover:bg-[#c98505]
                                    "
                                >
                                    I Arrived at Pickup

                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="m5 12 4 4L19 6"></path>
                                    </svg>
                                </button>
                            </form>


                        @elseif ($status === 'arrived_pickup')

                            <p
                                class="
                                    mt-2
                                    text-[10px] font-semibold
                                    text-[#6f5520]
                                "
                            >
                                Check the package, then confirm the Seller handoff.
                            </p>


                            <form
                                method="POST"
                                action="{{ route(
                                    'courier.orders.confirm-pickup',
                                    $currentDelivery
                                ) }}"
                                class="mt-3"
                            >
                                @csrf

                                <button
                                    class="
                                        inline-flex w-full
                                        min-h-11
                                        items-center justify-center gap-2
                                        rounded-xl
                                        bg-[#d9930a]
                                        px-4 py-3
                                        text-[10px] font-semibold
                                        text-white
                                        transition
                                        hover:bg-[#c98505]
                                    "
                                >
                                    Confirm Item Pickup

                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="M5 12h14"></path>
                                        <path d="m14 7 5 5-5 5"></path>
                                    </svg>
                                </button>
                            </form>


                        @elseif ($status === 'in_transit')

                            <p
                                class="
                                    mt-2
                                    text-[10px] font-semibold
                                    text-[#6f5520]
                                "
                            >
                                Package is in transit. Confirm only when you
                                reach the Buyer.
                            </p>


                            <form
                                method="POST"
                                action="{{ route(
                                    'courier.orders.arrived-buyer',
                                    $currentDelivery
                                ) }}"
                                class="mt-3"
                            >
                                @csrf

                                <button
                                    class="
                                        inline-flex w-full
                                        min-h-11
                                        items-center justify-center gap-2
                                        rounded-xl
                                        bg-[#d9930a]
                                        px-4 py-3
                                        text-[10px] font-semibold
                                        text-white
                                        transition
                                        hover:bg-[#c98505]
                                    "
                                >
                                    Confirm Arrived at Buyer

                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="M5 12h14"></path>
                                        <path d="m14 7 5 5-5 5"></path>
                                    </svg>
                                </button>
                            </form>


                        @elseif ($status === 'arrived_buyer')

                            <p
                                class="
                                    mt-2
                                    text-[10px] font-semibold
                                    text-[#4F7D63]
                                "
                            >
                                Buyer location reached. Complete the handoff.
                            </p>


                            <form
                                method="POST"
                                action="{{ route(
                                    'courier.orders.complete',
                                    $currentDelivery
                                ) }}"
                                class="mt-3"
                            >
                                @csrf

                                <button
                                    class="
                                        inline-flex w-full
                                        min-h-11
                                        items-center justify-center gap-2
                                        rounded-xl
                                        bg-[#4F7D63]
                                        px-4 py-3
                                        text-[10px] font-semibold
                                        text-white
                                        transition
                                        hover:bg-[#456f58]
                                    "
                                >
                                    Confirm Delivery Complete

                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="m5 12 4 4L19 6"></path>
                                    </svg>
                                </button>
                            </form>

                        @endif

                    </div>

                </div>


            @else

                {{-- =================================================
                     EMPTY CURRENT DELIVERY
                ================================================== --}}
                <div
                    class="
                        dashboard-grid
                        flex min-h-[280px]
                        items-center justify-center
                        px-6 py-8
                    "
                >

                    <div
                        class="
                            mx-auto
                            max-w-[420px]
                            text-center
                        "
                    >

                        <div
                            class="
                                relative
                                mx-auto
                                grid h-16 w-16
                                place-items-center
                                rounded-[20px]
                                border border-[#eadfc9]
                                bg-[#fffdf9]
                                text-[#b47a11]
                                shadow-[0_10px_28px_rgba(75,59,30,.06)]
                            "
                        >
                            <svg
                                viewBox="0 0 28 24"
                                class="h-7 w-8"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                            >
                                <circle cx="7" cy="18" r="3"></circle>
                                <circle cx="21" cy="18" r="3"></circle>
                                <path d="M7 18h6l4-8h4"></path>
                                <path d="M13 18 9 9h5"></path>
                            </svg>


                            <span
                                class="
                                    absolute
                                    -right-1 -top-1
                                    h-4 w-4
                                    rounded-full
                                    border-[3px] border-[#fffdf9]
                                    bg-[#b8afa1]
                                "
                            ></span>
                        </div>


                        <h4
                            class="
                                mt-4
                                text-[13px] font-bold
                                text-[#413a31]
                            "
                        >
                            No active assignment
                        </h4>


                        <p
                            class="
                                mt-2
                                text-[10px] leading-5
                                text-[#918677]
                            "
                        >
                            SARI Logistics will dispatch an eligible
                            delivery to your rider account.
                        </p>


                        <a
                            href="{{ route('courier.requests') }}"
                            class="
                                mt-5
                                inline-flex min-h-10
                                items-center justify-center gap-2
                                rounded-xl
                                bg-[#d9930a]
                                px-4 py-2.5
                                text-[10px] font-semibold
                                text-white
                                transition
                                hover:bg-[#c98505]
                            "
                        >
                            Check Assignments

                            <svg
                                viewBox="0 0 24 24"
                                class="h-3.5 w-3.5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M5 12h14"></path>
                                <path d="m14 7 5 5-5 5"></path>
                            </svg>
                        </a>

                    </div>

                </div>

            @endif

        </article>


        {{-- =====================================================
             LATEST ASSIGNMENTS
        ====================================================== --}}
        <aside
            class="
                reveal dashboard-card
                overflow-hidden
                rounded-[20px]
            "
        >

            <div
                class="
                    flex items-center justify-between gap-3
                    border-b border-[#eee4d3]
                    px-5 py-4
                "
            >

                <div>

                    <h3
                        class="
                            text-[13px] font-bold
                            text-[#211d17]
                        "
                    >
                        Latest Assignments
                    </h3>

                    <p
                        class="
                            mt-1
                            text-[9px]
                            text-[#918677]
                        "
                    >
                        Recently dispatched orders.
                    </p>

                </div>


                <a
                    href="{{ route('courier.requests') }}"
                    class="
                        text-[9px] font-semibold
                        text-[#a66d08]
                        transition
                        hover:text-[#c98505]
                    "
                >
                    View all
                </a>

            </div>


            <div class="p-4">

                <div class="space-y-2.5">

                    @forelse ($assignments as $order)

                        <div
                            class="
                                rounded-xl
                                border border-[#eee4d3]
                                bg-white
                                p-3.5
                                transition
                                hover:border-[#dbc89f]
                                hover:bg-[#fffaf1]
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
                                            text-[10px] font-bold
                                            text-[#2a251f]
                                        "
                                    >
                                        {{ $order->order_number }}
                                    </p>


                                    <p
                                        class="
                                            mt-1.5
                                            line-clamp-2
                                            text-[9px] leading-4
                                            text-[#84796c]
                                        "
                                    >
                                        {{ $order->pickup_name }}
                                        →
                                        {{ $order->buyer_name }}
                                    </p>

                                </div>


                                <span
                                    class="
                                        shrink-0
                                        rounded-full
                                        border border-[#f0d49a]
                                        bg-[#fff4d8]
                                        px-2.5 py-1
                                        text-[8px] font-bold
                                        text-[#aa6d00]
                                    "
                                >
                                    ASSIGNED
                                </span>

                            </div>

                        </div>


                    @empty

                        <div
                            class="
                                dashboard-grid
                                rounded-2xl
                                border border-dashed
                                border-[#ddd2c1]
                                px-5 py-8
                                text-center
                            "
                        >

                            <div
                                class="
                                    mx-auto
                                    grid h-11 w-11
                                    place-items-center
                                    rounded-xl
                                    border border-[#eadfc9]
                                    bg-[#fffdf9]
                                    text-[#b47a11]
                                "
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path d="M7 4h10"></path>
                                    <path d="M9 2h6v4H9z"></path>
                                    <path d="M6 4H5a2 2 0 0 0-2 2v14h18V6a2 2 0 0 0-2-2h-1"></path>
                                </svg>
                            </div>


                            <p
                                class="
                                    mt-3
                                    text-[10px] font-semibold
                                    text-[#51483d]
                                "
                            >
                                No assigned jobs yet
                            </p>


                            <p
                                class="
                                    mt-1
                                    text-[9px] leading-4
                                    text-[#918677]
                                "
                            >
                                New Logistics assignments will appear here.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>

        </aside>

    </section>

</div>
@endsection