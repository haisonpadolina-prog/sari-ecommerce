@extends('layouts.logistics')

@section('title', 'Pickup Requests | SARI Logistics')
@section('page-title', 'Pickup Requests')

@section('content')
<section class="space-y-4">
    <div class="rounded-[20px] border border-[#eee4d3] bg-white p-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[9px] font-bold uppercase tracking-[.1em] text-[#b97805]">Seller → Logistics</p>
                <h2 class="mt-1 text-[20px] font-bold text-[#2c261f]">Ready for pickup</h2>
                <p class="mt-1 text-[9px] text-[#8c8275]">These are real Seller orders marked Ready for Pickup and not yet assigned to a Rider.</p>
            </div>
            <span class="rounded-full bg-[#fff4dd] px-3 py-2 text-[9px] font-bold text-[#a96e05]">{{ $orders->count() }} waiting</span>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-2">
        @forelse($orders as $order)
            <article class="rounded-[18px] border border-[#eee4d3] bg-white p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[12px] font-bold text-[#302a22]">{{ $order->order_number }}</p>
                        <p class="mt-1 text-[8px] text-[#8c8275]">{{ $order->seller?->store_name ?: 'SARI Seller' }}</p>
                    </div>
                    <span class="rounded-full bg-[#fff6e5] px-2.5 py-1 text-[7px] font-bold text-[#a96e05]">READY</span>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-xl bg-[#fcfaf6] p-3">
                        <p class="text-[7px] uppercase tracking-[.08em] text-[#9b9184]">Pickup</p>
                        <p class="mt-1 text-[9px] font-semibold text-[#423a31]">{{ $order->pickup_name ?: 'Seller pickup' }}</p>
                        <p class="mt-1 text-[8px] leading-4 text-[#81776a]">{{ $order->pickup_address }}</p>
                    </div>
                    <div class="rounded-xl bg-[#fcfaf6] p-3">
                        <p class="text-[7px] uppercase tracking-[.08em] text-[#9b9184]">Buyer</p>
                        <p class="mt-1 text-[9px] font-semibold text-[#423a31]">{{ $order->buyer_name }}</p>
                        <p class="mt-1 text-[8px] leading-4 text-[#81776a]">{{ $order->buyer_address }}</p>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between gap-3 border-t border-[#f0e9df] pt-4">
                    <div>
                        <p class="text-[7px] text-[#9b9184]">Ready since</p>
                        <p class="mt-1 text-[8px] font-semibold text-[#5f574e]">{{ $order->ready_at?->format('M d, Y h:i A') ?: 'Just now' }}</p>
                    </div>
                    <a href="{{ route('logistics.delivery-assignment') }}" class="rounded-xl bg-[#d9930a] px-4 py-2.5 text-[8px] font-bold text-white">Assign Rider →</a>
                </div>
            </article>
        @empty
            <div class="xl:col-span-2 rounded-[18px] border border-dashed border-[#ded5c9] bg-white p-10 text-center text-[9px] text-[#8c8275]">No Seller orders are waiting for Rider assignment.</div>
        @endforelse
    </div>
</section>
@endsection
