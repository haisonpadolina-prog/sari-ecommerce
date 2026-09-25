@extends('layouts.seller')

@section('title', 'Order Management — SARI Seller')
@section('page-title', 'Order Management')

@push('styles')
{{-- Seller Order Management design merged inline; backend/JS/routes preserved. --}}
<style id="sariSellerOrdersInlineStyles">
    .seller-orders-page {
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
        font-weight: 400;
    }

    .seller-orders-page button,
    .seller-orders-page input,
    .seller-orders-page select,
    .seller-orders-page a {
        font-family: inherit;
    }

    .seller-order-row {
        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            transform .18s ease,
            background-color .18s ease;
    }

    .seller-order-row:hover {
        transform: translateY(-1px);
        border-color: #dfd1bd;
        background: #fffefa;
        box-shadow: 0 12px 26px rgba(48, 37, 24, .045);
    }

    .seller-order-modal-panel {
        animation: sellerOrderModalIn .14s ease-out both;
    }

    @keyframes sellerOrderModalIn {
        from {
            opacity: 0;
            transform: translateY(5px) scale(.995);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .seller-order-progress {
        background: linear-gradient(90deg, #d59618, #e4ae3f);
    }

    .seller-order-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #d8cdbf transparent;
    }

    .seller-order-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .seller-order-scrollbar::-webkit-scrollbar-thumb {
        background: #d8cdbf;
        border-radius: 999px;
    }


    .seller-order-image-frame {
        position: relative;
        isolation: isolate;
        background: #f1eee9;
    }

.seller-order-image-frame > img {
    opacity: 1;
}

.seller-orders-stage {
    position: relative;
}

.seller-orders-content {
    display: block !important;
}
@media (prefers-reduced-motion: reduce) {
    .seller-order-modal-panel,
    .seller-order-row {
        animation: none !important;
        transition: none !important;
    }
}


/* ============================================================
   ORDER MANAGEMENT — 100% ZOOM / LAPTOP-FIT COMPACT PASS
   Target: 1366×768 and 1440×900 at normal browser zoom.
   Layout-only overrides; order behavior, realtime and forms are unchanged.
   ============================================================ */
.seller-orders-page {
    width: 100%;
    max-width: 1560px !important;
    margin-inline: auto;
    padding-bottom: 18px;
}

.seller-orders-header {
    gap: 10px !important;
    padding-top: 0 !important;
}

.seller-orders-header-icon {
    width: 42px !important;
    height: 42px !important;
    flex-basis: 42px !important;
    border-radius: 12px !important;
}

.seller-orders-header-icon svg {
    width: 18px !important;
    height: 18px !important;
}

.seller-orders-title {
    font-size: clamp(23px, 1.65vw, 30px) !important;
    line-height: 1.04 !important;
    letter-spacing: -.04em !important;
}

.seller-orders-subtitle {
    margin-top: 5px !important;
    font-size: 9px !important;
    line-height: 1.45 !important;
}

.seller-orders-header a {
    height: 38px !important;
    gap: 7px !important;
    border-radius: 9px !important;
    padding-inline: 12px !important;
    font-size: 8.3px !important;
}

.seller-orders-header a svg {
    width: 14px !important;
    height: 14px !important;
}

.seller-orders-summary {
    margin-top: 12px !important;
    gap: 8px !important;
}

.seller-orders-summary-card {
    min-width: 0;
    min-height: 92px;
    border-radius: 14px !important;
    padding: 11px 12px !important;
}

.seller-orders-summary-card p:first-child {
    font-size: 9px !important;
}

.seller-orders-summary-card p:nth-child(2) {
    margin-top: 6px !important;
    font-size: 24px !important;
}

.seller-orders-summary-card p:nth-child(3) {
    margin-top: 7px !important;
    font-size: 7.8px !important;
}

.seller-orders-summary-card > div > span {
    width: 36px !important;
    height: 36px !important;
    border-radius: 10px !important;
}

.seller-orders-summary-card > div > span svg {
    width: 15px !important;
    height: 15px !important;
}

.seller-orders-workspace {
    margin-top: 11px !important;
    border-radius: 16px !important;
}

.seller-orders-toolbar {
    gap: 10px !important;
    padding: 12px 14px !important;
}

.seller-orders-toolbar h2 {
    font-size: 16px !important;
}

.seller-orders-toolbar h2 + p {
    margin-top: 3px !important;
    font-size: 8.7px !important;
    line-height: 1.45 !important;
}

#sellerOrderResultCount {
    margin-top: 6px !important;
    font-size: 8px !important;
}

#sellerOrderSearch,
#sellerOrderStatusFilter {
    height: 38px !important;
    border-radius: 9px !important;
    font-size: 9px !important;
}

#sellerOrderSearch {
    padding-left: 36px !important;
}

.seller-orders-table-head {
    grid-template-columns: minmax(205px, 1.45fr) minmax(130px, .85fr) 112px 92px 118px 44px !important;
    gap: 9px !important;
    padding: 9px 13px !important;
}

.seller-orders-table-head > span {
    font-size: 8px !important;
}

#sellerOrderRows {
    padding: 9px !important;
}

.seller-order-row {
    border-radius: 13px !important;
}

.seller-order-grid {
    grid-template-columns: minmax(205px, 1.45fr) minmax(130px, .85fr) 112px 92px 118px 44px !important;
    gap: 9px !important;
    padding: 9px 10px !important;
}

.seller-order-grid .seller-order-image-frame {
    width: 58px !important;
    height: 58px !important;
    border-radius: 10px !important;
}

.seller-order-grid .seller-order-image-frame + div p:first-child {
    font-size: 10px !important;
}

.seller-order-grid .seller-order-image-frame + div p:nth-child(2) {
    margin-top: 3px !important;
    font-size: 8.7px !important;
}

.seller-order-grid .seller-order-image-frame + div p:nth-child(3) {
    margin-top: 3px !important;
    font-size: 7.8px !important;
}

.seller-order-grid > div:nth-child(2) p:not(.lg\:hidden),
.seller-order-grid > div:nth-child(3) span,
.seller-order-grid > div:nth-child(4) p:last-child,
.seller-order-grid > div:nth-child(5) p {
    font-size: 8.7px !important;
}

.seller-order-view-button {
    width: 36px !important;
    height: 36px !important;
    border-radius: 9px !important;
}

.seller-order-view-button svg {
    width: 15px !important;
    height: 15px !important;
}

/* Order detail modal: full two-row workspace fits a normal laptop viewport. */
#sellerOrderDetailModal {
    padding: 10px !important;
}

.seller-order-modal-panel {
    width: min(1040px, calc(100vw - 20px)) !important;
    max-width: 1040px !important;
    height: min(720px, calc(100vh - 20px));
    max-height: calc(100vh - 20px) !important;
    display: flex;
    flex-direction: column;
    border-radius: 17px !important;
}

.seller-order-modal-panel > div:first-child {
    flex: 0 0 auto;
    padding: 11px 14px !important;
}

.seller-order-modal-panel > div:first-child > div > span {
    width: 37px !important;
    height: 37px !important;
    border-radius: 10px !important;
}

.seller-order-modal-panel > div:first-child h3 {
    font-size: 15px !important;
}

.seller-order-modal-panel > div:first-child [data-close-order-modal] {
    width: 34px !important;
    height: 34px !important;
    border-radius: 9px !important;
}

.seller-order-modal-scroll {
    flex: 1 1 auto;
    min-height: 0;
    max-height: none !important;
    overflow: hidden !important;
}

.seller-order-modal-grid {
    height: 100%;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    grid-template-rows: repeat(2, minmax(0, 1fr));
    gap: 9px !important;
    padding: 10px !important;
}

.seller-order-modal-card {
    min-height: 0 !important;
    overflow: hidden;
    border-radius: 13px !important;
    padding: 11px !important;
}

.seller-order-modal-card > div:first-child span.grid {
    width: 31px !important;
    height: 31px !important;
    border-radius: 9px !important;
}

.seller-order-modal-card h4 {
    font-size: 9.5px !important;
}

.seller-order-modal-card .mt-4 {
    margin-top: 10px !important;
}

.seller-order-modal-card .space-y-4 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 10px !important;
}

.seller-order-modal-card .space-y-3 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 8px !important;
}

.seller-order-modal-card:first-child .seller-order-scrollbar {
    max-height: none !important;
    min-height: 0;
    overflow-y: auto !important;
}

.seller-order-modal-card:first-child .seller-order-image-frame {
    width: 48px !important;
    height: 48px !important;
}

.seller-order-modal-panel > div:last-child {
    flex: 0 0 auto;
    padding: 9px 14px !important;
}

.seller-order-modal-panel > div:last-child [data-close-order-modal] {
    height: 36px !important;
    min-width: 104px !important;
    border-radius: 9px !important;
}

/* Mid-size desktop: keep the table dense enough to avoid horizontal overflow. */
@media (min-width: 1024px) and (max-width: 1279px) {
    .seller-orders-page {
        max-width: 100% !important;
    }

    .seller-orders-table-head,
    .seller-order-grid {
        grid-template-columns: minmax(185px, 1.4fr) minmax(115px, .8fr) 102px 82px 104px 40px !important;
        gap: 7px !important;
    }

    .seller-order-grid .seller-order-image-frame {
        width: 52px !important;
        height: 52px !important;
    }

    .seller-order-modal-panel {
        width: calc(100vw - 18px) !important;
    }
}

/* Smaller heights: shave vertical chrome before allowing any modal scroll. */
@media (min-width: 1024px) and (max-height: 760px) {
    .seller-orders-header-icon {
        width: 38px !important;
        height: 38px !important;
        flex-basis: 38px !important;
    }

    .seller-orders-title {
        font-size: 22px !important;
    }

    .seller-orders-summary-card {
        min-height: 82px;
        padding: 9px 11px !important;
    }

    .seller-orders-summary-card p:nth-child(2) {
        font-size: 22px !important;
    }

    .seller-order-modal-panel {
        height: calc(100vh - 14px);
        max-height: calc(100vh - 14px) !important;
    }

    .seller-order-modal-grid {
        gap: 7px !important;
        padding: 8px !important;
    }

    .seller-order-modal-card {
        padding: 9px !important;
    }
}

/* Tablet/mobile remains naturally scrollable and stacked. */
@media (max-width: 1023px) {
    .seller-orders-title {
        font-size: clamp(22px, 5vw, 28px) !important;
    }

    .seller-orders-summary {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .seller-orders-table-head {
        display: none !important;
    }

    .seller-order-grid {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
        padding: 12px !important;
    }

    .seller-order-modal-panel {
        height: auto;
        max-height: calc(100vh - 16px) !important;
    }

    .seller-order-modal-scroll {
        overflow-y: auto !important;
    }

    .seller-order-modal-grid {
        height: auto;
        grid-template-columns: 1fr !important;
        grid-template-rows: none;
        padding: 10px !important;
    }

    .seller-order-modal-card {
        overflow: visible;
    }
}

@media (max-width: 639px) {
    .seller-orders-header {
        align-items: stretch !important;
    }

    .seller-orders-header > div:last-child {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        width: 100%;
    }

    .seller-orders-header a {
        width: 100%;
        justify-content: center;
    }

    .seller-orders-summary {
        gap: 7px !important;
    }

    .seller-orders-summary-card {
        min-height: 86px;
    }

    .seller-orders-toolbar {
        padding: 12px !important;
    }

    #sellerOrderSearch,
    #sellerOrderStatusFilter {
        width: 100% !important;
        min-width: 0 !important;
    }
}

/* ============================================================
   ORDER MANAGEMENT — APPROVED ACCOUNTS REFERENCE VISUAL SYSTEM
   Clean admin table UI based on the supplied reference screenshot.
   UI-only: backend, routes, IDs, realtime and order actions unchanged.
   ============================================================ */
.seller-orders-page {
    width: 100%;
    max-width: none !important;
    margin: 0 !important;
    padding: 0 0 24px !important;
    color: #211d18;
}

.seller-orders-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px !important;
    padding: 4px 0 0 !important;
}

.seller-orders-heading-group {
    display: flex;
    min-width: 0;
    align-items: center;
    gap: 15px;
}

.seller-orders-header-icon {
    display: grid;
    width: 52px !important;
    height: 52px !important;
    flex: 0 0 52px !important;
    place-items: center;
    border: 1px solid #ead9ba;
    border-radius: 15px !important;
    background: #fff9ed;
    color: #b97805;
    box-shadow: 0 6px 18px rgba(108, 79, 34, .045);
}

.seller-orders-header-icon svg {
    width: 20px !important;
    height: 20px !important;
}

.seller-orders-eyebrow {
    margin: 0 0 6px;
    color: #9c6810;
    font-size: 8px;
    font-weight: 700;
    line-height: 1;
    letter-spacing: .15em;
}

.seller-orders-title {
    margin: 0;
    color: #17130f;
    font-size: clamp(28px, 2.15vw, 36px) !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    letter-spacing: -.045em !important;
}

.seller-orders-title-accent {
    margin-left: 5px;
    color: #d99400;
}

.seller-orders-subtitle {
    margin-top: 8px !important;
    max-width: 760px;
    color: #83796f !important;
    font-size: 10px !important;
    line-height: 1.55 !important;
}

.seller-orders-header-actions {
    display: flex;
    flex: 0 0 auto;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 8px;
}

.seller-orders-header-action {
    display: inline-flex;
    height: 42px !important;
    align-items: center;
    justify-content: center;
    gap: 8px !important;
    border: 1px solid #e5d9c9;
    border-radius: 12px !important;
    background: #fff;
    padding: 0 14px !important;
    color: #514a42;
    font-size: 8.7px !important;
    font-weight: 600;
    box-shadow: 0 9px 22px rgba(58, 45, 29, .05);
    transition: border-color .14s ease, background-color .14s ease, color .14s ease, transform .14s ease;
}

.seller-orders-header-action:hover {
    transform: translateY(-1px);
    border-color: #d6bd94;
    background: #fffaf1;
    color: #9a660b;
}

.seller-orders-header-action svg {
    width: 14px !important;
    height: 14px !important;
}

.seller-orders-summary {
    margin-top: 20px !important;
    grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    gap: 10px !important;
}

.seller-orders-summary-card {
    min-width: 0;
    min-height: 94px;
    border: 1px solid #e8dccb !important;
    border-radius: 15px !important;
    background: #fff;
    padding: 14px 16px !important;
    box-shadow: 0 10px 25px rgba(57, 45, 30, .045) !important;
}

.seller-orders-summary-card > div {
    height: 100%;
    align-items: flex-start;
}

.seller-orders-summary-card p:first-child {
    color: #675d53 !important;
    font-size: 8.6px !important;
    font-weight: 500 !important;
}

.seller-orders-summary-card p:nth-child(2) {
    margin-top: 6px !important;
    color: #17130f !important;
    font-size: 24px !important;
    font-weight: 700 !important;
    letter-spacing: -.035em !important;
}

.seller-orders-summary-card p:nth-child(3) {
    margin-top: 7px !important;
    color: #9a8f83 !important;
    font-size: 7.5px !important;
}

