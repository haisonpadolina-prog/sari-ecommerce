@extends('layouts.seller')

@section('title', 'Seller Dashboard — SARI')
@section('page-title', 'Dashboard Overview')

@push('styles')
<link
    rel="stylesheet"
    href="https://cdn-uicons.flaticon.com/uicons-thin-straight/css/uicons-thin-straight.css"
>

{{-- Seller Dashboard design merged inline to avoid a separate public CSS request. --}}
<style>
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


    /*
    |--------------------------------------------------------------------------
    | DESKTOP 100% ZOOM — COMPACT PROPORTION PASS
    |--------------------------------------------------------------------------
    | Designed for normal browser zoom (100%). This is a targeted desktop
    | density pass, not transform:scale(), so text remains sharp and layout
    | measurements stay reliable. Mobile/tablet behavior remains responsive.
    */
    @media (min-width: 1024px) {
        #sellerDashboardStage {
            max-width: 1660px !important;
        }

        /* Tighten the main vertical rhythm a little. */
        #sellerDashboardContent > section.mt-4,
        #sellerAnalyticsRow,
        #sellerCommerceSideColumn {
            margin-top: 12px !important;
        }

        /* ---------------------------------------------------------
           GREETING HERO
           --------------------------------------------------------- */
        .seller-greeting-hero {
            border-radius: 17px !important;
            padding: 15px 20px !important;
        }

        .seller-greeting-hero > .relative {
            gap: 14px !important;
        }

        .seller-greeting-hero .xl\:gap-5 {
            gap: 15px !important;
        }

        .seller-greeting-icon-shell {
            width: 58px !important;
            height: 58px !important;
            flex: 0 0 58px !important;
            border-radius: 15px !important;
        }

        .seller-greeting-icon-shell svg {
            width: 23px !important;
            height: 23px !important;
        }

        #sellerGreetingText {
            gap: 6px !important;
            font-size: 21px !important;
            line-height: 1.15 !important;
        }

        #sellerGreetingText > span[aria-hidden="true"] {
            width: 27px !important;
            height: 27px !important;
            border-radius: 8px !important;
        }

        #sellerGreetingText .fi {
            font-size: 15px !important;
        }

        #sellerGreetingText + p {
            margin-top: 4px !important;
            font-size: 9.5px !important;
            line-height: 1.6 !important;
        }

        #sellerGreetingText ~ div {
            margin-top: 8px !important;
            gap: 6px !important;
        }

        .seller-greeting-chip {
            padding: 5px 10px !important;
            font-size: 7.5px !important;
        }

        .seller-greeting-stat {
            min-width: 106px !important;
            padding: 4px 12px !important;
        }

        .seller-greeting-stat > p {
            font-size: 6.5px !important;
        }

        .seller-greeting-stat > div {
            margin-top: 5px !important;
            gap: 7px !important;
        }

        .seller-greeting-stat > div > span {
            width: 31px !important;
            height: 31px !important;
        }

        .seller-greeting-stat > div > span svg {
            width: 14px !important;
            height: 14px !important;
        }

        #sellerCurrentTime {
            font-size: 17px !important;
        }

        #sellerCurrentDay,
        #sellerWeatherTemp {
            font-size: 15.5px !important;
        }

        #sellerTimeContext,
        #sellerCurrentDate,
        #sellerWeatherCondition {
            font-size: 7px !important;
        }

        .seller-greeting-hero a[href*="products"],
        .seller-greeting-hero a[href*="orders"] {
            height: 38px !important;
            padding-inline: 14px !important;
            border-radius: 10px !important;
            font-size: 9px !important;
        }

        .seller-greeting-hero a[href*="products"] svg,
        .seller-greeting-hero a[href*="orders"] svg {
            width: 14px !important;
            height: 14px !important;
        }

        /* ---------------------------------------------------------
           SUMMARY CARDS
           --------------------------------------------------------- */
        #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 {
            gap: 10px !important;
        }

        #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a {
            border-radius: 15px !important;
            padding: 12px 13px !important;
        }

        #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a > div {
            gap: 10px !important;
        }

        #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a p:first-child {
            font-size: 9.5px !important;
            line-height: 1.35 !important;
        }

        #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a p[class*="text-[22px]"] {
            margin-top: 7px !important;
            font-size: 20px !important;
        }

        #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a p[class*="text-[9.5px]"] {
            margin-top: 7px !important;
            font-size: 8.5px !important;
            line-height: 1.35 !important;
        }

        #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a > div > div:last-child {
            width: 38px !important;
            height: 38px !important;
            border-radius: 10px !important;
        }

        #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a > div > div:last-child svg {
            width: 16px !important;
            height: 16px !important;
        }

        /* ---------------------------------------------------------
           CATALOG + SALES ANALYTICS
           --------------------------------------------------------- */
        #sellerAnalyticsRow {
            gap: 11px !important;
        }

        #sellerCategoryBreakdownCard,
        #sellerSalesPerformanceCard {
            min-height: 338px !important;
            border-radius: 16px !important;
        }

        #sellerCategoryBreakdownCard > div:first-child {
            padding: 10px 12px !important;
        }

        #sellerCategoryBreakdownCard > div:first-child > div:first-child {
            gap: 8px !important;
        }

        #sellerCategoryBreakdownCard > div:first-child > div:first-child > span {
            width: 32px !important;
            height: 32px !important;
            border-radius: 9px !important;
        }

        #sellerCategoryBreakdownCard > div:first-child > div:first-child > span svg {
            width: 16px !important;
            height: 16px !important;
        }

        #sellerCategoryBreakdownCard > div:first-child h3,
        #sellerSalesPerformanceCard h3 {
            font-size: 16px !important;
            line-height: 1.25 !important;
        }

        #sellerCategoryBreakdownCard > div:first-child p,
        #sellerSalesPeriodLabel {
            margin-top: 3px !important;
            font-size: 9.5px !important;
            line-height: 1.45 !important;
        }

        #sellerCategoryBreakdownCard > div:first-child > span {
            padding: 5px 9px !important;
            font-size: 8px !important;
        }

        #sellerCategoryBreakdownCard > div:nth-child(2) {
            padding: 10px 12px !important;
            gap: 10px !important;
            grid-template-columns: minmax(0, 1.3fr) minmax(145px, .7fr) !important;
        }

        .seller-category-donut {
            width: min(100%, 170px) !important;
        }

        .seller-category-donut::before {
            inset: 27% !important;
        }

        .seller-category-donut-center {
            inset: 30% !important;
            gap: 2px !important;
            padding: 2px !important;
        }

        #sellerCategoryCenterLabel,
        #sellerCategoryCenterSub {
            max-width: 96px !important;
            font-size: 7.7px !important;
            line-height: 1.25 !important;
        }

        #sellerCategoryCenterValue {
            font-size: 23px !important;
            line-height: 1 !important;
        }

        #sellerCategoryLegend {
            margin-top: 7px !important;
            gap: 5px !important;
        }

        #sellerCategoryLegend .seller-category-legend-button {
            min-height: 28px !important;
            padding: 5px 7px !important;
            border-radius: 8px !important;
            font-size: 8px !important;
        }

        #sellerCategoryBreakdownCard .seller-category-stat-row {
            padding: 7px 8px !important;
        }

        #sellerCategoryBreakdownCard .seller-category-stat-row > div {
            gap: 6px !important;
        }

        #sellerCategoryBreakdownCard .seller-category-stat-row > div > span {
            width: 27px !important;
            height: 27px !important;
            border-radius: 8px !important;
        }

        #sellerCategoryBreakdownCard .seller-category-stat-row > div > span svg {
            width: 12px !important;
            height: 12px !important;
        }

        #sellerCategoryBreakdownCard .seller-category-stat-row p {
            font-size: 8px !important;
        }

        #sellerCategoryBreakdownCard .seller-category-stat-row strong,
        #sellerCategoryTopPercent {
            font-size: 11px !important;
        }

        #sellerCategoryBreakdownCard > div:last-child {
            padding: 8px 11px !important;
        }

        #sellerCategoryBreakdownCard > div:last-child > p {
            font-size: 8px !important;
            line-height: 1.35 !important;
        }

        #sellerCategoryViewProducts {
            height: 31px !important;
            min-height: 31px !important;
            padding-inline: 10px !important;
            border-radius: 8px !important;
            font-size: 8.5px !important;
        }

        /* Sales card */
        #sellerSalesPerformanceCard {
            padding: 11px 12px !important;
        }

        #sellerSalesPerformanceCard > div:first-child {
            gap: 7px !important;
        }

        .seller-sales-period-button {
            min-height: 30px !important;
            padding: 6px 9px !important;
            border-radius: 7px !important;
            font-size: 8.7px !important;
        }

        #sellerSalesPerformanceCard > div:nth-child(2) {
            margin-top: 8px !important;
            border-radius: 9px !important;
        }

        #sellerSalesPerformanceCard > div:nth-child(2) > div {
            padding: 6px 8px !important;
        }

        #sellerSalesPerformanceCard > div:nth-child(2) p:first-child {
            font-size: 7.5px !important;
            line-height: 1.25 !important;
        }

        #sellerSalesPerformanceCard > div:nth-child(2) p:last-child {
            margin-top: 3px !important;
            font-size: 13px !important;
            line-height: 1.15 !important;
        }

        #sellerSalesPerformanceCard > div:nth-child(3) {
            margin-top: 8px !important;
            padding: 4px 6px !important;
            border-radius: 10px !important;
        }

        #sellerSalesChart {
            height: 145px !important;
        }

        #sellerSalesEmptyState p:first-child {
            font-size: 9px !important;
        }

        #sellerSalesEmptyState p:last-child {
            margin-top: 3px !important;
            font-size: 8px !important;
        }

        #sellerSalesPerformanceCard > div:last-child {
            margin-top: 6px !important;
        }

        #sellerSalesBestPeriod,
        #sellerSalesPerformanceCard > div:last-child > p:last-child {
            font-size: 7.8px !important;
            line-height: 1.35 !important;
        }

        /* ---------------------------------------------------------
           FINANCIAL / ORDERS / INVENTORY
           --------------------------------------------------------- */
        #sellerCommerceSideColumn {
            gap: 10px !important;
        }

        #sellerCommerceSideColumn > .seller-side-panel {
            min-height: 214px !important;
            padding: 10px !important;
            border-radius: 13px !important;
        }

        #sellerCommerceSideColumn .seller-panel-title {
            font-size: 14px !important;
            line-height: 1.2 !important;
        }

        #sellerCommerceSideColumn .seller-side-panel > div:first-child p {
            margin-top: 2px !important;
            font-size: 8.5px !important;
            line-height: 1.35 !important;
        }

        #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2),
        #sellerCommerceSideColumn .seller-inventory-panel > div:nth-child(2) {
            margin-top: 6px !important;
            gap: 4px !important;
        }

        #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div {
            padding: 5px 6px !important;
            border-radius: 8px !important;
        }

        #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div > div {
            gap: 6px !important;
        }

        #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div > div > div:first-child {
            width: 25px !important;
            height: 25px !important;
            border-radius: 7px !important;
        }

        #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) p:first-child {
            font-size: 7.5px !important;
        }

        #sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) p:last-child {
            margin-top: 1px !important;
            font-size: 12px !important;
        }

        #sellerCommerceSideColumn .seller-order-panel > div:nth-child(2) {
            margin-top: 6px !important;
            gap: 4px !important;
        }

        #sellerCommerceSideColumn .seller-order-row {
            min-height: 32px !important;
            padding: 5px 7px !important;
            border-radius: 7px !important;
        }

        #sellerCommerceSideColumn .seller-order-row span:first-child {
            font-size: 8.5px !important;
        }

        #sellerCommerceSideColumn .seller-order-row span:last-child {
            min-width: 20px !important;
            min-height: 20px !important;
            height: 20px !important;
            padding-inline: 5px !important;
            border-radius: 6px !important;
            font-size: 7.5px !important;
        }

        #sellerCommerceSideColumn .seller-inventory-row {
            min-height: 37px !important;
            padding: 6px 7px !important;
            border-radius: 7px !important;
        }

        #sellerCommerceSideColumn .seller-inventory-row p:first-child {
            font-size: 8.8px !important;
        }

        #sellerCommerceSideColumn .seller-inventory-row p + p,
        #sellerCommerceSideColumn .seller-inventory-count {
            margin-top: 1px !important;
            font-size: 7.6px !important;
        }

        #sellerCommerceSideColumn .seller-inventory-panel > div:first-child > .seller-inventory-count {
            padding: 4px 7px !important;
            font-size: 7.8px !important;
        }

        #sellerCommerceSideColumn .seller-order-view-all,
        #sellerCommerceSideColumn .seller-inventory-button,
        #sellerCommerceSideColumn .seller-financial-panel a[href*="reports"] {
            min-height: 32px !important;
            height: 32px !important;
            border-radius: 7px !important;
            font-size: 8.5px !important;
        }

        #sellerCommerceSideColumn .seller-side-panel > .mt-auto {
            padding-top: 6px !important;
        }

        }

    /*
     * Slightly denser treatment for common laptop heights at 100% zoom.
     * This reduces vertical scrolling without shrinking typography too far.
     */
    @media (min-width: 1024px) and (max-height: 820px) {
        .seller-greeting-hero {
            padding-top: 13px !important;
            padding-bottom: 13px !important;
        }

        #sellerCategoryBreakdownCard,
        #sellerSalesPerformanceCard {
            min-height: 318px !important;
        }

        .seller-category-donut {
            width: min(100%, 158px) !important;
        }

        #sellerSalesChart {
            height: 132px !important;
        }

        #sellerCommerceSideColumn > .seller-side-panel {
            min-height: 202px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER GREETING — FLATICON HAND WAVE
    |--------------------------------------------------------------------------
    | Clean Flaticon Uicons Thin Straight hand wave beside the greeting.
    */
    .seller-greeting-wave {
        display: inline-flex !important;
        width: 24px !important;
        height: 24px !important;
        flex: 0 0 24px !important;
        align-items: center !important;
        justify-content: center !important;
        align-self: center !important;
        margin-left: 1px !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        color: #efb43a !important;
        line-height: 1 !important;
        position: relative !important;
        top: 1px !important;
        overflow: visible !important;
    }

    .seller-greeting-wave .fi {
        display: inline-flex !important;
        width: 24px !important;
        height: 24px !important;
        flex: 0 0 24px !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 !important;
        padding: 0 !important;
        font-size: 22px !important;
        font-style: normal !important;
        line-height: 1 !important;
        transform: none !important;
    }

    .seller-greeting-wave .fi::before {
        display: block !important;
        margin: 0 !important;
        line-height: 1 !important;
    }

    @media (min-width: 1024px) {
        #sellerGreetingText .seller-greeting-wave {
            width: 22px !important;
            height: 22px !important;
            flex-basis: 22px !important;
        }

        #sellerGreetingText .seller-greeting-wave .fi {
            width: 22px !important;
            height: 22px !important;
            flex-basis: 22px !important;
            font-size: 20px !important;
        }
    }

    @media (max-width: 639px) {
        .seller-greeting-wave {
            width: 22px !important;
            height: 22px !important;
            flex-basis: 22px !important;
        }

        .seller-greeting-wave .fi {
            width: 22px !important;
            height: 22px !important;
            flex-basis: 22px !important;
            font-size: 20px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER SALES PERFORMANCE — ADMIN GRAPH VISUAL SYSTEM
    |--------------------------------------------------------------------------
    | Mirrors the Admin Sales Overview language while preserving Seller data.
    | Compact for 100% browser zoom.
    */
    #sellerSalesPerformanceCard.seller-sales-admin-card {
        min-width: 0 !important;
        min-height: 0 !important;
        border: 1px solid #e3dbcf !important;
        border-radius: 16px !important;
        background: linear-gradient(145deg, #ffffff 0%, #fdfbf7 100%) !important;
        padding: 14px 15px 12px !important;
        box-shadow:
            0 16px 34px rgba(37,29,20,.078),
            0 5px 12px rgba(37,29,20,.036),
            inset 0 1px 0 rgba(255,255,255,.96),
            inset 0 -1px 0 rgba(230,223,213,.68) !important;
        overflow: visible !important;
        transform: none !important;
    }

    #sellerSalesPerformanceCard.seller-sales-admin-card:hover {
        border-color: #e3dbcf !important;
        box-shadow:
            0 16px 34px rgba(37,29,20,.078),
            0 5px 12px rgba(37,29,20,.036),
            inset 0 1px 0 rgba(255,255,255,.96),
            inset 0 -1px 0 rgba(230,223,213,.68) !important;
        transform: none !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-head {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 14px !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-title {
        margin: 0 !important;
        font-size: 16px !important;
        line-height: 1.25 !important;
        font-weight: 700 !important;
        letter-spacing: -.025em !important;
        color: #211d17 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-subtitle {
        margin: 3px 0 0 !important;
        font-size: 8.8px !important;
        line-height: 1.45 !important;
        font-weight: 400 !important;
        color: #978f84 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-periods {
        display: inline-flex !important;
        width: auto !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        gap: 2px !important;
        border: 1px solid #e7e0d7 !important;
        border-radius: 10px !important;
        background: #faf8f4 !important;
        padding: 2px !important;
        box-shadow: inset 0 1px 2px rgba(61,47,31,.025) !important;
    }

    #sellerSalesPerformanceCard .seller-sales-period-button {
        min-height: 29px !important;
        height: 29px !important;
        border: 0 !important;
        border-radius: 8px !important;
        background: transparent !important;
        padding: 0 9px !important;
        font-size: 8.3px !important;
        line-height: 1 !important;
        font-weight: 550 !important;
        color: #8b8277 !important;
        box-shadow: none !important;
        transform: none !important;
        transition:
            background-color .18s ease,
            color .18s ease,
            box-shadow .18s ease !important;
        white-space: nowrap !important;
    }

    #sellerSalesPerformanceCard .seller-sales-period-button:hover {
        background: transparent !important;
        color: #5e554c !important;
    }

    #sellerSalesPerformanceCard .seller-sales-period-button.is-active,
    #sellerSalesPerformanceCard .seller-sales-period-button[aria-pressed="true"] {
        background: #ffffff !important;
        color: #302a24 !important;
        font-weight: 700 !important;
        box-shadow: 0 2px 7px rgba(57,44,28,.07) !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metrics {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0,1fr)) !important;
        margin-top: 13px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        overflow: visible !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric {
        min-width: 0 !important;
        padding: 4px 14px 9px !important;
        border: 0 !important;
        background: transparent !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric:first-child {
        padding-left: 0 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric:last-child {
        padding-right: 0 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric + .seller-sales-admin-metric {
        border-left: 1px solid #f0ebe5 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric-label {
        margin: 0 !important;
        font-size: 8px !important;
        line-height: 1.3 !important;
        font-weight: 500 !important;
        letter-spacing: 0 !important;
        text-transform: none !important;
        color: #7f776e !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric-value {
        margin: 4px 0 0 !important;
        font-size: 20px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: -.04em !important;
        color: #211d17 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric-note {
        margin: 6px 0 0 !important;
        font-size: 7.5px !important;
        line-height: 1.35 !important;
        font-weight: 500 !important;
        color: #9a9288 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-divider {
        height: 1px !important;
        margin-top: 2px !important;
        background: #f0ebe5 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-chart-head {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 12px !important;
        padding: 11px 0 4px !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-chart-title {
        margin: 0 !important;
        font-size: 10px !important;
        line-height: 1.3 !important;
        font-weight: 700 !important;
        color: #302a24 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-chart-copy {
        margin: 2px 0 0 !important;
        font-size: 7.5px !important;
        line-height: 1.4 !important;
        color: #9a9288 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-legend {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        padding-top: 1px !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-legend-item {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        font-size: 8px !important;
        line-height: 1 !important;
        font-weight: 500 !important;
        color: #746c63 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-legend-item > i {
        display: block !important;
        width: 7px !important;
        height: 7px !important;
        border-radius: 50% !important;
        background: #c79229 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-chart-shell {
        position: relative !important;
        min-height: 176px !important;
        margin-top: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        overflow: visible !important;
        box-shadow: none !important;
    }

    #sellerSalesPerformanceCard #sellerSalesChart {
        display: block !important;
        width: 100% !important;
        height: 176px !important;
        overflow: visible !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-line {
        fill: none !important;
        stroke: #c79229 !important;
        stroke-width: 2.3 !important;
        stroke-linecap: round !important;
        stroke-linejoin: round !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-area {
        opacity: 1;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-tooltip {
        position: absolute !important;
        z-index: 12 !important;
        min-width: 128px !important;
        border: 1px solid #e7dfd6 !important;
        border-radius: 10px !important;
        background: rgba(255,255,255,.98) !important;
        padding: 8px 9px !important;
        box-shadow: 0 10px 24px rgba(57,44,28,.10) !important;
        opacity: 0 !important;
        transform: translateY(5px) !important;
        pointer-events: none !important;
        transition: opacity .12s ease, transform .12s ease !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-tooltip.is-visible {
        opacity: 1 !important;
        transform: translateY(0) !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-tooltip-period {
        margin: 0 0 6px !important;
        font-size: 7.5px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        color: #756c62 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-tooltip-row {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        margin: 0 !important;
        font-size: 7.5px !important;
        color: #8a8178 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-tooltip-row span {
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-tooltip-row span > i {
        display: block !important;
        width: 6px !important;
        height: 6px !important;
        border-radius: 50% !important;
        background: #c79229 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-tooltip-row strong {
        font-size: 8px !important;
        color: #2b261f !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-empty {
        position: absolute !important;
        inset: 50% 16px auto !important;
        display: flex;
        align-items: center !important;
        justify-content: center !important;
        gap: 10px !important;
        transform: translateY(-50%) !important;
        pointer-events: none !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-empty.hidden {
        display: none !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-empty-icon {
        display: grid !important;
        width: 31px !important;
        height: 31px !important;
        flex: 0 0 31px !important;
        place-items: center !important;
        border-radius: 50% !important;
        background: #fff4da !important;
        color: #b67d18 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-empty-icon svg {
        width: 15px !important;
        height: 15px !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-empty p {
        margin: 0 !important;
        font-size: 8.5px !important;
        line-height: 1.3 !important;
        font-weight: 700 !important;
        color: #302a24 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-empty span:not(.seller-sales-admin-empty-icon) {
        display: block !important;
        margin-top: 2px !important;
        font-size: 7.4px !important;
        line-height: 1.35 !important;
        color: #928a80 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-footer {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 7px 12px !important;
        margin-top: 2px !important;
        padding-top: 8px !important;
        border-top: 1px solid #f2ede7 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-footer p {
        margin: 0 !important;
        font-size: 7.5px !important;
        line-height: 1.35 !important;
        font-weight: 500 !important;
        color: #918980 !important;
    }

    #sellerSalesPerformanceCard #sellerSalesBestPeriod {
        color: #756c62 !important;
    }

    @media (max-width: 639px) {
        #sellerSalesPerformanceCard.seller-sales-admin-card {
            padding: 13px !important;
        }

        #sellerSalesPerformanceCard .seller-sales-admin-head {
            flex-direction: column !important;
        }

        #sellerSalesPerformanceCard .seller-sales-admin-periods {
            width: 100% !important;
        }

        #sellerSalesPerformanceCard .seller-sales-period-button {
            flex: 1 1 0 !important;
        }

        #sellerSalesPerformanceCard .seller-sales-admin-metrics {
            grid-template-columns: 1fr !important;
        }

        #sellerSalesPerformanceCard .seller-sales-admin-metric {
            padding: 8px 0 !important;
        }

        #sellerSalesPerformanceCard .seller-sales-admin-metric + .seller-sales-admin-metric {
            border-left: 0 !important;
            border-top: 1px solid #f0ebe5 !important;
        }

        #sellerSalesPerformanceCard .seller-sales-admin-chart-head {
            flex-direction: column !important;
        }

        #sellerSalesPerformanceCard #sellerSalesChart {
            height: 190px !important;
        }

        #sellerSalesPerformanceCard .seller-sales-admin-footer {
            align-items: flex-start !important;
            flex-direction: column !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #sellerSalesPerformanceCard .seller-sales-period-button,
        #sellerSalesPerformanceCard .seller-sales-admin-tooltip {
            transition: none !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER SALES — TYPOGRAPHY REFINEMENT
    |--------------------------------------------------------------------------
    | Smaller/lighter Poppins for metric labels and helper copy.
    */
    #sellerSalesPerformanceCard,
    #sellerSalesPerformanceCard * {
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric-label {
        font-size: 7.3px !important;
        line-height: 1.25 !important;
        font-weight: 450 !important;
        letter-spacing: .01em !important;
        color: #857d74 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric-note {
        margin-top: 5px !important;
        font-size: 6.8px !important;
        line-height: 1.35 !important;
        font-weight: 400 !important;
        letter-spacing: 0 !important;
        color: #a19a91 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric-value {
        font-size: 18px !important;
        font-weight: 600 !important;
        letter-spacing: -.035em !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-chart-copy {
        font-size: 7px !important;
        font-weight: 400 !important;
        color: #9f978e !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-footer p,
    #sellerSalesPerformanceCard #sellerSalesBestPeriod {
        font-size: 7px !important;
        font-weight: 400 !important;
        color: #958d84 !important;
    }

    @media (max-width: 639px) {
        #sellerSalesPerformanceCard .seller-sales-admin-metric-label {
            font-size: 7.2px !important;
        }

        #sellerSalesPerformanceCard .seller-sales-admin-metric-note {
            font-size: 6.8px !important;
        }

        #sellerSalesPerformanceCard .seller-sales-admin-metric-value {
            font-size: 17px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER SALES — TYPOGRAPHY REFINEMENT V2
    |--------------------------------------------------------------------------
    | Smaller supporting text, larger chart section title.
    */
    #sellerSalesPerformanceCard .seller-sales-admin-metric-label {
        font-size: 6.7px !important;
        line-height: 1.2 !important;
        font-weight: 400 !important;
        letter-spacing: .01em !important;
        color: #8d857c !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric-note {
        margin-top: 4px !important;
        font-size: 6.2px !important;
        line-height: 1.3 !important;
        font-weight: 400 !important;
        letter-spacing: 0 !important;
        color: #a59e95 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric-value {
        font-size: 17px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
        letter-spacing: -.035em !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-chart-title {
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: 13px !important;
        line-height: 1.25 !important;
        font-weight: 600 !important;
        letter-spacing: -.018em !important;
        color: #2d2721 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-chart-copy {
        margin-top: 3px !important;
        font-size: 6.6px !important;
        line-height: 1.35 !important;
        font-weight: 400 !important;
        color: #a19a91 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-legend-item {
        font-size: 7.2px !important;
        font-weight: 400 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-footer p,
    #sellerSalesPerformanceCard #sellerSalesBestPeriod {
        font-size: 6.5px !important;
        line-height: 1.3 !important;
        font-weight: 400 !important;
    }

    @media (max-width: 639px) {
        #sellerSalesPerformanceCard .seller-sales-admin-metric-label {
            font-size: 6.6px !important;
        }

        #sellerSalesPerformanceCard .seller-sales-admin-metric-note {
            font-size: 6.1px !important;
        }

        #sellerSalesPerformanceCard .seller-sales-admin-metric-value {
            font-size: 16px !important;
        }

        #sellerSalesPerformanceCard .seller-sales-admin-chart-title {
            font-size: 12px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER SALES — VISIBLE NUMBERS + LARGER GRAPH
    |--------------------------------------------------------------------------
    | Removes secondary KPI copy and gives the key values/chart more presence.
    */
    #sellerSalesPerformanceCard .seller-sales-admin-metrics {
        margin-top: 14px !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric {
        padding-top: 5px !important;
        padding-bottom: 11px !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric-label {
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: 7.2px !important;
        line-height: 1.2 !important;
        font-weight: 450 !important;
        color: #877f76 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-metric-value {
        margin-top: 7px !important;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: 23px !important;
        line-height: 1 !important;
        font-weight: 650 !important;
        letter-spacing: -.042em !important;
        color: #211d17 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-chart-head {
        padding-top: 13px !important;
        padding-bottom: 6px !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-chart-title {
        font-size: 14px !important;
        font-weight: 600 !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-chart-shell {
        min-height: 222px !important;
    }

    #sellerSalesPerformanceCard #sellerSalesChart {
        height: 222px !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-footer {
        margin-top: 4px !important;
        padding-top: 9px !important;
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        #sellerSalesPerformanceCard .seller-sales-admin-metric-value {
            font-size: 22px !important;
        }

        #sellerSalesPerformanceCard .seller-sales-admin-chart-shell {
            min-height: 205px !important;
        }

        #sellerSalesPerformanceCard #sellerSalesChart {
            height: 205px !important;
        }
    }

    @media (max-width: 639px) {
        #sellerSalesPerformanceCard .seller-sales-admin-metric-value {
            font-size: 21px !important;
        }

        #sellerSalesPerformanceCard .seller-sales-admin-chart-shell {
            min-height: 210px !important;
        }

        #sellerSalesPerformanceCard #sellerSalesChart {
            height: 210px !important;
        }
    }


    /* ============================================================
       SELLER CATEGORY BREAKDOWN — EXECUTIVE CLEAN V2
       Donut + flat ranked category distribution only.
       ============================================================ */
    #sellerCategoryBreakdownCard .seller-category-executive-body {
        display:grid !important;
        grid-template-columns:182px minmax(0,1fr) !important;
        align-items:center !important;
        gap:26px !important;
        padding:18px 18px 16px !important;
    }

    #sellerCategoryBreakdownCard .seller-category-executive-donut {
        display:flex !important;
        align-items:center !important;
        justify-content:center !important;
        min-width:0 !important;
    }

    #sellerCategoryBreakdownCard .seller-category-donut {
        width:158px !important;
        height:158px !important;
        flex:0 0 158px !important;
        box-shadow:0 10px 22px rgba(58,45,29,.065),0 3px 8px rgba(58,45,29,.035) !important;
    }

    #sellerCategoryBreakdownCard .seller-category-donut::before {
        inset:29px !important;
        border:1px solid #eee7dd !important;
        background:linear-gradient(145deg,#fff 0%,#fffdf9 100%) !important;
        box-shadow:0 4px 10px rgba(58,45,29,.035),inset 0 1px 0 rgba(255,255,255,.95) !important;
    }

    #sellerCategoryBreakdownCard .seller-category-donut-center {
        inset:31% !important;
        gap:0 !important;
        padding:0 !important;
    }

    #sellerCategoryBreakdownCard .seller-category-center-label {
        max-width:92px !important;
        overflow:hidden !important;
        text-overflow:ellipsis !important;
        white-space:nowrap !important;
        font-family:"Poppins",ui-sans-serif,system-ui,sans-serif !important;
        font-size:6.8px !important;
        line-height:1.25 !important;
        font-weight:500 !important;
        letter-spacing:.08em !important;
        text-transform:uppercase !important;
        color:#9a9289 !important;
    }

    #sellerCategoryBreakdownCard .seller-category-center-value {
        margin-top:6px !important;
        font-family:"Poppins",ui-sans-serif,system-ui,sans-serif !important;
        font-size:27px !important;
        line-height:.95 !important;
        font-weight:650 !important;
        letter-spacing:-.05em !important;
        color:#211d17 !important;
    }

    #sellerCategoryBreakdownCard .seller-category-center-sub {
        max-width:96px !important;
        margin-top:6px !important;
        font-size:7px !important;
        line-height:1.35 !important;
        font-weight:400 !important;
        color:#938b82 !important;
        text-align:center !important;
    }

    #sellerCategoryBreakdownCard .seller-category-executive-list { min-width:0 !important; }

    #sellerCategoryBreakdownCard .seller-category-executive-list-head {
        padding-bottom:8px !important;
        border-bottom:1px solid #f0ebe4 !important;
    }

    #sellerCategoryBreakdownCard .seller-category-executive-kicker {
        margin:0 !important;
        font-size:6.5px !important;
        line-height:1.2 !important;
        font-weight:600 !important;
        letter-spacing:.10em !important;
        text-transform:uppercase !important;
        color:#b07a1b !important;
    }

    #sellerCategoryBreakdownCard .seller-category-executive-title {
        margin:3px 0 0 !important;
        font-size:10.5px !important;
        line-height:1.3 !important;
        font-weight:600 !important;
        letter-spacing:-.015em !important;
        color:#302a24 !important;
    }

    #sellerCategoryBreakdownCard .seller-category-ranked-list {
        display:block !important;
        margin-top:4px !important;
    }

    #sellerCategoryBreakdownCard .seller-category-ranked-row {
        --seller-category-row-color:#c99524;
        display:block !important;
        width:100% !important;
        min-height:44px !important;
        border:0 !important;
        border-bottom:1px solid #f2ede7 !important;
        border-radius:0 !important;
        background:transparent !important;
        padding:8px 0 9px !important;
        text-align:left !important;
        cursor:pointer !important;
        box-shadow:none !important;
        transform:none !important;
        transition:opacity .14s ease !important;
    }

    #sellerCategoryBreakdownCard .seller-category-ranked-row:last-child { border-bottom:0 !important; }

    #sellerCategoryBreakdownCard .seller-category-ranked-row:hover,
    #sellerCategoryBreakdownCard .seller-category-ranked-row.is-active {
        background:transparent !important;
        box-shadow:none !important;
        transform:none !important;
    }

    #sellerCategoryBreakdownCard .seller-category-ranked-row:hover { opacity:.82 !important; }

    #sellerCategoryBreakdownCard .seller-category-ranked-top {
        display:flex !important;
        align-items:center !important;
        justify-content:space-between !important;
        gap:12px !important;
    }

    #sellerCategoryBreakdownCard .seller-category-ranked-name {
        display:flex !important;
        min-width:0 !important;
        align-items:center !important;
        gap:8px !important;
        font-family:"Poppins",ui-sans-serif,system-ui,sans-serif !important;
        font-size:8.3px !important;
        line-height:1.25 !important;
        font-weight:500 !important;
        color:#4c453e !important;
    }

    #sellerCategoryBreakdownCard .seller-category-ranked-name > i {
        display:block !important;
        width:7px !important;
        height:7px !important;
        flex:0 0 7px !important;
        border-radius:50% !important;
        background:var(--seller-category-row-color) !important;
    }

    #sellerCategoryBreakdownCard .seller-category-ranked-name > span {
        min-width:0 !important;
        overflow:hidden !important;
        text-overflow:ellipsis !important;
        white-space:nowrap !important;
    }

    #sellerCategoryBreakdownCard .seller-category-ranked-top > strong {
        flex:0 0 auto !important;
        font-family:"Poppins",ui-sans-serif,system-ui,sans-serif !important;
        font-size:8.5px !important;
        line-height:1 !important;
        font-weight:600 !important;
        color:#5e564e !important;
    }

    #sellerCategoryBreakdownCard .seller-category-ranked-track {
        display:block !important;
        height:4px !important;
        margin-top:7px !important;
        overflow:hidden !important;
        border-radius:999px !important;
        background:#f0ece6 !important;
    }

    #sellerCategoryBreakdownCard .seller-category-ranked-track > span {
        display:block !important;
        height:100% !important;
        border-radius:inherit !important;
        background:var(--seller-category-row-color) !important;
        transition:width .28s cubic-bezier(.22,1,.36,1) !important;
    }

    #sellerCategoryBreakdownCard .seller-category-ranked-empty {
        margin-top:10px !important;
        border:1px dashed #e2d9cd !important;
        border-radius:10px !important;
        background:#fcfbf8 !important;
        padding:14px !important;
        text-align:center !important;
    }

    #sellerCategoryBreakdownCard .seller-category-executive-footer {
        display:flex !important;
        align-items:center !important;
        justify-content:space-between !important;
        gap:12px !important;
        border-top:1px solid #eee8df !important;
        background:#fffefa !important;
        padding:10px 18px !important;
    }

    #sellerCategoryBreakdownCard .seller-category-executive-footer > p {
        margin:0 !important;
        font-size:7.2px !important;
        line-height:1.4 !important;
        font-weight:400 !important;
        color:#918980 !important;
    }

    #sellerCategoryBreakdownCard .seller-category-executive-action {
        display:inline-flex !important;
        min-height:30px !important;
        flex:0 0 auto !important;
        align-items:center !important;
        justify-content:center !important;
        gap:6px !important;
        border:0 !important;
        background:transparent !important;
        padding:0 !important;
        font-family:"Poppins",ui-sans-serif,system-ui,sans-serif !important;
        font-size:7.8px !important;
        line-height:1 !important;
        font-weight:600 !important;
        color:#a56f16 !important;
        box-shadow:none !important;
        cursor:pointer !important;
    }

    #sellerCategoryBreakdownCard .seller-category-executive-action svg {
        width:12px !important;
        height:12px !important;
        transition:transform .14s ease !important;
    }

    #sellerCategoryBreakdownCard .seller-category-executive-action:hover svg { transform:translateX(2px) !important; }

    @media (min-width:1024px) and (max-height:820px) {
        #sellerCategoryBreakdownCard .seller-category-executive-body {
            grid-template-columns:164px minmax(0,1fr) !important;
            gap:20px !important;
            padding:14px 16px 13px !important;
        }
        #sellerCategoryBreakdownCard .seller-category-donut {
            width:146px !important;
            height:146px !important;
            flex-basis:146px !important;
        }
        #sellerCategoryBreakdownCard .seller-category-donut::before { inset:27px !important; }
        #sellerCategoryBreakdownCard .seller-category-ranked-row {
            min-height:40px !important;
            padding:7px 0 8px !important;
        }
    }

    @media (max-width:767px) {
        #sellerCategoryBreakdownCard .seller-category-executive-body {
            grid-template-columns:1fr !important;
            gap:18px !important;
            padding:16px !important;
        }
        #sellerCategoryBreakdownCard .seller-category-donut {
            width:156px !important;
            height:156px !important;
            flex-basis:156px !important;
        }
        #sellerCategoryBreakdownCard .seller-category-executive-footer {
            align-items:flex-start !important;
            flex-direction:column !important;
            padding:11px 16px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER DASHBOARD — PERFORMANCE / SMOOTHNESS PASS
    |--------------------------------------------------------------------------
    | No visual redesign. Removes persistent compositor hints where they are
    | unnecessary and isolates the SVG chart paint area.
    */
    #sellerDashboardContent {
        opacity: 1 !important;
        visibility: visible !important;
        transform: none !important;
        pointer-events: auto !important;
    }

    #sellerSalesPerformanceCard .seller-sales-admin-chart-shell {
        contain: layout paint;
    }

    #sellerAddProductModal > :first-child,
    #sellerAllProductsModal > :first-child,
    #sellerViewProductModal > :first-child,
    #sellerEditProductModal > :first-child,
    #sellerProductActionModal > :first-child {
        will-change: auto !important;
    }

    #sellerAddProductModal.seller-modal-visible > :first-child,
    #sellerAllProductsModal.seller-modal-visible > :first-child,
    #sellerViewProductModal.seller-modal-visible > :first-child,
    #sellerEditProductModal.seller-modal-visible > :first-child,
    #sellerProductActionModal.seller-modal-visible > :first-child {
        will-change: opacity, transform !important;
    }


    /* ============================================================
       CATALOG CATEGORY — PREMIUM LARGE DONUT V3
       Larger visual anchor, restrained depth, no expensive blur/filter.
       ============================================================ */
    #sellerCategoryBreakdownCard .seller-category-executive-body {
        grid-template-columns:232px minmax(0,1fr) !important;
        gap:30px !important;
        padding:20px 20px 18px !important;
    }

    #sellerCategoryBreakdownCard .seller-category-executive-donut {
        min-height:220px !important;
    }

    #sellerCategoryBreakdownCard .seller-category-donut {
        width:208px !important;
        height:208px !important;
        flex:0 0 208px !important;
        isolation:isolate !important;
        border:1px solid rgba(218,207,191,.58) !important;
        background:var(--seller-category-gradient) !important;
        box-shadow:
            0 18px 36px rgba(58,45,29,.085),
            0 5px 12px rgba(58,45,29,.045),
            inset 0 1px 0 rgba(255,255,255,.28) !important;
    }

    #sellerCategoryBreakdownCard .seller-category-donut::before {
        z-index:2 !important;
        inset:42px !important;
        border:1px solid #e8dfd2 !important;
        background:linear-gradient(145deg,#ffffff 0%,#fffdf8 100%) !important;
        box-shadow:
            0 8px 18px rgba(58,45,29,.065),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    #sellerCategoryBreakdownCard .seller-category-donut::after {
        content:"" !important;
        position:absolute !important;
        z-index:1 !important;
        inset:0 !important;
        border-radius:inherit !important;
        pointer-events:none !important;
        background:
            radial-gradient(circle at 30% 24%,rgba(255,255,255,.20),transparent 29%),
            radial-gradient(circle at 70% 78%,rgba(54,41,26,.045),transparent 40%) !important;
    }

    #sellerCategoryBreakdownCard .seller-category-donut-center {
        z-index:3 !important;
        inset:27% !important;
    }

    #sellerCategoryBreakdownCard .seller-category-center-label {
        max-width:112px !important;
        font-size:7.4px !important;
        letter-spacing:.10em !important;
    }

    #sellerCategoryBreakdownCard .seller-category-center-value {
        margin-top:7px !important;
        font-size:34px !important;
        font-weight:650 !important;
    }

    #sellerCategoryBreakdownCard .seller-category-center-sub {
        max-width:112px !important;
        margin-top:7px !important;
        font-size:7.4px !important;
    }

    @media (min-width:1024px) and (max-height:820px) {
        #sellerCategoryBreakdownCard .seller-category-executive-body {
            grid-template-columns:205px minmax(0,1fr) !important;
            gap:24px !important;
            padding:16px 18px 15px !important;
        }

        #sellerCategoryBreakdownCard .seller-category-executive-donut {
            min-height:194px !important;
        }

        #sellerCategoryBreakdownCard .seller-category-donut {
            width:184px !important;
            height:184px !important;
            flex-basis:184px !important;
        }

        #sellerCategoryBreakdownCard .seller-category-donut::before {
            inset:37px !important;
        }

        #sellerCategoryBreakdownCard .seller-category-center-value {
            font-size:31px !important;
        }
    }

    @media (max-width:767px) {
        #sellerCategoryBreakdownCard .seller-category-executive-body {
            grid-template-columns:1fr !important;
            gap:20px !important;
            padding:18px 16px 16px !important;
        }

        #sellerCategoryBreakdownCard .seller-category-executive-donut {
            min-height:192px !important;
        }

        #sellerCategoryBreakdownCard .seller-category-donut {
            width:182px !important;
            height:182px !important;
            flex-basis:182px !important;
        }

        #sellerCategoryBreakdownCard .seller-category-donut::before {
            inset:36px !important;
        }

        #sellerCategoryBreakdownCard .seller-category-center-value {
            font-size:30px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER GREETING — CLEAN TALL HERO V2
    |--------------------------------------------------------------------------
    | Time / Date / Weather are text-only. The welcome hero gets more vertical
    | breathing room while keeping the same live data and actions.
    */
    .seller-greeting-hero {
        min-height: 176px !important;
        padding: 24px 24px !important;
        display: flex !important;
        align-items: center !important;
    }

    .seller-greeting-hero > .relative {
        width: 100% !important;
        align-items: center !important;
    }

    .seller-greeting-hero .seller-greeting-meta {
        min-width: 0 !important;
        gap: 0 !important;
        align-items: stretch !important;
    }

    .seller-greeting-hero .seller-greeting-stat {
        min-width: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        padding: 8px 22px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-greeting-hero .seller-greeting-stat:first-child {
        padding-left: 4px !important;
    }

    .seller-greeting-hero .seller-greeting-stat + .seller-greeting-stat {
        border-left: 1px solid rgba(255,255,255,.11) !important;
    }

    .seller-greeting-hero .seller-greeting-stat-label {
        margin: 0 !important;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: 6.8px !important;
        line-height: 1.2 !important;
        font-weight: 550 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
        color: rgba(255,255,255,.46) !important;
    }

    .seller-greeting-hero .seller-greeting-stat-copy {
        display: block !important;
        margin-top: 9px !important;
        padding-left: 20px !important;
    }

    .seller-greeting-hero .seller-greeting-stat-value {
        margin: 0 !important;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: 18px !important;
        line-height: 1.05 !important;
        font-weight: 600 !important;
        letter-spacing: -.035em !important;
        color: #ffffff !important;
        white-space: nowrap !important;
    }

    .seller-greeting-hero .seller-greeting-stat-sub {
        margin: 5px 0 0 !important;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: 7.2px !important;
        line-height: 1.35 !important;
        font-weight: 400 !important;
        color: rgba(255,255,255,.55) !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    /* Icons stay visible, but without circular/boxed containers. */
    .seller-greeting-hero .seller-greeting-stat-heading {
        display: flex !important;
        align-items: center !important;
        gap: 7px !important;
    }

    .seller-greeting-hero .seller-greeting-stat-icon {
        display: block !important;
        width: 13px !important;
        height: 13px !important;
        flex: 0 0 13px !important;
        color: #e1a21f !important;
        opacity: .92 !important;
    }

    .seller-greeting-hero .seller-greeting-stat-icon-weather {
        color: #63aff5 !important;
    }

    /* Slightly more room around the main welcome message. */
    .seller-greeting-hero .seller-greeting-icon-shell {
        width: 64px !important;
        height: 64px !important;
        flex: 0 0 64px !important;
    }

    #sellerGreetingText {
        font-size: 22px !important;
    }

    #sellerGreetingText + p {
        margin-top: 6px !important;
        font-size: 9.8px !important;
        line-height: 1.65 !important;
    }

    @media (min-width: 1280px) {
        .seller-greeting-hero {
            min-height: 184px !important;
            padding-top: 27px !important;
            padding-bottom: 27px !important;
        }
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        .seller-greeting-hero {
            min-height: 164px !important;
            padding-top: 21px !important;
            padding-bottom: 21px !important;
        }

        .seller-greeting-hero .seller-greeting-stat {
            padding-inline: 18px !important;
        }
    }

    @media (max-width: 1279px) {
        .seller-greeting-hero .seller-greeting-meta {
            width: 100% !important;
        }
    }

    @media (max-width: 639px) {
        .seller-greeting-hero {
            min-height: 0 !important;
            padding: 20px 16px !important;
        }

        .seller-greeting-hero .seller-greeting-meta {
            grid-template-columns: 1fr !important;
            margin-top: 4px !important;
        }

        .seller-greeting-hero .seller-greeting-stat,
        .seller-greeting-hero .seller-greeting-stat:first-child {
            padding: 11px 0 !important;
        }

        .seller-greeting-hero .seller-greeting-stat + .seller-greeting-stat {
            border-left: 0 !important;
            border-top: 1px solid rgba(255,255,255,.09) !important;
        }

        .seller-greeting-hero .seller-greeting-stat-copy {
            padding-left: 20px !important;
        }

        .seller-greeting-hero .seller-greeting-stat-value {
            font-size: 17px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER HERO — CLEAN BACKGROUND + LARGER ACTION ICONS
    |--------------------------------------------------------------------------
    | Removes the concentric/spiral decoration and gives the two primary
    | action icons stronger visual presence. No route or behavior changes.
    */
    .seller-greeting-hero {
        background:
            linear-gradient(135deg, #1f1e1b 0%, #23211d 52%, #1a1917 100%) !important;
    }

    .seller-greeting-hero::before {
        background:
            linear-gradient(
                90deg,
                rgba(255,255,255,.035) 0%,
                rgba(255,255,255,.012) 34%,
                rgba(255,255,255,0) 70%
            ) !important;
    }

    .seller-greeting-rings,
    .seller-greeting-rings::before,
    .seller-greeting-rings::after {
        display: none !important;
        content: none !important;
    }

    .seller-greeting-hero a[href*="products"],
    .seller-greeting-hero a[href*="orders"] {
        gap: 9px !important;
    }

    .seller-greeting-hero a[href*="products"] svg,
    .seller-greeting-hero a[href*="orders"] svg {
        width: 19px !important;
        height: 19px !important;
        flex: 0 0 19px !important;
        stroke-width: 1.9 !important;
    }

    @media (max-width: 639px) {
        .seller-greeting-hero a[href*="products"] svg,
        .seller-greeting-hero a[href*="orders"] svg {
            width: 18px !important;
            height: 18px !important;
            flex-basis: 18px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER HERO — LARGER TIME / DATE / WEATHER ICONS
    |--------------------------------------------------------------------------
    | Icons stay inline and container-free; only their visual size is increased.
    */
    .seller-greeting-hero .seller-greeting-stat-heading {
        gap: 8px !important;
    }

    .seller-greeting-hero .seller-greeting-stat-icon {
        width: 17px !important;
        height: 17px !important;
        flex: 0 0 17px !important;
        stroke-width: 1.9 !important;
        opacity: 1 !important;
    }

    .seller-greeting-hero .seller-greeting-stat-copy {
        padding-left: 25px !important;
    }

    @media (max-width: 639px) {
        .seller-greeting-hero .seller-greeting-stat-icon {
            width: 16px !important;
            height: 16px !important;
            flex-basis: 16px !important;
        }

        .seller-greeting-hero .seller-greeting-stat-copy {
            padding-left: 24px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER HERO — SLIGHTLY SHORTER HEIGHT
    |--------------------------------------------------------------------------
    | Small vertical reduction only. Layout, icons, actions, and live data stay.
    */
    .seller-greeting-hero {
        min-height: 166px !important;
        padding-top: 21px !important;
        padding-bottom: 21px !important;
    }

    @media (min-width: 1280px) {
        .seller-greeting-hero {
            min-height: 172px !important;
            padding-top: 22px !important;
            padding-bottom: 22px !important;
        }
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        .seller-greeting-hero {
            min-height: 154px !important;
            padding-top: 17px !important;
            padding-bottom: 17px !important;
        }
    }

    @media (max-width: 639px) {
        .seller-greeting-hero {
            min-height: 0 !important;
            padding-top: 18px !important;
            padding-bottom: 18px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER HERO — REFERENCE DESIGN APPLIED
    |--------------------------------------------------------------------------
    | Matches the approved visual reference while keeping all Seller live-data
    | IDs, routes, and backend values intact.
    */
    .seller-greeting-hero.seller-hero-reference {
        position: relative !important;
        display: block !important;
        min-height: 168px !important;
        overflow: hidden !important;
        padding: 25px 30px !important;
        border: 1px solid #302d28 !important;
        border-radius: 21px !important;
        background:
            radial-gradient(circle at 10% 35%, rgba(214,150,17,.07), transparent 24%),
            radial-gradient(circle at 86% 52%, rgba(214,150,17,.045), transparent 22%),
            linear-gradient(115deg,#1f1f1c 0%,#211f1c 52%,#191917 100%) !important;
        box-shadow:
            0 14px 32px rgba(29,24,18,.085),
            inset 0 1px 0 rgba(255,255,255,.035) !important;
    }

    .seller-greeting-hero.seller-hero-reference::before {
        content: "" !important;
        position: absolute !important;
        inset: 0 !important;
        pointer-events: none !important;
        background:
            linear-gradient(
                90deg,
                rgba(255,255,255,.018) 0%,
                transparent 34%,
                transparent 100%
            ) !important;
    }

    .seller-greeting-hero.seller-hero-reference::after {
        content: none !important;
        display: none !important;
    }

    .seller-hero-reference-layout {
        position: relative !important;
        z-index: 1 !important;
        display: grid !important;
        grid-template-columns:
            minmax(360px,1.12fr)
            1px
            minmax(490px,1.36fr)
            auto !important;
        align-items: center !important;
        gap: 26px !important;
        width: 100% !important;
        min-height: 116px !important;
    }

    .seller-hero-reference-identity {
        display: flex !important;
        min-width: 0 !important;
        align-items: center !important;
        gap: 20px !important;
    }

    .seller-hero-store-icon {
        display: grid !important;
        width: 66px !important;
        height: 66px !important;
        flex: 0 0 66px !important;
        place-items: center !important;
        border: 1px solid rgba(220,159,38,.22) !important;
        border-radius: 17px !important;
        background:
            linear-gradient(145deg,rgba(218,155,28,.19),rgba(218,155,28,.075)) !important;
        color: #e5a41a !important;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.055),
            0 7px 16px rgba(0,0,0,.10) !important;
    }

    .seller-hero-store-icon svg {
        width: 25px !important;
        height: 25px !important;
        stroke-width: 1.85 !important;
    }

    .seller-hero-reference-copy {
        min-width: 0 !important;
    }

    #sellerGreetingText.seller-hero-reference-title {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 8px !important;
        margin: 0 !important;
        font-family: "Poppins",ui-sans-serif,system-ui,sans-serif !important;
        font-size: 25px !important;
        line-height: 1.08 !important;
        font-weight: 650 !important;
        letter-spacing: -.042em !important;
        color: #fff !important;
    }

    .seller-hero-reference-title .seller-greeting-wave {
        width: 25px !important;
        height: 25px !important;
        flex-basis: 25px !important;
        margin-left: 0 !important;
        color: #e6a20f !important;
    }

    .seller-hero-reference-title .seller-greeting-wave .fi {
        width: 25px !important;
        height: 25px !important;
        flex-basis: 25px !important;
        font-size: 22px !important;
    }

    .seller-hero-reference-subtitle {
        margin: 8px 0 0 !important;
        font-family: "Poppins",ui-sans-serif,system-ui,sans-serif !important;
        font-size: 9.5px !important;
        line-height: 1.5 !important;
        font-weight: 400 !important;
        color: rgba(255,255,255,.72) !important;
    }

    .seller-hero-reference-statuses {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        margin-top: 14px !important;
    }

    .seller-hero-status {
        display: inline-flex !important;
        min-height: 31px !important;
        align-items: center !important;
        gap: 8px !important;
        border-radius: 999px !important;
        padding: 0 12px !important;
        font-family: "Poppins",ui-sans-serif,system-ui,sans-serif !important;
        font-size: 8.5px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
        white-space: nowrap !important;
    }

    .seller-hero-status > i {
        display: block !important;
        width: 8px !important;
        height: 8px !important;
        flex: 0 0 8px !important;
        border-radius: 50% !important;
    }

    .seller-hero-status--active {
        border: 1px solid rgba(46,164,88,.28) !important;
        background: rgba(32,112,61,.14) !important;
        color: #d7f5df !important;
    }

    .seller-hero-status--active > i {
        background: #35e45f !important;
        box-shadow: 0 0 0 3px rgba(53,228,95,.08) !important;
    }

    .seller-hero-status--restricted {
        border: 1px solid rgba(219,91,91,.30) !important;
        background: rgba(151,55,55,.15) !important;
        color: #ffd8d8 !important;
    }

    .seller-hero-status--restricted > i {
        background: #e36b6b !important;
    }

    .seller-hero-status-divider {
        display: block !important;
        width: 1px !important;
        height: 25px !important;
        background: rgba(255,255,255,.15) !important;
    }

    .seller-hero-verified {
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px !important;
        font-family: "Poppins",ui-sans-serif,system-ui,sans-serif !important;
        font-size: 8.5px !important;
        line-height: 1 !important;
        font-weight: 450 !important;
        color: rgba(255,255,255,.58) !important;
        white-space: nowrap !important;
    }

    .seller-hero-verified svg {
        width: 17px !important;
        height: 17px !important;
        color: rgba(255,255,255,.55) !important;
    }

    .seller-hero-reference-divider {
        width: 1px !important;
        height: 102px !important;
        align-self: center !important;
        background:
            linear-gradient(
                to bottom,
                transparent 0%,
                rgba(255,255,255,.13) 16%,
                rgba(255,255,255,.13) 84%,
                transparent 100%
            ) !important;
    }

    .seller-hero-reference-meta {
        display: grid !important;
        grid-template-columns: repeat(3,minmax(0,1fr)) !important;
        align-items: center !important;
        min-width: 0 !important;
        gap: 0 !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat {
        min-width: 0 !important;
        padding: 9px 24px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat:first-child {
        padding-left: 0 !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat:last-child {
        padding-right: 0 !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat + .seller-greeting-stat {
        border-left: 1px solid rgba(255,255,255,.11) !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-heading {
        display: flex !important;
        align-items: center !important;
        gap: 9px !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-icon {
        width: 19px !important;
        height: 19px !important;
        flex: 0 0 19px !important;
        color: #e2a10f !important;
        opacity: 1 !important;
        stroke-width: 1.9 !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-icon-weather {
        color: #5dabeb !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-label {
        margin: 0 !important;
        font-family: "Poppins",ui-sans-serif,system-ui,sans-serif !important;
        font-size: 6.8px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
        letter-spacing: .16em !important;
        text-transform: uppercase !important;
        color: rgba(255,255,255,.52) !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-copy {
        margin-top: 10px !important;
        padding-left: 28px !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-value {
        margin: 0 !important;
        font-family: "Poppins",ui-sans-serif,system-ui,sans-serif !important;
        font-size: 19px !important;
        line-height: 1 !important;
        font-weight: 650 !important;
        letter-spacing: -.038em !important;
        color: #fff !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-sub {
        margin: 6px 0 0 !important;
        font-family: "Poppins",ui-sans-serif,system-ui,sans-serif !important;
        font-size: 7px !important;
        line-height: 1.35 !important;
        font-weight: 400 !important;
        color: rgba(255,255,255,.54) !important;
    }

    .seller-hero-reference-actions {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 10px !important;
        white-space: nowrap !important;
    }

    .seller-hero-action {
        display: inline-flex !important;
        height: 46px !important;
        min-width: 142px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 9px !important;
        border-radius: 12px !important;
        padding: 0 16px !important;
        font-family: "Poppins",ui-sans-serif,system-ui,sans-serif !important;
        font-size: 9px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        transition:
            transform .16s ease,
            background-color .16s ease,
            border-color .16s ease,
            box-shadow .16s ease !important;
    }

    .seller-hero-action svg {
        width: 19px !important;
        height: 19px !important;
        flex: 0 0 19px !important;
    }

    .seller-hero-action--primary {
        border: 1px solid #e09b08 !important;
        background: linear-gradient(180deg,#e6a006 0%,#d68e02 100%) !important;
        color: #fff !important;
        box-shadow:
            0 11px 24px rgba(215,144,4,.22),
            inset 0 1px 0 rgba(255,255,255,.16) !important;
    }

    .seller-hero-action--primary:hover {
        transform: translateY(-1px) !important;
        background: linear-gradient(180deg,#eba60b 0%,#dc9405 100%) !important;
        box-shadow:
            0 13px 28px rgba(215,144,4,.25),
            inset 0 1px 0 rgba(255,255,255,.18) !important;
    }

    .seller-hero-action--secondary {
        border: 1px solid rgba(255,255,255,.18) !important;
        background: rgba(255,255,255,.045) !important;
        color: #fff !important;
        box-shadow:
            0 5px 13px rgba(0,0,0,.10),
            inset 0 1px 0 rgba(255,255,255,.025) !important;
    }

    .seller-hero-action--secondary:hover {
        transform: translateY(-1px) !important;
        border-color: rgba(255,255,255,.26) !important;
        background: rgba(255,255,255,.075) !important;
    }

    .seller-greeting-rings,
    .seller-greeting-rings::before,
    .seller-greeting-rings::after {
        display: none !important;
        content: none !important;
    }

    @media (max-width: 1399px) {
        .seller-hero-reference-layout {
            grid-template-columns: minmax(320px,.9fr) 1px minmax(440px,1.1fr) !important;
            gap: 22px !important;
        }

        .seller-hero-reference-actions {
            grid-column: 1 / -1 !important;
            justify-content: flex-end !important;
        }
    }

    @media (max-width: 1099px) {
        .seller-greeting-hero.seller-hero-reference {
            padding: 22px !important;
        }

        .seller-hero-reference-layout {
            grid-template-columns: 1fr !important;
            gap: 18px !important;
        }

        .seller-hero-reference-divider {
            display: none !important;
        }

        .seller-hero-reference-meta {
            padding-top: 16px !important;
            border-top: 1px solid rgba(255,255,255,.09) !important;
        }

        .seller-hero-reference-actions {
            grid-column: auto !important;
            justify-content: flex-start !important;
        }
    }

    @media (max-width: 639px) {
        .seller-greeting-hero.seller-hero-reference {
            min-height: 0 !important;
            padding: 18px 16px !important;
            border-radius: 18px !important;
        }

        .seller-hero-reference-identity {
            align-items: flex-start !important;
            gap: 13px !important;
        }

        .seller-hero-store-icon {
            width: 48px !important;
            height: 48px !important;
            flex-basis: 48px !important;
            border-radius: 13px !important;
        }

        .seller-hero-store-icon svg {
            width: 20px !important;
            height: 20px !important;
        }

        #sellerGreetingText.seller-hero-reference-title {
            font-size: 20px !important;
        }

        .seller-hero-reference-statuses {
            flex-wrap: wrap !important;
            gap: 8px !important;
            margin-top: 11px !important;
        }

        .seller-hero-status-divider {
            display: none !important;
        }

        .seller-hero-reference-meta {
            grid-template-columns: 1fr !important;
            padding-top: 8px !important;
        }

        .seller-greeting-hero.seller-hero-reference .seller-greeting-stat,
        .seller-greeting-hero.seller-hero-reference .seller-greeting-stat:first-child,
        .seller-greeting-hero.seller-hero-reference .seller-greeting-stat:last-child {
            padding: 12px 0 !important;
        }

        .seller-greeting-hero.seller-hero-reference .seller-greeting-stat + .seller-greeting-stat {
            border-left: 0 !important;
            border-top: 1px solid rgba(255,255,255,.08) !important;
        }

        .seller-hero-reference-actions {
            display: grid !important;
            grid-template-columns: repeat(2,minmax(0,1fr)) !important;
            width: 100% !important;
            gap: 8px !important;
        }

        .seller-hero-action {
            width: 100% !important;
            min-width: 0 !important;
            height: 42px !important;
            padding: 0 11px !important;
            font-size: 8.2px !important;
        }

        .seller-hero-action svg {
            width: 17px !important;
            height: 17px !important;
            flex-basis: 17px !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-hero-action {
            transition: none !important;
            transform: none !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER HERO — COMPACT FIT PASS
    |--------------------------------------------------------------------------
    | Keeps the approved reference design but reduces the container footprint
    | so the content fits naturally without looking stretched or oversized.
    */
    .seller-greeting-hero.seller-hero-reference {
        min-height: 150px !important;
        padding: 20px 22px !important;
        border-radius: 19px !important;
    }

    .seller-hero-reference-layout {
        grid-template-columns:
            minmax(300px,.95fr)
            1px
            minmax(410px,1.08fr)
            auto !important;
        gap: 18px !important;
        min-height: 104px !important;
    }

    .seller-hero-reference-identity {
        gap: 15px !important;
    }

    .seller-hero-store-icon {
        width: 56px !important;
        height: 56px !important;
        flex-basis: 56px !important;
        border-radius: 15px !important;
    }

    .seller-hero-store-icon svg {
        width: 22px !important;
        height: 22px !important;
    }

    #sellerGreetingText.seller-hero-reference-title {
        font-size: 23px !important;
    }

    .seller-hero-reference-subtitle {
        margin-top: 6px !important;
        font-size: 9px !important;
    }

    .seller-hero-reference-statuses {
        margin-top: 11px !important;
        gap: 10px !important;
    }

    .seller-hero-status {
        min-height: 28px !important;
        padding-inline: 10px !important;
        font-size: 8px !important;
    }

    .seller-hero-status-divider {
        height: 22px !important;
    }

    .seller-hero-verified {
        font-size: 8px !important;
    }

    .seller-hero-verified svg {
        width: 15px !important;
        height: 15px !important;
    }

    .seller-hero-reference-divider {
        height: 88px !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat {
        padding: 7px 16px !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-heading {
        gap: 7px !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-icon {
        width: 18px !important;
        height: 18px !important;
        flex-basis: 18px !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-copy {
        margin-top: 8px !important;
        padding-left: 25px !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-value {
        font-size: 18px !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-sub {
        margin-top: 5px !important;
        font-size: 6.8px !important;
    }

    .seller-hero-reference-actions {
        gap: 8px !important;
    }

    .seller-hero-action {
        height: 42px !important;
        min-width: 126px !important;
        gap: 8px !important;
        border-radius: 11px !important;
        padding-inline: 13px !important;
        font-size: 8.5px !important;
    }

    .seller-hero-action svg {
        width: 17px !important;
        height: 17px !important;
        flex-basis: 17px !important;
    }

    @media (min-width: 1280px) and (max-width: 1499px) {
        .seller-hero-reference-layout {
            grid-template-columns:
                minmax(285px,.9fr)
                1px
                minmax(390px,1.05fr)
                auto !important;
            gap: 15px !important;
        }

        .seller-greeting-hero.seller-hero-reference .seller-greeting-stat {
            padding-inline: 13px !important;
        }

        .seller-hero-action {
            min-width: 118px !important;
            padding-inline: 11px !important;
            font-size: 8.2px !important;
        }
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        .seller-greeting-hero.seller-hero-reference {
            min-height: 142px !important;
            padding-top: 17px !important;
            padding-bottom: 17px !important;
        }
    }

    @media (max-width: 1099px) {
        .seller-greeting-hero.seller-hero-reference {
            padding: 19px !important;
        }
    }

    @media (max-width: 639px) {
        .seller-greeting-hero.seller-hero-reference {
            padding: 16px 14px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER HERO — COMPACT PROFESSIONAL EXECUTIVE BAR
    |--------------------------------------------------------------------------
    | Keeps all live Time / Date / Weather data and actions, while reducing
    | the vertical footprint at normal 100% browser zoom.
    */
    .seller-greeting-hero.seller-hero-reference {
        min-height: 118px !important;
        padding: 14px 18px !important;
        border-radius: 17px !important;
        box-shadow:
            0 9px 22px rgba(29,24,18,.07),
            inset 0 1px 0 rgba(255,255,255,.03) !important;
    }

    .seller-hero-reference-layout {
        grid-template-columns:
            minmax(275px,.92fr)
            1px
            minmax(370px,1.06fr)
            auto !important;
        min-height: 88px !important;
        gap: 15px !important;
    }

    /* Left identity */
    .seller-hero-reference-identity {
        gap: 12px !important;
    }

    .seller-hero-store-icon {
        width: 46px !important;
        height: 46px !important;
        flex: 0 0 46px !important;
        border-radius: 12px !important;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.045),
            0 4px 10px rgba(0,0,0,.075) !important;
    }

    .seller-hero-store-icon svg {
        width: 19px !important;
        height: 19px !important;
    }

    #sellerGreetingText.seller-hero-reference-title {
        font-size: 19px !important;
        line-height: 1.08 !important;
        letter-spacing: -.035em !important;
        gap: 6px !important;
    }

    .seller-hero-reference-title .seller-greeting-wave,
    .seller-hero-reference-title .seller-greeting-wave .fi {
        width: 20px !important;
        height: 20px !important;
        flex-basis: 20px !important;
    }

    .seller-hero-reference-title .seller-greeting-wave .fi {
        font-size: 18px !important;
    }

    .seller-hero-reference-subtitle {
        margin-top: 4px !important;
        font-size: 8px !important;
        line-height: 1.4 !important;
    }

    .seller-hero-reference-statuses {
        margin-top: 8px !important;
        gap: 8px !important;
    }

    .seller-hero-status {
        min-height: 24px !important;
        gap: 6px !important;
        padding: 0 9px !important;
        font-size: 7.2px !important;
    }

    .seller-hero-status > i {
        width: 6px !important;
        height: 6px !important;
        flex-basis: 6px !important;
    }

    .seller-hero-status-divider {
        height: 18px !important;
    }

    .seller-hero-verified {
        gap: 5px !important;
        font-size: 7.2px !important;
    }

    .seller-hero-verified svg {
        width: 13px !important;
        height: 13px !important;
    }

    /* Divider */
    .seller-hero-reference-divider {
        height: 68px !important;
    }

    /* Time / Date / Weather */
    .seller-hero-reference-meta {
        align-items: center !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat {
        padding: 5px 13px !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-heading {
        gap: 6px !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-icon {
        width: 15px !important;
        height: 15px !important;
        flex-basis: 15px !important;
        stroke-width: 1.75 !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-label {
        font-size: 6px !important;
        letter-spacing: .13em !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-copy {
        margin-top: 5px !important;
        padding-left: 21px !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-value {
        font-size: 15px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
    }

    .seller-greeting-hero.seller-hero-reference .seller-greeting-stat-sub {
        margin-top: 3px !important;
        font-size: 6.2px !important;
        line-height: 1.25 !important;
    }

    /* Actions */
    .seller-hero-reference-actions {
        gap: 7px !important;
    }

    .seller-hero-action {
        height: 36px !important;
        min-width: 106px !important;
        gap: 6px !important;
        border-radius: 9px !important;
        padding: 0 10px !important;
        font-size: 7.5px !important;
        box-shadow: none !important;
    }

    .seller-hero-action svg {
        width: 15px !important;
        height: 15px !important;
        flex-basis: 15px !important;
    }

    .seller-hero-action:hover {
        transform: none !important;
    }

    .seller-hero-action--primary {
        box-shadow: 0 5px 13px rgba(215,144,4,.14) !important;
    }

    .seller-hero-action--secondary {
        box-shadow: none !important;
    }

    /* Common 100% zoom laptop height */
    @media (min-width: 1100px) and (max-height: 820px) {
        .seller-greeting-hero.seller-hero-reference {
            min-height: 108px !important;
            padding-top: 11px !important;
            padding-bottom: 11px !important;
        }

        .seller-hero-reference-layout {
            min-height: 82px !important;
        }

        .seller-hero-store-icon {
            width: 42px !important;
            height: 42px !important;
            flex-basis: 42px !important;
        }

        #sellerGreetingText.seller-hero-reference-title {
            font-size: 18px !important;
        }

        .seller-greeting-hero.seller-hero-reference .seller-greeting-stat {
            padding-inline: 11px !important;
        }
    }

    @media (max-width: 1399px) and (min-width: 1100px) {
        .seller-hero-reference-layout {
            grid-template-columns:
                minmax(250px,.86fr)
                1px
                minmax(340px,1fr)
                auto !important;
            gap: 12px !important;
        }

        .seller-hero-action {
            min-width: 96px !important;
            padding-inline: 9px !important;
        }
    }

    /* Keep existing responsive stacking clean below desktop widths. */
    @media (max-width: 1099px) {
        .seller-greeting-hero.seller-hero-reference {
            min-height: 0 !important;
            padding: 15px !important;
        }

        .seller-hero-reference-layout {
            min-height: 0 !important;
            gap: 13px !important;
        }

        .seller-hero-reference-meta {
            padding-top: 10px !important;
        }

        .seller-hero-reference-actions {
            margin-top: 0 !important;
        }
    }

    @media (max-width: 639px) {
        .seller-greeting-hero.seller-hero-reference {
            padding: 14px !important;
            border-radius: 15px !important;
        }

        .seller-hero-store-icon {
            width: 42px !important;
            height: 42px !important;
            flex-basis: 42px !important;
        }

        #sellerGreetingText.seller-hero-reference-title {
            font-size: 18px !important;
        }

        .seller-greeting-hero.seller-hero-reference .seller-greeting-stat,
        .seller-greeting-hero.seller-hero-reference .seller-greeting-stat:first-child,
        .seller-greeting-hero.seller-hero-reference .seller-greeting-stat:last-child {
            padding: 9px 0 !important;
        }

        .seller-hero-action {
            height: 36px !important;
            font-size: 7.5px !important;
        }
    }


    /* ============================================================
       RECENT ACTIVITY — REAL ORDER FEED
       ============================================================ */
    #sellerRecentActivityCard {
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        border: 1px solid #e7ddd0;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: var(--seller-elevated-card-shadow, 0 12px 28px rgba(37,29,20,.07));
        overflow: hidden;
    }

    #sellerRecentActivityCard:hover {
        transform: none !important;
        border-color: #e7ddd0 !important;
        box-shadow: var(--seller-elevated-card-shadow, 0 12px 28px rgba(37,29,20,.07)) !important;
    }

    .seller-recent-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 16px;
        border-bottom: 1px solid #eee8df;
    }

    .seller-recent-live {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 28px;
        border: 1px solid #d5e7dc;
        border-radius: 999px;
        background: #f5fbf7;
        padding: 0 10px;
        font-size: 8px;
        font-weight: 600;
        color: #4d7e60;
        white-space: nowrap;
    }

    .seller-recent-live::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: #49a36c;
    }

    .seller-recent-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        padding: 6px 16px 10px;
    }

    .seller-recent-item {
        display: grid;
        grid-template-columns: 42px minmax(0, 1fr) auto;
        align-items: center;
        gap: 10px;
        min-width: 0;
        padding: 11px 8px;
        border-bottom: 1px solid #f1ece5;
        text-decoration: none;
    }

    .seller-recent-item:nth-child(odd) {
        padding-left: 0;
        padding-right: 14px;
        border-right: 1px solid #f1ece5;
    }

    .seller-recent-item:nth-child(even) {
        padding-left: 14px;
        padding-right: 0;
    }

    .seller-recent-item:nth-last-child(-n+2) {
        border-bottom: 0;
    }

    .seller-recent-item:hover {
        background: #fdfbf8;
    }

    .seller-recent-avatar {
        position: relative;
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;
        overflow: hidden;
        border: 1px solid #e8dfd4;
        border-radius: 12px;
        background: #fff9ed;
        color: #9a6a18;
        font-size: 10px;
        font-weight: 700;
    }

    .seller-recent-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .seller-recent-new-dot {
        position: absolute;
        right: -1px;
        bottom: -1px;
        width: 10px;
        height: 10px;
        border: 2px solid #fff;
        border-radius: 999px;
        background: #45a66b;
    }

    .seller-recent-buyer {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 10px;
        line-height: 1.35;
        font-weight: 650;
        color: #332d27;
    }

    .seller-recent-product {
        display: block;
        margin-top: 3px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 8.5px;
        line-height: 1.4;
        font-weight: 450;
        color: #71685f;
    }

    .seller-recent-meta {
        margin-top: 4px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 5px;
        font-size: 7.4px;
        color: #9a9187;
    }

    .seller-recent-status {
        display: inline-flex;
        align-items: center;
        min-height: 22px;
        border-radius: 999px;
        padding: 0 8px;
        font-size: 7px;
        line-height: 1;
        font-weight: 650;
        white-space: nowrap;
    }

    .seller-recent-status--new { background: #fff5df; color: #a66e11; }
    .seller-recent-status--progress { background: #eef5fb; color: #527795; }
    .seller-recent-status--courier { background: #f4f0f8; color: #735f86; }
    .seller-recent-status--success { background: #edf8f1; color: #4d7d60; }
    .seller-recent-status--danger { background: #fff0f0; color: #a75a5a; }

    .seller-recent-side {
        min-width: 82px;
        text-align: right;
    }

    .seller-recent-total {
        display: block;
        font-size: 10px;
        line-height: 1.2;
        font-weight: 700;
        color: #2f2923;
    }

    .seller-recent-time {
        display: block;
        margin-top: 5px;
        font-size: 7px;
        line-height: 1.3;
        color: #9b9389;
        white-space: nowrap;
    }

    .seller-recent-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 16px;
        border-top: 1px solid #eee8df;
        background: #fffefa;
    }

    .seller-recent-footer p {
        margin: 0;
        font-size: 7.5px;
        color: #978f86;
    }

    .seller-recent-footer a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 8px;
        font-weight: 650;
        color: #9d6c18;
    }

    @media (max-width: 900px) {
        .seller-recent-list { grid-template-columns: minmax(0, 1fr); }

        .seller-recent-item,
        .seller-recent-item:nth-child(odd),
        .seller-recent-item:nth-child(even) {
            padding: 11px 0;
            border-right: 0;
            border-bottom: 1px solid #f1ece5;
        }

        .seller-recent-item:last-child { border-bottom: 0; }
    }

    @media (max-width: 520px) {
        .seller-recent-head {
            align-items: flex-start;
            flex-direction: column;
        }

        .seller-recent-item {
            grid-template-columns: 40px minmax(0, 1fr);
        }

        .seller-recent-side {
            grid-column: 2;
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            text-align: left;
        }

        .seller-recent-time { margin-top: 0; }

        .seller-recent-footer {
            align-items: flex-start;
            flex-direction: column;
        }
    }


    /* ============================================================
       SELLER ACTIVITY + RECENT ORDERS SPLIT
       ============================================================ */
    #sellerActivityOrdersRow .seller-activity-panel {
        min-width: 0;
        overflow: hidden;
        border: 1px solid #e7ddd0;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 9px 22px rgba(37,29,20,.04);
    }

    #sellerActivityOrdersRow .seller-activity-panel:hover {
        transform: none !important;
    }

    .seller-activity-owner-badge {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        border: 1px solid #dfe7ee;
        border-radius: 999px;
        background: #f7f9fb;
        padding: 0 10px;
        font-size: 8px;
        font-weight: 600;
        color: #63778b;
        white-space: nowrap;
    }

    .seller-activity-list,
    .seller-orders-feed {
        padding: 5px 16px 8px;
    }

    .seller-activity-item,
    .seller-order-feed-item {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        padding: 11px 0;
        border-bottom: 1px solid #f1ece5;
        text-decoration: none;
    }

    .seller-activity-item:last-child,
    .seller-order-feed-item:last-child {
        border-bottom: 0;
    }

    .seller-activity-item:hover,
    .seller-order-feed-item:hover {
        background: #fdfbf8;
    }

    .seller-activity-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border-radius: 11px;
        border: 1px solid transparent;
    }

    .seller-activity-icon svg {
        width: 16px;
        height: 16px;
    }

    .seller-activity-icon--created {
        border-color: #d9eadf;
        background: #f2faf5;
        color: #4e8161;
    }

    .seller-activity-icon--edited {
        border-color: #dce7f0;
        background: #f4f8fb;
        color: #557793;
    }

    .seller-activity-icon--order {
        border-color: #eee0c6;
        background: #fff9ef;
        color: #a9741d;
    }

    .seller-activity-icon--neutral {
        border-color: #e6e0d8;
        background: #f8f6f3;
        color: #7d746a;
    }

    .seller-activity-title {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 9.5px;
        font-weight: 650;
        color: #3a342e;
    }

    .seller-activity-description {
        display: block;
        margin-top: 3px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 8px;
        color: #756d64;
    }

    .seller-activity-meta {
        display: block;
        margin-top: 3px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 7.2px;
        color: #a0978d;
    }

    .seller-activity-time {
        flex: 0 0 auto;
        align-self: flex-start;
        margin-top: 2px;
        font-size: 7px;
        color: #9d958b;
        white-space: nowrap;
    }

    .seller-order-feed-item {
        display: grid;
        grid-template-columns: 42px minmax(0, 1fr) auto;
    }

    @media (max-width: 520px) {
        .seller-order-feed-item {
            grid-template-columns: 40px minmax(0, 1fr);
        }

        .seller-order-feed-item .seller-recent-side {
            grid-column: 2;
        }

        .seller-activity-item {
            align-items: flex-start;
        }

        .seller-activity-time {
            max-width: 74px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }



    /* ============================================================
       DASHBOARD SWAP — ACTIVITY + ORDERS IN TOP COMMERCE ROW
       Warm SARI gold is the primary accent.
       ============================================================ */
    #sellerCommerceSideColumn > #sellerRecentActivityCard,
    #sellerCommerceSideColumn > #sellerRecentOrdersCard {
        padding: 0 !important;
        overflow: hidden !important;
        min-height: 236px !important;
        max-height: 286px !important;
        border: 1px solid #eadfce !important;
        border-radius: 14px !important;
        background: #fff !important;
        box-shadow: 0 6px 17px rgba(75, 52, 20, .035) !important;
    }

    #sellerCommerceSideColumn > #sellerRecentActivityCard:hover,
    #sellerCommerceSideColumn > #sellerRecentOrdersCard:hover {
        transform: none !important;
        border-color: #e6d6bd !important;
        box-shadow: 0 8px 20px rgba(75, 52, 20, .045) !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-head,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head {
        padding: 12px 12px 10px !important;
        gap: 10px !important;
        border-bottom: 1px solid #f0e7da !important;
        background: linear-gradient(180deg, #fffaf1 0%, #fff 100%) !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-head > div:first-child > span:first-child,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head > div:first-child > span:first-child {
        width: 34px !important;
        height: 34px !important;
        flex: 0 0 34px !important;
        border-radius: 10px !important;
        border-color: #eddab9 !important;
        background: #fff7e8 !important;
        color: #b77917 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-head h3,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head h3 {
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: clamp(14.5px, .26vw + 11px, 16px) !important;
        line-height: 1.2 !important;
        font-weight: 700 !important;
        color: #28221b !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-head p,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head p {
        margin-top: 3px !important;
        font-size: clamp(8.6px, .12vw + 7px, 9.5px) !important;
        line-height: 1.35 !important;
        color: #91877b !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-owner-badge,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-live {
        min-height: 24px !important;
        padding: 0 8px !important;
        border-radius: 999px !important;
        border: 1px solid #ead8b7 !important;
        background: #fff7e7 !important;
        color: #a87318 !important;
        font-size: 7.2px !important;
        font-weight: 700 !important;
        white-space: nowrap !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-live::before {
        width: 5px !important;
        height: 5px !important;
        background: #d99516 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-list,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-orders-feed {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        max-height: 176px !important;
        overflow-y: auto !important;
        padding: 4px 11px !important;
        scrollbar-width: thin;
        scrollbar-color: #e1cfae transparent;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-list::-webkit-scrollbar,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-orders-feed::-webkit-scrollbar {
        width: 4px;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-list::-webkit-scrollbar-thumb,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-orders-feed::-webkit-scrollbar-thumb {
        background: #e1cfae;
        border-radius: 999px;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-item,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item {
        gap: 8px !important;
        padding: 8px 0 !important;
        border-bottom: 1px solid #f2ebdf !important;
        background: transparent !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-item:last-child,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item:last-child {
        border-bottom: 0 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-item:hover,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item:hover {
        background: #fffdf9 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-icon {
        width: 31px !important;
        height: 31px !important;
        flex: 0 0 31px !important;
        border-radius: 9px !important;
        border-color: #ead9bd !important;
        background: #fff8eb !important;
        color: #ad771a !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-icon svg {
        width: 13px !important;
        height: 13px !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-title,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-buyer {
        font-size: 8.8px !important;
        line-height: 1.25 !important;
        font-weight: 700 !important;
        color: #39322b !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-description,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-product {
        margin-top: 2px !important;
        font-size: 7.4px !important;
        line-height: 1.3 !important;
        color: #756c61 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-meta,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-meta {
        margin-top: 2px !important;
        font-size: 6.7px !important;
        line-height: 1.25 !important;
        color: #a2988c !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-time,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-time {
        font-size: 6.5px !important;
        color: #a1988d !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item {
        grid-template-columns: 34px minmax(0, 1fr) auto !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-avatar {
        width: 34px !important;
        height: 34px !important;
        border-radius: 10px !important;
        border-color: #eadbc3 !important;
        background: #fff8eb !important;
        color: #a9741c !important;
        font-size: 8.5px !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-new-dot {
        width: 8px !important;
        height: 8px !important;
        background: #dc9b1c !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-side {
        min-width: 72px !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-total {
        font-size: 8.8px !important;
        font-weight: 700 !important;
        color: #2f2923 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-status {
        min-height: 18px !important;
        padding: 0 6px !important;
        font-size: 6.2px !important;
        font-weight: 700 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-status--new,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-status--progress {
        background: #fff4dc !important;
        color: #a66f13 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-status--courier {
        background: #f7f1e7 !important;
        color: #866d45 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-status--success {
        background: #eef7ef !important;
        color: #507353 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-footer,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-footer {
        margin-top: auto !important;
        padding: 8px 11px !important;
        gap: 8px !important;
        border-top: 1px solid #f0e7da !important;
        background: #fffdf9 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-footer p,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-footer p {
        font-size: 6.6px !important;
        color: #999085 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-footer a,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-footer a {
        font-size: 7.4px !important;
        font-weight: 700 !important;
        color: #a66f15 !important;
    }

    /* Financial Snapshot + Order Management moved below; keep them intact and polished. */
    #sellerFinancialOrdersRow > .seller-side-panel {
        min-width: 0 !important;
        min-height: 236px !important;
        padding: 14px !important;
        border: 1px solid #ebe1d3 !important;
        border-radius: 16px !important;
        background: #fff !important;
        box-shadow: 0 7px 18px rgba(54, 38, 18, .035) !important;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
    }

    #sellerFinancialOrdersRow .seller-panel-title {
        font-size: 15px !important;
        line-height: 1.2 !important;
        font-weight: 700 !important;
    }

    #sellerFinancialOrdersRow .seller-order-panel > div:nth-child(2) {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 6px !important;
    }

    #sellerFinancialOrdersRow .seller-order-row {
        min-height: 38px !important;
        padding: 7px 9px !important;
        border-radius: 9px !important;
    }

    #sellerFinancialOrdersRow .seller-order-view-all,
    #sellerFinancialOrdersRow .seller-financial-panel a[href*="reports"] {
        min-height: 36px !important;
        height: 36px !important;
        border-radius: 9px !important;
        font-size: 9px !important;
    }

    @media (max-width: 520px) {
        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item {
            grid-template-columns: 34px minmax(0, 1fr) !important;
        }
        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-side {
            grid-column: 2 !important;
            min-width: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 6px !important;
            text-align: left !important;
        }
    }

    /* ============================================================
       FINAL VISUAL PASS
       - Recent Activity / Recent Orders: clean white, no cream cast
       - Financial Snapshot / Order Management: tighter and compact
       ============================================================ */

    /* ---------- Recent Activity + Recent Orders: neutral white ---------- */
    #sellerCommerceSideColumn > #sellerRecentActivityCard,
    #sellerCommerceSideColumn > #sellerRecentOrdersCard {
        background: #ffffff !important;
        border-color: #e7e1d8 !important;
        box-shadow: 0 6px 16px rgba(31, 27, 22, .035) !important;
    }

    #sellerCommerceSideColumn > #sellerRecentActivityCard:hover,
    #sellerCommerceSideColumn > #sellerRecentOrdersCard:hover {
        background: #ffffff !important;
        border-color: #ddd4c8 !important;
        box-shadow: 0 8px 18px rgba(31, 27, 22, .045) !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-head,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head {
        background: #ffffff !important;
        border-bottom-color: #ece7e0 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-head > div:first-child > span:first-child,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head > div:first-child > span:first-child {
        background: #ffffff !important;
        border-color: #dfc58f !important;
        color: #b87910 !important;
        box-shadow: none !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-owner-badge,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-live {
        background: #ffffff !important;
        border-color: #dfc58f !important;
        color: #a96f12 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-live::before {
        background: #d59518 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-item,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item {
        background: #ffffff !important;
        border-bottom-color: #efebe5 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-item:hover,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item:hover {
        background: #fafafa !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-icon,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-avatar {
        background: #ffffff !important;
        border-color: #e0c995 !important;
        color: #ad7515 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-footer,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-footer {
        background: #ffffff !important;
        border-top-color: #ece7e0 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-list,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-orders-feed {
        scrollbar-color: #d7b66d transparent !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-list::-webkit-scrollbar-thumb,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-orders-feed::-webkit-scrollbar-thumb {
        background: #d7b66d !important;
    }

    /* ---------- Bottom row: compact Financial + Order Management ---------- */
    #sellerFinancialOrdersRow {
        gap: 12px !important;
        margin-top: 12px !important;
        align-items: stretch !important;
    }

    #sellerFinancialOrdersRow > .seller-side-panel {
        min-height: 158px !important;
        padding: 11px !important;
        border-radius: 14px !important;
        border-color: #e7e1d8 !important;
        box-shadow: 0 5px 14px rgba(31, 27, 22, .03) !important;
        background: #ffffff !important;
    }

    #sellerFinancialOrdersRow .seller-panel-title {
        font-size: 13.5px !important;
        line-height: 1.18 !important;
        font-weight: 700 !important;
    }

    #sellerFinancialOrdersRow > .seller-side-panel > div:first-child > p {
        margin-top: 2px !important;
        font-size: 8px !important;
        line-height: 1.3 !important;
        color: #91887d !important;
    }

    /* Financial Snapshot: 3 metrics in one compact desktop row */
    #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) {
        margin-top: 8px !important;
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 6px !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) > div {
        min-width: 0 !important;
        padding: 7px 8px !important;
        border-radius: 9px !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) > div > div {
        gap: 7px !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) > div > div > div:first-child {
        width: 29px !important;
        height: 29px !important;
        border-radius: 8px !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) > div > div > div:first-child span {
        font-size: 11px !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) p:first-child {
        font-size: 7.2px !important;
        line-height: 1.25 !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) p:last-child {
        margin-top: 1px !important;
        font-size: 11.5px !important;
        line-height: 1.12 !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel > .mt-auto {
        padding-top: 8px !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel a[href*="reports"] {
        min-height: 31px !important;
        height: 31px !important;
        padding-inline: 10px !important;
        border-radius: 8px !important;
        font-size: 8px !important;
        box-shadow: none !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel a[href*="reports"] > span > span:first-child {
        width: 23px !important;
        height: 23px !important;
        border-radius: 6px !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel a[href*="reports"] svg {
        width: 12px !important;
        height: 12px !important;
    }

    /* Order Management: tighter 2 x 3 grid */
    #sellerFinancialOrdersRow .seller-order-panel > div:nth-child(2) {
        margin-top: 8px !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 5px !important;
    }

    #sellerFinancialOrdersRow .seller-order-row {
        min-height: 30px !important;
        padding: 5px 7px !important;
        border-radius: 8px !important;
        border-color: #e9e4dd !important;
        background: #ffffff !important;
    }

    #sellerFinancialOrdersRow .seller-order-row:hover {
        background: #fafafa !important;
    }

    #sellerFinancialOrdersRow .seller-order-row span:first-child {
        font-size: 8px !important;
        line-height: 1.15 !important;
    }

    #sellerFinancialOrdersRow .seller-order-row span:last-child {
        min-width: 21px !important;
        height: 21px !important;
        min-height: 21px !important;
        border-radius: 6px !important;
        padding-inline: 5px !important;
        font-size: 7px !important;
    }

    #sellerFinancialOrdersRow .seller-order-panel > .mt-auto {
        padding-top: 8px !important;
    }

    #sellerFinancialOrdersRow .seller-order-view-all {
        min-height: 31px !important;
        height: 31px !important;
        border-radius: 8px !important;
        font-size: 8px !important;
        background: #ffffff !important;
        border-color: #ddd4c7 !important;
        color: #9b6b1c !important;
    }

    #sellerFinancialOrdersRow .seller-order-view-all:hover {
        background: #fafafa !important;
    }

    @media (max-width: 900px) {
        #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) {
            grid-template-columns: 1fr !important;
        }

        #sellerFinancialOrdersRow > .seller-side-panel {
            min-height: 0 !important;
        }
    }

    @media (max-width: 520px) {
        #sellerFinancialOrdersRow .seller-order-panel > div:nth-child(2) {
            grid-template-columns: 1fr !important;
        }
    }


    /* ============================================================
       FINAL DASHBOARD CARD SYSTEM
       - No internal scrollbars
       - Only latest 4 Activity / Orders rows
       - Financial + Order Management match Activity / Orders height
       - Clean white surface with restrained SARI gold accents
       ============================================================ */

    /* ----- Recent Activity + Recent Orders: no mini-scroll areas ----- */
    #sellerCommerceSideColumn > #sellerRecentActivityCard,
    #sellerCommerceSideColumn > #sellerRecentOrdersCard {
        overflow: hidden !important;
        background: #ffffff !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-list,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-orders-feed {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        max-height: none !important;
        overflow: hidden !important;
        padding: 4px 12px 5px !important;
        scrollbar-width: none !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-list::-webkit-scrollbar,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-orders-feed::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-item,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item {
        min-height: 43px !important;
        padding: 7px 0 !important;
    }

    #sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-icon {
        width: 30px !important;
        height: 30px !important;
        flex: 0 0 30px !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-avatar {
        width: 32px !important;
        height: 32px !important;
    }

    /* ----- Equal desktop card height ----- */
    @media (min-width: 1180px) {
        #sellerCommerceSideColumn > #sellerRecentActivityCard,
        #sellerCommerceSideColumn > #sellerRecentOrdersCard,
        #sellerFinancialOrdersRow > .seller-side-panel {
            height: 286px !important;
            min-height: 286px !important;
            max-height: 286px !important;
        }
    }

    /* ----- Financial + Orders row ----- */
    #sellerFinancialOrdersRow {
        gap: 12px !important;
        align-items: stretch !important;
    }

    #sellerFinancialOrdersRow > .seller-side-panel {
        padding: 13px !important;
        border: 1px solid #e7e1d9 !important;
        border-radius: 14px !important;
        background: #ffffff !important;
        box-shadow: 0 6px 16px rgba(31, 27, 22, .035) !important;
        overflow: hidden !important;
    }

    #sellerFinancialOrdersRow > .seller-side-panel:hover {
        transform: none !important;
        border-color: #ddd4c8 !important;
        box-shadow: 0 8px 18px rgba(31, 27, 22, .045) !important;
    }

    #sellerFinancialOrdersRow .seller-panel-title {
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: 14.5px !important;
        line-height: 1.2 !important;
        font-weight: 700 !important;
        letter-spacing: -.02em !important;
        color: #28221b !important;
    }

    #sellerFinancialOrdersRow > .seller-side-panel > div:first-child > p {
        margin-top: 3px !important;
        font-size: 8.4px !important;
        line-height: 1.35 !important;
        color: #91887d !important;
    }

    /* Financial Snapshot: compact stacked metrics for balanced card height */
    #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) {
        margin-top: 9px !important;
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 6px !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) > div {
        min-width: 0 !important;
        padding: 7px 9px !important;
        border: 1px solid #ece5db !important;
        border-radius: 9px !important;
        background: #ffffff !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) > div > div {
        gap: 8px !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) > div > div > div:first-child {
        width: 29px !important;
        height: 29px !important;
        border: 1px solid #e7cf9e !important;
        border-radius: 8px !important;
        background: #fff9ee !important;
        color: #b77917 !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) > div > div > div:first-child span {
        font-size: 11px !important;
        color: #b77917 !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) p:first-child {
        font-size: 7.4px !important;
        line-height: 1.2 !important;
        font-weight: 500 !important;
        color: #8d8378 !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) p:last-child {
        margin-top: 1px !important;
        font-size: 12px !important;
        line-height: 1.12 !important;
        font-weight: 700 !important;
        color: #302a24 !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel > .mt-auto {
        padding-top: 8px !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel a[href*="reports"] {
        min-height: 31px !important;
        height: 31px !important;
        padding-inline: 10px !important;
        border-radius: 8px !important;
        background: #de9710 !important;
        box-shadow: none !important;
        font-size: 8px !important;
        font-weight: 700 !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel a[href*="reports"]:hover {
        background: #c9860b !important;
    }

    #sellerFinancialOrdersRow .seller-financial-panel a[href*="reports"] > span > span:first-child {
        width: 23px !important;
        height: 23px !important;
        border-radius: 6px !important;
    }

    /* Order Management: compact professional workflow grid */
    #sellerFinancialOrdersRow .seller-order-panel > div:nth-child(2) {
        margin-top: 9px !important;
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 6px !important;
    }

    #sellerFinancialOrdersRow .seller-order-row {
        min-height: 34px !important;
        padding: 6px 8px !important;
        border: 1px solid #ebe5dd !important;
        border-radius: 8px !important;
        background: #ffffff !important;
    }

    #sellerFinancialOrdersRow .seller-order-row:hover {
        background: #fafafa !important;
        border-color: #dfd6ca !important;
    }

    #sellerFinancialOrdersRow .seller-order-row span:first-child {
        font-size: 8.2px !important;
        line-height: 1.15 !important;
        font-weight: 500 !important;
        color: #4b443d !important;
    }

    #sellerFinancialOrdersRow .seller-order-row span:last-child {
        min-width: 22px !important;
        min-height: 22px !important;
        height: 22px !important;
        padding-inline: 6px !important;
        border-radius: 7px !important;
        background: #fff5e3 !important;
        color: #a96e10 !important;
        font-size: 7.2px !important;
        font-weight: 700 !important;
    }

    #sellerFinancialOrdersRow .seller-order-panel > .mt-auto {
        padding-top: 8px !important;
    }

    #sellerFinancialOrdersRow .seller-order-view-all {
        min-height: 31px !important;
        height: 31px !important;
        border-radius: 8px !important;
        border-color: #dfd6ca !important;
        background: #ffffff !important;
        color: #9d6c17 !important;
        font-size: 8px !important;
        font-weight: 700 !important;
    }

    #sellerFinancialOrdersRow .seller-order-view-all:hover {
        border-color: #d3b97e !important;
        background: #fffdf9 !important;
    }

    @media (max-width: 900px) {
        #sellerCommerceSideColumn > #sellerRecentActivityCard,
        #sellerCommerceSideColumn > #sellerRecentOrdersCard,
        #sellerFinancialOrdersRow > .seller-side-panel {
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
        }

        #sellerFinancialOrdersRow .seller-order-panel > div:nth-child(2) {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 520px) {
        #sellerFinancialOrdersRow .seller-order-panel > div:nth-child(2) {
            grid-template-columns: 1fr !important;
        }
    }


    /* ============================================================
       FINAL ALIGNMENT + RECENT ORDERS POLISH
       ============================================================ */

    /*
     * Financial Snapshot and Order Management use the same desktop
     * footprint as Recent Activity / Recent Orders.
     */
    @media (min-width: 1180px) {
        #sellerFinancialOrdersRow {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 12px !important;
            align-items: stretch !important;
        }

        #sellerFinancialOrdersRow > .seller-side-panel {
            width: 100% !important;
            height: 286px !important;
            min-height: 286px !important;
            max-height: 286px !important;
        }
    }

    /*
     * Recent Orders: four clean preview rows, no scrolling,
     * consistent rhythm with Recent Activity.
     */
    #sellerCommerceSideColumn #sellerRecentOrdersCard {
        display: flex !important;
        flex-direction: column !important;
        background: #fff !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head {
        flex: 0 0 auto !important;
        padding: 12px 14px !important;
        background: #fff !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-orders-feed {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        max-height: none !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
        padding: 2px 14px 3px !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item {
        flex: 1 1 0 !important;
        min-height: 0 !important;
        display: grid !important;
        grid-template-columns: 32px minmax(0, 1fr) 88px !important;
        align-items: center !important;
        gap: 9px !important;
        padding: 5px 0 !important;
        border-bottom: 1px solid #eee9e2 !important;
        background: #fff !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item:last-child {
        border-bottom: 0 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item:hover {
        background: #fafafa !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-avatar {
        width: 32px !important;
        height: 32px !important;
        border-radius: 9px !important;
        border: 1px solid #dfc58f !important;
        background: #fff !important;
        color: #ab7314 !important;
        font-size: 8px !important;
        font-weight: 700 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-new-dot {
        width: 7px !important;
        height: 7px !important;
        right: -1px !important;
        bottom: -1px !important;
        border-width: 1.5px !important;
        background: #d9991a !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-buyer {
        font-size: 8.8px !important;
        line-height: 1.2 !important;
        font-weight: 700 !important;
        color: #342e28 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-product {
        margin-top: 2px !important;
        font-size: 7.2px !important;
        line-height: 1.2 !important;
        color: #746c63 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-meta {
        margin-top: 2px !important;
        gap: 4px !important;
        font-size: 6.4px !important;
        line-height: 1.15 !important;
        color: #a1988e !important;
        flex-wrap: nowrap !important;
        overflow: hidden !important;
        white-space: nowrap !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-side {
        min-width: 0 !important;
        width: 88px !important;
        text-align: right !important;
        align-self: center !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-total {
        font-size: 8.8px !important;
        line-height: 1.1 !important;
        font-weight: 700 !important;
        color: #29231e !important;
        white-space: nowrap !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-status {
        min-height: 18px !important;
        margin-top: 3px !important;
        padding: 0 6px !important;
        border: 1px solid #ead8b3 !important;
        border-radius: 999px !important;
        background: #fff8e9 !important;
        color: #a56f15 !important;
        font-size: 6px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-time {
        margin-top: 3px !important;
        font-size: 6.1px !important;
        line-height: 1.1 !important;
        color: #a59c92 !important;
        white-space: nowrap !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-footer {
        flex: 0 0 auto !important;
        min-height: 34px !important;
        padding: 8px 13px !important;
        background: #fff !important;
        border-top: 1px solid #ece7e0 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-footer p {
        font-size: 6.8px !important;
        color: #9a9187 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-footer a {
        font-size: 7.4px !important;
        font-weight: 700 !important;
        color: #a36e13 !important;
    }

    /*
     * Keep Recent Activity and Recent Orders visually identical in
     * outer dimensions.
     */
    @media (min-width: 1180px) {
        #sellerCommerceSideColumn > #sellerRecentActivityCard,
        #sellerCommerceSideColumn > #sellerRecentOrdersCard {
            height: 286px !important;
            min-height: 286px !important;
            max-height: 286px !important;
        }
    }

    @media (max-width: 520px) {
        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item {
            grid-template-columns: 32px minmax(0, 1fr) !important;
        }

        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-side {
            grid-column: 2 !important;
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 6px !important;
            margin-top: 2px !important;
            text-align: left !important;
        }

        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-time {
            margin-top: 0 !important;
        }
    }


    /* ============================================================
       RECENT ORDERS — FINAL UI ALIGNMENT FIX
       UI-only override. No routes, Blade data, controller values,
       status mapping, forms, or JavaScript behavior are changed.
       Keep this block last inside the dashboard <style>.
       ============================================================ */

    /* Keep the three commerce cards visually aligned on desktop. */
    @media (min-width: 1180px) {
        #sellerCommerceSideColumn > #sellerRecentActivityCard,
        #sellerCommerceSideColumn > #sellerRecentOrdersCard,
        #sellerCommerceSideColumn > .seller-inventory-panel {
            height: 352px !important;
            min-height: 352px !important;
            max-height: 352px !important;
        }
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard {
        display: flex !important;
        flex-direction: column !important;
        width: 100% !important;
        min-width: 0 !important;
        overflow: hidden !important;
        border: 1px solid #e5ded5 !important;
        border-radius: 18px !important;
        background: #ffffff !important;
        box-shadow:
            0 8px 22px rgba(37, 29, 20, .045),
            inset 0 1px 0 rgba(255, 255, 255, .95) !important;
        transform: none !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard:hover {
        border-color: #e0d7cc !important;
        background: #ffffff !important;
        box-shadow:
            0 8px 22px rgba(37, 29, 20, .045),
            inset 0 1px 0 rgba(255, 255, 255, .95) !important;
        transform: none !important;
    }

    /* Header */
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head {
        flex: 0 0 84px !important;
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) auto !important;
        align-items: center !important;
        gap: 16px !important;
        padding: 15px 18px !important;
        border-bottom: 1px solid #eee9e2 !important;
        background: #ffffff !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head > div:first-child {
        min-width: 0 !important;
        gap: 12px !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head > div:first-child > span:first-child {
        width: 42px !important;
        height: 42px !important;
        flex: 0 0 42px !important;
        border: 1px solid #e2c181 !important;
        border-radius: 12px !important;
        background: #fffdf8 !important;
        color: #b87910 !important;
        box-shadow: none !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head > div:first-child > span:first-child svg {
        width: 18px !important;
        height: 18px !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head h3 {
        margin: 0 !important;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: 15.5px !important;
        line-height: 1.25 !important;
        font-weight: 700 !important;
        letter-spacing: -.025em !important;
        color: #28221b !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head p {
        max-width: 320px !important;
        margin: 4px 0 0 !important;
        font-size: 8.5px !important;
        line-height: 1.45 !important;
        font-weight: 400 !important;
        color: #91887d !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-live {
        display: inline-flex !important;
        min-height: 30px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 7px !important;
        border: 1px solid #e2ba72 !important;
        border-radius: 999px !important;
        background: #ffffff !important;
        padding: 0 11px !important;
        font-size: 7.5px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        color: #a86f12 !important;
        white-space: nowrap !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-live::before {
        content: "" !important;
        width: 6px !important;
        height: 6px !important;
        flex: 0 0 6px !important;
        border-radius: 50% !important;
        background: #d59518 !important;
        box-shadow: 0 0 0 3px rgba(213, 149, 24, .08) !important;
    }

    /* Four fixed preview rows. */
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-orders-feed {
        flex: 1 1 auto !important;
        display: block !important;
        min-height: 0 !important;
        max-height: none !important;
        overflow: hidden !important;
        padding: 0 18px !important;
        background: #ffffff !important;
        scrollbar-width: none !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-orders-feed::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item {
        display: grid !important;
        grid-template-columns: 40px minmax(0, 1fr) 112px !important;
        align-items: center !important;
        width: 100% !important;
        min-width: 0 !important;
        height: 56px !important;
        min-height: 56px !important;
        max-height: 56px !important;
        gap: 11px !important;
        padding: 0 !important;
        border-bottom: 1px solid #eee9e2 !important;
        background: #ffffff !important;
        text-decoration: none !important;
        transform: none !important;
        transition: background-color .15s ease !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item:last-child {
        border-bottom: 0 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item:hover {
        background: #fcfbf9 !important;
        transform: none !important;
    }

    /* Avatar */
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-avatar {
        position: relative !important;
        display: grid !important;
        width: 38px !important;
        height: 38px !important;
        flex: 0 0 38px !important;
        place-items: center !important;
        border: 1px solid #dfc58f !important;
        border-radius: 11px !important;
        background: #fffdf9 !important;
        color: #aa7113 !important;
        font-size: 8px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        overflow: visible !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-avatar img {
        display: block !important;
        width: 100% !important;
        height: 100% !important;
        border-radius: inherit !important;
        object-fit: cover !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-new-dot {
        position: absolute !important;
        right: -2px !important;
        bottom: -2px !important;
        width: 8px !important;
        height: 8px !important;
        border: 2px solid #ffffff !important;
        border-radius: 50% !important;
        background: #d59518 !important;
    }

    /* Main order copy */
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item > span:nth-child(2) {
        min-width: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-buyer {
        display: block !important;
        overflow: hidden !important;
        font-size: 9.5px !important;
        line-height: 1.25 !important;
        font-weight: 700 !important;
        color: #302a24 !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-product {
        display: block !important;
        overflow: hidden !important;
        margin-top: 3px !important;
        font-size: 7.8px !important;
        line-height: 1.25 !important;
        font-weight: 400 !important;
        color: #756d64 !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-meta {
        display: flex !important;
        min-width: 0 !important;
        align-items: center !important;
        gap: 4px !important;
        margin-top: 3px !important;
        overflow: hidden !important;
        font-size: 6.6px !important;
        line-height: 1.2 !important;
        color: #a1988e !important;
        white-space: nowrap !important;
    }

    /* Amount / status / time */
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-side {
        width: 112px !important;
        min-width: 112px !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-end !important;
        justify-content: center !important;
        align-self: stretch !important;
        text-align: right !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-total {
        display: block !important;
        width: 100% !important;
        overflow: hidden !important;
        font-size: 9.5px !important;
        line-height: 1.15 !important;
        font-weight: 700 !important;
        letter-spacing: -.015em !important;
        color: #29231e !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-side > span:nth-child(2) {
        display: block !important;
        margin: 4px 0 0 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-status {
        display: inline-flex !important;
        min-height: 19px !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 999px !important;
        padding: 0 7px !important;
        font-size: 6.4px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        white-space: nowrap !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-status--new,
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-status--progress {
        border: 1px solid #efd59e !important;
        background: #fff8e8 !important;
        color: #9f6810 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-status--courier {
        border: 1px solid #ded7e8 !important;
        background: #f7f4fa !important;
        color: #735f86 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-status--success {
        border: 1px solid #cfe3d4 !important;
        background: #f0f8f2 !important;
        color: #4d7659 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-status--danger {
        border: 1px solid #eccccc !important;
        background: #fff2f2 !important;
        color: #a55656 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-time {
        display: block !important;
        margin-top: 3px !important;
        font-size: 6.2px !important;
        line-height: 1.1 !important;
        font-weight: 400 !important;
        color: #a79e94 !important;
        white-space: nowrap !important;
    }

    /* Footer */
    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-footer {
        flex: 0 0 42px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        min-height: 42px !important;
        margin: 0 !important;
        padding: 0 18px !important;
        border-top: 1px solid #eee9e2 !important;
        background: #ffffff !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-footer p {
        margin: 0 !important;
        font-size: 6.8px !important;
        line-height: 1.2 !important;
        color: #9c9389 !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-footer a {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        margin: 0 !important;
        font-size: 7.3px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        color: #a66f13 !important;
        white-space: nowrap !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-footer a svg {
        width: 11px !important;
        height: 11px !important;
        transition: transform .15s ease !important;
    }

    #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-footer a:hover svg {
        transform: translateX(2px) !important;
    }

    /* Responsive fallback: no forced card height and no clipped order content. */
    @media (max-width: 1179px) {
        #sellerCommerceSideColumn > #sellerRecentActivityCard,
        #sellerCommerceSideColumn > #sellerRecentOrdersCard,
        #sellerCommerceSideColumn > .seller-inventory-panel {
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
        }
    }

    @media (max-width: 640px) {
        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head {
            min-height: 0 !important;
            flex-basis: auto !important;
            grid-template-columns: minmax(0, 1fr) auto !important;
            gap: 10px !important;
            padding: 14px !important;
        }

        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head > div:first-child > span:first-child {
            width: 38px !important;
            height: 38px !important;
            flex-basis: 38px !important;
        }

        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head p {
            max-width: 240px !important;
        }

        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-live {
            min-height: 27px !important;
            padding-inline: 9px !important;
            font-size: 7px !important;
        }

        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-orders-feed {
            padding-inline: 14px !important;
        }

        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-order-feed-item {
            height: auto !important;
            min-height: 76px !important;
            max-height: none !important;
            grid-template-columns: 38px minmax(0, 1fr) !important;
            gap: 10px !important;
            padding: 9px 0 !important;
        }

        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-avatar {
            width: 36px !important;
            height: 36px !important;
            flex-basis: 36px !important;
        }

        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-side {
            grid-column: 2 !important;
            width: 100% !important;
            min-width: 0 !important;
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: flex-start !important;
            align-self: auto !important;
            gap: 7px !important;
            margin-top: 4px !important;
            text-align: left !important;
        }

        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-total {
            width: auto !important;
        }

        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-side > span:nth-child(2) {
            margin: 0 !important;
        }

        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-time {
            margin: 0 0 0 auto !important;
        }

        #sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-footer {
            min-height: 42px !important;
            flex-basis: 42px !important;
            padding-inline: 14px !important;
        }
    }
/* ==========================================================================
   SARI DASHBOARD — FLAT PALETTE / FLOATING SURFACES
   Final visual layer.
   - No decorative linear/radial gradients.
   - No visible outer card borders.
   - Lightweight shadows only.
   - No container lift animations.
   - Category donut conic-gradient stays because it renders real chart data.
   ========================================================================== */

:root {
    --sari-primary-gold: #D89B10;
    --sari-deep-gold: #B67A08;
    --sari-soft-ivory: #F7F3EC;
    --sari-warm-white: #FCFAF6;
    --sari-sand-beige: #EDE3D2;
    --sari-border-linen: #D9CBB6;
    --sari-charcoal: #222222;
    --sari-muted-slate: #6B7280;
    --sari-soft-green: #63A375;
    --sari-dusty-blue: #6C8FB5;
    --sari-soft-rose: #D97C6C;
    --sari-dark-olive: #4E5A4F;

    --sari-floating-shadow:
        0 12px 28px rgba(34, 28, 22, .065),
        0 2px 7px rgba(34, 28, 22, .025);
}

/* Page / typography */
#sellerDashboardStage,
#sellerDashboardContent {
    color: var(--sari-charcoal);
}

#sellerDashboardStage {
    background: transparent !important;
}

/* --------------------------------------------------------------------------
   HERO — solid colors only
   -------------------------------------------------------------------------- */
.seller-greeting-hero,
.seller-greeting-hero.seller-hero-reference {
    border-color: transparent !important;
    background: var(--sari-charcoal) !important;
    box-shadow: var(--sari-floating-shadow) !important;
}

.seller-greeting-hero::before,
.seller-greeting-hero.seller-hero-reference::before,
.seller-greeting-hero.seller-hero-reference::after {
    background: none !important;
    background-image: none !important;
}

.seller-greeting-divider,
.seller-hero-reference-divider {
    background: rgba(255, 255, 255, .12) !important;
}

.seller-greeting-icon-shell,
.seller-hero-store-icon {
    border-color: rgba(216, 155, 16, .22) !important;
    background: #342E25 !important;
    box-shadow: none !important;
}

.seller-hero-action--primary,
.seller-hero-action--primary:hover {
    border-color: var(--sari-primary-gold) !important;
    background: var(--sari-primary-gold) !important;
    box-shadow: 0 7px 16px rgba(182, 122, 8, .16) !important;
    transform: none !important;
}

.seller-hero-action--primary:hover {
    background: var(--sari-deep-gold) !important;
    border-color: var(--sari-deep-gold) !important;
}

.seller-hero-action--secondary,
.seller-hero-action--secondary:hover {
    background: rgba(255,255,255,.06) !important;
    transform: none !important;
}

/* --------------------------------------------------------------------------
   MAJOR DASHBOARD SURFACES — floating, borderless, still
   -------------------------------------------------------------------------- */
#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a,
#sellerCategoryBreakdownCard,
#sellerSalesPerformanceCard,
#sellerCommerceSideColumn > .seller-side-panel,
#sellerCommerceSideColumn > #sellerRecentActivityCard,
#sellerCommerceSideColumn > #sellerRecentOrdersCard,
#sellerFinancialOrdersRow > .seller-side-panel {
    border-color: transparent !important;
    background: var(--sari-warm-white) !important;
    box-shadow: var(--sari-floating-shadow) !important;
    transform: none !important;
    backdrop-filter: none !important;
    transition:
        box-shadow .12s ease,
        background-color .12s ease !important;
}

#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a:hover,
#sellerCategoryBreakdownCard:hover,
#sellerSalesPerformanceCard:hover,
#sellerCommerceSideColumn > .seller-side-panel:hover,
#sellerCommerceSideColumn > #sellerRecentActivityCard:hover,
#sellerCommerceSideColumn > #sellerRecentOrdersCard:hover,
#sellerFinancialOrdersRow > .seller-side-panel:hover {
    border-color: transparent !important;
    background: var(--sari-warm-white) !important;
    box-shadow: var(--sari-floating-shadow) !important;
    transform: none !important;
}

/* Clean section heads; no colored title-icon tiles and no gradient headers. */
#sellerCategoryBreakdownCard > div:first-child,
#sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-head,
#sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head {
    background: var(--sari-warm-white) !important;
    background-image: none !important;
    border-bottom-color: rgba(217, 203, 182, .42) !important;
}

#sellerCategoryBreakdownCard > div:first-child > div:first-child,
#sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-head > div:first-child,
#sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head > div:first-child {
    display: block !important;
}

/* Hide any legacy title icon shell left by cached/snapshot markup. */
#sellerCategoryBreakdownCard > div:first-child > div:first-child > span:first-child:has(svg),
#sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-head > div:first-child > span:first-child:has(svg),
#sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head > div:first-child > span:first-child:has(svg) {
    display: none !important;
}

/* --------------------------------------------------------------------------
   CATALOG — flat center, palette-only category colors
   -------------------------------------------------------------------------- */
#sellerCategoryBreakdownCard .seller-category-donut::before {
    background: #FFFFFF !important;
    background-image: none !important;
    border-color: rgba(217, 203, 182, .55) !important;
    box-shadow: none !important;
}

#sellerCategoryBreakdownCard .seller-category-donut::after {
    background: none !important;
    background-image: none !important;
}

#sellerCategoryBreakdownCard .seller-category-donut {
    /* var(--seller-category-gradient) is the actual chart, not decoration. */
    box-shadow: 0 8px 20px rgba(34, 28, 22, .06) !important;
}

#sellerCategoryBreakdownCard .seller-category-ranked-row,
#sellerCategoryBreakdownCard .seller-category-stat-row {
    border-color: transparent !important;
    box-shadow: none !important;
}

/* --------------------------------------------------------------------------
   SALES PERFORMANCE — no decorative card gradient / no delayed chart draw
   -------------------------------------------------------------------------- */
#sellerSalesPerformanceCard.seller-sales-admin-card,
#sellerSalesPerformanceCard.seller-sales-admin-card:hover {
    border-color: transparent !important;
    background: var(--sari-warm-white) !important;
    background-image: none !important;
    box-shadow: var(--sari-floating-shadow) !important;
    transform: none !important;
}

#sellerSalesPerformanceCard .seller-sales-admin-metrics,
#sellerSalesPerformanceCard .seller-sales-admin-chart-shell {
    background: #FFFFFF !important;
    background-image: none !important;
    box-shadow: none !important;
}

#sellerSalesPerformanceCard .seller-sales-admin-line {
    stroke: var(--sari-primary-gold) !important;
}

/* --------------------------------------------------------------------------
   RECENT ACTIVITY / RECENT ORDERS
   -------------------------------------------------------------------------- */
#sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-owner-badge {
    border-color: transparent !important;
    background: #F2F6F9 !important;
    color: var(--sari-dusty-blue) !important;
}

#sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-live {
    border-color: transparent !important;
    background: #F1F7F3 !important;
    color: #477B59 !important;
}

#sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-live::before {
    background: var(--sari-soft-green) !important;
}

.seller-activity-item,
.seller-order-feed-item {
    transition: background-color .1s ease !important;
}

.seller-activity-item:hover,
.seller-order-feed-item:hover {
    background: #FAF7F1 !important;
}

/* --------------------------------------------------------------------------
   INNER OPERATION CARDS — clean, quiet, no colored borders
   -------------------------------------------------------------------------- */
#sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div,
#sellerCommerceSideColumn .seller-order-row,
#sellerCommerceSideColumn .seller-inventory-row,
#sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) > div,
#sellerFinancialOrdersRow .seller-order-row {
    border-color: transparent !important;
    background: #FFFFFF !important;
    box-shadow: 0 3px 10px rgba(34, 28, 22, .035) !important;
    transform: none !important;
}

#sellerCommerceSideColumn .seller-order-row:hover,
#sellerCommerceSideColumn .seller-inventory-row:hover,
#sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div:hover,
#sellerFinancialOrdersRow .seller-order-row:hover,
#sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) > div:hover {
    border-color: transparent !important;
    background: #FFFFFF !important;
    transform: none !important;
}

