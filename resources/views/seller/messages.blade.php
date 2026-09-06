@extends('layouts.seller')

@section('title', 'Chat / Messaging — SARI Seller')
@section('page-title', 'Chat / Messaging')

@section('content')

<style>
    /*
    |--------------------------------------------------------------------------
    | FIXED CHAT WORKSPACE + INTERNAL SCROLL
    |--------------------------------------------------------------------------
    | The Seller page itself stays compact. Only the message list scrolls.
    | Composer stays fixed at the bottom of the center chat column.
    */
    #sellerChatWorkspace {
        height: calc(100dvh - 150px);
        min-height: 620px;
        max-height: 900px;
    }

    #sellerChatWorkspace > .seller-chat-grid {
        height: 100%;
        min-height: 0;
    }

    #sellerChatConversationList,
    #sellerChatCenterColumn,
    #sellerChatAccountDetails {
        min-height: 0;
    }

    #sellerChatConversationList,
    #sellerChatAccountDetails {
        overflow-y: auto;
        overscroll-behavior: contain;
    }

    #sellerChatCenterColumn {
        height: 100%;
        overflow: hidden;
    }

    #sellerChatMessages {
        min-height: 0;
        overflow-y: auto;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: #d8c7a7 transparent;
    }

    #sellerChatMessages::-webkit-scrollbar,
    #sellerChatConversationList::-webkit-scrollbar,
    #sellerChatAccountDetails::-webkit-scrollbar {
        width: 6px;
    }

    #sellerChatMessages::-webkit-scrollbar-track,
    #sellerChatConversationList::-webkit-scrollbar-track,
    #sellerChatAccountDetails::-webkit-scrollbar-track {
        background: transparent;
    }

    #sellerChatMessages::-webkit-scrollbar-thumb,
    #sellerChatConversationList::-webkit-scrollbar-thumb,
    #sellerChatAccountDetails::-webkit-scrollbar-thumb {
        background: #d8c7a7;
        border-radius: 999px;
    }

    #sellerChatComposer {
        position: relative;
        z-index: 20;
        flex: 0 0 auto;
        background: white;
        box-shadow: 0 -8px 24px rgba(35, 28, 20, .035);
    }

    /*
    |--------------------------------------------------------------------------
    | CHAT SKELETON
    |--------------------------------------------------------------------------
    | Skeleton covers only this page workspace.
    | Seller sidebar/header/logo remain visible immediately.
    */
    #sellerChatSkeleton {
        display: block;
    }

    #sellerChatRealContent {
        height: 100%;
        opacity: 0;
        visibility: hidden;
    }

    #sellerChatWorkspace.seller-chat-ready #sellerChatSkeleton {
        display: none;
    }

    #sellerChatWorkspace.seller-chat-ready #sellerChatRealContent {
        opacity: 1;
        visibility: visible;
        transition: opacity .32s cubic-bezier(.22, 1, .36, 1);
    }

    .seller-chat-skeleton-block {
        position: relative;
        overflow: hidden;
        background: #eeeae4;
    }

    .seller-chat-skeleton-block::after {
        content: "";
        position: absolute;
        inset: 0;
        transform: translateX(-100%);
        background: linear-gradient(
            90deg,
            rgba(255,255,255,0) 0%,
            rgba(255,255,255,.76) 48%,
            rgba(255,255,255,0) 100%
        );
        animation: sellerChatSkeletonSweep 1.2s ease-in-out infinite;
    }

    @keyframes sellerChatSkeletonSweep {
        100% { transform: translateX(100%); }
    }

    @media (max-width: 1023px) {
        #sellerChatWorkspace {
            height: calc(100dvh - 132px);
            min-height: 560px;
            max-height: none;
        }
    }

    @media (max-width: 639px) {
        #sellerChatWorkspace {
            height: calc(100dvh - 118px);
            min-height: 520px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-chat-skeleton-block::after {
            animation: none !important;
        }

        #sellerChatRealContent {
            transition: none !important;
        }
    }
</style>

