@extends('layouts.admin')

@section('title', 'Account Registrations — SARI Admin')
@section('page-title', 'Account Registrations')

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
                    Registration Management
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
                    Account Registrations
                </h2>

                <p
                    class="
                        mt-2
                        max-w-[700px]
                        text-[12px] leading-6
                        text-[#81786c]

                        sm:text-[13px]
                    "
                >
                    Review buyer, seller, and courier applications,
                    verify submitted information, and manage approval decisions.
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

                    Export List
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

                    Add Registration
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

        {{-- Pending --}}
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
                        Pending Review
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        128
                    </h3>

                    <p class="mt-2 text-[10px] text-[#9a9186]">
                        28 added this week
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
                        <circle cx="12" cy="12" r="8"></circle>
                        <path d="M12 8v4l2.5 2"></path>
                    </svg>
                </div>

            </div>
        </div>


        {{-- Buyers --}}
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
                        Buyer Applications
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        72
                    </h3>

                    <p class="mt-2 text-[10px] text-[#9a9186]">
                        56% of pending
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        place-items-center
                        rounded-xl
                        border border-[#d8e3ed]
                        bg-[#f2f6fa]
                        text-[#607f9d]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <circle cx="12" cy="8" r="3"></circle>
                        <path d="M5 20c.5-4 3-6 7-6s6.5 2 7 6"></path>
                    </svg>
                </div>

            </div>
        </div>


        {{-- Sellers --}}
        <div
            class="
                rounded-[18px]
                border border-[#d9e7df]
                bg-white
                p-5
            "
        >
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Seller Applications
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        38
                    </h3>

                    <p class="mt-2 text-[10px] text-[#9a9186]">
                        30% of pending
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        place-items-center
                        rounded-xl
                        border border-[#d5e6dc]
                        bg-[#f1f7f3]
                        text-[#56856a]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <path d="M5 8h14"></path>
                        <path d="M6 8v12h12V8"></path>
                        <path d="M7 8 8 4h8l1 4"></path>
                    </svg>
                </div>

            </div>
        </div>


        {{-- Couriers --}}
        <div
            class="
                rounded-[18px]
                border border-[#e5dfe9]
                bg-white
                p-5
            "
        >
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Courier Applications
                    </p>

                    <h3
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        18
                    </h3>

                    <p class="mt-2 text-[10px] text-[#9a9186]">
                        14% of pending
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        place-items-center
                        rounded-xl
                        border border-[#e0d9e6]
                        bg-[#f6f2f8]
                        text-[#806a91]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <path d="M3 7h11v10H3z"></path>
                        <path d="M14 10h4l3 3v4h-7z"></path>
                        <circle cx="7" cy="18" r="2"></circle>
                        <circle cx="18" cy="18" r="2"></circle>
                    </svg>
                </div>

            </div>
        </div>

    </section>


    {{-- =========================================================
        MAIN TABLE CARD
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
                All Applications
                <span
                    class="
                        ml-1.5
                        rounded-full
                        bg-[#f6eddd]
                        px-2 py-0.5
                        text-[9px]
                    "
                >
                    128
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
                Pending
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
                Approved
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
                Rejected
            </button>

        </div>


        {{-- Filters --}}
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
                    placeholder="Search applicant name, email, or ID..."
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
                    <option>All Account Types</option>
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
                    <option>Pending</option>
                    <option>Approved</option>
                    <option>Rejected</option>
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

                    More Filters
                </button>

            </div>

        </div>


        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="w-full min-w-[1080px] text-left">

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
                        <th class="px-5 py-4">Applicant</th>
                        <th class="px-5 py-4">Account Type</th>
                        <th class="px-5 py-4">Contact</th>
                        <th class="px-5 py-4">Submitted</th>
                        <th class="px-5 py-4">Documents</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>


                <tbody class="text-[11px]">

                    {{-- ROW 1 --}}
                    <tr
                        class="
                            border-b border-[#f1ece5]
                            transition
                            hover:bg-[#fdfbf8]
                        "
                    >

                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-10 w-10
                                        shrink-0 place-items-center
                                        rounded-full
                                        bg-[#f1f5f8]
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
                                        REG-2026-0128
                                    </p>
                                </div>

                            </div>

                        </td>


                        <td class="px-5 py-4">

                            <span
                                class="
                                    inline-flex items-center gap-1.5
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
                                Aug 18, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                08:42 AM
                            </p>

                        </td>


                        <td class="px-5 py-4">

                            <span
                                class="
                                    inline-flex items-center gap-1.5
                                    text-[10px] font-semibold
                                    text-[#4f8667]
                                "
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path d="m5 12 4 4L19 6"></path>
                                </svg>

                                Complete
                            </span>

                        </td>


                        <td class="px-5 py-4">

                            <span
                                class="
                                    rounded-full
                                    border border-[#eadcbe]
                                    bg-[#fbf6eb]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#ad781c]
                                "
                            >
                                Pending
                            </span>

                        </td>


                        <td class="px-5 py-4">

                            <div class="flex items-center justify-end gap-2">

                                <button
                                    type="button"
                                    title="View application"
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
                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>


                                <button
                                    type="button"
                                    title="Approve application"
                                    class="
                                        grid h-9 w-9
                                        place-items-center
                                        rounded-lg
                                        border border-[#d4e4da]
                                        bg-[#f4f8f5]
                                        text-[#528267]
                                        transition

                                        hover:border-[#b9d4c4]
                                        hover:bg-[#edf6f0]
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="m5 12 4 4L19 6"></path>
                                    </svg>
                                </button>


                                <button
                                    type="button"
                                    title="Reject application"
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
                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M6 6l12 12"></path>
                                        <path d="M18 6 6 18"></path>
                                    </svg>
                                </button>

                            </div>

                        </td>

                    </tr>


                    {{-- ROW 2 --}}
                    <tr
                        class="
                            border-b border-[#f1ece5]
                            transition
                            hover:bg-[#fdfbf8]
                        "
                    >

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
                                        REG-2026-0127
                                    </p>
                                </div>

                            </div>

                        </td>


                        <td class="px-5 py-4">

                            <span
                                class="
                                    inline-flex items-center
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
                                Aug 18, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                07:35 AM
                            </p>

                        </td>


                        <td class="px-5 py-4">

                            <span
                                class="
                                    inline-flex items-center gap-1.5
                                    text-[10px] font-semibold
                                    text-[#b27b1c]
                                "
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <circle cx="12" cy="12" r="8"></circle>
                                    <path d="M12 8v4"></path>
                                    <path d="M12 16h.01"></path>
                                </svg>

                                1 Missing
                            </span>

                        </td>


                        <td class="px-5 py-4">

                            <span
                                class="
                                    rounded-full
                                    border border-[#eadcbe]
                                    bg-[#fbf6eb]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#ad781c]
                                "
                            >
                                Pending
                            </span>

                        </td>


                        <td class="px-5 py-4">

                            <div class="flex items-center justify-end gap-2">

                                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] bg-white text-[#6c645a] transition hover:border-[#d4c3a5] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>

                                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-[#d4e4da] bg-[#f4f8f5] text-[#528267] transition hover:border-[#b9d4c4] hover:bg-[#edf6f0]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="m5 12 4 4L19 6"></path>
                                    </svg>
                                </button>

                                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-[#ead8d8] bg-[#fbf4f4] text-[#ad6666] transition hover:border-[#dcbcbc] hover:bg-[#faeded]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M6 6l12 12"></path>
                                        <path d="M18 6 6 18"></path>
                                    </svg>
                                </button>

                            </div>

                        </td>

                    </tr>


                    {{-- ROW 3 --}}
                    <tr
                        class="
                            border-b border-[#f1ece5]
                            transition
                            hover:bg-[#fdfbf8]
                        "
                    >

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
                                        REG-2026-0126
                                    </p>
                                </div>

                            </div>

                        </td>


                        <td class="px-5 py-4">

                            <span
                                class="
                                    inline-flex items-center
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
                                Aug 17, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                06:12 PM
                            </p>

                        </td>


                        <td class="px-5 py-4">

                            <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-[#4f8667]">
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
                                    border border-[#d8e5dc]
                                    bg-[#f1f7f3]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#568169]
                                "
                            >
                                Approved
                            </span>

                        </td>


                        <td class="px-5 py-4">

                            <div class="flex items-center justify-end gap-2">

                                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] bg-white text-[#6c645a] transition hover:border-[#d4c3a5] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>

                            </div>

                        </td>

                    </tr>


                    {{-- ROW 4 --}}
                    <tr
                        class="
                            border-b border-[#f1ece5]
                            transition
                            hover:bg-[#fdfbf8]
                        "
                    >

                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-10 w-10
                                        shrink-0 place-items-center
                                        rounded-full
                                        bg-[#f8f3ed]
                                        text-[11px] font-bold
                                        text-[#977454]
                                    "
                                >
                                    AG
                                </div>

                                <div>
                                    <p class="font-semibold text-[#312b25]">
                                        Ana Garcia
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        REG-2026-0125
                                    </p>
                                </div>

                            </div>

                        </td>


                        <td class="px-5 py-4">

                            <span class="inline-flex items-center rounded-full border border-[#d7e5dc] bg-[#f2f7f4] px-2.5 py-1.5 text-[9px] font-semibold text-[#568167]">
                                Seller
                            </span>

                        </td>


                        <td class="px-5 py-4">

                            <p class="font-medium text-[#625a50]">
                                ana@garciashop.com
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                +63 919 803 4421
                            </p>

                        </td>


                        <td class="px-5 py-4">

                            <p class="font-medium text-[#625a50]">
                                Aug 17, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                03:50 PM
                            </p>

                        </td>


                        <td class="px-5 py-4">

                            <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-[#4f8667]">
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
                                    border border-[#ead9d9]
                                    bg-[#fbf3f3]
                                    px-2.5 py-1.5
                                    text-[9px] font-semibold
                                    text-[#ae6565]
                                "
                            >
                                Rejected
                            </span>

                        </td>


                        <td class="px-5 py-4">

                            <div class="flex items-center justify-end gap-2">

                                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] bg-white text-[#6c645a] transition hover:border-[#d4c3a5] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>

                            </div>

                        </td>

                    </tr>


                    {{-- ROW 5 --}}
                    <tr
                        class="
                            transition
                            hover:bg-[#fdfbf8]
                        "
                    >

                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        grid h-10 w-10
                                        shrink-0 place-items-center
                                        rounded-full
                                        bg-[#f1f4f7]
                                        text-[11px] font-bold
                                        text-[#687987]
                                    "
                                >
                                    LM
                                </div>

                                <div>
                                    <p class="font-semibold text-[#312b25]">
                                        Luis Mendoza
                                    </p>

                                    <p class="mt-1 text-[9px] text-[#978e83]">
                                        REG-2026-0124
                                    </p>
                                </div>

                            </div>

                        </td>


                        <td class="px-5 py-4">

                            <span class="inline-flex items-center rounded-full border border-[#dce5ed] bg-[#f4f7fa] px-2.5 py-1.5 text-[9px] font-semibold text-[#617d96]">
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
                                Aug 17, 2026
                            </p>

                            <p class="mt-1 text-[9px] text-[#978f84]">
                                01:20 PM
                            </p>

                        </td>


                        <td class="px-5 py-4">

                            <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-[#4f8667]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="m5 12 4 4L19 6"></path>
                                </svg>

                                Complete
                            </span>

                        </td>


                        <td class="px-5 py-4">

                            <span class="rounded-full border border-[#eadcbe] bg-[#fbf6eb] px-2.5 py-1.5 text-[9px] font-semibold text-[#ad781c]">
                                Pending
                            </span>

                        </td>


                        <td class="px-5 py-4">

                            <div class="flex items-center justify-end gap-2">

                                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-[#e6dfd5] bg-white text-[#6c645a] transition hover:border-[#d4c3a5] hover:bg-[#fcf8f1] hover:text-[#a6701b]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>

                                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-[#d4e4da] bg-[#f4f8f5] text-[#528267] transition hover:border-[#b9d4c4] hover:bg-[#edf6f0]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="m5 12 4 4L19 6"></path>
                                    </svg>
                                </button>

                                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-[#ead8d8] bg-[#fbf4f4] text-[#ad6666] transition hover:border-[#dcbcbc] hover:bg-[#faeded]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M6 6l12 12"></path>
                                        <path d="M18 6 6 18"></path>
                                    </svg>
                                </button>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


        {{-- Footer / Pagination --}}
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
                <span class="font-semibold text-[#5d554c]">128</span>
                applications
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