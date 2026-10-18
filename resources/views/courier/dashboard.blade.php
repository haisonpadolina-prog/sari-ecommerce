@extends('layouts.courier')

@section('title', 'Rider Dashboard')
@section('header-title', 'Rider Dashboard')
@section('header-subtitle', 'Operations Overview')

@section('content')

@php
    $hasMarketplaceWork =
        $currentDelivery
        instanceof
        \App\Models\Orders\MarketplaceOrder;

    $isLogisticsPickup =
        $hasMarketplaceWork
        && (bool) $currentDelivery->logisticsParcel;

    $lastMileJobs =
        (int) ($stats['last_mile_waiting'] ?? 0)
        + (int) ($stats['last_mile_active'] ?? 0);

    $riderName =
        trim(
            (string) ($rider->first_name ?? '')
            . ' '
            . (string) ($rider->last_name ?? '')
        );

    $riderName =
        $riderName !== ''
            ? $riderName
            : 'SARI Rider';
@endphp

<div class="mx-auto max-w-[1620px] space-y-5">

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-[10px] font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-[10px] font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="overflow-hidden rounded-[22px] border border-[#eadfc9] bg-[#fffdf8]">
        <div class="grid gap-5 p-5 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center lg:p-6">

            <div class="min-w-0">

                <div class="flex flex-wrap items-center gap-2">

                    <span class="inline-flex rounded-full border border-[#eadfc9] bg-white px-3 py-1.5 text-[8px] font-semibold uppercase tracking-[.13em] text-[#8a7f70]">
                        Rider Operations
                    </span>

                    <span class="inline-flex rounded-full bg-[#eef6f1] px-3 py-1.5 text-[8px] font-semibold text-[#4F7D63]">
                        {{ ucfirst((string) ($rider->availability_status ?? 'offline')) }}
                    </span>

                </div>

                <h2 class="mt-4 text-[22px] font-bold tracking-[-.035em] text-[#211d17]">
                    Welcome back, {{ $riderName }}.
                </h2>

                <p class="mt-2 max-w-[760px] text-[9px] leading-5 text-[#817769]">
                    Review assigned pickup work, active operations,
                    last-mile batches, and today's earnings.
                    Workflow actions are handled inside each dedicated job page.
                </p>

            </div>


            <div class="flex flex-wrap gap-2">

                <a
                    href="{{ route('courier.requests') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-[#e5dac9] bg-white px-4 py-2.5 text-[9px] font-semibold text-[#665c4f] transition hover:border-[#d7bd8b] hover:text-[#a96e05]"
                >
                    Pickup Assignments
                </a>

                <a
                    href="{{ route('courier.last-mile') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-[#D99600] px-4 py-2.5 text-[9px] font-semibold text-white shadow-[0_8px_20px_rgba(217,150,0,.14)]"
                >
                    Last-Mile Deliveries →
                </a>

            </div>

        </div>
    </section>


    <section class="grid grid-cols-2 gap-3 xl:grid-cols-4">

        @foreach([
            [
                'Pickup Queue',
                (int) ($stats['pickup_waiting'] ?? 0),
                'Jobs waiting to start',
                'bg-[#fff2d8]',
                'text-[#b97805]',
                'pickup',
            ],
            [
                'Active Work',
                (int) ($stats['active_deliveries'] ?? 0),
                'Jobs currently moving',
                'bg-[#eaf2f7]',
                'text-[#3C6E91]',
                'active',
            ],
            [
                'Last-Mile Jobs',
                $lastMileJobs,
                'Assigned destination work',
                'bg-[#eef4ef]',
                'text-[#4F7D63]',
                'lastmile',
            ],
            [
                "Today's Earnings",
                '₱'.number_format(
                    (float) ($stats['earnings_today'] ?? 0),
                    2
                ),
                (int) ($stats['completed_today'] ?? 0).' completed today',
                'bg-[#f3eef7]',
                'text-[#715A86]',
                'earnings',
            ],
        ] as [$label, $value, $note, $iconBg, $iconText, $icon])

            <article class="rounded-[18px] border border-[#eadfc9] bg-white p-4 shadow-[0_7px_18px_rgba(33,24,14,.025)]">

                <div class="flex items-start justify-between gap-3">

                    <div class="min-w-0">

                        <p class="text-[8px] font-semibold uppercase tracking-[.1em] text-[#9a8f80]">
                            {{ $label }}
                        </p>

                        <p class="mt-2 text-[23px] font-bold tracking-[-.045em] text-[#211d17]">
                            {{ $value }}
                        </p>

                        <p class="mt-2 truncate text-[8px] text-[#918677]">
                            {{ $note }}
                        </p>

                    </div>


                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-[13px] {{ $iconBg }} {{ $iconText }}">

                        @if($icon === 'pickup')
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M5 7h14v12H5z"></path>
                                <path d="M8 7V5h8v2"></path>
                                <path d="M8 12h8"></path>
                            </svg>

                        @elseif($icon === 'active')
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 7h11v10H3z"></path>
                                <path d="M14 10h4l3 3v4h-7z"></path>
                                <circle cx="7" cy="18" r="2"></circle>
                                <circle cx="18" cy="18" r="2"></circle>
                            </svg>

                        @elseif($icon === 'lastmile')
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"></path>
                                <circle cx="12" cy="10" r="2"></circle>
                            </svg>

                        @else
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 3v18"></path>
                                <path d="M17 7.5c0-1.8-1.8-3-5-3s-5 1.2-5 3 1.5 2.6 5 3.5 5 1.7 5 3.5-1.8 3-5 3-5-1.2-5-3"></path>
                            </svg>
                        @endif

                    </span>

                </div>

            </article>

        @endforeach

    </section>


    <section class="grid gap-4 xl:grid-cols-2">

        <article class="overflow-hidden rounded-[20px] border border-[#eadfc9] bg-[#fffdf8]">

            <div class="flex items-center justify-between gap-3 border-b border-[#eadfc9] px-5 py-4">

                <div>
                    <h3 class="text-[12px] font-bold text-[#211d17]">
                        Current Pickup / Direct Job
                    </h3>

                    <p class="mt-1 text-[8px] text-[#918677]">
                        Workflow controls open on the dedicated job screen.
                    </p>
                </div>

                <a
                    href="{{ route('courier.requests') }}"
                    class="text-[8px] font-semibold text-[#a96e05]"
                >
                    View queue →
                </a>

            </div>


            @if($hasMarketplaceWork)

                <div class="p-5">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                        <div class="min-w-0">

                            <div class="flex flex-wrap items-center gap-2">

                                <h4 class="text-[14px] font-bold text-[#201c16]">
                                    {{ $currentDelivery->order_number }}
                                </h4>

                                <span class="rounded-full border border-[#d2dfe9] bg-[#f2f7fb] px-2.5 py-1 text-[7px] font-bold uppercase text-[#3C6E91]">
                                    {{ $currentDelivery->statusLabel() }}
                                </span>

                                <span class="rounded-full border border-[#eadfc9] bg-white px-2.5 py-1 text-[7px] font-semibold uppercase text-[#7d7264]">
                                    {{
                                        $isLogisticsPickup
                                            ? 'Seller Pickup'
                                            : 'Direct Delivery'
                                    }}
                                </span>

                            </div>


                            <p class="mt-3 text-[9px] font-semibold text-[#4c443a]">
                                {{
                                    $currentDelivery->pickup_name
                                    ?: $currentDelivery->seller?->store_name
                                    ?: 'Seller'
                                }}
                                →
                                {{
                                    $isLogisticsPickup
                                        ? 'Origin Hub'
                                        : (
                                            $currentDelivery->buyer_name
                                            ?: 'Buyer'
                                        )
                                }}
                            </p>


                            <p class="mt-2 max-w-[620px] text-[8px] leading-4 text-[#867b6d]">

                                @if($isLogisticsPickup)
                                    Collect the parcel from the Seller
                                    and hand it over to the assigned origin hub.
                                    Final Buyer delivery belongs to the
                                    destination last-mile Rider.
                                @else
                                    Continue the assigned direct-delivery
                                    workflow until Buyer handoff is complete.
                                @endif

                            </p>

                        </div>


                        <div class="shrink-0 rounded-xl border border-[#eadfc9] bg-white px-4 py-3">

                            <p class="text-[7px] uppercase tracking-[.09em] text-[#9b8e7d]">
                                Current Stage
                            </p>

                            <p class="mt-1 text-[9px] font-bold text-[#29241e]">
                                {{ $currentDelivery->statusLabel() }}
                            </p>

                        </div>

                    </div>


                    <a
                        href="{{
                            route(
                                'courier.delivery.show',
                                [
                                    'order' => $currentDelivery->id,
                                ]
                            )
                        }}"
                        class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-[#3C6E91] px-5 py-3 text-[9px] font-semibold text-white shadow-[0_8px_20px_rgba(60,110,145,.12)]"
                    >
                        Continue Dedicated Workflow →
                    </a>

                </div>

            @else

                <div class="p-9 text-center">

                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#f6f1e8] text-[#a87b2a]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M4 7h16v11H4z"></path>
                            <path d="M8 7V5h8v2"></path>
                        </svg>
                    </div>

                    <p class="mt-4 text-[10px] font-semibold text-[#51483d]">
                        No active pickup or direct-delivery job.
                    </p>

                    <p class="mx-auto mt-2 max-w-[420px] text-[8px] leading-4 text-[#918677]">
                        Assigned work will appear here after
                        SARI Logistics dispatches it to you.
                    </p>

                    <a
                        href="{{ route('courier.requests') }}"
                        class="mt-4 inline-flex rounded-xl border border-[#eadfc9] bg-white px-5 py-3 text-[9px] font-semibold text-[#6e6254]"
                    >
                        Open Pickup Assignments
                    </a>

                </div>

            @endif

        </article>


        <article class="overflow-hidden rounded-[20px] border border-[#eadfc9] bg-[#fffdf8]">

            <div class="flex items-center justify-between gap-3 border-b border-[#eadfc9] px-5 py-4">

                <div>
                    <h3 class="text-[12px] font-bold text-[#211d17]">
                        Current Last-Mile Batch
                    </h3>

                    <p class="mt-1 text-[8px] text-[#918677]">
                        Destination hub → assigned barangay → Buyer.
                    </p>
                </div>

                <a
                    href="{{ route('courier.last-mile') }}"
                    class="text-[8px] font-semibold text-[#a96e05]"
                >
                    Open last mile →
                </a>

            </div>


            @if($currentLastMile)

                <div class="p-5">

                    <div class="flex flex-wrap items-start justify-between gap-4">

                        <div class="min-w-0">

                            <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#b97805]">
                                {{ $currentLastMile->assignment_code }}
                            </p>

                            <h4 class="mt-2 text-[14px] font-bold text-[#201c16]">
                                {{ $currentLastMile->facility_name }}
                                →
                                {{ $currentLastMile->delivery_area }}
                            </h4>

                            <p class="mt-2 text-[8px] text-[#817769]">
                                {{ $currentLastMile->parcel_count }}
                                {{
                                    $currentLastMile->parcel_count === 1
                                        ? 'parcel'
                                        : 'parcels'
                                }}
                            </p>

                        </div>


                        <span class="rounded-full border border-[#eadfc9] bg-[#fff6e1] px-3 py-1.5 text-[7px] font-bold uppercase text-[#a56b00]">
                            {{
                                str_replace(
                                    '_',
                                    ' ',
                                    $currentLastMile->status
                                )
                            }}
                        </span>

                    </div>


                    <div class="mt-4 grid gap-3 sm:grid-cols-2">

                        <div class="rounded-xl border border-[#eee5d6] bg-white p-3">

                            <p class="text-[7px] uppercase tracking-[.1em] text-[#a09484]">
                                Potential Delivery Fee
                            </p>

                            <p class="mt-1 text-[12px] font-bold text-[#29241e]">
                                ₱{{
                                    number_format(
                                        (float)
                                        $currentLastMile
                                            ->delivery_fee_total,
                                        2
                                    )
                                }}
                            </p>

                        </div>


                        <div class="rounded-xl border border-[#eee5d6] bg-white p-3">

                            <p class="text-[7px] uppercase tracking-[.1em] text-[#a09484]">
                                Destination Hub
                            </p>

                            <p class="mt-1 text-[9px] font-semibold text-[#29241e]">
                                {{ $currentLastMile->facility_name }}
                            </p>

                        </div>

                    </div>


                    <a
                        href="{{ route('courier.last-mile') }}"
                        class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-[#D99600] px-5 py-3 text-[9px] font-semibold text-white"
                    >
                        Continue Last-Mile Workflow →
                    </a>

                </div>

            @else

                <div class="p-9 text-center">

                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#eef4ef] text-[#4F7D63]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"></path>
                            <circle cx="12" cy="10" r="2"></circle>
                        </svg>
                    </div>

                    <p class="mt-4 text-[10px] font-semibold text-[#51483d]">
                        No active last-mile batch.
                    </p>

                    <p class="mx-auto mt-2 max-w-[420px] text-[8px] leading-4 text-[#918677]">
                        Destination-hub assignments appear here
                        when Logistics dispatches a local batch to you.
                    </p>

                    <a
                        href="{{ route('courier.last-mile') }}"
                        class="mt-4 inline-flex rounded-xl border border-[#eadfc9] bg-white px-5 py-3 text-[9px] font-semibold text-[#6e6254]"
                    >
                        Open Last-Mile Deliveries
                    </a>

                </div>

            @endif

        </article>

    </section>


    <section class="grid gap-4 xl:grid-cols-[minmax(0,1.3fr)_minmax(320px,.7fr)]">

        <article class="rounded-[20px] border border-[#eadfc9] bg-[#fffdf8] p-5">

            <div class="flex items-start justify-between gap-3">

                <div>
                    <h3 class="text-[12px] font-bold text-[#211d17]">
                        Up Next
                    </h3>

                    <p class="mt-1 text-[8px] text-[#918677]">
                        Latest assignments waiting for you to start.
                    </p>
                </div>

                <a
                    href="{{ route('courier.requests') }}"
                    class="text-[8px] font-semibold text-[#a96e05]"
                >
                    View all →
                </a>

            </div>


            <div class="mt-4 space-y-2">

                @forelse($assignments->take(4) as $order)

                    <a
                        href="{{
                            route(
                                'courier.delivery.show',
                                [
                                    'order' => $order->id,
                                ]
                            )
                        }}"
                        class="flex items-center justify-between gap-3 rounded-xl border border-[#eadfc9] bg-white p-3 transition hover:border-[#d8c39e]"
                    >

                        <div class="min-w-0">

                            <p class="text-[9px] font-bold text-[#2a251f]">
                                {{ $order->order_number }}
                            </p>

                            <p class="mt-1 truncate text-[7px] text-[#84796c]">
                                {{
                                    $order->pickup_name
                                    ?: $order->seller?->store_name
                                    ?: 'Seller'
                                }}
                                →
                                {{
                                    $order->logisticsParcel
                                        ? 'Origin Hub'
                                        : (
                                            $order->buyer_name
                                            ?: 'Buyer'
                                        )
                                }}
                            </p>

                        </div>


                        <span class="shrink-0 rounded-full bg-[#fff2d8] px-2.5 py-1 text-[7px] font-bold text-[#aa6d00]">
                            OPEN →
                        </span>

                    </a>

                @empty

                    <div class="rounded-xl border border-dashed border-[#ddd2c1] bg-white p-6 text-center text-[8px] text-[#918677]">
                        No pickup assignments waiting.
                    </div>

                @endforelse

            </div>

        </article>


        <article class="rounded-[20px] border border-[#eadfc9] bg-[#fffdf8] p-5">

            <div>
                <h3 class="text-[12px] font-bold text-[#211d17]">
                    Today at a Glance
                </h3>

                <p class="mt-1 text-[8px] text-[#918677]">
                    Your current operational snapshot.
                </p>
            </div>


            <div class="mt-4 space-y-3">

                <div class="flex items-center justify-between rounded-xl border border-[#eadfc9] bg-white px-4 py-3">
                    <span class="text-[8px] text-[#817769]">
                        Completed jobs
                    </span>

                    <strong class="text-[11px] text-[#29241e]">
                        {{ (int) ($stats['completed_today'] ?? 0) }}
                    </strong>
                </div>


                <div class="flex items-center justify-between rounded-xl border border-[#eadfc9] bg-white px-4 py-3">
                    <span class="text-[8px] text-[#817769]">
                        Pickup active
                    </span>

                    <strong class="text-[11px] text-[#29241e]">
                        {{ (int) ($stats['pickup_active'] ?? 0) }}
                    </strong>
                </div>


                <div class="flex items-center justify-between rounded-xl border border-[#eadfc9] bg-white px-4 py-3">
                    <span class="text-[8px] text-[#817769]">
                        Last-mile active
                    </span>

                    <strong class="text-[11px] text-[#29241e]">
                        {{ (int) ($stats['last_mile_active'] ?? 0) }}
                    </strong>
                </div>


                <div class="flex items-center justify-between rounded-xl border border-[#e3d9eb] bg-[#faf7fc] px-4 py-3">
                    <span class="text-[8px] text-[#756783]">
                        Earned today
                    </span>

                    <strong class="text-[11px] text-[#715A86]">
                        ₱{{
                            number_format(
                                (float) ($stats['earnings_today'] ?? 0),
                                2
                            )
                        }}
                    </strong>
                </div>

            </div>


            <a
                href="{{ route('courier.earnings') }}"
                class="mt-4 inline-flex w-full items-center justify-center rounded-xl border border-[#e4d8eb] bg-white px-4 py-3 text-[9px] font-semibold text-[#715A86]"
            >
                Open Earnings
            </a>

        </article>

    </section>

</div>

@endsection