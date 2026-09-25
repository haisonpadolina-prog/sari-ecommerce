@extends('layouts.seller')

@section('title', 'Track ' . $order->order_number . ' — SARI Seller')
@section('page-title', 'Shipping Tracking')

@section('content')
@php
    $parcel = $order->logisticsParcel;

    $toneClasses = [
        'success' => 'border-[#cfe4d7] bg-[#f1f8f4] text-[#4f7d63]',
        'info' => 'border-[#cfdeea] bg-[#f1f7fb] text-[#3c6e91]',
        'hub' => 'border-[#d8d8ea] bg-[#f5f5fb] text-[#63638d]',
        'warning' => 'border-[#eadfc9] bg-[#fff9ef] text-[#a8731f]',
    ];

    $tone = $toneClasses[$shippingState['tone']] ?? $toneClasses['warning'];

    $kindStyle = fn (string $kind) => match ($kind) {
        'success' => ['bg-[#4f7d63]', 'text-[#4f7d63]'],
        'logistics' => ['bg-[#63638d]', 'text-[#63638d]'],
        'rider' => ['bg-[#3c6e91]', 'text-[#3c6e91]'],
        'seller' => ['bg-[#d89b10]', 'text-[#b97805]'],
        default => ['bg-[#8a919b]', 'text-[#6b7280]'],
    };

    $items = collect($order->items ?? []);
@endphp

