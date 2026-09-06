@extends('layouts.app')

@section('title', 'Login — SARI')

@section('content')

{{-- Tailwind CDN for this page --}}
<script src="https://cdn.tailwindcss.com"></script>

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
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .sari-login-input {
        transition:
            border-color .18s ease,
            box-shadow .18s ease;
    }

    .sari-login-input:focus {
        border-color: var(--sari-yellow);
        box-shadow: 0 0 0 4px rgba(212, 143, 8, .08);
    }

    .sari-login-button {
        transition:
            background-color .18s ease,
            border-color .18s ease,
            box-shadow .18s ease;
    }

    .sari-login-button:hover {
        background: var(--sari-yellow-dark);
        border-color: var(--sari-yellow-dark);
        box-shadow: 0 8px 20px rgba(189, 125, 5, .16);
    }

    .sari-login-social {
        transition:
            background-color .18s ease,
            border-color .18s ease,
            color .18s ease;
    }

    .sari-login-social:hover {
        background: #fffaf1;
        border-color: #d8bd89;
        color: #8f5d06;
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
    <div class="relative z-10 mx-auto flex min-h-screen min-h-[100dvh] w-full max-w-[1500px] flex-col px-4 py-5 min-[380px]:px-5 sm:px-6 sm:py-6 md:px-8 lg:px-10 lg:py-7 xl:px-12">

        {{-- MAIN CONTENT --}}
        <div class="flex flex-1 items-start justify-center py-4 sm:items-center sm:py-6 lg:py-8">

            <div class="flex w-full max-w-[590px] flex-col items-center">

                {{-- BRAND — shares the exact same center as the card --}}
                <a
                    href="{{ route('home') }}"
                    class="sari-login-brand mb-5 inline-flex flex-col items-center sm:mb-6 lg:mb-7"
                    aria-label="Back to SARI home"
                >
                    <img
                        src="{{ asset('images/sari-logo.png') }}"
                        alt="SARI"
                        class="h-auto w-[145px] object-contain brightness-0 min-[380px]:w-[155px] sm:w-[175px] lg:w-[195px] xl:w-[205px]"
                    >

                    <span class="mt-2 text-center text-[8px] font-semibold uppercase tracking-[0.22em] text-[#9a9185] min-[380px]:text-[9px] sm:tracking-[0.24em] lg:text-[10px]">
                        Elevated Everyday
                    </span>
                </a>

                <div
                    class="sari-login-card w-full max-w-[560px] rounded-[18px] border border-[#e7e0d7] bg-white p-5 shadow-[0_16px_44px_rgba(39,31,21,.08)] min-[380px]:p-6 sm:p-8 lg:p-9 xl:p-10"
                >

                {{-- HEADER --}}
                <div class="mb-6 text-center sm:mb-7 lg:mb-8">
                    <span class="mb-2 block text-[8px] font-bold uppercase tracking-[0.14em] text-[#a96f06] min-[380px]:text-[9px] sm:mb-2.5 lg:text-[10px]">
                        Welcome back
                    </span>

                    <h1 class="text-[25px] font-bold leading-[1.15] tracking-[-0.035em] text-[#1f1b16] min-[380px]:text-[28px] sm:text-[32px] lg:text-[34px]">
                        Sign in to SARI
                    </h1>

                    <p class="mx-auto mt-2.5 max-w-[410px] text-[11px] leading-5 text-[#81786c] min-[380px]:text-[12px] sm:mt-3 sm:text-[13px] sm:leading-6">
                        Access your account to continue shopping, selling, or managing deliveries.
                    </p>
                </div>

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
                    class="space-y-5 sm:space-y-6"
                >
                    @csrf

                    {{-- EMAIL --}}
                    <div>
                        <label
                            for="email"
                            class="mb-2 block text-[13px] font-semibold text-[#312b22] sm:mb-2.5 sm:text-[14px]"
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
                                class="sari-login-input h-[52px] w-full rounded-xl border border-[#e4ddd3] bg-white pl-11 pr-4 text-[14px] text-[#17140e] outline-none placeholder:text-[#aaa196] focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10 sm:h-[56px] sm:pl-12 sm:text-[15px]"
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
                            class="mb-2 block text-[13px] font-semibold text-[#312b22] sm:mb-2.5 sm:text-[14px]"
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
                                class="sari-login-input h-[52px] w-full rounded-xl border border-[#e4ddd3] bg-white pl-11 pr-11 text-[14px] text-[#17140e] outline-none placeholder:text-[#aaa196] focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10 sm:h-[56px] sm:pl-12 sm:pr-12 sm:text-[15px]"
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
                    <div class="flex flex-col gap-3 min-[360px]:flex-row min-[360px]:items-center min-[360px]:justify-between">
                        <label class="inline-flex cursor-pointer items-center gap-2.5 text-[12px] text-[#6f6658] sm:text-[13px]">
                            <input
                                type="checkbox"
                                name="remember"
                                class="h-4 w-4 shrink-0 rounded border-[#d8d1c8] accent-[#d48f08]"
                            >
                            <span>Remember me</span>
                        </label>

                        <a
                            href="#"
                            class="text-[12px] font-semibold text-[#a56c08] transition hover:text-[#7f5104] sm:text-[13px]"
                        >
                            Forgot password?
                        </a>
                    </div>

                    {{-- LOGIN --}}
                    <button
                        type="submit"
                        aria-label="Login to SARI"
                        class="sari-login-button flex h-[52px] w-full items-center justify-center gap-2 rounded-xl border border-[#d48f08] bg-[#d48f08] px-5 text-[13px] font-bold text-white shadow-[0_7px_18px_rgba(212,143,8,.14)] sm:h-[56px] sm:text-[14px]"
                    >
                        <span>Sign In</span>

                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="M5 12h14"></path>
                            <path d="m14 7 5 5-5 5"></path>
                        </svg>
                    </button>
                </form>

                {{-- DIVIDER --}}
                <div class="my-5 flex items-center gap-3 sm:my-6 sm:gap-4 lg:my-7">
                    <span class="h-px flex-1 bg-[#e9e3da]"></span>
                    <span class="whitespace-nowrap text-[9px] font-medium uppercase tracking-[0.10em] text-[#9b9287] min-[380px]:text-[10px] sm:text-[11px] sm:tracking-[0.13em]">
                        or continue with
                    </span>
                    <span class="h-px flex-1 bg-[#e9e3da]"></span>
                </div>

                {{-- SOCIAL --}}
                <div class="grid grid-cols-1 gap-2.5 min-[360px]:grid-cols-2 sm:gap-3">

                    {{-- GOOGLE --}}
                    <a
                        href="{{ route('oauth.redirect', ['provider' => 'google']) }}"
                        class="sari-login-social flex h-[48px] items-center justify-center gap-2.5 rounded-xl border border-[#e4ddd3] bg-white text-[14px] font-semibold text-[#28241e] hover:border-[#d8bd89] hover:bg-[#fffaf1] sm:h-[54px] sm:text-[15px]"
                        aria-label="Continue with Google"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-[20px] w-[20px] shrink-0 sm:h-[22px] sm:w-[22px]"
                            aria-hidden="true"
                        >
                            <path fill="#4285F4" d="M21.6 12.23c0-.71-.06-1.4-.18-2.07H12v3.92h5.38a4.6 4.6 0 0 1-2 3.02v2.51h3.23c1.89-1.74 2.99-4.31 2.99-7.38Z"/>
                            <path fill="#34A853" d="M12 22c2.7 0 4.97-.9 6.62-2.39l-3.23-2.51c-.9.6-2.04.96-3.39.96-2.61 0-4.82-1.76-5.61-4.13H3.06v2.59A10 10 0 0 0 12 22Z"/>
                            <path fill="#FBBC05" d="M6.39 13.93A6.02 6.02 0 0 1 6.08 12c0-.67.12-1.32.31-1.93V7.48H3.06A10 10 0 0 0 2 12c0 1.61.38 3.13 1.06 4.52l3.33-2.59Z"/>
                            <path fill="#EA4335" d="M12 5.94c1.47 0 2.79.51 3.83 1.5l2.87-2.87A9.66 9.66 0 0 0 12 2a10 10 0 0 0-8.94 5.48l3.33 2.59C7.18 7.7 9.39 5.94 12 5.94Z"/>
                        </svg>

                        Google
                    </a>


                    {{-- FACEBOOK --}}
                    <a
                        href="{{ route('oauth.redirect', ['provider' => 'facebook']) }}"
                        class="sari-login-social flex h-[48px] items-center justify-center gap-2.5 rounded-xl border border-[#e4ddd3] bg-white text-[14px] font-semibold text-[#28241e] hover:border-[#d8bd89] hover:bg-[#fffaf1] sm:h-[54px] sm:text-[15px]"
                        aria-label="Continue with Facebook"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-[20px] w-[20px] shrink-0 sm:h-[22px] sm:w-[22px]"
                            aria-hidden="true"
                        >
                            <path
                                fill="#1877F2"
                                d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.414c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.235 2.686.235v2.973h-1.513c-1.49 0-1.956.931-1.956 1.887v2.261h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073Z"
                            />
                        </svg>

                        Facebook
                    </a>

                </div>

                {{-- SIGNUP --}}
                <div class="mt-6 text-center text-[12px] leading-6 text-[#6f6658] sm:mt-7 sm:text-[13px] lg:mt-8">
                    <span>Don't have an account?</span>

                    <a
                        href="{{ route('register') }}"
                        class="ml-1 font-bold text-[#a96f06] transition hover:text-[#7f5104]"
                    >
                        Create one
                    </a>
                </div>

                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <footer class="flex flex-col items-center justify-between gap-3 border-t border-[#e4ddd3] pt-5 text-center text-[13px] leading-6 text-[#756b5b] sm:flex-row sm:pt-6 sm:text-left sm:text-[14px] lg:text-[15px]">
            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 sm:justify-start sm:gap-x-5">
                <a href="#" class="font-medium transition hover:text-[#a96f06]">Privacy Policy</a>
                <a href="#" class="font-medium transition hover:text-[#a96f06]">Terms of Service</a>
            </div>

            <span>
                © {{ date('Y') }} SARI. All rights reserved.
            </span>
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


<style>
/* Exact logo/card alignment */
.sari-login-brand {
    text-align: center;
}

.sari-login-brand img {
    transform: none !important;
    margin-inline: auto;
}

/* Keep the card comfortably sized on wide screens */
@media (min-width: 1536px) {
    .sari-login-card {
        max-width: 560px;
    }
}
</style>

@endsection