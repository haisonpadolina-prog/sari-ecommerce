@extends('layouts.logistics')

@section('title', 'Delivery Monitoring — SARI Logistics')
@section('page-title', 'Delivery Monitoring')

@push('styles')
<style>
/* =========================================================
   MERGED FROM public/css/logistics/delivery-monitoring.css
   Kept page-scoped/shared exactly as the original stylesheet.
========================================================= */
/*
|--------------------------------------------------------------------------
| SARI LOGISTICS — DELIVERY MONITORING
|--------------------------------------------------------------------------
|
| Seller-inspired end-to-end parcel operations dashboard.
|
*/


/* ==========================================================================
   TOKENS
   ========================================================================== */

.monitor-page {
    --monitor-ink: #302a23;
    --monitor-soft: #51483d;

    --monitor-muted: #8f877d;
    --monitor-muted-light: #aaa198;

    --monitor-line: #e8e2da;
    --monitor-line-soft: #f0ebe4;

    --monitor-gold: #d89b10;
    --monitor-gold-dark: #b97805;

    --monitor-green: #327a53;
    --monitor-green-soft: #f1faf5;

    --monitor-blue: #4f7899;
    --monitor-blue-soft: #f1f6fb;

    --monitor-red: #b75b55;
    --monitor-red-soft: #fff5f4;

    width: 100%;
    max-width: 1800px;

    margin-inline: auto;
    padding-bottom: 26px;

    color: var(--monitor-ink);

    font-family:
        "Poppins",
        ui-sans-serif,
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;

    font-size: 11px;
    line-height: 1.55;
}


.monitor-page *,
.monitor-page *::before,
.monitor-page *::after {
    box-sizing: border-box;
}


.monitor-page [hidden] {
    display: none !important;
}


.monitor-page button,
.monitor-page input {
    font: inherit;
}


.monitor-page button {
    cursor: pointer;
}


.monitor-page :is(
    button,
    a,
    input
):focus-visible {
    outline:
        2px solid rgba(216, 155, 16, .72);

    outline-offset: 2px;
}


/* ==========================================================================
   HERO
   ========================================================================== */

.monitor-page-header {
    position: relative;

    display: flex;
    align-items: center;
    justify-content: space-between;

    min-height: 116px;

    gap: 24px;

    margin-bottom: 14px;
    padding: 17px 20px;

    overflow: hidden;

    border: 1px solid #2f2a23;
    border-radius: 19px;

    background:
        radial-gradient(
            circle at 100% 50%,
            rgba(216, 155, 16, .13) 0,
            rgba(216, 155, 16, .06) 21%,
            transparent 42%
        ),
        linear-gradient(
            135deg,
            #211f1c 0%,
            #25231f 48%,
            #1b1917 100%
        );

    box-shadow:
        0 8px 22px
        rgba(20, 17, 13, .08);
}


.monitor-page-header::after {
    content: "";

    position: absolute;

    width: 190px;
    height: 190px;

    right: -72px;
    top: 50%;

    transform: translateY(-50%);

    border:
        1px solid rgba(216, 155, 16, .12);

    border-radius: 999px;

    box-shadow:
        0 0 0 24px rgba(216, 155, 16, .035),
        0 0 0 50px rgba(216, 155, 16, .018);

    pointer-events: none;
}


.monitor-page-header > * {
    position: relative;
    z-index: 1;
}


.monitor-eyebrow {
    margin: 0 0 5px;

    color: #ab8950;

    font-size: 7px;
    line-height: 1.2;
    font-weight: 600;

    letter-spacing: .15em;
    text-transform: uppercase;
}


.monitor-page-header
.monitor-eyebrow {
    color:
        rgba(226, 161, 15, .92);
}


.monitor-page-header h1 {
    margin: 0;

    color: #ffffff;

    font-size:
        clamp(19px, 1.55vw, 23px);

    line-height: 1.12;
    font-weight: 600;

    letter-spacing: -.038em;
}


.monitor-page-header h1 span {
    color: #e2a10f;
}


.monitor-subtitle {
    max-width: 720px;

    margin: 6px 0 0;

    color:
        rgba(255, 255, 255, .57);

    font-size: 8.5px;
    line-height: 1.65;
}


.monitor-header-actions {
    display: flex;
    align-items: center;

    gap: 8px;
}


/* ==========================================================================
   BUTTONS
   ========================================================================== */

.monitor-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-height: 38px;

    gap: 8px;

    padding: 8px 14px;

    border: 1px solid #dfd7cc;
    border-radius: 10px;

    background: #ffffff;

    color: #62594f;

    font-size: 8px;
    line-height: 1;
    font-weight: 600;

    text-decoration: none;
    white-space: nowrap;

    transition:
        background-color .16s ease,
        border-color .16s ease,
        color .16s ease,
        transform .16s ease;
}


.monitor-button:hover {
    transform: translateY(-1px);

    border-color: #d7c49c;

    background: #fffaf1;

    color: #946611;
}


.monitor-button-primary {
    border-color:
        var(--monitor-gold);

    background:
        var(--monitor-gold);

    color: #ffffff;

    box-shadow:
        0 5px 13px
        rgba(215, 144, 4, .14);
}


.monitor-button-primary:hover {
    border-color: #c68908;

    background: #c68908;

    color: #ffffff;
}


.monitor-page-header
.monitor-button:not(
    .monitor-button-primary
) {
    border-color:
        rgba(255, 255, 255, .13);

    background:
        rgba(255, 255, 255, .055);

    color:
        rgba(255, 255, 255, .82);
}


/* ==========================================================================
   STATS
   ========================================================================== */

.monitor-stats {
    display: grid;

    grid-template-columns:
        repeat(5, minmax(0, 1fr));

    gap: 10px;
}


.monitor-stat {
    position: relative;

    min-width: 0;

    padding: 14px 15px 13px;

    border:
        1px solid var(--monitor-line);

    border-radius: 16px;

    background: #ffffff;

    box-shadow:
        0 5px 16px
        rgba(39, 31, 22, .025);
}


.monitor-stat::after {
    content: "";

    position: absolute;

    right: 13px;
    top: 13px;

    width: 6px;
    height: 6px;

    border-radius: 999px;

    background: #a79e93;
}


.monitor-stat p {
    margin: 0;

    color: #81786e;

    font-size: 7px;
    font-weight: 600;

    letter-spacing: .085em;
    text-transform: uppercase;
}


.monitor-stat strong {
    display: block;

    margin: 5px 0 2px;

    color: #302a23;

    font-size: 23px;
    line-height: 1.12;
    font-weight: 600;

    letter-spacing: -.035em;
}


.monitor-stat span {
    display: block;

    color: #978f85;

    font-size: 7px;
}


.monitor-stat-amber {
    border-color: #eadfc9;
}


.monitor-stat-amber::after {
    background: #d89b10;
}


.monitor-stat-amber strong {
    color: #ad7713;
}


.monitor-stat-blue {
    border-color: #d7e3ed;
}


.monitor-stat-blue::after {
    background: #6c8fb5;
}


.monitor-stat-blue strong {
    color: #587b99;
}


.monitor-stat-green {
    border-color: #d5e5da;
}


.monitor-stat-green::after {
    background: #63a375;
}


.monitor-stat-green strong {
    color: #4f7c5b;
}


/* ==========================================================================
   FLOW
   ========================================================================== */

.monitor-flow {
    display: flex;
    align-items: center;

    width: fit-content;
    max-width: 100%;

    gap: 4px;

    margin: 13px 0;
    padding: 5px;

    border:
        1px solid var(--monitor-line);

    border-radius: 11px;

    background: #ffffff;
}


.monitor-flow :is(a, span) {
    display: inline-flex;
    align-items: center;

    min-height: 31px;

    gap: 7px;

    padding: 4px 9px;

    border-radius: 8px;

    color: #938a80;

    font-size: 7.5px;
    font-weight: 500;

    text-decoration: none;
    white-space: nowrap;
}


.monitor-flow a:hover {
    background: #faf7f1;

    color: #796647;
}


.monitor-flow b {
    display: grid;

    width: 20px;
    height: 20px;

    place-items: center;

    border: 1px solid #e5ded5;
    border-radius: 999px;

    background: #ffffff;

    color: #948a7f;

    font-size: 6.5px;
}


.monitor-flow .is-current {
    background: #fff6df;

    color: #9c6a0e;

    font-weight: 600;
}


.monitor-flow .is-current b {
    border-color: #e4c681;

    background: #e3a41a;

    color: #ffffff;
}


/* ==========================================================================
   WORKSPACE
   ========================================================================== */

