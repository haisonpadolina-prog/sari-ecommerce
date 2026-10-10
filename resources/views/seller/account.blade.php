@extends('layouts.seller')

@section('title', 'Account Management — SARI Seller')
@section('page-title', 'Account Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/account.css') }}?v=20261007-1">
@endpush

@section('content')
@php
    $fullName = trim(collect([
        $account->first_name,
        $account->middle_initial ? $account->middle_initial . '.' : null,
        $account->last_name,
    ])->filter()->implode(' '));

    $fullName = $fullName !== ''
        ? $fullName
        : ($account->store_name ?: 'SARI Seller');

    $initials = collect(preg_split('/\s+/', $fullName) ?: [])
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('') ?: 'SS';

    $registrationAddress = collect([
        $account->street_address,
        $account->barangay_name,
        $account->municipality_name,
        $account->province_name,
    ])->filter()->implode(', ');

    $accountStatus = strtolower((string) ($account->account_status ?: 'active'));
    $registrationStatus = strtolower((string) ($account->registration_status ?: 'approved'));
    $sexValue = old('sex', $account->sex);
@endphp



<div class="seller-account-enterprise">
    @if(session('success'))
        <div class="sa-alert sa-alert--success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="sa-alert sa-alert--error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <header class="sa-page-head">
        <div>
            <p class="sa-eyebrow">Seller Identity & Security</p>
            <h1 class="sa-page-title">Account <span>Management</span></h1>
            <p class="sa-page-copy">
                Manage your Seller identity, contact details, account address, profile photo, verification documents, and account security.
            </p>
        </div>
    </header>

    <section class="sa-profile-strip">
        <div class="sa-profile-main">
            <div class="sa-avatar">
                @if($profilePhotoUrl)
                    <img
                        id="sellerHeroAvatarImage"
                        src="{{ $profilePhotoUrl }}"
                        alt="{{ $fullName }} profile photo"
                        loading="eager"
                        decoding="async"
                    >
                    <span id="sellerHeroAvatarFallback" hidden>{{ $initials }}</span>
                @else
                    <img
                        id="sellerHeroAvatarImage"
                        src=""
                        alt="{{ $fullName }} profile photo"
                        hidden
                    >
                    <span id="sellerHeroAvatarFallback">{{ $initials }}</span>
                @endif
            </div>

            <div class="min-w-0">
                <h2 class="sa-profile-name">{{ $fullName }}</h2>
                <div class="sa-profile-meta">
                    <span>{{ $account->email }}</span>
                    <span class="sa-meta-dot"></span>
                    <span>{{ $account->store_name ?: 'Store name not set' }}</span>
                </div>
            </div>
        </div>

        <div class="sa-statuses">
            <span class="sa-status sa-status--active">
                <span class="sa-status__dot"></span>
                {{ ucfirst($accountStatus) }}
            </span>

            <span class="sa-status sa-status--registration">
                Registration {{ ucfirst($registrationStatus) }}
            </span>
        </div>
    </section>

    <div class="sa-layout">
        <main class="sa-stack">
            <form
                method="POST"
                action="{{ route('seller.account.update') }}"
                enctype="multipart/form-data"
                class="sa-panel"
                id="sellerAccountForm"
            >
                @csrf
                @method('PATCH')

                <div class="sa-panel__head">
                    <div>
                        <h2 class="sa-panel__title">Personal Information</h2>
                        <p class="sa-panel__copy">
                            Seller identity and contact details attached to your account.
                        </p>
                    </div>
                </div>

                <div class="sa-panel__body">
                    <div class="sa-photo-editor">
                        <div class="sa-photo">
                            @if($profilePhotoUrl)
                                <img
                                    id="sellerProfilePreview"
                                    src="{{ $profilePhotoUrl }}"
                                    alt="Current profile photo"
                                    loading="eager"
                                    decoding="async"
                                >
                                <span id="sellerProfileFallback" hidden>{{ $initials }}</span>
                            @else
                                <img
                                    id="sellerProfilePreview"
                                    src=""
                                    alt="Selected profile preview"
                                    hidden
                                >
                                <span id="sellerProfileFallback">{{ $initials }}</span>
                            @endif
                        </div>

                        <div>
                            <p class="sa-label">Profile photo</p>
                            <p class="sa-help" style="margin-top:0;">
                                JPG, PNG, or WEBP · maximum 5 MB. Your approved registration photo
                                remains available as Seller identity context.
                            </p>

                            <div class="sa-photo-actions">
                                <label
                                    for="sellerProfileImage"
                                    class="sa-icon-action sa-icon-action--edit"
                                    title="Edit profile photo"
                                    aria-label="Edit profile photo"
                                >
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                    </svg>
                                    <span class="sa-sr-only">Edit profile photo</span>
                                </label>

                                <button
                                    type="button"
                                    id="sellerProfileRemoveButton"
                                    class="sa-icon-action sa-icon-action--delete"
                                    title="Remove profile photo"
                                    aria-label="Remove profile photo"
                                    @if(!$profilePhotoUrl) hidden @endif
                                >
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M3 6h18"></path>
                                        <path d="M8 6V4h8v2"></path>
                                        <path d="M19 6l-1 14H6L5 6"></path>
                                        <path d="M10 11v5"></path>
                                        <path d="M14 11v5"></path>
                                    </svg>
                                </button>
                            </div>

                            <input
                                id="sellerProfileImage"
                                type="file"
                                name="profile_image"
                                accept="image/jpeg,image/png,image/webp"
                                class="sa-file-input"
                                data-has-original="{{ $profilePhotoUrl ? '1' : '0' }}"
                            >

                            <input
                                id="sellerRemoveProfileImage"
                                type="hidden"
                                name="remove_profile_image"
                                value="0"
                            >
                        </div>
                    </div>

                    <div class="sa-grid">
                        <div class="sa-field">
                            <label for="sellerFirstName">First name</label>
                            <input
                                id="sellerFirstName"
                                name="first_name"
                                required
                                maxlength="60"
                                autocomplete="given-name"
                                data-name-only
                                value="{{ old('first_name', $account->first_name) }}"
                                class="sa-control"
                            >
                            <p class="sa-help">Letters only · maximum 60 characters.</p>
                        </div>

                        <div class="sa-field">
                            <label for="sellerLastName">Last name</label>
                            <input
                                id="sellerLastName"
                                name="last_name"
                                required
                                maxlength="60"
                                autocomplete="family-name"
                                data-name-only
                                value="{{ old('last_name', $account->last_name) }}"
                                class="sa-control"
                            >
                            <p class="sa-help">Letters only · maximum 60 characters.</p>
                        </div>

                        <div class="sa-field">
                            <label for="sellerMiddleInitial">Middle initial</label>
                            <input
                                id="sellerMiddleInitial"
                                name="middle_initial"
                                maxlength="1"
                                data-middle-initial"
                                value="{{ old('middle_initial', $account->middle_initial) }}"
                                class="sa-control"
                            >
                            <p class="sa-help">One letter only.</p>
                        </div>

                        <div class="sa-field">
                            <label for="sellerSex">Sex</label>

                            <div class="sa-sex" id="sellerSexControl">
                                <select
                                    id="sellerSex"
                                    name="sex"
                                    required
                                    class="sa-control"
                                >
                                    <option value="Male" @selected($sexValue === 'Male')>Male</option>
                                    <option value="Female" @selected($sexValue === 'Female')>Female</option>
                                </select>

                                <button
                                    type="button"
                                    class="sa-sex__button"
                                    id="sellerSexButton"
                                    aria-haspopup="listbox"
                                    aria-expanded="false"
                                >
                                    <span id="sellerSexValue">{{ $sexValue ?: 'Select' }}</span>
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="m7 10 5 5 5-5"></path>
                                    </svg>
                                </button>

                                <div
                                    class="sa-sex__menu"
                                    id="sellerSexMenu"
                                    role="listbox"
                                    hidden
                                >
                                    <button
                                        type="button"
                                        class="sa-sex__option {{ $sexValue === 'Male' ? 'is-selected' : '' }}"
                                        data-sex-option="Male"
                                        role="option"
                                        aria-selected="{{ $sexValue === 'Male' ? 'true' : 'false' }}"
                                    >
                                        Male
                                    </button>

                                    <button
                                        type="button"
                                        class="sa-sex__option {{ $sexValue === 'Female' ? 'is-selected' : '' }}"
                                        data-sex-option="Female"
                                        role="option"
                                        aria-selected="{{ $sexValue === 'Female' ? 'true' : 'false' }}"
                                    >
                                        Female
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="sa-field">
                            <label for="sellerContact">Contact number</label>
                            <input
                                id="sellerContact"
                                name="contact_no"
                                required
                                inputmode="numeric"
                                autocomplete="tel"
                                maxlength="11"
                                minlength="11"
                                pattern="09[0-9]{9}"
                                aria-describedby="sellerContactHelp"
                                data-digits-only
                                value="{{ old('contact_no', $account->contact_no) }}"
                                class="sa-control"
                            >
                            <p id="sellerContactHelp" class="sa-help">
                                11 digits only · must start with 09.
                                <span id="sellerContactCounter">0 / 11</span>
                            </p>
                        </div>

                        <div class="sa-field">
                            <label for="sellerBirthday">Birthday</label>
                            <input
                                id="sellerBirthday"
                                type="date"
                                name="birthday"
                                required
                                value="{{ old('birthday', optional($account->birthday)->format('Y-m-d') ?: $account->getRawOriginal('birthday')) }}"
                                class="sa-control"
                            >
                        </div>
                    </div>

                    <div class="sa-subsection">
                        <div class="sa-subsection__head">
                            <div>
                                <h3>Account Address</h3>
                                <p>
                                    Current address details linked to this Seller account.
                                    Store pickup location remains separate in Store Management.
                                </p>
                            </div>
                        </div>

                        <div class="sa-grid">
                            <div class="sa-field sa-field--full">
                                <label for="sellerStreetAddress">Street / building / subdivision</label>
                                <textarea
                                    id="sellerStreetAddress"
                                    name="street_address"
                                    rows="3"
                                    maxlength="300"
                                    required
                                    class="sa-control sa-control--textarea"
                                >{{ old('street_address', $account->street_address) }}</textarea>
                                <p class="sa-help">Maximum 300 characters.</p>
                            </div>

                            <div class="sa-field">
                                <label for="sellerBarangay">Barangay</label>
                                <input
                                    id="sellerBarangay"
                                    name="barangay_name"
                                    maxlength="100"
                                    required
                                    value="{{ old('barangay_name', $account->barangay_name) }}"
                                    class="sa-control"
                                >
                            </div>

                            <div class="sa-field">
                                <label for="sellerMunicipality">City / Municipality</label>
                                <input
                                    id="sellerMunicipality"
                                    name="municipality_name"
                                    maxlength="100"
                                    required
                                    value="{{ old('municipality_name', $account->municipality_name) }}"
                                    class="sa-control"
                                >
                            </div>

                            <div class="sa-field">
                                <label for="sellerProvince">Province</label>
                                <input
                                    id="sellerProvince"
                                    name="province_name"
                                    maxlength="100"
                                    required
                                    value="{{ old('province_name', $account->province_name) }}"
                                    class="sa-control"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="sa-form-footer">
                        <p class="sa-form-note">
                            Saving updates your Seller identity, address, profile photo, and any
                            replacement verification files selected below.
                        </p>

                        <button type="submit" class="sa-primary">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M5 12.5 9.2 17 19 7"></path>
                            </svg>
                            Save personal information
                        </button>
                    </div>
                </div>

            <section class="sa-panel">
                <div class="sa-panel__head">
                    <div>
                        <h2 class="sa-panel__title">Verification Documents</h2>
                        <p class="sa-panel__copy">
                            View or replace the verification files associated with your Seller account. Replacements remain private.
                        </p>
                    </div>
                </div>

                <div class="sa-doc-list">
                    <div class="sa-doc-row">
                        <div class="sa-doc-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                                <path d="M8 8h8"></path>
                                <path d="M8 12h8"></path>
                                <path d="M8 16h5"></path>
                            </svg>
                        </div>

                        <div>
                            <p class="sa-doc-name">Government Valid ID</p>
                            <p class="sa-doc-copy">Identity requirement submitted with Seller registration.</p>
                        </div>

                        <span class="sa-doc-state {{ $documentAvailability['id'] ? 'sa-doc-state--available' : 'sa-doc-state--missing' }}">
                            {{ $documentAvailability['id'] ? 'Available' : 'Missing' }}
                        </span>

                        <div class="sa-doc-actions">
                            @if($documentAvailability['id'])
                                <a
                                    href="{{ route('seller.account.documents.show', 'id') }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="sa-doc-link"
                                >
                                    View
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M7 17 17 7"></path>
                                        <path d="M8 7h9v9"></path>
                                    </svg>
                                </a>
                            @endif

                            <label
                                for="sellerGovernmentId"
                                class="sa-icon-action sa-icon-action--edit"
                                title="Replace Government ID"
                                aria-label="Replace Government ID"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                </svg>
                                <span class="sa-sr-only">Replace Government ID</span>
                            </label>

                            <input
                                id="sellerGovernmentId"
                                type="file"
                                name="government_id"
                                accept="application/pdf,image/jpeg,image/png,image/webp"
                                class="sa-file-input"
                                data-document-input="government-id"
                            >
                        </div>

                        <p class="sa-doc-selected" data-document-name="government-id">
                            No replacement selected
                        </p>
                    </div>

                    <div class="sa-doc-row">
                        <div class="sa-doc-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                                <path d="M8 8h8"></path>
                                <path d="M8 12h8"></path>
                                <path d="M8 16h5"></path>
                            </svg>
                        </div>

                        <div>
                            <p class="sa-doc-name">Business Permit</p>
                            <p class="sa-doc-copy">Business requirement used for Seller approval.</p>
                        </div>

                        <span class="sa-doc-state {{ $documentAvailability['permit'] ? 'sa-doc-state--available' : 'sa-doc-state--missing' }}">
                            {{ $documentAvailability['permit'] ? 'Available' : 'Missing' }}
                        </span>

                        <div class="sa-doc-actions">
                            @if($documentAvailability['permit'])
                                <a
                                    href="{{ route('seller.account.documents.show', 'permit') }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="sa-doc-link"
                                >
                                    View
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M7 17 17 7"></path>
                                        <path d="M8 7h9v9"></path>
                                    </svg>
                                </a>
                            @endif

                            <label
                                for="sellerBusinessPermit"
                                class="sa-icon-action sa-icon-action--edit"
                                title="Replace Business Permit"
                                aria-label="Replace Business Permit"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                </svg>
                                <span class="sa-sr-only">Replace Business Permit</span>
                            </label>

                            <input
                                id="sellerBusinessPermit"
                                type="file"
                                name="business_permit"
                                accept="application/pdf,image/jpeg,image/png,image/webp"
                                class="sa-file-input"
                                data-document-input="business-permit"
                            >
                        </div>

                        <p class="sa-doc-selected" data-document-name="business-permit">
                            No replacement selected
                        </p>
                    </div>
                </div>

                <div class="sa-address">
                    <strong>Verification file rules</strong>
                    <p>
                        PDF, JPG, PNG, or WEBP · maximum 10 MB each.
                        Selecting a new file replaces the active account copy after a successful save.
                    </p>
                    <p>
                        Original registration evidence is preserved when the current file came from the registration record.
                    </p>
                </div>

                <div class="sa-verification-footer">
                    <span>Only selected verification files will be replaced.</span>
                    <button type="submit" class="sa-primary">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 12.5 9.2 17 19 7"></path>
                        </svg>
                        Save verification changes
                    </button>
                </div>
            </section>
            </form>
        </main>

        <aside class="sa-stack sa-side">
            <section class="sa-panel">
                <div class="sa-panel__head">
                    <div>
                        <h2 class="sa-panel__title">Account Overview</h2>
                        <p class="sa-panel__copy">Protected Seller account information.</p>
                    </div>
                </div>

                <dl class="sa-detail-list">
                    <div class="sa-detail-row">
                        <dt>Login email</dt>
                        <dd>{{ $account->email }}</dd>
                    </div>

                    <div class="sa-detail-row">
                        <dt>Seller ID</dt>
                        <dd>SLR-{{ str_pad((string) $account->id, 6, '0', STR_PAD_LEFT) }}</dd>
                    </div>

                    <div class="sa-detail-row">
                        <dt>Line of business</dt>
                        <dd>{{ $account->line_of_business ?: '—' }}</dd>
                    </div>

                    <div class="sa-detail-row">
                        <dt>Approved</dt>
                        <dd>{{ optional($account->approved_at)->format('M d, Y') ?: '—' }}</dd>
                    </div>
                </dl>

                <div class="sa-info-note">
                    Login email and approved business classification are protected fields.
                    Contact SARI Administration if verified information requires correction.
                </div>
            </section>

            <form
                method="POST"
                action="{{ route('seller.account.password.update') }}"
                class="sa-panel"
            >
                @csrf
                @method('PATCH')

                <div class="sa-panel__head">
                    <div>
                        <h2 class="sa-panel__title">Password & Security</h2>
                        <p class="sa-panel__copy">Update the password used to access your Seller account.</p>
                    </div>
                </div>

                <div class="sa-panel__body">
                    <div class="sa-password-stack">
                        <div class="sa-password-wrap">
                            <label class="sa-label" for="currentPassword">Current password</label>
                            <input
                                id="currentPassword"
                                type="password"
                                name="current_password"
                                autocomplete="current-password"
                                required
                                class="sa-control"
                            >
                            <button
                                type="button"
                                class="sa-password-toggle"
                                data-password-toggle="currentPassword"
                                aria-label="Show current password"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>
                            </button>
                            @error('current_password', 'password')
                                <p class="sa-help" style="color:#a65353;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sa-password-wrap">
                            <label class="sa-label" for="newPassword">New password</label>
                            <input
                                id="newPassword"
                                type="password"
                                name="password"
                                autocomplete="new-password"
                                required
                                minlength="8"
                                class="sa-control"
                            >
                            <button
                                type="button"
                                class="sa-password-toggle"
                                data-password-toggle="newPassword"
                                aria-label="Show new password"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>
                            </button>
                            @error('password', 'password')
                                <p class="sa-help" style="color:#a65353;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sa-password-wrap">
                            <label class="sa-label" for="newPasswordConfirmation">Confirm new password</label>
                            <input
                                id="newPasswordConfirmation"
                                type="password"
                                name="password_confirmation"
                                autocomplete="new-password"
                                required
                                minlength="8"
                                class="sa-control"
                            >
                            <button
                                type="button"
                                class="sa-password-toggle"
                                data-password-toggle="newPasswordConfirmation"
                                aria-label="Show password confirmation"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="sa-primary" style="width:100%;margin-top:12px;">
                        Update password
                    </button>
                </div>
            </form>

            <section class="sa-panel">
                <div class="sa-panel__body">
                    <p class="sa-panel__title">Store Operations</p>
                    <p class="sa-panel__copy" style="margin-top:5px;">
                        Public Store identity, contacts, Open/Paused status, pickup address,
                        and fulfillment instructions are managed separately.
                    </p>

                    <a
                        href="{{ route('seller.store.index') }}"
                        wire:navigate.hover
                        class="sa-store-link"
                    >
                        <span>Open Store Management</span>
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 12h14"></path>
                            <path d="m13 6 6 6-6 6"></path>
                        </svg>
                    </a>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function initSellerAccountEnterprise() {
        const form = document.getElementById('sellerAccountForm');
        if (!form || form.dataset.enterpriseBound === '1') return;
        form.dataset.enterpriseBound = '1';

        window.__SARI_SELLER_ACCOUNT_LIFECYCLE__?.abort();

        const lifecycle = new AbortController();
        const { signal } = lifecycle;

        window.__SARI_SELLER_ACCOUNT_LIFECYCLE__ = lifecycle;

        const profileInput = document.getElementById('sellerProfileImage');
        const removeInput = document.getElementById('sellerRemoveProfileImage');
        const removeButton = document.getElementById('sellerProfileRemoveButton');
        const profilePreview = document.getElementById('sellerProfilePreview');
        const profileFallback = document.getElementById('sellerProfileFallback');
        const heroPreview = document.getElementById('sellerHeroAvatarImage');
        const heroFallback = document.getElementById('sellerHeroAvatarFallback');

        let profileObjectUrl = null;

        const revokeProfileUrl = () => {
            if (profileObjectUrl) {
                URL.revokeObjectURL(profileObjectUrl);
                profileObjectUrl = null;
            }
        };


        const firstNameInput = document.getElementById('sellerFirstName');
        const lastNameInput = document.getElementById('sellerLastName');
        const middleInitialInput = document.getElementById('sellerMiddleInitial');
        const contactInput = document.getElementById('sellerContact');
        const contactCounter = document.getElementById('sellerContactCounter');

        const cleanName = (value, maxLength) => {
            try {
                return value
                    .replace(/[^\p{L}\s.'-]/gu, '')
                    .slice(0, maxLength);
            } catch (error) {
                return value
                    .replace(/[0-9]/g, '')
                    .slice(0, maxLength);
            }
        };

        [firstNameInput, lastNameInput].forEach((input) => {
            input?.addEventListener('input', () => {
                const cleaned = cleanName(input.value, 60);

                if (input.value !== cleaned) {
                    input.value = cleaned;
                }
            });
        });

        middleInitialInput?.addEventListener('input', () => {
            const cleaned = cleanName(
                middleInitialInput.value,
                1
            )
                .replace(/[\s.'-]/g, '')
                .slice(0, 1);

            middleInitialInput.value = cleaned;
        });

        const syncContact = () => {
            if (!contactInput) return;

            const digits = contactInput.value
                .replace(/\D/g, '')
                .slice(0, 11);

            contactInput.value = digits;

            if (contactCounter) {
                contactCounter.textContent =
                    `${digits.length} / 11`;
            }

            const valid =
                digits.length === 0
                || /^09\d{9}$/.test(digits);

            contactInput.setCustomValidity(
                valid
                    ? ''
                    : 'Use exactly 11 digits starting with 09.'
            );
        };

        contactInput?.addEventListener('input', syncContact);
        syncContact();

        document
            .querySelectorAll('[data-document-input]')
            .forEach((input) => {
                input.addEventListener('change', () => {
                    const key = input.dataset.documentInput;
                    const output = document.querySelector(
                        `[data-document-name="${key}"]`
                    );

                    if (!output) return;

                    const file = input.files?.[0];

                    output.textContent = file
                        ? `Ready to replace: ${file.name}`
                        : 'No replacement selected';

                    output.classList.toggle(
                        'is-ready',
                        Boolean(file)
                    );
                });
            });

        profileInput?.addEventListener('change', function () {
            const file = this.files?.[0];

            if (!file || !file.type.startsWith('image/')) return;

            revokeProfileUrl();
            profileObjectUrl = URL.createObjectURL(file);

            if (profilePreview) {
                profilePreview.src = profileObjectUrl;
                profilePreview.hidden = false;
            }

            if (heroPreview) {
                heroPreview.src = profileObjectUrl;
                heroPreview.hidden = false;
            }

            profileFallback?.setAttribute('hidden', '');
            heroFallback?.setAttribute('hidden', '');

            if (removeInput) {
                removeInput.value = '0';
            }

            removeButton?.removeAttribute('hidden');
        });

        removeButton?.addEventListener('click', () => {
            revokeProfileUrl();

            if (profileInput) {
                profileInput.value = '';
            }

            const hadOriginal =
                profileInput?.dataset.hasOriginal === '1';

            if (removeInput) {
                removeInput.value = hadOriginal ? '1' : '0';
            }

            if (profilePreview) {
                profilePreview.hidden = true;
            }

            if (heroPreview) {
                heroPreview.hidden = true;
            }

            profileFallback?.removeAttribute('hidden');
            heroFallback?.removeAttribute('hidden');
            removeButton.hidden = true;
        });

        const sexWrap = document.getElementById('sellerSexControl');
        const sexSelect = document.getElementById('sellerSex');
        const sexButton = document.getElementById('sellerSexButton');
        const sexMenu = document.getElementById('sellerSexMenu');
        const sexValue = document.getElementById('sellerSexValue');
        const sexOptions = Array.from(
            document.querySelectorAll('[data-sex-option]')
        );

        const syncSex = () => {
            if (!sexSelect || !sexValue) return;

            sexValue.textContent = sexSelect.value || 'Select';

            sexOptions.forEach((option) => {
                const selected =
                    option.dataset.sexOption === sexSelect.value;

                option.classList.toggle('is-selected', selected);
                option.setAttribute(
                    'aria-selected',
                    selected ? 'true' : 'false'
                );
            });
        };

        const closeSex = () => {
            if (!sexWrap || !sexButton || !sexMenu) return;

            sexWrap.classList.remove('is-open');
            sexButton.setAttribute('aria-expanded', 'false');
            sexMenu.hidden = true;
        };

        const openSex = () => {
            if (!sexWrap || !sexButton || !sexMenu) return;

            sexWrap.classList.add('is-open');
            sexButton.setAttribute('aria-expanded', 'true');
            sexMenu.hidden = false;

            requestAnimationFrame(() => {
                sexMenu.querySelector('.is-selected')
                    ?.focus({ preventScroll: true });
            });
        };

        if (sexWrap && sexSelect && sexButton && sexMenu) {
            sexWrap.classList.add('is-ready');
            syncSex();

            sexButton.addEventListener('click', () => {
                if (sexWrap.classList.contains('is-open')) {
                    closeSex();
                } else {
                    openSex();
                }
            });

            sexOptions.forEach((option) => {
                option.addEventListener('click', () => {
                    sexSelect.value =
                        option.dataset.sexOption || '';

                    sexSelect.dispatchEvent(
                        new Event('change', { bubbles: true })
                    );

                    syncSex();
                    closeSex();
                    sexButton.focus({ preventScroll: true });
                });
            });

            sexWrap.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeSex();
                    sexButton.focus({ preventScroll: true });
                }
            });

            document.addEventListener('click', (event) => {
                if (!sexWrap.contains(event.target)) {
                    closeSex();
                }
            }, { signal });
        }

        document
            .querySelectorAll('[data-password-toggle]')
            .forEach((button) => {
                button.addEventListener('click', () => {
                    const input = document.getElementById(
                        button.dataset.passwordToggle
                    );

                    if (!input) return;

                    const showing = input.type === 'text';
                    input.type = showing ? 'password' : 'text';

                    button.setAttribute(
                        'aria-label',
                        showing ? 'Show password' : 'Hide password'
                    );
                });
            });

        const cleanup = () => {
            revokeProfileUrl();
            form.dataset.enterpriseBound = '0';
            lifecycle.abort();

            if (window.__SARI_SELLER_ACCOUNT_LIFECYCLE__ === lifecycle) {
                window.__SARI_SELLER_ACCOUNT_LIFECYCLE__ = null;
            }
        };

        document.addEventListener(
            'livewire:navigating',
            cleanup,
            { once: true, signal }
        );

        window.addEventListener('beforeunload', cleanup, { once: true, signal });
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            initSellerAccountEnterprise,
            { once: true }
        );
    } else {
        initSellerAccountEnterprise();
    }

})();
</script>
@endpush
