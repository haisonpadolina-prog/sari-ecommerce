@extends('layouts.logistics')

@section('title', 'Logistics Dashboard | SARI')
@section('page-title', 'Dashboard Overview')

@section('content')
<section class="space-y-5">
    <div class="rounded-[24px] border border-[#eee4d3] bg-gradient-to-br from-[#fffdf8] to-[#fff7e9] p-6 shadow-[0_14px_40px_rgba(76,58,34,.06)]">
        <p class="text-[10px] font-bold uppercase tracking-[.14em] text-[#b97805]">SARI Logistics</p>
        <h2 class="mt-2 text-[26px] font-bold tracking-[-.035em] text-[#241f18]">Live operations control center</h2>
        <p class="mt-2 max-w-[760px] text-[11px] leading-5 text-[#7e7467]">
            Real marketplace orders, approved Riders, assignments, and delivery statuses now use the same database as Buyer and Seller.
        </p>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-7">
        @foreach([
            ['Pending Riders', $stats['pending_rider_applications'], 'logistics.rider-applications'],
            ['Active Riders', $stats['active_riders'], 'logistics.rider-management'],
            ['Pickup Requests', $stats['pickup_requests'], 'logistics.pickup-requests'],
            ['Active Deliveries', $stats['active_deliveries'], 'logistics.delivery-monitoring'],
            ['Hub Intake', $stats['awaiting_intake'], 'logistics.incoming-parcels'],
            ['Sorting Queue', $stats['sorting_queue'], 'logistics.parcel-sorting'],
            ['Delivered Today', $stats['delivered_today'], 'logistics.reports'],
        ] as [$label, $value, $routeName])
            <a href="{{ route($routeName) }}" class="rounded-[18px] border border-[#eee4d3] bg-white p-4 shadow-[0_8px_24px_rgba(76,58,34,.04)] transition hover:border-[#dfc99d]">
                <p class="text-[8px] font-semibold uppercase tracking-[.08em] text-[#968b7c]">{{ $label }}</p>
                <p class="mt-2 text-[24px] font-bold text-[#2d271f]">{{ $value }}</p>
            </a>
        @endforeach
    </div>

    <div class="rounded-[20px] border border-[#eee4d3] bg-white">
        <div class="border-b border-[#eee4d3] px-5 py-4">
            <h3 class="text-[13px] font-bold text-[#332c24]">Recent logistics activity</h3>
            <p class="mt-1 text-[9px] text-[#948a7c]">Latest real orders moving through pickup and delivery.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] text-left">
                <thead class="bg-[#fcfaf6] text-[8px] uppercase tracking-[.08em] text-[#978d80]">
                    <tr>
                        <th class="px-5 py-3">Order</th>
                        <th class="px-5 py-3">Seller</th>
                        <th class="px-5 py-3">Buyer</th>
                        <th class="px-5 py-3">Rider</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Hub</th>
                        <th class="px-5 py-3">Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0e9df]">
                    @forelse($recentOrders as $order)
                        <tr class="text-[9px] text-[#5f574e]">
                            <td class="px-5 py-3 font-bold text-[#332c24]">{{ $order->order_number }}</td>
                            <td class="px-5 py-3">{{ $order->seller?->store_name ?: 'SARI Seller' }}</td>
                            <td class="px-5 py-3">{{ $order->buyer_name }}</td>
                            <td class="px-5 py-3">{{ $order->courier_name ?: 'Unassigned' }}</td>
                            <td class="px-5 py-3"><span class="rounded-full bg-[#fff6e5] px-2.5 py-1 font-semibold text-[#a96e05]">{{ $order->statusLabel() }}</span></td>
                            <td class="px-5 py-3">{{ $order->logisticsParcel ? strtoupper(str_replace('_',' ',$order->logisticsParcel->status)) : '—' }}</td>
                            <td class="px-5 py-3">{{ $order->updated_at?->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-[9px] text-[#948a7c]">No logistics orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
