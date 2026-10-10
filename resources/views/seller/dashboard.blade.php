@extends('layouts.seller')

@section('title', 'Seller Dashboard — SARI')
@section('page-title', 'Dashboard Overview')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/dashboard.css') }}?v=20261007-5" fetchpriority="high">
<link
    rel="stylesheet"
    href="https://cdn-uicons.flaticon.com/uicons-thin-straight/css/uicons-thin-straight.css"
>

{{-- Cacheable dashboard stylesheet; visual rules remain unchanged. --}}

@endpush

@section('content')

{{-- Dashboard = overview/analytics only. Product CRUD lives on Product Management. --}}

@php
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD DATA — NO DATABASE QUERIES IN BLADE
    |--------------------------------------------------------------------------
    |
    | SellerDashboardController now sends:
    | - $products: only the four preview products + eager-loaded activeVariants
    | - $productStats: total / approved / low-stock counts
    | - $lowStockProducts: only the three inventory-alert products
    |
    | The complete product catalog is lazy-loaded only when "View All" opens.
    |
    */

    $warningCount = (int) ($sellerAccount->warning_count ?? 0);
    $totalProductCount = (int) ($productStats->total_count ?? 0);
    $approvedCount = (int) ($productStats->approved_count ?? 0);
    $lowStockCount = (int) ($productStats->low_stock_count ?? 0);

    /*
    |--------------------------------------------------------------------------
    | REAL DASHBOARD SUMMARY DATA
    |--------------------------------------------------------------------------
    | These values come from SellerDashboardController.
    | No MarketplaceOrder / ProductReview query is executed inside Blade.
    */
    $dashboardStats = $dashboardStats ?? [];
    $orderStats = $orderStats ?? [];
    $sellerActivities = $sellerActivities ?? collect();
    $recentOrdersFeed = $recentOrdersFeed ?? collect();

    $pendingOrderCount = (int) ($dashboardStats['pending_orders'] ?? 0);
    $newOrdersToday = (int) ($dashboardStats['new_today'] ?? 0);
    $monthlySales = (float) ($dashboardStats['monthly_sales'] ?? 0);
    $previousMonthSales = (float) ($dashboardStats['previous_month_sales'] ?? 0);
    $monthlySalesChange = (float) ($dashboardStats['monthly_sales_change'] ?? 0);
    $monthlySalesTrendLabel = (string) ($dashboardStats['monthly_sales_trend_label'] ?? 'No previous-month sales');
    $platformCommissionRate = (float) ($dashboardStats['platform_commission_rate'] ?? 10);
    $platformCommission = (float) ($dashboardStats['platform_commission'] ?? 0);
    $estimatedRevenue = (float) ($dashboardStats['estimated_revenue'] ?? 0);

    /*
    |--------------------------------------------------------------------------
    | REAL SALES PERFORMANCE
    |--------------------------------------------------------------------------
    | SellerDashboardController prepares all chart buckets from delivered
    | MarketplaceOrder subtotals. The chart only renders these values.
    */
    $salesPerformance = $salesPerformance ?? [
        'month' => [],
        'last_month' => [],
        'year' => [],
    ];

    $activeSuspension = $sellerAccount->suspended_until
        ? now()->lt(\Illuminate\Support\Carbon::parse($sellerAccount->suspended_until))
        : false;

    $sellerLocked = $warningCount >= 3 || $activeSuspension;

    /*
    | activeVariants was eager-loaded by the controller.
    | This replaces the old SellerProductVariant::query() inside the view.
    */
    $sellerVariantGroups = $products->mapWithKeys(
        fn ($product) => [
            $product->id => $product->activeVariants ?? collect(),
        ]
    );

    $dashboardPreviewProducts = $products;

    /*
    |--------------------------------------------------------------------------
    | CATEGORY BREAKDOWN — INSTANT PREVIEW PAYLOAD
    |--------------------------------------------------------------------------
    | The full active catalog is fetched once from the existing lazy Product
    | Library endpoint. These preview rows let the donut paint immediately so
    | the dashboard never waits on the network before showing useful content.
    */
    $categoryBreakdownPreview = $dashboardPreviewProducts
        ->map(function ($product) {
            return [
                'category' => trim((string) ($product->category ?: 'Uncategorized')),
                'moderation_status' => (string) ($product->moderation_status ?? ''),
                'stock' => (int) ($product->stock ?? 0),
            ];
        })
        ->values();


    /*
    | First-paint category preview uses only the controller-provided preview
    | rows. The complete catalog starts fetching immediately below and silently
    | refreshes this card as soon as the response is ready.
    */
    $categoryPreviewPalette = [
        '#D89B10', // Primary Gold
        '#B67A08', // Deep Gold
        '#63A375', // Soft Green
        '#6C8FB5', // Dusty Blue
        '#D97C6C', // Soft Rose
        '#4E5A4F', // Dark Olive Gray
    ];

    $categoryPreviewGroups = $categoryBreakdownPreview
        ->groupBy('category')
        ->map(fn ($items) => $items->count())
        ->sortDesc();

    if ($categoryPreviewGroups->count() > 6) {
        $categoryPreviewVisible = $categoryPreviewGroups->take(5);
        $categoryPreviewRemainder = $categoryPreviewGroups->skip(5)->sum();
        $categoryPreviewGroups = $categoryPreviewVisible->put('Other Categories', $categoryPreviewRemainder);
    }

    $categoryPreviewVisibleTotal = max(1, (int) $categoryPreviewGroups->sum());
    $categoryPreviewRows = collect();
    $categoryPreviewSegments = [];
    $categoryPreviewCursor = 0.0;

    foreach ($categoryPreviewGroups as $categoryName => $categoryCount) {
        $categoryPreviewIndex = $categoryPreviewRows->count();
        $categoryPreviewPercent = ((int) $categoryCount / $categoryPreviewVisibleTotal) * 100;
        $categoryPreviewStart = $categoryPreviewCursor;
        $categoryPreviewEnd = $categoryPreviewCursor + ($categoryPreviewPercent * 3.6);
        $categoryPreviewCursor = $categoryPreviewEnd;
        $categoryPreviewColor = $categoryPreviewPalette[$categoryPreviewIndex % count($categoryPreviewPalette)];

        $categoryPreviewRows->push([
            'name' => (string) $categoryName,
            'count' => (int) $categoryCount,
            'percentage' => $categoryPreviewPercent,
            'color' => $categoryPreviewColor,
        ]);

        $categoryPreviewSegments[] = sprintf(
            '%s %.2fdeg %.2fdeg',
            $categoryPreviewColor,
            $categoryPreviewStart,
            $categoryPreviewEnd
        );
    }

    $categoryPreviewGradient = count($categoryPreviewSegments)
        ? 'conic-gradient(' . implode(', ', $categoryPreviewSegments) . ')'
        : 'conic-gradient(#e8e3dc 0deg 360deg)';
@endphp

<script>
    /*
     * Product Library is non-critical to Dashboard first paint.
     * Warm one complete copy while the browser is idle and persist it for
     * Product Management so opening that page does not have to start from a
     * cold JSON request every time.
     */
    (function () {
        const libraryUrl = @json(route('seller.products.library'));
        const sellerId = @json((int) ($sellerAccount->id ?? 0));
        const cacheKey = 'sari:seller-products-library:v6:' + sellerId + ':' + libraryUrl;

        try {
            window.sessionStorage.removeItem('sari:seller-products-library:v5:' + libraryUrl);
        } catch (_) {}
        const maxCacheAgeMs = 10 * 60 * 1000;

        function isCompleteLibrary(data) {
            if (!data || !Array.isArray(data.products)) return false;

            const declaredCount = Number(data.count);

            return Number.isFinite(declaredCount)
                ? data.products.length >= Math.max(0, declaredCount)
                : !Object.prototype.hasOwnProperty.call(data, 'initial_count');
        }

        window.__SARI_CACHE_PRODUCTS_LIBRARY__ = function (data) {
            if (!isCompleteLibrary(data)) return data;

            window.__SARI_PRODUCTS_LIBRARY_URL__ = libraryUrl;
            window.__SARI_PRODUCTS_LIBRARY_CACHE_KEY__ = cacheKey;
            window.__SARI_PRODUCTS_LIBRARY_CACHED__ = data;
            window.__SARI_PRODUCTS_LIBRARY_CACHE_IS_COMPLETE__ = true;
            window.__SARI_PRODUCTS_LIBRARY_CACHE_SAVED_AT__ = Date.now();

            try {
                window.sessionStorage.setItem(
                    cacheKey,
                    JSON.stringify({
                        saved_at: Date.now(),
                        data: data
                    })
                );
            } catch (_) {}

            return data;
        };

        function sessionCachedLibrary() {
            try {
                const raw = window.sessionStorage.getItem(cacheKey);
                if (!raw) return null;

                const stored = JSON.parse(raw);
                const age = Date.now() - Number(stored?.saved_at || 0);

                if (
                    age >= 0
                    && age <= maxCacheAgeMs
                    && isCompleteLibrary(stored?.data)
                ) {
                    window.__SARI_PRODUCTS_LIBRARY_URL__ = libraryUrl;
                    window.__SARI_PRODUCTS_LIBRARY_CACHE_KEY__ = cacheKey;
                    window.__SARI_PRODUCTS_LIBRARY_CACHED__ = stored.data;
                    window.__SARI_PRODUCTS_LIBRARY_CACHE_IS_COMPLETE__ = true;
                    window.__SARI_PRODUCTS_LIBRARY_CACHE_SAVED_AT__ = Number(stored.saved_at || 0);
                    return stored.data;
                }
            } catch (_) {}

            return null;
        }

        window.__SARI_SELLER_PRODUCT_LIBRARY_PREFETCH__ ??=
            new Promise(function (resolve) {
                function finish(data) {
                    if (Array.isArray(data)) {
                        resolve(data);
                        return;
                    }

                    resolve(
                        Array.isArray(data?.products)
                            ? data.products
                            : []
                    );
                }

                function run() {
                    const cached = window.__SARI_PRODUCTS_LIBRARY_CACHED__;

                    if (isCompleteLibrary(cached)) {
                        finish(cached);
                        return;
                    }

                    const stored = sessionCachedLibrary();
                    if (stored) {
                        finish(stored);
                        return;
                    }

                    const sharedPromise = window.__SARI_PRODUCTS_LIBRARY_PROMISE__;

                    if (sharedPromise?.then) {
                        Promise.resolve(sharedPromise)
                            .then(function (data) {
                                if (isCompleteLibrary(data)) {
                                    window.__SARI_CACHE_PRODUCTS_LIBRARY__(data);
                                }
                                finish(data);
                            })
                            .catch(function () { resolve([]); });
                        return;
                    }

                    fetch(libraryUrl, {
                        method: 'GET',
                        credentials: 'same-origin',
                        cache: 'default',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('Unable to prefetch the Product Library.');
                            }

                            return response.json();
                        })
                        .then(function (data) {
                            window.__SARI_CACHE_PRODUCTS_LIBRARY__(data);
                            finish(data);
                        })
                        .catch(function () {
                            resolve([]);
                        });
                }

                if ('requestIdleCallback' in window) {
                    window.requestIdleCallback(run, { timeout: 900 });
                } else {
                    window.setTimeout(run, 260);
                }
            });
    })();
</script>



