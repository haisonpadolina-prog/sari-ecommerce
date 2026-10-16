@extends('layouts.logistics')

@section('title', 'Rider Applications | SARI Logistics')
@section('page-title', 'Rider Applications')

@section('content')

<div
    class="
        mx-auto w-full
        max-w-[1800px]
        space-y-4
    "
>

    {{-- =====================================================
        FLASH MESSAGES
    ====================================================== --}}

    @if(session('success'))
        <div
            class="
                rounded-[16px]
                border border-[#bfe0cd]
                bg-[#f1faf5]
                px-5 py-4
                text-[9px]
                font-medium
                text-[#327a53]
            "
        >
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div
            class="
                rounded-[16px]
                border border-[#efc6c6]
                bg-[#fff4f4]
                px-5 py-4
                text-[9px]
                font-medium
                text-[#b53e3e]
            "
        >
            {{ $errors->first() }}
        </div>
    @endif


    {{-- =====================================================
        HERO
    ====================================================== --}}

    <section
        class="
            relative overflow-hidden
            rounded-[20px]
            border border-[#e8dfd1]
            bg-white
            shadow-[0_5px_22px_rgba(51,39,25,.035)]
        "
    >
        <div
            class="
                pointer-events-none
                absolute -right-[90px] -top-[120px]
                h-[270px] w-[270px]
                rounded-full
                bg-[#f4d58b]/20
                blur-2xl
            "
        ></div>

        <div
            class="
                relative
                flex flex-col gap-4
                px-5 py-5
                sm:px-6
                xl:flex-row
                xl:items-center
                xl:justify-between
            "
        >

            <div class="flex min-w-0 items-start gap-3.5">

                <div
                    class="
                        grid h-[42px] w-[42px]
                        shrink-0 place-items-center
                        rounded-[13px]
                        bg-[#fff6e5]
                        text-[#b97805]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[20px] w-[20px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="9" cy="8" r="3"></circle>
                        <path d="M3 20c.6-4 2.8-6 6-6"></path>
                        <path d="M17 8h4"></path>
                        <path d="M19 6v4"></path>
                    </svg>
                </div>

                <div class="min-w-0">

                    <p
                        class="
                            text-[7.5px]
                            font-bold
                            uppercase
                            tracking-[.14em]
                            text-[#b97805]
                        "
                    >
                        Rider Recruitment
                    </p>

                    <h2
                        class="
                            mt-1
                            text-[20px]
                            font-bold
                            tracking-[-.035em]
                            text-[#211d17]
                        "
                    >
                        Review Rider applications
                    </h2>

                    <p
                        class="
                            mt-1
                            max-w-[760px]
                            text-[8.5px]
                            leading-[18px]
                            text-[#807668]
                        "
                    >
                        Review applicants who selected
                        <strong class="font-semibold text-[#51483d]">
                            {{ $logistics->displayName() }}
                        </strong>
                        as their Logistics provider. Verify their service area,
                        vehicle, and uploaded documents before approving access.
                    </p>

                </div>
            </div>


            {{-- Current Filter --}}
            <div
                class="
                    rounded-[12px]
                    border border-[#e7dece]
                    bg-[#fffdf9]
                    px-4 py-3
                "
            >
                <p
                    class="
                        text-[6.5px]
                        font-bold
                        uppercase
                        tracking-[.1em]
                        text-[#9a8f80]
                    "
                >
                    Viewing
                </p>

                <div
                    class="
                        mt-1
                        flex items-center gap-2
                    "
                >
                    <span
                        class="
                            h-2 w-2
                            rounded-full

                            {{
                                $status === 'approved'
                                    ? 'bg-[#4ca36e]'
                                    : (
                                        $status === 'rejected'
                                            ? 'bg-[#c96666]'
                                            : 'bg-[#d9930a]'
                                    )
                            }}
                        "
                    ></span>

                    <p
                        class="
                            text-[11px]
                            font-semibold
                            capitalize
                            text-[#342d25]
                        "
                    >
                        {{ $status }}
                    </p>
                </div>
            </div>

        </div>
    </section>


    {{-- =====================================================
        STATUS METRICS
    ====================================================== --}}

    <section
        class="
            grid gap-3
            grid-cols-2
            xl:grid-cols-4
        "
    >

        {{-- Total --}}
        <a
            href="{{ route('logistics.rider-applications', ['status' => 'pending']) }}"
            class="
                rounded-[16px]
                border border-[#e8dfd1]
                bg-white
                p-4
                shadow-[0_4px_18px_rgba(51,39,25,.035)]
                transition
                hover:-translate-y-[1px]
                hover:border-[#d8c18e]
            "
        >
            <div class="flex items-start justify-between">

                <div>
                    <p
                        class="
                            text-[7px]
                            font-bold uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Total Applications
                    </p>

                    <p
                        class="
                            mt-1.5
                            text-[23px]
                            font-bold
                            tracking-[-.04em]
                            text-[#272119]
                        "
                    >
                        {{ $stats['total'] }}
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[7.5px]
                            text-[#918779]
                        "
                    >
                        All Rider submissions
                    </p>
                </div>

                <span
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-[11px]
                        bg-[#f4f1ed]
                        text-[#756b5f]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[17px] w-[17px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="8" cy="8" r="3"></circle>
                        <circle cx="17" cy="9" r="2.5"></circle>
                        <path d="M2.5 20c.5-4 2.5-6 5.5-6"></path>
                        <path d="M13 15c3.2 0 5.2 1.7 5.7 5"></path>
                    </svg>
                </span>

            </div>
        </a>


        {{-- Pending --}}
        <a
            href="{{ route('logistics.rider-applications', ['status' => 'pending']) }}"
            class="
                rounded-[16px]
                border
                bg-white
                p-4
                shadow-[0_4px_18px_rgba(51,39,25,.035)]
                transition
                hover:-translate-y-[1px]

                {{
                    $status === 'pending'
                        ? 'border-[#dfbd78] ring-2 ring-[#d9930a]/10'
                        : 'border-[#e8dfd1] hover:border-[#d8c18e]'
                }}
            "
        >
            <div class="flex items-start justify-between">

                <div>
                    <p
                        class="
                            text-[7px]
                            font-bold uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Pending Review
                    </p>

                    <p
                        class="
                            mt-1.5
                            text-[23px]
                            font-bold
                            tracking-[-.04em]
                            text-[#272119]
                        "
                    >
                        {{ $stats['pending'] }}
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[7.5px]
                            text-[#918779]
                        "
                    >
                        Waiting for decision
                    </p>
                </div>

                <span
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-[11px]
                        bg-[#fff6e5]
                        text-[#b97805]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[17px] w-[17px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 7v5l3 2"></path>
                    </svg>
                </span>

            </div>
        </a>


        {{-- Approved --}}
        <a
            href="{{ route('logistics.rider-applications', ['status' => 'approved']) }}"
            class="
                rounded-[16px]
                border
                bg-white
                p-4
                shadow-[0_4px_18px_rgba(51,39,25,.035)]
                transition
                hover:-translate-y-[1px]

                {{
                    $status === 'approved'
                        ? 'border-[#9fc8ae] ring-2 ring-[#4ca36e]/10'
                        : 'border-[#e8dfd1] hover:border-[#bcd7c5]'
                }}
            "
        >
            <div class="flex items-start justify-between">

                <div>
                    <p
                        class="
                            text-[7px]
                            font-bold uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Approved
                    </p>

                    <p
                        class="
                            mt-1.5
                            text-[23px]
                            font-bold
                            tracking-[-.04em]
                            text-[#272119]
                        "
                    >
                        {{ $stats['approved'] }}
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[7.5px]
                            text-[#918779]
                        "
                    >
                        Rider accounts created
                    </p>
                </div>

                <span
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-[11px]
                        bg-[#edf8f1]
                        text-[#327a53]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[17px] w-[17px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="m8 12 2.5 2.5L16 9"></path>
                    </svg>
                </span>

            </div>
        </a>


        {{-- Rejected --}}
        <a
            href="{{ route('logistics.rider-applications', ['status' => 'rejected']) }}"
            class="
                rounded-[16px]
                border
                bg-white
                p-4
                shadow-[0_4px_18px_rgba(51,39,25,.035)]
                transition
                hover:-translate-y-[1px]

                {{
                    $status === 'rejected'
                        ? 'border-[#dda9a9] ring-2 ring-[#c96666]/10'
                        : 'border-[#e8dfd1] hover:border-[#e0c1c1]'
                }}
            "
        >
            <div class="flex items-start justify-between">

                <div>
                    <p
                        class="
                            text-[7px]
                            font-bold uppercase
                            tracking-[.11em]
                            text-[#978d7f]
                        "
                    >
                        Rejected
                    </p>

                    <p
                        class="
                            mt-1.5
                            text-[23px]
                            font-bold
                            tracking-[-.04em]
                            text-[#272119]
                        "
                    >
                        {{ $stats['rejected'] }}
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[7.5px]
                            text-[#918779]
                        "
                    >
                        Applications declined
                    </p>
                </div>

                <span
                    class="
                        grid h-9 w-9
                        place-items-center
                        rounded-[11px]
                        bg-[#fff1f1]
                        text-[#a65d5d]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[17px] w-[17px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="m9 9 6 6"></path>
                        <path d="m15 9-6 6"></path>
                    </svg>
                </span>

            </div>
        </a>

    </section>


    {{-- =====================================================
        LIST TOOLBAR
    ====================================================== --}}

    <section
        class="
            rounded-[20px]
            border border-[#e8dfd1]
            bg-white
            shadow-[0_4px_20px_rgba(51,39,25,.035)]
        "
    >

        <div
            class="
                flex flex-col gap-3
                border-b border-[#eee5d8]
                px-5 py-4
                xl:flex-row
                xl:items-center
                xl:justify-between
            "
        >

            <div>
                <p
                    class="
                        text-[7.5px]
                        font-bold uppercase
                        tracking-[.12em]
                        text-[#b97805]
                    "
                >
                    Application Queue
                </p>

                <h3
                    class="
                        mt-1
                        text-[13px]
                        font-bold
                        capitalize
                        text-[#2e2821]
                    "
                >
                    {{ $status }} Rider applications
                </h3>

                <p
                    class="
                        mt-0.5
                        text-[8px]
                        text-[#918779]
                    "
                >
                    {{ $applications->count() }}
                    {{ $applications->count() === 1 ? 'application' : 'applications' }}
                    in this view
                </p>
            </div>


            <div
                class="
                    flex flex-col gap-2
                    sm:flex-row
                    sm:items-center
                "
            >

                {{-- Search --}}
                <div class="relative">

                    <svg
                        viewBox="0 0 24 24"
                        class="
                            pointer-events-none
                            absolute left-3 top-1/2
                            h-[14px] w-[14px]
                            -translate-y-1/2
                            text-[#aaa094]
                        "
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-4-4"></path>
                    </svg>

                    <input
                        id="riderApplicationSearch"
                        type="search"
                        placeholder="Search applicant..."
                        class="
                            h-[36px]
                            w-full
                            rounded-[10px]
                            border border-[#e5dccf]
                            bg-white
                            pl-9 pr-3
                            text-[8px]
                            outline-none
                            placeholder:text-[#aaa094]
                            focus:border-[#d9a03d]
                            focus:ring-4
                            focus:ring-[#d9a03d]/10
                            sm:w-[220px]
                        "
                    >

                </div>


                {{-- Status dropdown --}}
                <form
                    method="GET"
                    class="flex items-center gap-2"
                >
                    <select
                        name="status"
                        class="
                            h-[36px]
                            rounded-[10px]
                            border border-[#e5dccf]
                            bg-white
                            px-3
                            text-[8px]
                            font-medium
                            text-[#51483d]
                            outline-none
                            focus:border-[#d9a03d]
                        "
                    >
                        <option
                            value="pending"
                            @selected($status === 'pending')
                        >
                            Pending
                        </option>

                        <option
                            value="approved"
                            @selected($status === 'approved')
                        >
                            Approved
                        </option>

                        <option
                            value="rejected"
                            @selected($status === 'rejected')
                        >
                            Rejected
                        </option>
                    </select>

                    <button
                        type="submit"
                        class="
                            h-[36px]
                            rounded-[10px]
                            bg-[#d9930a]
                            px-4
                            text-[8px]
                            font-semibold
                            text-white
                            transition
                            hover:bg-[#c48608]
                        "
                    >
                        Apply
                    </button>
                </form>

            </div>

        </div>


        {{-- =================================================
            APPLICATION CARDS
        ================================================== --}}

        <div
            id="riderApplicationGrid"
            class="
                grid gap-3
                p-4
                2xl:grid-cols-2
            "
        >

            @forelse($applications as $application)

                @php
                    $searchText =
                        strtolower(
                            implode(
                                ' ',
                                [
                                    $application->fullName(),
                                    $application->email,
                                    $application->contact_no,
                                    $application->vehicle_type,
                                    $application->plate_number,
                                    $application->province_name,
                                    $application->municipality_name,
                                    $application->barangay_name,
                                    $application->street_address,
                                ]
                            )
                        );

                    $initials =
                        strtoupper(
                            mb_substr(
                                $application->first_name ?: '',
                                0,
                                1
                            )
                            .
                            mb_substr(
                                $application->last_name ?: '',
                                0,
                                1
                            )
                        );

                    $statusClasses =
                        match($application->status) {
                            'approved' =>
                                'border-[#cfe1d5] bg-[#f2f8f4] text-[#4f7c60]',

                            'rejected' =>
                                'border-[#e8cccc] bg-[#fff3f3] text-[#a55a5a]',

                            default =>
                                'border-[#eadfc9] bg-[#fffaf2] text-[#a8731f]',
                        };
                @endphp

                <article
                    data-rider-application
                    data-search="{{ $searchText }}"
                    class="
                        overflow-hidden
                        rounded-[17px]
                        border border-[#e9e0d3]
                        bg-[#fffdf9]
                        transition
                        hover:border-[#ddccb0]
                        hover:shadow-[0_7px_22px_rgba(51,39,25,.045)]
                    "
                >

                    {{-- =====================================
                        Applicant header
                    ====================================== --}}

                    <div
                        class="
                            flex flex-col gap-3
                            border-b border-[#eee6da]
                            bg-white
                            px-4 py-4
                            sm:flex-row
                            sm:items-start
                            sm:justify-between
                        "
                    >

                        <div
                            class="
                                flex min-w-0
                                items-center gap-3
                            "
                        >

                            @if($application->profile_image_path)

                                <img
                                    src="{{ asset(
                                        'storage/'
                                        . $application->profile_image_path
                                    ) }}"
                                    alt="{{ $application->fullName() }}"
                                    class="
                                        h-11 w-11
                                        shrink-0
                                        rounded-[12px]
                                        border border-[#e5ded4]
                                        object-cover
                                    "
                                >

                            @else

                                <div
                                    class="
                                        grid h-11 w-11
                                        shrink-0
                                        place-items-center
                                        rounded-[12px]
                                        bg-[#fff3da]
                                        text-[9px]
                                        font-bold
                                        text-[#aa7209]
                                    "
                                >
                                    {{ $initials ?: 'R' }}
                                </div>

                            @endif


                            <div class="min-w-0">

                                <h3
                                    class="
                                        truncate
                                        text-[11px]
                                        font-bold
                                        text-[#312b25]
                                    "
                                >
                                    {{ $application->fullName() }}
                                </h3>

                                <p
                                    class="
                                        mt-1
                                        truncate
                                        text-[7px]
                                        text-[#81786c]
                                    "
                                >
                                    {{ $application->email }}
                                </p>

                                <p
                                    class="
                                        mt-0.5
                                        text-[6.5px]
                                        text-[#9b9389]
                                    "
                                >
                                    {{ $application->contact_no ?: 'No contact number' }}
                                </p>

                            </div>

                        </div>


                        <div
                            class="
                                flex items-center
                                gap-2
                                sm:flex-col
                                sm:items-end
                            "
                        >

                            <span
                                class="
                                    inline-flex
                                    rounded-full
                                    border
                                    px-2.5 py-1
                                    text-[6.5px]
                                    font-bold
                                    uppercase
                                    {{ $statusClasses }}
                                "
                            >
                                {{ $application->status }}
                            </span>

                            <span
                                class="
                                    text-[6.5px]
                                    text-[#9b9389]
                                "
                            >
                                {{ $application->created_at?->format('M d, Y · h:i A') }}
                            </span>

                        </div>

                    </div>


                    {{-- =====================================
                        Applicant information
                    ====================================== --}}

                    <div class="p-4">

                        <div
                            class="
                                grid gap-2
                                sm:grid-cols-2
                            "
                        >

                            {{-- Service Area --}}
                            <div
                                class="
                                    rounded-[12px]
                                    border border-[#eee6da]
                                    bg-white
                                    p-3
                                "
                            >
                                <div
                                    class="
                                        flex items-start gap-2.5
                                    "
                                >

                                    <span
                                        class="
                                            grid h-7 w-7
                                            shrink-0 place-items-center
                                            rounded-[8px]
                                            bg-[#f6f2eb]
                                            text-[#756b5f]
                                        "
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            class="h-[14px] w-[14px]"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <path d="M12 21s6-5.1 6-11a6 6 0 1 0-12 0c0 5.9 6 11 6 11Z"></path>
                                            <circle cx="12" cy="10" r="2"></circle>
                                        </svg>
                                    </span>

                                    <div>

                                        <p
                                            class="
                                                text-[6.5px]
                                                font-bold uppercase
                                                tracking-[.08em]
                                                text-[#958c80]
                                            "
                                        >
                                            Service Area
                                        </p>

                                        <p
                                            class="
                                                mt-1
                                                text-[8px]
                                                font-semibold
                                                text-[#403a33]
                                            "
                                        >
                                            {{ $application->barangay_name ?: '—' }}
                                        </p>

                                        <p
                                            class="
                                                mt-0.5
                                                text-[6.5px]
                                                leading-[13px]
                                                text-[#918779]
                                            "
                                        >
                                            {{ $application->municipality_name ?: '—' }}

                                            @if($application->province_name)
                                                · {{ $application->province_name }}
                                            @endif
                                        </p>

                                    </div>

                                </div>
                            </div>


                            {{-- Vehicle --}}
                            <div
                                class="
                                    rounded-[12px]
                                    border border-[#eee6da]
                                    bg-white
                                    p-3
                                "
                            >
                                <div
                                    class="
                                        flex items-start gap-2.5
                                    "
                                >

                                    <span
                                        class="
                                            grid h-7 w-7
                                            shrink-0 place-items-center
                                            rounded-[8px]
                                            bg-[#fff6e5]
                                            text-[#b97805]
                                        "
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            class="h-[14px] w-[14px]"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <circle cx="6" cy="17" r="2"></circle>
                                            <circle cx="18" cy="17" r="2"></circle>
                                            <path d="M8 17h8"></path>
                                            <path d="M6 15 8 9h7l3 6"></path>
                                        </svg>
                                    </span>

                                    <div>

                                        <p
                                            class="
                                                text-[6.5px]
                                                font-bold uppercase
                                                tracking-[.08em]
                                                text-[#958c80]
                                            "
                                        >
                                            Vehicle
                                        </p>

                                        <p
                                            class="
                                                mt-1
                                                text-[8px]
                                                font-semibold
                                                text-[#403a33]
                                            "
                                        >
                                            {{ $application->vehicle_type ?: '—' }}
                                        </p>

                                        <p
                                            class="
                                                mt-0.5
                                                text-[6.5px]
                                                text-[#918779]
                                            "
                                        >
                                            Plate:
                                            {{ $application->plate_number ?: 'Not provided' }}
                                        </p>

                                    </div>

                                </div>
                            </div>

                        </div>


                        {{-- Full address --}}
                        <div
                            class="
                                mt-2
                                rounded-[12px]
                                border border-[#eee6da]
                                bg-white
                                p-3
                            "
                        >
                            <p
                                class="
                                    text-[6.5px]
                                    font-bold uppercase
                                    tracking-[.08em]
                                    text-[#958c80]
                                "
                            >
                                Residential Address
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-[7.5px]
                                    leading-[15px]
                                    text-[#61584d]
                                "
                            >
                                {{ $application->street_address ?: 'No street address' }}

                                @if($application->barangay_name)
                                    , {{ $application->barangay_name }}
                                @endif

                                @if($application->municipality_name)
                                    , {{ $application->municipality_name }}
                                @endif

                                @if($application->province_name)
                                    , {{ $application->province_name }}
                                @endif
                            </p>
                        </div>


                        {{-- Documents --}}
                        <div
                            class="
                                mt-3
                                flex flex-wrap
                                items-center gap-2
                            "
                        >

                            <p
                                class="
                                    mr-1
                                    text-[6.5px]
                                    font-bold uppercase
                                    tracking-[.08em]
                                    text-[#958c80]
                                "
                            >
                                Documents
                            </p>

                            @if($application->id_path)

                                <a
                                    href="{{ route(
                                        'logistics.rider-applications.document',
                                        [
                                            $application,
                                            'id'
                                        ]
                                    ) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="
                                        inline-flex h-8
                                        items-center gap-1.5
                                        rounded-[8px]
                                        border border-[#ddd7ce]
                                        bg-white
                                        px-3
                                        text-[7px]
                                        font-semibold
                                        text-[#62594e]
                                        transition
                                        hover:bg-[#faf8f4]
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-[12px] w-[12px]"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                        <circle cx="9" cy="11" r="2"></circle>
                                        <path d="M5.5 16c.5-2 1.7-3 3.5-3s3 1 3.5 3"></path>
                                        <path d="M15 10h3"></path>
                                        <path d="M15 14h3"></path>
                                    </svg>

                                    ID / License
                                </a>

                            @else

                                <span
                                    class="
                                        inline-flex h-8
                                        items-center
                                        rounded-[8px]
                                        border border-dashed
                                        border-[#ddd7ce]
                                        px-3
                                        text-[7px]
                                        text-[#aaa094]
                                    "
                                >
                                    No ID
                                </span>

                            @endif


                            @if($application->orcr_path)

                                <a
                                    href="{{ route(
                                        'logistics.rider-applications.document',
                                        [
                                            $application,
                                            'orcr'
                                        ]
                                    ) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="
                                        inline-flex h-8
                                        items-center gap-1.5
                                        rounded-[8px]
                                        border border-[#d9e2ea]
                                        bg-[#f6f9fb]
                                        px-3
                                        text-[7px]
                                        font-semibold
                                        text-[#57758c]
                                        transition
                                        hover:bg-[#eef5fa]
                                    "
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-[12px] w-[12px]"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M6 2h9l4 4v16H6z"></path>
                                        <path d="M14 2v5h5"></path>
                                        <path d="M9 12h6"></path>
                                        <path d="M9 16h6"></path>
                                    </svg>

                                    OR / CR
                                </a>

                            @else

                                <span
                                    class="
                                        inline-flex h-8
                                        items-center
                                        rounded-[8px]
                                        border border-dashed
                                        border-[#ddd7ce]
                                        px-3
                                        text-[7px]
                                        text-[#aaa094]
                                    "
                                >
                                    No OR / CR
                                </span>

                            @endif

                        </div>


                        {{-- Existing review note --}}
                        @if($application->admin_note)

                            <div
                                class="
                                    mt-3
                                    rounded-[11px]
                                    border border-[#eee7de]
                                    bg-[#fcfaf7]
                                    px-3 py-2.5
                                "
                            >
                                <p
                                    class="
                                        text-[6.5px]
                                        font-bold uppercase
                                        tracking-[.08em]
                                        text-[#958c80]
                                    "
                                >
                                    Review Note
                                </p>

                                <p
                                    class="
                                        mt-1
                                        text-[7px]
                                        leading-[14px]
                                        text-[#756d63]
                                    "
                                >
                                    {{ $application->admin_note }}
                                </p>
                            </div>

                        @endif


                        {{-- =================================
                            REVIEW ACTIONS
                        ================================== --}}

                        @if($application->status === 'pending')

                            <div
                                class="
                                    mt-4
                                    grid gap-3
                                    border-t border-[#eee8df]
                                    pt-4
                                    xl:grid-cols-2
                                "
                            >

                                {{-- Approve --}}
                                <form
                                    method="POST"
                                    action="{{ route(
                                        'logistics.rider-applications.approve',
                                        $application
                                    ) }}"
                                    class="
                                        rounded-[12px]
                                        border border-[#dce7df]
                                        bg-[#f8fbf9]
                                        p-3
                                    "
                                >
                                    @csrf

                                    <div
                                        class="
                                            flex items-center gap-2
                                        "
                                    >
                                        <span
                                            class="
                                                grid h-7 w-7
                                                place-items-center
                                                rounded-[8px]
                                                bg-[#e9f5ee]
                                                text-[#56816a]
                                            "
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                class="h-[13px] w-[13px]"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path d="m7 12 3 3 7-7"></path>
                                            </svg>
                                        </span>

                                        <div>
                                            <p
                                                class="
                                                    text-[7.5px]
                                                    font-bold
                                                    text-[#52715d]
                                                "
                                            >
                                                Approve Rider
                                            </p>

                                            <p
                                                class="
                                                    text-[6.5px]
                                                    text-[#84958a]
                                                "
                                            >
                                                Creates an active Rider account
                                            </p>
                                        </div>
                                    </div>

                                    <input
                                        type="text"
                                        name="review_note"
                                        maxlength="1500"
                                        placeholder="Optional approval note..."
                                        class="
                                            mt-3 h-9 w-full
                                            rounded-[9px]
                                            border border-[#dce4de]
                                            bg-white
                                            px-3
                                            text-[7.5px]
                                            text-[#403a33]
                                            outline-none
                                            placeholder:text-[#aaa49c]
                                            focus:border-[#7fa58e]
                                        "
                                    >

                                    <button
                                        type="submit"
                                        class="
                                            mt-2
                                            h-9 w-full
                                            rounded-[9px]
                                            bg-[#56816a]
                                            text-[7.5px]
                                            font-bold
                                            text-white
                                            transition
                                            hover:bg-[#496f5b]
                                        "
                                    >
                                        Approve Application
                                    </button>

                                </form>


                                {{-- Reject --}}
                                <form
                                    method="POST"
                                    action="{{ route(
                                        'logistics.rider-applications.reject',
                                        $application
                                    ) }}"
                                    class="
                                        rounded-[12px]
                                        border border-[#eadada]
                                        bg-[#fffafa]
                                        p-3
                                    "
                                >
                                    @csrf

                                    <div
                                        class="
                                            flex items-center gap-2
                                        "
                                    >
                                        <span
                                            class="
                                                grid h-7 w-7
                                                place-items-center
                                                rounded-[8px]
                                                bg-[#fff0f0]
                                                text-[#a85c54]
                                            "
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                class="h-[13px] w-[13px]"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path d="m8 8 8 8"></path>
                                                <path d="m16 8-8 8"></path>
                                            </svg>
                                        </span>

                                        <div>
                                            <p
                                                class="
                                                    text-[7.5px]
                                                    font-bold
                                                    text-[#8e5d5d]
                                                "
                                            >
                                                Reject Rider
                                            </p>

                                            <p
                                                class="
                                                    text-[6.5px]
                                                    text-[#a58b8b]
                                                "
                                            >
                                                Rejection reason is required
                                            </p>
                                        </div>
                                    </div>

                                    <input
                                        type="text"
                                        name="review_note"
                                        required
                                        minlength="5"
                                        maxlength="1500"
                                        placeholder="Reason for rejection..."
                                        class="
                                            mt-3 h-9 w-full
                                            rounded-[9px]
                                            border border-[#ead9d9]
                                            bg-white
                                            px-3
                                            text-[7.5px]
                                            text-[#403a33]
                                            outline-none
                                            placeholder:text-[#b4a2a2]
                                            focus:border-[#c98680]
                                        "
                                    >

                                    <button
                                        type="submit"
                                        class="
                                            mt-2
                                            h-9 w-full
                                            rounded-[9px]
                                            bg-[#a85c54]
                                            text-[7.5px]
                                            font-bold
                                            text-white
                                            transition
                                            hover:bg-[#934f48]
                                        "
                                    >
                                        Reject Application
                                    </button>

                                </form>

                            </div>

                        @else

                            {{-- Already reviewed --}}
                            <div
                                class="
                                    mt-4
                                    flex items-center
                                    justify-between gap-3
                                    border-t border-[#eee8df]
                                    pt-3
                                "
                            >
                                <div>

                                    <p
                                        class="
                                            text-[6.5px]
                                            font-bold uppercase
                                            tracking-[.08em]
                                            text-[#958c80]
                                        "
                                    >
                                        Review Completed
                                    </p>

                                    <p
                                        class="
                                            mt-0.5
                                            text-[7px]
                                            text-[#817769]
                                        "
                                    >
                                        {{ $application->reviewed_at?->format('M d, Y · h:i A') ?: 'Decision recorded' }}
                                    </p>

                                </div>

                                <span
                                    class="
                                        rounded-full
                                        border
                                        px-2.5 py-1
                                        text-[6.5px]
                                        font-bold
                                        uppercase
                                        {{ $statusClasses }}
                                    "
                                >
                                    {{ $application->status }}
                                </span>

                            </div>

                        @endif

                    </div>

                </article>

            @empty

                <div
                    class="
                        rounded-[17px]
                        border border-dashed
                        border-[#ded5c9]
                        bg-[#fffdf9]
                        px-6 py-12
                        text-center
                        2xl:col-span-2
                    "
                >
                    <div
                        class="
                            mx-auto
                            grid h-11 w-11
                            place-items-center
                            rounded-full
                            bg-[#faf5ec]
                            text-[#b97805]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-[19px] w-[19px]"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="9" cy="8" r="3"></circle>
                            <path d="M3 20c.6-4 2.8-6 6-6"></path>
                            <path d="M17 8h4"></path>
                            <path d="M19 6v4"></path>
                        </svg>
                    </div>

                    <p
                        class="
                            mt-3
                            text-[9px]
                            font-semibold
                            text-[#514a42]
                        "
                    >
                        No {{ $status }} Rider applications
                    </p>

                    <p
                        class="
                            mx-auto mt-1
                            max-w-[420px]
                            text-[7.5px]
                            leading-[15px]
                            text-[#8d8479]
                        "
                    >
                        Rider applications submitted to
                        {{ $logistics->displayName() }}
                        will automatically appear here.
                    </p>

                </div>

            @endforelse


            {{-- Search empty state --}}
            <div
                id="riderApplicationSearchEmpty"
                class="
                    hidden
                    rounded-[17px]
                    border border-dashed
                    border-[#ded5c9]
                    bg-[#fffdf9]
                    px-6 py-10
                    text-center
                    2xl:col-span-2
                "
            >
                <p
                    class="
                        text-[9px]
                        font-semibold
                        text-[#514a42]
                    "
                >
                    No matching applicants
                </p>

                <p
                    class="
                        mt-1
                        text-[7.5px]
                        text-[#8d8479]
                    "
                >
                    Try another name, location, vehicle, or plate number.
                </p>
            </div>

        </div>

    </section>

</div>


@push('scripts')

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const searchInput =
            document.getElementById(
                'riderApplicationSearch'
            );

        const cards =
            Array.from(
                document.querySelectorAll(
                    '[data-rider-application]'
                )
            );

        const emptyState =
            document.getElementById(
                'riderApplicationSearchEmpty'
            );


        function normalize(value) {
            return String(value || '')
                .toLowerCase()
                .trim();
        }


        function filterApplications() {

            const keyword =
                normalize(
                    searchInput?.value
                );

            let visibleCount = 0;

            cards.forEach(
                function (card) {

                    const searchable =
                        normalize(
                            card.dataset.search
                        );

                    const visible =
                        ! keyword
                        || searchable.includes(
                            keyword
                        );

                    card.classList.toggle(
                        'hidden',
                        ! visible
                    );

                    if (visible) {
                        visibleCount++;
                    }
                }
            );


            if (
                cards.length > 0
                && emptyState
            ) {
                emptyState.classList.toggle(
                    'hidden',
                    visibleCount !== 0
                );
            }
        }


        searchInput?.addEventListener(
            'input',
            filterApplications
        );

    }
);
</script>

@endpush

@endsection