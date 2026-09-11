@extends('layouts.logistics')

@section('title', 'Delivery Assignment | SARI Logistics')
@section('page-title', 'Delivery Assignment')

@section('content')
<section class="space-y-4">
    @if(session('success'))
        <div class="rounded-xl border border-[#cfe2d5] bg-[#f4faf6] px-4 py-3 text-[9px] font-semibold text-[#56816a]">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-[#ead0d0] bg-[#fff6f6] px-4 py-3 text-[9px] font-semibold text-[#a65f5f]">{{ $errors->first() }}</div>
    @endif

    <div class="rounded-[20px] border border-[#eee4d3] bg-white p-5">
        <p class="text-[9px] font-bold uppercase tracking-[.1em] text-[#b97805]">Logistics Dispatch</p>
        <h2 class="mt-1 text-[20px] font-bold text-[#2c261f]">Assign approved Riders</h2>
        <p class="mt-1 text-[9px] text-[#8c8275]">Only active Riders approved through Logistics are selectable. A Rider can have only one active delivery at a time.</p>
    </div>

    <div class="space-y-3">
        @forelse($orders as $order)
            <article class="rounded-[18px] border border-[#eee4d3] bg-white p-5">
                <div class="grid gap-4 lg:grid-cols-[1fr_1fr_auto] lg:items-center">
                    <div>
                        <p class="text-[11px] font-bold text-[#302a22]">{{ $order->order_number }}</p>
                        <p class="mt-1 text-[8px] text-[#8c8275]">{{ $order->seller?->store_name ?: 'SARI Seller' }} → {{ $order->buyer_name }}</p>
                        <p class="mt-2 text-[8px] leading-4 text-[#81776a]">{{ $order->pickup_address }}</p>
                    </div>

                    <form method="POST" action="{{ route('logistics.delivery-assignment.assign', $order) }}" class="contents">
                        @csrf
                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5f574e]">Select Rider</label>
                            <select name="rider_id" required class="h-10 w-full rounded-xl border border-[#e5ddd2] bg-white px-3 text-[9px] text-[#40382f]">
                                <option value="">Choose available Rider</option>
                                @foreach($riders as $rider)
                                    @php($busy = in_array(strtolower((string) $rider->email), $busyRiderEmails, true))
                                    <option value="{{ $rider->id }}" @disabled($busy)>
                                        {{ trim($rider->first_name.' '.$rider->last_name) }} — {{ $rider->vehicle_type }}{{ $busy ? ' (Busy)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="h-10 rounded-xl bg-[#d9930a] px-5 text-[8px] font-bold text-white">Assign</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-[18px] border border-dashed border-[#ded5c9] bg-white p-10 text-center text-[9px] text-[#8c8275]">No unassigned Ready for Pickup orders.</div>
        @endforelse
    </div>
</section>
@endsection