.monitor-workspace {
    overflow: hidden;

    border:
        1px solid var(--monitor-line);

    border-radius: 19px;

    background: #ffffff;

    box-shadow:
        0 6px 18px
        rgba(33, 24, 14, .03);
}


.monitor-delivered-workspace {
    margin-top: 14px;
}


/* ==========================================================================
   TOOLBAR
   ========================================================================== */

.monitor-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 18px;

    padding: 16px 18px 13px;
}


.monitor-toolbar h2 {
    margin: 0;

    color: #302a23;

    font-size: 13px;
    font-weight: 600;
}


.monitor-toolbar p {
    margin: 3px 0 0;

    color: var(--monitor-muted);

    font-size: 7.5px;
}


.monitor-search {
    width: min(310px, 100%);

    flex-shrink: 0;
}


.monitor-search input {
    width: 100%;
    height: 36px;

    padding: 8px 11px;

    border: 1px solid #e5ded5;
    border-radius: 9px;

    background: #ffffff;

    color: var(--monitor-ink);

    font-size: 7.5px;

    outline: none;
}


.monitor-search input::placeholder {
    color: #aaa198;
}


.monitor-search input:focus {
    border-color: #d6b46d;

    box-shadow:
        0 0 0 4px
        rgba(216, 155, 16, .075);
}


/* ==========================================================================
   FILTERS
   ========================================================================== */

.monitor-filter-row {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 12px;

    padding:
        0
        18px
        13px;

    border-bottom:
        1px solid var(--monitor-line);
}


.monitor-tabs {
    display: flex;
    flex-wrap: wrap;

    gap: 5px;
}


.monitor-tabs button {
    display: inline-flex;
    align-items: center;

    min-height: 30px;

    gap: 6px;

    padding: 6px 9px;

    border: 1px solid transparent;
    border-radius: 8px;

    background: transparent;

    color: #887f75;

    font-size: 7.5px;
}


.monitor-tabs button:hover {
    background: #faf7f1;
}


.monitor-tabs
button[aria-pressed="true"] {
    border-color: #ead7b3;

    background: #fff6e5;

    color: #a07121;

    font-weight: 600;
}


.monitor-tabs span {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 21px;
    height: 18px;

    padding-inline: 5px;

    border-radius: 6px;

    background: #f0ece6;

    font-size: 6.5px;
}


.monitor-text-link {
    display: inline-flex;
    align-items: center;

    padding: 0;

    border: 0;

    background: transparent;

    color: #b2770f;

    font-size: 7.5px;
    font-weight: 600;

    text-decoration: none;
}


.monitor-text-link:hover {
    color: #84580a;

    text-decoration: underline;
    text-underline-offset: 3px;
}


/* ==========================================================================
   ORDER LIST
   ========================================================================== */

.monitor-order-list {
    display: grid;

    gap: 12px;

    padding: 14px;

    background: #f7f7f6;
}


.monitor-order-card {
    overflow: hidden;

    border:
        1px solid var(--monitor-line);

    border-radius: 16px;

    background: #ffffff;

    box-shadow:
        0 4px 14px
        rgba(43, 33, 21, .022);

    transition:
        border-color .16s ease,
        box-shadow .16s ease;
}


.monitor-order-card:hover {
    border-color: #d8ccb9;

    box-shadow:
        0 10px 26px
        rgba(43, 33, 21, .045);
}


/* ==========================================================================
   ORDER HEADER
   ========================================================================== */

.monitor-order-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 14px;

    padding: 13px 16px;

    border-bottom:
        1px solid var(--monitor-line);
}


.monitor-order-identity {
    display: flex;
    align-items: center;

    min-width: 0;

    gap: 11px;
}


.monitor-order-icon {
    display: grid;

    width: 36px;
    height: 36px;

    flex: 0 0 36px;

    place-items: center;

    border: 1px solid #ead8b4;
    border-radius: 10px;

    background:
        linear-gradient(
            180deg,
            #fff7e9,
            #fff2d5
        );

    color: #ad781b;

    font-size: 8px;
    font-weight: 600;
}


.monitor-order-identity
.monitor-eyebrow {
    margin-bottom: 2px;

    font-size: 5.8px;
}


.monitor-order-identity h3 {
    margin: 0;

    color: #373029;

    font-size: 10px;
    font-weight: 600;
}


.monitor-order-identity p {
    margin: 2px 0 0;

    color: #8f8579;

    font-size: 6.5px;
}


.monitor-order-status {
    display: flex;
    align-items: flex-end;
    flex-direction: column;

    gap: 4px;
}


.monitor-order-status small {
    color: #a0978d;

    font-size: 6px;
}


/* ==========================================================================
   STATUS BADGES
   ========================================================================== */

.monitor-status {
    display: inline-flex;
    align-items: center;

    min-height: 24px;

    padding: 4px 8px;

    border: 1px solid;
    border-radius: 999px;

    font-size: 6.5px;
    font-weight: 600;
}


.monitor-status-amber {
    border-color: #ead9b2;

    background: #fff8e8;

    color: #9a6b16;
}


.monitor-status-green {
    border-color: #d4e4d8;

    background: #f0f8f2;

    color: #4e7a59;
}


.monitor-status-blue {
    border-color: #d7e3ed;

    background: #f1f6fa;

    color: #587892;
}


.monitor-status-neutral {
    border-color: #e4ded6;

    background: #f8f6f3;

    color: #71685f;
}


/* ==========================================================================
   CURRENT STATE
   ========================================================================== */

.monitor-current-state {
    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        240px;

    gap: 16px;

    padding: 14px 16px;

    border-bottom:
        1px solid var(--monitor-line-soft);

    background: #fffdf9;
}


.monitor-current-state strong {
    display: block;

    color: #443c33;

    font-size: 8.5px;
    font-weight: 600;
}


.monitor-current-state p {
    margin: 3px 0 0;

    color: #8e8479;

    font-size: 7px;
}


.monitor-current-handler {
    display: flex;
    align-items: center;

    gap: 9px;
}


.monitor-handler-avatar {
    display: grid;

    width: 31px;
    height: 31px;

    flex: 0 0 31px;

    place-items: center;

    border: 1px solid #d7e3ed;
    border-radius: 9px;

    background: #f1f6fb;

    color: #587b99;

    font-size: 7px;
    font-weight: 600;
}


/* ==========================================================================
   LABELS
   ========================================================================== */

.monitor-label {
    display: block;

    margin-bottom: 4px;

    color: #a08e75;

    font-size: 6px;
    font-weight: 600;

    letter-spacing: .08em;
    text-transform: uppercase;
}


/* ==========================================================================
   JOURNEY
   ========================================================================== */

.monitor-journey-scroll {
    overflow-x: auto;

    padding: 16px;
}


.monitor-journey {
    display: grid;

    grid-template-columns:
        repeat(7, minmax(100px, 1fr));

    min-width: 780px;
}


.monitor-journey-step {
    position: relative;

    display: flex;
    align-items: center;

    min-width: 0;

    gap: 8px;
}


.monitor-journey-step::after {
    content: "";

    position: absolute;

    left: 35px;
    right: 7px;
    top: 13px;

    height: 1px;

    background: #e5dfd6;
}


.monitor-journey-step:last-child::after {
    display: none;
}


.monitor-step-marker {
    position: relative;
    z-index: 1;

    display: grid;

    width: 27px;
    height: 27px;

    flex: 0 0 27px;

    place-items: center;

    border: 1px solid #e1dbd3;
    border-radius: 999px;

    background: #ffffff;

    color: #9b9288;

    font-size: 6.5px;
    font-weight: 600;
}


.monitor-journey-step > div:last-child {
    position: relative;
    z-index: 1;

    min-width: 0;

    padding-right: 6px;

    background: #ffffff;
}


.monitor-journey-step strong {
    display: block;

    overflow: hidden;

    color: #756c62;

    font-size: 6.5px;
    font-weight: 600;

    text-overflow: ellipsis;
    white-space: nowrap;
}


.monitor-journey-step span {
    display: block;

    margin-top: 1px;

    color: #a1988e;

    font-size: 5.5px;
}


.monitor-journey-step.is-complete
.monitor-step-marker {
    border-color: #d4e4d8;

    background: #f0f8f2;

    color: #4e7a59;
}


.monitor-journey-step.is-complete::after {
    background: #cfe0d3;
}


.monitor-journey-step.is-current
.monitor-step-marker {
    border-color: #d79b13;

    background: #d89b10;

    color: #ffffff;

    box-shadow:
        0 0 0 4px
        rgba(216, 155, 16, .09);
}


.monitor-journey-step.is-current strong {
    color: #9c6a0e;
}


/* ==========================================================================
   DETAIL GRID
   ========================================================================== */

