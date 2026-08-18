@extends('layouts.admin')

@section('title', 'Chat / Messaging — SARI Admin')
@section('page-title', 'Chat / Messaging')

@section('content')
<div class="mx-auto w-full max-w-[1800px]">

    @if (session('success'))
        <div class="mb-5 rounded-[18px] border border-[#d5e5dc] bg-[#f3f8f5] px-5 py-4 text-[12px] font-medium text-[#56816a]">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-[18px] border border-[#ead7d7] bg-[#fdf5f5] px-5 py-4 text-[12px] text-[#a45f5f]">{{ $errors->first() }}</div>
    @endif

    {{-- INTRO --}}
    <section class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6 lg:p-7">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-[#e8dfd1] bg-[#fcfaf6] px-3 py-1.5 text-[9px] font-semibold uppercase tracking-[0.14em] text-[#a27428]">
                    <span class="h-2 w-2 rounded-full bg-[#c9952f]"></span> Admin Inbox
                </div>
                <h2 class="mt-3 text-[22px] font-bold tracking-[-0.03em] text-[#211c16] sm:text-[24px]">Chat / Messaging</h2>
                <p class="mt-2 max-w-[720px] text-[12px] leading-6 text-[#81786c] sm:text-[13px]">Respond to seller support, product review, account, and compliance concerns in real time.</p>
            </div>
            <div class="flex items-center gap-2 rounded-xl border border-[#d9e6dd] bg-[#f3f8f5] px-4 py-3 text-[10px] font-semibold text-[#56816a]">
                <span class="h-2 w-2 rounded-full bg-[#68a07b]"></span>
                <span id="adminRealtimeStatus">Connecting to Reverb…</span>
            </div>
        </div>
    </section>

    {{-- SUMMARY --}}
    <section class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                ['label' => 'Total Messages', 'value' => $stats['total_messages'], 'sub' => 'Seller and admin messages'],
                ['label' => 'Unread Seller Messages', 'value' => $stats['unread'], 'sub' => 'Waiting for admin review'],
                ['label' => 'Seller Threads', 'value' => $stats['seller_threads'], 'sub' => 'Active support conversations'],
                ['label' => 'Admin Replies', 'value' => $stats['admin_sent'], 'sub' => 'Messages sent by admin'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="rounded-[18px] border border-[#e7e1d8] bg-white p-5">
                <div class="flex items-start justify-between gap-4">
                    <div><p class="text-[11px] font-medium text-[#797168]">{{ $card['label'] }}</p><h3 class="mt-2 text-[27px] font-bold tracking-[-0.04em] text-[#211d18]">{{ $card['value'] }}</h3><p class="mt-2 text-[10px] text-[#9a9186]">{{ $card['sub'] }}</p></div>
                    <div class="grid h-11 w-11 place-items-center rounded-xl bg-[#f3f7fa] text-[#627f99]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path></svg></div>
                </div>
            </div>
        @endforeach
    </section>

    {{-- WORKSPACE --}}
    <section class="mt-5 overflow-hidden rounded-[22px] border border-[#ebe4da] bg-white">
        <div class="grid min-h-[690px] grid-cols-1 lg:grid-cols-[310px_1fr] 2xl:grid-cols-[320px_1fr_300px]">

            {{-- SELLERS --}}
            <aside class="border-b border-[#eee8df] lg:border-b-0 lg:border-r">
                <div class="border-b border-[#eee8df] p-4">
                    <h3 class="text-[14px] font-bold text-[#2b261f]">Seller Conversations</h3>
                    <p class="mt-1 text-[9px] text-[#948b7f]">{{ $sellers->count() }} seller account{{ $sellers->count() === 1 ? '' : 's' }}</p>
                    <div class="relative mt-4"><input id="adminSellerSearch" type="search" placeholder="Search sellers..." class="h-10 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none focus:border-[#c99a3d] focus:ring-4 focus:ring-[#c99a3d]/10"></div>
                </div>

                <div id="adminSellerList" class="max-h-[610px] overflow-y-auto">
                    @forelse ($sellers as $seller)
                        @php $active = $selectedSeller && $selectedSeller->id === $seller->id; @endphp
                        <a href="{{ route('admin.messages', ['seller' => $seller->id]) }}" data-seller-row data-seller-name="{{ strtolower(($seller->store_name ?: '') . ' ' . $seller->email) }}" data-seller-id="{{ $seller->id }}" class="block w-full border-b border-[#f0ebe4] px-4 py-4 text-left transition {{ $active ? 'bg-[#fbf7ef]' : 'hover:bg-[#fdfbf8]' }}">
                            <div class="flex gap-3">
                                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#f3f6f8] text-[10px] font-bold text-[#657f94]">{{ strtoupper(substr($seller->store_name ?: 'SS', 0, 2)) }}</div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2"><div class="min-w-0"><p class="truncate text-[10px] font-semibold text-[#312b25]">{{ $seller->store_name ?: $seller->email }}</p><p class="mt-0.5 truncate text-[8px] text-[#948b7f]">{{ $seller->email }}</p></div><span data-unread-badge class="{{ $seller->unread_messages_count ? 'grid' : 'hidden' }} h-5 min-w-[20px] place-items-center rounded-full bg-[#c99128] px-1.5 text-[8px] font-bold text-white">{{ $seller->unread_messages_count }}</span></div>
                                    <p class="mt-2 text-[8px] text-[#8d857a]">{{ $seller->message_count }} message{{ $seller->message_count === 1 ? '' : 's' }} • {{ $seller->warning_count }} warning{{ $seller->warning_count === 1 ? '' : 's' }}</p>
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
                            <div><p class="text-[11px] font-bold text-[#2f2923]">{{ $selectedSeller->store_name ?: $selectedSeller->email }}</p><div class="mt-1 flex items-center gap-2"><span class="text-[8px] text-[#8e8579]">Seller</span><span class="h-1 w-1 rounded-full bg-[#c8c1b8]"></span><span class="text-[8px] font-medium {{ $selectedSeller->isSuspended() ? 'text-[#a96565]' : 'text-[#56816a]' }}">{{ ucfirst($selectedSeller->account_status) }}</span></div></div>
                        </div>
                        <a href="{{ route('admin.seller-compliance') }}" class="rounded-xl border border-[#e6dfd5] bg-white px-4 py-2.5 text-[9px] font-semibold text-[#62594e] hover:bg-[#fcf8f1]">Open Compliance</a>
                    </div>

                    <div id="adminChatMessages" class="flex-1 overflow-y-auto bg-[#fdfbf8] px-4 py-5 sm:px-6">
                        @if ($messages->isEmpty())
                            <div id="adminEmptyChat" class="flex h-full min-h-[360px] items-center justify-center text-center"><div><div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#f3f6f8] text-[#657f94]"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path></svg></div><p class="mt-4 text-[11px] font-bold text-[#4f473e]">No messages yet</p><p class="mt-1 text-[9px] text-[#958c80]">Send the first message to this seller.</p></div></div>
                        @endif

                        @foreach ($messages as $message)
                            <div data-message-id="{{ $message->id }}" class="mt-5 {{ $message->sender_role === 'admin' ? 'flex justify-end' : 'flex items-end gap-2.5' }}">
                                @if ($message->sender_role === 'seller')<div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#f3f6f8] text-[8px] font-bold text-[#657f94]">{{ strtoupper(substr($selectedSeller->store_name ?: 'SS', 0, 2)) }}</div>@endif
                                <div class="max-w-[78%] sm:max-w-[65%]">
                                    @if ($message->body)<div class="rounded-[16px] px-4 py-3 text-[10px] leading-5 shadow-sm {{ $message->sender_role === 'admin' ? 'rounded-br-[5px] bg-[#c99128] text-white' : 'rounded-bl-[5px] border border-[#e9e2d9] bg-white text-[#5f574d]' }}">{{ $message->body }}</div>@endif
                                    @if ($message->attachment_path)
                                        <div class="mt-2 overflow-hidden rounded-[14px] border border-[#e9e2d9] bg-white p-2">
                                            @if ($message->attachmentIsImage())<a href="{{ route('chat.attachments.show', $message) }}" target="_blank"><img src="{{ route('chat.attachments.show', $message) }}" alt="{{ $message->attachment_name }}" class="max-h-[260px] w-full rounded-xl object-contain"></a>
                                            @else<a href="{{ route('chat.attachments.show', $message) }}" target="_blank" class="block rounded-xl bg-[#fcfaf7] p-3 text-[9px] font-semibold text-[#50483f]">{{ $message->attachment_name }}</a>@endif
                                        </div>
                                    @endif
                                    <p class="mt-1.5 {{ $message->sender_role === 'admin' ? 'text-right' : '' }} text-[8px] text-[#9c9388]">{{ $message->created_at->format('h:i A') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-[#eee8df] bg-white p-4">
                        <form id="adminChatForm" method="POST" action="{{ route('admin.messages.send', $selectedSeller) }}" enctype="multipart/form-data" class="rounded-[16px] border border-[#e6dfd5] bg-[#fcfbf9] p-3 transition focus-within:border-[#c99a3d] focus-within:ring-4 focus-within:ring-[#c99a3d]/10">
                            @csrf
                            <textarea id="adminChatInput" name="message" rows="3" placeholder="Write a message to seller..." class="w-full resize-none bg-transparent text-[10px] leading-5 text-[#3e3831] outline-none placeholder:text-[#aaa197]"></textarea>
                            <div id="adminAttachmentName" class="mt-2 hidden rounded-xl border border-[#e8e1d7] bg-white px-3 py-2 text-[9px] text-[#62594e]"></div>
                            <div class="mt-2 flex flex-col gap-3 border-t border-[#eee8df] pt-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-2"><label class="grid h-9 w-9 cursor-pointer place-items-center rounded-lg text-[#756d63] hover:bg-[#f5efe5] hover:text-[#a8731f]"><input id="adminChatAttachment" name="attachment" type="file" accept="image/*,.pdf,.doc,.docx,.zip,.txt" class="hidden"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 12.5 14.5 6a3 3 0 0 1 4.2 4.2l-8 8a5 5 0 0 1-7.1-7.1l8.3-8.3"></path></svg></label><span class="text-[8px] text-[#958c80]">Attachments up to 8 MB</span></div>
                                <button id="adminChatSend" type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[#c99128] px-5 text-[10px] font-semibold text-white hover:bg-[#b47e1e] disabled:opacity-50">Send Message<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m4 4 17 8-17 8 3-8-3-8Z"></path><path d="M7 12h14"></path></svg></button>
                            </div>
                        </form>
                        <p id="adminChatError" class="mt-2 hidden text-[9px] text-[#a45f5f]"></p>
                    </div>
                @else
                    <div class="flex flex-1 items-center justify-center p-8 text-center"><div><p class="text-[12px] font-bold text-[#4f473e]">No seller selected</p><p class="mt-2 text-[10px] text-[#958c80]">A seller conversation will appear here.</p></div></div>
                @endif
            </div>

            {{-- DETAILS --}}
            <aside class="hidden border-l border-[#eee8df] 2xl:block">
                <div class="border-b border-[#eee8df] p-5"><p class="text-[11px] font-bold text-[#312b25]">Seller Details</p><p class="mt-1 text-[9px] text-[#948b7f]">Support and compliance context</p></div>
                @if ($selectedSeller)
                    <div class="p-5">
                        <div class="flex flex-col items-center text-center"><div class="grid h-16 w-16 place-items-center rounded-full bg-[#f3f6f8] text-[16px] font-bold text-[#657f94]">{{ strtoupper(substr($selectedSeller->store_name ?: 'SS', 0, 2)) }}</div><p class="mt-3 text-[13px] font-bold text-[#302a24]">{{ $selectedSeller->store_name ?: $selectedSeller->email }}</p><p class="mt-1 text-[9px] text-[#92897e]">{{ $selectedSeller->email }}</p><span class="mt-3 rounded-full border border-[#dce5ed] bg-[#f4f7fa] px-2.5 py-1 text-[8px] font-semibold text-[#617d96]">Seller</span></div>
                        <div class="mt-5 space-y-3">
                            <div class="rounded-[14px] border border-[#ece6dd] bg-[#fcfbf8] p-4"><p class="text-[9px] text-[#948b7f]">Account Status</p><p class="mt-2 text-[10px] font-semibold {{ $selectedSeller->isSuspended() ? 'text-[#a96565]' : 'text-[#56816a]' }}">{{ ucfirst($selectedSeller->account_status) }}</p></div>
                            <div class="rounded-[14px] border border-[#ece6dd] bg-[#fcfbf8] p-4"><p class="text-[9px] text-[#948b7f]">Warnings</p><p class="mt-2 text-[13px] font-bold text-[#a8731f]">{{ $selectedSeller->warning_count }} / 3</p></div>
                            @if ($selectedSeller->isSuspended())<div class="rounded-[14px] border border-[#ead8d8] bg-[#fcf6f6] p-4"><p class="text-[9px] text-[#a96565]">Suspended Until</p><p class="mt-2 text-[10px] font-semibold text-[#7f5151]">{{ $selectedSeller->suspended_until?->format('M d, Y') }}</p></div>@endif
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </section>
    <div class="h-5"></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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

    const escapeHtml = (value) => String(value ?? '').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#039;');
    const scrollBottom = () => { if (messages) messages.scrollTop = messages.scrollHeight; };
    scrollBottom();

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

    function appendMessage(data) {
        if (!data) return;
        if (Number(data.seller_id) !== Number(selectedSellerId)) {
            if (data.sender_role === 'seller') bumpUnread(data.seller_id);
            return;
        }
        if (!messages || document.querySelector(`[data-message-id="${data.id}"]`)) return;
        document.getElementById('adminEmptyChat')?.remove();
        const mine = data.sender_role === 'admin';
        const wrapper = document.createElement('div'); wrapper.dataset.messageId = data.id; wrapper.className = `mt-5 ${mine ? 'flex justify-end' : 'flex items-end gap-2.5'}`;
        let attach = '';
        if (data.attachment_url) {
            attach = (data.attachment_mime || '').startsWith('image/')
                ? `<div class="mt-2 overflow-hidden rounded-[14px] border border-[#e9e2d9] bg-white p-2"><a href="${escapeHtml(data.attachment_url)}" target="_blank"><img src="${escapeHtml(data.attachment_url)}" alt="${escapeHtml(data.attachment_name)}" class="max-h-[260px] w-full rounded-xl object-contain"></a></div>`
                : `<div class="mt-2 rounded-[14px] border border-[#e9e2d9] bg-white p-2"><a href="${escapeHtml(data.attachment_url)}" target="_blank" class="block rounded-xl bg-[#fcfaf7] p-3 text-[9px] font-semibold text-[#50483f]">${escapeHtml(data.attachment_name || 'Attachment')}</a></div>`;
        }
        wrapper.innerHTML = `${mine ? '' : '<div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#f3f6f8] text-[8px] font-bold text-[#657f94]">SE</div>'}<div class="max-w-[78%] sm:max-w-[65%]">${data.body ? `<div class="rounded-[16px] px-4 py-3 text-[10px] leading-5 shadow-sm ${mine ? 'rounded-br-[5px] bg-[#c99128] text-white' : 'rounded-bl-[5px] border border-[#e9e2d9] bg-white text-[#5f574d]'}">${escapeHtml(data.body)}</div>` : ''}${attach}<p class="mt-1.5 ${mine ? 'text-right' : ''} text-[8px] text-[#9c9388]">${escapeHtml(data.time || '')}</p></div>`;
        messages.appendChild(wrapper); scrollBottom();
        if (!mine) fetch(`/admin/messages/${selectedSellerId}/read`, {method:'POST', headers:{'X-CSRF-TOKEN':@json(csrf_token()), 'Accept':'application/json'}}).catch(()=>{});
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

    if (window.Echo) {
        status.textContent = 'Live'; window.Echo.channel(channelName).listen('.chat.message', appendMessage);
    } else {
        status.textContent = 'Saved mode';
    }
});
</script>
@endpush