<div id="sellerDashboardStage" class="mx-auto w-full max-w-[1800px]">

    <div id="sellerDashboardContent" class="w-full">


    {{-- =========================================================
        SINGLE ACTION NOTIFICATION — AUTO DISMISS AFTER 5 SECONDS
        Priority: error > warning > success > info > compliance
    ========================================================== --}}
    @unless ($sellerLocked)
        @php
            $noticeType = null;
            $noticeTitle = null;
            $noticeMessage = null;

            if ($errors->has('product')) {
                $noticeType = 'error';
                $noticeTitle = 'Selling Action Unavailable';
                $noticeMessage = $errors->first('product');
            } elseif (session('warning')) {
                $noticeType = 'warning';
                $noticeTitle = 'Product Review Notice';
                $noticeMessage = session('warning');
            } elseif (session('success')) {
                $noticeType = 'success';
                $noticeTitle = 'Action Completed Successfully';
                $noticeMessage = session('success');
            } elseif (session('info')) {
                $noticeType = 'info';
                $noticeTitle = 'Seller Update';
                $noticeMessage = session('info');
            } elseif ($warningCount > 0) {
                $noticeType = 'warning';
                $noticeTitle = 'Compliance Warning: ' . $warningCount . ' / 3';
                $noticeMessage = $warningCount >= 2
                    ? 'Your seller account is close to the 3-warning limit. Review your listings carefully or contact the administrator if you need assistance.'
                    : 'Your seller account currently has one active compliance warning. Continue following SARI marketplace policies to keep your account in good standing.';
            }
        @endphp

        @if ($noticeType)
            @php
                $noticeStyle = match ($noticeType) {
                    'success' => [
                        'border' => 'border-[#bfe0cd]',
                        'bg' => 'bg-[#f1faf5]',
                        'iconBg' => 'bg-white',
                        'iconText' => 'text-[#327a53]',
                        'title' => 'text-[#246b45]',
                        'body' => 'text-[#547767]',
                    ],
                    'info' => [
                        'border' => 'border-[#c5daee]',
                        'bg' => 'bg-[#f2f7fd]',
                        'iconBg' => 'bg-white',
                        'iconText' => 'text-[#356f9f]',
                        'title' => 'text-[#2b6595]',
                        'body' => 'text-[#617d96]',
                    ],
                    default => [
                        'border' => 'border-[#efc6c6]',
                        'bg' => 'bg-[#fff4f4]',
                        'iconBg' => 'bg-white',
                        'iconText' => 'text-[#c65050]',
                        'title' => 'text-[#b53e3e]',
                        'body' => 'text-[#8d5d5d]',
                    ],
                };
            @endphp

            <section
                id="sellerTopNotice"
                data-auto-dismiss-notice
                class="mb-4 overflow-hidden rounded-[18px] border {{ $noticeStyle['border'] }} {{ $noticeStyle['bg'] }} shadow-[0_8px_24px_rgba(51,39,25,0.04)] transition-all duration-500"
            >
                <div class="flex min-h-[88px] items-center gap-4 px-5 py-4 sm:px-6">
                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-[13px] {{ $noticeStyle['iconBg'] }} {{ $noticeStyle['iconText'] }} shadow-sm">
                        @if ($noticeType === 'success')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="m8 12 2.5 2.5L16 9"></path>
                            </svg>
                        @elseif ($noticeType === 'info')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 11v5"></path>
                                <path d="M12 8h.01"></path>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 3 3 20h18L12 3Z"></path>
                                <path d="M12 9v5"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-[12px] font-bold leading-5 sm:text-[13px] {{ $noticeStyle['title'] }}">
                            {{ $noticeTitle }}
                        </p>

                        <p class="mt-1 text-[10px] leading-5 sm:text-[11px] {{ $noticeStyle['body'] }}">
                            {{ $noticeMessage }}
                        </p>
                    </div>

                    @if ($noticeType === 'warning' && $warningCount > 0 && !session('warning'))
                        <a
                            href="{{ route('seller.messages') }}"
                            class="hidden shrink-0 rounded-lg border border-[#e8baba] bg-white px-3.5 py-2 text-[9px] font-semibold text-[#b54848] transition hover:bg-[#fffafa] sm:inline-flex"
                wire:navigate
            >
                            Contact Admin
                        </a>
                    @endif
                </div>
            </section>
        @endif
    @endunless

    {{-- =========================================================
        PREMIUM WELCOME PANEL — CLEAN MODERN REFERENCE APPLIED
    ========================================================== --}}
    <section class="seller-greeting-hero seller-hero-reference seller-hero-admin-safe relative">
        <div class="seller-hero-reference-layout">

            <div class="seller-hero-reference-identity">
                <div class="seller-greeting-icon-shell seller-hero-store-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M4 10h16"></path>
                        <path d="M5 10v9h14v-9"></path>
                        <path d="M7 10 9 5h6l2 5"></path>
                        <path d="M9 19v-5h6v5"></path>
                    </svg>
                </div>

                <div class="seller-hero-reference-copy">
                    <h2 id="sellerGreetingText" class="seller-hero-reference-title">
                        <span id="sellerGreetingLabel">Good morning, Seller!</span>
                        <span class="seller-greeting-wave" aria-hidden="true">
                            <i class="fi fi-ts-hand-wave"></i>
                        </span>
                    </h2>

                    <p class="seller-hero-reference-subtitle">
                        Here’s what’s happening with your store today.
                    </p>

                    <div class="seller-hero-reference-statuses" aria-label="Store status">
                        <span class="seller-hero-status {{ $sellerLocked ? 'seller-hero-status--restricted' : 'seller-hero-status--active' }}">
                            <i aria-hidden="true"></i>
                            {{ $sellerLocked ? 'Store Restricted' : 'Store Active' }}
                        </span>

                        <span class="seller-hero-status-divider" aria-hidden="true"></span>

                        <span class="seller-hero-verified">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M12 3 5 6v5c0 4.7 2.8 8.3 7 10 4.2-1.7 7-5.3 7-10V6l-7-3Z"></path>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                            <span>Verified Store</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="seller-hero-reference-divider" aria-hidden="true"></div>

            <div class="seller-greeting-meta seller-hero-reference-meta">
                <div class="seller-greeting-stat">
                    <div class="seller-greeting-stat-heading">
                        <svg viewBox="0 0 24 24" class="seller-greeting-stat-icon" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <circle cx="12" cy="12" r="8.5"></circle>
                            <path d="M12 7.5v5l3 2"></path>
                        </svg>
                        <p class="seller-greeting-stat-label">Time</p>
                    </div>
                    <div class="seller-greeting-stat-copy">
                        <p id="sellerCurrentTime" class="seller-greeting-stat-value">08:56 AM</p>
                        <p id="sellerTimeContext" class="seller-greeting-stat-sub">Asia/Manila</p>
                    </div>
                </div>

                <div class="seller-greeting-stat">
                    <div class="seller-greeting-stat-heading">
                        <svg viewBox="0 0 24 24" class="seller-greeting-stat-icon" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="M7 3v3"></path>
                            <path d="M17 3v3"></path>
                            <rect x="4" y="5" width="16" height="15" rx="2"></rect>
                            <path d="M4 10h16"></path>
                        </svg>
                        <p class="seller-greeting-stat-label">Date</p>
                    </div>
                    <div class="seller-greeting-stat-copy">
                        <p id="sellerCurrentDay" class="seller-greeting-stat-value">Friday</p>
                        <p id="sellerCurrentDate" class="seller-greeting-stat-sub">May 23, 2025</p>
                    </div>
                </div>

                <div class="seller-greeting-stat">
                    <div class="seller-greeting-stat-heading">
                        <svg viewBox="0 0 24 24" class="seller-greeting-stat-icon seller-greeting-stat-icon-weather" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="M17.5 17.5H8a4 4 0 1 1 .8-7.92A5 5 0 0 1 18 9a3.5 3.5 0 1 1-.5 8.5Z"></path>
                            <path d="M15 7.5a2.5 2.5 0 0 1 2.5-2.5"></path>
                        </svg>
                        <p class="seller-greeting-stat-label">Weather</p>
                    </div>
                    <div class="seller-greeting-stat-copy">
                        <p id="sellerWeatherTemp" class="seller-greeting-stat-value">29°C</p>
                        <p id="sellerWeatherCondition" class="seller-greeting-stat-sub">Current conditions</p>
                    </div>
                </div>
            </div>

        </div>
    </section>


    {{-- =========================================================
        SUMMARY CARDS — REAL SELLER DATA
    ========================================================== --}}
    <section class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @php
            $summaryCards = [
                [
                    'label' => 'Uploaded Products',
                    'value' => number_format($totalProductCount),
                    'sub' => number_format($approvedCount) . ' approved · ' . number_format(max(0, $totalProductCount - $approvedCount)) . ' under review',
                    'bg' => 'bg-[#f4f8fd]',
                    'border' => 'border-[#d7e4f1]',
                    'icon' => 'text-[#3f78ad]',
                    'type' => 'products',
                    'href' => route('seller.products.index'),
                    'navigate' => true,
                    'subClass' => 'text-[#85807a]',
                ],
                [
                    'label' => 'Pending Orders',
                    'value' => number_format($pendingOrderCount),
                    'sub' => number_format($newOrdersToday) . ' new today',
                    'bg' => 'bg-[#fff9ef]',
                    'border' => 'border-[#eee0c6]',
                    'icon' => 'text-[#c3881f]',
                    'type' => 'orders',
                    'href' => route('seller.orders'),
                    'navigate' => true,
                    'subClass' => $newOrdersToday > 0 ? 'text-[#a8751f]' : 'text-[#85807a]',
                ],
                [
                    'label' => 'Monthly Sales',
                    'value' => '₱' . number_format($monthlySales, 2),
                    'sub' => $monthlySalesTrendLabel,
                    'bg' => 'bg-[#f4faf7]',
                    'border' => 'border-[#d6e7dc]',
                    'icon' => 'text-[#4b8666]',
                    'type' => 'sales',
                    'href' => route('seller.reports'),
                    'navigate' => true,
                    'subClass' => $monthlySalesChange > 0
                        ? 'text-[#4b8666]'
                        : ($monthlySalesChange < 0 ? 'text-[#ad6262]' : 'text-[#85807a]'),
                ],
                [
                    'label' => 'Low Stock Items',
                    'value' => number_format($lowStockCount),
                    'sub' => $lowStockCount > 0 ? 'Needs inventory attention' : 'Inventory levels healthy',
                    'bg' => $lowStockCount > 0 ? 'bg-[#fff7f7]' : 'bg-[#f4faf7]',
                    'border' => $lowStockCount > 0 ? 'border-[#eedada]' : 'border-[#d6e7dc]',
                    'icon' => $lowStockCount > 0 ? 'text-[#be6363]' : 'text-[#4b8666]',
                    'type' => 'low',
                    'href' => '#sellerCommerceSideColumn',
                    'navigate' => false,
                    'subClass' => $lowStockCount > 0 ? 'text-[#a66b6b]' : 'text-[#4b8666]',
                ],
                [
                    'label' => 'Est. Net Revenue',
                    'value' => '₱' . number_format($estimatedRevenue, 2),
                    'sub' => number_format($platformCommissionRate, 0) . '% platform commission deducted',
                    'bg' => 'bg-[#fffaf1]',
                    'border' => 'border-[#eee0c6]',
                    'icon' => 'text-[#c3881f]',
                    'type' => 'revenue',
                    'href' => route('seller.reports'),
                    'navigate' => true,
                    'subClass' => 'text-[#8f867b]',
                ],
            ];
        @endphp

        @foreach ($summaryCards as $card)
            <a
                href="{{ $card['href'] }}"
                data-dashboard-card="{{ $card['type'] }}"
                @if ($card['navigate']) wire:navigate @endif
                class="group relative overflow-hidden rounded-[18px] border {{ $card['border'] }} bg-white px-4 py-4 shadow-[0_5px_18px_rgba(37,29,20,.025)] sm:px-[17px] sm:py-[16px]"
            >
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="text-[10.5px] font-semibold leading-4 text-[#6f675e]">
                                {{ $card['label'] }}
                            </p>
                        </div>

                        <p
                            @if ($card['type'] === 'products') id="sellerProductCount" @endif
                            class="mt-2.5 truncate text-[22px] font-bold leading-none tracking-[-0.045em] text-[#211d18] sm:text-[24px]"
                        >
                            {{ $card['value'] }}
                        </p>

                        <p class="mt-2.5 line-clamp-1 text-[9.5px] font-medium leading-4 {{ $card['subClass'] }}">
                            {{ $card['sub'] }}
                        </p>
                    </div>

                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-[12px] {{ $card['bg'] }} {{ $card['icon'] }} ">
                        @switch($card['type'])
                            @case('products')
                                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M5 7h14l-1 13H6L5 7Z"></path>
                                    <path d="M9 7a3 3 0 0 1 6 0"></path>
                                </svg>
                                @break
                            @case('orders')
                                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M6 5h12v15H6z"></path>
                                    <path d="M9 5V3h6v2"></path>
                                    <path d="M9 10h6"></path>
                                </svg>
                                @break
                            @case('sales')
                                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 18 9 13l3 3 7-8"></path>
                                    <path d="M15 8h4v4"></path>
                                </svg>
                                @break
                            @case('low')
                                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M12 3 3 20h18L12 3Z"></path>
                                    <path d="M12 9v5"></path>
                                    <path d="M12 17h.01"></path>
                                </svg>
                                @break
                            @default
                                <span class="text-[15px] font-semibold">₱</span>
                        @endswitch
                    </div>
                </div>
            </a>
        @endforeach
    </section>


    {{-- =========================================================
        CATALOG + SALES OVERVIEW — SAME DESKTOP ROW
        Layout only: existing IDs, JS hooks, routes, and backend data stay intact.
        Full product management still lives on the dedicated Products page.
    ========================================================== --}}
    <section id="sellerAnalyticsRow" class="mt-4">
        {{-- CATALOG CATEGORY BREAKDOWN — REAL SELLER E-COMMERCE DATA --}}
        <div id="sellerCategoryBreakdownCard" class="seller-category-card h-full overflow-hidden rounded-[18px] border border-[#e8dfd2] bg-white shadow-[0_7px_18px_rgba(33,24,14,.035)]">
            <div class="flex items-start justify-between gap-3 border-b border-[#eee8df] px-4 py-4">
                <div class="flex min-w-0 items-start gap-3">
                    <div class="min-w-0">
                        <h3 class="text-[18px] font-semibold tracking-[-0.03em] text-[#28221b] sm:text-[19px]">Catalog Category Breakdown</h3>
                        <p class="mt-1 text-[10.5px] leading-5 text-[#887f75]">Distribution of your active e-commerce product listings.</p>
                    </div>
                </div>

                <span class="shrink-0 rounded-full border border-[#eadfc9] bg-[#fffaf2] px-3 py-1.5 text-[9px] font-medium text-[#9b6c1c]">
                    Active Catalog
                </span>
            </div>

            <div class="seller-category-executive-body">
                <div class="seller-category-executive-donut">
                    <div id="sellerCategoryDonut" class="seller-category-donut" role="img" aria-label="Seller catalog category distribution" style="--seller-category-gradient: {{ $categoryPreviewGradient }};">
                        <div class="seller-category-donut-center">
                            <span id="sellerCategoryCenterLabel" class="seller-category-center-label">Total Listings</span>
                            <strong id="sellerCategoryCenterValue" class="seller-category-center-value">{{ number_format($totalProductCount) }}</strong>
                            <span id="sellerCategoryCenterSub" class="seller-category-center-sub hidden"></span>
                        </div>
                    </div>
                </div>

                <div class="seller-category-executive-list">
                    <div class="seller-category-executive-list-head">
                        <div>
                            <p class="seller-category-executive-kicker">Category mix</p>
                            <h4 class="seller-category-executive-title">Active listing distribution</h4>
                        </div>
                    </div>

                    <div id="sellerCategoryLegend" class="seller-category-ranked-list">
                        @forelse($categoryPreviewRows as $previewRow)
                            @php
                                $previewPercent = (float) ($previewRow['percentage'] ?? 0);
                                $previewPercentLabel = $previewPercent < 10 && fmod($previewPercent, 1.0) !== 0.0
                                    ? number_format($previewPercent, 1) . '%'
                                    : number_format(round($previewPercent)) . '%';
                            @endphp
                            <button
                                type="button"
                                data-seller-category="{{ $previewRow['name'] }}"
                                data-seller-category-count="{{ $previewRow['count'] }}"
                                data-seller-category-percent="{{ $previewPercent }}"
                                class="seller-category-ranked-row"
                                style="--seller-category-row-color:{{ $previewRow['color'] }};"
                                aria-label="{{ $previewRow['name'] }}: {{ $previewPercentLabel }}"
                            >
                                <span class="seller-category-ranked-top">
                                    <span class="seller-category-ranked-name">
                                        <i aria-hidden="true"></i>
                                        <span>{{ $previewRow['name'] }}</span>
                                    </span>
                                    <strong>{{ $previewPercentLabel }}</strong>
                                </span>
                                <span class="seller-category-ranked-track" aria-hidden="true">
                                    <span style="width:{{ max(2.5, $previewPercent) }}%"></span>
                                </span>
                            </button>
                        @empty
                            <div class="seller-category-ranked-empty">
                                <p class="text-[8.5px] font-medium text-[#766d63]">No active product categories yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="seller-category-executive-footer">
                <p>Percentages are based on active listing count.</p>

                <button
                    id="sellerCategoryViewProducts"
                    type="button"
                    class="seller-category-executive-action"
                >
                    <span id="sellerCategoryViewProductsLabel">View All Products</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- SALES PERFORMANCE — ADMIN-STYLE SELLER ANALYTICS --}}
        <div
            id="sellerSalesPerformanceCard"
            class="seller-sales-admin-card h-full"
        >
            <div class="seller-sales-admin-head">
                <div class="min-w-0">
                    <h3 class="seller-sales-admin-title">Sales Performance</h3>
                    <p id="sellerSalesPeriodLabel" class="seller-sales-admin-subtitle">
                        Delivered merchandise sales.
                    </p>
                </div>

                <div class="seller-sales-admin-periods" aria-label="Sales reporting period">
                    <button
                        type="button"
                        data-sales-period="month"
                        aria-pressed="true"
                        class="seller-sales-period-button is-active"
                    >
                        This Month
                    </button>
                    <button
                        type="button"
                        data-sales-period="last_month"
                        aria-pressed="false"
                        class="seller-sales-period-button"
                    >
                        Last Month
                    </button>
                    <button
                        type="button"
                        data-sales-period="year"
                        aria-pressed="false"
                        class="seller-sales-period-button"
                    >
                        This Year
                    </button>
                </div>
            </div>

            <div class="seller-sales-admin-metrics">
                <div class="seller-sales-admin-metric">
                    <p class="seller-sales-admin-metric-label">Total Sales</p>
                    <p id="sellerSalesTotal" class="seller-sales-admin-metric-value">₱0.00</p>
                </div>

                <div class="seller-sales-admin-metric">
                    <p class="seller-sales-admin-metric-label">Delivered Orders</p>
                    <p id="sellerSalesOrders" class="seller-sales-admin-metric-value">0</p>
                </div>

                <div class="seller-sales-admin-metric">
                    <p class="seller-sales-admin-metric-label">Average Order</p>
                    <p id="sellerSalesAverage" class="seller-sales-admin-metric-value">₱0.00</p>
                </div>
            </div>

            <div class="seller-sales-admin-divider"></div>

            <div class="seller-sales-admin-chart-head">
                <div>
                    <h4 class="seller-sales-admin-chart-title">Delivered sales trend</h4>
                    <p class="seller-sales-admin-chart-copy">
                        Merchandise sales from completed delivered orders
                    </p>
                </div>

                <div class="seller-sales-admin-legend" aria-label="Chart series">
                    <span class="seller-sales-admin-legend-item">
                        <i aria-hidden="true"></i>
                        Sales
                    </span>
                </div>
            </div>

            <div id="sellerSalesChartShell" class="seller-sales-admin-chart-shell">
                <div
                    id="sellerSalesTooltip"
                    class="seller-sales-admin-tooltip"
                    aria-hidden="true"
                >
                    <p id="sellerSalesTooltipPeriod" class="seller-sales-admin-tooltip-period">Period</p>
                    <div class="seller-sales-admin-tooltip-row">
                        <span><i aria-hidden="true"></i>Sales</span>
                        <strong id="sellerSalesTooltipValue">₱0.00</strong>
                    </div>
                </div>

                <svg
                    id="sellerSalesChart"
                    viewBox="0 0 900 320"
                    preserveAspectRatio="xMidYMid meet"
                    role="img"
                    aria-label="Seller delivered sales trend"
                >