/* Buttons: solid palette, never gradient. */
.seller-inventory-button,
#sellerCommerceSideColumn .seller-financial-panel a[href*="reports"],
#sellerFinancialOrdersRow .seller-financial-panel a[href*="reports"] {
    border-color: var(--sari-primary-gold) !important;
    background: var(--sari-primary-gold) !important;
    background-image: none !important;
    color: #FFFFFF !important;
    box-shadow: 0 6px 14px rgba(182, 122, 8, .13) !important;
}

.seller-inventory-button:hover,
#sellerCommerceSideColumn .seller-financial-panel a[href*="reports"]:hover,
#sellerFinancialOrdersRow .seller-financial-panel a[href*="reports"]:hover {
    border-color: var(--sari-deep-gold) !important;
    background: var(--sari-deep-gold) !important;
}

/* Smooth without expensive visual effects. */
#sellerCategoryBreakdownCard,
#sellerSalesPerformanceCard,
#sellerRecentActivityCard,
#sellerRecentOrdersCard,
#sellerCommerceSideColumn > .seller-side-panel,
#sellerFinancialOrdersRow > .seller-side-panel {
    will-change: auto !important;
}

@media (prefers-reduced-motion: reduce) {
    #sellerDashboardContent *,
    #sellerDashboardContent *::before,
    #sellerDashboardContent *::after {
        transition-duration: 1ms !important;
        animation-duration: 1ms !important;
        animation-iteration-count: 1 !important;
    }
}
/* ==========================================================================
   SARI DASHBOARD — FLOATING DEPTH V2
   Stronger depth without gradients, colored borders, blur filters, or lift
   animations. Designed to stay smooth at normal 100% browser zoom.
   ========================================================================== */