.monitor-detail-grid {
    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 0;

    border-top:
        1px solid var(--monitor-line-soft);
}


.monitor-detail {
    min-width: 0;

    padding: 13px 16px;

    border-right:
        1px solid var(--monitor-line-soft);

    border-bottom:
        1px solid var(--monitor-line-soft);
}


.monitor-detail:nth-child(3n) {
    border-right: 0;
}


.monitor-detail:nth-last-child(-n + 3) {
    border-bottom: 0;
}


.monitor-detail strong {
    display: block;

    color: #4c443b;

    font-size: 7.5px;
    font-weight: 600;

    overflow-wrap: anywhere;
}


.monitor-detail p {
    margin: 3px 0 0;

    color: #91877b;

    font-size: 6.5px;
    line-height: 1.5;

    overflow-wrap: anywhere;
}


/* ==========================================================================
   ORDER FOOTER
   ========================================================================== */

.monitor-order-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 12px;

    padding: 10px 16px;

    background: #fcfbf9;

    color: #938b82;

    font-size: 6.5px;
}


.monitor-order-footer strong {
    color: #61584f;

    font-weight: 600;
}


/* ==========================================================================
   DELIVERED TODAY
   ========================================================================== */

.monitor-delivered-total {
    display: inline-flex;
    align-items: center;

    min-height: 27px;

    padding: 5px 9px;

    border: 1px solid #d4e4d8;
    border-radius: 999px;

    background: #f0f8f2;

    color: #4e7a59;

    font-size: 6.5px;
    font-weight: 600;
}


.monitor-delivered-grid {
    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 9px;

    padding: 0 14px 14px;
}


.monitor-delivered-card {
    padding: 12px;

    border: 1px solid #dce9df;
    border-radius: 12px;

    background: #f9fcfa;
}


.monitor-delivered-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 10px;

    padding-bottom: 9px;

    border-bottom: 1px solid #e5eee7;
}


.monitor-delivered-header strong {
    display: block;

    color: #3f5846;

    font-size: 8px;
}


.monitor-delivered-check {
    display: grid;

    width: 26px;
    height: 26px;

    place-items: center;

    border-radius: 999px;

    background: #e9f5ec;

    color: #4f795a;

    font-size: 8px;
    font-weight: 700;
}


.monitor-delivered-body {
    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 8px;

    margin: 10px 0;
}


.monitor-delivered-body strong {
    color: #55665a;

    font-size: 7px;
    font-weight: 600;
}


/* ==========================================================================
   EMPTY
   ========================================================================== */

.monitor-empty {
    padding: 42px 20px;

    text-align: center;
}


.monitor-empty-small {
    grid-column: 1 / -1;

    padding: 30px 20px;
}


.monitor-empty-icon {
    display: grid;

    width: 40px;
    height: 40px;

    place-items: center;

    margin: 0 auto 12px;

    border: 1px solid #ead9b8;
    border-radius: 12px;

    background: #fff6e3;

    color: #b78327;

    font-size: 14px;
}


.monitor-empty h3 {
    margin: 0 0 6px;

    color: #5e554b;

    font-size: 11px;
    font-weight: 600;
}


.monitor-empty p {
    max-width: 430px;

    margin: 0 auto 14px;

    color: #999086;

    font-size: 7.5px;
}


/* ==========================================================================
   FOOTER
   ========================================================================== */

.monitor-list-footer {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;

    gap: 10px;

    padding: 10px 18px;

    border-top:
        1px solid var(--monitor-line);

    background: #fcfbf9;

    color: #9a9288;

    font-size: 6.5px;
}


/* ==========================================================================
   RESPONSIVE
   ========================================================================== */

@media (max-width: 1200px) {

    .monitor-stats {
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
    }


    .monitor-delivered-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }
}


@media (max-width: 900px) {

    .monitor-page-header {
        align-items: flex-start;

        min-height: 0;
    }


    .monitor-current-state {
        grid-template-columns: 1fr 1fr;
    }


    .monitor-detail-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }


    .monitor-detail:nth-child(3n) {
        border-right:
            1px solid var(--monitor-line-soft);
    }


    .monitor-detail:nth-child(2n) {
        border-right: 0;
    }


    .monitor-detail:nth-last-child(-n + 3) {
        border-bottom:
            1px solid var(--monitor-line-soft);
    }


    .monitor-detail:nth-last-child(-n + 2) {
        border-bottom: 0;
    }
}


@media (max-width: 767px) {

    .monitor-page-header,
    .monitor-toolbar {
        flex-direction: column;
        align-items: stretch;

        gap: 13px;
    }


    .monitor-page-header {
        border-radius: 17px;
    }


    .monitor-header-actions {
        flex-wrap: wrap;
    }


    .monitor-header-actions
    .monitor-button {
        flex: 1;
    }


    .monitor-stats {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 8px;
    }


    .monitor-stat {
        padding: 12px;
    }


    .monitor-stat strong {
        font-size: 20px;
    }


    .monitor-flow {
        width: 100%;

        overflow-x: auto;
    }


    .monitor-flow :is(a, span) {
        flex-shrink: 0;

        font-size: 7px;
    }


    .monitor-toolbar {
        padding: 14px;
    }


    .monitor-search {
        width: 100%;
    }


    .monitor-filter-row {
        align-items: flex-start;

        padding:
            0
            14px
            12px;
    }


    .monitor-order-list {
        padding: 9px;
    }


    .monitor-order-header {
        align-items: flex-start;
    }


    .monitor-current-state {
        grid-template-columns: 1fr;
    }


    .monitor-delivered-grid {
        grid-template-columns: 1fr;
    }


    .monitor-list-footer {
        padding-inline: 14px;
    }
}


@media (max-width: 520px) {

    .monitor-page-header {
        padding: 15px 14px;
    }


    .monitor-header-actions {
        flex-direction: column;
    }


    .monitor-header-actions
    .monitor-button {
        width: 100%;
    }


    .monitor-filter-row {
        flex-direction: column;
        align-items: stretch;
    }


    .monitor-order-header {
        flex-direction: column;
    }


    .monitor-order-status {
        align-items: flex-start;
    }


    .monitor-detail-grid {
        grid-template-columns: 1fr;
    }


    .monitor-detail,
    .monitor-detail:nth-child(3n),
    .monitor-detail:nth-child(2n),
    .monitor-detail:nth-last-child(-n + 3),
    .monitor-detail:nth-last-child(-n + 2) {
        border-right: 0;
        border-bottom:
            1px solid var(--monitor-line-soft);
    }


    .monitor-detail:last-child {
        border-bottom: 0;
    }


    .monitor-delivered-body {
        grid-template-columns: 1fr;
    }
}


/* ==========================================================================
   REDUCED MOTION
   ========================================================================== */

@media (prefers-reduced-motion: reduce) {

    .monitor-page *,
    .monitor-page *::before,
    .monitor-page *::after {
        animation: none !important;
        transition: none !important;
        scroll-behavior: auto !important;
    }
}
</style>

<style>
#lgDeliveryMonitoring {
    --monitor-ink: #25211c;
    --monitor-muted: #8a8177;
    --monitor-line: #e8e2d9;
    --monitor-line-soft: #f0ebe5;
    --monitor-gold: #d9950b;
    width: 100%;
    max-width: 1620px;
    margin-inline: auto;
    font-size: 10px;
}

#lgDeliveryMonitoring [hidden] {
    display: none !important;
}

#lgDeliveryMonitoring :is(a, button, input):focus-visible {
    outline: 2px solid rgba(217,149,11,.65);
    outline-offset: 2px;
}

/* PAGE HEADING */

#lgDeliveryMonitoring .monitor-page-header {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    min-height: 0;
    gap: 16px;
    padding: 5px 2px 17px;
    margin-bottom: 14px;
    overflow: visible;
    border: 0;
    border-bottom: 1px solid #e4e1dc;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}

#lgDeliveryMonitoring .monitor-page-header::after {
    display: none;
}

#lgDeliveryMonitoring .monitor-eyebrow,
#lgDeliveryMonitoring .monitor-page-header .monitor-eyebrow {
    margin-bottom: 5px;
    color: #a76f11;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
    line-height: 1.4;
}

#lgDeliveryMonitoring .monitor-page-header h1 {
    color: #24201b;
    font-size: clamp(23px, 2.15vw, 30px);
    font-weight: 750;
    line-height: 1.2;
    letter-spacing: -.045em;
}

#lgDeliveryMonitoring .monitor-page-header h1 span {
    color: var(--monitor-gold);
}

#lgDeliveryMonitoring .monitor-subtitle {
    max-width: 740px;
    margin-top: 6px;
    color: #897f75;
    font-size: 10px;
    line-height: 1.7;
}

