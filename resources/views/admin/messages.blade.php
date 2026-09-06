@extends('layouts.admin')

@section('title', 'Chat / Messaging — SARI Admin')
@section('page-title', 'Chat / Messaging')

@section('content')

<style>
    /*
    |--------------------------------------------------------------------------
    | SARI ADMIN CHAT — RESPONSIVE TYPOGRAPHY + CLEAN DEPTH
    |--------------------------------------------------------------------------
    | Visual-only layer. No routes, controllers, models or chat logic changed.
    */
    #sariAdminChatUi {
        --sari-type-xs: clamp(10px, 0.56vw, 12px);
        --sari-type-sm: clamp(11px, 0.66vw, 13px);
        --sari-type-md: clamp(12px, 0.74vw, 14px);
        --sari-type-lg: clamp(15px, 0.90vw, 18px);
        --sari-type-xl: clamp(17px, 1.05vw, 21px);
        --sari-line: #e8e3dc;
        --sari-muted: #817a71;
        --sari-surface: #ffffff;
        --sari-soft: #faf9f7;
        --sari-soft-2: #f7f6f3;
        --sari-gold: #c99128;
        font-size: var(--sari-type-md);
    }

    #sariAdminChatUi .sari-chat-heading {
        font-size: var(--sari-type-lg) !important;
        line-height: 1.25;
        letter-spacing: -0.02em;
    }

    #sariAdminChatUi .sari-chat-body {
        font-size: var(--sari-type-md) !important;
        line-height: 1.55;
    }

    #sariAdminChatUi .sari-chat-small {
        font-size: var(--sari-type-sm) !important;
        line-height: 1.45;
    }

    #sariAdminChatUi .sari-chat-meta {
        font-size: var(--sari-type-xs) !important;
        line-height: 1.35;
    }

    #sariAdminChatUi .sari-chat-control {
        font-size: var(--sari-type-sm) !important;
    }

    #sariAdminChatUi .sari-message-body {
        font-size: var(--sari-type-md) !important;
        line-height: 1.55 !important;
    }

    #sariAdminChatUi .sari-main-workspace {
        box-shadow: 0 18px 48px rgba(31, 27, 22, 0.075);
    }

    #sariAdminChatUi .sari-side-panel {
        background: #fbfbfa;
    }

    #sariAdminChatUi .sari-chat-stage {
        background: #faf9f6;
    }

    #sariAdminChatUi .sari-detail-card {
        background: #fff;
        box-shadow: 0 8px 22px rgba(31, 27, 22, 0.045);
    }

    #sariAdminChatUi .sari-composer {
        box-shadow: 0 10px 28px rgba(31, 27, 22, 0.055);
    }

    #sariAdminChatUi .sari-message-card {
        box-shadow: 0 7px 18px rgba(31, 27, 22, 0.055);
    }

    @media (max-width: 1023px) {
        #sariAdminChatUi {
            --sari-type-xs: clamp(10px, 1.05vw, 11px);
            --sari-type-sm: clamp(11px, 1.2vw, 12.5px);
            --sari-type-md: clamp(12px, 1.35vw, 14px);
            --sari-type-lg: clamp(15px, 1.65vw, 18px);
        }
    }

    @media (max-width: 640px) {
        #sariAdminChatUi {
            --sari-type-xs: 10px;
            --sari-type-sm: 11.5px;
            --sari-type-md: 13px;
            --sari-type-lg: 16px;
        }
    }
</style>

