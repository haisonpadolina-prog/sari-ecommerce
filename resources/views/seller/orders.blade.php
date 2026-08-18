@extends('layouts.seller')

@section('title', 'Order Management — SARI Seller')
@section('page-title', 'Order Management')

@section('content')

<div class="mx-auto w-full max-w-[1800px]">

    {{-- =========================================================
        PAGE INTRO
    ========================================================== --}}
    <section class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6 lg:p-7">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-[#d9e6dd] bg-[#f3f8f5] px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#56816a]">
                    <span class="h-2 w-2 rounded-full bg-[#68a07b]"></span>
                    Fulfillment Center
                </div>

                <h2 class="mt-3 text-[22px] font-bold tracking-[-0.03em] text-[#211c16] sm:text-[24px]">
                    Order Management
                </h2>

                <p class="mt-2 max-w-[800px] text-[12px] leading-6 text-[#81786c] sm:text-[13px]">
                    Manage inventory, review new orders, prepare packages, print waybills,
                    hand orders to couriers, track delivery, and handle customer feedback.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button
                    id="orderPrintWaybillTop"
                    type="button"
                    class="inline-flex h-11 items-center gap-2 rounded-xl border border-[#e6dfd4] bg-white px-4 text-[12px] font-semibold text-[#62594e] transition hover:border-[#d4c29f] hover:bg-[#fcf9f3]"
                >
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M6 9V4h12v5"></path>
                        <rect x="5" y="13" width="14" height="7" rx="1"></rect>
                        <path d="M4 9h16a2 2 0 0 1 2 2v4h-3"></path>
                        <path d="M5 15H2v-4a2 2 0 0 1 2-2"></path>
                    </svg>
                    Print Waybill
                </button>

                <button
                    id="openInventoryModal"
                    type="button"
                    class="inline-flex h-11 items-center gap-2 rounded-xl bg-[#c99128] px-4 text-[12px] font-semibold text-white transition hover:bg-[#b47e1e]"
                >
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>
                    Add Product
                </button>
            </div>
        </div>
    </section>


    {{-- =========================================================
        SUMMARY CARDS
    ========================================================== --}}
    <section class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-[18px] border border-[#eadfc9] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[12px] font-medium text-[#797168]">New Orders</p>
                    <h3 class="mt-2 text-[27px] font-bold tracking-[-0.04em] text-[#211d18]">8</h3>
                    <p class="mt-2 text-[11px] text-[#ad781c]">Needs review</p>
                </div>
                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#eadfc8] bg-[#fbf6ec] text-[#b98020]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M5 7h14l-1 13H6L5 7Z"></path>
                        <path d="M9 7a3 3 0 0 1 6 0"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-[18px] border border-[#dce5ed] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[12px] font-medium text-[#797168]">Preparing</p>
                    <h3 class="mt-2 text-[27px] font-bold tracking-[-0.04em] text-[#211d18]">9</h3>
                    <p class="mt-2 text-[11px] text-[#6f8294]">Packing in progress</p>
                </div>
                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#d9e3ec] bg-[#f3f7fa] text-[#627f99]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M4 7h16v12H4z"></path>
                        <path d="m4 7 8 5 8-5"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-[18px] border border-[#e5dfea] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[12px] font-medium text-[#797168]">In Transit</p>
                    <h3 class="mt-2 text-[27px] font-bold tracking-[-0.04em] text-[#211d18]">12</h3>
                    <p class="mt-2 text-[11px] text-[#7c6a8c]">With courier</p>
                </div>
                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#e0d9e6] bg-[#f6f2f8] text-[#806a91]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M3 7h11v10H3z"></path>
                        <path d="M14 10h4l3 3v4h-7z"></path>
                        <circle cx="7" cy="18" r="2"></circle>
                        <circle cx="18" cy="18" r="2"></circle>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-[18px] border border-[#d8e6dd] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[12px] font-medium text-[#797168]">Delivered Today</p>
                    <h3 class="mt-2 text-[27px] font-bold tracking-[-0.04em] text-[#211d18]">31</h3>
                    <p class="mt-2 text-[11px] font-medium text-[#56816a]">94% successful delivery</p>
                </div>
                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#d5e5dc] bg-[#f1f7f3] text-[#56816a]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <circle cx="12" cy="12" r="8"></circle>
                        <path d="m8.5 12 2.2 2.2 4.8-5"></path>
                    </svg>
                </div>
            </div>
        </div>
    </section>


    {{-- =========================================================
        SELLER FULFILLMENT WORKFLOW
    ========================================================== --}}
    <section class="mt-5 overflow-hidden rounded-[24px] border border-[#e8e1d7] bg-white shadow-[0_10px_30px_rgba(61,48,28,0.04)]">

        {{-- Workflow Header --}}
        <div class="flex flex-col gap-4 border-b border-[#eee8df] bg-[#fffdf9] px-5 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-4">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl border border-[#eadbbc] bg-[#fbf5e9] text-[#b47e1e]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7h5"></path>
                        <path d="M15 7h5"></path>
                        <path d="M9 7h6"></path>
                        <circle cx="7" cy="7" r="2"></circle>
                        <circle cx="17" cy="7" r="2"></circle>
                        <path d="M7 9v7"></path>
                        <path d="M17 9v7"></path>
                        <path d="M7 16h10"></path>
                    </svg>
                </div>

                <div>
                    <h3 class="text-[17px] font-bold tracking-[-0.02em] text-[#28221b]">
                        Seller Fulfillment Workflow
                    </h3>

                    <p class="mt-1.5 max-w-[720px] text-[12px] leading-5 text-[#877e73]">
                        Track every order from customer checkout up to delivery confirmation and post-purchase feedback.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-2 rounded-full border border-[#d7e5dc] bg-[#f3f8f5] px-3 py-1.5 text-[11px] font-semibold text-[#56816a]">
                    <span class="h-2 w-2 rounded-full bg-[#68a07b]"></span>
                    60 active orders
                </span>

                <span class="rounded-full border border-[#e7dfd4] bg-white px-3 py-1.5 text-[11px] font-semibold text-[#756d63]">
                    7 fulfillment stages
                </span>
            </div>
        </div>


        {{-- Workflow Stages --}}
        <div class="relative px-5 py-6 sm:px-6 lg:px-7">

            {{-- Desktop connector --}}
            <div class="absolute left-[8%] right-[8%] top-[62px] hidden h-px bg-[#e4ddd3] xl:block"></div>

            <div class="relative grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-7">

                @php
                    $flow = [
                        [
                            'step' => '01',
                            'name' => 'New Orders',
                            'count' => 8,
                            'description' => 'Review buyer and payment details',
                            'iconBg' => 'bg-[#fbf5e9]',
                            'iconText' => 'text-[#ad791f]',
                            'badgeBg' => 'bg-[#fff7e7]',
                            'badgeText' => 'text-[#9c6a15]',
                            'icon' => 'bag'
                        ],
                        [
                            'step' => '02',
                            'name' => 'Prepare',
                            'count' => 9,
                            'description' => 'Pack and verify ordered items',
                            'iconBg' => 'bg-[#f3f7fa]',
                            'iconText' => 'text-[#647f97]',
                            'badgeBg' => 'bg-[#f2f6f9]',
                            'badgeText' => 'text-[#61798f]',
                            'icon' => 'box'
                        ],
                        [
                            'step' => '03',
                            'name' => 'Waybill',
                            'count' => 6,
                            'description' => 'Print shipping label and documents',
                            'iconBg' => 'bg-[#f7f4ef]',
                            'iconText' => 'text-[#756d64]',
                            'badgeBg' => 'bg-[#f6f2ec]',
                            'badgeText' => 'text-[#756d64]',
                            'icon' => 'document'
                        ],
                        [
                            'step' => '04',
                            'name' => 'Courier Pickup',
                            'count' => 6,
                            'description' => 'Schedule and confirm handover',
                            'iconBg' => 'bg-[#f6f2f8]',
                            'iconText' => 'text-[#7c6a8c]',
                            'badgeBg' => 'bg-[#f5f1f8]',
                            'badgeText' => 'text-[#786786]',
                            'icon' => 'truck'
                        ],
                        [
                            'step' => '05',
                            'name' => 'In Transit',
                            'count' => 12,
                            'description' => 'Monitor shipment movement',
                            'iconBg' => 'bg-[#f1f5f8]',
                            'iconText' => 'text-[#62798d]',
                            'badgeBg' => 'bg-[#f1f5f8]',
                            'badgeText' => 'text-[#62798d]',
                            'icon' => 'route'
                        ],
                        [
                            'step' => '06',
                            'name' => 'Delivered',
                            'count' => 31,
                            'description' => 'Customer received the order',
                            'iconBg' => 'bg-[#eef6f1]',
                            'iconText' => 'text-[#56816a]',
                            'badgeBg' => 'bg-[#edf6f0]',
                            'badgeText' => 'text-[#56816a]',
                            'icon' => 'check'
                        ],
                        [
                            'step' => '07',
                            'name' => 'Feedback',
                            'count' => 14,
                            'description' => 'Review ratings and buyer comments',
                            'iconBg' => 'bg-[#fbf7ee]',
                            'iconText' => 'text-[#917549]',
                            'badgeBg' => 'bg-[#fbf7ee]',
                            'badgeText' => 'text-[#8f7346]',
                            'icon' => 'star'
                        ],
                    ];
                @endphp


                @foreach($flow as $step)
                    <div class="group relative">

                        <div class="relative z-10 flex h-full flex-col rounded-[18px] border border-[#e9e2d8] bg-white p-4 transition-all duration-200 hover:-translate-y-0.5 hover:border-[#d8c49e] hover:shadow-[0_12px_26px_rgba(71,55,32,0.07)]">

                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[11px] font-bold tracking-[0.12em] text-[#a59c91]">
                                    STEP {{ $step['step'] }}
                                </span>

                                <span class="rounded-full {{ $step['badgeBg'] }} px-2.5 py-1 text-[11px] font-bold {{ $step['badgeText'] }}">
                                    {{ $step['count'] }}
                                </span>
                            </div>


                            <div class="mt-4 flex items-center gap-3 xl:block">
                                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-black/[0.035] {{ $step['iconBg'] }} {{ $step['iconText'] }} xl:mx-auto">

                                    @if($step['icon'] === 'bag')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M5 7h14l-1 13H6L5 7Z"></path>
                                            <path d="M9 7a3 3 0 0 1 6 0"></path>
                                        </svg>
                                    @elseif($step['icon'] === 'box')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="m4 7 8-4 8 4-8 4-8-4Z"></path>
                                            <path d="M4 7v10l8 4 8-4V7"></path>
                                            <path d="M12 11v10"></path>
                                        </svg>
                                    @elseif($step['icon'] === 'document')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M6 3h9l4 4v14H6z"></path>
                                            <path d="M15 3v5h5"></path>
                                            <path d="M9 13h6"></path>
                                            <path d="M9 17h5"></path>
                                        </svg>
                                    @elseif($step['icon'] === 'truck')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M3 7h11v10H3z"></path>
                                            <path d="M14 10h4l3 3v4h-7z"></path>
                                            <circle cx="7" cy="18" r="2"></circle>
                                            <circle cx="18" cy="18" r="2"></circle>
                                        </svg>
                                    @elseif($step['icon'] === 'route')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <circle cx="6" cy="18" r="2"></circle>
                                            <circle cx="18" cy="6" r="2"></circle>
                                            <path d="M8 18h3a3 3 0 0 0 3-3v-1a3 3 0 0 1 3-3h1"></path>
                                        </svg>
                                    @elseif($step['icon'] === 'check')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <circle cx="12" cy="12" r="8"></circle>
                                            <path d="m8.5 12 2.2 2.2 4.8-5"></path>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="m12 3 2.5 5 5.5.8-4 3.9.9 5.5-4.9-2.6-4.9 2.6.9-5.5-4-3.9 5.5-.8L12 3Z"></path>
                                        </svg>
                                    @endif
                                </div>

                                <div class="min-w-0 xl:mt-4 xl:text-center">
                                    <h4 class="text-[12px] font-bold leading-5 text-[#3f3830]">
                                        {{ $step['name'] }}
                                    </h4>

                                    <p class="mt-1 text-[11px] leading-4 text-[#8f867a]">
                                        {{ $step['description'] }}
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>
        </div>


        {{-- Workflow Footer --}}
        <div class="flex flex-col gap-3 border-t border-[#eee8df] bg-[#fcfaf7] px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <p class="text-[11px] leading-5 text-[#837a6f]">
                Orders automatically move through each stage as the seller and courier complete fulfillment actions.
            </p>

            <button
                type="button"
                class="inline-flex items-center gap-2 text-[11px] font-semibold text-[#a8731f] transition hover:text-[#805513]"
            >
                View fulfillment guide
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="m9 18 6-6-6-6"></path>
                </svg>
            </button>
        </div>
    </section>


    {{-- =========================================================
        MAIN MANAGEMENT PANEL
    ========================================================== --}}
    <section class="mt-5 overflow-hidden rounded-[22px] border border-[#ebe4da] bg-white">

        {{-- TABS --}}
        <div class="border-b border-[#eee8df] px-4 pt-4 sm:px-5">
            <div class="flex gap-2 overflow-x-auto pb-4">
                <button data-order-tab="orders" class="order-tab rounded-xl bg-[#c99128] px-5 py-3 text-[11px] font-semibold text-white shadow-sm">
                    Orders
                </button>

                <button data-order-tab="inventory" class="order-tab rounded-xl border border-[#e4ddd3] bg-white px-5 py-3 text-[11px] font-semibold text-[#6e665b] transition hover:border-[#d6c5a8] hover:bg-[#fcf8f1]">
                    Inventory
                </button>

                <button data-order-tab="shipments" class="order-tab rounded-xl border border-[#e4ddd3] bg-white px-5 py-3 text-[11px] font-semibold text-[#6e665b] transition hover:border-[#d6c5a8] hover:bg-[#fcf8f1]">
                    Shipments
                </button>

                <button data-order-tab="feedback" class="order-tab rounded-xl border border-[#e4ddd3] bg-white px-5 py-3 text-[11px] font-semibold text-[#6e665b] transition hover:border-[#d6c5a8] hover:bg-[#fcf8f1]">
                    Customer Feedback
                </button>
            </div>
        </div>


        {{-- =====================================================
            ORDERS TAB
        ====================================================== --}}
        <div id="orderPanel-orders" data-order-panel>
            <div class="flex flex-col gap-4 border-b border-[#eee8df] p-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h3 class="text-[15px] font-bold text-[#28221b]">Incoming & Active Orders</h3>
                    <p class="mt-1 text-[11px] text-[#91887d]">Review, prepare, and process customer orders.</p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <div class="relative">
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9e968b]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg>

                        <input
                            id="sellerOrderSearch"
                            type="search"
                            placeholder="Search order or buyer..."
                            class="h-10 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] pl-10 pr-4 text-[11px] outline-none focus:border-[#c99a3d] focus:ring-4 focus:ring-[#c99a3d]/10 sm:w-[230px]"
                        >
                    </div>

                    <select
                        id="sellerOrderStatus"
                        class="h-10 rounded-xl border border-[#e6dfd5] bg-white px-3 text-[11px] text-[#625a50] outline-none"
                    >
                        <option value="">All Status</option>
                        <option value="new">New Order</option>
                        <option value="preparing">Preparing</option>
                        <option value="pickup">Ready Pickup</option>
                        <option value="transit">In Transit</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>
            </div>


            <div class="overflow-x-auto">
                <table class="w-full min-w-[1120px] text-left">
                    <thead>
                        <tr class="border-b border-[#eee8df] bg-[#fcfaf7] text-[11px] font-semibold uppercase tracking-[0.08em] text-[#9b9287]">
                            <th class="px-5 py-4">Order</th>
                            <th class="px-5 py-4">Buyer</th>
                            <th class="px-5 py-4">Items</th>
                            <th class="px-5 py-4">Payment</th>
                            <th class="px-5 py-4">Total</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Courier</th>
                            <th class="px-5 py-4 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody id="sellerOrderRows" class="text-[11px]">
                        <tr data-order-row data-order-search="#ORD-8452 Maria Santos" data-order-status="new" class="border-b border-[#f1ece5] hover:bg-[#fdfbf8]">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#403a33]">#ORD-8452</p>
                                <p class="mt-1 text-[11px] text-[#958c80]">Aug 18, 10:20 AM</p>
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#4e473f]">Maria Santos</p>
                                <p class="mt-1 text-[11px] text-[#958c80]">Santa Rosa, Laguna</p>
                            </td>

                            <td class="px-5 py-4 text-[#625a50]">3 items</td>

                            <td class="px-5 py-4">
                                <span class="rounded-full bg-[#eef6f1] px-2 py-1 text-[11px] font-semibold text-[#56816a]">Paid</span>
                            </td>

                            <td class="px-5 py-4 font-semibold text-[#403a33]">₱2,480</td>

                            <td class="px-5 py-4">
                                <span class="rounded-full bg-[#fbf3e7] px-2.5 py-1.5 text-[11px] font-semibold text-[#a97724]">New Order</span>
                            </td>

                            <td class="px-5 py-4 text-[#8b8277]">Not assigned</td>

                            <td class="px-5 py-4 text-right">
                                <button data-order-action="prepare" class="rounded-lg bg-[#c99128] px-3 py-2 text-[11px] font-semibold text-white hover:bg-[#b47e1e]">
                                    Prepare
                                </button>
                            </td>
                        </tr>


                        <tr data-order-row data-order-search="#ORD-8451 Juan Dela Cruz" data-order-status="preparing" class="border-b border-[#f1ece5] hover:bg-[#fdfbf8]">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#403a33]">#ORD-8451</p>
                                <p class="mt-1 text-[11px] text-[#958c80]">Aug 18, 9:42 AM</p>
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#4e473f]">Juan Dela Cruz</p>
                                <p class="mt-1 text-[11px] text-[#958c80]">Biñan, Laguna</p>
                            </td>

                            <td class="px-5 py-4 text-[#625a50]">1 item</td>

                            <td class="px-5 py-4">
                                <span class="rounded-full bg-[#eef6f1] px-2 py-1 text-[11px] font-semibold text-[#56816a]">Paid</span>
                            </td>

                            <td class="px-5 py-4 font-semibold text-[#403a33]">₱899</td>

                            <td class="px-5 py-4">
                                <span class="rounded-full bg-[#f3f7fa] px-2.5 py-1.5 text-[11px] font-semibold text-[#647f97]">Preparing</span>
                            </td>

                            <td class="px-5 py-4 text-[#8b8277]">Pending pickup</td>

                            <td class="px-5 py-4 text-right">
                                <button data-order-action="waybill" class="rounded-lg border border-[#e4ddd3] px-3 py-2 text-[11px] font-semibold text-[#675f55] hover:bg-[#fcf8f1]">
                                    Print Waybill
                                </button>
                            </td>
                        </tr>


                        <tr data-order-row data-order-search="#ORD-8450 Ana Garcia" data-order-status="pickup" class="border-b border-[#f1ece5] hover:bg-[#fdfbf8]">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#403a33]">#ORD-8450</p>
                                <p class="mt-1 text-[11px] text-[#958c80]">Aug 18, 8:30 AM</p>
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#4e473f]">Ana Garcia</p>
                                <p class="mt-1 text-[11px] text-[#958c80]">Calamba, Laguna</p>
                            </td>

                            <td class="px-5 py-4 text-[#625a50]">2 items</td>

                            <td class="px-5 py-4">
                                <span class="rounded-full bg-[#fbf3e7] px-2 py-1 text-[11px] font-semibold text-[#a97724]">COD</span>
                            </td>

                            <td class="px-5 py-4 font-semibold text-[#403a33]">₱1,560</td>

                            <td class="px-5 py-4">
                                <span class="rounded-full bg-[#eef6f1] px-2.5 py-1.5 text-[11px] font-semibold text-[#56816a]">Ready Pickup</span>
                            </td>

                            <td class="px-5 py-4 text-[#625a50]">Pedro Reyes</td>

                            <td class="px-5 py-4 text-right">
                                <button data-order-action="handover" class="rounded-lg border border-[#ddd5e5] bg-[#f7f4f9] px-3 py-2 text-[11px] font-semibold text-[#796989]">
                                    Handover
                                </button>
                            </td>
                        </tr>


                        <tr data-order-row data-order-search="#ORD-8449 Luis Mendoza" data-order-status="transit" class="border-b border-[#f1ece5] hover:bg-[#fdfbf8]">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#403a33]">#ORD-8449</p>
                                <p class="mt-1 text-[11px] text-[#958c80]">Aug 17, 5:22 PM</p>
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#4e473f]">Luis Mendoza</p>
                                <p class="mt-1 text-[11px] text-[#958c80]">Cabuyao, Laguna</p>
                            </td>

                            <td class="px-5 py-4 text-[#625a50]">4 items</td>

                            <td class="px-5 py-4">
                                <span class="rounded-full bg-[#eef6f1] px-2 py-1 text-[11px] font-semibold text-[#56816a]">Paid</span>
                            </td>

                            <td class="px-5 py-4 font-semibold text-[#403a33]">₱3,280</td>

                            <td class="px-5 py-4">
                                <span class="rounded-full bg-[#f6f2f8] px-2.5 py-1.5 text-[11px] font-semibold text-[#7c6a8c]">In Transit</span>
                            </td>

                            <td class="px-5 py-4 text-[#625a50]">Flash Express</td>

                            <td class="px-5 py-4 text-right">
                                <button data-order-action="track" class="rounded-lg border border-[#e4ddd3] px-3 py-2 text-[11px] font-semibold text-[#675f55] hover:bg-[#fcf8f1]">
                                    Track
                                </button>
                            </td>
                        </tr>


                        <tr data-order-row data-order-search="#ORD-8448 Rosa Cruz" data-order-status="delivered" class="hover:bg-[#fdfbf8]">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#403a33]">#ORD-8448</p>
                                <p class="mt-1 text-[11px] text-[#958c80]">Aug 17, 2:12 PM</p>
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#4e473f]">Rosa Cruz</p>
                                <p class="mt-1 text-[11px] text-[#958c80]">San Pedro, Laguna</p>
                            </td>

                            <td class="px-5 py-4 text-[#625a50]">2 items</td>

                            <td class="px-5 py-4">
                                <span class="rounded-full bg-[#eef6f1] px-2 py-1 text-[11px] font-semibold text-[#56816a]">Paid</span>
                            </td>

                            <td class="px-5 py-4 font-semibold text-[#403a33]">₱1,150</td>

                            <td class="px-5 py-4">
                                <span class="rounded-full bg-[#eef6f1] px-2.5 py-1.5 text-[11px] font-semibold text-[#56816a]">Delivered</span>
                            </td>

                            <td class="px-5 py-4 text-[#625a50]">J&T Express</td>

                            <td class="px-5 py-4 text-right">
                                <button data-order-action="feedback" class="rounded-lg border border-[#e4ddd3] px-3 py-2 text-[11px] font-semibold text-[#675f55] hover:bg-[#fcf8f1]">
                                    Feedback
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


            <div id="sellerOrderEmpty" class="hidden border-t border-[#eee8df] p-8 text-center">
                <p class="text-[12px] font-semibold text-[#5d554c]">No matching orders found.</p>
                <p class="mt-1 text-[11px] text-[#948b7f]">Try changing the search or status filter.</p>
            </div>

            <div class="flex items-center justify-between border-t border-[#eee8df] bg-[#fcfaf7] px-5 py-4">
                <p class="text-[11px] text-[#91887d]">Showing 5 of 60 active orders</p>
                <div class="flex gap-1.5">
                    <button class="grid h-8 w-8 place-items-center rounded-lg border border-[#e4ddd3] bg-white text-[11px] text-[#777065]">1</button>
                    <button class="grid h-8 w-8 place-items-center rounded-lg border border-[#e4ddd3] bg-white text-[11px] text-[#777065]">2</button>
                    <button class="grid h-8 w-8 place-items-center rounded-lg border border-[#e4ddd3] bg-white text-[11px] text-[#777065]">3</button>
                </div>
            </div>
        </div>


        {{-- =====================================================
            INVENTORY TAB
        ====================================================== --}}
        <div id="orderPanel-inventory" data-order-panel class="hidden">
            <div class="flex flex-col gap-4 border-b border-[#eee8df] p-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h3 class="text-[15px] font-bold text-[#28221b]">Product Inventory</h3>
                    <p class="mt-1 text-[11px] text-[#91887d]">Update stock, prices, discounts, vouchers, or archive products.</p>
                </div>

                <button id="inventoryAddProduct" type="button" class="inline-flex h-10 items-center gap-2 rounded-xl bg-[#c99128] px-4 text-[11px] font-semibold text-white hover:bg-[#b47e1e]">
                    <span class="text-[14px]">+</span>
                    Add Product
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">
                @php
                    $inventoryItems = [
                        ['name'=>'Wireless Earbuds Pro','sku'=>'EL-10028','price'=>'₱1,299','stock'=>'2','status'=>'Low Stock','statusClass'=>'bg-[#faeeee] text-[#ad5f5f]'],
                        ['name'=>'Classic Canvas Tote Bag','sku'=>'FS-10431','price'=>'₱549','stock'=>'4','status'=>'Low Stock','statusClass'=>'bg-[#fbf3e7] text-[#a97724]'],
                        ['name'=>'Minimal Desk Lamp','sku'=>'HM-10082','price'=>'₱899','stock'=>'35','status'=>'Healthy','statusClass'=>'bg-[#eef6f1] text-[#56816a]'],
                        ['name'=>'Organic Daily Soap Set','sku'=>'BT-10942','price'=>'₱399','stock'=>'58','status'=>'Healthy','statusClass'=>'bg-[#eef6f1] text-[#56816a]'],
                        ['name'=>'Hardbound Journal','sku'=>'BK-10321','price'=>'₱329','stock'=>'18','status'=>'Healthy','statusClass'=>'bg-[#eef6f1] text-[#56816a]'],
                        ['name'=>'Ceramic Coffee Mug','sku'=>'HM-10776','price'=>'₱279','stock'=>'0','status'=>'Out of Stock','statusClass'=>'bg-[#faeeee] text-[#ad5f5f]'],
                    ];
                @endphp

                @foreach($inventoryItems as $product)
                    <article class="rounded-[17px] border border-[#e9e3da] bg-white p-4 transition hover:border-[#d9c8a8]">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-[12px] font-bold text-[#312b25]">{{ $product['name'] }}</p>
                                <p class="mt-1 text-[11px] text-[#978e83]">SKU: {{ $product['sku'] }}</p>
                            </div>

                            <span class="rounded-full px-2 py-1 text-[10px] font-semibold {{ $product['statusClass'] }}">
                                {{ $product['status'] }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-[#fcfaf7] p-3">
                                <p class="text-[10px] text-[#958c80]">Price</p>
                                <p class="mt-1 text-[12px] font-bold text-[#a8731f]">{{ $product['price'] }}</p>
                            </div>

                            <div class="rounded-xl bg-[#fcfaf7] p-3">
                                <p class="text-[10px] text-[#958c80]">Stock</p>
                                <p class="mt-1 text-[12px] font-bold text-[#3d3730]">{{ $product['stock'] }}</p>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-2">
                            <button class="rounded-lg border border-[#e5ded4] px-2 py-2 text-[11px] font-semibold text-[#675f55]">Update</button>
                            <button class="rounded-lg border border-[#e5ded4] px-2 py-2 text-[11px] font-semibold text-[#675f55]">Discount</button>
                            <button class="rounded-lg border border-[#ead8d8] bg-[#fcf5f5] px-2 py-2 text-[11px] font-semibold text-[#a96565]">Archive</button>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>


        {{-- =====================================================
            SHIPMENTS TAB
        ====================================================== --}}
        <div id="orderPanel-shipments" data-order-panel class="hidden">
            <div class="border-b border-[#eee8df] p-5">
                <h3 class="text-[15px] font-bold text-[#28221b]">Courier & Shipment Tracking</h3>
                <p class="mt-1 text-[11px] text-[#91887d]">Monitor courier pickup and current delivery status.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 p-5 xl:grid-cols-2">
                <div class="rounded-[18px] border border-[#e8e2d8] bg-[#fcfbf8] p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[12px] font-bold text-[#312b25]">#ORD-8450</p>
                            <p class="mt-1 text-[11px] text-[#958c80]">Ana Garcia • ₱1,560</p>
                        </div>
                        <span class="rounded-full bg-[#eef6f1] px-2.5 py-1 text-[11px] font-semibold text-[#56816a]">Ready Pickup</span>
                    </div>

                    <div class="mt-4 rounded-[14px] border border-[#e9e3da] bg-white p-4">
                        <div class="flex items-center gap-3">
                            <div class="grid h-10 w-10 place-items-center rounded-xl bg-[#f6f2f8] text-[#7c6a8c]">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M3 7h11v10H3z"></path>
                                    <path d="M14 10h4l3 3v4h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold text-[#4c453d]">Pedro Reyes</p>
                                <p class="mt-1 text-[11px] text-[#958c80]">Pickup today • 1:00 PM - 3:00 PM</p>
                            </div>
                        </div>
                    </div>

                    <button class="mt-4 w-full rounded-xl bg-[#c99128] px-4 py-2.5 text-[11px] font-semibold text-white">
                        Confirm Courier Handover
                    </button>
                </div>


                <div class="rounded-[18px] border border-[#e8e2d8] bg-[#fcfbf8] p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[12px] font-bold text-[#312b25]">#ORD-8449</p>
                            <p class="mt-1 text-[11px] text-[#958c80]">Luis Mendoza • ₱3,280</p>
                        </div>
                        <span class="rounded-full bg-[#f6f2f8] px-2.5 py-1 text-[11px] font-semibold text-[#7c6a8c]">In Transit</span>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="mt-1.5 h-2 w-2 rounded-full bg-[#56816a]"></span>
                            <div>
                                <p class="text-[11px] font-semibold text-[#514a42]">Package picked up</p>
                                <p class="mt-1 text-[10px] text-[#958c80]">Aug 17, 6:10 PM</p>
                            </div>
                        </div>

                        <div class="ml-[3px] h-6 w-px bg-[#ddd6cc]"></div>

                        <div class="flex items-start gap-3">
                            <span class="mt-1.5 h-2 w-2 rounded-full bg-[#c99128]"></span>
                            <div>
                                <p class="text-[11px] font-semibold text-[#514a42]">Arrived at sorting hub</p>
                                <p class="mt-1 text-[10px] text-[#958c80]">Aug 18, 7:45 AM</p>
                            </div>
                        </div>

                        <div class="ml-[3px] h-6 w-px bg-[#ddd6cc]"></div>

                        <div class="flex items-start gap-3">
                            <span class="mt-1.5 h-2 w-2 rounded-full bg-[#d1cac0]"></span>
                            <div>
                                <p class="text-[11px] font-semibold text-[#8f877d]">Out for delivery</p>
                                <p class="mt-1 text-[10px] text-[#aaa197]">Pending</p>
                            </div>
                        </div>
                    </div>

                    <button class="mt-4 w-full rounded-xl border border-[#e0d8cd] bg-white px-4 py-2.5 text-[11px] font-semibold text-[#675f55]">
                        View Full Tracking
                    </button>
                </div>
            </div>
        </div>


        {{-- =====================================================
            FEEDBACK TAB
        ====================================================== --}}
        <div id="orderPanel-feedback" data-order-panel class="hidden">
            <div class="border-b border-[#eee8df] p-5">
                <h3 class="text-[15px] font-bold text-[#28221b]">Customer Feedback</h3>
                <p class="mt-1 text-[11px] text-[#91887d]">Review ratings and respond to buyer feedback.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 p-5 lg:grid-cols-2">
                <article class="rounded-[18px] border border-[#e8e2d8] bg-[#fcfbf8] p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="grid h-10 w-10 place-items-center rounded-full bg-[#f0f7f3] text-[11px] font-bold text-[#5b846a]">RC</div>
                            <div>
                                <p class="text-[11px] font-semibold text-[#3d3730]">Rosa Cruz</p>
                                <p class="mt-1 text-[10px] text-[#958c80]">Order #ORD-8448</p>
                            </div>
                        </div>
                        <span class="text-[12px] text-[#c99128]">★★★★★</span>
                    </div>

                    <p class="mt-4 text-[11px] leading-5 text-[#6c645b]">
                        Items arrived safely and were packed very well. Seller also responded quickly to my questions.
                    </p>

                    <button class="mt-4 rounded-lg border border-[#e2d9cd] bg-white px-3 py-2 text-[11px] font-semibold text-[#675f55]">
                        Reply
                    </button>
                </article>

                <article class="rounded-[18px] border border-[#e8e2d8] bg-[#fcfbf8] p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="grid h-10 w-10 place-items-center rounded-full bg-[#f3f6f8] text-[11px] font-bold text-[#657f94]">JD</div>
                            <div>
                                <p class="text-[11px] font-semibold text-[#3d3730]">Juan Dela Cruz</p>
                                <p class="mt-1 text-[10px] text-[#958c80]">Order #ORD-8434</p>
                            </div>
                        </div>
                        <span class="text-[12px] text-[#c99128]">★★★★☆</span>
                    </div>

                    <p class="mt-4 text-[11px] leading-5 text-[#6c645b]">
                        Product quality was good. Delivery took a little longer than expected but overall satisfied.
                    </p>

                    <button class="mt-4 rounded-lg border border-[#e2d9cd] bg-white px-3 py-2 text-[11px] font-semibold text-[#675f55]">
                        Reply
                    </button>
                </article>
            </div>
        </div>
    </section>


    <div class="h-5"></div>
</div>


{{-- =============================================================
    DEMO ACTION MODAL
============================================================== --}}
<div id="orderActionModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/35 p-4 backdrop-blur-[2px]">
    <div class="w-full max-w-[430px] rounded-[22px] border border-[#e9dfcf] bg-white p-5 shadow-[0_30px_90px_rgba(38,30,18,0.22)] sm:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="inline-flex rounded-full bg-[#fbf5e9] px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.1em] text-[#a8731f]">
                    Seller Action
                </div>

                <h3 id="orderActionTitle" class="mt-3 text-[18px] font-bold text-[#28221b]">
                    Order Updated
                </h3>
            </div>

            <button id="closeOrderActionModal" type="button" class="grid h-9 w-9 place-items-center rounded-xl border border-[#e6dfd5] text-[#756d63]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 6l12 12"></path>
                    <path d="M18 6 6 18"></path>
                </svg>
            </button>
        </div>

        <p id="orderActionMessage" class="mt-4 text-[11px] leading-5 text-[#756d63]">
            This is a frontend demo action.
        </p>

        <button id="orderActionDone" type="button" class="mt-5 w-full rounded-xl bg-[#c99128] px-4 py-3 text-[11px] font-semibold text-white hover:bg-[#b47e1e]">
            Done
        </button>
    </div>
</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | TABS
    |--------------------------------------------------------------------------
    */

    const tabButtons = Array.from(document.querySelectorAll('[data-order-tab]'));
    const panels = Array.from(document.querySelectorAll('[data-order-panel]'));

    function activateTab(tabName) {
        tabButtons.forEach(function(button) {
            const active = button.dataset.orderTab === tabName;

            button.classList.toggle('bg-[#c99128]', active);
            button.classList.toggle('text-white', active);

            button.classList.toggle('border', !active);
            button.classList.toggle('border-[#e4ddd3]', !active);
            button.classList.toggle('bg-white', !active);
            button.classList.toggle('text-[#6e665b]', !active);
        });

        panels.forEach(function(panel) {
            panel.classList.toggle(
                'hidden',
                panel.id !== 'orderPanel-' + tabName
            );
        });
    }

    tabButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            activateTab(this.dataset.orderTab);
        });
    });


    /*
    |--------------------------------------------------------------------------
    | ORDER SEARCH + FILTER
    |--------------------------------------------------------------------------
    */

    const searchInput = document.getElementById('sellerOrderSearch');
    const statusFilter = document.getElementById('sellerOrderStatus');
    const orderRows = Array.from(document.querySelectorAll('[data-order-row]'));
    const orderEmpty = document.getElementById('sellerOrderEmpty');

    function filterOrders() {
        const query = searchInput?.value.trim().toLowerCase() || '';
        const status = statusFilter?.value.toLowerCase() || '';

        let visible = 0;

        orderRows.forEach(function(row) {
            const searchText = (row.dataset.orderSearch || '').toLowerCase();
            const rowStatus = (row.dataset.orderStatus || '').toLowerCase();

            const matchesSearch = query === '' || searchText.includes(query);
            const matchesStatus = status === '' || rowStatus === status;
            const matches = matchesSearch && matchesStatus;

            row.classList.toggle('hidden', !matches);

            if (matches) visible++;
        });

        orderEmpty?.classList.toggle('hidden', visible !== 0);
    }

    searchInput?.addEventListener('input', filterOrders);
    statusFilter?.addEventListener('change', filterOrders);


    /*
    |--------------------------------------------------------------------------
    | DEMO ACTIONS
    |--------------------------------------------------------------------------
    */

    const modal = document.getElementById('orderActionModal');
    const modalTitle = document.getElementById('orderActionTitle');
    const modalMessage = document.getElementById('orderActionMessage');
    const modalClose = document.getElementById('closeOrderActionModal');
    const modalDone = document.getElementById('orderActionDone');

    const actionCopy = {
        prepare: [
            'Order moved to Preparing',
            'The order is now ready for packing. You can print the shipping label once packing is complete.'
        ],
        waybill: [
            'Waybill Ready',
            'A demo shipping label has been prepared. In the real system, this can generate a printable waybill.'
        ],
        handover: [
            'Courier Handover',
            'The order is ready to be handed to the assigned courier. Shipment tracking can begin after pickup.'
        ],
        track: [
            'Shipment Tracking',
            'The package is currently in transit. In the real system, courier tracking updates can appear here.'
        ],
        feedback: [
            'Customer Feedback',
            'The order has been delivered. You can now review the buyer rating and respond to feedback.'
        ]
    };

    function openActionModal(title, message) {
        if (modalTitle) modalTitle.textContent = title;
        if (modalMessage) modalMessage.textContent = message;

        modal?.classList.remove('hidden');
        modal?.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeActionModal() {
        modal?.classList.add('hidden');
        modal?.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-order-action]').forEach(function(button) {
        button.addEventListener('click', function() {
            const action = actionCopy[this.dataset.orderAction];

            if (action) {
                openActionModal(action[0], action[1]);
            }
        });
    });

    document.getElementById('orderPrintWaybillTop')?.addEventListener('click', function() {
        openActionModal(
            'Print Waybill',
            'Select an order in Preparing status to generate and print its shipping label.'
        );
    });

    document.getElementById('openInventoryModal')?.addEventListener('click', function() {
        activateTab('inventory');
        openActionModal(
            'Add Product',
            'The Add Product form can be connected to the product modal from your Seller Dashboard.'
        );
    });

    document.getElementById('inventoryAddProduct')?.addEventListener('click', function() {
        openActionModal(
            'Add Product',
            'This button is ready to connect to your product creation form or database later.'
        );
    });

    modalClose?.addEventListener('click', closeActionModal);
    modalDone?.addEventListener('click', closeActionModal);

    modal?.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeActionModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeActionModal();
        }
    });

});
</script>
@endpush