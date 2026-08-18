@extends('layouts.admin')

@section('title', 'Seller Compliance — SARI Admin')
@section('page-title', 'Seller Compliance')

@section('content')

<div class="mx-auto w-full max-w-[1800px]">

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <div class="mb-5 rounded-2xl border border-[#cfe2d5] bg-[#f2f8f4] px-5 py-4 text-[13px] font-medium text-[#4f7c60]">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-2xl border border-[#ead0d0] bg-[#fff5f5] px-5 py-4 text-[13px] text-[#a65f5f]">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <section class="rounded-[22px] border border-[#ebe4da] bg-white p-6 shadow-sm sm:p-7">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex items-start gap-4">
                <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl border border-[#ece4d8] bg-[#fcfaf7] text-[#a8731f]">
                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M12 3l7 3v5c0 4.5-2.9 8.2-7 9-4.1-.8-7-4.5-7-9V6l7-3Z"></path>
                        <path d="m9.5 12 1.7 1.7 3.8-4"></path>
                    </svg>
                </div>

                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-[#e8e1d7] bg-[#fcfaf7] px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.12em] text-[#7f766a]">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-[#a8731f]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h16"></path>
                            <path d="M4 12h10"></path>
                            <path d="M4 17h7"></path>
                        </svg>
                        Marketplace Moderation Center
                    </div>

                    <h2 class="mt-3 text-[25px] font-bold tracking-[-0.03em] text-[#211c16]">
                        Seller Compliance
                    </h2>

                    <p class="mt-2 max-w-[820px] text-[14px] leading-7 text-[#81786c]">
                        Review seller uploads and product edits, compare previous versions, inspect automatically flagged listings, issue warnings, suspend repeat violators for 30 days, and respond to seller appeals.
                    </p>
                </div>
            </div>

            <div class="flex max-w-[360px] items-start gap-3 rounded-[18px] border border-[#ebe4da] bg-[#fcfaf7] px-4 py-4">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#e8e1d7] bg-white text-[#8f867b]">
                    <svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 8v4"></path>
                        <path d="M12 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-[#4d463e]">Important</p>
                    <p class="mt-1 text-[11px] leading-5 text-[#8b8277]">
                        Automated screening only flags suspicious listings. Admin review is required before a warning or suspension is applied.
                    </p>
                </div>
            </div>
        </div>
    </section>


    {{-- SUMMARY --}}
    <section class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @php
            $summaryCards = [
                [
                    'label' => 'Total Sellers',
                    'value' => $stats['total_sellers'],
                    'class' => 'border-[#ebe4da] bg-white',
                    'iconBg' => 'bg-[#f5f7fa]',
                    'iconColor' => 'text-[#74879a]',
                    'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path><circle cx="10" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
                ],
                [
                    'label' => 'Under Review',
                    'value' => $stats['under_review'],
                    'class' => 'border-[#ebe4da] bg-white',
                    'iconBg' => 'bg-[#fbf5e9]',
                    'iconColor' => 'text-[#a8731f]',
                    'icon' => '<path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"></path>',
                ],
                [
                    'label' => 'Flagged Products',
                    'value' => $stats['flagged_products'],
                    'class' => 'border-[#ebe4da] bg-white',
                    'iconBg' => 'bg-[#fdf2f2]',
                    'iconColor' => 'text-[#b46868]',
                    'icon' => '<path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path>',
                ],
                [
                    'label' => 'Warnings Issued',
                    'value' => $stats['active_warnings'],
                    'class' => 'border-[#ebe4da] bg-white',
                    'iconBg' => 'bg-[#fcf4ef]',
                    'iconColor' => 'text-[#b77b55]',
                    'icon' => '<path d="M12 2v6"></path><path d="M12 18v4"></path><path d="m4.93 4.93 4.24 4.24"></path><path d="m14.83 14.83 4.24 4.24"></path><path d="M2 12h6"></path><path d="M16 12h6"></path><path d="m4.93 19.07 4.24-4.24"></path><path d="m14.83 9.17 4.24-4.24"></path>',
                ],
                [
                    'label' => 'Suspended Sellers',
                    'value' => $stats['suspended_sellers'],
                    'class' => 'border-[#ebe4da] bg-white',
                    'iconBg' => 'bg-[#f6f3f9]',
                    'iconColor' => 'text-[#806992]',
                    'icon' => '<circle cx="12" cy="12" r="9"></circle><path d="m8 8 8 8"></path>',
                ],
            ];
        @endphp

        @foreach ($summaryCards as $card)
            <div class="rounded-[18px] border {{ $card['class'] }} p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[13px] font-medium text-[#777066]">{{ $card['label'] }}</p>
                        <p class="mt-3 text-[29px] font-bold tracking-[-0.04em] text-[#252019]">{{ $card['value'] }}</p>
                    </div>

                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl {{ $card['iconBg'] }} {{ $card['iconColor'] }}">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            {!! $card['icon'] !!}
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </section>


    {{-- TABS --}}
    <section class="mt-5 overflow-hidden rounded-[22px] border border-[#ebe4da] bg-white">
        <div class="border-b border-[#eee8df] px-5 pt-5">
            <div class="flex gap-2 overflow-x-auto pb-4">
                <button data-compliance-tab="flagged" class="compliance-tab inline-flex items-center gap-2 rounded-xl bg-[#f4efe6] px-4 py-2.5 text-[12px] font-semibold text-[#4f463d]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path>
                    </svg>
                    Flagged Products ({{ $flaggedProducts->count() }})
                </button>
                <button data-compliance-tab="pending" class="compliance-tab inline-flex items-center gap-2 rounded-xl border border-[#e5ddd1] bg-white px-4 py-2.5 text-[12px] font-semibold text-[#675f55]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M8 6h13"></path><path d="M8 12h13"></path><path d="M8 18h13"></path><path d="M3 6h.01"></path><path d="M3 12h.01"></path><path d="M3 18h.01"></path>
                    </svg>
                    Pending Review ({{ $pendingProducts->count() }})
                </button>
                <button data-compliance-tab="warnings" class="compliance-tab inline-flex items-center gap-2 rounded-xl border border-[#e5ddd1] bg-white px-4 py-2.5 text-[12px] font-semibold text-[#675f55]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 2v6"></path><path d="M12 18v4"></path><path d="m4.93 4.93 4.24 4.24"></path><path d="m14.83 14.83 4.24 4.24"></path><path d="M2 12h6"></path><path d="M16 12h6"></path><path d="m4.93 19.07 4.24-4.24"></path><path d="m14.83 9.17 4.24-4.24"></path>
                    </svg>
                    Warning History
                </button>
                <button data-compliance-tab="suspended" class="compliance-tab inline-flex items-center gap-2 rounded-xl border border-[#e5ddd1] bg-white px-4 py-2.5 text-[12px] font-semibold text-[#675f55]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="9"></circle><path d="m8 8 8 8"></path>
                    </svg>
                    Suspended Sellers
                </button>
                <button data-compliance-tab="messages" class="compliance-tab inline-flex items-center gap-2 rounded-xl border border-[#e5ddd1] bg-white px-4 py-2.5 text-[12px] font-semibold text-[#675f55]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    Appeals / Messages
                </button>
            </div>
        </div>


        {{-- FLAGGED PRODUCTS --}}
        <div id="compliancePanel-flagged" data-compliance-panel>
            <div class="border-b border-[#eee8df] p-5">
                <div class="flex items-center gap-3"><div class="grid h-10 w-10 place-items-center rounded-xl border border-[#ece4d8] bg-[#fcfaf7] text-[#a8731f]"><svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path></svg></div><h3 class="text-[17px] font-bold text-[#28221b]">Automatically Flagged Products</h3></div>
                <p class="mt-1 text-[12px] text-[#91887d]">
                    These products matched configured restricted/prohibited keywords. Review the product before taking action.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-4 p-5 xl:grid-cols-2">
                @forelse ($flaggedProducts as $product)
                    <article class="rounded-[19px] border border-[#ebe4da] bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-4 sm:flex-row">
                            <div class="h-28 w-full shrink-0 overflow-hidden rounded-[14px] border border-[#ece5dd] bg-white sm:w-28">
                                @if ($product->image_path)
                                    <img src="{{ route('seller.products.image', $product) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="grid h-full w-full place-items-center text-[#a79d91]">
                                        <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                                            <path d="m4 17 5-5 4 4 2-2 5 4"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p class="text-[15px] font-bold text-[#312b25]">{{ $product->name }}</p>
                                        <p class="mt-1 text-[12px] text-[#81786c]">
                                            {{ $product->seller->store_name ?: $product->seller->email }} • {{ $product->category }}
                                        </p>
                                    </div>

                                    <span class="rounded-full px-3 py-1.5 text-[10px] font-semibold {{ $product->screening_risk === 'high' ? 'bg-[#faeeee] text-[#ad5f5f]' : 'bg-[#fbf3e7] text-[#a97724]' }}">
                                        {{ strtoupper($product->screening_risk) }} RISK
                                    </span>
                                </div>

                                @if ($product->requires_re_review && $product->latestVersion)
                                    <div class="mt-4 rounded-[14px] border border-[#e6dccb] bg-[#fffdf8] p-4">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <p class="text-[11px] font-semibold text-[#8d681f]">Modified After Previous Review</p>
                                            <span class="rounded-full bg-[#fbf2df] px-2.5 py-1 text-[9px] font-semibold text-[#a8731f]">RE-REVIEW</span>
                                        </div>
                                        <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                            <div class="rounded-xl border border-[#eee6d8] bg-white p-3">
                                                <p class="text-[9px] font-semibold uppercase tracking-[0.08em] text-[#9b9287]">Previous</p>
                                                <p class="mt-2 text-[12px] font-bold text-[#433b32]">{{ $product->latestVersion->name }}</p>
                                                <p class="mt-1 text-[10px] text-[#81786c]">{{ $product->latestVersion->category }}</p>
                                                @if ($product->latestVersion->image_path)
                                                    <img src="{{ route('seller.products.version-image', $product->latestVersion) }}" class="mt-3 h-24 w-full rounded-xl object-cover" alt="Previous product image">
                                                @endif
                                            </div>
                                            <div class="rounded-xl border border-[#eee6d8] bg-white p-3">
                                                <p class="text-[9px] font-semibold uppercase tracking-[0.08em] text-[#9b9287]">Current</p>
                                                <p class="mt-2 text-[12px] font-bold text-[#433b32]">{{ $product->name }}</p>
                                                <p class="mt-1 text-[10px] text-[#81786c]">{{ $product->category }}</p>
                                                @if ($product->image_path)
                                                    <img src="{{ route('seller.products.image', $product) }}" class="mt-3 h-24 w-full rounded-xl object-cover" alt="Current product image">
                                                @endif
                                            </div>
                                        </div>
                                        @if (!empty($product->latestVersion->changed_fields))
                                            <p class="mt-3 text-[10px] text-[#8c8173]">Changed: {{ implode(', ', $product->latestVersion->changed_fields) }}</p>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-4 rounded-[14px] border border-[#ead8d8] bg-white p-4">
                                    <p class="text-[11px] font-semibold text-[#8f5c5c]">Automated Detection</p>
                                    <p class="mt-1 text-[12px] leading-5 text-[#756d63]">{{ $product->screening_reason }}</p>

                                    @if (!empty($product->matched_terms))
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @foreach ($product->matched_terms as $term)
                                                <span class="rounded-full bg-[#fff1f1] px-2.5 py-1 text-[10px] font-medium text-[#a65f5f]">{{ $term }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                    <div class="rounded-xl bg-white p-3">
                                        <p class="text-[10px] text-[#958c80]">Price</p>
                                        <p class="mt-1 text-[13px] font-bold text-[#3d3730]">₱{{ number_format((float) $product->price, 2) }}</p>
                                    </div>
                                    <div class="rounded-xl bg-white p-3">
                                        <p class="text-[10px] text-[#958c80]">Stock</p>
                                        <p class="mt-1 text-[13px] font-bold text-[#3d3730]">{{ $product->stock }}</p>
                                    </div>
                                    <div class="rounded-xl bg-white p-3">
                                        <p class="text-[10px] text-[#958c80]">Warnings</p>
                                        <p class="mt-1 text-[13px] font-bold {{ $product->seller->warning_count >= 2 ? 'text-[#ad5f5f]' : 'text-[#a8731f]' }}">
                                            {{ $product->seller->warning_count }} / 3
                                        </p>
                                    </div>
                                    <div class="rounded-xl bg-white p-3">
                                        <p class="text-[10px] text-[#958c80]">Uploaded</p>
                                        <p class="mt-1 text-[12px] font-semibold text-[#3d3730]">{{ $product->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-3 border-t border-[#eee2e2] pt-5 md:grid-cols-2 xl:grid-cols-4">
                            <form method="POST" action="{{ route('admin.compliance.products.approve', $product) }}">
                                @csrf
                                <button class="h-11 w-full rounded-xl border border-[#cfe1d5] bg-[#f2f8f4] text-[12px] font-semibold text-[#4f7c60] hover:bg-[#eaf5ee]">
                                    Approve Product
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.compliance.products.reject', $product) }}" class="flex gap-2">
                                @csrf
                                <input type="hidden" name="reason" value="Product rejected after administrator review.">
                                <button class="h-11 w-full rounded-xl border border-[#e4ddd3] bg-white text-[12px] font-semibold text-[#675f55] hover:bg-[#fcf8f1]">
                                    Reject Only
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.compliance.sellers.suspend30', $product->seller) }}">
                                @csrf
                                <input type="hidden" name="reason" value="Manual 30-day suspension after administrator compliance review.">
                                <button class="h-11 w-full rounded-xl border border-[#ded4e5] bg-[#f7f3f9] text-[12px] font-semibold text-[#765f87] hover:bg-[#f1ebf4]">
                                    Suspend 30 Days
                                </button>
                            </form>

                            <button
                                type="button"
                                data-warning-open
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                data-seller-name="{{ $product->seller->store_name ?: $product->seller->email }}"
                                data-warning-count="{{ $product->seller->warning_count }}"
                                class="h-11 rounded-xl bg-[#b8685f] text-[12px] font-semibold text-white hover:bg-[#a85c54]"
                            >
                                Issue Warning
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-[18px] border border-dashed border-[#ded5c9] bg-[#fcfbf8] p-10 text-center">
                        <p class="text-[14px] font-semibold text-[#514a42]">No flagged products right now.</p>
                        <p class="mt-2 text-[12px] text-[#91887d]">Flagged seller uploads will automatically appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>


        {{-- PENDING PRODUCTS --}}
        <div id="compliancePanel-pending" data-compliance-panel class="hidden">
            <div class="border-b border-[#eee8df] p-5">
                <div class="flex items-center gap-3"><div class="grid h-10 w-10 place-items-center rounded-xl border border-[#ece4d8] bg-[#fcfaf7] text-[#a8731f]"><svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 6h13"></path><path d="M8 12h13"></path><path d="M8 18h13"></path><path d="M3 6h.01"></path><path d="M3 12h.01"></path><path d="M3 18h.01"></path></svg></div><h3 class="text-[17px] font-bold text-[#28221b]">Pending Product Review</h3></div>
                <p class="mt-1 text-[12px] text-[#91887d]">Products that passed basic screening but still require admin approval.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left">
                    <thead>
                        <tr class="border-b border-[#eee8df] bg-[#fcfaf7] text-[11px] font-semibold uppercase tracking-[0.06em] text-[#948b7f]">
                            <th class="px-5 py-4">Product</th>
                            <th class="px-5 py-4">Seller</th>
                            <th class="px-5 py-4">Category</th>
                            <th class="px-5 py-4">Price</th>
                            <th class="px-5 py-4">Screening</th>
                            <th class="px-5 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-[13px]">
                        @forelse ($pendingProducts as $product)
                            <tr class="border-b border-[#f1ece5] last:border-0">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl border border-[#e9e2d8] bg-[#fcfaf7]">
                                            @if ($product->image_path)
                                                <img src="{{ route('seller.products.image', $product) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="grid h-full w-full place-items-center text-[#a79d91]">
                                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                                                        <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                                                        <path d="m4 17 5-5 4 4 2-2 5 4"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-[#3b352e]">{{ $product->name }}</p>
                                            @if ($product->requires_re_review)
                                                <span class="mt-1 inline-flex rounded-full bg-[#fbf2df] px-2 py-1 text-[9px] font-semibold text-[#a8731f]">Edited • Re-review</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-[#71695f]">{{ $product->seller->store_name ?: $product->seller->email }}</td>
                                <td class="px-5 py-4 text-[#71695f]">{{ $product->category }}</td>
                                <td class="px-5 py-4 font-semibold text-[#3b352e]">₱{{ number_format((float) $product->price, 2) }}</td>
                                <td class="px-5 py-4">
                                    @if ($product->requires_re_review)
                                        <span class="rounded-full bg-[#fbf2df] px-2.5 py-1 text-[10px] font-semibold text-[#a8731f]">Re-review required</span>
                                    @else
                                        <span class="rounded-full bg-[#eef6f1] px-2.5 py-1 text-[10px] font-semibold text-[#51836a]">No configured match</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <form method="POST" action="{{ route('admin.compliance.products.approve', $product) }}" class="inline">
                                        @csrf
                                        <button class="rounded-lg bg-[#c99128] px-3 py-2 text-[11px] font-semibold text-white">Approve</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-[13px] text-[#91887d]">No pending products.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        {{-- WARNING HISTORY --}}
        <div id="compliancePanel-warnings" data-compliance-panel class="hidden">
            <div class="border-b border-[#eee8df] p-5">
                <div class="flex items-center gap-3"><div class="grid h-10 w-10 place-items-center rounded-xl border border-[#ece4d8] bg-[#fcfaf7] text-[#a8731f]"><svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v6"></path><path d="M12 18v4"></path><path d="m4.93 4.93 4.24 4.24"></path><path d="m14.83 14.83 4.24 4.24"></path><path d="M2 12h6"></path><path d="M16 12h6"></path><path d="m4.93 19.07 4.24-4.24"></path><path d="m14.83 9.17 4.24-4.24"></path></svg></div><h3 class="text-[17px] font-bold text-[#28221b]">Seller Warning History</h3></div>
                <p class="mt-1 text-[12px] text-[#91887d]">Every warning is recorded. Warning #3 automatically suspends selling privileges for 30 days.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[950px] text-left">
                    <thead>
                        <tr class="border-b border-[#eee8df] bg-[#fcfaf7] text-[11px] font-semibold uppercase tracking-[0.06em] text-[#948b7f]">
                            <th class="px-5 py-4">Seller</th>
                            <th class="px-5 py-4">Warning</th>
                            <th class="px-5 py-4">Product</th>
                            <th class="px-5 py-4">Reason</th>
                            <th class="px-5 py-4">Issued</th>
                        </tr>
                    </thead>
                    <tbody class="text-[13px]">
                        @forelse ($recentWarnings as $warning)
                            <tr class="border-b border-[#f1ece5] last:border-0">
                                <td class="px-5 py-4 font-semibold text-[#3b352e]">{{ $warning->seller->store_name ?: $warning->seller->email }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full {{ $warning->warning_number >= 3 ? 'bg-[#faeeee] text-[#ad5f5f]' : 'bg-[#fbf3e7] text-[#a97724]' }} px-2.5 py-1 text-[11px] font-semibold">
                                        {{ $warning->warning_number }} / 3
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-[#71695f]">{{ $warning->product?->name ?? 'Product removed' }}</td>
                                <td class="px-5 py-4 text-[#71695f]">{{ $warning->reason }}</td>
                                <td class="px-5 py-4 text-[#91887d]">{{ $warning->issued_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-[#91887d]">No warnings issued yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        {{-- SUSPENDED SELLERS --}}
        <div id="compliancePanel-suspended" data-compliance-panel class="hidden">
            <div class="border-b border-[#eee8df] p-5">
                <div class="flex items-center gap-3"><div class="grid h-10 w-10 place-items-center rounded-xl border border-[#ece4d8] bg-[#fcfaf7] text-[#a8731f]"><svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="m8 8 8 8"></path></svg></div><h3 class="text-[17px] font-bold text-[#28221b]">Suspended Sellers</h3></div>
                <p class="mt-1 text-[12px] text-[#91887d]">Seller may still log in to view the suspension and message the administrator.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 p-5 lg:grid-cols-2">
                @forelse ($suspendedSellers as $seller)
                    <article class="rounded-[18px] border border-[#ebe4da] bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[15px] font-bold text-[#312b25]">{{ $seller->store_name ?: $seller->email }}</p>
                                <p class="mt-1 text-[12px] text-[#81786c]">{{ $seller->email }}</p>
                            </div>
                            <span class="rounded-full bg-[#eee8f2] px-3 py-1.5 text-[10px] font-semibold text-[#806992]">SUSPENDED</span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-white p-3">
                                <p class="text-[10px] text-[#958c80]">Warnings</p>
                                <p class="mt-1 text-[14px] font-bold text-[#ad5f5f]">{{ $seller->warning_count }} / 3</p>
                            </div>
                            <div class="rounded-xl bg-white p-3">
                                <p class="text-[10px] text-[#958c80]">Suspended Until</p>
                                <p class="mt-1 text-[12px] font-bold text-[#3d3730]">{{ $seller->suspended_until?->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <p class="mt-4 text-[12px] leading-5 text-[#756d63]">{{ $seller->suspension_reason }}</p>

                        <form method="POST" action="{{ route('admin.compliance.sellers.unsuspend', $seller) }}" class="mt-4">
                            @csrf
                            <button class="h-11 w-full rounded-xl border border-[#d8cde0] bg-white text-[12px] font-semibold text-[#735f84] hover:bg-[#f5f1f7]">Lift Suspension</button>
                        </form>
                    </article>
                @empty
                    <div class="col-span-full rounded-[18px] border border-dashed border-[#ded5c9] p-10 text-center text-[13px] text-[#91887d]">No active suspensions.</div>
                @endforelse
            </div>
        </div>


        {{-- MESSAGES --}}
        <div id="compliancePanel-messages" data-compliance-panel class="hidden">
            <div class="border-b border-[#eee8df] p-5">
                <div class="flex items-center gap-3"><div class="grid h-10 w-10 place-items-center rounded-xl border border-[#ece4d8] bg-[#fcfaf7] text-[#a8731f]"><svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></div><h3 class="text-[17px] font-bold text-[#28221b]">Compliance Appeals & Messages</h3></div>
                <p class="mt-1 text-[12px] text-[#91887d]">Messages sent between sellers and the SARI administrator.</p>
            </div>

            <div class="space-y-4 p-5">
                @forelse ($complianceMessages as $message)
                    <article class="rounded-[16px] border {{ $message->sender_role === 'seller' ? 'border-[#e5ddd1] bg-[#fcfbf8]' : 'border-[#eadfc9] bg-[#fffaf2]' }} p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <p class="text-[13px] font-semibold text-[#403a33]">{{ $message->seller->store_name ?: $message->seller->email }}</p>
                                <p class="mt-1 text-[10px] uppercase tracking-[0.08em] text-[#9b9287]">{{ $message->sender_role === 'seller' ? 'Seller message' : 'Admin message' }}</p>
                            </div>
                            <span class="text-[11px] text-[#91887d]">{{ $message->created_at->format('M d, Y h:i A') }}</span>
                        </div>

                        <p class="mt-3 text-[13px] leading-6 text-[#625a50]">{{ $message->message }}</p>

                        @if ($message->sender_role === 'seller')
                            <form method="POST" action="{{ route('admin.compliance.sellers.reply', $message->seller) }}" class="mt-4 flex flex-col gap-2 sm:flex-row">
                                @csrf
                                <input name="message" required placeholder="Reply to seller..." class="h-11 flex-1 rounded-xl border border-[#e6dfd5] bg-white px-4 text-[12px] outline-none focus:border-[#c99a3d] focus:ring-4 focus:ring-[#c99a3d]/10">
                                <button class="h-11 rounded-xl bg-[#c99128] px-5 text-[12px] font-semibold text-white">Send Reply</button>
                            </form>
                        @endif
                    </article>
                @empty
                    <div class="rounded-[18px] border border-dashed border-[#ded5c9] p-10 text-center text-[13px] text-[#91887d]">No compliance messages yet.</div>
                @endforelse
            </div>
        </div>
    </section>
</div>


{{-- WARNING MODAL --}}
<div id="warningModal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-black/40 p-4 backdrop-blur-[2px]">
    <div class="w-full max-w-[560px] rounded-[22px] border border-[#e8d8d8] bg-white p-6 shadow-[0_30px_80px_rgba(46,29,25,.22)]">
        <div class="flex items-start justify-between gap-4">
            <div>
                <span class="rounded-full bg-[#faeeee] px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.08em] text-[#ad5f5f]">Compliance Action</span>
                <h3 class="mt-3 text-[20px] font-bold text-[#28221b]">Issue Seller Warning</h3>
                <p id="warningModalMeta" class="mt-1 text-[12px] text-[#91887d]"></p>
            </div>
            <button id="warningModalClose" type="button" class="grid h-10 w-10 place-items-center rounded-xl border border-[#e6dfd5] text-[#756d63]">×</button>
        </div>

        <div id="thirdWarningNotice" class="mt-4 hidden rounded-[14px] border border-[#e7bcbc] bg-[#fff2f2] p-4 text-[12px] leading-5 text-[#9c5959]">
            This will become the seller's third warning and will automatically suspend selling privileges for 30 days.
        </div>

        <form id="warningForm" method="POST" action="" class="mt-5 space-y-4">
            @csrf
            <div>
                <label class="mb-2 block text-[12px] font-semibold text-[#514a41]">Violation Reason</label>
                <select name="reason" required class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-3 text-[12px] outline-none focus:border-[#c99a3d]">
                    <option value="">Select violation</option>
                    <option value="Prohibited product listing">Prohibited product listing</option>
                    <option value="Restricted product listing">Restricted product listing</option>
                    <option value="Counterfeit or deceptive listing">Counterfeit or deceptive listing</option>
                    <option value="Repeated marketplace policy violation">Repeated marketplace policy violation</option>
                    <option value="Other marketplace compliance violation">Other marketplace compliance violation</option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-[12px] font-semibold text-[#514a41]">Admin Note</label>
                <textarea name="admin_note" rows="4" placeholder="Explain why the warning is being issued..." class="w-full resize-none rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 py-3 text-[12px] leading-5 outline-none focus:border-[#c99a3d]"></textarea>
            </div>

            <div class="flex justify-end gap-2 border-t border-[#eee8df] pt-4">
                <button id="warningCancel" type="button" class="h-11 rounded-xl border border-[#e1d8cc] bg-white px-5 text-[12px] font-semibold text-[#675f55]">Cancel</button>
                <button class="h-11 rounded-xl bg-[#b8685f] px-5 text-[12px] font-semibold text-white hover:bg-[#a85c54]">Issue Warning</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = Array.from(document.querySelectorAll('[data-compliance-tab]'));
    const panels = Array.from(document.querySelectorAll('[data-compliance-panel]'));

    function activateTab(name) {
        tabs.forEach(function (tab) {
            const active = tab.dataset.complianceTab === name;
            tab.classList.toggle('bg-[#f4efe6]', active);
            tab.classList.toggle('text-[#4f463d]', active);
            tab.classList.toggle('border', !active);
            tab.classList.toggle('border-[#e5ddd1]', !active);
            tab.classList.toggle('bg-white', !active);
            tab.classList.toggle('text-[#675f55]', !active);
        });

        panels.forEach(function (panel) {
            panel.classList.toggle('hidden', panel.id !== 'compliancePanel-' + name);
        });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activateTab(this.dataset.complianceTab);
        });
    });

    const modal = document.getElementById('warningModal');
    const form = document.getElementById('warningForm');
    const meta = document.getElementById('warningModalMeta');
    const thirdNotice = document.getElementById('thirdWarningNotice');

    document.querySelectorAll('[data-warning-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const sellerName = this.dataset.sellerName;
            const warningCount = Number(this.dataset.warningCount || 0);

            form.action = '{{ url('/admin/seller-compliance/products') }}/' + productId + '/warn';
            meta.textContent = sellerName + ' • ' + productName + ' • Current warnings: ' + warningCount + '/3';
            thirdNotice.classList.toggle('hidden', warningCount < 2);

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    document.getElementById('warningModalClose')?.addEventListener('click', closeModal);
    document.getElementById('warningCancel')?.addEventListener('click', closeModal);

    modal?.addEventListener('click', function (event) {
        if (event.target === modal) closeModal();
    });
});
</script>
@endpush