.seller-orders-summary-icon {
    display: grid !important;
    width: 38px !important;
    height: 38px !important;
    flex: 0 0 38px !important;
    place-items: center;
    border: 1px solid rgba(0, 0, 0, .045);
    border-radius: 11px !important;
}

.seller-orders-summary-icon svg {
    width: 15px !important;
    height: 15px !important;
}

.seller-orders-filter-panel {
    display: grid;
    grid-template-columns: minmax(280px, 1fr) minmax(150px, 188px) 126px 64px;
    gap: 10px;
    margin-top: 14px;
    padding: 10px;
    border: 1px solid #e8dccb;
    border-radius: 15px;
    background: #fff;
    box-shadow: 0 9px 24px rgba(57, 45, 30, .04);
}

.seller-orders-search-field {
    position: relative;
    display: block;
    min-width: 0;
}

.seller-orders-search-field > span {
    position: absolute;
    left: 14px;
    top: 50%;
    display: grid;
    width: 16px;
    height: 16px;
    place-items: center;
    transform: translateY(-50%);
    color: #948a7f;
    pointer-events: none;
}

.seller-orders-search-field svg {
    width: 15px;
    height: 15px;
}

#sellerOrderSearch,
#sellerOrderStatusFilter {
    width: 100% !important;
    height: 42px !important;
    border: 1px solid #e2d6c6 !important;
    border-radius: 10px !important;
    background: #fff !important;
    color: #403930 !important;
    font-size: 8.8px !important;
    outline: none !important;
    box-shadow: 0 2px 8px rgba(54, 43, 29, .018);
    transition: border-color .14s ease, box-shadow .14s ease;
}

#sellerOrderSearch {
    padding: 0 14px 0 40px !important;
}

#sellerOrderSearch::placeholder {
    color: #a69c91;
}

#sellerOrderStatusFilter {
    min-width: 0 !important;
    padding: 0 36px 0 13px !important;
    font-weight: 600;
}

#sellerOrderSearch:focus,
#sellerOrderStatusFilter:focus {
    border-color: #d4af67 !important;
    box-shadow: 0 0 0 3px rgba(217, 148, 0, .07) !important;
}

.seller-orders-apply-filter,
.seller-orders-reset-filter {
    display: inline-flex;
    height: 42px;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    font-size: 8.6px;
    font-weight: 700;
    transition: transform .14s ease, background-color .14s ease, border-color .14s ease;
}

.seller-orders-apply-filter {
    gap: 7px;
    border: 1px solid #d99400;
    background: #d99400;
    padding: 0 15px;
    color: #fff;
    box-shadow: 0 8px 18px rgba(217, 148, 0, .14);
}

.seller-orders-apply-filter:hover {
    transform: translateY(-1px);
    background: #cc8b00;
}

.seller-orders-apply-filter svg {
    width: 14px;
    height: 14px;
}

.seller-orders-reset-filter {
    border: 1px solid #e3d8ca;
    background: #fff;
    color: #6c6258;
}

.seller-orders-reset-filter:hover {
    border-color: #d3c0a5;
    background: #fffaf2;
}

.seller-orders-workspace {
    margin-top: 14px !important;
    overflow: hidden;
    border: 1px solid #e8dccb !important;
    border-radius: 15px !important;
    background: #fff;
    box-shadow: 0 10px 26px rgba(57, 45, 30, .045) !important;
}

.seller-orders-table-titlebar {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    padding: 14px 16px 12px;
    border-bottom: 1px solid #eee5da;
    background: #fff;
}

.seller-orders-table-titlebar h2 {
    margin: 0;
    color: #211c17;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: -.02em;
}

.seller-orders-table-titlebar h2 + p {
    margin-top: 3px;
    color: #93887d;
    font-size: 7.8px;
}

#sellerOrderResultCount {
    margin: 0 !important;
    color: #8f681f !important;
    font-size: 7.8px !important;
    font-weight: 600 !important;
    white-space: nowrap;
}

.seller-orders-table-head {
    display: grid !important;
    grid-template-columns: 34px minmax(230px, 1.4fr) minmax(150px, .9fr) 112px 92px 112px 46px !important;
    align-items: center;
    gap: 10px !important;
    padding: 9px 14px !important;
    border-bottom: 1px solid #e9dfd2;
    background: #fdfbf8 !important;
}

.seller-orders-table-head > span {
    color: #7c7267 !important;
    font-size: 7.5px !important;
    font-weight: 700 !important;
    letter-spacing: .025em;
    text-transform: uppercase;
}

.seller-orders-table-body {
    padding: 0 !important;
}

.seller-order-row {
    border: 0 !important;
    border-bottom: 1px solid #eee6dc !important;
    border-radius: 0 !important;
    background: #fff !important;
    box-shadow: none !important;
    transform: none !important;
}

.seller-order-row:last-child {
    border-bottom: 0 !important;
}

.seller-order-row:hover {
    border-color: #eee6dc !important;
    background: #fffdf9 !important;
    box-shadow: inset 3px 0 0 #d99400 !important;
    transform: none !important;
}

.seller-order-grid {
    display: grid !important;
    grid-template-columns: 34px minmax(230px, 1.4fr) minmax(150px, .9fr) 112px 92px 112px 46px !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 10px 14px !important;
}

.seller-order-index {
    color: #61584f;
    font-size: 8px;
    font-weight: 600;
    text-align: center;
}

.seller-order-grid .seller-order-image-frame {
    width: 42px !important;
    height: 42px !important;
    border-color: #ece4db !important;
    border-radius: 50% !important;
    background: #f5f2ed !important;
}

.seller-order-grid .seller-order-image-frame + div p:first-child {
    color: #25201b !important;
    font-size: 9px !important;
    font-weight: 700 !important;
}

.seller-order-grid .seller-order-image-frame + div p:nth-child(2) {
    margin-top: 2px !important;
    color: #6f665d !important;
    font-size: 8px !important;
}

.seller-order-grid .seller-order-image-frame + div p:nth-child(3) {
    margin-top: 2px !important;
    color: #9a9085 !important;
    font-size: 7.2px !important;
}

.seller-order-grid > div:nth-child(3) p:not(.lg\:hidden),
.seller-order-grid > div:nth-child(4) span,
.seller-order-grid > div:nth-child(5) p:last-child,
.seller-order-grid > div:nth-child(6) p {
    font-size: 8px !important;
}

.seller-order-grid > div:nth-child(3) p:first-of-type:not(.lg\:hidden) {
    color: #433c35 !important;
    font-weight: 600 !important;
}

.seller-order-grid > div:nth-child(5) p:last-child {
    color: #302a24 !important;
    font-weight: 700 !important;
}

.seller-order-view-button {
    display: grid !important;
    width: 30px !important;
    height: 30px !important;
    place-items: center;
    border: 0 !important;
    border-radius: 8px !important;
    background: transparent !important;
    color: #4f4942 !important;
    transition: background-color .13s ease, color .13s ease, transform .13s ease;
}

.seller-order-view-button:hover {
    transform: translateY(-1px);
    background: #fff4df !important;
    color: #b77808 !important;
}

.seller-order-view-button svg {
    width: 15px !important;
    height: 15px !important;
}

.seller-order-progress {
    background: #d99400 !important;
}

#sellerOrderNoResults {
    border-top: 0 !important;
    padding: 34px 20px !important;
}

@media (min-width: 1024px) and (max-width: 1279px) {
    .seller-orders-table-head,
    .seller-order-grid {
        grid-template-columns: 28px minmax(185px, 1.35fr) minmax(120px, .82fr) 98px 78px 96px 40px !important;
        gap: 7px !important;
        padding-left: 11px !important;
        padding-right: 11px !important;
    }

    .seller-orders-filter-panel {
        grid-template-columns: minmax(240px, 1fr) 158px 116px 58px;
        gap: 8px;
    }
}

@media (max-width: 1023px) {
    .seller-orders-header {
        align-items: flex-start;
    }

    .seller-orders-summary {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .seller-orders-filter-panel {
        grid-template-columns: 1fr 1fr;
    }

    .seller-orders-search-field {
        grid-column: 1 / -1;
    }

    .seller-orders-table-head {
        display: none !important;
    }

    .seller-order-grid {
        grid-template-columns: 28px minmax(0, 1fr) !important;
        align-items: start !important;
        gap: 9px 11px !important;
        padding: 13px !important;
    }

    .seller-order-index {
        grid-row: 1 / span 5;
        padding-top: 4px;
    }

    .seller-order-grid > div:not(.seller-order-index) {
        grid-column: 2;
    }

    .seller-order-grid > div:last-child {
        justify-content: flex-start !important;
    }
}

@media (max-width: 639px) {
    .seller-orders-header {
        flex-direction: column;
        gap: 14px !important;
    }

    .seller-orders-heading-group {
        align-items: flex-start;
    }

    .seller-orders-header-actions {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .seller-orders-header-action {
        width: 100%;
    }

    .seller-orders-summary {
        gap: 8px !important;
    }

    .seller-orders-filter-panel {
        grid-template-columns: 1fr;
    }

    .seller-orders-search-field {
        grid-column: auto;
    }

    .seller-orders-table-titlebar {
        align-items: flex-start;
        flex-direction: column;
        gap: 7px;
    }

    #sellerOrderResultCount {
        white-space: normal;
    }
}



/* ============================================================
   INSTANT DATA PAINT — NO CONTENT GATE
   ============================================================ */
.seller-orders-content {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
}

.seller-order-image-frame::before {
    display: none !important;
    content: none !important;
    animation: none !important;
}

.seller-order-image-frame > img {
    opacity: 1 !important;
    transition: none !important;
}


/* ============================================================
   ORDER MANAGEMENT — APPROVED ACCOUNTS EXACT SIZE PARITY
   Matches the supplied admin reference's visual density at 100% zoom.
   UI only: no route, realtime, filter, modal, or order-action behavior changes.
   ============================================================ */
.seller-orders-page {
    width: 100% !important;
    max-width: 1640px !important;
    margin-inline: auto !important;
    padding-bottom: 18px !important;
    color: #211c16 !important;
}

/* Header parity: 44px icon, 29px title, 11px supporting copy. */
.seller-orders-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 20px !important;
    margin-bottom: 16px !important;
    padding: 0 !important;
}

.seller-orders-heading-group {
    display: flex !important;
    min-width: 0 !important;
    align-items: center !important;
    gap: 13px !important;
}

.seller-orders-header-icon {
    display: grid !important;
    width: 44px !important;
    height: 44px !important;
    flex: 0 0 44px !important;
    place-items: center !important;
    border: 1px solid #eadfc9 !important;
    border-radius: 12px !important;
    background: #fff8eb !important;
    color: #b77c18 !important;
    box-shadow: 0 4px 12px rgba(75,54,25,.045) !important;
}

.seller-orders-header-icon svg {
    width: 17px !important;
    height: 17px !important;
}

.seller-orders-eyebrow {
    margin: 0 !important;
    color: #9a6f23 !important;
    font-size: 8px !important;
    font-weight: 700 !important;
    line-height: 1.15 !important;
    letter-spacing: .13em !important;
    text-transform: uppercase !important;
}

.seller-orders-title {
    margin: 5px 0 0 !important;
    color: #17130f !important;
    font-size: 29px !important;
    font-weight: 700 !important;
    line-height: 1.02 !important;
    letter-spacing: -.045em !important;
}

.seller-orders-title-accent {
    margin-left: 4px !important;
    color: #d99500 !important;
}

.seller-orders-subtitle {
    max-width: 900px !important;
    margin: 7px 0 0 !important;
    color: #7f756a !important;
    font-size: 11px !important;
    font-weight: 400 !important;
    line-height: 1.5 !important;
}

.seller-orders-header-actions {
    display: flex !important;
    flex: 0 0 auto !important;
    flex-wrap: wrap !important;
    justify-content: flex-end !important;
    gap: 8px !important;
}

.seller-orders-header-action {
    display: inline-flex !important;
    height: 42px !important;
    min-height: 42px !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 7px !important;
    border: 1px solid #e6ddd2 !important;
    border-radius: 10px !important;
    background: #fff !important;
    padding: 0 13px !important;
    color: #5f574e !important;
    font-size: 8.5px !important;
    font-weight: 600 !important;
    box-shadow: 0 4px 10px rgba(63,49,32,.045) !important;
    transform: none !important;
}

.seller-orders-header-action:hover {
    border-color: #d8c4a3 !important;
    background: #fffaf2 !important;
    color: #a8731f !important;
    transform: none !important;
}

.seller-orders-header-action svg {
    width: 13px !important;
    height: 13px !important;
}

/* Metric cards: same compact reference proportions. */
.seller-orders-summary {
    margin-top: 0 !important;
    gap: 9px !important;
    grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
}

.seller-orders-summary-card {
    min-width: 0 !important;
    min-height: 78px !important;
    border: 1px solid #e7ddd1 !important;
    border-radius: 13px !important;
    background: #fff !important;
    padding: 11px 13px !important;
    box-shadow: 0 8px 24px rgba(61,43,22,.055) !important;
    contain: paint;
}

.seller-orders-summary-card > div {
    height: 100% !important;
    align-items: flex-start !important;
    gap: 9px !important;
}

.seller-orders-summary-card p:first-child {
    color: #7d746a !important;
    font-size: 8.5px !important;
    font-weight: 500 !important;
    line-height: 1.3 !important;
}

.seller-orders-summary-card p:nth-child(2) {
    margin-top: 3px !important;
    color: #1c1712 !important;
    font-size: 20px !important;
    font-weight: 700 !important;
    line-height: 1 !important;
}

.seller-orders-summary-card p:nth-child(3) {
    margin-top: 6px !important;
    color: #9b9288 !important;
    font-size: 7.5px !important;
    line-height: 1.35 !important;
}

.seller-orders-summary-icon {
    display: grid !important;
    width: 32px !important;
    height: 32px !important;
    flex: 0 0 32px !important;
    place-items: center !important;
    border-radius: 9px !important;
    box-shadow: 0 2px 4px rgba(61,43,22,.02), 0 6px 14px rgba(61,43,22,.035) !important;
}

.seller-orders-summary-icon svg {
    width: 14px !important;
    height: 14px !important;
}

/* Filter bar: 38px controls and 14px outer radius like the reference. */
.seller-orders-filter-panel {
    display: grid !important;
    grid-template-columns: minmax(300px, 1fr) 150px 120px 64px !important;
    gap: 8px !important;
    margin-top: 11px !important;
    padding: 9px !important;
    border: 1px solid #e7ddd1 !important;
    border-radius: 14px !important;
    background: #fff !important;
    box-shadow: 0 8px 24px rgba(61,43,22,.055) !important;
}

.seller-orders-search-field > span {
    left: 12px !important;
    width: 14px !important;
    height: 14px !important;
}

.seller-orders-search-field svg {
    width: 14px !important;
    height: 14px !important;
}

