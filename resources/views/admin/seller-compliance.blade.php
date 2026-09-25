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

    // Performance: index warning history once by seller instead of
    // re-filtering the complete warning collection inside every modal.
    $complianceWarningsBySeller = $recentWarnings
        ->groupBy(function ($warning) {
            return (string) (
                $warning->seller?->id
                ?? $warning->seller_account_id
                ?? $warning->seller_id
                ?? ''
            );
        });
@endphp

<style>
    /* ============================================================
       SARI SELLER COMPLIANCE — ENTERPRISE / PERFORMANCE EDITION
       Poppins • compact admin density • reduced paint cost
       ============================================================ */

    .seller-compliance-page {
        --sc-gold: #d99500;
        --sc-gold-dark: #b97d05;
        --sc-ink: #26211c;
        --sc-text: #514a42;
        --sc-muted: #8c8378;
        --sc-line: #e9e2d9;
        --sc-soft: #faf9f6;
        --sc-danger: #b45c54;
        --sc-success: #4f8060;
        width: 100%;
        max-width: 1640px !important;
        margin-inline: auto;
        padding-bottom: 20px;
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        color: var(--sc-ink);
    }

    .seller-compliance-page *,
    .seller-compliance-page *::before,
    .seller-compliance-page *::after {
        box-sizing: border-box;
    }

    /* ---------- Shared motion: keep it cheap ---------- */
    .seller-compliance-page button,
    .seller-compliance-page a,
    .seller-compliance-page .compliance-control,
    .seller-compliance-page .filter-dropdown-menu {
        transition:
            color .15s ease,
            background-color .15s ease,
            border-color .15s ease,
            opacity .15s ease,
            transform .15s ease;
    }

    /* ---------- Page header ---------- */
    .compliance-page-header {
        margin-bottom: 12px !important;
        gap: 12px !important;
    }

    .compliance-page-header-main {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px !important;
    }

    .compliance-page-icon {
        display: grid;
        width: 36px !important;
        height: 36px !important;
        flex: 0 0 36px !important;
        place-items: center;
        border: 1px solid #eadfc9 !important;
        border-radius: 10px !important;
        background: #fff8eb !important;
        color: #b77c18 !important;
        box-shadow: 0 4px 12px rgba(75,54,25,.045) !important;
    }

    .compliance-page-icon svg {
        width: 15px !important;
        height: 15px !important;
    }

    .compliance-eyebrow {
        margin: 0 !important;
        color: #9a7b43 !important;
        font-size: 7px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase;
    }

    .compliance-page-title {
        margin: 3px 0 0 !important;
        font-size: clamp(22px, 1.55vw, 27px) !important;
        font-weight: 700 !important;
        line-height: 1.08 !important;
        letter-spacing: -.035em !important;
    }

    .compliance-title-base {
        color: #17130f !important;
    }

    .compliance-title-accent {
        color: var(--sc-gold) !important;
    }

    .compliance-page-subtitle {
        max-width: 760px !important;
        margin: 5px 0 0 !important;
        color: #81786c !important;
        font-size: 9.5px !important;
        font-weight: 400 !important;
        line-height: 1.55 !important;
    }

    .compliance-account-control {
        min-height: 34px !important;
        border-radius: 9px !important;
        padding-inline: 12px !important;
        font-size: 8.5px !important;
        font-weight: 600 !important;
        box-shadow: 0 5px 14px rgba(217,149,0,.13) !important;
    }

    .compliance-account-control:hover {
        transform: translateY(-1px);
    }

    /* ---------- Flash messages ---------- */
    .seller-compliance-page > .mb-4 {
        margin-bottom: 10px !important;
        border-radius: 12px !important;
        padding: 9px 11px !important;
    }

    /* ---------- KPI summary ---------- */
    .compliance-summary-grid {
        gap: 9px !important;
        margin-top: 11px !important;
    }

    .compliance-summary-card {
        min-height: 76px !important;
        border: 1px solid var(--sc-line) !important;
        border-radius: 13px !important;
        background: #fff !important;
        padding: 11px 50px 11px 13px !important;
        box-shadow: 0 6px 18px rgba(61,43,22,.045) !important;
        contain: paint;
    }

    .compliance-summary-card:hover {
        border-color: #ddcfbb !important;
        transform: translateY(-1px);
        box-shadow: 0 8px 22px rgba(61,43,22,.06) !important;
    }

    .compliance-summary-card > div {
        min-height: 52px !important;
        align-items: center !important;
    }

    .compliance-summary-card > div > div:first-child > p:first-child {
        color: #8e857a !important;
        font-size: 8px !important;
        font-weight: 500 !important;
        line-height: 1.3 !important;
    }

    .compliance-summary-card > div > div:first-child > p:nth-child(2) {
        margin-top: 4px !important;
        color: #28221b !important;
        font-size: 19px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        letter-spacing: -.035em !important;
    }

    .compliance-summary-card > div > div:last-child {
        right: 12px !important;
        top: 12px !important;
        width: 32px !important;
        height: 32px !important;
        border-radius: 9px !important;
        box-shadow: none !important;
    }

    .compliance-summary-card > div > div:last-child svg {
        width: 14px !important;
        height: 14px !important;
    }

    /* ---------- Workspace ---------- */
    .compliance-workspace {
        margin-top: 11px !important;
        overflow: visible !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .compliance-workspace-tabs {
        overflow: visible;
        border: 1px solid var(--sc-line) !important;
        border-radius: 14px !important;
        background: #fff !important;
        padding: 9px !important;
        box-shadow: 0 6px 20px rgba(61,43,22,.045) !important;
    }

    .compliance-master-filter {
        display: grid;
        grid-template-columns: minmax(300px, 1fr) 170px 155px auto auto;
        align-items: center;
        gap: 8px;
        width: 100%;
    }

    .master-filter-control,
    .filter-dropdown-toggle {
        width: 100%;
        min-height: 38px !important;
        height: 38px !important;
        border: 1px solid #e5ddd2 !important;
        border-radius: 9px !important;
        background: #fff !important;
        color: #3d3730 !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 9px !important;
        font-weight: 400 !important;
        box-shadow: none !important;
    }

    .master-filter-control {
        padding-right: 10px !important;
    }

    .master-filter-control::placeholder {
        color: #a59c91 !important;
        font-size: 9px !important;
        opacity: 1;
    }

    .master-filter-control:hover:not(:disabled),
    .filter-dropdown-toggle:hover:not(:disabled) {
        border-color: #d4c5b4 !important;
    }

    .master-filter-control:focus,
    .filter-dropdown-toggle:focus-visible,
    .filter-dropdown.is-open .filter-dropdown-toggle {
        outline: none !important;
        border-color: #d49a2b !important;
        box-shadow: 0 0 0 3px rgba(217,149,0,.075) !important;
    }

    .master-filter-control:disabled,
    .filter-dropdown-toggle:disabled {
        cursor: not-allowed;
        opacity: .48;
        background: #f8f7f4 !important;
    }

    .filter-dropdown {
        position: relative;
        min-width: 0;
    }

    .filter-dropdown-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 0 10px;
        text-align: left;
    }

    .filter-dropdown-chevron {
        width: 12px !important;
        height: 12px !important;
        flex: 0 0 12px;
        color: #8b8175;
        transition: transform .15s ease;
    }

    .filter-dropdown.is-open .filter-dropdown-chevron {
        transform: rotate(180deg);
    }

    .filter-risk-dot {
        width: 6px;
        height: 6px;
        flex: 0 0 6px;
        border-radius: 999px;
        background: var(--sc-gold);
    }

    .filter-dropdown-menu {
        position: absolute;
        z-index: 90;
        top: calc(100% + 5px);
        right: 0;
        left: 0;
        padding: 4px;
        border: 1px solid #e4dcd1;
        border-radius: 10px;
        background: #fff;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(47,37,25,.12);
    }

    .filter-dropdown.is-open .filter-dropdown-menu {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0);
    }

    .filter-dropdown-option {
        display: flex;
        width: 100%;
        min-height: 31px;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        border-radius: 7px;
        padding: 0 8px;
        color: #5b534a;
        font-size: 8.3px;
        font-weight: 500;
        text-align: left;
    }

    .filter-dropdown-option:hover,
    .filter-dropdown-option.is-selected {
        background: #fff7e8;
        color: #9a6810;
    }

    .filter-dropdown-check {
        width: 6px;
        height: 6px;
        flex: 0 0 6px;
        border-radius: 999px;
        background: var(--sc-gold);
        opacity: 0;
    }

    .filter-dropdown-option.is-selected .filter-dropdown-check {
        opacity: 1;
    }

    .master-filter-apply,
    .master-filter-reset {
        min-height: 38px !important;
        height: 38px !important;
        border-radius: 9px !important;
        padding-inline: 11px !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 8.5px !important;
        font-weight: 600 !important;
        white-space: nowrap;
    }

    .master-filter-apply {
        box-shadow: 0 5px 14px rgba(217,149,0,.13) !important;
    }

    .master-filter-apply:hover {
        transform: translateY(-1px);
    }

    .master-filter-reset {
        box-shadow: none !important;
    }

    /* ---------- Queue surfaces ---------- */
    [data-compliance-panel] {
        margin-top: 10px !important;
        overflow: hidden;
        border: 1px solid var(--sc-line);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 6px 20px rgba(61,43,22,.045);
    }

    [data-compliance-panel][hidden] {
        display: none !important;
    }

    #compliancePanel-flagged .flagged-list-shell,
    #compliancePanel-flagged #flaggedSellerList {
        border: 0 !important;
        border-radius: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .flagged-list-shell {
        overflow-x: auto;
    }

    .flagged-table-head {
        min-width: 980px;
        min-height: 40px;
        align-items: center;
        border-bottom: 1px solid #eee8df !important;
        background: #faf9f6 !important;
        padding: 10px 14px !important;
        color: #81786d !important;
        font-size: 8px !important;
        font-weight: 700 !important;
        line-height: 1.3 !important;
        letter-spacing: .055em !important;
        text-transform: uppercase;
    }

    #flaggedSellerList {
        min-width: 980px;
    }

    .flagged-seller-row-modern {
        border: 0 !important;
        border-bottom: 1px solid #f0ebe4 !important;
        border-radius: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
        transform: none !important;
        content-visibility: auto;
        contain-intrinsic-size: 62px;
    }

    .flagged-seller-row-modern:hover {
        background: #fdfbf8 !important;
        transform: none !important;
    }

    #flaggedSellerList > article:last-of-type {
        border-bottom: 0 !important;
    }

    @media (min-width: 1280px) {
        .flagged-table-head,
        .flagged-seller-row-modern > div {
            grid-template-columns:
                minmax(285px, 1.85fr)
                130px
                105px
                120px
                145px
                58px !important;
            gap: 12px !important;
        }

        .flagged-seller-row-modern > div > div {
            min-height: 60px !important;
        }
    }

    .flagged-seller-row-modern > div > div {
        padding-top: 9px !important;
        padding-bottom: 9px !important;
    }

    .flagged-seller-avatar {
        width: 32px !important;
        height: 32px !important;
        flex: 0 0 32px !important;
        border: 0 !important;
        border-radius: 50% !important;
        background: #f3f1ed !important;
        color: #655d55 !important;
        font-size: 8px !important;
        font-weight: 700 !important;
    }

    .flagged-seller-name {
        color: #2e2924 !important;
        font-size: 9px !important;
        font-weight: 700 !important;
        line-height: 1.3 !important;
    }

    .flagged-seller-email {
        margin-top: 2px !important;
        color: #978e83 !important;
        font-size: 7.2px !important;
        line-height: 1.3 !important;
    }

    .flagged-count-box {
        min-width: 27px !important;
        height: 25px !important;
        border: 1px solid #efdada !important;
        border-radius: 8px !important;
        background: #fff7f7 !important;
        color: #a65d5d !important;
        font-size: 8px !important;
        font-weight: 700 !important;
    }

    .flagged-count-copy,
    .flagged-last-relative,
    .flagged-mobile-label {
        font-size: 7.2px !important;
    }

    .flagged-warning-badge,
    .flagged-risk-badge {
        padding: 4px 7px !important;
        border-radius: 999px !important;
        font-size: 7.5px !important;
        font-weight: 600 !important;
        line-height: 1 !important;
    }

    .flagged-last-date {
        font-size: 8px !important;
        font-weight: 600 !important;
    }

    .flagged-review-button {
        display: inline-grid !important;
        width: 28px !important;
        min-width: 28px !important;
        height: 28px !important;
        min-height: 28px !important;
        place-items: center;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 7px !important;
        background: transparent !important;
        color: #4d4842 !important;
        box-shadow: none !important;
    }

    .flagged-review-button svg {
        width: 14px !important;
        height: 14px !important;
    }

    .flagged-review-button:hover,
    .flagged-review-button:focus-visible {
        outline: none;
        background: #fff7e8 !important;
        color: #d99500 !important;
        transform: translateY(-1px);
    }

    .flagged-table-footer {
        min-height: 50px !important;
        padding: 9px 14px !important;
        background: #fff !important;
    }

    #flaggedSellerResultCount {
        color: #756d63 !important;
        font-size: 8px !important;
    }

    #flaggedSellerFilterEmpty {
        border-top: 1px solid #eee8df;
    }

    /* ---------- Other queue cards/tables ---------- */
    .compliance-card {
        border-radius: 11px !important;
        border-color: var(--sc-line) !important;
        background: #fff !important;
        box-shadow: 0 3px 10px rgba(61,43,22,.035) !important;
        contain: content;
    }

    .compliance-card:hover {
        border-color: #ddcfbb !important;
        box-shadow: 0 5px 15px rgba(61,43,22,.045) !important;
        transform: none !important;
    }

    #compliancePanel-pending > div:first-child,
    #compliancePanel-warnings > div:first-child,
    #compliancePanel-suspended > div:first-child,
    #compliancePanel-messages > div:first-child {
        padding: 11px 14px !important;
        background: #faf9f6;
    }

    #compliancePanel-pending > div:first-child p:first-child,
    #compliancePanel-warnings > div:first-child p:first-child,
    #compliancePanel-suspended > div:first-child p:first-child,
    #compliancePanel-messages > div:first-child p:first-child {
        font-size: 10px !important;
    }

    #compliancePanel-pending > div:first-child p:last-child,
    #compliancePanel-warnings > div:first-child p:last-child,
    #compliancePanel-suspended > div:first-child p:last-child,
    #compliancePanel-messages > div:first-child p:last-child {
        font-size: 7.5px !important;
    }

    #compliancePanel-pending > .grid,
    #compliancePanel-suspended > .grid,
    #compliancePanel-messages > .space-y-3 {
        gap: 9px !important;
        padding: 12px !important;
    }

    #compliancePanel-pending article,
    #compliancePanel-suspended article,
    #compliancePanel-messages article {
        content-visibility: auto;
        contain-intrinsic-size: 120px;
    }

    .seller-compliance-page table th {
        padding-top: 9px !important;
        padding-bottom: 9px !important;
        font-size: 7.5px !important;
        line-height: 1.3 !important;
    }

    .seller-compliance-page table td {
        padding-top: 9px !important;
        padding-bottom: 9px !important;
        font-size: 8px !important;
        line-height: 1.45 !important;
    }

    .compliance-control {
        min-height: 38px !important;
        border: 1px solid #e5ddd2 !important;
        border-radius: 9px !important;
        background: #fff !important;
        color: #332e28 !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 8.5px !important;
        box-shadow: none !important;
    }

    .compliance-control::placeholder {
        color: #aaa196 !important;
        opacity: 1;
    }

    .compliance-control:focus {
        outline: none;
        border-color: #d49a2b !important;
        box-shadow: 0 0 0 3px rgba(217,149,0,.075) !important;
    }

    textarea.compliance-control {
        min-height: 84px !important;
    }

    /* ============================================================
       SELLER REVIEW MODAL — CONTENT-ADAPTIVE ENTERPRISE LAYOUT
       ============================================================ */
    .seller-review-modal {
        background: rgba(28, 24, 20, .44) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        padding: 14px !important;
    }

    @keyframes sellerReviewDialogIn {
        from { opacity: 0; transform: translateY(8px) scale(.99); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .seller-review-modal:not(.hidden) .seller-review-dialog {
        animation: sellerReviewDialogIn .18s cubic-bezier(.22,1,.36,1) both;
    }

    .seller-review-dialog {
        width: min(900px, calc(100vw - 28px)) !important;
        max-width: 900px !important;
        max-height: min(88vh, 760px) !important;
        overflow: hidden !important;
        border: 1px solid #dfd8cf !important;
        border-radius: 16px !important;
        background: #fff !important;
        box-shadow:
            0 24px 64px rgba(31,24,17,.18),
            0 8px 22px rgba(31,24,17,.07) !important;
    }

    .seller-review-header {
        min-height: 58px;
        align-items: center !important;
        gap: 12px !important;
        padding: 10px 14px !important;
        border-bottom: 1px solid #ebe5dd !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .seller-review-identity {
        align-items: center !important;
        gap: 0 !important;
    }

    .seller-review-identity > div:first-child {
        display: none !important;
    }

    .seller-review-eyebrow {
        margin: 0 0 3px !important;
        color: #9a7b43 !important;
        font-size: 6.5px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
        letter-spacing: .11em !important;
        text-transform: uppercase !important;
    }

    .seller-review-header h3 {
        color: #25221e !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
        letter-spacing: -.025em !important;
    }

    .seller-review-header .seller-review-identity p:last-child {
        margin-top: 4px !important;
        color: #8a8177 !important;
        font-size: 7.5px !important;
    }

    .seller-review-header .rounded-full.border {
        padding: 4px 7px !important;
        font-size: 6.5px !important;
    }

    .seller-review-toolbar {
        gap: 6px !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
    }

    .seller-review-suspend-btn {
        min-height: 32px !important;
        height: 32px !important;
        gap: 6px !important;
        border: 1px solid #e2d8cc !important;
        border-radius: 8px !important;
        background: #fff !important;
        padding-inline: 9px !important;
        color: #625950 !important;
        font-size: 7.5px !important;
        font-weight: 600 !important;
        box-shadow: none !important;
    }

    .seller-review-suspend-btn:hover,
    .seller-review-suspend-btn:focus-visible {
        outline: none !important;
        border-color: #efc466 !important;
        background: #fffaf0 !important;
        color: #c8880b !important;
        transform: none !important;
    }

    .seller-review-close-btn {
        width: 30px !important;
        height: 30px !important;
        min-height: 30px !important;
        border: 1px solid #e4ddd4 !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: #71685f !important;
        box-shadow: none !important;
    }

    .seller-review-close-btn:hover {
        background: #f7f5f2 !important;
        color: #332d27 !important;
        transform: none !important;
    }

    .seller-review-body {
        min-height: 0;
        padding: 12px 14px 14px !important;
        background: #f8f7f4 !important;
        scrollbar-width: thin;
        scrollbar-color: #d0c8be transparent;
    }

    .seller-review-body > .mb-3 h4 {
        font-size: 10.5px !important;
    }

    .seller-review-body > .mb-3 p {
        margin-top: 3px !important;
        font-size: 7.5px !important;
    }

    .seller-review-stats-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 7px !important;
    }

    .seller-review-stat {
        min-height: 54px !important;
        padding: 8px 9px !important;
        border: 1px solid #e5ded5 !important;
        border-radius: 9px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .seller-review-stat::after {
        display: none !important;
        content: none !important;
    }

    .seller-review-stat p:first-child {
        margin: 0 !important;
        color: #91887d !important;
        font-size: 6.5px !important;
    }

    .seller-review-stat p:last-child {
        margin-top: 4px !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
        color: #35302b !important;
        font-size: 9px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
    }

    .seller-review-section-heading {
        margin: 12px 0 7px !important;
        padding: 0 1px !important;
    }

    .seller-review-section-heading h4 {
        color: #39332d !important;
        font-size: 9px !important;
        font-weight: 700 !important;
    }

    .seller-review-section-heading p {
        margin-top: 2px !important;
        color: #91887d !important;
        font-size: 7px !important;
        line-height: 1.45 !important;
    }

    .seller-review-section-count {
        min-height: 22px !important;
        border: 1px solid #e3ddd4 !important;
        border-radius: 999px !important;
        background: #fff !important;
        padding: 0 7px !important;
        color: #756d63 !important;
        font-size: 6.5px !important;
        font-weight: 600 !important;
        box-shadow: none !important;
    }

    .seller-review-listings {
        gap: 9px !important;
    }

    .seller-review-product {
        overflow: hidden !important;
        border: 1px solid #e3d8d5 !important;
        border-left: 3px solid #d86b68 !important;
        border-radius: 11px !important;
        background: #fff !important;
        box-shadow: none !important;
        content-visibility: auto;
        contain-intrinsic-size: 540px;
    }

    .seller-review-product-intro {
        padding: 8px 10px !important;
        border-bottom: 1px solid #eee4e2 !important;
        background: #fffafa !important;
    }

    .seller-review-product-intro::before {
        display: none !important;
        content: none !important;
    }

    .seller-review-product-intro p:first-child {
        color: #a45a57 !important;
        font-size: 6.5px !important;
        font-weight: 700 !important;
        letter-spacing: .06em !important;
    }

    .seller-review-product-intro p:last-child {
        margin-top: 2px !important;
        color: #94807e !important;
        font-size: 6.5px !important;
    }

    .seller-review-product-intro .rounded-full {
        padding: 4px 7px !important;
        font-size: 6.3px !important;
    }

    .seller-review-product-summary {
        display: grid !important;
        grid-template-columns: 82px minmax(0, 1fr) !important;
        gap: 11px !important;
        padding: 11px !important;
        background: #fff !important;
    }

    .seller-review-product-image {
        width: 82px !important;
        height: 82px !important;
        min-height: 82px !important;
        border: 1px solid #e4ddd4 !important;
        border-radius: 9px !important;
        background: #f7f5f2 !important;
        box-shadow: none !important;
    }

    .seller-review-product-main h4 {
        color: #302b26 !important;
        font-size: 9.5px !important;
        font-weight: 700 !important;
        line-height: 1.3 !important;
    }

    .seller-review-product-main > div:first-child > div:first-child > p {
        margin-top: 3px !important;
        color: #81786c !important;
        font-size: 7.2px !important;
    }

    .seller-review-product-main > div:first-child > span {
        padding: 4px 7px !important;
        font-size: 6.5px !important;
    }

    .seller-review-product-stats {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 0 !important;
        margin-top: 9px !important;
        padding-top: 8px !important;
        border-top: 1px solid #eee9e3 !important;
    }

    .seller-review-product-metric {
        position: relative;
        min-height: 38px !important;
        padding: 1px 9px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-review-product-metric:first-child {
        padding-left: 0 !important;
    }

    .seller-review-product-metric + .seller-review-product-metric::before {
        content: "";
        position: absolute;
        top: 1px;
        bottom: 1px;
        left: 0;
        width: 1px;
        background: #eee9e3;
    }

    .seller-review-product-metric p:first-child {
        color: #979087 !important;
        font-size: 6.3px !important;
        font-weight: 400 !important;
    }

    .seller-review-product-metric p:last-child {
        margin-top: 4px !important;
        color: #35302b !important;
        font-size: 7.5px !important;
        font-weight: 700 !important;
    }

    .seller-review-subsection {
        padding: 8px 11px !important;
        border-top: 1px solid #eee9e3 !important;
        background: #faf9f7 !important;
    }

    .seller-review-subsection p:first-child {
        color: #4e4740 !important;
        font-size: 7.5px !important;
        font-weight: 700 !important;
    }

    .seller-review-subsection p:last-child {
        margin-top: 2px !important;
        color: #91887d !important;
        font-size: 6.5px !important;
    }

    .seller-screening-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px !important;
        padding: 10px !important;
        border-top: 1px solid #eee9e3 !important;
        background: #fff !important;
    }

    .screening-panel {
        min-height: 0 !important;
        padding: 10px !important;
        border: 1px solid #e5dfd7 !important;
        border-radius: 9px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .screening-panel::before {
        display: none !important;
        content: none !important;
    }

    .screening-panel > div:first-child p {
        font-size: 7.5px !important;
        font-weight: 700 !important;
    }

    .screening-panel > p {
        margin-top: 6px !important;
        color: #6f675e !important;
        font-size: 7px !important;
        line-height: 1.5 !important;
    }

    .screening-panel span {
        font-size: 6.3px !important;
    }

    .seller-ai-meta {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0 !important;
        margin-top: 8px !important;
        padding-top: 7px !important;
        border-top: 1px solid #eee9e3 !important;
    }

    .seller-ai-meta-item {
        position: relative;
        min-height: 36px !important;
        padding: 0 8px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
    }

    .seller-ai-meta-item:first-child {
        padding-left: 0 !important;
    }

    .seller-ai-meta-item + .seller-ai-meta-item::before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 1px;
        background: #eee9e3;
    }

    .seller-ai-meta-label {
        color: #979087 !important;
        font-size: 6.2px !important;
    }

    .seller-ai-meta-value {
        margin-top: 4px !important;
        color: #39342f !important;
        font-size: 7.2px !important;
        font-weight: 700 !important;
    }

    .seller-review-product details {
        border-top: 1px solid #eee9e3 !important;
    }

    .seller-review-product details > summary {
        min-height: 40px !important;
        padding: 8px 11px !important;
        background: #fff !important;
    }

    .seller-review-product details > summary:hover,
    .seller-review-product details[open] > summary {
        background: #faf9f7 !important;
    }

    .seller-review-product details > summary p:first-child {
        font-size: 7.5px !important;
        font-weight: 700 !important;
    }

    .seller-review-product details > summary p:last-child {
        font-size: 6.5px !important;
    }

    .seller-review-product details > div {
        padding: 10px !important;
        background: #fff !important;
    }

    .seller-review-product details .rounded-xl.border,
    .seller-review-product details .rounded-\[14px\].border {
        border-radius: 8px !important;
        box-shadow: none !important;
    }

    .seller-review-actions {
        gap: 7px !important;
        padding: 9px 10px 10px !important;
        border-top: 1px solid #eee9e3 !important;
        background: #fff !important;
    }

    .seller-action-btn {
        min-height: 35px !important;
        height: 35px !important;
        gap: 6px !important;
        border: 1px solid #ddd7cf !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: #3f3933 !important;
        font-size: 7.5px !important;
        font-weight: 600 !important;
        box-shadow: none !important;
    }

    .seller-action-btn svg {
        width: 13px !important;
        height: 13px !important;
    }

    .seller-action-btn:hover,
    .seller-action-btn:focus-visible {
        outline: none;
        transform: translateY(-1px);
        box-shadow: none !important;
    }

    .seller-action-approve:hover,
    .seller-action-approve:focus-visible {
        border-color: #8bd4a5 !important;
        background: #f5fff8 !important;
        color: #218b49 !important;
    }

    .seller-action-reject:hover,
    .seller-action-reject:focus-visible {
        border-color: #edaaaa !important;
        background: #fff8f8 !important;
        color: #d94c4c !important;
    }

    .seller-action-warn:hover,
    .seller-action-warn:focus-visible {
        border-color: #edc56f !important;
        background: #fffaf0 !important;
        color: #d88f00 !important;
    }

    .seller-review-body > section {
        border: 1px solid #e4ddd4 !important;
        border-radius: 10px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    /* ---------- Warning modal ---------- */
    .warning-modal-backdrop {
        background: rgba(28,24,20,.44) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }

    .warning-modal-dialog {
        width: min(500px, calc(100vw - 24px)) !important;
        max-width: 500px !important;
        border: 1px solid #e4d9d5 !important;
        border-radius: 14px !important;
        padding: 16px !important;
        background: #fff !important;
        box-shadow:
            0 22px 56px rgba(31,24,17,.17),
            0 7px 20px rgba(31,24,17,.06) !important;
    }

    .warning-modal-dialog h3 {
        font-size: 14px !important;
    }

    .warning-modal-dialog label {
        font-size: 7.5px !important;
    }

    .warning-modal-dialog button {
        min-height: 36px !important;
        border-radius: 8px !important;
        font-size: 7.8px !important;
    }

    /* ---------- Performance containment ---------- */
    #compliancePanel-warnings tbody tr {
        content-visibility: auto;
        contain-intrinsic-size: 44px;
    }

    /* ---------- Laptop 100% zoom ---------- */
    @media (max-height: 850px) and (min-width: 900px) {
        .compliance-page-title {
            font-size: 22px !important;
        }

        .compliance-summary-card {
            min-height: 70px !important;
            padding-top: 9px !important;
            padding-bottom: 9px !important;
        }

        .compliance-summary-card > div > div:first-child > p:nth-child(2) {
            font-size: 18px !important;
        }

        .seller-review-dialog {
            max-height: calc(100vh - 26px) !important;
        }

        .seller-review-body {
            padding-top: 10px !important;
        }
    }

    /* ---------- Tablet ---------- */
    @media (max-width: 1023px) {
        .compliance-master-filter {
            grid-template-columns: minmax(0, 1fr) minmax(140px, .45fr);
        }

        .compliance-master-filter .master-search {
            grid-column: 1 / -1;
        }

        .master-filter-apply,
        .master-filter-reset {
            width: 100%;
        }

        #flaggedSellerList {
            min-width: 0;
        }

        .flagged-table-head {
            display: none !important;
        }

        .flagged-seller-row-modern {
            margin: 10px !important;
            border: 1px solid var(--sc-line) !important;
            border-radius: 11px !important;
        }
    }

    /* ---------- Mobile ---------- */
    @media (max-width: 639px) {
        .seller-compliance-page {
            padding-bottom: 14px;
        }

        .compliance-page-header-main {
            align-items: flex-start;
        }

        .compliance-page-icon {
            width: 34px !important;
            height: 34px !important;
            flex-basis: 34px !important;
        }

        .compliance-page-title {
            font-size: 22px !important;
        }

        .compliance-page-subtitle {
            font-size: 9px !important;
        }

        .compliance-account-control {
            width: 100%;
            min-height: 40px !important;
            font-size: 9px !important;
        }

        .compliance-summary-grid {
            grid-template-columns: 1fr 1fr !important;
        }

        .compliance-summary-card {
            min-height: 74px !important;
        }

        .compliance-master-filter {
            grid-template-columns: 1fr;
        }

        .compliance-master-filter .master-search {
            grid-column: auto;
        }

        .master-filter-control,
        .filter-dropdown-toggle,
        .master-filter-apply,
        .master-filter-reset {
            min-height: 42px !important;
            height: 42px !important;
            font-size: 9.5px !important;
        }

        .seller-review-modal {
            padding: 7px !important;
        }

        .seller-review-dialog {
            width: calc(100vw - 14px) !important;
            max-height: calc(100vh - 14px) !important;
            border-radius: 13px !important;
        }

        .seller-review-header {
            align-items: flex-start !important;
            padding: 10px 11px !important;
        }

        .seller-review-toolbar {
            width: 100%;
            justify-content: flex-end;
        }

        .seller-review-body {
            padding: 9px !important;
        }

        .seller-review-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .seller-review-product-summary {
            grid-template-columns: 1fr !important;
        }

        .seller-review-product-image {
            width: 100% !important;
            height: 150px !important;
        }

        .seller-screening-grid,
        .seller-review-actions {
            grid-template-columns: 1fr !important;
        }

        .seller-review-product-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            row-gap: 8px !important;
        }

        .seller-review-product-metric:nth-child(3)::before {
            display: none;
        }

        .seller-ai-meta {
            grid-template-columns: 1fr !important;
        }

        .seller-ai-meta-item {
            padding: 7px 0 !important;
        }

        .seller-ai-meta-item + .seller-ai-meta-item::before {
            top: 0;
            right: 0;
            bottom: auto;
            left: 0;
            width: auto;
            height: 1px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-compliance-page *,
        .seller-review-modal,
        .seller-review-dialog {
            scroll-behavior: auto !important;
            animation: none !important;
            transition: none !important;
            transform: none !important;
        }
    }

    /* ============================================================
       SELLER COMPLIANCE — HEADER SCALE MATCH
       Matches Platform Settings / Commissions / Complaints.
       Visual-only; compliance logic and backend remain untouched.
       ============================================================ */

    .seller-compliance-page .compliance-page-header{
        display:flex !important;
        align-items:center !important;
        justify-content:space-between !important;
        gap:20px !important;
        margin-bottom:16px !important;
    }

    .seller-compliance-page .compliance-page-header-main{
        display:flex !important;
        min-width:0 !important;
        align-items:center !important;
        gap:13px !important;
    }

    .seller-compliance-page .compliance-page-icon{
        width:44px !important;
        height:44px !important;
        flex:0 0 44px !important;
        border-radius:12px !important;
        box-shadow:0 4px 12px rgba(75,54,25,.045) !important;
    }

    .seller-compliance-page .compliance-page-icon svg{
        width:17px !important;
        height:17px !important;
    }

    .seller-compliance-page .compliance-eyebrow{
        color:#9a6f23 !important;
        font-size:8px !important;
        font-weight:700 !important;
        line-height:1.15 !important;
        letter-spacing:.13em !important;
    }

    .seller-compliance-page .compliance-page-title{
        margin:5px 0 0 !important;
        font-size:29px !important;
        font-weight:700 !important;
        line-height:1.02 !important;
        letter-spacing:-.045em !important;
    }

    .seller-compliance-page .compliance-title-base{
        color:#17130f !important;
    }

    .seller-compliance-page .compliance-title-accent{
        color:#d99500 !important;
    }

    .seller-compliance-page .compliance-page-subtitle{
        max-width:820px !important;
        margin-top:7px !important;
        color:#7f756a !important;
        font-size:11px !important;
        font-weight:400 !important;
        line-height:1.5 !important;
    }

    .seller-compliance-page .compliance-account-control{
        min-height:42px !important;
        height:42px !important;
        gap:7px !important;
        border-radius:10px !important;
        padding:0 13px !important;
        font-size:8.5px !important;
        box-shadow:0 4px 10px rgba(217,149,0,.11) !important;
    }

    .seller-compliance-page .compliance-account-control svg{
        width:13px !important;
        height:13px !important;
    }

    @media(max-height:850px) and (min-width:900px){
        .seller-compliance-page .compliance-page-header{
            margin-bottom:14px !important;
        }

        .seller-compliance-page .compliance-page-icon{
            width:42px !important;
            height:42px !important;
            flex-basis:42px !important;
        }

        .seller-compliance-page .compliance-page-title{
            font-size:27px !important;
        }

        .seller-compliance-page .compliance-page-subtitle{
            font-size:10.5px !important;
        }

        .seller-compliance-page .compliance-account-control{
            min-height:40px !important;
            height:40px !important;
        }
    }

    @media(max-width:639px){
        .seller-compliance-page .compliance-page-header{
            align-items:flex-start !important;
            gap:12px !important;
        }

        .seller-compliance-page .compliance-page-header-main{
            align-items:flex-start !important;
            gap:11px !important;
        }

        .seller-compliance-page .compliance-page-icon{
            width:40px !important;
            height:40px !important;
            flex-basis:40px !important;
            border-radius:11px !important;
        }

        .seller-compliance-page .compliance-page-title{
            font-size:24px !important;
        }

        .seller-compliance-page .compliance-page-subtitle{
            font-size:10px !important;
        }

        .seller-compliance-page .compliance-account-control{
            width:100% !important;
            min-height:40px !important;
            height:40px !important;
            font-size:9px !important;
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
            <article class="compliance-summary-card relative rounded-[17px] border border-[#ebe4da] bg-white p-3.5 pr-20 sm:p-4 sm:pr-20">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[8px] font-medium text-[#91887d]">{{ $card['label'] }}</p>
                        <p class="mt-2 text-[22px] font-bold tracking-[-.03em] text-[#28221b]">{{ $card['value'] }}</p>
                    </div>

                    <div class="absolute right-4 top-4 grid h-[34px] w-[34px] shrink-0 place-items-center rounded-[11px] border {{ $card['tone'] }} sm:right-[18px] sm:top-[18px]">
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
                                                            width="108"
                                                            height="108"
                                                            loading="lazy"
                                                            decoding="async"
                                                            fetchpriority="low"
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
                                    $sellerWarningHistory = $complianceWarningsBySeller
                                        ->get((string) ($seller?->id ?? ''), collect())
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
                                    <img
                                        src="{{ route('seller.products.image', $product) }}"
                                        alt="{{ $product->name }}"
                                        width="64"
                                        height="64"
                                        loading="lazy"
                                        decoding="async"
                                        fetchpriority="low"
                                        class="h-full w-full object-cover"
                                    >
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

    const flaggedSellerIndex = flaggedSellerRows.map(function (row) {
        return {
            row,
            searchable: (row.dataset.flaggedSellerSearch || '').toLowerCase(),
            risk: (row.dataset.flaggedSellerRisk || '').toLowerCase(),
        };
    });

    function debounce(callback, wait = 130) {
        let timer = 0;

        return function (...args) {
            window.clearTimeout(timer);
            timer = window.setTimeout(() => callback.apply(this, args), wait);
        };
    }

    let flaggedFilterFrame = 0;

    function filterFlaggedSellers() {
        const query = (flaggedSellerSearch?.value || '').trim().toLowerCase();
        const risk = (flaggedSellerRiskFilter?.value || '').trim().toLowerCase();

        window.cancelAnimationFrame(flaggedFilterFrame);

        flaggedFilterFrame = window.requestAnimationFrame(function () {
            let visible = 0;

            flaggedSellerIndex.forEach(function (item) {
                const matchesQuery = query === '' || item.searchable.includes(query);
                const matchesRisk = risk === '' || item.risk === risk;
                const matches = matchesQuery && matchesRisk;
                const shouldHide = !matches;

                if (item.row.classList.contains('hidden') !== shouldHide) {
                    item.row.classList.toggle('hidden', shouldHide);
                }

                if (matches) visible++;
            });

            if (flaggedSellerResultCount) {
                flaggedSellerResultCount.textContent =
                    'Showing ' + visible +
                    ' of ' + flaggedSellerIndex.length +
                    ' flagged seller' + (visible === 1 ? '' : 's');
            }

            const hasFilters = query !== '' || risk !== '';
            clearFlaggedSellerFilters?.classList.toggle('hidden', !hasFilters);

            if (flaggedSellerIndex.length > 0) {
                flaggedSellerList?.classList.toggle('hidden', visible === 0);
                flaggedSellerFilterEmpty?.classList.toggle('hidden', visible !== 0);
            }
        });
    }

    const debouncedFlaggedSellerFilter = debounce(filterFlaggedSellers, 130);

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

    flaggedSellerSearch?.addEventListener('input', debouncedFlaggedSellerFilter, { passive: true });
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

    function openSellerDetails(modalId) {
        const modal = document.getElementById(modalId);

        if (!modal) return;

        closeAllSellerDetails();

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        window.requestAnimationFrame(function () {
            modal.querySelector('[data-flagged-seller-close]')?.focus({ preventScroll: true });
        });
    }


    /*
    |--------------------------------------------------------------------------
    | WARNING MODAL
    |--------------------------------------------------------------------------
    */
    const warningModal = document.getElementById('warningModal');
    const warningForm = document.getElementById('warningForm');
    const warningMeta = document.getElementById('warningModalMeta');
    const thirdNotice = document.getElementById('thirdWarningNotice');

    function openWarningModal(button) {
        if (!button || !warningModal || !warningForm || !warningMeta || !thirdNotice) return;

        const productId = button.dataset.productId;
        const productName = button.dataset.productName;
        const sellerName = button.dataset.sellerName;
        const warningCount = Number(button.dataset.warningCount || 0);

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
    }

    function closeWarningModal() {
        warningModal?.classList.add('hidden');
        warningModal?.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    document.getElementById('warningModalClose')?.addEventListener('click', closeWarningModal);
    document.getElementById('warningCancel')?.addEventListener('click', closeWarningModal);

    document.addEventListener('click', function (event) {
        const openSellerButton = event.target.closest('[data-flagged-seller-open]');
        if (openSellerButton) {
            openSellerDetails(openSellerButton.dataset.flaggedSellerOpen);
            return;
        }

        if (event.target.closest('[data-flagged-seller-close]')) {
            closeAllSellerDetails();
            return;
        }

        const sellerBackdrop = event.target.closest('[data-flagged-seller-modal]');
        if (sellerBackdrop && event.target === sellerBackdrop) {
            closeAllSellerDetails();
            return;
        }

        const warningButton = event.target.closest('[data-warning-open]');
        if (warningButton) {
            openWarningModal(warningButton);
            return;
        }

        if (warningModal && event.target === warningModal) {
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
