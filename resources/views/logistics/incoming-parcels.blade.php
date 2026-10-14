@extends('layouts.logistics')

@section('title','Incoming Parcels')
@section('page-title','Incoming Parcels')

@section('content')
<section class="space-y-4">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-[9px] text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
        <p class="text-[9px] font-bold uppercase tracking-[.1em] text-[#b97805]">Origin facility intake</p>
        <h2 class="mt-1 text-[20px] font-bold">Incoming Parcels</h2>
        <p class="mt-2 text-[9px] text-[#81786c]">
            Orders shown here belong only to your Logistics network. The first facility is the Seller-area origin hub selected by SARI.
        </p>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        @forelse($orders as $order)
            @php
                $parcel = $parcels->get($order->id);
                $routeStops = $order->deliveryRouteStops->keyBy('stop_key');
                $originFacility = $routeStops->get('origin_facility');
                $destinationFacility = $routeStops->get('destination_facility');
            @endphp

            <article class="rounded-[18px] border border-[#ebe4da] bg-white p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-bold">{{ $order->order_number }}</p>
                        <p class="mt-1 text-[8px] text-[#918677]">
                            {{ $order->seller?->store_name ?: 'SARI Seller' }} → {{ $order->buyer_name }}
                        </p>
                    </div>
                    <span class="rounded-full border px-2.5 py-1 text-[7px] font-bold">
                        {{ strtoupper($parcel?->status ?: 'AWAITING INTAKE') }}
                    </span>
                </div>

                <div class="mt-4 rounded-xl border border-[#efe5d6] bg-[#fffdf8] p-3">
                    <p class="text-[7px] uppercase tracking-[.08em] text-[#9b9184]">Facility route</p>
                    <p class="mt-1 text-[8px] font-semibold text-[#4b4339]">
                        {{ $originFacility?->name ?: 'Origin pending' }}
                        <span class="mx-1 text-[#baaE9e]">→</span>
                        {{ $destinationFacility?->name ?: 'Destination pending' }}
                    </p>
                </div>

                <div class="mt-3 grid grid-cols-2 gap-3 text-[8px]">
                    <div class="rounded-xl bg-[#fcfbf8] p-3">
                        <p class="text-[#958c80]">Rider</p>
                        <p class="mt-1 font-semibold">{{ $order->courier_name ?: '—' }}</p>
                    </div>
                    <div class="rounded-xl bg-[#fcfbf8] p-3">
                        <p class="text-[#958c80]">Picked up</p>
                        <p class="mt-1 font-semibold">{{ $order->picked_up_at?->format('M d, h:i A') ?: '—' }}</p>
                    </div>
                </div>

                @if(!$parcel || $parcel->status === 'awaiting_intake')
                    <form method="POST" action="{{ route('logistics.incoming-parcels.receive', $order) }}" class="mt-4">
                        @csrf
                        <textarea name="notes" rows="2" placeholder="Optional intake note..." class="w-full rounded-xl border px-3 py-2 text-[8px]"></textarea>
                        <button class="mt-2 w-full rounded-xl bg-[#c99128] px-4 py-3 text-[8px] font-bold text-white">
                            Mark Received at Origin Facility
                        </button>
                    </form>
                @else
                    <p class="mt-4 rounded-xl bg-emerald-50 p-3 text-[8px] text-emerald-700">
                        Received {{ $parcel->received_at?->format('M d, Y h:i A') }}
                    </p>
                @endif
            </article>
        @empty
            <div class="lg:col-span-2 rounded-[18px] border border-dashed p-10 text-center text-[9px] text-[#918677]">
                No picked-up parcels waiting for facility intake.
            </div>
        @endforelse
    </div>
</section>
@endsection
