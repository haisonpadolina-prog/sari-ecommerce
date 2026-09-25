@extends('layouts.courier')

@section('title', 'Rider Account')
@section('header-title', 'Account')
@section('header-subtitle', 'Rider Profile & Vehicle')

@push('styles')
<style>
    .profile-card {
        border: 1px solid #eee4d3;
        background: #fffdf9;
        box-shadow: 0 8px 24px rgba(75, 59, 30, .035);
    }

    .profile-field {
        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            background-color .2s ease;
    }

    .profile-field:hover {
        border-color: #dbc9aa;
    }

    .profile-field:focus {
        border-color: #d9930a;
        box-shadow: 0 0 0 4px rgba(217, 147, 10, .08);
        outline: none;
    }

    .profile-grid {
        background-image:
            linear-gradient(
                to right,
                rgba(225, 213, 192, .13) 1px,
                transparent 1px
            ),
            linear-gradient(
                to bottom,
                rgba(225, 213, 192, .13) 1px,
                transparent 1px
            );

        background-size: 28px 28px;
    }
</style>
@endpush


@section('content')
<div class="mx-auto max-w-[1250px]">

    {{-- =========================================================
         SUCCESS
    ========================================================== --}}
    @if (session('success'))

        <div
            class="
                mb-5 flex items-start gap-3
                rounded-2xl
                border border-emerald-200
                bg-emerald-50
                px-4 py-3.5
                text-emerald-700
            "
        >
            <div
                class="
                    mt-0.5 grid h-6 w-6
                    shrink-0 place-items-center
                    rounded-full
                    bg-emerald-600
                    text-white
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-3.5 w-3.5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m5 12 4 4L19 6"></path>
                </svg>
            </div>

            <p class="text-[10px] font-semibold leading-5">
                {{ session('success') }}
            </p>
        </div>

    @endif


    {{-- =========================================================
         ERROR
    ========================================================== --}}
    @if ($errors->any())

        <div
            class="
                mb-5 flex items-start gap-3
                rounded-2xl
                border border-rose-200
                bg-rose-50
                px-4 py-3.5
                text-rose-700
            "
        >
            <div
                class="
                    mt-0.5 grid h-6 w-6
                    shrink-0 place-items-center
                    rounded-full
                    bg-rose-600
                    text-white
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-3.5 w-3.5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M12 8v5"></path>
                    <path d="M12 16.5h.01"></path>
                    <circle cx="12" cy="12" r="9"></circle>
                </svg>
            </div>

            <p class="text-[10px] font-semibold leading-5">
                {{ $errors->first() }}
            </p>
        </div>

    @endif


    {{-- =========================================================
         PAGE INTRO
    ========================================================== --}}
    <section
        class="
            reveal
            flex flex-col justify-between gap-5
            border-b border-[#eee4d3]
            pb-5
            lg:flex-row lg:items-end
        "
    >

        <div class="max-w-2xl">

            <div class="flex flex-wrap items-center gap-2.5">

                <span
                    class="
                        inline-flex items-center gap-2
                        rounded-full
                        border border-[#eadfc9]
                        bg-[#fffaf1]
                        px-3 py-1.5
                        text-[9px] font-bold uppercase
                        tracking-[.1em]
                        text-[#a66d08]
                    "
                >
                    <span
                        class="
                            h-1.5 w-1.5
                            rounded-full
                            bg-[#d9930a]
                        "
                    ></span>

                    Rider Account
                </span>


                <span
                    class="
                        inline-flex items-center gap-2
                        rounded-full
                        border border-[#e4ded4]
                        bg-white
                        px-3 py-1.5
                        text-[9px] font-semibold
                        text-[#817769]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="12" cy="8" r="3.5"></circle>
                        <path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path>
                    </svg>

                    Verified Rider
                </span>

            </div>


            <h2
                class="
                    mt-3
                    text-[20px] font-bold
                    tracking-[-.025em]
                    text-[#211d17]
                    sm:text-[22px]
                "
            >
                Manage your rider account
            </h2>


            <p
                class="
                    mt-1.5
                    max-w-xl
                    text-[10px] leading-5
                    text-[#817769]
                    sm:text-[11px]
                "
            >
                Keep your contact information, vehicle details,
                availability status, and account security up to date.
            </p>

        </div>


        <a
            href="{{ route('courier.dashboard') }}"
            class="
                inline-flex min-h-10
                items-center justify-center gap-2
                self-start
                rounded-xl
                border border-[#e6dccb]
                bg-white
                px-4 py-2.5
                text-[10px] font-semibold
                text-[#51483d]
                shadow-sm
                transition
                hover:border-[#d9be8c]
                hover:bg-[#fffaf1]
                hover:text-[#a66d08]
                lg:self-auto
            "
        >
            <svg
                viewBox="0 0 24 24"
                class="h-3.5 w-3.5"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="M3 12h18"></path>
                <path d="M8 7l-5 5 5 5"></path>
            </svg>

            Back to Dashboard
        </a>

    </section>


    {{-- =========================================================
         PROFILE SUMMARY
    ========================================================== --}}
    <section
        class="
            reveal profile-card profile-grid
            mt-5 overflow-hidden
            rounded-[20px]
        "
    >

        <div
            class="
                flex flex-col gap-5
                p-5
                md:flex-row
                md:items-center
                md:justify-between
                sm:p-6
            "
        >

            {{-- =================================================
                 IDENTITY
            ================================================== --}}
            <div class="flex min-w-0 items-center gap-4">

                <div
                    class="
                        relative
                        grid h-16 w-16
                        shrink-0 place-items-center
                        rounded-[20px]
                        bg-[#3C6E91]
                        text-[20px] font-bold
                        text-white
                        shadow-[0_10px_25px_rgba(60,110,145,.17)]
                    "
                >
                    {{ strtoupper(
                        substr(
                            $profile['full_name'] ?: 'R',
                            0,
                            1
                        )
                    ) }}


                    <span
                        class="
                            absolute
                            -bottom-1 -right-1
                            grid h-5 w-5
                            place-items-center
                            rounded-full
                            border-[3px] border-[#fffdf9]
                            bg-[#4F7D63]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-2.5 w-2.5 text-white"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                        >
                            <path d="m5 12 4 4L19 6"></path>
                        </svg>
                    </span>

                </div>


                <div class="min-w-0">

                    <div
                        class="
                            flex flex-wrap
                            items-center gap-2
                        "
                    >

                        <h3
                            class="
                                truncate
                                text-[16px] font-bold
                                tracking-[-.02em]
                                text-[#211d17]
                                sm:text-[18px]
                            "
                        >
                            {{ $profile['full_name'] }}
                        </h3>


                        <span
                            class="
                                rounded-full
                                border border-[#e5eee8]
                                bg-[#f3f8f5]
                                px-2.5 py-1
                                text-[8px] font-bold uppercase
                                tracking-[.08em]
                                text-[#4F7D63]
                            "
                        >
                            Verified
                        </span>

                    </div>


                    <p
                        class="
                            mt-1.5
                            text-[9px]
                            text-[#817769]
                        "
                    >
                        {{ $profile['vehicle_type'] }}

                        @if ($profile['vehicle_model'] !== '—')
                            · {{ $profile['vehicle_model'] }}
                        @endif
                    </p>


                    <div
                        class="
                            mt-3
                            flex flex-wrap
                            items-center gap-x-4 gap-y-2
                        "
                    >

                        <div
                            class="
                                flex items-center gap-1.5
                                text-[9px]
                                text-[#756b5f]
                            "
                        >
                            <svg
                                viewBox="0 0 24 24"
                                class="h-3.5 w-3.5 text-[#b47a11]"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    d="
                                        m12 3
                                        2.7 5.5
                                        6 .9
                                        -4.4 4.3
                                        1 6.1
                                        -5.3-2.9
                                        -5.3 2.9
                                        1-6.1
                                        -4.4-4.3
                                        6-.9
                                        L12 3Z
                                    "
                                ></path>
                            </svg>

                            <span class="font-semibold">
                                {{ $profile['rating'] }}
                            </span>

                            rating
                        </div>


                        <div
                            class="
                                flex items-center gap-1.5
                                text-[9px]
                                text-[#756b5f]
                            "
                        >
                            <svg
                                viewBox="0 0 24 24"
                                class="h-3.5 w-3.5 text-[#b47a11]"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="m7.5 12 3 3 6-7"></path>
                            </svg>

                            <span class="font-semibold">
                                {{ $profile['completed_deliveries'] }}
                            </span>

                            completed
                        </div>


                        <div
                            class="
                                flex items-center gap-1.5
                                text-[9px]
                                text-[#756b5f]
                            "
                        >
                            <svg
                                viewBox="0 0 24 24"
                                class="h-3.5 w-3.5 text-[#b47a11]"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect
                                    x="4"
                                    y="5"
                                    width="16"
                                    height="15"
                                    rx="2"
                                ></rect>

                                <path d="M8 3v4"></path>
                                <path d="M16 3v4"></path>
                            </svg>

                            Joined {{ $profile['joined'] }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 AVAILABILITY
            ================================================== --}}
            <form
                method="POST"
                action="{{ route('courier.profile.availability') }}"
                class="
                    w-full
                    rounded-2xl
                    border border-[#eadfc9]
                    bg-[#fffdf9]
                    p-4
                    md:w-auto
                    md:min-w-[300px]
                "
            >
                @csrf
                @method('PATCH')


                <div
                    class="
                        flex items-start
                        justify-between gap-3
                    "
                >

                    <div>

                        <p
                            class="
                                text-[8px] font-bold uppercase
                                tracking-[.1em]
                                text-[#9a9082]
                            "
                        >
                            Rider Availability
                        </p>


                        <div
                            class="
                                mt-1.5
                                flex items-center gap-2
                            "
                        >
                            <span
                                class="
                                    h-2 w-2
                                    rounded-full

                                    {{ $profile['availability_status'] === 'online'
                                        ? 'bg-[#4F7D63]'
                                        : 'bg-[#b8afa1]'
                                    }}
                                "
                            ></span>


                            <p
                                class="
                                    text-[10px] font-semibold
                                    text-[#413a31]
                                "
                            >
                                {{ $profile['availability_status'] === 'online'
                                    ? 'Available for assignments'
                                    : 'Currently offline'
                                }}
                            </p>

                        </div>

                    </div>


                    <div
                        class="
                            grid h-8 w-8
                            shrink-0 place-items-center
                            rounded-lg
                            bg-[#fff2d8]
                            text-[#b77900]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M8 12h8"></path>
                            <path d="M12 8v8"></path>
                        </svg>
                    </div>

                </div>


                <div
                    class="
                        mt-4
                        flex gap-2
                    "
                >

                    <div class="relative flex-1">

                        <select
                            name="availability_status"
                            class="
                                profile-field
                                h-10 w-full
                                appearance-none
                                rounded-xl
                                border border-[#e6dccb]
                                bg-white
                                px-3 pr-8
                                text-[9px] font-semibold
                                text-[#51483d]
                            "
                        >
                            <option
                                value="online"
                                @selected(
                                    $profile['availability_status']
                                    === 'online'
                                )
                            >
                                Online
                            </option>

                            <option
                                value="offline"
                                @selected(
                                    $profile['availability_status']
                                    === 'offline'
                                )
                            >
                                Offline
                            </option>
                        </select>


                        <svg
                            viewBox="0 0 24 24"
                            class="
                                pointer-events-none
                                absolute right-3 top-1/2
                                h-3 w-3
                                -translate-y-1/2
                                text-[#9a9082]
                            "
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>

                    </div>


                    <button
                        type="submit"
                        class="
                            rounded-xl
                            bg-[#d9930a]
                            px-4
                            text-[9px] font-semibold
                            text-white
                            transition
                            hover:bg-[#c98505]
                        "
                    >
                        Update
                    </button>

                </div>

            </form>

        </div>

    </section>


    {{-- =========================================================
         MAIN PROFILE FORM
    ========================================================== --}}
    <form
        method="POST"
        action="{{ route('courier.profile.update') }}"
        class="
            mt-5 grid gap-5
            xl:grid-cols-[minmax(0,1.15fr)_minmax(340px,.85fr)]
        "
    >
        @csrf
        @method('PATCH')


        {{-- =====================================================
             PERSONAL INFORMATION
        ====================================================== --}}
        <section
            class="
                reveal profile-card
                overflow-hidden
                rounded-[20px]
            "
        >

            <div
                class="
                    flex items-center gap-3
                    border-b border-[#eee4d3]
                    px-5 py-4
                "
            >

                <div
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-xl
                        bg-[#fff2d8]
                        text-[#b77900]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="12" cy="8" r="3.5"></circle>
                        <path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path>
                    </svg>
                </div>


                <div>

                    <h3
                        class="
                            text-[13px] font-bold
                            text-[#211d17]
                        "
                    >
                        Personal Information
                    </h3>

                    <p
                        class="
                            mt-1
                            text-[9px]
                            text-[#918677]
                        "
                    >
                        Your rider contact and account details.
                    </p>

                </div>

            </div>


            <div
                class="
                    grid gap-4
                    p-5
                    sm:grid-cols-2
                "
            >

                {{-- FIRST NAME --}}
                <label class="block">

                    <span
                        class="
                            text-[9px] font-semibold
                            text-[#51483d]
                        "
                    >
                        First Name
                    </span>

                    <input
                        name="first_name"
                        value="{{ old(
                            'first_name',
                            $account->first_name
                        ) }}"
                        required
                        class="
                            profile-field
                            mt-2 h-11 w-full
                            rounded-xl
                            border border-[#e6dccb]
                            bg-white
                            px-3.5
                            text-[10px]
                            text-[#413a31]
                        "
                    >

                </label>


                {{-- LAST NAME --}}
                <label class="block">

                    <span
                        class="
                            text-[9px] font-semibold
                            text-[#51483d]
                        "
                    >
                        Last Name
                    </span>

                    <input
                        name="last_name"
                        value="{{ old(
                            'last_name',
                            $account->last_name
                        ) }}"
                        required
                        class="
                            profile-field
                            mt-2 h-11 w-full
                            rounded-xl
                            border border-[#e6dccb]
                            bg-white
                            px-3.5
                            text-[10px]
                            text-[#413a31]
                        "
                    >

                </label>


                {{-- EMAIL --}}
                <label class="block">

                    <span
                        class="
                            text-[9px] font-semibold
                            text-[#51483d]
                        "
                    >
                        Email Address
                    </span>

                    <div class="relative mt-2">

                        <svg
                            viewBox="0 0 24 24"
                            class="
                                pointer-events-none
                                absolute left-3.5 top-1/2
                                h-3.5 w-3.5
                                -translate-y-1/2
                                text-[#a09687]
                            "
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <rect
                                x="3"
                                y="5"
                                width="18"
                                height="14"
                                rx="2"
                            ></rect>

                            <path d="m3 7 9 6 9-6"></path>
                        </svg>

                        <input
                            name="email"
                            type="email"
                            value="{{ old(
                                'email',
                                $account->email
                            ) }}"
                            required
                            class="
                                profile-field
                                h-11 w-full
                                rounded-xl
                                border border-[#e6dccb]
                                bg-white
                                pl-9 pr-3.5
                                text-[10px]
                                text-[#413a31]
                            "
                        >

                    </div>

                </label>


                {{-- CONTACT --}}
                <label class="block">

                    <span
                        class="
                            text-[9px] font-semibold
                            text-[#51483d]
                        "
                    >
                        Contact Number
                    </span>

                    <div class="relative mt-2">

                        <svg
                            viewBox="0 0 24 24"
                            class="
                                pointer-events-none
                                absolute left-3.5 top-1/2
                                h-3.5 w-3.5
                                -translate-y-1/2
                                text-[#a09687]
                            "
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                d="
                                    M6 3h3l1.5 4-2 1.5
                                    a14 14 0 0 0 7 7
                                    l1.5-2 4 1.5v3
                                    c0 1.1-.9 2-2 2
                                    C10.7 20 4 13.3 4 5
                                    c0-1.1.9-2 2-2Z
                                "
                            ></path>
                        </svg>

                        <input
                            name="contact_no"
                            value="{{ old(
                                'contact_no',
                                $account->contact_no
                            ) }}"
                            required
                            class="
                                profile-field
                                h-11 w-full
                                rounded-xl
                                border border-[#e6dccb]
                                bg-white
                                pl-9 pr-3.5
                                text-[10px]
                                text-[#413a31]
                            "
                        >

                    </div>

                </label>


                {{-- ADDRESS --}}
                <label
                    class="
                        block
                        sm:col-span-2
                    "
                >

                    <span
                        class="
                            text-[9px] font-semibold
                            text-[#51483d]
                        "
                    >
                        Street Address
                    </span>

                    <div class="relative mt-2">

                        <svg
                            viewBox="0 0 24 24"
                            class="
                                pointer-events-none
                                absolute left-3.5 top-1/2
                                h-3.5 w-3.5
                                -translate-y-1/2
                                text-[#a09687]
                            "
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                            <circle cx="12" cy="10" r="2.5"></circle>
                        </svg>

                        <input
                            name="street_address"
                            value="{{ old(
                                'street_address',
                                $account->street_address
                            ) }}"
                            required
                            class="
                                profile-field
                                h-11 w-full
                                rounded-xl
                                border border-[#e6dccb]
                                bg-white
                                pl-9 pr-3.5
                                text-[10px]
                                text-[#413a31]
                            "
                        >

                    </div>


                    @if (!empty($profile['address']))

                        <p
                            class="
                                mt-2
                                text-[8px] leading-4
                                text-[#918677]
                            "
                        >
                            Registered address:
                            {{ $profile['address'] }}
                        </p>

                    @endif

                </label>

            </div>

        </section>


        {{-- =====================================================
             VEHICLE INFORMATION
        ====================================================== --}}
        <section
            class="
                reveal profile-card
                overflow-hidden
                rounded-[20px]
            "
        >

            <div
                class="
                    flex items-center gap-3
                    border-b border-[#eee4d3]
                    px-5 py-4
                "
            >

                <div
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-xl
                        bg-[#fff2d8]
                        text-[#b77900]
                    "
                >
                    <svg
                        viewBox="0 0 28 24"
                        class="h-[18px] w-[20px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="7" cy="18" r="3"></circle>
                        <circle cx="21" cy="18" r="3"></circle>
                        <path d="M7 18h6l4-8h4"></path>
                        <path d="M13 18 9 9h5"></path>
                        <path d="m17 10 4 8"></path>
                    </svg>
                </div>


                <div>

                    <h3
                        class="
                            text-[13px] font-bold
                            text-[#211d17]
                        "
                    >
                        Vehicle Information
                    </h3>

                    <p
                        class="
                            mt-1
                            text-[9px]
                            text-[#918677]
                        "
                    >
                        Vehicle and license details used for rider operations.
                    </p>

                </div>

            </div>


            <div class="space-y-4 p-5">

                {{-- VEHICLE TYPE --}}
                <label class="block">

                    <span
                        class="
                            text-[9px] font-semibold
                            text-[#51483d]
                        "
                    >
                        Vehicle Type
                    </span>

                    <input
                        name="vehicle_type"
                        value="{{ old(
                            'vehicle_type',
                            $account->vehicle_type
                        ) }}"
                        required
                        class="
                            profile-field
                            mt-2 h-11 w-full
                            rounded-xl
                            border border-[#e6dccb]
                            bg-white
                            px-3.5
                            text-[10px]
                            text-[#413a31]
                        "
                    >

                </label>


                {{-- VEHICLE MODEL --}}
                <label class="block">

                    <span
                        class="
                            text-[9px] font-semibold
                            text-[#51483d]
                        "
                    >
                        Vehicle Model
                    </span>

                    <input
                        name="vehicle_model"
                        value="{{ old(
                            'vehicle_model',
                            $account->vehicle_model
                        ) }}"
                        class="
                            profile-field
                            mt-2 h-11 w-full
                            rounded-xl
                            border border-[#e6dccb]
                            bg-white
                            px-3.5
                            text-[10px]
                            text-[#413a31]
                        "
                    >

                </label>


                <div class="grid gap-4 sm:grid-cols-2">

                    {{-- PLATE NUMBER --}}
                    <label class="block">

                        <span
                            class="
                                text-[9px] font-semibold
                                text-[#51483d]
                            "
                        >
                            Plate Number
                        </span>

                        <input
                            name="plate_number"
                            value="{{ old(
                                'plate_number',
                                $account->plate_number
                            ) }}"
                            required
                            class="
                                profile-field
                                mt-2 h-11 w-full
                                rounded-xl
                                border border-[#e6dccb]
                                bg-white
                                px-3.5
                                text-[10px]
                                uppercase
                                text-[#413a31]
                            "
                        >

                    </label>


                    {{-- LICENSE --}}
                    <label class="block">

                        <span
                            class="
                                text-[9px] font-semibold
                                text-[#51483d]
                            "
                        >
                            License Number
                        </span>

                        <input
                            name="license_number"
                            value="{{ old(
                                'license_number',
                                $account->license_number
                            ) }}"
                            class="
                                profile-field
                                mt-2 h-11 w-full
                                rounded-xl
                                border border-[#e6dccb]
                                bg-white
                                px-3.5
                                text-[10px]
                                text-[#413a31]
                            "
                        >

                    </label>

                </div>


                <div
                    class="
                        rounded-xl
                        bg-[#fbf7ef]
                        px-4 py-3.5
                    "
                >

                    <div class="flex items-start gap-2.5">

                        <svg
                            viewBox="0 0 24 24"
                            class="
                                mt-0.5
                                h-3.5 w-3.5
                                shrink-0
                                text-[#a66d08]
                            "
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 10v6"></path>
                            <path d="M12 7h.01"></path>
                        </svg>


                        <p
                            class="
                                text-[8px] leading-4
                                text-[#817769]
                            "
                        >
                            Keep your vehicle and license information
                            accurate so Logistics can identify your
                            registered rider details.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             SECURITY
        ====================================================== --}}
        <section
            class="
                reveal profile-card
                overflow-hidden
                rounded-[20px]
                xl:col-span-2
            "
        >

            <div
                class="
                    flex items-center gap-3
                    border-b border-[#eee4d3]
                    px-5 py-4
                "
            >

                <div
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-xl
                        bg-[#fff2d8]
                        text-[#b77900]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <rect
                            x="5"
                            y="10"
                            width="14"
                            height="10"
                            rx="2"
                        ></rect>

                        <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                        <path d="M12 14v2"></path>
                    </svg>
                </div>


                <div>

                    <h3
                        class="
                            text-[13px] font-bold
                            text-[#211d17]
                        "
                    >
                        Account Security
                    </h3>

                    <p
                        class="
                            mt-1
                            text-[9px]
                            text-[#918677]
                        "
                    >
                        Leave the password fields blank if you do not
                        want to change your password.
                    </p>

                </div>

            </div>


            <div
                class="
                    grid gap-4
                    p-5
                    md:grid-cols-3
                "
            >

                {{-- CURRENT PASSWORD --}}
                <label class="block">

                    <span
                        class="
                            text-[9px] font-semibold
                            text-[#51483d]
                        "
                    >
                        Current Password
                    </span>

                    <div class="relative mt-2">

                        <svg
                            viewBox="0 0 24 24"
                            class="
                                pointer-events-none
                                absolute left-3.5 top-1/2
                                h-3.5 w-3.5
                                -translate-y-1/2
                                text-[#a09687]
                            "
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <rect
                                x="5"
                                y="10"
                                width="14"
                                height="10"
                                rx="2"
                            ></rect>

                            <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                        </svg>

                        <input
                            name="current_password"
                            type="password"
                            autocomplete="current-password"
                            placeholder="Enter current password"
                            class="
                                profile-field
                                h-11 w-full
                                rounded-xl
                                border border-[#e6dccb]
                                bg-white
                                pl-9 pr-3.5
                                text-[10px]
                                text-[#413a31]
                                placeholder:text-[#aaa093]
                            "
                        >

                    </div>

                </label>


                {{-- NEW PASSWORD --}}
                <label class="block">

                    <span
                        class="
                            text-[9px] font-semibold
                            text-[#51483d]
                        "
                    >
                        New Password
                    </span>

                    <input
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        placeholder="Minimum 8 characters"
                        class="
                            profile-field
                            mt-2 h-11 w-full
                            rounded-xl
                            border border-[#e6dccb]
                            bg-white
                            px-3.5
                            text-[10px]
                            text-[#413a31]
                            placeholder:text-[#aaa093]
                        "
                    >

                </label>


                {{-- CONFIRM PASSWORD --}}
                <label class="block">

                    <span
                        class="
                            text-[9px] font-semibold
                            text-[#51483d]
                        "
                    >
                        Confirm Password
                    </span>

                    <input
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        placeholder="Repeat new password"
                        class="
                            profile-field
                            mt-2 h-11 w-full
                            rounded-xl
                            border border-[#e6dccb]
                            bg-white
                            px-3.5
                            text-[10px]
                            text-[#413a31]
                            placeholder:text-[#aaa093]
                        "
                    >

                </label>

            </div>


            {{-- =================================================
                 SAVE AREA
            ================================================== --}}
            <div
                class="
                    flex flex-col gap-3
                    border-t border-[#eee4d3]
                    bg-[#fbf8f2]
                    px-5 py-4
                    sm:flex-row
                    sm:items-center
                    sm:justify-between
                "
            >

                <p
                    class="
                        max-w-xl
                        text-[8px] leading-4
                        text-[#918677]
                    "
                >
                    Changes to your profile will update the rider
                    information associated with your account.
                </p>


                <button
                    type="submit"
                    class="
                        inline-flex min-h-11
                        items-center justify-center gap-2
                        rounded-xl
                        bg-[#d9930a]
                        px-5 py-3
                        text-[10px] font-semibold
                        text-white
                        shadow-[0_8px_18px_rgba(217,147,10,.15)]
                        transition
                        hover:bg-[#c98505]
                        active:scale-[.98]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M5 4h14v16H5z"></path>
                        <path d="M8 4v6h8V4"></path>
                        <path d="M8 20v-6h8v6"></path>
                    </svg>

                    Save Rider Profile
                </button>

            </div>

        </section>

    </form>


    {{-- =========================================================
         RIDER SUPPORT / REPORT AN ISSUE
    ========================================================== --}}
    <section
        class="
            reveal profile-card
            mt-5 overflow-hidden
            rounded-[20px]
        "
    >

        {{-- =====================================================
             HEADER
        ====================================================== --}}
        <div
            class="
                flex flex-col gap-4
                border-b border-[#eee4d3]
                px-5 py-4
                sm:flex-row
                sm:items-center
                sm:justify-between
            "
        >

            <div class="flex items-center gap-3">

                <div
                    class="
                        grid h-9 w-9
                        shrink-0 place-items-center
                        rounded-xl
                        bg-[#fff2d8]
                        text-[#b77900]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M12 3 3.5 19h17L12 3Z"></path>
                        <path d="M12 9v4"></path>
                        <path d="M12 16h.01"></path>
                    </svg>
                </div>


                <div>

                    <h3
                        class="
                            text-[13px] font-bold
                            text-[#211d17]
                        "
                    >
                        Report an Issue
                    </h3>


                    <p
                        class="
                            mt-1
                            text-[9px]
                            text-[#918677]
                        "
                    >
                        Send a platform concern directly to the SARI Administrator.
                    </p>

                </div>

            </div>


            <span
                class="
                    inline-flex self-start
                    items-center gap-2
                    rounded-full
                    border border-[#eadfc9]
                    bg-[#fffaf1]
                    px-3 py-1.5
                    text-[8px] font-semibold
                    text-[#817769]
                    sm:self-auto
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-3 w-3 text-[#a66d08]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        d="
                            M21 14
                            a4 4 0 0 1-4 4
                            H8
                            l-5 3
                            V7
                            a4 4 0 0 1 4-4
                            h10
                            a4 4 0 0 1 4 4
                            v7Z
                        "
                    ></path>
                </svg>

                Platform Support
            </span>

        </div>


        <div
            class="
                grid gap-5
                p-5
                lg:grid-cols-[minmax(0,1fr)_300px]
            "
        >

            {{-- =================================================
                 COMPLAINT FORM
            ================================================== --}}
            <form
                method="POST"
                action="{{ route('platform.complaints.store') }}"
                class="space-y-4"
            >
                @csrf


                {{-- SUBJECT --}}
                <label class="block">

                    <span
                        class="
                            text-[9px] font-semibold
                            text-[#51483d]
                        "
                    >
                        Issue Subject
                    </span>


                    <p
                        class="
                            mt-1
                            text-[8px]
                            text-[#9a9082]
                        "
                    >
                        Give your concern a short and clear title.
                    </p>


                    <input
                        name="subject"
                        required
                        maxlength="150"
                        value="{{ old('subject') }}"
                        placeholder="Example: Issue with assigned delivery"
                        class="
                            profile-field
                            mt-2 h-11 w-full
                            rounded-xl
                            border border-[#e6dccb]
                            bg-white
                            px-3.5
                            text-[10px]
                            text-[#413a31]
                            placeholder:text-[#aaa093]
                        "
                    >

                </label>


                {{-- DESCRIPTION --}}
                <label class="block">

                    <div
                        class="
                            flex items-end
                            justify-between gap-3
                        "
                    >

                        <div>

                            <span
                                class="
                                    text-[9px] font-semibold
                                    text-[#51483d]
                                "
                            >
                                Description
                            </span>


                            <p
                                class="
                                    mt-1
                                    text-[8px]
                                    text-[#9a9082]
                                "
                            >
                                Explain what happened and include useful details.
                            </p>

                        </div>


                        <span
                            class="
                                shrink-0
                                text-[8px]
                                text-[#aaa093]
                            "
                        >
                            Max. 3000
                        </span>

                    </div>


                    <textarea
                        name="description"
                        required
                        minlength="10"
                        maxlength="3000"
                        rows="5"
                        placeholder="Describe what happened..."
                        class="
                            profile-field
                            mt-2 w-full
                            resize-y
                            rounded-xl
                            border border-[#e6dccb]
                            bg-white
                            px-3.5 py-3
                            text-[10px]
                            leading-5
                            text-[#413a31]
                            placeholder:text-[#aaa093]
                        "
                    >{{ old('description') }}</textarea>

                </label>


                {{-- ACTION --}}
                <div
                    class="
                        flex flex-col gap-3
                        border-t border-[#eee4d3]
                        pt-4
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                    "
                >

                    <p
                        class="
                            max-w-lg
                            text-[8px] leading-4
                            text-[#918677]
                        "
                    >
                        Your report will be submitted to the platform
                        Administrator for review.
                    </p>


                    <button
                        type="submit"
                        class="
                            inline-flex min-h-11
                            items-center justify-center gap-2
                            rounded-xl
                            bg-[#d9930a]
                            px-5 py-3
                            text-[10px] font-semibold
                            text-white
                            shadow-[0_8px_18px_rgba(217,147,10,.15)]
                            transition
                            hover:bg-[#c98505]
                            active:scale-[.98]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-3.5 w-3.5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M12 3 3.5 19h17L12 3Z"></path>
                            <path d="M12 9v4"></path>
                            <path d="M12 16h.01"></path>
                        </svg>

                        Submit Report
                    </button>

                </div>

            </form>


            {{-- =================================================
                 SUPPORT INFO
            ================================================== --}}
            <aside
                class="
                    rounded-2xl
                    border border-[#eee4d3]
                    bg-[#fbf8f2]
                    p-4
                "
            >

                <div
                    class="
                        grid h-10 w-10
                        place-items-center
                        rounded-xl
                        bg-[#fff2d8]
                        text-[#b77900]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[18px] w-[18px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 10v6"></path>
                        <path d="M12 7h.01"></path>
                    </svg>
                </div>


                <h4
                    class="
                        mt-3
                        text-[11px] font-bold
                        text-[#413a31]
                    "
                >
                    Before submitting
                </h4>


                <p
                    class="
                        mt-1.5
                        text-[9px] leading-5
                        text-[#817769]
                    "
                >
                    Include enough information so the Administrator
                    can understand and review your concern.
                </p>


                <div class="mt-4 space-y-3">

                    <div class="flex items-start gap-2.5">

                        <span
                            class="
                                mt-0.5
                                grid h-5 w-5
                                shrink-0 place-items-center
                                rounded-full
                                bg-[#fff2d8]
                                text-[8px] font-bold
                                text-[#a66d08]
                            "
                        >
                            1
                        </span>


                        <p
                            class="
                                text-[8px] leading-4
                                text-[#817769]
                            "
                        >
                            Use a specific subject instead of a vague title.
                        </p>

                    </div>


                    <div class="flex items-start gap-2.5">

                        <span
                            class="
                                mt-0.5
                                grid h-5 w-5
                                shrink-0 place-items-center
                                rounded-full
                                bg-[#fff2d8]
                                text-[8px] font-bold
                                text-[#a66d08]
                            "
                        >
                            2
                        </span>


                        <p
                            class="
                                text-[8px] leading-4
                                text-[#817769]
                            "
                        >
                            Describe the issue clearly and include relevant context.
                        </p>

                    </div>


                    <div class="flex items-start gap-2.5">

                        <span
                            class="
                                mt-0.5
                                grid h-5 w-5
                                shrink-0 place-items-center
                                rounded-full
                                bg-[#fff2d8]
                                text-[8px] font-bold
                                text-[#a66d08]
                            "
                        >
                            3
                        </span>


                        <p
                            class="
                                text-[8px] leading-4
                                text-[#817769]
                            "
                        >
                            For active delivery coordination, use Messages
                            when direct Logistics assistance is more appropriate.
                        </p>

                    </div>

                </div>


                <a
                    href="{{ url('/courier/messages') }}"
                    class="
                        mt-5
                        inline-flex min-h-10 w-full
                        items-center justify-center gap-2
                        rounded-xl
                        border border-[#e6dccb]
                        bg-white
                        px-4 py-2.5
                        text-[9px] font-semibold
                        text-[#51483d]
                        transition
                        hover:border-[#d9be8c]
                        hover:bg-[#fffaf1]
                        hover:text-[#a66d08]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            d="
                                M21 14
                                a4 4 0 0 1-4 4
                                H8
                                l-5 3
                                V7
                                a4 4 0 0 1 4-4
                                h10
                                a4 4 0 0 1 4 4
                                v7Z
                            "
                        ></path>
                    </svg>

                    Open Messages
                </a>

            </aside>

        </div>

    </section>

</div>
@endsection