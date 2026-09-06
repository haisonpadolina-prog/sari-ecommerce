@extends('layouts.seller')

@section('title', 'Seller Dashboard — SARI')
@section('page-title', 'Dashboard Overview')

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
@endphp

<style>
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD-ONLY SKELETON
    |--------------------------------------------------------------------------
    | The persistent Seller shell/logo/header stays visible immediately.
    | Only the dashboard content uses this short skeleton presentation.
    */
    /*
    | The real dashboard is rendered underneath from the first paint.
    | Only a viewport-sized skeleton overlay is animated. This avoids the
    | previous full-page repaint, scroll jump, and delayed double fade.
    */
    #sellerDashboardStage {
        position: relative;
        isolation: isolate;
    }

    #sellerDashboardSkeleton {
        position: absolute;
        inset: 0 0 auto 0;
        z-index: 20;
        width: 100%;
        height: min(900px, calc(100vh - 68px));
        min-height: 620px;
        overflow: hidden;
        pointer-events: none;
        opacity: 1;
        visibility: visible;
        background: #fbfaf7;
        contain: paint;
        transition: opacity .22s ease-out, visibility 0s linear 0s;
    }

    #sellerDashboardContent {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: none;
    }

    #sellerDashboardStage.seller-dashboard-ready #sellerDashboardSkeleton {
        opacity: 0;
        visibility: hidden;
        transition: opacity .22s ease-out, visibility 0s linear .22s;
    }

    /*
    | CLEAN PULSE SKELETON
    | No shimmer, shine, sweep, or glossy overlay.
    | The placeholders simply breathe using opacity.
    */
    .seller-skeleton-block {
        animation: sellerSkeletonPulse 1.05s ease-in-out infinite;
        will-change: opacity;
    }

    /* Neutral light placeholders. Existing dark bg utilities stay dark. */
    .seller-skeleton-block:not([class*="bg-["]) {
        background: #e9e5de;
    }

    /* Completely remove the old sweeping shine layer. */
    #sellerDashboardSkeleton::after {
        content: none !important;
        display: none !important;
    }

    @keyframes sellerSkeletonPulse {
        0%, 100% { opacity: .58; }
        50% { opacity: 1; }
    }

    @media (max-width: 699px) {
        #sellerDashboardSkeleton {
            height: min(820px, calc(100vh - 56px));
            min-height: 560px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-skeleton-block {
            animation: none !important;
            opacity: .82;
        }

        #sellerDashboardSkeleton {
            transition: none !important;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SMOOTH MODAL MOTION
    |--------------------------------------------------------------------------
    | Applies only to existing UI modals. Actions/forms remain unchanged.
    */
    #sellerAddProductModal,
    #sellerAllProductsModal,
    #sellerViewProductModal,
    #sellerEditProductModal,
    #sellerProductActionModal {
        opacity: 0;
        transition:
            opacity .22s ease,
            backdrop-filter .22s ease;
    }

    #sellerAddProductModal > :first-child,
    #sellerAllProductsModal > :first-child,
    #sellerViewProductModal > :first-child,
    #sellerEditProductModal > :first-child,
    #sellerProductActionModal > :first-child {
        opacity: 0;
        transform: translateY(10px) scale(.988);
        transition:
            opacity .28s cubic-bezier(.22, 1, .36, 1),
            transform .28s cubic-bezier(.22, 1, .36, 1);
        will-change: opacity, transform;
    }

    #sellerAddProductModal.seller-modal-visible,
    #sellerAllProductsModal.seller-modal-visible,
    #sellerViewProductModal.seller-modal-visible,
    #sellerEditProductModal.seller-modal-visible,
    #sellerProductActionModal.seller-modal-visible {
        opacity: 1;
    }

    #sellerAddProductModal.seller-modal-visible > :first-child,
    #sellerAllProductsModal.seller-modal-visible > :first-child,
    #sellerViewProductModal.seller-modal-visible > :first-child,
    #sellerEditProductModal.seller-modal-visible > :first-child,
    #sellerProductActionModal.seller-modal-visible > :first-child {
        opacity: 1;
        transform: translateY(0) scale(1);
    }



    /*
    |--------------------------------------------------------------------------
    | SELLER GREETING HERO
    |--------------------------------------------------------------------------
    */
    .seller-greeting-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid #2f2a23;
        background:
            radial-gradient(circle at 100% 50%, rgba(214,149,14,.10) 0, rgba(214,149,14,.05) 18%, rgba(214,149,14,0) 34%),
            linear-gradient(135deg, #1f1e1b 0%, #22211e 50%, #1a1917 100%);
        box-shadow: 0 8px 22px rgba(20, 17, 13, .08);
    }

    .seller-greeting-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(255,255,255,.04), rgba(255,255,255,0) 32%);
        pointer-events: none;
    }

    .seller-greeting-rings {
        position: absolute;
        right: -24px;
        top: 50%;
        transform: translateY(-50%);
        width: 190px;
        height: 190px;
        border-radius: 9999px;
        border: 1px solid rgba(218, 159, 33, .22);
        opacity: .95;
        pointer-events: none;
    }

    .seller-greeting-rings::before,
    .seller-greeting-rings::after {
        content: "";
        position: absolute;
        inset: 18px;
        border-radius: inherit;
        border: 1px solid rgba(218, 159, 33, .16);
    }

    .seller-greeting-rings::after {
        inset: 36px;
        border-color: rgba(218, 159, 33, .11);
    }

    .seller-greeting-divider {
        width: 1px;
        align-self: stretch;
        background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,.14), rgba(255,255,255,0));
    }

    .seller-greeting-chip {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border: 1px solid rgba(255,255,255,.12);
        background: rgba(255,255,255,.055);
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
        backdrop-filter: blur(4px);
    }

    .seller-greeting-icon-shell {
        background: linear-gradient(180deg, rgba(236, 173, 38, .18), rgba(236, 173, 38, .06));
        border: 1px solid rgba(230, 173, 59, .18);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.06), 0 4px 10px rgba(0,0,0,.08);
    }

    .seller-greeting-stat {
        min-width: 122px;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
        padding-top: 6px !important;
        padding-bottom: 6px !important;
    }

    /*
    | Time / Date / Weather now live directly on the greeting hero.
    | Only a very subtle separator remains so the information is readable
    | without looking like three extra cards inside the main card.
    */
    .seller-greeting-stat + .seller-greeting-stat {
        border-left: 1px solid rgba(255,255,255,.10) !important;
    }

    @media (max-width: 639px) {
        .seller-greeting-stat + .seller-greeting-stat {
            border-left: 0 !important;
            border-top: 1px solid rgba(255,255,255,.08) !important;
            padding-top: 12px !important;
        }
    }

    .seller-greeting-stat > p {
        color: rgba(255,255,255,.50) !important;
    }

    .seller-greeting-stat > div {
        color: #ffffff !important;
    }

    .seller-greeting-stat [id^="sellerCurrent"],
    .seller-greeting-stat #sellerWeatherTemp {
        color: #ffffff !important;
    }

    .seller-greeting-stat #sellerTimeContext,
    .seller-greeting-stat #sellerCurrentDate,
    .seller-greeting-stat #sellerWeatherCondition {
        color: rgba(255,255,255,.58) !important;
    }

    .seller-greeting-stat > div > span:not(.seller-greeting-weather-pulse) {
        border-color: rgba(214,143,8,.34) !important;
        background: rgba(212,143,8,.08) !important;
        color: #e1a21f !important;
        box-shadow: 0 2px 7px rgba(0,0,0,.08);
    }

    .seller-greeting-stat .seller-greeting-weather-pulse {
        border-color: rgba(86,168,245,.28) !important;
        background: rgba(25,48,69,.55) !important;
        color: #63aff5 !important;
        box-shadow: 0 2px 7px rgba(0,0,0,.08);
    }

    .seller-greeting-weather-pulse {
        position: relative;
    }

    .seller-greeting-weather-pulse::after {
        content: "";
        position: absolute;
        inset: -3px;
        border-radius: 9999px;
        border: 1px solid rgba(59, 130, 246, .28);
        opacity: .55;
    }

    @media (max-width: 1279px) {
        .seller-greeting-divider {
            display: none;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | YOUR PRODUCTS — CLEAN MARKETPLACE CARD SYSTEM
    |--------------------------------------------------------------------------
    | Poppins is already part of the Seller compiled stylesheet. This section
    | keeps the product cards light while using SARI yellow only as an accent.
    */
    #sellerProductsPanel .seller-responsive-product-card,
    #sellerProductsPanel .seller-responsive-add-card,
    #sellerAllProductsModal [data-library-product] {
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        font-weight: 400;
    }

    #sellerProductsPanel .seller-responsive-product-card {
        border-color: #e8e2da;
        box-shadow: 0 5px 16px rgba(39, 31, 22, .025);
    }

    #sellerProductsPanel .seller-responsive-product-card:hover {
        border-color: #d8ccb9;
        box-shadow: 0 14px 30px rgba(43, 33, 21, .055);
    }

    .seller-market-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 24px;
        border-radius: 999px;
        padding: 0 10px;
        font-size: 8.6px;
        line-height: 1;
        font-weight: 600;
        letter-spacing: .01em;
        white-space: nowrap;
        box-shadow: 0 3px 10px rgba(30, 24, 18, .08);
        backdrop-filter: blur(6px);
    }

    .seller-market-badge--trend {
        border: 1px solid #f0d36d;
        background: rgba(255, 249, 223, .98);
        color: #b27700;
    }

    .seller-market-badge--mall {
        gap: 6px;
        min-height: 29px;
        border: 1px solid rgba(255,255,255,.18);
        background: rgba(41, 35, 30, .96);
        color: #fffdf7;
        padding-inline: 12px;
        font-size: 10.2px;
        font-weight: 600;
    }

    .seller-market-badge--mall::before {
        content: "";
        width: 5px;
        height: 5px;
        flex: 0 0 5px;
        border-radius: 999px;
        background: #f1b51d;
        box-shadow: 0 0 0 2px rgba(241,181,29,.14);
    }

    .seller-market-badge--sale {
        min-height: 29px;
        border: 1px solid #efc84a;
        border-radius: 0 11px 0 15px;
        background: #ffd43f;
        padding: 0 12px;
        color: #5a4200;
        font-size: 10.2px;
        font-weight: 650;
        box-shadow: 0 4px 10px rgba(221, 165, 0, .14);
    }

    .seller-free-shipping-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-height: 23px;
        border: 1px solid #b9dfc8;
        border-radius: 999px;
        background: #eef9f2;
        padding: 0 8px;
        color: #2f7a4d;
        font-size: 8px;
        font-weight: 500;
        line-height: 1;
    }

    .seller-free-shipping-badge svg {
        color: #2f8a58;
        stroke-width: 1.9;
    }

    .seller-product-sale-price {
        color: #efa900 !important;
        font-weight: 500 !important;
        letter-spacing: -.025em;
    }

    .seller-product-original-price {
        color: #a8a097;
        font-size: 9px;
        font-weight: 400;
        text-decoration: line-through;
        text-decoration-thickness: 1px;
    }

    .seller-product-rating-row {
        color: #8f877e;
        font-size: 8.5px;
        font-weight: 400;
    }

    .seller-product-rating-row .seller-rating-star {
        color: #ffb800;
    }

    .seller-promo-toggle {
        position: relative;
        display: inline-flex;
        width: 42px;
        height: 23px;
        flex-shrink: 0;
        cursor: pointer;
        border-radius: 999px;
        background: #e7e3dc;
        transition: background .18s ease;
    }

    .seller-promo-toggle::after {
        content: "";
        position: absolute;
        left: 3px;
        top: 3px;
        width: 17px;
        height: 17px;
        border-radius: 999px;
        background: #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,.12);
        transition: transform .18s ease;
    }

    input:checked + .seller-promo-toggle {
        background: #f2b400;
    }

    input:checked + .seller-promo-toggle::after {
        transform: translateX(19px);
    }

    /*
    |--------------------------------------------------------------------------
    | CATALOG CATEGORY BREAKDOWN
    |--------------------------------------------------------------------------
    | Premium white / SARI-gold analytics card. The donut uses the Seller's
    | actual active Product Library data and contains no glossy/shiny effects.
    */
    .seller-category-card {
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
    }

    .seller-category-donut {
        --seller-category-gradient: conic-gradient(#e6e0d7 0deg 360deg);
        position: relative;
        width: min(100%, 252px);
        aspect-ratio: 1;
        border-radius: 9999px;
        background: var(--seller-category-gradient);
        box-shadow:
            inset 0 0 0 1px rgba(61, 50, 37, .06),
            0 10px 28px rgba(38, 29, 19, .055);
    }

    .seller-category-donut::before {
        content: "";
        position: absolute;
        inset: 27%;
        border-radius: inherit;
        border: 1px solid #eee7dd;
        background: #ffffff;
        box-shadow: 0 5px 18px rgba(45, 34, 21, .04);
    }

    .seller-category-donut-center {
        position: absolute;
        inset: 30%;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        pointer-events: none;
    }

    .seller-category-legend-button {
        transition:
            border-color .16s ease,
            background-color .16s ease,
            box-shadow .16s ease,
            transform .16s ease;
    }

    .seller-category-legend-button:hover,
    .seller-category-legend-button.is-active {
        transform: translateY(-1px);
        border-color: #d7c49c;
        background: #fffaf1;
        box-shadow: 0 6px 16px rgba(44, 34, 21, .05);
    }

    .seller-category-legend-button.is-active {
        color: #805a17;
    }

    .seller-category-stat-row + .seller-category-stat-row {
        border-top: 1px solid #eee8df;
    }

    @media (max-width: 639px) {
        .seller-category-donut {
            width: min(78vw, 220px);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RESPONSIVE PRODUCTS / ORDERS / INVENTORY WORKSPACE
    |--------------------------------------------------------------------------
    | Keeps this dashboard area readable even on wide screens or browser
    | zoom-out. Fluid clamp() sizes grow with the viewport but stay bounded.
    */
    #sellerCommerceWorkspace {
        --seller-readable-xs: clamp(10.5px, .42vw + 5px, 12.5px);
        --seller-readable-sm: clamp(11.5px, .48vw + 5px, 13.5px);
        --seller-readable-md: clamp(13px, .58vw + 5px, 15.5px);
        --seller-readable-lg: clamp(16px, .72vw + 6px, 20px);
        --seller-readable-price: clamp(17px, .78vw + 6px, 21px);
    }

    #sellerCommerceWorkspace .seller-responsive-product-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: clamp(14px, 1vw, 20px);
        align-items: stretch;
    }

    @media (min-width: 640px) {
        #sellerCommerceWorkspace .seller-responsive-product-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1024px) {
        #sellerCommerceWorkspace .seller-responsive-product-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        #sellerCommerceWorkspace .seller-responsive-product-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    #sellerCommerceWorkspace .seller-responsive-product-card,
    #sellerCommerceWorkspace .seller-responsive-add-card {
        min-width: 0;
        height: 100%;
    }

    #sellerCommerceWorkspace .seller-responsive-product-image {
        height: clamp(170px, 13vw, 230px) !important;
    }

    #sellerCommerceWorkspace .seller-product-card-title {
        font-size: var(--seller-readable-md) !important;
        line-height: 1.35 !important;
    }

    #sellerCommerceWorkspace .seller-product-card-meta,
    #sellerCommerceWorkspace .seller-product-card-subtext,
    #sellerCommerceWorkspace .seller-product-card-stock,
    #sellerCommerceWorkspace .seller-product-card-badge,
    #sellerCommerceWorkspace .seller-product-status-label {
        font-size: var(--seller-readable-xs) !important;
        line-height: 1.45 !important;
    }

    #sellerCommerceWorkspace .seller-product-card-price {
        font-size: var(--seller-readable-price) !important;
        line-height: 1.15 !important;
    }

    #sellerCommerceWorkspace .seller-responsive-product-card [data-view-product],
    #sellerCommerceWorkspace .seller-responsive-product-card [data-edit-product],
    #sellerCommerceWorkspace .seller-responsive-product-card [data-product-action] {
        min-height: 42px;
    }

    #sellerCommerceWorkspace .seller-responsive-add-card {
        min-height: clamp(330px, 23vw, 390px) !important;
    }

    #sellerCommerceWorkspace .seller-responsive-add-card > span {
        width: clamp(56px, 4vw, 68px) !important;
        height: clamp(56px, 4vw, 68px) !important;
    }

    #sellerCommerceWorkspace .seller-responsive-add-card > p:first-of-type {
        font-size: var(--seller-readable-md) !important;
    }

    #sellerCommerceWorkspace .seller-responsive-add-card > p:last-of-type {
        font-size: var(--seller-readable-sm) !important;
        max-width: 210px !important;
        line-height: 1.6 !important;
    }

    #sellerProductsPanel > div:first-child h3,
    #sellerCommerceSideColumn .seller-panel-title {
        font-size: var(--seller-readable-lg) !important;
        line-height: 1.25 !important;
    }

    #sellerProductsPanel > div:first-child p,
    #sellerProductsPanel > div:first-child span,
    #sellerProductsPanel > div:first-child button,
    #sellerProductsPanel .seller-products-footer-text,
    #sellerProductsPanel .seller-products-footer-button {
        font-size: var(--seller-readable-sm) !important;
    }

    #sellerProductsPanel > div:first-child button,
    #sellerProductsPanel .seller-products-footer-button {
        min-height: 42px;
    }

    /*
    |--------------------------------------------------------------------------
    | ANALYTICS — COMPACT TWO-CARD DESKTOP ROW
    |--------------------------------------------------------------------------
    */
    #sellerAnalyticsRow {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) !important;
        gap: 12px !important;
        align-items: stretch !important;
    }

    @media (min-width: 1180px) {
        #sellerAnalyticsRow {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    #sellerCategoryBreakdownCard,
    #sellerSalesPerformanceCard {
        min-width: 0 !important;
        border-radius: 16px !important;
        box-shadow: 0 6px 18px rgba(33, 24, 14, .03) !important;
    }

    #sellerCategoryBreakdownCard > div:first-child {
        padding: 11px 13px !important;
    }

    #sellerCategoryBreakdownCard > div:first-child > div:first-child {
        gap: 9px !important;
    }

    #sellerCategoryBreakdownCard > div:first-child > div:first-child > span {
        width: 34px !important;
        height: 34px !important;
        border-radius: 10px !important;
    }

    #sellerCategoryBreakdownCard > div:first-child h3,
    #sellerSalesPerformanceCard h3 {
        font-size: clamp(16px, .34vw + 11px, 18px) !important;
        line-height: 1.25 !important;
    }

    #sellerCategoryBreakdownCard > div:first-child p,
    #sellerSalesPeriodLabel {
        margin-top: 4px !important;
        font-size: clamp(10px, .18vw + 8px, 11px) !important;
        line-height: 1.5 !important;
    }

    #sellerCategoryBreakdownCard > div:first-child > span {
        font-size: clamp(9.5px, .15vw + 8px, 10.5px) !important;
        padding: 7px 12px !important;
    }

    #sellerCategoryBreakdownCard > div:nth-child(2) {
        padding: 11px 13px !important;
        gap: 11px !important;
        grid-template-columns: minmax(0, 1fr) 145px !important;
    }

    .seller-category-donut {
        width: min(100%, 174px) !important;
        box-shadow: inset 0 0 0 1px rgba(61,50,37,.05), 0 6px 16px rgba(38,29,19,.04) !important;
    }

    .seller-category-donut::before {
        inset: 28% !important;
    }

    .seller-category-donut-center {
        inset: 31% !important;
    }

    #sellerCategoryCenterLabel,
    #sellerCategoryCenterSub {
        font-size: clamp(8.3px, .12vw + 7px, 9px) !important;
        line-height: 1.35 !important;
    }

    #sellerCategoryCenterValue {
        margin-top: 5px !important;
        font-size: clamp(22px, .35vw + 18px, 24px) !important;
    }

    #sellerCategoryLegend {
        margin-top: 9px !important;
        gap: 5px !important;
    }

    #sellerCategoryLegend .seller-category-legend-button {
        min-height: 31px !important;
        padding: 6px 8px !important;
        border-radius: 8px !important;
        font-size: clamp(8.5px, .14vw + 7px, 9.5px) !important;
    }

    #sellerCategoryBreakdownCard .seller-category-stat-row {
        padding: 8px 10px !important;
    }

    #sellerCategoryBreakdownCard .seller-category-stat-row > div {
        gap: 7px !important;
    }

    #sellerCategoryBreakdownCard .seller-category-stat-row > div > span {
        width: 29px !important;
        height: 29px !important;
        border-radius: 8px !important;
    }

    #sellerCategoryBreakdownCard .seller-category-stat-row p {
        font-size: clamp(8.6px, .13vw + 7px, 9.4px) !important;
        line-height: 1.35 !important;
    }

    #sellerCategoryBreakdownCard .seller-category-stat-row strong {
        font-size: clamp(11.8px, .18vw + 9.5px, 13px) !important;
        line-height: 1.2 !important;
    }

    #sellerCategoryTopPercent {
        font-size: clamp(9.8px, .15vw + 8px, 10.8px) !important;
    }

    #sellerCategoryBreakdownCard > div:last-child {
        padding: 9px 12px !important;
    }

    #sellerCategoryBreakdownCard > div:last-child > p {
        font-size: clamp(8.8px, .14vw + 7px, 9.6px) !important;
        line-height: 1.45 !important;
    }

    #sellerCategoryViewProducts {
        height: 34px !important;
        padding-inline: 12px !important;
        border-radius: 8px !important;
        font-size: clamp(9px, .15vw + 7.5px, 10px) !important;
    }

    #sellerSalesPerformanceCard {
        padding: 13px !important;
    }

    #sellerSalesPerformanceCard > div:first-child {
        gap: 8px !important;
    }

    .seller-sales-period-button {
        padding: 7px 10px !important;
        border-radius: 7px !important;
        font-size: clamp(8.8px, .14vw + 7.3px, 9.8px) !important;
    }

    #sellerSalesPerformanceCard > div:nth-child(2) {
        margin-top: 9px !important;
        border-radius: 9px !important;
    }

    #sellerSalesPerformanceCard > div:nth-child(2) > div {
        padding: 7px 9px !important;
    }

    #sellerSalesPerformanceCard > div:nth-child(2) p:first-child {
        font-size: clamp(8.2px, .12vw + 7px, 9px) !important;
        line-height: 1.3 !important;
    }

    #sellerSalesPerformanceCard > div:nth-child(2) p:last-child {
        margin-top: 4px !important;
        font-size: clamp(13.2px, .22vw + 10.5px, 14.5px) !important;
        line-height: 1.2 !important;
    }

    #sellerSalesPerformanceCard > div:nth-child(3) {
        margin-top: 9px !important;
        padding: 5px 7px !important;
        border-radius: 10px !important;
    }

    #sellerSalesChart {
        height: 158px !important;
    }

    #sellerSalesEmptyState p:first-child {
        font-size: clamp(9px, .14vw + 7.4px, 10px) !important;
    }

    #sellerSalesEmptyState p:last-child {
        font-size: clamp(8.2px, .12vw + 7px, 9px) !important;
        line-height: 1.45 !important;
    }

    #sellerSalesPerformanceCard > div:last-child {
        margin-top: 7px !important;
    }

    #sellerSalesBestPeriod,
    #sellerSalesPerformanceCard > div:last-child > p:last-child {
        font-size: clamp(8.3px, .12vw + 7px, 9px) !important;
        line-height: 1.4 !important;
    }

    /*
    |--------------------------------------------------------------------------
    | FINANCIAL + ORDERS + INVENTORY — TRUE COMPACT SINGLE ROW
    |--------------------------------------------------------------------------
    */
    #sellerCommerceSideColumn {
        min-width: 0 !important;
        width: 100% !important;
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) !important;
        gap: 12px !important;
        align-items: stretch !important;
    }

    @media (min-width: 760px) and (max-width: 1179px) {
        #sellerCommerceSideColumn {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        #sellerCommerceSideColumn > .seller-side-panel:last-child {
            grid-column: 1 / -1 !important;
        }
    }

    @media (min-width: 1180px) {
        #sellerCommerceSideColumn {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }

        #sellerCommerceSideColumn > .seller-side-panel:last-child {
            grid-column: auto !important;
        }
    }

    #sellerCommerceSideColumn > .seller-side-panel {
        grid-column: auto !important;
        grid-row: auto !important;
        width: auto !important;
        max-width: none !important;
        min-width: 0 !important;
        min-height: 236px !important;
        margin: 0 !important;
        padding: 12px !important;
        border-radius: 14px !important;
        box-shadow: 0 5px 15px rgba(33,24,14,.028) !important;
    }

    #sellerCommerceSideColumn .seller-panel-title {
        font-size: clamp(14.5px, .26vw + 11px, 16px) !important;
        line-height: 1.2 !important;
        font-weight: 700 !important;
    }

    #sellerCommerceSideColumn .seller-side-panel > div:first-child p {
        margin-top: 3px !important;
        font-size: clamp(9px, .14vw + 7.5px, 10px) !important;
        line-height: 1.4 !important;
    }

    #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) {
        margin-top: 8px !important;
        display: grid !important;
        gap: 5px !important;
    }

    #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div {
        padding: 6px 7px !important;
        border-radius: 9px !important;
    }

    #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div > div {
        gap: 7px !important;
    }

    #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div > div > div:first-child {
        width: 27px !important;
        height: 27px !important;
        border-radius: 8px !important;
    }

    #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div > div > div:first-child span {
        font-size: 12px !important;
    }

    #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) p:first-child {
        font-size: clamp(8.2px, .12vw + 7px, 9px) !important;
        line-height: 1.3 !important;
    }

    #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) p:last-child {
        margin-top: 2px !important;
        font-size: clamp(12.8px, .2vw + 10.5px, 14px) !important;
        line-height: 1.15 !important;
    }

    #sellerCommerceSideColumn .seller-order-panel > div:nth-child(2) {
        margin-top: 8px !important;
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 5px !important;
    }

    #sellerCommerceSideColumn .seller-order-row {
        min-height: 37px !important;
        padding: 6px 8px !important;
        border-radius: 8px !important;
    }

    #sellerCommerceSideColumn .seller-order-row span:first-child {
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        font-size: clamp(9px, .14vw + 7.5px, 10px) !important;
        line-height: 1.2 !important;
    }

    #sellerCommerceSideColumn .seller-order-row span:last-child {
        min-width: 22px !important;
        min-height: 22px !important;
        height: 22px !important;
        border-radius: 6px !important;
        padding-inline: 6px !important;
        font-size: clamp(8px, .1vw + 7px, 8.8px) !important;
    }

    #sellerCommerceSideColumn .seller-inventory-panel > div:nth-child(2) {
        margin-top: 8px !important;
        display: grid !important;
        gap: 5px !important;
    }

    #sellerCommerceSideColumn .seller-inventory-row {
        min-height: 42px !important;
        padding: 7px 8px !important;
        border-radius: 8px !important;
    }

    #sellerCommerceSideColumn .seller-inventory-row p:first-child {
        font-size: clamp(9px, .14vw + 7.5px, 10px) !important;
        line-height: 1.25 !important;
    }

    #sellerCommerceSideColumn .seller-inventory-row p + p {
        margin-top: 2px !important;
        font-size: clamp(8px, .11vw + 7px, 8.8px) !important;
        line-height: 1.25 !important;
    }

    #sellerCommerceSideColumn .seller-inventory-count {
        font-size: clamp(8px, .1vw + 7px, 8.8px) !important;
        line-height: 1.15 !important;
    }

    #sellerCommerceSideColumn .seller-inventory-panel > div:first-child > .seller-inventory-count {
        padding: 5px 8px !important;
        font-size: clamp(8px, .1vw + 7px, 8.8px) !important;
    }

    #sellerCommerceSideColumn .seller-order-view-all,
    #sellerCommerceSideColumn .seller-inventory-button,
    #sellerCommerceSideColumn .seller-financial-panel a[href*="reports"] {
        min-height: 35px !important;
        height: 35px !important;
        border-radius: 8px !important;
        font-size: clamp(9px, .14vw + 7.5px, 10px) !important;
        line-height: 1.1 !important;
    }

    #sellerCommerceSideColumn .seller-side-panel > .mt-auto {
        padding-top: 9px !important;
    }

    @media (max-width: 520px) {
        #sellerCommerceSideColumn .seller-order-panel > div:nth-child(2) {
            grid-template-columns: minmax(0, 1fr) !important;
        }
    }

    @media (min-width: 1280px) {
        #sellerCommerceWorkspace {
            grid-template-columns: minmax(0, 3fr) minmax(310px, 1fr) !important;
        }
    }


    @media (prefers-reduced-motion: reduce) {
        #sellerAddProductModal,
        #sellerAllProductsModal,
        #sellerViewProductModal,
        #sellerEditProductModal,
        #sellerProductActionModal,
        #sellerAddProductModal > :first-child,
        #sellerAllProductsModal > :first-child,
        #sellerViewProductModal > :first-child,
        #sellerEditProductModal > :first-child,
        #sellerProductActionModal > :first-child {
            transition: none !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | READABILITY BOOST V2 — LARGER DASHBOARD TEXT
    |--------------------------------------------------------------------------
    | Extra sizing for users who browse the Seller dashboard zoomed out.
    | Layout, IDs, routes, backend data and JavaScript behavior are unchanged.
    */

    #sellerCategoryBreakdownCard > div:first-child h3,
    #sellerSalesPerformanceCard h3 {
        font-size: clamp(17px, 1.05vw, 19px) !important;
        line-height: 1.3 !important;
    }

    #sellerCategoryBreakdownCard > div:first-child p,
    #sellerSalesPeriodLabel {
        font-size: clamp(10.5px, .62vw, 12px) !important;
        line-height: 1.55 !important;
    }

    #sellerCategoryBreakdownCard > div:first-child > span {
        font-size: clamp(10px, .56vw, 11px) !important;
    }

    #sellerCategoryCenterLabel,
    #sellerCategoryCenterSub {
        font-size: clamp(9px, .5vw, 10px) !important;
        line-height: 1.4 !important;
    }

    #sellerCategoryCenterValue {
        font-size: clamp(24px, 1.45vw, 28px) !important;
    }

    #sellerCategoryLegend .seller-category-legend-button {
        min-height: 34px !important;
        padding: 7px 9px !important;
        font-size: clamp(9.5px, .52vw, 10.5px) !important;
        line-height: 1.25 !important;
    }

    #sellerCategoryBreakdownCard .seller-category-stat-row {
        padding: 9px 11px !important;
    }

    #sellerCategoryBreakdownCard .seller-category-stat-row p {
        font-size: clamp(9.5px, .53vw, 10.5px) !important;
        line-height: 1.35 !important;
    }

    #sellerCategoryBreakdownCard .seller-category-stat-row strong,
    #sellerCategoryTopPercent {
        font-size: clamp(12.5px, .68vw, 14px) !important;
        line-height: 1.25 !important;
    }

    #sellerCategoryBreakdownCard > div:last-child p {
        font-size: clamp(9.5px, .53vw, 10.5px) !important;
        line-height: 1.5 !important;
    }

    #sellerCategoryViewProducts {
        min-height: 37px !important;
        height: 37px !important;
        font-size: clamp(10px, .55vw, 11px) !important;
        padding-inline: 13px !important;
    }

    .seller-sales-period-button {
        min-height: 34px !important;
        padding: 7px 11px !important;
        font-size: clamp(9.5px, .52vw, 10.5px) !important;
    }

    #sellerSalesPerformanceCard > div:nth-child(2) p:first-child {
        font-size: clamp(9px, .5vw, 10px) !important;
        line-height: 1.35 !important;
    }

    #sellerSalesPerformanceCard > div:nth-child(2) p:last-child {
        font-size: clamp(14px, .8vw, 16px) !important;
        line-height: 1.2 !important;
    }

    #sellerSalesEmptyState p:first-child {
        font-size: clamp(10px, .56vw, 11px) !important;
    }

    #sellerSalesEmptyState p:last-child {
        font-size: clamp(9px, .5vw, 10px) !important;
        line-height: 1.5 !important;
    }

    #sellerSalesBestPeriod,
    #sellerSalesPerformanceCard > div:last-child > p:last-child {
        font-size: clamp(9px, .5vw, 10px) !important;
        line-height: 1.45 !important;
    }

    #sellerCommerceSideColumn > .seller-side-panel {
        min-height: 252px !important;
        padding: 14px !important;
    }

    #sellerCommerceSideColumn .seller-panel-title {
        font-size: clamp(16px, .9vw, 18px) !important;
        line-height: 1.25 !important;
    }

    #sellerCommerceSideColumn .seller-side-panel > div:first-child p {
        font-size: clamp(10px, .56vw, 11px) !important;
        line-height: 1.45 !important;
    }

    #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) {
        gap: 7px !important;
    }

    #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div {
        padding: 8px 9px !important;
    }

    #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) p:first-child {
        font-size: clamp(9px, .5vw, 10px) !important;
        line-height: 1.3 !important;
    }

    #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) p:last-child {
        font-size: clamp(14px, .8vw, 16px) !important;
        line-height: 1.2 !important;
    }

    #sellerCommerceSideColumn .seller-order-row {
        min-height: 42px !important;
        padding: 8px 9px !important;
    }

    #sellerCommerceSideColumn .seller-order-row span:first-child {
        font-size: clamp(10px, .56vw, 11px) !important;
        line-height: 1.25 !important;
    }

    #sellerCommerceSideColumn .seller-order-row span:last-child {
        min-width: 25px !important;
        min-height: 25px !important;
        height: 25px !important;
        font-size: clamp(9px, .5vw, 10px) !important;
    }

    #sellerCommerceSideColumn .seller-inventory-row {
        min-height: 48px !important;
        padding: 8px 9px !important;
    }

    #sellerCommerceSideColumn .seller-inventory-row p:first-child {
        font-size: clamp(10.5px, .58vw, 11.5px) !important;
        line-height: 1.3 !important;
    }

    #sellerCommerceSideColumn .seller-inventory-row p + p {
        font-size: clamp(9px, .5vw, 10px) !important;
        line-height: 1.3 !important;
    }

    #sellerCommerceSideColumn .seller-inventory-count {
        font-size: clamp(9px, .5vw, 10px) !important;
        line-height: 1.2 !important;
    }

    #sellerCommerceSideColumn .seller-inventory-panel > div:first-child > .seller-inventory-count {
        padding: 5px 9px !important;
        font-size: clamp(9.5px, .52vw, 10.5px) !important;
    }

    #sellerCommerceSideColumn .seller-order-view-all,
    #sellerCommerceSideColumn .seller-inventory-button,
    #sellerCommerceSideColumn .seller-financial-panel a[href*="reports"] {
        min-height: 39px !important;
        height: 39px !important;
        font-size: clamp(10px, .56vw, 11px) !important;
        line-height: 1.1 !important;
    }

    @media (max-width: 639px) {
        #sellerCategoryBreakdownCard > div:first-child h3,
        #sellerSalesPerformanceCard h3 {
            font-size: 16px !important;
        }

        #sellerCommerceSideColumn .seller-panel-title {
            font-size: 15px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SALES PERFORMANCE — FINAL READABILITY BOOST
    |--------------------------------------------------------------------------
    | Only this analytics card is enlarged further. The card dimensions and
    | chart behavior remain unchanged so the dashboard stays clean and compact.
    */
    #sellerSalesPerformanceCard h3 {
        font-size: clamp(19px, 1.15vw, 22px) !important;
        line-height: 1.25 !important;
        font-weight: 650 !important;
    }

    #sellerSalesPeriodLabel {
        margin-top: 5px !important;
        font-size: clamp(11.5px, .68vw, 13px) !important;
        line-height: 1.55 !important;
    }

    .seller-sales-period-button {
        min-height: 37px !important;
        padding: 8px 12px !important;
        font-size: clamp(10.5px, .6vw, 12px) !important;
        line-height: 1.15 !important;
    }

    #sellerSalesPerformanceCard > div:nth-child(2) > div {
        padding: 9px 11px !important;
    }

    #sellerSalesPerformanceCard > div:nth-child(2) p:first-child {
        font-size: clamp(10px, .58vw, 11.5px) !important;
        line-height: 1.35 !important;
        letter-spacing: .055em !important;
    }

    #sellerSalesPerformanceCard > div:nth-child(2) p:last-child {
        margin-top: 5px !important;
        font-size: clamp(16px, .95vw, 19px) !important;
        line-height: 1.15 !important;
        font-weight: 600 !important;
    }

    #sellerSalesEmptyState p:first-child {
        font-size: clamp(11px, .64vw, 12.5px) !important;
        line-height: 1.45 !important;
        font-weight: 600 !important;
    }

    #sellerSalesEmptyState p:last-child {
        margin-top: 5px !important;
        font-size: clamp(10px, .58vw, 11.5px) !important;
        line-height: 1.55 !important;
    }

    #sellerSalesBestPeriod,
    #sellerSalesPerformanceCard > div:last-child > p:last-child {
        font-size: clamp(10px, .58vw, 11.5px) !important;
        line-height: 1.45 !important;
    }

    @media (max-width: 639px) {
        #sellerSalesPerformanceCard h3 {
            font-size: 18px !important;
        }

        #sellerSalesPeriodLabel {
            font-size: 11px !important;
        }

        .seller-sales-period-button {
            min-height: 35px !important;
            padding-inline: 10px !important;
            font-size: 10px !important;
        }

        #sellerSalesPerformanceCard > div:nth-child(2) p:first-child {
            font-size: 9px !important;
        }

        #sellerSalesPerformanceCard > div:nth-child(2) p:last-child {
            font-size: 15px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SUBTLE CARD DEPTH — SOFT NEUMORPHISM / FLOATING CONTAINERS
    |--------------------------------------------------------------------------
    | Adds a slightly lifted look to the main dashboard containers only.
    | Not too strong, not too blurry — just enough depth so the cards no longer
    | look flat or glued to the page.
    */
    :root {
        --seller-card-shadow-soft:
            0 10px 24px rgba(37, 29, 20, .055),
            0 2px 7px rgba(37, 29, 20, .028),
            inset 0 1px 0 rgba(255, 255, 255, .92),
            inset 0 -1px 0 rgba(230, 223, 213, .62);

        --seller-card-shadow-soft-hover:
            0 14px 30px rgba(37, 29, 20, .075),
            0 4px 10px rgba(37, 29, 20, .035),
            inset 0 1px 0 rgba(255, 255, 255, .95),
            inset 0 -1px 0 rgba(230, 223, 213, .68);
    }

    /* SUMMARY CARDS */
    #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a {
        box-shadow: var(--seller-card-shadow-soft) !important;
        border-color: #e6ddd1 !important;
        transition:
            box-shadow .18s ease,
            transform .18s ease,
            border-color .18s ease !important;
    }

    #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a:hover {
        box-shadow: var(--seller-card-shadow-soft-hover) !important;
        transform: translateY(-1px);
        border-color: #dccfbd !important;
    }

    /* ANALYTICS + SIDE PANELS */
    #sellerCategoryBreakdownCard,
    #sellerSalesPerformanceCard,
    #sellerCommerceSideColumn > .seller-side-panel {
        box-shadow: var(--seller-card-shadow-soft) !important;
        border-color: #e8dfd3 !important;
        transition:
            box-shadow .18s ease,
            transform .18s ease,
            border-color .18s ease !important;
    }

    #sellerCategoryBreakdownCard:hover,
    #sellerSalesPerformanceCard:hover,
    #sellerCommerceSideColumn > .seller-side-panel:hover {
        box-shadow: var(--seller-card-shadow-soft-hover) !important;
        transform: translateY(-1px);
        border-color: #ddd0bf !important;
    }

    /* INNER MINI CONTAINERS — very light only */
    #sellerCategoryBreakdownCard .seller-category-stat-row,
    #sellerSalesPerformanceCard > div:nth-child(2),
    #sellerSalesPerformanceCard > div:nth-child(3),
    #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div,
    #sellerCommerceSideColumn .seller-order-row,
    #sellerCommerceSideColumn .seller-inventory-row {
        box-shadow:
            0 3px 10px rgba(37, 29, 20, .035),
            inset 0 1px 0 rgba(255, 255, 255, .90) !important;
    }

    /* Keep mobile subtle so it does not feel heavy */
    @media (max-width: 640px) {
        #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a,
        #sellerCategoryBreakdownCard,
        #sellerSalesPerformanceCard,
        #sellerCommerceSideColumn > .seller-side-panel {
            box-shadow:
                0 8px 18px rgba(37, 29, 20, .05),
                0 2px 6px rgba(37, 29, 20, .025),
                inset 0 1px 0 rgba(255,255,255,.90),
                inset 0 -1px 0 rgba(230,223,213,.56) !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ANALYTICS LAYOUT REFINEMENT — WIDER / CLEANER / LESS CRAMPED
    |--------------------------------------------------------------------------
    | Gives the Catalog + Sales cards more breathing room and slightly stronger
    | but still tasteful neumorphism so the containers feel more lifted.
    */

    #sellerAnalyticsRow {
        gap: 14px !important;
    }

    @media (min-width: 1280px) {
        #sellerAnalyticsRow {
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 1.15fr) !important;
        }
    }

    /* Slightly stronger, still subtle card depth */
    #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a,
    #sellerCategoryBreakdownCard,
    #sellerSalesPerformanceCard,
    #sellerCommerceSideColumn > .seller-side-panel {
        box-shadow:
            0 14px 30px rgba(37, 29, 20, .072),
            0 4px 10px rgba(37, 29, 20, .034),
            inset 0 1px 0 rgba(255, 255, 255, .95),
            inset 0 -1px 0 rgba(230, 223, 213, .66) !important;
    }

    #sellerCategoryBreakdownCard:hover,
    #sellerSalesPerformanceCard:hover,
    #sellerCommerceSideColumn > .seller-side-panel:hover,
    #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a:hover {
        box-shadow:
            0 18px 38px rgba(37, 29, 20, .088),
            0 6px 13px rgba(37, 29, 20, .042),
            inset 0 1px 0 rgba(255, 255, 255, .98),
            inset 0 -1px 0 rgba(230, 223, 213, .70) !important;
    }

    /* Make the two main analytics cards feel roomier */
    #sellerCategoryBreakdownCard,
    #sellerSalesPerformanceCard {
        min-height: 395px !important;
        border-radius: 18px !important;
    }

    #sellerCategoryBreakdownCard > div:first-child {
        padding: 13px 15px !important;
    }

    #sellerCategoryBreakdownCard > div:nth-child(2) {
        padding: 14px 15px !important;
        gap: 14px !important;
        grid-template-columns: minmax(0, 1.26fr) minmax(158px, .74fr) !important;
        align-items: center !important;
    }

    .seller-category-donut {
        width: min(100%, 196px) !important;
    }

    .seller-category-donut::before {
        inset: 27% !important;
    }

    .seller-category-donut-center {
        inset: 29.5% !important;
    }

    #sellerCategoryCenterLabel,
    #sellerCategoryCenterSub {
        max-width: 118px !important;
        font-size: clamp(8.9px, .14vw + 7.2px, 9.8px) !important;
        line-height: 1.35 !important;
    }

    #sellerCategoryCenterValue {
        margin-top: 6px !important;
        font-size: clamp(26px, .42vw + 21px, 29px) !important;
        line-height: 1 !important;
    }

    #sellerCategoryLegend {
        margin-top: 12px !important;
        gap: 7px !important;
    }

    #sellerCategoryLegend .seller-category-legend-button {
        min-height: 34px !important;
        padding: 7px 10px !important;
        border-radius: 9px !important;
    }

    #sellerCategoryBreakdownCard .seller-category-stat-row {
        padding: 10px 11px !important;
    }

    #sellerCategoryBreakdownCard > div:last-child {
        padding: 11px 14px !important;
    }

    #sellerCategoryViewProducts {
        height: 36px !important;
        padding-inline: 14px !important;
    }

    #sellerSalesPerformanceCard {
        padding: 15px !important;
    }

    #sellerSalesPerformanceCard > div:first-child {
        gap: 10px !important;
    }

    .seller-sales-period-button {
        padding: 8px 11px !important;
    }

    #sellerSalesPerformanceCard > div:nth-child(2) {
        margin-top: 12px !important;
        border-radius: 11px !important;
    }

    #sellerSalesPerformanceCard > div:nth-child(2) > div {
        padding: 9px 11px !important;
    }

    #sellerSalesPerformanceCard > div:nth-child(3) {
        margin-top: 11px !important;
        padding: 7px 9px !important;
        border-radius: 12px !important;
    }

    #sellerSalesChart {
        height: 176px !important;
    }

    #sellerSalesEmptyState p:first-child {
        font-size: clamp(9.5px, .16vw + 7.8px, 10.5px) !important;
    }

    #sellerSalesEmptyState p:last-child,
    #sellerSalesBestPeriod,
    #sellerSalesPerformanceCard > div:last-child > p:last-child {
        font-size: clamp(8.6px, .13vw + 7.2px, 9.4px) !important;
    }

    #sellerSalesPerformanceCard > div:last-child {
        margin-top: 9px !important;
    }

    @media (max-width: 1179px) {
        #sellerCategoryBreakdownCard,
        #sellerSalesPerformanceCard {
            min-height: auto !important;
        }

        #sellerCategoryBreakdownCard > div:nth-child(2) {
            grid-template-columns: minmax(0, 1fr) !important;
        }

        .seller-category-donut {
            width: min(100%, 188px) !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UNIFIED CONTAINER SHADOW + DONUT CENTER FIX
    |--------------------------------------------------------------------------
    | Makes all major dashboard containers use the same floating shadow feel as
    | the category card, and gives the donut center text cleaner spacing so
    | "Total Listings" and the count do not look cramped.
    */

    :root {
        --seller-elevated-card-shadow:
            0 16px 34px rgba(37, 29, 20, .078),
            0 5px 12px rgba(37, 29, 20, .036),
            inset 0 1px 0 rgba(255, 255, 255, .96),
            inset 0 -1px 0 rgba(230, 223, 213, .68);

        --seller-elevated-card-shadow-hover:
            0 20px 42px rgba(37, 29, 20, .095),
            0 7px 15px rgba(37, 29, 20, .044),
            inset 0 1px 0 rgba(255, 255, 255, .98),
            inset 0 -1px 0 rgba(230, 223, 213, .72);
    }

    /* Make all main dashboard cards visually consistent */
    #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a,
    #sellerCategoryBreakdownCard,
    #sellerSalesPerformanceCard,
    #sellerCommerceSideColumn > .seller-side-panel {
        box-shadow: var(--seller-elevated-card-shadow) !important;
        border-color: #e7ddd0 !important;
    }

    #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a:hover,
    #sellerCategoryBreakdownCard:hover,
    #sellerSalesPerformanceCard:hover,
    #sellerCommerceSideColumn > .seller-side-panel:hover {
        box-shadow: var(--seller-elevated-card-shadow-hover) !important;
        border-color: #ddcfbc !important;
    }

    /* Slightly stronger but still clean depth for inner stat areas */
    #sellerCategoryBreakdownCard .seller-category-stat-row,
    #sellerSalesPerformanceCard > div:nth-child(2),
    #sellerSalesPerformanceCard > div:nth-child(3),
    #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div,
    #sellerCommerceSideColumn .seller-order-row,
    #sellerCommerceSideColumn .seller-inventory-row {
        box-shadow:
            0 4px 11px rgba(37, 29, 20, .04),
            inset 0 1px 0 rgba(255, 255, 255, .92) !important;
    }

    /* Donut area: more room and cleaner label/value separation */
    .seller-category-donut {
        width: min(100%, 206px) !important;
    }

    .seller-category-donut::before {
        inset: 26% !important;
    }

    .seller-category-donut-center {
        inset: 28.5% !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
        padding: 4px !important;
    }

    #sellerCategoryCenterLabel,
    #sellerCategoryCenterSub {
        display: block !important;
        max-width: 118px !important;
        white-space: normal !important;
        overflow: visible !important;
        text-overflow: clip !important;
        line-height: 1.25 !important;
        letter-spacing: .05em !important;
        font-size: clamp(8.4px, .14vw + 7px, 9.4px) !important;
    }

    #sellerCategoryCenterValue {
        display: block !important;
        margin-top: 0 !important;
        line-height: .96 !important;
        font-size: clamp(26px, .44vw + 21px, 30px) !important;
    }

    #sellerCategoryBreakdownCard > div:nth-child(2) {
        padding: 15px 16px !important;
        gap: 15px !important;
        grid-template-columns: minmax(0, 1.28fr) minmax(160px, .72fr) !important;
    }

    @media (max-width: 1179px) {
        .seller-category-donut {
            width: min(100%, 194px) !important;
        }

        .seller-category-donut::before {
            inset: 27% !important;
        }

        .seller-category-donut-center {
            inset: 30% !important;
            gap: 3px !important;
        }
    }

    @media (max-width: 640px) {
        #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a,
        #sellerCategoryBreakdownCard,
        #sellerSalesPerformanceCard,
        #sellerCommerceSideColumn > .seller-side-panel {
            box-shadow:
                0 10px 22px rgba(37, 29, 20, .06),
                0 3px 8px rgba(37, 29, 20, .03),
                inset 0 1px 0 rgba(255,255,255,.94),
                inset 0 -1px 0 rgba(230,223,213,.60) !important;
        }
    }



    /*
    |--------------------------------------------------------------------------
    | HOVER POLICY — SUMMARY CARDS + BUTTONS ONLY
    |--------------------------------------------------------------------------
    | Normal dashboard containers stay visually still under the cursor.
    | Summary cards and real action buttons keep their existing hover feedback.
    */

    /* Product preview cards: keep the resting appearance on hover. */
    #sellerProductsPanel .seller-responsive-product-card:hover {
        transform: none !important;
        border-color: #e8e2da !important;
        box-shadow: 0 5px 16px rgba(39, 31, 22, .025) !important;
    }

    /* Main analytics / operations containers: no hover lift or shadow change. */
    #sellerCategoryBreakdownCard:hover,
    #sellerSalesPerformanceCard:hover,
    #sellerCommerceSideColumn > .seller-side-panel:hover {
        transform: none !important;
        border-color: #e7ddd0 !important;
        box-shadow: var(--seller-elevated-card-shadow) !important;
    }

    /* Product Library cards are static; their action buttons still react. */
    #sellerAllProductsModal [data-library-product]:hover {
        transform: none !important;
        border-color: #ebe5dd !important;
        box-shadow: none !important;
    }

    /* Workflow rows are information rows, not hover cards. */
    #sellerCommerceSideColumn .seller-order-row:hover {
        transform: none !important;
        background-color: #ffffff !important;
        border-color: #eee8df !important;
        box-shadow:
            0 4px 11px rgba(37, 29, 20, .04),
            inset 0 1px 0 rgba(255, 255, 255, .92) !important;
    }

    /* Inventory rows and financial mini-cards remain completely still. */
    #sellerCommerceSideColumn .seller-inventory-row:hover,
    #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div:hover,
    #sellerCategoryBreakdownCard .seller-category-stat-row:hover {
        transform: none !important;
    }