:root {
    --sari-floating-shadow:
        0 18px 42px rgba(34, 28, 22, .085),
        0 5px 14px rgba(34, 28, 22, .035),
        0 1px 2px rgba(34, 28, 22, .018);

    --sari-floating-shadow-small:
        0 10px 24px rgba(34, 28, 22, .060),
        0 3px 8px rgba(34, 28, 22, .025);
}

/* Give the white surfaces enough contrast to visually float. */
#sellerDashboardStage {
    position: relative;
    background: transparent !important;
}

#sellerDashboardContent {
    position: relative;
}

/* Major cards: more elevation, still borderless and completely still. */
#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a,
#sellerCategoryBreakdownCard,
#sellerSalesPerformanceCard,
#sellerCommerceSideColumn > .seller-side-panel,
#sellerCommerceSideColumn > #sellerRecentActivityCard,
#sellerCommerceSideColumn > #sellerRecentOrdersCard,
#sellerFinancialOrdersRow > .seller-side-panel {
    border: 1px solid transparent !important;
    background: #FCFAF6 !important;
    box-shadow: var(--sari-floating-shadow) !important;
    transform: none !important;
    filter: none !important;
    backdrop-filter: none !important;
    isolation: isolate;
}

/* No hover movement: depth remains stable so it feels premium, not bouncy. */
#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a:hover,
#sellerCategoryBreakdownCard:hover,
#sellerSalesPerformanceCard:hover,
#sellerCommerceSideColumn > .seller-side-panel:hover,
#sellerCommerceSideColumn > #sellerRecentActivityCard:hover,
#sellerCommerceSideColumn > #sellerRecentOrdersCard:hover,
#sellerFinancialOrdersRow > .seller-side-panel:hover {
    border-color: transparent !important;
    background: #FCFAF6 !important;
    box-shadow: var(--sari-floating-shadow) !important;
    transform: none !important;
}

