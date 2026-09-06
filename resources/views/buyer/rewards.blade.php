@extends('layouts.buyer')

@section('title', 'Rewards — SARI')
@section('page-title', 'Rewards')

@section('content')

@include('components.buyer.header')

@php
    /*
    |--------------------------------------------------------------------------
    | SAFE DEFAULT VALUES
    |--------------------------------------------------------------------------
    |
    | These prevent the Rewards page from crashing when the controller
    | has not yet provided reward data.
    |
    */

    $points = $points ?? 0;
    $spent = $spent ?? 0;
    $orders = $orders ?? collect();
@endphp


<div class="mx-auto w-full max-w-[1200px] p-4 sm:p-6 lg:p-8">

    {{-- ============================================================
        REWARDS SUMMARY
    ============================================================ --}}
    <section
        class="
            overflow-hidden
            rounded-[24px]
            border
            border-[#eadfc9]
            bg-[#fffaf2]
            p-6
            shadow-[0_8px_28px_rgba(77,58,26,.035)]
            sm:p-8
        "
    >

        <p
            class="
                text-[9px]
                font-semibold
                uppercase
                tracking-[.16em]
                text-[#a8731f]
            "
        >
            SARI Rewards
        </p>


        <div
            class="
                mt-4
                grid
                gap-5
                md:grid-cols-[1fr_auto]
                md:items-end
            "
        >

            {{-- POINTS --}}
            <div>

                <h1
                    class="
                        text-[28px]
                        font-bold
                        tracking-[-.04em]
                        text-[#302a24]
                        sm:text-[32px]
                    "
                >
                    {{ number_format((int) $points) }}
                    <span class="text-[18px] font-semibold text-[#7e7364]">
                        points
                    </span>
                </h1>


                <p
                    class="
                        mt-2
                        max-w-[560px]
                        text-[10px]
                        leading-5
                        text-[#806f52]
                    "
                >
                    Automatically earned from successfully delivered purchases.
                </p>

            </div>


            {{-- DELIVERED SPEND --}}
            <div
                class="
                    min-w-[190px]
                    rounded-2xl
                    border
                    border-[#eadfc9]
                    bg-white
                    px-5
                    py-4
                    shadow-[0_5px_16px_rgba(71,55,29,.035)]
                "
            >

                <p
                    class="
                        text-[8px]
                        font-medium
                        uppercase
                        tracking-[.08em]
                        text-[#91887d]
                    "
                >
                    Delivered spend
                </p>


                <p
                    class="
                        mt-1.5
                        text-[20px]
                        font-bold
                        tracking-[-.03em]
                        text-[#b97805]
                    "
                >
                    ₱{{ number_format((float) $spent, 2) }}
                </p>

            </div>

        </div>

    </section>



    {{-- ============================================================
        REWARD ACTIVITY
    ============================================================ --}}
    <section
        class="
            mt-5
            rounded-[22px]
            border
            border-[#ebe4da]
            bg-white
            p-5
            shadow-[0_8px_26px_rgba(64,49,28,.025)]
            sm:p-6
        "
    >

        <div
            class="
                flex
                items-center
                justify-between
                gap-4
            "
        >

            <div>

                <p
                    class="
                        text-[8px]
                        font-semibold
                        uppercase
                        tracking-[.14em]
                        text-[#ad7a28]
                    "
                >
                    History
                </p>


                <h2
                    class="
                        mt-1
                        text-[17px]
                        font-bold
                        tracking-[-.025em]
                        text-[#302a24]
                    "
                >
                    Reward Activity
                </h2>

            </div>


            @if ($orders->count() > 0)

                <span
                    class="
                        inline-flex
                        min-h-[28px]
                        items-center
                        rounded-full
                        border
                        border-[#e8dfd1]
                        bg-[#fbf8f2]
                        px-3
                        text-[8px]
                        font-medium
                        text-[#786d5d]
                    "
                >
                    {{ number_format($orders->count()) }}
                    {{ $orders->count() === 1 ? 'activity' : 'activities' }}
                </span>

            @endif

        </div>



        <div class="mt-5 space-y-3">

            @forelse ($orders as $order)

                @php
                    $orderTotal = (float) ($order->total ?? 0);

                    $rewardRate = (float) config(
                        'sari_buyer.reward_points_per_peso',
                        0.10
                    );

                    $earnedPoints = (int) floor(
                        $orderTotal * $rewardRate
                    );

                    $orderNumber = $order->order_number ?? 'Order';

                    $deliveredDate = $order->delivered_at ?? null;
                @endphp


                <div
                    class="
                        flex
                        items-center
                        justify-between
                        gap-4

                        rounded-[16px]

                        border
                        border-[#eee7dc]

                        bg-[#fcfbf8]

                        p-4

                        transition-all
                        duration-200

                        hover:border-[#e0d3bf]
                        hover:bg-[#fffdf9]
                        hover:shadow-[0_6px_18px_rgba(69,52,26,.035)]
                    "
                >

                    <div
                        class="
                            flex
                            min-w-0
                            items-center
                            gap-3
                        "
                    >

                        {{-- REWARD ICON --}}
                        <div
                            class="
                                grid
                                h-10
                                w-10
                                shrink-0
                                place-items-center

                                rounded-xl

                                border
                                border-[#eadfc9]

                                bg-[#fff8e9]

                                text-[#b97805]
                            "
                        >

                            <svg
                                viewBox="0 0 24 24"
                                class="h-4.5 w-4.5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path d="M12 3v18"></path>
                                <path d="M17 7H9.5a2.5 2.5 0 0 0 0 5H14a2.5 2.5 0 0 1 0 5H6"></path>
                            </svg>

                        </div>


                        <div class="min-w-0">

                            <p
                                class="
                                    truncate
                                    text-[10px]
                                    font-semibold
                                    text-[#514a42]
                                "
                            >
                                {{ $orderNumber }}
                            </p>


                            <p
                                class="
                                    mt-1
                                    text-[8px]
                                    text-[#91887d]
                                "
                            >
                                Delivered

                                @if ($deliveredDate)

                                    {{ $deliveredDate instanceof \Carbon\CarbonInterface
                                        ? $deliveredDate->format('M d, Y')
                                        : \Carbon\Carbon::parse($deliveredDate)->format('M d, Y') }}

                                @else

                                    successfully

                                @endif
                            </p>

                        </div>

                    </div>


                    <div class="shrink-0 text-right">

                        <span
                            class="
                                inline-flex
                                items-center
                                rounded-full

                                border
                                border-[#cfe3d6]

                                bg-[#f2faf5]

                                px-3
                                py-1.5

                                text-[9px]
                                font-bold
                                text-[#4F7D63]
                            "
                        >
                            +{{ number_format($earnedPoints) }} pts
                        </span>


                        <p
                            class="
                                mt-1.5
                                text-[7.5px]
                                text-[#a29a90]
                            "
                        >
                            ₱{{ number_format($orderTotal, 2) }} delivered
                        </p>

                    </div>

                </div>


            @empty

                {{-- ====================================================
                    EMPTY STATE
                ==================================================== --}}
                <div
                    class="
                        rounded-[18px]
                        border
                        border-dashed
                        border-[#ded5c9]

                        bg-[#fcfaf6]

                        px-6
                        py-10

                        text-center
                    "
                >

                    <div
                        class="
                            mx-auto

                            grid
                            h-12
                            w-12
                            place-items-center

                            rounded-full

                            border
                            border-[#eadfc9]

                            bg-white

                            text-[#c38a27]

                            shadow-[0_5px_16px_rgba(72,53,24,.04)]
                        "
                    >

                        <svg
                            viewBox="0 0 24 24"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="M20 12a2 2 0 0 0 0-4h-2.2a3.8 3.8 0 0 0-5.8-3 3.8 3.8 0 0 0-5.8 3H4a2 2 0 0 0 0 4h16Z"></path>
                            <path d="M12 5v15"></path>
                            <path d="M4 12h16v7H4z"></path>
                        </svg>

                    </div>


                    <p
                        class="
                            mt-4
                            text-[11px]
                            font-semibold
                            text-[#514a42]
                        "
                    >
                        No reward activity yet
                    </p>


                    <p
                        class="
                            mx-auto
                            mt-1.5
                            max-w-[420px]

                            text-[8.5px]
                            leading-5
                            text-[#91887d]
                        "
                    >
                        Complete your first successful delivery to start earning SARI reward points.
                    </p>

                </div>

            @endforelse

        </div>

    </section>

</div>

@endsection