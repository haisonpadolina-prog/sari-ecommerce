@extends('layouts.seller')

@section('title', 'Account Management — SARI Seller')
@section('page-title', 'Account Management')

@push('styles')
<style id="sariSellerEmbeddedCss-account" data-sari-inline-css="account">
@verbatim
/* Seller Account — Buyer Profile layout parity. Main surfaces float; inner elements remain flat. */
.seller-account-enterprise {
    --sa-gold: #c88912;
    --sa-gold-dark: #aa730f;
    --sa-ink: #273140;
    --sa-body: #5f6b79;
    --sa-muted: #83909e;
    --sa-line: #e2e7ed;
    --sa-line-soft: #edf0f4;
    --sa-float: 0 2px 5px rgba(15, 23, 42, .045), 0 10px 24px rgba(15, 23, 42, .075), 0 24px 54px rgba(15, 23, 42, .095), inset 0 1px 0 rgba(255, 255, 255, .92);
    width: 100%; max-width: 1380px; margin: 0 auto;
    color: var(--sa-ink); font-family: Poppins, ui-sans-serif, system-ui, sans-serif;
}
.seller-account-enterprise *, .seller-account-enterprise *::before, .seller-account-enterprise *::after {box-sizing:border-box}
.seller-account-enterprise [hidden] { display: none !important; }
.sa-page-head {padding:2px 2px 17px}
.sa-eyebrow {margin:0;color:#a87316;font-size:10.5px;font-weight:800;line-height:1.2;letter-spacing:.14em;text-transform:uppercase}
.sa-page-title {margin:5px 0 0;color:#2e343b;font-size:clamp(27px,2.3vw,33px);font-weight:760;letter-spacing:-.035em;line-height:1.1}
.sa-page-title span {color:var(--sa-gold)}
.sa-alert {margin-bottom:14px;border:1px solid;border-radius:11px;padding:11px 14px;font-size:12px;font-weight:550;line-height:1.5}
.sa-alert--success {border-color:#d5e5db;background:#f7fbf8;color:#4e765e}
.sa-alert--error {border-color:#edd4d4;background:#fff8f8;color:#a74e4e}
.sa-layout {display:grid;grid-template-columns:286px minmax(0,1fr);gap:14px;align-items:start}
.sa-left-column {position:sticky;top:84px;display:grid;gap:14px;min-width:0}
.sa-card {min-width:0;border:1px solid #e5e7eb;border-radius:17px;background:#fff;box-shadow:var(--sa-float)}
.sa-profile-card {overflow:hidden}
.sa-profile-card__head {display:flex;align-items:center;justify-content:space-between;gap:10px;padding:15px 16px;border-bottom:1px solid var(--sa-line-soft)}
.sa-profile-card__title {margin:0;color:#2d3642;font-size:14px;font-weight:760}
.sa-profile-card__type {color:#919ba8;font-size:10px;font-weight:750;text-transform:uppercase;letter-spacing:.06em}
.sa-profile-card__body {padding:20px 16px 17px}
.sa-avatar-wrap {position:relative;width:118px;height:118px;margin:0 auto}
.sa-avatar {display:grid;width:118px;height:118px;overflow:hidden;place-items:center;border:1px solid #dbe1e8;border-radius:20px;background:#f4f6f8;color:#8d98a6;font-size:30px;font-weight:720;letter-spacing:-.05em}
.sa-avatar img {display:block;width:100%;height:100%;object-fit:cover}
.sa-avatar-edit {position:absolute;right:-7px;bottom:-7px;display:grid;width:36px;height:36px;place-items:center;border:3px solid #fff;border-radius:11px;background:var(--sa-gold);color:#fff;cursor:pointer}
.sa-avatar-edit:hover,.sa-avatar-edit:focus-visible {background:var(--sa-gold-dark);outline:none}
.sa-avatar-edit svg {width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.sa-profile-name {margin:19px 0 0;color:#242d38;font-size:17px;font-weight:760;line-height:1.35;text-align:center;overflow-wrap:anywhere}
.sa-profile-email {margin:5px 0 0;overflow-wrap:anywhere;color:#8792a0;font-size:11.5px;text-align:center;line-height:1.5}
.sa-profile-role {display:table;margin:11px auto 0;border:1px solid #e6eaf0;border-radius:999px;background:#f8f9fb;padding:5px 11px;color:#697586;font-size:10px;font-weight:750}
.sa-profile-meta {display:grid;gap:11px;margin-top:17px;border-top:1px solid var(--sa-line-soft);padding-top:14px}
.sa-profile-meta-row {display:flex;justify-content:space-between;gap:10px;align-items:flex-start}
.sa-profile-meta-label {color:#929caa;font-size:10.5px}
.sa-profile-meta-value {max-width:62%;color:#4e5a69;font-size:11px;font-weight:650;overflow-wrap:anywhere;text-align:right}
.sa-sidebar-panel {overflow:hidden}
.sa-sidebar-head {padding:15px 16px 12px;border-bottom:1px solid var(--sa-line-soft)}
.sa-sidebar-head h2 {margin:0;font-size:14px;font-weight:750;color:#2b3642}
.sa-detail-list {margin:0;padding:1px 16px 8px;display:grid}
.sa-detail-row {display:flex;justify-content:space-between;align-items:flex-start;gap:12px;border-bottom:1px solid #f0f2f5;padding:11px 0}
.sa-detail-row:last-child {border:0}
.sa-detail-row dt {color:#8b96a4;font-size:10.5px;line-height:1.5}
.sa-detail-row dd {max-width:60%;margin:0;color:#4b5765;font-size:11px;font-weight:650;text-align:right;line-height:1.5;overflow-wrap:anywhere}
.sa-unified-account {
    overflow: visible; /* preserve custom Sex dropdown visibility */
    border-radius: 20px; /* main Personal Information -> Password & Security card */
}
/* Keep inner surfaces inside the outer rounded silhouette without clipping dropdowns. */
.sa-unified-account > #sellerAccountForm > .sa-unified-section:first-of-type {
    border-radius: 19px 19px 0 0;
}
.sa-unified-account > #sellerAccountForm > .sa-section-footer {
    border-radius: 0; /* Save Account Changes is an internal separator, not the bottom edge */
}
.sa-unified-account > #sellerPasswordForm > .sa-section-footer {
    border-radius: 0 0 19px 19px; /* actual bottom corners of the whole account card */
}
.sa-unified-section {min-width:0;background:#fff}
.sa-unified-section--bordered {border-top:1px solid var(--sa-line-soft)}
.sa-section-head {display:flex;align-items:flex-start;gap:16px;padding:19px 20px 13px}
.sa-section-heading {min-width:0;flex:0 1 auto}
.sa-section-title {margin:0;color:#273140;font-size:17px;font-weight:760;line-height:1.3;letter-spacing:-.015em}
.sa-section-line {height:1px;min-width:60px;flex:1;margin-top:13px;background:#e4e9ee}
.sa-section-body {padding:0 20px 21px}
.sa-grid {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px 15px}
.sa-field {min-width:0}
.sa-field--full {grid-column:1/-1}
.sa-field label,.sa-label {display:block;margin-bottom:7px;color:#40506a;font-size:11.5px;font-weight:690;line-height:1.4}
.sa-control {display:block;width:100%;min-height:47px;border:1px solid #d7dee7;border-radius:11px;background:#fff;padding:0 13px;color:#2f3948;font-family:inherit;font-size:12.5px;font-weight:500;line-height:1.4;outline:0;box-shadow:none;transition:border-color .14s ease,box-shadow .14s ease}
.sa-control:hover {border-color:#c9d1db}
.sa-control:focus,.sa-sex.is-open .sa-sex__button,.sa-sex__button:focus-visible {border-color:var(--sa-gold);box-shadow:0 0 0 3px rgba(200,137,18,.08);outline:0}
.sa-control::placeholder {color:#a0aab6}
.sa-control--textarea {min-height:87px;resize:vertical;padding:11px 13px;line-height:1.6}
.sa-help {margin:6px 0 0;color:#929caa;font-size:10.5px;line-height:1.55}
#sellerContactCounter {float:right;color:#667381;font-weight:700;font-variant-numeric:tabular-nums}
.sa-section-footer {display:flex;justify-content:flex-end;align-items:center;gap:14px;border-top:1px solid var(--sa-line-soft);background:#fbfcfd;padding:15px 20px;border-radius:0 0 17px 17px}
.sa-primary {display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:44px;border:1px solid var(--sa-gold);border-radius:10px;background:var(--sa-gold);padding:0 18px;color:#fff;font-family:inherit;font-size:11.5px;font-weight:750;cursor:pointer;white-space:nowrap}
.sa-primary:hover,.sa-primary:focus-visible {border-color:var(--sa-gold-dark);background:var(--sa-gold-dark);outline:0}
.sa-primary svg {width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round}
/* Verification rows are flat inside the unified surface. */
.sa-doc-list {display:grid;padding:0 20px 6px}
.sa-doc-row {display:grid;grid-template-columns:minmax(0,1fr) auto auto;gap:10px 13px;align-items:center;border-top:1px solid var(--sa-line-soft);padding:15px 0}
.sa-doc-row:first-child {border-top:0}
.sa-doc-name {margin:0;color:#344052;font-size:12px;font-weight:760}
.sa-doc-state {display:inline-flex;align-items:center;border:1px solid;border-radius:999px;padding:5px 9px;font-size:10px;font-weight:750;white-space:nowrap}
.sa-doc-state--available {border-color:#d7e5dc;background:#f7fbf8;color:#4f7d63}
.sa-doc-state--missing {border-color:#efcece;background:#fff8f8;color:#a65353}
.sa-doc-actions {display:flex;justify-content:flex-end;align-items:center;gap:7px}
.sa-doc-link {display:inline-flex;align-items:center;gap:5px;justify-content:center;min-height:35px;border:1px solid #dfe4ea;border-radius:8px;background:#fff;padding:0 12px;color:#586475;font-size:10.5px;font-weight:700;text-decoration:none}
.sa-doc-link:hover {border-color:#d1b576;color:#9b6812}
.sa-doc-link svg {width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:1.8}
.sa-icon-action {display:grid;place-items:center;width:35px;height:35px;border:1px solid #dfe4ea;border-radius:9px;background:#fff;color:#9b6812;cursor:pointer}
.sa-icon-action:hover,.sa-icon-action:focus-visible {border-color:#d1b576;outline:none}
.sa-icon-action svg {width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.sa-doc-selected {grid-column:1/-1;margin:-3px 0 0;color:#929caa;font-size:10px;line-height:1.5;overflow-wrap:anywhere}
.sa-doc-selected.is-ready {color:#996511;font-weight:690}
/* Password is a separate form: never nested with the account update form. */
.sa-password-stack {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}
.sa-password-wrap {position:relative;min-width:0}
.sa-password-wrap:first-child {grid-column:1/-1;max-width:440px;width:100%}
.sa-password-wrap .sa-control {padding-right:48px}
.sa-password-toggle {position:absolute;right:7px;top:29px;display:grid;place-items:center;width:34px;height:34px;border:0;border-radius:8px;background:transparent;color:#7c8998;cursor:pointer}
.sa-password-toggle:hover {background:#f3f5f7;color:#4b5563}
.sa-password-toggle svg {width:17px;height:17px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.sa-file-input,.sa-sr-only {position:absolute!important;width:1px!important;height:1px!important;padding:0!important;margin:-1px!important;overflow:hidden!important;clip:rect(0,0,0,0)!important;white-space:nowrap!important;border:0!important}
/* Custom sex select retains the original native select as the form value. */
.sa-sex {position:relative}
.sa-sex__button,.sa-sex__menu {display:none}
.sa-sex.is-ready > .sa-control {position:absolute!important;width:1px!important;height:1px!important;opacity:0!important;pointer-events:none!important}
.sa-sex.is-ready .sa-sex__button {display:flex;align-items:center;justify-content:space-between;gap:10px;width:100%;min-height:47px;border:1px solid #d7dee7;border-radius:11px;background:#fff;padding:0 13px;color:#2f3948;font:inherit;font-size:12.5px;font-weight:500;cursor:pointer}
.sa-sex__button svg {width:16px;height:16px;fill:none;stroke:#687482;stroke-width:1.9;transition:transform .15s ease}
.sa-sex.is-open {z-index:50}
.sa-sex.is-open .sa-sex__button svg {transform:rotate(180deg)}
.sa-sex.is-ready .sa-sex__menu {position:absolute;z-index:60;top:calc(100% + 6px);left:0;right:0;display:block;overflow:hidden;border:1px solid #dfe4ea;border-radius:12px;background:#fff;padding:6px;box-shadow:0 18px 40px rgba(22,29,37,.12)}
.sa-sex__menu[hidden]{display:none!important}
.sa-sex__option {display:block;width:100%;border:0;border-radius:8px;background:#fff;padding:10px 12px;color:#354151;font:inherit;font-size:12px;text-align:left;cursor:pointer}
.sa-sex__option:hover,.sa-sex__option:focus-visible {background:#f3f5f7;outline:none}
.sa-sex__option.is-selected {background:#f7f8fa;color:#9b6812;font-weight:700}
@media(max-width:980px) {.sa-layout{grid-template-columns:240px minmax(0,1fr)}}
@media(max-width:760px) {
    .sa-layout{grid-template-columns:1fr}
    .sa-left-column{position:static;top:auto;order:1}
    .sa-unified-account{order:2}
    .sa-profile-card__body{display:grid;grid-template-columns:110px minmax(0,1fr);gap:14px;align-items:center}
    .sa-avatar-wrap,.sa-avatar{width:105px;height:105px}
    .sa-avatar-wrap{margin:0}
    .sa-profile-card__identity{min-width:0}
    .sa-profile-name,.sa-profile-email{text-align:left;margin-top:0}
    .sa-profile-email{margin-top:5px}
    .sa-profile-role{margin:9px 0 0}
    .sa-profile-meta{grid-column:1/-1}
}
@media(max-width:640px) {
    .sa-page-title{font-size:28px}
    .sa-card{border-radius:14px}
    .sa-section-head{padding:16px 15px 12px}
    .sa-section-body{padding:0 15px 18px}
    .sa-section-title{font-size:15.5px}
    .sa-grid,.sa-password-stack{grid-template-columns:1fr}
    .sa-field--full,.sa-password-wrap:first-child{grid-column:auto}
    .sa-password-wrap:first-child{max-width:none}
    .sa-doc-list{padding-inline:15px}
    .sa-doc-row{grid-template-columns:minmax(0,1fr) auto;gap:9px}
    .sa-doc-actions{grid-column:1/-1;justify-content:flex-start}
    .sa-doc-selected{grid-column:1/-1}
    .sa-section-footer{flex-direction:column;align-items:stretch;padding:14px 15px}
    .sa-primary{width:100%}
}
/* Smaller radius matches the existing profile cards on narrow screens. */
@media(max-width:640px) {
    .sa-unified-account {border-radius: 16px;}
    .sa-unified-account > #sellerAccountForm > .sa-unified-section:first-of-type {
        border-radius: 15px 15px 0 0;
    }
    .sa-unified-account > #sellerPasswordForm > .sa-section-footer {
        border-radius: 0 0 15px 15px;
    }
}
@media(max-width:767px) {
    .sa-card {box-shadow:0 1px 3px rgba(15, 23, 42, .035), 0 6px 16px rgba(15, 23, 42, .055), 0 13px 28px rgba(15, 23, 42, .065), inset 0 1px 0 rgba(255, 255, 255, .90)}
}
@media(prefers-reduced-motion:reduce){.seller-account-enterprise *{transition-duration:.01ms!important}}
@endverbatim
</style>
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

    $accountStatus = strtolower((string) ($account->account_status ?: 'active'));
    $registrationStatus = strtolower((string) ($account->registration_status ?: 'approved'));
    $sexValue = old('sex', $account->sex);
@endphp



<div class="seller-account-enterprise">
    <header class="sa-page-head">
        <p class="sa-eyebrow">Account Management</p>
        <h1 class="sa-page-title">Seller <span>Profile</span></h1>
    </header>

    @if(session('success'))
        <div class="sa-alert sa-alert--success" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="sa-alert sa-alert--error" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="sa-layout">
        <aside class="sa-left-column" aria-label="Seller profile and account summary">
            <section class="sa-card sa-profile-card">
                <header class="sa-profile-card__head">
                    <h2 class="sa-profile-card__title">Profile</h2>
                    <span class="sa-profile-card__type">Seller account</span>
                </header>
                <div class="sa-profile-card__body">
                    <div class="sa-avatar-wrap">
                        <div class="sa-avatar">
                            @if($profilePhotoUrl)
                                <img id="sellerProfilePreview" src="{{ $profilePhotoUrl }}" alt="Seller profile photo" loading="eager" decoding="async">
                                <span id="sellerProfileFallback" hidden>{{ $initials }}</span>
                            @else
                                <img id="sellerProfilePreview" src="" alt="Selected seller profile photo" hidden>
                                <span id="sellerProfileFallback">{{ $initials }}</span>
                            @endif
                        </div>
                        <label for="sellerProfileImage" class="sa-avatar-edit" title="Change profile photo" aria-label="Change profile photo">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg>
                        </label>
                    </div>
                    <div class="sa-profile-card__identity">
                        <p class="sa-profile-name">{{ $fullName }}</p>
                        <p class="sa-profile-email">{{ $account->email }}</p>
                        <span class="sa-profile-role">Seller</span>
                    </div>
                    <div class="sa-profile-meta">
                        <div class="sa-profile-meta-row"><span class="sa-profile-meta-label">Contact</span><span class="sa-profile-meta-value">{{ $account->contact_no ?: 'Not set' }}</span></div>
                        <div class="sa-profile-meta-row"><span class="sa-profile-meta-label">Store</span><span class="sa-profile-meta-value">{{ $account->store_name ?: 'Not set' }}</span></div>
                        <div class="sa-profile-meta-row"><span class="sa-profile-meta-label">Area</span><span class="sa-profile-meta-value">{{ $account->municipality_name ?: $account->province_name ?: 'Not set' }}</span></div>
                    </div>
                </div>
            </section>

            <section class="sa-card sa-sidebar-panel">
                <header class="sa-sidebar-head"><h2>Account Overview</h2></header>
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

            </section>

        </aside>

        <section class="sa-card sa-unified-account" aria-label="Seller account settings">
            <form method="POST" action="{{ route('seller.account.update') }}" enctype="multipart/form-data" id="sellerAccountForm">
                @csrf
                @method('PATCH')
                <input id="sellerProfileImage" name="profile_image" type="file" accept="image/jpeg,image/png,image/webp" class="sa-file-input" data-has-original="{{ $profilePhotoUrl ? '1' : '0' }}">
                <input id="sellerRemoveProfileImage" name="remove_profile_image" type="hidden" value="0">
                <section class="sa-unified-section">
                    <header class="sa-section-head"><div class="sa-section-heading"><h2 class="sa-section-title">Personal Information</h2></div><span class="sa-section-line" aria-hidden="true"></span></header>
                    <div class="sa-section-body">
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
                    </div>
                </section>
                <section class="sa-unified-section sa-unified-section--bordered">
                    <header class="sa-section-head"><div class="sa-section-heading"><h2 class="sa-section-title">Account Address</h2></div><span class="sa-section-line" aria-hidden="true"></span></header>
                    <div class="sa-section-body">
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
                </section>
                <section class="sa-unified-section sa-unified-section--bordered">
                    <header class="sa-section-head"><div class="sa-section-heading"><h2 class="sa-section-title">Verification Documents</h2></div><span class="sa-section-line" aria-hidden="true"></span></header>
                <div class="sa-doc-list">
                    <div class="sa-doc-row">
<div>
                            <p class="sa-doc-name">Government Valid ID</p>
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
<div>
                            <p class="sa-doc-name">Business Permit</p>
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

                </section>
                <footer class="sa-section-footer">
                    <button type="submit" class="sa-primary">Save Account Changes</button>
                </footer>
            </form>

            <form method="POST" action="{{ route('seller.account.password.update') }}" class="sa-unified-section sa-unified-section--bordered" id="sellerPasswordForm">
                @csrf
                @method('PATCH')
                <header class="sa-section-head"><div class="sa-section-heading"><h2 class="sa-section-title">Password &amp; Security</h2></div><span class="sa-section-line" aria-hidden="true"></span></header>
                <div class="sa-section-body">
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
                </div>
                <footer class="sa-section-footer">
                    <button type="submit" class="sa-primary">Update Password</button>
                </footer>
            </form>
        </section>
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
        const profilePreview = document.getElementById('sellerProfilePreview');
        const profileFallback = document.getElementById('sellerProfileFallback');

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

            profileFallback?.setAttribute('hidden', '');

            if (removeInput) {
                removeInput.value = '0';
            }

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
