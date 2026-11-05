@extends('layouts.rider-registration')

@section('title', 'Choose Logistics — SARI Rider')
@section('body-class', 'rider-choose-page')


{{-- One-time animation is activated before first paint, only after a Rider selection. --}}
@push('styles')
<script>
(function () {
    try {
        var startedAt = Number(sessionStorage.getItem('sari-rider-logistics-intro'));
        sessionStorage.removeItem('sari-rider-logistics-intro');
        var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (startedAt > 0 && Date.now() >= startedAt && Date.now() - startedAt < 30000 && !reducedMotion) {
            document.documentElement.classList.add('sari-rider-intro-active');
        }
    } catch (_error) {
        // Loading the Logistics page must never depend on browser storage.
    }
})();
</script>
<style>
    /* Buyer Registration visual system (from layouts/buyer-registration.blade.php).
       Only the Rider Logistics selection page is affected. */
    body.sari-rider-registration.rider-choose-page {
        /* Match the original SARI login/registration background. */
        background: #f5f7f9 url("{{ asset('images/login-bg.png') }}") center center / cover fixed no-repeat !important;
        color: #20242c;
    }
    body.sari-rider-registration.rider-choose-page .sari-rider-brand img {
        filter: brightness(0);
    }

    /* The existing Lottie transition retains its dark-gold palette and timing. */
    html.sari-rider-intro-active body.sari-rider-registration.rider-choose-page { overflow: hidden; }
    .sari-rider-intro-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 2147483000;
        align-items: center;
        justify-content: center;
        padding: 20px;
        box-sizing: border-box;
        background: linear-gradient(125deg, #d9ae52 0%, #b77b1b 54%, #915708 100%);
        opacity: 1;
        transform: translate3d(0, 0, 0);
        backface-visibility: hidden;
        transition: opacity 1050ms cubic-bezier(.33, 0, .2, 1), transform 1050ms cubic-bezier(.22, 1, .36, 1);
    }
    html.sari-rider-intro-active .sari-rider-intro-overlay {
        display: flex;
        will-change: transform, opacity;
    }
    .sari-rider-intro-overlay.is-leaving {
        opacity: 0;
        transform: translate3d(-15vw, 0, 0);
        pointer-events: none;
    }
    .sari-rider-intro-inner {
        display: flex;
        align-items: center;
        justify-content: center;
        width: min(100%, 440px);
    }
    .sari-rider-intro-animation {
        display: block;
        width: min(82vw, 370px);
        height: min(82vw, 370px);
        border: 0;
        background: transparent;
        mix-blend-mode: multiply;
        pointer-events: none;
    }

    .rider-choose-shell {
        box-sizing: border-box;
        width: 100%;
        max-width: 1040px;
        margin: 0 auto;
        padding: 0 24px 34px;
    }
    .rider-choose-panel {
        padding: clamp(24px, 4vw, 42px);
        background: #fff;
        border: 1px solid #e4e7ec;
        border-radius: 16px;
        box-shadow: 0 14px 44px rgba(27, 39, 58, .09);
    }
    .rider-choose-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 28px;
    }
    .rider-choose-back,
    .rider-clear-search {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #a86c00;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.35;
        text-decoration: none;
        transition: color .18s ease;
    }
    .rider-choose-back:hover,
    .rider-clear-search:hover { color: #854f00; }
    .rider-choose-back svg,
    .rider-choose-signin svg,
    .rider-clear-search svg {
        width: 16px;
        height: 16px;
        flex: 0 0 auto;
    }
    /* Filled gold sign-in action, matching the Buyer Registration primary button. */
    .rider-choose-shell a.rider-choose-signin {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 43px;
        box-sizing: border-box;
        padding: 10px 19px;
        border: 1px solid #c57c00;
        border-radius: 10px;
        background: #c57c00;
        color: #fff !important;
        font-family: inherit;
        font-size: 14px;
        font-weight: 650;
        line-height: 1.2;
        text-decoration: none;
        box-shadow: 0 3px 9px rgba(159, 100, 0, .14);
        transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }
    .rider-choose-shell a.rider-choose-signin:hover {
        background: #a86600;
        border-color: #a86600;
        box-shadow: 0 6px 16px rgba(159, 100, 0, .17);
        color: #fff !important;
    }
    .rider-choose-shell a.rider-choose-signin:active,
    .rider-search-submit:active,
    .rider-provider-button:active { transform: translateY(1px); }

    .rider-choose-header { padding-bottom: 28px; }
    .rider-choose-eyebrow {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 0 0 11px;
        color: #a86c00;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .10em;
        line-height: 1.5;
        text-transform: uppercase;
    }
    .rider-choose-eyebrow::before {
        content: '';
        display: block;
        width: 21px;
        height: 2px;
        border-radius: 99px;
        background: #c57c00;
    }
    .rider-choose-title {
        margin: 0;
        color: #20232a;
        font-size: clamp(27px, 3.6vw, 38px);
        font-weight: 700;
        letter-spacing: -.038em;
        line-height: 1.2;
    }
    .rider-search-section {
        padding-top: 25px;
        border-top: 1px solid #e8ecf1;
    }
    .rider-section-title {
        margin: 0;
        color: #252c36;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -.02em;
        line-height: 1.35;
    }
    .rider-search-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(0, .85fr) minmax(0, 1fr) auto;
        align-items: end;
        gap: 13px;
        margin-top: 19px;
    }
    .rider-search-field { min-width: 0; }
    .rider-field-label {
        display: block;
        margin-bottom: 7px;
        color: #323843;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.5;
    }
    .rider-choose-control {
        display: block;
        box-sizing: border-box;
        width: 100%;
        min-width: 0;
        height: 46px;
        padding: 0 12px;
        appearance: none;
        border: 1px solid #d4dce6;
        border-radius: 9px;
        background: #fff;
        outline: none;
        color: #252c36;
        font: inherit;
        font-size: 14px;
        transition: border-color .18s ease, box-shadow .18s ease;
    }
    .rider-choose-control::placeholder { color: #98a1af; opacity: 1; }
    .rider-choose-control:hover { border-color: #bdc5cf; }
    .rider-choose-control:focus {
        border-color: #c57c00;
        box-shadow: 0 0 0 4px rgba(197, 124, 0, .09);
    }
    .rider-select-wrap { position: relative; }
    .rider-select-wrap::after {
        content: '';
        position: absolute;
        top: 50%;
        right: 16px;
        width: 8px;
        height: 8px;
        border-right: 1.5px solid #687180;
        border-bottom: 1.5px solid #687180;
        transform: translateY(-70%) rotate(45deg);
        pointer-events: none;
    }
    .rider-select-wrap .rider-choose-control { padding-right: 35px; cursor: pointer; }

    /* Enhanced region picker: a compact, scrollable menu instead of the browser's oversized popup.
       With JavaScript disabled, the native select stays visible and fully usable. */
    .rider-select-wrap.rider-region-enhanced::after { display: none; }
    .rider-region-enhanced .rider-region-native { display: none; }
    .rider-region-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        text-align: left;
        cursor: pointer;
        background: #fff;
    }
    .rider-region-trigger > span {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .rider-region-trigger svg {
        width: 17px;
        height: 17px;
        flex: 0 0 auto;
        transition: transform .18s ease;
    }
    .rider-region-enhanced.is-open .rider-region-trigger {
        border-color: #c57c00;
        box-shadow: 0 0 0 4px rgba(197, 124, 0, .10);
    }
    .rider-region-enhanced.is-open .rider-region-trigger svg { transform: rotate(180deg); }
    .rider-region-menu {
        display: none;
        position: absolute;
        z-index: 25;
        top: calc(100% + 7px);
        left: 0;
        width: min(360px, calc(100vw - 40px));
        min-width: 100%;
        padding: 9px;
        box-sizing: border-box;
        border: 1px solid #dce1e8;
        border-radius: 13px;
        background: #fff;
        box-shadow: 0 16px 42px rgba(22, 32, 48, .16);
    }
    .rider-region-enhanced.is-open .rider-region-menu { display: block; }
    .rider-region-filter {
        box-sizing: border-box;
        width: 100%;
        height: 39px;
        padding: 0 12px;
        border: 1px solid #d8dfe8;
        border-radius: 8px;
        background: #f9fafc;
        color: #252c36;
        font: inherit;
        font-size: 13px;
        outline: none;
    }
    .rider-region-filter:focus {
        border-color: #c57c00;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(197, 124, 0, .10);
    }
    .rider-region-options {
        max-height: 218px;
        overflow-y: auto;
        margin-top: 7px;
        padding: 2px;
        overscroll-behavior: contain;
        scrollbar-color: #c5b79f transparent;
        scrollbar-width: thin;
    }
    .rider-region-option {
        display: block;
        width: 100%;
        padding: 10px 12px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #303741;
        font: inherit;
        font-size: 13px;
        line-height: 1.45;
        text-align: left;
        cursor: pointer;
    }
    .rider-region-option[hidden], .rider-region-empty[hidden] { display: none !important; }
    .rider-region-option:hover,
    .rider-region-option:focus-visible {
        background: #faf2e4;
        color: #8e5600;
        outline: none;
    }
    .rider-region-option[aria-selected="true"] {
        background: #fff3dc;
        color: #8e5600;
        font-weight: 700;
    }
    .rider-region-empty {
        margin: 0;
        padding: 12px;
        color: #6c7582;
        font-size: 13px;
    }

    .rider-search-submit,
    .rider-choose-shell a.rider-provider-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        box-sizing: border-box;
        min-height: 46px;
        padding: 10px 19px;
        border: 1px solid #c57c00;
        border-radius: 10px;
        background: #c57c00;
        color: #fff !important;
        font-family: inherit;
        font-size: 14px !important;
        font-weight: 650;
        line-height: 1.3;
        white-space: nowrap;
        cursor: pointer;
        text-decoration: none;
        box-shadow: 0 3px 9px rgba(159, 100, 0, .14);
        transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }
    /* The shared Rider layout has smaller !important CTA text; override locally. */
    .rider-choose-shell button.rider-search-submit[type="submit"],
    .rider-choose-shell a.rider-provider-button[href*="register/rider/"] { font-size: 14px !important; }
    .rider-search-submit:hover,
    .rider-choose-shell a.rider-provider-button:hover {
        background: #a86600;
        border-color: #a86600;
        color: #fff !important;
        box-shadow: 0 6px 16px rgba(159, 100, 0, .17);
    }
    .rider-search-submit svg,
    .rider-provider-button svg { width: 16px; height: 16px; flex: 0 0 auto; }

    .rider-results-section {
        margin-top: 33px;
        padding-top: 25px;
        border-top: 1px solid #e8ecf1;
    }
    .rider-results-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }
    .rider-results-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    .rider-results-count { color: #7b8491; font-size: 13px; font-weight: 500; }
    .rider-provider-list { display: grid; gap: 14px; margin-top: 19px; }
    /* Each Logistics provider remains a separate bordered card/box. */
    .rider-provider-row {
        display: grid;
        grid-template-columns: 62px minmax(0, 1fr) auto;
        align-items: center;
        gap: 19px;
        padding: 22px;
        min-width: 0;
        border: 1px solid #e4e7ec;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 5px 17px rgba(27, 39, 58, .055);
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }
    .rider-provider-row:hover,
    .rider-provider-row:focus-within {
        border-color: #dab780;
        box-shadow: 0 9px 24px rgba(27, 39, 58, .085);
    }
    .rider-provider-icon {
        display: grid;
        place-items: center;
        width: 62px;
        height: 62px;
        border: 1px solid #ebe5dc;
        border-radius: 13px;
        background: #fbf8f3;
        color: #a86c00;
        font-size: 25px;
        font-weight: 700;
        letter-spacing: -.04em;
        line-height: 1;
    }
    .rider-provider-info { min-width: 0; }
    .rider-provider-name-line {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px 10px;
    }
    .rider-provider-name {
        margin: 0;
        color: #20232a;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -.025em;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }
    .rider-provider-badge,
    .rider-match-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 9px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.35;
    }
    .rider-provider-badge { background: #eef8f1; color: #267348; }
    .rider-provider-badge::before {
        content: '';
        display: block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #38a46a;
    }
    .rider-match-badge { background: #fff7e8; color: #9a6405; }
    .rider-provider-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 9px 17px;
        margin: 10px 0 0;
        color: #687382;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.55;
    }
    .rider-provider-meta-item { display: inline-flex; align-items: center; gap: 7px; }
    .rider-provider-meta-item svg { width: 15px; height: 15px; flex: 0 0 auto; color: #86909f; }
    .rider-provider-note { margin: 8px 0 0; color: #7e8793; font-size: 12px; line-height: 1.5; }
    .rider-choose-footer { margin: 18px 0 0; color: #7b8491; font-size: 11px; text-align: center; }
    .rider-empty {
        padding: 40px 18px;
        border: 1px dashed #dbe1e8;
        border-radius: 13px;
        text-align: center;
        color: #687382;
        font-size: 13px;
    }
    .rider-empty svg { width: 28px; height: 28px; margin-bottom: 10px; color: #a6b0bc; }
    .rider-empty strong { display: block; color: #252c36; font-size: 14px; }
    .rider-empty p { margin: 7px 0 0; }
    .rider-choose-back:focus-visible,
    .rider-choose-signin:focus-visible,
    .rider-clear-search:focus-visible,
    .rider-search-submit:focus-visible,
    .rider-provider-button:focus-visible {
        outline: 3px solid rgba(197, 124, 0, .40);
        outline-offset: 3px;
    }
    @media (max-width: 960px) {
        .rider-search-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .rider-search-submit { width: 100%; }
        .rider-provider-row { grid-template-columns: 62px minmax(0, 1fr); }
        .rider-provider-button { grid-column: 1 / -1; width: 100%; }
    }
    @media (max-width: 640px) {
        body.sari-rider-registration.rider-choose-page { background-attachment: scroll !important; }
        .rider-region-menu { width: 100%; }
        .rider-choose-shell { padding: 0 13px 26px; }
        .rider-choose-panel { padding: 23px 18px 25px; border-radius: 14px; }
        .rider-choose-topbar { gap: 9px; margin-bottom: 28px; }
        .rider-choose-back { font-size: 11px; }
        .rider-choose-shell a.rider-choose-signin { min-height: 40px; padding: 9px 12px; font-size: 12px; }
        .rider-choose-header { padding-bottom: 24px; }
        .rider-page-shell .rider-choose-title { font-size: clamp(27px, 7vw, 33px) !important; }
        .rider-search-section { padding-top: 23px; }
        .rider-search-grid { grid-template-columns: 1fr; gap: 14px; }
        .rider-choose-control { font-size: 16px; }
        .rider-search-submit { width: 100%; }
        .rider-results-section { margin-top: 29px; padding-top: 23px; }
        .rider-results-heading { align-items: flex-start; flex-direction: column; gap: 7px; }
        .rider-provider-row { grid-template-columns: 52px minmax(0, 1fr); gap: 12px; padding: 17px; }
        .rider-provider-icon { width: 52px; height: 52px; font-size: 22px; }
        .rider-provider-name { font-size: 16px; }
        .rider-provider-button { width: 100%; }
    }
    @media (prefers-reduced-motion: reduce) {
        .rider-region-trigger svg { transition: none !important; }
        .sari-rider-intro-overlay { transition: none; }
        .rider-choose-shell *, .rider-choose-shell *::before, .rider-choose-shell *::after { transition: none !important; }
    }
</style>
@endpush

@section('content')
{{-- The overlay is hidden on direct visits and only appears after Rider is selected. --}}
<div id="sariRiderIntro" class="sari-rider-intro-overlay" role="status" aria-label="Opening Logistics selection">
    <div class="sari-rider-intro-inner">
        <iframe
            id="sariRiderIntroAnimation"
            class="sari-rider-intro-animation"
            data-src="https://lottie.host/embed/7208f867-ad80-4670-bbd1-02dbee56fed7/qEhUI6WSfY.lottie"
            title="SARI Rider introduction animation"
            scrolling="no"
            tabindex="-1"
            aria-hidden="true"
        ></iframe>
    </div>
</div>

<div class="rider-page-shell rider-choose-shell">
    <main>
        <div class="rider-choose-panel">
            <nav class="rider-choose-topbar" aria-label="Registration navigation">
                <a class="rider-choose-back" href="{{ route('register') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                    <span>Back to registration</span>
                </a>
                <a class="rider-choose-signin" href="{{ route('login') }}">
                    <span>Sign in</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
                </a>
            </nav>

            <header class="rider-choose-header">
                <p class="rider-choose-eyebrow">Rider registration</p>
                <h1 class="rider-choose-title">Choose your Logistics team</h1>
            </header>

            <section class="rider-search-section" aria-labelledby="riderLocationSearchTitle">
                <h2 id="riderLocationSearchTitle" class="rider-section-title">Find a provider</h2>
                <form method="GET" action="{{ route('rider.logistics.index') }}" class="rider-search-grid" role="search">
                    <div class="rider-search-field">
                        <label class="rider-field-label" for="riderLocationSearch">City, province or barangay</label>
                        <input class="rider-choose-control" id="riderLocationSearch" name="location" type="search" list="riderLocationSuggestions" maxlength="100" autocomplete="address-level2" value="{{ $location }}" placeholder="Enter location">
                        <datalist id="riderLocationSuggestions">
                            @foreach ($locationSuggestions as $suggestion)
                                <option value="{{ $suggestion }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="rider-search-field">
                        <label class="rider-field-label" for="riderRegionSearch">Region</label>
                        <div class="rider-select-wrap">
                            <select class="rider-choose-control rider-region-native" id="riderRegionSearch" name="region">
                                <option value="">Any region</option>
                                @foreach ($serviceRegions as $regionOption)
                                    <option value="{{ $regionOption }}" @selected($region === $regionOption)>{{ $regionOption }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="rider-search-field">
                        <label class="rider-field-label" for="riderNameSearch">Logistics name (optional)</label>
                        <input class="rider-choose-control" id="riderNameSearch" name="q" type="search" maxlength="100" value="{{ $search }}" placeholder="Company name">
                    </div>
                    <button class="rider-search-submit" type="submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/></svg>
                        <span>Find Logistics</span>
                    </button>
                </form>
            </section>

            <section class="rider-results-section" aria-labelledby="riderResultsTitle">
                <div class="rider-results-heading">
                    <h2 id="riderResultsTitle" class="rider-section-title">{{ $hasLocationSearch ? 'Logistics recommendations' : 'Available Logistics' }}</h2>
                    <div class="rider-results-actions">
                        <span class="rider-results-count">{{ $logistics->count() }} {{ $logistics->count() === 1 ? 'provider' : 'providers' }}</span>
                        @if ($location !== '' || $region !== '' || $search !== '')
                            <a class="rider-clear-search" href="{{ route('rider.logistics.index') }}">Clear filters</a>
                        @endif
                    </div>
                </div>

                <div class="rider-provider-list" aria-label="Available Logistics companies">
                    @forelse ($logistics as $provider)
                        @php
                            $providerName = $provider->displayName();
                            $providerLocation = collect([$provider->barangay_name, $provider->municipality_name, $provider->province_name])->filter()->implode(', ');
                            $recommended = $hasLocationSearch && (int) $provider->rider_recommendation_score > 0;
                            $riderCount = (int) ($provider->approved_riders_count ?? 0);
                        @endphp
                        <article class="rider-provider-row">
                            <div class="rider-provider-icon" aria-hidden="true">{{ mb_strtoupper(mb_substr($providerName, 0, 1)) }}</div>
                            <div class="rider-provider-info">
                                <div class="rider-provider-name-line">
                                    <h3 class="rider-provider-name">{{ $providerName }}</h3>
                                    <span class="rider-provider-badge">Active</span>
                                    @if ($recommended)
                                        <span class="rider-match-badge">Recommended</span>
                                    @endif
                                </div>
                                <p class="rider-provider-meta">
                                    <span class="rider-provider-meta-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                                        <span>{{ $providerLocation ?: 'Location not specified' }}</span>
                                    </span>
                                    <span class="rider-provider-meta-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        <span>{{ number_format($riderCount) }} approved {{ $riderCount === 1 ? 'Rider' : 'Riders' }}</span>
                                    </span>
                                </p>
                                @if ($recommended)
                                    <p class="rider-provider-note">{{ $provider->rider_recommendation_reason }}</p>
                                @elseif ($hasLocationSearch)
                                    <p class="rider-provider-note">No verified location match</p>
                                @endif
                            </div>
                            <a class="rider-provider-button" href="{{ route('rider.logistics.apply', ['logistics' => $provider->getKey()]) }}">
                                <span>Apply</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                            </a>
                        </article>
                    @empty
                        <div class="rider-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/></svg>
                            <strong>No active Logistics providers found.</strong>
                            <p>Try a different search or clear your filters.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
        <p class="rider-choose-footer">© {{ date('Y') }} SARI</p>
    </main>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';
    var intro = document.getElementById('sariRiderIntro');
    if (!intro || !document.documentElement.classList.contains('sari-rider-intro-active')) return;

    var animation = document.getElementById('sariRiderIntroAnimation');
    var startedAt = performance.now();
    var isLeaving = false;
    var holdTimer;
    var fallbackTimer;
    var closeTimer;

    function finishIntro() {
        if (isLeaving) return;
        isLeaving = true;
        clearTimeout(holdTimer);
        clearTimeout(fallbackTimer);
        intro.classList.add('is-leaving');
        closeTimer = setTimeout(function () {
            document.documentElement.classList.remove('sari-rider-intro-active');
            intro.remove();
        }, 1100);
    }

    animation.addEventListener('load', function () {
        // Let the Lottie play at a comfortable pace, then reveal the page gently.
        // Avoid a long intro if the hosted iframe takes time to load.
        var remaining = Math.max(1100, 2700 - (performance.now() - startedAt));
        holdTimer = setTimeout(finishIntro, remaining);
    }, { once: true });
    // Fallback when the embedded animation loads slowly or never loads.
    fallbackTimer = setTimeout(finishIntro, 4000);
    animation.src = animation.dataset.src;

    window.addEventListener('pagehide', function () {
        clearTimeout(holdTimer);
        clearTimeout(fallbackTimer);
        clearTimeout(closeTimer);
    }, { once: true });
})();
</script>
@endpush

@push('scripts')
<script>
(function () {
    'use strict';

    const select = document.getElementById('riderRegionSearch');
    const wrapper = select && select.closest('.rider-select-wrap');
    if (!select || !wrapper || !select.options.length) return;

    const regionLabel = document.querySelector('label[for="riderRegionSearch"]');
    const entries = Array.from(select.options);
    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'rider-choose-control rider-region-trigger';
    trigger.setAttribute('aria-label', 'Region');
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');
    trigger.setAttribute('aria-controls', 'riderRegionOptions');

    const value = document.createElement('span');
    value.textContent = select.selectedOptions[0]?.textContent.trim() || 'Any region';
    const chevron = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    chevron.setAttribute('viewBox', '0 0 24 24');
    chevron.setAttribute('fill', 'none');
    chevron.setAttribute('stroke', 'currentColor');
    chevron.setAttribute('stroke-width', '1.8');
    chevron.setAttribute('stroke-linecap', 'round');
    chevron.setAttribute('aria-hidden', 'true');
    const arrow = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    arrow.setAttribute('d', 'm6 9 6 6 6-6');
    chevron.append(arrow);
    trigger.append(value, chevron);

    const menu = document.createElement('div');
    menu.className = 'rider-region-menu';
    const filter = document.createElement('input');
    filter.type = 'search';
    filter.className = 'rider-region-filter';
    filter.placeholder = 'Search regions...';
    filter.autocomplete = 'off';
    filter.setAttribute('aria-label', 'Search regions');
    const list = document.createElement('div');
    list.id = 'riderRegionOptions';
    list.className = 'rider-region-options';
    list.setAttribute('role', 'listbox');
    list.setAttribute('aria-label', 'Regions');
    const empty = document.createElement('p');
    empty.className = 'rider-region-empty';
    empty.textContent = 'No matching region';
    empty.hidden = true;

    const buttons = entries.map(function (entry, index) {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'rider-region-option';
        item.id = 'riderRegionOption' + index;
        item.setAttribute('role', 'option');
        item.textContent = entry.textContent.trim();
        item.setAttribute('aria-selected', String(select.selectedIndex === index));
        item.tabIndex = -1;
        item.addEventListener('click', function () { choose(index); });
        item.addEventListener('keydown', function (event) {
            const visible = visibleOptions();
            const current = visible.indexOf(item);
            if (event.key === 'Escape') {
                event.preventDefault();
                close(true);
            } else if (event.key === 'ArrowDown' || event.key === 'ArrowUp' || event.key === 'Home' || event.key === 'End') {
                event.preventDefault();
                if (!visible.length) return;
                const next = event.key === 'Home' ? 0
                    : event.key === 'End' ? visible.length - 1
                    : (current + (event.key === 'ArrowDown' ? 1 : -1) + visible.length) % visible.length;
                visible[next].focus();
            }
        });
        list.append(item);
        return item;
    });

    menu.append(filter, list, empty);
    wrapper.append(trigger, menu);
    wrapper.classList.add('rider-region-enhanced');
    select.tabIndex = -1;
    select.setAttribute('aria-hidden', 'true');

    function visibleOptions() { return buttons.filter(function (item) { return !item.hidden; }); }
    function filterOptions() {
        const query = filter.value.trim().toLocaleLowerCase();
        buttons.forEach(function (item) { item.hidden = !item.textContent.toLocaleLowerCase().includes(query); });
        empty.hidden = visibleOptions().length > 0;
    }
    function open() {
        wrapper.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
        filter.value = '';
        filterOptions();
        filter.focus();
        const selected = buttons[select.selectedIndex];
        if (selected) selected.scrollIntoView({ block: 'nearest' });
    }
    function close(restoreFocus) {
        wrapper.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
        if (restoreFocus) trigger.focus();
    }
    function choose(index) {
        select.selectedIndex = index;
        select.dispatchEvent(new Event('change', { bubbles: true }));
        value.textContent = entries[index].textContent.trim();
        buttons.forEach(function (item, i) { item.setAttribute('aria-selected', String(i === index)); });
        close(true);
    }

    trigger.addEventListener('click', function () {
        if (wrapper.classList.contains('is-open')) close(true);
        else open();
    });
    trigger.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp' || event.key === ' ') {
            event.preventDefault();
            open();
        } else if (event.key === 'Escape') {
            close(true);
        }
    });
    filter.addEventListener('input', filterOptions);
    filter.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            event.preventDefault();
            close(true);
        } else if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            const visible = visibleOptions();
            if (visible.length) visible[event.key === 'ArrowDown' ? 0 : visible.length - 1].focus();
        } else if (event.key === 'Enter') {
            event.preventDefault();
            const first = visibleOptions()[0];
            if (first) first.click();
        }
    });
    document.addEventListener('pointerdown', function (event) {
        if (!wrapper.contains(event.target)) close(false);
    });
    wrapper.addEventListener('focusout', function () {
        requestAnimationFrame(function () {
            if (!wrapper.contains(document.activeElement)) close(false);
        });
    });
    if (regionLabel) {
        regionLabel.addEventListener('click', function (event) {
            event.preventDefault();
            trigger.focus();
        });
    }
})();
</script>
@endpush
