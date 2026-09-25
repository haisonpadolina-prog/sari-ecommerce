@extends('layouts.seller')

@section('title', 'Products — SARI')
@section('page-title', 'Product Management')


@push('styles')
    {{-- Product Management design merged inline to avoid a separate public CSS request. --}}
    <style id="sariSellerProductsInlineStyles">
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


    /*
    |--------------------------------------------------------------------------
    | PRODUCT MANAGEMENT — 100% BROWSER ZOOM COMPACT PASS
    |--------------------------------------------------------------------------
    | Desktop density refinement only. No transform scaling, no backend
    | changes, and no behavior changes.
    */
    @media (min-width: 1024px) {
        .seller-products-page {
            max-width: 1680px !important;
        }

        /* Page heading */
        .seller-products-page > section:first-of-type {
            padding-top: 0 !important;
        }

        .seller-products-page > section:first-of-type .h-12,
        .seller-products-page > section:first-of-type .sm\:h-\[52px\] {
            width: 46px !important;
            height: 46px !important;
        }

        .seller-products-page > section:first-of-type h1 {
            font-size: 25px !important;
            line-height: 1.08 !important;
        }

        .seller-products-page > section:first-of-type h1 + p {
            margin-top: 3px !important;
            font-size: 9.5px !important;
            line-height: 1.65 !important;
        }

        /* Main two-column workspace */
        .seller-catalog-studio {
            grid-template-columns: 272px minmax(0, 1fr) !important;
            gap: 16px !important;
            margin-top: 18px !important;
        }

        .seller-catalog-rail-shell {
            padding: 14px !important;
            border-radius: 18px !important;
        }

        .seller-catalog-rail {
            top: 78px !important;
        }

        /* Sidebar actions / metrics */
        .seller-rail-primary-action,
        .seller-rail-secondary-action {
            height: 39px !important;
            border-radius: 10px !important;
            font-size: 8.3px !important;
        }

        .seller-rail-stat {
            padding: 10px !important;
            border-radius: 12px !important;
        }

        .seller-rail-stat .h-8.w-8 {
            width: 29px !important;
            height: 29px !important;
        }

        #productsTotalCount,
        #productsApprovedCount,
        #productsLowStockCount,
        #productsOutStockCount {
            font-size: 18px !important;
        }

        .seller-rail-health {
            margin-top: 13px !important;
            padding: 13px !important;
            border-radius: 14px !important;
        }

        #productsCatalogHealthScore {
            font-size: 25px !important;
        }

        .seller-products-quick-filter {
            min-height: 34px !important;
            padding-inline: 9px !important;
            border-radius: 9px !important;
            font-size: 8px !important;
        }

        .seller-products-quick-count {
            min-width: 21px !important;
            height: 20px !important;
            padding-inline: 5px !important;
            font-size: 7.4px !important;
        }

        /* Product workspace */
        .seller-products-workspace {
            padding: 15px !important;
            border-radius: 18px !important;
        }

        .seller-products-workspace > div:first-child {
            padding-bottom: 12px !important;
        }

        .seller-products-workspace > div:first-child h2 {
            font-size: 19px !important;
        }

        .seller-products-workspace > div:first-child p {
            line-height: 1.45 !important;
        }

        /* Filter strip */
        .seller-products-workspace > .mt-4.rounded-\[14px\] {
            margin-top: 13px !important;
            padding: 10px !important;
            border-radius: 12px !important;
        }

        .seller-products-workspace > .mt-4.rounded-\[14px\] > .grid {
            grid-template-columns:
                minmax(220px, 1.35fr)
                128px
                128px
                145px
                auto !important;
            gap: 8px !important;
        }

        #productsSearch,
        #productsStatus,
        #productsStock,
        #productsCategory,
        #productsClearFilters {
            height: 38px !important;
            border-radius: 9px !important;
            font-size: 8.4px !important;
        }

        #productsClearFilters {
            padding-inline: 12px !important;
        }

        /* Product grid/card density */
        #productsGrid {
            margin-top: 13px !important;
            gap: 12px !important;
        }

        .seller-products-card {
            min-height: 330px !important;
            border-radius: 15px !important;
        }

        .seller-products-card > .relative {
            height: 168px !important;
        }

        .seller-products-card > div:last-child {
            padding: 12px !important;
        }

        .seller-products-card h3 {
            font-size: 12.5px !important;
        }

        .seller-products-card h3 + p {
            margin-top: 4px !important;
            font-size: 8.5px !important;
        }

        .seller-products-card h3 + p + div {
            margin-top: 9px !important;
        }

        .seller-products-card strong {
            font-size: 15.5px !important;
        }

        .seller-products-card .seller-product-action {
            height: 35px !important;
            border-radius: 9px !important;
        }

        .seller-products-card .seller-product-action svg {
            width: 15px !important;
            height: 15px !important;
        }

        .seller-products-card > div:last-child > .mt-4.grid {
            margin-top: 11px !important;
            gap: 7px !important;
        }

        /* Initial loading placeholders match the real compact cards */
        #productsGrid > div > .h-\[205px\] {
            height: 168px !important;
        }

        #productsGrid > div > .p-4\.5 {
            padding: 12px !important;
        }
    }

    /* Common 1366x768 / compact laptop tuning at 100% zoom */
    @media (min-width: 1024px) and (max-height: 820px) {
        .seller-catalog-studio {
            grid-template-columns: 258px minmax(0, 1fr) !important;
            gap: 14px !important;
            margin-top: 14px !important;
        }

        .seller-catalog-rail-shell {
            padding: 12px !important;
        }

        .seller-products-workspace {
            padding: 13px !important;
        }

        .seller-products-card {
            min-height: 305px !important;
        }

        .seller-products-card > .relative,
        #productsGrid > div > .h-\[205px\] {
            height: 150px !important;
        }

        .seller-products-card > div:last-child,
        #productsGrid > div > .p-4\.5 {
            padding: 11px !important;
        }

        .seller-products-card .seller-product-action {
            height: 33px !important;
        }

        .seller-rail-health {
            padding: 11px !important;
        }

        .seller-products-quick-filter {
            min-height: 32px !important;
        }
    }

    /* Keep medium desktop widths from overflowing the filter row */
    @media (min-width: 1024px) and (max-width: 1279px) {
        .seller-catalog-studio {
            grid-template-columns: 245px minmax(0, 1fr) !important;
        }

        .seller-products-workspace > .mt-4.rounded-\[14px\] > .grid {
            grid-template-columns: minmax(210px, 1fr) 120px 120px !important;
        }

        #productsCategory {
            grid-column: span 2 !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT CARD ACTIONS — CLICKABILITY FIX
    |--------------------------------------------------------------------------
    | Keeps the action controls above decorative/image layers at 100% zoom.
    */
    #productsGrid .seller-product-actions {
        position: relative !important;
        z-index: 20 !important;
        isolation: isolate !important;
        pointer-events: auto !important;
    }

    #productsGrid .seller-product-action {
        position: relative !important;
        z-index: 21 !important;
        pointer-events: auto !important;
        cursor: pointer !important;
        touch-action: manipulation !important;
        user-select: none !important;
    }

    #productsGrid .seller-product-action svg,
    #productsGrid .seller-product-action svg * {
        pointer-events: none !important;
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT ACTIONS — MODAL HARD FIX
    |--------------------------------------------------------------------------
    | Ensures action controls receive pointer input and modal overlays always
    | sit above the Product Management workspace.
    */
    #productsGrid [data-product-item] {
        position: relative !important;
    }

    #productsGrid .seller-product-actions {
        position: relative !important;
        z-index: 50 !important;
        pointer-events: auto !important;
    }

    #productsGrid .seller-product-action {
        position: relative !important;
        z-index: 51 !important;
        pointer-events: auto !important;
        cursor: pointer !important;
    }

    #sellerViewProductModal,
    #sellerEditProductModal,
    #productsActionModal {
        z-index: 9999 !important;
    }

    #sellerViewProductModal.flex,
    #sellerEditProductModal.flex,
    #productsActionModal.flex {
        display: flex !important;
        visibility: visible !important;
        pointer-events: auto !important;
    }

    #sellerViewProductModal.seller-modal-visible,
    #sellerEditProductModal.seller-modal-visible {
        opacity: 1 !important;
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT MODALS — LIFECYCLE / STACKING CONTEXT FINAL FIX
    |--------------------------------------------------------------------------
    */
    body > #sellerViewProductModal.flex,
    body > #sellerEditProductModal.flex,
    body > #productsActionModal.flex {
        position: fixed !important;
        inset: 0 !important;
        display: flex !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        z-index: 2147483000 !important;
    }

    body > #sellerViewProductModal.flex > :first-child,
    body > #sellerEditProductModal.flex > :first-child,
    body > #productsActionModal.flex > :first-child {
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT MANAGEMENT — INSTANT INTERACTION PASS
    |--------------------------------------------------------------------------
    | Fast reaction: no backdrop blur cost, no opening-delay transitions.
    */
    #sellerViewProductModal,
    #sellerEditProductModal,
    #productsActionModal,
    #sellerAddProductModal {
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }

    #sellerViewProductModal,
    #sellerEditProductModal,
    #productsActionModal,
    #sellerAddProductModal,
    #sellerViewProductModal > :first-child,
    #sellerEditProductModal > :first-child,
    #productsActionModal > :first-child,
    #sellerAddProductModal > :first-child {
        transition-duration: .08s !important;
        transition-delay: 0s !important;
    }

    #sellerViewProductModal.flex,
    #sellerEditProductModal.flex,
    #productsActionModal.flex,
    #sellerAddProductModal.flex {
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
    }

    #productsGrid .seller-product-action {
        transition-duration: .10s !important;
    }

    @media (prefers-reduced-motion: reduce) {
        #sellerViewProductModal,
        #sellerEditProductModal,
        #productsActionModal,
        #sellerAddProductModal,
        #sellerViewProductModal > :first-child,
        #sellerEditProductModal > :first-child,
        #productsActionModal > :first-child,
        #sellerAddProductModal > :first-child {
            transition: none !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT CARDS — CLEAN REFERENCE DESIGN
    |--------------------------------------------------------------------------
    | Inset media, compact badges, title/price row, clean information footer,
    | and borderless icon actions based on the approved visual reference.
    */
    #productsGrid {
        align-items: start !important;
    }

    #productsGrid .seller-product-card--reference {
        display: flex !important;
        min-height: 0 !important;
        flex-direction: column !important;
        overflow: hidden !important;
        border: 1px solid #e5ddd2 !important;
        border-radius: 20px !important;
        background: #fff !important;
        padding: 12px !important;
        box-shadow:
            0 12px 28px rgba(46, 38, 29, .052),
            0 2px 6px rgba(46, 38, 29, .026) !important;
        transform: none !important;
    }

    #productsGrid .seller-product-card--reference:hover {
        border-color: #e0d6c9 !important;
        box-shadow:
            0 12px 28px rgba(46, 38, 29, .052),
            0 2px 6px rgba(46, 38, 29, .026) !important;
        transform: none !important;
    }

    #productsGrid .seller-product-card-media {
        position: relative !important;
        width: 100% !important;
        height: 208px !important;
        flex: 0 0 auto !important;
        overflow: hidden !important;
        border-radius: 15px !important;
        background: #f1efeb !important;
    }

    #productsGrid .seller-product-card-media .seller-product-image {
        display: block !important;
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        transform: none !important;
    }

    #productsGrid .seller-product-card-badges {
        position: absolute !important;
        z-index: 3 !important;
        top: 12px !important;
        left: 12px !important;
        display: flex !important;
        max-width: calc(100% - 24px) !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 7px !important;
        pointer-events: none !important;
    }

    #productsGrid .seller-product-card-badge {
        display: inline-flex !important;
        min-height: 27px !important;
        align-items: center !important;
        justify-content: center !important;
        border-width: 1px !important;
        border-style: solid !important;
        border-radius: 999px !important;
        padding: 0 11px !important;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: 8px !important;
        line-height: 1 !important;
        font-weight: 650 !important;
        white-space: nowrap !important;
        box-shadow: 0 3px 9px rgba(30, 24, 18, .05) !important;
    }

    #productsGrid .seller-product-card-badge--dark {
        border-color: rgba(255,255,255,.08) !important;
        background: rgba(42, 39, 35, .94) !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(20,18,15,.12) !important;
    }

    #productsGrid .seller-product-card-body {
        display: flex !important;
        min-width: 0 !important;
        flex: 1 1 auto !important;
        flex-direction: column !important;
        padding: 16px 8px 5px !important;
    }

    #productsGrid .seller-product-card-heading {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) auto !important;
        align-items: start !important;
        gap: 14px !important;
    }

    #productsGrid .seller-product-card-title {
        overflow: hidden !important;
        margin: 0 !important;
        color: #201c18 !important;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: 13.5px !important;
        line-height: 1.35 !important;
        font-weight: 650 !important;
        letter-spacing: -.025em !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    #productsGrid .seller-product-card-category {
        overflow: hidden !important;
        margin: 5px 0 0 !important;
        color: #897f74 !important;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: 8.7px !important;
        line-height: 1.4 !important;
        font-weight: 400 !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    #productsGrid .seller-product-card-price-wrap {
        display: flex !important;
        min-width: max-content !important;
        flex-direction: column !important;
        align-items: flex-end !important;
        gap: 3px !important;
        text-align: right !important;
    }

    #productsGrid .seller-product-card-price {
        margin: 0 !important;
        color: #b77a00 !important;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: 14.5px !important;
        line-height: 1.2 !important;
        font-weight: 700 !important;
        letter-spacing: -.025em !important;
    }

    #productsGrid .seller-product-card-original-price {
        color: #aaa198 !important;
        font-size: 7.5px !important;
        line-height: 1 !important;
        text-decoration: line-through !important;
    }

    #productsGrid .seller-product-card-divider {
        width: 100% !important;
        height: 1px !important;
        margin: 15px 0 13px !important;
        background: #e8e0d6 !important;
    }

    #productsGrid .seller-product-card-footer {
        display: flex !important;
        min-width: 0 !important;
        align-items: flex-end !important;
        justify-content: space-between !important;
        gap: 12px !important;
        margin-top: auto !important;
    }

    #productsGrid .seller-product-card-stock-copy {
        min-width: 0 !important;
    }

    #productsGrid .seller-product-card-stock {
        margin: 0 !important;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: 10px !important;
        line-height: 1.2 !important;
        font-weight: 600 !important;
    }

    #productsGrid .seller-product-card-updated {
        overflow: hidden !important;
        max-width: 160px !important;
        margin: 5px 0 0 !important;
        color: #8c8379 !important;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
        font-size: 7.6px !important;
        line-height: 1.2 !important;
        font-weight: 400 !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    #productsGrid .seller-product-card-actions {
        position: relative !important;
        z-index: 50 !important;
        display: flex !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 3px !important;
        margin: 0 !important;
        pointer-events: auto !important;
    }

    #productsGrid .seller-product-card--reference .seller-product-action {
        position: relative !important;
        z-index: 51 !important;
        display: grid !important;
        width: 31px !important;
        height: 31px !important;
        min-width: 31px !important;
        flex: 0 0 31px !important;
        place-items: center !important;
        border: 0 !important;
        border-radius: 8px !important;
        background: transparent !important;
        padding: 0 !important;
        color: #746c62 !important;
        box-shadow: none !important;
        cursor: pointer !important;
        pointer-events: auto !important;
        transition:
            background-color .10s ease,
            color .10s ease !important;
    }

    #productsGrid .seller-product-card--reference .seller-product-action svg {
        width: 16px !important;
        height: 16px !important;
        pointer-events: none !important;
    }

    #productsGrid .seller-product-card--reference .seller-product-action svg * {
        pointer-events: none !important;
    }

    #productsGrid .seller-product-card--reference .seller-product-action:hover {
        border: 0 !important;
        background: #f5f1eb !important;
        color: #3f3932 !important;
        box-shadow: none !important;
    }

    #productsGrid .seller-product-card--reference .seller-product-action--edit:hover {
        background: #fff6e5 !important;
        color: #aa7008 !important;
    }

    #productsGrid .seller-product-card--reference .seller-product-action--archive:hover {
        background: #f6f2ec !important;
        color: #745e40 !important;
    }

    #productsGrid .seller-product-card--reference .seller-product-action--delete:hover {
        background: #fff0f0 !important;
        color: #ad5555 !important;
    }

    #productsGrid .seller-product-card--reference .seller-product-action:focus-visible {
        outline: none !important;
        background: #f5f1eb !important;
        box-shadow: 0 0 0 2px rgba(191,133,22,.14) !important;
    }

    /* Keep the reference proportions clean at normal 100% browser zoom. */
    @media (min-width: 1024px) {
        #productsGrid .seller-product-card--reference {
            min-height: 332px !important;
        }

        #productsGrid .seller-product-card-media {
            height: 190px !important;
        }

        #productsGrid .seller-product-card-body {
            padding: 14px 7px 4px !important;
        }
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        #productsGrid .seller-product-card--reference {
            min-height: 308px !important;
            padding: 10px !important;
            border-radius: 17px !important;
        }

        #productsGrid .seller-product-card-media {
            height: 166px !important;
            border-radius: 13px !important;
        }

        #productsGrid .seller-product-card-body {
            padding: 12px 6px 3px !important;
        }

        #productsGrid .seller-product-card-divider {
            margin: 12px 0 10px !important;
        }

        #productsGrid .seller-product-card--reference .seller-product-action {
            width: 29px !important;
            height: 29px !important;
            min-width: 29px !important;
            flex-basis: 29px !important;
        }
    }

    @media (max-width: 639px) {
        #productsGrid .seller-product-card--reference {
            padding: 10px !important;
            border-radius: 17px !important;
        }

        #productsGrid .seller-product-card-media {
            height: 210px !important;
            border-radius: 13px !important;
        }

        #productsGrid .seller-product-card-body {
            padding: 13px 5px 3px !important;
        }

        #productsGrid .seller-product-card-title {
            font-size: 13px !important;
        }

        #productsGrid .seller-product-card-price {
            font-size: 13.5px !important;
        }

        #productsGrid .seller-product-card-actions {
            gap: 1px !important;
        }

        #productsGrid .seller-product-card--reference .seller-product-action {
            width: 29px !important;
            height: 29px !important;
            min-width: 29px !important;
            flex-basis: 29px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT WORKSPACE — OPEN / CONTAINERLESS LAYOUT
    |--------------------------------------------------------------------------
    | Removes the large enclosing workspace surface so the filter toolbar and
    | product cards sit directly on the page background.
    */
    .seller-products-workspace {
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
        overflow: visible !important;
    }

    .seller-products-workspace::before,
    .seller-products-workspace::after {
        content: none !important;
        display: none !important;
    }

    /* Keep the workspace heading open instead of looking like part of a card. */
    .seller-products-workspace > div:first-child {
        padding: 0 1px 10px !important;
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    /* Filter controls remain as one slim independent surface. */
    .seller-products-workspace > .mt-4.rounded-\[14px\] {
        margin-top: 10px !important;
        border: 1px solid #e6dfd6 !important;
        border-radius: 14px !important;
        background: rgba(255,255,255,.84) !important;
        padding: 10px !important;
        box-shadow:
            0 6px 18px rgba(43,33,21,.035),
            inset 0 1px 0 rgba(255,255,255,.94) !important;
    }

    /* Product grid now sits freely on the page background. */
    #productsGrid {
        margin-top: 16px !important;
        padding: 0 !important;
        background: transparent !important;
    }

    /* Empty-state should also feel open, not like another enclosing panel. */
    #productsEmpty {
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    @media (min-width: 1024px) {
        .seller-products-workspace {
            padding: 0 !important;
        }

        #productsGrid {
            margin-top: 14px !important;
        }
    }

    @media (max-width: 639px) {
        .seller-products-workspace > .mt-4.rounded-\[14px\] {
            border-radius: 12px !important;
            padding: 9px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SELLER INVENTORY SIDEBAR — WHITE / CLEAN / MODERN
    |--------------------------------------------------------------------------
    | Visual refinement only for the left catalog rail.
    */
    .seller-catalog-rail-shell {
        border: 1px solid #e7e0d7 !important;
        border-radius: 22px !important;
        background: linear-gradient(180deg, #ffffff 0%, #fdfbf8 100%) !important;
        box-shadow:
            0 14px 34px rgba(47, 35, 22, .045),
            0 2px 8px rgba(47, 35, 22, .02),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
        padding: 16px !important;
    }

    .seller-catalog-rail {
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-catalog-rail-shell .tracking-\[0\.22em\],
    .seller-catalog-rail-shell .tracking-\[0\.18em\],
    .seller-catalog-rail-shell .tracking-\[0\.14em\] {
        color: #b1843a !important;
    }

    .seller-rail-primary-action {
        height: 42px !important;
        border: 1px solid #c79218 !important;
        border-radius: 13px !important;
        background: linear-gradient(180deg, #d79a14 0%, #c88a08 100%) !important;
        color: #ffffff !important;
        box-shadow:
            0 10px 22px rgba(203, 140, 10, .16),
            inset 0 1px 0 rgba(255,255,255,.16) !important;
    }

    .seller-rail-primary-action:hover {
        background: linear-gradient(180deg, #de9f18 0%, #cf8f0a 100%) !important;
        box-shadow:
            0 12px 24px rgba(203, 140, 10, .18),
            inset 0 1px 0 rgba(255,255,255,.18) !important;
    }

    .seller-rail-secondary-action {
        height: 42px !important;
        border: 1px solid #e5ddd2 !important;
        border-radius: 13px !important;
        background: #ffffff !important;
        color: #40372f !important;
        box-shadow:
            0 6px 14px rgba(43, 33, 21, .04),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .seller-rail-secondary-action:hover {
        border-color: #ddd3c7 !important;
        background: #fffdfa !important;
        color: #2e2721 !important;
    }

    .seller-rail-stat {
        border: 1px solid #ebe3d8 !important;
        border-radius: 17px !important;
        background: #ffffff !important;
        box-shadow:
            0 8px 18px rgba(47, 35, 22, .035),
            0 1px 3px rgba(47, 35, 22, .018) !important;
        padding: 12px !important;
    }

    .seller-rail-stat .h-8.w-8,
    .seller-rail-stat .h-9.w-9,
    .seller-rail-stat .h-10.w-10 {
        border: 1px solid #eee6dc !important;
        background: #fcfaf7 !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.95) !important;
    }

    .seller-rail-stat .text-\[18px\],
    .seller-rail-stat .text-\[19px\],
    .seller-rail-stat .text-\[20px\] {
        color: #221d18 !important;
        font-weight: 700 !important;
    }

    .seller-rail-stat p:last-child,
    .seller-rail-stat .text-\[8px\],
    .seller-rail-stat .text-\[8\.5px\] {
        color: #877d72 !important;
    }

    .seller-rail-health {
        border: 1px solid #ebe2d8 !important;
        border-radius: 18px !important;
        background: linear-gradient(180deg, #fffefe 0%, #fcfaf7 100%) !important;
        box-shadow:
            0 10px 24px rgba(47, 35, 22, .035),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
        padding: 14px !important;
    }

    .seller-rail-health .text-\[24px\],
    .seller-rail-health .text-\[25px\] {
        color: #c38408 !important;
        font-weight: 700 !important;
    }

    .seller-products-quick-filter {
        min-height: 35px !important;
        border: 1px solid #ebe3d8 !important;
        border-radius: 11px !important;
        background: #ffffff !important;
        color: #4a4036 !important;
        box-shadow:
            0 4px 10px rgba(47, 35, 22, .025),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .seller-products-quick-filter:hover {
        border-color: #ded3c6 !important;
        background: #fffdfa !important;
        color: #2f2923 !important;
    }

    .seller-products-quick-filter.is-active,
    .seller-products-quick-filter[aria-pressed="true"] {
        border-color: #d8b36b !important;
        background: #fff7ea !important;
        color: #9f6a0b !important;
        box-shadow:
            0 6px 14px rgba(200, 140, 10, .06),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .seller-products-quick-count {
        border: 1px solid #ece3d7 !important;
        background: #fcfaf7 !important;
        color: #8c7a61 !important;
        box-shadow: none !important;
    }

    .seller-products-quick-filter.is-active .seller-products-quick-count,
    .seller-products-quick-filter[aria-pressed="true"] .seller-products-quick-count {
        border-color: #edd9af !important;
        background: #fff1cf !important;
        color: #a16c0d !important;
    }

    .seller-catalog-rail-shell h2,
    .seller-catalog-rail-shell h3,
    .seller-catalog-rail-shell h4 {
        color: #241f19 !important;
    }

    .seller-catalog-rail-shell p,
    .seller-catalog-rail-shell .text-\[8px\],
    .seller-catalog-rail-shell .text-\[8\.5px\],
    .seller-catalog-rail-shell .text-\[9px\] {
        color: #867c71 !important;
    }

    .seller-catalog-rail-shell hr,
    .seller-catalog-rail-shell .border-\[\#efe7db\],
    .seller-catalog-rail-shell .border-\[\#ece4d8\] {
        border-color: #eee6dc !important;
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        .seller-catalog-rail-shell {
            padding: 14px !important;
            border-radius: 20px !important;
        }

        .seller-rail-stat {
            padding: 11px !important;
        }

        .seller-rail-health {
            padding: 12px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT MANAGEMENT — FINAL CLEANUP
    |--------------------------------------------------------------------------
    | Clean primary action + tighter product-grid vertical rhythm.
    */
    .seller-rail-primary-action {
        height: 40px !important;
        border: 0 !important;
        border-radius: 11px !important;
        background: #d89208 !important;
        color: #ffffff !important;
        font-size: 8.5px !important;
        font-weight: 650 !important;
        letter-spacing: 0 !important;
        box-shadow:
            0 7px 16px rgba(190, 126, 4, .14),
            inset 0 1px 0 rgba(255,255,255,.16) !important;
    }

    .seller-rail-primary-action,
    .seller-rail-primary-action span,
    .seller-rail-primary-action svg {
        color: #ffffff !important;
    }

    .seller-rail-primary-action svg {
        width: 15px !important;
        height: 15px !important;
        stroke: #ffffff !important;
    }

    .seller-rail-primary-action:hover {
        background: #ca8505 !important;
        color: #ffffff !important;
        box-shadow:
            0 8px 17px rgba(190, 126, 4, .16),
            inset 0 1px 0 rgba(255,255,255,.16) !important;
    }

    .seller-rail-primary-action:focus-visible {
        outline: none !important;
        box-shadow:
            0 0 0 3px rgba(216,146,8,.14),
            0 7px 16px rgba(190,126,4,.14) !important;
    }

    /*
     * Pull the product cards upward so the layout feels tighter and more
     * connected to the filters.
     */
    #productsGrid {
        margin-top: 8px !important;
        row-gap: 12px !important;
    }

    .seller-products-workspace > .mt-4.rounded-\[14px\] {
        margin-bottom: 0 !important;
    }

    @media (min-width: 1024px) {
        #productsGrid {
            margin-top: 7px !important;
        }
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        #productsGrid {
            margin-top: 5px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT MANAGEMENT — FIRST-SCREEN PRODUCT VISIBILITY
    |--------------------------------------------------------------------------
    | Reduce non-essential vertical chrome so products appear immediately.
    */

    /* Compact top page identity */
    .seller-products-page > section:first-of-type {
        padding-top: 0 !important;
    }

    .seller-products-page > section:first-of-type > div {
        gap: 8px !important;
    }

    .seller-products-page > section:first-of-type .h-12,
    .seller-products-page > section:first-of-type .sm\:h-\[52px\] {
        width: 42px !important;
        height: 42px !important;
        border-radius: 12px !important;
    }

    .seller-products-page > section:first-of-type .h-5.w-5 {
        width: 18px !important;
        height: 18px !important;
    }

    .seller-products-page > section:first-of-type h1 {
        margin-top: 2px !important;
        font-size: 25px !important;
        line-height: 1.05 !important;
    }

    /* Pull left rail + catalog workspace up closer to the page title. */
    .seller-catalog-studio {
        margin-top: 10px !important;
    }

    /* The open workspace stays containerless and compact. */
    .seller-products-workspace {
        padding-top: 0 !important;
    }

    /* Manage your catalog header: keep only useful information. */
    .seller-products-workspace > div:first-child {
        min-height: 0 !important;
        gap: 8px !important;
        padding: 0 1px 8px !important;
        border-bottom: 0 !important;
    }

    .seller-products-workspace > div:first-child h2 {
        margin-top: 0 !important;
        font-size: 20px !important;
        line-height: 1.08 !important;
    }

    #productsResultCount {
        margin-top: 4px !important;
        font-size: 8.3px !important;
        line-height: 1.2 !important;
    }

    /* Filters sit directly under the compact catalog heading. */
    .seller-products-workspace > .mt-4.rounded-\[14px\] {
        margin-top: 8px !important;
        padding: 9px !important;
        border-radius: 13px !important;
    }

    #productsSearch,
    #productsStatus,
    #productsStock,
    #productsCategory,
    #productsClearFilters {
        height: 36px !important;
    }

    /* Product cards appear almost immediately after filters. */
    #productsGrid {
        margin-top: 6px !important;
        row-gap: 12px !important;
    }

    @media (min-width: 1024px) {
        .seller-products-page > section:first-of-type h1 {
            font-size: 24px !important;
        }

        .seller-catalog-studio {
            margin-top: 8px !important;
        }

        .seller-products-workspace > div:first-child {
            padding-bottom: 6px !important;
        }

        .seller-products-workspace > .mt-4.rounded-\[14px\] {
            margin-top: 6px !important;
        }

        #productsGrid {
            margin-top: 5px !important;
        }
    }

    /* Extra-tight common laptop view without changing card proportions. */
    @media (min-width: 1024px) and (max-height: 820px) {
        .seller-products-page > section:first-of-type .h-12,
        .seller-products-page > section:first-of-type .sm\:h-\[52px\] {
            width: 38px !important;
            height: 38px !important;
        }

        .seller-products-page > section:first-of-type h1 {
            font-size: 22px !important;
        }

        .seller-catalog-studio {
            margin-top: 6px !important;
        }

        .seller-products-workspace > div:first-child h2 {
            font-size: 18px !important;
        }

        .seller-products-workspace > .mt-4.rounded-\[14px\] {
            margin-top: 5px !important;
            padding: 8px !important;
        }

        #productsGrid {
            margin-top: 4px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT WORKSPACE — HEADERLESS / RAISED PRODUCT GRID
    |--------------------------------------------------------------------------
    */
    .seller-catalog-studio {
        margin-top: 3px !important;
        align-items: start !important;
    }

    .seller-products-workspace {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    /* Filter toolbar is now the first visible element on the right. */
    .seller-products-workspace > .mt-4.rounded-\[14px\] {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        padding: 7px !important;
        border-radius: 12px !important;
    }

    #productsSearch,
    #productsStatus,
    #productsStock,
    #productsCategory,
    #productsClearFilters {
        height: 34px !important;
    }

    /* Cards begin immediately after the filters. */
    #productsGrid {
        margin-top: 2px !important;
        row-gap: 11px !important;
    }

    @media (min-width: 1024px) {
        .seller-catalog-studio {
            margin-top: 1px !important;
        }

        .seller-products-workspace > .mt-4.rounded-\[14px\] {
            padding: 6px !important;
        }

        #productsGrid {
            margin-top: 1px !important;
        }
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        .seller-catalog-studio {
            margin-top: 0 !important;
        }

        #productsSearch,
        #productsStatus,
        #productsStock,
        #productsCategory,
        #productsClearFilters {
            height: 33px !important;
        }

        #productsGrid {
            margin-top: 0 !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CATALOG CONTROL — COMPACT DEFAULT + VIEW MORE
    |--------------------------------------------------------------------------
    */
    .seller-catalog-rail {
        padding-top: 10px !important;
    }

    .seller-catalog-rail-shell {
        padding: 13px !important;
        border-radius: 19px !important;
    }

    .seller-catalog-rail-shell > div:first-child .h-10.w-10 {
        width: 34px !important;
        height: 34px !important;
        border-radius: 10px !important;
    }

    .seller-catalog-rail-shell > div:first-child h2 {
        margin-top: 2px !important;
        font-size: 16px !important;
        line-height: 1.15 !important;
    }

    .seller-catalog-rail-shell > .mt-4.grid.grid-cols-2 {
        margin-top: 11px !important;
        gap: 7px !important;
    }

    .seller-rail-primary-action,
    .seller-rail-secondary-action {
        height: 36px !important;
        border-radius: 10px !important;
        font-size: 7.8px !important;
    }

    .seller-rail-primary-action svg,
    .seller-rail-secondary-action svg {
        width: 14px !important;
        height: 14px !important;
    }

    .seller-catalog-rail-shell > .mt-5 {
        margin-top: 13px !important;
    }

    .seller-catalog-rail-shell > .mt-5 > .mb-2\.5 {
        margin-bottom: 7px !important;
    }

    .seller-rail-stat {
        min-height: 74px !important;
        padding: 9px !important;
        border-radius: 13px !important;
    }

    .seller-rail-stat .h-8.w-8 {
        width: 27px !important;
        height: 27px !important;
        border-radius: 8px !important;
    }

    .seller-rail-stat .h-\[15px\].w-\[15px\] {
        width: 13px !important;
        height: 13px !important;
    }

    .seller-rail-stat span[id] {
        font-size: 17px !important;
    }

    .seller-rail-stat p {
        margin-top: 7px !important;
        font-size: 7.7px !important;
    }

    .seller-rail-health {
        margin-top: 11px !important;
        padding: 11px !important;
        border-radius: 14px !important;
    }

    #productsCatalogHealthScore {
        font-size: 24px !important;
    }

    #productsCatalogHealthLabel {
        padding: 4px 8px !important;
        font-size: 6.8px !important;
    }

    .seller-rail-health .seller-catalog-health-progress {
        margin-top: 9px !important;
        height: 5px !important;
    }

    #productsCatalogAttention {
        margin-top: 7px !important;
        font-size: 7.6px !important;
        line-height: 1.35 !important;
    }

    .seller-rail-health > .mt-3.grid {
        margin-top: 9px !important;
        padding-top: 9px !important;
        gap: 5px 10px !important;
        font-size: 7.3px !important;
    }

    .seller-rail-more {
        border-top: 1px solid #eee7de !important;
        padding-top: 10px !important;
    }

    .seller-rail-more > summary {
        list-style: none !important;
    }

    .seller-rail-more > summary::-webkit-details-marker {
        display: none !important;
    }

    .seller-rail-more-toggle {
        display: flex !important;
        width: 100% !important;
        height: 34px !important;
        cursor: pointer !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 10px !important;
        border: 1px solid #e8e0d6 !important;
        border-radius: 10px !important;
        background: #ffffff !important;
        padding: 0 10px !important;
        color: #665d54 !important;
        font-size: 8px !important;
        font-weight: 600 !important;
        box-shadow: none !important;
        user-select: none !important;
        transition: border-color .12s ease, background-color .12s ease, color .12s ease !important;
    }

    .seller-rail-more-toggle:hover {
        border-color: #ddcfbd !important;
        background: #fffdf9 !important;
        color: #3d352e !important;
    }

    .seller-rail-more-chevron {
        transition: transform .14s ease !important;
    }

    .seller-rail-more[open] .seller-rail-more-chevron {
        transform: rotate(180deg) !important;
    }

    .seller-rail-more-content {
        padding-top: 10px !important;
    }

    .seller-rail-more .seller-products-quick-filter {
        min-height: 31px !important;
        border-radius: 9px !important;
        padding-inline: 9px !important;
        font-size: 7.5px !important;
    }

    .seller-rail-more .seller-products-quick-count {
        min-width: 20px !important;
        height: 19px !important;
        padding-inline: 5px !important;
        font-size: 6.8px !important;
    }

    .seller-rail-more .seller-quick-dot {
        width: 6px !important;
        height: 6px !important;
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        .seller-catalog-rail {
            padding-top: 8px !important;
        }

        .seller-catalog-rail-shell {
            padding: 11px !important;
        }

        .seller-rail-stat {
            min-height: 68px !important;
            padding: 8px !important;
        }

        .seller-rail-health {
            padding: 10px !important;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | FILTER POSITION + PRIMARY BUTTON TEXT FIX
    |--------------------------------------------------------------------------
    */

    /* Keep the filter toolbar high in the workspace. */
    .seller-products-workspace {
        padding-top: 0 !important;
        margin-top: -2px !important;
    }

    .seller-products-workspace > .mt-4.rounded-\[14px\] {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        padding: 7px !important;
    }

    /* Give the filters breathing room before the first product row. */
    #productsGrid {
        margin-top: 11px !important;
    }

    @media (min-width: 1024px) {
        .seller-products-workspace {
            margin-top: -4px !important;
        }

        #productsGrid {
            margin-top: 10px !important;
        }
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        .seller-products-workspace {
            margin-top: -5px !important;
        }

        #productsGrid {
            margin-top: 9px !important;
        }
    }

    /* Add Product — always white text and icon. */
    #openAddProductModal,
    #openAddProductModal:hover,
    #openAddProductModal:focus,
    #openAddProductModal:active {
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
    }

    #openAddProductModal *,
    #openAddProductModal:hover *,
    #openAddProductModal:focus * {
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
    }

    #openAddProductModal svg,
    #openAddProductModal svg * {
        stroke: #ffffff !important;
        color: #ffffff !important;
    }


    /*
    |--------------------------------------------------------------------------
    | SAFE PRODUCT STATUS / STOCK VISUALS
    |--------------------------------------------------------------------------
    */
    #productsGrid .seller-product-card-badge {
        min-height: 28px !important;
        border: 0 !important;
        border-radius: 999px !important;
        padding: 0 12px !important;
        font-size: 8.5px !important;
        font-weight: 650 !important;
        box-shadow: 0 3px 9px rgba(38, 31, 23, .06) !important;
        backdrop-filter: none !important;
    }

    #productsGrid .seller-product-status--active {
        background-color: #dcf3e5 !important;
        color: #14764c !important;
    }

    #productsGrid .seller-product-status--pending {
        background-color: #edf4fb !important;
        color: #537a9f !important;
    }

    #productsGrid .seller-product-status--danger {
        background-color: #fff0f0 !important;
        color: #b45656 !important;
    }

    #productsGrid .seller-product-status--neutral {
        background-color: #f2efeb !important;
        color: #6f675f !important;
    }

    #productsGrid .seller-product-card-stock {
        margin: 0 !important;
        font-size: 10px !important;
        line-height: 1.2 !important;
        font-weight: 600 !important;
    }

    #productsGrid [data-stock="out-of-stock"] .seller-product-card-stock {
        color: #e00000 !important;
    }

    #productsGrid [data-stock="low-stock"] .seller-product-card-stock {
        color: #b8780b !important;
    }

    #productsGrid [data-stock="in-stock"] .seller-product-card-stock {
        color: #2d2925 !important;
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT STATUS + STOCK — STRONG CLEAN COLORS
    |--------------------------------------------------------------------------
    */
    #productsGrid .seller-product-status--active {
        background-color: #c8f0d7 !important;
        color: #087747 !important;
        border: 0 !important;
        box-shadow: 0 3px 9px rgba(8, 119, 71, .08) !important;
    }

    #productsGrid .seller-stock-text--out {
        color: #ff0000 !important;
        font-weight: 600 !important;
    }

    #productsGrid .seller-stock-text--low {
        color: #b97808 !important;
        font-weight: 600 !important;
    }

    #productsGrid .seller-stock-text--normal {
        color: #2d2925 !important;
        font-weight: 600 !important;
    }


    /*
    |--------------------------------------------------------------------------
    | CATALOG CONTROL — CLEAN FLAT WHITE PANEL
    |--------------------------------------------------------------------------
    | One white parent surface. Remove nested card-within-card styling from
    | inventory metrics, catalog health, and View more.
    */

    .seller-catalog-rail-shell {
        background: #ffffff !important;
        border: 1px solid #e9e2d8 !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 22px rgba(45, 35, 24, .035) !important;
    }

    /* Inventory metrics: no individual containers. */
    .seller-rail-stat {
        min-height: 62px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 8px 4px !important;
        box-shadow: none !important;
    }

    .seller-rail-stat:nth-child(odd) {
        padding-right: 10px !important;
        border-right: 1px solid #eee8df !important;
    }

    .seller-rail-stat:nth-child(even) {
        padding-left: 10px !important;
    }

    .seller-rail-stat:nth-child(n+3) {
        border-top: 1px solid #eee8df !important;
        padding-top: 10px !important;
    }

    .seller-rail-stat .h-8.w-8 {
        width: 26px !important;
        height: 26px !important;
        border: 0 !important;
        border-radius: 8px !important;
        box-shadow: none !important;
    }

    .seller-rail-stat span[id] {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #241f1a !important;
    }

    .seller-rail-stat p {
        margin-top: 6px !important;
        font-size: 7.8px !important;
        color: #756c62 !important;
    }

    /* Catalog Health: open section, no nested card. */
    .seller-rail-health {
        margin-top: 12px !important;
        padding: 12px 0 4px !important;
        border: 0 !important;
        border-top: 1px solid #eee7de !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    #productsCatalogHealthScore {
        font-size: 25px !important;
    }

    #productsCatalogHealthLabel {
        border: 0 !important;
        background: #fff4e3 !important;
        color: #9a6817 !important;
        box-shadow: none !important;
    }

    .seller-rail-health .seller-catalog-health-progress {
        margin-top: 9px !important;
        background: #eee8df !important;
        border-radius: 999px !important;
    }

    .seller-rail-health > .mt-3.grid {
        margin-top: 9px !important;
        padding-top: 9px !important;
        border-top-color: #eee8df !important;
    }

    /* View more: simple row, no button-like box. */
    .seller-rail-more {
        margin-top: 8px !important;
        padding-top: 8px !important;
        border-top: 1px solid #eee7de !important;
        background: transparent !important;
    }

    .seller-rail-more-toggle {
        height: 32px !important;
        border: 0 !important;
        border-radius: 8px !important;
        background: transparent !important;
        padding: 0 4px !important;
        color: #6b6259 !important;
        box-shadow: none !important;
    }

    .seller-rail-more-toggle:hover {
        border: 0 !important;
        background: #fff9ef !important;
        color: #8e610d !important;
    }

    .seller-rail-more-content {
        padding-top: 8px !important;
        background: #ffffff !important;
    }

    /* Quick Views stay white and lightweight when expanded. */
    .seller-rail-more .seller-products-quick-filter {
        border: 0 !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        box-shadow: none !important;
    }

    .seller-rail-more .seller-products-quick-filter:hover {
        background: #fff9ef !important;
    }

    .seller-rail-more .seller-products-quick-filter.is-active,
    .seller-rail-more .seller-products-quick-filter[aria-pressed="true"] {
        border: 0 !important;
        background: #fff5df !important;
        color: #98650b !important;
        box-shadow: none !important;
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        .seller-rail-stat {
            min-height: 56px !important;
            padding-top: 7px !important;
            padding-bottom: 7px !important;
        }

        .seller-rail-health {
            padding-top: 10px !important;
        }
    }



    /* ============================================================
       FLASH SALE — COMPACT INLINE BADGE
       Small red label + yellow bolt + dark live countdown.
       ============================================================ */
    #productsGrid .seller-product-flash-sale {
        display: inline-flex !important;
        width: max-content !important;
        max-width: 100% !important;
        min-height: 0 !important;
        align-self: flex-start !important;
        align-items: stretch !important;
        gap: 0 !important;
        margin-top: 11px !important;
        overflow: hidden !important;
        border: 0 !important;
        border-radius: 7px !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: 0 4px 10px rgba(74, 26, 27, .10) !important;
    }

    #productsGrid .seller-product-flash-sale-label {
        display: inline-flex !important;
        min-width: 0 !important;
        align-items: center !important;
        justify-content: center !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: #ff5148 !important;
        padding: 6px 8px !important;
        color: #ffffff !important;
        font-size: 8.5px !important;
        line-height: 1 !important;
        font-weight: 750 !important;
        letter-spacing: .005em !important;
        white-space: nowrap !important;
        box-shadow: none !important;
    }

    #productsGrid .seller-product-flash-sale-bolt {
        display: inline-grid !important;
        width: 20px !important;
        min-width: 20px !important;
        flex: 0 0 20px !important;
        place-items: center !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: linear-gradient(90deg, #ff5148 0 42%, #45262b 42% 100%) !important;
        color: #ffd928 !important;
        box-shadow: none !important;
    }

    #productsGrid .seller-product-flash-sale-bolt svg {
        width: 14px !important;
        height: 14px !important;
        filter: drop-shadow(0 1px 1px rgba(0, 0, 0, .18));
    }

    #productsGrid .seller-product-flash-sale-countdown {
        display: inline-flex !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 72px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: #45262b !important;
        padding: 6px 8px 6px 5px !important;
        color: #fff4f4 !important;
        font-variant-numeric: tabular-nums !important;
        font-size: 8.5px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: .055em !important;
        white-space: nowrap !important;
        box-shadow: none !important;
    }

    #productsGrid .seller-product-flash-sale-countdown--scheduled {
        min-width: 94px !important;
        letter-spacing: .015em !important;
        text-transform: none !important;
    }

    #productsGrid .seller-product-card--reference:has(.seller-product-flash-sale) .seller-product-card-divider {
        margin-top: 11px !important;
    }

    @media (max-width: 420px) {
        #productsGrid .seller-product-flash-sale-label,
        #productsGrid .seller-product-flash-sale-countdown {
            font-size: 7.8px !important;
        }

        #productsGrid .seller-product-flash-sale-label {
            padding-inline: 7px !important;
        }

        #productsGrid .seller-product-flash-sale-countdown {
            min-width: 66px !important;
            padding-inline: 5px 7px !important;
        }
    }


    /*
     |--------------------------------------------------------------------------
     | CATALOG CONTROL — SIMPLE / CLEAN / MODERN
     |--------------------------------------------------------------------------
     | Final visual pass for the left inventory rail.
    */
    .seller-catalog-rail-shell {
        border: 1px solid #e8e2da !important;
        border-radius: 22px !important;
        background: #ffffff !important;
        padding: 18px !important;
        box-shadow:
            0 14px 32px rgba(39, 31, 22, .045),
            0 2px 7px rgba(39, 31, 22, .018) !important;
    }

    .seller-catalog-rail-shell > div:first-child {
        align-items: flex-start !important;
    }

    .seller-catalog-rail-shell > div:first-child p {
        color: #8c8177 !important;
        font-size: 8px !important;
        font-weight: 700 !important;
        letter-spacing: .19em !important;
    }

    .seller-catalog-rail-shell > div:first-child h2 {
        margin-top: 4px !important;
        color: #1f1d1b !important;
        font-size: 18px !important;
        line-height: 1.12 !important;
        font-weight: 700 !important;
        letter-spacing: -.035em !important;
    }

    .seller-catalog-rail-shell > div:first-child > span {
        width: 42px !important;
        height: 42px !important;
        border: 1px solid #ead9b9 !important;
        border-radius: 13px !important;
        background: #fffaf0 !important;
        color: #bd7806 !important;
        box-shadow: none !important;
    }

    .seller-catalog-rail-shell > div:first-child > span svg {
        width: 19px !important;
        height: 19px !important;
    }

    .seller-catalog-rail-shell > .mt-4.grid.grid-cols-2 {
        margin-top: 16px !important;
        gap: 9px !important;
    }

    .seller-rail-primary-action,
    .seller-rail-secondary-action {
        height: 42px !important;
        border-radius: 12px !important;
        font-size: 8.6px !important;
        font-weight: 650 !important;
    }

    .seller-rail-primary-action {
        border: 0 !important;
        background: #d99000 !important;
        color: #ffffff !important;
        box-shadow: 0 8px 18px rgba(196, 126, 0, .14) !important;
    }

    .seller-rail-primary-action:hover {
        background: #ca8500 !important;
        box-shadow: 0 9px 20px rgba(196, 126, 0, .17) !important;
    }

    .seller-rail-secondary-action {
        border: 1px solid #dfd8d0 !important;
        background: #ffffff !important;
        color: #625d59 !important;
        box-shadow: none !important;
    }

    .seller-rail-secondary-action:hover {
        border-color: #d5ccc2 !important;
        background: #fcfbf9 !important;
        color: #3e3935 !important;
    }

    .seller-catalog-rail-shell > .mt-5 {
        margin-top: 18px !important;
    }

    .seller-catalog-rail-shell > .mt-5 > .mb-2\.5 {
        margin-bottom: 10px !important;
    }

    .seller-catalog-rail-shell > .mt-5 > .mb-2\.5 h3 {
        color: #24211f !important;
        font-size: 11px !important;
        font-weight: 700 !important;
    }

    .seller-live-catalog {
        color: #727871 !important;
        font-size: 8px !important;
    }

    .seller-live-catalog > span {
        width: 7px !important;
        height: 7px !important;
        background: #2f9562 !important;
        box-shadow: 0 0 0 3px rgba(47, 149, 98, .07) !important;
    }

    .seller-inventory-overview-grid {
        gap: 10px !important;
    }

    .seller-rail-stat,
    .seller-rail-stat:nth-child(odd),
    .seller-rail-stat:nth-child(even),
    .seller-rail-stat:nth-child(n+3) {
        display: flex !important;
        min-height: 86px !important;
        align-items: center !important;
        gap: 13px !important;
        border: 1px solid #ebe6e0 !important;
        border-radius: 15px !important;
        background: #ffffff !important;
        padding: 13px 14px !important;
        box-shadow: none !important;
    }

    .seller-rail-stat:hover {
        border-color: #e2dbd3 !important;
        background: #ffffff !important;
        transform: none !important;
        box-shadow: 0 5px 14px rgba(39, 31, 22, .025) !important;
    }

    /* Requested: no icon containers — icons only, slightly larger. */
    .seller-rail-stat-icon {
        display: grid !important;
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        flex: 0 0 38px !important;
        place-items: center !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
    }

    .seller-rail-stat-icon svg {
        width: 27px !important;
        height: 27px !important;
        stroke-width: 1.85 !important;
    }

    .seller-rail-stat-copy {
        min-width: 0 !important;
        flex: 1 1 auto !important;
    }

    .seller-rail-stat-label {
        margin: 0 0 5px !important;
        color: #77736f !important;
        font-size: 9px !important;
        line-height: 1.15 !important;
        font-weight: 500 !important;
    }

    .seller-rail-stat-value,
    .seller-rail-stat span[id] {
        display: block !important;
        color: #161821 !important;
        font-size: 23px !important;
        line-height: 1 !important;
        font-weight: 750 !important;
        letter-spacing: -.045em !important;
    }

    .seller-rail-health {
        margin-top: 17px !important;
        padding: 17px 0 3px !important;
        border: 0 !important;
        border-top: 1px solid #ebe5de !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .seller-rail-health > div:first-child {
        align-items: flex-start !important;
    }

    .seller-rail-health > div:first-child p {
        color: #77716b !important;
        font-size: 8px !important;
        letter-spacing: .18em !important;
    }

    #productsCatalogHealthScore {
        font-size: 31px !important;
        font-weight: 750 !important;
        letter-spacing: -.06em !important;
    }

    #productsCatalogHealthLabel {
        border: 0 !important;
        border-radius: 999px !important;
        padding: 6px 10px !important;
        font-size: 7.2px !important;
        font-weight: 650 !important;
        box-shadow: none !important;
    }

    .seller-rail-health .seller-catalog-health-progress {
        height: 6px !important;
        margin-top: 12px !important;
        overflow: hidden !important;
        border-radius: 999px !important;
        background: #ebe8e4 !important;
        box-shadow: none !important;
    }

    #productsCatalogAttention {
        margin-top: 9px !important;
        color: #817c77 !important;
        font-size: 8px !important;
        line-height: 1.4 !important;
        font-weight: 450 !important;
    }

    .seller-rail-health > .mt-3.grid {
        margin-top: 12px !important;
        padding-top: 11px !important;
        border-top: 1px solid #ebe5de !important;
        gap: 7px 12px !important;
        color: #76716c !important;
        font-size: 7.6px !important;
    }

    .seller-rail-more {
        margin-top: 10px !important;
        padding-top: 10px !important;
        border-top: 1px solid #ebe5de !important;
    }

    .seller-rail-more-toggle {
        height: 38px !important;
        border: 0 !important;
        border-radius: 10px !important;
        background: transparent !important;
        padding: 0 5px !important;
        color: #47423e !important;
        font-size: 8.6px !important;
        font-weight: 600 !important;
        box-shadow: none !important;
    }

    .seller-rail-more-toggle:hover {
        background: #faf8f5 !important;
        color: #2d2926 !important;
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        .seller-catalog-rail-shell {
            padding: 15px !important;
        }

        .seller-rail-stat,
        .seller-rail-stat:nth-child(odd),
        .seller-rail-stat:nth-child(even),
        .seller-rail-stat:nth-child(n+3) {
            min-height: 76px !important;
            padding: 11px 12px !important;
        }

        .seller-rail-stat-icon {
            width: 34px !important;
            height: 34px !important;
            min-width: 34px !important;
            flex-basis: 34px !important;
        }

        .seller-rail-stat-icon svg {
            width: 24px !important;
            height: 24px !important;
        }

        .seller-rail-stat-value,
        .seller-rail-stat span[id] {
            font-size: 21px !important;
        }
    }

    @media (max-width: 639px) {
        .seller-catalog-rail-shell {
            padding: 15px !important;
            border-radius: 18px !important;
        }

        .seller-rail-stat,
        .seller-rail-stat:nth-child(odd),
        .seller-rail-stat:nth-child(even),
        .seller-rail-stat:nth-child(n+3) {
            min-height: 78px !important;
            gap: 10px !important;
            padding: 11px !important;
        }

        .seller-rail-stat-icon {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            flex-basis: 32px !important;
        }

        .seller-rail-stat-icon svg {
            width: 23px !important;
            height: 23px !important;
        }

        .seller-rail-stat-value,
        .seller-rail-stat span[id] {
            font-size: 20px !important;
        }
    }



    /*
     |--------------------------------------------------------------------------
     | CATALOG CONTROL — V2 ALIGNMENT / COLOR FIX
     |--------------------------------------------------------------------------
     | Explicit icon colors, slightly smaller icons, and equal health stats.
    */

    /* Inventory icons: no box, slightly smaller, explicit colors. */
    .seller-inventory-overview-grid .seller-rail-stat-icon {
        width: 31px !important;
        height: 31px !important;
        min-width: 31px !important;
        flex: 0 0 31px !important;
        color: #c98208 !important;
    }

    .seller-inventory-overview-grid .seller-rail-stat-icon svg {
        width: 21px !important;
        height: 21px !important;
        stroke: currentColor !important;
        stroke-width: 1.9 !important;
    }

    .seller-inventory-overview-grid .seller-rail-stat:nth-child(1) .seller-rail-stat-icon {
        color: #c98208 !important;
    }

    .seller-inventory-overview-grid .seller-rail-stat:nth-child(2) .seller-rail-stat-icon {
        color: #2f9562 !important;
    }

    .seller-inventory-overview-grid .seller-rail-stat:nth-child(3) .seller-rail-stat-icon {
        color: #c98208 !important;
    }

    .seller-inventory-overview-grid .seller-rail-stat:nth-child(4) .seller-rail-stat-icon {
        color: #c94f55 !important;
    }

    .seller-inventory-overview-grid .seller-rail-stat {
        gap: 11px !important;
    }

    /* Four equal Catalog Health statistics in one aligned row. */
    .seller-health-status-grid {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        align-items: stretch !important;
        gap: 0 !important;
        margin-top: 12px !important;
        padding-top: 12px !important;
        border-top: 1px solid #ebe5de !important;
    }

    .seller-health-status-item {
        display: flex !important;
        min-width: 0 !important;
        min-height: 34px !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 3px !important;
        padding: 0 5px !important;
        color: #817a73 !important;
        text-align: center !important;
    }

    .seller-health-status-item + .seller-health-status-item {
        border-left: 1px solid #eee8e1 !important;
    }

    .seller-health-status-item strong {
        display: block !important;
        font-size: 10px !important;
        line-height: 1 !important;
        font-weight: 750 !important;
        font-variant-numeric: tabular-nums !important;
    }

    .seller-health-status-item > span {
        display: block !important;
        overflow: hidden !important;
        max-width: 100% !important;
        color: #817a73 !important;
        font-size: 6.7px !important;
        line-height: 1.15 !important;
        font-weight: 500 !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
    }

    .seller-health-status-item--approved strong {
        color: #2f9562 !important;
    }

    .seller-health-status-item--review strong {
        color: #3f73a4 !important;
    }

    .seller-health-status-item--low strong {
        color: #c98208 !important;
    }

    .seller-health-status-item--out strong {
        color: #c94f55 !important;
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        .seller-inventory-overview-grid .seller-rail-stat-icon {
            width: 29px !important;
            height: 29px !important;
            min-width: 29px !important;
            flex-basis: 29px !important;
        }

        .seller-inventory-overview-grid .seller-rail-stat-icon svg {
            width: 20px !important;
            height: 20px !important;
        }

        .seller-health-status-item {
            padding-inline: 4px !important;
        }

        .seller-health-status-item > span {
            font-size: 6.3px !important;
        }
    }

    @media (max-width: 639px) {
        .seller-inventory-overview-grid .seller-rail-stat-icon {
            width: 29px !important;
            height: 29px !important;
            min-width: 29px !important;
            flex-basis: 29px !important;
        }

        .seller-inventory-overview-grid .seller-rail-stat-icon svg {
            width: 20px !important;
            height: 20px !important;
        }

        .seller-health-status-item {
            min-height: 32px !important;
            padding-inline: 3px !important;
        }

        .seller-health-status-item strong {
            font-size: 9px !important;
        }

        .seller-health-status-item > span {
            font-size: 6.1px !important;
        }
    }



    /*
     |--------------------------------------------------------------------------
     | PRODUCT SEARCH — IMAGE SUGGESTIONS
     |--------------------------------------------------------------------------
     | Fast client-side autocomplete from the already-loaded product library.
    */
    .seller-product-search-suggestions {
        position: absolute !important;
        z-index: 120 !important;
        top: calc(100% + 7px) !important;
        left: 0 !important;
        right: 0 !important;
        overflow: hidden !important;
        max-height: 390px !important;
        overflow-y: auto !important;
        border: 1px solid #e4ddd5 !important;
        border-radius: 14px !important;
        background: rgba(255, 255, 255, .985) !important;
        padding: 6px !important;
        box-shadow:
            0 20px 45px rgba(34, 27, 20, .11),
            0 5px 14px rgba(34, 27, 20, .045) !important;
        scrollbar-width: thin;
        scrollbar-color: #d9d1c8 transparent;
    }

    .seller-product-search-suggestion {
        display: flex !important;
        width: 100% !important;
        min-width: 0 !important;
        cursor: pointer !important;
        align-items: center !important;
        gap: 10px !important;
        border: 0 !important;
        border-radius: 10px !important;
        background: transparent !important;
        padding: 8px !important;
        text-align: left !important;
        transition:
            background-color .12s ease,
            box-shadow .12s ease !important;
    }

    .seller-product-search-suggestion:hover,
    .seller-product-search-suggestion:focus-visible,
    .seller-product-search-suggestion.is-keyboard-active {
        outline: none !important;
        background: #fff8ea !important;
        box-shadow: inset 0 0 0 1px #f0dfbc !important;
    }

    .seller-product-search-suggestion + .seller-product-search-suggestion {
        margin-top: 2px !important;
    }

    .seller-product-search-thumb {
        display: grid !important;
        width: 44px !important;
        height: 44px !important;
        min-width: 44px !important;
        flex: 0 0 44px !important;
        place-items: center !important;
        overflow: hidden !important;
        border: 1px solid #ebe5de !important;
        border-radius: 10px !important;
        background: #f6f3ef !important;
        color: #9b9187 !important;
    }

    .seller-product-search-thumb img {
        display: block !important;
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }

    .seller-product-search-thumb svg {
        width: 17px !important;
        height: 17px !important;
    }

    .seller-product-search-copy {
        min-width: 0 !important;
        flex: 1 1 auto !important;
    }

    .seller-product-search-name {
        overflow: hidden !important;
        color: #2a2521 !important;
        font-size: 9px !important;
        line-height: 1.25 !important;
        font-weight: 700 !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    .seller-product-search-meta {
        display: flex !important;
        min-width: 0 !important;
        align-items: center !important;
        gap: 5px !important;
        margin-top: 4px !important;
        color: #8a8178 !important;
        font-size: 7px !important;
        line-height: 1.2 !important;
        font-weight: 500 !important;
    }

    .seller-product-search-meta span {
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    .seller-product-search-meta-dot {
        width: 3px !important;
        height: 3px !important;
        flex: 0 0 3px !important;
        border-radius: 999px !important;
        background: #cfc7bf !important;
    }

    .seller-product-search-side {
        display: flex !important;
        min-width: 74px !important;
        flex: 0 0 auto !important;
        flex-direction: column !important;
        align-items: flex-end !important;
        gap: 5px !important;
    }

    .seller-product-search-price {
        color: #b87908 !important;
        font-size: 8.5px !important;
        line-height: 1 !important;
        font-weight: 750 !important;
        white-space: nowrap !important;
    }

    .seller-product-search-status {
        display: inline-flex !important;
        min-height: 20px !important;
        align-items: center !important;
        border-radius: 999px !important;
        padding: 0 7px !important;
        font-size: 6.4px !important;
        line-height: 1 !important;
        font-weight: 650 !important;
        white-space: nowrap !important;
    }

    .seller-product-search-status--approved {
        background: #e7f6ed !important;
        color: #267c50 !important;
    }

    .seller-product-search-status--pending {
        background: #edf4fb !important;
        color: #52789c !important;
    }

    .seller-product-search-status--danger {
        background: #fff0f0 !important;
        color: #af5555 !important;
    }

    .seller-product-search-status--neutral {
        background: #f3f0ec !important;
        color: #746c64 !important;
    }

    .seller-product-search-empty {
        padding: 16px 14px !important;
        text-align: center !important;
    }

    .seller-product-search-empty strong {
        display: block !important;
        color: #4c453e !important;
        font-size: 8.5px !important;
        font-weight: 700 !important;
    }

    .seller-product-search-empty span {
        display: block !important;
        margin-top: 4px !important;
        color: #948b82 !important;
        font-size: 7px !important;
    }

    #productsGrid [data-product-item].seller-product-search-hit {
        border-color: #deb35d !important;
        box-shadow:
            0 0 0 3px rgba(217, 144, 0, .09),
            0 12px 28px rgba(46, 38, 29, .052) !important;
        transition: border-color .2s ease, box-shadow .2s ease !important;
    }

    @media (max-width: 639px) {
        .seller-product-search-suggestions {
            max-height: 330px !important;
            border-radius: 12px !important;
        }

        .seller-product-search-thumb {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
            flex-basis: 40px !important;
        }

        .seller-product-search-side {
            min-width: 66px !important;
        }

        .seller-product-search-name {
            font-size: 8.4px !important;
        }
    }



    /*
     |--------------------------------------------------------------------------
     | ADD PRODUCT MODAL — CLEAN MODERN GOLD UI FINAL PASS
     |--------------------------------------------------------------------------
     | Scoped visual refinement for the Seller Add Product modal only.
     | Keeps all current form fields / JS hooks / backend behavior intact.
     */

    #sellerAddProductModal {
        backdrop-filter: blur(6px) !important;
        background: rgba(17, 15, 12, .42) !important;
    }

    #sellerAddProductModal .seller-add-modal-shell {
        max-width: 1180px !important;
        max-height: 92vh !important;
        border: 1px solid #e8dfd2 !important;
        border-radius: 22px !important;
        background: #ffffff !important;
        box-shadow:
            0 34px 90px rgba(31, 24, 16, .18),
            0 10px 24px rgba(31, 24, 16, .06) !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 {
        border-bottom: 1px solid #eee6dd !important;
        background: #ffffff !important;
        padding-top: 16px !important;
        padding-bottom: 15px !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 > .relative > .flex.min-w-0.items-start.gap-4 > div:first-child {
        width: 46px !important;
        height: 46px !important;
        border: 1px solid #ecdab7 !important;
        border-radius: 15px !important;
        background: #fff9ee !important;
        color: #bf7f0d !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 h3 {
        color: #1f1c19 !important;
        font-size: 24px !important;
        line-height: 1.1 !important;
        letter-spacing: -.04em !important;
        font-weight: 700 !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 p.text-\[8px\].font-bold.uppercase {
        color: #a06a0b !important;
        letter-spacing: .16em !important;
    }

    #sellerAddProductModal #closeAddProductModal {
        border: 1px solid #e7ded2 !important;
        border-radius: 12px !important;
        background: #ffffff !important;
        color: #70665b !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal #closeAddProductModal:hover {
        border-color: #d8c5a2 !important;
        background: #fffaf1 !important;
        color: #a06a0b !important;
    }

    #sellerAddProductModal .seller-add-form-body {
        background: #fbfaf8 !important;
        padding-top: 18px !important;
        padding-bottom: 108px !important;
    }

    /* Compact progress header */
    #sellerAddProductModal .seller-add-progress {
        border: 1px solid #ece4d9 !important;
        border-radius: 18px !important;
        background: #ffffff !important;
        padding: 14px 16px !important;
        box-shadow: 0 4px 14px rgba(43,33,21,.028) !important;
    }

    #sellerAddProductModal .seller-add-progress-head {
        margin-bottom: 12px !important;
        align-items: center !important;
    }

    #sellerAddProductModal .seller-add-progress-head p.text-\[10px\].font-bold {
        color: #2f2924 !important;
        font-size: 11px !important;
    }

    #sellerAddProductModal .seller-add-progress-head p.text-\[7\.5px\] {
        color: #90857a !important;
        font-size: 7.6px !important;
    }

    #sellerAddProductModal .seller-add-progress-label {
        border: 1px solid #e7ddcf !important;
        border-radius: 999px !important;
        background: #fffaf1 !important;
        padding: 6px 10px !important;
        color: #a16b0e !important;
        font-size: 7.2px !important;
        font-weight: 700 !important;
        letter-spacing: .02em !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-add-progress-track-wrap {
        margin-top: 0 !important;
    }

    #sellerAddProductModal .seller-add-progress-track {
        top: 13px !important;
        left: 40px !important;
        right: 40px !important;
        height: 2px !important;
        background: #eee8df !important;
    }

    #sellerAddProductModal .seller-add-progress-fill {
        background: #d3941a !important;
    }

    #sellerAddProductModal .seller-add-progress-step {
        gap: 8px !important;
    }

    #sellerAddProductModal .seller-add-progress-circle {
        width: 27px !important;
        height: 27px !important;
        border: 1px solid #eadfcf !important;
        background: #ffffff !important;
        color: #887b6d !important;
        font-size: 9px !important;
        font-weight: 700 !important;
        box-shadow: 0 2px 8px rgba(43,33,21,.04) !important;
    }

    #sellerAddProductModal .seller-add-progress-step.is-active .seller-add-progress-circle,
    #sellerAddProductModal .seller-add-progress-step[aria-current="step"] .seller-add-progress-circle,
    #sellerAddProductModal .seller-add-progress-step.is-complete .seller-add-progress-circle {
        border-color: #cf9422 !important;
        background: #d3941a !important;
        color: #ffffff !important;
        box-shadow: 0 8px 16px rgba(211,148,26,.18) !important;
    }

    #sellerAddProductModal .seller-add-progress-copy strong {
        color: #2e2924 !important;
        font-size: 8.2px !important;
        font-weight: 700 !important;
    }

    #sellerAddProductModal .seller-add-progress-copy small {
        color: #9b9185 !important;
        font-size: 6.7px !important;
        line-height: 1.1 !important;
    }

    /* Main sections */
    #sellerAddProductModal .seller-add-flow-section,
    #sellerAddProductModal .seller-add-subsection,
    #sellerAddProductModal .seller-add-optional-card {
        border: 1px solid #e8dfd4 !important;
        border-radius: 18px !important;
        background: #ffffff !important;
        box-shadow: 0 5px 16px rgba(43,33,21,.025) !important;
        overflow: hidden !important;
    }

    #sellerAddProductModal .seller-add-flow-section,
    #sellerAddProductModal .seller-add-subsection {
        padding: 16px !important;
    }

    #sellerAddProductModal .seller-add-section-number {
        width: 24px !important;
        height: 24px !important;
        border: 1px solid #ead8b4 !important;
        background: #fff7e8 !important;
        color: #ad7312 !important;
        font-size: 8px !important;
        font-weight: 700 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-add-flow-section p.text-\[12px\].font-bold,
    #sellerAddProductModal .seller-add-subsection p.text-\[11px\].font-bold,
    #sellerAddProductModal .seller-add-optional-card span.block.text-\[10px\].font-bold {
        color: #2e2924 !important;
        letter-spacing: -.02em !important;
    }

    #sellerAddProductModal .seller-add-flow-section p.text-\[8px\],
    #sellerAddProductModal .seller-add-subsection p.text-\[7\.5px\],
    #sellerAddProductModal .seller-add-optional-card p.text-\[8px\] {
        color: #92887c !important;
    }

    /* Use subtle gold icon chips across the modal */
    #sellerAddProductModal .bg-\[\#fff8ea\],
    #sellerAddProductModal .bg-\[\#fffaf1\],
    #sellerAddProductModal .bg-\[\#fffaf0\] {
        background: #fffaf1 !important;
    }

    #sellerAddProductModal .border-\[\#ead9b7\],
    #sellerAddProductModal .border-\[\#e5e7eb\],
    #sellerAddProductModal .border-\[\#e8e0d6\] {
        border-color: #eadfce !important;
    }

    /* Cleaner uploader / media preview */
    #sellerAddProductModal #sellerCoverPreviewEmpty,
    #sellerAddProductModal #sellerGalleryEmpty {
        border-color: #ded6cb !important;
        background: #fcfbf9 !important;
        color: #8f857a !important;
    }

    #sellerAddProductModal label[for],
    #sellerAddProductModal .seller-add-flow-section label,
    #sellerAddProductModal .seller-add-subsection label,
    #sellerAddProductModal .seller-add-optional-card label {
        color: #4f473f !important;
    }

    /* Variant table styling */
    #sellerAddProductModal #sellerVariantSection {
        background: #ffffff !important;
    }

    #sellerAddProductModal #sellerVariantSection .inline-flex.h-9.items-center.justify-center.gap-2.rounded-xl.bg-\[\#d48f08\] {
        background: #d3941a !important;
        color: #ffffff !important;
        box-shadow: 0 8px 18px rgba(211,148,26,.16) !important;
    }

    #sellerAddProductModal #sellerVariantSection .inline-flex.h-9.items-center.justify-center.gap-2.rounded-xl.bg-\[\#d48f08\]:hover {
        background: #bf8515 !important;
    }

    #sellerAddProductModal #sellerManualVariantEmpty {
        border: 1px dashed #ddd4c8 !important;
        background: #fcfbf9 !important;
    }

    #sellerAddProductModal #sellerVariantTableWrap {
        border-top: 1px solid #f1ebe2 !important;
        padding-top: 12px !important;
    }

    #sellerAddProductModal .seller-manual-variant-card {
        border-radius: 15px !important;
        border: 1px solid #e6ddd1 !important;
        background: #ffffff !important;
        box-shadow: 0 4px 14px rgba(43,33,21,.022) !important;
    }

    #sellerAddProductModal .seller-variant-image-picker {
        border-radius: 12px !important;
        border-color: #ddd4c8 !important;
        background: #fcfbf9 !important;
    }

    /* Optional cards use clean collapsible treatment */
    #sellerAddProductModal .seller-add-optional-card > summary {
        min-height: 58px !important;
        padding: 14px 16px !important;
        background: #ffffff !important;
        list-style: none !important;
    }

    #sellerAddProductModal .seller-add-optional-card[open] > summary {
        border-bottom: 1px solid #f1ebe2 !important;
    }

    #sellerAddProductModal .seller-add-details-chevron {
        color: #9a7c4c !important;
    }

    /* Live promotion preview */
    #sellerAddProductModal #sellerPromotionPreview {
        border: 1px solid #f0e1b3 !important;
        border-radius: 14px !important;
        background: linear-gradient(180deg, #fffaf1 0%, #fff7e7 100%) !important;
    }

    #sellerAddProductModal #sellerPromotionSalePrice {
        color: #c88608 !important;
        font-weight: 700 !important;
    }

    /* Footer */
    #sellerAddProductModal .sticky.bottom-0 {
        border-top: 1px solid #eee6dd !important;
        background: rgba(255,255,255,.96) !important;
        backdrop-filter: blur(10px) !important;
        padding-top: 14px !important;
        padding-bottom: 14px !important;
    }

    #sellerAddProductModal #sellerPreviewProduct,
    #sellerAddProductModal #cancelAddProductModal {
        border-color: #ddd4c7 !important;
        background: #ffffff !important;
        color: #5f564c !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal #sellerPreviewProduct:hover,
    #sellerAddProductModal #cancelAddProductModal:hover {
        border-color: #d7c39d !important;
        background: #fffaf1 !important;
        color: #91620f !important;
    }

    #sellerAddProductModal #sellerSubmitProduct {
        background: #d3941a !important;
        color: #ffffff !important;
        box-shadow: 0 10px 24px rgba(211,148,26,.22) !important;
    }

    #sellerAddProductModal #sellerSubmitProduct:hover {
        background: #bf8515 !important;
    }

    /* Desktop layout inspired by the chosen mockup */
    @media (min-width: 1100px) {
        #sellerAddProductModal .seller-add-form-body > .space-y-4 {
            display: grid !important;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr) !important;
            gap: 16px !important;
            align-items: start !important;
        }

        #sellerAddProductModal #sellerAddBasics {
            grid-column: 1 !important;
            grid-row: 1 !important;
        }

        #sellerAddProductModal #sellerAddMedia {
            grid-column: 1 !important;
            grid-row: 2 !important;
        }

        #sellerAddProductModal #sellerSimpleInventorySection,
        #sellerAddProductModal #sellerVariantSection {
            grid-column: 2 !important;
            grid-row: 1 !important;
        }

        #sellerAddProductModal .seller-add-optional-card:first-of-type {
            grid-column: 2 !important;
            grid-row: 2 !important;
        }

        #sellerAddProductModal .seller-add-optional-card:last-of-type {
            grid-column: 1 / span 2 !important;
            grid-row: 3 !important;
        }

        #sellerAddProductModal #sellerAddMedia #sellerGalleryPreview {
            max-height: 220px !important;
        }
    }

    @media (max-width: 1099px) {
        #sellerAddProductModal .seller-add-progress-copy small {
            display: none !important;
        }
    }

    @media (max-width: 767px) {
        #sellerAddProductModal .seller-add-modal-shell {
            max-height: 96vh !important;
            border-radius: 18px !important;
        }

        #sellerAddProductModal .seller-add-form-body {
            padding: 14px 14px 100px !important;
        }

        #sellerAddProductModal .seller-add-progress {
            padding: 12px !important;
        }

        #sellerAddProductModal .seller-add-progress-track {
            left: 30px !important;
            right: 30px !important;
        }

        #sellerAddProductModal .seller-add-progress-step {
            flex-direction: column !important;
            text-align: center !important;
            gap: 5px !important;
        }

        #sellerAddProductModal .seller-add-progress-copy strong {
            font-size: 7.2px !important;
        }
    }



    /*
     |--------------------------------------------------------------------------
     | ADD PRODUCT MODAL — EXACT REFERENCE LAYOUT / CONFLICT RESET
     |--------------------------------------------------------------------------
     | This final block intentionally overrides the older A4/single-column
     | experiments above. It only affects #sellerAddProductModal.
     */

    #sellerAddProductModal,
    #sellerAddProductModal * {
        font-family: "Poppins", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
        box-sizing: border-box;
    }

    #sellerAddProductModal {
        padding: 16px !important;
        background: rgba(28, 25, 21, .42) !important;
    }

    #sellerAddProductModal .seller-add-modal-shell {
        width: min(96vw, 1180px) !important;
        max-width: 1180px !important;
        max-height: 94vh !important;
        border: 1px solid #e7dfd5 !important;
        border-radius: 20px !important;
        background: #ffffff !important;
        overflow: hidden !important;
        box-shadow:
            0 32px 90px rgba(30, 24, 17, .18),
            0 8px 24px rgba(30, 24, 17, .06) !important;
    }

    /* Compact modal header */
    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 {
        min-height: 82px !important;
        padding: 15px 22px !important;
        border-bottom: 1px solid #eee7de !important;
        background: #fff !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 > .relative {
        align-items: center !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 .h-12.w-12 {
        width: 44px !important;
        height: 44px !important;
        border: 1px solid #ead9b8 !important;
        border-radius: 14px !important;
        background: #fff9ee !important;
        color: #bf7d0d !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 h3 {
        margin-top: 2px !important;
        color: #191714 !important;
        font-size: 22px !important;
        line-height: 1.05 !important;
        font-weight: 700 !important;
        letter-spacing: -.04em !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 h3 + p {
        margin-top: 5px !important;
        max-width: 760px !important;
        color: #7e756b !important;
        font-size: 8.5px !important;
        line-height: 1.45 !important;
    }

    #sellerAddProductModal #closeAddProductModal {
        width: 38px !important;
        height: 38px !important;
        border: 1px solid #e5ddd3 !important;
        border-radius: 12px !important;
        background: #fff !important;
        color: #746b62 !important;
    }

    #sellerAddProductModal #sellerAddProductForm {
        overflow-y: auto !important;
        background: #fbfaf8 !important;
        scrollbar-width: thin;
        scrollbar-color: #cfc6bb transparent;
    }

    #sellerAddProductModal .seller-add-form-body {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 12px 16px 92px !important;
        background: #fbfaf8 !important;
    }

    /* Progress: no large nested card/header — just the compact stepper */
    #sellerAddProductModal .seller-add-progress {
        width: 100% !important;
        max-width: none !important;
        margin: 0 0 12px !important;
        padding: 6px 22px 10px !important;
        border: 0 !important;
        border-bottom: 1px solid #eee7de !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-add-progress-head {
        display: none !important;
    }

    #sellerAddProductModal .seller-add-progress-track-wrap {
        margin-top: 0 !important;
    }

    #sellerAddProductModal .seller-add-progress-track {
        top: 12px !important;
        left: 12.5% !important;
        right: 12.5% !important;
        height: 1.5px !important;
        background: #e7e1d9 !important;
    }

    #sellerAddProductModal .seller-add-progress-fill {
        background: #cf8d10 !important;
    }

    #sellerAddProductModal .seller-add-progress-circle {
        width: 25px !important;
        height: 25px !important;
        border: 1px solid #e1d7c9 !important;
        background: #fff !important;
        color: #8c8175 !important;
        font-size: 7.5px !important;
        font-weight: 700 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-add-progress-step.is-active .seller-add-progress-circle,
    #sellerAddProductModal .seller-add-progress-step.is-complete .seller-add-progress-circle {
        border-color: #cf8d10 !important;
        background: #cf8d10 !important;
        color: #fff !important;
    }

    #sellerAddProductModal .seller-add-progress-copy strong {
        color: #302a24 !important;
        font-size: 7.4px !important;
        font-weight: 700 !important;
    }

    #sellerAddProductModal .seller-add-progress-copy small {
        margin-top: 1px !important;
        color: #9d948b !important;
        font-size: 5.9px !important;
    }

    /* Independent left / right columns: no shared row heights */
    #sellerAddProductModal .seller-add-exact-layout {
        display: grid !important;
        grid-template-columns: minmax(0, .98fr) minmax(0, 1.02fr) !important;
        gap: 14px 16px !important;
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        align-items: start !important;
    }

    #sellerAddProductModal .seller-add-exact-column {
        display: flex !important;
        min-width: 0 !important;
        flex-direction: column !important;
        gap: 14px !important;
        align-self: start !important;
    }

    #sellerAddProductModal .seller-add-exact-bottom {
        grid-column: 1 / -1 !important;
        min-width: 0 !important;
    }

    /* Reset old A4/flat-section rules */
    #sellerAddProductModal .seller-add-flow-section,
    #sellerAddProductModal .seller-add-subsection,
    #sellerAddProductModal #sellerVariantSection,
    #sellerAddProductModal .seller-add-optional-card {
        width: 100% !important;
        min-width: 0 !important;
        margin: 0 !important;
        border: 1px solid #e7dfd5 !important;
        border-radius: 15px !important;
        background: #fff !important;
        padding: 14px !important;
        box-shadow: none !important;
        overflow: visible !important;
    }

    #sellerAddProductModal .seller-add-section-number {
        width: 25px !important;
        height: 25px !important;
        border: 1px solid #e8d3a8 !important;
        border-radius: 8px !important;
        background: #fff8e9 !important;
        color: #a56e0e !important;
        font-size: 7px !important;
    }

    /* Inputs */
    #sellerAddProductModal input[type="text"],
    #sellerAddProductModal input[type="number"],
    #sellerAddProductModal input[type="email"],
    #sellerAddProductModal input[type="date"],
    #sellerAddProductModal input[type="datetime-local"],
    #sellerAddProductModal select,
    #sellerAddProductModal #sellerCategoryDropdownButton {
        height: 38px !important;
        min-width: 0 !important;
        border: 1px solid #dcd5cc !important;
        border-radius: 9px !important;
        background: #fff !important;
        color: #332e28 !important;
        font-size: 8.2px !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal textarea {
        width: 100% !important;
        min-width: 0 !important;
        min-height: 132px !important;
        border: 1px solid #dcd5cc !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: #332e28 !important;
        font-size: 8.2px !important;
        line-height: 1.55 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal input:focus,
    #sellerAddProductModal select:focus,
    #sellerAddProductModal textarea:focus,
    #sellerAddProductModal #sellerCategoryDropdownButton:focus {
        border-color: #ce941f !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(206,148,31,.08) !important;
    }

    #sellerAddProductModal label {
        color: #4b433b !important;
        font-size: 7.7px !important;
        font-weight: 600 !important;
    }

    /* Product information */
    #sellerAddProductModal #sellerAddBasics .mt-4.grid {
        gap: 11px !important;
    }

    #sellerAddProductModal #sellerAddBasics .seller-add-inline-optional {
        margin-top: 2px !important;
        border: 1px solid #eee7de !important;
        border-radius: 10px !important;
        background: #fff !important;
    }

    /* Photos + description: fixed useful split inside the left column */
    #sellerAddProductModal #sellerAddMedia > .mt-4.grid {
        display: grid !important;
        grid-template-columns: 170px minmax(0, 1fr) !important;
        gap: 12px !important;
        align-items: stretch !important;
    }

    #sellerAddProductModal #sellerAddMedia > .mt-4.grid > div {
        min-width: 0 !important;
    }

    #sellerAddProductModal #sellerAddMedia label.group {
        min-height: 132px !important;
        height: 132px !important;
        border-radius: 12px !important;
        padding: 10px !important;
    }

    #sellerAddProductModal #sellerImagePreviewBox {
        width: 50px !important;
        height: 50px !important;
        border-radius: 12px !important;
    }

    #sellerAddProductModal #sellerAddMedia textarea {
        min-height: 132px !important;
        height: 132px !important;
    }

    #sellerAddProductModal #sellerAddMedia > .mt-4.border-t {
        margin-top: 12px !important;
        padding-top: 12px !important;
    }

    #sellerAddProductModal #sellerGalleryPreview {
        max-height: 116px !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 7px !important;
    }

    /* Price / variants */
    #sellerAddProductModal #sellerSimpleInventorySection,
    #sellerAddProductModal #sellerVariantSection {
        border-radius: 15px !important;
        background: #fff !important;
    }

    #sellerAddProductModal #sellerEnableVariants,
    #sellerAddProductModal #sellerUseSinglePrice,
    #sellerAddProductModal #sellerAddManualVariant {
        height: 32px !important;
        border-radius: 9px !important;
        font-size: 7px !important;
    }

    #sellerAddProductModal #sellerAddManualVariant {
        background: #cf8d10 !important;
        color: #fff !important;
    }

    #sellerAddProductModal .seller-manual-variant-card {
        border: 1px solid #e8e0d6 !important;
        border-radius: 12px !important;
        background: #fff !important;
        padding: 11px !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-variant-image-picker {
        background: #fcfbf9 !important;
        border-color: #ddd5cb !important;
        border-radius: 10px !important;
    }

    /* Promotion card */
    #sellerAddProductModal .seller-add-exact-column--right > .seller-add-optional-card {
        padding: 0 !important;
        overflow: hidden !important;
    }

    #sellerAddProductModal .seller-add-exact-column--right > .seller-add-optional-card > .seller-add-optional-summary {
        min-height: 54px !important;
        padding: 11px 13px !important;
        border: 0 !important;
        background: #fff !important;
    }

    #sellerAddProductModal .seller-add-exact-column--right > .seller-add-optional-card > .seller-add-optional-body {
        border-top: 1px solid #eee7de !important;
        padding: 12px !important;
        background: #fff !important;
    }

    #sellerAddProductModal .seller-add-exact-column--right .seller-add-optional-body section > .mt-4.grid {
        grid-template-columns: 160px minmax(0, 1fr) !important;
        gap: 10px !important;
    }

    #sellerAddProductModal .seller-add-exact-column--right .seller-add-optional-body section > .mt-4.grid > div {
        min-width: 0 !important;
    }

    /* Flash Sale card stays horizontal/readable */
    #sellerAddProductModal .seller-add-exact-column--right .seller-add-optional-body section > .mt-4.grid > div:nth-child(2) {
        padding: 10px !important;
    }

    #sellerAddProductModal .seller-add-exact-column--right .seller-add-optional-body section > .mt-4.grid > div:nth-child(2) > div {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) 178px !important;
        gap: 10px !important;
        align-items: center !important;
    }

    #sellerAddProductModal .seller-add-exact-column--right .seller-add-optional-body section > .mt-4.grid > div:nth-child(2) .w-full {
        width: 100% !important;
        max-width: none !important;
    }

    #sellerAddProductModal .seller-add-exact-column--right .seller-add-optional-body section > .mt-4.grid > label {
        grid-column: 1 / -1 !important;
        padding: 10px 12px !important;
    }

    #sellerAddProductModal #sellerPromotionPreview {
        margin-top: 10px !important;
        border: 1px solid #f0e1b7 !important;
        border-radius: 11px !important;
        background: #fffaf0 !important;
        padding: 9px 11px !important;
    }

    #sellerAddProductModal #sellerPromotionSalePrice {
        color: #c98608 !important;
        font-size: 13px !important;
        font-weight: 700 !important;
    }

    /* Shipping / specifications bottom row */
    #sellerAddProductModal #sellerAddDelivery {
        width: 100% !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
        overflow: visible !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-summary {
        display: none !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
        gap: 14px !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > .border-t {
        display: none !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section {
        min-width: 0 !important;
        border: 1px solid #e7dfd5 !important;
        border-radius: 15px !important;
        background: #fff !important;
        padding: 14px !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 9px !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-3.grid {
        gap: 8px !important;
    }

    #sellerAddProductModal #sellerAddDelivery #sellerSpecificationEmpty {
        margin-top: 10px !important;
        padding: 12px !important;
        border-radius: 10px !important;
    }

    #sellerAddProductModal #sellerAddSpecification {
        height: 32px !important;
        border-radius: 9px !important;
        font-size: 7px !important;
    }

    /* Sticky footer */
    #sellerAddProductModal .sticky.bottom-0 {
        padding: 10px 18px !important;
        border-top: 1px solid #e9e1d7 !important;
        background: rgba(255,255,255,.98) !important;
        box-shadow: 0 -6px 18px rgba(28,23,17,.035) !important;
        backdrop-filter: blur(8px) !important;
    }

    #sellerAddProductModal #cancelAddProductModal,
    #sellerAddProductModal #sellerPreviewProduct,
    #sellerAddProductModal #sellerSubmitProduct {
        height: 36px !important;
        border-radius: 9px !important;
        font-size: 7.5px !important;
    }

    #sellerAddProductModal #sellerSubmitProduct {
        min-width: 154px !important;
        background: #cf8d10 !important;
        color: #fff !important;
        box-shadow: 0 7px 16px rgba(207,141,16,.16) !important;
    }

    /* Medium desktop / laptop */
    @media (min-width: 900px) and (max-width: 1099px) {
        #sellerAddProductModal .seller-add-modal-shell {
            width: min(97vw, 1040px) !important;
        }

        #sellerAddProductModal #sellerAddMedia > .mt-4.grid {
            grid-template-columns: 150px minmax(0, 1fr) !important;
        }

        #sellerAddProductModal .seller-add-exact-column--right .seller-add-optional-body section > .mt-4.grid {
            grid-template-columns: 145px minmax(0, 1fr) !important;
        }

        #sellerAddProductModal .seller-add-exact-column--right .seller-add-optional-body section > .mt-4.grid > div:nth-child(2) > div {
            grid-template-columns: minmax(0, 1fr) 160px !important;
        }
    }

    /* Tablet / mobile falls back to one readable column */
    @media (max-width: 899px) {
        #sellerAddProductModal {
            padding: 8px !important;
        }

        #sellerAddProductModal .seller-add-modal-shell {
            width: 100% !important;
            max-height: 96vh !important;
            border-radius: 16px !important;
        }

        #sellerAddProductModal .seller-add-exact-layout {
            grid-template-columns: 1fr !important;
        }

        #sellerAddProductModal .seller-add-exact-bottom {
            grid-column: 1 !important;
        }

        #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body {
            grid-template-columns: 1fr !important;
        }

        #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-summary {
            display: flex !important;
        }

        #sellerAddProductModal #sellerAddDelivery {
            border: 1px solid #e7dfd5 !important;
            border-radius: 15px !important;
            background: #fff !important;
            overflow: hidden !important;
        }

        #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body {
            padding: 12px !important;
            border-top: 1px solid #eee7de !important;
        }

        #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section {
            border: 0 !important;
            padding: 0 !important;
        }

        #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section + section {
            padding-top: 14px !important;
            border-top: 1px solid #eee7de !important;
        }
    }

    @media (max-width: 640px) {
        #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 {
            padding: 12px 14px !important;
        }

        #sellerAddProductModal .seller-add-form-body {
            padding: 10px 10px 88px !important;
        }

        #sellerAddProductModal .seller-add-progress-copy small {
            display: none !important;
        }

        #sellerAddProductModal #sellerAddMedia > .mt-4.grid {
            grid-template-columns: 1fr !important;
        }

        #sellerAddProductModal #sellerAddMedia label.group,
        #sellerAddProductModal #sellerAddMedia textarea {
            height: auto !important;
            min-height: 130px !important;
        }

        #sellerAddProductModal .seller-add-exact-column--right .seller-add-optional-body section > .mt-4.grid {
            grid-template-columns: 1fr !important;
        }

        #sellerAddProductModal .seller-add-exact-column--right .seller-add-optional-body section > .mt-4.grid > div:nth-child(2) > div {
            grid-template-columns: 1fr !important;
        }

        #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }



    /* ======================================================================
       FINAL OVERRIDE V2 — VARIANT TABLE + TIGHTER MEDIA / SHIPPING LAYOUT
       ====================================================================== */
    #sellerAddProductModal #sellerAddMedia .flex.items-center.gap-2\.5 p,
    #sellerAddProductModal #sellerAddDelivery .flex.items-start.gap-3 p,
    #sellerAddProductModal #sellerVariantSection > div:first-child p:first-child,
    #sellerAddProductModal #sellerSimpleInventorySection p.text-\[11px\] {
        font-size: 14px !important;
        line-height: 1.2 !important;
        font-weight: 700 !important;
        color: #231f19 !important;
    }

    #sellerAddProductModal #sellerAddMedia .mt-1.text-\[8px\],
    #sellerAddProductModal #sellerAddDelivery .mt-1.text-\[8px\],
    #sellerAddProductModal #sellerVariantSection .mt-1.text-\[8px\],
    #sellerAddProductModal #sellerSimpleInventorySection .mt-1.text-\[7\.5px\] {
        font-size: 10.5px !important;
        line-height: 1.55 !important;
        color: #7e756b !important;
    }

    #sellerAddProductModal #sellerAddMedia,
    #sellerAddProductModal #sellerSimpleInventorySection,
    #sellerAddProductModal #sellerVariantSection,
    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section {
        padding: 16px !important;
    }

    /* Photos & description */
    #sellerAddProductModal #sellerAddMedia > .mt-4.grid {
        grid-template-columns: 200px minmax(0, 1fr) !important;
        gap: 12px !important;
        align-items: start !important;
    }

    #sellerAddProductModal #sellerAddMedia label,
    #sellerAddProductModal #sellerAddMedia p,
    #sellerAddProductModal #sellerAddDelivery label,
    #sellerAddProductModal #sellerAddDelivery p,
    #sellerAddProductModal #sellerVariantSection label,
    #sellerAddProductModal #sellerVariantSection p,
    #sellerAddProductModal #sellerSimpleInventorySection label,
    #sellerAddProductModal #sellerSimpleInventorySection p {
        font-size: 10px !important;
    }

    #sellerAddProductModal #sellerAddMedia label.group {
        min-height: 150px !important;
        height: 150px !important;
        padding: 12px !important;
    }

    #sellerAddProductModal #sellerImagePreviewBox {
        width: 56px !important;
        height: 56px !important;
    }

    #sellerAddProductModal #sellerAddMedia textarea {
        min-height: 150px !important;
        height: 150px !important;
        font-size: 10.5px !important;
        line-height: 1.65 !important;
    }

    #sellerAddProductModal #sellerAddMedia > .mt-4.border-t {
        margin-top: 10px !important;
        padding-top: 10px !important;
    }

    #sellerAddProductModal #sellerGalleryPreview {
        max-height: 124px !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }

    #sellerAddProductModal #sellerGalleryEmpty {
        padding: 12px !important;
        font-size: 10px !important;
    }

    /* Variant section */
    #sellerAddProductModal #sellerVariantSection {
        overflow: hidden !important;
    }

    #sellerAddProductModal #sellerVariantSection > div:first-child {
        margin-bottom: 12px !important;
    }

    #sellerAddProductModal #sellerAddManualVariant,
    #sellerAddProductModal #sellerUseSinglePrice,
    #sellerAddProductModal #sellerApplyVariantDefaults {
        height: 36px !important;
        font-size: 10px !important;
        border-radius: 10px !important;
    }

    #sellerAddProductModal #sellerManualVariantEmpty {
        padding: 14px !important;
    }

    #sellerAddProductModal #sellerVariantTableWrap {
        margin-top: 12px !important;
        border-top: 1px solid #efe7dc !important;
        padding-top: 12px !important;
    }

    #sellerAddProductModal .seller-variant-table-head {
        grid-template-columns: 96px minmax(210px, 1.4fr) minmax(120px, .9fr) minmax(140px, .9fr) minmax(92px, .65fr) 56px !important;
        align-items: center !important;
        gap: 10px !important;
        padding: 0 6px 10px !important;
        color: #6b7280 !important;
        font-size: 11px !important;
        font-weight: 600 !important;
    }

    #sellerAddProductModal .seller-manual-variant-card {
        border: 1px solid #e6ddd1 !important;
        border-radius: 16px !important;
        background: #ffffff !important;
        padding: 10px 12px !important;
        box-shadow: 0 4px 14px rgba(43,33,21,.025) !important;
    }

    #sellerAddProductModal .seller-manual-variant-grid {
        display: grid !important;
        grid-template-columns: 96px minmax(210px, 1.4fr) minmax(120px, .9fr) minmax(140px, .9fr) minmax(92px, .65fr) 56px !important;
        align-items: center !important;
        gap: 10px !important;
    }

    #sellerAddProductModal .seller-manual-variant-cell {
        min-width: 0 !important;
    }

    #sellerAddProductModal .seller-manual-variant-inline-label {
        display: block !important;
        margin-bottom: 6px !important;
        color: #7a7268 !important;
        font-size: 10px !important;
        font-weight: 600 !important;
    }

    #sellerAddProductModal .seller-variant-image-picker--row {
        min-height: 76px !important;
        border-radius: 14px !important;
        background: #fcfbf9 !important;
    }

    #sellerAddProductModal .seller-manual-variant-stack {
        display: flex !important;
        flex-direction: column !important;
        gap: 7px !important;
    }

    #sellerAddProductModal .seller-variant-inline-input,
    #sellerAddProductModal .seller-variant-inline-field {
        width: 100% !important;
        height: 42px !important;
        border: 1px solid #dad2c8 !important;
        border-radius: 11px !important;
        background: #fff !important;
        color: #302a24 !important;
        font-size: 12px !important;
        line-height: 1.2 !important;
        padding: 0 12px !important;
        outline: none !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-variant-inline-input--title {
        font-weight: 700 !important;
        color: #1f2937 !important;
    }

    #sellerAddProductModal .seller-variant-inline-input--subtitle,
    #sellerAddProductModal .seller-variant-inline-field {
        font-weight: 500 !important;
        color: #4b5563 !important;
    }

    #sellerAddProductModal .seller-variant-inline-input:focus,
    #sellerAddProductModal .seller-variant-inline-field:focus {
        border-color: #ce941f !important;
        box-shadow: 0 0 0 3px rgba(206,148,31,.08) !important;
    }

    #sellerAddProductModal .seller-variant-price-wrap {
        position: relative !important;
    }

    #sellerAddProductModal .seller-variant-price-wrap > span {
        position: absolute !important;
        left: 12px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        color: #9b6d1c !important;
        z-index: 1 !important;
    }

    #sellerAddProductModal .seller-variant-inline-field--price {
        padding-left: 28px !important;
    }

    #sellerAddProductModal .seller-variant-remove-btn {
        display: inline-grid !important;
        place-items: center !important;
        width: 42px !important;
        height: 42px !important;
        border: 1px solid #f1d8da !important;
        border-radius: 12px !important;
        background: #fff !important;
        color: #ef4444 !important;
        transition: background .2s ease, border-color .2s ease !important;
    }

    #sellerAddProductModal .seller-variant-remove-btn:hover {
        background: #fff5f5 !important;
        border-color: #efb5ba !important;
    }

    /* Shipping and package section tighter */
    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid {
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 10px !important;
        align-items: end !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid > div:nth-child(5) {
        grid-column: 1 / -1 !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-3.grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 10px !important;
        margin-top: 10px !important;
    }

    #sellerAddProductModal #sellerAddDelivery input,
    #sellerAddProductModal #sellerAddDelivery select,
    #sellerAddProductModal #sellerVariantSection input {
        font-size: 11px !important;
    }

    /* Responsive fallback */
    @media (max-width: 1199px) {
        #sellerAddProductModal .seller-variant-table-head,
        #sellerAddProductModal .seller-manual-variant-grid {
            grid-template-columns: 84px minmax(180px, 1.2fr) minmax(110px, .9fr) minmax(130px, .9fr) minmax(86px, .65fr) 52px !important;
        }
    }

    @media (max-width: 899px) {
        #sellerAddProductModal #sellerAddMedia > .mt-4.grid {
            grid-template-columns: 1fr !important;
        }

        #sellerAddProductModal .seller-variant-table-head {
            display: none !important;
        }

        #sellerAddProductModal .seller-manual-variant-grid {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }

        #sellerAddProductModal .seller-manual-variant-cell--action {
            display: flex !important;
            justify-content: flex-end !important;
        }

        #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }



    /* ======================================================================
       FINAL V3 — FLAT SINGLE-FLOW ADD PRODUCT + STAGED VARIANT BUILDER
       ====================================================================== */

    /* One continuous form — remove the card-inside-card appearance. */
    #sellerAddProductModal .seller-add-exact-layout {
        display: block !important;
        width: min(100%, 980px) !important;
        margin: 0 auto !important;
    }

    #sellerAddProductModal .seller-add-exact-column,
    #sellerAddProductModal .seller-add-exact-bottom {
        display: contents !important;
    }

    #sellerAddProductModal .seller-add-flow-section,
    #sellerAddProductModal .seller-add-subsection,
    #sellerAddProductModal #sellerVariantSection,
    #sellerAddProductModal .seller-add-optional-card,
    #sellerAddProductModal #sellerAddDelivery,
    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section {
        width: 100% !important;
        margin: 0 !important;
        padding: 22px 4px !important;
        border: 0 !important;
        border-bottom: 1px solid #e9e2d8 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        overflow: visible !important;
    }

    #sellerAddProductModal .seller-add-flow-section:first-of-type {
        padding-top: 8px !important;
    }

    #sellerAddProductModal .seller-add-optional-card {
        display: block !important;
    }

    #sellerAddProductModal .seller-add-optional-card > .seller-add-optional-summary,
    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-summary {
        display: flex !important;
        min-height: 0 !important;
        padding: 0 0 14px !important;
        border: 0 !important;
        background: transparent !important;
    }

    #sellerAddProductModal .seller-add-optional-card > .seller-add-optional-summary .seller-add-details-chevron,
    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-summary .seller-add-details-chevron {
        display: none !important;
    }

    #sellerAddProductModal .seller-add-optional-body,
    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body {
        display: block !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > .border-t {
        display: block !important;
        margin: 18px 0 !important;
        border-color: #eee7de !important;
    }

    /* Readable typography, still compact. */
    #sellerAddProductModal .seller-add-flow-section p.text-\[12px\],
    #sellerAddProductModal .seller-add-subsection p.text-\[11px\],
    #sellerAddProductModal #sellerVariantSection > div:first-child p:first-child,
    #sellerAddProductModal .seller-add-optional-summary .text-\[10px\],
    #sellerAddProductModal #sellerAddDelivery p.text-\[11px\] {
        font-size: 15px !important;
        line-height: 1.25 !important;
        font-weight: 700 !important;
        color: #211d18 !important;
    }

    #sellerAddProductModal .seller-add-flow-section p.text-\[8px\],
    #sellerAddProductModal .seller-add-subsection p.text-\[7\.5px\],
    #sellerAddProductModal #sellerVariantSection .mt-1.text-\[8px\],
    #sellerAddProductModal .seller-add-optional-summary .text-\[7\.5px\],
    #sellerAddProductModal #sellerAddDelivery p.text-\[8px\] {
        font-size: 10px !important;
        line-height: 1.55 !important;
        color: #81776d !important;
    }

    #sellerAddProductModal label {
        font-size: 10px !important;
        font-weight: 600 !important;
    }

    #sellerAddProductModal input[type="text"],
    #sellerAddProductModal input[type="number"],
    #sellerAddProductModal input[type="email"],
    #sellerAddProductModal input[type="date"],
    #sellerAddProductModal input[type="datetime-local"],
    #sellerAddProductModal select,
    #sellerAddProductModal #sellerCategoryDropdownButton {
        height: 43px !important;
        font-size: 11px !important;
    }

    /* Media stays compact and close together. */
    #sellerAddProductModal #sellerAddMedia > .mt-4.grid {
        grid-template-columns: 220px minmax(0, 1fr) !important;
        gap: 14px !important;
    }

    #sellerAddProductModal #sellerAddMedia label.group,
    #sellerAddProductModal #sellerAddMedia textarea {
        min-height: 150px !important;
        height: 150px !important;
    }

    #sellerAddProductModal #sellerAddMedia > .mt-4.border-t {
        margin-top: 14px !important;
        padding-top: 14px !important;
    }

    #sellerAddProductModal #sellerGalleryPreview {
        max-height: 130px !important;
    }

    /* Price section flat. */
    #sellerAddProductModal #sellerSimpleInventorySection {
        display: block !important;
    }

    /* Promotion inner blocks lose their extra boxes. */
    #sellerAddProductModal .seller-add-optional-body section > .mt-4.grid > div,
    #sellerAddProductModal .seller-add-optional-body section > .mt-4.grid > label,
    #sellerAddProductModal #sellerPromotionPreview {
        border-radius: 10px !important;
        box-shadow: none !important;
    }

    /* Shipping is one tight section, not a collection of cards. */
    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section {
        padding: 0 !important;
        border-bottom: 0 !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid {
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 10px !important;
        margin-top: 14px !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid > div:nth-child(5) {
        grid-column: 1 / -1 !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-3.grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 10px !important;
        margin-top: 10px !important;
    }

    /* Variant header / empty state */
    #sellerAddProductModal #sellerVariantSection {
        padding-left: 4px !important;
        padding-right: 4px !important;
    }

    #sellerAddProductModal #sellerVariantSection > div:first-child {
        align-items: center !important;
    }

    #sellerAddProductModal #sellerAddManualVariant {
        height: 38px !important;
        padding-inline: 16px !important;
        border-radius: 10px !important;
        background: #d99108 !important;
        color: #fff !important;
        font-size: 10px !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal #sellerUseSinglePrice,
    #sellerAddProductModal #sellerApplyVariantDefaults {
        height: 38px !important;
        border-radius: 10px !important;
        background: #fff !important;
        font-size: 9px !important;
    }

    #sellerAddProductModal #sellerManualVariantEmpty {
        margin-top: 14px !important;
        padding: 18px 4px !important;
        border: 0 !important;
        border-top: 1px dashed #ddd3c7 !important;
        border-bottom: 1px dashed #ddd3c7 !important;
        border-radius: 0 !important;
        background: transparent !important;
    }

    #sellerAddProductModal #sellerVariantTableWrap {
        margin-top: 14px !important;
        padding-top: 12px !important;
        border-top: 1px solid #eee7de !important;
    }

    #sellerAddProductModal .seller-variant-table-head {
        display: none !important;
    }

    /* Saved variant = single clean row, no outer card. */
    #sellerAddProductModal .seller-manual-variant-row {
        margin: 0 !important;
        border: 0 !important;
        background: transparent !important;
    }

    #sellerAddProductModal .seller-manual-variant-row + .seller-manual-variant-row {
        border-top: 1px solid #eee8df !important;
    }

    #sellerAddProductModal .seller-variant-saved-row {
        display: grid !important;
        grid-template-columns: 58px minmax(150px, 1.4fr) minmax(110px, .85fr) minmax(110px, .8fr) 86px 76px !important;
        align-items: center !important;
        gap: 12px !important;
        min-height: 78px !important;
        padding: 10px 4px !important;
    }

    #sellerAddProductModal .seller-variant-saved-image {
        display: grid !important;
        width: 54px !important;
        height: 54px !important;
        place-items: center !important;
        overflow: hidden !important;
        border: 1px solid #e5ddd3 !important;
        border-radius: 10px !important;
        background: #f7f4ef !important;
        color: #958a7f !important;
    }

    #sellerAddProductModal .seller-variant-saved-main strong,
    #sellerAddProductModal .seller-variant-saved-price {
        display: block !important;
        color: #25211d !important;
        font-size: 11px !important;
        font-weight: 700 !important;
    }

    #sellerAddProductModal .seller-variant-saved-main span,
    #sellerAddProductModal .seller-variant-saved-sku,
    #sellerAddProductModal .seller-variant-saved-stock {
        display: block !important;
        margin-top: 3px !important;
        color: #857c72 !important;
        font-size: 9px !important;
        font-weight: 500 !important;
    }

    #sellerAddProductModal .seller-variant-saved-actions {
        display: flex !important;
        justify-content: flex-end !important;
        gap: 4px !important;
    }

    #sellerAddProductModal .seller-variant-row-action {
        display: grid !important;
        width: 32px !important;
        height: 32px !important;
        place-items: center !important;
        border: 0 !important;
        border-radius: 8px !important;
        background: transparent !important;
        color: #756b61 !important;
    }

    #sellerAddProductModal .seller-variant-row-action:hover {
        background: #f7f3ed !important;
        color: #9b6810 !important;
    }

    #sellerAddProductModal .seller-variant-row-action--danger:hover {
        background: #fff1f1 !important;
        color: #bd5151 !important;
    }

    /* Only the currently-open variant editor gets a light work area. */
    #sellerAddProductModal .seller-variant-editor {
        margin: 8px 0 14px !important;
        padding: 16px !important;
        border: 1px solid #e5d9c7 !important;
        border-radius: 12px !important;
        background: #fffdf9 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-variant-editor-head {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 12px !important;
        margin-bottom: 14px !important;
    }

    #sellerAddProductModal .seller-variant-editor-head strong {
        display: block !important;
        color: #2b251f !important;
        font-size: 12px !important;
        font-weight: 700 !important;
    }

    #sellerAddProductModal .seller-variant-editor-head span {
        display: block !important;
        margin-top: 3px !important;
        color: #8f857a !important;
        font-size: 9px !important;
    }

    #sellerAddProductModal .seller-variant-editor-cancel {
        border: 0 !important;
        background: transparent !important;
        color: #8a7f74 !important;
        font-size: 9px !important;
        font-weight: 600 !important;
    }

    #sellerAddProductModal .seller-variant-editor-grid {
        display: grid !important;
        grid-template-columns: 120px repeat(5, minmax(0, 1fr)) !important;
        gap: 10px !important;
        align-items: end !important;
    }

    #sellerAddProductModal .seller-variant-editor-grid > div,
    #sellerAddProductModal .seller-variant-editor-grid label {
        min-width: 0 !important;
    }

    #sellerAddProductModal .seller-variant-editor-grid label {
        display: block !important;
        margin-bottom: 6px !important;
        color: #5d554c !important;
        font-size: 9px !important;
        font-weight: 600 !important;
    }

    #sellerAddProductModal .seller-variant-image-picker--draft {
        display: flex !important;
        height: 44px !important;
        cursor: pointer !important;
        align-items: center !important;
        gap: 8px !important;
        border: 1px solid #dbd2c7 !important;
        border-radius: 9px !important;
        background: #fff !important;
        padding: 4px 8px !important;
        color: #776d62 !important;
        font-size: 8px !important;
        font-weight: 600 !important;
    }

    #sellerAddProductModal .seller-variant-draft-preview {
        display: grid !important;
        width: 34px !important;
        height: 34px !important;
        flex: 0 0 34px !important;
        place-items: center !important;
        overflow: hidden !important;
        border-radius: 7px !important;
        background: #f4f0eb !important;
    }

    #sellerAddProductModal .seller-variant-draft-input {
        width: 100% !important;
        height: 44px !important;
        border: 1px solid #dbd2c7 !important;
        border-radius: 9px !important;
        background: #fff !important;
        padding: 0 11px !important;
        color: #302a24 !important;
        font-size: 10px !important;
        outline: none !important;
    }

    #sellerAddProductModal .seller-variant-draft-price {
        position: relative !important;
    }

    #sellerAddProductModal .seller-variant-draft-price > span {
        position: absolute !important;
        z-index: 1 !important;
        left: 10px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        color: #9c6b15 !important;
        font-size: 10px !important;
        font-weight: 700 !important;
    }

    #sellerAddProductModal .seller-variant-draft-price input {
        padding-left: 26px !important;
    }

    #sellerAddProductModal .seller-variant-editor-footer {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 14px !important;
        margin-top: 14px !important;
        padding-top: 12px !important;
        border-top: 1px solid #eee5d9 !important;
    }

    #sellerAddProductModal .seller-variant-editor-footer > span {
        color: #91867b !important;
        font-size: 8.5px !important;
    }

    #sellerAddProductModal .seller-variant-commit-btn {
        display: inline-flex !important;
        height: 36px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 7px !important;
        border: 0 !important;
        border-radius: 9px !important;
        background: #d99108 !important;
        padding: 0 14px !important;
        color: #fff !important;
        font-size: 9px !important;
        font-weight: 700 !important;
    }

    #sellerAddProductModal .seller-variant-validation-error .seller-variant-editor {
        border-color: #d98b8b !important;
        box-shadow: 0 0 0 3px rgba(190,80,80,.07) !important;
    }

    /* Because Tailwind's hidden can be overridden by display rules above. */
    #sellerAddProductModal .seller-variant-saved-row.hidden,
    #sellerAddProductModal .seller-variant-editor.hidden {
        display: none !important;
    }

    @media (max-width: 1024px) {
        #sellerAddProductModal .seller-variant-editor-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        #sellerAddProductModal .seller-variant-editor-image {
            grid-column: 1 / -1 !important;
        }

        #sellerAddProductModal .seller-variant-saved-row {
            grid-template-columns: 54px minmax(140px, 1fr) minmax(100px, .8fr) minmax(100px, .8fr) 78px 70px !important;
            gap: 8px !important;
        }
    }

    @media (max-width: 720px) {
        #sellerAddProductModal #sellerAddMedia > .mt-4.grid,
        #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid,
        #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-3.grid,
        #sellerAddProductModal .seller-variant-editor-grid {
            grid-template-columns: 1fr !important;
        }

        #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid > div:nth-child(5),
        #sellerAddProductModal .seller-variant-editor-image {
            grid-column: auto !important;
        }

        #sellerAddProductModal .seller-variant-saved-row {
            grid-template-columns: 54px minmax(0, 1fr) auto !important;
        }

        #sellerAddProductModal .seller-variant-saved-sku,
        #sellerAddProductModal .seller-variant-saved-price,
        #sellerAddProductModal .seller-variant-saved-stock {
            grid-column: 2 !important;
        }

        #sellerAddProductModal .seller-variant-saved-actions {
            grid-column: 3 !important;
            grid-row: 1 / span 3 !important;
        }
    }



    /* ======================================================================
       FINAL V4 — A4 COMPACT / ICON + DIVIDER FORM RHYTHM
       ====================================================================== */

    #sellerAddProductModal {
        padding: 10px !important;
        background: rgba(31, 27, 22, .42) !important;
    }

    #sellerAddProductModal .seller-add-modal-shell {
        width: min(94vw, 880px) !important;
        max-width: 880px !important;
        max-height: 93vh !important;
        border: 1px solid #e5ddd2 !important;
        border-radius: 18px !important;
        background: #fff !important;
        box-shadow: 0 28px 80px rgba(30, 24, 17, .18) !important;
    }

    /* Compact header */
    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 {
        min-height: 0 !important;
        padding: 12px 18px !important;
        border-bottom: 1px solid #eee6db !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 .h-12.w-12 {
        width: 38px !important;
        height: 38px !important;
        border-radius: 11px !important;
        border-color: #ead5aa !important;
        background: #fff9ed !important;
        color: #bd7a08 !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 h3 {
        margin-top: 1px !important;
        font-size: 20px !important;
        line-height: 1.08 !important;
        letter-spacing: -.035em !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 h3 + p {
        margin-top: 3px !important;
        font-size: 8px !important;
        line-height: 1.4 !important;
    }

    #sellerAddProductModal #closeAddProductModal {
        width: 34px !important;
        height: 34px !important;
        border-radius: 10px !important;
    }

    #sellerAddProductModal .seller-add-form-body {
        width: 100% !important;
        padding: 0 20px 72px !important;
        background: #fff !important;
    }

    #sellerAddProductModal .seller-add-exact-layout {
        width: 100% !important;
        max-width: 820px !important;
        margin: 0 auto !important;
    }

    /* Compact progress — stays informative but no longer dominates. */
    #sellerAddProductModal .seller-add-progress {
        width: 100% !important;
        max-width: 820px !important;
        margin: 0 auto !important;
        padding: 9px 8px 10px !important;
        border: 0 !important;
        border-bottom: 1px solid #eee7dd !important;
        background: #fff !important;
    }

    #sellerAddProductModal .seller-add-progress-head {
        display: none !important;
    }

    #sellerAddProductModal .seller-add-progress-track-wrap {
        margin-top: 0 !important;
    }

    #sellerAddProductModal .seller-add-progress-track {
        top: 10px !important;
        height: 1px !important;
    }

    #sellerAddProductModal .seller-add-progress-circle {
        width: 21px !important;
        height: 21px !important;
        font-size: 6.7px !important;
    }

    #sellerAddProductModal .seller-add-progress-step {
        gap: 4px !important;
        padding-inline: 2px !important;
    }

    #sellerAddProductModal .seller-add-progress-copy strong {
        font-size: 6.8px !important;
        line-height: 1.1 !important;
    }

    #sellerAddProductModal .seller-add-progress-copy small {
        display: none !important;
    }

    /* One continuous sheet: only dividers separate major sections. */
    #sellerAddProductModal .seller-add-flow-section,
    #sellerAddProductModal .seller-add-subsection,
    #sellerAddProductModal #sellerVariantSection,
    #sellerAddProductModal .seller-add-optional-card,
    #sellerAddProductModal #sellerAddDelivery,
    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section {
        width: 100% !important;
        margin: 0 !important;
        padding: 15px 0 !important;
        border: 0 !important;
        border-bottom: 1px solid #ece5db !important;
        border-radius: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-add-flow-section:first-of-type {
        padding-top: 13px !important;
    }

    #sellerAddProductModal .seller-add-optional-body,
    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body {
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
    }

    /* Icon → title → line rhythm requested by the user. */
    #sellerAddProductModal .seller-a4-section-title-row {
        display: flex !important;
        min-width: 0 !important;
        align-items: center !important;
        gap: 8px !important;
    }

    #sellerAddProductModal .seller-a4-section-icon {
        display: grid !important;
        width: 28px !important;
        height: 28px !important;
        min-width: 28px !important;
        flex: 0 0 28px !important;
        place-items: center !important;
        border: 1px solid #ead7b2 !important;
        border-radius: 8px !important;
        background: #fff9ee !important;
        color: #b87608 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-a4-section-icon svg {
        width: 14px !important;
        height: 14px !important;
    }

    #sellerAddProductModal .seller-a4-section-rule {
        display: block !important;
        min-width: 20px !important;
        height: 1px !important;
        flex: 1 1 auto !important;
        background: #eee5d9 !important;
    }

    #sellerAddProductModal .seller-a4-section-title-row p,
    #sellerAddProductModal .seller-a4-price-head p.text-\[11px\] {
        flex: 0 0 auto !important;
        font-size: 12.5px !important;
        line-height: 1.2 !important;
        font-weight: 700 !important;
        color: #221e19 !important;
    }

    #sellerAddProductModal .seller-a4-price-head {
        align-items: flex-start !important;
    }

    #sellerAddProductModal .seller-a4-price-head > .min-w-0.flex-1 > div:first-child {
        position: relative !important;
        padding-right: 6px !important;
    }

    #sellerAddProductModal .seller-a4-summary-title {
        width: 100% !important;
    }

    #sellerAddProductModal .seller-a4-summary-title::after {
        content: "" !important;
        display: block !important;
        min-width: 24px !important;
        height: 1px !important;
        flex: 1 1 auto !important;
        background: #eee5d9 !important;
    }

    #sellerAddProductModal .seller-add-flow-section p.text-\[8px\],
    #sellerAddProductModal .seller-add-subsection p.text-\[7\.5px\],
    #sellerAddProductModal #sellerVariantSection .mt-1.text-\[8px\],
    #sellerAddProductModal .seller-add-optional-summary .text-\[7\.5px\],
    #sellerAddProductModal #sellerAddDelivery p.text-\[8px\] {
        margin-top: 3px !important;
        font-size: 8px !important;
        line-height: 1.4 !important;
        color: #867c71 !important;
    }

    /* Labels and fields: compact, readable, consistent. */
    #sellerAddProductModal label {
        margin-bottom: 5px !important;
        font-size: 8.5px !important;
        line-height: 1.25 !important;
        font-weight: 600 !important;
        color: #4f473f !important;
    }

    #sellerAddProductModal input[type="text"],
    #sellerAddProductModal input[type="number"],
    #sellerAddProductModal input[type="email"],
    #sellerAddProductModal input[type="date"],
    #sellerAddProductModal input[type="datetime-local"],
    #sellerAddProductModal select,
    #sellerAddProductModal #sellerCategoryDropdownButton {
        height: 38px !important;
        border-radius: 9px !important;
        font-size: 9px !important;
    }

    #sellerAddProductModal textarea {
        border-radius: 9px !important;
        font-size: 9px !important;
        line-height: 1.55 !important;
    }

    #sellerAddProductModal #sellerAddBasics .mt-4.grid,
    #sellerAddProductModal #sellerSimpleInventorySection .mt-4.grid {
        margin-top: 11px !important;
        gap: 10px !important;
    }

    #sellerAddProductModal #sellerCategoryHint {
        min-height: 0 !important;
        margin-top: 4px !important;
        font-size: 6.7px !important;
        line-height: 1.35 !important;
    }

    #sellerAddProductModal .seller-add-inline-optional {
        border: 0 !important;
        border-top: 1px dashed #eee6dc !important;
        border-radius: 0 !important;
        background: transparent !important;
    }

    #sellerAddProductModal .seller-add-inline-summary {
        min-height: 36px !important;
        padding: 8px 0 !important;
    }

    /* Photos: close together, no giant empty zones. */
    #sellerAddProductModal #sellerAddMedia > .mt-4.grid {
        margin-top: 11px !important;
        grid-template-columns: 180px minmax(0, 1fr) !important;
        gap: 10px !important;
        align-items: start !important;
    }

    #sellerAddProductModal #sellerAddMedia label.group,
    #sellerAddProductModal #sellerAddMedia textarea {
        min-height: 118px !important;
        height: 118px !important;
    }

    #sellerAddProductModal #sellerAddMedia label.group {
        padding: 9px !important;
        border-radius: 10px !important;
    }

    #sellerAddProductModal #sellerImagePreviewBox {
        width: 42px !important;
        height: 42px !important;
        border-radius: 9px !important;
    }

    #sellerAddProductModal #sellerAddMedia label.group p.mt-3 {
        margin-top: 7px !important;
        font-size: 8.5px !important;
    }

    #sellerAddProductModal #sellerAddMedia label.group p.mt-1,
    #sellerAddProductModal #sellerAddMedia label.group p.mt-1\.5 {
        margin-top: 2px !important;
        font-size: 6.4px !important;
        line-height: 1.3 !important;
    }

    #sellerAddProductModal #sellerAddMedia > .mt-4.border-t {
        margin-top: 10px !important;
        padding-top: 9px !important;
    }

    #sellerAddProductModal #sellerGalleryPreview {
        margin-top: 8px !important;
        max-height: 92px !important;
        gap: 6px !important;
    }

    #sellerAddProductModal #sellerGalleryEmpty {
        padding: 9px !important;
        font-size: 7px !important;
    }

    /* Variants: flat staged workflow, no card-inside-card. */
    #sellerAddProductModal #sellerVariantSection {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    #sellerAddProductModal #sellerVariantSection > div:first-child {
        align-items: center !important;
        gap: 10px !important;
    }

    #sellerAddProductModal #sellerAddManualVariant,
    #sellerAddProductModal #sellerUseSinglePrice,
    #sellerAddProductModal #sellerApplyVariantDefaults {
        height: 32px !important;
        border-radius: 8px !important;
        padding-inline: 11px !important;
        font-size: 7.5px !important;
    }

    #sellerAddProductModal #sellerManualVariantEmpty {
        margin-top: 10px !important;
        padding: 11px 0 !important;
    }

    #sellerAddProductModal #sellerVariantTableWrap {
        margin-top: 9px !important;
        padding-top: 9px !important;
    }

    #sellerAddProductModal .seller-variant-editor {
        margin: 5px 0 8px !important;
        padding: 10px 0 !important;
        border: 0 !important;
        border-top: 1px solid #eee6dc !important;
        border-bottom: 1px solid #eee6dc !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-variant-editor-head {
        margin-bottom: 9px !important;
    }

    #sellerAddProductModal .seller-variant-editor-head strong {
        font-size: 10px !important;
    }

    #sellerAddProductModal .seller-variant-editor-head span {
        margin-top: 2px !important;
        font-size: 7px !important;
    }

    #sellerAddProductModal .seller-variant-editor-grid {
        grid-template-columns: 112px minmax(120px, 1.1fr) minmax(120px, 1fr) minmax(110px, .9fr) minmax(104px, .8fr) 72px !important;
        gap: 7px !important;
    }

    #sellerAddProductModal .seller-variant-editor-grid label {
        margin-bottom: 4px !important;
        font-size: 7.4px !important;
    }

    #sellerAddProductModal .seller-variant-image-picker--draft,
    #sellerAddProductModal .seller-variant-draft-input {
        height: 36px !important;
        border-radius: 8px !important;
        font-size: 8px !important;
    }

    #sellerAddProductModal .seller-variant-draft-preview {
        width: 28px !important;
        height: 28px !important;
        flex-basis: 28px !important;
    }

    #sellerAddProductModal .seller-variant-editor-footer {
        margin-top: 9px !important;
        padding-top: 8px !important;
    }

    #sellerAddProductModal .seller-variant-editor-footer > span {
        font-size: 7px !important;
    }

    #sellerAddProductModal .seller-variant-commit-btn {
        height: 31px !important;
        border-radius: 8px !important;
        padding-inline: 11px !important;
        font-size: 7.5px !important;
    }

    #sellerAddProductModal .seller-variant-saved-row {
        grid-template-columns: 46px minmax(125px, 1.35fr) minmax(96px, .8fr) minmax(92px, .75fr) 68px 62px !important;
        min-height: 62px !important;
        gap: 8px !important;
        padding: 7px 0 !important;
    }

    #sellerAddProductModal .seller-variant-saved-image {
        width: 42px !important;
        height: 42px !important;
        border-radius: 8px !important;
    }

    #sellerAddProductModal .seller-variant-saved-main strong,
    #sellerAddProductModal .seller-variant-saved-price {
        font-size: 9px !important;
    }

    #sellerAddProductModal .seller-variant-saved-main span,
    #sellerAddProductModal .seller-variant-saved-sku,
    #sellerAddProductModal .seller-variant-saved-stock {
        margin-top: 2px !important;
        font-size: 7.4px !important;
    }

    #sellerAddProductModal .seller-variant-row-action {
        width: 28px !important;
        height: 28px !important;
    }

    /* Promotion: flatten nested boxes into rows separated by subtle rules. */
    #sellerAddProductModal .seller-add-optional-card > .seller-add-optional-summary {
        padding: 0 0 9px !important;
    }

    #sellerAddProductModal .seller-add-optional-card > .seller-add-optional-body section > .mt-4.grid {
        margin-top: 9px !important;
        grid-template-columns: 160px minmax(0, 1fr) !important;
        gap: 0 12px !important;
    }

    #sellerAddProductModal .seller-add-optional-body section > .mt-4.grid > div,
    #sellerAddProductModal .seller-add-optional-body section > .mt-4.grid > label {
        padding: 9px 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-add-optional-body section > .mt-4.grid > div:nth-child(2) {
        padding-left: 11px !important;
        border-left: 1px solid #eee6dc !important;
    }

    #sellerAddProductModal .seller-add-optional-body section > .mt-4.grid > label {
        grid-column: 1 / -1 !important;
        border-top: 1px solid #eee6dc !important;
    }

    #sellerAddProductModal #sellerPromotionPreview {
        margin-top: 7px !important;
        padding: 7px 0 !important;
        border: 0 !important;
        border-top: 1px solid #eee6dc !important;
        border-radius: 0 !important;
        background: transparent !important;
    }

    /* Shipping / fulfillment: close the fields up. */
    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-summary {
        padding: 0 0 8px !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > .border-t {
        margin: 11px 0 !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section {
        padding: 0 0 12px !important;
        border-bottom: 0 !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid {
        margin-top: 9px !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid > div:nth-child(5) {
        grid-column: 1 / -1 !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-3.grid {
        margin-top: 7px !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }

    #sellerAddProductModal #sellerAddDelivery .h-9.w-9 {
        width: 28px !important;
        height: 28px !important;
        border-radius: 8px !important;
    }

    #sellerAddProductModal #sellerAddDelivery .rounded-full {
        display: none !important;
    }

    #sellerAddProductModal #sellerSpecificationEmpty {
        margin-top: 8px !important;
        padding: 10px !important;
    }

    #sellerAddProductModal #sellerAddSpecification {
        height: 31px !important;
        border-radius: 8px !important;
        font-size: 7.5px !important;
    }

    /* Footer */
    #sellerAddProductModal .sticky.bottom-0 {
        padding: 8px 18px !important;
        border-top: 1px solid #e9e1d6 !important;
        background: rgba(255,255,255,.985) !important;
        box-shadow: 0 -4px 14px rgba(25,20,15,.035) !important;
    }

    #sellerAddProductModal .sticky.bottom-0 > p {
        font-size: 6.8px !important;
    }

    #sellerAddProductModal #cancelAddProductModal,
    #sellerAddProductModal #sellerPreviewProduct,
    #sellerAddProductModal #sellerSubmitProduct {
        height: 34px !important;
        border-radius: 8px !important;
        font-size: 7.5px !important;
    }

    #sellerAddProductModal #sellerSubmitProduct {
        min-width: 145px !important;
        background: #d99108 !important;
        box-shadow: none !important;
    }

    @media (max-width: 900px) {
        #sellerAddProductModal .seller-add-modal-shell {
            width: min(96vw, 820px) !important;
        }

        #sellerAddProductModal .seller-add-form-body {
            padding-inline: 14px !important;
        }

        #sellerAddProductModal .seller-variant-editor-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }

        #sellerAddProductModal .seller-variant-editor-image {
            grid-column: auto !important;
        }
    }

    @media (max-width: 640px) {
        #sellerAddProductModal {
            padding: 4px !important;
        }

        #sellerAddProductModal .seller-add-modal-shell {
            width: 100% !important;
            max-height: 97vh !important;
            border-radius: 13px !important;
        }

        #sellerAddProductModal #sellerAddMedia > .mt-4.grid,
        #sellerAddProductModal .seller-add-optional-card > .seller-add-optional-body section > .mt-4.grid,
        #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid,
        #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-3.grid,
        #sellerAddProductModal .seller-variant-editor-grid {
            grid-template-columns: 1fr !important;
        }

        #sellerAddProductModal .seller-add-optional-body section > .mt-4.grid > div:nth-child(2) {
            padding-left: 0 !important;
            border-left: 0 !important;
            border-top: 1px solid #eee6dc !important;
        }

        #sellerAddProductModal .seller-variant-saved-row {
            grid-template-columns: 42px minmax(0, 1fr) auto !important;
        }
    }



    /* ======================================================================
       SARI SELLER CENTER — FINAL ADD PRODUCT FORM
       Clean enterprise form, A4-inspired width, compact vertical workflow.
       ====================================================================== */

    #sellerAddProductModal,
    #sellerAddProductModal * {
        font-family: "Poppins", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
        box-sizing: border-box !important;
    }

    #sellerAddProductModal {
        padding: 16px !important;
        background: rgba(29, 26, 22, .42) !important;
    }

    #sellerAddProductModal .seller-add-modal-shell {
        width: min(95vw, 920px) !important;
        max-width: 920px !important;
        max-height: 94vh !important;
        overflow: hidden !important;
        border: 1px solid #e7e0d7 !important;
        border-radius: 20px !important;
        background: #fff !important;
        box-shadow: 0 30px 80px rgba(28, 23, 18, .18), 0 8px 22px rgba(28, 23, 18, .055) !important;
    }

    /* Header */
    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 {
        min-height: 78px !important;
        padding: 14px 20px !important;
        border-bottom: 1px solid #eee8e0 !important;
        background: #fff !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 > .relative {
        align-items: center !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 .h-12.w-12 {
        width: 42px !important;
        height: 42px !important;
        border: 1px solid #ead7ae !important;
        border-radius: 13px !important;
        background: #fff9ee !important;
        color: #b8790c !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 h3 {
        margin-top: 1px !important;
        color: #191714 !important;
        font-size: 22px !important;
        line-height: 1.08 !important;
        font-weight: 700 !important;
        letter-spacing: -.04em !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 h3 + p {
        margin-top: 4px !important;
        max-width: 680px !important;
        color: #776f66 !important;
        font-size: 10.5px !important;
        line-height: 1.45 !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 p.text-\[8px\] {
        color: #a56d0e !important;
        font-size: 8px !important;
        letter-spacing: .14em !important;
    }

    #sellerAddProductModal #closeAddProductModal {
        width: 38px !important;
        height: 38px !important;
        border: 1px solid #e4ddd4 !important;
        border-radius: 11px !important;
        background: #fff !important;
        color: #70675f !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal #closeAddProductModal:hover {
        border-color: #d8c6a5 !important;
        background: #fffaf1 !important;
        color: #986514 !important;
    }

    #sellerAddProductModal #sellerAddProductForm {
        background: #fff !important;
        scrollbar-width: thin !important;
        scrollbar-color: #cfc6bc transparent !important;
    }

    #sellerAddProductModal .seller-add-form-body {
        width: 100% !important;
        padding: 0 24px 82px !important;
        background: #fff !important;
    }

    #sellerAddProductModal .seller-add-exact-layout,
    #sellerAddProductModal .seller-add-progress {
        width: 100% !important;
        max-width: 850px !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    /* Compact seller-center progress */
    #sellerAddProductModal .seller-add-progress {
        padding: 10px 14px 12px !important;
        border: 0 !important;
        border-bottom: 1px solid #eee8df !important;
        background: #fff !important;
    }

    #sellerAddProductModal .seller-add-progress-head {
        display: none !important;
    }

    #sellerAddProductModal .seller-add-progress-track-wrap {
        margin-top: 0 !important;
    }

    #sellerAddProductModal .seller-add-progress-track {
        top: 12px !important;
        left: 12.5% !important;
        right: 12.5% !important;
        height: 1.5px !important;
        background: #e7e1da !important;
    }

    #sellerAddProductModal .seller-add-progress-fill {
        background: #cb8a10 !important;
    }

    #sellerAddProductModal .seller-add-progress-circle {
        width: 25px !important;
        height: 25px !important;
        border: 1px solid #ded6cc !important;
        background: #fff !important;
        color: #8e857c !important;
        font-size: 8px !important;
        font-weight: 700 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-add-progress-step.is-active .seller-add-progress-circle,
    #sellerAddProductModal .seller-add-progress-step.is-complete .seller-add-progress-circle {
        border-color: #cb8a10 !important;
        background: #cb8a10 !important;
        color: #fff !important;
    }

    #sellerAddProductModal .seller-add-progress-copy strong {
        color: #39332d !important;
        font-size: 8.5px !important;
        font-weight: 650 !important;
    }

    #sellerAddProductModal .seller-add-progress-copy small {
        display: none !important;
    }

    /* One continuous form sheet — no card-per-section look. */
    #sellerAddProductModal .seller-add-exact-layout,
    #sellerAddProductModal .seller-add-exact-column,
    #sellerAddProductModal .seller-add-exact-bottom {
        display: block !important;
    }

    #sellerAddProductModal .seller-add-flow-section,
    #sellerAddProductModal .seller-add-subsection,
    #sellerAddProductModal #sellerVariantSection,
    #sellerAddProductModal .seller-add-optional-card,
    #sellerAddProductModal #sellerAddDelivery,
    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section {
        width: 100% !important;
        margin: 0 !important;
        padding: 20px 0 !important;
        border: 0 !important;
        border-bottom: 1px solid #eee7de !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        overflow: visible !important;
    }

    #sellerAddProductModal .seller-add-flow-section:first-of-type {
        padding-top: 18px !important;
    }

    #sellerAddProductModal .seller-add-flow-section:last-child,
    #sellerAddProductModal #sellerAddDelivery:last-child {
        border-bottom: 0 !important;
    }

    /* Icon + heading + thin line */
    #sellerAddProductModal .seller-a4-section-title-row {
        display: flex !important;
        min-width: 0 !important;
        align-items: center !important;
        gap: 10px !important;
    }

    #sellerAddProductModal .seller-a4-section-icon,
    #sellerAddProductModal #sellerSimpleInventorySection > div:first-child > span,
    #sellerAddProductModal .seller-add-optional-summary .seller-a4-section-icon {
        display: grid !important;
        width: 31px !important;
        height: 31px !important;
        min-width: 31px !important;
        flex: 0 0 31px !important;
        place-items: center !important;
        border: 1px solid #ead6aa !important;
        border-radius: 9px !important;
        background: #fff9ed !important;
        color: #b6770b !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-a4-section-icon svg,
    #sellerAddProductModal #sellerSimpleInventorySection > div:first-child > span svg {
        width: 15px !important;
        height: 15px !important;
    }

    #sellerAddProductModal .seller-a4-section-title-row p,
    #sellerAddProductModal #sellerSimpleInventorySection p.text-\[11px\],
    #sellerAddProductModal #sellerVariantSection > div:first-child p:first-child,
    #sellerAddProductModal .seller-add-optional-summary .block.text-\[10px\] {
        color: #211e1a !important;
        font-size: 15px !important;
        line-height: 1.15 !important;
        font-weight: 700 !important;
        letter-spacing: -.025em !important;
    }

    #sellerAddProductModal .seller-a4-section-rule {
        height: 1px !important;
        min-width: 20px !important;
        flex: 1 1 auto !important;
        background: #e8dfd4 !important;
    }

    #sellerAddProductModal .seller-add-flow-section > div:first-child > div > p.mt-1,
    #sellerAddProductModal #sellerSimpleInventorySection .mt-1.text-\[7\.5px\],
    #sellerAddProductModal #sellerVariantSection .mt-1.text-\[8px\],
    #sellerAddProductModal #sellerAddDelivery p.text-\[8px\] {
        margin-top: 6px !important;
        color: #7f776e !important;
        font-size: 10.5px !important;
        line-height: 1.5 !important;
    }

    /* Form controls */
    #sellerAddProductModal label {
        color: #49423b !important;
        font-size: 10.5px !important;
        line-height: 1.2 !important;
        font-weight: 600 !important;
    }

    #sellerAddProductModal input[type="text"],
    #sellerAddProductModal input[type="number"],
    #sellerAddProductModal input[type="email"],
    #sellerAddProductModal input[type="date"],
    #sellerAddProductModal input[type="datetime-local"],
    #sellerAddProductModal select,
    #sellerAddProductModal #sellerCategoryDropdownButton {
        height: 42px !important;
        min-width: 0 !important;
        border: 1px solid #dcd5cc !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: #302b26 !important;
        font-size: 11.5px !important;
        font-weight: 450 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal input::placeholder,
    #sellerAddProductModal textarea::placeholder {
        color: #a29a91 !important;
        opacity: 1 !important;
    }

    #sellerAddProductModal input:focus,
    #sellerAddProductModal textarea:focus,
    #sellerAddProductModal select:focus,
    #sellerAddProductModal #sellerCategoryDropdownButton:focus {
        outline: 0 !important;
        border-color: #ca931f !important;
        box-shadow: 0 0 0 3px rgba(202,147,31,.08) !important;
    }

    /* Product information spacing */
    #sellerAddProductModal #sellerAddBasics .mt-4.grid {
        margin-top: 16px !important;
        gap: 13px 14px !important;
    }

    #sellerAddProductModal #sellerAddBasics .seller-add-inline-optional {
        margin-top: 2px !important;
        border: 0 !important;
        border-top: 1px solid #f0ebe5 !important;
        border-radius: 0 !important;
        background: transparent !important;
    }

    #sellerAddProductModal #sellerAddBasics .seller-add-inline-summary {
        padding: 10px 0 !important;
        background: transparent !important;
    }

    /* Photos & description */
    #sellerAddProductModal #sellerAddMedia > .mt-4.grid {
        display: grid !important;
        grid-template-columns: 190px minmax(0, 1fr) !important;
        gap: 14px !important;
        margin-top: 16px !important;
        align-items: stretch !important;
    }

    #sellerAddProductModal #sellerAddMedia label.group {
        height: 154px !important;
        min-height: 154px !important;
        padding: 12px !important;
        border: 1px dashed #d7d0c7 !important;
        border-radius: 12px !important;
        background: #fff !important;
    }

    #sellerAddProductModal #sellerAddMedia label.group:hover {
        border-color: #cda455 !important;
        background: #fffdf8 !important;
    }

    #sellerAddProductModal #sellerImagePreviewBox {
        width: 52px !important;
        height: 52px !important;
        border-radius: 12px !important;
        background: #fffaf1 !important;
        color: #6f7480 !important;
    }

    #sellerAddProductModal #sellerAddMedia textarea {
        height: 154px !important;
        min-height: 154px !important;
        padding: 12px 14px !important;
        border: 1px solid #dcd5cc !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: #302b26 !important;
        font-size: 11.5px !important;
        line-height: 1.65 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal #sellerAddMedia > .mt-4.border-t {
        margin-top: 14px !important;
        padding-top: 14px !important;
        border-color: #eee8e0 !important;
    }

    #sellerAddProductModal #sellerGalleryCount {
        font-size: 9px !important;
    }

    #sellerAddProductModal #sellerGalleryPreview {
        max-height: 130px !important;
        margin-top: 10px !important;
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }

    #sellerAddProductModal #sellerGalleryEmpty {
        padding: 11px !important;
        border-radius: 10px !important;
        background: #fcfbf9 !important;
        color: #948b82 !important;
        font-size: 9.5px !important;
    }

    /* Price & stock */
    #sellerAddProductModal #sellerSimpleInventorySection > div:first-child {
        align-items: center !important;
    }

    #sellerAddProductModal #sellerSimpleInventorySection > .mt-4.grid {
        margin-top: 15px !important;
        gap: 14px !important;
    }

    #sellerAddProductModal .seller-variant-callout {
        display: grid !important;
        grid-template-columns: 34px minmax(0, 1fr) auto !important;
        align-items: center !important;
        gap: 11px !important;
        margin-top: 14px !important;
        padding: 11px 12px !important;
        border: 1px solid #eee2c7 !important;
        border-radius: 11px !important;
        background: #fffaf0 !important;
    }

    #sellerAddProductModal .seller-variant-callout-icon {
        display: grid !important;
        width: 32px !important;
        height: 32px !important;
        place-items: center !important;
        border-radius: 9px !important;
        background: #fff !important;
        color: #b97b0c !important;
        box-shadow: inset 0 0 0 1px #efe1c3 !important;
    }

    #sellerAddProductModal .seller-variant-callout-icon svg {
        width: 16px !important;
        height: 16px !important;
    }

    #sellerAddProductModal .seller-variant-callout-copy {
        min-width: 0 !important;
    }

    #sellerAddProductModal .seller-variant-callout-copy strong {
        display: block !important;
        color: #302a24 !important;
        font-size: 10.5px !important;
        font-weight: 650 !important;
    }

    #sellerAddProductModal .seller-variant-callout-copy small {
        display: block !important;
        margin-top: 2px !important;
        color: #81776d !important;
        font-size: 9px !important;
        line-height: 1.4 !important;
    }

    #sellerAddProductModal .seller-variant-callout-button {
        display: inline-flex !important;
        height: 36px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 7px !important;
        border: 0 !important;
        border-radius: 9px !important;
        background: #cb8a10 !important;
        padding: 0 14px !important;
        color: #fff !important;
        font-size: 9.5px !important;
        font-weight: 650 !important;
        white-space: nowrap !important;
        box-shadow: 0 6px 14px rgba(203,138,16,.14) !important;
    }

    #sellerAddProductModal .seller-variant-callout-button:hover {
        background: #b97b0d !important;
    }

    /* Progressive variants: editor only when adding, compact rows after save. */
    #sellerAddProductModal #sellerVariantSection {
        padding-top: 18px !important;
    }

    #sellerAddProductModal #sellerVariantSection > div:first-child {
        align-items: center !important;
    }

    #sellerAddProductModal #sellerAddManualVariant,
    #sellerAddProductModal #sellerUseSinglePrice,
    #sellerAddProductModal #sellerApplyVariantDefaults {
        height: 36px !important;
        border-radius: 9px !important;
        font-size: 9.5px !important;
        font-weight: 650 !important;
    }

    #sellerAddProductModal #sellerAddManualVariant {
        background: #cb8a10 !important;
        box-shadow: 0 6px 14px rgba(203,138,16,.14) !important;
    }

    #sellerAddProductModal #sellerManualVariantEmpty {
        margin-top: 12px !important;
        padding: 13px !important;
        border: 1px dashed #dcd4ca !important;
        border-radius: 10px !important;
        background: #fcfbf9 !important;
    }

    #sellerAddProductModal .seller-variant-editor {
        margin-top: 12px !important;
        padding: 14px 0 0 !important;
        border: 0 !important;
        border-top: 1px solid #eee7df !important;
        border-bottom: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-variant-editor-head strong {
        color: #27221e !important;
        font-size: 11px !important;
    }

    #sellerAddProductModal .seller-variant-editor-head span,
    #sellerAddProductModal .seller-variant-editor-footer > span {
        color: #8b8279 !important;
        font-size: 9px !important;
    }

    #sellerAddProductModal .seller-variant-editor-grid {
        margin-top: 12px !important;
        gap: 10px !important;
    }

    #sellerAddProductModal .seller-variant-editor-grid label {
        font-size: 9.5px !important;
    }

    #sellerAddProductModal .seller-variant-editor input {
        height: 40px !important;
        font-size: 10.5px !important;
    }

    #sellerAddProductModal .seller-variant-editor-footer {
        margin-top: 12px !important;
        padding-top: 11px !important;
        border-top: 1px solid #f0ebe5 !important;
    }

    #sellerAddProductModal .seller-variant-editor-footer button {
        height: 36px !important;
        border-radius: 9px !important;
        font-size: 9.5px !important;
    }

    #sellerAddProductModal .seller-variant-saved-row {
        margin-top: 9px !important;
        min-height: 62px !important;
        border: 0 !important;
        border-bottom: 1px solid #eee8e1 !important;
        border-radius: 0 !important;
        background: #fff !important;
        padding: 9px 0 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-variant-saved-row:last-child {
        border-bottom: 0 !important;
    }

    /* Promotion / shipping optional areas stay flat */
    #sellerAddProductModal .seller-add-optional-card {
        overflow: visible !important;
    }

    #sellerAddProductModal .seller-add-optional-summary {
        min-height: 44px !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
    }

    #sellerAddProductModal .seller-add-optional-body {
        padding: 13px 0 0 !important;
        border: 0 !important;
        background: transparent !important;
    }

    #sellerAddProductModal .seller-add-optional-body section > .mt-4.grid {
        gap: 10px !important;
        margin-top: 13px !important;
    }

    #sellerAddProductModal #sellerPromotionPreview {
        margin-top: 10px !important;
        border: 1px solid #eee1c1 !important;
        border-radius: 10px !important;
        background: #fffaf1 !important;
        padding: 9px 11px !important;
    }

    /* Shipping is dense, readable, and aligned. */
    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section {
        padding: 15px 0 !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid {
        display: grid !important;
        grid-template-columns: 1.15fr 1fr 1fr 1fr !important;
        gap: 10px !important;
        margin-top: 14px !important;
        align-items: end !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid > div:nth-child(5) {
        display: none !important;
    }

    #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-3.grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 10px !important;
        margin-top: 10px !important;
    }

    #sellerAddProductModal #sellerAddDelivery input,
    #sellerAddProductModal #sellerAddDelivery select {
        height: 40px !important;
        font-size: 10.5px !important;
    }

    #sellerAddProductModal #sellerSpecificationEmpty {
        margin-top: 10px !important;
        padding: 12px !important;
        border: 1px dashed #ddd5cb !important;
        border-radius: 10px !important;
        background: #fcfbf9 !important;
    }

    #sellerAddProductModal #sellerAddSpecification {
        height: 34px !important;
        border-radius: 9px !important;
        font-size: 9.5px !important;
    }

    /* Sticky footer */
    #sellerAddProductModal .sticky.bottom-0 {
        min-height: 58px !important;
        padding: 10px 22px !important;
        border-top: 1px solid #ebe4dc !important;
        background: rgba(255,255,255,.98) !important;
        box-shadow: 0 -5px 16px rgba(28,23,17,.035) !important;
        backdrop-filter: blur(8px) !important;
    }

    #sellerAddProductModal .sticky.bottom-0 > p {
        font-size: 8.5px !important;
    }

    #sellerAddProductModal #cancelAddProductModal,
    #sellerAddProductModal #sellerPreviewProduct,
    #sellerAddProductModal #sellerSubmitProduct {
        height: 38px !important;
        border-radius: 9px !important;
        font-size: 9px !important;
        font-weight: 650 !important;
    }

    #sellerAddProductModal #sellerSubmitProduct {
        min-width: 150px !important;
        background: #cb8a10 !important;
        box-shadow: 0 7px 16px rgba(203,138,16,.16) !important;
    }

    #sellerAddProductModal #sellerSubmitProduct:hover {
        background: #b97b0d !important;
    }

    @media (max-width: 760px) {
        #sellerAddProductModal {
            padding: 8px !important;
        }

        #sellerAddProductModal .seller-add-modal-shell {
            width: 100% !important;
            max-height: 96vh !important;
            border-radius: 16px !important;
        }

        #sellerAddProductModal .seller-add-form-body {
            padding-inline: 14px !important;
        }

        #sellerAddProductModal #sellerAddMedia > .mt-4.grid {
            grid-template-columns: 1fr !important;
        }

        #sellerAddProductModal #sellerGalleryPreview {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }

        #sellerAddProductModal .seller-variant-callout {
            grid-template-columns: 32px minmax(0, 1fr) !important;
        }

        #sellerAddProductModal .seller-variant-callout-button {
            grid-column: 1 / -1 !important;
            width: 100% !important;
        }

        #sellerAddProductModal #sellerAddDelivery > .seller-add-optional-body > section:first-of-type > .mt-4.grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }



    /* =====================================================================
       FINAL CLEANUP — INTEGRATED VARIANTS + SHIPPING / SPECS
       ===================================================================== */

    /* Price section: no nested variant card. */
    #sellerAddProductModal #sellerSimpleInventorySection {
        overflow: visible !important;
    }

    #sellerAddProductModal #sellerSimplePriceFields.hidden {
        display: none !important;
    }

    #sellerAddProductModal .seller-variant-callout {
        display: grid !important;
        grid-template-columns: 34px minmax(0, 1fr) 38px !important;
        align-items: center !important;
        gap: 11px !important;
        margin-top: 16px !important;
        padding: 14px 0 0 !important;
        border: 0 !important;
        border-top: 1px solid #ece6de !important;
        border-radius: 0 !important;
        background: transparent !important;
    }

    #sellerAddProductModal .seller-variant-callout-icon {
        width: 32px !important;
        height: 32px !important;
        border: 1px solid #ebd8b3 !important;
        border-radius: 9px !important;
        background: #fffaf1 !important;
        color: #b8790c !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-variant-callout-copy strong {
        color: #25211c !important;
        font-size: 11.5px !important;
        line-height: 1.2 !important;
        font-weight: 650 !important;
    }

    #sellerAddProductModal .seller-variant-callout-copy small {
        margin-top: 3px !important;
        color: #81786e !important;
        font-size: 9.2px !important;
        line-height: 1.45 !important;
    }

    #sellerAddProductModal .seller-variant-callout-button {
        display: grid !important;
        width: 36px !important;
        height: 36px !important;
        min-width: 36px !important;
        place-items: center !important;
        border: 1px solid #d49a2d !important;
        border-radius: 10px !important;
        background: #fff !important;
        padding: 0 !important;
        color: #b77708 !important;
        font-size: 19px !important;
        line-height: 1 !important;
        font-weight: 500 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-variant-callout-button:hover,
    #sellerAddProductModal .seller-variant-callout-button.is-active {
        background: #cf8d10 !important;
        color: #fff !important;
    }

    #sellerAddProductModal .seller-variant-inline-workspace {
        margin: 12px 0 0 45px !important;
        padding: 12px 0 0 !important;
        border-top: 1px solid #f0ebe5 !important;
        background: transparent !important;
    }

    #sellerAddProductModal .seller-variant-inline-workspace.hidden {
        display: none !important;
    }

    #sellerAddProductModal .seller-variant-inline-toolbar {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
    }

    #sellerAddProductModal .seller-variant-inline-summary {
        margin: 0 !important;
        color: #413a33 !important;
        font-size: 9.5px !important;
        line-height: 1.35 !important;
        font-weight: 600 !important;
    }

    #sellerAddProductModal .seller-variant-inline-help {
        margin: 2px 0 0 !important;
        color: #989087 !important;
        font-size: 8px !important;
        line-height: 1.4 !important;
    }

    #sellerAddProductModal .seller-variant-inline-actions {
        display: flex !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        gap: 7px !important;
    }

    #sellerAddProductModal .seller-variant-text-action,
    #sellerAddProductModal .seller-variant-add-action,
    #sellerAddProductModal .seller-variant-copy-action {
        min-height: 32px !important;
        border-radius: 8px !important;
        padding: 0 10px !important;
        font-family: inherit !important;
        font-size: 8.5px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
        white-space: nowrap !important;
    }

    #sellerAddProductModal .seller-variant-text-action,
    #sellerAddProductModal .seller-variant-copy-action {
        border: 1px solid #e2dbd2 !important;
        background: #fff !important;
        color: #71675d !important;
    }

    #sellerAddProductModal .seller-variant-add-action {
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        border: 1px solid #d39a2a !important;
        background: #fffaf1 !important;
        color: #9b6810 !important;
    }

    #sellerAddProductModal .seller-variant-add-action:hover {
        background: #cf8d10 !important;
        color: #fff !important;
    }

    #sellerAddProductModal .seller-variant-empty-note {
        margin-top: 10px !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
        color: #a0978e !important;
        font-size: 8.5px !important;
        text-align: left !important;
    }

    #sellerAddProductModal .seller-variant-utility-row {
        display: flex !important;
        justify-content: flex-end !important;
        margin-bottom: 5px !important;
    }

    #sellerAddProductModal .seller-variant-copy-action {
        min-height: 29px !important;
        padding-inline: 9px !important;
        font-size: 8px !important;
    }

    /* Variant draft stays a flat editor separated by one line only. */
    #sellerAddProductModal .seller-variant-editor {
        margin-top: 9px !important;
        padding: 12px 0 0 !important;
        border: 0 !important;
        border-top: 1px solid #eee8e1 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-variant-editor-grid {
        gap: 9px !important;
    }

    #sellerAddProductModal .seller-variant-saved-row {
        margin-top: 5px !important;
        padding: 9px 0 !important;
        border: 0 !important;
        border-bottom: 1px solid #eee8e1 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    /* Shipping: one title, no duplicate Package & fulfillment header/badge. */
    #sellerAddProductModal .seller-shipping-clean {
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        border-top: 1px solid #e9e2d9 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-shipping-clean-summary {
        min-height: 0 !important;
        padding: 18px 0 12px !important;
        background: transparent !important;
    }

    #sellerAddProductModal .seller-shipping-heading {
        display: flex !important;
        min-width: 0 !important;
        flex: 1 1 auto !important;
        align-items: center !important;
        gap: 10px !important;
    }

    #sellerAddProductModal .seller-shipping-heading-copy,
    #sellerAddProductModal .seller-specifications-heading-copy {
        min-width: max-content !important;
    }

    #sellerAddProductModal .seller-shipping-heading-copy strong,
    #sellerAddProductModal .seller-specifications-heading-copy strong {
        display: block !important;
        color: #24201b !important;
        font-size: 12px !important;
        line-height: 1.2 !important;
        font-weight: 650 !important;
    }

    #sellerAddProductModal .seller-shipping-heading-copy small,
    #sellerAddProductModal .seller-specifications-heading-copy small {
        display: block !important;
        margin-top: 2px !important;
        color: #8a8178 !important;
        font-size: 8.4px !important;
        line-height: 1.4 !important;
        font-weight: 400 !important;
    }

    #sellerAddProductModal .seller-shipping-clean-body {
        display: block !important;
        padding: 0 0 4px !important;
        border: 0 !important;
        background: transparent !important;
    }

    #sellerAddProductModal .seller-shipping-fields {
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
    }

    #sellerAddProductModal .seller-shipping-primary-grid {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 10px !important;
        align-items: end !important;
    }

    #sellerAddProductModal .seller-shipping-primary-grid label,
    #sellerAddProductModal .seller-package-dimensions-grid label {
        display: block !important;
        margin-bottom: 6px !important;
        color: #514a41 !important;
        font-size: 9px !important;
        font-weight: 600 !important;
    }

    #sellerAddProductModal .seller-shipping-primary-grid input,
    #sellerAddProductModal .seller-shipping-primary-grid select,
    #sellerAddProductModal .seller-package-dimensions-grid input {
        width: 100% !important;
        height: 40px !important;
        border: 1px solid #ddd6cd !important;
        border-radius: 9px !important;
        background: #fff !important;
        padding: 0 12px !important;
        color: #3d3731 !important;
        font-size: 10px !important;
        outline: 0 !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-shipping-primary-grid input:focus,
    #sellerAddProductModal .seller-shipping-primary-grid select:focus,
    #sellerAddProductModal .seller-package-dimensions-grid input:focus {
        border-color: #ca931f !important;
        box-shadow: 0 0 0 3px rgba(202,147,31,.07) !important;
    }

    #sellerAddProductModal .seller-field-unit {
        position: absolute !important;
        right: 11px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        color: #9a9188 !important;
        font-size: 8px !important;
        font-weight: 500 !important;
        pointer-events: none !important;
    }

    #sellerAddProductModal .seller-shipping-primary-grid .relative input,
    #sellerAddProductModal .seller-package-dimensions-grid .relative input {
        padding-right: 38px !important;
    }

    #sellerAddProductModal .seller-package-dimensions {
        margin-top: 14px !important;
        padding-top: 12px !important;
        border-top: 1px solid #f0ebe5 !important;
    }

    #sellerAddProductModal .seller-package-dimensions-head {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        margin-bottom: 9px !important;
    }

    #sellerAddProductModal .seller-package-dimensions-head > span:first-child {
        color: #514a41 !important;
        font-size: 9.5px !important;
        font-weight: 650 !important;
        white-space: nowrap !important;
    }

    #sellerAddProductModal .seller-package-dimensions-head small {
        color: #a0978e !important;
        font-size: 8px !important;
        white-space: nowrap !important;
    }

    #sellerAddProductModal .seller-package-dimensions-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 10px !important;
    }

    /* Specifications: next flat subsection, icon → title → line → action. */
    #sellerAddProductModal .seller-specifications-clean {
        margin-top: 17px !important;
        padding-top: 15px !important;
        border-top: 1px solid #e9e2d9 !important;
    }

    #sellerAddProductModal .seller-specifications-heading {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }

    #sellerAddProductModal .seller-specification-add-button {
        display: inline-flex !important;
        min-height: 34px !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 5px !important;
        border: 1px solid #dcd4ca !important;
        border-radius: 9px !important;
        background: #fff !important;
        padding: 0 11px !important;
        color: #454039 !important;
        font-size: 8.5px !important;
        font-weight: 600 !important;
    }

    #sellerAddProductModal .seller-specification-add-button:hover {
        border-color: #d2b16d !important;
        color: #966617 !important;
    }

    #sellerAddProductModal .seller-specification-empty {
        margin-top: 10px !important;
        padding: 9px 0 !important;
        border: 0 !important;
        border-top: 1px dashed #e7e0d7 !important;
        border-radius: 0 !important;
        background: transparent !important;
        text-align: left !important;
    }

    #sellerAddProductModal .seller-specification-empty strong {
        display: block !important;
        color: #776e65 !important;
        font-size: 8.5px !important;
        font-weight: 600 !important;
    }

    #sellerAddProductModal .seller-specification-empty span {
        display: block !important;
        margin-top: 2px !important;
        color: #a0978e !important;
        font-size: 8px !important;
    }

    @media (max-width: 899px) {
        #sellerAddProductModal .seller-variant-inline-workspace {
            margin-left: 0 !important;
        }

        #sellerAddProductModal .seller-variant-inline-toolbar {
            align-items: flex-start !important;
            flex-direction: column !important;
        }

        #sellerAddProductModal .seller-shipping-primary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 560px) {
        #sellerAddProductModal .seller-variant-callout {
            grid-template-columns: 32px minmax(0, 1fr) 36px !important;
        }

        #sellerAddProductModal .seller-shipping-primary-grid,
        #sellerAddProductModal .seller-package-dimensions-grid {
            grid-template-columns: 1fr !important;
        }

        #sellerAddProductModal .seller-specifications-heading {
            align-items: flex-start !important;
            flex-wrap: wrap !important;
        }

        #sellerAddProductModal .seller-specifications-heading .seller-a4-section-rule {
            display: none !important;
        }
    }



    /* ======================================================================
       FINAL POLISH OVERRIDE — HEADER / VARIANTS / TOGGLES / PREVIEW
       ====================================================================== */
    #sellerAddProductModal {
        align-items: flex-start !important;
    }

    #sellerAddProductModal .seller-add-modal-shell {
        max-width: 980px !important;
        border-color: #e5e7eb !important;
        background: #ffffff !important;
    }

    #sellerAddProductModal .seller-add-modal-shell > .relative.shrink-0 {
        position: sticky !important;
        top: 0 !important;
        z-index: 20 !important;
        background: rgba(255,255,255,.97) !important;
        backdrop-filter: blur(12px) !important;
    }

    #sellerAddProductModal #sellerAddProductForm {
        background: #ffffff !important;
    }

    #sellerAddProductModal .seller-add-form-body {
        background: #ffffff !important;
    }

    #sellerAddProductModal .seller-add-progress {
        position: sticky !important;
        top: 0 !important;
        z-index: 15 !important;
        background: rgba(255,255,255,.98) !important;
        border-bottom: 1px solid #e5e7eb !important;
    }

    #sellerAddProductModal .seller-add-flow-section,
    #sellerAddProductModal .seller-add-optional-card,
    #sellerAddProductModal .seller-shipping-clean {
        border-color: #e5e7eb !important;
        background: #ffffff !important;
        box-shadow: none !important;
    }

    #sellerAddProductModal .seller-a4-section-rule {
        background: #e5e7eb !important;
    }

    #sellerAddProductModal .seller-a4-section-icon {
        background: #fffaf0 !important;
        border-color: #efd8a6 !important;
        color: #c27b0e !important;
    }

    /* Variant area */
    #sellerAddProductModal .seller-variant-callout {
        display: grid !important;
        grid-template-columns: 40px minmax(0,1fr) auto !important;
        gap: 12px !important;
        align-items: center !important;
        padding: 14px !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 16px !important;
        background: linear-gradient(180deg, #ffffff 0%, #fcfdff 100%) !important;
    }

    #sellerAddProductModal .seller-variant-callout-copy strong {
        display: block !important;
        font-size: 14px !important;
        line-height: 1.2 !important;
        color: #1f2937 !important;
    }

    #sellerAddProductModal .seller-variant-callout-copy small {
        display: block !important;
        margin-top: 4px !important;
        font-size: 11px !important;
        line-height: 1.55 !important;
        color: #6b7280 !important;
    }

    #sellerAddProductModal .seller-variant-callout-button,
    #sellerAddProductModal .seller-variant-add-action {
        height: 40px !important;
        min-width: 40px !important;
        border-radius: 12px !important;
        background: #d59617 !important;
        color: #fff !important;
        border: 0 !important;
        box-shadow: 0 8px 16px rgba(213,150,23,.18) !important;
    }

    #sellerAddProductModal .seller-variant-inline-workspace {
        margin-top: 14px !important;
        padding-top: 14px !important;
        border-top: 1px solid #e5e7eb !important;
    }

    #sellerAddProductModal .seller-variant-inline-toolbar {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 14px !important;
        margin-bottom: 14px !important;
    }

    #sellerAddProductModal .seller-variant-inline-summary {
        font-size: 12px !important;
        font-weight: 700 !important;
        color: #111827 !important;
    }

    #sellerAddProductModal .seller-variant-inline-help {
        margin-top: 4px !important;
        font-size: 11px !important;
        color: #6b7280 !important;
    }

    #sellerAddProductModal .seller-variant-inline-actions {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        flex-wrap: wrap !important;
    }

    #sellerAddProductModal .seller-variant-text-action,
    #sellerAddProductModal .seller-variant-copy-action {
        height: 40px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 12px !important;
        background: #fff !important;
        color: #374151 !important;
        padding: 0 14px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
    }

    #sellerAddProductModal .seller-variant-empty-note {
        border: 1px dashed #d1d5db !important;
        border-radius: 14px !important;
        background: #f9fafb !important;
        color: #6b7280 !important;
        font-size: 11px !important;
        padding: 12px 14px !important;
    }

    #sellerAddProductModal .seller-variant-utility-row {
        display: flex !important;
        justify-content: flex-end !important;
        margin-bottom: 10px !important;
    }

    #sellerAddProductModal .seller-manual-variant-row {
        border-top: 1px solid #eef2f7 !important;
        padding-top: 12px !important;
    }

    #sellerAddProductModal .seller-variant-saved-row {
        display: grid !important;
        grid-template-columns: 56px minmax(0,1.6fr) minmax(0,.95fr) 110px 82px auto !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 12px 0 !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }

    #sellerAddProductModal .seller-variant-saved-image {
        width: 52px !important;
        height: 52px !important;
        border-radius: 12px !important;
        border: 1px solid #e5e7eb !important;
        background: #f8fafc !important;
        color: #94a3b8 !important;
    }

    #sellerAddProductModal .seller-variant-saved-main strong {
        display: block !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #111827 !important;
    }

    #sellerAddProductModal .seller-variant-saved-main span,
    #sellerAddProductModal .seller-variant-saved-sku,
    #sellerAddProductModal .seller-variant-saved-stock {
        font-size: 11px !important;
        color: #6b7280 !important;
    }

    #sellerAddProductModal .seller-variant-saved-price {
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #111827 !important;
    }

    #sellerAddProductModal .seller-variant-row-action {
        width: 34px !important;
        height: 34px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: #6b7280 !important;
    }

    #sellerAddProductModal .seller-variant-row-action--danger {
        color: #ef4444 !important;
        border-color: #fecaca !important;
    }

    #sellerAddProductModal .seller-variant-editor {
        margin-top: 12px !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 18px !important;
        background: #f8fafc !important;
        padding: 14px !important;
    }

    #sellerAddProductModal .seller-variant-editor-head {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 12px !important;
        margin-bottom: 12px !important;
        padding-bottom: 10px !important;
        border-bottom: 1px solid #e5e7eb !important;
    }

    #sellerAddProductModal .seller-variant-editor-head strong {
        font-size: 14px !important;
        color: #111827 !important;
    }

    #sellerAddProductModal .seller-variant-editor-head span,
    #sellerAddProductModal .seller-variant-editor-footer > span {
        font-size: 11px !important;
        color: #6b7280 !important;
    }

    #sellerAddProductModal .seller-variant-editor-cancel {
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #6b7280 !important;
    }

    #sellerAddProductModal .seller-variant-editor-grid {
        display: grid !important;
        grid-template-columns: 140px repeat(5, minmax(0,1fr)) !important;
        gap: 12px !important;
        align-items: end !important;
    }

    #sellerAddProductModal .seller-variant-editor-grid label {
        display: block !important;
        margin-bottom: 6px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #374151 !important;
    }

    #sellerAddProductModal .seller-variant-draft-input,
    #sellerAddProductModal .seller-variant-editor input[type="number"],
    #sellerAddProductModal .seller-variant-editor input[type="text"] {
        height: 42px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 12px !important;
        background: #fff !important;
        font-size: 12px !important;
        color: #111827 !important;
    }

    #sellerAddProductModal .seller-variant-image-picker--draft {
        display: grid !important;
        place-items: center !important;
        gap: 8px !important;
        min-height: 86px !important;
        border: 1px dashed #d1d5db !important;
        border-radius: 14px !important;
        background: #fff !important;
        padding: 10px !important;
    }

    #sellerAddProductModal .seller-variant-draft-preview {
        width: 42px !important;
        height: 42px !important;
        border-radius: 12px !important;
        border: 1px solid #e5e7eb !important;
        background: #f8fafc !important;
        color: #6b7280 !important;
    }

    #sellerAddProductModal .seller-variant-editor-footer {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        margin-top: 14px !important;
        padding-top: 12px !important;
        border-top: 1px solid #e5e7eb !important;
    }

    #sellerAddProductModal .seller-variant-commit-btn {
        height: 42px !important;
        padding: 0 16px !important;
        border-radius: 12px !important;
        background: #d59617 !important;
        color: #fff !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        box-shadow: 0 8px 18px rgba(213,150,23,.18) !important;
    }

    /* cleaner promo area */
    #sellerAddProductModal .seller-add-optional-card > .seller-add-optional-summary {
        background: #fff !important;
        border-bottom-color: #eef2f7 !important;
    }

    #sellerAddProductModal .seller-add-optional-body {
        background: #fff !important;
    }

    #sellerAddProductModal .seller-setting-switch-stack {
        display: grid !important;
        gap: 12px !important;
    }

    #sellerAddProductModal .seller-setting-switch-card {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 16px !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 16px !important;
        background: #fff !important;
        padding: 14px !important;
        transition: border-color .2s ease, box-shadow .2s ease !important;
    }

    #sellerAddProductModal .seller-setting-switch-card:hover {
        border-color: #d1d5db !important;
        box-shadow: 0 8px 24px rgba(15,23,42,.04) !important;
    }

    #sellerAddProductModal .seller-setting-switch-copy-wrap {
        display: flex !important;
        align-items: flex-start !important;
        gap: 12px !important;
        min-width: 0 !important;
    }

    #sellerAddProductModal .seller-setting-switch-icon {
        display: grid !important;
        place-items: center !important;
        width: 40px !important;
        height: 40px !important;
        border-radius: 12px !important;
        border: 1px solid #d1d5db !important;
        background: #f8fafc !important;
        color: #475569 !important;
        flex-shrink: 0 !important;
    }

    #sellerAddProductModal .seller-setting-switch-icon--green {
        border-color: #b7e4c7 !important;
        background: #f0fdf4 !important;
        color: #15803d !important;
    }

    #sellerAddProductModal .seller-setting-switch-icon--blue {
        border-color: #bfdbfe !important;
        background: #eff6ff !important;
        color: #2563eb !important;
    }

    #sellerAddProductModal .seller-setting-switch-title {
        display: block !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #111827 !important;
    }

    #sellerAddProductModal .seller-setting-switch-text {
        display: block !important;
        margin-top: 4px !important;
        font-size: 11px !important;
        line-height: 1.55 !important;
        color: #6b7280 !important;
    }

    #sellerAddProductModal .seller-modern-toggle {
        position: relative !important;
        display: inline-block !important;
        width: 48px !important;
        height: 28px !important;
        border-radius: 999px !important;
        background: #e5e7eb !important;
        transition: background-color .2s ease !important;
    }

    #sellerAddProductModal .seller-modern-toggle::after {
        content: '' !important;
        position: absolute !important;
        top: 3px !important;
        left: 3px !important;
        width: 22px !important;
        height: 22px !important;
        border-radius: 999px !important;
        background: #fff !important;
        box-shadow: 0 2px 6px rgba(15,23,42,.15) !important;
        transition: transform .2s ease !important;
    }

    #sellerAddProductModal .peer:checked + .seller-modern-toggle {
        background: #22c55e !important;
    }

    #sellerAddProductModal .peer:checked + .seller-modern-toggle::after {
        transform: translateX(20px) !important;
    }

    #sellerAddProductModal .seller-live-preview {
        border: 1px solid #e5e7eb !important;
        border-radius: 16px !important;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
        padding: 14px !important;
    }

    #sellerAddProductModal .seller-live-preview-head {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        justify-content: space-between !important;
        flex-wrap: wrap !important;
        padding-bottom: 12px !important;
        border-bottom: 1px solid #e5e7eb !important;
    }

    #sellerAddProductModal .seller-live-preview-icon {
        display: grid !important;
        place-items: center !important;
        width: 36px !important;
        height: 36px !important;
        border-radius: 12px !important;
        border: 1px solid #fde68a !important;
        background: #fff7db !important;
        color: #b77912 !important;
        flex-shrink: 0 !important;
    }

    #sellerAddProductModal .seller-live-preview-title {
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #111827 !important;
    }

    #sellerAddProductModal .seller-live-preview-subtitle,
    #sellerAddProductModal .seller-live-preview-hint {
        font-size: 11px !important;
        line-height: 1.55 !important;
        color: #6b7280 !important;
    }

    #sellerAddProductModal .seller-live-preview-badge {
        display: inline-flex !important;
        align-items: center !important;
        height: 30px !important;
        border-radius: 999px !important;
        border: 1px solid #fcd34d !important;
        background: #fff8db !important;
        color: #a16207 !important;
        padding: 0 12px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
    }

    #sellerAddProductModal .seller-live-preview-body {
        padding-top: 12px !important;
    }

    #sellerAddProductModal .seller-live-preview-price {
        font-size: 22px !important;
        line-height: 1 !important;
        font-weight: 800 !important;
        color: #111827 !important;
    }

    #sellerAddProductModal .seller-live-preview-old {
        font-size: 12px !important;
        color: #9ca3af !important;
    }

    #sellerAddProductModal .seller-shipping-clean-summary {
        background: #fff !important;
    }

    #sellerAddProductModal .seller-shipping-heading-copy strong {
        font-size: 14px !important;
        color: #111827 !important;
    }

    #sellerAddProductModal .seller-shipping-heading-copy small {
        font-size: 11px !important;
        color: #6b7280 !important;
    }

    @media (max-width: 900px) {
        #sellerAddProductModal .seller-variant-saved-row {
            grid-template-columns: 56px minmax(0,1fr) auto !important;
        }
        #sellerAddProductModal .seller-variant-saved-sku,
        #sellerAddProductModal .seller-variant-saved-price,
        #sellerAddProductModal .seller-variant-saved-stock {
            grid-column: 2 / 3 !important;
        }
        #sellerAddProductModal .seller-variant-saved-actions {
            grid-column: 3 / 4 !important;
            grid-row: 1 / span 2 !important;
            align-self: center !important;
        }
        #sellerAddProductModal .seller-variant-editor-grid {
            grid-template-columns: 1fr 1fr !important;
        }
        #sellerAddProductModal .seller-variant-editor-image {
            grid-column: 1 / -1 !important;
        }
    }

    @media (max-width: 640px) {
        #sellerAddProductModal .seller-variant-callout,
        #sellerAddProductModal .seller-setting-switch-card,
        #sellerAddProductModal .seller-live-preview-head,
        #sellerAddProductModal .seller-variant-inline-toolbar,
        #sellerAddProductModal .seller-variant-editor-footer {
            grid-template-columns: 1fr !important;
            flex-direction: column !important;
            align-items: stretch !important;
        }

        #sellerAddProductModal .seller-variant-editor-grid {
            grid-template-columns: 1fr !important;
        }

        #sellerAddProductModal .seller-variant-saved-row {
            grid-template-columns: 56px 1fr !important;
        }

        #sellerAddProductModal .seller-variant-saved-actions {
            grid-column: 2 / 3 !important;
            justify-self: end !important;
        }
    }



    /* ============================================================
       PRODUCT FILTERS — PREMIUM CUSTOM DROPDOWNS
       Fast 120ms interaction; no blur/backdrop-filter for zero lag.
       ============================================================ */
    .seller-products-workspace > .mt-4.rounded-\[14px\] {
        overflow: visible !important;
        border-color: #e7e2dc !important;
        background: #ffffff !important;
        box-shadow: 0 7px 20px rgba(26, 31, 44, .035) !important;
    }

    .seller-premium-dropdown {
        position: relative;
        min-width: 0;
        isolation: isolate;
    }

    .seller-premium-dropdown.is-open {
        z-index: 180;
    }

    .seller-premium-dropdown-trigger {
        display: flex;
        width: 100%;
        height: 34px;
        min-width: 0;
        align-items: center;
        gap: 8px;
        border: 1px solid #dfe4ea;
        border-radius: 10px;
        background: #ffffff;
        padding: 0 10px;
        color: #344054;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        font-size: 8.5px;
        font-weight: 600;
        line-height: 1;
        text-align: left;
        outline: none;
        box-shadow: 0 1px 2px rgba(16, 24, 40, .02);
        cursor: pointer;
        transition:
            border-color 120ms ease,
            background-color 120ms ease,
            box-shadow 120ms ease,
            color 120ms ease;
        will-change: border-color, box-shadow;
    }

    .seller-premium-dropdown-trigger:hover {
        border-color: #d1d7df;
        background: #fcfcfd;
        color: #1f2937;
    }

    .seller-premium-dropdown-trigger:focus-visible,
    .seller-premium-dropdown.is-open .seller-premium-dropdown-trigger {
        border-color: #d69a20;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(214, 154, 32, .09);
        color: #1f2937;
    }

    .seller-premium-dropdown-icon {
        display: inline-flex;
        width: 17px;
        height: 17px;
        flex: 0 0 17px;
        align-items: center;
        justify-content: center;
        color: #c98708;
    }

    .seller-premium-dropdown-icon svg {
        width: 16px;
        height: 16px;
        stroke-width: 1.9;
    }

    .seller-premium-dropdown-label {
        min-width: 0;
        flex: 1 1 auto;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .seller-premium-dropdown-chevron {
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
        color: #98a2b3;
        transition: transform 120ms ease, color 120ms ease;
        will-change: transform;
    }

    .seller-premium-dropdown.is-open .seller-premium-dropdown-chevron {
        transform: rotate(180deg);
        color: #b7790b;
    }

    .seller-premium-dropdown-menu {
        position: absolute;
        top: calc(100% + 6px);
        right: 0;
        left: 0;
        z-index: 200;
        max-height: 260px;
        overflow-y: auto;
        border: 1px solid #e2e7ed;
        border-radius: 12px;
        background: #ffffff;
        padding: 5px;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-4px) scale(.992);
        transform-origin: top center;
        box-shadow:
            0 16px 34px rgba(15, 23, 42, .10),
            0 3px 8px rgba(15, 23, 42, .035);
        transition:
            opacity 110ms ease,
            transform 110ms ease,
            visibility 0s linear 110ms;
        scrollbar-width: thin;
        scrollbar-color: #d7dde5 transparent;
        will-change: opacity, transform;
    }

    .seller-premium-dropdown.is-open .seller-premium-dropdown-menu {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0) scale(1);
        transition:
            opacity 110ms ease,
            transform 110ms ease,
            visibility 0s linear 0s;
    }

    .seller-premium-dropdown-option {
        display: flex;
        width: 100%;
        min-height: 34px;
        align-items: center;
        gap: 8px;
        border: 0;
        border-radius: 9px;
        background: transparent;
        padding: 7px 9px;
        color: #475467;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        font-size: 8.5px;
        font-weight: 550;
        text-align: left;
        cursor: pointer;
        transition:
            background-color 90ms ease,
            color 90ms ease;
    }

    .seller-premium-dropdown-option:hover,
    .seller-premium-dropdown-option:focus-visible {
        outline: none;
        background: #f8fafc;
        color: #1f2937;
    }

    .seller-premium-dropdown-option.is-selected {
        background: #fff8e8;
        color: #99650b;
        font-weight: 650;
    }

    .seller-premium-dropdown-option:active {
        background: #fff3d7;
    }

    .seller-premium-dropdown-trigger,
    .seller-premium-dropdown-option,
    .seller-premium-clear-filter {
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
    }

    .seller-premium-dropdown-option-dot {
        width: 6px;
        height: 6px;
        flex: 0 0 6px;
        border-radius: 999px;
        background: #cbd5e1;
    }

    .seller-premium-dropdown-option[data-value="approved"] .seller-premium-dropdown-option-dot,
    .seller-premium-dropdown-option[data-value="in-stock"] .seller-premium-dropdown-option-dot {
        background: #2f9562;
    }

    .seller-premium-dropdown-option[data-value="pending"] .seller-premium-dropdown-option-dot {
        background: #4f7fa8;
    }

    .seller-premium-dropdown-option[data-value="low-stock"] .seller-premium-dropdown-option-dot,
    .seller-premium-dropdown-option[data-value="flagged"] .seller-premium-dropdown-option-dot {
        background: #d28c0c;
    }

    .seller-premium-dropdown-option[data-value="out-of-stock"] .seller-premium-dropdown-option-dot,
    .seller-premium-dropdown-option[data-value="rejected"] .seller-premium-dropdown-option-dot,
    .seller-premium-dropdown-option[data-value="removed"] .seller-premium-dropdown-option-dot {
        background: #cc555d;
    }

    .seller-premium-dropdown-option-copy {
        min-width: 0;
        flex: 1 1 auto;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .seller-premium-dropdown-check {
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
        color: #c98708;
        opacity: 0;
        transform: scale(.8);
        transition: opacity 90ms ease, transform 90ms ease;
    }

    .seller-premium-dropdown-option.is-selected .seller-premium-dropdown-check {
        opacity: 1;
        transform: scale(1);
    }

    .seller-premium-clear-filter {
        display: inline-flex;
        height: 34px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 1px solid #dfe4ea;
        border-radius: 10px;
        background: #ffffff;
        padding: 0 11px;
        color: #667085;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        font-size: 8.3px;
        font-weight: 650;
        white-space: nowrap;
        cursor: pointer;
        transition:
            border-color 110ms ease,
            background-color 110ms ease,
            color 110ms ease,
            box-shadow 110ms ease;
    }

    .seller-premium-clear-filter svg {
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
    }

    .seller-premium-clear-filter:hover {
        border-color: #e0bd72;
        background: #fffaf1;
        color: #9b680e;
    }

    .seller-premium-clear-filter:focus-visible {
        outline: none;
        border-color: #d69a20;
        box-shadow: 0 0 0 3px rgba(214, 154, 32, .09);
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-premium-dropdown-trigger,
        .seller-premium-dropdown-chevron,
        .seller-premium-dropdown-menu,
        .seller-premium-dropdown-option,
        .seller-premium-dropdown-check,
        .seller-premium-clear-filter {
            transition: none !important;
        }
    }

    @media (max-width: 1023px) {
        .seller-premium-dropdown-menu {
            max-height: 230px;
        }
    }



    /* ============================================================
       PRODUCT FILTERS — FUNCTIONAL APPLY FLOW
       ============================================================ */

    /* IMPORTANT:
       Product cards are display:flex!important elsewhere in this file.
       This dedicated class must also use !important or filtering cannot
       visually hide non-matching cards. */
    #productsGrid [data-product-item].seller-filter-hidden {
        display: none !important;
    }

    .seller-premium-filter-actions {
        display: flex;
        min-width: max-content;
        align-items: center;
        gap: 6px;
    }

    .seller-premium-apply-filter {
        display: inline-flex;
        height: 34px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 1px solid #d29213;
        border-radius: 10px;
        background: #d29213;
        padding: 0 12px;
        color: #ffffff;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        font-size: 8.3px;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
        cursor: pointer;
        box-shadow: 0 6px 14px rgba(191, 126, 8, .12);
        transition:
            background-color 100ms ease,
            border-color 100ms ease,
            box-shadow 100ms ease,
            transform 100ms ease;
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
    }

    .seller-premium-apply-filter svg {
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
    }

    .seller-premium-apply-filter:hover {
        border-color: #c1830d;
        background: #c1830d;
        box-shadow: 0 7px 15px rgba(191, 126, 8, .15);
    }

    .seller-premium-apply-filter:active {
        transform: translateY(1px);
    }

    .seller-premium-apply-filter:focus-visible {
        outline: none;
        box-shadow:
            0 0 0 3px rgba(210, 146, 19, .13),
            0 6px 14px rgba(191,126,8,.12);
    }

    .seller-premium-apply-filter.is-dirty::after {
        content: "";
        width: 5px;
        height: 5px;
        flex: 0 0 5px;
        border-radius: 999px;
        background: #fff3b0;
        box-shadow: 0 0 0 2px rgba(255,255,255,.16);
    }

    .seller-premium-clear-filter {
        min-width: 34px;
        padding-inline: 9px;
    }

    @media (max-width: 1279px) {
        .seller-premium-filter-actions {
            width: 100%;
        }

        .seller-premium-apply-filter {
            flex: 1 1 auto;
        }
    }


    /*
     * PRODUCT CATALOG PERFORMANCE
     * Keep first-screen cards fully rendered; defer off-screen paint work.
     */
    #productsGrid [data-product-item] {
        content-visibility: auto;
        contain-intrinsic-size: auto 320px;
    }

    #productsGrid .seller-product-card-media {
        contain: paint;
    }



    /* ============================================================
       VIEW PRODUCT MODAL — COMPACT A4 INSPECTOR
       Visual-only refinement. Existing IDs and JS behavior stay intact.
       ============================================================ */

    #sellerViewProductModal {
        padding: 14px !important;
        background: rgba(24, 21, 17, .38) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
    }

    #sellerViewProductModal .seller-product-view-dialog {
        width: min(960px, calc(100vw - 28px)) !important;
        max-width: 960px !important;
        max-height: calc(100dvh - 28px) !important;
        border: 1px solid #e7e0d7 !important;
        border-radius: 20px !important;
        background: #fff !important;
        box-shadow:
            0 28px 72px rgba(31, 25, 18, .17),
            0 5px 18px rgba(31, 25, 18, .055) !important;
    }

    /* Header: compact document-style identity row. */
    #sellerViewProductModal .seller-product-view-dialog > div:first-child {
        min-height: 58px !important;
        padding: 10px 14px !important;
        border-bottom-color: #eee8e1 !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > div:first-child > div:first-child {
        gap: 10px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > div:first-child > div:first-child > span {
        width: 36px !important;
        height: 36px !important;
        border-radius: 11px !important;
        background: #fff9ef !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > div:first-child > div:first-child > span svg {
        width: 15px !important;
        height: 15px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > div:first-child p {
        font-size: 7px !important;
        letter-spacing: .13em !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > div:first-child h3 {
        margin-top: 1px !important;
        font-size: 16px !important;
        line-height: 1.05 !important;
    }

    #sellerViewProductModal [data-close-view]:first-of-type {
        width: 36px !important;
        height: 36px !important;
        border-radius: 11px !important;
    }

    /* Keep the sheet inside one viewport; only its content region may scroll. */
    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 {
        min-height: 0 !important;
        overflow-y: auto !important;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: #d9d1c8 transparent;
    }

    /* Main hero: deliberately compact so the details section remains visible. */
    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:first-child {
        gap: 16px !important;
        padding: 14px 16px !important;
    }

    #sellerViewProductModal #viewProductImageWrap {
        border-radius: 15px !important;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.35) !important;
    }

    #sellerViewProductModal #viewProductStatusBadge {
        left: 10px !important;
        top: 10px !important;
        min-height: 24px !important;
        padding-inline: 10px !important;
        font-size: 7px !important;
        backdrop-filter: none !important;
    }

    #sellerViewProductModal #viewProductImageWrap + .mt-3 {
        margin-top: 8px !important;
        gap: 7px !important;
    }

    #sellerViewProductModal #viewProductImageWrap + .mt-3 > div {
        min-height: 47px !important;
        border-radius: 10px !important;
        padding: 8px 10px !important;
        background: #fcfbf9 !important;
    }

    #sellerViewProductModal #viewProductImageWrap + .mt-3 p:first-child {
        font-size: 6.5px !important;
    }

    #sellerViewProductModal #viewProductCreated,
    #sellerViewProductModal #viewProductRating {
        margin-top: 3px !important;
        font-size: 8px !important;
        line-height: 1.25 !important;
    }

    #sellerViewProductModal #viewProductGallerySection {
        margin-top: 8px !important;
    }

    #sellerViewProductModal #viewProductGallery {
        display: flex !important;
        gap: 6px !important;
        overflow-x: auto !important;
        padding-bottom: 2px !important;
        scrollbar-width: thin;
    }

    #sellerViewProductModal #viewProductGallery > * {
        width: 43px !important;
        min-width: 43px !important;
        height: 43px !important;
        border-radius: 9px !important;
    }

    /* Product identity / price. */
    #sellerViewProductModal #viewProductCategoryChip,
    #sellerViewProductModal #viewProductTypeChip,
    #sellerViewProductModal #viewProductShippingChip {
        padding: 4px 8px !important;
        font-size: 6.7px !important;
    }

    #sellerViewProductModal #viewProductName {
        margin-top: 9px !important;
        font-size: 22px !important;
        line-height: 1.08 !important;
        letter-spacing: -.035em !important;
    }

    #sellerViewProductModal #viewProductBrandLine {
        margin-top: 4px !important;
        font-size: 8.5px !important;
    }

    #sellerViewProductModal #viewProductSalePrice {
        font-size: 27px !important;
    }

    #sellerViewProductModal #viewProductOriginalPrice {
        font-size: 9px !important;
    }

    #sellerViewProductModal #viewProductDiscountBadge {
        padding: 4px 8px !important;
        font-size: 6.8px !important;
    }

    #sellerViewProductModal #viewProductBrandLine + .mt-5 {
        margin-top: 13px !important;
        padding-bottom: 12px !important;
    }

    /* Four compact metrics in one clean row. */
    #sellerViewProductModal .seller-view-metric {
        min-width: 0 !important;
        min-height: 78px !important;
        border-radius: 12px !important;
        padding: 10px !important;
        background: #fcfbf9 !important;
        box-shadow: none !important;
    }

    #sellerViewProductModal .seller-view-metric > span {
        width: 27px !important;
        height: 27px !important;
        border-radius: 8px !important;
    }

    #sellerViewProductModal .seller-view-metric > span svg {
        width: 13px !important;
        height: 13px !important;
    }

    #sellerViewProductModal .seller-view-metric > p:nth-child(2) {
        margin-top: 7px !important;
        font-size: 6.5px !important;
    }

    #sellerViewProductModal .seller-view-metric > p:last-child {
        margin-top: 3px !important;
        font-size: 8.5px !important;
        line-height: 1.25 !important;
    }

    #sellerViewProductModal #viewProductStock {
        font-size: 12px !important;
    }

    /* Moderation strip stays visible but no longer consumes a large block. */
    #sellerViewProductModal .seller-view-metric + * {
        min-width: 0;
    }

    #sellerViewProductModal #viewProductStatus {
        margin-top: 2px !important;
        font-size: 8.5px !important;
    }

    #sellerViewProductModal #viewProductStatusBadge,
    #sellerViewProductModal #viewProductStatus {
        font-variant-numeric: tabular-nums;
    }

    #sellerViewProductModal .seller-view-metric ~ .mt-3\.5,
    #sellerViewProductModal .seller-view-metric ~ div.mt-3\.5 {
        margin-top: 10px !important;
    }

    #sellerViewProductModal .min-w-0 > .mt-3\.5.flex.items-start {
        gap: 9px !important;
        border-radius: 11px !important;
        padding: 9px 11px !important;
    }

    #sellerViewProductModal .min-w-0 > .mt-3\.5.flex.items-start > span {
        width: 27px !important;
        height: 27px !important;
        border-radius: 8px !important;
    }

    #sellerViewProductModal .min-w-0 > .mt-3\.5.flex.items-start p:first-child {
        font-size: 6.4px !important;
    }

    /* Bottom details become a compact two-column document section. */
    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) {
        gap: 10px !important;
        padding: 0 16px 12px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) > div {
        border-radius: 13px !important;
        padding: 12px !important;
        box-shadow: none !important;
        background: #fff !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) > div > .flex.items-center.gap-3 {
        gap: 9px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) > div > .flex.items-center.gap-3 > span {
        width: 29px !important;
        height: 29px !important;
        border-radius: 8px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) h4 {
        font-size: 9px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) h4 + p {
        margin-top: 1px !important;
        font-size: 6.5px !important;
    }

    #sellerViewProductModal #viewProductDescription {
        max-height: 84px !important;
        margin-top: 9px !important;
        overflow-y: auto !important;
        padding-right: 4px !important;
        font-size: 8px !important;
        line-height: 1.65 !important;
        scrollbar-width: thin;
    }

    /* Listing information uses two columns to reduce vertical height. */
    #sellerViewProductModal #viewProductDescription + * {
        min-width: 0;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) dl {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0 12px !important;
        margin-top: 8px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) dl > div {
        min-width: 0 !important;
        min-height: 29px !important;
        padding: 6px 0 !important;
        border-top: 1px solid #f0ebe5 !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) dl > div:nth-child(-n+2) {
        border-top: 0 !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) dt {
        font-size: 6.8px !important;
        white-space: nowrap !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) dd {
        overflow: hidden !important;
        max-width: 58% !important;
        font-size: 7.5px !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    /* Optional detail blocks stay compact and scroll only when truly needed. */
    #sellerViewProductModal #viewProductSpecificationsSection,
    #sellerViewProductModal #viewProductVariantsSection {
        margin-right: 16px !important;
        margin-left: 16px !important;
        margin-bottom: 12px !important;
        border-radius: 13px !important;
    }

    #sellerViewProductModal #viewProductSpecificationsSection > div:first-child,
    #sellerViewProductModal #viewProductVariantsSection > div:first-child {
        padding: 9px 12px !important;
    }

    #sellerViewProductModal #viewProductSpecificationsSection h4,
    #sellerViewProductModal #viewProductVariantsSection h4 {
        font-size: 9px !important;
    }

    #sellerViewProductModal #viewProductSpecificationsSection h4 + p,
    #sellerViewProductModal #viewProductVariantsSection h4 + p {
        font-size: 6.5px !important;
    }

    #sellerViewProductModal #viewProductSpecifications {
        gap: 7px !important;
        padding: 10px 12px !important;
    }

    #sellerViewProductModal [data-view-spec-card] {
        border-radius: 10px !important;
        padding: 8px 10px !important;
        box-shadow: none !important;
    }

    #sellerViewProductModal #viewProductVariantsSection .overflow-x-auto {
        max-height: 150px !important;
        overflow: auto !important;
    }

    #sellerViewProductModal #viewProductVariantsSection th,
    #sellerViewProductModal #viewProductVariantsSection td {
        padding-top: 7px !important;
        padding-bottom: 7px !important;
    }

    /* Footer resembles a clean document action bar. */
    #sellerViewProductModal .seller-product-view-dialog > div:last-child {
        min-height: 52px !important;
        padding: 8px 14px !important;
        border-top-color: #eee8e1 !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > div:last-child > p {
        font-size: 6.5px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > div:last-child button {
        height: 36px !important;
        border-radius: 10px !important;
        padding-inline: 13px !important;
        font-size: 8px !important;
        box-shadow: none !important;
    }

    #sellerViewProductModal #viewProductEditButton {
        background: #d58f08 !important;
        box-shadow: 0 6px 14px rgba(197, 126, 0, .13) !important;
    }

    #sellerViewProductModal #viewProductEditButton:hover {
        background: #c68104 !important;
    }

    @media (min-width: 900px) {
        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:first-child {
            grid-template-columns: 250px minmax(0, 1fr) !important;
        }

        #sellerViewProductModal #viewProductImageWrap {
            width: 250px !important;
            height: 250px !important;
            aspect-ratio: auto !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) {
            grid-template-columns: .95fr 1.05fr !important;
        }
    }

    /* Common 1366x768 laptop target: preserve the entire simple inspector in one screen. */
    @media (min-width: 900px) and (max-height: 820px) {
        #sellerViewProductModal {
            padding: 9px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog {
            width: min(930px, calc(100vw - 18px)) !important;
            max-width: 930px !important;
            max-height: calc(100dvh - 18px) !important;
            border-radius: 18px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > div:first-child {
            min-height: 52px !important;
            padding: 8px 12px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:first-child {
            grid-template-columns: 222px minmax(0, 1fr) !important;
            gap: 13px !important;
            padding: 11px 13px !important;
        }

        #sellerViewProductModal #viewProductImageWrap {
            width: 222px !important;
            height: 222px !important;
        }

        #sellerViewProductModal #viewProductName {
            font-size: 20px !important;
        }

        #sellerViewProductModal #viewProductSalePrice {
            font-size: 24px !important;
        }

        #sellerViewProductModal .seller-view-metric {
            min-height: 68px !important;
            padding: 8px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) {
            gap: 8px !important;
            padding: 0 13px 9px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) > div {
            padding: 9px 10px !important;
        }

        #sellerViewProductModal #viewProductDescription {
            max-height: 62px !important;
            margin-top: 6px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) dl {
            margin-top: 5px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) dl > div {
            min-height: 25px !important;
            padding: 4px 0 !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > div:last-child {
            min-height: 46px !important;
            padding: 6px 12px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > div:last-child button {
            height: 33px !important;
        }
    }

    @media (max-width: 899px) {
        #sellerViewProductModal {
            align-items: flex-start !important;
            padding: 8px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog {
            width: 100% !important;
            max-height: calc(100dvh - 16px) !important;
            border-radius: 17px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:first-child {
            padding: 12px !important;
        }

        #sellerViewProductModal #viewProductImageWrap {
            max-height: 310px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) {
            padding: 0 12px 10px !important;
        }
    }


    /* ============================================================
       VIEW PRODUCT MODAL — FLAT INFORMATION HIERARCHY
       Removes unnecessary card-within-card surfaces while keeping
       all existing IDs, data hooks, and product behavior unchanged.
       ============================================================ */

    /* Created / Rating become simple metadata, not separate cards. */
    #sellerViewProductModal #viewProductImageWrap + .mt-3 {
        display: flex !important;
        align-items: flex-start !important;
        gap: 0 !important;
        margin-top: 9px !important;
        padding: 0 2px !important;
    }

    #sellerViewProductModal #viewProductImageWrap + .mt-3 > div {
        min-height: 0 !important;
        flex: 1 1 50% !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 4px 10px 2px 0 !important;
        box-shadow: none !important;
    }

    #sellerViewProductModal #viewProductImageWrap + .mt-3 > div + div {
        border-left: 1px solid #ece6de !important;
        padding-left: 13px !important;
        padding-right: 0 !important;
    }

    #sellerViewProductModal #viewProductImageWrap + .mt-3 p:first-child {
        color: #9c9389 !important;
        font-size: 6.4px !important;
        line-height: 1 !important;
        letter-spacing: .08em !important;
    }

    #sellerViewProductModal #viewProductCreated,
    #sellerViewProductModal #viewProductRating {
        margin-top: 4px !important;
        color: #514940 !important;
        font-size: 8.2px !important;
        font-weight: 600 !important;
    }

    /* Product tags remain semantic but visually lighter. */
    #sellerViewProductModal #viewProductCategoryChip,
    #sellerViewProductModal #viewProductTypeChip,
    #sellerViewProductModal #viewProductShippingChip {
        min-height: 22px !important;
        border-width: 0 !important;
        border-radius: 7px !important;
        padding: 4px 8px !important;
        box-shadow: none !important;
    }

    #sellerViewProductModal #viewProductCategoryChip {
        background: #fff6e7 !important;
    }

    #sellerViewProductModal #viewProductTypeChip {
        background: #f4f2ef !important;
    }

    #sellerViewProductModal #viewProductShippingChip {
        background: #eef8f2 !important;
    }

    /* Stock / SKU / Brand / Type become one open information strip. */
    #sellerViewProductModal .min-w-0 > .mt-4.grid:has(.seller-view-metric) {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 0 !important;
        margin-top: 11px !important;
        border-top: 1px solid #ece6de !important;
        border-bottom: 1px solid #ece6de !important;
        background: transparent !important;
    }

    #sellerViewProductModal .seller-view-metric {
        min-height: 62px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 10px 12px !important;
        box-shadow: none !important;
    }

    #sellerViewProductModal .seller-view-metric + .seller-view-metric {
        border-left: 1px solid #eee8e1 !important;
    }

    #sellerViewProductModal .seller-view-metric > span {
        display: inline-grid !important;
        width: 19px !important;
        height: 19px !important;
        place-items: center !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    #sellerViewProductModal .seller-view-metric > span svg {
        width: 13px !important;
        height: 13px !important;
    }

    #sellerViewProductModal .seller-view-metric > p:nth-child(2) {
        margin-top: 4px !important;
        color: #9b9288 !important;
        font-size: 6.2px !important;
        line-height: 1 !important;
    }

    #sellerViewProductModal .seller-view-metric > p:last-child,
    #sellerViewProductModal #viewProductStock {
        margin-top: 4px !important;
        color: #3f3933 !important;
        font-size: 8.4px !important;
        line-height: 1.25 !important;
        font-weight: 700 !important;
    }

    #sellerViewProductModal #viewProductStock {
        font-size: 11px !important;
    }

    /* Moderation becomes a compact status line instead of a large card. */
    #sellerViewProductModal .min-w-0 > .mt-3\.5.flex.items-start {
        min-height: 32px !important;
        align-items: center !important;
        gap: 7px !important;
        margin-top: 8px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 4px 1px 0 !important;
        box-shadow: none !important;
    }

    #sellerViewProductModal .min-w-0 > .mt-3\.5.flex.items-start > span {
        width: 19px !important;
        height: 19px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        color: #b37a13 !important;
    }

    #sellerViewProductModal .min-w-0 > .mt-3\.5.flex.items-start > span svg {
        width: 14px !important;
        height: 14px !important;
    }

    #sellerViewProductModal .min-w-0 > .mt-3\.5.flex.items-start > div {
        display: flex !important;
        min-width: 0 !important;
        align-items: center !important;
        gap: 8px !important;
    }

    #sellerViewProductModal .min-w-0 > .mt-3\.5.flex.items-start p:first-child {
        margin: 0 !important;
        color: #9a8766 !important;
        font-size: 6.4px !important;
        line-height: 1 !important;
        letter-spacing: .08em !important;
    }

    #sellerViewProductModal #viewProductStatus {
        display: inline-flex !important;
        min-height: 22px !important;
        align-items: center !important;
        margin: 0 !important;
        border-radius: 999px !important;
        background: #fff5df !important;
        padding: 0 9px !important;
        color: #94620c !important;
        font-size: 7.3px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
    }

    /* Keep the hero airy after removing nested surfaces. */
    #sellerViewProductModal #viewProductBrandLine + .mt-5 {
        margin-top: 11px !important;
        padding-bottom: 10px !important;
    }

    @media (max-width: 899px) {
        #sellerViewProductModal .min-w-0 > .mt-4.grid:has(.seller-view-metric) {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        #sellerViewProductModal .seller-view-metric:nth-child(3) {
            border-left: 0 !important;
            border-top: 1px solid #eee8e1 !important;
        }

        #sellerViewProductModal .seller-view-metric:nth-child(4) {
            border-top: 1px solid #eee8e1 !important;
        }
    }

    @media (max-width: 520px) {
        #sellerViewProductModal .min-w-0 > .mt-3\.5.flex.items-start > div {
            flex-wrap: wrap !important;
            gap: 5px 8px !important;
        }
    }


    /* ============================================================
       VIEW + EDIT PRODUCT — FINAL COMPACT MODAL POLISH
       Visual-only. No IDs, fields, endpoints, or JS hooks changed.
       ============================================================ */

    /* View header: title only. The decorative Listing Details icon is removed. */
    #sellerViewProductModal .seller-product-view-dialog > div:first-child > div:first-child {
        gap: 0 !important;
    }

    /* Footer Close button: stable width/alignment and no squeezed text. */
    #sellerViewProductModal .seller-product-view-dialog > div:last-child [data-close-view] {
        display: inline-flex !important;
        min-width: 64px !important;
        height: 34px !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 14px !important;
        line-height: 1 !important;
        white-space: nowrap !important;
        flex: 0 0 auto !important;
    }

    /* Edit Product becomes a compact desktop document instead of a tall form. */
    #sellerEditProductModal {
        padding: 10px !important;
        background: rgba(24, 21, 17, .40) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
    }

    #sellerEditProductModal > div {
        display: flex !important;
        width: min(980px, calc(100vw - 20px)) !important;
        max-width: 980px !important;
        max-height: calc(100dvh - 20px) !important;
        flex-direction: column !important;
        overflow: hidden !important;
        border: 1px solid #e7e0d7 !important;
        border-radius: 19px !important;
        background: #fff !important;
        box-shadow:
            0 28px 72px rgba(31, 25, 18, .17),
            0 5px 18px rgba(31, 25, 18, .055) !important;
    }

    #sellerEditProductModal > div > div:first-child {
        position: static !important;
        min-height: 62px !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        padding: 10px 16px !important;
        border-bottom-color: #eee8e1 !important;
    }

    #sellerEditProductModal > div > div:first-child > div {
        min-width: 0 !important;
    }

    #sellerEditProductModal > div > div:first-child p:first-child {
        font-size: 7px !important;
        line-height: 1 !important;
        letter-spacing: .14em !important;
    }

    #sellerEditProductModal > div > div:first-child h3 {
        margin-top: 4px !important;
        font-size: 17px !important;
        line-height: 1 !important;
        letter-spacing: -.025em !important;
    }

    #sellerEditProductModal > div > div:first-child h3 + p {
        overflow: hidden !important;
        max-width: 720px !important;
        margin-top: 4px !important;
        color: #91887d !important;
        font-size: 7.5px !important;
        line-height: 1.35 !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    #sellerEditProductModal [data-close-edit]:first-of-type {
        width: 34px !important;
        height: 34px !important;
        min-width: 34px !important;
        border-radius: 10px !important;
        font-size: 18px !important;
        line-height: 1 !important;
    }

    #sellerEditProductForm {
        min-height: 0 !important;
        flex: 1 1 auto !important;
        overflow-y: auto !important;
        padding: 13px 16px 10px !important;
        scrollbar-width: thin;
        scrollbar-color: #d9d1c8 transparent;
    }

    #sellerEditProductForm > .grid {
        gap: 9px 10px !important;
    }

    #sellerEditProductForm label.mb-2 {
        margin-bottom: 5px !important;
        font-size: 8px !important;
        line-height: 1.15 !important;
    }

    #sellerEditProductForm input:not([type="hidden"]):not([type="checkbox"]),
    #sellerEditProductForm select {
        height: 36px !important;
        border-radius: 9px !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        font-size: 8.5px !important;
    }

    #sellerEditProductForm select {
        padding-left: 11px !important;
    }

    #sellerEditProductForm textarea {
        height: 72px !important;
        min-height: 72px !important;
        border-radius: 9px !important;
        padding: 9px 11px !important;
        font-size: 8.5px !important;
        line-height: 1.45 !important;
    }

    #sellerEditProductForm #editProductSalePreview,
    #sellerEditProductForm #editProductFlashSaleEndsAtLocal ~ p {
        margin-top: 4px !important;
        font-size: 6.3px !important;
        line-height: 1.35 !important;
    }

    /* Free Shipping is a slim control instead of a tall card. */
    #sellerEditProductForm label:has(#editProductFreeShipping) {
        min-height: 58px !important;
        padding: 9px 11px !important;
        border-radius: 10px !important;
        background: #fff !important;
    }

    #sellerEditProductForm label:has(#editProductFreeShipping) > span:first-child > span:first-child {
        font-size: 8px !important;
    }

    #sellerEditProductForm label:has(#editProductFreeShipping) > span:first-child > span:last-child {
        margin-top: 3px !important;
        font-size: 6.4px !important;
        line-height: 1.35 !important;
    }

    /* Image replacement stays compact beside Description. */
    #sellerEditProductForm #editCurrentImageWrap {
        height: 66px !important;
        border-radius: 9px !important;
    }

    #sellerEditProductForm input[type="file"] {
        height: 38px !important;
        padding: 7px 9px !important;
        font-size: 7.3px !important;
    }

    /* Sensitive-edit note becomes an unobtrusive single strip. */
    #sellerEditProductForm > .mt-5.rounded-\[14px\] {
        margin-top: 10px !important;
        border-radius: 9px !important;
        padding: 8px 10px !important;
        font-size: 7px !important;
        line-height: 1.45 !important;
    }

    /* Compact sticky action bar when a short viewport actually needs scrolling. */
    #sellerEditProductForm > div:last-child {
        position: sticky !important;
        z-index: 3 !important;
        bottom: -10px !important;
        margin: 10px -16px -10px !important;
        padding: 8px 16px 10px !important;
        border-top: 1px solid #eee8df !important;
        background: rgba(255,255,255,.97) !important;
        backdrop-filter: blur(8px) !important;
    }

    #sellerEditProductForm > div:last-child button {
        display: inline-flex !important;
        height: 34px !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 9px !important;
        padding-inline: 14px !important;
        font-size: 8px !important;
        line-height: 1 !important;
        white-space: nowrap !important;
    }

    @media (min-width: 900px) {
        #sellerEditProductForm > .grid {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        }

        /* Existing md:col-span-2 fields become two of four columns. */
        #sellerEditProductForm > .grid > .md\:col-span-2 {
            grid-column: span 2 / span 2 !important;
        }
    }

    /* Common laptop target: fit the complete edit form without a tall modal. */
    @media (min-width: 900px) and (max-height: 820px) {
        #sellerEditProductModal {
            padding: 7px !important;
        }

        #sellerEditProductModal > div {
            width: min(960px, calc(100vw - 14px)) !important;
            max-width: 960px !important;
            max-height: calc(100dvh - 14px) !important;
            border-radius: 17px !important;
        }

        #sellerEditProductModal > div > div:first-child {
            min-height: 54px !important;
            padding: 8px 14px !important;
        }

        #sellerEditProductModal > div > div:first-child h3 {
            font-size: 16px !important;
        }

        #sellerEditProductModal > div > div:first-child h3 + p {
            font-size: 6.8px !important;
        }

        #sellerEditProductForm {
            padding: 10px 14px 8px !important;
        }

        #sellerEditProductForm > .grid {
            gap: 7px 9px !important;
        }

        #sellerEditProductForm label.mb-2 {
            margin-bottom: 4px !important;
            font-size: 7.5px !important;
        }

        #sellerEditProductForm input:not([type="hidden"]):not([type="checkbox"]),
        #sellerEditProductForm select {
            height: 33px !important;
            font-size: 8px !important;
        }

        #sellerEditProductForm textarea {
            height: 61px !important;
            min-height: 61px !important;
        }

        #sellerEditProductForm label:has(#editProductFreeShipping) {
            min-height: 52px !important;
            padding: 7px 9px !important;
        }

        #sellerEditProductForm #editCurrentImageWrap {
            height: 57px !important;
        }

        #sellerEditProductForm > .mt-5.rounded-\[14px\] {
            margin-top: 8px !important;
            padding: 6px 9px !important;
            font-size: 6.5px !important;
        }

        #sellerEditProductForm > div:last-child {
            margin-top: 8px !important;
            padding-top: 7px !important;
            padding-bottom: 8px !important;
        }
    }

    @media (max-width: 899px) {
        #sellerEditProductModal {
            align-items: flex-start !important;
            padding: 8px !important;
        }

        #sellerEditProductModal > div {
            width: 100% !important;
            max-height: calc(100dvh - 16px) !important;
            border-radius: 16px !important;
        }

        #sellerEditProductModal > div > div:first-child h3 + p {
            white-space: normal !important;
        }
    }


    /* ============================================================
       EDIT PRODUCT — A4-INSPIRED PORTRAIT SHEET FINAL
       Layout-only. Keeps all existing fields, IDs and JS hooks.
       ============================================================ */
    #sellerEditProductModal {
        align-items: center !important;
        justify-content: center !important;
        overflow: hidden !important;
        padding: 16px !important;
    }

    #sellerEditProductModal > div {
        width: min(690px, calc(100vw - 32px)) !important;
        max-width: 690px !important;
        max-height: min(92dvh, 860px) !important;
        overflow: hidden !important;
        border-radius: 20px !important;
    }

    #sellerEditProductModal > div > div:first-child {
        min-height: 58px !important;
        padding: 10px 16px !important;
    }

    #sellerEditProductModal > div > div:first-child h3 {
        font-size: 17px !important;
    }

    #sellerEditProductModal > div > div:first-child h3 + p {
        max-width: 540px !important;
        font-size: 7px !important;
    }

    #sellerEditProductForm {
        overflow-x: hidden !important;
        overflow-y: auto !important;
        padding: 13px 16px 10px !important;
    }

    #sellerEditProductForm,
    #sellerEditProductForm * {
        box-sizing: border-box;
    }

    #sellerEditProductForm > .grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px 10px !important;
        width: 100% !important;
        min-width: 0 !important;
    }

    #sellerEditProductForm > .grid > * {
        min-width: 0 !important;
    }

    /* Product name remains a clear full-width first row. */
    #sellerEditProductForm > .grid > div:has(#editProductName) {
        grid-column: 1 / -1 !important;
    }

    /* Free Shipping is one quiet full-width preference row. */
    #sellerEditProductForm > .grid > div:has(#editProductFreeShipping) {
        grid-column: 1 / -1 !important;
    }

    /* Image and description share the same final content row. */
    #sellerEditProductForm > .grid > div:has(#editCurrentImageWrap),
    #sellerEditProductForm > .grid > div:has(#editProductDescription) {
        grid-column: span 1 / span 1 !important;
    }

    #sellerEditProductForm label.mb-2 {
        margin-bottom: 4px !important;
        font-size: 7.8px !important;
    }

    #sellerEditProductForm input:not([type="hidden"]):not([type="checkbox"]),
    #sellerEditProductForm select {
        width: 100% !important;
        min-width: 0 !important;
        height: 34px !important;
        border-radius: 9px !important;
        padding-inline: 10px !important;
        font-size: 8px !important;
    }

    #sellerEditProductForm textarea {
        width: 100% !important;
        min-width: 0 !important;
        height: 68px !important;
        min-height: 68px !important;
        padding: 8px 10px !important;
        font-size: 8px !important;
    }

    #sellerEditProductForm label:has(#editProductFreeShipping) {
        min-height: 48px !important;
        padding: 8px 10px !important;
    }

    #sellerEditProductForm #editCurrentImageWrap {
        width: 72px !important;
        height: 58px !important;
    }

    #sellerEditProductForm > .grid > div:has(#editCurrentImageWrap) > .grid {
        grid-template-columns: 72px minmax(0, 1fr) !important;
        gap: 8px !important;
        align-items: center !important;
    }

    #sellerEditProductForm input[type="file"] {
        min-width: 0 !important;
        max-width: 100% !important;
        height: 36px !important;
        overflow: hidden !important;
        padding: 6px 8px !important;
        font-size: 7px !important;
    }

    #sellerEditProductForm #editProductSalePreview,
    #sellerEditProductForm #editProductFlashSaleEndsAtLocal ~ p {
        margin-top: 3px !important;
        font-size: 6px !important;
        line-height: 1.3 !important;
    }

    #sellerEditProductForm > .mt-5.rounded-\[14px\] {
        margin-top: 9px !important;
        padding: 7px 9px !important;
        border-radius: 9px !important;
        font-size: 6.4px !important;
        line-height: 1.35 !important;
    }

    #sellerEditProductForm > div:last-child {
        bottom: -10px !important;
        margin: 9px -16px -10px !important;
        padding: 8px 16px 10px !important;
        overflow: hidden !important;
    }

    #sellerEditProductForm > div:last-child button {
        min-width: 86px !important;
        height: 34px !important;
        padding-inline: 14px !important;
        font-size: 8px !important;
    }

    @media (min-width: 760px) and (max-height: 820px) {
        #sellerEditProductModal {
            padding: 10px !important;
        }

        #sellerEditProductModal > div {
            width: min(660px, calc(100vw - 20px)) !important;
            max-width: 660px !important;
            max-height: calc(100dvh - 20px) !important;
            border-radius: 18px !important;
        }

        #sellerEditProductModal > div > div:first-child {
            min-height: 52px !important;
            padding: 8px 14px !important;
        }

        #sellerEditProductModal > div > div:first-child h3 {
            font-size: 16px !important;
        }

        #sellerEditProductForm {
            padding: 10px 14px 8px !important;
        }

        #sellerEditProductForm > .grid {
            gap: 6px 8px !important;
        }

        #sellerEditProductForm input:not([type="hidden"]):not([type="checkbox"]),
        #sellerEditProductForm select {
            height: 32px !important;
        }

        #sellerEditProductForm textarea {
            height: 58px !important;
            min-height: 58px !important;
        }

        #sellerEditProductForm label:has(#editProductFreeShipping) {
            min-height: 44px !important;
        }

        #sellerEditProductForm #editCurrentImageWrap {
            width: 66px !important;
            height: 52px !important;
        }

        #sellerEditProductForm > .grid > div:has(#editCurrentImageWrap) > .grid {
            grid-template-columns: 66px minmax(0, 1fr) !important;
        }

        #sellerEditProductForm > .mt-5.rounded-\[14px\] {
            margin-top: 7px !important;
            padding: 6px 8px !important;
            font-size: 6px !important;
        }

        #sellerEditProductForm > div:last-child {
            margin: 7px -14px -8px !important;
            padding: 7px 14px 8px !important;
        }
    }

    @media (max-width: 759px) {
        #sellerEditProductModal {
            align-items: flex-start !important;
            padding: 8px !important;
        }

        #sellerEditProductModal > div {
            width: 100% !important;
            max-width: 100% !important;
            max-height: calc(100dvh - 16px) !important;
            border-radius: 16px !important;
        }

        #sellerEditProductForm > .grid {
            grid-template-columns: 1fr !important;
        }

        #sellerEditProductForm > .grid > div:has(#editProductName),
        #sellerEditProductForm > .grid > div:has(#editProductFreeShipping),
        #sellerEditProductForm > .grid > div:has(#editCurrentImageWrap),
        #sellerEditProductForm > .grid > div:has(#editProductDescription) {
            grid-column: auto !important;
        }

        #sellerEditProductModal > div > div:first-child h3 + p {
            white-space: normal !important;
        }
    }


    /* ============================================================
       VIEW PRODUCT + EDIT ACTIONS — A4 CONSISTENCY FINAL PASS
       Visual/layout only. Existing IDs, data hooks and backend stay intact.
       ============================================================ */

    /* ---------- PRODUCT INSPECTOR: A4-inspired portrait sheet ---------- */
    #sellerViewProductModal {
        align-items: center !important;
        justify-content: center !important;
        overflow: hidden !important;
        padding: 12px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog {
        width: min(690px, calc(100vw - 24px)) !important;
        max-width: 690px !important;
        max-height: calc(100dvh - 24px) !important;
        border-radius: 19px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > div:first-child {
        min-height: 54px !important;
        padding: 9px 13px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > div:first-child p {
        font-size: 6.6px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > div:first-child h3 {
        margin-top: 2px !important;
        font-size: 16px !important;
    }

    #sellerViewProductModal [data-close-view]:first-of-type {
        width: 34px !important;
        height: 34px !important;
        min-width: 34px !important;
        border-radius: 10px !important;
    }

    /* Main product identity uses the same compact document rhythm as Edit. */
    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:first-child {
        grid-template-columns: 178px minmax(0, 1fr) !important;
        gap: 14px !important;
        padding: 12px 14px !important;
    }

    #sellerViewProductModal #viewProductImageWrap {
        width: 178px !important;
        height: 178px !important;
        aspect-ratio: auto !important;
        border-radius: 14px !important;
    }

    #sellerViewProductModal #viewProductStatusBadge {
        left: 8px !important;
        top: 8px !important;
        min-height: 22px !important;
        padding-inline: 8px !important;
        font-size: 6.5px !important;
    }

    /* Created / Rating stay as simple metadata, never mini-cards. */
    #sellerViewProductModal #viewProductImageWrap + .mt-3 {
        margin-top: 7px !important;
        padding: 0 !important;
        gap: 0 !important;
    }

    #sellerViewProductModal #viewProductImageWrap + .mt-3 > div {
        min-height: 34px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 5px 8px 2px !important;
        box-shadow: none !important;
    }

    #sellerViewProductModal #viewProductImageWrap + .mt-3 > div:first-child {
        padding-left: 0 !important;
        border-right: 1px solid #eee8e1 !important;
    }

    #sellerViewProductModal #viewProductImageWrap + .mt-3 > div:last-child {
        padding-right: 0 !important;
    }

    #sellerViewProductModal #viewProductCategoryChip,
    #sellerViewProductModal #viewProductTypeChip,
    #sellerViewProductModal #viewProductShippingChip {
        padding: 3px 7px !important;
        font-size: 6.2px !important;
    }

    #sellerViewProductModal #viewProductName {
        margin-top: 8px !important;
        font-size: 20px !important;
        line-height: 1.06 !important;
    }

    #sellerViewProductModal #viewProductBrandLine {
        margin-top: 3px !important;
        font-size: 7.8px !important;
    }

    #sellerViewProductModal #viewProductBrandLine + .mt-5 {
        margin-top: 10px !important;
        padding-bottom: 9px !important;
    }

    #sellerViewProductModal #viewProductSalePrice {
        font-size: 24px !important;
    }

    #sellerViewProductModal #viewProductOriginalPrice {
        font-size: 8px !important;
    }

    #sellerViewProductModal #viewProductDiscountBadge {
        padding: 3px 7px !important;
        font-size: 6.2px !important;
    }

    /* One flat four-column fact strip — no icon boxes, no nested cards. */
    #sellerViewProductModal .min-w-0 > .mt-4.grid:has(.seller-view-metric) {
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 0 !important;
        margin-top: 9px !important;
        border-top: 1px solid #eee8e1 !important;
        border-bottom: 1px solid #eee8e1 !important;
    }

    #sellerViewProductModal .seller-view-metric {
        min-height: 52px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 9px 10px !important;
        box-shadow: none !important;
    }

    #sellerViewProductModal .seller-view-metric + .seller-view-metric {
        border-left: 1px solid #eee8e1 !important;
    }

    #sellerViewProductModal .seller-view-metric > span {
        display: none !important;
    }

    #sellerViewProductModal .seller-view-metric > p:nth-child(2) {
        margin-top: 0 !important;
        font-size: 6px !important;
        line-height: 1.1 !important;
    }

    #sellerViewProductModal .seller-view-metric > p:last-child,
    #sellerViewProductModal #viewProductStock {
        margin-top: 5px !important;
        font-size: 8.1px !important;
        line-height: 1.15 !important;
    }

    #sellerViewProductModal #viewProductStock {
        font-size: 10px !important;
    }

    /* Moderation is one lightweight metadata line. */
    #sellerViewProductModal .min-w-0 > .mt-3\.5.flex.items-start {
        min-height: 28px !important;
        margin-top: 7px !important;
        padding: 3px 0 0 !important;
        gap: 6px !important;
    }

    #sellerViewProductModal .min-w-0 > .mt-3\.5.flex.items-start > span {
        display: none !important;
    }

    #sellerViewProductModal .min-w-0 > .mt-3\.5.flex.items-start > div {
        width: 100% !important;
        justify-content: space-between !important;
        gap: 10px !important;
    }

    #sellerViewProductModal #viewProductStatus {
        min-height: 21px !important;
        padding-inline: 8px !important;
        font-size: 6.7px !important;
    }

    /* Description + Listing Information: open document sections, not cards. */
    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) {
        grid-template-columns: .88fr 1.12fr !important;
        gap: 0 !important;
        padding: 0 14px 10px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) > div {
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 10px 11px !important;
        box-shadow: none !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) > div:first-child {
        padding-left: 0 !important;
        padding-right: 14px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) > div:last-child {
        padding-right: 0 !important;
        padding-left: 14px !important;
        border-left: 1px solid #eee8e1 !important;
    }

    /* Decorative section icons are unnecessary in this compact inspector. */
    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) > div > .flex.items-center.gap-3 > span {
        display: none !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) > div > .flex.items-center.gap-3 {
        gap: 0 !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) h4 {
        font-size: 8.5px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) h4 + p {
        font-size: 6.2px !important;
    }

    #sellerViewProductModal #viewProductDescription {
        max-height: 62px !important;
        margin-top: 7px !important;
        font-size: 7.5px !important;
        line-height: 1.55 !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) dl {
        gap: 0 10px !important;
        margin-top: 6px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) dl > div {
        min-height: 23px !important;
        padding: 4px 0 !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) dt {
        font-size: 6.2px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) dd {
        font-size: 6.8px !important;
    }

    /* Compact document footer with correctly aligned actions. */
    #sellerViewProductModal .seller-product-view-dialog > div:last-child {
        min-height: 46px !important;
        padding: 6px 13px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > div:last-child button,
    #sellerViewProductModal .seller-product-view-dialog > div:last-child [data-close-view] {
        display: inline-flex !important;
        width: auto !important;
        min-width: 68px !important;
        height: 32px !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 9px !important;
        padding: 0 12px !important;
        font-size: 7.5px !important;
        line-height: 1 !important;
        white-space: nowrap !important;
    }

    #sellerViewProductModal #viewProductEditButton {
        min-width: 96px !important;
    }

    /* ---------- EDIT PRODUCT: separate header × from footer Cancel ---------- */
    /* This fixes the old :first-of-type rule accidentally styling Cancel as ×. */
    #sellerEditProductModal > div > div:first-child [data-close-edit] {
        display: grid !important;
        width: 34px !important;
        min-width: 34px !important;
        height: 34px !important;
        place-items: center !important;
        border-radius: 10px !important;
        padding: 0 !important;
        font-size: 18px !important;
        line-height: 1 !important;
    }

    #sellerEditProductForm > div:last-child {
        align-items: center !important;
        gap: 8px !important;
    }

    #sellerEditProductForm > div:last-child [data-close-edit],
    #sellerEditProductForm > div:last-child button[type="submit"] {
        display: inline-flex !important;
        width: auto !important;
        min-width: 92px !important;
        height: 34px !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 9px !important;
        padding: 0 14px !important;
        font-size: 8px !important;
        line-height: 1 !important;
        font-weight: 650 !important;
        white-space: nowrap !important;
    }

    #sellerEditProductForm > div:last-child [data-close-edit] {
        border: 1px solid #ded6cc !important;
        background: #fff !important;
        color: #62594e !important;
    }

    #sellerEditProductForm > div:last-child button[type="submit"] {
        border: 1px solid #c98a0c !important;
        background: #d38e08 !important;
        color: #fff !important;
    }

    @media (min-width: 760px) and (max-height: 820px) {
        #sellerViewProductModal {
            padding: 8px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog {
            width: min(650px, calc(100vw - 16px)) !important;
            max-width: 650px !important;
            max-height: calc(100dvh - 16px) !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:first-child {
            grid-template-columns: 160px minmax(0, 1fr) !important;
            gap: 12px !important;
            padding: 10px 12px !important;
        }

        #sellerViewProductModal #viewProductImageWrap {
            width: 160px !important;
            height: 160px !important;
        }

        #sellerViewProductModal #viewProductName {
            font-size: 18px !important;
        }

        #sellerViewProductModal #viewProductSalePrice {
            font-size: 22px !important;
        }

        #sellerViewProductModal .seller-view-metric {
            min-height: 48px !important;
            padding: 8px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) {
            padding: 0 12px 8px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > div:last-child {
            min-height: 42px !important;
            padding: 5px 11px !important;
        }
    }

    @media (max-width: 759px) {
        #sellerViewProductModal {
            align-items: flex-start !important;
            padding: 8px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog {
            width: 100% !important;
            max-width: 100% !important;
            max-height: calc(100dvh - 16px) !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:first-child,
        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) {
            grid-template-columns: 1fr !important;
        }

        #sellerViewProductModal #viewProductImageWrap {
            width: 100% !important;
            height: auto !important;
            aspect-ratio: 1 / 1 !important;
            max-height: 300px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) > div:first-child,
        #sellerViewProductModal .seller-product-view-dialog > .min-h-0.flex-1 > section:nth-child(2) > div:last-child {
            padding: 10px 0 !important;
            border-left: 0 !important;
        }

        #sellerViewProductModal .min-w-0 > .mt-4.grid:has(.seller-view-metric) {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        #sellerViewProductModal .seller-view-metric:nth-child(3) {
            border-left: 0 !important;
            border-top: 1px solid #eee8e1 !important;
        }

        #sellerViewProductModal .seller-view-metric:nth-child(4) {
            border-top: 1px solid #eee8e1 !important;
        }
    }



    /* ============================================================
       VIEW PRODUCT — ENTERPRISE REFERENCE DESIGN FINAL
       Matches the approved generated mockup. View/Edit behavior unchanged.
       ============================================================ */

    #sellerViewProductModal {
        align-items: center !important;
        justify-content: center !important;
        overflow: hidden !important;
        padding: 18px !important;
        background: rgba(20, 22, 25, .42) !important;
        backdrop-filter: blur(5px) !important;
        -webkit-backdrop-filter: blur(5px) !important;
    }

    #sellerViewProductModal .seller-view-enterprise-dialog {
        display: flex !important;
        width: min(980px, calc(100vw - 36px)) !important;
        max-width: 980px !important;
        max-height: calc(100dvh - 36px) !important;
        flex-direction: column !important;
        overflow: hidden !important;
        border: 1px solid #e7e3dd !important;
        border-radius: 24px !important;
        background: #ffffff !important;
        box-shadow:
            0 34px 90px rgba(20, 22, 25, .20),
            0 10px 30px rgba(20, 22, 25, .08) !important;
        font-family: "Poppins", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
    }

    #sellerViewProductModal .seller-view-enterprise-header {
        display: flex !important;
        min-height: 92px !important;
        flex: 0 0 auto !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 24px !important;
        border-bottom: 1px solid #eeeae4 !important;
        background: #ffffff !important;
        padding: 24px 28px 20px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-eyebrow {
        margin: 0 !important;
        color: #b9780b !important;
        font-size: 10px !important;
        line-height: 1 !important;
        font-weight: 800 !important;
        letter-spacing: .18em !important;
        text-transform: uppercase !important;
    }

    #sellerViewProductModal .seller-view-enterprise-header h3 {
        margin: 10px 0 0 !important;
        color: #191d27 !important;
        font-size: 29px !important;
        line-height: 1.03 !important;
        font-weight: 760 !important;
        letter-spacing: -.045em !important;
    }

    #sellerViewProductModal .seller-view-enterprise-subtitle {
        margin: 9px 0 0 !important;
        color: #858b97 !important;
        font-size: 12px !important;
        line-height: 1.5 !important;
        font-weight: 450 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-close {
        display: grid !important;
        width: 46px !important;
        height: 46px !important;
        min-width: 46px !important;
        flex: 0 0 46px !important;
        place-items: center !important;
        border: 1px solid #dfddd9 !important;
        border-radius: 13px !important;
        background: #ffffff !important;
        padding: 0 !important;
        color: #20242b !important;
        box-shadow: 0 2px 8px rgba(20, 22, 25, .025) !important;
        cursor: pointer !important;
        transition: border-color .14s ease, background-color .14s ease, color .14s ease !important;
    }

    #sellerViewProductModal .seller-view-enterprise-close:hover {
        border-color: #d7c7a7 !important;
        background: #fffaf0 !important;
        color: #9b6810 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-close svg {
        width: 19px !important;
        height: 19px !important;
        stroke-width: 1.9 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-body {
        min-height: 0 !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        overscroll-behavior: contain !important;
        background: #ffffff !important;
        scrollbar-width: thin !important;
        scrollbar-color: #d7d3ce transparent !important;
    }

    #sellerViewProductModal .seller-view-enterprise-hero {
        display: grid !important;
        grid-template-columns: 300px minmax(0, 1fr) !important;
        gap: 26px 30px !important;
        padding: 28px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-media {
        min-width: 0 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-image-wrap {
        position: relative !important;
        width: 300px !important;
        height: 300px !important;
        overflow: hidden !important;
        border: 1px solid #e5e2dc !important;
        border-radius: 18px !important;
        background: #f3f2ef !important;
        box-shadow: 0 7px 20px rgba(26, 27, 30, .035) !important;
    }

    #sellerViewProductModal #viewProductImage {
        display: block !important;
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }

    #sellerViewProductModal #viewProductImage.hidden {
        display: none !important;
    }

    #sellerViewProductModal .seller-view-enterprise-image-placeholder {
        display: grid !important;
        width: 100% !important;
        height: 100% !important;
        place-items: center !important;
        color: #979ca5 !important;
        text-align: center !important;
    }

    #sellerViewProductModal .seller-view-enterprise-image-placeholder > div > span {
        display: grid !important;
        width: 54px !important;
        height: 54px !important;
        margin: 0 auto !important;
        place-items: center !important;
        border: 1px solid #dfddd8 !important;
        border-radius: 14px !important;
        background: #ffffff !important;
    }

    #sellerViewProductModal .seller-view-enterprise-image-placeholder svg {
        width: 24px !important;
        height: 24px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-image-placeholder p {
        margin: 10px 0 0 !important;
        font-size: 10px !important;
        font-weight: 600 !important;
    }

    #sellerViewProductModal #viewProductStatusBadge {
        left: 14px !important;
        top: 14px !important;
        display: inline-flex !important;
        min-height: 34px !important;
        align-items: center !important;
        gap: 7px !important;
        border-radius: 12px !important;
        padding: 0 14px !important;
        font-size: 10px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        box-shadow:
            0 8px 18px rgba(22, 24, 27, .08),
            inset 0 1px 0 rgba(255, 255, 255, .70) !important;
        backdrop-filter: blur(6px) !important;
    }

    #sellerViewProductModal #viewProductStatusBadge::before {
        content: "◷";
        font-size: 14px !important;
        line-height: 1 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-gallery {
        margin-top: 12px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-gallery-head {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 10px !important;
        margin-bottom: 7px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-gallery-head p,
    #sellerViewProductModal .seller-view-enterprise-gallery-head span {
        margin: 0 !important;
        color: #8d939c !important;
        font-size: 8px !important;
        line-height: 1 !important;
        font-weight: 650 !important;
    }

    #sellerViewProductModal #viewProductGallery {
        display: flex !important;
        gap: 7px !important;
        overflow-x: auto !important;
        padding-bottom: 2px !important;
        scrollbar-width: thin !important;
    }

    #sellerViewProductModal #viewProductGallery > * {
        width: 48px !important;
        min-width: 48px !important;
        height: 48px !important;
        border-radius: 10px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-summary {
        min-width: 0 !important;
        padding-top: 12px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-chips {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 9px !important;
    }

    #sellerViewProductModal .seller-view-chip {
        display: inline-flex !important;
        min-height: 31px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 7px !important;
        border: 0 !important;
        border-radius: 11px !important;
        padding: 0 13px !important;
        font-size: 9px !important;
        line-height: 1 !important;
        font-weight: 650 !important;
        box-shadow: none !important;
        white-space: nowrap !important;
    }

    #sellerViewProductModal .seller-view-chip--category {
        background: #fff4dc !important;
        color: #9b6811 !important;
    }

    #sellerViewProductModal .seller-view-chip--type {
        background: #f2f2f1 !important;
        color: #6f737a !important;
    }

    #sellerViewProductModal .seller-view-chip--shipping {
        background: #eaf7ef !important;
        color: #267b52 !important;
    }

    #sellerViewProductModal .seller-view-chip--shipping svg {
        width: 16px !important;
        height: 16px !important;
    }

    #sellerViewProductModal #viewProductName {
        margin: 28px 0 0 !important;
        color: #191d27 !important;
        font-size: 35px !important;
        line-height: 1.02 !important;
        font-weight: 760 !important;
        letter-spacing: -.045em !important;
    }

    #sellerViewProductModal #viewProductBrandLine {
        margin: 8px 0 0 !important;
        color: #888e98 !important;
        font-size: 12px !important;
        line-height: 1.4 !important;
        font-weight: 500 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-price {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 14px 22px !important;
        margin-top: 28px !important;
    }

    #sellerViewProductModal #viewProductSalePrice {
        color: #c98000 !important;
        font-size: 42px !important;
        line-height: .95 !important;
        font-weight: 800 !important;
        letter-spacing: -.045em !important;
    }

    #sellerViewProductModal #viewProductOriginalPrice {
        color: #8e939c !important;
        font-size: 14px !important;
        line-height: 1 !important;
        font-weight: 500 !important;
        text-decoration: line-through !important;
    }

    #sellerViewProductModal #viewProductDiscountBadge {
        display: inline-flex !important;
        min-height: 34px !important;
        align-items: center !important;
        justify-content: center !important;
        border: 0 !important;
        border-radius: 10px !important;
        background: #fff0cf !important;
        padding: 0 14px !important;
        color: #8a5b0c !important;
        font-size: 10px !important;
        line-height: 1 !important;
        font-weight: 750 !important;
    }

    #sellerViewProductModal #viewProductDiscountBadge.hidden,
    #sellerViewProductModal #viewProductOriginalPrice.hidden {
        display: none !important;
    }

    #sellerViewProductModal .seller-view-enterprise-meta {
        display: flex !important;
        align-items: center !important;
        gap: 22px !important;
        margin-top: 32px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-meta-item {
        display: flex !important;
        min-width: 0 !important;
        align-items: center !important;
        gap: 11px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-meta-icon {
        display: grid !important;
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        place-items: center !important;
        border-radius: 11px !important;
        background: #f5f6f7 !important;
        color: #8c929b !important;
    }

    #sellerViewProductModal .seller-view-enterprise-meta-icon--rating {
        background: #fff9ed !important;
        color: #d4a33b !important;
    }

    #sellerViewProductModal .seller-view-enterprise-meta-icon svg {
        width: 19px !important;
        height: 19px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-meta-item p {
        margin: 0 !important;
        color: #969ca5 !important;
        font-size: 9px !important;
        line-height: 1.1 !important;
        font-weight: 500 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-meta-item strong {
        display: block !important;
        margin-top: 4px !important;
        color: #20242b !important;
        font-size: 11px !important;
        line-height: 1.15 !important;
        font-weight: 700 !important;
        white-space: nowrap !important;
    }

    #sellerViewProductModal .seller-view-enterprise-meta-divider {
        width: 1px !important;
        height: 44px !important;
        background: #e6e5e2 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-facts {
        grid-column: 1 / -1 !important;
        display: grid !important;
        grid-template-columns: .8fr 1.25fr 1fr 1.15fr 1.25fr !important;
        overflow: hidden !important;
        border: 1px solid #e5e4e0 !important;
        border-radius: 14px !important;
        background: #fff !important;
        box-shadow: 0 3px 12px rgba(24, 26, 29, .025) !important;
    }

    #sellerViewProductModal .seller-view-enterprise-fact {
        display: flex !important;
        min-width: 0 !important;
        min-height: 82px !important;
        flex-direction: column !important;
        justify-content: center !important;
        padding: 14px 20px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-fact + .seller-view-enterprise-fact {
        border-left: 1px solid #e8e7e3 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-fact > span {
        color: #91979f !important;
        font-size: 9px !important;
        line-height: 1.1 !important;
        font-weight: 500 !important;
        white-space: nowrap !important;
    }

    #sellerViewProductModal .seller-view-enterprise-fact > strong {
        overflow: hidden !important;
        margin-top: 9px !important;
        color: #20242b !important;
        font-size: 12px !important;
        line-height: 1.2 !important;
        font-weight: 700 !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    #sellerViewProductModal .seller-view-enterprise-status-pill {
        display: inline-flex !important;
        width: max-content !important;
        max-width: 100% !important;
        min-height: 35px !important;
        align-items: center !important;
        gap: 8px !important;
        margin-top: 7px !important;
        border-radius: 11px !important;
        background: #fff0cf !important;
        padding: 0 13px !important;
        color: #925f09 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-status-pill svg {
        width: 17px !important;
        height: 17px !important;
        flex: 0 0 17px !important;
    }

    #sellerViewProductModal #viewProductStatus {
        overflow: hidden !important;
        margin: 0 !important;
        color: inherit !important;
        font-size: 10px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    #sellerViewProductModal .seller-view-enterprise-details {
        display: grid !important;
        grid-template-columns: .88fr 1.12fr !important;
        gap: 18px !important;
        border-top: 1px solid #eeeae5 !important;
        padding: 0 28px 24px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-panel {
        min-width: 0 !important;
        min-height: 216px !important;
        overflow: hidden !important;
        border: 1px solid #e4e3df !important;
        border-radius: 16px !important;
        background: #fff !important;
        padding: 20px !important;
        box-shadow: 0 3px 12px rgba(24, 26, 29, .018) !important;
    }

    #sellerViewProductModal .seller-view-enterprise-panel-head {
        display: flex !important;
        align-items: flex-start !important;
        gap: 12px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-panel-icon {
        display: grid !important;
        width: 34px !important;
        height: 34px !important;
        min-width: 34px !important;
        place-items: center !important;
        border-radius: 9px !important;
        background: #fafafa !important;
        color: #20242b !important;
    }

    #sellerViewProductModal .seller-view-enterprise-panel-icon svg {
        width: 18px !important;
        height: 18px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-panel-head h4 {
        margin: 1px 0 0 !important;
        color: #20242b !important;
        font-size: 14px !important;
        line-height: 1.2 !important;
        font-weight: 750 !important;
        letter-spacing: -.02em !important;
    }

    #sellerViewProductModal .seller-view-enterprise-panel-head p {
        margin: 5px 0 0 !important;
        color: #9298a1 !important;
        font-size: 9px !important;
        line-height: 1.35 !important;
        font-weight: 450 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-description-copy {
        max-height: 128px !important;
        margin-top: 16px !important;
        overflow-y: auto !important;
        border: 0 !important;
        border-radius: 12px !important;
        background: #f8f8f7 !important;
        padding: 14px 16px !important;
        color: #676d76 !important;
        font-size: 10px !important;
        line-height: 1.6 !important;
        font-weight: 450 !important;
        scrollbar-width: thin !important;
    }

    #sellerViewProductModal .seller-view-enterprise-list {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0 20px !important;
        margin: 15px 0 0 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-list > div {
        display: flex !important;
        min-width: 0 !important;
        min-height: 34px !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        border-top: 1px solid #ebeae7 !important;
        padding: 8px 0 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-list > div:nth-child(-n+2) {
        border-top: 0 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-list dt {
        color: #949aa2 !important;
        font-size: 9px !important;
        line-height: 1.2 !important;
        font-weight: 450 !important;
        white-space: nowrap !important;
    }

    #sellerViewProductModal .seller-view-enterprise-list dd {
        overflow: hidden !important;
        margin: 0 !important;
        color: #32363d !important;
        font-size: 9px !important;
        line-height: 1.2 !important;
        font-weight: 650 !important;
        text-align: right !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    #sellerViewProductModal .seller-view-enterprise-optional {
        margin: 0 28px 20px !important;
        overflow: hidden !important;
        border: 1px solid #e4e3df !important;
        border-radius: 16px !important;
        background: #fff !important;
        box-shadow: 0 3px 12px rgba(24, 26, 29, .018) !important;
    }

    #sellerViewProductModal .seller-view-enterprise-optional-head {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 16px !important;
        border-bottom: 1px solid #eceae6 !important;
        padding: 14px 18px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-optional-head h4 {
        margin: 0 !important;
        color: #20242b !important;
        font-size: 12px !important;
        font-weight: 750 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-optional-head p {
        margin: 4px 0 0 !important;
        color: #9399a1 !important;
        font-size: 8px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-optional-head > span {
        display: inline-flex !important;
        min-height: 27px !important;
        align-items: center !important;
        border-radius: 999px !important;
        background: #f5f4f2 !important;
        padding: 0 10px !important;
        color: #777d85 !important;
        font-size: 8px !important;
        font-weight: 650 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-spec-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 10px !important;
        padding: 14px 18px 18px !important;
    }

    #sellerViewProductModal [data-view-spec-card] {
        border: 1px solid #ebe9e5 !important;
        border-radius: 11px !important;
        background: #fafaf9 !important;
        padding: 11px 12px !important;
        box-shadow: none !important;
    }

    #sellerViewProductModal .seller-view-enterprise-table-wrap {
        max-height: 190px !important;
        overflow: auto !important;
    }

    #sellerViewProductModal .seller-view-enterprise-table-wrap table {
        width: 100% !important;
        min-width: 680px !important;
        border-collapse: collapse !important;
        text-align: left !important;
    }

    #sellerViewProductModal .seller-view-enterprise-table-wrap thead {
        background: #fafaf9 !important;
        color: #8b9199 !important;
        font-size: 8px !important;
        text-transform: uppercase !important;
        letter-spacing: .05em !important;
    }

    #sellerViewProductModal .seller-view-enterprise-table-wrap th {
        padding: 10px 14px !important;
        font-weight: 700 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-table-wrap td {
        padding-top: 10px !important;
        padding-bottom: 10px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-footer {
        display: flex !important;
        min-height: 76px !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 20px !important;
        border-top: 1px solid #ece9e4 !important;
        background: #ffffff !important;
        padding: 14px 28px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-footer > p {
        display: flex !important;
        min-width: 0 !important;
        align-items: center !important;
        gap: 9px !important;
        margin: 0 !important;
        color: #8d939c !important;
        font-size: 9px !important;
        line-height: 1.35 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-footer-icon {
        display: grid !important;
        width: 22px !important;
        height: 22px !important;
        min-width: 22px !important;
        place-items: center !important;
        color: #8e949c !important;
    }

    #sellerViewProductModal .seller-view-enterprise-footer-icon svg {
        width: 19px !important;
        height: 19px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-footer > div {
        display: flex !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        gap: 10px !important;
    }

    #sellerViewProductModal .seller-view-enterprise-button {
        display: inline-flex !important;
        min-width: 112px !important;
        height: 46px !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 9px !important;
        border-radius: 12px !important;
        padding: 0 17px !important;
        font-size: 10px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        white-space: nowrap !important;
        cursor: pointer !important;
        transition: background-color .14s ease, border-color .14s ease, color .14s ease, box-shadow .14s ease !important;
    }

    #sellerViewProductModal .seller-view-enterprise-button--secondary {
        border: 1px solid #dcdad5 !important;
        background: #ffffff !important;
        color: #20242b !important;
        box-shadow: 0 2px 8px rgba(20, 22, 25, .02) !important;
    }

    #sellerViewProductModal .seller-view-enterprise-button--secondary:hover {
        border-color: #d0cbc2 !important;
        background: #faf9f7 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-button--primary {
        border: 1px solid #cf8900 !important;
        background: #d88f00 !important;
        color: #ffffff !important;
        box-shadow: 0 8px 18px rgba(201, 130, 0, .18) !important;
    }

    #sellerViewProductModal .seller-view-enterprise-button--primary:hover {
        border-color: #bf7e00 !important;
        background: #c98500 !important;
        box-shadow: 0 9px 20px rgba(201, 130, 0, .20) !important;
    }

    #sellerViewProductModal .seller-view-enterprise-button svg {
        width: 17px !important;
        height: 17px !important;
    }

    @media (min-width: 900px) and (max-height: 820px) {
        #sellerViewProductModal {
            padding: 10px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-dialog {
            width: min(920px, calc(100vw - 20px)) !important;
            max-width: 920px !important;
            max-height: calc(100dvh - 20px) !important;
            border-radius: 20px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-header {
            min-height: 70px !important;
            padding: 15px 20px 13px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-header h3 {
            margin-top: 7px !important;
            font-size: 23px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-subtitle {
            margin-top: 5px !important;
            font-size: 9px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-close {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
            flex-basis: 40px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-hero {
            grid-template-columns: 224px minmax(0, 1fr) !important;
            gap: 18px 22px !important;
            padding: 16px 20px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-image-wrap {
            width: 224px !important;
            height: 224px !important;
            border-radius: 15px !important;
        }

        #sellerViewProductModal #viewProductStatusBadge {
            min-height: 29px !important;
            padding-inline: 11px !important;
            font-size: 8px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-summary {
            padding-top: 2px !important;
        }

        #sellerViewProductModal .seller-view-chip {
            min-height: 25px !important;
            border-radius: 9px !important;
            padding-inline: 10px !important;
            font-size: 7.4px !important;
        }

        #sellerViewProductModal .seller-view-chip--shipping svg {
            width: 13px !important;
            height: 13px !important;
        }

        #sellerViewProductModal #viewProductName {
            margin-top: 17px !important;
            font-size: 27px !important;
        }

        #sellerViewProductModal #viewProductBrandLine {
            margin-top: 5px !important;
            font-size: 9px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-price {
            gap: 10px 16px !important;
            margin-top: 18px !important;
        }

        #sellerViewProductModal #viewProductSalePrice {
            font-size: 32px !important;
        }

        #sellerViewProductModal #viewProductOriginalPrice {
            font-size: 10px !important;
        }

        #sellerViewProductModal #viewProductDiscountBadge {
            min-height: 27px !important;
            padding-inline: 10px !important;
            font-size: 7.5px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-meta {
            gap: 16px !important;
            margin-top: 20px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-meta-icon {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            border-radius: 9px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-meta-icon svg {
            width: 16px !important;
            height: 16px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-meta-item p {
            font-size: 7px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-meta-item strong {
            margin-top: 3px !important;
            font-size: 9px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-meta-divider {
            height: 34px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-fact {
            min-height: 62px !important;
            padding: 10px 14px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-fact > span {
            font-size: 7px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-fact > strong {
            margin-top: 6px !important;
            font-size: 9px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-status-pill {
            min-height: 27px !important;
            gap: 6px !important;
            margin-top: 5px !important;
            border-radius: 9px !important;
            padding-inline: 9px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-status-pill svg {
            width: 13px !important;
            height: 13px !important;
            flex-basis: 13px !important;
        }

        #sellerViewProductModal #viewProductStatus {
            font-size: 7.4px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-details {
            gap: 12px !important;
            padding: 0 20px 14px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-panel {
            min-height: 154px !important;
            border-radius: 14px !important;
            padding: 13px 14px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-panel-head {
            gap: 9px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-panel-icon {
            width: 28px !important;
            height: 28px !important;
            min-width: 28px !important;
            border-radius: 8px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-panel-icon svg {
            width: 14px !important;
            height: 14px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-panel-head h4 {
            font-size: 10.5px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-panel-head p {
            margin-top: 3px !important;
            font-size: 7px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-description-copy {
            max-height: 74px !important;
            margin-top: 10px !important;
            border-radius: 10px !important;
            padding: 10px 11px !important;
            font-size: 8px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-list {
            gap: 0 14px !important;
            margin-top: 9px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-list > div {
            min-height: 25px !important;
            gap: 8px !important;
            padding: 5px 0 !important;
        }

        #sellerViewProductModal .seller-view-enterprise-list dt,
        #sellerViewProductModal .seller-view-enterprise-list dd {
            font-size: 7px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-optional {
            margin: 0 20px 12px !important;
            border-radius: 14px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-footer {
            min-height: 58px !important;
            padding: 9px 20px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-footer > p {
            font-size: 7px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-button {
            min-width: 88px !important;
            height: 38px !important;
            border-radius: 10px !important;
            padding-inline: 13px !important;
            font-size: 8px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-button svg {
            width: 14px !important;
            height: 14px !important;
        }
    }

    @media (max-width: 899px) {
        #sellerViewProductModal {
            align-items: flex-start !important;
            padding: 8px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-dialog {
            width: 100% !important;
            max-width: 100% !important;
            max-height: calc(100dvh - 16px) !important;
            border-radius: 18px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-header {
            min-height: 74px !important;
            padding: 15px 16px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-header h3 {
            font-size: 22px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-subtitle {
            font-size: 9px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-hero {
            grid-template-columns: 1fr !important;
            gap: 18px !important;
            padding: 16px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-image-wrap {
            width: min(100%, 360px) !important;
            height: auto !important;
            aspect-ratio: 1 / 1 !important;
            margin: 0 auto !important;
        }

        #sellerViewProductModal .seller-view-enterprise-summary {
            padding-top: 0 !important;
        }

        #sellerViewProductModal #viewProductName {
            margin-top: 18px !important;
            font-size: 29px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-facts {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        #sellerViewProductModal .seller-view-enterprise-fact + .seller-view-enterprise-fact {
            border-left: 0 !important;
        }

        #sellerViewProductModal .seller-view-enterprise-fact:nth-child(even) {
            border-left: 1px solid #e8e7e3 !important;
        }

        #sellerViewProductModal .seller-view-enterprise-fact:nth-child(n+3) {
            border-top: 1px solid #e8e7e3 !important;
        }

        #sellerViewProductModal .seller-view-enterprise-fact--status {
            grid-column: 1 / -1 !important;
            border-left: 0 !important;
        }

        #sellerViewProductModal .seller-view-enterprise-details {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
            padding: 0 16px 16px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-panel {
            min-height: 0 !important;
        }

        #sellerViewProductModal .seller-view-enterprise-optional {
            margin: 0 16px 14px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-footer {
            align-items: flex-end !important;
            padding: 12px 16px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-footer > p {
            display: none !important;
        }
    }

    @media (max-width: 560px) {
        #sellerViewProductModal .seller-view-enterprise-header {
            padding: 13px 14px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-eyebrow {
            font-size: 8px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-header h3 {
            margin-top: 7px !important;
            font-size: 20px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-subtitle {
            display: none !important;
        }

        #sellerViewProductModal .seller-view-enterprise-close {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            flex-basis: 38px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-hero {
            padding: 13px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-price {
            gap: 10px !important;
        }

        #sellerViewProductModal #viewProductSalePrice {
            font-size: 31px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-meta {
            align-items: flex-start !important;
            gap: 12px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-meta-divider {
            display: none !important;
        }

        #sellerViewProductModal .seller-view-enterprise-fact {
            padding: 12px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-list {
            grid-template-columns: 1fr !important;
        }

        #sellerViewProductModal .seller-view-enterprise-list > div:nth-child(2) {
            border-top: 1px solid #ebeae7 !important;
        }

        #sellerViewProductModal .seller-view-enterprise-spec-grid {
            grid-template-columns: 1fr !important;
        }

        #sellerViewProductModal .seller-view-enterprise-footer {
            justify-content: stretch !important;
        }

        #sellerViewProductModal .seller-view-enterprise-footer > div {
            width: 100% !important;
        }

        #sellerViewProductModal .seller-view-enterprise-button {
            flex: 1 1 0 !important;
            min-width: 0 !important;
        }
    }



    /* Enterprise reference specificity lock: neutralize older modal overrides. */
    #sellerViewProductModal .seller-product-view-dialog > .seller-view-enterprise-header:first-child {
        min-height: 92px !important;
        padding: 24px 28px 20px !important;
        border-bottom: 1px solid #eeeae4 !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .seller-view-enterprise-header:first-child .seller-view-enterprise-eyebrow {
        margin: 0 !important;
        color: #b9780b !important;
        font-size: 10px !important;
        line-height: 1 !important;
        font-weight: 800 !important;
        letter-spacing: .18em !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .seller-view-enterprise-header:first-child h3 {
        margin: 10px 0 0 !important;
        color: #191d27 !important;
        font-size: 29px !important;
        line-height: 1.03 !important;
        font-weight: 760 !important;
        letter-spacing: -.045em !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .seller-view-enterprise-header:first-child .seller-view-enterprise-subtitle {
        margin: 9px 0 0 !important;
        color: #858b97 !important;
        font-size: 12px !important;
        line-height: 1.5 !important;
        font-weight: 450 !important;
        letter-spacing: 0 !important;
        text-transform: none !important;
    }

    #sellerViewProductModal .seller-view-enterprise-header > .seller-view-enterprise-close[data-close-view] {
        display: grid !important;
        width: 46px !important;
        height: 46px !important;
        min-width: 46px !important;
        flex: 0 0 46px !important;
        place-items: center !important;
        border-radius: 13px !important;
        padding: 0 !important;
    }

    #sellerViewProductModal #viewProductImageWrap.seller-view-enterprise-image-wrap {
        width: 300px !important;
        height: 300px !important;
        aspect-ratio: auto !important;
        border-radius: 18px !important;
    }

    #sellerViewProductModal #viewProductCategoryChip.seller-view-chip,
    #sellerViewProductModal #viewProductTypeChip.seller-view-chip,
    #sellerViewProductModal #viewProductShippingChip.seller-view-chip {
        display: inline-flex !important;
        min-height: 31px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 7px !important;
        border: 0 !important;
        border-radius: 11px !important;
        padding: 0 13px !important;
        font-size: 9px !important;
        line-height: 1 !important;
        font-weight: 650 !important;
        box-shadow: none !important;
    }

    #sellerViewProductModal #viewProductCreated,
    #sellerViewProductModal #viewProductRating {
        display: block !important;
        margin-top: 4px !important;
        color: #20242b !important;
        font-size: 11px !important;
        line-height: 1.15 !important;
        font-weight: 700 !important;
        white-space: nowrap !important;
    }

    #sellerViewProductModal #viewProductStatus {
        display: inline !important;
        min-height: 0 !important;
        margin: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        color: inherit !important;
        font-size: 10px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        box-shadow: none !important;
    }

    #sellerViewProductModal #viewProductDescription.seller-view-enterprise-description-copy {
        display: block !important;
        max-height: 128px !important;
        margin-top: 16px !important;
        overflow-y: auto !important;
        border: 0 !important;
        border-radius: 12px !important;
        background: #f8f8f7 !important;
        padding: 14px 16px !important;
        color: #676d76 !important;
        font-size: 10px !important;
        line-height: 1.6 !important;
    }

    #sellerViewProductModal #viewProductSpecificationsSection.seller-view-enterprise-optional,
    #sellerViewProductModal #viewProductVariantsSection.seller-view-enterprise-optional {
        margin: 0 28px 20px !important;
        overflow: hidden !important;
        border: 1px solid #e4e3df !important;
        border-radius: 16px !important;
        background: #ffffff !important;
        box-shadow: 0 3px 12px rgba(24, 26, 29, .018) !important;
    }

    #sellerViewProductModal #viewProductSpecifications.seller-view-enterprise-spec-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 10px !important;
        padding: 14px 18px 18px !important;
    }

    #sellerViewProductModal #viewProductVariantsSection .seller-view-enterprise-table-wrap th,
    #sellerViewProductModal #viewProductVariantsSection .seller-view-enterprise-table-wrap td {
        padding-top: 10px !important;
        padding-bottom: 10px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .seller-view-enterprise-footer:last-child {
        display: flex !important;
        min-height: 76px !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 20px !important;
        border-top: 1px solid #ece9e4 !important;
        background: #ffffff !important;
        padding: 14px 28px !important;
    }

    #sellerViewProductModal .seller-product-view-dialog > .seller-view-enterprise-footer:last-child .seller-view-enterprise-button,
    #sellerViewProductModal .seller-product-view-dialog > .seller-view-enterprise-footer:last-child [data-close-view] {
        display: inline-flex !important;
        width: auto !important;
        min-width: 112px !important;
        height: 46px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 9px !important;
        border-radius: 12px !important;
        padding: 0 17px !important;
        font-size: 10px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        white-space: nowrap !important;
    }

    #sellerViewProductModal #viewProductEditButton.seller-view-enterprise-button--primary {
        min-width: 150px !important;
        border: 1px solid #cf8900 !important;
        background: #d88f00 !important;
        color: #ffffff !important;
        box-shadow: 0 8px 18px rgba(201, 130, 0, .18) !important;
    }

    @media (min-width: 900px) and (max-height: 820px) {
        #sellerViewProductModal .seller-product-view-dialog > .seller-view-enterprise-header:first-child {
            min-height: 70px !important;
            padding: 15px 20px 13px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .seller-view-enterprise-header:first-child .seller-view-enterprise-eyebrow {
            font-size: 8px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .seller-view-enterprise-header:first-child h3 {
            margin-top: 7px !important;
            font-size: 23px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .seller-view-enterprise-header:first-child .seller-view-enterprise-subtitle {
            margin-top: 5px !important;
            font-size: 9px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-header > .seller-view-enterprise-close[data-close-view] {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
            flex-basis: 40px !important;
        }

        #sellerViewProductModal #viewProductImageWrap.seller-view-enterprise-image-wrap {
            width: 224px !important;
            height: 224px !important;
            border-radius: 15px !important;
        }

        #sellerViewProductModal #viewProductCategoryChip.seller-view-chip,
        #sellerViewProductModal #viewProductTypeChip.seller-view-chip,
        #sellerViewProductModal #viewProductShippingChip.seller-view-chip {
            min-height: 25px !important;
            border-radius: 9px !important;
            padding-inline: 10px !important;
            font-size: 7.4px !important;
        }

        #sellerViewProductModal #viewProductCreated,
        #sellerViewProductModal #viewProductRating {
            font-size: 9px !important;
        }

        #sellerViewProductModal #viewProductStatus {
            font-size: 7.4px !important;
        }

        #sellerViewProductModal #viewProductDescription.seller-view-enterprise-description-copy {
            max-height: 74px !important;
            margin-top: 10px !important;
            border-radius: 10px !important;
            padding: 10px 11px !important;
            font-size: 8px !important;
        }

        #sellerViewProductModal #viewProductSpecificationsSection.seller-view-enterprise-optional,
        #sellerViewProductModal #viewProductVariantsSection.seller-view-enterprise-optional {
            margin: 0 20px 12px !important;
            border-radius: 14px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .seller-view-enterprise-footer:last-child {
            min-height: 58px !important;
            padding: 9px 20px !important;
        }

        #sellerViewProductModal .seller-product-view-dialog > .seller-view-enterprise-footer:last-child .seller-view-enterprise-button,
        #sellerViewProductModal .seller-product-view-dialog > .seller-view-enterprise-footer:last-child [data-close-view] {
            min-width: 88px !important;
            height: 38px !important;
            border-radius: 10px !important;
            padding-inline: 13px !important;
            font-size: 8px !important;
        }

        #sellerViewProductModal #viewProductEditButton.seller-view-enterprise-button--primary {
            min-width: 122px !important;
        }
    }

    @media (max-width: 899px) {
        #sellerViewProductModal #viewProductImageWrap.seller-view-enterprise-image-wrap {
            width: min(100%, 360px) !important;
            height: auto !important;
            aspect-ratio: 1 / 1 !important;
        }

        #sellerViewProductModal #viewProductSpecificationsSection.seller-view-enterprise-optional,
        #sellerViewProductModal #viewProductVariantsSection.seller-view-enterprise-optional {
            margin: 0 16px 14px !important;
        }
    }


    /* ============================================================
       PRODUCT INSPECTOR — FINAL VISUAL POLISH
       Fixes close icon visibility, Pending badge clipping, complete
       description surface, and breathing room below the details divider.
       ============================================================ */

    /* Header close icon: explicit SVG stroke so the × is always visible. */
    #sellerViewProductModal .seller-view-enterprise-header > .seller-view-enterprise-close[data-close-view] {
        color: #3f454e !important;
        overflow: visible !important;
    }

    #sellerViewProductModal .seller-view-enterprise-header > .seller-view-enterprise-close[data-close-view] svg {
        display: block !important;
        width: 19px !important;
        height: 19px !important;
        overflow: visible !important;
        fill: none !important;
        stroke: currentColor !important;
        stroke-width: 1.9 !important;
        stroke-linecap: round !important;
        stroke-linejoin: round !important;
        opacity: 1 !important;
    }

    #sellerViewProductModal .seller-view-enterprise-header > .seller-view-enterprise-close[data-close-view] svg path {
        fill: none !important;
        stroke: currentColor !important;
    }

    /* Image status: never crop Pending/Approved/etc. */
    #sellerViewProductModal #viewProductStatusBadge.seller-view-enterprise-image-status {
        display: inline-flex !important;
        width: max-content !important;
        min-width: 96px !important;
        max-width: calc(100% - 28px) !important;
        min-height: 34px !important;
        box-sizing: border-box !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        overflow: visible !important;
        padding: 0 15px !important;
        line-height: 1.15 !important;
        white-space: nowrap !important;
        text-overflow: clip !important;
    }

    #sellerViewProductModal #viewProductStatusBadge.seller-view-enterprise-image-status::before {
        display: inline-flex !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
    }

    /* Give the lower information cards breathing room after the divider. */
    #sellerViewProductModal .seller-view-enterprise-details {
        padding: 18px 28px 24px !important;
    }

    /* Make the Description surface feel intentional even for one-line copy. */
    #sellerViewProductModal .seller-view-enterprise-panel--description {
        display: flex !important;
        flex-direction: column !important;
    }

    #sellerViewProductModal #viewProductDescription.seller-view-enterprise-description-copy {
        display: block !important;
        width: 100% !important;
        min-height: 118px !important;
        max-height: 138px !important;
        flex: 1 1 auto !important;
        box-sizing: border-box !important;
        overflow-y: auto !important;
        border-radius: 12px !important;
        background: #f7f7f5 !important;
        padding: 15px 16px !important;
    }

    @media (min-width: 900px) and (max-height: 820px) {
        #sellerViewProductModal #viewProductStatusBadge.seller-view-enterprise-image-status {
            min-width: 82px !important;
            min-height: 28px !important;
            padding-inline: 12px !important;
            font-size: 8px !important;
        }

        #sellerViewProductModal .seller-view-enterprise-details {
            padding: 14px 20px 14px !important;
        }

        #sellerViewProductModal #viewProductDescription.seller-view-enterprise-description-copy {
            min-height: 78px !important;
            max-height: 90px !important;
            margin-top: 10px !important;
            padding: 10px 11px !important;
        }
    }

    @media (max-width: 899px) {
        #sellerViewProductModal .seller-view-enterprise-details {
            padding: 14px 16px 16px !important;
        }

        #sellerViewProductModal #viewProductDescription.seller-view-enterprise-description-copy {
            min-height: 96px !important;
            max-height: 150px !important;
        }
    }



    /* ============================================================
       EDIT PRODUCT — ENTERPRISE GOLD REFERENCE DESIGN
       Layout-only. Existing form IDs / submit behavior are preserved.
       ============================================================ */
    #sellerEditProductModal {
        align-items: center !important;
        justify-content: center !important;
        overflow: hidden !important;
        padding: 18px !important;
        background: rgba(24, 25, 27, .44) !important;
        backdrop-filter: blur(7px) !important;
        -webkit-backdrop-filter: blur(7px) !important;
    }

    #sellerEditProductModal .seller-edit-enterprise-dialog {
        display: flex !important;
        width: min(1040px, calc(100vw - 36px)) !important;
        max-width: 1040px !important;
        max-height: calc(100dvh - 36px) !important;
        flex-direction: column !important;
        overflow: hidden !important;
        border: 1px solid #e5dfd7 !important;
        border-radius: 22px !important;
        background: #fff !important;
        box-shadow: 0 34px 90px rgba(28,24,19,.20), 0 8px 24px rgba(28,24,19,.07) !important;
        font-family: "Poppins", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
    }

    #sellerEditProductModal .seller-edit-enterprise-header {
        display: flex !important;
        flex: 0 0 auto !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 24px !important;
        border-bottom: 1px solid #eee9e2 !important;
        background: #fff !important;
        padding: 22px 30px 19px !important;
    }

    #sellerEditProductModal .seller-edit-header-copy {
        min-width: 0 !important;
    }

    #sellerEditProductModal .seller-edit-eyebrow {
        margin: 0 !important;
        color: #b87809 !important;
        font-size: 9px !important;
        line-height: 1 !important;
        font-weight: 800 !important;
        letter-spacing: .17em !important;
        text-transform: uppercase !important;
    }

    #sellerEditProductModal .seller-edit-enterprise-header h3 {
        margin: 7px 0 0 !important;
        color: #202126 !important;
        font-size: 25px !important;
        line-height: 1.08 !important;
        font-weight: 750 !important;
        letter-spacing: -.045em !important;
    }

    #sellerEditProductModal .seller-edit-subtitle {
        max-width: 800px !important;
        margin: 6px 0 0 !important;
        color: #858b95 !important;
        font-size: 9px !important;
        line-height: 1.55 !important;
        white-space: normal !important;
    }

    #sellerEditProductModal .seller-edit-header-close {
        display: grid !important;
        width: 44px !important;
        min-width: 44px !important;
        height: 44px !important;
        place-items: center !important;
        border: 1px solid #e1dbd3 !important;
        border-radius: 13px !important;
        background: #fff !important;
        padding: 0 !important;
        color: #58524d !important;
        box-shadow: none !important;
        cursor: pointer !important;
        transition: border-color .14s ease, background-color .14s ease, color .14s ease !important;
    }

    #sellerEditProductModal .seller-edit-header-close:hover {
        border-color: #d5c8b5 !important;
        background: #fffaf1 !important;
        color: #96620b !important;
    }

    #sellerEditProductModal .seller-edit-header-close svg {
        width: 18px !important;
        height: 18px !important;
    }

    #sellerEditProductForm.seller-edit-enterprise-form {
        display: flex !important;
        min-height: 0 !important;
        flex: 1 1 auto !important;
        flex-direction: column !important;
        overflow-y: auto !important;
        padding: 22px 30px 0 !important;
        background: #fff !important;
        scrollbar-width: thin !important;
        scrollbar-color: #d9d1c8 transparent !important;
    }

    #sellerEditProductForm .seller-edit-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 14px 22px !important;
        width: 100% !important;
        min-width: 0 !important;
    }

    #sellerEditProductForm .seller-edit-field {
        min-width: 0 !important;
    }

    #sellerEditProductForm .seller-edit-field--full {
        grid-column: 1 / -1 !important;
    }

    #sellerEditProductForm .seller-edit-field > label:not(.seller-edit-shipping-card) {
        display: block !important;
        margin: 0 0 7px !important;
        color: #36383e !important;
        font-size: 9px !important;
        line-height: 1.2 !important;
        font-weight: 650 !important;
    }

    #sellerEditProductForm .seller-edit-field > label > span {
        color: #bd3d3d !important;
    }

    #sellerEditProductForm .seller-edit-field input:not([type="hidden"]):not([type="checkbox"]):not([type="file"]),
    #sellerEditProductForm .seller-edit-field select,
    #sellerEditProductForm .seller-edit-field textarea {
        width: 100% !important;
        min-width: 0 !important;
        border: 1px solid #ddd8d1 !important;
        background: #fff !important;
        color: #2e3035 !important;
        outline: none !important;
        box-shadow: none !important;
        font-family: inherit !important;
        transition: border-color .14s ease, box-shadow .14s ease, background-color .14s ease !important;
    }

    #sellerEditProductForm .seller-edit-field input:not([type="hidden"]):not([type="checkbox"]):not([type="file"]),
    #sellerEditProductForm .seller-edit-field select {
        height: 45px !important;
        border-radius: 10px !important;
        padding: 0 14px !important;
        font-size: 9px !important;
        line-height: 45px !important;
        font-weight: 500 !important;
    }

    #sellerEditProductForm .seller-edit-field select {
        appearance: none !important;
        padding-right: 40px !important;
    }

    #sellerEditProductForm .seller-edit-select-wrap {
        position: relative !important;
    }

    #sellerEditProductForm .seller-edit-select-wrap > svg {
        position: absolute !important;
        top: 50% !important;
        right: 14px !important;
        width: 15px !important;
        height: 15px !important;
        transform: translateY(-50%) !important;
        color: #ad7208 !important;
        pointer-events: none !important;
    }

    #sellerEditProductForm .seller-edit-field input::placeholder,
    #sellerEditProductForm .seller-edit-field textarea::placeholder {
        color: #a2a5aa !important;
        opacity: 1 !important;
    }

    #sellerEditProductForm .seller-edit-field input:not([type="hidden"]):not([type="checkbox"]):not([type="file"]):focus,
    #sellerEditProductForm .seller-edit-field select:focus,
    #sellerEditProductForm .seller-edit-field textarea:focus {
        border-color: #cf961b !important;
        box-shadow: 0 0 0 3px rgba(207,150,27,.09) !important;
        background: #fff !important;
    }

    #sellerEditProductForm .seller-edit-flash-input {
        border-color: #e2dad1 !important;
        background: #fff !important;
        color: #34363b !important;
    }

    #sellerEditProductForm .seller-edit-helper {
        margin: 5px 0 0 !important;
        color: #9a9691 !important;
        font-size: 7px !important;
        line-height: 1.35 !important;
    }

    #sellerEditProductForm .seller-edit-helper--multiline {
        max-width: 94% !important;
        line-height: 1.5 !important;
    }

    /* Gold active switch — requested brand treatment. */
    #sellerEditProductForm .seller-edit-shipping-card {
        display: flex !important;
        min-height: 62px !important;
        align-items: center !important;
        gap: 15px !important;
        border: 1px solid #eadab9 !important;
        border-radius: 13px !important;
        background: linear-gradient(180deg, #fffdf9 0%, #fffaf1 100%) !important;
        padding: 12px 14px !important;
        cursor: pointer !important;
        box-shadow: none !important;
    }

    #sellerEditProductForm .seller-edit-shipping-control {
        position: relative !important;
        display: inline-flex !important;
        flex: 0 0 auto !important;
    }

    #sellerEditProductForm .seller-edit-gold-switch {
        position: relative !important;
        display: inline-block !important;
        width: 52px !important;
        height: 28px !important;
        border: 1px solid #ddd7cf !important;
        border-radius: 999px !important;
        background: #e9e5df !important;
        box-shadow: inset 0 1px 2px rgba(45,38,30,.08) !important;
        transition: background-color .16s ease, border-color .16s ease, box-shadow .16s ease !important;
    }

    #sellerEditProductForm .seller-edit-gold-switch::after {
        content: "" !important;
        position: absolute !important;
        top: 3px !important;
        left: 3px !important;
        width: 20px !important;
        height: 20px !important;
        border-radius: 999px !important;
        background: #fff !important;
        box-shadow: 0 2px 6px rgba(45,38,30,.20) !important;
        transition: transform .16s ease !important;
    }

    #sellerEditProductForm #editProductFreeShipping:checked + .seller-edit-gold-switch {
        border-color: #cf8f0c !important;
        background: linear-gradient(180deg, #dfa114 0%, #ca8705 100%) !important;
        box-shadow: 0 5px 12px rgba(202,135,5,.17), inset 0 1px 0 rgba(255,255,255,.18) !important;
    }

    #sellerEditProductForm #editProductFreeShipping:checked + .seller-edit-gold-switch::after {
        transform: translateX(24px) !important;
    }

    #sellerEditProductForm #editProductFreeShipping:focus-visible + .seller-edit-gold-switch {
        outline: 3px solid rgba(207,143,12,.16) !important;
        outline-offset: 2px !important;
    }

    #sellerEditProductForm .seller-edit-shipping-copy {
        min-width: 0 !important;
    }

    #sellerEditProductForm .seller-edit-shipping-copy strong {
        display: block !important;
        color: #34312e !important;
        font-size: 10px !important;
        line-height: 1.2 !important;
        font-weight: 700 !important;
    }

    #sellerEditProductForm .seller-edit-shipping-copy small {
        display: block !important;
        margin-top: 4px !important;
        color: #8f8981 !important;
        font-size: 7.4px !important;
        line-height: 1.4 !important;
    }

    #sellerEditProductForm .seller-edit-media-field,
    #sellerEditProductForm .seller-edit-description-field {
        align-self: stretch !important;
    }

    #sellerEditProductForm .seller-edit-media-row {
        display: grid !important;
        grid-template-columns: 94px minmax(0, 1fr) !important;
        gap: 12px !important;
        align-items: stretch !important;
        min-height: 88px !important;
    }

    #sellerEditProductForm .seller-edit-current-image {
        width: 94px !important;
        height: 88px !important;
        overflow: hidden !important;
        border: 1px solid #e4ddd4 !important;
        border-radius: 11px !important;
        background: #f3f0ec !important;
    }

    #sellerEditProductForm .seller-edit-current-image:not(.hidden) {
        display: block !important;
    }

    #sellerEditProductForm .seller-edit-current-image img {
        display: block !important;
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }

    #sellerEditProductForm .seller-edit-upload-box {
        display: flex !important;
        min-width: 0 !important;
        min-height: 88px !important;
        align-items: center !important;
        gap: 11px !important;
        border: 1px dashed #dcd4ca !important;
        border-radius: 12px !important;
        background: #fcfbf9 !important;
        padding: 11px 13px !important;
    }

    #sellerEditProductForm .seller-edit-upload-icon {
        display: grid !important;
        width: 38px !important;
        height: 38px !important;
        flex: 0 0 38px !important;
        place-items: center !important;
        border: 1px solid #eddbb8 !important;
        border-radius: 10px !important;
        background: #fff8e9 !important;
        color: #ad7107 !important;
    }

    #sellerEditProductForm .seller-edit-upload-icon svg {
        width: 19px !important;
        height: 19px !important;
    }

    #sellerEditProductForm .seller-edit-upload-copy {
        min-width: 0 !important;
        flex: 1 1 auto !important;
    }

    #sellerEditProductForm .seller-edit-upload-copy input[type="file"] {
        display: block !important;
        width: 100% !important;
        height: auto !important;
        border: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        color: #59534d !important;
        font-size: 7.5px !important;
        line-height: 1.4 !important;
        box-shadow: none !important;
    }

    #sellerEditProductForm .seller-edit-upload-copy input[type="file"]::file-selector-button {
        margin-right: 8px !important;
        border: 0 !important;
        border-radius: 7px !important;
        background: #fff !important;
        padding: 6px 8px !important;
        color: #34312e !important;
        font: inherit !important;
        font-weight: 650 !important;
        cursor: pointer !important;
    }

    #sellerEditProductForm .seller-edit-upload-copy small {
        display: block !important;
        margin-top: 5px !important;
        color: #9a948d !important;
        font-size: 6.7px !important;
        line-height: 1.35 !important;
    }

    #sellerEditProductForm .seller-edit-description-field textarea {
        height: 88px !important;
        min-height: 88px !important;
        resize: none !important;
        border-radius: 10px !important;
        padding: 11px 13px !important;
        font-size: 8.5px !important;
        line-height: 1.55 !important;
    }

    #sellerEditProductForm .seller-edit-sensitive-note {
        display: flex !important;
        align-items: flex-start !important;
        gap: 10px !important;
        margin: 16px 0 0 !important;
        border: 1px solid #ecd9b5 !important;
        border-radius: 12px !important;
        background: linear-gradient(180deg, #fffaf0 0%, #fff7e9 100%) !important;
        padding: 10px 12px !important;
        color: #837465 !important;
        box-shadow: none !important;
    }

    #sellerEditProductForm .seller-edit-sensitive-icon {
        display: grid !important;
        width: 22px !important;
        height: 22px !important;
        flex: 0 0 22px !important;
        place-items: center !important;
        border-radius: 999px !important;
        background: #d69208 !important;
        color: #fff !important;
        font-size: 11px !important;
        line-height: 1 !important;
        font-weight: 800 !important;
    }

    #sellerEditProductForm .seller-edit-sensitive-note p {
        margin: 1px 0 0 !important;
        font-size: 7.1px !important;
        line-height: 1.5 !important;
    }

    #sellerEditProductForm .seller-edit-sensitive-note strong {
        color: #a66b07 !important;
        font-weight: 750 !important;
    }

    #sellerEditProductForm .seller-edit-footer {
        position: sticky !important;
        z-index: 4 !important;
        bottom: 0 !important;
        display: flex !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 10px !important;
        margin: 16px -30px 0 !important;
        border-top: 1px solid #eee9e2 !important;
        background: rgba(255,255,255,.98) !important;
        padding: 12px 30px 13px !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
    }

    #sellerEditProductForm .seller-edit-button {
        display: inline-flex !important;
        min-width: 112px !important;
        height: 42px !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 10px !important;
        padding: 0 18px !important;
        font-size: 8.5px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        white-space: nowrap !important;
        cursor: pointer !important;
        transition: border-color .14s ease, background-color .14s ease, box-shadow .14s ease !important;
    }

    #sellerEditProductForm .seller-edit-button--secondary {
        border: 1px solid #ddd7d0 !important;
        background: #fff !important;
        color: #57514b !important;
        box-shadow: none !important;
    }

    #sellerEditProductForm .seller-edit-button--secondary:hover {
        border-color: #d2c8bd !important;
        background: #fcfaf7 !important;
    }

    #sellerEditProductForm .seller-edit-button--primary {
        border: 1px solid #c98505 !important;
        background: linear-gradient(180deg, #dda00f 0%, #cd8906 100%) !important;
        color: #fff !important;
        box-shadow: 0 8px 17px rgba(197,128,4,.17), inset 0 1px 0 rgba(255,255,255,.18) !important;
    }

    #sellerEditProductForm .seller-edit-button--primary:hover {
        background: linear-gradient(180deg, #d5960a 0%, #bf7d04 100%) !important;
        box-shadow: 0 9px 18px rgba(197,128,4,.20), inset 0 1px 0 rgba(255,255,255,.18) !important;
    }

    @media (min-width: 900px) and (max-height: 820px) {
        #sellerEditProductModal {
            padding: 10px !important;
        }

        #sellerEditProductModal .seller-edit-enterprise-dialog {
            width: min(1000px, calc(100vw - 20px)) !important;
            max-width: 1000px !important;
            max-height: calc(100dvh - 20px) !important;
        }

        #sellerEditProductModal .seller-edit-enterprise-header {
            padding: 15px 22px 13px !important;
        }

        #sellerEditProductModal .seller-edit-enterprise-header h3 {
            margin-top: 5px !important;
            font-size: 20px !important;
        }

        #sellerEditProductModal .seller-edit-subtitle {
            margin-top: 4px !important;
            font-size: 7.4px !important;
        }

        #sellerEditProductModal .seller-edit-header-close {
            width: 38px !important;
            min-width: 38px !important;
            height: 38px !important;
        }

        #sellerEditProductForm.seller-edit-enterprise-form {
            padding: 14px 22px 0 !important;
        }

        #sellerEditProductForm .seller-edit-grid {
            gap: 9px 16px !important;
        }

        #sellerEditProductForm .seller-edit-field > label:not(.seller-edit-shipping-card) {
            margin-bottom: 5px !important;
            font-size: 8px !important;
        }

        #sellerEditProductForm .seller-edit-field input:not([type="hidden"]):not([type="checkbox"]):not([type="file"]),
        #sellerEditProductForm .seller-edit-field select {
            height: 38px !important;
            line-height: 38px !important;
            font-size: 8px !important;
        }

        #sellerEditProductForm .seller-edit-helper {
            margin-top: 3px !important;
            font-size: 6.2px !important;
        }

        #sellerEditProductForm .seller-edit-shipping-card {
            min-height: 52px !important;
            padding: 9px 12px !important;
        }

        #sellerEditProductForm .seller-edit-gold-switch {
            width: 46px !important;
            height: 25px !important;
        }

        #sellerEditProductForm .seller-edit-gold-switch::after {
            width: 17px !important;
            height: 17px !important;
        }

        #sellerEditProductForm #editProductFreeShipping:checked + .seller-edit-gold-switch::after {
            transform: translateX(21px) !important;
        }

        #sellerEditProductForm .seller-edit-media-row,
        #sellerEditProductForm .seller-edit-upload-box {
            min-height: 72px !important;
        }

        #sellerEditProductForm .seller-edit-current-image {
            height: 72px !important;
        }

        #sellerEditProductForm .seller-edit-description-field textarea {
            height: 72px !important;
            min-height: 72px !important;
        }

        #sellerEditProductForm .seller-edit-sensitive-note {
            margin-top: 10px !important;
            padding: 8px 10px !important;
        }

        #sellerEditProductForm .seller-edit-footer {
            margin: 10px -22px 0 !important;
            padding: 9px 22px 10px !important;
        }

        #sellerEditProductForm .seller-edit-button {
            height: 38px !important;
        }
    }

    @media (max-width: 759px) {
        #sellerEditProductModal {
            align-items: flex-start !important;
            padding: 8px !important;
        }

        #sellerEditProductModal .seller-edit-enterprise-dialog {
            width: 100% !important;
            max-width: 100% !important;
            max-height: calc(100dvh - 16px) !important;
            border-radius: 18px !important;
        }

        #sellerEditProductModal .seller-edit-enterprise-header {
            padding: 16px !important;
        }

        #sellerEditProductModal .seller-edit-enterprise-header h3 {
            font-size: 21px !important;
        }

        #sellerEditProductForm.seller-edit-enterprise-form {
            padding: 16px 16px 0 !important;
        }

        #sellerEditProductForm .seller-edit-grid {
            grid-template-columns: 1fr !important;
            gap: 13px !important;
        }

        #sellerEditProductForm .seller-edit-field--full {
            grid-column: auto !important;
        }

        #sellerEditProductForm .seller-edit-media-row {
            grid-template-columns: 82px minmax(0, 1fr) !important;
        }

        #sellerEditProductForm .seller-edit-current-image {
            width: 82px !important;
        }

        #sellerEditProductForm .seller-edit-footer {
            margin-right: -16px !important;
            margin-left: -16px !important;
            padding-right: 16px !important;
            padding-left: 16px !important;
        }
    }


    /* ============================================================
       EDIT PRODUCT — FINAL COMPACT A4 / SOLID GOLD PASS
       Final scoped override only. No backend or JS behavior changes.
       ============================================================ */
    #sellerEditProductModal {
        padding: 14px !important;
        background: rgba(27, 25, 22, .42) !important;
        backdrop-filter: blur(5px) !important;
        -webkit-backdrop-filter: blur(5px) !important;
    }

    #sellerEditProductModal .seller-edit-enterprise-dialog {
        width: min(800px, calc(100vw - 28px)) !important;
        max-width: 800px !important;
        max-height: min(740px, calc(100dvh - 28px)) !important;
        border-radius: 20px !important;
        border-color: #e7dfd5 !important;
        background: #ffffff !important;
        box-shadow: 0 24px 64px rgba(31, 27, 22, .17), 0 5px 16px rgba(31, 27, 22, .05) !important;
    }

    #sellerEditProductModal .seller-edit-enterprise-header {
        gap: 16px !important;
        padding: 15px 20px 13px !important;
        background: #ffffff !important;
    }

    #sellerEditProductModal .seller-edit-eyebrow {
        font-size: 7.5px !important;
        letter-spacing: .16em !important;
    }

    #sellerEditProductModal .seller-edit-enterprise-header h3 {
        margin-top: 5px !important;
        font-size: 20px !important;
        line-height: 1.05 !important;
    }

    #sellerEditProductModal .seller-edit-subtitle {
        max-width: 610px !important;
        margin-top: 4px !important;
        font-size: 7px !important;
        line-height: 1.45 !important;
    }

    #sellerEditProductModal .seller-edit-header-close {
        width: 38px !important;
        min-width: 38px !important;
        height: 38px !important;
        border-radius: 11px !important;
        background: #ffffff !important;
    }

    #sellerEditProductForm.seller-edit-enterprise-form {
        padding: 14px 20px 0 !important;
        background: #ffffff !important;
    }

    #sellerEditProductForm .seller-edit-grid {
        gap: 9px 12px !important;
    }

    #sellerEditProductForm .seller-edit-field > label:not(.seller-edit-shipping-card) {
        margin-bottom: 5px !important;
        font-size: 7.8px !important;
    }

    #sellerEditProductForm .seller-edit-field input:not([type="hidden"]):not([type="checkbox"]):not([type="file"]),
    #sellerEditProductForm .seller-edit-field select {
        height: 38px !important;
        border-radius: 9px !important;
        padding-right: 12px !important;
        padding-left: 12px !important;
        font-size: 8px !important;
        line-height: 38px !important;
        background: #ffffff !important;
    }

    #sellerEditProductForm .seller-edit-field select {
        padding-right: 34px !important;
    }

    #sellerEditProductForm .seller-edit-select-wrap > svg {
        right: 11px !important;
        width: 13px !important;
        height: 13px !important;
    }

    #sellerEditProductForm .seller-edit-helper {
        margin-top: 3px !important;
        font-size: 6.2px !important;
        line-height: 1.35 !important;
    }

    #sellerEditProductForm .seller-edit-helper--multiline {
        max-width: 100% !important;
    }

    /* Solid warm-gold shipping control: no gradients. */
    #sellerEditProductForm .seller-edit-shipping-card {
        min-height: 50px !important;
        gap: 11px !important;
        border-color: #ead9b6 !important;
        border-radius: 11px !important;
        background: #fffaf0 !important;
        padding: 8px 11px !important;
    }

    #sellerEditProductForm .seller-edit-gold-switch {
        width: 46px !important;
        height: 25px !important;
        border-color: #dcd5cc !important;
        background: #e8e4de !important;
        box-shadow: inset 0 1px 2px rgba(45,38,30,.07) !important;
    }

    #sellerEditProductForm .seller-edit-gold-switch::after {
        top: 3px !important;
        left: 3px !important;
        width: 17px !important;
        height: 17px !important;
    }

    #sellerEditProductForm #editProductFreeShipping:checked + .seller-edit-gold-switch {
        border-color: #d89208 !important;
        background: #d89208 !important;
        box-shadow: 0 4px 10px rgba(216,146,8,.16) !important;
    }

    #sellerEditProductForm #editProductFreeShipping:checked + .seller-edit-gold-switch::after {
        transform: translateX(21px) !important;
    }

    #sellerEditProductForm .seller-edit-shipping-copy strong {
        font-size: 8.8px !important;
    }

    #sellerEditProductForm .seller-edit-shipping-copy small {
        margin-top: 2px !important;
        font-size: 6.3px !important;
    }

    #sellerEditProductForm .seller-edit-media-row {
        grid-template-columns: 70px minmax(0, 1fr) !important;
        gap: 9px !important;
        min-height: 68px !important;
    }

    #sellerEditProductForm .seller-edit-current-image {
        width: 70px !important;
        height: 68px !important;
        border-radius: 9px !important;
    }

    #sellerEditProductForm .seller-edit-upload-box {
        min-height: 68px !important;
        gap: 9px !important;
        border-radius: 10px !important;
        background: #fdfcf9 !important;
        padding: 8px 10px !important;
    }

    #sellerEditProductForm .seller-edit-upload-icon {
        width: 32px !important;
        height: 32px !important;
        flex-basis: 32px !important;
        border-radius: 9px !important;
        background: #fff7e8 !important;
        color: #b87908 !important;
    }

    #sellerEditProductForm .seller-edit-upload-icon svg {
        width: 16px !important;
        height: 16px !important;
    }

    #sellerEditProductForm .seller-edit-upload-copy input[type="file"] {
        font-size: 6.8px !important;
    }

    #sellerEditProductForm .seller-edit-upload-copy input[type="file"]::file-selector-button {
        padding: 5px 7px !important;
        background: #ffffff !important;
    }

    #sellerEditProductForm .seller-edit-upload-copy small {
        margin-top: 3px !important;
        font-size: 5.9px !important;
    }

    #sellerEditProductForm .seller-edit-description-field textarea {
        height: 68px !important;
        min-height: 68px !important;
        border-radius: 9px !important;
        padding: 9px 11px !important;
        font-size: 7.8px !important;
        background: #ffffff !important;
    }

    /* Solid note surface: no gradient. */
    #sellerEditProductForm .seller-edit-sensitive-note {
        gap: 8px !important;
        margin-top: 10px !important;
        border-color: #ecd9b5 !important;
        border-radius: 10px !important;
        background: #fff8ea !important;
        padding: 7px 9px !important;
    }

    #sellerEditProductForm .seller-edit-sensitive-icon {
        width: 20px !important;
        height: 20px !important;
        flex-basis: 20px !important;
        background: #d89208 !important;
        font-size: 9px !important;
    }

    #sellerEditProductForm .seller-edit-sensitive-note p {
        font-size: 6.3px !important;
        line-height: 1.4 !important;
    }

    /* Footer-specific reset wins over old generic [data-close-edit]:first-of-type. */
    #sellerEditProductModal #sellerEditProductForm .seller-edit-footer {
        margin: 10px -20px 0 !important;
        padding: 9px 20px 10px !important;
        background: #ffffff !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }

    #sellerEditProductModal #sellerEditProductForm .seller-edit-footer .seller-edit-button,
    #sellerEditProductModal #sellerEditProductForm .seller-edit-footer [data-close-edit]:first-of-type,
    #sellerEditProductModal #sellerEditProductForm .seller-edit-footer button[type="submit"] {
        display: inline-flex !important;
        width: auto !important;
        min-width: 104px !important;
        height: 38px !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 9px !important;
        padding: 0 15px !important;
        font-size: 8px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        white-space: nowrap !important;
    }

    #sellerEditProductModal #sellerEditProductForm .seller-edit-footer .seller-edit-button--secondary,
    #sellerEditProductModal #sellerEditProductForm .seller-edit-footer [data-close-edit]:first-of-type {
        border: 1px solid #ddd5cc !important;
        background: #ffffff !important;
        color: #5c554e !important;
        box-shadow: none !important;
    }

    #sellerEditProductModal #sellerEditProductForm .seller-edit-footer .seller-edit-button--primary,
    #sellerEditProductModal #sellerEditProductForm .seller-edit-footer button[type="submit"] {
        border: 1px solid #d89208 !important;
        background: #d89208 !important;
        color: #ffffff !important;
        box-shadow: 0 6px 14px rgba(216,146,8,.16) !important;
    }

    #sellerEditProductModal #sellerEditProductForm .seller-edit-footer .seller-edit-button--primary:hover,
    #sellerEditProductModal #sellerEditProductForm .seller-edit-footer button[type="submit"]:hover {
        background: #c98505 !important;
        border-color: #c98505 !important;
        box-shadow: 0 7px 15px rgba(201,133,5,.18) !important;
    }

    /* Compact laptop: preserve A4-like width instead of expanding landscape. */
    @media (min-width: 760px) and (max-height: 820px) {
        #sellerEditProductModal .seller-edit-enterprise-dialog {
            width: min(780px, calc(100vw - 20px)) !important;
            max-width: 780px !important;
            max-height: calc(100dvh - 20px) !important;
        }

        #sellerEditProductModal .seller-edit-enterprise-header {
            padding: 12px 18px 10px !important;
        }

        #sellerEditProductForm.seller-edit-enterprise-form {
            padding: 11px 18px 0 !important;
        }

        #sellerEditProductForm .seller-edit-grid {
            gap: 7px 11px !important;
        }

        #sellerEditProductForm .seller-edit-field input:not([type="hidden"]):not([type="checkbox"]):not([type="file"]),
        #sellerEditProductForm .seller-edit-field select {
            height: 35px !important;
            line-height: 35px !important;
        }

        #sellerEditProductForm .seller-edit-shipping-card {
            min-height: 46px !important;
            padding: 7px 10px !important;
        }

        #sellerEditProductForm .seller-edit-media-row,
        #sellerEditProductForm .seller-edit-upload-box {
            min-height: 62px !important;
        }

        #sellerEditProductForm .seller-edit-current-image,
        #sellerEditProductForm .seller-edit-description-field textarea {
            height: 62px !important;
            min-height: 62px !important;
        }

        #sellerEditProductModal #sellerEditProductForm .seller-edit-footer {
            margin: 8px -18px 0 !important;
            padding: 8px 18px 9px !important;
        }
    }

    @media (max-width: 759px) {
        #sellerEditProductModal .seller-edit-enterprise-dialog {
            width: 100% !important;
            max-width: 100% !important;
        }

        #sellerEditProductModal #sellerEditProductForm .seller-edit-footer {
            margin-right: -16px !important;
            margin-left: -16px !important;
            padding-right: 16px !important;
            padding-left: 16px !important;
        }
    }
    </style>

{{-- Critical first-paint layer: prevents cream/unstyled flash before full CSS finishes. --}}
    <style>
        .seller-products-page {
            background: transparent !important;
            background-color: transparent !important;
            background-image: none !important;
        }

        .seller-products-page .seller-catalog-rail-shell,
        .seller-products-page .seller-products-workspace {
            background: #FFFFFF !important;
            background-color: #FFFFFF !important;
            background-image: none !important;
        }

        .seller-products-page .seller-products-workspace > .mt-4,
        .seller-products-page .seller-products-filter-panel,
        .seller-products-page .seller-products-toolbar,
        .seller-products-page .seller-rail-more-content,
        .seller-products-page [class*="bg-[#fbfaf7]"],
        .seller-products-page [class*="bg-[#fffefa]"] {
            background: #FFFFFF !important;
            background-color: #FFFFFF !important;
            background-image: none !important;
        }
    </style>
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
    const cacheKey = 'sari:seller-products-library:v5:' + libraryUrl;
    const maxCacheAgeMs = 10 * 60 * 1000;
    const serverInitial = @json($initialProductLibrary ?? null);

    window.__SARI_PRODUCTS_LIBRARY_URL__ = libraryUrl;
    window.__SARI_PRODUCTS_LIBRARY_CACHE_KEY__ = cacheKey;

    /*
     * Server-rendered first-screen data wins immediately.
     */
    if (
        serverInitial &&
        Array.isArray(serverInitial.products)
    ) {
        window.__SARI_PRODUCTS_LIBRARY_CACHED__ = serverInitial;

        try {
            window.sessionStorage.setItem(
                cacheKey,
                JSON.stringify({
                    saved_at: Date.now(),
                    data: serverInitial
                })
            );
        } catch (_) {}
    } else {
        try {
            const raw = window.sessionStorage.getItem(cacheKey);

            if (raw) {
                const cached = JSON.parse(raw);
                const age = Date.now() - Number(cached?.saved_at || 0);

                if (
                    cached?.data &&
                    Array.isArray(cached.data.products) &&
                    age >= 0 &&
                    age <= maxCacheAgeMs
                ) {
                    window.__SARI_PRODUCTS_LIBRARY_CACHED__ = cached.data;
                }
            }
        } catch (_) {}
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
            cache: 'no-store',
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
            })
            .catch(function (error) {
                if (error?.name !== 'AbortError') {
                    window.__SARI_PRODUCTS_LIBRARY_PREFETCH_ERROR__ = error;
                }
                return null;
            });
    }

    /*
     * First paint and first product images get CPU/network priority.
     * The complete library is fetched after that initial paint.
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

            if ('requestIdleCallback' in window) {
                idleHandle = window.requestIdleCallback(run, { timeout: 700 });
            } else {
                timeoutHandle = window.setTimeout(run, 180);
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



{{-- Product Management stylesheet is merged directly into this Blade. --}}
<style>
/* ================================================================
   PRODUCT MANAGEMENT — PREMIUM CATALOG POLISH
   Header scale + richer Catalog Health + richer moderation tags.
   Scoped to this page and placed after the merged base Product Management CSS.
   ================================================================ */

.seller-products-page .seller-products-premium-heading {
    padding: 7px 4px 16px;
    background: transparent !important;
}

.seller-products-page .seller-products-premium-kicker {
    margin: 0;
    color: #A86F0B;
    font-size: 9.5px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: .17em;
    text-transform: uppercase;
}

.seller-products-page .seller-products-premium-title {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    gap: 9px;
    margin: 7px 0 0;
    color: #1D1915;
    font-size: clamp(50px, 4.15vw, 68px);
    font-weight: 760;
    line-height: .94;
    letter-spacing: -.052em;
}

.seller-products-page .seller-products-premium-title > span:last-child {
    color: #C7860A;
}

.seller-products-page .seller-products-premium-subtitle {
    max-width: 760px;
    margin: 11px 0 0;
    color: #7D746B;
    font-size: 10.5px;
    font-weight: 400;
    line-height: 1.6;
}


/* Open page heading — never render as a card/container. */
.seller-products-page > section:first-of-type,
.seller-products-page > section:first-of-type > div,
.seller-products-page .seller-products-premium-heading {
    border: 0 !important;
    background: transparent !important;
    background-color: transparent !important;
    background-image: none !important;
    box-shadow: none !important;
}

/* Keep the Seller shell/page canvas visible around the white catalog panels. */
.seller-products-page {
    padding-inline: 0 !important;
}

/* Catalog Health: richer warning palette, still refined rather than neon. */
.seller-products-page .seller-rail-health--premium {
    border-color: #F0C7C1 !important;
    background: linear-gradient(180deg, #FFF9F8 0%, #FFF4F2 100%) !important;
    box-shadow:
        0 8px 22px rgba(148, 49, 43, .055),
        inset 0 1px 0 rgba(255,255,255,.82) !important;
}

.seller-products-page .seller-rail-health--premium > div:first-child > div:first-child > p {
    color: #A9433C !important;
    font-weight: 750 !important;
    letter-spacing: .13em !important;
}

.seller-products-page #productsCatalogHealthScore {
    color: #B42318 !important;
    font-size: 34px !important;
    font-weight: 800 !important;
    letter-spacing: -.045em !important;
    text-shadow: 0 1px 0 rgba(255,255,255,.8);
}

.seller-products-page #productsCatalogHealthScore + span {
    color: #B86A63 !important;
    font-weight: 700 !important;
}

.seller-products-page #productsCatalogHealthLabel {
    border-color: #E7A8A2 !important;
    background: #FCE8E6 !important;
    color: #A8312A !important;
    font-weight: 800 !important;
    letter-spacing: .015em !important;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.48);
}

.seller-products-page .seller-catalog-health-progress {
    overflow: hidden;
    height: 7px !important;
    border-radius: 999px !important;
    background: #F4DAD7 !important;
}

.seller-products-page .seller-catalog-health-progress #productsCatalogHealthBar {
    height: 100% !important;
    border-radius: inherit !important;
    background: #C63D34 !important;
    box-shadow: 0 2px 7px rgba(180,35,24,.20) !important;
}

.seller-products-page #productsCatalogAttention {
    color: #89534E !important;
    font-weight: 600 !important;
}

.seller-products-page .seller-health-status-grid {
    border-top-color: #F0D7D4 !important;
}

/* Product status tags — premium, richer, clearer hierarchy. */
.seller-products-page .seller-product-card-badge {
    min-height: 25px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-width: 1px !important;
    border-style: solid !important;
    border-radius: 999px !important;
    padding: 0 10px !important;
    font-size: 7.5px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    letter-spacing: .035em !important;
    text-transform: none !important;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    box-shadow:
        0 5px 13px rgba(25,20,15,.07),
        inset 0 1px 0 rgba(255,255,255,.58) !important;
}

/* Active / approved — richer emerald. */
.seller-products-page .seller-product-status--active {
    border-color: #9FD0B2 !important;
    background: #DFF3E6 !important;
    color: #17653A !important;
}

/* Pending Review — premium amber/gold. */
.seller-products-page .seller-product-status--pending {
    border-color: #E7C56E !important;
    background: #FFF0C8 !important;
    color: #8D5A00 !important;
}

/* Flagged / rejected — richer rose red. */
.seller-products-page .seller-product-status--danger {
    border-color: #E8AAA5 !important;
    background: #FBE2E0 !important;
    color: #9F2F2A !important;
}

/* Removed / generic listing — polished graphite neutral. */
.seller-products-page .seller-product-status--neutral {
    border-color: #D7D2CA !important;
    background: #F2EFEB !important;
    color: #5F574F !important;
}

/* Secondary promo / Mall badge remains premium dark, but less flat. */
.seller-products-page .seller-product-card-badge--dark {
    border-color: rgba(255,255,255,.14) !important;
    background: rgba(31,28,25,.92) !important;
    color: #FFF7E5 !important;
    box-shadow:
        0 5px 13px rgba(20,17,14,.15),
        inset 0 1px 0 rgba(255,255,255,.08) !important;
}

/* Give cards a subtle premium lift without changing card geometry. */
.seller-products-page .seller-product-card--reference {
    transition:
        border-color .16s ease,
        box-shadow .16s ease,
        transform .16s ease !important;
}

.seller-products-page .seller-product-card--reference:hover {
    transform: translateY(-2px) !important;
    border-color: #DDD0BC !important;
    box-shadow: 0 16px 34px rgba(49,38,25,.075) !important;
}

@media (max-width: 767px) {
    .seller-products-page .seller-products-premium-heading {
        padding-top: 4px;
    }

    .seller-products-page .seller-products-premium-title {
        gap: 6px;
        font-size: clamp(40px, 12vw, 52px);
    }

    .seller-products-page .seller-products-premium-subtitle {
        max-width: 560px;
        font-size: 9.5px;
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
                                loading="{{ $productIndex < 6 ? 'eager' : 'lazy' }}"
                                fetchpriority="{{ $productIndex < 3 ? 'high' : 'auto' }}"
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

        <script>
        /*
         * ------------------------------------------------------------
         * PRODUCT GRID FAST PAINT
         * ------------------------------------------------------------
         * If Dashboard/Orders already prefetched the Product Library,
         * replace skeletons RIGHT HERE while the browser is still parsing
         * the rest of this Blade file. The full Product Management script
         * hydrates/refreshes everything later.
         */
        (function () {
            const grid = document.getElementById('productsGrid');
            if (!grid) return;

            if (grid.querySelector('[data-product-item]')) {
                window.__SARI_PRODUCTS_FAST_PAINTED__ = true;
                return;
            }

            const libraryUrl =
                window.__SARI_PRODUCTS_LIBRARY_URL__
                || @json(route('seller.products.library'));

            const cacheKey =
                window.__SARI_PRODUCTS_LIBRARY_CACHE_KEY__
                || ('sari:seller-products-library:v5:' + libraryUrl);

            let cachedData =
                window.__SARI_PRODUCTS_LIBRARY_CACHED__
                || null;

            if (!cachedData) {
                try {
                    const raw = window.sessionStorage.getItem(cacheKey);

                    if (raw) {
                        const stored = JSON.parse(raw);
                        const age = Date.now() - Number(stored?.saved_at || 0);

                        if (
                            stored?.data &&
                            Array.isArray(stored.data.products) &&
                            age >= 0 &&
                            age <= 10 * 60 * 1000
                        ) {
                            cachedData = stored.data;
                            window.__SARI_PRODUCTS_LIBRARY_CACHED__ = cachedData;
                        }
                    }
                } catch (_) {}
            }

            if (!cachedData || !Array.isArray(cachedData.products)) {
                return;
            }

            const escapeHtml = function (value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            };

            const peso = function (value) {
                const amount = Number(value || 0);

                return new Intl.NumberFormat('en-PH', {
                    style: 'currency',
                    currency: 'PHP',
                    maximumFractionDigits: 2
                }).format(Number.isFinite(amount) ? amount : 0);
            };

            const cards = cachedData.products.map(function (product, index) {
                const moderationStatus =
                    String(product?.moderation_status || '').toLowerCase();

                const stockValue =
                    Number(product?.stock || 0);

                const threshold =
                    Number(product?.low_stock_threshold ?? 5);

                let badgeLabel =
                    product?.status_label
                    || product?.moderation_status
                    || 'Listing';

                let badgeClass =
                    'seller-product-status--neutral';

                if (moderationStatus === 'approved') {
                    badgeLabel = 'Active';
                    badgeClass = 'seller-product-status--active';
                } else if (moderationStatus === 'pending') {
                    badgeLabel = 'Pending Review';
                    badgeClass = 'seller-product-status--pending';
                } else if (
                    moderationStatus === 'flagged'
                    || moderationStatus === 'rejected'
                ) {
                    badgeLabel =
                        moderationStatus === 'flagged'
                        ? 'Flagged'
                        : 'Rejected';

                    badgeClass = 'seller-product-status--danger';
                } else if (moderationStatus === 'removed') {
                    badgeLabel = 'Removed';
                }

                const stockClass =
                    stockValue <= 0
                        ? 'seller-stock-text--out'
                        : (
                            stockValue <= threshold
                                ? 'seller-stock-text--low'
                                : 'seller-stock-text--normal'
                        );

                const imageMarkup = product?.image_url
                    ? `
                        <img
                            src="${escapeHtml(product.image_url)}"
                            alt="${escapeHtml(product.name || 'Product')}"
                            class="seller-product-image h-full w-full object-cover"
                            loading="${index < 6 ? 'eager' : 'lazy'}"
                            fetchpriority="${index < 3 ? 'high' : 'auto'}"
                            decoding="async"
                        >
                    `
                    : `
                        <div class="grid h-full w-full place-items-center bg-[#f2f0ec] text-[#aaa198]">
                            <svg viewBox="0 0 24 24" class="h-9 w-9" fill="none" stroke="currentColor" stroke-width="1.45" aria-hidden="true">
                                <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                            </svg>
                        </div>
                    `;

                const price =
                    Number(
                        product?.sale_price
                        ?? product?.price
                        ?? 0
                    );

                return `
                    <article
                        data-product-item
                        data-product-id="${escapeHtml(product?.id)}"
                        data-search="${escapeHtml([
                            product?.name,
                            product?.category,
                            product?.brand,
                            product?.sku
                        ].filter(Boolean).join(' ').toLowerCase())}"
                        data-status="${escapeHtml(product?.moderation_status || '')}"
                        data-stock="${escapeHtml(product?.stock_state || '')}"
                        data-category="${escapeHtml(product?.category || 'Uncategorized')}"
                        class="seller-products-card seller-product-card--reference border bg-white"
                    >
                        <div class="seller-product-card-media">
                            ${imageMarkup}

                            <div class="seller-product-card-badges">
                                <span class="seller-product-card-badge ${badgeClass}">
                                    ${escapeHtml(badgeLabel)}
                                </span>

                                ${product?.mall_badge
                                    ? '<span class="seller-product-card-badge seller-product-card-badge--dark">Mall</span>'
                                    : ''
                                }
                            </div>
                        </div>

                        <div class="seller-product-card-body">
                            <div class="seller-product-card-heading">
                                <div class="min-w-0">
                                    <h3 class="seller-product-card-title">
                                        ${escapeHtml(product?.name || 'Untitled product')}
                                    </h3>

                                    <p class="seller-product-card-category">
                                        ${escapeHtml(product?.category || 'Uncategorized')}
                                        ${product?.brand ? ' · ' + escapeHtml(product.brand) : ''}
                                    </p>
                                </div>

                                <div class="seller-product-card-price-wrap">
                                    <strong class="seller-product-card-price">
                                        ${peso(price)}
                                    </strong>
                                </div>
                            </div>

                            <div class="seller-product-card-divider"></div>

                            <div class="seller-product-card-footer">
                                <div class="seller-product-card-stock-copy">
                                    <p class="seller-product-card-stock ${stockClass}">
                                        ${stockValue.toLocaleString('en-PH')} in stock
                                    </p>

                                    <p class="seller-product-card-updated">
                                        ${escapeHtml(product?.created_at_human || '')}
                                    </p>
                                </div>

                                <div class="seller-product-actions seller-product-card-actions">
                                    <button
                                        type="button"
                                        data-page-view-product
                                        data-id="${escapeHtml(product?.id)}"
                                        title="View product"
                                        aria-label="View product"
                                        class="seller-product-action seller-product-action--view"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65">
                                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                            <circle cx="12" cy="12" r="2.5"></circle>
                                        </svg>
                                    </button>

                                    <button
                                        type="button"
                                        data-page-edit-product
                                        data-id="${escapeHtml(product?.id)}"
                                        title="Edit product"
                                        aria-label="Edit product"
                                        class="seller-product-action seller-product-action--edit"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                        </svg>
                                    </button>

                                    <button
                                        type="button"
                                        data-page-product-action="archive"
                                        data-id="${escapeHtml(product?.id)}"
                                        data-name="${escapeHtml(product?.name || '')}"
                                        title="Archive product"
                                        aria-label="Archive product"
                                        class="seller-product-action seller-product-action--archive"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65">
                                            <rect x="4" y="7" width="16" height="13" rx="2"></rect>
                                            <path d="M3 4h18v3H3z"></path>
                                            <path d="M10 11h4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>
                `;
            });

            grid.innerHTML = cards.join('');
            window.__SARI_PRODUCTS_FAST_PAINTED__ = true;

            const resultCount =
                document.getElementById('productsResultCount');

            if (resultCount) {
                resultCount.textContent =
                    `${cachedData.products.length} active products`;
            }
        })();
        </script>

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
                        <span>Total Stock</span>
                        <strong id="viewProductStock">0</strong>
                    </div>

                    <div class="seller-view-enterprise-fact">
                        <span>SKU</span>
                        <strong id="viewProductSku">—</strong>
                    </div>

                    <div class="seller-view-enterprise-fact">
                        <span>Brand</span>
                        <strong id="viewProductBrand">No Brand</strong>
                    </div>

                    <div class="seller-view-enterprise-fact">
                        <span>Listing Type</span>
                        <strong id="viewProductType">Simple Product</strong>
                    </div>

                    <div class="seller-view-enterprise-fact seller-view-enterprise-fact--status">
                        <span>Moderation Status</span>
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


<style>
/* ================================================================
   PRODUCT MANAGEMENT — PURE WHITE SURFACE FINAL LAYER
   Removes remaining warm/cream surfaces while retaining gold accents.
   ================================================================ */
.seller-products-page {
    background: transparent !important;
    background-color: transparent !important;
    background-image: none !important;
}

.seller-products-page .seller-catalog-rail-shell,
.seller-products-page .seller-products-workspace,
.seller-products-page .seller-rail-more-content,
.seller-products-page .seller-products-card,
.seller-products-page .seller-product-card--reference,
.seller-products-page .seller-product-search-suggestions,
.seller-products-page .seller-premium-dropdown-trigger,
.seller-products-page .seller-premium-dropdown-menu,
.seller-products-page .seller-products-filter {
    background: #FFFFFF !important;
    background-color: #FFFFFF !important;
    background-image: none !important;
}

/* Filter surface used directly in Blade with bg-[#fbfaf7]. */
.seller-products-page .seller-products-workspace > div.rounded-\[14px\] {
    background: #FFFFFF !important;
    background-color: #FFFFFF !important;
}

/* Preserve intentionally colored semantic surfaces. */
.seller-products-page .seller-rail-health--premium {
    background: linear-gradient(180deg, #FFF9F8 0%, #FFF4F2 100%) !important;
}

.seller-products-page .seller-product-status--active {
    background: #DFF3E6 !important;
}

.seller-products-page .seller-product-status--pending {
    background: #FFF0C8 !important;
}

.seller-products-page .seller-product-status--danger {
    background: #FBE2E0 !important;
}

.seller-products-page .seller-product-status--neutral {
    background: #F2EFEB !important;
}

.seller-products-page .seller-product-card-badge--dark {
    background: rgba(31,28,25,.92) !important;
}
</style>


<style>
/* ================================================================
   PRODUCT MANAGEMENT — OPEN WORKSPACE FINAL PASS
   Removes the large outer container around filters + product cards.
   Individual product cards and the filter bar remain standalone.
   ================================================================ */

/* RIGHT workspace: no enclosing card/container. */
.seller-products-page .seller-products-workspace {
    border: 0 !important;
    border-color: transparent !important;
    border-radius: 0 !important;
    background: transparent !important;
    background-color: transparent !important;
    background-image: none !important;
    box-shadow: none !important;
    padding: 0 !important;
    overflow: visible !important;
}

/* The filter bar remains its own clean white control surface. */
.seller-products-page .seller-products-workspace > .mt-4.rounded-\[14px\] {
    margin-top: 0 !important;
    border: 1px solid #E7E2DA !important;
    border-radius: 14px !important;
    background: #FFFFFF !important;
    background-color: #FFFFFF !important;
    box-shadow: 0 7px 20px rgba(55, 43, 28, .035) !important;
}

/* Product grid sits directly on the page canvas, not inside another panel. */
.seller-products-page #productsGrid {
    margin-top: 14px !important;
    background: transparent !important;
}

/* Product cards stay as independent white cards. */
.seller-products-page #productsGrid .seller-product-card--reference,
.seller-products-page #productsGrid .seller-products-card {
    background: #FFFFFF !important;
    background-color: #FFFFFF !important;
}

/* Header remains completely open, with a larger title. */
.seller-products-page > section:first-of-type {
    margin-bottom: 4px !important;
    padding-left: 2px !important;
    padding-right: 2px !important;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
}

.seller-products-page .seller-products-premium-title {
    font-size: clamp(50px, 4.15vw, 68px) !important;
    line-height: .92 !important;
    letter-spacing: -.058em !important;
}

.seller-products-page .seller-products-premium-kicker {
    font-size: 10px !important;
    letter-spacing: .18em !important;
}

.seller-products-page .seller-products-premium-subtitle {
    margin-top: 12px !important;
    font-size: 10.5px !important;
}

/* Keep actual page background neutral/white-clean, never cream. */
.seller-products-page,
.seller-products-page .min-w-0 {
    background-image: none !important;
}

@media (max-width: 767px) {
    .seller-products-page .seller-products-premium-title {
        font-size: clamp(31px, 9vw, 38px) !important;
    }
}
</style>


<style>
/* ================================================================
   CATALOG HEALTH — FLAT PREMIUM WARNING PALETTE
   Keep the richer red palette, remove all red/pink glow and shadows.
   ================================================================ */

.seller-products-page .seller-rail-health--premium {
    box-shadow: none !important;
    filter: none !important;
}

.seller-products-page #productsCatalogHealthScore {
    text-shadow: none !important;
    filter: none !important;
}

.seller-products-page #productsCatalogHealthLabel {
    box-shadow: none !important;
    filter: none !important;
}

.seller-products-page .seller-catalog-health-progress,
.seller-products-page .seller-catalog-health-progress #productsCatalogHealthBar {
    box-shadow: none !important;
    filter: none !important;
}

.seller-products-page .seller-health-status-grid,
.seller-products-page .seller-health-status-item,
.seller-products-page #productsCatalogAttention {
    box-shadow: none !important;
    text-shadow: none !important;
    filter: none !important;
}
</style>


<style>
/* ================================================================
   FINAL HARD FIX — LARGE TITLE + FLAT WHITE CATALOG HEALTH
   This is intentionally last and uses !important to beat legacy rules.
   ================================================================ */

.seller-products-page .seller-products-premium-title {
    font-size: clamp(34px, 2.8vw, 44px) !important;
    line-height: .98 !important;
    letter-spacing: -.045em !important;
    font-weight: 800 !important;
}

.seller-products-page .seller-rail-health--premium,
.seller-products-page .seller-rail-health--premium:hover,
.seller-products-page .seller-rail-health--premium:focus-within {
    background: #FFFFFF !important;
    background-color: #FFFFFF !important;
    background-image: none !important;
    border-color: #E8DED6 !important;
    box-shadow: none !important;
    filter: none !important;
    text-shadow: none !important;
}

.seller-products-page .seller-rail-health--premium::before,
.seller-products-page .seller-rail-health--premium::after {
    content: none !important;
    display: none !important;
    box-shadow: none !important;
    background: transparent !important;
}

.seller-products-page .seller-rail-health--premium > *,
.seller-products-page .seller-health-status-grid,
.seller-products-page .seller-health-status-item {
    box-shadow: none !important;
    filter: none !important;
    text-shadow: none !important;
}

.seller-products-page .seller-health-status-grid {
    background: #FFFFFF !important;
    background-color: #FFFFFF !important;
    background-image: none !important;
    border-top-color: #EEE5DF !important;
}

/* Keep warning semantics red, but remove the pink panel wash. */
.seller-products-page #productsCatalogHealthScore {
    color: #B42318 !important;
    text-shadow: none !important;
}

.seller-products-page #productsCatalogHealthLabel {
    background: #FCE8E6 !important;
    border-color: #F0C1BD !important;
    color: #A8312A !important;
    box-shadow: none !important;
}

.seller-products-page .seller-catalog-health-progress {
    background: #EEE7E3 !important;
    box-shadow: none !important;
}

.seller-products-page .seller-catalog-health-progress #productsCatalogHealthBar {
    background: #C63D34 !important;
    box-shadow: none !important;
}

.seller-products-page #productsCatalogAttention {
    color: #7D5A56 !important;
    text-shadow: none !important;
}

@media (max-width: 767px) {
    .seller-products-page .seller-products-premium-title {
        font-size: clamp(40px, 12vw, 52px) !important;
    }
}
</style>


<style>
/* ================================================================
   PRODUCT HEADER — FORMAL SCALE FINAL PASS
   Clean executive proportions: prominent, but not oversized.
   ================================================================ */
.seller-products-page .seller-products-premium-heading {
    padding-top: 5px !important;
    padding-bottom: 13px !important;
}

.seller-products-page .seller-products-premium-kicker {
    font-size: 8.5px !important;
    font-weight: 800 !important;
    letter-spacing: .16em !important;
    color: #A46C0A !important;
}

.seller-products-page .seller-products-premium-title {
    font-size: clamp(34px, 2.8vw, 44px) !important;
    line-height: .98 !important;
    letter-spacing: -.045em !important;
    font-weight: 750 !important;
}

.seller-products-page .seller-products-premium-title > span:first-child {
    color: #1F1B17 !important;
}

.seller-products-page .seller-products-premium-title > span:last-child {
    color: #C9890B !important;
}

.seller-products-page .seller-products-premium-subtitle {
    max-width: 700px !important;
    margin-top: 8px !important;
    color: #7F766D !important;
    font-size: 9.5px !important;
    line-height: 1.55 !important;
}

@media (max-width: 767px) {
    .seller-products-page .seller-products-premium-title {
        font-size: clamp(31px, 9vw, 38px) !important;
    }
}
</style>


<style>
/* ================================================================
   PRODUCT HEADER — REFINED FORMAL SCALE
   Slightly smaller and lighter than the previous version.
   ================================================================ */
.seller-products-page .seller-products-premium-title {
    font-size: clamp(30px, 2.35vw, 38px) !important;
    line-height: 1 !important;
    letter-spacing: -.035em !important;
    font-weight: 650 !important;
}

.seller-products-page .seller-products-premium-title > span:first-child,
.seller-products-page .seller-products-premium-title > span:last-child {
    font-weight: 650 !important;
}

.seller-products-page .seller-products-premium-kicker {
    font-weight: 700 !important;
}

@media (max-width: 767px) {
    .seller-products-page .seller-products-premium-title {
        font-size: clamp(28px, 8vw, 34px) !important;
        font-weight: 650 !important;
    }
}
</style>

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
<script src="{{ asset('js/seller-products.js') }}" data-navigate-once></script>
@endpush
