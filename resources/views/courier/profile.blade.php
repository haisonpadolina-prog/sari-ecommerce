@extends('layouts.courier')

@section('title', 'Rider Account')
@section('header-title', 'Account')
@section('header-subtitle', 'Rider Profile & Vehicle')

@push('styles')
<style>
@verbatim
/* SARI Rider Account: adapted from the existing Buyer Account Management design. */
body:has(#sariRiderAccount),
body:has(#sariRiderAccount) #courierMain {
    background: #f4f5f7 !important;
    color: #202938;
}
body:has(#sariRiderAccount) #courierSidebar {
    background: #fff !important;
    border-right-color: #e5e7eb !important;
}
body:has(#sariRiderAccount) #courierMain > header {
    background: rgba(255,255,255,.97) !important;
    border-bottom: 1px solid #e5e7eb !important;
    box-shadow: none !important;
}
#courierMain > main:has(#sariRiderAccount) {
    background: transparent !important;
    padding: 22px clamp(14px,2.2vw,30px) 42px !important;
}
#sariRiderAccount {
    --ra-gold: #c88912;
    --ra-gold-dark: #aa730f;
    --ra-ink: #202938;
    --ra-body: #596579;
    --ra-muted: #8490a2;
    --ra-line: #e5e7eb;
    --ra-line-soft: #edf1f5;
    --ra-soft: #f7f9fb;
    --ra-shadow: 0 3px 7px rgba(15,23,42,.055),
                 0 14px 32px rgba(15,23,42,.095),
                 0 30px 68px rgba(15,23,42,.11);
    width: 100%;
    max-width: 1380px;
    margin: 0 auto;
    color: var(--ra-ink);
    font: 400 13px/1.6 'Poppins',ui-sans-serif,system-ui,sans-serif;
}
#sariRiderAccount,
#sariRiderAccount *,
#sariRiderAccount *::before,
#sariRiderAccount *::after { box-sizing: border-box; }
#sariRiderAccount :is(input,select,textarea,button) { font-family: inherit; }
#sariRiderAccount :is(button,input,select):focus-visible { outline: 2px solid var(--ra-gold); outline-offset: 3px; }
#sariRiderAccount svg { display:block; flex:none; }
#sariRiderAccount [hidden] { display:none !important; }
#sariRiderAccount .ra-sr-only {
    position:absolute !important; width:1px !important; height:1px !important; padding:0 !important;
    margin:-1px !important; overflow:hidden !important; clip:rect(0,0,0,0) !important;
    white-space:nowrap !important; border:0 !important;
}

/* Buyer Account Management exact heading structure and sizing.
   Based on the final .sa-page-head/.sa-eyebrow/.sa-page-title overrides in Buyer Account. */
#courierMain #sariRiderAccount .sa-page-head {
    padding: 2px 2px 14px !important;
    font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif !important;
}
#courierMain #sariRiderAccount .sa-eyebrow {
    margin: 0 !important;
    color: #a87316 !important;
    font-size: 10.5px !important;
    font-weight: 800 !important;
    line-height: 1.2 !important;
    letter-spacing: .14em !important;
    text-transform: uppercase !important;
}
#courierMain #sariRiderAccount .sa-page-title {
    margin: 5px 0 0 !important;
    color: #2e343b !important;
    font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif !important;
    font-size: clamp(27px, 2.25vw, 31px) !important;
    font-weight: 760 !important;
    line-height: 1.04 !important;
    letter-spacing: -.035em !important;
}
#courierMain #sariRiderAccount .sa-page-title span {
    color: #c88912 !important;
}
@media (max-width: 640px) {
    #courierMain #sariRiderAccount .sa-eyebrow { font-size: 9.5px !important; }
    #courierMain #sariRiderAccount .sa-page-title { font-size: 25px !important; }
}
#sariRiderAccount .ra-feedback {
    margin:0 0 14px; border:1px solid #dfe5eb; border-radius:11px;
    background:#fff; padding:11px 14px; font-size:11.5px; line-height:1.55;
}
#sariRiderAccount .ra-feedback--success { color:#27754e; background:#f6fbf8; border-color:#cce8d6; }
#sariRiderAccount .ra-feedback--error { color:#a34850; background:#fff8f8; border-color:#eecfd2; }

