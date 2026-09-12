@extends('layouts.app')

@section('title', 'Rider Application — SARI')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<style>
    :root {
        --rider-gold: #d48f08;
        --rider-gold-dark: #bd7d05;
        --rider-ink: #211d17;
        --rider-muted: #81786c;
        --rider-line: #e5ded4;
    }

    .rider-form-card,
    .rider-provider-summary {
        border: 1px solid var(--rider-line);
        background: #fff;
        box-shadow:
            12px 14px 32px rgba(52, 40, 25, .075),
            -4px -4px 14px rgba(255, 255, 255, .9);
    }

    .rider-control {
        min-height: 50px;
        width: 100%;
        border: 1px solid #e2dbd1;
        border-radius: 13px;
        background: #fff;
        padding: 0 14px;
        color: #211d17;
        font-size: 13px;
        outline: none;
        transition: border-color .16s ease, box-shadow .16s ease;
    }

    textarea.rider-control {
        min-height: 112px;
        padding-top: 13px;
        padding-bottom: 13px;
        resize: vertical;
    }

    .rider-control:focus {
        border-color: var(--rider-gold);
        box-shadow: 0 0 0 4px rgba(212, 143, 8, .09);
    }

    .rider-control:disabled {
        background: #f4f1ed;
        color: #a0998f;
    }

    .rider-label {
        margin-bottom: 7px;
        display: block;
        font-size: 11px;
        font-weight: 650;
        color: #514a42;
    }

    .rider-help {
        margin-top: 5px;
        font-size: 9px;
        line-height: 1.5;
        color: #989084;
    }

    .rider-section {
        border-top: 1px solid #eee8df;
        padding-top: 24px;
    }

    .rider-location-list {
        max-height: 220px;
        overflow-y: auto;
        overscroll-behavior: contain;
    }

    [hidden] { display: none !important; }

    .rider-profile-row {
        display: grid;
        grid-template-columns: 76px minmax(0, 1fr);
        gap: 15px;
        align-items: center;
        margin-top: 20px;
        border-top: 1px solid #eee8df;
        padding-top: 19px;
    }

    .rider-profile-preview {
        display: grid;
        width: 72px;
        height: 72px;
        place-items: center;
        overflow: hidden;
        border: 1px solid #e2dbd1;
        border-radius: 18px;
        background: #faf8f4;
        color: #b97805;
    }

    .rider-profile-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .rider-profile-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 9px;
    }

    .rider-profile-button {
        display: inline-flex;
        min-height: 36px;
        align-items: center;
        justify-content: center;
        border: 1px solid #d7bd8c;
        border-radius: 9px;
        background: #fff;
        padding: 8px 12px;
        font-size: 10px;
        font-weight: 650;
        color: #9b6607;
    }

    .rider-profile-error {
        margin-top: 6px;
        font-size: 10px;
        font-weight: 650;
        color: #d31324;
    }

    @media (max-width: 639px) {
        .rider-profile-row {
            grid-template-columns: 60px minmax(0, 1fr);
        }

        .rider-profile-preview {
            width: 58px;
            height: 58px;
            border-radius: 15px;
        }
    }


    /* ============================================================
       RIDER PROFILE V2 — LARGE DRAG & DROP PHOTO AREA
       ============================================================ */
    .rider-profile-row {
        display: block !important;
        margin-top: 22px !important;
        border-top: 1px solid #eee8df !important;
        padding-top: 20px !important;
    }

    .rider-profile-zone {
        position: relative;
        display: grid;
        width: 100%;
        min-height: 200px;
        margin-top: 13px;
        place-items: center;
        overflow: hidden;
        border: 1.5px dashed #d7b76f;
        border-radius: 16px;
        background: #fffdf9;
        color: #b97805;
        cursor: pointer;
        outline: none;
        transition:
            border-color .18s ease,
            background-color .18s ease,
            box-shadow .18s ease,
            transform .18s ease;
    }

    .rider-profile-zone:hover,
    .rider-profile-zone:focus-visible {
        border-color: #c88a16;
        background: #fffaf0;
        box-shadow: 0 0 0 4px rgba(212, 143, 8, .07);
    }

    .rider-profile-zone.is-dragging {
        border-style: solid;
        border-color: #c98208;
        background: #fff8e9;
        box-shadow: 0 0 0 5px rgba(212, 143, 8, .09);
        transform: translateY(-1px);
    }

    .rider-profile-zone.has-image {
        min-height: 230px;
        border-style: solid;
        border-color: #ddd7cf;
        background: #f6f4f0;
        cursor: default;
        box-shadow: 0 10px 28px rgba(31, 27, 22, .045);
    }

    .rider-profile-zone.has-error {
        border: 2px solid #e11d2e;
        box-shadow: 0 0 0 4px rgba(225, 29, 46, .09);
    }

    .rider-profile-zone img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .rider-profile-empty {
        display: flex;
        max-width: 420px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 26px 20px;
        text-align: center;
    }

    .rider-profile-camera {
        display: grid;
        width: 52px;
        height: 52px;
        place-items: center;
        border-radius: 15px;
        background: #fff4dc;
        color: #b97805;
    }

    .rider-profile-camera svg {
        width: 25px;
        height: 25px;
    }

    .rider-profile-drop-title {
        margin-top: 13px;
        font-size: 12px;
        line-height: 1.4;
        font-weight: 700;
        color: #39342e;
    }

    .rider-profile-drop-copy {
        margin-top: 5px;
        font-size: 9.5px;
        line-height: 1.55;
        color: #91887d;
    }

    .rider-profile-drop-browse {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        justify-content: center;
        margin-top: 12px;
        border: 1px solid #d4b16d;
        border-radius: 9px;
        background: #fff;
        padding: 8px 13px;
        font-size: 10px;
        font-weight: 650;
        color: #9b6607;
    }

    .rider-profile-drop-meta {
        margin-top: 9px;
        font-size: 9px;
        color: #a29a91;
    }

    .rider-profile-image-actions {
        position: absolute;
        right: 11px;
        bottom: 11px;
        z-index: 3;
        display: flex;
        gap: 7px;
    }

    .rider-profile-image-action {
        display: inline-flex;
        min-height: 33px;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255,255,255,.72);
        border-radius: 9px;
        background: rgba(32, 29, 25, .82);
        padding: 7px 10px;
        font-family: 'Poppins', sans-serif;
        font-size: 9.5px;
        font-weight: 650;
        color: #fff;
        backdrop-filter: blur(8px);
    }

    .rider-profile-image-action.is-remove {
        background: rgba(255,255,255,.92);
        border-color: rgba(255,255,255,.92);
        color: #5e574f;
    }

    .rider-profile-error {
        margin-top: 8px;
        font-size: 10px;
        font-weight: 650;
        color: #d31324;
    }

    @media (max-width: 639px) {
        .rider-profile-zone {
            min-height: 170px;
            border-radius: 14px;
        }

        .rider-profile-zone.has-image {
            min-height: 200px;
        }
    }


    /* RIDER PROFILE DROPZONE V3 — ULTRA CLEAN */
    .rider-profile-drop-copy,
    .rider-profile-drop-meta {
        display: none !important;
    }

    .rider-profile-drop-title {
        margin-top: 12px !important;
        font-size: 12px !important;
    }

    .rider-profile-drop-browse {
        margin-top: 10px !important;
        min-height: 32px !important;
        padding: 7px 12px !important;
        font-size: 9.5px !important;
    }

    .rider-profile-image-action {
        width: 36px !important;
        height: 36px !important;
        min-height: 36px !important;
        padding: 0 !important;
        border-radius: 10px !important;
    }

    .rider-profile-image-action svg {
        width: 16px !important;
        height: 16px !important;
    }

    .rider-profile-image-action.is-remove {
        color: #8a403d !important;
    }

