@extends('layouts.buyer')

@section('title', 'Seller Messages — SARI')
@section('page-title', 'Seller Messages')

@section('content')
@include('components.buyer.header')

<div class="mx-auto w-full max-w-[1500px] p-4 sm:p-6 lg:p-8">
    @if (session('success'))
        <div class="mb-4 rounded-2xl border border-[#cfe4d7] bg-[#f1f8f4] px-4 py-3 text-[11px] text-[#4F7D63]">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-[#efcece] bg-[#fff5f5] px-4 py-3 text-[11px] text-[#a65353]">{{ $errors->first() }}</div>
    @endif

    <section class="overflow-hidden rounded-[22px] border border-[#ebe4da] bg-white" style="height: calc(100dvh - 150px); min-height: 620px; max-height: 900px;">
        <div class="grid h-full min-h-0 lg:grid-cols-[280px_1fr]">
            <aside class="min-h-0 overflow-y-auto border-b border-[#eee8df] bg-[#fcfbf8] p-4 lg:border-b-0 lg:border-r">
                <p class="text-[8px] font-semibold uppercase tracking-[.12em] text-[#a8731f]">Seller conversations</p>
                <div class="mt-3 space-y-2">
                    @forelse ($sellers as $seller)
                        <a href="{{ route('buyer.messages', ['seller' => $seller->id]) }}" class="block rounded-xl border p-3 transition {{ $selectedSeller?->id === $seller->id ? 'border-[#d7b978] bg-[#fff8eb]' : 'border-[#ebe4da] bg-white hover:bg-[#fcf8f1]' }}">
                            <p class="truncate text-[9px] font-semibold text-[#514a42]">{{ $seller->store_name ?: 'SARI Seller Store' }}</p>
                            <p class="mt-1 truncate text-[7px] text-[#91887d]">{{ $seller->email }}</p>
                        </a>
                    @empty
                        <p class="rounded-xl border border-dashed border-[#ded5c9] p-4 text-[8px] leading-4 text-[#91887d]">No seller conversation is available yet.</p>
                    @endforelse
                </div>
            </aside>

            <div class="flex min-h-0 flex-col">
                @if ($selectedSeller)
                    <div class="shrink-0 border-b border-[#eee8df] px-5 py-4">
                        <h2 class="text-[13px] font-bold text-[#302a24]">{{ $selectedSeller->store_name ?: 'SARI Seller Store' }}</h2>
                        <p class="mt-1 text-[8px] text-[#91887d]">Direct Buyer ↔ Seller conversation</p>
                    </div>

                    <div id="buyerSellerMessageList" class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-5">
                        <div class="space-y-3">
                            @forelse ($messages as $message)
                                <div class="flex {{ $message->sender_role === 'buyer' ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[78%] rounded-2xl px-4 py-3 {{ $message->sender_role === 'buyer' ? 'bg-[#c99128] text-white' : 'border border-[#e7dfd4] bg-[#fcfbf8] text-[#514a42]' }}">
                                        <p class="whitespace-pre-wrap text-[9px] leading-5">{{ $message->body }}</p>
                                        <p class="mt-2 text-[7px] opacity-70">{{ $message->created_at?->format('M d, h:i A') }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="grid h-full place-items-center text-center">
                                    <div><p class="text-[11px] font-semibold text-[#514a42]">Start the conversation</p><p class="mt-1 text-[8px] text-[#91887d]">Ask about a product or your order.</p></div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <form method="POST" action="{{ route('buyer.messages.send', $selectedSeller) }}" class="shrink-0 border-t border-[#eee8df] bg-white p-4">
                        @csrf
                        <div class="flex gap-2">
                            <textarea name="body" required maxlength="3000" rows="2" placeholder="Message the seller..." class="min-h-[52px] flex-1 resize-none rounded-xl border border-[#e4ddd3] px-4 py-3 text-[9px] outline-none focus:border-[#c99128]"></textarea>
                            <button class="w-[96px] rounded-xl bg-[#c99128] text-[9px] font-semibold text-white">Send</button>
                        </div>
                    </form>
                @else
                    <div class="grid h-full place-items-center p-8 text-center"><div><h2 class="text-[14px] font-bold text-[#403a33]">Choose a seller</h2><p class="mt-2 text-[9px] text-[#91887d]">Approved shops appear here automatically.</p></div></div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
    @vite('resources/js/buyer-messages.js')
@endpush
