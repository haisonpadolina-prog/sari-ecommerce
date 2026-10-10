@extends('layouts.buyer')

@section('title', 'Notifications — SARI')
@section('page-title', 'Notifications')

@section('content')
@include('components.buyer.header')

<main class="mx-auto w-full max-w-[1180px] px-4 py-6 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[.16em] text-[#b47b1e]">Buyer updates</p>
            <h1 class="mt-1 text-[28px] font-bold tracking-[-.035em] text-[#201b17]">Notifications</h1>
            <p class="mt-1 text-[11px] text-[#8d8377]">Order and delivery updates from your actual SARI purchases.</p>
        </div>

        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('buyer.notifications.read-all') }}">
                @csrf
                <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl border border-[#dcc9a4] bg-[#fffaf0] px-4 text-[10px] font-semibold text-[#9c6815] transition hover:bg-[#fff3d8] focus:outline-none focus:ring-4 focus:ring-[#d89a25]/10">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    @if (session('success'))
        <div class="mt-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-[11px] font-medium text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-6 overflow-hidden rounded-[20px] border border-[#ebe3d8] bg-white shadow-[0_12px_35px_rgba(52,42,28,.04)]">
        @forelse ($notifications as $notification)
            <form method="POST" action="{{ route('buyer.notifications.read', ['event' => $notification->id]) }}" class="border-b border-[#f0e9df] last:border-b-0">
                @csrf
                <button type="submit" class="flex w-full items-start gap-4 px-4 py-4 text-left transition hover:bg-[#fffaf1] sm:px-5 {{ $notification->is_read ? 'bg-white' : 'bg-[#fff9eb]' }}">
                    <span class="mt-0.5 grid h-10 w-10 shrink-0 place-items-center rounded-xl {{ $notification->is_read ? 'bg-[#f5f2ed] text-[#81786e]' : 'bg-[#fff0ca] text-[#b7770c]' }}">
                        @if (in_array($notification->status, ['courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit', 'arrived_buyer']))
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 7h11v9H3z"></path><path d="M14 10h4l3 3v3h-7z"></path><circle cx="7" cy="18" r="1.5"></circle><circle cx="18" cy="18" r="1.5"></circle></svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 7h14l-1 13H6L5 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path></svg>
                        @endif
                    </span>

                    <span class="min-w-0 flex-1">
                        <span class="flex flex-wrap items-center gap-2">
                            <strong class="text-[12px] font-bold text-[#302a24]">{{ $notification->title }}</strong>
                            @unless ($notification->is_read)
                                <span class="h-2 w-2 rounded-full bg-[#d9930a]" aria-label="Unread"></span>
                            @endunless
                        </span>
                        <span class="mt-1 block text-[10px] leading-5 text-[#746b61]">{{ $notification->message }}</span>
                        <span class="mt-2 block text-[9px] font-medium text-[#a0978b]">
                            {{ $notification->seller?->store_name ?: 'SARI Seller' }}
                            @if ($notification->order)
                                · {{ $notification->order->order_number }}
                            @endif
                            · {{ $notification->created_at?->diffForHumans() }}
                        </span>
                    </span>

                    <span class="mt-2 text-[#b7770c]" aria-hidden="true">→</span>
                </button>
            </form>
        @empty
            <div class="px-6 py-14 text-center">
                <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#f6f2eb] text-[#9b9185]">
                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path><path d="M10 21h4"></path></svg>
                </div>
                <h2 class="mt-4 text-[14px] font-bold text-[#38312a]">No notifications yet</h2>
                <p class="mt-1 text-[10px] text-[#8c8378]">Order and shipping updates will appear here.</p>
            </div>
        @endforelse
    </div>

    @if ($notifications->hasPages())
        <div class="mt-5">{{ $notifications->links() }}</div>
    @endif
</main>
@endsection
