@extends('layouts.courier')

@section('title', 'Pick Up Orders')
@section('header-title', 'Pick Up Orders')
@section('header-subtitle', 'Seller Pickup Queue')

@push('styles')
<style>
    .rider-card {
        border: 1px solid #eee4d3;
        background: #fffdf9;
        box-shadow: 0 8px 24px rgba(75, 59, 30, .035);
    }

    .pickup-card {
        transition:
            transform .22s ease,
            border-color .22s ease,
            box-shadow .22s ease;
    }

    .pickup-card:hover {
        transform: translateY(-2px);
        border-color: #dbc89f;
        box-shadow: 0 14px 32px rgba(75, 59, 30, .07);
    }

    .pickup-grid {
        background-image:
            linear-gradient(
                to right,
                rgba(225, 213, 192, .16) 1px,
                transparent 1px
            ),
            linear-gradient(
                to bottom,
                rgba(225, 213, 192, .16) 1px,
                transparent 1px
            );

        background-size: 28px 28px;
    }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-[1620px]">

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
                        text-[8px] font-bold uppercase
                        tracking-[.11em]
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

                    Seller Pickup
                </span>


                <span
                    class="
                        inline-flex items-center gap-2
                        rounded-full
                        border border-[#e4ded4]
                        bg-white
                        px-3 py-1.5
                        text-[8px] font-semibold
                        text-[#817769]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-3 w-3"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M4 7h16v11H4z"></path>
                        <path d="m4 7 3-3h10l3 3"></path>
                    </svg>

                    {{ $orders->count() }} pickup
                    {{ $orders->count() === 1 ? 'order' : 'orders' }}
                </span>

            </div>


            <h2
                class="
                    mt-3
                    text-[18px] font-bold
                    tracking-[-.025em]
                    text-[#211d17]
                    sm:text-[20px]
                "
            >
                Orders ready for pickup
            </h2>


            <p
                class="
                    mt-1.5
                    max-w-xl
                    text-[9px] leading-5
                    text-[#817769]
                    sm:text-[10px]
                "
            >
                Review assigned deliveries that are still in the Seller pickup
                stage and open the delivery workflow when you are ready.
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
                text-[9px] font-semibold
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
         SUMMARY STRIP
    ========================================================== --}}
    <section
        class="
            reveal rider-card
            mt-5
            rounded-2xl
            px-4 py-4
            sm:px-5
        "
    >
        <div
            class="
                flex flex-col gap-4
                sm:flex-row
                sm:items-center
                sm:justify-between
            "
        >

            <div class="flex items-center gap-3">

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


                <div>

                    <p
                        class="
                            text-[7px] font-bold uppercase
                            tracking-[.1em]
                            text-[#9a9082]
                        "
                    >
                        Pickup Queue
                    </p>

                    <p
                        class="
                            mt-1
                            text-[10px] font-semibold
                            text-[#413a31]
                        "
                    >
                        {{ $orders->count() > 0
                            ? 'You have seller pickup orders waiting.'
                            : 'Your seller pickup queue is clear.'
                        }}
                    </p>

                </div>

            </div>


            <div
                class="
                    inline-flex self-start
                    items-center gap-2
                    rounded-xl
                    bg-[#fbf7ef]
                    px-3.5 py-2.5
                    text-[8px]
                    text-[#817769]
                    sm:self-auto
                "
            >
                <span
                    class="
                        h-2 w-2
                        rounded-full
                        {{ $orders->count() > 0
                            ? 'bg-[#d9930a]'
                            : 'bg-[#4F7D63]'
                        }}
                    "
                ></span>

                {{ $orders->count() > 0
                    ? $orders->count().' awaiting pickup'
                    : 'No pending pickups'
                }}
            </div>

        </div>
    </section>


    {{-- =========================================================
         PICKUP ORDERS
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
                        text-[12px] font-bold
                        text-[#211d17]
                    "
                >
                    Seller Pickup Queue
                </h3>

                <p
                    class="
                        mt-1
                        text-[8px]
                        text-[#918677]
                    "
                >
                    Deliveries currently waiting at the Seller pickup stage.
                </p>

            </div>


            @if ($orders->count() > 0)

                <span
                    class="
                        inline-flex self-start
                        items-center gap-2
                        rounded-lg
                        border border-[#eee4d3]
                        bg-white
                        px-3 py-2
                        text-[7px] font-semibold
                        text-[#817769]
                        sm:self-auto
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-3 w-3 text-[#a66d08]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 7v5l3 2"></path>
                    </svg>

                    Ready for rider action
                </span>

            @endif

        </div>


        <div class="grid gap-4 xl:grid-cols-2">

            @forelse ($orders as $order)

                <article
                    class="
                        reveal pickup-card
                        overflow-hidden
                        rounded-[20px]
                        border border-[#eadfc9]
                        bg-[#fffdf9]
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
                                        text-[7px] font-semibold uppercase
                                        tracking-[.1em]
                                        text-[#a09587]
                                    "
                                >
                                    Pickup Order
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
                                        text-[8px]
                                        text-[#918677]
                                    "
                                >
                                    Assigned seller pickup
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
                                text-[7px] font-bold uppercase
                                tracking-[.07em]
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

                            {{ strtoupper($order->statusLabel()) }}
                        </span>

                    </div>


                    {{-- =============================================
                         PICKUP INFORMATION
                    ============================================== --}}
                    <div class="p-4 sm:p-5">

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
                                        grid h-10 w-10
                                        shrink-0 place-items-center
                                        rounded-xl
                                        bg-[#fff7e8]
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
                                        <path d="M3 10h18"></path>
                                        <path d="M5 10v9h14v-9"></path>
                                        <path d="m4 10 2-5h12l2 5"></path>
                                        <path d="M9 19v-5h6v5"></path>
                                    </svg>
                                </div>


                                <div class="min-w-0 flex-1">

                                    <p
                                        class="
                                            text-[7px] font-bold uppercase
                                            tracking-[.1em]
                                            text-[#a09484]
                                        "
                                    >
                                        Seller Pickup Point
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


                                    <div
                                        class="
                                            mt-2
                                            flex items-start gap-2
                                        "
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            class="
                                                mt-0.5
                                                h-3.5 w-3.5
                                                shrink-0
                                                text-[#a99e90]
                                            "
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                                            <circle cx="12" cy="10" r="2.5"></circle>
                                        </svg>


                                        <p
                                            class="
                                                text-[8px] leading-5
                                                text-[#817769]
                                            "
                                        >
                                            {{ $order->pickup_address }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- =========================================
                             PICKUP STATUS GUIDE
                        ========================================== --}}
                        <div
                            class="
                                mt-4
                                rounded-xl
                                bg-[#fbf8f2]
                                px-4 py-3.5
                            "
                        >

                            <div class="flex items-start gap-3">

                                <div
                                    class="
                                        mt-0.5
                                        grid h-7 w-7
                                        shrink-0 place-items-center
                                        rounded-lg
                                        bg-white
                                        text-[#a66d08]
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 8v4"></path>
                                        <path d="M12 16h.01"></path>
                                    </svg>
                                </div>


                                <div>

                                    <p
                                        class="
                                            text-[8px] font-semibold
                                            text-[#51483d]
                                        "
                                    >
                                        Seller handoff pending
                                    </p>

                                    <p
                                        class="
                                            mt-1
                                            text-[7px] leading-4
                                            text-[#918677]
                                        "
                                    >
                                        Open this delivery to continue the current
                                        pickup workflow and view the next required
                                        rider action.
                                    </p>

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
                                        text-[7px] font-semibold uppercase
                                        tracking-[.09em]
                                        text-[#9a9082]
                                    "
                                >
                                    Order
                                </p>

                                <p
                                    class="
                                        mt-1
                                        text-[9px] font-bold
                                        text-[#413a31]
                                    "
                                >
                                    {{ $order->order_number }}
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
                                    text-[9px] font-semibold
                                    text-white
                                    shadow-[0_8px_18px_rgba(217,147,10,.15)]
                                    transition
                                    hover:bg-[#c98505]
                                "
                            >
                                Open Delivery

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
                        reveal pickup-grid
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
                            flex min-h-[300px]
                            max-w-[470px]
                            flex-col items-center
                            justify-center
                            px-6 py-10
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
                                <path d="M4 7h16v11H4z"></path>
                                <path d="m4 7 3-3h10l3 3"></path>
                                <path d="M8 11h8"></path>
                            </svg>


                            <span
                                class="
                                    absolute -right-1 -top-1
                                    grid h-5 w-5
                                    place-items-center
                                    rounded-full
                                    border-[3px] border-[#fffdf9]
                                    bg-[#4F7D63]
                                    text-white
                                "
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-2.5 w-2.5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                >
                                    <path d="m5 12 4 4L19 6"></path>
                                </svg>
                            </span>

                        </div>


                        <h3
                            class="
                                mt-4
                                text-[13px] font-bold
                                tracking-[-.02em]
                                text-[#413a31]
                            "
                        >
                            No pickup orders right now
                        </h3>


                        <p
                            class="
                                mt-2
                                max-w-[350px]
                                text-[8px] leading-5
                                text-[#918677]
                            "
                        >
                            Orders that reach the Seller pickup stage will
                            appear here so you can quickly continue the
                            delivery workflow.
                        </p>


                        <div
                            class="
                                mt-5
                                inline-flex
                                items-center gap-2.5
                                rounded-xl
                                border border-[#e5eee8]
                                bg-[#f3f8f5]
                                px-4 py-3
                            "
                        >

                            <span
                                class="
                                    h-2 w-2
                                    rounded-full
                                    bg-[#4F7D63]
                                "
                            ></span>

                            <p
                                class="
                                    text-[7px] font-semibold
                                    text-[#587160]
                                "
                            >
                                Your pickup queue is currently clear
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
                                text-[9px] font-semibold
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
@endsection