</style>

<div id="sellerDashboardStage" class="mx-auto w-full max-w-[1800px]">

    {{-- =========================================================
        DASHBOARD SKELETON — CONTENT ONLY
        Sidebar / header / logo remain immediately visible.
    ========================================================== --}}
    <div id="sellerDashboardSkeleton" aria-hidden="true" class="w-full">
        {{-- Notice placeholder --}}
        <div class="mb-4 h-[88px] rounded-[18px] border border-[#eee8df] bg-white p-4">
            <div class="flex h-full items-center gap-4">
                <div class="seller-skeleton-block h-11 w-11 shrink-0 rounded-[13px]"></div>
                <div class="min-w-0 flex-1">
                    <div class="seller-skeleton-block h-3.5 w-[170px] rounded-full"></div>
                    <div class="seller-skeleton-block mt-2.5 h-2.5 w-[min(620px,75%)] rounded-full"></div>
                </div>
                <div class="seller-skeleton-block hidden h-9 w-[100px] rounded-lg sm:block"></div>
            </div>
        </div>

        {{-- Greeting hero placeholder --}}
        <div class="rounded-[20px] border border-[#e9e3da] bg-[#26231f] px-5 py-5 sm:px-6 lg:px-7">
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-[1.25fr_2fr_auto] xl:items-center">
                <div class="flex items-center gap-4">
                    <div class="seller-skeleton-block h-[68px] w-[68px] shrink-0 rounded-[18px] bg-[#38332c]"></div>
                    <div class="min-w-0 flex-1">
                        <div class="seller-skeleton-block h-5 w-[210px] rounded-full bg-[#3a3630]"></div>
                        <div class="seller-skeleton-block mt-3 h-2.5 w-[180px] rounded-full bg-[#3a3630]"></div>
                        <div class="mt-3 flex gap-2">
                            <div class="seller-skeleton-block h-6 w-[105px] rounded-full bg-[#3a3630]"></div>
                            <div class="seller-skeleton-block h-6 w-[120px] rounded-full bg-[#3a3630]"></div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    @for ($i = 0; $i < 3; $i++)
                        <div class="rounded-[16px] border border-white/10 bg-white/[0.04] p-4">
                            <div class="seller-skeleton-block h-2 w-10 rounded-full bg-[#3d3933]"></div>
                            <div class="mt-3 flex items-center gap-3">
                                <div class="seller-skeleton-block h-9 w-9 rounded-full bg-[#3d3933]"></div>
                                <div class="flex-1">
                                    <div class="seller-skeleton-block h-4 w-[75%] rounded-full bg-[#3d3933]"></div>
                                    <div class="seller-skeleton-block mt-2 h-2 w-[58%] rounded-full bg-[#3d3933]"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                <div class="flex gap-2">
                    <div class="seller-skeleton-block h-11 w-[138px] rounded-xl bg-[#3a3630]"></div>
                    <div class="seller-skeleton-block h-11 w-[105px] rounded-xl bg-[#3a3630]"></div>
                </div>
            </div>
        </div>

        {{-- Summary cards --}}
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
            @for ($i = 0; $i < 5; $i++)
                <div class="rounded-[16px] border border-[#ebe4da] bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <div class="seller-skeleton-block h-2.5 w-[90px] rounded-full"></div>
                            <div class="seller-skeleton-block mt-3 h-6 w-[76px] rounded-lg"></div>
                            <div class="seller-skeleton-block mt-3 h-2 w-[105px] rounded-full"></div>
                        </div>
                        <div class="seller-skeleton-block h-10 w-10 rounded-xl"></div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Analytics row — matches the real 2-card desktop layout --}}
        <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
            @for ($card = 0; $card < 2; $card++)
                <div class="rounded-[18px] border border-[#ebe4da] bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="seller-skeleton-block h-4 w-[185px] max-w-[60vw] rounded-full"></div>
                            <div class="seller-skeleton-block mt-2 h-2.5 w-[245px] max-w-[72vw] rounded-full"></div>
                        </div>
                        <div class="seller-skeleton-block h-8 w-[92px] rounded-full"></div>
                    </div>

                    @if ($card === 0)
                        <div class="mt-4 grid grid-cols-1 items-center gap-4 sm:grid-cols-[minmax(0,1fr)_132px]">
                            <div class="flex items-center gap-3">
                                <div class="seller-skeleton-block h-[160px] w-[160px] shrink-0 rounded-full"></div>
                                <div class="grid flex-1 grid-cols-2 gap-2">
                                    @for ($i = 0; $i < 4; $i++)
                                        <div class="seller-skeleton-block h-8 rounded-lg"></div>
                                    @endfor
                                </div>
                            </div>
                            <div class="space-y-2">
                                @for ($i = 0; $i < 4; $i++)
                                    <div class="seller-skeleton-block h-[38px] rounded-[9px]"></div>
                                @endfor
                            </div>
                        </div>
                    @else
                        <div class="mt-3 grid grid-cols-3 gap-2">
                            @for ($i = 0; $i < 3; $i++)
                                <div class="seller-skeleton-block h-14 rounded-xl"></div>
                            @endfor
                        </div>
                        <div class="seller-skeleton-block mt-3 h-[150px] rounded-[10px]"></div>
                    @endif
                </div>
            @endfor
        </div>

        {{-- Compact operations row — mirrors Financial / Orders / Inventory --}}
        <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
            @for ($card = 0; $card < 3; $card++)
                <div class="rounded-[14px] border border-[#ebe4da] bg-white p-[11px]">
                    <div class="seller-skeleton-block h-3 w-[118px] rounded-full"></div>
                    <div class="seller-skeleton-block mt-1.5 h-2 w-[160px] max-w-[75%] rounded-full"></div>

                    @if ($card === 0)
                        <div class="mt-2 grid gap-[5px]">
                            @for ($i = 0; $i < 3; $i++)
                                <div class="seller-skeleton-block h-[39px] rounded-[9px]"></div>
                            @endfor
                        </div>
                    @elseif ($card === 1)
                        <div class="mt-2 grid grid-cols-2 gap-[5px]">
                            @for ($i = 0; $i < 6; $i++)
                                <div class="seller-skeleton-block h-[34px] rounded-[8px]"></div>
                            @endfor
                        </div>
                    @else
                        <div class="mt-2 grid gap-[5px]">
                            @for ($i = 0; $i < 3; $i++)
                                <div class="seller-skeleton-block h-[38px] rounded-[8px]"></div>
                            @endfor
                        </div>
                    @endif

                    <div class="seller-skeleton-block mt-2 h-[31px] rounded-[8px]"></div>
                </div>
            @endfor
        </div>
    </div>

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
        PREMIUM WELCOME PANEL
    ========================================================== --}}
    <section class="seller-greeting-hero relative rounded-[20px] px-5 py-5 sm:px-6 lg:px-7">
        <div class="seller-greeting-rings hidden lg:block"></div>

        <div class="relative flex flex-col gap-5 2xl:flex-row 2xl:items-center 2xl:justify-between">
            <div class="flex flex-1 flex-col gap-4 xl:flex-row xl:items-center xl:gap-5">
                <div class="flex items-start gap-4 sm:gap-5">
                    <div class="seller-greeting-icon-shell grid h-16 w-16 shrink-0 place-items-center rounded-[18px] text-[#eba81e] sm:h-[72px] sm:w-[72px]">
                        <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 10h16"></path>
                            <path d="M5 10v9h14v-9"></path>
                            <path d="M7 10 9 5h6l2 5"></path>
                            <path d="M9 19v-5h6v5"></path>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <h2 id="sellerGreetingText" class="text-[22px] font-bold tracking-[-0.03em] text-white sm:text-[25px]">
                            Good morning, Seller! 👋
                        </h2>
                        <p class="mt-1.5 text-[10px] leading-5 text-white/80 sm:text-[11px]">
                            Here’s what’s happening with your store today.
                        </p>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="seller-greeting-chip rounded-full px-3 py-1.5 text-[8px] font-medium text-white/90">
                                <span class="inline-block h-2 w-2 rounded-full bg-[#4ade80]"></span>
                                {{ number_format($pendingOrderCount) }} pending {{ $pendingOrderCount === 1 ? 'order' : 'orders' }}
                            </span>
                            <span class="seller-greeting-chip rounded-full px-3 py-1.5 text-[8px] font-medium text-white/90">
                                <svg viewBox="0 0 24 24" class="h-3 w-3 text-white/75" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="M12 7v5l3 2"></path>
                                </svg>
                                ₱{{ number_format($monthlySales, 2) }} monthly sales
                            </span>
                        </div>
                    </div>
                </div>

                <div class="hidden xl:block seller-greeting-divider"></div>

                <div class="grid flex-1 grid-cols-1 gap-3 sm:grid-cols-3 xl:gap-4">
                    <div class="seller-greeting-stat rounded-[16px] border px-4 py-3">
                        <p class="text-[7px] font-bold uppercase tracking-[.12em] text-white/45">Time</p>
                        <div class="mt-2 flex items-center gap-2.5 text-white">
                            <span class="grid h-9 w-9 place-items-center rounded-full border border-[#a87618]/35 bg-white/[0.035] text-[#e1a21f]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <circle cx="12" cy="12" r="8.5"></circle>
                                    <path d="M12 7.5v5l3 2"></path>
                                </svg>
                            </span>
                            <div>
                                <p id="sellerCurrentTime" class="text-[20px] font-bold tracking-[-0.03em] sm:text-[21px]">08:56 AM</p>
                                <p id="sellerTimeContext" class="mt-0.5 text-[8px] text-white/55">Asia/Manila</p>
                            </div>
                        </div>
                    </div>

                    <div class="seller-greeting-stat rounded-[16px] border px-4 py-3">
                        <p class="text-[7px] font-bold uppercase tracking-[.12em] text-white/45">Date</p>
                        <div class="mt-2 flex items-center gap-2.5 text-white">
                            <span class="grid h-9 w-9 place-items-center rounded-full border border-[#a87618]/35 bg-white/[0.035] text-[#e1a21f]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M7 3v3"></path>
                                    <path d="M17 3v3"></path>
                                    <rect x="4" y="5" width="16" height="15" rx="2"></rect>
                                    <path d="M4 10h16"></path>
                                </svg>
                            </span>
                            <div>
                                <p id="sellerCurrentDay" class="text-[18px] font-bold tracking-[-0.03em]">Friday</p>
                                <p id="sellerCurrentDate" class="mt-0.5 text-[8px] text-white/55">May 23, 2025</p>
                            </div>
                        </div>
                    </div>

                    <div class="seller-greeting-stat rounded-[16px] border px-4 py-3">
                        <p class="text-[7px] font-bold uppercase tracking-[.12em] text-white/45">Weather</p>
                        <div class="mt-2 flex items-center gap-2.5 text-white">
                            <span class="seller-greeting-weather-pulse grid h-9 w-9 place-items-center rounded-full border border-[#3d78aa]/35 bg-white/[0.035] text-[#63aff5]">
                                <svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M17.5 17.5H8a4 4 0 1 1 .8-7.92A5 5 0 0 1 18 9a3.5 3.5 0 1 1-.5 8.5Z"></path>
                                    <path d="M15 7.5a2.5 2.5 0 0 1 2.5-2.5"></path>
                                </svg>
                            </span>
                            <div>
                                <p id="sellerWeatherTemp" class="text-[18px] font-bold tracking-[-0.03em]">29°C</p>
                                <p id="sellerWeatherCondition" class="mt-0.5 text-[8px] text-white/55">Current conditions</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-[1] flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('seller.products.index') }}"
                    class="inline-flex h-11 items-center gap-2 rounded-xl bg-[#d48f08] px-5 text-[10px] font-semibold text-white shadow-[0_14px_24px_rgba(212,143,8,.18)] transition hover:bg-[#bd7d05]"
                    wire:navigate
                >
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M5 7h14l-1 13H6L5 7Z"></path>
                        <path d="M9 7a3 3 0 0 1 6 0"></path>
                    </svg>
                    Manage Products
                </a>

                <a href="{{ route('seller.orders') }}" class="inline-flex h-11 items-center gap-2 rounded-xl border border-white/20 bg-white/[0.06] px-4 text-[10px] font-semibold text-white transition hover:bg-white/[0.10] shadow-[0_3px_10px_rgba(0,0,0,.10)]"
                wire:navigate
            >
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M5 7h14l-1 13H6L5 7Z"></path>
                        <path d="M9 7a3 3 0 0 1 6 0"></path>
                    </svg>
                    View Orders
                </a>
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
                @if ($card['navigate']) wire:navigate @endif
                class="group relative overflow-hidden rounded-[18px] border {{ $card['border'] }} bg-white px-4 py-4 shadow-[0_5px_18px_rgba(37,29,20,.025)] transition duration-200 hover:-translate-y-px hover:border-[#d8ccb9] hover:shadow-[0_12px_26px_rgba(37,29,20,.055)] sm:px-[17px] sm:py-[16px]"
            >
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="text-[10.5px] font-semibold leading-4 text-[#6f675e]">
                                {{ $card['label'] }}
                            </p>
                            <span class="h-1.5 w-1.5 rounded-full {{ $card['icon'] }} bg-current opacity-40"></span>
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

                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-[12px] {{ $card['bg'] }} {{ $card['icon'] }} transition duration-200 group-hover:scale-[1.04]">
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

                <div class="absolute bottom-0 left-0 h-[2px] w-0 bg-[#d69a27]/55 transition-all duration-300 group-hover:w-full"></div>
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
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-[12px] border border-[#ecdcb9] bg-[#fff8e9] text-[#bd8118]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 3a9 9 0 1 0 9 9h-9V3Z"></path>
                            <path d="M15 3.7A9 9 0 0 1 20.3 9H15V3.7Z"></path>
                        </svg>
                    </span>

                    <div class="min-w-0">
                        <h3 class="text-[18px] font-semibold tracking-[-0.03em] text-[#28221b] sm:text-[19px]">Catalog Category Breakdown</h3>
                        <p class="mt-1 text-[10.5px] leading-5 text-[#887f75]">Distribution of your active e-commerce product listings.</p>
                    </div>
                </div>

                <span class="shrink-0 rounded-full border border-[#eadfc9] bg-[#fffaf2] px-3 py-1.5 text-[9px] font-medium text-[#9b6c1c]">
                    Active Catalog
                </span>
            </div>

            <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-[minmax(0,1.18fr)_minmax(132px,.82fr)] sm:items-center">
                {{-- DONUT + LEGEND --}}
                <div class="min-w-0">
                    <div class="flex justify-center">
                        <div id="sellerCategoryDonut" class="seller-category-donut" role="img" aria-label="Seller catalog category distribution">
                            <div class="seller-category-donut-center">
                                <span id="sellerCategoryCenterLabel" class="max-w-[100px] truncate text-[8.5px] font-medium uppercase tracking-[.08em] text-[#8f867c]">Total Listings</span>
                                <strong id="sellerCategoryCenterValue" class="mt-1.5 text-[26px] font-semibold leading-none tracking-[-.045em] text-[#24201b]">{{ number_format($totalProductCount) }}</strong>
                                <span id="sellerCategoryCenterSub" class="mt-1.5 hidden max-w-[100px] text-[8.5px] leading-4 text-[#91887d]"></span>
                            </div>
                        </div>
                    </div>

                    <div id="sellerCategoryLegend" class="mt-4 grid grid-cols-2 gap-2">
                        <div class="seller-skeleton-block h-8 rounded-[10px]"></div>
                        <div class="seller-skeleton-block h-8 rounded-[10px]"></div>
                        <div class="seller-skeleton-block h-8 rounded-[10px]"></div>
                        <div class="seller-skeleton-block h-8 rounded-[10px]"></div>
                    </div>
                </div>

                {{-- E-COMMERCE CATEGORY INSIGHTS --}}
                <div class="overflow-hidden rounded-[14px] border border-[#eee7dd] bg-[#fdfbf8]">
                    <div class="seller-category-stat-row px-3.5 py-3">
                        <div class="flex items-center gap-2.5">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-[10px] border border-[#eadfc9] bg-white text-[#bd8118]">
                                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M8 4h8v3a4 4 0 0 1-8 0V4Z"></path>
                                    <path d="M6 5H4v2a4 4 0 0 0 4 4"></path>
                                    <path d="M18 5h2v2a4 4 0 0 1-4 4"></path>
                                    <path d="M12 11v5"></path>
                                    <path d="M9 20h6"></path>
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[8.5px] font-medium text-[#887f75]">Largest Category</p>
                                <div class="mt-0.5 flex items-center justify-between gap-2">
                                    <strong id="sellerCategoryTopName" class="truncate text-[12px] font-semibold text-[#39332d]">—</strong>
                                    <span id="sellerCategoryTopPercent" class="shrink-0 text-[9.5px] font-medium text-[#b57912]">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="seller-category-stat-row px-3.5 py-3">
                        <div class="flex items-center gap-2.5">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-[10px] border border-[#d8e9df] bg-white text-[#4f8065]">
                                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="8"></circle>
                                    <path d="m8 12 2.5 2.5L16 9"></path>
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[8.5px] font-medium text-[#887f75]">Approved Listings</p>
                                <strong id="sellerCategoryApproved" class="mt-1 block text-[12.5px] font-semibold text-[#39332d]">{{ number_format($approvedCount) }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="seller-category-stat-row px-3.5 py-3">
                        <div class="flex items-center gap-2.5">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-[10px] border border-[#efdada] bg-white text-[#b65e5e]">
                                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M12 3 3 20h18L12 3Z"></path>
                                    <path d="M12 9v5"></path>
                                    <path d="M12 17h.01"></path>
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[8.5px] font-medium text-[#887f75]">Low Stock Listings</p>
                                <strong id="sellerCategoryLowStock" class="mt-1 block text-[12.5px] font-semibold text-[#39332d]">{{ number_format($lowStockCount) }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="seller-category-stat-row px-3.5 py-3">
                        <div class="flex items-center gap-2.5">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-[10px] border border-[#dce4ee] bg-white text-[#587894]">
                                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="4" y="4" width="6" height="6" rx="1"></rect>
                                    <rect x="14" y="4" width="6" height="6" rx="1"></rect>
                                    <rect x="4" y="14" width="6" height="6" rx="1"></rect>
                                    <rect x="14" y="14" width="6" height="6" rx="1"></rect>
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[8.5px] font-medium text-[#887f75]">Active Categories</p>
                                <strong id="sellerCategoryCount" class="mt-1 block text-[12.5px] font-semibold text-[#39332d]">0</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2 border-t border-[#eee8df] bg-[#fffefa] px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-[8.5px] leading-4 text-[#887f75]">
                    Tap a category to inspect it. Percentages are based on active listing count.
                </p>

                <button
                    id="sellerCategoryViewProducts"
                    type="button"
                    class="inline-flex h-9 shrink-0 items-center justify-center gap-2 rounded-[10px] border border-[#dfc783] bg-white px-3.5 text-[9.5px] font-medium text-[#9a6817] transition hover:border-[#cda94d] hover:bg-[#fff9eb]"
                >
                    <span id="sellerCategoryViewProductsLabel">View All Products</span>
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- SALES PERFORMANCE — REAL DELIVERED ORDER DATA --}}
        <div id="sellerSalesPerformanceCard" class="h-full rounded-[18px] border border-[#ebe4da] bg-white p-5 shadow-[0_7px_18px_rgba(33,24,14,.035)]">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h3 class="text-[18px] font-semibold tracking-[-0.03em] text-[#28221b] sm:text-[19px]">Sales Performance</h3>
                    <p id="sellerSalesPeriodLabel" class="mt-1 text-[10.5px] leading-5 text-[#887f75]">
                        Delivered merchandise sales.
                    </p>
                </div>

                <div class="inline-flex w-fit items-center rounded-[11px] border border-[#ece5dc] bg-[#fbfaf7] p-0.5">
                    <button
                        type="button"
                        data-sales-period="month"
                        class="seller-sales-period-button rounded-[9px] bg-white px-3.5 py-2 text-[9.5px] font-medium text-[#2f2923] shadow-[0_2px_7px_rgba(33,24,14,.05)]"
                    >
                        This Month
                    </button>
                    <button
                        type="button"
                        data-sales-period="last_month"
                        class="seller-sales-period-button rounded-[9px] px-3.5 py-2 text-[9.5px] font-normal text-[#8b8277] transition hover:bg-white hover:text-[#2f2923]"
                    >
                        Last Month
                    </button>
                    <button
                        type="button"
                        data-sales-period="year"
                        class="seller-sales-period-button rounded-[9px] px-3 py-1.5 text-[8px] font-normal text-[#8b8277] transition hover:bg-white hover:text-[#2f2923]"
                    >
                        This Year
                    </button>
                </div>
            </div>

            <div class="mt-3 grid grid-cols-3 overflow-hidden rounded-[12px] border border-[#eee8df] bg-[#fcfbf8]">
                <div class="px-3 py-2.5">
                    <p class="text-[8.5px] font-medium uppercase tracking-[.07em] text-[#8f867c]">Total Sales</p>
                    <p id="sellerSalesTotal" class="mt-1.5 text-[15px] font-medium tracking-[-0.025em] text-[#2d2822]">₱0.00</p>
                </div>

                <div class="border-x border-[#eee8df] px-3 py-2.5">
                    <p class="text-[8.5px] font-medium uppercase tracking-[.07em] text-[#8f867c]">Delivered Orders</p>
                    <p id="sellerSalesOrders" class="mt-1.5 text-[15px] font-medium tracking-[-0.025em] text-[#2d2822]">0</p>
                </div>

                <div class="px-3 py-2.5">
                    <p class="text-[8.5px] font-medium uppercase tracking-[.07em] text-[#8f867c]">Average Order</p>
                    <p id="sellerSalesAverage" class="mt-1.5 text-[15px] font-medium tracking-[-0.025em] text-[#2d2822]">₱0.00</p>
                </div>
            </div>

            <div class="relative mt-3 overflow-hidden rounded-[13px] border border-[#f0ebe4] bg-[#fffefa] px-2 py-2">
                <svg
                    id="sellerSalesChart"
                    viewBox="0 0 720 260"
                    class="block h-[225px] w-full"
                    preserveAspectRatio="xMidYMid meet"
                    role="img"
                    aria-label="Seller delivered sales chart"
                >
                    <defs>
                        <linearGradient id="sellerSalesAreaLive" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#E1A019" stop-opacity="0.20" />
                            <stop offset="100%" stop-color="#E1A019" stop-opacity="0.015" />
                        </linearGradient>
                    </defs>
                    <g id="sellerSalesGrid"></g>
                    <path id="sellerSalesAreaPath" fill="url(#sellerSalesAreaLive)"></path>
                    <path id="sellerSalesLinePath" fill="none" stroke="#D99A17" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path>
                    <g id="sellerSalesPoints"></g>
                    <g id="sellerSalesXAxis"></g>
                </svg>

                <div
                    id="sellerSalesEmptyState"
                    class="pointer-events-none absolute inset-x-0 top-1/2 hidden -translate-y-1/2 text-center"
                >
                    <p class="text-[9.5px] font-medium text-[#6f675e]">No delivered sales in this period yet.</p>
                    <p class="mt-1 text-[8.5px] text-[#9a9187]">Completed Buyer → Seller → Courier orders will appear here.</p>
                </div>
            </div>

            <div class="mt-2.5 flex flex-wrap items-center justify-between gap-2">
                <p id="sellerSalesBestPeriod" class="text-[8.5px] font-normal text-[#887f75]">
                    Best period: —
                </p>
                <p class="text-[8.5px] font-normal text-[#9a9187]">
                    Merchandise subtotal only · delivery fees excluded
                </p>
            </div>
        </div>
    </section>

    {{-- =========================================================
        FINANCIAL + ORDER MANAGEMENT + INVENTORY ALERTS
        One responsive row on desktop; stacked on smaller screens.
    ========================================================== --}}
    <section id="sellerCommerceSideColumn" class="mt-4">

        {{-- FINANCIAL SNAPSHOT --}}
        <div class="seller-side-panel seller-financial-panel flex h-full min-w-0 flex-col rounded-[18px] border border-[#ebe4da] bg-white p-4 shadow-[0_7px_18px_rgba(33,24,14,.035)]">
            <div>
                <h3 class="seller-panel-title font-bold tracking-[-0.02em] text-[#28221b]">Financial Snapshot</h3>
                <p class="mt-1 text-[#91887d]">Quick summary before generating a full report.</p>
            </div>

            <div class="mt-3 space-y-2">
                <div class="rounded-[12px] border border-[#dfe8f1] bg-[#f7fbff] p-3">
                    <div class="flex items-center gap-3">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-[10px] border border-[#dbe7f4] bg-white text-[#3f78b7]">
                            <span class="text-[15px] font-medium">₱</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[8.5px] font-medium text-[#74818e]">Gross Sales</p>
                            <p class="mt-0.5 truncate text-[15px] font-bold tracking-[-0.03em] text-[#20272e]">₱{{ number_format($monthlySales, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[12px] border border-[#f0e2c9] bg-[#fffaf1] p-3">
                    <div class="flex items-center gap-3">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-[10px] border border-[#f1dfbd] bg-white text-[#d18a07]">
                            <span class="text-[15px] font-medium">%</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[8.5px] font-medium text-[#917f63]">Est. Platform Commission ({{ number_format($platformCommissionRate, 0) }}%)</p>
                            <p class="mt-0.5 truncate text-[15px] font-bold tracking-[-0.03em] text-[#8a641e]">₱{{ number_format($platformCommission, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[12px] border border-[#dce8df] bg-[#f7fbf8] p-3">
                    <div class="flex items-center gap-3">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-[10px] border border-[#d7e7db] bg-white text-[#4f865f]">
                            <span class="text-[15px] font-medium">₱</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[8.5px] font-medium text-[#768a7b]">Estimated Net Revenue</p>
                            <p class="mt-0.5 truncate text-[15px] font-bold tracking-[-0.03em] text-[#2f6a44]">₱{{ number_format($estimatedRevenue, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-auto pt-3">
                <a
                    href="{{ route('seller.reports') }}"
                    class="flex h-10 w-full items-center justify-between rounded-[10px] bg-[#e9a315] px-3.5 text-[9.5px] font-semibold text-white shadow-[0_7px_16px_rgba(212,143,8,.11)] transition hover:bg-[#d8940d]"
                    wire:navigate
                >
                    <span class="flex min-w-0 items-center gap-2">
                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-[8px] bg-white/15">
                            <svg viewBox="0 0 24 24" class="h-[14px] w-[14px]" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M5 3h10l4 4v14H5z"></path>
                                <path d="M9 13h6"></path>
                                <path d="M9 17h6"></path>
                            </svg>
                        </span>
                        <span class="truncate">Generate Full Report</span>
                    </span>
                    <svg viewBox="0 0 24 24" class="h-[14px] w-[14px] shrink-0" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 6l6 6-6 6"></path>
                    </svg>
                </a>
            </div>
        </div>

        {{-- ORDER MANAGEMENT --}}
        <div class="seller-side-panel seller-order-panel flex h-full min-w-0 flex-col rounded-[18px] border border-[#ebe4da] bg-white p-4 shadow-[0_7px_18px_rgba(33,24,14,.035)]">
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
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-[#3d3730]">{{ $lowStockProduct->name }}</p>
                                <p class="mt-1 truncate text-[#958c80]">SKU: {{ $lowStockProduct->sku }}</p>
                            </div>
                            <span class="seller-inventory-count shrink-0 font-bold {{ (int) $lowStockProduct->stock <= 2 ? 'text-[#b65353]' : 'text-[#b47d1e]' }}">
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

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD SKELETON REVEAL — FAST / LAYOUT-STABLE
    |--------------------------------------------------------------------------
    | A short clean pulse is shown before the dashboard fades in.
    | No shimmer, shine, or sweeping highlight is used.
    | Because the real content remains in flow, reloads do not jump vertically.
    */
    const sellerDashboardStage = document.getElementById('sellerDashboardStage');

    if (sellerDashboardStage) {
        sellerDashboardStage.classList.remove('seller-dashboard-ready');

        /*
        | Always enter Dashboard at the top. This prevents browser scroll
        | restoration from showing a lower skeleton section first.
        */
        window.scrollTo({ top: 0, left: 0, behavior: 'auto' });

        /*
        | Give the clean pulse enough time to be visible without making the
        | dashboard feel slow. No shimmer or sweeping highlight is used.
        */
        window.requestAnimationFrame(function () {
            window.requestAnimationFrame(function () {
                window.setTimeout(function () {
                    sellerDashboardStage.classList.add('seller-dashboard-ready');
                }, 700);
            });
        });
    }

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
    const greetingText = document.getElementById('sellerGreetingText');
    const currentTime = document.getElementById('sellerCurrentTime');
    const timeContext = document.getElementById('sellerTimeContext');
    const currentDay = document.getElementById('sellerCurrentDay');
    const currentDate = document.getElementById('sellerCurrentDate');
    const weatherTemp = document.getElementById('sellerWeatherTemp');
    const weatherCondition = document.getElementById('sellerWeatherCondition');

    function getGreetingByHour(hour) {
        if (hour >= 5 && hour < 12) return 'Good morning';
        if (hour >= 12 && hour < 18) return 'Good afternoon';
        return 'Good evening';
    }

    function updateSellerDateTime() {
        const now = new Date();
        const hour = now.getHours();
        const greeting = getGreetingByHour(hour);

        if (greetingText) {
            greetingText.textContent = `${greeting}, Seller! 👋`;
        }

        if (currentTime) {
            currentTime.textContent = new Intl.DateTimeFormat('en-PH', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            }).format(now);
        }

        if (timeContext) {
            const resolvedTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Local Time';
            timeContext.textContent = resolvedTimeZone.replace('_', ' ');
        }

        if (currentDay) {
            currentDay.textContent = new Intl.DateTimeFormat('en-PH', {
                weekday: 'long'
            }).format(now);
        }

        if (currentDate) {
            currentDate.textContent = new Intl.DateTimeFormat('en-PH', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            }).format(now);
        }
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

    async function fetchSellerWeather(latitude, longitude) {
        if (!weatherTemp || !weatherCondition) return;

        try {
            weatherCondition.textContent = 'Current conditions';

            const response = await fetch(
                `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&current=temperature_2m,weather_code&timezone=auto`
            );

            if (!response.ok) {
                throw new Error('Weather request failed');
            }

            const data = await response.json();
            const current = data?.current || {};
            const temperature = Math.round(Number(current.temperature_2m ?? 0));
            const condition = weatherCodeToText(current.weather_code);

            weatherTemp.textContent = `${temperature}°C`;
            weatherCondition.textContent = condition;
        } catch (error) {
            weatherTemp.textContent = '—';
            weatherCondition.textContent = 'Weather unavailable';
        }
    }

    function initSellerWeather() {
        const fallback = { latitude: 14.5995, longitude: 120.9842 };

        // Paint useful weather immediately instead of waiting for geolocation.
        fetchSellerWeather(fallback.latitude, fallback.longitude);

        if (!navigator.geolocation) {
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (position) {
                fetchSellerWeather(position.coords.latitude, position.coords.longitude);
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

    updateSellerDateTime();
    window.clearInterval(window.__SARI_SELLER_DASHBOARD_CLOCK__);
    window.__SARI_SELLER_DASHBOARD_CLOCK__ =
        window.setInterval(updateSellerDateTime, 1000);

    document.addEventListener('livewire:navigating', function () {
        window.clearInterval(window.__SARI_SELLER_DASHBOARD_CLOCK__);
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
    const sellerSalesAreaPath = document.getElementById('sellerSalesAreaPath');
    const sellerSalesLinePath = document.getElementById('sellerSalesLinePath');
    const sellerSalesPoints = document.getElementById('sellerSalesPoints');
    const sellerSalesXAxis = document.getElementById('sellerSalesXAxis');
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

    function renderSellerSalesPerformance(period = 'month') {
        if (!sellerSalesChart) return;

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

            button.classList.toggle('bg-white', active);
            button.classList.toggle('text-[#2f2923]', active);
            button.classList.toggle('shadow-[0_2px_7px_rgba(33,24,14,.05)]', active);
            button.classList.toggle('font-medium', active);

            button.classList.toggle('text-[#8b8277]', !active);
            button.classList.toggle('font-normal', !active);
        });

        if (!sellerSalesGrid || !sellerSalesAreaPath || !sellerSalesLinePath || !sellerSalesPoints || !sellerSalesXAxis) {
            return;
        }

        sellerSalesGrid.innerHTML = '';
        sellerSalesPoints.innerHTML = '';
        sellerSalesXAxis.innerHTML = '';

        const chart = {
            left: 66,
            right: 694,
            top: 24,
            bottom: 214,
        };

        const width = chart.right - chart.left;
        const height = chart.bottom - chart.top;
        const maxValue = sellerNiceMaximum(Math.max(0, ...values));

        // Grid + Y-axis labels.
        for (let index = 0; index <= 4; index++) {
            const ratio = index / 4;
            const y = chart.top + (height * ratio);
            const axisValue = maxValue * (1 - ratio);

            sellerSalesGrid.appendChild(sellerSvgElement('line', {
                x1: chart.left,
                y1: y,
                x2: chart.right,
                y2: y,
                stroke: index === 4 ? '#DDD5CB' : '#EEE8E0',
                'stroke-dasharray': index === 4 ? '0' : '4 5',
            }));

            sellerSalesGrid.appendChild(sellerSvgElement('text', {
                x: 6,
                y: y + 4,
                fill: '#91887D',
                'font-size': 9.5,
                'font-family': 'Poppins, sans-serif',
                'font-weight': 400,
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

        points.forEach((point) => {
            const circle = sellerSvgElement('circle', {
                cx: point.x,
                cy: point.y,
                r: 4.7,
                fill: '#fff',
                stroke: '#D99A17',
                'stroke-width': 2.3,
            });

            const title = sellerSvgElement(
                'title',
                {},
                `${point.label}: ${sellerPeso(point.value)}`
            );
            circle.appendChild(title);
            sellerSalesPoints.appendChild(circle);

            sellerSalesXAxis.appendChild(sellerSvgElement('text', {
                x: point.x,
                y: 244,
                fill: '#81786E',
                'font-size': count > 8 ? 8.8 : 9.8,
                'font-family': 'Poppins, sans-serif',
                'font-weight': 400,
                'text-anchor': 'middle',
            }, point.label));
        });

        sellerSalesEmptyState?.classList.toggle('hidden', total > 0);
    }

    sellerSalesPeriodButtons.forEach((button) => {
        button.addEventListener('click', function () {
            renderSellerSalesPerformance(this.dataset.salesPeriod || 'month');
        });
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

    const sellerCategoryPreviewData = @json($categoryBreakdownPreview);
    const sellerCategoryPalette = [
        '#d5a12b',
        '#2f4668',
        '#399783',
        '#d59a4a',
        '#7f68ab',
        '#5b83a8'
    ];

    let sellerSelectedCategory = '';

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
        const topName = document.getElementById('sellerCategoryTopName');
        const topPercent = document.getElementById('sellerCategoryTopPercent');
        const approved = document.getElementById('sellerCategoryApproved');
        const lowStock = document.getElementById('sellerCategoryLowStock');
        const categoryCount = document.getElementById('sellerCategoryCount');
        const viewLabel = document.getElementById('sellerCategoryViewProductsLabel');

        if (!donut || !legend) return;

        const data = buildSellerCategoryBreakdown(products);
        const top = data.categories[0] || null;

        if (approved) approved.textContent = data.approved.toLocaleString('en-PH');
        if (lowStock) lowStock.textContent = data.lowStock.toLocaleString('en-PH');
        if (categoryCount) categoryCount.textContent = data.uniqueCategories.toLocaleString('en-PH');
        if (topName) topName.textContent = top?.name || 'No listings yet';
        if (topPercent) topPercent.textContent = top ? sellerCategoryPercent(top.percentage) : '0%';

        if (!data.total || !data.categories.length) {
            donut.style.setProperty('--seller-category-gradient', 'conic-gradient(#e8e3dc 0deg 360deg)');
            legend.innerHTML = `
                <div class="col-span-2 rounded-[10px] border border-dashed border-[#e2d9cd] bg-[#fcfbf8] px-3 py-4 text-center">
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
                    class="seller-category-legend-button flex min-w-0 items-center justify-between gap-2 rounded-[10px] border border-[#e9e2d9] bg-white px-2.5 py-2 text-left"
                >
                    <span class="flex min-w-0 items-center gap-2">
                        <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background:${item.color}"></span>
                        <span class="truncate text-[9px] font-medium text-[#514a42]">${escapeHtml(item.name)}</span>
                    </span>
                    <span class="shrink-0 text-[9px] font-medium text-[#81786e]">${sellerCategoryPercent(item.percentage)}</span>
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

        productLibraryPromise = fetch(productLibraryUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            cache: 'no-store',
            signal: sellerDashboardEventSignal,
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Unable to load the Product Library.');
                }

                return response.json();
            })
            .then(function (data) {
                productLibraryPayload = Array.isArray(data?.products) ? data.products : [];
                renderSellerCategoryBreakdown(productLibraryPayload);
                return productLibraryPayload;
            })
            .finally(function () {
                productLibraryPromise = null;
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
                    <div class="mx-auto grid h-9 w-9 place-items-center rounded-[11px] bg-[#eee9e2]">
                        <span class="seller-skeleton-block h-4 w-4 rounded-full"></span>
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
    | Paint immediately from the already-rendered dashboard preview, then
    | silently replace it with the complete active catalog from the existing
    | Product Library endpoint. The request is shared with the modal, so it is
    | never downloaded twice.
    */
    renderSellerCategoryBreakdown(sellerCategoryPreviewData);

    fetchProductLibraryData().catch(function (error) {
        if (error?.name !== 'AbortError') {
            console.debug('SARI category breakdown is using preview data.');
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

}, { once: true });
</script>

@endpush