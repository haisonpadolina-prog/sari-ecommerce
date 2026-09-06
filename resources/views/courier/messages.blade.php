@extends('layouts.courier')

@section('title', 'Chat / Messaging')
@section('header-title', 'Chat / Messaging')
@section('header-subtitle', 'Buyer, Seller & Support Messages')


@push('styles')
<style>
    .page-card {
        border: 1px solid #eee4d3;
        background: #fffdf9;
        box-shadow: 0 8px 24px rgba(75, 59, 30, .035);
    }
    .page-card-hover {
        transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
    }
    .page-card-hover:hover {
        transform: translateY(-2px);
        border-color: #dbc89f;
        box-shadow: 0 14px 32px rgba(75, 59, 30, .07);
    }
</style>
@endpush


@section('content')
<div class="mx-auto max-w-[1620px]">

    <section class="reveal mb-5 border-b border-[#eee4d3] pb-5">
        <h2 class="text-[15px] font-bold text-[#211d17]">Courier Messages</h2>
        <p class="mt-2 text-[10px] leading-5 text-[#817769]">
            Keep delivery conversations organized between the courier, buyer, seller, and SARI support.
        </p>
    </section>

    <section class="reveal page-card overflow-hidden rounded-2xl">
        <div class="grid min-h-[640px] lg:grid-cols-[330px_minmax(0,1fr)]">

            <aside class="border-b border-[#eee4d3] bg-[#fffdf9] lg:border-b-0 lg:border-r">
                <div class="border-b border-[#eee4d3] p-4">
                    <div class="relative">
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#a09687]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.5-3.5"></path>
                        </svg>
                        <input type="search" placeholder="Search conversations..." class="h-10 w-full rounded-xl border border-[#e5dac9] bg-white pl-9 pr-3 text-[9px] outline-none focus:border-[#d9930a]">
                    </div>
                </div>

                <div class="divide-y divide-[#f0e8db]">
                    @foreach($conversations as $index => $conversation)
                        <button
                            type="button"
                            class="conversation w-full p-4 text-left transition {{ $index===0 ? 'bg-[#fff7e7]' : 'hover:bg-[#fffaf1]' }}"
                        >
                            <div class="flex items-start gap-3">
                                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full {{ $index===0 ? 'bg-[#3C6E91] text-white' : 'bg-[#f3ede3] text-[#776d60]' }} text-[9px] font-bold">
                                    {{ $conversation['initials'] }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="truncate text-[9px] font-semibold text-[#332d25]">{{ $conversation['name'] }}</p>
                                        <span class="shrink-0 text-[7px] text-[#9b9183]">{{ $conversation['time'] }}</span>
                                    </div>
                                    <p class="mt-1 text-[7px] text-[#9a9081]">{{ $conversation['role'] }}</p>
                                    <p class="mt-1.5 truncate text-[8px] text-[#766c5f]">{{ $conversation['preview'] }}</p>
                                </div>

                                @if($conversation['unread'] > 0)
                                    <span class="grid h-5 min-w-5 place-items-center rounded-full bg-[#d9930a] px-1 text-[7px] font-bold text-white">{{ $conversation['unread'] }}</span>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
            </aside>

            <div class="flex min-h-[640px] flex-col bg-white">
                <div class="flex items-center justify-between border-b border-[#eee4d3] px-4 py-4 sm:px-5">
                    <div class="flex items-center gap-3">
                        <div class="grid h-10 w-10 place-items-center rounded-full bg-[#3C6E91] text-[9px] font-bold text-white">AR</div>
                        <div>
                            <p class="text-[10px] font-semibold text-[#302a23]">Angela Ramos</p>
                            <p class="mt-1 text-[7px] text-[#918677]">Buyer · SARI-1058 · Online</p>
                        </div>
                    </div>
                    <button type="button" class="rounded-xl border border-[#e6dccb] bg-white px-3 py-2 text-[8px] font-semibold text-[#62594d]">Order Details</button>
                </div>

                <div id="messageArea" class="flex-1 space-y-4 overflow-y-auto bg-[#fcfaf6] p-4 sm:p-6">
                    <div class="mx-auto w-fit rounded-full border border-[#e8dfd0] bg-white px-3 py-1 text-[7px] font-semibold text-[#8d8273]">Today</div>

                    @foreach($messages as $message)
                        <div class="flex {{ $message['from']==='me' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[78%] rounded-2xl px-4 py-3 {{ $message['from']==='me' ? 'bg-[#3C6E91] text-white' : 'border border-[#e9dfcf] bg-white text-[#51493f]' }}">
                                <p class="text-[9px] leading-5">{{ $message['text'] }}</p>
                                <p class="mt-1.5 text-[7px] {{ $message['from']==='me' ? 'text-white/65' : 'text-[#a09687]' }}">{{ $message['time'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-[#eee4d3] bg-[#fffdf9] p-4">
                    <div class="flex items-end gap-2">
                        <button type="button" class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-[#e6dccb] bg-white text-[#6b6256]">
                            +
                        </button>
                        <textarea id="messageInput" rows="1" placeholder="Type a message..." class="min-h-11 flex-1 resize-none rounded-xl border border-[#e6dccb] bg-white px-4 py-3 text-[9px] outline-none focus:border-[#3C6E91]"></textarea>
                        <button id="sendMessage" type="button" class="h-11 rounded-xl bg-[#3C6E91] px-5 text-[9px] font-semibold text-white transition hover:-translate-y-0.5">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('messageInput');
    const send = document.getElementById('sendMessage');
    const area = document.getElementById('messageArea');

    const sendDemo = () => {
        const text = input.value.trim();
        if (!text) return;

        const wrapper = document.createElement('div');
        wrapper.className = 'flex justify-end';
        wrapper.innerHTML = `
            <div class="max-w-[78%] rounded-2xl bg-[#3C6E91] px-4 py-3 text-white">
                <p class="text-[9px] leading-5"></p>
                <p class="mt-1.5 text-[7px] text-white/65">Just now</p>
            </div>
        `;
        wrapper.querySelector('p').textContent = text;
        area.appendChild(wrapper);
        input.value = '';
        area.scrollTop = area.scrollHeight;

        window.SariCourierToast?.('Message sent', 'This is a local UI demo. Real-time courier messaging can be connected next.');
    };

    send?.addEventListener('click', sendDemo);
    input?.addEventListener('keydown', event => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendDemo();
        }
    });
});
</script>
@endpush