<div class="mx-auto w-full max-w-[1500px] font-['Poppins',sans-serif]">
    <section class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <a
                href="{{ route('seller.shipping.index') }}"
                class="inline-flex items-center gap-2 text-[9px] font-semibold text-[#7b8089] hover:text-[#a96f06]"
            >
                ← Back to Shipping
            </a>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                <h1 class="text-[30px] font-semibold tracking-[-.04em] text-[#202124] sm:text-[36px]">
                    {{ $order->order_number }}
                </h1>

                <span class="inline-flex rounded-full border px-3 py-1.5 text-[8px] font-bold uppercase tracking-[.08em] {{ $tone }}">
                    {{ $shippingState['label'] }}
                </span>
            </div>

            <p class="mt-2 max-w-[760px] text-[10px] leading-5 text-[#7b8089]">
                {{ $shippingState['detail'] }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span
                id="sellerShippingLiveIndicator"
                class="inline-flex h-10 items-center gap-2 rounded-xl border border-[#d8e7dd] bg-[#f5faf7] px-3.5 text-[8px] font-semibold text-[#4f7d63]"
            >
                <span class="h-1.5 w-1.5 rounded-full bg-[#4f7d63]"></span>
                Live tracking
            </span>

            @if(Route::has('seller.orders.waybill'))
                <a
                    href="{{ route('seller.orders.waybill', $order) }}"
                    class="inline-flex h-10 items-center rounded-xl border border-[#e1e4e8] bg-white px-3.5 text-[8px] font-semibold text-[#606771] hover:border-[#d5b16a] hover:text-[#a96f06]"
                >
                    View Waybill
                </a>
            @endif
        </div>
    </section>

    <section class="mt-6 rounded-[20px] border border-[#e7e9ee] bg-white p-5 shadow-[0_10px_30px_rgba(32,33,36,.035)] sm:p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-[9px] font-semibold text-[#6b7280]">Overall shipment progress</p>
                <p class="mt-1 text-[8px] text-[#9aa0a6]">
                    Seller → Rider → Logistics Hub → Buyer
                </p>
            </div>

            <span class="text-[14px] font-bold text-[#b97805]">{{ $shippingState['progress'] }}%</span>
        </div>

        <div class="mt-4 h-2 overflow-hidden rounded-full bg-[#eceff2]">
            <div
                class="h-full rounded-full bg-[#d89b10]"
                style="width: {{ $shippingState['progress'] }}%"
            ></div>
        </div>

        <div class="mt-4 grid grid-cols-5 gap-2">
            @foreach([
                ['Ready', 10],
                ['Rider Pickup', 35],
                ['Logistics Hub', 65],
                ['Final Mile', 85],
                ['Delivered', 100],
            ] as [$label, $threshold])
                @php $done = $shippingState['progress'] >= $threshold; @endphp
                <div>
                    <span class="block h-1.5 rounded-full {{ $done ? 'bg-[#d89b10]' : 'bg-[#eceff2]' }}"></span>
                    <p class="mt-2 text-center text-[7px] font-semibold {{ $done ? 'text-[#7a5a18]' : 'text-[#a2a7ae]' }}">
                        {{ $label }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    <div class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1fr)_420px]">
        <div class="space-y-5">
            <section class="rounded-[20px] border border-[#e7e9ee] bg-white p-5 shadow-[0_10px_30px_rgba(32,33,36,.035)] sm:p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">
                            Shipment Activity
                        </p>
                        <h2 class="mt-1 text-[17px] font-semibold text-[#202124]">Tracking Timeline</h2>
                    </div>

                    <span class="text-[8px] text-[#9aa0a6]">Newest first</span>
                </div>

                <div class="mt-6">
                    @forelse($timeline as $index => $entry)
                        @php
                            [$dotClass, $textClass] = $kindStyle($entry['kind']);
                        @endphp

                        <div class="relative flex gap-4 {{ !$loop->last ? 'pb-6' : '' }}">
                            @if(!$loop->last)
                                <span class="absolute left-[7px] top-5 bottom-0 w-px bg-[#e5e8ec]"></span>
                            @endif

                            <span class="relative z-10 mt-1.5 h-[15px] w-[15px] shrink-0 rounded-full border-[4px] border-white {{ $dotClass }} shadow-[0_0_0_1px_#e1e4e8]"></span>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="text-[10px] font-semibold {{ $textClass }}">
                                        {{ $entry['title'] }}
                                    </p>

                                    <time class="text-[7px] text-[#9aa0a6]">
                                        {{ $entry['time']->format('M d, Y · h:i A') }}
                                    </time>
                                </div>

                                <p class="mt-1.5 text-[9px] leading-5 text-[#7b8089]">
                                    {{ $entry['detail'] }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[15px] border border-dashed border-[#dfe3e8] bg-[#fafafb] px-4 py-9 text-center">
                            <p class="text-[9px] font-semibold text-[#6b7280]">No tracking activity yet.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[20px] border border-[#e7e9ee] bg-white p-5 shadow-[0_10px_30px_rgba(32,33,36,.035)] sm:p-6">
                <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">Order Contents</p>
                <h2 class="mt-1 text-[17px] font-semibold text-[#202124]">Items in this shipment</h2>

                <div class="mt-5 space-y-2.5">
                    @forelse($items as $item)
                        <div class="flex items-start justify-between gap-4 rounded-[14px] border border-[#eceef1] bg-[#fafafb] p-3.5">
                            <div class="min-w-0">
                                <p class="truncate text-[9px] font-semibold text-[#374151]">
                                    {{ $item['name'] ?? 'Marketplace item' }}
                                </p>

                                @if(!empty($item['variant_label']))
                                    <p class="mt-1 text-[8px] text-[#8a919b]">
                                        {{ $item['variant_label'] }}
                                    </p>
                                @endif
                            </div>

                            <div class="shrink-0 text-right">
                                <p class="text-[9px] font-semibold text-[#374151]">
                                    × {{ (int) ($item['qty'] ?? 1) }}
                                </p>
                                <p class="mt-1 text-[8px] text-[#8a919b]">
                                    ₱{{ number_format((float) ($item['line_total'] ?? 0), 2) }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-[9px] text-[#8a919b]">No item snapshot available.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="space-y-5">
            <section class="rounded-[20px] border border-[#e7e9ee] bg-white p-5 shadow-[0_10px_30px_rgba(32,33,36,.035)]">
                <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">Delivery Details</p>

                <div class="mt-5 space-y-4">
                    <div>
                        <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Buyer</p>
                        <p class="mt-1 text-[10px] font-semibold text-[#374151]">{{ $order->buyer_name ?: 'SARI Buyer' }}</p>
                        <p class="mt-1 break-words text-[8px] leading-4 text-[#8a919b]">{{ $order->buyer_address }}</p>
                    </div>

                    <div class="border-t border-[#eceef1] pt-4">
                        <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Assigned Rider</p>
                        <p class="mt-1 text-[10px] font-semibold text-[#374151]">{{ $order->courier_name ?: 'Not assigned yet' }}</p>
                        <p class="mt-1 text-[8px] text-[#8a919b]">{{ $order->courier_email ?: 'Waiting for Logistics assignment' }}</p>
                    </div>

                    <div class="border-t border-[#eceef1] pt-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Payment</p>
                                <p class="mt-1 text-[10px] font-semibold text-[#374151]">{{ $order->payment_method }}</p>
                            </div>

                            <span class="rounded-full bg-[#f5f6f7] px-2.5 py-1 text-[7px] font-bold uppercase text-[#6b7280]">
                                {{ $order->payment_status }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-[20px] border border-[#e7e9ee] bg-white p-5 shadow-[0_10px_30px_rgba(32,33,36,.035)]">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#63638d]">Logistics Hub</p>
                        <h2 class="mt-1 text-[15px] font-semibold text-[#202124]">Parcel Processing</h2>
                    </div>

                    <span class="grid h-10 w-10 place-items-center rounded-[12px] bg-[#f5f5fb] text-[#63638d]">
                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 19V7l8-4 8 4v12"></path>
                            <path d="M8 19v-5h8v5"></path>
                            <path d="M8 9h.01M12 9h.01M16 9h.01"></path>
                        </svg>
                    </span>
                </div>

                @if($parcel)
                    <div class="mt-5 space-y-3">
                        <div class="rounded-[14px] border border-[#eceef1] bg-[#fafafb] p-3.5">
                            <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Parcel Status</p>
                            <p class="mt-1 text-[10px] font-semibold text-[#374151]">
                                {{ ucwords(str_replace('_', ' ', $parcel->status)) }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-[14px] border border-[#eceef1] bg-[#fafafb] p-3.5">
                                <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Sorting Zone</p>
                                <p class="mt-1 text-[9px] font-semibold text-[#374151]">{{ $parcel->sorting_zone ?: '—' }}</p>
                            </div>

                            <div class="rounded-[14px] border border-[#eceef1] bg-[#fafafb] p-3.5">
                                <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Received</p>
                                <p class="mt-1 text-[9px] font-semibold text-[#374151]">
                                    {{ $parcel->received_at?->format('M d, h:i A') ?: '—' }}
                                </p>
                            </div>
                        </div>

                        @if($parcel->notes)
                            <div class="rounded-[14px] border border-[#eceef1] bg-white p-3.5">
                                <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Hub Notes</p>
                                <p class="mt-1.5 text-[8px] leading-4 text-[#7b8089]">{{ $parcel->notes }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="mt-5 rounded-[14px] border border-dashed border-[#dfe3e8] bg-[#fafafb] px-4 py-7 text-center">
                        <p class="text-[9px] font-semibold text-[#6b7280]">Parcel has not entered Logistics processing yet.</p>
                    </div>
                @endif
            </section>

            <section class="rounded-[20px] border border-[#e7e9ee] bg-white p-5 shadow-[0_10px_30px_rgba(32,33,36,.035)]">
                <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">Order Value</p>

                <div class="mt-4 space-y-2.5 text-[9px]">
                    <div class="flex justify-between gap-4">
                        <span class="text-[#8a919b]">Subtotal</span>
                        <span class="font-semibold text-[#374151]">₱{{ number_format((float) $order->subtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-[#8a919b]">Delivery</span>
                        <span class="font-semibold text-[#374151]">₱{{ number_format((float) $order->delivery_fee, 2) }}</span>
                    </div>

                    <div class="flex justify-between gap-4 border-t border-[#eceef1] pt-3">
                        <span class="font-semibold text-[#374151]">Total</span>
                        <span class="text-[13px] font-bold text-[#b97805]">₱{{ number_format((float) $order->total, 2) }}</span>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const endpoint = @json(route('seller.shipping.live-state', $order));
    let revision = @json($revision);
    const indicator = document.getElementById('sellerShippingLiveIndicator');

    async function checkShipment() {
        if (document.visibilityState !== 'visible') return;

        try {
            const response = await fetch(endpoint, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                cache: 'no-store',
            });

            if (!response.ok) return;

            const payload = await response.json();

            if (payload.revision && payload.revision !== revision) {
                if (indicator) {
                    indicator.innerHTML = '<span class="h-1.5 w-1.5 rounded-full bg-[#d89b10]"></span> Updating…';
                }

                window.location.reload();
            }
        } catch (error) {
            if (indicator) {
                indicator.innerHTML = '<span class="h-1.5 w-1.5 rounded-full bg-[#8a919b]"></span> Refresh to update';
                indicator.className = 'inline-flex h-10 items-center gap-2 rounded-xl border border-[#e1e4e8] bg-[#fafafb] px-3.5 text-[8px] font-semibold text-[#7b8089]';
            }
        }
    }

    window.setInterval(checkShipment, 8000);
})();
</script>
@endpush
@endsection
