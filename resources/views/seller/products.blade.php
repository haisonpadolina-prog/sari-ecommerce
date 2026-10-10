@extends('layouts.seller')

@section('title', 'Products — SARI')
@section('page-title', 'Product Management')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/products.css') }}?v=20261007-1">
{{-- Cacheable page stylesheet; CSS rules are unchanged from the previous inline blocks. --}}
@endpush

@section('content')


@php
    /*
    | Reuse the seller account already passed to this page.
    | No database query is added here.
    */
    $warningCount = (int) ($sellerAccount->warning_count ?? 0);
    $activeSuspension = $sellerAccount->suspended_until
        ? now()->lt(\Illuminate\Support\Carbon::parse($sellerAccount->suspended_until))
        : false;
    $sellerLocked = $warningCount >= 3 || $activeSuspension;
@endphp

@php
    /*
     * First-screen products arrive from SellerProductController@index.
     * This removes the skeleton -> AJAX -> render wait from initial navigation.
     */
    $initialProductLibrary = $initialProductLibrary ?? [
        'success' => true,
        'server_now' => now()->toIso8601String(),
        'count' => 0,
        'initial_count' => 0,
        'products' => [],
    ];

    $initialProducts = collect($initialProductLibrary['products'] ?? []);
@endphp


<script>
(function () {
    const libraryUrl = @json(route('seller.products.library'));
    const sellerId = @json((int) ($sellerAccount->id ?? 0));
    const cacheKey = 'sari:seller-products-library:v6:' + sellerId + ':' + libraryUrl;

    try {
        window.sessionStorage.removeItem('sari:seller-products-library:v5:' + libraryUrl);
    } catch (_) {}
    const maxCacheAgeMs = 10 * 60 * 1000;
    const serverInitial = @json($initialProductLibrary ?? null);

    window.__SARI_PRODUCTS_LIBRARY_URL__ = libraryUrl;
    window.__SARI_PRODUCTS_LIBRARY_CACHE_KEY__ = cacheKey;
    window.__SARI_PRODUCTS_SERVER_INITIAL__ = serverInitial;

    function isCompleteLibrary(data) {
        if (!data || !Array.isArray(data.products)) return false;

        const declaredCount = Number(data.count);

        if (Number.isFinite(declaredCount)) {
            return data.products.length >= Math.max(0, declaredCount);
        }

        return !Object.prototype.hasOwnProperty.call(data, 'initial_count');
    }

    /*
     * Reuse a complete Product Library cache before falling back to the
     * server-rendered first 12 products. Most importantly, never overwrite a
     * complete cache with the partial first-screen payload on every visit.
     */
    let cachedEntry = null;

    try {
        const raw = window.sessionStorage.getItem(cacheKey);

        if (raw) {
            const stored = JSON.parse(raw);
            const age = Date.now() - Number(stored?.saved_at || 0);

            if (
                stored?.data
                && Array.isArray(stored.data.products)
                && age >= 0
                && age <= maxCacheAgeMs
                && isCompleteLibrary(stored.data)
            ) {
                cachedEntry = stored;
            }
        }
    } catch (_) {}

    const selectedLibrary = cachedEntry?.data || serverInitial || null;
    const selectedLibraryIsComplete = isCompleteLibrary(selectedLibrary);

    if (selectedLibrary && Array.isArray(selectedLibrary.products)) {
        window.__SARI_PRODUCTS_LIBRARY_CACHED__ = selectedLibrary;
        window.__SARI_PRODUCTS_LIBRARY_CACHE_IS_COMPLETE__ = selectedLibraryIsComplete;
        window.__SARI_PRODUCTS_LIBRARY_CACHE_SAVED_AT__ = cachedEntry?.saved_at || Date.now();

        /*
         * Only persist a complete payload. Serializing the partial first-screen
         * data during every Livewire navigation adds synchronous work and used
         * to destroy the useful full-library cache.
         */
        if (!cachedEntry && selectedLibraryIsComplete) {
            try {
                window.sessionStorage.setItem(
                    cacheKey,
                    JSON.stringify({
                        saved_at: Date.now(),
                        data: selectedLibrary
                    })
                );
            } catch (_) {}
        }
    }

    window.__SARI_PRODUCTS_EARLY_ABORT__?.abort();
    const earlyAbort = new AbortController();
    window.__SARI_PRODUCTS_EARLY_ABORT__ = earlyAbort;

    function fetchFullLibrary() {
        return fetch(libraryUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            /*
             * Respect the endpoint/browser cache. Dashboard may already have
             * warmed this exact JSON response before Product Management opens.
             * Mutation refreshes still use no-store from seller-products.js.
             */
            cache: 'default',
            signal: earlyAbort.signal
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Unable to load Product Library.');
                }

                return response.json();
            })
            .then(function (data) {
                window.__SARI_PRODUCTS_LIBRARY_CACHED__ = data;
                window.__SARI_PRODUCTS_LIBRARY_CACHE_IS_COMPLETE__ = isCompleteLibrary(data);
                window.__SARI_PRODUCTS_LIBRARY_CACHE_SAVED_AT__ = Date.now();

                if (window.__SARI_PRODUCTS_LIBRARY_CACHE_IS_COMPLETE__) {
                    try {
                        window.sessionStorage.setItem(
                            cacheKey,
                            JSON.stringify({
                                saved_at: Date.now(),
                                data: data
                            })
                        );
                    } catch (_) {}
                }

                return data;
            })
            .catch(function (error) {
                if (error?.name !== 'AbortError') {
                    window.__SARI_PRODUCTS_LIBRARY_PREFETCH_ERROR__ = error;
                }
                return null;
            });
    }

    /*
     * The first 12 cards are already server-rendered. When a complete cache is
     * available, let the browser paint first and revalidate later so the JSON
     * request cannot compete with the navigation-critical CSS/JS/images.
     */
    let idleHandle = null;
    let timeoutHandle = null;

    window.__SARI_PRODUCTS_LIBRARY_PROMISE__ =
        new Promise(function (resolve) {
            const run = function () {
                if (earlyAbort.signal.aborted) {
                    resolve(null);
                    return;
                }

                fetchFullLibrary().then(resolve);
            };

            const scheduleIdle = function () {
                if (earlyAbort.signal.aborted) {
                    resolve(null);
                    return;
                }

                if ('requestIdleCallback' in window) {
                    idleHandle = window.requestIdleCallback(run, {
                        timeout: selectedLibraryIsComplete ? 1400 : 700
                    });
                } else {
                    timeoutHandle = window.setTimeout(
                        run,
                        selectedLibraryIsComplete ? 260 : 180
                    );
                }
            };

            if (selectedLibraryIsComplete) {
                timeoutHandle = window.setTimeout(function () {
                    timeoutHandle = null;
                    scheduleIdle();
                }, 650);
            } else {
                scheduleIdle();
            }

            document.addEventListener('livewire:navigating', function () {
                if (idleHandle !== null && 'cancelIdleCallback' in window) {
                    window.cancelIdleCallback(idleHandle);
                    idleHandle = null;
                }

                if (timeoutHandle !== null) {
                    window.clearTimeout(timeoutHandle);
                    timeoutHandle = null;
                }

                earlyAbort.abort();
                resolve(null);
            }, { once: true });
        });
})();
</script>



