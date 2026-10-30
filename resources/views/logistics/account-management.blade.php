@extends('layouts.logistics')

@section('title', 'Account Management | SARI Logistics')
@section('page-title', 'Account Management')

@push('styles')
    <style>
/* =========================================================
   MERGED FROM public/css/logistics/account-management.css
   Kept page-scoped/shared exactly as the original stylesheet.
========================================================= */
/*
|--------------------------------------------------------------------------
| SARI LOGISTICS — ACCOUNT MANAGEMENT
|--------------------------------------------------------------------------
|
| Seller-aligned account/profile workspace.
|
*/


/* ==========================================================================
   PAGE
   ========================================================================== */

.account-page {
    --account-ink: #1f2937;
    --account-text: #344054;
    --account-muted: #98a2b3;

    --account-line: #e5e7eb;
    --account-soft-line: #edf0f3;

    --account-gold: #d59617;
    --account-gold-hover: #c9890f;
    --account-gold-soft: #fffaf0;

    --account-canvas: #f7f8fa;
    --account-white: #ffffff;

    width: 100%;
    max-width: 1500px;

    margin-inline: auto;
    padding-bottom: 24px;

    color: var(--account-text);

    font-family:
        "Poppins",
        ui-sans-serif,
        system-ui,
        sans-serif;
}


.account-page *,
.account-page *::before,
.account-page *::after {
    box-sizing: border-box;
}


/* ==========================================================================
   HEADER
   ========================================================================== */

.account-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 24px;

    margin-bottom: 14px;
    padding: 18px 20px;

    border: 1px solid var(--account-line);
    border-radius: 18px;

    background: #ffffff;

    box-shadow:
        0 4px 14px
        rgba(15, 23, 42, .025);
}


.account-eyebrow {
    margin: 0 0 4px;

    color: #a8731f;

    font-size: 7px;
    font-weight: 700;

    letter-spacing: .12em;
    text-transform: uppercase;
}


.account-page-header h1 {
    margin: 0;

    color: var(--account-ink);

    font-size: 21px;
    line-height: 1.2;
    font-weight: 700;

    letter-spacing: -.025em;
}


.account-subtitle {
    max-width: 680px;

    margin: 6px 0 0;

    color: #7c8592;

    font-size: 8.5px;
    line-height: 1.65;
}


/* ==========================================================================
   HEADER IDENTITY
   ========================================================================== */

.account-header-identity {
    display: flex;
    align-items: center;

    min-width: 240px;

    gap: 10px;

    padding: 10px 12px;

    border: 1px solid var(--account-line);
    border-radius: 12px;

    background: #fafbfc;
}


.account-header-avatar {
    display: grid;

    width: 38px;
    height: 38px;

    flex: 0 0 38px;

    place-items: center;

    border: 1px solid #efd9a7;
    border-radius: 11px;

    background: var(--account-gold-soft);

    color: #a96f06;

    font-size: 9px;
    font-weight: 800;
}


.account-header-identity > div {
    min-width: 0;
}


.account-header-identity span:not(
    .account-header-avatar
) {
    display: block;

    color: #98a2b3;

    font-size: 6px;
    font-weight: 700;

    letter-spacing: .08em;
}


.account-header-identity strong {
    display: block;

    margin-top: 2px;

    overflow: hidden;

    color: #344054;

    font-size: 8.5px;
    font-weight: 700;

    text-overflow: ellipsis;
    white-space: nowrap;
}


.account-header-identity p {
    margin: 1px 0 0;

    overflow: hidden;

    color: #98a2b3;

    font-size: 7px;

    text-overflow: ellipsis;
    white-space: nowrap;
}


/* ==========================================================================
   ALERTS
   ========================================================================== */

.account-alert {
    margin-bottom: 14px;
    padding: 12px 14px;

    border: 1px solid;
    border-radius: 12px;
}


.account-alert strong {
    display: block;

    font-size: 9px;
    font-weight: 700;
}


