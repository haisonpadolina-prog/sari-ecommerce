@extends('layouts.buyer')

@section('title', 'Buyer Account — SARI')
@section('page-title', 'Account')

@section('content')
@include('components.buyer.header')

<div class="mx-auto w-full max-w-[1100px] p-4 sm:p-6 lg:p-8">
    @if (session('success'))
        <div class="mb-4 rounded-2xl border border-[#cfe4d7] bg-[#f1f8f4] px-4 py-3 text-[11px] text-[#4F7D63]">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-[#efcece] bg-[#fff5f5] px-4 py-3 text-[11px] text-[#a65353]">{{ $errors->first() }}</div>
    @endif

    <section class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-7">
        <p class="text-[8px] font-semibold uppercase tracking-[.14em] text-[#a8731f]">Account Management</p>
        <h1 class="mt-2 text-[22px] font-bold tracking-[-.03em] text-[#302a24]">Buyer Profile</h1>
        <p class="mt-1 text-[9px] text-[#91887d]">Your registered profile is reused as the default checkout information.</p>

        @if ($buyerAccount)
            <form method="POST" action="{{ route('buyer.account.update') }}" class="mt-6 grid gap-4 sm:grid-cols-2">
                @csrf @method('PATCH')
                <div><label class="mb-2 block text-[8px] font-semibold text-[#62594e]">First Name</label><input name="first_name" value="{{ old('first_name', $buyerAccount->first_name) }}" required class="h-11 w-full rounded-xl border border-[#e4ddd3] px-4 text-[9px]"></div>
                <div><label class="mb-2 block text-[8px] font-semibold text-[#62594e]">Last Name</label><input name="last_name" value="{{ old('last_name', $buyerAccount->last_name) }}" required class="h-11 w-full rounded-xl border border-[#e4ddd3] px-4 text-[9px]"></div>
                <div><label class="mb-2 block text-[8px] font-semibold text-[#62594e]">Email</label><input value="{{ $buyerAccount->email }}" disabled class="h-11 w-full rounded-xl border border-[#e4ddd3] bg-[#f7f5f2] px-4 text-[9px] text-[#91887d]"></div>
                <div><label class="mb-2 block text-[8px] font-semibold text-[#62594e]">Contact Number</label><input name="contact_no" value="{{ old('contact_no', $buyerAccount->contact_no) }}" required class="h-11 w-full rounded-xl border border-[#e4ddd3] px-4 text-[9px]"></div>
                <div class="sm:col-span-2"><label class="mb-2 block text-[8px] font-semibold text-[#62594e]">Street / Unit Address</label><textarea name="street_address" rows="3" required class="w-full resize-none rounded-xl border border-[#e4ddd3] px-4 py-3 text-[9px]">{{ old('street_address', $buyerAccount->street_address) }}</textarea></div>
                <div class="sm:col-span-2 rounded-xl border border-[#eee7dc] bg-[#fcfbf8] p-4 text-[8px] leading-5 text-[#756d63]">Saved area: {{ $buyerAccount->barangay_name }}, {{ $buyerAccount->municipality_name }}, {{ $buyerAccount->province_name }}</div>
                <div class="sm:col-span-2"><button class="h-11 rounded-xl bg-[#c99128] px-5 text-[9px] font-semibold text-white">Save Account Changes</button></div>
            </form>
        @else
            <form method="POST" action="{{ route('buyer.account.update') }}" class="mt-6 max-w-[600px]">
                @csrf @method('PATCH')
                <div><label class="mb-2 block text-[8px] font-semibold text-[#62594e]">Display Name</label><input name="name" value="{{ old('name', $socialAccount?->name) }}" required class="h-11 w-full rounded-xl border border-[#e4ddd3] px-4 text-[9px]"></div>
                <div class="mt-4 rounded-xl border border-[#eadfc9] bg-[#fffaf2] p-4 text-[8px] leading-5 text-[#8a6a2e]">You signed in with {{ ucfirst($socialAccount?->provider ?? 'social login') }}. Shipping address and contact number are entered securely during checkout.</div>
                <button class="mt-4 h-11 rounded-xl bg-[#c99128] px-5 text-[9px] font-semibold text-white">Save Display Name</button>
            </form>
        @endif
    </section>
</div>

@include('components.support.complaint-form')
@endsection

@push('scripts')
    @vite('resources/js/buyer-account.js')
@endpush