<div class="seller-products-page mx-auto w-full max-w-[1800px]" style="--sari-products-flash-sale-strip-image: url('{{ asset('images/seller/flash-sale-strip.png') }}');">

    @if (session('success') || session('warning') || session('info') || $errors->any())
        @php
            $pageNotice = $errors->any()
                ? $errors->first()
                : (session('warning') ?: (session('success') ?: session('info')));
            $pageNoticeDanger = $errors->any() || session('warning');
        @endphp
        <div class="mb-4 rounded-[16px] border px-4 py-3 {{ $pageNoticeDanger ? 'border-[#efd3d3] bg-[#fff7f7] text-[#9f5555]' : 'border-[#d7e8dd] bg-[#f6fbf8] text-[#4f7d61]' }}">
            <p class="text-[10px] font-semibold">{{ $pageNotice }}</p>
        </div>
    @endif

    <div id="productsClientNotice" class="mb-4 hidden rounded-[15px] border border-[#d5e5da] bg-[#f5fbf7] px-4 py-3 text-[9.5px] font-semibold text-[#4f7d61]"></div>

    {{-- PAGE HEADER — CLEAN / OPEN, NO DARK CONTAINER --}}
    <section class="px-1 pt-1 sm:px-2">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="seller-products-premium-heading min-w-0">
                <p class="seller-products-premium-kicker">Seller Catalog</p>
                <h1 class="seller-products-premium-title" style="font-size:clamp(30px,2.35vw,38px)!important;line-height:1!important;letter-spacing:-.035em!important;font-weight:650!important;">
                    <span>Your</span>
                    <span>Products</span>
                </h1>
                <p class="seller-products-premium-subtitle">
                    Manage, review, and refine every listing from one premium catalog workspace.
                </p>
            </div>
        </div>
    </section>

    {{-- SELLER CATALOG STUDIO — LEFT CONTROL RAIL + RIGHT PRODUCT WORKSPACE --}}
    <section class="seller-catalog-studio mt-1 grid grid-cols-1 gap-5 xl:grid-cols-[310px_minmax(0,1fr)]">

        {{-- LEFT: INVENTORY / CATALOG CONTROL RAIL --}}
        <aside class="seller-catalog-rail self-start xl:sticky xl:top-[88px]">
            <div class="seller-catalog-rail-shell rounded-[20px] border border-[#e8dfd3] bg-[#fbfaf7] p-4.5 sm:p-5">

                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#b77912]">Seller Inventory</p>
                        <h2 class="mt-1 text-[18px] font-bold tracking-[-0.035em] text-[#28221c]">Catalog Control</h2>
                    </div>

                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-[12px] border border-[#eadab7] bg-[#fff8e9] text-[#b97913]">
                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h16"></path>
                            <path d="M5 7l2 12h10l2-12"></path>
                            <path d="M9 11h6"></path>
                        </svg>
                    </span>
                </div>

                {{-- INVENTORY METRICS --}}
                <div class="mt-5">
                    <div class="mb-2.5 flex items-center justify-between gap-3">
                        <h3 class="text-[10.5px] font-semibold text-[#494038]">Inventory Overview</h3>
                        <span class="seller-live-catalog inline-flex items-center gap-1.5 text-[8px] font-medium text-[#6f756f]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#2f9562]"></span>
                            Live catalog
                        </span>
                    </div>

                    <div class="seller-inventory-overview-grid grid grid-cols-2 gap-2.5">
                        @foreach ([
                            ['id' => 'productsTotalCount', 'label' => 'Active', 'tone' => 'text-[#c98208]', 'type' => 'active'],
                            ['id' => 'productsApprovedCount', 'label' => 'Approved', 'tone' => 'text-[#2f9562]', 'type' => 'approved'],
                            ['id' => 'productsLowStockCount', 'label' => 'Low Stock', 'tone' => 'text-[#c98208]', 'type' => 'low'],
                            ['id' => 'productsOutStockCount', 'label' => 'Out', 'tone' => 'text-[#c94f55]', 'type' => 'out'],
                        ] as $item)
                            <div class="seller-rail-stat">
                                <span class="seller-rail-stat-icon {{ $item['tone'] }}" aria-hidden="true">
                                    @switch($item['type'])
                                        @case('approved')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg>
                                            @break
                                        @case('low')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 8 12 4l8 4-8 4-8-4Z"></path><path d="M5 11v6l7 3 7-3v-6"></path></svg>
                                            @break
                                        @case('out')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"></circle><path d="m9 9 6 6"></path><path d="m15 9-6 6"></path></svg>
                                            @break
                                        @default
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 7h14l-1 13H6L5 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path></svg>
                                    @endswitch
                                </span>

                                <div class="seller-rail-stat-copy">
                                    <p class="seller-rail-stat-label">{{ $item['label'] }}</p>
                                    <span id="{{ $item['id'] }}" class="seller-rail-stat-value">—</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- CATALOG HEALTH --}}
                <div class="seller-rail-health seller-rail-health--premium mt-4 rounded-[16px] border border-[#e7dccb] bg-white p-4" style="background:#FFFFFF!important;background-color:#FFFFFF!important;background-image:none!important;box-shadow:none!important;filter:none!important;border-color:#E8DED6!important;">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[8px] font-bold uppercase tracking-[.11em] text-[#a57a2b]">Catalog Health</p>
                            <div class="mt-2 flex items-end gap-1">
                                <strong id="productsCatalogHealthScore" class="seller-catalog-health-score text-[30px] font-bold leading-none text-[#c48a13]">0</strong>
                                <span class="pb-0.5 text-[9px] font-semibold text-[#a69d92]">/100</span>
                            </div>
                        </div>

                        <span id="productsCatalogHealthLabel" class="rounded-full border border-[#eadfc9] bg-[#fffaf1] px-2.5 py-1 text-[7.5px] font-semibold text-[#9a6817]">Analyzing</span>
                    </div>

                    <div class="seller-catalog-health-progress mt-3">
                        <div id="productsCatalogHealthBar"></div>
                    </div>

                    <p id="productsCatalogAttention" class="mt-2.5 text-[8.5px] font-medium leading-4 text-[#81786e]">—</p>

                    <div class="seller-health-status-grid mt-3 border-t border-[#f0ebe3] pt-3" style="background:#FFFFFF!important;box-shadow:none!important;filter:none!important;">
                        <span class="seller-health-status-item seller-health-status-item--approved">
                            <strong id="productsHealthApproved">0</strong>
                            <span>approved</span>
                        </span>
                        <span class="seller-health-status-item seller-health-status-item--review">
                            <strong id="productsHealthReview">0</strong>
                            <span>review</span>
                        </span>
                        <span class="seller-health-status-item seller-health-status-item--low">
                            <strong id="productsHealthLow">0</strong>
                            <span>low stock</span>
                        </span>
                        <span class="seller-health-status-item seller-health-status-item--out">
                            <strong id="productsHealthOut">0</strong>
                            <span>out</span>
                        </span>
                    </div>
                </div>

                {{-- MORE CATALOG TOOLS — COLLAPSED BY DEFAULT --}}
                <details class="seller-rail-more mt-3">
                    <summary class="seller-rail-more-toggle">
                        <span class="flex min-w-0 items-center gap-2">
                            <svg viewBox="0 0 24 24" class="h-[14px] w-[14px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M4 7h16"></path>
                                <path d="M4 12h16"></path>
                                <path d="M4 17h16"></path>
                            </svg>
                            <span>View more</span>
                        </span>

                        <svg viewBox="0 0 24 24" class="seller-rail-more-chevron h-[14px] w-[14px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </summary>

                    <div class="seller-rail-more-content">
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <h3 class="text-[10px] font-semibold text-[#494038]">Quick Views</h3>
                            <span class="text-[7.5px] text-[#a0978d]">Filter catalog</span>
                        </div>

                        <div class="grid gap-1.5">
                            <button type="button" class="seller-products-quick-filter is-active" data-products-quick-filter="all">
                                <span class="flex items-center gap-2"><span class="seller-quick-dot bg-[#c98d19]"></span>All Products</span>
                                <span id="productsQuickAll" class="seller-products-quick-count">0</span>
                            </button>

                            <button type="button" class="seller-products-quick-filter" data-products-quick-filter="approved">
                                <span class="flex items-center gap-2"><span class="seller-quick-dot bg-[#4f8065]"></span>Approved</span>
                                <span id="productsQuickApproved" class="seller-products-quick-count">0</span>
                            </button>

                            <button type="button" class="seller-products-quick-filter" data-products-quick-filter="pending">
                                <span class="flex items-center gap-2"><span class="seller-quick-dot bg-[#537a9f]"></span>Under Review</span>
                                <span id="productsQuickPending" class="seller-products-quick-count">0</span>
                            </button>

                            <button type="button" class="seller-products-quick-filter" data-products-quick-filter="low">
                                <span class="flex items-center gap-2"><span class="seller-quick-dot bg-[#c48a13]"></span>Low Stock</span>
                                <span id="productsQuickLow" class="seller-products-quick-count">0</span>
                            </button>

                            <button type="button" class="seller-products-quick-filter" data-products-quick-filter="out">
                                <span class="flex items-center gap-2"><span class="seller-quick-dot bg-[#b65e5e]"></span>Out of Stock</span>
                                <span id="productsQuickOut" class="seller-products-quick-count">0</span>
                            </button>
                        </div>
                    </div>
                </details>
            </div>
        </aside>

        {{-- RIGHT: PRODUCT WORKSPACE --}}
        <div class="min-w-0">
            <div class="seller-products-workspace rounded-[20px] border border-[#e8e0d5] bg-white p-4 sm:p-5">
                {{-- FILTERS --}}
                <div class="mt-4 rounded-[14px] border border-[#ebe3d9] bg-[#fbfaf7] p-3">
            <div class="grid grid-cols-1 gap-2.5 lg:grid-cols-[minmax(240px,1.4fr)_150px_150px_160px_auto]">
                <div class="relative">
                    <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8f877e]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path>
                    </svg>
                    <input
                        id="productsSearch"
                        type="search"
                        autocomplete="off"
                        aria-autocomplete="list"
                        aria-controls="productsSearchSuggestions"
                        aria-expanded="false"
                        placeholder="Search product name, category, brand or SKU..."
                        class="seller-products-filter h-11 w-full rounded-[10px] border border-[#dfd7cc] bg-[#fffefa] pl-10 pr-3 text-[9.5px] text-[#403930] outline-none placeholder:text-[#a59d93]"
                    >

                    <div
                        id="productsSearchSuggestions"
                        class="seller-product-search-suggestions hidden"
                        role="listbox"
                        aria-label="Product search suggestions"
                    ></div>
                </div>

                <div class="seller-premium-dropdown" data-products-dropdown data-select-id="productsStatus">
                    <select id="productsStatus" class="sr-only" tabindex="-1" aria-hidden="true">
                        <option value="">All Status</option>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending Review</option>
                        <option value="flagged">Flagged</option>
                        <option value="rejected">Rejected</option>
                        <option value="removed">Removed</option>
                    </select>

                    <button type="button" class="seller-premium-dropdown-trigger" data-products-dropdown-trigger aria-haspopup="listbox" aria-expanded="false">
                        <span class="seller-premium-dropdown-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="8"></circle>
                                <path d="m9 12 2 2 4-5"></path>
                            </svg>
                        </span>
                        <span class="seller-premium-dropdown-label" data-products-dropdown-label>All Status</span>
                        <svg class="seller-premium-dropdown-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </button>

                    <div class="seller-premium-dropdown-menu" data-products-dropdown-menu role="listbox" aria-label="Product status"></div>
                </div>

                <div class="seller-premium-dropdown" data-products-dropdown data-select-id="productsStock">
                    <select id="productsStock" class="sr-only" tabindex="-1" aria-hidden="true">
                        <option value="">All Stock</option>
                        <option value="in-stock">In Stock</option>
                        <option value="low-stock">Low Stock</option>
                        <option value="out-of-stock">Out of Stock</option>
                    </select>

                    <button type="button" class="seller-premium-dropdown-trigger" data-products-dropdown-trigger aria-haspopup="listbox" aria-expanded="false">
                        <span class="seller-premium-dropdown-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M5 7h14v12H5z"></path>
                                <path d="M8 7V5h8v2"></path>
                                <path d="M9 11h6"></path>
                            </svg>
                        </span>
                        <span class="seller-premium-dropdown-label" data-products-dropdown-label>All Stock</span>
                        <svg class="seller-premium-dropdown-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </button>

                    <div class="seller-premium-dropdown-menu" data-products-dropdown-menu role="listbox" aria-label="Stock status"></div>
                </div>

                <div class="seller-premium-dropdown" data-products-dropdown data-select-id="productsCategory">
                    <select id="productsCategory" class="sr-only" tabindex="-1" aria-hidden="true">
                        <option value="">All Categories</option>
                    </select>

                    <button type="button" class="seller-premium-dropdown-trigger" data-products-dropdown-trigger aria-haspopup="listbox" aria-expanded="false">
                        <span class="seller-premium-dropdown-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M20 13 11 22l-8-8 9-9h7v7Z"></path>
                                <circle cx="16" cy="9" r="1.4"></circle>
                            </svg>
                        </span>
                        <span class="seller-premium-dropdown-label" data-products-dropdown-label>All Categories</span>
                        <svg class="seller-premium-dropdown-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </button>

                    <div class="seller-premium-dropdown-menu" data-products-dropdown-menu role="listbox" aria-label="Product category"></div>
                </div>

                <div class="seller-premium-filter-actions">
                    <button id="productsApplyFilters" type="button" class="seller-premium-apply-filter">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="M4 5h16"></path>
                            <path d="M7 10h10"></path>
                            <path d="M10 15h4"></path>
                            <path d="m16 18 2 2 4-5"></path>
                        </svg>
                        <span>Apply Filters</span>
                    </button>

                    <button id="productsClearFilters" type="button" class="seller-premium-clear-filter" title="Clear filters" aria-label="Clear filters">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M5 5 19 19"></path>
                            <path d="M19 5 5 19"></path>
                        </svg>
                        <span class="hidden xl:inline">Clear</span>
                    </button>
                </div>
            </div>
        </div>
