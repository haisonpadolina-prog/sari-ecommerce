@extends('layouts.courier')

@section('title', 'Rider Dashboard')
@section('header-title', 'Rider Dashboard')
@section('header-subtitle', 'Logistics Assigned Deliveries')

@section('content')
<div class="mx-auto max-w-[1620px]">
    @if (session('success'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-[10px] font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-[10px] font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <section class="mb-4 flex flex-col justify-between gap-4 border-b border-[#eadfc9] pb-5 sm:flex-row sm:items-end">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-[15px] font-bold text-[#211d17]">Rider Operations</h2>
                <span class="rounded-full border border-[#eadfc9] bg-white px-2.5 py-1 text-[8px] font-semibold text-[#8a7f70]">Logistics Dispatch</span>
            </div>
            <p class="mt-2 text-[9px] text-[#817769]">Delivery jobs are assigned by SARI Logistics. Confirm every pickup and delivery step here.</p>
        </div>
        <a href="{{ route('courier.requests') }}" class="rounded-xl bg-[#D99600] px-4 py-2.5 text-[9px] font-semibold text-white">View My Assignments →</a>
    </section>

    <section class="grid grid-cols-2 gap-3 md:grid-cols-4">
        @foreach([
            ['Waiting to Start', $stats['assigned_waiting'], 'bg-[#fff2d8] text-[#bb7b00]'],
            ['Active Deliveries', $stats['active_deliveries'], 'bg-[#eaf2f7] text-[#3C6E91]'],
            ['Completed Today', $stats['completed_today'], 'bg-[#eaf3ed] text-[#4F7D63]'],
            ["Today's Earnings", '₱'.number_format($stats['earnings_today'],2), 'bg-[#f0ecf6] text-[#715A86]'],
        ] as [$label, $value, $tone])
            <article class="rounded-2xl border border-[#eadfc9] bg-[#fffdf8] p-4">
                <div class="grid h-9 w-9 place-items-center rounded-xl {{ $tone }}"><span class="text-[9px] font-bold">•</span></div>
                <p class="mt-3 text-[8px] text-[#817769]">{{ $label }}</p>
                <p class="mt-1 text-[20px] font-bold text-[#201c16]">{{ $value }}</p>
            </article>
        @endforeach
    </section>

    <section class="mt-4 overflow-hidden rounded-[20px] border border-[#eadfc9] bg-[#fffdf8]">
        <div class="border-b border-[#eadfc9] px-5 py-4">
            <h3 class="text-[13px] font-bold text-[#211d17]">Current Delivery</h3>
            <p class="mt-1 text-[8px] text-[#918677]">The Buyer, Seller, Logistics, and Rider all use this same MarketplaceOrder record.</p>
        </div>

        @if ($currentDelivery instanceof \App\Models\MarketplaceOrder)
            @php
                $status = $currentDelivery->status;
                $pickupDone = in_array($status, ['in_transit', 'arrived_buyer', 'delivered'], true);
                $transitDone = in_array($status, ['arrived_buyer', 'delivered'], true);
                $deliveredDone = $status === 'delivered';
            @endphp

            <div class="p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="text-[13px] font-bold text-[#201c16]">{{ $currentDelivery->order_number }}</h4>
                            <span class="rounded-full border border-[#d2dfe9] bg-[#f2f7fb] px-2.5 py-1 text-[7px] font-bold text-[#3C6E91]">{{ strtoupper($currentDelivery->statusLabel()) }}</span>
                        </div>
                        <p class="mt-2 text-[8px] text-[#817769]">{{ $currentDelivery->pickup_name }} → {{ $currentDelivery->buyer_name }}</p>
                    </div>
                    <div class="rounded-xl border border-[#eadfc9] bg-white px-4 py-2.5">
                        <p class="text-[7px] text-[#918677]">Delivery Fee</p>
                        <p class="mt-1 text-[12px] font-bold text-[#201c16]">₱{{ number_format((float)$currentDelivery->delivery_fee,2) }}</p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-3 gap-3">
                    <div class="rounded-[16px] border {{ $pickupDone ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }} p-4 text-center">
                        <div class="mx-auto grid h-9 w-9 place-items-center rounded-full {{ $pickupDone ? 'bg-emerald-600' : 'bg-[#D99600]' }} text-white">{{ $pickupDone ? '✓' : '1' }}</div>
                        <p class="mt-3 text-[9px] font-bold text-[#2d2822]">Picked Up</p>
                    </div>
                    <div class="rounded-[16px] border {{ $transitDone ? 'border-emerald-200 bg-emerald-50' : ($status === 'in_transit' ? 'border-blue-200 bg-blue-50' : 'border-[#e5ddd1] bg-white') }} p-4 text-center">
                        <div class="mx-auto grid h-9 w-9 place-items-center rounded-full {{ $transitDone ? 'bg-emerald-600' : ($status === 'in_transit' ? 'bg-[#3C6E91]' : 'bg-[#e5ddd1]') }} text-white">{{ $transitDone ? '✓' : '2' }}</div>
                        <p class="mt-3 text-[9px] font-bold text-[#2d2822]">In Transit</p>
                    </div>
                    <div class="rounded-[16px] border {{ $deliveredDone ? 'border-emerald-200 bg-emerald-50' : 'border-[#e5ddd1] bg-white' }} p-4 text-center">
                        <div class="mx-auto grid h-9 w-9 place-items-center rounded-full {{ $deliveredDone ? 'bg-emerald-600' : 'bg-[#e5ddd1]' }} text-white">{{ $deliveredDone ? '✓' : '3' }}</div>
                        <p class="mt-3 text-[9px] font-bold text-[#2d2822]">Delivered</p>
                    </div>
                </div>

                <div class="mt-5 rounded-[16px] border border-[#eadfc9] bg-white p-4">
                    @if ($status === 'courier_accepted')
                        <p class="text-[8px] font-semibold text-[#8a6400]">SARI Logistics assigned this delivery to you.</p>
                        <form method="POST" action="{{ route('courier.orders.proceed-pickup', $currentDelivery) }}" class="mt-3">@csrf<button class="w-full rounded-xl bg-[#D99600] px-4 py-3 text-[9px] font-semibold text-white">Proceed to Pickup →</button></form>
                    @elseif ($status === 'heading_pickup')
                        <p class="text-[8px] font-semibold text-[#8a6400]">You are heading to the Seller pickup point.</p>
                        <form method="POST" action="{{ route('courier.orders.arrived-pickup', $currentDelivery) }}" class="mt-3">@csrf<button class="w-full rounded-xl bg-[#D99600] px-4 py-3 text-[9px] font-semibold text-white">I Arrived at Pickup ✓</button></form>
                    @elseif ($status === 'arrived_pickup')
                        <p class="text-[8px] font-semibold text-[#8a6400]">Check the package, then confirm the Seller handoff.</p>
                        <form method="POST" action="{{ route('courier.orders.confirm-pickup', $currentDelivery) }}" class="mt-3">@csrf<button class="w-full rounded-xl bg-[#3C6E91] px-4 py-3 text-[9px] font-semibold text-white">Confirm Item Pickup →</button></form>
                    @elseif ($status === 'in_transit')
                        <p class="text-[8px] font-semibold text-[#3C6E91]">Package is in transit. Confirm only when you reach the Buyer.</p>
                        <form method="POST" action="{{ route('courier.orders.arrived-buyer', $currentDelivery) }}" class="mt-3">@csrf<button class="w-full rounded-xl bg-[#3C6E91] px-4 py-3 text-[9px] font-semibold text-white">Confirm Arrived at Buyer →</button></form>
                    @elseif ($status === 'arrived_buyer')
                        <p class="text-[8px] font-semibold text-[#4F7D63]">Buyer location reached. Complete the handoff.</p>
                        <form method="POST" action="{{ route('courier.orders.complete', $currentDelivery) }}" class="mt-3">@csrf<button class="w-full rounded-xl bg-[#4F7D63] px-4 py-3 text-[9px] font-semibold text-white">Confirm Delivery Complete ✓</button></form>
                    @endif
                </div>
            </div>
        @else
            <div class="p-10 text-center">
                <p class="text-[10px] font-semibold text-[#51483d]">No active assignment.</p>
                <p class="mt-2 text-[8px] text-[#918677]">SARI Logistics will dispatch an eligible delivery to your account.</p>
                <a href="{{ route('courier.requests') }}" class="mt-4 inline-flex rounded-xl bg-[#D99600] px-5 py-3 text-[9px] font-semibold text-white">Check Assignments</a>
            </div>
        @endif
    </section>

    <section class="mt-4 rounded-[20px] border border-[#eadfc9] bg-[#fffdf8] p-5">
        <h3 class="text-[12px] font-bold text-[#211d17]">My Latest Assignments</h3>
        <div class="mt-4 space-y-2">
            @forelse($assignments as $order)
                <div class="flex items-center justify-between gap-3 rounded-xl border border-[#eadfc9] bg-white p-3">
                    <div><p class="text-[9px] font-bold text-[#2a251f]">{{ $order->order_number }}</p><p class="mt-1 text-[7px] text-[#84796c]">{{ $order->pickup_name }} → {{ $order->buyer_name }}</p></div>
                    <span class="rounded-full bg-[#fff2d8] px-2.5 py-1 text-[7px] font-bold text-[#aa6d00]">ASSIGNED</span>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-[#ddd2c1] p-5 text-center text-[8px] text-[#918677]">No assigned jobs.</div>
            @endforelse
        </div>
    </section>
</div>
@endsection
