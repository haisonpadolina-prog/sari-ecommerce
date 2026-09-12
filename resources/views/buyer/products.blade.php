@extends('layouts.buyer')

@section('title', 'Shop Products — SARI')
@section('page-title', 'Shop Products')

@push('styles')
<style>
    .sari-products-page,
    .sari-products-page button,
    .sari-products-page input,
    .sari-products-page select,
    .sari-products-page textarea {
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .sari-products-page {
        --sari-gold: #cd890b;
        --sari-gold-dark: #a96d08;
        --sari-cream: #fff8eb;
        --sari-soft: #fbfaf8;
        --sari-border: #e9e3db;
        --sari-text: #211c18;
        --sari-muted: #81786f;
        background: #fbfaf8;
        color: var(--sari-text);
    }

    .sari-products-main {
        width: 100%;
        max-width: 1640px;
        margin: 0 auto;
    }

    /* ============================================================
       PAGE INTRO
    ============================================================ */
    .sari-products-hero {
        padding: 6px 2px 18px;
        border-bottom: 1px solid #eee8e1;
    }

    .sari-editorial-note {
        max-width: 235px;
        padding-left: 18px;
        border-left: 2px solid #d2982e;
    }

    .sari-editorial-note strong {
        display: block;
        color: #50483f;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.35;
    }

    .sari-editorial-note p {
        margin-top: 7px;
        color: #999087;
        font-size: 7.5px;
        line-height: 1.55;
    }

    /* ============================================================
       ADMIN CAMPAIGN SPOTLIGHT
    ============================================================ */
    .sari-campaign-shell {
        position: relative;
        margin-top: 14px;
        overflow: hidden;
        border: 1px solid rgba(103, 28, 18, .20);
        border-radius: 17px;
        background: #731d14;
        box-shadow: 0 12px 28px rgba(79, 28, 19, .12);
    }

    .sari-campaign-slide {
        position: relative;
        min-height: 205px;
        overflow: hidden;
        isolation: isolate;
    }

    .sari-campaign-slide[hidden] {
        display: none !important;
    }

    .sari-campaign-slide[data-theme="green"] {
        background: #173f32;
    }

    .sari-campaign-slide[data-theme="charcoal"] {
        background: #292621;
    }

    .sari-campaign-slide[data-theme="rose"] {
        background: #753f48;
    }

    .sari-campaign-slide::before,
    .sari-campaign-slide::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        pointer-events: none;
        z-index: -1;
    }

    .sari-campaign-slide::before {
        width: 260px;
        height: 260px;
        right: -80px;
        top: -105px;
        border: 1px solid rgba(255, 219, 126, .18);
        box-shadow:
            0 0 0 22px rgba(255, 220, 129, .035),
            0 0 0 44px rgba(255, 220, 129, .022);
    }

    .sari-campaign-slide::after {
        width: 170px;
        height: 170px;
        left: 32%;
        bottom: -125px;
        border: 1px solid rgba(255, 255, 255, .08);
    }

    .sari-campaign-content {
        display: grid;
        min-height: 205px;
        grid-template-columns: 1.15fr .92fr .78fr;
        align-items: center;
        gap: 26px;
        padding: 24px 30px;
    }

    .sari-campaign-event {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        gap: 7px;
        border: 1px solid rgba(255, 224, 149, .35);
        border-radius: 999px;
        background: rgba(255, 255, 255, .09);
        padding: 0 10px;
        color: #ffe4a0;
        font-size: 6.8px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sari-campaign-title {
        margin-top: 10px;
        color: #fff4cf;
        font-size: clamp(34px, 4vw, 56px);
        font-weight: 800;
        line-height: .94;
        letter-spacing: -.055em;
    }

    .sari-campaign-subtitle {
        margin-top: 8px;
        color: #fff;
        font-size: 13px;
        font-weight: 750;
        letter-spacing: -.025em;
    }

    .sari-campaign-copy {
        margin-top: 6px;
        max-width: 430px;
        color: rgba(255,255,255,.72);
        font-size: 7.5px;
        line-height: 1.65;
    }

    .sari-campaign-offer {
        border-left: 1px solid rgba(255,255,255,.18);
        padding-left: 24px;
    }

    .sari-campaign-offer-label {
        color: rgba(255,255,255,.82);
        font-size: 8px;
        font-weight: 750;
        text-transform: uppercase;
    }

    .sari-campaign-discount {
        margin-top: 3px;
        color: #fff5d3;
        font-size: clamp(28px, 3vw, 45px);
        font-weight: 800;
        line-height: 1;
        letter-spacing: -.045em;
    }

    .sari-campaign-perks {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }

    .sari-campaign-perk {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        gap: 5px;
        border: 1px solid rgba(255, 224, 149, .25);
        border-radius: 999px;
        background: rgba(255,255,255,.07);
        padding: 0 9px;
        color: rgba(255,255,255,.90);
        font-size: 6.2px;
        font-weight: 650;
    }

    .sari-campaign-countdown {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 11px;
    }

    .sari-countdown-box {
        min-width: 44px;
        border: 1px solid rgba(255,255,255,.13);
        border-radius: 8px;
        background: rgba(0,0,0,.13);
        padding: 6px 7px;
        text-align: center;
    }

    .sari-countdown-box strong {
        display: block;
        color: #fff7df;
        font-size: 12px;
        font-weight: 800;
        line-height: 1;
    }

    .sari-countdown-box span {
        display: block;
        margin-top: 4px;
        color: rgba(255,255,255,.57);
        font-size: 5.5px;
        font-weight: 650;
        text-transform: uppercase;
    }

    .sari-campaign-visual {
        position: relative;
        min-height: 150px;
    }

    .sari-campaign-gift {
        position: absolute;
        border-radius: 8px;
        background: #9f281c;
        box-shadow: 0 12px 24px rgba(32,8,4,.22);
    }

    .sari-campaign-slide[data-theme="green"] .sari-campaign-gift {
        background: #275b48;
    }

    .sari-campaign-slide[data-theme="charcoal"] .sari-campaign-gift {
        background: #4b463f;
    }

    .sari-campaign-slide[data-theme="rose"] .sari-campaign-gift {
        background: #925260;
    }

    .sari-campaign-gift::before,
    .sari-campaign-gift::after {
        content: '';
        position: absolute;
        background: #e5b24f;
    }

    .sari-campaign-gift::before {
        left: 50%;
        top: 0;
        width: 12px;
        height: 100%;
        transform: translateX(-50%);
    }

    .sari-campaign-gift::after {
        left: 0;
        top: 35%;
        width: 100%;
        height: 11px;
    }

    .sari-campaign-gift--one {
        width: 110px;
        height: 90px;
        right: 58px;
        bottom: 8px;
        transform: rotate(-3deg);
    }

    .sari-campaign-gift--two {
        width: 74px;
        height: 65px;
        right: 3px;
        bottom: 24px;
        transform: rotate(6deg);
    }

    .sari-campaign-gift--three {
        width: 68px;
        height: 56px;
        right: 125px;
        bottom: 70px;
        transform: rotate(4deg);
        opacity: .92;
    }

    .sari-campaign-tag {
        position: absolute;
        right: 0;
        top: 6px;
        max-width: 125px;
        transform: rotate(7deg);
        border-radius: 8px;
        background: #f7e4bb;
        padding: 11px 13px;
        color: #4d3321;
        font-size: 8px;
        font-weight: 750;
        line-height: 1.35;
        text-align: center;
        box-shadow: 0 8px 20px rgba(32,8,4,.15);
    }

    .sari-campaign-cta {
        position: absolute;
        right: 18px;
        bottom: 16px;
        z-index: 10;
        display: inline-flex;
        height: 36px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 999px;
        background: #fff;
        padding: 0 17px;
        color: #9a4f15;
        font-size: 7.5px;
        font-weight: 800;
        box-shadow: 0 6px 16px rgba(31,9,4,.15);
        transition: transform .15s ease;
    }

    .sari-campaign-cta:hover {
        transform: translateY(-1px);
    }

    .sari-campaign-nav {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 20;
    }

    .sari-campaign-arrow {
        position: absolute;
        top: 50%;
        display: grid;
        width: 32px;
        height: 32px;
        transform: translateY(-50%);
        place-items: center;
        border-radius: 999px;
        background: rgba(255,255,255,.95);
        color: #6e6257;
        box-shadow: 0 4px 12px rgba(26,10,5,.14);
        pointer-events: auto;
    }

    .sari-campaign-arrow--prev { left: 10px; }
    .sari-campaign-arrow--next { right: 10px; }

    .sari-campaign-dots {
        position: absolute;
        left: 50%;
        bottom: 8px;
        display: flex;
        transform: translateX(-50%);
        gap: 5px;
        pointer-events: auto;
    }

    .sari-campaign-dot {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: rgba(255,255,255,.35);
    }

    .sari-campaign-dot.is-active {
        width: 16px;
        background: #f5d67f;
    }

    .sari-campaign-sparkle {
        position: absolute;
        width: 5px;
        height: 5px;
        border-radius: 1px;
        background: #f5ce65;
        transform: rotate(45deg);
        animation: sariCampaignSparkle 2.8s ease-in-out infinite;
        pointer-events: none;
    }

    .sari-campaign-sparkle:nth-child(1) { left: 17%; top: 20%; }
    .sari-campaign-sparkle:nth-child(2) { left: 47%; top: 13%; animation-delay: .6s; }
    .sari-campaign-sparkle:nth-child(3) { right: 22%; top: 25%; animation-delay: 1.2s; }
    .sari-campaign-sparkle:nth-child(4) { right: 35%; bottom: 20%; animation-delay: 1.7s; }

    @keyframes sariCampaignSparkle {
        0%, 100% { opacity: .25; transform: rotate(45deg) scale(.75); }
        50% { opacity: 1; transform: rotate(45deg) scale(1.15); }
    }

    /* ============================================================
       MODERN FILTER DOCK
    ============================================================ */
    .sari-filter-dock {
        position: relative;
        z-index: 45;
        margin-top: 14px;
        border: 1px solid #e7e1da;
        border-radius: 15px;
        background: #fff;
        padding: 11px 13px;
        box-shadow: 0 9px 24px rgba(43,34,24,.042);
    }

    .sari-filter-grid {
        display: grid;
        grid-template-columns:
            minmax(280px, 1.45fr)
            minmax(145px, .72fr)
            minmax(145px, .72fr)
            minmax(145px, .72fr)
            minmax(145px, .70fr)
            auto
            auto;
        align-items: end;
        gap: 10px;
    }

    .sari-filter-label {
        display: block;
        margin-bottom: 5px;
        color: #665d54;
        font-size: 6.4px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sari-filter-control {
        height: 40px;
        width: 100%;
        border: 1px solid #ddd7d0;
        border-radius: 9px;
        background: #fff;
        color: #50483f;
        font-size: 8px;
        font-weight: 550;
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .sari-filter-control:focus {
        border-color: #ca8b17;
        box-shadow: 0 0 0 3px rgba(202,139,23,.08);
    }

    .sari-products-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23685f55' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 11px center;
        padding: 0 33px 0 11px;
    }

    .sari-live-search {
        position: relative;
        z-index: 70;
    }

    .sari-live-search > svg {
        position: absolute;
        left: 12px;
        bottom: 12px;
        width: 15px;
        height: 15px;
        color: #9a9188;
        pointer-events: none;
    }

    .sari-live-search input {
        padding: 0 38px 0 37px;
    }

    .sari-live-search-clear {
        position: absolute;
        right: 7px;
        bottom: 7px;
        display: none;
        width: 26px;
        height: 26px;
        place-items: center;
        border-radius: 7px;
        color: #8c8278;
    }

    .sari-live-search-clear.is-visible {
        display: grid;
    }

    .sari-live-search-panel {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 8px);
        z-index: 100;
        overflow: hidden;
        border: 1px solid #e4ded7;
        border-radius: 13px;
        background: #fff;
        box-shadow:
            0 20px 48px rgba(39,31,23,.14),
            0 4px 12px rgba(39,31,23,.04);
    }

    .sari-live-search-head {
        display: flex;
        min-height: 37px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border-bottom: 1px solid #f0ebe5;
        background: #fffdfa;
        padding: 0 11px;
    }

    .sari-live-search-head strong {
        color: #514940;
        font-size: 6.8px;
        font-weight: 800;
        letter-spacing: .07em;
        text-transform: uppercase;
    }

    .sari-live-search-head span {
        color: #9b9288;
        font-size: 6.3px;
        font-weight: 600;
    }

    .sari-live-search-list {
        max-height: 320px;
        overflow-y: auto;
        padding: 5px;
        scrollbar-width: thin;
        scrollbar-color: #ddd7d0 transparent;
    }

    .sari-live-result {
        display: flex;
        width: 100%;
        min-width: 0;
        align-items: center;
        gap: 9px;
        border-radius: 9px;
        padding: 7px;
        text-align: left;
        transition: background-color .14s ease;
    }

    .sari-live-result:hover,
    .sari-live-result.is-active {
        background: #fff8eb;
    }

    .sari-live-result-image {
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 auto;
        place-items: center;
        overflow: hidden;
        border: 1px solid #e7e1d9;
        border-radius: 8px;
        background: #f6f3ee;
        color: #aaa198;
    }

    .sari-live-result-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .sari-live-result-copy {
        min-width: 0;
        flex: 1;
    }

    .sari-live-result-name {
        display: block;
        overflow: hidden;
        color: #302a24;
        font-size: 8.2px;
        font-weight: 750;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sari-live-result-meta {
        display: flex;
        min-width: 0;
        flex-wrap: wrap;
        align-items: center;
        gap: 5px;
        margin-top: 3px;
        color: #948b82;
        font-size: 6.2px;
    }

    .sari-live-result-stock {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #4f8065;
    }

    .sari-live-result-stock::before {
        content: '';
        width: 4px;
        height: 4px;
        border-radius: 999px;
        background: #4f8065;
    }

    .sari-live-result-stock.is-out {
        color: #bd5555;
    }

    .sari-live-result-stock.is-out::before {
        background: #bd5555;
    }

    .sari-live-result-price {
        flex: 0 0 auto;
        color: #bf7b08;
        font-size: 7.8px;
        font-weight: 800;
        white-space: nowrap;
    }

    .sari-live-search-empty {
        padding: 20px 14px;
        text-align: center;
    }

    .sari-live-search-empty strong {
        display: block;
        color: #5f574f;
        font-size: 8px;
        font-weight: 750;
    }

    .sari-live-search-empty p {
        margin-top: 4px;
        color: #9b9288;
        font-size: 6.5px;
        line-height: 1.5;
    }

    .sari-live-search-footer {
        display: flex;
        min-height: 39px;
        width: 100%;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border-top: 1px solid #eee9e3;
        background: #fffdfa;
        padding: 0 11px;
        color: #996414;
        font-size: 6.8px;
        font-weight: 750;
    }

    .sari-price-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 0 11px;
        cursor: pointer;
    }

    .sari-price-popover {
        position: absolute;
        left: 0;
        top: calc(100% + 8px);
        z-index: 85;
        width: 280px;
        border: 1px solid #e4ded7;
        border-radius: 12px;
        background: #fff;
        padding: 13px;
        box-shadow: 0 18px 40px rgba(39,31,23,.12);
    }

    .sari-price-field {
        position: relative;
    }

    .sari-price-field > span {
        position: absolute;
        left: 9px;
        top: 50%;
        transform: translateY(-50%);
        color: #9b9084;
        font-size: 7px;
        font-weight: 700;
        pointer-events: none;
    }

    .sari-price-field input {
        height: 38px;
        width: 100%;
        border: 1px solid #ded8d1;
        border-radius: 8px;
        background: #fff;
        padding: 0 8px 0 22px;
        color: #4b443d;
        font-size: 8px;
        outline: none;
    }

    .sari-filter-reset {
        display: inline-flex;
        height: 40px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 9px;
        padding: 0 10px;
        color: #ad700d;
        font-size: 7.5px;
        font-weight: 750;
        white-space: nowrap;
    }

    .sari-filter-reset:hover {
        background: #fff8ea;
    }

    .sari-result-pill {
        display: inline-flex;
        height: 40px;
        min-width: 100px;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: #fff3dc;
        padding: 0 15px;
        color: #a66b0b;
        font-size: 8px;
        font-weight: 800;
        white-space: nowrap;
    }

    .sari-active-filters {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
    }

    #activeCategoryBadge {
        display: inline-flex;
        min-height: 26px;
        align-items: center;
        border: 1px solid #e7d7b7;
        border-radius: 999px;
        background: #fff8e9;
        padding: 0 9px;
        color: #986312;
        font-size: 6.5px;
        font-weight: 700;
    }

    #activeCategoryBadge.hidden {
        display: none !important;
    }

    #variationFilters,
    #mobileVariationFilters {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    #variationFilters button,
    #mobileVariationFilters button {
        min-height: 26px !important;
        border: 1px solid #e2dcd5 !important;
        border-radius: 999px !important;
        background: #fff !important;
        padding: 0 9px !important;
        color: #70675e !important;
        font-size: 6.3px !important;
        font-weight: 650 !important;
        box-shadow: none !important;
    }

    #variationFilters button.is-active,
    #mobileVariationFilters button.is-active {
        border-color: #cf8b0e !important;
        background: #fff5df !important;
        color: #986312 !important;
    }

    /* ============================================================
       RECOMMENDED / PRODUCT GRID
    ============================================================ */
    .sari-recommended-head {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-top: 23px;
    }

    .sari-recommended-title {
        font-size: 23px;
        font-weight: 780;
        line-height: 1.08;
        letter-spacing: -.045em;
        color: #201b17;
    }

    .sari-recommended-title span {
        color: #c3830d;
    }

    .sari-recommended-copy {
        margin-top: 6px;
        color: #81786f;
        font-size: 8.5px;
    }

    .sari-category-chips {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 7px;
    }

    .sari-inline-chip {
        display: inline-flex;
        min-height: 33px;
        align-items: center;
        justify-content: center;
        border: 1px solid #ddd6ce;
        border-radius: 999px;
        background: #fff;
        padding: 0 15px;
        color: #413a33;
        font-size: 7px;
        font-weight: 650;
    }

    .sari-inline-chip:hover {
        border-color: #d0ba8a;
        background: #fff9ef;
        color: #9a6513;
    }

    .sari-inline-chip.is-active {
        border-color: #cf8b0e;
        background: #cf8b0e;
        color: #fff;
        box-shadow: 0 7px 15px rgba(201,135,14,.16);
    }

    .sari-view-all {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-left: 3px;
        color: #b5740c;
        font-size: 8px;
        font-weight: 750;
    }

    .sari-showcase-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
        margin-top: 14px;
    }

    .sari-showcase-grid > .sari-featured-product-card {
        grid-row: auto;
    }

    /* ============================================================
       BUYER PROTECTION STRIP
    ============================================================ */
    .sari-benefits-wrap {
        margin-top: 17px;
        overflow: hidden;
        border: 1px solid #e8e1d8;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 7px 18px rgba(43,34,24,.025);
    }

    .sari-benefits-row {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
    }

    .sari-benefit-tile {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px;
        padding: 13px 15px;
        border-right: 1px solid #eee8e1;
    }

    .sari-benefit-tile:last-child {
        border-right: 0;
    }

    .sari-benefit-icon {
        display: grid;
        width: 35px;
        height: 35px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 999px;
        background: #fff5df;
        color: #bd7b0f;
    }

    .sari-benefit-tile h3 {
        color: #36302a;
        font-size: 7.8px;
        font-weight: 750;
    }

    .sari-benefit-tile p {
        margin-top: 2px;
        color: #968d84;
        font-size: 6.3px;
        line-height: 1.4;
    }

    @media (min-width: 768px) {
        .sari-recommended-head {
            flex-direction: row;
            align-items: end;
            justify-content: space-between;
        }
    }

    @media (min-width: 1280px) {
        .sari-showcase-grid {
            grid-template-columns: minmax(0, 1.65fr) repeat(3, minmax(0, 1fr));
            grid-auto-flow: row dense;
        }

        .sari-showcase-grid > .sari-featured-product-card {
            grid-row: span 2;
        }
    }

    @media (max-width: 1399px) {
        .sari-filter-grid {
            grid-template-columns:
                minmax(240px, 1.25fr)
                minmax(140px, .7fr)
                minmax(140px, .7fr)
                minmax(140px, .7fr)
                minmax(140px, .68fr);
        }
    }

    @media (max-width: 1279px) {
        .sari-campaign-content {
            grid-template-columns: 1fr 1fr;
        }

        .sari-campaign-visual {
            display: none;
        }

        .sari-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sari-benefits-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sari-benefit-tile {
            border-right: 0;
            border-bottom: 1px solid #eee8e1;
        }
    }

    @media (max-width: 767px) {
        .sari-editorial-note,
        .sari-campaign-shell,
        .sari-filter-dock {
            display: none;
        }

        .sari-products-main {
            padding-inline: 0;
        }

        .sari-benefits-row {
            grid-template-columns: 1fr;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-campaign-sparkle {
            animation: none !important;
        }

        .sari-campaign-cta {
            transition: none !important;
            transform: none !important;
        }
    }

    /* ============================================================
       ACCURATE 12.12 PLACEHOLDER + REFERENCE LAYOUT OVERRIDES
    ============================================================ */
    .sari-products-main {
        max-width: 1540px;
    }

    .sari-products-hero {
        padding-bottom: 16px;
    }

    .sari-accurate-sale-banner {
        position: relative;
        margin-top: 10px;
        overflow: hidden;
        border: 1px solid #8f2d22;
        border-radius: 16px;
        background:
            radial-gradient(circle at 50% 0%, rgba(255, 203, 77, .10), transparent 42%),
            #8b1b12;
        box-shadow:
            0 10px 24px rgba(91, 30, 19, .10),
            inset 0 1px 0 rgba(255,255,255,.10);
    }

    .sari-accurate-sale-banner > a {
        display: block;
        width: 100%;
    }

    .sari-accurate-sale-banner img {
        display: block;
        width: 100%;
        height: auto;
        object-fit: fill;
        object-position: center;
    }

    .sari-filter-dock {
        margin-top: 14px;
        padding: 10px 12px;
        border-radius: 14px;
        box-shadow: 0 7px 20px rgba(43,34,24,.035);
    }

    .sari-filter-grid {
        grid-template-columns:
            minmax(300px, 1.55fr)
            minmax(150px, .74fr)
            minmax(150px, .74fr)
            minmax(150px, .74fr)
            minmax(150px, .72fr)
            auto
            auto;
        gap: 9px;
    }

    .sari-filter-control {
        height: 38px;
        border-radius: 9px;
        font-size: 7.7px;
    }

    .sari-result-pill {
        height: 38px;
        min-width: 102px;
    }

    .sari-filter-reset {
        height: 38px;
    }

    .sari-reference-category-row {
        display: flex;
        justify-content: flex-end;
        margin-top: 11px;
    }

    .sari-reference-category-row .sari-category-chips {
        gap: 7px;
    }

    .sari-inline-chip {
        min-height: 31px;
        padding: 0 15px;
        font-size: 6.8px;
    }

    .sari-showcase-grid {
        gap: 12px;
        margin-top: 11px;
    }

    .sari-benefits-wrap {
        margin-top: 13px;
    }

    .sari-benefit-tile {
        padding: 12px 14px;
    }

    @media (min-width: 1280px) {
        .sari-showcase-grid {
            grid-template-columns: minmax(0, 1.62fr) repeat(3, minmax(0, 1fr));
            grid-auto-flow: row dense;
        }

        .sari-showcase-grid > .sari-featured-product-card {
            grid-row: span 2;
        }
    }

    

    @media (max-width: 767px) {
        .sari-accurate-sale-banner {
            margin-top: 10px;
            border-radius: 13px;
        }

        .sari-reference-category-row {
            justify-content: flex-start;
            overflow-x: auto;
            padding-bottom: 3px;
        }

        .sari-reference-category-row .sari-category-chips {
            min-width: max-content;
            flex-wrap: nowrap;
        }
    }


    /* ============================================================
       FINAL SHOP PRODUCTS REFERENCE MATCH
       Banner is the picture itself — no colored wrapper behind it.
    ============================================================ */
    .sari-products-page {
        background: #fffdfa;
    }

    .sari-products-main {
        max-width: 1500px;
    }

    .sari-products-hero {
        padding-top: 4px;
        padding-bottom: 14px;
    }

    .sari-accurate-sale-banner {
        position: relative;
        margin-top: 12px;
        overflow: visible;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .sari-accurate-sale-banner > a {
        display: block;
        width: 100%;
        line-height: 0;
    }

    .sari-accurate-sale-banner img {
        display: block;
        width: 100%;
        height: auto;
        margin: 0;
        border: 0;
        border-radius: 15px;
        object-fit: contain;
        object-position: center;
        background: transparent;
        box-shadow: 0 8px 22px rgba(89,35,17,.08);
    }

    .sari-filter-dock {
        margin-top: 15px;
        border: 1px solid #e7e1d9;
        border-radius: 13px;
        background: rgba(255,255,255,.98);
        padding: 10px 12px;
        box-shadow: 0 7px 20px rgba(43,34,24,.035);
    }

    .sari-filter-grid {
        grid-template-columns:
            minmax(300px, 1.6fr)
            minmax(145px, .76fr)
            minmax(145px, .76fr)
            minmax(145px, .76fr)
            minmax(145px, .74fr)
            auto
            auto;
        gap: 9px;
    }

    .sari-filter-label {
        margin-bottom: 5px;
        font-size: 6.2px;
        letter-spacing: .07em;
    }

    .sari-filter-control {
        height: 39px;
        border-radius: 9px;
        background: #fff;
        font-size: 7.8px;
    }

    .sari-filter-reset,
    .sari-result-pill {
        height: 39px;
    }

    .sari-result-pill {
        min-width: 96px;
        border: 1px solid #f0dfbd;
        background: #fff4df;
    }

    .sari-reference-category-row {
        margin-top: 11px;
        justify-content: flex-end;
    }

    .sari-inline-chip {
        min-height: 31px;
        border-radius: 999px;
        padding: 0 14px;
        font-size: 6.7px;
    }

    .sari-showcase-grid {
        margin-top: 11px;
        gap: 12px;
    }

    .sari-benefits-wrap {
        margin-top: 14px;
        border-radius: 13px;
    }

    .sari-live-search-panel {
        border-radius: 12px;
        box-shadow:
            0 18px 42px rgba(39,31,23,.13),
            0 4px 12px rgba(39,31,23,.035);
    }

    @media (min-width: 1280px) {
        .sari-showcase-grid {
            grid-template-columns: minmax(0, 1.58fr) repeat(3, minmax(0, 1fr));
            grid-auto-flow: row dense;
        }

        .sari-showcase-grid > .sari-featured-product-card {
            grid-row: span 2;
        }
    }

    @media (max-width: 1279px) {
        .sari-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .sari-accurate-sale-banner {
            margin-top: 8px;
        }

        .sari-accurate-sale-banner img {
            width: 100%;
            border-radius: 11px;
            box-shadow: 0 5px 14px rgba(89,35,17,.07);
        }
    }


    /* ============================================================
       BANNER = IMAGE ONLY
       No card, no white/red outer container, no padding.
    ============================================================ */
    .sari-sale-banner-image {
        display: block;
        width: 100%;
        margin-top: 12px;
        overflow: hidden;
        aspect-ratio: 5.35 / 1;
        border: 0;
        border-radius: 15px;
        background: transparent;
        box-shadow: none;
        line-height: 0;
    }

    .sari-sale-banner-image img {
        display: block;
        width: 100%;
        height: 100%;
        margin: 0;
        border: 0;
        object-fit: cover;
        object-position: center 54%;
        background: transparent;
        box-shadow: none;
    }

    /* Neutralize all previous temporary banner wrapper rules. */
    .sari-accurate-sale-banner {
        display: none !important;
    }

    @media (max-width: 767px) {
        .sari-sale-banner-image {
            margin-top: 8px;
            aspect-ratio: 4.35 / 1;
            border-radius: 11px;
        }
    }


    /* ============================================================
       FINAL COMPACT SPACING PASS
       Tightens banner → filter → category chips → products.
    ============================================================ */
    .sari-products-hero {
        padding-top: 2px !important;
        padding-bottom: 10px !important;
    }

    .sari-sale-banner-image {
        margin-top: 8px !important;
    }

    .sari-filter-dock {
        margin-top: 10px !important;
        padding-top: 9px !important;
        padding-bottom: 9px !important;
    }

    .sari-active-filters {
        margin-top: 6px !important;
    }

    .sari-reference-category-row {
        margin-top: 7px !important;
    }

    .sari-showcase-grid {
        margin-top: 8px !important;
    }

    .sari-benefits-wrap {
        margin-top: 10px !important;
    }

    /* Keep product area visually compact without touching functionality. */
    #buyerProductGrid {
        scroll-margin-top: 90px;
    }

    @media (max-width: 767px) {
        .sari-products-hero {
            padding-bottom: 8px !important;
        }

        .sari-sale-banner-image {
            margin-top: 6px !important;
        }

        .sari-reference-category-row {
            margin-top: 6px !important;
        }

        .sari-showcase-grid {
            margin-top: 7px !important;
        }
    }

</style>
@endpush

@section('content')

@include('components.buyer.header')

@php
    /*
    |--------------------------------------------------------------------------
    | MODERN LIVE SEARCH INDEX
    |--------------------------------------------------------------------------
    | Uses the same approved products already passed to this page.
    | No additional database query is added here.
    */
    $buyerSearchImageUrl = function ($path) {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    };

    $buyerSearchIndex = collect($products ?? [])
        ->map(function ($product) use ($buyerSearchImageUrl) {
            $id = (int) ($product['id'] ?? 0);

            return [
                'id' => $id,
                'name' => trim((string) ($product['name'] ?? 'Product')),
                'category' => trim((string) ($product['category'] ?? 'General')),
                'brand' => trim((string) ($product['brand'] ?? '')),
                'price' => (float) ($product['price'] ?? 0),
                'old_price' => isset($product['old_price']) && $product['old_price'] !== null
                    ? (float) $product['old_price']
                    : null,
                'stock' => max(0, (int) ($product['stock'] ?? 0)),
                'image' => $buyerSearchImageUrl($product['image'] ?? null),
                'url' => $id > 0
                    ? route('buyer.product.details', ['product' => $id])
                    : route('buyer.products'),
            ];
        })
        ->filter(fn ($product) => $product['id'] > 0)
        ->values();


    /*
    |--------------------------------------------------------------------------
    | CAMPAIGN SPOTLIGHT
    |--------------------------------------------------------------------------
    | Controller-ready:
    |   $activeCampaigns or $campaigns
    |
    | Campaigns render only when real campaign data is supplied by the controller.
    */
    $buyerCampaigns = collect($activeCampaigns ?? $campaigns ?? [])
        ->filter(fn ($campaign) => is_array($campaign) || is_object($campaign))
        ->map(function ($campaign) {
            $campaign = (array) $campaign;

            return [
                'id' => $campaign['id'] ?? uniqid('campaign-', true),
                'badge' => trim((string) ($campaign['badge'] ?? 'SARI Event')),
                'title' => trim((string) ($campaign['title'] ?? $campaign['campaign_name'] ?? 'Marketplace Event')),
                'subtitle' => trim((string) ($campaign['subtitle'] ?? $campaign['headline'] ?? 'Limited-time offers')),
                'discount_text' => trim((string) ($campaign['discount_text'] ?? $campaign['offer'] ?? 'Special Deals')),
                'description' => trim((string) ($campaign['description'] ?? 'Discover limited-time offers from participating SARI sellers.')),
                'free_shipping' => (bool) ($campaign['free_shipping'] ?? true),
                'voucher_text' => trim((string) ($campaign['voucher_text'] ?? 'Exclusive Vouchers')),
                'extra_perk' => trim((string) ($campaign['extra_perk'] ?? 'Limited Time Only')),
                'starts_at' => $campaign['starts_at'] ?? $campaign['start_date'] ?? null,
                'ends_at' => $campaign['ends_at'] ?? $campaign['end_date'] ?? null,
                'cta_text' => trim((string) ($campaign['cta_text'] ?? 'Shop Sale')),
                'cta_url' => trim((string) ($campaign['cta_url'] ?? route('buyer.products', ['discounted' => 1]))),
                'theme' => in_array(($campaign['theme'] ?? 'red'), ['red', 'green', 'charcoal', 'rose'], true)
                    ? $campaign['theme']
                    : 'red',
                'tagline' => trim((string) ($campaign['tagline'] ?? 'Shop Local. Shop Better.')),
            ];
        })
        ->values();

    $buyerCampaigns = $buyerCampaigns
        ->filter(function ($campaign) {
            try {
                if (!empty($campaign['starts_at']) && now()->lt(\Illuminate\Support\Carbon::parse($campaign['starts_at']))) {
                    return false;
                }

                if (!empty($campaign['ends_at']) && now()->gte(\Illuminate\Support\Carbon::parse($campaign['ends_at']))) {
                    return false;
                }
            } catch (\Throwable $error) {
                return true;
            }

            return true;
        })
        ->values();

@endphp

<div class="sari-products-page bg-[#fffdf9]">
<main class="sari-products-main px-4 pb-9 pt-5 sm:px-6 sm:pt-6 lg:px-8 xl:px-10">

    {{-- =========================================================
         PAGE INTRO
    ========================================================== --}}
    <section class="sari-products-hero">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="min-w-0">
                <p class="text-[7px] font-bold uppercase tracking-[.18em] text-[#a87316]">SARI Marketplace</p>

                <h1 class="mt-2 text-[32px] font-bold leading-[1.02] tracking-[-.055em] text-[#17130f] sm:text-[40px] lg:text-[47px]">
                    Shop <span class="text-[#c2810d]">Products</span>
                </h1>

                <p class="mt-2 max-w-[720px] text-[9px] leading-5 text-[#746c63] sm:text-[10px]">
                    Discover approved products from trusted SARI sellers. Search, compare, and find the right item for you.
                </p>
            </div>

            <div class="sari-editorial-note hidden lg:block">
                <strong>Quality Products<br>Meaningful Choices</strong>
                <p>Support local. Shop trusted.</p>
            </div>
        </div>
    </section>

    {{-- =========================================================
         TEMPORARY 12.12 SALE BANNER
         Uses the generated asset for an accurate visual match.
         Dynamic admin campaign data above is preserved for later.
    ========================================================== --}}
    <a
        href="{{ route('buyer.products', ['discounted' => 1]) }}"
        class="sari-sale-banner-image"
        aria-label="Shop the SARI 12.12 Mega Sale"
    >
        <img
            src="{{ asset('images/12-12-mega-sale-banner.png') }}"
            alt="SARI 12.12 Mega Sale — Up to 50% off selected items"
            width="2159"
            height="470"
            loading="eager"
            decoding="async"
        >
    </a>

    {{-- =========================================================
         SEARCH RESULT NOTICE
    ========================================================== --}}
    @if (request('search'))
        <section class="mb-4 rounded-[16px] border border-[#eadfc9] bg-[#fffaf1] p-4 sm:p-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-3">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-[#a8731f]">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[8px] font-semibold uppercase tracking-[0.12em] text-[#a8731f]">Search Results</p>
                        <p class="mt-1 text-[12px] font-semibold text-[#3f3830]">Results for "{{ request('search') }}"</p>
                        <p class="mt-1 text-[8px] leading-4 text-[#8f8477]">Matching products from across the SARI marketplace.</p>
                    </div>
                </div>
                <a href="{{ route('buyer.products') }}" class="inline-flex h-9 items-center justify-center rounded-[9px] border border-[#dfd5c7] bg-white px-4 text-[8px] font-semibold text-[#62594e] transition hover:border-[#d4bc8e] hover:bg-[#fffaf2]">
                    Clear Search
                </a>
            </div>
        </section>
    @endif

    {{-- =========================================================
         MOBILE FILTER BAR
    ========================================================== --}}
    <section class="mb-4 lg:hidden">
        <div class="rounded-[16px] border border-[#ebe4da] bg-white p-4 shadow-[0_8px_24px_rgba(69,50,25,.035)]">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[8px] font-semibold uppercase tracking-[0.12em] text-[#a8731f]">Browse</p>
                    <p id="mobileFilterSummary" class="mt-1 text-[10px] font-semibold text-[#403930]">All Products</p>
                </div>
                <button id="openMobileFilters" type="button" class="inline-flex h-10 items-center justify-center gap-2 rounded-[9px] bg-[#c9870e] px-4 text-[9px] font-semibold text-white transition hover:bg-[#ae7008]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 6h16"></path><path d="M7 12h10"></path><path d="M10 18h4"></path>
                    </svg>
                    Filters
                    <span id="mobileFilterCount" class="hidden min-w-[18px] rounded-full bg-white/20 px-1.5 py-0.5 text-[7px] text-white">0</span>
                </button>
            </div>
        </div>
    </section>

    {{-- =========================================================
         RELATED SHOP RESULTS
    ========================================================== --}}
    @if (!empty($shopResults))
        <section class="mb-5 overflow-hidden rounded-[18px] border border-[#ebe4da] bg-white shadow-[0_8px_24px_rgba(69,50,25,.035)]">
            <div class="border-b border-[#eee8df] p-5 sm:p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-[8px] font-semibold uppercase tracking-[0.12em] text-[#a8731f]">Shops Related To</p>
                        <h2 class="mt-1 text-[15px] font-semibold text-[#28221b]">"{{ request('search') }}"</h2>
                    </div>
                    <span class="hidden text-[8px] text-[#958c80] sm:block">{{ count($shopResults) }} matching {{ count($shopResults) === 1 ? 'shop' : 'shops' }}</span>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-3 p-5 sm:p-6">
                @foreach ($shopResults as $shopResult)
                    <a href="{{ route('buyer.shop', ['shop' => $shopResult['slug']]) }}" class="group rounded-[14px] border border-[#e9e1d6] bg-[#fcfbf8] p-4 transition hover:-translate-y-0.5 hover:border-[#d8c7a7] hover:bg-white hover:shadow-[0_12px_28px_rgba(68,52,28,0.07)] sm:p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex items-center gap-4">
                                <div class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-[14px] border border-[#e5dac7] bg-white">
                                    <img src="{{ asset($shopResult['logo']) }}" alt="{{ $shopResult['name'] }}" loading="eager" decoding="async" class="h-full w-full object-contain p-2">
                                </div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-[13px] font-semibold text-[#312b25]">{{ $shopResult['name'] }}</p>
                                        <span class="rounded-md bg-[#c9870e] px-2 py-1 text-[6px] font-semibold text-white">{{ $shopResult['badge'] }}</span>
                                    </div>
                                    <p class="mt-1 text-[8px] text-[#857b70]">{{ $shopResult['username'] }}</p>
                                    <p class="mt-2 text-[8px] text-[#91887d]">{{ $shopResult['products_count'] }} approved products</p>
                                </div>
                            </div>
                            <div class="hidden h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#fbf5e9] text-[#a8731f] transition group-hover:translate-x-1 lg:grid">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- =========================================================
         MODERN FILTER DOCK + PRODUCT SHOWCASE
    ========================================================== --}}
    <section>
        <input type="radio" name="ratingFilter" value="" checked class="hidden">

        {{-- Desktop filter dock --}}
        <div class="sari-filter-dock hidden md:block">
            <div class="sari-filter-grid">
                <div id="buyerLiveSearchWrap" class="sari-live-search min-w-0">
                    <label for="buyerProductSearch" class="sari-filter-label">Search products</label>

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-4-4"></path>
                    </svg>

                    <input
                        id="buyerProductSearch"
                        type="search"
                        value="{{ request('search') }}"
                        placeholder="Search products, brands, or category..."
                        autocomplete="off"
                        role="combobox"
                        aria-autocomplete="list"
                        aria-expanded="false"
                        aria-controls="buyerLiveSearchPanel"
                        class="sari-filter-control"
                    >

                    <button id="buyerLiveSearchClear" type="button" class="sari-live-search-clear" aria-label="Clear search">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m7 7 10 10"></path><path d="m17 7-10 10"></path>
                        </svg>
                    </button>

                    <div id="buyerLiveSearchPanel" class="sari-live-search-panel hidden" role="listbox" aria-label="Product suggestions">
                        <div class="sari-live-search-head">
                            <strong>Popular products</strong>
                            <span id="buyerLiveSearchCount">0 matches</span>
                        </div>

                        <div id="buyerLiveSearchList" class="sari-live-search-list"></div>

                        <button id="buyerLiveSearchViewAll" type="button" class="sari-live-search-footer">
                            <span id="buyerLiveSearchViewAllText">View matching products</span>
                            <span>→</span>
                        </button>
                    </div>
                </div>

                <div class="min-w-0">
                    <label for="buyerCategorySelect" class="sari-filter-label">Category</label>
                    <select id="buyerCategorySelect" class="sari-filter-control sari-products-select">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category['name'] }}">{{ $category['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative min-w-0">
                    <span class="sari-filter-label">Price Range</span>
                    <button id="buyerPriceTrigger" type="button" class="sari-filter-control sari-price-trigger" aria-expanded="false">
                        <span id="buyerPriceTriggerText">Any price</span>
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </button>

                    <div id="buyerPricePopover" class="sari-price-popover hidden">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[8px] font-bold text-[#443c35]">Price Range</p>
                                <p class="mt-1 text-[6.3px] text-[#9b9289]">Set the minimum and maximum price.</p>
                            </div>
                            <button id="closeBuyerPricePopover" type="button" class="grid h-7 w-7 place-items-center rounded-lg text-[#8c8278] hover:bg-[#faf7f2]">×</button>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <div class="sari-price-field">
                                <span>₱</span>
                                <input id="minPrice" type="number" min="0" placeholder="Min">
                            </div>
                            <div class="sari-price-field">
                                <span>₱</span>
                                <input id="maxPrice" type="number" min="0" placeholder="Max">
                            </div>
                        </div>

                        <button id="applyPriceFilter" type="button" class="mt-3 h-9 w-full rounded-[9px] bg-[#cd890b] text-[7.5px] font-bold text-white transition hover:bg-[#ad7008]">
                            Apply Price
                        </button>
                    </div>
                </div>

                <div class="min-w-0">
                    <label for="buyerAvailabilitySelect" class="sari-filter-label">Availability</label>
                    <select id="buyerAvailabilitySelect" class="sari-filter-control sari-products-select">
                        <option value="">All Products</option>
                        <option value="in-stock">In Stock Only</option>
                    </select>
                    <input id="inStockFilter" type="checkbox" class="hidden">
                </div>

                <div class="min-w-0">
                    <label for="buyerSort" class="sari-filter-label">Sort By</label>
                    <select id="buyerSort" class="sari-filter-control sari-products-select">
                        <option value="featured">Featured</option>
                        <option value="latest">Newest</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="rating">Highest Rated</option>
                        <option value="sold">Most Sold</option>
                    </select>
                </div>

                <button id="clearAllFilters" type="button" class="sari-filter-reset">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M4 12a8 8 0 1 0 2.3-5.7"></path><path d="M4 4v5h5"></path>
                    </svg>
                    Reset
                </button>

                <span class="sari-result-pill">
                    <span id="toolbarResultCount">{{ count($products) }}</span>&nbsp;products
                </span>
            </div>

            <div class="sari-active-filters">
                <span id="activeCategoryBadge" class="hidden"></span>
                <div id="variationFilters"></div>
            </div>
        </div>

        {{-- Hidden buttons keep buyer-products.js category behavior intact. --}}
        <div class="hidden" aria-hidden="true">
            <button type="button" data-filter-category=""></button>
            @foreach ($categories as $category)
                <button type="button" data-filter-category="{{ $category['name'] }}"></button>
            @endforeach
        </div>

        {{-- Category chips — reference layout --}}
        <div class="sari-reference-category-row">
            <div class="sari-category-chips">
                <button type="button" data-shop-category-chip="" class="sari-inline-chip is-active">All</button>

                @foreach (collect($categories)->take(5) as $category)
                    <button type="button" data-shop-category-chip="{{ $category['name'] }}" class="sari-inline-chip">
                        {{ $category['name'] }}
                    </button>
                @endforeach

                <button id="showAllProducts" type="button" class="sari-view-all">
                    View all
                    <span>→</span>
                </button>
            </div>
        </div>

        {{-- Product grid --}}
        <div id="buyerProductGrid" class="sari-showcase-grid">
            @foreach ($products as $product)
                <x-buyer.product-card
                    :product="$product"
                    :priority="$loop->index < 6"
                    :featured="$loop->first"
                    :interactive="true"
                    :show-promotions="true"
                    :show-stock="true"
                    :show-quick-add="true"
                />
            @endforeach
        </div>

        <div id="buyerProductEmpty" class="mt-5 hidden rounded-[16px] border border-[#ebe4da] bg-white px-6 py-14 text-center">
            <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#fbf5e9] text-[#a8731f]">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path>
                </svg>
            </div>
            <p class="mt-4 text-[11px] font-semibold text-[#4f473e]">No products match your filters</p>
            <p class="mt-1 text-[9px] text-[#958c80]">Try another category, price range, availability, or product option.</p>
            <button id="clearProductFilters" type="button" class="mt-4 inline-flex h-10 items-center justify-center rounded-[9px] bg-[#c9870e] px-4 text-[9px] font-semibold text-white transition hover:bg-[#ae7008]">
                Clear Filters
            </button>
        </div>

        <div class="sr-only">
            Showing <span id="buyerResultCount">{{ count($products) }}</span> products
            <button id="showAllProductsBottom" type="button">Reset Filters</button>
        </div>

        {{-- Buyer protection --}}
        <div class="sari-benefits-wrap">
            <div class="sari-benefits-row">
                @php
                    $buyerBenefits = [
                        ['free', 'Free Shipping', 'On eligible orders'],
                        ['secure', 'Secure Payments', 'Safe and encrypted'],
                        ['returns', 'Easy Returns', 'Hassle-free returns'],
                        ['verified', 'Verified Sellers', 'Trusted SARI partners'],
                    ];
                @endphp

                @foreach ($buyerBenefits as [$type, $title, $copy])
                    <div class="sari-benefit-tile">
                        <span class="sari-benefit-icon">
                            @if ($type === 'free')
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M3 7h11v9H3z"></path><path d="M14 10h4l3 3v3h-7z"></path>
                                </svg>
                            @elseif ($type === 'secure')
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M12 3 5 6v5c0 4.5 2.8 8.1 7 10 4.2-1.9 7-5.5 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-4"></path>
                                </svg>
                            @elseif ($type === 'returns')
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 12a8 8 0 1 0 2.3-5.7"></path><path d="M4 4v5h5"></path>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 7h16v12H4z"></path><path d="M8 7V5h8v2"></path><path d="M8 11h8"></path>
                                </svg>
                            @endif
                        </span>
                        <div>
                            <h3>{{ $title }}</h3>
                            <p>{{ $copy }}</p>
                        </div>
                    </div>
                @endforeach

                <div class="sari-benefit-tile bg-[#fff8e9]">
                    <span class="sari-benefit-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 21s-7-4.4-7-11a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 6.6-7 11-7 11Z"></path>
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-[#a76c0c]">Shop with Purpose</h3>
                        <p>Support local. Build a brighter tomorrow.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
</div>

{{-- =========================================================
     MOBILE FILTER DRAWER
========================================================= --}}
<div id="mobileFilterDrawer" class="fixed inset-0 z-[90] hidden sari-products-page">
    <div id="mobileFilterOverlay" class="absolute inset-0 bg-black/30 backdrop-blur-[1px]"></div>
    <aside class="absolute right-0 top-0 flex h-full w-[340px] max-w-[90vw] flex-col border-l border-[#eee4d3] bg-white shadow-[0_24px_70px_rgba(44,34,22,0.15)]">
        <div class="flex min-h-[76px] items-center justify-between border-b border-[#eee4d3] px-5">
            <div><p class="text-[8px] font-semibold uppercase tracking-[0.12em] text-[#a8731f]">Product Filters</p><h2 class="mt-1 text-[15px] font-semibold text-[#28221b]">Narrow Results</h2></div>
            <button id="closeMobileFilters" type="button" class="grid h-10 w-10 place-items-center rounded-xl border border-[#e8dfd0] bg-white text-[#6e6558] transition hover:bg-[#f7eedf] hover:text-[#b97805]">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12"></path><path d="M18 6 6 18"></path></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5">
            <div class="border-b border-[#eee8df] pb-5">
                <p class="text-[10px] font-semibold text-[#403930]">Category</p>
                <div class="mt-3 space-y-1">
                    <button type="button" data-filter-category-mobile="" class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-[9px] transition hover:bg-[#fcf8f1]"><span>All Categories</span><span data-category-check-mobile="" class="hidden text-[#a8731f]">✓</span></button>
                    @foreach ($categories as $category)
                        <button type="button" data-filter-category-mobile="{{ $category['name'] }}" class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-[9px] transition hover:bg-[#fcf8f1]"><span>{{ $category['name'] }}</span><span data-category-check-mobile="{{ $category['name'] }}" class="hidden text-[#a8731f]">✓</span></button>
                    @endforeach
                </div>
            </div>

            <div class="border-b border-[#eee8df] py-5">
                <p class="text-[10px] font-semibold text-[#403930]">Price Range</p>
                <div class="mt-3 grid grid-cols-2 gap-2">
                    <div class="relative"><span class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-[8px] text-[#a1988d]">₱</span><input id="mobileMinPrice" type="number" min="0" placeholder="Min" class="h-9 w-full rounded-xl border border-[#e6dfd5] bg-white pl-6 pr-2 text-[9px] outline-none focus:border-[#c99a3d] focus:ring-4 focus:ring-[#c99a3d]/10"></div>
                    <div class="relative"><span class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-[8px] text-[#a1988d]">₱</span><input id="mobileMaxPrice" type="number" min="0" placeholder="Max" class="h-9 w-full rounded-xl border border-[#e6dfd5] bg-white pl-6 pr-2 text-[9px] outline-none focus:border-[#c99a3d] focus:ring-4 focus:ring-[#c99a3d]/10"></div>
                </div>
            </div>

            <input type="radio" name="mobileRatingFilter" value="" checked class="hidden">

                        <div class="border-b border-[#eee8df] py-5">
                <p class="text-[10px] font-semibold text-[#403930]">Availability</p>
                <label class="mt-3 flex cursor-pointer items-center gap-2"><input id="mobileInStockFilter" type="checkbox" class="h-3.5 w-3.5 rounded accent-[#c99128]"><span class="text-[9px] text-[#665e54]">In Stock Only</span></label>
            </div>

            <div class="py-5"><p class="text-[10px] font-semibold text-[#403930]">Size / Variation</p><div id="mobileVariationFilters" class="mt-3 flex flex-wrap gap-2"></div></div>
        </div>

        <div class="border-t border-[#eee4d3] bg-white p-4">
            <div class="grid grid-cols-2 gap-2">
                <button id="mobileClearFilters" type="button" class="h-11 rounded-xl border border-[#e1d6c6] bg-white text-[9px] font-semibold text-[#62594e] transition hover:bg-[#fcf8f1]">Clear</button>
                <button id="applyMobileFilters" type="button" class="h-11 rounded-xl bg-[#c9870e] text-[9px] font-semibold text-white transition hover:bg-[#ae7008]">Apply Filters</button>
            </div>
        </div>
    </aside>
</div>

{{-- =========================================================
     PRODUCT MODAL
========================================================= --}}
<div id="buyerProductModal" class="sari-products-page fixed inset-0 z-[120] hidden items-center justify-center bg-black/35 p-4 backdrop-blur-[2px]">
    <div class="max-h-[92vh] w-full max-w-[860px] overflow-y-auto rounded-[20px] border border-[#e9dfcf] bg-white shadow-[0_30px_90px_rgba(38,30,18,0.22)]">
        <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-[#eee7dc] bg-white p-5 sm:p-6">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#fbf5e9] px-2.5 py-1 text-[8px] font-semibold uppercase tracking-[0.1em] text-[#a8731f]">Customize Product</div>
                <h3 id="buyerModalTitle" class="mt-2 text-[19px] font-semibold tracking-[-0.03em] text-[#28221b]">Product Name</h3>
                <p id="buyerModalCategory" class="mt-1 text-[9px] text-[#91887d]">Category</p>
            </div>
            <button id="buyerCloseProductModal" type="button" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#e6dfd5] text-[#756d63] transition hover:bg-[#fcf7ee] hover:text-[#a8731f]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12"></path><path d="M18 6 6 18"></path></svg>
            </button>
        </div>

        <div class="grid grid-cols-1 gap-6 p-5 sm:p-6 lg:grid-cols-[.9fr_1.1fr]">
            <div class="overflow-hidden rounded-[16px] border border-[#ece6dd] bg-[#f5f1e8]"><img id="buyerModalImage" src="" alt="" class="aspect-square h-full w-full object-cover"></div>
            <div>
                <div class="flex flex-wrap items-end gap-3">
                    <p id="buyerModalPrice" class="text-[27px] font-semibold text-[#b97308]">₱0.00</p>
                    <p id="buyerModalOldPrice" class="hidden text-[10px] text-[#aaa197] line-through"></p>
                    <span id="buyerModalDiscount" class="hidden rounded-full bg-[#d9930a] px-2.5 py-1 text-[8px] font-semibold text-white"></span>
                </div>
                <div id="buyerModalSavings" class="mt-2 hidden rounded-xl border border-[#d8e7dc] bg-[#f3f8f5] px-3 py-2"><p class="text-[8px] font-semibold text-[#56816a]"></p></div>
                <div class="mt-3 flex flex-wrap items-center gap-2"><span id="buyerModalRating" class="rounded-full bg-[#fbf5e9] px-2.5 py-1 text-[8px] font-semibold text-[#a8731f]">★ 0.0</span><span id="buyerModalStock" class="rounded-full bg-[#eef6f1] px-2.5 py-1 text-[8px] font-semibold text-[#56816a]">0 in stock</span></div>
                <div id="buyerModalPromotions" class="mt-4 hidden flex-wrap gap-1.5"></div>

                <div id="buyerModalVoucher" class="mt-4 hidden rounded-[14px] border border-[#eadfc9] bg-[#fbf5e9] p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white text-[#a8731f]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 12v7H4v-7"></path><path d="M2 7h20v5H2z"></path><path d="M12 7v12"></path><path d="M12 7H8a2.5 2.5 0 1 1 2.5-2.5C10.5 6 12 7 12 7Z"></path><path d="M12 7h4a2.5 2.5 0 1 0-2.5-2.5C13.5 6 12 7 12 7Z"></path></svg></div>
                            <div><p class="text-[8px] font-semibold uppercase tracking-[0.1em] text-[#a8731f]">Voucher Available</p><p id="buyerModalVoucherStatus" class="mt-1 text-[10px] font-semibold text-[#463e36]">Claim this voucher to use it at checkout.</p></div>
                        </div>
                        <button id="buyerModalClaimVoucher" type="button" class="shrink-0 rounded-xl bg-[#c9870e] px-3 py-2 text-[8px] font-semibold text-white transition hover:bg-[#ae7008]">Claim Voucher</button>
                    </div>
                </div>

                <p id="buyerModalDescription" class="mt-5 text-[10px] leading-6 text-[#766d62]">Product description.</p>
                <div class="mt-5"><div class="flex items-center justify-between gap-3"><p class="text-[10px] font-semibold text-[#514a41]">Variation / Size</p><span id="buyerSelectedVariation" class="text-[8px] font-semibold text-[#a8731f]"></span></div><div id="buyerVariationGroup" class="mt-2 flex flex-wrap gap-2"></div></div>
                <div id="buyerModalSizeSection" class="mt-5 hidden"><div class="flex items-center justify-between gap-3"><p class="text-[10px] font-semibold text-[#514a41]">Size</p><span id="buyerSelectedSize" class="text-[8px] font-semibold text-[#a8731f]"></span></div><div id="buyerSizeGroup" class="mt-2 flex flex-wrap gap-2"></div></div>
                <div class="mt-5"><p class="text-[10px] font-semibold text-[#514a41]">Quantity</p><div class="mt-2 inline-flex items-center rounded-xl border border-[#e6dfd5] bg-white"><button id="buyerQtyMinus" type="button" class="grid h-10 w-10 place-items-center text-[#675f55] transition hover:bg-[#fcf8f1] hover:text-[#a8731f]">−</button><input id="buyerQty" type="number" min="1" value="1" class="h-10 w-12 border-x border-[#e6dfd5] text-center text-[10px] font-semibold text-[#302a24] outline-none"><button id="buyerQtyPlus" type="button" class="grid h-10 w-10 place-items-center text-[#675f55] transition hover:bg-[#fcf8f1] hover:text-[#a8731f]">+</button></div></div>
                <div class="mt-6 flex flex-col gap-2 sm:flex-row">
                    <button id="buyerModalAddToCart" type="button" class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-[9px] bg-[#c9870e] px-5 text-[10px] font-semibold text-white shadow-[0_8px_18px_rgba(201,145,40,0.16)] transition hover:-translate-y-0.5 hover:bg-[#ae7008]"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 4h2l2 12h10l2-8H7"></path><circle cx="9" cy="20" r="1.5"></circle><circle cx="17" cy="20" r="1.5"></circle></svg>Add to Cart</button>
                    <button id="buyerModalBuyNow" type="button" class="inline-flex h-11 flex-1 items-center justify-center rounded-[9px] border border-[#e1d6c6] bg-white px-5 text-[10px] font-semibold text-[#62594e] transition hover:bg-[#fcf8f1]">Buy Now</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================
     TOAST
========================================================= --}}
<div id="buyerToast" class="sari-products-page pointer-events-none fixed right-4 top-24 z-[150] hidden w-[calc(100%-2rem)] max-w-[360px] translate-y-2 rounded-[16px] border border-[#e8ddca] bg-white p-4 opacity-0 shadow-[0_18px_45px_rgba(55,43,25,0.16)] transition-all duration-300 sm:right-6">
    <div class="flex gap-3">
        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#fbf5e9] text-[#a8731f]"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m5 12 4 4L19 6"></path></svg></div>
        <div class="min-w-0"><p id="buyerToastTitle" class="text-[9px] font-semibold text-[#3d362d]">Cart Updated</p><p id="buyerToastText" class="mt-1 text-[8px] leading-4 text-[#7f766a]"></p></div>
    </div>
</div>

<x-buyer.products-page-data
    :products="$products"
    :search-query="$searchQuery ?? request('search', '')"
/>


<script>
(function () {
    const sariSearchProducts = @json($buyerSearchIndex);

    function escapeSearchHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function normalizeSearch(value) {
        return String(value ?? '')
            .trim()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    function pesoSearch(value) {
        return '₱' + Number(value || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function rankedProducts(query) {
        const q = normalizeSearch(query);
        if (!q) return [];

        return sariSearchProducts
            .map((product) => {
                const name = normalizeSearch(product.name);
                const category = normalizeSearch(product.category);
                const brand = normalizeSearch(product.brand);

                let score = 0;
                if (name === q) score += 100;
                if (name.startsWith(q)) score += 70;
                if (name.includes(q)) score += 50;
                if (brand.startsWith(q)) score += 30;
                if (brand.includes(q)) score += 20;
                if (category.startsWith(q)) score += 18;
                if (category.includes(q)) score += 12;

                return { product, score };
            })
            .filter((item) => item.score > 0)
            .sort((a, b) => b.score - a.score || String(a.product.name).localeCompare(String(b.product.name)))
            .map((item) => item.product);
    }

    function resultMarkup(product, index) {
        const stock = Number(product.stock || 0);

        const image = product.image
            ? `<img src="${escapeSearchHtml(product.image)}" alt="${escapeSearchHtml(product.name)}" loading="lazy">`
            : `
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6">
                    <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                    <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                </svg>
            `;

        const meta = [product.category, product.brand]
            .filter((value) => String(value || '').trim())
            .map(escapeSearchHtml)
            .join(' · ');

        return `
            <a
                href="${escapeSearchHtml(product.url)}"
                id="buyerLiveSearchOption-${index}"
                class="sari-live-result"
                role="option"
                data-live-search-option="${index}"
            >
                <span class="sari-live-result-image">${image}</span>

                <span class="sari-live-result-copy">
                    <span class="sari-live-result-name">${escapeSearchHtml(product.name)}</span>
                    <span class="sari-live-result-meta">
                        <span>${meta || 'SARI Marketplace'}</span>
                        <span class="sari-live-result-stock ${stock <= 0 ? 'is-out' : ''}">
                            ${stock > 0 ? `${stock.toLocaleString('en-PH')} in stock` : 'Out of stock'}
                        </span>
                    </span>
                </span>

                <span class="sari-live-result-price">${pesoSearch(product.price)}</span>
            </a>
        `;
    }

    function emptyMarkup(query) {
        return `
            <div class="sari-live-search-empty">
                <strong>No product found</strong>
                <p>No results for “${escapeSearchHtml(query)}”. Try another name, brand, or category.</p>
            </div>
        `;
    }

    function initSariLiveSearch() {
        const input = document.getElementById('buyerProductSearch');
        const wrap = document.getElementById('buyerLiveSearchWrap');
        const clear = document.getElementById('buyerLiveSearchClear');
        const panel = document.getElementById('buyerLiveSearchPanel');
        const list = document.getElementById('buyerLiveSearchList');
        const count = document.getElementById('buyerLiveSearchCount');
        const viewAll = document.getElementById('buyerLiveSearchViewAll');
        const viewAllText = document.getElementById('buyerLiveSearchViewAllText');

        if (!input || input.dataset.sariLiveSearchBound === '1') return;
        input.dataset.sariLiveSearchBound = '1';

        let matches = [];
        let activeIndex = -1;

        function closePanel() {
            panel?.classList.add('hidden');
            input.setAttribute('aria-expanded', 'false');
            activeIndex = -1;
            input.removeAttribute('aria-activedescendant');
        }

        function render() {
            const query = String(input.value || '').trim();
            clear?.classList.toggle('is-visible', query !== '');

            if (!query) {
                matches = [];
                if (list) list.innerHTML = '';
                closePanel();
                return;
            }

            const allMatches = rankedProducts(query);
            matches = allMatches.slice(0, 6);
            activeIndex = -1;

            if (count) {
                count.textContent = `${allMatches.length} match${allMatches.length === 1 ? '' : 'es'}`;
            }

            if (viewAllText) {
                viewAllText.textContent = allMatches.length
                    ? `View all results for “${query}”`
                    : 'No matching products';
            }

            if (list) {
                list.innerHTML = matches.length
                    ? matches.map(resultMarkup).join('')
                    : emptyMarkup(query);
            }

            panel?.classList.remove('hidden');
            input.setAttribute('aria-expanded', 'true');
        }

        function setActive(index) {
            const options = Array.from(list?.querySelectorAll('[data-live-search-option]') || []);
            if (!options.length) return;

            activeIndex = Math.max(0, Math.min(index, options.length - 1));

            options.forEach((option, optionIndex) => {
                option.classList.toggle('is-active', optionIndex === activeIndex);
            });

            const active = options[activeIndex];
            if (active) {
                input.setAttribute('aria-activedescendant', active.id);
                active.scrollIntoView({ block: 'nearest' });
            }
        }

        input.addEventListener('input', render);

        input.addEventListener('focus', function () {
            if (String(this.value || '').trim()) render();
        });

        input.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                if (panel?.classList.contains('hidden')) render();
                setActive(activeIndex + 1);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                setActive(activeIndex <= 0 ? 0 : activeIndex - 1);
            } else if (event.key === 'Enter' && activeIndex >= 0 && matches[activeIndex]) {
                event.preventDefault();
                window.location.href = matches[activeIndex].url;
            } else if (event.key === 'Escape') {
                closePanel();
            }
        });

        clear?.addEventListener('click', function () {
            input.value = '';
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.focus();
        });

        viewAll?.addEventListener('click', function () {
            closePanel();
            document.getElementById('buyerProductGrid')?.scrollIntoView({
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
                block: 'start',
            });
        });

        document.addEventListener('click', function (event) {
            if (panel && !panel.classList.contains('hidden') && !wrap?.contains(event.target)) {
                closePanel();
            }
        });

        document.addEventListener('keydown', function (event) {
            if ((event.ctrlKey || event.metaKey) && String(event.key).toLowerCase() === 'k') {
                event.preventDefault();
                input.focus();
                input.select();
            }
        });
    }

    function initSariFilterDock() {
        const categorySelect = document.getElementById('buyerCategorySelect');
        const availabilitySelect = document.getElementById('buyerAvailabilitySelect');
        const inStockFilter = document.getElementById('inStockFilter');

        const hiddenCategoryButtons = Array.from(document.querySelectorAll('[data-filter-category]'));
        const categoryChips = Array.from(document.querySelectorAll('[data-shop-category-chip]'));

        const priceTrigger = document.getElementById('buyerPriceTrigger');
        const pricePopover = document.getElementById('buyerPricePopover');
        const closePricePopover = document.getElementById('closeBuyerPricePopover');
        const priceTriggerText = document.getElementById('buyerPriceTriggerText');
        const minPrice = document.getElementById('minPrice');
        const maxPrice = document.getElementById('maxPrice');

        const clearAll = document.getElementById('clearAllFilters');
        const showAll = document.getElementById('showAllProducts');

        function chooseCategory(value) {
            const normalized = String(value || '');

            hiddenCategoryButtons
                .find((button) => String(button.dataset.filterCategory || '') === normalized)
                ?.click();

            if (categorySelect) categorySelect.value = normalized;

            categoryChips.forEach((chip) => {
                chip.classList.toggle(
                    'is-active',
                    String(chip.dataset.shopCategoryChip || '') === normalized
                );
            });
        }

        categorySelect?.addEventListener('change', function () {
            chooseCategory(this.value);
        });

        categoryChips.forEach((chip) => {
            chip.addEventListener('click', function () {
                chooseCategory(this.dataset.shopCategoryChip || '');
            });
        });

        availabilitySelect?.addEventListener('change', function () {
            if (!inStockFilter) return;

            const shouldCheck = this.value === 'in-stock';

            if (inStockFilter.checked !== shouldCheck) {
                inStockFilter.checked = shouldCheck;
                inStockFilter.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });

        priceTrigger?.addEventListener('click', function () {
            const opening = pricePopover?.classList.contains('hidden') ?? false;
            pricePopover?.classList.toggle('hidden', !opening);
            this.setAttribute('aria-expanded', opening ? 'true' : 'false');
        });

        closePricePopover?.addEventListener('click', function () {
            pricePopover?.classList.add('hidden');
            priceTrigger?.setAttribute('aria-expanded', 'false');
        });

        document.getElementById('applyPriceFilter')?.addEventListener('click', function () {
            const min = String(minPrice?.value || '').trim();
            const max = String(maxPrice?.value || '').trim();

            if (priceTriggerText) {
                if (min && max) {
                    priceTriggerText.textContent = `₱${min} – ₱${max}`;
                } else if (min) {
                    priceTriggerText.textContent = `From ₱${min}`;
                } else if (max) {
                    priceTriggerText.textContent = `Up to ₱${max}`;
                } else {
                    priceTriggerText.textContent = 'Any price';
                }
            }

            pricePopover?.classList.add('hidden');
            priceTrigger?.setAttribute('aria-expanded', 'false');
        });

        clearAll?.addEventListener('click', function () {
            if (categorySelect) categorySelect.value = '';
            if (availabilitySelect) availabilitySelect.value = '';
            if (priceTriggerText) priceTriggerText.textContent = 'Any price';

            categoryChips.forEach((chip) => {
                chip.classList.toggle(
                    'is-active',
                    String(chip.dataset.shopCategoryChip || '') === ''
                );
            });
        });

        showAll?.addEventListener('click', function () {
            chooseCategory('');
        });

        document.addEventListener('click', function (event) {
            if (
                pricePopover &&
                !pricePopover.classList.contains('hidden') &&
                !pricePopover.contains(event.target) &&
                !priceTrigger?.contains(event.target)
            ) {
                pricePopover.classList.add('hidden');
                priceTrigger?.setAttribute('aria-expanded', 'false');
            }
        });
    }

    function initSariCampaigns() {
        const shell = document.getElementById('buyerCampaignSpotlight');
        if (!shell || shell.dataset.bound === '1') return;

        shell.dataset.bound = '1';

        const slides = Array.from(shell.querySelectorAll('[data-campaign-slide]'));
        const dots = Array.from(shell.querySelectorAll('[data-campaign-dot]'));
        const prev = document.getElementById('buyerCampaignPrev');
        const next = document.getElementById('buyerCampaignNext');

        let activeIndex = 0;
        let rotationTimer = null;

        function updateCountdown(slide) {
            const endAt = String(slide.dataset.endAt || '').trim();
            if (!endAt) return;

            const target = new Date(endAt).getTime();
            if (!Number.isFinite(target)) return;

            const remaining = Math.max(0, target - Date.now());
            const totalSeconds = Math.floor(remaining / 1000);

            const values = {
                days: Math.floor(totalSeconds / 86400),
                hours: Math.floor((totalSeconds % 86400) / 3600),
                minutes: Math.floor((totalSeconds % 3600) / 60),
                seconds: totalSeconds % 60,
            };

            Object.entries(values).forEach(([part, value]) => {
                const node = slide.querySelector(`[data-countdown-${part}]`);
                if (node) node.textContent = String(value).padStart(2, '0');
            });
        }

        function show(index) {
            if (!slides.length) return;

            activeIndex = (index + slides.length) % slides.length;

            slides.forEach((slide, slideIndex) => {
                slide.hidden = slideIndex !== activeIndex;
            });

            dots.forEach((dot, dotIndex) => {
                dot.classList.toggle('is-active', dotIndex === activeIndex);
            });

            updateCountdown(slides[activeIndex]);
        }

        function restartRotation() {
            if (rotationTimer) window.clearInterval(rotationTimer);

            if (slides.length > 1) {
                rotationTimer = window.setInterval(() => {
                    show(activeIndex + 1);
                }, 6500);
            }
        }

        prev?.addEventListener('click', function () {
            show(activeIndex - 1);
            restartRotation();
        });

        next?.addEventListener('click', function () {
            show(activeIndex + 1);
            restartRotation();
        });

        dots.forEach((dot) => {
            dot.addEventListener('click', function () {
                show(Number(this.dataset.campaignDot || 0));
                restartRotation();
            });
        });

        slides.forEach(updateCountdown);
        window.setInterval(() => slides.forEach(updateCountdown), 1000);

        show(0);
        restartRotation();
    }

    function initSariProductsUi() {
        initSariLiveSearch();
        initSariFilterDock();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSariProductsUi, { once: true });
    } else {
        initSariProductsUi();
    }

    document.addEventListener('livewire:navigated', initSariProductsUi);
})();
</script>

@endsection

@push('scripts')
    @vite('resources/js/buyer-products.js')
@endpush