<div id="productsGrid" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-3">
            @foreach ($initialProducts as $productIndex => $product)
                @php
                    $moderationStatus = strtolower((string) ($product['moderation_status'] ?? ''));
                    $stockValue = (int) ($product['stock'] ?? 0);
                    $threshold = max(0, (int) ($product['low_stock_threshold'] ?? 5));
                    $discount = min(100, max(0, (float) ($product['discount'] ?? 0)));
                    $effectiveDiscount = min(100, max(0, (float) ($product['effective_discount'] ?? $discount)));
                    $flashSaleActive = (bool) ($product['flash_sale_active'] ?? false);
                    $flashSaleScheduled = in_array($moderationStatus, ['pending', 'flagged'], true)
                        && $discount > 0
                        && !empty($product['flash_sale_ends_at']);

                    $displayedSalePrice = ($flashSaleActive || $flashSaleScheduled)
                        ? round((float) ($product['price'] ?? 0) * (1 - $discount / 100), 2)
                        : (float) ($product['sale_price'] ?? $product['price'] ?? 0);

                    $stockTextClass = $stockValue <= 0
                        ? 'seller-stock-text--out'
                        : ($stockValue <= $threshold
                            ? 'seller-stock-text--low'
                            : 'seller-stock-text--normal');

                    $primaryBadgeLabel = $product['status_label'] ?? $product['moderation_status'] ?? 'Listing';
                    $primaryBadgeClass = 'seller-product-status--neutral';

                    if ($moderationStatus === 'approved') {
                        $primaryBadgeLabel = 'Active';
                        $primaryBadgeClass = 'seller-product-status--active';
                    } elseif ($moderationStatus === 'pending') {
                        $primaryBadgeLabel = 'Pending Review';
                        $primaryBadgeClass = 'seller-product-status--pending';
                    } elseif (in_array($moderationStatus, ['flagged', 'rejected'], true)) {
                        $primaryBadgeLabel = $moderationStatus === 'flagged' ? 'Flagged' : 'Rejected';
                        $primaryBadgeClass = 'seller-product-status--danger';
                    } elseif ($moderationStatus === 'removed') {
                        $primaryBadgeLabel = 'Removed';
                    }

                    $secondaryBadge = null;

                    if ($flashSaleActive || $flashSaleScheduled) {
                        $secondaryBadge = '⚡ -' . rtrim(rtrim(number_format($discount, 2, '.', ''), '0'), '.') . '%';
                    } elseif (!empty($product['on_trend'])) {
                        $secondaryBadge = 'Featured';
                    } elseif (!empty($product['mall_badge'])) {
                        $secondaryBadge = 'Mall';
                    } elseif ($effectiveDiscount > 0) {
                        $secondaryBadge = '-' . rtrim(rtrim(number_format($effectiveDiscount, 2, '.', ''), '0'), '.') . '%';
                    }

                    $searchValue = strtolower(trim(implode(' ', array_filter([
                        $product['name'] ?? null,
                        $product['category'] ?? null,
                        $product['brand'] ?? null,
                        $product['sku'] ?? null,
                    ]))));
                @endphp

                <article
                    data-product-item
                    data-product-id="{{ $product['id'] ?? '' }}"
                    data-search="{{ $searchValue }}"
                    data-status="{{ $product['moderation_status'] ?? '' }}"
                    data-stock="{{ $product['stock_state'] ?? '' }}"
                    data-category="{{ $product['category'] ?? 'Uncategorized' }}"
                    class="seller-products-card seller-product-card--reference border bg-white"
                >
                    <div class="seller-product-card-media">
                        @if (!empty($product['image_url']))
                            <img
                                src="{{ $product['image_url'] }}"
                                alt="{{ $product['name'] ?? 'Product' }}"
                                class="seller-product-image h-full w-full object-cover"
                                loading="{{ $productIndex < 3 ? 'eager' : 'lazy' }}"
                                fetchpriority="{{ $productIndex < 2 ? 'high' : 'auto' }}"
                                decoding="async"
                            >
                        @else
                            <div class="grid h-full w-full place-items-center bg-[#f2f0ec] text-[#aaa198]">
                                <svg viewBox="0 0 24 24" class="h-9 w-9" fill="none" stroke="currentColor" stroke-width="1.45" aria-hidden="true">
                                    <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                    <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                                </svg>
                            </div>
                        @endif

                        <div class="seller-product-card-badges">
                            <span class="seller-product-card-badge {{ $primaryBadgeClass }}">
                                {{ $primaryBadgeLabel }}
                            </span>

                            @if ($secondaryBadge)
                                <span class="seller-product-card-badge seller-product-card-badge--dark">
                                    {{ $secondaryBadge }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="seller-product-card-body">
                        <div class="seller-product-card-heading">
                            <div class="min-w-0">
                                <h3 class="seller-product-card-title">
                                    {{ $product['name'] ?? 'Untitled product' }}
                                </h3>

                                <p class="seller-product-card-category">
                                    {{ $product['category'] ?? 'Uncategorized' }}@if(!empty($product['brand'])) · {{ $product['brand'] }}@endif
                                </p>
                            </div>

                            <div class="seller-product-card-price-wrap">
                                <strong class="seller-product-card-price">
                                    ₱{{ number_format($displayedSalePrice, 2) }}
                                </strong>

                                @if ($effectiveDiscount > 0)
                                    <span class="seller-product-card-original-price">
                                        ₱{{ number_format((float) ($product['price'] ?? 0), 2) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if ($flashSaleActive)
                            <div
                                class="seller-product-flash-sale"
                                data-flash-sale
                                data-product-id="{{ $product['id'] ?? '' }}"
                                data-flash-sale-ends-at="{{ $product['flash_sale_ends_at'] ?? '' }}"
                            >
                                <span class="seller-product-flash-sale-label">Flash sale</span>
                                <span class="seller-product-flash-sale-bolt" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z"></path>
                                    </svg>
                                </span>
                                <span class="seller-product-flash-sale-countdown" data-flash-sale-countdown role="timer">--:--:--</span>
                            </div>
                        @elseif ($flashSaleScheduled)
                            <div class="seller-product-flash-sale seller-product-flash-sale--scheduled">
                                <span class="seller-product-flash-sale-label">Flash sale</span>
                                <span class="seller-product-flash-sale-bolt" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z"></path>
                                    </svg>
                                </span>
                                <span class="seller-product-flash-sale-countdown seller-product-flash-sale-countdown--scheduled">
                                    Starts on approval
                                </span>
                            </div>
                        @endif

                        <div class="seller-product-card-divider"></div>

                        <div class="seller-product-card-footer">
                            <div class="seller-product-card-stock-copy">
                                <p class="seller-product-card-stock {{ $stockTextClass }}">
                                    {{ number_format($stockValue) }} in stock
                                </p>

                                <p class="seller-product-card-updated">
                                    {{ $product['created_at_human'] ?? '' }}
                                </p>
                            </div>

                            <div class="seller-product-actions seller-product-card-actions">
                                <button
                                    type="button"
                                    data-page-view-product
                                    data-id="{{ $product['id'] ?? '' }}"
                                    title="View product"
                                    aria-label="View product"
                                    class="seller-product-action seller-product-action--view"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" aria-hidden="true">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="2.5"></circle>
                                    </svg>
                                </button>

                                <button
                                    type="button"
                                    data-page-edit-product
                                    data-id="{{ $product['id'] ?? '' }}"
                                    title="Edit product"
                                    aria-label="Edit product"
                                    class="seller-product-action seller-product-action--edit"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" aria-hidden="true">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                    </svg>
                                </button>

                                <button
                                    type="button"
                                    data-page-product-action="archive"
                                    data-id="{{ $product['id'] ?? '' }}"
                                    data-name="{{ $product['name'] ?? '' }}"
                                    title="Archive product"
                                    aria-label="Archive product"
                                    class="seller-product-action seller-product-action--archive"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" aria-hidden="true">
                                        <rect x="4" y="7" width="16" height="13" rx="2"></rect>
                                        <path d="M3 4h18v3H3z"></path>
                                        <path d="M10 11h4"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <script>
            /*
             * The server has already painted the first-screen cards.
             * Prevent the cache fast-painter from rebuilding the same DOM.
             */
            window.__SARI_PRODUCTS_FAST_PAINTED__ =
                document.querySelector('#productsGrid [data-product-item]') !== null;
        </script>

        {{--
            The first-screen product cards are already rendered by Blade.
            The previous inline cache fast-painter duplicated ~12 KB of JS on
            every navigation but immediately returned whenever those cards
            existed. The cached external Product Management script now owns
            all post-paint hydration and full-library rendering.
        --}}

                <div id="productsEmpty" class="hidden px-5 py-16 text-center">
                    <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-[#f4f0ea] text-[#948a7d]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg>
                    </span>
                    <p class="mt-3 text-[11px] font-semibold text-[#4b443c]">No products match these filters.</p>
                    <p class="mt-1 text-[9px] text-[#948b80]">Try clearing one or more filters.</p>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- =============================================================
    VIEW PRODUCT MODAL — ENTERPRISE PRODUCT INSPECTOR
    Reference-driven UI only. Existing IDs / JS hooks are preserved.
============================================================== --}}
<div id="sellerViewProductModal" class="fixed inset-0 z-[130] hidden items-center justify-center">
    <div class="seller-product-view-dialog seller-view-enterprise-dialog">

        {{-- HEADER --}}
        <div class="seller-view-enterprise-header">
            <div class="min-w-0">
                <p class="seller-view-enterprise-eyebrow">Product Inspector</p>
                <h3>Listing Details</h3>
                <p class="seller-view-enterprise-subtitle">
                    Review product information, stock details, and listing configuration.
                </p>
            </div>

            <button
                type="button"
                data-close-view
                class="seller-view-enterprise-close"
                aria-label="Close product details"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m7 7 10 10"></path>
                    <path d="m17 7-10 10"></path>
                </svg>
            </button>
        </div>

        {{-- CONTENT --}}
        <div class="seller-view-enterprise-body min-h-0 flex-1 overflow-y-auto">

            {{-- HERO --}}
            <div class="seller-view-enterprise-hero">
                {{-- MEDIA --}}
                <div class="seller-view-enterprise-media">
                    <div id="viewProductImageWrap" class="seller-view-enterprise-image-wrap">
                        <img id="viewProductImage" src="" alt="" class="hidden">

                        <div id="viewProductImagePlaceholder" class="seller-view-enterprise-image-placeholder">
                            <div>
                                <span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                        <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                        <circle cx="9" cy="9" r="1.5"></circle>
                                        <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                                    </svg>
                                </span>
                                <p>No product image</p>
                            </div>
                        </div>

                        <span id="viewProductStatusBadge" class="seller-view-enterprise-image-status">
                            Status
                        </span>
                    </div>

                    <div id="viewProductGallerySection" class="seller-view-enterprise-gallery hidden">
                        <div class="seller-view-enterprise-gallery-head">
                            <p>Gallery</p>
                            <span id="viewProductGalleryCount"></span>
                        </div>
                        <div id="viewProductGallery"></div>
                    </div>
                </div>

                {{-- PRODUCT SUMMARY --}}
                <div class="seller-view-enterprise-summary">
                    <div class="seller-view-enterprise-chips">
                        <span id="viewProductCategoryChip" class="seller-view-chip seller-view-chip--category">Category</span>
                        <span id="viewProductTypeChip" class="seller-view-chip seller-view-chip--type">Listing type</span>

                        <span id="viewProductShippingChip" class="seller-view-chip seller-view-chip--shipping hidden">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M3 7h11v9H3z"></path>
                                <path d="M14 10h3l4 4v2h-7z"></path>
                                <circle cx="7" cy="18" r="2"></circle>
                                <circle cx="18" cy="18" r="2"></circle>
                            </svg>
                            <span>Free Shipping</span>
                        </span>
                    </div>

                    <h2 id="viewProductName">Product</h2>

                    <p id="viewProductBrandLine">
                        No Brand
                    </p>

                    <div class="seller-view-enterprise-price">
                        <strong id="viewProductSalePrice">₱0.00</strong>

                        <span id="viewProductOriginalPrice" class="hidden">
                            ₱0.00
                        </span>

                        <span id="viewProductDiscountBadge" class="hidden">
                            0% OFF
                        </span>
                    </div>

                    <div class="seller-view-enterprise-meta">
                        <div class="seller-view-enterprise-meta-item">
                            <span class="seller-view-enterprise-meta-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                    <rect x="4" y="5" width="16" height="15" rx="2"></rect>
                                    <path d="M8 3v4"></path>
                                    <path d="M16 3v4"></path>
                                    <path d="M4 9h16"></path>
                                </svg>
                            </span>
                            <div>
                                <p>Created</p>
                                <strong id="viewProductCreated">—</strong>
                            </div>
                        </div>

                        <div class="seller-view-enterprise-meta-divider" aria-hidden="true"></div>

                        <div class="seller-view-enterprise-meta-item">
                            <span class="seller-view-enterprise-meta-icon seller-view-enterprise-meta-icon--rating">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                    <path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.8 1-6.1-4.4-4.3 6.1-.9L12 3Z"></path>
                                </svg>
                            </span>
                            <div>
                                <p>Rating</p>
                                <strong id="viewProductRating">—</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FACT STRIP --}}
                <div class="seller-view-enterprise-facts">
                    <div class="seller-view-enterprise-fact">
                        <span class="seller-view-enterprise-fact-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M4 8.5 12 4l8 4.5v7L12 20l-8-4.5z"></path>
                                <path d="M12 4v16M4 8.5l8 4.5 8-4.5"></path>
                            </svg>
                        </span>
                        <span class="seller-view-enterprise-fact-label">Total Stock</span>
                        <strong id="viewProductStock">0</strong>
                    </div>

                    <div class="seller-view-enterprise-fact">
                        <span class="seller-view-enterprise-fact-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M5 5v14M8 5v14M12 5v14M15 5v14M19 5v14"></path>
                            </svg>
                        </span>
                        <span class="seller-view-enterprise-fact-label">SKU</span>
                        <strong id="viewProductSku">—</strong>
                    </div>

                    <div class="seller-view-enterprise-fact">
                        <span class="seller-view-enterprise-fact-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M20 13 13 20l-9-9V4h7z"></path>
                                <circle cx="8.5" cy="8.5" r="1.4"></circle>
                            </svg>
                        </span>
                        <span class="seller-view-enterprise-fact-label">Brand</span>
                        <strong id="viewProductBrand">No Brand</strong>
                    </div>

                    <div class="seller-view-enterprise-fact">
                        <span class="seller-view-enterprise-fact-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="m12 3 8 4.5v9L12 21l-8-4.5v-9z"></path>
                                <path d="m4 7.5 8 4.5 8-4.5M12 12v9"></path>
                            </svg>
                        </span>
                        <span class="seller-view-enterprise-fact-label">Listing Type</span>
                        <strong id="viewProductType">Simple Product</strong>
                    </div>

                    <div class="seller-view-enterprise-fact seller-view-enterprise-fact--status">
                        <span class="seller-view-enterprise-fact-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M12 3 19 6v5c0 4.7-2.7 7.8-7 10-4.3-2.2-7-5.3-7-10V6z"></path>
                                <path d="M9.5 12 11 13.5 14.5 10"></path>
                            </svg>
                        </span>
                        <span class="seller-view-enterprise-fact-label">Moderation Status</span>
                        <div class="seller-view-enterprise-status-pill">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="12" cy="12" r="8"></circle>
                                <path d="M12 8v4l2.5 1.5"></path>
                            </svg>
                            <strong id="viewProductStatus">Pending</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PRIMARY DETAILS --}}
            <div class="seller-view-enterprise-details">
                {{-- DESCRIPTION --}}
                <section class="seller-view-enterprise-panel seller-view-enterprise-panel--description">
                    <div class="seller-view-enterprise-panel-head">
                        <span class="seller-view-enterprise-panel-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="5" y="4" width="14" height="16" rx="2"></rect>
                                <path d="M9 9h6"></path>
                                <path d="M9 13h6"></path>
                            </svg>
                        </span>

                        <div>
                            <h4>Product Description</h4>
                            <p>Buyer-facing listing description.</p>
                        </div>
                    </div>

                    <div id="viewProductDescription" class="seller-view-enterprise-description-copy">
                        No description has been added for this product yet.
                    </div>
                </section>

                {{-- LISTING INFORMATION --}}
                <section class="seller-view-enterprise-panel seller-view-enterprise-panel--listing">
                    <div class="seller-view-enterprise-panel-head">
                        <span class="seller-view-enterprise-panel-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="6" y="3" width="12" height="18" rx="2"></rect>
                                <path d="M9 8h6"></path>
                                <path d="M9 12h6"></path>
                                <path d="M9 16h4"></path>
                            </svg>
                        </span>

                        <div>
                            <h4>Listing Information</h4>
                            <p>Catalog and promotion details.</p>
                        </div>
                    </div>

                    <dl class="seller-view-enterprise-list">
                        <div>
                            <dt>Category</dt>
                            <dd id="viewProductCategory">—</dd>
                        </div>

                        <div>
                            <dt>Condition</dt>
                            <dd id="viewProductCondition">—</dd>
                        </div>

                        <div>
                            <dt>Package</dt>
                            <dd id="viewProductPackage">—</dd>
                        </div>

                        <div>
                            <dt>Preparation</dt>
                            <dd id="viewProductPreparation">—</dd>
                        </div>

                        <div>
                            <dt>Low Stock Alert</dt>
                            <dd id="viewProductLowStock">—</dd>
                        </div>

                        <div>
                            <dt>Discount</dt>
                            <dd id="viewProductDiscountText">—</dd>
                        </div>

                        <div>
                            <dt>Voucher</dt>
                            <dd id="viewProductVoucher">—</dd>
                        </div>

                        <div>
                            <dt>Shipping</dt>
                            <dd id="viewProductShippingText">—</dd>
                        </div>
                    </dl>
                </section>
            </div>

            {{-- SPECIFICATIONS --}}
            <section id="viewProductSpecificationsSection" class="seller-view-enterprise-optional hidden">
                <div class="seller-view-enterprise-optional-head">
                    <div>
                        <h4>Specifications</h4>
                        <p>Structured product attributes.</p>
                    </div>
                    <span id="viewProductSpecificationCount"></span>
                </div>

                <div id="viewProductSpecifications" class="seller-view-enterprise-spec-grid"></div>
            </section>

            {{-- VARIANTS --}}
            <section id="viewProductVariantsSection" class="seller-view-enterprise-optional hidden">
                <div class="seller-view-enterprise-optional-head">
                    <div>
                        <h4>Product Variants</h4>
                        <p>Option combinations with independent price and stock.</p>
                    </div>
                    <span id="viewProductVariantCount"></span>
                </div>

                <div class="seller-view-enterprise-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Variant</th>
                                <th>Image</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody id="viewProductVariants"></tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- FOOTER --}}
        <div class="seller-view-enterprise-footer">
            <p>
                <span class="seller-view-enterprise-footer-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 11v5"></path>
                        <path d="M12 8h.01"></path>
                    </svg>
                </span>
                <span>This view uses your current Product Library information.</span>
            </p>

            <div>
                <button
                    type="button"
                    data-close-view
                    class="seller-view-enterprise-button seller-view-enterprise-button--secondary"
                >
                    Close
                </button>

                <button
                    id="viewProductEditButton"
                    type="button"
                    class="seller-view-enterprise-button seller-view-enterprise-button--primary"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path d="M12 20h9"></path>
                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                    </svg>
                    <span>Edit Product</span>
                </button>
            </div>
        </div>
    </div>
