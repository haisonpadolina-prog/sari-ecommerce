@extends('layouts.logistics')

@section('title', 'Last Mile Assignment | SARI Logistics')
@section('page-title', 'Last Mile Assignment')

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
            Destination Hub Dispatch
        </p>

        <h2 class="mt-2 text-[20px] font-bold text-[#211d17]">
            Assign local delivery Riders
        </h2>

        <p class="mt-2 max-w-4xl text-[9px] leading-5 text-[#817769]">
            Parcels received at the destination hub are grouped by barangay.
            Orders in the same barangay can be assigned to one local Rider.
        </p>
    </section>

    @forelse($deliveryGroups as $group)

        @php
            $first = $group->first();

            $eligibleRiders = $riders->filter(
                function ($rider) use ($first) {
                    return strtolower(trim((string) $rider->municipality_name))
                            === strtolower(trim((string) $first->destination_municipality))
                        &&
                        strtolower(trim((string) $rider->barangay_name))
                            === strtolower(trim((string) $first->barangay_name));
                }
            );
        @endphp

        <form
            method="POST"
            action="{{ route('logistics.last-mile-assignment.batch-assign') }}"
            class="overflow-hidden rounded-[20px] border border-[#eadfc9] bg-[#fffdf8]"
        >
            @csrf

            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-[#eadfc9] p-5">

                <div>
                    <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#b97805]">
                        Last-Mile Delivery Area
                    </p>

                    <h3 class="mt-2 text-[15px] font-bold text-[#211d17]">
                        {{ $first->barangay_name }}
                    </h3>

                    <p class="mt-1 text-[8px] text-[#817769]">
                        {{ $first->destination_facility_name }}
                        ·
                        {{ $first->destination_municipality }}
                    </p>
                </div>

                <span class="rounded-full bg-[#fff2d8] px-3 py-1.5 text-[8px] font-bold text-[#a56b00]">
                    {{ $group->count() }}
                    {{ $group->count() === 1 ? 'PARCEL' : 'PARCELS' }}
                </span>

            </div>

            <div class="p-5">

                <div class="overflow-hidden rounded-xl border border-[#eee5d6]">

                    @foreach($group as $parcel)

                        <input
                            type="hidden"
                            name="order_ids[]"
                            value="{{ $parcel->order_id }}"
                        >

                        <div class="grid gap-3 border-b border-[#eee5d6] px-4 py-3 last:border-b-0 md:grid-cols-[150px_1fr_2fr]">

                            <div>
                                <p class="text-[7px] uppercase tracking-wide text-[#a09484]">
                                    Order
                                </p>

                                <p class="mt-1 text-[9px] font-bold text-[#29241e]">
                                    {{ $parcel->order_number }}
                                </p>
                            </div>

                            <div>
                                <p class="text-[7px] uppercase tracking-wide text-[#a09484]">
                                    Buyer
                                </p>

                                <p class="mt-1 text-[9px] font-semibold text-[#29241e]">
                                    {{ $parcel->buyer_name }}
                                </p>
                            </div>

                            <div>
                                <p class="text-[7px] uppercase tracking-wide text-[#a09484]">
                                    Delivery Address
                                </p>

                                <p class="mt-1 text-[8px] text-[#817769]">
                                    {{ $parcel->buyer_address }}
                                </p>
                            </div>

                        </div>

                    @endforeach

                </div>

                <div class="mt-4 grid gap-3 lg:grid-cols-[1fr_auto] lg:items-end">

                    <div>
                        <label class="mb-1.5 block text-[8px] font-semibold text-[#5f574e]">
                            Local Rider for {{ $first->barangay_name }}
                        </label>

                        <select
                            name="rider_id"
                            required
                            class="h-11 w-full rounded-xl border border-[#e5ddd2] bg-white px-3 text-[9px]"
                        >
                            <option value="">
                                Choose local Rider
                            </option>

                            @foreach($eligibleRiders as $rider)
                                <option value="{{ $rider->id }}">
                                    {{ $rider->first_name }}
                                    {{ $rider->last_name }}
                                    —
                                    {{ $rider->vehicle_type }}
                                </option>
                            @endforeach
                        </select>

                        @if($eligibleRiders->isEmpty())
                            <p class="mt-2 text-[8px] font-semibold text-rose-600">
                                No active Rider is registered for
                                {{ $first->barangay_name }}.
                            </p>
                        @endif
                    </div>

                    <button
                        type="submit"
                        @disabled($eligibleRiders->isEmpty())
                        class="h-11 rounded-xl bg-[#D99600] px-6 text-[8px] font-bold text-white disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        Assign {{ $group->count() }}
                        {{ $group->count() === 1 ? 'Parcel' : 'Parcels' }}
                    </button>

                </div>

            </div>

        </form>

    @empty

        <div class="rounded-[18px] border border-dashed border-[#ddd2c1] bg-[#fffdf8] p-10 text-center">
            <p class="text-[10px] font-semibold text-[#51483d]">
                No destination-hub parcels awaiting last-mile assignment.
            </p>
        </div>

    @endforelse


    <section class="rounded-[20px] border border-[#eadfc9] bg-[#fffdf8] p-5">

        <h3 class="text-[15px] font-bold text-[#211d17]">
            Last-Mile Assignments
        </h3>

        <div class="mt-4 space-y-3">

            @forelse($assignments as $assignment)

                <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-[#eee5d6] bg-white p-4">

                    <div>
                        <p class="text-[9px] font-bold text-[#29241e]">
                            {{ $assignment->assignment_code }}
                        </p>

                        <p class="mt-1 text-[8px] text-[#817769]">
                            {{ $assignment->facility_name }}
                            →
                            {{ $assignment->delivery_area }}
                        </p>

                        <p class="mt-1 text-[7px] text-[#918677]">
                            Rider:
                            {{ $assignment->first_name }}
                            {{ $assignment->last_name }}
                            ·
                            {{ $assignment->vehicle_type }}
                            ·
                            {{ $assignment->order_count }}
                            {{ $assignment->order_count === 1 ? 'parcel' : 'parcels' }}
                        </p>
                    </div>

                    <span class="rounded-full bg-[#f6f1e8] px-3 py-1.5 text-[7px] font-bold uppercase">
                        {{ str_replace('_', ' ', $assignment->status) }}
                    </span>

                </div>

            @empty

                <p class="text-[9px] text-[#918677]">
                    No last-mile assignments yet.
                </p>

            @endforelse

        </div>

    </section>

</div>

@endsection