/* LEFT: Rider profile preview, RIGHT: one unified information surface. */
#sariRiderAccount .ra-layout { display:grid; grid-template-columns:286px minmax(0,1fr); gap:14px; align-items:start; }
#sariRiderAccount .ra-left { position:sticky; top:84px; display:grid; gap:14px; min-width:0; }
#sariRiderAccount .ra-card {
    min-width:0; overflow:hidden; border:1px solid var(--ra-line);
    border-radius:17px; background:#fff; box-shadow:var(--ra-shadow);
}
#sariRiderAccount .ra-profile-head {
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    padding:15px 17px; border-bottom:1px solid var(--ra-line-soft);
}
#courierMain #sariRiderAccount .ra-card-title {
    margin:0; color:#2b3441; font-size:16px; font-weight:750; line-height:1.3;
}
#sariRiderAccount .ra-profile-type {
    color:#98a2ae; font-size:9px; font-weight:800; letter-spacing:.08em; text-transform:uppercase;
}
#sariRiderAccount .ra-profile-body { padding:21px 17px 18px; }
#sariRiderAccount .ra-avatar-wrap { position:relative; width:118px; height:118px; margin:0 auto; }
#sariRiderAccount .ra-avatar {
    display:grid; place-items:center; width:118px; height:118px; overflow:hidden;
    border:1px solid #dbe1e8; border-radius:20px; background:#f4f6f8;
    color:#919ba8; font-size:30px; font-weight:700; letter-spacing:-.05em;
}
#sariRiderAccount .ra-avatar img { width:100%; height:100%; object-fit:cover; }
#sariRiderAccount .ra-avatar-edit {
    position:absolute; right:-7px; bottom:-7px; z-index:2;
    display:grid; place-items:center; width:36px; height:36px;
    border:3px solid #fff; border-radius:11px; background:var(--ra-gold);
    color:#fff; cursor:pointer; box-shadow:0 5px 13px rgba(200,137,18,.2);
    transition:background-color .14s ease;
}
#sariRiderAccount .ra-avatar-edit:hover,
#sariRiderAccount .ra-avatar-edit:focus-visible { background:var(--ra-gold-dark); }
#sariRiderAccount .ra-avatar-edit svg { width:15px; height:15px; stroke:currentColor; stroke-width:1.8; fill:none; stroke-linecap:round; stroke-linejoin:round; }
#sariRiderAccount .ra-profile-name {
    margin:19px 0 0; color:#242d38; font-size:18px; font-weight:740;
    letter-spacing:-.025em; line-height:1.3; text-align:center; overflow-wrap:anywhere;
}
#sariRiderAccount .ra-profile-email {
    margin:5px 0 0; color:#8993a0; font-size:11px; line-height:1.45;
    text-align:center; overflow-wrap:anywhere;
}
#sariRiderAccount .ra-availability-pill {
    display:table; margin:11px auto 0; padding:5px 11px;
    border:1px solid #e1e5ea; border-radius:999px;
    background:#f8f9fb; color:#697586; font-size:10px; font-weight:700;
}
#sariRiderAccount .ra-availability-pill--online { background:#ecf9f1; border-color:#c9ead5; color:#238052; }
#sariRiderAccount .ra-profile-sub {
    margin:11px 0 0; text-align:center; color:#84909e; font-size:10.5px; line-height:1.65;
    overflow-wrap:anywhere;
}
#sariRiderAccount .ra-profile-file-status {
    display:block; margin:10px 0 0; color:#9aa3ad; font-size:10px;
    line-height:1.5; text-align:center; overflow-wrap:anywhere;
}
#sariRiderAccount .ra-profile-file-status.is-error { color:#b54d56; }
#sariRiderAccount .ra-meta {
    display:grid; gap:0; margin-top:17px; padding-top:9px;
    border-top:1px solid var(--ra-line-soft);
}
#sariRiderAccount .ra-meta-row {
    display:flex; align-items:flex-start; justify-content:space-between; gap:12px;
    padding:8px 0; min-width:0;
}
#sariRiderAccount .ra-meta-label { color:#96a0ad; font-size:10.5px; font-weight:550; }
#sariRiderAccount .ra-meta-value {
    color:#485465; font-size:11px; font-weight:650; text-align:right;
    max-width:63%; overflow-wrap:anywhere;
}
#sariRiderAccount .ra-side-section { padding:16px 17px 18px; }
#sariRiderAccount .ra-side-title { margin:0; color:#323d4c; font-size:14px; font-weight:740; }
#sariRiderAccount .ra-side-copy { margin:4px 0 13px; color:#8a95a2; font-size:10.5px; line-height:1.55; }
#sariRiderAccount .ra-form-label { display:block; margin:0 0 6px; color:#40506a; font-size:11.5px; font-weight:690; }
#sariRiderAccount .ra-select-shell { position:relative; }
#sariRiderAccount .ra-select-shell::after {
    content:''; position:absolute; top:50%; right:16px; width:8px; height:8px;
    border-right:1.8px solid #6a7380; border-bottom:1.8px solid #6a7380;
    transform:translateY(-65%) rotate(45deg); pointer-events:none;
}
#sariRiderAccount .ra-control {
    display:block; width:100%; min-width:0; min-height:45px; padding:10px 13px;
    border:1px solid #d7dee7; border-radius:11px; background:#fff;
    color:#2e3948; font-size:12px; font-weight:500; line-height:1.5;
    outline:none; transition:border-color .14s ease,box-shadow .14s ease,background-color .14s ease;
}
#sariRiderAccount select.ra-control {
    appearance:none; -webkit-appearance:none; -moz-appearance:none;
    padding-right:42px; cursor:pointer;
}
#sariRiderAccount .ra-control:hover:not(:disabled) { border-color:#c9d1db; }
/* Clean, thin gold focus border: no thick gold halo or offset outline. */
#sariRiderAccount .ra-control:focus,
#sariRiderAccount .ra-control:focus-visible { border-color:var(--ra-gold); box-shadow:none; outline:none; outline-offset:0; }
#sariRiderAccount .ra-control::placeholder { color:#a0a9b6; }
#sariRiderAccount .ra-control:disabled,
#sariRiderAccount .ra-control[readonly] { color:#697586; background:#f7f9fb; border-color:#e3e7ec; }
#sariRiderAccount .ra-button {
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    min-height:42px; padding:0 16px; border:1px solid var(--ra-gold);
    border-radius:10px; background:var(--ra-gold); color:#fff;
    font-size:11.5px; font-weight:720; cursor:pointer;
    box-shadow:0 5px 13px rgba(200,137,18,.12);
    transition:background-color .14s ease,border-color .14s ease;
}
#sariRiderAccount .ra-button:hover { background:var(--ra-gold-dark); border-color:var(--ra-gold-dark); }
#sariRiderAccount .ra-button svg { width:15px; height:15px; fill:none; stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }
#sariRiderAccount .ra-button--full { width:100%; margin-top:12px; }

