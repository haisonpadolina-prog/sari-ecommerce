@extends('layouts.seller')
@section('title', 'Promotions & Vouchers — SARI Seller')
@section('page-title', 'Promotions & Vouchers')
@section('content')
<div class="mx-auto w-full max-w-[1700px] font-['Poppins',sans-serif]">
    @if(session('success'))
        <div class="mb-4 rounded-2xl border border-[#cfe4d7] bg-[#f2faf5] px-4 py-3 text-[10px] font-medium text-[#4f7d63]">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-2xl border border-[#efcece] bg-[#fff5f5] px-4 py-3 text-[10px] font-medium text-[#a65353]">{{ $errors->first() }}</div>
    @endif

    <div><p class="text-[8px] font-bold uppercase tracking-[.15em] text-[#b97805]">Marketing</p><h1 class="mt-2 text-[30px] font-semibold tracking-[-.04em] text-[#202124]">Promotions & Vouchers</h1><p class="mt-2 text-[10px] text-[#8A919B]">Create server-validated Seller vouchers that Buyers can apply during checkout.</p></div>
    <div class="mt-5 grid gap-5 xl:grid-cols-[420px_1fr]">
        <form method="POST" action="{{ route('seller.vouchers.store') }}" class="h-fit rounded-[20px] border border-[#E7E9EE] bg-white p-5">@csrf
            <h2 class="text-[13px] font-semibold">Create Voucher</h2>
            <div class="mt-4 grid gap-3">
                <input name="code" required placeholder="Code e.g. SARI10" class="h-11 rounded-xl border border-[#E7E9EE] px-3 text-[9px] uppercase">
                <input name="name" required placeholder="Campaign name" class="h-11 rounded-xl border border-[#E7E9EE] px-3 text-[9px]">
                <div class="grid grid-cols-2 gap-2"><select name="discount_type" class="h-11 rounded-xl border border-[#E7E9EE] px-3 text-[9px]"><option value="percentage">Percentage</option><option value="fixed">Fixed Amount</option></select><input name="discount_value" type="number" step=".01" min=".01" required placeholder="Value" class="h-11 rounded-xl border border-[#E7E9EE] px-3 text-[9px]"></div>
                <div class="grid grid-cols-2 gap-2"><input name="minimum_spend" type="number" step=".01" min="0" placeholder="Minimum spend" class="h-11 rounded-xl border border-[#E7E9EE] px-3 text-[9px]"><input name="maximum_discount" type="number" step=".01" min="0" placeholder="Max discount" class="h-11 rounded-xl border border-[#E7E9EE] px-3 text-[9px]"></div>
                <input name="usage_limit" type="number" min="1" placeholder="Usage limit (optional)" class="h-11 rounded-xl border border-[#E7E9EE] px-3 text-[9px]">
                <div class="grid grid-cols-2 gap-2"><input name="starts_at" type="datetime-local" class="h-11 rounded-xl border border-[#E7E9EE] px-3 text-[9px]"><input name="ends_at" type="datetime-local" class="h-11 rounded-xl border border-[#E7E9EE] px-3 text-[9px]"></div>
                <button class="h-11 rounded-xl bg-[#D89B10] text-[9px] font-semibold text-white">Create Voucher</button>
            </div>
        </form>
        <section class="overflow-hidden rounded-[20px] border border-[#E7E9EE] bg-white">
            <div class="border-b border-[#E7E9EE] px-5 py-4"><h2 class="text-[13px] font-semibold">Seller Vouchers</h2></div>
            <div class="divide-y divide-[#eef0f2]">@forelse($vouchers as $voucher)
                <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div><div class="flex items-center gap-2"><span class="rounded-lg bg-[#FFF7E6] px-2.5 py-1 text-[9px] font-bold text-[#B67A08]">{{ $voucher->code }}</span><span class="text-[9px] font-semibold">{{ $voucher->name }}</span></div><p class="mt-2 text-[8px] text-[#8A919B]">{{ $voucher->discount_type==='percentage' ? $voucher->discount_value.'%' : '₱'.number_format((float)$voucher->discount_value,2) }} off · Used {{ $voucher->used_count }}{{ $voucher->usage_limit ? '/'.$voucher->usage_limit : '' }}</p></div>
                    <div class="flex gap-2"><form method="POST" action="{{ route('seller.vouchers.toggle',$voucher) }}">@csrf<button class="h-9 rounded-xl border border-[#E7E9EE] px-3 text-[8px] font-semibold">{{ $voucher->is_active ? 'Deactivate' : 'Activate' }}</button></form><form method="POST" action="{{ route('seller.vouchers.destroy',$voucher) }}">@csrf @method('DELETE')<button class="h-9 rounded-xl border border-[#efcece] px-3 text-[8px] font-semibold text-[#a65353]">Delete</button></form></div>
                </div>
            @empty<div class="p-10 text-center text-[9px] text-[#8A919B]">No vouchers created yet.</div>@endforelse</div>
            @if($vouchers->hasPages())<div class="border-t border-[#E7E9EE] p-4">{{ $vouchers->links() }}</div>@endif
        </section>
    </div>
</div>
@endsection
