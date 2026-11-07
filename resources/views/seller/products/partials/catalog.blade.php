{{-- Buyer Product Card-inspired Seller Product Card skin.
     Single-file change: these selectors apply to BOTH the server-rendered
     cards below and the dynamically re-rendered cards in seller-products.js.
     Seller status, form routes and View/Edit/Archive JS hooks remain intact. --}}
<style id="sariSellerBuyerCardParity">
@verbatim
/* Design parity with the Buyer card: flush image, clean white silhouette,
   compact 2-line product details and aligned price/stock/actions. */
.seller-products-page #productsGrid {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(min(100%, 290px), 1fr)) !important;
    align-items: stretch !important;
    gap: 15px !important;
    margin-top: 15px !important;
}
.seller-products-page #productsGrid .seller-products-card.seller-product-card--reference,
.seller-products-page #productsGrid .seller-products-card.seller-product-card--reference:hover {
    display: flex !important;
    flex-direction: column !important;
    min-width: 0 !important;
    min-height: 371px !important;
    height: auto !important;
    max-height: none !important;
    padding: 0 !important;
    overflow: hidden !important;
    border: 1px solid #e8e2da !important;
    border-radius: 20px !important;
    background: #ffffff !important;
    box-shadow: 0 14px 32px rgba(43,34,24,.085),
                0 4px 10px rgba(43,34,24,.038),
                inset 0 1px 0 rgba(255,255,255,.96) !important;
    transform: none !important;
}
.seller-products-page #productsGrid .seller-products-card.seller-product-card--reference:hover {
    border-color: #ded5c9 !important;
}
.seller-products-page #productsGrid .seller-product-card-media {
    display: block !important;
    position: relative !important;
    width: 100% !important;
    height: 194px !important;
    min-height: 194px !important;
    flex: 0 0 194px !important;
    overflow: hidden !important;
    margin: 0 !important;
    border: 0 !important;
    border-radius: 19px 19px 12px 12px !important;
    background: #f2f0ec !important;
    box-shadow: none !important;
}
.seller-products-page #productsGrid .seller-product-card-media .seller-product-image {
    display: block !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    object-position: center !important;
    transform: none !important;
    transition: transform .28s ease !important;
}
@media (hover:hover) and (pointer:fine) {
    .seller-products-page #productsGrid .seller-product-card--reference:hover .seller-product-card-media .seller-product-image {
        transform: scale(1.018) !important;
    }
}
.seller-products-page #productsGrid .seller-product-card-badges {
    position: absolute !important;
    z-index: 3 !important;
    top: 12px !important;
    left: 12px !important;
    right: 12px !important;
    display: flex !important;
    align-items: flex-start !important;
    flex-wrap: wrap !important;
    gap: 7px !important;
    pointer-events: none !important;
}
.seller-products-page #productsGrid .seller-product-card-badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 25px !important;
    padding: 0 10px !important;
    border: 1px solid rgba(255,255,255,.08) !important;
    border-radius: 999px !important;
    font-size: 8.5px !important;
    line-height: 1 !important;
    font-weight: 750 !important;
    white-space: nowrap !important;
    box-shadow: 0 4px 10px rgba(28,25,22,.12) !important;
    backdrop-filter: none !important;
}
/* Seller Active remains green, so the operational status is still obvious. */
.seller-products-page #productsGrid .seller-product-card-badge.seller-product-status--active {
    background: #dcf3e5 !important;
    color: #14764c !important;
}
.seller-products-page #productsGrid .seller-product-card-badge.seller-product-card-badge--dark {
    background: #2b2a28 !important;
    color: #e0a11a !important;
}
.seller-products-page #productsGrid .seller-product-card-badge.seller-product-status--pending {
    background: #eff5fc !important;
    color: #537a9f !important;
}
.seller-products-page #productsGrid .seller-product-card-badge.seller-product-status--danger {
    background: #fff0f0 !important;
    color: #b45656 !important;
}
.seller-products-page #productsGrid .seller-product-card-body {
    display: flex !important;
    flex: 1 1 auto !important;
    flex-direction: column !important;
    min-width: 0 !important;
    padding: 11px 14px 13px !important;
}
.seller-products-page #productsGrid .seller-product-card-heading {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) auto !important;
    align-items: start !important;
    gap: 11px !important;
}
.seller-products-page #productsGrid .seller-product-card-title {
    display: -webkit-box !important;
    min-width: 0 !important;
    margin: 0 !important;
    overflow: hidden !important;
    color: #26211d !important;
    font-size: 14.5px !important;
    line-height: 1.34 !important;
    font-weight: 650 !important;
    letter-spacing: -.03em !important;
    white-space: normal !important;
    overflow-wrap: anywhere !important;
    text-overflow: ellipsis !important;
    -webkit-box-orient: vertical !important;
    -webkit-line-clamp: 2 !important;
}
.seller-products-page #productsGrid .seller-product-card-category {
    margin: 5px 0 0 !important;
    overflow: hidden !important;
    color: #75808b !important;
    font-size: 10.5px !important;
    line-height: 1.45 !important;
    font-weight: 400 !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}
