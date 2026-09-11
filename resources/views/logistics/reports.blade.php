@extends('layouts.logistics')

@section('title', 'Reports | SARI Logistics')
@section('page-title', 'Reports')

@section('content')
<section class="space-y-4">
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-7">
        @foreach([
            ['Waiting Assignment', $stats['waiting_assignment']],
            ['Active Deliveries', $stats['active_deliveries']],
            ['Delivered Today', $stats['delivered_today']],
            ['Delivered This Month', $stats['delivered_this_month']],
            ['Delivery Fees This Month', '₱'.number_format($stats['delivery_fees_this_month'],2)],
            ['Hub Received', $stats['hub_received']],
            ['Hub Sorted', $stats['hub_sorted']],
        ] as [$label,$value])
            <div class="rounded-[18px] border border-[#eee4d3] bg-white p-4">
                <p class="text-[8px] uppercase tracking-[.08em] text-[#978d80]">{{ $label }}</p>
                <p class="mt-2 text-[20px] font-bold text-[#302a22]">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="rounded-[18px] border border-[#eee4d3] bg-white overflow-x-auto">
        <div class="border-b border-[#eee4d3] px-5 py-4">
            <h3 class="text-[12px] font-bold text-[#332c24]">Recent completed deliveries</h3>
        </div>
        <table class="w-full min-w-[900px] text-left">
            <thead class="bg-[#fcfaf6] text-[8px] uppercase tracking-[.08em] text-[#978d80]">
                <tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Seller</th><th class="px-4 py-3">Buyer</th><th class="px-4 py-3">Rider</th><th class="px-4 py-3">Fee</th><th class="px-4 py-3">Delivered</th></tr>
            </thead>
            <tbody class="divide-y divide-[#f0e9df]">
                @forelse($recentDelivered as $order)
                    <tr class="text-[9px] text-[#5f574e]">
                        <td class="px-4 py-3 font-bold">{{ $order->order_number }}</td>
                        <td class="px-4 py-3">{{ $order->seller?->store_name ?: 'SARI Seller' }}</td>
                        <td class="px-4 py-3">{{ $order->buyer_name }}</td>
                        <td class="px-4 py-3">{{ $order->courier_name }}</td>
                        <td class="px-4 py-3">₱{{ number_format((float)$order->delivery_fee,2) }}</td>
                        <td class="px-4 py-3">{{ $order->delivered_at?->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-[9px] text-[#8c8275]">No completed deliveries yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
