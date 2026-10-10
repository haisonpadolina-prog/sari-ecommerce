@extends('layouts.buyer')

@section('title', 'Shipping — SARI')
@section('page-title', 'Shipping')

@section('content')
@include('components.buyer.header')

<main class="mx-auto w-full max-w-[1240px] px-4 py-6 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[.16em] text-[#b47b1e]">Delivery center</p>
            <h1 class="mt-1 text-[28px] font-bold tracking-[-.035em] text-[#201b17]">Shipping</h1>
            <p class="mt-1 text-[11px] text-[#8d8377]">Track parcels using the same status managed by Seller, Logistics, and Courier.</p>
        </div>
        <a href="{{ route('buyer.orders') }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-[#e2d8ca] bg-white px-4 text-[10px] font-semibold text-[#61584d] transition hover:border-[#d3b77f] hover:text-[#a96e05]">
            View all orders
        </a>
    </div>

    <div class="mt-6 space-y-4">
        @forelse ($shipments as $order)
            @php
                $progress = $activity->shippingProgress($order);
                $sellerSlug = 'seller-' . (int) $order->seller_account_id;
                $items = collect($order->items ?? []);
            @endphp
            <article class="rounded-[20px] border border-[#ebe3d8] bg-white p-5 shadow-[0_12px_35px_rgba(52,42,28,.04)] sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('buyer.orders.show', ['order' => $order->id]) }}" class="text-[15px] font-bold text-[#2e2923] transition hover:text-[#ad7204]">
                                {{ $order->order_number }}
                            </a>
                            <span class="rounded-full bg-[#fff2d6] px-2.5 py-1 text-[9px] font-bold uppercase tracking-[.09em] text-[#a66f0f]">
                                {{ $activity->shippingStatusLabel($order) }}
                            </span>
                        </div>

                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-[10px] text-[#81776c]">
                            <a href="{{ route('buyer.shop', ['shop' => $sellerSlug]) }}" class="font-semibold text-[#655b50] transition hover:text-[#b67813]">
                                {{ $order->seller?->store_name ?: 'SARI Seller' }}
                            </a>
                            <span>{{ $items->sum(fn ($item) => max(1, (int) ($item['qty'] ?? 1))) }} item(s)</span>
                            <span>₱{{ number_format((float) $order->total, 2) }}</span>
                        </div>

                        <div class="mt-5">
                            <div class="flex items-center justify-between text-[9px] font-semibold text-[#91877c]">
                                <span>Shipment progress</span>
                                <span>{{ $progress }}%</span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-[#eee8df]">
                                <div class="h-full rounded-full bg-[#d99a18] transition-all" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-[#faf8f4] px-3 py-3">
                                <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#a1988c]">Courier</p>
                                <p class="mt-1 truncate text-[10px] font-semibold text-[#463f37]">{{ $order->courier_name ?: 'Waiting for assignment' }}</p>
                            </div>
                            <div class="rounded-xl bg-[#faf8f4] px-3 py-3 sm:col-span-2">
                                <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#a1988c]">Delivery address</p>
                                <p class="mt-1 line-clamp-2 text-[10px] font-medium leading-4 text-[#5f574f]">{{ $order->buyer_address ?: 'Delivery address unavailable' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-wrap gap-2 lg:max-w-[190px] lg:flex-col">
                        <a href="{{ route('buyer.orders.show', ['order' => $order->id]) }}" class="inline-flex h-10 items-center justify-center rounded-xl bg-[#d9930a] px-4 text-[9px] font-bold text-white transition hover:bg-[#bf7f08]">
                            Track order
                        </a>
                        <a href="{{ route('buyer.shop', ['shop' => $sellerSlug]) }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-[#dfd4c5] bg-white px-4 text-[9px] font-semibold text-[#665c50] transition hover:bg-[#fff9ed] hover:text-[#ad7204]">
                            View seller
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-[20px] border border-dashed border-[#ddd2c4] bg-white px-6 py-14 text-center">
                <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#fff4dc] text-[#b67813]">
                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 7h11v9H3z"></path><path d="M14 10h4l3 3v3h-7z"></path><circle cx="7" cy="18" r="1.5"></circle><circle cx="18" cy="18" r="1.5"></circle></svg>
                </div>
                <h2 class="mt-4 text-[14px] font-bold text-[#38312a]">No shipments yet</h2>
                <p class="mt-1 text-[10px] text-[#8c8378]">Orders appear here once they reach the shipping workflow.</p>
                <a href="{{ route('buyer.products') }}" class="mt-4 inline-flex h-10 items-center rounded-xl bg-[#d9930a] px-5 text-[9px] font-bold text-white">Shop products</a>
            </div>
        @endforelse
    </div>

    @if ($shipments->hasPages())
        <div class="mt-5">{{ $shipments->links() }}</div>
    @endif
</main>
@endsection