.seller-products-page #productsGrid .seller-product-card-price-wrap {
    display: flex !important;
    min-width: 88px !important;
    flex-direction: column !important;
    align-items: flex-end !important;
    gap: 4px !important;
    text-align: right !important;
}
.seller-products-page #productsGrid .seller-product-card-price {
    display: block !important;
    margin: 0 !important;
    color: #bd7b00 !important;
    font-size: 16px !important;
    line-height: 1.12 !important;
    font-weight: 800 !important;
    letter-spacing: -.035em !important;
    white-space: nowrap !important;
}
.seller-products-page #productsGrid .seller-product-card-original-price {
    color: #aca49a !important;
    font-size: 9.5px !important;
    line-height: 1 !important;
    text-decoration: line-through !important;
}
/* Buyer-inspired flash strip. Countdown hooks and scheduled state preserved. */
.seller-products-page #productsGrid .seller-product-flash-sale {
    display: inline-flex !important;
    width: min(220px,100%) !important;
    min-height: 29px !important;
    height: 29px !important;
    align-items: stretch !important;
    align-self: flex-start !important;
    gap: 0 !important;
    margin: 8px 0 0 !important;
    overflow: hidden !important;
    border: 0 !important;
    border-radius: 999px !important;
    background-color: #202329 !important;
    background-image: var(--sari-products-flash-sale-strip-image, none) !important;
    background-position: center !important;
    background-size: 100% 100% !important;
    box-shadow: none !important;
}
.seller-products-page #productsGrid .seller-product-flash-sale-label {
    display: flex !important;
    width: 43% !important;
    flex: 0 0 43% !important;
    align-items: center !important;
    padding: 0 10px !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    color: #fff !important;
    font-size: 9px !important;
    font-weight: 800 !important;
    white-space: nowrap !important;
    box-shadow: none !important;
}
.seller-products-page #productsGrid .seller-product-flash-sale-bolt { display: none !important; }
.seller-products-page #productsGrid .seller-product-flash-sale-countdown {
    display: flex !important;
    min-width: 0 !important;
    flex: 1 1 auto !important;
    align-items: center !important;
    justify-content: center !important;
    overflow: hidden !important;
    padding: 0 7px !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    color: #fff !important;
    font-variant-numeric: tabular-nums !important;
    font-size: 8px !important;
    font-weight: 800 !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    box-shadow: none !important;
}
.seller-products-page #productsGrid .seller-product-flash-sale--scheduled {
    width: min(260px,100%) !important;
}
.seller-products-page #productsGrid .seller-product-card-divider {
    width: 100% !important;
    height: 1px !important;
    flex: 0 0 auto !important;
    margin: auto 0 9px !important;
    background: #ebe5dd !important;
}
.seller-products-page #productsGrid .seller-product-card-footer {
    display: flex !important;
    align-items: flex-end !important;
    justify-content: space-between !important;
    gap: 10px !important;
    min-width: 0 !important;
    margin-top: 0 !important;
}
.seller-products-page #productsGrid .seller-product-card-stock-copy { min-width: 0 !important; }
.seller-products-page #productsGrid .seller-product-card-stock {
    margin: 0 !important;
    font-size: 11px !important;
    line-height: 1.2 !important;
    font-weight: 700 !important;
}
.seller-products-page #productsGrid .seller-product-card-updated {
    margin: 5px 0 0 !important;
    color: #77818c !important;
    font-size: 9.5px !important;
    line-height: 1.3 !important;
    font-weight: 400 !important;
}
.seller-products-page #productsGrid .seller-product-card-actions,
.seller-products-page #productsGrid .seller-product-actions {
    display: flex !important;
    position: relative !important;
    z-index: 50 !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 7px !important;
    flex: 0 0 auto !important;
    margin: 0 !important;
    pointer-events: auto !important;
}
.seller-products-page #productsGrid .seller-product-card--reference .seller-product-action {
    display: grid !important;
    place-items: center !important;
    position: relative !important;
    z-index: 51 !important;
    width: 34px !important;
    height: 34px !important;
    min-width: 34px !important;
    flex: 0 0 34px !important;
    border: 1px solid #e5ddd4 !important;
    border-radius: 8px !important;
    background: #fff !important;
    color: #d68f08 !important;
    box-shadow: 0 3px 9px rgba(45,37,28,.035) !important;
    transform: none !important;
    cursor: pointer !important;
    pointer-events: auto !important;
}
.seller-products-page #productsGrid .seller-product-card--reference .seller-product-action svg {
    width: 16px !important;
    height: 16px !important;
    pointer-events: none !important;
}
.seller-products-page #productsGrid .seller-product-card--reference .seller-product-action:hover {
    border-color: #d8a63f !important;
    background: #fffaf0 !important;
    color: #b67508 !important;
    box-shadow: 0 6px 13px rgba(201,137,14,.10) !important;
}
.seller-products-page #productsGrid .seller-product-card--reference .seller-product-action--archive:hover {
    border-color: #ded0c1 !important;
    background: #f8f5f0 !important;
    color: #745e40 !important;
}
.seller-products-page #productsGrid .seller-product-card--reference .seller-product-action:focus-visible {
    outline: 2px solid rgba(214,143,8,.35) !important;
    outline-offset: 2px !important;
}
@media (max-width: 639px) {
    .seller-products-page #productsGrid {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
    }
    .seller-products-page #productsGrid .seller-products-card.seller-product-card--reference {
        min-height: 0 !important;
        border-radius: 18px !important;
    }
    .seller-products-page #productsGrid .seller-product-card-media {
        height: 196px !important;
        min-height: 196px !important;
        flex-basis: 196px !important;
        border-radius: 17px 17px 11px 11px !important;
    }
    .seller-products-page #productsGrid .seller-product-card-body { padding: 11px 13px 12px !important; }
    .seller-products-page #productsGrid .seller-product-card-title { font-size: 14px !important; }
    .seller-products-page #productsGrid .seller-product-card-price { font-size: 15.5px !important; }
    .seller-products-page #productsGrid .seller-product-card-category { font-size: 10px !important; }
    .seller-products-page #productsGrid .seller-product-card-divider { margin: 12px 0 9px !important; }
}
@media (prefers-reduced-motion: reduce) {
    .seller-products-page #productsGrid .seller-product-card-media .seller-product-image {
        transition: none !important;
        transform: none !important;
    }
}
@endverbatim
</style>

