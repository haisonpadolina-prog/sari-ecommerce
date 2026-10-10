@extends('layouts.seller')

@section('title', 'Chat / Messaging — SARI Seller')
@section('page-title', 'Chat / Messaging')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/messages.css') }}?v=20261007-1">
@endpush

@section('content')
@php
    // SellerAdminChatController resolves both Admin Support and Buyer threads.
    $sellerChatRestriction = $sellerChatRestriction ?? null;
    $sellerChatBlocked = (bool) ($sellerChatBlocked ?? false);
    $buyerConversations = $buyerConversations ?? collect();
    $selectedBuyer = $selectedBuyer ?? null;
    $buyerMessages = $buyerMessages ?? collect();
    $buyerMode = (bool) $selectedBuyer;
@endphp



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
            {{-- LEFT: UNIFIED CONVERSATIONS --}}
            <aside id="sellerConversationPane" class="border-b border-[#ebe5dc] bg-white lg:border-b-0 lg:border-r">
                <div class="border-b border-[#eee8df] px-4 py-4 sm:px-5">
                    <h3 class="font-bold tracking-[-0.02em] text-[#24201b]" style="font-size:clamp(14px,.88vw,16px)">Conversations</h3>
                    <p class="mt-1 text-[#938a7f]" style="font-size:clamp(10px,.66vw,12px)">Admin support and your buyers</p>

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

                <div class="seller-conversation-list">
                    <a
                        href="{{ route('seller.messages') }}"
                        class="seller-conversation-item {{ $buyerMode ? '' : 'is-active' }}"
                        data-conversation-item
                        data-conversation-search="sari admin support human administrator support"
                    >
                        <div class="flex items-center gap-3">
                            <div class="relative shrink-0">
                                <div class="sari-support-brand-avatar">
                                    <img src="{{ asset('images/sari-main-logo.png') }}" alt="SARI">
                                </div>
                                <span class="sari-support-online-dot" aria-hidden="true"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[13px] font-bold text-[#2d2822]">SARI Admin Support</p>
                                <p class="mt-0.5 truncate text-[10px] text-[#8f867b]">Human administrator support</p>
                                <p id="sellerLastMessagePreview" class="mt-2 line-clamp-2 text-[11px] leading-5 text-[#72695f]">
                                    {{ $buyerMode ? 'Official SARI support channel' : 'Loading conversation…' }}
                                </p>
                            </div>
                        </div>
                    </a>

                    @foreach ($buyerConversations as $conversation)
                        <a
                            href="{{ route('seller.messages', ['buyer' => $conversation['key']]) }}"
                            class="seller-conversation-item {{ $selectedBuyer && $selectedBuyer['key'] === $conversation['key'] ? 'is-active' : '' }}"
                            data-conversation-item
                            data-conversation-search="{{ strtolower($conversation['name'] . ' ' . ($conversation['email'] ?? '') . ' ' . $conversation['preview']) }}"
                        >
                            <div class="flex items-center gap-3">
                                <div class="seller-buyer-avatar" aria-hidden="true">
                                    {{ $conversation['initials'] }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="truncate text-[12px] font-bold text-[#344054]">{{ $conversation['name'] }}</p>

                                        @if ((int) $conversation['unread'] > 0)
                                            <span class="seller-conversation-unread">{{ (int) $conversation['unread'] }}</span>
                                        @endif
                                    </div>

                                    <p class="mt-0.5 truncate text-[8px] text-[#98a2b3]">
                                        Buyer{{ $conversation['email'] ? ' · ' . $conversation['email'] : '' }}
                                    </p>

                                    <p class="mt-1.5 line-clamp-2 text-[8.5px] leading-4 text-[#667085]">
                                        {{ $conversation['preview'] }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </aside>

            {{-- CENTER: SELECTED CONVERSATION --}}
            <div id="sellerCenterPane" class="flex min-h-0 min-w-0 flex-col bg-white">
                @if ($selectedBuyer)
                    <div class="flex items-center justify-between gap-4 border-b border-[#ebe5dc] bg-white px-4 py-4 sm:px-5">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="seller-buyer-avatar" aria-hidden="true">
                                {{ $selectedBuyer['initials'] }}
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-[14px] font-bold tracking-[-0.02em] text-[#29241f]">{{ $selectedBuyer['name'] }}</p>
                                <div class="seller-buyer-thread-meta mt-1">
                                    <span>Buyer conversation</span>
                                    @if ((int) $selectedBuyer['order_count'] > 0)
                                        <span>•</span>
                                        <span>{{ (int) $selectedBuyer['order_count'] }} {{ (int) $selectedBuyer['order_count'] === 1 ? 'order' : 'orders' }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <span class="seller-buyer-thread-badge">Buyer</span>
                    </div>
                @else
                    <div class="flex items-center justify-between gap-4 border-b border-[#ebe5dc] bg-white px-4 py-4 sm:px-5">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="relative shrink-0">
                                <div class="sari-support-brand-avatar">
                                    <img src="{{ asset('images/sari-main-logo.png') }}" alt="SARI">
                                </div>
                                <span class="sari-support-online-dot" aria-hidden="true"></span>
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-[14px] font-bold tracking-[-0.02em] text-[#29241f]">SARI Admin Support</p>
                                <p class="mt-1 text-[10px] text-[#8f867b]">Official human support channel</p>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <span id="sellerRealtimeStatus" class="seller-platform-status" role="status" aria-live="polite" data-state="connecting">Connecting</span>
                            <span id="sellerAssistantBadge" class="seller-platform-ai" title="SARI Assistant automatically confirms receipt while you wait for a human administrator.">
                                <img src="{{ asset('images/sari-seller-ai-assistant.png') }}" alt="" aria-hidden="true">
                                Receipt confirmation
                            </span>
                        </div>
                    </div>
                @endif

                <div id="sellerPlatformMessages" class="min-h-0 flex-1 overflow-y-auto bg-[#fbfaf7] px-4 py-6 sm:px-6 lg:px-7">
                    @if ($selectedBuyer)
                        @forelse ($buyerMessages as $message)
                            @php
                                $mine = $message->sender_role === 'seller';
                            @endphp

                            <div
                                class="seller-message-row {{ $mine ? 'flex justify-end' : 'flex items-end gap-2.5' }}"
                                data-message-id="{{ $message->id }}"
                                data-search="{{ strtolower(($message->body ?? '') . ' ' . ($mine ? 'seller you' : $selectedBuyer['name'])) }}"
                            >
                                @unless ($mine)
                                    <div class="seller-buyer-avatar" aria-hidden="true">
                                        {{ $selectedBuyer['initials'] }}
                                    </div>
                                @endunless

                                <div class="seller-message-content">
                                    @unless ($mine)
                                        <div class="seller-message-label">{{ $selectedBuyer['name'] }}</div>
                                    @endunless

                                    <span
                                        data-chat-bubble
                                        data-chat-side="{{ $mine ? 'outgoing' : 'incoming' }}"
                                        data-chat-kind="{{ $mine ? 'seller' : 'buyer' }}"
                                        class="seller-message-surface"
                                    >{{ trim((string) ($message->body ?? '')) }}</span>

                                    <p class="seller-message-time {{ $mine ? 'text-right' : '' }}">
                                        {{ $message->created_at?->format('h:i A') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div id="sellerEmptyChat" class="flex h-full min-h-[360px] items-center justify-center text-center">
                                <div>
                                    <div class="seller-buyer-avatar mx-auto h-14 w-14 text-[12px]">
                                        {{ $selectedBuyer['initials'] }}
                                    </div>
                                    <p class="mt-4 text-[13px] font-bold text-[#4f473e]">Start a conversation with {{ $selectedBuyer['name'] }}</p>
                                    <p class="mt-1 text-[11px] text-[#958c80]">Send a message about their order or delivery.</p>
                                </div>
                            </div>
                        @endforelse
                    @else
                        <div id="sellerEmptyChat" class="flex h-full min-h-[360px] items-center justify-center text-center">
                            <div>
                                <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#f6efe2] text-[#9d6f22]">
                                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path></svg>
                                </div>
                                <p class="mt-4 text-[13px] font-bold text-[#4f473e]">Start a conversation</p>
                                <p class="mt-1 text-[11px] text-[#958c80]">Send your concern to SARI Admin Support. SARI Assistant will confirm receipt while you wait for a human administrator.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="seller-platform-composer border-t border-[#ebe5dc] bg-white p-3 sm:p-3.5">
                    @if ($selectedBuyer)
                        <form
                            id="sellerBuyerMessageForm"
                            action="{{ route('seller.buyer-messages.send', $selectedBuyer['key']) }}"
                            method="POST"
                        >
                            @csrf

                            <textarea
                                id="sellerBuyerInput"
                                name="body"
                                rows="1"
                                maxlength="3000"
                                required
                                placeholder="Write a message to {{ $selectedBuyer['name'] }}..."
                            ></textarea>

                            <button
                                id="sellerBuyerSend"
                                type="submit"
                                title="Send message"
                                aria-label="Send message to {{ $selectedBuyer['name'] }}"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m4 4 17 8-17 8 3-8-3-8Z"></path><path d="M7 12h14"></path></svg>
                            </button>
                        </form>

                        <p id="sellerBuyerMessageError" class="mt-2 hidden text-[10px] text-[#a45f5f]" role="alert" aria-live="assertive"></p>
                    @else
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

                        <p id="sellerPlatformError" class="mt-2 hidden text-[10px] text-[#a45f5f]" role="alert" aria-live="assertive"></p>
                    @endif
                </div>
            </div>


            {{-- RIGHT: CONVERSATION CONTEXT --}}
            <aside id="sellerDetailsPane" class="hidden border-l border-[#ebe5dc] bg-white 2xl:block">
                @if ($selectedBuyer)
                    <div class="border-b border-[#eee8df] px-5 py-4">
                        <p class="text-[13px] font-bold text-[#302a24]">Buyer Details</p>
                        <p class="mt-1 text-[10px] text-[#948b7f]">Order customer context</p>
                    </div>

                    <div class="p-5">
                        <div class="flex flex-col items-center text-center">
                            <div class="seller-buyer-avatar h-16 w-16 text-[14px]">
                                {{ $selectedBuyer['initials'] }}
                            </div>

                            <p class="mt-3 text-[14px] font-bold text-[#2e2923]">{{ $selectedBuyer['name'] }}</p>
                            <p class="mt-1 text-[11px] text-[#92897e]">{{ $selectedBuyer['email'] ?: 'Buyer account' }}</p>
                            <span class="mt-3 rounded-full border border-[#dce5ed] bg-[#f4f7fa] px-2.5 py-1 text-[10px] font-semibold text-[#617d96]">Buyer</span>
                        </div>

                        <div class="mt-6 space-y-3">
                            <div class="rounded-[14px] border border-[#e8e2d9] bg-[#fdfcf9] p-4">
                                <p class="text-[10px] text-[#948b7f]">Orders with your store</p>
                                <p class="mt-2 text-[12px] font-semibold text-[#50483f]">{{ (int) $selectedBuyer['order_count'] }}</p>
                            </div>

                            <div class="rounded-[14px] border border-[#e8e2d9] bg-[#fdfcf9] p-4">
                                <p class="text-[10px] text-[#948b7f]">Conversation</p>
                                <p class="mt-2 text-[12px] font-semibold text-[#50483f]"><span id="sellerBuyerTotalMessages">{{ $buyerMessages->count() }}</span> total messages</p>
                            </div>

                            <div class="rounded-[14px] border border-[#dfe7ec] bg-[#f7fafc] p-4">
                                <p class="text-[10px] font-bold text-[#5c7180]">Buyer conversation</p>
                                <p class="mt-1 text-[10px] leading-5 text-[#81909a]">Use this thread for order, shipping, payment, or delivery coordination with this buyer.</p>
                            </div>
                        </div>
                    </div>
                @else
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
                                        <img src="{{ asset('images/sari-seller-ai-assistant.png') }}" alt="" aria-hidden="true">
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-[#5c7180]">SARI Assistant</p>
                                        <p class="mt-1 text-[10px] leading-5 text-[#81909a]">Confirms receipt of your concern while you wait for a human SARI administrator to review and respond.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const buyerMode = @json($buyerMode);
    const selectedBuyerName = @json($selectedBuyer['name'] ?? '');
    const selectedBuyerInitials = @json($selectedBuyer['initials'] ?? 'B');
    const buyerThreadUrl = @json($selectedBuyer ? route('seller.buyer-messages.thread', $selectedBuyer['key']) : null);
    const buyerSendUrl = @json($selectedBuyer ? route('seller.buyer-messages.send', $selectedBuyer['key']) : null);
    const csrfTokenShared = @json(csrf_token());

    function bootSellerBuyerThread() {
        if (!buyerMode) return;

        const root = document.getElementById('sellerPlatformChat');
        if (!root || root.dataset.initialized === '1') return;

        root.dataset.initialized = '1';

        const buyerThreadLifecycle = new AbortController();
        const buyerThreadSignal = buyerThreadLifecycle.signal;

        const messagesEl = document.getElementById('sellerPlatformMessages');
        const form = document.getElementById('sellerBuyerMessageForm');
        const input = document.getElementById('sellerBuyerInput');
        const sendButton = document.getElementById('sellerBuyerSend');
        const errorBox = document.getElementById('sellerBuyerMessageError');
        const totalEl = document.getElementById('sellerBuyerTotalMessages');
        const searchInput = document.getElementById('sellerMessageSearch');
        const searchClear = document.getElementById('sellerMessageSearchClear');

        let pollTimer = null;
        let loading = false;
        let sending = false;

        const escapeHtml = (value) => String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const formatTime = (value) => {
            if (!value) return '';

            const date = new Date(value);

            if (Number.isNaN(date.getTime())) {
                return '';
            }

            return new Intl.DateTimeFormat('en-PH', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true,
            }).format(date);
        };

        function setError(message = '') {
            if (!errorBox) return;

            errorBox.textContent = message;
            errorBox.classList.toggle(
                'hidden',
                !message
            );
        }

        function scrollBottom(behavior = 'auto') {
            if (!messagesEl) return;

            messagesEl.scrollTo({
                top: messagesEl.scrollHeight,
                behavior,
            });
        }

        function resizeInput() {
            if (!input) return;

            input.style.height = 'auto';
            input.style.height =
                `${Math.min(
                    112,
                    Math.max(
                        38,
                        input.scrollHeight
                    )
                )}px`;
        }

        function messageHtml(message) {
            const mine =
                message.sender_role === 'seller';

            return `
                <div
                    class="seller-message-row ${mine ? 'flex justify-end' : 'flex items-end gap-2.5'}"
                    data-message-id="${Number(message.id || 0)}"
                    data-search="${escapeHtml(`${message.body || ''} ${mine ? 'seller you' : selectedBuyerName}`.toLowerCase())}"
                >
                    ${mine ? '' : `
                        <div class="seller-buyer-avatar" aria-hidden="true">
                            ${escapeHtml(selectedBuyerInitials)}
                        </div>
                    `}

                    <div class="seller-message-content">
                        ${mine ? '' : `
                            <div class="seller-message-label">
                                ${escapeHtml(selectedBuyerName)}
                            </div>
                        `}

                        <span
                            data-chat-bubble
                            data-chat-side="${mine ? 'outgoing' : 'incoming'}"
                            data-chat-kind="${mine ? 'seller' : 'buyer'}"
                            class="seller-message-surface"
                        >${escapeHtml(String(message.body || '').trim())}</span>

                        <p class="seller-message-time ${mine ? 'text-right' : ''}">
                            ${escapeHtml(formatTime(message.created_at))}
                        </p>
                    </div>
                </div>
            `;
        }

        function renderMessages(messages, followLatest = false) {
            if (!messagesEl) return;

            const rows =
                Array.isArray(messages)
                    ? messages
                    : [];

            if (!rows.length) {
                messagesEl.innerHTML = `
                    <div id="sellerEmptyChat" class="flex h-full min-h-[360px] items-center justify-center text-center">
                        <div>
                            <div class="seller-buyer-avatar mx-auto h-14 w-14 text-[12px]">
                                ${escapeHtml(selectedBuyerInitials)}
                            </div>

                            <p class="mt-4 text-[13px] font-bold text-[#4f473e]">
                                Start a conversation with ${escapeHtml(selectedBuyerName)}
                            </p>

                            <p class="mt-1 text-[11px] text-[#958c80]">
                                Send a message about their order or delivery.
                            </p>
                        </div>
                    </div>
                `;
            } else {
                messagesEl.innerHTML =
                    rows.map(messageHtml).join('');
            }

            if (totalEl) {
                totalEl.textContent =
                    String(rows.length);
            }

            applySearch();

            if (followLatest) {
                requestAnimationFrame(
                    () => scrollBottom('auto')
                );
            }
        }

        async function parseResponse(response) {
            const raw = await response.text();

            try {
                return raw
                    ? JSON.parse(raw)
                    : {};
            } catch (_) {
                return {};
            }
        }

        async function loadThread(initial = false) {
            if (
                !buyerThreadUrl
                || loading
                || (!initial && document.hidden)
                || buyerThreadSignal.aborted
            ) {
                return;
            }

            loading = true;

            try {
                const response = await fetch(
                    buyerThreadUrl,
                    {
                        credentials: 'same-origin',
                        cache: 'no-store',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    }
                );

                const data =
                    await parseResponse(response);

                if (!response.ok) {
                    throw new Error(
                        data.message
                        || 'Unable to load buyer messages.'
                    );
                }

                renderMessages(
                    data.messages || [],
                    initial
                );

                setError('');
            } catch (error) {
                setError(
                    error.message
                    || 'Unable to load buyer messages.'
                );
            } finally {
                loading = false;
                root.dataset.ready = '1';
            }
        }

        function applySearch() {
            const query =
                (
                    searchInput?.value
                    || ''
                )
                    .trim()
                    .toLowerCase();

            messagesEl
                ?.querySelectorAll(
                    '[data-message-id]'
                )
                .forEach(row => {
                    const haystack =
                        String(
                            row.dataset.search
                            || ''
                        ).toLowerCase();

                    row.classList.toggle(
                        'hidden',
                        query !== ''
                        && !haystack.includes(query)
                    );
                });

            if (searchClear) {
                searchClear.classList.toggle(
                    'hidden',
                    query === ''
                );

                searchClear.classList.toggle(
                    'grid',
                    query !== ''
                );
            }
        }

        form?.addEventListener(
            'submit',
            async (event) => {
                event.preventDefault();

                if (sending) return;

                const body =
                    (input?.value || '').trim();

                if (!body) {
                    input?.focus();
                    return;
                }

                sending = true;
                setError('');

                if (sendButton) {
                    sendButton.disabled = true;
                }

                try {
                    const response = await fetch(
                        buyerSendUrl,
                        {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'X-CSRF-TOKEN':
                                    csrfTokenShared,
                                'Accept':
                                    'application/json',
                                'Content-Type':
                                    'application/json',
                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                body,
                            }),
                        }
                    );

                    const data =
                        await parseResponse(response);

                    if (!response.ok) {
                        const validationMessage =
                            Object.values(
                                data.errors || {}
                            )?.[0]?.[0];

                        throw new Error(
                            data.message
                            || validationMessage
                            || 'Unable to send message.'
                        );
                    }

                    if (input) {
                        input.value = '';
                        resizeInput();
                    }

                    await loadThread(true);
                    input?.focus();
                } catch (error) {
                    setError(
                        error.message
                        || 'Unable to send message.'
                    );
                } finally {
                    sending = false;

                    if (sendButton) {
                        sendButton.disabled = false;
                    }
                }
            }
        );

        input?.addEventListener(
            'input',
            () => {
                resizeInput();
                setError('');
            }
        );

        input?.addEventListener(
            'keydown',
            (event) => {
                if (event.isComposing) {
                    return;
                }

                if (
                    event.key === 'Enter'
                    && !event.shiftKey
                ) {
                    event.preventDefault();
                    form?.requestSubmit();
                }
            }
        );

        searchInput?.addEventListener(
            'input',
            applySearch
        );

        searchClear?.addEventListener(
            'click',
            () => {
                if (searchInput) {
                    searchInput.value = '';
                }

                applySearch();
                searchInput?.focus();
            }
        );

        resizeInput();
        scrollBottom('auto');
        loadThread(false);

        pollTimer = window.setInterval(
            () => loadThread(false),
            3000
        );

        document.addEventListener(
            'visibilitychange',
            function () {
                if (!document.hidden) {
                    loadThread(false);
                }
            },
            { signal: buyerThreadSignal }
        );

        const cleanup = () => {
            if (pollTimer) {
                window.clearInterval(pollTimer);
                pollTimer = null;
            }

            buyerThreadLifecycle.abort();
            root.dataset.initialized = '0';
        };

        document.addEventListener(
            'livewire:navigating',
            cleanup,
            { once: true, signal: buyerThreadSignal }
        );

        window.addEventListener(
            'beforeunload',
            cleanup,
            { once: true, signal: buyerThreadSignal }
        );
    }

    function bootSellerPlatformChat() {
        const root = document.getElementById('sellerPlatformChat');
        if (!root || root.dataset.initialized === '1') return;
        root.dataset.initialized = '1';

        const platformChatLifecycle = new AbortController();
        const platformChatSignal = platformChatLifecycle.signal;

        const apiBase = @json(url('/messaging/api'));
        const csrfToken = @json(csrf_token());
        const sellerId = {{ (int) $seller->id }};
        const allowedReactionEmojis = ['👍', '❤️', '😂', '😮', '😢', '🙏'];
        const assistantEnabled = @json((bool) config('sari.assistant.enabled', true));
        const assistantDelayMs = Math.max(0, Number(@json((int) config('sari.assistant.delay_seconds', 0))) * 1000);
        const assistantTypingStartMs = 0; // receipt acknowledgement is generated server-side without an AI-provider wait

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
        const assistantBadge = document.getElementById('sellerAssistantBadge');

        let conversationUuid = null;
        let realtimeChannel = null;
        let pollTimer = null;
        let loadingDetail = false;
        let detailReloadPending = false;
        let sendingMessage = false;
        let sellerChatBlocked = @json($sellerChatBlocked);
        let currentMessages = [];
        let assistantTypingTimer = null;
        let assistantTypingExpiryTimer = null;
        let assistantBurstTimer = null;
        let assistantBurstAttempt = 0;
        let supportConnecting = false;
        let assistantRequestController = null;

        function stopFallbackPolling() {
            if (!pollTimer) return;
            window.clearInterval(pollTimer);
            pollTimer = null;
        }

        function startFallbackPolling() {
            if (pollTimer || platformChatSignal.aborted) return;

            pollTimer = window.setInterval(function () {
                if (!document.hidden && navigator.onLine) {
                    loadDetail(false);
                }
            }, 3000);
        }

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


        if (assistantBadge && !assistantEnabled) {
            assistantBadge.dataset.disabled = '1';
            assistantBadge.setAttribute('title', 'Automatic receipt acknowledgement is currently unavailable. Human SARI Admin support remains available.');
            assistantBadge.lastChild.textContent = ' Confirmation off';
        }

        function resizeComposerInput() {
            if (!input) return;
            input.style.height = 'auto';
            input.style.height = `${Math.min(112, Math.max(38, input.scrollHeight))}px`;
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
                    <img src="{{ asset('images/sari-seller-ai-assistant.png') }}" alt="" aria-hidden="true">
                </div>`;
        }

        function adminAvatarHtml(sizeClass = 'h-8 w-8') {
            return `
                <div class="sari-admin-avatar ${sizeClass}" aria-label="SARI Admin">
                    <img src="{{ asset('images/sari-main-logo.png') }}" alt="" aria-hidden="true">
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
            // Admin Support uses an immediate persisted receipt acknowledgement.
            // Avoid a fake typing delay; the direct endpoint returns the saved
            // acknowledgement while polling/realtime remains a fallback.
            hideAssistantTyping();
        }


        function messageHtml(message) {
            const mine = message.sender_role === 'seller' && Number(message.sender_id) === sellerId;
            const isBot = Boolean(message?.metadata?.ai_assistant);
            const isAcknowledgement = Boolean(
                message?.metadata?.support_acknowledgement
            );
            const isPending = Boolean(message?._optimistic);
            const senderLabel = isBot
                ? (
                    isAcknowledgement
                        ? 'SARI Assistant · acknowledgement'
                        : 'SARI Assistant · previous automated response'
                )
                : (
                    message.sender_role === 'admin'
                        ? 'SARI Admin'
                        : escapeHtml(message.sender || 'Support')
                );

            const body = message.body
                ? `
                    <span
                        data-chat-bubble
                        data-chat-side="${mine ? 'outgoing' : 'incoming'}"
                        data-chat-kind="${mine ? 'seller' : (isBot ? 'assistant' : (message.sender_role === 'admin' ? 'admin' : 'support'))}"
                        class="seller-message-surface"
                    >${escapeHtml(message.body)}</span>
                `
                : '';

            const pendingAttachment = isPending && message?._optimisticAttachment
                ? `
                    <div class="seller-message-uploading">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M8 12.5 14.5 6a3 3 0 0 1 4.2 4.2l-8 8a5 5 0 0 1-7.1-7.1l8.3-8.3"></path>
                        </svg>
                        <span class="min-w-0 truncate">${escapeHtml(message._optimisticAttachment.name || 'Attachment')}</span>
                        <span class="shrink-0 text-[#98a2b3]">Uploading…</span>
                    </div>`
                : '';

            const meta = isPending
                ? `<div class="seller-message-pending">Sending…</div>`
                : reactionHtml(message, mine);

            return `
                <div class="seller-message-row ${mine ? 'flex justify-end' : 'flex items-end gap-2.5'}"
                     data-message-id="${escapeHtml(message.id)}"
                     data-search="${escapeHtml(`${message.body || ''} ${message.attachment?.name || message?._optimisticAttachment?.name || ''} ${senderLabel}`.toLowerCase())}">
                    ${mine ? '' : (isBot ? botAvatarHtml('h-8 w-8') : adminAvatarHtml('h-8 w-8'))}
                    <div class="seller-message-content">
                        ${mine ? '' : `<div class="seller-message-label ${isBot ? 'seller-message-label--assistant' : ''}">${senderLabel}</div>`}
                        ${body}
                        ${pendingAttachment || attachmentHtml(message)}
                        ${meta}
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

        function upsertMessage(message, followLatest = true) {
            if (!message?.id) return;

            const id = Number(message.id);
            const next = [...currentMessages];
            const index = next.findIndex(row => Number(row.id) === id);

            if (index >= 0) {
                next[index] = { ...next[index], ...message };
            } else {
                next.push(message);
            }

            next.sort((a, b) => Number(a.id || 0) - Number(b.id || 0));
            renderMessages(next, followLatest);
        }

        function removeOptimisticMessage(token) {
            if (!token) return;

            renderMessages(
                currentMessages.filter(
                    (message) => message?._optimisticToken !== token
                ),
                true
            );
        }

        function makeOptimisticMessage(body, file) {
            const maxId = currentMessages.reduce(
                (max, message) => Math.max(max, Number(message?.id || 0)),
                0
            );
            const token = `seller-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;

            return {
                id: maxId + 0.5,
                sender_role: 'seller',
                sender_id: sellerId,
                sender: 'You',
                message_type: file ? 'attachment' : 'text',
                body: body || null,
                attachment: null,
                reactions: [],
                metadata: {},
                created_at: new Date().toISOString(),
                _optimistic: true,
                _optimisticToken: token,
                _optimisticAttachment: file
                    ? {
                        name: file.name || 'Attachment',
                        size: Number(file.size || 0),
                    }
                    : null,
            };
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

        function setRealtimeBadge(mode = 'connecting') {
            if (!statusEl) return;

            const labels = {
                live: 'Live',
                syncing: 'Syncing',
                fallback: 'Polling',
                offline: 'Offline',
                unavailable: 'Unavailable',
                connecting: 'Connecting',
            };

            statusEl.dataset.state = mode;
            statusEl.textContent = labels[mode] || labels.connecting;
        }

        function setSupportConnectionState(mode = 'connecting') {
            const connected = mode === 'connected';
            const unavailable = mode === 'unavailable';

            if (unavailable) {
                setRealtimeBadge('unavailable');
            } else if (!connected) {
                setRealtimeBadge('connecting');
            } else if (!window.Echo) {
                setRealtimeBadge('fallback');
            } else if (statusEl?.dataset.state !== 'live') {
                setRealtimeBadge('syncing');
                window.setTimeout(() => {
                    if (statusEl?.dataset.state === 'syncing') {
                        setRealtimeBadge('fallback');
                    }
                }, 1800);
            }

            if (!sellerChatBlocked && sendButton) {
                sendButton.disabled = !connected || sendingMessage;
            }

            if (!sellerChatBlocked && input) {
                input.disabled = !connected;
                input.placeholder = connected
                    ? 'Write a message to SARI Admin…'
                    : 'Connecting to SARI Admin…';
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
                setRealtimeBadge('fallback');
                startFallbackPolling();
                return;
            }

            try {
                const channel = window.Echo.channel(channelName);
                const refresh = (event = {}) => {
                    if (event?.conversation_uuid && String(event.conversation_uuid) !== String(conversationUuid)) {
                        return;
                    }
                    loadDetail(false);
                };

                channel
                    .listen('.platform.message', refresh)
                    .listen('.platform.reaction', refresh);

                if (typeof channel.subscribed === 'function') {
                    channel.subscribed(() => {
                        setRealtimeBadge('live');
                        stopFallbackPolling();
                    });
                } else {
                    setRealtimeBadge('live');
                    stopFallbackPolling();
                }

                if (typeof channel.error === 'function') {
                    channel.error(() => {
                        setRealtimeBadge('fallback');
                        startFallbackPolling();
                    });
                }
            } catch (_) {
                setRealtimeBadge('fallback');
                startFallbackPolling();
            }
        }

        async function loadDetail(initial = false) {
            if (!conversationUuid) return;

            if (loadingDetail) {
                detailReloadPending = true;
                return;
            }

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
                setSupportConnectionState('connected');
                setError('');
            } catch (error) {
                setError(error.message || 'Unable to load messages.');
                if (!navigator.onLine) setRealtimeBadge('offline');
            } finally {
                loadingDetail = false;
                root.dataset.ready = '1';

                if (detailReloadPending) {
                    detailReloadPending = false;
                    queueMicrotask(() => loadDetail(false));
                }
            }
        }

        async function requestAssistantReply(triggerMessage) {
            if (
                !assistantEnabled
                || !conversationUuid
                || !triggerMessage?.id
            ) {
                hideAssistantTyping();
                return null;
            }

            assistantRequestController?.abort();
            assistantRequestController = new AbortController();
            const controller = assistantRequestController;
            const timeout = window.setTimeout(() => controller.abort(), 5000);

            try {
                const response = await fetch(
                    `${apiBase}/conversations/${encodeURIComponent(conversationUuid)}/assistant-reply`,
                    {
                        method: 'POST',
                        credentials: 'same-origin',
                        signal: controller.signal,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            trigger_message_id: Number(triggerMessage.id),
                        }),
                    }
                );

                const data = await parseResponse(response);

                if (!response.ok) {
                    const validationMessage = Object.values(data.errors || {})?.[0]?.[0];
                    throw new Error(data.message || validationMessage || 'SARI Assistant could not respond.');
                }

                if (data?.message) {
                    upsertMessage(data.message, true);
                    setError('');
                    return data.message;
                }

                await loadDetail(false);
                return null;
            } catch (error) {
                if (error?.name !== 'AbortError') {
                    console.warn('SARI Assistant reply request failed:', error);
                }

                hideAssistantTyping();
                startAssistantBurstRefresh();

                if (error?.name === 'AbortError') {
                    setError('Your message was sent to SARI Admin Support. The automatic receipt confirmation is taking longer than expected, but a human administrator can still reply in this conversation.');
                }

                return null;
            } finally {
                window.clearTimeout(timeout);

                if (assistantRequestController === controller) {
                    assistantRequestController = null;
                }

                hideAssistantTyping();
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

            const delays = [120, 280, 450, 700, 1000, 1400, 2000, 2800, 3800, 5000];

            const tick = async () => {
                assistantBurstAttempt += 1;
                await loadDetail(false);

                const latest = currentMessages[currentMessages.length - 1];
                const receivedReply = latest && latest.sender_role === 'admin';

                if (receivedReply || assistantBurstAttempt >= delays.length) {
                    stopAssistantBurstRefresh();
                    return;
                }

                assistantBurstTimer = window.setTimeout(
                    tick,
                    delays[Math.min(assistantBurstAttempt, delays.length - 1)]
                );
            };

            assistantBurstTimer = window.setTimeout(tick, delays[0]);
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

            if (sendingMessage) return;

            if (sellerChatBlocked) {
                lockComposer(blockedReason?.textContent || 'Messaging is restricted by SARI Admin.');
                return;
            }

            const body = (input?.value || '').trim();
            const file = attachment?.files?.[0] || null;

            if (!body && !file) {
                setError('Write a message or attach a file.');
                input?.focus();
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

            sendingMessage = true;
            if (sendButton) sendButton.disabled = true;

            const submittedBody = body;
            const optimisticMessage = makeOptimisticMessage(submittedBody, file);
            const optimisticToken = optimisticMessage._optimisticToken;

            // Show the outgoing message immediately. The API response replaces
            // this temporary row with the authoritative saved message.
            upsertMessage(optimisticMessage, true);

            if (input && submittedBody) {
                input.value = '';
                resizeComposerInput();
            }

            try {
                const payload = new FormData();
                if (submittedBody) payload.append('body', submittedBody);
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

                removeOptimisticMessage(optimisticToken);

                if (data?.message) {
                    upsertMessage(data.message, true);
                } else {
                    loadDetail(false);
                }

                if (attachment) attachment.value = '';
                attachmentName?.classList.add('hidden');
                if (attachmentName) attachmentName.textContent = '';

                /*
                | The acknowledgement is created by the same server-side send
                | flow. Reconcile once; no second assistant endpoint is needed.
                */
                await loadDetail(false);
                input?.focus();
            } catch (error) {
                removeOptimisticMessage(optimisticToken);

                if (submittedBody && input) {
                    const newerDraft = input.value.trim();
                    input.value = newerDraft ? `${submittedBody}\n${newerDraft}` : submittedBody;
                    resizeComposerInput();
                }

                setError(error.message || 'Unable to send message.');
            } finally {
                sendingMessage = false;
                if (!sellerChatBlocked && sendButton) sendButton.disabled = false;
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
        }, { signal: platformChatSignal });

        searchInput?.addEventListener('input', applySearch);
        searchClear?.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            applySearch();
            searchInput?.focus();
        });

        input?.addEventListener('input', function () {
            resizeComposerInput();
            if (errorBox && !errorBox.classList.contains('hidden')) setError('');
        });

        input?.addEventListener('keydown', function (event) {
            if (event.isComposing) return;
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                form?.requestSubmit();
            }
        });

        window.addEventListener('sari:realtime-state', (event) => {
            const state = String(event?.detail?.state || '');

            if (state === 'live') {
                setRealtimeBadge('live');
                stopFallbackPolling();
                loadDetail(false);
            } else if (state === 'connecting') {
                if (statusEl?.dataset.state !== 'live') {
                    setRealtimeBadge('connecting');
                }
            } else if (state === 'fallback') {
                setRealtimeBadge('fallback');
                startFallbackPolling();
            }
        }, { signal: platformChatSignal });

        window.addEventListener('offline', () => {
            setRealtimeBadge('offline');
            stopFallbackPolling();
        }, { signal: platformChatSignal });

        window.addEventListener('online', () => {
            setRealtimeBadge(window.Echo ? 'syncing' : 'fallback');
            loadDetail(false);

            if (statusEl?.dataset.state !== 'live') {
                startFallbackPolling();
            }
        }, { signal: platformChatSignal });

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) return;

            loadDetail(false);
            if (statusEl?.dataset.state !== 'live') {
                startFallbackPolling();
            }
        }, { signal: platformChatSignal });

        resizeComposerInput();

        async function start() {
            setSupportConnectionState('connecting');

            try {
                const connected = await ensureSupportConversation();
                if (!connected || !conversationUuid) {
                    throw new Error('Unable to establish the SARI Support conversation.');
                }

                await loadDetail(true);
                setSupportConnectionState('connected');

                if (statusEl?.dataset.state !== 'live') {
                    startFallbackPolling();
                }
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

            if (assistantRequestController) {
                assistantRequestController.abort();
                assistantRequestController = null;
            }

            stopFallbackPolling();
            if (realtimeChannel && window.Echo) {
                try { window.Echo.leave(realtimeChannel); } catch (_) {}
            }

            platformChatLifecycle.abort();
            root.dataset.initialized = '0';
        };

        document.addEventListener(
            'livewire:navigating',
            cleanup,
            { once: true, signal: platformChatSignal }
        );
        window.addEventListener(
            'beforeunload',
            cleanup,
            { once: true, signal: platformChatSignal }
        );
    }

    /* Livewire evaluates this body script when the page arrives. Boot once here;
       bootSellerPlatformChat registers its own one-shot navigation cleanup. */
    const bootSelectedSellerConversation = () => {
        if (buyerMode) {
            bootSellerBuyerThread();
            return;
        }

        bootSellerPlatformChat();
    };

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            bootSelectedSellerConversation,
            { once: true }
        );
    } else {
        bootSelectedSellerConversation();
    }
})();
</script>
@endpush
