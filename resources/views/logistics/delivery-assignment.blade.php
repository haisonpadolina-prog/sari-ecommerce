@extends('layouts.logistics')

@section('title', 'Delivery Assignment | SARI Logistics')
@section('page-title', 'Delivery Assignment')

@section('content')
<section class="space-y-4">

    @if(session('success'))
        <div
            class="rounded-xl border border-[#cfe2d5] bg-[#f4faf6] px-4 py-3 text-[9px] font-semibold text-[#56816a]"
        >
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div
            class="rounded-xl border border-[#ead0d0] bg-[#fff6f6] px-4 py-3 text-[9px] font-semibold text-[#a65f5f]"
        >
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Header --}}
    <div
        class="rounded-[20px] border border-[#eee4d3] bg-white p-5"
    >
        <p
            class="text-[9px] font-bold uppercase tracking-[.1em] text-[#b97805]"
        >
            Logistics Dispatch
        </p>

        <h2
            class="mt-1 text-[20px] font-bold text-[#2c261f]"
        >
            Assign pickup Riders
        </h2>

        <p
            class="mt-1 max-w-3xl text-[9px] leading-4 text-[#8c8275]"
        >
            Verified orders from the same Seller, pickup location,
            and origin hub are grouped together so one Rider can
            collect all parcels in a single pickup run.
        </p>
    </div>

    {{-- Pickup Groups --}}
    <div class="space-y-4">

        @forelse($pickupGroups as $pickupGroup)

            @php
                $firstOrder = $pickupGroup->first();

                $routeStops = $firstOrder
                    ->deliveryRouteStops
                    ->keyBy('stop_key');

                $originFacility =
                    $routeStops->get(
                        'origin_facility'
                    );

                $orderCount =
                    $pickupGroup->count();

                $sellerName =
                    $firstOrder->seller?->store_name
                    ?: $firstOrder->pickup_name
                    ?: 'SARI Seller';
            @endphp

            <article
                class="overflow-hidden rounded-[20px] border border-[#eee4d3] bg-white"
            >

                {{-- Group Header --}}
                <div
                    class="border-b border-[#eee4d3] px-5 py-4"
                >
                    <div
                        class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
                    >

                        <div>
                            <p
                                class="text-[8px] font-bold uppercase tracking-[.1em] text-[#b97805]"
                            >
                                Seller Pickup
                            </p>

                            <h3
                                class="mt-1 text-[15px] font-bold text-[#302a22]"
                            >
                                {{ $sellerName }}
                            </h3>

                            <p
                                class="mt-1 text-[8px] text-[#8c8275]"
                            >
                                {{ $firstOrder->pickup_address }}
                            </p>
                        </div>

                        <div
                            class="flex flex-wrap items-center gap-2"
                        >
                            <span
                                class="rounded-full bg-[#fff6e5] px-3 py-1.5 text-[8px] font-bold text-[#9d6d18]"
                            >
                                {{ $orderCount }}
                                {{ $orderCount === 1 ? 'ORDER' : 'ORDERS' }}
                            </span>

                            <span
                                class="rounded-full bg-[#f4faf6] px-3 py-1.5 text-[8px] font-bold text-[#56816a]"
                            >
                                VERIFIED
                            </span>
                        </div>

                    </div>
                </div>

                {{-- Pickup Route --}}
                <div
                    class="border-b border-[#eee4d3] bg-[#fffdf9] px-5 py-4"
                >
                    <p
                        class="text-[7px] font-bold uppercase tracking-[.08em] text-[#a39787]"
                    >
                        Pickup Route
                    </p>

                    <div
                        class="mt-2 flex flex-wrap items-center gap-2 text-[8px]"
                    >
                        <span
                            class="rounded-full bg-[#fff6e5] px-3 py-1.5 font-semibold text-[#9d6d18]"
                        >
                            {{ $sellerName }}
                        </span>

                        <span
                            class="text-[#b3aaa0]"
                        >
                            →
                        </span>

                        <span
                            class="rounded-full bg-[#f7f2e9] px-3 py-1.5 font-semibold text-[#756a5e]"
                        >
                            {{ $originFacility?->name ?: 'Origin hub pending' }}
                        </span>
                    </div>
                </div>

                {{-- Orders --}}
                <div class="px-5 py-4">

                    <div
                        class="mb-3 flex items-center justify-between"
                    >
                        <div>
                            <p
                                class="text-[9px] font-bold text-[#302a22]"
                            >
                                Orders for pickup
                            </p>

                            <p
                                class="mt-0.5 text-[8px] text-[#8c8275]"
                            >
                                All orders below will be assigned to the same Rider.
                            </p>
                        </div>
                    </div>

                    <div
                        class="overflow-hidden rounded-xl border border-[#eee4d3]"
                    >

                        @foreach($pickupGroup as $order)

                            <div
                                class="grid gap-2 border-b border-[#f0e8dc] px-4 py-3 last:border-b-0 md:grid-cols-[140px_1fr_1fr]"
                            >

                                <div>
                                    <p
                                        class="text-[7px] uppercase tracking-wide text-[#a39787]"
                                    >
                                        Order
                                    </p>

                                    <p
                                        class="mt-1 text-[9px] font-bold text-[#302a22]"
                                    >
                                        {{ $order->order_number }}
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-[7px] uppercase tracking-wide text-[#a39787]"
                                    >
                                        Buyer
                                    </p>

                                    <p
                                        class="mt-1 text-[9px] font-semibold text-[#40382f]"
                                    >
                                        {{ $order->buyer_name }}
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-[7px] uppercase tracking-wide text-[#a39787]"
                                    >
                                        Delivery Address
                                    </p>

                                    <p
                                        class="mt-1 text-[8px] leading-4 text-[#81776a]"
                                    >
                                        {{ $order->buyer_address }}
                                    </p>
                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

                {{-- Assignment --}}
                <form
                    method="POST"
                    action="{{ route('logistics.delivery-assignment.batch-assign') }}"
                    class="border-t border-[#eee4d3] bg-[#fffdf9] px-5 py-4"
                >
                    @csrf

                    @foreach($pickupGroup as $order)
                        <input
                            type="hidden"
                            name="order_ids[]"
                            value="{{ $order->id }}"
                        >
                    @endforeach

                    <div
                        class="grid gap-3 lg:grid-cols-[1fr_auto] lg:items-end"
                    >

                        <div>
                            <label
                                class="mb-1.5 block text-[8px] font-semibold text-[#5f574e]"
                            >
                                Select Pickup Rider
                            </label>

                            <select
                                name="rider_id"
                                required
                                class="h-11 w-full rounded-xl border border-[#e5ddd2] bg-white px-3 text-[9px] text-[#40382f]"
                            >
                                <option value="">
                                    Choose available Rider
                                </option>

                                @foreach($riders as $rider)

                                    @php
                                        $busy = in_array(
                                            strtolower(
                                                (string) $rider->email
                                            ),
                                            $busyRiderEmails,
                                            true
                                        );
                                    @endphp

                                    <option
                                        value="{{ $rider->id }}"
                                        @disabled($busy)
                                    >
                                        {{ trim($rider->first_name.' '.$rider->last_name) }}
                                        —
                                        {{ $rider->vehicle_type }}

                                        {{ $busy ? ' (Busy)' : '' }}
                                    </option>

                                @endforeach
                            </select>
                        </div>

                        <button
                            type="submit"
                            class="h-11 rounded-xl bg-[#d9930a] px-6 text-[8px] font-bold text-white transition hover:bg-[#c98508]"
                        >
                            Assign {{ $orderCount }}
                            {{ $orderCount === 1 ? 'Order' : 'Orders' }}
                        </button>

                    </div>

                </form>

            </article>

        @empty

            <div
                class="rounded-[18px] border border-dashed border-[#ded5c9] bg-white p-10 text-center"
            >
                <p
                    class="text-[10px] font-semibold text-[#5f574e]"
                >
                    No pickup groups waiting for assignment.
                </p>

                <p
                    class="mt-1 text-[8px] text-[#8c8275]"
                >
                    Verified Seller pickup requests will appear here.
                </p>
            </div>

        @endforelse

    </div>

</section>
@endsection