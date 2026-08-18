@extends('layouts.admin')

@section('title', 'Account Management — SARI Admin')
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
                        border border-[#e8dfd1]
                        bg-[#fcfaf6]
                        px-3 py-1.5
                        text-[9px] font-semibold
                        uppercase tracking-[0.14em]
                        text-[#a27428]
                    "
                >
                    <span class="h-2 w-2 rounded-full bg-[#c9952f]"></span>
                    Administrator Settings
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
                        max-w-[700px]
                        text-[12px] leading-6
                        text-[#81786c]

                        sm:text-[13px]
                    "
                >
                    Manage your administrator profile, account information,
                    password, security preferences, and notification settings.
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
                        <path d="M8 8h8"></path>
                        <path d="M8 12h8"></path>
                        <path d="M8 16h5"></path>
                    </svg>

                    View Activity
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
                        <path d="M5 12.5 9.5 17 19 7"></path>
                    </svg>

                    Save Changes
                </button>

            </div>

        </div>
    </section>


    {{-- =========================================================
        PROFILE OVERVIEW + ACCOUNT INFO
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-5

            xl:grid-cols-[360px_1fr]
        "
    >

        {{-- PROFILE CARD --}}
        <div
            class="
                rounded-[22px]
                border border-[#ebe4da]
                bg-white
                p-5

                sm:p-6
            "
        >

            <div class="flex flex-col items-center text-center">

                <div
                    class="
                        relative
                        grid h-24 w-24
                        place-items-center
                        rounded-full
                        border-4 border-[#f5eddf]
                        bg-[#c99128]
                        text-[26px] font-bold
                        text-white
                        shadow-sm
                    "
                >
                    AD

                    <button
                        type="button"
                        class="
                            absolute bottom-0 right-0
                            grid h-8 w-8
                            place-items-center
                            rounded-full
                            border-2 border-white
                            bg-[#2e2923]
                            text-white
                            transition

                            hover:bg-[#c99128]
                        "
                        aria-label="Change profile photo"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M4 7h3l1.5-2h7L17 7h3v12H4z"></path>
                            <circle cx="12" cy="13" r="3"></circle>
                        </svg>
                    </button>
                </div>


                <h3
                    class="
                        mt-4
                        text-[18px] font-bold
                        tracking-[-0.02em]
                        text-[#28221b]
                    "
                >
                    Admin User
                </h3>

                <p class="mt-1 text-[11px] text-[#8e8579]">
                    admin@gmail.com
                </p>

                <span
                    class="
                        mt-3
                        rounded-full
                        border border-[#eadcc1]
                        bg-[#fbf5e8]
                        px-3 py-1.5
                        text-[9px] font-semibold
                        uppercase tracking-[0.08em]
                        text-[#a8741f]
                    "
                >
                    Super Administrator
                </span>

            </div>


            <div
                class="
                    mt-6
                    space-y-3
                    border-t border-[#eee8df]
                    pt-5
                "
            >

                <div
                    class="
                        flex items-center
                        justify-between gap-3
                        rounded-xl
                        bg-[#fcfaf7]
                        px-4 py-3
                    "
                >
                    <span class="text-[10px] text-[#948b7f]">
                        Account Status
                    </span>

                    <span
                        class="
                            inline-flex items-center gap-1.5
                            text-[10px] font-semibold
                            text-[#51836a]
                        "
                    >
                        <span class="h-2 w-2 rounded-full bg-[#63a078]"></span>
                        Active
                    </span>
                </div>


                <div
                    class="
                        flex items-center
                        justify-between gap-3
                        rounded-xl
                        bg-[#fcfaf7]
                        px-4 py-3
                    "
                >
                    <span class="text-[10px] text-[#948b7f]">
                        Last Login
                    </span>

                    <span class="text-[10px] font-semibold text-[#625a50]">
                        Today, 9:18 AM
                    </span>
                </div>


                <div
                    class="
                        flex items-center
                        justify-between gap-3
                        rounded-xl
                        bg-[#fcfaf7]
                        px-4 py-3
                    "
                >
                    <span class="text-[10px] text-[#948b7f]">
                        Member Since
                    </span>

                    <span class="text-[10px] font-semibold text-[#625a50]">
                        Aug 2026
                    </span>
                </div>

            </div>

        </div>


        {{-- PERSONAL INFORMATION --}}
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
                    <h3
                        class="
                            text-[16px] font-bold
                            tracking-[-0.02em]
                            text-[#28221b]
                        "
                    >
                        Personal Information
                    </h3>

                    <p class="mt-1 text-[10px] text-[#91887d]">
                        Update your administrator profile details.
                    </p>
                </div>

                <span
                    class="
                        w-fit
                        rounded-full
                        border border-[#e8dfd2]
                        bg-[#fcfaf7]
                        px-3 py-1.5
                        text-[9px] font-semibold
                        text-[#8b8072]
                    "
                >
                    Profile Information
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

                    {{-- First Name --}}
                    <div>
                        <label
                            for="first_name"
                            class="
                                mb-2 block
                                text-[10px] font-semibold
                                text-[#5c544a]
                            "
                        >
                            First Name
                        </label>

                        <input
                            id="first_name"
                            type="text"
                            value="Admin"
                            class="
                                h-11 w-full
                                rounded-xl
                                border border-[#e6dfd5]
                                bg-[#fcfbf9]
                                px-4
                                text-[11px]
                                text-[#302b25]
                                outline-none
                                transition

                                focus:border-[#c99a3d]
                                focus:ring-4
                                focus:ring-[#c99a3d]/10
                            "
                        >
                    </div>


                    {{-- Last Name --}}
                    <div>
                        <label
                            for="last_name"
                            class="
                                mb-2 block
                                text-[10px] font-semibold
                                text-[#5c544a]
                            "
                        >
                            Last Name
                        </label>

                        <input
                            id="last_name"
                            type="text"
                            value="User"
                            class="
                                h-11 w-full
                                rounded-xl
                                border border-[#e6dfd5]
                                bg-[#fcfbf9]
                                px-4
                                text-[11px]
                                text-[#302b25]
                                outline-none
                                transition

                                focus:border-[#c99a3d]
                                focus:ring-4
                                focus:ring-[#c99a3d]/10
                            "
                        >
                    </div>


                    {{-- Email --}}
                    <div>
                        <label
                            for="admin_email"
                            class="
                                mb-2 block
                                text-[10px] font-semibold
                                text-[#5c544a]
                            "
                        >
                            Email Address
                        </label>

                        <div class="relative">

                            <span
                                class="
                                    pointer-events-none
                                    absolute left-4 top-1/2
                                    -translate-y-1/2
                                    text-[#a1988d]
                                "
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                    <path d="m3 7 9 6 9-6"></path>
                                </svg>
                            </span>

                            <input
                                id="admin_email"
                                type="email"
                                value="admin@gmail.com"
                                class="
                                    h-11 w-full
                                    rounded-xl
                                    border border-[#e6dfd5]
                                    bg-[#fcfbf9]
                                    pl-11 pr-4
                                    text-[11px]
                                    text-[#302b25]
                                    outline-none
                                    transition

                                    focus:border-[#c99a3d]
                                    focus:ring-4
                                    focus:ring-[#c99a3d]/10
                                "
                            >

                        </div>
                    </div>


                    {{-- Phone --}}
                    <div>
                        <label
                            for="phone"
                            class="
                                mb-2 block
                                text-[10px] font-semibold
                                text-[#5c544a]
                            "
                        >
                            Phone Number
                        </label>

                        <div class="relative">

                            <span
                                class="
                                    pointer-events-none
                                    absolute left-4 top-1/2
                                    -translate-y-1/2
                                    text-[#a1988d]
                                "
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path d="M6 3h4l2 5-3 2c1.5 3 3.5 5 6.5 6.5l2-3 4.5 2V20c0 1-1 2-2 2C10 22 2 14 2 4c0-1 1-1 2-1h2Z"></path>
                                </svg>
                            </span>

                            <input
                                id="phone"
                                type="text"
                                value="+63 912 345 6789"
                                class="
                                    h-11 w-full
                                    rounded-xl
                                    border border-[#e6dfd5]
                                    bg-[#fcfbf9]
                                    pl-11 pr-4
                                    text-[11px]
                                    text-[#302b25]
                                    outline-none
                                    transition

                                    focus:border-[#c99a3d]
                                    focus:ring-4
                                    focus:ring-[#c99a3d]/10
                                "
                            >

                        </div>
                    </div>


                    {{-- Role --}}
                    <div>
                        <label
                            for="role"
                            class="
                                mb-2 block
                                text-[10px] font-semibold
                                text-[#5c544a]
                            "
                        >
                            Administrator Role
                        </label>

                        <select
                            id="role"
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
                            <option selected>Super Administrator</option>
                            <option>Administrator</option>
                            <option>Moderator</option>
                        </select>
                    </div>


                    {{-- Employee ID --}}
                    <div>
                        <label
                            for="employee_id"
                            class="
                                mb-2 block
                                text-[10px] font-semibold
                                text-[#5c544a]
                            "
                        >
                            Administrator ID
                        </label>

                        <input
                            id="employee_id"
                            type="text"
                            value="SARI-ADM-001"
                            readonly
                            class="
                                h-11 w-full
                                rounded-xl
                                border border-[#e6dfd5]
                                bg-[#f6f3ee]
                                px-4
                                text-[11px]
                                font-medium
                                text-[#81786c]
                                outline-none
                            "
                        >
                    </div>

                </div>


                <div
                    class="
                        mt-5
                        flex items-center justify-end
                        border-t border-[#eee8df]
                        pt-5
                    "
                >
                    <button
                        type="button"
                        class="
                            inline-flex items-center gap-2
                            rounded-xl
                            bg-[#c99128]
                            px-5 py-2.5
                            text-[11px] font-semibold
                            text-white
                            transition

                            hover:bg-[#b47e1e]
                        "
                    >
                        Save Profile
                    </button>
                </div>

            </form>

        </div>

    </section>


    {{-- =========================================================
        PASSWORD + SECURITY
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
        <div
            class="
                rounded-[22px]
                border border-[#ebe4da]
                bg-white
                p-5

                sm:p-6
            "
        >

            <div class="flex items-start gap-3">

                <div
                    class="
                        grid h-11 w-11
                        shrink-0 place-items-center
                        rounded-xl
                        border border-[#eadfc8]
                        bg-[#fbf6ec]
                        text-[#b87f20]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                        <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                    </svg>
                </div>

                <div>
                    <h3 class="text-[15px] font-bold text-[#28221b]">
                        Change Password
                    </h3>

                    <p class="mt-1 text-[10px] leading-5 text-[#91887d]">
                        Use a strong password to keep your administrator account secure.
                    </p>
                </div>

            </div>


            <form class="mt-5 space-y-4">

                <div>
                    <label
                        for="current_password"
                        class="
                            mb-2 block
                            text-[10px] font-semibold
                            text-[#5c544a]
                        "
                    >
                        Current Password
                    </label>

                    <input
                        id="current_password"
                        type="password"
                        placeholder="Enter current password"
                        class="
                            h-11 w-full
                            rounded-xl
                            border border-[#e6dfd5]
                            bg-[#fcfbf9]
                            px-4
                            text-[11px]
                            outline-none

                            focus:border-[#c99a3d]
                            focus:ring-4
                            focus:ring-[#c99a3d]/10
                        "
                    >
                </div>


                <div
                    class="
                        grid grid-cols-1
                        gap-4

                        sm:grid-cols-2
                    "
                >

                    <div>
                        <label
                            for="new_password"
                            class="
                                mb-2 block
                                text-[10px] font-semibold
                                text-[#5c544a]
                            "
                        >
                            New Password
                        </label>

                        <input
                            id="new_password"
                            type="password"
                            placeholder="New password"
                            class="
                                h-11 w-full
                                rounded-xl
                                border border-[#e6dfd5]
                                bg-[#fcfbf9]
                                px-4
                                text-[11px]
                                outline-none

                                focus:border-[#c99a3d]
                                focus:ring-4
                                focus:ring-[#c99a3d]/10
                            "
                        >
                    </div>


                    <div>
                        <label
                            for="confirm_password"
                            class="
                                mb-2 block
                                text-[10px] font-semibold
                                text-[#5c544a]
                            "
                        >
                            Confirm Password
                        </label>

                        <input
                            id="confirm_password"
                            type="password"
                            placeholder="Confirm password"
                            class="
                                h-11 w-full
                                rounded-xl
                                border border-[#e6dfd5]
                                bg-[#fcfbf9]
                                px-4
                                text-[11px]
                                outline-none

                                focus:border-[#c99a3d]
                                focus:ring-4
                                focus:ring-[#c99a3d]/10
                            "
                        >
                    </div>

                </div>


                <div
                    class="
                        rounded-xl
                        border border-[#ece6dd]
                        bg-[#fcfaf7]
                        px-4 py-3
                        text-[9px] leading-5
                        text-[#8e857a]
                    "
                >
                    Password should contain at least 8 characters,
                    including uppercase, lowercase, number, and special character.
                </div>


                <div class="flex justify-end">
                    <button
                        type="button"
                        class="
                            rounded-xl
                            bg-[#2e2923]
                            px-5 py-2.5
                            text-[11px] font-semibold
                            text-white
                            transition

                            hover:bg-[#17140f]
                        "
                    >
                        Update Password
                    </button>
                </div>

            </form>

        </div>


        {{-- SECURITY --}}
        <div
            class="
                rounded-[22px]
                border border-[#ebe4da]
                bg-white
                p-5

                sm:p-6
            "
        >

            <div class="flex items-start gap-3">

                <div
                    class="
                        grid h-11 w-11
                        shrink-0 place-items-center
                        rounded-xl
                        border border-[#dce7e1]
                        bg-[#f2f7f4]
                        text-[#58816a]
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
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                </div>

                <div>
                    <h3 class="text-[15px] font-bold text-[#28221b]">
                        Security
                    </h3>

                    <p class="mt-1 text-[10px] leading-5 text-[#91887d]">
                        Review your sign-in protection and administrator sessions.
                    </p>
                </div>

            </div>


            <div class="mt-5 space-y-3">

                {{-- 2FA --}}
                <div
                    class="
                        flex items-center justify-between gap-4
                        rounded-[16px]
                        border border-[#ece6dd]
                        bg-[#fcfbf8]
                        p-4
                    "
                >
                    <div>
                        <p class="text-[11px] font-semibold text-[#37312b]">
                            Two-Factor Authentication
                        </p>

                        <p class="mt-1 text-[9px] leading-4 text-[#91887d]">
                            Add an extra layer of protection to your account.
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
                        aria-label="Toggle two factor authentication"
                    >
                        <span
                            class="
                                absolute right-1 top-1
                                h-4 w-4
                                rounded-full
                                bg-white
                                shadow-sm
                            "
                        ></span>
                    </button>
                </div>


                {{-- Login alerts --}}
                <div
                    class="
                        flex items-center justify-between gap-4
                        rounded-[16px]
                        border border-[#ece6dd]
                        bg-[#fcfbf8]
                        p-4
                    "
                >
                    <div>
                        <p class="text-[11px] font-semibold text-[#37312b]">
                            Login Alerts
                        </p>

                        <p class="mt-1 text-[9px] leading-4 text-[#91887d]">
                            Receive an alert when your account signs in on a new device.
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
                        aria-label="Toggle login alerts"
                    >
                        <span
                            class="
                                absolute right-1 top-1
                                h-4 w-4
                                rounded-full
                                bg-white
                                shadow-sm
                            "
                        ></span>
                    </button>
                </div>


                {{-- Active Session --}}
                <div
                    class="
                        rounded-[16px]
                        border border-[#ece6dd]
                        bg-[#fcfbf8]
                        p-4
                    "
                >
                    <div
                        class="
                            flex flex-col gap-3
                            sm:flex-row
                            sm:items-center
                            sm:justify-between
                        "
                    >

                        <div class="flex items-start gap-3">

                            <div
                                class="
                                    grid h-9 w-9
                                    shrink-0 place-items-center
                                    rounded-lg
                                    bg-white
                                    text-[#6f675d]
                                    shadow-sm
                                "
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <rect x="3" y="4" width="18" height="14" rx="2"></rect>
                                    <path d="M8 21h8"></path>
                                    <path d="M12 18v3"></path>
                                </svg>
                            </div>

                            <div>
                                <p class="text-[11px] font-semibold text-[#37312b]">
                                    Windows PC • Chrome
                                </p>

                                <p class="mt-1 text-[9px] text-[#91887d]">
                                    Current session • Philippines
                                </p>
                            </div>

                        </div>

                        <span
                            class="
                                w-fit rounded-full
                                bg-[#eef6f1]
                                px-2.5 py-1
                                text-[9px] font-semibold
                                text-[#548069]
                            "
                        >
                            Active
                        </span>

                    </div>
                </div>

            </div>

        </div>

    </section>


    {{-- =========================================================
        NOTIFICATION PREFERENCES + DANGER ZONE
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-5

            xl:grid-cols-[1.2fr_.8fr]
        "
    >

        {{-- NOTIFICATIONS --}}
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
                <h3 class="text-[15px] font-bold text-[#28221b]">
                    Notification Preferences
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Choose which admin activities should send you notifications.
                </p>
            </div>


            <div class="mt-5 divide-y divide-[#eee8df]">

                <label
                    class="
                        flex cursor-pointer
                        items-center justify-between
                        gap-4 py-4
                    "
                >
                    <div>
                        <p class="text-[11px] font-semibold text-[#37312b]">
                            Registration Requests
                        </p>

                        <p class="mt-1 text-[9px] text-[#91887d]">
                            Notify me when new buyer, seller, or courier applications arrive.
                        </p>
                    </div>

                    <input
                        type="checkbox"
                        checked
                        class="
                            h-4 w-4
                            shrink-0
                            accent-[#c99128]
                        "
                    >
                </label>


                <label
                    class="
                        flex cursor-pointer
                        items-center justify-between
                        gap-4 py-4
                    "
                >
                    <div>
                        <p class="text-[11px] font-semibold text-[#37312b]">
                            Complaints & Disputes
                        </p>

                        <p class="mt-1 text-[9px] text-[#91887d]">
                            Notify me when urgent complaints require administrator action.
                        </p>
                    </div>

                    <input
                        type="checkbox"
                        checked
                        class="
                            h-4 w-4
                            shrink-0
                            accent-[#c99128]
                        "
                    >
                </label>


                <label
                    class="
                        flex cursor-pointer
                        items-center justify-between
                        gap-4 py-4
                    "
                >
                    <div>
                        <p class="text-[11px] font-semibold text-[#37312b]">
                            Seller Compliance
                        </p>

                        <p class="mt-1 text-[9px] text-[#91887d]">
                            Receive alerts about policy violations and flagged products.
                        </p>
                    </div>

                    <input
                        type="checkbox"
                        checked
                        class="
                            h-4 w-4
                            shrink-0
                            accent-[#c99128]
                        "
                    >
                </label>


                <label
                    class="
                        flex cursor-pointer
                        items-center justify-between
                        gap-4 py-4
                    "
                >
                    <div>
                        <p class="text-[11px] font-semibold text-[#37312b]">
                            Commission Reports
                        </p>

                        <p class="mt-1 text-[9px] text-[#91887d]">
                            Receive periodic commission and marketplace performance reports.
                        </p>
                    </div>

                    <input
                        type="checkbox"
                        class="
                            h-4 w-4
                            shrink-0
                            accent-[#c99128]
                        "
                    >
                </label>

            </div>


            <div
                class="
                    mt-3
                    flex justify-end
                    border-t border-[#eee8df]
                    pt-5
                "
            >
                <button
                    type="button"
                    class="
                        rounded-xl
                        border border-[#dfd5c7]
                        bg-white
                        px-5 py-2.5
                        text-[11px] font-semibold
                        text-[#62594e]
                        transition

                        hover:border-[#cdb993]
                        hover:bg-[#fcf8f1]
                    "
                >
                    Save Preferences
                </button>
            </div>

        </div>


        {{-- ACCOUNT ACTIONS --}}
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
                <h3 class="text-[15px] font-bold text-[#28221b]">
                    Account Actions
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Manage sensitive administrator account actions.
                </p>
            </div>


            <div class="mt-5 space-y-3">

                <button
                    type="button"
                    class="
                        flex w-full
                        items-center justify-between
                        rounded-[15px]
                        border border-[#e8e1d7]
                        bg-[#fcfbf8]
                        p-4
                        text-left
                        transition

                        hover:border-[#d7c7ac]
                    "
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="
                                grid h-10 w-10
                                place-items-center
                                rounded-xl
                                bg-white
                                text-[#6f665b]
                            "
                        >
                            <svg
                                viewBox="0 0 24 24"
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                            >
                                <path d="M4 12h12"></path>
                                <path d="m12 6 6 6-6 6"></path>
                            </svg>
                        </div>

                        <div>
                            <p class="text-[11px] font-semibold text-[#39332c]">
                                Sign Out Other Sessions
                            </p>

                            <p class="mt-1 text-[9px] text-[#91887d]">
                                End other active administrator sessions.
                            </p>
                        </div>

                    </div>

                    <svg
                        viewBox="0 0 24 24"
                        class="h-4 w-4 text-[#9c9388]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>

                </button>


                <button
                    type="button"
                    class="
                        flex w-full
                        items-center justify-between
                        rounded-[15px]
                        border border-[#ead8d8]
                        bg-[#fdf8f8]
                        p-4
                        text-left
                        transition

                        hover:border-[#dfbebe]
                        hover:bg-[#fcf2f2]
                    "
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="
                                grid h-10 w-10
                                place-items-center
                                rounded-xl
                                bg-[#f9eeee]
                                text-[#ad6262]
                            "
                        >
                            <svg
                                viewBox="0 0 24 24"
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                            >
                                <path d="M4 7h16"></path>
                                <path d="M9 7V4h6v3"></path>
                                <path d="M7 7l1 13h8l1-13"></path>
                            </svg>
                        </div>

                        <div>
                            <p class="text-[11px] font-semibold text-[#7e4444]">
                                Deactivate Account
                            </p>

                            <p class="mt-1 text-[9px] text-[#9b7777]">
                                Temporarily disable this administrator account.
                            </p>
                        </div>

                    </div>

                    <svg
                        viewBox="0 0 24 24"
                        class="h-4 w-4 text-[#b88a8a]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>

                </button>

            </div>


            <div
                class="
                    mt-5
                    rounded-[15px]
                    border border-[#eee5d7]
                    bg-[#fcfaf6]
                    p-4
                "
            >
                <p class="text-[10px] font-semibold text-[#5e554a]">
                    Administrator access
                </p>

                <p class="mt-1 text-[9px] leading-5 text-[#91887d]">
                    Sensitive account actions may require password confirmation
                    and should only be used when necessary.
                </p>
            </div>

        </div>

    </section>


    <div class="h-5"></div>

</div>

@endsection