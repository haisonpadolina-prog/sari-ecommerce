@extends('layouts.logistics')

@section('title', 'Account Management | SARI Logistics')
@section('page-title', 'Account Management')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/logistics/account-management.css') }}?v=20261015-1"
    >
@endpush


@section('content')

<div class="account-page">

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