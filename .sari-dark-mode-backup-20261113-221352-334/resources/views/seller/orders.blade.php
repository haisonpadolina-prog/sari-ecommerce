@extends('layouts.seller')

@section('title', 'Order Management — SARI Seller')
@section('page-title', 'Order Management')

@push('styles')
<style id="sariSellerEmbeddedCss-orders" data-sari-inline-css="orders">
@verbatim
/* Extracted from resources/views/seller/orders.blade.php — original style block 1. */
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
@media (prefers-reduced-motion: reduce)  {
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




































/* Mid-size desktop: keep the table dense enough to avoid horizontal overflow. */
@media (min-width: 1024px) and (max-width: 1279px)  {
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

    
}

/* Smaller heights: shave vertical chrome before allowing any modal scroll. */
@media (min-width: 1024px) and (max-height: 760px)  {
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

    

    

    
}

/* Tablet/mobile remains naturally scrollable and stacked. */
@media (max-width: 1023px)  {
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

    

    

    

    
}

@media (max-width: 639px)  {
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



#sellerOrderNoResults {
    border-top: 0 !important;
    padding: 34px 20px !important;
}

@media (min-width: 1024px) and (max-width: 1279px)  {
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

@media (max-width: 1023px)  {
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

@media (max-width: 639px)  {
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
@media (max-height: 850px) and (min-width: 900px)  {
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

@media (min-width: 1024px) and (max-width: 1279px)  {
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

@media (max-width: 1023px)  {
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

@media (max-width: 767px)  {
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


























/* LEFT: visually merge Products + Buyer/Delivery + Delivery/Payment into one surface. */






















/* Product list: show the full image, not a cropped thumbnail, and avoid internal clipping. */




















/* RIGHT: compact operational cards. */












































/* On short laptop screens the whole body scrolls once; product cards themselves do not clip. */






/* ============================================================
   SELLER ORDERS — ADD PRODUCT VISUAL PARITY / PROFESSIONAL FINAL
   Header scale follows Add Product. UI only: order workflow,
   routes, realtime refresh, filters, and modal behavior unchanged.
   ============================================================ */

.seller-orders-page {
    width: 100% !important;
    max-width: 1440px !important;
    margin-inline: auto !important;
    padding: 0 0 28px !important;
    color: #111827 !important;
}

/* Text-only page header — same hierarchy as Seller Catalog / Add Product. */
.seller-orders-header {
    display: flex !important;
    min-height: 72px !important;
    align-items: flex-start !important;
    justify-content: space-between !important;
    gap: 24px !important;
    margin: 0 0 16px !important;
    padding: 2px 2px 16px !important;
    border: 0 !important;
    border-bottom: 1px solid #e7ebf0 !important;
    background: transparent !important;
}

.seller-orders-heading-group {
    display: block !important;
    min-width: 0 !important;
}

.seller-orders-header-icon {
    display: none !important;
}

.seller-orders-eyebrow {
    margin: 0 !important;
    color: #b87605 !important;
    font-size: 8px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    letter-spacing: .16em !important;
    text-transform: uppercase !important;
}

.seller-orders-title {
    margin: 6px 0 0 !important;
    color: #111827 !important;
    font-size: clamp(30px, 2.35vw, 38px) !important;
    font-weight: 650 !important;
    line-height: 1 !important;
    letter-spacing: -.035em !important;
}

.seller-orders-title-accent {
    margin-left: .18em !important;
    color: #C9890B !important;
}

.seller-orders-subtitle {
    max-width: 720px !important;
    margin: 8px 0 0 !important;
    color: #7c8592 !important;
    font-size: 10.5px !important;
    font-weight: 400 !important;
    line-height: 1.55 !important;
}

.seller-orders-header-actions {
    display: flex !important;
    flex: 0 0 auto !important;
    flex-wrap: wrap !important;
    justify-content: flex-end !important;
    gap: 8px !important;
    padding-top: 2px !important;
}

.seller-orders-header-action {
    display: inline-flex !important;
    height: 42px !important;
    min-height: 42px !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    border: 1px solid #dfe3e8 !important;
    border-radius: 12px !important;
    background: #fff !important;
    padding: 0 14px !important;
    color: #4b5563 !important;
    font-size: 10px !important;
    font-weight: 650 !important;
    box-shadow: 0 5px 14px rgba(15, 23, 42, .035) !important;
    transition: border-color .14s ease, background-color .14s ease, color .14s ease !important;
}

.seller-orders-header-action:hover,
.seller-orders-header-action:focus-visible {
    outline: none !important;
    border-color: #e2bd67 !important;
    background: #fffaf2 !important;
    color: #9b6505 !important;
    transform: none !important;
}

.seller-orders-header-action svg {
    width: 14px !important;
    height: 14px !important;
}

/* Summary — restrained neutral cards; icons carry state color. */
.seller-orders-summary {
    margin-top: 0 !important;
    grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    gap: 12px !important;
}

.seller-orders-summary-card {
    min-width: 0 !important;
    min-height: 94px !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 16px !important;
    background: #fff !important;
    padding: 14px 15px !important;
    box-shadow: 0 8px 22px rgba(15, 23, 42, .035) !important;
}

.seller-orders-summary-card > div {
    height: 100% !important;
    align-items: flex-start !important;
    gap: 10px !important;
}

.seller-orders-summary-card p:first-child {
    color: #667085 !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;
}

.seller-orders-summary-card p:nth-child(2) {
    margin-top: 5px !important;
    color: #111827 !important;
    font-size: 24px !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    letter-spacing: -.035em !important;
}

.seller-orders-summary-card p:nth-child(3) {
    margin-top: 7px !important;
    color: #98a2b3 !important;
    font-size: 8.5px !important;
    line-height: 1.4 !important;
}

.seller-orders-summary-icon {
    width: 36px !important;
    height: 36px !important;
    flex: 0 0 36px !important;
    border: 1px solid rgba(17, 24, 39, .05) !important;
    border-radius: 10px !important;
    box-shadow: none !important;
}

.seller-orders-summary-icon svg {
    width: 15px !important;
    height: 15px !important;
}

/* Filters — same control sizing/radius family as Add Product. */
.seller-orders-filter-panel {
    display: grid !important;
    grid-template-columns: minmax(300px, 1fr) minmax(150px, 190px) 124px 70px !important;
    gap: 10px !important;
    margin-top: 14px !important;
    padding: 11px !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 16px !important;
    background: #fff !important;
    box-shadow: 0 8px 22px rgba(15, 23, 42, .03) !important;
}

.seller-orders-search-field > span {
    left: 14px !important;
    width: 16px !important;
    height: 16px !important;
    color: #98a2b3 !important;
}

.seller-orders-search-field svg {
    width: 15px !important;
    height: 15px !important;
}

#sellerOrderSearch,
#sellerOrderStatusFilter,
.seller-orders-apply-filter,
.seller-orders-reset-filter {
    height: 44px !important;
    min-height: 44px !important;
    border-radius: 12px !important;
    font-size: 10.5px !important;
}

#sellerOrderSearch,
#sellerOrderStatusFilter {
    border: 1px solid #d7dde5 !important;
    background: #fff !important;
    color: #344054 !important;
    box-shadow: none !important;
}

#sellerOrderSearch {
    padding: 0 14px 0 40px !important;
}

#sellerOrderSearch::placeholder {
    color: #98a2b3 !important;
    font-size: 10.5px !important;
}

#sellerOrderStatusFilter {
    padding: 0 34px 0 13px !important;
    font-weight: 600 !important;
}

#sellerOrderSearch:focus,
#sellerOrderStatusFilter:focus {
    border-color: #d59617 !important;
    box-shadow: 0 0 0 4px rgba(213, 150, 23, .09) !important;
}

.seller-orders-apply-filter {
    gap: 7px !important;
    border: 1px solid #d59617 !important;
    background: #d59617 !important;
    padding-inline: 14px !important;
    color: #fff !important;
    font-weight: 700 !important;
    box-shadow: 0 7px 16px rgba(213, 150, 23, .14) !important;
}

.seller-orders-apply-filter:hover,
.seller-orders-apply-filter:focus-visible {
    outline: none !important;
    border-color: #c9890f !important;
    background: #c9890f !important;
    transform: none !important;
}

.seller-orders-reset-filter {
    border: 1px solid #dfe3e8 !important;
    background: #fff !important;
    color: #667085 !important;
    font-weight: 650 !important;
    box-shadow: none !important;
}

.seller-orders-reset-filter:hover,
.seller-orders-reset-filter:focus-visible {
    outline: none !important;
    border-color: #d2d7de !important;
    background: #f8fafc !important;
    color: #344054 !important;
}

/* Order list — one calm operational surface. */
.seller-orders-workspace {
    margin-top: 12px !important;
    overflow: hidden !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 16px !important;
    background: #fff !important;
    box-shadow: 0 10px 26px rgba(15, 23, 42, .035) !important;
}

.seller-orders-table-head {
    display: grid !important;
    grid-template-columns: 36px minmax(250px, 1.4fr) minmax(150px, .8fr) 112px 96px 112px 48px !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 11px 14px !important;
    border-bottom: 1px solid #e8ebef !important;
    background: #fafbfc !important;
}

.seller-orders-table-head > span {
    color: #667085 !important;
    font-size: 8.5px !important;
    font-weight: 700 !important;
    line-height: 1.3 !important;
    letter-spacing: .055em !important;
    text-transform: uppercase !important;
}

.seller-order-row {
    border: 0 !important;
    border-bottom: 1px solid #edf0f3 !important;
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
    border-color: #edf0f3 !important;
    background: #fffdf8 !important;
    box-shadow: none !important;
    transform: none !important;
}

.seller-order-grid {
    display: grid !important;
    grid-template-columns: 36px minmax(250px, 1.4fr) minmax(150px, .8fr) 112px 96px 112px 48px !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 11px 14px !important;
}

.seller-order-index {
    color: #667085 !important;
    font-size: 9px !important;
    font-weight: 600 !important;
}

.seller-order-grid .seller-order-image-frame {
    width: 44px !important;
    height: 44px !important;
    flex: 0 0 44px !important;
    overflow: hidden !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 10px !important;
    background: #f8fafc !important;
}

.seller-order-grid .seller-order-image-frame > img {
    object-fit: cover !important;
}

.seller-order-grid .seller-order-image-frame + div p:first-child {
    color: #1f2937 !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    line-height: 1.35 !important;
}

.seller-order-grid .seller-order-image-frame + div p:nth-child(2) {
    margin-top: 3px !important;
    color: #475467 !important;
    font-size: 9.3px !important;
    line-height: 1.4 !important;
}

.seller-order-grid .seller-order-image-frame + div p:nth-child(3) {
    margin-top: 3px !important;
    color: #98a2b3 !important;
    font-size: 8.2px !important;
    line-height: 1.4 !important;
}

.seller-order-grid > div:nth-child(3) p:first-of-type:not(.lg\:hidden) {
    color: #344054 !important;
    font-size: 9.5px !important;
    font-weight: 600 !important;
}

.seller-order-grid > div:nth-child(3) p:last-child {
    color: #98a2b3 !important;
    font-size: 8.4px !important;
}

.seller-order-grid > div:nth-child(4) span {
    padding: 4px 8px !important;
    font-size: 8px !important;
    font-weight: 650 !important;
}

.seller-order-grid > div:nth-child(5) p:last-child {
    color: #1f2937 !important;
    font-size: 10px !important;
    font-weight: 700 !important;
}

.seller-order-grid > div:nth-child(6) p:first-of-type:not(.lg\:hidden) {
    color: #475467 !important;
    font-size: 9px !important;
    font-weight: 600 !important;
}

.seller-order-grid > div:nth-child(6) p:last-child {
    margin-top: 2px !important;
    color: #98a2b3 !important;
    font-size: 8px !important;
}

.seller-order-view-button {
    display: inline-grid !important;
    width: 32px !important;
    height: 32px !important;
    min-width: 32px !important;
    place-items: center !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 9px !important;
    background: #fff !important;
    color: #667085 !important;
    box-shadow: none !important;
    transition: border-color .14s ease, background-color .14s ease, color .14s ease !important;
}

.seller-order-view-button:hover,
.seller-order-view-button:focus-visible {
    outline: none !important;
    transform: none !important;
    border-color: #efd9a7 !important;
    background: #fffaf0 !important;
    color: #b97805 !important;
}

.seller-order-view-button svg {
    width: 14px !important;
    height: 14px !important;
}

/* Cleaner empty state — no oversized dashed container. */
.seller-orders-empty-state {
    padding: 44px 24px 46px;
    text-align: center;
    background: #fff;
}

.seller-orders-empty-icon {
    display: grid;
    width: 42px;
    height: 42px;
    margin-inline: auto;
    place-items: center;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #fafbfc;
    color: #98a2b3;
}

.seller-orders-empty-icon svg {
    width: 18px !important;
    height: 18px !important;
}

.seller-orders-empty-state > p:nth-of-type(1) {
    margin-top: 13px !important;
    color: #344054 !important;
    font-size: 11px !important;
    font-weight: 650 !important;
}

.seller-orders-empty-state > p:nth-of-type(2) {
    max-width: 460px;
    margin: 5px auto 0 !important;
    color: #98a2b3 !important;
    font-size: 9px !important;
    line-height: 1.55 !important;
}

#sellerOrderNoResults {
    padding: 34px 20px !important;
}

.seller-orders-table-footer {
    min-height: 52px !important;
    border-top: 1px solid #edf0f3 !important;
    padding: 10px 14px !important;
    background: #fff !important;
}

#sellerOrderResultCount {
    color: #7c8592 !important;
    font-size: 8.5px !important;
}

.seller-orders-pagination {
    gap: 6px !important;
}

.seller-orders-pagination button,
.seller-orders-pagination span {
    width: 32px !important;
    min-width: 32px !important;
    height: 32px !important;
    border-radius: 9px !important;
}

.seller-orders-pagination button {
    border: 1px solid #e5e7eb !important;
    background: #fff !important;
    color: #98a2b3 !important;
}

.seller-orders-pagination span {
    background: #d59617 !important;
    color: #fff !important;
    font-size: 8.5px !important;
    box-shadow: 0 6px 14px rgba(213, 150, 23, .14) !important;
}

/* Keep the current order-detail modal workflow untouched; only align its font family. */


@media (min-width: 1024px) and (max-width: 1279px)  {
    .seller-orders-table-head,
.seller-order-grid {
        grid-template-columns: 30px minmax(200px, 1.35fr) minmax(120px, .78fr) 98px 82px 96px 40px !important;
        gap: 7px !important;
        padding-left: 11px !important;
        padding-right: 11px !important;
    }

    .seller-orders-filter-panel {
        grid-template-columns: minmax(250px, 1fr) 150px 116px 62px !important;
        gap: 8px !important;
    }

    .seller-orders-summary {
        gap: 9px !important;
    }
}

@media (max-width: 1023px)  {
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
        padding: 13px !important;
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

@media (max-width: 767px)  {
    .seller-orders-header {
        flex-direction: column !important;
        gap: 13px !important;
        min-height: 0 !important;
    }

    .seller-orders-title {
        font-size: clamp(28px, 8vw, 34px) !important;
        font-weight: 650 !important;
    }

    .seller-orders-subtitle {
        font-size: 10px !important;
    }

    .seller-orders-header-actions {
        display: grid !important;
        width: 100% !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        padding-top: 0 !important;
    }

    .seller-orders-header-action {
        width: 100% !important;
        font-size: 9.5px !important;
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
        height: 44px !important;
        min-height: 44px !important;
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

@media (max-width: 520px)  {
    .seller-orders-summary {
        grid-template-columns: 1fr !important;
    }

    .seller-orders-summary-card {
        min-height: 82px !important;
    }
}


/* ============================================================
   SELLER ORDERS — COMPACT + READABLE FINAL LAYER
   Tightens layout density while keeping operational text visible.
   No route, realtime, filter, order-state, modal-action, messaging,
   waybill, or fulfillment behavior is changed.
   ============================================================ */

.seller-orders-page {
    max-width: 1440px !important;
    padding-bottom: 22px !important;
}

/* Header stays in the same Add Product visual family, with less height. */
.seller-orders-header {
    min-height: 66px !important;
    gap: 18px !important;
    margin-bottom: 12px !important;
    padding: 2px 2px 13px !important;
}

.seller-orders-eyebrow {
    font-size: 8px !important;
    letter-spacing: .16em !important;
}

.seller-orders-title {
    margin-top: 5px !important;
    font-size: clamp(29px, 2.2vw, 36px) !important;
    font-weight: 650 !important;
}

.seller-orders-subtitle {
    margin-top: 7px !important;
    font-size: 10px !important;
    line-height: 1.5 !important;
}

.seller-orders-header-actions {
    gap: 7px !important;
}

.seller-orders-header-action {
    height: 38px !important;
    min-height: 38px !important;
    border-radius: 10px !important;
    padding-inline: 11px !important;
    font-size: 9.5px !important;
}

/* Summary cards: shorter, with text still easy to scan. */
.seller-orders-summary {
    gap: 9px !important;
}

.seller-orders-summary-card {
    min-height: 78px !important;
    border-radius: 13px !important;
    padding: 10px 12px !important;
    box-shadow: 0 5px 16px rgba(15, 23, 42, .028) !important;
}

.seller-orders-summary-card > div {
    gap: 8px !important;
}

.seller-orders-summary-card p:first-child {
    font-size: 9.5px !important;
    line-height: 1.3 !important;
}

.seller-orders-summary-card p:nth-child(2) {
    margin-top: 3px !important;
    font-size: 21px !important;
}

.seller-orders-summary-card p:nth-child(3) {
    margin-top: 5px !important;
    font-size: 8.5px !important;
    line-height: 1.35 !important;
}

.seller-orders-summary-icon {
    width: 32px !important;
    height: 32px !important;
    flex-basis: 32px !important;
    border-radius: 9px !important;
}

.seller-orders-summary-icon svg {
    width: 14px !important;
    height: 14px !important;
}

/* Filter bar: compact controls but normal readable labels. */
.seller-orders-filter-panel {
    grid-template-columns: minmax(300px, 1fr) 160px 118px 66px !important;
    gap: 8px !important;
    margin-top: 10px !important;
    padding: 9px !important;
    border-radius: 13px !important;
    box-shadow: 0 5px 16px rgba(15, 23, 42, .026) !important;
}

#sellerOrderSearch,
#sellerOrderStatusFilter,
.seller-orders-apply-filter,
.seller-orders-reset-filter {
    height: 40px !important;
    min-height: 40px !important;
    border-radius: 9px !important;
    font-size: 9.8px !important;
}

#sellerOrderSearch {
    padding-left: 36px !important;
}

#sellerOrderSearch::placeholder {
    font-size: 9.8px !important;
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

.seller-orders-apply-filter {
    gap: 6px !important;
    padding-inline: 11px !important;
}

.seller-orders-reset-filter {
    padding-inline: 10px !important;
}

/* Main table: shorter rows, readable order/buyer/status data. */
.seller-orders-workspace {
    margin-top: 10px !important;
    border-radius: 13px !important;
    box-shadow: 0 6px 18px rgba(15, 23, 42, .03) !important;
}

.seller-orders-table-head {
    grid-template-columns: 32px minmax(235px, 1.38fr) minmax(140px, .82fr) 108px 90px 106px 42px !important;
    gap: 8px !important;
    padding: 8px 12px !important;
}

.seller-orders-table-head > span {
    font-size: 8.8px !important;
    line-height: 1.3 !important;
}

.seller-order-grid {
    grid-template-columns: 32px minmax(235px, 1.38fr) minmax(140px, .82fr) 108px 90px 106px 42px !important;
    gap: 8px !important;
    padding: 8px 12px !important;
}

.seller-order-index {
    font-size: 9px !important;
}

.seller-order-grid .seller-order-image-frame {
    width: 38px !important;
    height: 38px !important;
    flex-basis: 38px !important;
    border-radius: 9px !important;
}

.seller-order-grid .seller-order-image-frame + div p:first-child {
    font-size: 10.5px !important;
    line-height: 1.3 !important;
}

.seller-order-grid .seller-order-image-frame + div p:nth-child(2) {
    margin-top: 2px !important;
    font-size: 9.3px !important;
}

.seller-order-grid .seller-order-image-frame + div p:nth-child(3) {
    margin-top: 2px !important;
    font-size: 8.5px !important;
}

.seller-order-grid > div:nth-child(3) p:first-of-type:not(.lg\:hidden) {
    font-size: 9.8px !important;
}

.seller-order-grid > div:nth-child(3) p:last-child {
    font-size: 8.7px !important;
}

.seller-order-grid > div:nth-child(4) span {
    padding: 3px 7px !important;
    font-size: 8.4px !important;
}

.seller-order-grid > div:nth-child(5) p:last-child {
    font-size: 10px !important;
}

.seller-order-grid > div:nth-child(6) p:first-of-type:not(.lg\:hidden) {
    font-size: 9px !important;
}

.seller-order-grid > div:nth-child(6) p:last-child {
    font-size: 8.2px !important;
}

.seller-order-view-button {
    width: 30px !important;
    height: 30px !important;
    min-width: 30px !important;
    border-radius: 8px !important;
}

.seller-order-view-button svg {
    width: 13px !important;
    height: 13px !important;
}

.seller-orders-empty-state {
    padding: 34px 22px 36px !important;
}

.seller-orders-empty-icon {
    width: 38px !important;
    height: 38px !important;
    border-radius: 10px !important;
}

.seller-orders-empty-state > p:nth-of-type(1) {
    margin-top: 10px !important;
    font-size: 10.5px !important;
}

.seller-orders-empty-state > p:nth-of-type(2) {
    margin-top: 4px !important;
    font-size: 8.8px !important;
}

.seller-orders-table-footer {
    min-height: 46px !important;
    padding: 8px 12px !important;
}

#sellerOrderResultCount {
    font-size: 8.5px !important;
}

.seller-orders-pagination button,
.seller-orders-pagination span {
    width: 28px !important;
    min-width: 28px !important;
    height: 28px !important;
    border-radius: 8px !important;
}

/* ============================================================
   ORDER DETAIL MODAL — COMPACT BUT LEGIBLE
   ============================================================ */





































/* Raise previously tiny modal typography. */






























































/* Laptop / tablet refinements. */
@media (min-width: 1024px) and (max-width: 1279px)  {
    .seller-orders-table-head,
.seller-order-grid {
        grid-template-columns: 28px minmax(190px, 1.32fr) minmax(118px, .78fr) 96px 78px 94px 36px !important;
        gap: 6px !important;
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    .seller-orders-filter-panel {
        grid-template-columns: minmax(230px, 1fr) 145px 108px 58px !important;
        gap: 7px !important;
    }
}

@media (max-width: 1023px)  {
    .seller-orders-summary {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .seller-orders-filter-panel {
        grid-template-columns: 1fr 1fr !important;
    }

    .seller-orders-search-field {
        grid-column: 1 / -1 !important;
    }

    

    

    
}

@media (max-width: 767px)  {
    .seller-orders-header {
        gap: 10px !important;
    }

    .seller-orders-title {
        font-size: clamp(28px, 8vw, 34px) !important;
    }

    .seller-orders-header-action {
        height: 40px !important;
        min-height: 40px !important;
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

    
}

@media (max-width: 520px)  {
    .seller-orders-summary {
        grid-template-columns: 1fr !important;
    }

    .seller-orders-summary-card {
        min-height: 74px !important;
    }
}



/* ======================================================================
   ORDER MANAGEMENT — FLOATING CONTAINERS + STATUS DROPDOWN POLISH
   UI-only. No route, realtime, filter logic, modal actions, or backend
   behavior changed.
   ====================================================================== */

.seller-orders-page {
    --orders-float-border: #E2E7ED;
    --orders-float-border-strong: #D7DDE5;
    --orders-float-shadow:
        0 1px 2px rgba(15, 23, 42, .035),
        0 10px 26px rgba(15, 23, 42, .075),
        inset 0 1px 0 rgba(255, 255, 255, .86);
    --orders-float-shadow-soft:
        0 1px 2px rgba(15, 23, 42, .025),
        0 6px 16px rgba(15, 23, 42, .05),
        inset 0 1px 0 rgba(255, 255, 255, .82);
}

/* Main page surfaces: static floating depth, neutral outline. */
.seller-orders-page .seller-orders-summary-card,
.seller-orders-page .seller-orders-filter-panel,
.seller-orders-page .seller-orders-workspace {
    border-color: var(--orders-float-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--orders-float-shadow) !important;
    transform: none !important;
}

/* Preserve a calm interface: no container lift on hover. */
.seller-orders-page .seller-orders-summary-card:hover,
.seller-orders-page .seller-orders-filter-panel:hover,
.seller-orders-page .seller-orders-workspace:hover {
    border-color: var(--orders-float-border) !important;
    box-shadow: var(--orders-float-shadow) !important;
    transform: none !important;
}

/* Main table should float as one surface; rows themselves stay flat. */
.seller-orders-page .seller-order-row,
.seller-orders-page .seller-order-row:hover {
    box-shadow: none !important;
    transform: none !important;
}

/* Order-detail modal + internal cards use the same neutral elevation family. */




/* Product rows inside the modal stay secondary to their parent card. */


/* ------------------------------------------------------------------
   STATUS DROPDOWN — enterprise button-like select
   Existing #sellerOrderStatusFilter remains the actual native control.
   ------------------------------------------------------------------ */
.seller-orders-status-field {
    position: relative;
    display: block;
    width: 100%;
    min-width: 0;
    height: 40px;
    border-radius: 10px;
}

/* Remove browser-specific arrow so the custom chevron is consistent. */
.seller-orders-page #sellerOrderStatusFilter {
    width: 100% !important;
    height: 40px !important;
    min-height: 40px !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    border: 1px solid #D8DEE6 !important;
    border-radius: 10px !important;
    background: #FFFFFF !important;
    padding: 0 34px 0 37px !important;
    color: #344054 !important;
    font-family: inherit !important;
    font-size: 9.8px !important;
    font-weight: 650 !important;
    line-height: 40px !important;
    outline: none !important;
    cursor: pointer !important;
    box-shadow:
        0 1px 2px rgba(15, 23, 42, .02),
        0 4px 10px rgba(15, 23, 42, .035) !important;
    transition:
        border-color .14s ease,
        box-shadow .14s ease,
        background-color .14s ease !important;
}

/* Keep native option readability. */
.seller-orders-page #sellerOrderStatusFilter option {
    color: #344054;
    background: #FFFFFF;
    font-weight: 500;
}

/* Leading icon. */
.seller-orders-status-field-icon {
    position: absolute;
    top: 50%;
    left: 12px;
    z-index: 2;
    display: grid;
    width: 16px;
    height: 16px;
    place-items: center;
    transform: translateY(-50%);
    color: #98A2B3;
    pointer-events: none;
}

.seller-orders-status-field-icon svg {
    width: 14px;
    height: 14px;
}

/* Custom chevron. */
.seller-orders-status-field-chevron {
    position: absolute;
    top: 50%;
    right: 11px;
    z-index: 2;
    display: grid;
    width: 16px;
    height: 16px;
    place-items: center;
    transform: translateY(-50%);
    color: #667085;
    pointer-events: none;
}

.seller-orders-status-field-chevron svg {
    width: 13px;
    height: 13px;
}

/* Neutral professional interaction state — no movement. */
.seller-orders-status-field:hover #sellerOrderStatusFilter {
    border-color: #C9D1DB !important;
    background: #FFFFFF !important;
    box-shadow:
        0 1px 2px rgba(15, 23, 42, .025),
        0 6px 14px rgba(15, 23, 42, .045) !important;
}

.seller-orders-status-field:focus-within #sellerOrderStatusFilter {
    border-color: #C6A451 !important;
    background: #FFFFFF !important;
    box-shadow:
        0 0 0 3px rgba(201, 148, 22, .08),
        0 5px 14px rgba(15, 23, 42, .04) !important;
}

.seller-orders-status-field:focus-within .seller-orders-status-field-icon,
.seller-orders-status-field:focus-within .seller-orders-status-field-chevron {
    color: #A87510;
}

/* Search control follows the same neutral outlined family. */
.seller-orders-page #sellerOrderSearch {
    border-color: #D8DEE6 !important;
    background: #FFFFFF !important;
    box-shadow:
        0 1px 2px rgba(15, 23, 42, .02),
        0 4px 10px rgba(15, 23, 42, .035) !important;
}

