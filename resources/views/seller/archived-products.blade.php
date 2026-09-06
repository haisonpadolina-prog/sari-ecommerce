@extends('layouts.seller')

@section('title', 'Archived Products — SARI Seller')
@section('page-title', 'Archived Products')

@section('content')

<style>
    #sellerArchiveStage {
        position: relative;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
    }

    #sellerArchiveSkeleton {
        display: block;
    }

    #sellerArchiveContent {
        opacity: 0;
        visibility: hidden;
        transform: translateY(3px);
    }

    #sellerArchiveStage.seller-archive-ready #sellerArchiveSkeleton {
        display: none;
    }

    #sellerArchiveStage.seller-archive-ready #sellerArchiveContent {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        transition:
            opacity .28s cubic-bezier(.22,1,.36,1),
            transform .28s cubic-bezier(.22,1,.36,1);
    }

    /* Simple skeleton only — no shimmer / glossy sweep */
    .seller-archive-skeleton-block {
        display: block;
        background: #e9e5df;
        animation: sellerArchiveSoftPulse 1.1s ease-in-out infinite;
    }

    @keyframes sellerArchiveSoftPulse {
        0%, 100% { opacity: .56; }
        50% { opacity: .94; }
    }

    .archive-row {
        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            transform .18s ease,
            background-color .18s ease;
    }

    .archive-row:hover {
        transform: translateY(-1px);
        border-color: #dfd3c2;
        background: #fffefa;
        box-shadow: 0 10px 22px rgba(36,32,26,.04);
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-archive-skeleton-block {
            animation: none !important;
        }

        #sellerArchiveContent,
        .archive-row {
            transition: none !important;
        }
    }
</style>

@php
    $totalHistoryCount = $products->count();
    $archivedCount = $products->where('archive_reason', '!=', 'deleted')->count();
    $deletedCount = $products->where('archive_reason', 'deleted')->count();
    $approvedHistoryCount = $products->where('moderation_status', 'approved')->count();