/* Slightly rounder silhouettes improve the floating effect without looking soft. */
#sellerCategoryBreakdownCard,
#sellerSalesPerformanceCard,
#sellerRecentActivityCard,
#sellerRecentOrdersCard {
    border-radius: 18px !important;
}

#sellerCommerceSideColumn > .seller-side-panel,
#sellerFinancialOrdersRow > .seller-side-panel {
    border-radius: 16px !important;
}

/* Inner surfaces remain flatter than the parent so hierarchy is clear. */
#sellerSalesPerformanceCard .seller-sales-admin-metrics,
#sellerSalesPerformanceCard .seller-sales-admin-chart-shell,
#sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div,
#sellerCommerceSideColumn .seller-order-row,
#sellerCommerceSideColumn .seller-inventory-row,
#sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) > div,
#sellerFinancialOrdersRow .seller-order-row {
    box-shadow: var(--sari-floating-shadow-small) !important;
}

/* Headers stay visually attached to each card instead of becoming mini cards. */
#sellerCategoryBreakdownCard > div:first-child,
#sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-head,
#sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head {
    box-shadow: none !important;
}

/* Summary cards use a slightly lighter depth than the analytics panels. */
#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a {
    box-shadow:
        0 13px 30px rgba(34, 28, 22, .068),
        0 4px 10px rgba(34, 28, 22, .028) !important;
}