.seller-orders-page #sellerOrderSearch:focus {
    border-color: #C6A451 !important;
    box-shadow:
        0 0 0 3px rgba(201, 148, 22, .08),
        0 5px 14px rgba(15, 23, 42, .04) !important;
}

/* Static button behavior: visual state only, no lift. */
.seller-orders-page .seller-orders-apply-filter,
.seller-orders-page .seller-orders-reset-filter,
.seller-orders-page .seller-orders-header-action,
.seller-orders-page .seller-order-view-button {
    transform: none !important;
}

.seller-orders-page .seller-orders-apply-filter:hover,
.seller-orders-page .seller-orders-reset-filter:hover,
.seller-orders-page .seller-orders-header-action:hover,
.seller-orders-page .seller-order-view-button:hover,
.seller-orders-page .seller-order-view-button:focus-visible {
    transform: none !important;
}

/* Neutral structural borders only; status colors remain semantic. */
.seller-orders-page .seller-orders-summary-card,
.seller-orders-page .seller-orders-filter-panel,
.seller-orders-page .seller-orders-workspace {
    outline: 0 !important;
}

/* Responsive parity with existing filter grid. */
@media (max-width: 767px)  {
    .seller-orders-status-field,
.seller-orders-page #sellerOrderStatusFilter {
        height: 42px !important;
        min-height: 42px !important;
    }

    .seller-orders-page #sellerOrderStatusFilter {
        line-height: 42px !important;
        font-size: 10px !important;
    }
}