.account-alert p {
    margin: 3px 0 0;

    font-size: 8px;
}


.account-alert ul {
    margin: 5px 0 0;
    padding-left: 17px;

    font-size: 8px;
}


.account-alert-success {
    border-color: #dce7df;

    background: #f4f9f6;

    color: #4f7d63;
}


.account-alert-danger {
    border-color: #efcece;

    background: #fff5f5;

    color: #a65353;
}


/* ==========================================================================
   FORM LAYOUT
   ========================================================================== */

.account-layout {
    display: grid;

    grid-template-columns:
        minmax(0, 1.55fr)
        minmax(300px, .7fr);

    align-items: start;

    gap: 14px;
}


.account-main-column,
.account-side-column {
    display: grid;

    gap: 14px;
}


/* ==========================================================================
   CARD
   ========================================================================== */

.account-card {
    overflow: hidden;

    border: 1px solid var(--account-line);
    border-radius: 17px;

    background: #ffffff;

    box-shadow:
        0 4px 14px
        rgba(15, 23, 42, .025);
}


.account-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 14px;

    padding:
        15px
        17px;

    border-bottom: 1px solid var(--account-soft-line);
}


.account-card-header h2 {
    margin: 0;

    color: var(--account-ink);

    font-size: 12px;
    font-weight: 700;
}


.account-card-header p:not(
    .account-eyebrow
) {
    margin: 3px 0 0;

    color: #98a2b3;

    font-size: 7.5px;
    line-height: 1.5;
}


.account-card-body {
    padding:
        17px;
}


/* ==========================================================================
   PROFILE PHOTO
   ========================================================================== */

.account-photo-section {
    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        220px;

    align-items: center;

    gap: 20px;

    padding: 17px;
}


.account-photo-component {
    min-width: 0;
}


/*
|--------------------------------------------------------------------------
| Shared profile-photo component normalization
|--------------------------------------------------------------------------
*/

.account-photo-component input[type="file"] {
    max-width: 100%;
}


.account-photo-note {
    padding: 11px 12px;

    border: 1px solid #e8e1d6;
    border-radius: 10px;

    background: #fffaf0;
}


.account-photo-note strong {
    display: block;

    color: #7f632d;

    font-size: 7.5px;
    font-weight: 700;
}


.account-photo-note p {
    margin: 3px 0 0;

    color: #a18d69;

    font-size: 6.5px;
    line-height: 1.5;
}


/* ==========================================================================
   FIELDS
   ========================================================================== */

.account-fields-grid {
    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 14px;
}


.account-field {
    display: block;

    min-width: 0;
}


.account-field-wide {
    grid-column: 1 / -1;
}


.account-field > span {
    display: block;

    margin-bottom: 6px;

    color: #667085;

    font-size: 7.5px;
    font-weight: 600;
}


.account-field input,
.account-field textarea {
    width: 100%;

    border: 1px solid #d8dee6;
    border-radius: 10px;

    background: #ffffff;

    color: #344054;

    font-size: 9px;

    outline: none;

    transition:
        border-color .15s ease,
        box-shadow .15s ease,
        background-color .15s ease;
}


.account-field input {
    height: 41px;

    padding:
        0
        11px;
}


.account-field textarea {
    min-height: 78px;

    resize: vertical;

    padding:
        10px
        11px;

    line-height: 1.55;
}


.account-field input:hover,
.account-field textarea:hover {
    border-color: #cdd4dd;
}


.account-field input:focus,
.account-field textarea:focus {
    border-color: var(--account-gold);

    background: #ffffff;

    box-shadow:
        0 0 0 3px
        rgba(213, 150, 23, .08);
}


.account-field-help {
    display: block;

    margin-top: 5px;

    color: #98a2b3;

    font-size: 6.5px;
    line-height: 1.45;
}


.account-field-error {
    display: block;

    margin-top: 5px;

    color: #b35b5b;

    font-size: 6.8px;
    font-weight: 600;
}


