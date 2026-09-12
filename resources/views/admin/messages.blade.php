@extends('layouts.admin')

@section('title', 'Messages — SARI Admin')
@section('page-title', 'Messages')

@section('content')
<div id="sariUniversalAdminMessages" class="mx-auto w-full max-w-[1840px]">
    <style>
        #sariUniversalAdminMessages {
            --sari-gold: #D29A28;
            --sari-gold-deep: #A97012;
            --sari-ink: #28231d;
            --sari-muted: #81796f;
            --sari-line: #e8e2d9;
            --sari-soft: #faf9f6;
            --sari-soft-2: #f5f3ef;
            --sari-green: #6F826A;
            --sari-teal: #5F7873;
            --sari-plum: #806F7F;
            --sari-red: #B86556;
            --sari-ivory: #FFF7E7;
            font-size: 14px;
        }

        #sariUniversalAdminMessages * {
            scrollbar-width: thin;
            scrollbar-color: #d9d2c8 transparent;
        }

        #sariUniversalAdminMessages .sari-shadow {
            box-shadow: 0 18px 48px rgba(42, 34, 24, 0.07);
        }

        #sariUniversalAdminMessages .sari-soft-shadow {
            box-shadow: 0 8px 24px rgba(42, 34, 24, 0.05);
        }

        #sariUniversalAdminMessages .sari-scroll::-webkit-scrollbar {
            width: 8px;
        }

        #sariUniversalAdminMessages .sari-scroll::-webkit-scrollbar-thumb {
            background: #d9d2c8;
            border-radius: 999px;
        }

        #sariUniversalAdminMessages .sari-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        #sariUniversalAdminMessages .sari-message-body {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        #sariUniversalAdminMessages .sari-role-admin { background:#fff4dd; color:#9a6815; border-color:#ecd5a7; }
        #sariUniversalAdminMessages .sari-role-buyer,
        #sariUniversalAdminMessages .sari-role-social_buyer { background:#f3f7fa; color:#5f7687; border-color:#dce6ed; }
        #sariUniversalAdminMessages .sari-role-seller { background:#fff7ea; color:#986c20; border-color:#edd9b5; }
        #sariUniversalAdminMessages .sari-role-logistics { background:#f2f7f6; color:#55736d; border-color:#d9e6e2; }
        #sariUniversalAdminMessages .sari-role-rider { background:#f5f4f8; color:#74657f; border-color:#e2ddea; }

        @media (max-width: 1279px) {
            #sariUniversalAdminMessages .sari-context-panel {
                display: none;
            }
        }

        @media (max-width: 1023px) {
            #sariUniversalAdminMessages .sari-message-grid {
                grid-template-columns: 1fr !important;
            }

            #sariUniversalAdminMessages .sari-inbox-panel {
                display: none;
            }

            #sariUniversalAdminMessages[data-mobile-pane="inbox"] .sari-inbox-panel {
                display: flex;
            }

            #sariUniversalAdminMessages[data-mobile-pane="inbox"] .sari-thread-panel {
                display: none;
            }
        }
    </style>

    <div id="adminMessagingNotice" class="mb-4 hidden rounded-[16px] border px-4 py-3 text-[12px]"></div>

    <section class="sari-shadow overflow-hidden rounded-[24px] border border-[#e8e2d9] bg-white">
        <div class="sari-message-grid grid min-h-[720px] grid-cols-[330px_minmax(0,1fr)_300px]">
            {{-- INBOX --}}
            <aside class="sari-inbox-panel flex min-h-0 flex-col border-r border-[#ebe6df] bg-[#fbfaf8]">
                <div class="border-b border-[#ebe6df] p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#9f7a3c]">SARI Communications</p>
                            <h2 class="mt-1 text-[20px] font-bold tracking-[-0.03em] text-[#2c2721]">Admin Inbox</h2>
                            <p class="mt-1 text-[11px] text-[#91887d]">Direct and report-support conversations</p>
                        </div>

                        <button id="adminNewConversationButton" type="button"
                            class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#e5ded3] bg-white text-[#a97012] transition hover:border-[#d29a28] hover:bg-[#fff9ee]"
                            aria-label="Start conversation">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </button>
                    </div>

                    <div class="relative mt-4">
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#aaa197]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                        </svg>
                        <input id="adminConversationSearch" type="search" placeholder="Search conversations..."
                            class="h-11 w-full rounded-xl border border-[#e5dfd6] bg-white pl-10 pr-4 text-[12px] text-[#3f3932] outline-none transition placeholder:text-[#aaa197] focus:border-[#d29a28] focus:ring-4 focus:ring-[#d29a28]/10">
                    </div>

                    <div id="adminConversationFilters" class="mt-3 flex flex-wrap gap-1.5">
                        <button type="button" data-filter="all" class="rounded-full border border-[#d8c28e] bg-[#fff6df] px-3 py-1.5 text-[10px] font-semibold text-[#9b6c19]">All</button>
                        <button type="button" data-filter="direct" class="rounded-full border border-[#e4ded6] bg-white px-3 py-1.5 text-[10px] font-semibold text-[#71695f]">Direct</button>
                        <button type="button" data-filter="report_support" class="rounded-full border border-[#e4ded6] bg-white px-3 py-1.5 text-[10px] font-semibold text-[#71695f]">Support</button>
                        <button type="button" data-filter="unread" class="rounded-full border border-[#e4ded6] bg-white px-3 py-1.5 text-[10px] font-semibold text-[#71695f]">Unread</button>
                    </div>
                </div>

                <div id="adminConversationList" class="sari-scroll min-h-0 flex-1 overflow-y-auto">
                    <div class="p-5 text-center text-[11px] text-[#9a9186]">Loading conversations…</div>
                </div>

                <div class="border-t border-[#ebe6df] px-4 py-3">
                    <div class="flex items-center justify-between gap-3">
                        <span id="adminMessagingRefreshStatus" class="text-[10px] text-[#989084]">Auto-refresh enabled</span>
                        <button id="adminRefreshInbox" type="button" class="text-[10px] font-semibold text-[#9e7020] hover:text-[#7d5715]">Refresh</button>
                    </div>
                </div>
            </aside>

            {{-- THREAD --}}
            <main class="sari-thread-panel flex min-h-0 min-w-0 flex-col bg-white">
                <header class="flex min-h-[76px] items-center justify-between gap-4 border-b border-[#ebe6df] px-4 py-3 sm:px-5">
                    <div class="flex min-w-0 items-center gap-3">
                        <button id="adminMobileBack" type="button"
                            class="hidden h-9 w-9 shrink-0 place-items-center rounded-xl border border-[#e5ded5] text-[#6c645a] lg:hidden"
                            aria-label="Back to inbox">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="m15 18-6-6 6-6"/>
                            </svg>
                        </button>

                        <div id="adminThreadAvatar" class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#f2f4f5] text-[11px] font-bold text-[#677986]">SA</div>
                        <div class="min-w-0">
                            <h3 id="adminThreadTitle" class="truncate text-[14px] font-bold text-[#302a24]">Select a conversation</h3>
                            <div class="mt-1 flex flex-wrap items-center gap-2">
                                <span id="adminThreadTypeBadge" class="hidden rounded-full border px-2 py-0.5 text-[9px] font-bold uppercase tracking-[0.08em]"></span>
                                <span id="adminThreadSubtitle" class="truncate text-[10px] text-[#948b80]">Choose a thread from the inbox or start a new one.</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <span id="adminThreadStatus" class="hidden rounded-full border border-[#dde6df] bg-[#f4f8f5] px-2.5 py-1 text-[9px] font-semibold text-[#5f7866]">Active</span>
                    </div>
                </header>

                <div id="adminThreadMessages" class="sari-scroll flex-1 overflow-y-auto bg-[#faf9f6] px-4 py-5 sm:px-6">
                    <div class="flex h-full min-h-[420px] items-center justify-center text-center">
                        <div class="max-w-[340px]">
                            <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl border border-[#e4ded6] bg-white text-[#8d7d68]">
                                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"/>
                                </svg>
                            </div>
                            <p class="mt-4 text-[13px] font-bold text-[#554d44]">No conversation selected</p>
                            <p class="mt-1.5 text-[11px] leading-5 text-[#948b80]">Admin can start a direct conversation with a user, or open a report-specific support thread.</p>
                        </div>
                    </div>
                </div>

                <div id="adminComposerWrap" class="hidden border-t border-[#ebe6df] bg-white p-4">
                    <form id="adminUniversalMessageForm" class="sari-soft-shadow rounded-[16px] border border-[#e4ded6] bg-white p-3 transition focus-within:border-[#d29a28] focus-within:ring-4 focus-within:ring-[#d29a28]/10">
                        <textarea id="adminUniversalMessageInput" rows="3" maxlength="3000"
                            placeholder="Write a message…"
                            class="w-full resize-none bg-transparent px-1 text-[13px] leading-6 text-[#3f3932] outline-none placeholder:text-[#aaa197]"></textarea>

                        <div class="mt-2 flex items-center justify-between gap-3 border-t border-[#eee8e0] pt-3">
                            <div>
                                <p class="text-[10px] text-[#958c80]">Text messages only in the current universal backend.</p>
                                <p id="adminMessageCounter" class="mt-0.5 text-[9px] text-[#aaa197]">0 / 3000</p>
                            </div>

                            <button id="adminUniversalSendButton" type="submit"
                                class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[#d29a28] px-5 text-[11px] font-semibold text-white shadow-[0_7px_18px_rgba(166,112,18,0.16)] transition hover:bg-[#b8801d] disabled:cursor-not-allowed disabled:opacity-50">
                                Send
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="m4 4 17 8-17 8 3-8-3-8Z"/><path d="M7 12h14"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                    <p id="adminComposerError" class="mt-2 hidden text-[10px] text-[#b15f55]"></p>
                </div>
            </main>

            {{-- CONTEXT --}}
            <aside class="sari-context-panel min-h-0 border-l border-[#ebe6df] bg-[#fbfaf8]">
                <div class="border-b border-[#ebe6df] p-5">
                    <p class="text-[12px] font-bold text-[#332d27]">Conversation Context</p>
                    <p class="mt-1 text-[10px] leading-4 text-[#948b80]">Role, participants, and report linkage</p>
                </div>

                <div id="adminConversationContext" class="sari-scroll max-h-[640px] overflow-y-auto p-5">
                    <div class="rounded-[16px] border border-[#e7e1d8] bg-white p-4 text-[11px] leading-5 text-[#8e857a]">
                        Select a conversation to view its context.
                    </div>
                </div>

                <div class="border-t border-[#ebe6df] p-5">
                    <div class="rounded-[16px] border border-[#e8e0d3] bg-[#fffaf0] p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-[#9c742d]">Access Rule</p>
                        <p class="mt-2 text-[10px] leading-5 text-[#7d7060]">This inbox shows conversations where Admin is an actual participant. Private user-to-user conversations are not automatically inserted into Admin chat.</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    {{-- NEW CONVERSATION MODAL --}}
    <div id="adminNewConversationModal" class="fixed inset-0 z-[160] hidden items-center justify-center bg-black/35 p-4 backdrop-blur-[2px]">
        <div class="w-full max-w-[760px] overflow-hidden rounded-[24px] border border-[#e8e1d8] bg-white shadow-[0_30px_90px_rgba(38,30,18,.22)]">
            <div class="flex items-start justify-between gap-4 border-b border-[#ece6de] p-5 sm:p-6">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#9c742d]">New Conversation</p>
                    <h3 class="mt-1 text-[20px] font-bold tracking-[-0.03em] text-[#302a24]">Choose a user or support report</h3>
                    <p class="mt-1 text-[11px] text-[#90877c]">All contacts and reports below come from the current database-backed messaging API.</p>
                </div>
                <button id="adminNewConversationClose" type="button" class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-[#e7e0d7] text-[#756d63] hover:bg-[#faf7f2]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m7 7 10 10M17 7 7 17"/>
                    </svg>
                </button>
            </div>

            <div class="border-b border-[#ece6de] px-5 pt-4 sm:px-6">
                <div class="flex gap-2">
                    <button type="button" data-new-tab="contacts" class="border-b-2 border-[#d29a28] px-1 pb-3 text-[11px] font-semibold text-[#9e7020]">Direct message</button>
                    <button type="button" data-new-tab="reports" class="border-b-2 border-transparent px-1 pb-3 text-[11px] font-semibold text-[#81796f]">Report support</button>
                </div>
            </div>

            <div class="p-5 sm:p-6">
                <div id="adminNewContactsPanel">
                    <div class="grid gap-3 sm:grid-cols-[180px_1fr]">
                        <select id="adminNewRoleFilter" class="h-11 rounded-xl border border-[#e3ddd4] bg-white px-3 text-[11px] text-[#504940] outline-none focus:border-[#d29a28]">
                            <option value="all">All roles</option>
                            <option value="buyer">Buyer</option>
                            <option value="social_buyer">Social Buyer</option>
                            <option value="seller">Seller</option>
                            <option value="logistics">Logistics</option>
                            <option value="rider">Rider</option>
                        </select>
                        <input id="adminNewContactSearch" type="search" placeholder="Search name or email…"
                            class="h-11 rounded-xl border border-[#e3ddd4] bg-white px-4 text-[11px] text-[#504940] outline-none placeholder:text-[#aaa197] focus:border-[#d29a28]">
                    </div>

                    <div id="adminNewContactsList" class="sari-scroll mt-4 max-h-[420px] overflow-y-auto rounded-[16px] border border-[#ece6de]">
                        <div class="p-6 text-center text-[11px] text-[#9b9185]">Loading contacts…</div>
                    </div>
                </div>

                <div id="adminNewReportsPanel" class="hidden">
                    <div class="mb-3 rounded-[14px] border border-[#eadfca] bg-[#fffaf0] px-4 py-3 text-[10px] leading-5 text-[#7d7060]">
                        Opening a report creates or reuses the report-specific Admin support conversation for that complaint.
                    </div>
                    <div id="adminNewReportsList" class="sari-scroll max-h-[460px] overflow-y-auto rounded-[16px] border border-[#ece6de]">
                        <div class="p-6 text-center text-[11px] text-[#9b9185]">Loading reports…</div>
                    </div>
                </div>

                <p id="adminNewConversationError" class="mt-3 hidden text-[10px] text-[#b15f55]"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const root = document.getElementById('sariUniversalAdminMessages');
    if (!root) return;

    window.__SARI_UNIVERSAL_ADMIN_MESSAGES_CLEANUP__?.();

    const endpoints = {
        me: @json(route('messaging.api.me')),
        connections: @json(route('messaging.api.connections')),
        conversations: @json(route('messaging.api.index')),
        direct: @json(route('messaging.api.direct')),
        reportTemplate: @json(route('messaging.api.report-support', ['complaint' => '__COMPLAINT__'])),
        showTemplate: @json(route('messaging.api.show', ['conversation' => '__CONVERSATION__'])),
        sendTemplate: @json(route('messaging.api.send', ['conversation' => '__CONVERSATION__'])),
        readTemplate: @json(route('messaging.api.read', ['conversation' => '__CONVERSATION__'])),
    };

    const csrf = @json(csrf_token());

    const state = {
        actor: null,
        conversations: [],
        contacts: [],
        reports: [],
        selectedUuid: null,
        selectedDetail: null,
        filter: 'all',
        search: '',
        newTab: 'contacts',
        loadingThread: false,
        destroyed: false,
    };

    const elements = {
        notice: document.getElementById('adminMessagingNotice'),
        conversationList: document.getElementById('adminConversationList'),
        conversationSearch: document.getElementById('adminConversationSearch'),
        filters: document.getElementById('adminConversationFilters'),
        refreshInbox: document.getElementById('adminRefreshInbox'),
        refreshStatus: document.getElementById('adminMessagingRefreshStatus'),
        mobileBack: document.getElementById('adminMobileBack'),
        threadAvatar: document.getElementById('adminThreadAvatar'),
        threadTitle: document.getElementById('adminThreadTitle'),
        threadTypeBadge: document.getElementById('adminThreadTypeBadge'),
        threadSubtitle: document.getElementById('adminThreadSubtitle'),
        threadStatus: document.getElementById('adminThreadStatus'),
        threadMessages: document.getElementById('adminThreadMessages'),
        composerWrap: document.getElementById('adminComposerWrap'),
        form: document.getElementById('adminUniversalMessageForm'),
        input: document.getElementById('adminUniversalMessageInput'),
        counter: document.getElementById('adminMessageCounter'),
        sendButton: document.getElementById('adminUniversalSendButton'),
        composerError: document.getElementById('adminComposerError'),
        context: document.getElementById('adminConversationContext'),
        newButton: document.getElementById('adminNewConversationButton'),
        modal: document.getElementById('adminNewConversationModal'),
        modalClose: document.getElementById('adminNewConversationClose'),
        newTabs: document.querySelectorAll('[data-new-tab]'),
        contactsPanel: document.getElementById('adminNewContactsPanel'),
        reportsPanel: document.getElementById('adminNewReportsPanel'),
        roleFilter: document.getElementById('adminNewRoleFilter'),
        contactSearch: document.getElementById('adminNewContactSearch'),
        contactsList: document.getElementById('adminNewContactsList'),
        reportsList: document.getElementById('adminNewReportsList'),
        modalError: document.getElementById('adminNewConversationError'),
    };

    const intervals = [];
    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const normalizeRole = (role) => role === 'social_buyer' ? 'Social Buyer' : role
        ? role.replaceAll('_', ' ').replace(/\b\w/g, c => c.toUpperCase())
        : 'User';

    const initials = (value) => {
        const parts = String(value || 'SARI').trim().split(/\s+/).filter(Boolean);
        return ((parts[0]?.[0] || 'S') + (parts[1]?.[0] || parts[0]?.[1] || 'A')).toUpperCase();
    };

    const humanDate = (value) => {
        if (!value) return '';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '';
        const now = new Date();
        const sameDay = date.toDateString() === now.toDateString();

        return new Intl.DateTimeFormat('en-PH', sameDay
            ? { hour: 'numeric', minute: '2-digit', hour12: true }
            : { month: 'short', day: 'numeric' }
        ).format(date);
    };

    const fullDate = (value) => {
        if (!value) return '';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '';
        return new Intl.DateTimeFormat('en-PH', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        }).format(date);
    };

    const urlFor = (template, token, value) => template.replace(token, encodeURIComponent(value));

    function showNotice(message, type = 'error') {
        if (!elements.notice) return;
        elements.notice.textContent = message;
        elements.notice.className = 'mb-4 rounded-[16px] border px-4 py-3 text-[12px]';

        if (type === 'success') {
            elements.notice.classList.add('border-[#d7e3d9]', 'bg-[#f3f8f4]', 'text-[#617667]');
        } else {
            elements.notice.classList.add('border-[#ead8d4]', 'bg-[#fdf6f4]', 'text-[#a45f55]');
        }

        elements.notice.classList.remove('hidden');
        window.setTimeout(() => elements.notice?.classList.add('hidden'), 4200);
    }

    async function request(url, options = {}) {
        const config = {
            method: options.method || 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.body ? { 'Content-Type': 'application/json' } : {}),
                ...(options.method && options.method !== 'GET' ? { 'X-CSRF-TOKEN': csrf } : {}),
                ...(options.headers || {}),
            },
            credentials: 'same-origin',
            cache: 'no-store',
        };

        if (options.body) {
            config.body = JSON.stringify(options.body);
        }

        const response = await fetch(url, config);
        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            const validation = payload.errors
                ? Object.values(payload.errors).flat().find(Boolean)
                : null;
            throw new Error(validation || payload.message || 'Request failed.');
        }

        return payload;
    }

    function otherParticipants(conversation) {
        const actor = state.actor;
        return (conversation?.participants || []).filter(participant => {
            if (!actor) return true;
            return !(participant.role === actor.role && Number(participant.id) === Number(actor.id));
        });
    }

    function conversationDisplayName(conversation) {
        const others = otherParticipants(conversation);
        if (conversation?.type === 'report_support') {
            const reporter = others.find(row => row.role !== 'admin');
            return reporter?.label || conversation?.subject || 'Report Support';
        }
        return others.map(row => row.label).filter(Boolean).join(', ')
            || conversation?.subject
            || 'Conversation';
    }

    function conversationRole(conversation) {
        const others = otherParticipants(conversation);
        if (conversation?.type === 'report_support') {
            return 'report_support';
        }
        return others[0]?.role || 'user';
    }

    function roleBadge(role) {
        const label = role === 'report_support' ? 'Support' : normalizeRole(role);
        const cls = role === 'report_support'
            ? 'border-[#ead8bb] bg-[#fff8eb] text-[#956c24]'
            : `sari-role-${role}`;

        return `<span class="inline-flex rounded-full border px-2 py-0.5 text-[9px] font-semibold ${cls}">${escapeHtml(label)}</span>`;
    }

    function renderConversationList() {
        if (!elements.conversationList) return;

        const query = state.search.trim().toLowerCase();
        const filtered = state.conversations.filter(conversation => {
            if (state.filter === 'direct' && conversation.type !== 'direct') return false;
            if (state.filter === 'report_support' && conversation.type !== 'report_support') return false;
            if (state.filter === 'unread' && Number(conversation.unread_count || 0) < 1) return false;

            if (!query) return true;

            const haystack = [
                conversationDisplayName(conversation),
                conversation.subject,
                conversation.type,
                ...(conversation.participants || []).map(row => `${row.label || ''} ${row.role || ''}`)
            ].join(' ').toLowerCase();

            return haystack.includes(query);
        });

        if (!filtered.length) {
            elements.conversationList.innerHTML = `
                <div class="p-8 text-center">
                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl border border-[#e6e0d8] bg-white text-[#93877a]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-[11px] font-semibold text-[#5d554c]">No matching conversations</p>
                    <p class="mt-1 text-[10px] leading-4 text-[#968d82]">Start a direct message or open report support.</p>
                </div>
            `;
            return;
        }

        elements.conversationList.innerHTML = filtered.map(conversation => {
            const name = conversationDisplayName(conversation);
            const role = conversationRole(conversation);
            const selected = conversation.uuid === state.selectedUuid;
            const unread = Number(conversation.unread_count || 0);
            const lastMessage = conversation.last_message;
            const preview = lastMessage?.body || (lastMessage?.attachment ? 'Attachment' : 'No messages yet');
            const time = humanDate(conversation.last_message_at || lastMessage?.created_at || conversation.created_at);

            return `
                <button type="button" data-conversation-uuid="${escapeHtml(conversation.uuid)}"
                    class="w-full border-b border-[#eee9e2] border-l-2 px-4 py-4 text-left transition ${
                        selected
                            ? 'border-l-[#d29a28] bg-[#fff8ea]'
                            : 'border-l-transparent hover:bg-white'
                    }">
                    <div class="flex gap-3">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full ${
                            role === 'report_support' ? 'bg-[#fff0d4] text-[#9b6b1c]' : 'bg-[#f0f3f4] text-[#657985]'
                        } text-[10px] font-bold">${escapeHtml(initials(name))}</div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="truncate text-[11px] font-bold text-[#332d27]">${escapeHtml(name)}</p>
                                    <div class="mt-1">${roleBadge(role)}</div>
                                </div>
                                <div class="flex shrink-0 flex-col items-end gap-1">
                                    <span class="text-[9px] text-[#9b9286]">${escapeHtml(time)}</span>
                                    ${unread > 0 ? `<span class="grid min-w-[20px] place-items-center rounded-full bg-[#d29a28] px-1.5 py-0.5 text-[9px] font-bold text-white">${unread}</span>` : ''}
                                </div>
                            </div>
                            <p class="mt-2 truncate text-[10px] text-[#8d857b]">${escapeHtml(preview)}</p>
                        </div>
                    </div>
                </button>
            `;
        }).join('');

        elements.conversationList.querySelectorAll('[data-conversation-uuid]').forEach(button => {
            button.addEventListener('click', () => selectConversation(button.dataset.conversationUuid));
        });
    }

    function renderThread(detail) {
        const conversation = detail?.conversation;
        if (!conversation) return;

        const name = conversationDisplayName(conversation);
        const role = conversationRole(conversation);
        const participantRoles = otherParticipants(conversation).map(row => normalizeRole(row.role)).join(', ');

        elements.threadAvatar.textContent = initials(name);
        elements.threadTitle.textContent = name;
        elements.threadSubtitle.textContent = conversation.type === 'report_support'
            ? `Report support${conversation.context?.id ? ` · Complaint #${conversation.context.id}` : ''}`
            : (participantRoles || 'Direct conversation');

        elements.threadTypeBadge.textContent = conversation.type === 'report_support' ? 'Support' : 'Direct';
        elements.threadTypeBadge.className = `rounded-full border px-2 py-0.5 text-[9px] font-bold uppercase tracking-[0.08em] ${
            conversation.type === 'report_support'
                ? 'border-[#ead8bb] bg-[#fff8eb] text-[#956c24]'
                : 'border-[#dfe5e7] bg-[#f4f6f7] text-[#657985]'
        }`;

        elements.threadTypeBadge.classList.remove('hidden');
        elements.threadStatus.textContent = conversation.status ? normalizeRole(conversation.status) : 'Active';
        elements.threadStatus.classList.remove('hidden');
        elements.composerWrap.classList.toggle('hidden', conversation.status !== 'active');

        const messages = detail.messages || [];

        if (!messages.length) {
            elements.threadMessages.innerHTML = `
                <div class="flex h-full min-h-[420px] items-center justify-center text-center">
                    <div>
                        <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl border border-[#e4ded6] bg-white text-[#8d7d68]">
                            <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"/>
                            </svg>
                        </div>
                        <p class="mt-4 text-[12px] font-bold text-[#554d44]">No messages yet</p>
                        <p class="mt-1 text-[10px] text-[#948b80]">Send the first message in this conversation.</p>
                    </div>
                </div>
            `;
        } else {
            elements.threadMessages.innerHTML = messages.map(message => {
                const mine = state.actor
                    && message.sender_role === state.actor.role
                    && Number(message.sender_id) === Number(state.actor.id);

                const sender = message.sender || normalizeRole(message.sender_role);
                const body = message.body || '';
                const time = fullDate(message.created_at);

                return `
                    <div class="mb-5 ${mine ? 'flex justify-end' : 'flex items-end gap-2.5'}" data-message-id="${escapeHtml(message.id)}">
                        ${mine ? '' : `<div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-white text-[8px] font-bold text-[#6c7d87] shadow-sm">${escapeHtml(initials(sender))}</div>`}
                        <div class="max-w-[82%] sm:max-w-[68%]">
                            ${mine ? '' : `<div class="mb-1.5 flex items-center gap-2"><span class="text-[9px] font-semibold text-[#746c62]">${escapeHtml(sender)}</span>${roleBadge(message.sender_role)}</div>`}
                            ${body ? `<div class="sari-message-body rounded-[16px] px-4 py-3 text-[12px] leading-5 shadow-[0_7px_18px_rgba(31,27,22,.045)] ${
                                mine
                                    ? 'rounded-br-[5px] bg-[#d29a28] text-white'
                                    : 'rounded-bl-[5px] border border-[#e7e1d8] bg-white text-[#554d44]'
                            }">${escapeHtml(body)}</div>` : ''}
                            ${message.attachment ? `<div class="mt-2 rounded-xl border border-[#e7e1d8] bg-white px-3 py-2 text-[10px] text-[#746b61]">Attachment metadata exists for this message.</div>` : ''}
                            <p class="mt-1.5 text-[9px] text-[#9b9286] ${mine ? 'text-right' : ''}">${escapeHtml(time)}</p>
                        </div>
                    </div>
                `;
            }).join('');
        }

        renderContext(detail);
        requestAnimationFrame(() => {
            elements.threadMessages.scrollTop = elements.threadMessages.scrollHeight;
        });
    }

    function renderContext(detail) {
        const conversation = detail?.conversation;
        if (!conversation) return;

        const participants = detail.participants || conversation.participants || [];
        const supportBlock = conversation.type === 'report_support' && conversation.context?.id
            ? `
                <div class="sari-soft-shadow rounded-[16px] border border-[#eadfca] bg-[#fffaf0] p-4">
                    <p class="text-[9px] font-bold uppercase tracking-[0.1em] text-[#9b742f]">Report Context</p>
                    <p class="mt-2 text-[12px] font-bold text-[#4c4339]">Platform Complaint #${escapeHtml(conversation.context.id)}</p>
                    <p class="mt-1 text-[10px] leading-4 text-[#81776a]">This support thread is tied to the report and is separate from ordinary direct chat.</p>
                </div>
            `
            : '';

        elements.context.innerHTML = `
            ${supportBlock}
            <div class="${supportBlock ? 'mt-4' : ''} rounded-[16px] border border-[#e7e1d8] bg-white p-4">
                <p class="text-[9px] font-bold uppercase tracking-[0.1em] text-[#948777]">Conversation</p>
                <dl class="mt-3 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-[10px] text-[#978e83]">Type</dt>
                        <dd class="text-right text-[10px] font-semibold text-[#4e473f]">${escapeHtml(conversation.type === 'report_support' ? 'Report Support' : 'Direct')}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-[10px] text-[#978e83]">Status</dt>
                        <dd class="text-right text-[10px] font-semibold text-[#5f7866]">${escapeHtml(normalizeRole(conversation.status || 'active'))}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-[10px] text-[#978e83]">UUID</dt>
                        <dd class="max-w-[170px] break-all text-right text-[9px] font-medium text-[#6f675d]">${escapeHtml(conversation.uuid)}</dd>
                    </div>
                </dl>
            </div>

            <div class="mt-4">
                <p class="mb-2 text-[9px] font-bold uppercase tracking-[0.1em] text-[#948777]">Participants</p>
                <div class="space-y-2">
                    ${participants.map(participant => `
                        <div class="rounded-[14px] border border-[#e7e1d8] bg-white p-3">
                            <div class="flex items-center gap-3">
                                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#f2f4f5] text-[9px] font-bold text-[#657985]">${escapeHtml(initials(participant.label || normalizeRole(participant.role)))}</div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[10px] font-semibold text-[#4f473e]">${escapeHtml(participant.label || normalizeRole(participant.role))}</p>
                                    <div class="mt-1">${roleBadge(participant.role)}</div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    async function loadInbox({ preserveSelection = true, silent = false } = {}) {
        if (!silent) {
            elements.refreshStatus.textContent = 'Refreshing…';
        }

        try {
            const payload = await request(endpoints.conversations);
            state.conversations = Array.isArray(payload.conversations) ? payload.conversations : [];

            if (!preserveSelection && state.conversations.length) {
                state.selectedUuid = state.conversations[0].uuid;
            }

            renderConversationList();

            if (!silent) {
                elements.refreshStatus.textContent = 'Updated just now';
                window.setTimeout(() => {
                    if (!state.destroyed) elements.refreshStatus.textContent = 'Auto-refresh enabled';
                }, 1800);
            }
        } catch (error) {
            if (!silent) showNotice(error.message);
            elements.refreshStatus.textContent = 'Refresh failed';
        }
    }

    async function loadConnections() {
        const payload = await request(endpoints.connections);
        state.actor = payload.actor || state.actor;
        state.contacts = Array.isArray(payload.contacts) ? payload.contacts : [];
        state.reports = Array.isArray(payload.reports) ? payload.reports : [];
        renderNewContacts();
        renderReports();
    }

    async function loadActor() {
        const payload = await request(endpoints.me);
        state.actor = payload.actor || null;
    }

    async function selectConversation(uuid, { pushState = true, silent = false } = {}) {
        if (!uuid || state.loadingThread) return;

        state.loadingThread = true;
        state.selectedUuid = uuid;
        renderConversationList();

        if (!silent) {
            elements.threadMessages.innerHTML = '<div class="p-8 text-center text-[11px] text-[#968d82]">Loading conversation…</div>';
        }

        try {
            const payload = await request(urlFor(endpoints.showTemplate, '__CONVERSATION__', uuid));
            state.selectedDetail = payload;
            renderThread(payload);

            await request(urlFor(endpoints.readTemplate, '__CONVERSATION__', uuid), {
                method: 'POST',
            }).catch(() => null);

            state.conversations = state.conversations.map(row => row.uuid === uuid
                ? { ...row, unread_count: 0 }
                : row
            );
            renderConversationList();

            if (pushState) {
                const url = new URL(window.location.href);
                url.searchParams.set('conversation', uuid);
                window.history.replaceState({}, '', url.toString());
            }

            root.dataset.mobilePane = 'thread';
        } catch (error) {
            showNotice(error.message);
        } finally {
            state.loadingThread = false;
        }
    }

    function renderNewContacts() {
        if (!elements.contactsList) return;

        const role = elements.roleFilter?.value || 'all';
        const query = (elements.contactSearch?.value || '').trim().toLowerCase();

        const filtered = state.contacts.filter(contact => {
            if (role !== 'all' && contact.role !== role) return false;
            if (!query) return true;
            return `${contact.name || ''} ${contact.email || ''} ${contact.role || ''}`.toLowerCase().includes(query);
        });

        if (!filtered.length) {
            elements.contactsList.innerHTML = '<div class="p-8 text-center text-[11px] text-[#958c80]">No matching contacts.</div>';
            return;
        }

        elements.contactsList.innerHTML = filtered.map(contact => `
            <button type="button" data-contact-role="${escapeHtml(contact.role)}" data-contact-id="${escapeHtml(contact.id)}"
                class="flex w-full items-center gap-3 border-b border-[#eee8e1] px-4 py-3.5 text-left last:border-b-0 hover:bg-[#fbfaf8]">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#f1f4f5] text-[10px] font-bold text-[#667a86]">${escapeHtml(initials(contact.name))}</div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <p class="truncate text-[11px] font-semibold text-[#403930]">${escapeHtml(contact.name || normalizeRole(contact.role))}</p>
                        ${roleBadge(contact.role)}
                    </div>
                    <p class="mt-1 truncate text-[10px] text-[#938a7f]">${escapeHtml(contact.email || `Account #${contact.id}`)}</p>
                </div>
                <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0 text-[#aaa197]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
            </button>
        `).join('');

        elements.contactsList.querySelectorAll('[data-contact-role]').forEach(button => {
            button.addEventListener('click', () => openDirect(button.dataset.contactRole, Number(button.dataset.contactId)));
        });
    }

    function renderReports() {
        if (!elements.reportsList) return;

        if (!state.reports.length) {
            elements.reportsList.innerHTML = '<div class="p-8 text-center text-[11px] text-[#958c80]">No reports available.</div>';
            return;
        }

        elements.reportsList.innerHTML = state.reports.map(report => `
            <button type="button" data-report-id="${escapeHtml(report.id)}"
                class="flex w-full items-start gap-3 border-b border-[#eee8e1] px-4 py-4 text-left last:border-b-0 hover:bg-[#fbfaf8]">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#fff3dc] text-[10px] font-bold text-[#9b6b1c]">#${escapeHtml(report.id)}</div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="truncate text-[11px] font-semibold text-[#403930]">${escapeHtml(report.subject || `Report #${report.id}`)}</p>
                        <span class="rounded-full border border-[#e7dfd4] bg-white px-2 py-0.5 text-[9px] font-semibold text-[#72695f]">${escapeHtml(normalizeRole(report.status || 'open'))}</span>
                    </div>
                    <p class="mt-1 text-[10px] text-[#938a7f]">Reporter: ${escapeHtml(normalizeRole(report.reporter_role || 'user'))}</p>
                    <p class="mt-1 text-[9px] text-[#aaa197]">${report.support_conversation_uuid ? 'Support conversation already exists' : 'Open support conversation'}</p>
                </div>
                <svg viewBox="0 0 24 24" class="mt-1 h-4 w-4 shrink-0 text-[#aaa197]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
            </button>
        `).join('');

        elements.reportsList.querySelectorAll('[data-report-id]').forEach(button => {
            button.addEventListener('click', () => openReport(Number(button.dataset.reportId)));
        });
    }

    async function openDirect(role, id) {
        elements.modalError.classList.add('hidden');

        try {
            const payload = await request(endpoints.direct, {
                method: 'POST',
                body: {
                    target_role: role,
                    target_id: id,
                },
            });

            const uuid = payload.conversation?.uuid;
            if (!uuid) throw new Error('Conversation was created but no UUID was returned.');

            closeModal();
            await loadInbox({ preserveSelection: true, silent: true });
            await selectConversation(uuid);
        } catch (error) {
            elements.modalError.textContent = error.message;
            elements.modalError.classList.remove('hidden');
        }
    }

    async function openReport(reportId) {
        elements.modalError.classList.add('hidden');

        try {
            const url = urlFor(endpoints.reportTemplate, '__COMPLAINT__', reportId);
            const payload = await request(url, { method: 'POST' });
            const uuid = payload.conversation?.uuid;
            if (!uuid) throw new Error('Support conversation was opened but no UUID was returned.');

            closeModal();
            await loadInbox({ preserveSelection: true, silent: true });
            await selectConversation(uuid);
        } catch (error) {
            elements.modalError.textContent = error.message;
            elements.modalError.classList.remove('hidden');
        }
    }

    function openModal() {
        elements.modal.classList.remove('hidden');
        elements.modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
        elements.modalError.classList.add('hidden');

        loadConnections().catch(error => {
            elements.modalError.textContent = error.message;
            elements.modalError.classList.remove('hidden');
        });
    }

    function closeModal() {
        elements.modal.classList.add('hidden');
        elements.modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    function switchNewTab(tab) {
        state.newTab = tab;
        elements.contactsPanel.classList.toggle('hidden', tab !== 'contacts');
        elements.reportsPanel.classList.toggle('hidden', tab !== 'reports');

        elements.newTabs.forEach(button => {
            const active = button.dataset.newTab === tab;
            button.classList.toggle('border-[#d29a28]', active);
            button.classList.toggle('text-[#9e7020]', active);
            button.classList.toggle('border-transparent', !active);
            button.classList.toggle('text-[#81796f]', !active);
        });
    }

    async function sendMessage(event) {
        event.preventDefault();

        if (!state.selectedUuid) return;

        const body = elements.input.value.trim();
        if (!body) return;

        elements.sendButton.disabled = true;
        elements.composerError.classList.add('hidden');

        try {
            await request(urlFor(endpoints.sendTemplate, '__CONVERSATION__', state.selectedUuid), {
                method: 'POST',
                body: { body },
            });

            elements.input.value = '';
            elements.counter.textContent = '0 / 3000';

            await selectConversation(state.selectedUuid, { pushState: false, silent: true });
            await loadInbox({ preserveSelection: true, silent: true });
        } catch (error) {
            elements.composerError.textContent = error.message;
            elements.composerError.classList.remove('hidden');
        } finally {
            elements.sendButton.disabled = false;
            elements.input.focus();
        }
    }

    async function initialLoad() {
        try {
            await loadActor();
            await Promise.all([
                loadConnections(),
                loadInbox({ preserveSelection: true }),
            ]);

            const url = new URL(window.location.href);
            const requestedUuid = url.searchParams.get('conversation');

            if (requestedUuid) {
                await selectConversation(requestedUuid, { pushState: false });
            } else if (state.conversations.length) {
                await selectConversation(state.conversations[0].uuid, { pushState: false });
            }
        } catch (error) {
            showNotice(error.message);
        }
    }

    elements.conversationSearch?.addEventListener('input', event => {
        state.search = event.target.value || '';
        renderConversationList();
    });

    elements.filters?.querySelectorAll('[data-filter]').forEach(button => {
        button.addEventListener('click', () => {
            state.filter = button.dataset.filter;
            elements.filters.querySelectorAll('[data-filter]').forEach(item => {
                const active = item === button;
                item.className = `rounded-full border px-3 py-1.5 text-[10px] font-semibold ${
                    active
                        ? 'border-[#d8c28e] bg-[#fff6df] text-[#9b6c19]'
                        : 'border-[#e4ded6] bg-white text-[#71695f]'
                }`;
            });
            renderConversationList();
        });
    });

    elements.refreshInbox?.addEventListener('click', () => loadInbox({ preserveSelection: true }));

    elements.mobileBack?.addEventListener('click', () => {
        root.dataset.mobilePane = 'inbox';
    });

    elements.newButton?.addEventListener('click', openModal);
    elements.modalClose?.addEventListener('click', closeModal);
    elements.modal?.addEventListener('click', event => {
        if (event.target === elements.modal) closeModal();
    });

    elements.newTabs.forEach(button => {
        button.addEventListener('click', () => switchNewTab(button.dataset.newTab));
    });

    elements.roleFilter?.addEventListener('change', renderNewContacts);
    elements.contactSearch?.addEventListener('input', renderNewContacts);

    elements.input?.addEventListener('input', () => {
        elements.counter.textContent = `${elements.input.value.length} / 3000`;
    });

    elements.form?.addEventListener('submit', sendMessage);

    const onKeydown = event => {
        if (event.key === 'Escape') closeModal();

        if (
            event.key === 'Enter'
            && !event.shiftKey
            && document.activeElement === elements.input
        ) {
            event.preventDefault();
            elements.form?.requestSubmit();
        }
    };

    document.addEventListener('keydown', onKeydown);

    intervals.push(window.setInterval(async () => {
        if (document.hidden || state.destroyed) return;

        await loadInbox({ preserveSelection: true, silent: true });

        if (state.selectedUuid && !state.loadingThread) {
            await selectConversation(state.selectedUuid, {
                pushState: false,
                silent: true,
            });
        }
    }, 5000));

    initialLoad();

    let cleaned = false;
    function cleanup() {
        if (cleaned) return;
        cleaned = true;
        state.destroyed = true;

        intervals.forEach(id => window.clearInterval(id));
        document.removeEventListener('keydown', onKeydown);
        document.body.classList.remove('overflow-hidden');

        if (window.__SARI_UNIVERSAL_ADMIN_MESSAGES_CLEANUP__ === cleanup) {
            window.__SARI_UNIVERSAL_ADMIN_MESSAGES_CLEANUP__ = null;
        }
    }

    window.__SARI_UNIVERSAL_ADMIN_MESSAGES_CLEANUP__ = cleanup;
    document.addEventListener('livewire:navigating', cleanup, { once: true });
})();
</script>
@endpush
