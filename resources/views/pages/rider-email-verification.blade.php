@extends('layouts.app')

@section('title', 'Verify Rider Email — SARI')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

@php
    $providerName = $logistics->displayName();
    $email = old('email', session('rider_registration_otp_email', ''));
@endphp

<div class="min-h-screen bg-[#f4f5f7] font-['Poppins',sans-serif] text-[#202124]">
    <main class="mx-auto flex min-h-screen w-full max-w-[760px] items-center px-4 py-10 sm:px-6">
        <section class="w-full rounded-[24px] border border-[#e7e9ee] bg-white p-6 shadow-[0_18px_45px_rgba(32,33,36,.08)] sm:p-8">
            <a
                href="{{ route('rider.logistics.index') }}"
                class="text-[11px] font-semibold text-[#7b8089] hover:text-[#b67a08]"
            >
                ← Choose another Logistics
            </a>

            <div class="mt-6">
                <p class="text-[9px] font-bold uppercase tracking-[.16em] text-[#b67a08]">
                    Rider Email Verification
                </p>
                <h1 class="mt-2 text-[28px] font-bold tracking-[-.035em] text-[#202124]">
                    Verify your email first
                </h1>
                <p class="mt-2 text-[11px] leading-6 text-[#6b7280]">
                    Before applying to <strong>{{ $providerName }}</strong>, verify the email
                    address you will use for your Rider account.
                </p>
            </div>

            @if(session('success'))
                <div class="mt-5 rounded-[14px] border border-[#cfe5d7] bg-[#f4faf6] px-4 py-3 text-[10px] font-semibold text-[#477b59]">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mt-5 rounded-[14px] border border-[#edd4d4] bg-[#fff7f7] px-4 py-3 text-[10px] font-semibold text-[#a65353]">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('rider_debug_otp'))
                <div class="mt-5 rounded-[14px] border border-[#ecd8aa] bg-[#fff9ec] px-4 py-3">
                    <p class="text-[9px] font-bold uppercase tracking-[.12em] text-[#a86f05]">
                        Local development code
                    </p>
                    <p class="mt-1 font-mono text-[24px] font-bold tracking-[.25em] text-[#8c5d06]">
                        {{ session('rider_debug_otp') }}
                    </p>
                    <p class="mt-1 text-[9px] text-[#8a8176]">
                        This appears only while APP_ENV=local and MAIL_MAILER is log/array.
                    </p>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('rider.logistics.email.send', $logistics) }}"
                class="mt-6"
            >
                @csrf

                <label class="mb-2 block text-[10px] font-semibold text-[#4b5563]">
                    Rider Email
                </label>

                <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_150px]">
                    <input
                        type="email"
                        name="email"
                        value="{{ $email }}"
                        required
                        autocomplete="email"
                        placeholder="name@email.com"
                        class="h-12 rounded-xl border border-[#dfe3e8] bg-white px-4 text-[11px] outline-none transition focus:border-[#d89b10] focus:ring-4 focus:ring-[#fff7e6]"
                    >

                    <button
                        type="submit"
                        class="h-12 rounded-xl bg-[#d89b10] px-5 text-[10px] font-bold text-white transition hover:bg-[#b67a08]"
                    >
                        Send Code
                    </button>
                </div>
            </form>

            @if(session('rider_registration_otp_email') || $email)
                <div class="my-6 flex items-center gap-3">
                    <div class="h-px flex-1 bg-[#eceef1]"></div>
                    <span class="text-[9px] font-semibold uppercase tracking-[.12em] text-[#9aa0a6]">
                        Enter Code
                    </span>
                    <div class="h-px flex-1 bg-[#eceef1]"></div>
                </div>

                <form
                    method="POST"
                    action="{{ route('rider.logistics.email.verify', $logistics) }}"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="email"
                        value="{{ session('rider_registration_otp_email', $email) }}"
                    >

                    <label class="mb-2 block text-[10px] font-semibold text-[#4b5563]">
                        6-digit verification code
                    </label>

                    <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_150px]">
                        <input
                            type="text"
                            name="otp"
                            required
                            inputmode="numeric"
                            pattern="[0-9]{6}"
                            maxlength="6"
                            autocomplete="one-time-code"
                            placeholder="000000"
                            class="h-12 rounded-xl border border-[#dfe3e8] bg-white px-4 font-mono text-[15px] tracking-[.25em] outline-none transition focus:border-[#d89b10] focus:ring-4 focus:ring-[#fff7e6]"
                        >

                        <button
                            type="submit"
                            class="h-12 rounded-xl bg-[#202124] px-5 text-[10px] font-bold text-white transition hover:bg-[#111214]"
                        >
                            Verify
                        </button>
                    </div>
                </form>
            @endif

            <div class="mt-7 rounded-[14px] bg-[#fafafb] p-4">
                <p class="text-[9px] leading-5 text-[#7b8089]">
                    The code expires after 10 minutes. After verification, SARI will open the
                    normal Rider application form for your selected Logistics provider.
                </p>
            </div>
        </section>
    </main>
</div>
@endsection