/* ==========================================================================
   ACCOUNT SUMMARY
   ========================================================================== */

.account-summary-card {
    padding:
        18px
        16px
        15px;
}


.account-summary-profile {
    text-align: center;
}


.account-summary-avatar {
    display: grid;

    width: 58px;
    height: 58px;

    place-items: center;

    margin:
        0
        auto;

    border: 1px solid #efd9a7;
    border-radius: 16px;

    background: #fffaf0;

    color: #a96f06;

    font-size: 13px;
    font-weight: 800;

    box-shadow:
        0 6px 16px
        rgba(213, 150, 23, .07);
}


.account-summary-profile h2 {
    margin:
        10px
        0
        0;

    color: #1f2937;

    font-size: 12px;
    font-weight: 700;
}


.account-summary-profile p {
    margin: 2px 0 0;

    color: #98a2b3;

    font-size: 7px;
}


.account-summary-list {
    display: grid;

    gap: 8px;

    margin-top: 16px;
}


.account-summary-list > div {
    padding: 10px;

    border: 1px solid #e5e7eb;
    border-radius: 10px;

    background: #fafbfc;
}


.account-summary-list span {
    display: block;

    color: #98a2b3;

    font-size: 6px;
    font-weight: 700;

    letter-spacing: .07em;
}


.account-summary-list strong {
    display: block;

    margin-top: 3px;

    color: #344054;

    font-size: 8px;
    font-weight: 700;

    overflow-wrap: anywhere;
}


/* ==========================================================================
   SECURITY
   ========================================================================== */

.account-security-fields {
    display: grid;

    gap: 13px;
}


.account-password-field {
    position: relative;
}


.account-password-field input {
    padding-right: 52px;
}


.account-password-field button {
    position: absolute;

    right: 5px;
    top: 50%;

    min-width: 42px;
    height: 30px;

    transform: translateY(-50%);

    border: 0;
    border-radius: 7px;

    background: #f6f7f9;

    color: #667085;

    font-size: 6.5px;
    font-weight: 700;

    cursor: pointer;
}


.account-password-field button:hover {
    background: #fff7e7;

    color: #9b6505;
}


/* ==========================================================================
   SAVE CARD
   ========================================================================== */

.account-save-card {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 14px;

    padding: 14px;

    border: 1px solid #eadfc9;
    border-radius: 14px;

    background:
        linear-gradient(
            135deg,
            #fffdf8,
            #fff8e9
        );
}


.account-save-card strong {
    display: block;

    color: #5c4930;

    font-size: 8px;
    font-weight: 700;
}


.account-save-card p {
    margin: 3px 0 0;

    color: #a08b6c;

    font-size: 6.5px;
    line-height: 1.45;
}


.account-save-button {
    min-height: 38px;

    flex: 0 0 auto;

    border: 0;
    border-radius: 10px;

    background: var(--account-gold);

    padding:
        0
        14px;

    color: #ffffff;

    font-size: 7.5px;
    font-weight: 700;

    cursor: pointer;

    box-shadow:
        0 5px 12px
        rgba(213, 150, 23, .14);

    transition:
        background-color .15s ease,
        opacity .15s ease;
}


.account-save-button:hover:not(:disabled) {
    background: var(--account-gold-hover);
}


.account-save-button:disabled {
    cursor: not-allowed;

    opacity: .55;
}


/* ==========================================================================
   NO ACCOUNT
   ========================================================================== */

.account-no-account {
    padding: 36px 22px;

    text-align: center;
}


.account-no-account-icon {
    display: grid;

    width: 42px;
    height: 42px;

    place-items: center;

    margin:
        0
        auto
        11px;

    border: 1px solid #ead9b2;
    border-radius: 12px;

    background: #fff8e8;

    color: #a8731f;

    font-size: 12px;
    font-weight: 700;
}