#lgDeliveryMonitoring .monitor-header-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

/* BUTTONS */

#lgDeliveryMonitoring .monitor-button {
    min-height: 39px;
    padding: 9px 14px;
    border: 1px solid #e4d8c6;
    border-radius: 10px;
    background: #fff;
    color: #776a5a;
    font-size: 10px;
    font-weight: 650;
    box-shadow: none;
}

#lgDeliveryMonitoring .monitor-page-header
.monitor-button:not(.monitor-button-primary) {
    border-color: #e4d8c6;
    background: #fff;
    color: #776a5a;
}

#lgDeliveryMonitoring .monitor-page-header
.monitor-button:not(.monitor-button-primary):hover {
    border-color: #d5bb87;
    background: #fff9ed;
    color: #a36c0a;
}

#lgDeliveryMonitoring .monitor-button-primary,
#lgDeliveryMonitoring .monitor-page-header .monitor-button-primary {
    background: var(--monitor-gold);
    border-color: var(--monitor-gold);
    color: #fff;
}

#lgDeliveryMonitoring .monitor-button-primary:hover,
#lgDeliveryMonitoring .monitor-page-header .monitor-button-primary:hover {
    background: #c88708;
    border-color: #c88708;
    color: #fff;
}

/* KPI CARDS */

#lgDeliveryMonitoring .monitor-stats {
    display: grid;
    grid-template-columns: repeat(5,minmax(0,1fr));
    gap: 12px;
}

#lgDeliveryMonitoring .monitor-stat {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    min-width: 0;
    min-height: 99px;
    gap: 10px;
    padding: 14px;
    border: 1px solid #e8e2d9;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 6px 18px rgba(33,24,14,.035);
    transition:
        transform .18s ease,
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgDeliveryMonitoring .monitor-stat::after {
    display: none;
}

#lgDeliveryMonitoring .monitor-stat p {
    color: #756b60;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: normal;
    text-transform: none;
}

#lgDeliveryMonitoring .monitor-stat strong,
#lgDeliveryMonitoring .monitor-stat-amber strong,
#lgDeliveryMonitoring .monitor-stat-blue strong,
#lgDeliveryMonitoring .monitor-stat-green strong {
    display: block;
    margin: 8px 0 5px;
    color: #211d18;
    font-size: 25px;
    font-weight: 750;
    line-height: 1.1;
}

#lgDeliveryMonitoring .monitor-stat span:not(.dm-stat-icon) {
    color: #8d8378;
    font-size: 9px;
    line-height: 1.5;
}

#lgDeliveryMonitoring .dm-stat-icon {
    display: grid;
    place-items: center;
    flex: 0 0 41px;
    width: 41px;
    height: 41px;
    border-radius: 11px;
    background: #f2f1ef;
    color: #80776d;
}

#lgDeliveryMonitoring .dm-stat-icon svg {
    width: 18px;
    height: 18px;
}

#lgDeliveryMonitoring .monitor-stat-amber .dm-stat-icon {
    background: #fff7e9;
    color: #bc8420;
}

#lgDeliveryMonitoring .monitor-stat-blue .dm-stat-icon {
    background: #eff5fa;
    color: #587ead;
}

#lgDeliveryMonitoring .monitor-stat-green .dm-stat-icon {
    background: #eef8f2;
    color: #4c8663;
}

/* WORKFLOW AND WORKSPACE */

#lgDeliveryMonitoring .monitor-flow {
    max-width: 100%;
    gap: 4px;
    margin: 14px 0;
    padding: 4px;
    border-radius: 10px;
}

#lgDeliveryMonitoring .monitor-flow :is(a,span) {
    min-height: 32px;
    padding: 5px 10px;
    border-radius: 7px;
    font-size: 9px;
}

#lgDeliveryMonitoring .monitor-flow b {
    width: 21px;
    height: 21px;
    font-size: 9px;
}

#lgDeliveryMonitoring .monitor-workspace {
    min-width: 0;
    border-color: #e8e2d9;
    border-radius: 17px;
    background: #fff;
    box-shadow: 0 6px 18px rgba(33,24,14,.035);
}

#lgDeliveryMonitoring .monitor-delivered-workspace {
    margin-top: 14px;
}

#lgDeliveryMonitoring .monitor-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    padding: 15px 17px;
}

#lgDeliveryMonitoring .monitor-toolbar h2 {
    margin-top: 4px;
    color: #29231d;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.035em;
}

#lgDeliveryMonitoring .monitor-toolbar p:not(.monitor-eyebrow) {
    margin-top: 4px;
    color: #93897e;
    font-size: 9px;
    line-height: 1.55;
}

/* SEARCH AND FILTERS */

#lgDeliveryMonitoring .monitor-search {
    width: min(315px,100%);
}

#lgDeliveryMonitoring .monitor-search input {
    height: 39px;
    padding: 0 12px;
    border: 1px solid #dfe2e6;
    border-radius: 10px;
    font-size: 10px;
}

#lgDeliveryMonitoring .monitor-search input:focus {
    border-color: var(--monitor-gold);
    box-shadow: 0 0 0 3px rgba(217,149,11,.09);
}

#lgDeliveryMonitoring .monitor-filter-row {
    gap: 10px;
    padding: 0 17px 13px;
    border-color: #eee8df;
}

#lgDeliveryMonitoring .monitor-tabs {
    gap: 4px;
    padding: 4px;
    border: 1px solid #e6e1d9;
    border-radius: 10px;
    background: #f7f8fa;
}

#lgDeliveryMonitoring .monitor-tabs button {
    min-height: 30px;
    padding: 5px 10px;
    border-radius: 7px;
    font-size: 9px;
}

#lgDeliveryMonitoring .monitor-tabs button[aria-pressed="true"] {
    border-color: #e9dfce;
    background: #fff;
    color: #a36c0a;
    font-weight: 700;
    box-shadow: 0 2px 5px rgba(35,29,22,.035);
}

#lgDeliveryMonitoring .monitor-tabs span {
    min-width: 21px;
    height: 19px;
    font-size: 8px;
}

#lgDeliveryMonitoring .monitor-text-link {
    color: #a66d0b;
    font-size: 9px;
    font-weight: 650;
    line-height: 1.5;
}

/* ACTIVE ORDER CARDS */

#lgDeliveryMonitoring .monitor-order-list {
    gap: 12px;
    padding: 14px;
    background: #fff;
}

#lgDeliveryMonitoring .monitor-order-card {
    min-width: 0;
    border: 1px solid #e8e2d9;
    border-radius: 13px;
    background: #fff;
    box-shadow: 0 3px 12px rgba(35,27,18,.025);
    transition:
        transform .18s ease,
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgDeliveryMonitoring .monitor-order-header {
    gap: 12px;
    padding: 14px 15px;
    border-bottom-color: #eee8df;
}

#lgDeliveryMonitoring .monitor-order-identity {
    min-width: 0;
    gap: 11px;
}

#lgDeliveryMonitoring .monitor-order-identity > div {
    min-width: 0;
}

#lgDeliveryMonitoring .monitor-order-icon {
    flex: 0 0 41px;
    width: 41px;
    height: 41px;
    border: 1px solid #eee3ce;
    border-radius: 11px;
    background: #fff5e1;
    color: #a97312;
    font-size: 12px;
    font-weight: 700;
}

#lgDeliveryMonitoring .monitor-order-identity .monitor-eyebrow {
    margin: 0 0 3px;
    color: #a68c61;
    font-size: 8px;
}

#lgDeliveryMonitoring .monitor-order-identity h3 {
    color: #383027;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgDeliveryMonitoring .monitor-order-identity p:not(.monitor-eyebrow) {
    margin-top: 4px;
    color: #8f8579;
    font-size: 9px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgDeliveryMonitoring .monitor-order-status {
    gap: 6px;
    text-align: right;
}

#lgDeliveryMonitoring .monitor-order-status small {
    color: #92887e;
    font-size: 9px;
}

#lgDeliveryMonitoring .monitor-status {
    min-height: 25px;
    padding: 5px 9px;
    border-radius: 999px;
    font-size: 8.5px;
    font-weight: 700;
    line-height: 1.35;
}

/* CURRENT OPERATION / HANDLER */

#lgDeliveryMonitoring .monitor-current-state {
    display: grid;
    grid-template-columns: minmax(0,1fr) minmax(230px,.52fr);
    gap: 10px;
    padding: 13px 15px;
    border-bottom: 1px solid #eee8df;
    background: #fff;
}

#lgDeliveryMonitoring .dm-operation-cell {
    min-width: 0;
    min-height: 83px;
    padding: 12px;
    border: 1px solid #e9e5df;
    border-radius: 10px;
    background: #fbfbfc;
}

