@extends('layouts.courier')

@section('title', 'Earnings & Payouts')
@section('header-title', 'Earnings & Payouts')
@section('header-subtitle', 'Rider Earnings')

@push('styles')
<style>
    .earnings-card {
        border: 1px solid #eee4d3;
        background: #fffdf9;
        box-shadow: 0 8px 24px rgba(75, 59, 30, .035);
    }

    .earnings-card-hover {
        transition:
            transform .22s ease,
            border-color .22s ease,
            box-shadow .22s ease;
    }

    .earnings-card-hover:hover {
        transform: translateY(-2px);
        border-color: #dbc89f;
        box-shadow: 0 14px 32px rgba(75, 59, 30, .07);
    }

    .earnings-grid {
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

    .earning-bar {
        background:
            linear-gradient(
                180deg,
                #e6aa2e 0%,
                #d9930a 100%
            );

        transition:
            transform .2s ease,
            opacity .2s ease;
    }

    .earning-bar:hover {
        transform: translateY(-3px);
        opacity: .88;
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

                    Rider Earnings
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
                        <path d="M12 3v18"></path>
                        <path d="M16.5 7c0-1.5-2-2.7-4.5-2.7S7.5 5.5 7.5 7s2 2.7 4.5 2.7 4.5 1.2 4.5 2.7-2 2.7-4.5 2.7-4.5-1.2-4.5-2.7"></path>
                    </svg>

                    Earnings Overview
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
                Earnings & payout overview
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
                Track credited delivery fees, pending earnings,
                available balance, and your weekly rider performance.
            </p>

        </div>


        <a
            href="{{ route('courier.earnings.statement') }}"
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
                <path d="M12 3v12"></path>
                <path d="m7 10 5 5 5-5"></path>
                <path d="M5 21h14"></path>
            </svg>

            Download Statement
        </a>

    </section>


    {{-- =========================================================
         KPI CARDS
    ========================================================== --}}
    <section
        class="
            mt-5
            grid gap-3
            sm:grid-cols-2
            lg:grid-cols-3
            xl:grid-cols-5
        "
    >

        {{-- TODAY --}}
        <article
            class="
                reveal earnings-card earnings-card-hover
                relative overflow-hidden
                rounded-2xl p-4
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
                Today
            </p>


            <p
                class="
                    mt-1
                    text-[21px] font-bold
                    tracking-[-.04em]
                    text-[#211d17]
                "
            >
                ₱{{ number_format($earnings['today'], 2) }}
            </p>


            <p
                class="
                    mt-1
                    text-[9px]
                    text-[#a09587]
                "
            >
                Today's credited earnings
            </p>
        </article>


        {{-- WEEK --}}
        <article
            class="
                reveal earnings-card earnings-card-hover
                relative overflow-hidden
                rounded-2xl p-4
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
                    viewBox="0 0 24 24"
                    class="h-[18px] w-[18px]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <rect
                        x="4"
                        y="5"
                        width="16"
                        height="15"
                        rx="2"
                    ></rect>

                    <path d="M8 3v4"></path>
                    <path d="M16 3v4"></path>
                    <path d="M4 10h16"></path>
                </svg>
            </div>


            <p
                class="
                    mt-3
                    text-[9px] font-medium
                    text-[#817769]
                "
            >
                This Week
            </p>


            <p
                class="
                    mt-1
                    text-[21px] font-bold
                    tracking-[-.04em]
                    text-[#211d17]
                "
            >
                ₱{{ number_format($earnings['week'], 2) }}
            </p>


            <p
                class="
                    mt-1
                    text-[9px]
                    text-[#a09587]
                "
            >
                Current week total
            </p>
        </article>


        {{-- MONTH --}}
        <article
            class="
                reveal earnings-card earnings-card-hover
                relative overflow-hidden
                rounded-2xl p-4
            "
        >
            <div
                class="
                    absolute inset-x-0 top-0
                    h-[3px]
                    bg-[#b98218]
                "
            ></div>


            <div
                class="
                    grid h-10 w-10
                    place-items-center
                    rounded-xl
                    bg-[#f9efd9]
                    text-[#a97009]
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-[18px] w-[18px]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M4 19V9"></path>
                    <path d="M10 19V5"></path>
                    <path d="M16 19V12"></path>
                    <path d="M22 19H2"></path>
                </svg>
            </div>


            <p
                class="
                    mt-3
                    text-[9px] font-medium
                    text-[#817769]
                "
            >
                This Month
            </p>


            <p
                class="
                    mt-1
                    text-[21px] font-bold
                    tracking-[-.04em]
                    text-[#211d17]
                "
            >
                ₱{{ number_format($earnings['month'], 2) }}
            </p>


            <p
                class="
                    mt-1
                    text-[9px]
                    text-[#a09587]
                "
            >
                Month-to-date earnings
            </p>
        </article>


        {{-- PENDING --}}
        <article
            class="
                reveal earnings-card earnings-card-hover
                relative overflow-hidden
                rounded-2xl p-4
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
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 7v5"></path>
                    <path d="M12 16h.01"></path>
                </svg>
            </div>


            <p
                class="
                    mt-3
                    text-[9px] font-medium
                    text-[#817769]
                "
            >
                Pending
            </p>


            <p
                class="
                    mt-1
                    text-[21px] font-bold
                    tracking-[-.04em]
                    text-[#211d17]
                "
            >
                ₱{{ number_format($earnings['pending'], 2) }}
            </p>


            <p
                class="
                    mt-1
                    text-[9px]
                    text-[#a09587]
                "
            >
                Awaiting credit
            </p>
        </article>


        {{-- AVAILABLE --}}
        <article
            class="
                reveal earnings-card earnings-card-hover
                relative overflow-hidden
                rounded-2xl p-4
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
                    <path d="M4 7h16v12H4z"></path>
                    <path d="M4 10h16"></path>
                    <circle cx="16" cy="15" r="1"></circle>
                </svg>
            </div>


            <p
                class="
                    mt-3
                    text-[9px] font-medium
                    text-[#817769]
                "
            >
                Available
            </p>


            <p
                class="
                    mt-1
                    text-[21px] font-bold
                    tracking-[-.04em]
                    text-[#211d17]
                "
            >
                ₱{{ number_format($earnings['available'], 2) }}
            </p>


            <p
                class="
                    mt-1
                    text-[9px]
                    text-[#a09587]
                "
            >
                Ready for payout
            </p>
        </article>

    </section>


    {{-- =========================================================
         PERFORMANCE + PAYOUT
    ========================================================== --}}
    <section
        class="
            mt-5
            grid gap-5
            xl:grid-cols-[minmax(0,1.45fr)_minmax(310px,.55fr)]
        "
    >

        {{-- =====================================================
             WEEKLY PERFORMANCE
        ====================================================== --}}
        <article
            class="
                reveal earnings-card
                overflow-hidden
                rounded-[20px]
            "
        >

            <div
                class="
                    flex flex-col gap-3
                    border-b border-[#eee4d3]
                    px-5 py-4
                    sm:flex-row
                    sm:items-center
                    sm:justify-between
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
                            viewBox="0 0 24 24"
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M4 19V9"></path>
                            <path d="M10 19V5"></path>
                            <path d="M16 19V12"></path>
                            <path d="M22 19H2"></path>
                        </svg>
                    </div>


                    <div>

                        <h3
                            class="
                                text-[13px] font-bold
                                text-[#211d17]
                            "
                        >
                            Weekly Performance
                        </h3>


                        <p
                            class="
                                mt-1
                                text-[9px]
                                text-[#918677]
                            "
                        >
                            Delivery earnings for the current week.
                        </p>

                    </div>

                </div>


                @if ($weeklyChange === null)

                    <span
                        class="
                            self-start
                            rounded-full
                            border border-[#e7dfd2]
                            bg-[#fbf8f2]
                            px-3 py-1.5
                            text-[8px] font-semibold
                            text-[#817769]
                            sm:self-auto
                        "
                    >
                        No prior week
                    </span>


                @elseif ($weeklyChange >= 0)

                    <span
                        class="
                            inline-flex self-start
                            items-center gap-1.5
                            rounded-full
                            border border-emerald-200
                            bg-emerald-50
                            px-3 py-1.5
                            text-[8px] font-semibold
                            text-emerald-700
                            sm:self-auto
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-3 w-3"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="m6 15 6-6 6 6"></path>
                        </svg>

                        +{{ number_format($weeklyChange, 1) }}%
                    </span>


                @else

                    <span
                        class="
                            inline-flex self-start
                            items-center gap-1.5
                            rounded-full
                            border border-[#eadfc9]
                            bg-[#fff8e8]
                            px-3 py-1.5
                            text-[8px] font-semibold
                            text-[#9b6b14]
                            sm:self-auto
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-3 w-3"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>

                        {{ number_format($weeklyChange, 1) }}%
                    </span>

                @endif

            </div>


            @php
                $maxAmount = max(
                    1,
                    max(array_column($daily, 'amount'))
                );
            @endphp


            <div class="px-4 pb-5 pt-4 sm:px-5">

                <div
                    class="
                        earnings-grid
                        rounded-2xl
                        border border-[#eee5d6]
                        bg-[#fffdf9]
                        px-3 pb-4 pt-5
                        sm:px-5
                    "
                >

                    <div
                        class="
                            grid h-[240px]
                            grid-cols-7
                            items-end
                            gap-2
                            sm:gap-4
                        "
                    >

                        @foreach ($daily as $day)

                            @php
                                $height = max(
                                    18,
                                    round(
                                        ($day['amount'] / $maxAmount) * 170
                                    )
                                );
                            @endphp


                            <div
                                class="
                                    flex h-full
                                    min-w-0
                                    flex-col
                                    justify-end
                                "
                            >

                                <div
                                    class="
                                        mb-2
                                        truncate
                                        text-center
                                        text-[8px] font-semibold
                                        text-[#645b4f]
                                    "
                                >
                                    ₱{{ number_format($day['amount'], 0) }}
                                </div>


                                <div
                                    class="
                                        earning-bar
                                        mx-auto
                                        w-full
                                        max-w-[44px]
                                        rounded-t-[10px]
                                    "
                                    style="height: {{ $height }}px"
                                ></div>


                                <p
                                    class="
                                        mt-2.5
                                        text-center
                                        text-[9px] font-semibold
                                        text-[#857b6d]
                                    "
                                >
                                    {{ $day['day'] }}
                                </p>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>

        </article>


        {{-- =====================================================
             PAYOUT SUMMARY
        ====================================================== --}}
        <aside
            class="
                reveal earnings-card
                overflow-hidden
                rounded-[20px]
            "
        >

            <div
                class="
                    border-b border-[#eee4d3]
                    px-5 py-4
                "
            >

                <div class="flex items-center gap-3">

                    <div
                        class="
                            grid h-9 w-9
                            place-items-center
                            rounded-xl
                            bg-[#fff2d8]
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
                            <path d="M4 7h16v12H4z"></path>
                            <path d="M4 10h16"></path>
                            <circle cx="16" cy="15" r="1"></circle>
                        </svg>
                    </div>


                    <div>

                        <h3
                            class="
                                text-[13px] font-bold
                                text-[#211d17]
                            "
                        >
                            Payout Summary
                        </h3>


                        <p
                            class="
                                mt-1
                                text-[9px]
                                text-[#918677]
                            "
                        >
                            Your current rider balance.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-4 sm:p-5">

                {{-- =============================================
                     AVAILABLE BALANCE
                ============================================== --}}
                <div
                    class="
                        relative overflow-hidden
                        rounded-2xl
                        bg-[#29241e]
                        p-5
                        text-white
                    "
                >

                    <div
                        class="
                            absolute
                            -right-8 -top-8
                            h-28 w-28
                            rounded-full
                            bg-[#d9930a]/10
                        "
                    ></div>


                    <div
                        class="
                            absolute
                            -bottom-10 -left-10
                            h-32 w-32
                            rounded-full
                            bg-white/[.025]
                        "
                    ></div>


                    <div class="relative z-10">

                        <div
                            class="
                                flex items-center
                                justify-between gap-3
                            "
                        >

                            <p
                                class="
                                    text-[8px] font-bold uppercase
                                    tracking-[.1em]
                                    text-white/55
                                "
                            >
                                Available Balance
                            </p>


                            <div
                                class="
                                    grid h-7 w-7
                                    place-items-center
                                    rounded-lg
                                    bg-white/10
                                    text-[#e6aa2e]
                                "
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-3.5 w-3.5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path d="M4 7h16v12H4z"></path>
                                    <path d="M4 10h16"></path>
                                </svg>
                            </div>

                        </div>


                        <p
                            class="
                                mt-3
                                text-[26px] font-bold
                                tracking-[-.04em]
                            "
                        >
                            ₱{{ number_format($earnings['available'], 2) }}
                        </p>


                        <div
                            class="
                                mt-4
                                flex items-center gap-2
                                border-t border-white/10
                                pt-3
                            "
                        >
                            <span
                                class="
                                    h-1.5 w-1.5
                                    rounded-full

                                    {{ $earnings['available'] > 0
                                        ? 'bg-[#e6aa2e]'
                                        : 'bg-white/30'
                                    }}
                                "
                            ></span>


                            <p
                                class="
                                    text-[8px]
                                    leading-4
                                    text-white/60
                                "
                            >
                                {{ $earnings['available'] > 0
                                    ? 'Balance available for payout request'
                                    : 'Complete deliveries to earn payout balance'
                                }}
                            </p>
                        </div>

                    </div>

                </div>


                {{-- =============================================
                     PAYOUT BUTTON
                ============================================== --}}
                <form
                    method="POST"
                    action="{{ route('courier.earnings.payout') }}"
                    class="mt-4"
                >
                    @csrf

                    <button
                        class="
                            inline-flex w-full
                            min-h-11
                            items-center
                            justify-center gap-2
                            rounded-xl
                            bg-[#d9930a]
                            px-4 py-3
                            text-[10px] font-semibold
                            text-white
                            shadow-[0_8px_18px_rgba(217,147,10,.15)]
                            transition
                            hover:bg-[#c98505]
                            disabled:cursor-not-allowed
                            disabled:bg-[#e7dcc8]
                            disabled:text-[#a99d8b]
                            disabled:shadow-none
                        "
                        @disabled($earnings['available'] <= 0)
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-3.5 w-3.5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M12 3v12"></path>
                            <path d="m7 10 5 5 5-5"></path>
                            <path d="M5 21h14"></path>
                        </svg>

                        Request Payout
                    </button>
                </form>


                {{-- =============================================
                     RECENT REQUESTS
                ============================================== --}}
                <div class="mt-5">

                    <div
                        class="
                            mb-2.5
                            flex items-center
                            justify-between gap-3
                        "
                    >

                        <p
                            class="
                                text-[10px] font-bold
                                text-[#51483d]
                            "
                        >
                            Recent Requests
                        </p>


                        @if ($payoutRequests->count() > 0)

                            <span
                                class="
                                    rounded-full
                                    bg-[#fbf7ef]
                                    px-2.5 py-1
                                    text-[8px] font-semibold
                                    text-[#918677]
                                "
                            >
                                {{ $payoutRequests->count() }}
                            </span>

                        @endif

                    </div>


                    <div class="space-y-2">

                        @forelse ($payoutRequests as $payout)

                            @php
                                $payoutStatus =
                                    strtolower($payout->status);
                            @endphp


                            <div
                                class="
                                    flex items-center
                                    justify-between gap-3
                                    rounded-xl
                                    border border-[#eee4d3]
                                    bg-white
                                    px-3.5 py-3
                                "
                            >

                                <div>

                                    <p
                                        class="
                                            text-[10px] font-bold
                                            text-[#302a23]
                                        "
                                    >
                                        ₱{{ number_format(
                                            (float) $payout->amount,
                                            2
                                        ) }}
                                    </p>


                                    <p
                                        class="
                                            mt-0.5
                                            text-[8px]
                                            text-[#9a9082]
                                        "
                                    >
                                        Payout request
                                    </p>

                                </div>


                                <span
                                    class="
                                        rounded-full
                                        px-2.5 py-1
                                        text-[8px] font-semibold

                                        {{ in_array(
                                            $payoutStatus,
                                            [
                                                'approved',
                                                'paid',
                                                'completed'
                                            ],
                                            true
                                        )
                                            ? 'border border-emerald-200 bg-emerald-50 text-emerald-700'
                                            : 'border border-[#eadfc9] bg-[#fff8e8] text-[#9b6b14]'
                                        }}
                                    "
                                >
                                    {{ strtoupper($payout->status) }}
                                </span>

                            </div>


                        @empty

                            <div
                                class="
                                    rounded-xl
                                    border border-dashed
                                    border-[#ddd2c1]
                                    bg-[#fffcf7]
                                    px-4 py-5
                                    text-center
                                "
                            >

                                <div
                                    class="
                                        mx-auto
                                        grid h-9 w-9
                                        place-items-center
                                        rounded-xl
                                        bg-[#fff2d8]
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
                                        <path d="M4 7h16v12H4z"></path>
                                        <path d="M4 10h16"></path>
                                    </svg>
                                </div>


                                <p
                                    class="
                                        mt-2.5
                                        text-[9px] font-semibold
                                        text-[#51483d]
                                    "
                                >
                                    No payout requests yet
                                </p>


                                <p
                                    class="
                                        mt-1
                                        text-[8px]
                                        leading-4
                                        text-[#9a9082]
                                    "
                                >
                                    Your payout requests will appear here.
                                </p>

                            </div>

                        @endforelse

                    </div>

                </div>

            </div>

        </aside>

    </section>


    {{-- =========================================================
         RECENT EARNINGS
    ========================================================== --}}
    <section
        class="
            reveal earnings-card
            mt-5 overflow-hidden
            rounded-[20px]
        "
    >

        <div
            class="
                flex flex-col gap-3
                border-b border-[#eee4d3]
                px-5 py-4
                sm:flex-row
                sm:items-center
                sm:justify-between
            "
        >

            <div class="flex items-center gap-3">

                <div
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-xl
                        bg-[#fff2d8]
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
                        <path d="M4 5h16v14H4z"></path>
                        <path d="M8 9h8"></path>
                        <path d="M8 13h8"></path>
                    </svg>
                </div>


                <div>

                    <h3
                        class="
                            text-[13px] font-bold
                            text-[#211d17]
                        "
                    >
                        Recent Earnings
                    </h3>


                    <p
                        class="
                            mt-1
                            text-[9px]
                            text-[#918677]
                        "
                    >
                        Latest credited delivery fees.
                    </p>

                </div>

            </div>


            @if (count($transactions) > 0)

                <span
                    class="
                        self-start
                        rounded-lg
                        bg-[#fbf7ef]
                        px-3 py-2
                        text-[8px] font-semibold
                        text-[#817769]
                        sm:self-auto
                    "
                >
                    {{ count($transactions) }}
                    {{ count($transactions) === 1
                        ? 'transaction'
                        : 'transactions'
                    }}
                </span>

            @endif

        </div>


        @if (count($transactions) > 0)

            <div class="overflow-x-auto">

                <table class="w-full min-w-[720px]">

                    <thead class="bg-[#fbf8f1]">

                        <tr class="text-left">

                            <th
                                class="
                                    px-5 py-3
                                    text-[8px] font-bold uppercase
                                    tracking-[.1em]
                                    text-[#9c9182]
                                "
                            >
                                Order
                            </th>

                            <th
                                class="
                                    px-5 py-3
                                    text-[8px] font-bold uppercase
                                    tracking-[.1em]
                                    text-[#9c9182]
                                "
                            >
                                Date
                            </th>

                            <th
                                class="
                                    px-5 py-3
                                    text-[8px] font-bold uppercase
                                    tracking-[.1em]
                                    text-[#9c9182]
                                "
                            >
                                Type
                            </th>

                            <th
                                class="
                                    px-5 py-3
                                    text-[8px] font-bold uppercase
                                    tracking-[.1em]
                                    text-[#9c9182]
                                "
                            >
                                Amount
                            </th>

                            <th
                                class="
                                    px-5 py-3
                                    text-[8px] font-bold uppercase
                                    tracking-[.1em]
                                    text-[#9c9182]
                                "
                            >
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-[#f0e8db]">

                        @foreach ($transactions as $transaction)

                            <tr
                                class="
                                    transition
                                    hover:bg-[#fffaf1]
                                "
                            >

                                <td
                                    class="
                                        px-5 py-3.5
                                        text-[10px] font-semibold
                                        text-[#302a23]
                                    "
                                >
                                    {{ $transaction['order'] }}
                                </td>


                                <td
                                    class="
                                        px-5 py-3.5
                                        text-[9px]
                                        text-[#81776a]
                                    "
                                >
                                    {{ $transaction['date'] }}
                                </td>


                                <td
                                    class="
                                        px-5 py-3.5
                                        text-[9px]
                                        text-[#81776a]
                                    "
                                >
                                    {{ $transaction['type'] }}
                                </td>


                                <td
                                    class="
                                        px-5 py-3.5
                                        text-[10px] font-bold
                                        text-[#51483d]
                                    "
                                >
                                    +₱{{ number_format(
                                        $transaction['amount'],
                                        2
                                    ) }}
                                </td>


                                <td class="px-5 py-3.5">

                                    <span
                                        class="
                                            inline-flex
                                            items-center gap-1.5
                                            rounded-full
                                            border border-emerald-200
                                            bg-emerald-50
                                            px-2.5 py-1
                                            text-[8px] font-semibold
                                            text-emerald-700
                                        "
                                    >
                                        <span
                                            class="
                                                h-1.5 w-1.5
                                                rounded-full
                                                bg-emerald-500
                                            "
                                        ></span>

                                        {{ $transaction['status'] }}
                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


        @else

            {{-- =================================================
                 EMPTY TRANSACTIONS
            ================================================== --}}
            <div
                class="
                    earnings-grid
                    px-5 py-10
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
                            mx-auto
                            grid h-14 w-14
                            place-items-center
                            rounded-2xl
                            border border-[#eadfc9]
                            bg-[#fffdf9]
                            text-[#b47a11]
                            shadow-[0_8px_22px_rgba(75,59,30,.05)]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M4 5h16v14H4z"></path>
                            <path d="M8 9h8"></path>
                            <path d="M8 13h5"></path>
                        </svg>
                    </div>


                    <h4
                        class="
                            mt-4
                            text-[13px] font-bold
                            text-[#413a31]
                        "
                    >
                        No earnings history yet
                    </h4>


                    <p
                        class="
                            mx-auto mt-2
                            max-w-[330px]
                            text-[10px] leading-5
                            text-[#918677]
                        "
                    >
                        Completed delivery fee credits will appear here
                        once you begin completing rider assignments.
                    </p>

                </div>

            </div>

        @endif

    </section>

</div>
@endsection