#sellerOrderSearch,
#sellerOrderStatusFilter,
.seller-orders-apply-filter,
.seller-orders-reset-filter {
    height: 38px !important;
    min-height: 38px !important;
    border-radius: 9px !important;
    font-size: 9.5px !important;
}

#sellerOrderSearch,
#sellerOrderStatusFilter {
    border-color: #e8e0d5 !important;
    box-shadow: 0 1px 2px rgba(61,43,22,.018), 0 4px 10px rgba(61,43,22,.025) !important;
}

#sellerOrderSearch {
    padding-left: 36px !important;
    padding-right: 10px !important;
}

#sellerOrderSearch::placeholder {
    font-size: 9.5px !important;
}

#sellerOrderStatusFilter {
    padding-left: 10px !important;
    padding-right: 28px !important;
}

.seller-orders-apply-filter {
    gap: 6px !important;
    padding-inline: 12px !important;
    border-color: #d99500 !important;
    background: #d99500 !important;
    box-shadow: 0 5px 14px rgba(217,149,0,.15) !important;
}

.seller-orders-apply-filter:hover {
    background: #bd8205 !important;
    transform: none !important;
}

.seller-orders-apply-filter svg {
    width: 12px !important;
    height: 12px !important;
}

.seller-orders-reset-filter {
    padding-inline: 11px !important;
    border-color: #e6ddd2 !important;
    background: #fff !important;
    color: #6f665b !important;
    box-shadow: none !important;
}

/* Table: compact flat rows, same density as Approved Accounts. */
.seller-orders-workspace {
    margin-top: 11px !important;
    border: 1px solid #e7ddd1 !important;
    border-radius: 14px !important;
    background: #fff !important;
    box-shadow: 0 8px 24px rgba(61,43,22,.055) !important;
    contain: paint;
}

.seller-orders-table-head {
    display: grid !important;
    grid-template-columns: 36px minmax(230px, 1.35fr) minmax(145px, .85fr) 108px 90px 108px 42px !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 8px 14px !important;
    border-bottom: 1px solid #eee8df !important;
    background: #fcfbf8 !important;
}

.seller-orders-table-head > span {
    color: #847b70 !important;
    font-size: 7.5px !important;
    font-weight: 700 !important;
    line-height: 1.35 !important;
    letter-spacing: .065em !important;
    text-transform: uppercase !important;
}

.seller-orders-table-body {
    padding: 0 !important;
}

.seller-order-row {
    border: 0 !important;
    border-bottom: 1px solid #f0ebe4 !important;
    border-radius: 0 !important;
    background: #fff !important;
    box-shadow: none !important;
    transform: none !important;
    transition: background-color .14s ease !important;
}

.seller-order-row:last-child {
    border-bottom: 0 !important;
}

.seller-order-row:hover {
    border-color: #f0ebe4 !important;
    background: #fdfbf7 !important;
    box-shadow: none !important;
    transform: none !important;
}

.seller-order-grid {
    display: grid !important;
    grid-template-columns: 36px minmax(230px, 1.35fr) minmax(145px, .85fr) 108px 90px 108px 42px !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 9px 14px !important;
}

.seller-order-index {
    color: #5f574e !important;
    font-size: 8px !important;
    font-weight: 600 !important;
    text-align: center !important;
}

.seller-order-grid .seller-order-image-frame {
    width: 30px !important;
    height: 30px !important;
    flex: 0 0 30px !important;
    border-color: #eee8df !important;
    border-radius: 50% !important;
    background: #f3f1ed !important;
}

.seller-order-grid .seller-order-image-frame + div p:first-child {
    color: #2e2924 !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    line-height: 1.35 !important;
}

.seller-order-grid .seller-order-image-frame + div p:nth-child(2) {
    margin-top: 2px !important;
    color: #6f665d !important;
    font-size: 7.8px !important;
    line-height: 1.35 !important;
}

.seller-order-grid .seller-order-image-frame + div p:nth-child(3) {
    margin-top: 1px !important;
    color: #988f84 !important;
    font-size: 7px !important;
    line-height: 1.35 !important;
}

/* Buyer column */
.seller-order-grid > div:nth-child(3) p:not(.lg\:hidden) {
    font-size: 8px !important;
    line-height: 1.4 !important;
}

.seller-order-grid > div:nth-child(3) p:first-of-type:not(.lg\:hidden) {
    color: #514a42 !important;
    font-weight: 600 !important;
}

/* Status pill */
.seller-order-grid > div:nth-child(4) span {
    padding: 3px 7px !important;
    font-size: 7px !important;
    line-height: 1.25 !important;
}

/* Total */
.seller-order-grid > div:nth-child(5) p:last-child {
    color: #302a24 !important;
    font-size: 8px !important;
    font-weight: 700 !important;
}

/* Date */
.seller-order-grid > div:nth-child(6) p:first-of-type:not(.lg\:hidden) {
    color: #514a42 !important;
    font-size: 8px !important;
    font-weight: 500 !important;
}

.seller-order-grid > div:nth-child(6) p:last-child {
    margin-top: 1px !important;
    color: #958c80 !important;
    font-size: 7px !important;
}

.seller-order-view-button {
    display: inline-grid !important;
    width: 26px !important;
    height: 26px !important;
    min-width: 26px !important;
    place-items: center !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    color: #3f3b37 !important;
    box-shadow: none !important;
    transition: color .15s ease, transform .15s ease !important;
}

.seller-order-view-button:hover,
.seller-order-view-button:focus-visible {
    outline: none !important;
    background: transparent !important;
    color: #e09a00 !important;
    transform: translateY(-1px) scale(1.08) !important;
}

.seller-order-view-button svg {
    width: 14px !important;
    height: 14px !important;
}

#sellerOrderNoResults {
    padding: 28px 20px !important;
}

.seller-orders-table-footer {
    display: flex !important;
    min-height: 50px !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    border-top: 1px solid #eee8df !important;
    padding: 10px 14px !important;
    background: #fff !important;
}

#sellerOrderResultCount {
    margin: 0 !important;
    color: #756d63 !important;
    font-size: 8px !important;
    font-weight: 400 !important;
}

.seller-orders-pagination {
    display: flex !important;
    align-items: center !important;
    gap: 7px !important;
}

.seller-orders-pagination button,
.seller-orders-pagination span {
    display: grid !important;
    width: 30px !important;
    min-width: 30px !important;
    height: 30px !important;
    place-items: center !important;
    border-radius: 8px !important;
}

.seller-orders-pagination button {
    border: 1px solid #eee8df !important;
    background: #faf8f4 !important;
    color: #9b9389 !important;
}

.seller-orders-pagination button:disabled {
    cursor: default !important;
    opacity: 1 !important;
}

.seller-orders-pagination button svg {
    width: 12px !important;
    height: 12px !important;
}

.seller-orders-pagination span {
    background: #d99500 !important;
    color: #fff !important;
    font-size: 8px !important;
    font-weight: 700 !important;
    box-shadow: 0 6px 15px rgba(217,149,0,.16) !important;
}

/* Keep page paint immediate and calm. */
.seller-orders-content,
.seller-orders-stage {
    opacity: 1 !important;
    visibility: visible !important;
}

.seller-order-row,
.seller-orders-summary-card,
.seller-orders-filter-panel,
.seller-orders-workspace {
    animation: none !important;
}

/* Laptop parity with the reference's compact height tuning. */
@media (max-height: 850px) and (min-width: 900px) {
    .seller-orders-header {
        margin-bottom: 14px !important;
    }

    .seller-orders-header-icon {
        width: 42px !important;
        height: 42px !important;
        flex-basis: 42px !important;
    }

    .seller-orders-title {
        font-size: 27px !important;
    }

    .seller-orders-subtitle {
        font-size: 10.5px !important;
    }

    .seller-orders-summary-card {
        min-height: 72px !important;
        padding-top: 9px !important;
        padding-bottom: 9px !important;
    }

    .seller-orders-summary-card p:nth-child(2) {
        font-size: 18px !important;
    }
}

