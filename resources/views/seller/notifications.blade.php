@extends('layouts.seller')
@section('title', 'Notifications — SARI Seller')
@section('page-title', 'Notifications')
@section('content')
<div class="mx-auto w-full max-w-[1700px] font-['Poppins',sans-serif]">
    @if(session('success'))
        <div class="mb-4 rounded-2xl border border-[#cfe4d7] bg-[#f2faf5] px-4 py-3 text-[10px] font-medium text-[#4f7d63]">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-2xl border border-[#efcece] bg-[#fff5f5] px-4 py-3 text-[10px] font-medium text-[#a65353]">{{ $errors->first() }}</div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-[8px] font-bold uppercase tracking-[.15em] text-[#b97805]">Support</p><h1 class="mt-2 text-[30px] font-semibold tracking-[-.04em] text-[#202124]">Notifications</h1><p class="mt-2 text-[10px] text-[#8A919B]">{{ $unread }} unread Seller notification{{ $unread===1?'':'s' }}.</p></div>@if($unread>0)<form method="POST" action="{{ route('seller.notifications.read-all') }}">@csrf<button class="h-10 rounded-xl bg-[#202124] px-4 text-[8px] font-semibold text-white">Mark All Read</button></form>@endif</div>
    <section class="mt-5 overflow-hidden rounded-[20px] border border-[#E7E9EE] bg-white">
        <div class="divide-y divide-[#eef0f2]">@forelse($notifications as $notification)
        <div class="flex gap-3 p-4 {{ $notification->read_at ? 'bg-white' : 'bg-[#fffaf1]' }}">
            <span class="mt-1 h-2 w-2 shrink-0 rounded-full {{ $notification->read_at ? 'bg-[#d7dbe0]' : 'bg-[#D89B10]' }}"></span>
            <div class="min-w-0 flex-1"><div class="flex justify-between gap-3"><p class="text-[9px] font-semibold">{{ $notification->title }}</p><span class="text-[7px] text-[#8A919B]">{{ $notification->created_at?->diffForHumans() }}</span></div><p class="mt-1 text-[8px] leading-4 text-[#6b7280]">{{ $notification->message }}</p><div class="mt-2 flex gap-3">@if($notification->action_url)<a href="{{ $notification->action_url }}" class="text-[8px] font-semibold text-[#B67A08]">Open →</a>@endif @if(!$notification->read_at)<form method="POST" action="{{ route('seller.notifications.read',$notification) }}">@csrf<button class="text-[8px] font-semibold text-[#6b7280]">Mark read</button></form>@endif</div></div>
        </div>
        @empty<div class="p-12 text-center text-[9px] text-[#8A919B]">No notifications yet.</div>@endforelse</div>
        @if($notifications->hasPages())<div class="border-t border-[#E7E9EE] p-4">{{ $notifications->links() }}</div>@endif
    </section>
</div>
@endsection
