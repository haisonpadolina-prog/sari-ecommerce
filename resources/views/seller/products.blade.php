@extends('layouts.seller')

@section('title', 'Products — SARI')
@section('page-title', 'Product Management')

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


<style>
    .seller-products-page {
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
    }

    .seller-products-card {
        min-width: 0;
        border-color: #e8e0d6;
        box-shadow:
            0 11px 26px rgba(43, 33, 21, .055),
            0 3px 8px rgba(43, 33, 21, .028),
            inset 0 1px 0 rgba(255, 255, 255, .96),
            inset 0 -1px 0 rgba(228, 220, 210, .54);
    }

    /* Product cards stay completely still on hover. */
    .seller-products-card:hover {
        transform: none;
        border-color: #e8e0d6;
        box-shadow:
            0 11px 26px rgba(43, 33, 21, .055),
            0 3px 8px rgba(43, 33, 21, .028),
            inset 0 1px 0 rgba(255, 255, 255, .96),
            inset 0 -1px 0 rgba(228, 220, 210, .54);
    }

    .seller-product-action {
        border: 1px solid #e2dbd1;
        background: #ffffff;
        color: #706960;
        box-shadow:
            0 3px 8px rgba(43, 33, 21, .035),
            inset 0 1px 0 rgba(255, 255, 255, .96);
        transition: background-color .16s ease, border-color .16s ease, color .16s ease, box-shadow .16s ease;
    }

    .seller-product-action--view:hover {
        border-color: #9fc2e0;
        background: #edf5fc;
        color: #2f6da4;
        box-shadow: 0 7px 16px rgba(63, 120, 173, .14);
    }

    .seller-product-action--edit:hover {
        border-color: #ead19a;
        background: #fff8e8;
        color: #b6760a;
        box-shadow: 0 5px 12px rgba(182, 118, 10, .10);
    }

    .seller-product-action--archive:hover {
        border-color: #ddc9a7;
        background: #faf6ef;
        color: #8d692f;
        box-shadow: 0 5px 12px rgba(141, 105, 47, .09);
    }

    .seller-product-action--delete:hover {
        border-color: #e2aaaa;
        background: #fff0f0;
        color: #b14b4b;
        box-shadow: 0 7px 16px rgba(180, 86, 86, .13);
    }

    .seller-product-action:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(212, 154, 45, .12);
    }

    .seller-products-add-card {
        background:
            radial-gradient(circle at 50% 28%, rgba(216, 151, 19, .075), transparent 31%),
            #fffefa;
        box-shadow: 0 8px 24px rgba(43, 33, 21, .035);
        transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    .seller-products-add-card:hover {
        transform: translateY(-2px);
        border-color: #d7ae55;
        background-color: #fffdf7;
        box-shadow: 0 16px 34px rgba(44, 34, 22, .065);
    }

    .seller-products-summary-card {
        box-shadow: 0 9px 25px rgba(43, 33, 21, .045);
        transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    .seller-products-summary-card:hover {
        transform: translateY(-1px);
        border-color: #ddd1bf;
        box-shadow: 0 13px 30px rgba(43, 33, 21, .06);
    }

    .seller-products-filter {
        transition: border-color .16s ease, box-shadow .16s ease, background-color .16s ease;
    }

    .seller-products-filter:focus {
        border-color: #d49a2d;
        box-shadow: 0 0 0 4px rgba(212,154,45,.09);
    }

    .seller-product-image {
        transform: none;
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-products-card,
        .seller-products-add-card,
        .seller-products-summary-card,
        .seller-product-image {
            transition: none !important;
            transform: none !important;
        }
    }

    /* ROOMIER PRODUCT CARDS — larger typography and breathing space */
    .seller-products-card {
        min-height: 400px;
        border-radius: 18px;
        box-shadow:
            0 12px 28px rgba(43, 33, 21, .06),
            0 3px 9px rgba(43, 33, 21, .028),
            inset 0 1px 0 rgba(255, 255, 255, .96),
            inset 0 -1px 0 rgba(228, 220, 210, .54);
    }

    .seller-products-card:hover {
        transform: none;
        border-color: #e8e0d6;
        box-shadow:
            0 12px 28px rgba(43, 33, 21, .06),
            0 3px 9px rgba(43, 33, 21, .028),
            inset 0 1px 0 rgba(255, 255, 255, .96),
            inset 0 -1px 0 rgba(228, 220, 210, .54);
    }

    .seller-products-add-card {
        min-height: 400px;
    }

    @media (max-width: 639px) {
        .seller-products-card,
        .seller-products-add-card {
            min-height: auto;
        }
    }



    .seller-products-summary-grid {
        align-items: stretch;
    }

    .seller-products-summary-item {
        box-shadow:
            0 10px 24px rgba(43, 33, 21, .05),
            0 3px 8px rgba(43, 33, 21, .022),
            inset 0 1px 0 rgba(255, 255, 255, .95),
            inset 0 -1px 0 rgba(231, 223, 212, .56);
    }

    .seller-products-summary-item:hover {
        box-shadow:
            0 12px 28px rgba(43, 33, 21, .058),
            0 4px 10px rgba(43, 33, 21, .026),
            inset 0 1px 0 rgba(255, 255, 255, .96),
            inset 0 -1px 0 rgba(231, 223, 212, .56);
    }

    @media (max-width: 1279px) {
        .seller-products-summary-grid {
            max-width: 980px;
        }
    }


    /* ============================================================
       CATALOG HEALTH + QUICK FILTERS + PRODUCT HEALTH
       Frontend-only presentation. No backend behavior is changed.
    ============================================================ */

    .seller-catalog-health {
        position: relative;
        overflow: hidden;
        box-shadow:
            0 12px 30px rgba(43, 33, 21, .055),
            0 3px 9px rgba(43, 33, 21, .025),
            inset 0 1px 0 rgba(255, 255, 255, .96),
            inset 0 -1px 0 rgba(229, 220, 208, .58);
    }

    .seller-catalog-health::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 3px;
        background: linear-gradient(180deg, #e5ae2b, #c68608);
    }

    .seller-catalog-health-score {
        letter-spacing: -.055em;
    }

    .seller-catalog-health-progress {
        height: 7px;
        overflow: hidden;
        border-radius: 999px;
        background: #eee8df;
        box-shadow: inset 0 1px 2px rgba(50, 39, 27, .06);
    }

    #productsCatalogHealthBar {
        width: 0;
        height: 100%;
        border-radius: inherit;
        background: #d49a2d;
        transition: width .35s ease, background-color .2s ease;
    }

    .seller-products-quick-filter {
        display: inline-flex;
        min-height: 36px;
        align-items: center;
        gap: 7px;
        border: 1px solid #e5ddd2;
        border-radius: 10px;
        background: #fff;
        padding: 0 11px;
        color: #746b61;
        font-size: 9px;
        font-weight: 600;
        box-shadow:
            0 4px 11px rgba(43, 33, 21, .035),
            inset 0 1px 0 rgba(255,255,255,.96);
        transition:
            border-color .16s ease,
            background-color .16s ease,
            color .16s ease,
            box-shadow .16s ease;
    }

    .seller-products-quick-filter:hover {
        border-color: #dcc793;
        background: #fffaf0;
        color: #91620f;
    }

    .seller-products-quick-filter.is-active {
        border-color: #d6a743;
        background: #fff7e5;
        color: #8f5e09;
        box-shadow:
            0 6px 15px rgba(195, 135, 17, .10),
            inset 0 1px 0 rgba(255,255,255,.98);
    }

    .seller-products-quick-count {
        display: inline-grid;
        min-width: 22px;
        height: 22px;
        place-items: center;
        border-radius: 7px;
        background: #f4f0ea;
        padding-inline: 6px;
        color: #766d63;
        font-size: 8px;
        font-weight: 700;
    }

    .seller-products-quick-filter.is-active .seller-products-quick-count {
        background: #ead7a6;
        color: #76500a;
    }


    @media (max-width: 639px) {
        .seller-products-quick-filter {
            min-height: 34px;
            padding-inline: 10px;
            font-size: 8.5px;
        }
    }


    /* ============================================================
       SELLER CATALOG STUDIO — SIDEBAR WORKSPACE
    ============================================================ */

    .seller-catalog-rail-shell,
    .seller-products-workspace {
        box-shadow:
            0 13px 32px rgba(43, 33, 21, .055),
            0 4px 10px rgba(43, 33, 21, .025),
            inset 0 1px 0 rgba(255,255,255,.96),
            inset 0 -1px 0 rgba(229,221,211,.58);
    }

    .seller-rail-stat {
        box-shadow:
            0 6px 16px rgba(43, 33, 21, .035),
            inset 0 1px 0 rgba(255,255,255,.96);
    }

    .seller-rail-primary-action {
        box-shadow:
            0 8px 18px rgba(201,141,25,.16),
            inset 0 1px 0 rgba(255,255,255,.18);
        transition: background-color .16s ease, box-shadow .16s ease;
    }

    .seller-rail-primary-action:hover {
        background: #b77d10;
        box-shadow: 0 10px 20px rgba(184,126,16,.20);
    }

    .seller-rail-secondary-action {
        box-shadow:
            0 5px 13px rgba(43,33,21,.035),
            inset 0 1px 0 rgba(255,255,255,.96);
        transition: border-color .16s ease, background-color .16s ease, color .16s ease;
    }

    .seller-rail-secondary-action:hover {
        border-color: #d7c39b;
        background: #fffaf1;
        color: #966719;
    }

    .seller-rail-health {
        box-shadow:
            0 7px 18px rgba(43,33,21,.035),
            inset 0 1px 0 rgba(255,255,255,.96);
    }

    .seller-products-quick-filter {
        width: 100%;
        min-height: 40px;
        justify-content: space-between;
        padding-inline: 11px;
        border-radius: 10px;
        background: #fff;
        font-size: 8.7px;
    }

    .seller-products-quick-filter:hover {
        border-color: #ddc792;
        background: #fffaf0;
        color: #8d6112;
    }

    .seller-products-quick-filter.is-active {
        border-color: #d7ad51;
        background: #fff6e0;
        color: #875a0a;
        box-shadow:
            0 6px 14px rgba(194,136,22,.09),
            inset 0 1px 0 rgba(255,255,255,.96);
    }

    .seller-quick-dot {
        width: 7px;
        height: 7px;
        flex: 0 0 auto;
        border-radius: 999px;
    }

    .seller-products-quick-count {
        min-width: 24px;
        height: 23px;
        border-radius: 7px;
        font-size: 8px;
    }

    .seller-products-workspace > .mt-4.rounded-\[14px\] {
        box-shadow: inset 0 1px 0 rgba(255,255,255,.92);
    }

    @media (min-width: 1280px) {
        .seller-catalog-rail {
            max-height: calc(100vh - 110px);
            overflow-y: auto;
            padding-right: 2px;
            scrollbar-width: thin;
            scrollbar-color: #ded4c6 transparent;
        }
    }


    /* ============================================================
       ADD PRODUCT — LOCAL PRODUCT MANAGEMENT MODAL
       Same existing form/endpoint; now opened from this page.
    ============================================================ */
    #sellerAddProductModal {
        opacity: 0;
        transition: opacity .20s ease, backdrop-filter .20s ease;
    }

    #sellerAddProductModal > :first-child {
        opacity: 0;
        transform: translateY(10px) scale(.988);
        transition:
            opacity .26s cubic-bezier(.22,1,.36,1),
            transform .26s cubic-bezier(.22,1,.36,1);
    }

    #sellerAddProductModal.seller-modal-visible {
        opacity: 1;
    }

    #sellerAddProductModal.seller-modal-visible > :first-child {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    @media (prefers-reduced-motion: reduce) {
        #sellerAddProductModal,
        #sellerAddProductModal > :first-child {
            transition: none !important;
            transform: none !important;
        }
    }


    /* Product CRUD belongs to Product Management. */
    #sellerViewProductModal,
    #sellerEditProductModal {
        opacity: 0;
        transition: opacity .20s ease, backdrop-filter .20s ease;
    }

    #sellerViewProductModal > :first-child,
    #sellerEditProductModal > :first-child {
        opacity: 0;
        transform: translateY(8px) scale(.992);
        transition: opacity .24s cubic-bezier(.22,1,.36,1), transform .24s cubic-bezier(.22,1,.36,1);
    }

    #sellerViewProductModal.seller-modal-visible,
    #sellerEditProductModal.seller-modal-visible {
        opacity: 1;
    }

    #sellerViewProductModal.seller-modal-visible > :first-child,
    #sellerEditProductModal.seller-modal-visible > :first-child {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    @media (prefers-reduced-motion: reduce) {
        #sellerViewProductModal,
        #sellerEditProductModal,
        #sellerViewProductModal > :first-child,
        #sellerEditProductModal > :first-child {
            transition: none !important;
            transform: none !important;
        }
    }


    /* ============================================================
       PREMIUM VIEW PRODUCT INSPECTOR
    ============================================================ */

    .seller-product-view-dialog {
        box-shadow:
            0 28px 80px rgba(37, 29, 20, .18),
            0 8px 24px rgba(37, 29, 20, .07);
    }

    .seller-view-metric {
        box-shadow:
            0 5px 14px rgba(43, 33, 21, .028),
            inset 0 1px 0 rgba(255, 255, 255, .96);
    }

    .seller-view-metric:hover {
        transform: none;
        border-color: #ebe4da;
        box-shadow:
            0 5px 14px rgba(43, 33, 21, .028),
            inset 0 1px 0 rgba(255, 255, 255, .96);
    }

    #sellerViewProductModal [data-view-spec-card] {
        box-shadow:
            0 4px 12px rgba(43, 33, 21, .025),
            inset 0 1px 0 rgba(255,255,255,.95);
    }

    #sellerViewProductModal tbody tr {
        background: #fff;
    }

    #sellerViewProductModal tbody tr + tr {
        border-top: 1px solid #eee9e2;
    }


    /* ============================================================
       CLEAN SELLER ADD PRODUCT / VARIATION BUILDER
    ============================================================ */

    #sellerAddProductForm .seller-add-section {
        box-shadow:
            0 5px 16px rgba(43, 33, 21, .025),
            inset 0 1px 0 rgba(255,255,255,.96);
    }

    .seller-selling-type-card {
        transition: border-color .16s ease, background-color .16s ease, box-shadow .16s ease;
    }

    .seller-selling-type-card:hover {
        transform: none;
    }

    .seller-variant-preset {
        height: 30px;
        border: 1px solid #ded6cb;
        border-radius: 9px;
        background: #fff;
        padding: 0 10px;
        color: #71675d;
        font-size: 7.5px;
        font-weight: 650;
        transition: border-color .15s ease, background-color .15s ease, color .15s ease;
    }

    .seller-variant-preset:hover {
        border-color: #d7b76f;
        background: #fff9ec;
        color: #956513;
    }

    #sellerVariantRows input {
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    #sellerVariantRows input:focus {
        border-color: #d48f08;
        box-shadow: 0 0 0 3px rgba(212,143,8,.08);
    }


    #sellerListingPreviewModal {
        opacity: 1;
    }

    #sellerListingPreviewModal table tbody tr {
        background: #fff;
    }

    #sellerListingPreviewModal table tbody tr + tr {
        border-top: 1px solid #eee9e2;
    }


    #sellerGalleryPreview img {
        display: block;
    }

    #sellerVariantRows [data-variant-image-preview] img {
        display: block;
    }


    /* ============================================================
       CLEAR ADD PRODUCT MODAL
    ============================================================ */

    #sellerAddProductModal .seller-add-modal-shell {
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
    }

    #sellerAddProductModal .seller-add-form-body {
        scrollbar-width: thin;
        scrollbar-color: #d9d1c6 transparent;
    }

    #sellerAddProductModal .seller-add-flow-section,
    #sellerAddProductModal .seller-add-subsection {
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-add-section-number {
        display: inline-grid;
        width: 27px;
        height: 27px;
        flex: 0 0 auto;
        place-items: center;
        border: 1px solid #e7d2a6;
        border-radius: 9px;
        background: #fff8e9;
        color: #a56e0e;
        font-size: 8px;
        font-weight: 800;
    }

    #sellerAddProductModal .seller-add-flow-guide {
        border: 1px solid #e7dfd4;
        border-radius: 15px;
        background: #fff;
        padding: 13px;
        box-shadow: 0 4px 14px rgba(43, 33, 21, .025);
    }

    #sellerAddProductModal .seller-add-flow-guide-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    #sellerAddProductModal .seller-add-flow-steps {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 7px;
        margin-top: 11px;
    }

    #sellerAddProductModal .seller-add-flow-step {
        display: flex;
        min-height: 38px;
        align-items: center;
        gap: 8px;
        border: 1px solid #e8e1d8;
        border-radius: 10px;
        background: #fcfbf9;
        padding: 0 10px;
        color: #6f665c;
        font-size: 7.8px;
        font-weight: 650;
        text-align: left;
        transition: border-color .15s ease, background-color .15s ease, color .15s ease;
    }

    #sellerAddProductModal .seller-add-flow-step:hover {
        border-color: #d8bd83;
        background: #fffaf0;
        color: #8f6010;
    }

    #sellerAddProductModal .seller-add-flow-step-dot {
        display: inline-grid;
        width: 21px;
        height: 21px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 7px;
        background: #f1ece5;
        color: #756b60;
        font-size: 7px;
        font-weight: 800;
    }

    #sellerAddProductModal .seller-add-flow-step:hover .seller-add-flow-step-dot {
        background: #f3dfb3;
        color: #84580d;
    }

    #sellerAddProductModal .seller-add-inline-optional,
    #sellerAddProductModal .seller-add-optional-card {
        overflow: hidden;
        border: 1px solid #e7dfd5;
        border-radius: 14px;
        background: #fff;
        box-shadow: none;
    }

    #sellerAddProductModal .seller-add-inline-summary,
    #sellerAddProductModal .seller-add-optional-summary {
        display: flex;
        min-height: 54px;
        cursor: pointer;
        list-style: none;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 11px 14px;
        user-select: none;
    }

    #sellerAddProductModal summary::-webkit-details-marker {
        display: none;
    }

    #sellerAddProductModal .seller-add-inline-summary:hover,
    #sellerAddProductModal .seller-add-optional-summary:hover {
        background: #fffdf8;
    }

    #sellerAddProductModal .seller-add-optional-body {
        border-top: 1px solid #eee8df;
        background: #fff;
        padding: 16px;
    }

    #sellerAddProductModal details[open] > summary .seller-add-details-chevron {
        transform: rotate(180deg);
    }

    #sellerAddProductModal .seller-add-details-chevron {
        transition: transform .16s ease;
    }

    #sellerAddProductModal .seller-selling-type-card {
        min-height: 112px;
    }

    #sellerAddProductModal .seller-selling-type-card:hover {
        transform: none !important;
    }

    #sellerAddProductModal input,
    #sellerAddProductModal select,
    #sellerAddProductModal textarea {
        font-family: inherit;
    }

    @media (max-width: 767px) {
        #sellerAddProductModal .seller-add-flow-guide-head {
            display: block;
        }

        #sellerAddProductModal .seller-add-flow-guide-head > span {
            display: inline-flex;
            margin-top: 8px;
        }

        #sellerAddProductModal .seller-add-flow-steps {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 479px) {
        #sellerAddProductModal .seller-add-flow-steps {
            grid-template-columns: 1fr;
        }
    }


    /* ============================================================
       CLEANER ADD PRODUCT PAGE LAYOUT
    ============================================================ */
    #sellerAddProductModal .seller-add-form-body {
        scrollbar-width: thin;
        scrollbar-color: #d8d1c7 transparent;
    }

    #sellerAddProductModal .seller-add-content-grid {
        display: grid;
        gap: 16px;
        align-items: start;
    }

    @media (min-width: 1280px) {
        #sellerAddProductModal .seller-add-content-grid {
            grid-template-columns: minmax(0, 1fr) 300px;
        }

        #sellerAddProductModal .seller-add-sidebar {
            position: sticky;
            top: 0;
        }
    }

    #sellerAddProductModal .seller-add-flow-guide {
        border-color: #e6ded4;
        background: linear-gradient(180deg, #ffffff 0%, #fcfbf8 100%);
        box-shadow: 0 8px 24px rgba(38, 31, 23, .04);
    }

    #sellerAddProductModal .seller-add-flow-step {
        min-height: 40px;
        border-color: #e7dfd4;
        background: #fff;
    }

    #sellerAddProductModal .seller-add-flow-step:hover {
        background: #fffaf2;
        border-color: #d6bb82;
        color: #8d6010;
    }

    #sellerAddProductModal .seller-add-flow-section,
    #sellerAddProductModal .seller-add-subsection,
    #sellerAddProductModal .seller-add-inline-optional,
    #sellerAddProductModal .seller-add-optional-card {
        border-color: #e7dfd4;
        box-shadow: 0 10px 30px rgba(36, 28, 19, .03);
    }

    #sellerAddProductModal .seller-add-flow-section,
    #sellerAddProductModal .seller-add-subsection {
        border-radius: 20px;
    }

    #sellerAddProductModal .seller-add-section-number {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        background: #fff8e9;
        border-color: #e7d2a6;
    }

    #sellerAddProductModal [data-product-type-card] {
        min-height: 0 !important;
        padding: 14px !important;
        border-radius: 16px !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal [data-product-type-card] .inline-flex.rounded-full {
        border: 1px solid #ede5d7;
    }

    #sellerAddProductModal [data-product-type-card] .h-10.w-10 {
        width: 42px;
        height: 42px;
        border-radius: 12px;
    }

    #sellerAddProductModal #sellerSimpleInventorySection,
    #sellerAddProductModal #sellerVariantSection {
        border-style: solid;
        background: linear-gradient(180deg, #ffffff 0%, #fffdfa 100%);
    }

    #sellerAddProductModal .seller-add-sidebar {
        display: none;
    }

    #sellerAddProductModal .seller-add-sidebar-card {
        border: 1px solid #e7dfd4;
        border-radius: 18px;
        background: #fff;
        padding: 16px;
        box-shadow: 0 12px 28px rgba(36, 28, 19, .04);
    }

    #sellerAddProductModal .seller-add-sidebar-card + .seller-add-sidebar-card {
        margin-top: 14px;
    }

    #sellerAddProductModal .seller-add-sidebar-icon {
        display: grid;
        width: 34px;
        height: 34px;
        flex: 0 0 auto;
        place-items: center;
        border: 1px solid #ece4d8;
        border-radius: 11px;
        background: #fcfaf6;
    }

    #sellerAddProductModal .seller-add-tip-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    #sellerAddProductModal .seller-add-tip-number {
        display: inline-grid;
        width: 20px;
        height: 20px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 999px;
        background: #f3dfb3;
        color: #84580d;
        font-size: 7px;
        font-weight: 800;
    }

    #sellerAddProductModal .seller-add-example-chip {
        display: block;
        border: 1px solid #ede5d8;
        border-radius: 13px;
        background: #fffcf6;
        padding: 10px 11px;
    }

    #sellerAddProductModal .seller-add-example-chip strong {
        display: block;
        font-size: 8px;
        line-height: 1.1;
    }

    #sellerAddProductModal .seller-add-example-chip span {
        display: block;
        margin-top: 4px;
        font-size: 7px;
        line-height: 1.45;
        color: #8f867b;
    }

    #sellerAddProductModal .seller-add-check-item {
        position: relative;
        padding-left: 22px;
        line-height: 1.5;
    }

    #sellerAddProductModal .seller-add-check-item::before {
        content: "";
        position: absolute;
        left: 0;
        top: .25rem;
        width: 14px;
        height: 14px;
        border-radius: 999px;
        border: 1px solid #d9cfbd;
        background: #fffaf1;
        box-shadow: inset 0 0 0 3px #fffaf1;
    }

    #sellerAddProductModal .sticky.bottom-0 {
        border-top-color: #ece5da;
        box-shadow: 0 -10px 24px rgba(24, 20, 15, .04);
    }

    @media (min-width: 1280px) {
        #sellerAddProductModal .seller-add-sidebar {
            display: block;
        }
    }


    /* ============================================================
       VARIANT BUILDER — CLEANER FULL-WIDTH LAYOUT
    ============================================================ */
    #sellerAddProductModal .seller-add-content-grid,
    #sellerAddProductModal .seller-add-sidebar {
        display: block;
    }

    #sellerAddProductModal #sellerVariantSection {
        border-radius: 20px;
    }

    #sellerAddProductModal .seller-variant-preset {
        height: 34px;
        border-radius: 999px;
        border: 1px solid #e0d5c2;
        background: #fff;
        padding: 0 12px;
        font-size: 8px;
        font-weight: 700;
        color: #6f6253;
        transition: all .15s ease;
    }

    #sellerAddProductModal .seller-variant-preset:hover {
        border-color: #d3b273;
        background: #fff8ec;
        color: #966617;
    }

    #sellerAddProductModal #sellerColorStepCard,
    #sellerAddProductModal #sellerSizeStepCard,
    #sellerAddProductModal #sellerVariantSection > .rounded-\[15px\] {
        box-shadow: 0 10px 24px rgba(36, 28, 19, .03);
    }

    #sellerAddProductModal #sellerVariantRows td {
        vertical-align: top;
    }

    #sellerAddProductModal #sellerVariantRows input {
        font-size: 12px;
    }

    #sellerAddProductModal [data-variant-option-row="true"] {
        grid-template-columns: 1fr;
        gap: 10px;
        border-radius: 14px;
        border-color: #e5ddd2;
        padding: 14px;
        box-shadow: 0 8px 22px rgba(36, 28, 19, .025);
    }

    @media (min-width: 768px) {
        #sellerAddProductModal [data-variant-option-row="true"] {
            grid-template-columns: 200px 1fr 42px;
            align-items: end;
        }
    }


    /* ============================================================
       SIMPLE DIRECT VARIANT ROWS
    ============================================================ */
    #sellerAddProductModal #sellerVariantSection {
        box-shadow: none !important;
    }

    #sellerAddProductModal #sellerVariantTableWrap {
        box-shadow: 0 9px 24px rgba(43, 33, 21, .028);
    }

    #sellerAddProductModal #sellerVariantRows tr {
        background: #fff;
    }

    #sellerAddProductModal #sellerVariantRows tr:hover {
        background: #fff;
    }

    #sellerAddProductModal #sellerVariantRows td {
        vertical-align: top;
    }

    #sellerAddProductModal #sellerVariantRows input {
        font-family: inherit;
    }

    @media (max-width: 767px) {
        #sellerAddProductModal #sellerVariantTableWrap {
            border-radius: 13px;
        }
    }


    /* ============================================================
       MINIMAL VARIANT CARDS
    ============================================================ */
    #sellerAddProductModal #sellerVariantSection {
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-manual-variant-card {
        box-shadow: 0 7px 20px rgba(38, 30, 20, .028);
    }

    #sellerAddProductModal .seller-manual-variant-card:hover {
        transform: none !important;
        border-color: #e7dfd5 !important;
        box-shadow: 0 7px 20px rgba(38, 30, 20, .028) !important;
    }

    #sellerAddProductModal .seller-variant-image-picker:hover {
        transform: none !important;
    }

    #sellerAddProductModal #sellerVariantTableWrap {
        box-shadow: none !important;
    }


    /* ============================================================
       ADD PRODUCT — COMPACT WHITE / POPPINS
    ============================================================ */

    #sellerAddProductModal,
    #sellerAddProductModal * {
        font-family: "Poppins", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    #sellerAddProductModal .seller-add-modal-shell,
    #sellerAddProductModal .seller-add-form-body,
    #sellerAddProductModal .seller-add-flow-guide,
    #sellerAddProductModal .seller-add-flow-section,
    #sellerAddProductModal .seller-add-subsection,
    #sellerAddProductModal .seller-add-inline-optional,
    #sellerAddProductModal .seller-add-optional-card,
    #sellerAddProductModal .seller-add-optional-body,
    #sellerAddProductModal [data-product-type-card],
    #sellerAddProductModal #sellerSimpleInventorySection,
    #sellerAddProductModal #sellerVariantSection,
    #sellerAddProductModal #sellerVariantTableWrap,
    #sellerAddProductModal .seller-manual-variant-card,
    #sellerAddProductModal #sellerGalleryEmpty,
    #sellerAddProductModal #sellerSpecificationEmpty {
        background: #ffffff !important;
    }

    #sellerAddProductModal .seller-add-form-body {
        padding: 14px 16px 88px !important;
        gap: 10px !important;
    }

    #sellerAddProductModal .seller-add-flow-guide {
        padding: 10px 12px !important;
        border-radius: 13px !important;
        border-color: #e8e3dd !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-add-flow-guide-head {
        align-items: center !important;
    }

    #sellerAddProductModal .seller-add-flow-steps {
        margin-top: 8px !important;
        gap: 6px !important;
    }

    #sellerAddProductModal .seller-add-flow-step {
        min-height: 34px !important;
        border-radius: 9px !important;
        background: #fff !important;
        padding: 0 9px !important;
        font-size: 7.4px !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-add-flow-step-dot {
        width: 19px !important;
        height: 19px !important;
        border-radius: 6px !important;
        background: #fff8e9 !important;
        color: #a66e0f !important;
        border: 1px solid #ead7ae;
    }

    #sellerAddProductModal .seller-add-flow-section,
    #sellerAddProductModal .seller-add-subsection,
    #sellerAddProductModal .seller-add-optional-card {
        border-color: #e8e2dc !important;
        border-radius: 15px !important;
        padding: 14px !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-add-section-number {
        width: 24px !important;
        height: 24px !important;
        border-radius: 8px !important;
        background: #fff8e9 !important;
        border-color: #ead6aa !important;
        font-size: 7px !important;
    }

    #sellerAddProductModal .seller-add-flow-section h3,
    #sellerAddProductModal .seller-add-subsection h3 {
        margin: 0 !important;
    }

    #sellerAddProductModal input[type="text"],
    #sellerAddProductModal input[type="number"],
    #sellerAddProductModal input[type="email"],
    #sellerAddProductModal select {
        height: 40px !important;
        border-radius: 10px !important;
        border-color: #dcd7d1 !important;
        background: #fff !important;
        font-size: 9px !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal textarea {
        min-height: 126px !important;
        border-radius: 12px !important;
        border-color: #dcd7d1 !important;
        background: #fff !important;
        font-size: 9px !important;
        line-height: 1.6 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal input:focus,
    #sellerAddProductModal select:focus,
    #sellerAddProductModal textarea:focus {
        border-color: #d39a2c !important;
        box-shadow: 0 0 0 3px rgba(211, 154, 44, .08) !important;
        background: #fff !important;
    }

    #sellerAddProductModal label {
        letter-spacing: 0 !important;
    }

    #sellerAddProductModal [data-product-type-card] {
        min-height: 0 !important;
        padding: 12px 13px !important;
        border-radius: 13px !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal [data-product-type-card]:hover {
        transform: none !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal [data-product-type-card].border-\[\#d5a64c\],
    #sellerAddProductModal [data-product-type-card].bg-\[\#fffaf0\] {
        background: #fff !important;
    }

    #sellerAddProductModal .seller-selling-type-card {
        background: #fff !important;
    }

    #sellerAddProductModal .seller-selling-type-card .bg-\[\#f7f5f2\],
    #sellerAddProductModal .seller-selling-type-card .bg-\[\#faf8f5\],
    #sellerAddProductModal .seller-selling-type-card .bg-\[\#fffaf0\] {
        background: #fff !important;
    }

    #sellerAddProductModal .seller-add-inline-summary,
    #sellerAddProductModal .seller-add-optional-summary {
        min-height: 46px !important;
        padding: 9px 12px !important;
        background: #fff !important;
    }

    #sellerAddProductModal .seller-add-inline-summary:hover,
    #sellerAddProductModal .seller-add-optional-summary:hover {
        background: #fff !important;
    }

    #sellerAddProductModal .seller-add-optional-body {
        padding: 13px !important;
        border-top-color: #ece7e1 !important;
    }

    #sellerAddProductModal #sellerGalleryEmpty,
    #sellerAddProductModal #sellerSpecificationEmpty,
    #sellerAddProductModal #sellerManualVariantEmpty {
        background: #fff !important;
        border-color: #ddd8d2 !important;
        padding-top: 14px !important;
        padding-bottom: 14px !important;
    }

    #sellerAddProductModal #sellerVariantSection {
        padding: 14px !important;
        background: #fff !important;
    }

    #sellerAddProductModal .seller-manual-variant-card {
        padding: 12px !important;
        border-radius: 13px !important;
        border-color: #e4dfda !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-manual-variant-card:hover {
        background: #fff !important;
        border-color: #e4dfda !important;
        box-shadow: none !important;
        transform: none !important;
    }

    #sellerAddProductModal .seller-variant-image-picker {
        background: #fff !important;
        border-color: #dcd7d1 !important;
    }

    #sellerAddProductModal .seller-variant-image-picker:hover {
        background: #fff !important;
        transform: none !important;
    }

    #sellerAddProductModal .sticky.bottom-0 {
        background: #fff !important;
        border-top-color: #e7e2dc !important;
        padding: 10px 16px !important;
        box-shadow: 0 -6px 18px rgba(24,20,15,.035) !important;
        backdrop-filter: none !important;
    }

    #sellerAddProductModal #cancelAddProductModal,
    #sellerAddProductModal #sellerPreviewProduct,
    #sellerAddProductModal #sellerSubmitProduct {
        height: 38px !important;
        border-radius: 10px !important;
        font-size: 8px !important;
    }

    #sellerAddProductModal #sellerPreviewProduct {
        background: #fff !important;
    }

    #sellerAddProductModal #sellerSubmitProduct {
        box-shadow: none !important;
    }

    @media (min-width: 640px) {
        #sellerAddProductModal .seller-add-form-body {
            padding: 16px 18px 92px !important;
        }
    }


    #sellerAddProductModal #sellerEnableVariants,
    #sellerAddProductModal #sellerUseSinglePrice {
        box-shadow: none !important;
    }

    #sellerAddProductModal #sellerEnableVariants:hover,
    #sellerAddProductModal #sellerUseSinglePrice:hover {
        transform: none !important;
    }


    /* ============================================================
       ADD PRODUCT — SINGLE A4-STYLE WHITE FORM
    ============================================================ */

    #sellerAddProductModal,
    #sellerAddProductModal * {
        font-family: "Poppins", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    #sellerAddProductModal .seller-add-modal-shell {
        width: min(94vw, 900px) !important;
        max-width: 900px !important;
        background: #fff !important;
        border-radius: 18px !important;
        overflow: hidden !important;
    }

    #sellerAddProductModal .seller-add-form-body {
        background: #fff !important;
        padding: 0 28px 92px !important;
        margin: 0 !important;
    }

    /* A4-like inner sheet */
    #sellerAddProductModal .seller-add-form-body > .space-y-4,
    #sellerAddProductModal .seller-add-form-body > .space-y-3 {
        display: block !important;
        max-width: 794px !important;
        margin: 0 auto !important;
        background: #fff !important;
    }

    /* Flow guide becomes a simple top strip, not a card. */
    #sellerAddProductModal .seller-add-flow-guide {
        margin: 0 auto !important;
        max-width: 794px !important;
        border: 0 !important;
        border-bottom: 1px solid #ece7e1 !important;
        border-radius: 0 !important;
        background: #fff !important;
        padding: 18px 0 14px !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-add-flow-guide-head {
        gap: 12px !important;
    }

    #sellerAddProductModal .seller-add-flow-guide-head p:first-child {
        font-size: 10px !important;
        font-weight: 700 !important;
        color: #302a24 !important;
    }

    #sellerAddProductModal .seller-add-flow-guide-head p:last-child {
        font-size: 8px !important;
        color: #7d746b !important;
    }

    #sellerAddProductModal .seller-add-flow-steps {
        margin-top: 12px !important;
        gap: 8px !important;
    }

    #sellerAddProductModal .seller-add-flow-step {
        min-height: 36px !important;
        border: 1px solid #e7e2dc !important;
        border-radius: 9px !important;
        background: #fff !important;
        color: #5d554d !important;
        font-size: 8px !important;
        font-weight: 600 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-add-flow-step:hover {
        background: #fff !important;
        border-color: #d7c39b !important;
        color: #8a5e10 !important;
        transform: none !important;
    }

    #sellerAddProductModal .seller-add-flow-step-dot {
        width: 20px !important;
        height: 20px !important;
        border-radius: 6px !important;
        background: #fff7e8 !important;
        color: #9b6813 !important;
        border: 1px solid #ead4a9 !important;
        font-size: 7px !important;
    }

    /* Every former card becomes one continuous form section. */
    #sellerAddProductModal .seller-add-flow-section,
    #sellerAddProductModal .seller-add-subsection,
    #sellerAddProductModal .seller-add-optional-card,
    #sellerAddProductModal .seller-add-inline-optional,
    #sellerAddProductModal #sellerSimpleInventorySection,
    #sellerAddProductModal #sellerVariantSection {
        margin: 0 !important;
        border: 0 !important;
        border-bottom: 1px solid #ece7e1 !important;
        border-radius: 0 !important;
        background: #fff !important;
        padding: 22px 0 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-add-flow-section:last-child,
    #sellerAddProductModal .seller-add-optional-card:last-child {
        border-bottom: 0 !important;
    }

    /* Optional detail rows stay flat within the same form. */
    #sellerAddProductModal .seller-add-inline-summary,
    #sellerAddProductModal .seller-add-optional-summary {
        min-height: 44px !important;
        padding: 10px 0 !important;
        border: 0 !important;
        background: #fff !important;
        color: #403930 !important;
    }

    #sellerAddProductModal .seller-add-inline-summary:hover,
    #sellerAddProductModal .seller-add-optional-summary:hover {
        background: #fff !important;
    }

    #sellerAddProductModal .seller-add-optional-body {
        border-top: 1px solid #eee9e3 !important;
        background: #fff !important;
        padding: 16px 0 0 !important;
    }

    /* Section headings: clearer and more readable. */
    #sellerAddProductModal .seller-add-section-number {
        width: 26px !important;
        height: 26px !important;
        border-radius: 8px !important;
        background: #fff7e8 !important;
        border-color: #ead4a9 !important;
        color: #98650f !important;
        font-size: 7.5px !important;
        font-weight: 800 !important;
    }

    #sellerAddProductModal .seller-add-flow-section p,
    #sellerAddProductModal .seller-add-subsection p,
    #sellerAddProductModal .seller-add-optional-card p {
        color: #736a61;
    }

    #sellerAddProductModal .seller-add-flow-section .text-\[12px\],
    #sellerAddProductModal .seller-add-subsection .text-\[11px\] {
        color: #2f2924 !important;
    }

    #sellerAddProductModal label {
        color: #4a433c !important;
        font-weight: 600 !important;
        font-size: 8.5px !important;
    }

    /* Input readability */
    #sellerAddProductModal input[type="text"],
    #sellerAddProductModal input[type="number"],
    #sellerAddProductModal input[type="email"],
    #sellerAddProductModal input[type="date"],
    #sellerAddProductModal select {
        height: 42px !important;
        border: 1px solid #d9d4ce !important;
        border-radius: 9px !important;
        background: #fff !important;
        color: #302b27 !important;
        font-size: 9.5px !important;
        font-weight: 500 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal input::placeholder,
    #sellerAddProductModal textarea::placeholder {
        color: #a39b93 !important;
        opacity: 1 !important;
    }

    #sellerAddProductModal textarea {
        min-height: 120px !important;
        border: 1px solid #d9d4ce !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: #302b27 !important;
        font-size: 9.5px !important;
        line-height: 1.65 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal input:focus,
    #sellerAddProductModal select:focus,
    #sellerAddProductModal textarea:focus {
        border-color: #c99735 !important;
        box-shadow: 0 0 0 3px rgba(201,151,53,.08) !important;
        outline: none !important;
    }

    /* Remove tinted blocks inside the form. */
    #sellerAddProductModal .bg-\[\#fbfaf8\],
    #sellerAddProductModal .bg-\[\#fcfbf8\],
    #sellerAddProductModal .bg-\[\#fffaf1\],
    #sellerAddProductModal .bg-\[\#fffefb\],
    #sellerAddProductModal .bg-\[\#fffefc\],
    #sellerAddProductModal .bg-\[\#f7fafc\],
    #sellerAddProductModal .bg-\[\#faf8f5\],
    #sellerAddProductModal .bg-\[\#f6f9fc\] {
        background: #fff !important;
    }

    /* Product-type / compact buttons stay white and flat. */
    #sellerAddProductModal [data-product-type-card],
    #sellerAddProductModal .seller-selling-type-card {
        background: #fff !important;
        border-color: #e2ddd7 !important;
        border-radius: 10px !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal [data-product-type-card]:hover,
    #sellerAddProductModal .seller-selling-type-card:hover {
        transform: none !important;
        box-shadow: none !important;
    }

    /* Product variants: one clean section in the same form. */
    #sellerAddProductModal .seller-manual-variant-card {
        border: 1px solid #e2ddd7 !important;
        border-radius: 10px !important;
        background: #fff !important;
        padding: 14px !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-manual-variant-card:hover {
        transform: none !important;
        border-color: #e2ddd7 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-variant-image-picker {
        border-color: #d9d4ce !important;
        background: #fff !important;
        border-radius: 9px !important;
    }

    #sellerAddProductModal #sellerManualVariantEmpty,
    #sellerAddProductModal #sellerGalleryEmpty,
    #sellerAddProductModal #sellerSpecificationEmpty {
        background: #fff !important;
        border-color: #ddd8d2 !important;
        border-radius: 9px !important;
        padding: 14px !important;
    }

    /* Footer stays part of the same white sheet. */
    #sellerAddProductModal .sticky.bottom-0 {
        background: #fff !important;
        border-top: 1px solid #e7e2dc !important;
        padding: 10px 28px !important;
        box-shadow: 0 -4px 14px rgba(24,20,15,.03) !important;
        backdrop-filter: none !important;
    }

    #sellerAddProductModal #cancelAddProductModal,
    #sellerAddProductModal #sellerPreviewProduct,
    #sellerAddProductModal #sellerSubmitProduct {
        height: 38px !important;
        border-radius: 9px !important;
        font-size: 8px !important;
        font-weight: 700 !important;
    }

    #sellerAddProductModal #sellerPreviewProduct {
        background: #fff !important;
    }

    #sellerAddProductModal #sellerSubmitProduct {
        box-shadow: none !important;
    }

    @media (max-width: 640px) {
        #sellerAddProductModal .seller-add-form-body {
            padding: 0 16px 88px !important;
        }

        #sellerAddProductModal .sticky.bottom-0 {
            padding: 10px 16px !important;
        }
    }


    /* ============================================================
       ADD PRODUCT PROGRESS UI
    ============================================================ */
    #sellerAddProductModal .seller-add-progress {
        max-width: 794px;
        margin: 0 auto;
        padding: 18px 0 16px;
        border-bottom: 1px solid #ece7e1;
        background: #fff;
    }

    #sellerAddProductModal .seller-add-progress-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    #sellerAddProductModal .seller-add-progress-label {
        flex: 0 0 auto;
        border: 1px solid #e6dfd7;
        border-radius: 999px;
        background: #fff;
        padding: 5px 9px;
        color: #786f65;
        font-size: 7px;
        font-weight: 700;
    }

    #sellerAddProductModal .seller-add-progress-track-wrap {
        position: relative;
        margin-top: 15px;
    }

    #sellerAddProductModal .seller-add-progress-track {
        position: absolute;
        top: 13px;
        left: calc(12.5% + 2px);
        right: calc(12.5% + 2px);
        height: 2px;
        overflow: hidden;
        border-radius: 999px;
        background: #eee9e3;
    }

    #sellerAddProductModal .seller-add-progress-fill {
        display: block;
        width: 0;
        height: 100%;
        border-radius: inherit;
        background: #cf8d10;
        transition: width .22s ease;
    }

    #sellerAddProductModal .seller-add-progress-steps {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 4px;
    }

    #sellerAddProductModal .seller-add-progress-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        min-width: 0;
        border: 0;
        background: transparent;
        padding: 0 4px;
        text-align: center;
        cursor: pointer;
    }

    #sellerAddProductModal .seller-add-progress-circle {
        display: grid;
        width: 28px;
        height: 28px;
        place-items: center;
        border: 1px solid #ded8d1;
        border-radius: 999px;
        background: #fff;
        color: #928980;
        font-size: 7.5px;
        font-weight: 800;
        transition: border-color .16s ease, background-color .16s ease, color .16s ease;
    }

    #sellerAddProductModal .seller-add-progress-copy {
        display: block;
        min-width: 0;
    }

    #sellerAddProductModal .seller-add-progress-copy strong {
        display: block;
        color: #756d64;
        font-size: 7.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    #sellerAddProductModal .seller-add-progress-copy small {
        display: block;
        margin-top: 2px;
        color: #a19a92;
        font-size: 6.3px;
        font-weight: 500;
        white-space: nowrap;
    }

    #sellerAddProductModal .seller-add-progress-step.is-active .seller-add-progress-circle,
    #sellerAddProductModal .seller-add-progress-step.is-complete .seller-add-progress-circle {
        border-color: #cf8d10;
        background: #cf8d10;
        color: #fff;
    }

    #sellerAddProductModal .seller-add-progress-step.is-active .seller-add-progress-copy strong,
    #sellerAddProductModal .seller-add-progress-step.is-complete .seller-add-progress-copy strong {
        color: #4d4439;
    }

    #sellerAddProductModal #sellerGalleryPreview {
        scrollbar-width: thin;
        scrollbar-color: #ddd6ce transparent;
    }

    @media (max-width: 640px) {
        #sellerAddProductModal .seller-add-progress-copy small {
            display: none;
        }

        #sellerAddProductModal .seller-add-progress-copy strong {
            font-size: 6.8px;
        }

        #sellerAddProductModal .seller-add-progress-track {
            left: 12.5%;
            right: 12.5%;
        }
    }