<div class="mx-auto w-full max-w-[1800px]">

    @php
        /*
        |--------------------------------------------------------------------------
        | MESSAGE REACTIONS — ADDITIVE ONLY
        |--------------------------------------------------------------------------
        | The existing Seller ↔ Admin chat backend stays untouched.
        | If the new reactions table has not been migrated yet, the page still loads
        | normally and simply shows no saved reactions.
        */
        $sellerReactionGroups = collect();

        if (\Illuminate\Support\Facades\Schema::hasTable('chat_message_reactions')) {
            $sellerReactionGroups = \App\Models\ChatMessageReaction::query()
                ->whereIn('chat_message_id', $messages->pluck('id'))
                ->get()
                ->groupBy('chat_message_id');
        }


        $sellerChatRestriction = null;
        $sellerChatBlocked = false;

        if (\Illuminate\Support\Facades\Schema::hasTable('seller_chat_restrictions')) {
            $sellerChatRestriction = \App\Models\SellerChatRestriction::query()
                ->where('seller_account_id', $seller->id)
                ->first();
            $sellerChatBlocked = (bool) ($sellerChatRestriction?->is_blocked);
        }
    @endphp

    @if (session('success'))
        <div class="mb-5 rounded-[18px] border border-[#d5e5dc] bg-[#f3f8f5] px-5 py-4 text-[12px] font-medium text-[#56816a]">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-[18px] border border-[#ead7d7] bg-[#fdf5f5] px-5 py-4 text-[12px] text-[#a45f5f]">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- CHAT WORKSPACE --}}
    <section
        id="sellerChatWorkspace"
        class="relative overflow-hidden rounded-[20px] border border-[#e8e1d8] bg-white shadow-[0_14px_38px_rgba(35,28,20,.07)]"
    >
        {{-- Skeleton only for this chat workspace --}}
        <div id="sellerChatSkeleton" aria-hidden="true" class="absolute inset-0 z-30 bg-white">
            <div class="seller-chat-grid grid h-full min-h-0 grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] 2xl:grid-cols-[280px_minmax(0,1fr)_280px]">
                {{-- Left skeleton --}}
                <div class="hidden border-r border-[#ebe5dc] bg-white lg:block">
                    <div class="border-b border-[#eee8df] p-5">
                        <div class="seller-chat-skeleton-block h-4 w-28 rounded-full"></div>
                        <div class="seller-chat-skeleton-block mt-2 h-2.5 w-24 rounded-full"></div>
                        <div class="seller-chat-skeleton-block mt-4 h-11 w-full rounded-xl"></div>
                    </div>

                    <div class="p-5">
                        <div class="flex items-center gap-3">
                            <div class="seller-chat-skeleton-block h-11 w-11 shrink-0 rounded-full"></div>
                            <div class="min-w-0 flex-1">
                                <div class="seller-chat-skeleton-block h-3.5 w-32 rounded-full"></div>
                                <div class="seller-chat-skeleton-block mt-2 h-2.5 w-24 rounded-full"></div>
                                <div class="seller-chat-skeleton-block mt-3 h-2.5 w-full rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Center skeleton --}}
                <div class="flex min-h-0 flex-col bg-white">
                    <div class="flex h-[78px] shrink-0 items-center gap-3 border-b border-[#ebe5dc] px-5">
                        <div class="seller-chat-skeleton-block h-11 w-11 rounded-full"></div>
                        <div>
                            <div class="seller-chat-skeleton-block h-3.5 w-36 rounded-full"></div>
                            <div class="seller-chat-skeleton-block mt-2 h-2.5 w-24 rounded-full"></div>
                        </div>
                    </div>

                    <div class="min-h-0 flex-1 overflow-hidden bg-[#fbfaf7] px-5 py-6">
                        <div class="space-y-5">
                            <div class="flex items-end gap-2.5">
                                <div class="seller-chat-skeleton-block h-8 w-8 rounded-full"></div>
                                <div class="w-[58%]">
                                    <div class="seller-chat-skeleton-block h-14 rounded-[14px]"></div>
                                    <div class="seller-chat-skeleton-block mt-2 h-2 w-16 rounded-full"></div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <div class="w-[54%]">
                                    <div class="seller-chat-skeleton-block h-20 rounded-[14px]"></div>
                                    <div class="seller-chat-skeleton-block ml-auto mt-2 h-2 w-16 rounded-full"></div>
                                </div>
                            </div>

                            <div class="flex items-end gap-2.5">
                                <div class="seller-chat-skeleton-block h-8 w-8 rounded-full"></div>
                                <div class="w-[65%]">
                                    <div class="seller-chat-skeleton-block h-24 rounded-[14px]"></div>
                                    <div class="seller-chat-skeleton-block mt-2 h-2 w-16 rounded-full"></div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <div class="w-[46%]">
                                    <div class="seller-chat-skeleton-block h-14 rounded-[14px]"></div>
                                    <div class="seller-chat-skeleton-block ml-auto mt-2 h-2 w-16 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0 border-t border-[#ebe5dc] bg-white p-4">
                        <div class="rounded-[16px] border border-[#e3ddd4] p-3">
                            <div class="seller-chat-skeleton-block h-[54px] rounded-xl"></div>
                            <div class="mt-3 flex items-center justify-between border-t border-[#eee8df] pt-3">
                                <div class="seller-chat-skeleton-block h-8 w-28 rounded-lg"></div>
                                <div class="seller-chat-skeleton-block h-10 w-28 rounded-[11px]"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right skeleton --}}
                <div class="hidden border-l border-[#ebe5dc] bg-white 2xl:block">
                    <div class="border-b border-[#eee8df] p-5">
                        <div class="seller-chat-skeleton-block h-3.5 w-28 rounded-full"></div>
                        <div class="seller-chat-skeleton-block mt-2 h-2.5 w-32 rounded-full"></div>
                    </div>

                    <div class="p-5">
                        <div class="mx-auto seller-chat-skeleton-block h-16 w-16 rounded-full"></div>
                        <div class="mx-auto mt-3 seller-chat-skeleton-block h-3.5 w-36 rounded-full"></div>
                        <div class="mx-auto mt-2 seller-chat-skeleton-block h-2.5 w-28 rounded-full"></div>

                        <div class="mt-6 space-y-3">
                            @for ($i = 0; $i < 3; $i++)
                                <div class="rounded-[14px] border border-[#e8e2d9] p-4">
                                    <div class="seller-chat-skeleton-block h-2.5 w-24 rounded-full"></div>
                                    <div class="seller-chat-skeleton-block mt-3 h-3.5 w-20 rounded-full"></div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="sellerChatRealContent" class="seller-chat-grid grid h-full min-h-0 grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] 2xl:grid-cols-[280px_minmax(0,1fr)_280px]">

            {{-- LEFT: CONVERSATIONS --}}
            <aside id="sellerChatConversationList" class="border-b border-[#ebe5dc] bg-white lg:border-b-0 lg:border-r">
                <div class="border-b border-[#eee8df] px-4 py-4 sm:px-5">
                    <h3 class="font-bold tracking-[-0.02em] text-[#24201b]" style="font-size:clamp(14px,.88vw,16px)">Conversations</h3>
                    <p class="mt-1 text-[#938a7f]" style="font-size:clamp(10px,.66vw,12px)">1 support conversation</p>

                    <div class="relative mt-4">
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9b9287]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="6.5"></circle>
                            <path d="m16 16 4 4"></path>
                        </svg>
                        <input
                            id="sellerMessageSearch"
                            type="search"
                            autocomplete="off"
                            placeholder="Search messages..."
                            class="h-11 w-full rounded-[12px] border border-[#e3ddd4] bg-[#fbfaf8] pl-10 pr-10 text-[#3d3730] outline-none transition placeholder:text-[#aaa197] focus:border-[#c99128] focus:bg-white focus:ring-4 focus:ring-[#c99128]/10"
                            style="font-size:clamp(11px,.72vw,13px)"
                        >
                        <button
                            id="sellerMessageSearchClear"
                            type="button"
                            class="absolute right-2.5 top-1/2 hidden h-7 w-7 -translate-y-1/2 place-items-center rounded-lg text-[#91887d] transition hover:bg-[#f1ede7] hover:text-[#4d463e]"
                            aria-label="Clear message search"
                            title="Clear search"
                        >
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <path d="m7 7 10 10"></path>
                                <path d="m17 7-10 10"></path>
                            </svg>
                        </button>
                        <button id="sellerMessageSearchButton" type="button" class="sr-only">Search</button>
                    </div>
                </div>

                <div class="border-b border-[#eee8df] bg-[#fbf7ef] px-4 py-4 sm:px-5">
                    <div class="flex items-center gap-3">
                        <div class="relative shrink-0">
                            <div class="grid h-11 w-11 place-items-center rounded-full bg-[#f6efe2] font-bold text-[#9b6b1a]" style="font-size:clamp(10px,.68vw,12px)">SA</div>
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-[#62a278]"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="truncate font-bold text-[#2d2822]" style="font-size:clamp(12px,.78vw,14px)">SARI Admin Support</p>
                                    <p class="mt-0.5 truncate text-[#8f867b]" style="font-size:clamp(10px,.64vw,11.5px)">Platform Support</p>
                                </div>
                                <span class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-[#d79618]"></span>
                            </div>
                            <p id="sellerLastMessagePreview" class="mt-2 line-clamp-2 leading-5 text-[#72695f]" style="font-size:clamp(10px,.67vw,12px)">
                                {{ $messages->last()?->body ?: 'Start a conversation with SARI Admin.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- CENTER: ACTIVE CHAT --}}
            <div id="sellerChatCenterColumn" class="flex min-h-0 min-w-0 flex-col bg-white">
                <div class="flex items-center justify-between gap-4 border-b border-[#ebe5dc] bg-white px-4 py-4 sm:px-5">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="relative shrink-0">
                            <div class="grid h-11 w-11 place-items-center rounded-full bg-[#f6efe2] font-bold text-[#9b6b1a]" style="font-size:clamp(10px,.68vw,12px)">SA</div>
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-[#62a278]"></span>
                        </div>
                        <div class="min-w-0">
                            <p class="truncate font-bold tracking-[-0.02em] text-[#29241f]" style="font-size:clamp(13px,.85vw,15px)">SARI Admin Support</p>
                            <div class="mt-1 flex flex-wrap items-center gap-2">
                                <span class="text-[#8f867b]" style="font-size:clamp(10px,.64vw,11.5px)">Administrator</span>
                                <span class="h-1 w-1 rounded-full bg-[#c8c1b8]"></span>
                                <span id="sellerRealtimeStatus" class="font-semibold text-[#56816a]" style="font-size:clamp(10px,.64vw,11.5px)">Connecting…</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="sellerChatMessages" class="min-h-0 flex-1 overflow-y-auto bg-[#fbfaf7] px-4 py-6 sm:px-6 lg:px-7">
                    <div id="sellerMessageSearchEmpty" class="hidden min-h-[360px] items-center justify-center text-center">
                        <div>
                            <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl border border-[#e7e0d7] bg-white text-[#8f8579] shadow-[0_6px_18px_rgba(35,28,20,.05)]">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="6"></circle><path d="m16 16 4 4"></path></svg>
                            </div>
                            <p class="mt-3 font-bold text-[#4f473e]" style="font-size:clamp(12px,.76vw,14px)">No matching messages</p>
                            <p class="mt-1 text-[#958c80]" style="font-size:clamp(10px,.64vw,12px)">Try another keyword.</p>
                        </div>
                    </div>

                    @if ($messages->isEmpty())
                        <div id="sellerEmptyChat" class="flex h-full min-h-[360px] items-center justify-center text-center">
                            <div>
                                <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#f6efe2] text-[#9d6f22]">
                                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path></svg>
                                </div>
                                <p class="mt-4 font-bold text-[#4f473e]" style="font-size:clamp(12px,.76vw,14px)">Start a conversation</p>
                                <p class="mt-1 text-[#958c80]" style="font-size:clamp(10px,.64vw,12px)">Send a message to SARI Admin below.</p>
                            </div>
                        </div>
                    @endif

                    @foreach ($messages as $message)
                        @php $isBotMessage = (bool) ($message->is_bot ?? false); @endphp
                        <div
                            data-message-id="{{ $message->id }}"
                            data-message-is-bot="{{ $isBotMessage ? 'true' : 'false' }}"
                            data-message-sender-role="{{ $message->sender_role }}"
                            data-message-search="{{ e(strtolower(trim(($message->body ?? '') . ' ' . ($message->attachment_name ?? '') . ' ' . ($message->sender_role ?? '')))) }}"
                            class="mt-6 {{ $message->sender_role === 'seller' ? 'flex justify-end' : 'flex items-end gap-2.5' }}"
                        >
                            @if ($message->sender_role === 'admin')
                                <div class="grid h-8 w-8 shrink-0 place-items-center rounded-full {{ $isBotMessage ? 'bg-[#edf4f8] text-[#59798f]' : 'bg-[#f6efe2] text-[#9b6b1a]' }} font-bold" style="font-size:clamp(9px,.58vw,10.5px)">{{ $isBotMessage ? 'SB' : 'SA' }}</div>
                            @endif

                            <div class="max-w-[84%] sm:max-w-[70%] lg:max-w-[64%]">
                                @if ($message->body)
                                    @if($isBotMessage)
                                        <div class="mb-1.5 font-semibold uppercase tracking-[.08em] text-[#6e899c]" style="font-size:clamp(9px,.58vw,10.5px)">SARI Support Bot</div>
                                    @endif
                                    <div
                                        class="rounded-[14px] px-4 py-3 leading-6 shadow-[0_7px_18px_rgba(35,28,20,.055)] {{ $message->sender_role === 'seller' ? 'rounded-br-[5px] bg-[#cf941b] text-white' : ($isBotMessage ? 'rounded-bl-[5px] border border-[#dce8ef] bg-[#f4f8fa] text-[#526a79]' : 'rounded-bl-[5px] border border-[#e6dfd6] bg-white text-[#514a42]') }}"
                                        style="font-size:clamp(12px,.76vw,14px)"
                                    >
                                        {{ $message->body }}
                                    </div>
                                @endif

                                @if ($message->attachment_path)
                                    <div class="mt-2 overflow-hidden rounded-[14px] border border-[#e6dfd6] bg-white p-2 shadow-[0_7px_18px_rgba(35,28,20,.05)]">
                                        @if ($message->attachmentIsImage())
                                            <a href="{{ route('chat.attachments.show', $message) }}" target="_blank">
                                                <img src="{{ route('chat.attachments.show', $message) }}" alt="{{ $message->attachment_name }}" class="max-h-[300px] w-full rounded-[10px] object-contain">
                                            </a>
                                        @else
                                            <a href="{{ route('chat.attachments.show', $message) }}" target="_blank" class="flex items-center gap-3 rounded-xl bg-[#fbfaf7] p-3">
                                                <div class="grid h-9 w-9 place-items-center rounded-lg bg-[#f5efe4] text-[#a8731f]">
                                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 3h10l4 4v14H5z"></path></svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="truncate font-semibold text-[#50483f]" style="font-size:clamp(10px,.67vw,12px)">{{ $message->attachment_name }}</p>
                                                    <p class="mt-1 text-[#958c80]" style="font-size:clamp(9px,.59vw,11px)">Open attachment</p>
                                                </div>
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                @php
                                    $messageReactionRows = $sellerReactionGroups->get($message->id, collect());
                                    $messageReactionCounts = $messageReactionRows
                                        ->groupBy('emoji')
                                        ->map(fn ($rows) => $rows->count());
                                    $sellerOwnReaction = $messageReactionRows
                                        ->first(fn ($reaction) =>
                                            $reaction->reactor_role === 'seller'
                                            && (int) $reaction->reactor_id === (int) $seller->id
                                        )?->emoji;
                                @endphp

                                <div class="mt-2 flex flex-wrap items-center gap-1.5 {{ $message->sender_role === 'seller' ? 'justify-end' : 'justify-start' }}">
                                    <div data-reaction-summary class="flex flex-wrap items-center gap-1">
                                        @foreach ($messageReactionCounts as $emoji => $count)
                                            <span
                                                class="inline-flex h-7 items-center gap-1 rounded-full border px-2.5 shadow-[0_4px_10px_rgba(35,28,20,.05)] {{ $sellerOwnReaction === $emoji ? 'border-[#dfbb74] bg-[#fff8ea] text-[#8d6218]' : 'border-[#e7e0d7] bg-white text-[#6f675d]' }}"
                                                data-reaction-chip
                                                data-reaction-emoji="{{ $emoji }}"
                                                style="font-size:clamp(9px,.58vw,11px)"
                                            >
                                                <span class="text-[13px] leading-none">{{ $emoji }}</span>
                                                <span class="font-semibold">{{ $count }}</span>
                                            </span>
                                        @endforeach
                                    </div>

                                    <div class="relative">
                                        <button
                                            type="button"
                                            data-reaction-toggle
                                            class="grid h-7 w-7 place-items-center rounded-full border border-[#e5ded4] bg-white text-[#8c8378] shadow-[0_4px_10px_rgba(35,28,20,.05)] transition hover:border-[#d8bd8a] hover:bg-[#fffaf2] hover:text-[#a8731f]"
                                            aria-label="React to message"
                                            title="React"
                                        >
                                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <circle cx="12" cy="12" r="8"></circle>
                                                <path d="M9 10h.01M15 10h.01"></path>
                                                <path d="M8.5 14c1 1.3 2.1 2 3.5 2s2.5-.7 3.5-2"></path>
                                            </svg>
                                        </button>

                                        <div
                                            data-reaction-picker
                                            class="absolute bottom-9 {{ $message->sender_role === 'seller' ? 'right-0' : 'left-0' }} z-30 hidden items-center gap-1 rounded-full border border-[#e5ded4] bg-white p-1.5 shadow-[0_14px_36px_rgba(38,30,20,.14)]"
                                        >
                                            @foreach (['👍', '❤️', '😂', '😮', '😢', '🙏'] as $reactionEmoji)
                                                <button
                                                    type="button"
                                                    data-reaction-emoji="{{ $reactionEmoji }}"
                                                    class="grid h-8 w-8 place-items-center rounded-full text-[17px] transition hover:bg-[#f7f2ea] hover:scale-110 {{ $sellerOwnReaction === $reactionEmoji ? 'bg-[#fff3d9]' : '' }}"
                                                    title="React {{ $reactionEmoji }}"
                                                >{{ $reactionEmoji }}</button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <p
                                        data-message-time
                                        data-sent-at="{{ $message->created_at->toIso8601String() }}"
                                        class="text-[#968d82]"
                                        style="font-size:clamp(9px,.59vw,11px)"
                                        title="{{ $message->created_at->format('M d, Y h:i A') }}"
                                    >
                                        {{ $message->created_at->format('h:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- COMPOSER --}}
                <div id="sellerChatComposer" class="border-t border-[#ebe5dc] bg-white p-3.5 sm:p-4">
                    <div id="sellerChatBlockedNotice" class="{{ $sellerChatBlocked ? 'flex' : 'hidden' }} mb-3 items-start gap-3 rounded-[14px] border border-[#e8cccc] bg-[#fff7f7] px-4 py-3.5">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-[#a65f5f] shadow-sm">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="m8 8 8 8"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-[#8f5050]" style="font-size:clamp(11px,.71vw,13px)">Messaging temporarily unavailable</p>
                            <p id="sellerChatBlockedReason" class="mt-1 leading-5 text-[#9a6d6d]" style="font-size:clamp(10px,.64vw,12px)">{{ $sellerChatRestriction?->block_reason ?: 'SARI Admin has restricted this support conversation.' }}</p>
                        </div>
                    </div>

                    <form id="sellerChatForm" method="POST" action="{{ route('seller.messages.send') }}" enctype="multipart/form-data" class="rounded-[16px] border border-[#e3ddd4] bg-[#fdfcfa] p-3 shadow-[0_6px_18px_rgba(35,28,20,.035)] transition focus-within:border-[#c99a3d] focus-within:bg-white focus-within:ring-4 focus-within:ring-[#c99a3d]/10">
                        @csrf
                        <textarea
                            id="sellerChatInput"
                            name="message"
                            rows="3"
                            @if($sellerChatBlocked) disabled @endif
                            placeholder="{{ $sellerChatBlocked ? 'Messaging is restricted by SARI Admin.' : 'Write a message to SARI Admin...' }}"
                            class="w-full resize-none bg-transparent px-1 py-1 leading-6 text-[#3e3831] outline-none placeholder:text-[#aaa197]"
                            style="font-size:clamp(12px,.76vw,14px)"
                        ></textarea>

                        <div id="sellerAttachmentName" class="mt-2 hidden rounded-xl border border-[#e8e1d7] bg-white px-3 py-2 text-[#62594e]" style="font-size:clamp(10px,.64vw,12px)"></div>

                        <div class="mt-2 flex flex-col gap-3 border-t border-[#eee8df] pt-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-2">
                                <label class="grid h-9 w-9 cursor-pointer place-items-center rounded-lg text-[#756d63] transition hover:bg-[#f5efe5] hover:text-[#a8731f]" title="Attach file">
                                    <input id="sellerChatAttachment" name="attachment" type="file" @if($sellerChatBlocked) disabled @endif accept="image/*,.pdf,.doc,.docx,.zip,.txt" class="hidden">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 12.5 14.5 6a3 3 0 0 1 4.2 4.2l-8 8a5 5 0 0 1-7.1-7.1l8.3-8.3"></path></svg>
                                </label>
                                <span class="text-[#958c80]" style="font-size:clamp(9px,.59vw,11px)">Images/files up to 8 MB</span>
                            </div>

                            <button id="sellerChatSend" type="submit" @if($sellerChatBlocked) disabled @endif class="inline-flex h-10 items-center justify-center gap-2 rounded-[11px] bg-[#cf941b] px-5 font-semibold text-white shadow-[0_7px_16px_rgba(207,148,27,.16)] transition hover:bg-[#b98217] disabled:cursor-not-allowed disabled:opacity-50" style="font-size:clamp(11px,.71vw,13px)">
                                Send Message
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m4 4 17 8-17 8 3-8-3-8Z"></path><path d="M7 12h14"></path></svg>
                            </button>
                        </div>
                    </form>
                    <p id="sellerChatError" class="mt-2 hidden text-[#a45f5f]" style="font-size:clamp(10px,.64vw,12px)"></p>
                </div>
            </div>

            {{-- RIGHT: ACCOUNT DETAILS --}}
            <aside id="sellerChatAccountDetails" class="hidden border-l border-[#ebe5dc] bg-white 2xl:block">
                <div class="border-b border-[#eee8df] px-5 py-4">
                    <p class="font-bold text-[#302a24]" style="font-size:clamp(12px,.78vw,14px)">Account Details</p>
                    <p class="mt-1 text-[#948b7f]" style="font-size:clamp(10px,.64vw,11.5px)">Your seller support context</p>
                </div>

                <div class="p-5">
                    <div class="flex flex-col items-center text-center">
                        <div class="grid h-16 w-16 place-items-center rounded-full bg-[#f4f6f7] font-bold text-[#607a8f]" style="font-size:clamp(14px,.9vw,16px)">{{ strtoupper(substr($seller->store_name ?: 'SS', 0, 2)) }}</div>
                        <p class="mt-3 font-bold text-[#2e2923]" style="font-size:clamp(13px,.84vw,15px)">{{ $seller->store_name ?: 'SARI Seller Store' }}</p>
                        <p class="mt-1 text-[#92897e]" style="font-size:clamp(10px,.64vw,12px)">{{ $seller->email }}</p>
                        <span class="mt-3 rounded-full border border-[#dce5ed] bg-[#f4f7fa] px-2.5 py-1 font-semibold text-[#617d96]" style="font-size:clamp(9px,.58vw,10.5px)">Seller</span>
                    </div>

                    <div class="mt-6 space-y-3">
                        <div class="rounded-[14px] border border-[#e8e2d9] bg-[#fdfcf9] p-4 shadow-[0_5px_14px_rgba(35,28,20,.035)]">
                            <p class="text-[#948b7f]" style="font-size:clamp(10px,.64vw,11.5px)">Account Status</p>
                            <p class="mt-2 font-semibold {{ $seller->isSuspended() ? 'text-[#a96565]' : 'text-[#56816a]' }}" style="font-size:clamp(11px,.71vw,13px)">{{ ucfirst($seller->account_status) }}</p>
                        </div>

                        <div class="rounded-[14px] border border-[#e8e2d9] bg-[#fdfcf9] p-4 shadow-[0_5px_14px_rgba(35,28,20,.035)]">
                            <p class="text-[#948b7f]" style="font-size:clamp(10px,.64vw,11.5px)">Compliance Warnings</p>
                            <p class="mt-2 font-bold text-[#a8731f]" style="font-size:clamp(14px,.92vw,17px)">{{ $seller->warning_count }} / 3</p>
                        </div>

                        <div class="rounded-[14px] border border-[#e8e2d9] bg-[#fdfcf9] p-4 shadow-[0_5px_14px_rgba(35,28,20,.035)]">
                            <p class="text-[#948b7f]" style="font-size:clamp(10px,.64vw,11.5px)">Conversation</p>
                            <p class="mt-2 font-semibold text-[#50483f]" style="font-size:clamp(11px,.71vw,13px)">{{ $stats['total_messages'] }} total messages</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <div class="h-5"></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('livewire:navigated', function () {
    const sellerChatWorkspace = document.getElementById('sellerChatWorkspace');

    if (sellerChatWorkspace) {
        sellerChatWorkspace.classList.remove('seller-chat-ready');

        window.clearTimeout(window.__SARI_SELLER_CHAT_SKELETON_TIMER__);

        window.__SARI_SELLER_CHAT_SKELETON_TIMER__ =
            window.setTimeout(function () {
                sellerChatWorkspace.classList.add('seller-chat-ready');

                const chatMessages = document.getElementById('sellerChatMessages');

                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }, 1600);
    }

    const sellerId = {{ (int) $seller->id }};
    const form = document.getElementById('sellerChatForm');
    const input = document.getElementById('sellerChatInput');
    const attachment = document.getElementById('sellerChatAttachment');
    const attachmentName = document.getElementById('sellerAttachmentName');
    const messages = document.getElementById('sellerChatMessages');
    const sendButton = document.getElementById('sellerChatSend');
    const errorBox = document.getElementById('sellerChatError');
    const status = document.getElementById('sellerRealtimeStatus');
    const preview = document.getElementById('sellerLastMessagePreview');
    const messageSearchInput = document.getElementById('sellerMessageSearch');
    const messageSearchButton = document.getElementById('sellerMessageSearchButton');
    const messageSearchClear = document.getElementById('sellerMessageSearchClear');
    const messageSearchEmpty = document.getElementById('sellerMessageSearchEmpty');
    const sellerMessageBaseUrl = @json(url('/seller/messages'));
    const csrfToken = @json(csrf_token());
    const allowedReactionEmojis = ['👍', '❤️', '😂', '😮', '😢', '🙏'];
    let sellerChatBlocked = @json($sellerChatBlocked);
    const blockedNotice = document.getElementById('sellerChatBlockedNotice');
    const blockedReason = document.getElementById('sellerChatBlockedReason');

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;').replaceAll("'", '&#039;');

    function formatMessageTime(value) {
        if (!value) return '';

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '';

        return new Intl.DateTimeFormat('en-PH', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        }).format(date);
    }

    function formatMessageFullTime(value) {
        if (!value) return '';

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '';

        return new Intl.DateTimeFormat('en-PH', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        }).format(date);
    }

    function hydrateMessageTimes(root = document) {
        root.querySelectorAll?.('[data-message-time]').forEach(function (timeElement) {
            const sentAt = timeElement.dataset.sentAt;
            const formatted = formatMessageTime(sentAt);

            if (formatted) {
                timeElement.textContent = formatted;
                timeElement.title = formatMessageFullTime(sentAt);
            }
        });
    }

    function reactionControlsHtml(messageId) {
        return `
            <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                <div data-reaction-summary class="flex flex-wrap items-center gap-1"></div>
                <div class="relative">
                    <button type="button" data-reaction-toggle class="grid h-6 w-6 place-items-center rounded-full border border-[#e7e0d7] bg-white text-[#8c8378] shadow-sm transition hover:border-[#d8bd8a] hover:bg-[#fffaf2] hover:text-[#a8731f]" aria-label="React to message" title="React">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="8"></circle><path d="M9 10h.01M15 10h.01"></path><path d="M8.5 14c1 1.3 2.1 2 3.5 2s2.5-.7 3.5-2"></path></svg>
                    </button>
                    <div data-reaction-picker class="absolute bottom-8 right-0 z-30 hidden items-center gap-1 rounded-full border border-[#e5ded4] bg-white p-1.5 shadow-[0_14px_36px_rgba(38,30,20,.14)]">
                        ${allowedReactionEmojis.map(emoji => `<button type="button" data-reaction-emoji="${emoji}" class="grid h-8 w-8 place-items-center rounded-full text-[17px] transition hover:bg-[#f7f2ea] hover:scale-110" title="React ${emoji}">${emoji}</button>`).join('')}
                    </div>
                </div>
            </div>`;
    }

    function renderReactionSummary(messageWrapper, reactions = [], mine = null) {
        const summary = messageWrapper?.querySelector('[data-reaction-summary]');
        if (!summary) return;

        summary.innerHTML = reactions.map(function (reaction) {
            const active = mine === reaction.emoji;
            return `
                <span class="inline-flex h-6 items-center gap-1 rounded-full border px-2 text-[8px] shadow-sm ${active ? 'border-[#dfbb74] bg-[#fff8ea] text-[#8d6218]' : 'border-[#e7e0d7] bg-white text-[#6f675d]'}" data-reaction-chip data-reaction-emoji="${escapeHtml(reaction.emoji)}">
                    <span class="text-[12px] leading-none">${escapeHtml(reaction.emoji)}</span>
                    <span class="font-semibold">${Number(reaction.count || 0)}</span>
                </span>`;
        }).join('');

        messageWrapper.querySelectorAll('[data-reaction-picker] [data-reaction-emoji]').forEach(function (button) {
            button.classList.toggle('bg-[#fff3d9]', button.dataset.reactionEmoji === mine);
        });
    }

    function closeReactionPickers(except = null) {
        document.querySelectorAll('[data-reaction-picker]').forEach(function (picker) {
            if (picker === except) return;
            picker.classList.add('hidden');
            picker.classList.remove('flex');
        });
    }

    async function refreshExactMessageTime(messageWrapper) {
        const messageId = messageWrapper?.dataset.messageId;
        const timeElement = messageWrapper?.querySelector('[data-message-time]');
        if (!messageId || !timeElement) return;

        try {
            const response = await fetch(`${sellerMessageBaseUrl}/${messageId}/meta`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) return;

            const data = await response.json();
            if (!data.created_at) return;

            timeElement.dataset.sentAt = data.created_at;
            hydrateMessageTimes(messageWrapper);
        } catch (_) {
            // Keep the near-instant local fallback if metadata endpoint is unavailable.
        }
    }

    hydrateMessageTimes(document);

    function isNearChatBottom(threshold = 120) {
        if (!messages) return true;

        return (
            messages.scrollHeight
            - messages.scrollTop
            - messages.clientHeight
        ) <= threshold;
    }

    function scrollChatToBottom(behavior = 'auto') {
        if (!messages) return;

        messages.scrollTo({
            top: messages.scrollHeight,
            behavior
        });
    }

    scrollChatToBottom('auto');

    function applyMessageSearch() {
        const query = (messageSearchInput?.value || '').trim().toLowerCase();
        const messageItems = Array.from(messages?.querySelectorAll('[data-message-id]') || []);
        const originalEmpty = document.getElementById('sellerEmptyChat');
        let visible = 0;

        messageItems.forEach(function (item) {
            const searchable = (item.dataset.messageSearch || item.textContent || '').toLowerCase();
            const matches = query === '' || searchable.includes(query);
            item.classList.toggle('hidden', !matches);
            if (matches) visible++;
        });

        if (messageSearchClear) {
            messageSearchClear.classList.toggle('hidden', query === '');
            messageSearchClear.classList.toggle('grid', query !== '');
        }

        if (originalEmpty) {
            originalEmpty.classList.toggle('hidden', query !== '');
        }

        if (messageSearchEmpty) {
            const showNoResults = query !== '' && visible === 0;
            messageSearchEmpty.classList.toggle('hidden', !showNoResults);
            messageSearchEmpty.classList.toggle('flex', showNoResults);
        }

        if (query !== '' && visible > 0) {
            const firstVisible = messageItems.find(item => !item.classList.contains('hidden'));
            firstVisible?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    messageSearchButton?.addEventListener('click', applyMessageSearch);

    messageSearchInput?.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            applyMessageSearch();
        }
    });

    messageSearchInput?.addEventListener('input', function () {
        if (this.value.trim() === '') applyMessageSearch();
    });

    messageSearchClear?.addEventListener('click', function () {
        if (messageSearchInput) messageSearchInput.value = '';
        applyMessageSearch();
        messageSearchInput?.focus();
    });

    attachment?.addEventListener('change', function () {
        const file = this.files?.[0];
        attachmentName.classList.toggle('hidden', !file);
        attachmentName.textContent = file ? `Attached: ${file.name}` : '';
    });

    function lockSellerComposer(reason = '') {
        sellerChatBlocked = true;
        if (input) input.disabled = true;
        if (attachment) attachment.disabled = true;
        if (sendButton) sendButton.disabled = true;
        if (input) input.placeholder = 'Messaging is restricted by SARI Admin.';
        blockedNotice?.classList.remove('hidden');
        blockedNotice?.classList.add('flex');
        if (blockedReason && reason) blockedReason.textContent = reason;
    }

    function appendMessage(data) {
        if (!data || Number(data.seller_id) !== sellerId) return;
        if (document.querySelector(`[data-message-id="${data.id}"]`)) return;

        document.getElementById('sellerEmptyChat')?.remove();

        const mine = data.sender_role === 'seller';
        const isBot = Boolean(data.is_bot);
        const wrapper = document.createElement('div');
        wrapper.dataset.messageId = data.id;
        wrapper.dataset.messageSenderRole = data.sender_role || '';
        wrapper.dataset.messageIsBot = isBot ? 'true' : 'false';
        wrapper.dataset.messageSearch = `${data.body || ''} ${data.attachment_name || ''} ${data.sender_role || ''}`.toLowerCase();
        wrapper.className = `mt-5 ${mine ? 'flex justify-end' : 'flex items-end gap-2.5'}`;

        let attachmentHtml = '';
        if (data.attachment_url) {
            if ((data.attachment_mime || '').startsWith('image/')) {
                attachmentHtml = `<div class="mt-2 overflow-hidden rounded-[14px] border border-[#e9e2d9] bg-white p-2"><a href="${escapeHtml(data.attachment_url)}" target="_blank"><img src="${escapeHtml(data.attachment_url)}" alt="${escapeHtml(data.attachment_name)}" class="max-h-[260px] w-full rounded-xl object-contain"></a></div>`;
            } else {
                attachmentHtml = `<div class="mt-2 rounded-[14px] border border-[#e9e2d9] bg-white p-2"><a href="${escapeHtml(data.attachment_url)}" target="_blank" class="block rounded-xl bg-[#fcfaf7] p-3 text-[9px] font-semibold text-[#50483f]">${escapeHtml(data.attachment_name || 'Attachment')}</a></div>`;
            }
        }

        wrapper.innerHTML = `
            ${mine ? '' : `<div class="grid h-8 w-8 shrink-0 place-items-center rounded-full ${isBot ? 'bg-[#edf4f8] text-[#59798f]' : 'bg-[#fbf5e9] text-[#ad791f]'} text-[8px] font-bold">${isBot ? 'SB' : 'SA'}</div>`}
            <div class="max-w-[82%] sm:max-w-[68%]">
                ${isBot ? '<div class="mb-1.5 text-[8px] font-semibold uppercase tracking-[.08em] text-[#6e899c]">SARI Support Bot</div>' : ''}
                ${data.body ? `<div class="rounded-[16px] px-4 py-3 text-[10.5px] leading-5 shadow-sm ${mine ? 'rounded-br-[5px] bg-[#c99128] text-white' : (isBot ? 'rounded-bl-[5px] border border-[#dce8ef] bg-[#f4f8fa] text-[#526a79]' : 'rounded-bl-[5px] border border-[#e9e2d9] bg-white text-[#5f574d]')}">${escapeHtml(data.body)}</div>` : ''}
                ${attachmentHtml}
                <div class="mt-1.5 flex flex-wrap items-center gap-1.5 ${mine ? 'justify-end' : 'justify-start'}">
                    <div data-reaction-summary class="flex flex-wrap items-center gap-1"></div>
                    <div class="relative">
                        <button type="button" data-reaction-toggle class="grid h-6 w-6 place-items-center rounded-full border border-[#e7e0d7] bg-white text-[#8c8378] shadow-sm transition hover:border-[#d8bd8a] hover:bg-[#fffaf2] hover:text-[#a8731f]" aria-label="React to message" title="React">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="8"></circle><path d="M9 10h.01M15 10h.01"></path><path d="M8.5 14c1 1.3 2.1 2 3.5 2s2.5-.7 3.5-2"></path></svg>
                        </button>
                        <div data-reaction-picker class="absolute bottom-8 ${mine ? 'right-0' : 'left-0'} z-30 hidden items-center gap-1 rounded-full border border-[#e5ded4] bg-white p-1.5 shadow-[0_14px_36px_rgba(38,30,20,.14)]">
                            ${allowedReactionEmojis.map(emoji => `<button type="button" data-reaction-emoji="${emoji}" class="grid h-8 w-8 place-items-center rounded-full text-[17px] transition hover:bg-[#f7f2ea] hover:scale-110" title="React ${emoji}">${emoji}</button>`).join('')}
                        </div>
                    </div>
                    <p data-message-time data-sent-at="${escapeHtml(data.created_at || data.created_at_iso || data.sent_at || new Date().toISOString())}" class="text-[8.5px] text-[#9c9388]"></p>
                </div>
            </div>`;

        messages.appendChild(wrapper);
        hydrateMessageTimes(wrapper);

        if (!(data.created_at || data.created_at_iso || data.sent_at)) {
            refreshExactMessageTime(wrapper);
        }

        if (preview) preview.textContent = data.body || data.attachment_name || 'Attachment';

        if ((messageSearchInput?.value || '').trim() !== '') {
            applyMessageSearch();
        } else {
            const shouldFollowLatest = mine || isNearChatBottom(160);

            if (shouldFollowLatest) {
                window.requestAnimationFrame(function () {
                    scrollChatToBottom('smooth');
                });
            }
        }

        if (!mine) {
            fetch(@json(route('seller.messages.read')), {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json'}
            }).catch(() => {});
        }
    }

    messages?.addEventListener('click', async function (event) {
        const toggle = event.target.closest('[data-reaction-toggle]');

        if (toggle) {
            event.preventDefault();
            const picker = toggle.parentElement?.querySelector('[data-reaction-picker]');
            if (!picker) return;

            const willOpen = picker.classList.contains('hidden');
            closeReactionPickers(picker);
            picker.classList.toggle('hidden', !willOpen);
            picker.classList.toggle('flex', willOpen);
            return;
        }

        const reactionButton = event.target.closest('[data-reaction-picker] [data-reaction-emoji]');
        if (!reactionButton) return;

        event.preventDefault();

        const wrapper = reactionButton.closest('[data-message-id]');
        const messageId = wrapper?.dataset.messageId;
        const emoji = reactionButton.dataset.reactionEmoji;

        // IMPORTANT:
        // Do NOT authorize reactions by sender_role here.
        // Older/admin-side messages may use a legacy role label such as
        // "administrator" or a differently-cased value. The backend verifies
        // that the message belongs to this seller conversation by seller_account_id.
        if (!messageId || !allowedReactionEmojis.includes(emoji)) {
            return;
        }

        reactionButton.disabled = true;

        try {
            const response = await fetch(`${sellerMessageBaseUrl}/${messageId}/reaction`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ emoji })
            });

            const raw = await response.text();
            let data = {};

            try {
                data = raw ? JSON.parse(raw) : {};
            } catch (_) {
                data = {};
            }

            if (!response.ok) {
                const fallbackMessage = response.status === 419
                    ? 'Session expired. Refresh the page and try again.'
                    : response.status === 403
                        ? 'This message is not available in your seller conversation.'
                        : `Unable to save reaction (${response.status}).`;

                throw new Error(data.message || fallbackMessage);
            }

            renderReactionSummary(wrapper, data.reactions || [], data.mine || null);
            closeReactionPickers();
        } catch (error) {
            errorBox.textContent = error.message || 'Unable to save reaction. Please try again.';
            errorBox.classList.remove('hidden');
        } finally {
            reactionButton.disabled = false;
        }
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('[data-reaction-toggle], [data-reaction-picker]')) {
            closeReactionPickers();
        }
    });

    form?.addEventListener('submit', async function (event) {
        event.preventDefault();
        errorBox.classList.add('hidden');

        if (sellerChatBlocked) {
            lockSellerComposer(blockedReason?.textContent || 'Messaging is restricted by SARI Admin.');
            return;
        }

        sendButton.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {'Accept': 'application/json'}
            });

            const raw = await response.text();
            let data = {};
            try { data = raw ? JSON.parse(raw) : {}; } catch (_) {}

            if (!response.ok) {
                if (response.status === 423 || data.blocked) {
                    lockSellerComposer(data.reason || data.message || 'Messaging is restricted by SARI Admin.');
                }
                throw new Error(data.message || Object.values(data.errors || {})?.[0]?.[0] || 'Unable to send message.');
            }

            appendMessage(data.message);
            if (data.bot_message) appendMessage(data.bot_message);

            form.reset();
            attachmentName.classList.add('hidden');
            attachmentName.textContent = '';
            input.focus();
        } catch (error) {
            errorBox.textContent = error.message || 'Unable to send message.';
            errorBox.classList.remove('hidden');
        } finally {
            if (!sellerChatBlocked) sendButton.disabled = false;
        }
    });

    /*
    |--------------------------------------------------------------------------
    | Realtime chat events come from the persistent Seller shell.
    |--------------------------------------------------------------------------
    */
    const sellerChatPageAbort = new AbortController();

    window.addEventListener(
        'sari:seller-chat-message',
        function (customEvent) {
            appendMessage(customEvent.detail);
        },
        { signal: sellerChatPageAbort.signal }
    );

    document.addEventListener('livewire:navigating', function () {
        window.clearTimeout(window.__SARI_SELLER_CHAT_SKELETON_TIMER__);
        window.__SARI_SELLER_CHAT_SKELETON_TIMER__ = null;
        sellerChatPageAbort.abort();
    }, { once: true });

    function syncSellerChatRealtimeStatus(attempt = 0) {
        if (window.Echo) {
            status.textContent = 'Live';
            status.className = 'font-semibold text-[#56816a]';
            return;
        }

        if (attempt < 20) {
            window.setTimeout(function () {
                syncSellerChatRealtimeStatus(attempt + 1);
            }, 250);
            return;
        }

        status.textContent = 'Saved mode';
        status.className = 'rounded-full border border-[#eadfc9] bg-[#fffaf2] px-3 py-1.5 text-[8px] font-semibold text-[#a8731f]';
    }

    syncSellerChatRealtimeStatus();
}, { once: true });
</script>
@endpush