/* Lighter floating depth on small screens. */
@media (max-width: 640px)  {
    .seller-orders-page .seller-orders-summary-card,
.seller-orders-page .seller-orders-filter-panel,
.seller-orders-page .seller-orders-workspace {
        box-shadow:
            0 1px 2px rgba(15, 23, 42, .03),
            0 7px 18px rgba(15, 23, 42, .06),
            inset 0 1px 0 rgba(255, 255, 255, .84) !important;
    }
}


/* ======================================================================
   ORDER MANAGEMENT — CUSTOM FILTER DROPDOWN + WHITE MODAL FINAL
   Front-end presentation only. Existing status select ID, values, routes,
   realtime refresh, order actions and backend behavior remain unchanged.
   ====================================================================== */

/* -------------------------
   Custom status dropdown
   ------------------------- */
.seller-orders-status-dropdown {
    position: relative;
    z-index: 40;
    min-width: 0;
    width: 100%;
}

/* Keep the original select in the DOM for existing filtering logic, but
   remove the browser/OS-native dropdown from the visual experience. */
.seller-orders-page #sellerOrderStatusFilter.seller-orders-status-native {
    position: absolute !important;
    width: 1px !important;
    min-width: 1px !important;
    height: 1px !important;
    min-height: 1px !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    clip: rect(0 0 0 0) !important;
    clip-path: inset(50%) !important;
    border: 0 !important;
    opacity: 0 !important;
    pointer-events: none !important;
}

.seller-orders-status-trigger {
    display: grid !important;
    width: 100% !important;
    height: 40px !important;
    min-height: 40px !important;
    grid-template-columns: 18px minmax(0, 1fr) 16px !important;
    align-items: center !important;
    gap: 9px !important;
    border: 1px solid #D8DEE6 !important;
    border-radius: 10px !important;
    background: #FFFFFF !important;
    padding: 0 11px 0 12px !important;
    color: #344054 !important;
    font-family: inherit !important;
    font-size: 9.8px !important;
    font-weight: 650 !important;
    text-align: left !important;
    box-shadow:
        0 1px 2px rgba(15, 23, 42, .025),
        0 4px 12px rgba(15, 23, 42, .04) !important;
    cursor: pointer !important;
    transform: none !important;
    transition:
        border-color .14s ease,
        box-shadow .14s ease,
        background-color .14s ease !important;
}

.seller-orders-status-trigger:hover {
    border-color: #C8D0DA !important;
    background: #FFFFFF !important;
    box-shadow:
        0 1px 2px rgba(15, 23, 42, .03),
        0 6px 15px rgba(15, 23, 42, .055) !important;
    transform: none !important;
}

.seller-orders-status-trigger:focus-visible,
.seller-orders-status-trigger[aria-expanded="true"] {
    outline: none !important;
    border-color: #D09A2F !important;
    background: #FFFFFF !important;
    box-shadow:
        0 0 0 3px rgba(208, 154, 47, .09),
        0 6px 16px rgba(15, 23, 42, .05) !important;
}

.seller-orders-status-trigger-icon,
.seller-orders-status-trigger-chevron {
    display: grid;
    place-items: center;
    color: #98A2B3;
    pointer-events: none;
}

.seller-orders-status-trigger-icon svg {
    width: 14px;
    height: 14px;
}

.seller-orders-status-trigger-chevron {
    color: #667085;
    transition: transform .15s ease, color .15s ease;
}

.seller-orders-status-trigger-chevron svg {
    width: 13px;
    height: 13px;
}

.seller-orders-status-trigger[aria-expanded="true"] .seller-orders-status-trigger-icon,
.seller-orders-status-trigger[aria-expanded="true"] .seller-orders-status-trigger-chevron {
    color: #B77A08;
}

.seller-orders-status-trigger[aria-expanded="true"] .seller-orders-status-trigger-chevron {
    transform: rotate(180deg);
}

.seller-orders-status-trigger-label {
    min-width: 0;
    overflow: hidden;
    color: #344054;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Floating custom menu: white only, no OS-blue selected row. */
.seller-orders-status-menu {
    position: absolute;
    top: calc(100% + 7px);
    left: 0;
    z-index: 80;
    width: max(100%, 196px);
    min-width: 196px;
    overflow: hidden;
    border: 1px solid #E1E6EC;
    border-radius: 12px;
    background: #FFFFFF;
    padding: 5px;
    box-shadow:
        0 18px 42px rgba(15, 23, 42, .12),
        0 4px 12px rgba(15, 23, 42, .04);
}

.seller-orders-status-menu[hidden] {
    display: none !important;
}

.seller-orders-status-option {
    display: grid;
    width: 100%;
    min-height: 36px;
    grid-template-columns: 8px minmax(0, 1fr) 18px;
    align-items: center;
    gap: 9px;
    border: 0;
    border-radius: 8px;
    background: #FFFFFF;
    padding: 0 9px;
    color: #475467;
    font-family: inherit;
    font-size: 9.5px;
    font-weight: 550;
    text-align: left;
    cursor: pointer;
    transform: none !important;
}

.seller-orders-status-option:hover,
.seller-orders-status-option:focus-visible {
    outline: none;
    background: #F8FAFC;
    color: #1F2937;
    transform: none !important;
}

.seller-orders-status-option[aria-selected="true"] {
    background: #FFF9EC;
    color: #9B6505;
    font-weight: 700;
}

.seller-orders-status-option-dot {
    width: 5px;
    height: 5px;
    border-radius: 999px;
    background: #D0D5DD;
}

.seller-orders-status-option[aria-selected="true"] .seller-orders-status-option-dot {
    background: #D59617;
    box-shadow: 0 0 0 3px rgba(213, 150, 23, .10);
}

.seller-orders-status-option-check {
    display: grid;
    width: 18px;
    height: 18px;
    place-items: center;
    color: transparent;
}

.seller-orders-status-option-check svg {
    width: 13px;
    height: 13px;
}

.seller-orders-status-option[aria-selected="true"] .seller-orders-status-option-check {
    color: #B97805;
}

/* -------------------------
   Order Details: white-only
   ------------------------- */








































/* No physical hover movement. */


@media (max-width: 767px)  {
    .seller-orders-status-trigger {
        height: 42px !important;
        min-height: 42px !important;
        font-size: 10px !important;
    }

    .seller-orders-status-menu {
        width: 100%;
        min-width: 100%;
    }
}


/* ======================================================================
   ORDER MANAGEMENT — DASHBOARD SHADOW REFERENCE FINAL PASS
   Front-end/CSS only. Search, custom status dropdown, realtime refresh,
   order workflow/actions, routes, forms and backend behavior unchanged.
   ====================================================================== */

.seller-orders-page {
    --orders-dashboard-border: #E5E7EB;
    --orders-dashboard-border-strong: #D1D5DB;
    --orders-dashboard-divider: #ECEFF2;

    /* Dashboard-style elevation:
       contact shadow + middle separation + broad ambient float. */
    --orders-dashboard-shadow:
        0 2px 5px rgba(15, 23, 42, .045),
        0 10px 24px rgba(15, 23, 42, .075),
        0 24px 54px rgba(15, 23, 42, .095),
        inset 0 1px 0 rgba(255, 255, 255, .92);

    --orders-dashboard-shadow-soft:
        0 1px 3px rgba(15, 23, 42, .035),
        0 7px 18px rgba(15, 23, 42, .06),
        0 15px 32px rgba(15, 23, 42, .065),
        inset 0 1px 0 rgba(255, 255, 255, .90);

    --orders-dashboard-shadow-inner:
        0 1px 2px rgba(15, 23, 42, .025),
        0 5px 14px rgba(15, 23, 42, .045);
}

/* ------------------------------------------------------------
   SUMMARY CARDS — medium floating elevation
   ------------------------------------------------------------ */
.seller-orders-page .seller-orders-summary-card {
    border: 1px solid var(--orders-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--orders-dashboard-shadow-soft) !important;
    transform: none !important;
}

.seller-orders-page .seller-orders-summary-card:hover {
    border-color: var(--orders-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--orders-dashboard-shadow-soft) !important;
    transform: none !important;
}

/* Summary icons keep their semantic colors but neutral structure. */
.seller-orders-page .seller-orders-summary-icon {
    border-color: var(--orders-dashboard-border) !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, .035) !important;
}