/* RIGHT: one unified form card with Buyer-style section separators. */
#sariRiderAccount .ra-unified-section + .ra-unified-section { border-top:1px solid var(--ra-line-soft); }
#sariRiderAccount .ra-section-heading { display:flex; align-items:flex-start; gap:16px; padding:19px 21px 13px; }
#courierMain #sariRiderAccount .ra-section-title {
    margin:0; color:#273140; font-size:16px; font-weight:760;
    letter-spacing:-.018em; line-height:1.3; white-space:nowrap;
}
#sariRiderAccount .ra-section-rule { height:1px; flex:1; margin-top:11px; background:#e2e7ed; }
#sariRiderAccount .ra-section-body { padding:0 21px 21px; }
#sariRiderAccount .ra-fields { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
#sariRiderAccount .ra-field { display:block; min-width:0; }
#sariRiderAccount .ra-field--full { grid-column:1/-1; }
#sariRiderAccount .ra-required { color:#c75a5a; }
#sariRiderAccount .ra-help { margin:5px 0 0; color:#929ca9; font-size:10px; line-height:1.5; }
#sariRiderAccount .ra-field-error { display:block; margin:5px 0 0; color:#b24754; font-size:10.5px; line-height:1.45; }
#sariRiderAccount .ra-control[aria-invalid="true"] { border-color:#d77f89; }
#sariRiderAccount .ra-control[aria-invalid="true"]:focus { box-shadow:0 0 0 3px rgba(199,90,90,.10); }

#sariRiderAccount .ra-readonly {
    display:flex; justify-content:space-between; align-items:center; gap:10px;
    min-height:45px; padding:9px 12px; border:1px solid #e3e7ec;
    border-radius:11px; background:#f7f9fb;
}
#sariRiderAccount .ra-readonly-value { color:#647183; font-size:11.5px; overflow-wrap:anywhere; }
#sariRiderAccount .ra-readonly-badge {
    border:1px solid #e1e5ea; border-radius:999px; background:#fff; padding:3px 8px;
    color:#8993a0; font-size:9px; font-weight:700; white-space:nowrap;
}
#sariRiderAccount .ra-form-footer {
    display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;
    gap:13px; border-top:1px solid var(--ra-line-soft);
    background:#fbfcfd; padding:15px 21px;
}
#sariRiderAccount .ra-save-note { margin:0; color:#8d97a4; font-size:10px; line-height:1.5; }

/* The existing platform complaint form stays functional, styled to match the Buyer surface. */
#sariRiderAccount .ra-support-wrapper > section {
    margin:0 !important; padding:16px 17px 17px !important;
    border:1px solid var(--ra-line) !important; border-radius:17px !important;
    background:#fff !important; box-shadow:var(--ra-shadow);
}
#sariRiderAccount .ra-support-wrapper h3 { color:#323d4c !important; font-size:14px !important; font-weight:740 !important; }
#sariRiderAccount .ra-support-wrapper p { font-size:10.5px !important; color:#8a95a2 !important; }
#sariRiderAccount .ra-support-wrapper form { gap:10px !important; margin-top:13px !important; }
#sariRiderAccount .ra-support-wrapper :is(input,textarea) {
    width:100%; min-height:43px; padding:10px 12px !important;
    border:1px solid #d7dee7 !important; border-radius:10px !important;
    background:#fff !important; color:#2e3948 !important;
    font-size:11px !important; outline:0 !important;
}
#sariRiderAccount .ra-support-wrapper textarea { min-height:90px !important; resize:vertical; }
#sariRiderAccount .ra-support-wrapper :is(input,textarea):focus {
    border-color:var(--ra-gold) !important;
    box-shadow:0 0 0 3px rgba(200,137,18,.08) !important;
}
#sariRiderAccount .ra-support-wrapper form button {
    width:100%; min-height:40px; background:#343d49 !important;
    border-radius:10px !important; color:#fff !important;
    font-size:11px !important; font-weight:700 !important;
}
#sariRiderAccount .ra-support-wrapper form button:hover { background:#252c34 !important; }

@media (max-width:1020px) {
    #sariRiderAccount .ra-layout { grid-template-columns:245px minmax(0,1fr); }
}
@media (max-width:760px) {
    #sariRiderAccount .ra-layout { grid-template-columns:1fr; }
    #sariRiderAccount .ra-left { position:static; }
    #sariRiderAccount .ra-profile-body {
        display:grid; grid-template-columns:118px minmax(0,1fr); gap:12px 18px; align-items:center;
    }
    #sariRiderAccount .ra-avatar-wrap { margin:0; }
    #sariRiderAccount .ra-profile-name,
    #sariRiderAccount .ra-profile-email,
    #sariRiderAccount .ra-profile-sub { text-align:left; }
    #sariRiderAccount .ra-profile-name { margin-top:0; }
    #sariRiderAccount .ra-availability-pill { margin-left:0; margin-right:auto; }
    #sariRiderAccount .ra-meta,
    #sariRiderAccount .ra-profile-file-status { grid-column:1/-1; }
    #sariRiderAccount .ra-profile-sub { grid-column:2; }
}
/* Exact Buyer Account header breakpoint: 640px (not 540px). */
@media (max-width:640px) {
}
@media (max-width:540px) {
    #courierMain > main:has(#sariRiderAccount) { padding:16px 12px 28px !important; }
    #sariRiderAccount .ra-fields { grid-template-columns:1fr; gap:13px; }
    #sariRiderAccount .ra-field--full { grid-column:auto; }
    #sariRiderAccount .ra-section-heading { padding:17px 15px 13px; }
    #sariRiderAccount .ra-section-body { padding:0 15px 18px; }
    #sariRiderAccount .ra-form-footer { padding:15px; flex-direction:column; align-items:stretch; }
    #sariRiderAccount .ra-form-footer .ra-button { width:100%; }
    #sariRiderAccount .ra-profile-body { grid-template-columns:96px minmax(0,1fr); gap:10px 14px; }
    #sariRiderAccount .ra-avatar-wrap,
    #sariRiderAccount .ra-avatar { width:96px; height:96px; }
    #sariRiderAccount .ra-avatar-edit { right:-5px; bottom:-5px; }
    #sariRiderAccount .ra-profile-name { font-size:16px; }
    #sariRiderAccount .ra-card { border-radius:14px; box-shadow:0 2px 4px rgba(15,23,42,.035),0 8px 18px rgba(15,23,42,.06),0 18px 38px rgba(15,23,42,.07); }
}
@media (prefers-reduced-motion:reduce) {
    #sariRiderAccount *,#sariRiderAccount *::before,#sariRiderAccount *::after { transition-duration:.01ms !important; }
}