</style>

<div class="seller-products-page mx-auto w-full max-w-[1800px]">

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
            <div class="flex min-w-0 items-start gap-3.5">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-[14px] border border-[#ecdcb9] bg-[#fff8e9] text-[#bf7d0d] shadow-[0_7px_18px_rgba(178,119,14,.06)] sm:h-[52px] sm:w-[52px]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M5 7h14l-1 13H6L5 7Z"></path>
                        <path d="M9 7a3 3 0 0 1 6 0"></path>
                    </svg>
                </span>

                <div class="min-w-0 pt-0.5">
                    <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#b77912] sm:text-[8.5px]">Seller Catalog</p>
                    <h1 class="mt-1 text-[25px] font-bold tracking-[-0.04em] sm:text-[30px]"><span class="text-[#201c18]">Your</span> <span class="text-[#c89216]">Products</span></h1>
                    <p class="mt-1 max-w-[720px] text-[10px] leading-5 text-[#81786e] sm:text-[11px]">
                        Manage and organize your products. Add new items, update details, and keep your catalog up to date.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- SELLER CATALOG STUDIO — LEFT CONTROL RAIL + RIGHT PRODUCT WORKSPACE --}}
    <section class="seller-catalog-studio mt-6 grid grid-cols-1 gap-5 xl:grid-cols-[310px_minmax(0,1fr)]">

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

                {{-- PRIMARY ACTIONS --}}
                <div class="mt-4 grid grid-cols-2 gap-2.5">
                    <button
                        id="openAddProductModal"
                        type="button"
                        class="seller-rail-primary-action inline-flex h-11 items-center justify-center gap-2 rounded-[11px] bg-[#c98d19] px-3 text-[9px] font-semibold text-white"
                    >
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M12 5v14"></path><path d="M5 12h14"></path>
                        </svg>
                        Add Product
                    </button>

                    <a
                        href="{{ route('seller.products.archive') }}"
                        class="seller-rail-secondary-action inline-flex h-11 items-center justify-center gap-2 rounded-[11px] border border-[#ddd4c8] bg-white px-3 text-[9px] font-semibold text-[#5f564c]"
                        wire:navigate
                    >
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h16"></path><path d="M6 7v12h12V7"></path><path d="M9 11h6"></path>
                        </svg>
                        Archive
                    </a>
                </div>

                {{-- INVENTORY METRICS --}}
                <div class="mt-5">
                    <div class="mb-2.5 flex items-center justify-between gap-3">
                        <h3 class="text-[10.5px] font-semibold text-[#494038]">Inventory Overview</h3>
                        <span class="text-[8px] text-[#a0978d]">Live catalog</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        @foreach ([
                            ['id' => 'productsTotalCount', 'label' => 'Active', 'tone' => 'text-[#b9780c]', 'bg' => 'bg-[#fff5df]', 'type' => 'active'],
                            ['id' => 'productsApprovedCount', 'label' => 'Approved', 'tone' => 'text-[#3f865e]', 'bg' => 'bg-[#edf8f1]', 'type' => 'approved'],
                            ['id' => 'productsLowStockCount', 'label' => 'Low Stock', 'tone' => 'text-[#b87912]', 'bg' => 'bg-[#fff7e8]', 'type' => 'low'],
                            ['id' => 'productsOutStockCount', 'label' => 'Out', 'tone' => 'text-[#b85b5b]', 'bg' => 'bg-[#fff0f0]', 'type' => 'out'],
                        ] as $item)
                            <div class="seller-rail-stat rounded-[14px] border border-[#e9e1d7] bg-white p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="grid h-8 w-8 place-items-center rounded-[9px] {{ $item['bg'] }} {{ $item['tone'] }}">
                                        @switch($item['type'])
                                            @case('approved')
                                                <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg>
                                                @break
                                            @case('low')
                                                <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 8 12 4l8 4-8 4-8-4Z"></path><path d="M5 11v6l7 3 7-3v-6"></path></svg>
                                                @break
                                            @case('out')
                                                <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"></circle><path d="m9 9 6 6"></path><path d="m15 9-6 6"></path></svg>
                                                @break
                                            @default
                                                <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 7h14l-1 13H6L5 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path></svg>
                                        @endswitch
                                    </span>
                                    <span id="{{ $item['id'] }}" class="text-[21px] font-bold leading-none tracking-[-0.05em] text-[#241f1a]">—</span>
                                </div>
                                <p class="mt-2.5 text-[8.5px] font-semibold text-[#71685f]">{{ $item['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- CATALOG HEALTH --}}
                <div class="seller-rail-health mt-4 rounded-[16px] border border-[#e7dccb] bg-white p-4">
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

                    <div class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 border-t border-[#f0ebe3] pt-3 text-[8px] text-[#81786e]">
                        <span><strong id="productsHealthApproved" class="font-bold text-[#3f865e]">0</strong> approved</span>
                        <span><strong id="productsHealthReview" class="font-bold text-[#537a9f]">0</strong> review</span>
                        <span><strong id="productsHealthLow" class="font-bold text-[#b87912]">0</strong> low stock</span>
                        <span><strong id="productsHealthOut" class="font-bold text-[#b85b5b]">0</strong> out</span>
                    </div>
                </div>

                {{-- QUICK VIEWS --}}
                <div class="mt-4">
                    <div class="mb-2.5 flex items-center justify-between gap-3">
                        <h3 class="text-[10.5px] font-semibold text-[#494038]">Quick Views</h3>
                        <span class="text-[8px] text-[#a0978d]">Filter catalog</span>
                    </div>

                    <div class="grid gap-2">
                        <button type="button" class="seller-products-quick-filter is-active" data-products-quick-filter="all">
                            <span class="flex items-center gap-2">
                                <span class="seller-quick-dot bg-[#c98d19]"></span>
                                All Products
                            </span>
                            <span id="productsQuickAll" class="seller-products-quick-count">0</span>
                        </button>

                        <button type="button" class="seller-products-quick-filter" data-products-quick-filter="approved">
                            <span class="flex items-center gap-2">
                                <span class="seller-quick-dot bg-[#4f8065]"></span>
                                Approved
                            </span>
                            <span id="productsQuickApproved" class="seller-products-quick-count">0</span>
                        </button>

                        <button type="button" class="seller-products-quick-filter" data-products-quick-filter="pending">
                            <span class="flex items-center gap-2">
                                <span class="seller-quick-dot bg-[#537a9f]"></span>
                                Under Review
                            </span>
                            <span id="productsQuickPending" class="seller-products-quick-count">0</span>
                        </button>

                        <button type="button" class="seller-products-quick-filter" data-products-quick-filter="low">
                            <span class="flex items-center gap-2">
                                <span class="seller-quick-dot bg-[#c48a13]"></span>
                                Low Stock
                            </span>
                            <span id="productsQuickLow" class="seller-products-quick-count">0</span>
                        </button>

                        <button type="button" class="seller-products-quick-filter" data-products-quick-filter="out">
                            <span class="flex items-center gap-2">
                                <span class="seller-quick-dot bg-[#b65e5e]"></span>
                                Out of Stock
                            </span>
                            <span id="productsQuickOut" class="seller-products-quick-count">0</span>
                        </button>
                    </div>
                </div>
            </div>
        </aside>

        {{-- RIGHT: PRODUCT WORKSPACE --}}
        <div class="min-w-0">
            <div class="seller-products-workspace rounded-[20px] border border-[#e8e0d5] bg-white p-4 sm:p-5">
                <div class="flex flex-col gap-3 border-b border-[#f0ebe4] pb-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-[8px] font-bold uppercase tracking-[.13em] text-[#b77912]">Product Library</p>
                        <h2 class="mt-1 text-[20px] font-bold tracking-[-0.035em] text-[#25201b] sm:text-[22px]">
                            Manage your <span class="text-[#c89216]">catalog</span>
                        </h2>
                        <p id="productsResultCount" class="mt-1.5 text-[9px] font-medium text-[#8e857b]">Loading products...</p>
                    </div>

                    <p class="text-[8px] text-[#a0978d]">Active listings only · archived products are stored separately</p>
                </div>

                {{-- FILTERS --}}
                <div class="mt-4 rounded-[14px] border border-[#ebe3d9] bg-[#fbfaf7] p-3">
            <div class="grid grid-cols-1 gap-2.5 lg:grid-cols-[minmax(240px,1.4fr)_150px_150px_160px_auto]">
                <div class="relative">
                    <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8f877e]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path>
                    </svg>
                    <input id="productsSearch" type="search" placeholder="Search product name, category, brand or SKU..." class="seller-products-filter h-11 w-full rounded-[10px] border border-[#dfd7cc] bg-[#fffefa] pl-10 pr-3 text-[9.5px] text-[#403930] outline-none placeholder:text-[#a59d93]">
                </div>

                <select id="productsStatus" class="seller-products-filter h-11 rounded-[10px] border border-[#dfd7cc] bg-[#fffefa] px-3 text-[9px] font-medium text-[#655d53] outline-none">
                    <option value="">All Status</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending Review</option>
                    <option value="flagged">Flagged</option>
                    <option value="rejected">Rejected</option>
                    <option value="removed">Removed</option>
                </select>

                <select id="productsStock" class="seller-products-filter h-11 rounded-[10px] border border-[#dfd7cc] bg-[#fffefa] px-3 text-[9px] font-medium text-[#655d53] outline-none">
                    <option value="">All Stock</option>
                    <option value="in-stock">In Stock</option>
                    <option value="low-stock">Low Stock</option>
                    <option value="out-of-stock">Out of Stock</option>
                </select>

                <select id="productsCategory" class="seller-products-filter h-11 rounded-[10px] border border-[#dfd7cc] bg-[#fffefa] px-3 text-[9px] font-medium text-[#655d53] outline-none">
                    <option value="">All Categories</option>
                </select>

                <button id="productsClearFilters" type="button" class="h-11 rounded-[10px] border border-[#ded5c8] bg-white px-4 text-[9px] font-semibold text-[#746b61] transition hover:border-[#cfba8d] hover:bg-[#fffaf2] hover:text-[#9a6817]">
                    Clear Filters
                </button>
            </div>
        </div>
<div id="productsGrid" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-3">
            @for ($i = 0; $i < 7; $i++)
                <div class="overflow-hidden rounded-[16px] border border-[#ebe4da] bg-white shadow-[0_7px_20px_rgba(43,33,21,.03)]">
                    <div class="h-[205px] animate-pulse bg-[#eeeae4]"></div>
                    <div class="p-4.5">
                        <div class="h-3.5 w-2/3 animate-pulse rounded-full bg-[#eeeae4]"></div>
                        <div class="mt-2.5 h-3 w-1/2 animate-pulse rounded-full bg-[#eeeae4]"></div>
                        <div class="mt-4 h-6 w-1/3 animate-pulse rounded-lg bg-[#eeeae4]"></div>
                        <div class="mt-5 grid grid-cols-4 gap-2.5">
                            @for ($j = 0; $j < 4; $j++)
                                <div class="h-10 animate-pulse rounded-[10px] bg-[#eeeae4]"></div>
                            @endfor
                        </div>
                    </div>
                </div>
            @endfor
        </div>

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

<div id="sellerAddProductModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/40 p-3 backdrop-blur-[3px] sm:p-5">
    <div class="seller-add-modal-shell flex max-h-[96vh] w-full max-w-[1320px] flex-col overflow-hidden rounded-[24px] border border-[#e7dfd5] bg-white shadow-[0_28px_90px_rgba(24,20,15,.16)]">

        {{-- HEADER --}}
        <div class="relative shrink-0 overflow-hidden border-b border-[#ece7e1] bg-white px-5 py-3.5 sm:px-6 sm:py-4">
            <div class="relative flex items-start justify-between gap-5">
                <div class="flex min-w-0 items-start gap-4">
                    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl border border-[#e5e7eb] bg-white text-[#111827]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M5 7h14l-1 13H6L5 7Z"></path>
                            <path d="M9 7a3 3 0 0 1 6 0"></path>
                            <path d="M12 11v5"></path>
                            <path d="M9.5 13.5h5"></path>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#b77912]">New Listing</p>

                        <h3 class="mt-1 text-[21px] font-bold tracking-[-.035em] text-[#211c16] sm:text-[24px]">
                            Add Product
                        </h3>
                        <p class="mt-1.5 max-w-[680px] text-[9px] leading-5 text-[#81786c] sm:text-[10px]">
                            Complete the product information below. Keep the details clear and accurate before submitting for review.
                        </p>
                    </div>
                </div>

                <button id="closeAddProductModal" type="button" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#e5e7eb] bg-white text-[#6b7280] transition hover:border-[#9ca3af] hover:text-[#a8731f]" aria-label="Close add product">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="m7 7 10 10"></path><path d="m17 7-10 10"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div id="sellerAddProductNotice" class="mx-5 mt-4 hidden rounded-[12px] border px-4 py-3 text-[9px] font-semibold sm:mx-7"></div>

        <form id="sellerAddProductForm" method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="min-h-0 flex-1 overflow-y-auto">
            @csrf
            <input id="sellerProductDraftId" type="hidden" name="draft_id" value="">
            <input id="sellerHasVariants" type="hidden" name="has_variants" value="0">

            <div class="seller-add-form-body space-y-4 bg-[#fbfaf8] p-4 pb-24 sm:p-6 sm:pb-28">

                
                {{-- ADD PRODUCT PROGRESS --}}
                <div class="seller-add-progress" aria-label="Add product progress">
                    <div class="seller-add-progress-head">
                        <div>
                            <p class="text-[10px] font-bold text-[#302a24]">Add Product</p>
                            <p class="mt-0.5 text-[7.5px] text-[#81786c]">
                                Complete the form from left to right. Required fields are marked with *.
                            </p>
                        </div>

                        <span id="sellerAddProgressLabel" class="seller-add-progress-label">Step 1 of 4</span>
                    </div>

                    <div class="seller-add-progress-track-wrap">
                        <div class="seller-add-progress-track" aria-hidden="true">
                            <span id="sellerAddProgressFill" class="seller-add-progress-fill"></span>
                        </div>

                        <div class="seller-add-progress-steps">
                            <button type="button" data-add-progress-step="1" class="seller-add-progress-step is-active">
                                <span class="seller-add-progress-circle">1</span>
                                <span class="seller-add-progress-copy">
                                    <strong>Product info</strong>
                                    <small>Name & category</small>
                                </span>
                            </button>

                            <button type="button" data-add-progress-step="2" class="seller-add-progress-step">
                                <span class="seller-add-progress-circle">2</span>
                                <span class="seller-add-progress-copy">
                                    <strong>Photos</strong>
                                    <small>Images & description</small>
                                </span>
                            </button>

                            <button type="button" data-add-progress-step="3" class="seller-add-progress-step">
                                <span class="seller-add-progress-circle">3</span>
                                <span class="seller-add-progress-copy">
                                    <strong>Price & stock</strong>
                                    <small>Variants if needed</small>
                                </span>
                            </button>

                            <button type="button" data-add-progress-step="4" class="seller-add-progress-step">
                                <span class="seller-add-progress-circle">4</span>
                                <span class="seller-add-progress-copy">
                                    <strong>Shipping</strong>
                                    <small>Extra details</small>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">

{{-- BASIC INFORMATION --}}
                <section id="sellerAddBasics" class="seller-add-flow-section rounded-[18px] border border-[#e7dfd5] bg-white p-4 sm:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2.5"><span class="seller-add-section-number">1</span><p class="text-[12px] font-bold tracking-[-.02em] text-[#302a24]">Product information</p></div>
                            <p class="mt-1 text-[8px] text-[#91887d]">Start with the details buyers need to identify your product.</p>
                        </div>
                        
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-[9px] font-semibold text-[#514a41]">Product Name <span class="text-[#b95d55]">*</span></label>
                            <input id="sellerNewProductName" name="name" type="text" required value="{{ old('name') }}" placeholder="Example: Nordic Solid Wood Dining Chair" class="h-11 w-full rounded-xl border border-[#d1d5db] bg-white px-4 text-[10px] text-[#332e28] outline-none transition placeholder:text-[#aaa196] focus:border-[#d48f08] focus:bg-white focus:ring-4 focus:ring-[#d48f08]/10">
                        </div>

                        <div>
                            <label class="mb-2 block text-[9px] font-semibold text-[#514a41]">Category <span class="text-[#b95d55]">*</span></label>
                            <div id="sellerCategoryDropdown" class="relative">
                                <input id="sellerNewProductCategory" name="category" type="hidden" value="{{ old('category') }}">

                                <button id="sellerCategoryDropdownButton" type="button" class="flex h-11 w-full items-center gap-3 rounded-xl border border-[#d1d5db] bg-white px-3.5 text-left transition hover:border-[#9ca3af] focus:border-[#d48f08] focus:outline-none focus:ring-4 focus:ring-[#d48f08]/10" aria-haspopup="listbox" aria-expanded="false">
                                    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-lg border border-[#e5e7eb] bg-white text-[#4b5563]">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h6v6H4z"></path><path d="M14 6h6v6h-6z"></path><path d="M4 16h6v4H4z"></path><path d="M14 16h6v4h-6z"></path></svg>
                                    </span>
                                    <span id="sellerCategoryDropdownLabel" class="min-w-0 flex-1 truncate text-[10px] font-medium text-[#8f877c]">Choose product category</span>
                                    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-lg border border-[#e5e7eb] bg-white text-[#4b5563]">
                                        <svg id="sellerCategoryChevron" viewBox="0 0 24 24" class="h-3.5 w-3.5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 10 5 5 5-5"></path></svg>
                                    </span>
                                </button>

                                <div id="sellerCategoryDropdownPanel" class="absolute left-0 right-0 top-[calc(100%+8px)] z-[40] hidden overflow-hidden rounded-[16px] border border-[#e5e7eb] bg-white shadow-[0_20px_55px_rgba(15,23,42,.14)]">
                                    <div class="border-b border-[#e5e7eb] p-3">
                                        <div class="relative">
                                            <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#9a9185]" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="6"></circle><path d="m16 16 4 4"></path></svg>
                                            <input id="sellerCategorySearch" type="search" autocomplete="off" placeholder="Search categories..." class="h-9 w-full rounded-xl border border-[#d1d5db] bg-white pl-9 pr-3 text-[8px] text-[#403930] outline-none placeholder:text-[#aaa196] focus:border-[#d48f08] focus:bg-white">
                                        </div>
                                    </div>

                                    <div id="sellerCategoryOptions" role="listbox" class="max-h-[280px] overflow-y-auto p-2">
                                        @php
                                            $sellerProductCategories = [
                                                ['Electronics', 'Phones, gadgets, computers'],
                                                ['Fashion & Apparel', 'Clothing, bags, accessories'],
                                                ['Shoes & Footwear', 'Sneakers, sandals, formal shoes'],
                                                ['Home & Living', 'Decor, kitchen, household'],
                                                ['Furniture & Office', 'Chairs, tables, office furniture'],
                                                ['Beauty & Personal Care', 'Cosmetics, skincare, grooming'],
                                                ['Books & Stationery', 'Books, notebooks, school supplies'],
                                                ['Food & Gourmet', 'Food, snacks, specialty goods'],
                                                ['Jewelry & Watches', 'Jewelry, watches, accessories'],
                                                ['Sports & Outdoors', 'Sports gear, camping, fitness'],
                                                ['Automotive & Motorcycle', 'Parts, tools, accessories'],
                                                ['Baby & Kids', 'Baby care, kids products'],
                                                ['Pet Supplies', 'Pet food, care, accessories'],
                                                ['Health & Wellness', 'Wellness and personal-use goods'],
                                                ['Toys & Collectibles', 'Toys, figures, collectibles'],
                                                ['Appliances', 'Home and kitchen appliances'],
                                                ['Others', 'Custom or uncategorized product'],
                                            ];
                                        @endphp

                                        @foreach($sellerProductCategories as [$categoryName, $categoryDescription])
                                            <button type="button" role="option" data-category-option="{{ $categoryName }}" data-category-search="{{ strtolower($categoryName . ' ' . $categoryDescription) }}" class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left transition hover:bg-[#f9fafb]">
                                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-xl border border-[#e5e7eb] bg-white text-[#6b7280] transition group-hover:border-[#9ca3af] group-hover:text-[#a8731f]">
                                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 7h14l-1 13H6L5 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path></svg>
                                                </span>
                                                <span class="min-w-0 flex-1">
                                                    <span class="block text-[8px] font-bold text-[#403930]">{{ $categoryName }}</span>
                                                    <span class="mt-0.5 block truncate text-[7px] text-[#9a9185]">{{ $categoryDescription }}</span>
                                                </span>
                                                <span data-category-check class="hidden h-5 w-5 shrink-0 place-items-center rounded-full bg-[#d48f08] text-white">
                                                    <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m6 12 4 4 8-8"></path></svg>
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>

                                    <div id="sellerCategoryEmpty" class="hidden border-t border-[#eee7de] px-4 py-5 text-center">
                                        <p class="text-[8px] font-semibold text-[#6d645a]">No matching category</p>
                                        <p class="mt-1 text-[9px] text-[#9b9287]">Try another keyword or choose Others.</p>
                                    </div>
                                </div>
                            </div>
                            <p id="sellerCategoryHint" class="mt-1.5 min-h-[16px] text-[7px] leading-4 text-[#9a9185]">Select a category to get suggested specifications.</p>
                            <div id="sellerCustomCategoryWrap" class="mt-2 hidden rounded-xl border border-[#e5e7eb] bg-white p-3">
                                <label class="mb-1.5 block text-[7px] font-bold uppercase tracking-[.07em] text-[#9a762f]">Custom Category Name</label>
                                <input id="sellerCustomCategory" name="custom_category" type="text" value="{{ old('custom_category') }}" maxlength="100" placeholder="Example: Musical Instruments" class="h-9 w-full rounded-lg border border-[#e5d8c1] bg-white px-3 text-[8px] text-[#403930] outline-none placeholder:text-[#aaa196] focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10">
                                <p class="mt-1.5 text-[7px] leading-4 text-[#9a8d78]">Use this when your product does not fit the suggested marketplace categories.</p>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-[9px] font-semibold text-[#514a41]">Brand</label>
                            <div class="relative">
                                <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#9d8a69]" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 13 13 20 4 11V4h7l9 9Z"></path><circle cx="8.5" cy="8.5" r="1"></circle></svg>
                                <input id="sellerNewProductBrand" name="brand" type="text" value="{{ old('brand') }}" placeholder="Brand name or No Brand" class="h-11 w-full rounded-xl border border-[#d1d5db] bg-white pl-10 pr-4 text-[10px] text-[#332e28] outline-none transition placeholder:text-[#aaa196] focus:border-[#d48f08] focus:bg-white focus:ring-4 focus:ring-[#d48f08]/10">
                            </div>
                        </div>


                        <details class="seller-add-inline-optional md:col-span-2">
                            <summary class="seller-add-inline-summary">
                                <span>
                                    <span class="block text-[9px] font-semibold text-[#51483f]">More listing information</span>
                                    <span class="mt-0.5 block text-[7.5px] text-[#958c80]">Optional SKU and voucher code.</span>
                                </span>
                                <svg viewBox="0 0 24 24" class="seller-add-details-chevron h-4 w-4 shrink-0 text-[#8f7d63]" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m7 10 5 5 5-5"></path>
                                </svg>
                            </summary>
                            <div class="grid grid-cols-1 gap-4 border-t border-[#eee8df] px-4 py-4 md:grid-cols-2">
                                <div>
                            <label class="mb-2 block text-[9px] font-semibold text-[#514a41]">SKU / Product Code</label>
                            <input id="sellerNewProductSku" name="sku" type="text" value="{{ old('sku') }}" placeholder="Optional — auto-generated if empty" class="h-11 w-full rounded-xl border border-[#d1d5db] bg-white px-4 text-[10px] text-[#332e28] outline-none transition placeholder:text-[#aaa196] focus:border-[#d48f08] focus:bg-white focus:ring-4 focus:ring-[#d48f08]/10">
                        </div>

                        <div>
                            <label class="mb-2 block text-[9px] font-semibold text-[#514a41]">Voucher Code</label>
                            <input name="voucher_code" type="text" value="{{ old('voucher_code') }}" placeholder="Optional" class="h-11 w-full rounded-xl border border-[#d1d5db] bg-white px-4 text-[10px] text-[#332e28] outline-none transition placeholder:text-[#aaa196] focus:border-[#d48f08] focus:bg-white focus:ring-4 focus:ring-[#d48f08]/10">
                        </div>
                            </div>
                        </details>
                    </div>
                </section>

{{-- MEDIA + DESCRIPTION --}}
                <section id="sellerAddMedia" class="seller-add-flow-section rounded-[18px] border border-[#e7dfd5] bg-white p-4 sm:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2.5"><span class="seller-add-section-number">2</span><p class="text-[12px] font-bold tracking-[-.02em] text-[#302a24]">Photos & description</p></div>
                            <p class="mt-1 text-[8px] text-[#91887d]">Use a strong cover image, add gallery photos, and write an accurate description.</p>
                        </div>
                        
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-[280px_1fr]">
                        <div>
                            <label class="mb-2 block text-[9px] font-semibold text-[#514a41]">Cover Image</label>
                            <label class="group flex min-h-[158px] cursor-pointer flex-col items-center justify-center rounded-[17px] border border-dashed border-[#d1d5db] bg-white p-4 text-center transition hover:border-[#9ca3af] hover:bg-white">
                                <input id="sellerNewProductImage" name="image" type="file" accept="image/*" class="hidden">
                                <div id="sellerImagePreviewBox" class="grid h-16 w-16 place-items-center overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white text-[#4b5563]">
                                    <svg id="sellerImagePlaceholder" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="8" cy="9" r="1.5"></circle><path d="m4 17 5-5 4 4 2-2 5 4"></path></svg>
                                    <img id="sellerImagePreview" src="" alt="" class="hidden h-full w-full object-cover">
                                </div>
                                <p class="mt-3 text-[9px] font-bold text-[#51483e]">Choose cover image</p>
                                <p class="mt-1 text-[7px] leading-4 text-[#9a9185]">PNG, JPG or WEBP · up to 4 MB</p>
                                <p class="mt-1.5 max-w-[220px] text-[6.8px] leading-4 text-[#a39a8f]">
                                    This is the primary image buyers see first.
                                </p>
                            </label>
                        </div>

                        <div>
                            <label class="mb-2 block text-[9px] font-semibold text-[#514a41]">Product Description</label>
                            <textarea name="description" rows="7" placeholder="Describe the product clearly: features, materials, measurements, condition, package inclusions, warranty, or other important information..." class="min-h-[158px] w-full resize-none rounded-[17px] border border-[#d1d5db] bg-white px-4 py-3 text-[10px] leading-5 text-[#332e28] outline-none transition placeholder:text-[#aaa196] focus:border-[#d48f08] focus:bg-white focus:ring-4 focus:ring-[#d48f08]/10">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 border-t border-[#eee8df] pt-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-start justify-between gap-3">
                                <div>
                                    <p class="text-[9px] font-bold text-[#51483f]">Product Gallery</p>
                                    <p class="mt-1 text-[7px] text-[#958c80]">Add multiple extra images for different angles, packaging, colors, variants, or product details.</p>
                                </div>
                                <span id="sellerGalleryCount" class="shrink-0 rounded-full border border-[#e5dfd8] bg-white px-2.5 py-1 text-[7px] font-semibold text-[#756c63]">0 images</span>
                            </div>

                            <label class="inline-flex h-9 cursor-pointer items-center justify-center gap-2 rounded-xl border border-[#d9cfbd] bg-[#fffaf1] px-3 text-[8px] font-bold text-[#946313] transition hover:bg-[#fff5dd]">
                                <input id="sellerGalleryImages" name="gallery_images[]" type="file" accept="image/*" multiple class="hidden">
                                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>
                                Add Gallery Images
                            </label>
                        </div>

                        <div id="sellerGalleryPreview" class="mt-3 grid max-h-[300px] grid-cols-2 gap-2.5 overflow-y-auto pr-1 sm:grid-cols-3 lg:grid-cols-5">
                            <div id="sellerGalleryEmpty" class="col-span-full rounded-[13px] border border-dashed border-[#ddd5ca] bg-[#fcfbf8] px-4 py-4 text-center text-[7.5px] text-[#9a9187]">
                                No gallery images selected. You can add multiple images.
                            </div>
                        </div>
                    </div>
                </section>

{{-- VARIANT MODE — hidden compatibility controls --}}
                <div class="hidden" aria-hidden="true">
                    <label><input type="radio" name="product_type" value="simple" checked> Simple</label>
                    <label><input type="radio" name="product_type" value="variant"> Variant</label>
                </div>

{{-- SINGLE ITEM PRICE + INVENTORY --}}
                <section id="sellerSimpleInventorySection" class="seller-add-subsection rounded-[15px] border border-[#e8e0d6] bg-white p-4 sm:p-5">
                    <div class="flex items-start gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-[10px] border border-[#ead9b7] bg-[#fff8ea] text-[#b77a13]">
                            <span class="text-[12px] font-bold">₱</span>
                        </span>

                        <div>
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-[11px] font-bold text-[#302a24]">Price & stock</p>
                                    <p class="mt-1 text-[7.5px] text-[#91887d]">Use one price and stock, or add variants when the product has different options.</p>
                                </div>

                                <button
                                    id="sellerEnableVariants"
                                    type="button"
                                    class="inline-flex h-8 items-center justify-center gap-1.5 rounded-lg border border-[#dfd6c9] bg-white px-3 text-[7.5px] font-bold text-[#966617] transition hover:border-[#d2b16d] hover:bg-[#fffaf1]"
                                >
                                    <span class="text-[12px] leading-none">+</span>
                                    Add variants
                                </button>
                            </div>
                            <p class="mt-1 text-[8px] leading-4 text-[#91887d]">
                                
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-[9px] font-semibold text-[#514a41]">Price <span class="text-[#b95d55]">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-[#a8731f]">₱</span>
                                <input id="sellerNewProductPrice" name="price" type="number" min="0" step="0.01" required placeholder="0.00" class="h-11 w-full rounded-xl border border-[#d9d2c9] bg-white pl-8 pr-4 text-[10px] text-[#332e28] outline-none focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10">
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-[9px] font-semibold text-[#514a41]">Stock Quantity <span class="text-[#b95d55]">*</span></label>
                            <input id="sellerNewProductStock" name="stock" type="number" min="0" required placeholder="0" class="h-11 w-full rounded-xl border border-[#d9d2c9] bg-white px-4 text-[10px] text-[#332e28] outline-none focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10">
                        </div>
                    </div>
                </section>

{{-- PRODUCT VARIATIONS --}}
                <section id="sellerVariantSection" class="hidden rounded-[18px] border border-[#e7dfd5] bg-white p-4 sm:p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-[11px] font-bold text-[#302a24]">Product variants</p>
                            <p class="mt-1 text-[8px] leading-4 text-[#91887d]">
                                Add one card for each option buyers can purchase.
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                id="sellerUseSinglePrice"
                                type="button"
                                class="inline-flex h-9 items-center justify-center rounded-xl border border-[#ded8d1] bg-white px-3 text-[7.5px] font-bold text-[#6f665c] transition hover:bg-[#faf8f5]"
                            >
                                Use single price
                            </button>

                            <button
                                id="sellerAddManualVariant"
                                type="button"
                                class="inline-flex h-9 items-center justify-center gap-2 rounded-xl bg-[#d48f08] px-4 text-[8px] font-bold text-white transition hover:bg-[#bd7d05]"
                            >
                                <span class="text-[14px] leading-none">+</span>
                                Add Variant
                            </button>
                        </div>
                    </div>

                    <div id="sellerManualVariantEmpty" class="mt-4 rounded-[14px] border border-dashed border-[#d8d0c5] bg-[#fcfbf8] px-4 py-6 text-center">
                        <p class="text-[8.5px] font-semibold text-[#665d53]">No variants yet</p>
                        <p class="mt-1 text-[7px] text-[#9a9187]">
                            Click Add Variant, then enter image, color/design/scent, size, price, and stock.
                        </p>
                    </div>

                    <div id="sellerVariantTableWrap" class="mt-4 hidden">
                        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                            <p id="sellerVariantSummary" class="text-[7.5px] font-medium text-[#8d857b]">
                                Complete each variant below.
                            </p>

                            <button
                                id="sellerApplyVariantDefaults"
                                type="button"
                                class="rounded-lg border border-[#e1d6c4] bg-white px-3 py-2 text-[7px] font-bold text-[#82632d] transition hover:bg-[#fffaf2]"
                            >
                                Copy first price & stock
                            </button>
                        </div>

                        <div id="sellerVariantRows" class="space-y-3"></div>
                    </div>

                    {{-- Compatibility hooks retained for the existing JS/backend. --}}
                    <button id="sellerGenerateVariants" type="button" class="hidden">Generate</button>
                    <div id="sellerVariantOptions" class="hidden"></div>
                    <input id="sellerColorValueInput" type="hidden">
                    <input id="sellerSizeValueInput" type="hidden">
                    <div id="sellerColorChips" class="hidden"></div>
                    <div id="sellerSizeChips" class="hidden"></div>
                    <p id="sellerColorEmpty" class="hidden"></p>
                    <p id="sellerSizeEmpty" class="hidden"></p>
                    <div id="sellerColorStepCard" class="hidden"></div>
                    <div id="sellerSizeStepCard" class="hidden"></div>
                    <span id="sellerSizeStepNumber" class="hidden"></span>
                    <p id="sellerSizeGuide" class="hidden"></p>
                    <p id="sellerQuickVariantStatus" class="hidden"></p>
                    <button id="sellerAddColorValue" type="button" class="hidden"></button>
                    <button id="sellerAddSizeValue" type="button" class="hidden"></button>
                    <button id="sellerToggleAdvancedVariants" type="button" class="hidden"></button>
                    <div id="sellerAdvancedVariantWrap" class="hidden"></div>
                    <svg id="sellerAdvancedVariantChevron" class="hidden"></svg>
                    <button id="sellerAddVariantOption" type="button" class="hidden"></button>
                </section>

                <details class="seller-add-optional-card">
                    <summary class="seller-add-optional-summary">
                        <span class="flex min-w-0 items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-[10px] border border-[#ead9b7] bg-[#fff8ea] text-[#aa7414]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M5 7h14v10H5z"></path><path d="m8 14 8-4"></path><circle cx="9" cy="10" r="1"></circle><circle cx="15" cy="14" r="1"></circle>
                                </svg>
                            </span>
                            <span>
                                <span class="block text-[10px] font-bold text-[#403930]">Promotion options</span>
                                <span class="mt-0.5 block text-[7.5px] text-[#958c80]">Optional discount and free shipping settings.</span>
                            </span>
                        </span>
                        <svg viewBox="0 0 24 24" class="seller-add-details-chevron h-4 w-4 shrink-0 text-[#8f7d63]" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </summary>
                    <div class="seller-add-optional-body">
{{-- PROMOTION + SHIPPING --}}
                <section class="border-0 bg-transparent p-0">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-[10px] font-semibold text-[#302a24]">Discount & free shipping</p>
                            <p class="mt-1 text-[8px] leading-4 text-[#91887d]">
                                Only use these settings when the listing has a discount or free shipping.
                            </p>
                        </div>
                        <span class="rounded-full border border-[#f0dfaa] bg-[#fff9e6] px-2.5 py-1 text-[7px] font-medium text-[#a9770d]">
                            Buyer-facing
                        </span>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3 lg:grid-cols-[220px_1fr]">
                        <div class="rounded-[14px] border border-[#e6dfd5] bg-white p-3.5">
                            <label for="sellerNewProductDiscount" class="block text-[8px] font-medium text-[#5d554c]">
                                Discount %
                            </label>

                            <div class="relative mt-2">
                                <input
                                    id="sellerNewProductDiscount"
                                    name="discount"
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    value="{{ old('discount', 0) }}"
                                    class="h-10 w-full rounded-[10px] border border-[#dcd5cc] bg-white px-3 pr-9 text-[9px] font-normal text-[#332e28] outline-none transition focus:border-[#dca31d] focus:ring-4 focus:ring-[#f2b400]/10"
                                >
                                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[9px] font-medium text-[#b07d10]">%</span>
                            </div>

                            <p class="mt-2 text-[7px] leading-4 text-[#9b9287]">
                                The buyer price is deducted automatically from the original price.
                            </p>
                        </div>

                        <label class="flex cursor-pointer items-center justify-between gap-4 rounded-[14px] border border-[#e6dfd5] bg-white p-3.5 transition hover:border-[#ddc981]">
                            <span class="flex min-w-0 items-start gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-[10px] border border-[#c9e5d3] bg-[#f1faf4] text-[#3f855a]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M3 7h11v9H3z"></path>
                                        <path d="M14 10h4l3 3v3h-7z"></path>
                                        <circle cx="7" cy="18" r="1.5"></circle>
                                        <circle cx="18" cy="18" r="1.5"></circle>
                                    </svg>
                                </span>

                                <span class="min-w-0">
                                    <span class="block text-[8.5px] font-medium text-[#403930]">Free Shipping</span>
                                    <span class="mt-1 block text-[7px] leading-4 text-[#958c80]">
                                        When enabled, eligible buyer checkout for this seller will not add the standard delivery fee when every item in the seller group has free shipping.
                                    </span>
                                </span>
                            </span>

                            <span class="relative shrink-0">
                                <input
                                    id="sellerNewProductFreeShipping"
                                    name="free_shipping"
                                    type="checkbox"
                                    value="1"
                                    class="sr-only"
                                    @checked(old('free_shipping'))
                                >
                                <span class="seller-promo-toggle"></span>
                            </span>
                        </label>
                    </div>

                    <div id="sellerPromotionPreview" class="mt-3 rounded-[12px] border border-[#f0e5c1] bg-[#fffaf0] px-3.5 py-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-[7.5px] font-medium text-[#716754]">Live sale preview</p>
                            <div class="flex items-baseline gap-2">
                                <span id="sellerPromotionSalePrice" class="text-[12px] font-medium text-[#e8a400]">₱0.00</span>
                                <span id="sellerPromotionOriginalPrice" class="hidden text-[8px] font-normal text-[#a8a097] line-through">₱0.00</span>
                            </div>
                        </div>
                        <p id="sellerPromotionPreviewHint" class="mt-1 text-[7px] text-[#9a9185]">
                            Enter a price and discount to preview the buyer price.
                        </p>
                    </div>
                </section>
                    </div>
                </details>



                <details id="sellerAddDelivery" class="seller-add-optional-card">
                    <summary class="seller-add-optional-summary">
                        <span class="flex min-w-0 items-center gap-3">
                            <span class="seller-add-section-number">4</span>
                            <span>
                                <span class="block text-[10px] font-bold text-[#403930]">Shipping & extra details</span>
                                <span class="mt-0.5 block text-[7.5px] text-[#958c80]">Package size, preparation time, condition, low-stock warning, and specifications.</span>
                            </span>
                        </span>
                        <svg viewBox="0 0 24 24" class="seller-add-details-chevron h-4 w-4 shrink-0 text-[#8f7d63]" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </summary>
                    <div class="seller-add-optional-body space-y-5">
{{-- PACKAGE & FULFILLMENT — DATABASE BACKED --}}
                <section class="border-0 bg-transparent p-0">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-start gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-[10px] border border-[#e3ddd5] bg-[#faf8f5] text-[#756b60]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 8 12 4l8 4-8 4-8-4Z"></path>
                                    <path d="M5 11v6l7 3 7-3v-6"></path>
                                </svg>
                            </span>
                            <div>
                                <p class="text-[11px] font-bold text-[#302a24]">Package & fulfillment</p>
                                <p class="mt-1 text-[8px] leading-4 text-[#91887d]">
                                    Add practical delivery and inventory details. These values are stored as dedicated product fields.
                                </p>
                            </div>
                        </div>

                        <span class="rounded-full border border-[#dce6ed] bg-[#f5f9fc] px-2.5 py-1 text-[7px] font-semibold text-[#5f7e95]">
                            Shipping details
                        </span>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
                        <div>
                            <label class="mb-2 block text-[8px] font-semibold text-[#514a41]">Condition</label>
                            <select id="sellerProductCondition" name="condition" class="h-10 w-full rounded-xl border border-[#d9d2c9] bg-white px-3 text-[8.5px] text-[#51483f] outline-none focus:border-[#d48f08]">
                                <option value="">Not specified</option>
                                <option value="new">New</option>
                                <option value="like_new">Like New</option>
                                <option value="used">Used</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-[8px] font-semibold text-[#514a41]">Package Weight</label>
                            <div class="relative">
                                <input id="sellerPackageWeight" name="package_weight" type="number" min="0" step="0.01" placeholder="0.50" class="h-10 w-full rounded-xl border border-[#d9d2c9] bg-white px-3 pr-10 text-[8.5px] text-[#51483f] outline-none focus:border-[#d48f08]">
                                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[7.5px] font-semibold text-[#9b9287]">kg</span>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-[8px] font-semibold text-[#514a41]">Preparation Time</label>
                            <div class="relative">
                                <input id="sellerPreparationTime" name="preparation_days" type="number" min="0" step="1" placeholder="1" class="h-10 w-full rounded-xl border border-[#d9d2c9] bg-white px-3 pr-12 text-[8.5px] text-[#51483f] outline-none focus:border-[#d48f08]">
                                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[7.5px] font-semibold text-[#9b9287]">days</span>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-[8px] font-semibold text-[#514a41]">Low Stock Alert</label>
                            <input id="sellerLowStockThreshold" name="low_stock_threshold" type="number" min="0" step="1" value="5" placeholder="5" class="h-10 w-full rounded-xl border border-[#d9d2c9] bg-white px-3 text-[8.5px] text-[#51483f] outline-none focus:border-[#d48f08]">
                        </div>

                        <div>
                            <label class="mb-2 block text-[8px] font-semibold text-[#514a41]">Package Size</label>
                            <p class="h-10 rounded-xl border border-[#e4ddd3] bg-[#faf8f5] px-3 py-2 text-[7.5px] leading-3 text-[#8d847a]">
                                Length × Width × Height in cm
                            </p>
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-3 gap-2.5">
                        <div>
                            <label class="mb-1.5 block text-[7px] font-medium text-[#8e857b]">Length</label>
                            <div class="relative">
                                <input id="sellerPackageLength" name="package_length" type="number" min="0" step="0.1" placeholder="0" class="h-9 w-full rounded-[10px] border border-[#ddd6cc] bg-white px-3 pr-8 text-[8px] outline-none focus:border-[#d48f08]">
                                <span class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[7px] text-[#9b9287]">cm</span>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[7px] font-medium text-[#8e857b]">Width</label>
                            <div class="relative">
                                <input id="sellerPackageWidth" name="package_width" type="number" min="0" step="0.1" placeholder="0" class="h-9 w-full rounded-[10px] border border-[#ddd6cc] bg-white px-3 pr-8 text-[8px] outline-none focus:border-[#d48f08]">
                                <span class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[7px] text-[#9b9287]">cm</span>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[7px] font-medium text-[#8e857b]">Height</label>
                            <div class="relative">
                                <input id="sellerPackageHeight" name="package_height" type="number" min="0" step="0.1" placeholder="0" class="h-9 w-full rounded-[10px] border border-[#ddd6cc] bg-white px-3 pr-8 text-[8px] outline-none focus:border-[#d48f08]">
                                <span class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[7px] text-[#9b9287]">cm</span>
                            </div>
                        </div>
                    </div>
                </section>
                        <div class="border-t border-[#eee8df]"></div>
{{-- SPECIFICATIONS --}}
                <section class="border-0 bg-transparent p-0">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-[#302a24]">Product specifications</p>
                            <p class="mt-1 text-[8px] leading-4 text-[#91887d]">Add details such as material, dimensions, weight, model, warranty, capacity, author, or any custom attribute.</p>
                        </div>
                        <button id="sellerAddSpecification" type="button" class="inline-flex h-9 shrink-0 items-center justify-center gap-1.5 rounded-xl border border-[#d1d5db] bg-white px-3 text-[8px] font-bold text-[#374151] transition hover:border-[#9ca3af] hover:text-[#a8731f]">
                            <span class="text-[13px] leading-none">+</span> Add Specification
                        </button>
                    </div>

                    <div id="sellerSuggestedSpecifications" class="mt-3 hidden flex-wrap gap-1.5"></div>
                    <div id="sellerSpecificationsList" class="mt-4 space-y-2"></div>

                    <div id="sellerSpecificationEmpty" class="mt-4 rounded-[14px] border border-dashed border-[#d1d5db] bg-white px-4 py-5 text-center">
                        <p class="text-[8px] font-semibold text-[#6d645a]">No specifications added yet</p>
                        <p class="mt-1 text-[9px] text-[#9b9287]">Choose a category or add your own custom product detail.</p>
                    </div>
                </section>
                    </div>
                </details>

            </div>

            {{-- STICKY FOOTER --}}
            <div class="sticky bottom-0 z-10 flex flex-col-reverse gap-2 border-t border-[#e5e7eb] bg-white px-5 py-4 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between sm:px-7">
                <p class="hidden text-[7.5px] leading-4 text-[#9b9287] sm:block">Review the product details, preview the buyer view, then submit for review.</p>
                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:flex-wrap sm:justify-end">
                    <button id="cancelAddProductModal" type="button" class="h-10 rounded-xl border border-[#d1d5db] bg-white px-4 text-[9px] font-bold text-[#675f55] transition hover:bg-[#f9fafb]">Cancel</button>

                    

                    <button id="sellerPreviewProduct" type="button" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[#cfb67e] bg-[#fffaf1] px-4 text-[9px] font-bold text-[#91620f] transition hover:border-[#c99c42] hover:bg-[#fff5dc]">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path><circle cx="12" cy="12" r="2.5"></circle>
                        </svg>
                        Preview
                    </button>

                    <button id="sellerSubmitProduct" type="submit" @if ($sellerLocked) disabled @endif class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[#d48f08] px-5 text-[9px] font-bold text-white shadow-[0_8px_20px_rgba(212,143,8,.16)] transition hover:bg-[#bd7d05] disabled:cursor-not-allowed disabled:opacity-45">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg>
                        {{ $sellerLocked ? 'Selling Suspended' : 'Submit for Review' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


{{-- =============================================================
    ADD PRODUCT — BUYER PREVIEW
============================================================== --}}
<div id="sellerListingPreviewModal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-black/45 p-3 backdrop-blur-[3px] sm:p-5">
    <div class="flex max-h-[92vh] w-full max-w-[920px] flex-col overflow-hidden rounded-[24px] border border-[#e5ddd2] bg-white shadow-[0_30px_90px_rgba(37,29,20,.22)]">

        <div class="flex shrink-0 items-center justify-between gap-4 border-b border-[#eee8df] px-5 py-4 sm:px-6">
            <div>
                <p class="text-[8px] font-bold uppercase tracking-[.13em] text-[#b77912]">Buyer Preview</p>
                <h3 class="mt-1 text-[18px] font-bold tracking-[-.03em] text-[#28221b]">Preview your listing</h3>
                <p class="mt-1 text-[8px] text-[#91887d]">A simplified preview using the information currently entered in the form.</p>
            </div>

            <button id="closeSellerListingPreview" type="button" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#e5ddd2] bg-white text-[#756d63] transition hover:bg-[#fffaf1]" aria-label="Close preview">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="m7 7 10 10"></path><path d="m17 7-10 10"></path>
                </svg>
            </button>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto p-5 sm:p-6">
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-[320px_minmax(0,1fr)]">

                <div>
                    <div class="aspect-square overflow-hidden rounded-[20px] border border-[#e7e0d7] bg-[#f7f4ef]">
                        <img id="sellerPreviewImage" src="" alt="" class="hidden h-full w-full object-cover">
                        <div id="sellerPreviewImageEmpty" class="grid h-full w-full place-items-center text-[#aaa198]">
                            <div class="text-center">
                                <svg viewBox="0 0 24 24" class="mx-auto h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="m5 17 5-5 4 4 2-2 3 3"></path>
                                </svg>
                                <p class="mt-2 text-[8px]">No cover image selected</p>
                            </div>
                        </div>
                    </div>

                    <div id="sellerPreviewVariationChips" class="mt-3 flex flex-wrap gap-1.5"></div>
                </div>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="sellerPreviewCategory" class="rounded-full border border-[#eadfc9] bg-[#fffaf1] px-2.5 py-1 text-[7.5px] font-semibold text-[#966719]">Category</span>
                        <span id="sellerPreviewShipping" class="hidden rounded-full border border-[#cce2d4] bg-[#f1faf4] px-2.5 py-1 text-[7.5px] font-semibold text-[#3f7d59]">Free Shipping</span>
                    </div>

                    <h2 id="sellerPreviewName" class="mt-3 text-[24px] font-bold leading-tight tracking-[-.04em] text-[#25201b]">Untitled Product</h2>
                    <p id="sellerPreviewBrand" class="mt-1 text-[9px] text-[#8f877d]">No Brand</p>

                    <div class="mt-5 flex flex-wrap items-baseline gap-2.5">
                        <strong id="sellerPreviewPrice" class="text-[28px] font-bold tracking-[-.04em] text-[#d18b09]">₱0.00</strong>
                        <span id="sellerPreviewOriginalPrice" class="hidden text-[10px] text-[#aaa198] line-through">₱0.00</span>
                        <span id="sellerPreviewDiscount" class="hidden rounded-full border border-[#efd083] bg-[#fff6dc] px-2.5 py-1 text-[7.5px] font-bold text-[#9b6a0b]"></span>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-2.5 sm:grid-cols-3">
                        <div class="rounded-[13px] border border-[#ebe4da] bg-[#fcfbf8] p-3">
                            <p class="text-[7px] text-[#958c80]">Stock</p>
                            <p id="sellerPreviewStock" class="mt-1 text-[10px] font-bold text-[#403930]">0</p>
                        </div>
                        <div class="rounded-[13px] border border-[#ebe4da] bg-[#fcfbf8] p-3">
                            <p class="text-[7px] text-[#958c80]">Selling Format</p>
                            <p id="sellerPreviewFormat" class="mt-1 text-[9px] font-bold text-[#403930]">Single item</p>
                        </div>
                        <div class="rounded-[13px] border border-[#ebe4da] bg-[#fcfbf8] p-3">
                            <p class="text-[7px] text-[#958c80]">Preparation</p>
                            <p id="sellerPreviewPreparation" class="mt-1 text-[9px] font-bold text-[#403930]">Not specified</p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-[15px] border border-[#ebe4da] bg-white p-4">
                        <p class="text-[9px] font-semibold text-[#51483f]">Description</p>
                        <p id="sellerPreviewDescription" class="mt-2 whitespace-pre-line text-[9px] leading-5 text-[#756d63]">No description yet.</p>
                    </div>

                    <div id="sellerPreviewVariantTableWrap" class="mt-4 hidden overflow-hidden rounded-[15px] border border-[#e5ddd2]">
                        <div class="border-b border-[#eee8df] bg-[#fcfbf8] px-4 py-3">
                            <p class="text-[9px] font-bold text-[#51483f]">Available variations</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[520px] text-left">
                                <thead class="text-[7px] uppercase tracking-[.06em] text-[#9a9187]">
                                    <tr><th class="px-4 py-2.5">Option</th><th class="px-3 py-2.5">Price</th><th class="px-3 py-2.5">Stock</th></tr>
                                </thead>
                                <tbody id="sellerPreviewVariantRows" class="divide-y divide-[#eee9e2] text-[8px]"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex shrink-0 justify-end gap-2 border-t border-[#eee8df] bg-white px-5 py-4 sm:px-6">
            <button id="sellerPreviewBackToEdit" type="button" class="h-10 rounded-xl border border-[#dcd4c9] bg-white px-4 text-[9px] font-semibold text-[#655d53] transition hover:bg-[#faf8f5]">
                Back to Edit
            </button>
        </div>
    </div>
</div>

{{-- =============================================================
    VIEW PRODUCT MODAL — PREMIUM PRODUCT INSPECTOR
============================================================== --}}
<div id="sellerViewProductModal" class="fixed inset-0 z-[130] hidden items-center justify-center bg-black/45 p-3 backdrop-blur-[3px] sm:p-5">
    <div class="seller-product-view-dialog flex max-h-[94vh] w-full max-w-[1040px] flex-col overflow-hidden rounded-[24px] border border-[#e7dfd4] bg-white shadow-[0_30px_90px_rgba(38,30,18,0.22)]">

        {{-- HEADER --}}
        <div class="flex shrink-0 items-center justify-between gap-4 border-b border-[#eee8df] bg-white px-5 py-4 sm:px-6">
            <div class="flex min-w-0 items-center gap-3.5">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-[13px] border border-[#ead9b5] bg-[#fff8e9] text-[#b97913]">
                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7h16"></path>
                        <path d="M6 7v12h12V7"></path>
                        <path d="M9 11h6"></path>
                    </svg>
                </span>

                <div class="min-w-0">
                    <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#b77912]">Product Inspector</p>
                    <h3 class="mt-0.5 text-[17px] font-bold tracking-[-.025em] text-[#28221b] sm:text-[19px]">
                        Listing Details
                    </h3>
                </div>
            </div>

            <button
                type="button"
                data-close-view
                class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#e6dfd5] bg-white text-[#756d63] transition hover:border-[#d4bf97] hover:bg-[#fffaf1] hover:text-[#9a6817]"
                aria-label="Close product details"
            >
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="m7 7 10 10"></path><path d="m17 7-10 10"></path>
                </svg>
            </button>
        </div>

        {{-- SCROLLABLE CONTENT --}}
        <div class="min-h-0 flex-1 overflow-y-auto">

            {{-- PRODUCT HERO --}}
            <section class="grid grid-cols-1 gap-5 border-b border-[#f0ebe4] p-5 sm:p-6 lg:grid-cols-[330px_minmax(0,1fr)] lg:gap-7">

                {{-- IMAGE --}}
                <div>
                    <div id="viewProductImageWrap" class="relative aspect-square overflow-hidden rounded-[20px] border border-[#ebe3d9] bg-[#f8f5f0]">
                        <img id="viewProductImage" src="" alt="" class="hidden h-full w-full object-cover">

                        <div id="viewProductImagePlaceholder" class="grid h-full w-full place-items-center text-[#b0a79c]">
                            <div class="text-center">
                                <span class="mx-auto grid h-14 w-14 place-items-center rounded-[16px] border border-[#e5ddd2] bg-white text-[#9c9287]">
                                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6">
                                        <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                        <circle cx="9" cy="9" r="1.5"></circle>
                                        <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                                    </svg>
                                </span>
                                <p class="mt-3 text-[9px] font-semibold">No product image</p>
                            </div>
                        </div>

                        <span
                            id="viewProductStatusBadge"
                            class="absolute left-3 top-3 inline-flex min-h-[28px] items-center rounded-full border border-[#e1ddd6] bg-white/95 px-3 text-[8px] font-semibold text-[#71695f] shadow-sm backdrop-blur"
                        >
                            Status
                        </span>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2.5">
                        <div class="rounded-[13px] border border-[#ebe4db] bg-[#fcfbf8] px-3.5 py-3">
                            <p class="text-[7.5px] font-semibold uppercase tracking-[.07em] text-[#9b9287]">Created</p>
                            <p id="viewProductCreated" class="mt-1 text-[9px] font-semibold text-[#51483f]">—</p>
                        </div>

                        <div class="rounded-[13px] border border-[#ebe4db] bg-[#fcfbf8] px-3.5 py-3">
                            <p class="text-[7.5px] font-semibold uppercase tracking-[.07em] text-[#9b9287]">Rating</p>
                            <p id="viewProductRating" class="mt-1 text-[9px] font-semibold text-[#51483f]">—</p>
                        </div>
                    </div>

                    <div id="viewProductGallerySection" class="mt-3 hidden">
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <p class="text-[7.5px] font-semibold uppercase tracking-[.07em] text-[#9b9287]">Gallery</p>
                            <span id="viewProductGalleryCount" class="text-[7px] font-medium text-[#aaa198]"></span>
                        </div>
                        <div id="viewProductGallery" class="grid grid-cols-4 gap-2"></div>
                    </div>
                </div>

                {{-- MAIN INFORMATION --}}
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="viewProductCategoryChip" class="rounded-full border border-[#eadfc9] bg-[#fffaf1] px-2.5 py-1 text-[7.5px] font-semibold text-[#946417]">
                            Category
                        </span>

                        <span id="viewProductTypeChip" class="rounded-full border border-[#dfddd8] bg-[#f7f5f2] px-2.5 py-1 text-[7.5px] font-semibold text-[#71695f]">
                            Listing type
                        </span>

                        <span id="viewProductShippingChip" class="hidden rounded-full border border-[#cde4d6] bg-[#f1faf4] px-2.5 py-1 text-[7.5px] font-semibold text-[#3f7d59]">
                            Free Shipping
                        </span>
                    </div>

                    <h2 id="viewProductName" class="mt-3 text-[24px] font-bold leading-[1.22] tracking-[-.04em] text-[#25201b] sm:text-[28px]">
                        Product
                    </h2>

                    <p id="viewProductBrandLine" class="mt-1.5 text-[10px] font-medium text-[#8d847a]">
                        No Brand
                    </p>

                    {{-- PRICE --}}
                    <div class="mt-5 flex flex-wrap items-end gap-x-3 gap-y-2 border-b border-[#f0ebe4] pb-5">
                        <strong id="viewProductSalePrice" class="text-[28px] font-bold leading-none tracking-[-.045em] text-[#cc8a0f] sm:text-[32px]">
                            ₱0.00
                        </strong>

                        <span id="viewProductOriginalPrice" class="hidden pb-0.5 text-[11px] text-[#aaa198] line-through">
                            ₱0.00
                        </span>

                        <span id="viewProductDiscountBadge" class="hidden rounded-full border border-[#efd083] bg-[#fff6dc] px-2.5 py-1 text-[8px] font-bold text-[#9b6a0b]">
                            0% OFF
                        </span>
                    </div>

                    {{-- KEY METRICS --}}
                    <div class="mt-4 grid grid-cols-2 gap-2.5 sm:grid-cols-4">
                        <div class="seller-view-metric rounded-[14px] border border-[#ebe4da] bg-[#fcfbf8] p-3.5">
                            <span class="grid h-8 w-8 place-items-center rounded-[9px] bg-[#fff5df] text-[#b9780c]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 8 12 4l8 4-8 4-8-4Z"></path>
                                    <path d="M5 11v6l7 3 7-3v-6"></path>
                                </svg>
                            </span>
                            <p class="mt-2.5 text-[7.5px] font-medium text-[#9a9187]">Total Stock</p>
                            <p id="viewProductStock" class="mt-1 text-[13px] font-bold text-[#403930]">0</p>
                        </div>

                        <div class="seller-view-metric rounded-[14px] border border-[#ebe4da] bg-[#fcfbf8] p-3.5">
                            <span class="grid h-8 w-8 place-items-center rounded-[9px] bg-[#f0f5fa] text-[#527797]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M5 6h14v12H5z"></path><path d="M8 9h8"></path><path d="M8 13h5"></path>
                                </svg>
                            </span>
                            <p class="mt-2.5 text-[7.5px] font-medium text-[#9a9187]">SKU</p>
                            <p id="viewProductSku" class="mt-1 truncate text-[10px] font-bold text-[#403930]">—</p>
                        </div>

                        <div class="seller-view-metric rounded-[14px] border border-[#ebe4da] bg-[#fcfbf8] p-3.5">
                            <span class="grid h-8 w-8 place-items-center rounded-[9px] bg-[#f2f8f4] text-[#4e8062]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M20 13 13 20 4 11V4h7l9 9Z"></path><circle cx="8.5" cy="8.5" r="1"></circle>
                                </svg>
                            </span>
                            <p class="mt-2.5 text-[7.5px] font-medium text-[#9a9187]">Brand</p>
                            <p id="viewProductBrand" class="mt-1 truncate text-[10px] font-bold text-[#403930]">—</p>
                        </div>

                        <div class="seller-view-metric rounded-[14px] border border-[#ebe4da] bg-[#fcfbf8] p-3.5">
                            <span class="grid h-8 w-8 place-items-center rounded-[9px] bg-[#f7f3ec] text-[#8d6b35]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M5 7h14l-1 13H6L5 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path>
                                </svg>
                            </span>
                            <p class="mt-2.5 text-[7.5px] font-medium text-[#9a9187]">Listing Type</p>
                            <p id="viewProductType" class="mt-1 text-[9px] font-bold leading-4 text-[#403930]">—</p>
                        </div>
                    </div>

                    {{-- MODERATION --}}
                    <div class="mt-3.5 flex items-start gap-3 rounded-[14px] border border-[#eadfc9] bg-[#fffaf2] px-4 py-3.5">
                        <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-[9px] border border-[#ead6ad] bg-white text-[#a8751b]">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 3l7 3v5c0 4.5-2.9 8.2-7 9-4.1-.8-7-4.5-7-9V6l7-3Z"></path>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[7.5px] font-bold uppercase tracking-[.08em] text-[#a57622]">Moderation Status</p>
                            <p id="viewProductStatus" class="mt-1 text-[10px] font-bold text-[#76551a]">—</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- DETAILS GRID --}}
            <section class="grid grid-cols-1 gap-4 p-5 sm:p-6 lg:grid-cols-[1.1fr_.9fr]">

                {{-- DESCRIPTION --}}
                <div class="rounded-[17px] border border-[#ebe4da] bg-white p-4.5 sm:p-5">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-[10px] bg-[#f6f2eb] text-[#786c5c]">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M5 5h14v14H5z"></path><path d="M8 9h8"></path><path d="M8 13h8"></path>
                            </svg>
                        </span>
                        <div>
                            <h4 class="text-[11px] font-bold text-[#40382f]">Product Description</h4>
                            <p class="mt-0.5 text-[7.5px] text-[#9a9187]">Buyer-facing listing description.</p>
                        </div>
                    </div>
                    <p id="viewProductDescription" class="mt-4 whitespace-pre-line text-[9.5px] leading-6 text-[#746b61]"></p>
                </div>

                {{-- LISTING INFORMATION --}}
                <div class="rounded-[17px] border border-[#ebe4da] bg-[#fcfbf8] p-4.5 sm:p-5">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-[10px] bg-white text-[#8b6a32]">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="8"></circle><path d="M12 11v5"></path><path d="M12 8h.01"></path>
                            </svg>
                        </span>
                        <div>
                            <h4 class="text-[11px] font-bold text-[#40382f]">Listing Information</h4>
                            <p class="mt-0.5 text-[7.5px] text-[#9a9187]">Catalog and promotion details.</p>
                        </div>
                    </div>

                    <dl class="mt-4 divide-y divide-[#eee8df]">
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-[8px] text-[#948b80]">Category</dt>
                            <dd id="viewProductCategory" class="text-right text-[9px] font-semibold text-[#51483f]">—</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-[8px] text-[#948b80]">Condition</dt>
                            <dd id="viewProductCondition" class="text-right text-[9px] font-semibold text-[#51483f]">—</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-[8px] text-[#948b80]">Package</dt>
                            <dd id="viewProductPackage" class="text-right text-[9px] font-semibold text-[#51483f]">—</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-[8px] text-[#948b80]">Preparation</dt>
                            <dd id="viewProductPreparation" class="text-right text-[9px] font-semibold text-[#51483f]">—</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-[8px] text-[#948b80]">Low Stock Alert</dt>
                            <dd id="viewProductLowStock" class="text-right text-[9px] font-semibold text-[#51483f]">—</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-[8px] text-[#948b80]">Discount</dt>
                            <dd id="viewProductDiscountText" class="text-right text-[9px] font-semibold text-[#51483f]">—</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-[8px] text-[#948b80]">Voucher</dt>
                            <dd id="viewProductVoucher" class="text-right text-[9px] font-semibold text-[#51483f]">—</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-[8px] text-[#948b80]">Shipping</dt>
                            <dd id="viewProductShippingText" class="text-right text-[9px] font-semibold text-[#51483f]">—</dd>
                        </div>
                    </dl>
                </div>
            </section>

            {{-- SPECIFICATIONS --}}
            <section id="viewProductSpecificationsSection" class="mx-5 mb-4 hidden overflow-hidden rounded-[17px] border border-[#e8e1d7] bg-[#fcfbf8] sm:mx-6">
                <div class="flex items-center justify-between gap-3 border-b border-[#eee8df] bg-white px-4.5 py-3.5 sm:px-5">
                    <div>
                        <h4 class="text-[11px] font-bold text-[#40382f]">Specifications</h4>
                        <p class="mt-1 text-[7.5px] text-[#958c80]">Technical and descriptive product attributes.</p>
                    </div>
                    <span id="viewProductSpecificationCount" class="rounded-full border border-[#e4ddd3] bg-[#faf8f4] px-2.5 py-1 text-[7px] font-bold text-[#7e7469]"></span>
                </div>
                <div id="viewProductSpecifications" class="grid grid-cols-1 gap-2.5 p-4 sm:grid-cols-2 lg:grid-cols-3 sm:p-5"></div>
            </section>

            {{-- VARIANTS --}}
            <section id="viewProductVariantsSection" class="mx-5 mb-5 hidden overflow-hidden rounded-[17px] border border-[#e8e1d7] bg-white sm:mx-6 sm:mb-6">
                <div class="flex items-center justify-between gap-3 border-b border-[#eee8df] bg-[#fcfbf8] px-4.5 py-3.5 sm:px-5">
                    <div>
                        <h4 class="text-[11px] font-bold text-[#40382f]">Product Variants</h4>
                        <p class="mt-1 text-[7.5px] text-[#958c80]">Option combinations with independent price and stock.</p>
                    </div>
                    <span id="viewProductVariantCount" class="rounded-full border border-[#e4ddd3] bg-white px-2.5 py-1 text-[7px] font-bold text-[#7e7469]"></span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[680px] text-left">
                        <thead class="bg-white text-[7.5px] font-bold uppercase tracking-[.06em] text-[#9a9187]">
                            <tr>
                                <th class="px-5 py-3">Variant</th>
                                <th class="px-4 py-3">Image</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Price</th>
                                <th class="px-4 py-3">Stock</th>
                            </tr>
                        </thead>
                        <tbody id="viewProductVariants" class="divide-y divide-[#eee9e2] text-[8.5px]"></tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- FOOTER --}}
        <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-[#eee8df] bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <p class="hidden text-[7.5px] text-[#9a9187] sm:block">
                This view uses your current Product Library information.
            </p>

            <div class="flex gap-2">
                <button
                    type="button"
                    data-close-view
                    class="h-10 rounded-xl border border-[#dfd7cc] bg-white px-4 text-[9px] font-semibold text-[#655d53] transition hover:bg-[#faf8f5]"
                >
                    Close
                </button>

                <button
                    id="viewProductEditButton"
                    type="button"
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[#c98d19] px-4 text-[9px] font-semibold text-white shadow-[0_7px_16px_rgba(201,141,25,.14)] transition hover:bg-[#b77d10]"
                >
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                    </svg>
                    Edit Product
                </button>
            </div>
        </div>
    </div>
</div>


{{-- =============================================================
    EDIT PRODUCT MODAL
============================================================== --}}
<div id="sellerEditProductModal" class="fixed inset-0 z-[130] hidden items-center justify-center bg-black/40 p-4 backdrop-blur-[2px]">
    <div class="max-h-[92vh] w-full max-w-[760px] overflow-y-auto rounded-[24px] border border-[#e9dfcf] bg-white shadow-[0_30px_90px_rgba(38,30,18,0.22)]">
        <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-[#eee7dc] bg-white p-5 sm:p-6">
            <div>
                <p class="text-[9px] font-semibold uppercase tracking-[0.12em] text-[#a8731f]">Product Management</p>
                <h3 class="mt-1 text-[19px] font-bold text-[#28221b]">Edit Product</h3>
                <p class="mt-1 text-[9px] text-[#91887d]">Sensitive listing changes are re-screened automatically. Your existing specifications and variants are preserved when editing common fields.</p>
            </div>
            <button type="button" data-close-edit class="grid h-10 w-10 place-items-center rounded-xl border border-[#e6dfd5] text-[#756d63] hover:bg-[#fcf7ee]">×</button>
        </div>

        <form id="sellerEditProductForm" method="POST" action="" enctype="multipart/form-data" class="p-5 sm:p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Product Name *</label>
                    <input id="editProductName" name="name" required class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none focus:border-[#c99a3d] focus:ring-4 focus:ring-[#c99a3d]/10">
                </div>

                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Category *</label>
                    <div class="relative">
                        <select id="editProductCategory" name="category" required class="h-11 w-full appearance-none rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] pl-3.5 pr-10 text-[10px] font-medium text-[#40382f] outline-none transition focus:border-[#c99a3d] focus:ring-4 focus:ring-[#c99a3d]/10">
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
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9b7a3f]" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m7 10 5 5 5-5"></path></svg>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Brand</label>
                    <input id="editProductBrand" name="brand" placeholder="Example: Samsung, Nike, No Brand" class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none transition focus:border-[#c99a3d] focus:ring-4 focus:ring-[#c99a3d]/10">
                </div>

                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">SKU</label>
                    <input id="editProductSku" name="sku" class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Price *</label>
                    <input id="editProductPrice" name="price" type="number" min="0" step="0.01" required class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Stock *</label>
                    <input id="editProductStock" name="stock" type="number" min="0" required class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-[9px] font-medium text-[#514a41]">Discount %</label>
                    <input id="editProductDiscount" name="discount" type="number" min="0" max="100" step="0.01" class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-white px-4 text-[9px] font-normal outline-none focus:border-[#dca31d] focus:ring-4 focus:ring-[#f2b400]/10">
                    <p id="editProductSalePreview" class="mt-1.5 text-[7px] font-normal text-[#9a9185]"></p>
                </div>

                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Voucher Code</label>
                    <input id="editProductVoucher" name="voucher_code" class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="flex cursor-pointer items-center justify-between gap-4 rounded-[14px] border border-[#e6dfd5] bg-[#fffefb] p-3.5">
                        <span>
                            <span class="block text-[9px] font-medium text-[#403930]">Free Shipping</span>
                            <span class="mt-1 block text-[7.5px] font-normal text-[#958c80]">Show the green Free Shipping badge and waive the eligible buyer delivery fee.</span>
                        </span>
                        <span class="relative shrink-0">
                            <input id="editProductFreeShipping" name="free_shipping" type="checkbox" value="1" class="sr-only">
                            <span class="seller-promo-toggle"></span>
                        </span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Replace Product Image</label>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-[120px_1fr]">
                        <div id="editCurrentImageWrap" class="hidden h-[100px] overflow-hidden rounded-xl border border-[#e6dfd5] bg-[#fcfaf7]">
                            <img id="editCurrentImage" src="" alt="Current image" class="h-full w-full object-cover">
                        </div>
                        <input name="image" type="file" accept="image/*" class="block w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-3 py-3 text-[9px]">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Description</label>
                    <textarea id="editProductDescription" name="description" rows="4" class="w-full resize-none rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 py-3 text-[10px] leading-5 outline-none"></textarea>
                </div>
            </div>

            <div class="mt-5 rounded-[14px] border border-[#eadfc9] bg-[#fffaf2] p-4 text-[9px] leading-5 text-[#8f7953]">
                <strong>Sensitive edits:</strong> name, category, brand, description, specifications, variant options, and product image. When these change, previous approval is removed and SARI screens the listing again before admin re-review.
            </div>

            <div class="mt-6 flex justify-end gap-2 border-t border-[#eee8df] pt-5">
                <button type="button" data-close-edit class="h-11 rounded-xl border border-[#e0d7ca] bg-white px-5 text-[10px] font-semibold text-[#62594e]">Cancel</button>
                <button type="submit" class="h-11 rounded-xl bg-[#c99128] px-5 text-[10px] font-semibold text-white hover:bg-[#b47e1e]">Save Changes</button>
            </div>
        </form>
    </div>
</div>



{{-- ARCHIVE / DELETE CONFIRMATION --}}
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
document.addEventListener('livewire:navigated', function () {
    window.__SARI_PRODUCTS_PAGE_ABORT__?.abort();
    const pageAbort = new AbortController();
    window.__SARI_PRODUCTS_PAGE_ABORT__ = pageAbort;
    const signal = pageAbort.signal;

    const libraryUrl = @json(route('seller.products.library'));
    const productsBaseUrl = @json(url('/seller/products'));
    const productDraftUrl = @json(route('seller.products.draft'));
    const productDraftSaveUrl = @json(route('seller.products.draft.save'));
    const productDraftDeleteUrl = @json(route('seller.products.draft.delete'));
    const requestedCategory = @json((string) request('category', ''));

    const grid = document.getElementById('productsGrid');
    const empty = document.getElementById('productsEmpty');
    const resultCount = document.getElementById('productsResultCount');
    const search = document.getElementById('productsSearch');
    const status = document.getElementById('productsStatus');
    const stock = document.getElementById('productsStock');
    const category = document.getElementById('productsCategory');
    const clear = document.getElementById('productsClearFilters');

    const totalCount = document.getElementById('productsTotalCount');
    const approvedCount = document.getElementById('productsApprovedCount');
    const lowStockCount = document.getElementById('productsLowStockCount');
    const outStockCount = document.getElementById('productsOutStockCount');

    const catalogHealthScore = document.getElementById('productsCatalogHealthScore');
    const catalogHealthLabel = document.getElementById('productsCatalogHealthLabel');
    const catalogHealthBar = document.getElementById('productsCatalogHealthBar');
    const catalogAttention = document.getElementById('productsCatalogAttention');

    const healthApproved = document.getElementById('productsHealthApproved');
    const healthLow = document.getElementById('productsHealthLow');
    const healthOut = document.getElementById('productsHealthOut');
    const healthReview = document.getElementById('productsHealthReview');

    const quickAll = document.getElementById('productsQuickAll');
    const quickApproved = document.getElementById('productsQuickApproved');
    const quickPending = document.getElementById('productsQuickPending');
    const quickLow = document.getElementById('productsQuickLow');
    const quickOut = document.getElementById('productsQuickOut');
    const quickFilterButtons = Array.from(document.querySelectorAll('[data-products-quick-filter]'));

    const actionModal = document.getElementById('productsActionModal');
    const actionForm = document.getElementById('productsActionForm');
    const actionTitle = document.getElementById('productsActionTitle');
    const actionText = document.getElementById('productsActionText');
    const actionSubmit = document.getElementById('productsActionSubmit');
    const actionCancel = document.getElementById('productsActionCancel');

    let products = [];

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function peso(value) {
        return '₱' + Number(value || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function statusClass(value) {
        const map = {
            approved: 'border-[#d8e9df] bg-[#edf7f1] text-[#4f8065]',
            pending: 'border-[#d8e5f1] bg-[#eef5fc] text-[#537a9f]',
            flagged: 'border-[#f0d4d4] bg-[#fff0f0] text-[#b45b5b]',
            rejected: 'border-[#eed2d2] bg-[#fff0f0] text-[#a85858]',
            removed: 'border-[#e2ddd6] bg-[#f4f2ef] text-[#746d64]',
        };
        return map[String(value || '').toLowerCase()] || 'border-[#e2ddd6] bg-[#f4f2ef] text-[#746d64]';
    }

    function updateSummary() {
        const total = products.length;
        const approved = products.filter((p) => String(p.moderation_status || '').toLowerCase() === 'approved').length;
        const pending = products.filter((p) => String(p.moderation_status || '').toLowerCase() === 'pending').length;
        const low = products.filter((p) => String(p.stock_state || '') === 'low-stock').length;
        const out = products.filter((p) => Number(p.stock || 0) <= 0).length;

        if (totalCount) totalCount.textContent = total.toLocaleString('en-PH');
        if (approvedCount) approvedCount.textContent = approved.toLocaleString('en-PH');
        if (lowStockCount) lowStockCount.textContent = low.toLocaleString('en-PH');
        if (outStockCount) outStockCount.textContent = out.toLocaleString('en-PH');

        if (healthApproved) healthApproved.textContent = approved.toLocaleString('en-PH');
        if (healthLow) healthLow.textContent = low.toLocaleString('en-PH');
        if (healthOut) healthOut.textContent = out.toLocaleString('en-PH');
        if (healthReview) healthReview.textContent = pending.toLocaleString('en-PH');

        if (quickAll) quickAll.textContent = total.toLocaleString('en-PH');
        if (quickApproved) quickApproved.textContent = approved.toLocaleString('en-PH');
        if (quickPending) quickPending.textContent = pending.toLocaleString('en-PH');
        if (quickLow) quickLow.textContent = low.toLocaleString('en-PH');
        if (quickOut) quickOut.textContent = out.toLocaleString('en-PH');

        /*
         * Readiness is intentionally derived only from data already present
         * on this page: approved + currently in-stock listings.
         */
        const ready = products.filter((p) => (
            String(p.moderation_status || '').toLowerCase() === 'approved'
            && Number(p.stock || 0) > 0
        )).length;

        const score = total ? Math.round((ready / total) * 100) : 0;
        const attention = products.filter((p) => (
            String(p.moderation_status || '').toLowerCase() !== 'approved'
            || (Number(p.stock || 0) > 0 && Number(p.stock || 0) <= Number(p.low_stock_threshold ?? 5))
        )).length;

        if (catalogHealthScore) catalogHealthScore.textContent = score.toLocaleString('en-PH');
        if (catalogAttention) {
            catalogAttention.textContent = attention === 1
                ? '1 listing needs attention'
                : `${attention.toLocaleString('en-PH')} listings need attention`;
        }

        const scoreColor = score >= 80 ? '#4f8065' : (score >= 50 ? '#c48a13' : '#b65e5e');
        const scoreLabel = score >= 80 ? 'Strong' : (score >= 50 ? 'Needs Attention' : 'Priority Review');

        if (catalogHealthBar) {
            catalogHealthBar.style.width = `${score}%`;
            catalogHealthBar.style.backgroundColor = scoreColor;
        }

        if (catalogHealthScore) catalogHealthScore.style.color = scoreColor;

        if (catalogHealthLabel) {
            catalogHealthLabel.textContent = scoreLabel;
            catalogHealthLabel.style.color = scoreColor;
            catalogHealthLabel.style.borderColor = score >= 80 ? '#cfe4d7' : (score >= 50 ? '#ead9b2' : '#ebcaca');
            catalogHealthLabel.style.backgroundColor = score >= 80 ? '#f3faf5' : (score >= 50 ? '#fff8e9' : '#fff4f4');
        }
    }

    function populateCategories() {
        if (!category) return;

        const categories = [...new Set(products.map((p) => String(p.category || 'Uncategorized').trim() || 'Uncategorized'))]
            .sort((a, b) => a.localeCompare(b));

        category.innerHTML = '<option value="">All Categories</option>' + categories.map((item) => (
            `<option value="${escapeHtml(item)}">${escapeHtml(item)}</option>`
        )).join('');

        if (requestedCategory && categories.includes(requestedCategory)) {
            category.value = requestedCategory;
        }
    }

    function cardMarkup(product) {
        const stockValue = Number(product.stock || 0);
        const discount = Number(product.discount || 0);
        const threshold = Number(product.low_stock_threshold ?? 5);
        const stockTextClass = stockValue <= 0
            ? 'text-[#b85b5b]'
            : (stockValue <= threshold ? 'text-[#b87912]' : 'text-[#6e675f]');

        const image = product.image_url
            ? `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.name || 'Product')}" class="seller-product-image h-full w-full object-cover" loading="lazy" decoding="async">`
            : `<div class="grid h-full w-full place-items-center bg-[#f7f4ef] text-[#aaa198]"><svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="m5 17 5-5 4 4 2-2 3 3"></path></svg></div>`;

        return `
            <article
                data-product-item
                data-search="${escapeHtml([product.name, product.category, product.brand, product.sku].filter(Boolean).join(' ').toLowerCase())}"
                data-status="${escapeHtml(product.moderation_status || '')}"
                data-stock="${escapeHtml(product.stock_state || '')}"
                data-category="${escapeHtml(product.category || 'Uncategorized')}"
                class="seller-products-card overflow-hidden rounded-[18px] border border-[#e8e0d6] bg-white"
            >
                <div class="relative h-[205px] overflow-hidden bg-[#f6f3ee]">
                    ${image}

                    <div class="absolute left-2.5 top-2.5 flex max-w-[70%] flex-wrap items-center gap-1.5">
                        ${product.mall_badge ? '<span class="rounded-full border border-white/15 bg-[#292520]/95 px-3 py-1.5 text-[8px] font-semibold text-white shadow-sm">Mall</span>' : ''}
                        ${product.on_trend ? '<span class="rounded-full border border-[#efd06b] bg-[#fff7d8]/95 px-3 py-1.5 text-[8px] font-semibold text-[#a97208] shadow-sm">On Trend</span>' : ''}
                        ${discount > 0 ? `<span class="rounded-full border border-[#efc74e] bg-[#ffdd58]/95 px-3 py-1.5 text-[8px] font-semibold text-[#604700] shadow-sm">-${discount.toLocaleString('en-PH', {maximumFractionDigits: 2})}%</span>` : ''}
                    </div>

                    <span class="absolute right-3 top-3 rounded-full border px-3 py-1.5 text-[8px] font-semibold shadow-sm ${statusClass(product.moderation_status)}">
                        ${escapeHtml(product.status_label || product.moderation_status || 'Unknown')}
                    </span>
                </div>

                <div class="p-4.5">
                    <div class="min-w-0">
                        <h3 class="truncate text-[14px] font-semibold tracking-[-0.02em] text-[#2e2924]">${escapeHtml(product.name || '')}</h3>
                        <p class="mt-1.5 truncate text-[9.5px] text-[#938a80]">${escapeHtml(product.category || 'Uncategorized')}${product.brand ? ' · ' + escapeHtml(product.brand) : ''}</p>
                    </div>

                    <div class="mt-3.5 flex items-end justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-baseline gap-1.5">
                                <strong class="text-[17px] font-semibold tracking-[-0.025em] text-[#d78e07]">${peso(product.sale_price ?? product.price)}</strong>
                                ${discount > 0 ? `<span class="text-[9px] text-[#aaa198] line-through">${peso(product.price)}</span>` : ''}
                            </div>
                        </div>
                        <span class="shrink-0 text-[9px] font-medium ${stockTextClass}">
                            ${stockValue <= 0 ? 'Stock: 0' : `Stock: ${stockValue.toLocaleString('en-PH')}`}
                        </span>
                    </div>

                    <div class="mt-3 flex min-h-[24px] items-center justify-between gap-3">
                        <span class="truncate text-[9px] text-[#91887d]"><span class="text-[#f3ad10]">★</span> ${Number(product.rating_count || 0) ? Number(product.rating || 0).toFixed(1) + ' (' + Number(product.rating_count).toLocaleString('en-PH') + ')' : 'No ratings yet'}</span>
                        <span class="shrink-0 text-[9px] text-[#a1988e]">${escapeHtml(product.created_at_human || '')}</span>
                    </div>

                    <div class="mt-4 grid grid-cols-4 gap-2.5">
                        <button type="button" data-page-view-product data-id="${escapeHtml(product.id)}" title="View product" aria-label="View product" class="seller-product-action seller-product-action--view grid h-10 place-items-center rounded-[10px]">
                            <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path><circle cx="12" cy="12" r="2.5"></circle></svg>
                        </button>
                        <button type="button" data-page-edit-product data-id="${escapeHtml(product.id)}" title="Edit product" aria-label="Edit product" class="seller-product-action seller-product-action--edit grid h-10 place-items-center rounded-[10px]">
                            <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg>
                        </button>
                        <button type="button" data-page-product-action="archive" data-id="${escapeHtml(product.id)}" data-name="${escapeHtml(product.name || '')}" title="Archive product" aria-label="Archive product" class="seller-product-action seller-product-action--archive grid h-10 place-items-center rounded-[10px]">
                            <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="7" width="16" height="13" rx="2"></rect><path d="M3 4h18v3H3z"></path><path d="M10 11h4"></path></svg>
                        </button>
                        <button type="button" data-page-product-action="delete" data-id="${escapeHtml(product.id)}" data-name="${escapeHtml(product.name || '')}" title="Remove product" aria-label="Remove product" class="seller-product-action seller-product-action--delete grid h-10 place-items-center rounded-[10px]">
                            <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="M7 7l1 13h8l1-13"></path><path d="M10 11v5"></path><path d="M14 11v5"></path></svg>
                        </button>
                    </div>
                </div>
            </article>
        `;
    }

    function syncQuickFilterState() {
        const statusValue = String(status?.value || '').toLowerCase();
        const stockValue = String(stock?.value || '').toLowerCase();

        let active = 'all';

        if (statusValue === 'approved' && !stockValue) {
            active = 'approved';
        } else if (statusValue === 'pending' && !stockValue) {
            active = 'pending';
        } else if (stockValue === 'low-stock' && !statusValue) {
            active = 'low';
        } else if (stockValue === 'out-of-stock' && !statusValue) {
            active = 'out';
        } else if (statusValue || stockValue) {
            active = '';
        }

        quickFilterButtons.forEach((button) => {
            button.classList.toggle('is-active', button.dataset.productsQuickFilter === active);
        });
    }

    function applyFilters() {
        const q = String(search?.value || '').trim().toLowerCase();
        const statusValue = String(status?.value || '').toLowerCase();
        const stockValue = String(stock?.value || '').toLowerCase();
        const categoryValue = String(category?.value || '');

        const cards = Array.from(grid?.querySelectorAll('[data-product-item]') || []);
        let visible = 0;

        cards.forEach((card) => {
            const matches =
                (!q || String(card.dataset.search || '').includes(q)) &&
                (!statusValue || String(card.dataset.status || '').toLowerCase() === statusValue) &&
                (!stockValue || String(card.dataset.stock || '').toLowerCase() === stockValue) &&
                (!categoryValue || String(card.dataset.category || '') === categoryValue);

            card.classList.toggle('hidden', !matches);
            if (matches) visible++;
        });

        if (resultCount) {
            resultCount.textContent = `Showing ${visible.toLocaleString('en-PH')} of ${cards.length.toLocaleString('en-PH')} products`;
        }

        empty?.classList.toggle('hidden', visible !== 0 || cards.length === 0);
        syncQuickFilterState();
    }

    function renderProducts() {
        if (!grid) return;

        grid.innerHTML = products.map(cardMarkup).join('');
        updateSummary();
        populateCategories();
        applyFilters();

        if (!products.length) {
            grid.innerHTML = `
                <div class="col-span-full rounded-[16px] border border-dashed border-[#ded5c8] bg-[#fcfaf7] px-6 py-14 text-center sm:col-span-1 lg:col-span-2">
                    <p class="text-[11px] font-semibold text-[#514a42]">No active products yet.</p>
                    <p class="mt-1 text-[9px] text-[#91887d]">Use Add New Product to create your first listing.</p>
                </div>
            `;
            if (resultCount) resultCount.textContent = '0 active products';
        }
    }

    async function loadProducts() {
        try {
            const response = await fetch(libraryUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                cache: 'no-store',
                signal,
            });

            if (!response.ok) throw new Error('Unable to load Product Library.');
            const data = await response.json();
            products = Array.isArray(data?.products) ? data.products : [];
            renderProducts();
        } catch (error) {
            if (error?.name === 'AbortError') return;
            console.error(error);
            if (grid) {
                grid.innerHTML = `
                    <div class="col-span-full rounded-[16px] border border-[#efd7d7] bg-[#fff8f8] px-6 py-16 text-center">
                        <p class="text-[11px] font-semibold text-[#a55b5b]">Unable to load your products.</p>
                        <p class="mt-1 text-[9px] text-[#987777]">Refresh the page and try again.</p>
                    </div>
                `;
            }
            if (resultCount) resultCount.textContent = 'Product Library unavailable';
        }
    }

    [search, status, stock, category].forEach((element) => {
        element?.addEventListener(element === search ? 'input' : 'change', applyFilters, { signal });
    });

    quickFilterButtons.forEach((button) => {
        button.addEventListener('click', function () {
            const mode = String(button.dataset.productsQuickFilter || 'all');

            if (status) status.value = '';
            if (stock) stock.value = '';

            if (mode === 'approved' && status) {
                status.value = 'approved';
            } else if (mode === 'pending' && status) {
                status.value = 'pending';
            } else if (mode === 'low' && stock) {
                stock.value = 'low-stock';
            } else if (mode === 'out' && stock) {
                stock.value = 'out-of-stock';
            }

            applyFilters();
        }, { signal });
    });

    clear?.addEventListener('click', function () {
        if (search) search.value = '';
        if (status) status.value = '';
        if (stock) stock.value = '';
        if (category) category.value = '';
        applyFilters();
        search?.focus();
    }, { signal });


    /*
    |--------------------------------------------------------------------------
    | VIEW / EDIT — LOCAL PRODUCT MANAGEMENT
    |--------------------------------------------------------------------------
    | No Dashboard query-string bridge. The already-loaded Product Library
    | object is used directly by this page.
    */
    const viewProductModal = document.getElementById('sellerViewProductModal');
    const editProductModal = document.getElementById('sellerEditProductModal');
    const editProductForm = document.getElementById('sellerEditProductForm');
    const editProductPriceInput = document.getElementById('editProductPrice');
    const editProductDiscountInput = document.getElementById('editProductDiscount');
    const editProductSalePreview = document.getElementById('editProductSalePreview');

    function getProductById(id) {
        return products.find((product) => String(product?.id ?? '') === String(id ?? '')) || null;
    }

    function showProductPageModal(element) {
        if (!element) return;
        element.classList.remove('hidden');
        element.classList.add('flex');
        document.body.classList.add('overflow-hidden');
        requestAnimationFrame(() => element.classList.add('seller-modal-visible'));
    }

    function hideProductPageModal(element) {
        if (!element) return;
        element.classList.remove('seller-modal-visible');
        window.setTimeout(() => {
            element.classList.add('hidden');
            element.classList.remove('flex');
            if (
                !viewProductModal?.classList.contains('flex') &&
                !editProductModal?.classList.contains('flex') &&
                !actionModal?.classList.contains('flex')
            ) {
                document.body.classList.remove('overflow-hidden');
            }
        }, 190);
    }

    function discountedProductPrice(price, discount) {
        const original = Math.max(0, Number(price || 0));
        const percent = Math.min(100, Math.max(0, Number(discount || 0)));
        return original * (1 - percent / 100);
    }

    function formatProductVariantOptions(options) {
        if (!options || typeof options !== 'object') return 'Default';
        const entries = Object.entries(options)
            .filter(([, value]) => String(value ?? '').trim() !== '');

        return entries.length
            ? entries.map(([key, value]) => `${key}: ${value}`).join(' · ')
            : 'Default';
    }

    let activeViewedProduct = null;

    function viewStatusStyle(status) {
        const value = String(status || '').toLowerCase();

        if (value === 'approved') {
            return {
                classes: 'border-[#cce2d4] bg-[#f1faf4] text-[#3f7d59]',
                label: 'Approved'
            };
        }

        if (value === 'pending') {
            return {
                classes: 'border-[#cedfec] bg-[#f1f7fc] text-[#527797]',
                label: 'Under Review'
            };
        }

        if (value === 'flagged') {
            return {
                classes: 'border-[#efd1d1] bg-[#fff3f3] text-[#a95656]',
                label: 'Flagged'
            };
        }

        if (value === 'rejected') {
            return {
                classes: 'border-[#edcccc] bg-[#fff1f1] text-[#a04f4f]',
                label: 'Rejected'
            };
        }

        return {
            classes: 'border-[#e1ddd6] bg-[#f7f5f2] text-[#71695f]',
            label: status ? String(status) : 'Unknown'
        };
    }

    function openLocalViewProduct(product) {
        if (!product || !viewProductModal) return;

        activeViewedProduct = product;

        const originalPrice = Math.max(0, Number(product.price || 0));
        const discount = Math.min(100, Math.max(0, Number(product.discount || 0)));
        const salePrice = Number(product.sale_price ?? discountedProductPrice(originalPrice, discount));
        const stockValue = Number(product.stock || 0);
        const variants = Array.isArray(product.variants) ? product.variants : [];
        const specifications = Array.isArray(product.specifications) ? product.specifications : [];
        const isVariantProduct = Boolean(product.has_variants || variants.length);
        const freeShipping = Boolean(product.free_shipping);

        const productName = product.name || 'Product';
        const categoryName = product.category || 'Uncategorized';
        const brandName = product.brand || 'No Brand';
        const listingType = isVariantProduct ? 'With Variants' : 'Simple Product';
        const moderationLabel = product.status_label || product.moderation_status || 'Unknown';

        document.getElementById('viewProductName').textContent = productName;
        document.getElementById('viewProductCategory').textContent = categoryName;
        document.getElementById('viewProductCategoryChip').textContent = categoryName;
        document.getElementById('viewProductBrand').textContent = brandName;
        document.getElementById('viewProductBrandLine').textContent = `${brandName} · ${product.sku || 'No SKU'}`;
        document.getElementById('viewProductSku').textContent = product.sku || '—';
        document.getElementById('viewProductStock').textContent = stockValue.toLocaleString('en-PH');
        document.getElementById('viewProductType').textContent = listingType;
        document.getElementById('viewProductTypeChip').textContent = listingType;
        document.getElementById('viewProductStatus').textContent = moderationLabel;
        document.getElementById('viewProductDescription').textContent =
            product.description || 'No description has been added for this product yet.';

        const conditionLabels = { new: 'New', like_new: 'Like New', used: 'Used' };
        document.getElementById('viewProductCondition').textContent =
            conditionLabels[String(product.condition || '')] || 'Not specified';

        const packageParts = [];
        if (product.package_weight !== null && product.package_weight !== undefined) packageParts.push(`${Number(product.package_weight)} kg`);
        const dimensions = [product.package_length, product.package_width, product.package_height];
        if (dimensions.every(value => value !== null && value !== undefined && value !== '')) {
            packageParts.push(`${Number(dimensions[0])} × ${Number(dimensions[1])} × ${Number(dimensions[2])} cm`);
        }
        document.getElementById('viewProductPackage').textContent = packageParts.join(' · ') || 'Not specified';
        document.getElementById('viewProductPreparation').textContent = product.preparation_days !== null && product.preparation_days !== undefined
            ? `${Number(product.preparation_days)} day${Number(product.preparation_days) === 1 ? '' : 's'}`
            : 'Not specified';
        document.getElementById('viewProductLowStock').textContent = `${Number(product.low_stock_threshold ?? 5).toLocaleString('en-PH')} units`;

        document.getElementById('viewProductSalePrice').textContent = peso(salePrice);

        const originalPriceNode = document.getElementById('viewProductOriginalPrice');
        const discountBadge = document.getElementById('viewProductDiscountBadge');
        const discountText = document.getElementById('viewProductDiscountText');

        if (discount > 0) {
            originalPriceNode.textContent = peso(originalPrice);
            originalPriceNode.classList.remove('hidden');

            discountBadge.textContent = `${discount.toLocaleString('en-PH', { maximumFractionDigits: 2 })}% OFF`;
            discountBadge.classList.remove('hidden');

            discountText.textContent = `${discount.toLocaleString('en-PH', { maximumFractionDigits: 2 })}% off`;
        } else {
            originalPriceNode.classList.add('hidden');
            discountBadge.classList.add('hidden');
            discountText.textContent = 'No active discount';
        }

        const voucher = String(product.voucher_code || '').trim();
        document.getElementById('viewProductVoucher').textContent = voucher || 'None';

        const shippingChip = document.getElementById('viewProductShippingChip');
        document.getElementById('viewProductShippingText').textContent =
            freeShipping ? 'Free Shipping enabled' : 'Standard shipping';

        shippingChip.classList.toggle('hidden', !freeShipping);

        document.getElementById('viewProductCreated').textContent =
            product.created_at_human || '—';

        const ratingCount = Number(product.rating_count || 0);
        const ratingValue = Number(product.rating || 0);
        document.getElementById('viewProductRating').textContent =
            ratingCount > 0
                ? `★ ${ratingValue.toFixed(1)} (${ratingCount.toLocaleString('en-PH')})`
                : 'No ratings';

        const statusBadge = document.getElementById('viewProductStatusBadge');
        const statusVisual = viewStatusStyle(product.moderation_status);

        statusBadge.className =
            `absolute left-3 top-3 inline-flex min-h-[28px] items-center rounded-full border px-3 text-[8px] font-semibold shadow-sm backdrop-blur ${statusVisual.classes}`;
        statusBadge.textContent = product.status_label || statusVisual.label;

        const gallerySection = document.getElementById('viewProductGallerySection');
        const galleryGrid = document.getElementById('viewProductGallery');
        const galleryCount = document.getElementById('viewProductGalleryCount');
        const galleryItems = Array.isArray(product.gallery) ? product.gallery : [];

        if (galleryItems.length) {
            galleryGrid.innerHTML = galleryItems.map((item, index) => `
                <div class="aspect-square overflow-hidden rounded-[9px] border border-[#e6dfd5] bg-[#f8f5f0]">
                    <img src="${escapeHtml(item?.url || '')}" alt="Gallery image ${index + 1}" class="h-full w-full object-cover">
                </div>
            `).join('');
            galleryCount.textContent = `${galleryItems.length} image${galleryItems.length === 1 ? '' : 's'}`;
            gallerySection.classList.remove('hidden');
        } else {
            galleryGrid.innerHTML = '';
            gallerySection.classList.add('hidden');
        }

        const specSection = document.getElementById('viewProductSpecificationsSection');
        const specGrid = document.getElementById('viewProductSpecifications');
        const specCount = document.getElementById('viewProductSpecificationCount');

        if (specifications.length) {
            specGrid.innerHTML = specifications.map((spec) => `
                <div data-view-spec-card class="rounded-[13px] border border-[#ebe4da] bg-white px-3.5 py-3">
                    <p class="text-[7.5px] font-medium text-[#958c80]">
                        ${escapeHtml(spec?.name || 'Specification')}
                    </p>
                    <p class="mt-1.5 text-[9.5px] font-semibold text-[#40382f]">
                        ${escapeHtml(spec?.value || '—')}${spec?.unit ? ' ' + escapeHtml(spec.unit) : ''}
                    </p>
                </div>
            `).join('');

            specCount.textContent = `${specifications.length} ${specifications.length === 1 ? 'detail' : 'details'}`;
            specSection.classList.remove('hidden');
        } else {
            specGrid.innerHTML = '';
            specSection.classList.add('hidden');
        }

        const variantSection = document.getElementById('viewProductVariantsSection');
        const variantBody = document.getElementById('viewProductVariants');
        const variantCount = document.getElementById('viewProductVariantCount');

        if (variants.length) {
            variantBody.innerHTML = variants.map((variant) => {
                const variantStock = Number(variant?.stock || 0);
                const threshold = Number(product.low_stock_threshold ?? 5);
                const stockClass = variantStock <= 0
                    ? 'text-[#b65e5e]'
                    : (variantStock <= threshold ? 'text-[#b87912]' : 'text-[#4f7d61]');

                return `
                    <tr>
                        <td class="px-5 py-3.5 font-semibold text-[#51483f]">
                            ${escapeHtml(formatProductVariantOptions(variant?.options))}
                        </td>
                        <td class="px-4 py-3.5">
                            ${variant?.image_url
                                ? `<img src="${escapeHtml(variant.image_url)}" alt="Variation image" class="h-9 w-9 rounded-[8px] border border-[#e6dfd5] object-cover">`
                                : '<span class="text-[#aaa198]">—</span>'}
                        </td>
                        <td class="px-4 py-3.5 text-[#81786e]">
                            ${escapeHtml(variant?.sku || '—')}
                        </td>
                        <td class="px-4 py-3.5 font-bold text-[#a8731f]">
                            ${peso(variant?.price || 0)}
                        </td>
                        <td class="px-4 py-3.5 font-semibold ${stockClass}">
                            ${variantStock.toLocaleString('en-PH')}
                        </td>
                    </tr>
                `;
            }).join('');

            variantCount.textContent = `${variants.length} ${variants.length === 1 ? 'variant' : 'variants'}`;
            variantSection.classList.remove('hidden');
        } else {
            variantBody.innerHTML = '';
            variantSection.classList.add('hidden');
        }

        const image = document.getElementById('viewProductImage');
        const imagePlaceholder = document.getElementById('viewProductImagePlaceholder');

        if (product.image_url) {
            image.src = product.image_url;
            image.alt = `${productName} product image`;
            image.classList.remove('hidden');
            imagePlaceholder.classList.add('hidden');
        } else {
            image.src = '';
            image.alt = '';
            image.classList.add('hidden');
            imagePlaceholder.classList.remove('hidden');
        }

        showProductPageModal(viewProductModal);
    }

    function updateLocalEditSalePreview() {
        if (!editProductSalePreview) return;

        const original = Math.max(0, Number(editProductPriceInput?.value || 0));
        const discount = Math.min(100, Math.max(0, Number(editProductDiscountInput?.value || 0)));
        const sale = discountedProductPrice(original, discount);

        editProductSalePreview.textContent = discount > 0
            ? `Buyer price: ${peso(sale)} · original ${peso(original)}`
            : `Buyer price: ${peso(original)}`;
    }

    function openLocalEditProduct(product) {
        if (!product || !editProductModal || !editProductForm) return;

        editProductForm.action = productsBaseUrl + '/' + encodeURIComponent(product.id);

        document.getElementById('editProductName').value = product.name || '';

        const categorySelect = document.getElementById('editProductCategory');
        const requested = product.category || 'Others';

        if (categorySelect && !Array.from(categorySelect.options).some((option) => option.value === requested)) {
            categorySelect.add(new Option(requested, requested));
        }
        if (categorySelect) categorySelect.value = requested;

        document.getElementById('editProductBrand').value = product.brand || '';
        document.getElementById('editProductSku').value = product.sku || '';
        document.getElementById('editProductPrice').value = Number(product.price || 0);
        document.getElementById('editProductStock').value = Number(product.stock || 0);
        document.getElementById('editProductDiscount').value = Number(product.discount || 0);
        document.getElementById('editProductVoucher').value = product.voucher_code || '';
        document.getElementById('editProductDescription').value = product.description || '';

        const freeShipping = document.getElementById('editProductFreeShipping');
        if (freeShipping) freeShipping.checked = Boolean(product.free_shipping);

        const currentImage = document.getElementById('editCurrentImage');
        const currentImageWrap = document.getElementById('editCurrentImageWrap');

        if (product.image_url) {
            currentImage.src = product.image_url;
            currentImageWrap.classList.remove('hidden');
        } else {
            currentImage.src = '';
            currentImageWrap.classList.add('hidden');
        }

        updateLocalEditSalePreview();
        showProductPageModal(editProductModal);
    }

    editProductPriceInput?.addEventListener('input', updateLocalEditSalePreview, { signal });
    editProductDiscountInput?.addEventListener('input', updateLocalEditSalePreview, { signal });

    document.querySelectorAll('[data-close-view]').forEach((button) => {
        button.addEventListener('click', () => hideProductPageModal(viewProductModal), { signal });
    });

    document.querySelectorAll('[data-close-edit]').forEach((button) => {
        button.addEventListener('click', () => hideProductPageModal(editProductModal), { signal });
    });

    document.getElementById('viewProductEditButton')?.addEventListener('click', function () {
        if (!activeViewedProduct) return;

        hideProductPageModal(viewProductModal);

        window.setTimeout(function () {
            openLocalEditProduct(activeViewedProduct);
        }, 210);
    }, { signal });

    [viewProductModal, editProductModal].forEach((modalElement) => {
        modalElement?.addEventListener('click', (event) => {
            if (event.target === modalElement) hideProductPageModal(modalElement);
        }, { signal });
    });

    document.addEventListener('click', function (event) {
        const viewButton = event.target.closest('[data-page-view-product]');
        if (viewButton) {
            openLocalViewProduct(getProductById(viewButton.dataset.id));
            return;
        }

        const editButton = event.target.closest('[data-page-edit-product]');
        if (editButton) {
            openLocalEditProduct(getProductById(editButton.dataset.id));
        }
    }, { signal });

    /*
    | Keep Update inside Product Management even if the existing controller
    | returns a Dashboard redirect after success.
    */
    editProductForm?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const submit = editProductForm.querySelector('button[type="submit"]');
        const originalHtml = submit?.innerHTML || '';

        if (submit) {
            submit.disabled = true;
            submit.textContent = 'Saving...';
        }

        try {
            const response = await fetch(editProductForm.action, {
                method: 'POST',
                body: new FormData(editProductForm),
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                redirect: 'manual',
                signal,
            });

            if (response.status === 422) {
                const payload = await response.json().catch(() => ({}));
                const firstError = Object.values(payload?.errors || {}).flat().find(Boolean);
                throw new Error(firstError || payload?.message || 'Please check the product fields.');
            }

            if (!(response.ok || response.type === 'opaqueredirect' || (response.status >= 300 && response.status < 400))) {
                const payload = await response.json().catch(() => ({}));
                throw new Error(payload?.message || 'Unable to update this product.');
            }

            hideProductPageModal(editProductModal);
            await loadProducts();

            const notice = document.getElementById('productsClientNotice');
            if (notice) {
                notice.textContent = 'Product updated successfully.';
                notice.classList.remove('hidden');
                window.setTimeout(() => notice.classList.add('hidden'), 5000);
            }
        } catch (error) {
            if (error?.name !== 'AbortError') {
                window.alert(error?.message || 'Unable to update this product.');
            }
        } finally {
            if (submit) {
                submit.disabled = false;
                submit.innerHTML = originalHtml;
            }
        }
    });


    function closeActionModal() {
        actionModal?.classList.add('hidden');
        actionModal?.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    function openActionModal(button) {
        const action = String(button.dataset.pageProductAction || 'archive');
        const id = String(button.dataset.id || '');
        const name = String(button.dataset.name || 'Product');
        if (!actionForm || !id) return;

        actionForm.action = productsBaseUrl + '/' + encodeURIComponent(id) + '/' + action;

        if (action === 'delete') {
            actionTitle.textContent = 'Remove Product?';
            actionText.textContent = `“${name}” will leave your active catalog and move to Archived Products. Compliance history is preserved.`;
            actionSubmit.textContent = 'Move to Archive';
            actionSubmit.className = 'h-10 rounded-xl bg-[#a96565] px-5 text-[9px] font-semibold text-white hover:bg-[#955757]';
        } else {
            actionTitle.textContent = 'Archive Product?';
            actionText.textContent = `“${name}” will move to Archived Products and can be restored later.`;
            actionSubmit.textContent = 'Archive Product';
            actionSubmit.className = 'h-10 rounded-xl bg-[#c99128] px-5 text-[9px] font-semibold text-white hover:bg-[#b47e1e]';
        }

        actionModal.classList.remove('hidden');
        actionModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    document.addEventListener('click', function (event) {
        const actionButton = event.target.closest('[data-page-product-action]');
        if (actionButton) openActionModal(actionButton);
    }, { signal });


    actionForm?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const originalText = actionSubmit?.textContent || '';
        if (actionSubmit) {
            actionSubmit.disabled = true;
            actionSubmit.textContent = 'Processing...';
        }

        try {
            const response = await fetch(actionForm.action, {
                method: 'POST',
                body: new FormData(actionForm),
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                redirect: 'manual',
                signal,
            });

            if (response.status === 422) {
                const payload = await response.json().catch(() => ({}));
                const firstError = Object.values(payload?.errors || {}).flat().find(Boolean);
                throw new Error(firstError || payload?.message || 'Unable to process this product action.');
            }

            if (!(response.ok || response.type === 'opaqueredirect' || (response.status >= 300 && response.status < 400))) {
                const payload = await response.json().catch(() => ({}));
                throw new Error(payload?.message || 'Unable to process this product action.');
            }

            closeActionModal();
            await loadProducts();

            const notice = document.getElementById('productsClientNotice');
            if (notice) {
                notice.textContent = 'Product catalog updated successfully.';
                notice.classList.remove('hidden');
                window.setTimeout(() => notice.classList.add('hidden'), 5000);
            }
        } catch (error) {
            if (error?.name !== 'AbortError') {
                window.alert(error?.message || 'Unable to process this product action.');
            }
        } finally {
            if (actionSubmit) {
                actionSubmit.disabled = false;
                actionSubmit.textContent = originalText;
            }
        }
    }, { signal });

    actionCancel?.addEventListener('click', closeActionModal, { signal });
    actionModal?.addEventListener('click', function (event) {
        if (event.target === actionModal) closeActionModal();
    }, { signal });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        closeActionModal();
        hideProductPageModal(viewProductModal);
        hideProductPageModal(editProductModal);
    }, { signal });

    document.addEventListener('livewire:navigating', function () {
        pageAbort.abort();
    }, { once: true });


    /*
    |--------------------------------------------------------------------------
    | LOCAL ADD PRODUCT EXPERIENCE
    |--------------------------------------------------------------------------
    | This is the same flexible Add Product builder previously hosted on the
    | Dashboard, but it is mounted directly inside Product Management.
    */
    {
        function showSmoothSellerModal(element) {
            if (!element) return;

            element.classList.remove('hidden');
            element.classList.add('flex');
            document.body.classList.add('overflow-hidden');

            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
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
                document.body.classList.remove('overflow-hidden');
            }, 220);
        }

    const modal = document.getElementById('sellerAddProductModal');

    const addProductProgressSteps = Array.from(
        document.querySelectorAll('[data-add-progress-step]')
    );
    const addProductProgressFill = document.getElementById('sellerAddProgressFill');
    const addProductProgressLabel = document.getElementById('sellerAddProgressLabel');

    function addProductProgressTarget(step) {
        if (step === 1) {
            return document.getElementById('sellerAddBasics');
        }

        if (step === 2) {
            return document.getElementById('sellerAddMedia');
        }

        if (step === 3) {
            const variantTarget = document.getElementById('sellerVariantSection');
            const simpleTarget = document.getElementById('sellerSimpleInventorySection');

            return variantTarget && !variantTarget.classList.contains('hidden')
                ? variantTarget
                : simpleTarget;
        }

        if (step === 4) {
            return document.getElementById('sellerAddDelivery');
        }

        return null;
    }

    function setAddProductProgress(step) {
        const normalized = Math.min(4, Math.max(1, Number(step || 1)));

        addProductProgressSteps.forEach(function (button) {
            const buttonStep = Number(button.dataset.addProgressStep || 0);
            button.classList.toggle('is-active', buttonStep === normalized);
            button.classList.toggle('is-complete', buttonStep < normalized);

            const circle = button.querySelector('.seller-add-progress-circle');
            if (circle) {
                circle.textContent = buttonStep < normalized ? '✓' : String(buttonStep);
            }
        });

        if (addProductProgressFill) {
            addProductProgressFill.style.width = `${((normalized - 1) / 3) * 100}%`;
        }

        if (addProductProgressLabel) {
            addProductProgressLabel.textContent = `Step ${normalized} of 4`;
        }
    }

    function updateAddProductProgressFromScroll() {
        const formScroller = document.getElementById('sellerAddProductForm');
        if (!formScroller) return;

        const formRect = formScroller.getBoundingClientRect();
        const markerY = formRect.top + Math.min(210, formRect.height * 0.28);

        let activeStep = 1;

        [1, 2, 3, 4].forEach(function (step) {
            const target = addProductProgressTarget(step);
            if (!target || target.classList.contains('hidden')) return;

            const rect = target.getBoundingClientRect();
            if (rect.top <= markerY) {
                activeStep = step;
            }
        });

        setAddProductProgress(activeStep);
    }

    addProductProgressSteps.forEach(function (button) {
        button.addEventListener('click', function () {
            const step = Number(this.dataset.addProgressStep || 1);
            const target = addProductProgressTarget(step);

            if (!target) return;

            if (target.tagName === 'DETAILS') {
                target.open = true;
            }

            setAddProductProgress(step);

            target.scrollIntoView({
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
                block: 'start',
            });
        }, { signal });
    });

    document.getElementById('sellerAddProductForm')?.addEventListener(
        'scroll',
        updateAddProductProgressFromScroll,
        { passive: true, signal }
    );




    const openButtons = [
        document.getElementById('openAddProductModal'),
        document.getElementById('openAddProductModalSecondary'),
        document.getElementById('openAddProductModalInventory'),
        document.getElementById('openAddProductModalQuick')
    ].filter(Boolean);

    const closeButton = document.getElementById('closeAddProductModal');
    const cancelButton = document.getElementById('cancelAddProductModal');

    const searchInput = document.getElementById('sellerProductSearch');
    const statusFilter = document.getElementById('sellerProductStatus');
    const emptyState = document.getElementById('sellerProductEmpty');

    const imageInput = document.getElementById('sellerNewProductImage');
    const imagePreview = document.getElementById('sellerImagePreview');
    const imagePlaceholder = document.getElementById('sellerImagePlaceholder');
    const galleryInput = document.getElementById('sellerGalleryImages');
    const galleryPreview = document.getElementById('sellerGalleryPreview');
    const galleryEmpty = document.getElementById('sellerGalleryEmpty');
    let restoredDraftGallery = [];


    function openModal() {
        showSmoothSellerModal(modal);
        window.setTimeout(function () {
            setAddProductProgress(1);
            updateAddProductProgressFromScroll();
        }, 80);
    }


    function closeModal() {
        hideSmoothSellerModal(modal);
    }


    openButtons.forEach(function (button) {
        button.addEventListener('click', openModal);
    });


    closeButton?.addEventListener('click', closeModal);
    cancelButton?.addEventListener('click', closeModal);


    modal?.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });


    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            if (typeof categoryDropdownPanel !== 'undefined' && categoryDropdownPanel && !categoryDropdownPanel.classList.contains('hidden')) {
                closeCategoryDropdown();
                return;
            }
            closeModal();
        }
    }, { signal: signal });


    /*
    |--------------------------------------------------------------------------
    | IMAGE PREVIEW
    |--------------------------------------------------------------------------
    */

    imageInput?.addEventListener('change', function () {

        const file = this.files?.[0];

        if (!file) {
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {

            if (!imagePreview) {
                return;
            }

            imagePreview.src = event.target.result;

            imagePreview.classList.remove('hidden');
            imagePlaceholder?.classList.add('hidden');

        };

        reader.readAsDataURL(file);

    });




    function setGalleryFiles(files) {
        if (!galleryInput || typeof DataTransfer === 'undefined') return;
        const transfer = new DataTransfer();
        Array.from(files || []).forEach(file => transfer.items.add(file));
        galleryInput.files = transfer.files;
    }

    function renderGalleryPreview(remoteImages = null) {
        if (!galleryPreview) return;

        const localFiles = Array.from(galleryInput?.files || []);
        const remote = Array.isArray(remoteImages) ? remoteImages : restoredDraftGallery;
        const galleryCount = document.getElementById('sellerGalleryCount');
        const visibleCount = localFiles.length || remote.length;

        if (galleryCount) {
            galleryCount.textContent = `${visibleCount} image${visibleCount === 1 ? '' : 's'}`;
        }

        galleryPreview.innerHTML = '';

        if (!localFiles.length && !remote.length) {
            const empty = document.createElement('div');
            empty.id = 'sellerGalleryEmpty';
            empty.className = 'col-span-full rounded-[13px] border border-dashed border-[#ddd5ca] bg-[#fcfbf8] px-4 py-4 text-center text-[7.5px] text-[#9a9187]';
            empty.textContent = 'No gallery images selected.';
            galleryPreview.appendChild(empty);
            return;
        }

        if (localFiles.length) {
            localFiles.forEach(function (file, index) {
                const card = document.createElement('div');
                card.className = 'relative overflow-hidden rounded-[12px] border border-[#e4ddd3] bg-[#faf8f5]';
                const url = URL.createObjectURL(file);
                card.innerHTML = `
                    <img src="${url}" alt="Gallery preview ${index + 1}" class="aspect-square w-full object-cover">
                    <div class="absolute inset-x-1.5 bottom-1.5 flex items-center justify-between gap-1">
                        <button type="button" data-set-cover-gallery="${index}" class="rounded-full bg-white/95 px-2 py-1 text-[5.8px] font-bold text-[#8d6112] shadow-sm">Set cover</button>
                    </div>
                    <button type="button" data-remove-gallery="${index}" class="absolute right-1.5 top-1.5 grid h-6 w-6 place-items-center rounded-full bg-white/95 text-[#a65e5e] shadow-sm" aria-label="Remove gallery image">×</button>
                `;
                card.querySelector('[data-set-cover-gallery]')?.addEventListener('click', function () {
                    if (!imageInput || typeof DataTransfer === 'undefined') return;
                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    imageInput.files = transfer.files;
                    if (imagePreview) {
                        imagePreview.src = url;
                        imagePreview.classList.remove('hidden');
                        imagePlaceholder?.classList.add('hidden');
                    }
                    showAddProductNotice('Gallery image set as the cover image.');
                    window.setTimeout(() => addProductNotice?.classList.add('hidden'), 2600);
                });
                card.querySelector('[data-remove-gallery]')?.addEventListener('click', function () {
                    const next = Array.from(galleryInput.files || []).filter((_, fileIndex) => fileIndex !== index);
                    setGalleryFiles(next);
                    renderGalleryPreview([]);
                });
                galleryPreview.appendChild(card);
            });
            return;
        }

        remote.forEach(function (image, index) {
            const card = document.createElement('div');
            card.className = 'relative overflow-hidden rounded-[12px] border border-[#e4ddd3] bg-[#faf8f5]';
            card.innerHTML = `
                <img src="${escapeHtml(image.url || '')}" alt="Saved draft gallery ${index + 1}" class="aspect-square w-full object-cover">
                <span class="absolute bottom-1.5 left-1.5 rounded-full bg-white/95 px-2 py-1 text-[6px] font-semibold text-[#756b60] shadow-sm">Saved draft</span>
            `;
            galleryPreview.appendChild(card);
        });
    }

    galleryInput?.addEventListener('change', function () {
        restoredDraftGallery = [];
        renderGalleryPreview([]);
    });

    /*
    |--------------------------------------------------------------------------
    | FLEXIBLE PRODUCT BUILDER
    |--------------------------------------------------------------------------
    */
    const hasVariantsInput = document.getElementById('sellerHasVariants');
    const simpleInventorySection = document.getElementById('sellerSimpleInventorySection');
    const variantSection = document.getElementById('sellerVariantSection');
    const typeRadios = Array.from(document.querySelectorAll('input[name="product_type"]'));
    const enableVariantsButton = document.getElementById('sellerEnableVariants');
    const useSinglePriceButton = document.getElementById('sellerUseSinglePrice');
    const typeCards = Array.from(document.querySelectorAll('[data-product-type-card]'));
    const categorySelect = document.getElementById('sellerNewProductCategory');
    const categoryDropdown = document.getElementById('sellerCategoryDropdown');
    const categoryDropdownButton = document.getElementById('sellerCategoryDropdownButton');
    const categoryDropdownPanel = document.getElementById('sellerCategoryDropdownPanel');
    const categoryDropdownLabel = document.getElementById('sellerCategoryDropdownLabel');
    const categoryChevron = document.getElementById('sellerCategoryChevron');
    const categorySearch = document.getElementById('sellerCategorySearch');
    const categoryOptions = Array.from(document.querySelectorAll('[data-category-option]'));
    const categoryEmpty = document.getElementById('sellerCategoryEmpty');
    const customCategoryWrap = document.getElementById('sellerCustomCategoryWrap');
    const customCategoryInput = document.getElementById('sellerCustomCategory');
    const categoryHint = document.getElementById('sellerCategoryHint');
    const suggestedSpecifications = document.getElementById('sellerSuggestedSpecifications');
    const specificationsList = document.getElementById('sellerSpecificationsList');
    const specificationEmpty = document.getElementById('sellerSpecificationEmpty');
    const addSpecificationButton = document.getElementById('sellerAddSpecification');
    const variantOptionsContainer = document.getElementById('sellerVariantOptions');
    const addVariantOptionButton = document.getElementById('sellerAddVariantOption');
    const generateVariantsButton = document.getElementById('sellerGenerateVariants');
    const variantTableWrap = document.getElementById('sellerVariantTableWrap');
    const variantRows = document.getElementById('sellerVariantRows');
    const variantSummary = document.getElementById('sellerVariantSummary');
    const applyVariantDefaults = document.getElementById('sellerApplyVariantDefaults');
    const addManualVariantButton = document.getElementById('sellerAddManualVariant');
    const manualVariantEmpty = document.getElementById('sellerManualVariantEmpty');
    const colorValueInput = document.getElementById('sellerColorValueInput');
    const addColorValueButton = document.getElementById('sellerAddColorValue');
    const colorChips = document.getElementById('sellerColorChips');
    const colorEmpty = document.getElementById('sellerColorEmpty');
    const colorStepCard = document.getElementById('sellerColorStepCard');
    const sizeValueInput = document.getElementById('sellerSizeValueInput');
    const addSizeValueButton = document.getElementById('sellerAddSizeValue');
    const sizeChips = document.getElementById('sellerSizeChips');
    const sizeEmpty = document.getElementById('sellerSizeEmpty');
    const sizeStepCard = document.getElementById('sellerSizeStepCard');
    const sizeStepNumber = document.getElementById('sellerSizeStepNumber');
    const sizeGuide = document.getElementById('sellerSizeGuide');
    const quickVariantStatus = document.getElementById('sellerQuickVariantStatus');
    const toggleAdvancedVariants = document.getElementById('sellerToggleAdvancedVariants');
    const advancedVariantWrap = document.getElementById('sellerAdvancedVariantWrap');
    const advancedVariantChevron = document.getElementById('sellerAdvancedVariantChevron');
    const addProductForm = document.getElementById('sellerAddProductForm');
    const simplePriceInput = document.getElementById('sellerNewProductPrice');
    const simpleStockInput = document.getElementById('sellerNewProductStock');
    const simpleDiscountInput = document.getElementById('sellerNewProductDiscount');
    const freeShippingInput = document.getElementById('sellerNewProductFreeShipping');
    const promotionSalePrice = document.getElementById('sellerPromotionSalePrice');
    const promotionOriginalPrice = document.getElementById('sellerPromotionOriginalPrice');
    const promotionPreviewHint = document.getElementById('sellerPromotionPreviewHint');

    function sellerDiscountedPrice(basePrice, discountPercent) {
        const base = Math.max(0, Number(basePrice || 0));
        const discount = Math.min(100, Math.max(0, Number(discountPercent || 0)));

        return Math.round((discount > 0 ? base * (1 - discount / 100) : base) * 100) / 100;
    }

    function updateSellerPromotionPreview() {
        if (!promotionSalePrice || !promotionOriginalPrice || !promotionPreviewHint) return;

        const variantMode = hasVariantsInput?.value === '1';
        const basePrice = Number(simplePriceInput?.value || 0);
        const discount = Math.min(100, Math.max(0, Number(simpleDiscountInput?.value || 0)));

        if (variantMode) {
            promotionSalePrice.textContent = discount > 0 ? `${discount}% OFF` : 'No discount';
            promotionOriginalPrice.classList.add('hidden');
            promotionPreviewHint.textContent = discount > 0
                ? 'This discount will be deducted automatically from every variant price shown to buyers.'
                : 'Variant prices will be shown at their original values.';
            return;
        }

        const finalPrice = sellerDiscountedPrice(basePrice, discount);
        promotionSalePrice.textContent = '₱' + finalPrice.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        if (discount > 0 && basePrice > 0) {
            promotionOriginalPrice.textContent = '₱' + basePrice.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            promotionOriginalPrice.classList.remove('hidden');
            promotionPreviewHint.textContent = `${discount}% will be deducted automatically from the original price.`;
        } else {
            promotionOriginalPrice.classList.add('hidden');
            promotionPreviewHint.textContent = basePrice > 0
                ? 'No sale discount applied.'
                : 'Enter a price and discount to preview the buyer price.';
        }
    }

    simplePriceInput?.addEventListener('input', updateSellerPromotionPreview);
    simpleDiscountInput?.addEventListener('input', updateSellerPromotionPreview);

    const categoryBlueprints = {
        'Electronics': {
            hint: 'Suggested: Brand, Model, Storage, RAM, Color, Warranty.',
            specs: ['Model', 'Warranty', 'Processor', 'Screen Size', 'Battery Capacity']
        },
        'Fashion & Apparel': {
            hint: 'Suggested: Material, Gender, Fit, Size, Color, Pattern.',
            specs: ['Material', 'Gender', 'Fit', 'Pattern', 'Care Instructions']
        },
        'Shoes & Footwear': {
            hint: 'Suggested: Brand, Shoe Size, Color, Material, Gender.',
            specs: ['Material', 'Gender', 'Sole Material', 'Closure Type', 'Country of Origin']
        },
        'Home & Living': {
            hint: 'Suggested: Material, Dimensions, Color, Weight, Capacity.',
            specs: ['Material', 'Width', 'Height', 'Depth', 'Weight']
        },
        'Furniture & Office': {
            hint: 'Suggested: Material, Width, Height, Depth, Weight Capacity.',
            specs: ['Material', 'Width', 'Height', 'Depth', 'Weight Capacity']
        },
        'Beauty & Personal Care': {
            hint: 'Suggested: Skin Type, Shade, Volume, Ingredients, Expiry.',
            specs: ['Skin Type', 'Volume', 'Ingredients', 'Country of Origin', 'Expiry Information']
        },
        'Books & Stationery': {
            hint: 'Suggested: Author, ISBN, Format, Language, Publisher.',
            specs: ['Author', 'ISBN', 'Format', 'Language', 'Publisher']
        },
        'Food & Gourmet': {
            hint: 'Suggested: Flavor, Net Weight, Ingredients, Expiry, Storage.',
            specs: ['Net Weight', 'Ingredients', 'Expiry Information', 'Storage Instructions', 'Country of Origin']
        },
        'Jewelry & Watches': {
            hint: 'Suggested: Material, Gemstone, Length, Movement, Water Resistance.',
            specs: ['Material', 'Gemstone', 'Length', 'Movement', 'Water Resistance']
        },
        'Sports & Outdoors': {
            hint: 'Suggested: Material, Size, Weight, Capacity, Recommended Use.',
            specs: ['Material', 'Weight', 'Capacity', 'Recommended Use', 'Warranty']
        },
        'Automotive & Motorcycle': {
            hint: 'Suggested: Compatible Model, Part Number, Material, Dimensions.',
            specs: ['Compatible Model', 'Part Number', 'Material', 'Dimensions', 'Warranty']
        },
        'Baby & Kids': {
            hint: 'Suggested: Recommended Age, Material, Size, Safety Information.',
            specs: ['Recommended Age', 'Material', 'Dimensions', 'Safety Information', 'Country of Origin']
        },
        'Pet Supplies': {
            hint: 'Suggested: Pet Type, Size, Material, Weight, Flavor.',
            specs: ['Pet Type', 'Material', 'Net Weight', 'Recommended Age', 'Care Instructions']
        },
        'Health & Wellness': {
            hint: 'Suggested: Size, Material, Intended Use, Package Quantity.',
            specs: ['Material', 'Intended Use', 'Package Quantity', 'Country of Origin', 'Storage Instructions']
        },
        'Toys & Collectibles': {
            hint: 'Suggested: Recommended Age, Material, Scale, Edition.',
            specs: ['Recommended Age', 'Material', 'Scale', 'Edition', 'Dimensions']
        },
        'Appliances': {
            hint: 'Suggested: Model, Power, Voltage, Capacity, Warranty.',
            specs: ['Model', 'Power', 'Voltage', 'Capacity', 'Warranty']
        },
        'Others': {
            hint: 'Add any specification that accurately describes this product.',
            specs: ['Material', 'Model', 'Dimensions', 'Weight', 'Country of Origin']
        }
    };

    const commonVariantOptions = [
        'Color', 'Size', 'Design', 'Scent', 'Shoe Size', 'Storage', 'RAM',
        'Style', 'Material', 'Flavor', 'Length', 'Capacity', 'Pack Size', 'Custom'
    ];

    function setProductType(type) {
        const variantMode = type === 'variant';

        if (hasVariantsInput) {
            hasVariantsInput.value = variantMode ? '1' : '0';
        }

        simpleInventorySection?.classList.toggle('hidden', variantMode);
        variantSection?.classList.toggle('hidden', !variantMode);

        if (simplePriceInput) {
            simplePriceInput.disabled = variantMode;
            simplePriceInput.required = !variantMode;
        }

        if (simpleStockInput) {
            simpleStockInput.disabled = variantMode;
            simpleStockInput.required = !variantMode;
        }

        typeCards.forEach(function (card) {
            const active = card.dataset.productTypeCard === type;
            const check = card.querySelector(`[data-product-type-check="${card.dataset.productTypeCard}"]`);

            card.classList.toggle('border-[#d5a64c]', active);
            card.classList.toggle('bg-[#fffaf0]', active);
            card.classList.toggle('shadow-[0_7px_18px_rgba(183,127,20,.055)]', active);
            card.classList.toggle('border-[#e5ded4]', !active);
            card.classList.toggle('bg-white', !active);
            card.classList.toggle('shadow-none', !active);

            if (check) {
                check.classList.toggle('hidden', !active);
                check.classList.toggle('grid', active);
                check.classList.toggle('bg-[#d48f08]', active);
            }
        });

        if (variantMode) {
            window.setTimeout(function () {
                variantSection?.scrollIntoView({
                    behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
                    block: 'nearest',
                });
            }, 80);
        }

        updateSellerPromotionPreview();
    }

    typeRadios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (this.checked) setProductType(this.value);
        });
    });

    enableVariantsButton?.addEventListener('click', function () {
        const variantRadio = typeRadios.find((radio) => radio.value === 'variant');

        if (variantRadio) {
            variantRadio.checked = true;
            setProductType('variant');
        }

        window.setTimeout(function () {
            variantSection?.scrollIntoView({
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
                block: 'start',
            });
        }, 80);
    });

    useSinglePriceButton?.addEventListener('click', function () {
        const simpleRadio = typeRadios.find((radio) => radio.value === 'simple');

        if (simpleRadio) {
            simpleRadio.checked = true;
            setProductType('simple');
        }

        window.setTimeout(function () {
            simpleInventorySection?.scrollIntoView({
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
                block: 'start',
            });
        }, 80);
    });

    function unitOptions(selected = '') {
        const units = ['', 'mm', 'cm', 'm', 'in', 'g', 'kg', 'ml', 'L', 'GB', 'TB', 'pcs', 'days', 'years'];
        return units.map(function (unit) {
            const label = unit || 'No unit';
            return `<option value="${escapeHtml(unit)}" ${unit === selected ? 'selected' : ''}>${escapeHtml(label)}</option>`;
        }).join('');
    }

    function reindexSpecifications() {
        const rows = Array.from(specificationsList?.querySelectorAll('[data-specification-row]') || []);

        rows.forEach(function (row, index) {
            row.querySelector('[data-spec-name]')?.setAttribute('name', `specifications[${index}][name]`);
            row.querySelector('[data-spec-value]')?.setAttribute('name', `specifications[${index}][value]`);
            row.querySelector('[data-spec-unit]')?.setAttribute('name', `specifications[${index}][unit]`);
        });

        specificationEmpty?.classList.toggle('hidden', rows.length > 0);
    }

    function addSpecification(name = '', value = '', unit = '') {
        if (!specificationsList) return;

        const existing = Array.from(specificationsList.querySelectorAll('[data-spec-name]'))
            .some(input => input.value.trim().toLowerCase() === String(name).trim().toLowerCase() && name !== '');

        if (existing) {
            specificationsList.querySelectorAll('[data-spec-name]').forEach(function (input) {
                if (input.value.trim().toLowerCase() === String(name).trim().toLowerCase()) {
                    input.closest('[data-specification-row]')?.classList.add('ring-2', 'ring-[#c99128]/20');
                    window.setTimeout(() => input.closest('[data-specification-row]')?.classList.remove('ring-2', 'ring-[#c99128]/20'), 800);
                }
            });
            return;
        }

        const row = document.createElement('div');
        row.dataset.specificationRow = 'true';
        row.className = 'grid grid-cols-1 gap-2 rounded-[14px] border border-[#ebe4da] bg-[#fcfbf8] p-3 sm:grid-cols-[1fr_1.35fr_120px_38px] sm:items-center';
        row.innerHTML = `
            <input data-spec-name type="text" value="${escapeHtml(name)}" placeholder="Specification name" class="h-9 rounded-lg border border-[#e4ddd3] bg-white px-3 text-[8px] text-[#3f3932] outline-none focus:border-[#c99128]">
            <input data-spec-value type="text" value="${escapeHtml(value)}" placeholder="Value" class="h-9 rounded-lg border border-[#e4ddd3] bg-white px-3 text-[8px] text-[#3f3932] outline-none focus:border-[#c99128]">
            <div class="relative">
                <select data-spec-unit class="h-9 w-full appearance-none rounded-lg border border-[#e4ddd3] bg-white pl-3 pr-8 text-[8px] text-[#6d645a] outline-none focus:border-[#c99128]">${unitOptions(unit)}</select>
                <svg viewBox="0 0 24 24" class="pointer-events-none absolute right-2.5 top-1/2 h-3 w-3 -translate-y-1/2 text-[#9b9184]" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 10 5 5 5-5"></path></svg>
            </div>
            <button type="button" data-remove-specification class="grid h-9 w-9 place-items-center rounded-lg border border-[#ead8d8] bg-white text-[#ad6660] transition hover:bg-[#fff4f4]" aria-label="Remove specification">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M5 7h14"></path><path d="M9 7V5h6v2"></path><path d="M8 7l1 12h6l1-12"></path></svg>
            </button>
        `;

        row.querySelector('[data-remove-specification]')?.addEventListener('click', function () {
            row.remove();
            reindexSpecifications();
        });

        specificationsList.appendChild(row);
        reindexSpecifications();

        if (name === '') {
            row.querySelector('[data-spec-name]')?.focus();
        } else {
            row.querySelector('[data-spec-value]')?.focus();
        }
    }

    addSpecificationButton?.addEventListener('click', function () {
        addSpecification();
    });

    function closeCategoryDropdown() {
        categoryDropdownPanel?.classList.add('hidden');
        categoryDropdownButton?.setAttribute('aria-expanded', 'false');
        categoryChevron?.classList.remove('rotate-180');
    }

    function openCategoryDropdown() {
        categoryDropdownPanel?.classList.remove('hidden');
        categoryDropdownButton?.setAttribute('aria-expanded', 'true');
        categoryChevron?.classList.add('rotate-180');
        window.setTimeout(function () { categorySearch?.focus(); }, 30);
    }

    function setCategory(value, closeAfter = true) {
        if (!categorySelect) return;

        categorySelect.value = value || '';
        customCategoryWrap?.classList.toggle('hidden', value !== 'Others');
        if (value !== 'Others' && customCategoryInput) customCategoryInput.value = '';

        if (categoryDropdownLabel) {
            categoryDropdownLabel.textContent = value || 'Choose product category';
            categoryDropdownLabel.classList.toggle('text-[#403930]', Boolean(value));
            categoryDropdownLabel.classList.toggle('text-[#8f877c]', !value);
        }

        categoryOptions.forEach(function (option) {
            const selected = option.dataset.categoryOption === value;
            option.classList.toggle('bg-[#f9fafb]', selected);
            option.classList.toggle('ring-1', selected);
            option.classList.toggle('ring-[#d48f08]', selected);

            const check = option.querySelector('[data-category-check]');
            check?.classList.toggle('hidden', !selected);
            check?.classList.toggle('grid', selected);
            option.setAttribute('aria-selected', selected ? 'true' : 'false');
        });

        categorySelect.dispatchEvent(new Event('change', { bubbles: true }));
        if (closeAfter) closeCategoryDropdown();
    }

    function filterCategoryOptions() {
        const query = (categorySearch?.value || '').trim().toLowerCase();
        let visible = 0;

        categoryOptions.forEach(function (option) {
            const searchable = (option.dataset.categorySearch || option.textContent || '').toLowerCase();
            const matches = query === '' || searchable.includes(query);
            option.classList.toggle('hidden', !matches);
            if (matches) visible++;
        });

        categoryEmpty?.classList.toggle('hidden', visible !== 0);
    }

    categoryDropdownButton?.addEventListener('click', function (event) {
        event.stopPropagation();
        const willOpen = categoryDropdownPanel?.classList.contains('hidden');
        if (willOpen) openCategoryDropdown();
        else closeCategoryDropdown();
    });

    categoryDropdownPanel?.addEventListener('click', function (event) {
        event.stopPropagation();
    });

    categoryOptions.forEach(function (option) {
        option.addEventListener('click', function () {
            setCategory(this.dataset.categoryOption || '');
        });
    });

    categorySearch?.addEventListener('input', filterCategoryOptions);
    document.addEventListener('click', function (event) {
        if (categoryDropdown && !categoryDropdown.contains(event.target)) closeCategoryDropdown();
    }, { signal: signal });

    customCategoryInput?.addEventListener('input', function () {
        const value = this.value.trim();
        if (categoryDropdownLabel && categorySelect?.value === 'Others') {
            categoryDropdownLabel.textContent = value || 'Others';
        }
    });

    function renderCategoryBlueprint() {
        const blueprint = categoryBlueprints[categorySelect?.value] || (categorySelect?.value === 'Others' ? categoryBlueprints['Others'] : null);

        if (categoryHint) {
            categoryHint.textContent = blueprint?.hint || 'Select a category to get suggested specifications.';
        }

        if (!suggestedSpecifications) return;

        suggestedSpecifications.innerHTML = '';

        if (!blueprint) {
            suggestedSpecifications.classList.add('hidden');
            suggestedSpecifications.classList.remove('flex');
            return;
        }

        blueprint.specs.forEach(function (name) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'rounded-full border border-[#d1d5db] bg-white px-2.5 py-1.5 text-[7px] font-semibold text-[#4b5563] transition hover:border-[#d48f08] hover:text-[#a8731f]';
            button.textContent = '+ ' + name;
            button.addEventListener('click', function () { addSpecification(name); });
            suggestedSpecifications.appendChild(button);
        });

        suggestedSpecifications.classList.remove('hidden');
        suggestedSpecifications.classList.add('flex');
    }

    categorySelect?.addEventListener('change', renderCategoryBlueprint);

    if (categorySelect?.value) {
        setCategory(categorySelect.value, false);
    } else {
        renderCategoryBlueprint();
    }

    const quickVariantValues = {
        Color: [],
        Size: [],
    };

    function optionSelectMarkup(selected = 'Storage') {
        return commonVariantOptions
            .filter(option => !['Color', 'Size'].includes(option))
            .map(function (option) {
                return `<option value="${escapeHtml(option)}" ${option === selected ? 'selected' : ''}>${escapeHtml(option)}</option>`;
            }).join('');
    }

    function uniqueVariantValues(raw) {
        return String(raw || '')
            .split(',')
            .map(value => value.trim())
            .filter(Boolean);
    }

    function renderQuickVariantChips(type) {
        const values = quickVariantValues[type];
        const container = type === 'Color' ? colorChips : sizeChips;
        const empty = type === 'Color' ? colorEmpty : sizeEmpty;

        if (!container) return;
        container.innerHTML = '';

        values.forEach(function (value) {
            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-1.5 rounded-full border border-[#ead6ad] bg-[#fffaf0] px-2.5 py-1.5 text-[7.5px] font-semibold text-[#735a2b]';
            chip.innerHTML = `
                <span>${escapeHtml(value)}</span>
                <button type="button" aria-label="Remove ${escapeHtml(value)}" class="grid h-4 w-4 place-items-center rounded-full text-[#a8731f] transition hover:bg-[#f4e3bd]">
                    <svg viewBox="0 0 24 24" class="h-2.5 w-2.5" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m7 7 10 10"></path><path d="m17 7-10 10"></path></svg>
                </button>
            `;
            chip.querySelector('button')?.addEventListener('click', function () {
                quickVariantValues[type] = quickVariantValues[type].filter(item => item !== value);
                renderQuickVariantChips(type);
                updateGuidedVariantFlow(true);
            });
            container.appendChild(chip);
        });

        empty?.classList.toggle('hidden', values.length > 0);
    }

    function addQuickVariantValue(type, rawValue, moveForward = true) {
        const values = uniqueVariantValues(rawValue);
        if (!values.length) return;

        values.forEach(function (value) {
            const exists = quickVariantValues[type].some(item => item.toLowerCase() === value.toLowerCase());
            if (!exists) quickVariantValues[type].push(value);
        });

        if (type === 'Color' && colorValueInput) colorValueInput.value = '';
        if (type === 'Size' && sizeValueInput) sizeValueInput.value = '';

        renderQuickVariantChips(type);
        updateGuidedVariantFlow(true);

        window.setTimeout(function () {
            (type === 'Color' ? colorValueInput : sizeValueInput)?.focus();
        }, 50);
    }

    function bindQuickVariantInput(input, button, type) {
        button?.addEventListener('click', function () {
            addQuickVariantValue(type, input?.value || '');
        });

        input?.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ',') {
                event.preventDefault();
                addQuickVariantValue(type, this.value || '');
            }
        });

        input?.addEventListener('blur', function () {
            if (this.value.trim()) addQuickVariantValue(type, this.value, false);
        });
    }

    bindQuickVariantInput(colorValueInput, addColorValueButton, 'Color');
    bindQuickVariantInput(sizeValueInput, addSizeValueButton, 'Size');

    function updateGuidedVariantFlow(autoGenerate = true) {
        const hasColor = quickVariantValues.Color.length > 0;
        const hasSize = quickVariantValues.Size.length > 0;
        const definitions = getVariantDefinitions();
        const hasAnyVariation = definitions.length > 0;

        if (sizeValueInput) {
            sizeValueInput.disabled = false;
            sizeValueInput.placeholder = 'Example: S, M, L, XL';
        }

        if (addSizeValueButton) {
            addSizeValueButton.disabled = false;
        }

        colorStepCard?.classList.toggle('border-[#d8b66a]', hasColor);
        sizeStepCard?.classList.toggle('border-[#d8b66a]', hasSize);

        if (sizeGuide) {
            sizeGuide.textContent = 'Optional · useful for apparel, shoes, furniture dimensions, or pack sizes.';
        }

        if (quickVariantStatus) {
            if (!hasAnyVariation) {
                quickVariantStatus.textContent = 'Add Color, Size, Design, Scent, Storage, or another option. One option is enough.';
            } else {
                const counts = definitions.map((definition) => `${definition.values.length} ${definition.name}`).join(' × ');
                const combinationCount = cartesianProduct(definitions).length;

                quickVariantStatus.textContent =
                    `${counts} = ${combinationCount} variation${combinationCount === 1 ? '' : 's'}. Set price and stock below.`;
            }
        }

        if (autoGenerate) {
            if (hasAnyVariation) {
                generateVariants(true);
            } else {
                clearGeneratedVariants('Add at least one variation option.');
            }
        }
    }

    function addVariantOption(selectedName = 'Storage', values = '') {
        if (!variantOptionsContainer) return;

        if (variantOptionsContainer.children.length >= 3) {
            if (variantSummary) {
                variantSummary.textContent = 'Maximum of 3 advanced option groups per product.';
                variantSummary.classList.add('text-[#ad6262]');
            }
            return;
        }

        const row = document.createElement('div');
        row.dataset.variantOptionRow = 'true';
        row.className = 'grid grid-cols-1 gap-2 rounded-[14px] border border-[#e5e7eb] bg-white p-3 md:grid-cols-[180px_1fr_40px] md:items-end';
        row.innerHTML = `
            <div>
                <label class="mb-1.5 block text-[7px] font-bold uppercase tracking-[.07em] text-[#6b7280]">Option Type</label>
                <div class="relative">
                    <select data-variant-option-name class="h-10 w-full appearance-none rounded-xl border border-[#dfe3ea] bg-white pl-3 pr-9 text-[8px] font-semibold text-[#374151] outline-none focus:border-[#d48f08]">${optionSelectMarkup(selectedName)}</select>
                    <svg viewBox="0 0 24 24" class="pointer-events-none absolute right-3 top-1/2 h-3 w-3 -translate-y-1/2 text-[#a8731f]" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 10 5 5 5-5"></path></svg>
                </div>
                <input data-variant-option-custom type="text" placeholder="Custom option name" class="mt-2 hidden h-9 w-full rounded-lg border border-[#dfe3ea] bg-white px-3 text-[8px] text-[#374151] outline-none focus:border-[#d48f08]">
            </div>
            <div>
                <label class="mb-1.5 block text-[7px] font-bold uppercase tracking-[.07em] text-[#6b7280]">Values <span class="normal-case font-medium tracking-normal text-[#adb2bb]">— separate with commas</span></label>
                <input data-variant-option-values type="text" value="${escapeHtml(values)}" placeholder="Example: 128GB, 256GB" class="h-10 w-full rounded-xl border border-[#dfe3ea] bg-white px-3 text-[8px] text-[#434b59] outline-none placeholder:text-[#a4aab4] focus:border-[#d48f08] focus:bg-white">
            </div>
            <button type="button" data-remove-variant-option class="grid h-10 w-10 place-items-center rounded-xl border border-[#eadde0] bg-white text-[#a96a70] transition hover:bg-[#fff6f7]" aria-label="Remove variant option">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M5 7h14"></path><path d="M9 7V5h6v2"></path><path d="M8 7l1 12h6l1-12"></path></svg>
            </button>
        `;

        const select = row.querySelector('[data-variant-option-name]');
        const custom = row.querySelector('[data-variant-option-custom]');

        function syncCustom() {
            const isCustom = select?.value === 'Custom';
            custom?.classList.toggle('hidden', !isCustom);
            if (isCustom) custom?.focus();
        }

        select?.addEventListener('change', function () {
            syncCustom();
            if (getVariantDefinitions().length) generateVariants(true);
        });
        row.querySelector('[data-remove-variant-option]')?.addEventListener('click', function () {
            row.remove();
            if (getVariantDefinitions().length) generateVariants(true);
            else clearGeneratedVariants('Variant options changed.');
        });
        row.querySelector('[data-variant-option-values]')?.addEventListener('input', function () {
            if (getVariantDefinitions().length) generateVariants(true);
            else clearGeneratedVariants('Add values, then refresh combinations.');
        });
        custom?.addEventListener('input', function () {
            if (getVariantDefinitions().length) generateVariants(true);
        });

        variantOptionsContainer.appendChild(row);
        syncCustom();
    }

    addVariantOptionButton?.addEventListener('click', function () {
        addVariantOption('Design', '');
    });

    document.querySelectorAll('[data-variant-preset]').forEach(function (button) {
        button.addEventListener('click', function () {
            const preset = String(this.dataset.variantPreset || '').trim();

            const variantRadio = typeRadios.find((radio) => radio.value === 'variant');
            if (variantRadio) {
                variantRadio.checked = true;
                setProductType('variant');
            }

            if (preset === 'Color') {
                colorValueInput?.focus();
                return;
            }

            if (preset === 'Size') {
                sizeValueInput?.focus();
                return;
            }

            const existingRows = Array.from(
                variantOptionsContainer?.querySelectorAll('[data-variant-option-row]') || []
            );

            const alreadyExists = existingRows.some(function (row) {
                const select = row.querySelector('[data-variant-option-name]');
                const custom = row.querySelector('[data-variant-option-custom]');
                const name = select?.value === 'Custom'
                    ? String(custom?.value || '').trim()
                    : String(select?.value || '').trim();

                return name.toLowerCase() === preset.toLowerCase();
            });

            if (!alreadyExists) {
                addVariantOption(preset, '');
            }

            window.setTimeout(function () {
                const rows = Array.from(
                    variantOptionsContainer?.querySelectorAll('[data-variant-option-row]') || []
                );

                const target = rows.find(function (row) {
                    const select = row.querySelector('[data-variant-option-name]');
                    return String(select?.value || '').toLowerCase() === preset.toLowerCase();
                });

                target?.querySelector('[data-variant-option-values]')?.focus();
            }, 60);
        });
    });

    function clearGeneratedVariants(message = '') {
        if (variantRows) variantRows.innerHTML = '';
        variantTableWrap?.classList.add('hidden');
        if (variantSummary && message) {
            variantSummary.textContent = message;
            variantSummary.classList.remove('text-[#ad6262]');
        }
    }

    function getVariantDefinitions() {
        const definitions = [];

        if (quickVariantValues.Color.length) {
            definitions.push({ name: 'Color', values: [...quickVariantValues.Color] });
        }

        if (quickVariantValues.Size.length) {
            definitions.push({ name: 'Size', values: [...quickVariantValues.Size] });
        }

        const rows = Array.from(variantOptionsContainer?.querySelectorAll('[data-variant-option-row]') || []);

        rows.forEach(function (row) {
            const select = row.querySelector('[data-variant-option-name]');
            const custom = row.querySelector('[data-variant-option-custom]');
            const valuesInput = row.querySelector('[data-variant-option-values]');
            const name = select?.value === 'Custom' ? custom?.value.trim() : select?.value.trim();
            const values = Array.from(new Set(
                String(valuesInput?.value || '')
                    .split(',')
                    .map(value => value.trim())
                    .filter(Boolean)
            ));

            if (name && values.length) {
                definitions.push({ name, values });
            }
        });

        return definitions;
    }

    function cartesianProduct(definitions) {
        return definitions.reduce(function (combinations, definition) {
            const next = [];
            combinations.forEach(function (combination) {
                definition.values.forEach(function (value) {
                    next.push({ ...combination, [definition.name]: value });
                });
            });
            return next;
        }, [{}]);
    }

    function variantSku(index, options) {
        const base = (document.getElementById('sellerNewProductSku')?.value || 'SARI')
            .toUpperCase()
            .replace(/[^A-Z0-9]+/g, '-')
            .replace(/^-|-$/g, '') || 'SARI';
        const suffix = Object.values(options)
            .map(value => String(value).toUpperCase().replace(/[^A-Z0-9]+/g, '').slice(0, 5))
            .filter(Boolean)
            .join('-');
        return `${base}-${suffix || index + 1}`.slice(0, 120);
    }

    function manualVariantSku(index, variantName = '', size = '') {
        const base = (document.getElementById('sellerNewProductSku')?.value || 'SARI')
            .toUpperCase()
            .replace(/[^A-Z0-9]+/g, '-')
            .replace(/^-|-$/g, '') || 'SARI';

        const suffix = [variantName, size]
            .map(value => String(value || '').toUpperCase().replace(/[^A-Z0-9]+/g, '').slice(0, 8))
            .filter(Boolean)
            .join('-');

        return `${base}-${suffix || index + 1}`.slice(0, 120);
    }

    function syncManualVariantRow(row) {
        if (!row) return;

        const variantInput = row.querySelector('[data-manual-variant-name]');
        const sizeInput = row.querySelector('[data-manual-variant-size]');
        const optionsInput = row.querySelector('input[name$="[options]"]');
        const skuInput = row.querySelector('input[name$="[sku]"]');

        const options = {};
        const variantName = String(variantInput?.value || '').trim();
        const size = String(sizeInput?.value || '').trim();

        if (variantName) options.Variant = variantName;
        if (size) options.Size = size;

        if (optionsInput) {
            optionsInput.value = JSON.stringify(options);
        }

        if (skuInput && skuInput.dataset.autoSku === '1') {
            const index = Array.from(variantRows?.querySelectorAll('[data-manual-variant-row]') || []).indexOf(row);
            skuInput.value = manualVariantSku(Math.max(index, 0), variantName, size);
        }
    }

    function reindexManualVariantRows() {
        const rows = Array.from(variantRows?.querySelectorAll('[data-manual-variant-row]') || []);

        rows.forEach(function (row, index) {
            row.dataset.variantIndex = String(index);

            const title = row.querySelector('.seller-manual-variant-card-title');
            if (title) title.textContent = `Variant ${index + 1}`;

            row.querySelectorAll('[data-variant-field]').forEach(function (field) {
                const suffix = String(field.dataset.variantField || '');
                field.name = `variants[${index}][${suffix}]`;
            });

            syncManualVariantRow(row);
        });

        variantTableWrap?.classList.toggle('hidden', rows.length === 0);
        manualVariantEmpty?.classList.toggle('hidden', rows.length > 0);

        if (variantSummary) {
            variantSummary.textContent = rows.length
                ? `${rows.length} variant${rows.length === 1 ? '' : 's'} added. Complete price and stock for each row.`
                : 'Add at least one variant row.';
            variantSummary.classList.remove('text-[#ad6262]');
        }
    }

    function addManualVariantRow(saved = null) {
        if (!variantRows) return null;

        const index = variantRows.querySelectorAll('[data-manual-variant-row]').length;

        let savedOptions = {};
        try {
            savedOptions = typeof saved?.options === 'string'
                ? JSON.parse(saved.options || '{}')
                : (saved?.options || {});
        } catch (_) {
            savedOptions = {};
        }

        const optionEntries = Object.entries(savedOptions || {});
        const primaryValue = String(
            savedOptions.Variant
            ?? savedOptions.Color
            ?? savedOptions.Design
            ?? savedOptions.Scent
            ?? savedOptions.Storage
            ?? savedOptions.Material
            ?? optionEntries.find(([key]) => key !== 'Size')?.[1]
            ?? ''
        );

        const sizeValue = String(savedOptions.Size ?? '');

        const card = document.createElement('div');
        card.dataset.manualVariantRow = 'true';
        card.dataset.variantIndex = String(index);
        card.className = 'seller-manual-variant-card rounded-[16px] border border-[#e7dfd5] bg-[#fffefc] p-4';

        card.innerHTML = `
            <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] pb-3">
                <div>
                    <p class="text-[8.5px] font-bold text-[#403930]">Variant ${index + 1}</p>
                    <p class="mt-0.5 text-[6.8px] text-[#9a9187]">Image, option, size, price, stock, and SKU.</p>
                </div>

                <button
                    type="button"
                    data-remove-manual-variant
                    class="grid h-9 w-9 place-items-center rounded-[10px] border border-[#eadde0] bg-white text-[#a65e64] transition hover:bg-[#fff5f6]"
                    aria-label="Remove variant"
                    title="Remove variant"
                >
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M5 7h14"></path>
                        <path d="M9 7V5h6v2"></path>
                        <path d="M8 7l1 12h6l1-12"></path>
                    </svg>
                </button>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-[130px_minmax(0,1fr)_140px]">
                <div>
                    <label class="mb-2 block text-[8px] font-semibold text-[#51483f]">Image</label>

                    <label class="seller-variant-image-picker flex h-[112px] cursor-pointer flex-col items-center justify-center overflow-hidden rounded-[13px] border border-dashed border-[#d9d1c6] bg-white text-center transition hover:border-[#cda95e] hover:bg-[#fffaf1]">
                        <input
                            data-variant-field="image"
                            data-variant-image-input
                            name="variants[${index}][image]"
                            type="file"
                            accept="image/*"
                            class="hidden"
                        >

                        <span data-variant-image-preview class="grid h-12 w-12 place-items-center overflow-hidden rounded-[10px] bg-[#f4f1ec] text-[#9a9187]">
                            <svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                            </svg>
                        </span>

                        <span data-variant-image-label class="mt-2 text-[7px] font-semibold text-[#80766b]">Choose image</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-[8px] font-semibold text-[#51483f]">Variant / Color / Design / Scent</label>
                        <input
                            data-manual-variant-name
                            type="text"
                            required
                            value="${escapeHtml(primaryValue)}"
                            placeholder="Example: Black, Vanilla, Floral, 128GB"
                            class="h-11 w-full rounded-xl border border-[#d9d2c9] bg-white px-3.5 text-[9px] text-[#45413d] outline-none focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10"
                        >
                        <input data-variant-field="options" type="hidden" name="variants[${index}][options]" value="">
                    </div>

                    <div>
                        <label class="mb-2 block text-[8px] font-semibold text-[#51483f]">Size <span class="font-normal text-[#a2998e]">(optional)</span></label>
                        <input
                            data-manual-variant-size
                            type="text"
                            value="${escapeHtml(sizeValue)}"
                            placeholder="S, M, L, 30ml..."
                            class="h-11 w-full rounded-xl border border-[#d9d2c9] bg-white px-3.5 text-[9px] text-[#45413d] outline-none focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-[8px] font-semibold text-[#51483f]">SKU <span class="font-normal text-[#a2998e]">(optional)</span></label>
                        <input
                            data-variant-field="sku"
                            data-auto-sku="${saved?.sku ? '0' : '1'}"
                            name="variants[${index}][sku]"
                            type="text"
                            value="${escapeHtml(saved?.sku || manualVariantSku(index, primaryValue, sizeValue))}"
                            placeholder="Auto-generated"
                            class="h-11 w-full rounded-xl border border-[#d9d2c9] bg-white px-3.5 text-[8px] text-[#52504c] outline-none focus:border-[#d48f08]"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 md:grid-cols-1">
                    <div>
                        <label class="mb-2 block text-[8px] font-semibold text-[#51483f]">Price</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[8px] font-bold text-[#9b6d1c]">₱</span>
                            <input
                                data-variant-field="price"
                                name="variants[${index}][price]"
                                type="number"
                                min="0"
                                step="0.01"
                                required
                                value="${escapeHtml(saved?.price ?? '')}"
                                placeholder="0.00"
                                class="h-11 w-full rounded-xl border border-[#d9d2c9] bg-white pl-7 pr-3 text-[9px] text-[#45413d] outline-none focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-[8px] font-semibold text-[#51483f]">Stock</label>
                        <input
                            data-variant-field="stock"
                            name="variants[${index}][stock]"
                            type="number"
                            min="0"
                            required
                            value="${escapeHtml(saved?.stock ?? '')}"
                            placeholder="0"
                            class="h-11 w-full rounded-xl border border-[#d9d2c9] bg-white px-3.5 text-[9px] text-[#45413d] outline-none focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10"
                        >
                    </div>
                </div>
            </div>
        `;

        variantRows.appendChild(card);

        const variantNameInput = card.querySelector('[data-manual-variant-name]');
        const sizeInput = card.querySelector('[data-manual-variant-size]');
        const skuInput = card.querySelector('input[name$="[sku]"]');
        const imageInput = card.querySelector('[data-variant-image-input]');
        const imagePreview = card.querySelector('[data-variant-image-preview]');
        const imageLabel = card.querySelector('[data-variant-image-label]');

        [variantNameInput, sizeInput].forEach(function (input) {
            input?.addEventListener('input', function () {
                syncManualVariantRow(card);
            });
        });

        skuInput?.addEventListener('input', function () {
            this.dataset.autoSku = '0';
        });

        imageInput?.addEventListener('change', function () {
            const file = this.files?.[0];
            if (!file || !imagePreview) return;

            const url = URL.createObjectURL(file);
            imagePreview.innerHTML = `<img src="${url}" alt="Variant image" class="h-full w-full object-cover">`;
            if (imageLabel) imageLabel.textContent = 'Change image';
        });

        card.querySelector('[data-remove-manual-variant]')?.addEventListener('click', function () {
            card.remove();
            reindexManualVariantRows();
        });

        syncManualVariantRow(card);
        reindexManualVariantRows();

        return card;
    }


    function generateVariants(silent = false) {
        const existing = variantRows?.querySelectorAll('[data-manual-variant-row]').length || 0;

        if (!existing) {
            const row = addManualVariantRow();

            if (!silent && variantSummary) {
                variantSummary.textContent = 'A variant row was added. Enter the variant name, price, and stock.';
                variantSummary.classList.add('text-[#ad6262]');
            }

            row?.querySelector('[data-manual-variant-name]')?.focus();
        }

        return true;
    }

    addManualVariantButton?.addEventListener('click', function () {
        const row = addManualVariantRow();
        row?.querySelector('[data-manual-variant-name]')?.focus();
    });

    generateVariantsButton?.addEventListener('click', function () { generateVariants(false); });

    applyVariantDefaults?.addEventListener('click', function () {
        const priceInputs = Array.from(variantRows?.querySelectorAll('input[name$="[price]"]') || []);
        const stockInputs = Array.from(variantRows?.querySelectorAll('input[name$="[stock]"]') || []);
        if (!priceInputs.length) return;

        const defaultPrice = priceInputs.find(input => input.value !== '')?.value || '';
        const defaultStock = stockInputs.find(input => input.value !== '')?.value || '';

        priceInputs.forEach(input => { if (input.value === '' && defaultPrice !== '') input.value = defaultPrice; });
        stockInputs.forEach(input => { if (input.value === '' && defaultStock !== '') input.value = defaultStock; });
    });

    addProductForm?.addEventListener('submit', function (event) {
        if (!categorySelect?.value) {
            event.preventDefault();
            openCategoryDropdown();
            categoryDropdownButton?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            categoryDropdownButton?.focus();
            return;
        }

        if (hasVariantsInput?.value !== '1') return;

        const generated = variantRows?.querySelectorAll('[data-manual-variant-row]').length || 0;
        if (generated === 0) {
            event.preventDefault();
            const ok = generateVariants();
            if (!ok) {
                variantSection?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                if (variantSummary) {
                    variantSummary.textContent = 'Variants generated. Enter a price and stock for each row, then submit again.';
                    variantSummary.classList.add('text-[#ad6262]');
                }
                variantSection?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });


    /*
    |--------------------------------------------------------------------------
    | DATABASE-BACKED FULFILLMENT + SERVER DRAFT
    |--------------------------------------------------------------------------
    */
    const productConditionInput = document.getElementById('sellerProductCondition');
    const packageWeightInput = document.getElementById('sellerPackageWeight');
    const packageLengthInput = document.getElementById('sellerPackageLength');
    const packageWidthInput = document.getElementById('sellerPackageWidth');
    const packageHeightInput = document.getElementById('sellerPackageHeight');
    const preparationTimeInput = document.getElementById('sellerPreparationTime');
    const lowStockThresholdInput = document.getElementById('sellerLowStockThreshold');
    const draftIdInput = document.getElementById('sellerProductDraftId');
    const previewButton = document.getElementById('sellerPreviewProduct');

    function normalizeOptionsString(value) {
        try {
            const parsed = typeof value === 'string' ? JSON.parse(value || '{}') : (value || {});
            const ordered = Object.keys(parsed).sort().reduce((carry, key) => {
                carry[key] = parsed[key];
                return carry;
            }, {});
            return JSON.stringify(ordered);
        } catch (_) {
            return String(value || '');
        }
    }

    function restoreDraftPayload(draft) {
        if (!draft || !draft.payload) return false;

        const payload = draft.payload || {};
        if (draftIdInput) draftIdInput.value = draft.id || '';

        const variantMode = String(payload.has_variants || '') === '1'
            || String(payload.product_type || '') === 'variant';
        const type = variantMode ? 'variant' : 'simple';
        const radio = typeRadios.find(item => item.value === type);
        if (radio) radio.checked = true;
        setProductType(type);

        const assign = (id, value) => {
            const el = document.getElementById(id);
            if (el && value !== undefined && value !== null) el.value = value;
        };

        assign('sellerNewProductName', payload.name || '');
        assign('sellerNewProductBrand', payload.brand || '');
        assign('sellerNewProductSku', payload.sku || '');
        assign('sellerNewProductPrice', payload.price ?? '');
        assign('sellerNewProductStock', payload.stock ?? '');
        assign('sellerNewProductDiscount', payload.discount ?? '');
        assign('sellerProductCondition', payload.condition || '');
        assign('sellerPackageWeight', payload.package_weight ?? '');
        assign('sellerPackageLength', payload.package_length ?? '');
        assign('sellerPackageWidth', payload.package_width ?? '');
        assign('sellerPackageHeight', payload.package_height ?? '');
        assign('sellerPreparationTime', payload.preparation_days ?? '');
        assign('sellerLowStockThreshold', payload.low_stock_threshold ?? 5);

        const voucherInput = addProductForm?.querySelector('[name="voucher_code"]');
        const descriptionInput = addProductForm?.querySelector('[name="description"]');
        if (voucherInput) voucherInput.value = payload.voucher_code || '';
        if (descriptionInput) descriptionInput.value = payload.description || '';
        if (freeShippingInput) freeShippingInput.checked = ['1', 1, true, 'true', 'on'].includes(payload.free_shipping);

        setCategory(payload.category || '', false);
        if (customCategoryInput) customCategoryInput.value = payload.custom_category || '';

        if (specificationsList) specificationsList.innerHTML = '';
        (Array.isArray(payload.specifications) ? payload.specifications : []).forEach(function (spec) {
            addSpecification(spec?.name || '', spec?.value || '', spec?.unit || '');
        });

        const savedVariants = Array.isArray(payload.variants) ? payload.variants : [];

        if (variantRows) variantRows.innerHTML = '';

        if (variantMode) {
            savedVariants.forEach(function (variant) {
                addManualVariantRow(variant);
            });

            reindexManualVariantRows();
        } else {
            variantTableWrap?.classList.add('hidden');
            manualVariantEmpty?.classList.remove('hidden');
        }

        if (draft.cover_image_url && imagePreview) {
            imagePreview.src = draft.cover_image_url;
            imagePreview.classList.remove('hidden');
            imagePlaceholder?.classList.add('hidden');
        }

        restoredDraftGallery = Array.isArray(draft.gallery_images) ? draft.gallery_images : [];
        renderGalleryPreview(restoredDraftGallery);
        updateSellerPromotionPreview();
        return true;
    }

    async function loadServerDraft() {
        try {
            const response = await fetch(productDraftUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                cache: 'no-store',
                signal,
            });

            if (!response.ok) return;
            const payload = await response.json();
            if (!payload?.draft) return;

            restoreDraftPayload(payload.draft);
            showAddProductNotice('Your saved server draft was restored. Saved media will be reused when you submit.');
            window.setTimeout(() => addProductNotice?.classList.add('hidden'), 4500);
        } catch (error) {
            if (error?.name !== 'AbortError') console.debug('No product draft restored.', error);
        }
    }



    /*
    |--------------------------------------------------------------------------
    | BUYER PREVIEW
    |--------------------------------------------------------------------------
    */
    const listingPreviewModal = document.getElementById('sellerListingPreviewModal');
    const closeListingPreview = document.getElementById('closeSellerListingPreview');
    const previewBackToEdit = document.getElementById('sellerPreviewBackToEdit');

    function openListingPreview() {

        const type = hasVariantsInput?.value === '1' ? 'variant' : 'simple';
        const name = document.getElementById('sellerNewProductName')?.value.trim() || 'Untitled Product';
        const brand = document.getElementById('sellerNewProductBrand')?.value.trim() || 'No Brand';
        const categoryValue = categorySelect?.value === 'Others'
            ? (customCategoryInput?.value.trim() || 'Others')
            : (categorySelect?.value || 'Uncategorized');
        const discount = Math.min(100, Math.max(0, Number(simpleDiscountInput?.value || 0)));
        const description = addProductForm?.querySelector('[name="description"]')?.value.trim() || 'No description yet.';

        let basePrice = Number(simplePriceInput?.value || 0);
        let totalStock = Number(simpleStockInput?.value || 0);
        let variantData = [];

        if (type === 'variant') {
            variantData = Array.from(variantRows?.querySelectorAll('[data-manual-variant-row]') || []).map(function (row) {
                const optionsRaw = row.querySelector('input[name$="[options]"]')?.value || '{}';
                let options = {};

                try {
                    options = JSON.parse(optionsRaw);
                } catch (_) {
                    options = {};
                }

                return {
                    options,
                    price: Number(row.querySelector('input[name$="[price]"]')?.value || 0),
                    stock: Number(row.querySelector('input[name$="[stock]"]')?.value || 0),
                };
            });

            const validPrices = variantData.map(item => item.price).filter(value => Number.isFinite(value) && value >= 0);
            basePrice = validPrices.length ? Math.min(...validPrices) : 0;
            totalStock = variantData.reduce((sum, item) => sum + Math.max(0, item.stock || 0), 0);
        }

        const salePrice = sellerDiscountedPrice(basePrice, discount);

        document.getElementById('sellerPreviewName').textContent = name;
        document.getElementById('sellerPreviewBrand').textContent = brand;
        document.getElementById('sellerPreviewCategory').textContent = categoryValue;
        document.getElementById('sellerPreviewPrice').textContent = '₱' + Number(salePrice || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('sellerPreviewStock').textContent = totalStock.toLocaleString('en-PH');
        document.getElementById('sellerPreviewFormat').textContent = type === 'variant' ? 'With Variations' : 'Single item';
        document.getElementById('sellerPreviewPreparation').textContent = preparationTimeInput?.value
            ? `${preparationTimeInput.value} day${Number(preparationTimeInput.value) === 1 ? '' : 's'}`
            : 'Not specified';
        document.getElementById('sellerPreviewDescription').textContent = description;

        const previewShipping = document.getElementById('sellerPreviewShipping');
        previewShipping.classList.toggle('hidden', !freeShippingInput?.checked);

        const previewOriginal = document.getElementById('sellerPreviewOriginalPrice');
        const previewDiscount = document.getElementById('sellerPreviewDiscount');

        if (discount > 0) {
            previewOriginal.textContent = '₱' + Number(basePrice || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            previewOriginal.classList.remove('hidden');
            previewDiscount.textContent = `${discount.toLocaleString('en-PH', { maximumFractionDigits: 2 })}% OFF`;
            previewDiscount.classList.remove('hidden');
        } else {
            previewOriginal.classList.add('hidden');
            previewDiscount.classList.add('hidden');
        }

        const previewImage = document.getElementById('sellerPreviewImage');
        const previewImageEmpty = document.getElementById('sellerPreviewImageEmpty');

        if (imagePreview?.src && !imagePreview.classList.contains('hidden')) {
            previewImage.src = imagePreview.src;
            previewImage.alt = name;
            previewImage.classList.remove('hidden');
            previewImageEmpty.classList.add('hidden');
        } else {
            previewImage.src = '';
            previewImage.classList.add('hidden');
            previewImageEmpty.classList.remove('hidden');
        }

        const chipWrap = document.getElementById('sellerPreviewVariationChips');
        chipWrap.innerHTML = '';

        const definitions = getVariantDefinitions();
        definitions.forEach(function (definition) {
            definition.values.slice(0, 8).forEach(function (value) {
                const chip = document.createElement('span');
                chip.className = 'rounded-full border border-[#e5ddd2] bg-white px-2.5 py-1.5 text-[7.5px] font-semibold text-[#71675d]';
                chip.textContent = `${definition.name}: ${value}`;
                chipWrap.appendChild(chip);
            });
        });

        const previewVariantWrap = document.getElementById('sellerPreviewVariantTableWrap');
        const previewVariantRows = document.getElementById('sellerPreviewVariantRows');

        if (type === 'variant' && variantData.length) {
            previewVariantRows.innerHTML = variantData.slice(0, 20).map(function (item) {
                const optionLabel = Object.entries(item.options)
                    .map(([key, value]) => `${escapeHtml(key)}: ${escapeHtml(value)}`)
                    .join(' · ');

                return `
                    <tr>
                        <td class="px-4 py-3 font-semibold text-[#51483f]">${optionLabel || 'Variation'}</td>
                        <td class="px-3 py-3 font-bold text-[#a8731f]">₱${Number(sellerDiscountedPrice(item.price, discount)).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td class="px-3 py-3 font-semibold ${item.stock <= Number(lowStockThresholdInput?.value || 5) ? 'text-[#b87912]' : 'text-[#4f7d61]'}">${Math.max(0, item.stock || 0).toLocaleString('en-PH')}</td>
                    </tr>
                `;
            }).join('');

            previewVariantWrap.classList.remove('hidden');
        } else {
            previewVariantRows.innerHTML = '';
            previewVariantWrap.classList.add('hidden');
        }

        listingPreviewModal?.classList.remove('hidden');
        listingPreviewModal?.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeListingPreviewModal() {
        listingPreviewModal?.classList.add('hidden');
        listingPreviewModal?.classList.remove('flex');

        if (!modal?.classList.contains('flex')) {
            document.body.classList.remove('overflow-hidden');
        }
    }

    previewButton?.addEventListener('click', openListingPreview);
    closeListingPreview?.addEventListener('click', closeListingPreviewModal);
    previewBackToEdit?.addEventListener('click', closeListingPreviewModal);

    listingPreviewModal?.addEventListener('click', function (event) {
        if (event.target === listingPreviewModal) closeListingPreviewModal();
    });


    setProductType('simple');
    renderCategoryBlueprint();
    renderGalleryPreview([]);
    loadServerDraft();

    /*
    |--------------------------------------------------------------------------
    | ADD PRODUCT HERE — AJAX SUBMIT
    |--------------------------------------------------------------------------
    | Product creation now persists fulfillment fields, gallery media, variation
    | images, and any server draft media through the Product Management backend.
    */
    const productsIndexUrlForAdd = @json(route('seller.products.index'));
    const addProductNotice = document.getElementById('sellerAddProductNotice');
    const addProductSubmit = document.getElementById('sellerSubmitProduct');

    function showAddProductNotice(message, danger = false) {
        if (!addProductNotice) return;

        addProductNotice.textContent = String(message || '');
        addProductNotice.classList.remove(
            'hidden',
            'border-[#d5e5da]', 'bg-[#f5fbf7]', 'text-[#4f7d61]',
            'border-[#edd0d0]', 'bg-[#fff6f6]', 'text-[#a45b5b]'
        );

        if (danger) {
            addProductNotice.classList.add('border-[#edd0d0]', 'bg-[#fff6f6]', 'text-[#a45b5b]');
        } else {
            addProductNotice.classList.add('border-[#d5e5da]', 'bg-[#f5fbf7]', 'text-[#4f7d61]');
        }
    }

    addProductForm?.addEventListener('submit', async function (event) {
        /*
        | The flexible product builder's validation listener runs first.
        | If it blocks submission, do not send anything.
        */
        if (event.defaultPrevented) return;

        event.preventDefault();

        if (!addProductSubmit) return;

        const originalLabel = addProductSubmit.innerHTML;
        addProductSubmit.disabled = true;
        addProductSubmit.innerHTML = `
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 animate-spin" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 3a9 9 0 1 1-6.36 2.64"></path>
            </svg>
            Screening product...
        `;

        addProductNotice?.classList.add('hidden');

        try {
            const response = await fetch(addProductForm.action, {
                method: 'POST',
                body: new FormData(addProductForm),
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                signal,
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                const firstError = Object.values(payload?.errors || {})
                    .flat()
                    .find(Boolean);

                throw new Error(firstError || payload?.message || 'Unable to add this product.');
            }

            /*
            | Keep the seller inside Product Management after creation.
            | sessionStorage gives the refreshed page a clean success notice.
            */
            window.sessionStorage.setItem(
                'sariProductsNotice',
                payload?.message || 'Product submitted successfully and is now pending review.'
            );

            hideSmoothSellerModal(modal);

            window.setTimeout(function () {
                window.location.assign(productsIndexUrlForAdd);
            }, 180);
        } catch (error) {
            if (error?.name === 'AbortError') return;

            showAddProductNotice(
                error?.message || 'Unable to add this product. Please check the form and try again.',
                true
            );

            addProductSubmit.disabled = false;
            addProductSubmit.innerHTML = originalLabel;
        }
    });

    }

    /*
    | Show a success message after the local Add Product flow refreshes
    | Product Management.
    */
    const productsClientNotice = document.getElementById('productsClientNotice');
    const savedProductsNotice = window.sessionStorage.getItem('sariProductsNotice');

    if (productsClientNotice && savedProductsNotice) {
        productsClientNotice.textContent = savedProductsNotice;
        productsClientNotice.classList.remove('hidden');
        window.sessionStorage.removeItem('sariProductsNotice');

        window.setTimeout(function () {
            productsClientNotice.classList.add('hidden');
        }, 5500);
    }


    const productsOpenRequest = new URLSearchParams(window.location.search).get('open');
    if (productsOpenRequest === 'add-product') {
        document.getElementById('openAddProductModal')?.click();
    }

    loadProducts();
});
</script>
@endpush