/* ------------------------------------------------------------
   FILTER BAR + ORDERS WORKSPACE — strongest page surfaces
   ------------------------------------------------------------ */
.seller-orders-page .seller-orders-filter-panel,
.seller-orders-page .seller-orders-workspace {
    border: 1px solid var(--orders-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--orders-dashboard-shadow) !important;
    transform: none !important;
}

.seller-orders-page .seller-orders-filter-panel:hover,
.seller-orders-page .seller-orders-workspace:hover {
    border-color: var(--orders-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--orders-dashboard-shadow) !important;
    transform: none !important;
}

/* Search/custom dropdown controls receive only restrained inner depth. */
.seller-orders-page #sellerOrderSearch,
.seller-orders-page .seller-orders-status-trigger,
.seller-orders-page .seller-orders-reset-filter {
    border-color: var(--orders-dashboard-border-strong) !important;
    background: #FFFFFF !important;
    box-shadow:
        0 1px 2px rgba(15, 23, 42, .025),
        0 5px 12px rgba(15, 23, 42, .04) !important;
    transform: none !important;
}

.seller-orders-page #sellerOrderSearch:hover,
.seller-orders-page .seller-orders-status-trigger:hover,
.seller-orders-page .seller-orders-reset-filter:hover {
    border-color: #C9D0D9 !important;
    background: #FFFFFF !important;
    box-shadow:
        0 1px 2px rgba(15, 23, 42, .03),
        0 6px 14px rgba(15, 23, 42, .05) !important;
    transform: none !important;
}

/* Focus/open states remain professional but structurally neutral. */
.seller-orders-page #sellerOrderSearch:focus,
.seller-orders-page .seller-orders-status-trigger:focus-visible,
.seller-orders-page .seller-orders-status-trigger[aria-expanded="true"] {
    border-color: #C4A35B !important;
    background: #FFFFFF !important;
    box-shadow:
        0 0 0 3px rgba(196, 163, 91, .08),
        0 5px 14px rgba(15, 23, 42, .04) !important;
}

/* Dropdown popover floats above the filter panel. */
.seller-orders-page .seller-orders-status-menu {
    border-color: #E1E5EA !important;
    background: #FFFFFF !important;
    box-shadow:
        0 4px 10px rgba(15, 23, 42, .05),
        0 18px 42px rgba(15, 23, 42, .14),
        0 32px 70px rgba(15, 23, 42, .08) !important;
}

/* ------------------------------------------------------------
   ORDER TABLE — workspace floats, rows stay flat
   ------------------------------------------------------------ */
.seller-orders-page .seller-orders-table-head,
.seller-orders-page .seller-orders-table-titlebar,
.seller-orders-page .seller-orders-table-footer {
    border-color: var(--orders-dashboard-divider) !important;
    background: #FFFFFF !important;
}

.seller-orders-page .seller-orders-table-head {
    background: #F8FAFC !important;
}

.seller-orders-page .seller-order-row {
    border-bottom-color: var(--orders-dashboard-divider) !important;
    background: #FFFFFF !important;
    box-shadow: none !important;
    transform: none !important;
}

/* Remove remaining cream row hover; use a quiet neutral gray. */
.seller-orders-page .seller-order-row:hover {
    border-bottom-color: var(--orders-dashboard-divider) !important;
    background: #F8FAFC !important;
    box-shadow: none !important;
    transform: none !important;
}

/* Product/image frames stay neutral. */
.seller-orders-page .seller-order-image-frame {
    border-color: var(--orders-dashboard-border) !important;
    background: #F8FAFC !important;
}