/* Keep Current Password the same width as the two password fields below on desktop.
   Mobile already collapses .ra-fields to one column. */
/* Password visibility: scoped; preserves existing field layout. */
#sariRiderAccount .ra-password-wrap { position:relative; }
#sariRiderAccount .ra-password-wrap .ra-control { padding-right:48px; }
#sariRiderAccount .ra-password-toggle { position:absolute; right:7px; top:50%; transform:translateY(-50%); display:grid; place-items:center; width:34px; height:34px; padding:0; border:0; border-radius:8px; background:transparent; color:#697586; cursor:pointer; }
#sariRiderAccount .ra-password-toggle:hover { background:#f3f5f7; color:#202938; }
#sariRiderAccount .ra-password-toggle svg { width:18px; height:18px; fill:none; stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }
/* Match the same thin border on the support form inputs, too. */
#sariRiderAccount .ra-support-wrapper :is(input,textarea):focus,
#sariRiderAccount .ra-support-wrapper :is(input,textarea):focus-visible {
    border-color:var(--ra-gold) !important;
    box-shadow:none !important;
    outline:none !important;
}
/* Validation errors remain red at rest; focused fields always show the thin gold border. */
#sariRiderAccount .ra-control[aria-invalid="true"]:focus,
#sariRiderAccount .ra-control[aria-invalid="true"]:focus-visible {
    border-color:var(--ra-gold);
    box-shadow:none;
    outline:none;
}


/* V4: Applied actual Buyer UI metrics to Rider, including the surrounding page. */
#courierMain > main:has(#sariRiderAccount) {
    padding:20px 16px 40px !important;
}
#courierMain #sariRiderAccount .sa-page-head {
    padding:2px 2px 14px !important;
}
#courierMain #sariRiderAccount .sa-eyebrow {
    font-family:'Poppins',ui-sans-serif,system-ui,sans-serif !important;
    font-size:10.5px !important;
    font-weight:800 !important;
    line-height:1.2 !important;
    letter-spacing:.14em !important;
    color:#a87316 !important;
}
#courierMain #sariRiderAccount .sa-page-title {
    font-family:'Poppins',ui-sans-serif,system-ui,sans-serif !important;
    font-size:clamp(27px,2.25vw,31px) !important;
    font-weight:760 !important;
    line-height:1.04 !important;
    letter-spacing:-.035em !important;
    color:#2e343b !important;
}
#courierMain #sariRiderAccount .sa-page-title > span { color:#c88912 !important; }
/* Buyer card rhythm: visible difference from the former Courier-only padding. */
#sariRiderAccount .ra-profile-head { padding:14px 15px; }
#sariRiderAccount .ra-profile-body { padding:16px; }
#sariRiderAccount .ra-profile-type { font-size:10px; }
#sariRiderAccount .ra-profile-email { font-size:12px; }
#sariRiderAccount .ra-meta-label { font-size:11px; }
#sariRiderAccount .ra-meta-value { font-size:12px; }
#courierMain #sariRiderAccount .ra-section-title { font-size:17px; }
#sariRiderAccount .ra-section-heading { padding:17px 18px 12px; }
#sariRiderAccount .ra-section-body { padding:0 18px 18px; }
#sariRiderAccount .ra-fields { gap:12px 13px; }
#sariRiderAccount .ra-form-label { margin-bottom:5px; }
#sariRiderAccount .ra-control { font-size:12.5px; }
#sariRiderAccount .ra-control:focus,
#sariRiderAccount .ra-control:focus-visible { border:1px solid #c88912; box-shadow:none; outline:none; }
/* Keep Current Password half-width on wider screens. */
#sariRiderAccount #riderCurrentPassword { max-width:100%; }
@media (min-width:640px) {
    #courierMain > main:has(#sariRiderAccount) { padding-inline:24px !important; }
}
@media (min-width:1024px) {
    #courierMain > main:has(#sariRiderAccount) { padding-inline:32px !important; }
}
@media (max-width:640px) {
    #courierMain #sariRiderAccount .sa-eyebrow { font-size:9.5px !important; }
    #courierMain #sariRiderAccount .sa-page-title { font-size:25px !important; }
    #sariRiderAccount .ra-fields { grid-template-columns:1fr; gap:13px; }
    #sariRiderAccount .ra-field--full { grid-column:auto; }
    #sariRiderAccount .ra-form-footer { flex-direction:column; align-items:stretch; }
    #sariRiderAccount .ra-form-footer .ra-button { width:100%; }
}
@media (max-width:540px) {
    #courierMain > main:has(#sariRiderAccount) { padding:20px 14px 28px !important; }
    #sariRiderAccount .ra-section-heading { padding:17px 14px 12px; }
    #sariRiderAccount .ra-section-body { padding:0 14px 18px; }
}

@endverbatim
</style>
@endpush

