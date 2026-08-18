@extends('layouts.seller')

@section('title', 'Chat / Messaging — SARI Seller')
@section('page-title', 'Chat / Messaging')

@section('content')
<div class="mx-auto w-full max-w-[1800px]">

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

    {{-- PAGE INTRO --}}
    <section class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6 lg:p-7">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-[#d9e6dd] bg-[#f3f8f5] px-3 py-1.5 text-[9px] font-semibold uppercase tracking-[0.14em] text-[#56816a]">
                    <span class="h-2 w-2 rounded-full bg-[#68a07b]"></span>
                    Seller Inbox
                </div>

                <h2 class="mt-3 text-[22px] font-bold tracking-[-0.03em] text-[#211c16] sm:text-[24px]">
                    Chat / Messaging
                </h2>

                <p class="mt-2 max-w-[760px] text-[12px] leading-6 text-[#81786c] sm:text-[13px]">
                    Message SARI Admin directly for account, compliance, product review, and marketplace support concerns.
                </p>
            </div>

            <button id="focusSellerChat" type="button" class="inline-flex h-11 w-fit items-center gap-2 rounded-xl bg-[#c99128] px-5 text-[10px] font-semibold text-white transition hover:bg-[#b47e1e]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 5v14"></path><path d="M5 12h14"></path>
                </svg>
                New Message
            </button>
        </div>
    </section>

    {{-- SUMMARY --}}
    <section class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                ['label' => 'Support Conversations', 'value' => '1', 'sub' => 'SARI Admin Support', 'bg' => '#f3f7fa', 'color' => '#627f99'],
                ['label' => 'Unread Messages', 'value' => $stats['unread'], 'sub' => 'Messages awaiting your view', 'bg' => '#fbf6ec', 'color' => '#b98020'],
                ['label' => 'Messages Sent', 'value' => $stats['sent_by_seller'], 'sub' => 'Messages sent to admin', 'bg' => '#f1f7f3', 'color' => '#56816a'],
                ['label' => 'Admin Replies', 'value' => $stats['admin_replies'], 'sub' => 'Support responses received', 'bg' => '#f6f2f8', 'color' => '#806a91'],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="rounded-[18px] border border-[#e7e1d8] bg-white p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-medium text-[#797168]">{{ $card['label'] }}</p>
                        <h3 class="mt-2 text-[25px] font-bold tracking-[-0.04em] text-[#211d18]">{{ $card['value'] }}</h3>
                        <p class="mt-2 text-[9px] text-[#8f877d]">{{ $card['sub'] }}</p>
                    </div>
                    <div class="grid h-11 w-11 place-items-center rounded-xl" style="background: {{ $card['bg'] }}; color: {{ $card['color'] }};">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    {{-- CHAT WORKSPACE --}}
    <section class="mt-5 overflow-hidden rounded-[22px] border border-[#ebe4da] bg-white">
        <div class="grid min-h-[680px] grid-cols-1 lg:grid-cols-[300px_1fr] 2xl:grid-cols-[310px_1fr_280px]">

            {{-- LEFT --}}
            <aside class="border-b border-[#eee8df] lg:border-b-0 lg:border-r">
                <div class="border-b border-[#eee8df] p-4">
                    <h3 class="text-[13px] font-bold text-[#2b261f]">Conversations</h3>
                    <p class="mt-1 text-[9px] text-[#948b7f]">Your direct SARI support channel</p>
                </div>

                <div class="bg-[#fbf7ef] px-4 py-4">
                    <div class="flex gap-3">
                        <div class="relative shrink-0">
                            <div class="grid h-11 w-11 place-items-center rounded-full bg-[#fbf5e9] text-[10px] font-bold text-[#ad791f]">SA</div>
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-[#68a07b]"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-[10px] font-bold text-[#312b25]">SARI Admin Support</p>
                                    <p class="mt-0.5 text-[8px] text-[#9b7340]">Platform Support</p>
                                </div>
                                <span class="text-[8px] text-[#9c9388]">Live</span>
                            </div>
                            <p id="sellerLastMessagePreview" class="mt-2 line-clamp-2 text-[8px] leading-4 text-[#746c62]">
                                {{ $messages->last()?->body ?: 'Start a conversation with SARI Admin.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-[#eee8df] p-4">
                    <div class="rounded-[14px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                        <div class="flex items-center gap-3">
                            <div class="grid h-9 w-9 place-items-center rounded-xl bg-[#f1f7f3] text-[#56816a]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M12 3 4 7v5c0 5 3.5 7.7 8 9 4.5-1.3 8-4 8-9V7l-8-4Z"></path><path d="m9 12 2 2 4-4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[9px] font-semibold text-[#4f473e]">Official Support</p>
                                <p class="mt-1 text-[8px] leading-4 text-[#958c80]">Messages are stored in your SARI account history.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- CENTER --}}
            <div class="flex min-h-[680px] flex-col">
                <div class="flex items-center justify-between gap-4 border-b border-[#eee8df] px-4 py-4 sm:px-5">
                    <div class="flex items-center gap-3">
                        <div class="relative shrink-0">
                            <div class="grid h-11 w-11 place-items-center rounded-full bg-[#fbf5e9] text-[10px] font-bold text-[#ad791f]">SA</div>
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-[#68a07b]"></span>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-[#2f2923]">SARI Admin Support</p>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="text-[8px] text-[#8e8579]">Administrator</span>
                                <span class="h-1 w-1 rounded-full bg-[#c8c1b8]"></span>
                                <span class="text-[8px] font-medium text-[#56816a]">Real-time support</span>
                            </div>
                        </div>
                    </div>
                    <span id="sellerRealtimeStatus" class="rounded-full border border-[#d9e6dd] bg-[#f3f8f5] px-3 py-1.5 text-[8px] font-semibold text-[#56816a]">Connecting…</span>
                </div>

                <div id="sellerChatMessages" class="flex-1 overflow-y-auto bg-[#fdfbf8] px-4 py-5 sm:px-6">
                    @if ($messages->isEmpty())
                        <div id="sellerEmptyChat" class="flex h-full min-h-[360px] items-center justify-center text-center">
                            <div>
                                <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#fbf5e9] text-[#a8731f]">
                                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path></svg>
                                </div>
                                <p class="mt-4 text-[11px] font-bold text-[#4f473e]">Start a conversation</p>
                                <p class="mt-1 text-[9px] text-[#958c80]">Send a message to SARI Admin below.</p>
                            </div>
                        </div>
                    @endif

                    @foreach ($messages as $message)
                        <div data-message-id="{{ $message->id }}" class="mt-5 {{ $message->sender_role === 'seller' ? 'flex justify-end' : 'flex items-end gap-2.5' }}">
                            @if ($message->sender_role === 'admin')
                                <div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#fbf5e9] text-[8px] font-bold text-[#ad791f]">SA</div>
                            @endif

                            <div class="max-w-[82%] sm:max-w-[68%]">
                                @if ($message->body)
                                    <div class="rounded-[16px] px-4 py-3 text-[9px] leading-5 shadow-sm {{ $message->sender_role === 'seller' ? 'rounded-br-[5px] bg-[#c99128] text-white' : 'rounded-bl-[5px] border border-[#e9e2d9] bg-white text-[#5f574d]' }}">
                                        {{ $message->body }}
                                    </div>
                                @endif

                                @if ($message->attachment_path)
                                    <div class="mt-2 overflow-hidden rounded-[14px] border border-[#e9e2d9] bg-white p-2">
                                        @if ($message->attachmentIsImage())
                                            <a href="{{ route('chat.attachments.show', $message) }}" target="_blank">
                                                <img src="{{ route('chat.attachments.show', $message) }}" alt="{{ $message->attachment_name }}" class="max-h-[260px] w-full rounded-xl object-contain">
                                            </a>
                                        @else
                                            <a href="{{ route('chat.attachments.show', $message) }}" target="_blank" class="flex items-center gap-3 rounded-xl bg-[#fcfaf7] p-3">
                                                <div class="grid h-9 w-9 place-items-center rounded-lg bg-[#f5efe4] text-[#a8731f]">
                                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 3h10l4 4v14H5z"></path></svg>
                                                </div>
                                                <div class="min-w-0"><p class="truncate text-[9px] font-semibold text-[#50483f]">{{ $message->attachment_name }}</p><p class="mt-1 text-[8px] text-[#958c80]">Open attachment</p></div>
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                <p class="mt-1.5 {{ $message->sender_role === 'seller' ? 'text-right' : '' }} text-[8px] text-[#9c9388]">{{ $message->created_at->format('h:i A') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-[#eee8df] bg-white p-4">
                    <form id="sellerChatForm" method="POST" action="{{ route('seller.messages.send') }}" enctype="multipart/form-data" class="rounded-[16px] border border-[#e6dfd5] bg-[#fcfbf9] p-3 transition focus-within:border-[#c99a3d] focus-within:ring-4 focus-within:ring-[#c99a3d]/10">
                        @csrf
                        <textarea id="sellerChatInput" name="message" rows="3" placeholder="Write a message to SARI Admin..." class="w-full resize-none bg-transparent text-[10px] leading-5 text-[#3e3831] outline-none placeholder:text-[#aaa197]"></textarea>

                        <div id="sellerAttachmentName" class="mt-2 hidden rounded-xl border border-[#e8e1d7] bg-white px-3 py-2 text-[9px] text-[#62594e]"></div>

                        <div class="mt-2 flex flex-col gap-3 border-t border-[#eee8df] pt-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-1">
                                <label class="grid h-9 w-9 cursor-pointer place-items-center rounded-lg text-[#756d63] transition hover:bg-[#f5efe5] hover:text-[#a8731f]" title="Attach file">
                                    <input id="sellerChatAttachment" name="attachment" type="file" accept="image/*,.pdf,.doc,.docx,.zip,.txt" class="hidden">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 12.5 14.5 6a3 3 0 0 1 4.2 4.2l-8 8a5 5 0 0 1-7.1-7.1l8.3-8.3"></path></svg>
                                </label>
                                <span class="text-[8px] text-[#958c80]">Images/files up to 8 MB</span>
                            </div>

                            <button id="sellerChatSend" type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[#c99128] px-5 text-[9px] font-semibold text-white transition hover:bg-[#b47e1e] disabled:cursor-not-allowed disabled:opacity-50">
                                Send Message
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m4 4 17 8-17 8 3-8-3-8Z"></path><path d="M7 12h14"></path></svg>
                            </button>
                        </div>
                    </form>
                    <p id="sellerChatError" class="mt-2 hidden text-[9px] text-[#a45f5f]"></p>
                </div>
            </div>

            {{-- RIGHT --}}
            <aside class="hidden border-l border-[#eee8df] 2xl:block">
                <div class="border-b border-[#eee8df] p-5">
                    <p class="text-[11px] font-bold text-[#312b25]">Account Details</p>
                    <p class="mt-1 text-[8px] text-[#948b7f]">Information available to support</p>
                </div>
                <div class="p-5">
                    <div class="flex flex-col items-center text-center">
                        <div class="grid h-16 w-16 place-items-center rounded-full bg-[#fbf5e9] text-[15px] font-bold text-[#ad791f]">{{ strtoupper(substr($seller->store_name ?: 'SS', 0, 2)) }}</div>
                        <p class="mt-3 text-[12px] font-bold text-[#302a24]">{{ $seller->store_name ?: 'SARI Seller Store' }}</p>
                        <p class="mt-1 text-[8px] text-[#92897e]">{{ $seller->email }}</p>
                        <span class="mt-3 rounded-full border border-[#dce5ed] bg-[#f4f7fa] px-2.5 py-1 text-[8px] font-semibold text-[#617d96]">Seller</span>
                    </div>

                    <div class="mt-5 space-y-3">
                        <div class="rounded-[14px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                            <p class="text-[8px] text-[#948b7f]">Account Status</p>
                            <p class="mt-2 text-[10px] font-semibold {{ $seller->isSuspended() ? 'text-[#a96565]' : 'text-[#56816a]' }}">{{ ucfirst($seller->account_status) }}</p>
                        </div>
                        <div class="rounded-[14px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                            <p class="text-[8px] text-[#948b7f]">Compliance Warnings</p>
                            <p class="mt-2 text-[12px] font-bold text-[#a8731f]">{{ $seller->warning_count }} / 3</p>
                        </div>
                        <div class="rounded-[14px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                            <p class="text-[8px] text-[#948b7f]">Conversation</p>
                            <p class="mt-2 text-[10px] font-semibold text-[#50483f]">{{ $stats['total_messages'] }} total messages</p>
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
document.addEventListener('DOMContentLoaded', function () {
    const sellerId = {{ (int) $seller->id }};
    const channelName = @json('sari.seller.' . $seller->realtime_token);
    const form = document.getElementById('sellerChatForm');
    const input = document.getElementById('sellerChatInput');
    const attachment = document.getElementById('sellerChatAttachment');
    const attachmentName = document.getElementById('sellerAttachmentName');
    const messages = document.getElementById('sellerChatMessages');
    const sendButton = document.getElementById('sellerChatSend');
    const errorBox = document.getElementById('sellerChatError');
    const status = document.getElementById('sellerRealtimeStatus');
    const preview = document.getElementById('sellerLastMessagePreview');

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;').replaceAll("'", '&#039;');

    const scrollBottom = () => { messages.scrollTop = messages.scrollHeight; };
    scrollBottom();

    document.getElementById('focusSellerChat')?.addEventListener('click', () => input.focus());

    attachment?.addEventListener('change', function () {
        const file = this.files?.[0];
        attachmentName.classList.toggle('hidden', !file);
        attachmentName.textContent = file ? `Attached: ${file.name}` : '';
    });

    function appendMessage(data) {
        if (!data || Number(data.seller_id) !== sellerId) return;
        if (document.querySelector(`[data-message-id="${data.id}"]`)) return;

        document.getElementById('sellerEmptyChat')?.remove();

        const mine = data.sender_role === 'seller';
        const wrapper = document.createElement('div');
        wrapper.dataset.messageId = data.id;
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
            ${mine ? '' : '<div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#fbf5e9] text-[8px] font-bold text-[#ad791f]">SA</div>'}
            <div class="max-w-[82%] sm:max-w-[68%]">
                ${data.body ? `<div class="rounded-[16px] px-4 py-3 text-[9px] leading-5 shadow-sm ${mine ? 'rounded-br-[5px] bg-[#c99128] text-white' : 'rounded-bl-[5px] border border-[#e9e2d9] bg-white text-[#5f574d]'}">${escapeHtml(data.body)}</div>` : ''}
                ${attachmentHtml}
                <p class="mt-1.5 ${mine ? 'text-right' : ''} text-[8px] text-[#9c9388]">${escapeHtml(data.time || '')}</p>
            </div>`;

        messages.appendChild(wrapper);
        if (preview) preview.textContent = data.body || data.attachment_name || 'Attachment';
        scrollBottom();

        if (!mine) {
            fetch(@json(route('seller.messages.read')), {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json'}
            }).catch(() => {});
        }
    }

    form?.addEventListener('submit', async function (event) {
        event.preventDefault();
        errorBox.classList.add('hidden');
        sendButton.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {'Accept': 'application/json'}
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || Object.values(data.errors || {})?.[0]?.[0] || 'Unable to send message.');

            appendMessage(data.message);
            form.reset();
            attachmentName.classList.add('hidden');
            attachmentName.textContent = '';
            input.focus();
        } catch (error) {
            errorBox.textContent = error.message;
            errorBox.classList.remove('hidden');
        } finally {
            sendButton.disabled = false;
        }
    });

    if (window.Echo) {
        status.textContent = 'Live';
        window.Echo.channel(channelName).listen('.chat.message', appendMessage);
    } else {
        status.textContent = 'Saved mode';
        status.className = 'rounded-full border border-[#eadfc9] bg-[#fffaf2] px-3 py-1.5 text-[8px] font-semibold text-[#a8731f]';
    }
});
</script>
@endpush
