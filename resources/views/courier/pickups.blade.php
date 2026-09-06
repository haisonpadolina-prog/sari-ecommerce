@extends('layouts.courier')
@section('title','Pick Up Orders')
@section('header-title','Pick Up Orders')
@section('header-subtitle','Seller Pickup Queue')
@section('content')
<div class="mx-auto max-w-[1620px]">
    <section class="rounded-[20px] border border-[#eadfc9] bg-[#fffdf8] p-5"><h2 class="text-[18px] font-bold text-[#211d17]">Pick Up Orders</h2><p class="mt-2 text-[9px] text-[#817769]">Accepted orders that are still in the Seller pickup stage.</p></section>
    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        @forelse($orders as $order)
            <article class="rounded-[18px] border border-[#eadfc9] bg-[#fffdf8] p-5">
                <div class="flex items-start justify-between gap-3"><div><p class="text-[11px] font-bold">{{ $order->order_number }}</p><p class="mt-1 text-[8px] text-[#918677]">{{ $order->pickup_name }}</p></div><span class="rounded-full bg-[#fff2d8] px-2.5 py-1 text-[7px] font-bold text-[#a56b00]">{{ strtoupper($order->statusLabel()) }}</span></div>
                <p class="mt-4 text-[8px] text-[#817769]">{{ $order->pickup_address }}</p>
                <a href="{{ route('courier.dashboard') }}" class="mt-4 inline-flex rounded-lg bg-[#D99600] px-4 py-2.5 text-[8px] font-semibold text-white">Open Delivery</a>
            </article>
        @empty
            <div class="lg:col-span-2 rounded-[18px] border border-dashed border-[#ddd2c1] p-10 text-center text-[9px] text-[#918677]">No pickup-stage orders.</div>
        @endforelse
    </div>
</div>
@endsection
