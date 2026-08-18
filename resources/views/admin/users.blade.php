@extends('layouts.admin')

@section('title', 'User Accounts — SARI Admin')
@section('page-title', 'User Accounts')

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
                    Marketplace Users
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
                    User Accounts
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
                    View and manage buyer, seller, and courier accounts,
                    monitor account activity, and control account access.
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

                    Export Users
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
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>

                    Add User
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

        {{-- TOTAL USERS --}}
        <div
            class="
                rounded-[18px]
                border border-[#dfe6e9]
                bg-white
                p-5
            "
        >
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Total Users
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        12,458
                    </h3>

                    <p class="mt-2 text-[10px] text-[#59816a]">
                        ▲ 12.5% this month
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        place-items-center
                        rounded-xl
                        border border-[#dce5ea]
                        bg-[#f3f6f8]
                        text-[#657f94]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <circle cx="9" cy="8" r="3"></circle>
                        <circle cx="17" cy="10" r="2.5"></circle>
                        <path d="M3 20c.4-3.8 2.6-6 6-6s5.6 2.2 6 6"></path>
                        <path d="M15 15c3.4 0 5.4 1.8 6 5"></path>
                    </svg>
                </div>

            </div>
        </div>


        {{-- ACTIVE USERS --}}
        <div
            class="
                rounded-[18px]
                border border-[#d7e6dc]
                bg-white
                p-5
            "
        >
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Active Accounts
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        11,924
                    </h3>

                    <p class="mt-2 text-[10px] text-[#7f887f]">
                        95.7% of users
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        place-items-center
                        rounded-xl
                        border border-[#d2e5d9]
                        bg-[#f1f7f3]
                        text-[#55846a]
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
                        <path d="m8.5 12 2.2 2.2 4.8-5"></path>
                    </svg>
                </div>

            </div>
        </div>


        {{-- SUSPENDED --}}
        <div
            class="
                rounded-[18px]
                border border-[#eadada]
                bg-white
                p-5
            "
        >
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Suspended Accounts
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        86
                    </h3>

                    <p class="mt-2 text-[10px] text-[#9a9186]">
                        14 suspended this month
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        place-items-center
                        rounded-xl
                        border border-[#ead8d8]
                        bg-[#faf1f1]
                        text-[#ad6767]
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
                        <path d="m8 8 8 8"></path>
                    </svg>
                </div>

            </div>
        </div>


        {{-- NEW USERS --}}
        <div
            class="
                rounded-[18px]
                border border-[#eadfc9]
                bg-white
                p-5
            "
        >
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        New This Month
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        634
                    </h3>

                    <p class="mt-2 text-[10px] text-[#9a9186]">
                        Buyers, sellers & couriers
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        place-items-center
                        rounded-xl
                        border border-[#eadfc8]
                        bg-[#fbf6ec]
                        text-[#b98020]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <circle cx="10" cy="8" r="3"></circle>
                        <path d="M4 20c.5-4 2.7-6 6-6s5.5 2 6 6"></path>
                        <path d="M18 8v6"></path>
                        <path d="M15 11h6"></path>
                    </svg>
                </div>

            </div>
        </div>

    </section>


    {{-- =========================================================
        ACCOUNT DISTRIBUTION
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-4

            md:grid-cols-3
        "
    >

        {{-- BUYERS --}}
        <div
            class="
                rounded-[18px]
                border border-[#e2e7ec]
                bg-white
                p-5
            "
        >
            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="text-[10px] font-medium text-[#8c8479]">
                        Buyers
                    </p>

                    <p class="mt-1 text-[20px] font-bold text-[#28231d]">
                        8,732
                    </p>
                </div>

                <span
                    class="
                        rounded-full
                        bg-[#f2f6f9]
                        px-2.5 py-1
                        text-[9px] font-semibold
                        text-[#647e96]
                    "
                >
                    70%
                </span>

            </div>

            <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-[#edf0f2]">
                <div class="h-full w-[70%] rounded-full bg-[#9aabba]"></div>
            </div>
        </div>


        {{-- SELLERS --}}
        <div
            class="
                rounded-[18px]
                border border-[#dce8e1]
                bg-white
                p-5
            "
        >
            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="text-[10px] font-medium text-[#8c8479]">
                        Sellers
                    </p>

                    <p class="mt-1 text-[20px] font-bold text-[#28231d]">
                        2,345
                    </p>
                </div>

                <span
                    class="
                        rounded-full
                        bg-[#eff6f2]
                        px-2.5 py-1
                        text-[9px] font-semibold
                        text-[#5b826b]
                    "
                >
                    19%
                </span>

            </div>

            <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-[#edf0ee]">
                <div class="h-full w-[19%] rounded-full bg-[#88a894]"></div>
            </div>
        </div>


        {{-- COURIERS --}}
        <div
            class="
                rounded-[18px]
                border border-[#e5dfeb]
                bg-white
                p-5
            "
        >
            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="text-[10px] font-medium text-[#8c8479]">
                        Couriers
                    </p>

                    <p class="mt-1 text-[20px] font-bold text-[#28231d]">
                        1,381
                    </p>
                </div>

                <span
                    class="
                        rounded-full
                        bg-[#f5f2f7]
                        px-2.5 py-1
                        text-[9px] font-semibold
                        text-[#78688a]
                    "
                >
                    11%
                </span>

            </div>

            <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-[#efedf1]">
                <div class="h-full w-[11%] rounded-full bg-[#a093ad]"></div>
            </div>
        </div>

    </section>


    {{-- =========================================================
        USER TABLE
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

        {{-- Tabs --}}
        <div
            class="
                flex overflow-x-auto
                border-b border-[#eee8df]
                px-4

                sm:px-5
            "
        >

            <button
                type="button"
                class="
                    whitespace-nowrap
                    border-b-2 border-[#c99128]
                    px-4 py-4
                    text-[11px] font-semibold
                    text-[#a8731f]
                "
            >
                All Users
                <span
                    class="
                        ml-1.5
                        rounded-full
                        bg-[#f6eddd]
                        px-2 py-0.5
                        text-[9px]
                    "
                >
                    12,458
                </span>
            </button>


            <button
                type="button"
                class="
                    whitespace-nowrap
                    border-b-2 border-transparent
                    px-4 py-4
                    text-[11px] font-medium
                    text-[#8d857a]
                    transition
                    hover:text-[#5f574d]
                "
            >
                Buyers
            </button>


            <button
                type="button"
                class="
                    whitespace-nowrap
                    border-b-2 border-transparent
                    px-4 py-4
                    text-[11px] font-medium
                    text-[#8d857a]
                    transition
                    hover:text-[#5f574d]
                "
            >
                Sellers
            </button>


            <button
                type="button"
                class="
                    whitespace-nowrap
                    border-b-2 border-transparent
                    px-4 py-4
                    text-[11px] font-medium
                    text-[#8d857a]
                    transition
                    hover:text-[#5f574d]
                "
            >
                Couriers
            </button>


            <button
                type="button"
                class="
                    whitespace-nowrap
                    border-b-2 border-transparent
                    px-4 py-4
                    text-[11px] font-medium
                    text-[#8d857a]
                    transition
                    hover:text-[#5f574d]
                "
            >
                Suspended
            </button>

        </div>


        {{-- Search + Filters --}}
        <div
            class="
                flex flex-col gap-3
                border-b border-[#eee8df]
                p-4

                sm:p-5
                xl:flex-row
                xl:items-center
                xl:justify-between
            "
        >

            <div class="relative w-full xl:max-w-[430px]">

                <svg
                    viewBox="0 0 24 24"
                    class="
                        pointer-events-none
                        absolute left-4 top-1/2
                        h-[17px] w-[17px]
                        -translate-y-1/2
                        text-[#9f978d]
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
                    placeholder="Search name, email, phone, or user ID..."
                    class="
                        h-11
                        w-full
                        rounded-xl
                        border border-[#e6dfd5]
                        bg-[#fcfbf9]
                        pl-11 pr-4
                        text-[11px]
                        text-[#3d3730]
                        outline-none
                        transition

                        placeholder:text-[#aaa197]

                        focus:border-[#c99a3d]
                        focus:ring-4
                        focus:ring-[#c99a3d]/10
                    "
                >

            </div>


            <div
                class="
                    grid grid-cols-1 gap-2

                    sm:grid-cols-3
                    xl:flex
                "
            >

                <select
                    class="
                        h-11
                        rounded-xl
                        border border-[#e6dfd5]
                        bg-white
                        px-3
                        text-[11px]
                        text-[#5d554b]
                        outline-none
                        focus:border-[#c99a3d]
                    "
                >
                    <option>All Roles</option>
                    <option>Buyer</option>
                    <option>Seller</option>
                    <option>Courier</option>
                </select>


                <select
                    class="
                        h-11
                        rounded-xl
                        border border-[#e6dfd5]
                        bg-white
                        px-3
                        text-[11px]
                        text-[#5d554b]
                        outline-none
                        focus:border-[#c99a3d]
                    "
                >
                    <option>All Statuses</option>
                    <option>Active</option>
                    <option>Suspended</option>
                    <option>Inactive</option>
                </select>


                <button
                    type="button"
                    class="
                        inline-flex h-11
                        items-center justify-center gap-2
                        rounded-xl
                        border border-[#e6dfd5]
                        bg-white
                        px-4
                        text-[11px] font-semibold
                        text-[#62594e]
                        transition

                        hover:border-[#d5c4a5]
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
                        <path d="M4 6h16"></path>
                        <path d="M7 12h10"></path>
                        <path d="M10 18h4"></path>
                    </svg>

                    Filters
                </button>

            </div>

        </div>


        {{-- USER TABLE --}}
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
                        <th class="px-5 py-4">User</th>
                        <th class="px-5 py-4">Role</th>
                        <th class="px-5 py-4">Contact</th>
                        <th class="px-5 py-4">Joined</th>
                        <th class="px-5 py-4">Activity</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>


                <tbody class="text-[11px]">

                    {{-- USER 1 --}}
                    <tr class="border-b border-[#f1ece5] transition hover:bg-[#fdfbf8]">

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-10 w-10
                                        shrink-0 place-items-center
                                        rounded-full
                                        bg-[#f2f6f9]
                                        text-[11px] font-bold
                                        text-[#617c95]
                                    "
                                >
                                    JD
                                </div>

                                <div>
                                    <p class="font-semibold text-[#312b25]">
                                        Juan Dela Cruz
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        USR-102458
                                    </p>
                                </div>

                            </div>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    rounded-full
                                    border border-[#dce5ed]
                                    bg-[#f4f7fa]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#617d96]
                                "
                            >
                                Buyer
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                juan@email.com
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                +63 912 345 6789
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Aug 10, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                8 days ago
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                12 orders
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                Active 4 min ago
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
                                    text-[#548069]
                                "
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-[#68a07b]"></span>
                                Active
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">

                                {{-- View --}}
                                <button
                                    type="button"
                                    title="View user"
                                    class="
                                        grid h-9 w-9
                                        place-items-center
                                        rounded-lg
                                        border border-[#e6dfd5]
                                        bg-white
                                        text-[#6c645a]
                                        transition

                                        hover:border-[#d4c3a5]
                                        hover:bg-[#fcf8f1]
                                        hover:text-[#a6701b]
                                    "
                                >
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>


                                {{-- Suspend --}}
                                <button
                                    type="button"
                                    title="Suspend account"
                                    class="
                                        grid h-9 w-9
                                        place-items-center
                                        rounded-lg
                                        border border-[#eadfc8]
                                        bg-[#fbf6ec]
                                        text-[#ad791d]
                                        transition

                                        hover:border-[#dac49a]
                                        hover:bg-[#f9f0df]
                                    "
                                >
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="12" cy="12" r="7"></circle>
                                        <path d="M8 12h8"></path>
                                    </svg>
                                </button>


                                {{-- Delete --}}
                                <button
                                    type="button"
                                    title="Delete user"
                                    class="
                                        grid h-9 w-9
                                        place-items-center
                                        rounded-lg
                                        border border-[#ead8d8]
                                        bg-[#fbf4f4]
                                        text-[#ad6666]
                                        transition

                                        hover:border-[#dcbcbc]
                                        hover:bg-[#faeded]
                                    "
                                >
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 7h16"></path>
                                        <path d="M9 7V4h6v3"></path>
                                        <path d="M7 7l1 13h8l1-13"></path>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>


                    {{-- USER 2 --}}
                    <tr class="border-b border-[#f1ece5] transition hover:bg-[#fdfbf8]">

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-10 w-10
                                        shrink-0 place-items-center
                                        rounded-full
                                        bg-[#f0f7f3]
                                        text-[11px] font-bold
                                        text-[#5b846a]
                                    "
                                >
                                    MS
                                </div>

                                <div>
                                    <p class="font-semibold text-[#312b25]">
                                        Maria Santos
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        USR-102457
                                    </p>
                                </div>

                            </div>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    rounded-full
                                    border border-[#d7e5dc]
                                    bg-[#f2f7f4]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#568167]
                                "
                            >
                                Seller
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                maria@shop.com
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                +63 917 230 8832
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Jul 28, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                21 days ago
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                86 products
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                Active 12 min ago
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
                                    text-[#548069]
                                "
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-[#68a07b]"></span>
                                Active
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">

                                <button type="button" title="View user" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] bg-white text-[#6c645a] transition hover:border-[#d4c3a5] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>

                                <button type="button" title="Suspend account" class="grid h-9 w-9 place-items-center rounded-lg border border-[#eadfc8] bg-[#fbf6ec] text-[#ad791d] transition hover:border-[#dac49a] hover:bg-[#f9f0df]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="12" cy="12" r="7"></circle>
                                        <path d="M8 12h8"></path>
                                    </svg>
                                </button>

                                <button type="button" title="Delete user" class="grid h-9 w-9 place-items-center rounded-lg border border-[#ead8d8] bg-[#fbf4f4] text-[#ad6666] transition hover:border-[#dcbcbc] hover:bg-[#faeded]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 7h16"></path>
                                        <path d="M9 7V4h6v3"></path>
                                        <path d="M7 7l1 13h8l1-13"></path>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>


                    {{-- USER 3 --}}
                    <tr class="border-b border-[#f1ece5] transition hover:bg-[#fdfbf8]">

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-10 w-10
                                        shrink-0 place-items-center
                                        rounded-full
                                        bg-[#f5f2f7]
                                        text-[11px] font-bold
                                        text-[#796989]
                                    "
                                >
                                    PR
                                </div>

                                <div>
                                    <p class="font-semibold text-[#312b25]">
                                        Pedro Reyes
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        USR-102456
                                    </p>
                                </div>

                            </div>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    rounded-full
                                    border border-[#e2dce7]
                                    bg-[#f6f3f8]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#79678a]
                                "
                            >
                                Courier
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                pedro@courier.com
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                +63 918 555 1122
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Jul 15, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                34 days ago
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                154 deliveries
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                Active 28 min ago
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
                                    text-[#548069]
                                "
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-[#68a07b]"></span>
                                Active
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">

                                <button type="button" title="View user" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] bg-white text-[#6c645a] transition hover:border-[#d4c3a5] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>

                                <button type="button" title="Suspend account" class="grid h-9 w-9 place-items-center rounded-lg border border-[#eadfc8] bg-[#fbf6ec] text-[#ad791d] transition hover:border-[#dac49a] hover:bg-[#f9f0df]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="12" cy="12" r="7"></circle>
                                        <path d="M8 12h8"></path>
                                    </svg>
                                </button>

                                <button type="button" title="Delete user" class="grid h-9 w-9 place-items-center rounded-lg border border-[#ead8d8] bg-[#fbf4f4] text-[#ad6666] transition hover:border-[#dcbcbc] hover:bg-[#faeded]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 7h16"></path>
                                        <path d="M9 7V4h6v3"></path>
                                        <path d="M7 7l1 13h8l1-13"></path>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>


                    {{-- USER 4 SUSPENDED --}}
                    <tr class="border-b border-[#f1ece5] bg-[#fffdfd] transition hover:bg-[#fdf8f8]">

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-10 w-10
                                        shrink-0 place-items-center
                                        rounded-full
                                        bg-[#faf0f0]
                                        text-[11px] font-bold
                                        text-[#a66969]
                                    "
                                >
                                    AG
                                </div>

                                <div>
                                    <p class="font-semibold text-[#312b25]">
                                        Ana Garcia
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        USR-102455
                                    </p>
                                </div>

                            </div>
                        </td>


                        <td class="px-5 py-4">
                            <span class="rounded-full border border-[#d7e5dc] bg-[#f2f7f4] px-2.5 py-1.5 text-[9px] font-semibold text-[#568167]">
                                Seller
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                ana@shop.com
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                +63 919 803 4421
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Jun 22, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                57 days ago
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                21 products
                            </p>

                            <p class="mt-1 text-[9px] text-[#a37f7f]">
                                Suspended Aug 16
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    inline-flex items-center gap-1.5
                                    rounded-full
                                    bg-[#faf0f0]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#a96060]
                                "
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-[#b86c6c]"></span>
                                Suspended
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">

                                <button type="button" title="View user" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] bg-white text-[#6c645a] transition hover:border-[#d4c3a5] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>

                                {{-- Reactivate --}}
                                <button
                                    type="button"
                                    title="Activate account"
                                    class="
                                        grid h-9 w-9
                                        place-items-center
                                        rounded-lg
                                        border border-[#d5e4db]
                                        bg-[#f2f7f4]
                                        text-[#56816a]
                                        transition

                                        hover:border-[#bdd5c5]
                                        hover:bg-[#ebf4ee]
                                    "
                                >
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="m5 12 4 4L19 6"></path>
                                    </svg>
                                </button>

                                <button type="button" title="Delete user" class="grid h-9 w-9 place-items-center rounded-lg border border-[#ead8d8] bg-[#fbf4f4] text-[#ad6666] transition hover:border-[#dcbcbc] hover:bg-[#faeded]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 7h16"></path>
                                        <path d="M9 7V4h6v3"></path>
                                        <path d="M7 7l1 13h8l1-13"></path>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>


                    {{-- USER 5 --}}
                    <tr class="transition hover:bg-[#fdfbf8]">

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-10 w-10
                                        shrink-0 place-items-center
                                        rounded-full
                                        bg-[#f4f2ee]
                                        text-[11px] font-bold
                                        text-[#7b7268]
                                    "
                                >
                                    LM
                                </div>

                                <div>
                                    <p class="font-semibold text-[#312b25]">
                                        Luis Mendoza
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        USR-102454
                                    </p>
                                </div>

                            </div>
                        </td>


                        <td class="px-5 py-4">
                            <span class="rounded-full border border-[#dce5ed] bg-[#f4f7fa] px-2.5 py-1.5 text-[9px] font-semibold text-[#617d96]">
                                Buyer
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                luis@email.com
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                +63 920 442 1987
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                May 18, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                3 months ago
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                34 orders
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                Active 1 hour ago
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef6f1] px-2.5 py-1.5 text-[9px] font-semibold text-[#548069]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#68a07b]"></span>
                                Active
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">

                                <button type="button" title="View user" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] bg-white text-[#6c645a] transition hover:border-[#d4c3a5] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>

                                <button type="button" title="Suspend account" class="grid h-9 w-9 place-items-center rounded-lg border border-[#eadfc8] bg-[#fbf6ec] text-[#ad791d] transition hover:border-[#dac49a] hover:bg-[#f9f0df]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="12" cy="12" r="7"></circle>
                                        <path d="M8 12h8"></path>
                                    </svg>
                                </button>

                                <button type="button" title="Delete user" class="grid h-9 w-9 place-items-center rounded-lg border border-[#ead8d8] bg-[#fbf4f4] text-[#ad6666] transition hover:border-[#dcbcbc] hover:bg-[#faeded]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 7h16"></path>
                                        <path d="M9 7V4h6v3"></path>
                                        <path d="M7 7l1 13h8l1-13"></path>
                                    </svg>
                                </button>

                            </div>
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
                <span class="font-semibold text-[#5d554c]">12,458</span>
                users
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


                <span class="px-1 text-[10px] text-[#9a9186]">
                    ...
                </span>


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
                    98
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