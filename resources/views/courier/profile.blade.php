@extends('layouts.courier')

@section('title', 'Rider Account')
@section('header-title', 'Account Management')
@section('header-subtitle', 'Rider Profile & Vehicle')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/courier/profile.css') }}?v=20261030-1"
    >
@endpush

@section('content')
@php
    $isOnline = $profile['availability_status'] === 'online';

    $initials = mb_strtoupper(
        mb_substr((string) $account->first_name, 0, 1).
        mb_substr((string) $account->last_name, 0, 1)
    ) ?: 'R';

    $personalFields = [
        [
            'name' => 'first_name',
            'label' => 'First Name',
            'type' => 'text',
            'maxlength' => 120,
            'autocomplete' => 'given-name',
            'required' => true,
        ],
        [
            'name' => 'last_name',
            'label' => 'Last Name',
            'type' => 'text',
            'maxlength' => 120,
            'autocomplete' => 'family-name',
            'required' => true,
        ],
        [
            'name' => 'email',
            'label' => 'Email Address',
            'type' => 'email',
            'maxlength' => 255,
            'autocomplete' => 'email',
            'required' => true,
        ],
        [
            'name' => 'contact_no',
            'label' => 'Contact Number',
            'type' => 'tel',
            'maxlength' => 30,
            'autocomplete' => 'tel',
            'required' => true,
        ],
        [
            'name' => 'street_address',
            'label' => 'Street Address',
            'type' => 'text',
            'maxlength' => 1000,
            'autocomplete' => 'street-address',
            'required' => true,
            'wide' => true,
        ],
    ];

    $vehicleFields = [
        [
            'name' => 'vehicle_type',
            'label' => 'Vehicle Type',
            'required' => true,
        ],
        [
            'name' => 'vehicle_model',
            'label' => 'Vehicle Model',
            'required' => false,
        ],
        [
            'name' => 'plate_number',
            'label' => 'Plate Number',
            'required' => true,
        ],
        [
            'name' => 'license_number',
            'label' => 'License Number',
            'required' => false,
        ],
    ];

    $passwordFields = [
        [
            'name' => 'current_password',
            'label' => 'Current Password',
            'autocomplete' => 'current-password',
            'wide' => true,
        ],
        [
            'name' => 'password',
            'label' => 'New Password',
            'autocomplete' => 'new-password',
            'wide' => false,
        ],
        [
            'name' => 'password_confirmation',
            'label' => 'Confirm New Password',
            'autocomplete' => 'new-password',
            'wide' => false,
        ],
    ];
@endphp

