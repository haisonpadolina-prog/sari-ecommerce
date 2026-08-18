@extends('layouts.seller')

@section('title', 'Generate Report — SARI Seller')
@section('page-title', 'Generate Report')

@section('content')

<div class="mx-auto w-full max-w-[1800px]">

    {{-- =========================================================
        PAGE INTRO
    ========================================================== --}}
    <section class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6 lg:p-7">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-[#d9e6dd] bg-[#f3f8f5] px-3 py-1.5 text-[9px] font-semibold uppercase tracking-[0.14em] text-[#56816a]">
                    <span class="h-2 w-2 rounded-full bg-[#68a07b]"></span>
                    Seller Analytics
                </div>

                <h2 class="mt-3 text-[22px] font-bold tracking-[-0.03em] text-[#211c16] sm:text-[24px]">
                    Generate Report
                </h2>

                <p class="mt-2 max-w-[780px] text-[12px] leading-6 text-[#81786c] sm:text-[13px]">
                    Review sales, profit, commission, orders, and seller performance
                    for a selected date range.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button
                    id="downloadReportButton"
                    type="button"
                    class="inline-flex h-11 items-center gap-2 rounded-xl border border-[#e6dfd4] bg-white px-4 text-[10px] font-semibold text-[#62594e] transition hover:border-[#d4c29f] hover:bg-[#fcf9f3]"
                >
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 3v12"></path>
                        <path d="m7 10 5 5 5-5"></path>
                        <path d="M5 21h14"></path>
                    </svg>
                    Download Report
                </button>

                <button
                    id="printReportButton"
                    type="button"
                    class="inline-flex h-11 items-center gap-2 rounded-xl bg-[#c99128] px-4 text-[10px] font-semibold text-white transition hover:bg-[#b47e1e]"
                >
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M6 9V4h12v5"></path>
                        <rect x="5" y="13" width="14" height="7" rx="1"></rect>
                        <path d="M4 9h16a2 2 0 0 1 2 2v4h-3"></path>
                        <path d="M5 15H2v-4a2 2 0 0 1 2-2"></path>
                    </svg>
                    Print Report
                </button>
            </div>
        </div>
    </section>


    {{-- =========================================================
        DATE RANGE FILTER
    ========================================================== --}}
    <section class="mt-5 rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">

            <div>
                <h3 class="text-[15px] font-bold text-[#28221b]">
                    Report Period
                </h3>

                <p class="mt-1 text-[9px] text-[#91887d]">
                    Choose the date range you want to analyze.
                </p>
            </div>


            <div class="grid w-full grid-cols-1 gap-3 sm:grid-cols-2 xl:w-auto xl:grid-cols-[190px_190px_150px_130px]">

                <div>
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        From Date
                    </label>

                    <input
                        id="reportFromDate"
                        type="date"
                        value="2026-08-01"
                        class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-3 text-[9px] outline-none focus:border-[#c99a3d] focus:ring-4 focus:ring-[#c99a3d]/10"
                    >
                </div>


                <div>
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        To Date
                    </label>

                    <input
                        id="reportToDate"
                        type="date"
                        value="2026-08-18"
                        class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-3 text-[9px] outline-none focus:border-[#c99a3d] focus:ring-4 focus:ring-[#c99a3d]/10"
                    >
                </div>


                <div>
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        Report Type
                    </label>

                    <select
                        id="reportType"
                        class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-3 text-[9px] outline-none focus:border-[#c99a3d]"
                    >
                        <option value="all">All Reports</option>
                        <option value="sales">Sales Report</option>
                        <option value="profit">Profit Report</option>
                        <option value="orders">Order Performance</option>
                        <option value="products">Product Performance</option>
                    </select>
                </div>


                <div class="flex items-end">
                    <button
                        id="generateSellerReport"
                        type="button"
                        class="h-11 w-full rounded-xl bg-[#c99128] px-4 text-[9px] font-semibold text-white transition hover:bg-[#b47e1e]"
                    >
                        Generate
                    </button>
                </div>

            </div>

        </div>


        <div class="mt-4 flex flex-wrap gap-2 border-t border-[#eee8df] pt-4">
            <button data-report-range="today" class="report-range rounded-full border border-[#e4ddd3] bg-white px-3 py-1.5 text-[8px] font-semibold text-[#756d62]">
                Today
            </button>

            <button data-report-range="7days" class="report-range rounded-full border border-[#e4ddd3] bg-white px-3 py-1.5 text-[8px] font-semibold text-[#756d62]">
                Last 7 Days
            </button>

            <button data-report-range="30days" class="report-range rounded-full bg-[#c99128] px-3 py-1.5 text-[8px] font-semibold text-white">
                Last 30 Days
            </button>

            <button data-report-range="month" class="report-range rounded-full border border-[#e4ddd3] bg-white px-3 py-1.5 text-[8px] font-semibold text-[#756d62]">
                This Month
            </button>

            <button data-report-range="year" class="report-range rounded-full border border-[#e4ddd3] bg-white px-3 py-1.5 text-[8px] font-semibold text-[#756d62]">
                This Year
            </button>
        </div>
    </section>


    {{-- =========================================================
        FINANCIAL SUMMARY
    ========================================================== --}}
    <section class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-[18px] border border-[#d8e6dd] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-medium text-[#797168]">
                        Gross Sales
                    </p>

                    <h3 class="mt-2 text-[24px] font-bold tracking-[-0.04em] text-[#211d18]">
                        ₱186,420
                    </h3>

                    <p class="mt-2 text-[9px] font-medium text-[#56816a]">
                        ▲ 18.3% vs previous period
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#d5e5dc] bg-[#f1f7f3] text-[#56816a]">
                    <span class="text-[17px] font-semibold">₱</span>
                </div>
            </div>
        </div>


        <div class="rounded-[18px] border border-[#eadfc9] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-medium text-[#797168]">
                        Platform Commission
                    </p>

                    <h3 class="mt-2 text-[24px] font-bold tracking-[-0.04em] text-[#211d18]">
                        ₱18,642
                    </h3>

                    <p class="mt-2 text-[9px] text-[#ad781c]">
                        10% platform rate
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#eadfc8] bg-[#fbf6ec] text-[#b98020]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <circle cx="12" cy="12" r="8"></circle>
                        <path d="M8 8h8"></path>
                        <path d="M8 16h8"></path>
                        <path d="M9 8c0 4 6 4 6 8"></path>
                    </svg>
                </div>
            </div>
        </div>


        <div class="rounded-[18px] border border-[#dce5ed] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-medium text-[#797168]">
                        Net Revenue
                    </p>

                    <h3 class="mt-2 text-[24px] font-bold tracking-[-0.04em] text-[#211d18]">
                        ₱167,778
                    </h3>

                    <p class="mt-2 text-[9px] text-[#647f97]">
                        After commission
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#d9e3ec] bg-[#f3f7fa] text-[#627f99]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M4 18 9 13l3 3 7-8"></path>
                        <path d="M15 8h4v4"></path>
                    </svg>
                </div>
            </div>
        </div>


        <div class="rounded-[18px] border border-[#e5dfea] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-medium text-[#797168]">
                        Completed Orders
                    </p>

                    <h3 class="mt-2 text-[24px] font-bold tracking-[-0.04em] text-[#211d18]">
                        184
                    </h3>

                    <p class="mt-2 text-[9px] text-[#7c6a8c]">
                        94% completion rate
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#e0d9e6] bg-[#f6f2f8] text-[#806a91]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M5 7h14l-1 13H6L5 7Z"></path>
                        <path d="m9 13 2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

    </section>


    {{-- =========================================================
        CHARTS
    ========================================================== --}}
    <section class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-[1.35fr_.65fr]">

        {{-- SALES TREND --}}
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h3 class="text-[16px] font-bold text-[#28221b]">
                        Sales & Profit Trend
                    </h3>

                    <p class="mt-1 text-[9px] text-[#91887d]">
                        Sales performance for the selected report period.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 text-[8px]">
                    <span class="inline-flex items-center gap-1.5 text-[#7b7268]">
                        <span class="h-2 w-5 rounded-full bg-[#c99128]"></span>
                        Sales
                    </span>

                    <span class="inline-flex items-center gap-1.5 text-[#7b7268]">
                        <span class="h-2 w-5 rounded-full bg-[#aebbc7]"></span>
                        Net Revenue
                    </span>
                </div>
            </div>


            <div class="mt-5 overflow-hidden rounded-[18px] border border-[#efebe4] bg-[#fcfbf8] p-4">
                <div class="overflow-x-auto">
                    <svg viewBox="0 0 850 300" class="h-[290px] min-w-[700px] w-full" fill="none">

                        <line x1="65" y1="35" x2="820" y2="35" stroke="#EAE5DE"/>
                        <line x1="65" y1="85" x2="820" y2="85" stroke="#EAE5DE"/>
                        <line x1="65" y1="135" x2="820" y2="135" stroke="#EAE5DE"/>
                        <line x1="65" y1="185" x2="820" y2="185" stroke="#EAE5DE"/>
                        <line x1="65" y1="235" x2="820" y2="235" stroke="#E4DED6"/>

                        <text x="10" y="39" fill="#A1988D" font-size="10">₱60K</text>
                        <text x="10" y="89" fill="#A1988D" font-size="10">₱45K</text>
                        <text x="10" y="139" fill="#A1988D" font-size="10">₱30K</text>
                        <text x="10" y="189" fill="#A1988D" font-size="10">₱15K</text>
                        <text x="10" y="239" fill="#A1988D" font-size="10">₱0</text>

                        <path
                            d="M70 220 C130 210,155 188,210 194 S300 160,355 165 S445 127,500 139 S600 98,655 109 S760 72,815 84"
                            stroke="#C99128"
                            stroke-width="4"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />

                        <path
                            d="M70 230 C130 219,155 202,210 207 S300 178,355 182 S445 150,500 159 S600 125,655 136 S760 103,815 112"
                            stroke="#AEBBC7"
                            stroke-width="3"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-dasharray="7 7"
                        />

                        <circle cx="70" cy="220" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                        <circle cx="210" cy="194" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                        <circle cx="355" cy="165" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                        <circle cx="500" cy="139" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                        <circle cx="655" cy="109" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                        <circle cx="815" cy="84" r="5" fill="#C99128"/>

                        <text x="55" y="275" fill="#9B9388" font-size="10">Aug 1</text>
                        <text x="195" y="275" fill="#9B9388" font-size="10">Aug 4</text>
                        <text x="340" y="275" fill="#9B9388" font-size="10">Aug 8</text>
                        <text x="485" y="275" fill="#9B9388" font-size="10">Aug 12</text>
                        <text x="640" y="275" fill="#9B9388" font-size="10">Aug 15</text>
                        <text x="790" y="275" fill="#9B9388" font-size="10">Aug 18</text>
                    </svg>
                </div>
            </div>
        </div>


        {{-- PERFORMANCE SUMMARY --}}
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
            <div>
                <h3 class="text-[16px] font-bold text-[#28221b]">
                    Performance Summary
                </h3>

                <p class="mt-1 text-[9px] text-[#91887d]">
                    Core seller performance indicators.
                </p>
            </div>


            <div class="mt-5 space-y-3">

                <div class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[9px] font-semibold text-[#514a42]">
                            Order Completion Rate
                        </span>

                        <span class="text-[10px] font-bold text-[#56816a]">
                            94%
                        </span>
                    </div>

                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-[#ece8e2]">
                        <div class="h-full w-[94%] rounded-full bg-[#84a991]"></div>
                    </div>
                </div>


                <div class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[9px] font-semibold text-[#514a42]">
                            On-Time Shipment
                        </span>

                        <span class="text-[10px] font-bold text-[#647f97]">
                            91%
                        </span>
                    </div>

                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-[#ece8e2]">
                        <div class="h-full w-[91%] rounded-full bg-[#9eb1c1]"></div>
                    </div>
                </div>


                <div class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[9px] font-semibold text-[#514a42]">
                            Customer Rating
                        </span>

                        <span class="text-[10px] font-bold text-[#a8731f]">
                            4.8 / 5
                        </span>
                    </div>

                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-[#ece8e2]">
                        <div class="h-full w-[96%] rounded-full bg-[#c99128]"></div>
                    </div>
                </div>


                <div class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[9px] font-semibold text-[#514a42]">
                            Response Rate
                        </span>

                        <span class="text-[10px] font-bold text-[#7c6a8c]">
                            96%
                        </span>
                    </div>

                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-[#ece8e2]">
                        <div class="h-full w-[96%] rounded-full bg-[#9f90aa]"></div>
                    </div>
                </div>

            </div>
        </div>

    </section>


    {{-- =========================================================
        PRODUCT PERFORMANCE + ORDER BREAKDOWN
    ========================================================== --}}
    <section class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-2">

        {{-- TOP PRODUCTS --}}
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-[16px] font-bold text-[#28221b]">
                        Top Selling Products
                    </h3>

                    <p class="mt-1 text-[9px] text-[#91887d]">
                        Highest-performing products for the selected period.
                    </p>
                </div>

                <span class="rounded-full border border-[#e7dfd4] bg-[#fcfaf7] px-3 py-1 text-[8px] font-semibold text-[#7b7267]">
                    By Sales
                </span>
            </div>


            <div class="mt-5 space-y-3">

                @php
                    $topProducts = [
                        ['name'=>'Wireless Earbuds Pro','sold'=>'42 sold','sales'=>'₱54,558','width'=>'w-[92%]'],
                        ['name'=>'Organic Daily Soap Set','sold'=>'38 sold','sales'=>'₱15,162','width'=>'w-[78%]'],
                        ['name'=>'Minimal Desk Lamp','sold'=>'31 sold','sales'=>'₱27,869','width'=>'w-[67%]'],
                        ['name'=>'Classic Canvas Tote Bag','sold'=>'27 sold','sales'=>'₱14,823','width'=>'w-[58%]'],
                        ['name'=>'Hardbound Journal','sold'=>'19 sold','sales'=>'₱6,251','width'=>'w-[42%]'],
                    ];
                @endphp

                @foreach($topProducts as $index => $product)
                    <div class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                        <div class="flex items-center gap-3">
                            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-[9px] font-bold text-[#a8731f]">
                                {{ $index + 1 }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-[9px] font-semibold text-[#453e36]">
                                            {{ $product['name'] }}
                                        </p>

                                        <p class="mt-1 text-[8px] text-[#958c80]">
                                            {{ $product['sold'] }}
                                        </p>
                                    </div>

                                    <span class="text-[9px] font-bold text-[#514a42]">
                                        {{ $product['sales'] }}
                                    </span>
                                </div>

                                <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-[#ece8e2]">
                                    <div class="h-full {{ $product['width'] }} rounded-full bg-[#c9a052]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>


        {{-- ORDER BREAKDOWN --}}
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
            <div>
                <h3 class="text-[16px] font-bold text-[#28221b]">
                    Order Breakdown
                </h3>

                <p class="mt-1 text-[9px] text-[#91887d]">
                    Order activity during the selected report period.
                </p>
            </div>


            <div class="mt-5 grid grid-cols-2 gap-3">
                <div class="rounded-[16px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <p class="text-[8px] text-[#958c80]">Total Orders</p>
                    <p class="mt-2 text-[20px] font-bold text-[#302a24]">196</p>
                </div>

                <div class="rounded-[16px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <p class="text-[8px] text-[#958c80]">Completed</p>
                    <p class="mt-2 text-[20px] font-bold text-[#56816a]">184</p>
                </div>

                <div class="rounded-[16px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <p class="text-[8px] text-[#958c80]">Cancelled</p>
                    <p class="mt-2 text-[20px] font-bold text-[#ad6767]">7</p>
                </div>

                <div class="rounded-[16px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <p class="text-[8px] text-[#958c80]">Returned</p>
                    <p class="mt-2 text-[20px] font-bold text-[#a8731f]">5</p>
                </div>
            </div>


            <div class="mt-4 rounded-[16px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                <div class="flex items-center justify-between text-[8px]">
                    <span class="font-medium text-[#756d63]">Successful Orders</span>
                    <span class="font-bold text-[#56816a]">94%</span>
                </div>

                <div class="mt-3 h-2 overflow-hidden rounded-full bg-[#ece8e2]">
                    <div class="h-full w-[94%] rounded-full bg-[#84a991]"></div>
                </div>
            </div>


            <div class="mt-4 rounded-[16px] border border-[#eadfc9] bg-[#fcfaf6] p-4">
                <div class="flex items-start gap-3">
                    <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0 text-[#a8731f]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="8"></circle>
                        <path d="M12 8v4"></path>
                        <path d="M12 16h.01"></path>
                    </svg>

                    <p class="text-[8px] leading-4 text-[#8e7c61]">
                        Completion rate is calculated from orders completed within the selected date period.
                    </p>
                </div>
            </div>
        </div>

    </section>


    {{-- =========================================================
        FINANCIAL BREAKDOWN
    ========================================================== --}}
    <section class="mt-5 overflow-hidden rounded-[22px] border border-[#ebe4da] bg-white">

        <div class="flex flex-col gap-3 border-b border-[#eee8df] p-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-[16px] font-bold text-[#28221b]">
                    Financial Breakdown
                </h3>

                <p class="mt-1 text-[9px] text-[#91887d]">
                    Transaction-level seller earnings and commission overview.
                </p>
            </div>

            <select class="h-10 rounded-xl border border-[#e6dfd5] bg-white px-3 text-[9px] text-[#625a50] outline-none">
                <option>All Transactions</option>
                <option>Paid</option>
                <option>Pending</option>
                <option>Refunded</option>
            </select>
        </div>


        <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px] text-left">
                <thead>
                    <tr class="border-b border-[#eee8df] bg-[#fcfaf7] text-[8px] font-semibold uppercase tracking-[0.08em] text-[#9b9287]">
                        <th class="px-5 py-4">Order</th>
                        <th class="px-5 py-4">Date</th>
                        <th class="px-5 py-4">Gross Sales</th>
                        <th class="px-5 py-4">Commission</th>
                        <th class="px-5 py-4">Net Revenue</th>
                        <th class="px-5 py-4">Payment</th>
                        <th class="px-5 py-4 text-right">Status</th>
                    </tr>
                </thead>

                <tbody class="text-[9px]">

                    <tr class="border-b border-[#f1ece5] hover:bg-[#fdfbf8]">
                        <td class="px-5 py-4 font-semibold text-[#403a33]">#ORD-8452</td>
                        <td class="px-5 py-4 text-[#625a50]">Aug 18, 2026</td>
                        <td class="px-5 py-4 font-semibold text-[#403a33]">₱2,480</td>
                        <td class="px-5 py-4 text-[#a8731f]">₱248</td>
                        <td class="px-5 py-4 font-semibold text-[#56816a]">₱2,232</td>
                        <td class="px-5 py-4 text-[#625a50]">Online Payment</td>
                        <td class="px-5 py-4 text-right">
                            <span class="rounded-full bg-[#eef6f1] px-2.5 py-1 text-[8px] font-semibold text-[#56816a]">
                                Paid
                            </span>
                        </td>
                    </tr>


                    <tr class="border-b border-[#f1ece5] hover:bg-[#fdfbf8]">
                        <td class="px-5 py-4 font-semibold text-[#403a33]">#ORD-8451</td>
                        <td class="px-5 py-4 text-[#625a50]">Aug 18, 2026</td>
                        <td class="px-5 py-4 font-semibold text-[#403a33]">₱899</td>
                        <td class="px-5 py-4 text-[#a8731f]">₱89.90</td>
                        <td class="px-5 py-4 font-semibold text-[#56816a]">₱809.10</td>
                        <td class="px-5 py-4 text-[#625a50]">Online Payment</td>
                        <td class="px-5 py-4 text-right">
                            <span class="rounded-full bg-[#eef6f1] px-2.5 py-1 text-[8px] font-semibold text-[#56816a]">
                                Paid
                            </span>
                        </td>
                    </tr>


                    <tr class="border-b border-[#f1ece5] hover:bg-[#fdfbf8]">
                        <td class="px-5 py-4 font-semibold text-[#403a33]">#ORD-8450</td>
                        <td class="px-5 py-4 text-[#625a50]">Aug 18, 2026</td>
                        <td class="px-5 py-4 font-semibold text-[#403a33]">₱1,560</td>
                        <td class="px-5 py-4 text-[#a8731f]">₱156</td>
                        <td class="px-5 py-4 font-semibold text-[#56816a]">₱1,404</td>
                        <td class="px-5 py-4 text-[#625a50]">COD</td>
                        <td class="px-5 py-4 text-right">
                            <span class="rounded-full bg-[#fbf3e7] px-2.5 py-1 text-[8px] font-semibold text-[#a97724]">
                                Pending
                            </span>
                        </td>
                    </tr>


                    <tr class="border-b border-[#f1ece5] hover:bg-[#fdfbf8]">
                        <td class="px-5 py-4 font-semibold text-[#403a33]">#ORD-8449</td>
                        <td class="px-5 py-4 text-[#625a50]">Aug 17, 2026</td>
                        <td class="px-5 py-4 font-semibold text-[#403a33]">₱3,280</td>
                        <td class="px-5 py-4 text-[#a8731f]">₱328</td>
                        <td class="px-5 py-4 font-semibold text-[#56816a]">₱2,952</td>
                        <td class="px-5 py-4 text-[#625a50]">Online Payment</td>
                        <td class="px-5 py-4 text-right">
                            <span class="rounded-full bg-[#eef6f1] px-2.5 py-1 text-[8px] font-semibold text-[#56816a]">
                                Paid
                            </span>
                        </td>
                    </tr>


                    <tr class="hover:bg-[#fdfbf8]">
                        <td class="px-5 py-4 font-semibold text-[#403a33]">#ORD-8448</td>
                        <td class="px-5 py-4 text-[#625a50]">Aug 17, 2026</td>
                        <td class="px-5 py-4 font-semibold text-[#403a33]">₱1,150</td>
                        <td class="px-5 py-4 text-[#a8731f]">₱115</td>
                        <td class="px-5 py-4 font-semibold text-[#56816a]">₱1,035</td>
                        <td class="px-5 py-4 text-[#625a50]">Online Payment</td>
                        <td class="px-5 py-4 text-right">
                            <span class="rounded-full bg-[#eef6f1] px-2.5 py-1 text-[8px] font-semibold text-[#56816a]">
                                Paid
                            </span>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>


        <div class="flex items-center justify-between border-t border-[#eee8df] bg-[#fcfaf7] px-5 py-4">
            <p class="text-[8px] text-[#91887d]">
                Showing 5 recent transactions
            </p>

            <button class="text-[8px] font-semibold text-[#a8731f] hover:text-[#805513]">
                View All Transactions →
            </button>
        </div>

    </section>


    {{-- =========================================================
        GENERATED REPORTS
    ========================================================== --}}
    <section class="mt-5 rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-[16px] font-bold text-[#28221b]">
                    Generated Reports
                </h3>

                <p class="mt-1 text-[9px] text-[#91887d]">
                    Recently generated seller reports.
                </p>
            </div>

            <span class="rounded-full border border-[#e7dfd4] bg-[#fcfaf7] px-3 py-1.5 text-[8px] font-semibold text-[#7b7267]">
                4 reports
            </span>
        </div>


        <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">

            @php
                $reports = [
                    ['title'=>'Monthly Sales Report','date'=>'Aug 1 - Aug 18, 2026','type'=>'Sales'],
                    ['title'=>'Profit Summary','date'=>'Jul 1 - Jul 31, 2026','type'=>'Financial'],
                    ['title'=>'Product Performance','date'=>'Jul 1 - Jul 31, 2026','type'=>'Products'],
                    ['title'=>'Order Performance','date'=>'Jul 1 - Jul 31, 2026','type'=>'Orders'],
                ];
            @endphp

            @foreach($reports as $report)
                <div class="rounded-[16px] border border-[#e9e3da] bg-[#fcfbf8] p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="grid h-10 w-10 place-items-center rounded-xl bg-white text-[#a8731f]">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M5 3h10l4 4v14H5z"></path>
                                <path d="M15 3v5h5"></path>
                                <path d="M9 13h6"></path>
                                <path d="M9 17h4"></path>
                            </svg>
                        </div>

                        <span class="rounded-full bg-white px-2 py-1 text-[7px] font-semibold text-[#81786e]">
                            {{ $report['type'] }}
                        </span>
                    </div>

                    <p class="mt-4 text-[9px] font-semibold text-[#3d3730]">
                        {{ $report['title'] }}
                    </p>

                    <p class="mt-1 text-[8px] text-[#958c80]">
                        {{ $report['date'] }}
                    </p>

                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <button class="rounded-lg border border-[#e0d8cd] bg-white px-3 py-2 text-[8px] font-semibold text-[#675f55]">
                            View
                        </button>

                        <button class="rounded-lg border border-[#e0d8cd] bg-white px-3 py-2 text-[8px] font-semibold text-[#675f55]">
                            Download
                        </button>
                    </div>
                </div>
            @endforeach

        </div>
    </section>


    <div class="h-5"></div>
</div>


{{-- =============================================================
    REPORT DEMO MODAL
============================================================== --}}
<div
    id="sellerReportModal"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/35 p-4 backdrop-blur-[2px]"
>
    <div class="w-full max-w-[450px] rounded-[22px] border border-[#e9dfcf] bg-white p-5 shadow-[0_30px_90px_rgba(38,30,18,0.22)] sm:p-6">

        <div class="flex items-start justify-between gap-4">
            <div>
                <span class="rounded-full bg-[#fbf5e9] px-2.5 py-1 text-[8px] font-semibold uppercase tracking-[0.1em] text-[#a8731f]">
                    Report
                </span>

                <h3 id="sellerReportModalTitle" class="mt-3 text-[18px] font-bold text-[#28221b]">
                    Report Generated
                </h3>
            </div>

            <button
                id="sellerReportModalClose"
                type="button"
                class="grid h-9 w-9 place-items-center rounded-xl border border-[#e6dfd5] text-[#756d63]"
            >
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 6l12 12"></path>
                    <path d="M18 6 6 18"></path>
                </svg>
            </button>
        </div>

        <p id="sellerReportModalText" class="mt-4 text-[9px] leading-5 text-[#756d63]">
            Your seller report is ready.
        </p>

        <button
            id="sellerReportModalDone"
            type="button"
            class="mt-5 w-full rounded-xl bg-[#c99128] px-4 py-3 text-[9px] font-semibold text-white transition hover:bg-[#b47e1e]"
        >
            Done
        </button>

    </div>
</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const fromDate = document.getElementById('reportFromDate');
    const toDate = document.getElementById('reportToDate');
    const reportType = document.getElementById('reportType');

    const modal = document.getElementById('sellerReportModal');
    const modalTitle = document.getElementById('sellerReportModalTitle');
    const modalText = document.getElementById('sellerReportModalText');

    const modalClose = document.getElementById('sellerReportModalClose');
    const modalDone = document.getElementById('sellerReportModalDone');


    function openReportModal(title, text) {

        if (modalTitle) {
            modalTitle.textContent = title;
        }

        if (modalText) {
            modalText.textContent = text;
        }

        modal?.classList.remove('hidden');
        modal?.classList.add('flex');

        document.body.classList.add('overflow-hidden');
    }


    function closeReportModal() {

        modal?.classList.add('hidden');
        modal?.classList.remove('flex');

        document.body.classList.remove('overflow-hidden');
    }


    document.getElementById('generateSellerReport')?.addEventListener(
        'click',
        function () {

            if (!fromDate?.value || !toDate?.value) {

                openReportModal(
                    'Select a Date Range',
                    'Please choose both the From Date and To Date before generating a seller report.'
                );

                return;
            }


            if (new Date(fromDate.value) > new Date(toDate.value)) {

                openReportModal(
                    'Invalid Date Range',
                    'The From Date cannot be later than the To Date.'
                );

                return;
            }


            const selectedType =
                reportType?.options[
                    reportType.selectedIndex
                ]?.text || 'Seller Report';


            openReportModal(
                'Report Generated',
                selectedType +
                ' has been generated for ' +
                fromDate.value +
                ' to ' +
                toDate.value +
                '. This is a frontend demonstration for now.'
            );

        }
    );


    document.getElementById('downloadReportButton')?.addEventListener(
        'click',
        function () {
            openReportModal(
                'Download Report',
                'The report download button is ready to connect to PDF, CSV, or Excel export later.'
            );
        }
    );


    document.getElementById('printReportButton')?.addEventListener(
        'click',
        function () {
            openReportModal(
                'Print Report',
                'This button can later open a print-friendly seller report or browser print dialog.'
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | QUICK DATE RANGE BUTTONS
    |--------------------------------------------------------------------------
    */

    const rangeButtons =
        Array.from(
            document.querySelectorAll('[data-report-range]')
        );


    function formatDate(date) {

        const year = date.getFullYear();

        const month =
            String(date.getMonth() + 1)
                .padStart(2, '0');

        const day =
            String(date.getDate())
                .padStart(2, '0');


        return `${year}-${month}-${day}`;
    }


    function setRange(type) {

        const today = new Date(2026, 7, 18);

        let start = new Date(today);
        let end = new Date(today);


        if (type === '7days') {
            start.setDate(today.getDate() - 6);
        }


        if (type === '30days') {
            start.setDate(today.getDate() - 29);
        }


        if (type === 'month') {
            start = new Date(today.getFullYear(), today.getMonth(), 1);
        }


        if (type === 'year') {
            start = new Date(today.getFullYear(), 0, 1);
        }


        if (fromDate) {
            fromDate.value = formatDate(start);
        }


        if (toDate) {
            toDate.value = formatDate(end);
        }


        rangeButtons.forEach(function (button) {

            const active =
                button.dataset.reportRange === type;


            button.classList.toggle(
                'bg-[#c99128]',
                active
            );

            button.classList.toggle(
                'text-white',
                active
            );

            button.classList.toggle(
                'border',
                !active
            );

            button.classList.toggle(
                'border-[#e4ddd3]',
                !active
            );

            button.classList.toggle(
                'bg-white',
                !active
            );

            button.classList.toggle(
                'text-[#756d62]',
                !active
            );

        });

    }


    rangeButtons.forEach(function (button) {

        button.addEventListener(
            'click',
            function () {
                setRange(
                    this.dataset.reportRange
                );
            }
        );

    });


    modalClose?.addEventListener(
        'click',
        closeReportModal
    );


    modalDone?.addEventListener(
        'click',
        closeReportModal
    );


    modal?.addEventListener(
        'click',
        function (event) {

            if (event.target === modal) {
                closeReportModal();
            }

        }
    );


    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {
                closeReportModal();
            }

        }
    );

});
</script>

@endpush