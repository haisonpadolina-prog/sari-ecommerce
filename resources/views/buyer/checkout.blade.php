@extends('layouts.buyer')

@section('title', 'Checkout — SARI')
@section('page-title', 'Checkout')

@push('styles')
<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
>
<style>
    .sari-checkout {
        --checkout-gold: #c88912;
        --checkout-gold-dark: #aa720e;
        --checkout-ink: #20242a;
        --checkout-muted: #75808c;
        --checkout-border: #e2e6ea;
        --checkout-soft: #f6f7f9;
        --checkout-green: #4f8065;
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        color: var(--checkout-ink);
    }

    .sari-checkout-shell {
        width: 100%;
        max-width: 1500px;
        margin: 0 auto;
        padding: 22px 24px 42px;
    }

    .sari-checkout-intro {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
    }

    .sari-checkout-eyebrow {
        margin: 0;
        color: #9a6815;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .sari-checkout-title {
        margin: 6px 0 0;
        color: #171a1f;
        font-size: clamp(25px, 2.4vw, 34px);
        font-weight: 700;
        line-height: 1.12;
        letter-spacing: -.04em;
    }

    .sari-checkout-subtitle {
        max-width: 570px;
        margin: 8px 0 0;
        color: #7b8490;
        font-size: 11px;
        line-height: 1.65;
    }

    .sari-checkout-secure {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 38px;
        border: 1px solid #e2e6ea;
        border-radius: 10px;
        background: #fff;
        padding: 0 13px;
        color: #5d6671;
        font-size: 10px;
        font-weight: 600;
        white-space: nowrap;
    }

    .sari-checkout-secure svg {
        width: 17px;
        height: 17px;
        color: #b47a0f;
    }

    .sari-checkout-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 400px;
        gap: 18px;
        align-items: start;
    }

    .sari-checkout-main {
        display: grid;
        gap: 16px;
        min-width: 0;
    }

    .sari-checkout-card,
    .sari-checkout-summary {
        border: 1px solid var(--checkout-border);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .04);
    }

    .sari-checkout-card {
        padding: 22px;
    }

    .sari-checkout-card-head {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .sari-checkout-step {
        display: grid;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        place-items: center;
        border: 1px solid #ead7ad;
        border-radius: 11px;
        background: #fff9ec;
        color: #a96d07;
        font-size: 11px;
        font-weight: 700;
    }

    .sari-checkout-card-kicker {
        margin: 0;
        color: #a8731f;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .11em;
        text-transform: uppercase;
    }

    .sari-checkout-card-title {
        margin: 4px 0 0;
        color: #252a31;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.25;
        letter-spacing: -.025em;
    }

    .sari-checkout-card-copy {
        margin: 5px 0 0;
        color: #848d98;
        font-size: 10px;
        line-height: 1.55;
    }

    .sari-checkout-saved {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(230px, 310px);
        gap: 18px;
        align-items: end;
        margin-top: 20px;
        padding: 0 0 18px;
        border: 0;
        border-bottom: 1px solid #edf0f2;
        border-radius: 0;
        background: transparent;
    }

    .sari-checkout-saved strong {
        display: block;
        color: #343c45;
        font-size: 11px;
        font-weight: 650;
    }

    .sari-checkout-saved p {
        max-width: 560px;
        margin: 4px 0 0;
        color: #8b949f;
        font-size: 9px;
        line-height: 1.5;
    }

    .sari-checkout-address-card .sari-checkout-card-head {
        margin-bottom: 18px;
    }

    .sari-checkout-address-field {
        padding: 0;
    }

    .sari-checkout-address-help {
        margin: 7px 0 0;
        color: #929aa4;
        font-size: 8.5px;
        line-height: 1.5;
    }

    .sari-checkout-fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 18px;
    }

    .sari-field-full {
        grid-column: 1 / -1;
    }

    .sari-checkout-label {
        display: block;
        margin-bottom: 7px;
        color: #5d6670;
        font-size: 10px;
        font-weight: 600;
    }

    .sari-checkout-input,
    .sari-checkout-select,
    .sari-checkout-textarea {
        width: 100%;
        border: 1px solid #dfe4e8;
        border-radius: 10px;
        background: #fff;
        color: #303740;
        font-size: 11px;
        font-weight: 400;
        outline: none;
        transition: border-color .16s ease, box-shadow .16s ease, background-color .16s ease;
    }

    .sari-checkout-input,
    .sari-checkout-select {
        height: 44px;
        padding: 0 13px;
    }

    .sari-checkout-textarea {
        min-height: 100px;
        resize: vertical;
        padding: 12px 13px;
        line-height: 1.55;
    }

    .sari-checkout-input:focus,
    .sari-checkout-select:focus,
    .sari-checkout-textarea:focus {
        border-color: rgba(200, 137, 18, .72);
        box-shadow: 0 0 0 3px rgba(200, 137, 18, .08);
    }

    .sari-map-card {
        margin-top: 16px;
        overflow: hidden;
        border: 1px solid #dfe4e8;
        border-radius: 14px;
        background: #fff;
    }

    .sari-map-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border-bottom: 1px solid #e7eaee;
        background: #fbfcfd;
    }

    .sari-map-toolbar strong {
        display: block;
        color: #333a43;
        font-size: 10.5px;
        font-weight: 650;
    }

    .sari-map-toolbar p {
        margin: 3px 0 0;
        color: #8a939e;
        font-size: 8.5px;
        line-height: 1.4;
    }

    .sari-map-location-btn {
        display: inline-flex;
        min-height: 36px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 1px solid #dfc895;
        border-radius: 9px;
        background: #fffaf0;
        padding: 0 12px;
        color: #956619;
        font-size: 9px;
        font-weight: 650;
        transition: border-color .16s ease, background-color .16s ease;
    }

    .sari-map-location-btn:hover,
    .sari-map-location-btn:focus-visible {
        border-color: #c88912;
        background: #fff5df;
        outline: none;
    }

    .sari-map-location-btn svg {
        width: 15px;
        height: 15px;
    }

    #checkoutDeliveryMap {
        width: 100%;
        height: 310px;
        background: #eef1f4;
    }

    .sari-map-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-height: 46px;
        padding: 10px 14px;
        border-top: 1px solid #e7eaee;
        background: #fff;
    }

    .sari-map-status {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 8px;
        color: #69737e;
        font-size: 9px;
        line-height: 1.4;
    }

    .sari-map-status-dot {
        width: 8px;
        height: 8px;
        flex: 0 0 8px;
        border-radius: 999px;
        background: #c2c9d1;
    }

    .sari-map-status.is-set .sari-map-status-dot {
        background: #4f8065;
        box-shadow: 0 0 0 4px rgba(79,128,101,.10);
    }

    .sari-map-coordinates {
        color: #929aa4;
        font-size: 8px;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .sari-checkout-payment-option {
        display: flex;
        align-items: center;
        gap: 13px;
        margin-top: 17px;
        border: 1px solid #e5d6b4;
        border-radius: 13px;
        background: #fffaf1;
        padding: 15px;
        cursor: pointer;
    }

    .sari-payment-icon {
        display: grid;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        place-items: center;
        border: 1px solid #ead9b5;
        border-radius: 11px;
        background: #fff;
        color: #a8731f;
    }

    .sari-payment-icon svg {
        width: 20px;
        height: 20px;
    }

    .sari-checkout-payment-option input {
        width: 16px;
        height: 16px;
        accent-color: #c88912;
    }

    .sari-payment-copy {
        min-width: 0;
        flex: 1;
    }

    .sari-payment-copy strong {
        display: block;
        color: #343a42;
        font-size: 11.5px;
        font-weight: 650;
    }

    .sari-payment-copy span {
        display: block;
        margin-top: 4px;
        color: #858e99;
        font-size: 9px;
        line-height: 1.45;
    }

    .sari-seller-list {
        display: grid;
        gap: 12px;
        margin-top: 17px;
    }

    .sari-seller-card {
        overflow: hidden;
        border: 1px solid #e7eaed;
        border-radius: 13px;
        background: #fff;
    }

    .sari-seller-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-height: 44px;
        padding: 10px 13px;
        border-bottom: 1px solid #edf0f2;
        background: #fafbfc;
    }

    .sari-seller-head strong {
        color: #815711;
        font-size: 10px;
        font-weight: 650;
    }

    .sari-shipping-promo {
        margin: 11px 12px 0;
        border: 1px solid #d7e6dc;
        border-radius: 10px;
        background: #f5faf7;
        padding: 10px 11px;
    }

    .sari-shipping-promo-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .sari-shipping-promo strong {
        display: block;
        color: #46775a;
        font-size: 9px;
        font-weight: 650;
    }

    .sari-shipping-promo p {
        margin: 3px 0 0;
        color: #708078;
        font-size: 8px;
        line-height: 1.45;
    }

    .sari-shipping-badge {
        flex: 0 0 auto;
        border-radius: 999px;
        padding: 5px 8px;
        font-size: 7.5px;
        font-weight: 650;
        white-space: nowrap;
    }

    .sari-shipping-badge.is-eligible {
        background: #e5f5eb;
        color: #397451;
    }

    .sari-shipping-badge.is-pending {
        border: 1px solid #d5e3da;
        background: #fff;
        color: #748178;
    }

    .sari-seller-items {
        padding: 4px 13px 9px;
    }

    .sari-seller-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 10px 0;
    }

    .sari-seller-item + .sari-seller-item {
        border-top: 1px solid #eff1f3;
    }

    .sari-seller-item-name {
        margin: 0;
        color: #3e454e;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.4;
    }

    .sari-seller-item-variant {
        margin: 4px 0 0;
        color: #9199a3;
        font-size: 8px;
        line-height: 1.35;
    }

    .sari-seller-item-price {
        flex: 0 0 auto;
        color: #2e343b;
        font-size: 10px;
        font-weight: 650;
        font-variant-numeric: tabular-nums;
    }

    .sari-checkout-summary {
        position: sticky;
        top: 92px;
        padding: 20px;
    }

    .sari-summary-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .sari-summary-head h2 {
        margin: 0;
        color: #252a31;
        font-size: 17px;
        font-weight: 700;
        letter-spacing: -.02em;
    }

    .sari-summary-chip {
        border-radius: 999px;
        background: #f5f6f8;
        padding: 5px 8px;
        color: #7c858f;
        font-size: 7.5px;
        font-weight: 650;
    }

    .sari-voucher-card {
        margin-top: 16px;
        border: 1px solid #e5e8eb;
        border-radius: 12px;
        background: #fafbfc;
        padding: 13px;
    }

    .sari-voucher-label {
        display: block;
        color: #767f89;
        font-size: 8.5px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sari-voucher-row {
        display: flex;
        gap: 8px;
        margin-top: 8px;
    }

    .sari-voucher-row .sari-checkout-input {
        min-width: 0;
        flex: 1;
        height: 40px;
        text-transform: uppercase;
    }

    .sari-voucher-apply {
        min-width: 72px;
        height: 40px;
        flex: 0 0 auto;
        border: 1px solid #d7ad59;
        border-radius: 9px;
        background: #fff9ec;
        padding: 0 13px;
        color: #956619;
        font-size: 9px;
        font-weight: 700;
        transition: background-color .16s ease, border-color .16s ease;
    }

    .sari-voucher-apply:hover,
    .sari-voucher-apply:focus-visible {
        border-color: #c88912;
        background: #fff2d2;
        outline: none;
    }

    .sari-voucher-help {
        margin: 8px 0 0;
        color: #939ba4;
        font-size: 8px;
        line-height: 1.5;
    }

    .sari-voucher-success,
    .sari-voucher-error {
        margin-top: 10px;
        border-radius: 9px;
        padding: 9px 10px;
        font-size: 8.5px;
        line-height: 1.45;
    }

    .sari-voucher-success {
        border: 1px solid #d9e8de;
        background: #f4faf6;
        color: #4d735c;
    }

    .sari-voucher-error {
        border: 1px solid #ead7d2;
        background: #fff7f5;
        color: #9b5f55;
    }

    .sari-voucher-offers {
        margin-top: 11px;
        padding-top: 11px;
        border-top: 1px solid #e8ebee;
    }

    .sari-voucher-offers-title {
        margin: 0;
        color: #68717b;
        font-size: 8.5px;
        font-weight: 650;
    }

    .sari-voucher-offers-list {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 8px;
    }

    .sari-voucher-offer {
        border: 1px solid #e4cf9f;
        border-radius: 8px;
        background: #fff9ec;
        padding: 7px 9px;
        text-align: left;
    }

    .sari-voucher-offer:hover {
        background: #fff3d7;
    }

    .sari-voucher-offer strong,
    .sari-voucher-offer span {
        display: block;
    }

    .sari-voucher-offer strong {
        color: #93651b;
        font-size: 8px;
        font-weight: 700;
    }

    .sari-voucher-offer span {
        margin-top: 2px;
        color: #8d7b60;
        font-size: 7px;
        line-height: 1.35;
    }

    .sari-summary-totals {
        display: grid;
        gap: 10px;
        margin-top: 17px;
        font-size: 10px;
    }

    .sari-summary-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .sari-summary-row span:first-child {
        color: #7f8892;
    }

    .sari-summary-row strong,
    .sari-summary-row span:last-child {
        color: #343a42;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
        text-align: right;
    }

    .sari-summary-row.is-discount span,
    .sari-summary-row.is-discount strong {
        color: #4f8065;
    }

    .sari-summary-total {
        margin-top: 4px;
        padding-top: 14px;
        border-top: 1px solid #e5e8eb;
    }

    .sari-summary-total .sari-summary-row {
        align-items: flex-end;
    }

    .sari-summary-total .sari-summary-row span:first-child {
        color: #272d34;
        font-size: 11px;
        font-weight: 650;
    }

    .sari-summary-grand {
        color: #b97805 !important;
        font-size: 22px !important;
        font-weight: 750 !important;
        line-height: 1 !important;
    }

    .sari-place-order {
        display: inline-flex;
        width: 100%;
        height: 48px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 17px;
        border: 1px solid #c88912;
        border-radius: 10px;
        background: #c88912;
        color: #fff;
        font-size: 11px;
        font-weight: 650;
        box-shadow: 0 9px 18px rgba(200,137,18,.14);
        transition: background-color .16s ease, border-color .16s ease, transform .16s ease;
    }

    .sari-place-order:hover,
    .sari-place-order:focus-visible {
        border-color: #aa720e;
        background: #aa720e;
        transform: translateY(-1px);
        outline: none;
    }

    .sari-place-order svg {
        width: 16px;
        height: 16px;
    }

    .sari-summary-note {
        margin: 10px 0 0;
        color: #969ea7;
        font-size: 7.8px;
        line-height: 1.5;
        text-align: center;
    }

    .sari-checkout-error {
        margin-bottom: 15px;
        border: 1px solid #efcece;
        border-radius: 11px;
        background: #fff5f5;
        padding: 11px 13px;
        color: #a65353;
        font-size: 10px;
    }

    .leaflet-container {
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }

    .leaflet-control-attribution {
        font-size: 9px !important;
    }

    .sari-delivery-pin-wrap {
        background: transparent;
        border: 0;
    }

    .sari-delivery-pin {
        position: relative;
        width: 34px;
        height: 34px;
        border: 3px solid #fff;
        border-radius: 50% 50% 50% 0;
        background: #c88912;
        box-shadow: 0 6px 14px rgba(45,35,20,.24);
        transform: rotate(-45deg);
    }

    .sari-delivery-pin::after {
        content: '';
        position: absolute;
        top: 9px;
        left: 9px;
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: #fff;
    }

    @media (max-width: 1199px) {
        .sari-checkout-grid {
            grid-template-columns: minmax(0, 1fr) 360px;
        }
    }

    @media (max-width: 1023px) {
        .sari-checkout-grid {
            grid-template-columns: 1fr;
        }

        .sari-checkout-summary {
            position: static;
        }
    }

    @media (max-width: 767px) {
        .sari-checkout-shell {
            padding: 16px 14px 30px;
        }

        .sari-checkout-intro {
            align-items: flex-start;
            flex-direction: column;
            margin-bottom: 14px;
        }

        .sari-checkout-secure {
            display: none;
        }

        .sari-checkout-card,
        .sari-checkout-summary {
            border-radius: 15px;
        }

        .sari-checkout-card {
            padding: 17px;
        }

        .sari-checkout-saved,
        .sari-checkout-fields {
            grid-template-columns: 1fr;
        }

        .sari-field-full {
            grid-column: auto;
        }

        .sari-map-toolbar,
        .sari-map-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .sari-map-location-btn {
            width: 100%;
        }

        #checkoutDeliveryMap {
            height: 260px;
        }

        .sari-map-coordinates {
            white-space: normal;
        }

        .sari-shipping-promo-row {
            flex-direction: column;
        }
    }


    /* ============================================================
       TIKTOK-SHOP INSPIRED CHECKOUT — SARI BRAND
       Compact hierarchy, flatter sections, fewer nested cards.
       Existing checkout IDs, inputs, routes, voucher logic and map JS preserved.
    ============================================================ */
    .sari-checkout--tiktok {
        background: #f5f6f7;
        min-height: calc(100vh - 64px);
    }

    .sari-checkout--tiktok .sari-checkout-shell {
        max-width: 1440px;
        padding: 18px 24px 42px;
    }

    .sari-tiktok-page-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 14px;
        padding: 0 2px;
    }

    .sari-tiktok-page-head h1 {
        margin: 0;
        color: #16181c;
        font-size: 24px;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: -.03em;
    }

    .sari-tiktok-secure {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #727981;
        font-size: 9.5px;
        font-weight: 600;
    }

    .sari-tiktok-secure svg {
        width: 15px;
        height: 15px;
        color: #b67a0f;
    }

    .sari-checkout--tiktok .sari-checkout-grid {
        grid-template-columns: minmax(0, 1fr) 390px;
        gap: 14px;
    }

    .sari-tiktok-stack {
        display: grid;
        gap: 12px;
        min-width: 0;
    }

    .sari-tiktok-panel,
    .sari-tiktok-summary {
        border: 1px solid #e7e9ec;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .025);
    }

    .sari-tiktok-section {
        padding: 18px 20px;
    }

    .sari-tiktok-section + .sari-tiktok-section {
        border-top: 1px solid #eceef0;
    }

    .sari-tiktok-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .sari-tiktok-section-title {
        display: flex;
        align-items: center;
        gap: 9px;
        min-width: 0;
    }

    .sari-tiktok-section-title svg {
        width: 19px;
        height: 19px;
        flex: 0 0 19px;
        color: #a86f0d;
        stroke-width: 1.8;
    }

    .sari-tiktok-section-title h2 {
        margin: 0;
        color: #202328;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.3;
        letter-spacing: -.015em;
    }

    .sari-tiktok-section-title p {
        margin: 2px 0 0;
        color: #949aa2;
        font-size: 8.5px;
        line-height: 1.4;
    }

    .sari-tiktok-saved-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(220px, 300px);
        gap: 16px;
        align-items: center;
        margin-top: 15px;
        padding: 12px 0 0;
        border-top: 1px solid #eff0f2;
    }

    .sari-tiktok-saved-label strong {
        display: block;
        color: #363b42;
        font-size: 10px;
        font-weight: 650;
    }

    .sari-tiktok-saved-label span {
        display: block;
        margin-top: 2px;
        color: #989fa7;
        font-size: 8px;
        line-height: 1.4;
    }

    .sari-checkout--tiktok .sari-checkout-select,
    .sari-checkout--tiktok .sari-checkout-input,
    .sari-checkout--tiktok .sari-checkout-textarea {
        border-color: #dfe2e6;
        border-radius: 8px;
        font-size: 10.5px;
        box-shadow: none;
    }

    .sari-checkout--tiktok .sari-checkout-input,
    .sari-checkout--tiktok .sari-checkout-select {
        height: 41px;
        padding: 0 12px;
    }

    .sari-checkout--tiktok .sari-checkout-textarea {
        min-height: 86px;
        padding: 11px 12px;
    }

    .sari-checkout--tiktok .sari-checkout-input:focus,
    .sari-checkout--tiktok .sari-checkout-select:focus,
    .sari-checkout--tiktok .sari-checkout-textarea:focus {
        border-color: #c88912;
        box-shadow: 0 0 0 2px rgba(200,137,18,.08);
    }

    .sari-tiktok-fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 15px;
    }

    .sari-tiktok-field-full {
        grid-column: 1 / -1;
    }

    .sari-checkout--tiktok .sari-checkout-label {
        margin-bottom: 6px;
        color: #666d75;
        font-size: 9px;
        font-weight: 600;
    }

    .sari-tiktok-address-divider {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eff0f2;
    }

    .sari-tiktok-address-help {
        margin: 6px 0 0;
        color: #9aa0a7;
        font-size: 7.8px;
        line-height: 1.45;
    }

    .sari-tiktok-map-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #eff0f2;
    }

    .sari-tiktok-map-head strong {
        display: block;
        color: #353a40;
        font-size: 9.5px;
        font-weight: 650;
    }

    .sari-tiktok-map-head p {
        margin: 2px 0 0;
        color: #969ca4;
        font-size: 7.8px;
        line-height: 1.4;
    }

    .sari-checkout--tiktok .sari-map-location-btn {
        min-height: 34px;
        border-radius: 8px;
        padding: 0 11px;
        font-size: 8.5px;
    }

    .sari-checkout--tiktok .sari-map-card {
        margin-top: 10px;
        border-color: #e2e5e8;
        border-radius: 8px;
        box-shadow: none;
    }

    .sari-checkout--tiktok .sari-map-toolbar {
        display: none;
    }

    .sari-checkout--tiktok #checkoutDeliveryMap {
        height: 235px;
    }

    .sari-checkout--tiktok .sari-map-footer {
        min-height: 40px;
        padding: 8px 11px;
        background: #fbfbfc;
    }

    .sari-checkout--tiktok .sari-map-status {
        font-size: 8px;
    }

    .sari-checkout--tiktok .sari-map-coordinates {
        font-size: 7.5px;
    }

    .sari-tiktok-payment-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 14px;
        padding: 13px 0 0;
        border-top: 1px solid #eff0f2;
        cursor: pointer;
    }

    .sari-tiktok-payment-icon {
        display: grid;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        place-items: center;
        border-radius: 8px;
        background: #fff7e8;
        color: #a86f0d;
    }

    .sari-tiktok-payment-icon svg {
        width: 18px;
        height: 18px;
    }

    .sari-tiktok-payment-copy {
        min-width: 0;
        flex: 1;
    }

    .sari-tiktok-payment-copy strong {
        display: block;
        color: #343940;
        font-size: 10.5px;
        font-weight: 650;
    }

    .sari-tiktok-payment-copy span {
        display: block;
        margin-top: 2px;
        color: #969ca4;
        font-size: 8px;
        line-height: 1.4;
    }

    .sari-tiktok-payment-row input {
        width: 16px;
        height: 16px;
        flex: 0 0 16px;
        accent-color: #c88912;
    }

    .sari-tiktok-seller {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #eff0f2;
    }

    .sari-tiktok-seller:first-of-type {
        margin-top: 14px;
    }

    .sari-tiktok-seller-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 8px;
    }

    .sari-tiktok-store {
        display: flex;
        align-items: center;
        gap: 7px;
        color: #2f343a;
        font-size: 10px;
        font-weight: 650;
    }

    .sari-tiktok-store svg {
        width: 16px;
        height: 16px;
        color: #a86f0d;
    }

    .sari-tiktok-promo-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 0 0 5px;
        padding: 8px 10px;
        border-radius: 6px;
        background: #f3faf5;
    }

    .sari-tiktok-promo-copy strong {
        display: block;
        color: #3f7352;
        font-size: 8.5px;
        font-weight: 650;
    }

    .sari-tiktok-promo-copy span {
        display: block;
        margin-top: 2px;
        color: #77867d;
        font-size: 7.3px;
        line-height: 1.35;
    }

    .sari-tiktok-promo-badge {
        flex: 0 0 auto;
        color: #3f7352;
        font-size: 7.5px;
        font-weight: 650;
        white-space: nowrap;
    }

    .sari-tiktok-promo-badge.is-pending {
        color: #8b9299;
    }

    .sari-tiktok-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 11px 0;
    }

    .sari-tiktok-item + .sari-tiktok-item {
        border-top: 1px solid #f0f1f2;
    }

    .sari-tiktok-item-name {
        margin: 0;
        color: #33383f;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.4;
    }

    .sari-tiktok-item-variant {
        margin: 3px 0 0;
        color: #9a9fa6;
        font-size: 7.8px;
        line-height: 1.4;
    }

    .sari-tiktok-item-price {
        flex: 0 0 auto;
        color: #202328;
        font-size: 12px;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
        padding-top: 3px;
    }

    /* Seller profile + product thumbnails */
    .sari-tiktok-store {
        gap: 10px;
        min-width: 0;
    }

    .sari-tiktok-store-profile {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        overflow: hidden;
        place-items: center;
        border: 1px solid #e2e5e8;
        border-radius: 999px;
        background: #f5f6f7;
        color: #9b6811;
        text-decoration: none;
    }

    .sari-tiktok-store-profile img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .sari-tiktok-store-fallback {
        display: grid;
        width: 100%;
        height: 100%;
        place-items: center;
        background: #fff7e8;
        color: #9b6811;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: -.02em;
    }

    .sari-tiktok-store-copy {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: 2px;
    }

    .sari-tiktok-store-name {
        overflow: hidden;
        color: #252a30;
        font-size: 11.5px;
        font-weight: 700;
        line-height: 1.3;
        text-decoration: none;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    a.sari-tiktok-store-name:hover {
        color: #a86f0d;
    }

    .sari-tiktok-store-meta {
        color: #969da5;
        font-size: 7.5px;
        font-weight: 500;
    }

    .sari-tiktok-item {
        align-items: center;
        padding: 13px 0;
    }

    .sari-tiktok-item-main {
        display: flex;
        min-width: 0;
        flex: 1 1 auto;
        align-items: center;
        gap: 12px;
    }

    .sari-tiktok-product-thumb {
        display: grid;
        width: 72px;
        height: 72px;
        flex: 0 0 72px;
        overflow: hidden;
        place-items: center;
        border: 1px solid #e5e7ea;
        border-radius: 8px;
        background: #f6f7f8;
    }

    .sari-tiktok-product-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .18s ease;
    }

    .sari-tiktok-product-thumb:hover img {
        transform: scale(1.035);
    }

    .sari-tiktok-product-fallback {
        display: grid;
        width: 100%;
        height: 100%;
        place-items: center;
        color: #a1a7ae;
    }

    .sari-tiktok-product-fallback svg {
        width: 24px;
        height: 24px;
    }

    .sari-tiktok-item-copy {
        min-width: 0;
    }

    .sari-tiktok-item-name {
        display: block;
        color: #252a30;
        font-size: 11.5px;
        font-weight: 650;
        line-height: 1.4;
        text-decoration: none;
    }

    a.sari-tiktok-item-name:hover {
        color: #a86f0d;
    }

    .sari-tiktok-item-variant {
        margin-top: 4px;
        color: #8f969e;
        font-size: 8.5px;
    }

    .sari-tiktok-item-qty {
        display: inline-block;
        margin-top: 5px;
        color: #626b74;
        font-size: 8.5px;
        font-weight: 600;
    }

    /* Saved address now belongs under Complete Delivery Address. */
    .sari-tiktok-address-divider + .sari-tiktok-saved-row {
        margin-top: 13px;
        padding-top: 13px;
    }

    .sari-tiktok-summary {
        position: sticky;
        top: 88px;
        overflow: hidden;
    }

    .sari-tiktok-summary-head {
        padding: 17px 18px 13px;
        border-bottom: 1px solid #eceef0;
    }

    .sari-tiktok-summary-head h2 {
        margin: 0;
        color: #202328;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.02em;
    }

    .sari-tiktok-summary-body {
        padding: 0 18px 18px;
    }

    .sari-checkout--tiktok .sari-voucher-card {
        margin-top: 0;
        border: 0;
        border-bottom: 1px solid #eceef0;
        border-radius: 0;
        background: transparent;
        padding: 14px 0;
    }

    .sari-checkout--tiktok .sari-voucher-label {
        color: #3d4248;
        font-size: 9px;
        letter-spacing: 0;
        text-transform: none;
    }

    .sari-checkout--tiktok .sari-voucher-help {
        display: none;
    }

    .sari-checkout--tiktok .sari-voucher-offers {
        margin-top: 9px;
        padding-top: 9px;
    }

    .sari-checkout--tiktok .sari-summary-totals {
        gap: 9px;
        margin-top: 14px;
        font-size: 9.5px;
    }

    .sari-checkout--tiktok .sari-summary-total {
        margin-top: 5px;
        padding-top: 13px;
    }

    .sari-checkout--tiktok .sari-summary-grand {
        font-size: 21px !important;
    }

    .sari-checkout--tiktok .sari-place-order {
        height: 46px;
        margin-top: 15px;
        border-radius: 7px;
        font-size: 10.5px;
        box-shadow: none;
    }

    .sari-checkout--tiktok .sari-summary-note {
        margin-top: 9px;
        font-size: 7.2px;
    }

    @media (max-width: 1199px) {
        .sari-checkout--tiktok .sari-checkout-grid {
            grid-template-columns: minmax(0, 1fr) 350px;
        }
    }

    @media (max-width: 1023px) {
        .sari-checkout--tiktok .sari-checkout-grid {
            grid-template-columns: 1fr;
        }

        .sari-tiktok-summary {
            position: static;
        }
    }

    @media (max-width: 767px) {
        .sari-checkout--tiktok .sari-checkout-shell {
            padding: 12px 10px 28px;
        }

        .sari-tiktok-page-head {
            margin-bottom: 10px;
        }

        .sari-tiktok-page-head h1 {
            font-size: 20px;
        }

        .sari-tiktok-secure {
            font-size: 8px;
        }

        .sari-tiktok-panel,
        .sari-tiktok-summary {
            border-radius: 8px;
        }

        .sari-tiktok-section {
            padding: 15px 14px;
        }

        .sari-tiktok-saved-row,
        .sari-tiktok-fields {
            grid-template-columns: 1fr;
        }

        .sari-tiktok-field-full {
            grid-column: auto;
        }

        .sari-tiktok-map-head {
            align-items: flex-start;
            flex-direction: column;
        }

        .sari-checkout--tiktok .sari-map-location-btn {
            width: 100%;
        }

        .sari-checkout--tiktok #checkoutDeliveryMap {
            height: 210px;
        }

        .sari-checkout--tiktok .sari-map-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .sari-tiktok-promo-row {
            align-items: flex-start;
            flex-direction: column;
        }

        .sari-tiktok-promo-badge {
            white-space: normal;
        }
    }

    @media (max-width: 639px) {
        .sari-tiktok-saved-row {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .sari-tiktok-store-profile {
            width: 34px;
            height: 34px;
            flex-basis: 34px;
        }

        .sari-tiktok-product-thumb {
            width: 62px;
            height: 62px;
            flex-basis: 62px;
        }

        .sari-tiktok-item {
            align-items: flex-start;
            gap: 10px;
        }

        .sari-tiktok-item-main {
            gap: 10px;
        }

        .sari-tiktok-item-price {
            font-size: 10.5px;
        }
    }


    /* ============================================================
       FINAL READABILITY + LIVE BUYER LOCATION
       Controlled typography increase; compact checkout preserved.
    ============================================================ */
    .sari-checkout--tiktok .sari-tiktok-page-head h1 {
        font-size: 26px !important;
    }

    .sari-checkout--tiktok .sari-tiktok-secure {
        font-size: 11px !important;
    }

    .sari-checkout--tiktok .sari-tiktok-section-title h2 {
        font-size: 16px !important;
        line-height: 1.35 !important;
    }

    .sari-checkout--tiktok .sari-tiktok-section-title p {
        margin-top: 3px !important;
        font-size: 10.5px !important;
        line-height: 1.5 !important;
    }

    .sari-checkout--tiktok .sari-checkout-label {
        margin-bottom: 7px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
    }

    .sari-checkout--tiktok .sari-checkout-select,
    .sari-checkout--tiktok .sari-checkout-input,
    .sari-checkout--tiktok .sari-checkout-textarea {
        font-size: 12.5px !important;
    }

    .sari-checkout--tiktok .sari-checkout-input,
    .sari-checkout--tiktok .sari-checkout-select {
        height: 44px !important;
    }

    .sari-checkout--tiktok .sari-checkout-textarea {
        min-height: 96px !important;
        line-height: 1.55 !important;
    }

    .sari-checkout--tiktok .sari-tiktok-address-help {
        font-size: 10px !important;
        line-height: 1.5 !important;
    }

    .sari-checkout--tiktok .sari-tiktok-saved-label strong {
        font-size: 12px !important;
    }

    .sari-checkout--tiktok .sari-tiktok-saved-label span {
        margin-top: 3px !important;
        font-size: 10px !important;
        line-height: 1.45 !important;
    }

    .sari-checkout--tiktok .sari-tiktok-map-head strong {
        font-size: 11.5px !important;
    }

    .sari-checkout--tiktok .sari-tiktok-map-head p {
        margin-top: 3px !important;
        font-size: 10px !important;
        line-height: 1.45 !important;
    }

    .sari-checkout--tiktok .sari-map-location-btn {
        min-height: 38px !important;
        padding: 0 13px !important;
        font-size: 10.5px !important;
    }

    .sari-checkout--tiktok .sari-map-status {
        font-size: 10px !important;
        line-height: 1.45 !important;
    }

    .sari-checkout--tiktok .sari-map-coordinates {
        font-size: 9.5px !important;
    }

    .sari-checkout--tiktok .sari-tiktok-payment-copy strong {
        font-size: 12.5px !important;
    }

    .sari-checkout--tiktok .sari-tiktok-payment-copy span {
        margin-top: 3px !important;
        font-size: 10px !important;
        line-height: 1.45 !important;
    }

    .sari-checkout--tiktok .sari-tiktok-store-name {
        font-size: 13px !important;
    }

    .sari-checkout--tiktok .sari-tiktok-store-meta {
        font-size: 10px !important;
    }

    .sari-checkout--tiktok .sari-tiktok-store-fallback {
        font-size: 11px !important;
    }

    .sari-checkout--tiktok .sari-tiktok-promo-copy strong {
        font-size: 11px !important;
    }

    .sari-checkout--tiktok .sari-tiktok-promo-copy span {
        margin-top: 3px !important;
        font-size: 9.5px !important;
        line-height: 1.4 !important;
    }

    .sari-checkout--tiktok .sari-tiktok-promo-badge {
        font-size: 9.5px !important;
    }

    .sari-checkout--tiktok .sari-tiktok-item-name {
        font-size: 13px !important;
        line-height: 1.4 !important;
    }

    .sari-checkout--tiktok .sari-tiktok-item-variant,
    .sari-checkout--tiktok .sari-tiktok-item-qty {
        font-size: 10.5px !important;
        line-height: 1.45 !important;
    }

    .sari-checkout--tiktok .sari-tiktok-item-price {
        font-size: 13px !important;
    }

    .sari-checkout--tiktok .sari-tiktok-summary-head h2 {
        font-size: 17px !important;
    }

    .sari-checkout--tiktok .sari-voucher-label {
        font-size: 11px !important;
    }

    .sari-checkout--tiktok .sari-voucher-apply {
        font-size: 10.5px !important;
    }

    .sari-checkout--tiktok .sari-voucher-success,
    .sari-checkout--tiktok .sari-voucher-error,
    .sari-checkout--tiktok .sari-voucher-offers-title {
        font-size: 10px !important;
        line-height: 1.45 !important;
    }

    .sari-checkout--tiktok .sari-voucher-offer strong {
        font-size: 10px !important;
    }

    .sari-checkout--tiktok .sari-voucher-offer span {
        font-size: 9px !important;
    }

    .sari-checkout--tiktok .sari-summary-totals {
        font-size: 11.5px !important;
        line-height: 1.45 !important;
    }

    .sari-checkout--tiktok .sari-summary-total .sari-summary-row span:first-child {
        font-size: 12.5px !important;
    }

    .sari-checkout--tiktok .sari-summary-grand {
        font-size: 23px !important;
    }

    .sari-checkout--tiktok .sari-place-order {
        height: 48px !important;
        font-size: 12.5px !important;
    }

    .sari-checkout--tiktok .sari-summary-note {
        font-size: 9.5px !important;
        line-height: 1.5 !important;
    }

    .sari-checkout--tiktok .sari-checkout-error {
        font-size: 11.5px !important;
    }

    /* Buyer map pin: red/pink, clearly visible over OSM tiles. */
    .sari-checkout--tiktok .sari-delivery-pin {
        background: #ef4056 !important;
        box-shadow:
            0 0 0 5px rgba(239, 64, 86, .14),
            0 8px 18px rgba(117, 25, 39, .28) !important;
    }

    .sari-checkout--tiktok .sari-map-status.is-set .sari-map-status-dot {
        background: #ef4056 !important;
        box-shadow: 0 0 0 4px rgba(239, 64, 86, .12) !important;
    }

    @media (max-width: 767px) {
        .sari-checkout--tiktok .sari-tiktok-page-head h1 {
            font-size: 22px !important;
        }

        .sari-checkout--tiktok .sari-tiktok-section-title h2 {
            font-size: 15px !important;
        }

        .sari-checkout--tiktok .sari-checkout-label,
        .sari-checkout--tiktok .sari-tiktok-saved-label strong,
        .sari-checkout--tiktok .sari-tiktok-map-head strong {
            font-size: 10.5px !important;
        }

        .sari-checkout--tiktok .sari-checkout-select,
        .sari-checkout--tiktok .sari-checkout-input,
        .sari-checkout--tiktok .sari-checkout-textarea {
            font-size: 12px !important;
        }

        .sari-checkout--tiktok .sari-tiktok-item-name,
        .sari-checkout--tiktok .sari-tiktok-store-name,
        .sari-checkout--tiktok .sari-tiktok-item-price {
            font-size: 12px !important;
        }

        .sari-checkout--tiktok .sari-summary-totals {
            font-size: 11px !important;
        }
    }

</style>
@endpush

@section('content')
@include('components.buyer.header')

<div class="sari-checkout sari-checkout--tiktok">
    <div class="sari-checkout-shell">
        <div class="sari-tiktok-page-head">
            <h1>Checkout</h1>
            <span class="sari-tiktok-secure">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                    <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                </svg>
                Secure checkout
            </span>
        </div>

        <form id="buyerCheckoutForm" method="POST" action="{{ route('buyer.checkout.store') }}" class="sari-checkout-grid">
            @csrf

            @if (!empty($buyNowItemId))
                <input type="hidden" name="buy_now_item_id" value="{{ (int) $buyNowItemId }}">
            @elseif (!empty($selectedItemIds))
                @foreach ($selectedItemIds as $selectedItemId)
                    <input type="hidden" name="checkout_item_ids[]" value="{{ (int) $selectedItemId }}">
                @endforeach
            @endif

            <input id="checkoutDeliveryLatitude" type="hidden" name="delivery_latitude" value="{{ old('delivery_latitude') }}">
            <input id="checkoutDeliveryLongitude" type="hidden" name="delivery_longitude" value="{{ old('delivery_longitude') }}">

            <div class="sari-tiktok-stack">
                <section class="sari-tiktok-panel" aria-labelledby="checkoutDeliveryTitle">
                    <div class="sari-tiktok-section">
                        <div class="sari-tiktok-section-head">
                            <div class="sari-tiktok-section-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path d="M12 21s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12Z"></path>
                                    <circle cx="12" cy="9" r="2.4"></circle>
                                </svg>
                                <div>
                                    <h2 id="checkoutDeliveryTitle">Shipping address</h2>
                                    <p>Confirm the recipient and delivery details.</p>
                                </div>
                            </div>
                        </div>

                        <div class="sari-tiktok-fields">
                            <div>
                                <label for="checkoutBuyerName" class="sari-checkout-label">Full Name</label>
                                <input
                                    id="checkoutBuyerName"
                                    name="buyer_name"
                                    value="{{ old('buyer_name', $profile['name']) }}"
                                    required
                                    autocomplete="name"
                                    class="sari-checkout-input"
                                >
                            </div>

                            <div>
                                <label for="checkoutBuyerEmail" class="sari-checkout-label">Email</label>
                                <input
                                    id="checkoutBuyerEmail"
                                    name="buyer_email"
                                    type="email"
                                    value="{{ old('buyer_email', $profile['email']) }}"
                                    required
                                    autocomplete="email"
                                    class="sari-checkout-input"
                                >
                            </div>

                            <div class="sari-tiktok-field-full">
                                <label for="checkoutBuyerPhone" class="sari-checkout-label">Contact Number</label>
                                <input
                                    id="checkoutBuyerPhone"
                                    name="buyer_phone"
                                    value="{{ old('buyer_phone', $profile['phone']) }}"
                                    required
                                    inputmode="tel"
                                    autocomplete="tel"
                                    placeholder="09xxxxxxxxx"
                                    class="sari-checkout-input"
                                >
                            </div>
                        </div>

                        <div class="sari-tiktok-address-divider">
                            <label for="checkoutBuyerAddress" class="sari-checkout-label">Complete Delivery Address</label>
                            <textarea
                                id="checkoutBuyerAddress"
                                name="buyer_address"
                                required
                                rows="3"
                                autocomplete="street-address"
                                class="sari-checkout-textarea"
                                placeholder="House/Unit, Street, Barangay, City/Municipality, Province"
                            >{{ old('buyer_address', $profile['address']) }}</textarea>
                            <p class="sari-tiktok-address-help">Include house/unit, street, barangay, city or municipality, and province.</p>
                        </div>


                        @if (!empty($savedAddresses) && $savedAddresses->isNotEmpty())
                            <div class="sari-tiktok-saved-row">
                                <div class="sari-tiktok-saved-label">
                                    <strong>Saved address</strong>
                                    <span>Select an address to fill the details automatically.</span>
                                </div>

                                <select id="checkoutSavedAddress" class="sari-checkout-select" aria-label="Saved delivery address">
                                    <option
                                        value=""
                                        data-recipient="{{ $registeredProfile['name'] ?? $profile['name'] }}"
                                        data-phone="{{ $registeredProfile['phone'] ?? $profile['phone'] }}"
                                        data-address="{{ $registeredProfile['address'] ?? $profile['address'] }}"
                                        @selected(empty($selectedAddressId))
                                    >
                                        Registered address{{ empty($selectedAddressId) ? ' · Active' : '' }}
                                    </option>

                                    @foreach ($savedAddresses as $address)
                                        <option
                                            value="{{ $address->id }}"
                                            data-recipient="{{ $address->recipient_name }}"
                                            data-phone="{{ $address->contact_no }}"
                                            data-address="{{ $address->fullAddress() }}"
                                            @selected((int) ($selectedAddressId ?? 0) === (int) $address->id)
                                        >
                                            {{ $address->label }}{{ $address->is_default ? ' · Default' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="sari-tiktok-map-head">
                            <div>
                                <strong>Precise delivery pin <span style="font-weight:500;color:#9aa0a7;">(optional)</span></strong>
                                <p>Tap the map or drag the marker to help the courier find the exact location.</p>
                            </div>

                            <button id="checkoutUseLocation" type="button" class="sari-map-location-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <circle cx="12" cy="12" r="8"></circle>
                                    <path d="M12 2v2M12 20v2M2 12h2M20 12h2"></path>
                                </svg>
                                Use my current location
                            </button>
                        </div>

                        <div class="sari-map-card">
                            <div id="checkoutDeliveryMap" aria-label="OpenStreetMap delivery location picker"></div>
                            <div class="sari-map-footer">
                                <div id="checkoutMapStatus" class="sari-map-status">
                                    <span class="sari-map-status-dot" aria-hidden="true"></span>
                                    <span id="checkoutMapStatusText">No precise pin selected yet.</span>
                                </div>
                                <span id="checkoutMapCoordinates" class="sari-map-coordinates">Latitude — · Longitude —</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="sari-tiktok-panel" aria-labelledby="checkoutPaymentTitle">
                    <div class="sari-tiktok-section">
                        <div class="sari-tiktok-section-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                <path d="M3 9h18"></path>
                            </svg>
                            <div>
                                <h2 id="checkoutPaymentTitle">Payment method</h2>
                            </div>
                        </div>

                        <label class="sari-tiktok-payment-row">
                            <span class="sari-tiktok-payment-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <rect x="3" y="6" width="18" height="12" rx="2"></rect>
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M7 9H5M19 15h-2"></path>
                                </svg>
                            </span>
                            <span class="sari-tiktok-payment-copy">
                                <strong>Cash on Delivery</strong>
                                <span>Pay the courier when your package arrives.</span>
                            </span>
                            <input type="radio" name="payment_method" value="COD" checked aria-label="Cash on Delivery">
                        </label>
                    </div>
                </section>

                <section class="sari-tiktok-panel" aria-labelledby="checkoutProductsTitle">
                    <div class="sari-tiktok-section">
                        <div class="sari-tiktok-section-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="M4 7h16l-1 13H5L4 7Z"></path>
                                <path d="M8 7a4 4 0 0 1 8 0"></path>
                            </svg>
                            <div>
                                <h2 id="checkoutProductsTitle">Order items</h2>
                            </div>
                        </div>

                        @foreach ($groups as $sellerId => $sellerItems)
                            @php
                                $seller = $sellerItems->first()?->product?->seller;
                                $storeName = $seller?->store_name ?: 'SARI Seller Store';
                                $storeProfilePath = $seller?->store_logo_path ?: $seller?->profile_image_path;
                                $storeProfileUrl = filled($storeProfilePath)
                                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($storeProfilePath)
                                    : null;
                                $storeInitials = collect(preg_split('/\s+/', trim($storeName)))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                                    ->implode('');
                                $storeInitials = $storeInitials !== '' ? $storeInitials : 'S';
                                $storeUrl = (int) $sellerId > 0
                                    ? route('buyer.shop', ['shop' => 'seller-' . (int) $sellerId])
                                    : null;

                                $sellerShipping = $shippingBySeller[(int) $sellerId] ?? null;
                                $shippingOffer = $sellerShipping['promotion'] ?? null;
                            @endphp

                            <div class="sari-tiktok-seller">
                                <div class="sari-tiktok-seller-head">
                                    <div class="sari-tiktok-store">
                                        @if ($storeUrl)
                                            <a href="{{ $storeUrl }}" class="sari-tiktok-store-profile" aria-label="Visit {{ $storeName }}">
                                                @if ($storeProfileUrl)
                                                    <img
                                                        src="{{ $storeProfileUrl }}"
                                                        alt="{{ $storeName }} profile"
                                                        loading="lazy"
                                                        decoding="async"
                                                        onerror="this.hidden=true; this.nextElementSibling.hidden=false;"
                                                    >
                                                    <span class="sari-tiktok-store-fallback" hidden aria-hidden="true">{{ $storeInitials }}</span>
                                                @else
                                                    <span class="sari-tiktok-store-fallback" aria-hidden="true">{{ $storeInitials }}</span>
                                                @endif
                                            </a>
                                        @else
                                            <span class="sari-tiktok-store-profile">
                                                @if ($storeProfileUrl)
                                                    <img
                                                        src="{{ $storeProfileUrl }}"
                                                        alt="{{ $storeName }} profile"
                                                        loading="lazy"
                                                        decoding="async"
                                                        onerror="this.hidden=true; this.nextElementSibling.hidden=false;"
                                                    >
                                                    <span class="sari-tiktok-store-fallback" hidden aria-hidden="true">{{ $storeInitials }}</span>
                                                @else
                                                    <span class="sari-tiktok-store-fallback" aria-hidden="true">{{ $storeInitials }}</span>
                                                @endif
                                            </span>
                                        @endif

                                        <div class="sari-tiktok-store-copy">
                                            @if ($storeUrl)
                                                <a href="{{ $storeUrl }}" class="sari-tiktok-store-name">{{ $storeName }}</a>
                                            @else
                                                <span class="sari-tiktok-store-name">{{ $storeName }}</span>
                                            @endif
                                            <span class="sari-tiktok-store-meta">{{ $sellerItems->count() }} item{{ $sellerItems->count() === 1 ? '' : 's' }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if($shippingOffer && ($shippingOffer['active'] ?? false))
                                    <div class="sari-tiktok-promo-row">
                                        <div class="sari-tiktok-promo-copy">
                                            <strong>Free Shipping Promotion</strong>
                                            <span>
                                                Minimum ₱{{ number_format((float) ($shippingOffer['minimum_order_amount'] ?? 0), 2) }}
                                                · up to ₱{{ number_format((float) ($shippingOffer['maximum_shipping_discount'] ?? 0), 2) }} off
                                            </span>
                                        </div>

                                        <span class="sari-tiktok-promo-badge {{ ($shippingOffer['eligible'] ?? false) ? '' : 'is-pending' }}">
                                            @if($shippingOffer['eligible'] ?? false)
                                                Applied · save ₱{{ number_format((float) ($shippingOffer['shipping_discount'] ?? 0), 2) }}
                                            @else
                                                Add ₱{{ number_format((float) ($shippingOffer['remaining_to_qualify'] ?? 0), 2) }} more
                                            @endif
                                        </span>
                                    </div>
                                @endif

                                @foreach ($sellerItems as $item)
                                    <div class="sari-tiktok-item">
                                        <div class="sari-tiktok-item-main">
                                            <a
                                                href="{{ route('buyer.product.details', $item->product->id) }}"
                                                class="sari-tiktok-product-thumb"
                                                aria-label="View {{ $item->product->name }}"
                                            >
                                                <img
                                                    src="{{ route('buyer.product.image', $item->product->id) }}"
                                                    alt="{{ $item->product->name }}"
                                                    loading="lazy"
                                                    decoding="async"
                                                    onerror="this.hidden=true; this.nextElementSibling.hidden=false;"
                                                >
                                                <span class="sari-tiktok-product-fallback" hidden aria-hidden="true">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                                        <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                                                        <circle cx="9" cy="9" r="1.5"></circle>
                                                        <path d="m5 17 4-4 3 3 2-2 5 5"></path>
                                                    </svg>
                                                </span>
                                            </a>

                                            <div class="sari-tiktok-item-copy">
                                                <a href="{{ route('buyer.product.details', $item->product->id) }}" class="sari-tiktok-item-name">
                                                    {{ $item->product->name }}
                                                </a>
                                                <p class="sari-tiktok-item-variant">{{ $cart->variantLabel($item) }}</p>
                                                <span class="sari-tiktok-item-qty">Qty: {{ $item->quantity }}</span>
                                            </div>
                                        </div>

                                        <span class="sari-tiktok-item-price">₱{{ number_format($cart->lineTotal($item), 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <aside class="sari-tiktok-summary" aria-labelledby="checkoutSummaryTitle">
                <div class="sari-tiktok-summary-head">
                    <h2 id="checkoutSummaryTitle">Order summary</h2>
                </div>

                <div class="sari-tiktok-summary-body">
                    <div class="sari-voucher-card">
                        <label for="checkoutVoucherCode" class="sari-voucher-label">Voucher</label>
                        <div class="sari-voucher-row">
                            <input
                                id="checkoutVoucherCode"
                                name="voucher_code"
                                value="{{ old('voucher_code', $appliedVoucherCode ?? '') }}"
                                placeholder="Enter voucher code"
                                class="sari-checkout-input"
                            >
                            <button id="applyCheckoutVoucherButton" type="button" class="sari-voucher-apply">Apply</button>
                        </div>

                        @if(($platformVoucherDiscount ?? 0) > 0)
                            <div class="sari-voucher-success">
                                <strong>SARI voucher applied · {{ $appliedVoucherCode }}</strong><br>
                                You save ₱{{ number_format((float) $platformVoucherDiscount, 2) }}.
                            </div>
                        @elseif(!empty($voucherPreviewError))
                            <div class="sari-voucher-error">{{ $voucherPreviewError }}</div>
                        @endif

                        @if(!empty($platformVoucherOffers))
                            <div class="sari-voucher-offers">
                                <p class="sari-voucher-offers-title">Available vouchers</p>
                                <div class="sari-voucher-offers-list">
                                    @foreach($platformVoucherOffers as $offer)
                                        <button type="button" data-platform-voucher-code="{{ $offer['code'] }}" class="sari-voucher-offer">
                                            <strong>{{ $offer['code'] }}</strong>
                                            <span>{{ $offer['value'] }}@if(($offer['minimum'] ?? 0) > 0) · Min ₱{{ number_format((float) $offer['minimum'], 2) }}@endif</span>
                                            @if(!empty($offer['ends_at_pht']))
                                                <span>Until {{ $offer['ends_at_pht'] }} PHT</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="sari-summary-totals">
                        @if(($platformSaleDiscount ?? 0) > 0)
                            <div class="sari-summary-row">
                                <span>Merchandise subtotal</span>
                                <strong>₱{{ number_format($sellerSubtotal, 2) }}</strong>
                            </div>
                            <div class="sari-summary-row is-discount">
                                <span>SARI event discount</span>
                                <strong>-₱{{ number_format($platformSaleDiscount, 2) }}</strong>
                            </div>
                            <div class="sari-summary-row">
                                <span>Products payable</span>
                                <strong>₱{{ number_format($subtotal, 2) }}</strong>
                            </div>
                        @else
                            <div class="sari-summary-row">
                                <span>Merchandise subtotal</span>
                                <strong>₱{{ number_format($subtotal, 2) }}</strong>
                            </div>
                        @endif

                        @if(($platformVoucherDiscount ?? 0) > 0)
                            <div class="sari-summary-row is-discount">
                                <span>Voucher discount</span>
                                <strong>-₱{{ number_format((float) $platformVoucherDiscount, 2) }}</strong>
                            </div>
                        @endif

                        <div class="sari-summary-row">
                            <span>Shipping fee</span>
                            <strong>₱{{ number_format($originalDeliveryFee, 2) }}</strong>
                        </div>

                        @if(($shippingDiscount ?? 0) > 0)
                            <div class="sari-summary-row is-discount">
                                <span>Shipping discount</span>
                                <strong>-₱{{ number_format($shippingDiscount, 2) }}</strong>
                            </div>
                        @endif

                        <div class="sari-summary-row">
                            <span>Shipping payable</span>
                            <strong>₱{{ number_format($deliveryFee, 2) }}</strong>
                        </div>

                        <div class="sari-summary-total">
                            <div class="sari-summary-row">
                                <span>Total</span>
                                <strong class="sari-summary-grand">₱{{ number_format($grandTotal, 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    <button id="buyerPlaceOrderButton" type="submit" class="sari-place-order">
                        Place Order
                    </button>

                    <p class="sari-summary-note">By placing the order, stock, vouchers, shipping discounts and totals are revalidated securely on the server.</p>
                </div>
            </aside>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/buyer-checkout.js')

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
    ></script>

    <script>
        (() => {
            const voucherInput = document.getElementById('checkoutVoucherCode');
            const applyButton = document.getElementById('applyCheckoutVoucherButton');
            const savedAddress = document.getElementById('checkoutSavedAddress');
            const buyerName = document.getElementById('checkoutBuyerName');
            const buyerPhone = document.getElementById('checkoutBuyerPhone');
            const buyerAddress = document.getElementById('checkoutBuyerAddress');
            const latitudeInput = document.getElementById('checkoutDeliveryLatitude');
            const longitudeInput = document.getElementById('checkoutDeliveryLongitude');
            const useLocationButton = document.getElementById('checkoutUseLocation');
            const mapStatus = document.getElementById('checkoutMapStatus');
            const mapStatusText = document.getElementById('checkoutMapStatusText');
            const coordinatesText = document.getElementById('checkoutMapCoordinates');
            const mapElement = document.getElementById('checkoutDeliveryMap');

            let checkoutMap = null;
            let checkoutMarker = null;
            let checkoutAccuracyCircle = null;

            function clearDeliveryPin() {
                if (checkoutMarker && checkoutMap) {
                    checkoutMap.removeLayer(checkoutMarker);
                }

                checkoutMarker = null;

                if (checkoutAccuracyCircle && checkoutMap) {
                    checkoutMap.removeLayer(checkoutAccuracyCircle);
                }
                checkoutAccuracyCircle = null;

                if (latitudeInput) latitudeInput.value = '';
                if (longitudeInput) longitudeInput.value = '';

                mapStatus?.classList.remove('is-set');
                if (mapStatusText) mapStatusText.textContent = 'No precise pin selected yet.';
                if (coordinatesText) coordinatesText.textContent = 'Latitude — · Longitude —';
            }

            function applySavedAddress() {
                const option = savedAddress?.selectedOptions?.[0];
                if (!option) return;

                if (buyerName) buyerName.value = option.dataset.recipient || buyerName.value;
                if (buyerPhone) buyerPhone.value = option.dataset.phone || buyerPhone.value;
                if (buyerAddress) buyerAddress.value = option.dataset.address || buyerAddress.value;

                clearDeliveryPin();
            }

            savedAddress?.addEventListener('change', applySavedAddress);

            function openVoucherPreview(code) {
                const value = String(code || '').trim().toUpperCase();
                const url = new URL(@json(route('buyer.checkout')), window.location.origin);

                if (value) {
                    url.searchParams.set('voucher_code', value);
                }

                @if (!empty($buyNowItemId))
                    url.searchParams.set('buy_now', @json((int) $buyNowItemId));
                @else
                    url.searchParams.set('selection', '1');
                    @foreach ($selectedItemIds as $selectedItemId)
                        url.searchParams.append('items[]', @json((int) $selectedItemId));
                    @endforeach
                @endif

                window.location.assign(url.toString());
            }

            applyButton?.addEventListener('click', () => openVoucherPreview(voucherInput?.value));

            voucherInput?.addEventListener('keydown', (event) => {
                if (event.key !== 'Enter') return;
                event.preventDefault();
                openVoucherPreview(voucherInput.value);
            });

            document.addEventListener('click', (event) => {
                const button = event.target.closest('[data-platform-voucher-code]');
                if (!button) return;

                if (voucherInput) {
                    voucherInput.value = button.dataset.platformVoucherCode || '';
                }

                openVoucherPreview(button.dataset.platformVoucherCode || '');
            });

            function updatePin(latitude, longitude, { center = true, zoom = 17, statusText = 'Precise delivery pin selected.' } = {}) {
                if (!checkoutMap || !Number.isFinite(latitude) || !Number.isFinite(longitude)) return;

                const lat = Number(latitude.toFixed(7));
                const lng = Number(longitude.toFixed(7));

                if (!checkoutMarker) {
                    const pinIcon = window.L.divIcon({
                        className: 'sari-delivery-pin-wrap',
                        html: '<span class="sari-delivery-pin" aria-hidden="true"></span>',
                        iconSize: [34, 42],
                        iconAnchor: [17, 38],
                    });

                    checkoutMarker = window.L.marker([lat, lng], {
                        draggable: true,
                        icon: pinIcon,
                        title: 'Delivery location',
                    }).addTo(checkoutMap);

                    checkoutMarker.on('dragend', () => {
                        const position = checkoutMarker.getLatLng();
                        updatePin(position.lat, position.lng, {
                            center: false,
                            statusText: 'Delivery pin adjusted. Drag again if needed.',
                        });
                    });
                } else {
                    checkoutMarker.setLatLng([lat, lng]);
                }

                if (center) {
                    checkoutMap.flyTo([lat, lng], zoom, {
                        animate: true,
                        duration: .8,
                    });
                }

                if (latitudeInput) latitudeInput.value = String(lat);
                if (longitudeInput) longitudeInput.value = String(lng);

                mapStatus?.classList.add('is-set');
                if (mapStatusText) mapStatusText.textContent = statusText;
                if (coordinatesText) coordinatesText.textContent = `Latitude ${lat.toFixed(6)} · Longitude ${lng.toFixed(6)}`;
            }

            function locateBuyer({ automatic = false } = {}) {
                if (!navigator.geolocation) {
                    if (mapStatusText) mapStatusText.textContent = 'Location access is not supported by this browser. Click the map to place the pin manually.';
                    return;
                }

                if (useLocationButton) {
                    useLocationButton.disabled = true;
                    useLocationButton.style.opacity = '.7';
                }

                if (mapStatusText) {
                    mapStatusText.textContent = automatic
                        ? 'Finding your current location…'
                        : 'Getting your current location…';
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = Number(position.coords.latitude);
                        const longitude = Number(position.coords.longitude);
                        const accuracy = Number(position.coords.accuracy || 0);

                        updatePin(latitude, longitude, {
                            center: true,
                            zoom: 18,
                            statusText: 'You are here. The red pin marks your current delivery location.',
                        });

                        if (checkoutAccuracyCircle && checkoutMap) {
                            checkoutMap.removeLayer(checkoutAccuracyCircle);
                        }

                        if (checkoutMap && Number.isFinite(accuracy) && accuracy > 0) {
                            checkoutAccuracyCircle = window.L.circle([latitude, longitude], {
                                radius: accuracy,
                                color: '#ef4056',
                                weight: 1,
                                opacity: .42,
                                fillColor: '#ef4056',
                                fillOpacity: .07,
                                interactive: false,
                            }).addTo(checkoutMap);
                        }

                        window.setTimeout(() => {
                            checkoutMap?.invalidateSize();
                            checkoutMap?.panTo([latitude, longitude], { animate: true });
                        }, 120);

                        if (useLocationButton) {
                            useLocationButton.disabled = false;
                            useLocationButton.style.opacity = '';
                        }
                    },
                    () => {
                        mapStatus?.classList.remove('is-set');
                        if (mapStatusText) {
                            mapStatusText.textContent = automatic
                                ? 'Current location was unavailable. Click the map or use the location button to place the pin.'
                                : 'Location access was unavailable. Click the map to place the pin manually.';
                        }

                        if (useLocationButton) {
                            useLocationButton.disabled = false;
                            useLocationButton.style.opacity = '';
                        }
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 12000,
                        maximumAge: 30000,
                    }
                );
            }

            function initCheckoutMap() {
                if (!mapElement || !window.L || checkoutMap) return;

                const philippinesCenter = [12.8797, 121.7740];
                checkoutMap = window.L.map(mapElement, {
                    zoomControl: true,
                    scrollWheelZoom: false,
                }).setView(philippinesCenter, 6);

                window.L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                }).addTo(checkoutMap);

                checkoutMap.on('click', (event) => {
                    updatePin(event.latlng.lat, event.latlng.lng, {
                        center: true,
                        zoom: Math.max(checkoutMap.getZoom(), 16),
                        statusText: 'Delivery pin selected manually.',
                    });
                });

                const storedLat = Number(latitudeInput?.value);
                const storedLng = Number(longitudeInput?.value);

                if (Number.isFinite(storedLat) && Number.isFinite(storedLng) && latitudeInput?.value && longitudeInput?.value) {
                    updatePin(storedLat, storedLng, {
                        center: true,
                        zoom: 17,
                        statusText: 'Saved delivery pin restored.',
                    });
                } else if (mapStatusText) {
                    mapStatusText.textContent = 'Press “Use my current location” to zoom to your location, or click the map to place the red pin manually.';
                }

                window.setTimeout(() => checkoutMap?.invalidateSize(), 80);
            }

            useLocationButton?.addEventListener('click', () => {
                locateBuyer({ automatic: false });
            });

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCheckoutMap, { once: true });
            } else {
                initCheckoutMap();
            }
        })();
    </script>
@endpush