</div>


{{-- =============================================================
    EDIT PRODUCT MODAL — ENTERPRISE GOLD DESIGN
============================================================== --}}
<div id="sellerEditProductModal" class="fixed inset-0 z-[130] hidden items-center justify-center">
    <div class="seller-edit-enterprise-dialog">
        <header class="seller-edit-enterprise-header">
            <div class="seller-edit-header-copy">
                <p class="seller-edit-eyebrow">Product Management</p>
                <h3>Edit Product</h3>
                <p class="seller-edit-subtitle">Sensitive listing changes are re-screened automatically. Your existing specifications and variants are preserved when editing common fields.</p>
            </div>

            <button type="button" data-close-edit class="seller-edit-header-close" aria-label="Close Edit Product">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                    <path d="M6 6l12 12M18 6 6 18"></path>
                </svg>
            </button>
        </header>

        <form id="sellerEditProductForm" method="POST" action="" enctype="multipart/form-data" class="seller-edit-enterprise-form">
            @csrf
            @method('PUT')

            <div class="seller-edit-grid">
                <div class="seller-edit-field seller-edit-field--full">
                    <label for="editProductName">Product Name <span>*</span></label>
                    <input id="editProductName" name="name" required>
                </div>

                <div class="seller-edit-field">
                    <label for="editProductCategory">Category <span>*</span></label>
                    <div class="seller-edit-select-wrap">
                        <select id="editProductCategory" name="category" required>
                            <option value="">Select Category</option>
                            <option>Electronics</option>
                            <option>Fashion</option>
                            <option>Fashion & Apparel</option>
                            <option>Shoes & Footwear</option>
                            <option>Home & Living</option>
                            <option>Furniture & Office</option>
                            <option>Beauty</option>
                            <option>Beauty & Personal Care</option>
                            <option>Books</option>
                            <option>Books & Stationery</option>
                            <option>Food & Beverage</option>
                            <option>Food & Gourmet</option>
                            <option>Jewelry & Watches</option>
                            <option>Sports & Outdoors</option>
                            <option>Automotive & Motorcycle</option>
                            <option>Baby & Kids</option>
                            <option>Pet Supplies</option>
                            <option>Health & Wellness</option>
                            <option>Toys & Collectibles</option>
                            <option>Appliances</option>
                            <option>Others</option>
                        </select>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="m7 10 5 5 5-5"></path></svg>
                    </div>
                </div>

                <div class="seller-edit-field">
                    <label for="editProductBrand">Brand</label>
                    <input id="editProductBrand" name="brand" placeholder="Example: Samsung, Nike, No Brand">
                </div>

                <div class="seller-edit-field">
                    <label for="editProductSku">SKU</label>
                    <input id="editProductSku" name="sku">
                </div>

                <div class="seller-edit-field">
                    <label for="editProductPrice">Price <span>*</span></label>
                    <input id="editProductPrice" name="price" type="number" min="0" step="0.01" required>
                </div>

                <div class="seller-edit-field">
                    <label for="editProductStock">Stock <span>*</span></label>
                    <input id="editProductStock" name="stock" type="number" min="0" required>
                </div>

                <div class="seller-edit-field">
                    <label for="editProductDiscount">Discount %</label>
                    <input id="editProductDiscount" name="discount" type="number" min="0" max="100" step="0.01">
                    <p id="editProductSalePreview" class="seller-edit-helper"></p>
                </div>

                <div class="seller-edit-field">
                    <label for="editProductFlashSaleEndsAtLocal">Flash Sale duration target</label>
                    <input id="editProductFlashSaleEndsAtLocal" type="datetime-local" class="seller-edit-flash-input">
                    <input id="editProductFlashSaleEndsAt" name="flash_sale_ends_at" type="hidden">
                    <p class="seller-edit-helper seller-edit-helper--multiline">Leave blank for a normal discount. For Flash Sale, choose a future time to define its duration; the countdown starts only after administrator approval.</p>
                </div>

                <div class="seller-edit-field">
                    <label for="editProductVoucher">Voucher Code</label>
                    <input id="editProductVoucher" name="voucher_code" placeholder="Enter voucher code (optional)">
                </div>

                <div class="seller-edit-field seller-edit-field--full">
                    <label class="seller-edit-shipping-card" for="editProductFreeShipping">
                        <span class="seller-edit-shipping-control">
                            <input id="editProductFreeShipping" name="free_shipping" type="checkbox" value="1" class="sr-only">
                            <span class="seller-edit-gold-switch" aria-hidden="true"></span>
                        </span>
                        <span class="seller-edit-shipping-copy">
                            <strong>Free Shipping</strong>
                            <small>Show the gold Free Shipping badge and waive the eligible buyer delivery fee.</small>
                        </span>
                    </label>
                </div>

                <div class="seller-edit-field seller-edit-media-field">
                    <label for="editProductImage">Replace Product Image</label>
                    <div class="seller-edit-media-row">
                        <div id="editCurrentImageWrap" class="hidden seller-edit-current-image">
                            <img id="editCurrentImage" src="" alt="Current product image">
                        </div>

                        <div class="seller-edit-upload-box">
                            <span class="seller-edit-upload-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M12 16V4"></path><path d="m7 9 5-5 5 5"></path><path d="M5 14v5h14v-5"></path>
                                </svg>
                            </span>
                            <div class="seller-edit-upload-copy">
                                <input id="editProductImage" name="image" type="file" accept="image/*">
                                <small>Supports JPG, PNG, WEBP. Max 5MB.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="seller-edit-field seller-edit-description-field">
                    <label for="editProductDescription">Description</label>
                    <textarea id="editProductDescription" name="description" rows="4"></textarea>
                </div>
            </div>

            <div class="seller-edit-sensitive-note">
                <span class="seller-edit-sensitive-icon" aria-hidden="true">!</span>
                <p><strong>Sensitive edits:</strong> name, category, brand, description, specifications, variant options, and product image. When these change, previous approval is removed and SARI screens the listing again before admin re-review.</p>
            </div>

            <footer class="seller-edit-footer">
                <button type="button" data-close-edit class="seller-edit-button seller-edit-button--secondary">Cancel</button>
                <button type="submit" class="seller-edit-button seller-edit-button--primary">Save Changes</button>
            </footer>
        </form>
    </div>