<g id="sellerSalesGrid"></g>
                    <g id="sellerSalesGuides"></g>

                    <path
                        id="sellerSalesAreaPath"
                        class="seller-sales-admin-area"
                        fill="#D89B10"
                        fill-opacity=".08"
                    ></path>

                    <path
                        id="sellerSalesLinePath"
                        class="seller-sales-admin-line"
                        fill="none"
                    ></path>

                    <g id="sellerSalesPoints"></g>
                    <g id="sellerSalesXAxis"></g>
                    <g id="sellerSalesHitboxes"></g>
                </svg>

                <div
                    id="sellerSalesEmptyState"
                    class="seller-sales-admin-empty hidden"
                >
                    <div>
                        <p>No delivered sales in this period yet.</p>
                        <span>Completed Buyer → Seller → Courier orders will appear here.</span>
                    </div>
                </div>
            </div>

            <div class="seller-sales-admin-footer">
                <p id="sellerSalesBestPeriod">Best period: —</p>
                <p>Merchandise subtotal only · delivery fees excluded</p>
            </div>
        </div>

    </section>

    {{-- =========================================================
        FINANCIAL + ORDER MANAGEMENT + INVENTORY ALERTS
        One responsive row on desktop; stacked on smaller screens.
    ========================================================== --}}
    <section id="sellerCommerceSideColumn" class="mt-4">


        {{-- SELLER RECENT ACTIVITY --}}
        <div id="sellerRecentActivityCard" class="seller-activity-panel">
            <div class="seller-recent-head">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="min-w-0">
                        <h3 class="text-[15px] font-bold tracking-[-0.025em] text-[#28221b]">Recent Activity</h3>
                        <p class="mt-0.5 text-[8.5px] leading-4 text-[#91887d]">
                            Actions you performed in your seller workspace.
                        </p>
                    </div>
                </div>

                <span class="seller-activity-owner-badge">Seller activity</span>
            </div>

            <div class="seller-activity-list">
                @forelse ($sellerActivities->take(4) as $activity)
                    <a
                        href="{{ $activity['url'] ?? '#' }}"
                        class="seller-activity-item"
                        wire:navigate.hover
                    >
                        <span class="min-w-0 flex-1">
                            <span class="seller-activity-title">
                                {{ $activity['title'] ?? 'Seller activity' }}
                            </span>
                            <span class="seller-activity-description">
                                {{ $activity['description'] ?? '' }}
                            </span>
                            <span class="seller-activity-meta">
                                {{ $activity['meta'] ?? '' }}
                            </span>
                        </span>

                        <span class="seller-activity-time">
                            {{ $activity['occurred_at_human'] ?? 'Just now' }}
                        </span>
                    </a>
                @empty
                    <div class="px-5 py-10 text-center">
                        <span class="mx-auto grid h-10 w-10 place-items-center rounded-full bg-[#f4f7fa] text-[#6b7f93]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                                <circle cx="12" cy="12" r="8"></circle>
                                <path d="M12 8v4l3 2"></path>
                            </svg>
                        </span>
                        <p class="mt-3 text-[10px] font-semibold text-[#4d463e]">No seller activity yet.</p>
                        <p class="mt-1 text-[8px] text-[#958d84]">Product and fulfillment actions will appear here.</p>
                    </div>
                @endforelse
            </div>

            <div class="seller-recent-footer">
                <p>Your latest seller-side actions.</p>
                <a href="{{ route('seller.products.index') }}" wire:navigate.hover>
                    Manage products
                    <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 6 6 6-6 6"></path>
                    </svg>
                </a>
            </div>
        </div>

        {{-- RECENT BUYER ORDERS --}}
        <div id="sellerRecentOrdersCard" class="seller-activity-panel">
            <div class="seller-recent-head">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="min-w-0">
                        <h3 class="text-[15px] font-bold tracking-[-0.025em] text-[#28221b]">Recent Orders</h3>
                        <p class="mt-0.5 text-[8.5px] leading-4 text-[#91887d]">
                            See who ordered, what they bought, and the current order status.
                        </p>
                    </div>
                </div>

                <span class="seller-recent-live">Live orders</span>
            </div>

            <div class="seller-orders-feed">
                @forelse ($recentOrdersFeed->take(4) as $order)
                    @php
                        $orderStatus = (string) ($order['status'] ?? '');

                        $orderTone = match ($orderStatus) {
                            'new' => 'seller-recent-status--new',
                            'preparing', 'ready_for_pickup' => 'seller-recent-status--progress',
                            'courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit', 'arrived_buyer' => 'seller-recent-status--courier',
                            'delivered' => 'seller-recent-status--success',
                            'cancelled' => 'seller-recent-status--danger',
                            default => 'seller-recent-status--progress',
                        };
                    @endphp

                    <a
                        href="{{ route('seller.orders') }}"
                        class="seller-order-feed-item"
                        wire:navigate.hover
                        aria-label="Open order {{ $order['order_number'] ?? '' }}"
                    >
                        <span class="seller-recent-avatar" aria-hidden="true">
                            @if (!empty($order['product_image_url']))
                                <img
                                    src="{{ $order['product_image_url'] }}"
                                    alt=""
                                    loading="lazy"
                                    decoding="async"
                                    onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                >
                                <span class="seller-recent-image-fallback hidden">
                                    {{ mb_strtoupper(mb_substr((string) ($order['item_summary'] ?? 'P'), 0, 1)) }}
                                </span>
                            @else
                                <span class="seller-recent-image-fallback">
                                    {{ mb_strtoupper(mb_substr((string) ($order['item_summary'] ?? 'P'), 0, 1)) }}
                                </span>
                            @endif

                            @if (!empty($order['is_new']))
                                <i class="seller-recent-new-dot" aria-hidden="true"></i>
                            @endif
                        </span>

                        <span class="min-w-0 flex-1">
                            <span class="seller-recent-buyer">
                                {{ $order['buyer_name'] ?? 'SARI Buyer' }}
                            </span>

                            <span class="seller-recent-product">
                                Ordered {{ $order['item_summary'] ?? 'items' }}
                            </span>

                            <span class="seller-recent-meta">
                                <span>{{ $order['order_number'] ?? 'Order' }}</span>
                                <span>•</span>
                                <span>{{ number_format((int) ($order['total_quantity'] ?? 0)) }} qty</span>
                                @if (!empty($order['payment_method']))
                                    <span>•</span>
                                    <span>{{ $order['payment_method'] }}</span>
                                @endif
                            </span>
                        </span>

                        <span class="seller-recent-side">
                            <span class="seller-recent-total">
                                ₱{{ number_format((float) ($order['total'] ?? 0), 2) }}
                            </span>

                            <span class="mt-1.5 block">
                                <span class="seller-recent-status {{ $orderTone }}">
                                    {{ $order['status_label'] ?? 'Order Update' }}
                                </span>
                            </span>

                            <span class="seller-recent-time">
                                {{ $order['activity_human'] ?? 'Just now' }}
                            </span>
                        </span>
                    </a>
                @empty
                    <div class="px-5 py-10 text-center">
                        <span class="mx-auto grid h-10 w-10 place-items-center rounded-full bg-[#fff8e9] text-[#ad7720]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M5 7h14l-1 13H6L5 7Z"></path>
                                <path d="M9 7a3 3 0 0 1 6 0"></path>
                            </svg>
                        </span>
                        <p class="mt-3 text-[10px] font-semibold text-[#4d463e]">No buyer orders yet.</p>
                        <p class="mt-1 text-[8px] text-[#958d84]">New orders will appear here automatically.</p>
                    </div>
                @endforelse
            </div>

            <div class="seller-recent-footer">
                <p>
                    Showing {{ min(4, $recentOrdersFeed->count()) }} latest
                    {{ $recentOrdersFeed->count() === 1 ? 'order' : 'orders' }}.
                </p>

                <a href="{{ route('seller.orders') }}" wire:navigate.hover>
                    View all orders
                    <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 6 6 6-6 6"></path>
                    </svg>
                </a>
            </div>
        </div>

        {{-- INVENTORY ALERTS --}}
        <div class="seller-side-panel seller-inventory-panel flex h-full min-w-0 flex-col rounded-[18px] border border-[#ebe4da] bg-white p-4 shadow-[0_7px_18px_rgba(33,24,14,.035)]">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <h3 class="seller-panel-title font-bold tracking-[-0.02em] text-[#28221b]">Inventory Alerts</h3>
                    <p class="mt-1 text-[#91887d]">Products that need attention.</p>
                </div>
                <span class="seller-inventory-count shrink-0 rounded-full bg-[#fff0f0] px-2.5 py-1 font-bold text-[#b55c5c]">{{ $lowStockCount }} low</span>
            </div>

            <div class="mt-3 space-y-2">
                @forelse ($lowStockProducts as $lowStockProduct)
                    <div class="seller-inventory-row rounded-lg border border-[#eee8df]">
                        @php
                            $inventoryImageUrl = !empty($lowStockProduct->image_path)
                                ? route('seller.products.image', $lowStockProduct)
                                : null;
                        @endphp

                        <div class="flex items-center justify-between gap-3">
                            <div class="seller-inventory-product">
                                <span class="seller-inventory-thumb" aria-hidden="true">
                                    @if ($inventoryImageUrl)
                                        <img
                                            src="{{ $inventoryImageUrl }}"
                                            alt=""
                                            loading="lazy"
                                            decoding="async"
                                            onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                        >
                                        <span class="seller-inventory-thumb-fallback hidden">
                                            {{ mb_strtoupper(mb_substr((string) $lowStockProduct->name, 0, 1)) }}
                                        </span>
                                    @else
                                        <span class="seller-inventory-thumb-fallback">
                                            {{ mb_strtoupper(mb_substr((string) $lowStockProduct->name, 0, 1)) }}
                                        </span>
                                    @endif
                                </span>

                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-[#3d3730]">{{ $lowStockProduct->name }}</p>
                                    <p class="mt-1 truncate text-[#958c80]">SKU: {{ $lowStockProduct->sku }}</p>
                                </div>
                            </div>

                            <span class="seller-inventory-count shrink-0 font-bold {{ (int) $lowStockProduct->stock <= 2 ? 'text-[#c24d4d]' : 'text-[#b47718]' }}">
                                {{ $lowStockProduct->stock }} left
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-[#dce9e1] bg-[#f7fbf8] px-3 py-4 text-center">
                        <p class="font-semibold text-[#56816a]">Inventory levels look healthy.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-auto pt-3">
                <a
                    href="{{ route('seller.products.index') }}"
                    class="seller-inventory-button flex w-full items-center justify-center rounded-lg border border-[#d48f08] bg-[#d48f08] px-4 font-semibold text-white shadow-[0_7px_16px_rgba(212,143,8,.12)] transition hover:border-[#bd7d05] hover:bg-[#bd7d05]"
                    wire:navigate
                >
                    Manage Inventory
                </a>
            </div>
        </div>

    </section>


    {{-- =========================================================
        FINANCIAL SNAPSHOT + ORDER MANAGEMENT
        Moved below the main commerce row; functionality preserved.
    ========================================================== --}}
    <section id="sellerFinancialOrdersRow" class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        {{-- FINANCIAL SNAPSHOT --}}
        <div class="seller-side-panel seller-financial-panel flex h-full min-w-0 flex-col rounded-[18px] border border-[#ebe4da] bg-white p-4 shadow-[0_7px_18px_rgba(33,24,14,.035)]">
            <div>
                <h3 class="seller-panel-title font-bold tracking-[-0.02em] text-[#28221b]">Financial Snapshot</h3>
                <p class="mt-1 text-[#91887d]">Quick summary before generating a full report.</p>
            </div>

            <div class="mt-3 space-y-2">
                <div class="seller-financial-metric">
                    <p class="seller-financial-metric-label">Gross Sales</p>
                    <p class="seller-financial-metric-value">₱{{ number_format($monthlySales, 2) }}</p>
                </div>

                <div class="seller-financial-metric">
                    <p class="seller-financial-metric-label">Est. Platform Commission ({{ number_format($platformCommissionRate, 0) }}%)</p>
                    <p class="seller-financial-metric-value">₱{{ number_format($platformCommission, 2) }}</p>
                </div>

                <div class="seller-financial-metric">
                    <p class="seller-financial-metric-label">Estimated Net Revenue</p>
                    <p class="seller-financial-metric-value">₱{{ number_format($estimatedRevenue, 2) }}</p>
                </div>
            </div>

            <div class="mt-auto pt-3">
                <a
                    href="{{ route('seller.reports') }}"
                    class="flex h-10 w-full items-center justify-between rounded-[10px] bg-[#e9a315] px-3.5 text-[9.5px] font-semibold text-white shadow-[0_7px_16px_rgba(212,143,8,.11)] transition hover:bg-[#d8940d]"
                    wire:navigate
                >
                    <span class="truncate">Generate Full Report</span>
                </a>
            </div>
        </div>

        {{-- ORDER MANAGEMENT --}}
        <div id="sellerOrderManagementPanel" class="seller-side-panel seller-order-panel flex h-full min-w-0 flex-col rounded-[18px] border border-[#ebe4da] bg-white p-4 shadow-[0_7px_18px_rgba(33,24,14,.035)]">
            <div>
                <h3 class="seller-panel-title font-bold tracking-[-0.02em] text-[#28221b]">Order Management</h3>
                <p class="mt-1 text-[#91887d]">View your seller workflow.</p>
            </div>

            @php
                $compactOrderFlow = [
                    ['title' => 'New Orders', 'count' => (int) ($orderStats['new'] ?? 0), 'bg' => 'bg-[#fff6e8]', 'text' => 'text-[#b57a18]'],
                    ['title' => 'Preparing', 'count' => (int) ($orderStats['preparing'] ?? 0), 'bg' => 'bg-[#eef5fc]', 'text' => 'text-[#5e7f9f]'],
                    ['title' => 'Ready Pickup', 'count' => (int) ($orderStats['ready'] ?? 0), 'bg' => 'bg-[#eef8f2]', 'text' => 'text-[#56816a]'],
                    ['title' => 'Courier Flow', 'count' => (int) ($orderStats['courier'] ?? 0), 'bg' => 'bg-[#f5f1f9]', 'text' => 'text-[#7b6a8b]'],
                    ['title' => 'Delivered', 'count' => (int) ($orderStats['delivered'] ?? 0), 'bg' => 'bg-[#edf7f1]', 'text' => 'text-[#56816a]'],
                    ['title' => 'Feedback', 'count' => (int) ($orderStats['feedback'] ?? 0), 'bg' => 'bg-[#fff8ec]', 'text' => 'text-[#9a7839]'],
                ];
            @endphp

            <div class="mt-3 space-y-1.5">
                @foreach ($compactOrderFlow as $item)
                    <a
                        href="{{ route('seller.orders') }}"
                        class="seller-order-row flex items-center justify-between rounded-lg border border-[#eee8df] transition hover:bg-[#fcfaf7]"
                        wire:navigate
                    >
                        <span class="font-medium text-[#4a433b]">{{ $item['title'] }}</span>
                        <span class="grid h-6 min-w-[24px] place-items-center rounded-lg {{ $item['bg'] }} px-1.5 text-[8px] font-bold {{ $item['text'] }}">{{ $item['count'] }}</span>
                    </a>
                @endforeach
            </div>

            <div class="mt-auto pt-3">
                <a
                    href="{{ route('seller.orders') }}"
                    class="seller-order-view-all flex w-full items-center justify-center rounded-lg border border-[#dfd5c7] font-semibold text-[#9b6b1c] transition hover:bg-[#fffaf2]"
                    wire:navigate
                >
                    View All Orders
                </a>
            </div>
        </div>
    </section>

    <div class="h-4"></div>
</div>
</div>


{{-- Product CRUD modals were moved to the dedicated Product Management page. --}}

{{-- =============================================================
    REAL-TIME ADMIN COMPLIANCE POPUP
============================================================== --}}
<div id="sellerRealtimeAlert" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/45 p-4 backdrop-blur-[3px]">
    <div id="sellerRealtimeAlertCard" class="w-full max-w-[520px] scale-95 rounded-[26px] border border-[#ead8d1] bg-white p-6 opacity-0 shadow-[0_35px_100px_rgba(38,30,18,.30)] transition-all duration-300 sm:p-7">
        <div class="flex items-start gap-4">
            <div id="realtimeAlertIcon" class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-[#fff2ef] text-[#b8685f]">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3 3 20h18L12 3Z"></path><path d="M12 9v5"></path><path d="M12 17h.01"></path></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[9px] font-semibold uppercase tracking-[0.14em] text-[#a8731f]">SARI Compliance Center</p>
                <h3 id="realtimeAlertTitle" class="mt-2 text-[20px] font-bold tracking-[-0.03em] text-[#28221b]">Compliance Update</h3>
                <p id="realtimeAlertProduct" class="mt-1 hidden text-[10px] font-semibold text-[#756d63]"></p>
            </div>
        </div>

        <div class="mt-5 rounded-[16px] border border-[#eee4da] bg-[#fcfaf7] p-4">
            <p id="realtimeAlertMessage" class="text-[11px] leading-6 text-[#6f675e]"></p>
            <div id="realtimeWarningBadge" class="mt-3 hidden w-fit rounded-full bg-[#faeeee] px-3 py-1.5 text-[9px] font-bold text-[#ad5f5f]"></div>
            <p id="realtimeSuspendedUntil" class="mt-3 hidden text-[10px] font-semibold text-[#8c605b]"></p>
        </div>

        <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <a href="{{ route('seller.messages') }}" id="realtimeMessageAdmin" class="hidden h-11 items-center justify-center rounded-xl border border-[#e0d7ca] bg-white px-5 text-[10px] font-semibold text-[#62594e]"
                wire:navigate
            >Message Admin</a>
            <button id="closeRealtimeAlert" type="button" class="h-11 rounded-xl bg-[#c99128] px-6 text-[10px] font-semibold text-white hover:bg-[#b47e1e]">Got It</button>
        </div>
    </div>
</div>


@endsection


@push('scripts')

<script>
document.addEventListener('livewire:navigated', function () {
    const run = function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD EVENT LIFECYCLE
    |--------------------------------------------------------------------------
    | Every time Livewire revisits Dashboard, abort the old document listeners
    | first. This prevents duplicate click/keydown handlers from accumulating
    | and making Seller actions feel slower over time.
    */
    window.__SARI_SELLER_DASHBOARD_EVENT_ABORT__?.abort();
    const sellerDashboardEventAbort = new AbortController();
    window.__SARI_SELLER_DASHBOARD_EVENT_ABORT__ = sellerDashboardEventAbort;
    const sellerDashboardEventSignal = sellerDashboardEventAbort.signal;

    /*
    |--------------------------------------------------------------------------
    | SHARED SMOOTH MODAL HELPERS
    |--------------------------------------------------------------------------
    | Keeps existing modal actions intact while improving open/close motion.
    */
    function hasAnotherOpenSellerModal(exceptElement = null) {
        const modalIds = [
            'sellerAddProductModal',
            'sellerAllProductsModal',
            'sellerViewProductModal',
            'sellerEditProductModal',
            'sellerProductActionModal',
            'sellerRealtimeAlert'
        ];

        return modalIds.some(function (id) {
            const element = document.getElementById(id);
            return element &&
                element !== exceptElement &&
                !element.classList.contains('hidden');
        });
    }

    function showSmoothSellerModal(element) {
        if (!element) return;

        element.classList.remove('hidden');
        element.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                element.classList.add('seller-modal-visible');
            });
        });
    }

    function hideSmoothSellerModal(element) {
        if (!element || element.classList.contains('hidden')) return;

        element.classList.remove('seller-modal-visible');

        window.setTimeout(function () {
            element.classList.add('hidden');
            element.classList.remove('flex');

            if (!hasAnotherOpenSellerModal(element)) {
                document.body.classList.remove('overflow-hidden');
            }
        }, 230);
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO-DISMISS DASHBOARD STATUS NOTICES
    |--------------------------------------------------------------------------
    | Normal success / warning / information cards stay visible for 5 seconds.
    | Critical 3-warning restriction is handled by the permanent layout lock.
    */
    const dashboardNotice = document.querySelector('[data-auto-dismiss-notice]');

    if (dashboardNotice) {
        window.setTimeout(function () {
            dashboardNotice.classList.add('opacity-0', '-translate-y-1');

            window.setTimeout(function () {
                dashboardNotice.style.display = 'none';
            }, 500);
        }, 5000);
    }



    /*
    |--------------------------------------------------------------------------
    | DYNAMIC GREETING + DATE / TIME / WEATHER
    |--------------------------------------------------------------------------
    | Front-end only. Uses browser time and Open-Meteo weather.
    */
    const greetingLabel = document.getElementById('sellerGreetingLabel');
    const currentTime = document.getElementById('sellerCurrentTime');
    const timeContext = document.getElementById('sellerTimeContext');
    const currentDay = document.getElementById('sellerCurrentDay');
    const currentDate = document.getElementById('sellerCurrentDate');
    const weatherTemp = document.getElementById('sellerWeatherTemp');
    const weatherCondition = document.getElementById('sellerWeatherCondition');

    /*
    | Cache formatters once. The visible clock only shows minutes, so rebuilding
    | four Intl formatters every second wastes main-thread work without changing
    | anything the seller can see.
    */
    const sellerTimeFormatter = new Intl.DateTimeFormat('en-PH', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
    });
    const sellerDayFormatter = new Intl.DateTimeFormat('en-PH', {
        weekday: 'long',
    });
    const sellerDateFormatter = new Intl.DateTimeFormat('en-PH', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
    });
    const sellerTimeZoneLabel = (
        Intl.DateTimeFormat().resolvedOptions().timeZone || 'Local Time'
    ).replaceAll('_', ' ');

    function getGreetingByHour(hour) {
        if (hour >= 5 && hour < 12) return 'Good morning';
        if (hour >= 12 && hour < 18) return 'Good afternoon';
        return 'Good evening';
    }

    function updateSellerDateTime() {
        const now = new Date();
        const hour = now.getHours();
        const greeting = getGreetingByHour(hour);

        if (greetingLabel) {
            greetingLabel.textContent = `${greeting}, Seller!`;
        }

        if (currentTime) {
            currentTime.textContent = sellerTimeFormatter.format(now);
        }

        if (timeContext) {
            timeContext.textContent = sellerTimeZoneLabel;
        }

        if (currentDay) {
            currentDay.textContent = sellerDayFormatter.format(now);
        }

        if (currentDate) {
            currentDate.textContent = sellerDateFormatter.format(now);
        }
    }

    function scheduleSellerClock() {
        updateSellerDateTime();
        window.clearTimeout(window.__SARI_SELLER_DASHBOARD_CLOCK__);

        /* Update just after the next minute boundary because seconds are hidden. */
        const delay = 60000 - (Date.now() % 60000) + 25;
        window.__SARI_SELLER_DASHBOARD_CLOCK__ = window.setTimeout(
            scheduleSellerClock,
            delay
        );
    }

    function weatherCodeToText(code) {
        const map = {
            0: 'Clear sky',
            1: 'Mainly clear',
            2: 'Partly cloudy',
            3: 'Overcast',
            45: 'Foggy',
            48: 'Rime fog',
            51: 'Light drizzle',
            53: 'Drizzle',
            55: 'Heavy drizzle',
            56: 'Freezing drizzle',
            57: 'Heavy freezing drizzle',
            61: 'Light rain',
            63: 'Rain',
            65: 'Heavy rain',
            66: 'Freezing rain',
            67: 'Heavy freezing rain',
            71: 'Light snow',
            73: 'Snow',
            75: 'Heavy snow',
            77: 'Snow grains',
            80: 'Rain showers',
            81: 'Heavy showers',
            82: 'Violent showers',
            85: 'Snow showers',
            86: 'Heavy snow showers',
            95: 'Thunderstorm',
            96: 'Storm with hail',
            99: 'Severe storm'
        };

        return map[Number(code)] || 'Weather unavailable';
    }

    const sellerWeatherCacheKey = 'sari:seller-dashboard-weather-v1';
    const sellerWeatherCacheMaxAge = 10 * 60 * 1000;

    function paintSellerWeather(snapshot) {
        if (!weatherTemp || !weatherCondition || !snapshot) return;

        weatherTemp.textContent = `${Number(snapshot.temperature)}°C`;
        weatherCondition.textContent = String(snapshot.condition || 'Current conditions');
    }

    function readCachedSellerWeather() {
        try {
            const raw = window.sessionStorage.getItem(sellerWeatherCacheKey);
            if (!raw) return null;

            const cached = JSON.parse(raw);
            if (
                !Number.isFinite(Number(cached?.temperature))
                || !cached?.condition
                || Date.now() - Number(cached?.savedAt || 0) > sellerWeatherCacheMaxAge
            ) {
                return null;
            }

            return cached;
        } catch (_) {
            return null;
        }
    }

    function cacheSellerWeather(snapshot) {
        try {
            window.sessionStorage.setItem(
                sellerWeatherCacheKey,
                JSON.stringify({ ...snapshot, savedAt: Date.now() })
            );
        } catch (_) {}
    }

    async function fetchSellerWeather(latitude, longitude) {
        if (!weatherTemp || !weatherCondition) return;

        try {
            weatherCondition.textContent = 'Current conditions';

            const response = await fetch(
                `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&current=temperature_2m,weather_code&timezone=auto`,
                { signal: sellerDashboardEventSignal }
            );

            if (!response.ok) {
                throw new Error('Weather request failed');
            }

            const data = await response.json();
            const current = data?.current || {};
            const snapshot = {
                temperature: Math.round(Number(current.temperature_2m ?? 0)),
                condition: weatherCodeToText(current.weather_code),
            };

            paintSellerWeather(snapshot);
            cacheSellerWeather(snapshot);
        } catch (error) {
            if (error?.name === 'AbortError') return;

            weatherTemp.textContent = '—';
            weatherCondition.textContent = 'Weather unavailable';
        }
    }

    function initSellerWeather() {
        const cachedWeather = readCachedSellerWeather();
        if (cachedWeather) {
            paintSellerWeather(cachedWeather);
            return;
        }

        const fallback = { latitude: 14.5995, longitude: 120.9842 };

        // Paint useful weather immediately instead of waiting for geolocation.
        fetchSellerWeather(fallback.latitude, fallback.longitude);

        if (!navigator.geolocation) {
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (position) {
                if (sellerDashboardEventSignal.aborted) return;

                fetchSellerWeather(
                    position.coords.latitude,
                    position.coords.longitude
                );
            },
            function () {
                // Fallback request is already running / already painted.
            },
            {
                enableHighAccuracy: false,
                timeout: 3000,
                maximumAge: 30 * 60 * 1000,
            }
        );
    }

    scheduleSellerClock();

    document.addEventListener('livewire:navigating', function () {
        window.clearTimeout(window.__SARI_SELLER_DASHBOARD_CLOCK__);
        window.__SARI_SELLER_DASHBOARD_CLOCK__ = null;

        sellerDashboardEventAbort.abort();
    }, { once: true });
    initSellerWeather();

    /*
    |--------------------------------------------------------------------------
    | REAL SALES PERFORMANCE CHART
    |--------------------------------------------------------------------------
    | All numbers come from SellerDashboardController and delivered
    | MarketplaceOrder subtotals. Buttons switch datasets instantly.
    */
    const sellerSalesPerformanceData = @json($salesPerformance);
    const sellerSalesPeriodButtons = Array.from(document.querySelectorAll('[data-sales-period]'));
    const sellerSalesChart = document.getElementById('sellerSalesChart');
    const sellerSalesGrid = document.getElementById('sellerSalesGrid');
    const sellerSalesGuides = document.getElementById('sellerSalesGuides');
    const sellerSalesAreaPath = document.getElementById('sellerSalesAreaPath');
    const sellerSalesLinePath = document.getElementById('sellerSalesLinePath');
    const sellerSalesPoints = document.getElementById('sellerSalesPoints');
    const sellerSalesXAxis = document.getElementById('sellerSalesXAxis');
    const sellerSalesHitboxes = document.getElementById('sellerSalesHitboxes');
    const sellerSalesChartShell = document.getElementById('sellerSalesChartShell');
    const sellerSalesTooltip = document.getElementById('sellerSalesTooltip');
    const sellerSalesTooltipPeriod = document.getElementById('sellerSalesTooltipPeriod');
    const sellerSalesTooltipValue = document.getElementById('sellerSalesTooltipValue');
    const sellerSalesTotal = document.getElementById('sellerSalesTotal');
    const sellerSalesOrders = document.getElementById('sellerSalesOrders');
    const sellerSalesAverage = document.getElementById('sellerSalesAverage');
    const sellerSalesPeriodLabel = document.getElementById('sellerSalesPeriodLabel');
    const sellerSalesBestPeriod = document.getElementById('sellerSalesBestPeriod');
    const sellerSalesEmptyState = document.getElementById('sellerSalesEmptyState');

    function sellerPeso(value) {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(Number(value || 0)).replace('PHP', '₱').replace(/\s/g, '');
    }

    function sellerCompactPeso(value) {
        const amount = Number(value || 0);
        const absolute = Math.abs(amount);

        if (absolute >= 1000000) {
            return '₱' + (amount / 1000000).toFixed(absolute >= 10000000 ? 0 : 1).replace(/\.0$/, '') + 'M';
        }

        if (absolute >= 1000) {
            return '₱' + (amount / 1000).toFixed(absolute >= 100000 ? 0 : 1).replace(/\.0$/, '') + 'K';
        }

        return '₱' + Math.round(amount).toLocaleString('en-PH');
    }

    function sellerNiceMaximum(value) {
        const raw = Number(value || 0);

        if (raw <= 0) return 1000;

        const exponent = Math.floor(Math.log10(raw));
        const magnitude = Math.pow(10, exponent);
        const normalized = raw / magnitude;

        let nice = 1;
        if (normalized <= 1) nice = 1;
        else if (normalized <= 2) nice = 2;
        else if (normalized <= 5) nice = 5;
        else nice = 10;

        return nice * magnitude;
    }

    function sellerSvgElement(name, attrs = {}, text = '') {
        const element = document.createElementNS('http://www.w3.org/2000/svg', name);

        Object.entries(attrs).forEach(([key, value]) => {
            element.setAttribute(key, String(value));
        });

        if (text !== '') {
            element.textContent = text;
        }

        return element;
    }

    let sellerSalesTooltipFrame = 0;
    let sellerSalesTooltipPending = null;
    let sellerSalesTooltipShellRect = null;
    let sellerSalesTooltipWidth = 0;
    let sellerSalesTooltipHeight = 0;

    function hideSellerSalesTooltip() {
        if (sellerSalesTooltipFrame) {
            window.cancelAnimationFrame(sellerSalesTooltipFrame);
            sellerSalesTooltipFrame = 0;
        }

        sellerSalesTooltipPending = null;
        sellerSalesTooltipShellRect = null;
        sellerSalesTooltipWidth = 0;
        sellerSalesTooltipHeight = 0;

        sellerSalesTooltip?.classList.remove('is-visible');
        sellerSalesTooltip?.setAttribute('aria-hidden', 'true');

        if (sellerSalesGuides) {
            sellerSalesGuides.querySelectorAll('[data-seller-sales-guide]').forEach((guide) => {
                guide.setAttribute('opacity', '0');
            });
        }
    }

    function showSellerSalesTooltip(point, guideIndex, pointer = null) {
        if (!sellerSalesTooltip || !sellerSalesChartShell) return;

        if (sellerSalesTooltipPeriod) {
            sellerSalesTooltipPeriod.textContent = point.label || 'Period';
        }

        if (sellerSalesTooltipValue) {
            sellerSalesTooltipValue.textContent = sellerPeso(point.value || 0);
        }

        if (sellerSalesGuides) {
            sellerSalesGuides.querySelectorAll('[data-seller-sales-guide]').forEach((guide) => {
                guide.setAttribute(
                    'opacity',
                    guide.getAttribute('data-seller-sales-guide') === String(guideIndex) ? '1' : '0'
                );
            });
        }

        sellerSalesTooltip.classList.add('is-visible');
        sellerSalesTooltip.setAttribute('aria-hidden', 'false');

        sellerSalesTooltipShellRect ||= sellerSalesChartShell.getBoundingClientRect();
        sellerSalesTooltipWidth ||= sellerSalesTooltip.offsetWidth || 128;
        sellerSalesTooltipHeight ||= sellerSalesTooltip.offsetHeight || 58;

        const shellRect = sellerSalesTooltipShellRect;
        const tooltipWidth = sellerSalesTooltipWidth;
        const tooltipHeight = sellerSalesTooltipHeight;

        let left = pointer
            ? pointer.clientX - shellRect.left + 10
            : (point.x / 900) * shellRect.width + 10;

        let top = pointer
            ? pointer.clientY - shellRect.top - tooltipHeight - 10
            : (point.y / 320) * sellerSalesChartShell.clientHeight - tooltipHeight - 8;

        left = Math.max(6, Math.min(left, sellerSalesChartShell.clientWidth - tooltipWidth - 6));
        top = Math.max(4, Math.min(top, sellerSalesChartShell.clientHeight - tooltipHeight - 4));

        sellerSalesTooltip.style.left = `${left}px`;
        sellerSalesTooltip.style.top = `${top}px`;
    }

    function scheduleSellerSalesTooltip(point, guideIndex, event = null) {
        sellerSalesTooltipPending = {
            point,
            guideIndex,
            pointer: event
                ? { clientX: event.clientX, clientY: event.clientY }
                : null,
        };

        if (sellerSalesTooltipFrame) return;

        sellerSalesTooltipFrame = window.requestAnimationFrame(() => {
            sellerSalesTooltipFrame = 0;

            const pending = sellerSalesTooltipPending;
            if (!pending) return;

            showSellerSalesTooltip(
                pending.point,
                pending.guideIndex,
                pending.pointer
            );
        });
    }

    function animateSellerSalesGraph() {
        if (!sellerSalesLinePath || !sellerSalesAreaPath) return;

        sellerSalesLinePath.style.transition = 'none';
        sellerSalesLinePath.style.strokeDasharray = '';
        sellerSalesLinePath.style.strokeDashoffset = '';

        sellerSalesAreaPath.style.transition = 'none';
        sellerSalesAreaPath.style.opacity = '1';

        sellerSalesPoints?.querySelectorAll('circle').forEach((circle) => {
            circle.style.transition = 'none';
            circle.style.opacity = '1';
            circle.style.transform = 'none';
        });
    }

    function renderSellerSalesPerformance(period = 'month') {
        if (!sellerSalesChart) return;

        hideSellerSalesTooltip();

        const dataset = sellerSalesPerformanceData?.[period] || {};
        const labels = Array.isArray(dataset.labels) ? dataset.labels : [];
        const values = Array.isArray(dataset.values) ? dataset.values.map(Number) : [];

        const total = Number(dataset.total || 0);
        const orderCount = Number(dataset.order_count || 0);
        const averageOrder = Number(dataset.average_order || 0);

        if (sellerSalesTotal) sellerSalesTotal.textContent = sellerPeso(total);
        if (sellerSalesOrders) sellerSalesOrders.textContent = orderCount.toLocaleString('en-PH');
        if (sellerSalesAverage) sellerSalesAverage.textContent = sellerPeso(averageOrder);

        if (sellerSalesPeriodLabel) {
            sellerSalesPeriodLabel.textContent =
                `${dataset.period_label || 'Sales period'} · delivered merchandise sales`;
        }

        if (sellerSalesBestPeriod) {
            sellerSalesBestPeriod.textContent = Number(dataset.best_value || 0) > 0
                ? `Best period: ${dataset.best_label || '—'} · ${sellerPeso(dataset.best_value || 0)}`
                : 'Best period: —';
        }

        sellerSalesPeriodButtons.forEach((button) => {
            const active = button.dataset.salesPeriod === period;
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        });

        if (
            !sellerSalesGrid ||
            !sellerSalesGuides ||
            !sellerSalesAreaPath ||
            !sellerSalesLinePath ||
            !sellerSalesPoints ||
            !sellerSalesXAxis ||
            !sellerSalesHitboxes
        ) {
            return;
        }

        sellerSalesGrid.innerHTML = '';
        sellerSalesGuides.innerHTML = '';
        sellerSalesPoints.innerHTML = '';
        sellerSalesXAxis.innerHTML = '';
        sellerSalesHitboxes.innerHTML = '';

        const chart = {
            left: 84,
            right: 858,
            top: 34,
            bottom: 230,
        };

        const width = chart.right - chart.left;
        const height = chart.bottom - chart.top;
        const maxValue = sellerNiceMaximum(Math.max(0, ...values));

        // Horizontal grid + Y-axis labels.
        for (let index = 0; index <= 4; index++) {
            const ratio = index / 4;
            const y = chart.top + (height * ratio);
            const axisValue = maxValue * (1 - ratio);

            sellerSalesGrid.appendChild(sellerSvgElement('line', {
                x1: chart.left,
                y1: y,
                x2: chart.right,
                y2: y,
                stroke: index === 4 ? '#E1E6EC' : '#EEF1F4',
                'stroke-width': 1,
                'stroke-dasharray': index === 4 ? '0' : '4 7',
            }));

            sellerSalesGrid.appendChild(sellerSvgElement('text', {
                x: 18,
                y: y + 4,
                fill: '#6B7686',
                'font-size': 12,
                'font-family': 'Poppins, sans-serif',
                'font-weight': 500,
            }, sellerCompactPeso(axisValue)));
        }

        const count = Math.max(labels.length, values.length);

        if (!count) {
            sellerSalesAreaPath.setAttribute('d', '');
            sellerSalesLinePath.setAttribute('d', '');
            sellerSalesEmptyState?.classList.remove('hidden');
            return;
        }

        const points = Array.from({ length: count }, (_, index) => {
            const x = count === 1
                ? chart.left + (width / 2)
                : chart.left + (width * index / (count - 1));

            const value = Number(values[index] || 0);
            const y = chart.bottom - ((value / maxValue) * height);

            return {
                x,
                y,
                value,
                label: labels[index] || `Period ${index + 1}`,
            };
        });

        const lineD = points
            .map((point, index) => `${index === 0 ? 'M' : 'L'}${point.x.toFixed(2)} ${point.y.toFixed(2)}`)
            .join(' ');

        const areaD = `${lineD} L${points[points.length - 1].x.toFixed(2)} ${chart.bottom} L${points[0].x.toFixed(2)} ${chart.bottom} Z`;

        sellerSalesLinePath.setAttribute('d', lineD);
        sellerSalesAreaPath.setAttribute('d', areaD);

        const defaultHitWidth = count > 1
            ? Math.min(150, width / (count - 1))
            : 150;

        points.forEach((point, index) => {
            // Light vertical guide in the Admin graph style.
            sellerSalesGrid.appendChild(sellerSvgElement('line', {
                x1: point.x,
                y1: chart.top,
                x2: point.x,
                y2: chart.bottom,
                stroke: '#F1F3F6',
                'stroke-width': 1,
            }));

            const hoverGuide = sellerSvgElement('line', {
                x1: point.x,
                y1: chart.top,
                x2: point.x,
                y2: chart.bottom,
                stroke: '#CBD3DD',
                'stroke-width': 1,
                'stroke-dasharray': '4 5',
                opacity: 0,
                'pointer-events': 'none',
                'data-seller-sales-guide': index,
            });
            sellerSalesGuides.appendChild(hoverGuide);

            const circle = sellerSvgElement('circle', {
                cx: point.x,
                cy: point.y,
                r: 5.2,
                fill: '#FFFFFF',
                stroke: '#C88C16',
                'stroke-width': 2.4,
            });
            sellerSalesPoints.appendChild(circle);

            sellerSalesXAxis.appendChild(sellerSvgElement('text', {
                x: point.x,
                y: 296,
                fill: '#6B7686',
                'font-size': count > 8 ? 10 : 12,
                'font-family': 'Poppins, sans-serif',
                'font-weight': 500,
                'text-anchor': 'middle',
            }, point.label));

            const hitLeft = index === 0
                ? chart.left - (defaultHitWidth / 2)
                : ((points[index - 1].x + point.x) / 2);

            const hitRight = index === points.length - 1
                ? chart.right + (defaultHitWidth / 2)
                : ((point.x + points[index + 1].x) / 2);

            const hitbox = sellerSvgElement('rect', {
                x: Math.max(chart.left - 26, hitLeft),
                y: chart.top - 8,
                width: Math.max(28, Math.min(chart.right + 26, hitRight) - Math.max(chart.left - 26, hitLeft)),
                height: (chart.bottom - chart.top) + 20,
                fill: 'transparent',
                tabindex: 0,
                role: 'button',
                'aria-label': `${point.label}: sales ${sellerPeso(point.value)}`,
                style: 'cursor:crosshair;outline:none;',
            });

            hitbox.addEventListener(
                'pointerenter',
                (event) => scheduleSellerSalesTooltip(point, index, event),
                { signal: sellerDashboardEventSignal, passive: true }
            );

            hitbox.addEventListener(
                'pointermove',
                (event) => scheduleSellerSalesTooltip(point, index, event),
                { signal: sellerDashboardEventSignal, passive: true }
            );

            hitbox.addEventListener(
                'pointerleave',
                hideSellerSalesTooltip,
                { signal: sellerDashboardEventSignal }
            );

            hitbox.addEventListener(
                'focus',
                () => scheduleSellerSalesTooltip(point, index),
                { signal: sellerDashboardEventSignal }
            );

            hitbox.addEventListener(
                'blur',
                hideSellerSalesTooltip,
                { signal: sellerDashboardEventSignal }
            );

            sellerSalesHitboxes.appendChild(hitbox);
        });

        sellerSalesEmptyState?.classList.toggle('hidden', total > 0);

        animateSellerSalesGraph();
    }

    sellerSalesPeriodButtons.forEach((button) => {
        button.addEventListener('click', function () {
            renderSellerSalesPerformance(this.dataset.salesPeriod || 'month');
        }, { signal: sellerDashboardEventSignal });
    });

    renderSellerSalesPerformance('month');

    /*
    |--------------------------------------------------------------------------
    | PRODUCT ANALYTICS HELPER
    |--------------------------------------------------------------------------
    | Dashboard only reads Product Library data for analytics. CRUD UI lives
    | exclusively on seller.products.index.
    */
    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT LIBRARY MODAL — LAZY LOADED
    |--------------------------------------------------------------------------
    |
    | Full catalog data is fetched only after the Seller clicks View All.
    | This removes the largest hidden block of product HTML from the initial
    | dashboard response and greatly reduces Tailwind/DOM processing.
    |
    */
    const allProductsModal = document.getElementById('sellerAllProductsModal');
    const allProductsOpenButtons = [
        document.getElementById('openAllProductsModal'),
        ...Array.from(document.querySelectorAll('[data-open-all-products]'))
    ].filter(Boolean);
    const closeAllProductsButton = document.getElementById('closeAllProductsModal');
    const allProductsSearch = document.getElementById('allProductsSearch');
    const allProductsStatusFilter = document.getElementById('allProductsStatusFilter');
    const allProductsStockFilter = document.getElementById('allProductsStockFilter');
    const clearAllProductFilters = document.getElementById('clearAllProductFilters');
    const allProductsResultCount = document.getElementById('allProductsResultCount');
    const allProductsNoResults = document.getElementById('allProductsNoResults');
    const allProductsGrid = document.getElementById('allProductsGrid');

    const productLibraryUrl = @json(route('seller.products.library'));
    const sellerLockedForLibrary = @json((bool) $sellerLocked);

    let allLibraryCards = [];
    let productLibraryLoaded = false;
    let productLibraryLoading = false;
    let productLibraryPayload = null;
    let productLibraryPromise = null;

    const sellerCategoryPalette = [
        '#d5a12b',
        '#2f4668',
        '#399783',
        '#d59a4a',
        '#7f68ab',
        '#5b83a8'
    ];

    let sellerSelectedCategory = '';
    let sellerCategoryRenderSignature = '';

    function normalizeSellerCategoryProducts(products) {
        return (Array.isArray(products) ? products : [])
            .map(function (product) {
                return {
                    category: String(product?.category || 'Uncategorized').trim() || 'Uncategorized',
                    moderation_status: String(product?.moderation_status || '').toLowerCase(),
                    stock: Number(product?.stock || 0),
                };
            });
    }

    function buildSellerCategoryBreakdown(products) {
        const normalized = normalizeSellerCategoryProducts(products);
        const grouped = new Map();

        normalized.forEach(function (product) {
            grouped.set(
                product.category,
                (grouped.get(product.category) || 0) + 1
            );
        });

        let categories = Array.from(grouped.entries())
            .map(([name, count]) => ({ name, count }))
            .sort((a, b) => b.count - a.count || a.name.localeCompare(b.name));

        /*
        | Keep the card readable: show the five largest categories and group
        | the remainder into Other Categories. No data is discarded.
        */
        if (categories.length > 6) {
            const visible = categories.slice(0, 5);
            const remainder = categories.slice(5).reduce((sum, item) => sum + item.count, 0);
            visible.push({ name: 'Other Categories', count: remainder, grouped: true });
            categories = visible;
        }

        const total = normalized.length;
        const approved = normalized.filter((product) => product.moderation_status === 'approved').length;
        const lowStock = normalized.filter((product) => product.stock > 0 && product.stock <= Number(product.low_stock_threshold ?? 5)).length;
        const uniqueCategories = grouped.size;

        return {
            total,
            approved,
            lowStock,
            uniqueCategories,
            categories: categories.map(function (item, index) {
                return {
                    ...item,
                    percentage: total > 0 ? (item.count / total) * 100 : 0,
                    color: sellerCategoryPalette[index % sellerCategoryPalette.length],
                };
            }),
        };
    }

    function sellerCategoryPercent(value) {
        const number = Number(value || 0);
        return number < 10 && number % 1 !== 0
            ? number.toFixed(1) + '%'
            : Math.round(number) + '%';
    }

    function renderSellerCategoryBreakdown(products) {
        const donut = document.getElementById('sellerCategoryDonut');
        const legend = document.getElementById('sellerCategoryLegend');
        const centerLabel = document.getElementById('sellerCategoryCenterLabel');
        const centerValue = document.getElementById('sellerCategoryCenterValue');
        const centerSub = document.getElementById('sellerCategoryCenterSub');
        const viewLabel = document.getElementById('sellerCategoryViewProductsLabel');

        if (!donut || !legend) return;

        const data = buildSellerCategoryBreakdown(products);
        const nextSignature = JSON.stringify({
            total: data.total,
            categories: data.categories.map((item) => [
                item.name,
                item.count,
                Number(item.percentage || 0).toFixed(4),
            ]),
        });

        if (nextSignature === sellerCategoryRenderSignature) {
            return;
        }

        sellerCategoryRenderSignature = nextSignature;

        if (!data.total || !data.categories.length) {
            donut.style.setProperty('--seller-category-gradient', 'conic-gradient(#e8e3dc 0deg 360deg)');
            legend.innerHTML = `
                <div class="seller-category-ranked-empty">
                    <p class="text-[8.5px] font-medium text-[#766d63]">No active product categories yet.</p>
                </div>
            `;
            sellerSelectedCategory = '';
            if (centerLabel) centerLabel.textContent = 'Total Listings';
            if (centerValue) centerValue.textContent = '0';
            if (centerSub) { centerSub.textContent = ''; centerSub.classList.add('hidden'); }
            if (viewLabel) viewLabel.textContent = 'View All Products';
            return;
        }

        let cursor = 0;
        const segments = data.categories.map(function (item) {
            const start = cursor;
            const end = cursor + item.percentage * 3.6;
            cursor = end;
            return `${item.color} ${start.toFixed(2)}deg ${end.toFixed(2)}deg`;
        });

        donut.style.setProperty(
            '--seller-category-gradient',
            `conic-gradient(${segments.join(', ')})`
        );

        legend.innerHTML = data.categories.map(function (item) {
            return `
                <button
                    type="button"
                    data-seller-category="${escapeHtml(item.name)}"
                    data-seller-category-count="${item.count}"
                    data-seller-category-percent="${item.percentage}"
                    class="seller-category-ranked-row"
                    style="--seller-category-row-color:${item.color};"
                    aria-label="${escapeHtml(item.name)}: ${sellerCategoryPercent(item.percentage)}"
                >
                    <span class="seller-category-ranked-top">
                        <span class="seller-category-ranked-name">
                            <i aria-hidden="true"></i>
                            <span>${escapeHtml(item.name)}</span>
                        </span>
                        <strong>${sellerCategoryPercent(item.percentage)}</strong>
                    </span>
                    <span class="seller-category-ranked-track" aria-hidden="true">
                        <span style="width:${Math.max(2.5, Number(item.percentage || 0))}%"></span>
                    </span>
                </button>
            `;
        }).join('');

        sellerSelectedCategory = '';
        if (centerLabel) centerLabel.textContent = 'Total Listings';
        if (centerValue) centerValue.textContent = data.total.toLocaleString('en-PH');
        if (centerSub) { centerSub.textContent = ''; centerSub.classList.add('hidden'); }
        if (viewLabel) viewLabel.textContent = 'View All Products';

        legend.querySelectorAll('[data-seller-category]').forEach(function (button) {
            button.addEventListener('click', function () {
                legend.querySelectorAll('[data-seller-category]').forEach((item) => item.classList.remove('is-active'));
                this.classList.add('is-active');

                sellerSelectedCategory = String(this.dataset.sellerCategory || '');
                const count = Number(this.dataset.sellerCategoryCount || 0);
                const percent = Number(this.dataset.sellerCategoryPercent || 0);

                if (centerLabel) centerLabel.textContent = sellerSelectedCategory;
                if (centerValue) centerValue.textContent = count.toLocaleString('en-PH');
                if (centerSub) { centerSub.textContent = `${sellerCategoryPercent(percent)} of catalog`; centerSub.classList.remove('hidden'); }
                if (viewLabel) {
                    viewLabel.textContent = sellerSelectedCategory === 'Other Categories'
                        ? 'View Product Library'
                        : `View ${sellerSelectedCategory}`;
                }
            }, { signal: sellerDashboardEventSignal });
        });
    }

    async function fetchProductLibraryData() {
        if (Array.isArray(productLibraryPayload)) {
            return productLibraryPayload;
        }

        if (productLibraryPromise) {
            return productLibraryPromise;
        }

        function applyProductLibraryProducts(products) {
            productLibraryPayload = Array.isArray(products) ? products : [];
            renderSellerCategoryBreakdown(productLibraryPayload);
            return productLibraryPayload;
        }

        function fetchProductLibraryDirectly() {
            return fetch(productLibraryUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                cache: 'default',
                signal: sellerDashboardEventSignal,
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Unable to load the Product Library.');
                    }

                    return response.json();
                })
                .then(function (data) {
                    window.__SARI_CACHE_PRODUCTS_LIBRARY__?.(data);
                    return Array.isArray(data?.products) ? data.products : [];
                });
        }

        const earlyPrefetch = window.__SARI_SELLER_PRODUCT_LIBRARY_PREFETCH__;

        productLibraryPromise = (earlyPrefetch
            ? Promise.resolve(earlyPrefetch).then(function (products) {
                if (Array.isArray(products)) {
                    return products;
                }

                return fetchProductLibraryDirectly();
            })
            : fetchProductLibraryDirectly()
        )
            .then(applyProductLibraryProducts)
            .finally(function () {
                productLibraryPromise = null;
                window.__SARI_SELLER_PRODUCT_LIBRARY_PREFETCH__ = null;
            });

        return productLibraryPromise;
    }

    function libraryStatusClasses(status) {
        const map = {
            approved: 'border-[#d8e9df] bg-[#edf7f1] text-[#4f8065]',
            pending: 'border-[#d8e5f1] bg-[#eef5fc] text-[#537a9f]',
            flagged: 'border-[#f0d4d4] bg-[#fff0f0] text-[#b45b5b]',
            rejected: 'border-[#eed2d2] bg-[#fff0f0] text-[#a85858]',
            removed: 'border-[#e2ddd6] bg-[#f4f2ef] text-[#746d64]'
        };

        return map[String(status || '').toLowerCase()]
            || 'border-[#e2ddd6] bg-[#f4f2ef] text-[#746d64]';
    }

    function productDataAttributes(product) {
        return `
            data-product-id="${escapeHtml(product.id)}"
            data-product-name="${escapeHtml(product.name || '')}"
            data-product-category="${escapeHtml(product.category || '')}"
            data-product-brand="${escapeHtml(product.brand || '')}"
            data-product-specifications="${escapeHtml(JSON.stringify(product.specifications || []))}"
            data-product-variants="${escapeHtml(JSON.stringify(product.variants || []))}"
            data-product-has-variants="${product.has_variants ? '1' : '0'}"
            data-product-sku="${escapeHtml(product.sku || '')}"
            data-product-price="${escapeHtml(product.price ?? 0)}"
            data-product-stock="${escapeHtml(product.stock ?? 0)}"
            data-product-discount="${escapeHtml(product.discount ?? 0)}"
            data-product-free-shipping="${product.free_shipping ? '1' : '0'}"
            data-product-voucher="${escapeHtml(product.voucher_code || '')}"
            data-product-description="${escapeHtml(product.description || '')}"
            data-product-status="${escapeHtml(product.status_label || '')}"
            data-product-image="${escapeHtml(product.image_url || '')}"
        `;
    }

    function renderProductLibrary(products) {
        if (!allProductsGrid) return;

        if (!Array.isArray(products) || products.length === 0) {
            allProductsGrid.innerHTML = '';
            allLibraryCards = [];
            productLibraryLoaded = true;
            filterAllProducts();
            return;
        }

        allProductsGrid.innerHTML = products.map(function (product) {
            const variants = Array.isArray(product.variants) ? product.variants : [];
            const searchText = [
                product.name,
                product.category,
                product.brand,
                product.sku
            ].filter(Boolean).join(' ').toLowerCase();

            const stock = Number(product.stock || 0);
            const status = String(product.moderation_status || '').toLowerCase();
            const statusClasses = libraryStatusClasses(status);
            const disabled = sellerLockedForLibrary ? 'disabled' : '';
            const sharedData = productDataAttributes(product);

            const imageMarkup = product.image_url
                ? `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.name || 'Product')}" class="h-full w-full object-cover" loading="lazy" decoding="async">`
                : `
                    <div class="grid h-full w-full place-items-center text-[#aaa094]">
                        <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                            <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                        </svg>
                    </div>
                `;

            const variantBadge = product.has_variants && variants.length
                ? `<span class="shrink-0 rounded-full bg-[#f6f2fa] px-2 py-1 text-[6px] font-bold text-[#77698a]">${variants.length} var.</span>`
                : '';

            return `
                <article
                    data-library-product
                    data-library-search="${escapeHtml(searchText)}"
                    data-library-status="${escapeHtml(status)}"
                    data-library-stock="${escapeHtml(product.stock_state || '')}"
                    class="overflow-hidden rounded-[15px] border border-[#ebe5dd] bg-white transition hover:border-[#d9c9ad] hover:shadow-[0_10px_25px_rgba(54,42,24,.06)]"
                >
                    <div class="relative h-[145px] overflow-hidden bg-[#f7f5f1]">
                        ${imageMarkup}
                        <div class="absolute left-2 top-2 z-[2] flex items-center gap-1">
                            ${product.on_trend ? '<span class="seller-market-badge seller-market-badge--trend">On Trend</span>' : ''}
                            ${product.mall_badge ? '<span class="seller-market-badge seller-market-badge--mall">Mall</span>' : ''}
                        </div>

                        ${Number(product.discount || 0) > 0
                            ? `<span class="seller-market-badge seller-market-badge--sale absolute right-2 top-2 z-[2]">-${Number(product.discount).toLocaleString('en-PH', {maximumFractionDigits: 2})}%</span>`
                            : ''
                        }

                        <span class="absolute bottom-2 right-2 rounded-full border px-2 py-1 text-[6.5px] font-medium shadow-sm ${statusClasses}">
                            ${escapeHtml(product.status_label || status)}
                        </span>
                    </div>

                    <div class="p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="truncate text-[11px] font-bold text-[#312b25]">${escapeHtml(product.name || '')}</p>
                                <p class="mt-1 truncate text-[8.5px] text-[#958c80]">
                                    ${escapeHtml(product.category || '')}${product.brand ? ' · ' + escapeHtml(product.brand) : ''}
                                </p>
                            </div>
                            ${variantBadge}
                        </div>

                        <div class="mt-3">
                            <div class="flex flex-wrap items-baseline gap-2">
                                <p class="text-[13px] font-medium text-[#efa900]">
                                    ₱${Number(product.sale_price ?? product.price ?? 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                                </p>
                                ${Number(product.discount || 0) > 0
                                    ? `<span class="text-[8px] font-normal text-[#aaa198] line-through">₱${Number(product.price || 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`
                                    : ''
                                }
                            </div>

                            <div class="mt-2 flex min-h-[22px] items-center justify-between gap-2">
                                <div>
                                    ${product.free_shipping
                                        ? `<span class="seller-free-shipping-badge">
                                            <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h11v9H3z"></path><path d="M14 10h4l3 3v3h-7z"></path><circle cx="7" cy="18" r="1.5"></circle><circle cx="18" cy="18" r="1.5"></circle></svg>
                                            Free Shipping
                                        </span>`
                                        : ''
                                    }
                                </div>

                                <p class="text-[7px] font-medium ${stock <= Number(product.low_stock_threshold ?? 5) ? 'text-[#b75c5c]' : 'text-[#56816a]'}">
                                    ${stock <= 0 ? 'Out of stock' : stock + ' in stock'}
                                </p>
                            </div>

                            <div class="mt-2.5 flex items-center justify-between border-t border-[#f1ede7] pt-2.5 text-[7.5px] font-normal text-[#948c82]">
                                <span>
                                    <span class="text-[#ffb800]">★</span>
                                    ${Number(product.rating_count || 0) > 0
                                        ? `${Number(product.rating || 0).toFixed(1)} · ${Number(product.rating_count || 0).toLocaleString()} ratings`
                                        : 'No ratings yet'
                                    }
                                </span>
                                <span>SKU: ${escapeHtml(product.sku || '—')}</span>
                            </div>
                        </div>

                        <div class="mt-3 grid grid-cols-4 gap-2 border-t border-[#f0ebe4] pt-3">
                            <button
                                type="button"
                                data-view-product
                                data-close-library-before-action
                                ${sharedData}
                                title="View product"
                                aria-label="View product"
                                class="grid h-9 w-full place-items-center rounded-[9px] border border-[#e4ddd4] bg-white text-[#8b837a] transition duration-150 hover:border-[#cfc3b2] hover:bg-[#fbfaf8] hover:text-[#37312b]"
                            >
                                <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>
                            </button>

                            <button
                                type="button"
                                data-edit-product
                                data-close-library-before-action
                                ${sharedData}
                                ${disabled}
                                title="Edit product"
                                aria-label="Edit product"
                                class="grid h-9 w-full place-items-center rounded-[9px] border border-[#e4ddd4] bg-white text-[#8b837a] transition duration-150 hover:border-[#cfc3b2] hover:bg-[#fbfaf8] hover:text-[#37312b] disabled:cursor-not-allowed disabled:opacity-40"
                            >
                                <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                </svg>
                            </button>

                            <button
                                type="button"
                                data-product-action="archive"
                                data-close-library-before-action
                                data-product-id="${escapeHtml(product.id)}"
                                data-product-name="${escapeHtml(product.name || '')}"
                                ${disabled}
                                title="Archive product"
                                aria-label="Archive product"
                                class="grid h-9 w-full place-items-center rounded-[9px] border border-[#e4ddd4] bg-white text-[#8b837a] transition duration-150 hover:border-[#d8c7aa] hover:bg-[#fffaf5] hover:text-[#9d711e] disabled:cursor-not-allowed disabled:opacity-40"
                            >
                                <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="4" y="7" width="16" height="13" rx="2"></rect>
                                    <path d="M3 4h18v3H3z"></path>
                                    <path d="M10 11h4"></path>
                                </svg>
                            </button>

                            <button
                                type="button"
                                data-product-action="delete"
                                data-close-library-before-action
                                data-product-id="${escapeHtml(product.id)}"
                                data-product-name="${escapeHtml(product.name || '')}"
                                ${disabled}
                                title="Delete product"
                                aria-label="Delete product"
                                class="grid h-9 w-full place-items-center rounded-[9px] border border-[#e4ddd4] bg-white text-[#8b837a] transition duration-150 hover:border-[#e4c3c3] hover:bg-[#fff8f8] hover:text-[#a85f5f] disabled:cursor-not-allowed disabled:opacity-40"
                            >
                                <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 7h16"></path>
                                    <path d="M9 7V4h6v3"></path>
                                    <path d="M7 7l1 13h8l1-13"></path>
                                    <path d="M10 11v5"></path>
                                    <path d="M14 11v5"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </article>
            `;
        }).join('');

        allLibraryCards = Array.from(
            allProductsGrid.querySelectorAll('[data-library-product]')
        );

        productLibraryLoaded = true;
        filterAllProducts();
    }

    async function ensureProductLibraryLoaded() {
        if (productLibraryLoaded || productLibraryLoading) {
            return;
        }

        productLibraryLoading = true;

        if (allProductsGrid) {
            allProductsGrid.innerHTML = `
                <div class="col-span-full rounded-[16px] border border-dashed border-[#e3dbcf] bg-[#fcfaf7] px-6 py-12 text-center">
                    <div class="mx-auto grid h-9 w-9 place-items-center rounded-[11px] bg-[#eee9e2] text-[#9a9186]">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <path d="M5 7h14l-1 13H6L5 7Z"></path>
                            <path d="M9 7a3 3 0 0 1 6 0"></path>
                        </svg>
                    </div>
                    <p class="mt-3 text-[9px] font-medium text-[#5c5449]">Loading Product Library...</p>
                </div>
            `;
        }

        try {
            const products = await fetchProductLibraryData();
            renderProductLibrary(products);
        } catch (error) {
            if (error?.name === 'AbortError') {
                return;
            }

            if (allProductsGrid) {
                allProductsGrid.innerHTML = `
                    <div class="col-span-full rounded-[16px] border border-[#efd8d8] bg-[#fff8f8] px-6 py-12 text-center">
                        <p class="text-[9px] font-semibold text-[#a55b5b]">Unable to load Product Library</p>
                        <p class="mt-1 text-[7.5px] text-[#987777]">Close this window and try again.</p>
                    </div>
                `;
            }

            console.error(error);
        } finally {
            productLibraryLoading = false;
        }
    }

    function openAllProducts() {
        showSmoothSellerModal(allProductsModal);
        ensureProductLibraryLoaded();

        window.setTimeout(function () {
            allProductsSearch?.focus();
        }, 180);
    }

    function closeAllProducts() {
        hideSmoothSellerModal(allProductsModal);
    }

    function filterAllProducts() {
        const query = (allProductsSearch?.value || '').trim().toLowerCase();
        const status = (allProductsStatusFilter?.value || '').toLowerCase();
        const stock = (allProductsStockFilter?.value || '').toLowerCase();

        let visible = 0;

        allLibraryCards.forEach(function (card) {
            const searchable = (card.dataset.librarySearch || '').toLowerCase();
            const cardStatus = (card.dataset.libraryStatus || '').toLowerCase();
            const cardStock = (card.dataset.libraryStock || '').toLowerCase();

            const matchesQuery = query === '' || searchable.includes(query);
            const matchesStatus = status === '' || cardStatus === status;
            const matchesStock = stock === '' || cardStock === stock;
            const matches = matchesQuery && matchesStatus && matchesStock;

            card.classList.toggle('hidden', !matches);

            if (matches) visible++;
        });

        if (allProductsResultCount) {
            allProductsResultCount.textContent =
                'Showing ' + visible + ' of ' + allLibraryCards.length +
                ' product' + (allLibraryCards.length === 1 ? '' : 's');
        }

        allProductsNoResults?.classList.toggle(
            'hidden',
            !productLibraryLoaded || visible !== 0
        );
    }

    allProductsOpenButtons.forEach(function (button) {
        button.addEventListener('click', openAllProducts);
    });

    closeAllProductsButton?.addEventListener('click', closeAllProducts);

    allProductsModal?.addEventListener('click', function (event) {
        if (event.target === allProductsModal) {
            closeAllProducts();
        }
    });

    allProductsSearch?.addEventListener('input', filterAllProducts);
    allProductsStatusFilter?.addEventListener('change', filterAllProducts);
    allProductsStockFilter?.addEventListener('change', filterAllProducts);

    clearAllProductFilters?.addEventListener('click', function () {
        if (allProductsSearch) allProductsSearch.value = '';
        if (allProductsStatusFilter) allProductsStatusFilter.value = '';
        if (allProductsStockFilter) allProductsStockFilter.value = '';

        filterAllProducts();
        allProductsSearch?.focus();
    });

    const sellerCategoryViewProducts = document.getElementById('sellerCategoryViewProducts');

    const sellerProductsPageUrl = @json(route('seller.products.index'));

    sellerCategoryViewProducts?.addEventListener('click', function () {
        const category = sellerSelectedCategory === 'Other Categories'
            ? ''
            : sellerSelectedCategory;

        const target = category
            ? sellerProductsPageUrl + '?category=' + encodeURIComponent(category)
            : sellerProductsPageUrl;

        window.location.href = target;
    }, { signal: sellerDashboardEventSignal });

    /*
    | Preview rows are already server-rendered, so the card is visible on first
    | paint. Consume the Product Library request that started near the top of
    | the document and silently refresh with the complete active catalog.
    */
    fetchProductLibraryData().catch(function (error) {
        if (error?.name !== 'AbortError') {
            console.debug('SARI category breakdown is using first-paint preview data.');
        }
    });

    document.addEventListener('keydown', function (event) {
        if (
            event.key === 'Escape'
            && allProductsModal
            && !allProductsModal.classList.contains('hidden')
        ) {
            closeAllProducts();
        }
    }, { signal: sellerDashboardEventSignal });


    /*
    |--------------------------------------------------------------------------
    | PRODUCT CRUD OWNERSHIP
    |--------------------------------------------------------------------------
    | View / Edit / Archive / Remove are handled only on Product Management.
    */


    /*
    |--------------------------------------------------------------------------
    | LIVE ORDER ACTIVITY REFRESH
    |--------------------------------------------------------------------------
    */
    let sellerDashboardOrderRefreshTimer = null;
    let sellerDashboardOrderRefreshBusy = false;

    async function refreshSellerDashboardOrderWidgets() {
        if (sellerDashboardOrderRefreshBusy || document.hidden) return;

        sellerDashboardOrderRefreshBusy = true;

        try {
            const response = await fetch(@json(route('seller.dashboard')), {
                method: 'GET',
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: sellerDashboardEventSignal,
            });

            if (!response.ok) return;

            const html = await response.text();
            const parsed = new DOMParser().parseFromString(html, 'text/html');

            [
                '#sellerRecentOrdersCard',
                '#sellerOrderManagementPanel',
                '[data-dashboard-card="orders"]',
            ].forEach(function (selector) {
                const current = document.querySelector(selector);
                const fresh = parsed.querySelector(selector);

                if (!current || !fresh) return;

                current.replaceWith(fresh);
            });
        } catch (error) {
            if (error?.name !== 'AbortError') {
                console.debug('SARI live dashboard order refresh is temporarily unavailable.');
            }
        } finally {
            sellerDashboardOrderRefreshBusy = false;
        }
    }

    window.addEventListener(
        'sari:seller-order-update',
        function () {
            window.clearTimeout(sellerDashboardOrderRefreshTimer);

            sellerDashboardOrderRefreshTimer = window.setTimeout(
                refreshSellerDashboardOrderWidgets,
                180
            );
        },
        { signal: sellerDashboardEventSignal }
    );

    /*
    |--------------------------------------------------------------------------
    | REAL-TIME ADMIN COMPLIANCE ALERTS (LARAVEL REVERB / ECHO)
    |--------------------------------------------------------------------------
    */
    const realtimeModal = document.getElementById('sellerRealtimeAlert');
    const realtimeCard = document.getElementById('sellerRealtimeAlertCard');
    const realtimeAlertIcon = document.getElementById('realtimeAlertIcon');
    let realtimeAutoCloseTimer = null;

    function applyRealtimeAlertTone(payload) {
        const title = String(payload?.title || '').toLowerCase();
        const message = String(payload?.message || '').toLowerCase();
        const combined = title + ' ' + message;

        const approved = combined.includes('approved') || combined.includes('success');
        const dangerous = combined.includes('warning') || combined.includes('rejected') || combined.includes('suspend');

        if (!realtimeCard || !realtimeAlertIcon) return;

        realtimeCard.classList.remove('border-[#ead8d1]', 'border-[#cfe3d7]', 'border-[#cfdded]');
        realtimeAlertIcon.classList.remove(
            'bg-[#fff2ef]', 'text-[#b8685f]',
            'bg-[#f1f8f4]', 'text-[#4d8a69]',
            'bg-[#f2f7fc]', 'text-[#4779a8]'
        );

        if (approved) {
            realtimeCard.classList.add('border-[#cfe3d7]');
            realtimeAlertIcon.classList.add('bg-[#f1f8f4]', 'text-[#4d8a69]');
        } else if (dangerous) {
            realtimeCard.classList.add('border-[#ead8d1]');
            realtimeAlertIcon.classList.add('bg-[#fff2ef]', 'text-[#b8685f]');
        } else {
            realtimeCard.classList.add('border-[#cfdded]');
            realtimeAlertIcon.classList.add('bg-[#f2f7fc]', 'text-[#4779a8]');
        }
    }

    function openRealtimeAlert(payload) {
        document.getElementById('realtimeAlertTitle').textContent = payload.title || 'Compliance Update';
        document.getElementById('realtimeAlertMessage').textContent = payload.message || 'Your seller account has a new compliance update.';

        const productLine = document.getElementById('realtimeAlertProduct');
        if (payload.product_name) {
            productLine.textContent = 'Product: ' + payload.product_name;
            productLine.classList.remove('hidden');
        } else {
            productLine.classList.add('hidden');
        }

        const warningBadge = document.getElementById('realtimeWarningBadge');
        if (payload.warning_number) {
            warningBadge.textContent = 'Warning ' + payload.warning_number + ' / ' + (payload.max_warnings || 3);
            warningBadge.classList.remove('hidden');
        } else {
            warningBadge.classList.add('hidden');
        }

        const suspendedUntil = document.getElementById('realtimeSuspendedUntil');
        const messageAdmin = document.getElementById('realtimeMessageAdmin');

        if (payload.suspended_until) {
            const date = new Date(payload.suspended_until);
            suspendedUntil.textContent = 'Suspended until: ' + date.toLocaleString('en-PH', { dateStyle: 'medium', timeStyle: 'short' });
            suspendedUntil.classList.remove('hidden');
            messageAdmin.classList.remove('hidden');
            messageAdmin.classList.add('flex');
        } else {
            suspendedUntil.classList.add('hidden');
            messageAdmin.classList.add('hidden');
            messageAdmin.classList.remove('flex');
        }

        applyRealtimeAlertTone(payload);

        realtimeModal.classList.remove('hidden');
        realtimeModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(function () {
            realtimeCard.classList.remove('scale-95', 'opacity-0');
            realtimeCard.classList.add('scale-100', 'opacity-100');
        });

        window.clearTimeout(realtimeAutoCloseTimer);

        const isCritical =
            Number(payload?.warning_number || 0) >= 3 ||
            Boolean(payload?.suspended_until);

        if (!isCritical) {
            realtimeAutoCloseTimer = window.setTimeout(function () {
                closeRealtimeAlert();
            }, 5000);
        }
    }

    function closeRealtimeAlert() {
        window.clearTimeout(realtimeAutoCloseTimer);
        realtimeCard.classList.add('scale-95', 'opacity-0');
        realtimeCard.classList.remove('scale-100', 'opacity-100');
        window.setTimeout(function () {
            realtimeModal.classList.add('hidden');
            realtimeModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }, 220);
    }

    document.getElementById('closeRealtimeAlert')?.addEventListener('click', closeRealtimeAlert);

    /*
    |--------------------------------------------------------------------------
    | Compliance events come from the persistent Seller shell.
    |--------------------------------------------------------------------------
    */
    const dashboardComplianceAbort = new AbortController();

    window.addEventListener(
        'sari:seller-compliance-alert',
        function (customEvent) {
            const event = customEvent.detail || {};
            openRealtimeAlert(event);

            document.getElementById('closeRealtimeAlert').onclick = function () {
                closeRealtimeAlert();

                window.setTimeout(function () {
                    if (window.Livewire?.navigate) {
                        window.Livewire.navigate(window.location.href);
                    } else {
                        window.location.reload();
                    }
                }, 260);
            };
        },
        { signal: dashboardComplianceAbort.signal }
    );

    document.addEventListener('livewire:navigating', function () {
        dashboardComplianceAbort.abort();
    }, { once: true });

    };

    if (window.__SARI_SELLER_AFTER_PAINT__) {
        window.__SARI_SELLER_AFTER_PAINT__(run);
    } else {
        window.requestAnimationFrame(() => window.requestAnimationFrame(run));
    }
}, { once: true });
</script>

@endpush