@media (min-width: 1024px) and (max-width: 1279px) {
    .seller-orders-table-head,
    .seller-order-grid {
        grid-template-columns: 30px minmax(185px, 1.3fr) minmax(118px, .78fr) 96px 76px 94px 36px !important;
        gap: 6px !important;
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    .seller-orders-filter-panel {
        grid-template-columns: minmax(230px, 1fr) 142px 110px 58px !important;
        gap: 7px !important;
    }
}

@media (max-width: 1023px) {
    .seller-orders-header {
        align-items: flex-start !important;
    }

    .seller-orders-summary {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .seller-orders-filter-panel {
        grid-template-columns: 1fr 1fr !important;
    }

    .seller-orders-search-field {
        grid-column: 1 / -1 !important;
    }

    .seller-orders-table-head {
        display: none !important;
    }

    .seller-order-grid {
        grid-template-columns: 28px minmax(0, 1fr) !important;
        align-items: start !important;
        gap: 9px 11px !important;
        padding: 12px !important;
    }

    .seller-order-index {
        grid-row: 1 / span 6 !important;
        padding-top: 4px !important;
    }

    .seller-order-grid > div:not(.seller-order-index) {
        grid-column: 2 !important;
    }

    .seller-order-grid > div:last-child {
        justify-content: flex-start !important;
    }
}

@media (max-width: 767px) {
    .seller-orders-header {
        flex-direction: column !important;
        gap: 12px !important;
    }

    .seller-orders-heading-group {
        align-items: flex-start !important;
        gap: 11px !important;
    }

    .seller-orders-header-icon {
        width: 40px !important;
        height: 40px !important;
        flex-basis: 40px !important;
        border-radius: 11px !important;
    }

    .seller-orders-title {
        font-size: 24px !important;
    }

    .seller-orders-subtitle {
        font-size: 10px !important;
    }

    .seller-orders-header-actions {
        display: grid !important;
        width: 100% !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .seller-orders-header-action {
        width: 100% !important;
        min-height: 40px !important;
        height: 40px !important;
        font-size: 9px !important;
    }

    .seller-orders-filter-panel {
        grid-template-columns: 1fr !important;
    }

    .seller-orders-search-field {
        grid-column: auto !important;
    }

    #sellerOrderSearch,
    #sellerOrderStatusFilter,
    .seller-orders-apply-filter,
    .seller-orders-reset-filter {
        height: 42px !important;
        min-height: 42px !important;
        font-size: 10px !important;
    }

    .seller-orders-table-footer {
        align-items: flex-start !important;
        flex-direction: column !important;
    }

    .seller-orders-pagination {
        align-self: flex-end !important;
    }
}

/* ============================================================
   ORDER DETAILS — WORKFLOW-FIRST MODAL V3
   Clean marketplace-inspired hierarchy: products + buyer data on
   the left, summary + fulfillment + next action on the right.
   ============================================================ */
#sellerOrderDetailModal {
    padding: 14px !important;
}

.seller-order-workflow-panel {
    width: min(1120px, calc(100vw - 28px)) !important;
    max-width: 1120px !important;
    max-height: calc(100vh - 28px) !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
    border: 1px solid #e6ded3 !important;
    border-radius: 16px !important;
    background: #fff !important;
    box-shadow: 0 24px 70px rgba(35, 27, 19, .18) !important;
}

.seller-order-workflow-header {
    display: flex;
    flex: 0 0 auto;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    min-height: 68px;
    border-bottom: 1px solid #ece6de;
    background: #fff;
    padding: 11px 14px;
}

.seller-order-workflow-header-main {
    display: flex;
    min-width: 0;
    align-items: center;
    gap: 11px;
}

.seller-order-workflow-header-icon,
.seller-order-workflow-section-icon {
    display: grid;
    place-items: center;
    border: 1px solid #eadfc9;
    background: #fff8eb;
    color: #b77c18;
}

.seller-order-workflow-header-icon {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    border-radius: 10px;
}

.seller-order-workflow-header-icon svg {
    width: 16px;
    height: 16px;
}

.seller-order-workflow-title-row {
    display: flex;
    min-width: 0;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
}

.seller-order-workflow-title-row h3 {
    margin: 0;
    color: #211c17;
    font-size: 16px;
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -.025em;
}

.seller-order-workflow-status {
    display: inline-flex;
    min-height: 23px;
    align-items: center;
    border-width: 1px;
    border-style: solid;
    border-radius: 999px;
    padding: 0 8px;
    font-size: 7.5px;
    font-weight: 700;
    white-space: nowrap;
}

.seller-order-workflow-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    margin-top: 5px;
    color: #8c8378;
    font-size: 7.5px;
    line-height: 1.45;
}

.seller-order-workflow-meta strong {
    color: #b77911;
    font-weight: 700;
}

.seller-order-workflow-close {
    display: grid;
    width: 34px;
    height: 34px;
    flex: 0 0 34px;
    place-items: center;
    border: 1px solid #e5ddd2;
    border-radius: 9px;
    background: #fff;
    color: #71685f;
    transition: border-color .14s ease, background-color .14s ease, color .14s ease;
}

.seller-order-workflow-close:hover,
.seller-order-workflow-close:focus-visible {
    outline: none;
    border-color: #d6c7b4;
    background: #faf8f4;
    color: #2f2923;
}

.seller-order-workflow-close svg {
    width: 14px;
    height: 14px;
}

.seller-order-workflow-body {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 340px;
    flex: 1 1 auto;
    min-height: 0;
    gap: 11px;
    overflow: hidden;
    background: #f8f7f4;
    padding: 11px;
}

.seller-order-workflow-main {
    display: flex;
    min-width: 0;
    min-height: 0;
    flex-direction: column;
    gap: 11px;
}

.seller-order-workflow-card {
    min-width: 0;
    border: 1px solid #e7e1d9;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 2px 8px rgba(61, 43, 22, .025);
}

.seller-order-workflow-products {
    display: flex;
    min-height: 0;
    flex: 1 1 auto;
    flex-direction: column;
    padding: 12px;
}

.seller-order-workflow-section-head,
.seller-order-workflow-action-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.seller-order-workflow-section-title {
    display: flex;
    min-width: 0;
    align-items: center;
    gap: 9px;
}

.seller-order-workflow-section-icon {
    width: 29px;
    height: 29px;
    flex: 0 0 29px;
    border-radius: 8px;
}

.seller-order-workflow-section-icon svg {
    width: 13px;
    height: 13px;
}

.seller-order-workflow-section-title h4,
.seller-order-workflow-action-heading h4 {
    margin: 0;
    color: #342e28;
    font-size: 9.5px;
    font-weight: 700;
    line-height: 1.3;
}

.seller-order-workflow-section-title p {
    margin: 2px 0 0;
    color: #978e83;
    font-size: 6.8px;
    line-height: 1.35;
}

.seller-order-workflow-product-list {
    display: grid;
    min-height: 0;
    max-height: 300px;
    gap: 7px;
    margin-top: 10px;
    overflow-y: auto;
    padding-right: 3px;
}

.seller-order-workflow-product-row {
    display: grid;
    grid-template-columns: 54px minmax(0, 1fr) auto;
    align-items: center;
    gap: 10px;
    min-height: 68px;
    border: 1px solid #ece5dd;
    border-radius: 10px;
    background: #fdfcf9;
    padding: 7px 9px 7px 7px;
}

.seller-order-workflow-product-image {
    position: relative;
    width: 54px;
    height: 54px;
    overflow: hidden;
    border: 1px solid #e8e1d8;
    border-radius: 9px;
    background: #f4f1ec;
}

.seller-order-workflow-image-fallback {
    display: grid;
    width: 100%;
    height: 100%;
    place-items: center;
    color: #aaa197;
}

.seller-order-workflow-image-fallback svg {
    width: 16px;
    height: 16px;
}

.seller-order-workflow-product-copy {
    min-width: 0;
}

.seller-order-workflow-product-name {
    overflow: hidden;
    margin: 0;
    color: #3b342e;
    font-size: 8.5px;
    font-weight: 650;
    line-height: 1.35;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.seller-order-workflow-product-meta {
    overflow: hidden;
    margin: 4px 0 0;
    color: #968d82;
    font-size: 7px;
    line-height: 1.35;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.seller-order-workflow-product-price {
    margin: 0;
    color: #ad7410;
    font-size: 8px;
    font-weight: 700;
    white-space: nowrap;
}

.seller-order-workflow-empty {
    border: 1px dashed #ded6cc;
    border-radius: 10px;
    background: #fcfbf8;
    padding: 22px;
    color: #91887d;
    font-size: 8px;
    text-align: center;
}

.seller-order-workflow-info-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 11px;
}

.seller-order-workflow-info-card {
    padding: 12px;
}

.seller-order-workflow-details {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px 12px;
    margin: 12px 0 0;
}

.seller-order-workflow-details div {
    min-width: 0;
}

.seller-order-workflow-detail-wide {
    grid-column: 1 / -1;
}

.seller-order-workflow-details dt {
    margin: 0;
    color: #a0978d;
    font-size: 6.2px;
    font-weight: 700;
    letter-spacing: .08em;
    line-height: 1.3;
    text-transform: uppercase;
}

.seller-order-workflow-details dd {
    margin: 4px 0 0;
    color: #514a42;
    font-size: 7.8px;
    font-weight: 500;
    line-height: 1.5;
}

.seller-order-workflow-sidebar {
    display: grid;
    min-width: 0;
    min-height: 0;
    align-content: start;
    gap: 11px;
    overflow-y: auto;
    padding-right: 2px;
}

.seller-order-workflow-summary,
.seller-order-workflow-progress-card,
.seller-order-workflow-actions {
    padding: 12px;
}

.seller-order-workflow-money-lines {
    display: grid;
    gap: 8px;
    margin-top: 12px;
}

.seller-order-workflow-money-lines div {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    color: #837a70;
    font-size: 7.5px;
}

.seller-order-workflow-money-lines strong {
    color: #4a433c;
    font-size: 7.8px;
    font-weight: 650;
}

.seller-order-workflow-total {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px;
    margin-top: 10px;
    border-top: 1px solid #eee8df;
    padding-top: 10px;
}

.seller-order-workflow-total span {
    color: #3d3731;
    font-size: 8.5px;
    font-weight: 700;
}

.seller-order-workflow-total strong {
    color: #d48e05;
    font-size: 17px;
    font-weight: 750;
    line-height: 1;
    letter-spacing: -.035em;
}

.seller-order-workflow-progress-percent {
    color: #a66f10;
    font-size: 8px;
    font-weight: 700;
}

.seller-order-workflow-progress-bar {
    height: 5px;
    overflow: hidden;
    margin-top: 11px;
    border-radius: 999px;
    background: #ece7df;
}

.seller-order-workflow-progress-bar span {
    display: block;
    height: 100%;
    border-radius: inherit;
    background: #d99500;
}

.seller-order-workflow-timeline {
    display: grid;
    gap: 0;
    margin: 11px 0 0;
    padding: 0;
    list-style: none;
}

.seller-order-workflow-timeline li {
    position: relative;
    display: grid;
    grid-template-columns: 24px minmax(0, 1fr);
    min-height: 31px;
    align-items: start;
    gap: 7px;
    color: #9b9288;
    font-size: 7.2px;
    font-weight: 500;
}

.seller-order-workflow-timeline li:not(:last-child)::after {
    content: "";
    position: absolute;
    left: 10px;
    top: 18px;
    bottom: -2px;
    width: 1px;
    background: #e4ddd5;
}

.seller-order-workflow-timeline li.is-complete {
    color: #62584d;
}

.seller-order-workflow-timeline li.is-complete:not(:last-child)::after {
    background: #e1bd6b;
}

.seller-order-workflow-timeline-dot {
    position: relative;
    z-index: 1;
    display: grid;
    width: 21px;
    height: 21px;
    place-items: center;
    border: 1px solid #e4ddd5;
    border-radius: 999px;
    background: #faf8f5;
    color: #aaa198;
}

.seller-order-workflow-timeline li.is-complete .seller-order-workflow-timeline-dot {
    border-color: #e1bd6b;
    background: #fff8e9;
    color: #a97416;
}

.seller-order-workflow-timeline-dot svg {
    width: 10px;
    height: 10px;
}

.seller-order-workflow-timeline-dot > span {
    width: 4px;
    height: 4px;
    border-radius: 999px;
    background: currentColor;
}

.seller-order-workflow-action-heading {
    align-items: flex-start;
}

.seller-order-workflow-action-heading > div > p {
    margin: 0;
    color: #9a7b43;
    font-size: 6.2px;
    font-weight: 700;
    letter-spacing: .1em;
    line-height: 1.2;
    text-transform: uppercase;
}

.seller-order-workflow-action-heading h4 {
    margin-top: 4px;
    font-size: 10px;
}

.seller-order-workflow-primary-action {
    display: inline-flex;
    width: 100%;
    min-height: 39px;
    align-items: center;
    justify-content: center;
    gap: 7px;
    margin-top: 11px;
    border: 1px solid #d99500;
    border-radius: 9px;
    background: #d99500;
    padding: 0 12px;
    color: #fff;
    font-size: 8px;
    font-weight: 700;
    box-shadow: 0 6px 16px rgba(217, 149, 0, .14);
    transition: background-color .14s ease, border-color .14s ease, transform .14s ease;
}

.seller-order-workflow-primary-action:hover,
.seller-order-workflow-primary-action:focus-visible {
    outline: none;
    transform: translateY(-1px);
    border-color: #bd8205;
    background: #bd8205;
}

.seller-order-workflow-primary-action--success {
    border-color: #56856a;
    background: #56856a;
    box-shadow: 0 6px 16px rgba(86, 133, 106, .13);
}

.seller-order-workflow-primary-action--success:hover,
.seller-order-workflow-primary-action--success:focus-visible {
    border-color: #467257;
    background: #467257;
}

.seller-order-workflow-primary-action svg,
.seller-order-workflow-secondary-actions svg {
    width: 13px;
    height: 13px;
}

.seller-order-workflow-passive-action {
    margin-top: 11px;
    border-width: 1px;
    border-style: solid;
    border-radius: 9px;
    padding: 10px 12px;
    font-size: 7.8px;
    font-weight: 700;
    text-align: center;
}

.seller-order-workflow-secondary-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 7px;
    margin-top: 7px;
}

.seller-order-workflow-secondary-actions a {
    display: inline-flex;
    min-height: 35px;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border: 1px solid #e3dbd0;
    border-radius: 8px;
    background: #fff;
    padding: 0 9px;
    color: #675f56;
    font-size: 7.2px;
    font-weight: 650;
    text-decoration: none;
    transition: border-color .14s ease, background-color .14s ease, color .14s ease;
}

.seller-order-workflow-secondary-actions a:hover,
.seller-order-workflow-secondary-actions a:focus-visible {
    outline: none;
    border-color: #d6bf95;
    background: #fffaf1;
    color: #9a6812;
}

@media (max-height: 760px) and (min-width: 1024px) {
    .seller-order-workflow-header {
        min-height: 60px;
        padding-top: 9px;
        padding-bottom: 9px;
    }

    .seller-order-workflow-body {
        gap: 8px;
        padding: 8px;
    }

    .seller-order-workflow-main,
    .seller-order-workflow-sidebar,
    .seller-order-workflow-info-grid {
        gap: 8px;
    }

    .seller-order-workflow-product-list {
        max-height: 222px;
    }

    .seller-order-workflow-product-row {
        min-height: 60px;
    }

    .seller-order-workflow-product-image {
        width: 46px;
        height: 46px;
    }

    .seller-order-workflow-product-row {
        grid-template-columns: 46px minmax(0, 1fr) auto;
    }

    .seller-order-workflow-products,
    .seller-order-workflow-info-card,
    .seller-order-workflow-summary,
    .seller-order-workflow-progress-card,
    .seller-order-workflow-actions {
        padding: 10px;
    }

    .seller-order-workflow-timeline li {
        min-height: 27px;
    }
}

@media (max-width: 1023px) {
    #sellerOrderDetailModal {
        align-items: flex-start !important;
        overflow-y: auto;
        padding: 8px !important;
    }

    .seller-order-workflow-panel {
        width: 100% !important;
        max-height: none !important;
        overflow: visible !important;
    }

    .seller-order-workflow-body {
        grid-template-columns: 1fr;
        overflow: visible;
    }

    .seller-order-workflow-main,
    .seller-order-workflow-sidebar {
        overflow: visible;
    }

    .seller-order-workflow-product-list {
        max-height: 300px;
    }

    .seller-order-workflow-sidebar {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .seller-order-workflow-actions {
        grid-column: 1 / -1;
    }
}

@media (max-width: 639px) {
    .seller-order-workflow-header {
        align-items: flex-start;
    }

    .seller-order-workflow-info-grid,
    .seller-order-workflow-sidebar,
    .seller-order-workflow-secondary-actions {
        grid-template-columns: 1fr;
    }

    .seller-order-workflow-product-row {
        grid-template-columns: 48px minmax(0, 1fr);
    }

    .seller-order-workflow-product-image {
        width: 48px;
        height: 48px;
    }

    .seller-order-workflow-product-price {
        grid-column: 2;
        margin-top: -2px;
    }

    .seller-order-workflow-meta {
        gap: 4px;
    }
}


/* ============================================================
   ORDER DETAILS — GOLD PROFESSIONAL FINAL LAYER
   Reference-matched proportions: spacious primary work area,
   compact operational sidebar, consistent button sizing.
   Visual-only override; routes/forms/realtime remain untouched.
   ============================================================ */
:root {
    --sari-order-gold: #d89208;
    --sari-order-gold-hover: #c58200;
    --sari-order-gold-soft: #fff8ea;
    --sari-order-gold-border: #ead9b8;
    --sari-order-ink: #26211d;
    --sari-order-muted: #81786f;
    --sari-order-border: #e8dfd3;
    --sari-order-surface: #ffffff;
    --sari-order-canvas: #faf8f4;
}

#sellerOrderDetailModal {
    padding: 12px !important;
    background: rgba(27, 22, 16, .34) !important;
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
}

.seller-order-workflow-panel {
    width: min(1216px, calc(100vw - 24px)) !important;
    max-width: 1216px !important;
    height: min(804px, calc(100vh - 24px)) !important;
    max-height: calc(100vh - 24px) !important;
    border: 1px solid var(--sari-order-border) !important;
    border-radius: 17px !important;
    background: var(--sari-order-surface) !important;
    box-shadow: 0 30px 90px rgba(31, 24, 17, .20) !important;
}

.seller-order-workflow-header {
    min-height: 88px !important;
    gap: 18px !important;
    border-bottom-color: #eee7de !important;
    padding: 15px 22px !important;
}

.seller-order-workflow-header-main {
    gap: 16px !important;
}

.seller-order-workflow-header-icon {
    width: 48px !important;
    height: 48px !important;
    flex-basis: 48px !important;
    border-color: var(--sari-order-gold-border) !important;
    border-radius: 12px !important;
    background: var(--sari-order-gold-soft) !important;
    color: var(--sari-order-gold) !important;
}

.seller-order-workflow-header-icon svg {
    width: 19px !important;
    height: 19px !important;
}

.seller-order-workflow-title-row {
    gap: 10px !important;
}

.seller-order-workflow-title-row h3 {
    color: #201b17 !important;
    font-size: 24px !important;
    font-weight: 700 !important;
    letter-spacing: -.035em !important;
}

.seller-order-workflow-status {
    min-height: 27px !important;
    padding-inline: 10px !important;
    font-size: 9px !important;
}

.seller-order-workflow-meta {
    gap: 8px !important;
    margin-top: 7px !important;
    color: #8b8278 !important;
    font-size: 9px !important;
}

.seller-order-workflow-meta strong {
    color: var(--sari-order-gold) !important;
    font-weight: 700 !important;
}

.seller-order-workflow-close {
    width: 42px !important;
    height: 42px !important;
    flex-basis: 42px !important;
    border-color: #e5ddd2 !important;
    border-radius: 11px !important;
    color: #655d54 !important;
}

.seller-order-workflow-close svg {
    width: 17px !important;
    height: 17px !important;
}

.seller-order-workflow-body {
    grid-template-columns: minmax(0, 1.72fr) minmax(320px, .78fr) !important;
    gap: 13px !important;
    background: var(--sari-order-canvas) !important;
    padding: 13px !important;
}

.seller-order-workflow-main,
.seller-order-workflow-sidebar,
.seller-order-workflow-info-grid {
    gap: 13px !important;
}

.seller-order-workflow-card {
    border-color: var(--sari-order-border) !important;
    border-radius: 13px !important;
    box-shadow: 0 3px 11px rgba(56, 42, 27, .035) !important;
}

.seller-order-workflow-products {
    padding: 15px !important;
}

.seller-order-workflow-info-card,
.seller-order-workflow-summary,
.seller-order-workflow-progress-card,
.seller-order-workflow-actions {
    padding: 14px !important;
}

.seller-order-workflow-section-title {
    gap: 10px !important;
}

.seller-order-workflow-section-icon,
.seller-order-workflow-action-icon {
    display: grid;
    width: 34px !important;
    height: 34px !important;
    flex: 0 0 34px !important;
    place-items: center;
    border: 1px solid var(--sari-order-gold-border) !important;
    border-radius: 9px !important;
    background: var(--sari-order-gold-soft) !important;
    color: var(--sari-order-gold) !important;
}

.seller-order-workflow-section-icon svg,
.seller-order-workflow-action-icon svg {
    width: 15px !important;
    height: 15px !important;
}

.seller-order-workflow-section-title h4,
.seller-order-workflow-action-heading h4 {
    color: #37302a !important;
    font-size: 11px !important;
    font-weight: 700 !important;
}

.seller-order-workflow-section-title p {
    margin-top: 2px !important;
    font-size: 7.5px !important;
}

.seller-order-workflow-product-list {
    max-height: 325px !important;
    gap: 9px !important;
    margin-top: 13px !important;
}

.seller-order-workflow-product-row {
    grid-template-columns: 64px minmax(0, 1fr) auto !important;
    min-height: 84px !important;
    gap: 13px !important;
    border-color: #ebe1d4 !important;
    border-radius: 12px !important;
    background: #fffdfa !important;
    padding: 9px 13px 9px 9px !important;
}

