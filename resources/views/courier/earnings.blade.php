@extends('layouts.courier')

@section('title', 'Rider Earnings')
@section('header-title', 'Rider Earnings')
@section('header-subtitle', 'Delivery Fee Ledger')


@push('styles')

<style>
    .rider-earning-card {
        border: 1px solid #eee4d3;
        background: #fffdf9;

        box-shadow:
            0 8px 24px
            rgba(75, 59, 30, .035);
    }

    .rider-earning-hover {
        transition:
            transform .22s ease,
            border-color .22s ease,
            box-shadow .22s ease;
    }

    .rider-earning-hover:hover {
        transform: translateY(-2px);
        border-color: #dbc89f;

        box-shadow:
            0 14px 32px
            rgba(75, 59, 30, .07);
    }
</style>

@endpush


@section('content')

<div class="mx-auto max-w-[1620px]">

    @if(session('success'))

        <div
            class="
                mb-4
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
                mb-4
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
        HEADER
    ====================================================== --}}

    <section
        class="
            reveal
            mb-5
            flex flex-col
            justify-between
            gap-4
            border-b border-[#eee4d3]
            pb-5
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
                Rider Ledger
            </span>


            <h2
                class="
                    mt-3
                    text-[15px]
                    font-bold
                    text-[#211d17]
                "
            >
                Rider Earnings
            </h2>


            <p
                class="
                    mt-2
                    max-w-[720px]
                    text-[10px]
                    leading-5
                    text-[#817769]
                "
            >
                Track your direct delivery, Seller-to-Hub pickup,
                and Hub-to-Buyer last-mile earnings. Logistics-managed
                shipping is split by the configured Rider shares.
            </p>

        </div>


        <a href="{{ route('courier.remittances.index') }}" class="rounded-xl border border-[#e6dccb] bg-[#fff8e9] px-4 py-2.5 text-[9px] font-semibold text-[#a96e05]">COD Remittance</a>

        <a
            href="{{
                route(
                    'courier.earnings.statement'
                )
            }}"
            class="
                rounded-xl
                border border-[#e6dccb]
                bg-white
                px-4 py-2.5
                text-[9px]
                font-semibold
                text-[#62594d]
            "
        >
            Download Statement
        </a>

    </section>


    {{-- =====================================================
        SUMMARY
    ====================================================== --}}

    <section
        class="
            grid
            gap-3
            sm:grid-cols-2
            xl:grid-cols-5
        "
    >

        @foreach([
            [
                'Today',
                '₱'.number_format(
                    $earnings['today'],
                    2
                ),
                '#fff2d8',
                '#b77900',
            ],
            [
                'This Week',
                '₱'.number_format(
                    $earnings['week'],
                    2
                ),
                '#eaf2f7',
                '#3C6E91',
            ],
            [
                'This Month',
                '₱'.number_format(
                    $earnings['month'],
                    2
                ),
                '#eaf3ed',
                '#4F7D63',
            ],
            [
                'Potential',
                '₱'.number_format(
                    $earnings['pending'],
                    2
                ),
                '#f5f0e7',
                '#8a6940',
            ],
            [
                'Available',
                '₱'.number_format(
                    $earnings['available'],
                    2
                ),
                '#ecf5ee',
                '#437356',
            ],
        ] as [
            $label,
            $value,
            $bg,
            $color
        ])

            <article
                class="
                    reveal
                    rider-earning-card
                    rider-earning-hover
                    rounded-2xl
                    p-4
                "
            >

                <div
                    class="
                        mb-4
                        h-2 w-2
                        rounded-full
                    "
                    style="
                        background:
                        {{ $color }}
                    "
                ></div>


                <p
                    class="
                        text-[8px]
                        text-[#887e70]
                    "
                >
                    {{ $label }}
                </p>


                <p
                    class="
                        mt-2
                        text-[20px]
                        font-bold
                        tracking-[-.035em]
                        text-[#211d17]
                    "
                >
                    {{ $value }}
                </p>

            </article>

        @endforeach

    </section>


    <div
        class="
            mt-3
            rounded-xl
            border border-[#e9dfcf]
            bg-[#fffaf1]
            px-4 py-3
        "
    >

        <p
            class="
                text-[8px]
                leading-4
                text-[#887760]
            "
        >
            <strong>
                Potential earnings
            </strong>
            represent active direct-delivery or Last-Mile
            work that has not yet completed. Earnings become
            available only after successful delivery and
            finance recognition.
        </p>

    </div>


    <section class="mt-4 grid gap-3 sm:grid-cols-3">
        <article class="rider-earning-card rounded-2xl p-4"><p class="text-[8px] text-[#887e70]">Awaiting COD Remittance</p><p class="mt-2 text-[18px] font-bold text-[#211d17]">₱{{ number_format($earnings['awaiting_remittance'],2) }}</p><p class="mt-1 text-[7px] text-[#918677]">Earned but locked until Rider cash remittance is verified.</p></article>
        <article class="rider-earning-card rounded-2xl p-4"><p class="text-[8px] text-[#887e70]">Reserved for Payout</p><p class="mt-2 text-[18px] font-bold text-[#211d17]">₱{{ number_format($earnings['reserved'],2) }}</p><p class="mt-1 text-[7px] text-[#918677]">Already included in a withdrawal request.</p></article>
        <article class="rider-earning-card rounded-2xl p-4"><p class="text-[8px] text-[#887e70]">Lifetime Paid Out</p><p class="mt-2 text-[18px] font-bold text-[#211d17]">₱{{ number_format($earnings['paid'],2) }}</p><p class="mt-1 text-[7px] text-[#918677]">Confirmed external payouts.</p></article>
    </section>

    {{-- =====================================================
        PERFORMANCE / PAYOUT
    ====================================================== --}}

    <section
        class="
            mt-5
            grid
            gap-5
            xl:grid-cols-[minmax(0,1.4fr)_minmax(320px,.6fr)]
        "
    >

        <div
            class="
                reveal
                rider-earning-card
                rounded-2xl
                p-5
            "
        >

            <div
                class="
                    flex
                    items-center
                    justify-between
                    gap-3
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
                        Weekly Performance
                    </h3>


                    <p
                        class="
                            mt-1
                            text-[8px]
                            text-[#918677]
                        "
                    >
                        Recognized Rider earnings for
                        the current week.
                    </p>

                </div>


                <span
                    class="
                        rounded-full
                        border border-emerald-200
                        bg-emerald-50
                        px-2.5 py-1
                        text-[7px]
                        font-semibold
                        text-emerald-700
                    "
                >
                    {{
                        $weeklyChange === null
                            ? 'No prior week'
                            : (
                                (
                                    $weeklyChange >= 0
                                        ? '+'
                                        : ''
                                )
                                .
                                number_format(
                                    $weeklyChange,
                                    1
                                )
                                .
                                '%'
                            )
                    }}
                </span>

            </div>


            @php
                $maxAmount =
                    max(
                        1,
                        max(
                            array_column(
                                $daily,
                                'amount'
                            )
                        )
                    );
            @endphp


            <div
                class="
                    mt-8
                    grid h-[220px]
                    grid-cols-7
                    items-end
                    gap-2
                    sm:gap-4
                "
            >

                @foreach($daily as $day)

                    @php
                        $height =
                            max(
                                20,
                                round(
                                    (
                                        $day['amount']
                                        /
                                        $maxAmount
                                    )
                                    *
                                    180
                                )
                            );
                    @endphp


                    <div
                        class="
                            flex h-full
                            flex-col
                            justify-end
                        "
                    >

                        <div
                            class="
                                mb-2
                                text-center
                                text-[7px]
                                font-semibold
                                text-[#645b4f]
                            "
                        >
                            ₱{{ number_format(
                                $day['amount'],
                                2
                            ) }}
                        </div>


                        <div
                            class="
                                mx-auto
                                w-full
                                max-w-[42px]
                                rounded-t-xl
                                bg-[#3C6E91]/90
                                transition
                                hover:bg-[#d9930a]
                            "
                            style="
                                height:
                                {{ $height }}px
                            "
                        ></div>


                        <p
                            class="
                                mt-2
                                text-center
                                text-[8px]
                                font-semibold
                                text-[#857b6d]
                            "
                        >
                            {{ $day['day'] }}
                        </p>

                    </div>

                @endforeach

            </div>

        </div>


        <aside
            class="
                reveal
                rider-earning-card
                rounded-2xl
                p-5
            "
        >

            <h3
                class="
                    text-[12px]
                    font-bold
                    text-[#211d17]
                "
            >
                Payout Summary
            </h3>


            <p
                class="
                    mt-1
                    text-[8px]
                    text-[#918677]
                "
            >
                Current Rider ledger balance.
            </p>


            <div
                class="
                    mt-5
                    rounded-2xl
                    bg-[#3C6E91]
                    p-5
                    text-white
                "
            >

                <p
                    class="
                        text-[8px]
                        uppercase
                        tracking-[.12em]
                        text-white/60
                    "
                >
                    Available Balance
                </p>


                <p
                    class="
                        mt-2
                        text-[25px]
                        font-bold
                    "
                >
                    ₱{{
                        number_format(
                            $earnings['available'],
                            2
                        )
                    }}
                </p>


                <p
                    class="
                        mt-2
                        text-[8px]
                        leading-4
                        text-white/70
                    "
                >
                    Available means earned in the internal
                    Rider ledger. It does not yet indicate
                    an external payout.
                </p>

            </div>


            <form method="POST" action="{{ route('courier.earnings.payout') }}" class="mt-4 space-y-3">
                @csrf
                <input name="amount" type="number" min="1" step="0.01" max="{{ $earnings['available'] }}" required placeholder="Amount" class="w-full rounded-xl border border-[#e6dccb] bg-white px-3 py-3 text-[9px]">
                <select name="payout_method" required class="w-full rounded-xl border border-[#e6dccb] bg-white px-3 py-3 text-[9px]"><option value="gcash">GCash</option><option value="maya">Maya</option><option value="bank">Bank Transfer</option><option value="manual_cash">Manual Cash Release</option></select>
                <input name="account_name" required minlength="2" maxlength="120" placeholder="Account name" class="w-full rounded-xl border border-[#e6dccb] bg-white px-3 py-3 text-[9px]">
                <input name="account_reference" required minlength="4" maxlength="120" placeholder="Mobile / bank account" class="w-full rounded-xl border border-[#e6dccb] bg-white px-3 py-3 text-[9px]">
                <button class="w-full rounded-xl bg-[#d9930a] px-4 py-3 text-[9px] font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50" @disabled($earnings['available'] <= 0)>Request Withdrawal</button>
            </form>


            <div class="mt-3 space-y-2">

                @forelse(
                    $payoutRequests
                    as $payout
                )

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            rounded-xl
                            border border-[#eee4d3]
                            bg-white
                            px-3 py-2
                            text-[7px]
                        "
                    >

                        <span>
                            ₱{{
                                number_format(
                                    (float)
                                    $payout->amount,
                                    2
                                )
                            }}
                        </span>


                        <strong>
                            {{
                                strtoupper(
                                    $payout->status
                                )
                            }}
                        </strong>

                    </div>

                @empty

                    <div
                        class="
                            rounded-xl
                            border border-dashed
                            border-[#e5dccf]
                            px-3 py-4
                            text-center
                            text-[7px]
                            text-[#918677]
                        "
                    >
                        No payout requests yet.
                    </div>

                @endforelse

            </div>

        </aside>

    </section>


    {{-- =====================================================
        RECENT EARNINGS
    ====================================================== --}}

    <section
        class="
            reveal
            rider-earning-card
            mt-5
            overflow-hidden
            rounded-2xl
        "
    >

        <div
            class="
                border-b border-[#eee4d3]
                px-5 py-4
            "
        >

            <h3
                class="
                    text-[12px]
                    font-bold
                    text-[#211d17]
                "
            >
                Recent Earnings
            </h3>


            <p
                class="
                    mt-1
                    text-[8px]
                    text-[#918677]
                "
            >
                Delivery fees recognized for this Rider.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table
                class="
                    w-full
                    min-w-[680px]
                "
            >

                <thead class="bg-[#fbf8f1]">

                    <tr class="text-left">

                        <th
                            class="
                                px-5 py-3
                                text-[7px]
                                uppercase
                                tracking-[.11em]
                                text-[#9c9182]
                            "
                        >
                            Order
                        </th>

                        <th
                            class="
                                px-5 py-3
                                text-[7px]
                                uppercase
                                tracking-[.11em]
                                text-[#9c9182]
                            "
                        >
                            Earned At
                        </th>

                        <th
                            class="
                                px-5 py-3
                                text-[7px]
                                uppercase
                                tracking-[.11em]
                                text-[#9c9182]
                            "
                        >
                            Type
                        </th>

                        <th
                            class="
                                px-5 py-3
                                text-[7px]
                                uppercase
                                tracking-[.11em]
                                text-[#9c9182]
                            "
                        >
                            Amount
                        </th>

                        <th
                            class="
                                px-5 py-3
                                text-[7px]
                                uppercase
                                tracking-[.11em]
                                text-[#9c9182]
                            "
                        >
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-[#f0e8db]">

                    @forelse(
                        $transactions
                        as $transaction
                    )

                        <tr class="hover:bg-[#fffaf1]">

                            <td
                                class="
                                    px-5 py-3.5
                                    text-[9px]
                                    font-semibold
                                    text-[#302a23]
                                "
                            >
                                {{ $transaction['order'] }}
                            </td>


                            <td
                                class="
                                    px-5 py-3.5
                                    text-[8px]
                                    text-[#81776a]
                                "
                            >
                                {{ $transaction['date'] }}
                            </td>


                            <td
                                class="
                                    px-5 py-3.5
                                    text-[8px]
                                    text-[#81776a]
                                "
                            >
                                {{ $transaction['type'] }}
                            </td>


                            <td
                                class="
                                    px-5 py-3.5
                                    text-[9px]
                                    font-semibold
                                    text-[#302a23]
                                "
                            >
                                +₱{{
                                    number_format(
                                        $transaction['amount'],
                                        2
                                    )
                                }}
                            </td>


                            <td class="px-5 py-3.5">

                                <span
                                    class="
                                        rounded-full
                                        border border-emerald-200
                                        bg-emerald-50
                                        px-2.5 py-1
                                        text-[7px]
                                        font-semibold
                                        text-emerald-700
                                    "
                                >
                                    {{ $transaction['status'] }}
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="5"
                                class="
                                    px-5 py-12
                                    text-center
                                    text-[8px]
                                    text-[#918677]
                                "
                            >
                                No recognized Rider earnings yet.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>

@endsection