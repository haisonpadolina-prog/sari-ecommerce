@extends('layouts.seller')

@section('title', 'Chat / Messaging — SARI Seller')
@section('page-title', 'Chat / Messaging')

@section('content')
@php
    $sellerChatRestriction = null;
    $sellerChatBlocked = false;

    if (\Illuminate\Support\Facades\Schema::hasTable('seller_chat_restrictions')) {
        $sellerChatRestriction = \App\Models\SellerChatRestriction::query()
            ->where('seller_account_id', $seller->id)
            ->first();
        $sellerChatBlocked = (bool) ($sellerChatRestriction?->is_blocked);
    }
@endphp

<style>
    #sellerPlatformChat {
        height: calc(100dvh - 150px);
        min-height: 620px;
        max-height: 900px;
    }

    #sellerPlatformChat .chat-grid { height: 100%; min-height: 0; }
    #sellerConversationPane,
    #sellerCenterPane,
    #sellerDetailsPane { min-height: 0; }
    #sellerConversationPane,
    #sellerDetailsPane { overflow-y: auto; overscroll-behavior: contain; }
    #sellerCenterPane { height: 100%; overflow: hidden; }
    #sellerPlatformMessages {
        min-height: 0;
        overflow-y: auto;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: #d8c7a7 transparent;
    }
    #sellerPlatformMessages::-webkit-scrollbar,
    #sellerConversationPane::-webkit-scrollbar,
    #sellerDetailsPane::-webkit-scrollbar { width: 6px; }
    #sellerPlatformMessages::-webkit-scrollbar-track,
    #sellerConversationPane::-webkit-scrollbar-track,
    #sellerDetailsPane::-webkit-scrollbar-track { background: transparent; }
    #sellerPlatformMessages::-webkit-scrollbar-thumb,
    #sellerConversationPane::-webkit-scrollbar-thumb,
    #sellerDetailsPane::-webkit-scrollbar-thumb {
        background: #d8c7a7;
        border-radius: 999px;
    }

    .seller-platform-composer {
        position: relative;
        z-index: 20;
        flex: 0 0 auto;
        background: #fff;
        box-shadow: 0 -8px 24px rgba(35, 28, 20, .035);
    }

    .seller-platform-status {
        display: inline-flex;
        height: 24px;
        align-items: center;
        gap: 6px;
        border: 1px solid #dce7df;
        border-radius: 999px;
        background: #f6f9f6;
        padding: 0 9px;
        color: #617568;
        font-size: 10px;
        font-weight: 700;
    }

    .seller-platform-status::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: #4f9a69;
    }

    .seller-platform-ai {
        display: inline-flex;
        height: 24px;
        align-items: center;
        gap: 6px;
        border: 1px solid #e4e8ec;
        border-radius: 999px;
        background: #f7f9fb;
        padding: 0 9px;
        color: #647888;
        font-size: 10px;
        font-weight: 700;
    }

    .seller-platform-ai svg { width: 12px; height: 12px; }

    .seller-message-row { margin-top: 18px; }
    .seller-message-bubble { white-space: pre-wrap; word-break: break-word; }

    .seller-platform-loading {
        position: absolute;
        inset: 0;
        z-index: 40;
        display: grid;
        place-items: center;
        background: rgba(255,255,255,.96);
    }

    #sellerPlatformChat[data-ready="1"] .seller-platform-loading { display: none; }


    /* Compact composer sizing for 100% zoom */
    #sellerPlatformMessageForm {
        border-radius: 14px;
        padding: 10px;
    }

    #sellerPlatformInput {
        min-height: 54px;
        max-height: 112px;
        font-size: 12px;
        line-height: 1.45;
    }

    #sellerAttachmentName {
        margin-top: 8px;
        padding: 8px 10px;
        font-size: 10px;
    }

    .seller-composer-actions {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        gap: 10px;
        margin-top: 8px;
        border-top: 1px solid #eee8df;
        padding-top: 10px;
    }

    .seller-composer-attachment {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .seller-composer-attachment-label {
        display: grid;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        place-items: center;
        border-radius: 10px;
    }

    .seller-composer-attachment-label svg {
        width: 14px;
        height: 14px;
    }

    .seller-composer-attachment-text {
        min-width: 0;
        font-size: 9px;
        line-height: 1.3;
        color: #958c80;
    }

    #sellerPlatformSend {
        height: 36px;
        padding-left: 14px;
        padding-right: 14px;
        border-radius: 10px;
        font-size: 11px;
        gap: 6px;
        flex-shrink: 0;
    }

    #sellerPlatformSend svg {
        width: 13px;
        height: 13px;
    }

    @media (max-width: 640px) {
        .seller-platform-composer {
            padding: 10px !important;
        }

        #sellerPlatformMessageForm {
            padding: 9px;
        }

        #sellerPlatformInput {
            min-height: 48px;
            max-height: 96px;
            font-size: 11.5px;
        }

        .seller-composer-actions {
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 8px;
        }

        .seller-composer-attachment-text {
            font-size: 8.8px;
        }

        #sellerPlatformSend {
            height: 34px;
            padding-left: 12px;
            padding-right: 12px;
            font-size: 10.5px;
        }
    }

    @media (max-width: 1023px) {
        #sellerPlatformChat {
            height: calc(100dvh - 132px);
            min-height: 560px;
            max-height: none;
        }
    }

    @media (max-width: 639px) {
        #sellerPlatformChat {
            height: calc(100dvh - 118px);
            min-height: 520px;
        }
    }


    /* SARI Assistant identity + typing presence */
    .sari-bot-avatar {
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        border: 1px solid #dce8ef;
        border-radius: 999px;
        background: #f2f7fa;
        color: #5d788b;
        box-shadow: 0 4px 12px rgba(52, 78, 96, .06);
    }

    .sari-bot-avatar svg {
        width: 58%;
        height: 58%;
    }

    .sari-assistant-typing {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    .sari-assistant-typing__bubble {
        min-width: 72px;
        border: 1px solid #dce8ef;
        border-radius: 14px 14px 14px 5px;
        background: #f4f8fa;
        padding: 11px 14px;
        box-shadow: 0 7px 18px rgba(35, 28, 20, .045);
    }

    .sari-assistant-typing__dots {
        display: flex;
        align-items: center;
        gap: 4px;
        height: 10px;
    }

    .sari-assistant-typing__dots span {
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: #718896;
        animation: sariAssistantTypingDot 1.05s ease-in-out infinite;
    }

    .sari-assistant-typing__dots span:nth-child(2) { animation-delay: .14s; }
    .sari-assistant-typing__dots span:nth-child(3) { animation-delay: .28s; }

    @keyframes sariAssistantTypingDot {
        0%, 60%, 100% { transform: translateY(0); opacity: .42; }
        30% { transform: translateY(-3px); opacity: 1; }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-assistant-typing__dots span { animation: none !important; opacity: .8; }
    }


    /* ============================================================
       FULL-WORKSPACE MESSAGING
       Flat Messenger-style layout: no floating outer card/container.
       ============================================================ */
    .seller-platform-page {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0;
    }

    #sellerPlatformChat {
        width: 100%;
        height: calc(100dvh - 112px);
        min-height: 560px;
        max-height: none;
        overflow: hidden;
        border: 0 !important;
        border-radius: 0 !important;
        background: #fbfaf7 !important;
        box-shadow: none !important;
    }

    #sellerPlatformChat .chat-grid {
        width: 100%;
        height: 100%;
        min-height: 0;
        background: #fbfaf7;
    }

    #sellerConversationPane {
        background: #fff !important;
        border-color: #ece5dc !important;
    }

    #sellerCenterPane {
        min-width: 0;
        background: #fbfaf7 !important;
    }

    #sellerDetailsPane {
        background: #fff !important;
        border-color: #ece5dc !important;
    }

    #sellerPlatformMessages {
        background: #fbfaf7 !important;
        padding-top: 18px !important;
        padding-bottom: 18px !important;
    }

    /* Integrated thread header — part of the workspace, not another card. */
    #sellerCenterPane > div:first-child {
        min-height: 64px;
        padding-top: 10px !important;
        padding-bottom: 10px !important;
        background: #fff !important;
        border-color: #ece5dc !important;
    }

    /* Bottom composer stays attached to the conversation surface. */
    .seller-platform-composer {
        border-color: #ece5dc !important;
        background: #fff !important;
        box-shadow: 0 -6px 18px rgba(35, 28, 20, .025) !important;
    }

    #sellerPlatformMessageForm {
        border: 1px solid #e7dfd5 !important;
        border-radius: 13px !important;
        background: #fbfaf7 !important;
        box-shadow: none !important;
    }

    #sellerPlatformMessageForm:focus-within {
        border-color: #d3aa59 !important;
        background: #fff !important;
        box-shadow: 0 0 0 3px rgba(201, 145, 40, .07) !important;
    }

    /* Let the actual conversation be the dominant background. */
    .seller-message-row {
        margin-top: 14px;
    }

    .seller-message-bubble {
        box-shadow: none !important;
    }

    /* Do not block first paint with a full-page "connecting" cover. */
    .seller-platform-loading {
        display: none !important;
    }

    @media (min-width: 1536px) {
        #sellerPlatformChat .chat-grid {
            grid-template-columns: 260px minmax(0, 1fr) 260px !important;
        }
    }

    @media (min-width: 1024px) and (max-width: 1535px) {
        #sellerPlatformChat .chat-grid {
            grid-template-columns: 250px minmax(0, 1fr) !important;
        }
    }

    @media (max-width: 1023px) {
        #sellerPlatformChat {
            height: calc(100dvh - 104px);
            min-height: 520px;
        }
    }

    @media (max-width: 639px) {
        #sellerPlatformChat {
            height: calc(100dvh - 92px);
            min-height: 500px;
        }

        #sellerPlatformMessages {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
    }


    /* ============================================================
       FULL-BLEED SELLER CONTENT
       The seller layout wraps every page in responsive padding:
       px-4/6/8/10 + py-5/6/7. Cancel that padding only for chat
       so the workspace touches the header, right edge, and bottom.
       ============================================================ */
    .seller-platform-page {
        margin: -20px -16px;
        width: calc(100% + 32px);
        max-width: none !important;
    }

    #sellerPlatformChat {
        height: calc(100dvh - 86px) !important;
        min-height: 0 !important;
        max-height: none !important;
    }

    @media (min-width: 640px) {
        .seller-platform-page {
            margin: -24px;
            width: calc(100% + 48px);
        }
    }

    @media (min-width: 1024px) {
        .seller-platform-page {
            margin-top: -28px;
            margin-bottom: -28px;
            margin-left: -32px;
            margin-right: -32px;
            width: calc(100% + 64px);
        }

        #sellerPlatformChat {
            height: calc(100dvh - 86px) !important;
        }
    }

    @media (min-width: 1280px) {
        .seller-platform-page {
            margin-left: -40px;
            margin-right: -40px;
            width: calc(100% + 80px);
        }
    }

    @media (max-width: 639px) {
        #sellerPlatformChat {
            height: calc(100dvh - 86px) !important;
        }
    }


    /* ============================================================
       COMPACT MESSENGER-STYLE COMPOSER
       One horizontal row: attach | message | send.
       ============================================================ */
    .seller-platform-composer {
        padding: 10px 14px !important;
    }

    #sellerPlatformMessageForm {
        display: flex !important;
        min-height: 54px !important;
        align-items: center !important;
        gap: 8px !important;
        border: 1px solid #e6ded4 !important;
        border-radius: 16px !important;
        background: #fff !important;
        padding: 6px 8px !important;
        box-shadow: 0 3px 12px rgba(35, 28, 20, .035) !important;
    }

    #sellerPlatformMessageForm:focus-within {
        border-color: #d9c49a !important;
        box-shadow: 0 0 0 3px rgba(201, 145, 40, .06) !important;
    }

    #sellerPlatformInput {
        min-width: 0 !important;
        min-height: 38px !important;
        max-height: 76px !important;
        flex: 1 1 auto !important;
        padding: 9px 6px !important;
        border: 0 !important;
        background: transparent !important;
        font-size: 12px !important;
        line-height: 20px !important;
        overflow-y: auto !important;
    }

    #sellerAttachmentName {
        position: absolute !important;
        left: 14px !important;
        bottom: calc(100% + 7px) !important;
        z-index: 30 !important;
        max-width: min(420px, 70vw) !important;
        margin: 0 !important;
        border: 1px solid #e7dfd5 !important;
        border-radius: 9px !important;
        background: #fff !important;
        padding: 6px 9px !important;
        color: #655d54 !important;
        font-size: 8px !important;
        line-height: 1.3 !important;
        box-shadow: 0 7px 18px rgba(35, 28, 20, .07) !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    .seller-composer-actions {
        display: contents !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
    }

    .seller-composer-attachment {
        display: contents !important;
    }

    .seller-composer-attachment-label {
        display: grid !important;
        width: 38px !important;
        height: 38px !important;
        flex: 0 0 38px !important;
        place-items: center !important;
        border: 1px solid #e8e0d6 !important;
        border-radius: 11px !important;
        background: #faf8f5 !important;
        color: #7e756b !important;
    }

    .seller-composer-attachment-label:hover {
        border-color: #dac59f !important;
        background: #fff8eb !important;
        color: #a8731f !important;
    }

    .seller-composer-attachment-label svg {
        width: 15px !important;
        height: 15px !important;
    }

    .seller-composer-attachment-text {
        display: none !important;
    }

    #sellerPlatformSend {
        display: grid !important;
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        flex: 0 0 40px !important;
        place-items: center !important;
        gap: 0 !important;
        border-radius: 12px !important;
        padding: 0 !important;
        background: #d39412 !important;
        box-shadow: 0 5px 12px rgba(201, 145, 40, .15) !important;
        font-size: 0 !important;
    }

    #sellerPlatformSend:hover {
        background: #bd8209 !important;
    }

    #sellerPlatformSend svg {
        width: 16px !important;
        height: 16px !important;
        margin: 0 !important;
    }

    #sellerPlatformError {
        margin-top: 5px !important;
        font-size: 8px !important;
    }

    @media (max-width: 639px) {
        .seller-platform-composer {
            padding: 7px 8px !important;
        }

        #sellerPlatformMessageForm {
            min-height: 50px !important;
            gap: 6px !important;
            border-radius: 14px !important;
            padding: 5px 6px !important;
        }

        .seller-composer-attachment-label {
            width: 36px !important;
            height: 36px !important;
            flex-basis: 36px !important;
        }

        #sellerPlatformSend {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            flex-basis: 38px !important;
        }

        #sellerPlatformInput {
            min-height: 36px !important;
            padding: 8px 4px !important;
            font-size: 11.5px !important;
        }
    }

