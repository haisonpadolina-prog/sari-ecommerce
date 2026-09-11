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

    /* =========================================================
       SARI SELLER COMPLIANCE — CLEAN ENTERPRISE REFINEMENT
       Visual layer only. Existing backend and JS behavior retained.
       ========================================================= */

    .seller-compliance-page {
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        padding-bottom: 1.5rem;
    }

    /* Page heading */
    .compliance-page-header {
        margin-bottom: 16px;
    }

    .compliance-page-icon {
        box-shadow:
            0 2px 5px rgba(75, 54, 25, .03),
            0 9px 20px rgba(75, 54, 25, .06);
    }

    .compliance-page-title {
        font-size: clamp(1.75rem, 1.55rem + .5vw, 2.15rem);
        line-height: 1.08;
    }

    .compliance-page-subtitle {
        font-size: clamp(.73rem, .70rem + .08vw, .81rem);
        line-height: 1.65;
    }

    .compliance-account-control {
        min-height: 39px;
        font-size: clamp(.72rem, .69rem + .06vw, .78rem);
        box-shadow:
            0 2px 4px rgba(148, 98, 8, .04),
            0 8px 18px rgba(148, 98, 8, .16);
    }

    /* Summary cards — light floating depth, no selected-state noise */
    .seller-compliance-page .compliance-summary-grid {
        gap: 12px;
    }

    .seller-compliance-page .compliance-summary-card {
        min-height: 100px;
        border-radius: 16px !important;
        border-color: #e9e1d7 !important;
        background: #fff !important;
        box-shadow:
            0 2px 4px rgba(61, 43, 22, .03),
            0 10px 24px rgba(61, 43, 22, .06),
            0 20px 38px rgba(61, 43, 22, .025) !important;
    }

    .seller-compliance-page .compliance-summary-card:hover {
        transform: translateY(-1px);
        border-color: #ddcfb8 !important;
        box-shadow:
            0 3px 6px rgba(61, 43, 22, .035),
            0 14px 30px rgba(61, 43, 22, .075),
            0 24px 44px rgba(61, 43, 22, .03) !important;
    }

    .seller-compliance-page .compliance-summary-card > div > div:last-child {
        border-radius: 10px !important;
        box-shadow: 0 4px 12px rgba(61, 43, 22, .035);
    }

    /* Main moderation workspace */
    .seller-compliance-page .compliance-workspace {
        border-radius: 18px !important;
        border-color: #e9e1d7 !important;
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .035),
            0 13px 30px rgba(61, 43, 22, .07),
            0 26px 52px rgba(61, 43, 22, .03) !important;
    }

    .seller-compliance-page .compliance-workspace-tabs {
        padding: 10px 12px !important;
        background: #faf9f6 !important;
    }

    .seller-compliance-page .compliance-workspace-tabs > div {
        gap: 5px !important;
    }

    .seller-compliance-page .compliance-tab {
        min-height: 38px;
        border-radius: 9px !important;
        border-color: transparent !important;
        background: transparent !important;
        padding-inline: 12px !important;
        color: #756d63;
        font-weight: 600;
    }

    .seller-compliance-page .compliance-tab:hover {
        background: #fff !important;
        color: #51483f;
    }

    .seller-compliance-page .compliance-tab[data-active="true"] {
        border-color: #e7d4aa !important;
        background: #fff9ee !important;
        color: #9a6706 !important;
        box-shadow:
            0 1px 2px rgba(61, 43, 22, .025),
            0 5px 12px rgba(100, 72, 28, .05);
    }

    .seller-compliance-page .compliance-tab span {
        box-shadow: none !important;
        border: 1px solid #eee7dd;
    }

    /* Search and filter controls */
    .seller-compliance-page .compliance-control {
        min-height: 39px !important;
        border-radius: 10px !important;
        border-color: #e5ddd2 !important;
        box-shadow: none !important;
        font-size: clamp(.74rem, .71rem + .06vw, .80rem) !important;
    }

    .seller-compliance-page .compliance-control:hover {
        border-color: #d9cbbb !important;
    }

    .seller-compliance-page .compliance-control:focus {
        border-color: #d49a2b !important;
        box-shadow: 0 0 0 3px rgba(217,149,0,.075) !important;
    }

    /* Flagged seller list */
    .compliance-seller-row {
        border-color: #ebe4da !important;
        box-shadow:
            0 1px 2px rgba(61, 43, 22, .018),
            0 7px 18px rgba(61, 43, 22, .04);
    }

    .compliance-seller-row:hover {
        transform: none !important;
        border-color: #ddcfb8 !important;
        background: #fffdfa !important;
        box-shadow:
            0 2px 4px rgba(61, 43, 22, .025),
            0 10px 24px rgba(61, 43, 22, .055);
    }

    .compliance-seller-row [data-flagged-seller-open] {
        width: 38px;
        padding-inline: 0 !important;
        border-radius: 10px !important;
        background: #fff !important;
        box-shadow: 0 4px 10px rgba(61,43,22,.03);
    }

    .compliance-seller-row [data-flagged-seller-open]:hover {
        background: #fff9ee !important;
    }

    /* Other compliance cards */
    .seller-compliance-page .compliance-card {
        border-radius: 16px !important;
        border-color: #e9e1d7 !important;
        box-shadow:
            0 2px 4px rgba(61,43,22,.025),
            0 9px 22px rgba(61,43,22,.045) !important;
    }

    .seller-compliance-page .compliance-card:hover {
        transform: none !important;
        border-color: #ddcfb8 !important;
        box-shadow:
            0 2px 5px rgba(61,43,22,.03),
            0 12px 26px rgba(61,43,22,.06) !important;
    }

    /* =========================================================
       SELLER REVIEW MODAL
       ========================================================= */
    .seller-review-modal {
        background: rgba(27, 22, 17, .34) !important;
        backdrop-filter: blur(2px);
    }

    .seller-review-dialog {
        max-width: 1120px !important;
        max-height: 90vh !important;
        border-radius: 20px !important;
        border-color: #e7dfd5 !important;
        box-shadow:
            0 18px 45px rgba(31, 24, 17, .14),
            0 38px 90px rgba(31, 24, 17, .16) !important;
    }

    .seller-review-header {
        padding-top: 16px !important;
        padding-bottom: 16px !important;
        background: #fff !important;
    }

    .seller-review-header > div:first-child > div:first-child {
        border-radius: 11px !important;
        box-shadow: 0 5px 13px rgba(38, 31, 24, .08);
    }

    .seller-review-header h3 {
        font-size: clamp(1rem, .95rem + .18vw, 1.16rem) !important;
        letter-spacing: -.02em;
    }

    .seller-review-header form button,
    .seller-review-header [data-flagged-seller-close] {
        min-height: 37px !important;
        border-radius: 9px !important;
    }

    .seller-review-body {
        background: #f8f7f4 !important;
        padding: 16px !important;
        scrollbar-width: thin;
        scrollbar-color: #cfc7bd transparent;
    }

    .seller-review-body::-webkit-scrollbar {
        width: 6px;
    }

    .seller-review-body::-webkit-scrollbar-thumb {
        background: #cfc7bd;
        border-radius: 999px;
    }

    .seller-review-stat {
        border-radius: 12px !important;
        border-color: #e7dfd5 !important;
        background: #fff !important;
        box-shadow:
            0 1px 2px rgba(61,43,22,.02),
            0 5px 14px rgba(61,43,22,.035);
    }

    .seller-review-product {
        border-radius: 16px !important;
        border-color: #e5ddd2 !important;
        box-shadow:
            0 2px 4px rgba(61,43,22,.025),
            0 10px 24px rgba(61,43,22,.05) !important;
    }

    .seller-review-product > div:first-child {
        padding: 16px !important;
    }

    .seller-review-product > div:first-child > div:first-child {
        border-radius: 12px !important;
        background: #f7f5f1 !important;
    }

    .seller-review-product .grid.grid-cols-2.gap-2.sm\:grid-cols-4 > div {
        border: 1px solid #eee8df;
        background: #faf9f6 !important;
        border-radius: 10px !important;
    }

    /* Screening results are structured and calm rather than large tinted boxes */
    .seller-screening-grid {
        gap: 10px !important;
        background: #faf9f6 !important;
    }

    .screening-panel {
        position: relative;
        overflow: hidden;
        border-radius: 12px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .screening-panel::before {
        content: "";
        position: absolute;
        left: 0;
        top: 12px;
        bottom: 12px;
        width: 3px;
        border-radius: 0 999px 999px 0;
    }

    .screening-panel-local {
        border-color: #ead9d7 !important;
    }

    .screening-panel-local::before {
        background: #bd6b62;
    }

    .screening-panel-ai {
        border-color: #e7ddc8 !important;
        background: #fffdfa !important;
    }

    .screening-panel-ai::before {
        background: #d99500;
    }

    .screening-panel-ai > div:first-child > p:first-child {
        color: #8f6418 !important;
    }

    .screening-panel-ai .rounded-xl.border {
        border-color: #ebe3d8 !important;
        background: #fff !important;
    }

    .seller-review-product details > summary {
        min-height: 50px;
        background: #fff;
    }

    .seller-review-product details[open] > summary {
        background: #faf9f6;
    }

    .seller-review-actions {
        gap: 7px !important;
        background: #fff !important;
    }

    .seller-review-actions button {
        min-height: 38px !important;
        border-radius: 9px !important;
    }

    .seller-review-actions form:first-child button {
        background: #f4f9f5 !important;
        border-color: #d5e5da !important;
    }

    .seller-review-actions form:nth-child(2) button {
        background: #fff !important;
        border-color: #e4ddd3 !important;
    }

    .seller-review-actions > button {
        background: #b8685f !important;
        box-shadow: 0 5px 12px rgba(168, 92, 84, .13);
    }

    /* Warning dialog */
    .warning-modal-backdrop {
        background: rgba(27,22,17,.32) !important;
        backdrop-filter: blur(2px);
    }

    .warning-modal-dialog {
        max-width: 590px !important;
        border-radius: 18px !important;
        border-color: #e7dfd5 !important;
        box-shadow:
            0 16px 40px rgba(31,24,17,.13),
            0 34px 76px rgba(31,24,17,.14) !important;
    }

    /* Table/panel polish */
    .seller-compliance-page table thead {
        background: #faf9f6;
    }

    .seller-compliance-page table tbody tr {
        transition: background-color .14s ease;
    }

    .seller-compliance-page table tbody tr:hover {
        background: #fffdfa;
    }

    @media (max-width: 767px) {
        .compliance-page-header {
            align-items: stretch;
        }

        .compliance-page-title {
            font-size: 1.65rem;
        }

        .compliance-account-control {
            width: 100%;
        }

        .seller-compliance-page .compliance-summary-card,
        .seller-compliance-page .compliance-workspace,
        .seller-compliance-page .compliance-card {
            box-shadow:
                0 2px 5px rgba(61,43,22,.03),
                0 10px 24px rgba(61,43,22,.055) !important;
        }

        .seller-review-dialog {
            max-height: 94vh !important;
            border-radius: 16px !important;
        }

        .seller-review-body {
            padding: 12px !important;
        }
    }


    /* =========================================================
       SELLER MODAL — LAYOUT REWORK
       ========================================================= */

    .seller-review-dialog {
        width: min(1160px, calc(100vw - 32px)) !important;
        max-width: 1160px !important;
    }

    .seller-review-header {
        position: relative;
        z-index: 2;
        box-shadow: 0 1px 0 rgba(61,43,22,.045);
    }

    .seller-review-identity {
        align-items: center;
    }

    .seller-review-identity > div:first-child {
        width: 46px;
        height: 46px;
        border-radius: 14px !important;
        box-shadow:
            0 2px 4px rgba(37,29,19,.04),
            0 8px 18px rgba(37,29,19,.08);
    }

    .seller-review-header form button {
        box-shadow:
            0 2px 4px rgba(70,55,87,.03),
            0 8px 18px rgba(70,55,87,.08);
    }

    .seller-review-header [data-flagged-seller-close] {
        box-shadow:
            0 1px 2px rgba(61,43,22,.025),
            0 5px 12px rgba(61,43,22,.04);
    }

    .seller-review-stats-grid {
        margin-bottom: 2px;
    }

    .seller-review-stat {
        min-height: 84px;
        padding: 14px !important;
    }

    .seller-review-stat p:first-child {
        font-size: .76rem !important;
        color: #8c8377 !important;
    }

    .seller-review-stat p:last-child {
        margin-top: .3rem !important;
        font-size: 1.08rem !important;
        letter-spacing: -.02em;
    }

    .seller-review-listings {
        gap: 16px !important;
    }

    .seller-review-product {
        border-radius: 18px !important;
        overflow: hidden;
    }

    .seller-review-product-intro {
        background:
            linear-gradient(180deg, #fffdfa 0%, #fcfbf8 100%) !important;
    }

    .seller-review-product-summary {
        display: grid !important;
        grid-template-columns: 118px minmax(0, 1fr);
        align-items: start;
    }

    .seller-review-product-image {
        width: 118px !important;
        height: 118px !important;
        box-shadow:
            0 2px 5px rgba(61,43,22,.03),
            0 8px 18px rgba(61,43,22,.04);
    }

    .seller-review-product-main {
        min-width: 0;
    }

    .seller-review-product-main h4 {
        font-size: 1.02rem !important;
        line-height: 1.2 !important;
        letter-spacing: -.02em;
    }

    .seller-review-product-main > div:first-child > div:first-child > p {
        font-size: .86rem !important;
    }

    .seller-review-product-stats {
        gap: 10px !important;
    }

    .seller-review-product-metric {
        border: 1px solid #eee7de;
        border-radius: 12px !important;
        background: #faf9f6 !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
    }

    .seller-review-product-metric p:first-child {
        color: #91887d !important;
    }

    .seller-review-subsection {
        background: #fff !important;
    }

    .seller-screening-grid {
        gap: 12px !important;
        border-top: none !important;
        padding-top: 12px !important;
        align-items: stretch;
    }

    .screening-panel {
        display: flex;
        flex-direction: column;
        min-height: 235px;
        border-radius: 14px !important;
        padding: 16px !important;
        box-shadow:
            0 1px 2px rgba(61,43,22,.02),
            0 6px 16px rgba(61,43,22,.035) !important;
    }

    .screening-panel::before {
        top: 14px;
        bottom: auto;
        width: 4px;
        height: 28px;
        border-radius: 0 999px 999px 0;
    }

    .screening-panel-local {
        background: #fffefe !important;
    }

    .screening-panel-ai {
        background: #fffdf9 !important;
    }

    .screening-panel > div:first-child {
        margin-bottom: 8px;
    }

    .screening-panel > div:first-child p {
        font-size: .86rem !important;
        letter-spacing: -.01em;
    }

    .screening-panel > p {
        flex: 1 1 auto;
    }

    .screening-panel .rounded-xl.border {
        border-radius: 11px !important;
        min-height: 70px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .seller-review-product details {
        border-top: 1px solid #eee8df !important;
    }

    .seller-review-product details > summary {
        min-height: 56px;
        padding-top: 13px !important;
        padding-bottom: 13px !important;
    }

    .seller-review-product details > div {
        background: #fff !important;
    }

    .seller-review-product details .rounded-xl.border {
        border-radius: 12px !important;
    }

    .seller-review-actions {
        gap: 10px !important;
        padding-top: 12px !important;
    }

    .seller-action-btn {
        min-height: 42px !important;
        border-radius: 11px !important;
        box-shadow:
            0 1px 2px rgba(61,43,22,.02),
            0 5px 12px rgba(61,43,22,.04);
    }

    .seller-action-approve:hover,
    .seller-action-reject:hover,
    .seller-action-warn:hover {
        transform: translateY(-1px);
    }

    .seller-action-warn {
        box-shadow:
            0 2px 4px rgba(168,92,84,.05),
            0 8px 18px rgba(168,92,84,.16) !important;
    }

    @media (max-width: 767px) {
        .seller-review-dialog {
            width: min(100vw - 16px, 1160px) !important;
        }

        .seller-review-product-summary {
            grid-template-columns: 1fr !important;
        }

        .seller-review-product-image {
            width: 100% !important;
            height: 190px !important;
        }

        .screening-panel {
            min-height: 0;
        }
    }

    /* =========================================================
       FLAGGED SELLERS — APPROVED ACCOUNTS INSPIRED
       Clean table rhythm, low-noise filters, minimal tab rail.
       Backend data attributes and moderation actions are unchanged.
       ========================================================= */

    .seller-compliance-page .compliance-workspace-tabs {
        padding: 0 20px !important;
        background: #fff !important;
    }

    .seller-compliance-page .compliance-workspace-tabs > div {
        gap: 4px !important;
    }

    .seller-compliance-page .compliance-tab {
        position: relative;
        min-height: 52px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding-inline: 12px !important;
        color: #7a7268 !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .compliance-tab:hover {
        background: transparent !important;
        color: #39332d !important;
    }

    .seller-compliance-page .compliance-tab[data-active="true"] {
        border: 0 !important;
        background: transparent !important;
        color: #a66f08 !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .compliance-tab[data-active="true"]::after {
        content: "";
        position: absolute;
        right: 12px;
        bottom: 0;
        left: 12px;
        height: 2px;
        border-radius: 999px 999px 0 0;
        background: #d99500;
    }

    .seller-compliance-page .compliance-tab span {
        border: 0 !important;
        background: #f4f1ec !important;
        color: #81786d !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .compliance-tab[data-active="true"] span {
        background: #fff4dc !important;
        color: #a66f08 !important;
    }

    .seller-compliance-page .compliance-workspace-tabs {
        background: #faf9f6 !important;
    }

    .compliance-view-filter-shell {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 10px;
        border: 1px solid #e9e1d7;
        border-radius: 16px;
        background: #fff;
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .025),
            0 10px 24px rgba(61, 43, 22, .045);
    }

    .compliance-view-select {
        min-width: 0;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .compliance-view-select:hover {
        border-color: #d8cbbb !important;
    }

    .compliance-view-select:focus {
        border-color: #d49a2b !important;
        box-shadow: 0 0 0 3px rgba(217, 149, 0, .075);
    }

    .compliance-view-apply,
    .compliance-view-reset {
        flex: 0 0 auto;
        white-space: nowrap;
    }

    @media (max-width: 639px) {
        .compliance-view-filter-shell {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .compliance-view-filter-shell > div:first-child {
            grid-column: 1 / -1;
        }

        .compliance-view-apply,
        .compliance-view-reset {
            width: 100%;
        }
    }

    .flagged-panel-header {
        background: #fff;
    }

    .flagged-toolbar-shell {
        padding: 14px;
        border: 1px solid #e9e1d7;
        border-radius: 18px;
        background: #fff;
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .025),
            0 10px 24px rgba(61, 43, 22, .045);
    }

    .flagged-filter-field {
        min-height: 48px !important;
        border-radius: 12px !important;
        border-color: #e5ddd2 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .flagged-list-shell {
        overflow: hidden;
        border: 1px solid #e8e1d8;
        border-radius: 18px;
        background: #fff;
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .025),
            0 10px 24px rgba(61, 43, 22, .04);
    }

    .flagged-filter-field:hover {
        border-color: #d8cbbb !important;
    }

    .flagged-filter-field:focus {
        border-color: #d49a2b !important;
        box-shadow: 0 0 0 3px rgba(217, 149, 0, .075) !important;
    }

    #clearFlaggedSellerFilters:not(.hidden) {
        display: flex;
    }

    .flagged-table-head {
        color: #7f776d;
        letter-spacing: .025em;
    }

    #flaggedSellerList {
        background: #fff;
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .025),
            0 10px 24px rgba(61, 43, 22, .04);
    }

    .seller-compliance-page .flagged-seller-row-modern.compliance-seller-row {
        border: 0 !important;
        border-bottom: 1px solid #eee9e2 !important;
        border-radius: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
        transform: none !important;
    }

    .seller-compliance-page .flagged-seller-row-modern.compliance-seller-row:hover {
        border-color: #eee9e2 !important;
        background: #fffdfa !important;
        box-shadow: none !important;
        transform: none !important;
    }

    #flaggedSellerList > article:last-of-type {
        border-bottom: 0 !important;
    }

    .flagged-seller-avatar {
        border: 1px solid #ebe7e1;
        background: #f4f2ee;
        color: #5e574f;
    }

    .flagged-count-box {
        border: 1px solid #f0dddd;
        background: #fff7f7;
        color: #a65d5d;
    }

    .flagged-review-button {
        width: 38px !important;
        min-width: 38px !important;
        height: 38px !important;
        min-height: 38px !important;
        padding: 0 !important;
        border-radius: 10px !important;
        border: 1px solid #e6ded3 !important;
        background: #fff !important;
        color: #6c6258 !important;
        box-shadow:
            0 1px 2px rgba(61, 43, 22, .02),
            0 4px 10px rgba(61, 43, 22, .035);
    }

    .flagged-review-button:hover {
        border-color: #d9c9af !important;
        background: #fff9ee !important;
        color: #9b6915 !important;
    }

    @media (max-width: 1279px) {
        #flaggedSellerList {
            border: 0 !important;
            background: transparent;
            box-shadow: none;
        }

        .seller-compliance-page .flagged-seller-row-modern.compliance-seller-row {
            margin-bottom: 12px;
            border: 1px solid #e9e1d7 !important;
            border-radius: 16px !important;
            box-shadow: 0 5px 16px rgba(61, 43, 22, .035) !important;
        }

        .seller-compliance-page .flagged-seller-row-modern.compliance-seller-row:last-of-type {
            margin-bottom: 0;
        }
    }


    /* =========================================================
       FINAL FILTER + TABLE LAYOUT
       Matches Approved Accounts hierarchy: one filter bar, list below.
       ========================================================= */
    .compliance-master-filter {
        display: grid;
        grid-template-columns: minmax(360px, 1fr) 210px 190px auto auto;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 10px;
        border: 1px solid #e9e1d7;
        border-radius: 16px;
        background: #fff;
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .025),
            0 10px 24px rgba(61, 43, 22, .045);
    }

    .compliance-master-filter .master-filter-control {
        min-height: 44px !important;
        border-radius: 11px !important;
        border: 1px solid #e5ddd2 !important;
        background: #fff !important;
        box-shadow: none !important;
        color: #4f473f !important;
        transition: border-color .18s ease, box-shadow .18s ease, opacity .18s ease;
    }

    .compliance-master-filter .master-filter-control:hover:not(:disabled) {
        border-color: #d8cbbb !important;
    }

    .compliance-master-filter .master-filter-control:focus {
        outline: none;
        border-color: #d49a2b !important;
        box-shadow: 0 0 0 3px rgba(217,149,0,.075) !important;
    }

    .compliance-master-filter .master-filter-control:disabled {
        cursor: not-allowed;
        opacity: .52;
        background: #faf9f6 !important;
    }

    .compliance-master-filter .master-filter-apply,
    .compliance-master-filter .master-filter-reset {
        min-height: 44px !important;
        white-space: nowrap;
    }

    .flagged-list-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 13px 4px 12px;
        color: #91887d;
    }

    .flagged-list-shell {
        overflow: hidden;
        border: 1px solid #e8e1d8;
        border-radius: 18px;
        background: #fff;
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .025),
            0 10px 24px rgba(61, 43, 22, .04);
    }

    @media (max-width: 1280px) {
        .compliance-master-filter {
            grid-template-columns: minmax(280px, 1fr) 190px 180px auto auto;
        }
    }

    @media (max-width: 1023px) {
        .compliance-master-filter {
            grid-template-columns: minmax(0, 1fr) 1fr;
        }

        .compliance-master-filter .master-search {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 639px) {
        .compliance-master-filter {
            grid-template-columns: 1fr;
            padding: 8px;
        }

        .compliance-master-filter .master-search {
            grid-column: auto;
        }

        .compliance-master-filter .master-filter-apply,
        .compliance-master-filter .master-filter-reset {
            width: 100%;
        }

        .flagged-list-meta {
            align-items: flex-start;
            flex-direction: column;
        }
    }



    /* =========================================================
       USER MANAGEMENT LAYOUT PARITY — FINAL OVERRIDES
       Mirrors Approved Accounts: separate filter surface + separate data surface.
       ========================================================= */
    .seller-compliance-page .compliance-workspace {
        overflow: visible !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .compliance-workspace-tabs {
        padding: 12px !important;
        border: 1px solid #e9e1d7 !important;
        border-radius: 18px !important;
        background: #fff !important;
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .035),
            0 12px 28px rgba(61, 43, 22, .07),
            0 24px 50px rgba(61, 43, 22, .032) !important;
    }

    .seller-compliance-page .compliance-master-filter {
        display: grid;
        grid-template-columns: minmax(360px, 1fr) 205px 185px auto auto;
        gap: 10px;
        align-items: center;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .master-filter-control {
        min-height: 44px !important;
        border: 1px solid #e8e0d5 !important;
        border-radius: 12px !important;
        background: #fff !important;
        color: #332c25 !important;
        font-size: .64rem !important;
        box-shadow:
            0 1px 2px rgba(61, 43, 22, .018),
            0 4px 10px rgba(61, 43, 22, .025) !important;
    }

    .seller-compliance-page .master-filter-control:focus {
        outline: none;
        border-color: #d9a33a !important;
        box-shadow:
            0 0 0 4px rgba(217,149,0,.08),
            0 6px 16px rgba(61, 43, 22, .045) !important;
    }

    .seller-compliance-page .master-filter-apply,
    .seller-compliance-page .master-filter-reset {
        min-height: 44px !important;
        border-radius: 12px !important;
        font-size: .62rem !important;
        font-weight: 600 !important;
    }

    .seller-compliance-page .master-filter-apply {
        box-shadow: 0 10px 22px rgba(217,149,0,.18) !important;
    }

    /* Each queue becomes its own card, like the Users table surface. */
    .seller-compliance-page [data-compliance-panel] {
        margin-top: 16px;
        overflow: hidden;
        border: 1px solid #e9e1d7;
        border-radius: 18px;
        background: #fff;
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .035),
            0 12px 28px rgba(61, 43, 22, .07),
            0 24px 50px rgba(61, 43, 22, .032);
    }

    .seller-compliance-page [data-compliance-panel][hidden] {
        display: none !important;
    }

    /* Flagged Sellers table: no nested floating card. */
    #compliancePanel-flagged .flagged-table-wrap {
        margin: 0 !important;
    }

    #compliancePanel-flagged .flagged-list-shell {
        overflow-x: auto;
        border: 0 !important;
        border-radius: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    #compliancePanel-flagged #flaggedSellerList {
        min-width: 1050px;
        border: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    #compliancePanel-flagged .flagged-table-head {
        min-width: 1050px;
        border-bottom: 1px solid #eee8df !important;
        background: #fcfbf8 !important;
        padding: 14px 20px !important;
        color: #847b70 !important;
        font-size: .56rem !important;
        font-weight: 700 !important;
        letter-spacing: .08em !important;
        text-transform: uppercase;
    }

    @media (min-width: 1280px) {
        #compliancePanel-flagged .flagged-table-head,
        #compliancePanel-flagged .flagged-seller-row-modern > div {
            grid-template-columns: minmax(320px, 1.85fr) 150px 125px 140px 175px 78px !important;
            gap: 16px !important;
        }
    }

    .seller-compliance-page .flagged-seller-row-modern.compliance-seller-row {
        margin: 0 !important;
        border: 0 !important;
        border-bottom: 1px solid #f0ebe4 !important;
        border-radius: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .flagged-seller-row-modern.compliance-seller-row:hover {
        background: #fdfbf7 !important;
    }

    .seller-compliance-page .flagged-seller-row-modern > div > div {
        min-height: 72px;
    }

    .seller-compliance-page .flagged-seller-row-modern .flagged-seller-avatar {
        width: 40px !important;
        height: 40px !important;
        border: 0 !important;
        border-radius: 999px !important;
        background: #f3f1ed !important;
        color: #655d55 !important;
    }

    .seller-compliance-page .flagged-seller-row-modern p.truncate.text-sm {
        font-size: .64rem !important;
        color: #2e2924 !important;
    }

    .seller-compliance-page .flagged-seller-row-modern p.text-xs,
    .seller-compliance-page .flagged-seller-row-modern span.text-xs {
        font-size: .54rem !important;
    }

    .seller-compliance-page .flagged-review-button {
        width: 36px !important;
        min-width: 36px !important;
        height: 36px !important;
        min-height: 36px !important;
        border: 1px solid #e6dfd6 !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: #6f675e !important;
        box-shadow:
            0 2px 4px rgba(52, 41, 27, .025),
            0 6px 14px rgba(52, 41, 27, .04) !important;
    }

    .seller-compliance-page .flagged-review-button:hover {
        transform: translateY(-1px);
        border-color: #d8c8b1 !important;
        background: #fffaf2 !important;
        color: #9c6c1f !important;
    }

    .flagged-table-footer {
        background: #fff;
    }

    #flaggedSellerFilterEmpty {
        border-top: 1px solid #eee8df;
    }

    /* Other queues use the same surface rhythm as the Users table. */
    #compliancePanel-pending > div:first-child,
    #compliancePanel-warnings > div:first-child,
    #compliancePanel-suspended > div:first-child,
    #compliancePanel-messages > div:first-child {
        background: #fcfbf8;
    }

    @media (max-width: 1279px) {
        .seller-compliance-page .compliance-master-filter {
            grid-template-columns: minmax(0, 1fr) 190px 175px auto auto;
        }

        #compliancePanel-flagged #flaggedSellerList {
            min-width: 0;
        }

        .seller-compliance-page .flagged-seller-row-modern.compliance-seller-row {
            margin: 12px !important;
            border: 1px solid #e9e1d7 !important;
            border-radius: 16px !important;
            box-shadow: 0 5px 16px rgba(61, 43, 22, .035) !important;
        }
    }

    @media (max-width: 1023px) {
        .seller-compliance-page .compliance-master-filter {
            grid-template-columns: 1fr 1fr;
        }

        .seller-compliance-page .compliance-master-filter .master-search {
            grid-column: 1 / -1;
        }

        .seller-compliance-page .master-filter-apply,
        .seller-compliance-page .master-filter-reset {
            width: 100%;
        }
    }

    @media (max-width: 639px) {
        .seller-compliance-page .compliance-master-filter {
            grid-template-columns: 1fr;
        }

        .seller-compliance-page .compliance-master-filter .master-search {
            grid-column: auto;
        }
    }


    /* =========================================================
       USER-STYLE FILTER DROPDOWNS
       Mirrors the Approved Accounts dropdown treatment.
       ========================================================= */
    .seller-compliance-page .filter-dropdown {
        position: relative;
        min-width: 0;
    }

    .seller-compliance-page .filter-dropdown-toggle {
        display: flex;
        width: 100%;
        min-height: 44px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border: 1px solid #e8e0d5;
        border-radius: 12px;
        background: #fff;
        padding: 0 14px;
        color: #332c25;
        font-size: .68rem;
        font-weight: 500;
        line-height: 1;
        box-shadow:
            0 1px 2px rgba(61, 43, 22, .018),
            0 4px 10px rgba(61, 43, 22, .025);
        transition:
            border-color .16s ease,
            box-shadow .16s ease,
            background-color .16s ease,
            color .16s ease;
    }

    .seller-compliance-page .filter-dropdown-toggle:hover:not(:disabled) {
        border-color: #d8c8b1;
        background: #fffdfa;
    }

    .seller-compliance-page .filter-dropdown-toggle:focus-visible,
    .seller-compliance-page .filter-dropdown.is-open .filter-dropdown-toggle {
        outline: none;
        border-color: #d9a33a;
        box-shadow:
            0 0 0 4px rgba(217,149,0,.08),
            0 6px 16px rgba(61,43,22,.045);
    }

    .seller-compliance-page .filter-dropdown-toggle:disabled {
        cursor: not-allowed;
        opacity: .48;
        background: #faf9f6;
    }

    .seller-compliance-page .filter-dropdown-menu {
        position: absolute;
        top: calc(100% + 6px);
        right: 0;
        left: 0;
        z-index: 80;
        padding: 6px;
        border: 1px solid #e7dfd4;
        border-radius: 14px;
        background: #fff;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-5px) scale(.985);
        transform-origin: top;
        box-shadow:
            0 8px 18px rgba(47,37,25,.08),
            0 20px 42px rgba(47,37,25,.12);
        transition:
            opacity .14s ease,
            transform .14s ease,
            visibility .14s ease;
    }

    .seller-compliance-page .filter-dropdown.is-open .filter-dropdown-menu {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0) scale(1);
    }

    .seller-compliance-page .filter-dropdown-option {
        display: flex;
        width: 100%;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border-radius: 10px;
        padding: 10px 11px;
        color: #5c534a;
        font-size: .66rem;
        font-weight: 500;
        text-align: left;
        transition: background-color .14s ease, color .14s ease;
    }

    .seller-compliance-page .filter-dropdown-option:hover,
    .seller-compliance-page .filter-dropdown-option.is-selected {
        background: #fff7e8;
        color: #a8731f;
    }

    .seller-compliance-page .filter-dropdown-check {
        width: 6px;
        height: 6px;
        flex: 0 0 auto;
        border-radius: 999px;
        background: #d99500;
        opacity: 0;
    }

    .seller-compliance-page .filter-dropdown-option.is-selected .filter-dropdown-check {
        opacity: 1;
    }

    .seller-compliance-page .filter-risk-dot {
        width: 7px;
        height: 7px;
        flex: 0 0 auto;
        border-radius: 999px;
        background: #d99500;
    }

    .seller-compliance-page .filter-dropdown-chevron {
        width: 14px;
        height: 14px;
        flex: 0 0 auto;
        color: #8b8175;
        transition: transform .14s ease;
    }

    .seller-compliance-page .filter-dropdown.is-open .filter-dropdown-chevron {
        transform: rotate(180deg);
    }

    /* Only the result summary is slightly smaller. */
    #flaggedSellerResultCount {
        font-size: .66rem !important;
        line-height: 1.4 !important;
        font-weight: 400 !important;
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-compliance-page .filter-dropdown-menu,
        .seller-compliance-page .filter-dropdown-chevron {
            transition: none !important;
        }
    }


    /* =========================================================
       STRONG FLOATING DEPTH + PREMIUM SELLER REVIEW MODAL
       Final visual layer only. Backend routes/forms remain intact.
       ========================================================= */

    /* Main page surfaces */
    .seller-compliance-page .compliance-summary-card {
        border-color: #e7ddd1 !important;
        box-shadow:
            0 3px 7px rgba(61, 43, 22, .04),
            0 15px 34px rgba(61, 43, 22, .085),
            0 30px 58px rgba(61, 43, 22, .038),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .seller-compliance-page .compliance-summary-card:hover {
        transform: translateY(-3px) !important;
        border-color: #d9c9b1 !important;
        box-shadow:
            0 4px 9px rgba(61, 43, 22, .05),
            0 21px 46px rgba(61, 43, 22, .115),
            0 40px 76px rgba(61, 43, 22, .048),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .seller-compliance-page .compliance-workspace-tabs {
        border-color: #e7ddd1 !important;
        box-shadow:
            0 3px 8px rgba(61, 43, 22, .045),
            0 18px 42px rgba(61, 43, 22, .095),
            0 38px 78px rgba(61, 43, 22, .045),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .seller-compliance-page [data-compliance-panel] {
        border-color: #e7ddd1 !important;
        box-shadow:
            0 3px 8px rgba(61, 43, 22, .045),
            0 18px 42px rgba(61, 43, 22, .095),
            0 38px 78px rgba(61, 43, 22, .045),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .seller-compliance-page .compliance-card {
        box-shadow:
            0 3px 7px rgba(61,43,22,.035),
            0 14px 32px rgba(61,43,22,.075),
            0 26px 50px rgba(61,43,22,.03) !important;
    }

    .seller-compliance-page .compliance-card:hover {
        transform: translateY(-2px) !important;
        box-shadow:
            0 4px 8px rgba(61,43,22,.04),
            0 18px 38px rgba(61,43,22,.095),
            0 32px 58px rgba(61,43,22,.035) !important;
    }

    .seller-compliance-page .filter-dropdown-toggle,
    .seller-compliance-page .master-filter-control {
        box-shadow:
            0 2px 4px rgba(61,43,22,.025),
            0 7px 16px rgba(61,43,22,.045),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .seller-compliance-page .filter-dropdown-menu {
        box-shadow:
            0 8px 18px rgba(47,37,25,.09),
            0 24px 52px rgba(47,37,25,.15) !important;
    }

    .seller-compliance-page .master-filter-apply {
        box-shadow:
            0 3px 7px rgba(183,124,0,.10),
            0 13px 28px rgba(217,149,0,.23) !important;
    }

    .seller-compliance-page .master-filter-apply:hover {
        transform: translateY(-1px);
        box-shadow:
            0 4px 8px rgba(183,124,0,.12),
            0 16px 34px rgba(183,124,0,.26) !important;
    }

    .seller-compliance-page .master-filter-reset {
        box-shadow:
            0 2px 4px rgba(61,43,22,.025),
            0 7px 16px rgba(61,43,22,.045) !important;
    }

    .seller-compliance-page .flagged-review-button {
        box-shadow:
            0 2px 4px rgba(52,41,27,.03),
            0 8px 18px rgba(52,41,27,.055),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .seller-compliance-page .flagged-review-button:hover {
        transform: translateY(-2px) !important;
        box-shadow:
            0 3px 6px rgba(52,41,27,.04),
            0 12px 26px rgba(88,64,31,.10) !important;
    }

    /* =========================================================
       VIEW SELLER — PREMIUM MODAL
       ========================================================= */

    @keyframes sellerReviewBackdropIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes sellerReviewDialogIn {
        from {
            opacity: 0;
            transform: translate3d(0, 14px, 0) scale(.985);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0) scale(1);
        }
    }

    .seller-review-modal {
        background: rgba(28, 23, 18, .48) !important;
        backdrop-filter: blur(7px);
        -webkit-backdrop-filter: blur(7px);
    }

    .seller-review-modal:not(.hidden) {
        animation: sellerReviewBackdropIn .18s ease both;
    }

    .seller-review-modal:not(.hidden) .seller-review-dialog {
        animation: sellerReviewDialogIn .24s cubic-bezier(.22,.72,.24,1) both;
    }

    .seller-review-dialog {
        position: relative;
        width: min(1180px, calc(100vw - 34px)) !important;
        max-width: 1180px !important;
        max-height: min(92vh, 920px) !important;
        overflow: hidden;
        border: 1px solid #dfd5c8 !important;
        border-radius: 24px !important;
        background: #fff !important;
        box-shadow:
            0 8px 22px rgba(31,24,17,.10),
            0 32px 76px rgba(31,24,17,.22),
            0 64px 130px rgba(31,24,17,.14) !important;
    }

    .seller-review-dialog::before {
        content: "";
        position: absolute;
        z-index: 6;
        top: 0;
        left: 28px;
        width: 74px;
        height: 3px;
        border-radius: 0 0 999px 999px;
        background: #d99500;
    }

    .seller-review-header {
        position: relative;
        z-index: 5;
        padding: 18px 22px !important;
        border-bottom-color: #eae2d8 !important;
        background: #fff !important;
        box-shadow:
            0 1px 0 rgba(61,43,22,.025),
            0 8px 22px rgba(61,43,22,.035) !important;
    }

    .seller-review-eyebrow {
        margin-bottom: 4px;
        color: #a47a33;
        font-size: .66rem;
        font-weight: 700;
        letter-spacing: .11em;
        line-height: 1;
        text-transform: uppercase;
    }

    .seller-review-identity > div:first-child {
        width: 48px !important;
        height: 48px !important;
        border: 1px solid #3c352e;
        border-radius: 14px !important;
        background: #2e2923 !important;
        font-size: .72rem !important;
        letter-spacing: .04em;
        box-shadow:
            0 3px 7px rgba(37,29,19,.08),
            0 11px 24px rgba(37,29,19,.16) !important;
    }

    .seller-review-header h3 {
        font-size: 1.08rem !important;
        line-height: 1.25 !important;
        letter-spacing: -.025em;
    }

    .seller-review-toolbar {
        padding: 4px;
        border: 1px solid #eee6dc;
        border-radius: 13px;
        background: #faf9f6;
    }

    .seller-review-suspend-btn,
    .seller-review-close-btn {
        box-shadow:
            0 2px 4px rgba(61,43,22,.025),
            0 7px 16px rgba(61,43,22,.045) !important;
    }

    .seller-review-suspend-btn:hover,
    .seller-review-close-btn:hover {
        transform: translateY(-1px);
    }

    .seller-review-body {
        padding: 20px !important;
        background: #f7f5f1 !important;
        scrollbar-color: #c8beb1 transparent;
    }

    .seller-review-stats-grid {
        gap: 12px !important;
    }

    .seller-review-stat {
        position: relative;
        min-height: 92px !important;
        overflow: hidden;
        padding: 15px 16px !important;
        border-color: #e6ddd2 !important;
        border-radius: 15px !important;
        background: #fff !important;
        box-shadow:
            0 3px 7px rgba(61,43,22,.035),
            0 13px 28px rgba(61,43,22,.07),
            0 24px 44px rgba(61,43,22,.026) !important;
    }

    .seller-review-stat::after {
        content: "";
        position: absolute;
        right: 14px;
        bottom: 12px;
        width: 22px;
        height: 2px;
        border-radius: 999px;
        background: #e9dcc4;
    }

    .seller-review-stat p:first-child {
        color: #8c8378 !important;
        font-size: .72rem !important;
        font-weight: 500;
    }

    .seller-review-stat p:last-child {
        margin-top: 8px !important;
        font-size: 1.08rem !important;
        line-height: 1 !important;
        letter-spacing: -.025em;
    }

    .seller-review-section-heading {
        margin-top: 20px;
        margin-bottom: 10px;
        padding: 0 2px;
    }

    .seller-review-section-heading h4 {
        color: #302a24;
        font-size: .86rem;
        font-weight: 700;
        letter-spacing: -.015em;
    }

    .seller-review-section-heading p {
        margin-top: 3px;
        color: #91887d;
        font-size: .68rem;
        line-height: 1.45;
    }

    .seller-review-section-count {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        border: 1px solid #e8dfd3;
        border-radius: 999px;
        background: #fff;
        padding: 0 10px;
        color: #756b60;
        font-size: .64rem;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(61,43,22,.03);
    }

    .seller-review-listings {
        margin-top: 0 !important;
        gap: 18px !important;
    }

    .seller-review-product {
        border-color: #dfd6ca !important;
        border-radius: 18px !important;
        background: #fff !important;
        box-shadow:
            0 3px 8px rgba(61,43,22,.04),
            0 16px 36px rgba(61,43,22,.085),
            0 28px 52px rgba(61,43,22,.032) !important;
    }

    .seller-review-product-intro {
        padding: 14px 16px !important;
        border-bottom-color: #eee6dc !important;
        background: #fffdfa !important;
    }

    .seller-review-product-summary {
        padding: 18px !important;
        background: #fff;
    }

    .seller-review-product-image {
        border-color: #e5ddd2 !important;
        background: #f7f5f1 !important;
        box-shadow:
            0 3px 7px rgba(61,43,22,.035),
            0 12px 26px rgba(61,43,22,.07) !important;
    }

    .seller-review-product-main h4 {
        font-size: .98rem !important;
        line-height: 1.28 !important;
    }

    .seller-review-product-metric {
        border-color: #e9e1d7 !important;
        background: #faf9f6 !important;
        box-shadow:
            0 1px 2px rgba(61,43,22,.018),
            inset 0 1px 0 rgba(255,255,255,.9) !important;
    }

    .seller-review-subsection {
        padding: 13px 18px !important;
        background: #faf9f6 !important;
    }

    .seller-screening-grid {
        gap: 14px !important;
        padding: 18px !important;
        background: #f9f7f4 !important;
    }

    .screening-panel {
        min-height: 220px;
        border-radius: 15px !important;
        padding: 17px !important;
        box-shadow:
            0 3px 7px rgba(61,43,22,.03),
            0 12px 28px rgba(61,43,22,.06) !important;
    }

    .screening-panel-local {
        background: #fffdfd !important;
    }

    .screening-panel-ai {
        background: #fffdf9 !important;
    }

    .seller-review-product details > summary {
        min-height: 58px;
        padding-inline: 18px !important;
        background: #fff !important;
    }

    .seller-review-product details > summary:hover {
        background: #faf8f4 !important;
    }

    .seller-review-product details[open] > summary {
        background: #faf8f4 !important;
    }

    .seller-review-product details > div {
        padding: 18px !important;
        background: #fff !important;
    }

    .seller-review-actions {
        gap: 10px !important;
        padding: 16px 18px 18px !important;
        background: #fff !important;
    }

    .seller-action-btn {
        min-height: 42px !important;
        border-radius: 11px !important;
        box-shadow:
            0 2px 4px rgba(61,43,22,.025),
            0 8px 18px rgba(61,43,22,.05) !important;
    }

    .seller-action-btn:hover {
        transform: translateY(-2px) !important;
    }

    .seller-action-warn {
        box-shadow:
            0 3px 7px rgba(168,92,84,.08),
            0 13px 28px rgba(168,92,84,.18) !important;
    }

    .seller-review-body > section,
    .seller-review-body .seller-review-listings + section {
        box-shadow:
            0 3px 7px rgba(61,43,22,.03),
            0 13px 30px rgba(61,43,22,.065) !important;
    }

    @media (max-width: 767px) {
        .seller-compliance-page .compliance-summary-card,
        .seller-compliance-page .compliance-workspace-tabs,
        .seller-compliance-page [data-compliance-panel],
        .seller-compliance-page .compliance-card {
            box-shadow:
                0 3px 7px rgba(61,43,22,.035),
                0 14px 32px rgba(61,43,22,.075),
                0 24px 46px rgba(61,43,22,.025) !important;
        }

        .seller-review-modal {
            padding: 8px !important;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .seller-review-dialog {
            width: calc(100vw - 16px) !important;
            max-height: 95vh !important;
            border-radius: 18px !important;
        }

        .seller-review-dialog::before {
            left: 20px;
            width: 58px;
        }

        .seller-review-header {
            padding: 16px !important;
        }

        .seller-review-toolbar {
            width: 100%;
            justify-content: flex-end;
        }

        .seller-review-body {
            padding: 12px !important;
        }

        .seller-review-stat {
            min-height: 82px !important;
            padding: 13px !important;
        }

        .seller-review-product-summary,
        .seller-screening-grid,
        .seller-review-product details > div,
        .seller-review-actions {
            padding: 14px !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-review-modal:not(.hidden),
        .seller-review-modal:not(.hidden) .seller-review-dialog {
            animation: none !important;
        }
    }


    /* =========================================================
       SARI MASTER ADMIN HEADER — SELLER COMPLIANCE
       This page is the reference header style for the admin system.
       Header-only final override; all backend/UI behavior stays intact.
       ========================================================= */

    .seller-compliance-page .compliance-page-header {
        margin-bottom: 16px !important;
        padding: 0 !important;
    }

    .seller-compliance-page .compliance-page-header-main {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .seller-compliance-page .compliance-page-icon {
        display: grid;
        width: 44px !important;
        height: 44px !important;
        flex: 0 0 44px !important;
        place-items: center;
        border: 1px solid #eadfc9 !important;
        border-radius: 14px !important;
        background: #fff8eb !important;
        color: #b77c18 !important;
        box-shadow:
            0 2px 5px rgba(75,54,25,.03),
            0 9px 20px rgba(75,54,25,.06) !important;
    }

    .seller-compliance-page .compliance-page-icon svg {
        width: 18px !important;
        height: 18px !important;
    }

    .seller-compliance-page .compliance-eyebrow {
        margin: 0 !important;
        color: #9a7b43 !important;
        -webkit-text-fill-color: #9a7b43 !important;
        font-size: 9px !important;
        font-weight: 600 !important;
        line-height: 1.2 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
    }

    .seller-compliance-page .compliance-page-title {
        margin: 4px 0 0 !important;
        font-size: clamp(1.75rem, 1.55rem + .5vw, 2.15rem) !important;
        font-weight: 700 !important;
        line-height: 1.08 !important;
        letter-spacing: -.04em !important;
    }

    .seller-compliance-page .compliance-title-base {
        color: #17130f !important;
        -webkit-text-fill-color: #17130f !important;
    }

    .seller-compliance-page .compliance-title-accent {
        color: #d99500 !important;
        -webkit-text-fill-color: #d99500 !important;
    }

    .seller-compliance-page .compliance-page-subtitle {
        max-width: 820px !important;
        margin: 6px 0 0 !important;
        color: #81786c !important;
        -webkit-text-fill-color: #81786c !important;
        font-size: clamp(.73rem, .70rem + .08vw, .81rem) !important;
        font-weight: 400 !important;
        line-height: 1.65 !important;
        letter-spacing: 0 !important;
        text-transform: none !important;
    }

    .seller-compliance-page .compliance-account-control {
        min-height: 39px !important;
        border-radius: 10px !important;
        padding-inline: 16px !important;
        font-size: clamp(.72rem, .69rem + .06vw, .78rem) !important;
        font-weight: 600 !important;
        box-shadow:
            0 3px 7px rgba(183,124,0,.09),
            0 12px 26px rgba(217,149,0,.20) !important;
    }

    .seller-compliance-page .compliance-account-control:hover {
        transform: translateY(-1px);
        box-shadow:
            0 4px 8px rgba(183,124,0,.11),
            0 15px 32px rgba(217,149,0,.23) !important;
    }

    @media (max-width: 767px) {
        .seller-compliance-page .compliance-page-header-main {
            align-items: flex-start;
            gap: 12px;
        }

        .seller-compliance-page .compliance-page-icon {
            width: 42px !important;
            height: 42px !important;
            flex-basis: 42px !important;
            border-radius: 13px !important;
        }

        .seller-compliance-page .compliance-eyebrow {
            font-size: 8.5px !important;
        }

        .seller-compliance-page .compliance-page-title {
            font-size: 1.65rem !important;
        }

        .seller-compliance-page .compliance-page-subtitle {
            font-size: .72rem !important;
        }

        .seller-compliance-page .compliance-account-control {
            width: 100%;
        }
    }


    /* =========================================================
       SELLER COMPLIANCE SUMMARY — USER MANAGEMENT SIZE PARITY
       Size/proportion only. Seller Compliance content and logic unchanged.
       ========================================================= */

    .seller-compliance-page .compliance-summary-card {
        min-height: 110px !important;
        padding: 18px !important;
        border-radius: 18px !important;
    }

    .seller-compliance-page .compliance-summary-card > div {
        min-height: 72px;
        align-items: center !important;
        gap: 16px !important;
    }

    .seller-compliance-page .compliance-summary-card > div > div:first-child {
        min-width: 0;
    }

    .seller-compliance-page .compliance-summary-card > div > div:first-child > p:first-child {
        font-size: 11.5px !important;
        line-height: 1.35 !important;
        font-weight: 500 !important;
    }

    .seller-compliance-page .compliance-summary-card > div > div:first-child > p:nth-child(2) {
        margin-top: 4px !important;
        font-size: 26px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: -.04em !important;
    }

    .seller-compliance-page .compliance-summary-card > div > div:last-child {
        width: 48px !important;
        height: 48px !important;
        flex: 0 0 48px !important;
        border-radius: 12px !important;
    }

    .seller-compliance-page .compliance-summary-card > div > div:last-child svg {
        width: 20px !important;
        height: 20px !important;
    }

    @media (max-width: 639px) {
        .seller-compliance-page .compliance-summary-card {
            min-height: 110px !important;
            padding: 16px !important;
        }

        .seller-compliance-page .compliance-summary-card > div {
            gap: 14px !important;
        }

        .seller-compliance-page .compliance-summary-card > div > div:last-child {
            width: 44px !important;
            height: 44px !important;
            flex-basis: 44px !important;
        }
    }


    /* =========================================================
       FLAGGED SELLERS — USER MANAGEMENT TABLE SIZE + ICON PARITY
       Matches the Users table's readable density and clean action treatment.
       Backend data attributes, filtering, modal hooks, and moderation logic stay intact.
       ========================================================= */

    /* Main flagged queue surface: same clean floating "sheet" feel as Users. */
    .seller-compliance-page #compliancePanel-flagged {
        border-color: #e7ddd1 !important;
        border-radius: 18px !important;
        background: #fff !important;
        box-shadow:
            0 3px 8px rgba(61, 43, 22, .045),
            0 18px 42px rgba(61, 43, 22, .095),
            0 38px 78px rgba(61, 43, 22, .045),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .seller-compliance-page #compliancePanel-flagged .flagged-list-shell,
    .seller-compliance-page #compliancePanel-flagged #flaggedSellerList {
        border: 0 !important;
        border-radius: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    /* Header — same visual scale as Approved Accounts table. */
    .seller-compliance-page #compliancePanel-flagged .flagged-table-head {
        min-height: 50px;
        padding: 14px 20px !important;
        border-bottom: 1px solid #eee8df !important;
        background: #fcfbf8 !important;
        color: #847b70 !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        line-height: 1.35 !important;
        letter-spacing: .07em !important;
        text-transform: uppercase !important;
    }

    /* Desktop row rhythm — same comfortable height as the Users table. */
    @media (min-width: 1280px) {
        .seller-compliance-page #compliancePanel-flagged .flagged-table-head,
        .seller-compliance-page #compliancePanel-flagged .flagged-seller-row-modern > div {
            grid-template-columns:
                minmax(320px, 1.85fr)
                150px
                125px
                140px
                175px
                78px !important;
            gap: 16px !important;
        }

        .seller-compliance-page #compliancePanel-flagged .flagged-seller-row-modern > div > div {
            min-height: 72px !important;
        }
    }

    .seller-compliance-page #compliancePanel-flagged .flagged-seller-row-modern.compliance-seller-row {
        margin: 0 !important;
        border: 0 !important;
        border-bottom: 1px solid #f0ebe4 !important;
        border-radius: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
        transform: none !important;
        transition: background-color .14s ease !important;
    }

    .seller-compliance-page #compliancePanel-flagged .flagged-seller-row-modern.compliance-seller-row:hover {
        border-color: #f0ebe4 !important;
        background: #fdfbf7 !important;
        box-shadow: none !important;
        transform: none !important;
    }

    .seller-compliance-page #compliancePanel-flagged #flaggedSellerList > article:last-of-type {
        border-bottom: 0 !important;
    }

    /* Seller identity — User table sizing. */
    .seller-compliance-page #compliancePanel-flagged .flagged-seller-avatar {
        width: 40px !important;
        height: 40px !important;
        flex: 0 0 40px !important;
        border: 0 !important;
        border-radius: 999px !important;
        background: #f3f1ed !important;
        color: #655d55 !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        box-shadow: none !important;
    }

    .seller-compliance-page #compliancePanel-flagged .flagged-seller-name {
        color: #2e2924 !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        line-height: 1.35 !important;
    }

    .seller-compliance-page #compliancePanel-flagged .flagged-seller-email {
        color: #988f84 !important;
        font-size: 9.5px !important;
        font-weight: 400 !important;
        line-height: 1.35 !important;
    }

    /* Flagged count — compact like the Users table pills. */
    .seller-compliance-page #compliancePanel-flagged .flagged-count-box {
        min-width: 30px !important;
        height: 28px !important;
        border-radius: 9px !important;
        font-size: 9.5px !important;
        font-weight: 700 !important;
        box-shadow: none !important;
    }

    .seller-compliance-page #compliancePanel-flagged .flagged-count-copy {
        color: #8f867b !important;
        font-size: 9px !important;
        line-height: 1.4 !important;
    }

    /* Warnings / risk badges — same readable badge scale as Users. */
    .seller-compliance-page #compliancePanel-flagged .flagged-warning-badge,
    .seller-compliance-page #compliancePanel-flagged .flagged-risk-badge {
        padding: 6px 10px !important;
        border-radius: 999px !important;
        font-size: 9.5px !important;
        font-weight: 600 !important;
        line-height: 1 !important;
    }

    /* Date and relative time — mirrors Joined + helper text. */
    .seller-compliance-page #compliancePanel-flagged .flagged-last-date {
        color: #514a42 !important;
        font-size: 10px !important;
        font-weight: 500 !important;
        line-height: 1.4 !important;
    }

    .seller-compliance-page #compliancePanel-flagged .flagged-last-relative {
        color: #958c80 !important;
        font-size: 9px !important;
        font-weight: 400 !important;
        line-height: 1.4 !important;
    }

    /* Mobile field labels stay compact and readable. */
    .seller-compliance-page #compliancePanel-flagged .flagged-mobile-label {
        color: #958c80 !important;
        font-size: 9px !important;
        font-weight: 500 !important;
        line-height: 1.35 !important;
    }

    /* =========================================================
       ACTION — ICON ONLY
       Same behavior as the final Users Action column:
       dark by default, bright SARI gold on hover/focus.
       ========================================================= */
    .seller-compliance-page #compliancePanel-flagged .flagged-review-button {
        display: inline-grid !important;
        width: 30px !important;
        min-width: 30px !important;
        height: 30px !important;
        min-height: 30px !important;
        place-items: center !important;
        padding: 0 !important;

        border: 0 !important;
        border-color: transparent !important;
        border-radius: 0 !important;
        background: transparent !important;
        background-color: transparent !important;
        box-shadow: none !important;

        color: #3f3b37 !important;
        outline: none !important;

        transition:
            color .15s ease,
            transform .15s ease !important;
    }

    .seller-compliance-page #compliancePanel-flagged .flagged-review-button svg {
        width: 15px !important;
        height: 15px !important;
        color: currentColor !important;
        stroke: currentColor !important;
        filter: none !important;
        transition: none !important;
    }

    .seller-compliance-page #compliancePanel-flagged .flagged-review-button:hover,
    .seller-compliance-page #compliancePanel-flagged .flagged-review-button:focus-visible {
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        color: #e09a00 !important;
        transform: translateY(-1px) scale(1.08) !important;
    }

    .seller-compliance-page #compliancePanel-flagged .flagged-review-button:active {
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        color: #c98500 !important;
        transform: scale(1.02) !important;
    }

    /* Footer text size aligned with the Users table footer. */
    .seller-compliance-page #compliancePanel-flagged .flagged-table-footer {
        min-height: 68px;
        background: #fff !important;
    }

    .seller-compliance-page #compliancePanel-flagged #flaggedSellerResultCount {
        color: #756d63 !important;
        font-size: 10px !important;
        font-weight: 400 !important;
        line-height: 1.4 !important;
    }

    @media (max-width: 1279px) {
        .seller-compliance-page #compliancePanel-flagged .flagged-seller-row-modern.compliance-seller-row {
            margin: 12px !important;
            border: 1px solid #e9e1d7 !important;
            border-radius: 16px !important;
            box-shadow:
                0 2px 5px rgba(61,43,22,.025),
                0 9px 20px rgba(61,43,22,.05) !important;
        }

        .seller-compliance-page #compliancePanel-flagged .flagged-seller-row-modern.compliance-seller-row:hover {
            border-color: #ddcfb8 !important;
            background: #fffdfa !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-compliance-page #compliancePanel-flagged .flagged-review-button,
        .seller-compliance-page #compliancePanel-flagged .flagged-review-button:hover,
        .seller-compliance-page #compliancePanel-flagged .flagged-review-button:focus-visible {
            transform: none !important;
            transition: none !important;
        }
    }


    /* =========================================================
       SELLER COMPLIANCE — CLEAN REVIEW MODAL
       Matches the newer clean Seller Account Control modal language:
       white form-like surface, restrained borders, low visual noise.
       Existing review data, routes, forms, details accordions, and JS stay intact.
       ========================================================= */

    /* Backdrop */
    .seller-compliance-page .seller-review-modal {
        background: rgba(31, 29, 26, .46) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        padding: 18px !important;
    }

    /* Main dialog */
    .seller-compliance-page .seller-review-dialog {
        position: relative;
        width: min(960px, calc(100vw - 28px)) !important;
        max-width: 960px !important;
        max-height: min(92vh, 900px) !important;
        overflow: hidden !important;
        border: 1px solid #dedbd6 !important;
        border-radius: 22px !important;
        background: #fff !important;
        box-shadow:
            0 18px 44px rgba(24,22,19,.13),
            0 44px 100px rgba(24,22,19,.20) !important;
    }

    .seller-compliance-page .seller-review-dialog::before {
        display: none !important;
        content: none !important;
    }

    /* Header */
    .seller-compliance-page .seller-review-header {
        align-items: center !important;
        padding: 22px 22px 18px !important;
        border-bottom: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-review-identity {
        align-items: center !important;
        gap: 0 !important;
    }

    /* Hide chunky avatar to match the cleaner form-like modal */
    .seller-compliance-page .seller-review-identity > div:first-child {
        display: none !important;
    }

    .seller-compliance-page .seller-review-eyebrow {
        margin: 0 0 5px !important;
        color: #77777c !important;
        font-size: .66rem !important;
        font-weight: 500 !important;
        letter-spacing: 0 !important;
        line-height: 1.3 !important;
        text-transform: none !important;
    }

    .seller-compliance-page .seller-review-header h3 {
        color: #252525 !important;
        font-size: 1.34rem !important;
        font-weight: 700 !important;
        line-height: 1.18 !important;
        letter-spacing: -.035em !important;
    }

    .seller-compliance-page .seller-review-header .seller-review-identity p:last-child {
        margin-top: 7px !important;
        color: #7d7d82 !important;
        font-size: .73rem !important;
        line-height: 1.45 !important;
    }

    .seller-compliance-page .seller-review-header .rounded-full.border {
        padding: 5px 9px !important;
        font-size: .61rem !important;
        font-weight: 600 !important;
    }

    /* Header action group: remove toolbar container */
    .seller-compliance-page .seller-review-toolbar {
        gap: 8px !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
    }

    .seller-compliance-page .seller-review-suspend-btn {
        min-height: 38px !important;
        height: 38px !important;
        border: 1px solid #e6d5b3 !important;
        border-radius: 10px !important;
        background: #fffaf0 !important;
        padding-inline: 13px !important;
        color: #94671f !important;
        font-size: .68rem !important;
        font-weight: 600 !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-review-suspend-btn:hover {
        border-color: #d7bb7e !important;
        background: #fff4df !important;
        transform: none !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-review-close-btn {
        width: 36px !important;
        height: 36px !important;
        min-height: 36px !important;
        border: 0 !important;
        border-radius: 10px !important;
        background: #f7f7f8 !important;
        color: #636363 !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-review-close-btn:hover {
        background: #eeeeef !important;
        color: #2d2d2d !important;
        transform: none !important;
        box-shadow: none !important;
    }

    /* Body */
    .seller-compliance-page .seller-review-body {
        padding: 0 22px 22px !important;
        background: #fff !important;
        scrollbar-color: #d1d1d4 transparent !important;
    }

    /* Seller snapshot -> clean read-only form fields */
    .seller-compliance-page .seller-review-stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 14px !important;
        margin: 0 !important;
    }

    .seller-compliance-page .seller-review-stat {
        min-height: 0 !important;
        overflow: visible !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-review-stat::after {
        display: none !important;
        content: none !important;
    }

    .seller-compliance-page .seller-review-stat p:first-child {
        margin-bottom: 7px !important;
        color: #626268 !important;
        font-size: .70rem !important;
        font-weight: 500 !important;
        line-height: 1.3 !important;
    }

    .seller-compliance-page .seller-review-stat p:last-child {
        display: flex;
        min-height: 48px;
        align-items: center;
        margin: 0 !important;
        padding: 0 14px !important;
        border: 1px solid #dcdde1;
        border-radius: 10px;
        background: #fff;
        color: #303034 !important;
        font-size: .86rem !important;
        font-weight: 500 !important;
        line-height: 1.2 !important;
        letter-spacing: 0 !important;
        box-shadow: none !important;
    }

    /* Section heading */
    .seller-compliance-page .seller-review-section-heading {
        margin: 20px 0 10px !important;
        padding: 0 !important;
    }

    .seller-compliance-page .seller-review-section-heading h4 {
        color: #303034 !important;
        font-size: .80rem !important;
        font-weight: 600 !important;
        letter-spacing: -.01em !important;
    }

    .seller-compliance-page .seller-review-section-heading p {
        margin-top: 3px !important;
        color: #85858b !important;
        font-size: .66rem !important;
        line-height: 1.5 !important;
    }

    .seller-compliance-page .seller-review-section-count {
        min-height: 26px !important;
        border-color: #e2e2e5 !important;
        background: #f7f7f8 !important;
        color: #69696f !important;
        font-size: .62rem !important;
        box-shadow: none !important;
    }

    /* Flagged listings */
    .seller-compliance-page .seller-review-listings {
        gap: 14px !important;
    }

    .seller-compliance-page .seller-review-product {
        overflow: hidden !important;
        border: 1px solid #dcdde1 !important;
        border-radius: 14px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-review-product-intro {
        padding: 12px 14px !important;
        border-bottom: 1px solid #ececef !important;
        background: #fafafa !important;
    }

    .seller-compliance-page .seller-review-product-intro p:first-child {
        color: #6f6f74 !important;
        font-size: .65rem !important;
        font-weight: 600 !important;
        letter-spacing: .06em !important;
    }

    .seller-compliance-page .seller-review-product-intro p:last-child {
        color: #8a8a8f !important;
        font-size: .64rem !important;
    }

    .seller-compliance-page .seller-review-product-summary {
        grid-template-columns: 108px minmax(0, 1fr) !important;
        gap: 16px !important;
        padding: 16px !important;
        background: #fff !important;
    }

    .seller-compliance-page .seller-review-product-image {
        width: 108px !important;
        height: 108px !important;
        border: 1px solid #e1e1e4 !important;
        border-radius: 10px !important;
        background: #f7f7f8 !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-review-product-main h4 {
        color: #303034 !important;
        font-size: .86rem !important;
        font-weight: 600 !important;
        line-height: 1.3 !important;
    }

    .seller-compliance-page .seller-review-product-main > div:first-child > div:first-child > p {
        color: #7d7d82 !important;
        font-size: .69rem !important;
    }

    /* Metrics inside each listing -> input-like read only fields */
    .seller-compliance-page .seller-review-product-stats {
        gap: 10px !important;
    }

    .seller-compliance-page .seller-review-product-metric {
        min-height: 58px !important;
        padding: 10px 11px !important;
        border: 1px solid #e2e2e5 !important;
        border-radius: 9px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-review-product-metric p:first-child {
        color: #88888d !important;
        font-size: .62rem !important;
        font-weight: 400 !important;
    }

    .seller-compliance-page .seller-review-product-metric p:last-child {
        color: #37373b !important;
        font-size: .71rem !important;
        font-weight: 600 !important;
    }

    /* Section bars */
    .seller-compliance-page .seller-review-subsection {
        padding: 12px 16px !important;
        border-top-color: #ececef !important;
        background: #fafafa !important;
    }

    .seller-compliance-page .seller-review-subsection p:first-child {
        color: #4b4b50 !important;
        font-size: .69rem !important;
        font-weight: 600 !important;
    }

    .seller-compliance-page .seller-review-subsection p:last-child {
        color: #8b8b90 !important;
        font-size: .63rem !important;
    }

    /* Screening panels: clean, no floating cards */
    .seller-compliance-page .seller-screening-grid {
        gap: 12px !important;
        padding: 16px !important;
        border-top-color: #ececef !important;
        background: #fff !important;
    }

    .seller-compliance-page .screening-panel {
        min-height: 0 !important;
        padding: 14px !important;
        border: 1px solid #e0e0e3 !important;
        border-radius: 11px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .screening-panel::before {
        display: none !important;
        content: none !important;
    }

    .seller-compliance-page .screening-panel-local,
    .seller-compliance-page .screening-panel-ai {
        background: #fff !important;
    }

    .seller-compliance-page .screening-panel > div:first-child p {
        font-size: .72rem !important;
        font-weight: 600 !important;
    }

    .seller-compliance-page .screening-panel > p {
        color: #69696f !important;
        font-size: .67rem !important;
        line-height: 1.55 !important;
    }

    .seller-compliance-page .screening-panel .rounded-xl.border {
        min-height: 58px !important;
        border-color: #e4e4e7 !important;
        border-radius: 9px !important;
        background: #fafafa !important;
    }

    /* Full Listing Details accordion */
    .seller-compliance-page .seller-review-product details {
        border-top: 1px solid #ececef !important;
    }

    .seller-compliance-page .seller-review-product details > summary {
        min-height: 52px !important;
        padding: 12px 16px !important;
        background: #fff !important;
    }

    .seller-compliance-page .seller-review-product details > summary:hover,
    .seller-compliance-page .seller-review-product details[open] > summary {
        background: #fafafa !important;
    }

    .seller-compliance-page .seller-review-product details > summary p:first-child {
        color: #45454a !important;
        font-size: .70rem !important;
        font-weight: 600 !important;
    }

    .seller-compliance-page .seller-review-product details > summary p:last-child {
        color: #89898e !important;
        font-size: .63rem !important;
    }

    .seller-compliance-page .seller-review-product details > div {
        padding: 16px !important;
        border-top-color: #ececef !important;
        background: #fff !important;
    }

    .seller-compliance-page .seller-review-product details .rounded-xl.border,
    .seller-compliance-page .seller-review-product details .rounded-\[14px\].border {
        border-color: #e3e3e6 !important;
        border-radius: 9px !important;
        background: #fafafa !important;
        box-shadow: none !important;
    }

    /* Moderation buttons */
    .seller-compliance-page .seller-review-actions {
        gap: 10px !important;
        padding: 14px 16px 16px !important;
        border-top: 1px solid #ececef !important;
        background: #fff !important;
    }

    .seller-compliance-page .seller-action-btn {
        min-height: 42px !important;
        height: 42px !important;
        border-radius: 10px !important;
        font-size: .68rem !important;
        font-weight: 600 !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-action-btn:hover {
        transform: none !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-action-approve {
        border-color: #cfe2d5 !important;
        background: #f5faf6 !important;
        color: #4d7b5c !important;
    }

    .seller-compliance-page .seller-action-approve:hover {
        background: #edf7ef !important;
        border-color: #bdd8c5 !important;
    }

    .seller-compliance-page .seller-action-reject {
        border-color: #dcdde1 !important;
        background: #f8f8f8 !important;
        color: #55555a !important;
    }

    .seller-compliance-page .seller-action-reject:hover {
        background: #f1f1f2 !important;
        border-color: #cfcfd3 !important;
    }

    .seller-compliance-page .seller-action-warn {
        border: 1px solid #efcaca !important;
        background: #fff7f7 !important;
        color: #a45151 !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-action-warn:hover {
        border-color: #e3aaaa !important;
        background: #fff0f0 !important;
        box-shadow: none !important;
    }

    /* Warning history becomes a clean section rather than a floating card */
    .seller-compliance-page .seller-review-body > section {
        border-color: #dcdde1 !important;
        border-radius: 12px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-review-body > section > div:first-child {
        border-bottom-color: #ececef !important;
        background: #fafafa !important;
    }

    /* Warning modal receives the same clean language */
    .seller-compliance-page + .warning-modal-backdrop,
    .warning-modal-backdrop {
        background: rgba(31,29,26,.46) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
    }

    .warning-modal-dialog {
        max-width: 560px !important;
        border: 1px solid #dedbd6 !important;
        border-radius: 20px !important;
        background: #fff !important;
        box-shadow:
            0 18px 44px rgba(24,22,19,.13),
            0 44px 100px rgba(24,22,19,.20) !important;
    }

    @media (max-width: 767px) {
        .seller-compliance-page .seller-review-modal {
            padding: 8px !important;
        }

        .seller-compliance-page .seller-review-dialog {
            width: calc(100vw - 16px) !important;
            max-height: 95vh !important;
            border-radius: 18px !important;
        }

        .seller-compliance-page .seller-review-header {
            align-items: flex-start !important;
            padding: 18px 16px 14px !important;
        }

        .seller-compliance-page .seller-review-toolbar {
            width: 100%;
            justify-content: flex-end;
        }

        .seller-compliance-page .seller-review-body {
            padding: 0 16px 18px !important;
        }

        .seller-compliance-page .seller-review-stats-grid {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }

        .seller-compliance-page .seller-review-product-summary {
            grid-template-columns: 1fr !important;
            padding: 14px !important;
        }

        .seller-compliance-page .seller-review-product-image {
            width: 100% !important;
            height: 180px !important;
        }

        .seller-compliance-page .seller-screening-grid {
            grid-template-columns: 1fr !important;
            padding: 14px !important;
        }

        .seller-compliance-page .seller-review-actions {
            grid-template-columns: 1fr !important;
            padding: 14px !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-compliance-page .seller-review-modal:not(.hidden),
        .seller-compliance-page .seller-review-modal:not(.hidden) .seller-review-dialog {
            animation: none !important;
        }
    }


    /* =========================================================
       SELLER COMPLIANCE REVIEW MODAL — V2 CLEAN REFINEMENT
       - Flat product metrics (no mini-card containers)
       - Flat AI Decision / Policy values
       - Soft red warning outline for flagged listing
       - Neutral action buttons with bright hover-only colors
       ========================================================= */

    /* ---------------------------------------------------------
       Flagged listing warning treatment
       --------------------------------------------------------- */
    .seller-compliance-page .seller-review-product {
        border: 1px solid #efcaca !important;
        border-radius: 14px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-review-product-intro {
        position: relative;
        padding: 13px 15px 13px 18px !important;
        border-bottom: 1px solid #f0dddd !important;
        background: #fffafa !important;
    }

    .seller-compliance-page .seller-review-product-intro::before {
        content: "";
        position: absolute;
        top: 11px;
        bottom: 11px;
        left: 0;
        width: 3px;
        border-radius: 0 999px 999px 0;
        background: #e05252;
    }

    .seller-compliance-page .seller-review-product-intro p:first-child {
        color: #a84f4f !important;
        font-size: .66rem !important;
        font-weight: 700 !important;
        letter-spacing: .07em !important;
    }

    .seller-compliance-page .seller-review-product-intro p:last-child {
        color: #8b7777 !important;
    }

    .seller-compliance-page .seller-review-product-intro .rounded-full {
        border-color: #efcaca !important;
        background: #fff !important;
        color: #a45151 !important;
        box-shadow: none !important;
    }

    /* ---------------------------------------------------------
       Price / Stock / Variants / Uploaded — no containers
       --------------------------------------------------------- */
    .seller-compliance-page .seller-review-product-stats {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 0 !important;
        margin-top: 16px !important;
        padding: 13px 0 0 !important;
        border-top: 1px solid #ececef !important;
    }

    .seller-compliance-page .seller-review-product-metric {
        position: relative;
        min-height: 48px !important;
        padding: 2px 14px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-review-product-metric:first-child {
        padding-left: 0 !important;
    }

    .seller-compliance-page .seller-review-product-metric:last-child {
        padding-right: 0 !important;
    }

    .seller-compliance-page .seller-review-product-metric + .seller-review-product-metric::before {
        content: "";
        position: absolute;
        top: 2px;
        bottom: 2px;
        left: 0;
        width: 1px;
        background: #ececef;
    }

    .seller-compliance-page .seller-review-product-metric p:first-child {
        margin: 0 !important;
        color: #919196 !important;
        font-size: .63rem !important;
        font-weight: 400 !important;
        line-height: 1.35 !important;
    }

    .seller-compliance-page .seller-review-product-metric p:last-child {
        margin-top: 6px !important;
        color: #28282c !important;
        font-size: .75rem !important;
        font-weight: 650 !important;
        line-height: 1.25 !important;
    }

    /* ---------------------------------------------------------
       AI Decision / Policy — flat values, no boxes
       --------------------------------------------------------- */
    .seller-compliance-page .seller-ai-meta {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0 !important;
        margin-top: 14px !important;
        padding: 12px 0 2px !important;
        border-top: 1px solid #ececef !important;
    }

    .seller-compliance-page .seller-ai-meta-item {
        position: relative;
        min-height: 48px;
        padding: 0 14px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-ai-meta-item:first-child {
        padding-left: 0 !important;
    }

    .seller-compliance-page .seller-ai-meta-item:last-child {
        padding-right: 0 !important;
    }

    .seller-compliance-page .seller-ai-meta-item + .seller-ai-meta-item::before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 1px;
        background: #ececef;
    }

    .seller-compliance-page .seller-ai-meta-label {
        color: #919196 !important;
        font-size: .63rem !important;
        font-weight: 400 !important;
        line-height: 1.35 !important;
    }

    .seller-compliance-page .seller-ai-meta-value {
        margin-top: 6px !important;
        color: #2d2d31 !important;
        font-size: .77rem !important;
        font-weight: 650 !important;
        line-height: 1.3 !important;
    }

    /* ---------------------------------------------------------
       Moderation actions — neutral default, bright hover only
       --------------------------------------------------------- */
    .seller-compliance-page .seller-review-actions {
        gap: 12px !important;
    }

    .seller-compliance-page .seller-action-btn {
        min-height: 48px !important;
        height: 48px !important;
        gap: 10px !important;
        border: 1px solid #dcdde1 !important;
        border-radius: 11px !important;
        background: #fff !important;
        color: #303034 !important;
        font-size: .72rem !important;
        font-weight: 600 !important;
        box-shadow: none !important;
        transition:
            color .16s ease,
            border-color .16s ease,
            background-color .16s ease,
            transform .16s ease !important;
    }

    .seller-compliance-page .seller-action-btn svg {
        width: 19px !important;
        height: 19px !important;
        flex: 0 0 19px !important;
        color: currentColor !important;
        stroke: currentColor !important;
    }

    .seller-compliance-page .seller-action-btn:hover,
    .seller-compliance-page .seller-action-btn:focus-visible {
        box-shadow: none !important;
        transform: translateY(-1px) !important;
        outline: none !important;
    }

    /* Approve: black -> bright green */
    .seller-compliance-page .seller-action-approve {
        border-color: #dcdde1 !important;
        background: #fff !important;
        color: #303034 !important;
    }

    .seller-compliance-page .seller-action-approve:hover,
    .seller-compliance-page .seller-action-approve:focus-visible {
        border-color: #78d89a !important;
        background: #f6fff9 !important;
        color: #16a34a !important;
    }

    /* Reject: black -> bright red */
    .seller-compliance-page .seller-action-reject {
        border-color: #dcdde1 !important;
        background: #fff !important;
        color: #303034 !important;
    }

    .seller-compliance-page .seller-action-reject:hover,
    .seller-compliance-page .seller-action-reject:focus-visible {
        border-color: #f29b9b !important;
        background: #fff8f8 !important;
        color: #ef3f3f !important;
    }

    /* Issue warning: black -> bright amber */
    .seller-compliance-page .seller-action-warn {
        border: 1px solid #dcdde1 !important;
        background: #fff !important;
        color: #303034 !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-action-warn:hover,
    .seller-compliance-page .seller-action-warn:focus-visible {
        border-color: #f0c466 !important;
        background: #fffaf0 !important;
        color: #e69a00 !important;
        box-shadow: none !important;
    }

    /* Header suspension action — same interaction language */
    .seller-compliance-page .seller-review-suspend-btn {
        min-height: 42px !important;
        height: 42px !important;
        gap: 9px !important;
        border: 1px solid #dcdde1 !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: #303034 !important;
        box-shadow: none !important;
        transition:
            color .16s ease,
            border-color .16s ease,
            background-color .16s ease,
            transform .16s ease !important;
    }

    .seller-compliance-page .seller-review-suspend-btn svg {
        width: 18px !important;
        height: 18px !important;
        color: currentColor !important;
        stroke: currentColor !important;
    }

    .seller-compliance-page .seller-review-suspend-btn:hover,
    .seller-compliance-page .seller-review-suspend-btn:focus-visible {
        border-color: #f0c466 !important;
        background: #fffaf0 !important;
        color: #e69a00 !important;
        transform: translateY(-1px) !important;
        box-shadow: none !important;
        outline: none !important;
    }

    @media (max-width: 767px) {
        .seller-compliance-page .seller-review-product-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            row-gap: 14px !important;
        }

        .seller-compliance-page .seller-review-product-metric:nth-child(3)::before {
            display: none !important;
        }

        .seller-compliance-page .seller-review-product-metric:nth-child(3),
        .seller-compliance-page .seller-review-product-metric:nth-child(4) {
            padding-top: 10px !important;
            border-top: 1px solid #ececef !important;
        }

        .seller-compliance-page .seller-review-product-metric:nth-child(3) {
            padding-left: 0 !important;
        }

        .seller-compliance-page .seller-ai-meta {
            grid-template-columns: 1fr !important;
        }

        .seller-compliance-page .seller-ai-meta-item {
            padding: 10px 0 !important;
        }

        .seller-compliance-page .seller-ai-meta-item + .seller-ai-meta-item::before {
            top: 0;
            right: 0;
            bottom: auto;
            left: 0;
            width: auto;
            height: 1px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-compliance-page .seller-action-btn,
        .seller-compliance-page .seller-action-btn:hover,
        .seller-compliance-page .seller-action-btn:focus-visible,
        .seller-compliance-page .seller-review-suspend-btn,
        .seller-compliance-page .seller-review-suspend-btn:hover,
        .seller-compliance-page .seller-review-suspend-btn:focus-visible {
            transform: none !important;
            transition: none !important;
        }
    }


    /* =========================================================
       FLAGGED LISTING REVIEW — STRONGER RED OUTLINE
       Warning is communicated by the container line only.
       Interior remains clean white.
       ========================================================= */

    .seller-compliance-page .seller-review-product {
        border: 2px solid #e77979 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .seller-compliance-page .seller-review-product-intro {
        background: #fff !important;
        border-bottom: 1px solid #eadede !important;
    }

    .seller-compliance-page .seller-review-product-intro::before {
        width: 4px !important;
        background: #df4f4f !important;
    }


    /* Remove the extra left red warning bar.
       Keep only the clean red outline around the flagged listing container. */
    .seller-compliance-page .seller-review-product-intro::before {
        display: none !important;
        content: none !important;
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

    {{-- PAGE HEADER — MASTER SARI ADMIN STYLE --}}
    <section class="compliance-page-header flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="compliance-page-header-main">
            <span class="compliance-page-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 3 4 6v5c0 5 3.4 8.2 8 10 4.6-1.8 8-5 8-10V6l-8-3Z"></path>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>
            </span>

            <div class="min-w-0">
                <p class="compliance-eyebrow">Marketplace Safety</p>

                <h2 class="compliance-page-title">
                    <span class="compliance-title-base">Seller</span>
                    <span class="compliance-title-accent">Compliance</span>
                </h2>

                <p class="compliance-page-subtitle">
                    Review flagged listings, seller warnings, suspensions, and compliance appeals from one moderation workspace.
                </p>
            </div>
        </div>

        <a
            href="{{ route('admin.seller-accounts.control') }}"
            wire:navigate
            class="compliance-account-control inline-flex items-center justify-center gap-2 self-start border border-[#e0bd76] bg-[#d99500] text-white transition hover:bg-[#bd8205]"
        >
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                <circle cx="12" cy="8" r="3"></circle>
                <path d="M5 20a7 7 0 0 1 14 0"></path>
                <path d="M18 7h4"></path>
            </svg>
            Seller Account Control
        </a>
    </section>

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
        {{-- ONE CLEAN FILTER BAR — same hierarchy as Approved Accounts --}}
        <div class="compliance-workspace-tabs border-b border-[#eee8df] p-3 sm:p-4">
            <div class="compliance-master-filter">
                {{-- SEARCH --}}
                <div class="master-search relative min-w-0">
                    <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9b9287]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-4-4"></path>
                    </svg>

                    <input
                        id="flaggedSellerSearch"
                        type="search"
                        placeholder="Search seller, email, product, brand, or SKU..."
                        class="master-filter-control w-full pl-10 pr-3 text-xs"
                        aria-label="Search flagged sellers"
                    >
                </div>

                {{-- QUEUE FILTER — custom dropdown styled like User Management --}}
                <div class="filter-dropdown" data-compliance-queue-dropdown>
                    <select id="complianceViewFilter" class="sr-only" aria-label="Compliance queue" tabindex="-1">
                        <option value="flagged">Flagged Sellers ({{ $flaggedSellerCount }})</option>
                        <option value="pending">Pending Review ({{ $pendingProducts->count() }})</option>
                        <option value="warnings">Warning History ({{ $recentWarnings->count() }})</option>
                        <option value="suspended">Suspended Sellers ({{ $suspendedSellers->count() }})</option>
                        <option value="messages">Appeals / Messages ({{ $complianceMessages->count() }})</option>
                    </select>

                    <button
                        type="button"
                        class="filter-dropdown-toggle"
                        data-compliance-queue-toggle
                        aria-haspopup="listbox"
                        aria-expanded="false"
                    >
                        <span class="truncate" data-compliance-queue-label>
                            Flagged Sellers ({{ $flaggedSellerCount }})
                        </span>
                        <svg viewBox="0 0 24 24" class="filter-dropdown-chevron" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </button>

                    <div class="filter-dropdown-menu" data-compliance-queue-menu role="listbox">
                        @foreach([
                            'flagged' => 'Flagged Sellers (' . $flaggedSellerCount . ')',
                            'pending' => 'Pending Review (' . $pendingProducts->count() . ')',
                            'warnings' => 'Warning History (' . $recentWarnings->count() . ')',
                            'suspended' => 'Suspended Sellers (' . $suspendedSellers->count() . ')',
                            'messages' => 'Appeals / Messages (' . $complianceMessages->count() . ')',
                        ] as $value => $label)
                            <button
                                type="button"
                                class="filter-dropdown-option {{ $value === 'flagged' ? 'is-selected' : '' }}"
                                data-compliance-queue-option="{{ $value }}"
                                role="option"
                                aria-selected="{{ $value === 'flagged' ? 'true' : 'false' }}"
                            >
                                <span>{{ $label }}</span>
                                <span class="filter-dropdown-check"></span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- RISK FILTER — custom dropdown styled like User Management --}}
                <div class="filter-dropdown" data-risk-dropdown>
                    <select id="flaggedSellerRiskFilter" class="sr-only" aria-label="Risk level" tabindex="-1">
                        <option value="">All risk levels</option>
                        <option value="high">High risk</option>
                        <option value="medium">Medium risk</option>
                        <option value="low">Low risk</option>
                        <option value="review">Needs review</option>
                    </select>

                    <button
                        type="button"
                        class="filter-dropdown-toggle"
                        data-risk-toggle
                        aria-haspopup="listbox"
                        aria-expanded="false"
                    >
                        <span class="inline-flex min-w-0 items-center gap-2">
                            <span class="filter-risk-dot" data-risk-dot></span>
                            <span class="truncate" data-risk-label>All risk levels</span>
                        </span>
                        <svg viewBox="0 0 24 24" class="filter-dropdown-chevron" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </button>

                    <div class="filter-dropdown-menu" data-risk-menu role="listbox">
                        @foreach([
                            '' => 'All risk levels',
                            'high' => 'High risk',
                            'medium' => 'Medium risk',
                            'low' => 'Low risk',
                            'review' => 'Needs review',
                        ] as $value => $label)
                            <button
                                type="button"
                                class="filter-dropdown-option {{ $value === '' ? 'is-selected' : '' }}"
                                data-risk-option="{{ $value }}"
                                role="option"
                                aria-selected="{{ $value === '' ? 'true' : 'false' }}"
                            >
                                <span>{{ $label }}</span>
                                <span class="filter-dropdown-check"></span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <button
                    id="applyComplianceViewFilter"
                    type="button"
                    class="master-filter-apply inline-flex items-center justify-center gap-2 rounded-[11px] bg-[#d99500] px-5 text-xs font-bold text-white transition hover:bg-[#c48700] focus:outline-none focus:ring-4 focus:ring-[#d99500]/10"
                >
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M4 5h16"></path>
                        <path d="M7 12h10"></path>
                        <path d="M10 19h4"></path>
                    </svg>
                    Apply Filter
                </button>

                <button
                    id="resetComplianceViewFilter"
                    type="button"
                    class="master-filter-reset rounded-[11px] border border-[#e5ddd2] bg-white px-4 text-xs font-semibold text-[#675f55] transition hover:bg-[#faf8f4] focus:outline-none focus:ring-4 focus:ring-[#d99500]/[.06]"
                >
                    Reset
                </button>
            </div>
        </div>

        {{-- FLAGGED SELLERS --}}
        <div id="compliancePanel-flagged" data-compliance-panel>
            <div class="flagged-table-wrap">
                {{-- DESKTOP COLUMN LABELS --}}
                <div class="flagged-list-shell">
                    <div class="flagged-table-head hidden border-b border-[#eee8df] bg-[#faf9f6] px-5 py-3.5 text-xs font-semibold xl:grid xl:grid-cols-[minmax(280px,1.7fr)_140px_120px_130px_170px_72px] xl:gap-4">
                        <p>Seller</p>
                        <p>Flagged Listings</p>
                        <p>Warnings</p>
                        <p>Highest Risk</p>
                        <p>Last Flagged</p>
                        <p class="text-right">Action</p>
                    </div>

                    <div id="flaggedSellerList" class="bg-white">
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
                        class="flagged-seller-row-modern compliance-seller-row bg-white transition-colors duration-150"
                    >
                        <div class="grid grid-cols-1 xl:grid-cols-[minmax(280px,1.7fr)_140px_120px_130px_170px_72px] xl:items-center xl:gap-4">

                            {{-- SELLER --}}
                            <div class="flex min-w-0 items-center gap-3.5 border-b border-[#f0ebe4] px-4 py-4 xl:border-b-0 xl:px-5">
                                <div class="flagged-seller-avatar grid h-10 w-10 shrink-0 place-items-center rounded-full text-xs font-bold">
                                    {{ $sellerInitials }}
                                </div>

                                <div class="min-w-0">
                                    <p class="flagged-seller-name truncate text-sm font-bold text-[#302a24]">{{ $sellerName }}</p>
                                    <p class="flagged-seller-email mt-0.5 truncate text-xs text-[#91887d]">{{ $sellerEmail }}</p>
                                </div>
                            </div>

                            {{-- FLAGGED COUNT --}}
                            <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3.5 xl:border-b-0 xl:px-0">
                                <span class="flagged-mobile-label text-xs font-medium text-[#958c80] xl:hidden">Flagged Listings</span>

                                <div class="flex items-center gap-2">
                                    <span class="flagged-count-box grid h-8 min-w-[32px] place-items-center rounded-lg px-2 text-xs font-bold">
                                        {{ $sellerProducts->count() }}
                                    </span>
                                    <span class="flagged-count-copy text-xs text-[#8f867b]">product{{ $sellerProducts->count() === 1 ? '' : 's' }}</span>
                                </div>
                            </div>

                            {{-- WARNING COUNT --}}
                            <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3.5 xl:border-b-0 xl:px-0">
                                <span class="flagged-mobile-label text-xs font-medium text-[#958c80] xl:hidden">Warnings</span>

                                <span class="flagged-warning-badge rounded-full border px-2.5 py-1.5 text-xs font-bold {{ $warningClass }}">
                                    {{ $warningCount }} / 3
                                </span>
                            </div>

                            {{-- RISK --}}
                            <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3.5 xl:border-b-0 xl:px-0">
                                <span class="flagged-mobile-label text-xs font-medium text-[#958c80] xl:hidden">Highest Risk</span>

                                <span class="flagged-risk-badge rounded-full border px-2.5 py-1.5 text-xs font-bold {{ $riskClass }}">
                                    {{ strtoupper($highestRisk === 'review' ? 'REVIEW' : $highestRisk) }}
                                </span>
                            </div>

                            {{-- LAST FLAGGED --}}
                            <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3.5 xl:border-b-0 xl:px-0">
                                <span class="flagged-mobile-label text-xs font-medium text-[#958c80] xl:hidden">Last Flagged</span>

                                <div class="text-right xl:text-left">
                                    <p class="flagged-last-date text-xs font-semibold text-[#514a42]">
                                        {{ $lastFlaggedAt?->format('M d, Y') ?: '—' }}
                                    </p>

                                    @if($lastFlaggedAt)
                                        <p class="flagged-last-relative mt-0.5 text-xs text-[#9a9185]">{{ $lastFlaggedAt->diffForHumans() }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- ACTION --}}
                            <div class="flex items-center justify-between px-4 py-4 xl:justify-end xl:px-0 xl:pr-4">
                                <span class="flagged-mobile-label text-xs font-medium text-[#958c80] xl:hidden">Action</span>

                                <button
                                    type="button"
                                    data-flagged-seller-open="{{ $sellerModalId }}"
                                    class="flagged-review-button inline-flex items-center justify-center transition"
                                    aria-label="Review {{ $sellerName }}"
                                    title="Open seller review"
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
                        class="seller-review-modal fixed inset-0 z-[170] hidden items-center justify-center p-3 sm:p-5"
                        role="dialog"
                        aria-modal="true"
                        aria-hidden="true"
                        aria-labelledby="{{ $sellerModalId }}-title"
                    >
                        <div class="seller-review-dialog flex max-h-[92vh] w-full max-w-[1180px] flex-col overflow-hidden rounded-[24px] border border-[#e8dfd3] bg-white">

                            {{-- MODAL HEADER --}}
                            <div class="seller-review-header flex shrink-0 flex-col gap-4 border-b border-[#eee8df] bg-white px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                                <div class="seller-review-identity flex min-w-0 items-start gap-3.5">
                                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-[13px] bg-[#2e2923] text-[8px] font-bold text-white">
                                        {{ $sellerInitials }}
                                    </div>

                                    <div class="min-w-0">
                                        <p class="seller-review-eyebrow">Seller compliance review</p>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 id="{{ $sellerModalId }}-title" class="text-[13px] font-bold text-[#302a24]">{{ $sellerName }}</h3>

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

                                <div class="seller-review-toolbar flex flex-wrap items-center gap-2">
                                    @if($seller)
                                        <form method="POST" action="{{ route('admin.compliance.sellers.suspend30', $seller) }}">
                                            @csrf
                                            <input type="hidden" name="reason" value="Manual 30-day suspension after administrator compliance review.">

                                            <button
                                                type="submit"
                                                class="seller-review-suspend-btn inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[#ded4e5] bg-[#f7f3f9] px-3.5 text-[8px] font-bold text-[#765f87] transition hover:bg-[#f1ebf4]"
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
                                        class="seller-review-close-btn grid h-10 w-10 place-items-center rounded-xl border border-[#e6dfd5] bg-white text-[#756d63] transition hover:bg-[#fffaf2]"
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
                            <div class="seller-review-body min-h-0 flex-1 overflow-y-auto bg-[#fcfbf8] p-4 sm:p-5">

                                {{-- SELLER SNAPSHOT --}}
                                <div class="mb-3">
                                    <h4 class="text-[12px] font-semibold text-[#38383c]">Review details</h4>
                                    <p class="mt-1 text-[10px] leading-4 text-[#85858a]">
                                        Review the seller's current compliance state before opening a flagged listing.
                                    </p>
                                </div>

                                <div class="seller-review-stats-grid grid grid-cols-2 gap-3 lg:grid-cols-4">
                                    <div class="seller-review-stat rounded-[14px] border border-[#e8e2d9] bg-white p-3.5">
                                        <p class="text-[7px] text-[#958c80]">Flagged Listings</p>
                                        <p class="mt-1 text-[12px] font-bold text-[#302a24]">{{ $sellerProducts->count() }}</p>
                                    </div>

                                    <div class="seller-review-stat rounded-[14px] border border-[#e8e2d9] bg-white p-3.5">
                                        <p class="text-[7px] text-[#958c80]">Warnings</p>
                                        <p class="mt-1 text-[12px] font-bold {{ $warningCount >= 2 ? 'text-[#a65d5d]' : 'text-[#a8731f]' }}">{{ $warningCount }} / 3</p>
                                    </div>

                                    <div class="seller-review-stat rounded-[14px] border border-[#e8e2d9] bg-white p-3.5">
                                        <p class="text-[7px] text-[#958c80]">Highest Risk</p>
                                        <p class="mt-1 text-[10px] font-bold text-[#514a42]">{{ strtoupper($highestRisk === 'review' ? 'REVIEW' : $highestRisk) }}</p>
                                    </div>

                                    <div class="seller-review-stat rounded-[14px] border border-[#e8e2d9] bg-white p-3.5">
                                        <p class="text-[7px] text-[#958c80]">Last Flagged</p>
                                        <p class="mt-1 text-[9px] font-bold text-[#514a42]">{{ $lastFlaggedAt?->format('M d, Y') ?: '—' }}</p>
                                    </div>
                                </div>

                                {{-- FLAGGED LISTINGS --}}
                                <div class="seller-review-section-heading flex items-end justify-between gap-4">
                                    <div>
                                        <h4>Flagged listings</h4>
                                        <p>Review the screening evidence and choose the appropriate moderation action for each listing.</p>
                                    </div>
                                    <span class="seller-review-section-count">
                                        {{ $sellerProducts->count() }} listing{{ $sellerProducts->count() === 1 ? '' : 's' }}
                                    </span>
                                </div>

                                <div class="seller-review-listings space-y-4">
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

                                        <article class="seller-review-product overflow-hidden rounded-[18px] border border-[#e7e0d7] bg-white">
                                            <div class="seller-review-product-intro border-b border-[#f1ebe4] bg-[#fcfbf8] px-4 py-3">
                                                <div class="flex flex-wrap items-center justify-between gap-2">
                                                    <div>
                                                        <p class="text-[7px] font-bold uppercase tracking-[.12em] text-[#9a7b43]">Flagged Listing Review</p>
                                                        <p class="mt-1 text-[7px] text-[#91887d]">Inspect the listing, screening findings, and moderation decision.</p>
                                                    </div>

                                                    <span class="rounded-full border border-[#eee0c5] bg-[#fff8ec] px-2.5 py-1 text-[7px] font-bold text-[#a8731f]">
                                                        Manual Review
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- PRODUCT SUMMARY --}}
                                            <div class="seller-review-product-summary flex flex-col gap-4 p-4 sm:flex-row">
                                                <div class="seller-review-product-image h-[118px] w-full shrink-0 overflow-hidden rounded-[14px] border border-[#ebe4da] bg-[#faf8f4] sm:w-[118px]">
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

                                                <div class="seller-review-product-main min-w-0 flex-1">
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

                                                    <div class="seller-review-product-stats mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
                                                        <div class="seller-review-product-metric rounded-xl bg-[#faf9f6] p-3">
                                                            <p class="text-[7px] text-[#958c80]">Price</p>
                                                            <p class="mt-1 text-[9px] font-bold text-[#3d3730]">₱{{ number_format((float) $product->price, 2) }}</p>
                                                        </div>

                                                        <div class="seller-review-product-metric rounded-xl bg-[#faf9f6] p-3">
                                                            <p class="text-[7px] text-[#958c80]">Stock</p>
                                                            <p class="mt-1 text-[9px] font-bold text-[#3d3730]">{{ $product->stock }}</p>
                                                        </div>

                                                        <div class="seller-review-product-metric rounded-xl bg-[#faf9f6] p-3">
                                                            <p class="text-[7px] text-[#958c80]">Variants</p>
                                                            <p class="mt-1 text-[9px] font-bold text-[#3d3730]">{{ $productVariants->count() }}</p>
                                                        </div>

                                                        <div class="seller-review-product-metric rounded-xl bg-[#faf9f6] p-3">
                                                            <p class="text-[7px] text-[#958c80]">Uploaded</p>
                                                            <p class="mt-1 text-[8px] font-bold text-[#3d3730]">{{ $product->created_at?->format('M d, Y') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="seller-review-subsection border-t border-[#eee8df] bg-[#faf9f6] px-4 py-3">
                                                <p class="text-[8px] font-bold text-[#514a42]">Screening Insights</p>
                                                <p class="mt-0.5 text-[7px] text-[#91887d]">Local screening output and SARI AI moderation findings.</p>
                                            </div>

                                            {{-- SCREENING SNAPSHOT --}}
                                            <div class="seller-screening-grid grid grid-cols-1 gap-3 border-t border-[#eee8df] bg-[#fcfbf8] p-4 lg:grid-cols-2">
                                                <section class="screening-panel screening-panel-local rounded-[14px] border border-[#eadada] bg-[#fffafa] p-4">
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

                                                <section class="screening-panel screening-panel-ai rounded-[14px] border border-[#dde0e9] bg-[#fbfbfe] p-4">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <p class="text-[8px] font-bold text-[#5e6278]">SARI AI Inspector</p>

                                                        @if($aiCompleted)
                                                            <span class="rounded-full bg-white px-2 py-1 text-[7px] font-bold text-[#666b82]">
                                                                {{ (int) ($product->ai_confidence ?? 0) }}%
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @if($aiCompleted)
                                                        <div class="seller-ai-meta mt-3 grid grid-cols-2 gap-2">
                                                            <div class="seller-ai-meta-item rounded-xl border border-[#e7e8ef] bg-white p-3">
                                                                <p class="seller-ai-meta-label text-[7px] text-[#9699a8]">Decision</p>
                                                                <p class="seller-ai-meta-value mt-1 text-[8px] font-bold text-[#505467]">
                                                                    {{ str($product->ai_decision ?: 'pending_review')->replace('_', ' ')->title() }}
                                                                </p>
                                                            </div>

                                                            <div class="seller-ai-meta-item rounded-xl border border-[#e7e8ef] bg-white p-3">
                                                                <p class="seller-ai-meta-label text-[7px] text-[#9699a8]">Policy</p>
                                                                <p class="seller-ai-meta-value mt-1 text-[8px] font-bold text-[#505467]">
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

                                            <div class="seller-review-subsection border-t border-[#eee8df] bg-white px-4 py-3">
                                                <p class="text-[8px] font-bold text-[#514a42]">Moderation Actions</p>
                                                <p class="mt-0.5 text-[7px] text-[#91887d]">Choose the appropriate action for this flagged listing.</p>
                                            </div>

                                            {{-- PRODUCT ACTIONS --}}
                                            <div class="seller-review-actions grid grid-cols-1 gap-2 border-t border-[#f3ede5] bg-white p-4 sm:grid-cols-3">
                                                <form method="POST" action="{{ route('admin.compliance.products.approve', $product) }}">
                                                    @csrf

                                                    <button class="seller-action-btn seller-action-approve inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-[#d5e5da] bg-[#f3f9f5] text-[8px] font-bold text-[#56816a] transition hover:bg-[#ecf6ef]">
                                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="m7 12 3 3 7-7"></path>
                                                        </svg>
                                                        Approve
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.compliance.products.reject', $product) }}">
                                                    @csrf
                                                    <input type="hidden" name="reason" value="Product rejected after administrator review.">

                                                    <button class="seller-action-btn seller-action-reject inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-[#e4ddd3] bg-white text-[8px] font-bold text-[#675f55] transition hover:bg-[#faf8f4]">
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
                                                    class="seller-action-btn seller-action-warn inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[#b8685f] text-[8px] font-bold text-white transition hover:bg-[#a85c54]"
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
                    <div class="px-5 py-12 text-center">
                        <div class="mx-auto grid h-10 w-10 place-items-center rounded-full border border-[#e9e1d7] bg-[#faf9f6] text-[#91887d]">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 9v4"></path>
                                <path d="M12 17h.01"></path>
                                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path>
                            </svg>
                        </div>

                        <p class="mt-3 text-sm font-bold text-[#514a42]">No flagged sellers</p>
                        <p class="mt-1 text-xs text-[#91887d]">Sellers with flagged listings will appear here.</p>
                    </div>
                @endforelse
                    </div>
                </div>
            </div>

            <div class="flagged-table-footer flex flex-col gap-3 border-t border-[#eee8df] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p id="flaggedSellerResultCount" class="text-[9px] text-[#756d63]">
                    Showing {{ $flaggedSellerCount }} of {{ $flaggedSellerCount }} flagged seller{{ $flaggedSellerCount === 1 ? '' : 's' }}
                </p>

                <div class="flex items-center gap-2 self-end">
                    <button type="button" class="grid h-9 w-9 place-items-center rounded-[10px] border border-[#eee8df] bg-[#faf8f4] text-[#9b9389]" disabled aria-label="Previous page">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"></path></svg>
                    </button>
                    <span class="grid h-9 min-w-9 place-items-center rounded-[10px] bg-[#d99500] px-3 text-[9px] font-bold text-white shadow-[0_8px_18px_rgba(217,149,0,.18)]">1</span>
                    <button type="button" class="grid h-9 w-9 place-items-center rounded-[10px] border border-[#eee8df] bg-[#faf8f4] text-[#9b9389]" disabled aria-label="Next page">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 18 6-6-6-6"></path></svg>
                    </button>
                </div>
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

                            <div class="seller-review-product-main min-w-0 flex-1">
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
                            <div class="seller-review-product-metric rounded-xl bg-[#faf9f6] p-3"><p class="text-[7px] text-[#958c80]">Warnings</p><p class="mt-1 text-[10px] font-bold text-[#a65d5d]">{{ $seller->warning_count }} / 3</p></div>
                            <div class="seller-review-product-metric rounded-xl bg-[#faf9f6] p-3"><p class="text-[7px] text-[#958c80]">Suspended Until</p><p class="mt-1 text-[9px] font-bold text-[#3d3730]">{{ $seller->suspended_until?->format('M d, Y') }}</p></div>
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
<div id="warningModal" class="warning-modal-backdrop fixed inset-0 z-[210] hidden items-center justify-center p-4">
    <div class="warning-modal-dialog w-full max-w-[620px] rounded-[24px] border border-[#e8d8d8] bg-white p-5 sm:p-6">
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
    | COMPLIANCE VIEW FILTER
    |--------------------------------------------------------------------------
    */
    const panels = Array.from(document.querySelectorAll('[data-compliance-panel]'));
    const complianceViewFilter = document.getElementById('complianceViewFilter');
    const applyComplianceViewFilter = document.getElementById('applyComplianceViewFilter');
    const resetComplianceViewFilter = document.getElementById('resetComplianceViewFilter');

    const queueDropdown = document.querySelector('[data-compliance-queue-dropdown]');
    const queueToggle = queueDropdown?.querySelector('[data-compliance-queue-toggle]');
    const queueLabel = queueDropdown?.querySelector('[data-compliance-queue-label]');
    const queueOptions = Array.from(queueDropdown?.querySelectorAll('[data-compliance-queue-option]') || []);

    const riskDropdown = document.querySelector('[data-risk-dropdown]');
    const riskToggle = riskDropdown?.querySelector('[data-risk-toggle]');
    const riskLabel = riskDropdown?.querySelector('[data-risk-label]');
    const riskDot = riskDropdown?.querySelector('[data-risk-dot]');
    const riskOptions = Array.from(riskDropdown?.querySelectorAll('[data-risk-option]') || []);

    function closeFilterDropdown(dropdown, toggle) {
        dropdown?.classList.remove('is-open');
        toggle?.setAttribute('aria-expanded', 'false');
    }

    function closeAllFilterDropdowns(except = null) {
        if (except !== queueDropdown) closeFilterDropdown(queueDropdown, queueToggle);
        if (except !== riskDropdown) closeFilterDropdown(riskDropdown, riskToggle);
    }

    function toggleFilterDropdown(dropdown, toggle) {
        if (!dropdown || !toggle || toggle.disabled) return;

        const willOpen = !dropdown.classList.contains('is-open');
        closeAllFilterDropdowns(dropdown);
        dropdown.classList.toggle('is-open', willOpen);
        toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
    }

    function syncQueueDropdown() {
        const selected = complianceViewFilter?.value || 'flagged';
        const activeOption = queueOptions.find(option => option.dataset.complianceQueueOption === selected);

        if (queueLabel && activeOption) {
            queueLabel.textContent = activeOption.querySelector('span')?.textContent?.trim() || activeOption.textContent.trim();
        }

        queueOptions.forEach(option => {
            const active = option.dataset.complianceQueueOption === selected;
            option.classList.toggle('is-selected', active);
            option.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    }

    function syncRiskDropdown() {
        const selected = flaggedSellerRiskFilter?.value || '';
        const activeOption = riskOptions.find(option => option.dataset.riskOption === selected);

        if (riskLabel && activeOption) {
            riskLabel.textContent = activeOption.querySelector('span')?.textContent?.trim() || activeOption.textContent.trim();
        }

        const riskColors = {
            '': '#d99500',
            high: '#b85f5f',
            medium: '#c58a22',
            low: '#4f8a66',
            review: '#6f7f91',
        };

        if (riskDot) {
            riskDot.style.backgroundColor = riskColors[selected] || riskColors[''];
        }

        riskOptions.forEach(option => {
            const active = option.dataset.riskOption === selected;
            option.classList.toggle('is-selected', active);
            option.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    }

    queueToggle?.addEventListener('click', function (event) {
        event.stopPropagation();
        toggleFilterDropdown(queueDropdown, queueToggle);
    });

    riskToggle?.addEventListener('click', function (event) {
        event.stopPropagation();
        toggleFilterDropdown(riskDropdown, riskToggle);
    });

    queueOptions.forEach(option => {
        option.addEventListener('click', function () {
            if (!complianceViewFilter) return;

            complianceViewFilter.value = this.dataset.complianceQueueOption || 'flagged';
            complianceViewFilter.dispatchEvent(new Event('change', { bubbles: true }));
            syncQueueDropdown();
            closeFilterDropdown(queueDropdown, queueToggle);
        });
    });

    riskOptions.forEach(option => {
        option.addEventListener('click', function () {
            if (!flaggedSellerRiskFilter) return;

            flaggedSellerRiskFilter.value = this.dataset.riskOption || '';
            flaggedSellerRiskFilter.dispatchEvent(new Event('change', { bubbles: true }));
            syncRiskDropdown();
            closeFilterDropdown(riskDropdown, riskToggle);
        });
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('[data-compliance-queue-dropdown]') &&
            !event.target.closest('[data-risk-dropdown]')) {
            closeAllFilterDropdowns();
        }
    });

    function activateCompliancePanel(name) {
        const selectedPanel = document.getElementById('compliancePanel-' + name);

        if (!selectedPanel) {
            return;
        }

        panels.forEach(function (panel) {
            panel.hidden = panel !== selectedPanel;
        });
    }

    applyComplianceViewFilter?.addEventListener('click', function () {
        const selectedView = complianceViewFilter?.value || 'flagged';
        activateCompliancePanel(selectedView);

        if (selectedView === 'flagged') {
            filterFlaggedSellers();
        }

        syncFlaggedFilterAvailability(selectedView);
    });

    complianceViewFilter?.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            const selectedView = this.value || 'flagged';
            activateCompliancePanel(selectedView);
            syncFlaggedFilterAvailability(selectedView);
        }
    });

    resetComplianceViewFilter?.addEventListener('click', function () {
        if (complianceViewFilter) {
            complianceViewFilter.value = 'flagged';
        }

        syncQueueDropdown();
        activateCompliancePanel('flagged');
    });

    activateCompliancePanel('flagged');


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

    function syncFlaggedFilterAvailability(viewName) {
        const enabled = viewName === 'flagged';

        if (flaggedSellerSearch) {
            flaggedSellerSearch.disabled = !enabled;
        }

        if (flaggedSellerRiskFilter) {
            flaggedSellerRiskFilter.disabled = !enabled;
        }

        if (riskToggle) {
            riskToggle.disabled = !enabled;
        }

        if (!enabled) {
            closeFilterDropdown(riskDropdown, riskToggle);
        }
    }

    syncQueueDropdown();
    syncRiskDropdown();
    syncFlaggedFilterAvailability('flagged');

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

        syncRiskDropdown();
        filterFlaggedSellers();
        flaggedSellerSearch?.focus();
    }

    flaggedSellerSearch?.addEventListener('input', filterFlaggedSellers);
    flaggedSellerRiskFilter?.addEventListener('change', function () {
        syncRiskDropdown();
        filterFlaggedSellers();
    });
    clearFlaggedSellerFilters?.addEventListener('click', resetFlaggedSellerFilters);
    flaggedSellerEmptyClear?.addEventListener('click', resetFlaggedSellerFilters);

    resetComplianceViewFilter?.addEventListener('click', function () {
        resetFlaggedSellerFilters();
        syncFlaggedFilterAvailability('flagged');
    });

    complianceViewFilter?.addEventListener('change', function () {
        // Keep the search/risk controls visibly scoped to the Flagged Sellers queue.
        syncQueueDropdown();
        syncFlaggedFilterAvailability(this.value || 'flagged');
    });

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
            modal.setAttribute('aria-hidden', 'true');
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
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');

            requestAnimationFrame(function () {
                modal.querySelector('[data-flagged-seller-close]')?.focus({ preventScroll: true });
            });
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
