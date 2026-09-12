@extends('layouts.app')

@section('title', 'Forgot Password — SARI')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<style>
    :root {
        --sari-gold: #d48f08;
        --sari-gold-dark: #b97804;
        --sari-ink: #111827;
        --sari-muted: #6b7280;
        --sari-border: #e5e7eb;
        --sari-card: rgba(255,255,255,.96);
        --sari-soft: #f8fafc;
    }

    .sari-recovery-card { animation: sariCardIn .44s cubic-bezier(.2,.8,.2,1) both; }
    .sari-step { display: none; }
    .sari-step.is-active { display: block; }
    .sari-step-enter { animation: sariStepIn .34s cubic-bezier(.2,.8,.2,1) both; }
    .sari-step-leave { animation: sariStepOut .2s ease both; }

    @keyframes sariCardIn {
        from { opacity: 0; transform: translateY(14px) scale(.992); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes sariStepIn {
        from { opacity: 0; transform: translateX(18px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes sariStepOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(-12px); }
    }
    @keyframes sariSpin { to { transform: rotate(360deg); } }
    @keyframes sariCheckPop {
        0% { opacity: 0; transform: scale(.72); }
        64% { opacity: 1; transform: scale(1.08); }
        100% { opacity: 1; transform: scale(1); }
    }
    @keyframes sariCheckDraw { to { stroke-dashoffset: 0; } }
    @keyframes sariBurst {
        0% { opacity: 0; transform: translate(-50%,-50%) scale(.5); }
        30% { opacity: 1; }
        100% { opacity: 0; transform: translate(var(--tx),var(--ty)) scale(1); }
    }

    .sari-spinner {
        width: 16px; height: 16px; border-radius: 999px;
        border: 2px solid rgba(255,255,255,.38); border-top-color: #fff;
        animation: sariSpin .7s linear infinite;
    }
    .sari-check-pop { animation: sariCheckPop .48s cubic-bezier(.2,.9,.25,1.2) both; }
    .sari-check-path { stroke-dasharray: 30; stroke-dashoffset: 30; animation: sariCheckDraw .42s .2s ease-out forwards; }
    .sari-particle {
        position:absolute; left:50%; top:50%; width:6px; height:6px; border-radius:999px;
        background: currentColor; opacity:0; animation: sariBurst .72s ease-out forwards;
    }
    .sari-otp-input {
        caret-color: transparent;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }
    .sari-otp-input:focus {
        border-color: var(--sari-gold);
        box-shadow: 0 0 0 4px rgba(212,143,8,.11);
        transform: translateY(-1px);
    }
    .sari-progress-line { transition: background-color .28s ease; }
    .sari-progress-dot { transition: all .28s ease; }
    .sari-input { transition: border-color .18s ease, box-shadow .18s ease; }
    .sari-input:focus { border-color: var(--sari-gold); box-shadow:0 0 0 4px rgba(212,143,8,.10); }
    #sariConfettiCanvas { pointer-events:none; position:fixed; inset:0; z-index:80; width:100%; height:100%; }
    .sari-shell { background: var(--sari-card); border: 1px solid rgba(255,255,255,.65); box-shadow: 0 30px 90px rgba(15,23,42,.22); }
    .sari-header { border-bottom: 1px solid #e5e7eb; background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.92)); }
    .sari-progress-track { background: #e5e7eb !important; }
    .sari-progress-track.is-active { background: #d9b76f !important; }
    .sari-progress-circle { border-color: #d1d5db !important; background: #fff !important; color: #9ca3af !important; }
    .sari-progress-circle.is-active { border-color: #d48f08 !important; background: #fff !important; color: #a56c08 !important; box-shadow: 0 0 0 5px rgba(212,143,8,.08); }
    .sari-label { color: #111827; }
    .sari-subcopy { color: #6b7280; }
    .sari-input-wrap input { background: #fff !important; border-color: #d1d5db !important; color: #111827 !important; }
    .sari-input-wrap input::placeholder { color: #9ca3af !important; }
    .sari-inline-note { border: 1px solid #e5e7eb; background: #fff; color: #4b5563; }
    .sari-soft-panel { border: 1px solid #e5e7eb; background: #fff; }
    .sari-soft-divider { border-color: #e5e7eb !important; }
    .sari-muted-link { color: #6b7280; }
    .sari-muted-link:hover { color: #a56c08; }
    .sari-overlay-note { background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.24); }

    @media (prefers-reduced-motion: reduce) {
        .sari-recovery-card, .sari-step-enter, .sari-step-leave, .sari-check-pop,
        .sari-check-path, .sari-particle, .sari-spinner { animation:none !important; }
        .sari-step, .sari-progress-line, .sari-progress-dot, .sari-otp-input, .sari-input { transition:none !important; }
        .sari-check-path { stroke-dashoffset:0 !important; }
    }
</style>

<canvas id="sariConfettiCanvas" aria-hidden="true"></canvas>

<div class="relative min-h-screen min-h-[100dvh] overflow-hidden bg-[#17130f] font-['Poppins',sans-serif]">
    {{-- Keep the original background image visible; no white wash. --}}
    <div class="absolute inset-0">
        <img src="{{ asset('images/login-bg.jpg') }}" alt="" class="h-full w-full object-cover object-center">
        <div class="absolute inset-0 bg-black/36"></div>
    </div>

    <div class="relative z-10 mx-auto flex min-h-screen min-h-[100dvh] w-full max-w-[1500px] items-center justify-center px-4 py-7 sm:px-6 md:px-8 lg:px-10">
        <div class="w-full max-w-[570px]">
            <a href="{{ route('home') }}" class="mb-5 flex flex-col items-center sm:mb-6" aria-label="Back to SARI home">
                <img src="{{ asset('images/sari-logo.png') }}" alt="SARI" class="h-auto w-[158px] object-contain brightness-0 invert sm:w-[180px]">
                <span class="mt-2 text-[9px] font-semibold uppercase tracking-[.22em] text-white/80 sm:text-[10px]">Elevated Everyday</span>
            </a>

            <div class="sari-shell sari-recovery-card overflow-hidden rounded-[24px] backdrop-blur-xl">
                <div class="sari-header px-5 py-5 sm:px-8 sm:py-6">
                    <div class="mb-5 flex items-center justify-center">
                        <div class="flex w-full max-w-[330px] items-center" aria-label="Password recovery progress">
                            @foreach ([1,2,3] as $index)
                                <div class="sari-progress-dot sari-progress-circle grid h-8 w-8 shrink-0 place-items-center rounded-full border text-[10px] font-bold {{ ($step === 'email' ? 1 : ($step === 'otp' ? 2 : 3)) >= $index ? 'is-active border-[#d48f08] bg-white text-[#a56c08]' : 'border-[#d1d5db] bg-white text-[#9ca3af]' }}" data-progress-dot="{{ $index }}">{{ $index }}</div>
                                @if ($index < 3)
                                    <div class="sari-progress-line sari-progress-track h-px flex-1 {{ ($step === 'email' ? 1 : ($step === 'otp' ? 2 : 3)) > $index ? 'is-active bg-[#d9b76f]' : 'bg-[#e5e7eb]' }}" data-progress-line="{{ $index }}"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="text-center">
                        <span id="sariEyebrow" class="text-[9px] font-bold uppercase tracking-[.16em] text-[#a96f06] sm:text-[10px]">Account Recovery</span>
                        <h1 id="sariTitle" class="mt-2 text-[27px] font-bold leading-[1.15] tracking-[-.035em] text-[#1f1b16] sm:text-[32px]">{{ $step === 'otp' ? 'Check your email' : ($step === 'password' ? 'Create a new password' : 'Forgot your password?') }}</h1>
                        <p id="sariSubtitle" class="mx-auto mt-3 max-w-[430px] text-[12px] leading-6 sari-subcopy sm:text-[13px]">{{ $step === 'otp' ? 'Enter the 6-digit verification code we sent to your email.' : ($step === 'password' ? 'Your email is verified. Choose a secure new password for your account.' : 'Enter the email associated with your SARI account. We’ll send you a 6-digit verification code.') }}</p>
                    </div>
                </div>

                <div class="px-5 py-6 sm:px-8 sm:py-7">
                    <div id="sariAlert" class="mb-5 hidden rounded-xl border px-4 py-3 text-[12px] leading-5 sm:text-[13px]" role="alert"></div>

                    {{-- STEP 1: EMAIL --}}
                    <section id="sariStepEmail" class="sari-step {{ $step === 'email' ? 'is-active' : '' }}" data-step="email">
                        <form id="sariEmailForm" class="space-y-5" novalidate>
                            <div>
                                <label for="recoveryEmail" class="mb-2 block text-[13px] font-semibold sari-label sm:text-[14px]">Email Address</label>
                                <div class="sari-input-wrap relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center text-[#9ca3af]" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
                                    </span>
                                    <input id="recoveryEmail" type="email" value="{{ $email }}" autocomplete="email" required placeholder="Enter your email" class="sari-input h-[54px] w-full rounded-xl border border-[#d1d5db] bg-white pl-12 pr-4 text-[14px] text-[#111827] outline-none placeholder:text-[#9ca3af] sm:h-[56px] sm:text-[15px]">
                                </div>
                            </div>

                            <button id="sariSendButton" type="submit" class="flex h-[54px] w-full items-center justify-center gap-2.5 rounded-xl border border-[#d48f08] bg-[#d48f08] px-5 text-[13px] font-bold text-white shadow-[0_8px_20px_rgba(189,125,5,.18)] transition hover:border-[#bd7d05] hover:bg-[#bd7d05] hover:shadow-[0_10px_28px_rgba(189,125,5,.24)] focus:outline-none focus:ring-4 focus:ring-[#d48f08]/20 sm:h-[56px] sm:text-[14px]">
                                <span data-label>Send verification code</span>
                                <svg data-arrow viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M5 12h14"></path><path d="m14 7 5 5-5 5"></path></svg>
                            </button>
                        </form>
                    </section>

                    {{-- STEP 2: OTP --}}
                    <section id="sariStepOtp" class="sari-step {{ $step === 'otp' ? 'is-active' : '' }}" data-step="otp">
                        <div class="text-center">
                            <p class="text-[11px] leading-5 text-[#847b70] sm:text-[12px]">Code sent to</p>
                            <p id="sariMaskedEmail" class="mt-1 text-[13px] font-bold text-[#3b3329] sm:text-[14px]">{{ $maskedEmail }}</p>
                        </div>

                        <form id="sariOtpForm" class="mt-5" novalidate>
                            <div id="sariOtpInputs" class="mx-auto grid max-w-[390px] grid-cols-6 gap-1.5 min-[380px]:gap-2 sm:gap-2.5">
                                @for ($i = 0; $i < 6; $i++)
                                    <input type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="1" aria-label="Verification code digit {{ $i + 1 }}" class="sari-otp-input h-[52px] min-w-0 rounded-xl border border-[#d1d5db] bg-white text-center text-[21px] font-bold text-[#262019] outline-none sm:h-[58px] sm:text-[23px]" data-otp-input>
                                @endfor
                            </div>

                            <button id="sariVerifyButton" type="submit" class="mt-5 flex h-[54px] w-full items-center justify-center gap-2.5 rounded-xl border border-[#d48f08] bg-[#d48f08] px-5 text-[13px] font-bold text-white shadow-[0_8px_20px_rgba(189,125,5,.18)] transition hover:bg-[#bd7d05] focus:outline-none focus:ring-4 focus:ring-[#d48f08]/20 sm:h-[56px] sm:text-[14px]">
                                <span data-label>Verify code</span>
                                <svg data-arrow viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m8 12 2.5 2.5L16 9"></path></svg>
                            </button>
                        </form>

                        <div class="mt-5 text-center text-[11px] leading-5 sari-subcopy sm:text-[12px]">
                            <span>Didn't receive it?</span>
                            <button id="sariResendButton" type="button" class="ml-1 font-bold text-[#a56c08] transition hover:text-[#7f5104] disabled:cursor-not-allowed disabled:text-[#aaa196]" disabled>Resend code <span id="sariResendCountdown"></span></button>
                        </div>

                        <button id="sariChangeEmail" type="button" class="mx-auto mt-3 block text-[11px] font-semibold sari-muted-link underline decoration-[#d1d5db] underline-offset-4 transition hover:text-[#9b6505] sm:text-[12px]">Change email</button>
                    </section>

                    {{-- INLINE OTP SUCCESS before password step --}}
                    <section id="sariStepVerified" class="sari-step" data-step="verified">
                        <div class="py-4 text-center sm:py-6">
                            <div class="relative mx-auto h-24 w-24">
                                <div class="sari-check-pop absolute inset-2 grid place-items-center rounded-full border border-[#f3dfae] bg-white text-[#bd7d05] shadow-[0_14px_34px_rgba(189,125,5,.14)]">
                                    <svg viewBox="0 0 64 64" class="h-14 w-14" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path class="sari-check-path" d="M18 33 28 43 47 22"></path></svg>
                                </div>
                                <span class="sari-particle text-[#d48f08]" style="--tx:-38px;--ty:-35px;animation-delay:.10s"></span>
                                <span class="sari-particle text-[#e6b84d]" style="--tx:42px;--ty:-27px;animation-delay:.14s"></span>
                                <span class="sari-particle text-[#c88a12]" style="--tx:-44px;--ty:18px;animation-delay:.18s"></span>
                                <span class="sari-particle text-[#e8c679]" style="--tx:44px;--ty:24px;animation-delay:.12s"></span>
                            </div>
                            <h2 class="mt-3 text-[22px] font-bold tracking-[-.03em] text-[#1f1b16]">Email verified</h2>
                            <p class="mx-auto mt-2 max-w-[330px] text-[12px] leading-6 sari-subcopy">Verification complete. You can now create your new password.</p>
                        </div>
                    </section>

                    {{-- STEP 3: NEW PASSWORD --}}
                    <section id="sariStepPassword" class="sari-step {{ $step === 'password' ? 'is-active' : '' }}" data-step="password">
                        <form id="sariPasswordForm" class="space-y-4" novalidate>
                            <div>
                                <label for="newPassword" class="mb-2 block text-[13px] font-semibold sari-label sm:text-[14px]">New Password</label>
                                <div class="sari-input-wrap relative">
                                    <input id="newPassword" type="password" autocomplete="new-password" required minlength="8" placeholder="Enter new password" class="sari-input h-[54px] w-full rounded-xl border border-[#d1d5db] bg-white px-4 pr-12 text-[14px] text-[#111827] outline-none placeholder:text-[#9ca3af] sm:h-[56px] sm:text-[15px]">
                                    <button type="button" class="sariPasswordToggle absolute inset-y-0 right-0 flex w-12 items-center justify-center text-[#9ca3af] hover:text-[#a96f06]" data-target="newPassword" aria-label="Show new password">
                                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path><circle cx="12" cy="12" r="2.5"></circle></svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="confirmPassword" class="mb-2 block text-[13px] font-semibold sari-label sm:text-[14px]">Confirm Password</label>
                                <div class="sari-input-wrap relative">
                                    <input id="confirmPassword" type="password" autocomplete="new-password" required minlength="8" placeholder="Confirm new password" class="sari-input h-[54px] w-full rounded-xl border border-[#d1d5db] bg-white px-4 pr-12 text-[14px] text-[#111827] outline-none placeholder:text-[#9ca3af] sm:h-[56px] sm:text-[15px]">
                                    <button type="button" class="sariPasswordToggle absolute inset-y-0 right-0 flex w-12 items-center justify-center text-[#9ca3af] hover:text-[#a96f06]" data-target="confirmPassword" aria-label="Show confirmation password">
                                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path><circle cx="12" cy="12" r="2.5"></circle></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-start gap-2 rounded-xl border sari-inline-note px-3.5 py-3 text-[11px] leading-5 sm:text-[12px]">
                                <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0 text-[#b67a0a]" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>
                                <span>Use at least 8 characters. Choose a password you don't use on other sites.</span>
                            </div>

                            <button id="sariResetButton" type="submit" class="flex h-[54px] w-full items-center justify-center gap-2.5 rounded-xl border border-[#d48f08] bg-[#d48f08] px-5 text-[13px] font-bold text-white shadow-[0_8px_20px_rgba(189,125,5,.18)] transition hover:bg-[#bd7d05] focus:outline-none focus:ring-4 focus:ring-[#d48f08]/20 sm:h-[56px] sm:text-[14px]">
                                <span data-label>Reset password</span>
                                <svg data-arrow viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M5 12h14"></path><path d="m14 7 5 5-5 5"></path></svg>
                            </button>
                        </form>
                    </section>

                    {{-- FINAL SUCCESS --}}
                    <section id="sariStepSuccess" class="sari-step" data-step="success">
                        <div class="py-2 text-center sm:py-4">
                            <div class="relative mx-auto h-28 w-28">
                                <div class="sari-check-pop absolute inset-2 grid place-items-center rounded-full border border-emerald-200 bg-emerald-50 text-emerald-600 shadow-[0_16px_38px_rgba(22,163,74,.14)]">
                                    <svg viewBox="0 0 64 64" class="h-16 w-16" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path class="sari-check-path" d="M18 33 28 43 47 22"></path></svg>
                                </div>
                            </div>
                            <span class="text-[9px] font-bold uppercase tracking-[.16em] text-emerald-700 sm:text-[10px]">Password Updated</span>
                            <h2 class="mt-2 text-[26px] font-bold tracking-[-.035em] text-[#1f1b16] sm:text-[30px]">You're all set</h2>
                            <p class="mx-auto mt-3 max-w-[390px] text-[12px] leading-6 sari-subcopy sm:text-[13px]">Your password has been changed successfully. You can now sign in to SARI with your new password.</p>
                            <a href="{{ route('login') }}" class="mt-6 inline-flex h-[54px] w-full items-center justify-center gap-2 rounded-xl border border-[#d48f08] bg-[#d48f08] px-5 text-[13px] font-bold text-white shadow-[0_8px_20px_rgba(189,125,5,.18)] transition hover:bg-[#bd7d05] sm:h-[56px] sm:text-[14px]">
                                Back to Sign In
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M5 12h14"></path><path d="m14 7 5 5-5 5"></path></svg>
                            </a>
                        </div>
                    </section>

                    <div id="sariBackArea" class="mt-6 border-t sari-soft-divider pt-5 text-center {{ $step === 'email' ? '' : 'hidden' }}">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-[12px] font-semibold text-[#8d5d08] transition hover:text-[#6f4703] sm:text-[13px]">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M19 12H5"></path><path d="m10 7-5 5 5 5"></path></svg>
                            Back to sign in
                        </a>
                    </div>
                </div>
            </div>

            <p class="mt-4 sari-overlay-note rounded-lg px-3 py-2 text-center text-[10px] font-medium leading-5 text-white shadow-sm backdrop-blur-sm sm:text-[11px]">
                Verification codes expire after 10 minutes and can only be used for this recovery session.
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const routes = {
        send: @json(route('password.otp.send')),
        verify: @json(route('password.otp.verify')),
        reset: @json(route('password.update')),
        restart: @json(route('password.otp.restart')),
    };
    const csrf = @json(csrf_token());
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const copy = {
        email: ['Account Recovery', 'Forgot your password?', 'Enter the email associated with your SARI account. We’ll send you a 6-digit verification code.'],
        otp: ['Verify Email', 'Check your email', 'Enter the 6-digit verification code we sent to your email.'],
        password: ['Secure Your Account', 'Create a new password', 'Your email is verified. Choose a secure new password for your account.'],
        success: ['Recovery Complete', 'Password updated', 'Your new password is ready to use.'],
    };

    const sections = {
        email: document.getElementById('sariStepEmail'),
        otp: document.getElementById('sariStepOtp'),
        verified: document.getElementById('sariStepVerified'),
        password: document.getElementById('sariStepPassword'),
        success: document.getElementById('sariStepSuccess'),
    };

    const alertBox = document.getElementById('sariAlert');
    const title = document.getElementById('sariTitle');
    const subtitle = document.getElementById('sariSubtitle');
    const eyebrow = document.getElementById('sariEyebrow');
    const backArea = document.getElementById('sariBackArea');
    const emailInput = document.getElementById('recoveryEmail');
    const maskedEmail = document.getElementById('sariMaskedEmail');
    const otpInputs = [...document.querySelectorAll('[data-otp-input]')];
    const resendButton = document.getElementById('sariResendButton');
    const resendCountdown = document.getElementById('sariResendCountdown');

    let currentStep = @json($step);
    let resendTimer = null;
    let currentEmail = @json($email);
    let verifyingAutomatically = false;

    function showAlert(message, type = 'error') {
        alertBox.textContent = message;
        alertBox.className = 'mb-5 rounded-xl border px-4 py-3 text-[12px] leading-5 sm:text-[13px]';
        if (type === 'success') {
            alertBox.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
        } else if (type === 'info') {
            alertBox.classList.add('border-[#f3dfae]', 'bg-white', 'text-[#815b17]', 'shadow-sm');
        } else {
            alertBox.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
        }
        alertBox.classList.remove('hidden');
    }

    function hideAlert() { alertBox.classList.add('hidden'); }

    function setHeader(step) {
        const key = step === 'verified' ? 'password' : step;
        if (!copy[key]) return;
        eyebrow.textContent = copy[key][0];
        title.textContent = copy[key][1];
        subtitle.textContent = copy[key][2];
    }

    function updateProgress(step) {
        const number = step === 'email' ? 1 : step === 'otp' || step === 'verified' ? 2 : 3;
        document.querySelectorAll('[data-progress-dot]').forEach((dot) => {
            const n = Number(dot.dataset.progressDot);
            dot.classList.toggle('is-active', n <= number);
            dot.classList.toggle('border-[#d48f08]', n <= number);
            dot.classList.toggle('bg-white', true);
            dot.classList.toggle('text-[#a56c08]', n <= number);
            dot.classList.toggle('border-[#d1d5db]', n > number);
            dot.classList.toggle('text-[#9ca3af]', n > number);
        });
        document.querySelectorAll('[data-progress-line]').forEach((line) => {
            const n = Number(line.dataset.progressLine);
            line.classList.toggle('is-active', n < number);
            line.classList.toggle('bg-[#d9b76f]', n < number);
            line.classList.toggle('bg-[#e5e7eb]', n >= number);
        });
    }

    async function goTo(step, { delay = 0 } = {}) {
        if (delay) await new Promise(resolve => setTimeout(resolve, delay));
        hideAlert();
        const old = sections[currentStep];
        const next = sections[step];
        if (!next || old === next) return;

        if (old && old.classList.contains('is-active') && !reducedMotion) {
            old.classList.add('sari-step-leave');
            await new Promise(resolve => setTimeout(resolve, 190));
            old.classList.remove('is-active', 'sari-step-leave');
        } else if (old) {
            old.classList.remove('is-active');
        }

        next.classList.add('is-active');
        if (!reducedMotion) {
            next.classList.add('sari-step-enter');
            setTimeout(() => next.classList.remove('sari-step-enter'), 360);
        }
        currentStep = step;
        setHeader(step);
        updateProgress(step);
        backArea.classList.toggle('hidden', step !== 'email');
    }

    function setLoading(button, loading, label) {
        if (!button) return;
        button.disabled = loading;
        const labelEl = button.querySelector('[data-label]');
        const arrow = button.querySelector('[data-arrow]');
        if (loading) {
            if (labelEl) labelEl.textContent = label;
            if (arrow) arrow.classList.add('hidden');
            if (!button.querySelector('.sari-spinner')) {
                const spinner = document.createElement('span');
                spinner.className = 'sari-spinner';
                button.appendChild(spinner);
            }
        } else {
            button.querySelector('.sari-spinner')?.remove();
            if (arrow) arrow.classList.remove('hidden');
        }
    }

    async function post(url, body = {}) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(body),
        });
        let data = {};
        try { data = await response.json(); } catch (_) {}
        if (!response.ok) {
            const validation = data?.errors ? Object.values(data.errors).flat()[0] : null;
            const error = new Error(validation || data.message || 'Something went wrong. Please try again.');
            error.data = data;
            error.status = response.status;
            throw error;
        }
        return data;
    }

    function startResendCountdown(seconds) {
        clearInterval(resendTimer);
        let remaining = Math.max(0, Number(seconds || 0));
        const render = () => {
            if (remaining > 0) {
                resendButton.disabled = true;
                resendCountdown.textContent = `in ${remaining}s`;
                remaining--;
            } else {
                resendButton.disabled = false;
                resendCountdown.textContent = '';
                clearInterval(resendTimer);
            }
        };
        render();
        resendTimer = setInterval(render, 1000);
    }

    function otpValue() { return otpInputs.map(input => input.value).join(''); }
    function clearOtp() { otpInputs.forEach(input => input.value = ''); otpInputs[0]?.focus(); }

    document.getElementById('sariEmailForm')?.addEventListener('submit', async (event) => {
        event.preventDefault();
        hideAlert();
        const button = document.getElementById('sariSendButton');
        const email = emailInput.value.trim().toLowerCase();
        if (!email || !emailInput.checkValidity()) {
            showAlert('Enter a valid email address to continue.');
            emailInput.focus();
            return;
        }
        setLoading(button, true, 'Sending code...');
        try {
            const data = await post(routes.send, { email });
            currentEmail = email;
            maskedEmail.textContent = data.masked_email || email;
            clearOtp();
            startResendCountdown(data.resend_after || 45);
            await goTo('otp');
            showAlert('Verification code sent. Check your inbox and spam folder.', 'success');
        } catch (error) {
            showAlert(error.message);
            if (error.data?.retry_after) startResendCountdown(error.data.retry_after);
        } finally {
            setLoading(button, false);
            button.querySelector('[data-label]').textContent = 'Send verification code';
        }
    });

    otpInputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '').slice(-1);
            if (input.value && index < otpInputs.length - 1) otpInputs[index + 1].focus();
            if (otpValue().length === 6 && !verifyingAutomatically) {
                verifyingAutomatically = true;
                setTimeout(() => {
                    document.getElementById('sariOtpForm')?.requestSubmit();
                    verifyingAutomatically = false;
                }, 130);
            }
        });
        input.addEventListener('keydown', (event) => {
            if (event.key === 'Backspace' && !input.value && index > 0) otpInputs[index - 1].focus();
            if (event.key === 'ArrowLeft' && index > 0) otpInputs[index - 1].focus();
            if (event.key === 'ArrowRight' && index < otpInputs.length - 1) otpInputs[index + 1].focus();
        });
        input.addEventListener('paste', (event) => {
            const digits = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            if (!digits) return;
            event.preventDefault();
            digits.split('').forEach((digit, i) => { if (otpInputs[i]) otpInputs[i].value = digit; });
            otpInputs[Math.min(digits.length, 6) - 1]?.focus();
            if (digits.length === 6) setTimeout(() => document.getElementById('sariOtpForm')?.requestSubmit(), 120);
        });
    });

    document.getElementById('sariOtpForm')?.addEventListener('submit', async (event) => {
        event.preventDefault();
        hideAlert();
        const code = otpValue();
        const button = document.getElementById('sariVerifyButton');
        if (code.length !== 6) {
            showAlert('Enter the complete 6-digit verification code.');
            return;
        }
        setLoading(button, true, 'Verifying...');
        try {
            await post(routes.verify, { code });
            await goTo('verified');
            if (reducedMotion) {
                await goTo('password', { delay: 350 });
            } else {
                await goTo('password', { delay: 1050 });
            }
            document.getElementById('newPassword')?.focus();
        } catch (error) {
            showAlert(error.message);
            if (error.data?.restart) {
                setTimeout(() => restartFlow(false), 900);
            } else {
                clearOtp();
            }
        } finally {
            setLoading(button, false);
            button.querySelector('[data-label]').textContent = 'Verify code';
        }
    });

    resendButton?.addEventListener('click', async () => {
        if (resendButton.disabled || !currentEmail) return;
        hideAlert();
        resendButton.disabled = true;
        resendButton.textContent = 'Sending...';
        try {
            const data = await post(routes.send, { email: currentEmail });
            clearOtp();
            startResendCountdown(data.resend_after || 45);
            showAlert('A new verification code has been sent.', 'success');
        } catch (error) {
            showAlert(error.message);
            startResendCountdown(error.data?.retry_after || 3);
        } finally {
            if (!resendButton.querySelector('#sariResendCountdown')) {
                resendButton.innerHTML = 'Resend code <span id="sariResendCountdown"></span>';
            }
        }
    });

    async function restartFlow(focus = true) {
        try { await post(routes.restart); } catch (_) {}
        currentEmail = '';
        emailInput.value = '';
        clearInterval(resendTimer);
        clearOtp();
        await goTo('email');
        if (focus) emailInput.focus();
    }

    document.getElementById('sariChangeEmail')?.addEventListener('click', () => restartFlow(true));

    document.querySelectorAll('.sariPasswordToggle').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.target);
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    });

    document.getElementById('sariPasswordForm')?.addEventListener('submit', async (event) => {
        event.preventDefault();
        hideAlert();
        const password = document.getElementById('newPassword').value;
        const confirmation = document.getElementById('confirmPassword').value;
        const button = document.getElementById('sariResetButton');
        if (password.length < 8) {
            showAlert('Your new password must contain at least 8 characters.');
            return;
        }
        if (password !== confirmation) {
            showAlert('The password confirmation does not match.');
            return;
        }
        setLoading(button, true, 'Updating password...');
        try {
            await post(routes.reset, { password, password_confirmation: confirmation });
            await goTo('success');
            launchConfetti();
        } catch (error) {
            showAlert(error.message);
            if (error.data?.restart) setTimeout(() => restartFlow(false), 1000);
        } finally {
            setLoading(button, false);
            button.querySelector('[data-label]').textContent = 'Reset password';
        }
    });

    function launchConfetti() {
        if (reducedMotion) return;
        const canvas = document.getElementById('sariConfettiCanvas');
        const ctx = canvas.getContext('2d');
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        const resize = () => {
            canvas.width = Math.floor(innerWidth * dpr);
            canvas.height = Math.floor(innerHeight * dpr);
            canvas.style.width = `${innerWidth}px`;
            canvas.style.height = `${innerHeight}px`;
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        };
        resize();

        const colors = ['#d48f08', '#e9b949', '#f6d887', '#2f9e67', '#ffffff'];
        const pieces = [];
        const amount = Math.min(150, Math.max(90, Math.floor(innerWidth / 8)));
        for (let i = 0; i < amount; i++) {
            const fromLeft = i % 2 === 0;
            const startX = fromLeft ? innerWidth * .16 : innerWidth * .84;
            const startY = innerHeight * (.25 + Math.random() * .42);
            pieces.push({
                x: startX + (Math.random() - .5) * 35,
                y: startY,
                vx: (fromLeft ? 1 : -1) * (3.2 + Math.random() * 5.2),
                vy: -4.8 - Math.random() * 5.4,
                g: .16 + Math.random() * .09,
                drag: .988,
                w: 4 + Math.random() * 6,
                h: 2 + Math.random() * 4,
                r: Math.random() * Math.PI,
                vr: (Math.random() - .5) * .28,
                color: colors[Math.floor(Math.random() * colors.length)],
                life: 0,
                ttl: 95 + Math.random() * 55,
            });
        }

        let frame = 0;
        const draw = () => {
            ctx.clearRect(0, 0, innerWidth, innerHeight);
            pieces.forEach(p => {
                p.life++; p.vx *= p.drag; p.vy += p.g; p.x += p.vx; p.y += p.vy; p.r += p.vr;
                const alpha = Math.max(0, Math.min(1, (p.ttl - p.life) / 28));
                if (alpha <= 0) return;
                ctx.save(); ctx.globalAlpha = alpha; ctx.translate(p.x, p.y); ctx.rotate(p.r); ctx.fillStyle = p.color;
                ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h); ctx.restore();
            });
            frame++;
            if (frame < 160) requestAnimationFrame(draw); else ctx.clearRect(0, 0, innerWidth, innerHeight);
        };
        requestAnimationFrame(draw);
    }

    if (currentStep === 'otp') startResendCountdown(@json($resendAfter));
    updateProgress(currentStep);
    setHeader(currentStep);
});
</script>
@endsection
