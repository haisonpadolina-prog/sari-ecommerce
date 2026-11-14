@extends('layouts.logistics')

@section('title', 'Account Management | SARI Logistics')
@section('page-title', 'Account Management')

@push('styles')
<style>
/* Buyer Account-inspired Logistics page; page-scoped to avoid shared CSS conflicts. */
#lgAccountBuyerParity {
    --ap-ink: #27313d;
    --ap-muted: #8390a0;
    --ap-line: #e5e8ed;
    --ap-soft-line: #edf0f4;
    --ap-gold: #d9950b;
    width: 100%;
    max-width: 1480px;
    margin-inline: auto;
    color: var(--ap-ink);
    font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
}
#lgAccountBuyerParity *, #lgAccountBuyerParity *::before, #lgAccountBuyerParity *::after { box-sizing: border-box; }
#lgAccountBuyerParity [hidden] { display: none !important; }
#lgAccountBuyerParity .lg-account-heading {
    display: block !important;
    margin: 0 0 21px !important;
    padding: 2px 2px 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}
#lgAccountBuyerParity .lg-account-heading::before, #lgAccountBuyerParity .lg-account-heading::after { content: none !important; }
#lgAccountBuyerParity .lg-account-eyebrow {
    margin: 0 0 6px; color: #a87316; font-size: 10.5px;
    font-weight: 800; letter-spacing: .14em; line-height: 1.3; text-transform: uppercase;
}
#lgAccountBuyerParity .lg-account-heading h1 {
    margin: 0; color: #2e343b; font-size: clamp(27px,2.25vw,31px);
    font-weight: 760; line-height: 1.08; letter-spacing: -.035em;
}
#lgAccountBuyerParity .lg-account-heading h1 span { color: #c88912; }
#lgAccountBuyerParity .lg-account-layout {
    display: grid; grid-template-columns: minmax(245px,285px) minmax(0,1fr);
    align-items: start; gap: 16px; min-width: 0;
}
#lgAccountBuyerParity .lg-account-card {
    min-width: 0; overflow: hidden;
    border: 1px solid #e5e7eb; border-radius: 17px; background: #fff;
    box-shadow: 0 3px 7px rgba(15,23,42,.045), 0 12px 30px rgba(15,23,42,.07), 0 26px 58px rgba(15,23,42,.075);
}
#lgAccountBuyerParity .lg-account-profile { position: sticky; top: 86px; }
#lgAccountBuyerParity .lg-account-card-header {
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    padding: 15px 17px; border-bottom: 1px solid var(--ap-soft-line); background: #fff;
}
#lgAccountBuyerParity .lg-account-card-header h2 {
    margin: 0; color: #293444; font-size: 14px; font-weight: 750; line-height: 1.35;
}
#lgAccountBuyerParity .lg-account-profile-badge {
    color: #8e99a6; font-size: 8.5px; font-weight: 750; letter-spacing: .05em;
    text-transform: uppercase; white-space: nowrap;
}
#lgAccountBuyerParity .lg-account-profile-body { padding: 20px 18px 18px; text-align: center; }
#lgAccountBuyerParity .lg-account-photo-wrap {
    position: relative; width: 116px; height: 116px; margin: 0 auto;
}
#lgAccountBuyerParity .lg-account-avatar {
    display: grid; width: 116px; height: 116px; overflow: hidden; place-items: center;
    border: 1px solid #dee4eb; border-radius: 19px; background: #f3f6f9;
    color: #9ba5b1; font-size: 32px; font-weight: 740;
}
#lgAccountBuyerParity .lg-account-avatar img { display: block; width: 100%; height: 100%; object-fit: cover; }
#lgAccountBuyerParity .lg-account-photo-trigger {
    position: absolute; right: -7px; bottom: -7px; display: grid; place-items: center;
    width: 35px; height: 35px; border: 3px solid white; border-radius: 11px;
    background: #c88912; color: white; cursor: pointer;
    box-shadow: 0 5px 13px rgba(200,137,18,.2);
}
#lgAccountBuyerParity .lg-account-photo-trigger:hover { background: #ad760f; }
#lgAccountBuyerParity .lg-account-photo-trigger svg { width: 16px; height: 16px; }
#lgAccountBuyerParity .lg-account-profile-name {
    margin: 17px 0 0; color: #283340; font-size: 16px; font-weight: 750; line-height: 1.35;
    overflow-wrap: anywhere;
}
#lgAccountBuyerParity .lg-account-profile-email {
    margin: 4px 0 0; color: #8591a0; font-size: 10px; line-height: 1.45;
    overflow-wrap: anywhere;
}
#lgAccountBuyerParity .lg-account-role {
    display: inline-block; margin-top: 10px; padding: 5px 10px;
    border: 1px solid #e5eaf0; border-radius: 100px; background: #f8fafc;
    color: #677586; font-size: 9px; font-weight: 700;
}
#lgAccountBuyerParity .lg-account-photo-help {
    margin: 14px auto 0; max-width: 230px; color: #98a2ae; font-size: 9px; line-height: 1.65;
}
#lgAccountBuyerParity .lg-account-photo-file { color: #66758a; overflow-wrap: anywhere; }
#lgAccountBuyerParity input[data-profile-input] {
    position: absolute !important; width: 1px !important; height: 1px !important;
    margin: -1px !important; padding: 0 !important; border: 0 !important;
    overflow: hidden !important; clip: rect(0,0,0,0) !important;
    white-space: nowrap !important; opacity: 0 !important;
}
#lgAccountBuyerParity .lg-account-meta { margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--ap-soft-line); text-align: left; }
#lgAccountBuyerParity .lg-account-meta-row {
    display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;
    padding: 10px 0; border-bottom: 1px solid #f0f2f5;
}
#lgAccountBuyerParity .lg-account-meta-row:last-child { border-bottom: 0; padding-bottom: 0; }
#lgAccountBuyerParity .lg-account-meta-row span { color: #83909e; font-size: 10px; }
#lgAccountBuyerParity .lg-account-meta-row strong {
    color: #465466; font-size: 10px; font-weight: 680; text-align: right; overflow-wrap: anywhere;
}
#lgAccountBuyerParity .lg-account-settings > .lg-account-section + .lg-account-section { border-top: 1px solid var(--ap-soft-line); }
#lgAccountBuyerParity .lg-account-section-head {
    display: flex; align-items: center; gap: 15px; padding: 18px 19px 12px;
}
#lgAccountBuyerParity .lg-account-section-head h2 {
    margin: 0; font-size: 15.5px; font-weight: 750; line-height: 1.35; color: #293444;
}
#lgAccountBuyerParity .lg-account-section-head .lg-account-head-rule { flex: 1; height: 1px; background: #e4e8ed; }
#lgAccountBuyerParity .lg-account-section-copy {
    margin: 3px 0 0; padding: 0 19px; color: #8793a2; font-size: 10px; line-height: 1.55;
}
#lgAccountBuyerParity .lg-account-fields { padding: 13px 19px 20px; }
#lgAccountBuyerParity .account-fields-grid {
    display: grid !important; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 14px;
}
#lgAccountBuyerParity .account-field { display: block; min-width: 0; }
#lgAccountBuyerParity .account-field-wide { grid-column: 1 / -1; }
#lgAccountBuyerParity .account-field > span {
    display: block; margin-bottom: 6px; font-size: 10.5px; font-weight: 700; color: #47566a;
}
#lgAccountBuyerParity .account-field input, #lgAccountBuyerParity .account-field textarea {
    display: block; width: 100%; height: 44px; min-height: 44px;
    padding: 10px 12px; border: 1px solid #d9e0e8; border-radius: 10px;
    background: white; color: #334155; font-family: inherit;
    font-size: 11.5px; line-height: 1.5; outline: none; box-shadow: none;
    transition: border-color .15s ease, box-shadow .15s ease;
}
#lgAccountBuyerParity .account-field textarea { min-height: 85px; height: auto; resize: vertical; }
#lgAccountBuyerParity .account-field :is(input,textarea):hover { border-color: #cbd4de; }
#lgAccountBuyerParity .account-field :is(input,textarea):focus { border-color: #c88912; box-shadow: 0 0 0 3px rgba(200,137,18,.08); }
#lgAccountBuyerParity .account-field-help { display: block; margin-top: 6px; color: #96a0ad; font-size: 9px; line-height: 1.55; }
#lgAccountBuyerParity .account-field-error { display: block; margin-top: 5px; color: #b45454; font-size: 9px; }
#lgAccountBuyerParity .account-security-fields { display: grid; gap: 13px; }
#lgAccountBuyerParity .account-password-field { position: relative; }
#lgAccountBuyerParity .account-password-field input { padding-right: 48px; }
#lgAccountBuyerParity .account-password-field button {
    position: absolute; top: 50%; right: 7px; transform: translateY(-50%);
    display: inline-flex; align-items: center; justify-content: center;
    width: 34px; min-width: 34px; height: 34px; padding: 0;
    border: 0; border-radius: 8px; background: transparent;
    color: #8894a3; cursor: pointer;
    transition: color .15s ease, background-color .15s ease;
}
#lgAccountBuyerParity .account-password-field button svg {
    display: block; width: 19px; height: 19px; stroke: currentColor; fill: none;
    stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
}
#lgAccountBuyerParity .account-password-field button [hidden] { display: none !important; }
#lgAccountBuyerParity .account-password-field button:hover { background: #f0f2f5; color: #556578; }
#lgAccountBuyerParity .account-password-field button:focus-visible { outline: 2px solid rgba(217,149,11,.6); outline-offset: 2px; }
#lgAccountBuyerParity .lg-account-save {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
    border-top: 1px solid var(--ap-soft-line); padding: 16px 19px 18px;
}
#lgAccountBuyerParity .lg-account-save p { margin: 0; color: #8794a2; font-size: 10px; }
#lgAccountBuyerParity .account-save-button {
    min-height: 43px; padding: 0 20px; border: 1px solid #d9950b; border-radius: 10px;
    background: #d9950b; color: white; cursor: pointer;
    font-family: inherit; font-size: 11px; font-weight: 750;
    box-shadow: 0 5px 13px rgba(217,149,11,.15);
}
#lgAccountBuyerParity .account-save-button:hover { background: #be8208; border-color: #be8208; }
#lgAccountBuyerParity .account-save-button:disabled { cursor: wait; opacity: .65; }
#lgAccountBuyerParity .account-alert {
    border-radius: 11px; margin: 0 0 14px; padding: 12px 15px; font-size: 11px; line-height: 1.5;
}
#lgAccountBuyerParity .account-alert-success { background: #f6fbf8; border: 1px solid #d9eadf; color: #447a58; }
#lgAccountBuyerParity .account-alert-danger { background: #fff7f7; border: 1px solid #efd5d5; color: #a54f4f; }
#lgAccountBuyerParity .account-alert strong { font-size: 11px; }
#lgAccountBuyerParity .account-alert p, #lgAccountBuyerParity .account-alert li { margin-top: 4px; font-size: 10px; }
#lgAccountBuyerParity .lg-account-support { margin-top: 17px; }
#lgAccountBuyerParity .lg-account-support .lg-account-card-header { border-bottom: 1px solid var(--ap-soft-line); }
#lgAccountBuyerParity .lg-account-support .lg-account-card-header p { margin: 4px 0 0; color: #8995a2; font-size: 10px; }
#lgAccountBuyerParity .account-support-component { padding: 17px 19px 20px; }
#lgAccountBuyerParity .account-support-component > * { max-width: none !important; margin-top: 0 !important; }
#lgAccountBuyerParity .lg-account-no-account { padding: 45px 20px; text-align: center; }
#lgAccountBuyerParity .lg-account-no-account svg { width: 56px; height: 56px; margin: 0 auto 14px; color: #b1bac4; }
#lgAccountBuyerParity .lg-account-no-account h2 { margin: 0; font-size: 21px; color: #778595; }
#lgAccountBuyerParity .lg-account-no-account p { max-width: 470px; margin: 9px auto 0; font-size: 11px; color: #8a96a4; line-height: 1.6; }
#lgAccountBuyerParity :is(button,input,textarea,a):focus-visible { outline: 2px solid rgba(200,137,18,.6); outline-offset: 2px; }
@media(max-width:1000px) { #lgAccountBuyerParity .lg-account-layout { grid-template-columns: minmax(230px,260px) minmax(0,1fr); } }
@media(max-width:760px) {
    #lgAccountBuyerParity .lg-account-layout { grid-template-columns: 1fr; }
    #lgAccountBuyerParity .lg-account-profile { position: static; top: auto; }
    #lgAccountBuyerParity .lg-account-profile-body { padding: 18px 17px; }
    #lgAccountBuyerParity .lg-account-heading h1 { font-size: 25px; }
    #lgAccountBuyerParity .lg-account-eyebrow { font-size: 9.5px; }
}
@media(max-width:520px) {
    #lgAccountBuyerParity .account-fields-grid { grid-template-columns: 1fr; }
    #lgAccountBuyerParity .account-field-wide { grid-column: auto; }
    #lgAccountBuyerParity .lg-account-section-head { padding: 16px 15px 11px; }
    #lgAccountBuyerParity .lg-account-fields { padding: 10px 15px 18px; }
    #lgAccountBuyerParity .lg-account-section-copy { padding-inline: 15px; }
    #lgAccountBuyerParity .lg-account-save { padding: 15px; align-items: stretch; }
    #lgAccountBuyerParity .account-save-button { width: 100%; }
    #lgAccountBuyerParity .account-support-component { padding: 14px; }
}
@media(prefers-reduced-motion:reduce) { #lgAccountBuyerParity *, #lgAccountBuyerParity *::before, #lgAccountBuyerParity *::after { transition: none !important; animation: none !important; } }

/* Compact icon trigger and accessible floating support modal (account-page only). */
#lgAccountBuyerParity .lg-account-heading-with-help {
    display: flex !important; align-items: center; justify-content: space-between;
    gap: 14px; width: 100%;
}
#lgAccountBuyerParity .lg-account-help-trigger,
#lgAccountBuyerParity .lg-account-support-close {
    display: inline-flex !important; align-items: center; justify-content: center;
    flex: 0 0 auto; width: 40px; height: 40px; padding: 0 !important;
    border: 1px solid #dfe5ec !important; border-radius: 11px !important;
    background: #fff !important; color: #728093 !important;
    box-shadow: 0 3px 8px rgba(25,35,50,.035) !important; cursor: pointer;
    transition: background-color .17s ease, color .17s ease, border-color .17s ease;
}
#lgAccountBuyerParity .lg-account-help-trigger:hover,
#lgAccountBuyerParity .lg-account-support-close:hover {
    background: #f4f6f9 !important; color: #344256 !important; border-color: #cdd6e1 !important;
}
#lgAccountBuyerParity :is(.lg-account-help-trigger,.lg-account-support-close) svg {
    display: block; width: 20px; height: 20px;
}
#lgAccountBuyerParity .lg-account-support-modal {
    position: fixed !important; inset: 0 !important; z-index: 10060 !important;
    display: flex !important; align-items: center; justify-content: center;
    padding: 18px !important; margin: 0 !important; width: auto !important; height: auto !important;
    border: 0 !important; border-radius: 0 !important; box-shadow: none !important;
    background: transparent !important; visibility: hidden; opacity: 0;
    pointer-events: none; transition: opacity .21s ease, visibility .21s ease;
}
#lgAccountBuyerParity .lg-account-support-modal.is-open {
    visibility: visible; opacity: 1; pointer-events: auto;
}
#lgAccountBuyerParity .lg-account-support-backdrop {
    position: absolute; inset: 0; background: rgba(24,33,45,.52);
    backdrop-filter: blur(3px);
}
#lgAccountBuyerParity .lg-account-support-panel {
    position: relative; z-index: 1; display: flex; flex-direction: column;
    width: min(100%, 540px); max-height: min(90dvh, 740px);
    overflow: auto; overscroll-behavior: contain;
    border: 1px solid #e5e9ef; border-radius: 18px; background: #fff;
    box-shadow: 0 28px 75px rgba(13,24,38,.22);
    transform: translateY(13px) scale(.985);
    transition: transform .24s cubic-bezier(.22,1,.36,1);
}
#lgAccountBuyerParity .lg-account-support-modal.is-open .lg-account-support-panel {
    transform: translateY(0) scale(1);
}
#lgAccountBuyerParity .lg-account-support-modal-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 12px; padding: 20px 22px 17px; border-bottom: 1px solid #edf0f4;
    background: white;
}
#lgAccountBuyerParity .lg-account-support-overline {
    margin: 0 0 5px; color: #b27b16; font-size: 9px; font-weight: 750;
    letter-spacing: .12em;
}
#lgAccountBuyerParity .lg-account-support-modal-header h2 {
    margin: 0; font-size: 19px; font-weight: 750; line-height: 1.3; color: #293444;
}
#lgAccountBuyerParity .lg-account-support-modal-header p:not(.lg-account-support-overline) {
    margin: 6px 0 0; color: #8390a0; font-size: 11px; line-height: 1.55;
}
#lgAccountBuyerParity .lg-account-support-close {
    width: 34px; height: 34px; box-shadow: none !important;
}
#lgAccountBuyerParity .lg-account-support-close svg { width: 18px; height: 18px; }
#lgAccountBuyerParity .lg-account-support-modal .account-support-component {
    padding: 20px 22px 22px !important;
}
#lgAccountBuyerParity .lg-account-support-modal .account-support-component > section {
    max-width: none !important; margin: 0 !important; padding: 0 !important;
    border: 0 !important; border-radius: 0 !important;
    background: transparent !important; box-shadow: none !important;
}
#lgAccountBuyerParity .lg-account-support-modal .account-support-component h3 {
    font-size: 14px !important; font-weight: 700 !important; color: #354256 !important;
}
#lgAccountBuyerParity .lg-account-support-modal .account-support-component section > p {
    font-size: 10px !important; line-height: 1.6; color: #8290a0 !important;
}
#lgAccountBuyerParity .lg-account-support-modal .account-support-component form {
    display: grid !important; gap: 13px !important; margin-top: 16px !important;
}
#lgAccountBuyerParity .lg-account-support-modal .account-support-component :is(input,textarea) {
    display: block; width: 100%; min-height: 43px;
    padding: 11px 13px !important; border: 1px solid #dce3ea !important;
    border-radius: 10px !important; background: white !important;
    color: #334155 !important; font: inherit !important; font-size: 12px !important;
    line-height: 1.5; outline: none;
}
#lgAccountBuyerParity .lg-account-support-modal .account-support-component textarea { min-height: 105px; resize: vertical; }
#lgAccountBuyerParity .lg-account-support-modal .account-support-component :is(input,textarea):focus {
    border-color: #c88912 !important; box-shadow: 0 0 0 3px rgba(200,137,18,.09);
}
#lgAccountBuyerParity .lg-account-support-modal .account-support-component form button {
    min-height: 42px !important; padding: 0 18px !important;
    border: 1px solid #d9950b !important; border-radius: 10px !important;
    background: #d9950b !important; color: #fff !important;
    font: inherit !important; font-size: 11px !important; font-weight: 700 !important;
    cursor: pointer;
}
#lgAccountBuyerParity .lg-account-support-modal .account-support-component form button:hover {
    background: #c4870a !important;
}
@media(max-width:520px) {
    #lgAccountBuyerParity .lg-account-support-modal { padding: 12px !important; }
    #lgAccountBuyerParity .lg-account-support-panel { max-height: 88dvh; border-radius: 15px; }
    #lgAccountBuyerParity .lg-account-support-modal-header { padding: 17px 16px 14px; }
    #lgAccountBuyerParity .lg-account-support-modal .account-support-component { padding: 17px 16px 19px !important; }
}
@media(prefers-reduced-motion: reduce) {
    #lgAccountBuyerParity .lg-account-support-modal,
    #lgAccountBuyerParity .lg-account-support-panel { transition: none !important; }
}
</style>
@endpush