#lgDeliveryMonitoring .monitor-current-handler {
    align-items: center;
    gap: 9px;
}

#lgDeliveryMonitoring .monitor-handler-avatar {
    flex: 0 0 34px;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border-color: #e4daca;
    background: #f6f2e9;
    color: #8c7856;
    font-size: 10px;
    font-weight: 700;
}

#lgDeliveryMonitoring .monitor-label {
    margin-bottom: 5px;
    color: #958879;
    font-size: 8px;
    font-weight: 700;
    letter-spacing: .065em;
}

#lgDeliveryMonitoring .monitor-current-state strong {
    color: #443c33;
    font-size: 10px;
    font-weight: 700;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgDeliveryMonitoring .monitor-current-state p {
    margin-top: 4px;
    color: #908679;
    font-size: 9px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

/* SEVEN-STEP PARCEL JOURNEY */

#lgDeliveryMonitoring .monitor-journey-scroll {
    padding: 16px 15px;
    overflow-x: auto;
}

#lgDeliveryMonitoring .monitor-journey {
    display: grid;
    grid-template-columns: repeat(7,minmax(118px,1fr));
    min-width: 875px;
}

#lgDeliveryMonitoring .monitor-journey-step {
    align-items: center;
    gap: 7px;
}

#lgDeliveryMonitoring .monitor-journey-step::after {
    top: 15px;
    left: 38px;
    right: 8px;
}

#lgDeliveryMonitoring .monitor-step-marker {
    flex: 0 0 30px;
    width: 30px;
    height: 30px;
    color: #9b9288;
    font-size: 10px;
    font-weight: 700;
}

#lgDeliveryMonitoring .monitor-journey-step > div:last-child {
    padding-right: 5px;
}

#lgDeliveryMonitoring .monitor-journey-step strong {
    color: #756c62;
    font-size: 9px;
    font-weight: 650;
    line-height: 1.45;
}

#lgDeliveryMonitoring .monitor-journey-step span {
    margin-top: 3px;
    color: #a1988e;
    font-size: 8px;
    line-height: 1.35;
}

#lgDeliveryMonitoring .monitor-journey-step.is-current .monitor-step-marker {
    background: #d9950b;
    border-color: #d9950b;
}

#lgDeliveryMonitoring .monitor-journey-step.is-current strong {
    color: #9c6a0e;
}

/* OPERATIONAL DETAILS */

#lgDeliveryMonitoring .monitor-detail-grid {
    grid-template-columns: repeat(3,minmax(0,1fr));
    border-top-color: #eee8df;
}

#lgDeliveryMonitoring .monitor-detail {
    min-width: 0;
    padding: 13px 15px;
    border-color: #f0ebe5;
}

#lgDeliveryMonitoring .monitor-detail strong {
    color: #4c443b;
    font-size: 10px;
    font-weight: 650;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgDeliveryMonitoring .monitor-detail p {
    margin-top: 4px;
    color: #91877b;
    font-size: 9px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

#lgDeliveryMonitoring .monitor-order-footer {
    gap: 12px;
    padding: 12px 15px;
    background: #fcfcfd;
    color: #938b82;
    font-size: 9px;
}

#lgDeliveryMonitoring .monitor-order-footer strong {
    color: #51473d;
    font-weight: 700;
}

/* DELIVERED TODAY */

#lgDeliveryMonitoring .monitor-delivered-total {
    min-height: 25px;
    padding: 5px 9px;
    font-size: 9px;
    font-weight: 700;
}

#lgDeliveryMonitoring .monitor-delivered-grid {
    grid-template-columns: repeat(3,minmax(0,1fr));
    gap: 12px;
    padding: 0 14px 14px;
}

#lgDeliveryMonitoring .monitor-delivered-card {
    min-width: 0;
    padding: 13px;
    border: 1px solid #e8e2d9;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 3px 12px rgba(35,27,18,.025);
    transition:
        transform .18s ease,
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgDeliveryMonitoring .monitor-delivered-header {
    padding-bottom: 10px;
    border-bottom-color: #eee8df;
}

#lgDeliveryMonitoring .monitor-delivered-header strong {
    color: #443c33;
    font-size: 11px;
    font-weight: 700;
}

#lgDeliveryMonitoring .monitor-delivered-check {
    flex: 0 0 29px;
    width: 29px;
    height: 29px;
    background: #edf7ef;
    color: #4f795a;
    font-size: 12px;
}

#lgDeliveryMonitoring .monitor-delivered-body {
    gap: 9px;
    margin: 12px 0;
}

#lgDeliveryMonitoring .monitor-delivered-body > div {
    min-width: 0;
}

#lgDeliveryMonitoring .monitor-delivered-body strong {
    display: block;
    color: #51483e;
    font-size: 10px;
    font-weight: 650;
    overflow-wrap: anywhere;
}

/* EMPTY STATES AND FOOTERS */

#lgDeliveryMonitoring .monitor-empty {
    padding: 40px 20px;
}

#lgDeliveryMonitoring .monitor-empty-icon {
    width: 43px;
    height: 43px;
    border-radius: 12px;
    background: #fff7e8;
}

#lgDeliveryMonitoring .monitor-empty h3 {
    color: #51483e;
    font-size: 11px;
    font-weight: 700;
}

#lgDeliveryMonitoring .monitor-empty p {
    color: #948a7e;
    font-size: 9px;
    line-height: 1.6;
}

#lgDeliveryMonitoring .monitor-list-footer {
    padding: 12px 16px;
    background: #fff;
    border-top-color: #eee8df;
    font-size: 9px;
}

/* HOVER */

@media (hover:hover) and (pointer:fine) {
    #lgDeliveryMonitoring .monitor-stat:hover,
    #lgDeliveryMonitoring .monitor-order-card:hover,
    #lgDeliveryMonitoring .monitor-delivered-card:hover {
        transform: translateY(-2px);
        border-color: #d8c9b2;
        box-shadow: 0 13px 28px rgba(43,33,21,.055);
    }
}

/* RESPONSIVE */

@media (max-width:1200px) {
    #lgDeliveryMonitoring .monitor-stats {
        grid-template-columns: repeat(3,minmax(0,1fr));
    }

    #lgDeliveryMonitoring .monitor-delivered-grid {
        grid-template-columns: repeat(2,minmax(0,1fr));
    }
}

@media (max-width:900px) {
    #lgDeliveryMonitoring .monitor-current-state {
        grid-template-columns: repeat(2,minmax(0,1fr));
    }

    #lgDeliveryMonitoring .monitor-detail-grid {
        grid-template-columns: repeat(2,minmax(0,1fr));
    }

    #lgDeliveryMonitoring .monitor-detail:nth-child(3n) {
        border-right: 1px solid #f0ebe5;
    }

    #lgDeliveryMonitoring .monitor-detail:nth-child(2n) {
        border-right: 0;
    }

    #lgDeliveryMonitoring .monitor-detail:nth-last-child(-n+3) {
        border-bottom: 1px solid #f0ebe5;
    }

    #lgDeliveryMonitoring .monitor-detail:nth-last-child(-n+2) {
        border-bottom: 0;
    }
}

@media (max-width:767px) {
    #lgDeliveryMonitoring .monitor-page-header,
    #lgDeliveryMonitoring .monitor-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    #lgDeliveryMonitoring .monitor-header-actions {
        width: 100%;
    }

    #lgDeliveryMonitoring .monitor-header-actions .monitor-button {
        flex: 1;
    }

    #lgDeliveryMonitoring .monitor-stats {
        grid-template-columns: repeat(2,minmax(0,1fr));
    }

    #lgDeliveryMonitoring .monitor-search {
        width: 100%;
    }

    #lgDeliveryMonitoring .monitor-filter-row {
        padding-inline: 14px;
    }

    #lgDeliveryMonitoring .monitor-order-list {
        padding: 10px;
    }

    #lgDeliveryMonitoring .monitor-current-state {
        grid-template-columns: 1fr;
    }

    #lgDeliveryMonitoring .monitor-delivered-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width:520px) {
    #lgDeliveryMonitoring .monitor-page-header {
        padding: 5px 2px 17px;
    }

    #lgDeliveryMonitoring .monitor-header-actions {
        flex-direction: column;
    }

    #lgDeliveryMonitoring .monitor-header-actions .monitor-button {
        width: 100%;
    }

    #lgDeliveryMonitoring .monitor-stat {
        padding: 12px;
    }

    #lgDeliveryMonitoring .monitor-stat strong {
        font-size: 22px;
    }

    #lgDeliveryMonitoring .dm-stat-icon {
        flex-basis: 34px;
        width: 34px;
        height: 34px;
    }

    #lgDeliveryMonitoring .monitor-filter-row {
        flex-direction: column;
        align-items: stretch;
    }

    #lgDeliveryMonitoring .monitor-order-header {
        flex-direction: column;
        align-items: flex-start;
    }

    #lgDeliveryMonitoring .monitor-order-status {
        align-items: flex-start;
        text-align: left;
    }

    #lgDeliveryMonitoring .monitor-detail-grid {
        grid-template-columns: 1fr;
    }

    #lgDeliveryMonitoring .monitor-detail,
    #lgDeliveryMonitoring .monitor-detail:nth-child(2n),
    #lgDeliveryMonitoring .monitor-detail:nth-child(3n),
    #lgDeliveryMonitoring .monitor-detail:nth-last-child(-n+3),
    #lgDeliveryMonitoring .monitor-detail:nth-last-child(-n+2) {
        border-right: 0;
        border-bottom: 1px solid #f0ebe5;
    }

    #lgDeliveryMonitoring .monitor-detail:last-child {
        border-bottom: 0;
    }

    #lgDeliveryMonitoring .monitor-delivered-body {
        grid-template-columns: 1fr;
    }
}

