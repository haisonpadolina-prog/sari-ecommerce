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
        --sari-cream: var(--sari-dark-4d21eb669b, #fff8eb);
        --sari-soft: #fbfaf8;
        --sari-border: var(--sari-dark-d8c2716225, #e9e3db);
        --sari-text: var(--sari-dark-e23e64dd1e, #211c18);
        --sari-muted: var(--sari-dark-a63aa16d7c, #81786f);
        background: var(--sari-dark-950baec030, #fbfaf8);
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
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #eee8e1);
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
        box-shadow: 0 12px 28px var(--sari-dark-3b2338cbf0, rgba(79, 28, 19, .12));
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
        background: var(--sari-dark-fbb35de0d1, #173f32);
    }

    .sari-campaign-slide[data-theme="charcoal"] {
        background: var(--sari-dark-fbb35de0d1, #292621);
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
            0 0 0 22px var(--sari-dark-e1384eb7fc, rgba(255, 220, 129, .035)),
            0 0 0 44px var(--sari-dark-104df339cd, rgba(255, 220, 129, .022));
    }

    .sari-campaign-slide::after {
        width: 170px;
        height: 170px;
        left: 32%;
        bottom: -125px;
        border: 1px solid var(--sari-dark-6da278ba55, rgba(255, 255, 255, .08));
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
        background: var(--sari-dark-30bd406a14, rgba(255, 255, 255, .09));
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
        border-left: 1px solid var(--sari-dark-ab70a14a3e, rgba(255,255,255,.18));
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
        background: var(--sari-dark-56adac4dda, rgba(255,255,255,.07));
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
        border: 1px solid var(--sari-dark-70334c7f17, rgba(255,255,255,.13));
        border-radius: 8px;
        background: var(--sari-dark-e832c3023b, rgba(0,0,0,.13));
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
        box-shadow: 0 12px 24px var(--sari-dark-e267f3cebc, rgba(32,8,4,.22));
    }

    .sari-campaign-slide[data-theme="green"] .sari-campaign-gift {
        background: #275b48;
    }

    .sari-campaign-slide[data-theme="charcoal"] .sari-campaign-gift {
        background: var(--sari-dark-fbb35de0d1, #4b463f);
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
        background: var(--sari-dark-a19bde6a70, #f7e4bb);
        padding: 11px 13px;
        color: var(--sari-dark-e23e64dd1e, #4d3321);
        font-size: 8px;
        font-weight: 750;
        line-height: 1.35;
        text-align: center;
        box-shadow: 0 8px 20px var(--sari-dark-f4efbbfaa5, rgba(32,8,4,.15));
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
        background: var(--sari-dark-950baec030, #fff);
        padding: 0 17px;
        color: var(--sari-dark-c62d3a8937, #9a4f15);
        font-size: 7.5px;
        font-weight: 800;
        box-shadow: 0 6px 16px var(--sari-dark-f4efbbfaa5, rgba(31,9,4,.15));
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
        background: var(--sari-dark-ff55c56e19, rgba(255,255,255,.95));
        color: var(--sari-dark-a63aa16d7c, #6e6257);
        box-shadow: 0 4px 12px var(--sari-dark-59ed29fe9d, rgba(26,10,5,.14));
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
        background: var(--sari-dark-09ef87e90f, rgba(255,255,255,.35));
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
        border: 1px solid var(--sari-dark-d8c2716225, #e7e1da);
        border-radius: 15px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 11px 13px;
        box-shadow: 0 9px 24px var(--sari-dark-6301903fc7, rgba(43,34,24,.042));
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
        color: var(--sari-dark-a63aa16d7c, #665d54);
        font-size: 6.4px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sari-filter-control {
        height: 40px;
        width: 100%;
        border: 1px solid var(--sari-dark-d8c2716225, #ddd7d0);
        border-radius: 9px;
        background: var(--sari-dark-950baec030, #fff);
        color: var(--sari-dark-e23e64dd1e, #50483f);
        font-size: 8px;
        font-weight: 550;
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .sari-filter-control:focus {
        border-color: #ca8b17;
        box-shadow: 0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(202,139,23,.08));
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
        color: var(--sari-dark-a63aa16d7c, #9a9188);
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
        color: var(--sari-dark-a63aa16d7c, #8c8278);
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
        border: 1px solid var(--sari-dark-d8c2716225, #e4ded7);
        border-radius: 13px;
        background: var(--sari-dark-950baec030, #fff);
        box-shadow:
            0 20px 48px var(--sari-dark-59ed29fe9d, rgba(39,31,23,.14)),
            0 4px 12px var(--sari-dark-25b2da5e49, rgba(39,31,23,.04));
    }

    .sari-live-search-head {
        display: flex;
        min-height: 37px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #f0ebe5);
        background: var(--sari-dark-950baec030, #fffdfa);
        padding: 0 11px;
    }

    .sari-live-search-head strong {
        color: var(--sari-dark-e23e64dd1e, #514940);
        font-size: 6.8px;
        font-weight: 800;
        letter-spacing: .07em;
        text-transform: uppercase;
    }

    .sari-live-search-head span {
        color: var(--sari-dark-a63aa16d7c, #9b9288);
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
        background: var(--sari-dark-4d21eb669b, #fff8eb);
    }

    .sari-live-result-image {
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 auto;
        place-items: center;
        overflow: hidden;
        border: 1px solid var(--sari-dark-d8c2716225, #e7e1d9);
        border-radius: 8px;
        background: var(--sari-dark-4d21eb669b, #f6f3ee);
        color: var(--sari-dark-a63aa16d7c, #aaa198);
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
        color: var(--sari-dark-e23e64dd1e, #302a24);
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
        color: var(--sari-dark-a63aa16d7c, #948b82);
        font-size: 6.2px;
    }

    .sari-live-result-stock {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: var(--sari-dark-a63aa16d7c, #4f8065);
    }

    .sari-live-result-stock::before {
        content: '';
        width: 4px;
        height: 4px;
        border-radius: 999px;
        background: #4f8065;
    }

    .sari-live-result-stock.is-out {
        color: var(--sari-dark-2a778702e9, #bd5555);
    }

    .sari-live-result-stock.is-out::before {
        background: #bd5555;
    }

    .sari-live-result-price {
        flex: 0 0 auto;
        color: var(--sari-dark-6e784b4931, #bf7b08);
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
        color: var(--sari-dark-e23e64dd1e, #5f574f);
        font-size: 8px;
        font-weight: 750;
    }

    .sari-live-search-empty p {
        margin-top: 4px;
        color: var(--sari-dark-a63aa16d7c, #9b9288);
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
        border-top: 1px solid var(--sari-dark-d8c2716225, #eee9e3);
        background: var(--sari-dark-950baec030, #fffdfa);
        padding: 0 11px;
        color: var(--sari-dark-9b82ece8ba, #996414);
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
        border: 1px solid var(--sari-dark-d8c2716225, #e4ded7);
        border-radius: 12px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 13px;
        box-shadow: 0 18px 40px var(--sari-dark-3b2338cbf0, rgba(39,31,23,.12));
    }

    .sari-price-field {
        position: relative;
    }

    .sari-price-field > span {
        position: absolute;
        left: 9px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--sari-dark-a63aa16d7c, #9b9084);
        font-size: 7px;
        font-weight: 700;
        pointer-events: none;
    }

    .sari-price-field input {
        height: 38px;
        width: 100%;
        border: 1px solid var(--sari-dark-d8c2716225, #ded8d1);
        border-radius: 8px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 0 8px 0 22px;
        color: var(--sari-dark-e23e64dd1e, #4b443d);
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
        color: var(--sari-dark-9b2ca2d764, #ad700d);
        font-size: 7.5px;
        font-weight: 750;
        white-space: nowrap;
    }

    .sari-filter-reset:hover {
        background: var(--sari-dark-4d21eb669b, #fff8ea);
    }

    .sari-result-pill {
        display: inline-flex;
        height: 40px;
        min-width: 100px;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: var(--sari-dark-4d21eb669b, #fff3dc);
        padding: 0 15px;
        color: var(--sari-dark-9b2ca2d764, #a66b0b);
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
        border: 1px solid var(--sari-dark-d8c2716225, #e7d7b7);
        border-radius: 999px;
        background: var(--sari-dark-4d21eb669b, #fff8e9);
        padding: 0 9px;
        color: var(--sari-dark-0e58bfc5a3, #986312);
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
        border: 1px solid var(--sari-dark-d8c2716225, #e2dcd5) !important;
        border-radius: 999px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        padding: 0 9px !important;
        color: var(--sari-dark-a63aa16d7c, #70675e) !important;
        font-size: 6.3px !important;
        font-weight: 650 !important;
        box-shadow: none !important;
    }

    #variationFilters button.is-active,
    #mobileVariationFilters button.is-active {
        border-color: #cf8b0e !important;
        background: var(--sari-dark-4d21eb669b, #fff5df) !important;
        color: var(--sari-dark-0e58bfc5a3, #986312) !important;
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
        color: var(--sari-dark-e23e64dd1e, #201b17);
    }

    .sari-recommended-title span {
        color: var(--sari-dark-c8bdcce6ab, #c3830d);
    }

    .sari-recommended-copy {
        margin-top: 6px;
        color: var(--sari-dark-a63aa16d7c, #81786f);
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
        border: 1px solid var(--sari-dark-d8c2716225, #ddd6ce);
        border-radius: 999px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 0 15px;
        color: var(--sari-dark-e23e64dd1e, #413a33);
        font-size: 7px;
        font-weight: 650;
    }

    .sari-inline-chip:hover {
        border-color: #d0ba8a;
        background: var(--sari-dark-950baec030, #fff9ef);
        color: var(--sari-dark-4b8b580e58, #9a6513);
    }

    .sari-inline-chip.is-active {
        border-color: #cf8b0e;
        background: #cf8b0e;
        color: #fff;
        box-shadow: 0 7px 15px var(--sari-dark-b5c2a2270e, rgba(201,135,14,.16));
    }

    .sari-view-all {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-left: 3px;
        color: var(--sari-dark-4b8b580e58, #b5740c);
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
        border: 1px solid var(--sari-dark-d8c2716225, #e8e1d8);
        border-radius: 14px;
        background: var(--sari-dark-950baec030, #fff);
        box-shadow: 0 7px 18px var(--sari-dark-e1384eb7fc, rgba(43,34,24,.025));
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
        border-right: 1px solid var(--sari-dark-d8c2716225, #eee8e1);
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
        background: var(--sari-dark-4d21eb669b, #fff5df);
        color: var(--sari-dark-9b2ca2d764, #bd7b0f);
    }

    .sari-benefit-tile h3 {
        color: var(--sari-dark-e23e64dd1e, #36302a);
        font-size: 7.8px;
        font-weight: 750;
    }

    .sari-benefit-tile p {
        margin-top: 2px;
        color: var(--sari-dark-a63aa16d7c, #968d84);
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
            border-bottom: 1px solid var(--sari-dark-d8c2716225, #eee8e1);
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
            0 10px 24px var(--sari-dark-8eadf403a5, rgba(91, 30, 19, .10)),
            inset 0 1px 0 var(--sari-dark-2f91cbc37f, rgba(255,255,255,.10));
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
        box-shadow: 0 7px 20px var(--sari-dark-7593018246, rgba(43,34,24,.035));
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
        background: var(--sari-dark-950baec030, #fffdfa);
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
        box-shadow: 0 8px 22px var(--sari-dark-c33e3d1bb4, rgba(89,35,17,.08));
    }

    .sari-filter-dock {
        margin-top: 15px;
        border: 1px solid var(--sari-dark-d8c2716225, #e7e1d9);
        border-radius: 13px;
        background: var(--sari-dark-ad56cde17e, rgba(255,255,255,.98));
        padding: 10px 12px;
        box-shadow: 0 7px 20px var(--sari-dark-7593018246, rgba(43,34,24,.035));
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
        background: var(--sari-dark-950baec030, #fff);
        font-size: 7.8px;
    }

    .sari-filter-reset,
    .sari-result-pill {
        height: 39px;
    }

    .sari-result-pill {
        min-width: 96px;
        border: 1px solid var(--sari-dark-d8c2716225, #f0dfbd);
        background: var(--sari-dark-4d21eb669b, #fff4df);
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
            0 18px 42px var(--sari-dark-3e32d50f5e, rgba(39,31,23,.13)),
            0 4px 12px var(--sari-dark-7593018246, rgba(39,31,23,.035));
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
            box-shadow: 0 5px 14px var(--sari-dark-7c935c8886, rgba(89,35,17,.07));
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
.sari-reference-category-row {
            margin-top: 6px !important;
        }

        .sari-showcase-grid {
            margin-top: 7px !important;
        }
    }


    /* ============================================================
       FINAL EQUAL PRODUCT CARD GRID
       Matches the Home page behavior: every product card has equal weight.
    ============================================================ */
    .sari-showcase-grid {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        grid-auto-flow: row !important;
        align-items: stretch !important;
        gap: 14px !important;
        margin-top: 10px !important;
    }

    .sari-showcase-grid > * {
        min-width: 0;
        height: 100%;
    }

    .sari-showcase-grid > .sari-featured-product-card {
        grid-row: auto !important;
        grid-column: auto !important;
    }

    @media (max-width: 1199px) {
        .sari-showcase-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 899px) {
        .sari-showcase-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 639px) {
        .sari-showcase-grid {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }
    }


    /* ============================================================
       SARI SHOP PRODUCTS — SELLER CREATE DESIGN LANGUAGE
       Visual-only overrides. Existing search/filter/product logic
       and all IDs/data attributes remain untouched.
    ============================================================ */

    .sari-products-page {
        --catalog-panel: var(--sari-dark-950baec030, #ffffff);
        --catalog-page: #f5f6f8;
        --catalog-border: var(--sari-dark-d8c2716225, #e3e6ea);
        --catalog-border-soft: var(--sari-dark-d8c2716225, #eceef1);
        --catalog-text: var(--sari-dark-e23e64dd1e, #2f343b);
        --catalog-muted: var(--sari-dark-a63aa16d7c, #818995);
        --catalog-gold: #c88912;
        --catalog-gold-dark: #a96f0e;

        background: var(--catalog-page) !important;
        color: var(--catalog-text);
    }

    .sari-products-main {
        max-width: 1440px !important;
        padding-top: 20px !important;
        padding-bottom: 34px !important;
    }

    /* -----------------------------
       FLOATING PAGE INTRO
    ----------------------------- */
    .sari-products-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--catalog-border) !important;
        border-radius: 14px !important;
        background: var(--catalog-panel) !important;
        padding: 17px 20px 18px !important;
        box-shadow:
            0 10px 28px var(--sari-dark-1190f58155, rgba(31, 41, 55, .045)),
            0 2px 7px var(--sari-dark-e1384eb7fc, rgba(31, 41, 55, .025)) !important;
    }

    .sari-products-hero::after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        width: 115px;
        height: 3px;
        border-radius: 0 0 0 999px;
        background: #c88912;
        opacity: .95;
    }

    .sari-products-hero > div {
        max-width: 760px;
    }

    .sari-products-hero p:first-child {
        margin: 0 !important;
        color: var(--sari-dark-685eb4fc40, #a87316) !important;
        font-size: 7px !important;
        font-weight: 800 !important;
        line-height: 1.2 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase;
    }

    .sari-products-hero h1 {
        margin-top: 5px !important;
        color: var(--sari-dark-e23e64dd1e, #2d3137) !important;
        font-size: clamp(27px, 2.3vw, 32px) !important;
        font-weight: 750 !important;
        line-height: 1.06 !important;
        letter-spacing: -.035em !important;
    }

    .sari-products-hero h1 span {
        color: var(--sari-dark-19b301f69a, #c88912) !important;
    }

    .sari-products-hero h1 + p {
        max-width: 650px !important;
        margin-top: 7px !important;
        color: var(--sari-dark-a63aa16d7c, #818995) !important;
        font-size: 8px !important;
        font-weight: 400 !important;
        line-height: 1.65 !important;
    }

    /* -----------------------------
       FLOATING SEARCH RESULT NOTICE
    ----------------------------- */
    .sari-products-hero + section[class*="mb-4"] {
        border-color: var(--catalog-border) !important;
        border-radius: 13px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow:
            0 8px 22px var(--sari-dark-7593018246, rgba(31, 41, 55, .035)),
            0 2px 6px var(--sari-dark-f6fe7319d9, rgba(31, 41, 55, .02)) !important;
    }

    /* -----------------------------
       FLOATING FILTER WORKSPACE
    ----------------------------- */
    .sari-filter-dock {
        margin-top: 12px !important;
        border: 1px solid var(--catalog-border) !important;
        border-radius: 14px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        padding: 13px 14px !important;
        box-shadow:
            0 10px 28px var(--sari-dark-1190f58155, rgba(31, 41, 55, .045)),
            0 2px 7px var(--sari-dark-e1384eb7fc, rgba(31, 41, 55, .025)) !important;
    }

    .sari-filter-grid {
        grid-template-columns:
            minmax(320px, 1.55fr)
            minmax(145px, .72fr)
            minmax(145px, .72fr)
            minmax(145px, .72fr)
            minmax(145px, .72fr)
            auto
            auto !important;
        gap: 9px !important;
    }

    .sari-filter-label {
        margin-bottom: 5px !important;
        color: var(--sari-dark-a63aa16d7c, #747c86) !important;
        font-size: 6.2px !important;
        font-weight: 750 !important;
        letter-spacing: .07em !important;
    }

    .sari-filter-control {
        height: 40px !important;
        border: 1px solid var(--sari-dark-d8c2716225, #dfe3e8) !important;
        border-radius: 9px !important;
        background-color: var(--sari-dark-950baec030, #fff) !important;
        color: var(--sari-dark-e23e64dd1e, #4a515b) !important;
        font-size: 8px !important;
        font-weight: 500 !important;
        box-shadow: none !important;
    }

    .sari-filter-control:hover {
        border-color: var(--sari-dark-d8c2716225, #d3d8de) !important;
        background-color: var(--sari-dark-950baec030, #fff) !important;
    }

    .sari-filter-control:focus {
        border-color: #c88912 !important;
        box-shadow: 0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(200, 137, 18, .08)) !important;
    }

    .sari-live-search input {
        border-color: #d9c8a8 !important;
        background: var(--sari-dark-950baec030, #fffdf9) !important;
    }

    .sari-live-search > svg {
        color: var(--sari-dark-a63aa16d7c, #7f8790) !important;
    }

    .sari-live-search-panel,
    .sari-price-popover {
        border-color: var(--catalog-border) !important;
        border-radius: 12px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow:
            0 20px 48px var(--sari-dark-3b2338cbf0, rgba(31, 41, 55, .12)),
            0 4px 12px var(--sari-dark-7593018246, rgba(31, 41, 55, .035)) !important;
    }

    .sari-live-search-head,
    .sari-live-search-footer {
        background: var(--sari-dark-950baec030, #fafbfc) !important;
        border-color: var(--sari-dark-d8c2716225, #eceff2) !important;
    }

    .sari-filter-reset {
        height: 40px !important;
        border: 1px solid transparent;
        border-radius: 9px !important;
        color: var(--sari-dark-6e784b4931, #9f6b13) !important;
        font-size: 7.3px !important;
    }

    .sari-filter-reset:hover {
        border-color: var(--sari-dark-d8c2716225, #eadfc9) !important;
        background: var(--sari-dark-950baec030, #fffaf1) !important;
    }

    .sari-result-pill {
        height: 40px !important;
        min-width: 96px !important;
        border: 1px solid var(--sari-dark-d8c2716225, #eadfc9) !important;
        border-radius: 9px !important;
        background: var(--sari-dark-950baec030, #fffaf1) !important;
        color: var(--sari-dark-6e784b4931, #9c6811) !important;
        font-size: 7.4px !important;
        box-shadow: none !important;
    }

    /* -----------------------------
       FLOATING CATEGORY BAR
    ----------------------------- */
    .sari-reference-category-row {
        justify-content: flex-start !important;
        margin-top: 11px !important;
        border: 1px solid var(--catalog-border);
        border-radius: 13px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 9px 11px;
        box-shadow:
            0 8px 22px var(--sari-dark-7593018246, rgba(31, 41, 55, .035)),
            0 2px 6px var(--sari-dark-7672bb207e, rgba(31, 41, 55, .018));
    }

    .sari-category-chips {
        gap: 6px !important;
    }

    .sari-inline-chip {
        min-height: 30px !important;
        border: 1px solid var(--sari-dark-d8c2716225, #e0e4e8) !important;
        border-radius: 8px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        padding: 0 12px !important;
        color: var(--sari-dark-a63aa16d7c, #59616b) !important;
        font-size: 6.8px !important;
        font-weight: 650 !important;
        box-shadow: none !important;
    }

    .sari-inline-chip:hover {
        border-color: #dfc995 !important;
        background: var(--sari-dark-950baec030, #fffaf1) !important;
        color: var(--sari-dark-9b2ca2d764, #946313) !important;
    }

    .sari-inline-chip.is-active {
        border-color: #c88912 !important;
        background: #c88912 !important;
        color: #fff !important;
        box-shadow: 0 5px 12px var(--sari-dark-59ed29fe9d, rgba(200,137,18,.14)) !important;
    }

    .sari-view-all {
        min-height: 30px;
        margin-left: 2px !important;
        border-radius: 8px;
        padding: 0 8px;
        color: var(--sari-dark-6e784b4931, #9e6a12) !important;
        font-size: 7px !important;
    }

    .sari-view-all:hover {
        background: var(--sari-dark-950baec030, #fffaf1);
    }

    /* -----------------------------
       PRODUCT GRID / FLOATING CARDS
       Product card internals stay untouched.
    ----------------------------- */
    .sari-showcase-grid {
        margin-top: 12px !important;
        gap: 13px !important;
    }

    .sari-showcase-grid > * {
        min-width: 0;
        height: 100%;
        filter: drop-shadow(0 8px 18px rgba(31, 41, 55, .025));
    }

    /* -----------------------------
       FLOATING BUYER PROTECTION
    ----------------------------- */
    .sari-benefits-wrap {
        margin-top: 13px !important;
        border: 1px solid var(--catalog-border) !important;
        border-radius: 13px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow:
            0 9px 24px var(--sari-dark-7593018246, rgba(31, 41, 55, .035)),
            0 2px 6px var(--sari-dark-7672bb207e, rgba(31, 41, 55, .018)) !important;
    }

    .sari-benefit-tile {
        min-height: 62px;
        border-color: var(--sari-dark-d8c2716225, #eceef1) !important;
        padding: 10px 13px !important;
    }

    .sari-benefit-icon {
        width: 32px !important;
        height: 32px !important;
        border-radius: 9px !important;
        background: var(--sari-dark-4d21eb669b, #fff8eb) !important;
        color: var(--sari-dark-9b2ca2d764, #b9780f) !important;
    }

    .sari-benefit-tile h3 {
        color: var(--sari-dark-e23e64dd1e, #3d434b) !important;
        font-size: 7.2px !important;
        font-weight: 700 !important;
    }

    .sari-benefit-tile p {
        margin-top: 2px !important;
        color: var(--sari-dark-a63aa16d7c, #8b929b) !important;
        font-size: 5.9px !important;
        line-height: 1.4 !important;
    }

    /* -----------------------------
       FLOATING EMPTY STATE / SHOPS
    ----------------------------- */
    #buyerProductEmpty {
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    /* -----------------------------
       MODAL MATCHES SAME SYSTEM
    ----------------------------- */
    #buyerProductModal > div {
        border-color: var(--catalog-border) !important;
        border-radius: 15px !important;
        box-shadow: 0 30px 80px var(--sari-dark-ef22e61893, rgba(31,41,55,.20)) !important;
    }

    /* -----------------------------
       RESPONSIVE
    ----------------------------- */
    @media (max-width: 1279px) {
        .sari-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .sari-live-search {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 767px) {
        .sari-products-main {
            padding-top: 13px !important;
        }

        .sari-products-hero {
            border-radius: 12px !important;
            padding: 15px 16px !important;
        }

        .sari-products-hero h1 {
            font-size: 25px !important;
        }

        .sari-products-hero h1 + p {
            font-size: 7.7px !important;
        }

        .sari-reference-category-row {
            margin-top: 8px !important;
            border-radius: 11px;
            overflow-x: auto;
            padding: 8px;
            scrollbar-width: none;
        }

        .sari-reference-category-row::-webkit-scrollbar {
            display: none;
        }

        .sari-reference-category-row .sari-category-chips {
            min-width: max-content;
            flex-wrap: nowrap !important;
        }

        .sari-benefits-wrap {
            border-radius: 11px !important;
        }
    }


    /* ============================================================
       FINAL REFINEMENT — OPEN TITLE + PREMIUM SEARCH/DROPDOWNS
       Visual-only overrides. Functionality stays unchanged.
    ============================================================ */

    /* Open heading: remove the floating container treatment */
    .sari-products-hero {
        overflow: visible !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 2px 2px 14px !important;
        box-shadow: none !important;
    }

    .sari-products-hero::after {
        display: none !important;
    }

    .sari-products-hero > div {
        max-width: 760px;
    }

    .sari-products-hero p:first-child {
        color: var(--sari-dark-685eb4fc40, #a87316) !important;
        font-size: 6.6px !important;
        font-weight: 800 !important;
        letter-spacing: .15em !important;
    }

    .sari-products-hero h1 {
        margin-top: 5px !important;
        color: var(--sari-dark-e23e64dd1e, #2f343b) !important;
        font-size: clamp(27px, 2.25vw, 31px) !important;
        font-weight: 760 !important;
        line-height: 1.04 !important;
        letter-spacing: -.035em !important;
    }

    .sari-products-hero h1 + p {
        margin-top: 7px !important;
        max-width: 650px !important;
        color: var(--sari-dark-a63aa16d7c, #818995) !important;
        font-size: 8px !important;
        line-height: 1.6 !important;
    }

    /* Search gets stronger visual priority */
    .sari-live-search {
        position: relative;
        z-index: 75;
    }

    .sari-live-search .sari-filter-label {
        color: var(--sari-dark-a63aa16d7c, #626a74) !important;
    }

    .sari-live-search input.sari-filter-control {
        height: 42px !important;
        border: 1px solid var(--sari-dark-d8c2716225, #d9c9aa) !important;
        border-radius: 10px !important;
        background: var(--sari-dark-950baec030, #fffefa) !important;
        padding-left: 40px !important;
        padding-right: 40px !important;
        color: var(--sari-dark-e23e64dd1e, #343a42) !important;
        font-size: 8.3px !important;
        font-weight: 500 !important;
        box-shadow:
            inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255,255,255,.75)),
            0 2px 5px var(--sari-dark-7672bb207e, rgba(31,41,55,.018)) !important;
        transition:
            border-color .16s ease,
            box-shadow .16s ease,
            background-color .16s ease !important;
    }

    .sari-live-search input.sari-filter-control::placeholder {
        color: var(--sari-dark-a63aa16d7c, #9aa1a9) !important;
        opacity: 1;
    }

    .sari-live-search input.sari-filter-control:hover {
        border-color: #ccb684 !important;
        background: var(--sari-dark-950baec030, #fff) !important;
    }

    .sari-live-search input.sari-filter-control:focus {
        border-color: #c88912 !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow:
            0 0 0 3px var(--sari-dark-b12a5e9552, rgba(200,137,18,.09)),
            0 5px 14px var(--sari-dark-7593018246, rgba(31,41,55,.035)) !important;
    }

    .sari-live-search > svg {
        left: 13px !important;
        bottom: 13px !important;
        width: 16px !important;
        height: 16px !important;
        color: var(--sari-dark-a63aa16d7c, #7f8790) !important;
        stroke-width: 1.9 !important;
    }

    .sari-live-search-clear {
        right: 8px !important;
        bottom: 8px !important;
        width: 26px !important;
        height: 26px !important;
        border-radius: 7px !important;
    }

    .sari-live-search-clear:hover {
        background: var(--sari-dark-950baec030, #f6f7f8) !important;
        color: var(--sari-dark-a63aa16d7c, #5e6670) !important;
    }

    /* Premium dropdown/select controls */
    .sari-products-select,
    .sari-price-trigger {
        height: 42px !important;
        border: 1px solid var(--sari-dark-d8c2716225, #dfe3e8) !important;
        border-radius: 10px !important;
        background-color: var(--sari-dark-950baec030, #fff) !important;
        color: var(--sari-dark-e23e64dd1e, #4a515b) !important;
        font-size: 8px !important;
        font-weight: 550 !important;
        box-shadow:
            inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255,255,255,.8)),
            0 2px 5px var(--sari-dark-37396001f9, rgba(31,41,55,.015)) !important;
        transition:
            border-color .16s ease,
            box-shadow .16s ease,
            background-color .16s ease !important;
    }

    .sari-products-select {
        padding-left: 12px !important;
        padding-right: 36px !important;
        background-position: right 12px center !important;
        background-size: 13px 13px !important;
    }

    .sari-products-select:hover,
    .sari-price-trigger:hover {
        border-color: var(--sari-dark-d8c2716225, #cfd5dc) !important;
        background-color: var(--sari-dark-950baec030, #fbfcfd) !important;
    }

    .sari-products-select:focus,
    .sari-price-trigger:focus,
    .sari-price-trigger[aria-expanded="true"] {
        border-color: #c88912 !important;
        background-color: var(--sari-dark-950baec030, #fff) !important;
        box-shadow: 0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(200,137,18,.08)) !important;
        outline: none !important;
    }

    .sari-price-trigger {
        padding: 0 12px !important;
        text-align: left !important;
    }

    .sari-price-trigger > span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sari-price-trigger svg {
        width: 14px !important;
        height: 14px !important;
        color: var(--sari-dark-a63aa16d7c, #7a828c) !important;
        transition: transform .16s ease;
    }

    .sari-price-trigger[aria-expanded="true"] svg {
        transform: rotate(180deg);
    }

    /* Cleaner dropdown popover */
    .sari-price-popover {
        top: calc(100% + 7px) !important;
        width: 270px !important;
        border: 1px solid var(--sari-dark-d8c2716225, #e1e5e9) !important;
        border-radius: 11px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        padding: 12px !important;
        box-shadow:
            0 20px 48px var(--sari-dark-3b2338cbf0, rgba(31,41,55,.12)),
            0 4px 12px var(--sari-dark-7593018246, rgba(31,41,55,.035)) !important;
    }

    .sari-price-field input {
        height: 38px !important;
        border: 1px solid var(--sari-dark-d8c2716225, #dfe3e8) !important;
        border-radius: 8px !important;
        color: var(--sari-dark-e23e64dd1e, #4a515b) !important;
        font-size: 8px !important;
    }

    .sari-price-field input:focus {
        border-color: #c88912 !important;
        box-shadow: 0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(200,137,18,.08)) !important;
        outline: none !important;
    }

    #applyPriceFilter {
        border-radius: 8px !important;
        background: #c88912 !important;
        box-shadow: 0 5px 12px var(--sari-dark-59ed29fe9d, rgba(200,137,18,.14)) !important;
    }

    #applyPriceFilter:hover {
        background: #ad760f !important;
    }

    /* Keep row aligned now that controls are 42px */
    .sari-filter-reset,
    .sari-result-pill {
        height: 42px !important;
    }

    .sari-filter-dock {
        padding-top: 12px !important;
        padding-bottom: 12px !important;
    }

    @media (max-width: 767px) {
        .sari-products-hero {
            padding: 1px 2px 12px !important;
        }

        .sari-products-hero h1 {
            font-size: 25px !important;
        }
    }


    /* ============================================================
       FINAL ENTERPRISE CATEGORY NAV
       Matches supplied charcoal-active reference.
    ============================================================ */
    .sari-reference-category-row {
        display: flex !important;
        width: 100% !important;
        align-items: center !important;
        justify-content: flex-start !important;
        margin-top: 12px !important;
        overflow: hidden !important;
        border: 1px solid var(--sari-dark-d8c2716225, #e1e5e9) !important;
        border-radius: 18px !important;
        background: var(--sari-dark-56c375634d, rgba(255, 255, 255, .985)) !important;
        padding: 10px 14px !important;
        box-shadow:
            0 14px 34px var(--sari-dark-fbd3b3b9f6, rgba(31, 41, 55, .055)),
            0 3px 9px var(--sari-dark-e1384eb7fc, rgba(31, 41, 55, .025)) !important;
    }

    .sari-reference-category-row .sari-category-chips {
        display: flex !important;
        width: 100% !important;
        min-width: 0 !important;
        align-items: center !important;
        flex-wrap: nowrap !important;
        gap: 10px !important;
    }

    .sari-reference-category-row .sari-inline-chip {
        display: inline-flex !important;
        min-height: 42px !important;
        flex: 0 0 auto !important;
        align-items: center !important;
        justify-content: center !important;
        border: 1px solid var(--sari-dark-d8c2716225, #dfe3e8) !important;
        border-radius: 13px !important;
        background: var(--sari-dark-950baec030, #ffffff) !important;
        padding: 0 18px !important;
        color: var(--sari-dark-e23e64dd1e, #303741) !important;
        font-size: 8.2px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        letter-spacing: -.015em !important;
        white-space: nowrap !important;
        box-shadow:
            inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255,255,255,.85)),
            0 2px 5px var(--sari-dark-7672bb207e, rgba(31,41,55,.018)) !important;
        transition:
            background-color .16s ease,
            border-color .16s ease,
            color .16s ease,
            box-shadow .16s ease,
            transform .16s ease !important;
    }

    .sari-reference-category-row .sari-inline-chip:hover {
        border-color: var(--sari-dark-d8c2716225, #cfd4da) !important;
        background: var(--sari-dark-950baec030, #f7f8f9) !important;
        color: var(--sari-dark-e23e64dd1e, #20252b) !important;
        transform: translateY(-1px);
    }

    .sari-reference-category-row .sari-inline-chip:focus-visible {
        outline: 2px solid var(--sari-dark-dd32a0938d, rgba(47, 47, 47, .28)) !important;
        outline-offset: 2px !important;
    }

    .sari-reference-category-row .sari-inline-chip.is-active {
        border-color: var(--sari-dark-d8c2716225, #2f2f2f) !important;
        background: linear-gradient(180deg, var(--sari-dark-fbb35de0d1, #363636) 0%, var(--sari-dark-fbb35de0d1, #292929) 100%) !important;
        color: #ffffff !important;
        box-shadow:
            0 8px 18px var(--sari-dark-3135cb7628, rgba(24, 24, 24, .17)),
            inset 0 1px 0 var(--sari-dark-0d18deb7fd, rgba(255,255,255,.06)) !important;
        transform: none !important;
    }

    .sari-reference-category-row .sari-inline-chip.is-active:hover {
        border-color: var(--sari-dark-d8c2716225, #292929) !important;
        background: linear-gradient(180deg, var(--sari-dark-fbb35de0d1, #333333) 0%, var(--sari-dark-fbb35de0d1, #262626) 100%) !important;
        color: #fff !important;
    }

    @media (max-width: 1279px) {
        .sari-reference-category-row {
            overflow-x: auto !important;
            scrollbar-width: none;
        }

        .sari-reference-category-row::-webkit-scrollbar {
            display: none;
        }

        .sari-reference-category-row .sari-category-chips {
            width: max-content !important;
            min-width: max-content !important;
        }
    }

    @media (max-width: 767px) {
        .sari-reference-category-row {
            margin-top: 9px !important;
            border-radius: 14px !important;
            padding: 8px 9px !important;
        }

        .sari-reference-category-row .sari-category-chips {
            gap: 7px !important;
        }

        .sari-reference-category-row .sari-inline-chip {
            min-height: 38px !important;
            border-radius: 11px !important;
            padding: 0 14px !important;
            font-size: 7.7px !important;
        }
    }


    /* ============================================================
       RELIABLE NATIVE PRICE DROPDOWN
       Uses <details>/<summary> so the Price Range always opens
       without depending on a JavaScript click listener.
    ============================================================ */
    .sari-price-details {
        position: relative;
        display: block;
    }

    .sari-price-details > summary {
        list-style: none;
    }

    .sari-price-details > summary::-webkit-details-marker {
        display: none;
    }

    .sari-price-details .sari-price-trigger {
        display: flex !important;
        width: 100% !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 8px !important;
        cursor: pointer !important;
        user-select: none;
        pointer-events: auto !important;
    }

    .sari-price-details .sari-price-trigger > * {
        pointer-events: none;
    }

    .sari-price-details[open] .sari-price-trigger {
        border-color: #c88912 !important;
        box-shadow: 0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(200,137,18,.08)) !important;
    }

    .sari-price-details .sari-price-trigger svg {
        transition: transform .16s ease;
    }

    .sari-price-details[open] .sari-price-trigger svg {
        transform: rotate(180deg);
    }

    .sari-price-details:not([open]) .sari-price-popover {
        display: none !important;
    }

    .sari-price-details[open] .sari-price-popover {
        display: block !important;
        pointer-events: auto !important;
    }

    .sari-price-popover {
        z-index: 180 !important;
    }


    /* ============================================================
       FORMAL RELIABLE DROPDOWNS
       Native <details> handles opening; hidden selects keep existing
       buyer-products.js behavior as the source of truth.
    ============================================================ */
    .sari-formal-dropdown {
        position: relative;
        display: block;
    }

    .sari-formal-dropdown > summary {
        list-style: none;
    }

    .sari-formal-dropdown > summary::-webkit-details-marker {
        display: none;
    }

    .sari-formal-trigger {
        display: flex !important;
        width: 100% !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 8px !important;
        padding: 0 12px !important;
        cursor: pointer !important;
        user-select: none;
        pointer-events: auto !important;
        text-align: left;
    }

    .sari-formal-trigger > * {
        pointer-events: none;
    }

    .sari-formal-trigger > span {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sari-formal-trigger svg {
        width: 14px;
        height: 14px;
        flex: 0 0 auto;
        fill: none;
        stroke: var(--sari-dark-a63aa16d7c, #737c87);
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
        transition: transform .16s ease, stroke .16s ease;
    }

    .sari-formal-dropdown[open] .sari-formal-trigger {
        border-color: #c88912 !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow: 0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(200,137,18,.08)) !important;
    }

    .sari-formal-dropdown[open] .sari-formal-trigger svg {
        transform: rotate(180deg);
        stroke: var(--sari-dark-6e784b4931, #9b6710);
    }

    .sari-formal-menu {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 6px);
        z-index: 190;
        max-height: 270px;
        overflow-y: auto;
        border: 1px solid var(--sari-dark-d8c2716225, #e1e5e9);
        border-radius: 12px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 5px;
        box-shadow:
            0 18px 42px var(--sari-dark-3e32d50f5e, rgba(31,41,55,.13)),
            0 4px 12px var(--sari-dark-25b2da5e49, rgba(31,41,55,.04));
        scrollbar-width: thin;
        scrollbar-color: #d9dde2 transparent;
    }

    .sari-formal-dropdown:not([open]) .sari-formal-menu {
        display: none !important;
    }

    .sari-formal-option {
        display: flex;
        width: 100%;
        min-height: 34px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        padding: 0 10px;
        color: var(--sari-dark-e23e64dd1e, #4c545e);
        font-size: 7.6px;
        font-weight: 600;
        line-height: 1.2;
        text-align: left;
        cursor: pointer;
        transition: background-color .13s ease, color .13s ease;
    }

    .sari-formal-option:hover,
    .sari-formal-option:focus-visible {
        background: var(--sari-dark-950baec030, #f5f6f8);
        color: var(--sari-dark-e23e64dd1e, #22282f);
        outline: none;
    }

    .sari-formal-option.is-selected {
        background: var(--sari-dark-4d21eb669b, #f7f3eb);
        color: var(--sari-dark-9b2ca2d764, #8e5d0e);
        font-weight: 750;
    }

    .sari-formal-check {
        display: none;
        flex: 0 0 auto;
        color: var(--sari-dark-6e784b4931, #9b6710);
        font-size: 8px;
        font-weight: 800;
    }

    .sari-formal-option.is-selected .sari-formal-check {
        display: inline;
    }


    /* ============================================================
       FINAL READABILITY PASS
       Stronger filter labels and values; benefits strip removed.
    ============================================================ */
    .sari-filter-label {
        color: var(--sari-dark-e23e64dd1e, #4f5964) !important;
        font-size: 7.2px !important;
        font-weight: 800 !important;
        letter-spacing: .07em !important;
        line-height: 1.2 !important;
    }

    .sari-live-search input.sari-filter-control {
        color: var(--sari-dark-e23e64dd1e, #2f3740) !important;
        font-size: 8.8px !important;
        font-weight: 550 !important;
    }

    .sari-live-search input.sari-filter-control::placeholder {
        color: var(--sari-dark-a63aa16d7c, #737d88) !important;
        opacity: 1 !important;
        font-weight: 500 !important;
    }

    .sari-formal-trigger,
    .sari-price-trigger {
        color: var(--sari-dark-e23e64dd1e, #303842) !important;
        font-size: 8.7px !important;
        font-weight: 700 !important;
    }

    .sari-formal-trigger > span,
    .sari-price-trigger > span,
    #buyerPriceTriggerText {
        color: var(--sari-dark-e23e64dd1e, #303842) !important;
        font-weight: 700 !important;
    }

    .sari-formal-option {
        min-height: 36px !important;
        color: var(--sari-dark-e23e64dd1e, #3f4852) !important;
        font-size: 8.2px !important;
        font-weight: 600 !important;
    }

    .sari-formal-option:hover,
    .sari-formal-option:focus-visible {
        color: var(--sari-dark-e23e64dd1e, #1f252c) !important;
    }

    .sari-formal-option.is-selected {
        color: var(--sari-dark-4b8b580e58, #89580c) !important;
        font-weight: 800 !important;
    }

    .sari-formal-check {
        color: var(--sari-dark-4b8b580e58, #89580c) !important;
        font-size: 8.5px !important;
    }

    .sari-price-field input {
        color: var(--sari-dark-e23e64dd1e, #303842) !important;
        font-size: 8.5px !important;
        font-weight: 600 !important;
    }

    .sari-price-field input::placeholder {
        color: var(--sari-dark-a63aa16d7c, #858e98) !important;
        opacity: 1 !important;
    }

    .sari-filter-reset {
        color: var(--sari-dark-9b2ca2d764, #865709) !important;
        font-size: 8.2px !important;
        font-weight: 800 !important;
    }

    .sari-result-pill {
        color: #fff !important;
        font-size: 8.5px !important;
        font-weight: 800 !important;
    }

    #toolbarResultCount {
        font-weight: 850 !important;
    }

    .sari-live-search > svg,
    .sari-formal-trigger svg,
    .sari-price-trigger svg {
        color: var(--sari-dark-a63aa16d7c, #596470) !important;
        stroke: var(--sari-dark-a63aa16d7c, #596470) !important;
    }


    /* Final SARI gold product-count pill */
    .sari-result-pill {
        border: 1px solid #d79a28 !important;
        background: linear-gradient(180deg, #e0a11a 0%, #c98408 100%) !important;
        color: #ffffff !important;
        box-shadow:
            0 7px 16px var(--sari-dark-b5c2a2270e, rgba(201,132,8,.16)),
            inset 0 1px 0 var(--sari-dark-3ab7594414, rgba(255,255,255,.18)) !important;
    }

    .sari-result-pill #toolbarResultCount {
        color: #ffffff !important;
        font-weight: 850 !important;
    }



    /* ============================================================
       FINAL OPEN FILTER + CATEGORY LAYOUT
       Removes the outer filter/category cards while preserving all
       search, dropdown, reset, count and category functionality.
    ============================================================ */

    /* Filter workspace: controls float directly on the page. */
    .sari-filter-dock {
        margin-top: 12px !important;
        overflow: visible !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
    }

    .sari-filter-grid {
        align-items: end !important;
    }

    /* Category row: remove the surrounding white card/container. */
    .sari-reference-category-row {
        margin-top: 12px !important;
        overflow: visible !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
    }

    /* Keep category choices as clean independent controls. */
    .sari-reference-category-row .sari-category-chips {
        gap: 8px !important;
    }

    /* Product result count: flat text, no gold gradient/pill. */
    .sari-result-pill {
        display: inline-flex !important;
        height: 42px !important;
        min-width: auto !important;
        align-items: center !important;
        justify-content: flex-start !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 4px !important;
        color: var(--sari-dark-a63aa16d7c, #59636f) !important;
        font-size: 8.8px !important;
        font-weight: 650 !important;
        box-shadow: none !important;
        white-space: nowrap !important;
    }

    .sari-result-pill #toolbarResultCount {
        color: var(--sari-dark-e23e64dd1e, #343d47) !important;
        font-weight: 800 !important;
    }

    /* Reset remains a text action instead of visually merging into a card. */
    .sari-filter-reset {
        border-color: transparent !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .sari-filter-reset:hover,
    .sari-filter-reset:focus-visible {
        border-color: transparent !important;
        background: transparent !important;
        color: var(--sari-dark-19b301f69a, #c88912) !important;
        box-shadow: none !important;
    }

    @media (max-width: 1279px) {
        .sari-reference-category-row {
            overflow-x: auto !important;
            padding-bottom: 2px !important;
        }
    }

    @media (max-width: 767px) {
        .sari-reference-category-row {
            margin-top: 9px !important;
            padding: 0 0 2px !important;
        }
    }



    /* ============================================================
       FINAL BUYER CATALOG — CART-STYLE HEADER + NO PRICE RANGE
    ============================================================ */
    .sari-products-page .sari-products-hero {
        padding: 2px 2px 14px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .sari-products-page .sari-products-hero p:first-child {
        margin: 0 !important;
        color: var(--sari-dark-685eb4fc40, #a87316) !important;
        font-size: 10.5px !important;
        font-weight: 800 !important;
        line-height: 1.2 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
    }

    .sari-products-page .sari-products-hero h1 {
        margin-top: 5px !important;
        color: var(--sari-dark-e23e64dd1e, #2e343b) !important;
        font-size: clamp(29px, 2.35vw, 34px) !important;
        font-weight: 760 !important;
        line-height: 1.04 !important;
        letter-spacing: -.035em !important;
    }

    .sari-products-page .sari-products-hero h1 span {
        color: var(--sari-dark-19b301f69a, #c88912) !important;
    }

    .sari-products-page .sari-products-hero h1 + p {
        max-width: 760px !important;
        margin-top: 7px !important;
        color: var(--sari-dark-a63aa16d7c, #7d8792) !important;
        font-size: 11.5px !important;
        font-weight: 450 !important;
        line-height: 1.55 !important;
    }

    .sari-products-page .sari-price-details,
    .sari-products-page .sari-price-popover {
        display: none !important;
    }

    .sari-products-page .sari-filter-grid {
        grid-template-columns:
            minmax(320px, 1.65fr)
            minmax(155px, .78fr)
            minmax(155px, .78fr)
            auto
            auto !important;
        gap: 10px !important;
        align-items: end !important;
    }

    .sari-products-page .sari-filter-label {
        font-size: 8.5px !important;
        font-weight: 750 !important;
        line-height: 1.2 !important;
        letter-spacing: .06em !important;
    }

    .sari-products-page .sari-live-search input.sari-filter-control,
    .sari-products-page .sari-formal-trigger {
        font-size: 10px !important;
        font-weight: 600 !important;
    }

    .sari-products-page .sari-formal-option {
        font-size: 9.5px !important;
        font-weight: 550 !important;
    }

    .sari-products-page .sari-filter-reset,
    .sari-products-page .sari-result-pill {
        font-size: 9.5px !important;
    }

    .sari-products-page .sari-reference-category-row .sari-inline-chip {
        font-size: 9px !important;
        font-weight: 650 !important;
    }

    @media (max-width: 1279px) {
        .sari-products-page .sari-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .sari-products-page .sari-live-search {
            grid-column: 1 / -1 !important;
        }
    }

    @media (max-width: 767px) {
        .sari-products-page .sari-products-hero p:first-child {
            font-size: 9.5px !important;
        }

        .sari-products-page .sari-products-hero h1 {
            font-size: 29px !important;
        }

        .sari-products-page .sari-products-hero h1 + p {
            font-size: 10.5px !important;
        }
    }



    /* ============================================================
       FINAL CATALOG HEADER + PURE WHITE SEARCH
    ============================================================ */
    .sari-products-page .sari-products-hero h1 + p {
        display: none !important;
    }

    .sari-products-page .sari-live-search input.sari-filter-control,
    .sari-products-page .sari-live-search input.sari-filter-control:hover,
    .sari-products-page .sari-live-search input.sari-filter-control:focus {
        background: var(--sari-dark-950baec030, #ffffff) !important;
        background-color: var(--sari-dark-950baec030, #ffffff) !important;
    }

    /*
     * Mobile filter drawer is explicitly controlled here instead of relying
     * only on generated utility classes. This prevents stale/malformed drawer
     * markup from ever leaking into the normal catalog document flow.
     */
    #mobileFilterDrawer[hidden] {
        display: none !important;
    }

    #mobileFilterDrawer {
        position: fixed;
        inset: 0;
        z-index: 90;
    }

    body.sari-mobile-filter-open {
        overflow: hidden;
    }

    @media (min-width: 1024px) {
        #mobileFilterDrawer {
            display: none !important;
        }
    }


    /* ============================================================
       ENTERPRISE CATALOG DISCOVERY RAIL
       Real catalog data, restrained surfaces, no decorative clutter.
    ============================================================ */
    .sari-catalog-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 22px;
        align-items: start;
    }

    .sari-catalog-primary {
        min-width: 0;
    }

    .sari-catalog-rail {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        min-width: 0;
    }

    .sari-insight-card {
        overflow: hidden;
        border: 1px solid var(--sari-dark-d8c2716225, #e2e5e9);
        border-radius: 14px;
        background: var(--sari-dark-950baec030, #ffffff);
        box-shadow: 0 8px 22px var(--sari-dark-7593018246, rgba(31, 41, 55, .035));
    }

    .sari-insight-card-head {
        display: flex;
        min-height: 52px;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 0 14px;
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #eceef1);
    }

    .sari-insight-heading {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 8px;
    }

    .sari-insight-heading-icon {
        display: inline-grid;
        width: 20px;
        height: 20px;
        flex: 0 0 auto;
        place-items: center;
        color: var(--sari-dark-19b301f69a, #c88912);
    }

    .sari-insight-heading-icon svg {
        width: 17px;
        height: 17px;
    }

    .sari-insight-heading h2 {
        margin: 0;
        color: var(--sari-dark-e23e64dd1e, #20252b);
        font-size: 11px;
        font-weight: 750;
        line-height: 1.2;
        letter-spacing: -.015em;
    }

    .sari-insight-link {
        display: inline-flex;
        min-height: 32px;
        flex: 0 0 auto;
        align-items: center;
        gap: 5px;
        border: 0;
        background: transparent;
        padding: 0;
        color: var(--sari-dark-6e784b4931, #a96f0e);
        font-size: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: color .16s ease;
    }

    .sari-insight-link:hover,
    .sari-insight-link:focus-visible {
        color: var(--sari-dark-9b2ca2d764, #7d5007);
        outline: none;
    }

    .sari-trending-list,
    .sari-popular-category-list {
        padding: 4px 14px 8px;
    }

    .sari-trending-item,
    .sari-popular-category-item {
        position: relative;
        display: flex;
        width: 100%;
        min-width: 0;
        align-items: center;
        gap: 10px;
        border: 0;
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #eef0f2);
        background: transparent;
        padding: 10px 0;
        color: inherit;
        text-align: left;
        text-decoration: none;
        cursor: pointer;
        transition: background-color .16s ease, color .16s ease;
    }

    .sari-trending-item:last-child,
    .sari-popular-category-item:last-child {
        border-bottom: 0;
    }

    .sari-trending-item:hover,
    .sari-trending-item:focus-visible,
    .sari-popular-category-item:hover,
    .sari-popular-category-item:focus-visible {
        outline: none;
    }

    .sari-trending-item:hover .sari-trending-copy strong,
    .sari-trending-item:focus-visible .sari-trending-copy strong,
    .sari-popular-category-item:hover .sari-popular-category-copy strong,
    .sari-popular-category-item:focus-visible .sari-popular-category-copy strong {
        color: var(--sari-dark-6e784b4931, #9d670c);
    }

    .sari-trending-thumb {
        display: grid;
        width: 54px;
        height: 54px;
        flex: 0 0 54px;
        overflow: hidden;
        place-items: center;
        border: 1px solid var(--sari-dark-d8c2716225, #e7e9ec);
        border-radius: 9px;
        background: var(--sari-dark-4d21eb669b, #f3f4f5);
        color: var(--sari-dark-a63aa16d7c, #8c9299);
        font-size: 11px;
        font-weight: 700;
    }

    .sari-trending-thumb img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .sari-trending-copy,
    .sari-popular-category-copy {
        display: block;
        min-width: 0;
        flex: 1 1 auto;
    }

    .sari-trending-copy strong,
    .sari-popular-category-copy strong {
        display: block;
        overflow: hidden;
        color: var(--sari-dark-e23e64dd1e, #2d3339);
        font-size: 9px;
        font-weight: 650;
        line-height: 1.3;
        text-overflow: ellipsis;
        white-space: nowrap;
        transition: color .16s ease;
    }

    .sari-trending-copy small,
    .sari-popular-category-copy small {
        display: block;
        margin-top: 2px;
        color: var(--sari-dark-a63aa16d7c, #8b939d);
        font-size: 7.5px;
        font-weight: 500;
        line-height: 1.3;
    }

    .sari-trending-copy b {
        display: block;
        margin-top: 4px;
        color: var(--sari-dark-af18afcaa4, #c17e08);
        font-size: 9px;
        font-weight: 750;
        line-height: 1.2;
    }

    .sari-trending-arrow,
    .sari-popular-category-arrow {
        width: 14px;
        height: 14px;
        flex: 0 0 auto;
        color: var(--sari-dark-a63aa16d7c, #a6adb5);
    }

    .sari-popular-category-icon {
        display: grid;
        width: 32px;
        height: 32px;
        flex: 0 0 32px;
        place-items: center;
        border: 1px solid var(--sari-dark-d8c2716225, #e7e9ec);
        border-radius: 9px;
        background: var(--sari-dark-950baec030, #f8f9fa);
        color: var(--sari-dark-a63aa16d7c, #5f6872);
    }

    .sari-popular-category-icon svg {
        width: 15px;
        height: 15px;
    }

    .sari-popular-category-item.is-active .sari-popular-category-icon {
        border-color: #e2c98f;
        background: var(--sari-dark-4d21eb669b, #fff8e9);
        color: var(--sari-dark-af18afcaa4, #b87808);
    }

    .sari-popular-category-item.is-active .sari-popular-category-copy strong {
        color: var(--sari-dark-6e784b4931, #9d670c);
    }

    .sari-insight-empty {
        margin: 0;
        padding: 14px 0;
        color: var(--sari-dark-a63aa16d7c, #8b939d);
        font-size: 8px;
        line-height: 1.55;
    }

    @media (min-width: 1280px) {
        .sari-catalog-layout {
            grid-template-columns: minmax(0, 1fr) 286px;
            gap: 22px;
        }

        .sari-catalog-rail {
            position: sticky;
            top: 92px;
            grid-template-columns: minmax(0, 1fr);
            align-self: start;
        }

        .sari-catalog-primary .sari-showcase-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }
    }

    @media (min-width: 1600px) {
        .sari-catalog-primary .sari-showcase-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 767px) {
        .sari-catalog-rail {
            grid-template-columns: minmax(0, 1fr);
        }

        .sari-insight-card-head {
            min-height: 50px;
        }
    }


    /* ============================================================
       BUYER CATALOG — READABLE DISCOVERY UI
       Scoped visual changes only: no filter, product or route logic.
    ============================================================ */

    /* Pure white search: remove cream fill and warm-colored border. */
    .sari-products-page .sari-live-search input.sari-filter-control,
    .sari-products-page .sari-live-search input.sari-filter-control:hover {
        background: var(--sari-dark-950baec030, #ffffff) !important;
        background-color: var(--sari-dark-950baec030, #ffffff) !important;
        border: 1px solid var(--sari-dark-d8c2716225, #dfe3e8) !important;
        color: var(--sari-dark-e23e64dd1e, #29323b) !important;
        font-size: 12px !important;
        font-weight: 550 !important;
        box-shadow: none !important;
    }

    .sari-products-page .sari-live-search input.sari-filter-control:focus {
        background: var(--sari-dark-950baec030, #ffffff) !important;
        background-color: var(--sari-dark-950baec030, #ffffff) !important;
        border-color: var(--sari-dark-d8c2716225, #9faab5) !important;
        box-shadow: 0 0 0 3px var(--sari-dark-8eadf403a5, rgba(75, 85, 99, .10)) !important;
    }

    .sari-products-page .sari-live-search input.sari-filter-control::placeholder {
        color: var(--sari-dark-a63aa16d7c, #79838e) !important;
        font-size: 11.5px !important;
        font-weight: 450 !important;
        opacity: 1 !important;
    }

    /* Search, Category and Sort By labels; selected dropdown values. */
    .sari-products-page .sari-filter-label {
        color: var(--sari-dark-e23e64dd1e, #4b5560) !important;
        font-size: 11px !important;
        font-weight: 750 !important;
        letter-spacing: .045em !important;
    }

    .sari-products-page .sari-formal-trigger,
    .sari-products-page .sari-formal-trigger > span {
        color: var(--sari-dark-e23e64dd1e, #333d47) !important;
        font-size: 12px !important;
        font-weight: 650 !important;
    }

    .sari-products-page .sari-formal-option {
        min-height: 39px !important;
        font-size: 11.5px !important;
        font-weight: 550 !important;
    }

    .sari-products-page .sari-filter-reset,
    .sari-products-page .sari-result-pill,
    .sari-products-page .sari-result-pill #toolbarResultCount {
        font-size: 11px !important;
    }

    /* More legible category names without changing category behavior. */
    .sari-products-page .sari-reference-category-row .sari-inline-chip {
        font-size: 11px !important;
        font-weight: 650 !important;
    }

    .sari-products-page .sari-reference-category-row {
        overflow-x: auto !important;
        overflow-y: hidden !important;
        scrollbar-width: thin;
    }

    .sari-products-page .sari-reference-category-row .sari-category-chips {
        width: max-content !important;
        min-width: max-content !important;
    }

    /* Larger, clear text in Trending Now and Popular Categories. */
    .sari-products-page .sari-insight-heading h2 {
        font-size: 14px !important;
        font-weight: 750 !important;
        line-height: 1.35 !important;
    }

    .sari-products-page .sari-insight-link {
        font-size: 11.5px !important;
        font-weight: 700 !important;
    }

    .sari-products-page .sari-trending-copy strong,
    .sari-products-page .sari-popular-category-copy strong {
        color: var(--sari-dark-e23e64dd1e, #29323b) !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        line-height: 1.45 !important;
        white-space: normal !important;
        display: -webkit-box !important;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        line-clamp: 2;
    }

    .sari-products-page .sari-trending-copy small,
    .sari-products-page .sari-popular-category-copy small {
        color: var(--sari-dark-a63aa16d7c, #707b86) !important;
        font-size: 10.5px !important;
        font-weight: 500 !important;
    }

    .sari-products-page .sari-trending-copy b {
        color: var(--sari-dark-6e784b4931, #a96f0e) !important;
        font-size: 13px !important;
        font-weight: 800 !important;
    }

    .sari-products-page .sari-insight-empty {
        font-size: 11px !important;
    }

    /* Keep live-search suggestions legible at the same scale. */
    .sari-products-page .sari-live-search-head strong,
    .sari-products-page .sari-live-search-footer {
        font-size: 10.5px !important;
    }

    .sari-products-page .sari-live-search-head span,
    .sari-products-page .sari-live-result-meta {
        font-size: 10px !important;
    }

    .sari-products-page .sari-live-result-name,
    .sari-products-page .sari-live-result-price {
        font-size: 11.5px !important;
    }

    .sari-products-page .sari-live-search-empty strong,
    .sari-products-page .sari-live-search-empty p {
        font-size: 11px !important;
    }



    /* ============================================================
       BUYER SHOP PRODUCTS — SELLER-STYLE FLOATING SURFACES
       Only catalog cards and standalone results panels are raised.
       Keep the header, filter controls, category navigation and any
       campaign/banner artwork open; no JS / Blade / backend changes.
    ============================================================ */

    /* Product component already has its own subtle surface. Use one
       consistent shadow instead of stacking a CSS drop-shadow on top. */
    .sari-products-page .sari-showcase-grid > article.sari-product-card {
        border: 1px solid var(--sari-dark-d8c2716225, #e5e7eb) !important;
        border-radius: 17px !important;
        background: var(--sari-dark-950baec030, #ffffff) !important;
        filter: none !important;
        box-shadow:
            0 2px 5px var(--sari-dark-1190f58155, rgba(15, 23, 42, .045)),
            0 10px 24px var(--sari-dark-e07575a086, rgba(15, 23, 42, .075)),
            0 24px 54px var(--sari-dark-a2f72dee3d, rgba(15, 23, 42, .095)),
            inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255, 255, 255, .92)) !important;
        transform: none !important;
    }

    @media (hover: hover) and (pointer: fine) {
        .sari-products-page .sari-showcase-grid > article.sari-product-card:hover {
            border-color: var(--sari-dark-d8c2716225, #d5dbe0) !important;
            box-shadow:
                0 2px 5px var(--sari-dark-1190f58155, rgba(15, 23, 42, .045)),
                0 10px 24px var(--sari-dark-e07575a086, rgba(15, 23, 42, .075)),
                0 24px 54px var(--sari-dark-a2f72dee3d, rgba(15, 23, 42, .095)),
                inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255, 255, 255, .92)) !important;
            transform: none !important;
        }
    }

    /* The two insight workspaces float as top-level cards. */
    .sari-products-page .sari-insight-card {
        border: 1px solid var(--sari-dark-d8c2716225, #e5e7eb) !important;
        border-radius: 17px !important;
        background: var(--sari-dark-950baec030, #ffffff) !important;
        box-shadow:
            0 2px 5px var(--sari-dark-1190f58155, rgba(15, 23, 42, .045)),
            0 10px 24px var(--sari-dark-e07575a086, rgba(15, 23, 42, .075)),
            0 24px 54px var(--sari-dark-a2f72dee3d, rgba(15, 23, 42, .095)),
            inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255, 255, 255, .92)) !important;
    }

    /* Related shops and the search-results notice are standalone
       containers; shop entries inside the related-shops panel stay flat. */
    .sari-products-page .sari-products-main > .sari-products-hero + section.mb-4,
    .sari-products-page .sari-products-main > section.mb-5,
    .sari-products-page .sari-products-main > section.lg\:hidden > div {
        border: 1px solid var(--sari-dark-d8c2716225, #e5e7eb) !important;
        border-radius: 17px !important;
        background: var(--sari-dark-950baec030, #ffffff) !important;
        box-shadow:
            0 1px 3px var(--sari-dark-7593018246, rgba(15, 23, 42, .035)),
            0 7px 18px var(--sari-dark-361b571631, rgba(15, 23, 42, .06)),
            0 15px 32px var(--sari-dark-4fc908fafe, rgba(15, 23, 42, .065)),
            inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255, 255, 255, .90)) !important;
    }

    .sari-products-page .sari-products-main > section.mb-5 > div:last-child > a {
        transform: none !important;
        box-shadow: none !important;
    }

    /* Only visible empty results need a standalone floating surface. */
    .sari-products-page #buyerProductEmpty:not(.hidden) {
        border: 1px solid var(--sari-dark-d8c2716225, #e5e7eb) !important;
        border-radius: 17px !important;
        background: var(--sari-dark-950baec030, #ffffff) !important;
        box-shadow:
            0 1px 3px var(--sari-dark-7593018246, rgba(15, 23, 42, .035)),
            0 7px 18px var(--sari-dark-361b571631, rgba(15, 23, 42, .06)),
            0 15px 32px var(--sari-dark-4fc908fafe, rgba(15, 23, 42, .065)),
            inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255, 255, 255, .90)) !important;
    }

    /* Preserve all existing grid columns, card sizes, banners and
       discovery-rail sticky behavior; allow shadows breathing room. */
    .sari-products-page .sari-showcase-grid {
        gap: 18px !important;
        margin-top: 16px !important;
    }

    .sari-products-page .sari-catalog-rail {
        gap: 18px;
    }

    @media (max-width: 767px) {
        .sari-products-page .sari-showcase-grid {
            gap: 14px !important;
            margin-top: 13px !important;
        }

        .sari-products-page .sari-catalog-rail {
            gap: 14px;
        }

        .sari-products-page .sari-showcase-grid > article.sari-product-card,
        .sari-products-page .sari-insight-card,
        .sari-products-page .sari-products-main > .sari-products-hero + section.mb-4,
        .sari-products-page .sari-products-main > section.mb-5,
        .sari-products-page .sari-products-main > section.lg\:hidden > div,
        .sari-products-page #buyerProductEmpty:not(.hidden) {
            border-radius: 14px !important;
            box-shadow:
                0 2px 4px var(--sari-dark-7593018246, rgba(15, 23, 42, .035)),
                0 8px 18px var(--sari-dark-361b571631, rgba(15, 23, 42, .06)),
                0 18px 38px var(--sari-dark-7c935c8886, rgba(15, 23, 42, .07)) !important;
        }
    }

    /* Keep the catalog filters in page flow and below the buyer header. */
    .sari-products-page .sari-filter-dock {
        position: relative !important;
        inset: auto !important;
        z-index: 0 !important;
        isolation: isolate;
    }

    /* Compact gold action aligned with the existing filter controls. */
    .sari-products-page #buyerApplyFilters {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        height: 40px; min-height: 40px; padding: 0 15px; border: 1px solid #c9870e;
        border-radius: 10px; background: #c9870e; color: #fff;
        font: inherit; font-size: 13px; font-weight: 700; cursor: pointer;
        align-self: end; white-space: nowrap;
        transition: background-color .15s ease, border-color .15s ease;
    }
    .sari-products-page #buyerApplyFilters svg { width: 16px; height: 16px; }
    .sari-products-page #buyerApplyFilters:hover { background: #ad720b; border-color: #ad720b; }
    .sari-products-page #buyerApplyFilters:focus-visible { outline: 3px solid #e9c56e; outline-offset: 3px; }
    .sari-products-page .sari-insight-heading-icon {
        width: 28px; height: 28px; flex: 0 0 28px;
        background: transparent; border: 0; box-shadow: none;
    }
    .sari-products-page .sari-insight-heading-icon svg { width: 28px; height: 28px; }

    /* Only product cards participate in catalog grid placement. */
    .sari-products-page #buyerProductGrid::before,
    .sari-products-page #buyerProductGrid::after {
        content: none !important;
        display: none !important;
    }
    .sari-products-page #buyerProductGrid > :not([data-product-card]) {
        display: none !important;
    }
    .sari-products-page #buyerProductGrid > [data-product-card] {
        min-width: 0;
        grid-column: auto !important;
        grid-row: auto !important;
        filter: none !important;
    }
    .sari-products-page #buyerProductGrid .sari-buyer-card-media,
    .sari-products-page #buyerProductGrid .sari-buyer-card-media-link,
    .sari-products-page #buyerProductGrid .sari-buyer-card-media-button,
    .sari-products-page #buyerProductGrid .sari-buyer-card-media > div.grid {
        background: #ffffff !important;
        background-image: none !important;
    }
    .sari-products-page #buyerProductGrid .sari-buyer-card-media img {
        background-color: #ffffff !important;
        filter: none !important;
        mix-blend-mode: normal !important;
    }
</style>
@endpush

@section('content')

@include('components.buyer.header')

@php


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
        <div class="min-w-0">
                <p class="text-[7px] font-bold uppercase tracking-[.18em] text-[#a87316]">Buyer Catalog</p>

                <h1 class="mt-2 text-[32px] font-bold leading-[1.02] tracking-[-.055em] text-[#17130f] sm:text-[40px] lg:text-[47px]">
                    Shop <span class="text-[#c2810d]">Products</span>
                </h1>

        </div>
</section>
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

        <div class="sari-catalog-layout">
            <div class="sari-catalog-primary min-w-0">

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
                        placeholder="Try “black shirt large”, “shoes under 500”, brand, category, or seller..."
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
                            <strong>Smart search</strong>
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
                    <span class="sari-filter-label">Category</span>

                    <select id="buyerCategorySelect" class="sr-only" tabindex="-1" aria-hidden="true">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category['name'] }}">{{ $category['name'] }}</option>
                        @endforeach
                    </select>

                    <details id="buyerCategoryDetails" class="sari-formal-dropdown">
                        <summary class="sari-filter-control sari-formal-trigger">
                            <span id="buyerCategoryLabel">All Categories</span>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 10 5 5 5-5"></path></svg>
                        </summary>

                        <div class="sari-formal-menu" role="listbox" aria-label="Category">
                            <button type="button" data-formal-select="buyerCategorySelect" data-formal-value="" class="sari-formal-option is-selected">
                                <span>All Categories</span><span class="sari-formal-check">✓</span>
                            </button>
                            @foreach ($categories as $category)
                                <button type="button" data-formal-select="buyerCategorySelect" data-formal-value="{{ $category['name'] }}" class="sari-formal-option">
                                    <span>{{ $category['name'] }}</span><span class="sari-formal-check">✓</span>
                                </button>
                            @endforeach
                        </div>
                    </details>
                </div>


                <div class="min-w-0">
                    <span class="sari-filter-label">Sort By</span>

                    <select id="buyerSort" class="sr-only" tabindex="-1" aria-hidden="true">
                        <option value="featured">Featured</option>
                        <option value="latest">Newest</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="rating">Highest Rated</option>
                        <option value="sold">Most Sold</option>
                    </select>

                    <details id="buyerSortDetails" class="sari-formal-dropdown">
                        <summary class="sari-filter-control sari-formal-trigger">
                            <span id="buyerSortLabel">Featured</span>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 10 5 5 5-5"></path></svg>
                        </summary>

                        <div class="sari-formal-menu" role="listbox" aria-label="Sort products">
                            <button type="button" data-formal-select="buyerSort" data-formal-value="featured" class="sari-formal-option is-selected">
                                <span>Featured</span><span class="sari-formal-check">✓</span>
                            </button>
                            <button type="button" data-formal-select="buyerSort" data-formal-value="latest" class="sari-formal-option">
                                <span>Newest</span><span class="sari-formal-check">✓</span>
                            </button>
                            <button type="button" data-formal-select="buyerSort" data-formal-value="price-low" class="sari-formal-option">
                                <span>Price: Low to High</span><span class="sari-formal-check">✓</span>
                            </button>
                            <button type="button" data-formal-select="buyerSort" data-formal-value="price-high" class="sari-formal-option">
                                <span>Price: High to Low</span><span class="sari-formal-check">✓</span>
                            </button>
                            <button type="button" data-formal-select="buyerSort" data-formal-value="rating" class="sari-formal-option">
                                <span>Highest Rated</span><span class="sari-formal-check">✓</span>
                            </button>
                            <button type="button" data-formal-select="buyerSort" data-formal-value="sold" class="sari-formal-option">
                                <span>Most Sold</span><span class="sari-formal-check">✓</span>
                            </button>
                        </div>
                    </details>
                </div>

                <button id="buyerApplyFilters" type="button" class="sari-filter-apply">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path d="M4 6h16M7 12h10M10 18h4"></path>
                    </svg>
                    Apply
                </button>
                <button id="clearAllFilters" type="button" hidden aria-hidden="true" tabindex="-1"></button>

                <span class="sari-result-pill">
                    <span id="toolbarResultCount">{{ count($products) }}</span>&nbsp;products
                </span>
            </div>

            <div class="sari-active-filters">
                <span id="activeCategoryBadge" class="hidden"></span>
                <div id="variationFilters"></div>
            </div>
        </div>


        {{-- Price Range UI removed. Hidden compatibility fields preserve existing JS hooks safely. --}}
        <div class="hidden" aria-hidden="true">
            <input id="minPrice" type="hidden" value="">
            <input id="maxPrice" type="hidden" value="">
            <input id="mobileMinPrice" type="hidden" value="">
            <input id="mobileMaxPrice" type="hidden" value="">
        </div>

        {{-- Hidden buttons keep buyer-products.js category behavior intact. --}}
        <div class="hidden" aria-hidden="true">
            <button type="button" data-filter-category=""></button>
            @foreach ($categories as $category)
                <button type="button" data-filter-category="{{ $category['name'] }}"></button>
            @endforeach
        </div>

        {{-- Product grid --}}
        <div id="buyerProductGrid" class="sari-showcase-grid">
            @foreach ($products as $product)
                <x-buyer.product-card
                    :product="$product"
                    :priority="$loop->index < 4"
                    :interactive="true"
                    :show-promotions="true"
                    :show-stock="true"
                    :show-quick-add="true"
                />
            @endforeach
        </div>

        <div id="buyerProductEmpty" class="mt-5 hidden px-6 py-14 text-center">
            <svg viewBox="0 0 24 24" class="mx-auto block h-7 w-7 text-[#8f99a3]" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path>
            </svg>
            <p class="mt-4 text-[11px] font-semibold text-[#4f473e]">No products match your filters</p>
            <p class="mt-1 text-[9px] text-[#958c80]">Try another category, search term, sort option, or product selection.</p>
            <div id="buyerZeroResultSuggestions" class="mt-4 flex flex-wrap justify-center gap-2"></div>
            <button id="clearProductFilters" type="button" class="mt-4 inline-flex h-10 items-center justify-center rounded-[9px] bg-[#c9870e] px-4 text-[9px] font-semibold text-white transition hover:bg-[#ae7008]">
                Clear Filters
            </button>
        </div>

        <div class="sr-only">
            Showing <span id="buyerResultCount">{{ count($products) }}</span> products
            <button id="showAllProductsBottom" type="button">Reset Filters</button>
        </div>
            </div>

            <aside class="sari-catalog-rail" aria-label="Catalog discovery">
                <section class="sari-insight-card" aria-labelledby="buyerTrendingTitle">
                    <div class="sari-insight-card-head">
                        <div class="sari-insight-heading">
                            <span class="sari-insight-heading-icon" aria-hidden="true">
                                <svg viewBox="0 0 32 32" fill="none" aria-hidden="true">
                                    <path d="M4 27V18h5v9" fill="#e5c47b" stroke="#a97413" stroke-width="1.3"/>
                                    <path d="M13 27V13h5v14" fill="#dba834" stroke="#a97413" stroke-width="1.3"/>
                                    <path d="M22 27V7h5v20" fill="#c9870e" stroke="#a97413" stroke-width="1.3"/>
                                    <path d="m4 13 9-6 6 2 9-7m-6 0h6v6" stroke="#685027" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M3 28h26" stroke="#64748b" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <h2 id="buyerTrendingTitle">Trending Now</h2>
                        </div>
                    </div>

                    <div class="sari-trending-list">
                        @forelse ($trendingProducts ?? [] as $trendingProduct)
                            <a
                                href="{{ route('buyer.product.details', ['product' => $trendingProduct['id']]) }}"
                                class="sari-trending-item"
                                aria-label="View {{ $trendingProduct['name'] }}"
                            >
                                <span class="sari-trending-thumb">
                                    @if (!empty($trendingProduct['image']))
                                        <img src="{{ str_starts_with(ltrim($trendingProduct['image'], '/'), 'buyer/') ? '/' . ltrim($trendingProduct['image'], '/') : asset($trendingProduct['image']) }}" alt="" loading="lazy" decoding="async">
                                    @else
                                        <span aria-hidden="true">S</span>
                                    @endif
                                </span>
                                <span class="sari-trending-copy">
                                    <strong>{{ $trendingProduct['name'] }}</strong>
                                    <small>{{ $trendingProduct['category'] ?? 'General' }}</small>
                                    <b>₱{{ number_format((float) ($trendingProduct['price'] ?? 0), 2) }}</b>
                                </span>
                                <svg class="sari-trending-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="m9 18 6-6-6-6"></path>
                                </svg>
                            </a>
                        @empty
                            <p class="sari-insight-empty">Products will appear here as catalog activity grows.</p>
                        @endforelse
                    </div>
                </section>

                <section class="sari-insight-card" aria-labelledby="buyerPopularCategoriesTitle">
                    <div class="sari-insight-card-head">
                        <div class="sari-insight-heading">
                            <span class="sari-insight-heading-icon" aria-hidden="true">
                                <svg viewBox="0 0 32 32" fill="none" aria-hidden="true">
                                    <path d="m3 12 9-5 9 5-9 5-9-5Z" fill="#f0d79d" stroke="#a97413" stroke-width="1.3" stroke-linejoin="round"/>
                                    <path d="M3 12v11l9 5V17L3 12Z" fill="#dba834" stroke="#a97413" stroke-width="1.3" stroke-linejoin="round"/>
                                    <path d="m12 17 9-5v11l-9 5V17Z" fill="#c9870e" stroke="#a97413" stroke-width="1.3" stroke-linejoin="round"/>
                                    <path d="m8 9 9 5v5" stroke="#fff5d9" stroke-width="2"/>
                                    <path d="m21 4 7 4v8l-7 4V4Z" fill="#e2e8f0" stroke="#64748b" stroke-width="1.3" stroke-linejoin="round"/>
                                    <path d="m14 8 7-4 7 4-7 4-7-4Z" fill="#f1f5f9" stroke="#64748b" stroke-width="1.3" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <h2 id="buyerPopularCategoriesTitle">Popular Categories</h2>
                        </div>
                    </div>

                    <div class="sari-popular-category-list">
                        @forelse ($popularCategories ?? [] as $popularCategory)
                            <button
                                type="button"
                                class="sari-popular-category-item"
                                data-popular-category="{{ $popularCategory['name'] }}"
                                aria-label="Filter products by {{ $popularCategory['name'] }}"
                            >
                                <span class="sari-popular-category-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <rect x="4" y="4" width="6" height="6" rx="1.2"></rect>
                                        <rect x="14" y="4" width="6" height="6" rx="1.2"></rect>
                                        <rect x="4" y="14" width="6" height="6" rx="1.2"></rect>
                                        <rect x="14" y="14" width="6" height="6" rx="1.2"></rect>
                                    </svg>
                                </span>
                                <span class="sari-popular-category-copy">
                                    <strong>{{ $popularCategory['name'] }}</strong>
                                    <small>{{ $popularCategory['count'] }} {{ $popularCategory['count'] === 1 ? 'product' : 'products' }}</small>
                                </span>
                                <svg class="sari-popular-category-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="m9 18 6-6-6-6"></path>
                                </svg>
                            </button>
                        @empty
                            <p class="sari-insight-empty">Categories will appear when products are available.</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>

    </section>

</main>
</div>

{{-- =========================================================
     MOBILE FILTER DRAWER
========================================================= --}}
<div
    id="mobileFilterDrawer"
    class="sari-products-page"
    hidden
    aria-hidden="true"
>
    <div id="mobileFilterOverlay" class="absolute inset-0 bg-black/30 backdrop-blur-[1px]"></div>

    <aside
        class="absolute right-0 top-0 flex h-full w-[340px] max-w-[90vw] flex-col border-l border-[#eee4d3] bg-white shadow-[0_24px_70px_rgba(44,34,22,0.15)]"
        role="dialog"
        aria-modal="true"
        aria-labelledby="mobileFilterTitle"
    >
        <div class="flex min-h-[76px] items-center justify-between border-b border-[#eee4d3] px-5">
            <div>
                <p class="text-[8px] font-semibold uppercase tracking-[0.12em] text-[#a8731f]">Product Filters</p>
                <h2 id="mobileFilterTitle" class="mt-1 text-[15px] font-semibold text-[#28221b]">Narrow Results</h2>
            </div>

            <button
                id="closeMobileFilters"
                type="button"
                class="grid h-10 w-10 place-items-center rounded-xl border border-[#e8dfd0] bg-white text-[#6e6558] transition hover:bg-[#f7eedf] hover:text-[#b97805]"
                aria-label="Close product filters"
            >
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M6 6l12 12"></path>
                    <path d="M18 6 6 18"></path>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5">
            <div class="border-b border-[#eee8df] pb-5">
                <p class="text-[10px] font-semibold text-[#403930]">Category</p>

                <div class="mt-3 space-y-1">
                    <button type="button" data-filter-category-mobile="" class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-[9px] transition hover:bg-[#fcf8f1]">
                        <span>All Categories</span>
                        <span data-category-check-mobile="" class="hidden text-[#a8731f]">✓</span>
                    </button>

                    @foreach ($categories as $category)
                        <button type="button" data-filter-category-mobile="{{ $category['name'] }}" class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-[9px] transition hover:bg-[#fcf8f1]">
                            <span>{{ $category['name'] }}</span>
                            <span data-category-check-mobile="{{ $category['name'] }}" class="hidden text-[#a8731f]">✓</span>
                        </button>
                    @endforeach
                </div>
            </div>

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

<x-buyer.products-page-data
    :products="$products"
    :search-query="$searchQuery ?? request('search', '')"
/>


<script>
(function () {
    function initSariFilterDock() {
        const categorySelect = document.getElementById('buyerCategorySelect');
        const hiddenCategoryButtons = Array.from(document.querySelectorAll('[data-filter-category]'));
        const categoryChips = Array.from(document.querySelectorAll('[data-shop-category-chip]'));
        const popularCategoryButtons = Array.from(document.querySelectorAll('[data-popular-category]'));

        const minPrice = document.getElementById('minPrice');
        const maxPrice = document.getElementById('maxPrice');

        const clearAll = document.getElementById('clearAllFilters');
        const applyFiltersButton = document.getElementById('buyerApplyFilters');
        if (applyFiltersButton && applyFiltersButton.dataset.bound !== '1') {
            applyFiltersButton.dataset.bound = '1';
            applyFiltersButton.addEventListener('click', () => {
                categorySelect?.dispatchEvent(new Event('change', { bubbles: true }));
                document.getElementById('buyerSort')?.dispatchEvent(new Event('change', { bubbles: true }));
                document.querySelectorAll('.sari-formal-dropdown[open]').forEach((menu) => menu.removeAttribute('open'));
                document.getElementById('buyerLiveSearchViewAll')?.click();
            });
        }

        const showAll = document.getElementById('showAllProducts');

        const formalDropdowns = Array.from(document.querySelectorAll('.sari-formal-dropdown'));

        const mobileFilterDrawer = document.getElementById('mobileFilterDrawer');
        const openMobileFilters = document.getElementById('openMobileFilters');
        const closeMobileFilters = document.getElementById('closeMobileFilters');
        const mobileFilterOverlay = document.getElementById('mobileFilterOverlay');
        const mobileClearFilters = document.getElementById('mobileClearFilters');
        const applyMobileFilters = document.getElementById('applyMobileFilters');
        const mobileFilterSummary = document.getElementById('mobileFilterSummary');
        const mobileFilterCount = document.getElementById('mobileFilterCount');
        const mobileCategoryButtons = Array.from(document.querySelectorAll('[data-filter-category-mobile]'));

        function syncFormalDropdown(selectId) {
            const select = document.getElementById(selectId);
            if (!select) return;

            const details = document.querySelector(`.sari-formal-dropdown [data-formal-select="${selectId}"]`)?.closest('.sari-formal-dropdown');
            if (!details) return;

            const selectedOption = Array.from(select.options).find(
                (option) => String(option.value) === String(select.value)
            );

            const label = details.querySelector('.sari-formal-trigger > span');
            if (label) label.textContent = selectedOption?.textContent?.trim() || 'Select';

            details.querySelectorAll(`[data-formal-select="${selectId}"]`).forEach((button) => {
                const selected = String(button.dataset.formalValue ?? '') === String(select.value ?? '');
                button.classList.toggle('is-selected', selected);
            });
        }

        function bindFormalDropdown(selectId) {
            const select = document.getElementById(selectId);
            if (!select) return;

            const buttons = Array.from(document.querySelectorAll(`[data-formal-select="${selectId}"]`));
            if (!buttons.length) return;

            buttons.forEach((button) => {
                if (button.dataset.bound === '1') return;
                button.dataset.bound = '1';

                button.addEventListener('click', function () {
                    select.value = String(this.dataset.formalValue ?? '');
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    syncFormalDropdown(selectId);

                    this.closest('.sari-formal-dropdown')?.removeAttribute('open');
                });
            });

            if (select.dataset.formalSyncBound !== '1') {
                select.dataset.formalSyncBound = '1';
                select.addEventListener('change', () => syncFormalDropdown(selectId));
            }

            syncFormalDropdown(selectId);
        }

        ['buyerCategorySelect', 'buyerSort'].forEach(bindFormalDropdown);

        formalDropdowns.forEach((details) => {
            if (details.dataset.toggleBound === '1') return;
            details.dataset.toggleBound = '1';

            details.addEventListener('toggle', function () {
                if (!this.open) return;

                document.querySelectorAll('.sari-formal-dropdown[open]').forEach((other) => {
                    if (other !== this) other.removeAttribute('open');
                });
            });
        });

        function syncMobileFilterUi() {
            const selectedCategory = String(categorySelect?.value || '');

            mobileCategoryButtons.forEach((button) => {
                const selected = String(button.dataset.filterCategoryMobile || '') === selectedCategory;
                button.classList.toggle('bg-[#fcf8f1]', selected);
                button.classList.toggle('font-semibold', selected);

                const check = button.querySelector('[data-category-check-mobile]');
                check?.classList.toggle('hidden', !selected);
            });

            const activeCount = Number(Boolean(selectedCategory));
            if (mobileFilterCount) {
                mobileFilterCount.textContent = String(activeCount);
                mobileFilterCount.classList.toggle('hidden', activeCount === 0);
            }

            if (mobileFilterSummary) {
                mobileFilterSummary.textContent = selectedCategory || 'All Products';
            }
        }

        function setMobileFilterDrawerOpen(open) {
            if (!mobileFilterDrawer) return;

            const shouldOpen = Boolean(open) && window.matchMedia('(max-width: 1023px)').matches;
            mobileFilterDrawer.hidden = !shouldOpen;
            mobileFilterDrawer.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');
            document.body.classList.toggle('sari-mobile-filter-open', shouldOpen);

            if (shouldOpen) {
                syncMobileFilterUi();
                window.requestAnimationFrame(() => closeMobileFilters?.focus());
            } else {
                openMobileFilters?.focus({ preventScroll: true });
            }
        }

        function syncPopularCategoryUi() {
            const selectedCategory = String(categorySelect?.value || '');

            popularCategoryButtons.forEach((button) => {
                const selected = String(button.dataset.popularCategory || '') === selectedCategory;
                button.classList.toggle('is-active', selected);
                button.setAttribute('aria-pressed', selected ? 'true' : 'false');
            });
        }

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

            syncMobileFilterUi();
            syncPopularCategoryUi();
        }

        categorySelect?.addEventListener('change', function () {
            chooseCategory(this.value);
        });

        categoryChips.forEach((chip) => {
            chip.addEventListener('click', function () {
                chooseCategory(this.dataset.shopCategoryChip || '');
            });
        });

        popularCategoryButtons.forEach((button) => {
            if (button.dataset.bound === '1') return;
            button.dataset.bound = '1';

            button.addEventListener('click', function () {
                chooseCategory(this.dataset.popularCategory || '');
                document.getElementById('buyerProductGrid')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        if (mobileFilterDrawer && mobileFilterDrawer.dataset.bound !== '1') {
            mobileFilterDrawer.dataset.bound = '1';

            openMobileFilters?.addEventListener('click', () => setMobileFilterDrawerOpen(true));
            closeMobileFilters?.addEventListener('click', () => setMobileFilterDrawerOpen(false));
            mobileFilterOverlay?.addEventListener('click', () => setMobileFilterDrawerOpen(false));
            applyMobileFilters?.addEventListener('click', () => setMobileFilterDrawerOpen(false));

            mobileCategoryButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    chooseCategory(button.dataset.filterCategoryMobile || '');
                });
            });

            mobileClearFilters?.addEventListener('click', () => {
                clearAll?.click();
                syncMobileFilterUi();
            });

            mobileFilterDrawer.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') setMobileFilterDrawerOpen(false);
            });
        }

        syncMobileFilterUi();
        syncPopularCategoryUi();


        clearAll?.addEventListener('click', function () {
            if (categorySelect) {
                categorySelect.value = '';
                categorySelect.dispatchEvent(new Event('change', { bubbles: true }));
            }

            const sortSelect = document.getElementById('buyerSort');
            if (sortSelect) {
                sortSelect.value = 'featured';
                sortSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
            if (minPrice) minPrice.value = '';
            if (maxPrice) maxPrice.value = '';

            categoryChips.forEach((chip) => {
                chip.classList.toggle(
                    'is-active',
                    String(chip.dataset.shopCategoryChip || '') === ''
                );
            });

            syncMobileFilterUi();
        });

        showAll?.addEventListener('click', function () {
            chooseCategory('');
        });

        document.addEventListener('click', function (event) {


            document.querySelectorAll('.sari-formal-dropdown[open]').forEach((details) => {
                if (!details.contains(event.target)) details.removeAttribute('open');
            });
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
    <script>
    (() => {
        function initBuyerCatalogGrid() {
            const grid = document.getElementById('buyerProductGrid');
            if (window.__sariCatalogGridObserver) {
                window.__sariCatalogGridObserver.disconnect();
                window.__sariCatalogGridObserver = null;
            }
            if (!grid) return;

            // BOM / zero-width text can become anonymous grid items. Keep the
            // actual card nodes in place so catalog filtering and sorting work.
            const removeSpacerText = () => {
                Array.from(grid.childNodes).forEach(node => {
                    if (node.nodeType === 3 && /^[\s\uFEFF\u200B]*$/.test(node.textContent || '')) {
                        node.remove();
                    }
                });
            };

            removeSpacerText();
            const observer = new MutationObserver(removeSpacerText);
            observer.observe(grid, { childList: true });
            window.__sariCatalogGridObserver = observer;
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initBuyerCatalogGrid, { once: true });
        } else {
            initBuyerCatalogGrid();
        }
        document.addEventListener('livewire:navigated', initBuyerCatalogGrid);
    })();
    </script>
    @vite('resources/js/buyer-products.js')
@endpush
