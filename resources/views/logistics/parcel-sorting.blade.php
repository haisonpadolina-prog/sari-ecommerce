@extends('layouts.logistics')

@section('title','Parcel Sorting')
@section('page-title','Parcel Sorting')

@section('content')
<section class="space-y-4">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-[9px] text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
        <p class="text-[9px] font-bold uppercase tracking-[.1em] text-[#b97805]">Network sorting</p>
        <h2 class="mt-1 text-[20px] font-bold">Sorting Queue</h2>
        <p class="mt-2 text-[9px] text-[#81786c]">
            Route labels show the origin hub and the destination hub nearest the Buyer.
        </p>
    </div>

    <div class="space-y-3">
        @forelse($parcels as $parcel)
            @php
                $routeStops = $parcel->order?->deliveryRouteStops?->keyBy('stop_key') ?? collect();
                $originFacility = $routeStops->get('origin_facility');
                $destinationFacility = $routeStops->get('destination_facility');
            @endphp

            <article class="rounded-[18px] border border-[#ebe4da] bg-white p-4">
                <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold">{{ $parcel->order?->order_number }}</p>
                        <p class="mt-1 text-[8px] text-[#918677]">
                            {{ $parcel->order?->seller?->store_name ?: 'Seller' }} → {{ $parcel->order?->buyer_name }}
                        </p>
                        <div class="mt-2 flex flex-wrap items-center gap-1.5 text-[7px]">
                            <span class="rounded-full bg-[#fff6e5] px-2 py-1 font-semibold text-[#9d6d18]">{{ $originFacility?->name ?: 'Origin pending' }}</span>
                            <span class="text-[#b3aaa0]">→</span>
                            <span class="rounded-full bg-[#f6f3ed] px-2 py-1 font-semibold text-[#71685e]">{{ $destinationFacility?->name ?: 'Destination pending' }}</span>
                        </div>
                        <p class="mt-2 text-[7px] text-[#a0978c]">
                            Received {{ $parcel->received_at?->format('M d, Y h:i A') ?: '—' }}
                        </p>
                    </div>

                    @if($parcel->status === 'received')
                        <form method="POST" action="{{ route('logistics.parcel-sorting.sort', $parcel) }}" class="flex flex-col gap-2 sm:flex-row">
                            @csrf
                            <input name="sorting_zone" required maxlength="80" placeholder="Zone / rack (e.g. A-01)" class="h-10 rounded-xl border px-3 text-[8px]">
                            <input name="notes" maxlength="1000" placeholder="Optional note" class="h-10 rounded-xl border px-3 text-[8px]">
                            <button class="rounded-xl bg-[#56816a] px-4 py-2 text-[8px] font-bold text-white">
                                Mark Sorted
                            </button>
                        </form>
                    @else
                        <div class="text-right">
                            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-[7px] font-bold text-emerald-700">
                                SORTED · {{ $parcel->sorting_zone }}
                            </span>
                            <p class="mt-1 text-[7px] text-[#918677]">{{ $parcel->sorted_at?->format('M d, h:i A') }}</p>
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-[18px] border border-dashed p-10 text-center text-[9px] text-[#918677]">
                No parcels in the sorting queue.
            </div>
        @endforelse
    </div>
</section>
@endsection