{{-- Match Buyer Product Card: discount corner + MEGA / COD / XTRA overlay. --}}
<style id="sariSellerBuyerOfferParity">
@verbatim
.seller-products-page #productsGrid .seller-product-card-media.has-buyer-discount-corner .seller-product-card-badges {
    right: 70px !important;
}
.seller-products-page #productsGrid .seller-buyer-discount-corner {
    position: absolute !important;
    z-index: 7 !important;
    top: 0 !important;
    right: 0 !important;
    display: inline-flex !important;
    min-width: 56px !important;
    height: 30px !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 10px 1px 11px !important;
    border: 0 !important;
    border-radius: 0 19px 0 17px !important;
    background: #dda00b !important;
    color: white !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    letter-spacing: -.025em !important;
    font-variant-numeric: tabular-nums !important;
    white-space: nowrap !important;
    box-shadow: 0 4px 10px rgba(126,84,7,.14) !important;
    pointer-events: none !important;
}
.seller-products-page #productsGrid .seller-buyer-offer-cluster {
    position: absolute !important;
    z-index: 6 !important;
    left: 0 !important;
    bottom: 0 !important;
    display: inline-flex !important;
    align-items: stretch !important;
    flex-wrap: nowrap !important;
    max-width: calc(100% - 8px) !important;
    gap: 0 !important;
    filter: drop-shadow(0 5px 11px rgba(22,20,18,.16));
    pointer-events: none !important;
    isolation: isolate !important;
}
.seller-products-page #productsGrid .seller-buyer-offer-item {
    --offer-bg: #333;
    position: relative !important;
    z-index: 1 !important;
    display: flex !important;
    min-width: 0 !important;
    height: 29px !important;
    flex: 0 1 auto !important;
    align-items: center !important;
    padding: 0 9px !important;
    background: transparent !important;
    color: white !important;
    white-space: nowrap !important;
}
.seller-products-page #productsGrid .seller-buyer-offer-item + .seller-buyer-offer-item {
    margin-left: -9px !important;
    padding-left: 14px !important;
}
.seller-products-page #productsGrid .seller-buyer-offer-item::before {
    content: '';
    position: absolute;
    inset: 0 -2px;
    z-index: -1;
    background: var(--offer-bg);
    transform: skewX(-18deg);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.13);
}
.seller-products-page #productsGrid .seller-buyer-offer-item:first-child::before {
    left: -7px;
    border-radius: 11px 0 0 0;
}
.seller-products-page #productsGrid .seller-buyer-offer-item:last-child::before {
    right: -7px;
    border-radius: 0 10px 0 0;
}
.seller-products-page #productsGrid .seller-buyer-offer-item--mega {
    --offer-bg: linear-gradient(135deg,#8f1730 0%,#b92f49 100%);
    flex-basis: 68px !important;
}
.seller-products-page #productsGrid .seller-buyer-offer-item--cod {
    --offer-bg: linear-gradient(135deg,#f6cf66 0%,#e2a91d 100%);
    flex-basis: 106px !important;
    color: #201b13 !important;
}
.seller-products-page #productsGrid .seller-buyer-offer-item--shipping {
    --offer-bg: linear-gradient(135deg,#16283b 0%,#24384d 100%);
    flex-basis: 100px !important;
}
.seller-products-page #productsGrid .seller-buyer-offer-stack {
    display: flex !important;
    min-width: 0 !important;
    flex-direction: column !important;
    justify-content: center !important;
    gap: 2px !important;
    line-height: 1 !important;
}
.seller-products-page #productsGrid .seller-buyer-offer-kicker {
    display: block !important;
    font-size: 6.6px !important;
    font-weight: 800 !important;
    letter-spacing: .045em !important;
    text-transform: uppercase !important;
}
.seller-products-page #productsGrid .seller-buyer-offer-main {
    display: block !important;
    overflow: hidden !important;
    font-size: 7px !important;
    font-weight: 750 !important;
    line-height: 1.05 !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}
