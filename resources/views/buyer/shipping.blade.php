@extends('layouts.buyer')

@section('title', 'Shipping — SARI')
@section('page-title', 'Shipping')

@section('content')

@include('components.buyer.header')

<main
    class="mx-auto w-full max-w-[1400px] px-4 py-5 sm:px-6 lg:px-8"
    style="font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;"
>
    <style>
        /* ============================================================
           SHIPPING PAGE — CART / ORDERS VISUAL SYSTEM
        ============================================================ */
        .sari-shipping-header {
            padding: 2px 2px 14px;
        }

        .sari-shipping-eyebrow {
            color: #a87316;
            font-size: 10.5px;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .sari-shipping-title {
            margin-top: 5px;
            color: #2e343b;
            font-size: clamp(27px, 2.25vw, 31px);
            font-weight: 760;
            line-height: 1.04;
            letter-spacing: -.035em;
        }

        .sari-shipping-orders-link {
            position: relative;
            top: -5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 0;
            background: transparent;
            padding: 0;
            color: #59636e;
            font-size: 11.5px;
            font-weight: 750;
            line-height: 1.35;
            text-decoration: none;
            transition: color .14s ease;
        }

        .sari-shipping-orders-link:hover,
        .sari-shipping-orders-link:focus-visible {
            color: #c88912;
            outline: none;
        }

        .sari-shipping-orders-link span {
            display: inline-block;
            color: currentColor;
            font-size: 18px;
            font-weight: 800;
            line-height: .8;
            transform: translateY(-1px);
        }

        /* Empty state mirrors the Buyer Cart / My Orders empty UI. */
        .sari-shipping-empty {
            border: 1px solid #e3e7eb !important;
            border-radius: 16px !important;
            background: #ffffff !important;
            padding: 72px 24px !important;
            text-align: center;
            box-shadow: 0 10px 28px rgba(31,41,55,.045) !important;
        }

        .sari-shipping-empty-icon {
            display: grid;
            width: 92px;
            height: 92px;
            margin-inline: auto;
            place-items: center;
            border: 0;
            background: transparent;
            color: #b4bbc3;
        }

        .sari-shipping-empty-icon svg {
            width: 58px;
            height: 58px;
            color: #b4bbc3;
            stroke: currentColor;
            stroke-width: 1.45;
        }

        .sari-shipping-empty-title {
            margin-top: 18px;
            color: #9ca4ad;
            font-size: 22px;
            font-weight: 750;
            line-height: 1.2;
            letter-spacing: -.025em;
        }

        .sari-shipping-empty-copy {
            margin-top: 7px;
            color: #b0b7bf;
            font-size: 12px;
            font-weight: 500;
            line-height: 1.55;
        }

        .sari-shipping-empty-button {
            display: inline-flex;
            min-width: 170px;
            height: 48px;
            margin-top: 28px;
            align-items: center;
            justify-content: center;
            border: 1px solid #c88912;
            border-radius: 11px;
            background: #c88912;
            padding: 0 26px;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            line-height: 1;
            text-decoration: none;
            box-shadow: none;
            transition: background-color .14s ease, border-color .14s ease;
        }

        .sari-shipping-empty-button:hover,
        .sari-shipping-empty-button:focus-visible {
            border-color: #ad760f;
            background: #ad760f;
            color: #ffffff;
            outline: none;
        }

        /* Existing shipment cards stay functional; only light UI cleanup. */
        .sari-shipment-card {
            border: 1px solid #e3e7eb !important;
            background: #ffffff !important;
            box-shadow:
                0 10px 28px rgba(31,41,55,.045),
                0 2px 7px rgba(31,41,55,.018) !important;
        }

        @media (max-width: 640px) {
            .sari-shipping-eyebrow {
                font-size: 9.5px;
            }

            .sari-shipping-title {
                font-size: 29px;
            }

            .sari-shipping-orders-link {
                top: 0;
                font-size: 10.5px;
            }

            .sari-shipping-orders-link span {
                font-size: 16px;
            }

            .sari-shipping-empty {
                padding: 56px 20px !important;
            }

            .sari-shipping-empty-icon {
                width: 64px;
                height: 64px;
            }

            .sari-shipping-empty-icon svg {
                width: 40px;
                height: 40px;
            }

            .sari-shipping-empty-title {
                font-size: 20px;
            }

            .sari-shipping-empty-copy {
                font-size: 11px;
            }

            .sari-shipping-empty-button {
                min-width: 160px;
                height: 46px;
                font-size: 10.5px;
            }
        }
    </style>

    {{-- PAGE HEADER --}}
    <section class="sari-shipping-header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="sari-shipping-eyebrow">Delivery center</p>
                <h1 class="sari-shipping-title">Shipping</h1>
            </div>

            <a href="{{ route('buyer.orders') }}" class="sari-shipping-orders-link">
                View all orders
                <span aria-hidden="true">&gt;</span>
            </a>
        </div>
    </section>

    <div class="mt-5 space-y-4">
        @forelse ($shipments as $order)
            @php
                $progress = $activity->shippingProgress($order);
                $sellerSlug = 'seller-' . (int) $order->seller_account_id;
                $items = collect($order->items ?? []);
            @endphp

            <article class="sari-shipment-card rounded-[20px] p-5 sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <a
                                href="{{ route('buyer.orders.show', ['order' => $order->id]) }}"
                                class="text-[15px] font-bold text-[#2e2923] transition hover:text-[#ad7204]"
                            >
                                {{ $order->order_number }}
                            </a>

                            <span class="rounded-full bg-[#fff2d6] px-2.5 py-1 text-[9px] font-bold uppercase tracking-[.09em] text-[#a66f0f]">
                                {{ $activity->shippingStatusLabel($order) }}
                            </span>
                        </div>

                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-[10px] text-[#81776c]">
                            <a
                                href="{{ route('buyer.shop', ['shop' => $sellerSlug]) }}"
                                class="font-semibold text-[#655b50] transition hover:text-[#b67813]"
                            >
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
                                <div
                                    class="h-full rounded-full bg-[#d99a18] transition-all"
                                    style="width: {{ $progress }}%"
                                ></div>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-[#faf8f4] px-3 py-3">
                                <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#a1988c]">Courier</p>
                                <p class="mt-1 truncate text-[10px] font-semibold text-[#463f37]">
                                    {{ $order->courier_name ?: 'Waiting for assignment' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-[#faf8f4] px-3 py-3 sm:col-span-2">
                                <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#a1988c]">Delivery address</p>
                                <p class="mt-1 line-clamp-2 text-[10px] font-medium leading-4 text-[#5f574f]">
                                    {{ $order->buyer_address ?: 'Delivery address unavailable' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-wrap gap-2 lg:max-w-[190px] lg:flex-col">
                        <a
                            href="{{ route('buyer.orders.show', ['order' => $order->id]) }}"
                            class="inline-flex h-10 items-center justify-center rounded-xl bg-[#d9930a] px-4 text-[9px] font-bold text-white transition hover:bg-[#bf7f08]"
                        >
                            Track order
                        </a>

                        <a
                            href="{{ route('buyer.shop', ['shop' => $sellerSlug]) }}"
                            class="inline-flex h-10 items-center justify-center rounded-xl border border-[#dfd4c5] bg-white px-4 text-[9px] font-semibold text-[#665c50] transition hover:bg-[#fff9ed] hover:text-[#ad7204]"
                        >
                            View seller
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <section class="sari-shipping-empty">
                <span class="sari-shipping-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M3 7h11v9H3z"></path>
                        <path d="M14 10h4l3 3v3h-7z"></path>
                        <circle cx="7" cy="18" r="1.5"></circle>
                        <circle cx="18" cy="18" r="1.5"></circle>
                    </svg>
                </span>

                <h2 class="sari-shipping-empty-title">No shipments yet</h2>

                <p class="sari-shipping-empty-copy">
                    Orders appear here once they reach the shipping workflow.
                </p>

                <a href="{{ route('buyer.products') }}" class="sari-shipping-empty-button">
                    Shop products
                </a>
            </section>
        @endforelse
    </div>

    @if ($shipments->hasPages())
        <div class="mt-5">{{ $shipments->links() }}</div>
    @endif
</main>

@endsection