/* View action: neutral surface, gold only as icon/text feedback. */
.seller-orders-page .seller-order-view-button {
    border-color: var(--orders-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: 0 2px 7px rgba(15, 23, 42, .025) !important;
    transform: none !important;
}

.seller-orders-page .seller-order-view-button:hover,
.seller-orders-page .seller-order-view-button:focus-visible {
    border-color: var(--orders-dashboard-border-strong) !important;
    background: #F8FAFC !important;
    color: #B97805 !important;
    transform: none !important;
}

/* Header secondary actions should not turn cream. */
.seller-orders-page .seller-orders-header-action {
    border-color: var(--orders-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, .035) !important;
    transform: none !important;
}

.seller-orders-page .seller-orders-header-action:hover,
.seller-orders-page .seller-orders-header-action:focus-visible {
    border-color: var(--orders-dashboard-border-strong) !important;
    background: #F8FAFC !important;
    color: #9B6505 !important;
    transform: none !important;
}

/* ------------------------------------------------------------
   ORDER DETAIL MODAL — strongest overlay elevation
   ------------------------------------------------------------ */


/* Modal body stays white; no cream canvas. */


/* Primary modal information surfaces use medium floating depth. */


/* Product rows remain subordinate to their parent surfaces. */




/* Modal structural dividers stay neutral. */


/* Secondary modal actions: light gray, never cream. */


/* All structural surfaces remain static. */
.seller-orders-page .seller-orders-summary-card,
.seller-orders-page .seller-orders-filter-panel,
.seller-orders-page .seller-orders-workspace {
    outline: 0 !important;
}

/* Mobile: reduce ambient spread while preserving the floating hierarchy. */
@media (max-width: 640px)  {
    .seller-orders-page .seller-orders-summary-card {
        box-shadow:
            0 1px 3px rgba(15, 23, 42, .03),
            0 6px 14px rgba(15, 23, 42, .05),
            0 12px 26px rgba(15, 23, 42, .055) !important;
    }

    .seller-orders-page .seller-orders-filter-panel,
.seller-orders-page .seller-orders-workspace {
        box-shadow:
            0 2px 4px rgba(15, 23, 42, .035),
            0 8px 18px rgba(15, 23, 42, .06),
            0 18px 38px rgba(15, 23, 42, .07) !important;
    }

    
}


/* ======================================================================
   ORDER DETAILS MODAL — REMOVE DARK / BLACK STRUCTURAL LINES
   Visual-only fix. No size, layout, route, action, realtime, or backend
   behavior changes.
   ====================================================================== */

/* Outer modal shell: keep elevation, remove the dark outline completely. */


/* Header divider becomes a very light neutral gray instead of a dark line. */


/* Main left surface and right-side operational cards:
   explicit neutral borders prevent older dark-border layers from winning. */


/* The two Buyer/Delivery sections use only subtle dividers. */






/* Product row: light neutral edge only. */


/* Total divider should never render as a dark rule. */


/* Icon/control shells also stay neutral. */


/* Timeline keeps only its intentional light connector. */




/* Mobile keeps the same neutral border treatment. */



/* ======================================================================
   ORDER DETAILS — SECONDARY ACTION GOLD HOVER
   Message Buyer + Print Waybill keep their size/layout/route behavior.
   Only hover/focus palette changes; no movement.
   ====================================================================== */







/* Pressed state: slightly deeper gold, still static. */


/* ======================================================================
   SELLER ORDER MANAGEMENT — PICKUP REQUESTS UI PARITY (MAIN PAGE ONLY)
   Does not override #sellerOrderDetailModal or its workflow components.
   Consistent Poppins typography, cool gray/white/gold and table density.
   ====================================================================== */
.seller-orders-page {
    --sop-gold: #C88912;
    --sop-gold-hover: #B47B0E;
    --sop-charcoal: #2F3742;
    --sop-muted: #667383;
    --sop-border: #E5E7EB;
    --sop-soft: #F8FAFC;
    max-width: 1520px !important;
    margin: 0 auto !important;
    padding-bottom: 28px !important;
    font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
    color: var(--sop-charcoal) !important;
}
/* Header: text-based hierarchy and cart-style navigation actions. */
.seller-orders-page .seller-orders-header {
    min-height: 0 !important;
    align-items: flex-end !important;
    gap: 14px 24px !important;
    margin: 0 0 20px !important;
    padding: 0 0 14px !important;
    border-bottom: 1px solid #E9EDF1 !important;
}
.seller-orders-page .seller-orders-heading-group { display: block !important; }
.seller-orders-page .seller-orders-eyebrow {
    font-size: 10.5px !important;
    font-weight: 800 !important;
    line-height: 1.4 !important;
    letter-spacing: .14em !important;
    color: #A87316 !important;
}
.seller-orders-page .seller-orders-title {
    margin-top: 6px !important;
    font-size: clamp(27px, 2.3vw, 31px) !important;
    font-weight: 760 !important;
    line-height: 1.08 !important;
    letter-spacing: -.04em !important;
    color: #2E343B !important;
}
.seller-orders-page .seller-orders-title-accent {
    margin-left: .18em !important;
    color: #C88912 !important;
}
.seller-orders-page .seller-orders-subtitle {
    margin-top: 6px !important;
    max-width: 780px !important;
    font-size: 11.5px !important;
    line-height: 1.5 !important;
    color: #8793A1 !important;
}
.seller-orders-page .seller-orders-header-actions {
    display: flex !important;
    width: auto !important;
    flex-wrap: wrap !important;
    justify-content: flex-end !important;
    gap: 10px 24px !important;
    padding: 0 2px 0 0 !important;
}
.seller-orders-page .seller-orders-header-action {
    position: relative;
    top: -4px;
    display: inline-flex !important;
    width: auto !important;
    min-height: 0 !important;
    height: auto !important;
    align-items: center !important;
    justify-content: flex-start !important;
    gap: 8px !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    color: #59636E !important;
    font-size: 11.5px !important;
    font-weight: 750 !important;
    line-height: 1.35 !important;
    text-decoration: none !important;
    transform: none !important;
}
.seller-orders-page .seller-orders-header-action:hover,
.seller-orders-page .seller-orders-header-action:focus-visible {
    background: transparent !important;
    color: #C88912 !important;
}
.seller-orders-page .seller-orders-header-action:focus-visible {
    outline: 2px solid #C88912 !important;
    outline-offset: 4px !important;
}
.seller-orders-page .seller-orders-header-arrow {
    font-size: 18px;
    font-weight: 800;
    line-height: .8;
    transform: translateY(-1px);
}
/* Five cards preserve all five metrics, matching the Pickup Requests family. */
.seller-orders-page .seller-orders-summary {
    grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    gap: 14px !important;
    margin: 0 !important;
}
.seller-orders-page .seller-orders-summary-card,
.seller-orders-page .seller-orders-summary-card:hover {
    min-height: 110px !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 18px !important;
    background: #FFF !important;
    padding: 14px 16px !important;
    box-shadow: 0 3px 10px rgba(15,23,42,.035), 0 12px 30px rgba(15,23,42,.055) !important;
    contain: none !important;
    transform: none !important;
}
.seller-orders-page .seller-orders-summary-card > div { align-items: flex-start !important; gap: 12px !important; }
.seller-orders-page .seller-orders-summary-card p:first-child {
    font-size: 13px !important;
    line-height: 1.4 !important;
    font-weight: 650 !important;
    color: #637081 !important;
}
.seller-orders-page .seller-orders-summary-card p:nth-child(2) {
    margin-top: 7px !important;
    font-size: clamp(25px, 1.9vw, 30px) !important;
    line-height: 1.12 !important;
    font-weight: 650 !important;
    font-variant-numeric: tabular-nums;
    color: #1F2937 !important;
}
.seller-orders-page .seller-orders-summary-card p:nth-child(3) {
    margin-top: 6px !important;
    font-size: 11.5px !important;
    line-height: 1.5 !important;
    color: #8995A3 !important;
    white-space: normal !important;
    overflow: visible !important;
    text-overflow: clip !important;
}
.seller-orders-page .seller-orders-summary-icon {
    display: grid !important;
    width: 27px !important;
    min-width: 27px !important;
    height: 27px !important;
    flex: 0 0 27px !important;
    place-items: center !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}
.seller-orders-page .seller-orders-summary-icon svg {
    width: 27px !important;
    height: 27px !important;
    stroke-width: 1.8;
}
/* Flat four-control toolbar: standalone search / dropdown / actions. */
.seller-orders-page .seller-orders-filter-panel,
.seller-orders-page .seller-orders-filter-panel:hover {
    position: relative;
    z-index: 15;
    grid-template-columns: minmax(0, 1fr) minmax(185px, 210px) auto auto !important;
    align-items: center !important;
    gap: 12px !important;
    margin: 20px 0 0 !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}
.seller-orders-page #sellerOrderSearch,
.seller-orders-page .seller-orders-status-trigger,
.seller-orders-page .seller-orders-apply-filter,
.seller-orders-page .seller-orders-reset-filter {
    height: 48px !important;
    min-height: 48px !important;
    border-radius: 14px !important;
}
.seller-orders-page #sellerOrderSearch,
.seller-orders-page .seller-orders-status-trigger {
    border: 1px solid #DCE2E8 !important;
    background: #FFF !important;
    box-shadow: none !important;
    color: #3F4B59 !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
}
.seller-orders-page .seller-orders-search-field > span { left: 16px !important; width: 21px !important; height: 21px !important; color: #97A3B2 !important; }
.seller-orders-page .seller-orders-search-field svg { width: 21px !important; height: 21px !important; }
.seller-orders-page #sellerOrderSearch { padding: 0 14px 0 46px !important; }
.seller-orders-page #sellerOrderSearch::placeholder { font-size: 11.5px !important; color: #A0AAB6 !important; }
.seller-orders-page .seller-orders-status-dropdown { position: relative; z-index: 50; }
.seller-orders-page .seller-orders-status-trigger {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 8px !important;
    padding: 0 16px !important;
    text-align: left !important;
}
.seller-orders-page .seller-orders-status-trigger-label { color: #3F4B59 !important; }
.seller-orders-page .seller-orders-status-trigger-chevron svg { width: 17px !important; height: 17px !important; }
.seller-orders-page #sellerOrderSearch:hover,
.seller-orders-page .seller-orders-status-trigger:hover {
    border-color: #CFD6DE !important;
    background: #F8FAFC !important;
    box-shadow: none !important;
}
.seller-orders-page #sellerOrderSearch:focus,
.seller-orders-page .seller-orders-status-trigger:focus-visible,
.seller-orders-page .seller-orders-status-trigger[aria-expanded="true"] {
    border-color: #C88912 !important;
    background: #FFF !important;
    box-shadow: 0 0 0 1px rgba(200,137,18,.12) !important;
    outline: none !important;
}
.seller-orders-page .seller-orders-status-menu {
    top: calc(100% + 8px) !important;
    left: 0 !important;
    right: 0 !important;
    z-index: 100 !important;
    width: 100% !important;
    min-width: 100% !important;
    padding: 8px !important;
    border: 1px solid #E2E8F0 !important;
    border-radius: 14px !important;
    background: #FFF !important;
    box-shadow: 0 16px 32px rgba(15,23,42,.12), 0 2px 6px rgba(15,23,42,.05) !important;
}
.seller-orders-page .seller-orders-status-option {
    display: flex !important;
    min-height: 38px !important;
    width: 100% !important;
    gap: 0 !important;
    align-items: center !important;
    padding: 0 12px !important;
    border: 0 !important;
    border-radius: 10px !important;
    background: transparent !important;
    color: #475569 !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    text-align: left !important;
}
.seller-orders-page .seller-orders-status-option:hover,
.seller-orders-page .seller-orders-status-option:focus-visible {
    outline: none !important;
    background: #F8FAFC !important;
    color: #A56D0A !important;
}
.seller-orders-page .seller-orders-status-option[aria-selected="true"] {
    background: #F8FAFC !important;
    color: #2F3742 !important;
    font-weight: 700 !important;
}
.seller-orders-page .seller-orders-apply-filter {
    gap: 8px !important;
    padding: 0 18px !important;
    border: 1px solid #C88912 !important;
    background: #C88912 !important;
    box-shadow: none !important;
    color: #FFF !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    white-space: nowrap;
}
.seller-orders-page .seller-orders-apply-filter svg { width: 15px !important; height: 15px !important; }
.seller-orders-page .seller-orders-apply-filter:hover,
.seller-orders-page .seller-orders-apply-filter:focus-visible {
    border-color: #B47B0E !important;
    background: #B47B0E !important;
    box-shadow: none !important;
    outline-offset: 2px;
}
.seller-orders-page .seller-orders-reset-filter {
    min-width: 86px !important;
    padding: 0 18px !important;
    border: 1px solid #DCE2E8 !important;
    background: #FFF !important;
    color: #5F6D7C !important;
    box-shadow: none !important;
    font-size: 12px !important;
    font-weight: 700 !important;
}
.seller-orders-page .seller-orders-reset-filter:hover { border-color: #CFD6DE !important; background: #F8FAFC !important; }
/* Large, unified order table with fixed-height controls and readable text. */
.seller-orders-page .seller-orders-workspace,
.seller-orders-page .seller-orders-workspace:hover {
    position: relative;
    z-index: 1;
    margin-top: 20px !important;
    overflow: hidden !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 18px !important;
    background: #FFF !important;
    box-shadow: 0 3px 10px rgba(15,23,42,.035), 0 12px 30px rgba(15,23,42,.055) !important;
    contain: none !important;
}
.seller-orders-page .seller-orders-table-titlebar {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 20px !important;
    padding: 21px 23px !important;
    border-bottom: 1px solid #E9EDF1 !important;
    background: #FFF !important;
}
.seller-orders-page .seller-orders-table-titlebar h2 {
    margin: 0 !important;
    font-size: 18px !important;
    font-weight: 780 !important;
    color: #2F3742 !important;
}
.seller-orders-page .seller-orders-table-titlebar p {
    margin: 4px 0 0 !important;
    font-size: 11.5px !important;
    color: #8B96A4 !important;
}
.seller-orders-page .seller-orders-table-counter {
    flex-shrink: 0;
    font-size: 12px;
    font-weight: 600;
    color: #7B8794;
}
.seller-orders-page .seller-orders-table-scroll { width: 100%; overflow-x: auto; overflow-y: hidden; scrollbar-width: thin; }
.seller-orders-page .seller-orders-table-scroll:focus-visible { outline: 2px solid #C88912; outline-offset: -2px; }
.seller-orders-page .seller-orders-table-head,
.seller-orders-page .seller-order-grid {
    grid-template-columns: 36px minmax(250px,1.45fr) minmax(155px,.9fr) minmax(125px,.65fr) minmax(94px,.55fr) minmax(120px,.65fr) 52px !important;
    gap: 12px !important;
    min-width: 1060px !important;
    align-items: center !important;
    padding-left: 16px !important;
    padding-right: 16px !important;
}
.seller-orders-page .seller-orders-table-head {
    display: grid !important;
    min-height: 48px !important;
    padding-top: 13px !important;
    padding-bottom: 13px !important;
    border-bottom: 1px solid #E9EDF1 !important;
    background: #F8FAFC !important;
}
.seller-orders-page .seller-orders-table-head > span {
    font-size: 11px !important;
    font-weight: 750 !important;
    line-height: 1.4 !important;
    letter-spacing: .075em !important;
    color: #8591A0 !important;
    text-transform: uppercase !important;
}
.seller-orders-page .seller-order-row,
.seller-orders-page .seller-order-row:hover {
    border-bottom: 1px solid #EDF0F3 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    transform: none !important;
}
.seller-orders-page .seller-order-row { background: #FFF !important; }
.seller-orders-page .seller-order-row:hover { background: #F8FAFC !important; }
.seller-orders-page .seller-order-grid { padding-top: 17px !important; padding-bottom: 17px !important; }
.seller-orders-page .seller-order-index { font-size: 11.5px !important; color: #8793A1 !important; }
.seller-orders-page .seller-order-grid .seller-order-image-frame {
    width: 44px !important; height: 44px !important; flex: 0 0 44px !important;
    border: 1px solid #E5E7EB !important; border-radius: 9px !important;
    background: #F8FAFC !important;
}
.seller-orders-page .seller-order-grid .seller-order-image-frame + div p:first-child {
    color: #293440 !important; font-size: 12.5px !important; font-weight: 750 !important;
    line-height: 1.5 !important;
}
.seller-orders-page .seller-order-grid .seller-order-image-frame + div p:nth-child(2) {
    margin-top: 3px !important; font-size: 11.5px !important; line-height: 1.5 !important; color: #667383 !important;
}
.seller-orders-page .seller-order-grid .seller-order-image-frame + div p:nth-child(3) {
    margin-top: 3px !important; font-size: 11px !important; line-height: 1.5 !important; color: #8793A1 !important;
}
.seller-orders-page .seller-order-grid > div:nth-child(3) p:first-of-type:not(.lg\:hidden) {
    font-size: 12px !important; font-weight: 650 !important; color: #293440 !important;
}
.seller-orders-page .seller-order-grid > div:nth-child(3) p:last-child {
    margin-top: 4px !important; font-size: 11px !important; color: #8793A1 !important;
}
.seller-orders-page .seller-order-grid > div:nth-child(4) span {
    padding: 4px 9px !important;
    border-radius: 999px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    white-space: nowrap !important;
}
.seller-orders-page .seller-order-grid [data-order-status-code="new"],
.seller-orders-page .seller-order-grid [data-order-status-code="ready_for_pickup"] {
    background: #F8FAFC !important;
    border-color: #E9DFCD !important;
    color: #A56D0A !important;
}
.seller-orders-page .seller-order-grid > div:nth-child(5) p:last-child {
    font-size: 12.5px !important; color: #293440 !important; font-weight: 750 !important;
}
.seller-orders-page .seller-order-grid > div:nth-child(6) p:first-of-type:not(.lg\:hidden) {
    font-size: 11.5px !important; color: #293440 !important; font-weight: 650 !important;
}
.seller-orders-page .seller-order-grid > div:nth-child(6) p:last-child {
    margin-top: 4px !important; font-size: 11px !important; color: #8793A1 !important;
}
.seller-orders-page .seller-order-view-button {
    display: grid !important;
    width: 32px !important; height: 32px !important; min-width: 32px !important;
    place-items: center !important;
    border: 1px solid #DCE2E8 !important; border-radius: 10px !important;
    background: #FFF !important; color: #59636E !important; box-shadow: none !important;
    cursor: pointer !important;
}
.seller-orders-page .seller-order-view-button:hover,
.seller-orders-page .seller-order-view-button:focus-visible {
    border-color: #C88912 !important; background: #F8FAFC !important;
    color: #C88912 !important; transform: none !important;
    outline-offset: 2px !important;
}
.seller-orders-page .seller-order-view-button svg { width: 17px !important; height: 17px !important; }
/* Empty states and pagination echo the reference without fake actions. */
.seller-orders-page .seller-orders-empty-state,
.seller-orders-page .seller-orders-no-results {
    padding: 72px 20px !important;
    background: #FFF !important;
    text-align: center !important;
    color: #8793A1 !important;
}
.seller-orders-page .seller-orders-empty-icon {
    display: grid; width: 92px !important; height: 92px !important;
    margin: 0 auto !important; place-items: center;
    padding: 0 !important; border: 0 !important; border-radius: 0 !important;
    background: transparent !important; color: #A0AAB6 !important;
}
.seller-orders-page .seller-orders-empty-icon svg,
.seller-orders-page .seller-orders-no-results > svg {
    width: 58px !important; height: 58px !important; margin: 0 auto;
    stroke-width: 1.6;
}
.seller-orders-page .seller-orders-empty-state > p:nth-of-type(1),
.seller-orders-page .seller-orders-no-results > p:nth-of-type(1) {
    margin-top: 10px !important; font-size: 22px !important; font-weight: 700 !important;
    color: #59636E !important;
}
.seller-orders-page .seller-orders-empty-state > p:nth-of-type(2),
.seller-orders-page .seller-orders-no-results > p:nth-of-type(2) {
    margin: 8px auto 0 !important; max-width: 480px !important;
    font-size: 12px !important; font-weight: 400 !important;
    color: #8793A1 !important; line-height: 1.6 !important;
}
.seller-orders-page .seller-orders-no-results > svg { display: block; color: #A0AAB6; }
.seller-orders-page .seller-orders-table-footer {
    display: flex !important; min-height: 64px !important;
    flex-direction: row !important; align-items: center !important;
    justify-content: space-between !important;
    padding: 13px 20px !important; border-top: 1px solid #E9EDF1 !important;
    background: #FFF !important;
}
.seller-orders-page #sellerOrderResultCount {
    font-size: 11px !important; font-weight: 550 !important; color: #7B8794 !important;
}
.seller-orders-page .seller-orders-pagination { gap: 6px !important; }
.seller-orders-page .seller-orders-pagination button,
.seller-orders-page .seller-orders-pagination span {
    width: 34px !important; min-width: 34px !important; height: 34px !important;
    border-radius: 10px !important;
}
.seller-orders-page .seller-orders-pagination button {
    border: 1px solid #E5E7EB !important; background: #FFF !important; color: #A0AAB6 !important;
}
.seller-orders-page .seller-orders-pagination span {
    background: #C88912 !important; color: #FFF !important;
    font-size: 11.5px !important; font-weight: 700 !important;
    box-shadow: none !important;
}
/* Responsive cards and horizontally scrolling data table, never compressed columns. */
@media (max-width: 1180px)  {
    .seller-orders-page .seller-orders-summary { grid-template-columns: repeat(2,minmax(0,1fr)) !important; }
}
@media (max-width: 1023px)  {
    .seller-orders-page .seller-orders-table-head { display: grid !important; }
    .seller-orders-page .seller-order-grid { grid-template-columns: 36px minmax(250px,1.45fr) minmax(155px,.9fr) minmax(125px,.65fr) minmax(94px,.55fr) minmax(120px,.65fr) 52px !important; }
    .seller-orders-page .seller-order-grid > div:not(.seller-order-index) { grid-column: auto !important; }
    .seller-orders-page .seller-order-index { grid-row: auto !important; padding-top: 0 !important; }
    .seller-orders-page .seller-order-grid p.lg\:hidden { display: none !important; }
}
@media (max-width: 900px)  {
    .seller-orders-page .seller-orders-filter-panel { grid-template-columns: repeat(2,minmax(0,1fr)) !important; }
    .seller-orders-page .seller-orders-search-field,
.seller-orders-page .seller-orders-status-dropdown { grid-column: 1 / -1 !important; }
    .seller-orders-page .seller-orders-apply-filter,
.seller-orders-page .seller-orders-reset-filter { width: 100% !important; }
}
@media (max-width: 767px)  {
    .seller-orders-page .seller-orders-header { flex-direction: column !important; align-items: flex-start !important; gap: 10px !important; }
    .seller-orders-page .seller-orders-title { font-size: 25px !important; }
    .seller-orders-page .seller-orders-eyebrow { font-size: 9.5px !important; }
    .seller-orders-page .seller-orders-header-actions { display: flex !important; justify-content: flex-start !important; width: auto !important; }
    .seller-orders-page .seller-orders-header-action { top: 0 !important; font-size: 10.5px !important; }
    .seller-orders-page .seller-orders-header-arrow { font-size: 16px; }
    .seller-orders-page .seller-orders-summary-card p:first-child { font-size: 12.5px !important; }
    .seller-orders-page .seller-orders-summary-card p:nth-child(3) { font-size: 11px !important; }
    .seller-orders-page .seller-orders-empty-state,
.seller-orders-page .seller-orders-no-results { padding: 56px 16px !important; }
    .seller-orders-page .seller-orders-empty-icon { width: 64px !important; height: 64px !important; }
    .seller-orders-page .seller-orders-empty-icon svg,
.seller-orders-page .seller-orders-no-results > svg { width: 40px !important; height: 40px !important; }
    .seller-orders-page .seller-orders-empty-state > p:nth-of-type(1),
.seller-orders-page .seller-orders-no-results > p:nth-of-type(1) { font-size: 20px !important; }
    .seller-orders-page .seller-orders-empty-state > p:nth-of-type(2),
.seller-orders-page .seller-orders-no-results > p:nth-of-type(2) { font-size: 11px !important; }
}
@media (max-width: 540px)  {
    .seller-orders-page .seller-orders-summary { grid-template-columns: 1fr !important; gap: 11px !important; }
    .seller-orders-page .seller-orders-summary-card { min-height: 100px !important; }
    .seller-orders-page .seller-orders-table-titlebar { align-items: flex-start !important; flex-direction: column !important; padding: 17px 16px !important; gap: 6px !important; }
    .seller-orders-page .seller-orders-table-footer { gap: 12px !important; flex-direction: column !important; align-items: flex-start !important; }
    .seller-orders-page .seller-orders-pagination { align-self: flex-end !important; }
}
@media (prefers-reduced-motion: reduce)  {
    .seller-orders-page * { transition-duration: .01ms !important; animation-duration: .01ms !important; }
}


/* Final seller orders list: actual selection and 10-row pagination */
.seller-orders-page .seller-orders-table-head,
.seller-orders-page .seller-order-grid { grid-template-columns: 42px minmax(250px,1.4fr) minmax(150px,.8fr) 112px 96px 112px 48px !important; }
.seller-orders-page .seller-orders-check-wrap { display: inline-flex; align-items: center; justify-content: center; min-width: 20px; min-height: 32px; cursor: pointer; }
.seller-orders-page .seller-orders-check-wrap input { width: 17px; height: 17px; margin: 0; cursor: pointer; accent-color: #C88912; }
.seller-orders-page .seller-orders-check-wrap input:focus-visible { outline: 2px solid #C88912; outline-offset: 3px; }
.seller-orders-page .seller-order-row.is-selected { background: #F8FAFC !important; }
.seller-orders-page .seller-orders-selection { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid #E5E7EB; padding: 10px 19px; background: #F8FAFC; color: #465362; font-size: 11.5px; font-weight: 600; }
.seller-orders-page .seller-orders-selection[hidden],
.seller-orders-page [data-order-pagination][hidden] { display: none !important; }
.seller-orders-page .seller-orders-selection-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; }
.seller-orders-page .seller-orders-selection-actions button { min-height: 32px; border-radius: 9px; border: 1px solid #DCE2E8; background: #FFFFFF; color: #465362; padding: 0 12px; font-size: 11px; font-weight: 650; cursor: pointer; }
.seller-orders-page .seller-orders-selection-actions [data-order-review-selected] { background: #C88912; border-color: #C88912; color: #FFFFFF; }
.seller-orders-page .seller-orders-selection-actions button:hover { border-color: #C88912; }
.seller-orders-page .seller-orders-selection-actions [data-order-review-selected]:hover { background: #B47B0E; }
.seller-orders-page .seller-orders-selection-actions button:focus-visible { outline: 2px solid #C88912; outline-offset: 2px; }
.seller-orders-page .seller-orders-page-numbers { display: flex; gap: 6px; align-items: center; }
.seller-orders-page .seller-orders-pagination button.is-current { color: #FFFFFF !important; background: #C88912 !important; border-color: #C88912 !important; box-shadow: 0 5px 12px rgba(200,137,18,.14); }
.seller-orders-page .seller-orders-pagination button:disabled { opacity: .42 !important; cursor: not-allowed; }
.seller-orders-page .seller-order-grid [data-order-status-code="ready_for_pickup"] { color: #92600B !important; background: #F2F6FA !important; border-color: #D7DFE7 !important; }
@media (min-width: 1024px) and (max-width: 1279px) {
    .seller-orders-page .seller-orders-table-head, .seller-orders-page .seller-order-grid { grid-template-columns: 38px minmax(200px,1.35fr) minmax(120px,.78fr) 98px 82px 96px 40px !important; }
}
@media (max-width: 1023px) {
    .seller-orders-page .seller-orders-table-head { display: grid !important; }
    .seller-orders-page .seller-orders-table-head, .seller-orders-page .seller-order-grid { grid-template-columns: 42px minmax(250px,1.4fr) minmax(150px,.8fr) 112px 96px 112px 48px !important; min-width: 950px; }
    .seller-orders-page .seller-order-grid > div:not(.seller-order-index) { grid-column: auto !important; }
}
@media (max-width: 640px) {
    .seller-orders-page .seller-orders-selection { padding: 10px 13px; }
    .seller-orders-page .seller-orders-selection-actions { width: 100%; }
}


/* V3 — accessible selected rows and semantic order-status colors. */
.seller-orders-page .seller-orders-check-wrap input[type="checkbox"] {
    appearance: none !important;
    -webkit-appearance: none !important;
    display: inline-grid !important;
    place-items: center !important;
    width: 18px !important;
    height: 18px !important;
    min-width: 18px !important;
    margin: 0 !important;
    padding: 0 !important;
    border: 1.5px solid #CBD5E1 !important;
    border-radius: 5px !important;
    background: #FFFFFF !important;
    cursor: pointer !important;
    vertical-align: middle !important;
    transition: background-color .12s ease, border-color .12s ease !important;
}
.seller-orders-page .seller-orders-check-wrap input[type="checkbox"]::after {
    content: "";
    box-sizing: content-box;
    width: 4px;
    height: 8px;
    border: solid #FFFFFF;
    border-width: 0 2px 2px 0;
    transform: translateY(-1px) rotate(45deg);
    opacity: 0;
}
.seller-orders-page .seller-orders-check-wrap input[type="checkbox"]:checked,
.seller-orders-page .seller-orders-check-wrap input[type="checkbox"]:indeterminate {
    border-color: #C88912 !important;
    background: #C88912 !important;
}
.seller-orders-page .seller-orders-check-wrap input[type="checkbox"]:checked::after { opacity: 1; }
.seller-orders-page .seller-orders-check-wrap input[type="checkbox"]:indeterminate::after {
    opacity: 1;
    width: 9px;
    height: 2px;
    border: 0;
    border-radius: 999px;
    background: #FFFFFF;
    transform: none;
}
.seller-orders-page .seller-orders-check-wrap input[type="checkbox"]:focus-visible {
    outline: 2px solid #C88912 !important;
    outline-offset: 3px !important;
}
.seller-orders-page .seller-orders-check-wrap input[type="checkbox"]:disabled { opacity: .5; cursor: not-allowed !important; }

/* Explicit status tones override legacy cream and tiny-label rules. */
.seller-orders-page .seller-order-grid [data-order-status-code] {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    min-height: 27px;
    max-width: 100%;
    padding: 5px 10px !important;
    border: 1px solid #DBE2EA !important;
    border-radius: 999px !important;
    background: #F1F5F9 !important;
    color: #475569 !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    line-height: 1.3 !important;
    text-align: center;
    white-space: normal;
}
.seller-orders-page .seller-order-grid [data-order-status-code="new"] {
    background: #F1F5F9 !important;
    border-color: #DCE3EC !important;
    color: #475569 !important;
}
.seller-orders-page .seller-order-grid [data-order-status-code="preparing"] {
    background: #EFF6FF !important;
    border-color: #BFDBFE !important;
    color: #1D4ED8 !important;
}
.seller-orders-page .seller-order-grid [data-order-status-code="ready_for_pickup"] {
    background: #F8FAFC !important;
    border-color: #E2D3B1 !important;
    color: #A56D0A !important;
}
.seller-orders-page .seller-order-grid [data-order-status-code="courier_accepted"],
.seller-orders-page .seller-order-grid [data-order-status-code="heading_pickup"],
.seller-orders-page .seller-order-grid [data-order-status-code="arrived_pickup"] {
    background: #F5F3FF !important;
    border-color: #DDD6FE !important;
    color: #6D28D9 !important;
}
.seller-orders-page .seller-order-grid [data-order-status-code="in_transit"] {
    background: #E8F4FF !important;
    border-color: #BAE0FF !important;
    color: #075985 !important;
}
.seller-orders-page .seller-order-grid [data-order-status-code="arrived_buyer"] {
    background: #ECFEFF !important;
    border-color: #A5F3FC !important;
    color: #0E7490 !important;
}
.seller-orders-page .seller-order-grid [data-order-status-code="delivered"] {
    background: #ECFDF3 !important;
    border-color: #BBF7D0 !important;
    color: #166534 !important;
}
.seller-orders-page .seller-order-grid [data-order-status-code="cancelled"] {
    background: #FEF2F2 !important;
    border-color: #FECACA !important;
    color: #B91C1C !important;
}
/* Bulk preparation action appears only once at least one row is selected. */
.seller-orders-page .seller-orders-selection-actions .seller-orders-bulk-form { display: inline-flex; align-items: center; margin: 0; }
.seller-orders-page .seller-orders-selection-actions [data-order-prepare-selected] {
    display: inline-flex;
    gap: 7px;
    align-items: center;
    min-height: 36px;
    padding: 0 14px;
    border: 1px solid #C88912;
    border-radius: 9px;
    background: #C88912;
    color: #FFFFFF;
    font: 700 11px/1.3 Poppins,sans-serif;
    cursor: pointer;
}
.seller-orders-page .seller-orders-selection-actions [data-order-prepare-selected]:hover:not(:disabled) {
    background: #B47B0E;
    border-color: #B47B0E;
}
.seller-orders-page .seller-orders-selection-actions [data-order-prepare-selected]:disabled {
    border-color: #CBD5E1;
    background: #E2E8F0;
    color: #64748B;
    cursor: not-allowed;
}
.seller-orders-page .seller-orders-selection-actions [data-order-prepare-selected]:focus-visible {
    outline: 2px solid #C88912 !important;
    outline-offset: 3px;
}
.seller-orders-page .seller-orders-selection-hint {
    color: #64748B;
    font-size: 10.5px;
    font-weight: 500;
    line-height: 1.4;
}
@media (max-width: 640px) {
    .seller-orders-page .seller-orders-selection-actions .seller-orders-bulk-form { flex: 1 1 100%; }
    .seller-orders-page .seller-orders-selection-actions [data-order-prepare-selected] { width: 100%; justify-content: center; }
}

/* ======================================================================
   SELLER ORDER MANAGEMENT — FILTER HOVER POSITION LOCK (CSS ONLY)
   Keep the filter row and its controls in one fixed position on pointer
   hover, keyboard focus, and when the custom status menu is expanded.
   Does not change dropdown options, search, filtering or order workflow.
   ====================================================================== */
.seller-orders-page .seller-orders-filter-panel,
.seller-orders-page .seller-orders-filter-panel:hover,
.seller-orders-page .seller-orders-filter-panel:focus-within {
    margin-top: 20px !important;
    transform: none !important;
    translate: none !important;
}

/* Preserve the status dropdown's relative positioning and stacking. */
.seller-orders-page .seller-orders-filter-panel > .seller-orders-search-field,
.seller-orders-page .seller-orders-filter-panel > .seller-orders-status-dropdown,
.seller-orders-page .seller-orders-filter-panel > .seller-orders-apply-filter,
.seller-orders-page .seller-orders-filter-panel > .seller-orders-reset-filter {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
    transform: none !important;
    translate: none !important;
}

/* The clickable controls do not lift or slide on hover/focus/press. */
.seller-orders-page .seller-orders-filter-panel #sellerOrderSearch,
.seller-orders-page .seller-orders-filter-panel .seller-orders-status-trigger,
.seller-orders-page .seller-orders-filter-panel .seller-orders-apply-filter,
.seller-orders-page .seller-orders-filter-panel .seller-orders-reset-filter,
.seller-orders-page .seller-orders-filter-panel #sellerOrderSearch:hover,
.seller-orders-page .seller-orders-filter-panel #sellerOrderSearch:focus,
.seller-orders-page .seller-orders-filter-panel .seller-orders-status-trigger:hover,
.seller-orders-page .seller-orders-filter-panel .seller-orders-status-trigger:focus-visible,
.seller-orders-page .seller-orders-filter-panel .seller-orders-status-trigger[aria-expanded="true"],
.seller-orders-page .seller-orders-filter-panel .seller-orders-apply-filter:hover,
.seller-orders-page .seller-orders-filter-panel .seller-orders-apply-filter:focus-visible,
.seller-orders-page .seller-orders-filter-panel .seller-orders-reset-filter:hover,
.seller-orders-page .seller-orders-filter-panel .seller-orders-reset-filter:focus-visible {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
    transform: none !important;
    translate: none !important;
}

@endverbatim
</style>
{{-- Dedicated dialog design is maintained separately from the orders table. --}}
@include('seller.order-partials.order-detail-modal-styles')
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
            <div class="min-w-0">
                <p class="seller-orders-eyebrow">ORDER MANAGEMENT</p>
                <h1 class="seller-orders-title">
                    <span>Seller</span><span class="seller-orders-title-accent">Orders</span>
                </h1>
               
            </div>
        </div>

        <div class="seller-orders-header-actions">
            <a href="{{ route('seller.messages') }}" class="seller-orders-header-action">
                Buyer Inbox <span class="seller-orders-header-arrow" aria-hidden="true">&gt;</span>
            </a>
            <a href="{{ route('seller.reviews') }}" wire:navigate class="seller-orders-header-action">
                Product Reviews <span class="seller-orders-header-arrow" aria-hidden="true">&gt;</span>
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

                    <span class="seller-orders-summary-icon {{ $card['iconText'] }}">
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

        <div class="seller-orders-status-dropdown" data-order-status-dropdown>
            {{-- Preserve the original select as the filter state source.
                 It stays synchronized with the custom dropdown below. --}}
            <select
                id="sellerOrderStatusFilter"
                class="seller-orders-filter-select seller-orders-status-native"
                tabindex="-1"
                aria-hidden="true"
            >
                <option value="all">All Status</option>
                <option value="new">New</option>
                <option value="preparing">Preparing</option>
                <option value="ready_for_pickup">Ready Pickup</option>
                <option value="courier">With Courier</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>

            <button
                type="button"
                class="seller-orders-status-trigger"
                data-order-status-trigger
                aria-haspopup="listbox"
                aria-expanded="false"
                aria-controls="sellerOrderStatusMenu"
            >
                <span class="seller-orders-status-trigger-label" data-order-status-label>All Status</span>

                <span class="seller-orders-status-trigger-chevron" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m7 10 5 5 5-5"></path>
                    </svg>
                </span>
            </button>

            <div
                id="sellerOrderStatusMenu"
                class="seller-orders-status-menu"
                data-order-status-menu
                role="listbox"
                aria-label="Filter orders by status"
                hidden
            >
                @foreach ([
                    'all' => 'All Status',
                    'new' => 'New',
                    'preparing' => 'Preparing',
                    'ready_for_pickup' => 'Ready Pickup',
                    'courier' => 'With Courier',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ] as $statusValue => $statusLabel)
                    <button
                        type="button"
                        class="seller-orders-status-option"
                        data-order-status-option
                        data-value="{{ $statusValue }}"
                        role="option"
                        aria-selected="{{ $statusValue === 'all' ? 'true' : 'false' }}"
                    >
                        <span class="seller-orders-status-option-label">{{ $statusLabel }}</span>
                    </button>
                @endforeach
            </div>
        </div>

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
        <div class="seller-orders-table-titlebar">
            <div>
                <h2>Seller Order Records</h2>
                <p>Most recent buyer orders and fulfillment activity.</p>
            </div>
            <span class="seller-orders-table-counter" data-order-table-counter>Showing {{ $orders->count() }} of {{ $orders->count() }}</span>
        </div>
        <div class="seller-orders-selection" data-order-selection-bar hidden aria-live="polite">
            <span><strong data-order-selected-count>0</strong> selected <span class="seller-orders-selection-hint" data-order-selection-hint></span></span>
            <div class="seller-orders-selection-actions">
                <form method="POST" action="{{ route('seller.orders.prepare-selected') }}" id="sellerOrdersBulkPrepareForm" class="seller-orders-bulk-form" data-order-bulk-prepare-form>
                    @csrf
                    <button type="submit" data-order-prepare-selected disabled title="Only New orders can be prepared together.">Prepare selected orders</button>
                </form>
                <button type="button" data-order-review-selected>Review selected</button>
                <button type="button" data-order-show-selected aria-pressed="false">Show selected only</button>
                <button type="button" data-order-clear-selection>Clear selection</button>
            </div>
        </div>
        <div class="seller-orders-table-scroll" tabindex="0" aria-label="Orders table, scroll horizontally to see all columns">
        {{-- DESKTOP TABLE HEADER --}}
        <div class="seller-orders-table-head">
            <label class="seller-orders-check-wrap" title="Select all visible orders">
                <input type="checkbox" data-order-select-all aria-label="Select all orders on this page">
            </label>
            <span>Order / Product</span>
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
                    data-order-id="{{ $order->id }}"
                    data-order-current-status="{{ $order->status }}"
                    data-search="{{ $searchText }}"
                    data-status="{{ $filterStatus }}"
                >
                    <div class="seller-order-grid">
                        <label class="seller-orders-check-wrap" title="Select order {{ $order->order_number }}">
                            <input type="checkbox" data-order-select aria-label="Select order {{ $order->order_number }}" value="{{ $order->id }}">
                        </label>

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
                            <span data-order-status-code="{{ $order->status }}" class="inline-flex rounded-full border px-2.5 py-1.5 text-[9px] font-medium {{ $statusTone($order->status) }}">
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

                @include('seller.order-partials.order-detail-modal', ['order' => $order, 'items' => $items, 'statusTone' => $statusTone, 'progressValue' => $progressValue])
            @empty
                <div class="seller-orders-empty-state">
                    <div class="seller-orders-empty-icon">
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
            <div id="sellerOrderNoResults" class="seller-orders-no-results hidden">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <circle cx="10.8" cy="10.8" r="7"></circle><path d="m16 16 5 5"></path>
                </svg>
                <p>No matching orders</p>
                <p>Try another order ID, buyer name, or status filter.</p>
            </div>
        @endif

        </div>{{-- /seller-orders-table-scroll --}}
        <div class="seller-orders-table-footer">
            <p id="sellerOrderResultCount">Showing {{ $orders->count() }} of {{ $orders->count() }} orders</p>
            <nav class="seller-orders-pagination" aria-label="Order pagination" data-order-pagination>
                <button type="button" data-order-page-prev disabled aria-label="Previous page">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"></path></svg>
                </button>
                <div data-order-page-numbers class="seller-orders-page-numbers"></div>
                <button type="button" data-order-page-next disabled aria-label="Next page">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 18 6-6-6-6"></path></svg>
                </button>
            </nav>
        </div>
    </section>
    </div>
</div>
    </div>
</div>


@include('seller.order-partials.order-detail-modal-shell')

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

@include('seller.order-partials.order-detail-modal-script')

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
    const ORDER_PAGE_SIZE = 10;
    const selectedOrderIds = new Set();
    let currentOrderPage = 1;
    let onlySelectedOrders = false;

    function getStatusDropdownElements() {
        const root = document.querySelector('[data-order-status-dropdown]');

        return {
            root,
            select: document.getElementById('sellerOrderStatusFilter'),
            trigger: root?.querySelector('[data-order-status-trigger]') || null,
            label: root?.querySelector('[data-order-status-label]') || null,
            menu: root?.querySelector('[data-order-status-menu]') || null,
            options: root ? Array.from(root.querySelectorAll('[data-order-status-option]')) : [],
        };
    }

    function syncStatusDropdownUi() {
        const { select, label, options } = getStatusDropdownElements();
        if (!select) return;

        const selectedValue = select.value || 'all';
        const selectedText = select.options[select.selectedIndex]?.text || 'All Status';

        if (label) label.textContent = selectedText;

        options.forEach((option) => {
            option.setAttribute(
                'aria-selected',
                option.dataset.value === selectedValue ? 'true' : 'false'
            );
        });
    }

    function closeStatusDropdown({ focusTrigger = false } = {}) {
        const { trigger, menu } = getStatusDropdownElements();
        if (!trigger || !menu) return;

        menu.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');

        if (focusTrigger) {
            trigger.focus({ preventScroll: true });
        }
    }

    function openStatusDropdown() {
        const { trigger, menu, options } = getStatusDropdownElements();
        if (!trigger || !menu) return;

        menu.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');

        const selected = options.find((option) => option.getAttribute('aria-selected') === 'true');
        window.requestAnimationFrame(() => {
            (selected || options[0])?.focus({ preventScroll: true });
        });
    }

    function toggleStatusDropdown() {
        const { trigger, menu } = getStatusDropdownElements();
        if (!trigger || !menu) return;

        if (menu.hidden) {
            openStatusDropdown();
        } else {
            closeStatusDropdown({ focusTrigger: true });
        }
    }


    const { closeOrderModal, openOrderModal, trapModalFocus } = createSellerOrderModalController(modal, modalContent);

    function orderRows() {
        return [...document.querySelectorAll('[data-order-row]')];
    }

    function updateSelectionUi() {
        const rows = orderRows();
        const visibleRows = rows.filter((row) => !row.classList.contains('hidden'));
        const selectAll = document.querySelector('[data-order-select-all]');
        const selectedVisible = visibleRows.filter((row) => selectedOrderIds.has(row.dataset.orderId));
        rows.forEach((row) => {
            const selected = selectedOrderIds.has(row.dataset.orderId);
            const checkbox = row.querySelector('[data-order-select]');
            if (checkbox) checkbox.checked = selected;
            row.classList.toggle('is-selected', selected);
        });
        if (selectAll) {
            selectAll.checked = visibleRows.length > 0 && selectedVisible.length === visibleRows.length;
            selectAll.indeterminate = selectedVisible.length > 0 && selectedVisible.length < visibleRows.length;
            selectAll.disabled = visibleRows.length === 0;
        }
        const toolbar = document.querySelector('[data-order-selection-bar]');
        if (toolbar) toolbar.hidden = selectedOrderIds.size === 0;
        const count = document.querySelector('[data-order-selected-count]');
        if (count) count.textContent = String(selectedOrderIds.size);
        const selectedRows = rows.filter((row) => selectedOrderIds.has(row.dataset.orderId));
        const prepareEligible = selectedRows.length > 0 && selectedRows.every((row) => row.dataset.orderCurrentStatus === 'new');
        const prepareButton = document.querySelector('[data-order-prepare-selected]');
        const selectedHint = document.querySelector('[data-order-selection-hint]');
        if (prepareButton) {
            prepareButton.disabled = !prepareEligible || selectedRows.length > 100;
            prepareButton.textContent = `Prepare selected orders (${selectedRows.length})`;
            prepareButton.title = selectedRows.length > 100
                ? 'Maximum 100 orders per bulk preparation.'
                : prepareEligible ? 'Prepare all selected New orders' : 'Select only New orders to prepare together.';
        }
        if (selectedHint) {
            selectedHint.textContent = selectedRows.length > 100
                ? '· Select up to 100 orders at a time'
                : (!prepareEligible && selectedRows.length > 0 ? '· Only New orders can be prepared' : '');
        }
        const showSelected = document.querySelector('[data-order-show-selected]');
        if (showSelected) {
            showSelected.setAttribute('aria-pressed', String(onlySelectedOrders));
            showSelected.textContent = onlySelectedOrders ? 'Show all orders' : 'Show selected only';
        }
    }

    function renderOrderPagination(pageCount) {
        const pageNumbers = document.querySelector('[data-order-page-numbers]');
        const prev = document.querySelector('[data-order-page-prev]');
        const next = document.querySelector('[data-order-page-next]');
        const nav = document.querySelector('[data-order-pagination]');
        if (prev) prev.disabled = currentOrderPage <= 1;
        if (next) next.disabled = currentOrderPage >= pageCount;
        if (nav) nav.hidden = pageCount <= 1;
        if (!pageNumbers) return;
        pageNumbers.replaceChildren();
        const start = Math.max(1, Math.min(currentOrderPage - 2, pageCount - 4));
        const end = Math.min(pageCount, start + 4);
        for (let i = start; i <= end; i++) {
            const button = document.createElement('button');
            button.type = 'button';
            button.dataset.orderPage = String(i);
            button.textContent = String(i);
            button.className = 'seller-orders-page-button' + (i === currentOrderPage ? ' is-current' : '');
            if (i === currentOrderPage) button.setAttribute('aria-current', 'page');
            button.setAttribute('aria-label', 'Page ' + i);
            pageNumbers.append(button);
        }
    }

    function applyOrderFilters({ resetPage = false } = {}) {
        const searchInput = document.getElementById('sellerOrderSearch');
        const statusFilter = document.getElementById('sellerOrderStatusFilter');
        const resultCount = document.getElementById('sellerOrderResultCount');
        const noResults = document.getElementById('sellerOrderNoResults');
        const rows = orderRows();
        const query = (searchInput?.value || '').trim().toLowerCase();
        const selectedStatus = statusFilter?.value || 'all';
        const matches = rows.filter((row) =>
            (!query || (row.dataset.search || '').includes(query)) &&
            (selectedStatus === 'all' || row.dataset.status === selectedStatus) &&
            (!onlySelectedOrders || selectedOrderIds.has(row.dataset.orderId))
        );
        const pageCount = Math.max(1, Math.ceil(matches.length / ORDER_PAGE_SIZE));
        if (resetPage) currentOrderPage = 1;
        currentOrderPage = Math.min(Math.max(1, currentOrderPage), pageCount);
        const offset = (currentOrderPage - 1) * ORDER_PAGE_SIZE;
        rows.forEach((row) => row.classList.add('hidden'));
        matches.slice(offset, offset + ORDER_PAGE_SIZE).forEach((row) => row.classList.remove('hidden'));
        const from = matches.length ? offset + 1 : 0;
        const to = Math.min(offset + ORDER_PAGE_SIZE, matches.length);
        if (resultCount) resultCount.textContent = `Showing ${from}–${to} of ${matches.length} orders`;
        const counter = document.querySelector('[data-order-table-counter]');
        if (counter) counter.textContent = `Showing ${matches.length} of ${rows.length}`;
        noResults?.classList.toggle('hidden', rows.length === 0 || matches.length !== 0);
        renderOrderPagination(pageCount);
        updateSelectionUi();
        syncStatusDropdownUi();
    }

    function captureUiState() {
        return {
            search: document.getElementById('sellerOrderSearch')?.value || '',
            status: document.getElementById('sellerOrderStatusFilter')?.value || 'all',
            openTemplate: modal && !modal.classList.contains('hidden') ? (modal.dataset.openTemplate || '') : '',
            selected: [...selectedOrderIds],
            page: currentOrderPage,
            onlySelected: onlySelectedOrders,
        };
    }

    function restoreUiState(state) {
        const searchInput = document.getElementById('sellerOrderSearch');
        const statusFilter = document.getElementById('sellerOrderStatusFilter');
        if (searchInput) searchInput.value = state.search;
        if (statusFilter) statusFilter.value = state.status;
        selectedOrderIds.clear();
        const availableIds = new Set(orderRows().map((row) => row.dataset.orderId));
        (state.selected || []).forEach((id) => { if (availableIds.has(String(id))) selectedOrderIds.add(String(id)); });
        onlySelectedOrders = Boolean(state.onlySelected && selectedOrderIds.size);
        currentOrderPage = Math.max(1, Number(state.page) || 1);
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
        const statusTrigger = event.target.closest('[data-order-status-trigger]');
        if (statusTrigger) {
            event.preventDefault();
            toggleStatusDropdown();
            return;
        }

        const statusOption = event.target.closest('[data-order-status-option]');
        if (statusOption) {
            event.preventDefault();

            const { select } = getStatusDropdownElements();
            if (select) {
                select.value = statusOption.dataset.value || 'all';
                syncStatusDropdownUi();
                closeStatusDropdown({ focusTrigger: true });
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }

            return;
        }

        if (!event.target.closest('[data-order-status-dropdown]')) {
            closeStatusDropdown();
        }

        const pageButton = event.target.closest('[data-order-page]');
        if (pageButton) {
            currentOrderPage = Number(pageButton.dataset.orderPage) || 1;
            applyOrderFilters();
            return;
        }
        if (event.target.closest('[data-order-page-prev]')) {
            currentOrderPage = Math.max(1, currentOrderPage - 1);
            applyOrderFilters();
            return;
        }
        if (event.target.closest('[data-order-page-next]')) {
            currentOrderPage += 1;
            applyOrderFilters();
            return;
        }
        if (event.target.closest('[data-order-show-selected]')) {
            onlySelectedOrders = !onlySelectedOrders;
            applyOrderFilters({ resetPage: true });
            return;
        }
        if (event.target.closest('[data-order-clear-selection]')) {
            selectedOrderIds.clear();
            onlySelectedOrders = false;
            applyOrderFilters({ resetPage: true });
            return;
        }
        if (event.target.closest('[data-order-review-selected]')) {
            const target = orderRows().find((row) => selectedOrderIds.has(row.dataset.orderId));
            const templateId = target?.querySelector('[data-order-template]')?.dataset.orderTemplate;
            if (templateId) openOrderModal(templateId, false, target.querySelector('[data-order-template]'));
            return;
        }

        const viewButton = event.target.closest('.seller-order-view-button');
        if (viewButton) {
            openOrderModal(viewButton.dataset.orderTemplate || '', false, viewButton);
            return;
        }

        if (event.target.closest('[data-close-order-modal]')) {
            closeOrderModal();
        }
    }, { signal });

    document.addEventListener('submit', function (event) {
        const form = event.target.closest?.('[data-order-bulk-prepare-form]');
        if (!form) return;
        const selectedRows = orderRows().filter((row) => selectedOrderIds.has(row.dataset.orderId));
        const validSelection = selectedRows.length > 0 && selectedRows.length <= 100 &&
            selectedRows.every((row) => row.dataset.orderCurrentStatus === 'new');
        if (!validSelection) {
            event.preventDefault();
            updateSelectionUi();
            return;
        }
        if (!window.confirm(`Prepare all ${selectedRows.length} selected order${selectedRows.length === 1 ? '' : 's'}?`)) {
            event.preventDefault();
            return;
        }
        form.querySelectorAll('input[name="order_ids[]"]').forEach((element) => element.remove());
        selectedRows.forEach((row) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'order_ids[]';
            input.value = row.dataset.orderId;
            form.appendChild(input);
        });
        const submit = form.querySelector('[data-order-prepare-selected]');
        if (submit) {
            submit.disabled = true;
            submit.textContent = 'Preparing orders...';
        }
    }, { signal });

    document.addEventListener('input', function (event) {
        if (event.target?.id === 'sellerOrderSearch') {
            applyOrderFilters({ resetPage: true });
        }
    }, { signal });

    document.addEventListener('change', function (event) {
        if (event.target?.matches('[data-order-select]')) {
            const row = event.target.closest('[data-order-row]');
            if (row?.dataset.orderId) {
                if (event.target.checked) selectedOrderIds.add(row.dataset.orderId);
                else selectedOrderIds.delete(row.dataset.orderId);
                if (selectedOrderIds.size === 0) onlySelectedOrders = false;
                applyOrderFilters();
            }
            return;
        }
        if (event.target?.matches('[data-order-select-all]')) {
            const checked = event.target.checked;
            orderRows().filter((row) => !row.classList.contains('hidden')).forEach((row) => {
                if (checked) selectedOrderIds.add(row.dataset.orderId);
                else selectedOrderIds.delete(row.dataset.orderId);
            });
            if (selectedOrderIds.size === 0) onlySelectedOrders = false;
            applyOrderFilters();
            return;
        }

        if (event.target?.id === 'sellerOrderStatusFilter') {
            syncStatusDropdownUi();
            applyOrderFilters({ resetPage: true });
        }
    }, { signal });

    document.getElementById('sellerOrderApplyFilter')?.addEventListener('click', function () {
        applyOrderFilters({ resetPage: true });
    }, { signal });

    document.getElementById('sellerOrderResetFilter')?.addEventListener('click', function () {
        const searchInput = document.getElementById('sellerOrderSearch');
        const statusFilter = document.getElementById('sellerOrderStatusFilter');
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = 'all';
        syncStatusDropdownUi();
        closeStatusDropdown();
        onlySelectedOrders = false;
        applyOrderFilters({ resetPage: true });
        searchInput?.focus({ preventScroll: true });
    }, { signal });

    document.addEventListener('keydown', function (event) {
        if (modal && !modal.classList.contains('hidden')) {
            if (event.key === 'Tab') { trapModalFocus(event); return; }
        }

        const { trigger, menu, options } = getStatusDropdownElements();
        const dropdownOpen = Boolean(menu && !menu.hidden);
        const targetIsTrigger = event.target === trigger;
        const optionIndex = options.indexOf(event.target);

        if (targetIsTrigger && (event.key === 'ArrowDown' || event.key === 'ArrowUp')) {
            event.preventDefault();
            openStatusDropdown();

            const index = event.key === 'ArrowUp' ? options.length - 1 : 0;
            options[index]?.focus({ preventScroll: true });
            return;
        }

        if (dropdownOpen && optionIndex >= 0 && (event.key === 'ArrowDown' || event.key === 'ArrowUp')) {
            event.preventDefault();

            const direction = event.key === 'ArrowDown' ? 1 : -1;
            const nextIndex = (optionIndex + direction + options.length) % options.length;
            options[nextIndex]?.focus({ preventScroll: true });
            return;
        }

        if (dropdownOpen && optionIndex >= 0 && event.key === 'Home') {
            event.preventDefault();
            options[0]?.focus({ preventScroll: true });
            return;
        }

        if (dropdownOpen && optionIndex >= 0 && event.key === 'End') {
            event.preventDefault();
            options[options.length - 1]?.focus({ preventScroll: true });
            return;
        }

        if (event.key === 'Escape' && dropdownOpen) {
            event.preventDefault();
            closeStatusDropdown({ focusTrigger: true });
            return;
        }

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
