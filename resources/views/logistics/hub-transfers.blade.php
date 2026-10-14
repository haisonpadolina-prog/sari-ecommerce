@extends('layouts.logistics')

@section('title', 'Hub Transfers | SARI Logistics')
@section('page-title', 'Hub Transfers')

@section('content')

<div class="space-y-4">

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[9px] font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[9px] font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="rounded-[20px] border border-[#eadfc9] bg-[#fffdf8] p-5">
        <p class="text-[8px] font-bold uppercase tracking-[.12em] text-[#b97805]">
            Line-haul Operations
        </p>

        <h2 class="mt-2 text-[20px] font-bold text-[#211d17]">
            Hub-to-Hub Transfers
        </h2>

        <p class="mt-2 text-[9px] text-[#817769]">
            Group sorted parcels by destination hub and dispatch them using a truck, van, L300, or other long-distance vehicle.
        </p>
    </section>

    @foreach($transferGroups as $group)

        @php
            $first = $group->first();
        @endphp

        <form
            method="POST"
            action="{{ route('logistics.hub-transfers.store') }}"
            class="rounded-[18px] border border-[#eadfc9] bg-[#fffdf8] p-5"
        >
            @csrf

            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#b97805]">
                        Ready for Hub Transfer
                    </p>

                    <h3 class="mt-2 text-[15px] font-bold">
                        {{ $first->origin_facility_name }}
                        →
                        {{ $first->destination_facility_name }}
                    </h3>
                </div>

                <span class="rounded-full bg-[#fff2d8] px-3 py-1.5 text-[8px] font-bold text-[#a56b00]">
                    {{ $group->count() }} PARCELS
                </span>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl border border-[#eee5d6]">
                @foreach($group as $parcel)

                    <div class="flex items-center justify-between border-b border-[#eee5d6] px-4 py-3 last:border-0">

                        <div>
                            <p class="text-[9px] font-bold">
                                {{ $parcel->order_number }}
                            </p>

                            <p class="mt-1 text-[7px] text-[#918677]">
                                {{ $parcel->buyer_name }}
                                ·
                                {{ $parcel->sorting_zone }}
                            </p>
                        </div>

                        <input
                            type="hidden"
                            name="parcel_ids[]"
                            value="{{ $parcel->parcel_id }}"
                        >

                        <span class="text-[7px] font-bold text-emerald-700">
                            SORTED
                        </span>

                    </div>

                @endforeach
            </div>

            <div class="mt-4 grid gap-3 lg:grid-cols-3">

                <select
                    name="vehicle_type"
                    required
                    class="h-11 rounded-xl border border-[#e5ddd2] bg-white px-3 text-[9px]"
                >
                    <option value="">
                        Select vehicle type
                    </option>

                    <option value="L300">
                        L300
                    </option>

                    <option value="Van">
                        Van
                    </option>

                    <option value="Truck">
                        Truck
                    </option>

                    <option value="Multicab">
                        Multicab
                    </option>

                    <option value="Other">
                        Other
                    </option>
                </select>

                <input
                    name="vehicle_plate"
                    placeholder="Plate number"
                    class="h-11 rounded-xl border border-[#e5ddd2] bg-white px-3 text-[9px]"
                >

                <input
                    name="driver_name"
                    placeholder="Driver name"
                    class="h-11 rounded-xl border border-[#e5ddd2] bg-white px-3 text-[9px]"
                >

            </div>

            <button
                class="mt-4 h-11 rounded-xl bg-[#D99600] px-6 text-[9px] font-bold text-white"
            >
                Create Transfer for {{ $group->count() }} Parcels
            </button>

        </form>

    @endforeach

    <section class="rounded-[20px] border border-[#eadfc9] bg-[#fffdf8] p-5">

        <h3 class="text-[15px] font-bold">
            Transfer History
        </h3>

        <div class="mt-4 space-y-3">

            @forelse($transfers as $transfer)

                <div class="rounded-xl border border-[#eee5d6] bg-white p-4">

                    <div class="flex flex-wrap items-center justify-between gap-3">

                        <div>
                            <p class="text-[10px] font-bold">
                                {{ $transfer->transfer_code }}
                            </p>

                            <p class="mt-1 text-[8px] text-[#817769]">
                                {{ $transfer->origin_name }}
                                →
                                {{ $transfer->destination_name }}
                            </p>

                            <p class="mt-1 text-[7px] text-[#918677]">
                                {{ $transfer->vehicle_type }}
                                @if($transfer->vehicle_plate)
                                    · {{ $transfer->vehicle_plate }}
                                @endif

                                · {{ $transfer->parcel_count }} parcels
                            </p>
                        </div>

                        <div class="flex items-center gap-2">

                            <span class="rounded-full bg-[#f6f1e8] px-3 py-1.5 text-[7px] font-bold uppercase">
                                {{ str_replace('_', ' ', $transfer->status) }}
                            </span>

                            @if($transfer->status === 'preparing')

                                <form
                                    method="POST"
                                    action="{{ route('logistics.hub-transfers.dispatch', $transfer->id) }}"
                                >
                                    @csrf

                                    <button class="rounded-lg bg-[#D99600] px-4 py-2 text-[8px] font-bold text-white">
                                        Dispatch
                                    </button>
                                </form>

                            @elseif($transfer->status === 'in_transit')

                                <form
                                    method="POST"
                                    action="{{ route('logistics.hub-transfers.receive', $transfer->id) }}"
                                >
                                    @csrf

                                    <button class="rounded-lg bg-emerald-600 px-4 py-2 text-[8px] font-bold text-white">
                                        Receive at Destination
                                    </button>
                                </form>

                            @endif

                        </div>

                    </div>

                </div>

            @empty

                <p class="text-[9px] text-[#918677]">
                    No Hub Transfers yet.
                </p>

            @endforelse

        </div>

    </section>

</div>

@endsection