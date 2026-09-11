@extends('layouts.courier')

@section('title', 'My Assignments')
@section('header-title', 'My Assignments')
@section('header-subtitle', 'Dispatched by SARI Logistics')

@section('content')
<div class="mx-auto max-w-[1620px]">
    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-[10px] font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <section class="rounded-[20px] border border-[#eadfc9] bg-[#fffdf8] p-5 sm:p-6">
        <span class="rounded-full border border-[#eadfc9] bg-white px-3 py-1.5 text-[8px] font-semibold uppercase tracking-[.13em] text-[#8a7f70]">Logistics Dispatch</span>
        <h2 class="mt-3 text-[20px] font-bold text-[#211d17]">Assigned delivery jobs</h2>
        <p class="mt-2 text-[9px] leading-5 text-[#817769]">Riders no longer self-accept public delivery requests. SARI Logistics assigns one active job at a time.</p>
    </section>

    @if ($activeOrder)
        <section class="mt-4 rounded-[18px] border border-[#d2dfe9] bg-[#f4f8fb] p-4">
            <p class="text-[9px] font-bold text-[#3C6E91]">Current assignment: {{ $activeOrder->order_number }}</p>
            <p class="mt-1 text-[8px] text-[#6f8190]">{{ $activeOrder->statusLabel() }}</p>
            <a href="{{ route('courier.dashboard') }}" class="mt-3 inline-flex rounded-lg bg-[#3C6E91] px-4 py-2 text-[8px] font-semibold text-white">Open Delivery Workflow</a>
        </section>
    @endif

    <section class="mt-4 grid gap-4 lg:grid-cols-2">
        @forelse ($orders as $order)
            <article class="rounded-[18px] border border-[#eadfc9] bg-[#fffdf8] p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold text-[#29241e]">{{ $order->order_number }}</p>
                        <p class="mt-1 text-[8px] text-[#918677]">{{ $order->seller?->store_name ?: 'SARI Seller' }}</p>
                    </div>
                    <span class="rounded-full border border-[#eadfc9] bg-[#fff6e1] px-2.5 py-1 text-[7px] font-bold text-[#a56b00]">ASSIGNED</span>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-xl border border-[#eee5d6] bg-white p-3"><p class="text-[7px] uppercase tracking-[.1em] text-[#a09484]">Pickup</p><p class="mt-1 text-[9px] font-semibold text-[#2a251f]">{{ $order->pickup_name }}</p><p class="mt-1 text-[7px] leading-4 text-[#8a8073]">{{ $order->pickup_address }}</p></div>
                    <div class="rounded-xl border border-[#eee5d6] bg-white p-3"><p class="text-[7px] uppercase tracking-[.1em] text-[#a09484]">Buyer</p><p class="mt-1 text-[9px] font-semibold text-[#2a251f]">{{ $order->buyer_name }}</p><p class="mt-1 text-[7px] leading-4 text-[#8a8073]">{{ $order->buyer_address }}</p></div>
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-[#eee5d6] pt-4">
                    <p class="text-[9px] font-bold text-[#29241e]">₱{{ number_format((float) $order->delivery_fee, 2) }}</p>
                    <a href="{{ route('courier.dashboard') }}" class="rounded-xl bg-[#D99600] px-5 py-3 text-[9px] font-semibold text-white">Start Workflow</a>
                </div>
            </article>
        @empty
            <div class="lg:col-span-2 rounded-[18px] border border-dashed border-[#dcd2c3] bg-[#fffdf8] p-12 text-center">
                <p class="text-[10px] font-semibold text-[#51483d]">No Logistics assignment yet.</p>
                <p class="mt-2 text-[8px] text-[#918677]">When Logistics assigns an order to you, it will appear here automatically.</p>
            </div>
        @endforelse
    </section>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentCount = {{ $orders->count() }};
    async function checkAssignments() {
        if (document.hidden) return;
        try {
            const response = await fetch(@json(route('courier.orders.live-state')), {
                headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
                credentials: 'same-origin',
                cache: 'no-store'
            });
            if (!response.ok) return;
            const data = await response.json();
            if (Number(data.assigned_count) !== Number(currentCount)) window.location.reload();
        } catch (error) {
            console.debug('Rider assignment polling temporarily unavailable.');
        }
    }
    window.setInterval(checkAssignments, 3000);
});
</script>
@endpush
@endsection