@section('content')
{{-- SARI-COURIER-BUYER-UI-V4-ACTIVE: inspect page source to verify active Blade. --}}
<div id="sariRiderAccount" data-profile-ui-version="SARI-COURIER-BUYER-UI-V4-ACTIVE">
    {{-- V4 also aligns the actual Courier top bar with the Buyer Account top bar. --}}
    <section class="sa-page-head" aria-label="Rider account page heading">
        <p class="sa-eyebrow">Account Management</p>
        <h1 class="sa-page-title" data-courier-ui-version="V4">Rider <span>Profile</span></h1>
    </section>

    @if($errors->any())
        <div class="ra-feedback ra-feedback--error" role="alert">{{ $errors->first() }}</div>
    @endif

    @php
        $riderInitials = mb_strtoupper(mb_substr((string) $account->first_name, 0, 1) . mb_substr((string) $account->last_name, 0, 1));
        $riderPhotoUrl = filled($account->profile_image_path)
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($account->profile_image_path)
            : null;
    @endphp

    <div class="ra-layout">
        <div class="ra-left">
            <aside class="ra-card" aria-label="Rider profile summary">
                <div class="ra-profile-head">
                    <h2 class="ra-card-title">Profile</h2>
                    <span class="ra-profile-type">Rider account</span>
                </div>
                <div class="ra-profile-body">
                    <div class="ra-avatar-wrap">
                        <div class="ra-avatar">
                            <img id="riderPhotoPreview" src="{{ $riderPhotoUrl ?: '' }}" alt="Rider profile photo" @if(!$riderPhotoUrl) hidden @endif>
                            <span id="riderPhotoFallback" @if($riderPhotoUrl) hidden @endif>{{ $riderInitials ?: 'R' }}</span>
                        </div>
                        <label for="riderProfileImage" class="ra-avatar-edit" title="Change profile photo" aria-label="Change profile photo">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg>
                        </label>
                    </div>
                    <div>
                        <p class="ra-profile-name" id="riderNamePreview">{{ $profile['full_name'] }}</p>
                        <p class="ra-profile-email" id="riderEmailPreview">{{ $account->email }}</p>
                        <span class="ra-availability-pill {{ $profile['availability_status'] === 'online' ? 'ra-availability-pill--online' : '' }}">
                            {{ ucfirst($profile['availability_status']) }}
                        </span>
                        <p class="ra-profile-sub">{{ $profile['vehicle_type'] ?: 'Vehicle not set' }} · {{ $profile['plate_number'] ?: 'Plate not set' }}</p>
                    </div>
                    <input id="riderProfileImage" name="profile_image" type="file" accept="image/jpeg,image/png,image/webp" form="riderAccountForm" class="ra-sr-only" aria-label="Upload rider profile photo">
                    <p class="ra-profile-file-status" id="riderPhotoStatus" aria-live="polite">JPG, PNG, or WEBP · Maximum 5 MB</p>
                    <div class="ra-meta">
                        <div class="ra-meta-row"><span class="ra-meta-label">Contact</span><span class="ra-meta-value" id="riderContactPreview">{{ $account->contact_no ?: 'Not set' }}</span></div>
                        <div class="ra-meta-row"><span class="ra-meta-label">Assigned area</span><span class="ra-meta-value">{{ $account->barangay_name ?: 'Not assigned' }}@if($account->municipality_name) · {{ $account->municipality_name }}@endif</span></div>
                        <div class="ra-meta-row"><span class="ra-meta-label">Joined</span><span class="ra-meta-value">{{ $profile['joined'] }}</span></div>
                        <div class="ra-meta-row"><span class="ra-meta-label">Completed deliveries</span><span class="ra-meta-value">{{ $profile['completed_deliveries'] }}</span></div>
                        <div class="ra-meta-row"><span class="ra-meta-label">Rider rating</span><span class="ra-meta-value">★ {{ $profile['rating'] }}</span></div>
                    </div>
                </div>
            </aside>

            <section class="ra-card ra-side-section" aria-label="Availability settings">
                <h2 class="ra-side-title">Availability</h2>
                <p class="ra-side-copy">Update whether you are available to receive delivery assignments.</p>
                <form method="POST" action="{{ route('courier.profile.availability') }}">
                    @csrf
                    @method('PATCH')
                    <label class="ra-form-label" for="riderAvailability">Current status</label>
                    <div class="ra-select-shell">
                        <select id="riderAvailability" name="availability_status" class="ra-control" required>
                            <option value="online" @selected(old('availability_status', $profile['availability_status']) === 'online')>Online</option>
                            <option value="offline" @selected(old('availability_status', $profile['availability_status']) === 'offline')>Offline</option>
                        </select>
                    </div>
                    <button type="submit" class="ra-button ra-button--full">Update Status</button>
                </form>
            </section>

            <div class="ra-support-wrapper">
                @include('components.support.complaint-form')
            </div>
        </div>

        <section class="ra-card" aria-label="Rider account information">
            <form id="riderAccountForm" method="POST" action="{{ route('courier.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <section class="ra-unified-section" aria-labelledby="riderBasicHeading">
                    <header class="ra-section-heading">
                        <h2 id="riderBasicHeading" class="ra-section-title">Basic information</h2>
                        <span class="ra-section-rule" aria-hidden="true"></span>
                    </header>
                    <div class="ra-section-body">
                        <div class="ra-fields">
                            <div class="ra-field">
                                <label for="riderFirstName" class="ra-form-label">First Name <span class="ra-required">*</span></label>
                                <input id="riderFirstName" name="first_name" class="ra-control" value="{{ old('first_name', $account->first_name) }}" autocomplete="given-name" maxlength="50" data-rider-name required>
                                <small class="ra-field-error" data-error-for="riderFirstName" aria-live="polite" hidden></small>
                            </div>
                            <div class="ra-field">
                                <label for="riderLastName" class="ra-form-label">Last Name <span class="ra-required">*</span></label>
                                <input id="riderLastName" name="last_name" class="ra-control" value="{{ old('last_name', $account->last_name) }}" autocomplete="family-name" maxlength="50" data-rider-name required>
                                <small class="ra-field-error" data-error-for="riderLastName" aria-live="polite" hidden></small>
                            </div>
                            <div class="ra-field">
                                <label for="riderMiddleInitial" class="ra-form-label">Middle Initial <span class="ra-help">(optional)</span></label>
                                <input id="riderMiddleInitial" name="middle_initial" class="ra-control" value="{{ old('middle_initial', $account->middle_initial) }}" autocomplete="additional-name" maxlength="2" data-rider-middle placeholder="Up to 2 letters">
                                <small class="ra-field-error" data-error-for="riderMiddleInitial" aria-live="polite" hidden></small>
                            </div>
                            <div class="ra-field">
                                <label for="riderEmail" class="ra-form-label">Email <span class="ra-required">*</span></label>
                                <input id="riderEmail" name="email" type="email" class="ra-control" value="{{ old('email', $account->email) }}" autocomplete="email" maxlength="255" required>
                            </div>
                            <div class="ra-field">
                                <label for="riderContact" class="ra-form-label">Contact Number <span class="ra-required">*</span></label>
                                <input id="riderContact" name="contact_no" type="tel" inputmode="tel" class="ra-control" value="{{ old('contact_no', $account->contact_no) }}" autocomplete="tel" minlength="11" maxlength="13" pattern="(?:09[0-9]{9}|\+639[0-9]{9})" placeholder="09XXXXXXXXX or +639XXXXXXXXX" required>
                                <p class="ra-help">Philippine mobile number only: 11 digits starting with 09, or +63 followed by 9 digits starting with 9.</p>
                                <small class="ra-field-error" data-error-for="riderContact" aria-live="polite" hidden></small>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ra-unified-section" aria-labelledby="riderAddressHeading">
                    <header class="ra-section-heading">
                        <h2 id="riderAddressHeading" class="ra-section-title">Address details</h2>
                        <span class="ra-section-rule" aria-hidden="true"></span>
                    </header>
                    <div class="ra-section-body">
                        <div class="ra-fields">
                            <div class="ra-field ra-field--full">
                                <label for="riderStreet" class="ra-form-label">Street / Unit Address <span class="ra-required">*</span></label>
                                <input id="riderStreet" name="street_address" class="ra-control" value="{{ old('street_address', $account->street_address) }}" maxlength="500" autocomplete="street-address" required>
                            </div>
                            <div class="ra-field">
                                <span class="ra-form-label">Barangay</span>
                                <div class="ra-readonly"><span class="ra-readonly-value">{{ $account->barangay_name ?: 'Not set' }}</span><span class="ra-readonly-badge">Registered</span></div>
                            </div>
                            <div class="ra-field">
                                <span class="ra-form-label">City / Municipality</span>
                                <div class="ra-readonly"><span class="ra-readonly-value">{{ $account->municipality_name ?: 'Not set' }}</span><span class="ra-readonly-badge">Registered</span></div>
                            </div>
                            <div class="ra-field ra-field--full">
                                <span class="ra-form-label">Province / Area</span>
                                <div class="ra-readonly"><span class="ra-readonly-value">{{ $account->province_name ?: 'Not set' }}</span><span class="ra-readonly-badge">Registered</span></div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ra-unified-section" aria-labelledby="riderVehicleHeading">
                    <header class="ra-section-heading">
                        <h2 id="riderVehicleHeading" class="ra-section-title">Vehicle information</h2>
                        <span class="ra-section-rule" aria-hidden="true"></span>
                    </header>
                    <div class="ra-section-body">
                        <div class="ra-fields">
                            <div class="ra-field">
                                <label for="riderVehicleType" class="ra-form-label">Vehicle Type <span class="ra-required">*</span></label>
                                <input id="riderVehicleType" name="vehicle_type" type="text" inputmode="text" class="ra-control" value="{{ old('vehicle_type', $account->vehicle_type) }}" maxlength="80" required>
                            </div>
                            <div class="ra-field">
                                <label for="riderVehicleModel" class="ra-form-label">Vehicle Model</label>
                                <input id="riderVehicleModel" name="vehicle_model" class="ra-control" value="{{ old('vehicle_model', $account->vehicle_model) }}" maxlength="120">
                            </div>
                            <div class="ra-field">
                                <label for="riderPlate" class="ra-form-label">Plate Number <span class="ra-required">*</span></label>
                                <input id="riderPlate" name="plate_number" class="ra-control" value="{{ old('plate_number', $account->plate_number) }}" maxlength="25" autocapitalize="characters" required>
                            </div>
                            <div class="ra-field">
                                <label for="riderLicense" class="ra-form-label">License Number</label>
                                <input id="riderLicense" name="license_number" class="ra-control" value="{{ old('license_number', $account->license_number) }}" maxlength="35" autocapitalize="characters">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ra-unified-section" aria-labelledby="riderSecurityHeading">
                    <header class="ra-section-heading">
                        <h2 id="riderSecurityHeading" class="ra-section-title">Password &amp; security</h2>
                        <span class="ra-section-rule" aria-hidden="true"></span>
                    </header>
                    <div class="ra-section-body">
                        <div class="ra-fields">
                            <div class="ra-field">
                                <label for="riderCurrentPassword" class="ra-form-label">Current Password</label>
                                <div class="ra-password-wrap">
                                    <input id="riderCurrentPassword" name="current_password" type="password" class="ra-control" autocomplete="current-password" maxlength="255">
                                    <button type="button" class="ra-password-toggle" data-password-toggle="riderCurrentPassword" aria-label="Show password" aria-pressed="false" title="Show password"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button>
                                </div>
                                <p class="ra-help">Required only when setting a new password.</p>
                            </div>
                            <div class="ra-field">
                                <label for="riderNewPassword" class="ra-form-label">New Password</label>
                                <div class="ra-password-wrap">
                                    <input id="riderNewPassword" name="password" type="password" class="ra-control" autocomplete="new-password" minlength="8" maxlength="255">
                                    <button type="button" class="ra-password-toggle" data-password-toggle="riderNewPassword" aria-label="Show password" aria-pressed="false" title="Show password"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button>
                                </div>
                            </div>
                            <div class="ra-field">
                                <label for="riderConfirmPassword" class="ra-form-label">Confirm New Password</label>
                                <div class="ra-password-wrap">
                                    <input id="riderConfirmPassword" name="password_confirmation" type="password" class="ra-control" autocomplete="new-password" minlength="8" maxlength="255">
                                    <button type="button" class="ra-password-toggle" data-password-toggle="riderConfirmPassword" aria-label="Show password" aria-pressed="false" title="Show password"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <footer class="ra-form-footer">
                    <p class="ra-save-note">Profile changes are saved securely to your Rider account.</p>
                    <button type="submit" class="ra-button">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h11l3 3v13H5z"/><path d="M8 4v6h8V4M8 20v-6h8v6"/></svg>
                        Save Rider Profile
                    </button>
                </footer>
            </form>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('sariRiderAccount');
    if (!root) return;


    // Password visibility controls are independent of profile submission.
    root.querySelectorAll('[data-password-toggle]').forEach(button => {
        const field = document.getElementById(button.dataset.passwordToggle);
        if (!field) return;
        button.addEventListener('click', () => {
            const visible = field.type === 'password';
            field.type = visible ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(visible));
            button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
            button.title = visible ? 'Hide password' : 'Show password';
            button.innerHTML = visible
                ? '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3l18 18M10.6 5.1A11.4 11.4 0 0 1 12 5c6.5 0 10 7 10 7a16.7 16.7 0 0 1-3.4 4.1M6.3 6.3C3.6 8.2 2 12 2 12s3.5 7 10 7c1.8 0 3.4-.5 4.7-1.2M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>'
                : '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>';
        });
    });

    const photoInput = root.querySelector('#riderProfileImage');
    const photoPreview = root.querySelector('#riderPhotoPreview');
    const photoFallback = root.querySelector('#riderPhotoFallback');
    const photoStatus = root.querySelector('#riderPhotoStatus');
    const firstName = root.querySelector('#riderFirstName');
    const lastName = root.querySelector('#riderLastName');
    const email = root.querySelector('#riderEmail');
    const contact = root.querySelector('#riderContact');
    const profileName = root.querySelector('#riderNamePreview');
    const profileEmail = root.querySelector('#riderEmailPreview');
    const profileContact = root.querySelector('#riderContactPreview');
    const savedPhotoSrc = photoPreview?.getAttribute('src') || '';
    let objectUrl = null;

    function refreshPreview() {
        if (profileName) profileName.textContent = [firstName?.value, lastName?.value].map(v => (v || '').trim()).filter(Boolean).join(' ') || 'SARI Rider';
        if (profileEmail) profileEmail.textContent = email?.value?.trim() || 'Not set';
        if (profileContact) profileContact.textContent = contact?.value?.trim() || 'Not set';
    }
    // Same constraints as CourierPageController::updateProfile; passwords are never sanitized.
    const middleInitial = root.querySelector('#riderMiddleInitial');
    const nameInputs = [firstName, lastName];
    const namePattern = /^[\p{L}\p{M}]+(?:[ '\u2019.-][\p{L}\p{M}]+)*\.?$/u;
    const mobilePattern = /^(?:09[0-9]{9}|\+639[0-9]{9})$/;
    let composing = false;

    const setFieldError = (field, message = '') => {
        if (!field) return;
        field.setCustomValidity(message);
        const hint = root.querySelector(`[data-error-for="${field.id}"]`);
        if (hint) {
            hint.textContent = message;
            hint.hidden = !message;
        }
        field.setAttribute('aria-invalid', message ? 'true' : 'false');
    };

    const tidyName = value => String(value).replace(/[^\p{L}\p{M} '\u2019.-]/gu, '')
        .replace(/\s{2,}/gu, ' ').replace(/^\s+/u, '');

    const interceptPaste = (field, sanitize) => {
        field?.addEventListener('paste', event => {
            const pasted = event.clipboardData?.getData('text');
            if (typeof pasted !== 'string' || !pasted) return;
            event.preventDefault();
            const start = field.selectionStart ?? field.value.length;
            const end = field.selectionEnd ?? start;
            const merged = field.value.slice(0, start) + pasted + field.value.slice(end);
            field.value = sanitize(merged);
            field.dispatchEvent(new Event('input', { bubbles:true }));
        });
    };

    nameInputs.forEach(field => {
        if (!field) return;
        field.addEventListener('beforeinput', event => {
            if (!event.isComposing && event.data && /[^\p{L}\p{M} '\u2019.-]/u.test(event.data)) event.preventDefault();
        });
        interceptPaste(field, value => tidyName(value).slice(0, 50));
        field.addEventListener('input', () => {
            if (composing) return;
            const position = field.selectionStart;
            const sanitized = tidyName(field.value);
            if (sanitized !== field.value) {
                const beforeCursor = position === null ? sanitized.length : tidyName(field.value.slice(0, position)).length;
                field.value = sanitized;
                if (document.activeElement === field) field.setSelectionRange(beforeCursor, beforeCursor);
            }
            setFieldError(field, '');
            refreshPreview();
        });
        field.addEventListener('blur', () => {
            const value = field.value.trim();
            field.value = value;
            setFieldError(field, value && !namePattern.test(value)
                ? 'Use letters only (spaces, hyphens, apostrophes or periods allowed).' : '');
            refreshPreview();
        });
    });

    middleInitial?.addEventListener('beforeinput', event => {
        if (!event.isComposing && event.data && /[^\p{L}]/u.test(event.data)) event.preventDefault();
    });
    interceptPaste(middleInitial, value => Array.from(value.match(/\p{L}/gu) || [])
        .slice(0, 2).join('').toUpperCase());
    middleInitial?.addEventListener('input', () => {
        if (composing) return;
        middleInitial.value = Array.from(middleInitial.value.match(/\p{L}/gu) || [])
            .slice(0, 2).join('').toUpperCase();
        setFieldError(middleInitial, '');
    });

    contact?.addEventListener('beforeinput', event => {
        if (!event.isComposing && event.data && /[^0-9+]/.test(event.data)) event.preventDefault();
    });
    contact?.addEventListener('input', () => {
        if (composing) return;
        const raw = contact.value.trimStart();
        const hasPlus = raw.startsWith('+');
        const digits = raw.replace(/\D/g, '').slice(0, hasPlus ? 12 : 11);
        contact.value = (hasPlus ? '+' : '') + digits;
        setFieldError(contact, '');
        refreshPreview();
    });
    // A formatted number pasted with spaces must not be truncated by maxlength first.
    contact?.addEventListener('paste', event => {
        const pasted = event.clipboardData?.getData('text');
        if (typeof pasted !== 'string' || pasted.length === 0) return;
        event.preventDefault();
        const start = contact.selectionStart ?? contact.value.length;
        const end = contact.selectionEnd ?? start;
        const merged = contact.value.slice(0, start) + pasted + contact.value.slice(end);
        const raw = merged.trimStart();
        const digits = raw.replace(/\D/g, '').slice(0, raw.startsWith('+') ? 12 : 11);
        contact.value = (raw.startsWith('+') ? '+' : '') + digits;
        contact.dispatchEvent(new Event('input', { bubbles:true }));
    });

    contact?.addEventListener('blur', () => {
        setFieldError(contact, contact.value && !mobilePattern.test(contact.value)
            ? 'Use 09XXXXXXXXX (11 digits) or +639XXXXXXXXX (13 characters).' : '');
    });

    // Composition events keep accented/IME name entry intact until composition finishes.
    [firstName,lastName,middleInitial,contact].forEach(field => {
        field?.addEventListener('compositionstart', () => { composing = true; });
        field?.addEventListener('compositionend', () => {
            composing = false;
            field.dispatchEvent(new Event('input', { bubbles:true }));
        });
    });

    root.querySelector('#riderAccountForm')?.addEventListener('submit', event => {
        let valid = true;
        nameInputs.forEach(field => {
            if (!field) return;
            field.value = field.value.trim();
            const message = field.value && !namePattern.test(field.value)
                ? 'Use letters only (spaces, hyphens, apostrophes or periods allowed).' : '';
            setFieldError(field, message);
            if (message) valid = false;
        });
        if (middleInitial) {
            const message = middleInitial.value && !/^\p{L}{1,2}$/u.test(middleInitial.value)
                ? 'Middle Initial is optional, but must contain 1 or 2 letters only.' : '';
            setFieldError(middleInitial, message);
            if (message) valid = false;
        }
        if (contact) {
            const message = !mobilePattern.test(contact.value)
                ? 'Use 09XXXXXXXXX (11 digits) or +639XXXXXXXXX (13 characters).' : '';
            setFieldError(contact, message);
            if (message) valid = false;
        }
        if (!valid) {
            event.preventDefault();
            root.querySelector('#riderAccountForm input:invalid')?.focus();
        }
    });

    // Vehicle identifiers are alphanumeric; do not impose phone-only digit rules on them.
    [root.querySelector('#riderPlate'), root.querySelector('#riderLicense')].forEach(field => {
        field?.addEventListener('input', () => {
            if (composing) return;
            field.value = field.value.replace(/[^A-Za-z0-9 \/-]/g, '').toUpperCase();
        });
    });

    [email].forEach(field => field?.addEventListener('input', refreshPreview));

    function releasePhotoUrl() {
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = null;
    }

    function restoreSavedPhoto() {
        releasePhotoUrl();
        if (photoPreview) {
            photoPreview.src = savedPhotoSrc;
            photoPreview.hidden = !savedPhotoSrc;
        }
        if (photoFallback) photoFallback.hidden = Boolean(savedPhotoSrc);
    }

    photoInput?.addEventListener('change', () => {
        const file = photoInput.files?.[0];
        if (!file) return;
        const allowed = ['image/jpeg','image/png','image/webp'];
        const invalid = !allowed.includes(file.type) ? 'Choose a JPG, PNG, or WEBP photo.'
            : file.size > 5 * 1024 * 1024 ? 'Profile photo must be 5 MB or smaller.' : '';
        if (invalid) {
            photoInput.value = '';
            restoreSavedPhoto();
            if (photoStatus) { photoStatus.textContent = invalid; photoStatus.classList.add('is-error'); }
            return;
        }
        releasePhotoUrl();
        objectUrl = URL.createObjectURL(file);
        if (photoPreview) { photoPreview.src = objectUrl; photoPreview.hidden = false; }
        if (photoFallback) photoFallback.hidden = true;
        if (photoStatus) { photoStatus.textContent = 'New photo selected. Save Rider Profile to upload.'; photoStatus.classList.remove('is-error'); }
    });
    window.addEventListener('beforeunload', releasePhotoUrl, { once:true });
});
</script>
@endpush
