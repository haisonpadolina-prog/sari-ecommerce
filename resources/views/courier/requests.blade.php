@extends('layouts.courier')

@section('title', 'Delivery Requests')
@section('header-title', 'Delivery Requests')
@section('header-subtitle', 'Ready for Pickup')

@section('content')
<div class="mx-auto max-w-[1620px]">
    @if (session('success'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-[10px] font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-[10px] font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <section class="rounded-[20px] border border-[#eadfc9] bg-[#fffdf8] p-5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <span class="rounded-full border border-[#eadfc9] bg-white px-3 py-1.5 text-[8px] font-semibold uppercase tracking-[.13em] text-[#8a7f70]">Live Queue</span>
                <h2 class="mt-3 text-[20px] font-bold text-[#211d17]">Available Delivery Requests</h2>
                <p class="mt-2 text-[9px] leading-5 text-[#817769]">Orders appear here only after the Seller clicks <strong>Ready for Pickup</strong>.</p>
            </div>
            <span class="rounded-xl bg-[#fff2d8] px-4 py-2.5 text-[9px] font-bold text-[#aa6d00]">{{ $orders->count() }} available</span>
        </div>
    </section>

    @if ($activeOrder)
        <section class="mt-4 rounded-[18px] border border-[#d2dfe9] bg-[#f4f8fb] p-4">
            <p class="text-[9px] font-bold text-[#3C6E91]">You already have an active delivery: {{ $activeOrder->order_number }}</p>
            <p class="mt-1 text-[8px] text-[#6f8190]">Complete it before accepting another request.</p>
            <a href="{{ route('courier.dashboard') }}" class="mt-3 inline-flex rounded-lg bg-[#3C6E91] px-4 py-2 text-[8px] font-semibold text-white">Open Active Delivery</a>
        </section>
    @endif

    <section class="mt-4 grid gap-4 lg:grid-cols-2">
        @forelse ($orders as $order)
            <article class="rounded-[18px] border border-[#eadfc9] bg-[#fffdf8] p-5">
                <div class="flex items-start justify-between gap-4">
                    <div><p class="text-[11px] font-bold text-[#29241e]">{{ $order->order_number }}</p><p class="mt-1 text-[8px] text-[#918677]">{{ $order->seller?->store_name ?: 'SARI Seller' }}</p></div>
                    <span class="rounded-full border border-[#eadfc9] bg-[#fff6e1] px-2.5 py-1 text-[7px] font-bold text-[#a56b00]">READY</span>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-xl border border-[#eee5d6] bg-white p-3"><p class="text-[7px] uppercase tracking-[.1em] text-[#a09484]">Pickup</p><p class="mt-1 text-[9px] font-semibold text-[#2a251f]">{{ $order->pickup_name }}</p><p class="mt-1 text-[7px] leading-4 text-[#8a8073]">{{ $order->pickup_address }}</p></div>
                    <div class="rounded-xl border border-[#eee5d6] bg-white p-3"><p class="text-[7px] uppercase tracking-[.1em] text-[#a09484]">Buyer</p><p class="mt-1 text-[9px] font-semibold text-[#2a251f]">{{ $order->buyer_name }}</p><p class="mt-1 text-[7px] leading-4 text-[#8a8073]">{{ $order->buyer_address }}</p></div>
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-[#eee5d6] pt-4">
                    <div><p class="text-[7px] uppercase tracking-[.1em] text-[#a09484]">Courier Fee</p><p class="mt-1 text-[13px] font-bold text-[#29241e]">₱{{ number_format((float) $order->delivery_fee, 2) }}</p></div>
                    <form method="POST" action="{{ route('courier.orders.accept', $order) }}">@csrf
                        <button @if ($activeOrder) disabled @endif class="rounded-xl bg-[#D99600] px-5 py-3 text-[9px] font-semibold text-white shadow-[0_10px_24px_rgba(217,150,0,.16)] transition hover:bg-[#BA7B00] disabled:cursor-not-allowed disabled:bg-[#d7d0c4]">Accept Request</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="lg:col-span-2 rounded-[18px] border border-dashed border-[#dcd2c3] bg-[#fffdf8] p-12 text-center">
                <p class="text-[10px] font-semibold text-[#51483d]">No seller pickup requests yet.</p>
                <p class="mt-2 text-[8px] text-[#918677]">A request appears automatically when a Seller marks an order Ready for Pickup.</p>
            </div>
        @endforelse
    </section>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentCount = {{ $orders->count() }};
    async function checkRequests() {
        if (document.hidden) return;
        try {
            const response = await fetch(@json(route('courier.orders.live-state')), {
                headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
                credentials: 'same-origin',
                cache: 'no-store'
            });
            if (!response.ok) return;
            const data = await response.json();
            if (Number(data.available_count) !== Number(currentCount)) window.location.reload();
        } catch (error) {
            console.debug('Courier request polling temporarily unavailable.');
        }
    }
    window.setInterval(checkRequests, 3000);
});
</script>
@endpush
@endsection