/* Keep paint work cheap: no filter, blur, or animated shadows. */
#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a,
#sellerCategoryBreakdownCard,
#sellerSalesPerformanceCard,
#sellerRecentActivityCard,
#sellerRecentOrdersCard,
#sellerCommerceSideColumn > .seller-side-panel,
#sellerFinancialOrdersRow > .seller-side-panel {
    transition: background-color .10s ease !important;
    will-change: auto !important;
}

@media (max-width: 640px) {
    #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a,
    #sellerCategoryBreakdownCard,
    #sellerSalesPerformanceCard,
    #sellerCommerceSideColumn > .seller-side-panel,
    #sellerCommerceSideColumn > #sellerRecentActivityCard,
    #sellerCommerceSideColumn > #sellerRecentOrdersCard,
    #sellerFinancialOrdersRow > .seller-side-panel {
        box-shadow:
            0 12px 28px rgba(34, 28, 22, .070),
            0 3px 9px rgba(34, 28, 22, .028) !important;
    }
}

/* ==========================================================================
   SARI DASHBOARD — REFERENCE FLOATING SHADOW FINAL
   Inspired by the supplied reference:
   - soft neutral page contrast
   - bright white card surfaces
   - layered soft shadow for visible elevation
   - no gradients, blur filters, or hover lift
   - static shadows for smooth rendering
   ========================================================================== */

