@extends('layouts.app')

@section('title', 'Register — SARI')

@section('content')

<script src="https://cdn.tailwindcss.com"></script>

<style>
    @keyframes sariRegisterFadeUp {
        from {
            opacity: 0;
            transform: translateY(18px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes sariRegisterLogoIn {
        from {
            opacity: 0;
            transform: translateY(-8px) scale(.98);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes sariRegisterFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    .sari-register-card {
        animation: sariRegisterFadeUp .75s cubic-bezier(.22, 1, .36, 1) both;
    }

    .sari-register-brand {
        animation: sariRegisterLogoIn .7s cubic-bezier(.22, 1, .36, 1) both;
    }

    .sari-register-decor {
        animation: sariRegisterFloat 7s ease-in-out infinite;
    }

    .sari-register-input {
        transition:
            border-color .25s ease,
            box-shadow .25s ease,
            background-color .25s ease,
            transform .25s ease;
    }

    .sari-register-input:focus {
        transform: translateY(-1px);
    }

    .sari-register-button {
        position: relative;
        overflow: hidden;
        isolation: isolate;
        transition:
            transform .3s cubic-bezier(.22, 1, .36, 1),
            box-shadow .3s ease,
            background-color .3s ease;
    }

    .sari-register-button::after {
        content: "";
        position: absolute;
        top: -60%;
        left: -80%;
        width: 42%;
        height: 220%;
        background: rgba(255, 255, 255, .18);
        transform: skewX(-18deg);
        transition: left .75s cubic-bezier(.22, 1, .36, 1);
        pointer-events: none;
    }

    .sari-register-button:hover {
        transform: translateY(-2px);
    }

    .sari-register-button:hover::after {
        left: 135%;
    }

    .sari-register-social {
        transition:
            transform .25s cubic-bezier(.22, 1, .36, 1),
            border-color .25s ease,
            background-color .25s ease,
            box-shadow .25s ease;
    }

    .sari-register-social:hover {
        transform: translateY(-2px);
    }

    .sari-register-brand {
        text-align: center;
    }

    .sari-register-brand img {
        transform: none !important;
        margin-inline: auto;
    }

    @media (min-width: 1536px) {
        .sari-register-card {
            max-width: 620px;
        }
    }


    /* Compact layout for short laptop screens and landscape devices */
    @media (max-height: 860px) and (min-width: 640px) {
        .sari-register-shell {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        .sari-register-main {
            align-items: flex-start !important;
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        .sari-register-brand {
            margin-bottom: 1rem !important;
        }

        .sari-register-card {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }

        .sari-register-header {
            margin-bottom: 1.25rem !important;
        }

        .sari-register-form {
            gap: .9rem !important;
        }

        .sari-register-divider {
            margin-top: 1.15rem !important;
            margin-bottom: 1.15rem !important;
        }

        .sari-register-login {
            margin-top: 1.15rem !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-register-card,
        .sari-register-brand,
        .sari-register-decor,
        .sari-register-input,
        .sari-register-button,
        .sari-register-social {
            animation: none !important;
            transition: none !important;
            transform: none !important;
        }
    }
</style>

<div class="relative min-h-screen min-h-[100dvh] overflow-x-hidden bg-[#f8f1df] font-['Poppins',sans-serif]">

    {{-- BACKGROUND IMAGE --}}
    <div class="absolute inset-0">
        <img
            src="{{ asset('images/login-bg.jpg') }}"
            alt=""
            class="h-full w-full object-cover object-center"
        >

        <div class="absolute inset-0 bg-[#fff9eb]/60 sm:bg-[#fff9eb]/55"></div>
        <div class="absolute inset-0 bg-[#fffaf0]/55 sm:bg-[#fffaf0]/45 lg:inset-y-0 lg:left-0 lg:right-auto lg:w-[60%] lg:bg-[#fffaf0]/72"></div>
    </div>

    {{-- DECORATIVE DETAILS --}}
    <div
        class="sari-register-decor pointer-events-none absolute -left-24 top-24 hidden h-64 w-64 rounded-full border border-[#c98a08]/15 lg:block"
        aria-hidden="true"
    >
        <div class="absolute inset-7 rounded-full border border-[#c98a08]/10"></div>
        <div class="absolute inset-14 rounded-full border border-[#c98a08]/10"></div>
    </div>

    <div
        class="pointer-events-none absolute bottom-8 left-10 hidden items-center gap-3.5 text-[13px] font-semibold uppercase tracking-[0.22em] text-[#8d7a55] xl:flex"
        aria-hidden="true"
    >
        <span class="h-px w-12 bg-[#c98a08]"></span>
        Elevated Everyday
    </div>

    {{-- PAGE --}}
    <div class="sari-register-shell relative z-10 mx-auto flex min-h-screen min-h-[100dvh] w-full max-w-[1500px] flex-col px-4 py-5 sm:px-6 sm:py-6 md:px-8 lg:px-10 lg:py-7 xl:px-12">

        <div class="sari-register-main flex flex-1 items-start justify-center py-4 sm:items-center sm:py-6 lg:py-8">

            <div class="flex w-full max-w-[640px] flex-col items-center">

                {{-- BRAND --}}
                <a
                    href="{{ route('home') }}"
                    class="sari-register-brand mb-5 inline-flex flex-col items-center sm:mb-6 lg:mb-7"
                    aria-label="Back to SARI home"
                >
                    <img
                        src="{{ asset('images/sari-logo.png') }}"
                        alt="SARI"
                        class="h-auto w-[145px] object-contain brightness-0 min-[380px]:w-[155px] sm:w-[175px] lg:w-[195px] xl:w-[205px]"
                    >

                    <span class="mt-1.5 text-center text-[9px] font-semibold uppercase tracking-[0.24em] text-[#9a7939] sm:text-[10px] sm:tracking-[0.27em] lg:text-[11px]">
                        Shop Everywhere
                    </span>
                </a>

                {{-- CARD --}}
                <div
                    class="sari-register-card w-full max-w-[610px] rounded-[20px] border border-[#ead9b7] bg-[#fffdf8]/95 p-5 shadow-[0_24px_70px_rgba(83,59,17,0.13)] backdrop-blur-[3px] min-[380px]:p-6 sm:rounded-[24px] sm:p-8 lg:rounded-[28px] lg:p-10 xl:p-11"
                >

                    {{-- HEADER --}}
                    <div class="sari-register-header mb-6 text-center sm:mb-7 lg:mb-8">
                        <span class="mb-2 block text-[10px] font-bold uppercase tracking-[0.20em] text-[#c98a08] sm:mb-3 sm:text-[11px] sm:tracking-[0.22em] lg:text-[12px] lg:tracking-[0.24em]">
                            Join SARI
                        </span>

                        <h1 class="text-[27px] font-bold leading-[1.12] tracking-[-0.035em] text-[#17140e] min-[380px]:text-[30px] sm:text-[34px] lg:text-[38px] xl:text-[40px]">
                            Create your account
                        </h1>

                        <p class="mx-auto mt-3 max-w-[430px] text-[13px] leading-6 text-[#756b5b] min-[380px]:text-[14px] sm:mt-4 sm:text-[15px] sm:leading-7 lg:text-[16px]">
                            Create an account and start discovering products made for your everyday life.
                        </p>
                    </div>

                    {{-- FORM --}}
                    <form
                        method="POST"
                        action="#"
                        class="sari-register-form flex flex-col gap-4 sm:gap-5"
                    >
                        @csrf

                        {{-- FULL NAME --}}
                        <div>
                            <label
                                for="name"
                                class="mb-2 block text-[13px] font-semibold text-[#312b22] sm:mb-2.5 sm:text-[14px]"
                            >
                                Full Name
                            </label>

                            <div class="relative">
                                <span
                                    class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-[#ad9d82] sm:w-12"
                                    aria-hidden="true"
                                >
                                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <circle cx="12" cy="8" r="4"></circle>
                                        <path d="M4 21a8 8 0 0 1 16 0"></path>
                                    </svg>
                                </span>

                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Enter your full name"
                                    autocomplete="name"
                                    required
                                    class="sari-register-input h-[52px] w-full rounded-xl border border-[#e7dcc6] bg-white pl-11 pr-4 text-[14px] text-[#17140e] outline-none placeholder:text-[#a99f91] focus:border-[#c98a08] focus:ring-4 focus:ring-[#c98a08]/10 sm:h-[56px] sm:pl-12 sm:text-[15px]"
                                >
                            </div>

                            @error('name')
                                <p class="mt-2 text-[12px] text-red-600 sm:text-[13px]">{{ $message }}</p>
                            @enderror
                        </div>

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
                                    class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-[#ad9d82] sm:w-12"
                                    aria-hidden="true"
                                >
                                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
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
                                    required
                                    class="sari-register-input h-[52px] w-full rounded-xl border border-[#e7dcc6] bg-white pl-11 pr-4 text-[14px] text-[#17140e] outline-none placeholder:text-[#a99f91] focus:border-[#c98a08] focus:ring-4 focus:ring-[#c98a08]/10 sm:h-[56px] sm:pl-12 sm:text-[15px]"
                                >
                            </div>

                            @error('email')
                                <p class="mt-2 text-[12px] text-red-600 sm:text-[13px]">{{ $message }}</p>
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
                                    class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-[#ad9d82] sm:w-12"
                                    aria-hidden="true"
                                >
                                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                                        <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                                    </svg>
                                </span>

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="Create a password"
                                    autocomplete="new-password"
                                    required
                                    class="sari-register-input h-[52px] w-full rounded-xl border border-[#e7dcc6] bg-white pl-11 pr-11 text-[14px] text-[#17140e] outline-none placeholder:text-[#a99f91] focus:border-[#c98a08] focus:ring-4 focus:ring-[#c98a08]/10 sm:h-[56px] sm:pl-12 sm:pr-12 sm:text-[15px]"
                                >

                                <button
                                    type="button"
                                    class="sari-password-toggle absolute inset-y-0 right-0 flex w-11 items-center justify-center text-[#a59b8b] transition hover:text-[#c98a08] sm:w-12"
                                    data-target="password"
                                    aria-label="Show password"
                                >
                                    <svg data-eye-open viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>

                                    <svg data-eye-closed viewBox="0 0 24 24" class="hidden h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="m3 3 18 18"></path>
                                        <path d="M10.6 10.7a2 2 0 0 0 2.7 2.7"></path>
                                        <path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6 0 9.5 7 9.5 7a16 16 0 0 1-2.1 2.8"></path>
                                        <path d="M6.2 6.2C3.8 8 2.5 12 2.5 12S6 19 12 19a9.5 9.5 0 0 0 3.2-.5"></path>
                                    </svg>
                                </button>
                            </div>

                            @error('password')
                                <p class="mt-2 text-[12px] text-red-600 sm:text-[13px]">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- CONFIRM PASSWORD --}}
                        <div>
                            <label
                                for="password_confirmation"
                                class="mb-2 block text-[13px] font-semibold text-[#312b22] sm:mb-2.5 sm:text-[14px]"
                            >
                                Confirm Password
                            </label>

                            <div class="relative">
                                <span
                                    class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-[#ad9d82] sm:w-12"
                                    aria-hidden="true"
                                >
                                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                                        <path d="M8 10V7a4 4 4 0 0 1 8 0v3"></path>
                                        <path d="m9.5 15 1.5 1.5 3.5-3.5"></path>
                                    </svg>
                                </span>

                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    placeholder="Confirm your password"
                                    autocomplete="new-password"
                                    required
                                    class="sari-register-input h-[52px] w-full rounded-xl border border-[#e7dcc6] bg-white pl-11 pr-11 text-[14px] text-[#17140e] outline-none placeholder:text-[#a99f91] focus:border-[#c98a08] focus:ring-4 focus:ring-[#c98a08]/10 sm:h-[56px] sm:pl-12 sm:pr-12 sm:text-[15px]"
                                >

                                <button
                                    type="button"
                                    class="sari-password-toggle absolute inset-y-0 right-0 flex w-11 items-center justify-center text-[#a59b8b] transition hover:text-[#c98a08] sm:w-12"
                                    data-target="password_confirmation"
                                    aria-label="Show password"
                                >
                                    <svg data-eye-open viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>

                                    <svg data-eye-closed viewBox="0 0 24 24" class="hidden h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="m3 3 18 18"></path>
                                        <path d="M10.6 10.7a2 2 0 0 0 2.7 2.7"></path>
                                        <path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6 0 9.5 7 9.5 7a16 16 0 0 1-2.1 2.8"></path>
                                        <path d="M6.2 6.2C3.8 8 2.5 12 2.5 12S6 19 12 19a9.5 9.5 0 0 0 3.2-.5"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- TERMS --}}
                        <label class="flex cursor-pointer items-start gap-2.5 text-[12px] leading-5 text-[#6f6658] sm:gap-3 sm:text-[13px]">
                            <input
                                type="checkbox"
                                name="terms"
                                required
                                class="mt-0.5 h-4 w-4 shrink-0 rounded border-[#d7c8aa] accent-[#c98a08]"
                            >

                            <span>
                                I agree to the
                                <a href="#" class="font-semibold text-[#a96f06] hover:text-[#805304]">Terms of Service</a>
                                and
                                <a href="#" class="font-semibold text-[#a96f06] hover:text-[#805304]">Privacy Policy</a>.
                            </span>
                        </label>

                        {{-- REGISTER --}}
                        <button
                            type="submit"
                            class="sari-register-button flex h-[54px] w-full items-center justify-between rounded-xl bg-[#c98a08] px-5 text-[16px] font-bold text-white shadow-[0_14px_32px_rgba(201,138,8,0.24)] hover:bg-[#b77b05] sm:h-[60px] sm:px-6 sm:text-[17px]"
                        >
                            <span class="flex-1 text-center">
                                Create Account
                            </span>

                            <span class="text-lg leading-none">→</span>
                        </button>
                    </form>

                    {{-- DIVIDER --}}
                    <div class="sari-register-divider my-5 flex items-center gap-3 sm:my-6 sm:gap-4 lg:my-7">
                        <span class="h-px flex-1 bg-[#eadfc9]"></span>
                        <span class="whitespace-nowrap text-[9px] font-medium uppercase tracking-[0.10em] text-[#999080] min-[380px]:text-[10px] sm:text-[11px] sm:tracking-[0.13em]">
                            or sign up with
                        </span>
                        <span class="h-px flex-1 bg-[#eadfc9]"></span>
                    </div>

                    {{-- SOCIAL --}}
                    <div class="grid grid-cols-1 gap-2.5 min-[360px]:grid-cols-2 sm:gap-3">
                        <button
                            type="button"
                            class="sari-register-social flex h-[48px] items-center justify-center gap-2.5 rounded-xl border border-[#e7dcc6] bg-white text-[14px] font-semibold text-[#28241e] hover:border-[#d6bd89] hover:bg-[#fffaf0] hover:shadow-sm sm:h-[54px] sm:text-[15px]"
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
                        </button>

                        <button
                            type="button"
                            class="sari-register-social flex h-[48px] items-center justify-center gap-2.5 rounded-xl border border-[#e7dcc6] bg-white text-[14px] font-semibold text-[#28241e] hover:border-[#d6bd89] hover:bg-[#fffaf0] hover:shadow-sm sm:h-[54px] sm:text-[15px]"
                        >
                            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] fill-current sm:h-[21px] sm:w-[21px]" aria-hidden="true">
                                <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.79 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.53 4.09ZM12.03 7.25C11.88 5.02 13.69 3.18 15.77 3c.29 2.58-2.34 4.5-3.74 4.25Z"/>
                            </svg>
                            Apple
                        </button>
                    </div>

                    {{-- LOGIN LINK --}}
                    <div class="sari-register-login mt-6 text-center text-[12px] leading-6 text-[#6f6658] sm:mt-7 sm:text-[13px] lg:mt-8">
                        <span>Already have an account?</span>

                        <a
                            href="{{ route('login') }}"
                            class="ml-1 font-bold text-[#b97805] transition hover:text-[#8b5904]"
                        >
                            Login
                        </a>
                    </div>

                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <footer class="flex flex-col items-center justify-between gap-3 border-t border-[#d9cba9]/55 pt-5 text-center text-[13px] leading-6 text-[#756b5b] sm:flex-row sm:pt-6 sm:text-left sm:text-[14px] lg:text-[15px]">
            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 sm:justify-start sm:gap-x-5">
                <a href="#" class="font-medium transition hover:text-[#b97805]">Privacy Policy</a>
                <a href="#" class="font-medium transition hover:text-[#b97805]">Terms of Service</a>
            </div>

            <span>
                © {{ date('Y') }} SARI. All rights reserved.
            </span>
        </footer>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggles = document.querySelectorAll('.sari-password-toggle');

    toggles.forEach(function (toggle) {
        const targetId = toggle.dataset.target;
        const input = document.getElementById(targetId);
        const eyeOpen = toggle.querySelector('[data-eye-open]');
        const eyeClosed = toggle.querySelector('[data-eye-closed]');

        if (!input) return;

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
});
</script>

@endsection