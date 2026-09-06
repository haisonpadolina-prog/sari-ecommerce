@extends('layouts.courier')
@section('title','Active Deliveries')
@section('header-title','Active Deliveries')
@section('header-subtitle','In Transit')
@section('content')
<div class="mx-auto max-w-[1620px]">
    <section class="rounded-[20px] border border-[#eadfc9] bg-[#fffdf8] p-5"><h2 class="text-[18px] font-bold text-[#211d17]">Active Deliveries</h2><p class="mt-2 text-[9px] text-[#817769]">Packages already picked up from Sellers.</p></section>
    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        @forelse($orders as $order)
            <article class="rounded-[18px] border border-[#d2dfe9] bg-[#f6f9fb] p-5">
                <div class="flex items-start justify-between gap-3"><div><p class="text-[11px] font-bold">{{ $order->order_number }}</p><p class="mt-1 text-[8px] text-[#72808c]">{{ $order->buyer_name }} · {{ $order->buyer_address }}</p></div><span class="rounded-full bg-white px-2.5 py-1 text-[7px] font-bold text-[#3C6E91]">{{ strtoupper($order->statusLabel()) }}</span></div>
                <a href="{{ route('courier.dashboard') }}" class="mt-4 inline-flex rounded-lg bg-[#3C6E91] px-4 py-2.5 text-[8px] font-semibold text-white">Open Active Delivery</a>
            </article>
        @empty
            <div class="lg:col-span-2 rounded-[18px] border border-dashed border-[#ddd2c1] p-10 text-center text-[9px] text-[#918677]">No active in-transit delivery.</div>
        @endforelse
    </div>
</div>
@endsection
