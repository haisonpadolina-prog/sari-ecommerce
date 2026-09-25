@extends('layouts.app')

@section('title', 'Login — SARI')

@section('content')

{{-- Tailwind CDN for this page --}}
<script src="https://cdn.tailwindcss.com"></script>

{{-- Poppins was referenced via font-['Poppins'] but never loaded — this was silently
     falling back to the browser's generic sans-serif, which is why type felt "off". --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --sari-yellow: #d48f08;
        --sari-yellow-dark: #bd7d05;
        --sari-text: #1f1b16;
        --sari-muted: #756d63;
        --sari-border: #e4ddd3;
        --sari-surface: #ffffff;
    }

    .sari-login-card {
        animation: sariLoginFade .42s ease-out both;
    }

    .sari-login-brand {
        animation: sariLoginFade .35s ease-out both;
    }

    @keyframes sariLoginFade {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .sari-login-input {
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .sari-login-input:focus {
        border-color: var(--sari-yellow);
        box-shadow: 0 0 0 4px rgba(212, 143, 8, .08);
    }

    /* --- THE FIX for the "blue tint / black text contrast" issue ---
       Chrome/Edge/Safari paint their own background + text color on
       autofilled inputs, ignoring bg-white on the element. This forces
       the field to stay on our surface color and keep our text color
       instead of the browser's default light-blue autofill style. */
    .sari-login-input:-webkit-autofill,
    .sari-login-input:-webkit-autofill:hover,
    .sari-login-input:-webkit-autofill:focus {
        -webkit-text-fill-color: var(--sari-text);
        caret-color: var(--sari-text);
        transition: background-color 9999s ease-in-out 0s;
        box-shadow: 0 0 0 1000px var(--sari-surface) inset;
    }

    .sari-login-button {
        transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    .sari-login-button:hover {
        background: var(--sari-yellow-dark);
        border-color: var(--sari-yellow-dark);
        box-shadow: 0 8px 20px rgba(189, 125, 5, .16);
    }

    .sari-login-social {
        transition: background-color .18s ease, border-color .18s ease, color .18s ease;
    }

    .sari-login-social:hover {
        background: rgba(255, 250, 241, .72);
        color: #8f5d06;
    }

    /*
     * Responsive sizing strategy — SIMPLIFIED.
     * Previously this used clamp() PLUS two separate hard @media(max-height)
     * breakpoints that re-declared almost every value again. Every time the
     * viewport crossed 820px or 690px height, spacing jumped abruptly instead
     * of scaling smoothly — that abrupt jump is what read as "sabog" on
     * common laptop resolutions. A single clamp() per property, with the
     * floor tuned for short viewports, removes the jump entirely.
     */
    .sari-login-page {
        padding-top: clamp(12px, 2.2vh, 26px);
        padding-bottom: clamp(12px, 2.2vh, 26px);
    }

    .sari-login-main {
        padding-top: clamp(8px, 2vh, 22px);
        padding-bottom: clamp(8px, 2vh, 22px);
    }

    .sari-login-wrapper {
        width: min(100%, 430px);
    }

    .sari-login-brand {
        margin-bottom: clamp(6px, 1.5vh, 14px);
        text-align: center;
    }

    .sari-login-brand img {
        width: clamp(105px, 7.8vw, 145px);
        transform: none !important;
        margin-inline: auto;
    }

    .sari-login-tagline {
        margin-top: clamp(3px, .5vh, 5px);
        font-size: clamp(7px, .55vw, 8px);
    }

    .sari-login-card {
        width: 100%;
        max-width: 430px;
        min-height: clamp(545px, 68vh, 625px);
        padding:
            clamp(24px, 5.8vh, 58px)
            clamp(18px, 1.7vw, 24px)
            clamp(20px, 3vh, 34px);
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }

    .sari-login-header {
        margin-bottom: clamp(11px, 2.2vh, 22px);
    }

    .sari-login-title {
        font-size: clamp(22px, 1.6vw, 26px);
    }

    .sari-login-description {
        margin-top: clamp(4px, .8vh, 8px);
        font-size: clamp(10px, .78vw, 12px);
        line-height: 1.5;
    }

    .sari-login-form {
        display: grid;
        gap: clamp(9px, 1.9vh, 18px);
    }

    .sari-login-control {
        height: clamp(36px, 5.2vh, 46px);
    }

    .sari-login-divider {
        margin-top: clamp(9px, 2vh, 20px);
        margin-bottom: clamp(9px, 1.8vh, 18px);
    }

    .sari-login-social-control {
        height: clamp(36px, 4.5vh, 40px);
    }

    .sari-login-signup {
        margin-top: clamp(9px, 2.3vh, 23px);
    }

    .sari-login-footer {
        padding-top: clamp(8px, 1.2vh, 13px);
        font-size: clamp(10px, .72vw, 12px);
        line-height: 1.5;
    }

    /* Refined type scale and controls */
    .sari-field-label {
        margin-bottom: 7px;
        color: #383126;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.25;
        letter-spacing: -0.01em;
    }

    .sari-login-input {
        font-size: 12.5px;
        font-weight: 400;
        letter-spacing: -0.005em;
    }

    .sari-option-text {
        color: #756d63;
        font-size: 11.5px;
        font-weight: 400;
        line-height: 1;
    }

    .sari-forgot-link {
        color: #a96f06;
        font-size: 11.5px;
        font-weight: 600;
        line-height: 1;
    }

    .sari-forgot-link:hover {
        color: #7f5104;
    }

    .sari-remember-box {
        display: inline-flex;
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
        align-items: center;
        justify-content: center;
        border: 1px solid #cfc6b9;
        border-radius: 4px;
        background: #fff;
        color: #fff;
        transition: background-color .16s ease, border-color .16s ease, box-shadow .16s ease;
    }

    .sari-remember-box svg {
        width: 9px;
        height: 9px;
        opacity: 0;
        transform: scale(.75);
        transition: opacity .14s ease, transform .14s ease;
    }

    .peer:checked + .sari-remember-box {
        border-color: var(--sari-yellow);
        background: var(--sari-yellow);
    }

    .peer:checked + .sari-remember-box svg {
        opacity: 1;
        transform: scale(1);
    }

    .peer:focus-visible + .sari-remember-box {
        box-shadow: 0 0 0 3px rgba(212, 143, 8, .13);
    }

    .sari-login-button {
        font-size: 11.5px;
        font-weight: 600;
        letter-spacing: .01em;
    }

    .sari-login-button svg {
        width: 14px;
        height: 14px;
    }

    .sari-login-social {
        font-size: 11.5px;
        font-weight: 600;
        letter-spacing: -0.005em;
    }

    .sari-divider-label {
        font-size: 8px;
        font-weight: 500;
        letter-spacing: .15em;
        color: #aaa196;
    }

    .sari-signup-copy {
        font-size: 11.5px;
        line-height: 1.45;
    }

    .sari-signup-copy a {
        font-weight: 600;
    }

    /* Lower login actions — small breathing room after Password */
    .sari-login-lower-start {
        margin-top: clamp(8px, 1.3vh, 13px);
    }

    /* Mobile keeps comfortable touch targets even with the compact desktop layout. */
    @media (max-width: 639px) {
        .sari-login-wrapper {
            width: min(100%, 430px);
        }

        .sari-login-card {
            padding: 28px 18px 20px;
        }

        .sari-login-brand img {
            width: 120px;
        }

        .sari-login-control {
            min-height: 46px;
        }

        .sari-login-social-control {
            min-height: 44px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-login-card,
        .sari-login-brand,
        .sari-login-input,
        .sari-login-button,
        .sari-login-social {
            animation: none !important;
            transition: none !important;
        }
    }
</style>

<div
    class="relative min-h-screen min-h-[100dvh] overflow-x-hidden bg-[#f7f4ee] font-['Poppins',sans-serif]"
>

    {{-- BACKGROUND IMAGE --}}
    <div class="absolute inset-0">
        <img
            src="{{ asset('images/login-bg.jpg') }}"
            alt=""
            class="h-full w-full object-cover object-center"
        >

        {{-- Solid overlays only — no gradients --}}
        <div class="absolute inset-0 bg-white/68"></div>
        <div class="absolute inset-0 bg-[#fffaf1]/18"></div>
    </div>

    {{-- PAGE --}}
    <div class="sari-login-page relative z-10 mx-auto flex min-h-screen min-h-[100dvh] w-full max-w-[1500px] flex-col px-4 min-[380px]:px-5 sm:px-6 md:px-8 lg:px-10 xl:px-12">

        {{-- MAIN CONTENT --}}
        <div class="sari-login-main flex flex-1 items-start justify-center sm:items-center">

            <div class="sari-login-wrapper flex flex-col items-center">

                {{-- BRAND — shares the exact same center as the card --}}
                <a
                    href="{{ route('home') }}"
                    class="sari-login-brand inline-flex flex-col items-center"
                    aria-label="Back to SARI home"
                >
                    <img
                        src="{{ asset('images/sari-logo.png') }}"
                        alt="SARI"
                        class="h-auto object-contain brightness-0"
                    >

                    <span class="sari-login-tagline text-center font-semibold uppercase tracking-[0.22em] text-[#9a9185] sm:tracking-[0.24em]">
                        Elevated Everyday
                    </span>
                </a>

                <div
                    class="sari-login-card rounded-[18px] border border-[#e7e0d7] bg-white shadow-[0_16px_44px_rgba(39,31,21,.08)]"
                >

                {{-- HEADER --}}
                <div class="sari-login-header text-center">
                    <span class="mb-1.5 block text-[8px] font-bold uppercase tracking-[0.14em] text-[#a96f06] min-[380px]:text-[8.5px] sm:mb-2 lg:text-[9px]">
                        Welcome back
                    </span>

                    <h1 class="sari-login-title font-bold leading-[1.15] tracking-[-0.035em] text-[#1f1b16]">
                        Sign in to SARI
                    </h1>

                    <p class="sari-login-description mx-auto max-w-[390px] text-[#81786c]">
                        Access your account to continue shopping, selling, or managing deliveries.
                    </p>
                </div>

                {{-- STATUS / PASSWORD RESET SUCCESS --}}
                @if (session('status'))
                    <div
                        class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[12px] leading-5 text-emerald-700 sm:text-[13px]"
                        role="status"
                    >
                        <svg viewBox="0 0 24 24" class="mt-0.5 h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="m8 12 2.5 2.5L16 9"></path>
                        </svg>
                        <p>{{ session('status') }}</p>
                    </div>
                @endif

                {{-- LOGIN ERROR --}}
                @if ($errors->any())
                    <div
                        class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[12px] leading-5 text-red-700 sm:text-[13px]"
                        role="alert"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="mt-0.5 h-[18px] w-[18px] shrink-0"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            aria-hidden="true"
                        >
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 8v5"></path>
                            <path d="M12 16.5h.01"></path>
                        </svg>

                        <div>
                            <p class="font-semibold">
                                Unable to log in
                            </p>

                            <p class="mt-0.5">
                                {{ $errors->first() }}
                            </p>
                        </div>
                    </div>
                @endif

                {{-- FORM --}}
                <form
                    method="POST"
                    action="{{ route('login.submit') }}"
                    class="sari-login-form"
                >
                    @csrf

                    {{-- EMAIL --}}
                    <div>
                        <label
                            for="email"
                            class="sari-field-label block"
                        >
                            Email Address
                        </label>

                        <div class="relative">
                            <span
                                class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-[#9b9287] sm:w-12"
                                aria-hidden="true"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-[17px] w-[17px] sm:h-[18px] sm:w-[18px]"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                >
                                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                    <path d="m3 7 9 6 9-6"></path>
                                </svg>
                            </span>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter your email"
                                autocomplete="email"
                                autofocus
                                required
                                class="sari-login-input sari-login-control w-full rounded-xl border border-[#e4ddd3] bg-white pl-11 pr-4 text-[#17140e] outline-none placeholder:text-[#aaa196] focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10 sm:pl-12"
                            >
                        </div>

                        @error('email')
                            <p class="mt-2 text-[12px] text-red-600 sm:text-[13px]">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- PASSWORD --}}
                    <div>
                        <label
                            for="password"
                            class="sari-field-label block"
                        >
                            Password
                        </label>

                        <div class="relative">
                            <span
                                class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-[#9b9287] sm:w-12"
                                aria-hidden="true"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-[17px] w-[17px] sm:h-[18px] sm:w-[18px]"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                >
                                    <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                                    <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                                </svg>
                            </span>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                                class="sari-login-input sari-login-control w-full rounded-xl border border-[#e4ddd3] bg-white pl-11 pr-11 text-[#17140e] outline-none placeholder:text-[#aaa196] focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10 sm:pl-12 sm:pr-12"
                            >

                            <button
                                type="button"
                                id="sariPasswordToggle"
                                class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-[#a59b8b] transition hover:text-[#c98a08] sm:w-12"
                                aria-label="Show password"
                            >
                                <svg
                                    id="sariEyeOpen"
                                    viewBox="0 0 24 24"
                                    class="h-[17px] w-[17px] sm:h-[18px] sm:w-[18px]"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                >
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>

                                <svg
                                    id="sariEyeClosed"
                                    viewBox="0 0 24 24"
                                    class="hidden h-[17px] w-[17px] sm:h-[18px] sm:w-[18px]"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                >
                                    <path d="m3 3 18 18"></path>
                                    <path d="M10.6 10.7a2 2 0 0 0 2.7 2.7"></path>
                                    <path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6 0 9.5 7 9.5 7a16 16 0 0 1-2.1 2.8"></path>
                                    <path d="M6.2 6.2C3.8 8 2.5 12 2.5 12S6 19 12 19a9.5 9.5 0 0 0 3.2-.5"></path>
                                </svg>
                            </button>
                        </div>

                        @error('password')
                            <p class="mt-2 text-[12px] text-red-600 sm:text-[13px]">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- OPTIONS --}}
                    <div class="sari-login-lower-start flex flex-col gap-3 min-[360px]:flex-row min-[360px]:items-center min-[360px]:justify-between">
                        <label class="inline-flex cursor-pointer select-none items-center gap-2">
                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                class="peer sr-only"
                            >
                            <span class="sari-remember-box" aria-hidden="true">
                                <svg viewBox="0 0 12 12" fill="none">
                                    <path
                                        d="M2.2 6.1 4.7 8.5 9.8 3.5"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </span>
                            <span class="sari-option-text">Remember me</span>
                        </label>

                        <a
                            href="{{ route('password.request') }}"
                            class="sari-forgot-link transition"
                        >
                            Forgot password?
                        </a>
                    </div>

                    {{-- LOGIN --}}
                    <button
                        type="submit"
                        aria-label="Login to SARI"
                        class="sari-login-button sari-login-control flex w-full items-center justify-center gap-2 rounded-xl border border-[#d48f08] bg-[#d48f08] px-4 text-white shadow-[0_7px_18px_rgba(212,143,8,.14)]"
                    >
                        <span>Sign In</span>

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M5 12h14"></path>
                            <path d="m14 7 5 5-5 5"></path>
                        </svg>
                    </button>
                </form>

                {{-- DIVIDER --}}
                <div class="sari-login-divider flex items-center gap-3 sm:gap-4">
                    <span class="h-px flex-1 bg-[#eee8df]"></span>
                    <span class="sari-divider-label whitespace-nowrap uppercase text-[#9b9287]">
                        or continue with
                    </span>
                    <span class="h-px flex-1 bg-[#eee8df]"></span>
                </div>

                {{-- SOCIAL --}}
                <div class="grid grid-cols-1 gap-2.5 sm:gap-3">

                    {{-- GOOGLE --}}
                    <a
                        href="{{ route('oauth.redirect', ['provider' => 'google']) }}"
                        class="sari-login-social sari-login-social-control flex items-center justify-center gap-2 rounded-lg border border-[#e4ddd3] bg-transparent text-[#28241e] hover:border-[#d8cdb9] hover:bg-[#fffaf1]/70"
                        aria-label="Continue with Google"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-[17px] w-[17px] shrink-0 sm:h-[18px] sm:w-[18px]"
                            aria-hidden="true"
                        >
                            <path fill="#4285F4" d="M21.6 12.23c0-.71-.06-1.4-.18-2.07H12v3.92h5.38a4.6 4.6 0 0 1-2 3.02v2.51h3.23c1.89-1.74 2.99-4.31 2.99-7.38Z"/>
                            <path fill="#34A853" d="M12 22c2.7 0 4.97-.9 6.62-2.39l-3.23-2.51c-.9.6-2.04.96-3.39.96-2.61 0-4.82-1.76-5.61-4.13H3.06v2.59A10 10 0 0 0 12 22Z"/>
                            <path fill="#FBBC05" d="M6.39 13.93A6.02 6.02 0 0 1 6.08 12c0-.67.12-1.32.31-1.93V7.48H3.06A10 10 0 0 0 2 12c0 1.61.38 3.13 1.06 4.52l3.33-2.59Z"/>
                            <path fill="#EA4335" d="M12 5.94c1.47 0 2.79.51 3.83 1.5l2.87-2.87A9.66 9.66 0 0 0 12 2a10 10 0 0 0-8.94 5.48l3.33 2.59C7.18 7.7 9.39 5.94 12 5.94Z"/>
                        </svg>

                        Continue with Google
                    </a>

                </div>

                {{-- SIGNUP --}}
                <div class="sari-login-signup sari-signup-copy text-center text-[#6f6658]">
                    <span>Don't have an account?</span>

                    <a
                        href="{{ route('register') }}"
                        class="ml-1 text-[#a96f06] transition hover:text-[#7f5104]"
                    >
                        Create one
                    </a>
                </div>

                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <footer class="sari-login-footer flex justify-start border-t border-[#e4ddd3] text-left text-[#756b5b]">
            <div class="flex flex-wrap items-center justify-start gap-x-4 gap-y-1">
                <span>
                    © {{ date('Y') }} SARI. All rights reserved.
                </span>

                <a
                    href="#"
                    class="font-medium transition hover:text-[#a96f06]"
                >
                    Privacy Policy
                </a>

                <a
                    href="#"
                    class="font-medium transition hover:text-[#a96f06]"
                >
                    Terms of Service
                </a>
            </div>
        </footer>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('password');
    const toggle = document.getElementById('sariPasswordToggle');
    const eyeOpen = document.getElementById('sariEyeOpen');
    const eyeClosed = document.getElementById('sariEyeClosed');

    if (!input || !toggle) return;

    toggle.addEventListener('click', function () {
        const showing = input.type === 'text';

        input.type = showing ? 'password' : 'text';

        eyeOpen?.classList.toggle('hidden', !showing);
        eyeClosed?.classList.toggle('hidden', showing);

        toggle.setAttribute(
            'aria-label',
            showing ? 'Show password' : 'Hide password'
        );
    });
});
</script>

@endsection