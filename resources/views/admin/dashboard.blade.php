@extends('layouts.admin')

@section('title', 'Dashboard — SARI Admin')
@section('page-title', 'Dashboard Overview')

@section('content')

<div class="mx-auto w-full max-w-[1800px]">

    {{-- =========================================================
        WELCOME / OVERVIEW BANNER
    ========================================================== --}}
    <section
        class="
            relative overflow-hidden
            rounded-[22px]
            border border-[#e9e2d8]
            bg-[#f8f5ef]
            px-5 py-5

            sm:px-6 sm:py-6
            lg:px-8
        "
    >

        <div class="relative z-10 max-w-[680px]">

            <div
                class="
                    inline-flex items-center gap-2
                    rounded-full
                    border border-[#e6ddd0]
                    bg-white/90
                    px-3 py-1.5
                    text-[10px] font-semibold
                    uppercase tracking-[0.16em]
                    text-[#9b7b3e]
                "
            >
                <span class="h-2 w-2 rounded-full bg-[#c9962f]"></span>
                SARI Marketplace
            </div>

            <h2
                class="
                    mt-4
                    text-[22px] font-bold
                    tracking-[-0.03em]
                    text-[#1f1a14]

                    sm:text-[25px]
                    lg:text-[27px]
                "
            >
                Good morning, Admin! 👋
            </h2>

            <p
                class="
                    mt-2
                    max-w-[580px]
                    text-[12px] leading-6
                    text-[#756d62]

                    sm:text-[13px]
                "
            >
                Here’s a clean overview of your marketplace performance,
                registrations, seller activity, complaints, and commission.
            </p>

            <div
                class="
                    mt-4
                    inline-flex items-center gap-2
                    rounded-xl
                    border border-[#e7ded2]
                    bg-white/90
                    px-4 py-2.5
                    text-[11px] font-medium
                    text-[#6a6257]
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-4 w-4 text-[#c9962f]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="m12 3 2.2 4.7L19 10l-4.8 2.2L12 17l-2.2-4.8L5 10l4.8-2.3L12 3Z"></path>
                </svg>

                Keep track of the marketplace with one clear dashboard.
            </div>

        </div>

        {{-- Subtle decorative shapes --}}
        <div
            class="
                pointer-events-none
                absolute -right-12 -top-16
                h-[175px] w-[175px]
                rounded-full
                border-[24px]
                border-[#eee7dc]
            "
        ></div>

        <div
            class="
                pointer-events-none
                absolute -bottom-16 right-[9%]
                h-[115px] w-[230px]
                rounded-t-full
                bg-[#efe6d7]/75
            "
        ></div>

        <div
            class="
                pointer-events-none
                absolute right-[27%] top-6
                hidden h-3 w-3
                rotate-45
                border border-[#d8bd87]
                lg:block
            "
        ></div>

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
            lg:grid-cols-3
            2xl:grid-cols-6
        "
    >

        {{-- TOTAL USERS --}}
        <div
            class="
                group
                rounded-[20px]
                border border-[#d9e8dd]
                bg-white
                p-5
                transition duration-200

                hover:-translate-y-0.5
                hover:border-[#c7ddce]
                hover:shadow-sm

                xl:p-6
            "
        >

            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-[12px] font-medium text-[#746f67]">
                        Total Users
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#1d1b18]

                            xl:text-[29px]
                        "
                    >
                        12,458
                    </h3>
                </div>

                <div
                    class="
                        grid h-12 w-12
                        shrink-0 place-items-center
                        rounded-[14px]
                        border border-[#d7e8dc]
                        bg-[#f2f8f3]
                        text-[#4b8f60]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[22px] w-[22px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <circle cx="9" cy="8" r="3"></circle>
                        <circle cx="17" cy="10" r="2.5"></circle>
                        <path d="M3 20c.4-4 2.6-6 6-6s5.6 2 6 6"></path>
                        <path d="M15 15c3.5 0 5.5 1.8 6 5"></path>
                    </svg>
                </div>

            </div>

            <div class="mt-5 flex items-center gap-2 text-[11px]">
                <span class="font-semibold text-[#4a8d60]">
                    ▲ 12.5%
                </span>

                <span class="text-[#989188]">
                    vs last month
                </span>
            </div>

        </div>


        {{-- PENDING REGISTRATIONS --}}
        <div
            class="
                group
                rounded-[20px]
                border border-[#eadfc9]
                bg-white
                p-5
                transition duration-200

                hover:-translate-y-0.5
                hover:border-[#dfcfb0]
                hover:shadow-sm

                xl:p-6
            "
        >

            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-[12px] font-medium text-[#746f67]">
                        Pending Registrations
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#1d1b18]

                            xl:text-[29px]
                        "
                    >
                        128
                    </h3>
                </div>

                <div
                    class="
                        grid h-12 w-12
                        shrink-0 place-items-center
                        rounded-[14px]
                        border border-[#eadfc8]
                        bg-[#fbf6ec]
                        text-[#bf8420]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[22px] w-[22px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                        <path d="M9 4.5V3h6v1.5"></path>
                        <path d="M9 9h6"></path>
                        <path d="M9 13h6"></path>
                        <path d="M9 17h3"></path>
                    </svg>
                </div>

            </div>

            <div class="mt-5 flex items-center gap-2 text-[11px]">
                <span class="font-semibold text-[#b87d18]">
                    28 new
                </span>

                <span class="text-[#989188]">
                    awaiting review
                </span>
            </div>

        </div>


        {{-- ACTIVE SELLERS --}}
        <div
            class="
                group
                rounded-[20px]
                border border-[#d3e5df]
                bg-white
                p-5
                transition duration-200

                hover:-translate-y-0.5
                hover:border-[#bdd8cf]
                hover:shadow-sm

                xl:p-6
            "
        >

            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-[12px] font-medium text-[#746f67]">
                        Active Sellers
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#1d1b18]

                            xl:text-[29px]
                        "
                    >
                        2,345
                    </h3>
                </div>

                <div
                    class="
                        grid h-12 w-12
                        shrink-0 place-items-center
                        rounded-[14px]
                        border border-[#d2e7df]
                        bg-[#f0f8f5]
                        text-[#438879]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[22px] w-[22px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <path d="M4 9h16"></path>
                        <path d="M5 9 7 4h10l2 5"></path>
                        <path d="M6 9v11h12V9"></path>
                        <path d="M9 20v-6h6v6"></path>
                    </svg>
                </div>

            </div>

            <div class="mt-5 flex items-center gap-2 text-[11px]">
                <span class="font-semibold text-[#438879]">
                    ▲ 9.6%
                </span>

                <span class="text-[#989188]">
                    vs last month
                </span>
            </div>

        </div>


        {{-- TOTAL ORDERS --}}
        <div
            class="
                group
                rounded-[20px]
                border border-[#d9e1ea]
                bg-white
                p-5
                transition duration-200

                hover:-translate-y-0.5
                hover:border-[#c6d2df]
                hover:shadow-sm

                xl:p-6
            "
        >

            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-[12px] font-medium text-[#746f67]">
                        Total Orders
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#1d1b18]

                            xl:text-[29px]
                        "
                    >
                        8,765
                    </h3>
                </div>

                <div
                    class="
                        grid h-12 w-12
                        shrink-0 place-items-center
                        rounded-[14px]
                        border border-[#d7e1eb]
                        bg-[#f3f7fb]
                        text-[#5b7fa3]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[22px] w-[22px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <path d="M5 7h14l-1 13H6L5 7Z"></path>
                        <path d="M9 7a3 3 0 0 1 6 0"></path>
                    </svg>
                </div>

            </div>

            <div class="mt-5 flex items-center gap-2 text-[11px]">
                <span class="font-semibold text-[#587b9f]">
                    ▲ 15.8%
                </span>

                <span class="text-[#989188]">
                    vs last month
                </span>
            </div>

        </div>


        {{-- OPEN COMPLAINTS --}}
        <div
            class="
                group
                rounded-[20px]
                border border-[#ead9d9]
                bg-white
                p-5
                transition duration-200

                hover:-translate-y-0.5
                hover:border-[#ddc4c4]
                hover:shadow-sm

                xl:p-6
            "
        >

            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-[12px] font-medium text-[#746f67]">
                        Open Complaints
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#1d1b18]

                            xl:text-[29px]
                        "
                    >
                        23
                    </h3>
                </div>

                <div
                    class="
                        grid h-12 w-12
                        shrink-0 place-items-center
                        rounded-[14px]
                        border border-[#ead7d7]
                        bg-[#fbf3f3]
                        text-[#b96464]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[22px] w-[22px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <path d="M12 3 3 20h18L12 3Z"></path>
                        <path d="M12 9v5"></path>
                        <path d="M12 17h.01"></path>
                    </svg>
                </div>

            </div>

            <div class="mt-5 flex items-center gap-2 text-[11px]">
                <span class="font-semibold text-[#b75f5f]">
                    8 urgent
                </span>

                <span class="text-[#989188]">
                    need attention
                </span>
            </div>

        </div>


        {{-- PLATFORM COMMISSION --}}
        <div
            class="
                group
                rounded-[20px]
                border border-[#e2dce8]
                bg-white
                p-5
                transition duration-200

                hover:-translate-y-0.5
                hover:border-[#d2c8dc]
                hover:shadow-sm

                xl:p-6
            "
        >

            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0">
                    <p class="text-[12px] font-medium text-[#746f67]">
                        Platform Commission
                    </p>

                    <h3
                        class="
                            mt-2
                            truncate
                            text-[24px] font-bold
                            tracking-[-0.04em]
                            text-[#1d1b18]

                            xl:text-[26px]
                        "
                    >
                        ₱245,680
                    </h3>
                </div>

                <div
                    class="
                        grid h-12 w-12
                        shrink-0 place-items-center
                        rounded-[14px]
                        border border-[#e1d9e9]
                        bg-[#f7f3fa]
                        text-[#80649a]
                    "
                >
                    <span class="text-[19px] font-semibold">
                        ₱
                    </span>
                </div>

            </div>

            <div class="mt-5 flex items-center gap-2 text-[11px]">
                <span class="font-semibold text-[#80649a]">
                    10%
                </span>

                <span class="text-[#989188]">
                    platform rate
                </span>
            </div>

        </div>

    </section>


    {{-- =========================================================
        MAIN ANALYTICS
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-5

            xl:grid-cols-[1.35fr_.65fr]
        "
    >

        {{-- SALES OVERVIEW --}}
        <div
            class="
                rounded-[22px]
                border border-[#ebe5dc]
                bg-white
                p-5

                sm:p-6
            "
        >

            <div
                class="
                    flex flex-col
                    gap-4

                    sm:flex-row
                    sm:items-start
                    sm:justify-between
                "
            >

                <div>
                    <h3
                        class="
                            text-[16px] font-bold
                            tracking-[-0.02em]
                            text-[#211d17]
                        "
                    >
                        Sales Overview
                    </h3>

                    <p class="mt-1 text-[11px] text-[#978f84]">
                        Monthly marketplace activity
                    </p>
                </div>

                <button
                    type="button"
                    class="
                        inline-flex w-fit
                        items-center gap-2
                        rounded-xl
                        border border-[#e8e0d5]
                        bg-[#fcfbf9]
                        px-3.5 py-2.5
                        text-[11px] font-medium
                        text-[#625b52]
                        transition

                        hover:border-[#d9cbb7]
                        hover:bg-[#faf7f2]
                    "
                >
                    This Month

                    <svg
                        viewBox="0 0 24 24"
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="m8 10 4 4 4-4"></path>
                    </svg>
                </button>

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
                        rounded-[16px]
                        border border-[#efebe4]
                        bg-[#fcfbf8]
                        p-4
                    "
                >
                    <p
                        class="
                            text-[9px] font-semibold
                            uppercase tracking-[0.12em]
                            text-[#aaa196]
                        "
                    >
                        Orders
                    </p>

                    <p
                        class="
                            mt-2
                            text-[22px] font-bold
                            tracking-[-0.03em]
                            text-[#262018]
                        "
                    >
                        8,765
                    </p>
                </div>


                <div
                    class="
                        rounded-[16px]
                        border border-[#efebe4]
                        bg-[#fcfbf8]
                        p-4
                    "
                >
                    <p
                        class="
                            text-[9px] font-semibold
                            uppercase tracking-[0.12em]
                            text-[#aaa196]
                        "
                    >
                        Sales Growth
                    </p>

                    <p
                        class="
                            mt-2
                            text-[22px] font-bold
                            tracking-[-0.03em]
                            text-[#4d977f]
                        "
                    >
                        +24%
                    </p>
                </div>


                <div
                    class="
                        rounded-[16px]
                        border border-[#efebe4]
                        bg-[#fcfbf8]
                        p-4
                    "
                >
                    <p
                        class="
                            text-[9px] font-semibold
                            uppercase tracking-[0.12em]
                            text-[#aaa196]
                        "
                    >
                        Commission
                    </p>

                    <p
                        class="
                            mt-2
                            text-[22px] font-bold
                            tracking-[-0.03em]
                            text-[#262018]
                        "
                    >
                        ₱245K
                    </p>
                </div>

            </div>


            {{-- CLEAN SALES & COMMISSION LINE GRAPH --}}
            <div class="mt-6">

                {{-- Legend --}}
                <div
                    class="
                        mb-4 flex flex-wrap
                        items-center gap-x-5 gap-y-2
                        text-[10px] font-medium
                        text-[#7e776e]
                    "
                >
                    <div class="flex items-center gap-2">
                        <span class="h-[2px] w-5 rounded-full bg-[#c9962f]"></span>
                        Sales
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="h-[2px] w-5 rounded-full bg-[#aebbc7]"></span>
                        Commission
                    </div>

                    <div class="ml-auto hidden items-center gap-1.5 text-[#9a9288] sm:flex">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#c9962f]"></span>
                        Updated today
                    </div>
                </div>

                <div
                    class="
                        relative overflow-hidden
                        rounded-[18px]
                        border border-[#efebe4]
                        bg-[#fcfbf8]
                        p-3

                        sm:p-4
                    "
                >

                    <div class="overflow-x-auto">
                        <div class="min-w-[660px]">

                            <svg
                                viewBox="0 0 820 290"
                                class="h-[270px] w-full"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-label="Sales and commission trend chart"
                            >

                                {{-- Horizontal grid lines --}}
                                <line x1="60" y1="42" x2="790" y2="42" stroke="#EAE5DE" stroke-width="1"/>
                                <line x1="60" y1="95" x2="790" y2="95" stroke="#EAE5DE" stroke-width="1"/>
                                <line x1="60" y1="148" x2="790" y2="148" stroke="#EAE5DE" stroke-width="1"/>
                                <line x1="60" y1="201" x2="790" y2="201" stroke="#EAE5DE" stroke-width="1"/>
                                <line x1="60" y1="254" x2="790" y2="254" stroke="#E4DED6" stroke-width="1"/>

                                {{-- Y axis labels --}}
                                <text x="10" y="46" fill="#A1988D" font-size="10">₱250K</text>
                                <text x="10" y="99" fill="#A1988D" font-size="10">₱200K</text>
                                <text x="10" y="152" fill="#A1988D" font-size="10">₱150K</text>
                                <text x="10" y="205" fill="#A1988D" font-size="10">₱100K</text>
                                <text x="23" y="258" fill="#A1988D" font-size="10">₱50K</text>

                                {{-- Soft vertical guides --}}
                                <line x1="100" y1="42" x2="100" y2="254" stroke="#F0ECE6" stroke-width="1"/>
                                <line x1="260" y1="42" x2="260" y2="254" stroke="#F0ECE6" stroke-width="1"/>
                                <line x1="420" y1="42" x2="420" y2="254" stroke="#F0ECE6" stroke-width="1"/>
                                <line x1="580" y1="42" x2="580" y2="254" stroke="#F0ECE6" stroke-width="1"/>
                                <line x1="740" y1="42" x2="740" y2="254" stroke="#F0ECE6" stroke-width="1"/>

                                {{-- Commission line - muted blue gray --}}
                                <path
                                    d="M70 226
                                       C105 217, 115 212, 145 215
                                       S190 199, 220 201
                                       S270 192, 300 195
                                       S345 176, 380 181
                                       S430 164, 465 170
                                       S510 157, 545 161
                                       S595 144, 625 149
                                       S675 136, 705 140
                                       S755 120, 785 126"
                                    stroke="#AEBBC7"
                                    stroke-width="3"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-dasharray="5 7"
                                />

                                {{-- Sales line - soft gold --}}
                                <path
                                    d="M70 210
                                       C100 188, 118 180, 145 184
                                       S185 153, 220 158
                                       S265 177, 300 171
                                       S345 135, 380 145
                                       S425 122, 460 130
                                       S505 101, 540 110
                                       S585 84, 620 96
                                       S665 75, 700 82
                                       S750 46, 785 55"
                                    stroke="#C9962F"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />

                                {{-- Sales points --}}
                                <circle cx="70" cy="210" r="4" fill="#FCFBF8" stroke="#C9962F" stroke-width="2"/>
                                <circle cx="145" cy="184" r="4" fill="#FCFBF8" stroke="#C9962F" stroke-width="2"/>
                                <circle cx="220" cy="158" r="4" fill="#FCFBF8" stroke="#C9962F" stroke-width="2"/>
                                <circle cx="300" cy="171" r="4" fill="#FCFBF8" stroke="#C9962F" stroke-width="2"/>
                                <circle cx="380" cy="145" r="4" fill="#FCFBF8" stroke="#C9962F" stroke-width="2"/>
                                <circle cx="460" cy="130" r="4" fill="#FCFBF8" stroke="#C9962F" stroke-width="2"/>
                                <circle cx="540" cy="110" r="4" fill="#FCFBF8" stroke="#C9962F" stroke-width="2"/>
                                <circle cx="620" cy="96" r="4" fill="#FCFBF8" stroke="#C9962F" stroke-width="2"/>
                                <circle cx="700" cy="82" r="4" fill="#FCFBF8" stroke="#C9962F" stroke-width="2"/>
                                <circle cx="785" cy="55" r="5" fill="#C9962F"/>

                                {{-- Commission end point --}}
                                <circle cx="785" cy="126" r="4" fill="#AEBBC7"/>

                                {{-- X axis labels --}}
                                <text x="57" y="280" fill="#9B9388" font-size="10">May 1</text>
                                <text x="205" y="280" fill="#9B9388" font-size="10">May 5</text>
                                <text x="360" y="280" fill="#9B9388" font-size="10">May 10</text>
                                <text x="515" y="280" fill="#9B9388" font-size="10">May 15</text>
                                <text x="675" y="280" fill="#9B9388" font-size="10">May 20</text>

                            </svg>

                        </div>
                    </div>

                </div>

                {{-- Graph summary --}}
                <div
                    class="
                        mt-4 flex flex-col gap-2
                        rounded-[14px]
                        border border-[#eee8df]
                        bg-[#fdfbf7]
                        px-4 py-3

                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                    "
                >
                    <div>
                        <p class="text-[10px] text-[#938b80]">
                            Current sales
                        </p>
                        <p class="mt-1 text-[15px] font-bold text-[#29231c]">
                            ₱245,680
                        </p>
                    </div>

                    <div class="flex items-center gap-2 text-[10px] text-[#7d776d]">
                        <span
                            class="
                                rounded-full
                                bg-[#eef6f1]
                                px-2.5 py-1
                                font-semibold
                                text-[#4d8d68]
                            "
                        >
                            ▲ 18.7%
                        </span>

                        <span>
                            from last month
                        </span>
                    </div>
                </div>

            </div>

        </div>


        {{-- RECENT REGISTRATIONS --}}
        <div
            class="
                rounded-[22px]
                border border-[#ebe5dc]
                bg-white
                p-5

                sm:p-6
            "
        >

            <div class="flex items-start justify-between gap-4">

                <div>
                    <h3
                        class="
                            text-[16px] font-bold
                            tracking-[-0.02em]
                            text-[#211d17]
                        "
                    >
                        Recent Registrations
                    </h3>

                    <p class="mt-1 text-[11px] text-[#978f84]">
                        Accounts awaiting review
                    </p>
                </div>

                <button
                    type="button"
                    class="
                        whitespace-nowrap
                        rounded-lg
                        border border-[#e9e1d6]
                        px-3 py-2
                        text-[10px] font-semibold
                        text-[#756b5d]
                        transition

                        hover:border-[#d8c7aa]
                        hover:text-[#a67019]
                    "
                >
                    View All
                </button>

            </div>


            <div class="mt-5">

                {{-- REGISTRATION 1 --}}
                <div
                    class="
                        flex items-center
                        justify-between
                        gap-3
                        border-b border-[#f0ebe4]
                        py-3.5
                    "
                >

                    <div class="flex min-w-0 items-center gap-3">

                        <div
                            class="
                                grid h-10 w-10
                                shrink-0 place-items-center
                                rounded-full
                                bg-[#f2f6f9]
                                text-[11px] font-bold
                                text-[#667f97]
                            "
                        >
                            J
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-[12px] font-semibold text-[#28231d]">
                                Juan Dela Cruz
                            </p>

                            <p class="mt-0.5 text-[10px] text-[#978e82]">
                                Buyer
                            </p>
                        </div>

                    </div>

                    <span
                        class="
                            rounded-full
                            border border-[#eadcbe]
                            bg-[#fbf6eb]
                            px-2.5 py-1
                            text-[9px] font-semibold
                            text-[#ae781b]
                        "
                    >
                        Pending
                    </span>

                </div>


                {{-- REGISTRATION 2 --}}
                <div
                    class="
                        flex items-center
                        justify-between
                        gap-3
                        border-b border-[#f0ebe4]
                        py-3.5
                    "
                >

                    <div class="flex min-w-0 items-center gap-3">

                        <div
                            class="
                                grid h-10 w-10
                                shrink-0 place-items-center
                                rounded-full
                                bg-[#f1f7f3]
                                text-[11px] font-bold
                                text-[#5d886b]
                            "
                        >
                            M
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-[12px] font-semibold text-[#28231d]">
                                Maria Santos
                            </p>

                            <p class="mt-0.5 text-[10px] text-[#978e82]">
                                Seller
                            </p>
                        </div>

                    </div>

                    <span
                        class="
                            rounded-full
                            border border-[#eadcbe]
                            bg-[#fbf6eb]
                            px-2.5 py-1
                            text-[9px] font-semibold
                            text-[#ae781b]
                        "
                    >
                        Pending
                    </span>

                </div>


                {{-- REGISTRATION 3 --}}
                <div
                    class="
                        flex items-center
                        justify-between
                        gap-3
                        border-b border-[#f0ebe4]
                        py-3.5
                    "
                >

                    <div class="flex min-w-0 items-center gap-3">

                        <div
                            class="
                                grid h-10 w-10
                                shrink-0 place-items-center
                                rounded-full
                                bg-[#f5f2f8]
                                text-[11px] font-bold
                                text-[#7d698f]
                            "
                        >
                            P
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-[12px] font-semibold text-[#28231d]">
                                Pedro Reyes
                            </p>

                            <p class="mt-0.5 text-[10px] text-[#978e82]">
                                Courier
                            </p>
                        </div>

                    </div>

                    <span
                        class="
                            rounded-full
                            border border-[#eadcbe]
                            bg-[#fbf6eb]
                            px-2.5 py-1
                            text-[9px] font-semibold
                            text-[#ae781b]
                        "
                    >
                        Pending
                    </span>

                </div>


                {{-- REGISTRATION 4 --}}
                <div
                    class="
                        flex items-center
                        justify-between
                        gap-3
                        border-b border-[#f0ebe4]
                        py-3.5
                    "
                >

                    <div class="flex min-w-0 items-center gap-3">

                        <div
                            class="
                                grid h-10 w-10
                                shrink-0 place-items-center
                                rounded-full
                                bg-[#f8f3ed]
                                text-[11px] font-bold
                                text-[#9b7651]
                            "
                        >
                            A
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-[12px] font-semibold text-[#28231d]">
                                Ana Garcia
                            </p>

                            <p class="mt-0.5 text-[10px] text-[#978e82]">
                                Seller
                            </p>
                        </div>

                    </div>

                    <span
                        class="
                            rounded-full
                            border border-[#eadcbe]
                            bg-[#fbf6eb]
                            px-2.5 py-1
                            text-[9px] font-semibold
                            text-[#ae781b]
                        "
                    >
                        Pending
                    </span>

                </div>


                {{-- REGISTRATION 5 --}}
                <div
                    class="
                        flex items-center
                        justify-between
                        gap-3
                        py-3.5
                    "
                >

                    <div class="flex min-w-0 items-center gap-3">

                        <div
                            class="
                                grid h-10 w-10
                                shrink-0 place-items-center
                                rounded-full
                                bg-[#f3f5f7]
                                text-[11px] font-bold
                                text-[#6f7881]
                            "
                        >
                            L
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-[12px] font-semibold text-[#28231d]">
                                Luis Mendoza
                            </p>

                            <p class="mt-0.5 text-[10px] text-[#978e82]">
                                Buyer
                            </p>
                        </div>

                    </div>

                    <span
                        class="
                            rounded-full
                            border border-[#eadcbe]
                            bg-[#fbf6eb]
                            px-2.5 py-1
                            text-[9px] font-semibold
                            text-[#ae781b]
                        "
                    >
                        Pending
                    </span>

                </div>

            </div>

        </div>

    </section>


    {{-- =========================================================
        BOTTOM PANELS
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-5

            lg:grid-cols-2
            2xl:grid-cols-3
        "
    >

        {{-- PENDING COMPLAINTS --}}
        <div
            class="
                rounded-[22px]
                border border-[#ebe5dc]
                bg-white
                p-5

                sm:p-6
            "
        >

            <div class="flex items-start justify-between gap-4">

                <div>
                    <h3 class="text-[15px] font-bold text-[#211d17]">
                        Pending Complaints
                    </h3>

                    <p class="mt-1 text-[10px] text-[#978f84]">
                        Cases that require admin review
                    </p>
                </div>

                <button
                    type="button"
                    class="
                        rounded-lg
                        border border-[#ebe3d9]
                        px-3 py-2
                        text-[10px] font-semibold
                        text-[#756c60]
                        transition
                        hover:border-[#d8c9b4]
                    "
                >
                    View All
                </button>

            </div>


            <div class="mt-5 overflow-x-auto">

                <div class="min-w-[470px]">

                    <div
                        class="
                            grid grid-cols-[.9fr_1.7fr_1fr_.8fr]
                            gap-3
                            border-b border-[#eee8e0]
                            pb-2.5
                            text-[9px] font-semibold
                            uppercase tracking-[0.08em]
                            text-[#a39a8f]
                        "
                    >
                        <span>ID</span>
                        <span>Subject</span>
                        <span>From</span>
                        <span>Priority</span>
                    </div>


                    <div
                        class="
                            grid grid-cols-[.9fr_1.7fr_1fr_.8fr]
                            items-center gap-3
                            border-b border-[#f1ece5]
                            py-3
                            text-[10px]
                        "
                    >
                        <span class="font-semibold text-[#5d554b]">#CMP-1032</span>
                        <span class="text-[#49433b]">Late Delivery</span>
                        <span class="text-[#7f776c]">Maria Santos</span>
                        <span class="w-fit rounded-full bg-[#f9eeee] px-2 py-1 text-[9px] font-semibold text-[#b45e5e]">High</span>
                    </div>


                    <div
                        class="
                            grid grid-cols-[.9fr_1.7fr_1fr_.8fr]
                            items-center gap-3
                            border-b border-[#f1ece5]
                            py-3
                            text-[10px]
                        "
                    >
                        <span class="font-semibold text-[#5d554b]">#CMP-1031</span>
                        <span class="text-[#49433b]">Item Not As Described</span>
                        <span class="text-[#7f776c]">John Doe</span>
                        <span class="w-fit rounded-full bg-[#fbf4e8] px-2 py-1 text-[9px] font-semibold text-[#a9751e]">Medium</span>
                    </div>


                    <div
                        class="
                            grid grid-cols-[.9fr_1.7fr_1fr_.8fr]
                            items-center gap-3
                            py-3
                            text-[10px]
                        "
                    >
                        <span class="font-semibold text-[#5d554b]">#CMP-1030</span>
                        <span class="text-[#49433b]">Refund Request</span>
                        <span class="text-[#7f776c]">Ana Garcia</span>
                        <span class="w-fit rounded-full bg-[#f9eeee] px-2 py-1 text-[9px] font-semibold text-[#b45e5e]">High</span>
                    </div>

                </div>

            </div>

        </div>


        {{-- SELLER COMPLIANCE ALERTS --}}
        <div
            class="
                rounded-[22px]
                border border-[#ebe5dc]
                bg-white
                p-5

                sm:p-6
            "
        >

            <div class="flex items-start justify-between gap-4">

                <div>
                    <h3 class="text-[15px] font-bold text-[#211d17]">
                        Seller Compliance Alerts
                    </h3>

                    <p class="mt-1 text-[10px] text-[#978f84]">
                        Recent seller policy activity
                    </p>
                </div>

                <button
                    type="button"
                    class="
                        rounded-lg
                        border border-[#ebe3d9]
                        px-3 py-2
                        text-[10px] font-semibold
                        text-[#756c60]
                        transition
                        hover:border-[#d8c9b4]
                    "
                >
                    View All
                </button>

            </div>


            <div class="mt-5 space-y-3">

                <div
                    class="
                        flex items-start gap-3
                        rounded-[15px]
                        border border-[#ece4d7]
                        bg-[#fcfaf6]
                        p-4
                    "
                >

                    <div
                        class="
                            grid h-10 w-10
                            shrink-0 place-items-center
                            rounded-xl
                            bg-[#fbf1dc]
                            text-[#b78225]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M12 3 3 20h18L12 3Z"></path>
                            <path d="M12 9v5"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>

                    <div>
                        <p class="text-[11px] font-semibold text-[#3a342d]">
                            Prohibited item detected
                        </p>

                        <p class="mt-1 text-[9px] leading-4 text-[#8e857a]">
                            Seller: TechWorld Store
                        </p>

                        <p class="text-[9px] leading-4 text-[#a0968a]">
                            Today • 10:30 AM
                        </p>
                    </div>

                </div>


                <div
                    class="
                        flex items-start gap-3
                        rounded-[15px]
                        border border-[#e4e8ec]
                        bg-[#fafbfc]
                        p-4
                    "
                >

                    <div
                        class="
                            grid h-10 w-10
                            shrink-0 place-items-center
                            rounded-xl
                            bg-[#eef3f7]
                            text-[#627e97]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 10v6"></path>
                            <path d="M12 7h.01"></path>
                        </svg>
                    </div>

                    <div>
                        <p class="text-[11px] font-semibold text-[#3a4249]">
                            Warning issued
                        </p>

                        <p class="mt-1 text-[9px] leading-4 text-[#87919a]">
                            Seller: Fashion Hub
                        </p>

                        <p class="text-[9px] leading-4 text-[#9aa2a9]">
                            Yesterday • 02:15 PM
                        </p>
                    </div>

                </div>


                <div
                    class="
                        flex items-start gap-3
                        rounded-[15px]
                        border border-[#ebe2e2]
                        bg-[#fcf9f9]
                        p-4
                    "
                >

                    <div
                        class="
                            grid h-10 w-10
                            shrink-0 place-items-center
                            rounded-xl
                            bg-[#f8eeee]
                            text-[#ac6363]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z"></path>
                            <path d="M9 12h6"></path>
                        </svg>
                    </div>

                    <div>
                        <p class="text-[11px] font-semibold text-[#453939]">
                            Product flagged for review
                        </p>

                        <p class="mt-1 text-[9px] leading-4 text-[#918282]">
                            Seller: Home Essentials
                        </p>

                        <p class="text-[9px] leading-4 text-[#a39494]">
                            Yesterday • 11:45 AM
                        </p>
                    </div>

                </div>

            </div>

        </div>


        {{-- QUICK ACTIONS --}}
        <div
            class="
                rounded-[22px]
                border border-[#ebe5dc]
                bg-white
                p-5

                sm:p-6

                lg:col-span-2
                2xl:col-span-1
            "
        >

            <div>
                <h3 class="text-[15px] font-bold text-[#211d17]">
                    Quick Actions
                </h3>

                <p class="mt-1 text-[10px] text-[#978f84]">
                    Frequently used admin tools
                </p>
            </div>


            <div
                class="
                    mt-5
                    grid grid-cols-2
                    gap-3
                "
            >

                {{-- Announcement --}}
                <button
                    type="button"
                    class="
                        group
                        rounded-[16px]
                        border border-[#ebe3d7]
                        bg-[#fcfaf6]
                        p-4
                        text-left
                        transition

                        hover:border-[#d9c39d]
                        hover:bg-[#fbf7ef]
                    "
                >

                    <div
                        class="
                            grid h-10 w-10
                            place-items-center
                            rounded-xl
                            bg-[#f7eedf]
                            text-[#b67d1f]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M4 13h4l9 5V6l-9 5H4v2Z"></path>
                            <path d="M8 13v6"></path>
                        </svg>
                    </div>

                    <p class="mt-3 text-[11px] font-semibold text-[#40382f]">
                        Announcement
                    </p>

                    <p class="mt-1 text-[9px] leading-4 text-[#91887c]">
                        Post marketplace updates
                    </p>

                </button>


                {{-- Policies --}}
                <button
                    type="button"
                    class="
                        rounded-[16px]
                        border border-[#e3e8e5]
                        bg-[#fafcfb]
                        p-4
                        text-left
                        transition

                        hover:border-[#cfdcd5]
                        hover:bg-[#f7faf8]
                    "
                >

                    <div
                        class="
                            grid h-10 w-10
                            place-items-center
                            rounded-xl
                            bg-[#eef5f1]
                            text-[#5e8772]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M6 3h12v18H6z"></path>
                            <path d="M9 8h6"></path>
                            <path d="M9 12h6"></path>
                            <path d="M9 16h4"></path>
                        </svg>
                    </div>

                    <p class="mt-3 text-[11px] font-semibold text-[#39423d]">
                        Policies
                    </p>

                    <p class="mt-1 text-[9px] leading-4 text-[#89928d]">
                        Update platform rules
                    </p>

                </button>


                {{-- Reports --}}
                <button
                    type="button"
                    class="
                        rounded-[16px]
                        border border-[#e2e7ec]
                        bg-[#fafbfd]
                        p-4
                        text-left
                        transition

                        hover:border-[#ccd7e2]
                        hover:bg-[#f6f9fb]
                    "
                >

                    <div
                        class="
                            grid h-10 w-10
                            place-items-center
                            rounded-xl
                            bg-[#eef3f7]
                            text-[#607f9b]
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
                            <path d="M7 17v-5"></path>
                            <path d="M12 17V8"></path>
                            <path d="M17 17V4"></path>
                        </svg>
                    </div>

                    <p class="mt-3 text-[11px] font-semibold text-[#3a424a]">
                        Reports
                    </p>

                    <p class="mt-1 text-[9px] leading-4 text-[#89939c]">
                        View marketplace reports
                    </p>

                </button>


                {{-- Settings --}}
                <button
                    type="button"
                    class="
                        rounded-[16px]
                        border border-[#e8e3ea]
                        bg-[#fcfafc]
                        p-4
                        text-left
                        transition

                        hover:border-[#d8cedd]
                        hover:bg-[#faf7fb]
                    "
                >

                    <div
                        class="
                            grid h-10 w-10
                            place-items-center
                            rounded-xl
                            bg-[#f3eef5]
                            text-[#806b8d]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19 12a7 7 0 0 0-.1-1l2-1.5-2-3.4-2.4 1A7 7 0 0 0 14.8 6L14.5 3h-5L9.2 6a7 7 0 0 0-1.7 1.1l-2.4-1-2 3.4L5.1 11a7 7 0 0 0 0 2l-2 1.5 2 3.4 2.4-1A7 7 0 0 0 9.2 18l.3 3h5l.3-3a7 7 0 0 0 1.7-1.1l2.4 1 2-3.4-2-1.5c.1-.3.1-.7.1-1Z"></path>
                        </svg>
                    </div>

                    <p class="mt-3 text-[11px] font-semibold text-[#403a43]">
                        Settings
                    </p>

                    <p class="mt-1 text-[9px] leading-4 text-[#918a95]">
                        Configure marketplace
                    </p>

                </button>

            </div>

        </div>

    </section>


    <div class="h-5"></div>

</div>

@endsection