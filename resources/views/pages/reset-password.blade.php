@extends('layouts.app')

@section('title', 'Reset Password — SARI')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<div class="relative min-h-screen min-h-[100dvh] overflow-hidden bg-[#f7f4ee] font-['Poppins',sans-serif]">
    <div class="absolute inset-0">
        <img src="{{ asset('images/login-bg.jpg') }}" alt="" class="h-full w-full object-cover object-center">
        <div class="absolute inset-0 bg-white/75"></div>
    </div>

    <div class="relative z-10 mx-auto flex min-h-screen min-h-[100dvh] w-full max-w-[1500px] items-center justify-center px-4 py-8 sm:px-6">
        <div class="w-full max-w-[520px]">
            <a href="{{ route('home') }}" class="mb-6 flex justify-center" aria-label="Back to SARI home">
                <img src="{{ asset('images/sari-logo.png') }}" alt="SARI" class="h-auto w-[180px] object-contain brightness-0">
            </a>

            <div class="rounded-[20px] border border-[#e7e0d7] bg-white p-6 shadow-[0_18px_50px_rgba(39,31,21,.09)] sm:p-9">
                <div class="mb-7 text-center">
                    <span class="text-[10px] font-bold uppercase tracking-[.15em] text-[#a96f06]">Secure recovery</span>
                    <h1 class="mt-2 text-[28px] font-bold tracking-[-.035em] text-[#1f1b16] sm:text-[32px]">Create a new password</h1>
                    <p class="mx-auto mt-3 max-w-[390px] text-[13px] leading-6 text-[#81786c]">
                        Use at least 8 characters and confirm the password before saving.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[13px] leading-5 text-red-700" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label for="email" class="mb-2 block text-[13px] font-semibold text-[#312b22]">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email" class="h-[54px] w-full rounded-xl border border-[#e4ddd3] bg-white px-4 text-[14px] outline-none transition focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10">
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-[13px] font-semibold text-[#312b22]">New Password</label>
                        <input type="password" id="password" name="password" required autocomplete="new-password" minlength="8" placeholder="Minimum 8 characters" class="h-[54px] w-full rounded-xl border border-[#e4ddd3] bg-white px-4 text-[14px] outline-none transition placeholder:text-[#aaa196] focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10">
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-[13px] font-semibold text-[#312b22]">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" minlength="8" placeholder="Re-enter your new password" class="h-[54px] w-full rounded-xl border border-[#e4ddd3] bg-white px-4 text-[14px] outline-none transition placeholder:text-[#aaa196] focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10">
                    </div>

                    <button type="submit" class="flex h-[54px] w-full items-center justify-center rounded-xl border border-[#d48f08] bg-[#d48f08] px-5 text-[14px] font-bold text-white shadow-[0_7px_18px_rgba(212,143,8,.14)] transition hover:bg-[#bd7d05]">
                        Update password
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-[13px] font-semibold text-[#a56c08] transition hover:text-[#7f5104]">Back to sign in</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
