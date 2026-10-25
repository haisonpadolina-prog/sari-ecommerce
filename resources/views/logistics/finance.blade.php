@extends('layouts.logistics')

@section('title', 'Finance & Payouts — SARI Logistics')
@section('page-title', 'Finance & Payouts')

@section('content')
<div class="mx-auto max-w-[1600px] space-y-5">
    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[10px] font-semibold text-emerald-700">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[10px] font-semibold text-rose-700">{{ $errors->first() }}</div>@endif

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-6 shadow-sm">
        <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#a06b0b]">Shipping Economics</p>
        <h2 class="mt-2 text-xl font-bold text-[#211d17]">{{ $logistics->displayName() }}</h2>
        <p class="mt-2 max-w-4xl text-[10px] leading-5 text-[#7e756a]">Gross shipping revenue minus pickup Rider and final-mile Rider shares becomes the Logistics margin. COD amounts enter the wallet only after verified Rider remittance.</p>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @foreach([
            ['Gross shipping', data_get($stats, 'gross_shipping', data_get($stats, 'gross', 0))],
            ['Pickup Rider cost', data_get($stats, 'pickup_rider_cost', data_get($stats, 'pickup_cost', 0))],
            ['Last-mile Rider cost', data_get($stats, 'last_mile_rider_cost', data_get($stats, 'last_mile_cost', 0))],
            ['Net Logistics', data_get($stats, 'net_logistics', data_get($stats, 'net', 0))],
            ['Awaiting COD remittance', data_get($stats, 'pending_remittance', data_get($stats, 'pending', 0))],
        ] as [$label,$value])
            <article class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm"><p class="text-[8px] uppercase tracking-[.1em] text-[#8a8175]">{{ $label }}</p><p class="mt-2 text-[18px] font-bold text-[#211d17]">₱{{ number_format((float)$value,2) }}</p></article>
        @endforeach
    </section>

    @include('finance.payout-form', ['payoutAction'=>route('logistics.finance.payout'),'wallet'=>$wallet,'payoutTitle'=>'Withdraw Logistics Earnings'])

    <section class="grid gap-5 xl:grid-cols-2">
        <div class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
            <h3 class="text-[13px] font-bold">Recent Shipping Earnings</h3>
            <div class="mt-4 space-y-2">
                @forelse($earnings as $earning)
                    <div class="grid grid-cols-[1fr_auto] gap-3 rounded-xl border border-[#eee6da] px-4 py-3 text-[9px]"><div><strong>{{ $earning->order?->order_number ?: 'Order' }}</strong><div class="mt-1 text-[#817769]">Shipping ₱{{ number_format((float)$earning->gross_shipping_revenue,2) }} · Riders ₱{{ number_format((float)$earning->pickup_rider_cost + (float)$earning->last_mile_rider_cost,2) }}</div></div><div class="text-right"><strong>₱{{ number_format((float)$earning->net_logistics_amount,2) }}</strong><div class="mt-1 text-[#817769]">{{ strtoupper(str_replace('_',' ',$earning->status)) }}</div></div></div>
                @empty<p class="text-[9px] text-[#817769]">No Logistics earnings yet.</p>@endforelse
            </div>
            <div class="mt-4">{{ $earnings->links() }}</div>
        </div>

        <div class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
            <h3 class="text-[13px] font-bold">Withdrawal Requests</h3>
            <div class="mt-4 space-y-2">
                @forelse($payoutRequests as $payout)
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-[#eee6da] px-4 py-3 text-[9px]"><div><strong>{{ $payout->request_code }}</strong><div class="mt-1 text-[#817769]">{{ $payout->destination_label }}</div></div><div class="text-right"><strong>₱{{ number_format((float)$payout->amount,2) }}</strong><div class="mt-1 text-[#817769]">{{ strtoupper($payout->status) }}</div></div></div>
                @empty<p class="text-[9px] text-[#817769]">No withdrawal requests yet.</p>@endforelse
            </div>
        </div>
    </section>

    <a href="{{ route('logistics.remittances.index') }}" class="inline-flex rounded-xl border border-[#d9c8a6] bg-[#fff9ef] px-4 py-3 text-[9px] font-semibold text-[#9b6708]">Open COD Remittance Reconciliation →</a>
</div>
@endsection
