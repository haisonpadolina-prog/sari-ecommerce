@extends('layouts.seller')
@section('title', 'Store Management — SARI Seller')
@section('page-title', 'Store Management')
@section('content')
<div class="mx-auto w-full max-w-[1700px] font-['Poppins',sans-serif]">
    @if(session('success'))
        <div class="mb-4 rounded-2xl border border-[#cfe4d7] bg-[#f2faf5] px-4 py-3 text-[10px] font-medium text-[#4f7d63]">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-2xl border border-[#efcece] bg-[#fff5f5] px-4 py-3 text-[10px] font-medium text-[#a65353]">{{ $errors->first() }}</div>
    @endif

    <div><p class="text-[8px] font-bold uppercase tracking-[.15em] text-[#b97805]">Public Store</p><h1 class="mt-2 text-[30px] font-semibold tracking-[-.04em] text-[#202124]">Store Management</h1><p class="mt-2 text-[10px] text-[#8A919B]">Manage public store information and pickup instructions separately from account security.</p></div>
    <form method="POST" action="{{ route('seller.store.update') }}" class="mt-5 max-w-[900px] rounded-[20px] border border-[#E7E9EE] bg-white p-5 sm:p-6">@csrf @method('PATCH')
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2"><label class="text-[8px] font-semibold text-[#6b7280]">Store Name</label><input name="store_name" required value="{{ old('store_name',$seller->store_name) }}" class="mt-2 h-11 w-full rounded-xl border border-[#E7E9EE] px-3 text-[9px]"></div>
            <div class="sm:col-span-2"><label class="text-[8px] font-semibold text-[#6b7280]">Store Description</label><textarea name="store_description" rows="4" class="mt-2 w-full rounded-xl border border-[#E7E9EE] p-3 text-[9px]">{{ old('store_description',$seller->store_description) }}</textarea></div>
            <div><label class="text-[8px] font-semibold text-[#6b7280]">Public Phone</label><input name="store_phone" value="{{ old('store_phone',$seller->store_phone ?: $seller->contact_no) }}" class="mt-2 h-11 w-full rounded-xl border border-[#E7E9EE] px-3 text-[9px]"></div>
            <div><label class="text-[8px] font-semibold text-[#6b7280]">Public Email</label><input name="store_public_email" type="email" value="{{ old('store_public_email',$seller->store_public_email ?: $seller->email) }}" class="mt-2 h-11 w-full rounded-xl border border-[#E7E9EE] px-3 text-[9px]"></div>
            <div><label class="text-[8px] font-semibold text-[#6b7280]">Store Status</label><select name="store_status" class="mt-2 h-11 w-full rounded-xl border border-[#E7E9EE] px-3 text-[9px]"><option value="open" @selected($seller->store_status==='open')>Open</option><option value="paused" @selected($seller->store_status==='paused')>Paused</option></select></div>
            <div class="sm:col-span-2"><label class="text-[8px] font-semibold text-[#6b7280]">Pickup Address</label><textarea name="street_address" rows="3" class="mt-2 w-full rounded-xl border border-[#E7E9EE] p-3 text-[9px]">{{ old('street_address',$seller->street_address) }}</textarea></div>
            <div class="sm:col-span-2"><label class="text-[8px] font-semibold text-[#6b7280]">Pickup Instructions</label><textarea name="pickup_instructions" rows="3" class="mt-2 w-full rounded-xl border border-[#E7E9EE] p-3 text-[9px]">{{ old('pickup_instructions',$seller->pickup_instructions) }}</textarea></div>
        </div>
        <button class="mt-5 h-11 rounded-xl bg-[#D89B10] px-5 text-[9px] font-semibold text-white">Save Store Settings</button>
    </form>
</div>
@endsection
