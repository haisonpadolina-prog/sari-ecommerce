@extends('layouts.seller')

@section('title', 'Store Management — SARI Seller')
@section('page-title', 'Store Management')

@section('content')
@php
    $storeName = $seller->store_name ?: 'SARI Seller Store';
    $initials = collect(preg_split('/\s+/', $storeName) ?: [])
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('') ?: 'SS';

    $storeStatus = old('store_status', $seller->store_status ?: 'open');
    $registeredAddress = collect([
        $seller->street_address,
        $seller->barangay_name,
        $seller->municipality_name,
        $seller->province_name,
    ])->filter()->implode(', ');
@endphp

<style>
    .seller-store-page { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; color:#302a24; }
    .seller-store-card { border:1px solid #e9e3da; background:#fff; border-radius:18px; box-shadow:0 8px 26px rgba(55,45,32,.035); }
    .seller-store-control { width:100%; min-height:44px; border:1px solid #e3ddd4; border-radius:12px; background:#fff; padding:0 14px; color:#302a24; font-size:12px; transition:border-color .18s ease, box-shadow .18s ease; }
    textarea.seller-store-control { min-height:96px; padding-top:12px; padding-bottom:12px; }
    .seller-store-control:focus { outline:none; border-color:#d49a2d; box-shadow:0 0 0 4px rgba(212,154,45,.08); }
    .seller-store-label { display:block; margin-bottom:7px; color:#625a50; font-size:11px; font-weight:600; }
</style>

<div class="seller-store-page mx-auto w-full max-w-[1500px] pb-8">
    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 rounded-[14px] border border-[#d5e6da] bg-[#f4faf6] px-4 py-3 text-[11px] text-[#51715d]"><span class="grid h-8 w-8 place-items-center rounded-lg bg-white">✓</span>{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-[14px] border border-[#ead2d2] bg-[#fff7f7] px-4 py-3 text-[11px] text-[#955f5f]">{{ $errors->first() }}</div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#a8731f]">Public Store & Fulfillment</p>
            <h1 class="mt-1 text-[28px] font-bold tracking-[-.04em] text-[#202124]">Store <span class="text-[#c99128]">Management</span></h1>
            <p class="mt-2 max-w-[760px] text-[10px] leading-5 text-[#8A919B]">Manage the information buyers see and the operational pickup details used by SARI fulfillment. Account identity and verification remain under Account Management.</p>
        </div>
        <a href="{{ route('seller.account') }}" wire:navigate.hover class="inline-flex min-h-10 items-center justify-center rounded-xl border border-[#e2d7c6] bg-white px-4 text-[10px] font-semibold text-[#6d6255] hover:bg-[#fffaf2]">Account Management</a>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-[minmax(0,1.35fr)_minmax(330px,.65fr)]">
        <form method="POST" action="{{ route('seller.store.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <section class="seller-store-card overflow-hidden">
                <div class="border-b border-[#eee8df] px-5 py-4 sm:px-6">
                    <p class="text-[9px] font-bold uppercase tracking-[.13em] text-[#a8731f]">Storefront</p>
                    <h2 class="mt-1 text-[16px] font-bold">Public store information</h2>
                    <p class="mt-1 text-[10px] leading-5 text-[#91887d]">These details identify your shop to buyers across the marketplace.</p>
                </div>
                <div class="grid gap-4 p-5 sm:grid-cols-2 sm:p-6">
                    <div class="sm:col-span-2">
                        <label class="seller-store-label" for="storeName">Store name</label>
                        <input id="storeName" name="store_name" required maxlength="150" value="{{ old('store_name', $seller->store_name) }}" class="seller-store-control">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="seller-store-label" for="storeDescription">Store description</label>
                        <textarea id="storeDescription" name="store_description" rows="5" maxlength="2000" class="seller-store-control">{{ old('store_description', $seller->store_description) }}</textarea>
                    </div>
                    <div>
                        <label class="seller-store-label" for="storePhone">Public phone</label>
                        <input id="storePhone" name="store_phone" maxlength="40" value="{{ old('store_phone', $seller->store_phone ?: $seller->contact_no) }}" class="seller-store-control">
                        <p class="mt-1.5 text-[9px] leading-4 text-[#978e83]">Shown as store contact information, separate from your account identity.</p>
                    </div>
                    <div>
                        <label class="seller-store-label" for="storePublicEmail">Public email</label>
                        <input id="storePublicEmail" name="store_public_email" type="email" maxlength="255" value="{{ old('store_public_email', $seller->store_public_email ?: $seller->email) }}" class="seller-store-control">
                        <p class="mt-1.5 text-[9px] leading-4 text-[#978e83]">This does not change the email you use to log in.</p>
                    </div>
                </div>
            </section>

            <section class="seller-store-card overflow-hidden">
                <div class="border-b border-[#eee8df] px-5 py-4 sm:px-6">
                    <p class="text-[9px] font-bold uppercase tracking-[.13em] text-[#a8731f]">Availability</p>
                    <h2 class="mt-1 text-[16px] font-bold">Store selling status</h2>
                </div>
                <div class="p-5 sm:p-6">
                    <label class="seller-store-label" for="storeStatus">Store status</label>
                    <select id="storeStatus" name="store_status" required class="seller-store-control">
                        <option value="open" @selected($storeStatus === 'open')>Open — accepting new orders</option>
                        <option value="paused" @selected($storeStatus === 'paused')>Paused — temporarily stop new orders</option>
                    </select>
                    <div class="mt-3 rounded-[13px] border border-[#e7e1d8] bg-[#faf9f7] p-3 text-[9px] leading-5 text-[#81786c]">
                        <strong class="text-[#514a42]">Open:</strong> approved listings can appear in the marketplace and buyers can checkout. <strong class="text-[#514a42]">Paused:</strong> new selling is stopped while your existing orders remain available for fulfillment.
                    </div>
                </div>
            </section>

            <section class="seller-store-card overflow-hidden">
                <div class="border-b border-[#eee8df] px-5 py-4 sm:px-6">
                    <p class="text-[9px] font-bold uppercase tracking-[.13em] text-[#a8731f]">Fulfillment</p>
                    <h2 class="mt-1 text-[16px] font-bold">Pickup location & instructions</h2>
                    <p class="mt-1 text-[10px] leading-5 text-[#91887d]">This operational address can differ from your verified registration address.</p>
                </div>
                <div class="space-y-4 p-5 sm:p-6">
                    <div>
                        <label class="seller-store-label" for="pickupAddress">Pickup address</label>
                        <textarea id="pickupAddress" name="pickup_address" required rows="4" maxlength="1000" class="seller-store-control">{{ old('pickup_address', $seller->pickup_address ?: $registeredAddress) }}</textarea>
                    </div>
                    <div>
                        <label class="seller-store-label" for="pickupInstructions">Pickup instructions</label>
                        <textarea id="pickupInstructions" name="pickup_instructions" rows="4" maxlength="1200" placeholder="Gate, landmark, receiving hours, contact instructions, etc." class="seller-store-control">{{ old('pickup_instructions', $seller->pickup_instructions) }}</textarea>
                    </div>
                    <div class="rounded-[13px] border border-[#eadfc9] bg-[#fffaf3] p-3.5">
                        <p class="text-[9px] font-bold text-[#7f642f]">Verified registration address — read only</p>
                        <p class="mt-1 text-[9px] leading-5 text-[#8e7c61]">{{ $registeredAddress ?: 'No verified registration address is stored.' }}</p>
                        <p class="mt-2 text-[9px] leading-5 text-[#9a8b70]">Changing your pickup address does not alter the address that was reviewed during seller registration.</p>
                    </div>
                </div>
            </section>

            <div class="seller-store-card flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-[9px] leading-5 text-[#8e8579]">Saving updates the active public store and fulfillment settings immediately.</p>
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#D89B10] px-6 text-[10px] font-bold text-white transition hover:bg-[#bd8205]">Save Store Settings</button>
            </div>
        </form>

        <aside class="space-y-4 xl:sticky xl:top-[112px]">
            <section class="seller-store-card overflow-hidden">
                <div class="border-b border-[#eee8df] bg-[#fcfbf9] px-5 py-3.5">
                    <p class="text-[9px] font-bold uppercase tracking-[.12em] text-[#9a7b43]">Store Preview</p>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3">
                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-[15px] border border-[#eadfc9] bg-[#fff8eb]">
                            @if($profilePhotoUrl)
                                <img src="{{ $profilePhotoUrl }}" alt="Seller profile photo" class="h-full w-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';">
                                <div style="display:none" class="h-full w-full place-items-center text-[16px] font-bold text-[#b77c18]">{{ $initials }}</div>
                            @else
                                <div class="grid h-full w-full place-items-center text-[16px] font-bold text-[#b77c18]">{{ $initials }}</div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-[13px] font-bold text-[#302a24]">{{ $storeName }}</p>
                            <p class="mt-1 text-[9px] text-[#91887d]">{{ $seller->line_of_business ?: 'SARI Marketplace Seller' }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between rounded-xl border border-[#e9e3da] bg-[#fcfbf9] p-3">
                        <span class="text-[9px] text-[#91887d]">Current status</span>
                        <span class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-[8px] font-bold {{ ($seller->store_status ?: 'open') === 'open' ? 'bg-[#eef6f1] text-[#56816a]' : 'bg-[#fff4e8] text-[#a8731f]' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ ($seller->store_status ?: 'open') === 'open' ? 'bg-[#67a17c]' : 'bg-[#d89b10]' }}"></span>
                            {{ ucfirst($seller->store_status ?: 'open') }}
                        </span>
                    </div>
                    <p class="mt-4 text-[9px] leading-5 text-[#91887d]">Your seller profile photo is shown here as Seller Center identity context. Change that photo under Account Management.</p>
                </div>
            </section>

            <section class="seller-store-card p-5">
                <p class="text-[9px] font-bold uppercase tracking-[.12em] text-[#9a7b43]">Separation of duties</p>
                <div class="mt-3 space-y-3 text-[9px] leading-5 text-[#81786c]">
                    <p><strong class="text-[#514a42]">Account Management:</strong> personal identity, profile photo, login security, verified registration requirements.</p>
                    <p><strong class="text-[#514a42]">Store Management:</strong> public store identity, public contacts, availability, and operational pickup details.</p>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
