@extends('layouts.admin')

@section('title', 'Seller Compliance — SARI Admin')
@section('page-title', 'Seller Compliance')

@section('content')

@php
    // Load variant rows once for all products shown in the compliance workspace.
    $complianceProductIds = $flaggedProducts->pluck('id')
        ->merge($pendingProducts->pluck('id'))
        ->filter()
        ->unique()
        ->values();

    $complianceVariantGroups = $complianceProductIds->isEmpty()
        ? collect()
        : \App\Models\SellerProductVariant::query()
            ->whereIn('seller_product_id', $complianceProductIds)
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->groupBy('seller_product_id');


    /*
    |--------------------------------------------------------------------------
    | SELLER-CENTRIC FLAGGED QUEUE
    |--------------------------------------------------------------------------
    | A flagged seller appears only once in the main Admin queue. Full product,
    | AI, variant, specification, comparison, and action details live inside
    | the View Details modal.
    */
    $flaggedSellerGroups = $flaggedProducts
        ->groupBy(function ($product) {
            return (string) ($product->seller?->id ?? $product->seller_account_id ?? $product->seller_id ?? $product->id);
        });

    $flaggedSellerCount = $flaggedSellerGroups->count();
@endphp

<style>
    .compliance-card {
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .compliance-card:hover {
        transform: translateY(-2px);
        border-color: #ddd5ca;
        box-shadow: 0 16px 36px rgba(66, 55, 42, .06);
    }

    .compliance-control {
        color: #332e28 !important;
        -webkit-text-fill-color: #332e28 !important;
        background: #fff !important;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .compliance-control::placeholder {
        color: #aaa196 !important;
        -webkit-text-fill-color: #aaa196 !important;
    }

    .compliance-control:focus {
        outline: none;
        border-color: #c99128 !important;
        box-shadow: 0 0 0 4px rgba(201, 145, 40, .08);
    }

    .compliance-tab {
        white-space: nowrap;
        transition: background-color .2s ease, color .2s ease, border-color .2s ease;
    }

    .compliance-tab[data-active="true"] {
        border-color: #eadfc9;
        background: #fff8ec;
        color: #8f6418;
    }

    [data-compliance-panel][hidden] {
        display: none !important;
    }


    /*
    |--------------------------------------------------------------------------
    | ENTERPRISE COMPLIANCE UI — READABLE RESPONSIVE TYPOGRAPHY
    |--------------------------------------------------------------------------
    | Keeps all existing Blade/backend actions intact while making the Admin
    | compliance workspace easier to scan on laptops, desktops, zoomed-out
    | screens, and smaller displays.
    */
    .seller-compliance-page {
        --cp-xs: clamp(0.74rem, 0.70rem + 0.08vw, 0.82rem);
        --cp-sm: clamp(0.80rem, 0.75rem + 0.10vw, 0.90rem);
        --cp-md: clamp(0.88rem, 0.82rem + 0.14vw, 0.98rem);
        --cp-lg: clamp(1rem, 0.93rem + 0.18vw, 1.14rem);
        --cp-xl: clamp(1.22rem, 1.10rem + 0.30vw, 1.45rem);
        --cp-title: clamp(1.65rem, 1.42rem + 0.55vw, 2.05rem);
    }

    /* Replace the old 6–11px visual scale without touching markup logic. */
    .seller-compliance-page [class*="text-[6px]"],
    .seller-compliance-page [class*="text-[6.5px]"],
    .seller-compliance-page [class*="text-[7px]"],
    .seller-compliance-page [class*="text-[7.5px]"] {
        font-size: var(--cp-xs) !important;
        line-height: 1.45 !important;
    }

    .seller-compliance-page [class*="text-[8px]"],
    .seller-compliance-page [class*="text-[8.5px]"] {
        font-size: var(--cp-sm) !important;
        line-height: 1.5 !important;
    }

    .seller-compliance-page [class*="text-[9px]"],
    .seller-compliance-page [class*="text-[9.5px]"],
    .seller-compliance-page [class*="text-[10px]"] {
        font-size: var(--cp-md) !important;
        line-height: 1.5 !important;
    }

    .seller-compliance-page [class*="text-[11px]"] {
        font-size: var(--cp-lg) !important;
        line-height: 1.4 !important;
    }

    .seller-compliance-page [class*="text-[13px]"] {
        font-size: clamp(.98rem, .92rem + .14vw, 1.08rem) !important;
        line-height: 1.35 !important;
    }

    .seller-compliance-page [class*="text-[23px]"],
    .seller-compliance-page [class*="text-[27px]"] {
        font-size: var(--cp-title) !important;
        line-height: 1.15 !important;
    }

    .seller-compliance-page .compliance-control {
        min-height: 44px;
        font-size: var(--cp-sm) !important;
        line-height: 1.45 !important;
    }

    .seller-compliance-page textarea.compliance-control {
        min-height: 116px;
    }

    .seller-compliance-page .compliance-tab {
        min-height: 44px;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
        font-size: var(--cp-sm) !important;
    }

    .seller-compliance-page .compliance-surface {
        box-shadow: 0 10px 28px rgba(45, 37, 28, .035);
    }

    .seller-compliance-page .compliance-summary-card {
        min-height: 104px;
        box-shadow: 0 7px 18px rgba(45, 37, 28, .028);
        transition:
            transform .18s ease,
            box-shadow .18s ease,
            border-color .18s ease;
    }

    .seller-compliance-page .compliance-summary-card:hover {
        transform: translateY(-1px);
        border-color: #ddd3c7;
        box-shadow: 0 12px 28px rgba(45, 37, 28, .045);
    }

    .seller-compliance-page .compliance-summary-card > div > div:first-child > p:first-child {
        font-size: var(--cp-sm) !important;
        line-height: 1.35 !important;
    }

    .seller-compliance-page .compliance-summary-card > div > div:first-child > p:nth-child(2) {
        font-size: clamp(1.45rem, 1.30rem + .34vw, 1.80rem) !important;
        line-height: 1 !important;
    }

    .seller-compliance-page .compliance-card {
        border-radius: 20px;
        box-shadow: 0 8px 22px rgba(45, 37, 28, .032);
    }

    .seller-compliance-page .compliance-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 28px rgba(45, 37, 28, .045);
    }

    .seller-compliance-page .compliance-card button,
    .seller-compliance-page form button {
        min-height: 40px;
        font-size: var(--cp-sm) !important;
    }

    .seller-compliance-page table th {
        font-size: var(--cp-xs) !important;
        line-height: 1.4 !important;
    }

    .seller-compliance-page table td,
    .seller-compliance-page table tbody {
        font-size: var(--cp-sm) !important;
        line-height: 1.5 !important;
    }

    .seller-compliance-page .compliance-workspace {
        box-shadow: 0 12px 30px rgba(45, 37, 28, .035);
    }

    .seller-compliance-page .compliance-workspace-tabs {
        background:
            linear-gradient(180deg, #ffffff 0%, #fdfbf8 100%);
    }

    .seller-compliance-page .flagged-review-grid {
        grid-template-columns: minmax(0, 1fr);
    }

    .seller-compliance-page .flagged-product-image {
        min-height: 116px;
    }

    @media (min-width: 640px) {
        .seller-compliance-page .flagged-product-image {
            width: 116px !important;
            height: 116px !important;
        }
    }

    @media (min-width: 1536px) {
        .seller-compliance-page .flagged-review-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        .seller-compliance-page .compliance-summary-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }
    }

    @media (max-width: 639px) {
        .seller-compliance-page {
            --cp-xs: .76rem;
            --cp-sm: .82rem;
            --cp-md: .90rem;
            --cp-lg: 1rem;
        }

        .seller-compliance-page .compliance-summary-card {
            min-height: 108px;
        }
    }


    .seller-compliance-page .compliance-card > section,
    .seller-compliance-page .compliance-card > div.rounded-\[14px\],
    .seller-compliance-page .compliance-card > div.rounded-\[15px\] {
        box-shadow: none !important;
    }

    .seller-compliance-page .compliance-card .grid.grid-cols-2.gap-2 > div {
        min-height: auto !important;
    }

    @media (prefers-reduced-motion: reduce) {
        .compliance-card,
        .compliance-control,
        .compliance-tab {
            transition: none !important;
            transform: none !important;
        }
    }
</style>

