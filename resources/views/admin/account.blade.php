@extends('layouts.admin')

@section('title','Account — SARI Admin')
@section('page-title','Account Management')

@push('styles')
<style>
    .sari-account-page{font-family:'Poppins',sans-serif;color:#26211d;}
    .sari-account-page input:-webkit-autofill,
    .sari-account-page input:-webkit-autofill:hover,
    .sari-account-page input:-webkit-autofill:focus{
        -webkit-box-shadow:0 0 0 1000px #fff inset !important;
        -webkit-text-fill-color:#2f2924 !important;
    }
    .sari-account-page .account-card{box-shadow:0 8px 26px rgba(55,45,32,.035);}
    .sari-account-page .field-input:focus{border-color:#d6b363;box-shadow:0 0 0 4px rgba(201,145,40,.07);outline:none;}

    /* ============================================================
       SELLER VISUAL SYSTEM — FINAL ADMIN ACCOUNT OVERRIDE
       Presentation only. Profile/password routes and JS stay intact.
       ============================================================ */

    .sari-account-page{
        --account-canvas:#F4F5F7;
        --account-surface:#FFFFFF;
        --account-surface-soft:#F9FAFB;
        --account-text:#111827;
        --account-text-secondary:#344054;
        --account-muted:#7C8592;
        --account-gold:#D59617;
        --account-gold-strong:#B97805;
        --account-border:#E5E7EB;
        --account-border-strong:#D1D5DB;
        --account-divider:#ECEFF2;
        --account-radius:14px;
        --account-shadow-main:
            0 2px 5px rgba(15,23,42,.045),
            0 10px 24px rgba(15,23,42,.075),
            0 24px 54px rgba(15,23,42,.095),
            inset 0 1px 0 rgba(255,255,255,.92);
        --account-shadow-soft:
            0 1px 3px rgba(15,23,42,.035),
            0 7px 18px rgba(15,23,42,.06),
            0 15px 32px rgba(15,23,42,.065),
            inset 0 1px 0 rgba(255,255,255,.90);
        --account-shadow-focal:
            0 3px 7px rgba(15,23,42,.055),
            0 14px 32px rgba(15,23,42,.095),
            0 30px 68px rgba(15,23,42,.11);

        width:100% !important;
        max-width:1500px !important;
        margin-inline:auto !important;
        padding-bottom:24px !important;
        color:var(--account-text) !important;
        font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif !important;
    }

    .sari-account-page *,
    .sari-account-page *::before,
    .sari-account-page *::after{
        box-sizing:border-box;
    }

    /* Open document-style header */
    .sari-account-page > .mb-5{
        min-height:66px !important;
        margin-bottom:14px !important;
        padding:2px 2px 13px !important;
        border-bottom:1px solid var(--account-divider) !important;
        align-items:flex-end !important;
    }

    .sari-account-page > .mb-5 > div:first-child{
        align-items:flex-start !important;
        gap:0 !important;
    }

    .sari-account-page > .mb-5 > div:first-child > div:first-child{
        display:none !important;
    }

    .sari-account-page > .mb-5 p:first-child{
        margin:0 !important;
        color:var(--account-gold-strong) !important;
        font-size:8px !important;
        font-weight:800 !important;
        line-height:1.15 !important;
        letter-spacing:.16em !important;
        text-transform:uppercase !important;
    }

    .sari-account-page > .mb-5 h1{
        margin-top:5px !important;
        color:var(--account-text) !important;
        font-size:clamp(29px,2.2vw,36px) !important;
        font-weight:700 !important;
        line-height:1 !important;
        letter-spacing:-.035em !important;
    }

    .sari-account-page > .mb-5 h1 span{
        color:var(--account-gold) !important;
    }

    .sari-account-page > .mb-5 h1 + p{
        max-width:760px !important;
        margin-top:7px !important;
        color:var(--account-muted) !important;
        font-size:10px !important;
        font-weight:400 !important;
        line-height:1.55 !important;
    }

    /* Alerts */
    .sari-account-page > .mb-4{
        margin-bottom:14px !important;
        border-radius:14px !important;
        border-color:#D7E7DB !important;
        background:#F5FAF6 !important;
        padding:10px 12px !important;
        color:#4F725A !important;
        font-size:9px !important;
        box-shadow:var(--account-shadow-soft) !important;
    }

    .sari-account-page > .mb-4 > span:first-child{
        width:26px !important;
        height:26px !important;
        background:#fff !important;
        box-shadow:none !important;
    }

    /* Main profile/access workspace */
    .sari-account-page > .grid{
        grid-template-columns:minmax(0,1.55fr) minmax(300px,.72fr) !important;
        gap:14px !important;
        align-items:stretch !important;
    }

    .sari-account-page .account-card{
        border:1px solid var(--account-border) !important;
        border-radius:var(--account-radius) !important;
        background:var(--account-surface) !important;
        box-shadow:var(--account-shadow-main) !important;
    }

    .sari-account-page > .grid > .account-card{
        min-height:100% !important;
        padding:18px !important;
    }

    .sari-account-page > .grid > aside.account-card{
        background:var(--account-surface) !important;
        box-shadow:var(--account-shadow-soft) !important;
    }

    /* Profile identity header */
    .sari-account-page > .grid > section.account-card > div:first-child{
        gap:14px !important;
        padding-bottom:15px !important;
        border-bottom:1px solid var(--account-divider) !important;
    }

    .sari-account-page > .grid > section.account-card > div:first-child > div:first-child{
        gap:11px !important;
    }

    .sari-account-page > .grid > section.account-card > div:first-child .h-12.w-12{
        width:42px !important;
        height:42px !important;
        flex-basis:42px !important;
        border:1px solid #DDE6E2 !important;
        background:#F1F5F3 !important;
        color:#60756E !important;
        font-size:10px !important;
        box-shadow:none !important;
    }

    .sari-account-page > .grid > section.account-card h2{
        color:var(--account-text) !important;
        font-size:14px !important;
        font-weight:700 !important;
        letter-spacing:-.02em !important;
    }

    .sari-account-page > .grid > section.account-card h2 + div{
        margin-top:5px !important;
        gap:6px !important;
    }

    .sari-account-page > .grid > section.account-card h2 + div > span{
        height:21px !important;
        min-height:21px !important;
        padding-inline:7px !important;
        border-radius:999px !important;
        font-size:6.5px !important;
        box-shadow:none !important;
    }

    /* Account ID stays a flat nested tile */
    .sari-account-page > .grid > section.account-card > div:first-child > div:last-child{
        border-color:var(--account-border) !important;
        border-radius:10px !important;
        background:var(--account-surface-soft) !important;
        padding:8px 10px !important;
        box-shadow:none !important;
    }

    .sari-account-page > .grid > section.account-card > div:first-child > div:last-child p:first-child{
        color:var(--account-muted) !important;
        font-size:6.5px !important;
        letter-spacing:.12em !important;
    }

    .sari-account-page > .grid > section.account-card > div:first-child > div:last-child p:last-child{
        margin-top:3px !important;
        color:var(--account-text-secondary) !important;
        font-size:9px !important;
    }

    /* Section headings */
    .sari-account-page .account-card p.font-bold.uppercase{
        color:#8B6B31 !important;
        font-size:8px !important;
        font-weight:800 !important;
        letter-spacing:.14em !important;
    }

    .sari-account-page .account-card p.font-bold.uppercase + p{
        color:var(--account-muted) !important;
        font-size:9px !important;
        line-height:1.5 !important;
    }

    /* Profile form */
    .sari-account-page > .grid > section.account-card > .mt-5{
        margin-top:16px !important;
    }

    .sari-account-page > .grid > section.account-card form{
        gap:13px !important;
    }

    .sari-account-page .field-input{
        height:40px !important;
        margin-top:6px !important;
        border:1px solid var(--account-border-strong) !important;
        border-radius:10px !important;
        background:#fff !important;
        padding-inline:12px !important;
        color:var(--account-text-secondary) !important;
        font-size:10px !important;
        font-weight:500 !important;
        box-shadow:none !important;
        outline:none !important;
        transition:border-color .14s ease,box-shadow .14s ease,background-color .14s ease !important;
    }

    .sari-account-page .field-input:hover{
        border-color:#C7CCD3 !important;
    }

    .sari-account-page .field-input:focus{
        border-color:var(--account-gold) !important;
        background:#fff !important;
        box-shadow:0 0 0 3px rgba(213,150,23,.10) !important;
    }

    .sari-account-page label > span:first-child{
        color:var(--account-text-secondary) !important;
        font-size:9px !important;
        font-weight:600 !important;
    }

    .sari-account-page form button[type="submit"]{
        min-height:40px !important;
        height:40px !important;
        border-radius:10px !important;
        padding-inline:14px !important;
        font-size:9px !important;
        font-weight:700 !important;
        transform:none !important;
    }

    .sari-account-page > .grid > section.account-card form button[type="submit"]{
        border:1px solid var(--account-gold) !important;
        background:var(--account-gold) !important;
        color:#fff !important;
        box-shadow:0 4px 10px rgba(185,120,5,.12) !important;
    }

    .sari-account-page > .grid > section.account-card form button[type="submit"]:hover{
        border-color:var(--account-gold-strong) !important;
        background:var(--account-gold-strong) !important;
    }

    /* Access panel */
    .sari-account-page > .grid > aside.account-card > div:first-child{
        align-items:flex-start !important;
    }

    .sari-account-page > .grid > aside.account-card > div:first-child > span{
        min-height:22px !important;
        height:22px !important;
        padding-inline:8px !important;
        border-radius:999px !important;
        font-size:6.5px !important;
    }

    .sari-account-page > .grid > aside.account-card dl{
        margin-top:16px !important;
    }

    .sari-account-page > .grid > aside.account-card dl > div{
        min-height:36px !important;
        padding-bottom:10px !important;
        border-color:var(--account-divider) !important;
    }

    .sari-account-page > .grid > aside.account-card dl > div:last-child{
        min-height:auto !important;
        padding-bottom:0 !important;
    }

    .sari-account-page > .grid > aside.account-card dt{
        color:var(--account-muted) !important;
        font-size:8px !important;
    }

    .sari-account-page > .grid > aside.account-card dd{
        color:var(--account-text-secondary) !important;
        font-size:8.5px !important;
        font-weight:650 !important;
    }

    /* Security */
    .sari-account-page > section.account-card{
        margin-top:14px !important;
        padding:18px !important;
    }

    .sari-account-page > section.account-card > div:first-child{
        gap:12px !important;
    }

    .sari-account-page > section.account-card > div:first-child > div:first-child > div:first-child{
        gap:7px !important;
    }

    .sari-account-page > section.account-card > div:first-child > div:last-child.rounded-\[10px\]{
        border-color:#E9DEC3 !important;
        background:#FFFBF3 !important;
        color:#8A682A !important;
        box-shadow:none !important;
    }

    .sari-account-page > section.account-card form.mt-5{
        margin-top:16px !important;
        gap:13px !important;
    }

    .sari-account-page [data-password-toggle]{
        width:30px !important;
        height:30px !important;
        border-radius:8px !important;
        color:var(--account-muted) !important;
    }

    .sari-account-page [data-password-toggle]:hover,
    .sari-account-page [data-password-toggle]:focus-visible{
        outline:none !important;
        background:#F5F3EF !important;
        color:#7E5C21 !important;
    }

    .sari-account-page #passwordStrengthBar{
        background:var(--account-gold) !important;
    }

    .sari-account-page > section.account-card form > div:last-child{
        margin-top:2px !important;
        padding-top:13px !important;
        border-color:var(--account-divider) !important;
    }

    .sari-account-page > section.account-card form > div:last-child button[type="submit"]{
        border:1px solid #E1C98D !important;
        background:#FFFAEF !important;
        color:#8B641E !important;
        box-shadow:none !important;
    }

    .sari-account-page > section.account-card form > div:last-child button[type="submit"]:hover{
        border-color:#D8B86A !important;
        background:#FFF5DE !important;
        color:#7B5718 !important;
    }

    /* Activity section + flat nested stats */
    .sari-account-page > section.account-card:last-of-type{
        background:#fff !important;
        box-shadow:var(--account-shadow-main) !important;
    }

    .sari-account-page > section.account-card:last-of-type > div:first-child > span{
        min-height:22px !important;
        height:22px !important;
        padding-inline:8px !important;
        border-radius:999px !important;
        font-size:6.5px !important;
    }

    .sari-account-page > section.account-card:last-of-type > .mt-5{
        margin-top:15px !important;
        gap:10px !important;
    }

    .sari-account-page > section.account-card:last-of-type > .mt-5 > div{
        min-height:88px !important;
        border:1px solid var(--account-border) !important;
        border-radius:11px !important;
        background:var(--account-surface-soft) !important;
        padding:13px !important;
        box-shadow:none !important;
    }

    .sari-account-page > section.account-card:last-of-type > .mt-5 > div p:first-child{
        color:var(--account-muted) !important;
        font-size:6.5px !important;
        font-weight:800 !important;
        letter-spacing:.12em !important;
    }

    .sari-account-page > section.account-card:last-of-type > .mt-5 > div p:nth-child(2){
        margin-top:7px !important;
        color:var(--account-text-secondary) !important;
        font-size:9px !important;
        font-weight:700 !important;
        line-height:1.4 !important;
    }

    .sari-account-page > section.account-card:last-of-type > .mt-5 > div p:last-child{
        margin-top:4px !important;
        color:var(--account-muted) !important;
        font-size:7px !important;
        line-height:1.45 !important;
    }

    /* Validation text */
    .sari-account-page .text-\[7px\].text-\[\#a65f57\]{
        color:#A65F57 !important;
        font-size:7px !important;
    }

    /* Responsive */
    @media(max-width:1100px){
        .sari-account-page > .grid{
            grid-template-columns:1fr !important;
        }

        .sari-account-page > .grid > aside.account-card{
            min-height:auto !important;
        }
    }

    @media(max-width:700px){
        .sari-account-page{
            padding-bottom:18px !important;
        }

        .sari-account-page > .mb-5{
            min-height:auto !important;
            margin-bottom:12px !important;
            padding:0 0 12px !important;
        }

        .sari-account-page > .mb-5 h1{
            font-size:clamp(28px,8vw,34px) !important;
        }

        .sari-account-page > .grid,
        .sari-account-page > section.account-card:last-of-type > .mt-5{
            gap:10px !important;
        }

        .sari-account-page > .grid > .account-card,
        .sari-account-page > section.account-card{
            padding:14px !important;
            border-radius:13px !important;
        }

        .sari-account-page > .grid > section.account-card > div:first-child{
            align-items:flex-start !important;
        }

        .sari-account-page > .grid > section.account-card > div:first-child > div:last-child{
            width:100% !important;
            text-align:left !important;
        }

        .sari-account-page .field-input{
            height:42px !important;
            font-size:10px !important;
        }

        .sari-account-page form button[type="submit"]{
            min-height:42px !important;
            height:42px !important;
        }
    }

    @media(prefers-reduced-motion:reduce){
        .sari-account-page *,
        .sari-account-page *::before,
        .sari-account-page *::after{
            animation:none !important;
            transition:none !important;
            scroll-behavior:auto !important;
        }
    }

</style>
@endpush

@section('content')
@php
    $roleValue = (string) ($adminAccount->role ?? 'super_admin');

    $roleLabel = match($roleValue) {
        'super_admin' => 'Super Administrator',
        'admin' => 'Administrator',
        default => ucwords(str_replace('_', ' ', $roleValue ?: 'Administrator')),
    };

    $canManageSettings = method_exists($adminAccount, 'canManagePlatformSettings')
        ? (bool) $adminAccount->canManagePlatformSettings()
        : ($roleValue === 'super_admin' || (bool) ($adminAccount->can_manage_platform_settings ?? false));

    $accessLabel = $canManageSettings
        ? 'Full Platform Access'
        : 'Standard Admin Access';

    $toDateTime = static function ($value) {
        if (!$value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return \Illuminate\Support\Carbon::instance($value);
        }

        try {
            return \Illuminate\Support\Carbon::parse((string) $value);
        } catch (\Throwable $exception) {
            return null;
        }
    };

    $profileUpdated = $toDateTime($adminAccount->profile_updated_at ?? $adminAccount->updated_at ?? null);
    $passwordChanged = $toDateTime($adminAccount->password_changed_at ?? null);
    $lastLogin = $toDateTime($adminAccount->last_login_at ?? null);

    $sessionBrowser = (string) data_get($sessionInfo ?? [], 'browser', 'Web browser');
    $sessionPlatform = (string) data_get($sessionInfo ?? [], 'platform', 'Unknown platform');

    $hasAdminRecentActivityRoute = \Illuminate\Support\Facades\Route::has('admin.recent-activity');
    $hasUserActivityRoute = \Illuminate\Support\Facades\Route::has('admin.user-activity');
@endphp

<div class="sari-account-page mx-auto max-w-[1180px] pb-8">
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex items-start gap-3.5">
            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-[12px] border border-[#eadfc8] bg-[#fff8e9] text-[#a66f16]">
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="8" r="4"></circle>
                    <path d="M4.8 20c.9-4 3.4-6 7.2-6s6.3 2 7.2 6"></path>
                </svg>
            </div>
            <div>
                <p class="text-[8px] font-bold uppercase tracking-[.18em] text-[#aa7a25]">Administration</p>
                <h1 class="mt-1 text-[28px] font-bold leading-none tracking-[-.035em] text-[#25211d]">Account <span class="text-[#c99128]">Management</span></h1>
                <p class="mt-2 max-w-[620px] text-[10px] leading-5 text-[#7e766d]">Manage administrator identity, password security, access information, and account activity.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 flex items-center gap-2.5 rounded-[13px] border border-[#dbe8df] bg-[#f5faf6] px-4 py-3 text-[9px] font-medium text-[#51715d]">
            <span class="grid h-6 w-6 place-items-center rounded-full bg-white text-[#568269]">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg>
            </span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-[minmax(0,1.45fr)_minmax(310px,.75fr)]">
        <section class="account-card rounded-[16px] border border-[#e8e3dc] bg-white p-5 sm:p-6">
            <div class="flex flex-col gap-4 border-b border-[#eee9e3] pb-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex min-w-0 items-center gap-3.5">
                    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-full border border-[#dce5e1] bg-[#eef3f1] text-[11px] font-bold text-[#60756e]">
                        {{ collect(preg_split('/\s+/', trim($adminAccount->name)))->filter()->map(fn($part) => strtoupper(substr($part,0,1)))->take(2)->implode('') ?: 'SA' }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="truncate text-[15px] font-bold text-[#2b2622]">{{ $adminAccount->name }}</h2>
                        <div class="mt-1 flex flex-wrap items-center gap-2">
                            <span class="inline-flex h-5 items-center rounded-full border border-[#ead39d] bg-[#fff8e9] px-2 text-[7px] font-bold text-[#a26e17]">{{ $roleLabel }}</span>
                            <span class="inline-flex h-5 items-center gap-1.5 rounded-full border border-[#dce7df] bg-[#f5f9f6] px-2 text-[7px] font-bold text-[#617568]"><span class="h-1.5 w-1.5 rounded-full bg-[#4a9a68]"></span>Active</span>
                        </div>
                    </div>
                </div>
                <div class="rounded-[11px] border border-[#ece7e0] bg-[#faf9f7] px-3 py-2 text-right">
                    <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#9a9187]">Account ID</p>
                    <p class="mt-1 text-[9px] font-semibold text-[#4d463f]">ADM-{{ str_pad((string) $adminAccount->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>

            <div class="mt-5">
                <div class="mb-4">
                    <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#8f806d]">Administrator Profile</p>
                    <p class="mt-1 text-[9px] text-[#91887e]">Update the identity used for your SARI administrator account.</p>
                </div>

                <form method="POST" action="{{ route('admin.account.profile.update') }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @method('PATCH')

                    <label class="block">
                        <span class="text-[8px] font-semibold text-[#5f574f]">Full name</span>
                        <input name="name" value="{{ old('name', $adminAccount->name) }}" autocomplete="name" class="field-input mt-2 h-11 w-full rounded-[11px] border border-[#ddd7cf] bg-white px-3.5 text-[9px] font-medium text-[#332d28] transition" required>
                        @error('name', 'profile')<span class="mt-1.5 block text-[7px] text-[#a65f57]">{{ $message }}</span>@enderror
                    </label>

                    <label class="block">
                        <span class="text-[8px] font-semibold text-[#5f574f]">Email address</span>
                        <input name="email" type="email" value="{{ old('email', $adminAccount->email) }}" autocomplete="email" class="field-input mt-2 h-11 w-full rounded-[11px] border border-[#ddd7cf] bg-white px-3.5 text-[9px] font-medium text-[#332d28] transition" required>
                        @error('email', 'profile')<span class="mt-1.5 block text-[7px] text-[#a65f57]">{{ $message }}</span>@enderror
                    </label>

                    <div class="sm:col-span-2 flex justify-end pt-1">
                        <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-[10px] bg-[#c99128] px-4 text-[9px] font-bold text-white shadow-[0_6px_14px_rgba(166,112,18,.14)] transition hover:bg-[#b6811f]">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M5 12l4 4L19 6"/></svg>
                            Save profile
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <aside class="account-card rounded-[16px] border border-[#e8e3dc] bg-[#fbfaf8] p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#8f806d]">Account Access</p>
                    <p class="mt-1 text-[9px] text-[#91887e]">Current platform authorization.</p>
                </div>
                <span class="inline-flex h-6 items-center gap-1.5 rounded-full border border-[#dce7df] bg-white px-2.5 text-[7px] font-bold text-[#617568]"><span class="h-1.5 w-1.5 rounded-full bg-[#4a9a68]"></span>Active</span>
            </div>

            <dl class="mt-5 space-y-3">
                <div class="flex items-center justify-between gap-4 border-b border-[#ece7e0] pb-3"><dt class="text-[8px] text-[#8f867d]">Role</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">{{ $roleLabel }}</dd></div>
                <div class="flex items-center justify-between gap-4 border-b border-[#ece7e0] pb-3"><dt class="text-[8px] text-[#8f867d]">Access level</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">{{ $accessLabel }}</dd></div>
                <div class="flex items-center justify-between gap-4 border-b border-[#ece7e0] pb-3"><dt class="text-[8px] text-[#8f867d]">Authentication</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">Password</dd></div>
                <div class="flex items-center justify-between gap-4 border-b border-[#ece7e0] pb-3"><dt class="text-[8px] text-[#8f867d]">Platform settings</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">{{ $canManageSettings ? 'Allowed' : 'Restricted' }}</dd></div>
                <div class="flex items-center justify-between gap-4"><dt class="text-[8px] text-[#8f867d]">Account created</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">{{ optional($adminAccount->created_at)->format('M d, Y') ?? '—' }}</dd></div>
            </dl>
        </aside>
    </div>

    <section class="account-card mt-4 rounded-[16px] border border-[#e8e3dc] bg-white p-5 sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#8f806d]">Account Security</p>
                    <span class="inline-flex h-5 items-center gap-1.5 rounded-full border border-[#dce7df] bg-[#f5f9f6] px-2 text-[7px] font-bold text-[#617568]"><span class="h-1.5 w-1.5 rounded-full bg-[#4a9a68]"></span>Secured</span>
                </div>
                <p class="mt-1.5 text-[9px] text-[#91887e]">Change your administrator password. Your current password is required.</p>
            </div>
            <div class="rounded-[10px] border border-[#eee7da] bg-[#fffaf0] px-3 py-2 text-[7px] text-[#8b6a32]">Minimum 8 characters</div>
        </div>

        <form method="POST" action="{{ route('admin.account.password.update') }}" class="mt-5 grid gap-4 lg:grid-cols-3">
            @csrf
            @method('PATCH')

            @foreach([
                ['current_password', 'Current password', 'current-password'],
                ['password', 'New password', 'new-password'],
                ['password_confirmation', 'Confirm new password', 'new-password'],
            ] as [$field, $label, $autocomplete])
                <label class="block">
                    <span class="text-[8px] font-semibold text-[#5f574f]">{{ $label }}</span>
                    <div class="relative mt-2">
                        <input id="{{ $field }}" name="{{ $field }}" type="password" autocomplete="{{ $autocomplete }}" class="field-input h-11 w-full rounded-[11px] border border-[#ddd7cf] bg-white px-3.5 pr-10 text-[9px] font-medium text-[#332d28] transition" required>
                        <button type="button" data-password-toggle="{{ $field }}" class="absolute right-2 top-1/2 grid h-7 w-7 -translate-y-1/2 place-items-center rounded-lg text-[#8b837a] transition hover:bg-[#f5f2ed] hover:text-[#76551d]" aria-label="Show or hide password">
                            <svg data-eye-open viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                        </button>
                    </div>
                    @error($field, 'password')<span class="mt-1.5 block text-[7px] text-[#a65f57]">{{ $message }}</span>@enderror
                </label>
            @endforeach

            <div class="lg:col-span-3 flex flex-col gap-3 border-t border-[#eee9e3] pt-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1 sm:max-w-[420px]">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[7px] font-semibold text-[#756d64]">Password strength</span>
                        <span id="passwordStrengthLabel" class="text-[7px] font-bold text-[#9a9187]">Enter a new password</span>
                    </div>
                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#eeeae4]"><div id="passwordStrengthBar" class="h-full w-0 rounded-full bg-[#c99128] transition-all duration-200"></div></div>
                </div>

                <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-[10px] border border-[#d7b86f] bg-[#fff9ec] px-4 text-[9px] font-bold text-[#97691d] transition hover:bg-[#fff3d8]">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                    Update password
                </button>
            </div>
        </form>
    </section>

    <section class="account-card mt-4 rounded-[16px] border border-[#e8e3dc] bg-[#fbfaf8] p-5 sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#8f806d]">Login & Account Activity</p>
                <p class="mt-1 text-[9px] text-[#91887e]">Security information recorded by the administrator account.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex w-fit h-6 items-center gap-1.5 rounded-full border border-[#dce7df] bg-white px-2.5 text-[7px] font-bold text-[#617568]"><span class="h-1.5 w-1.5 rounded-full bg-[#4a9a68]"></span>Current session active</span>
                @if($hasAdminRecentActivityRoute)
                    <a href="{{ route('admin.recent-activity') }}" class="inline-flex h-8 items-center justify-center rounded-[9px] border border-[#d7b86f] bg-[#fff9ec] px-3 text-[7px] font-bold text-[#8f651c] transition hover:bg-[#fff3d8]">Admin Recent Activity</a>
                @endif
                @if($hasUserActivityRoute)
                    <a href="{{ route('admin.user-activity') }}" class="inline-flex h-8 items-center justify-center rounded-[9px] border border-[#ddd7cf] bg-white px-3 text-[7px] font-bold text-[#655e56] transition hover:bg-[#f8f6f2]">User Activity Log</a>
                @endif
            </div>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[12px] border border-[#e8e3dc] bg-white p-4">
                <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#a0968c]">Current session</p>
                <p class="mt-2 text-[9px] font-bold text-[#413a34]">{{ $sessionBrowser }} · {{ $sessionPlatform }}</p>
                <p class="mt-1 text-[7px] text-[#8f867d]">Active now</p>
            </div>
            <div class="rounded-[12px] border border-[#e8e3dc] bg-white p-4">
                <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#a0968c]">Last sign-in</p>
                <p class="mt-2 text-[9px] font-bold text-[#413a34]">{{ $lastLogin ? $lastLogin->format('M d, Y · h:i A') : 'Not recorded yet' }}</p>
                <p class="mt-1 text-[7px] text-[#8f867d]">Login count: {{ number_format((int) ($adminAccount->login_count ?? 0)) }}</p>
            </div>
            <div class="rounded-[12px] border border-[#e8e3dc] bg-white p-4">
                <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#a0968c]">Profile updated</p>
                <p class="mt-2 text-[9px] font-bold text-[#413a34]">{{ $profileUpdated ? $profileUpdated->format('M d, Y') : 'Not recorded' }}</p>
                <p class="mt-1 text-[7px] text-[#8f867d]">Identity and email changes</p>
            </div>
            <div class="rounded-[12px] border border-[#e8e3dc] bg-white p-4">
                <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#a0968c]">Password changed</p>
                <p class="mt-2 text-[9px] font-bold text-[#413a34]">{{ $passwordChanged ? $passwordChanged->format('M d, Y') : 'Not recorded yet' }}</p>
                <p class="mt-1 text-[7px] text-[#8f867d]">Security credential history</p>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
(function () {
    function initAccountPage() {
        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
            if (button.dataset.bound === '1') return;
            button.dataset.bound = '1';

            button.addEventListener('click', function () {
                const input = document.getElementById(button.dataset.passwordToggle);
                if (!input) return;
                input.type = input.type === 'password' ? 'text' : 'password';
            });
        });

        const password = document.getElementById('password');
        const bar = document.getElementById('passwordStrengthBar');
        const label = document.getElementById('passwordStrengthLabel');

        if (password && bar && label && password.dataset.strengthBound !== '1') {
            password.dataset.strengthBound = '1';

            const updateStrength = function () {
                const value = password.value;
                let score = 0;
                if (value.length >= 8) score++;
                if (value.length >= 12) score++;
                if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++;
                if (/\d/.test(value)) score++;
                if (/[^A-Za-z0-9]/.test(value)) score++;

                if (!value) {
                    bar.style.width = '0%';
                    label.textContent = 'Enter a new password';
                    label.style.color = '#9a9187';
                    return;
                }

                const percent = Math.max(20, Math.min(100, score * 20));
                bar.style.width = percent + '%';

                if (score <= 2) {
                    label.textContent = 'Basic';
                    label.style.color = '#9a6a45';
                } else if (score <= 4) {
                    label.textContent = 'Good';
                    label.style.color = '#9a741f';
                } else {
                    label.textContent = 'Strong';
                    label.style.color = '#577660';
                }
            };

            password.addEventListener('input', updateStrength);
            updateStrength();
        }
    }

    document.addEventListener('DOMContentLoaded', initAccountPage, { once: true });
    document.addEventListener('livewire:navigated', initAccountPage);
})();
</script>
@endpush
@endsection
