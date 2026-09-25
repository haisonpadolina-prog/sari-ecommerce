@extends('layouts.courier')

@section('title', 'My Assignments')
@section('header-title', 'My Assignments')
@section('header-subtitle', 'Logistics Assigned Deliveries')

@push('styles')
<style>
    .assignment-card {
        border: 1px solid #eee4d3;
        background: #fffdf9;
        box-shadow: 0 8px 24px rgba(75, 59, 30, .035);
    }

    .assignment-hover {
        transition:
            transform .22s ease,
            border-color .22s ease,
            box-shadow .22s ease;
    }

    .assignment-hover:hover {
        transform: translateY(-2px);
        border-color: #dbc89f;
        box-shadow: 0 14px 32px rgba(75, 59, 30, .07);
    }

    .assignment-grid {
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

                    Logistics Dispatch
                </span>


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
                    <svg
                        viewBox="0 0 24 24"
                        class="h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M7 4h10"></path>
                        <path d="M9 2h6v4H9z"></path>
                        <path d="M6 4H5a2 2 0 0 0-2 2v14h18V6a2 2 0 0 0-2-2h-1"></path>
                    </svg>

                    {{ $orders->count() }}
                    {{ $orders->count() === 1 ? 'assignment' : 'assignments' }}
                </span>

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
                Assigned delivery jobs
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
                Review deliveries dispatched to your rider account
                and continue each assigned order from the delivery workflow.
            </p>

        </div>


        <a
            href="{{ route('courier.dashboard') }}"
            class="
                inline-flex min-h-10
                items-center justify-center gap-2
                self-start
                rounded-xl
                border border-[#e6dccb]
                bg-white
                px-4 py-2.5
                text-[10px] font-semibold
                text-[#51483d]
                shadow-sm
                transition
                hover:border-[#d9be8c]
                hover:bg-[#fffaf1]
                hover:text-[#a66d08]
                lg:self-auto
            "
        >
            <svg
                viewBox="0 0 24 24"
                class="h-3.5 w-3.5"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="M3 12h18"></path>
                <path d="M8 7l-5 5 5 5"></path>
            </svg>

            Back to Dashboard
        </a>

    </section>


    {{-- =========================================================
         CURRENT ASSIGNMENT
    ========================================================== --}}
    @if ($activeOrder)

        <section
            class="
                reveal assignment-card
                mt-5 overflow-hidden
                rounded-[20px]
            "
        >

            <div
                class="
                    flex flex-col gap-4
                    p-4
                    sm:flex-row
                    sm:items-center
                    sm:justify-between
                    sm:p-5
                "
            >

                <div class="flex min-w-0 items-start gap-3">

                    <div
                        class="
                            grid h-11 w-11
                            shrink-0 place-items-center
                            rounded-xl
                            bg-[#fff2d8]
                            text-[#b77900]
                        "
                    >
                        <svg
                            viewBox="0 0 28 24"
                            class="h-[19px] w-[21px]"
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


                    <div class="min-w-0">

                        <div
                            class="
                                flex flex-wrap
                                items-center gap-2
                            "
                        >

                            <p
                                class="
                                    text-[13px] font-bold
                                    text-[#29241e]
                                "
                            >
                                {{ $activeOrder->order_number }}
                            </p>


                            <span
                                class="
                                    inline-flex items-center gap-1.5
                                    rounded-full
                                    border border-[#f0d49a]
                                    bg-[#fff4d8]
                                    px-2.5 py-1
                                    text-[8px] font-bold uppercase
                                    tracking-[.06em]
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

                                Active
                            </span>

                        </div>


                        <p
                            class="
                                mt-1.5
                                text-[10px] font-semibold
                                text-[#6f5520]
                            "
                        >
                            Current Assignment
                        </p>


                        <p
                            class="
                                mt-1
                                text-[9px]
                                text-[#918677]
                            "
                        >
                            {{ $activeOrder->statusLabel() }}
                        </p>

                    </div>

                </div>


                <a
                    href="{{ route('courier.dashboard') }}"
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
                        sm:self-auto
                    "
                >
                    Open Delivery Workflow

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

        </section>

    @endif


    {{-- =========================================================
         ASSIGNMENTS SECTION
    ========================================================== --}}
    <section class="mt-5">

        <div
            class="
                mb-3
                flex flex-col gap-2
                sm:flex-row
                sm:items-end
                sm:justify-between
            "
        >

            <div>

                <h3
                    class="
                        text-[13px] font-bold
                        text-[#211d17]
                    "
                >
                    Delivery Assignments
                </h3>


                <p
                    class="
                        mt-1
                        text-[9px]
                        text-[#918677]
                    "
                >
                    Orders recently dispatched by SARI Logistics.
                </p>

            </div>


            @if ($orders->count() > 0)

                <span
                    class="
                        inline-flex self-start
                        items-center gap-2
                        rounded-lg
                        border border-[#eadfc9]
                        bg-[#fffaf1]
                        px-3 py-2
                        text-[8px] font-semibold
                        text-[#817769]
                        sm:self-auto
                    "
                >
                    <span
                        class="
                            h-1.5 w-1.5
                            rounded-full
                            bg-[#d9930a]
                        "
                    ></span>

                    Live assignment updates enabled
                </span>

            @endif

        </div>


        <div class="grid gap-4 xl:grid-cols-2">

            @forelse ($orders as $order)

                <article
                    class="
                        reveal assignment-card assignment-hover
                        overflow-hidden
                        rounded-[20px]
                    "
                >

                    {{-- =============================================
                         CARD HEADER
                    ============================================== --}}
                    <div
                        class="
                            flex items-start justify-between gap-4
                            border-b border-[#eee4d3]
                            px-4 py-4
                            sm:px-5
                        "
                    >

                        <div class="flex min-w-0 items-start gap-3">

                            <div
                                class="
                                    grid h-10 w-10
                                    shrink-0 place-items-center
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
                                    <path d="M4 7h16v11H4z"></path>
                                    <path d="m4 7 3-3h10l3 3"></path>
                                    <path d="M8 11h8"></path>
                                </svg>
                            </div>


                            <div class="min-w-0">

                                <p
                                    class="
                                        text-[8px] font-semibold uppercase
                                        tracking-[.09em]
                                        text-[#a09587]
                                    "
                                >
                                    Delivery Order
                                </p>


                                <h4
                                    class="
                                        mt-1 truncate
                                        text-[13px] font-bold
                                        tracking-[-.02em]
                                        text-[#29241e]
                                    "
                                >
                                    {{ $order->order_number }}
                                </h4>


                                <p
                                    class="
                                        mt-1
                                        text-[9px]
                                        text-[#918677]
                                    "
                                >
                                    {{ $order->seller?->store_name ?: 'SARI Seller' }}
                                </p>

                            </div>

                        </div>


                        <span
                            class="
                                inline-flex shrink-0
                                items-center gap-1.5
                                rounded-full
                                border border-[#f0d49a]
                                bg-[#fff4d8]
                                px-2.5 py-1.5
                                text-[8px] font-bold uppercase
                                tracking-[.06em]
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

                            Assigned
                        </span>

                    </div>


                    {{-- =============================================
                         ROUTE INFORMATION
                    ============================================== --}}
                    <div class="p-4 sm:p-5">

                        <div
                            class="
                                grid gap-3
                                lg:grid-cols-[minmax(0,1fr)_36px_minmax(0,1fr)]
                                lg:items-stretch
                            "
                        >

                            {{-- PICKUP --}}
                            <div
                                class="
                                    rounded-2xl
                                    border border-[#eee5d6]
                                    bg-white
                                    p-4
                                "
                            >

                                <div class="flex items-start gap-3">

                                    <div
                                        class="
                                            grid h-9 w-9
                                            shrink-0 place-items-center
                                            rounded-xl
                                            bg-[#fff7e8]
                                            text-[#b77900]
                                        "
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <path d="M4 7h16v11H4z"></path>
                                            <path d="m4 7 3-3h10l3 3"></path>
                                        </svg>
                                    </div>


                                    <div class="min-w-0">

                                        <p
                                            class="
                                                text-[8px] font-bold uppercase
                                                tracking-[.09em]
                                                text-[#a09484]
                                            "
                                        >
                                            Pickup
                                        </p>


                                        <p
                                            class="
                                                mt-1.5
                                                text-[10px] font-semibold
                                                leading-4
                                                text-[#2a251f]
                                            "
                                        >
                                            {{ $order->pickup_name }}
                                        </p>


                                        <p
                                            class="
                                                mt-1.5
                                                text-[9px] leading-5
                                                text-[#817769]
                                            "
                                        >
                                            {{ $order->pickup_address }}
                                        </p>

                                    </div>

                                </div>

                            </div>


                            {{-- ARROW --}}
                            <div
                                class="
                                    hidden
                                    items-center
                                    justify-center
                                    lg:flex
                                "
                            >
                                <div
                                    class="
                                        grid h-8 w-8
                                        place-items-center
                                        rounded-full
                                        bg-[#fbf7ef]
                                        text-[#b78a37]
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M5 12h14"></path>
                                        <path d="m14 7 5 5-5 5"></path>
                                    </svg>
                                </div>
                            </div>


                            {{-- BUYER --}}
                            <div
                                class="
                                    rounded-2xl
                                    border border-[#eee5d6]
                                    bg-white
                                    p-4
                                "
                            >

                                <div class="flex items-start gap-3">

                                    <div
                                        class="
                                            grid h-9 w-9
                                            shrink-0 place-items-center
                                            rounded-xl
                                            bg-[#fff7e8]
                                            text-[#b77900]
                                        "
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                                            <circle cx="12" cy="10" r="2.5"></circle>
                                        </svg>
                                    </div>


                                    <div class="min-w-0">

                                        <p
                                            class="
                                                text-[8px] font-bold uppercase
                                                tracking-[.09em]
                                                text-[#a09484]
                                            "
                                        >
                                            Buyer
                                        </p>


                                        <p
                                            class="
                                                mt-1.5
                                                text-[10px] font-semibold
                                                leading-4
                                                text-[#2a251f]
                                            "
                                        >
                                            {{ $order->buyer_name }}
                                        </p>


                                        <p
                                            class="
                                                mt-1.5
                                                text-[9px] leading-5
                                                text-[#817769]
                                            "
                                        >
                                            {{ $order->buyer_address }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- =========================================
                             CARD FOOTER
                        ========================================== --}}
                        <div
                            class="
                                mt-4
                                flex flex-col gap-3
                                border-t border-[#eee5d6]
                                pt-4
                                sm:flex-row
                                sm:items-center
                                sm:justify-between
                            "
                        >

                            <div>

                                <p
                                    class="
                                        text-[8px] font-semibold uppercase
                                        tracking-[.09em]
                                        text-[#9a9082]
                                    "
                                >
                                    Delivery Fee
                                </p>


                                <p
                                    class="
                                        mt-1
                                        text-[13px] font-bold
                                        text-[#29241e]
                                    "
                                >
                                    ₱{{ number_format(
                                        (float) $order->delivery_fee,
                                        2
                                    ) }}
                                </p>

                            </div>


                            <a
                                href="{{ route('courier.dashboard') }}"
                                class="
                                    inline-flex min-h-10
                                    items-center justify-center gap-2
                                    rounded-xl
                                    bg-[#d9930a]
                                    px-5 py-2.5
                                    text-[10px] font-semibold
                                    text-white
                                    shadow-[0_8px_18px_rgba(217,147,10,.15)]
                                    transition
                                    hover:bg-[#c98505]
                                "
                            >
                                Open Workflow

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

                </article>


            @empty

                {{-- =============================================
                     EMPTY STATE
                ============================================== --}}
                <div
                    class="
                        reveal assignment-grid
                        xl:col-span-2
                        overflow-hidden
                        rounded-[20px]
                        border border-[#eadfc9]
                        bg-[#fffdf9]
                    "
                >

                    <div
                        class="
                            mx-auto
                            flex min-h-[290px]
                            max-w-[460px]
                            flex-col
                            items-center
                            justify-center
                            px-6 py-9
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
                                viewBox="0 0 24 24"
                                class="h-7 w-7"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                            >
                                <path d="M7 4h10"></path>
                                <path d="M9 2h6v4H9z"></path>
                                <path d="M6 4H5a2 2 0 0 0-2 2v14h18V6a2 2 0 0 0-2-2h-1"></path>
                                <path d="M8 11h8"></path>
                                <path d="M8 15h5"></path>
                            </svg>


                            <span
                                class="
                                    absolute -right-1 -top-1
                                    h-4 w-4
                                    rounded-full
                                    border-[3px] border-[#fffdf9]
                                    bg-[#b8afa1]
                                "
                            ></span>

                        </div>


                        <h3
                            class="
                                mt-4
                                text-[13px] font-bold
                                tracking-[-.02em]
                                text-[#413a31]
                            "
                        >
                            No assignment yet
                        </h3>


                        <p
                            class="
                                mt-2
                                max-w-[350px]
                                text-[10px] leading-5
                                text-[#918677]
                            "
                        >
                            When SARI Logistics dispatches an order to
                            your rider account, it will appear here
                            automatically.
                        </p>


                        <div
                            class="
                                mt-5
                                inline-flex
                                items-center gap-2.5
                                rounded-xl
                                border border-[#eee4d3]
                                bg-[#fffdf9]
                                px-4 py-3
                            "
                        >
                            <span
                                class="
                                    h-2 w-2
                                    rounded-full
                                    bg-[#d9930a]
                                "
                            ></span>

                            <p
                                class="
                                    text-[9px] font-semibold
                                    text-[#817769]
                                "
                            >
                                Live assignment refresh is enabled
                            </p>
                        </div>


                        <a
                            href="{{ route('courier.dashboard') }}"
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
                            Return to Dashboard

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

            @endforelse

        </div>

    </section>

</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let currentCount =
        {{ $orders->count() }};


    async function checkAssignments() {

        if (document.hidden) {
            return;
        }


        try {

            const response = await fetch(
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
                            'XMLHttpRequest'
                    },

                    credentials:
                        'same-origin',

                    cache:
                        'no-store'
                }
            );


            if (!response.ok) {
                return;
            }


            const data =
                await response.json();


            if (
                Number(data.assigned_count) !==
                Number(currentCount)
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

});
</script>
@endpush

@endsection