.seller-products-page #productsGrid .seller-buyer-offer-item--cod .seller-buyer-offer-main {
    font-size: 6.5px !important;
}
@media (max-width: 420px) {
    .seller-products-page #productsGrid .seller-buyer-offer-item {
        height: 27px !important;
        padding-right: 7px !important;
    }
    .seller-products-page #productsGrid .seller-buyer-offer-item--mega { flex-basis: 62px !important; }
    .seller-products-page #productsGrid .seller-buyer-offer-item--cod { flex-basis: 96px !important; }
    .seller-products-page #productsGrid .seller-buyer-offer-item--shipping { flex-basis: 91px !important; }
    .seller-products-page #productsGrid .seller-buyer-offer-kicker { font-size: 6px !important; }
    .seller-products-page #productsGrid .seller-buyer-offer-main { font-size: 6.4px !important; }
}
@endverbatim
</style>

{{-- Search, Status, Stock, Category, Apply, and Clear: one aligned desktop row. --}}
<style>
@media (min-width: 1200px) {
    .seller-products-page .seller-products-filters-flat.seller-products-controls-inline {
        display: grid !important;
        grid-template-columns: minmax(230px, 1.6fr) repeat(3, minmax(125px, 1fr)) auto !important;
        align-items: end !important;
        max-width: none !important;
        gap: 10px !important;
    }
    .seller-products-page .seller-products-controls-inline .seller-products-search-line {
        margin: 0 !important;
        min-width: 0 !important;
    }
    .seller-products-page .seller-products-controls-inline .seller-products-search-field {
        max-width: none !important;
    }
    .seller-products-page .seller-products-controls-inline .seller-premium-filter-actions {
        align-self: end !important;
    }
}
@media (max-width: 1199px) {
    .seller-products-page .seller-products-filters-flat.seller-products-controls-inline {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }
    .seller-products-page .seller-products-controls-inline .seller-products-search-line {
        grid-column: 1 / -1 !important;
        margin: 0 !important;
    }
    .seller-products-page .seller-products-controls-inline .seller-products-search-field {
        max-width: none !important;
    }
    .seller-products-page .seller-products-controls-inline .seller-premium-filter-actions {
        grid-column: 1 / -1 !important;
    }
}
@media (max-width: 650px) {
    .seller-products-page .seller-products-filters-flat.seller-products-controls-inline {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}
</style>

{{-- UI-only compact cards: natural content height, no oversized forced 378px/380px box.
     Both initial Blade and AJAX-rendered cards use these shared classes.
     The 2-line title and See details control from the prior patch remain unchanged. --}}
<style id="sariSellerProductUniformCardHeight">
@verbatim
.seller-products-page #productsGrid .seller-products-card.seller-product-card--reference,
.seller-products-page #productsGrid .seller-products-card.seller-product-card--reference:hover {
    /* Grid stretch keeps cards aligned within each row, without dead vertical space. */
    height: auto !important;
    min-height: 0 !important;
    max-height: none !important;
    box-sizing: border-box !important;
}
.seller-products-page #productsGrid .seller-product-card-media {
    height: 182px !important;
    min-height: 182px !important;
    max-height: 182px !important;
    flex: 0 0 182px !important;
}
.seller-products-page #productsGrid .seller-product-card-body {
    min-height: 0 !important;
    flex: 1 1 auto !important;
    padding: 9px 14px 11px !important;
}
.seller-products-page #productsGrid .seller-product-card-heading {
    grid-template-columns: minmax(0, 1fr) max-content !important;
    min-width: 0 !important;
}
.seller-products-page #productsGrid .seller-product-card-heading > div:first-child {
    min-width: 0 !important;
}
.seller-products-page #productsGrid .seller-product-card-title {
    /* 2-line title area remains uniform even when visible title is short. */
    display: -webkit-box !important;
    height: 39px !important;
    min-height: 39px !important;
    max-height: 39px !important;
    overflow: hidden !important;
    -webkit-box-orient: vertical !important;
    -webkit-line-clamp: 2 !important;
    line-clamp: 2 !important;
    white-space: normal !important;
    overflow-wrap: anywhere !important;
    text-overflow: ellipsis !important;
}
.seller-products-page #productsGrid .seller-product-card-category {
    overflow: hidden !important;
    white-space: nowrap !important;
    text-overflow: ellipsis !important;
}
.seller-products-page #productsGrid .seller-product-card-price-wrap {
    min-width: 0 !important;
    flex-shrink: 0 !important;
}
.seller-products-page #productsGrid .seller-product-card-divider {
    /* Divider hugs the footer, never leaves a large gap after product details. */
    margin: auto 0 7px !important;
}
.seller-products-page #productsGrid .seller-product-card-footer {
    flex-shrink: 0 !important;
}
.seller-products-page #productsGrid .seller-product-flash-sale {
    height: 24px !important;
    min-height: 24px !important;
    margin-top: 5px !important;
}
@media (max-width: 639px) {
    .seller-products-page #productsGrid .seller-product-card-media {
        height: 188px !important;
        min-height: 188px !important;
        max-height: 188px !important;
        flex: 0 0 188px !important;
    }
    .seller-products-page #productsGrid .seller-product-card-body {
        padding: 9px 13px 11px !important;
    }
}
@endverbatim
</style>

