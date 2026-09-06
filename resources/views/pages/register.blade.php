@extends('layouts.app')

@section('title', 'Register — SARI')

@section('content')

<script src="https://cdn.tailwindcss.com"></script>

<style>
    :root {
        --sari-yellow: #d48f08;
        --sari-yellow-dark: #bd7d05;
        --sari-text: #1f1b16;
        --sari-muted: #81786c;
        --sari-border: #e4ddd3;
    }

    @keyframes sariWizardEnter {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes sariWizardLeave {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-6px);
        }
    }

    .sari-wizard-step.is-entering {
        animation: sariWizardEnter .30s cubic-bezier(.22,1,.36,1) both;
    }

    .sari-wizard-step.is-leaving {
        animation: sariWizardLeave .15s ease both;
    }

    .sari-control {
        color: #1f1b16 !important;
        -webkit-text-fill-color: #1f1b16 !important;
        caret-color: #1f1b16 !important;
        background: #fff !important;
        transition:
            border-color .18s ease,
            box-shadow .18s ease;
    }

    .sari-control::placeholder {
        color: #aaa196 !important;
        -webkit-text-fill-color: #aaa196 !important;
        opacity: 1;
    }

    .sari-control:focus {
        border-color: var(--sari-yellow) !important;
        box-shadow: 0 0 0 4px rgba(212,143,8,.08);
        outline: none;
    }

    .sari-control:-webkit-autofill,
    .sari-control:-webkit-autofill:hover,
    .sari-control:-webkit-autofill:focus {
        -webkit-text-fill-color: #1f1b16 !important;
        box-shadow: 0 0 0 1000px #fff inset !important;
    }

    .sari-location-list {
        max-height: 210px;
        overflow-y: auto;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: #d9d1c7 transparent;
    }

    .sari-location-option {
        transition:
            background-color .16s ease,
            color .16s ease;
    }

    .sari-location-option:hover {
        background: #fffaf1;
        color: #9a6817;
    }

    .sari-register-action {
        transition:
            background-color .18s ease,
            border-color .18s ease,
            color .18s ease,
            box-shadow .18s ease;
    }

    .sari-register-primary {
        border: 1px solid var(--sari-yellow);
        background: var(--sari-yellow);
        color: #fff;
        box-shadow: 0 7px 18px rgba(212,143,8,.13);
    }

    .sari-register-primary:hover {
        border-color: var(--sari-yellow-dark);
        background: var(--sari-yellow-dark);
        box-shadow: 0 8px 20px rgba(189,125,5,.16);
    }

    .sari-register-secondary {
        border: 1px solid #dfd8ce;
        background: #fff;
        color: #625a50;
    }

    .sari-register-secondary:hover {
        border-color: #d6bf95;
        background: #fffaf1;
        color: #9a6817;
    }

    .sari-register-card {
        animation: sariRegisterCardIn .38s ease-out both;
    }

    @keyframes sariRegisterCardIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    [hidden] {
        display: none !important;
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-wizard-step,
        .sari-control,
        .sari-register-action,
        .sari-register-card {
            animation: none !important;
            transition: none !important;
            transform: none !important;
        }
    }
</style>