@section('content')
<div id="lgAccountBuyerParity" class="sari-logistics-ui account-page">
    <header class="lg-account-heading lg-account-heading-with-help">
        <div>
            <p class="lg-account-eyebrow">Account Management</p>
            <h1>Logistics <span>Profile</span></h1>
        </div>
        <button type="button" class="lg-account-help-trigger" data-account-support-open
                aria-label="Open account help and support" aria-haspopup="dialog"
                aria-controls="lgAccountSupportModal" title="Help & Support">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="9"/><path d="M9.4 9a2.6 2.6 0 0 1 5.2.5c0 2-2.6 2.3-2.6 4.5"/>
                <path d="M12 17h.01"/>
            </svg>
        </button>
    </header>

    @if(session('success'))
        <div class="account-alert account-alert-success" role="status">
            <strong>Account updated</strong><p>{{ session('success') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="account-alert account-alert-danger" role="alert">
            <strong>Please review your account details</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($account)
        <form method="POST" action="{{ route('logistics.account-management.update') }}"
            enctype="multipart/form-data" class="account-form" id="logisticsAccountForm">
            @csrf
            @method('PATCH')

            <div class="lg-account-layout">
                {{-- Buyer-style left profile card; profile_image remains the original Laravel field. --}}
                <aside class="lg-account-card lg-account-profile">
                    <div class="lg-account-card-header">
                        <h2>Profile</h2><span class="lg-account-profile-badge">Logistics Account</span>
                    </div>
                    <div class="lg-account-profile-body">
                        <div class="lg-account-photo-wrap">
                            <div class="lg-account-avatar">
                                @if($account->profile_image_path)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($account->profile_image_path) }}"
                                        alt="Current Logistics profile photo" data-profile-preview>
                                @else
                                    <img alt="Selected Logistics profile photo" data-profile-preview hidden>
                                @endif
                                <span data-profile-fallback @if($account->profile_image_path) hidden @endif>{{ mb_strtoupper(mb_substr(trim((string) ($account->first_name ?: 'L')),0,1)) }}</span>
                            </div>
                            <button type="button" class="lg-account-photo-trigger" data-photo-trigger aria-label="Change profile photo" title="Change profile photo">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4z" />
                                </svg>
                            </button>
                        </div>
                        <input id="logisticsProfilePhotoInput" type="file" name="profile_image"
                            accept="image/jpeg,image/png,image/webp" class="sr-only" data-profile-input tabindex="-1">
                        <p class="lg-account-profile-name" data-profile-name>{{ $account->displayName() }}</p>
                        <p class="lg-account-profile-email" data-profile-email>{{ $account->email }}</p>
                        <span class="lg-account-role">Logistics Manager</span>
                        <p class="lg-account-photo-help">Select the pencil icon to change your photo. JPG, PNG or WEBP, up to 5 MB.</p>
                        <p class="lg-account-photo-help lg-account-photo-file" data-photo-filename aria-live="polite"></p>
                        <div class="lg-account-meta">
                            <div class="lg-account-meta-row"><span>Contact</span><strong data-profile-contact>{{ $account->contact_no ?: 'Not provided' }}</strong></div>
                            <div class="lg-account-meta-row"><span>Account ID</span><strong>#{{ $account->id }}</strong></div>
                        </div>
                    </div>
                </aside>

                {{-- Buyer-style one unified account container; original forms unchanged. --}}
                <div class="lg-account-card lg-account-settings">
                    <div class="lg-account-section">
                        <div class="lg-account-section-head"><h2>Basic information</h2><span class="lg-account-head-rule" aria-hidden="true"></span></div>
                        <div class="lg-account-fields">
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
                    </div>
                    <div class="lg-account-section">
                        <div class="lg-account-section-head"><h2>Change password</h2><span class="lg-account-head-rule" aria-hidden="true"></span></div>
                        <p class="lg-account-section-copy">Leave these fields blank if you don't want to change your password.</p>
                        <div class="lg-account-fields">
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
                                            aria-pressed="false"
                                            aria-label="Show current password"
                                         title="Show password">
                                            <svg data-eye-show viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6-10-6-10-6Z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            <svg data-eye-hide viewBox="0 0 24 24" aria-hidden="true" hidden>
                                                <path d="M3 3l18 18M9.9 6.3A10.9 10.9 0 0 1 12 6c6.4 0 10 6 10 6a15.9 15.9 0 0 1-4 4.4M6.6 6.7C3.8 8.6 2 12 2 12s3.6 6 10 6c1.6 0 3-.3 4.2-.9"/>
                                                <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                                            </svg>
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
                                            aria-pressed="false"
                                            aria-label="Show new password"
                                         title="Show password">
                                            <svg data-eye-show viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6-10-6-10-6Z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            <svg data-eye-hide viewBox="0 0 24 24" aria-hidden="true" hidden>
                                                <path d="M3 3l18 18M9.9 6.3A10.9 10.9 0 0 1 12 6c6.4 0 10 6 10 6a15.9 15.9 0 0 1-4 4.4M6.6 6.7C3.8 8.6 2 12 2 12s3.6 6 10 6c1.6 0 3-.3 4.2-.9"/>
                                                <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                                            </svg>
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
                                            aria-pressed="false"
                                            aria-label="Show password confirmation"
                                         title="Show password">
                                            <svg data-eye-show viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6-10-6-10-6Z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            <svg data-eye-hide viewBox="0 0 24 24" aria-hidden="true" hidden>
                                                <path d="M3 3l18 18M9.9 6.3A10.9 10.9 0 0 1 12 6c6.4 0 10 6 10 6a15.9 15.9 0 0 1-4 4.4M6.6 6.7C3.8 8.6 2 12 2 12s3.6 6 10 6c1.6 0 3-.3 4.2-.9"/>
                                                <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                                            </svg>
                                        </button>

                                    </div>

                                </label>

                            </div>
                        </div>
                    </div>
                    <div class="lg-account-save">
                        <p>Your profile and security changes will be saved to your Logistics account.</p>
                        <button type="submit" class="account-save-button" data-account-submit>Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="lg-account-card lg-account-no-account">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v6m0 4h.01"/></svg>
            <h2>No editable Logistics account</h2>
            <p>You are currently using the temporary Logistics test login. Register and approve a real Logistics account before editing profile information.</p>
        </div>
    @endif

    {{-- Support form remains unchanged; this modal only changes its presentation. --}}
    <div id="lgAccountSupportModal" class="lg-account-support-modal" data-account-support-modal
         role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="lgAccountSupportTitle">
        <div class="lg-account-support-backdrop" data-account-support-dismiss></div>
        <div class="lg-account-support-panel" tabindex="-1" data-account-support-panel>
            <header class="lg-account-support-modal-header">
                <div>
                    <p class="lg-account-support-overline">LOGISTICS SUPPORT</p>
                    <h2 id="lgAccountSupportTitle">Help & Support</h2>
                    <p>Report an account or Logistics-related issue to the SARI support team.</p>
                </div>
                <button type="button" class="lg-account-support-close" data-account-support-close
                        aria-label="Close help and support" title="Close">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18M6 6l12 12"/>
                    </svg>
                </button>
            </header>
            <div class="account-support-component">
                @include('components.support.complaint-form')
            </div>
        </div>
    </div>
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


                        const eyeShow = button.querySelector('[data-eye-show]');
                        const eyeHide = button.querySelector('[data-eye-hide]');
                        if (eyeShow) eyeShow.toggleAttribute('hidden', !showing);
                        if (eyeHide) eyeHide.toggleAttribute('hidden', showing);
                        button.setAttribute('aria-pressed', String(!showing));
                        const action = showing ? 'Show' : 'Hide';
                        const fieldName = wrapper?.closest('label')?.querySelector('span')?.textContent?.trim() || 'password';
                        button.setAttribute('aria-label', `${action} ${fieldName}`);
                        button.title = `${action} password`;

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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const page = document.getElementById('lgAccountBuyerParity');
    if (!page) return;
    const fileInput = page.querySelector('[data-profile-input]');
    const preview = page.querySelector('[data-profile-preview]');
    const fallback = page.querySelector('[data-profile-fallback]');
    const fileName = page.querySelector('[data-photo-filename]');
    const trigger = page.querySelector('[data-photo-trigger]');
    let objectUrl = null;
    trigger?.addEventListener('click', () => fileInput?.click());
    fileInput?.addEventListener('change', () => {
        const file = fileInput.files?.[0];
        if (!file) return;
        if (!['image/jpeg','image/png','image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) {
            fileInput.value = '';
            if (fileName) fileName.textContent = 'Choose JPG, PNG or WEBP under 5 MB.';
            return;
        }
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = URL.createObjectURL(file);
        if (preview) { preview.src = objectUrl; preview.hidden = false; }
        if (fallback) fallback.hidden = true;
        if (fileName) fileName.textContent = file.name;
    });
    window.addEventListener('pagehide', () => { if (objectUrl) URL.revokeObjectURL(objectUrl); }, {once:true});
});
</script>

