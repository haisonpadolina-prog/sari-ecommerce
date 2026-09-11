@extends('layouts.logistics')

@section('title', 'Delivery Monitoring | SARI Logistics')
@section('page-title', 'Delivery Monitoring')

@section('content')
<section class="space-y-4">
    <div class="rounded-[20px] border border-[#eee4d3] bg-white p-5">
        <p class="text-[9px] font-bold uppercase tracking-[.1em] text-[#b97805]">Live Marketplace Status</p>
        <h2 class="mt-1 text-[20px] font-bold text-[#2c261f]">Active Rider deliveries</h2>
        <p class="mt-1 text-[9px] text-[#8c8275]">Every Rider action updates the same MarketplaceOrder seen by Buyer and Seller.</p>
    </div>

    <div class="rounded-[18px] border border-[#eee4d3] bg-white overflow-x-auto">
        <table class="w-full min-w-[980px] text-left">
            <thead class="bg-[#fcfaf6] text-[8px] uppercase tracking-[.08em] text-[#978d80]">
                <tr>
                    <th class="px-4 py-3">Order</th><th class="px-4 py-3">Seller</th><th class="px-4 py-3">Buyer</th><th class="px-4 py-3">Rider</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Hub</th><th class="px-4 py-3">Updated</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f0e9df]">
                @forelse($activeOrders as $order)
                    <tr class="text-[9px] text-[#5f574e]">
                        <td class="px-4 py-3 font-bold text-[#332c24]">{{ $order->order_number }}</td>
                        <td class="px-4 py-3">{{ $order->seller?->store_name ?: 'SARI Seller' }}</td>
                        <td class="px-4 py-3">{{ $order->buyer_name }}</td>
                        <td class="px-4 py-3">{{ $order->courier_name }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-[#f2f7fb] px-2.5 py-1 font-semibold text-[#3C6E91]">{{ $order->statusLabel() }}</span></td>
                        <td class="px-4 py-3">{{ $order->logisticsParcel ? strtoupper(str_replace('_',' ',$order->logisticsParcel->status)) : 'LEGACY' }}</td>
                        <td class="px-4 py-3">{{ $order->updated_at?->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-[9px] text-[#8c8275]">No active deliveries.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="rounded-[18px] border border-[#eee4d3] bg-white p-5">
        <h3 class="text-[12px] font-bold text-[#332c24]">Delivered today</h3>
        <div class="mt-3 grid gap-2 md:grid-cols-2 xl:grid-cols-3">
            @forelse($deliveredToday as $order)
                <div class="rounded-xl bg-[#f6faf7] p-3 text-[8px] text-[#5e6f63]">
                    <span class="font-bold">{{ $order->order_number }}</span> · {{ $order->courier_name }} · {{ $order->delivered_at?->format('h:i A') }}
                </div>
            @empty
                <p class="text-[9px] text-[#8c8275]">No completed deliveries today.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