@media (prefers-reduced-motion:reduce) {
    #lgDeliveryMonitoring *,
    #lgDeliveryMonitoring *::before,
    #lgDeliveryMonitoring *::after {
        transition-duration: .01ms !important;
        animation-duration: .01ms !important;
    }

    #lgDeliveryMonitoring .monitor-stat:hover,
    #lgDeliveryMonitoring .monitor-order-card:hover,
    #lgDeliveryMonitoring .monitor-delivered-card:hover {
        transform: none !important;
    }
}

/* HUB OPERATOR SCOPE */

#lgDeliveryMonitoring .monitor-hub-banner {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin: 0 0 14px;
    padding: 12px 14px;
    border: 1px solid #e8dfd0;
    border-radius: 11px;
    background: #fffaf1;
    color: #6f604c;
}

#lgDeliveryMonitoring .monitor-hub-banner svg {
    flex: 0 0 17px;
    width: 17px;
    height: 17px;
    margin-top: 2px;
    color: #a97312;
}

#lgDeliveryMonitoring .monitor-hub-banner strong {
    display: block;
    color: #5b4525;
    font-size: 10px;
    font-weight: 700;
}

#lgDeliveryMonitoring .monitor-hub-banner p {
    margin: 4px 0 0;
    color: #817260;
    font-size: 9px;
    line-height: 1.6;
}

#lgDeliveryMonitoring .monitor-readonly-indicator {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #8b8276;
    font-size: 9px;
    font-weight: 600;
    line-height: 1.5;
}

#lgDeliveryMonitoring .monitor-readonly-indicator svg {
    width: 13px;
    height: 13px;
}

@media (max-width: 520px) {
    #lgDeliveryMonitoring .monitor-hub-banner {
        padding: 11px;
    }
}
</style>
@endpush

@section('content')

@php
    // Authorization is enforced by the Controller.
    $isHubOperator = (bool) ($isHubOperator ?? false);
    $hubFacility = $hubFacility ?? null;
@endphp

<div
    id="lgDeliveryMonitoring"
    class="monitor-page"
    data-monitor-workspace
