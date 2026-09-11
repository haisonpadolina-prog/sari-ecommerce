@extends('layouts.app')

@section('title', 'Registration Pending — SARI')

@section('content')
@php
    $registrationRole = (string) session('registration_role', 'user');
    $reviewerLabel = $registrationRole === 'rider'
        ? (string) session('registration_logistics_name', 'SARI Logistics / Sorting Center')
        : 'administrator';
@endphp
<script src="https://cdn.tailwindcss.com"></script>

<div class="min-h-screen bg-[#f8f1df] px-4 py-10 font-['Poppins',sans-serif]">
    <div class="mx-auto flex min-h-[80vh] max-w-[720px] items-center justify-center">
        <div class="w-full rounded-[28px] border border-[#ead9b7] bg-white p-7 text-center shadow-[0_24px_70px_rgba(83,59,17,.12)] sm:p-10">
            <img src="{{ asset('images/sari-logo.png') }}" alt="SARI" class="mx-auto h-auto w-[170px] brightness-0">

            <div class="mx-auto mt-7 grid h-16 w-16 place-items-center rounded-full border border-[#eadfc9] bg-[#fffaf2] text-[#b97805]">
                <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 7v5l3 2"></path>
                </svg>
            </div>

            <p class="mt-5 text-[10px] font-bold uppercase tracking-[.18em] text-[#c98a08]">Application Submitted</p>
            <h1 class="mt-2 text-[28px] font-bold tracking-[-.04em] text-[#201b15]">Waiting for {{ $reviewerLabel }} review</h1>

            <p class="mx-auto mt-4 max-w-[560px] text-[12px] leading-6 text-[#756b5b]">
                Your SARI {{ ucfirst(session('registration_role', 'user')) }} registration has been saved successfully.
                {{ $registrationRole === 'rider' ? 'Your selected Logistics / Sorting Center must review your Rider information and uploaded documents before the account can log in.' : 'The administrator must review your information and uploaded documents before the account can log in.' }}
            </p>

            @if (session('registration_email'))
                <div class="mx-auto mt-5 max-w-[500px] rounded-[14px] border border-[#e8dfcf] bg-[#fcfaf7] px-4 py-3">
                    <p class="text-[9px] text-[#918677]">Decision email</p>
                    <p class="mt-1 text-[11px] font-semibold text-[#433a30]">{{ session('registration_email') }}</p>
                </div>
            @endif

            <div class="mt-7 flex flex-col gap-2 sm:flex-row sm:justify-center">
                <a href="{{ route('login') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-[#c98a08] px-6 text-[11px] font-bold text-white hover:bg-[#b77b05]">
                    Go to Login
                </a>
                <a href="{{ route('home') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-[#e2d6c2] bg-white px-6 text-[11px] font-semibold text-[#665d50]">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