<div id="sariAdminChatUi" class="mx-auto w-full max-w-[1800px]">

    @php
        /*
        |--------------------------------------------------------------------------
        | CHAT REACTION DISPLAY DATA
        |--------------------------------------------------------------------------
        |
        | Read-only on the Admin screen. This does NOT replace or modify the old
        | chat controller/backend. Seller reactions saved in chat_message_reactions
        | are grouped here so Admin can actually see them under the correct message.
        |
        */
        $adminReactionMap = collect();

        if (\Illuminate\Support\Facades\Schema::hasTable('chat_message_reactions') && isset($messages) && $messages->isNotEmpty()) {
            $adminMessageIds = $messages->pluck('id')->filter()->values();

            if ($adminMessageIds->isNotEmpty()) {
                $adminReactionMap = \Illuminate\Support\Facades\DB::table('chat_message_reactions')
                    ->whereIn('chat_message_id', $adminMessageIds)
                    ->orderBy('id')
                    ->get()
                    ->groupBy('chat_message_id');
            }
        }


        $selectedChatRestriction = null;
        $selectedChatBlocked = false;
        $selectedModerationActions = collect();

        if ($selectedSeller && \Illuminate\Support\Facades\Schema::hasTable('seller_chat_restrictions')) {
            $selectedChatRestriction = \App\Models\SellerChatRestriction::query()
                ->where('seller_account_id', $selectedSeller->id)
                ->first();
            $selectedChatBlocked = (bool) ($selectedChatRestriction?->is_blocked);
        }

        if ($selectedSeller && \Illuminate\Support\Facades\Schema::hasTable('seller_chat_moderation_actions')) {
            $selectedModerationActions = \App\Models\SellerChatModerationAction::query()
                ->where('seller_account_id', $selectedSeller->id)
                ->latest('created_at')
                ->limit(5)
                ->get();
        }
    @endphp

    @if (session('success'))
        <div class="mb-5 rounded-[18px] border border-[#d5e5dc] bg-[#f3f8f5] px-5 py-4 text-[12px] font-medium text-[#56816a]">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-[18px] border border-[#ead7d7] bg-[#fdf5f5] px-5 py-4 text-[12px] text-[#a45f5f]">{{ $errors->first() }}</div>
    @endif

    {{-- WORKSPACE --}}
    <section class="sari-main-workspace overflow-hidden rounded-[22px] border border-[#e7e2da] bg-white">
        <div class="grid min-h-[690px] grid-cols-1 lg:grid-cols-[310px_1fr] 2xl:grid-cols-[320px_1fr_300px]">

            {{-- SELLERS --}}
            <aside class="sari-side-panel border-b border-[#eee8df] lg:border-b-0 lg:border-r">
                <div class="border-b border-[#eee8df] p-4">
                    <h3 class="sari-chat-heading font-bold text-[#2b261f]">Seller Conversations</h3>
                    <p class="sari-chat-meta mt-1 text-[#948b7f]">{{ $sellers->count() }} seller account{{ $sellers->count() === 1 ? '' : 's' }}</p>
                    <div class="relative mt-4"><input id="adminSellerSearch" type="search" placeholder="Search sellers..." class="sari-chat-control h-11 w-full rounded-xl border border-[#e5e0d9] bg-white px-4 text-[#3f3932] shadow-[0_5px_15px_rgba(31,27,22,0.035)] outline-none focus:border-[#c99a3d] focus:ring-4 focus:ring-[#c99a3d]/10"></div>
                </div>

                <div id="adminSellerList" class="max-h-[610px] overflow-y-auto">
                    @forelse ($sellers as $seller)
                        @php $active = $selectedSeller && $selectedSeller->id === $seller->id; @endphp
                        <a href="{{ route('admin.messages', ['seller' => $seller->id]) }}" data-seller-row data-seller-name="{{ strtolower(($seller->store_name ?: '') . ' ' . $seller->email) }}" data-seller-id="{{ $seller->id }}" class="block w-full border-b border-[#f0ebe4] px-4 py-4 text-left transition {{ $active ? 'border-l-2 border-l-[#d99a1b] bg-[#fbf6ea]' : 'border-l-2 border-l-transparent hover:bg-[#f8f7f4]' }}">
                            <div class="flex gap-3">
                                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#f3f6f8] text-[10px] font-bold text-[#657f94]">{{ strtoupper(substr($seller->store_name ?: 'SS', 0, 2)) }}</div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2"><div class="min-w-0"><p class="sari-chat-small truncate font-semibold text-[#312b25]">{{ $seller->store_name ?: $seller->email }}</p><p class="sari-chat-meta mt-0.5 truncate text-[#948b7f]">{{ $seller->email }}</p></div><span data-unread-badge class="{{ $seller->unread_messages_count ? 'grid' : 'hidden' }} h-5 min-w-[20px] place-items-center rounded-full bg-[#c99128] px-1.5 text-[10px] font-bold text-white">{{ $seller->unread_messages_count }}</span></div>
                                    <p class="sari-chat-meta mt-2 text-[#8d857a]">{{ $seller->message_count }} message{{ $seller->message_count === 1 ? '' : 's' }} • {{ $seller->warning_count }} warning{{ $seller->warning_count === 1 ? '' : 's' }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-[10px] text-[#91887d]">No seller accounts yet.</div>
                    @endforelse
                </div>
            </aside>

            {{-- ACTIVE CHAT --}}
            <div class="flex min-h-[690px] flex-col">
                @if ($selectedSeller)
                    <div class="flex items-center justify-between gap-4 border-b border-[#eee8df] px-4 py-4 sm:px-5">
                        <div class="flex items-center gap-3">
                            <div class="grid h-11 w-11 place-items-center rounded-full bg-[#f3f6f8] text-[10px] font-bold text-[#657f94]">{{ strtoupper(substr($selectedSeller->store_name ?: 'SS', 0, 2)) }}</div>
                            <div><p class="sari-chat-small font-bold text-[#2f2923]">{{ $selectedSeller->store_name ?: $selectedSeller->email }}</p><div class="mt-1 flex items-center gap-2"><span class="sari-chat-meta text-[#8e8579]">Seller</span><span class="h-1 w-1 rounded-full bg-[#c8c1b8]"></span><span class="sari-chat-meta font-medium {{ $selectedSeller->isSuspended() ? 'text-[#a96565]' : 'text-[#56816a]' }}">{{ ucfirst($selectedSeller->account_status) }}</span></div></div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($selectedChatBlocked)
                                <span class="sari-chat-meta rounded-full border border-[#e9cccc] bg-[#fff5f5] px-3 py-1.5 font-semibold text-[#a65f5f]">Chat Blocked</span>
                            @endif

                            <div class="relative">
                                <button id="adminSellerActionsToggle" type="button" class="grid h-10 w-10 place-items-center rounded-xl border border-[#e6dfd5] bg-white text-[#6d655b] transition hover:bg-[#fcf8f1]" aria-label="Seller actions" aria-expanded="false">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor"><circle cx="5" cy="12" r="1.5"></circle><circle cx="12" cy="12" r="1.5"></circle><circle cx="19" cy="12" r="1.5"></circle></svg>
                                </button>

                                <div id="adminSellerActionsMenu" class="absolute right-0 top-12 z-50 hidden w-[230px] overflow-hidden rounded-[15px] border border-[#e7e0d7] bg-white p-1.5 shadow-[0_18px_50px_rgba(41,31,20,.14)]">
                                    <a href="{{ route('admin.seller-compliance') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 sari-chat-small font-medium text-[#514a42] hover:bg-[#faf7f2]">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3 4 7v5c0 5 3.5 7.7 8 9 4.5-1.3 8-4 8-9V7l-8-4Z"></path><path d="m9 12 2 2 4-4"></path></svg>
                                        Open Compliance
                                    </a>
                                    <div class="my-1 border-t border-[#f0ebe4]"></div>
                                    <button type="button" data-admin-action-open="warning" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left sari-chat-small font-medium text-[#8c651f] hover:bg-[#fff9ee]">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 3 20h18L12 3Z"></path><path d="M12 9v5"></path><path d="M12 17h.01"></path></svg>
                                        Issue Warning
                                    </button>
                                    <button type="button" data-admin-action-open="suspend" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left sari-chat-small font-medium text-[#765f87] hover:bg-[#f8f4fa]">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="m8 8 8 8"></path></svg>
                                        Suspend 30 Days
                                    </button>
                                    <div class="my-1 border-t border-[#f0ebe4]"></div>
                                    @if($selectedChatBlocked)
                                        <form method="POST" action="{{ route('admin.messages.unblock', $selectedSeller) }}">
                                            @csrf
                                            <button class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left sari-chat-small font-medium text-[#56816a] hover:bg-[#f3f8f5]">
                                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="10" width="14" height="10" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 7.5-2"></path></svg>
                                                Unblock Seller Chat
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" data-admin-action-open="block" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left sari-chat-small font-medium text-[#a65f5f] hover:bg-[#fff6f6]">
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="10" width="14" height="10" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path></svg>
                                            Block Seller Chat
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="adminChatMessages" class="sari-chat-stage flex-1 overflow-y-auto px-4 py-5 sm:px-6">
                        @if ($messages->isEmpty())
                            <div id="adminEmptyChat" class="flex h-full min-h-[360px] items-center justify-center text-center"><div><div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#f3f6f8] text-[#657f94]"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path></svg></div><p class="sari-chat-small mt-4 font-bold text-[#4f473e]">No messages yet</p><p class="sari-chat-meta mt-1 text-[#958c80]">Send the first message to this seller.</p></div></div>
                        @endif

                        @foreach ($messages as $message)
                            @php $isBotMessage = (bool) ($message->is_bot ?? false); @endphp
                            <div data-message-id="{{ $message->id }}" data-message-is-bot="{{ $isBotMessage ? 'true' : 'false' }}" class="mt-5 {{ $message->sender_role === 'admin' ? 'flex justify-end' : 'flex items-end gap-2.5' }}">
                                @if ($message->sender_role === 'seller')<div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#f3f6f8] text-[8px] font-bold text-[#657f94]">{{ strtoupper(substr($selectedSeller->store_name ?: 'SS', 0, 2)) }}</div>@endif
                                <div class="max-w-[78%] sm:max-w-[65%]">
                                    @if ($message->body)
                                        @if($isBotMessage)<div class="sari-chat-meta mb-1.5 text-right font-semibold uppercase tracking-[.08em] text-[#6e899c]">SARI Support Bot</div>@endif
                                        <div class="sari-message-body sari-message-card rounded-[16px] px-4 py-3 {{ $message->sender_role === 'admin' ? ($isBotMessage ? 'rounded-br-[5px] border border-[#dce8ef] bg-[#f4f8fa] text-[#526a79]' : 'rounded-br-[5px] bg-[#c99128] text-white') : 'rounded-bl-[5px] border border-[#e9e2d9] bg-white text-[#5f574d]' }}">{{ $message->body }}</div>
                                    @endif
                                    @if ($message->attachment_path)
                                        <div class="mt-2 overflow-hidden rounded-[14px] border border-[#e9e2d9] bg-white p-2">
                                            @if ($message->attachmentIsImage())<a href="{{ route('chat.attachments.show', $message) }}" target="_blank"><img src="{{ route('chat.attachments.show', $message) }}" alt="{{ $message->attachment_name }}" class="max-h-[260px] w-full rounded-xl object-contain"></a>
                                            @else<a href="{{ route('chat.attachments.show', $message) }}" target="_blank" class="sari-chat-small block rounded-xl bg-[#fcfaf7] p-3 font-semibold text-[#50483f]">{{ $message->attachment_name }}</a>@endif
                                        </div>
                                    @endif
                                    @php
                                        $adminMessageReactionRows = collect($adminReactionMap->get($message->id, collect()));
                                        $adminMessageReactionGroups = $adminMessageReactionRows
                                            ->groupBy('emoji')
                                            ->map(fn ($items, $emoji) => [
                                                'emoji' => $emoji,
                                                'count' => $items->count(),
                                            ])
                                            ->values();
                                    @endphp

                                    <div
                                        data-admin-reaction-summary
                                        class="mt-2 flex flex-wrap items-center gap-1 {{ $message->sender_role === 'admin' ? 'justify-end' : 'justify-start' }}"
                                    >
                                        @foreach ($adminMessageReactionGroups as $reaction)
                                            <span class="sari-chat-meta inline-flex h-6 items-center gap-1 rounded-full border border-[#e7e0d7] bg-white px-2 text-[#6f675d] shadow-sm">
                                                <span class="text-[12px] leading-none">{{ $reaction['emoji'] }}</span>
                                                <span class="font-semibold">{{ $reaction['count'] }}</span>
                                            </span>
                                        @endforeach
                                    </div>

                                    <p
                                        data-message-time
                                        data-sent-at="{{ $message->created_at?->toIso8601String() }}"
                                        class="sari-chat-meta mt-1.5 {{ $message->sender_role === 'admin' ? 'text-right' : '' }} text-[#9c9388]"
                                        title="{{ $message->created_at?->format('M d, Y h:i A') }}"
                                    >
                                        {{ $message->created_at?->format('h:i A') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($selectedModerationActions->isNotEmpty())
                        <div class="border-t border-[#eee8df] bg-[#fcfbf9] px-4 py-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="sari-chat-meta font-semibold uppercase tracking-[.08em] text-[#9a9186]">Recent Admin Actions</span>
                                @foreach($selectedModerationActions->take(3) as $action)
                                    <span class="sari-chat-meta rounded-full border border-[#e8e1d8] bg-white px-2.5 py-1 text-[#6f675d]" title="{{ $action->reason }}">{{ str_replace('_', ' ', ucfirst($action->action_type)) }} · {{ $action->created_at?->format('M d, h:i A') }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="border-t border-[#eee8df] bg-white p-4">
                        <form id="adminChatForm" method="POST" action="{{ route('admin.messages.send', $selectedSeller) }}" enctype="multipart/form-data" class="sari-composer rounded-[16px] border border-[#e4dfd8] bg-white p-3.5 transition focus-within:border-[#c99a3d] focus-within:ring-4 focus-within:ring-[#c99a3d]/10">
                            @csrf
                            <textarea id="adminChatInput" name="message" rows="3" placeholder="Write a message to seller..." class="sari-chat-body w-full resize-none bg-transparent leading-6 text-[#3e3831] outline-none placeholder:text-[#aaa197]"></textarea>
                            <div id="adminAttachmentName" class="sari-chat-small mt-2 hidden rounded-xl border border-[#e8e1d7] bg-white px-3 py-2 text-[#62594e]"></div>
                            <div class="mt-2 flex flex-col gap-3 border-t border-[#eee8df] pt-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-2"><label class="grid h-9 w-9 cursor-pointer place-items-center rounded-lg text-[#756d63] hover:bg-[#f5efe5] hover:text-[#a8731f]"><input id="adminChatAttachment" name="attachment" type="file" accept="image/*,.pdf,.doc,.docx,.zip,.txt" class="hidden"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 12.5 14.5 6a3 3 0 0 1 4.2 4.2l-8 8a5 5 0 0 1-7.1-7.1l8.3-8.3"></path></svg></label><span class="sari-chat-meta text-[#958c80]">Attachments up to 8 MB</span></div>
                                <button id="adminChatSend" type="submit" class="sari-chat-control inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[#c99128] px-5 font-semibold text-white shadow-[0_7px_18px_rgba(201,145,40,0.17)] hover:bg-[#b47e1e] disabled:opacity-50">Send Message<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m4 4 17 8-17 8 3-8-3-8Z"></path><path d="M7 12h14"></path></svg></button>
                            </div>
                        </form>
                        <p id="adminChatError" class="sari-chat-small mt-2 hidden text-[#a45f5f]"></p>
                    </div>
                @else
                    <div class="flex flex-1 items-center justify-center p-8 text-center"><div><p class="sari-chat-heading font-bold text-[#4f473e]">No seller selected</p><p class="sari-chat-small mt-2 text-[#958c80]">A seller conversation will appear here.</p></div></div>
                @endif
            </div>

            {{-- DETAILS --}}
            <aside class="sari-side-panel hidden border-l border-[#eee8df] 2xl:block">
                <div class="border-b border-[#eee8df] p-5"><p class="sari-chat-small font-bold text-[#312b25]">Seller Details</p><p class="sari-chat-meta mt-1 text-[#948b7f]">Support and compliance context</p></div>
                @if ($selectedSeller)
                    <div class="p-5">
                        <div class="flex flex-col items-center text-center"><div class="grid h-16 w-16 place-items-center rounded-full bg-[#f3f6f8] text-[16px] font-bold text-[#657f94]">{{ strtoupper(substr($selectedSeller->store_name ?: 'SS', 0, 2)) }}</div><p class="sari-chat-heading mt-3 font-bold text-[#302a24]">{{ $selectedSeller->store_name ?: $selectedSeller->email }}</p><p class="sari-chat-meta mt-1 text-[#92897e]">{{ $selectedSeller->email }}</p><span class="sari-chat-meta mt-3 rounded-full border border-[#dce5ed] bg-[#f4f7fa] px-2.5 py-1 font-semibold text-[#617d96]">Seller</span></div>
                        <div class="mt-5 space-y-3">
                            <div class="sari-detail-card rounded-[14px] border border-[#e8e3dc] p-4"><p class="sari-chat-meta text-[#948b7f]">Account Status</p><p class="sari-chat-small mt-2 font-semibold {{ $selectedSeller->isSuspended() ? 'text-[#a96565]' : 'text-[#56816a]' }}">{{ ucfirst($selectedSeller->account_status) }}</p></div>
                            <div class="sari-detail-card rounded-[14px] border border-[#e8e3dc] p-4"><p class="sari-chat-meta text-[#948b7f]">Warnings</p><p class="sari-chat-heading mt-2 font-bold text-[#a8731f]">{{ $selectedSeller->warning_count }} / 3</p></div>
                            @if ($selectedSeller->isSuspended())<div class="rounded-[14px] border border-[#ead8d8] bg-[#fcf6f6] p-4"><p class="text-[9px] text-[#a96565]">Suspended Until</p><p class="mt-2 text-[10px] font-semibold text-[#7f5151]">{{ $selectedSeller->suspended_until?->format('M d, Y') }}</p></div>@endif
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </section>
    <div class="h-5"></div>
</div>


@if($selectedSeller)
    <div id="adminChatActionModal" class="fixed inset-0 z-[140] hidden items-center justify-center bg-black/35 p-4 backdrop-blur-[2px]">
        <div class="w-full max-w-[520px] rounded-[22px] border border-[#e8e1d8] bg-white p-5 shadow-[0_30px_90px_rgba(38,30,18,.22)] sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="sari-chat-meta font-bold uppercase tracking-[.12em] text-[#9b7340]">Seller Action</p>
                    <h3 id="adminChatActionTitle" class="mt-2 text-[clamp(18px,1.1vw,22px)] font-bold tracking-[-.03em] text-[#302a24]">Confirm Action</h3>
                    <p id="adminChatActionText" class="sari-chat-body mt-2 text-[#81786c]"></p>
                </div>
                <button type="button" data-admin-action-close class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-[#e7e0d7] text-[#756d63] hover:bg-[#faf7f2]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m7 7 10 10"></path><path d="m17 7-10 10"></path></svg>
                </button>
            </div>
            <form id="adminChatActionForm" method="POST" class="mt-5">
                @csrf
                <label class="sari-chat-small mb-2 block font-semibold text-[#5d554c]">Reason</label>
                <textarea id="adminChatActionReason" name="reason" rows="4" required minlength="5" maxlength="1000" class="sari-chat-body w-full resize-none rounded-[14px] border border-[#e6dfd5] bg-[#fcfbf9] px-4 py-3 leading-6 text-[#403a33] outline-none focus:border-[#c99128] focus:ring-4 focus:ring-[#c99128]/10" placeholder="Enter a clear reason for this action..."></textarea>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" data-admin-action-close class="sari-chat-control h-10 rounded-xl border border-[#e6dfd5] bg-white px-4 font-semibold text-[#62594e] hover:bg-[#faf8f4]">Cancel</button>
                    <button id="adminChatActionSubmit" type="submit" class="sari-chat-control h-10 rounded-xl bg-[#c99128] px-5 font-semibold text-white hover:bg-[#b47e1e]">Confirm</button>
                </div>
            </form>
        </div>
    </div>
@endif

@endsection

@push('scripts')
@php
    $selectedSellerNameForJs = $selectedSeller
        ? ($selectedSeller->store_name ?: $selectedSeller->email)
        : null;

    $adminActionUrlsForJs = [];

    if ($selectedSeller) {
        $adminActionUrlsForJs = [
            'warning' => route('admin.messages.warning', $selectedSeller),
            'suspend' => route('admin.messages.suspend', $selectedSeller),
            'block' => route('admin.messages.block', $selectedSeller),
        ];
    }
@endphp
<script>
(function () {
    window.__SARI_ADMIN_MESSAGES_CLEANUP__?.();

    window.__SARI_ADMIN_ACTIVE_SELLER_ID__ =
        {{ $selectedSeller ? (int) $selectedSeller->id : 'null' }};
    const selectedSellerId = {{ $selectedSeller ? (int) $selectedSeller->id : 'null' }};
    const channelName = @json($adminChannel);
    const messages = document.getElementById('adminChatMessages');
    const form = document.getElementById('adminChatForm');
    const input = document.getElementById('adminChatInput');
    const attachment = document.getElementById('adminChatAttachment');
    const attachmentName = document.getElementById('adminAttachmentName');
    const sendButton = document.getElementById('adminChatSend');
    const errorBox = document.getElementById('adminChatError');
    const status = document.getElementById('adminRealtimeStatus');
    const actionsToggle = document.getElementById('adminSellerActionsToggle');
    const actionsMenu = document.getElementById('adminSellerActionsMenu');
    const actionModal = document.getElementById('adminChatActionModal');
    const actionForm = document.getElementById('adminChatActionForm');
    const actionTitle = document.getElementById('adminChatActionTitle');
    const actionText = document.getElementById('adminChatActionText');
    const actionReason = document.getElementById('adminChatActionReason');
    const actionSubmit = document.getElementById('adminChatActionSubmit');
    const selectedSellerName = @json($selectedSellerNameForJs);
    const adminActionUrls = @json($adminActionUrlsForJs);

    const escapeHtml = (value) => String(value ?? '').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#039;');
    const scrollBottom = () => { if (messages) messages.scrollTop = messages.scrollHeight; };
    let reactionSyncBusy = false;
    const adminMessageIntervals = [];

    const onDocumentKeydown = function (event) {
        if (event.key === 'Escape') {
            closeActionModal();
            closeSellerActionsMenu();
        }
    };

    const onVisibilityChange = function () {
        if (!document.hidden) {
            syncAdminReactionSummaries();
        }
    };

    function formatLocalMessageTime(value) {
        if (!value) return '';

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '';

        return new Intl.DateTimeFormat('en-PH', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        }).format(date);
    }

    function refreshVisibleMessageTimes(root = document) {
        root.querySelectorAll('[data-message-time][data-sent-at]').forEach((element) => {
            const formatted = formatLocalMessageTime(element.dataset.sentAt);
            if (formatted) element.textContent = formatted;
        });
    }

    scrollBottom();
    refreshVisibleMessageTimes();

    function closeSellerActionsMenu() {
        actionsMenu?.classList.add('hidden');
        actionsToggle?.setAttribute('aria-expanded', 'false');
    }

    actionsToggle?.addEventListener('click', function (event) {
        event.stopPropagation();
        const willOpen = actionsMenu?.classList.contains('hidden');
        actionsMenu?.classList.toggle('hidden', !willOpen);
        actionsToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
    });

    actionsMenu?.addEventListener('click', function (event) { event.stopPropagation(); });
    document.addEventListener('click', closeSellerActionsMenu);

    const actionConfig = {
        warning: {
            title: 'Issue Seller Warning',
            text: `Issue a compliance warning to ${selectedSellerName || 'this seller'}. Warning 3/3 automatically triggers a 30-day suspension.`,
            button: 'Issue Warning',
            buttonClass: 'sari-chat-control h-10 rounded-xl bg-[#b98527] px-5 font-semibold text-white hover:bg-[#a9761e]',
        },
        suspend: {
            title: 'Suspend Seller for 30 Days',
            text: `Temporarily suspend ${selectedSellerName || 'this seller'} from seller privileges for 30 days. The official support history remains available.`,
            button: 'Suspend Seller',
            buttonClass: 'sari-chat-control h-10 rounded-xl bg-[#765f87] px-5 font-semibold text-white hover:bg-[#665174]',
        },
        block: {
            title: 'Block Seller Chat',
            text: `Stop ${selectedSellerName || 'this seller'} from sending new messages to SARI Admin. Previous messages remain visible and Admin can unblock the chat later.`,
            button: 'Block Chat',
            buttonClass: 'sari-chat-control h-10 rounded-xl bg-[#a65f5f] px-5 font-semibold text-white hover:bg-[#935252]',
        },
    };

    document.querySelectorAll('[data-admin-action-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            const action = this.dataset.adminActionOpen;
            const config = actionConfig[action];
            const url = adminActionUrls[action];
            if (!config || !url || !actionModal || !actionForm) return;
            actionForm.action = url;
            actionTitle.textContent = config.title;
            actionText.textContent = config.text;
            actionSubmit.textContent = config.button;
            actionSubmit.className = config.buttonClass;
            actionReason.value = '';
            closeSellerActionsMenu();
            actionModal.classList.remove('hidden');
            actionModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            window.setTimeout(() => actionReason?.focus(), 50);
        });
    });

    function closeActionModal() {
        actionModal?.classList.add('hidden');
        actionModal?.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-admin-action-close]').forEach(button => button.addEventListener('click', closeActionModal));
    actionModal?.addEventListener('click', event => { if (event.target === actionModal) closeActionModal(); });
    document.addEventListener('keydown', onDocumentKeydown);

    async function pingAdminPresence() {
        try {
            await fetch(@json(route('admin.messages.presence')), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': @json(csrf_token()),
                    'Accept': 'application/json',
                },
            });
        } catch (_) {}
    }

    pingAdminPresence();
    adminMessageIntervals.push(window.setInterval(pingAdminPresence, 60000));

    function markAdminSupportOffline() {
        try {
            const data = new FormData();
            data.append('_token', @json(csrf_token()));
            data.append('online', '0');
            navigator.sendBeacon(@json(route('admin.messages.presence')), data);
        } catch (_) {}
    }

    window.addEventListener('pagehide', markAdminSupportOffline);

    document.getElementById('adminSellerSearch')?.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        document.querySelectorAll('[data-seller-row]').forEach(row => row.classList.toggle('hidden', q && !row.dataset.sellerName.includes(q)));
    });

    attachment?.addEventListener('change', function () {
        const file = this.files?.[0]; attachmentName.classList.toggle('hidden', !file); attachmentName.textContent = file ? `Attached: ${file.name}` : '';
    });

    function bumpUnread(sellerId) {
        const row = document.querySelector(`[data-seller-row][data-seller-id="${sellerId}"]`);
        if (!row) return;
        const badge = row.querySelector('[data-unread-badge]');
        if (!badge) return;
        const next = Number(badge.textContent || 0) + 1;
        badge.textContent = next; badge.classList.remove('hidden'); badge.classList.add('grid');
    }

    async function markSellerThreadRead(sellerId, fallbackCount = 0) {
        if (!sellerId) {
            return;
        }

        const row = document.querySelector(
            `[data-seller-row][data-seller-id="${sellerId}"]`
        );

        const badge = row?.querySelector(
            '[data-unread-badge]'
        );

        const unreadBefore =
            Number(badge?.textContent || fallbackCount || 0);

        try {
            const response = await fetch(
                `/admin/messages/${sellerId}/read`,
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN':
                            @json(csrf_token()),
                        'Accept': 'application/json'
                    }
                }
            );

            if (!response.ok) {
                return;
            }

            if (badge) {
                badge.textContent = '0';
                badge.classList.add('hidden');
                badge.classList.remove('grid');
            }

            window.dispatchEvent(
                new CustomEvent(
                    'sari:admin-thread-read',
                    {
                        detail: {
                            sellerId: Number(sellerId),
                            count: unreadBefore
                        }
                    }
                )
            );
        } catch (error) {
            console.error(
                'Unable to mark admin chat as read.',
                error
            );
        }
    }


    function appendMessage(data) {
        if (!data) return;
        if (Number(data.seller_id) !== Number(selectedSellerId)) {
            if (data.sender_role === 'seller') bumpUnread(data.seller_id);
            return;
        }
        if (!messages || document.querySelector(`[data-message-id="${data.id}"]`)) return;
        document.getElementById('adminEmptyChat')?.remove();
        const mine = data.sender_role === 'admin';
        const isBot = Boolean(data.is_bot);
        const wrapper = document.createElement('div'); wrapper.dataset.messageId = data.id; wrapper.dataset.messageIsBot = isBot ? 'true' : 'false'; wrapper.className = `mt-5 ${mine ? 'flex justify-end' : 'flex items-end gap-2.5'}`;
        let attach = '';
        if (data.attachment_url) {
            attach = (data.attachment_mime || '').startsWith('image/')
                ? `<div class="mt-2 overflow-hidden rounded-[14px] border border-[#e9e2d9] bg-white p-2"><a href="${escapeHtml(data.attachment_url)}" target="_blank"><img src="${escapeHtml(data.attachment_url)}" alt="${escapeHtml(data.attachment_name)}" class="max-h-[260px] w-full rounded-xl object-contain"></a></div>`
                : `<div class="mt-2 rounded-[14px] border border-[#e9e2d9] bg-white p-2"><a href="${escapeHtml(data.attachment_url)}" target="_blank" class="sari-chat-small block rounded-xl bg-[#fcfaf7] p-3 font-semibold text-[#50483f]">${escapeHtml(data.attachment_name || 'Attachment')}</a></div>`;
        }
        const sentAt = data.created_at || data.created_at_iso || data.sent_at || '';
        const initialTime = sentAt ? formatLocalMessageTime(sentAt) : escapeHtml(data.time || '');

        wrapper.innerHTML = `${mine ? '' : '<div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#f3f6f8] text-[8px] font-bold text-[#657f94]">SE</div>'}<div class="max-w-[78%] sm:max-w-[65%]">${isBot ? '<div class="sari-chat-meta mb-1.5 text-right font-semibold uppercase tracking-[.08em] text-[#6e899c]">SARI Support Bot</div>' : ''}${data.body ? `<div class="sari-message-body sari-message-card rounded-[16px] px-4 py-3 ${mine ? (isBot ? 'rounded-br-[5px] border border-[#dce8ef] bg-[#f4f8fa] text-[#526a79]' : 'rounded-br-[5px] bg-[#c99128] text-white') : 'rounded-bl-[5px] border border-[#e9e2d9] bg-white text-[#5f574d]'}">${escapeHtml(data.body)}</div>` : ''}${attach}<div data-admin-reaction-summary class="mt-2 flex flex-wrap items-center gap-1 ${mine ? 'justify-end' : 'justify-start'}"></div><p data-message-time ${sentAt ? `data-sent-at="${escapeHtml(sentAt)}"` : ''} class="mt-1.5 ${mine ? 'text-right' : ''} sari-chat-meta text-[#9c9388]">${initialTime}</p></div>`;
        messages.appendChild(wrapper);
        refreshVisibleMessageTimes(wrapper);
        scrollBottom();
        if (!mine) {
            markSellerThreadRead(
                selectedSellerId,
                1
            );
        }
    }

    async function syncAdminReactionSummaries() {
        if (!selectedSellerId || reactionSyncBusy || document.hidden) return;

        reactionSyncBusy = true;

        try {
            const syncUrl = new URL(window.location.href);
            syncUrl.searchParams.set('_reaction_sync', Date.now().toString());

            const response = await fetch(syncUrl.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache',
                },
                cache: 'no-store',
            });

            if (!response.ok) return;

            const html = await response.text();
            const remoteDocument = new DOMParser().parseFromString(html, 'text/html');

            remoteDocument.querySelectorAll('[data-message-id]').forEach((remoteMessage) => {
                const messageId = remoteMessage.dataset.messageId;
                if (!messageId) return;

                const localMessage = document.querySelector(`[data-message-id="${CSS.escape(messageId)}"]`);
                if (!localMessage) return;

                const remoteSummary = remoteMessage.querySelector('[data-admin-reaction-summary]');
                const localSummary = localMessage.querySelector('[data-admin-reaction-summary]');

                if (remoteSummary && localSummary && localSummary.innerHTML !== remoteSummary.innerHTML) {
                    localSummary.innerHTML = remoteSummary.innerHTML;
                }
            });
        } catch (error) {
            console.debug('Reaction display sync skipped.', error);
        } finally {
            reactionSyncBusy = false;
        }
    }

    // The old chat backend does not broadcast reaction updates yet.
    // This lightweight sync uses the already-protected Admin page itself,
    // so Seller reactions become visible to Admin without replacing old backend code.
    if (selectedSellerId) {
        adminMessageIntervals.push(
            window.setInterval(syncAdminReactionSummaries, 2000)
        );

        document.addEventListener(
            'visibilitychange',
            onVisibilityChange
        );
    }

    form?.addEventListener('submit', async function (event) {
        event.preventDefault(); errorBox.classList.add('hidden'); sendButton.disabled = true;
        try {
            const response = await fetch(form.action, {method:'POST', body:new FormData(form), headers:{'Accept':'application/json'}});
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || Object.values(data.errors || {})?.[0]?.[0] || 'Unable to send message.');
            appendMessage(data.message); form.reset(); attachmentName.classList.add('hidden'); attachmentName.textContent=''; input.focus();
        } catch (error) { errorBox.textContent = error.message; errorBox.classList.remove('hidden'); }
        finally { sendButton.disabled = false; }
    });

    /*
    |--------------------------------------------------------------------------
    | MARK CURRENTLY OPEN THREAD AS READ
    |--------------------------------------------------------------------------
    |
    | If this page was opened from the bell/sidebar notification,
    | clear that seller's unread count immediately.
    |
    */

    if (selectedSellerId) {
        const selectedRow = document.querySelector(
            `[data-seller-row][data-seller-id="${selectedSellerId}"]`
        );

        const selectedBadge =
            selectedRow?.querySelector(
                '[data-unread-badge]'
            );

        const initialUnread =
            Number(selectedBadge?.textContent || 0);

        if (initialUnread > 0) {
            markSellerThreadRead(
                selectedSellerId,
                initialUnread
            );
        }
    }


    let adminEchoChannel = null;

    if (window.Echo) {
        if (status) {
            status.textContent = 'Live';
        }

        adminEchoChannel = window.Echo.channel(channelName);
        adminEchoChannel.listen('.chat.message', appendMessage);
    } else if (status) {
        status.textContent = 'Saved mode';
    }

    let cleanedUp = false;

    function cleanupAdminMessagesPage() {
        if (cleanedUp) return;
        cleanedUp = true;

        adminMessageIntervals.forEach(function (intervalId) {
            window.clearInterval(intervalId);
        });

        document.removeEventListener(
            'click',
            closeSellerActionsMenu
        );

        document.removeEventListener(
            'keydown',
            onDocumentKeydown
        );

        document.removeEventListener(
            'visibilitychange',
            onVisibilityChange
        );

        window.removeEventListener(
            'pagehide',
            markAdminSupportOffline
        );

        if (adminEchoChannel?.stopListening) {
            adminEchoChannel.stopListening(
                '.chat.message',
                appendMessage
            );
        }

        document.body.classList.remove('overflow-hidden');

        if (
            Number(window.__SARI_ADMIN_ACTIVE_SELLER_ID__) ===
            Number(selectedSellerId)
        ) {
            window.__SARI_ADMIN_ACTIVE_SELLER_ID__ = null;
        }

        if (
            window.__SARI_ADMIN_MESSAGES_CLEANUP__ ===
            cleanupAdminMessagesPage
        ) {
            window.__SARI_ADMIN_MESSAGES_CLEANUP__ = null;
        }
    }

    window.__SARI_ADMIN_MESSAGES_CLEANUP__ =
        cleanupAdminMessagesPage;

    document.addEventListener(
        'livewire:navigating',
        cleanupAdminMessagesPage,
        { once: true }
    );
})();
</script>
@endpush
