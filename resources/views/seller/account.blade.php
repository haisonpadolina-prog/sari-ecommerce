@extends('layouts.seller')

@section('title', 'Account Management — SARI Seller')
@section('page-title', 'Account Management')

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
                        border border-[#d9e6dd]
                        bg-[#f3f8f5]
                        px-3 py-1.5
                        text-[9px] font-semibold
                        uppercase tracking-[0.14em]
                        text-[#56816a]
                    "
                >
                    <span class="h-2 w-2 rounded-full bg-[#68a07b]"></span>
                    Verified Seller
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
                    Account Management
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
                    Manage your personal information, business profile,
                    addresses, verification documents, password, and seller preferences.
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
                        text-[10px] font-semibold
                        text-[#62594e]
                        transition
                        hover:border-[#d4c29f]
                        hover:bg-[#fcf9f3]
                    "
                >
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="8"></circle>
                        <path d="M12 8v4"></path>
                        <path d="M12 16h.01"></path>
                    </svg>
                    View Seller Status
                </button>

                <button
                    type="button"
                    class="
                        inline-flex items-center gap-2
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
                        <path d="m5 12 4 4L19 6"></path>
                    </svg>
                    Save Changes
                </button>
            </div>
        </div>
    </section>


    {{-- =========================================================
        ACCOUNT STATUS SUMMARY
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
                    <p class="text-[10px] font-medium text-[#797168]">Account Status</p>
                    <h3 class="mt-2 text-[18px] font-bold text-[#211d18]">Active</h3>
                    <p class="mt-2 text-[9px] font-medium text-[#56816a]">Seller account is operational</p>
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
                    <p class="text-[10px] font-medium text-[#797168]">Verification</p>
                    <h3 class="mt-2 text-[18px] font-bold text-[#211d18]">Approved</h3>
                    <p class="mt-2 text-[9px] text-[#9a9186]">ID and permit verified</p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#eadfc8] bg-[#fbf6ec] text-[#b98020]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z"></path>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-[18px] border border-[#dce5ed] bg-white p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[10px] font-medium text-[#797168]">Store Rating</p>
                    <h3 class="mt-2 text-[18px] font-bold text-[#211d18]">4.8 / 5</h3>
                    <p class="mt-2 text-[9px] text-[#9a9186]">Based on customer feedback</p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#d9e3ec] bg-[#f3f7fa] text-[#627f99]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="m12 3 2.5 5 5.5.8-4 3.9.9 5.5-4.9-2.6-4.9 2.6.9-5.5-4-3.9 5.5-.8L12 3Z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-[18px] border border-[#e5dfea] bg-white p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[10px] font-medium text-[#797168]">Seller Since</p>
                    <h3 class="mt-2 text-[18px] font-bold text-[#211d18]">Aug 2026</h3>
                    <p class="mt-2 text-[9px] text-[#9a9186]">SARI marketplace member</p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#e0d9e6] bg-[#f6f2f8] text-[#806a91]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                        <path d="M8 3v4"></path>
                        <path d="M16 3v4"></path>
                        <path d="M3 10h18"></path>
                    </svg>
                </div>
            </div>
        </div>
    </section>


    {{-- =========================================================
        PROFILE + PERSONAL INFO
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-5
            xl:grid-cols-[340px_1fr]
        "
    >

        {{-- PROFILE CARD --}}
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
            <div class="flex flex-col items-center text-center">
                <div class="relative">
                    <div
                        class="
                            grid h-[92px] w-[92px]
                            place-items-center
                            rounded-full
                            border-4 border-[#fff6e5]
                            bg-[#d9930a]
                            text-[23px] font-bold
                            text-white
                            shadow-sm
                        "
                    >
                        SS
                    </div>

                    <button
                        type="button"
                        class="
                            absolute bottom-0 right-0
                            grid h-8 w-8
                            place-items-center
                            rounded-full
                            border-2 border-white
                            bg-[#2f2a24]
                            text-white
                            shadow-md
                        "
                        title="Change profile photo"
                    >
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 8h4l2-3h4l2 3h4v11H4z"></path>
                            <circle cx="12" cy="13" r="3"></circle>
                        </svg>
                    </button>
                </div>

                <h3 class="mt-4 text-[16px] font-bold text-[#2d2822]">
                    SARI Seller
                </h3>

                <p class="mt-1 text-[9px] text-[#91887d]">
                    seller@gmail.com
                </p>

                <span
                    class="
                        mt-3
                        rounded-full
                        border border-[#d6e5dc]
                        bg-[#f1f7f3]
                        px-3 py-1.5
                        text-[8px] font-semibold
                        text-[#56816a]
                    "
                >
                    Verified Seller
                </span>
            </div>


            <div class="mt-6 space-y-3">

                <div class="flex items-center justify-between rounded-[14px] border border-[#ece6dd] bg-[#fcfbf8] p-3.5">
                    <span class="text-[9px] text-[#91887d]">Seller ID</span>
                    <span class="text-[9px] font-semibold text-[#49423a]">SLR-2026-001</span>
                </div>

                <div class="flex items-center justify-between rounded-[14px] border border-[#ece6dd] bg-[#fcfbf8] p-3.5">
                    <span class="text-[9px] text-[#91887d]">Account Status</span>
                    <span class="inline-flex items-center gap-1.5 text-[9px] font-semibold text-[#56816a]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#68a07b]"></span>
                        Active
                    </span>
                </div>

                <div class="flex items-center justify-between rounded-[14px] border border-[#ece6dd] bg-[#fcfbf8] p-3.5">
                    <span class="text-[9px] text-[#91887d]">Last Login</span>
                    <span class="text-[9px] font-semibold text-[#49423a]">Today, 10:42 AM</span>
                </div>
            </div>


            <button
                type="button"
                class="
                    mt-4 w-full
                    rounded-xl
                    border border-[#dfd5c7]
                    bg-white
                    px-4 py-2.5
                    text-[9px] font-semibold
                    text-[#62594e]
                    transition
                    hover:bg-[#fcf8f1]
                "
            >
                View Public Store Profile
            </button>
        </div>


        {{-- PERSONAL INFORMATION --}}
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
            <div class="border-b border-[#eee8df] pb-5">
                <h3 class="text-[16px] font-bold text-[#28221b]">
                    Personal Information
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Information submitted during seller registration.
                </p>
            </div>

            <form class="mt-5">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                            First Name
                        </label>
                        <input
                            type="text"
                            value="SARI"
                            class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none focus:border-[#c99a3d] focus:ring-4 focus:ring-[#c99a3d]/10"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                            Last Name
                        </label>
                        <input
                            type="text"
                            value="Seller"
                            class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none focus:border-[#c99a3d] focus:ring-4 focus:ring-[#c99a3d]/10"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                            Middle Initial
                        </label>
                        <input
                            type="text"
                            value="A."
                            class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none focus:border-[#c99a3d]"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                            Sex
                        </label>
                        <select class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-3 text-[10px] outline-none focus:border-[#c99a3d]">
                            <option selected>Male</option>
                            <option>Female</option>
                            <option>Prefer not to say</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                            Email Address
                        </label>
                        <input
                            type="email"
                            value="seller@gmail.com"
                            class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none focus:border-[#c99a3d]"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                            Contact Number
                        </label>
                        <input
                            type="text"
                            value="+63 912 345 6789"
                            class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none focus:border-[#c99a3d]"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                            Birthday
                        </label>
                        <input
                            type="date"
                            value="2000-05-12"
                            class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none focus:border-[#c99a3d]"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                            Age
                        </label>
                        <input
                            type="text"
                            value="26"
                            readonly
                            class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#f4f1ec] px-4 text-[10px] text-[#8e8579] outline-none"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                            Seller ID
                        </label>
                        <input
                            type="text"
                            value="SLR-2026-001"
                            readonly
                            class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#f4f1ec] px-4 text-[10px] text-[#8e8579] outline-none"
                        >
                    </div>

                </div>

                <div class="mt-5 flex justify-end border-t border-[#eee8df] pt-5">
                    <button
                        type="button"
                        class="rounded-xl bg-[#c99128] px-5 py-2.5 text-[10px] font-semibold text-white transition hover:bg-[#b47e1e]"
                    >
                        Save Personal Information
                    </button>
                </div>
            </form>
        </div>

    </section>


    {{-- =========================================================
        BUSINESS INFORMATION + ADDRESS
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-5
            xl:grid-cols-2
        "
    >

        {{-- BUSINESS INFORMATION --}}
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
            <div class="border-b border-[#eee8df] pb-5">
                <h3 class="text-[16px] font-bold text-[#28221b]">
                    Business Information
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Store and business details displayed to the marketplace.
                </p>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">

                <div class="md:col-span-2">
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        Business Name
                    </label>

                    <input
                        type="text"
                        value="SARI Seller Store"
                        class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none focus:border-[#c99a3d]"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        Line of Business
                    </label>

                    <select class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-3 text-[10px] outline-none focus:border-[#c99a3d]">
                        <option selected>General Merchandise</option>
                        <option>Electronics</option>
                        <option>Fashion</option>
                        <option>Home & Living</option>
                        <option>Beauty</option>
                        <option>Books</option>
                        <option>Food & Beverage</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        Store Status
                    </label>

                    <input
                        type="text"
                        value="Active / Verified"
                        readonly
                        class="h-11 w-full rounded-xl border border-[#d9e5de] bg-[#f3f8f5] px-4 text-[10px] font-medium text-[#56816a] outline-none"
                    >
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        Store Description
                    </label>

                    <textarea
                        rows="4"
                        class="w-full resize-none rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 py-3 text-[10px] leading-5 outline-none focus:border-[#c99a3d]"
                    >A verified SARI marketplace store offering quality everyday products with reliable order fulfillment and customer service.</textarea>
                </div>

            </div>

            <div class="mt-5 flex justify-end border-t border-[#eee8df] pt-5">
                <button
                    type="button"
                    class="rounded-xl bg-[#c99128] px-5 py-2.5 text-[10px] font-semibold text-white transition hover:bg-[#b47e1e]"
                >
                    Save Business Information
                </button>
            </div>
        </div>


        {{-- ADDRESS --}}
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
            <div class="border-b border-[#eee8df] pb-5">
                <h3 class="text-[16px] font-bold text-[#28221b]">
                    Business Address
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Pickup and business location used for seller operations.
                </p>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">

                <div>
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        Province
                    </label>

                    <select class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-3 text-[10px] outline-none">
                        <option selected>Laguna</option>
                        <option>Metro Manila</option>
                        <option>Cavite</option>
                        <option>Rizal</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        Municipality / City
                    </label>

                    <select class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-3 text-[10px] outline-none">
                        <option selected>Santa Rosa City</option>
                        <option>Calamba City</option>
                        <option>Biñan City</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        Barangay
                    </label>

                    <select class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-3 text-[10px] outline-none">
                        <option selected>Balibago</option>
                        <option>Tagapo</option>
                        <option>Macabling</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        ZIP Code
                    </label>

                    <input
                        type="text"
                        value="4026"
                        class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none"
                    >
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        Street / House No. / Building
                    </label>

                    <input
                        type="text"
                        value="123 Marketplace Street"
                        class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none"
                    >
                </div>

            </div>


            <div
                class="
                    mt-5
                    rounded-[16px]
                    border border-[#dce5ed]
                    bg-[#f5f8fa]
                    p-4
                "
            >
                <div class="flex items-start gap-3">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-[#647f97]">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 21s7-6 7-12a7 7 0 1 0-14 0c0 6 7 12 7 12Z"></path>
                            <circle cx="12" cy="9" r="2"></circle>
                        </svg>
                    </div>

                    <div>
                        <p class="text-[9px] font-semibold text-[#4b5359]">
                            Address API Placeholder
                        </p>

                        <p class="mt-1 text-[8px] leading-4 text-[#84909a]">
                            Province, municipality, and barangay can later be connected to an address API or Philippine location dataset.
                        </p>
                    </div>
                </div>
            </div>


            <div class="mt-5 flex justify-end border-t border-[#eee8df] pt-5">
                <button
                    type="button"
                    class="rounded-xl bg-[#c99128] px-5 py-2.5 text-[10px] font-semibold text-white transition hover:bg-[#b47e1e]"
                >
                    Save Address
                </button>
            </div>
        </div>

    </section>


    {{-- =========================================================
        VERIFICATION DOCUMENTS
    ========================================================== --}}
    <section class="mt-5 rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
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
                    Verification Documents
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Government ID and business documents submitted during registration.
                </p>
            </div>

            <span class="w-fit rounded-full bg-[#eef6f1] px-3 py-1.5 text-[8px] font-semibold text-[#56816a]">
                All documents verified
            </span>
        </div>


        <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-2">

            {{-- ID --}}
            <div class="rounded-[18px] border border-[#e7e1d8] bg-[#fcfbf8] p-4">
                <div class="flex items-start gap-4">
                    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-[#f3f7fa] text-[#647f97]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <circle cx="8" cy="11" r="2"></circle>
                            <path d="M13 10h5"></path>
                            <path d="M13 14h4"></path>
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-[10px] font-semibold text-[#3d3730]">
                                Government Valid ID
                            </p>

                            <span class="rounded-full bg-[#eef6f1] px-2 py-1 text-[8px] font-semibold text-[#56816a]">
                                Verified
                            </span>
                        </div>

                        <p class="mt-1 text-[8px] text-[#958c80]">
                            seller-valid-id.jpg • Uploaded Aug 10, 2026
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <button class="rounded-lg border border-[#dfd7cb] bg-white px-3 py-2 text-[8px] font-semibold text-[#675f55]">
                                View Document
                            </button>

                            <button class="rounded-lg border border-[#dfd7cb] bg-white px-3 py-2 text-[8px] font-semibold text-[#675f55]">
                                Replace
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            {{-- PERMIT --}}
            <div class="rounded-[18px] border border-[#e7e1d8] bg-[#fcfbf8] p-4">
                <div class="flex items-start gap-4">
                    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-[#fbf5e9] text-[#ad791f]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M5 3h10l4 4v14H5z"></path>
                            <path d="M15 3v5h5"></path>
                            <path d="M9 12h6"></path>
                            <path d="M9 16h4"></path>
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-[10px] font-semibold text-[#3d3730]">
                                Business Permit
                            </p>

                            <span class="rounded-full bg-[#eef6f1] px-2 py-1 text-[8px] font-semibold text-[#56816a]">
                                Verified
                            </span>
                        </div>

                        <p class="mt-1 text-[8px] text-[#958c80]">
                            business-permit.pdf • Uploaded Aug 10, 2026
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <button class="rounded-lg border border-[#dfd7cb] bg-white px-3 py-2 text-[8px] font-semibold text-[#675f55]">
                                View Document
                            </button>

                            <button class="rounded-lg border border-[#dfd7cb] bg-white px-3 py-2 text-[8px] font-semibold text-[#675f55]">
                                Replace
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div
            class="
                mt-4
                rounded-[15px]
                border border-[#eadfc9]
                bg-[#fcfaf6]
                p-4
            "
        >
            <div class="flex items-start gap-3">
                <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0 text-[#a8731f]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="8"></circle>
                    <path d="M12 8v4"></path>
                    <path d="M12 16h.01"></path>
                </svg>

                <p class="text-[8px] leading-4 text-[#8e7c61]">
                    Replacing a verified document may require another administrator review before the new document becomes active.
                </p>
            </div>
        </div>
    </section>


    {{-- =========================================================
        SECURITY + NOTIFICATIONS
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-5
            xl:grid-cols-2
        "
    >

        {{-- CHANGE PASSWORD --}}
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
            <div class="border-b border-[#eee8df] pb-5">
                <h3 class="text-[16px] font-bold text-[#28221b]">
                    Password & Security
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Protect your seller account with a strong password.
                </p>
            </div>

            <div class="mt-5 space-y-4">

                <div>
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        Current Password
                    </label>

                    <input
                        type="password"
                        placeholder="Enter current password"
                        class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none focus:border-[#c99a3d]"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        New Password
                    </label>

                    <input
                        type="password"
                        placeholder="Enter new password"
                        class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none focus:border-[#c99a3d]"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-[9px] font-semibold text-[#5c544a]">
                        Confirm New Password
                    </label>

                    <input
                        type="password"
                        placeholder="Confirm new password"
                        class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none focus:border-[#c99a3d]"
                    >
                </div>

            </div>


            <div class="mt-4 rounded-[14px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                <p class="text-[9px] font-semibold text-[#4f473e]">
                    Password recommendation
                </p>

                <p class="mt-1 text-[8px] leading-4 text-[#958c80]">
                    Use at least 8 characters with uppercase, lowercase, number, and symbol.
                </p>
            </div>


            <button
                type="button"
                class="mt-4 rounded-xl bg-[#c99128] px-5 py-2.5 text-[10px] font-semibold text-white transition hover:bg-[#b47e1e]"
            >
                Update Password
            </button>
        </div>


        {{-- SELLER NOTIFICATIONS --}}
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
            <div class="border-b border-[#eee8df] pb-5">
                <h3 class="text-[16px] font-bold text-[#28221b]">
                    Seller Notifications
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Choose which seller activities should notify you.
                </p>
            </div>


            <div class="mt-5 space-y-3">

                <label class="flex cursor-pointer items-center justify-between gap-4 rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <div>
                        <p class="text-[10px] font-semibold text-[#3d3730]">
                            New Order Notifications
                        </p>

                        <p class="mt-1 text-[8px] text-[#958c80]">
                            Notify me whenever a new customer order arrives.
                        </p>
                    </div>

                    <input type="checkbox" checked class="h-4 w-4 accent-[#c99128]">
                </label>


                <label class="flex cursor-pointer items-center justify-between gap-4 rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <div>
                        <p class="text-[10px] font-semibold text-[#3d3730]">
                            Low Stock Alerts
                        </p>

                        <p class="mt-1 text-[8px] text-[#958c80]">
                            Notify me when a product is running low.
                        </p>
                    </div>

                    <input type="checkbox" checked class="h-4 w-4 accent-[#c99128]">
                </label>


                <label class="flex cursor-pointer items-center justify-between gap-4 rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <div>
                        <p class="text-[10px] font-semibold text-[#3d3730]">
                            Delivery Updates
                        </p>

                        <p class="mt-1 text-[8px] text-[#958c80]">
                            Receive courier pickup and delivery status updates.
                        </p>
                    </div>

                    <input type="checkbox" checked class="h-4 w-4 accent-[#c99128]">
                </label>


                <label class="flex cursor-pointer items-center justify-between gap-4 rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <div>
                        <p class="text-[10px] font-semibold text-[#3d3730]">
                            Customer Feedback
                        </p>

                        <p class="mt-1 text-[8px] text-[#958c80]">
                            Notify me when buyers leave a new review.
                        </p>
                    </div>

                    <input type="checkbox" checked class="h-4 w-4 accent-[#c99128]">
                </label>


                <label class="flex cursor-pointer items-center justify-between gap-4 rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <div>
                        <p class="text-[10px] font-semibold text-[#3d3730]">
                            Marketing & Promotions
                        </p>

                        <p class="mt-1 text-[8px] text-[#958c80]">
                            Receive seller campaign and promotion updates.
                        </p>
                    </div>

                    <input type="checkbox" class="h-4 w-4 accent-[#c99128]">
                </label>

            </div>


            <button
                type="button"
                class="mt-4 rounded-xl bg-[#c99128] px-5 py-2.5 text-[10px] font-semibold text-white transition hover:bg-[#b47e1e]"
            >
                Save Preferences
            </button>
        </div>

    </section>


    {{-- =========================================================
        ACCOUNT ACTIONS
    ========================================================== --}}
    <section class="mt-5 rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
        <div class="border-b border-[#eee8df] pb-5">
            <h3 class="text-[16px] font-bold text-[#28221b]">
                Account Actions
            </h3>

            <p class="mt-1 text-[10px] text-[#91887d]">
                Security and seller account controls.
            </p>
        </div>


        <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-2">

            <div class="rounded-[16px] border border-[#e5dfd6] bg-[#fcfbf8] p-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-semibold text-[#3d3730]">
                            Sign Out Other Sessions
                        </p>

                        <p class="mt-1 text-[8px] leading-4 text-[#958c80]">
                            Log out your seller account from other browsers or devices.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="shrink-0 rounded-lg border border-[#dfd7cb] bg-white px-3 py-2 text-[8px] font-semibold text-[#675f55]"
                    >
                        Sign Out
                    </button>
                </div>
            </div>


            <div class="rounded-[16px] border border-[#ead8d8] bg-[#fdf8f8] p-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-semibold text-[#865858]">
                            Request Account Deactivation
                        </p>

                        <p class="mt-1 text-[8px] leading-4 text-[#9b7777]">
                            Request temporary deactivation of your seller account.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="shrink-0 rounded-lg border border-[#e0bebe] bg-white px-3 py-2 text-[8px] font-semibold text-[#a65f5f]"
                    >
                        Request
                    </button>
                </div>
            </div>

        </div>


        <div class="mt-4 flex items-start gap-3 rounded-[15px] border border-[#eadfc9] bg-[#fcfaf6] p-4">
            <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0 text-[#a8731f]" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="8"></circle>
                <path d="M12 8v4"></path>
                <path d="M12 16h.01"></path>
            </svg>

            <p class="text-[8px] leading-4 text-[#8e7c61]">
                Changes to your business name, address, valid ID, or business permit may require administrator verification before they take effect.
            </p>
        </div>
    </section>


    <div class="h-5"></div>

</div>

@endsection