.seller-order-workflow-product-image {
    width: 64px !important;
    height: 64px !important;
    border-radius: 10px !important;
}

.seller-order-workflow-product-name {
    color: #332d27 !important;
    font-size: 10px !important;
    font-weight: 700 !important;
}

.seller-order-workflow-product-meta {
    margin-top: 5px !important;
    font-size: 8px !important;
}

.seller-order-workflow-product-price {
    color: var(--sari-order-gold) !important;
    font-size: 10px !important;
    font-weight: 750 !important;
}

.seller-order-workflow-info-grid {
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
}

.seller-order-workflow-details {
    gap: 13px 16px !important;
    margin-top: 14px !important;
}

.seller-order-workflow-details dt {
    color: #9b9186 !important;
    font-size: 6.9px !important;
}

.seller-order-workflow-details dd {
    margin-top: 5px !important;
    color: #4a433c !important;
    font-size: 8.7px !important;
    line-height: 1.55 !important;
}

.seller-order-workflow-sidebar {
    gap: 11px !important;
    padding-right: 0 !important;
}

.seller-order-workflow-money-lines {
    gap: 9px !important;
    margin-top: 13px !important;
}

.seller-order-workflow-money-lines div {
    font-size: 8.5px !important;
}

.seller-order-workflow-money-lines strong {
    font-size: 9px !important;
}

.seller-order-workflow-total {
    margin-top: 12px !important;
    padding-top: 12px !important;
}

.seller-order-workflow-total span {
    font-size: 10px !important;
}

.seller-order-workflow-total strong {
    color: var(--sari-order-gold) !important;
    font-size: 22px !important;
    font-weight: 800 !important;
}

.seller-order-workflow-progress-percent {
    color: var(--sari-order-gold) !important;
    font-size: 9.5px !important;
}

.seller-order-workflow-progress-bar {
    height: 6px !important;
    margin-top: 13px !important;
    background: #ebe6de !important;
}

.seller-order-workflow-progress-bar span {
    background: var(--sari-order-gold) !important;
}

.seller-order-workflow-timeline {
    margin-top: 14px !important;
}

.seller-order-workflow-timeline li {
    grid-template-columns: 29px minmax(0, 1fr) !important;
    min-height: 35px !important;
    gap: 9px !important;
    font-size: 8.5px !important;
}

.seller-order-workflow-timeline li:not(:last-child)::after {
    left: 12px !important;
    top: 22px !important;
    bottom: -1px !important;
}

.seller-order-workflow-timeline-dot {
    width: 26px !important;
    height: 26px !important;
}

.seller-order-workflow-timeline li.is-complete .seller-order-workflow-timeline-dot {
    border-color: #e1b451 !important;
    background: var(--sari-order-gold-soft) !important;
    color: var(--sari-order-gold) !important;
}

.seller-order-workflow-action-heading {
    display: grid !important;
    grid-template-columns: 34px minmax(0, 1fr) !important;
    align-items: start !important;
    justify-content: initial !important;
    gap: 10px !important;
}

.seller-order-workflow-action-heading > div > p {
    color: #a26d0e !important;
    font-size: 6.8px !important;
    letter-spacing: .11em !important;
}

.seller-order-workflow-action-heading h4 {
    margin-top: 4px !important;
    font-size: 13px !important;
    letter-spacing: -.02em !important;
}

.seller-order-workflow-action-heading small {
    display: block;
    margin-top: 4px;
    color: #948a7e;
    font-size: 7.6px;
    font-weight: 400;
    line-height: 1.45;
}

.seller-order-workflow-primary-action {
    min-height: 46px !important;
    gap: 8px !important;
    margin-top: 14px !important;
    border-color: var(--sari-order-gold) !important;
    border-radius: 10px !important;
    background: var(--sari-order-gold) !important;
    padding: 0 16px !important;
    font-size: 9.5px !important;
    box-shadow: 0 8px 20px rgba(216, 146, 8, .17) !important;
}

.seller-order-workflow-primary-action:hover,
.seller-order-workflow-primary-action:focus-visible {
    border-color: var(--sari-order-gold-hover) !important;
    background: var(--sari-order-gold-hover) !important;
}

.seller-order-workflow-primary-action--success {
    border-color: #56856a !important;
    background: #56856a !important;
    box-shadow: 0 8px 20px rgba(86, 133, 106, .14) !important;
}

.seller-order-workflow-primary-action--success:hover,
.seller-order-workflow-primary-action--success:focus-visible {
    border-color: #467257 !important;
    background: #467257 !important;
}

.seller-order-workflow-primary-action svg,
.seller-order-workflow-secondary-actions svg {
    width: 15px !important;
    height: 15px !important;
}

.seller-order-workflow-secondary-actions {
    gap: 8px !important;
    margin-top: 9px !important;
}

.seller-order-workflow-secondary-actions a {
    min-height: 42px !important;
    gap: 7px !important;
    border-color: #e2d8cb !important;
    border-radius: 10px !important;
    padding: 0 12px !important;
    color: #625a51 !important;
    font-size: 8.6px !important;
    font-weight: 650 !important;
}

.seller-order-workflow-secondary-actions a:hover,
.seller-order-workflow-secondary-actions a:focus-visible {
    border-color: #d9bd83 !important;
    background: var(--sari-order-gold-soft) !important;
    color: #9a650b !important;
}

.seller-order-workflow-passive-action {
    margin-top: 14px !important;
    border-radius: 10px !important;
    padding: 12px 14px !important;
    font-size: 8.8px !important;
}

@media (max-height: 820px) and (min-width: 1024px) {
    .seller-order-workflow-panel {
        height: calc(100vh - 20px) !important;
        max-height: calc(100vh - 20px) !important;
    }

    .seller-order-workflow-header {
        min-height: 74px !important;
        padding: 11px 17px !important;
    }

    .seller-order-workflow-header-icon {
        width: 42px !important;
        height: 42px !important;
        flex-basis: 42px !important;
    }

    .seller-order-workflow-title-row h3 {
        font-size: 20px !important;
    }

    .seller-order-workflow-body {
        gap: 9px !important;
        padding: 9px !important;
    }

    .seller-order-workflow-main,
    .seller-order-workflow-sidebar,
    .seller-order-workflow-info-grid {
        gap: 9px !important;
    }

    .seller-order-workflow-products,
    .seller-order-workflow-info-card,
    .seller-order-workflow-summary,
    .seller-order-workflow-progress-card,
    .seller-order-workflow-actions {
        padding: 10px !important;
    }

    .seller-order-workflow-product-list {
        max-height: 250px !important;
        margin-top: 9px !important;
    }

    .seller-order-workflow-product-row {
        grid-template-columns: 50px minmax(0, 1fr) auto !important;
        min-height: 64px !important;
        padding: 6px 9px 6px 6px !important;
    }

    .seller-order-workflow-product-image {
        width: 50px !important;
        height: 50px !important;
    }

    .seller-order-workflow-details {
        gap: 8px 10px !important;
        margin-top: 9px !important;
    }

    .seller-order-workflow-timeline {
        margin-top: 8px !important;
    }

    .seller-order-workflow-timeline li {
        min-height: 28px !important;
    }

    .seller-order-workflow-primary-action {
        min-height: 40px !important;
        margin-top: 9px !important;
    }

    .seller-order-workflow-secondary-actions a {
        min-height: 37px !important;
    }
}

@media (max-width: 1023px) {
    .seller-order-workflow-panel {
        height: auto !important;
    }

    .seller-order-workflow-body {
        grid-template-columns: 1fr !important;
    }

    .seller-order-workflow-sidebar {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .seller-order-workflow-actions {
        grid-column: 1 / -1 !important;
    }
}

@media (max-width: 639px) {
    .seller-order-workflow-header {
        min-height: 72px !important;
        padding: 11px 12px !important;
    }

    .seller-order-workflow-header-icon {
        width: 40px !important;
        height: 40px !important;
        flex-basis: 40px !important;
    }

    .seller-order-workflow-title-row h3 {
        font-size: 18px !important;
    }

    .seller-order-workflow-info-grid,
    .seller-order-workflow-sidebar,
    .seller-order-workflow-secondary-actions {
        grid-template-columns: 1fr !important;
    }

    .seller-order-workflow-product-row {
        grid-template-columns: 50px minmax(0, 1fr) !important;
    }

    .seller-order-workflow-product-image {
        width: 50px !important;
        height: 50px !important;
    }

    .seller-order-workflow-product-price {
        grid-column: 2 !important;
    }
}

/* ============================================================
   ORDER MANAGEMENT — COMPACT A4-LIKE FINAL PASS
   One clean main information surface + compact operational sidebar.
   Keeps all order actions/realtime logic untouched.
   ============================================================ */

/* Readability: slightly larger summary and order-list typography. */
.seller-orders-summary-card {
    min-height: 82px !important;
    padding: 11px 13px !important;
}

.seller-orders-summary-card p:first-child {
    font-size: 10px !important;
    line-height: 1.3 !important;
    font-weight: 600 !important;
}

.seller-orders-summary-card p:nth-child(2) {
    margin-top: 4px !important;
    font-size: 22px !important;
    line-height: 1 !important;
}

.seller-orders-summary-card p:nth-child(3) {
    margin-top: 6px !important;
    font-size: 8.5px !important;
    line-height: 1.35 !important;
}

.seller-orders-summary-icon {
    width: 34px !important;
    height: 34px !important;
}

.seller-orders-table-head > span {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    letter-spacing: .055em !important;
}

.seller-order-index {
    font-size: 9.5px !important;
    font-weight: 650 !important;
}

.seller-order-grid .seller-order-image-frame + div p:first-child {
    font-size: 11.5px !important;
    line-height: 1.3 !important;
}

.seller-order-grid .seller-order-image-frame + div p:nth-child(2) {
    font-size: 10px !important;
    line-height: 1.35 !important;
}

.seller-order-grid .seller-order-image-frame + div p:nth-child(3) {
    font-size: 9px !important;
    line-height: 1.35 !important;
}

.seller-order-grid > div:nth-child(3) p:first-of-type:not(.lg\:hidden) {
    font-size: 10.5px !important;
    font-weight: 600 !important;
}

.seller-order-grid > div:nth-child(3) p:last-child {
    font-size: 9.2px !important;
}

.seller-order-grid > div:nth-child(4) span {
    font-size: 9px !important;
    font-weight: 600 !important;
}

.seller-order-grid > div:nth-child(5) p:last-child {
    font-size: 11px !important;
    font-weight: 700 !important;
}

.seller-order-grid > div:nth-child(6) p:first-of-type:not(.lg\:hidden) {
    font-size: 9.8px !important;
    font-weight: 600 !important;
}

.seller-order-grid > div:nth-child(6) p:last-child {
    font-size: 8.8px !important;
}

/* Compact, document-like modal proportions. */
#sellerOrderDetailModal {
    padding: 10px !important;
}

.seller-order-workflow-panel {
    width: min(1000px, calc(100vw - 20px)) !important;
    max-width: 1000px !important;
    height: auto !important;
    max-height: calc(100vh - 20px) !important;
    border-radius: 16px !important;
    box-shadow: 0 24px 72px rgba(31, 24, 17, .18) !important;
}

.seller-order-workflow-header {
    min-height: 70px !important;
    gap: 12px !important;
    padding: 10px 16px !important;
}

.seller-order-workflow-header-main {
    gap: 11px !important;
}

.seller-order-workflow-header-icon {
    width: 40px !important;
    height: 40px !important;
    flex-basis: 40px !important;
    border-radius: 10px !important;
}

.seller-order-workflow-header-icon svg {
    width: 16px !important;
    height: 16px !important;
}

.seller-order-workflow-title-row {
    gap: 8px !important;
}

.seller-order-workflow-title-row h3 {
    font-size: 20px !important;
    line-height: 1.1 !important;
}

.seller-order-workflow-status {
    min-height: 24px !important;
    padding-inline: 9px !important;
    font-size: 8px !important;
}

.seller-order-workflow-meta {
    gap: 6px !important;
    margin-top: 5px !important;
    font-size: 8px !important;
}

.seller-order-workflow-close {
    width: 38px !important;
    height: 38px !important;
    flex-basis: 38px !important;
    border-radius: 10px !important;
}

.seller-order-workflow-close svg {
    width: 15px !important;
    height: 15px !important;
}

.seller-order-workflow-body {
    grid-template-columns: minmax(0, 1.62fr) minmax(285px, .78fr) !important;
    align-items: start !important;
    gap: 10px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    padding: 10px !important;
}

/* LEFT: visually merge Products + Buyer/Delivery + Delivery/Payment into one surface. */
.seller-order-workflow-main {
    display: block !important;
    min-height: 0 !important;
    overflow: hidden !important;
    border: 1px solid var(--sari-order-border) !important;
    border-radius: 13px !important;
    background: #fff !important;
    box-shadow: 0 3px 11px rgba(56, 42, 27, .035) !important;
}

.seller-order-workflow-main > .seller-order-workflow-products,
.seller-order-workflow-main .seller-order-workflow-info-card {
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}

.seller-order-workflow-main > .seller-order-workflow-products {
    display: block !important;
    padding: 12px !important;
}

.seller-order-workflow-info-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 0 !important;
    border-top: 1px solid #eee7de !important;
}

.seller-order-workflow-info-card {
    padding: 12px !important;
}

.seller-order-workflow-info-card + .seller-order-workflow-info-card {
    border-left: 1px solid #eee7de !important;
}

.seller-order-workflow-section-title {
    gap: 8px !important;
}

.seller-order-workflow-section-icon,
.seller-order-workflow-action-icon {
    width: 30px !important;
    height: 30px !important;
    flex-basis: 30px !important;
    border-radius: 8px !important;
}

.seller-order-workflow-section-icon svg,
.seller-order-workflow-action-icon svg {
    width: 13px !important;
    height: 13px !important;
}

.seller-order-workflow-section-title h4,
.seller-order-workflow-action-heading h4 {
    font-size: 10.5px !important;
}

.seller-order-workflow-section-title p {
    font-size: 7px !important;
}

/* Product list: show the full image, not a cropped thumbnail, and avoid internal clipping. */
.seller-order-workflow-product-list {
    max-height: none !important;
    gap: 7px !important;
    margin-top: 10px !important;
    overflow: visible !important;
    padding-right: 0 !important;
}

.seller-order-workflow-product-row {
    grid-template-columns: 50px minmax(0, 1fr) auto !important;
    min-height: 62px !important;
    gap: 10px !important;
    border-radius: 10px !important;
    padding: 6px 10px 6px 6px !important;
}

.seller-order-workflow-product-image {
    display: grid !important;
    width: 50px !important;
    height: 50px !important;
    place-items: center !important;
    overflow: hidden !important;
    border-radius: 9px !important;
    background: #fff !important;
    padding: 2px !important;
}

.seller-order-workflow-product-image > img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    object-position: center !important;
    border-radius: 7px !important;
}

