@extends('layouts.seller')
@section('title', 'Compliance Center — SARI Seller')
@section('page-title', 'Compliance Center')
@section('content')
<div class="mx-auto w-full max-w-[1700px] font-['Poppins',sans-serif]">
    @if(session('success'))
        <div class="mb-4 rounded-2xl border border-[#cfe4d7] bg-[#f2faf5] px-4 py-3 text-[10px] font-medium text-[#4f7d63]">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-2xl border border-[#efcece] bg-[#fff5f5] px-4 py-3 text-[10px] font-medium text-[#a65353]">{{ $errors->first() }}</div>
    @endif

    <div><p class="text-[8px] font-bold uppercase tracking-[.15em] text-[#b97805]">Seller Compliance</p><h1 class="mt-2 text-[30px] font-semibold tracking-[-.04em] text-[#202124]">Compliance Center</h1><p class="mt-2 text-[10px] text-[#8A919B]">Review warnings, account restrictions, and communicate with SARI Administration.</p></div>
    <div class="mt-5 grid gap-5 xl:grid-cols-[.8fr_1.2fr]">
        <section class="rounded-[20px] border border-[#E7E9EE] bg-white p-5"><h2 class="text-[13px] font-semibold">Account Standing</h2><div class="mt-4 rounded-xl bg-[#FAFAFB] p-4"><p class="text-[8px] text-[#8A919B]">Account status</p><p class="mt-1 text-[12px] font-semibold">{{ ucfirst($seller->account_status) }}</p><p class="mt-3 text-[8px] text-[#8A919B]">Warnings</p><p class="mt-1 text-[18px] font-bold">{{ (int)$seller->warning_count }} / 3</p>@if($seller->suspended_until)<p class="mt-3 text-[8px] text-[#a65353]">Suspended until {{ $seller->suspended_until->format('M d, Y h:i A') }}</p>@endif</div><div class="mt-4 space-y-2">@forelse($warnings as $warning)<div class="rounded-xl border border-[#efdfc2] bg-[#fffaf1] p-3"><p class="text-[8px] font-semibold text-[#9a680b]">{{ $warning->reason ?? 'Compliance Warning' }}</p><p class="mt-1 text-[7px] text-[#8A919B]">{{ $warning->created_at?->format('M d, Y') }}</p></div>@empty<p class="text-[8px] text-[#8A919B]">No warning records.</p>@endforelse</div></section>
        <section class="rounded-[20px] border border-[#E7E9EE] bg-white p-5"><h2 class="text-[13px] font-semibold">Admin Communication</h2><div class="mt-4 max-h-[430px] space-y-2 overflow-y-auto">@forelse($messages as $message)<div class="rounded-xl p-3 {{ $message->sender_role==='seller' ? 'ml-10 bg-[#FFF7E6]' : 'mr-10 bg-[#F4F5F7]' }}"><p class="text-[7px] font-bold uppercase text-[#8A919B]">{{ $message->sender_role }}</p><p class="mt-1 text-[8px] leading-4 text-[#4B5563]">{{ $message->message }}</p></div>@empty<p class="text-[8px] text-[#8A919B]">No compliance messages.</p>@endforelse</div><form method="POST" action="{{ route('seller.compliance.message') }}" class="mt-4 flex gap-2">@csrf<input name="message" required maxlength="3000" placeholder="Message the SARI Administrator..." class="h-10 min-w-0 flex-1 rounded-xl border border-[#E7E9EE] px-3 text-[8px]"><button class="h-10 rounded-xl bg-[#202124] px-4 text-[8px] font-semibold text-white">Send</button></form></section>
    </div>
</div>
@endsection