<div class="seller-compliance-page mx-auto w-full max-w-[1800px]">

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <div class="mb-4 flex items-start gap-3 rounded-[16px] border border-[#cfe2d5] bg-[#f4faf6] px-4 py-3.5 sm:px-5">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-white text-[#56816a] shadow-sm">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="m7 12 3 3 7-7"></path>
                    <circle cx="12" cy="12" r="9"></circle>
                </svg>
            </span>
            <div>
                <p class="text-[8px] font-bold uppercase tracking-[.10em] text-[#56816a]">Success</p>
                <p class="mt-1 text-[10px] leading-5 text-[#55705f]">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 flex items-start gap-3 rounded-[16px] border border-[#ead0d0] bg-[#fff6f6] px-4 py-3.5 sm:px-5">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-white text-[#a65f5f] shadow-sm">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="M12 8v5"></path>
                    <path d="M12 16.5h.01"></path>
                    <circle cx="12" cy="12" r="9"></circle>
                </svg>
            </span>
            <div>
                <p class="text-[8px] font-bold uppercase tracking-[.10em] text-[#a65f5f]">Action Required</p>
                <p class="mt-1 text-[10px] leading-5 text-[#8d5f5f]">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif

    {{-- TOP ACTION ONLY --}}
    <div class="flex justify-end">
        <a
            href="{{ route('admin.seller-accounts.control') }}"
            wire:navigate
            class="inline-flex h-9 items-center justify-center gap-1.5 rounded-[10px] bg-[#c99128] px-3 text-[clamp(.72rem,.69rem+.06vw,.78rem)] font-medium text-white shadow-[0_4px_10px_rgba(201,145,40,.10)] transition duration-200 hover:bg-[#b88020]"
        >
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                <circle cx="12" cy="8" r="3"></circle>
                <path d="M5 20a7 7 0 0 1 14 0"></path>
                <path d="M18 7h4"></path>
            </svg>
            Seller Account Control
        </a>
    </div>

    {{-- SUMMARY --}}
    @php
        $summaryCards = [
            ['label' => 'Total Sellers', 'value' => $stats['total_sellers'], 'tone' => 'border-[#dfe7ec] bg-[#f4f7f9] text-[#657f94]', 'icon' => 'users'],
            ['label' => 'Under Review', 'value' => $stats['under_review'], 'tone' => 'border-[#eee0c5] bg-[#fff8ec] text-[#a8731f]', 'icon' => 'review'],
            ['label' => 'Flagged Sellers', 'value' => $flaggedSellerCount, 'tone' => 'border-[#ecdada] bg-[#fff3f3] text-[#a65d5d]', 'icon' => 'alert'],
            ['label' => 'Warnings Issued', 'value' => $stats['active_warnings'], 'tone' => 'border-[#eee1d8] bg-[#fcf5f1] text-[#a86f4f]', 'icon' => 'warning'],
            ['label' => 'Suspended Sellers', 'value' => $stats['suspended_sellers'], 'tone' => 'border-[#e4dce9] bg-[#f7f3f9] text-[#806992]', 'icon' => 'ban'],
        ];
    @endphp

    <section class="compliance-summary-grid mt-4 grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-5">
        @foreach($summaryCards as $card)
            <article class="compliance-summary-card rounded-[17px] border border-[#ebe4da] bg-white p-3.5 sm:p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[8px] font-medium text-[#91887d]">{{ $card['label'] }}</p>
                        <p class="mt-2 text-[22px] font-bold tracking-[-.03em] text-[#28221b]">{{ $card['value'] }}</p>
                    </div>

                    <div class="grid h-[34px] w-[34px] shrink-0 place-items-center rounded-[11px] border {{ $card['tone'] }}">
                        @if($card['icon'] === 'users')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            </svg>
                        @elseif($card['icon'] === 'review')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M8 6h13"></path><path d="M8 12h13"></path><path d="M8 18h13"></path>
                                <path d="M3 6h.01"></path><path d="M3 12h.01"></path><path d="M3 18h.01"></path>
                            </svg>
                        @elseif($card['icon'] === 'alert')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 9v4"></path><path d="M12 17h.01"></path>
                                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path>
                            </svg>
                        @elseif($card['icon'] === 'warning')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"></circle><path d="M12 7v6"></path><path d="M12 17h.01"></path>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"></circle><path d="m8 8 8 8"></path>
                            </svg>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    {{-- WORKSPACE --}}
    <section class="compliance-workspace mt-4 overflow-hidden rounded-[22px] border border-[#ebe4da] bg-white">
        <div class="compliance-workspace-tabs border-b border-[#eee8df] p-4 sm:p-5">
            <div class="flex gap-2 overflow-x-auto">
                @php
                    $tabs = [
                        ['name' => 'flagged', 'label' => 'Flagged Sellers', 'count' => $flaggedSellerCount, 'icon' => 'alert'],
                        ['name' => 'pending', 'label' => 'Pending Review', 'count' => $pendingProducts->count(), 'icon' => 'list'],
                        ['name' => 'warnings', 'label' => 'Warning History', 'count' => null, 'icon' => 'warning'],
                        ['name' => 'suspended', 'label' => 'Suspended Sellers', 'count' => null, 'icon' => 'ban'],
                        ['name' => 'messages', 'label' => 'Appeals / Messages', 'count' => null, 'icon' => 'message'],
                    ];
                @endphp

                @foreach($tabs as $index => $tab)
                    <button
                        type="button"
                        data-compliance-tab="{{ $tab['name'] }}"
                        data-active="{{ $index === 0 ? 'true' : 'false' }}"
                        class="compliance-tab inline-flex h-10 items-center gap-2 rounded-xl border border-[#e5ddd1] bg-white px-3.5 text-[9px] font-semibold text-[#675f55]"
                    >
                        @if($tab['icon'] === 'alert')
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path></svg>
                        @elseif($tab['icon'] === 'list')
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 6h13"></path><path d="M8 12h13"></path><path d="M8 18h13"></path><path d="M3 6h.01"></path><path d="M3 12h.01"></path><path d="M3 18h.01"></path></svg>
                        @elseif($tab['icon'] === 'warning')
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6"></path><path d="M12 17h.01"></path></svg>
                        @elseif($tab['icon'] === 'ban')
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="m8 8 8 8"></path></svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        @endif

                        {{ $tab['label'] }}

                        @if(!is_null($tab['count']))
                            <span class="rounded-full bg-white px-2 py-0.5 text-[7px] font-bold text-[#8a8175] shadow-sm">{{ $tab['count'] }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        {{-- FLAGGED SELLERS --}}
        <div id="compliancePanel-flagged" data-compliance-panel>
            <div class="border-b border-[#eee8df] px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-[#302a24]">Flagged Sellers</p>
                        <p class="mt-1 text-[8px] text-[#8d8478]">
                            Review sellers with flagged listings. Open details only when a full moderation review is needed.
                        </p>
                    </div>

                    <div class="grid w-full grid-cols-1 gap-2.5 sm:grid-cols-[minmax(250px,1fr)_170px] xl:w-[520px]">
                        <div class="relative">
                            <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9b9287]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="m20 20-4-4"></path>
                            </svg>
                            <input
                                id="flaggedSellerSearch"
                                type="search"
                                placeholder="Search seller or flagged product..."
                                class="compliance-control h-11 w-full rounded-xl border border-[#e5ddd1] pl-10 pr-3 text-[8px]"
                            >
                        </div>

                        <div class="relative">
                            <select
                                id="flaggedSellerRiskFilter"
                                class="compliance-control h-11 w-full appearance-none rounded-xl border border-[#e5ddd1] pl-3 pr-9 text-[8px] font-semibold"
                            >
                                <option value="">All risk levels</option>
                                <option value="high">High risk</option>
                                <option value="medium">Medium risk</option>
                                <option value="low">Low risk</option>
                                <option value="review">Needs review</option>
                            </select>
                            <svg viewBox="0 0 24 24" class="pointer-events-none absolute right-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#91887d]" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m7 10 5 5 5-5"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                    <p id="flaggedSellerResultCount" class="text-[7px] font-medium text-[#91887d]">
                        Showing {{ $flaggedSellerCount }} flagged seller{{ $flaggedSellerCount === 1 ? '' : 's' }}
                    </p>

                    <button
                        id="clearFlaggedSellerFilters"
                        type="button"
                        class="hidden rounded-lg px-3 py-1.5 text-[7px] font-bold text-[#9a6817] transition hover:bg-[#fff7e9]"
                    >
                        Clear filters
                    </button>
                </div>
            </div>

            {{-- DESKTOP COLUMN LABELS --}}
            <div class="hidden border-b border-[#eee8df] bg-[#fcfbf9] px-5 py-3 xl:grid xl:grid-cols-[minmax(260px,1.6fr)_140px_130px_130px_170px_120px] xl:gap-4 sm:px-6">
                <p class="text-[7px] font-bold text-[#8f867b]">Seller</p>
                <p class="text-[7px] font-bold text-[#8f867b]">Flagged Listings</p>
                <p class="text-[7px] font-bold text-[#8f867b]">Warnings</p>
                <p class="text-[7px] font-bold text-[#8f867b]">Highest Risk</p>
                <p class="text-[7px] font-bold text-[#8f867b]">Last Flagged</p>
                <p class="text-right text-[7px] font-bold text-[#8f867b]">Action</p>
            </div>

            <div id="flaggedSellerList" class="space-y-3 p-4 sm:p-5">
                @forelse($flaggedSellerGroups as $sellerProducts)
                    @php
                        $seller = $sellerProducts->first()?->seller;
                        $sellerName = $seller?->store_name ?: ($seller?->email ?: 'Seller');
                        $sellerEmail = $seller?->email ?: 'No email';
                        $warningCount = (int) ($seller?->warning_count ?? 0);

                        $highestRiskProduct = $sellerProducts
                            ->sortByDesc(function ($product) {
                                return match (strtolower((string) $product->screening_risk)) {
                                    'high' => 3,
                                    'medium' => 2,
                                    'low' => 1,
                                    default => 0,
                                };
                            })
                            ->first();

                        $highestRisk = strtolower((string) ($highestRiskProduct?->screening_risk ?: 'review'));

                        $riskClass = match ($highestRisk) {
                            'high' => 'border-[#efd5d5] bg-[#fff5f5] text-[#a65d5d]',
                            'medium' => 'border-[#eee0c5] bg-[#fff8ec] text-[#a8731f]',
                            'low' => 'border-[#d7e7dd] bg-[#f3f8f5] text-[#56816a]',
                            default => 'border-[#dfe4ea] bg-[#f5f7f9] text-[#65798b]',
                        };

                        $warningClass = match (true) {
                            $warningCount >= 3 => 'border-[#efd5d5] bg-[#fff4f4] text-[#a65d5d]',
                            $warningCount >= 2 => 'border-[#ecd9c4] bg-[#fff7ed] text-[#a96c36]',
                            $warningCount >= 1 => 'border-[#eee0c5] bg-[#fff9ef] text-[#a8731f]',
                            default => 'border-[#dfe5e2] bg-[#f6f8f7] text-[#687a70]',
                        };

                        $lastFlaggedProduct = $sellerProducts->sortByDesc('updated_at')->first();
                        $lastFlaggedAt = $lastFlaggedProduct?->updated_at ?: $lastFlaggedProduct?->created_at;

                        $productSearchText = $sellerProducts
                            ->map(fn ($product) => trim(($product->name ?? '') . ' ' . ($product->category ?? '') . ' ' . ($product->brand ?? '') . ' ' . ($product->sku ?? '')))
                            ->implode(' ');

                        $sellerSearch = strtolower(trim($sellerName . ' ' . $sellerEmail . ' ' . $productSearchText));
                        $sellerModalId = 'flaggedSellerDetails-' . ($seller?->id ?? $sellerProducts->first()?->id);
                        $sellerInitials = strtoupper(substr(trim($sellerName), 0, 2));
                    @endphp

                    <article
                        data-flagged-seller-row
                        data-flagged-seller-search="{{ $sellerSearch }}"
                        data-flagged-seller-risk="{{ $highestRisk }}"
                        class="rounded-[18px] border border-[#ebe4da] bg-white shadow-[0_7px_18px_rgba(45,37,28,.028)] transition duration-200 hover:border-[#ddd4c7] hover:shadow-[0_11px_24px_rgba(45,37,28,.042)]"
                    >
                        <div class="grid grid-cols-1 xl:grid-cols-[minmax(260px,1.6fr)_140px_130px_130px_170px_120px] xl:items-center xl:gap-4">

                            {{-- SELLER --}}
                            <div class="flex min-w-0 items-center gap-3 border-b border-[#f0ebe4] p-4 xl:border-b-0">
                                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-[13px] bg-[#2e2923] text-[8px] font-bold text-white">
                                    {{ $sellerInitials }}
                                </div>

                                <div class="min-w-0">
                                    <p class="truncate text-[10px] font-bold text-[#302a24]">{{ $sellerName }}</p>
                                    <p class="mt-1 truncate text-[7.5px] text-[#91887d]">{{ $sellerEmail }}</p>
                                </div>
                            </div>

                            {{-- FLAGGED COUNT --}}
                            <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3 xl:border-b-0 xl:px-0">
                                <span class="xl:hidden text-[7px] font-medium text-[#958c80]">Flagged Listings</span>
                                <div class="flex items-center gap-2">
                                    <span class="grid h-8 min-w-[32px] place-items-center rounded-lg bg-[#fff3f3] px-2 text-[8px] font-bold text-[#a65d5d]">
                                        {{ $sellerProducts->count() }}
                                    </span>
                                    <span class="text-[7px] text-[#958c80]">product{{ $sellerProducts->count() === 1 ? '' : 's' }}</span>
                                </div>
                            </div>

                            {{-- WARNING COUNT --}}
                            <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3 xl:border-b-0 xl:px-0">
                                <span class="xl:hidden text-[7px] font-medium text-[#958c80]">Warnings</span>
                                <span class="rounded-full border px-2.5 py-1.5 text-[7px] font-bold {{ $warningClass }}">
                                    {{ $warningCount }} / 3
                                </span>
                            </div>

                            {{-- RISK --}}
                            <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3 xl:border-b-0 xl:px-0">
                                <span class="xl:hidden text-[7px] font-medium text-[#958c80]">Highest Risk</span>
                                <span class="rounded-full border px-2.5 py-1.5 text-[7px] font-bold {{ $riskClass }}">
                                    {{ strtoupper($highestRisk === 'review' ? 'REVIEW' : $highestRisk) }}
                                </span>
                            </div>

                            {{-- LAST FLAGGED --}}
                            <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3 xl:border-b-0 xl:px-0">
                                <span class="xl:hidden text-[7px] font-medium text-[#958c80]">Last Flagged</span>
                                <div class="text-right xl:text-left">
                                    <p class="text-[8px] font-semibold text-[#514a42]">
                                        {{ $lastFlaggedAt?->format('M d, Y') ?: '—' }}
                                    </p>
                                    @if($lastFlaggedAt)
                                        <p class="mt-0.5 text-[7px] text-[#9a9185]">{{ $lastFlaggedAt->diffForHumans() }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- ACTION --}}
                            <div class="flex items-center justify-between px-4 py-4 xl:justify-end xl:px-0 xl:pr-4">
                                <span class="xl:hidden text-[7px] font-medium text-[#958c80]">Action</span>

                                <button
                                    type="button"
                                    data-flagged-seller-open="{{ $sellerModalId }}"
                                    class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[#e4d8c2] bg-[#fffaf1] px-3.5 text-[8px] font-bold text-[#9a6817] transition hover:border-[#d5bd8e] hover:bg-[#fff5e5]"
                                >
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                    
                                </button>
                            </div>
                        </div>
                    </article>


                    {{-- =====================================================
                        FLAGGED SELLER DETAILS MODAL
                    ====================================================== --}}
                    <div
                        id="{{ $sellerModalId }}"
                        data-flagged-seller-modal
                        class="fixed inset-0 z-[170] hidden items-center justify-center bg-black/40 p-3 backdrop-blur-[3px] sm:p-5"
                    >
                        <div class="flex max-h-[92vh] w-full max-w-[1180px] flex-col overflow-hidden rounded-[24px] border border-[#e8dfd3] bg-white shadow-[0_32px_95px_rgba(37,29,19,.22)]">

                            {{-- MODAL HEADER --}}
                            <div class="flex shrink-0 flex-col gap-4 border-b border-[#eee8df] bg-white px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                                <div class="flex min-w-0 items-start gap-3.5">
                                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-[13px] bg-[#2e2923] text-[8px] font-bold text-white">
                                        {{ $sellerInitials }}
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-[13px] font-bold text-[#302a24]">{{ $sellerName }}</h3>

                                            <span class="rounded-full border px-2.5 py-1 text-[7px] font-bold {{ $warningClass }}">
                                                Warning {{ $warningCount }} / 3
                                            </span>

                                            <span class="rounded-full border px-2.5 py-1 text-[7px] font-bold {{ $riskClass }}">
                                                {{ strtoupper($highestRisk === 'review' ? 'REVIEW' : $highestRisk) }} RISK
                                            </span>
                                        </div>

                                        <p class="mt-1 text-[8px] text-[#91887d]">
                                            {{ $sellerEmail }} · {{ $sellerProducts->count() }} flagged listing{{ $sellerProducts->count() === 1 ? '' : 's' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    @if($seller)
                                        <form method="POST" action="{{ route('admin.compliance.sellers.suspend30', $seller) }}">
                                            @csrf
                                            <input type="hidden" name="reason" value="Manual 30-day suspension after administrator compliance review.">

                                            <button
                                                type="submit"
                                                class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[#ded4e5] bg-[#f7f3f9] px-3.5 text-[8px] font-bold text-[#765f87] transition hover:bg-[#f1ebf4]"
                                            >
                                                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                                                    <circle cx="12" cy="12" r="9"></circle>
                                                    <path d="m8 8 8 8"></path>
                                                </svg>
                                                Suspend 30 Days
                                            </button>
                                        </form>
                                    @endif

                                    <button
                                        type="button"
                                        data-flagged-seller-close
                                        class="grid h-10 w-10 place-items-center rounded-xl border border-[#e6dfd5] bg-white text-[#756d63] transition hover:bg-[#fffaf2]"
                                        aria-label="Close seller details"
                                    >
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                            <path d="m7 7 10 10"></path>
                                            <path d="m17 7-10 10"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- MODAL BODY --}}
                            <div class="min-h-0 flex-1 overflow-y-auto bg-[#fcfbf8] p-4 sm:p-5">

                                {{-- SELLER SNAPSHOT --}}
                                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                                    <div class="rounded-[14px] border border-[#e8e2d9] bg-white p-3.5">
                                        <p class="text-[7px] text-[#958c80]">Flagged Listings</p>
                                        <p class="mt-1 text-[12px] font-bold text-[#302a24]">{{ $sellerProducts->count() }}</p>
                                    </div>

                                    <div class="rounded-[14px] border border-[#e8e2d9] bg-white p-3.5">
                                        <p class="text-[7px] text-[#958c80]">Warnings</p>
                                        <p class="mt-1 text-[12px] font-bold {{ $warningCount >= 2 ? 'text-[#a65d5d]' : 'text-[#a8731f]' }}">{{ $warningCount }} / 3</p>
                                    </div>

                                    <div class="rounded-[14px] border border-[#e8e2d9] bg-white p-3.5">
                                        <p class="text-[7px] text-[#958c80]">Highest Risk</p>
                                        <p class="mt-1 text-[10px] font-bold text-[#514a42]">{{ strtoupper($highestRisk === 'review' ? 'REVIEW' : $highestRisk) }}</p>
                                    </div>

                                    <div class="rounded-[14px] border border-[#e8e2d9] bg-white p-3.5">
                                        <p class="text-[7px] text-[#958c80]">Last Flagged</p>
                                        <p class="mt-1 text-[9px] font-bold text-[#514a42]">{{ $lastFlaggedAt?->format('M d, Y') ?: '—' }}</p>
                                    </div>
                                </div>

                                {{-- FLAGGED LISTINGS --}}
                                <div class="mt-4 space-y-4">
                                    @foreach($sellerProducts as $product)
                                        @php
                                            $rawMatchedTerms = $product->matched_terms ?? [];
                                            $matchedTerms = is_array($rawMatchedTerms)
                                                ? $rawMatchedTerms
                                                : (json_decode((string) $rawMatchedTerms, true) ?: []);

                                            if (is_string($matchedTerms)) {
                                                $matchedTerms = json_decode($matchedTerms, true) ?: [];
                                            }

                                            $rawAiSignals = $product->ai_signals ?? [];
                                            $aiSignals = is_array($rawAiSignals)
                                                ? $rawAiSignals
                                                : (json_decode((string) $rawAiSignals, true) ?: []);

                                            if (is_string($aiSignals)) {
                                                $aiSignals = json_decode($aiSignals, true) ?: [];
                                            }

                                            $aiCompleted = ($product->ai_status ?? null) === 'completed';

                                            $rawSpecifications = $product->specifications ?? [];
                                            $productSpecifications = is_array($rawSpecifications)
                                                ? $rawSpecifications
                                                : (json_decode((string) $rawSpecifications, true) ?: []);

                                            $productVariants = $complianceVariantGroups->get($product->id, collect());

                                            $previousSpecifications = [];
                                            $previousVariants = [];

                                            if ($product->latestVersion) {
                                                $previousSpecifications = $product->latestVersion->specifications ?? [];

                                                if (!is_array($previousSpecifications)) {
                                                    $previousSpecifications = json_decode((string) $previousSpecifications, true) ?: [];
                                                }

                                                $previousVariants = $product->latestVersion->variants_snapshot ?? [];

                                                if (!is_array($previousVariants)) {
                                                    $previousVariants = json_decode((string) $previousVariants, true) ?: [];
                                                }
                                            }

                                            $productRisk = strtolower((string) ($product->screening_risk ?: 'review'));

                                            $productRiskClass = match ($productRisk) {
                                                'high' => 'border-[#efd5d5] bg-[#fff5f5] text-[#a65d5d]',
                                                'medium' => 'border-[#eee0c5] bg-[#fff8ec] text-[#a8731f]',
                                                'low' => 'border-[#d7e7dd] bg-[#f3f8f5] text-[#56816a]',
                                                default => 'border-[#dfe4ea] bg-[#f5f7f9] text-[#65798b]',
                                            };
                                        @endphp

                                        <article class="overflow-hidden rounded-[18px] border border-[#e7e0d7] bg-white shadow-[0_8px_22px_rgba(45,37,28,.03)]">
                                            {{-- PRODUCT SUMMARY --}}
                                            <div class="flex flex-col gap-4 p-4 sm:flex-row">
                                                <div class="h-[118px] w-full shrink-0 overflow-hidden rounded-[14px] border border-[#ebe4da] bg-[#faf8f4] sm:w-[118px]">
                                                    @if($product->image_path)
                                                        <img
                                                            src="{{ route('seller.products.image', $product) }}"
                                                            alt="{{ $product->name }}"
                                                            class="h-full w-full object-cover"
                                                        >
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
                                                            <div class="flex flex-wrap items-center gap-2">
                                                                <h4 class="text-[11px] font-bold text-[#312b25]">{{ $product->name }}</h4>

                                                                @if($product->requires_re_review)
                                                                    <span class="rounded-full border border-[#eee0c5] bg-[#fff8ec] px-2 py-1 text-[7px] font-bold text-[#a8731f]">RE-REVIEW</span>
                                                                @endif
                                                            </div>

                                                            <p class="mt-1 text-[8px] text-[#81786c]">
                                                                {{ $product->category }}{{ $product->brand ? ' · ' . $product->brand : '' }}
                                                            </p>
                                                        </div>

                                                        <span class="rounded-full border px-2.5 py-1 text-[7px] font-bold {{ $productRiskClass }}">
                                                            {{ strtoupper($productRisk === 'review' ? 'REVIEW' : $productRisk) }} RISK
                                                        </span>
                                                    </div>

                                                    <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
                                                        <div class="rounded-xl bg-[#faf9f6] p-3">
                                                            <p class="text-[7px] text-[#958c80]">Price</p>
                                                            <p class="mt-1 text-[9px] font-bold text-[#3d3730]">₱{{ number_format((float) $product->price, 2) }}</p>
                                                        </div>

                                                        <div class="rounded-xl bg-[#faf9f6] p-3">
                                                            <p class="text-[7px] text-[#958c80]">Stock</p>
                                                            <p class="mt-1 text-[9px] font-bold text-[#3d3730]">{{ $product->stock }}</p>
                                                        </div>

                                                        <div class="rounded-xl bg-[#faf9f6] p-3">
                                                            <p class="text-[7px] text-[#958c80]">Variants</p>
                                                            <p class="mt-1 text-[9px] font-bold text-[#3d3730]">{{ $productVariants->count() }}</p>
                                                        </div>

                                                        <div class="rounded-xl bg-[#faf9f6] p-3">
                                                            <p class="text-[7px] text-[#958c80]">Uploaded</p>
                                                            <p class="mt-1 text-[8px] font-bold text-[#3d3730]">{{ $product->created_at?->format('M d, Y') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- SCREENING SNAPSHOT --}}
                                            <div class="grid grid-cols-1 gap-3 border-t border-[#eee8df] bg-[#fcfbf8] p-4 lg:grid-cols-2">
                                                <section class="rounded-[14px] border border-[#eadada] bg-[#fffafa] p-4">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <p class="text-[8px] font-bold text-[#875656]">Local Screening</p>

                                                        @if(!is_null($product->risk_score))
                                                            <span class="rounded-full bg-white px-2 py-1 text-[7px] font-bold text-[#a65d5d]">
                                                                {{ $product->risk_score }}/100
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <p class="mt-2 text-[8px] leading-5 text-[#756d63]">
                                                        {{ $product->screening_reason ?: 'No local screening reason available.' }}
                                                    </p>

                                                    @if(!empty($matchedTerms))
                                                        <div class="mt-3 flex flex-wrap gap-1.5">
                                                            @foreach($matchedTerms as $term)
                                                                <span class="rounded-full border border-[#efdada] bg-white px-2 py-1 text-[7px] font-medium text-[#a65d5d]">{{ $term }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </section>

                                                <section class="rounded-[14px] border border-[#dde0e9] bg-[#fbfbfe] p-4">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <p class="text-[8px] font-bold text-[#5e6278]">SARI AI Inspector</p>

                                                        @if($aiCompleted)
                                                            <span class="rounded-full bg-white px-2 py-1 text-[7px] font-bold text-[#666b82]">
                                                                {{ (int) ($product->ai_confidence ?? 0) }}%
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @if($aiCompleted)
                                                        <div class="mt-3 grid grid-cols-2 gap-2">
                                                            <div class="rounded-xl border border-[#e7e8ef] bg-white p-3">
                                                                <p class="text-[7px] text-[#9699a8]">Decision</p>
                                                                <p class="mt-1 text-[8px] font-bold text-[#505467]">
                                                                    {{ str($product->ai_decision ?: 'pending_review')->replace('_', ' ')->title() }}
                                                                </p>
                                                            </div>

                                                            <div class="rounded-xl border border-[#e7e8ef] bg-white p-3">
                                                                <p class="text-[7px] text-[#9699a8]">Policy</p>
                                                                <p class="mt-1 text-[8px] font-bold text-[#505467]">
                                                                    {{ str($product->ai_policy_category ?: 'none')->replace('_', ' ')->title() }}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        @if($product->ai_reason)
                                                            <p class="mt-3 text-[8px] leading-5 text-[#6d7184]">{{ $product->ai_reason }}</p>
                                                        @endif

                                                        @if(!empty($aiSignals))
                                                            <div class="mt-3 flex flex-wrap gap-1.5">
                                                                @foreach($aiSignals as $signal)
                                                                    <span class="rounded-full border border-[#e2e4ed] bg-white px-2 py-1 text-[7px] font-medium text-[#696d80]">{{ $signal }}</span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    @else
                                                        <p class="mt-3 text-[8px] leading-5 text-[#777b8d]">
                                                            AI screening did not complete. Manual administrator review is required.
                                                        </p>
                                                    @endif
                                                </section>
                                            </div>

                                            {{-- EXPANDED LISTING DETAILS --}}
                                            <details class="group border-t border-[#eee8df]">
                                                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3.5 transition hover:bg-[#fcfaf7]">
                                                    <div>
                                                        <p class="text-[8px] font-bold text-[#514a42]">Full Listing Details</p>
                                                        <p class="mt-0.5 text-[7px] text-[#91887d]">Specifications, variants, and seller edits</p>
                                                    </div>

                                                    <svg viewBox="0 0 24 24" class="h-4 w-4 text-[#9b7a3f] transition group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="m7 10 5 5 5-5"></path>
                                                    </svg>
                                                </summary>

                                                <div class="border-t border-[#eee8df] bg-white p-4">
                                                    @if(!empty($productSpecifications))
                                                        <div>
                                                            <div class="flex items-center justify-between gap-3">
                                                                <p class="text-[8px] font-bold text-[#4b433a]">Specifications</p>
                                                                <span class="text-[7px] text-[#958c80]">{{ count($productSpecifications) }} item{{ count($productSpecifications) === 1 ? '' : 's' }}</span>
                                                            </div>

                                                            <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                                                @foreach($productSpecifications as $spec)
                                                                    @php
                                                                        $specName = is_array($spec) ? ($spec['name'] ?? 'Specification') : 'Specification';
                                                                        $specValue = is_array($spec) ? ($spec['value'] ?? '—') : (string) $spec;
                                                                        $specUnit = is_array($spec) ? ($spec['unit'] ?? null) : null;
                                                                    @endphp

                                                                    <div class="rounded-xl border border-[#eee7de] bg-[#fcfbf9] p-3">
                                                                        <p class="text-[7px] text-[#9a9186]">{{ $specName }}</p>
                                                                        <p class="mt-1 break-words text-[8px] font-semibold text-[#403930]">
                                                                            {{ $specValue }}{{ $specUnit ? ' ' . $specUnit : '' }}
                                                                        </p>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if($productVariants->isNotEmpty())
                                                        <div class="{{ !empty($productSpecifications) ? 'mt-4' : '' }}">
                                                            <div class="flex items-center justify-between gap-3">
                                                                <p class="text-[8px] font-bold text-[#4b433a]">Variants</p>
                                                                <span class="text-[7px] text-[#958c80]">{{ $productVariants->count() }} active</span>
                                                            </div>

                                                            <div class="mt-3 overflow-x-auto rounded-xl border border-[#e8e2d9]">
                                                                <table class="w-full min-w-[620px] text-left">
                                                                    <thead class="bg-[#fcfbf9]">
                                                                        <tr>
                                                                            <th class="px-4 py-3">Options</th>
                                                                            <th class="px-4 py-3">SKU</th>
                                                                            <th class="px-4 py-3">Price</th>
                                                                            <th class="px-4 py-3">Stock</th>
                                                                        </tr>
                                                                    </thead>

                                                                    <tbody class="divide-y divide-[#eee9e2]">
                                                                        @foreach($productVariants as $variant)
                                                                            @php
                                                                                $variantOptions = $variant->option_values ?? [];

                                                                                if (!is_array($variantOptions)) {
                                                                                    $variantOptions = json_decode((string) $variantOptions, true) ?: [];
                                                                                }
                                                                            @endphp

                                                                            <tr>
                                                                                <td class="px-4 py-3 font-semibold text-[#554d45]">
                                                                                    @forelse($variantOptions as $optionName => $optionValue)
                                                                                        {{ $optionName }}: {{ $optionValue }}@if(!$loop->last) · @endif
                                                                                    @empty
                                                                                        Default
                                                                                    @endforelse
                                                                                </td>

                                                                                <td class="px-4 py-3 text-[#81786c]">{{ $variant->sku ?: '—' }}</td>
                                                                                <td class="px-4 py-3 font-bold text-[#514a42]">₱{{ number_format((float) $variant->price, 2) }}</td>
                                                                                <td class="px-4 py-3 font-semibold {{ (int) $variant->stock <= 5 ? 'text-[#aa6262]' : 'text-[#56816a]' }}">{{ $variant->stock }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if($product->requires_re_review && $product->latestVersion)
                                                        <div class="mt-4 rounded-[14px] border border-[#e8e1d7] bg-[#fcfbf8] p-4">
                                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                                <p class="text-[8px] font-bold text-[#6c5a37]">Previous vs Current Listing</p>

                                                                @if(!empty($product->latestVersion->changed_fields))
                                                                    <p class="text-[7px] text-[#928779]">
                                                                        Changed: {{ implode(', ', $product->latestVersion->changed_fields) }}
                                                                    </p>
                                                                @endif
                                                            </div>

                                                            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                                                <div class="rounded-xl border border-[#eee7de] bg-white p-3">
                                                                    <p class="text-[7px] font-bold uppercase tracking-[.08em] text-[#9b9287]">Previous</p>
                                                                    <p class="mt-2 text-[9px] font-bold text-[#433b32]">{{ $product->latestVersion->name }}</p>
                                                                    <p class="mt-1 text-[8px] text-[#81786c]">
                                                                        {{ $product->latestVersion->category }}{{ $product->latestVersion->brand ? ' · ' . $product->latestVersion->brand : '' }}
                                                                    </p>

                                                                    @if(!empty($previousSpecifications) || !empty($previousVariants))
                                                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                                                            @if(!empty($previousSpecifications))
                                                                                <span class="rounded-full bg-[#faf8f4] px-2 py-1 text-[7px] text-[#81786c]">{{ count($previousSpecifications) }} specs</span>
                                                                            @endif

                                                                            @if(!empty($previousVariants))
                                                                                <span class="rounded-full bg-[#f8f5fb] px-2 py-1 text-[7px] text-[#77698a]">{{ count($previousVariants) }} variants</span>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                                <div class="rounded-xl border border-[#eee7de] bg-white p-3">
                                                                    <p class="text-[7px] font-bold uppercase tracking-[.08em] text-[#9b9287]">Current</p>
                                                                    <p class="mt-2 text-[9px] font-bold text-[#433b32]">{{ $product->name }}</p>
                                                                    <p class="mt-1 text-[8px] text-[#81786c]">
                                                                        {{ $product->category }}{{ $product->brand ? ' · ' . $product->brand : '' }}
                                                                    </p>

                                                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                                                        @if(!empty($productSpecifications))
                                                                            <span class="rounded-full bg-[#faf8f4] px-2 py-1 text-[7px] text-[#81786c]">{{ count($productSpecifications) }} specs</span>
                                                                        @endif

                                                                        @if($productVariants->isNotEmpty())
                                                                            <span class="rounded-full bg-[#f8f5fb] px-2 py-1 text-[7px] text-[#77698a]">{{ $productVariants->count() }} variants</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if(empty($productSpecifications) && $productVariants->isEmpty() && !($product->requires_re_review && $product->latestVersion))
                                                        <div class="rounded-xl border border-dashed border-[#ded6ca] bg-[#fcfbf9] px-4 py-5 text-center text-[8px] text-[#91887d]">
                                                            No additional listing details available.
                                                        </div>
                                                    @endif
                                                </div>
                                            </details>

                                            {{-- PRODUCT ACTIONS --}}
                                            <div class="grid grid-cols-1 gap-2 border-t border-[#eee8df] bg-white p-4 sm:grid-cols-3">
                                                <form method="POST" action="{{ route('admin.compliance.products.approve', $product) }}">
                                                    @csrf

                                                    <button class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-[#d5e5da] bg-[#f3f9f5] text-[8px] font-bold text-[#56816a] transition hover:bg-[#ecf6ef]">
                                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="m7 12 3 3 7-7"></path>
                                                        </svg>
                                                        Approve
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.compliance.products.reject', $product) }}">
                                                    @csrf
                                                    <input type="hidden" name="reason" value="Product rejected after administrator review.">

                                                    <button class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-[#e4ddd3] bg-white text-[8px] font-bold text-[#675f55] transition hover:bg-[#faf8f4]">
                                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="m8 8 8 8"></path>
                                                            <path d="m16 8-8 8"></path>
                                                        </svg>
                                                        Reject
                                                    </button>
                                                </form>

                                                <button
                                                    type="button"
                                                    data-warning-open
                                                    data-product-id="{{ $product->id }}"
                                                    data-product-name="{{ $product->name }}"
                                                    data-seller-name="{{ $sellerName }}"
                                                    data-warning-count="{{ $warningCount }}"
                                                    class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[#b8685f] text-[8px] font-bold text-white transition hover:bg-[#a85c54]"
                                                >
                                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                                                        <circle cx="12" cy="12" r="9"></circle>
                                                        <path d="M12 7v6"></path>
                                                        <path d="M12 17h.01"></path>
                                                    </svg>
                                                    Issue Warning
                                                </button>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>

                                {{-- WARNING HISTORY FOR THIS SELLER --}}
                                @php
                                    $sellerWarningHistory = $recentWarnings
                                        ->filter(function ($warning) use ($seller) {
                                            $warningSellerId = $warning->seller?->id
                                                ?? $warning->seller_account_id
                                                ?? $warning->seller_id
                                                ?? null;

                                            return (string) $warningSellerId === (string) ($seller?->id ?? '');
                                        })
                                        ->values();
                                @endphp

                                @if($sellerWarningHistory->isNotEmpty())
                                    <section class="mt-4 overflow-hidden rounded-[18px] border border-[#e7e0d7] bg-white">
                                        <div class="border-b border-[#eee8df] px-4 py-3.5">
                                            <p class="text-[9px] font-bold text-[#403930]">Recent Warning History</p>
                                            <p class="mt-1 text-[7px] text-[#91887d]">Official warnings already issued to this seller.</p>
                                        </div>

                                        <div class="divide-y divide-[#eee8df]">
                                            @foreach($sellerWarningHistory as $warning)
                                                <div class="grid grid-cols-1 gap-2 px-4 py-3.5 sm:grid-cols-[100px_1fr_160px] sm:items-center">
                                                    <span class="w-fit rounded-full border px-2.5 py-1 text-[7px] font-bold {{ $warning->warning_number >= 3 ? 'border-[#ecdada] bg-[#fff3f3] text-[#a65d5d]' : 'border-[#eee0c5] bg-[#fff8ec] text-[#a8731f]' }}">
                                                        {{ $warning->warning_number }} / 3
                                                    </span>

                                                    <div class="min-w-0">
                                                        <p class="text-[8px] font-semibold text-[#514a42]">{{ $warning->reason }}</p>
                                                        <p class="mt-1 truncate text-[7px] text-[#91887d]">{{ $warning->product?->name ?? 'Product removed' }}</p>
                                                    </div>

                                                    <p class="text-[7px] text-[#91887d] sm:text-right">{{ $warning->issued_at->format('M d, Y h:i A') }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </section>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[18px] border border-dashed border-[#ded5c9] bg-[#fcfbf8] p-10 text-center">
                        <div class="mx-auto grid h-10 w-10 place-items-center rounded-full bg-white text-[#91887d] shadow-sm">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 9v4"></path>
                                <path d="M12 17h.01"></path>
                                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path>
                            </svg>
                        </div>

                        <p class="mt-3 text-[9px] font-bold text-[#514a42]">No flagged sellers</p>
                        <p class="mt-1 text-[8px] text-[#91887d]">Sellers with flagged listings will appear here.</p>
                    </div>
                @endforelse
            </div>

            <div id="flaggedSellerFilterEmpty" class="hidden p-5">
                <div class="rounded-[18px] border border-dashed border-[#ded5c9] bg-[#fcfbf8] p-10 text-center">
                    <p class="text-[9px] font-bold text-[#514a42]">No matching sellers</p>
                    <p class="mt-1 text-[8px] text-[#91887d]">Try another seller name, product keyword, or risk filter.</p>

                    <button
                        id="flaggedSellerEmptyClear"
                        type="button"
                        class="mt-4 rounded-xl border border-[#e2d8c8] bg-white px-4 py-2.5 text-[8px] font-bold text-[#9a6817] transition hover:bg-[#fffaf2]"
                    >
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        {{-- PENDING REVIEW --}}
        <div id="compliancePanel-pending" data-compliance-panel hidden>
            <div class="border-b border-[#eee8df] px-5 py-4">
                <p class="text-[11px] font-bold text-[#302a24]">Pending Product Review</p>
                <p class="mt-1 text-[8px] text-[#8d8478]">Products that passed basic screening but still require administrator approval.</p>
            </div>

            <div class="grid grid-cols-1 gap-3 p-4 sm:p-5 xl:grid-cols-2">
                @forelse($pendingProducts as $product)
                    @php
                        $pendingRawSpecs = $product->specifications ?? [];
                        $pendingSpecs = is_array($pendingRawSpecs)
                            ? $pendingRawSpecs
                            : (json_decode((string) $pendingRawSpecs, true) ?: []);
                        $pendingVariants = $complianceVariantGroups->get($product->id, collect());
                    @endphp
                    <article class="compliance-card rounded-[17px] border border-[#ebe4da] bg-white p-4">
                        <div class="flex gap-3">
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-[#e9e2d8] bg-[#faf8f4]">
                                @if($product->image_path)
                                    <img src="{{ route('seller.products.image', $product) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="grid h-full w-full place-items-center text-[#a79d91]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="m4 17 5-5 4 4 2-2 5 4"></path></svg></div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <h3 class="text-[10px] font-bold text-[#3b352e]">{{ $product->name }}</h3>
                                        <p class="mt-1 text-[8px] text-[#81786c]">{{ $product->seller->store_name ?: $product->seller->email }}{{ $product->brand ? ' · ' . $product->brand : '' }}</p>
                                    </div>

                                    @if($product->requires_re_review)
                                        <span class="rounded-full border border-[#eee0c5] bg-[#fff8ec] px-2 py-1 text-[7px] font-bold text-[#a8731f]">RE-REVIEW</span>
                                    @else
                                        <span class="rounded-full border border-[#d5e5da] bg-[#f3f9f5] px-2 py-1 text-[7px] font-bold text-[#56816a]">NO MATCH</span>
                                    @endif
                                </div>

                                <div class="mt-3 flex flex-wrap items-center gap-2 text-[8px] text-[#756d63]">
                                    <span>{{ $product->category }}</span>
                                    <span>·</span>
                                    <span class="font-semibold text-[#3b352e]">₱{{ number_format((float) $product->price, 2) }}</span>
                                    @if($pendingVariants->isNotEmpty())
                                        <span class="rounded-full border border-[#ddd8e8] bg-[#f8f5fb] px-2 py-1 text-[7px] font-semibold text-[#77698a]">{{ $pendingVariants->count() }} variants</span>
                                    @endif
                                    @if(!empty($pendingSpecs))
                                        <span class="rounded-full border border-[#e4ddd3] bg-[#faf8f4] px-2 py-1 text-[7px] font-semibold text-[#7d7368]">{{ count($pendingSpecs) }} specs</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.compliance.products.approve', $product) }}" class="mt-4">
                            @csrf
                            <button class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-[#c99128] text-[8px] font-bold text-white transition hover:bg-[#b88020]">
                                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg>
                                Approve Product
                            </button>
                        </form>
                    </article>
                @empty
                    <div class="col-span-full rounded-[18px] border border-dashed border-[#ded5c9] bg-[#fcfbf8] p-10 text-center text-[8px] text-[#91887d]">No pending products.</div>
                @endforelse
            </div>
        </div>

        {{-- WARNING HISTORY --}}
        <div id="compliancePanel-warnings" data-compliance-panel hidden>
            <div class="border-b border-[#eee8df] px-5 py-4">
                <p class="text-[11px] font-bold text-[#302a24]">Seller Warning History</p>
                <p class="mt-1 text-[8px] text-[#8d8478]">Every warning is retained. Warning #3 automatically triggers a 30-day seller suspension.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left">
                    <thead>
                        <tr class="border-b border-[#eee8df] bg-[#fcfaf7] text-[8px] font-bold uppercase tracking-[.06em] text-[#948b7f]">
                            <th class="px-5 py-4">Seller</th>
                            <th class="px-5 py-4">Warning</th>
                            <th class="px-5 py-4">Product</th>
                            <th class="px-5 py-4">Reason</th>
                            <th class="px-5 py-4">Issued</th>
                        </tr>
                    </thead>
                    <tbody class="text-[9px]">
                        @forelse($recentWarnings as $warning)
                            <tr class="border-b border-[#f1ece5] last:border-0">
                                <td class="px-5 py-4 font-semibold text-[#3b352e]">{{ $warning->seller->store_name ?: $warning->seller->email }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full border px-2.5 py-1 text-[7px] font-bold {{ $warning->warning_number >= 3 ? 'border-[#ecdada] bg-[#fff3f3] text-[#a65d5d]' : 'border-[#eee0c5] bg-[#fff8ec] text-[#a8731f]' }}">{{ $warning->warning_number }} / 3</span>
                                </td>
                                <td class="px-5 py-4 text-[#71695f]">{{ $warning->product?->name ?? 'Product removed' }}</td>
                                <td class="px-5 py-4 text-[#71695f]">{{ $warning->reason }}</td>
                                <td class="px-5 py-4 text-[#91887d]">{{ $warning->issued_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-[8px] text-[#91887d]">No warnings issued yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SUSPENDED SELLERS --}}
        <div id="compliancePanel-suspended" data-compliance-panel hidden>
            <div class="border-b border-[#eee8df] px-5 py-4">
                <p class="text-[11px] font-bold text-[#302a24]">Suspended Sellers</p>
                <p class="mt-1 text-[8px] text-[#8d8478]">Review active suspensions and lift them manually when appropriate.</p>
            </div>

            <div class="grid grid-cols-1 gap-3 p-4 sm:p-5 xl:grid-cols-2">
                @forelse($suspendedSellers as $seller)
                    <article class="compliance-card rounded-[17px] border border-[#e5dde9] bg-white p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#f7f3f9] text-[#806992]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="m8 8 8 8"></path></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-[#312b25]">{{ $seller->store_name ?: $seller->email }}</p>
                                    <p class="mt-1 text-[8px] text-[#81786c]">{{ $seller->email }}</p>
                                </div>
                            </div>

                            <span class="rounded-full border border-[#e4dce9] bg-[#f7f3f9] px-2.5 py-1 text-[7px] font-bold text-[#806992]">SUSPENDED</span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2">
                            <div class="rounded-xl bg-[#faf9f6] p-3"><p class="text-[7px] text-[#958c80]">Warnings</p><p class="mt-1 text-[10px] font-bold text-[#a65d5d]">{{ $seller->warning_count }} / 3</p></div>
                            <div class="rounded-xl bg-[#faf9f6] p-3"><p class="text-[7px] text-[#958c80]">Suspended Until</p><p class="mt-1 text-[9px] font-bold text-[#3d3730]">{{ $seller->suspended_until?->format('M d, Y') }}</p></div>
                        </div>

                        <p class="mt-3 text-[8px] leading-4 text-[#756d63]">{{ $seller->suspension_reason }}</p>

                        <form method="POST" action="{{ route('admin.compliance.sellers.unsuspend', $seller) }}" class="mt-4">
                            @csrf
                            <button class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-[#d8cde0] bg-white text-[8px] font-bold text-[#735f84] transition hover:bg-[#f5f1f7]">
                                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M3 12a9 9 0 1 0 3-6.7"></path><path d="M3 3v6h6"></path></svg>
                                Lift Suspension
                            </button>
                        </form>
                    </article>
                @empty
                    <div class="col-span-full rounded-[18px] border border-dashed border-[#ded5c9] bg-[#fcfbf8] p-10 text-center text-[8px] text-[#91887d]">No active suspensions.</div>
                @endforelse
            </div>
        </div>

        {{-- MESSAGES --}}
        <div id="compliancePanel-messages" data-compliance-panel hidden>
            <div class="border-b border-[#eee8df] px-5 py-4">
                <p class="text-[11px] font-bold text-[#302a24]">Compliance Appeals & Messages</p>
                <p class="mt-1 text-[8px] text-[#8d8478]">Review seller messages and send a direct compliance response.</p>
            </div>

            <div class="space-y-3 p-4 sm:p-5">
                @forelse($complianceMessages as $message)
                    <article class="rounded-[16px] border {{ $message->sender_role === 'seller' ? 'border-[#e5ddd1] bg-[#fcfbf8]' : 'border-[#eadfc9] bg-[#fffaf2]' }} p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-[#7e7468] shadow-sm">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-[#403a33]">{{ $message->seller->store_name ?: $message->seller->email }}</p>
                                    <p class="mt-1 text-[7px] uppercase tracking-[.08em] text-[#9b9287]">{{ $message->sender_role === 'seller' ? 'Seller message' : 'Admin message' }}</p>
                                </div>
                            </div>
                            <span class="text-[7px] text-[#91887d]">{{ $message->created_at->format('M d, Y h:i A') }}</span>
                        </div>

                        <p class="mt-3 text-[9px] leading-5 text-[#625a50]">{{ $message->message }}</p>

                        @if($message->sender_role === 'seller')
                            <form method="POST" action="{{ route('admin.compliance.sellers.reply', $message->seller) }}" class="mt-4 flex flex-col gap-2 sm:flex-row">
                                @csrf
                                <input name="message" required placeholder="Reply to seller..." class="compliance-control h-10 flex-1 rounded-xl border border-[#e6dfd5] px-3 text-[8px]">
                                <button class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[#c99128] px-4 text-[8px] font-bold text-white transition hover:bg-[#b88020]">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
                                    Send Reply
                                </button>
                            </form>
                        @endif
                    </article>
                @empty
                    <div class="rounded-[18px] border border-dashed border-[#ded5c9] bg-[#fcfbf8] p-10 text-center text-[8px] text-[#91887d]">No compliance messages yet.</div>
                @endforelse
            </div>
        </div>
    </section>
</div>

{{-- WARNING MODAL --}}
<div id="warningModal" class="fixed inset-0 z-[210] hidden items-center justify-center bg-black/35 p-4 backdrop-blur-[2px]">
    <div class="w-full max-w-[620px] rounded-[24px] border border-[#e8d8d8] bg-white p-5 shadow-[0_30px_80px_rgba(46,29,25,.18)] sm:p-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#fff3f3] text-[#a65d5d]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6"></path><path d="M12 17h.01"></path></svg>
                </div>
                <div>
                    <span class="text-[7px] font-bold uppercase tracking-[.10em] text-[#a65d5d]">Compliance Action</span>
                    <h3 class="mt-1 text-[17px] font-bold text-[#28221b]">Issue Seller Warning</h3>
                    <p id="warningModalMeta" class="mt-1 text-[8px] text-[#91887d]"></p>
                </div>
            </div>

            <button id="warningModalClose" type="button" class="grid h-9 w-9 place-items-center rounded-xl border border-[#e6dfd5] bg-white text-[#756d63] transition hover:bg-[#faf8f4]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m7 7 10 10"></path><path d="m17 7-10 10"></path></svg>
            </button>
        </div>

        <div id="thirdWarningNotice" class="mt-4 hidden rounded-[14px] border border-[#e7bcbc] bg-[#fff2f2] p-4 text-[8px] leading-4 text-[#9c5959]">
            This becomes the seller's third warning and automatically suspends selling privileges for 30 days.
        </div>

        <form id="warningForm" method="POST" action="" class="mt-5 space-y-4">
            @csrf

            <div>
                <label class="mb-2 block text-[8px] font-bold text-[#514a41]">Violation Reason *</label>
                <select name="reason" required class="compliance-control h-10 w-full rounded-xl border border-[#e6dfd5] px-3 text-[8px]">
                    <option value="">Select violation</option>
                    <option value="Prohibited product listing">Prohibited product listing</option>
                    <option value="Restricted product listing">Restricted product listing</option>
                    <option value="Counterfeit or deceptive listing">Counterfeit or deceptive listing</option>
                    <option value="Repeated marketplace policy violation">Repeated marketplace policy violation</option>
                    <option value="Other marketplace compliance violation">Other marketplace compliance violation</option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-[8px] font-bold text-[#514a41]">Admin Note</label>
                <textarea name="admin_note" rows="4" placeholder="Explain why the warning is being issued..." class="compliance-control w-full resize-none rounded-xl border border-[#e6dfd5] px-3 py-3 text-[8px] leading-4"></textarea>
            </div>

            <div class="flex justify-end gap-2 border-t border-[#eee8df] pt-4">
                <button id="warningCancel" type="button" class="h-10 rounded-xl border border-[#e1d8cc] bg-white px-4 text-[8px] font-bold text-[#675f55]">Cancel</button>
                <button class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[#b8685f] px-4 text-[8px] font-bold text-white transition hover:bg-[#a85c54]">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6"></path><path d="M12 17h.01"></path></svg>
                    Issue Warning
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    /*
    |--------------------------------------------------------------------------
    | COMPLIANCE TABS
    |--------------------------------------------------------------------------
    */
    const tabs = Array.from(document.querySelectorAll('[data-compliance-tab]'));
    const panels = Array.from(document.querySelectorAll('[data-compliance-panel]'));

    function activateTab(name) {
        tabs.forEach(function (tab) {
            const active = tab.dataset.complianceTab === name;
            tab.dataset.active = active ? 'true' : 'false';
        });

        panels.forEach(function (panel) {
            panel.hidden = panel.id !== 'compliancePanel-' + name;
        });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activateTab(this.dataset.complianceTab);
        });
    });

    activateTab('flagged');


    /*
    |--------------------------------------------------------------------------
    | FLAGGED SELLER SEARCH + RISK FILTER
    |--------------------------------------------------------------------------
    */
    const flaggedSellerSearch = document.getElementById('flaggedSellerSearch');
    const flaggedSellerRiskFilter = document.getElementById('flaggedSellerRiskFilter');
    const flaggedSellerRows = Array.from(document.querySelectorAll('[data-flagged-seller-row]'));
    const flaggedSellerList = document.getElementById('flaggedSellerList');
    const flaggedSellerFilterEmpty = document.getElementById('flaggedSellerFilterEmpty');
    const flaggedSellerResultCount = document.getElementById('flaggedSellerResultCount');
    const clearFlaggedSellerFilters = document.getElementById('clearFlaggedSellerFilters');
    const flaggedSellerEmptyClear = document.getElementById('flaggedSellerEmptyClear');

    function filterFlaggedSellers() {
        const query = (flaggedSellerSearch?.value || '').trim().toLowerCase();
        const risk = (flaggedSellerRiskFilter?.value || '').trim().toLowerCase();

        let visible = 0;

        flaggedSellerRows.forEach(function (row) {
            const searchable = (row.dataset.flaggedSellerSearch || '').toLowerCase();
            const rowRisk = (row.dataset.flaggedSellerRisk || '').toLowerCase();

            const matchesQuery = query === '' || searchable.includes(query);
            const matchesRisk = risk === '' || rowRisk === risk;
            const matches = matchesQuery && matchesRisk;

            row.classList.toggle('hidden', !matches);

            if (matches) {
                visible++;
            }
        });

        if (flaggedSellerResultCount) {
            flaggedSellerResultCount.textContent =
                'Showing ' + visible +
                ' of ' + flaggedSellerRows.length +
                ' flagged seller' + (visible === 1 ? '' : 's');
        }

        const hasFilters = query !== '' || risk !== '';

        clearFlaggedSellerFilters?.classList.toggle('hidden', !hasFilters);

        if (flaggedSellerRows.length > 0) {
            flaggedSellerList?.classList.toggle('hidden', visible === 0);
            flaggedSellerFilterEmpty?.classList.toggle('hidden', visible !== 0);
        }
    }

    function resetFlaggedSellerFilters() {
        if (flaggedSellerSearch) {
            flaggedSellerSearch.value = '';
        }

        if (flaggedSellerRiskFilter) {
            flaggedSellerRiskFilter.value = '';
        }

        filterFlaggedSellers();
        flaggedSellerSearch?.focus();
    }

    flaggedSellerSearch?.addEventListener('input', filterFlaggedSellers);
    flaggedSellerRiskFilter?.addEventListener('change', filterFlaggedSellers);
    clearFlaggedSellerFilters?.addEventListener('click', resetFlaggedSellerFilters);
    flaggedSellerEmptyClear?.addEventListener('click', resetFlaggedSellerFilters);

    filterFlaggedSellers();


    /*
    |--------------------------------------------------------------------------
    | FLAGGED SELLER DETAILS MODALS
    |--------------------------------------------------------------------------
    */
    const flaggedSellerModals = Array.from(
        document.querySelectorAll('[data-flagged-seller-modal]')
    );

    function closeAllSellerDetails() {
        flaggedSellerModals.forEach(function (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });

        document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-flagged-seller-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            const modal = document.getElementById(this.dataset.flaggedSellerOpen);

            if (!modal) {
                return;
            }

            closeAllSellerDetails();

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        });
    });

    document.querySelectorAll('[data-flagged-seller-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            closeAllSellerDetails();
        });
    });

    flaggedSellerModals.forEach(function (modal) {
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeAllSellerDetails();
            }
        });
    });


    /*
    |--------------------------------------------------------------------------
    | WARNING MODAL
    |--------------------------------------------------------------------------
    */
    const warningModal = document.getElementById('warningModal');
    const warningForm = document.getElementById('warningForm');
    const warningMeta = document.getElementById('warningModalMeta');
    const thirdNotice = document.getElementById('thirdWarningNotice');

    document.querySelectorAll('[data-warning-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const sellerName = this.dataset.sellerName;
            const warningCount = Number(this.dataset.warningCount || 0);

            closeAllSellerDetails();

            warningForm.action = '{{ url('/admin/seller-compliance/products') }}/' + productId + '/warn';
            warningMeta.textContent =
                sellerName +
                ' · ' +
                productName +
                ' · Current warnings: ' +
                warningCount +
                '/3';

            thirdNotice.classList.toggle('hidden', warningCount < 2);

            warningModal.classList.remove('hidden');
            warningModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        });
    });

    function closeWarningModal() {
        warningModal?.classList.add('hidden');
        warningModal?.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    document.getElementById('warningModalClose')?.addEventListener('click', closeWarningModal);
    document.getElementById('warningCancel')?.addEventListener('click', closeWarningModal);

    warningModal?.addEventListener('click', function (event) {
        if (event.target === warningModal) {
            closeWarningModal();
        }
    });


    /*
    |--------------------------------------------------------------------------
    | ESCAPE KEY
    |--------------------------------------------------------------------------
    */
    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }

        if (
            warningModal &&
            !warningModal.classList.contains('hidden')
        ) {
            closeWarningModal();
            return;
        }

        closeAllSellerDetails();
    });
})();
</script>
@endpush