:root {
    --sari-dashboard-canvas: #F6F4F0;
    --sari-card-surface: #FFFFFF;
    --sari-card-border: rgba(217, 203, 182, .52);

    --sari-reference-shadow:
        0 1px 2px rgba(34, 34, 34, .025),
        0 8px 20px rgba(34, 34, 34, .060),
        0 20px 46px rgba(34, 34, 34, .070);

    --sari-reference-shadow-soft:
        0 1px 2px rgba(34, 34, 34, .020),
        0 6px 14px rgba(34, 34, 34, .045),
        0 12px 26px rgba(34, 34, 34, .045);
}

/* Slightly warmer neutral canvas makes the white cards visibly float. */
#sellerDashboardStage {
    background: var(--sari-dashboard-canvas) !important;
    border-radius: 22px !important;
}

/* MAIN FLOATING CONTAINERS */
#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a,
#sellerCategoryBreakdownCard,
#sellerSalesPerformanceCard,
#sellerProductsPanel,
#sellerCommerceSideColumn > .seller-side-panel,
#sellerCommerceSideColumn > #sellerRecentActivityCard,
#sellerCommerceSideColumn > #sellerRecentOrdersCard,
#sellerFinancialOrdersRow > .seller-side-panel {
    border: 1px solid var(--sari-card-border) !important;
    background: var(--sari-card-surface) !important;
    background-image: none !important;
    box-shadow: var(--sari-reference-shadow) !important;
    transform: none !important;
    filter: none !important;
    backdrop-filter: none !important;
}

