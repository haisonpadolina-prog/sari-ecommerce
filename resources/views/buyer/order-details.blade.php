@extends('layouts.buyer')

@section('title', 'Order Tracking — SARI')
@section('page-title', 'Order Tracking')

@section('content')
@include('components.buyer.header')

@php
    $items = (array) $order->items;

    $statusIndex = match ($order->status) {
        'new' => 1,
        'preparing' => 2,
        'ready_for_pickup' => 3,
        'courier_accepted', 'heading_pickup', 'arrived_pickup' => 4,
        'in_transit' => 5,
        'arrived_buyer' => 6,
        'delivered' => 7,
        default => 0,
    };

    $cancelled = $order->status === 'cancelled';

    $steps = [
        [
            'label' => 'Order placed',
            'description' => 'Your order was successfully submitted to the seller.',
            'time' => $order->created_at,
        ],
        [
            'label' => 'Seller preparing order',
            'description' => 'The seller is packing and preparing your items.',
            'time' => $order->status !== 'new' ? $order->updated_at : null,
        ],
        [
            'label' => 'Ready for pickup',
            'description' => 'Your parcel is packed and waiting for courier pickup.',
            'time' => $order->ready_at,
        ],
        [
            'label' => 'Courier assigned',
            'description' => $order->courier_name
                ? $order->courier_name . ' accepted your delivery.'
                : 'Waiting for a courier to accept the delivery request.',
            'time' => $order->accepted_at,
        ],
        [
            'label' => 'Parcel in transit',
            'description' => 'The courier has picked up your parcel and is heading to you.',
            'time' => $order->picked_up_at,
        ],
        [
            'label' => 'Courier arrived',
            'description' => 'The courier reached your delivery location.',
            'time' => $order->arrived_buyer_at,
        ],
        [
            'label' => 'Delivered',
            'description' => 'Your order has been delivered successfully.',
            'time' => $order->delivered_at,
        ],
    ];

    $progress = $cancelled ? 0 : match ($order->status) {
        'new' => 10,
        'preparing' => 25,
        'ready_for_pickup' => 40,
        'courier_accepted' => 52,
        'heading_pickup' => 58,
        'arrived_pickup' => 66,
        'in_transit' => 78,
        'arrived_buyer' => 92,
        'delivered' => 100,
        default => 0,
    };

    $summarySteps = [
        ['label' => 'Order Placed', 'threshold' => 1],
        ['label' => 'Processing', 'threshold' => 2],
        ['label' => 'In Transit', 'threshold' => 5],
        ['label' => 'Out for Delivery', 'threshold' => 6],
        ['label' => 'Delivered', 'threshold' => 7],
    ];

    $activeSummaryStep = match (true) {
        $statusIndex >= 7 => 5,
        $statusIndex >= 6 => 4,
        $statusIndex >= 5 => 3,
        $statusIndex >= 2 => 2,
        $statusIndex >= 1 => 1,
        default => 0,
    };

    $deliveryHeadline = match ($order->status) {
        'new' => 'Your order has been placed.',
        'preparing' => 'The seller is preparing your order.',
        'ready_for_pickup' => 'Your parcel is ready for pickup.',
        'courier_accepted', 'heading_pickup', 'arrived_pickup' => 'A courier is handling your parcel.',
        'in_transit' => 'Your order is on the way.',
        'arrived_buyer' => 'Your courier has arrived.',
        'delivered' => 'Your order has been delivered.',
        default => 'Track your order progress here.',
    };

    $deliverySubtext = match ($order->status) {
        'new' => 'The seller will start preparing your items shortly.',
        'preparing' => 'Your items are being packed and prepared for dispatch.',
        'ready_for_pickup' => 'Your parcel is waiting for courier pickup.',
        'courier_accepted', 'heading_pickup', 'arrived_pickup' => 'The courier is preparing to move your parcel to the next stage.',
        'in_transit' => 'The courier is currently moving your parcel toward your delivery area.',
        'arrived_buyer' => 'Please be ready to receive your order.',
        'delivered' => 'Thank you for shopping with SARI.',
        default => 'Delivery updates will appear as your order moves through fulfillment.',
    };

    $routeProgress = $cancelled ? 0 : match ($order->status) {
        'new', 'preparing' => 0,
        'ready_for_pickup' => 8,
        'courier_accepted' => 18,
        'heading_pickup' => 28,
        'arrived_pickup' => 38,
        'in_transit' => 68,
        'arrived_buyer' => 92,
        'delivered' => 100,
        default => 0,
    };

    $routeOffset = max(0, 100 - $routeProgress);
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    .sari-order-tracking {
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /*
     * Soft elevation system:
     * keeps the page white while giving the main surfaces a restrained
     * neumorphic lift without making the interface look overly glossy.
     */
    .sari-soft-panel {
        box-shadow:
            0 16px 36px rgba(44, 39, 33, 0.065),
            0 4px 12px rgba(44, 39, 33, 0.035),
            inset 0 1px 0 rgba(255, 255, 255, 0.96);
    }

    .sari-soft-panel-sm {
        box-shadow:
            0 10px 24px rgba(44, 39, 33, 0.055),
            0 2px 8px rgba(44, 39, 33, 0.03),
            inset 0 1px 0 rgba(255, 255, 255, 0.95);
    }

    .sari-map-panel {
        box-shadow:
            0 14px 30px rgba(48, 42, 35, 0.07),
            0 3px 10px rgba(48, 42, 35, 0.035),
            inset 0 1px 0 rgba(255, 255, 255, 0.92);
    }

    @media (hover: hover) and (pointer: fine) {
        .sari-soft-panel,
        .sari-soft-panel-sm {
            transition:
                box-shadow .22s ease,
                transform .22s ease,
                border-color .22s ease;
        }

        .sari-soft-panel:hover {
            box-shadow:
                0 18px 42px rgba(44, 39, 33, 0.075),
                0 5px 14px rgba(44, 39, 33, 0.04),
                inset 0 1px 0 rgba(255, 255, 255, 0.98);
        }

        .sari-soft-panel-sm:hover {
            box-shadow:
                0 12px 28px rgba(44, 39, 33, 0.065),
                0 3px 9px rgba(44, 39, 33, 0.035),
                inset 0 1px 0 rgba(255, 255, 255, 0.98);
        }
    }

    .sari-map-route-progress {
        stroke-dasharray: 100;
        stroke-dashoffset: var(--route-offset, 100);
        animation: sariRouteReveal 1.15s cubic-bezier(.22, 1, .36, 1) forwards;
    }

    .sari-map-courier {
        filter: drop-shadow(0 8px 10px rgba(108, 74, 22, .18));
    }

    .sari-map-road-label {
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        font-size: 10px;
        font-weight: 600;
        fill: #9c958c;
        letter-spacing: .02em;
    }

    .sari-map-place-label {
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        font-size: 11px;
        font-weight: 700;
        fill: #4a443e;
    }

    @keyframes sariRouteReveal {
        from {
            stroke-dashoffset: 100;
        }
        to {
            stroke-dashoffset: var(--route-offset, 100);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-map-route-progress {
            animation: none !important;
        }
    }
</style>

<div class="sari-order-tracking mx-auto w-full max-w-[1450px] p-4 sm:p-6 lg:p-8">
    <div class="mb-5 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <a href="{{ route('buyer.orders') }}"
               class="inline-flex items-center gap-2 text-[14px] font-semibold text-[#7c746c] transition hover:text-[#b67813]">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back to My Orders
            </a>

            <p class="mt-5 text-[13px] font-semibold uppercase tracking-[.16em] text-[#b47b1e]">Order tracking</p>
            <h1 class="mt-1 text-[30px] font-bold tracking-[-.04em] text-[#201d19] sm:text-[36px]">Track your delivery</h1>
            <p class="mt-2 max-w-[680px] text-[14px] leading-5 text-[#857b70]">
                Follow your order status and delivery progress from checkout to doorstep.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="text-[13px] text-[#9a9187]">Need help?</span>
            <a href="{{ route('buyer.messages', ['seller' => $order->seller_account_id]) }}"
               class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[#e8d7b6] bg-[#fffaf0] px-4 text-[13px] font-semibold text-[#9d6d18] transition hover:border-[#d9bd83] hover:bg-[#fff6e7]">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M4 13a8 8 0 0116 0v3a2 2 0 01-2 2h-2v-6h4M4 12v6h4v-6H4z" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 18c0 1.1-.9 2-2 2h-2" stroke-linecap="round"/>
                </svg>
                Contact Support
            </a>
        </div>
    </div>

    <div class="mb-5 flex flex-wrap items-center gap-2 text-[12px] font-medium text-[#9b9289]">
        <a href="{{ route('buyer.home') }}" class="transition hover:text-[#b67813]">Home</a>
        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <a href="{{ route('buyer.orders') }}" class="transition hover:text-[#b67813]">Orders</a>
        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="font-semibold text-[#b67813]">{{ $order->order_number }}</span>
    </div>

    @if ($cancelled)
        <section class="rounded-[24px] border border-[#efcece] bg-[#fff8f8] p-5 sm:p-6">
            <div class="flex items-start gap-4">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#fbe4e4] text-[#a65353]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M12 8v4m0 4h.01M10.3 3.9L2.6 17.3A2 2 0 004.3 20h15.4a2 2 0 001.7-2.7L13.7 3.9a2 2 0 00-3.4 0z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-[17px] font-bold text-[#913f3f]">Order cancelled</h2>
                    <p class="mt-1 text-[13px] leading-5 text-[#926e6e]">
                        {{ $order->cancellation_reason ?: 'This order was cancelled.' }}
                    </p>
                    @if ($order->cancelled_at)
                        <p class="mt-2 text-[11px] text-[#ad8b8b]">{{ $order->cancelled_at->format('M d, Y · h:i A') }}</p>
                    @endif
                </div>
            </div>
        </section>
    @else
        <section class="sari-soft-panel overflow-hidden rounded-[26px] border border-[#ebe4da] bg-white">
            <div class="flex flex-col gap-4 border-b border-[#efe8df] px-5 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-7">
                <div class="flex min-w-0 items-start gap-4">
                    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-[#fff5df] text-[#b77914]">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <path d="M4 7.5L12 3l8 4.5v9L12 21l-8-4.5v-9z" stroke-linejoin="round"/>
                            <path d="M4.5 7.7L12 12l7.5-4.3M12 12v9M8 5.2l8 4.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h2 class="truncate text-[20px] font-bold tracking-[-.025em] text-[#26211d] sm:text-[22px]">{{ $order->order_number }}</h2>
                            <span class="rounded-full bg-[#fff2d6] px-3 py-1 text-[11px] font-bold uppercase tracking-[.11em] text-[#a66f0f]">
                                {{ $order->statusLabel() }}
                            </span>
                        </div>
                        <p class="mt-1.5 text-[12px] text-[#968c82]">
                            Placed {{ $order->created_at?->format('M d, Y · h:i A') }}
                            <span class="mx-1.5">•</span>
                            {{ $order->seller?->store_name ?: 'SARI Seller' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4 lg:text-right">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.13em] text-[#9b9289]">Fulfillment</p>
                        <p class="mt-1 text-[18px] font-bold text-[#b67813]">{{ $progress }}%</p>
                    </div>
                    <div class="h-10 w-px bg-[#eee6dc]"></div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.13em] text-[#9b9289]">Payment</p>
                        <p class="mt-1 text-[13px] font-semibold text-[#4e4740]">{{ $order->payment_method }} · {{ ucfirst($order->payment_status) }}</p>
                    </div>
                </div>
            </div>

            <div class="px-5 py-6 sm:px-6 lg:px-7 lg:py-7">
                <div class="relative hidden lg:block">
                    <div class="absolute left-[8%] right-[8%] top-[22px] h-[2px] bg-[#ece6de]"></div>
                    <div class="absolute left-[8%] top-[22px] h-[2px] bg-[#e9ad32] transition-all duration-500"
                         style="width: {{ max(0, min(84, (($activeSummaryStep - 1) / 4) * 84)) }}%;"></div>

                    <div class="relative grid grid-cols-5 gap-4">
                        @foreach ($summarySteps as $index => $summaryStep)
                            @php
                                $summaryNumber = $index + 1;
                                $isComplete = $activeSummaryStep > $summaryNumber || $statusIndex >= $summaryStep['threshold'] && $activeSummaryStep >= $summaryNumber;
                                $isCurrent = $activeSummaryStep === $summaryNumber && $order->status !== 'delivered';
                            @endphp

                            <div class="text-center">
                                <div class="mx-auto grid h-11 w-11 place-items-center rounded-full border-2 bg-white transition
                                    {{ $isComplete ? 'border-[#ecb033] text-[#b67813]' : 'border-[#ded8cf] text-[#aaa198]' }}
                                    {{ $isCurrent ? 'ring-8 ring-[#fff7e7]' : '' }}">
                                    @if ($summaryNumber === 1)
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M5 12l4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @elseif ($summaryNumber === 2)
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M4 7h16M6 7l1 12h10l1-12M9 11v4m6-4v4M8 4h8" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @elseif ($summaryNumber === 3)
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M3 7h11v10H3V7zm11 3h3l3 3v4h-6v-7zM7 19a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z" stroke-linejoin="round"/>
                                        </svg>
                                    @elseif ($summaryNumber === 4)
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M12 3a7 7 0 017 7v3a5 5 0 11-10 0v-3a3 3 0 116 0v3a1 1 0 11-2 0v-3a1 1 0 10-2 0v3a3 3 0 106 0v-3a5 5 0 00-10 0v3" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M5 12l4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @endif
                                </div>
                                <p class="mt-3 text-[13px] font-semibold {{ $isComplete ? 'text-[#332d28]' : 'text-[#9f968d]' }}">{{ $summaryStep['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="lg:hidden">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[.12em] text-[#9c9186]">Current stage</p>
                            <p class="mt-1 text-[14px] font-bold text-[#342e28]">{{ $order->statusLabel() }}</p>
                        </div>
                        <span class="text-[15px] font-bold text-[#b67813]">{{ $progress }}%</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-[#ede7df]">
                        <div class="h-full rounded-full bg-[#e2a328] transition-all duration-500" style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                <div class="sari-soft-panel-sm mt-7 flex flex-col gap-4 rounded-2xl border border-[#eee5d8] bg-[#fffdf9] p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                    <div class="flex min-w-0 items-center gap-3.5">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-[#fff3d7] text-[#b67813]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M3 7h11v10H3V7zm11 3h3l3 3v4h-6v-7zM7 19a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[14px] font-bold text-[#332d28]">{{ $deliveryHeadline }}</p>
                            <p class="mt-1 text-[12px] leading-5 text-[#8a8076]">{{ $deliverySubtext }}</p>
                        </div>
                    </div>

                    <div class="shrink-0 sm:text-right">
                        <p class="text-[11px] font-semibold uppercase tracking-[.12em] text-[#9c9186]">Delivery status</p>
                        <p class="mt-1 text-[14px] font-bold text-[#413a34]">{{ $order->statusLabel() }}</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1.45fr)_minmax(320px,.75fr)]">
            <section class="sari-soft-panel overflow-hidden rounded-[24px] border border-[#ebe4da] bg-white">
                <div class="flex flex-col gap-4 border-b border-[#eee7de] px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[.14em] text-[#a8751c]">Shipment progress</p>
                        <h2 class="mt-1 text-[19px] font-bold tracking-[-.025em] text-[#312b26]">Delivery journey</h2>
                    </div>
                    <span class="inline-flex w-fit items-center gap-2 rounded-full border border-[#eadfcf] bg-[#fcfaf6] px-3 py-1.5 text-[11px] font-semibold text-[#80766c]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#d59b2d]"></span>
                        Status-based preview
                    </span>
                </div>

                <div class="grid lg:grid-cols-[300px_minmax(0,1fr)]">
                    <div class="border-b border-[#eee7de] p-5 sm:p-6 lg:border-b-0 lg:border-r">
                        <div class="relative">
                            @foreach ($steps as $index => $step)
                                @php
                                    $stepNumber = $index + 1;
                                    $complete = $statusIndex >= $stepNumber;
                                    $current = $statusIndex === $stepNumber && $order->status !== 'delivered';
                                @endphp

                                <div class="relative flex gap-3.5 pb-6 last:pb-0">
                                    @if (!$loop->last)
                                        <div class="absolute left-[15px] top-8 h-[calc(100%-10px)] w-px {{ $complete && $statusIndex > $stepNumber ? 'bg-[#dca137]' : 'bg-[#e6dfd6]' }}"></div>
                                    @endif

                                    <div class="relative z-10 grid h-8 w-8 shrink-0 place-items-center rounded-full border text-[12px] font-bold
                                        {{ $complete ? 'border-[#d9a13c] bg-[#fff6e5] text-[#b67813]' : 'border-[#e2dbd2] bg-white text-[#b4aaa0]' }}
                                        {{ $current ? 'ring-4 ring-[#fff6e7]' : '' }}">
                                        @if ($complete && !$current)
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path d="M5 12l4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        @else
                                            {{ $stepNumber }}
                                        @endif
                                    </div>

                                    <div class="min-w-0 pt-0.5">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-[13px] font-bold {{ $complete ? 'text-[#3b342e]' : 'text-[#a29a91]' }}">{{ $step['label'] }}</p>
                                            @if ($current)
                                                <span class="rounded-full bg-[#fff1d3] px-2 py-0.5 text-[9px] font-bold uppercase tracking-[.1em] text-[#a66f0f]">Current</span>
                                            @endif
                                        </div>
                                        @if ($step['time'])
                                            <p class="mt-1 text-[10px] font-medium text-[#9b9187]">{{ $step['time']->format('M d, Y · h:i A') }}</p>
                                        @endif
                                        <p class="mt-1 text-[11px] leading-4 {{ $complete ? 'text-[#8b8177]' : 'text-[#aaa198]' }}">{{ $step['description'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        <div class="sari-map-panel relative overflow-hidden rounded-[24px] border border-[#e7e0d7] bg-[#f7f5f1]">
                            <div class="absolute left-4 top-4 z-20 flex max-w-[calc(100%-2rem)] items-center gap-3 rounded-2xl border border-white/80 bg-white/95 px-4 py-3 shadow-[0_8px_26px_rgba(55,46,37,0.08)] backdrop-blur">
                                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#fff3d8] text-[#b77914]">
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="M3 7h11v10H3V7zm11 3h3l3 3v4h-6v-7zM7 19a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[12px] font-bold text-[#3e3832]">Delivery route</p>
                                    <p class="mt-0.5 text-[10px] font-medium text-[#91877d]">
                                        Progress preview based on your current order status
                                    </p>
                                </div>
                            </div>

                            <div class="absolute right-4 top-4 z-20 hidden rounded-full border border-[#e8dfd1] bg-[#fffaf0] px-3 py-2 text-[10px] font-semibold text-[#9b6b17] sm:inline-flex">
                                {{ $routeProgress }}% route progress
                            </div>

                            <svg class="block h-auto w-full" viewBox="0 0 820 520" role="img" aria-label="Premium static delivery map showing route progress to the buyer">
                                <defs>
                                    <linearGradient id="sariMapBackground" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#f9f7f2"/>
                                        <stop offset="100%" stop-color="#f0eee8"/>
                                    </linearGradient>
                                    <linearGradient id="sariRouteGold" x1="0" y1="0" x2="1" y2="0">
                                        <stop offset="0%" stop-color="#c88818"/>
                                        <stop offset="100%" stop-color="#e1ac3e"/>
                                    </linearGradient>
                                    <filter id="sariMapSoftShadow" x="-40%" y="-40%" width="180%" height="180%">
                                        <feDropShadow dx="0" dy="7" stdDeviation="7" flood-color="#6e5635" flood-opacity=".14"/>
                                    </filter>
                                </defs>

                                <rect width="820" height="520" fill="url(#sariMapBackground)"/>

                                {{-- Water --}}
                                <path d="M690 -10C674 69 677 126 698 184C722 252 764 293 834 326V-10Z" fill="#e9f0f1"/>
                                <path d="M702 -10C686 70 689 126 710 181C733 239 773 281 829 305" fill="none" stroke="#d8e5e7" stroke-width="4"/>

                                {{-- Green spaces --}}
                                <path d="M66 90L174 58L236 108L205 184L93 177L47 136Z" fill="#e8ecdf"/>
                                <path d="M534 70L654 48L715 112L681 177L559 165L516 117Z" fill="#e7ecdf"/>
                                <path d="M421 337L553 308L630 369L592 455L454 446L398 395Z" fill="#e7ebdf"/>
                                <path d="M75 341L188 306L256 365L220 447L103 436L54 394Z" fill="#e9ede2"/>

                                {{-- Major roads --}}
                                <path d="M-20 104C128 63 256 74 375 105C511 139 624 111 850 45" fill="none" stroke="#dedad3" stroke-width="30" stroke-linecap="round"/>
                                <path d="M-25 397C126 350 262 351 390 386C535 426 665 402 850 317" fill="none" stroke="#dfdbd4" stroke-width="25" stroke-linecap="round"/>
                                <path d="M193 -35C215 89 232 169 269 247C304 320 331 405 339 555" fill="none" stroke="#ddd9d2" stroke-width="24" stroke-linecap="round"/>
                                <path d="M589 -30C548 77 531 159 525 251C519 348 549 431 598 550" fill="none" stroke="#dedad3" stroke-width="24" stroke-linecap="round"/>

                                {{-- Road center lines --}}
                                <path d="M-20 104C128 63 256 74 375 105C511 139 624 111 850 45" fill="none" stroke="#f8f6f2" stroke-width="5" stroke-linecap="round" opacity=".95"/>
                                <path d="M-25 397C126 350 262 351 390 386C535 426 665 402 850 317" fill="none" stroke="#f8f6f2" stroke-width="4" stroke-linecap="round" opacity=".9"/>
                                <path d="M193 -35C215 89 232 169 269 247C304 320 331 405 339 555" fill="none" stroke="#f8f6f2" stroke-width="4" stroke-linecap="round" opacity=".9"/>
                                <path d="M589 -30C548 77 531 159 525 251C519 348 549 431 598 550" fill="none" stroke="#f8f6f2" stroke-width="4" stroke-linecap="round" opacity=".9"/>

                                {{-- Secondary streets --}}
                                <g fill="none" stroke="#e5e1da" stroke-width="8" stroke-linecap="round" opacity=".95">
                                    <path d="M32 237C145 215 255 219 378 246C505 275 632 261 799 215"/>
                                    <path d="M122 30C116 138 130 218 166 287C196 346 211 420 215 497"/>
                                    <path d="M404 19C384 113 382 196 404 266C425 335 447 403 455 511"/>
                                    <path d="M655 114C599 174 570 231 560 297C551 360 565 421 611 494"/>
                                </g>

                                {{-- Buildings --}}
                                <g fill="#e4e0d8">
                                    <rect x="103" y="204" width="42" height="34" rx="7"/>
                                    <rect x="157" y="210" width="58" height="43" rx="8"/>
                                    <rect x="320" y="122" width="63" height="42" rx="8"/>
                                    <rect x="405" y="157" width="46" height="34" rx="7"/>
                                    <rect x="608" y="231" width="55" height="38" rx="8"/>
                                    <rect x="283" y="416" width="55" height="39" rx="8"/>
                                    <rect x="485" y="271" width="39" height="31" rx="7"/>
                                    <rect x="94" y="279" width="54" height="35" rx="7"/>
                                </g>

                                {{-- Map labels --}}
                                <text x="47" y="122" class="sari-map-road-label">North Avenue</text>
                                <text x="606" y="405" class="sari-map-road-label">Central Road</text>
                                <text x="541" y="285" class="sari-map-road-label" transform="rotate(-78 541 285)">Delivery Avenue</text>
                                <text x="199" y="305" class="sari-map-road-label" transform="rotate(75 199 305)">Market Street</text>

                                {{-- Full route base --}}
                                <path id="sariPremiumRoute"
                                      pathLength="100"
                                      d="M171 385C223 366 256 340 287 306C323 267 340 229 387 215C440 199 487 212 525 183C561 155 590 124 635 102"
                                      fill="none"
                                      stroke="#eadfca"
                                      stroke-width="14"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>

                                {{-- Completed route --}}
                                <path pathLength="100"
                                      d="M171 385C223 366 256 340 287 306C323 267 340 229 387 215C440 199 487 212 525 183C561 155 590 124 635 102"
                                      fill="none"
                                      stroke="url(#sariRouteGold)"
                                      stroke-width="7"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"
                                      class="sari-map-route-progress"
                                      style="--route-offset: {{ $routeOffset }};"/>

                                {{-- Sorting center --}}
                                <g transform="translate(171 385)" filter="url(#sariMapSoftShadow)">
                                    <circle r="27" fill="#fff" stroke="#ded5c8" stroke-width="2"/>
                                    <circle r="20" fill="#2f2b27"/>
                                    <path d="M-9 4V-7H9V4M-12 4H12M-5-7V-12H5V-7" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </g>
                                <text x="126" y="429" class="sari-map-place-label">Sorting Center</text>

                                {{-- Destination --}}
                                <g transform="translate(635 102)" filter="url(#sariMapSoftShadow)">
                                    <circle r="28" fill="#fff" stroke="#e1d6c5" stroke-width="2"/>
                                    <circle r="20" fill="#fff4d9"/>
                                    <path d="M0-10C-7-10-12-5-12 2c0 9 12 19 12 19S12 11 12 2C12-5 7-10 0-10Z" fill="#c98b1c"/>
                                    <circle cy="2" r="4" fill="#fff"/>
                                </g>
                                <text x="650" y="108" class="sari-map-place-label">Delivery Area</text>

                                {{-- Courier moves once to current progress and stays there --}}
                                @if ($routeProgress > 0)
                                    <g class="sari-map-courier">
                                        <circle r="23" fill="#fff" stroke="#e5d6bb" stroke-width="2"/>
                                        <circle r="17" fill="#d79a2b"/>
                                        <g transform="translate(-10 -8)" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 5h12v8H1zM13 8h4l3 3v2h-7z"/>
                                            <circle cx="5" cy="15" r="2"/>
                                            <circle cx="16" cy="15" r="2"/>
                                        </g>
                                        <animateMotion
                                            dur="1.15s"
                                            begin="0s"
                                            fill="freeze"
                                            rotate="auto"
                                            calcMode="linear"
                                            keyPoints="0;{{ number_format($routeProgress / 100, 2, '.', '') }}"
                                            keyTimes="0;1">
                                            <mpath href="#sariPremiumRoute"/>
                                        </animateMotion>
                                    </g>
                                @else
                                    <g transform="translate(171 385)" class="sari-map-courier">
                                        <circle r="23" fill="#fff" stroke="#e5d6bb" stroke-width="2"/>
                                        <circle r="17" fill="#d79a2b"/>
                                        <g transform="translate(-10 -8)" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 5h12v8H1zM13 8h4l3 3v2h-7z"/>
                                            <circle cx="5" cy="15" r="2"/>
                                            <circle cx="16" cy="15" r="2"/>
                                        </g>
                                    </g>
                                @endif
                            </svg>

                            <div class="absolute bottom-4 left-4 right-4 z-20 rounded-2xl border border-white/80 bg-white/95 p-4 shadow-[0_10px_30px_rgba(52,44,34,0.09)] backdrop-blur">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="truncate text-[13px] font-bold text-[#3b352f]">{{ $order->courier_name ?: 'Courier not yet assigned' }}</p>
                                            <span class="rounded-full bg-[#fff3d8] px-2.5 py-1 text-[9px] font-bold uppercase tracking-[.08em] text-[#9d6c17]">
                                                {{ $order->statusLabel() }}
                                            </span>
                                        </div>
                                        <p class="mt-1.5 text-[10px] leading-5 text-[#8c8278]">
                                            The route fills to the parcel's current stage and then stops. This is a status preview, not live GPS tracking.
                                        </p>
                                    </div>

                                    <div class="shrink-0 rounded-xl bg-[#f8f5ef] px-4 py-2.5 sm:text-right">
                                        <p class="text-[9px] font-semibold uppercase tracking-[.1em] text-[#9b9187]">Route progress</p>
                                        <p class="mt-0.5 text-[16px] font-bold text-[#b67813]">{{ $routeProgress }}%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <aside class="space-y-5">
                <section class="sari-soft-panel-sm rounded-[24px] border border-[#ebe4da] bg-white p-5 sm:p-6">
                    <div class="flex items-center justify-between gap-3 border-b border-[#eee7de] pb-4">
                        <h2 class="text-[17px] font-bold text-[#322c27]">Order summary</h2>
                        <span class="rounded-full bg-[#fbf7ef] px-2.5 py-1 text-[10px] font-semibold text-[#8b8177]">{{ count($items) }} {{ count($items) === 1 ? 'item' : 'items' }}</span>
                    </div>

                    <dl class="mt-4 space-y-4">
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-[11px] text-[#968c82]">Order ID</dt>
                            <dd class="text-right text-[11px] font-semibold text-[#403932]">{{ $order->order_number }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-[11px] text-[#968c82]">Payment method</dt>
                            <dd class="text-right text-[11px] font-semibold text-[#403932]">{{ $order->payment_method }}</dd>
                        </div>
                        <div class="border-t border-[#eee7de] pt-4">
                            <dt class="text-[11px] text-[#968c82]">Shipping address</dt>
                            <dd class="mt-2 text-[12px] font-semibold leading-5 text-[#49423b]">{{ $order->buyer_name }}</dd>
                            <dd class="mt-0.5 text-[11px] leading-4 text-[#847a70]">{{ $order->buyer_address }}</dd>
                            @if ($order->buyer_phone)
                                <dd class="mt-1 text-[10px] text-[#9c9186]">{{ $order->buyer_phone }}</dd>
                            @endif
                        </div>
                        <div class="border-t border-[#eee7de] pt-4">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-[11px] text-[#968c82]">Subtotal</dt>
                                <dd class="text-[11px] font-semibold text-[#403932]">₱{{ number_format((float) $order->subtotal, 2) }}</dd>
                            </div>
                            <div class="mt-2.5 flex items-center justify-between gap-4">
                                <dt class="text-[11px] text-[#968c82]">Delivery fee</dt>
                                <dd class="text-[11px] font-semibold text-[#403932]">₱{{ number_format((float) $order->delivery_fee, 2) }}</dd>
                            </div>
                            <div class="mt-4 flex items-center justify-between gap-4 border-t border-[#eee7de] pt-4">
                                <dt class="text-[13px] font-bold text-[#39332d]">Total amount</dt>
                                <dd class="text-[20px] font-bold tracking-[-.02em] text-[#b67813]">₱{{ number_format((float) $order->total, 2) }}</dd>
                            </div>
                        </div>
                    </dl>
                </section>

                <section class="sari-soft-panel-sm rounded-[24px] border border-[#ebe4da] bg-white p-5 sm:p-6">
                    <div class="flex items-center gap-3">
                        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#fff4dd] text-[#b67813]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M3 7h11v10H3V7zm11 3h3l3 3v4h-6v-7zM7 19a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-[.11em] text-[#978d82]">Courier details</p>
                            <p class="mt-1 truncate text-[14px] font-bold text-[#3d3630]">{{ $order->courier_name ?: 'Waiting for assignment' }}</p>
                        </div>
                    </div>

                    @if ($order->courier_email)
                        <div class="mt-4 rounded-xl bg-[#faf8f4] px-3.5 py-3">
                            <p class="text-[10px] font-semibold uppercase tracking-[.1em] text-[#9a9086]">Courier contact</p>
                            <p class="mt-1 break-all text-[11px] font-medium text-[#59514a]">{{ $order->courier_email }}</p>
                        </div>
                    @endif

                    <div class="mt-4 rounded-xl border border-[#efe3cd] bg-[#fffaf1] px-3.5 py-3">
                        <p class="text-[11px] font-semibold text-[#8d631c]">Map preview only</p>
                        <p class="mt-1 text-[10px] leading-4 text-[#9b8664]">Courier movement on the map is an interface animation, not live location data.</p>
                    </div>
                </section>
            </aside>
        </div>
    @endif

    <section class="sari-soft-panel mt-5 rounded-[24px] border border-[#ebe4da] bg-white p-5 sm:p-6">
        <div class="flex flex-wrap items-end justify-between gap-3 border-b border-[#eee7de] pb-4">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[.13em] text-[#978d82]">Items in this order</p>
                <h2 class="mt-1 text-[19px] font-bold text-[#342e28]">{{ count($items) }} {{ count($items) === 1 ? 'item' : 'items' }}</h2>
            </div>
            <span class="text-[11px] font-semibold text-[#91867b]">{{ $order->payment_method }} · {{ ucfirst($order->payment_status) }}</span>
        </div>

        <div class="divide-y divide-[#eee7dd]">
            @forelse ($items as $item)
                <div class="flex items-start justify-between gap-4 py-4">
                    <div class="flex min-w-0 items-start gap-3">
                        <div class="grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-xl border border-[#eee7de] bg-[#faf8f4] text-[#aa9f93]">
                            @if (!empty($item['image']))
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] ?? 'Product' }}" class="h-full w-full object-cover">
                            @else
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                    <path d="M4 7.5L12 3l8 4.5v9L12 21l-8-4.5v-9z" stroke-linejoin="round"/>
                                    <path d="M4.5 7.7L12 12l7.5-4.3M12 12v9" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            @endif
                        </div>

                        <div class="min-w-0 pt-0.5">
                            <p class="truncate text-[13px] font-bold text-[#4b443d]">{{ $item['name'] ?? 'Product' }}</p>
                            <p class="mt-1 text-[11px] text-[#92887c]">
                                {{ $item['variant_label'] ?? 'Standard' }}
                                <span class="mx-1">•</span>
                                Qty {{ $item['qty'] ?? 1 }}
                            </p>
                        </div>
                    </div>

                    <span class="shrink-0 pt-1 text-[13px] font-bold text-[#342e28]">
                        ₱{{ number_format((float) ($item['line_total'] ?? (($item['price'] ?? 0) * ($item['qty'] ?? 1))), 2) }}
                    </span>
                </div>
            @empty
                <div class="py-8 text-center">
                    <p class="text-[12px] text-[#958b80]">No order items found.</p>
                </div>
            @endforelse
        </div>
    </section>

    <div class="sari-soft-panel-sm mt-5 flex items-start gap-3 rounded-[20px] border border-[#efe2ca] bg-[#fffaf1] p-4 sm:p-5">
        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#fff1d2] text-[#b67813]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M12 3l7 3v5c0 4.6-2.9 8.1-7 10-4.1-1.9-7-5.4-7-10V6l7-3z" stroke-linejoin="round"/>
                <path d="M9 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div>
            <p class="text-[12px] font-bold text-[#5a4b32]">Delivery reminder</p>
            <p class="mt-1 text-[11px] leading-4 text-[#8c795a]">Please inspect your order upon delivery. If there are any issues, contact the seller through Messages.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/buyer-orders.js')
@endpush