{{-- Visible title text is shortened before paint, including the server-rendered cards.
     The existing View Product action opens full details without changing stored names. --}}
<style id="sariSellerCatalogTitleEllipsis">
@verbatim
.seller-products-page #productsGrid .seller-product-card-heading > div:first-child {
    min-width: 0 !important;
    overflow: hidden !important;
}
.seller-products-page #productsGrid .seller-product-card-title {
    display: -webkit-box !important;
    min-height: 39px !important;
    max-height: 39px !important;
    height: 39px !important;
    max-width: 100% !important;
    overflow: hidden !important;
    -webkit-box-orient: vertical !important;
    -webkit-line-clamp: 2 !important;
    white-space: normal !important;
    overflow-wrap: anywhere !important;
    text-overflow: ellipsis !important;
}
.seller-products-page #productsGrid .seller-product-card-meta-line {
    display: flex !important;
    align-items: baseline !important;
    min-width: 0 !important;
    gap: 7px !important;
    margin-top: 5px !important;
    white-space: nowrap !important;
}
.seller-products-page #productsGrid .seller-product-card-meta-line .seller-product-card-category {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    margin: 0 !important;
    overflow: hidden !important;
    white-space: nowrap !important;
    text-overflow: ellipsis !important;
}
.seller-products-page #productsGrid .seller-product-card-see-details {
    display: inline-flex !important;
    align-items: center !important;
    flex: 0 0 auto !important;
    max-width: 100% !important;
    padding: 0 !important;
    border: 0 !important;
    background: transparent !important;
    color: #b87908 !important;
    font-family: inherit !important;
    font-size: 10px !important;
    font-weight: 650 !important;
    line-height: 1.4 !important;
    white-space: nowrap !important;
    cursor: pointer !important;
    text-decoration: none !important;
}
.seller-products-page #productsGrid .seller-product-card-see-details:hover,
.seller-products-page #productsGrid .seller-product-card-see-details:focus-visible {
    color: #885808 !important;
    text-decoration: underline !important;
    text-underline-offset: 2px !important;
}
.seller-products-page #productsGrid .seller-product-card-see-details:focus-visible {
    outline: 2px solid #c88912 !important;
    outline-offset: 3px !important;
    border-radius: 2px !important;
}
@endverbatim
</style>


{{-- Final exact Buyer-card dimensions: uploaded buyer product card is the source of truth.
     Card width is intentionally capped at 325px to follow the buyer image reference;
     the buyer component itself lets its parent grid determine the rendered width.
     This final layer wins over historical auto-height/card-media overrides above.
     Scoped to the Seller Product Management grid and works with Blade + AJAX cards. --}}