/* Keep the main containers completely still on hover. */
#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a:hover,
#sellerCategoryBreakdownCard:hover,
#sellerSalesPerformanceCard:hover,
#sellerProductsPanel:hover,
#sellerCommerceSideColumn > .seller-side-panel:hover,
#sellerCommerceSideColumn > #sellerRecentActivityCard:hover,
#sellerCommerceSideColumn > #sellerRecentOrdersCard:hover,
#sellerFinancialOrdersRow > .seller-side-panel:hover {
    border-color: var(--sari-card-border) !important;
    background: var(--sari-card-surface) !important;
    box-shadow: var(--sari-reference-shadow) !important;
    transform: none !important;
}

/* Rounded silhouette closer to the supplied floating-card reference. */
#sellerCategoryBreakdownCard,
#sellerSalesPerformanceCard,
#sellerProductsPanel,
#sellerRecentActivityCard,
#sellerRecentOrdersCard,
#sellerCommerceSideColumn > .seller-side-panel,
#sellerFinancialOrdersRow > .seller-side-panel {
    border-radius: 19px !important;
}

/* Summary cards: slightly smaller shadow than analytics cards. */
#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a {
    border-radius: 17px !important;
    box-shadow:
        0 1px 2px rgba(34, 34, 34, .020),
        0 7px 17px rgba(34, 34, 34, .052),
        0 15px 32px rgba(34, 34, 34, .055) !important;
}

/* Hero remains dark, but gets the same soft floating depth. */
.seller-greeting-hero,
.seller-greeting-hero.seller-hero-reference {
    box-shadow:
        0 2px 5px rgba(20, 18, 16, .075),
        0 12px 28px rgba(20, 18, 16, .105),
        0 24px 48px rgba(20, 18, 16, .080) !important;
    transform: none !important;
}

/* Inner cards stay lighter than their parent so the hierarchy stays clean. */
#sellerSalesPerformanceCard .seller-sales-admin-metrics,
#sellerSalesPerformanceCard .seller-sales-admin-chart-shell,
#sellerCategoryBreakdownCard .seller-category-stat-row,
#sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div,
#sellerCommerceSideColumn .seller-order-row,
#sellerCommerceSideColumn .seller-inventory-row,
#sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) > div,
#sellerFinancialOrdersRow .seller-order-row {
    border-color: rgba(217, 203, 182, .34) !important;
    background: #FFFFFF !important;
    box-shadow: var(--sari-reference-shadow-soft) !important;
    transform: none !important;
}

/* Clean attached headers — no separate elevated header layer. */
#sellerCategoryBreakdownCard > div:first-child,
#sellerCommerceSideColumn #sellerRecentActivityCard .seller-recent-head,
#sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-head {
    background: #FFFFFF !important;
    background-image: none !important;
    box-shadow: none !important;
}

/* Product cards get a lighter elevation so the page does not become too busy. */
#sellerProductsPanel .seller-responsive-product-card,
#sellerProductsPanel .seller-responsive-add-card {
    border-color: rgba(217, 203, 182, .42) !important;
    background: #FFFFFF !important;
    box-shadow:
        0 1px 2px rgba(34,34,34,.018),
        0 5px 14px rgba(34,34,34,.040),
        0 10px 24px rgba(34,34,34,.035) !important;
    transform: none !important;
}

#sellerProductsPanel .seller-responsive-product-card:hover,
#sellerProductsPanel .seller-responsive-add-card:hover {
    border-color: rgba(217, 203, 182, .42) !important;
    box-shadow:
        0 1px 2px rgba(34,34,34,.018),
        0 5px 14px rgba(34,34,34,.040),
        0 10px 24px rgba(34,34,34,.035) !important;
    transform: none !important;
}

/* Performance: no shadow animation, filter, blur, or compositor forcing. */
#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a,
#sellerCategoryBreakdownCard,
#sellerSalesPerformanceCard,
#sellerProductsPanel,
#sellerRecentActivityCard,
#sellerRecentOrdersCard,
#sellerCommerceSideColumn > .seller-side-panel,
#sellerFinancialOrdersRow > .seller-side-panel {
    transition:
        background-color .10s ease,
        border-color .10s ease !important;
    will-change: auto !important;
}

/* Mobile gets a slightly lighter shadow so the UI stays crisp. */
@media (max-width: 640px) {
    #sellerDashboardStage {
        border-radius: 16px !important;
    }

    #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a,
    #sellerCategoryBreakdownCard,
    #sellerSalesPerformanceCard,
    #sellerProductsPanel,
    #sellerCommerceSideColumn > .seller-side-panel,
    #sellerCommerceSideColumn > #sellerRecentActivityCard,
    #sellerCommerceSideColumn > #sellerRecentOrdersCard,
    #sellerFinancialOrdersRow > .seller-side-panel {
        box-shadow:
            0 1px 2px rgba(34,34,34,.020),
            0 7px 18px rgba(34,34,34,.050),
            0 13px 28px rgba(34,34,34,.045) !important;
    }
}

/* ==========================================================================
   SARI DASHBOARD — UNIFIED SELLER PALETTE FINAL
   Uses the shared Seller design language:
   neutral gray canvas, white elevated surfaces, restrained gold,
   neutral text, and lighter inner-card hierarchy.
   ========================================================================== */

:root {
    --sari-dashboard-canvas: #F4F5F7;
    --sari-card-surface: #FFFFFF;
    --sari-card-surface-soft: #FAFAFB;
    --sari-card-border: #E7E9EE;

    --sari-dashboard-text: #202124;
    --sari-dashboard-secondary: #4B5563;
    --sari-dashboard-muted: #8A919B;

    --sari-dashboard-gold: #D89B10;
    --sari-dashboard-gold-dark: #B67A08;
    --sari-dashboard-gold-soft: #FFF7E6;

    --sari-dashboard-success: #63A375;
    --sari-dashboard-info: #6C8FB5;
    --sari-dashboard-danger: #D97C6C;

    --sari-dashboard-shadow:
        0 1px 2px rgba(32, 33, 36, .025),
        0 8px 20px rgba(32, 33, 36, .050),
        0 18px 38px rgba(32, 33, 36, .055);

    --sari-dashboard-shadow-soft:
        0 1px 2px rgba(32, 33, 36, .020),
        0 5px 13px rgba(32, 33, 36, .035),
        0 10px 24px rgba(32, 33, 36, .035);
}

#sellerDashboardStage {
    background: transparent !important;
    color: var(--sari-dashboard-text) !important;
}

/* Primary dashboard surfaces: white + soft floating depth. */
#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a,
#sellerCategoryBreakdownCard,
#sellerSalesPerformanceCard,
#sellerProductsPanel,
#sellerCommerceSideColumn > .seller-side-panel,
#sellerCommerceSideColumn > #sellerRecentActivityCard,
#sellerCommerceSideColumn > #sellerRecentOrdersCard,
#sellerFinancialOrdersRow > .seller-side-panel {
    border-color: var(--sari-card-border) !important;
    background: var(--sari-card-surface) !important;
    background-image: none !important;
    box-shadow: var(--sari-dashboard-shadow) !important;
    color: var(--sari-dashboard-text) !important;
}

/* No bounce/lift on information containers. */
#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a:hover,
#sellerCategoryBreakdownCard:hover,
#sellerSalesPerformanceCard:hover,
#sellerProductsPanel:hover,
#sellerCommerceSideColumn > .seller-side-panel:hover,
#sellerCommerceSideColumn > #sellerRecentActivityCard:hover,
#sellerCommerceSideColumn > #sellerRecentOrdersCard:hover,
#sellerFinancialOrdersRow > .seller-side-panel:hover {
    border-color: var(--sari-card-border) !important;
    background: var(--sari-card-surface) !important;
    box-shadow: var(--sari-dashboard-shadow) !important;
    transform: none !important;
}

/* Inner blocks are flatter so the parent card remains the visual anchor. */
#sellerSalesPerformanceCard .seller-sales-admin-metrics,
#sellerSalesPerformanceCard .seller-sales-admin-chart-shell,
#sellerCategoryBreakdownCard .seller-category-stat-row,
#sellerCommerceSideColumn .seller-financial-panel > div:nth-child(2) > div,
#sellerCommerceSideColumn .seller-order-row,
#sellerCommerceSideColumn .seller-inventory-row,
#sellerFinancialOrdersRow .seller-financial-panel > div:nth-child(2) > div,
#sellerFinancialOrdersRow .seller-order-row {
    border-color: rgba(231, 233, 238, .9) !important;
    background: var(--sari-card-surface-soft) !important;
    box-shadow: var(--sari-dashboard-shadow-soft) !important;
}

/* Text hierarchy */
#sellerCategoryBreakdownCard h3,
#sellerSalesPerformanceCard h3,
#sellerCommerceSideColumn .seller-panel-title,
#sellerRecentActivityCard h3,
#sellerRecentOrdersCard h3 {
    color: var(--sari-dashboard-text) !important;
}

#sellerCategoryBreakdownCard p,
#sellerSalesPeriodLabel,
#sellerCommerceSideColumn .seller-side-panel > div:first-child p,
#sellerRecentActivityCard p,
#sellerRecentOrdersCard p {
    color: var(--sari-dashboard-muted);
}

/* Restrained gold usage: actions and selected state only. */
.seller-inventory-button,
#sellerCategoryViewProducts,
#sellerCommerceSideColumn .seller-financial-panel a[href*="reports"],
#sellerFinancialOrdersRow .seller-financial-panel a[href*="reports"] {
    border-color: var(--sari-dashboard-gold) !important;
    background: var(--sari-dashboard-gold) !important;
    color: #FFFFFF !important;
    box-shadow: 0 6px 14px rgba(182, 122, 8, .12) !important;
}

