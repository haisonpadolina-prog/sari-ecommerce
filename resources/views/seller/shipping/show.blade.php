@extends('layouts.seller')



@section('title', 'Track ' . $order->order_number . ' — SARI Seller')



@section('page-title', 'Shipping Tracking')



@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/shipping-detail.css') }}?v=20261007-1">







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



    @php

        $progressPercent = max(0, min(100, (int) ($shippingState['progress'] ?? 0)));



        $shipmentMilestones = [

            ['label' => 'Ready', 'caption' => 'Seller prepared', 'threshold' => 10],

            ['label' => 'Rider Pickup', 'caption' => 'Handoff to rider', 'threshold' => 35],

            ['label' => 'Logistics Hub', 'caption' => 'Hub processing', 'threshold' => 65],

            ['label' => 'Final Mile', 'caption' => 'Out for delivery', 'threshold' => 85],

            ['label' => 'Delivered', 'caption' => 'Buyer received', 'threshold' => 100],

        ];



        $currentMilestoneIndex = count($shipmentMilestones) - 1;



        foreach ($shipmentMilestones as $milestoneIndex => $milestone) {

            if ($progressPercent < $milestone['threshold']) {

                $currentMilestoneIndex = $milestoneIndex;

                break;

            }

        }

    @endphp



    <section class="seller-shipping-progress-card seller-shipping-progress-card--modern">

        <div class="seller-shipping-progress-modern-head">

            <div class="seller-shipping-progress-modern-copy">

                <p class="seller-shipping-progress-modern-eyebrow">Shipment progress</p>



                <div class="seller-shipping-progress-modern-title-row">

                    <h2>Delivery journey</h2>



                    <span class="seller-shipping-progress-modern-status">

                        {{ $progressPercent >= 100 ? 'Completed' : $shippingState['label'] }}

                    </span>

                </div>



                <p class="seller-shipping-progress-modern-route">

                    Seller <span>→</span> Rider <span>→</span> Logistics Hub <span>→</span> Buyer

                </p>

            </div>



            <div class="seller-shipping-progress-modern-metric" aria-label="{{ $progressPercent }} percent complete">

                <strong>{{ $progressPercent }}%</strong>

                <span>{{ $progressPercent >= 100 ? 'Complete' : 'In progress' }}</span>

            </div>

        </div>



        <div

            class="seller-shipping-progress-modern-meter"

            role="progressbar"

            aria-valuemin="0"

            aria-valuemax="100"

            aria-valuenow="{{ $progressPercent }}"

            aria-label="Shipment completion"

        >

            <span style="width: {{ $progressPercent }}%"></span>

        </div>



        <ol class="seller-shipping-progress-modern-steps" aria-label="Shipment milestones">

            @foreach($shipmentMilestones as $milestone)

                @php

                    $isComplete = $progressPercent >= $milestone['threshold'];

                    $isCurrent = $loop->index === $currentMilestoneIndex;

                    $nextThreshold = $shipmentMilestones[$loop->index + 1]['threshold'] ?? null;

                    $segmentComplete = $nextThreshold !== null && $progressPercent >= $nextThreshold;

                @endphp



                <li

                    class="seller-shipping-progress-modern-step

                        {{ $isComplete ? 'is-complete' : '' }}

                        {{ $isCurrent ? 'is-current' : '' }}

                        {{ $segmentComplete ? 'is-segment-complete' : '' }}"

                >

                    <div class="seller-shipping-progress-modern-node" aria-hidden="true">

                        @if($isComplete)

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">

                                <path d="m7 12 3 3 7-7"></path>

                            </svg>

                        @else

                            <span>{{ str_pad((string) ($loop->iteration), 2, '0', STR_PAD_LEFT) }}</span>

                        @endif

                    </div>



                    <div class="seller-shipping-progress-modern-step-copy">

                        <span class="seller-shipping-progress-modern-step-kicker">

                            Step {{ str_pad((string) ($loop->iteration), 2, '0', STR_PAD_LEFT) }}

                        </span>

                        <strong>{{ $milestone['label'] }}</strong>

                        <small>{{ $milestone['caption'] }}</small>

                    </div>

                </li>

            @endforeach

        </ol>

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



    window.__SARI_SELLER_SHIPPING_SHOW_LIFECYCLE__?.abort();

    const lifecycle = new AbortController();

    window.__SARI_SELLER_SHIPPING_SHOW_LIFECYCLE__ = lifecycle;

    const { signal } = lifecycle;

    const endpoint = @json(route('seller.shipping.live-state', $order));



    let revision = @json($revision);

    let checking = false;



    const indicator = document.getElementById('sellerShippingLiveIndicator');



    async function checkShipment() {



        if (signal.aborted || checking || document.visibilityState !== 'visible') return;

        checking = true;



        try {



            const response = await fetch(endpoint, {



                headers: {



                    'Accept': 'application/json',



                    'X-Requested-With': 'XMLHttpRequest',



                },



                credentials: 'same-origin',



                cache: 'no-store',

                signal,



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



            if (error?.name === 'AbortError') return;



            if (indicator) {



                indicator.innerHTML = '<span class="h-1.5 w-1.5 rounded-full bg-[#8a919b]"></span> Refresh to update';



                indicator.className = 'seller-shipping-live';



            }



        } finally {

            checking = false;

        }



    }



    const pollTimer = window.setInterval(checkShipment, 8000);

    const cleanup = () => {

        window.clearInterval(pollTimer);

        lifecycle.abort();

        if (window.__SARI_SELLER_SHIPPING_SHOW_LIFECYCLE__ === lifecycle) {

            window.__SARI_SELLER_SHIPPING_SHOW_LIFECYCLE__ = null;

        }

    };

    document.addEventListener('livewire:navigating', cleanup, { once: true, signal });

    window.addEventListener('beforeunload', cleanup, { once: true, signal });



})();



</script>



@endpush



@endsection