<style id="sariSellerActualBuyerCardDimensions">
@verbatim
.seller-products-page #productsGrid {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(min(100%, 285px), 325px)) !important;
    gap: 16px !important;
    align-items: start !important;
    justify-content: start !important;
}
.seller-products-page #productsGrid .seller-products-card.seller-product-card--reference,
.seller-products-page #productsGrid .seller-products-card.seller-product-card--reference:hover {
    width: 100% !important;
    max-width: 325px !important;
    height: 371px !important;
    min-height: 371px !important;
    max-height: 371px !important;
    display: flex !important;
    flex-direction: column !important;
    box-sizing: border-box !important;
    overflow: hidden !important;
    align-self: start !important;
    padding: 0 !important;
    transform: none !important;
}
.seller-products-page #productsGrid .seller-product-card-media {
    height: 194px !important;
    min-height: 194px !important;
    max-height: 194px !important;
    flex: 0 0 194px !important;
}
.seller-products-page #productsGrid .seller-product-card-body {
    min-height: 0 !important;
    min-width: 0 !important;
    flex: 1 1 auto !important;
    padding: 10px 14px 12px !important;
}
.seller-products-page #productsGrid .seller-product-card-title {
    height: 39px !important;
    min-height: 39px !important;
    max-height: 39px !important;
    overflow: hidden !important;
    -webkit-line-clamp: 2 !important;
    line-clamp: 2 !important;
}
.seller-products-page #productsGrid .seller-product-card-divider {
    margin: auto 0 9px !important;
}
/* The buyer reference uses 361px cards and 184px images on short desktops. */
@media (min-width: 1024px) and (max-height: 820px) {
    .seller-products-page #productsGrid .seller-products-card.seller-product-card--reference,
    .seller-products-page #productsGrid .seller-products-card.seller-product-card--reference:hover {
        height: 361px !important;
        min-height: 361px !important;
        max-height: 361px !important;
    }
    .seller-products-page #productsGrid .seller-product-card-media {
        height: 184px !important;
        min-height: 184px !important;
        max-height: 184px !important;
        flex-basis: 184px !important;
    }
    .seller-products-page #productsGrid .seller-product-card-body {
        padding: 9px 13px 10px !important;
    }
}
/* The buyer card does not force a fixed-height product card below desktop width. */
@media (max-width: 1023px) {
    .seller-products-page #productsGrid .seller-products-card.seller-product-card--reference,
    .seller-products-page #productsGrid .seller-products-card.seller-product-card--reference:hover {
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
    }
    .seller-products-page #productsGrid .seller-product-card-media {
        height: 196px !important;
        min-height: 196px !important;
        max-height: 196px !important;
        flex-basis: 196px !important;
    }
    .seller-products-page #productsGrid .seller-product-card-divider {
        margin: 12px 0 9px !important;
    }
}
@media (max-width: 639px) {
    .seller-products-page #productsGrid {
        grid-template-columns: minmax(0, 1fr) !important;
        gap: 13px !important;
    }
    .seller-products-page #productsGrid .seller-products-card.seller-product-card--reference,
    .seller-products-page #productsGrid .seller-products-card.seller-product-card--reference:hover {
        max-width: 100% !important;
    }
    .seller-products-page #productsGrid .seller-product-card-body {
        padding: 11px 13px 11px !important;
    }
}
@endverbatim
</style>

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
            
            </div>
        </div>
    </section>

    {{-- PRODUCT CATALOG — FULL-WIDTH WORKSPACE (NO INVENTORY SIDEBAR) --}}
    <section class="seller-catalog-studio seller-catalog-studio--full mt-1">
        <div class="seller-products-main min-w-0 w-full">
            <div class="seller-products-workspace rounded-[20px] border border-[#e8e0d5] bg-white p-4 sm:p-5">
                {{-- Open catalog controls: search and filters without a surrounding filter card. --}}
                {{-- All filter controls stay visible; the oversized bordered filter container stays removed. --}}
                <div class="seller-products-filters-flat seller-products-controls-inline" role="group" aria-label="Search and filter products">
                <div class="seller-products-search-line">
                    <label for="productsSearch" class="seller-products-search-label">Search products</label>
                <div class="seller-products-search-field relative">
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

                {{--
                    Compatibility-only DOM IDs for the existing product JS.
                    Sidebar-only stats and Quick View handlers are retained but hidden.
                    Real filter dropdowns/buttons above are visible and bound to the existing JS.
                --}}
                <div class="seller-products-filter-compat" hidden aria-hidden="true">
                    <span id="productsTotalCount">0</span>
                    <span id="productsApprovedCount">0</span>
                    <span id="productsLowStockCount">0</span>
                    <span id="productsOutStockCount">0</span>
                    <span id="productsCatalogHealthScore">0</span>
                    <span id="productsCatalogHealthLabel"></span>
                    <span id="productsCatalogHealthBar"></span>
                    <span id="productsCatalogAttention"></span>
                    <span id="productsHealthApproved">0</span>
                    <span id="productsHealthReview">0</span>
                    <span id="productsHealthLow">0</span>
                    <span id="productsHealthOut">0</span>
                    <button type="button" class="seller-products-quick-filter is-active" data-products-quick-filter="all" tabindex="-1" aria-hidden="true"><span id="productsQuickAll">0</span></button>
                    <button type="button" class="seller-products-quick-filter" data-products-quick-filter="approved" tabindex="-1" aria-hidden="true"><span id="productsQuickApproved">0</span></button>
                    <button type="button" class="seller-products-quick-filter" data-products-quick-filter="pending" tabindex="-1" aria-hidden="true"><span id="productsQuickPending">0</span></button>
                    <button type="button" class="seller-products-quick-filter" data-products-quick-filter="low" tabindex="-1" aria-hidden="true"><span id="productsQuickLow">0</span></button>
                    <button type="button" class="seller-products-quick-filter" data-products-quick-filter="out" tabindex="-1" aria-hidden="true"><span id="productsQuickOut">0</span></button>

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

                    // Keep seller-specific status/featured badges. Price discount now uses
                    // the Buyer-style gold corner instead of a duplicate gray pill.
                    $secondaryBadge = !empty($product['mall_badge'])
                        ? 'Mall'
                        : (!empty($product['on_trend']) ? 'Featured' : null);

                    $regularPrice = (float) ($product['price'] ?? 0);
                    $hasRealSellerDiscount = $moderationStatus === 'approved'
                        && $regularPrice > 0
                        && $effectiveDiscount > 0
                        && $displayedSalePrice < $regularPrice;
                    $sellerOfferDiscount = $hasRealSellerDiscount
                        ? max(1, min(100, (int) round(100 * ($regularPrice - $displayedSalePrice) / $regularPrice)))
                        : 0;

                    $hasSellerCod = collect(['cod', 'cash_on_delivery', 'is_cod', 'payment_cod'])
                        ->contains(fn ($key) => in_array($product[$key] ?? null, [true, 1, '1'], true))
                        || strtolower((string) ($product['delivery_mode'] ?? '')) === 'cod';

                    // Same rule as the Buyer card: trust an active promotion in the
                    // payload; legacy product-level free_shipping is also accepted.
                    $activeShippingPromotion = $product['free_shipping_promotion'] ?? null;
                    $hasActiveShippingPromotion = is_array($activeShippingPromotion)
                        && $activeShippingPromotion !== []
                        && !in_array(strtolower((string) ($activeShippingPromotion['status'] ?? 'active')), ['paused', 'ended', 'inactive', 'scheduled'], true);
                    $hasSellerFreeShipping = $hasActiveShippingPromotion
                        || in_array($product['free_shipping'] ?? null, [true, 1, '1'], true);
                    $shippingMinSpend = $hasActiveShippingPromotion
                        ? max(0, (float) ($activeShippingPromotion['minimum_order_amount'] ?? 0))
                        : 0;
                    $showSellerOfferStrip = $sellerOfferDiscount > 0 || $hasSellerCod || $hasSellerFreeShipping;

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
                    <div class="seller-product-card-media {{ $sellerOfferDiscount > 0 ? 'has-buyer-discount-corner' : '' }}">
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

                        {{-- Buyer-style discount corner: only when the actual displayed price is reduced. --}}
                        @if ($sellerOfferDiscount > 0)
                            <span class="seller-buyer-discount-corner" aria-label="{{ $sellerOfferDiscount }} percent discount">-{{ $sellerOfferDiscount }}%</span>
                        @endif

                        @if ($showSellerOfferStrip)
                            <div class="seller-buyer-offer-cluster" aria-label="Available product offers">
                                @if ($sellerOfferDiscount > 0)
                                    <span class="seller-buyer-offer-item seller-buyer-offer-item--mega">
                                        <span class="seller-buyer-offer-stack">
                                            <span class="seller-buyer-offer-kicker">MEGA</span>
                                            <span class="seller-buyer-offer-main">Discount</span>
                                        </span>
                                    </span>
                                @endif
                                @if ($hasSellerCod)
                                    <span class="seller-buyer-offer-item seller-buyer-offer-item--cod">
                                        <span class="seller-buyer-offer-stack">
                                            <span class="seller-buyer-offer-kicker">COD</span>
                                            <span class="seller-buyer-offer-main">Cash on Delivery</span>
                                        </span>
                                    </span>
                                @endif
                                @if ($hasSellerFreeShipping)
                                    <span class="seller-buyer-offer-item seller-buyer-offer-item--shipping">
                                        <span class="seller-buyer-offer-stack">
                                            <span class="seller-buyer-offer-kicker">XTRA</span>
                                            <span class="seller-buyer-offer-main">{{ $shippingMinSpend > 0 ? 'Free Shipping ₱' . number_format($shippingMinSpend, 0) . '+' : 'Free Shipping' }}</span>
                                        </span>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="seller-product-card-body">
                        <div class="seller-product-card-heading">
                            <div class="min-w-0">
                                @php
                                    $fullProductCardName = trim((string) ($product['name'] ?? '')) ?: 'Untitled product';
                                    $shortProductCardName = \Illuminate\Support\Str::limit($fullProductCardName, 40, '…');
                                    $productCardNameShortened = \Illuminate\Support\Str::length($fullProductCardName) > 40;
                                @endphp
                                <h3 class="seller-product-card-title" title="{{ $fullProductCardName }}">{{ $shortProductCardName }}</h3>

                                <div class="seller-product-card-meta-line">
                                    <p class="seller-product-card-category">
                                        {{ $product['category'] ?? 'Uncategorized' }}@if(!empty($product['brand'])) · {{ $product['brand'] }}@endif
                                    </p>
                                    @if ($productCardNameShortened)
                                        <button type="button" class="seller-product-card-see-details"
                                            data-page-view-product data-id="{{ $product['id'] ?? '' }}"
                                            aria-label="See full details for {{ $fullProductCardName }}"
                                            title="View full product name and details">See details</button>
                                    @endif
                                </div>
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