</style>

@php
    $providerName = $logistics->displayName();
    $providerLocation = collect([$logistics->municipality_name, $logistics->province_name])->filter()->implode(', ');
@endphp

<div class="min-h-screen bg-[#f7f4ee] font-['Poppins',sans-serif] text-[#211d17]">
    <div class="fixed inset-0 pointer-events-none">
        <img src="{{ asset('images/login-bg.jpg') }}" alt="" class="h-full w-full object-cover object-center opacity-[.09]">
        <div class="absolute inset-0 bg-[#f8f5ef]/91"></div>
    </div>

    <main class="relative z-10 mx-auto w-full max-w-[1180px] px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('rider.logistics.index') }}" class="inline-flex items-center gap-2 text-[11px] font-semibold text-[#8e7755] hover:text-[#a96f06]">← Choose another Logistics</a>
                <p class="mt-6 text-[10px] font-bold uppercase tracking-[.18em] text-[#b97805]">Rider Application</p>
                <h1 class="mt-2 text-[30px] font-bold tracking-[-.04em] text-[#201c17] sm:text-[38px]">Apply to {{ $providerName }}</h1>
                <p class="mt-3 max-w-[680px] text-[12px] leading-6 text-[#7d7469] sm:text-[13px]">
                    Submit your Rider details and documents. This application will be visible only to the Logistics provider you selected for operational review.
                </p>
            </div>
        </div>

        <div class="mt-7 grid gap-5 lg:grid-cols-[300px_minmax(0,1fr)] lg:items-start">
            <aside class="rider-provider-summary rounded-[22px] p-5 lg:sticky lg:top-6">
                @if($logistics->profile_image_path)
                    <img
                        src="{{ asset('storage/' . $logistics->profile_image_path) }}"
                        alt="{{ $providerName }}"
                        class="h-14 w-14 rounded-[15px] border border-[#eadfc9] object-cover"
                    >
                @else
                    <span class="grid h-12 w-12 place-items-center rounded-[14px] bg-[#fff5df] text-[#b67814]">
                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V7l8-4 8 4v12"></path><path d="M8 19v-5h8v5M8 9h.01M12 9h.01M16 9h.01"></path></svg>
                    </span>
                @endif
                <p class="mt-5 text-[8px] font-bold uppercase tracking-[.13em] text-[#a98a60]">Applying to</p>
                <h2 class="mt-2 text-[18px] font-bold tracking-[-.025em] text-[#2b261f]">{{ $providerName }}</h2>
                <p class="mt-1 text-[10px] leading-5 text-[#8c8378]">{{ $providerLocation ?: 'SARI Logistics network' }}</p>

                <div class="mt-5 space-y-3 border-y border-[#eee8df] py-4">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[9px] text-[#91887d]">Status</span>
                        <span class="inline-flex items-center gap-1.5 text-[9px] font-bold text-[#438461]"><span class="h-1.5 w-1.5 rounded-full bg-[#438461]"></span>Active</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[9px] text-[#91887d]">Approved Riders</span>
                        <strong class="text-[11px] text-[#2f2923]">{{ number_format((int) ($logistics->approved_riders_count ?? 0)) }}</strong>
                    </div>
                </div>

                <div class="mt-5 rounded-[14px] bg-[#f7f9f7] p-4">
                    <p class="text-[9px] font-bold text-[#486d58]">What happens next?</p>
                    <p class="mt-1.5 text-[9px] leading-5 text-[#789083]">Your selected Logistics team reviews the application. If approved, your Rider account becomes available for delivery assignments.</p>
                </div>
            </aside>

            <section class="rider-form-card rounded-[24px] p-5 sm:p-7 lg:p-8">
                @if ($errors->any())
                    <div class="mb-6 rounded-[15px] border border-[#edcccc] bg-[#fff7f7] p-4">
                        <p class="text-[11px] font-bold text-[#a55555]">Please review your application</p>
                        <p class="mt-1 text-[10px] leading-5 text-[#a55555]">{{ $errors->first() }}</p>
                    </div>
                @endif

                <form id="riderApplicationForm" method="POST" action="{{ route('rider.logistics.submit', $logistics) }}" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" id="provinceCode" name="province_code" value="{{ old('province_code') }}">
                    <input type="hidden" id="provinceName" name="province_name" value="{{ old('province_name') }}">
                    <input type="hidden" id="municipalityCode" name="municipality_code" value="{{ old('municipality_code') }}">
                    <input type="hidden" id="municipalityName" name="municipality_name" value="{{ old('municipality_name') }}">
                    <input type="hidden" id="barangayCode" name="barangay_code" value="{{ old('barangay_code') }}">
                    <input type="hidden" id="barangayName" name="barangay_name" value="{{ old('barangay_name') }}">

                    <section>
                        <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">01 · Personal</p>
                        <h2 class="mt-1 text-[20px] font-bold tracking-[-.025em] text-[#29241f]">Personal information</h2>
                        <p class="mt-2 text-[10px] leading-5 text-[#8d8479]">Use the same details shown on your valid ID or driver's license.</p>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="rider-label">Last Name *</label>
                                <input name="last_name" value="{{ old('last_name') }}" required maxlength="100" placeholder="Dela Cruz" class="rider-control" data-person-name>
                            </div>
                            <div>
                                <label class="rider-label">First Name *</label>
                                <input name="first_name" value="{{ old('first_name') }}" required maxlength="100" placeholder="Juan" class="rider-control" data-person-name>
                            </div>
                            <div>
                                <label class="rider-label">Middle Initial</label>
                                <input id="middleInitial" name="middle_initial" value="{{ old('middle_initial') }}" maxlength="1" placeholder="M" class="rider-control uppercase">
                            </div>
                            <div>
                                <label class="rider-label">Sex *</label>
                                <select name="sex" required class="rider-control">
                                    <option value="">Select sex</option>
                                    <option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div>
                                <label class="rider-label">Email *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="name@email.com" class="rider-control">
                            </div>
                            <div>
                                <label class="rider-label">Contact No. *</label>
                                <input id="contactNumber" type="tel" name="contact_no" value="{{ old('contact_no') }}" required inputmode="numeric" maxlength="11" pattern="09[0-9]{9}" placeholder="09XXXXXXXXX" class="rider-control">
                                <p id="contactHint" class="rider-help">Exactly 11 digits beginning with 09.</p>
                            </div>
                            <div>
                                <label class="rider-label">Birthday *</label>
                                <input id="birthday" type="date" name="birthday" value="{{ old('birthday') }}" required min="1900-01-01" max="{{ now()->subYears(18)->toDateString() }}" class="rider-control">
                                <p id="ageRequirement" class="rider-help">You must be 18 years old or older to register.</p>
                            </div>
                            <div>
                                <label class="rider-label">Age</label>
                                <input id="agePreview" readonly placeholder="Auto-generated" class="rider-control !bg-[#f5f2ee]">
                            </div>
                        </div>
                        <div class="rider-profile-row">
                            <p class="rider-label !mb-0">
                                Profile Photo
                                <span class="font-medium text-[#999086]">(Optional)</span>
                            </p>
                            <p class="sr-only">
                                Optional Rider profile photo.
                            </p>

                            <div
                                id="riderProfileZone"
                                class="rider-profile-zone"
                                role="button"
                                tabindex="0"
                                aria-label="Upload Rider profile photo"
                            >
                                <img id="riderProfilePreviewImage" src="" alt="Selected Rider profile photo" hidden>

                                <div id="riderProfileFallback" class="rider-profile-empty">
                                    <span class="rider-profile-camera" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M4 8h4l1.6-2.5h4.8L16 8h4v10H4z"></path>
                                            <circle cx="12" cy="13" r="3.2"></circle>
                                        </svg>
                                    </span>

                                    <p class="rider-profile-drop-title">Drag photo here</p>
                                    <span class="rider-profile-drop-browse">Browse</span>
                                </div>

                                <div id="riderProfileActions" class="rider-profile-image-actions" hidden>
                                    <button
                                        id="riderProfileBrowse"
                                        type="button"
                                        class="rider-profile-image-action"
                                        aria-label="Change photo"
                                        title="Change photo"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                            <path d="M4 20h4l10.5-10.5a2.8 2.8 0 0 0-4-4L4 16v4Z"></path>
                                            <path d="m13.5 6.5 4 4"></path>
                                        </svg>
                                    </button>
                                    <button
                                        id="riderProfileRemove"
                                        type="button"
                                        class="rider-profile-image-action is-remove"
                                        aria-label="Remove photo"
                                        title="Remove photo"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                            <path d="M4 7h16"></path>
                                            <path d="M9 7V4h6v3"></path>
                                            <path d="m8 10 .6 8h6.8l.6-8"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <input
                                id="riderProfileInput"
                                type="file"
                                name="profile_image"
                                accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                                class="sr-only"
                            >
                            <p id="riderProfileFile" class="sr-only" hidden></p>
                            <p id="riderProfileError" class="rider-profile-error" hidden></p>
                        </div>
                    </section>

                    <section class="rider-section mt-7">
                        <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">02 · Address</p>
                        <h2 class="mt-1 text-[20px] font-bold tracking-[-.025em] text-[#29241f]">Home address</h2>
                        <p class="mt-2 text-[10px] leading-5 text-[#8d8479]">Choose your Philippine address from the directory.</p>

                        <div id="addressStatus" class="mt-4 inline-flex items-center gap-2 rounded-full bg-[#faf8f4] px-3 py-2 text-[9px] font-semibold text-[#867c70]">
                            <span id="addressStatusDot" class="h-1.5 w-1.5 rounded-full bg-[#d29b2d]"></span>
                            <span id="addressStatusText">Loading Philippine address directory...</span>
                        </div>

                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div class="relative">
                                <label class="rider-label">Province / Area *</label>
                                <input id="provinceSearch" autocomplete="off" value="{{ old('province_name') }}" placeholder="Type Laguna, Cebu, Batangas..." class="rider-control">
                                <div id="provinceResults" hidden class="rider-location-list absolute left-0 right-0 top-[78px] z-30 rounded-xl border border-[#e3ddd4] bg-white p-1.5 shadow-[0_16px_38px_rgba(44,38,31,.12)]"></div>
                            </div>
                            <div class="relative">
                                <label class="rider-label">City / Municipality *</label>
                                <input id="municipalitySearch" autocomplete="off" value="{{ old('municipality_name') }}" disabled placeholder="Choose a province first" class="rider-control">
                                <div id="municipalityResults" hidden class="rider-location-list absolute left-0 right-0 top-[78px] z-30 rounded-xl border border-[#e3ddd4] bg-white p-1.5 shadow-[0_16px_38px_rgba(44,38,31,.12)]"></div>
                            </div>
                            <div class="relative">
                                <label class="rider-label">Barangay *</label>
                                <input id="barangaySearch" autocomplete="off" value="{{ old('barangay_name') }}" disabled placeholder="Choose a city / municipality first" class="rider-control">
                                <div id="barangayResults" hidden class="rider-location-list absolute left-0 right-0 top-[78px] z-30 rounded-xl border border-[#e3ddd4] bg-white p-1.5 shadow-[0_16px_38px_rgba(44,38,31,.12)]"></div>
                            </div>
                            <div class="sm:row-span-2">
                                <label class="rider-label">Street / House No. / Subdivision *</label>
                                <textarea name="street_address" required maxlength="500" placeholder="House number, street, subdivision, landmark, etc." class="rider-control">{{ old('street_address') }}</textarea>
                            </div>
                        </div>

                        <div id="manualAddressFallback" hidden class="mt-4 rounded-[14px] border border-[#eadfc9] bg-[#fffaf2] p-4">
                            <p class="text-[10px] font-bold text-[#805d20]">Manual address entry</p>
                            <p class="mt-1 text-[9px] leading-5 text-[#887451]">The online directory could not be reached. You can still complete the application.</p>
                            <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                <input id="manualProvince" placeholder="Province / Area" class="rider-control">
                                <input id="manualMunicipality" placeholder="City / Municipality" class="rider-control">
                                <input id="manualBarangay" placeholder="Barangay" class="rider-control">
                            </div>
                        </div>
                    </section>

                    <section class="rider-section mt-7">
                        <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">03 · Vehicle & Verification</p>
                        <h2 class="mt-1 text-[20px] font-bold tracking-[-.025em] text-[#29241f]">Delivery requirements</h2>
                        <p class="mt-2 text-[10px] leading-5 text-[#8d8479]">Your selected Logistics provider will review these details before approval.</p>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="rider-label">Vehicle *</label>
                                <select name="vehicle_type" required class="rider-control">
                                    <option value="">Choose vehicle</option>
                                    @foreach(['Motorcycle','Bicycle','Car','Van','Truck'] as $vehicle)
                                        <option value="{{ $vehicle }}" {{ old('vehicle_type') === $vehicle ? 'selected' : '' }}>{{ $vehicle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="rider-label">Plate Number *</label>
                                <input id="plateNumber" name="plate_number" value="{{ old('plate_number') }}" required maxlength="30" placeholder="ABC 1234" class="rider-control uppercase">
                            </div>
                            <div>
                                <label class="rider-label">Valid ID / Driver's License *</label>
                                <input type="file" name="id_document" required accept=".jpg,.jpeg,.png,.webp,.pdf" class="block min-h-[50px] w-full rounded-[13px] border border-[#e2dbd1] bg-white px-3 py-3 text-[10px] text-[#484039]">
                                <p class="rider-help">JPG, PNG, WEBP, or PDF. Maximum 5 MB.</p>
                            </div>
                            <div>
                                <label class="rider-label">OR / CR *</label>
                                <input type="file" name="orcr_document" required accept=".jpg,.jpeg,.png,.webp,.pdf" class="block min-h-[50px] w-full rounded-[13px] border border-[#e2dbd1] bg-white px-3 py-3 text-[10px] text-[#484039]">
                                <p class="rider-help">JPG, PNG, WEBP, or PDF. Maximum 8 MB.</p>
                            </div>
                        </div>
                    </section>

                    <section class="rider-section mt-7">
                        <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">04 · Account</p>
                        <h2 class="mt-1 text-[20px] font-bold tracking-[-.025em] text-[#29241f]">Create login credentials</h2>
                        <p class="mt-2 text-[10px] leading-5 text-[#8d8479]">These credentials become usable only after {{ $providerName }} approves your Rider application.</p>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="rider-label">Password *</label>
                                <input type="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Minimum 8 characters" class="rider-control">
                            </div>
                            <div>
                                <label class="rider-label">Confirm Password *</label>
                                <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" placeholder="Repeat password" class="rider-control">
                            </div>
                        </div>

                        <label class="mt-5 flex items-start gap-3 rounded-[14px] bg-[#faf8f4] p-4 text-[10px] leading-5 text-[#746c62]">
                            <input type="checkbox" name="terms" value="1" required class="mt-0.5 h-4 w-4 shrink-0 accent-[#c98a08]">
                            <span>I agree to the <a href="#" class="font-semibold text-[#a96f06]">Terms of Service</a> and <a href="#" class="font-semibold text-[#a96f06]">Privacy Policy</a>.</span>
                        </label>
                    </section>

                    <div class="mt-7 flex flex-col-reverse gap-3 border-t border-[#eee8df] pt-6 sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ route('rider.logistics.index') }}" class="inline-flex h-12 items-center justify-center rounded-xl border border-[#dfd8ce] bg-white px-5 text-[10px] font-bold text-[#625a50]">← Change Logistics</a>
                        <button type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-xl bg-[#d48f08] px-6 text-[10px] font-bold text-white shadow-[0_8px_20px_rgba(212,143,8,.18)] hover:bg-[#bd7d05]">
                            Submit Rider Application →
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('riderApplicationForm');
    const contactNumber = document.getElementById('contactNumber');
    const contactHint = document.getElementById('contactHint');
    const birthday = document.getElementById('birthday');
    const agePreview = document.getElementById('agePreview');
    const middleInitial = document.getElementById('middleInitial');
    const plateNumber = document.getElementById('plateNumber');

    const riderProfileInput = document.getElementById('riderProfileInput');
    const riderProfileZone = document.getElementById('riderProfileZone');
    const riderProfileActions = document.getElementById('riderProfileActions');
    const riderProfileBrowse = document.getElementById('riderProfileBrowse');
    const riderProfileRemove = document.getElementById('riderProfileRemove');
    const riderProfilePreviewImage = document.getElementById('riderProfilePreviewImage');
    const riderProfileFallback = document.getElementById('riderProfileFallback');
    const riderProfileFile = document.getElementById('riderProfileFile');
    const riderProfileError = document.getElementById('riderProfileError');    let riderProfileObjectUrl = null;

    function clearRiderProfileError() {
        riderProfileZone.classList.remove('has-error');
        riderProfileError.hidden = true;
        riderProfileError.textContent = '';
    }

    function showRiderProfileError(message) {
        riderProfileZone.classList.add('has-error');
        riderProfileError.textContent = message;
        riderProfileError.hidden = false;
    }

    function setRiderProfileEmptyState() {
        riderProfileZone.classList.remove('has-image');
        riderProfilePreviewImage.src = '';
        riderProfilePreviewImage.hidden = true;
        riderProfileFallback.hidden = false;
        riderProfileActions.hidden = true;
        riderProfileFile.hidden = true;
        riderProfileFile.textContent = '';
    }

    function resetRiderProfile() {
        if (riderProfileObjectUrl) {
            URL.revokeObjectURL(riderProfileObjectUrl);
            riderProfileObjectUrl = null;
        }

        riderProfileInput.value = '';
        riderProfileInput.setCustomValidity('');
        setRiderProfileEmptyState();
        clearRiderProfileError();
    }

    function validateRiderProfile(file) {
        if (!file) {
            resetRiderProfile();
            return false;
        }

        const extension = String(file.name || '').toLowerCase().split('.').pop();
        const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (
            !allowedExtensions.includes(extension)
            || !allowedTypes.includes(String(file.type || '').toLowerCase())
        ) {
            riderProfileInput.value = '';
            setRiderProfileEmptyState();
            riderProfileInput.setCustomValidity('Choose a JPG, JPEG, PNG, or WEBP image.');
            showRiderProfileError('Choose a JPG, JPEG, PNG, or WEBP image.');
            return false;
        }

        if (file.size > 5 * 1024 * 1024) {
            riderProfileInput.value = '';
            setRiderProfileEmptyState();
            riderProfileInput.setCustomValidity('Profile photo must not be larger than 5 MB.');
            showRiderProfileError('Profile photo must not be larger than 5 MB.');
            return false;
        }

        riderProfileInput.setCustomValidity('');
        clearRiderProfileError();

        if (riderProfileObjectUrl) {
            URL.revokeObjectURL(riderProfileObjectUrl);
        }

        riderProfileObjectUrl = URL.createObjectURL(file);
        riderProfilePreviewImage.src = riderProfileObjectUrl;
        riderProfilePreviewImage.hidden = false;
        riderProfileFallback.hidden = true;
        riderProfileActions.hidden = false;
        riderProfileZone.classList.add('has-image');

        return true;
    }

    function assignRiderDroppedFile(file) {
        try {
            const transfer = new DataTransfer();
            transfer.items.add(file);
            riderProfileInput.files = transfer.files;
            return true;
        } catch (error) {
            console.warn('SARI Rider profile upload: dropped file could not be assigned.', error);
            return false;
        }
    }

    function openRiderProfilePicker() {
        riderProfileInput.click();
    }

    riderProfileZone.addEventListener('click', function (event) {
        if (event.target.closest('.rider-profile-image-actions')) return;
        if (!riderProfileZone.classList.contains('has-image')) {
            openRiderProfilePicker();
        }
    });

    riderProfileZone.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        if (event.target.closest('.rider-profile-image-actions')) return;

        event.preventDefault();

        if (!riderProfileZone.classList.contains('has-image')) {
            openRiderProfilePicker();
        }
    });

    riderProfileBrowse.addEventListener('click', function (event) {
        event.stopPropagation();
        openRiderProfilePicker();
    });

    riderProfileRemove.addEventListener('click', function (event) {
        event.stopPropagation();
        resetRiderProfile();
    });

    riderProfileInput.addEventListener('change', function () {
        validateRiderProfile(riderProfileInput.files?.[0] || null);
    });

    ['dragenter', 'dragover'].forEach(function (eventName) {
        riderProfileZone.addEventListener(eventName, function (event) {
            event.preventDefault();
            event.stopPropagation();
            riderProfileZone.classList.add('is-dragging');
        });
    });

    ['dragleave', 'dragend'].forEach(function (eventName) {
        riderProfileZone.addEventListener(eventName, function (event) {
            event.preventDefault();
            event.stopPropagation();
            riderProfileZone.classList.remove('is-dragging');
        });
    });

    riderProfileZone.addEventListener('drop', function (event) {
        event.preventDefault();
        event.stopPropagation();
        riderProfileZone.classList.remove('is-dragging');

        const files = Array.from(event.dataTransfer?.files || []);

        if (files.length !== 1) {
            showRiderProfileError('Drop one image only.');
            return;
        }

        const file = files[0];

        if (!assignRiderDroppedFile(file)) {
            showRiderProfileError('Use Browse photo to select this image.');
            return;
        }

        validateRiderProfile(riderProfileInput.files?.[0] || null);
    });

    document.querySelectorAll('[data-person-name]').forEach(function (input) {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^\p{L}\s.'-]/gu, '').replace(/\s{2,}/g, ' ');
        });
    });

    middleInitial.addEventListener('input', function () {
        this.value = this.value.replace(/[^\p{L}]/gu, '').slice(0, 1).toUpperCase();
    });

    plateNumber.addEventListener('input', function () {
        this.value = this.value.replace(/[^A-Za-z0-9 -]/g, '').toUpperCase();
    });

    function syncPhone() {
        contactNumber.value = contactNumber.value.replace(/\D/g, '').slice(0, 11);
        const valid = !contactNumber.value || /^09\d{9}$/.test(contactNumber.value);
        contactNumber.setCustomValidity(valid ? '' : 'Enter an 11-digit Philippine mobile number starting with 09.');
        contactHint.textContent = contactNumber.value && valid
            ? 'Valid Philippine mobile number.'
            : 'Exactly 11 digits beginning with 09.';
        contactHint.style.color = contactNumber.value && valid ? '#56816a' : '#989084';
    }

    function calculateAge() {
        if (!birthday.value) {
            agePreview.value = '';
            return;
        }
        const birth = new Date(birthday.value + 'T00:00:00');
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        if (today.getMonth() < birth.getMonth() || (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) age--;
        agePreview.value = Math.max(0, age);
    }

    contactNumber.addEventListener('input', syncPhone);
    birthday.addEventListener('change', calculateAge);

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

    function locationCode(item) { return String(item.code ?? item.psgc_code ?? item.psgcCode ?? item.id ?? ''); }
    function locationName(item) { return String(item.name ?? item.area_name ?? item.areaName ?? item.label ?? ''); }

    function setAddressStatus(type, text) {
        statusText.textContent = text;
        statusDot.className = 'h-1.5 w-1.5 rounded-full ' + (type === 'ready' ? 'bg-[#56816a]' : type === 'error' ? 'bg-[#b25a5a]' : 'bg-[#d29b2d]');
    }

    async function fetchJson(url) {
        const response = await fetch(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        });
        if (!response.ok) throw new Error('Address request failed: ' + response.status);
        return response.json();
    }

    function renderResults(container, items, searchValue, onSelect) {
        container.innerHTML = '';
        const query = searchValue.trim().toLocaleLowerCase();
        const matches = items.filter(item => locationName(item).toLocaleLowerCase().includes(query)).slice(0, 30);

        if (!matches.length) {
            const empty = document.createElement('div');
            empty.className = 'px-3 py-3 text-[10px] text-[#91887c]';
            empty.textContent = 'No matching location found.';
            container.appendChild(empty);
            container.hidden = false;
            return;
        }

        matches.forEach(function (item) {
            const option = document.createElement('button');
            option.type = 'button';
            option.className = 'block w-full rounded-lg px-3 py-3 text-left text-[11px] text-[#403930] hover:bg-[#fffaf1] hover:text-[#9a6817]';
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
            if (!input.disabled) renderResults(container, getItems(), input.value, onSelect);
        });
        input.addEventListener('input', function () {
            if (!input.disabled) renderResults(container, getItems(), input.value, onSelect);
        });
        input.addEventListener('blur', function () {
            window.setTimeout(() => container.hidden = true, 120);
        });
    }

    async function loadProvinces() {
        setAddressStatus('loading', 'Loading Philippine address directory...');
        try {
            provinces = normalizeList(await fetchJson(@json(route('address.provinces'))));
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
            const url = @json(url('/address/provinces')) + '/' + encodeURIComponent(selectedProvinceCode) + '/municipalities';
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
            const url = @json(url('/address/municipalities')) + '/' + encodeURIComponent(selectedMunicipalityCode) + '/barangays';
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

    bindAutocomplete(provinceSearch, provinceResults, () => provinces, selectProvince);
    bindAutocomplete(municipalitySearch, municipalityResults, () => municipalities, selectMunicipality);
    bindAutocomplete(barangaySearch, barangayResults, () => barangays, selectBarangay);

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
        setAddressStatus('error', 'Online address directory unavailable — manual entry enabled.');
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

    [manualProvince, manualMunicipality, manualBarangay].forEach(input => input.addEventListener('input', syncManualAddress));

    form.addEventListener('submit', function (event) {
        syncPhone();
        if (manualMode) syncManualAddress();
        if (!provinceName.value || !municipalityName.value || !barangayName.value) {
            event.preventDefault();
            window.alert('Please select a complete Province / Area, City / Municipality, and Barangay.');
            provinceSearch.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    syncPhone();
    calculateAge();
    loadProvinces();
});
</script>
@endsection