.seller-order-workflow-product-name {
    font-size: 9.5px !important;
}

.seller-order-workflow-product-meta {
    margin-top: 3px !important;
    font-size: 7.7px !important;
}

.seller-order-workflow-product-price {
    font-size: 9.5px !important;
}

.seller-order-workflow-details {
    gap: 9px 12px !important;
    margin-top: 10px !important;
}

.seller-order-workflow-details dt {
    font-size: 6.8px !important;
}

.seller-order-workflow-details dd {
    margin-top: 3px !important;
    font-size: 8.3px !important;
    line-height: 1.45 !important;
}

/* RIGHT: compact operational cards. */
.seller-order-workflow-sidebar {
    gap: 9px !important;
    overflow: visible !important;
}

.seller-order-workflow-summary,
.seller-order-workflow-progress-card,
.seller-order-workflow-actions {
    padding: 11px !important;
}

.seller-order-workflow-money-lines {
    gap: 6px !important;
    margin-top: 9px !important;
}

.seller-order-workflow-money-lines div {
    font-size: 8px !important;
}

.seller-order-workflow-money-lines strong {
    font-size: 8.5px !important;
}

.seller-order-workflow-total {
    margin-top: 9px !important;
    padding-top: 9px !important;
}

.seller-order-workflow-total span {
    font-size: 9px !important;
}

.seller-order-workflow-total strong {
    font-size: 19px !important;
}

.seller-order-workflow-progress-bar {
    height: 5px !important;
    margin-top: 9px !important;
}

.seller-order-workflow-timeline {
    margin-top: 9px !important;
}

.seller-order-workflow-timeline li {
    grid-template-columns: 25px minmax(0, 1fr) !important;
    min-height: 29px !important;
    gap: 8px !important;
    font-size: 8px !important;
}

.seller-order-workflow-timeline-dot {
    width: 23px !important;
    height: 23px !important;
}

.seller-order-workflow-timeline li:not(:last-child)::after {
    left: 10px !important;
    top: 20px !important;
    bottom: -1px !important;
}

.seller-order-workflow-action-heading {
    grid-template-columns: 30px minmax(0, 1fr) !important;
    gap: 8px !important;
}

.seller-order-workflow-action-heading > div > p {
    font-size: 6.5px !important;
}

.seller-order-workflow-action-heading h4 {
    margin-top: 2px !important;
    font-size: 11.5px !important;
}

.seller-order-workflow-action-heading small {
    margin-top: 2px !important;
    font-size: 7px !important;
}

.seller-order-workflow-primary-action,
.seller-order-workflow-primary-action--success {
    min-height: 40px !important;
    margin-top: 10px !important;
    border-color: var(--sari-order-gold) !important;
    background: var(--sari-order-gold) !important;
    color: #fff !important;
    font-size: 8.7px !important;
    box-shadow: 0 6px 15px rgba(216, 146, 8, .15) !important;
}

.seller-order-workflow-primary-action:hover,
.seller-order-workflow-primary-action:focus-visible,
.seller-order-workflow-primary-action--success:hover,
.seller-order-workflow-primary-action--success:focus-visible {
    border-color: var(--sari-order-gold-hover) !important;
    background: var(--sari-order-gold-hover) !important;
}

.seller-order-workflow-secondary-actions {
    gap: 7px !important;
    margin-top: 7px !important;
}

.seller-order-workflow-secondary-actions a {
    min-height: 36px !important;
    border-radius: 9px !important;
    padding-inline: 9px !important;
    font-size: 7.8px !important;
}

.seller-order-workflow-primary-action svg,
.seller-order-workflow-secondary-actions svg {
    width: 13px !important;
    height: 13px !important;
}

/* On short laptop screens the whole body scrolls once; product cards themselves do not clip. */
@media (max-height: 760px) and (min-width: 1024px) {
    .seller-order-workflow-panel {
        max-height: calc(100vh - 12px) !important;
    }

    .seller-order-workflow-header {
        min-height: 62px !important;
        padding-block: 8px !important;
    }

    .seller-order-workflow-body {
        padding: 8px !important;
        gap: 8px !important;
    }

    .seller-order-workflow-product-row {
        min-height: 58px !important;
    }

    .seller-order-workflow-product-image {
        width: 46px !important;
        height: 46px !important;
    }
}