.seller-inventory-button:hover,
#sellerCategoryViewProducts:hover,
#sellerCommerceSideColumn .seller-financial-panel a[href*="reports"]:hover,
#sellerFinancialOrdersRow .seller-financial-panel a[href*="reports"]:hover {
    border-color: var(--sari-dashboard-gold-dark) !important;
    background: var(--sari-dashboard-gold-dark) !important;
}

/* Status palette */
#sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-live {
    background: #F1F7F3 !important;
    color: #477B59 !important;
}

#sellerCommerceSideColumn #sellerRecentOrdersCard .seller-recent-live::before {
    background: var(--sari-dashboard-success) !important;
}

#sellerCommerceSideColumn #sellerRecentActivityCard .seller-activity-owner-badge {
    background: #F2F6FA !important;
    color: var(--sari-dashboard-info) !important;
}

/* Keep dashboard paint lightweight. */
#sellerCategoryBreakdownCard,
#sellerSalesPerformanceCard,
#sellerProductsPanel,
#sellerRecentActivityCard,
#sellerRecentOrdersCard,
#sellerCommerceSideColumn > .seller-side-panel,
#sellerFinancialOrdersRow > .seller-side-panel {
    filter: none !important;
    backdrop-filter: none !important;
    transform: none !important;
    transition: background-color .10s ease, border-color .10s ease !important;
    will-change: auto !important;
}

/* ==========================================================================
   SUMMARY CARDS — CLEAN ELEVATION HOVER FINAL
   No colored dots, no bottom accent line, no icon scale.
   Hover feedback is elevation only.
   ========================================================================== */

#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a {
    border-color: #E7E9EE !important;
    background: #FFFFFF !important;
    box-shadow:
        0 1px 2px rgba(32, 33, 36, .020),
        0 7px 17px rgba(32, 33, 36, .048),
        0 15px 32px rgba(32, 33, 36, .052) !important;
    transform: translateY(0) !important;
    transition:
        transform .14s cubic-bezier(.22, 1, .36, 1),
        box-shadow .14s ease !important;
    will-change: auto !important;
}

#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a:hover {
    border-color: #E7E9EE !important;
    background: #FFFFFF !important;
    transform: translateY(-4px) !important;
    box-shadow:
        0 2px 4px rgba(32, 33, 36, .025),
        0 11px 25px rgba(32, 33, 36, .060),
        0 24px 48px rgba(32, 33, 36, .075) !important;
}

#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a:active {
    transform: translateY(-2px) !important;
}

/* Icon tile stays still; only the whole card rises. */
#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a > div > div:last-child {
    transform: none !important;
    transition: none !important;
}

/* Safety: hide any stale cached decorative dot / bottom accent if older markup remains. */
#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a
    .h-1\.5.w-1\.5.rounded-full.bg-current {
    display: none !important;
}

#sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a
    > .absolute.bottom-0.left-0.h-\[2px\] {
    display: none !important;
}

@media (prefers-reduced-motion: reduce) {
    #sellerDashboardContent > section.mt-4.grid.grid-cols-1.gap-3.sm\:grid-cols-2.xl\:grid-cols-5 > a {
        transition: none !important;
    }
}

/* ==========================================================================
   SELLER HERO — SAFE ADMIN-STYLE FINAL OVERRIDE
   --------------------------------------------------------------------------
   Uses the original, stable Seller hero DOM. Only presentation is changed.
   No new grid markup, no action column, and no viewport-height behavior.
   ========================================================================== */

.seller-greeting-hero.seller-hero-reference.seller-hero-admin-safe {
    position: relative !important;
    display: block !important;
    width: 100% !important;
    height: auto !important;
    min-height: 0 !important;
    max-height: none !important;
    overflow: hidden !important;

    padding: 18px 24px !important;
    border: 1px solid #31302e !important;
    border-radius: 19px !important;

    background: #20201f !important;
    background-image: none !important;

    box-shadow:
        0 13px 30px rgba(20, 18, 16, .12),
        inset 0 1px 0 rgba(255,255,255,.035) !important;

    align-items: initial !important;
}

.seller-greeting-hero.seller-hero-reference.seller-hero-admin-safe::before {
    content: "" !important;
    position: absolute !important;
    inset: 0 !important;
    pointer-events: none !important;
    background:
        radial-gradient(circle at 5% 50%, rgba(220,158,23,.05), transparent 23%),
        linear-gradient(90deg, rgba(255,255,255,.018), transparent 30%) !important;
}

.seller-greeting-hero.seller-hero-reference.seller-hero-admin-safe::after {
    content: none !important;
    display: none !important;
}

/* Stable 3-column structure: greeting | separator | time/date/weather. */
.seller-hero-admin-safe .seller-hero-reference-layout {
    position: relative !important;
    z-index: 1 !important;

    display: grid !important;
    grid-template-columns:
        minmax(360px, 1.18fr)
        1px
        minmax(560px, 1.42fr) !important;

    align-items: center !important;
    gap: 22px !important;

    width: 100% !important;
    height: auto !important;
    min-height: 102px !important;
    max-height: none !important;
}

/* Remove the old decorative Seller storefront tile for the cleaner Admin look. */
.seller-hero-admin-safe .seller-hero-store-icon {
    display: none !important;
}

.seller-hero-admin-safe .seller-hero-reference-identity {
    display: flex !important;
    min-width: 0 !important;
    align-items: center !important;
    gap: 0 !important;
}

.seller-hero-admin-safe .seller-hero-reference-copy {
    min-width: 0 !important;
}

.seller-hero-admin-safe #sellerGreetingText.seller-hero-reference-title {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 8px !important;

    margin: 0 !important;

    font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
    font-size: clamp(23px, 1.42vw, 29px) !important;
    line-height: 1.08 !important;
    font-weight: 700 !important;
    letter-spacing: -.045em !important;

    color: #ffffff !important;
}

.seller-hero-admin-safe .seller-greeting-wave {
    display: inline-flex !important;
    width: 27px !important;
    height: 27px !important;
    flex: 0 0 27px !important;
    align-items: center !important;
    justify-content: center !important;

    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    background: transparent !important;
    box-shadow: none !important;

    color: #e6a61e !important;
}

.seller-hero-admin-safe .seller-greeting-wave .fi {
    width: auto !important;
    height: auto !important;
    flex: 0 0 auto !important;
    font-size: 24px !important;
    line-height: 1 !important;
}

.seller-hero-admin-safe .seller-hero-reference-subtitle {
    margin: 8px 0 0 !important;

    font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
    font-size: 9.6px !important;
    line-height: 1.55 !important;
    font-weight: 400 !important;

    color: rgba(255,255,255,.74) !important;
}

/* Store state stays available, but is flatter and less "button-like". */
.seller-hero-admin-safe .seller-hero-reference-statuses {
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;

    margin-top: 12px !important;
}

.seller-hero-admin-safe .seller-hero-status {
    display: inline-flex !important;
    min-height: 0 !important;
    align-items: center !important;
    gap: 6px !important;

    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    padding: 0 !important;
    box-shadow: none !important;

    font-size: 7.4px !important;
    font-weight: 600 !important;

    color: rgba(255,255,255,.72) !important;
}

.seller-hero-admin-safe .seller-hero-status > i {
    width: 6px !important;
    height: 6px !important;
    flex: 0 0 6px !important;
}

.seller-hero-admin-safe .seller-hero-status--active > i {
    background: #57c875 !important;
    box-shadow: 0 0 0 3px rgba(87,200,117,.08) !important;
}

.seller-hero-admin-safe .seller-hero-status--restricted > i {
    background: #df7474 !important;
    box-shadow: none !important;
}

.seller-hero-admin-safe .seller-hero-status-divider {
    width: 1px !important;
    height: 13px !important;
    background: rgba(255,255,255,.14) !important;
}

.seller-hero-admin-safe .seller-hero-verified {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;

    font-size: 7.4px !important;
    font-weight: 500 !important;

    color: rgba(255,255,255,.56) !important;
}

.seller-hero-admin-safe .seller-hero-verified svg {
    width: 13px !important;
    height: 13px !important;
    color: rgba(255,255,255,.54) !important;
}

/* Thin central separator, matching the Admin reference. */
.seller-hero-admin-safe .seller-hero-reference-divider {
    display: block !important;
    width: 1px !important;
    height: 76px !important;
    align-self: center !important;

    background: linear-gradient(
        to bottom,
        transparent,
        rgba(255,255,255,.13) 18%,
        rgba(255,255,255,.13) 82%,
        transparent
    ) !important;
}

/* Time / Date / Weather: flat, compact and aligned like the Admin card. */
.seller-hero-admin-safe .seller-hero-reference-meta {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0,1fr)) !important;
    align-items: center !important;

    min-width: 0 !important;
    gap: 0 !important;

    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
}

.seller-hero-admin-safe .seller-greeting-stat {
    display: block !important;
    min-width: 0 !important;

    padding: 8px 22px !important;

    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}

.seller-hero-admin-safe .seller-greeting-stat:first-child {
    padding-left: 0 !important;
}

.seller-hero-admin-safe .seller-greeting-stat:last-child {
    padding-right: 0 !important;
}

.seller-hero-admin-safe .seller-greeting-stat + .seller-greeting-stat {
    border-left: 1px solid rgba(255,255,255,.11) !important;
}

.seller-hero-admin-safe .seller-greeting-stat-heading {
    display: flex !important;
    align-items: center !important;
    gap: 7px !important;
}

.seller-hero-admin-safe .seller-greeting-stat-icon {
    display: block !important;
    width: 13px !important;
    height: 13px !important;
    flex: 0 0 13px !important;

    color: #dca01c !important;
    opacity: 1 !important;
}

.seller-hero-admin-safe .seller-greeting-stat-icon-weather {
    color: #9da9b4 !important;
}

.seller-hero-admin-safe .seller-greeting-stat-label {
    margin: 0 !important;

    font-size: 6.4px !important;
    line-height: 1 !important;
    font-weight: 700 !important;
    letter-spacing: .14em !important;
    text-transform: uppercase !important;

    color: rgba(255,255,255,.43) !important;
}

.seller-hero-admin-safe .seller-greeting-stat-copy {
    display: block !important;

    margin-top: 7px !important;
    padding-left: 20px !important;
}

.seller-hero-admin-safe .seller-greeting-stat-value {
    margin: 0 !important;

    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;

    font-size: 15px !important;
    line-height: 1 !important;
    font-weight: 700 !important;
    letter-spacing: -.03em !important;

    color: #ffffff !important;
}

.seller-hero-admin-safe .seller-greeting-stat-sub {
    margin: 5px 0 0 !important;

    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;

    font-size: 6.4px !important;
    line-height: 1.25 !important;
    font-weight: 400 !important;

    color: rgba(255,255,255,.50) !important;
}

/* Old actions are deliberately gone. */
.seller-hero-admin-safe .seller-hero-reference-actions,
.seller-hero-admin-safe .seller-hero-action {
    display: none !important;
}

/* Prevent older responsive rules from stretching the hero. */
@media (min-width: 1280px) {
    .seller-greeting-hero.seller-hero-reference.seller-hero-admin-safe {
        min-height: 0 !important;
        padding-top: 18px !important;
        padding-bottom: 18px !important;
    }

    .seller-hero-admin-safe .seller-hero-reference-layout {
        min-height: 102px !important;
    }
}

@media (min-width: 1024px) and (max-height: 820px) {
    .seller-greeting-hero.seller-hero-reference.seller-hero-admin-safe {
        min-height: 0 !important;
        padding-top: 15px !important;
        padding-bottom: 15px !important;
    }

    .seller-hero-admin-safe .seller-hero-reference-layout {
        min-height: 94px !important;
    }
}

@media (max-width: 1099px) {
    .seller-greeting-hero.seller-hero-reference.seller-hero-admin-safe {
        padding: 18px !important;
    }

    .seller-hero-admin-safe .seller-hero-reference-layout {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
        min-height: 0 !important;
    }

    .seller-hero-admin-safe .seller-hero-reference-divider {
        display: none !important;
    }

    .seller-hero-admin-safe .seller-hero-reference-meta {
        padding-top: 14px !important;
        border-top: 1px solid rgba(255,255,255,.09) !important;
    }
}

@media (max-width: 639px) {
    .seller-greeting-hero.seller-hero-reference.seller-hero-admin-safe {
        padding: 17px 15px !important;
        border-radius: 17px !important;
    }

    .seller-hero-admin-safe #sellerGreetingText.seller-hero-reference-title {
        font-size: 21px !important;
    }

    .seller-hero-admin-safe .seller-hero-reference-meta {
        grid-template-columns: 1fr !important;
    }

    .seller-hero-admin-safe .seller-greeting-stat,
    .seller-hero-admin-safe .seller-greeting-stat:first-child,
    .seller-hero-admin-safe .seller-greeting-stat:last-child {
        padding: 11px 0 !important;
    }

    .seller-hero-admin-safe .seller-greeting-stat + .seller-greeting-stat {
        border-top: 1px solid rgba(255,255,255,.08) !important;
        border-left: 0 !important;
    }
}

/* ==========================================================================
   SALES PERFORMANCE — FINAL PURE WHITE OVERRIDE
   --------------------------------------------------------------------------
   Must remain LAST in this stylesheet so older warm/cream Seller dashboard
   theme rules cannot re-apply --sari-warm-white to this card.
   ========================================================================== */

#sellerSalesPerformanceCard.seller-sales-admin-card,
#sellerSalesPerformanceCard.seller-sales-admin-card:hover,
#sellerSalesPerformanceCard {
    background: #FFFFFF !important;
    background-color: #FFFFFF !important;
    background-image: none !important;
}

/* KPI strip + chart area must also stay white. */
#sellerSalesPerformanceCard .seller-sales-admin-metrics,
#sellerSalesPerformanceCard .seller-sales-admin-chart-shell,
#sellerSalesPerformanceCard > div:nth-child(2),
#sellerSalesPerformanceCard > div:nth-child(3) {
    background: #FFFFFF !important;
    background-color: #FFFFFF !important;
    background-image: none !important;
}

/* Keep text/header/footer areas transparent over the white parent surface. */
#sellerSalesPerformanceCard .seller-sales-admin-head,
#sellerSalesPerformanceCard .seller-sales-admin-chart-head,
#sellerSalesPerformanceCard .seller-sales-admin-footer,
#sellerSalesPerformanceCard .seller-sales-admin-metric {
    background: transparent !important;
    background-color: transparent !important;
    background-image: none !important;
}
</style>
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
     * Wait until the browser is idle and reuse the persistent Seller shell's
     * already-warmed cache/promise whenever available.
     */
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

                if (cached && Array.isArray(cached.products)) {
                    finish(cached);
                    return;
                }

                const sharedPromise = window.__SARI_PRODUCTS_LIBRARY_PROMISE__;

                if (sharedPromise?.then) {
                    Promise.resolve(sharedPromise)
                        .then(finish)
                        .catch(function () { resolve([]); });
                    return;
                }

                fetch(
                    @json(route('seller.products.library')),
                    {
                        method: 'GET',
                        credentials: 'same-origin',
                        cache: 'default',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    }
                )
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Unable to prefetch the Product Library.');
                        }

                        return response.json();
                    })
                    .then(finish)
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
            style="background:#FFFFFF !important;background-color:#FFFFFF !important;background-image:none !important;"
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

            <div class="seller-sales-admin-metrics" style="background:#FFFFFF !important;background-color:#FFFFFF !important;background-image:none !important;">
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
                    <h4 class="seller-sales-admin-chart-title">Seller delivered sales trend</h4>
                    <p class="seller-sales-admin-chart-copy">
                        Each point represents one reporting period from delivered orders
                    </p>
                </div>

                <div class="seller-sales-admin-legend" aria-label="Chart series">
                    <span class="seller-sales-admin-legend-item">
                        <i aria-hidden="true"></i>
                        Sales
                    </span>
                </div>
            </div>

            <div id="sellerSalesChartShell" class="seller-sales-admin-chart-shell" style="background:#FFFFFF !important;background-color:#FFFFFF !important;background-image:none !important;">
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
                    viewBox="0 0 900 286"
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
                    <span class="seller-sales-admin-empty-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M5 19V13M10 19V9M15 19v-4M20 19V6"></path>
                        </svg>
                    </span>
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
                    @php
                        $activityType = (string) ($activity['type'] ?? 'activity');

                        $activityIconClass = match ($activityType) {
                            'product_created' => 'seller-activity-icon--created',
                            'product_edited' => 'seller-activity-icon--edited',
                            'order_ready' => 'seller-activity-icon--order',
                            default => 'seller-activity-icon--neutral',
                        };
                    @endphp

                    <a
                        href="{{ $activity['url'] ?? '#' }}"
                        class="seller-activity-item"
                        wire:navigate.hover
                    >
                        <span class="seller-activity-icon {{ $activityIconClass }}">
                            @if ($activityType === 'product_created')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M5 7h14l-1 13H6L5 7Z"></path>
                                    <path d="M9 7a3 3 0 0 1 6 0"></path>
                                    <path d="M12 11v5"></path>
                                    <path d="M9.5 13.5h5"></path>
                                </svg>
                            @elseif ($activityType === 'product_edited')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                </svg>
                            @elseif ($activityType === 'order_ready')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 6h11v10H4z"></path>
                                    <path d="M15 9h3l2 3v4h-5z"></path>
                                    <circle cx="8" cy="18" r="1.5"></circle>
                                    <circle cx="17" cy="18" r="1.5"></circle>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="8"></circle>
                                    <path d="M12 8v4l3 2"></path>
                                </svg>
                            @endif
                        </span>

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
                        <span class="seller-recent-avatar">
                            @if (!empty($order['buyer_avatar_url']))
                                <img
                                    src="{{ $order['buyer_avatar_url'] }}"
                                    alt="{{ $order['buyer_name'] ?? 'Buyer' }}"
                                    loading="lazy"
                                    decoding="async"
                                    referrerpolicy="no-referrer"
                                    onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                >
                                <span class="hidden">{{ $order['buyer_initials'] ?? 'SB' }}</span>
                            @else
                                <span>{{ $order['buyer_initials'] ?? 'SB' }}</span>
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
            : (point.y / 286) * sellerSalesChartShell.clientHeight - tooltipHeight - 8;

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
            left: 76,
            right: 858,
            top: 42,
            bottom: 242,
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
                stroke: index === 4 ? '#DFD8CF' : '#EEE9E3',
                'stroke-width': 1,
                'stroke-dasharray': index === 4 ? '0' : '4 7',
            }));

            sellerSalesGrid.appendChild(sellerSvgElement('text', {
                x: 14,
                y: y + 4,
                fill: '#9A9288',
                'font-size': 9,
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
                stroke: '#F3EFEA',
                'stroke-width': 1,
            }));

            const hoverGuide = sellerSvgElement('line', {
                x1: point.x,
                y1: chart.top,
                x2: point.x,
                y2: chart.bottom,
                stroke: '#D8C59E',
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
                r: 4.4,
                fill: '#FFFFFF',
                stroke: '#C79229',
                'stroke-width': 2,
            });
            sellerSalesPoints.appendChild(circle);

            sellerSalesXAxis.appendChild(sellerSvgElement('text', {
                x: point.x,
                y: 274,
                fill: '#9A9288',
                'font-size': count > 8 ? 8 : 9,
                'font-family': 'Poppins, sans-serif',
                'font-weight': 400,
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