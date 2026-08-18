@extends('layouts.admin')

@section('title', 'Platform Settings — SARI Admin')
@section('page-title', 'Platform Settings')

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
                    Marketplace Configuration
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
                    Platform Settings
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
                    Manage SARI marketplace configuration, announcements,
                    policies, system preferences, and platform-wide controls.
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
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 4h16v16H4z"></path>
                        <path d="M8 8h8"></path>
                        <path d="M8 12h8"></path>
                        <path d="M8 16h5"></path>
                    </svg>

                    View Change Log
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
                        <path d="m5 12 4 4L19 6"></path>
                    </svg>

                    Save All Changes
                </button>

            </div>

        </div>
    </section>


    {{-- =========================================================
        SYSTEM STATUS
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

        <div class="rounded-[18px] border border-[#d8e6dd] bg-white p-5">
            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Platform Status
                    </p>

                    <h3 class="mt-2 text-[20px] font-bold text-[#211d18]">
                        Online
                    </h3>

                    <p class="mt-2 text-[10px] text-[#56816a]">
                        All systems operational
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#d5e5dc] bg-[#f1f7f3] text-[#56816a]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <circle cx="12" cy="12" r="8"></circle>
                        <path d="m8.5 12 2.2 2.2 4.8-5"></path>
                    </svg>
                </div>

            </div>
        </div>


        <div class="rounded-[18px] border border-[#eadfc9] bg-white p-5">
            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Active Announcement
                    </p>

                    <h3 class="mt-2 text-[20px] font-bold text-[#211d18]">
                        2
                    </h3>

                    <p class="mt-2 text-[10px] text-[#9a9186]">
                        Displayed marketplace-wide
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#eadfc8] bg-[#fbf6ec] text-[#b98020]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M4 13V8l12-4v13L4 13Z"></path>
                        <path d="M8 14v5h4v-4"></path>
                        <path d="M19 8v5"></path>
                    </svg>
                </div>

            </div>
        </div>


        <div class="rounded-[18px] border border-[#dce5ed] bg-white p-5">
            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Active Policies
                    </p>

                    <h3 class="mt-2 text-[20px] font-bold text-[#211d18]">
                        8
                    </h3>

                    <p class="mt-2 text-[10px] text-[#9a9186]">
                        Last updated Aug 16
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#d9e3ec] bg-[#f3f7fa] text-[#627f99]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M6 3h9l4 4v14H6z"></path>
                        <path d="M15 3v5h5"></path>
                        <path d="M9 12h7"></path>
                        <path d="M9 16h5"></path>
                    </svg>
                </div>

            </div>
        </div>


        <div class="rounded-[18px] border border-[#e5dfea] bg-white p-5">
            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Maintenance Mode
                    </p>

                    <h3 class="mt-2 text-[20px] font-bold text-[#211d18]">
                        Disabled
                    </h3>

                    <p class="mt-2 text-[10px] text-[#9a9186]">
                        Marketplace accessible
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#e0d9e6] bg-[#f6f2f8] text-[#806a91]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="m14 5 5 5"></path>
                        <path d="m5 19 9-9"></path>
                        <path d="m4 4 16 16"></path>
                    </svg>
                </div>

            </div>
        </div>

    </section>


    {{-- =========================================================
        SETTINGS BODY
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-5

            xl:grid-cols-[260px_1fr]
        "
    >

        {{-- SETTINGS NAV --}}
        <aside
            class="
                h-fit
                rounded-[22px]
                border border-[#ebe4da]
                bg-white
                p-3
            "
        >

            <p
                class="
                    px-3 pb-3 pt-2
                    text-[9px] font-semibold
                    uppercase tracking-[0.12em]
                    text-[#a0978c]
                "
            >
                Settings
            </p>


            <button
                type="button"
                class="
                    flex w-full items-center gap-3
                    rounded-xl
                    bg-[#fbf5e9]
                    px-3 py-3
                    text-left
                    text-[10px] font-semibold
                    text-[#a8731f]
                "
            >
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19 12a7 7 0 0 0-.1-1l2-1.5-2-3.4-2.4 1A7 7 0 0 0 14.8 6L14.5 3h-5L9.2 6a7 7 0 0 0-1.7 1.1l-2.4-1-2 3.4L5.1 11a7 7 0 0 0 0 2l-2 1.5 2 3.4 2.4-1A7 7 0 0 0 9.2 18l.3 3h5l.3-3a7 7 0 0 0 1.7-1.1l2.4 1 2-3.4-2-1.5c.1-.3.1-.7.1-1Z"></path>
                </svg>

                General Settings
            </button>


            <button
                type="button"
                class="
                    mt-1
                    flex w-full items-center gap-3
                    rounded-xl
                    px-3 py-3
                    text-left
                    text-[10px] font-medium
                    text-[#675f55]
                    transition

                    hover:bg-[#fcfaf7]
                    hover:text-[#a8731f]
                "
            >
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 13V8l12-4v13L4 13Z"></path>
                    <path d="M8 14v5h4v-4"></path>
                </svg>

                Announcements
            </button>


            <button
                type="button"
                class="
                    mt-1
                    flex w-full items-center gap-3
                    rounded-xl
                    px-3 py-3
                    text-left
                    text-[10px] font-medium
                    text-[#675f55]
                    transition

                    hover:bg-[#fcfaf7]
                    hover:text-[#a8731f]
                "
            >
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 3h9l4 4v14H6z"></path>
                    <path d="M15 3v5h5"></path>
                </svg>

                Marketplace Policies
            </button>


            <button
                type="button"
                class="
                    mt-1
                    flex w-full items-center gap-3
                    rounded-xl
                    px-3 py-3
                    text-left
                    text-[10px] font-medium
                    text-[#675f55]
                    transition

                    hover:bg-[#fcfaf7]
                    hover:text-[#a8731f]
                "
            >
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 7h16v12H4z"></path>
                    <path d="M7 7V4h10v3"></path>
                    <path d="M8 12h8"></path>
                </svg>

                Orders & Payments
            </button>


            <button
                type="button"
                class="
                    mt-1
                    flex w-full items-center gap-3
                    rounded-xl
                    px-3 py-3
                    text-left
                    text-[10px] font-medium
                    text-[#675f55]
                    transition

                    hover:bg-[#fcfaf7]
                    hover:text-[#a8731f]
                "
            >
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z"></path>
                </svg>

                Security & Access
            </button>


            <button
                type="button"
                class="
                    mt-1
                    flex w-full items-center gap-3
                    rounded-xl
                    px-3 py-3
                    text-left
                    text-[10px] font-medium
                    text-[#675f55]
                    transition

                    hover:bg-[#fcfaf7]
                    hover:text-[#a8731f]
                "
            >
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 3v3"></path>
                    <path d="M12 18v3"></path>
                    <path d="M3 12h3"></path>
                    <path d="M18 12h3"></path>
                    <circle cx="12" cy="12" r="5"></circle>
                </svg>

                System
            </button>

        </aside>


        {{-- SETTINGS CONTENT --}}
        <div class="space-y-5">

            {{-- GENERAL SETTINGS --}}
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
                        border-b border-[#eee8df]
                        pb-5

                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                    "
                >
                    <div>
                        <h3 class="text-[16px] font-bold text-[#28221b]">
                            General Settings
                        </h3>

                        <p class="mt-1 text-[10px] text-[#91887d]">
                            Configure basic information shown throughout the SARI marketplace.
                        </p>
                    </div>

                    <span
                        class="
                            w-fit
                            rounded-full
                            border border-[#e7dfd3]
                            bg-[#fcfaf7]
                            px-3 py-1.5
                            text-[9px] font-semibold
                            text-[#897f72]
                        "
                    >
                        Marketplace Profile
                    </span>
                </div>


                <form class="mt-5">

                    <div
                        class="
                            grid grid-cols-1
                            gap-4

                            md:grid-cols-2
                        "
                    >

                        <div>
                            <label class="mb-2 block text-[10px] font-semibold text-[#5c544a]">
                                Marketplace Name
                            </label>

                            <input
                                type="text"
                                value="SARI"
                                class="
                                    h-11 w-full
                                    rounded-xl
                                    border border-[#e6dfd5]
                                    bg-[#fcfbf9]
                                    px-4
                                    text-[11px]
                                    text-[#302b25]
                                    outline-none

                                    focus:border-[#c99a3d]
                                    focus:ring-4
                                    focus:ring-[#c99a3d]/10
                                "
                            >
                        </div>


                        <div>
                            <label class="mb-2 block text-[10px] font-semibold text-[#5c544a]">
                                Tagline
                            </label>

                            <input
                                type="text"
                                value="Shop Everywhere"
                                class="
                                    h-11 w-full
                                    rounded-xl
                                    border border-[#e6dfd5]
                                    bg-[#fcfbf9]
                                    px-4
                                    text-[11px]
                                    text-[#302b25]
                                    outline-none

                                    focus:border-[#c99a3d]
                                    focus:ring-4
                                    focus:ring-[#c99a3d]/10
                                "
                            >
                        </div>


                        <div>
                            <label class="mb-2 block text-[10px] font-semibold text-[#5c544a]">
                                Support Email
                            </label>

                            <input
                                type="email"
                                value="support@sari.com"
                                class="
                                    h-11 w-full
                                    rounded-xl
                                    border border-[#e6dfd5]
                                    bg-[#fcfbf9]
                                    px-4
                                    text-[11px]
                                    text-[#302b25]
                                    outline-none

                                    focus:border-[#c99a3d]
                                    focus:ring-4
                                    focus:ring-[#c99a3d]/10
                                "
                            >
                        </div>


                        <div>
                            <label class="mb-2 block text-[10px] font-semibold text-[#5c544a]">
                                Support Contact
                            </label>

                            <input
                                type="text"
                                value="+63 912 345 6789"
                                class="
                                    h-11 w-full
                                    rounded-xl
                                    border border-[#e6dfd5]
                                    bg-[#fcfbf9]
                                    px-4
                                    text-[11px]
                                    text-[#302b25]
                                    outline-none

                                    focus:border-[#c99a3d]
                                    focus:ring-4
                                    focus:ring-[#c99a3d]/10
                                "
                            >
                        </div>


                        <div>
                            <label class="mb-2 block text-[10px] font-semibold text-[#5c544a]">
                                Currency
                            </label>

                            <select
                                class="
                                    h-11 w-full
                                    rounded-xl
                                    border border-[#e6dfd5]
                                    bg-[#fcfbf9]
                                    px-4
                                    text-[11px]
                                    text-[#302b25]
                                    outline-none
                                    focus:border-[#c99a3d]
                                "
                            >
                                <option selected>Philippine Peso (PHP)</option>
                            </select>
                        </div>


                        <div>
                            <label class="mb-2 block text-[10px] font-semibold text-[#5c544a]">
                                Timezone
                            </label>

                            <select
                                class="
                                    h-11 w-full
                                    rounded-xl
                                    border border-[#e6dfd5]
                                    bg-[#fcfbf9]
                                    px-4
                                    text-[11px]
                                    text-[#302b25]
                                    outline-none
                                    focus:border-[#c99a3d]
                                "
                            >
                                <option selected>Asia/Manila (GMT+8)</option>
                            </select>
                        </div>

                    </div>


                    <div class="mt-5">
                        <label class="mb-2 block text-[10px] font-semibold text-[#5c544a]">
                            Marketplace Description
                        </label>

                        <textarea
                            rows="4"
                            class="
                                w-full resize-none
                                rounded-xl
                                border border-[#e6dfd5]
                                bg-[#fcfbf9]
                                px-4 py-3
                                text-[11px]
                                leading-5
                                text-[#302b25]
                                outline-none

                                focus:border-[#c99a3d]
                                focus:ring-4
                                focus:ring-[#c99a3d]/10
                            "
                        >SARI is an online marketplace connecting buyers, sellers, and couriers through one convenient shopping platform.</textarea>
                    </div>


                    <div class="mt-5 flex justify-end border-t border-[#eee8df] pt-5">
                        <button
                            type="button"
                            class="
                                rounded-xl
                                bg-[#c99128]
                                px-5 py-2.5
                                text-[10px] font-semibold
                                text-white
                                transition

                                hover:bg-[#b47e1e]
                            "
                        >
                            Save General Settings
                        </button>
                    </div>

                </form>

            </div>


            {{-- ANNOUNCEMENTS --}}
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
                        border-b border-[#eee8df]
                        pb-5

                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                    "
                >

                    <div>
                        <h3 class="text-[16px] font-bold text-[#28221b]">
                            Announcements
                        </h3>

                        <p class="mt-1 text-[10px] text-[#91887d]">
                            Create marketplace-wide notices for buyers, sellers, and couriers.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="
                            inline-flex w-fit
                            items-center gap-2
                            rounded-xl
                            bg-[#c99128]
                            px-4 py-2.5
                            text-[10px] font-semibold
                            text-white
                            transition

                            hover:bg-[#b47e1e]
                        "
                    >
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 5v14"></path>
                            <path d="M5 12h14"></path>
                        </svg>

                        New Announcement
                    </button>

                </div>


                <div class="mt-5 space-y-3">

                    <div
                        class="
                            rounded-[16px]
                            border border-[#eadfc9]
                            bg-[#fcfaf6]
                            p-4
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

                            <div class="flex gap-3">

                                <div
                                    class="
                                        grid h-10 w-10
                                        shrink-0 place-items-center
                                        rounded-xl
                                        bg-[#fbf3e4]
                                        text-[#ae791f]
                                    "
                                >
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M4 13V8l12-4v13L4 13Z"></path>
                                        <path d="M8 14v5h4v-4"></path>
                                    </svg>
                                </div>

                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-[11px] font-semibold text-[#3d3730]">
                                            Payday Sale Weekend
                                        </p>

                                        <span
                                            class="
                                                rounded-full
                                                bg-[#eef6f1]
                                                px-2 py-1
                                                text-[8px] font-semibold
                                                text-[#56816a]
                                            "
                                        >
                                            Active
                                        </span>
                                    </div>

                                    <p class="mt-2 max-w-[700px] text-[9px] leading-5 text-[#847b70]">
                                        Enjoy selected marketplace promotions from August 28–31.
                                        Seller participation rules apply.
                                    </p>

                                    <p class="mt-2 text-[8px] text-[#a0978c]">
                                        Published Aug 18, 2026 • All Users
                                    </p>
                                </div>

                            </div>


                            <div class="flex items-center gap-2">

                                <button
                                    type="button"
                                    class="
                                        rounded-lg
                                        border border-[#dfd7cb]
                                        bg-white
                                        px-3 py-2
                                        text-[9px] font-semibold
                                        text-[#675f55]
                                    "
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="
                                        rounded-lg
                                        border border-[#ead8d8]
                                        bg-[#fcf5f5]
                                        px-3 py-2
                                        text-[9px] font-semibold
                                        text-[#a96565]
                                    "
                                >
                                    Disable
                                </button>

                            </div>

                        </div>

                    </div>


                    <div
                        class="
                            rounded-[16px]
                            border border-[#e2e5e8]
                            bg-[#fcfbf9]
                            p-4
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

                            <div class="flex gap-3">

                                <div
                                    class="
                                        grid h-10 w-10
                                        shrink-0 place-items-center
                                        rounded-xl
                                        bg-[#f2f6f9]
                                        text-[#647f97]
                                    "
                                >
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z"></path>
                                    </svg>
                                </div>

                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-[11px] font-semibold text-[#3d3730]">
                                            Updated Seller Policy
                                        </p>

                                        <span
                                            class="
                                                rounded-full
                                                bg-[#eef6f1]
                                                px-2 py-1
                                                text-[8px] font-semibold
                                                text-[#56816a]
                                            "
                                        >
                                            Active
                                        </span>
                                    </div>

                                    <p class="mt-2 max-w-[700px] text-[9px] leading-5 text-[#847b70]">
                                        Sellers are reminded to review the latest prohibited
                                        product and listing compliance guidelines.
                                    </p>

                                    <p class="mt-2 text-[8px] text-[#a0978c]">
                                        Published Aug 16, 2026 • Sellers
                                    </p>
                                </div>

                            </div>


                            <div class="flex items-center gap-2">

                                <button type="button" class="rounded-lg border border-[#dfd7cb] bg-white px-3 py-2 text-[9px] font-semibold text-[#675f55]">
                                    Edit
                                </button>

                                <button type="button" class="rounded-lg border border-[#ead8d8] bg-[#fcf5f5] px-3 py-2 text-[9px] font-semibold text-[#a96565]">
                                    Disable
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- MARKETPLACE POLICIES --}}
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
                        border-b border-[#eee8df]
                        pb-5

                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                    "
                >

                    <div>
                        <h3 class="text-[16px] font-bold text-[#28221b]">
                            Marketplace Policies
                        </h3>

                        <p class="mt-1 text-[10px] text-[#91887d]">
                            Manage marketplace rules and user-facing policy documents.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="
                            rounded-xl
                            border border-[#dfd5c7]
                            bg-white
                            px-4 py-2.5
                            text-[10px] font-semibold
                            text-[#62594e]
                            transition

                            hover:border-[#cdb993]
                            hover:bg-[#fcf8f1]
                        "
                    >
                        Manage All Policies
                    </button>

                </div>


                <div
                    class="
                        mt-5
                        grid grid-cols-1
                        gap-3

                        md:grid-cols-2
                    "
                >

                    <div
                        class="
                            rounded-[16px]
                            border border-[#ece6dd]
                            bg-[#fcfbf8]
                            p-4
                        "
                    >
                        <div class="flex items-start justify-between gap-4">

                            <div>
                                <p class="text-[11px] font-semibold text-[#3d3730]">
                                    Terms of Service
                                </p>

                                <p class="mt-1 text-[9px] text-[#958c80]">
                                    General platform terms and responsibilities.
                                </p>
                            </div>

                            <span class="rounded-full bg-[#eef6f1] px-2 py-1 text-[8px] font-semibold text-[#56816a]">
                                Published
                            </span>

                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-[#eee8df] pt-3">
                            <span class="text-[8px] text-[#9c9388]">
                                Updated Aug 12
                            </span>

                            <button class="text-[9px] font-semibold text-[#a8731f]">
                                Edit Policy
                            </button>
                        </div>
                    </div>


                    <div
                        class="
                            rounded-[16px]
                            border border-[#ece6dd]
                            bg-[#fcfbf8]
                            p-4
                        "
                    >
                        <div class="flex items-start justify-between gap-4">

                            <div>
                                <p class="text-[11px] font-semibold text-[#3d3730]">
                                    Seller Policy
                                </p>

                                <p class="mt-1 text-[9px] text-[#958c80]">
                                    Listing, prohibited products, and seller conduct.
                                </p>
                            </div>

                            <span class="rounded-full bg-[#eef6f1] px-2 py-1 text-[8px] font-semibold text-[#56816a]">
                                Published
                            </span>

                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-[#eee8df] pt-3">
                            <span class="text-[8px] text-[#9c9388]">
                                Updated Aug 16
                            </span>

                            <button class="text-[9px] font-semibold text-[#a8731f]">
                                Edit Policy
                            </button>
                        </div>
                    </div>


                    <div
                        class="
                            rounded-[16px]
                            border border-[#ece6dd]
                            bg-[#fcfbf8]
                            p-4
                        "
                    >
                        <div class="flex items-start justify-between gap-4">

                            <div>
                                <p class="text-[11px] font-semibold text-[#3d3730]">
                                    Refund & Return Policy
                                </p>

                                <p class="mt-1 text-[9px] text-[#958c80]">
                                    Refund eligibility and return procedures.
                                </p>
                            </div>

                            <span class="rounded-full bg-[#eef6f1] px-2 py-1 text-[8px] font-semibold text-[#56816a]">
                                Published
                            </span>

                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-[#eee8df] pt-3">
                            <span class="text-[8px] text-[#9c9388]">
                                Updated Aug 10
                            </span>

                            <button class="text-[9px] font-semibold text-[#a8731f]">
                                Edit Policy
                            </button>
                        </div>
                    </div>


                    <div
                        class="
                            rounded-[16px]
                            border border-[#ece6dd]
                            bg-[#fcfbf8]
                            p-4
                        "
                    >
                        <div class="flex items-start justify-between gap-4">

                            <div>
                                <p class="text-[11px] font-semibold text-[#3d3730]">
                                    Privacy Policy
                                </p>

                                <p class="mt-1 text-[9px] text-[#958c80]">
                                    Data collection and privacy information.
                                </p>
                            </div>

                            <span class="rounded-full bg-[#eef6f1] px-2 py-1 text-[8px] font-semibold text-[#56816a]">
                                Published
                            </span>

                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-[#eee8df] pt-3">
                            <span class="text-[8px] text-[#9c9388]">
                                Updated Aug 08
                            </span>

                            <button class="text-[9px] font-semibold text-[#a8731f]">
                                Edit Policy
                            </button>
                        </div>
                    </div>

                </div>

            </div>


            {{-- ORDER SETTINGS + SYSTEM --}}
            <div
                class="
                    grid grid-cols-1
                    gap-5

                    xl:grid-cols-2
                "
            >

                {{-- ORDER SETTINGS --}}
                <div
                    class="
                        rounded-[22px]
                        border border-[#ebe4da]
                        bg-white
                        p-5

                        sm:p-6
                    "
                >

                    <h3 class="text-[16px] font-bold text-[#28221b]">
                        Order & Payment Settings
                    </h3>

                    <p class="mt-1 text-[10px] text-[#91887d]">
                        Configure marketplace order behavior and transaction rules.
                    </p>


                    <div class="mt-5 space-y-3">

                        <div class="flex items-center justify-between gap-4 rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">

                            <div>
                                <p class="text-[10px] font-semibold text-[#3d3730]">
                                    Auto Cancel Unpaid Orders
                                </p>

                                <p class="mt-1 text-[9px] text-[#958c80]">
                                    Automatically cancel after 24 hours.
                                </p>
                            </div>

                            <button
                                type="button"
                                class="
                                    relative h-6 w-11
                                    shrink-0
                                    rounded-full
                                    bg-[#c99128]
                                "
                            >
                                <span class="absolute right-1 top-1 h-4 w-4 rounded-full bg-white shadow-sm"></span>
                            </button>

                        </div>


                        <div class="flex items-center justify-between gap-4 rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">

                            <div>
                                <p class="text-[10px] font-semibold text-[#3d3730]">
                                    Buyer Order Cancellation
                                </p>

                                <p class="mt-1 text-[9px] text-[#958c80]">
                                    Allow cancellation before seller processing.
                                </p>
                            </div>

                            <button type="button" class="relative h-6 w-11 shrink-0 rounded-full bg-[#c99128]">
                                <span class="absolute right-1 top-1 h-4 w-4 rounded-full bg-white shadow-sm"></span>
                            </button>

                        </div>


                        <div class="flex items-center justify-between gap-4 rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">

                            <div>
                                <p class="text-[10px] font-semibold text-[#3d3730]">
                                    Cash on Delivery
                                </p>

                                <p class="mt-1 text-[9px] text-[#958c80]">
                                    Enable COD as a marketplace payment option.
                                </p>
                            </div>

                            <button type="button" class="relative h-6 w-11 shrink-0 rounded-full bg-[#c99128]">
                                <span class="absolute right-1 top-1 h-4 w-4 rounded-full bg-white shadow-sm"></span>
                            </button>

                        </div>


                        <div>
                            <label class="mb-2 block text-[10px] font-semibold text-[#5c544a]">
                                Default Return Window
                            </label>

                            <select
                                class="
                                    h-11 w-full
                                    rounded-xl
                                    border border-[#e6dfd5]
                                    bg-[#fcfbf9]
                                    px-4
                                    text-[10px]
                                    text-[#625a50]
                                    outline-none
                                    focus:border-[#c99a3d]
                                "
                            >
                                <option>3 Days</option>
                                <option selected>7 Days</option>
                                <option>14 Days</option>
                            </select>
                        </div>

                    </div>

                </div>


                {{-- SYSTEM CONTROLS --}}
                <div
                    class="
                        rounded-[22px]
                        border border-[#ebe4da]
                        bg-white
                        p-5

                        sm:p-6
                    "
                >

                    <h3 class="text-[16px] font-bold text-[#28221b]">
                        System Controls
                    </h3>

                    <p class="mt-1 text-[10px] text-[#91887d]">
                        Platform-wide administrative controls.
                    </p>


                    <div class="mt-5 space-y-3">

                        <div
                            class="
                                flex items-center justify-between gap-4
                                rounded-[15px]
                                border border-[#d9e5de]
                                bg-[#f7faf8]
                                p-4
                            "
                        >

                            <div>
                                <p class="text-[10px] font-semibold text-[#3d3730]">
                                    New Registrations
                                </p>

                                <p class="mt-1 text-[9px] text-[#958c80]">
                                    Allow new users to create accounts.
                                </p>
                            </div>

                            <span class="rounded-full bg-[#eaf4ed] px-2.5 py-1 text-[8px] font-semibold text-[#56816a]">
                                Enabled
                            </span>

                        </div>


                        <div
                            class="
                                flex items-center justify-between gap-4
                                rounded-[15px]
                                border border-[#d9e5de]
                                bg-[#f7faf8]
                                p-4
                            "
                        >

                            <div>
                                <p class="text-[10px] font-semibold text-[#3d3730]">
                                    Seller Listings
                                </p>

                                <p class="mt-1 text-[9px] text-[#958c80]">
                                    Allow sellers to publish products.
                                </p>
                            </div>

                            <span class="rounded-full bg-[#eaf4ed] px-2.5 py-1 text-[8px] font-semibold text-[#56816a]">
                                Enabled
                            </span>

                        </div>


                        <div
                            class="
                                flex items-center justify-between gap-4
                                rounded-[15px]
                                border border-[#d9e5de]
                                bg-[#f7faf8]
                                p-4
                            "
                        >

                            <div>
                                <p class="text-[10px] font-semibold text-[#3d3730]">
                                    Checkout
                                </p>

                                <p class="mt-1 text-[9px] text-[#958c80]">
                                    Allow customers to place orders.
                                </p>
                            </div>

                            <span class="rounded-full bg-[#eaf4ed] px-2.5 py-1 text-[8px] font-semibold text-[#56816a]">
                                Enabled
                            </span>

                        </div>


                        {{-- MAINTENANCE --}}
                        <div
                            class="
                                rounded-[15px]
                                border border-[#ead9d9]
                                bg-[#fdf8f8]
                                p-4
                            "
                        >

                            <div class="flex items-start gap-3">

                                <div
                                    class="
                                        grid h-9 w-9 shrink-0
                                        place-items-center
                                        rounded-lg
                                        bg-[#f9eeee]
                                        text-[#ad6666]
                                    "
                                >
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="m14 5 5 5"></path>
                                        <path d="m5 19 9-9"></path>
                                        <path d="m4 4 16 16"></path>
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-[10px] font-semibold text-[#754848]">
                                        Maintenance Mode
                                    </p>

                                    <p class="mt-1 text-[9px] leading-5 text-[#9b7777]">
                                        Temporarily prevent public access while performing maintenance.
                                    </p>
                                </div>

                            </div>

                            <button
                                type="button"
                                class="
                                    mt-4 w-full
                                    rounded-xl
                                    border border-[#e1bebe]
                                    bg-white
                                    px-4 py-2.5
                                    text-[9px] font-semibold
                                    text-[#a65d5d]
                                    transition

                                    hover:bg-[#fceeee]
                                "
                            >
                                Enable Maintenance Mode
                            </button>

                        </div>

                    </div>

                </div>

            </div>


            {{-- SAVE FOOTER --}}
            <div
                class="
                    flex flex-col gap-3
                    rounded-[22px]
                    border border-[#eadfc9]
                    bg-[#fcfaf6]
                    p-5

                    sm:flex-row
                    sm:items-center
                    sm:justify-between
                "
            >

                <div>
                    <p class="text-[11px] font-semibold text-[#443d34]">
                        Unsaved changes
                    </p>

                    <p class="mt-1 text-[9px] text-[#91877a]">
                        Review your settings before applying changes to the marketplace.
                    </p>
                </div>


                <div class="flex flex-wrap items-center gap-2">

                    <button
                        type="button"
                        class="
                            rounded-xl
                            border border-[#dfd5c7]
                            bg-white
                            px-4 py-2.5
                            text-[10px] font-semibold
                            text-[#62594e]
                        "
                    >
                        Discard
                    </button>

                    <button
                        type="button"
                        class="
                            rounded-xl
                            bg-[#c99128]
                            px-5 py-2.5
                            text-[10px] font-semibold
                            text-white
                            transition

                            hover:bg-[#b47e1e]
                        "
                    >
                        Save All Changes
                    </button>

                </div>

            </div>

        </div>

    </section>


    <div class="h-5"></div>

</div>

@endsection