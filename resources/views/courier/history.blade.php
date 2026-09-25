@extends('layouts.courier')

@section('title', 'Delivery History')
@section('header-title', 'Delivery History')
@section('header-subtitle', 'Completed Deliveries')

@push('styles')
<style>
    .history-card {
        border: 1px solid #eee4d3;
        background: #fffdf9;
        box-shadow: 0 8px 24px rgba(75, 59, 30, .035);
    }

    .history-card-hover {
        transition:
            transform .22s ease,
            border-color .22s ease,
            box-shadow .22s ease;
    }

    .history-card-hover:hover {
        transform: translateY(-2px);
        border-color: #dbc89f;
        box-shadow: 0 14px 32px rgba(75, 59, 30, .07);
    }

    .history-grid {
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

                    Delivery Records
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
                        <path d="M3 12a9 9 0 1 0 3-6.7"></path>
                        <path d="M3 3v6h6"></path>
                        <path d="M12 7v5l3 2"></path>
                    </svg>

                    {{ count($history) }}
                    {{ count($history) === 1 ? 'record' : 'records' }}
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
                Your delivery activity
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
                Review your previous delivery routes, completion times,
                statuses, and earned courier fees.
            </p>

        </div>


        <div class="flex flex-wrap gap-2">

            {{-- DATE FILTER --}}
            <div class="relative">

                <svg
                    viewBox="0 0 24 24"
                    class="
                        pointer-events-none
                        absolute left-3 top-1/2
                        h-3.5 w-3.5
                        -translate-y-1/2
                        text-[#a09687]
                    "
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


                <select
                    id="historyFilter"
                    class="
                        h-10
                        appearance-none
                        rounded-xl
                        border border-[#e6dccb]
                        bg-white
                        pl-9 pr-9
                        text-[9px] font-semibold
                        text-[#62594d]
                        outline-none
                        transition
                        hover:border-[#d9be8c]
                        focus:border-[#d9930a]
                    "
                >
                    <option value="all">
                        All deliveries
                    </option>

                    <option value="today">
                        Today
                    </option>

                    <option value="previous">
                        Previous days
                    </option>
                </select>


                <svg
                    viewBox="0 0 24 24"
                    class="
                        pointer-events-none
                        absolute right-3 top-1/2
                        h-3 w-3
                        -translate-y-1/2
                        text-[#9b9183]
                    "
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m7 10 5 5 5-5"></path>
                </svg>

            </div>


            {{-- EXPORT --}}
            <a
                href="{{ route('courier.history.export') }}"
                class="
                    inline-flex min-h-10
                    items-center justify-center gap-2
                    rounded-xl
                    border border-[#e6dccb]
                    bg-white
                    px-4 py-2.5
                    text-[9px] font-semibold
                    text-[#62594d]
                    shadow-sm
                    transition
                    hover:border-[#d9be8c]
                    hover:bg-[#fffaf1]
                    hover:text-[#a66d08]
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

                Export
            </a>

        </div>

    </section>


    {{-- =========================================================
         KPI CARDS
    ========================================================== --}}
    <section
        class="
            mt-5
            grid gap-3
            sm:grid-cols-2
            xl:grid-cols-4
        "
    >

        {{-- COMPLETED TODAY --}}
        <article
            class="
                reveal history-card history-card-hover
                relative overflow-hidden
                rounded-2xl
                p-4 sm:p-5
            "
        >
            <div
                class="
                    absolute inset-x-0 top-0
                    h-[3px]
                    bg-[#d9930a]
                "
            ></div>


            <div class="flex items-start justify-between">

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
                        <path d="m9 15 2 2 4-4"></path>
                    </svg>
                </div>


                <span
                    class="
                        mt-1 h-2 w-2
                        rounded-full
                        bg-[#d9930a]
                    "
                ></span>

            </div>


            <p
                class="
                    mt-4
                    text-[8px] font-medium
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
                {{ $historyStats['today'] }}
            </p>


            <p
                class="
                    mt-1
                    text-[7px]
                    text-[#a09587]
                "
            >
                Successful trips today
            </p>

        </article>


        {{-- TOTAL COMPLETED --}}
        <article
            class="
                reveal history-card history-card-hover
                relative overflow-hidden
                rounded-2xl
                p-4 sm:p-5
            "
        >
            <div
                class="
                    absolute inset-x-0 top-0
                    h-[3px]
                    bg-[#c58c1c]
                "
            ></div>


            <div class="flex items-start justify-between">

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
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="m7.5 12 3 3 6-7"></path>
                    </svg>
                </div>


                <span
                    class="
                        mt-1 h-2 w-2
                        rounded-full
                        bg-[#c58c1c]
                    "
                ></span>

            </div>


            <p
                class="
                    mt-4
                    text-[8px] font-medium
                    text-[#817769]
                "
            >
                Total Completed
            </p>


            <p
                class="
                    mt-1
                    text-[22px] font-bold
                    tracking-[-.04em]
                    text-[#211d17]
                "
            >
                {{ $historyStats['total'] }}
            </p>


            <p
                class="
                    mt-1
                    text-[7px]
                    text-[#a09587]
                "
            >
                Completed since joining
            </p>

        </article>


        {{-- SUCCESS RATE --}}
        <article
            class="
                reveal history-card history-card-hover
                relative overflow-hidden
                rounded-2xl
                p-4 sm:p-5
            "
        >
            <div
                class="
                    absolute inset-x-0 top-0
                    h-[3px]
                    bg-[#4F7D63]
                "
            ></div>


            <div class="flex items-start justify-between">

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
                        <path d="M4 19V9"></path>
                        <path d="M10 19V5"></path>
                        <path d="M16 19V12"></path>
                        <path d="M22 19H2"></path>
                        <path d="m6 9 4-4 3 3 5-5"></path>
                    </svg>
                </div>


                <span
                    class="
                        mt-1 h-2 w-2
                        rounded-full
                        bg-[#4F7D63]
                    "
                ></span>

            </div>


            <p
                class="
                    mt-4
                    text-[8px] font-medium
                    text-[#817769]
                "
            >
                Success Rate
            </p>


            <p
                class="
                    mt-1
                    text-[22px] font-bold
                    tracking-[-.04em]
                    text-[#211d17]
                "
            >
                {{ number_format($historyStats['success_rate'], 1) }}%
            </p>


            <p
                class="
                    mt-1
                    text-[7px]
                    text-[#a09587]
                "
            >
                Overall completion rate
            </p>

        </article>


        {{-- AVERAGE FEE --}}
        <article
            class="
                reveal history-card history-card-hover
                relative overflow-hidden
                rounded-2xl
                p-4 sm:p-5
            "
        >
            <div
                class="
                    absolute inset-x-0 top-0
                    h-[3px]
                    bg-[#b79a67]
                "
            ></div>


            <div class="flex items-start justify-between">

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


                <span
                    class="
                        mt-1 h-2 w-2
                        rounded-full
                        bg-[#b79a67]
                    "
                ></span>

            </div>


            <p
                class="
                    mt-4
                    text-[8px] font-medium
                    text-[#817769]
                "
            >
                Average Fee
            </p>


            <p
                class="
                    mt-1
                    text-[22px] font-bold
                    tracking-[-.04em]
                    text-[#211d17]
                "
            >
                ₱{{ number_format($historyStats['average_fee'], 2) }}
            </p>


            <p
                class="
                    mt-1
                    text-[7px]
                    text-[#a09587]
                "
            >
                Per completed delivery
            </p>

        </article>

    </section>


    {{-- =========================================================
         DELIVERY RECORDS
    ========================================================== --}}
    <section
        class="
            reveal history-card
            mt-5 overflow-hidden
            rounded-[20px]
        "
    >

        {{-- TABLE HEADER --}}
        <div
            class="
                flex flex-col gap-4
                border-b border-[#eee4d3]
                px-4 py-4
                sm:px-5
                lg:flex-row
                lg:items-center
                lg:justify-between
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
                        <path d="M3 12a9 9 0 1 0 3-6.7"></path>
                        <path d="M3 3v6h6"></path>
                        <path d="M12 7v5l3 2"></path>
                    </svg>
                </div>


                <div>

                    <h3
                        class="
                            text-[12px] font-bold
                            text-[#211d17]
                        "
                    >
                        Delivery Records
                    </h3>

                    <p
                        class="
                            mt-1
                            text-[8px]
                            text-[#918677]
                        "
                    >
                        Your most recent completed and cancelled deliveries.
                    </p>

                </div>

            </div>


            {{-- SEARCH --}}
            <div
                class="
                    relative
                    w-full
                    lg:w-[310px]
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="
                        pointer-events-none
                        absolute left-3 top-1/2
                        h-4 w-4
                        -translate-y-1/2
                        text-[#a09687]
                    "
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.5-3.5"></path>
                </svg>


                <input
                    id="historySearch"
                    type="search"
                    placeholder="Search order, customer, route..."
                    class="
                        h-10 w-full
                        rounded-xl
                        border border-[#e5dac9]
                        bg-white
                        pl-9 pr-3
                        text-[9px]
                        text-[#51483d]
                        outline-none
                        transition
                        placeholder:text-[#aaa093]
                        hover:border-[#dbc9aa]
                        focus:border-[#d9930a]
                        focus:ring-2
                        focus:ring-[#d9930a]/10
                    "
                >
            </div>

        </div>


        @if (count($history) > 0)

            {{-- =================================================
                 TABLE
            ================================================== --}}
            <div class="overflow-x-auto">

                <table class="w-full min-w-[900px]">

                    <thead class="bg-[#fbf8f1]">

                        <tr class="text-left">

                            <th
                                class="
                                    px-5 py-3
                                    text-[7px] font-bold uppercase
                                    tracking-[.11em]
                                    text-[#9c9182]
                                "
                            >
                                Order
                            </th>

                            <th
                                class="
                                    px-5 py-3
                                    text-[7px] font-bold uppercase
                                    tracking-[.11em]
                                    text-[#9c9182]
                                "
                            >
                                Customer
                            </th>

                            <th
                                class="
                                    px-5 py-3
                                    text-[7px] font-bold uppercase
                                    tracking-[.11em]
                                    text-[#9c9182]
                                "
                            >
                                Route
                            </th>

                            <th
                                class="
                                    px-5 py-3
                                    text-[7px] font-bold uppercase
                                    tracking-[.11em]
                                    text-[#9c9182]
                                "
                            >
                                Date
                            </th>

                            <th
                                class="
                                    px-5 py-3
                                    text-[7px] font-bold uppercase
                                    tracking-[.11em]
                                    text-[#9c9182]
                                "
                            >
                                Time
                            </th>

                            <th
                                class="
                                    px-5 py-3
                                    text-[7px] font-bold uppercase
                                    tracking-[.11em]
                                    text-[#9c9182]
                                "
                            >
                                Fee
                            </th>

                            <th
                                class="
                                    px-5 py-3
                                    text-[7px] font-bold uppercase
                                    tracking-[.11em]
                                    text-[#9c9182]
                                "
                            >
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody
                        id="historyRows"
                        class="divide-y divide-[#f0e8db]"
                    >

                        @foreach ($history as $row)

                            <tr
                                class="
                                    history-row
                                    transition-colors
                                    hover:bg-[#fffaf1]
                                "
                                data-search="{{ strtolower(
                                    $row['order'].' '.
                                    $row['customer'].' '.
                                    $row['route']
                                ) }}"
                                data-day="{{ $row['is_today']
                                    ? 'today'
                                    : 'previous'
                                }}"
                            >

                                {{-- ORDER --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="
                                                grid h-8 w-8
                                                shrink-0 place-items-center
                                                rounded-lg
                                                bg-[#fff2d8]
                                                text-[#b77900]
                                            "
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                class="h-3.5 w-3.5"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path d="M4 7h16v11H4z"></path>
                                                <path d="m4 7 3-3h10l3 3"></path>
                                            </svg>
                                        </div>


                                        <span
                                            class="
                                                whitespace-nowrap
                                                text-[9px] font-semibold
                                                text-[#302a23]
                                            "
                                        >
                                            {{ $row['order'] }}
                                        </span>

                                    </div>

                                </td>


                                {{-- CUSTOMER --}}
                                <td
                                    class="
                                        px-5 py-4
                                        text-[8px]
                                        text-[#746a5e]
                                    "
                                >
                                    <div class="flex items-center gap-2">

                                        <svg
                                            viewBox="0 0 24 24"
                                            class="
                                                h-3.5 w-3.5
                                                shrink-0
                                                text-[#aaa093]
                                            "
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <circle cx="12" cy="8" r="4"></circle>
                                            <path d="M4 21a8 8 0 0 1 16 0"></path>
                                        </svg>

                                        <span>
                                            {{ $row['customer'] }}
                                        </span>

                                    </div>
                                </td>


                                {{-- ROUTE --}}
                                <td
                                    class="
                                        max-w-[250px]
                                        px-5 py-4
                                        text-[8px]
                                        text-[#746a5e]
                                    "
                                >
                                    <div
                                        class="
                                            flex items-center gap-2
                                        "
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            class="
                                                h-3.5 w-3.5
                                                shrink-0
                                                text-[#aaa093]
                                            "
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <path d="M5 6h7"></path>
                                            <path d="m9 3 3 3-3 3"></path>
                                            <path d="M19 18h-7"></path>
                                            <path d="m15 15-3 3 3 3"></path>
                                        </svg>

                                        <span class="leading-4">
                                            {{ $row['route'] }}
                                        </span>

                                    </div>
                                </td>


                                {{-- DATE --}}
                                <td
                                    class="
                                        whitespace-nowrap
                                        px-5 py-4
                                        text-[8px]
                                        text-[#81776a]
                                    "
                                >
                                    {{ $row['date'] }}
                                </td>


                                {{-- TIME --}}
                                <td
                                    class="
                                        whitespace-nowrap
                                        px-5 py-4
                                        text-[8px]
                                        text-[#81776a]
                                    "
                                >
                                    {{ $row['time'] }}
                                </td>


                                {{-- FEE --}}
                                <td
                                    class="
                                        whitespace-nowrap
                                        px-5 py-4
                                        text-[9px] font-bold
                                        text-[#302a23]
                                    "
                                >
                                    ₱{{ number_format(
                                        $row['fee'],
                                        2
                                    ) }}
                                </td>


                                {{-- STATUS --}}
                                <td class="px-5 py-4">

                                    @if ($row['status'] === 'Completed')

                                        <span
                                            class="
                                                inline-flex
                                                items-center gap-1.5
                                                rounded-full
                                                border border-emerald-200
                                                bg-emerald-50
                                                px-2.5 py-1
                                                text-[7px] font-semibold
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

                                            {{ $row['status'] }}
                                        </span>

                                    @else

                                        <span
                                            class="
                                                inline-flex
                                                items-center gap-1.5
                                                rounded-full
                                                border border-[#ead8cf]
                                                bg-[#f9f1ed]
                                                px-2.5 py-1
                                                text-[7px] font-semibold
                                                text-[#966552]
                                            "
                                        >
                                            <span
                                                class="
                                                    h-1.5 w-1.5
                                                    rounded-full
                                                    bg-[#ad725c]
                                                "
                                            ></span>

                                            {{ $row['status'] }}
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach


                        {{-- =========================================
                             NO SEARCH/FILTER RESULTS
                        ========================================== --}}
                        <tr
                            id="historyNoResults"
                            class="hidden"
                        >
                            <td
                                colspan="7"
                                class="
                                    px-5 py-10
                                    text-center
                                "
                            >

                                <div
                                    class="
                                        mx-auto
                                        grid h-11 w-11
                                        place-items-center
                                        rounded-xl
                                        bg-[#fff2d8]
                                        text-[#b77900]
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <circle cx="11" cy="11" r="7"></circle>
                                        <path d="m20 20-3.5-3.5"></path>
                                    </svg>
                                </div>


                                <p
                                    class="
                                        mt-3
                                        text-[9px] font-semibold
                                        text-[#51483d]
                                    "
                                >
                                    No matching delivery records
                                </p>


                                <p
                                    class="
                                        mt-1
                                        text-[7px]
                                        text-[#918677]
                                    "
                                >
                                    Try another search term or delivery date filter.
                                </p>

                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>


        @else

            {{-- =================================================
                 EMPTY HISTORY
            ================================================== --}}
            <div
                class="
                    history-grid
                    px-5 py-10
                "
            >

                <div
                    class="
                        mx-auto
                        flex min-h-[250px]
                        max-w-[440px]
                        flex-col
                        items-center
                        justify-center
                        text-center
                    "
                >

                    <div
                        class="
                            relative
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
                            <path d="M3 12a9 9 0 1 0 3-6.7"></path>
                            <path d="M3 3v6h6"></path>
                            <path d="M12 7v5l3 2"></path>
                        </svg>


                        <span
                            class="
                                absolute -right-1 -top-1
                                grid h-5 w-5
                                place-items-center
                                rounded-full
                                border-[3px] border-[#fffdf9]
                                bg-[#d9930a]
                                text-white
                            "
                        >
                            <span
                                class="
                                    h-1.5 w-1.5
                                    rounded-full
                                    bg-white
                                "
                            ></span>
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
                        No delivery history yet
                    </h3>


                    <p
                        class="
                            mt-2
                            max-w-[350px]
                            text-[8px] leading-5
                            text-[#918677]
                        "
                    >
                        Completed and cancelled delivery records will
                        appear here once you begin processing rider
                        assignments.
                    </p>


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
                            shadow-[0_8px_18px_rgba(217,147,10,.15)]
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

        @endif

    </section>

</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('historySearch');
    const filter = document.getElementById('historyFilter');

    const rows = [
        ...document.querySelectorAll('.history-row')
    ];

    const noResults = document.getElementById(
        'historyNoResults'
    );


    const applyFilters = () => {
        const query = (
            search?.value || ''
        ).trim().toLowerCase();

        const selected = filter?.value || 'all';

        let visibleCount = 0;


        rows.forEach(row => {
            const searchMatch =
                row.dataset.search.includes(query);

            const dayMatch =
                selected === 'all' ||
                row.dataset.day === selected;

            const visible =
                searchMatch &&
                dayMatch;


            row.classList.toggle(
                'hidden',
                !visible
            );


            if (visible) {
                visibleCount++;
            }
        });


        if (noResults) {
            noResults.classList.toggle(
                'hidden',
                visibleCount > 0
            );
        }
    };


    search?.addEventListener(
        'input',
        applyFilters
    );


    filter?.addEventListener(
        'change',
        applyFilters
    );
});
</script>
@endpush