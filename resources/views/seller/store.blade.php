@extends('layouts.seller')

@section('title', 'Store Management — SARI Seller')
@section('page-title', 'Store Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/store.css') }}?v=20261007-1">
@endpush

@section('content')
@php
    $storeName = old(
        'store_name',
        $seller->store_name ?: 'SARI Seller Store'
    );

    $initials = collect(preg_split('/\s+/', $storeName) ?: [])
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('') ?: 'SS';

    $storeStatus = old(
        'store_status',
        $seller->store_status ?: 'open'
    );

    $registeredAddress = collect([
        $seller->street_address,
        $seller->barangay_name,
        $seller->municipality_name,
        $seller->province_name,
    ])->filter()->implode(', ');

    $pickupAddress = old(
        'pickup_address',
        $seller->pickup_address ?: $registeredAddress
    );

    $storeLogoUrl = $seller->store_logo_path
        ? \Illuminate\Support\Facades\Storage::disk('public')
            ->url($seller->store_logo_path)
        : null;

    $storeBannerUrl = $seller->store_banner_path
        ? \Illuminate\Support\Facades\Storage::disk('public')
            ->url($seller->store_banner_path)
        : null;
@endphp



<div class="store-admin">
    <div id="storeDynamicAlert" class="sm-alert" hidden role="status"></div>
    @if(session('success'))
        <div class="sm-alert sm-alert--ok" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="sm-alert sm-alert--err" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <header class="sm-header">
        <div>
            <p class="sm-eyebrow">Public Store & Fulfillment</p>
            <h1 class="sm-title">Store <span>Management</span></h1>
            <p class="sm-subtitle">
                Edit the public information buyers see, upload your Store identity media,
                and manage the operational pickup details used for fulfillment.
            </p>
        </div>

        <div class="sm-header-actions">
            <div class="sm-header-meta">
                <span
                    id="storeHeaderStatus"
                    class="sm-enterprise-status {{ $storeStatus === 'open' ? 'sm-enterprise-status--open' : 'sm-enterprise-status--paused' }}"
                >
                    <span class="sm-enterprise-status__dot"></span>
                    <span id="storeHeaderStatusText">{{ ucfirst($storeStatus) }}</span>
                </span>

                <span id="storeLastUpdated" class="sm-last-updated">
                    Updated {{ $seller->updated_at?->diffForHumans() ?: 'recently' }}
                </span>
            </div>

            <a
                href="{{ route('seller.account') }}"
                class="sm-account-link"
            >
                Account Management
            </a>
        </div>
    </header>

    <div class="sm-layout">
        <form
            method="POST"
            action="{{ route('seller.store.update') }}"
            enctype="multipart/form-data"
            class="sm-stack"
            id="sellerStoreForm"
        >
            @csrf
            @method('PATCH')

            <section class="sm-panel">
                <div class="sm-panel__head">
                    <div>
                        <h2 class="sm-panel__title">Store Media</h2>
                        <p class="sm-panel__copy">Upload a Store logo and optional banner for your marketplace identity.</p>
                    </div>
                </div>

                <div class="sm-panel__body">
                    <div class="sm-media-grid">
                        <div class="sm-upload-card">
                            <div class="sm-upload-top">
                                <p class="sm-upload-title">Store logo</p>

                                <div class="sm-media-actions" aria-label="Store logo actions">
                                    <label
                                        for="storeLogoInput"
                                        class="sm-media-action sm-media-action--edit"
                                        title="Edit or replace Store logo"
                                        aria-label="Edit or replace Store logo"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                        </svg>
                                        <span class="sm-sr-only">Edit Store logo</span>
                                    </label>

                                    <button
                                        type="button"
                                        class="sm-media-action sm-media-action--delete"
                                        data-remove-media="logo"
                                        title="Remove Store logo"
                                        aria-label="Remove Store logo"
                                        @if(!$seller->store_logo_path) hidden @endif
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
                            </div>

                            <div class="sm-image sm-image--logo">
                                <img
                                    id="storeLogoPreview"
                                    src="{{ $storeLogoUrl ?: '' }}"
                                    alt="Store logo preview"
                                    loading="eager"
                                    decoding="async"
                                    fetchpriority="high"
                                    @if(!$storeLogoUrl) hidden @endif
                                >
                                <div
                                    id="storeLogoFallback"
                                    class="sm-placeholder"
                                    @if($storeLogoUrl) hidden @endif
                                >
                                    {{ $initials }}
                                </div>
                            </div>

                            <input
                                id="storeLogoInput"
                                class="sm-file-input"
                                type="file"
                                name="store_logo"
                                accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                            >
                            <input id="removeStoreLogo" type="hidden" name="remove_store_logo" value="0">

                            <p class="sm-help">Square image recommended. JPG, PNG, or WEBP · max 5 MB.</p>
                        </div>

                        <div class="sm-upload-card">
                            <div class="sm-upload-top">
                                <p class="sm-upload-title">Store banner</p>

                                <div class="sm-media-actions" aria-label="Store banner actions">
                                    <label
                                        for="storeBannerInput"
                                        class="sm-media-action sm-media-action--edit"
                                        title="Edit or replace Store banner"
                                        aria-label="Edit or replace Store banner"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                        </svg>
                                        <span class="sm-sr-only">Edit Store banner</span>
                                    </label>

                                    <button
                                        type="button"
                                        class="sm-media-action sm-media-action--delete"
                                        data-remove-media="banner"
                                        title="Remove Store banner"
                                        aria-label="Remove Store banner"
                                        @if(!$seller->store_banner_path) hidden @endif
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
                            </div>

                            <div class="sm-image sm-image--banner">
                                <img
                                    id="storeBannerPreview"
                                    src="{{ $storeBannerUrl ?: '' }}"
                                    alt="Store banner preview"
                                    loading="eager"
                                    decoding="async"
                                    @if(!$storeBannerUrl) hidden @endif
                                >
                                <div
                                    id="storeBannerFallback"
                                    class="sm-placeholder sm-banner-placeholder"
                                    @if($storeBannerUrl) hidden @endif
                                >
                                    Optional storefront banner
                                </div>
                            </div>

                            <input
                                id="storeBannerInput"
                                class="sm-file-input"
                                type="file"
                                name="store_banner"
                                accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                            >
                            <input id="removeStoreBanner" type="hidden" name="remove_store_banner" value="0">

                            <p class="sm-help">Wide image recommended. JPG, PNG, or WEBP · max 8 MB.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="sm-panel" data-editable-section="identity">
                <div class="sm-panel__head">
                    <div>
                        <h2 class="sm-panel__title">Public Store Information</h2>
                        <p class="sm-panel__copy">Buyer-facing Store details and public contact information.</p>
                    </div>

                    <button
                        type="button"
                        class="sm-section-edit"
                        data-edit-section="identity"
                        aria-pressed="false"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                        </svg>
                        <span data-edit-label>Edit</span>
                    </button>
                </div>

                <div class="sm-panel__body">
                    <div class="sm-grid">
                        <div class="sm-span-2">
                            <label class="sm-label" for="storeName">Store name</label>
                            <input
                                id="storeName"
                                data-section-control
                                name="store_name"
                                required
                                maxlength="150"
                                value="{{ old('store_name', $seller->store_name) }}"
                                class="sm-control"
                            >
                        </div>

                        <div class="sm-span-2">
                            <label class="sm-label" for="storeDescription">Store description</label>
                            <textarea
                                id="storeDescription"
                                data-section-control
                                name="store_description"
                                rows="5"
                                maxlength="2000"
                                class="sm-control"
                            >{{ old('store_description', $seller->store_description) }}</textarea>
                            <div class="sm-field-footer">
                                <span>Keep it concise and buyer-focused.</span>
                                <span id="storeDescriptionCounter">0 / 2000</span>
                            </div>
                        </div>

                        <div>
                            <label class="sm-label" for="storePhone">Public phone</label>
                            <input
                                id="storePhone"
                                data-section-control
                                name="store_phone"
                                maxlength="40"
                                value="{{ old('store_phone', $seller->store_phone ?: $seller->contact_no) }}"
                                class="sm-control"
                            >
                            <p class="sm-help">Does not change your verified account contact number.</p>
                        </div>

                        <div>
                            <label class="sm-label" for="storePublicEmail">Public email</label>
                            <input
                                id="storePublicEmail"
                                data-section-control
                                name="store_public_email"
                                type="email"
                                maxlength="255"
                                value="{{ old('store_public_email', $seller->store_public_email ?: $seller->email) }}"
                                class="sm-control"
                            >
                            <p class="sm-help">Does not change the email used to sign in.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="sm-panel" data-editable-section="fulfillment">
                <div class="sm-panel__head">
                    <div>
                        <h2 class="sm-panel__title">Availability & Fulfillment</h2>
                        <p class="sm-panel__copy">Selling status and operational pickup details used for fulfillment.</p>
                    </div>

                    <button
                        type="button"
                        class="sm-section-edit"
                        data-edit-section="fulfillment"
                        aria-pressed="false"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                        </svg>
                        <span data-edit-label>Edit</span>
                    </button>
                </div>

                <div class="sm-panel__body">
                    <div class="sm-grid">
                        <div class="sm-span-2">
                            <label class="sm-label" for="storeStatus">Store status</label>
                            <div class="sm-select" data-custom-select>
                                <select
                                    id="storeStatus"
                                    data-section-control
                                    name="store_status"
                                    required
                                    class="sm-native-select"
                                    aria-hidden="true"
                                    tabindex="-1"
                                >
                                    <option value="open" @selected($storeStatus === 'open')>
                                        Open — accepting new orders
                                    </option>
                                    <option value="paused" @selected($storeStatus === 'paused')>
                                        Paused — temporarily stop new orders
                                    </option>
                                </select>

                                <button
                                    type="button"
                                    class="sm-select__button"
                                    data-custom-select-button
                                    aria-haspopup="listbox"
                                    aria-expanded="false"
                                    disabled
                                >
                                    <span class="sm-select__value" data-custom-select-value>
                                        {{ $storeStatus === 'paused'
                                            ? 'Paused — temporarily stop new orders'
                                            : 'Open — accepting new orders' }}
                                    </span>

                                    <svg class="sm-select__chevron" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="m7 10 5 5 5-5"></path>
                                    </svg>
                                </button>

                                <div
                                    class="sm-select__menu"
                                    data-custom-select-menu
                                    role="listbox"
                                    aria-label="Store status"
                                    hidden
                                >
                                    <button
                                        type="button"
                                        class="sm-select__option {{ $storeStatus === 'open' ? 'is-selected' : '' }}"
                                        data-custom-select-option="open"
                                        role="option"
                                        aria-selected="{{ $storeStatus === 'open' ? 'true' : 'false' }}"
                                    >
                                        <span class="sm-select__option-dot sm-select__option-dot--open"></span>
                                        <span>
                                            <strong>Open</strong>
                                            <small>Accepting new orders</small>
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        class="sm-select__option {{ $storeStatus === 'paused' ? 'is-selected' : '' }}"
                                        data-custom-select-option="paused"
                                        role="option"
                                        aria-selected="{{ $storeStatus === 'paused' ? 'true' : 'false' }}"
                                    >
                                        <span class="sm-select__option-dot sm-select__option-dot--paused"></span>
                                        <span>
                                            <strong>Paused</strong>
                                            <small>Temporarily stop new orders</small>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="sm-span-2">
                            <label class="sm-label" for="pickupAddress">Pickup address</label>
                            <textarea
                                id="pickupAddress" data-section-control
                                name="pickup_address"
                                required
                                rows="4"
                                maxlength="1000"
                                class="sm-control"
                            >{{ $pickupAddress }}</textarea>
                            <p class="sm-help">This is the operational pickup address used for fulfillment.</p>
                        </div>

                        <div class="sm-span-2">
                            <label class="sm-label" for="pickupInstructions">Pickup instructions</label>
                            <textarea
                                id="pickupInstructions" data-section-control
                                name="pickup_instructions"
                                rows="4"
                                maxlength="1200"
                                placeholder="Gate, landmark, receiving hours, contact instructions, etc."
                                class="sm-control"
                            >{{ old('pickup_instructions', $seller->pickup_instructions) }}</textarea>
                            <div class="sm-field-footer">
                                <span>Landmarks, receiving hours, gate instructions, or contact notes.</span>
                                <span id="pickupInstructionsCounter">0 / 1200</span>
                            </div>
                        </div>
                    </div>

                    <div class="sm-address-readonly">
                        <strong>Verified registration address — read only</strong>
                        <p>{{ $registeredAddress ?: 'No verified registration address is stored.' }}</p>
                        <p>
                            Store Management does not modify the address verified during Seller registration.
                        </p>
                    </div>
                </div>
            </section>

            <div class="sm-savebar" id="storeSaveBar" hidden>
                <div class="sm-savebar__status">
                    <span class="sm-unsaved-dot" aria-hidden="true"></span>
                    <div>
                        <p id="storeSaveBarTitle">Unsaved changes</p>
                        <span id="storeSaveState" aria-live="polite">
                            Review your changes before saving.
                        </span>
                    </div>
                </div>

                <div class="sm-savebar__actions">
                    <button
                        type="button"
                        class="sm-discard"
                        id="storeDiscardButton"
                    >
                        Discard
                    </button>

                    <button type="submit" class="sm-save" id="storeSaveButton">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 12.5 9.2 17 19 7"></path>
                        </svg>
                        <span id="storeSaveButtonText">Save changes</span>
                    </button>
                </div>
            </div>
        </form>

        <aside class="sm-stack sm-preview">
            <section class="sm-panel">
                <div class="sm-panel__head">
                    <div>
                        <h2 class="sm-panel__title">Live Store Preview</h2>
                        <p class="sm-panel__copy">Buyer-facing preview updates as you edit.</p>
                    </div>

                    <span class="sm-preview-chip">Buyer view</span>
                </div>

                <div class="sm-preview-cover">
                    <img
                        id="storeBannerLivePreview"
                        src="{{ $storeBannerUrl ?: '' }}"
                        alt="Store banner"
                        loading="eager"
                        decoding="async"
                        @if(!$storeBannerUrl) hidden @endif
                    >
                    <div
                        id="storeBannerLiveFallback"
                        class="sm-preview-cover__empty"
                        @if($storeBannerUrl) hidden @endif
                    >
                        No Store banner uploaded
                    </div>
                </div>

                <div class="sm-preview-body">
                    <div class="sm-preview-identity">
                        <div class="sm-preview-logo">
                            <img
                                id="storeLogoLivePreview"
                                src="{{ $storeLogoUrl ?: '' }}"
                                alt="Store logo"
                                loading="eager"
                                decoding="async"
                                @if(!$storeLogoUrl) hidden @endif
                            >
                            <span id="storeLogoLiveFallback" @if($storeLogoUrl) hidden @endif>
                                {{ $initials }}
                            </span>
                        </div>

                        <div class="min-w-0">
                            <p id="storeNameLivePreview" class="sm-preview-name">{{ $storeName }}</p>
                            <p class="sm-preview-business">
                                {{ $seller->line_of_business ?: 'SARI Marketplace Seller' }}
                            </p>
                        </div>
                    </div>

                    <span
                        id="storeStatusLivePreview"
                        class="sm-status {{ $storeStatus === 'open' ? 'sm-status--open' : 'sm-status--paused' }}"
                    >
                        {{ ucfirst($storeStatus) }}
                    </span>

                    <p id="storeDescriptionLivePreview" class="sm-preview-desc">
                        {{ old('store_description', $seller->store_description) ?: 'Add a concise Store description so buyers understand what your shop offers.' }}
                    </p>

                    <div class="sm-preview-meta">
                        <p>
                            <strong>Public phone:</strong>
                            <span id="storePhoneLivePreview">
                                {{ old('store_phone', $seller->store_phone ?: $seller->contact_no) ?: 'Not provided' }}
                            </span>
                        </p>
                        <p>
                            <strong>Public email:</strong>
                            <span id="storeEmailLivePreview">
                                {{ old('store_public_email', $seller->store_public_email ?: $seller->email) ?: 'Not provided' }}
                            </span>
                        </p>
                        <p>
                            <strong>Pickup:</strong>
                            <span id="pickupAddressLivePreview">{{ $pickupAddress }}</span>
                        </p>
                        <p>
                            <strong>Last saved:</strong>
                            <span id="storePreviewLastSaved">
                                {{ $seller->updated_at?->diffForHumans() ?: 'Recently' }}
                            </span>
                        </p>
                    </div>
                </div>
            </section>

            <section class="sm-panel">
                <div class="sm-panel__body">
                    <p class="sm-panel__title">Store vs. Account identity</p>
                    <p class="sm-panel__copy" style="margin-top:7px;line-height:1.65;">
                        Store logo, banner, public contacts, availability, and pickup details are managed here.
                        Personal identity, login security, and verified registration information remain in Account Management.
                    </p>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function bootStoreManagement() {
        const form = document.getElementById('sellerStoreForm');
        if (!form || form.dataset.initialized === '1') return;
        form.dataset.initialized = '1';

        window.__SARI_SELLER_STORE_LIFECYCLE__?.abort();

        const lifecycle = new AbortController();
        const { signal } = lifecycle;

        window.__SARI_SELLER_STORE_LIFECYCLE__ = lifecycle;

        const byId = (id) => document.getElementById(id);

        const logoInput = byId('storeLogoInput');
        const bannerInput = byId('storeBannerInput');
        const logoPreview = byId('storeLogoPreview');
        const logoFallback = byId('storeLogoFallback');
        const logoLive = byId('storeLogoLivePreview');
        const logoLiveFallback = byId('storeLogoLiveFallback');
        const bannerPreview = byId('storeBannerPreview');
        const bannerFallback = byId('storeBannerFallback');
        const bannerLive = byId('storeBannerLivePreview');
        const bannerLiveFallback = byId('storeBannerLiveFallback');
        const removeLogo = byId('removeStoreLogo');
        const removeBanner = byId('removeStoreBanner');
        const saveButton = byId('storeSaveButton');
        const saveButtonText = byId('storeSaveButtonText');
        const saveState = byId('storeSaveState');
        const saveBar = byId('storeSaveBar');
        const saveBarTitle = byId('storeSaveBarTitle');
        const discardButton = byId('storeDiscardButton');
        const dynamicAlert = byId('storeDynamicAlert');
        const lastUpdated = byId('storeLastUpdated');
        const previewLastSaved = byId('storePreviewLastSaved');
        const headerStatus = byId('storeHeaderStatus');
        const headerStatusText = byId('storeHeaderStatusText');

        const trackedFields = [
            'storeName',
            'storeDescription',
            'storePhone',
            'storePublicEmail',
            'storeStatus',
            'pickupAddress',
            'pickupInstructions',
        ];

        const objectUrls = {
            logo: null,
            banner: null,
        };

        const revokeObjectUrl = (key) => {
            if (objectUrls[key]) {
                URL.revokeObjectURL(objectUrls[key]);
                objectUrls[key] = null;
            }
        };

        const setDynamicAlert = (message = '', type = 'ok') => {
            if (!dynamicAlert) return;

            if (!message) {
                dynamicAlert.hidden = true;
                dynamicAlert.textContent = '';
                dynamicAlert.className = 'sm-alert';
                return;
            }

            dynamicAlert.textContent = message;
            dynamicAlert.className =
                `sm-alert ${type === 'error'
                    ? 'sm-alert--dynamic-error'
                    : 'sm-alert--dynamic-ok'}`;
            dynamicAlert.hidden = false;
        };

        const showImage = (
            key,
            file,
            preview,
            fallback,
            live,
            liveFallback,
            removeInput
        ) => {
            if (!file) return;

            revokeObjectUrl(key);

            const url = URL.createObjectURL(file);
            objectUrls[key] = url;

            preview.src = url;
            preview.hidden = false;
            fallback.hidden = true;

            live.src = url;
            live.hidden = false;
            liveFallback.hidden = true;

            removeInput.value = '0';
        };

        const previewQueue = new Map();
        let previewFrame = 0;

        const queuePreview = (output, value) => {
            if (!output) return;

            previewQueue.set(output, value);

            if (previewFrame) return;

            previewFrame = requestAnimationFrame(() => {
                previewQueue.forEach((value, element) => {
                    element.textContent = value;
                });

                previewQueue.clear();
                previewFrame = 0;
            });
        };

        const syncCounter = (input, output, max) => {
            if (!input || !output) return;

            output.textContent =
                `${String(input.value || '').length} / ${max}`;
        };

        const description = byId('storeDescription');
        const descriptionCounter = byId('storeDescriptionCounter');
        const pickupInstructions = byId('pickupInstructions');
        const pickupCounter = byId('pickupInstructionsCounter');

        syncCounter(description, descriptionCounter, 2000);
        syncCounter(pickupInstructions, pickupCounter, 1200);

        const setSectionEditing = (section, editing) => {
            if (!section) return;

            section.classList.toggle('is-editing', editing);

            const button = section.querySelector('[data-edit-section]');
            const label = button?.querySelector('[data-edit-label]');

            button?.setAttribute(
                'aria-pressed',
                editing ? 'true' : 'false'
            );

            if (label) {
                label.textContent = editing ? 'Done' : 'Edit';
            }

            section
                .querySelectorAll('[data-section-control]')
                .forEach((control) => {
                    if (
                        control instanceof HTMLInputElement
                        || control instanceof HTMLTextAreaElement
                    ) {
                        control.readOnly = !editing;
                    }

                    if (control instanceof HTMLSelectElement) {
                        control.dataset.enterpriseLocked =
                            editing ? '0' : '1';
                    }
                });

            section
                .querySelectorAll('[data-custom-select-button]')
                .forEach((button) => {
                    button.disabled = !editing;

                    if (!editing) {
                        const customSelect =
                            button.closest('[data-custom-select]');

                        customSelect?.classList.remove('is-open');
                        button.setAttribute('aria-expanded', 'false');

                        const menu =
                            customSelect?.querySelector(
                                '[data-custom-select-menu]'
                            );

                        if (menu) {
                            menu.hidden = true;
                        }
                    }
                });
        };

        document
            .querySelectorAll('[data-editable-section]')
            .forEach((section) => {
                setSectionEditing(section, false);
            });

        document
            .querySelectorAll('[data-edit-section]')
            .forEach((button) => {
                button.addEventListener('click', () => {
                    const section =
                        button.closest('[data-editable-section]');

                    const editing =
                        !section?.classList.contains('is-editing');

                    setSectionEditing(section, editing);

                    if (editing) {
                        section
                            ?.querySelector('[data-section-control]')
                            ?.focus({ preventScroll: true });
                    }
                });
            });

        const status = byId('storeStatus');
        const statusOutput = byId('storeStatusLivePreview');
        const customStatus = status?.closest('[data-custom-select]');
        const customStatusButton =
            customStatus?.querySelector('[data-custom-select-button]');
        const customStatusMenu =
            customStatus?.querySelector('[data-custom-select-menu]');
        const customStatusValue =
            customStatus?.querySelector('[data-custom-select-value]');
        const customStatusOptions = Array.from(
            customStatus?.querySelectorAll(
                '[data-custom-select-option]'
            ) || []
        );

        const statusLabels = {
            open: 'Open — accepting new orders',
            paused: 'Paused — temporarily stop new orders',
        };

        const closeStatusMenu = () => {
            if (!customStatus || !customStatusButton || !customStatusMenu) {
                return;
            }

            customStatus.classList.remove('is-open');
            customStatusButton.setAttribute('aria-expanded', 'false');
            customStatusMenu.hidden = true;
        };

        const openStatusMenu = () => {
            if (
                !customStatus
                || !customStatusButton
                || !customStatusMenu
                || customStatusButton.disabled
            ) {
                return;
            }

            customStatus.classList.add('is-open');
            customStatusButton.setAttribute('aria-expanded', 'true');
            customStatusMenu.hidden = false;

            const selected =
                customStatusMenu.querySelector('.is-selected');

            requestAnimationFrame(() => {
                selected?.focus({ preventScroll: true });
            });
        };

        const syncCustomStatusSelect = () => {
            const value = String(status?.value || 'open');

            if (customStatusValue) {
                customStatusValue.textContent =
                    statusLabels[value] || statusLabels.open;
            }

            customStatusOptions.forEach((option) => {
                const selected =
                    option.dataset.customSelectOption === value;

                option.classList.toggle('is-selected', selected);
                option.setAttribute(
                    'aria-selected',
                    selected ? 'true' : 'false'
                );
            });
        };

        customStatusButton?.addEventListener('click', () => {
            if (customStatus?.classList.contains('is-open')) {
                closeStatusMenu();
            } else {
                openStatusMenu();
            }
        });

        customStatusOptions.forEach((option) => {
            option.addEventListener('click', () => {
                const value =
                    option.dataset.customSelectOption;

                if (!value || !status) {
                    return;
                }

                status.value = value;
                status.dispatchEvent(
                    new Event('change', { bubbles: true })
                );

                syncCustomStatusSelect();
                closeStatusMenu();
                customStatusButton?.focus({
                    preventScroll: true,
                });
            });
        });

        customStatus?.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeStatusMenu();
                customStatusButton?.focus({
                    preventScroll: true,
                });
            }

            if (
                (event.key === 'Enter' || event.key === ' ')
                && event.target === customStatusButton
            ) {
                event.preventDefault();

                if (customStatus?.classList.contains('is-open')) {
                    closeStatusMenu();
                } else {
                    openStatusMenu();
                }
            }
        });

        document.addEventListener('click', (event) => {
            if (
                customStatus
                && !customStatus.contains(event.target)
            ) {
                closeStatusMenu();
            }
        }, { signal });

        const syncStatusUI = () => {
            const value = String(status?.value || 'open');
            const label =
                value.charAt(0).toUpperCase() + value.slice(1);
            const open = value === 'open';

            queuePreview(statusOutput, label);
            queuePreview(headerStatusText, label);

            statusOutput?.classList.toggle(
                'sm-status--open',
                open
            );
            statusOutput?.classList.toggle(
                'sm-status--paused',
                !open
            );

            headerStatus?.classList.toggle(
                'sm-enterprise-status--open',
                open
            );
            headerStatus?.classList.toggle(
                'sm-enterprise-status--paused',
                !open
            );
        };

        const bindings = [
            ['storeName', 'storeNameLivePreview', 'SARI Seller Store'],
            [
                'storeDescription',
                'storeDescriptionLivePreview',
                'Add a concise Store description so buyers understand what your shop offers.'
            ],
            ['storePhone', 'storePhoneLivePreview', 'Not provided'],
            ['storePublicEmail', 'storeEmailLivePreview', 'Not provided'],
            ['pickupAddress', 'pickupAddressLivePreview', 'Not provided'],
        ];

        const syncAllPreviewText = () => {
            bindings.forEach(([inputId, outputId, fallback]) => {
                const input = byId(inputId);
                const output = byId(outputId);

                if (!input || !output) return;

                queuePreview(
                    output,
                    String(input.value || '').trim() || fallback
                );
            });

            syncStatusUI();
            syncCustomStatusSelect();
            syncCounter(description, descriptionCounter, 2000);
            syncCounter(pickupInstructions, pickupCounter, 1200);
        };

        bindings.forEach(([inputId, outputId, fallback]) => {
            const input = byId(inputId);
            const output = byId(outputId);

            if (!input || !output) return;

            input.addEventListener('input', () => {
                queuePreview(
                    output,
                    String(input.value || '').trim() || fallback
                );

                if (input === description) {
                    syncCounter(
                        description,
                        descriptionCounter,
                        2000
                    );
                }

                if (input === pickupInstructions) {
                    syncCounter(
                        pickupInstructions,
                        pickupCounter,
                        1200
                    );
                }
            });
        });

        status?.addEventListener('change', () => {
            syncStatusUI();
            syncCustomStatusSelect();
        });

        const mediaDeleteButton = (type) =>
            document.querySelector(
                `[data-remove-media="${type}"]`
            );

        let savedMedia = {
            logo:
                logoPreview && !logoPreview.hidden
                    ? logoPreview.src
                    : '',
            banner:
                bannerPreview && !bannerPreview.hidden
                    ? bannerPreview.src
                    : '',
        };

        const captureState = () => {
            const fields = {};

            trackedFields.forEach((id) => {
                fields[id] = String(byId(id)?.value || '');
            });

            return fields;
        };

        let savedState = captureState();

        const hasUnsavedChanges = () => {
            const current = captureState();

            const fieldsChanged =
                JSON.stringify(current)
                !== JSON.stringify(savedState);

            const mediaChanged =
                Boolean(logoInput?.files?.length)
                || Boolean(bannerInput?.files?.length)
                || removeLogo?.value === '1'
                || removeBanner?.value === '1';

            return fieldsChanged || mediaChanged;
        };

        const updateDirtyState = () => {
            if (!saveBar) return;

            saveBar.hidden = !hasUnsavedChanges();

            if (!saveBar.hidden) {
                saveBar.classList.remove('is-saving');
                saveBarTitle.textContent = 'Unsaved changes';

                if (!form.dataset.saving) {
                    saveState.textContent =
                        'Review your changes before saving.';
                }
            }
        };

        trackedFields.forEach((id) => {
            const control = byId(id);

            control?.addEventListener(
                control instanceof HTMLSelectElement
                    ? 'change'
                    : 'input',
                updateDirtyState
            );
        });

        logoInput?.addEventListener('change', () => {
            const file = logoInput.files?.[0];

            showImage(
                'logo',
                file,
                logoPreview,
                logoFallback,
                logoLive,
                logoLiveFallback,
                removeLogo
            );

            if (file) {
                mediaDeleteButton('logo')
                    ?.removeAttribute('hidden');
            }

            updateDirtyState();
        });

        bannerInput?.addEventListener('change', () => {
            const file = bannerInput.files?.[0];

            showImage(
                'banner',
                file,
                bannerPreview,
                bannerFallback,
                bannerLive,
                bannerLiveFallback,
                removeBanner
            );

            if (file) {
                mediaDeleteButton('banner')
                    ?.removeAttribute('hidden');
            }

            updateDirtyState();
        });

        document
            .querySelectorAll('[data-remove-media]')
            .forEach((button) => {
                button.addEventListener('click', () => {
                    const type = button.dataset.removeMedia;

                    if (type === 'logo') {
                        revokeObjectUrl('logo');
                        logoInput.value = '';
                        removeLogo.value = '1';
                        logoPreview.hidden = true;
                        logoFallback.hidden = false;
                        logoLive.hidden = true;
                        logoLiveFallback.hidden = false;
                    }

                    if (type === 'banner') {
                        revokeObjectUrl('banner');
                        bannerInput.value = '';
                        removeBanner.value = '1';
                        bannerPreview.hidden = true;
                        bannerFallback.hidden = false;
                        bannerLive.hidden = true;
                        bannerLiveFallback.hidden = false;
                    }

                    button.hidden = true;
                    updateDirtyState();
                });
            });

        const restoreMedia = (
            type,
            url,
            preview,
            fallback,
            live,
            liveFallback
        ) => {
            revokeObjectUrl(type);

            const exists = Boolean(url);

            preview.hidden = !exists;
            fallback.hidden = exists;
            live.hidden = !exists;
            liveFallback.hidden = exists;

            if (exists) {
                preview.src = url;
                live.src = url;
            }

            mediaDeleteButton(type).hidden = !exists;
        };

        discardButton?.addEventListener('click', () => {
            trackedFields.forEach((id) => {
                const control = byId(id);
                if (!control) return;

                control.value = savedState[id] ?? '';
            });

            logoInput.value = '';
            bannerInput.value = '';
            removeLogo.value = '0';
            removeBanner.value = '0';

            restoreMedia(
                'logo',
                savedMedia.logo,
                logoPreview,
                logoFallback,
                logoLive,
                logoLiveFallback
            );

            restoreMedia(
                'banner',
                savedMedia.banner,
                bannerPreview,
                bannerFallback,
                bannerLive,
                bannerLiveFallback
            );

            document
                .querySelectorAll('[data-editable-section]')
                .forEach((section) => {
                    setSectionEditing(section, false);
                });

            setDynamicAlert();
            syncAllPreviewText();
            updateDirtyState();
        });

        const loadBitmap = async (file) => {
            if ('createImageBitmap' in window) {
                return createImageBitmap(file);
            }

            return new Promise((resolve, reject) => {
                const image = new Image();
                const url = URL.createObjectURL(file);

                image.onload = () => {
                    URL.revokeObjectURL(url);
                    resolve(image);
                };

                image.onerror = () => {
                    URL.revokeObjectURL(url);
                    reject(
                        new Error('Image could not be decoded.')
                    );
                };

                image.src = url;
            });
        };

        const optimizeImage = async (
            file,
            {
                maxWidth,
                maxHeight,
                quality = 0.86,
                minBytes = 450000,
            }
        ) => {
            if (!file || file.size < minBytes) {
                return file;
            }

            try {
                const bitmap = await loadBitmap(file);
                const sourceWidth =
                    bitmap.width || bitmap.naturalWidth;
                const sourceHeight =
                    bitmap.height || bitmap.naturalHeight;

                if (!sourceWidth || !sourceHeight) {
                    bitmap.close?.();
                    return file;
                }

                const scale = Math.min(
                    1,
                    maxWidth / sourceWidth,
                    maxHeight / sourceHeight
                );

                const width = Math.max(
                    1,
                    Math.round(sourceWidth * scale)
                );

                const height = Math.max(
                    1,
                    Math.round(sourceHeight * scale)
                );

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const context = canvas.getContext(
                    '2d',
                    { alpha: true }
                );

                if (!context) {
                    bitmap.close?.();
                    return file;
                }

                context.drawImage(
                    bitmap,
                    0,
                    0,
                    width,
                    height
                );

                bitmap.close?.();

                const blob = await new Promise((resolve) => {
                    canvas.toBlob(
                        resolve,
                        'image/webp',
                        quality
                    );
                });

                if (!blob || blob.size >= file.size) {
                    return file;
                }

                const baseName =
                    file.name.replace(/\.[^.]+$/, '');

                return new File(
                    [blob],
                    `${baseName}.webp`,
                    {
                        type: 'image/webp',
                        lastModified: Date.now(),
                    }
                );
            } catch (error) {
                return file;
            }
        };

        const firstErrorMessage = (payload) => {
            const errors = payload?.errors || {};

            for (const value of Object.values(errors)) {
                if (Array.isArray(value) && value[0]) {
                    return String(value[0]);
                }
            }

            return String(
                payload?.message
                || 'Store changes could not be saved.'
            );
        };

        const applySavedImage = (
            url,
            preview,
            fallback,
            live,
            liveFallback,
            key
        ) => {
            revokeObjectUrl(key);

            if (!url) {
                preview.hidden = true;
                fallback.hidden = false;
                live.hidden = true;
                liveFallback.hidden = false;
                savedMedia[key] = '';
                mediaDeleteButton(key).hidden = true;
                return;
            }

            const freshUrl =
                `${url}${url.includes('?') ? '&' : '?'}v=${Date.now()}`;

            preview.src = freshUrl;
            preview.hidden = false;
            fallback.hidden = true;

            live.src = freshUrl;
            live.hidden = false;
            liveFallback.hidden = true;

            savedMedia[key] = freshUrl;
            mediaDeleteButton(key).hidden = false;
        };

        form.addEventListener('submit', async (event) => {
            if (
                !window.fetch
                || !window.FormData
                || form.dataset.saving === '1'
            ) {
                return;
            }

            event.preventDefault();

            if (!hasUnsavedChanges()) {
                return;
            }

            form.dataset.saving = '1';
            saveButton.disabled = true;
            discardButton.disabled = true;
            saveButtonText.textContent = 'Saving…';
            saveBar.hidden = false;
            saveBar.classList.add('is-saving');
            saveBarTitle.textContent = 'Saving changes';
            saveState.textContent = 'Optimizing media…';
            setDynamicAlert();

            try {
                const formData = new FormData(form);

                const [
                    optimizedLogo,
                    optimizedBanner,
                ] = await Promise.all([
                    optimizeImage(
                        logoInput?.files?.[0],
                        {
                            maxWidth: 1200,
                            maxHeight: 1200,
                            quality: 0.88,
                        }
                    ),
                    optimizeImage(
                        bannerInput?.files?.[0],
                        {
                            maxWidth: 2200,
                            maxHeight: 1200,
                            quality: 0.86,
                        }
                    ),
                ]);

                if (optimizedLogo) {
                    formData.set(
                        'store_logo',
                        optimizedLogo,
                        optimizedLogo.name
                    );
                }

                if (optimizedBanner) {
                    formData.set(
                        'store_banner',
                        optimizedBanner,
                        optimizedBanner.name
                    );
                }

                saveState.textContent = 'Saving securely…';

                const response = await fetch(
                    form.action,
                    {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    }
                );

                const contentType =
                    response.headers.get('content-type') || '';

                const payload =
                    contentType.includes('application/json')
                        ? await response.json()
                        : {
                            message:
                                'The server returned an unexpected response.'
                        };

                if (!response.ok) {
                    throw new Error(
                        firstErrorMessage(payload)
                    );
                }

                const store = payload.store || {};

                if (
                    Object.prototype.hasOwnProperty.call(
                        store,
                        'store_logo_url'
                    )
                ) {
                    applySavedImage(
                        store.store_logo_url,
                        logoPreview,
                        logoFallback,
                        logoLive,
                        logoLiveFallback,
                        'logo'
                    );
                }

                if (
                    Object.prototype.hasOwnProperty.call(
                        store,
                        'store_banner_url'
                    )
                ) {
                    applySavedImage(
                        store.store_banner_url,
                        bannerPreview,
                        bannerFallback,
                        bannerLive,
                        bannerLiveFallback,
                        'banner'
                    );
                }

                logoInput.value = '';
                bannerInput.value = '';
                removeLogo.value = '0';
                removeBanner.value = '0';

                savedState = captureState();

                document
                    .querySelectorAll('[data-editable-section]')
                    .forEach((section) => {
                        setSectionEditing(section, false);
                    });

                syncStatusUI();

                const justNow = 'Updated just now';
                const previewNow = 'Just now';

                if (lastUpdated) {
                    lastUpdated.textContent = justNow;
                }

                if (previewLastSaved) {
                    previewLastSaved.textContent = previewNow;
                }

                saveBar.classList.remove('is-saving');
                saveBarTitle.textContent = 'Changes saved';
                saveState.textContent =
                    'Your Store settings are up to date.';

                setDynamicAlert(
                    payload.message
                    || 'Store settings saved successfully.',
                    'ok'
                );

                window.setTimeout(() => {
                    updateDirtyState();
                }, 900);
            } catch (error) {
                saveBar.classList.remove('is-saving');
                saveBarTitle.textContent = 'Save failed';
                saveState.textContent =
                    'Review the error above and try again.';

                setDynamicAlert(
                    error?.message
                    || 'Store changes could not be saved.',
                    'error'
                );
            } finally {
                form.dataset.saving = '0';
                saveButton.disabled = false;
                discardButton.disabled = false;
                saveButtonText.textContent = 'Save changes';
            }
        });

        const cleanup = () => {
            revokeObjectUrl('logo');
            revokeObjectUrl('banner');

            if (previewFrame) {
                cancelAnimationFrame(previewFrame);
            }

            form.dataset.initialized = '0';
            lifecycle.abort();

            if (window.__SARI_SELLER_STORE_LIFECYCLE__ === lifecycle) {
                window.__SARI_SELLER_STORE_LIFECYCLE__ = null;
            }
        };

        document.addEventListener(
            'livewire:navigating',
            cleanup,
            { once: true, signal }
        );

        window.addEventListener('beforeunload', cleanup, { once: true, signal });

        syncCustomStatusSelect();
        syncAllPreviewText();
        updateDirtyState();
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            bootStoreManagement,
            { once: true }
        );
    } else {
        bootStoreManagement();
    }
})();
</script>
@endpush