<script>
(() => {
    'use strict';
    const initSupportModal = () => {
        const root = document.getElementById('lgAccountBuyerParity');
        const modal = root?.querySelector('[data-account-support-modal]');
        const trigger = root?.querySelector('[data-account-support-open]');
        const panel = modal?.querySelector('[data-account-support-panel]');
        if (!root || !modal || !trigger || !panel || modal.dataset.initialized) return;
        modal.dataset.initialized = 'true';
        let priorFocus = null;
        let previousOverflow = '';
        const focusable = () => [...modal.querySelectorAll('button:not([disabled]),input:not([disabled]),textarea:not([disabled]),a[href]')]
            .filter(el => el.getClientRects().length);
        const open = () => {
            if (modal.classList.contains('is-open')) return;
            priorFocus = document.activeElement;
            previousOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
            modal.setAttribute('aria-hidden','false');
            modal.classList.add('is-open');
            const target = modal.querySelector('[data-account-support-close]') || panel;
            requestAnimationFrame(() => requestAnimationFrame(() => target.focus({preventScroll:true})));
        };
        const close = () => {
            if (!modal.classList.contains('is-open')) return;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden','true');
            document.body.style.overflow = previousOverflow;
            (priorFocus?.isConnected ? priorFocus : trigger).focus({preventScroll:true});
        };
        trigger.addEventListener('click', open);
        modal.querySelector('[data-account-support-close]')?.addEventListener('click', close);
        modal.querySelector('[data-account-support-dismiss]')?.addEventListener('click', close);
        modal.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') { event.preventDefault(); close(); return; }
            if (event.key !== 'Tab') return;
            const items = focusable();
            if (!items.length) { event.preventDefault(); panel.focus(); return; }
            const first = items[0], last = items[items.length-1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        });
        // Reopen after a server-side complaint form validation error.
        @if($errors->has('subject') || $errors->has('description'))
            open();
            const supportForm = modal.querySelector('form');
            if (supportForm) {
                if (supportForm.elements.subject) supportForm.elements.subject.value = @json(old('subject', ''));
                if (supportForm.elements.description) supportForm.elements.description.value = @json(old('description', ''));
            }
        @endif
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initSupportModal);
    else initSupportModal();
})();
</script>
@endpush
