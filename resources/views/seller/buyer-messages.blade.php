@extends('layouts.seller')

@section('title', 'Buyer Inbox — SARI Seller')
@section('page-title', 'Buyer Inbox')

@section('content')
<div class="mx-auto w-full max-w-[1600px]">
    @if (session('success')) <div class="mb-4 rounded-2xl border border-[#cfe4d7] bg-[#f1f8f4] px-4 py-3 text-[10px] text-[#4F7D63]">{{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="mb-4 rounded-2xl border border-[#efcece] bg-[#fff5f5] px-4 py-3 text-[10px] text-[#a65353]">{{ $errors->first() }}</div> @endif

    <section class="overflow-hidden rounded-[22px] border border-[#ebe4da] bg-white" style="height: calc(100dvh - 150px); min-height: 620px; max-height: 900px;">
        <div class="grid h-full min-h-0 lg:grid-cols-[290px_1fr]">
            <aside class="min-h-0 overflow-y-auto border-b border-[#eee8df] bg-[#fcfbf8] p-4 lg:border-b-0 lg:border-r">
                <div class="mb-4"><p class="text-[8px] font-semibold uppercase tracking-[.12em] text-[#a8731f]">Customer conversations</p><p class="mt-1 text-[8px] text-[#91887d]">Buyers with orders or messages</p></div>
                <div class="space-y-2">
                    @forelse ($conversations as $conversation)
                        <a href="{{ route('seller.buyer-messages', ['buyer' => $conversation['key']]) }}" class="block rounded-xl border p-3 {{ ($selected['key'] ?? null) === $conversation['key'] ? 'border-[#d7b978] bg-[#fff8eb]' : 'border-[#ebe4da] bg-white' }}">
                            <div class="flex items-center justify-between gap-2"><p class="truncate text-[9px] font-semibold text-[#514a42]">{{ $conversation['name'] }}</p>@if($conversation['unread'] > 0)<span class="rounded-full bg-[#c99128] px-2 py-0.5 text-[7px] font-bold text-white">{{ $conversation['unread'] }}</span>@endif</div>
                            <p class="mt-1 truncate text-[7px] text-[#91887d]">{{ $conversation['email'] ?: $conversation['key'] }}</p>
                        </a>
                    @empty
                        <p class="rounded-xl border border-dashed border-[#ded5c9] p-4 text-[8px] leading-4 text-[#91887d]">Buyer conversations will appear after an order or message.</p>
                    @endforelse
                </div>
            </aside>

            <div class="flex min-h-0 flex-col">
                @if ($selected)
                    <div class="shrink-0 border-b border-[#eee8df] px-5 py-4"><h2 class="text-[13px] font-bold text-[#302a24]">{{ $selected['name'] }}</h2><p class="mt-1 text-[8px] text-[#91887d]">{{ $selected['email'] ?: 'Buyer account' }}</p></div>
                    <div id="sellerBuyerMessageList" class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-5"><div class="space-y-3">
                        @forelse ($messages as $message)
                            <div class="flex {{ $message->sender_role === 'seller' ? 'justify-end' : 'justify-start' }}"><div class="max-w-[78%] rounded-2xl px-4 py-3 {{ $message->sender_role === 'seller' ? 'bg-[#3e3429] text-white' : 'border border-[#e7dfd4] bg-[#fcfbf8] text-[#514a42]' }}"><p class="whitespace-pre-wrap text-[9px] leading-5">{{ $message->body }}</p><p class="mt-2 text-[7px] opacity-70">{{ $message->created_at?->format('M d, h:i A') }}</p></div></div>
                        @empty
                            <div class="grid h-full place-items-center text-center"><p class="text-[9px] text-[#91887d]">No messages yet.</p></div>
                        @endforelse
                    </div></div>
                    <form method="POST" action="{{ route('seller.buyer-messages.send', $selected['key']) }}" class="shrink-0 border-t border-[#eee8df] p-4">@csrf<div class="flex gap-2"><textarea name="body" required maxlength="3000" rows="2" placeholder="Reply to buyer..." class="min-h-[52px] flex-1 resize-none rounded-xl border border-[#e4ddd3] px-4 py-3 text-[9px]"></textarea><button class="w-[96px] rounded-xl bg-[#3e3429] text-[9px] font-semibold text-white">Send</button></div></form>
                @else
                    <div class="grid h-full place-items-center p-8 text-center"><div><h2 class="text-[14px] font-bold text-[#403a33]">Choose a buyer</h2><p class="mt-2 text-[9px] text-[#91887d]">Order customers and incoming conversations appear on the left.</p></div></div>
                @endif
            </div>
        </div>
    </section>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>{const el=document.getElementById('sellerBuyerMessageList');if(el)el.scrollTop=el.scrollHeight;});</script>
@endsection