@endphp
<div id="sellerArchiveStage" class="mx-auto w-full max-w-[1800px]">

    <div id="sellerArchiveSkeleton" aria-hidden="true" class="w-full">
        <section class="mb-4 flex flex-col gap-3 px-1 pt-1 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="seller-archive-skeleton-block h-10 w-10 shrink-0 rounded-[12px]"></div>
                <div class="min-w-0 flex-1">
                    <div class="seller-archive-skeleton-block h-6 w-[195px] rounded-full"></div>
                    <div class="seller-archive-skeleton-block mt-2.5 h-2.5 w-[min(470px,72vw)] rounded-full"></div>
                </div>
            </div>
            <div class="seller-archive-skeleton-block h-10 w-[132px] rounded-[10px]"></div>
        </section>

        <section class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @for ($i = 0; $i < 4; $i++)
                <article class="rounded-[16px] border border-[#ebe5dc] bg-white p-4 shadow-[0_8px_22px_rgba(35,29,22,0.025)]">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="seller-archive-skeleton-block h-3 w-[105px] rounded-full"></div>
                            <div class="seller-archive-skeleton-block mt-3 h-8 w-[52px] rounded-lg"></div>
                            <div class="seller-archive-skeleton-block mt-3 h-2.5 w-[145px] rounded-full"></div>
                        </div>
                        <div class="seller-archive-skeleton-block h-10 w-10 rounded-[12px]"></div>
                    </div>
                </article>
            @endfor
        </section>

        <section class="overflow-hidden rounded-[20px] border border-[#ebe5dc] bg-white shadow-[0_10px_24px_rgba(35,29,22,0.025)]">
            <div class="border-b border-[#f1ece4] px-5 py-5 sm:px-6 lg:px-7">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <div class="seller-archive-skeleton-block h-4 w-[145px] rounded-full"></div>
                        <div class="seller-archive-skeleton-block mt-2 h-2.5 w-[250px] rounded-full"></div>
                    </div>
                    <div class="grid w-full grid-cols-1 gap-3 sm:grid-cols-[minmax(260px,1fr)_180px] xl:w-[520px]">
                        <div class="seller-archive-skeleton-block h-10 rounded-[10px]"></div>
                        <div class="seller-archive-skeleton-block h-10 rounded-[10px]"></div>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <div class="seller-archive-skeleton-block h-2.5 w-[130px] rounded-full"></div>
                    <div class="seller-archive-skeleton-block h-8 w-[90px] rounded-lg"></div>
                </div>
            </div>

            <div class="space-y-3 p-4 sm:p-5 lg:p-6">
                @for ($i = 0; $i < 4; $i++)
                    <div class="grid overflow-hidden rounded-[16px] border border-[#ebe5dc] bg-white xl:grid-cols-[minmax(330px,1.8fr)_minmax(180px,1fr)_minmax(120px,.7fr)_minmax(120px,.7fr)_minmax(160px,.9fr)_100px] xl:gap-4">
                        <div class="flex min-w-0 items-center gap-3 border-b border-[#f3efe8] p-3.5 xl:border-b-0">
                            <div class="seller-archive-skeleton-block h-[68px] w-[68px] shrink-0 rounded-[12px]"></div>
                            <div class="min-w-0 flex-1">
                                <div class="seller-archive-skeleton-block h-4 w-[58%] rounded-full"></div>
                                <div class="seller-archive-skeleton-block mt-2.5 h-2.5 w-[42%] rounded-full"></div>
                                <div class="mt-3 flex gap-3">
                                    <div class="seller-archive-skeleton-block h-2.5 w-[75px] rounded-full"></div>
                                    <div class="seller-archive-skeleton-block h-2.5 w-[60px] rounded-full"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center px-4 py-3 xl:px-0"><div class="seller-archive-skeleton-block h-3 w-[72%] rounded-full"></div></div>
                        <div class="flex items-center px-4 py-3 xl:px-0"><div class="seller-archive-skeleton-block h-7 w-[72px] rounded-full"></div></div>
                        <div class="flex items-center px-4 py-3 xl:px-0"><div class="seller-archive-skeleton-block h-4 w-[88px] rounded-full"></div></div>
                        <div class="flex items-center px-4 py-3 xl:px-0"><div class="seller-archive-skeleton-block h-3 w-[82px] rounded-full"></div></div>
                        <div class="flex items-center justify-end px-4 py-4 xl:px-0 xl:pr-4"><div class="seller-archive-skeleton-block h-10 w-10 rounded-[10px]"></div></div>
                    </div>
                @endfor
            </div>
        </section>
    </div>

    <div id="sellerArchiveContent">
        <div class="w-full text-[#27221d]" data-archive-page-ready>

    @if (session('success'))
        <div class="mb-5 flex items-start gap-3 rounded-[18px] border border-[#d6e6dc] bg-[#f7fbf8] px-4 py-4 shadow-[0_8px_20px_rgba(36,32,26,0.035)] sm:px-5">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#ddebe2] bg-white text-[#5f836a]">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="m7 12 3 3 7-7"></path>
                    <circle cx="12" cy="12" r="9"></circle>
                </svg>
            </span>

            <div class="min-w-0">
                <p class="text-[clamp(.84rem,.80rem+.12vw,.92rem)] font-bold text-[#426952]">
                    Success
                </p>
                <p class="mt-1 text-[clamp(.78rem,.74rem+.10vw,.86rem)] leading-6 text-[#5f7867]">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 flex items-start gap-3 rounded-[18px] border border-[#ecd5d5] bg-[#fff8f8] px-4 py-4 shadow-[0_8px_20px_rgba(36,32,26,0.035)] sm:px-5">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#f0dfdf] bg-white text-[#b16666]">
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="M12 8v5"></path>
                    <path d="M12 16.5h.01"></path>
                    <circle cx="12" cy="12" r="9"></circle>
                </svg>
            </span>

            <div class="min-w-0">
                <p class="text-[clamp(.84rem,.80rem+.12vw,.92rem)] font-bold text-[#9f5f5f]">
                    Action required
                </p>
                <p class="mt-1 text-[clamp(.78rem,.74rem+.10vw,.86rem)] leading-6 text-[#876767]">
                    {{ $errors->first() }}
                </p>
            </div>
        </div>
    @endif

    <section class="mb-4 flex flex-col gap-3 px-1 pt-1 lg:flex-row lg:items-end lg:justify-between">
        <div class="flex items-center gap-3">
            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-[12px] border border-[#eadfcf] bg-[#fffaf2] text-[#bb8120]">
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 7h16"></path>
                    <path d="M6 7v12h12V7"></path>
                    <path d="M9 11h6"></path>
                </svg>
            </div>

            <div class="min-w-0">
                <h2 class="text-[clamp(1.72rem,1.58rem+.34vw,2.05rem)] font-semibold leading-none tracking-[-0.04em]">
                    <span class="text-[#201b16]">Archive</span>
                    <span class="text-[#d39116]">Products</span>
                </h2>
                <p class="mt-2 max-w-[720px] text-[10.5px] font-normal leading-5 text-[#887f75]">
                    Search, review, and restore inactive product records from your catalog.
                </p>
            </div>
        </div>

        <a
            href="{{ route('seller.dashboard') }}"
            wire:navigate
            class="inline-flex h-10 w-fit items-center justify-center gap-2 rounded-[10px] border border-[#e3dacd] bg-white px-3.5 text-[9.5px] font-medium text-[#5f564b] transition duration-200 hover:border-[#d4be8e] hover:bg-[#fffaf1] hover:text-[#9a6b1d]"
        >
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                <path d="m15 18-6-6 6-6"></path>
            </svg>
            Back to Dashboard
        </a>
    </section>

    {{-- =========================================================
        SUMMARY CARDS — DASHBOARD-STYLE ENTERPRISE KPI CARDS
    ========================================================== --}}
    <section class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">

        <article class="rounded-[16px] border border-[#dbe5f0] bg-white px-4 py-5 shadow-[0_8px_22px_rgba(35,29,22,0.035)] transition duration-200 hover:-translate-y-[1px] hover:shadow-[0_12px_26px_rgba(35,29,22,0.05)]">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-[11px] font-medium text-[#5f574f]">
                        Total History
                    </p>

                    <p class="mt-2 text-[31px] font-semibold leading-none tracking-[-0.04em] text-[#211d18]">
                        {{ $totalHistoryCount }}
                    </p>

                    <p class="mt-2 text-[9.5px] font-normal text-[#8f867c]">
                        All inactive product records
                    </p>
                </div>

                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-[12px] bg-[#f1f6fc] text-[#4b78ad]">
                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="8"></circle>
                        <path d="M12 7v5l3 2"></path>
                    </svg>
                </span>
            </div>
        </article>

        <article class="rounded-[16px] border border-[#eee0c8] bg-white px-4 py-5 shadow-[0_8px_22px_rgba(35,29,22,0.035)] transition duration-200 hover:-translate-y-[1px] hover:shadow-[0_12px_26px_rgba(35,29,22,0.05)]">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-[11px] font-medium text-[#5f574f]">
                        Archived
                    </p>

                    <p class="mt-2 text-[31px] font-semibold leading-none tracking-[-0.04em] text-[#211d18]">
                        {{ $archivedCount }}
                    </p>

                    <p class="mt-2 text-[9.5px] font-normal text-[#8f867c]">
                        Available to restore
                    </p>
                </div>

                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-[12px] bg-[#fff8ea] text-[#bc8120]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7h16"></path>
                        <path d="M6 7v12h12V7"></path>
                        <path d="M9 11h6"></path>
                    </svg>
                </span>
            </div>
        </article>

        <article class="rounded-[16px] border border-[#efdada] bg-white px-4 py-5 shadow-[0_8px_22px_rgba(35,29,22,0.035)] transition duration-200 hover:-translate-y-[1px] hover:shadow-[0_12px_26px_rgba(35,29,22,0.05)]">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-[11px] font-medium text-[#5f574f]">
                        Deleted
                    </p>

                    <p class="mt-2 text-[31px] font-semibold leading-none tracking-[-0.04em] text-[#211d18]">
                        {{ $deletedCount }}
                    </p>

                    <p class="mt-2 text-[9.5px] font-normal text-[#9a7474]">
                        Retained for product history
                    </p>
                </div>

                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-[12px] bg-[#fff3f3] text-[#bc6666]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7h16"></path>
                        <path d="M9 7V4h6v3"></path>
                        <path d="M7 7l1 13h8l1-13"></path>
                    </svg>
                </span>
            </div>
        </article>

        <article class="rounded-[16px] border border-[#d9e8df] bg-white px-4 py-5 shadow-[0_8px_22px_rgba(35,29,22,0.035)] transition duration-200 hover:-translate-y-[1px] hover:shadow-[0_12px_26px_rgba(35,29,22,0.05)]">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-[11px] font-medium text-[#5f574f]">
                        Approved History
                    </p>

                    <p class="mt-2 text-[31px] font-semibold leading-none tracking-[-0.04em] text-[#211d18]">
                        {{ $approvedHistoryCount }}
                    </p>

                    <p class="mt-2 text-[9.5px] font-normal text-[#698172]">
                        Previously approved listings
                    </p>
                </div>

                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-[12px] bg-[#f2f8f4] text-[#59856a]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="8"></circle>
                        <path d="m8.5 12 2.2 2.2 4.8-5"></path>
                    </svg>
                </span>
            </div>
        </article>

    </section>

    <section class="overflow-hidden rounded-[20px] border border-[#ebe5dc] bg-white shadow-[0_10px_24px_rgba(35,29,22,0.03)]">
        <div class="border-b border-[#f1ece4] px-4 py-4 sm:px-5 lg:px-6">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="min-w-0">
                    <h3 class="text-[20px] font-semibold tracking-[-0.025em] text-[#24201b]">
                        Product History
                    </h3>
                    <p class="mt-1 text-[10.5px] font-normal leading-5 text-[#887f75]">
                        Manage product history using quick filters and a smoother restore flow.
                    </p>
                </div>

                <div class="grid w-full grid-cols-1 gap-3 sm:grid-cols-[minmax(260px,1fr)_180px] xl:w-[520px]">
                    <div class="relative">
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9c9488]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg>

                        <input
                            id="archiveProductSearch"
                            type="search"
                            placeholder="Search product name, category, SKU, or brand..."
                            class="h-12 w-full rounded-[11px] border border-[#e4ddd3] bg-white pl-11 pr-4 text-[10.5px] font-normal text-[#3d3730] outline-none transition duration-200 placeholder:text-[#a09689] focus:border-[#cf9530] focus:ring-4 focus:ring-[#cf9530]/10"
                        >
                    </div>

                    <div class="relative">
                        <select
                            id="archiveProductFilter"
                            class="h-12 w-full appearance-none rounded-[11px] border border-[#e4ddd3] bg-white pl-4 pr-10 text-[10.5px] font-medium text-[#4f473f] outline-none transition duration-200 focus:border-[#cf9530] focus:ring-4 focus:ring-[#cf9530]/10"
                        >
                            <option value="">All history</option>
                            <option value="archived">Archived</option>
                            <option value="deleted">Deleted</option>
                        </select>

                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#968b7f]" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <p id="archiveResultCount" class="text-[9.5px] font-medium text-[#9b6c1c]">
                    Showing {{ $products->count() }} of {{ $products->count() }} products
                </p>

                <button
                    id="archiveClearFilters"
                    type="button"
                    class="hidden rounded-lg px-3 py-2 text-[9.5px] font-medium text-[#9b6c1f] transition duration-200 hover:bg-[#fff7e9]"
                >
                    Clear filters
                </button>
            </div>
        </div>

        <div class="hidden border-b border-[#f2ede6] bg-[#fcfbf9] px-6 py-3.5 xl:grid xl:grid-cols-[minmax(330px,1.8fr)_minmax(180px,1fr)_minmax(120px,.7fr)_minmax(120px,.7fr)_minmax(160px,.9fr)_100px] xl:gap-4">
            <div class="text-[9.5px] font-medium text-[#786f65]">Product</div>
            <div class="text-[9.5px] font-medium text-[#786f65]">Category / Brand</div>
            <div class="text-[9.5px] font-medium text-[#786f65]">Status</div>
            <div class="text-[9.5px] font-medium text-[#786f65]">Price</div>
            <div class="text-[9.5px] font-medium text-[#786f65]">Archived Date</div>
            <div class="text-right text-[9.5px] font-medium text-[#786f65]">Action</div>
        </div>

        <div class="p-3.5 sm:p-4 lg:p-5">
            <div id="archiveProductGrid" class="space-y-2.5">
                @forelse ($products as $product)
                    @php
                        $archiveType = $product->archive_reason === 'deleted' ? 'deleted' : 'archived';
                        $isDeleted = $archiveType === 'deleted';

                        $statusClass = match ($product->moderation_status) {
                            'approved' => 'border-[#d7e7dd] bg-[#f4f9f5] text-[#5a8268]',
                            'pending' => 'border-[#d8e4ef] bg-[#f4f8fc] text-[#5a7b98]',
                            'flagged' => 'border-[#edd7d7] bg-[#fff7f7] text-[#af6666]',
                            'rejected' => 'border-[#edd7d7] bg-[#fff7f7] text-[#af6666]',
                            default => 'border-[#e3ddd6] bg-[#f7f5f2] text-[#766d63]',
                        };
                    @endphp

                    <article
                        data-archive-product
                        data-archive-type="{{ $archiveType }}"
                        data-archive-search="{{ strtolower(trim(($product->name ?? '') . ' ' . ($product->category ?? '') . ' ' . ($product->sku ?? '') . ' ' . ($product->brand ?? ''))) }}"
                        class="archive-row grid overflow-hidden rounded-[16px] border border-[#ebe5dc] bg-white shadow-[0_8px_20px_rgba(36,32,26,0.03)] transition duration-200 hover:border-[#ddd4c6] hover:shadow-[0_12px_24px_rgba(36,32,26,0.045)] xl:grid-cols-[minmax(330px,1.8fr)_minmax(180px,1fr)_minmax(120px,.7fr)_minmax(120px,.7fr)_minmax(160px,.9fr)_100px] xl:gap-4"
                    >
                        <div class="flex min-w-0 items-center gap-4 border-b border-[#f3efe8] px-4 py-4.5 xl:border-b-0">
                            <div class="h-[74px] w-[74px] shrink-0 overflow-hidden rounded-[12px] border border-[#ebe5dc] bg-[#f8f6f2] sm:h-[76px] sm:w-[76px] xl:h-[74px] xl:w-[74px]">
                                @if ($product->image_path)
                                    <img
                                        src="{{ route('seller.products.image', $product) }}"
                                        alt="{{ $product->name }}"
                                        class="h-full w-full object-cover"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                @else
                                    <div class="grid h-full w-full place-items-center text-[#b0a79a]">
                                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                            <path d="m6.5 16 3.5-3.5 2.5 2.5 2-2 3 3"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="truncate text-[12px] font-semibold text-[#2c2721]">
                                        {{ $product->name }}
                                    </p>

                                    <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[9px] font-medium {{ $isDeleted ? 'border-[#f1d8d8] bg-[#fff6f6] text-[#af6666]' : 'border-[#ece0c9] bg-[#fffaf1] text-[#a77621]' }}">
                                        {{ $isDeleted ? 'Deleted' : 'Archived' }}
                                    </span>
                                </div>

                                <p class="mt-1 text-[10px] font-normal text-[#5a524a]">
                                    {{ $product->category ?: 'Uncategorized' }}
                                    @if($product->brand)
                                        <span class="px-1.5 text-[#c8bfaf]">•</span>
                                        {{ $product->brand }}
                                    @endif
                                </p>

                                <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-[9px] font-normal text-[#91887e]">
                                    <span>SKU: <strong class="font-semibold text-[#6a6157]">{{ $product->sku ?: 'No SKU' }}</strong></span>
                                    <span>Stock: <strong class="font-semibold text-[#6a6157]">{{ $product->stock ?? 0 }}</strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-4 border-b border-[#f3efe8] px-4 py-3 xl:border-b-0 xl:px-0">
                            <span class="xl:hidden text-[8.8px] font-medium text-[#91887e]">Category / Brand</span>
                            <div class="min-w-0 text-right xl:text-left">
                                <p class="truncate text-[10.5px] font-medium text-[#454038]">
                                    {{ $product->category ?: 'Uncategorized' }}
                                </p>
                                <p class="mt-1 truncate text-[9px] font-normal text-[#91887e]">
                                    {{ $product->brand ?: 'No brand' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-4 border-b border-[#f3efe8] px-4 py-3 xl:border-b-0 xl:px-0">
                            <span class="xl:hidden text-[8.8px] font-medium text-[#91887e]">Status</span>
                            <span class="inline-flex rounded-full border px-3 py-1.5 text-[9px] font-medium {{ $statusClass }}">
                                {{ ucfirst($product->moderation_status) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-4 border-b border-[#f3efe8] px-4 py-3 xl:border-b-0 xl:px-0">
                            <span class="xl:hidden text-[8.8px] font-medium text-[#91887e]">Price</span>
                            <span class="text-[11px] font-semibold text-[#302a24]">
                                ₱{{ number_format((float) $product->price, 2) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-4 border-b border-[#f3efe8] px-4 py-3 xl:border-b-0 xl:px-0">
                            <span class="xl:hidden text-[8.8px] font-medium text-[#91887e]">Archived Date</span>
                            <div class="text-right xl:text-left">
                                <p class="text-[10px] font-medium text-[#4a443d]">
                                    {{ $product->archived_at?->format('M d, Y') ?: '—' }}
                                </p>
                                @if($product->archived_at)
                                    <p class="mt-1 text-[8.8px] font-normal text-[#91887e]">
                                        {{ $product->archived_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between px-4 py-4 xl:justify-end xl:px-0 xl:pr-4">
                            <span class="xl:hidden text-[8.8px] font-medium text-[#91887e]">Action</span>

                            <form
                                method="POST"
                                action="{{ route('seller.products.restore', $product) }}"
                                class="archive-restore-form"
                                data-product-row
                            >
                                @csrf

                                <button
                                    type="submit"
                                    title="Restore product"
                                    aria-label="Restore product"
                                    @if ($seller->isSuspended()) disabled @endif
                                    class="group inline-flex h-11 w-11 items-center justify-center rounded-[11px] border border-[#e5dccd] bg-white text-[#bf8525] shadow-[0_6px_16px_rgba(36,32,26,0.03)] transition duration-200 hover:border-[#d5be8e] hover:bg-[#fff9f0] hover:text-[#a87216] disabled:cursor-not-allowed disabled:border-[#e6e0d7] disabled:bg-[#f7f4ef] disabled:text-[#c5bcaf]"
                                >
                                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M3 12a9 9 0 1 0 3-6.7"></path>
                                        <path d="M3 3v6h6"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[22px] border border-dashed border-[#ddd5ca] bg-[#fcfbf9] px-6 py-16 text-center shadow-[0_10px_24px_rgba(36,32,26,0.025)]">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-xl border border-[#ebe5dc] bg-white text-[#a69d90]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 7h16"></path>
                                <path d="M6 7v12h12V7"></path>
                                <path d="M9 11h6"></path>
                            </svg>
                        </div>

                        <p class="mt-4 text-[clamp(1rem,.94rem+.18vw,1.12rem)] font-bold text-[#50483f]">
                            No archived products
                        </p>

                        <p class="mx-auto mt-2 max-w-[520px] text-[clamp(.78rem,.74rem+.10vw,.86rem)] leading-6 text-[#92887b]">
                            Products you archive or remove from your active catalog will appear here.
                        </p>
                    </div>
                @endforelse
            </div>

            <div
                id="archiveFilterEmpty"
                class="hidden rounded-[22px] border border-dashed border-[#ddd5ca] bg-[#fcfbf9] px-6 py-16 text-center shadow-[0_10px_24px_rgba(36,32,26,0.025)]"
            >
                <div class="mx-auto grid h-12 w-12 place-items-center rounded-xl border border-[#ebe5dc] bg-white text-[#a69d90]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="6"></circle>
                        <path d="m16 16 4 4"></path>
                    </svg>
                </div>

                <p class="mt-4 text-[clamp(1rem,.94rem+.18vw,1.12rem)] font-bold text-[#50483f]">
                    No matching products
                </p>

                <p class="mt-2 text-[clamp(.78rem,.74rem+.10vw,.86rem)] text-[#92887b]">
                    Try another keyword or change the archive filter.
                </p>

                <button
                    id="archiveEmptyClear"
                    type="button"
                    class="mt-5 inline-flex h-10 items-center justify-center rounded-xl border border-[#e2d8c8] bg-white px-4 text-[clamp(.80rem,.76rem+.10vw,.90rem)] font-semibold text-[#956718] transition hover:bg-[#fffaf2]"
                >
                    Clear Filters
                </button>
            </div>
        </div>
    </section>

    <div id="archiveToastContainer" class="pointer-events-none fixed bottom-5 right-5 z-[80] flex w-[min(92vw,360px)] flex-col gap-3"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const initArchivePage = () => {
        const stage = document.getElementById('sellerArchiveStage');
        const shell = document.querySelector('[data-archive-page-ready]');

        if (stage) {
            stage.classList.remove('seller-archive-ready');
            window.clearTimeout(window.__SARI_ARCHIVE_SKELETON_TIMER__);
            window.__SARI_ARCHIVE_SKELETON_TIMER__ = window.setTimeout(() => {
                stage.classList.add('seller-archive-ready');
            }, 1250);
        }
        const searchInput = document.getElementById('archiveProductSearch');
        const typeFilter = document.getElementById('archiveProductFilter');
        const clearButton = document.getElementById('archiveClearFilters');
        const emptyClearButton = document.getElementById('archiveEmptyClear');
        const resultCount = document.getElementById('archiveResultCount');
        const grid = document.getElementById('archiveProductGrid');
        const filterEmpty = document.getElementById('archiveFilterEmpty');
        const toastContainer = document.getElementById('archiveToastContainer');

        const cards = Array.from(document.querySelectorAll('[data-archive-product]'));
        const restoreForms = Array.from(document.querySelectorAll('.archive-restore-form'));

        function showToast(type, message) {
            if (!toastContainer) return;

            const tone = type === 'error'
                ? {
                    border: '#ecd5d5',
                    bg: '#fff9f9',
                    title: '#9f5f5f',
                    text: '#866767'
                }
                : {
                    border: '#d8e6dd',
                    bg: '#f8fbf9',
                    title: '#507560',
                    text: '#5d7566'
                };

            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto translate-y-2 opacity-0 rounded-2xl border px-4 py-3 shadow-[0_14px_30px_rgba(35,29,22,0.08)] transition duration-300';
            toast.style.borderColor = tone.border;
            toast.style.backgroundColor = tone.bg;
            toast.innerHTML = `
                <p style="color:${tone.title}" class="text-[0.88rem] font-bold">${type === 'error' ? 'Action required' : 'Success'}</p>
                <p style="color:${tone.text}" class="mt-1 text-[0.80rem] leading-5">${message}</p>
            `;

            toastContainer.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => toast.remove(), 260);
            }, 2800);
        }

        function visibleCards() {
            return cards.filter((card) => !card.classList.contains('hidden'));
        }

        function updateResultCount() {
            if (!resultCount) return;
            const visible = visibleCards().length;
            resultCount.textContent =
                'Showing ' + visible +
                ' of ' + cards.length +
                ' product' + (cards.length === 1 ? '' : 's');
        }

        function filterArchive() {
            const query = (searchInput?.value || '').trim().toLowerCase();
            const type = (typeFilter?.value || '').trim().toLowerCase();
            let visible = 0;

            cards.forEach((card) => {
                const searchable = (card.dataset.archiveSearch || '').toLowerCase();
                const archiveType = (card.dataset.archiveType || '').toLowerCase();
                const matchesQuery = query === '' || searchable.includes(query);
                const matchesType = type === '' || archiveType === type;
                const matches = matchesQuery && matchesType;

                card.classList.toggle('hidden', !matches);
                if (matches) visible++;
            });

            const hasFilters = query !== '' || type !== '';
            clearButton?.classList.toggle('hidden', !hasFilters);

            if (cards.length > 0) {
                filterEmpty?.classList.toggle('hidden', visible !== 0);
                grid?.classList.toggle('hidden', visible === 0);
            }

            updateResultCount();
        }

        function clearFilters() {
            if (searchInput) searchInput.value = '';
            if (typeFilter) typeFilter.value = '';
            filterArchive();
            searchInput?.focus();
        }

        async function handleRestoreSubmit(event) {
            event.preventDefault();

            const form = event.currentTarget;
            const button = form.querySelector('button[type="submit"]');
            const row = form.closest('[data-archive-product]');

            if (!form || !button || !row || button.disabled) return;

            const original = button.innerHTML;
            button.disabled = true;
            button.innerHTML = `
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px] animate-spin" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 12a9 9 0 1 1-2.64-6.36"></path>
                </svg>
            `;

            try {
                const formData = new FormData(form);

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html,application/xhtml+xml'
                    },
                    body: formData,
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error('Unable to restore product right now.');
                }

                row.style.transition = 'opacity .25s ease, transform .25s ease, max-height .25s ease, margin .25s ease, padding .25s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateY(-4px)';
                row.style.maxHeight = row.offsetHeight + 'px';

                requestAnimationFrame(() => {
                    row.style.maxHeight = '0px';
                    row.style.marginTop = '0px';
                    row.style.marginBottom = '0px';
                    row.style.paddingTop = '0px';
                    row.style.paddingBottom = '0px';
                });

                setTimeout(() => {
                    row.remove();
                    const index = cards.indexOf(row);
                    if (index >= 0) cards.splice(index, 1);

                    filterArchive();

                    if (cards.length === 0 && grid) {
                        grid.innerHTML = `
                            <div class="rounded-[22px] border border-dashed border-[#ddd5ca] bg-[#fcfbf9] px-6 py-16 text-center shadow-[0_10px_24px_rgba(36,32,26,0.025)]">
                                <div class="mx-auto grid h-12 w-12 place-items-center rounded-xl border border-[#ebe5dc] bg-white text-[#a69d90]">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 7h16"></path>
                                        <path d="M6 7v12h12V7"></path>
                                        <path d="M9 11h6"></path>
                                    </svg>
                                </div>
                                <p class="mt-4 text-[1.05rem] font-bold text-[#50483f]">No archived products</p>
                                <p class="mx-auto mt-2 max-w-[520px] text-[0.84rem] leading-6 text-[#92887b]">
                                    Products you archive or remove from your active catalog will appear here.
                                </p>
                            </div>
                        `;
                    }

                    showToast('success', 'Product restored successfully.');
                }, 260);
            } catch (error) {
                button.disabled = false;
                button.innerHTML = original;
                showToast('error', error?.message || 'Unable to restore product.');
            }
        }

        searchInput?.addEventListener('input', filterArchive);
        typeFilter?.addEventListener('change', filterArchive);
        clearButton?.addEventListener('click', clearFilters);
        emptyClearButton?.addEventListener('click', clearFilters);

        restoreForms.forEach((form) => {
            if (form.dataset.bound === 'yes') return;
            form.dataset.bound = 'yes';
            form.addEventListener('submit', handleRestoreSubmit);
        });

        filterArchive();
    };

    document.addEventListener('DOMContentLoaded', initArchivePage);
    document.addEventListener('livewire:navigated', initArchivePage);

    document.addEventListener('livewire:navigating', function () {
        window.clearTimeout(window.__SARI_ARCHIVE_SKELETON_TIMER__);
        window.__SARI_ARCHIVE_SKELETON_TIMER__ = null;
    });
})();
</script>
@endpush