>
    {{-- PAGE HEADING --}}

    <header class="monitor-page-header">
        <div class="min-w-0">
            <p class="monitor-eyebrow">
                {{ $isHubOperator ? 'ASSIGNED HUB · READ-ONLY JOURNEYS' : 'END-TO-END OPERATIONS' }}
            </p>

            <h1>
                Delivery <span>Monitoring</span>
            </h1>

            <p class="monitor-subtitle">
                @if($isHubOperator)
                    Follow parcels originating from or destined for your assigned hub,
                    including their full Seller-to-Buyer journey. Monitoring is read-only.
                @else
                    Follow every active parcel from Seller pickup through
                    origin hub processing, linehaul, destination hub dispatch,
                    Last-Mile delivery, and final Buyer handoff.
                @endif
            </p>
        </div>

        <div class="monitor-header-actions">
            <a
                href="{{ route('logistics.last-mile-assignment') }}"
                class="monitor-button"
            >
                <span aria-hidden="true">←</span>
                Last Mile
            </a>

            <a
                href="{{ route('logistics.dashboard') }}"
                class="monitor-button monitor-button-primary"
            >
                Dashboard
            </a>
        </div>
    </header>

    @if($isHubOperator)
        <aside
            class="monitor-hub-banner"
            role="note"
            aria-label="Monitoring visibility"
        >
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >
                <path d="M3 10.5 12 4l9 6.5V21H3zM9 21v-7h6v7"/>
            </svg>

            <div>
                <strong>
                    {{ $hubFacility?->name ?: 'Assigned Municipal Hub' }}
                    @if($hubFacility?->municipality_name)
                        · {{ $hubFacility->municipality_name }}
                    @endif
                </strong>

                <p>
                    Only parcels whose origin or destination route stop belongs to this hub are included.
                    You can track the complete journey here, but delivery-route editing remains a Main Logistics action.
                </p>
            </div>
        </aside>
    @endif

    {{-- KPI SUMMARY --}}

    @php
        $summaryCards = [
            [
                'Active Deliveries',
                $stats['active'],
                $isHubOperator ? 'Journeys involving this hub' : 'Across all delivery legs',
                'neutral',
            ],
            [
                'At Hubs',
                $stats['at_hub'],
                'Sorting or dispatch operations',
                'amber',
            ],
            [
                'In Linehaul',
                $stats['linehaul'],
                'Moving between hubs',
                'blue',
            ],
            [
                'Out for Delivery',
                $stats['out_for_delivery'],
                'With local Riders',
                'green',
            ],
            [
                'Delivered Today',
                $stats['delivered_today'],
                'Completed Buyer handoffs',
                'green',
            ],
        ];
    @endphp

    <section
        class="monitor-stats"
        aria-label="Delivery Monitoring overview"
    >
        @foreach($summaryCards as $index => [$label, $value, $note, $tone])
            <article class="monitor-stat monitor-stat-{{ $tone }}">
                <div class="min-w-0">
                    <p>{{ $label }}</p>

                    <strong>
                        {{ number_format($value) }}
                    </strong>

                    <span>{{ $note }}</span>
                </div>

                <span class="dm-stat-icon" aria-hidden="true">
                    @switch($index)
                        @case(0)
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M4 5h16v14H4zM8 9h8M8 13h8"/>
                            </svg>
                            @break

                        @case(1)
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect x="3" y="5" width="8" height="7" rx="1"/>
                                <rect x="13" y="5" width="8" height="7" rx="1"/>
                                <rect x="8" y="14" width="8" height="7" rx="1"/>
                            </svg>
                            @break

                        @case(2)
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M3 7h12v10H3z"/>
                                <path d="M15 10h3l3 3v4h-6"/>
                                <circle cx="7" cy="18" r="1.5"/>
                                <circle cx="18" cy="18" r="1.5"/>
                            </svg>
                            @break

                        @case(3)
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v5l3 2"/>
                            </svg>
                            @break

                        @default
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m8 12 2.5 2.5L16 9"/>
                            </svg>
                    @endswitch
                </span>
            </article>
        @endforeach
    </section>

    {{-- DELIVERY WORKFLOW --}}

    <nav
        class="monitor-flow"
        aria-label="Delivery operations workflow"
    >
        <a href="{{ route('logistics.pickup-requests') }}">
            <b>1</b>
            Seller Pickup
        </a>

        <a href="{{ route('logistics.incoming-parcels') }}">
            <b>2</b>
            Origin Hub
        </a>

        <a href="{{ route('logistics.parcel-sorting') }}">
            <b>3</b>
            Sorting
        </a>

        <a href="{{ route('logistics.hub-transfers') }}">
            <b>4</b>
            Linehaul
        </a>

        <a href="{{ route('logistics.last-mile-assignment') }}">
            <b>5</b>
            Last Mile
        </a>

        <span class="is-current" aria-current="step">
            <b>6</b>
            Monitoring
        </span>
    </nav>

    {{-- ACTIVE PARCEL JOURNEYS --}}

    <section
        class="monitor-workspace"
        aria-labelledby="active-deliveries-title"
    >
        <header class="monitor-toolbar">
            <div>
                <p class="monitor-eyebrow">
                    PARCEL JOURNEYS
                </p>

                <h2 id="active-deliveries-title">
                    Active Parcel Journeys
                </h2>

                <p>
                    Current stage is derived from Marketplace Order and Logistics Parcel states.
                    @if($isHubOperator)
                        Only your hub's permitted journeys appear here.
                    @endif
                </p>
            </div>

            <label class="monitor-search">
                <span class="sr-only">
                    Search active deliveries
                </span>

                <input
                    type="search"
                    data-monitor-search
                    value="{{ request()->query('q', '') }}"
                    autocomplete="off"
                    placeholder="Search order, Buyer, Seller, Rider, hub…"
                >
            </label>
        </header>

        {{-- STAGE FILTERS --}}

        @php
            $pickupCount = $activeOrders
                ->where('monitor_stage_group', 'pickup')
                ->count();

            $hubCount = $activeOrders
                ->where('monitor_stage_group', 'hub')
                ->count();

            $linehaulCount = $activeOrders
                ->where('monitor_stage_group', 'linehaul')
                ->count();

            $lastMileCount = $activeOrders
                ->where('monitor_stage_group', 'last_mile')
                ->count();

            $filters = [
                'all' => ['All', $activeOrders->count()],
                'pickup' => ['Pickup', $pickupCount],
                'hub' => ['At Hub', $hubCount],
                'linehaul' => ['Linehaul', $linehaulCount],
                'last_mile' => ['Last Mile', $lastMileCount],
            ];
        @endphp

        <div class="monitor-filter-row">
            <div
                class="monitor-tabs"
                role="group"
                aria-label="Filter delivery stage"
            >
                @foreach($filters as $filter => [$label, $count])
                    <button
                        type="button"
                        data-monitor-filter="{{ $filter }}"
                        aria-pressed="{{ $filter === 'all' ? 'true' : 'false' }}"
                    >
                        {{ $label }}
                        <span>{{ $count }}</span>
                    </button>
                @endforeach
            </div>

            <button
                type="button"
                class="monitor-text-link"
                data-monitor-clear
            >
                Clear filters
            </button>
        </div>

        {{-- ACTIVE ORDER CARDS --}}

        <div class="monitor-order-list">
            @forelse($activeOrders as $order)
                @php
                    $routeStops = $order
                        ->deliveryRouteStops
                        ->keyBy('stop_key');

                    $originStop = $routeStops->get('origin_facility');

                    $destinationStop = $routeStops->get(
                        'destination_facility'
                    );

                    $originName = $originStop?->facility?->name
                        ?: $originStop?->name
                        ?: 'Origin hub pending';

                    $destinationName = $destinationStop?->facility?->name
                        ?: $destinationStop?->name
                        ?: 'Destination hub pending';

                    $lastMile = $order->getAttribute(
                        'monitor_last_mile'
                    );

                    $transfer = $order->getAttribute(
                        'monitor_transfer'
                    );

                    $currentStep = (int) $order->getAttribute(
                        'monitor_stage_step'
                    );

                    $stageGroup = $order->getAttribute(
                        'monitor_stage_group'
                    );

                    $stageTone = $order->getAttribute(
                        'monitor_stage_tone'
                    );

                    $stageLabel = $order->getAttribute(
                        'monitor_stage_label'
                    );

                    $stageNote = $order->getAttribute(
                        'monitor_stage_note'
                    );

                    $handlerName = $order->getAttribute(
                        'monitor_handler_name'
                    );

                    $handlerRole = $order->getAttribute(
                        'monitor_handler_role'
                    );

                    $localRiderName = $lastMile
                        ? trim(
                            $lastMile->first_name
                            . ' '
                            . $lastMile->last_name
                        )
                        : null;

                    $searchText = implode(' ', array_filter([
                        $order->order_number,
                        $order->buyer_name,
                        $order->buyer_address,
                        $order->seller?->store_name,
                        $order->pickup_name,
                        $order->courier_name,
                        $order->courier_email,
                        $localRiderName,
                        $lastMile?->delivery_area,
                        $originName,
                        $destinationName,
                        $transfer?->transfer_code,
                        $transfer?->driver_name,
                        $stageLabel,
                    ]));

                    $journeySteps = [
                        ['Seller Pickup', 'Pickup Rider'],
                        ['Origin Hub', 'Hub Intake'],
                        ['Sorting', 'Sort Zone'],
                        ['Hub Transfer', 'Linehaul'],
                        ['Destination Hub', 'Hub Receipt'],
                        ['Local Rider', 'Last Mile'],
                        ['Buyer', 'Delivery'],
                    ];
                @endphp

                <article
                    class="monitor-order-card"
                    data-monitor-order
                    data-stage-group="{{ $stageGroup }}"
                    data-search="{{ $searchText }}"
                >
                    <header class="monitor-order-header">
                        <div class="monitor-order-identity">
                            <span
                                class="monitor-order-icon"
                                aria-hidden="true"
                            >
                                {{
                                    mb_strtoupper(
                                        mb_substr(
                                            trim(
                                                (string) (
                                                    $order->buyer_name ?: 'O'
                                                )
                                            ),
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </span>

                            <div>
                                <p class="monitor-eyebrow">
                                    MARKETPLACE ORDER
                                </p>

                                <h3>
                                    {{ $order->order_number }}
                                </h3>

                                <p>
                                    {{ $order->seller?->store_name ?: 'SARI Seller' }}
                                    ·
                                    {{ $order->buyer_name }}
                                </p>
                            </div>
                        </div>

                        <div class="monitor-order-status">
                            <span class="monitor-status monitor-status-{{ $stageTone }}">
                                {{ $stageLabel }}
                            </span>

                            <small>
                                Updated {{ $order->updated_at?->diffForHumans() }}
                            </small>
                        </div>
                    </header>

                    {{-- CURRENT OPERATION / HANDLER --}}

                    <div class="monitor-current-state">
                        <div class="dm-operation-cell">
                            <span class="monitor-label">
                                Current Operation
                            </span>

                            <strong>
                                {{ $stageLabel }}
                            </strong>

                            <p>
                                {{ $stageNote }}
                            </p>
                        </div>

                        <div class="dm-operation-cell monitor-current-handler">
                            <span
                                class="monitor-handler-avatar"
                                aria-hidden="true"
                            >
                                {{
                                    mb_strtoupper(
                                        mb_substr(
                                            trim((string) $handlerName),
                                            0,
                                            1
                                        )
                                    ) ?: '—'
                                }}
                            </span>

                            <div class="min-w-0">
                                <span class="monitor-label">
                                    Current Handler
                                </span>

                                <strong>
                                    {{ $handlerName }}
                                </strong>

                                <p>
                                    {{ $handlerRole }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- SEVEN-STEP JOURNEY --}}

                    <div
                        class="monitor-journey-scroll"
                        aria-label="Parcel journey progress"
                    >
                        <div class="monitor-journey">
                            @foreach($journeySteps as $stepIndex => [$stepName, $stepNote])
                                @php
                                    $stepNumber = $stepIndex + 1;

                                    $stepState = $stepNumber < $currentStep
                                        ? 'complete'
                                        : (
                                            $stepNumber === $currentStep
                                                ? 'current'
                                                : 'pending'
                                        );
                                @endphp

                                <div class="monitor-journey-step is-{{ $stepState }}">
                                    <div class="monitor-step-marker">
                                        {{ $stepState === 'complete' ? '✓' : $stepNumber }}
                                    </div>

                                    <div>
                                        <strong>
                                            {{ $stepName }}
                                        </strong>

                                        <span>
                                            {{ $stepNote }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- OPERATION DETAILS --}}

                    <div class="monitor-detail-grid">
                        <div class="monitor-detail">
                            <span class="monitor-label">
                                Pickup Rider
                            </span>

                            <strong>
                                {{ $order->courier_name ?: 'Not assigned' }}
                            </strong>

                            <p>
                                {{ $order->courier_email ?: 'No pickup Rider email' }}
                            </p>
                        </div>

                        <div class="monitor-detail">
                            <span class="monitor-label">
                                Origin Hub
                            </span>

                            <strong>
                                {{ $originName }}
                            </strong>

                            <p>
                                {{
                                    $order->logisticsParcel?->sorting_zone
                                        ? 'Zone ' . $order->logisticsParcel->sorting_zone
                                        : 'Sorting zone not assigned'
                                }}
                            </p>
                        </div>

                        <div class="monitor-detail">
                            <span class="monitor-label">
                                Hub Transfer
                            </span>

                            @if($transfer)
                                <strong>
                                    {{ $transfer->transfer_code }}
                                </strong>

                                <p>
                                    {{ $transfer->vehicle_type }}

                                    @if($transfer->vehicle_plate)
                                        · {{ $transfer->vehicle_plate }}
                                    @endif
                                </p>

                                <p>
                                    Driver: {{ $transfer->driver_name }}
                                </p>
                            @else
                                <strong>
                                    Not yet assigned
                                </strong>

                                <p>
                                    Awaiting linehaul stage
                                </p>
                            @endif
                        </div>

                        <div class="monitor-detail">
                            <span class="monitor-label">
                                Destination Hub
                            </span>

                            <strong>
                                {{ $destinationName }}
                            </strong>

                            <p>
                                {{
                                    $lastMile?->delivery_area
                                        ? 'Delivery area: Barangay ' . $lastMile->delivery_area
                                        : 'Last-mile area pending'
                                }}
                            </p>
                        </div>

                        <div class="monitor-detail">
                            <span class="monitor-label">
                                Local Rider
                            </span>

                            @if($lastMile)
                                <strong>
                                    {{ $localRiderName }}
                                </strong>

                                <p>
                                    {{ $lastMile->vehicle_type ?: 'Vehicle not recorded' }}

                                    @if($lastMile->plate_number)
                                        · {{ $lastMile->plate_number }}
                                    @endif
                                </p>

                                <p>
                                    {{ $lastMile->assignment_code }}
                                </p>
                            @else
                                <strong>
                                    Not yet assigned
                                </strong>

                                <p>
                                    Destination dispatch pending
                                </p>
                            @endif
                        </div>

                        <div class="monitor-detail">
                            <span class="monitor-label">
                                Buyer Destination
                            </span>

                            <strong>
                                {{ $order->buyer_name }}
                            </strong>

                            <p>
                                {{ $order->buyer_address }}
                            </p>
                        </div>
                    </div>

                    {{-- ORDER FOOTER --}}

                    <footer class="monitor-order-footer">
                        <div>
                            <span>
                                Parcel state:
                            </span>

                            <strong>
                                {{
                                    ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $order->logisticsParcel?->status
                                                ?: $order->status
                                        )
                                    )
                                }}
                            </strong>
                        </div>

                        @if($isHubOperator)
                            <span
                                class="monitor-readonly-indicator"
                                title="Journey shown above; route editing is restricted to Main Logistics"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    aria-hidden="true"
                                >
                                    <rect x="5" y="10" width="14" height="11" rx="2"/>
                                    <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                                </svg>
                                Journey details · Read-only
                            </span>
                        @else
                            <a
                                href="{{ route('logistics.delivery-routes.edit', $order) }}"
                                class="monitor-text-link"
                            >
                                View Delivery Route →
                            </a>
                        @endif
                    </footer>
                </article>
            @empty
                <div class="monitor-empty">
                    <span
                        class="monitor-empty-icon"
                        aria-hidden="true"
                    >
                        ✓
                    </span>

                    <h3>
                        No active deliveries
                    </h3>

                    <p>
                        Active Seller pickup, hub, linehaul, and
                        Last-Mile operations will appear here.
                    </p>
                </div>
            @endforelse

            <div
                class="monitor-empty"
                data-monitor-empty
                hidden
            >
                <h3>
                    No matching active deliveries
                </h3>

                <p>
                    Try another order, Buyer, Seller, Rider,
                    hub, or delivery stage.
                </p>

                <button
                    type="button"
                    class="monitor-text-link"
                    data-monitor-clear
                >
                    Clear filters
                </button>
            </div>
        </div>

        <footer class="monitor-list-footer">
            <span
                data-monitor-visible-count
                aria-live="polite"
            >
                {{ $activeOrders->count() }}
                active deliveries shown
            </span>

            <span>
                Seller → Pickup → Hubs → Last Mile → Buyer
            </span>
        </footer>
    </section>

    {{-- DELIVERED TODAY --}}

    <section
        class="monitor-workspace monitor-delivered-workspace"
        aria-labelledby="monitor-delivered-heading"
    >
        <header class="monitor-toolbar">
            <div>
                <p class="monitor-eyebrow">
                    COMPLETED HANDOFFS
                </p>

                <h2 id="monitor-delivered-heading">
                    Delivered Today
                </h2>

                <p>
                    Completed Buyer handoffs for the current day.
                    Showing the latest {{ $deliveredToday->count() }} {{ $deliveredToday->count() === 1 ? 'record' : 'records' }}.
                </p>
            </div>

            <span class="monitor-delivered-total">
                {{ $stats['delivered_today'] }}
                completed today
            </span>
        </header>

        <div class="monitor-delivered-grid">
            @forelse($deliveredToday as $order)
                @php
                    $lastMile = $order->getAttribute(
                        'monitor_last_mile'
                    );

                    $localRiderName = $lastMile
                        ? trim(
                            $lastMile->first_name
                            . ' '
                            . $lastMile->last_name
                        )
                        : null;
                @endphp

                <article class="monitor-delivered-card">
                    <header class="monitor-delivered-header">
                        <div>
                            <span class="monitor-label">
                                Delivered Order
                            </span>

                            <strong>
                                {{ $order->order_number }}
                            </strong>
                        </div>

                        <span
                            class="monitor-delivered-check"
                            aria-hidden="true"
                        >
                            ✓
                        </span>
                    </header>

                    <div class="monitor-delivered-body">
                        <div>
                            <span class="monitor-label">
                                Buyer
                            </span>

                            <strong>
                                {{ $order->buyer_name }}
                            </strong>
                        </div>

                        <div>
                            <span class="monitor-label">
                                Local Rider
                            </span>

                            <strong>
                                {{
                                    $localRiderName
                                        ?: $order->courier_name
                                        ?: 'Rider'
                                }}
                            </strong>
                        </div>

                        <div>
                            <span class="monitor-label">
                                Completed
                            </span>

                            <strong>
                                {{
                                    $order->delivered_at
                                        ?->timezone('Asia/Manila')
                                        ->format('g:i A')
                                        ?: 'Delivered'
                                }}
                            </strong>
                        </div>
                    </div>

                    @if($isHubOperator)
                        <span
                            class="monitor-readonly-indicator"
                            title="Completed journey details are read-only"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                aria-hidden="true"
                            >
                                <rect x="5" y="10" width="14" height="11" rx="2"/>
                                <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                            </svg>
                            Completed journey · Read-only
                        </span>
                    @else
                        <a
                            href="{{ route('logistics.delivery-routes.edit', $order) }}"
                            class="monitor-text-link"
                        >
                            View completed journey →
                        </a>
                    @endif
                </article>
            @empty
                <div class="monitor-empty monitor-empty-small">
                    <h3>
                        No completed deliveries today
                    </h3>

                    <p>
                        Completed Last-Mile deliveries will appear here.
                    </p>
                </div>
            @endforelse
        </div>
    </section>
