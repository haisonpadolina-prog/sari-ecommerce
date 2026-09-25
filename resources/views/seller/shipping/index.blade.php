@extends('layouts.seller')

@section('title', 'Shipping — SARI Seller')
@section('page-title', 'Shipping')

@section('content')
@php
    $toneClasses = [
        'success' => 'border-[#cfe4d7] bg-[#f1f8f4] text-[#4f7d63]',
        'info' => 'border-[#cfdeea] bg-[#f1f7fb] text-[#3c6e91]',
        'hub' => 'border-[#d8d8ea] bg-[#f5f5fb] text-[#63638d]',
        'warning' => 'border-[#eadfc9] bg-[#fff9ef] text-[#a8731f]',
    ];

    $tabs = [
        'all' => ['All Shipments', array_sum([
            $stats['awaiting_pickup'],
            $stats['in_transit'],
            $stats['delivered'],
        ])],
        'awaiting_pickup' => ['Awaiting Pickup', $stats['awaiting_pickup']],
        'in_transit' => ['In Transit', $stats['in_transit']],
        'delivered' => ['Delivered', $stats['delivered']],
    ];
@endphp

<div class="mx-auto w-full max-w-[1800px] font-['Poppins',sans-serif]">
    <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-[9px] font-bold uppercase tracking-[.16em] text-[#b97805]">
                Fulfillment & Logistics
            </p>
            <h1 class="mt-2 text-[32px] font-semibold tracking-[-.045em] text-[#202124] sm:text-[38px]">
                Shipping Tracking
            </h1>
            <p class="mt-2 max-w-[720px] text-[10px] leading-5 text-[#7b8089] sm:text-[11px]">
                Follow Seller parcels from pickup verification to Logistics hub intake,
                sorting, final delivery, and completion.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a
                href="{{ route('seller.orders') }}"
                class="inline-flex h-11 items-center justify-center rounded-xl border border-[#e1e4e8] bg-white px-4 text-[10px] font-semibold text-[#4b5563] transition hover:border-[#d5b16a] hover:text-[#a96f06]"
            >
                ← Order Management
            </a>

            <a
                href="{{ route('seller.shipping.index', request()->query()) }}"
                class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-[#202124] px-4 text-[10px] font-semibold text-white transition hover:bg-[#111214]"
            >
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M20 12a8 8 0 1 1-2.34-5.66"></path>
                    <path d="M20 4v6h-6"></path>
                </svg>
                Refresh
            </a>
        </div>
    </section>

    <section class="mt-6 grid grid-cols-2 gap-3 xl:grid-cols-4">
        @foreach([
            ['Active Shipments', $stats['active'], 'Truck in fulfillment', 'text-[#b97805]', 'bg-[#fff7e6]'],
            ['Awaiting Pickup', $stats['awaiting_pickup'], 'Seller / Rider handoff', 'text-[#a8731f]', 'bg-[#fff9ef]'],
            ['In Transit', $stats['in_transit'], 'Hub and final-mile movement', 'text-[#3c6e91]', 'bg-[#f1f7fb]'],
            ['Delivered', $stats['delivered'], 'Completed shipments', 'text-[#4f7d63]', 'bg-[#f1f8f4]'],
        ] as [$label, $value, $caption, $textTone, $iconTone])
            <article class="rounded-[18px] border border-[#e7e9ee] bg-white p-4 shadow-[0_9px_24px_rgba(32,33,36,.035)] sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[9px] font-semibold text-[#7b8089]">{{ $label }}</p>
                        <p class="mt-2 text-[25px] font-semibold tracking-[-.04em] text-[#202124]">
                            {{ number_format($value) }}
                        </p>
                        <p class="mt-2 text-[8px] leading-4 text-[#9aa0a6]">{{ $caption }}</p>
                    </div>

                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-[12px] {{ $iconTone }} {{ $textTone }}">
                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 7h11v10H3z"></path>
                            <path d="M14 10h3l4 4v3h-7z"></path>
                            <circle cx="7" cy="18" r="1.5"></circle>
                            <circle cx="18" cy="18" r="1.5"></circle>
                        </svg>
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="mt-5 overflow-hidden rounded-[20px] border border-[#e7e9ee] bg-white shadow-[0_10px_30px_rgba(32,33,36,.035)]">
        <div class="border-b border-[#eceef1] p-4 sm:p-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="flex flex-wrap gap-2">
                    @foreach($tabs as $tabKey => [$tabLabel, $tabCount])
                        <a
                            href="{{ route('seller.shipping.index', array_filter(['status' => $tabKey, 'q' => $search])) }}"
                            class="
                                inline-flex h-9 items-center gap-2 rounded-xl px-3.5 text-[9px] font-semibold transition
                                {{ $filter === $tabKey
                                    ? 'bg-[#202124] text-white'
                                    : 'border border-[#e4e7eb] bg-[#fafafb] text-[#6b7280] hover:border-[#d7b76f] hover:text-[#a96f06]' }}
                            "
                        >
                            {{ $tabLabel }}
                            <span class="{{ $filter === $tabKey ? 'text-white/65' : 'text-[#a2a7ae]' }}">
                                {{ number_format($tabCount) }}
                            </span>
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('seller.shipping.index') }}" class="flex w-full gap-2 xl:w-auto">
                    <input type="hidden" name="status" value="{{ $filter }}">

                    <div class="relative min-w-0 flex-1 xl:w-[330px]">
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9aa0a6]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg>

                        <input
                            type="search"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Order, Buyer, Rider..."
                            class="h-11 w-full rounded-xl border border-[#e1e4e8] bg-[#fafafb] pl-10 pr-4 text-[10px] text-[#374151] outline-none transition placeholder:text-[#a2a7ae] focus:border-[#d7b76f] focus:bg-white"
                        >
                    </div>

                    <button
                        class="h-11 rounded-xl bg-[#d89b10] px-4 text-[9px] font-semibold text-white transition hover:bg-[#b67a08]"
                    >
                        Search
                    </button>
                </form>
            </div>
        </div>

        <div class="space-y-3 bg-[#f7f8fa] p-3 sm:p-4">
            @forelse($orders as $order)
                @php
                    $state = $shippingStates[$order->id];
                    $tone = $toneClasses[$state['tone']] ?? $toneClasses['warning'];
                    $parcel = $order->logisticsParcel;
                    $itemCount = collect($order->items ?? [])->sum(fn ($item) => (int) ($item['qty'] ?? 1));
                @endphp

                <article class="rounded-[18px] border border-[#e5e8ec] bg-white p-4 transition duration-150 hover:-translate-y-[1px] hover:border-[#ddd1bd] hover:shadow-[0_12px_26px_rgba(32,33,36,.045)] sm:p-5">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <a
                                    href="{{ route('seller.shipping.show', $order) }}"
                                    class="text-[13px] font-bold tracking-[-.02em] text-[#202124] hover:text-[#a96f06]"
                                >
                                    {{ $order->order_number }}
                                </a>

                                <span class="inline-flex rounded-full border px-2.5 py-1 text-[7px] font-bold uppercase tracking-[.08em] {{ $tone }}">
                                    {{ $state['label'] }}
                                </span>
                            </div>

                            <p class="mt-2 max-w-[760px] text-[9px] leading-5 text-[#7b8089]">
                                {{ $state['detail'] }}
                            </p>

                            <div class="mt-4">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-[8px] font-semibold text-[#6b7280]">Shipment progress</span>
                                    <span class="text-[8px] font-bold text-[#b97805]">{{ $state['progress'] }}%</span>
                                </div>

                                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#eceff2]">
                                    <div
                                        class="h-full rounded-full bg-[#d89b10] transition-all duration-300"
                                        style="width: {{ $state['progress'] }}%"
                                    ></div>
                                </div>

                                <div class="mt-2 grid grid-cols-5 gap-1 text-center text-[7px] text-[#9aa0a6]">
                                    <span>Ready</span>
                                    <span>Pickup</span>
                                    <span>Hub</span>
                                    <span>Final Mile</span>
                                    <span>Delivered</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid min-w-0 gap-3 sm:grid-cols-3 xl:w-[560px]">
                            <div class="rounded-[14px] border border-[#eceef1] bg-[#fafafb] p-3.5">
                                <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Buyer</p>
                                <p class="mt-1.5 truncate text-[9px] font-semibold text-[#374151]">
                                    {{ $order->buyer_name ?: 'SARI Buyer' }}
                                </p>
                                <p class="mt-1 text-[8px] text-[#8a919b]">
                                    {{ $itemCount }} item{{ $itemCount === 1 ? '' : 's' }}
                                </p>
                            </div>

                            <div class="rounded-[14px] border border-[#eceef1] bg-[#fafafb] p-3.5">
                                <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Assigned Rider</p>
                                <p class="mt-1.5 truncate text-[9px] font-semibold text-[#374151]">
                                    {{ $order->courier_name ?: 'Not assigned yet' }}
                                </p>
                                <p class="mt-1 truncate text-[8px] text-[#8a919b]">
                                    {{ $order->courier_email ?: 'Waiting for Logistics' }}
                                </p>
                            </div>

                            <div class="rounded-[14px] border border-[#eceef1] bg-[#fafafb] p-3.5">
                                <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Logistics Hub</p>
                                <p class="mt-1.5 truncate text-[9px] font-semibold text-[#374151]">
                                    {{ $parcel ? ucwords(str_replace('_', ' ', $parcel->status)) : 'Not received yet' }}
                                </p>
                                <p class="mt-1 truncate text-[8px] text-[#8a919b]">
                                    {{ $parcel?->sorting_zone ?: 'No sorting zone yet' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col gap-3 border-t border-[#f0f1f3] pt-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[8px] text-[#8a919b]">
                            <span>
                                Updated
                                <strong class="font-semibold text-[#606771]">{{ $order->updated_at?->diffForHumans() }}</strong>
                            </span>

                            @if($order->picked_up_at)
                                <span>
                                    Picked up
                                    <strong class="font-semibold text-[#606771]">{{ $order->picked_up_at->format('M d, h:i A') }}</strong>
                                </span>
                            @endif

                            @if($order->delivered_at)
                                <span>
                                    Delivered
                                    <strong class="font-semibold text-[#606771]">{{ $order->delivered_at->format('M d, h:i A') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @if(Route::has('seller.orders.waybill'))
                                <a
                                    href="{{ route('seller.orders.waybill', $order) }}"
                                    class="inline-flex h-9 items-center justify-center rounded-xl border border-[#e2e5e9] bg-white px-3 text-[8px] font-semibold text-[#6b7280] hover:border-[#d5b16a] hover:text-[#a96f06]"
                                >
                                    Waybill
                                </a>
                            @endif

                            <a
                                href="{{ route('seller.shipping.show', $order) }}"
                                class="inline-flex h-9 items-center justify-center gap-2 rounded-xl bg-[#202124] px-3.5 text-[8px] font-semibold text-white hover:bg-[#111214]"
                            >
                                Track Shipment
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[18px] border border-dashed border-[#dfe3e8] bg-white px-5 py-14 text-center">
                    <span class="mx-auto grid h-12 w-12 place-items-center rounded-[14px] bg-[#fff7e6] text-[#b97805]">
                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 7h11v10H3z"></path>
                            <path d="M14 10h3l4 4v3h-7z"></path>
                            <circle cx="7" cy="18" r="1.5"></circle>
                            <circle cx="18" cy="18" r="1.5"></circle>
                        </svg>
                    </span>

                    <h3 class="mt-4 text-[12px] font-semibold text-[#374151]">No shipments found</h3>
                    <p class="mt-2 text-[9px] leading-5 text-[#8a919b]">
                        Orders appear here after the Seller marks them ready for pickup.
                    </p>
                </div>
            @endforelse
        </div>

        @if($orders->hasPages())
            <div class="border-t border-[#eceef1] bg-white px-4 py-4 sm:px-5">
                {{ $orders->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