<div id="riderProfile" class="courier-profile-page">
    @if(session('success'))
        <div class="pf-alert pf-alert-success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="pf-alert pf-alert-error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <header>
        <p class="pf-eyebrow">Rider Workspace</p>

        <h2 class="pf-page-title">
            Account <span>Management</span>
        </h2>

        <p class="pf-page-description">
            Manage your profile, availability, and vehicle information.
        </p>
    </header>

    {{-- ACCOUNT SUMMARY --}}
    <section class="pf-panel" aria-label="Rider account summary">
        <div class="pf-summary-main">
            <div class="pf-identity">
                <div class="pf-avatar">
                    @if($account->profile_image_path)
                        <img
                            src="{{ Storage::disk('public')->url($account->profile_image_path) }}"
                            alt="Your profile photo"
                        >
                    @else
                        <span aria-hidden="true">{{ $initials }}</span>
                    @endif
                </div>

                <div>
                    <div class="pf-name-row">
                        <h3 class="pf-name">
                            {{ $profile['full_name'] }}
                        </h3>

                        <span class="pf-status {{ $isOnline ? 'pf-status-online' : '' }}">
                            {{ $isOnline ? 'Online' : 'Offline' }}
                        </span>
                    </div>

                    <p class="pf-identity-meta">
                        {{ $profile['vehicle_type'] }}
                        · {{ $profile['plate_number'] }}
                    </p>

                    <p class="pf-joined">
                        Joined {{ $profile['joined'] }}
                    </p>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('courier.profile.availability') }}"
                class="pf-availability"
            >
                @csrf
                @method('PATCH')

                <div class="pf-field">
                    <label for="riderAvailability" class="pf-label">
                        Availability
                    </label>

                    <select
                        id="riderAvailability"
                        name="availability_status"
                        class="pf-control"
                        required
                    >
                        <option
                            value="online"
                            @selected($profile['availability_status'] === 'online')
                        >
                            Online
                        </option>

                        <option
                            value="offline"
                            @selected($profile['availability_status'] === 'offline')
                        >
                            Offline
                        </option>
                    </select>
                </div>

                <button type="submit" class="pf-button">
                    Update Status
                </button>
            </form>
        </div>

        <dl class="pf-metrics">
            <div class="pf-metric">
                <dt>Completed Deliveries</dt>
                <dd>{{ $profile['completed_deliveries'] }}</dd>
            </div>

            <div class="pf-metric">
                <dt>Rider Rating</dt>

                <dd class="pf-rating">
                    <svg
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path d="m12 2 3.1 6.3 7 .9-5.1 5 1.2 7-6.2-3.3L5.8 21l1.2-6.8-5-5 6.9-1L12 2Z"/>
                    </svg>

                    {{ $profile['rating'] }}
                </dd>
            </div>

            <div class="pf-metric">
                <dt>Assigned Area</dt>

                <dd class="pf-area">
                    {{ $account->barangay_name ?: 'Not assigned' }}
                </dd>

                <p class="pf-area-note">
                    {{ $account->municipality_name ?: 'Municipality unavailable' }}
                </p>
            </div>
        </dl>
    </section>

    {{-- ONE PROFILE FORM, VISUALLY SEPARATED INTO SECTIONS --}}
    <form
        id="riderProfileForm"
        method="POST"
        action="{{ route('courier.profile.update') }}"
        enctype="multipart/form-data"
        class="pf-edit-form"
    >
        @csrf
        @method('PATCH')

        <section class="pf-panel" aria-labelledby="riderPersonalTitle">
            <div class="pf-panel-header">
                <h3 id="riderPersonalTitle" class="pf-panel-title">
                    Personal Information
                </h3>

                <p class="pf-panel-description">
                    Keep your photo and contact details up to date.
                </p>
            </div>

            <div class="pf-photo">
                <x-account.profile-photo :account="$account" />

                @error('profile_image')
                    <p class="pf-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="pf-section-body">
                <div class="pf-fields">
                    @foreach($personalFields as $field)
                        @php
                            $fieldName = $field['name'];
                            $fieldId = 'riderProfile_'.$fieldName;
                        @endphp

                        <div class="pf-field {{ ($field['wide'] ?? false) ? 'pf-field-wide' : '' }}">
                            <label for="{{ $fieldId }}" class="pf-label">
                                {{ $field['label'] }}
                            </label>

                            <input
                                id="{{ $fieldId }}"
                                name="{{ $fieldName }}"
                                type="{{ $field['type'] }}"
                                value="{{ old($fieldName, $account->{$fieldName}) }}"
                                maxlength="{{ $field['maxlength'] }}"
                                autocomplete="{{ $field['autocomplete'] }}"
                                class="pf-control"
                                aria-invalid="{{ $errors->has($fieldName) ? 'true' : 'false' }}"
                                @if($errors->has($fieldName))
                                    aria-describedby="{{ $fieldId }}_error"
                                @endif
                                @required($field['required'])
                            >

                            @error($fieldName)
                                <p
                                    id="{{ $fieldId }}_error"
                                    class="pf-field-error"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="pf-panel" aria-labelledby="riderVehicleTitle">
            <div class="pf-panel-header">
                <h3 id="riderVehicleTitle" class="pf-panel-title">
                    Vehicle Information
                </h3>

                <p class="pf-panel-description">
                    Update the vehicle details associated with your Rider account.
                </p>
            </div>

            <div class="pf-section-body">
                <div class="pf-fields">
                    @foreach($vehicleFields as $field)
                        @php
                            $fieldName = $field['name'];
                            $fieldId = 'riderProfile_'.$fieldName;
                        @endphp

                        <div class="pf-field">
                            <label for="{{ $fieldId }}" class="pf-label">
                                {{ $field['label'] }}

                                @unless($field['required'])
                                    <span class="pf-optional">(optional)</span>
                                @endunless
                            </label>

                            <input
                                id="{{ $fieldId }}"
                                name="{{ $fieldName }}"
                                type="text"
                                value="{{ old($fieldName, $account->{$fieldName}) }}"
                                maxlength="120"
                                class="pf-control"
                                aria-invalid="{{ $errors->has($fieldName) ? 'true' : 'false' }}"
                                @if($errors->has($fieldName))
                                    aria-describedby="{{ $fieldId }}_error"
                                @endif
                                @required($field['required'])
                            >

                            @error($fieldName)
                                <p
                                    id="{{ $fieldId }}_error"
                                    class="pf-field-error"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="pf-panel" aria-labelledby="riderPasswordTitle">
            <div class="pf-panel-header">
                <h3 id="riderPasswordTitle" class="pf-panel-title">
                    Change Password
                </h3>

                <p id="riderPasswordHelp" class="pf-panel-description">
                    Leave these fields blank to keep your current password.
                    A new password must contain at least 8 characters.
                </p>
            </div>

            <div class="pf-section-body">
                <div class="pf-fields">
                    @foreach($passwordFields as $field)
                        @php
                            $fieldName = $field['name'];
                            $fieldId = 'riderProfile_'.$fieldName;
                        @endphp

                        <div class="pf-field {{ $field['wide'] ? 'pf-field-wide' : '' }}">
                            <label for="{{ $fieldId }}" class="pf-label">
                                {{ $field['label'] }}
                            </label>

                            <div class="pf-password-wrap">
                                <input
                                    id="{{ $fieldId }}"
                                    name="{{ $fieldName }}"
                                    type="password"
                                    autocomplete="{{ $field['autocomplete'] }}"
                                    class="pf-control pf-password-control"
                                    aria-invalid="{{ $errors->has($fieldName) ? 'true' : 'false' }}"
                                    aria-describedby="riderPasswordHelp{{ $errors->has($fieldName) ? ' '.$fieldId.'_error' : '' }}"
                                    @if($fieldName === 'password')
                                        minlength="8"
                                    @endif
                                >

                                <button
                                    type="button"
                                    class="pf-password-toggle"
                                    data-password-target="{{ $fieldId }}"
                                    data-password-label="{{ $field['label'] }}"
                                    aria-label="Show {{ strtolower($field['label']) }}"
                                    aria-controls="{{ $fieldId }}"
                                    hidden
                                >
                                    Show
                                </button>
                            </div>

                            @error($fieldName)
                                <p
                                    id="{{ $fieldId }}_error"
                                    class="pf-field-error"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="pf-save-bar">
            <p>
                Save your changes to update your personal, vehicle,
                and password information.
            </p>

            <button type="submit" class="pf-button pf-button-primary">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="m5 12 4 4L19 6"/>
                </svg>

                Save Rider Profile
            </button>
        </div>
    </form>

    <div class="pf-complaint">
        @include('components.support.complaint-form')
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('riderProfile');

    if (!root) return;

    root.querySelectorAll('[data-password-target]').forEach((button) => {
        const input = document.getElementById(button.dataset.passwordTarget);

        if (!input || !root.contains(input)) return;

        button.hidden = false;

        button.addEventListener('click', () => {
            const showPassword = input.type === 'password';

            input.type = showPassword ? 'text' : 'password';
            button.textContent = showPassword ? 'Hide' : 'Show';

            const fieldLabel = (
                button.dataset.passwordLabel || 'password'
            ).toLowerCase();

            button.setAttribute(
                'aria-label',
                `${showPassword ? 'Hide' : 'Show'} ${fieldLabel}`
            );
        });
    });
});
</script>
@endpush