</style>

<div class="seller-platform-page">
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

    <section
        id="sellerPlatformChat"
        data-ready="0"
        class="relative overflow-hidden bg-[#fbfaf7]"
    >
        <div class="seller-platform-loading">
            <div class="text-center">
                <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#f6efe2] text-[#9b6b1a]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5 animate-pulse" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                    </svg>
                </div>
                <p class="mt-3 text-[11px] font-semibold text-[#655d54]">Connecting to SARI Support…</p>
            </div>
        </div>

        <div class="chat-grid grid grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] 2xl:grid-cols-[280px_minmax(0,1fr)_280px]">
            {{-- LEFT: SUPPORT CONVERSATION --}}
            <aside id="sellerConversationPane" class="border-b border-[#ebe5dc] bg-white lg:border-b-0 lg:border-r">
                <div class="border-b border-[#eee8df] px-4 py-4 sm:px-5">
                    <h3 class="font-bold tracking-[-0.02em] text-[#24201b]" style="font-size:clamp(14px,.88vw,16px)">Conversations</h3>
                    <p class="mt-1 text-[#938a7f]" style="font-size:clamp(10px,.66vw,12px)">SARI platform support</p>

                    <div class="relative mt-4">
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9b9287]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="6.5"></circle><path d="m16 16 4 4"></path>
                        </svg>
                        <input id="sellerMessageSearch" type="search" autocomplete="off" placeholder="Search messages..." class="h-11 w-full rounded-[12px] border border-[#e3ddd4] bg-[#fbfaf8] pl-10 pr-10 text-[12px] text-[#3d3730] outline-none transition placeholder:text-[#aaa197] focus:border-[#c99128] focus:bg-white focus:ring-4 focus:ring-[#c99128]/10">
                        <button id="sellerMessageSearchClear" type="button" class="absolute right-2.5 top-1/2 hidden h-7 w-7 -translate-y-1/2 place-items-center rounded-lg text-[#91887d] transition hover:bg-[#f1ede7] hover:text-[#4d463e]" aria-label="Clear search">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m7 7 10 10"></path><path d="m17 7-10 10"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="border-l-2 border-[#c99128] bg-[#fbf7ef] px-4 py-4 sm:px-5">
                    <div class="flex items-center gap-3">
                        <div class="relative shrink-0">
                            <div class="grid h-11 w-11 place-items-center rounded-full bg-[#f6efe2] text-[11px] font-bold text-[#9b6b1a]">SA</div>
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-[#62a278]"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[13px] font-bold text-[#2d2822]">SARI Admin Support</p>
                            <p class="mt-0.5 truncate text-[10px] text-[#8f867b]">Administrator + SARI Assistant</p>
                            <p id="sellerLastMessagePreview" class="mt-2 line-clamp-2 text-[11px] leading-5 text-[#72695f]">Loading conversation…</p>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- CENTER: PLATFORM SUPPORT THREAD --}}
            <div id="sellerCenterPane" class="flex min-h-0 min-w-0 flex-col bg-white">
                <div class="flex items-center justify-between gap-4 border-b border-[#ebe5dc] bg-white px-4 py-4 sm:px-5">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="relative shrink-0">
                            <div class="grid h-11 w-11 place-items-center rounded-full bg-[#f6efe2] text-[11px] font-bold text-[#9b6b1a]">SA</div>
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-[#62a278]"></span>
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-[14px] font-bold tracking-[-0.02em] text-[#29241f]">SARI Admin Support</p>
                            <p class="mt-1 text-[10px] text-[#8f867b]">Official seller support conversation</p>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <span id="sellerRealtimeStatus" class="seller-platform-status">Connecting</span>
                        <span class="seller-platform-ai" title="SARI Assistant can provide a temporary first response while Admin is unavailable.">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v3M12 18v3M3 12h3M18 12h3"></path><rect x="7" y="7" width="10" height="10" rx="3"></rect><path d="M10 11h.01M14 11h.01M10 14h4"></path></svg>
                            AI support
                        </span>
                    </div>
                </div>

                <div id="sellerPlatformMessages" class="min-h-0 flex-1 overflow-y-auto bg-[#fbfaf7] px-4 py-6 sm:px-6 lg:px-7">
                    <div id="sellerEmptyChat" class="flex h-full min-h-[360px] items-center justify-center text-center">
                        <div>
                            <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#f6efe2] text-[#9d6f22]">
                                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path></svg>
                            </div>
                            <p class="mt-4 text-[13px] font-bold text-[#4f473e]">Start a conversation</p>
                            <p class="mt-1 text-[11px] text-[#958c80]">Send a message to SARI Admin. SARI Assistant may reply while Admin is unavailable.</p>
                        </div>
                    </div>
                </div>

                <div class="seller-platform-composer border-t border-[#ebe5dc] bg-white p-3 sm:p-3.5">
                    <div id="sellerChatBlockedNotice" class="{{ $sellerChatBlocked ? 'flex' : 'hidden' }} mb-3 items-start gap-3 rounded-[14px] border border-[#e8cccc] bg-[#fff7f7] px-4 py-3">
                        <div class="grid h-8 w-8 shrink-0 place-items-center rounded-[10px] bg-white text-[#a65f5f] shadow-sm">
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="m8 8 8 8"></path></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-[#8f5050]">Messaging temporarily unavailable</p>
                            <p id="sellerChatBlockedReason" class="mt-1 text-[9.5px] leading-5 text-[#9a6d6d]">{{ $sellerChatRestriction?->block_reason ?: 'SARI Admin has restricted this support conversation.' }}</p>
                        </div>
                    </div>

                    <form id="sellerPlatformMessageForm" class="rounded-[16px] border border-[#e3ddd4] bg-[#fdfcfa] p-3 shadow-[0_6px_18px_rgba(35,28,20,.035)] transition focus-within:border-[#c99a3d] focus-within:bg-white focus-within:ring-4 focus-within:ring-[#c99a3d]/10">
                        @csrf
                        <textarea id="sellerPlatformInput" rows="1" @if($sellerChatBlocked) disabled @endif placeholder="{{ $sellerChatBlocked ? 'Messaging is restricted by SARI Admin.' : 'Message...' }}" class="w-full resize-none bg-transparent px-1 py-1 text-[12px] leading-[1.45] text-[#3e3831] outline-none placeholder:text-[#aaa197]"></textarea>

                        <div id="sellerAttachmentName" class="mt-2 hidden rounded-xl border border-[#e8e1d7] bg-white px-3 py-2 text-[10px] text-[#62594e]"></div>

                        <div class="seller-composer-actions">
                            <div class="seller-composer-attachment">
                                <label class="seller-composer-attachment-label cursor-pointer border border-transparent text-[#756d63] transition hover:border-[#e8dfd0] hover:bg-[#f8f4ed] hover:text-[#a8731f]" title="Attach file">
                                    <input id="sellerPlatformAttachment" type="file" @if($sellerChatBlocked) disabled @endif accept="image/*,.pdf,.txt,.csv,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.mp4,.mov,.mp3,.m4a,.wav" class="hidden">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 12.5 14.5 6a3 3 0 0 1 4.2 4.2l-8 8a5 5 0 0 1-7.1-7.1l8.3-8.3"></path></svg>
                                </label>
                                <span class="seller-composer-attachment-text">Images/files up to 15 MB</span>
                            </div>

                            <button id="sellerPlatformSend" type="submit" @if($sellerChatBlocked) disabled @endif class="disabled:cursor-not-allowed disabled:opacity-50" title="Send message" aria-label="Send message">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m4 4 17 8-17 8 3-8-3-8Z"></path><path d="M7 12h14"></path></svg>
                            </button>
                        </div>
                    </form>
                    <p id="sellerPlatformError" class="mt-2 hidden text-[10px] text-[#a45f5f]"></p>
                </div>
            </div>

            {{-- RIGHT: SELLER CONTEXT --}}
            <aside id="sellerDetailsPane" class="hidden border-l border-[#ebe5dc] bg-white 2xl:block">
                <div class="border-b border-[#eee8df] px-5 py-4">
                    <p class="text-[13px] font-bold text-[#302a24]">Account Details</p>
                    <p class="mt-1 text-[10px] text-[#948b7f]">Your seller support context</p>
                </div>

                <div class="p-5">
                    <div class="flex flex-col items-center text-center">
                        <div class="grid h-16 w-16 place-items-center rounded-full bg-[#f4f6f7] text-[15px] font-bold text-[#607a8f]">{{ strtoupper(substr($seller->store_name ?: 'SS', 0, 2)) }}</div>
                        <p class="mt-3 text-[14px] font-bold text-[#2e2923]">{{ $seller->store_name ?: 'SARI Seller Store' }}</p>
                        <p class="mt-1 text-[11px] text-[#92897e]">{{ $seller->email }}</p>
                        <span class="mt-3 rounded-full border border-[#dce5ed] bg-[#f4f7fa] px-2.5 py-1 text-[10px] font-semibold text-[#617d96]">Seller</span>
                    </div>

                    <div class="mt-6 space-y-3">
                        <div class="rounded-[14px] border border-[#e8e2d9] bg-[#fdfcf9] p-4">
                            <p class="text-[10px] text-[#948b7f]">Account Status</p>
                            <p class="mt-2 text-[12px] font-semibold {{ $seller->isSuspended() ? 'text-[#a96565]' : 'text-[#56816a]' }}">{{ ucfirst($seller->account_status ?: 'active') }}</p>
                        </div>

                        <div class="rounded-[14px] border border-[#e8e2d9] bg-[#fdfcf9] p-4">
                            <p class="text-[10px] text-[#948b7f]">Compliance Warnings</p>
                            <p class="mt-2 text-[16px] font-bold text-[#a8731f]">{{ (int) ($seller->warning_count ?? 0) }} / 3</p>
                        </div>

                        <div class="rounded-[14px] border border-[#e8e2d9] bg-[#fdfcf9] p-4">
                            <p class="text-[10px] text-[#948b7f]">Conversation</p>
                            <p class="mt-2 text-[12px] font-semibold text-[#50483f]"><span id="sellerTotalMessages">0</span> total messages</p>
                        </div>

                        <div class="rounded-[14px] border border-[#dfe7ec] bg-[#f7fafc] p-4">
                            <div class="flex items-start gap-3">
                                <div class="sari-bot-avatar h-9 w-9">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 5V3"></path><circle cx="12" cy="2.5" r=".7" fill="currentColor" stroke="none"></circle>
                                        <rect x="5.5" y="6" width="13" height="11" rx="4"></rect>
                                        <path d="M8 17v2M16 17v2M5.5 10H4M20 10h-1.5"></path>
                                        <circle cx="9.5" cy="11" r="1" fill="currentColor" stroke="none"></circle>
                                        <circle cx="14.5" cy="11" r="1" fill="currentColor" stroke="none"></circle>
                                        <path d="M9.5 14h5"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-[#5c7180]">SARI Assistant</p>
                                    <p class="mt-1 text-[10px] leading-5 text-[#81909a]">Provides a temporary first response when Admin has not replied yet.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
(function () {
    function bootSellerPlatformChat() {
        const root = document.getElementById('sellerPlatformChat');
        if (!root || root.dataset.initialized === '1') return;
        root.dataset.initialized = '1';

        const apiBase = @json(url('/messaging/api'));
        const csrfToken = @json(csrf_token());
        const sellerId = {{ (int) $seller->id }};
        const allowedReactionEmojis = ['👍', '❤️', '😂', '😮', '😢', '🙏'];
        const assistantEnabled = @json((bool) config('sari_assistant.enabled', true));
        const assistantDelayMs = Math.max(0, Number(@json((int) config('sari_assistant.delay_seconds', 45))) * 1000);
        const assistantTypingStartMs = 180;

        const messagesEl = document.getElementById('sellerPlatformMessages');
        const form = document.getElementById('sellerPlatformMessageForm');
        const input = document.getElementById('sellerPlatformInput');
        const attachment = document.getElementById('sellerPlatformAttachment');
        const attachmentName = document.getElementById('sellerAttachmentName');
        const sendButton = document.getElementById('sellerPlatformSend');
        const errorBox = document.getElementById('sellerPlatformError');
        const statusEl = document.getElementById('sellerRealtimeStatus');
        const previewEl = document.getElementById('sellerLastMessagePreview');
        const totalEl = document.getElementById('sellerTotalMessages');
        const searchInput = document.getElementById('sellerMessageSearch');
        const searchClear = document.getElementById('sellerMessageSearchClear');
        const blockedNotice = document.getElementById('sellerChatBlockedNotice');
        const blockedReason = document.getElementById('sellerChatBlockedReason');

        let conversationUuid = null;
        let realtimeChannel = null;
        let pollTimer = null;
        let loadingDetail = false;
        let sellerChatBlocked = @json($sellerChatBlocked);
        let currentMessages = [];
        let assistantTypingTimer = null;
        let assistantTypingExpiryTimer = null;
        let assistantBurstTimer = null;
        let assistantBurstAttempt = 0;
        let supportConnecting = false;

        const escapeHtml = (value) => String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const formatTime = (value) => {
            if (!value) return '';
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return '';
            return new Intl.DateTimeFormat('en-PH', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true,
            }).format(date);
        };

        const formatFileSize = (bytes) => {
            const size = Number(bytes || 0);
            if (!size) return '';
            if (size < 1024) return `${size} B`;
            if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} KB`;
            return `${(size / (1024 * 1024)).toFixed(1)} MB`;
        };

        function setError(message = '') {
            if (!errorBox) return;
            errorBox.textContent = message;
            errorBox.classList.toggle('hidden', !message);
        }

        function lockComposer(reason = '') {
            sellerChatBlocked = true;
            if (input) {
                input.disabled = true;
                input.placeholder = 'Messaging is restricted by SARI Admin.';
            }
            if (attachment) attachment.disabled = true;
            if (sendButton) sendButton.disabled = true;
            blockedNotice?.classList.remove('hidden');
            blockedNotice?.classList.add('flex');
            if (blockedReason && reason) blockedReason.textContent = reason;
        }

        function isNearBottom(threshold = 140) {
            if (!messagesEl) return true;
            return messagesEl.scrollHeight - messagesEl.scrollTop - messagesEl.clientHeight <= threshold;
        }

        function scrollBottom(behavior = 'auto') {
            if (!messagesEl) return;
            messagesEl.scrollTo({ top: messagesEl.scrollHeight, behavior });
        }

        function attachmentHtml(message) {
            const file = message?.attachment;
            if (!file) return '';

            if (file.is_image) {
                return `
                    <div class="mt-2 overflow-hidden rounded-[14px] border border-[#e6dfd6] bg-white p-2 shadow-[0_7px_18px_rgba(35,28,20,.05)]">
                        <a href="${escapeHtml(file.url)}" target="_blank" rel="noopener">
                            <img src="${escapeHtml(file.url)}" alt="${escapeHtml(file.name || 'Attachment')}" class="max-h-[300px] w-full rounded-[10px] object-contain">
                        </a>
                    </div>`;
            }

            return `
                <div class="mt-2 overflow-hidden rounded-[14px] border border-[#e6dfd6] bg-white p-2 shadow-[0_7px_18px_rgba(35,28,20,.05)]">
                    <a href="${escapeHtml(file.download_url || file.url)}" class="flex items-center gap-3 rounded-xl bg-[#fbfaf7] p-3">
                        <div class="grid h-9 w-9 place-items-center rounded-lg bg-[#f5efe4] text-[#a8731f]">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 3h10l4 4v14H5z"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-[11px] font-semibold text-[#50483f]">${escapeHtml(file.name || 'Attachment')}</p>
                            <p class="mt-1 text-[9px] text-[#958c80]">${escapeHtml(formatFileSize(file.size) || 'Download file')}</p>
                        </div>
                    </a>
                </div>`;
        }

        function reactionHtml(message, mine) {
            const reactions = Array.isArray(message.reactions) ? message.reactions : [];
            const chips = reactions.map(reaction => `
                <span class="inline-flex h-7 items-center gap-1 rounded-full border px-2.5 text-[10px] shadow-[0_4px_10px_rgba(35,28,20,.05)] ${reaction.mine ? 'border-[#dfbb74] bg-[#fff8ea] text-[#8d6218]' : 'border-[#e7e0d7] bg-white text-[#6f675d]'}">
                    <span class="text-[13px] leading-none">${escapeHtml(reaction.emoji)}</span>
                    <span class="font-semibold">${Number(reaction.count || 0)}</span>
                </span>`).join('');

            return `
                <div class="mt-2 flex flex-wrap items-center gap-1.5 ${mine ? 'justify-end' : 'justify-start'}">
                    <div class="flex flex-wrap items-center gap-1">${chips}</div>
                    <div class="relative" data-reaction-wrap>
                        <button type="button" data-reaction-toggle class="grid h-7 w-7 place-items-center rounded-full border border-[#e5ded4] bg-white text-[#8c8378] shadow-[0_4px_10px_rgba(35,28,20,.05)] transition hover:border-[#d8bd8a] hover:bg-[#fffaf2] hover:text-[#a8731f]" aria-label="React to message">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="8"></circle><path d="M9 10h.01M15 10h.01"></path><path d="M8.5 14c1 1.3 2.1 2 3.5 2s2.5-.7 3.5-2"></path></svg>
                        </button>
                        <div data-reaction-picker class="absolute bottom-9 ${mine ? 'right-0' : 'left-0'} z-30 hidden items-center gap-1 rounded-full border border-[#e5ded4] bg-white p-1.5 shadow-[0_14px_36px_rgba(38,30,20,.14)]">
                            ${allowedReactionEmojis.map(emoji => `<button type="button" data-reaction-emoji="${emoji}" class="grid h-8 w-8 place-items-center rounded-full text-[17px] transition hover:bg-[#f7f2ea]">${emoji}</button>`).join('')}
                        </div>
                    </div>
                    <p class="text-[10px] text-[#968d82]">${escapeHtml(formatTime(message.created_at))}</p>
                </div>`;
        }

        function botAvatarHtml(sizeClass = 'h-8 w-8') {
            return `
                <div class="sari-bot-avatar ${sizeClass}" aria-label="SARI Assistant">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 5V3"></path><circle cx="12" cy="2.5" r=".7" fill="currentColor" stroke="none"></circle>
                        <rect x="5.5" y="6" width="13" height="11" rx="4"></rect>
                        <path d="M8 17v2M16 17v2M5.5 10H4M20 10h-1.5"></path>
                        <circle cx="9.5" cy="11" r="1" fill="currentColor" stroke="none"></circle>
                        <circle cx="14.5" cy="11" r="1" fill="currentColor" stroke="none"></circle>
                        <path d="M9.5 14h5"></path>
                    </svg>
                </div>`;
        }

        function hideAssistantTyping() {
            if (assistantTypingTimer) {
                window.clearTimeout(assistantTypingTimer);
                assistantTypingTimer = null;
            }
            if (assistantTypingExpiryTimer) {
                window.clearTimeout(assistantTypingExpiryTimer);
                assistantTypingExpiryTimer = null;
            }
            document.getElementById('sellerAssistantTyping')?.remove();
        }

        function showAssistantTyping() {
            if (!assistantEnabled || !messagesEl || document.getElementById('sellerAssistantTyping')) return;

            messagesEl.insertAdjacentHTML('beforeend', `
                <div id="sellerAssistantTyping" class="sari-assistant-typing" aria-live="polite" aria-label="SARI Assistant is typing">
                    ${botAvatarHtml('h-8 w-8')}
                    <div class="max-w-[72%]">
                        <div class="mb-1.5 text-[9px] font-semibold uppercase tracking-[.08em] text-[#6e899c]">SARI Assistant</div>
                        <div class="sari-assistant-typing__bubble">
                            <div class="sari-assistant-typing__dots" aria-hidden="true"><span></span><span></span><span></span></div>
                        </div>
                    </div>
                </div>`);

            requestAnimationFrame(() => scrollBottom('smooth'));
        }

        function syncAssistantTyping() {
            hideAssistantTyping();
            if (!assistantEnabled || !currentMessages.length) return;

            const last = currentMessages[currentMessages.length - 1];
            const waitingForAssistant = last?.sender_role === 'seller' && Number(last?.sender_id) === sellerId;
            if (!waitingForAssistant) return;

            const sentAt = new Date(last?.created_at || '').getTime();
            if (!Number.isFinite(sentAt)) return;

            const now = Date.now();
            const age = now - sentAt;
            const pendingWindowMs = assistantDelayMs + 60000;

            // Do not show a stale typing indicator for old unanswered history.
            if (age < -5000 || age > pendingWindowMs) return;

            // Fast Messenger-style presence: show the Assistant almost
            // immediately after a seller message while the async AI job works.
            const typingStartsAt = sentAt + assistantTypingStartMs;
            const waitMs = Math.max(0, typingStartsAt - now);

            assistantTypingTimer = window.setTimeout(() => {
                showAssistantTyping();
                assistantTypingTimer = null;
            }, waitMs);

            const expiryMs = Math.max(4000, (sentAt + pendingWindowMs) - now);
            assistantTypingExpiryTimer = window.setTimeout(() => {
                document.getElementById('sellerAssistantTyping')?.remove();
                assistantTypingExpiryTimer = null;
            }, expiryMs);
        }

        function messageHtml(message) {
            const mine = message.sender_role === 'seller' && Number(message.sender_id) === sellerId;
            const isBot = Boolean(message?.metadata?.ai_assistant);
            const senderLabel = isBot ? 'SARI Assistant · automated' : (message.sender_role === 'admin' ? 'SARI Admin' : escapeHtml(message.sender || 'Support'));
            const body = message.body
                ? `<div class="seller-message-bubble rounded-[14px] px-4 py-3 text-[12px] leading-6 shadow-[0_7px_18px_rgba(35,28,20,.055)] ${mine ? 'rounded-br-[5px] bg-[#c99128] text-white' : (isBot ? 'rounded-bl-[5px] border border-[#dce8ef] bg-[#f4f8fa] text-[#526a79]' : 'rounded-bl-[5px] border border-[#e6dfd6] bg-white text-[#514a42]')}">${escapeHtml(message.body)}</div>`
                : '';

            return `
                <div class="seller-message-row ${mine ? 'flex justify-end' : 'flex items-end gap-2.5'}" data-message-id="${Number(message.id)}" data-search="${escapeHtml(`${message.body || ''} ${message.attachment?.name || ''} ${senderLabel}`.toLowerCase())}">
                    ${mine ? '' : (isBot ? botAvatarHtml('h-8 w-8') : `<div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#f6efe2] text-[#9b6b1a] text-[9px] font-bold">SA</div>`)}
                    <div class="max-w-[84%] sm:max-w-[70%] lg:max-w-[64%]">
                        ${mine ? '' : `<div class="mb-1.5 text-[9px] font-semibold ${isBot ? 'uppercase tracking-[.08em] text-[#6e899c]' : 'text-[#7c7369]'}">${senderLabel}</div>`}
                        ${body}
                        ${attachmentHtml(message)}
                        ${reactionHtml(message, mine)}
                    </div>
                </div>`;
        }

        function renderMessages(nextMessages, followLatest = false) {
            const wasNearBottom = isNearBottom();
            currentMessages = Array.isArray(nextMessages) ? nextMessages : [];

            if (!messagesEl) return;

            if (!currentMessages.length) {
                messagesEl.innerHTML = `
                    <div id="sellerEmptyChat" class="flex h-full min-h-[360px] items-center justify-center text-center">
                        <div>
                            <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#f6efe2] text-[#9d6f22]"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path></svg></div>
                            <p class="mt-4 text-[13px] font-bold text-[#4f473e]">Start a conversation</p>
                            <p class="mt-1 text-[11px] text-[#958c80]">Send a message to SARI Admin below.</p>
                        </div>
                    </div>`;
            } else {
                messagesEl.innerHTML = currentMessages.map(messageHtml).join('');
            }

            if (totalEl) totalEl.textContent = String(currentMessages.length);
            const last = currentMessages[currentMessages.length - 1];
            if (previewEl) previewEl.textContent = last?.body || last?.attachment?.name || 'No messages yet.';

            applySearch();
            syncAssistantTyping();

            if (followLatest || wasNearBottom) {
                requestAnimationFrame(() => scrollBottom(followLatest ? 'auto' : 'smooth'));
            }
        }

        function applySearch() {
            const query = (searchInput?.value || '').trim().toLowerCase();
            messagesEl?.querySelectorAll('[data-message-id]').forEach(row => {
                const haystack = String(row.dataset.search || '').toLowerCase();
                row.classList.toggle('hidden', query !== '' && !haystack.includes(query));
            });
            if (searchClear) {
                searchClear.classList.toggle('hidden', query === '');
                searchClear.classList.toggle('grid', query !== '');
            }
        }

        async function parseResponse(response) {
            const raw = await response.text();
            try { return raw ? JSON.parse(raw) : {}; }
            catch (_) { return {}; }
        }

        function setSupportConnectionState(mode = 'connecting') {
            const connected = mode === 'connected';
            const unavailable = mode === 'unavailable';

            if (statusEl) {
                statusEl.textContent = connected
                    ? (window.Echo ? 'Live' : 'Connected')
                    : (unavailable ? 'Unavailable' : 'Connecting…');
            }

            if (!sellerChatBlocked && sendButton) {
                sendButton.disabled = !connected;
            }

            if (!sellerChatBlocked && input) {
                input.disabled = !connected;
                input.placeholder = connected
                    ? 'Write a message to SARI Admin...'
                    : 'Connecting to SARI Admin...';
            }

            if (!sellerChatBlocked && attachment) {
                attachment.disabled = !connected;
            }
        }

        async function recoverExistingSupportConversation() {
            const response = await fetch(`${apiBase}/conversations`, {
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const data = await parseResponse(response);
            if (!response.ok) return false;

            const rows = Array.isArray(data?.conversations) ? data.conversations : [];
            const support = rows.find(row => (
                row?.type === 'admin_support'
                && row?.uuid
                && (row.participants || []).some(participant => (
                    participant.role === 'seller'
                    && Number(participant.id) === sellerId
                ))
            ));

            if (!support?.uuid) return false;

            conversationUuid = String(support.uuid).trim();
            return conversationUuid !== '';
        }

        async function ensureSupportConversation() {
            if (conversationUuid) return true;
            if (supportConnecting) return false;

            supportConnecting = true;
            setSupportConnectionState('connecting');

            try {
                // Existing support threads should open instantly without running
                // creation/import work on every page load.
                if (await recoverExistingSupportConversation()) {
                    setSupportConnectionState('connected');
                    return true;
                }

                await openSupportConversation();
                setSupportConnectionState('connected');
                return Boolean(conversationUuid);
            } finally {
                supportConnecting = false;
            }
        }

        async function openSupportConversation() {
            const controller = new AbortController();
            const timeout = window.setTimeout(() => controller.abort(), 8000);

            try {
                const response = await fetch(`${apiBase}/support`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    signal: controller.signal,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await parseResponse(response);
                if (!response.ok) throw new Error(data.message || 'Unable to open SARI Support.');
                conversationUuid = String(data?.conversation?.uuid || '').trim();
                if (!conversationUuid) throw new Error('Support conversation is unavailable.');
            } catch (error) {
                // If the creation request completed server-side but the browser
                // timed out, recover the deduplicated support thread from inbox.
                if (await recoverExistingSupportConversation()) return;
                if (error?.name === 'AbortError') {
                    throw new Error('SARI Support took too long to connect. Please try again.');
                }
                throw error;
            } finally {
                window.clearTimeout(timeout);
            }
        }

        async function markRead() {
            if (!conversationUuid) return;
            fetch(`${apiBase}/conversations/${encodeURIComponent(conversationUuid)}/read`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            }).catch(() => {});
        }

        function subscribeRealtime(channelName) {
            if (!channelName || realtimeChannel === channelName) return;

            if (realtimeChannel && window.Echo) {
                try { window.Echo.leave(realtimeChannel); } catch (_) {}
            }

            realtimeChannel = channelName;

            if (!window.Echo) {
                if (statusEl) statusEl.textContent = 'Saved mode';
                return;
            }

            try {
                window.Echo.channel(channelName)
                    .listen('.platform.message', () => loadDetail(false))
                    .listen('.platform.reaction', () => loadDetail(false));

                if (statusEl) statusEl.textContent = 'Live';
            } catch (_) {
                if (statusEl) statusEl.textContent = 'Saved mode';
            }
        }

        async function loadDetail(initial = false) {
            if (!conversationUuid || loadingDetail) return;
            loadingDetail = true;

            try {
                const response = await fetch(`${apiBase}/conversations/${encodeURIComponent(conversationUuid)}`, {
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await parseResponse(response);
                if (!response.ok) throw new Error(data.message || 'Unable to load messages.');

                renderMessages(data.messages || [], initial);
                subscribeRealtime(data?.conversation?.realtime_channel || null);
                markRead();
                setError('');
            } catch (error) {
                setError(error.message || 'Unable to load messages.');
            } finally {
                loadingDetail = false;
                root.dataset.ready = '1';
            }
        }

        function stopAssistantBurstRefresh() {
            if (assistantBurstTimer) {
                window.clearTimeout(assistantBurstTimer);
                assistantBurstTimer = null;
            }
            assistantBurstAttempt = 0;
        }

        function startAssistantBurstRefresh() {
            stopAssistantBurstRefresh();
            if (!assistantEnabled || !conversationUuid) return;

            const tick = async () => {
                assistantBurstAttempt += 1;
                await loadDetail(false);

                const latest = currentMessages[currentMessages.length - 1];
                const receivedReply = latest && latest.sender_role === 'admin';

                if (receivedReply || assistantBurstAttempt >= 8) {
                    stopAssistantBurstRefresh();
                    return;
                }

                // Briefly poll only while an AI answer is expected. This is
                // intentionally short-lived so normal page usage stays light.
                assistantBurstTimer = window.setTimeout(tick, 650);
            };

            assistantBurstTimer = window.setTimeout(tick, 320);
        }

        attachment?.addEventListener('change', function () {
            const file = this.files?.[0];
            if (file && file.size > 15 * 1024 * 1024) {
                this.value = '';
                attachmentName?.classList.add('hidden');
                setError('Attachments must be 15 MB or smaller.');
                return;
            }
            if (attachmentName) {
                attachmentName.textContent = file ? `Attached: ${file.name} · ${formatFileSize(file.size)}` : '';
                attachmentName.classList.toggle('hidden', !file);
            }
        });

        form?.addEventListener('submit', async function (event) {
            event.preventDefault();
            setError('');

            if (sellerChatBlocked) {
                lockComposer(blockedReason?.textContent || 'Messaging is restricted by SARI Admin.');
                return;
            }

            const body = (input?.value || '').trim();
            const file = attachment?.files?.[0] || null;
            if (!body && !file) {
                setError('Write a message or attach a file.');
                return;
            }
            if (!conversationUuid) {
                try {
                    const connected = await ensureSupportConversation();
                    if (!connected || !conversationUuid) {
                        setError('Unable to connect to SARI Support. Please try again.');
                        return;
                    }
                    await loadDetail(false);
                } catch (error) {
                    setSupportConnectionState('unavailable');
                    setError(error.message || 'Unable to connect to SARI Support.');
                    return;
                }
            }

            sendButton.disabled = true;

            try {
                const payload = new FormData();
                if (body) payload.append('body', body);
                if (file) payload.append('attachment', file);

                const response = await fetch(`${apiBase}/conversations/${encodeURIComponent(conversationUuid)}/messages`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: payload,
                });
                const data = await parseResponse(response);

                if (!response.ok) {
                    if (response.status === 423) {
                        lockComposer(data.message || 'Messaging is restricted by SARI Admin.');
                    }
                    const validationMessage = Object.values(data.errors || {})?.[0]?.[0];
                    throw new Error(data.message || validationMessage || 'Unable to send message.');
                }

                if (input) input.value = '';
                if (attachment) attachment.value = '';
                attachmentName?.classList.add('hidden');
                if (attachmentName) attachmentName.textContent = '';

                await loadDetail(false);
                startAssistantBurstRefresh();
                scrollBottom('smooth');
                input?.focus();
            } catch (error) {
                setError(error.message || 'Unable to send message.');
            } finally {
                if (!sellerChatBlocked) sendButton.disabled = false;
            }
        });

        messagesEl?.addEventListener('click', async function (event) {
            const toggle = event.target.closest('[data-reaction-toggle]');
            if (toggle) {
                event.preventDefault();
                const wrap = toggle.closest('[data-reaction-wrap]');
                const picker = wrap?.querySelector('[data-reaction-picker]');
                messagesEl.querySelectorAll('[data-reaction-picker]').forEach(item => {
                    if (item !== picker) {
                        item.classList.add('hidden');
                        item.classList.remove('flex');
                    }
                });
                if (picker) {
                    const opening = picker.classList.contains('hidden');
                    picker.classList.toggle('hidden', !opening);
                    picker.classList.toggle('flex', opening);
                }
                return;
            }

            const reactionButton = event.target.closest('[data-reaction-emoji]');
            if (!reactionButton) return;
            const row = reactionButton.closest('[data-message-id]');
            const messageId = Number(row?.dataset.messageId || 0);
            const emoji = reactionButton.dataset.reactionEmoji;
            if (!messageId || !allowedReactionEmojis.includes(emoji)) return;

            reactionButton.disabled = true;
            try {
                const response = await fetch(`${apiBase}/messages/${messageId}/reaction`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ emoji }),
                });
                const data = await parseResponse(response);
                if (!response.ok) throw new Error(data.message || 'Unable to save reaction.');
                await loadDetail(false);
            } catch (error) {
                setError(error.message || 'Unable to save reaction.');
            } finally {
                reactionButton.disabled = false;
            }
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('[data-reaction-wrap]')) {
                messagesEl?.querySelectorAll('[data-reaction-picker]').forEach(item => {
                    item.classList.add('hidden');
                    item.classList.remove('flex');
                });
            }
        });

        searchInput?.addEventListener('input', applySearch);
        searchClear?.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            applySearch();
            searchInput?.focus();
        });

        async function start() {
            setSupportConnectionState('connecting');

            try {
                const connected = await ensureSupportConversation();
                if (!connected || !conversationUuid) {
                    throw new Error('Unable to establish the SARI Support conversation.');
                }

                await loadDetail(true);
                setSupportConnectionState('connected');
                pollTimer = window.setInterval(() => loadDetail(false), 12000);
            } catch (error) {
                root.dataset.ready = '1';
                setSupportConnectionState('unavailable');
                setError(error.message || 'Unable to connect to SARI Support.');
            }
        }

        start();

        const cleanup = function () {
            hideAssistantTyping();
            stopAssistantBurstRefresh();
            if (pollTimer) window.clearInterval(pollTimer);
            if (realtimeChannel && window.Echo) {
                try { window.Echo.leave(realtimeChannel); } catch (_) {}
            }
            root.dataset.initialized = '0';
        };

        document.addEventListener('livewire:navigating', cleanup, { once: true });
        window.addEventListener('beforeunload', cleanup, { once: true });
    }

    /* Livewire evaluates this body script when the page arrives. Boot once here;
       bootSellerPlatformChat registers its own one-shot navigation cleanup. */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootSellerPlatformChat, { once: true });
    } else {
        bootSellerPlatformChat();
    }
})();
</script>
@endpush
