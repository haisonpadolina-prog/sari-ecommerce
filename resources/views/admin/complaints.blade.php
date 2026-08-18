@extends('layouts.admin')

@section('title', 'Complaints & Disputes — SARI Admin')
@section('page-title', 'Complaints & Disputes')

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
                    Case Management
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
                    Complaints & Disputes
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
                    Review marketplace complaints, inspect submitted evidence,
                    coordinate with involved users, and resolve dispute cases.
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
                        <path d="M8 9h8"></path>
                        <path d="M8 13h8"></path>
                        <path d="M8 17h5"></path>
                    </svg>

                    Export Cases
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

                    Create Case
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

        {{-- OPEN CASES --}}
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
                        Open Cases
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        23
                    </h3>

                    <p class="mt-2 text-[10px] text-[#9a9186]">
                        6 added today
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
                        <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                        <path d="M9 8h6"></path>
                        <path d="M9 12h6"></path>
                        <path d="M9 16h3"></path>
                    </svg>
                </div>

            </div>
        </div>


        {{-- URGENT --}}
        <div
            class="
                rounded-[18px]
                border border-[#ead9d9]
                bg-white
                p-5
            "
        >
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Urgent Cases
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        8
                    </h3>

                    <p class="mt-2 text-[10px] text-[#a47777]">
                        Requires immediate review
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        place-items-center
                        rounded-xl
                        border border-[#ead7d7]
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
                        <path d="M12 3 3 20h18L12 3Z"></path>
                        <path d="M12 9v5"></path>
                        <path d="M12 17h.01"></path>
                    </svg>
                </div>

            </div>
        </div>


        {{-- UNDER INVESTIGATION --}}
        <div
            class="
                rounded-[18px]
                border border-[#dce5ed]
                bg-white
                p-5
            "
        >
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Under Investigation
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        12
                    </h3>

                    <p class="mt-2 text-[10px] text-[#8f877d]">
                        Evidence being reviewed
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        place-items-center
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
                        <circle cx="11" cy="11" r="6"></circle>
                        <path d="m16 16 4 4"></path>
                    </svg>
                </div>

            </div>
        </div>


        {{-- RESOLVED --}}
        <div
            class="
                rounded-[18px]
                border border-[#d8e6dd]
                bg-white
                p-5
            "
        >
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Resolved This Month
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        94
                    </h3>

                    <p class="mt-2 text-[10px] text-[#56816a]">
                        ▲ 14% resolution rate
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        place-items-center
                        rounded-xl
                        border border-[#d5e5dc]
                        bg-[#f1f7f3]
                        text-[#56816a]
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

    </section>


    {{-- =========================================================
        CASE DISTRIBUTION
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-4

            md:grid-cols-3
        "
    >

        <div class="rounded-[18px] border border-[#e6e1da] bg-white p-5">
            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="text-[10px] font-medium text-[#8c8479]">
                        Delivery Issues
                    </p>

                    <p class="mt-1 text-[20px] font-bold text-[#28231d]">
                        38%
                    </p>
                </div>

                <div
                    class="
                        grid h-10 w-10 place-items-center
                        rounded-xl
                        bg-[#f4f6f8]
                        text-[#687f93]
                    "
                >
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M3 7h11v10H3z"></path>
                        <path d="M14 10h4l3 3v4h-7z"></path>
                        <circle cx="7" cy="18" r="2"></circle>
                        <circle cx="18" cy="18" r="2"></circle>
                    </svg>
                </div>

            </div>

            <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-[#edf0f2]">
                <div class="h-full w-[38%] rounded-full bg-[#9aabba]"></div>
            </div>
        </div>


        <div class="rounded-[18px] border border-[#e6e1da] bg-white p-5">
            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="text-[10px] font-medium text-[#8c8479]">
                        Product Issues
                    </p>

                    <p class="mt-1 text-[20px] font-bold text-[#28231d]">
                        34%
                    </p>
                </div>

                <div
                    class="
                        grid h-10 w-10 place-items-center
                        rounded-xl
                        bg-[#fbf5eb]
                        text-[#ad7b26]
                    "
                >
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M5 7h14l-1 13H6L5 7Z"></path>
                        <path d="M9 7a3 3 0 0 1 6 0"></path>
                    </svg>
                </div>

            </div>

            <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-[#f0ece5]">
                <div class="h-full w-[34%] rounded-full bg-[#d3ac65]"></div>
            </div>
        </div>


        <div class="rounded-[18px] border border-[#e6e1da] bg-white p-5">
            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="text-[10px] font-medium text-[#8c8479]">
                        Refund / Payment
                    </p>

                    <p class="mt-1 text-[20px] font-bold text-[#28231d]">
                        28%
                    </p>
                </div>

                <div
                    class="
                        grid h-10 w-10 place-items-center
                        rounded-xl
                        bg-[#f5f1f7]
                        text-[#7b688a]
                    "
                >
                    <span class="text-[16px] font-semibold">₱</span>
                </div>

            </div>

            <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-[#efedf1]">
                <div class="h-full w-[28%] rounded-full bg-[#a093ad]"></div>
            </div>
        </div>

    </section>


    {{-- =========================================================
        CASE MANAGEMENT TABLE
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

        {{-- TABS --}}
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
                All Cases

                <span
                    class="
                        ml-1.5
                        rounded-full
                        bg-[#f6eddd]
                        px-2 py-0.5
                        text-[9px]
                    "
                >
                    129
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
                Open
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
                Investigating
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
                Resolved
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
                Closed
            </button>

        </div>


        {{-- FILTERS --}}
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
                    placeholder="Search case ID, complainant, order, or subject..."
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
                    <option>All Categories</option>
                    <option>Delivery</option>
                    <option>Product</option>
                    <option>Refund</option>
                    <option>Payment</option>
                    <option>Seller Conduct</option>
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
                    <option>All Priorities</option>
                    <option>Urgent</option>
                    <option>High</option>
                    <option>Medium</option>
                    <option>Low</option>
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


        {{-- TABLE --}}
        <div class="overflow-x-auto">

            <table class="w-full min-w-[1300px] text-left">

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
                        <th class="px-5 py-4">Case</th>
                        <th class="px-5 py-4">Complainant</th>
                        <th class="px-5 py-4">Against</th>
                        <th class="px-5 py-4">Order</th>
                        <th class="px-5 py-4">Evidence</th>
                        <th class="px-5 py-4">Priority</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Updated</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>


                <tbody class="text-[11px]">

                    {{-- CASE 1 --}}
                    <tr
                        class="
                            border-b border-[#f1ece5]
                            transition
                            hover:bg-[#fdfbf8]
                        "
                    >

                        <td class="px-5 py-4">
                            <p class="font-semibold text-[#37312b]">
                                #CMP-1032
                            </p>

                            <p class="mt-1 max-w-[160px] truncate text-[9px] text-[#8f877c]">
                                Late delivery and damaged item
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-9 w-9
                                        shrink-0 place-items-center
                                        rounded-full
                                        bg-[#f2f6f9]
                                        text-[10px] font-bold
                                        text-[#617c95]
                                    "
                                >
                                    MS
                                </div>

                                <div>
                                    <p class="font-semibold text-[#3a342e]">
                                        Maria Santos
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        Buyer
                                    </p>
                                </div>

                            </div>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                TechWorld Store
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                Seller
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-semibold text-[#5f574e]">
                                #SRI-18472
                            </p>

                            <p class="mt-1 text-[9px] text-[#978e83]">
                                ₱12,500
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    inline-flex items-center gap-1.5
                                    text-[10px] font-semibold
                                    text-[#56816a]
                                "
                            >
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="m5 12 4 4L19 6"></path>
                                </svg>

                                4 files
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    rounded-full
                                    bg-[#faeeee]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#ad5f5f]
                                "
                            >
                                Urgent
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    rounded-full
                                    border border-[#e4dcd0]
                                    bg-[#fbf7ef]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#9d742f]
                                "
                            >
                                Investigating
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                12 min ago
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                Admin review
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">

                                <button
                                    type="button"
                                    title="View case"
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


                                <button
                                    type="button"
                                    title="Open investigation"
                                    class="
                                        grid h-9 w-9
                                        place-items-center
                                        rounded-lg
                                        border border-[#dce5ed]
                                        bg-[#f3f7fa]
                                        text-[#617e97]
                                        transition

                                        hover:border-[#c3d3e0]
                                        hover:bg-[#eef4f8]
                                    "
                                >
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="11" cy="11" r="6"></circle>
                                        <path d="m16 16 4 4"></path>
                                    </svg>
                                </button>


                                <button
                                    type="button"
                                    title="Resolve case"
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

                            </div>
                        </td>

                    </tr>


                    {{-- CASE 2 --}}
                    <tr
                        class="
                            border-b border-[#f1ece5]
                            transition
                            hover:bg-[#fdfbf8]
                        "
                    >

                        <td class="px-5 py-4">
                            <p class="font-semibold text-[#37312b]">
                                #CMP-1031
                            </p>

                            <p class="mt-1 max-w-[160px] truncate text-[9px] text-[#8f877c]">
                                Item not as described
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-9 w-9
                                        shrink-0 place-items-center
                                        rounded-full
                                        bg-[#f4f2ee]
                                        text-[10px] font-bold
                                        text-[#756d64]
                                    "
                                >
                                    JD
                                </div>

                                <div>
                                    <p class="font-semibold text-[#3a342e]">
                                        John Doe
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        Buyer
                                    </p>
                                </div>

                            </div>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Fashion Hub
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                Seller
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-semibold text-[#5f574e]">
                                #SRI-18421
                            </p>

                            <p class="mt-1 text-[9px] text-[#978e83]">
                                ₱4,250
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    inline-flex items-center gap-1.5
                                    text-[10px] font-semibold
                                    text-[#56816a]
                                "
                            >
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="m5 12 4 4L19 6"></path>
                                </svg>

                                2 files
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    rounded-full
                                    bg-[#fbf3e7]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#a97724]
                                "
                            >
                                Medium
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    rounded-full
                                    border border-[#e6dfd3]
                                    bg-[#fcfaf6]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#756b5f]
                                "
                            >
                                Open
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                42 min ago
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                New response
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">

                                <button type="button" title="View case" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] bg-white text-[#6c645a] transition hover:border-[#d4c3a5] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>

                                <button type="button" title="Open investigation" class="grid h-9 w-9 place-items-center rounded-lg border border-[#dce5ed] bg-[#f3f7fa] text-[#617e97] transition hover:border-[#c3d3e0] hover:bg-[#eef4f8]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="11" cy="11" r="6"></circle>
                                        <path d="m16 16 4 4"></path>
                                    </svg>
                                </button>

                                <button type="button" title="Resolve case" class="grid h-9 w-9 place-items-center rounded-lg border border-[#d5e4db] bg-[#f2f7f4] text-[#56816a] transition hover:border-[#bdd5c5] hover:bg-[#ebf4ee]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="m5 12 4 4L19 6"></path>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>


                    {{-- CASE 3 --}}
                    <tr
                        class="
                            border-b border-[#f1ece5]
                            transition
                            hover:bg-[#fdfbf8]
                        "
                    >

                        <td class="px-5 py-4">
                            <p class="font-semibold text-[#37312b]">
                                #CMP-1030
                            </p>

                            <p class="mt-1 max-w-[160px] truncate text-[9px] text-[#8f877c]">
                                Refund not received
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-9 w-9
                                        shrink-0 place-items-center
                                        rounded-full
                                        bg-[#f5f2f7]
                                        text-[10px] font-bold
                                        text-[#796989]
                                    "
                                >
                                    AG
                                </div>

                                <div>
                                    <p class="font-semibold text-[#3a342e]">
                                        Ana Garcia
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        Buyer
                                    </p>
                                </div>

                            </div>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Home Essentials
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                Seller
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-semibold text-[#5f574e]">
                                #SRI-18398
                            </p>

                            <p class="mt-1 text-[9px] text-[#978e83]">
                                ₱8,800
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    inline-flex items-center gap-1.5
                                    text-[10px] font-semibold
                                    text-[#56816a]
                                "
                            >
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="m5 12 4 4L19 6"></path>
                                </svg>

                                3 files
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    rounded-full
                                    bg-[#faeeee]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#ad5f5f]
                                "
                            >
                                High
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    rounded-full
                                    border border-[#e4dcd0]
                                    bg-[#fbf7ef]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#9d742f]
                                "
                            >
                                Investigating
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                1 hr ago
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                Evidence added
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">

                                <button type="button" title="View case" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] bg-white text-[#6c645a] transition hover:border-[#d4c3a5] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>

                                <button type="button" title="Open investigation" class="grid h-9 w-9 place-items-center rounded-lg border border-[#dce5ed] bg-[#f3f7fa] text-[#617e97] transition hover:border-[#c3d3e0] hover:bg-[#eef4f8]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="11" cy="11" r="6"></circle>
                                        <path d="m16 16 4 4"></path>
                                    </svg>
                                </button>

                                <button type="button" title="Resolve case" class="grid h-9 w-9 place-items-center rounded-lg border border-[#d5e4db] bg-[#f2f7f4] text-[#56816a] transition hover:border-[#bdd5c5] hover:bg-[#ebf4ee]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="m5 12 4 4L19 6"></path>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>


                    {{-- CASE 4 --}}
                    <tr
                        class="
                            border-b border-[#f1ece5]
                            transition
                            hover:bg-[#fdfbf8]
                        "
                    >

                        <td class="px-5 py-4">
                            <p class="font-semibold text-[#37312b]">
                                #CMP-1029
                            </p>

                            <p class="mt-1 max-w-[160px] truncate text-[9px] text-[#8f877c]">
                                Courier marked delivered
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-9 w-9
                                        shrink-0 place-items-center
                                        rounded-full
                                        bg-[#f2f6f9]
                                        text-[10px] font-bold
                                        text-[#617c95]
                                    "
                                >
                                    LM
                                </div>

                                <div>
                                    <p class="font-semibold text-[#3a342e]">
                                        Luis Mendoza
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        Buyer
                                    </p>
                                </div>

                            </div>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Pedro Reyes
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                Courier
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-semibold text-[#5f574e]">
                                #SRI-18366
                            </p>

                            <p class="mt-1 text-[9px] text-[#978e83]">
                                ₱2,150
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    inline-flex items-center gap-1.5
                                    text-[10px] font-semibold
                                    text-[#ae791f]
                                "
                            >
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="8"></circle>
                                    <path d="M12 8v4"></path>
                                    <path d="M12 16h.01"></path>
                                </svg>

                                1 missing
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    rounded-full
                                    bg-[#fbf3e7]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#a97724]
                                "
                            >
                                Medium
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    rounded-full
                                    border border-[#e6dfd3]
                                    bg-[#fcfaf6]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#756b5f]
                                "
                            >
                                Open
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                3 hrs ago
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                Waiting evidence
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">

                                <button type="button" title="View case" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] bg-white text-[#6c645a] transition hover:border-[#d4c3a5] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>

                                <button type="button" title="Open investigation" class="grid h-9 w-9 place-items-center rounded-lg border border-[#dce5ed] bg-[#f3f7fa] text-[#617e97] transition hover:border-[#c3d3e0] hover:bg-[#eef4f8]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="11" cy="11" r="6"></circle>
                                        <path d="m16 16 4 4"></path>
                                    </svg>
                                </button>

                                <button type="button" title="Resolve case" class="grid h-9 w-9 place-items-center rounded-lg border border-[#d5e4db] bg-[#f2f7f4] text-[#56816a] transition hover:border-[#bdd5c5] hover:bg-[#ebf4ee]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="m5 12 4 4L19 6"></path>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>


                    {{-- CASE 5 --}}
                    <tr class="transition hover:bg-[#fdfbf8]">

                        <td class="px-5 py-4">
                            <p class="font-semibold text-[#37312b]">
                                #CMP-1028
                            </p>

                            <p class="mt-1 max-w-[160px] truncate text-[9px] text-[#8f877c]">
                                Seller accepted refund
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-9 w-9
                                        shrink-0 place-items-center
                                        rounded-full
                                        bg-[#f0f7f3]
                                        text-[10px] font-bold
                                        text-[#5b846a]
                                    "
                                >
                                    RC
                                </div>

                                <div>
                                    <p class="font-semibold text-[#3a342e]">
                                        Rosa Cruz
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        Buyer
                                    </p>
                                </div>

                            </div>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Beauty Essentials
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                Seller
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-semibold text-[#5f574e]">
                                #SRI-18325
                            </p>

                            <p class="mt-1 text-[9px] text-[#978e83]">
                                ₱3,600
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    inline-flex items-center gap-1.5
                                    text-[10px] font-semibold
                                    text-[#56816a]
                                "
                            >
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="m5 12 4 4L19 6"></path>
                                </svg>

                                Complete
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <span
                                class="
                                    rounded-full
                                    bg-[#f2f5f3]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#6b786f]
                                "
                            >
                                Low
                            </span>
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
                                Resolved
                            </span>
                        </td>


                        <td class="px-5 py-4">
                            <p class="font-medium text-[#625a50]">
                                Yesterday
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                Case resolved
                            </p>
                        </td>


                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">

                                <button type="button" title="View case" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] bg-white text-[#6c645a] transition hover:border-[#d4c3a5] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
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
                <span class="font-semibold text-[#5d554c]">129</span>
                cases
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


    {{-- =========================================================
        RESOLUTION WORKFLOW PREVIEW
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-5

            xl:grid-cols-3
        "
    >

        <div class="rounded-[18px] border border-[#ebe4da] bg-white p-5">

            <div
                class="
                    grid h-10 w-10
                    place-items-center
                    rounded-xl
                    bg-[#f3f7fa]
                    text-[#617e97]
                "
            >
                <span class="text-[11px] font-bold">01</span>
            </div>

            <h3 class="mt-4 text-[13px] font-bold text-[#302a24]">
                Review Evidence
            </h3>

            <p class="mt-2 text-[10px] leading-5 text-[#8f877c]">
                Inspect order records, screenshots, photos, messages, and supporting documents.
            </p>

        </div>


        <div class="rounded-[18px] border border-[#ebe4da] bg-white p-5">

            <div
                class="
                    grid h-10 w-10
                    place-items-center
                    rounded-xl
                    bg-[#fbf6ec]
                    text-[#ae7a20]
                "
            >
                <span class="text-[11px] font-bold">02</span>
            </div>

            <h3 class="mt-4 text-[13px] font-bold text-[#302a24]">
                Coordinate Parties
            </h3>

            <p class="mt-2 text-[10px] leading-5 text-[#8f877c]">
                Contact the buyer, seller, or courier and collect additional information when needed.
            </p>

        </div>


        <div class="rounded-[18px] border border-[#ebe4da] bg-white p-5">

            <div
                class="
                    grid h-10 w-10
                    place-items-center
                    rounded-xl
                    bg-[#f1f7f3]
                    text-[#56816a]
                "
            >
                <span class="text-[11px] font-bold">03</span>
            </div>

            <h3 class="mt-4 text-[13px] font-bold text-[#302a24]">
                Resolve Dispute
            </h3>

            <p class="mt-2 text-[10px] leading-5 text-[#8f877c]">
                Apply the appropriate resolution, record the decision, and close the case.
            </p>

        </div>

    </section>


    <div class="h-5"></div>

</div>

@endsection