@extends('layouts.courier')

@section('title', 'Last Mile Deliveries')
@section('header-title', 'Last Mile Deliveries')
@section('header-subtitle', 'Destination Hub → Buyer')

@section('content')

<div class="mx-auto max-w-[1620px] space-y-4">

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-[9px] font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-[9px] font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="rounded-[20px] border border-[#eadfc9] bg-[#fffdf8] p-5 sm:p-6">

        <span class="rounded-full border border-[#eadfc9] bg-white px-3 py-1.5 text-[8px] font-semibold uppercase tracking-[.13em] text-[#8a7f70]">
            Last-Mile Operations
        </span>

        <h2 class="mt-3 text-[20px] font-bold text-[#211d17]">
            Destination Hub Deliveries
        </h2>

        <p class="mt-2 text-[9px] leading-5 text-[#817769]">
            Pick up assigned parcels from the destination hub and deliver
            them within your assigned barangay.
        </p>

    </section>

    @forelse($assignments as $assignment)

        <article class="overflow-hidden rounded-[20px] border border-[#eadfc9] bg-[#fffdf8]">

            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-[#eadfc9] p-5">

                <div>
                    <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#b97805]">
                        {{ $assignment->assignment_code }}
                    </p>

                    <h3 class="mt-2 text-[15px] font-bold text-[#211d17]">
                        {{ $assignment->facility_name }}
                        →
                        {{ $assignment->delivery_area }}
                    </h3>

                    <p class="mt-1 text-[8px] text-[#817769]">
                        {{ $assignment->orders->count() }}
                        {{ $assignment->orders->count() === 1 ? 'parcel' : 'parcels' }}
                    </p>
                </div>

                <span class="rounded-full bg-[#fff2d8] px-3 py-1.5 text-[8px] font-bold uppercase text-[#a56b00]">
                    {{ str_replace('_', ' ', $assignment->status) }}
                </span>

            </div>

            @if($assignment->status === 'assigned')

                <div class="p-5">

                    <div class="rounded-xl border border-[#eee5d6] bg-white p-4">

                        <p class="text-[8px] font-semibold text-[#817769]">
                            Pickup location
                        </p>

                        <p class="mt-1 text-[10px] font-bold text-[#29241e]">
                            {{ $assignment->facility_name }}
                        </p>

                        <p class="mt-1 text-[8px] text-[#918677]">
                            {{ $assignment->facility_address }}
                        </p>

                    </div>

                    <form
                        method="POST"
                        action="{{ route('courier.last-mile.start', $assignment->id) }}"
                        class="mt-4"
                    >
                        @csrf

                        <button
                            class="h-11 w-full rounded-xl bg-[#D99600] px-6 text-[9px] font-bold text-white"
                        >
                            Start Delivery Route
                        </button>
                    </form>

                </div>

            @endif

            <div class="space-y-3 p-5">

                @foreach($assignment->orders as $order)

                    <div class="rounded-[16px] border border-[#eee5d6] bg-white p-4">

                        <div class="flex flex-wrap items-start justify-between gap-3">

                            <div>
                                <p class="text-[10px] font-bold text-[#29241e]">
                                    {{ $order->order_number }}
                                </p>

                                <p class="mt-1 text-[9px] font-semibold text-[#51483d]">
                                    {{ $order->buyer_name }}
                                </p>

                                <p class="mt-1 text-[8px] leading-4 text-[#817769]">
                                    {{ $order->buyer_address }}
                                </p>

                                @if($order->buyer_phone)
                                    <p class="mt-1 text-[8px] text-[#817769]">
                                        {{ $order->buyer_phone }}
                                    </p>
                                @endif
                            </div>

                            <span class="rounded-full bg-[#f6f1e8] px-3 py-1.5 text-[7px] font-bold uppercase text-[#6f6559]">
                                {{ str_replace('_', ' ', $order->parcel_status ?? $order->order_status) }}
                            </span>

                        </div>

                        @if(
                            $assignment->status === 'out_for_delivery'
                            && $order->parcel_status === 'out_for_delivery'
                        )

                            <form
                                method="POST"
                                action="{{ route(
                                    'courier.last-mile.arrived',
                                    [
                                        'assignment' => $assignment->id,
                                        'order' => $order->id,
                                    ]
                                ) }}"
                                class="mt-4"
                            >
                                @csrf

                                <button
                                    class="h-10 w-full rounded-xl bg-[#3C6E91] px-5 text-[8px] font-bold text-white"
                                >
                                    Confirm Arrived at Buyer
                                </button>
                            </form>

                        @elseif(
                            $assignment->status === 'out_for_delivery'
                            && $order->parcel_status === 'arrived_buyer'
                        )

                            <form
                                method="POST"
                                action="{{ route(
                                    'courier.last-mile.delivered',
                                    [
                                        'assignment' => $assignment->id,
                                        'order' => $order->id,
                                    ]
                                ) }}"
                                class="mt-4"
                            >
                                @csrf

                                <button
                                    class="h-10 w-full rounded-xl bg-emerald-600 px-5 text-[8px] font-bold text-white"
                                >
                                    Mark Delivered
                                </button>
                            </form>

                        @elseif($order->parcel_status === 'delivered')

                            <div class="mt-4 rounded-xl bg-emerald-50 px-4 py-3 text-center text-[8px] font-bold text-emerald-700">
                                Delivered
                            </div>

                        @endif

                    </div>

                @endforeach

            </div>

        </article>

    @empty

        <div class="rounded-[20px] border border-dashed border-[#dcd2c3] bg-[#fffdf8] p-12 text-center">

            <p class="text-[10px] font-semibold text-[#51483d]">
                No last-mile assignments.
            </p>

            <p class="mt-2 text-[8px] text-[#918677]">
                Destination-hub assignments from Logistics will appear here.
            </p>

        </div>

    @endforelse

</div>

@endsection