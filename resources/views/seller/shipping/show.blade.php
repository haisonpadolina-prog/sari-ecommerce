@extends('layouts.seller')



@section('title', 'Track ' . $order->order_number . ' — SARI Seller')

@section('page-title', 'Shipping Tracking')


@push('styles')
<style id="sariSellerShippingDetailProfessionalStyles">
    .seller-shipping-detail-page {
        --ship-gold: #d59617;
        --ship-gold-hover: #c9890f;
        --ship-gold-soft: #fffaf0;
        --ship-ink: #111827;
        --ship-text: #344054;
        --ship-muted: #7c8592;
        --ship-soft-muted: #98a2b3;
        --ship-line: #e5e7eb;
        width: 100%;
        max-width: 1440px;
        margin-inline: auto;
        padding-bottom: 28px;
        color: var(--ship-ink);
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
    }

    .seller-shipping-detail-page a,
    .seller-shipping-detail-page button {
        font-family: inherit;
    }

    .seller-shipping-detail-header {
        display: flex;
        min-height: 72px;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
        margin: 0 0 16px;
        padding: 2px 2px 16px;
        border-bottom: 1px solid #e7ebf0;
    }

    .seller-shipping-detail-heading { min-width: 0; }

    .seller-shipping-detail-eyebrow {
        margin: 0;
        color: #b87605;
        font-size: 8px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .seller-shipping-detail-title {
        margin: 6px 0 0;
        color: var(--ship-ink);
        font-size: clamp(30px, 2.35vw, 38px);
        font-weight: 650;
        line-height: 1;
        letter-spacing: -.035em;
    }
    .seller-shipping-detail-title span { color: #C9890B; }

    .seller-shipping-detail-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
    }

    .seller-shipping-back {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #7c8592;
        font-size: 8px;
        font-weight: 600;
        text-decoration: none;
    }
    .seller-shipping-back:hover { color: #a56b05; }

    .seller-shipping-order-id {
        color: #475467;
        font-size: 9px;
        font-weight: 700;
    }

    .seller-shipping-detail-subtitle {
        max-width: 760px;
        margin: 8px 0 0;
        color: var(--ship-muted);
        font-size: 10.5px;
        line-height: 1.55;
    }

    .seller-shipping-detail-actions {
        display: flex;
        flex: 0 0 auto;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
        padding-top: 2px;
    }

    .seller-shipping-live,
    .seller-shipping-waybill-top {
        display: inline-flex;
        height: 42px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 12px;
        padding: 0 14px;
        font-size: 9px;
        font-weight: 650;
        white-space: nowrap;
    }

    .seller-shipping-live {
        border: 1px solid #d4e7da;
        background: #f5faf7;
        color: #4f7d63;
    }

    .seller-shipping-waybill-top {
        border: 1px solid #dfe3e8;
        background: #fff;
        color: #667085;
        text-decoration: none;
        transition: border-color .14s ease, background-color .14s ease, color .14s ease;
    }
    .seller-shipping-waybill-top:hover {
        border-color: #efd9a7;
        background: #fffaf0;
        color: #9b6505;
    }

    .seller-shipping-progress-card,
    .seller-shipping-detail-card {
        border: 1px solid var(--ship-line);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(15,23,42,.035);
    }

    .seller-shipping-progress-card {
        margin-top: 0;
        padding: 17px 18px;
    }

    .seller-shipping-progress-card .progress-title {
        color: #475467;
        font-size: 10px;
        font-weight: 650;
    }

    .seller-shipping-progress-card .progress-subtitle {
        margin-top: 3px;
        color: #98a2b3;
        font-size: 8px;
    }

    .seller-shipping-progress-card .progress-value {
        color: #b97805;
        font-size: 14px;
        font-weight: 750;
    }

    .seller-shipping-progress-track {
        height: 6px;
        overflow: hidden;
        margin-top: 13px;
        border-radius: 999px;
        background: #edf0f3;
    }
    .seller-shipping-progress-track > div {
        height: 100%;
        border-radius: inherit;
        background: var(--ship-gold);
    }

    .seller-shipping-milestones {
        display: grid;
        grid-template-columns: repeat(5, minmax(0,1fr));
        gap: 7px;
        margin-top: 12px;
    }

    .seller-shipping-detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 14px;
        margin-top: 14px;
        align-items: start;
    }

    .seller-shipping-main-stack,
    .seller-shipping-side-stack {
        display: grid;
        gap: 14px;
    }

    .seller-shipping-detail-card {
        padding: 16px;
    }

    .seller-shipping-section-eyebrow {
        color: #b87605;
        font-size: 8px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .seller-shipping-section-title {
        margin-top: 5px;
        color: #1f2937;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.02em;
    }

    .seller-shipping-timeline {
        margin-top: 18px;
    }

    .seller-shipping-timeline-entry {
        position: relative;
        display: flex;
        gap: 13px;
    }

    .seller-shipping-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        border: 1px solid #e7e9ee;
        border-radius: 12px;
        background: #fafbfc;
        padding: 11px 12px;
    }

    .seller-shipping-detail-label {
        color: #98a2b3;
        font-size: 7px;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .seller-shipping-detail-value {
        margin-top: 4px;
        color: #344054;
        font-size: 9.5px;
        font-weight: 650;
        line-height: 1.45;
    }

    .seller-shipping-detail-secondary {
        margin-top: 3px;
        color: #98a2b3;
        font-size: 8px;
        line-height: 1.5;
    }

    .seller-shipping-hub-icon {
        display: grid;
        width: 36px;
        height: 36px;
        place-items: center;
        border: 1px solid #e4e4f0;
        border-radius: 10px;
        background: #f7f7fc;
        color: #63638d;
    }
    .seller-shipping-hub-icon svg { width: 15px; height: 15px; }

    .seller-shipping-hub-field {
        border: 1px solid #e7e9ee;
        border-radius: 12px;
        background: #fafbfc;
        padding: 11px 12px;
    }

    .seller-shipping-value-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        color: #667085;
        font-size: 9px;
    }

    .seller-shipping-value-row + .seller-shipping-value-row { margin-top: 9px; }
    .seller-shipping-value-row strong { color: #344054; font-weight: 650; }

    .seller-shipping-total-row {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px;
        margin-top: 11px;
        border-top: 1px solid #edf0f3;
        padding-top: 11px;
    }
    .seller-shipping-total-row span { color: #344054; font-size: 10px; font-weight: 650; }
    .seller-shipping-total-row strong { color: #b97805; font-size: 17px; font-weight: 800; letter-spacing: -.03em; }

    @media (max-width: 1023px) {
        .seller-shipping-detail-header { flex-direction: column; gap: 13px; }
        .seller-shipping-detail-actions { width: 100%; justify-content: flex-start; padding-top: 0; }
        .seller-shipping-detail-grid { grid-template-columns: 1fr; }
        .seller-shipping-side-stack { grid-template-columns: repeat(2, minmax(0,1fr)); }
        .seller-shipping-side-stack > :last-child { grid-column: 1 / -1; }
    }

    @media (max-width: 640px) {
        .seller-shipping-detail-title { font-size: clamp(28px, 8vw, 34px); }
        .seller-shipping-detail-actions { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); }
        .seller-shipping-live, .seller-shipping-waybill-top { width: 100%; }
        .seller-shipping-milestones { gap: 4px; }
        .seller-shipping-side-stack { grid-template-columns: 1fr; }
        .seller-shipping-side-stack > :last-child { grid-column: auto; }
        .seller-shipping-detail-card { padding: 14px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-shipping-detail-page * { transition-duration: .01ms !important; scroll-behavior: auto !important; }
    }
</style>
@endpush




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



<div class="seller-shipping-detail-page">

    <section class="seller-shipping-detail-header">
        <div class="seller-shipping-detail-heading">
            <p class="seller-shipping-detail-eyebrow">Fulfillment & Logistics</p>
            <h1 class="seller-shipping-detail-title">Shipment <span>Tracking</span></h1>

            <div class="seller-shipping-detail-meta">
                <a href="{{ route('seller.shipping.index') }}" class="seller-shipping-back">
                    <span aria-hidden="true">←</span>
                    Back to Shipping
                </a>

                <span class="text-[#d0d5dd]" aria-hidden="true">•</span>
                <strong class="seller-shipping-order-id">{{ $order->order_number }}</strong>

                <span class="inline-flex rounded-full border px-2.5 py-1 text-[7.5px] font-bold uppercase tracking-[.08em] {{ $tone }}">
                    {{ $shippingState['label'] }}
                </span>
            </div>

            <p class="seller-shipping-detail-subtitle">{{ $shippingState['detail'] }}</p>
        </div>

        <div class="seller-shipping-detail-actions">
            <span id="sellerShippingLiveIndicator" class="seller-shipping-live">
                <span class="h-1.5 w-1.5 rounded-full bg-[#4f7d63]"></span>
                Live tracking
            </span>

            @if(Route::has('seller.orders.waybill'))
                <a href="{{ route('seller.orders.waybill', $order) }}" class="seller-shipping-waybill-top">
                    View Waybill
                </a>
            @endif
        </div>
    </section>



    <section class="seller-shipping-progress-card">

        <div class="flex items-center justify-between gap-4">

            <div>

                <p class="progress-title">Overall shipment progress</p>

                <p class="progress-subtitle">

                    Seller → Rider → Logistics Hub → Buyer

                </p>

            </div>



            <span class="progress-value">{{ $shippingState['progress'] }}%</span>

        </div>



        <div class="seller-shipping-progress-track">

            <div

                

                style="width: {{ $shippingState['progress'] }}%"

            ></div>

        </div>



        <div class="seller-shipping-milestones">

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



    <div class="seller-shipping-detail-grid">

        <div class="seller-shipping-main-stack">

            <section class="seller-shipping-detail-card">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <p class="seller-shipping-section-eyebrow">

                            Shipment Activity

                        </p>

                        <h2 class="seller-shipping-section-title">Tracking Timeline</h2>

                    </div>



                    <span class="text-[8px] text-[#9aa0a6]">Newest first</span>

                </div>



                <div class="seller-shipping-timeline">

                    @forelse($timeline as $index => $entry)

                        @php

                            [$dotClass, $textClass] = $kindStyle($entry['kind']);

                        @endphp



                        <div class="seller-shipping-timeline-entry {{ !$loop->last ? 'pb-6' : '' }}">

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



            <section class="seller-shipping-detail-card">

                <p class="seller-shipping-section-eyebrow">Order Contents</p>

                <h2 class="seller-shipping-section-title">Items in this shipment</h2>



                <div class="mt-5 space-y-2.5">

                    @forelse($items as $item)

                        <div class="seller-shipping-item">

                            <div class="min-w-0">

                                <p class="truncate text-[9px] font-semibold text-[#374151]">

                                    {{ $item['name'] ?? 'Marketplace item' }}

                                </p>



                                @if(!empty($item['variant_label']))

                                    <p class="seller-shipping-detail-secondary">

                                        {{ $item['variant_label'] }}

                                    </p>

                                @endif

                            </div>



                            <div class="shrink-0 text-right">

                                <p class="text-[9px] font-semibold text-[#374151]">

                                    × {{ (int) ($item['qty'] ?? 1) }}

                                </p>

                                <p class="seller-shipping-detail-secondary">

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



        <aside class="seller-shipping-side-stack">

            <section class="seller-shipping-detail-card">

                <p class="seller-shipping-section-eyebrow">Delivery Details</p>



                <div class="mt-5 space-y-4">

                    <div>

                        <p class="seller-shipping-detail-label">Buyer</p>

                        <p class="seller-shipping-detail-value">{{ $order->buyer_name ?: 'SARI Buyer' }}</p>

                        <p class="seller-shipping-detail-secondary">{{ $order->buyer_address }}</p>

                    </div>



                    <div class="border-t border-[#eceef1] pt-4">

                        <p class="seller-shipping-detail-label">Assigned Rider</p>

                        <p class="seller-shipping-detail-value">{{ $order->courier_name ?: 'Not assigned yet' }}</p>

                        <p class="seller-shipping-detail-secondary">{{ $order->courier_email ?: 'Waiting for Logistics assignment' }}</p>

                    </div>



                    <div class="border-t border-[#eceef1] pt-4">

                        <div class="flex items-center justify-between gap-4">

                            <div>

                                <p class="seller-shipping-detail-label">Payment</p>

                                <p class="seller-shipping-detail-value">{{ $order->payment_method }}</p>

                            </div>



                            <span class="rounded-full bg-[#f5f6f7] px-2.5 py-1 text-[7px] font-bold uppercase text-[#6b7280]">

                                {{ $order->payment_status }}

                            </span>

                        </div>

                    </div>

                </div>

            </section>



            <section class="seller-shipping-detail-card">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#63638d]">Logistics Hub</p>

                        <h2 class="seller-shipping-section-title">Parcel Processing</h2>

                    </div>



                    <span class="seller-shipping-hub-icon">

                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">

                            <path d="M4 19V7l8-4 8 4v12"></path>

                            <path d="M8 19v-5h8v5"></path>

                            <path d="M8 9h.01M12 9h.01M16 9h.01"></path>

                        </svg>

                    </span>

                </div>



                @if($parcel)

                    <div class="mt-5 space-y-3">

                        <div class="seller-shipping-hub-field">

                            <p class="seller-shipping-detail-label">Parcel Status</p>

                            <p class="seller-shipping-detail-value">

                                {{ ucwords(str_replace('_', ' ', $parcel->status)) }}

                            </p>

                        </div>



                        <div class="grid grid-cols-2 gap-3">

                            <div class="seller-shipping-hub-field">

                                <p class="seller-shipping-detail-label">Sorting Zone</p>

                                <p class="mt-1 text-[9px] font-semibold text-[#374151]">{{ $parcel->sorting_zone ?: '—' }}</p>

                            </div>



                            <div class="seller-shipping-hub-field">

                                <p class="seller-shipping-detail-label">Received</p>

                                <p class="mt-1 text-[9px] font-semibold text-[#374151]">

                                    {{ $parcel->received_at?->format('M d, h:i A') ?: '—' }}

                                </p>

                            </div>

                        </div>



                        @if($parcel->notes)

                            <div class="seller-shipping-hub-field">

                                <p class="seller-shipping-detail-label">Hub Notes</p>

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



            <section class="seller-shipping-detail-card">

                <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">Order Value</p>



                <div class="mt-4">

                    <div class="seller-shipping-value-row">

                        <span class="text-[#8a919b]">Subtotal</span>

                        <span class="font-semibold text-[#374151]">₱{{ number_format((float) $order->subtotal, 2) }}</span>

                    </div>



                    <div class="seller-shipping-value-row">

                        <span class="text-[#8a919b]">Delivery</span>

                        <span class="font-semibold text-[#374151]">₱{{ number_format((float) $order->delivery_fee, 2) }}</span>

                    </div>



                    <div class="seller-shipping-total-row">

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

                indicator.className = 'seller-shipping-live';

            }

        }

    }



    window.setInterval(checkShipment, 8000);

})();

</script>

@endpush

@endsection
