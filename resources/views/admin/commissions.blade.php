@extends('layouts.admin')

@section('title', 'Commission Management — SARI Admin')
@section('page-title', 'Commission Management')

@section('content')

<div class="mx-auto w-full max-w-[1800px]">

    {{-- =========================================================
        PAGE INTRO
    ========================================================== --}}
    <section
        class="
            rounded-[22px]
            border border-[#ebe4da]
            bg-white
            p-5

            sm:p-6
            lg:p-7
        "
    >
        <div
            class="
                flex flex-col gap-5
                lg:flex-row
                lg:items-center
                lg:justify-between
            "
        >

            <div>
                <div
                    class="
                        inline-flex items-center gap-2
                        rounded-full
                        border border-[#e8dfd1]
                        bg-[#fcfaf6]
                        px-3 py-1.5
                        text-[9px] font-semibold
                        uppercase tracking-[0.14em]
                        text-[#a27428]
                    "
                >
                    <span class="h-2 w-2 rounded-full bg-[#c9952f]"></span>
                    Platform Revenue
                </div>

                <h2
                    class="
                        mt-3
                        text-[22px] font-bold
                        tracking-[-0.03em]
                        text-[#211c16]

                        sm:text-[24px]
                    "
                >
                    Commission Management
                </h2>

                <p
                    class="
                        mt-2
                        max-w-[720px]
                        text-[12px] leading-6
                        text-[#81786c]

                        sm:text-[13px]
                    "
                >
                    Monitor the 10% SARI platform commission, review seller
                    transactions, and track commission performance across the marketplace.
                </p>
            </div>


            <div class="flex flex-wrap items-center gap-3">

                <button
                    type="button"
                    class="
                        inline-flex items-center gap-2
                        rounded-xl
                        border border-[#e6dfd4]
                        bg-white
                        px-4 py-2.5
                        text-[11px] font-semibold
                        text-[#62594e]
                        transition

                        hover:border-[#d4c29f]
                        hover:bg-[#fcf9f3]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M4 4h16v16H4z"></path>
                        <path d="M8 10h8"></path>
                        <path d="M8 14h5"></path>
                    </svg>

                    Export Report
                </button>


                <button
                    type="button"
                    class="
                        inline-flex items-center gap-2
                        rounded-xl
                        bg-[#c99128]
                        px-4 py-2.5
                        text-[11px] font-semibold
                        text-white
                        shadow-[0_8px_20px_rgba(201,145,40,0.16)]
                        transition

                        hover:bg-[#b47e1e]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M4 12h16"></path>
                        <path d="M12 4v16"></path>
                    </svg>

                    New Adjustment
                </button>

            </div>

        </div>
    </section>


    {{-- =========================================================
        SUMMARY CARDS
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-4

            sm:grid-cols-2
            xl:grid-cols-4
        "
    >

        {{-- TOTAL COMMISSION --}}
        <div
            class="
                rounded-[18px]
                border border-[#eadfc9]
                bg-white
                p-5
            "
        >
            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Total Commission
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[26px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        ₱245,680
                    </h3>

                    <p class="mt-2 text-[10px] font-medium text-[#56816a]">
                        ▲ 18.7% this month
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        shrink-0 place-items-center
                        rounded-xl
                        border border-[#eadfc8]
                        bg-[#fbf6ec]
                        text-[#b98020]
                    "
                >
                    <span class="text-[18px] font-semibold">₱</span>
                </div>

            </div>
        </div>


        {{-- GROSS SALES --}}
        <div
            class="
                rounded-[18px]
                border border-[#dce5ed]
                bg-white
                p-5
            "
        >
            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Gross Marketplace Sales
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[26px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        ₱2.45M
                    </h3>

                    <p class="mt-2 text-[10px] text-[#8f877d]">
                        Before commission deduction
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        shrink-0 place-items-center
                        rounded-xl
                        border border-[#d9e3ec]
                        bg-[#f3f7fa]
                        text-[#627f99]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <path d="M4 20h16"></path>
                        <path d="M7 17v-4"></path>
                        <path d="M12 17V9"></path>
                        <path d="M17 17V5"></path>
                    </svg>
                </div>

            </div>
        </div>


        {{-- PENDING --}}
        <div
            class="
                rounded-[18px]
                border border-[#e9e1d2]
                bg-white
                p-5
            "
        >
            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Pending Commission
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[26px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        ₱38,420
                    </h3>

                    <p class="mt-2 text-[10px] text-[#9a9186]">
                        42 transactions
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        shrink-0 place-items-center
                        rounded-xl
                        border border-[#e8dfcf]
                        bg-[#fbf6ed]
                        text-[#ae7a21]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <circle cx="12" cy="12" r="8"></circle>
                        <path d="M12 8v4l2 2"></path>
                    </svg>
                </div>

            </div>
        </div>


        {{-- COMMISSION RATE --}}
        <div
            class="
                rounded-[18px]
                border border-[#d9e6df]
                bg-white
                p-5
            "
        >
            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Platform Rate
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[26px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        10%
                    </h3>

                    <p class="mt-2 text-[10px] text-[#8f877d]">
                        Current commission rate
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        shrink-0 place-items-center
                        rounded-xl
                        border border-[#d6e5dc]
                        bg-[#f1f7f3]
                        text-[#56816a]
                    "
                >
                    <span class="text-[16px] font-semibold">%</span>
                </div>

            </div>
        </div>

    </section>


    {{-- =========================================================
        ANALYTICS + RATE CARD
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-5

            xl:grid-cols-[1.45fr_.55fr]
        "
    >

        {{-- COMMISSION TREND --}}
        <div
            class="
                rounded-[22px]
                border border-[#ebe4da]
                bg-white
                p-5

                sm:p-6
            "
        >

            <div
                class="
                    flex flex-col gap-3
                    sm:flex-row
                    sm:items-start
                    sm:justify-between
                "
            >

                <div>
                    <h3 class="text-[16px] font-bold text-[#28221b]">
                        Commission Trend
                    </h3>

                    <p class="mt-1 text-[10px] text-[#91887d]">
                        Platform commission performance for the current period.
                    </p>
                </div>

                <select
                    class="
                        h-10
                        rounded-xl
                        border border-[#e6dfd5]
                        bg-[#fcfbf9]
                        px-3
                        text-[10px]
                        text-[#625a50]
                        outline-none
                        focus:border-[#c99a3d]
                    "
                >
                    <option>This Month</option>
                    <option>Last 30 Days</option>
                    <option>This Quarter</option>
                    <option>This Year</option>
                </select>

            </div>


            {{-- MINI STATS --}}
            <div
                class="
                    mt-5
                    grid grid-cols-1
                    gap-3

                    sm:grid-cols-3
                "
            >

                <div
                    class="
                        rounded-[15px]
                        border border-[#efebe4]
                        bg-[#fcfbf8]
                        p-4
                    "
                >
                    <p class="text-[9px] uppercase tracking-[0.1em] text-[#a1988d]">
                        Processed
                    </p>

                    <p class="mt-2 text-[18px] font-bold text-[#28231d]">
                        ₱207,260
                    </p>
                </div>


                <div
                    class="
                        rounded-[15px]
                        border border-[#efebe4]
                        bg-[#fcfbf8]
                        p-4
                    "
                >
                    <p class="text-[9px] uppercase tracking-[0.1em] text-[#a1988d]">
                        Pending
                    </p>

                    <p class="mt-2 text-[18px] font-bold text-[#ae791f]">
                        ₱38,420
                    </p>
                </div>


                <div
                    class="
                        rounded-[15px]
                        border border-[#efebe4]
                        bg-[#fcfbf8]
                        p-4
                    "
                >
                    <p class="text-[9px] uppercase tracking-[0.1em] text-[#a1988d]">
                        Growth
                    </p>

                    <p class="mt-2 text-[18px] font-bold text-[#56816a]">
                        +18.7%
                    </p>
                </div>

            </div>


            {{-- GRAPH --}}
            <div class="mt-6">

                <div class="mb-4 flex flex-wrap items-center gap-5 text-[10px] text-[#80786e]">

                    <div class="flex items-center gap-2">
                        <span class="h-[2px] w-5 rounded-full bg-[#c99128]"></span>
                        Commission
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="h-[2px] w-5 rounded-full bg-[#aebbc7]"></span>
                        Sales Reference
                    </div>

                </div>


                <div
                    class="
                        overflow-hidden
                        rounded-[18px]
                        border border-[#efebe4]
                        bg-[#fcfbf8]
                        p-3

                        sm:p-4
                    "
                >
                    <div class="overflow-x-auto">

                        <div class="min-w-[650px]">

                            <svg
                                viewBox="0 0 820 285"
                                class="h-[265px] w-full"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-label="Commission trend chart"
                            >

                                {{-- Grid --}}
                                <line x1="65" y1="40" x2="790" y2="40" stroke="#EAE5DE"/>
                                <line x1="65" y1="92" x2="790" y2="92" stroke="#EAE5DE"/>
                                <line x1="65" y1="144" x2="790" y2="144" stroke="#EAE5DE"/>
                                <line x1="65" y1="196" x2="790" y2="196" stroke="#EAE5DE"/>
                                <line x1="65" y1="248" x2="790" y2="248" stroke="#E4DED6"/>

                                {{-- Y labels --}}
                                <text x="12" y="44" fill="#A1988D" font-size="10">₱50K</text>
                                <text x="12" y="96" fill="#A1988D" font-size="10">₱40K</text>
                                <text x="12" y="148" fill="#A1988D" font-size="10">₱30K</text>
                                <text x="12" y="200" fill="#A1988D" font-size="10">₱20K</text>
                                <text x="12" y="252" fill="#A1988D" font-size="10">₱10K</text>

                                {{-- Sales reference --}}
                                <path
                                    d="M70 220
                                       C120 206, 150 198, 190 201
                                       S255 174, 310 180
                                       S390 150, 445 157
                                       S525 130, 575 136
                                       S650 106, 705 112
                                       S760 91, 785 96"
                                    stroke="#AEBBC7"
                                    stroke-width="3"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-dasharray="5 7"
                                />

                                {{-- Commission line --}}
                                <path
                                    d="M70 232
                                       C120 221, 150 212, 190 216
                                       S250 191, 310 195
                                       S390 166, 445 172
                                       S525 145, 575 151
                                       S650 120, 705 128
                                       S760 102, 785 108"
                                    stroke="#C99128"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />

                                {{-- Points --}}
                                <circle cx="70" cy="232" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                                <circle cx="190" cy="216" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                                <circle cx="310" cy="195" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                                <circle cx="445" cy="172" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                                <circle cx="575" cy="151" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                                <circle cx="705" cy="128" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                                <circle cx="785" cy="108" r="5" fill="#C99128"/>

                                {{-- X labels --}}
                                <text x="55" y="276" fill="#9B9388" font-size="10">Week 1</text>
                                <text x="175" y="276" fill="#9B9388" font-size="10">Week 2</text>
                                <text x="300" y="276" fill="#9B9388" font-size="10">Week 3</text>
                                <text x="430" y="276" fill="#9B9388" font-size="10">Week 4</text>
                                <text x="565" y="276" fill="#9B9388" font-size="10">Week 5</text>
                                <text x="695" y="276" fill="#9B9388" font-size="10">Current</text>

                            </svg>

                        </div>

                    </div>
                </div>

            </div>

        </div>


        {{-- COMMISSION SETTINGS --}}
        <div
            class="
                rounded-[22px]
                border border-[#ebe4da]
                bg-white
                p-5

                sm:p-6
            "
        >

            <div>
                <h3 class="text-[16px] font-bold text-[#28221b]">
                    Commission Settings
                </h3>

                <p class="mt-1 text-[10px] leading-5 text-[#91887d]">
                    Current platform commission configuration.
                </p>
            </div>


            <div
                class="
                    mt-5
                    rounded-[18px]
                    border border-[#eadfc9]
                    bg-[#fcfaf6]
                    p-5
                "
            >

                <p class="text-[10px] font-medium text-[#8d8272]">
                    Standard Commission Rate
                </p>

                <div class="mt-3 flex items-end gap-2">

                    <span
                        class="
                            text-[42px] font-bold
                            leading-none
                            tracking-[-0.05em]
                            text-[#c18a25]
                        "
                    >
                        10
                    </span>

                    <span class="pb-1 text-[18px] font-semibold text-[#8c7650]">
                        %
                    </span>

                </div>

                <p class="mt-3 text-[10px] leading-5 text-[#8e8579]">
                    Applied automatically to completed marketplace transactions.
                </p>

            </div>


            <div class="mt-4 space-y-3">

                <div
                    class="
                        flex items-center
                        justify-between
                        rounded-[14px]
                        border border-[#ece6dd]
                        bg-[#fcfbf8]
                        p-4
                    "
                >
                    <div>
                        <p class="text-[10px] font-semibold text-[#3c362f]">
                            Auto Calculation
                        </p>

                        <p class="mt-1 text-[9px] text-[#958c80]">
                            Calculate commission automatically.
                        </p>
                    </div>

                    <span
                        class="
                            rounded-full
                            bg-[#eef6f1]
                            px-2.5 py-1
                            text-[9px] font-semibold
                            text-[#56816a]
                        "
                    >
                        Enabled
                    </span>
                </div>


                <div
                    class="
                        flex items-center
                        justify-between
                        rounded-[14px]
                        border border-[#ece6dd]
                        bg-[#fcfbf8]
                        p-4
                    "
                >
                    <div>
                        <p class="text-[10px] font-semibold text-[#3c362f]">
                            Settlement
                        </p>

                        <p class="mt-1 text-[9px] text-[#958c80]">
                            Seller settlement schedule.
                        </p>
                    </div>

                    <span class="text-[10px] font-semibold text-[#625a50]">
                        Weekly
                    </span>
                </div>


                <div
                    class="
                        flex items-center
                        justify-between
                        rounded-[14px]
                        border border-[#ece6dd]
                        bg-[#fcfbf8]
                        p-4
                    "
                >
                    <div>
                        <p class="text-[10px] font-semibold text-[#3c362f]">
                            Last Updated
                        </p>

                        <p class="mt-1 text-[9px] text-[#958c80]">
                            Current rate configuration.
                        </p>
                    </div>

                    <span class="text-[10px] font-semibold text-[#625a50]">
                        Aug 18
                    </span>
                </div>

            </div>


            <button
                type="button"
                class="
                    mt-4 w-full
                    rounded-xl
                    border border-[#dfd4c4]
                    bg-white
                    px-4 py-2.5
                    text-[10px] font-semibold
                    text-[#675e53]
                    transition

                    hover:border-[#cdb58c]
                    hover:bg-[#fcf8f1]
                "
            >
                Edit Commission Settings
            </button>

        </div>

    </section>


    {{-- =========================================================
        COMMISSION RECORDS
    ========================================================== --}}
    <section
        class="
            mt-5
            overflow-hidden
            rounded-[22px]
            border border-[#ebe4da]
            bg-white
        "
    >

        {{-- TABLE HEADER --}}
        <div
            class="
                flex flex-col gap-4
                border-b border-[#eee8df]
                p-4

                sm:p-5
                xl:flex-row
                xl:items-center
                xl:justify-between
            "
        >

            <div>
                <h3 class="text-[16px] font-bold text-[#28221b]">
                    Commission Records
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Review marketplace orders and generated commission.
                </p>
            </div>


            <div
                class="
                    flex flex-col gap-2
                    sm:flex-row
                "
            >

                <div class="relative">

                    <svg
                        viewBox="0 0 24 24"
                        class="
                            pointer-events-none
                            absolute left-3.5 top-1/2
                            h-4 w-4
                            -translate-y-1/2
                            text-[#9e968b]
                        "
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-4-4"></path>
                    </svg>

                    <input
                        type="search"
                        placeholder="Search order or seller..."
                        class="
                            h-10 w-full
                            rounded-xl
                            border border-[#e6dfd5]
                            bg-[#fcfbf9]
                            pl-10 pr-4
                            text-[10px]
                            outline-none

                            focus:border-[#c99a3d]
                            focus:ring-4
                            focus:ring-[#c99a3d]/10

                            sm:w-[230px]
                        "
                    >

                </div>


                <select
                    class="
                        h-10
                        rounded-xl
                        border border-[#e6dfd5]
                        bg-white
                        px-3
                        text-[10px]
                        text-[#625a50]
                        outline-none
                        focus:border-[#c99a3d]
                    "
                >
                    <option>All Statuses</option>
                    <option>Processed</option>
                    <option>Pending</option>
                    <option>Adjusted</option>
                </select>


                <select
                    class="
                        h-10
                        rounded-xl
                        border border-[#e6dfd5]
                        bg-white
                        px-3
                        text-[10px]
                        text-[#625a50]
                        outline-none
                        focus:border-[#c99a3d]
                    "
                >
                    <option>This Month</option>
                    <option>Last Month</option>
                    <option>This Quarter</option>
                </select>

            </div>

        </div>


        {{-- TABLE --}}
        <div class="overflow-x-auto">

            <table class="w-full min-w-[1180px] text-left">

                <thead>
                    <tr
                        class="
                            border-b border-[#eee8df]
                            bg-[#fcfaf7]
                            text-[9px] font-semibold
                            uppercase tracking-[0.08em]
                            text-[#9b9287]
                        "
                    >
                        <th class="px-5 py-4">Order ID</th>
                        <th class="px-5 py-4">Seller</th>
                        <th class="px-5 py-4">Order Amount</th>
                        <th class="px-5 py-4">Rate</th>
                        <th class="px-5 py-4">Commission</th>
                        <th class="px-5 py-4">Seller Net</th>
                        <th class="px-5 py-4">Date</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Action</th>
                    </tr>
                </thead>


                <tbody class="text-[11px]">

                    {{-- ROW 1 --}}
                    <tr class="border-b border-[#f1ece5] transition hover:bg-[#fdfbf8]">

                        <td class="px-5 py-4 font-semibold text-[#4a433b]">
                            #SRI-18472
                        </td>

                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-9 w-9
                                        place-items-center
                                        rounded-full
                                        bg-[#f2f7f4]
                                        text-[10px] font-bold
                                        text-[#5a8069]
                                    "
                                >
                                    TW
                                </div>

                                <div>
                                    <p class="font-semibold text-[#34302a]">
                                        TechWorld Store
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        Electronics
                                    </p>
                                </div>

                            </div>

                        </td>

                        <td class="px-5 py-4 font-medium text-[#625a50]">
                            ₱12,500
                        </td>

                        <td class="px-5 py-4 text-[#81786d]">
                            10%
                        </td>

                        <td class="px-5 py-4 font-semibold text-[#a8731f]">
                            ₱1,250
                        </td>

                        <td class="px-5 py-4 font-medium text-[#625a50]">
                            ₱11,250
                        </td>

                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Aug 18, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978e83]">
                                08:30 AM
                            </p>
                        </td>

                        <td class="px-5 py-4">
                            <span
                                class="
                                    inline-flex items-center gap-1.5
                                    rounded-full
                                    bg-[#eef6f1]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#56816a]
                                "
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-[#6a9f7b]"></span>
                                Processed
                            </span>
                        </td>

                        <td class="px-5 py-4 text-right">
                            <button
                                type="button"
                                class="
                                    rounded-lg
                                    border border-[#e4ddd3]
                                    px-3 py-2
                                    text-[9px] font-semibold
                                    text-[#675f55]
                                    transition

                                    hover:border-[#d3c09f]
                                    hover:bg-[#fcf8f1]
                                    hover:text-[#a6701b]
                                "
                            >
                                View
                            </button>
                        </td>

                    </tr>


                    {{-- ROW 2 --}}
                    <tr class="border-b border-[#f1ece5] transition hover:bg-[#fdfbf8]">

                        <td class="px-5 py-4 font-semibold text-[#4a433b]">
                            #SRI-18471
                        </td>

                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-9 w-9
                                        place-items-center
                                        rounded-full
                                        bg-[#f6f2f8]
                                        text-[10px] font-bold
                                        text-[#78698a]
                                    "
                                >
                                    FH
                                </div>

                                <div>
                                    <p class="font-semibold text-[#34302a]">
                                        Fashion Hub
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        Fashion
                                    </p>
                                </div>

                            </div>

                        </td>

                        <td class="px-5 py-4 font-medium text-[#625a50]">
                            ₱8,990
                        </td>

                        <td class="px-5 py-4 text-[#81786d]">
                            10%
                        </td>

                        <td class="px-5 py-4 font-semibold text-[#a8731f]">
                            ₱899
                        </td>

                        <td class="px-5 py-4 font-medium text-[#625a50]">
                            ₱8,091
                        </td>

                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Aug 18, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978e83]">
                                07:52 AM
                            </p>
                        </td>

                        <td class="px-5 py-4">
                            <span
                                class="
                                    rounded-full
                                    border border-[#eadfc9]
                                    bg-[#fbf6ec]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#ad781c]
                                "
                            >
                                Pending
                            </span>
                        </td>

                        <td class="px-5 py-4 text-right">
                            <button
                                type="button"
                                class="
                                    rounded-lg
                                    border border-[#e4ddd3]
                                    px-3 py-2
                                    text-[9px] font-semibold
                                    text-[#675f55]
                                    transition

                                    hover:border-[#d3c09f]
                                    hover:bg-[#fcf8f1]
                                    hover:text-[#a6701b]
                                "
                            >
                                Review
                            </button>
                        </td>

                    </tr>


                    {{-- ROW 3 --}}
                    <tr class="border-b border-[#f1ece5] transition hover:bg-[#fdfbf8]">

                        <td class="px-5 py-4 font-semibold text-[#4a433b]">
                            #SRI-18470
                        </td>

                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-9 w-9
                                        place-items-center
                                        rounded-full
                                        bg-[#f7f3ed]
                                        text-[10px] font-bold
                                        text-[#967554]
                                    "
                                >
                                    HE
                                </div>

                                <div>
                                    <p class="font-semibold text-[#34302a]">
                                        Home Essentials
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        Home & Living
                                    </p>
                                </div>

                            </div>

                        </td>

                        <td class="px-5 py-4 font-medium text-[#625a50]">
                            ₱15,750
                        </td>

                        <td class="px-5 py-4 text-[#81786d]">
                            10%
                        </td>

                        <td class="px-5 py-4 font-semibold text-[#a8731f]">
                            ₱1,575
                        </td>

                        <td class="px-5 py-4 font-medium text-[#625a50]">
                            ₱14,175
                        </td>

                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Aug 17, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978e83]">
                                06:14 PM
                            </p>
                        </td>

                        <td class="px-5 py-4">
                            <span
                                class="
                                    inline-flex items-center gap-1.5
                                    rounded-full
                                    bg-[#eef6f1]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#56816a]
                                "
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-[#6a9f7b]"></span>
                                Processed
                            </span>
                        </td>

                        <td class="px-5 py-4 text-right">
                            <button
                                type="button"
                                class="
                                    rounded-lg
                                    border border-[#e4ddd3]
                                    px-3 py-2
                                    text-[9px] font-semibold
                                    text-[#675f55]
                                    transition

                                    hover:border-[#d3c09f]
                                    hover:bg-[#fcf8f1]
                                    hover:text-[#a6701b]
                                "
                            >
                                View
                            </button>
                        </td>

                    </tr>


                    {{-- ROW 4 --}}
                    <tr class="border-b border-[#f1ece5] transition hover:bg-[#fdfbf8]">

                        <td class="px-5 py-4 font-semibold text-[#4a433b]">
                            #SRI-18469
                        </td>

                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-9 w-9
                                        place-items-center
                                        rounded-full
                                        bg-[#f3f6f8]
                                        text-[10px] font-bold
                                        text-[#667f94]
                                    "
                                >
                                    BE
                                </div>

                                <div>
                                    <p class="font-semibold text-[#34302a]">
                                        Beauty Essentials
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        Beauty
                                    </p>
                                </div>

                            </div>

                        </td>

                        <td class="px-5 py-4 font-medium text-[#625a50]">
                            ₱6,450
                        </td>

                        <td class="px-5 py-4 text-[#81786d]">
                            10%
                        </td>

                        <td class="px-5 py-4 font-semibold text-[#a8731f]">
                            ₱645
                        </td>

                        <td class="px-5 py-4 font-medium text-[#625a50]">
                            ₱5,805
                        </td>

                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Aug 17, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978e83]">
                                02:20 PM
                            </p>
                        </td>

                        <td class="px-5 py-4">
                            <span
                                class="
                                    rounded-full
                                    border border-[#e2dce8]
                                    bg-[#f6f3f8]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#79698a]
                                "
                            >
                                Adjusted
                            </span>
                        </td>

                        <td class="px-5 py-4 text-right">
                            <button
                                type="button"
                                class="
                                    rounded-lg
                                    border border-[#e4ddd3]
                                    px-3 py-2
                                    text-[9px] font-semibold
                                    text-[#675f55]
                                    transition

                                    hover:border-[#d3c09f]
                                    hover:bg-[#fcf8f1]
                                    hover:text-[#a6701b]
                                "
                            >
                                Details
                            </button>
                        </td>

                    </tr>


                    {{-- ROW 5 --}}
                    <tr class="transition hover:bg-[#fdfbf8]">

                        <td class="px-5 py-4 font-semibold text-[#4a433b]">
                            #SRI-18468
                        </td>

                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-9 w-9
                                        place-items-center
                                        rounded-full
                                        bg-[#f2f7f4]
                                        text-[10px] font-bold
                                        text-[#5a8069]
                                    "
                                >
                                    GS
                                </div>

                                <div>
                                    <p class="font-semibold text-[#34302a]">
                                        Gadget Station
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        Electronics
                                    </p>
                                </div>

                            </div>

                        </td>

                        <td class="px-5 py-4 font-medium text-[#625a50]">
                            ₱21,300
                        </td>

                        <td class="px-5 py-4 text-[#81786d]">
                            10%
                        </td>

                        <td class="px-5 py-4 font-semibold text-[#a8731f]">
                            ₱2,130
                        </td>

                        <td class="px-5 py-4 font-medium text-[#625a50]">
                            ₱19,170
                        </td>

                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Aug 17, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978e83]">
                                11:06 AM
                            </p>
                        </td>

                        <td class="px-5 py-4">
                            <span
                                class="
                                    inline-flex items-center gap-1.5
                                    rounded-full
                                    bg-[#eef6f1]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#56816a]
                                "
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-[#6a9f7b]"></span>
                                Processed
                            </span>
                        </td>

                        <td class="px-5 py-4 text-right">
                            <button
                                type="button"
                                class="
                                    rounded-lg
                                    border border-[#e4ddd3]
                                    px-3 py-2
                                    text-[9px] font-semibold
                                    text-[#675f55]
                                    transition

                                    hover:border-[#d3c09f]
                                    hover:bg-[#fcf8f1]
                                    hover:text-[#a6701b]
                                "
                            >
                                View
                            </button>
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        <div
            class="
                flex flex-col gap-3
                border-t border-[#eee8df]
                px-5 py-4

                sm:flex-row
                sm:items-center
                sm:justify-between
            "
        >

            <p class="text-[10px] text-[#91887d]">
                Showing
                <span class="font-semibold text-[#5d554c]">1–5</span>
                of
                <span class="font-semibold text-[#5d554c]">286</span>
                commission records
            </p>


            <div class="flex items-center gap-1.5">

                <button
                    type="button"
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-lg
                        border border-[#e6dfd5]
                        text-[#8c8479]
                        transition
                        hover:border-[#d2c2a7]
                        hover:bg-[#fcf8f1]
                    "
                >
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                </button>


                <button
                    type="button"
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-lg
                        bg-[#c99128]
                        text-[10px] font-semibold
                        text-white
                    "
                >
                    1
                </button>


                <button
                    type="button"
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-lg
                        border border-[#e6dfd5]
                        text-[10px] font-semibold
                        text-[#6f675d]
                        transition
                        hover:border-[#d2c2a7]
                        hover:bg-[#fcf8f1]
                    "
                >
                    2
                </button>


                <button
                    type="button"
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-lg
                        border border-[#e6dfd5]
                        text-[10px] font-semibold
                        text-[#6f675d]
                        transition
                        hover:border-[#d2c2a7]
                        hover:bg-[#fcf8f1]
                    "
                >
                    3
                </button>


                <button
                    type="button"
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-lg
                        border border-[#e6dfd5]
                        text-[#8c8479]
                        transition
                        hover:border-[#d2c2a7]
                        hover:bg-[#fcf8f1]
                    "
                >
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>

            </div>

        </div>

    </section>


    <div class="h-5"></div>

</div>

@endsection