</div>

@endsection

@push('scripts')
<script>
(() => {
    'use strict';

    function initializeDeliveryMonitoring() {
        const root = document.querySelector(
            '[data-monitor-workspace]'
        );

        if (!root || root.dataset.initialized) {
            return;
        }

        root.dataset.initialized = 'true';

        const cards = [
            ...root.querySelectorAll('[data-monitor-order]')
        ];

        const search = root.querySelector(
            '[data-monitor-search]'
        );

        const tabs = [
            ...root.querySelectorAll('[data-monitor-filter]')
        ];

        const empty = root.querySelector(
            '[data-monitor-empty]'
        );

        const visibleCount = root.querySelector(
            '[data-monitor-visible-count]'
        );

        let filter = 'all';

        const normalize = value =>
            String(value || '')
                .toLocaleLowerCase()
                .trim();

        function applyFilters() {
            const keyword = normalize(search?.value);
            let shown = 0;

            cards.forEach(card => {
                const matchesStage =
                    filter === 'all'
                    || card.dataset.stageGroup === filter;

                const matchesSearch =
                    normalize(card.dataset.search).includes(keyword);

                card.hidden = !matchesStage || !matchesSearch;

                if (!card.hidden) {
                    shown++;
                }
            });

            tabs.forEach(tab => {
                tab.setAttribute(
                    'aria-pressed',
                    String(
                        tab.dataset.monitorFilter === filter
                    )
                );
            });

            if (empty) {
                empty.hidden = cards.length === 0 || shown > 0;
            }

            if (visibleCount) {
                visibleCount.textContent =
                    `${shown} of ${cards.length} `
                    + `${
                        cards.length === 1
                            ? 'active delivery'
                            : 'active deliveries'
                    } shown`;
            }
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                filter = tab.dataset.monitorFilter || 'all';
                applyFilters();
            });
        });

        search?.addEventListener(
            'input',
            applyFilters
        );

        root.querySelectorAll(
            '[data-monitor-clear]'
        ).forEach(button => {
            button.addEventListener('click', () => {
                filter = 'all';

                if (search) {
                    search.value = '';
                }

                applyFilters();
            });
        });

        applyFilters();
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            initializeDeliveryMonitoring
        );
    } else {
        initializeDeliveryMonitoring();
    }
})();
</script>
@endpush