@media (max-width: 1023px) {
    .seller-order-workflow-panel {
        width: calc(100vw - 14px) !important;
        max-height: calc(100vh - 14px) !important;
    }

    .seller-order-workflow-body {
        grid-template-columns: 1fr !important;
    }

    .seller-order-workflow-sidebar {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .seller-order-workflow-actions {
        grid-column: 1 / -1 !important;
    }
}

@media (max-width: 639px) {
    .seller-order-workflow-header {
        min-height: 62px !important;
        padding: 9px 10px !important;
    }

    .seller-order-workflow-title-row h3 {
        font-size: 17px !important;
    }

    .seller-order-workflow-info-grid,
    .seller-order-workflow-sidebar,
    .seller-order-workflow-secondary-actions {
        grid-template-columns: 1fr !important;
    }

    .seller-order-workflow-info-card + .seller-order-workflow-info-card {
        border-top: 1px solid #eee7de !important;
        border-left: 0 !important;
    }
}
</style>
@endpush

@section('content')

@php
    $statusTone = fn (string $status) => match ($status) {
        'new' => 'border-[#eadfc9] bg-[#fff9ef] text-[#a8731f]',
        'preparing' => 'border-[#d5e2ed] bg-[#f3f8fc] text-[#5d7f9d]',
        'ready_for_pickup' => 'border-[#e5d9b9] bg-[#fff9ec] text-[#a67820]',
        'courier_accepted', 'heading_pickup', 'arrived_pickup' => 'border-[#ddd5e7] bg-[#f7f4f9] text-[#75618a]',
        'in_transit', 'arrived_buyer' => 'border-[#cfdeea] bg-[#f1f7fb] text-[#3c6e91]',
        'delivered' => 'border-[#cfe4d7] bg-[#f1f8f4] text-[#4f7d63]',
        'cancelled' => 'border-[#efcece] bg-[#fff5f5] text-[#a65353]',
        default => 'border-[#e5dfd7] bg-[#f7f5f2] text-[#756d63]',
    };

    $progressValue = fn (string $status) => match ($status) {
        'new' => 10,
        'preparing' => 25,
        'ready_for_pickup' => 40,
        'courier_accepted' => 52,
        'heading_pickup' => 60,
        'arrived_pickup' => 68,
        'in_transit' => 80,
        'arrived_buyer' => 92,
        'delivered' => 100,
        default => 0,
    };

    $totalOrders = $orders->count();
    $sellerOrderRealtimeToken = (string) ($seller->realtime_token ?? '');
    $sellerOrderLatestUpdatedAt = $orders->max('updated_at');
    $sellerOrderRevision = $sellerOrderLatestUpdatedAt
        ? \Illuminate\Support\Carbon::parse($sellerOrderLatestUpdatedAt)->format('Y-m-d H:i:s')
        : '';
@endphp


<div class="seller-orders-page mx-auto w-full max-w-[1800px]">
<div id="sellerOrdersStage" class="seller-orders-stage">

    {{-- Server-rendered orders are visible immediately; no loading skeleton. --}}

    <div id="sellerOrdersContent" class="seller-orders-content">


    @if (session('success'))
        <div class="mb-4 flex items-start gap-3 rounded-[15px] border border-[#cfe4d7] bg-[#f3faf5] px-4 py-3.5 text-[#4f7d63]">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-[10px] border border-[#d5e7dc] bg-white">
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="m8 12 2.5 2.5L16 9"></path>
                </svg>
            </span>
            <div>
                <p class="text-[11px] font-medium">Order updated</p>
                <p class="mt-1 text-[9px] leading-4 text-[#6f8778]">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 flex items-start gap-3 rounded-[15px] border border-[#efcece] bg-[#fff6f6] px-4 py-3.5 text-[#a65353]">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-[10px] border border-[#efdada] bg-white">
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 3 3 20h18L12 3Z"></path>
                    <path d="M12 9v5"></path>
                    <path d="M12 17h.01"></path>
                </svg>
            </span>
            <div>
                <p class="text-[11px] font-medium">Action unavailable</p>
                <p class="mt-1 text-[9px] leading-4 text-[#906363]">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif

    {{-- =========================================================
        HEADER — LARGE / LIGHT / NO OUTER CARD
    ========================================================== --}}
    <section class="seller-orders-header">
        <div class="seller-orders-heading-group">
            <span class="seller-orders-header-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 5h12v15H6z"></path>
                    <path d="M9 5V3h6v2"></path>
                    <path d="M9 10h6"></path>
                    <path d="M9 14h6"></path>
                </svg>
            </span>

            <div class="min-w-0">
                <p class="seller-orders-eyebrow">ORDER MANAGEMENT</p>
                <h1 class="seller-orders-title">
                    <span>Seller</span><span class="seller-orders-title-accent">Orders</span>
                </h1>
                <p class="seller-orders-subtitle">
                    Track, prepare, and fulfill Buyer orders from one clean workspace.
                </p>
            </div>
        </div>

        <div class="seller-orders-header-actions">
            <a href="{{ route('seller.buyer-messages') }}" wire:navigate class="seller-orders-header-action">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 11.5a7.5 7.5 0 0 1-7.5 7.5H9l-4 2v-4A7.5 7.5 0 1 1 21 11.5Z"></path>
                </svg>
                Buyer Inbox
            </a>

            <a href="{{ route('seller.reviews') }}" wire:navigate class="seller-orders-header-action">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="m12 3 2.7 5.5 6 .9-4.3 4.2 1 5.9L12 16.7 6.6 19.5l1-5.9-4.3-4.2 6-.9L12 3Z"></path>
                </svg>
                Product Reviews
            </a>
        </div>
    </section>

    <div
        id="sellerOrdersDynamicRoot"
        data-revision="{{ $sellerOrderRevision }}"
        data-realtime-channel="{{ $sellerOrderRealtimeToken ? 'sari.seller.' . $sellerOrderRealtimeToken : '' }}"
    >
    {{-- =========================================================
        SUMMARY CARDS — ARCHIVE-LIKE LAYOUT
    ========================================================== --}}
    <section class="seller-orders-summary mt-5 grid grid-cols-2 gap-3 md:grid-cols-5">
        @php
            $summaryCards = [
                [
                    'label' => 'Total Orders',
                    'value' => $totalOrders,
                    'sub' => 'All seller order records',
                    'border' => 'border-[#d8e4f0]',
                    'iconBg' => 'bg-[#f0f6fb]',
                    'iconText' => 'text-[#4e80aa]',
                    'icon' => 'history',
                ],
                [
                    'label' => 'New Orders',
                    'value' => $stats['new'],
                    'sub' => 'Awaiting preparation',
                    'border' => 'border-[#eadbbd]',
                    'iconBg' => 'bg-[#fff7e9]',
                    'iconText' => 'text-[#b77c16]',
                    'icon' => 'bag',
                ],
                [
                    'label' => 'Preparing',
                    'value' => $stats['preparing'],
                    'sub' => 'Currently being packed',
                    'border' => 'border-[#dce7ef]',
                    'iconBg' => 'bg-[#f2f8fc]',
                    'iconText' => 'text-[#5a7d99]',
                    'icon' => 'box',
                ],
                [
                    'label' => 'With Courier',
                    'value' => $stats['courier'],
                    'sub' => 'Pickup or delivery flow',
                    'border' => 'border-[#ddd8e8]',
                    'iconBg' => 'bg-[#f6f3f9]',
                    'iconText' => 'text-[#75618a]',
                    'icon' => 'truck',
                ],
                [
                    'label' => 'Delivered',
                    'value' => $stats['delivered'],
                    'sub' => 'Completed orders',
                    'border' => 'border-[#d4e6db]',
                    'iconBg' => 'bg-[#f1f8f4]',
                    'iconText' => 'text-[#4f7d63]',
                    'icon' => 'check',
                ],
            ];
        @endphp

        @foreach ($summaryCards as $card)
            <div class="seller-orders-summary-card">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium text-[#5f574f]">{{ $card['label'] }}</p>
                        <p class="mt-2 text-[31px] font-semibold leading-none tracking-[-.04em] text-[#211d18]">{{ $card['value'] }}</p>
                        <p class="mt-3 truncate text-[9.5px] font-normal text-[#8f867c]">{{ $card['sub'] }}</p>
                    </div>

                    <span class="seller-orders-summary-icon {{ $card['iconBg'] }} {{ $card['iconText'] }}">
                        @if ($card['icon'] === 'history')
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="8"></circle><path d="M12 8v5l3 2"></path>
                            </svg>
                        @elseif ($card['icon'] === 'bag')
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M5 7h14l-1 13H6L5 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path>
                            </svg>
                        @elseif ($card['icon'] === 'box')
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 6h16v13H4z"></path><path d="M8 6V4h8v2"></path><path d="M8 11h8"></path>
                            </svg>
                        @elseif ($card['icon'] === 'truck')
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 7h11v9H3z"></path><path d="M14 10h4l3 3v3h-7z"></path><circle cx="7" cy="18" r="1.5"></circle><circle cx="18" cy="18" r="1.5"></circle>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="8"></circle><path d="m8 12 2.5 2.5L16 9"></path>
                            </svg>
                        @endif
                    </span>
                </div>
            </div>
        @endforeach
    </section>

    {{-- =========================================================
        ORDERS TABLE
    ========================================================== --}}
    <section class="seller-orders-filter-panel" aria-label="Order filters">
        <label class="seller-orders-search-field">
            <span aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.5-3.5"></path>
                </svg>
            </span>
            <input
                id="sellerOrderSearch"
                type="search"
                placeholder="Search order ID, buyer, product..."
                autocomplete="off"
            >
        </label>

        <select id="sellerOrderStatusFilter" class="seller-orders-filter-select">
            <option value="all">All Status</option>
            <option value="new">New</option>
            <option value="preparing">Preparing</option>
            <option value="ready_for_pickup">Ready Pickup</option>
            <option value="courier">With Courier</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
        </select>

        <button id="sellerOrderApplyFilter" type="button" class="seller-orders-apply-filter">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M4 5h16"></path>
                <path d="M7 10h10"></path>
                <path d="M10 15h4"></path>
            </svg>
            Apply Filter
        </button>

        <button id="sellerOrderResetFilter" type="button" class="seller-orders-reset-filter">Reset</button>
    </section>

    <section class="seller-orders-workspace">
        {{-- DESKTOP TABLE HEADER --}}
        <div class="seller-orders-table-head">
            <span>#</span>
            <span>Order</span>
            <span>Buyer</span>
            <span>Status</span>
            <span>Total</span>
            <span>Date</span>
            <span class="text-right">Action</span>
        </div>

        <div id="sellerOrderRows" class="seller-orders-table-body">
            @forelse ($orders as $order)
                @php
                    $items = is_array($order->items) ? $order->items : [];
                    $firstItem = $items[0] ?? [];
                    $firstProductId = (int) ($firstItem['product_id'] ?? 0);
                    $firstImage = $firstProductId > 0
                        ? route('seller.products.image', $firstProductId)
                        : null;

                    $searchText = strtolower(implode(' ', array_filter([
                        $order->order_number,
                        $order->buyer_name,
                        $order->buyer_email ?? null,
                        collect($items)->pluck('name')->implode(' '),
                    ])));

                    $thumbnailIsPriority = $loop->index < 4;
                    $thumbnailIsEager = $loop->index < 6;

                    $filterStatus = in_array($order->status, [
                        'courier_accepted',
                        'heading_pickup',
                        'arrived_pickup',
                        'in_transit',
                        'arrived_buyer',
                    ], true)
                        ? 'courier'
                        : $order->status;
                @endphp

                <article
                    class="seller-order-row"
                    data-order-row
                    data-search="{{ $searchText }}"
                    data-status="{{ $filterStatus }}"
                >
                    <div class="seller-order-grid">
                        <div class="seller-order-index">{{ $loop->iteration }}</div>

                        {{-- ORDER / PRODUCT --}}
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="seller-order-image-frame {{ $firstImage ? '' : 'is-loaded' }} relative h-[74px] w-[74px] shrink-0 overflow-hidden rounded-[12px] border border-[#e8e1d8] bg-[#f7f5f1]">
                                @if ($firstImage)
                                    <img
                                        src="{{ $firstImage }}"
                                        alt="{{ $firstItem['name'] ?? 'Ordered product' }}"
                                        width="74"
                                        height="74"
                                        loading="{{ $thumbnailIsEager ? 'eager' : 'lazy' }}"
                                        fetchpriority="{{ $thumbnailIsPriority ? 'high' : 'auto' }}"
                                        decoding="async"
                                        class="h-full w-full object-cover"
                                        onload="this.parentElement.classList.add('is-loaded')"
                                        onerror="this.parentElement.classList.add('is-loaded'); this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                    >
                                @endif

                                <div class="{{ $firstImage ? 'hidden' : '' }} grid h-full w-full place-items-center text-[#aaa197]">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                                        <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                        <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                                    </svg>
                                </div>

                                @if (count($items) > 1)
                                    <span class="absolute bottom-1 right-1 grid h-5 min-w-[20px] place-items-center rounded-full border border-white/80 bg-[#28231e]/85 px-1 text-[7.5px] font-medium text-white">
                                        +{{ count($items) - 1 }}
                                    </span>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-[12px] font-semibold text-[#2c2721]">{{ $order->order_number }}</p>
                                <p class="mt-1 truncate text-[10px] font-medium text-[#5a524a]">{{ $firstItem['name'] ?? 'Order items' }}</p>
                                <p class="mt-1 text-[9px] font-normal text-[#91887e]">
                                    {{ count($items) }} {{ count($items) === 1 ? 'item' : 'items' }}
                                    <span class="mx-1 text-[#d2cbc2]">•</span>
                                    {{ $order->payment_method }}
                                </p>
                            </div>
                        </div>

                        {{-- BUYER --}}
                        <div class="min-w-0 lg:block">
                            <p class="mb-1 text-[8.8px] font-medium uppercase tracking-[.08em] text-[#91887e] lg:hidden">Buyer</p>
                            <p class="truncate text-[10.5px] font-medium text-[#454038]">{{ $order->buyer_name }}</p>
                            <p class="mt-1 truncate text-[9px] font-normal text-[#91887e]">{{ $order->buyer_email ?? 'Buyer account' }}</p>
                        </div>

                        {{-- STATUS --}}
                        <div>
                            <p class="mb-1 text-[8.8px] font-medium uppercase tracking-[.08em] text-[#91887e] lg:hidden">Status</p>
                            <span class="inline-flex rounded-full border px-2.5 py-1.5 text-[9px] font-medium {{ $statusTone($order->status) }}">
                                {{ $order->statusLabel() }}
                            </span>
                        </div>

                        {{-- TOTAL --}}
                        <div>
                            <p class="mb-1 text-[8.8px] font-medium uppercase tracking-[.08em] text-[#91887e] lg:hidden">Total</p>
                            <p class="text-[11px] font-semibold text-[#302a24]">₱{{ number_format((float) $order->total, 2) }}</p>
                        </div>

                        {{-- DATE --}}
                        <div>
                            <p class="mb-1 text-[8.8px] font-medium uppercase tracking-[.08em] text-[#91887e] lg:hidden">Date</p>
                            <p class="text-[10px] font-medium text-[#4a443d]">{{ $order->created_at?->format('M d, Y') }}</p>
                            <p class="mt-1 text-[8.8px] font-normal text-[#91887e]">{{ $order->created_at?->format('h:i A') }}</p>
                        </div>

                        {{-- ACTION --}}
                        <div class="flex items-center justify-end gap-1.5">
                            <button
                                type="button"
                                class="seller-order-view-button"
                                data-order-template="sellerOrderTemplate-{{ $order->id }}"
                                aria-label="View {{ $order->order_number }}"
                                title="View Order"
                            >
                                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                </article>

                {{-- =====================================================
                    PRE-RENDERED ORDER DETAIL TEMPLATE
                    Modal opens instantly: no fetch / no API delay.
                ====================================================== --}}
                <template id="sellerOrderTemplate-{{ $order->id }}">
                    @php
                        $progress = $progressValue($order->status);
                        $workflowSteps = [
                            ['Order placed', 10],
                            ['Preparing', 25],
                            ['Ready for pickup', 40],
                            ['In transit', 80],
                            ['Delivered', 100],
                        ];
                        $nextActionLabel = match ($order->status) {
                            'new' => 'Prepare this order',
                            'preparing' => 'Mark ready for pickup',
                            'ready_for_pickup' => 'Waiting for courier',
                            'courier_accepted', 'heading_pickup', 'arrived_pickup' => 'Courier pickup in progress',
                            'in_transit', 'arrived_buyer' => 'Delivery in progress',
                            'delivered' => 'Order completed',
                            'cancelled' => 'Order cancelled',
                            default => $order->statusLabel(),
                        };
                    @endphp

                    <div class="seller-order-modal-panel seller-order-workflow-panel">
                        <header class="seller-order-workflow-header">
                            <div class="seller-order-workflow-header-main">
                                <span class="seller-order-workflow-header-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M6 5h12v15H6z"></path>
                                        <path d="M9 5V3h6v2"></path>
                                        <path d="M9 10h6"></path>
                                        <path d="M9 14h6"></path>
                                    </svg>
                                </span>

                                <div class="min-w-0">
                                    <div class="seller-order-workflow-title-row">
                                        <h3>Order Details</h3>
                                        <span class="seller-order-workflow-status {{ $statusTone($order->status) }}">
                                            {{ $order->statusLabel() }}
                                        </span>
                                    </div>

                                    <div class="seller-order-workflow-meta">
                                        <span>{{ $order->order_number }}</span>
                                        <span aria-hidden="true">•</span>
                                        <span>{{ $order->created_at?->format('M d, Y · h:i A') }}</span>
                                        <span aria-hidden="true">•</span>
                                        <strong>₱{{ number_format((float) $order->total, 2) }}</strong>
                                    </div>
                                </div>
                            </div>

                            <button type="button" data-close-order-modal class="seller-order-workflow-close" aria-label="Close order details">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="m7 7 10 10"></path>
                                    <path d="M17 7 7 17"></path>
                                </svg>
                            </button>
                        </header>

                        <div class="seller-order-workflow-body">
                            <div class="seller-order-workflow-main">
                                <section class="seller-order-workflow-card seller-order-workflow-products">
                                    <div class="seller-order-workflow-section-head">
                                        <div class="seller-order-workflow-section-title">
                                            <span class="seller-order-workflow-section-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M5 7h14l-1 13H6L5 7Z"></path>
                                                    <path d="M9 7a3 3 0 0 1 6 0"></path>
                                                </svg>
                                            </span>
                                            <div>
                                                <h4>Ordered Products</h4>
                                                <p>{{ count($items) }} {{ count($items) === 1 ? 'item' : 'items' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="seller-order-workflow-product-list seller-order-scrollbar">
                                        @forelse ($items as $item)
                                            @php
                                                $itemProductId = (int) ($item['product_id'] ?? 0);
                                                $itemImage = $itemProductId > 0
                                                    ? route('seller.products.image', $itemProductId)
                                                    : null;
                                                $itemQty = max(1, (int) ($item['qty'] ?? 1));
                                                $itemPrice = (float) ($item['price'] ?? 0);
                                                $itemLineTotal = (float) ($item['line_total'] ?? ($itemPrice * $itemQty));
                                            @endphp

                                            <div class="seller-order-workflow-product-row">
                                                <div class="seller-order-workflow-product-image seller-order-image-frame {{ $itemImage ? '' : 'is-loaded' }}">
                                                    @if ($itemImage)
                                                        <img
                                                            src="{{ $itemImage }}"
                                                            alt="{{ $item['name'] ?? 'Ordered product' }}"
                                                            width="54"
                                                            height="54"
                                                            loading="lazy"
                                                            fetchpriority="low"
                                                            decoding="async"
                                                            class="h-full w-full object-contain"
                                                            onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                                        >
                                                    @endif

                                                    <div class="{{ $itemImage ? 'hidden' : '' }} seller-order-workflow-image-fallback">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                                            <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                                            <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                                                        </svg>
                                                    </div>
                                                </div>

                                                <div class="seller-order-workflow-product-copy">
                                                    <p class="seller-order-workflow-product-name">{{ $item['name'] ?? 'Product' }}</p>
                                                    <p class="seller-order-workflow-product-meta">
                                                        Qty {{ $itemQty }}
                                                        @if (!empty($item['variant_label']))
                                                            · {{ $item['variant_label'] }}
                                                        @endif
                                                    </p>
                                                </div>

                                                <p class="seller-order-workflow-product-price">₱{{ number_format($itemLineTotal, 2) }}</p>
                                            </div>
                                        @empty
                                            <div class="seller-order-workflow-empty">No item detail available.</div>
                                        @endforelse
                                    </div>
                                </section>

                                <div class="seller-order-workflow-info-grid">
                                    <section class="seller-order-workflow-card seller-order-workflow-info-card">
                                        <div class="seller-order-workflow-section-title">
                                            <span class="seller-order-workflow-section-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <circle cx="12" cy="8" r="3"></circle>
                                                    <path d="M5 20a7 7 0 0 1 14 0"></path>
                                                </svg>
                                            </span>
                                            <div>
                                                <h4>Buyer & Delivery</h4>
                                                <p>Customer and shipping details</p>
                                            </div>
                                        </div>

                                        <dl class="seller-order-workflow-details">
                                            <div>
                                                <dt>Buyer</dt>
                                                <dd>{{ $order->buyer_name }}</dd>
                                            </div>
                                            <div>
                                                <dt>Email</dt>
                                                <dd class="break-all">{{ $order->buyer_email ?: 'Buyer account' }}</dd>
                                            </div>
                                            <div class="seller-order-workflow-detail-wide">
                                                <dt>Shipping address</dt>
                                                <dd>{{ $order->buyer_address }}</dd>
                                            </div>
                                        </dl>
                                    </section>

                                    <section class="seller-order-workflow-card seller-order-workflow-info-card">
                                        <div class="seller-order-workflow-section-title">
                                            <span class="seller-order-workflow-section-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M3 7h11v9H3z"></path>
                                                    <path d="M14 10h4l3 3v3h-7z"></path>
                                                    <circle cx="7" cy="18" r="1.5"></circle>
                                                    <circle cx="18" cy="18" r="1.5"></circle>
                                                </svg>
                                            </span>
                                            <div>
                                                <h4>Delivery & Payment</h4>
                                                <p>Courier and transaction method</p>
                                            </div>
                                        </div>

                                        <dl class="seller-order-workflow-details">
                                            <div>
                                                <dt>Courier</dt>
                                                <dd>{{ $order->courier_name ?: 'Waiting for courier' }}</dd>
                                            </div>
                                            <div>
                                                <dt>Payment</dt>
                                                <dd>{{ $order->payment_method }}</dd>
                                            </div>
                                            <div class="seller-order-workflow-detail-wide">
                                                <dt>Order date</dt>
                                                <dd>{{ $order->created_at?->format('M d, Y · h:i A') }}</dd>
                                            </div>
                                        </dl>
                                    </section>
                                </div>
                            </div>

                            <aside class="seller-order-workflow-sidebar seller-order-scrollbar">
                                <section class="seller-order-workflow-card seller-order-workflow-summary">
                                    <div class="seller-order-workflow-section-title">
                                        <span class="seller-order-workflow-section-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M6 3h12v18H6z"></path>
                                                <path d="M9 8h6"></path>
                                                <path d="M9 12h6"></path>
                                            </svg>
                                        </span>
                                        <div>
                                            <h4>Order Summary</h4>
                                            <p>Payment breakdown</p>
                                        </div>
                                    </div>

                                    <div class="seller-order-workflow-money-lines">
                                        <div><span>Subtotal</span><strong>₱{{ number_format((float) $order->subtotal, 2) }}</strong></div>
                                        <div><span>Delivery fee</span><strong>₱{{ number_format((float) $order->delivery_fee, 2) }}</strong></div>
                                    </div>

                                    <div class="seller-order-workflow-total">
                                        <span>Total</span>
                                        <strong>₱{{ number_format((float) $order->total, 2) }}</strong>
                                    </div>
                                </section>

                                <section class="seller-order-workflow-card seller-order-workflow-progress-card">
                                    <div class="seller-order-workflow-section-head">
                                        <div class="seller-order-workflow-section-title">
                                            <span class="seller-order-workflow-section-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <circle cx="12" cy="12" r="8"></circle>
                                                    <path d="M12 8v4l3 2"></path>
                                                </svg>
                                            </span>
                                            <div>
                                                <h4>Fulfillment</h4>
                                                <p>Order progress</p>
                                            </div>
                                        </div>
                                        <strong class="seller-order-workflow-progress-percent">{{ $progress }}%</strong>
                                    </div>

                                    <div class="seller-order-workflow-progress-bar" aria-hidden="true">
                                        <span style="width: {{ $progress }}%"></span>
                                    </div>

                                    <ol class="seller-order-workflow-timeline">
                                        @foreach ($workflowSteps as [$step, $stepProgress])
                                            @php $complete = $progress >= $stepProgress; @endphp
                                            <li class="{{ $complete ? 'is-complete' : '' }}">
                                                <span class="seller-order-workflow-timeline-dot">
                                                    @if ($complete)
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="m7 12 3 3 7-7"></path>
                                                        </svg>
                                                    @else
                                                        <span></span>
                                                    @endif
                                                </span>
                                                <span>{{ $step }}</span>
                                            </li>
                                        @endforeach
                                    </ol>
                                </section>

                                <section class="seller-order-workflow-card seller-order-workflow-actions">
                                    <div class="seller-order-workflow-action-heading">
                                        <span class="seller-order-workflow-action-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                <path d="m13 2-8 11h6l-1 9 9-12h-6l0-8Z"></path>
                                            </svg>
                                        </span>
                                        <div>
                                            <p>Next Action</p>
                                            <h4>{{ $nextActionLabel }}</h4>
                                            <small>{{ $order->status === 'new' ? 'Start preparing the items for pickup.' : ($order->status === 'preparing' ? 'Finish packing and hand the order to the courier.' : 'Continue with the current fulfillment stage.') }}</small>
                                        </div>
                                    </div>

                                    @if ($order->status === 'new')
                                        <form method="POST" action="{{ route('seller.orders.prepare', $order) }}">
                                            @csrf
                                            <button class="seller-order-workflow-primary-action">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M4 6h16v13H4z"></path>
                                                    <path d="M8 6V4h8v2"></path>
                                                    <path d="M8 11h8"></path>
                                                </svg>
                                                Prepare Order
                                            </button>
                                        </form>
                                    @elseif ($order->status === 'preparing')
                                        <form method="POST" action="{{ route('seller.orders.ready-pickup', $order) }}">
                                            @csrf
                                            <button class="seller-order-workflow-primary-action seller-order-workflow-primary-action--success">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M4 6h16v12H4z"></path>
                                                    <path d="m8 12 2.5 2.5L16 9"></path>
                                                </svg>
                                                Mark Ready for Pickup
                                            </button>
                                        </form>
                                    @else
                                        <div class="seller-order-workflow-passive-action {{ $statusTone($order->status) }}">
                                            {{ $order->statusLabel() }}
                                        </div>
                                    @endif

                                    <div class="seller-order-workflow-secondary-actions">
                                        @if ($order->buyer_account_id || $order->buyer_social_account_id)
                                            <a
                                                href="{{ route('seller.buyer-messages', ['buyer' => $order->buyer_account_id ? 'account-' . $order->buyer_account_id : 'social-' . $order->buyer_social_account_id]) }}"
                                                wire:navigate
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M21 11.5a7.5 7.5 0 0 1-7.5 7.5H9l-4 2v-4A7.5 7.5 0 1 1 21 11.5Z"></path>
                                                </svg>
                                                Message Buyer
                                            </a>
                                        @endif

                                        <a href="{{ route('seller.orders.waybill', $order) }}" target="_blank">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M6 9V3h12v6"></path>
                                                <path d="M6 18H4V9h16v9h-2"></path>
                                                <path d="M7 14h10v7H7z"></path>
                                            </svg>
                                            Print Waybill
                                        </a>
                                    </div>
                                </section>
                            </aside>
                        </div>
                    </div>
                </template>
            @empty
                <div class="rounded-[17px] border border-dashed border-[#ded5c9] bg-[#fcfbf8] px-6 py-12 text-center">
                    <div class="mx-auto grid h-11 w-11 place-items-center rounded-[14px] border border-[#ebe3d8] bg-white text-[#9b9185]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M5 7h14l-1 13H6L5 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path>
                        </svg>
                    </div>
                    <p class="mt-3 text-[11px] font-medium text-[#514a42]">No buyer orders yet.</p>
                    <p class="mt-1 text-[9px] font-normal text-[#91887d]">New Buyer checkout orders will automatically appear here.</p>
                </div>
            @endforelse
        </div>

        @if ($orders->isNotEmpty())
            <div id="sellerOrderNoResults" class="hidden border-t border-[#eee8df] px-6 py-10 text-center">
                <p class="text-[10px] font-medium text-[#595149]">No matching orders.</p>
                <p class="mt-1 text-[8.5px] font-normal text-[#958c82]">Try another search or status filter.</p>
            </div>
        @endif

        <div class="seller-orders-table-footer">
            <p id="sellerOrderResultCount">Showing {{ $orders->count() }} of {{ $orders->count() }} orders</p>
            <div class="seller-orders-pagination" aria-label="Current order page">
                <button type="button" disabled aria-label="Previous page">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"></path></svg>
                </button>
                <span>1</span>
                <button type="button" disabled aria-label="Next page">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 18 6-6-6-6"></path></svg>
                </button>
            </div>
        </div>
    </section>
    </div>
</div>
    </div>
</div>


{{-- =============================================================
    SHARED CENTER MODAL
============================================================= --}}
<div
    id="sellerOrderDetailModal"
    class="fixed inset-0 z-[120] hidden items-center justify-center bg-[#211b14]/35 p-3 backdrop-blur-[2px] sm:p-5"
    aria-hidden="true"
>
    <button
        type="button"
        data-close-order-modal
        class="absolute inset-0 cursor-default"
        aria-label="Close order modal backdrop"
    ></button>

    <div id="sellerOrderDetailModalContent" class="relative z-[1] flex w-full justify-center"></div>
</div>

@push('scripts')
<script>
function resetSellerOrderManagementPage() {
    window.clearInterval(window.__SARI_SELLER_ORDER_SYNC_INTERVAL__);

    window.__SARI_SELLER_ORDER_SYNC_INTERVAL__ = null;

    window.clearTimeout(window.__SARI_SELLER_ORDER_REFRESH_TIMER__);
    window.__SARI_SELLER_ORDER_REFRESH_TIMER__ = null;

    if (window.__SARI_SELLER_ORDER_ABORT__) {
        window.__SARI_SELLER_ORDER_ABORT__.abort();
    }

    window.__SARI_SELLER_ORDER_ABORT__ = new AbortController();
}

function initSellerOrderManagementPage() {
    const signal = window.__SARI_SELLER_ORDER_ABORT__?.signal;

    const modal = document.getElementById('sellerOrderDetailModal');
    const modalContent = document.getElementById('sellerOrderDetailModalContent');
    const realtimeChannelName = @json($sellerOrderRealtimeToken ? 'sari.seller.' . $sellerOrderRealtimeToken : null);
    const ordersPageUrl = @json(route('seller.orders'));
    const liveStateUrl = @json(route('seller.orders.live-state'));

    let currentRevision = document.getElementById('sellerOrdersDynamicRoot')?.dataset.revision || @json($sellerOrderRevision);
    let refreshBusy = false;
    let refreshQueued = false;
    let subscribedChannel = null;


    function closeOrderModal() {
        if (!modal || !modalContent) return;

        modal.dataset.openTemplate = '';
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
        modalContent.innerHTML = '';
        document.documentElement.classList.remove('overflow-hidden');
    }

    function openOrderModal(templateId, preserveFocus = false) {
        if (!modal || !modalContent) return;

        const template = document.getElementById(templateId);
        if (!(template instanceof HTMLTemplateElement)) return;

        modal.dataset.openTemplate = templateId;
        modalContent.innerHTML = '';
        modalContent.appendChild(template.content.cloneNode(true));

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.documentElement.classList.add('overflow-hidden');

        if (!preserveFocus) {
            window.requestAnimationFrame(() => {
                modalContent.querySelector('[data-close-order-modal]')?.focus({ preventScroll: true });
            });
        }
    }

    function applyOrderFilters() {
        const searchInput = document.getElementById('sellerOrderSearch');
        const statusFilter = document.getElementById('sellerOrderStatusFilter');
        const resultCount = document.getElementById('sellerOrderResultCount');
        const noResults = document.getElementById('sellerOrderNoResults');
        const rows = Array.from(document.querySelectorAll('[data-order-row]'));

        const query = (searchInput?.value || '').trim().toLowerCase();
        const selectedStatus = statusFilter?.value || 'all';
        let visibleCount = 0;

        rows.forEach((row) => {
            const matchesSearch = !query || (row.dataset.search || '').includes(query);
            const matchesStatus = selectedStatus === 'all' || row.dataset.status === selectedStatus;
            const visible = matchesSearch && matchesStatus;

            row.classList.toggle('hidden', !visible);
            if (visible) visibleCount++;
        });

        if (resultCount) {
            resultCount.textContent = `Showing ${visibleCount} of ${rows.length} orders`;
        }

        const hasOrderRows = rows.length > 0;
        noResults?.classList.toggle('hidden', !hasOrderRows || visibleCount !== 0);
    }

    function captureUiState() {
        return {
            search: document.getElementById('sellerOrderSearch')?.value || '',
            status: document.getElementById('sellerOrderStatusFilter')?.value || 'all',
            openTemplate: modal && !modal.classList.contains('hidden')
                ? (modal.dataset.openTemplate || '')
                : '',
        };
    }

    function restoreUiState(state) {
        const searchInput = document.getElementById('sellerOrderSearch');
        const statusFilter = document.getElementById('sellerOrderStatusFilter');

        if (searchInput) searchInput.value = state.search;
        if (statusFilter) statusFilter.value = state.status;

        applyOrderFilters();

        if (state.openTemplate && document.getElementById(state.openTemplate)) {
            openOrderModal(state.openTemplate, true);
        } else if (state.openTemplate) {
            closeOrderModal();
        }
    }

    async function refreshOrderRegion(reason = 'realtime') {
        if (document.hidden) {
            refreshQueued = true;
            return;
        }

        if (refreshBusy) {
            refreshQueued = true;
            return;
        }

        const currentRoot = document.getElementById('sellerOrdersDynamicRoot');
        if (!currentRoot) return;

        refreshBusy = true;
        refreshQueued = false;
        const uiState = captureUiState();

        try {
            const response = await fetch(ordersPageUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-SARI-Partial-Refresh': reason,
                },
                credentials: 'same-origin',
                cache: 'no-store',
                signal,
            });

            if (!response.ok) return;

            const html = await response.text();
            const parsed = new DOMParser().parseFromString(html, 'text/html');
            const incomingRoot = parsed.getElementById('sellerOrdersDynamicRoot');

            if (!incomingRoot) return;

            currentRoot.replaceWith(incomingRoot);
            currentRevision = incomingRoot.dataset.revision || currentRevision;
            restoreUiState(uiState);

            window.dispatchEvent(new CustomEvent('sari:seller-orders-refreshed', {
                detail: { reason, revision: currentRevision }
            }));
        } catch (error) {
            if (error?.name !== 'AbortError') {
                console.debug('Seller order background refresh temporarily unavailable.');
            }
        } finally {
            refreshBusy = false;

            if (refreshQueued && !signal?.aborted) {
                refreshQueued = false;
                window.clearTimeout(window.__SARI_SELLER_ORDER_REFRESH_TIMER__);
                window.__SARI_SELLER_ORDER_REFRESH_TIMER__ = window.setTimeout(
                    () => refreshOrderRegion('queued'),
                    180
                );
            }
        }
    }

    function scheduleRegionRefresh(reason = 'realtime') {
        window.clearTimeout(window.__SARI_SELLER_ORDER_REFRESH_TIMER__);
        window.__SARI_SELLER_ORDER_REFRESH_TIMER__ = window.setTimeout(
            () => refreshOrderRegion(reason),
            140
        );
    }

    async function pollOrderRevision() {
        if (document.hidden || refreshBusy) return;

        try {
            const response = await fetch(liveStateUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                cache: 'no-store',
                signal,
            });

            if (!response.ok) return;

            const data = await response.json();
            const revision = data.revision ? String(data.revision) : '';

            if (revision && currentRevision && revision !== String(currentRevision)) {
                currentRevision = revision;
                scheduleRegionRefresh('fallback-poll');
                return;
            }

            if (revision) currentRevision = revision;
        } catch (error) {
            if (error?.name !== 'AbortError') {
                console.debug('Seller order fallback sync temporarily unavailable.');
            }
        }
    }

    function subscribeRealtime(attempt = 0) {
        if (!realtimeChannelName || signal?.aborted) return;

        if (!window.Echo) {
            if (attempt < 24) {
                window.setTimeout(() => subscribeRealtime(attempt + 1), 250);
            }
            return;
        }

        try {
            subscribedChannel = window.Echo.channel(realtimeChannelName);
            subscribedChannel.stopListening('.seller.order.updated');
            subscribedChannel.listen('.seller.order.updated', (event) => {
                if (event?.revision) {
                    currentRevision = String(event.revision);
                }

                window.dispatchEvent(new CustomEvent('sari:seller-order-updated', {
                    detail: event || {}
                }));

                scheduleRegionRefresh('reverb');
            });
        } catch (_) {
            // The 20-second fallback poll below keeps this page functional.
        }
    }

    document.addEventListener('click', function (event) {
        const viewButton = event.target.closest('.seller-order-view-button');
        if (viewButton) {
            openOrderModal(viewButton.dataset.orderTemplate || '');
            return;
        }

        if (event.target.closest('[data-close-order-modal]')) {
            closeOrderModal();
        }
    }, { signal });

    document.addEventListener('input', function (event) {
        if (event.target?.id === 'sellerOrderSearch') {
            applyOrderFilters();
        }
    }, { signal });

    document.addEventListener('change', function (event) {
        if (event.target?.id === 'sellerOrderStatusFilter') {
            applyOrderFilters();
        }
    }, { signal });

    document.getElementById('sellerOrderApplyFilter')?.addEventListener('click', function () {
        applyOrderFilters();
    }, { signal });

    document.getElementById('sellerOrderResetFilter')?.addEventListener('click', function () {
        const searchInput = document.getElementById('sellerOrderSearch');
        const statusFilter = document.getElementById('sellerOrderStatusFilter');
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = 'all';
        applyOrderFilters();
        searchInput?.focus({ preventScroll: true });
    }, { signal });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeOrderModal();
        }
    }, { signal });

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden && refreshQueued) {
            scheduleRegionRefresh('visibility-resume');
        }
    }, { signal });

    applyOrderFilters();

    /*
    | The persistent Seller shell already owns the seller order Echo channel and
    | its fallback poll. Reuse that single source instead of opening a second
    | subscription + timer every time Order Management is visited.
    */
    const sellerShellOwnsOrderSync = Boolean(window.__SARI_SELLER_SHELL_STATE__);

    window.addEventListener('sari:seller-order-update', function (event) {
        if (event?.detail?.revision) {
            currentRevision = String(event.detail.revision);
        }

        scheduleRegionRefresh('shell-realtime');
    }, { signal });

    /* Standalone safety only if this view is ever rendered without seller.blade.php. */
    if (!sellerShellOwnsOrderSync) {
        subscribeRealtime();
        window.__SARI_SELLER_ORDER_SYNC_INTERVAL__ = window.setInterval(pollOrderRevision, 45000);
        window.setTimeout(pollOrderRevision, 10000);
    }

    signal?.addEventListener('abort', function () {
        if (!sellerShellOwnsOrderSync && subscribedChannel) {
            try {
                subscribedChannel.stopListening('.seller.order.updated');
            } catch (_) {}
        }
    }, { once: true });
}

resetSellerOrderManagementPage();

function scheduleSellerOrderManagementPage() {
    initSellerOrderManagementPage();
}

/*
| Body scripts are evaluated when Livewire lands on this page, so initialize
| exactly once here. Cleanup is one-shot on the NEXT navigation away.
*/
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', scheduleSellerOrderManagementPage, { once: true });
} else {
    scheduleSellerOrderManagementPage();
}

document.addEventListener('livewire:navigating', resetSellerOrderManagementPage, { once: true });
</script>
@endpush
@endsection