.account-no-account h2 {
    margin: 0;

    color: #344054;

    font-size: 11px;
}


.account-no-account p {
    max-width: 500px;

    margin:
        6px
        auto
        0;

    color: #98a2b3;

    font-size: 8px;
    line-height: 1.6;
}


/* ==========================================================================
   SUPPORT
   ========================================================================== */

.account-support-section {
    margin-top: 14px;

    overflow: hidden;

    border: 1px solid var(--account-line);
    border-radius: 17px;

    background: #ffffff;

    box-shadow:
        0 4px 14px
        rgba(15, 23, 42, .025);
}


.account-section-heading {
    padding:
        15px
        17px;

    border-bottom: 1px solid var(--account-soft-line);
}


.account-section-heading h2 {
    margin: 0;

    color: #1f2937;

    font-size: 12px;
    font-weight: 700;
}


.account-section-heading p:not(
    .account-eyebrow
) {
    margin: 3px 0 0;

    color: #98a2b3;

    font-size: 7.5px;
}


.account-support-component {
    padding: 16px;
}


/*
|--------------------------------------------------------------------------
| Keep the shared support form visually contained.
|--------------------------------------------------------------------------
*/

.account-support-component > * {
    max-width: none !important;

    margin-top: 0 !important;
}


/* ==========================================================================
   FOCUS
   ========================================================================== */

.account-page
:is(
    button,
    input,
    textarea,
    a
):focus-visible {
    outline:
        2px solid rgba(213, 150, 23, .28);

    outline-offset: 2px;
}


/* ==========================================================================
   RESPONSIVE
   ========================================================================== */

@media (max-width: 1050px) {

    .account-layout {
        grid-template-columns:
            minmax(0, 1fr)
            280px;
    }


    .account-photo-section {
        grid-template-columns: 1fr;
    }

}


@media (max-width: 850px) {

    .account-layout {
        grid-template-columns: 1fr;
    }


    .account-side-column {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        align-items: start;
    }


    .account-save-card {
        grid-column: 1 / -1;
    }

}


@media (max-width: 700px) {

    .account-page-header {
        flex-direction: column;
        align-items: stretch;

        gap: 14px;

        padding: 16px;
    }


    .account-header-identity {
        min-width: 0;
    }


    .account-fields-grid {
        grid-template-columns: 1fr;
    }


    .account-field-wide {
        grid-column: auto;
    }


    .account-side-column {
        grid-template-columns: 1fr;
    }


    .account-save-card {
        grid-column: auto;
    }

}


@media (max-width: 520px) {

    .account-page-header h1 {
        font-size: 18px;
    }


    .account-card-header,
    .account-card-body,
    .account-photo-section,
    .account-support-component {
        padding:
            13px
            14px;
    }


    .account-save-card {
        align-items: stretch;
        flex-direction: column;
    }


    .account-save-button {
        width: 100%;
    }

}


/* ==========================================================================
   REDUCED MOTION
   ========================================================================== */

@media (prefers-reduced-motion: reduce) {

    .account-page *,
    .account-page *::before,
    .account-page *::after {
        animation: none !important;

        transition: none !important;
    }

}
</style>
@endpush


@section('content')

