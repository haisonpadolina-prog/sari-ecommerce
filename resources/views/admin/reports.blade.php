@extends('layouts.admin')

@section('title', 'Reports — SARI Admin')
@section('page-title', 'Reports')

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
                    Analytics & Reporting
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
                    Reports
                </h2>

                <p
                    class="
                        mt-2
                        max-w-[760px]
                        text-[12px] leading-6
                        text-[#81786c]

                        sm:text-[13px]
                    "
                >
                    Review marketplace performance, sales, commission,
                    users, and administrative activity through summarized reports.
                </p>
            </div>


            <div class="flex flex-wrap items-center gap-3">

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
                    <option>Last 30 Days</option>
                    <option>This Quarter</option>
                    <option>This Year</option>
                </select>

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
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M5 4h14v16H5z"></path>
                        <path d="M8 8h8"></path>
                        <path d="M8 12h8"></path>
                        <path d="M8 16h5"></path>
                    </svg>

                    Export Summary
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
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 3v12"></path>
                        <path d="m8 11 4 4 4-4"></path>
                        <path d="M5 20h14"></path>
                    </svg>

                    Generate Report
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

        <div class="rounded-[18px] border border-[#dce5ed] bg-white p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Gross Sales
                    </p>

                    <h3 class="mt-2 text-[26px] font-bold tracking-[-0.04em] text-[#211d18]">
                        ₱2.45M
                    </h3>

                    <p class="mt-2 text-[10px] font-medium text-[#56816a]">
                        ▲ 24.3% this month
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#d9e3ec] bg-[#f3f7fa] text-[#627f99]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M4 20h16"></path>
                        <path d="M7 17v-4"></path>
                        <path d="M12 17V9"></path>
                        <path d="M17 17V5"></path>
                    </svg>
                </div>
            </div>
        </div>


        <div class="rounded-[18px] border border-[#eadfc9] bg-white p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Platform Commission
                    </p>

                    <h3 class="mt-2 text-[26px] font-bold tracking-[-0.04em] text-[#211d18]">
                        ₱245,680
                    </h3>

                    <p class="mt-2 text-[10px] font-medium text-[#56816a]">
                        ▲ 18.7% this month
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#eadfc8] bg-[#fbf6ec] text-[#b98020]">
                    <span class="text-[18px] font-semibold">₱</span>
                </div>
            </div>
        </div>


        <div class="rounded-[18px] border border-[#d8e6dd] bg-white p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Completed Orders
                    </p>

                    <h3 class="mt-2 text-[26px] font-bold tracking-[-0.04em] text-[#211d18]">
                        8,765
                    </h3>

                    <p class="mt-2 text-[10px] text-[#8f877d]">
                        91.4% completion rate
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#d5e5dc] bg-[#f1f7f3] text-[#56816a]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M5 7h14l-1 13H6L5 7Z"></path>
                        <path d="M9 7a3 3 0 0 1 6 0"></path>
                        <path d="m9 14 2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>


        <div class="rounded-[18px] border border-[#e5dfea] bg-white p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Registered Users
                    </p>

                    <h3 class="mt-2 text-[26px] font-bold tracking-[-0.04em] text-[#211d18]">
                        12,458
                    </h3>

                    <p class="mt-2 text-[10px] text-[#8f877d]">
                        +634 this month
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#e0d9e6] bg-[#f6f2f8] text-[#806a91]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <circle cx="9" cy="8" r="3"></circle>
                        <circle cx="17" cy="10" r="2.5"></circle>
                        <path d="M3 20c.4-3.8 2.6-6 6-6s5.6 2.2 6 6"></path>
                        <path d="M15 15c3.4 0 5.4 1.8 6 5"></path>
                    </svg>
                </div>
            </div>
        </div>

    </section>


    {{-- =========================================================
        SALES GRAPH + QUICK REPORTS
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-5

            xl:grid-cols-[1.4fr_.6fr]
        "
    >

        {{-- SALES PERFORMANCE --}}
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
                        Sales Performance
                    </h3>

                    <p class="mt-1 text-[10px] text-[#91887d]">
                        Gross sales and commission performance for the selected period.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-4 text-[9px] text-[#81796e]">
                    <span class="inline-flex items-center gap-2">
                        <span class="h-[2px] w-5 rounded-full bg-[#aebbc7]"></span>
                        Gross Sales
                    </span>

                    <span class="inline-flex items-center gap-2">
                        <span class="h-[2px] w-5 rounded-full bg-[#c99128]"></span>
                        Commission
                    </span>
                </div>
            </div>


            <div
                class="
                    mt-5
                    overflow-hidden
                    rounded-[18px]
                    border border-[#efebe4]
                    bg-[#fcfbf8]
                    p-3

                    sm:p-4
                "
            >
                <div class="overflow-x-auto">

                    <div class="min-w-[680px]">

                        <svg
                            viewBox="0 0 860 310"
                            class="h-[285px] w-full"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >

                            {{-- GRID --}}
                            <line x1="70" y1="35" x2="825" y2="35" stroke="#EAE5DE"/>
                            <line x1="70" y1="90" x2="825" y2="90" stroke="#EAE5DE"/>
                            <line x1="70" y1="145" x2="825" y2="145" stroke="#EAE5DE"/>
                            <line x1="70" y1="200" x2="825" y2="200" stroke="#EAE5DE"/>
                            <line x1="70" y1="255" x2="825" y2="255" stroke="#E4DED6"/>

                            {{-- Y LABELS --}}
                            <text x="10" y="39" fill="#A1988D" font-size="10">₱600K</text>
                            <text x="10" y="94" fill="#A1988D" font-size="10">₱450K</text>
                            <text x="10" y="149" fill="#A1988D" font-size="10">₱300K</text>
                            <text x="10" y="204" fill="#A1988D" font-size="10">₱150K</text>
                            <text x="10" y="259" fill="#A1988D" font-size="10">₱0</text>

                            {{-- SALES LINE --}}
                            <path
                                d="M75 228
                                   C125 212, 165 198, 205 201
                                   S285 166, 335 174
                                   S415 136, 470 144
                                   S555 112, 610 120
                                   S690 82, 745 94
                                   S795 65, 820 71"
                                stroke="#AEBBC7"
                                stroke-width="4"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />

                            {{-- COMMISSION LINE --}}
                            <path
                                d="M75 248
                                   C125 240, 165 232, 205 234
                                   S285 218, 335 220
                                   S415 199, 470 202
                                   S555 181, 610 184
                                   S690 162, 745 167
                                   S795 149, 820 151"
                                stroke="#C99128"
                                stroke-width="4"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />

                            {{-- POINTS --}}
                            <circle cx="75" cy="248" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                            <circle cx="205" cy="234" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                            <circle cx="335" cy="220" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                            <circle cx="470" cy="202" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                            <circle cx="610" cy="184" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                            <circle cx="745" cy="167" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                            <circle cx="820" cy="151" r="5" fill="#C99128"/>

                            {{-- X LABELS --}}
                            <text x="62" y="292" fill="#9B9388" font-size="10">Week 1</text>
                            <text x="190" y="292" fill="#9B9388" font-size="10">Week 2</text>
                            <text x="320" y="292" fill="#9B9388" font-size="10">Week 3</text>
                            <text x="455" y="292" fill="#9B9388" font-size="10">Week 4</text>
                            <text x="595" y="292" fill="#9B9388" font-size="10">Week 5</text>
                            <text x="730" y="292" fill="#9B9388" font-size="10">Current</text>

                        </svg>

                    </div>

                </div>
            </div>


            <div
                class="
                    mt-4
                    grid grid-cols-1
                    gap-3

                    sm:grid-cols-3
                "
            >

                <div class="rounded-[14px] border border-[#eee8df] bg-[#fcfbf8] p-4">
                    <p class="text-[9px] uppercase tracking-[0.1em] text-[#9d958a]">
                        Highest Sales Day
                    </p>

                    <p class="mt-2 text-[15px] font-bold text-[#302a24]">
                        ₱186,400
                    </p>

                    <p class="mt-1 text-[8px] text-[#948b7f]">
                        August 15, 2026
                    </p>
                </div>


                <div class="rounded-[14px] border border-[#eee8df] bg-[#fcfbf8] p-4">
                    <p class="text-[9px] uppercase tracking-[0.1em] text-[#9d958a]">
                        Average Order
                    </p>

                    <p class="mt-2 text-[15px] font-bold text-[#302a24]">
                        ₱2,794
                    </p>

                    <p class="mt-1 text-[8px] text-[#948b7f]">
                        Across completed orders
                    </p>
                </div>


                <div class="rounded-[14px] border border-[#eee8df] bg-[#fcfbf8] p-4">
                    <p class="text-[9px] uppercase tracking-[0.1em] text-[#9d958a]">
                        Commission Rate
                    </p>

                    <p class="mt-2 text-[15px] font-bold text-[#a8731f]">
                        10%
                    </p>

                    <p class="mt-1 text-[8px] text-[#948b7f]">
                        Standard platform rate
                    </p>
                </div>

            </div>

        </div>


        {{-- QUICK REPORTS --}}
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
                    Quick Reports
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Generate common marketplace reports.
                </p>
            </div>


            <div class="mt-5 space-y-3">

                <button
                    type="button"
                    class="
                        flex w-full
                        items-center justify-between gap-4
                        rounded-[15px]
                        border border-[#e9e3da]
                        bg-[#fcfbf8]
                        p-4
                        text-left
                        transition

                        hover:border-[#d8c8ad]
                        hover:bg-[#fcf8f1]
                    "
                >
                    <span class="flex items-center gap-3">

                        <span
                            class="
                                grid h-10 w-10
                                shrink-0 place-items-center
                                rounded-xl
                                bg-[#f3f7fa]
                                text-[#647f97]
                            "
                        >
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M4 20h16"></path>
                                <path d="M7 17v-5"></path>
                                <path d="M12 17V8"></path>
                                <path d="M17 17V4"></path>
                            </svg>
                        </span>

                        <span>
                            <span class="block text-[10px] font-semibold text-[#3d3730]">
                                Sales Summary
                            </span>

                            <span class="mt-1 block text-[8px] text-[#958c80]">
                                Orders, sales, refunds, and trends
                            </span>
                        </span>

                    </span>

                    <svg viewBox="0 0 24 24" class="h-4 w-4 text-[#a0978c]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>


                <button
                    type="button"
                    class="
                        flex w-full
                        items-center justify-between gap-4
                        rounded-[15px]
                        border border-[#e9e3da]
                        bg-[#fcfbf8]
                        p-4
                        text-left
                        transition

                        hover:border-[#d8c8ad]
                        hover:bg-[#fcf8f1]
                    "
                >
                    <span class="flex items-center gap-3">

                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#fbf6ec] text-[#ae791f]">
                            <span class="text-[16px] font-semibold">₱</span>
                        </span>

                        <span>
                            <span class="block text-[10px] font-semibold text-[#3d3730]">
                                Commission Report
                            </span>

                            <span class="mt-1 block text-[8px] text-[#958c80]">
                                Platform commission and seller net
                            </span>
                        </span>

                    </span>

                    <svg viewBox="0 0 24 24" class="h-4 w-4 text-[#a0978c]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>


                <button
                    type="button"
                    class="
                        flex w-full
                        items-center justify-between gap-4
                        rounded-[15px]
                        border border-[#e9e3da]
                        bg-[#fcfbf8]
                        p-4
                        text-left
                        transition

                        hover:border-[#d8c8ad]
                        hover:bg-[#fcf8f1]
                    "
                >
                    <span class="flex items-center gap-3">

                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#f1f7f3] text-[#56816a]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                                <circle cx="9" cy="8" r="3"></circle>
                                <circle cx="17" cy="10" r="2.5"></circle>
                                <path d="M3 20c.4-3.8 2.6-6 6-6s5.6 2.2 6 6"></path>
                                <path d="M15 15c3.4 0 5.4 1.8 6 5"></path>
                            </svg>
                        </span>

                        <span>
                            <span class="block text-[10px] font-semibold text-[#3d3730]">
                                User Report
                            </span>

                            <span class="mt-1 block text-[8px] text-[#958c80]">
                                Buyers, sellers, couriers, activity
                            </span>
                        </span>

                    </span>

                    <svg viewBox="0 0 24 24" class="h-4 w-4 text-[#a0978c]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>


                <button
                    type="button"
                    class="
                        flex w-full
                        items-center justify-between gap-4
                        rounded-[15px]
                        border border-[#e9e3da]
                        bg-[#fcfbf8]
                        p-4
                        text-left
                        transition

                        hover:border-[#d8c8ad]
                        hover:bg-[#fcf8f1]
                    "
                >
                    <span class="flex items-center gap-3">

                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#f6f2f8] text-[#7c6a8c]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                                <path d="M9 8h6"></path>
                                <path d="M9 12h6"></path>
                                <path d="M9 16h3"></path>
                            </svg>
                        </span>

                        <span>
                            <span class="block text-[10px] font-semibold text-[#3d3730]">
                                Dispute Report
                            </span>

                            <span class="mt-1 block text-[8px] text-[#958c80]">
                                Complaints, disputes, and resolutions
                            </span>
                        </span>

                    </span>

                    <svg viewBox="0 0 24 24" class="h-4 w-4 text-[#a0978c]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>

            </div>

        </div>

    </section>


    {{-- =========================================================
        CATEGORY PERFORMANCE
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-5

            xl:grid-cols-2
        "
    >

        {{-- TOP CATEGORIES --}}
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
                    Top Categories
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Categories contributing the most marketplace sales.
                </p>
            </div>


            <div class="mt-5 space-y-4">

                <div>
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-semibold text-[#3e3831]">
                                Electronics
                            </p>

                            <p class="mt-1 text-[8px] text-[#978e83]">
                                ₱742,500 sales
                            </p>
                        </div>

                        <span class="text-[10px] font-semibold text-[#647f97]">
                            30%
                        </span>
                    </div>

                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#edf0f2]">
                        <div class="h-full w-[30%] rounded-full bg-[#9aabba]"></div>
                    </div>
                </div>


                <div>
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-semibold text-[#3e3831]">
                                Fashion
                            </p>

                            <p class="mt-1 text-[8px] text-[#978e83]">
                                ₱588,000 sales
                            </p>
                        </div>

                        <span class="text-[10px] font-semibold text-[#a8731f]">
                            24%
                        </span>
                    </div>

                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#f0ece5]">
                        <div class="h-full w-[24%] rounded-full bg-[#d3ac65]"></div>
                    </div>
                </div>


                <div>
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-semibold text-[#3e3831]">
                                Home & Living
                            </p>

                            <p class="mt-1 text-[8px] text-[#978e83]">
                                ₱465,500 sales
                            </p>
                        </div>

                        <span class="text-[10px] font-semibold text-[#56816a]">
                            19%
                        </span>
                    </div>

                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#edf0ee]">
                        <div class="h-full w-[19%] rounded-full bg-[#88a894]"></div>
                    </div>
                </div>


                <div>
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-semibold text-[#3e3831]">
                                Beauty
                            </p>

                            <p class="mt-1 text-[8px] text-[#978e83]">
                                ₱343,000 sales
                            </p>
                        </div>

                        <span class="text-[10px] font-semibold text-[#806a91]">
                            14%
                        </span>
                    </div>

                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#efedf1]">
                        <div class="h-full w-[14%] rounded-full bg-[#a093ad]"></div>
                    </div>
                </div>


                <div>
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-semibold text-[#3e3831]">
                                Others
                            </p>

                            <p class="mt-1 text-[8px] text-[#978e83]">
                                ₱318,500 sales
                            </p>
                        </div>

                        <span class="text-[10px] font-semibold text-[#8a8176]">
                            13%
                        </span>
                    </div>

                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#efebe5]">
                        <div class="h-full w-[13%] rounded-full bg-[#b8afa5]"></div>
                    </div>
                </div>

            </div>

        </div>


        {{-- PLATFORM METRICS --}}
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
                    Platform Metrics
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Operational performance indicators for the current period.
                </p>
            </div>


            <div
                class="
                    mt-5
                    grid grid-cols-1
                    gap-3

                    sm:grid-cols-2
                "
            >

                <div class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <p class="text-[9px] text-[#958c80]">
                        Order Completion Rate
                    </p>

                    <div class="mt-2 flex items-end justify-between gap-3">
                        <p class="text-[20px] font-bold text-[#302a24]">
                            91.4%
                        </p>

                        <span class="text-[9px] font-semibold text-[#56816a]">
                            +2.1%
                        </span>
                    </div>
                </div>


                <div class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <p class="text-[9px] text-[#958c80]">
                        Refund Rate
                    </p>

                    <div class="mt-2 flex items-end justify-between gap-3">
                        <p class="text-[20px] font-bold text-[#302a24]">
                            3.8%
                        </p>

                        <span class="text-[9px] font-semibold text-[#56816a]">
                            -0.7%
                        </span>
                    </div>
                </div>


                <div class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <p class="text-[9px] text-[#958c80]">
                        Active Sellers
                    </p>

                    <div class="mt-2 flex items-end justify-between gap-3">
                        <p class="text-[20px] font-bold text-[#302a24]">
                            2,345
                        </p>

                        <span class="text-[9px] font-semibold text-[#56816a]">
                            +8.3%
                        </span>
                    </div>
                </div>


                <div class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <p class="text-[9px] text-[#958c80]">
                        Dispute Resolution
                    </p>

                    <div class="mt-2 flex items-end justify-between gap-3">
                        <p class="text-[20px] font-bold text-[#302a24]">
                            94
                        </p>

                        <span class="text-[9px] font-semibold text-[#56816a]">
                            This month
                        </span>
                    </div>
                </div>

            </div>


            <div
                class="
                    mt-4
                    rounded-[15px]
                    border border-[#e7dfd2]
                    bg-[#fcfaf6]
                    p-4
                "
            >
                <div class="flex items-start gap-3">

                    <div
                        class="
                            grid h-9 w-9
                            shrink-0 place-items-center
                            rounded-lg
                            bg-[#fbf3e4]
                            text-[#ae791f]
                        "
                    >
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 3v9"></path>
                            <circle cx="12" cy="17" r="1"></circle>
                        </svg>
                    </div>

                    <div>
                        <p class="text-[10px] font-semibold text-[#413a32]">
                            Marketplace insight
                        </p>

                        <p class="mt-1 text-[9px] leading-5 text-[#8e8579]">
                            Sales and order completion are trending upward,
                            while refund rates remain relatively low for the current period.
                        </p>
                    </div>

                </div>
            </div>

        </div>

    </section>


    {{-- =========================================================
        GENERATED REPORTS TABLE
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
                    Generated Reports
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Recently generated administrative and marketplace reports.
                </p>
            </div>


            <div class="flex flex-col gap-2 sm:flex-row">

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
                        placeholder="Search reports..."
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

                            sm:w-[220px]
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
                    <option>All Reports</option>
                    <option>Sales</option>
                    <option>Commission</option>
                    <option>Users</option>
                    <option>Disputes</option>
                </select>

            </div>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full min-w-[1000px] text-left">

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
                        <th class="px-5 py-4">Report</th>
                        <th class="px-5 py-4">Type</th>
                        <th class="px-5 py-4">Period</th>
                        <th class="px-5 py-4">Generated By</th>
                        <th class="px-5 py-4">Generated</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Action</th>
                    </tr>
                </thead>


                <tbody class="text-[11px]">

                    <tr class="border-b border-[#f1ece5] transition hover:bg-[#fdfbf8]">

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div class="grid h-9 w-9 place-items-center rounded-lg bg-[#f3f7fa] text-[#647f97]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M5 3h10l4 4v14H5z"></path>
                                        <path d="M15 3v5h5"></path>
                                    </svg>
                                </div>

                                <div>
                                    <p class="font-semibold text-[#3a342e]">
                                        Monthly Sales Summary
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        RPT-2026-0818-001
                                    </p>
                                </div>

                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <span class="rounded-full bg-[#f3f7fa] px-2.5 py-1.5 text-[9px] font-semibold text-[#647f97]">
                                Sales
                            </span>
                        </td>

                        <td class="px-5 py-4 text-[#625a50]">
                            Aug 1–18, 2026
                        </td>

                        <td class="px-5 py-4 text-[#625a50]">
                            Admin User
                        </td>

                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Today
                            </p>

                            <p class="mt-1 text-[9px] text-[#978e83]">
                                09:42 AM
                            </p>
                        </td>

                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef6f1] px-2.5 py-1.5 text-[9px] font-semibold text-[#56816a]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#6a9f7b]"></span>
                                Ready
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">

                                <button type="button" class="rounded-lg border border-[#e4ddd3] px-3 py-2 text-[9px] font-semibold text-[#675f55] transition hover:border-[#d3c09f] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    View
                                </button>

                                <button type="button" class="grid h-8 w-8 place-items-center rounded-lg border border-[#e4ddd3] text-[#675f55] transition hover:border-[#d3c09f] hover:bg-[#fcf8f1]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M12 3v12"></path>
                                        <path d="m8 11 4 4 4-4"></path>
                                        <path d="M5 20h14"></path>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>


                    <tr class="border-b border-[#f1ece5] transition hover:bg-[#fdfbf8]">

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div class="grid h-9 w-9 place-items-center rounded-lg bg-[#fbf6ec] text-[#ae791f]">
                                    <span class="text-[14px] font-semibold">₱</span>
                                </div>

                                <div>
                                    <p class="font-semibold text-[#3a342e]">
                                        Commission Report
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        RPT-2026-0817-004
                                    </p>
                                </div>

                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <span class="rounded-full bg-[#fbf6ec] px-2.5 py-1.5 text-[9px] font-semibold text-[#a8731f]">
                                Commission
                            </span>
                        </td>

                        <td class="px-5 py-4 text-[#625a50]">
                            Aug 1–17, 2026
                        </td>

                        <td class="px-5 py-4 text-[#625a50]">
                            Admin User
                        </td>

                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Yesterday
                            </p>

                            <p class="mt-1 text-[9px] text-[#978e83]">
                                05:18 PM
                            </p>
                        </td>

                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef6f1] px-2.5 py-1.5 text-[9px] font-semibold text-[#56816a]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#6a9f7b]"></span>
                                Ready
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">

                                <button type="button" class="rounded-lg border border-[#e4ddd3] px-3 py-2 text-[9px] font-semibold text-[#675f55] transition hover:border-[#d3c09f] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    View
                                </button>

                                <button type="button" class="grid h-8 w-8 place-items-center rounded-lg border border-[#e4ddd3] text-[#675f55] transition hover:border-[#d3c09f] hover:bg-[#fcf8f1]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M12 3v12"></path>
                                        <path d="m8 11 4 4 4-4"></path>
                                        <path d="M5 20h14"></path>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>


                    <tr class="border-b border-[#f1ece5] transition hover:bg-[#fdfbf8]">

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div class="grid h-9 w-9 place-items-center rounded-lg bg-[#f1f7f3] text-[#56816a]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <circle cx="9" cy="8" r="3"></circle>
                                        <path d="M3 20c.4-3.8 2.6-6 6-6s5.6 2.2 6 6"></path>
                                    </svg>
                                </div>

                                <div>
                                    <p class="font-semibold text-[#3a342e]">
                                        User Growth Report
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        RPT-2026-0815-002
                                    </p>
                                </div>

                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <span class="rounded-full bg-[#f1f7f3] px-2.5 py-1.5 text-[9px] font-semibold text-[#56816a]">
                                Users
                            </span>
                        </td>

                        <td class="px-5 py-4 text-[#625a50]">
                            Jul 15–Aug 15
                        </td>

                        <td class="px-5 py-4 text-[#625a50]">
                            Admin User
                        </td>

                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Aug 15
                            </p>

                            <p class="mt-1 text-[9px] text-[#978e83]">
                                02:06 PM
                            </p>
                        </td>

                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef6f1] px-2.5 py-1.5 text-[9px] font-semibold text-[#56816a]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#6a9f7b]"></span>
                                Ready
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">

                                <button type="button" class="rounded-lg border border-[#e4ddd3] px-3 py-2 text-[9px] font-semibold text-[#675f55] transition hover:border-[#d3c09f] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    View
                                </button>

                                <button type="button" class="grid h-8 w-8 place-items-center rounded-lg border border-[#e4ddd3] text-[#675f55] transition hover:border-[#d3c09f] hover:bg-[#fcf8f1]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M12 3v12"></path>
                                        <path d="m8 11 4 4 4-4"></path>
                                        <path d="M5 20h14"></path>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>


                    <tr class="transition hover:bg-[#fdfbf8]">

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div class="grid h-9 w-9 place-items-center rounded-lg bg-[#f6f2f8] text-[#7c6a8c]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                                        <path d="M9 8h6"></path>
                                    </svg>
                                </div>

                                <div>
                                    <p class="font-semibold text-[#3a342e]">
                                        Complaints & Disputes Report
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        RPT-2026-0812-003
                                    </p>
                                </div>

                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <span class="rounded-full bg-[#f6f2f8] px-2.5 py-1.5 text-[9px] font-semibold text-[#7c6a8c]">
                                Disputes
                            </span>
                        </td>

                        <td class="px-5 py-4 text-[#625a50]">
                            Aug 1–12, 2026
                        </td>

                        <td class="px-5 py-4 text-[#625a50]">
                            Admin User
                        </td>

                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Aug 12
                            </p>

                            <p class="mt-1 text-[9px] text-[#978e83]">
                                11:24 AM
                            </p>
                        </td>

                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef6f1] px-2.5 py-1.5 text-[9px] font-semibold text-[#56816a]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#6a9f7b]"></span>
                                Ready
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">

                                <button type="button" class="rounded-lg border border-[#e4ddd3] px-3 py-2 text-[9px] font-semibold text-[#675f55] transition hover:border-[#d3c09f] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    View
                                </button>

                                <button type="button" class="grid h-8 w-8 place-items-center rounded-lg border border-[#e4ddd3] text-[#675f55] transition hover:border-[#d3c09f] hover:bg-[#fcf8f1]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M12 3v12"></path>
                                        <path d="m8 11 4 4 4-4"></path>
                                        <path d="M5 20h14"></path>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


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
                <span class="font-semibold text-[#5d554c]">1–4</span>
                of
                <span class="font-semibold text-[#5d554c]">36</span>
                generated reports
            </p>


            <div class="flex items-center gap-1.5">

                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] text-[#8c8479] transition hover:border-[#d2c2a7] hover:bg-[#fcf8f1]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                </button>

                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg bg-[#c99128] text-[10px] font-semibold text-white">
                    1
                </button>

                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] text-[10px] font-semibold text-[#6f675d] transition hover:border-[#d2c2a7] hover:bg-[#fcf8f1]">
                    2
                </button>

                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] text-[10px] font-semibold text-[#6f675d] transition hover:border-[#d2c2a7] hover:bg-[#fcf8f1]">
                    3
                </button>

                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] text-[#8c8479] transition hover:border-[#d2c2a7] hover:bg-[#fcf8f1]">
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