</div>


{{-- ARCHIVE CONFIRMATION --}}
<div id="productsActionModal" class="fixed inset-0 z-[190] hidden items-center justify-center bg-black/40 p-4 backdrop-blur-[3px]">
    <div class="w-full max-w-[430px] rounded-[22px] border border-[#e7dfd4] bg-white p-5 shadow-[0_30px_90px_rgba(37,29,19,.24)] sm:p-6">
        <div class="flex items-start gap-3.5">
            <span id="productsActionIcon" class="grid h-11 w-11 shrink-0 place-items-center rounded-[13px] bg-[#fff5e7] text-[#b97918]">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16"></path><path d="M6 7v12h12V7"></path><path d="M9 11h6"></path></svg>
            </span>
            <div>
                <h3 id="productsActionTitle" class="text-[17px] font-bold tracking-[-0.025em] text-[#302a24]">Archive Product?</h3>
                <p id="productsActionText" class="mt-1.5 text-[9.5px] leading-5 text-[#81786d]"></p>
            </div>
        </div>

        <form id="productsActionForm" method="POST" class="mt-5 flex justify-end gap-2.5">
            @csrf
            <button id="productsActionCancel" type="button" class="h-10 rounded-xl border border-[#e3dcd2] bg-white px-4 text-[9px] font-semibold text-[#746b61] hover:bg-[#faf8f5]">Cancel</button>
            <button id="productsActionSubmit" type="submit" class="h-10 rounded-xl bg-[#c99128] px-5 text-[9px] font-semibold text-white hover:bg-[#b47e1e]">Archive Product</button>
        </form>
    </div>
</div>



















@endsection

@push('scripts')
<script>
window.__SARI_PRODUCTS_CONFIG__ = {
    libraryUrl: @json(route('seller.products.library')),
    productsBaseUrl: @json(url('/seller/products')),
    productDraftUrl: @json(route('seller.products.draft')),
    productDraftSaveUrl: @json(route('seller.products.draft.save')),
    productDraftDeleteUrl: @json(route('seller.products.draft.delete')),
    requestedCategory: @json((string) request('category', '')),
    createUrl: @json(route('seller.products.create')),
};
</script>
<script src="{{ asset('js/seller-products.js') }}?v=20261007-3" data-navigate-once></script>














@endpush