<div class="relative min-h-screen min-h-[100dvh] overflow-x-hidden bg-[#f7f4ee] font-['Poppins',sans-serif] text-[#17140e]">

    {{-- SAME BACKGROUND PHOTO AS LOGIN --}}
    <div class="fixed inset-0">
        <img
            src="{{ asset('images/login-bg.jpg') }}"
            alt=""
            class="h-full w-full object-cover object-center"
        >
        <div class="absolute inset-0 bg-white/68"></div>
        <div class="absolute inset-0 bg-[#fffaf1]/18"></div>
    </div>

    <div class="relative z-10 mx-auto flex min-h-screen min-h-[100dvh] w-full max-w-[760px] flex-col items-center justify-center px-4 py-7 sm:px-6 sm:py-10">

        {{-- CENTERED BRAND --}}
        <a
            href="{{ route('home') }}"
            class="mb-5 inline-flex flex-col items-center sm:mb-6"
            aria-label="Back to SARI home"
        >
            <img
                src="{{ asset('images/sari-logo.png') }}"
                alt="SARI"
                class="h-auto w-[150px] object-contain brightness-0 sm:w-[175px] lg:w-[190px]"
            >

            <span class="mt-2 text-center text-[8px] font-semibold uppercase tracking-[0.22em] text-[#9a9185] sm:text-[9px]">
                Elevated Everyday
            </span>
        </a>


        {{-- SINGLE REGISTRATION CARD --}}
        <div class="sari-register-card w-full max-w-[590px] overflow-visible rounded-[20px] border border-[#e7e0d7] bg-white shadow-[0_16px_44px_rgba(39,31,21,.08)]">

            {{-- CARD HEADER --}}
            <div class="border-b border-[#eee8df] px-5 pb-4 pt-5 text-center sm:px-7 sm:pb-5 sm:pt-6">
                <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#b97805]">
                    Join SARI
                </p>

                <h1 class="mt-2 text-[25px] font-bold tracking-[-.04em] text-[#1f1b16] sm:text-[30px]">
                    Create your account
                </h1>

                <p class="mx-auto mt-2.5 max-w-[430px] text-[10px] leading-5 text-[#81786c] sm:text-[11px]">
                    Complete one step at a time. We only show the information needed for the role you choose.
                </p>
            </div>


            {{-- COMPACT STEP STATUS --}}
            <div class="border-b border-[#eee8df] bg-[#fcfbf9] px-5 py-3 sm:px-7">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p id="wizardStepEyebrow" class="text-[6.5px] font-bold uppercase tracking-[.11em] text-[#b97805]">
                            Step 1
                        </p>
                        <p id="wizardStepTitle" class="mt-0.5 truncate text-[8px] font-bold text-[#514a42]">
                            Choose account type
                        </p>
                    </div>

                    <p id="wizardCounter" class="shrink-0 text-[7px] font-bold text-[#8f867a]">
                        1 of 5
                    </p>
                </div>

                <div class="mt-2.5 h-[3px] overflow-hidden rounded-full bg-[#ebe6df]">
                    <div
                        id="wizardProgressBar"
                        class="h-full rounded-full bg-[#d48f08] transition-all duration-300"
                        style="width:20%"
                    ></div>
                </div>
            </div>


            {{-- SERVER VALIDATION --}}
            @if ($errors->any())
                <div class="mx-5 mt-5 flex items-start gap-3 rounded-[13px] border border-[#ead0d0] bg-[#fff7f7] px-3.5 py-3 sm:mx-7">
                    <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0 text-[#a55555]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 8v5"></path>
                        <path d="M12 16.5h.01"></path>
                    </svg>
                    <div>
                        <p class="text-[7.5px] font-bold text-[#a55555]">Please review your registration</p>
                        <p class="mt-1 text-[7px] leading-4 text-[#a55555]">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <div class="px-5 py-5 sm:px-7 sm:py-6">
        <form
            id="registrationWizard"
            method="POST"
            action="{{ route('register.submit') }}"
            enctype="multipart/form-data"
            novalidate
        >
            @csrf

            <input type="hidden" id="provinceCode" name="province_code" value="{{ old('province_code') }}">
            <input type="hidden" id="provinceName" name="province_name" value="{{ old('province_name') }}">
            <input type="hidden" id="municipalityCode" name="municipality_code" value="{{ old('municipality_code') }}">
            <input type="hidden" id="municipalityName" name="municipality_name" value="{{ old('municipality_name') }}">
            <input type="hidden" id="barangayCode" name="barangay_code" value="{{ old('barangay_code') }}">
            <input type="hidden" id="barangayName" name="barangay_name" value="{{ old('barangay_name') }}">

            {{-- ROLE --}}
            <section
                class="sari-wizard-step is-entering"
                data-step="role"
            >
                <div class="text-center">
                    <h2 class="text-[18px] font-bold tracking-[-.025em] text-[#28231d]">
                        What will you use SARI for?
                    </h2>
                    <p class="mx-auto mt-2 max-w-[470px] text-[9px] leading-5 text-[#81786d]">
                        Choose how you want to use SARI. Your next step will open automatically.
                    </p>
                </div>

                <div class="mt-6">
                    <label for="roleSelect" class="mb-2 block text-[9px] font-semibold text-[#514b43]">
                        Register as *
                    </label>

                    <select
                        id="roleSelect"
                        name="role"
                        required
                        class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3.5 text-[9px] font-semibold"
                    >
                        <option value="">Select account type</option>
                        <option value="buyer" {{ old('role') === 'buyer' ? 'selected' : '' }}>Buyer</option>
                        <option value="seller" {{ old('role') === 'seller' ? 'selected' : '' }}>Seller</option>
                        <option value="courier" {{ old('role') === 'courier' ? 'selected' : '' }}>Courier</option>
                    </select>

                    <div id="rolePreview" hidden class="mt-3 rounded-xl border border-[#eadfc9] bg-[#fffaf2] px-4 py-3">
                        <p id="rolePreviewTitle" class="text-[9px] font-bold text-[#75551c]"></p>
                        <p id="rolePreviewText" class="mt-1 text-[8px] leading-4 text-[#877451]"></p>
                    </div>
                </div>
            </section>

            {{-- PERSONAL --}}
            <section
                hidden
                class="sari-wizard-step"
                data-step="personal"
            >
                <h2 class="text-[18px] font-bold text-[#28231d]">Personal Information</h2>
                <p class="mt-2 text-[9px] leading-5 text-[#81786d]">
                    Use the same information shown on your valid ID.
                </p>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Last Name *</label>
                        <input
                            type="text"
                            name="last_name"
                            value="{{ old('last_name') }}"
                            required
                            maxlength="100"
                            data-person-name
                            placeholder="Dela Cruz"
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px]"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">First Name *</label>
                        <input
                            type="text"
                            name="first_name"
                            value="{{ old('first_name') }}"
                            required
                            maxlength="100"
                            data-person-name
                            placeholder="Juan"
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px]"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Middle Initial</label>
                        <input
                            id="middleInitial"
                            type="text"
                            name="middle_initial"
                            value="{{ old('middle_initial') }}"
                            maxlength="1"
                            placeholder="M"
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px] uppercase"
                        >
                        <p class="mt-1 text-[7px] text-[#989084]">One letter only.</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Sex *</label>
                        <select
                            name="sex"
                            required
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px]"
                        >
                            <option value="">Select sex</option>
                            <option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Email *</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            placeholder="name@email.com"
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px]"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Contact No. *</label>
                        <input
                            id="contactNumber"
                            type="tel"
                            name="contact_no"
                            value="{{ old('contact_no') }}"
                            required
                            inputmode="numeric"
                            maxlength="11"
                            pattern="09[0-9]{9}"
                            placeholder="09XXXXXXXXX"
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px]"
                        >
                        <p id="contactHint" class="mt-1 text-[7px] text-[#989084]">
                            Exactly 11 digits beginning with 09.
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Birthday *</label>
                        <input
                            id="birthday"
                            type="date"
                            name="birthday"
                            value="{{ old('birthday') }}"
                            required
                            max="{{ now()->toDateString() }}"
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px]"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Age</label>
                        <input
                            id="agePreview"
                            type="text"
                            readonly
                            placeholder="Auto-generated"
                            class="h-11 w-full rounded-xl border border-[#e3ddd5] bg-[#f7f5f2] px-3 text-[10px] text-[#554d44] [-webkit-text-fill-color:#554d44]"
                        >
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <button type="button" data-back class="sari-register-action sari-register-secondary h-10 rounded-xl px-4 text-[8px] font-bold">
                        ← Back
                    </button>
                    <button type="button" data-next class="sari-register-action sari-register-primary h-10 rounded-xl px-4 text-[8px] font-bold">
                        Continue to Address →
                    </button>
                </div>
            </section>

            {{-- ADDRESS --}}
            <section
                hidden
                class="sari-wizard-step"
                data-step="address"
            >
                <h2 class="text-[18px] font-bold text-[#28231d]">Address</h2>
                <p class="mt-2 text-[9px] leading-5 text-[#81786d]">
                    Type a Province / Area such as <strong>Laguna</strong>. After selecting it, SARI loads only its cities and municipalities.
                </p>

                <div id="addressStatus" class="mt-4 inline-flex items-center gap-2 rounded-full bg-[#faf8f4] px-3 py-1.5 text-[7px] font-semibold text-[#867c70]">
                    <span id="addressStatusDot" class="h-1.5 w-1.5 rounded-full bg-[#d29b2d]"></span>
                    <span id="addressStatusText">Loading Philippine address directory...</span>
                </div>

                <div class="mt-5 space-y-4">
                    <div class="relative">
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Province / Area *</label>
                        <input
                            id="provinceSearch"
                            type="text"
                            autocomplete="off"
                            placeholder="Type Laguna, Cebu, Batangas..."
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px]"
                        >
                        <div id="provinceResults" hidden class="sari-location-list absolute left-0 right-0 top-[68px] z-30 rounded-xl border border-[#e3ddd4] bg-white p-1.5 shadow-[0_16px_38px_rgba(44,38,31,.12)]"></div>
                    </div>

                    <div class="relative">
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">City / Municipality *</label>
                        <input
                            id="municipalitySearch"
                            type="text"
                            autocomplete="off"
                            disabled
                            placeholder="Choose a province first"
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px] disabled:bg-[#f5f3ef] disabled:!text-[#a59c90] disabled:[-webkit-text-fill-color:#a59c90]"
                        >
                        <div id="municipalityResults" hidden class="sari-location-list absolute left-0 right-0 top-[68px] z-30 rounded-xl border border-[#e3ddd4] bg-white p-1.5 shadow-[0_16px_38px_rgba(44,38,31,.12)]"></div>
                    </div>

                    <div class="relative">
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Barangay *</label>
                        <input
                            id="barangaySearch"
                            type="text"
                            autocomplete="off"
                            disabled
                            placeholder="Choose a city / municipality first"
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px] disabled:bg-[#f5f3ef] disabled:!text-[#a59c90] disabled:[-webkit-text-fill-color:#a59c90]"
                        >
                        <div id="barangayResults" hidden class="sari-location-list absolute left-0 right-0 top-[68px] z-30 rounded-xl border border-[#e3ddd4] bg-white p-1.5 shadow-[0_16px_38px_rgba(44,38,31,.12)]"></div>
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Street / House No. / Subdivision *</label>
                        <textarea
                            name="street_address"
                            required
                            rows="3"
                            maxlength="500"
                            placeholder="House number, street, subdivision, landmark, etc."
                            class="sari-control w-full resize-none rounded-xl border border-[#e3ddd4] px-3 py-3 text-[10px] leading-5"
                        >{{ old('street_address') }}</textarea>
                    </div>
                </div>

                <div id="manualAddressFallback" hidden class="mt-4 rounded-xl border border-[#eadfc9] bg-[#fffaf2] p-4">
                    <p class="text-[8px] font-bold text-[#805d20]">Manual address entry</p>
                    <p class="mt-1 text-[7px] leading-4 text-[#887451]">
                        The online address directory could not be reached. Registration can still continue.
                    </p>

                    <div class="mt-3 grid grid-cols-1 gap-3">
                        <input id="manualProvince" type="text" placeholder="Province / Area" class="sari-control h-10 rounded-lg border border-[#e3ddd4] px-3 text-[9px]">
                        <input id="manualMunicipality" type="text" placeholder="City / Municipality" class="sari-control h-10 rounded-lg border border-[#e3ddd4] px-3 text-[9px]">
                        <input id="manualBarangay" type="text" placeholder="Barangay" class="sari-control h-10 rounded-lg border border-[#e3ddd4] px-3 text-[9px]">
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <button type="button" data-back class="sari-register-action sari-register-secondary h-10 rounded-xl px-4 text-[8px] font-bold">
                        ← Back
                    </button>
                    <button type="button" data-next class="sari-register-action sari-register-primary h-10 rounded-xl px-4 text-[8px] font-bold">
                        Continue →
                    </button>
                </div>
            </section>

            {{-- SELLER BUSINESS --}}
            <section
                hidden
                class="sari-wizard-step"
                data-step="seller-business"
            >
                <p class="text-[8px] font-bold uppercase tracking-[.13em] text-[#b97805]">Seller Requirement</p>
                <h2 class="mt-1 text-[18px] font-bold text-[#28231d]">Business Information</h2>

                <div class="mt-6 space-y-4">
                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Business Name *</label>
                        <input
                            id="businessName"
                            type="text"
                            name="business_name"
                            value="{{ old('business_name') }}"
                            maxlength="180"
                            data-seller-required
                            disabled
                            placeholder="SARI Home Essentials"
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px]"
                        >
                        <p class="mt-1 text-[7px] text-[#989084]">Numbers are not accepted.</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Line of Business *</label>
                        <select
                            name="line_of_business"
                            data-seller-required
                            disabled
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px]"
                        >
                            <option value="">Select business category</option>
                            @foreach([
                                'Electronics','Fashion','Home & Living','Beauty',
                                'Books','Food & Beverage','Jewelry & Watches',
                                'Furniture & Office','Others'
                            ] as $category)
                                <option value="{{ $category }}" {{ old('line_of_business') === $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <button type="button" data-back class="sari-register-action sari-register-secondary h-10 rounded-xl px-4 text-[8px] font-bold">← Back</button>
                    <button type="button" data-next class="sari-register-action sari-register-primary h-10 rounded-xl px-4 text-[8px] font-bold">Continue to Documents →</button>
                </div>
            </section>

            {{-- COURIER VEHICLE --}}
            <section
                hidden
                class="sari-wizard-step"
                data-step="courier-vehicle"
            >
                <p class="text-[8px] font-bold uppercase tracking-[.13em] text-[#52758e]">Courier Requirement</p>
                <h2 class="mt-1 text-[18px] font-bold text-[#28231d]">Vehicle Information</h2>

                <div class="mt-6 space-y-4">
                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Vehicle *</label>
                        <select
                            name="vehicle_type"
                            data-courier-required
                            disabled
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px]"
                        >
                            <option value="">Choose vehicle</option>
                            @foreach(['Motorcycle','Bicycle','Car','Van','Truck'] as $vehicle)
                                <option value="{{ $vehicle }}" {{ old('vehicle_type') === $vehicle ? 'selected' : '' }}>
                                    {{ $vehicle }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Plate Number *</label>
                        <input
                            id="plateNumber"
                            type="text"
                            name="plate_number"
                            value="{{ old('plate_number') }}"
                            maxlength="30"
                            data-courier-required
                            disabled
                            placeholder="ABC 1234"
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px] uppercase"
                        >
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <button type="button" data-back class="sari-register-action sari-register-secondary h-10 rounded-xl px-4 text-[8px] font-bold">← Back</button>
                    <button type="button" data-next class="sari-register-action sari-register-primary h-10 rounded-xl px-4 text-[8px] font-bold">Continue to Documents →</button>
                </div>
            </section>

            {{-- DOCUMENTS --}}
            <section
                hidden
                class="sari-wizard-step"
                data-step="documents"
            >
                <h2 class="text-[18px] font-bold text-[#28231d]">Verification Documents</h2>
                <p class="mt-2 text-[9px] leading-5 text-[#81786d]">
                    Only documents required for your selected role appear here.
                </p>

                <div class="mt-6 space-y-4">
                    <div>
                        <label id="idDocumentLabel" class="mb-2 block text-[9px] font-semibold text-[#514b43]">Valid ID *</label>
                        <input
                            type="file"
                            name="id_document"
                            required
                            accept=".jpg,.jpeg,.png,.webp,.pdf"
                            class="block w-full rounded-xl border border-[#e3ddd4] bg-white px-3 py-3 text-[9px] !text-[#3d3731]"
                        >
                    </div>

                    <div id="sellerPermitGroup" hidden>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Business Permit *</label>
                        <input
                            type="file"
                            name="business_permit"
                            data-seller-required
                            disabled
                            accept=".jpg,.jpeg,.png,.webp,.pdf"
                            class="block w-full rounded-xl border border-[#e3ddd4] bg-white px-3 py-3 text-[9px] !text-[#3d3731]"
                        >
                    </div>

                    <div id="courierOrcrGroup" hidden>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">OR / CR *</label>
                        <input
                            type="file"
                            name="orcr_document"
                            data-courier-required
                            disabled
                            accept=".jpg,.jpeg,.png,.webp,.pdf"
                            class="block w-full rounded-xl border border-[#e3ddd4] bg-white px-3 py-3 text-[9px] !text-[#3d3731]"
                        >
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <button type="button" data-back class="sari-register-action sari-register-secondary h-10 rounded-xl px-4 text-[8px] font-bold">← Back</button>
                    <button type="button" data-next class="sari-register-action sari-register-primary h-10 rounded-xl px-4 text-[8px] font-bold">Create Login Credentials →</button>
                </div>
            </section>

            {{-- CREDENTIALS --}}
            <section
                hidden
                class="sari-wizard-step"
                data-step="credentials"
            >
                <h2 class="text-[18px] font-bold text-[#28231d]">Login Credentials</h2>
                <p class="mt-2 text-[9px] leading-5 text-[#81786d]">
                    These credentials become usable only after administrator approval.
                </p>

                <div class="mt-6 space-y-4">
                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Password *</label>
                        <input
                            type="password"
                            name="password"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Minimum 8 characters"
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px]"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-[9px] font-semibold text-[#514b43]">Confirm Password *</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Repeat password"
                            class="sari-control h-11 w-full rounded-xl border border-[#e3ddd4] px-3 text-[10px]"
                        >
                    </div>

                    <label class="flex items-start gap-2.5 rounded-xl bg-[#faf8f4] p-3 text-[8px] leading-4 text-[#746c62]">
                        <input
                            type="checkbox"
                            name="terms"
                            value="1"
                            required
                            class="mt-0.5 h-4 w-4 shrink-0 accent-[#c98a08]"
                        >
                        <span>
                            I agree to the <a href="#" class="font-semibold text-[#a96f06]">Terms of Service</a>
                            and <a href="#" class="font-semibold text-[#a96f06]">Privacy Policy</a>.
                        </span>
                    </label>

                    <div class="rounded-xl border border-[#eadfc9] bg-[#fffaf2] p-4">
                        <p class="text-[8px] font-bold text-[#805d20]">Administrator approval required</p>
                        <p class="mt-1 text-[7px] leading-4 text-[#887451]">
                            Submission creates a pending application. Approval is required before login.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <button type="button" data-back class="sari-register-action sari-register-secondary h-10 rounded-xl px-4 text-[8px] font-bold">← Back</button>
                    <button type="submit" class="sari-register-action sari-register-primary h-10 rounded-xl px-4 text-[8px] font-bold">
                        Submit Registration
                    </button>
                </div>
            </section>
        </form>

            </div>
        </div>

        {{-- ACCOUNT SHORTCUT --}}
        <p class="mt-5 text-center text-[9px] text-[#756d63] sm:text-[10px]">
            Already have an account?
            <a
                href="{{ route('login') }}"
                class="ml-1 font-bold text-[#a96f06] transition hover:text-[#7f5104]"
            >
                Sign in
            </a>
        </p>

        <p class="mt-5 text-center text-[7px] text-[#978e83]">
            © {{ date('Y') }} SARI. All rights reserved.
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registrationWizard');
    const allSteps = Array.from(document.querySelectorAll('[data-step]'));
    const roleSelect = document.getElementById('roleSelect');

    const progressBar = document.getElementById('wizardProgressBar');
    const stepEyebrow = document.getElementById('wizardStepEyebrow');
    const stepTitle = document.getElementById('wizardStepTitle');
    const counter = document.getElementById('wizardCounter');

    let currentStepName = 'role';
    let addressLoaded = false;

    const stepTitles = {
        role: 'Choose account type',
        personal: 'Personal information',
        address: 'Address',
        'seller-business': 'Business information',
        'courier-vehicle': 'Vehicle information',
        documents: 'Verification documents',
        credentials: 'Login credentials'
    };

    function selectedRole() {
        return roleSelect.value || '';
    }

    function flow() {
        if (selectedRole() === 'seller') {
            return ['role', 'personal', 'address', 'seller-business', 'documents', 'credentials'];
        }

        if (selectedRole() === 'courier') {
            return ['role', 'personal', 'address', 'courier-vehicle', 'documents', 'credentials'];
        }

        return ['role', 'personal', 'address', 'documents', 'credentials'];
    }

    function activeStep() {
        return allSteps.find(step => step.dataset.step === currentStepName);
    }

    function updateProgress() {
        const steps = flow();
        const index = Math.max(0, steps.indexOf(currentStepName));
        const percent = ((index + 1) / steps.length) * 100;

        progressBar.style.width = percent + '%';
        stepEyebrow.textContent = 'Step ' + (index + 1);
        stepTitle.textContent = stepTitles[currentStepName] || '';
        counter.textContent = (index + 1) + ' of ' + steps.length;
    }

    function showStep(stepName) {
        const current = activeStep();
        const target = allSteps.find(step => step.dataset.step === stepName);

        if (!target || target === current) return;

        if (current) {
            current.classList.remove('is-entering');
            current.classList.add('is-leaving');

            window.setTimeout(function () {
                current.hidden = true;
                current.classList.remove('is-leaving');
            }, 180);
        }

        window.setTimeout(function () {
            target.hidden = false;
            target.classList.add('is-entering');
            currentStepName = stepName;
            updateProgress();

            const card = target.closest('.sari-register-card');

            if (card) {
                const cardTop = card.getBoundingClientRect().top + window.scrollY - 24;

                if (window.scrollY > cardTop + 120 || window.scrollY < cardTop - 120) {
                    window.scrollTo({
                        top: Math.max(0, cardTop),
                        behavior: 'smooth'
                    });
                }
            }

            if (stepName === 'address' && !addressLoaded) {
                addressLoaded = true;
                loadProvinces();
            }
        }, current ? 185 : 0);
    }

    function validateCurrentStep() {
        const step = activeStep();
        if (!step) return true;

        const fields = Array.from(
            step.querySelectorAll('input, select, textarea')
        ).filter(field => !field.disabled && field.type !== 'hidden');

        for (const field of fields) {
            if (!field.checkValidity()) {
                field.reportValidity();
                field.focus();
                return false;
            }
        }

        if (
            currentStepName === 'address'
            && (
                !document.getElementById('provinceName').value
                || !document.getElementById('municipalityName').value
                || !document.getElementById('barangayName').value
            )
        ) {
            window.alert('Please complete Province / Area, City / Municipality, and Barangay.');
            return false;
        }

        return true;
    }

    function goNext() {
        if (!validateCurrentStep()) return;

        const steps = flow();
        const index = steps.indexOf(currentStepName);

        if (index >= 0 && index < steps.length - 1) {
            showStep(steps[index + 1]);
        }
    }

    function goBack() {
        const steps = flow();
        const index = steps.indexOf(currentStepName);

        if (index > 0) {
            showStep(steps[index - 1]);
        }
    }

    document.querySelectorAll('[data-next]').forEach(
        button => button.addEventListener('click', goNext)
    );

    document.querySelectorAll('[data-back]').forEach(
        button => button.addEventListener('click', goBack)
    );

    /* ROLE */
    const rolePreview = document.getElementById('rolePreview');
    const rolePreviewTitle = document.getElementById('rolePreviewTitle');
    const rolePreviewText = document.getElementById('rolePreviewText');
    const sellerPermitGroup = document.getElementById('sellerPermitGroup');
    const courierOrcrGroup = document.getElementById('courierOrcrGroup');
    const idDocumentLabel = document.getElementById('idDocumentLabel');

    function syncRoleRequirements() {
        const role = selectedRole();
        const isSeller = role === 'seller';
        const isCourier = role === 'courier';

        document.querySelectorAll('[data-seller-required]').forEach(function (field) {
            field.disabled = !isSeller;
            field.required = isSeller;
        });

        document.querySelectorAll('[data-courier-required]').forEach(function (field) {
            field.disabled = !isCourier;
            field.required = isCourier;
        });

        sellerPermitGroup.hidden = !isSeller;
        courierOrcrGroup.hidden = !isCourier;

        idDocumentLabel.textContent = isCourier
            ? 'Valid ID / Driver’s License *'
            : 'Valid ID *';

        const details = {
            buyer: {
                title: 'Buyer account',
                text: 'Personal Information → Address → Valid ID → Login Credentials.'
            },
            seller: {
                title: 'Seller account',
                text: 'Personal Information → Address → Business Information → Valid ID + Business Permit → Login Credentials.'
            },
            courier: {
                title: 'Courier account',
                text: 'Personal Information → Address → Vehicle Information → Valid ID / Driver’s License + OR/CR → Login Credentials.'
            }
        };

        if (!details[role]) {
            rolePreview.hidden = true;
            return;
        }

        rolePreviewTitle.textContent = details[role].title;
        rolePreviewText.textContent = details[role].text;
        rolePreview.hidden = false;
    }

    roleSelect.addEventListener('change', function () {
        syncRoleRequirements();
        updateProgress();

        if (roleSelect.value) {
            window.setTimeout(() => showStep('personal'), 180);
        }
    });

    /* PERSONAL VALIDATION */
    document.querySelectorAll('[data-person-name]').forEach(function (input) {
        input.addEventListener('input', function () {
            this.value = this.value
                .replace(/[^\p{L}\s.'-]/gu, '')
                .replace(/\s{2,}/g, ' ');
        });
    });

    const middleInitial = document.getElementById('middleInitial');

    middleInitial.addEventListener('input', function () {
        this.value = this.value
            .replace(/[^\p{L}]/gu, '')
            .slice(0, 1)
            .toUpperCase();
    });

    const contactNumber = document.getElementById('contactNumber');
    const contactHint = document.getElementById('contactHint');

    function syncPhone() {
        contactNumber.value = contactNumber.value.replace(/\D/g, '').slice(0, 11);

        if (!contactNumber.value) {
            contactHint.textContent = 'Exactly 11 digits beginning with 09.';
            contactHint.className = 'mt-1 text-[7px] text-[#989084]';
            contactNumber.setCustomValidity('');
            return;
        }

        const valid = /^09\d{9}$/.test(contactNumber.value);

        if (valid) {
            contactHint.textContent = 'Valid Philippine mobile number.';
            contactHint.className = 'mt-1 text-[7px] text-[#56816a]';
            contactNumber.setCustomValidity('');
        } else {
            contactHint.textContent = 'Use 11 digits starting with 09, e.g. 09171234567.';
            contactHint.className = 'mt-1 text-[7px] text-[#b25a5a]';
            contactNumber.setCustomValidity(
                'Enter an 11-digit Philippine mobile number starting with 09.'
            );
        }
    }

    contactNumber.addEventListener('input', syncPhone);

    const birthday = document.getElementById('birthday');
    const agePreview = document.getElementById('agePreview');

    function calculateAge() {
        if (!birthday.value) {
            agePreview.value = '';
            return;
        }

        const birth = new Date(birthday.value + 'T00:00:00');
        const today = new Date();

        let age = today.getFullYear() - birth.getFullYear();

        if (
            today.getMonth() < birth.getMonth()
            || (
                today.getMonth() === birth.getMonth()
                && today.getDate() < birth.getDate()
            )
        ) {
            age--;
        }

        agePreview.value = Math.max(0, age);
    }

    birthday.addEventListener('change', calculateAge);

    /* BUSINESS / COURIER */
    const businessName = document.getElementById('businessName');

    businessName.addEventListener('input', function () {
        this.value = this.value
            .replace(/[^\p{L}\s.&'()\-]/gu, '')
            .replace(/\s{2,}/g, ' ');
    });

    const plateNumber = document.getElementById('plateNumber');

    plateNumber.addEventListener('input', function () {
        this.value = this.value
            .replace(/[^A-Za-z0-9 -]/g, '')
            .toUpperCase();
    });

    /* ADDRESS */
    const provinceSearch = document.getElementById('provinceSearch');
    const municipalitySearch = document.getElementById('municipalitySearch');
    const barangaySearch = document.getElementById('barangaySearch');

    const provinceResults = document.getElementById('provinceResults');
    const municipalityResults = document.getElementById('municipalityResults');
    const barangayResults = document.getElementById('barangayResults');

    const provinceCode = document.getElementById('provinceCode');
    const provinceName = document.getElementById('provinceName');
    const municipalityCode = document.getElementById('municipalityCode');
    const municipalityName = document.getElementById('municipalityName');
    const barangayCode = document.getElementById('barangayCode');
    const barangayName = document.getElementById('barangayName');

    const statusDot = document.getElementById('addressStatusDot');
    const statusText = document.getElementById('addressStatusText');

    const manualFallback = document.getElementById('manualAddressFallback');
    const manualProvince = document.getElementById('manualProvince');
    const manualMunicipality = document.getElementById('manualMunicipality');
    const manualBarangay = document.getElementById('manualBarangay');

    let provinces = [];
    let municipalities = [];
    let barangays = [];
    let manualMode = false;

    function normalizeList(payload) {
        if (Array.isArray(payload)) return payload;
        if (Array.isArray(payload?.data)) return payload.data;
        if (Array.isArray(payload?.data?.data)) return payload.data.data;
        if (Array.isArray(payload?.results)) return payload.results;
        if (Array.isArray(payload?.items)) return payload.items;
        return [];
    }

    function locationCode(item) {
        return String(item.code ?? item.psgc_code ?? item.psgcCode ?? item.id ?? '');
    }

    function locationName(item) {
        return String(item.name ?? item.area_name ?? item.areaName ?? item.label ?? '');
    }

    function setAddressStatus(type, text) {
        statusText.textContent = text;

        statusDot.classList.remove(
            'bg-[#d29b2d]',
            'bg-[#56816a]',
            'bg-[#b25a5a]'
        );

        statusDot.classList.add(
            type === 'ready'
                ? 'bg-[#56816a]'
                : type === 'error'
                    ? 'bg-[#b25a5a]'
                    : 'bg-[#d29b2d]'
        );
    }

    async function fetchJson(url) {
        const response = await fetch(url, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            cache: 'no-store'
        });

        if (!response.ok) {
            throw new Error('Address request failed: ' + response.status);
        }

        return response.json();
    }

    function renderResults(container, items, searchValue, onSelect) {
        container.innerHTML = '';

        const query = searchValue.trim().toLocaleLowerCase();

        const matches = items
            .filter(item => locationName(item).toLocaleLowerCase().includes(query))
            .slice(0, 30);

        if (!matches.length) {
            const empty = document.createElement('div');
            empty.className = 'px-3 py-3 text-[8px] text-[#91887c]';
            empty.textContent = 'No matching location found.';
            container.appendChild(empty);
            container.hidden = false;
            return;
        }

        matches.forEach(function (item) {
            const option = document.createElement('button');
            option.type = 'button';
            option.className =
                'sari-location-option block w-full rounded-lg px-3 py-2.5 text-left text-[9px] text-[#403930]';
            option.textContent = locationName(item);

            option.addEventListener('mousedown', function (event) {
                event.preventDefault();
                onSelect(item);
            });

            container.appendChild(option);
        });

        container.hidden = false;
    }

    function bindAutocomplete(input, container, getItems, onSelect) {
        input.addEventListener('focus', function () {
            if (!input.disabled) {
                renderResults(container, getItems(), input.value, onSelect);
            }
        });

        input.addEventListener('input', function () {
            if (!input.disabled) {
                renderResults(container, getItems(), input.value, onSelect);
            }
        });

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !container.hidden) {
                const first = container.querySelector('button');

                if (first) {
                    event.preventDefault();
                    first.dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));
                }
            }
        });

        input.addEventListener('blur', function () {
            window.setTimeout(() => container.hidden = true, 120);
        });
    }

    async function loadProvinces() {
        setAddressStatus('loading', 'Loading Philippine address directory...');

        try {
            provinces = normalizeList(
                await fetchJson(@json(route('address.provinces')))
            );

            if (!provinces.length) throw new Error('No provinces returned.');

            setAddressStatus('ready', 'Address directory ready. Type a province or area.');
        } catch (error) {
            console.error('SARI address directory:', error);
            enableManualAddress();
        }
    }

    async function loadMunicipalities(selectedProvinceCode) {
        municipalitySearch.disabled = true;
        barangaySearch.disabled = true;

        municipalitySearch.value = '';
        barangaySearch.value = '';

        municipalityCode.value = '';
        municipalityName.value = '';
        barangayCode.value = '';
        barangayName.value = '';

        municipalitySearch.placeholder = 'Loading cities / municipalities...';

        try {
            const url =
                @json(url('/address/provinces'))
                + '/'
                + encodeURIComponent(selectedProvinceCode)
                + '/municipalities';

            municipalities = normalizeList(await fetchJson(url));

            if (!municipalities.length) throw new Error('No cities returned.');

            municipalitySearch.disabled = false;
            municipalitySearch.placeholder = 'Type city or municipality';
        } catch (error) {
            console.error('SARI city directory:', error);
            enableManualAddress();
        }
    }

    async function loadBarangays(selectedMunicipalityCode) {
        barangaySearch.disabled = true;
        barangaySearch.value = '';

        barangayCode.value = '';
        barangayName.value = '';
        barangaySearch.placeholder = 'Loading barangays...';

        try {
            const url =
                @json(url('/address/municipalities'))
                + '/'
                + encodeURIComponent(selectedMunicipalityCode)
                + '/barangays';

            barangays = normalizeList(await fetchJson(url));

            if (!barangays.length) throw new Error('No barangays returned.');

            barangaySearch.disabled = false;
            barangaySearch.placeholder = 'Type barangay';
        } catch (error) {
            console.error('SARI barangay directory:', error);
            enableManualAddress();
        }
    }

    function selectProvince(item) {
        provinceCode.value = locationCode(item);
        provinceName.value = locationName(item);
        provinceSearch.value = locationName(item);
        provinceResults.hidden = true;

        loadMunicipalities(locationCode(item));
    }

    function selectMunicipality(item) {
        municipalityCode.value = locationCode(item);
        municipalityName.value = locationName(item);
        municipalitySearch.value = locationName(item);
        municipalityResults.hidden = true;

        loadBarangays(locationCode(item));
    }

    function selectBarangay(item) {
        barangayCode.value = locationCode(item);
        barangayName.value = locationName(item);
        barangaySearch.value = locationName(item);
        barangayResults.hidden = true;
    }

    bindAutocomplete(
        provinceSearch,
        provinceResults,
        () => provinces,
        selectProvince
    );

    bindAutocomplete(
        municipalitySearch,
        municipalityResults,
        () => municipalities,
        selectMunicipality
    );

    bindAutocomplete(
        barangaySearch,
        barangayResults,
        () => barangays,
        selectBarangay
    );

    provinceSearch.addEventListener('input', function () {
        if (provinceSearch.value !== provinceName.value) {
            provinceCode.value = '';
            provinceName.value = '';

            municipalityCode.value = '';
            municipalityName.value = '';
            municipalitySearch.value = '';
            municipalitySearch.disabled = true;

            barangayCode.value = '';
            barangayName.value = '';
            barangaySearch.value = '';
            barangaySearch.disabled = true;
        }
    });

    municipalitySearch.addEventListener('input', function () {
        if (municipalitySearch.value !== municipalityName.value) {
            municipalityCode.value = '';
            municipalityName.value = '';

            barangayCode.value = '';
            barangayName.value = '';
            barangaySearch.value = '';
            barangaySearch.disabled = true;
        }
    });

    barangaySearch.addEventListener('input', function () {
        if (barangaySearch.value !== barangayName.value) {
            barangayCode.value = '';
            barangayName.value = '';
        }
    });

    function enableManualAddress() {
        manualMode = true;

        provinceSearch.disabled = true;
        municipalitySearch.disabled = true;
        barangaySearch.disabled = true;

        manualFallback.hidden = false;

        manualProvince.required = true;
        manualMunicipality.required = true;
        manualBarangay.required = true;

        setAddressStatus(
            'error',
            'Online address directory unavailable — manual entry enabled.'
        );

        syncManualAddress();
    }

    function syncManualAddress() {
        if (!manualMode) return;

        provinceCode.value = 'MANUAL';
        municipalityCode.value = 'MANUAL';
        barangayCode.value = 'MANUAL';

        provinceName.value = manualProvince.value.trim();
        municipalityName.value = manualMunicipality.value.trim();
        barangayName.value = manualBarangay.value.trim();
    }

    [manualProvince, manualMunicipality, manualBarangay].forEach(
        input => input.addEventListener('input', syncManualAddress)
    );

    form.addEventListener('submit', function (event) {
        syncPhone();

        if (manualMode) {
            syncManualAddress();
        }

        if (!form.checkValidity()) {
            event.preventDefault();

            const invalid = form.querySelector(':invalid');

            if (invalid) {
                const step = invalid.closest('[data-step]');

                if (step) {
                    showStep(step.dataset.step);

                    window.setTimeout(function () {
                        invalid.reportValidity();
                        invalid.focus();
                    }, 250);
                }
            }

            return;
        }

        if (!provinceName.value || !municipalityName.value || !barangayName.value) {
            event.preventDefault();
            showStep('address');

            window.setTimeout(function () {
                window.alert('Please select a complete address.');
            }, 220);
        }
    });

    syncRoleRequirements();
    calculateAge();
    syncPhone();
    updateProgress();
});
</script>

@endsection