{{-- Sync Buyer-style offer UI when seller-products.js replaces card HTML after data refresh. --}}
<script id="sariSellerBuyerOfferSync">
(function () {
    window.__SARI_SELLER_OFFER_DECORATOR_CLEANUP__?.();

    const grid = document.getElementById('productsGrid');
    if (!grid) return;

    const truthy = (value) => value === true || value === 1 || value === '1';
    const pct = (value) => Math.max(0, Math.min(100, Number(value) || 0));
    const moneyWhole = (value) => Number(value).toLocaleString('en-PH', { maximumFractionDigits: 0 });
    const productsById = () => {
        const payload = window.__SARI_PRODUCTS_LIBRARY_CACHED__ || window.__SARI_PRODUCTS_SERVER_INITIAL__;
        const products = Array.isArray(payload?.products) ? payload.products : [];
        return new Map(products.map((product) => [String(product.id), product]));
    };
    const isLivePromotion = (promotion) => {
        if (!promotion || typeof promotion !== 'object' || Array.isArray(promotion)) return false;
        const status = String(promotion.status || 'active').toLowerCase();
        return !['paused', 'ended', 'inactive', 'scheduled'].includes(status);
    };
    function offerNode(type, kicker, title) {
        const item = document.createElement('span');
        item.className = 'seller-buyer-offer-item seller-buyer-offer-item--' + type;
        const stack = document.createElement('span');
        stack.className = 'seller-buyer-offer-stack';
        const top = document.createElement('span');
        top.className = 'seller-buyer-offer-kicker';
        top.textContent = kicker;
        const bottom = document.createElement('span');
        bottom.className = 'seller-buyer-offer-main';
        bottom.textContent = title;
        stack.append(top, bottom);
        item.append(stack);
        return item;
    }
    function syncCard(card, product) {
        if (!product) return;
        const media = card.querySelector('.seller-product-card-media');
        const badges = card.querySelector('.seller-product-card-badges');
        if (!media || !badges) return;

        const status = String(product.moderation_status || '').toLowerCase();
        const regular = Number(product.price || 0);
        const sale = Number(product.sale_price ?? regular);
        const discount = pct(product.effective_discount ?? product.discount);
        const flashEnds = Date.parse(product.flash_sale_ends_at || '');
        const flashActive = status === 'approved'
            && pct(product.discount) > 0
            && Number.isFinite(flashEnds)
            && flashEnds > Date.now();
        const actualSale = flashActive
            ? Math.round(regular * (1 - pct(product.discount) / 100) * 100) / 100
            : sale;
        const activeDiscount = status === 'approved' && regular > 0
            && discount > 0 && actualSale < regular
            ? Math.max(1, Math.min(100, Math.round(100 * (regular - actualSale) / regular)))
            : 0;

        const cod = ['cod', 'cash_on_delivery', 'is_cod', 'payment_cod']
            .some((key) => truthy(product[key]))
            || String(product.delivery_mode || '').toLowerCase() === 'cod';
        const shipping = product.free_shipping_promotion;
        const hasShipping = isLivePromotion(shipping) || truthy(product.free_shipping);
        const minimum = isLivePromotion(shipping)
            ? Math.max(0, Number(shipping.minimum_order_amount) || 0)
            : 0;

        // The original JS uses one secondary pill for discount OR Mall/Featured.
        // Reserve that pill for listing identity; place discounts in the gold corner.
        const dark = badges.querySelector('.seller-product-card-badge--dark');
        const identity = product.mall_badge ? 'Mall' : (product.on_trend ? 'Featured' : '');
        if (dark) {
            if (identity) dark.textContent = identity;
            else dark.remove();
        } else if (identity) {
            const pill = document.createElement('span');
            pill.className = 'seller-product-card-badge seller-product-card-badge--dark';
            pill.textContent = identity;
            badges.append(pill);
        }

        let corner = media.querySelector('.seller-buyer-discount-corner');
        media.classList.toggle('has-buyer-discount-corner', activeDiscount > 0);
        if (activeDiscount > 0) {
            if (!corner) {
                corner = document.createElement('span');
                corner.className = 'seller-buyer-discount-corner';
                media.append(corner);
            }
            corner.textContent = '-' + activeDiscount + '%';
            corner.setAttribute('aria-label', activeDiscount + ' percent discount');
        } else {
            corner?.remove();
        }

        let offers = media.querySelector('.seller-buyer-offer-cluster');
        if (!activeDiscount && !cod && !hasShipping) {
            offers?.remove();
            return;
        }
        if (!offers) {
            offers = document.createElement('div');
            offers.className = 'seller-buyer-offer-cluster';
            offers.setAttribute('aria-label', 'Available product offers');
            media.append(offers);
        }
        const items = [];
        if (activeDiscount) items.push(offerNode('mega', 'MEGA', 'Discount'));
        if (cod) items.push(offerNode('cod', 'COD', 'Cash on Delivery'));
        if (hasShipping) items.push(offerNode('shipping', 'XTRA', minimum > 0 ? 'Free Shipping ₱' + moneyWhole(minimum) + '+' : 'Free Shipping'));
        offers.replaceChildren(...items);
    }

    function syncGrid() {
        if (!grid.isConnected) return;
        const all = productsById();
        grid.querySelectorAll('[data-product-item][data-product-id]').forEach((card) => {
            syncCard(card, all.get(String(card.dataset.productId)));
        });
    }
    const observer = new MutationObserver((records) => {
        // Watch only direct card additions/removals; our badge edits do not retrigger this.
        if (records.some((record) => record.addedNodes.length || record.removedNodes.length)) {
            syncGrid();
        }
    });
    observer.observe(grid, { childList: true });
    const cleanup = () => {
        observer.disconnect();
        if (window.__SARI_SELLER_OFFER_DECORATOR_CLEANUP__ === cleanup) {
            window.__SARI_SELLER_OFFER_DECORATOR_CLEANUP__ = null;
        }
    };
    window.__SARI_SELLER_OFFER_DECORATOR_CLEANUP__ = cleanup;
    document.addEventListener('livewire:navigating', cleanup, { once: true });
    syncGrid();
    window.__SARI_PRODUCTS_LIBRARY_PROMISE__?.then(() => syncGrid()).catch(() => {});
})();
</script>