<div class="sari-logistics-ui account-page">

    {{-- =====================================================
        PAGE INTRO
    ====================================================== --}}

    <header class="account-page-header">

        <div>

            <p class="account-eyebrow">
                LOGISTICS PROFILE
            </p>

            <h1>
                Account Management
            </h1>

            <p class="account-subtitle">
                Manage your Logistics profile, contact information,
                account address, profile image, and sign-in password.
            </p>

        </div>


        @if($account)

            <div class="account-header-identity">

                <span class="account-header-avatar">
                    {{
                        mb_strtoupper(
                            mb_substr(
                                trim(
                                    (string) (
                                        $account->first_name
                                        ?: 'L'
                                    )
                                ),
                                0,
                                1
                            )
                        )
                    }}
                </span>


                <div>

                    <span>
                        SIGNED IN AS
                    </span>

                    <strong>
                        {{ $account->displayName() }}
                    </strong>

                    <p>
                        {{ $account->email }}
                    </p>

                </div>

            </div>

        @endif

    </header>


    {{-- =====================================================
        FLASH MESSAGES
    ====================================================== --}}

    @if(session('success'))

        <div
            class="account-alert account-alert-success"
            role="status"
        >
            <div>

                <strong>
                    Account updated
                </strong>

                <p>
                    {{ session('success') }}
                </p>

            </div>
        </div>

    @endif


    @if($errors->any())

        <div
            class="account-alert account-alert-danger"
            role="alert"
        >
            <div>

                <strong>
                    Please review your account details
                </strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>

            </div>
        </div>

    @endif


    @if($account)

        <form
            method="POST"
            action="{{ route(
                'logistics.account-management.update'
            ) }}"
            enctype="multipart/form-data"
            class="account-form"
        >
            @csrf
            @method('PATCH')


            <div class="account-layout">

                {{-- =========================================
                    LEFT COLUMN
                ========================================== --}}

                <div class="account-main-column">

                    {{-- =====================================
                        PROFILE PHOTO
                    ====================================== --}}

                    <section class="account-card">

                        <div class="account-card-header">

                            <div>

                                <p class="account-eyebrow">
                                    PROFILE IDENTITY
                                </p>

                                <h2>
                                    Profile photo
                                </h2>

                                <p>
                                    This image is used across your
                                    SARI Logistics workspace.
                                </p>

                            </div>

                        </div>


                        <div class="account-photo-section">

                            <div class="account-photo-component">
                                <x-account.profile-photo
                                    :account="$account"
                                />
                            </div>


                            <div class="account-photo-note">

                                <strong>
                                    Profile image requirements
                                </strong>

                                <p>
                                    JPG, JPEG, PNG, or WEBP.
                                    Maximum file size is 5 MB.
                                </p>

                            </div>

                        </div>

                    </section>


                    {{-- =====================================
                        PERSONAL INFORMATION
                    ====================================== --}}

                    <section class="account-card">

                        <div class="account-card-header">

                            <div>

                                <p class="account-eyebrow">
                                    ACCOUNT DETAILS
                                </p>

                                <h2>
                                    Personal information
                                </h2>

                                <p>
                                    Keep your Logistics contact
                                    information accurate and current.
                                </p>

                            </div>

                        </div>


                        <div class="account-card-body">

                            <div class="account-fields-grid">

                                {{-- First Name --}}
                                <label class="account-field">

                                    <span>
                                        First name
                                    </span>

                                    <input
                                        type="text"
                                        name="first_name"
                                        value="{{ old(
                                            'first_name',
                                            $account->first_name
                                        ) }}"
                                        required
                                        maxlength="120"
                                        autocomplete="given-name"
                                    >

                                    @error('first_name')
                                        <small class="account-field-error">
                                            {{ $message }}
                                        </small>
                                    @enderror

                                </label>


                                {{-- Last Name --}}
                                <label class="account-field">

                                    <span>
                                        Last name
                                    </span>

                                    <input
                                        type="text"
                                        name="last_name"
                                        value="{{ old(
                                            'last_name',
                                            $account->last_name
                                        ) }}"
                                        required
                                        maxlength="120"
                                        autocomplete="family-name"
                                    >

                                    @error('last_name')
                                        <small class="account-field-error">
                                            {{ $message }}
                                        </small>
                                    @enderror

                                </label>


                                {{-- Email --}}
                                <label class="account-field">

                                    <span>
                                        Email address
                                    </span>

                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ old(
                                            'email',
                                            $account->email
                                        ) }}"
                                        required
                                        maxlength="255"
                                        autocomplete="email"
                                    >

                                    @error('email')
                                        <small class="account-field-error">
                                            {{ $message }}
                                        </small>
                                    @enderror

                                </label>


                                {{-- Contact --}}
                                <label class="account-field">

                                    <span>
                                        Contact number
                                    </span>

                                    <input
                                        type="text"
                                        name="contact_no"
                                        value="{{ old(
                                            'contact_no',
                                            $account->contact_no
                                        ) }}"
                                        required
                                        maxlength="30"
                                        autocomplete="tel"
                                    >

                                    @error('contact_no')
                                        <small class="account-field-error">
                                            {{ $message }}
                                        </small>
                                    @enderror

                                </label>


                                {{-- Address --}}
                                <label
                                    class="
                                        account-field
                                        account-field-wide
                                    "
                                >

                                    <span>
                                        Street address
                                    </span>

                                    <textarea
                                        name="street_address"
                                        required
                                        maxlength="1000"
                                        rows="3"
                                        autocomplete="street-address"
                                    >{{ old(
                                        'street_address',
                                        $account->street_address
                                    ) }}</textarea>

                                    <small class="account-field-help">
                                        Primary address associated with
                                        this Logistics account.
                                    </small>

                                    @error('street_address')
                                        <small class="account-field-error">
                                            {{ $message }}
                                        </small>
                                    @enderror

                                </label>

                            </div>

                        </div>

                    </section>

                </div>


                {{-- =========================================
                    RIGHT COLUMN
                ========================================== --}}

                <aside class="account-side-column">

                    {{-- =====================================
                        ACCOUNT SUMMARY
                    ====================================== --}}

                    <section class="account-card account-summary-card">

                        <div class="account-summary-profile">

                            <span class="account-summary-avatar">
                                {{
                                    mb_strtoupper(
                                        mb_substr(
                                            trim(
                                                (string) (
                                                    $account->first_name
                                                    ?: 'L'
                                                )
                                            ),
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </span>


                            <h2>
                                {{ $account->displayName() }}
                            </h2>

                            <p>
                                Logistics Manager
                            </p>

                        </div>


                        <div class="account-summary-list">

                            <div>

                                <span>
                                    EMAIL
                                </span>

                                <strong>
                                    {{ $account->email }}
                                </strong>

                            </div>


                            <div>

                                <span>
                                    CONTACT
                                </span>

                                <strong>
                                    {{
                                        $account->contact_no
                                        ?: 'Not provided'
                                    }}
                                </strong>

                            </div>


                            <div>

                                <span>
                                    ACCOUNT ID
                                </span>

                                <strong>
                                    #{{ $account->id }}
                                </strong>

                            </div>

                        </div>

                    </section>


                    {{-- =====================================
                        SECURITY
                    ====================================== --}}

                    <section class="account-card">

                        <div class="account-card-header">

                            <div>

                                <p class="account-eyebrow">
                                    SECURITY
                                </p>

                                <h2>
                                    Change password
                                </h2>

                                <p>
                                    Leave these fields empty if you
                                    do not want to change the password.
                                </p>

                            </div>

                        </div>


                        <div class="account-card-body">

                            <div class="account-security-fields">

                                <label class="account-field">

                                    <span>
                                        Current password
                                    </span>

                                    <div class="account-password-field">

                                        <input
                                            type="password"
                                            name="current_password"
                                            autocomplete="current-password"
                                            data-password-input
                                        >

                                        <button
                                            type="button"
                                            data-password-toggle
                                            aria-label="Show current password"
                                        >
                                            Show
                                        </button>

                                    </div>

                                    @error('current_password')
                                        <small class="account-field-error">
                                            {{ $message }}
                                        </small>
                                    @enderror

                                </label>


                                <label class="account-field">

                                    <span>
                                        New password
                                    </span>

                                    <div class="account-password-field">

                                        <input
                                            type="password"
                                            name="password"
                                            minlength="8"
                                            autocomplete="new-password"
                                            data-password-input
                                        >

                                        <button
                                            type="button"
                                            data-password-toggle
                                            aria-label="Show new password"
                                        >
                                            Show
                                        </button>

                                    </div>

                                    <small class="account-field-help">
                                        Minimum of 8 characters.
                                    </small>

                                    @error('password')
                                        <small class="account-field-error">
                                            {{ $message }}
                                        </small>
                                    @enderror

                                </label>


                                <label class="account-field">

                                    <span>
                                        Confirm new password
                                    </span>

                                    <div class="account-password-field">

                                        <input
                                            type="password"
                                            name="password_confirmation"
                                            minlength="8"
                                            autocomplete="new-password"
                                            data-password-input
                                        >

                                        <button
                                            type="button"
                                            data-password-toggle
                                            aria-label="Show password confirmation"
                                        >
                                            Show
                                        </button>

                                    </div>

                                </label>

                            </div>

                        </div>

                    </section>


                    {{-- =====================================
                        SAVE
                    ====================================== --}}

                    <section class="account-save-card">

                        <div>

                            <strong>
                                Save account changes
                            </strong>

                            <p>
                                Your updated profile will be used
                                across the Logistics workspace.
                            </p>

                        </div>


                        <button
                            type="submit"
                            class="account-save-button"
                            data-account-submit
                        >
                            Save Changes
                        </button>

                    </section>

                </aside>

            </div>

        </form>

    @else

        <section class="account-card account-no-account">

            <span class="account-no-account-icon">
                !
            </span>

            <h2>
                No editable Logistics account
            </h2>

            <p>
                You are currently using the temporary Logistics
                test login. Register and approve a real Logistics
                account before editing profile information.
            </p>

        </section>

    @endif


    {{-- =====================================================
        SUPPORT / COMPLAINT FORM
    ====================================================== --}}

    <section class="account-support-section">

        <div class="account-section-heading">

            <div>

                <p class="account-eyebrow">
                    SUPPORT
                </p>

                <h2>
                    Need help with your account?
                </h2>

                <p>
                    Submit an account or Logistics-related concern
                    to the SARI support team.
                </p>

            </div>

        </div>


        <div class="account-support-component">
            @include('components.support.complaint-form')
        </div>

    </section>

</div>

@endsection


@push('scripts')

<script>
(() => {
    'use strict';


    function initializeLogisticsAccount() {

        const page =
            document.querySelector(
                '.account-page'
            );


        if (
            !page
            ||
            page.dataset.initialized
        ) {
            return;
        }


        page.dataset.initialized =
            'true';


        /*
        |--------------------------------------------------------------------------
        | Password visibility
        |--------------------------------------------------------------------------
        */

        page.querySelectorAll(
            '[data-password-toggle]'
        ).forEach(
            (button) => {

                button.addEventListener(
                    'click',
                    () => {

                        const wrapper =
                            button.closest(
                                '.account-password-field'
                            );


                        const input =
                            wrapper?.querySelector(
                                '[data-password-input]'
                            );


                        if (!input) {
                            return;
                        }


                        const showing =
                            input.type
                            === 'text';


                        input.type =
                            showing
                                ? 'password'
                                : 'text';


                        button.textContent =
                            showing
                                ? 'Show'
                                : 'Hide';


                        button.setAttribute(
                            'aria-label',
                            showing
                                ? 'Show password'
                                : 'Hide password'
                        );

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Save state
        |--------------------------------------------------------------------------
        */

        const form =
            page.querySelector(
                '.account-form'
            );


        const submit =
            page.querySelector(
                '[data-account-submit]'
            );


        form?.addEventListener(
            'submit',
            () => {

                if (!submit) {
                    return;
                }


                submit.disabled =
                    true;


                submit.textContent =
                    'Saving Changes…';

            }
        );

    }


    if (
        document.readyState
        === 'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            initializeLogisticsAccount
        );

    } else {

        initializeLogisticsAccount();
    }

})();
</script>

@endpush