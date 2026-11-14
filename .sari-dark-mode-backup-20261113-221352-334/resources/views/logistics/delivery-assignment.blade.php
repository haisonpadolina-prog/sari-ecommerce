
@extends('layouts.logistics')

@section('title', 'Pickup Assignment — SARI Logistics')
@section('page-title', 'Pickup Assignment')

@push('styles')
<style>
/* =========================================================
   MERGED FROM public/css/logistics/pickup.css
   Kept page-scoped/shared exactly as the original stylesheet.
========================================================= */
/*
|--------------------------------------------------------------------------
| SARI LOGISTICS — PICKUP WORKSPACE
|--------------------------------------------------------------------------
|
| Shared visual system for:
| - Pickup Requests
| - Pickup Assignment
|
| Visual direction follows the Seller workspace:
| - Poppins
| - charcoal / white / SARI gold
| - compact cards
| - subtle borders and shadows
| - restrained status colors
|
*/


/* ==========================================================================
   ROOT / TOKENS
   ========================================================================== */

.pickup-page {
    --pickup-bg: #f4f5f7;

    --pickup-surface: #ffffff;
    --pickup-surface-soft: #fcfaf7;
    --pickup-surface-warm: #fffaf1;

    --pickup-ink: #2f2a23;
    --pickup-ink-soft: #51483d;
    --pickup-muted: #8f877d;
    --pickup-muted-light: #aaa198;

    --pickup-line: #e8e2da;
    --pickup-line-soft: #f0ebe4;

    --pickup-gold: #d89b10;
    --pickup-gold-dark: #b97805;
    --pickup-gold-soft: #fff6df;

    --pickup-green: #327a53;
    --pickup-green-soft: #f1faf5;
    --pickup-green-line: #c9e0d2;

    --pickup-red: #b75b55;
    --pickup-red-soft: #fff5f4;
    --pickup-red-line: #ebd1ce;

    --pickup-blue: #4f7899;
    --pickup-blue-soft: #f1f6fb;
    --pickup-blue-line: #d7e3ed;

    --pickup-shadow:
        0 6px 18px rgba(33, 24, 14, .03);

    --pickup-shadow-hover:
        0 14px 30px rgba(43, 33, 21, .055);

    --pickup-radius-lg: 19px;
    --pickup-radius-md: 13px;
    --pickup-radius-sm: 9px;

    width: 100%;
    max-width: 1800px;
    margin-inline: auto;
    padding-bottom: 26px;

    color: var(--pickup-ink);

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


/* ==========================================================================
   LOCAL RESET
   ========================================================================== */

.pickup-page *,
.pickup-page *::before,
.pickup-page *::after {
    box-sizing: border-box;
}


.pickup-page [hidden] {
    display: none !important;
}


.pickup-page button,
.pickup-page input,
.pickup-page select,
.pickup-page textarea {
    font: inherit;
}


.pickup-page button,
.pickup-page summary {
    cursor: pointer;
}


.pickup-page button:disabled,
.pickup-page select:disabled,
.pickup-page input:disabled {
    cursor: not-allowed;
    opacity: .5;
}


.pickup-page a {
    color: inherit;
}


.pickup-page input[type="checkbox"] {
    width: 15px;
    height: 15px;
    flex: 0 0 15px;

    margin: 0;

    accent-color: var(--pickup-gold);

    cursor: pointer;
}


.pickup-page input[type="checkbox"]:disabled {
    cursor: not-allowed;
    opacity: .38;
}


.pickup-page :is(
    button,
    a,
    input,
    select,
    textarea,
    summary
):focus-visible {
    outline: 2px solid rgba(216, 155, 16, .72);
    outline-offset: 2px;
}


/* ==========================================================================
   PAGE HERO
   ========================================================================== */

.pickup-page-header {
    position: relative;

    display: flex;
    align-items: center;
    justify-content: space-between;

    min-height: 116px;

    gap: 24px;

    margin: 0 0 14px;
    padding: 17px 20px;

    overflow: hidden;

    border: 1px solid #2f2a23;
    border-radius: var(--pickup-radius-lg);

    background:
        radial-gradient(
            circle at 100% 50%,
            rgba(216, 155, 16, .13) 0,
            rgba(216, 155, 16, .06) 21%,
            rgba(216, 155, 16, 0) 42%
        ),
        linear-gradient(
            135deg,
            #211f1c 0%,
            #25231f 48%,
            #1b1917 100%
        );

    box-shadow:
        0 8px 22px rgba(20, 17, 13, .08);
}


.pickup-page-header::before {
    content: "";

    position: absolute;
    inset: 0;

    pointer-events: none;

    background:
        linear-gradient(
            90deg,
            rgba(255, 255, 255, .035),
            rgba(255, 255, 255, 0) 38%
        );
}


.pickup-page-header::after {
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


.pickup-page-header > * {
    position: relative;
    z-index: 1;
}


.pickup-eyebrow {
    margin: 0 0 5px;

    color: #c5a35c;

    font-size: 7px;
    line-height: 1.2;
    font-weight: 600;

    letter-spacing: .15em;
    text-transform: uppercase;
}


.pickup-page-header .pickup-eyebrow {
    color: rgba(226, 161, 15, .92);
}


.pickup-page h1 {
    margin: 0;

    color: #ffffff;

    font-size: clamp(19px, 1.55vw, 23px);
    line-height: 1.12;

    font-weight: 600;
    letter-spacing: -.038em;
}


.pickup-page h1 span {
    color: #e2a10f;
}


.pickup-subtitle {
    max-width: 650px;

    margin: 6px 0 0;

    color: rgba(255, 255, 255, .57);

    font-size: 8.5px;
    line-height: 1.65;
    font-weight: 400;
}


/* ==========================================================================
   BUTTONS
   ========================================================================== */

.pickup-button {
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

    box-shadow: none;

    transition:
        background-color .16s ease,
        border-color .16s ease,
        color .16s ease,
        box-shadow .16s ease,
        transform .16s ease;
}


.pickup-button:hover {
    transform: translateY(-1px);

    border-color: #d7c49c;

    background: #fffaf1;

    color: #946611;
}


.pickup-button-primary {
    border-color: var(--pickup-gold);

    background: var(--pickup-gold);

    color: #ffffff;

    box-shadow:
        0 5px 13px rgba(215, 144, 4, .14);
}


.pickup-button-primary:hover {
    border-color: #c68908;
    background: #c68908;
    color: #ffffff;
}


.pickup-button-primary:active {
    transform: translateY(0);
}


.pickup-page-header .pickup-button:not(.pickup-button-primary) {
    border-color: rgba(255, 255, 255, .13);

    background: rgba(255, 255, 255, .055);

    color: rgba(255, 255, 255, .82);

    backdrop-filter: blur(5px);
}


.pickup-page-header .pickup-button:not(.pickup-button-primary):hover {
    border-color: rgba(226, 161, 15, .32);

    background: rgba(216, 155, 16, .08);

    color: #f0ba45;
}


/* ==========================================================================
   ALERTS
   ========================================================================== */

.pickup-alert {
    position: relative;

    margin: 0 0 14px;
    padding: 13px 16px;

    overflow: hidden;

    border: 1px solid;
    border-radius: 14px;

    box-shadow:
        0 5px 16px rgba(39, 31, 22, .025);
}


.pickup-alert strong {
    display: block;

    margin: 0;

    font-size: 9px;
    line-height: 1.4;
    font-weight: 600;
}


.pickup-alert p {
    margin: 3px 0 0;

    font-size: 8px;
    line-height: 1.55;
}


.pickup-alert ul {
    margin: 5px 0 0;
    padding-left: 18px;

    font-size: 8px;
}


.pickup-alert a {
    font-weight: 600;

    text-decoration: underline;
    text-underline-offset: 3px;
}


.pickup-alert-success {
    color: #427259;

    border-color: var(--pickup-green-line);

    background: var(--pickup-green-soft);
}


.pickup-alert-danger {
    color: #a65353;

    border-color: var(--pickup-red-line);

    background: var(--pickup-red-soft);
}


.pickup-alert-warning {
    color: #977020;

    border-color: #ead9b5;

    background: #fff9ec;
}


/* ==========================================================================
   SUMMARY CARDS
   ========================================================================== */

.pickup-stats {
    display: grid;

    grid-template-columns:
        repeat(4, minmax(0, 1fr));

    gap: 10px;
}


.pickup-stat {
    position: relative;

    min-width: 0;

    padding: 14px 15px 13px;

    overflow: hidden;

    border: 1px solid #e8e2da;
    border-radius: 16px;

    background: #ffffff;

    box-shadow:
        0 5px 16px rgba(39, 31, 22, .025);

    transition:
        transform .16s ease,
        border-color .16s ease,
        box-shadow .16s ease;
}


.pickup-stat:hover {
    transform: translateY(-1px);

    border-color: #d8ccb9;

    box-shadow: var(--pickup-shadow-hover);
}


.pickup-stat::after {
    content: "";

    position: absolute;

    right: 13px;
    top: 13px;

    width: 6px;
    height: 6px;

    border-radius: 999px;

    background: #d7d0c7;
}


.pickup-stat p {
    margin: 0;

    color: #81786e;

    font-size: 7px;
    line-height: 1.25;
    font-weight: 600;

    letter-spacing: .085em;
    text-transform: uppercase;
}


.pickup-stat strong {
    display: block;

    margin: 5px 0 2px;

    color: #302a23;

    font-size: 23px;
    line-height: 1.12;
    font-weight: 600;

    letter-spacing: -.035em;
}


.pickup-stat span {
    display: block;

    color: #978f85;

    font-size: 7px;
    line-height: 1.45;
}


.pickup-stat-neutral::after {
    background: #a79e93;
}


.pickup-stat-amber {
    border-color: #eadfc9;
}


.pickup-stat-amber::after {
    background: #d89b10;
}


.pickup-stat-amber strong {
    color: #ad7713;
}


.pickup-stat-green {
    border-color: #d5e5da;
}


.pickup-stat-green::after {
    background: #63a375;
}


.pickup-stat-green strong {
    color: #4f7c5b;
}


.pickup-stat-red {
    border-color: #ead8d5;
}


.pickup-stat-red::after {
    background: #d97c6c;
}


.pickup-stat-red strong {
    color: #a95e57;
}


.pickup-stat-blue {
    border-color: #d7e3ed;
}


.pickup-stat-blue::after {
    background: #6c8fb5;
}


.pickup-stat-blue strong {
    color: #587b99;
}


/* ==========================================================================
   WORKFLOW STEPPER
   ========================================================================== */

.pickup-flow {
    display: flex;
    align-items: center;

    gap: 4px;

    margin: 13px 0;

    padding: 5px;

    width: fit-content;
    max-width: 100%;

    border: 1px solid var(--pickup-line);
    border-radius: 11px;

    background: #ffffff;

    box-shadow:
        0 4px 14px rgba(42, 32, 21, .022);
}


.pickup-flow :is(a, span) {
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

    transition:
        background-color .15s ease,
        color .15s ease;
}


.pickup-flow a:hover {
    background: #faf7f1;

    color: #796647;
}


.pickup-flow b {
    display: grid;

    width: 20px;
    height: 20px;

    place-items: center;

    flex: 0 0 20px;

    border: 1px solid #e5ded5;
    border-radius: 999px;

    background: #ffffff;

    color: #948a7f;

    font-size: 6.5px;
    font-weight: 600;
}


.pickup-flow .is-current {
    background: #fff6df;

    color: #9c6a0e;

    font-weight: 600;
}


.pickup-flow .is-current b {
    border-color: #e4c681;

    background: #e3a41a;

    color: #ffffff;
}


/* ==========================================================================
   MAIN WORKSPACE CARD
   ========================================================================== */

.pickup-workspace {
    overflow: hidden;

    border: 1px solid var(--pickup-line);
    border-radius: var(--pickup-radius-lg);

    background: #ffffff;

    box-shadow: var(--pickup-shadow);
}


/* ==========================================================================
   TOOLBAR
   ========================================================================== */

.pickup-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 18px;

    padding: 16px 18px 13px;
}


.pickup-toolbar h2 {
    margin: 0;

    color: #302a23;

    font-size: 13px;
    line-height: 1.3;
    font-weight: 600;

    letter-spacing: -.02em;
}


.pickup-toolbar p {
    margin: 3px 0 0;

    color: var(--pickup-muted);

    font-size: 7.5px;
    line-height: 1.5;
}


.pickup-search {
    width: min(280px, 100%);

    flex-shrink: 0;
}


.pickup-search input {
    width: 100%;
    height: 36px;

    padding: 8px 11px;

    border: 1px solid #e5ded5;
    border-radius: 9px;

    background: #ffffff;

    color: var(--pickup-ink);

    font-size: 7.5px;

    transition:
        border-color .15s ease,
        box-shadow .15s ease,
        background-color .15s ease;
}


.pickup-search input::placeholder {
    color: #aaa198;
}


.pickup-search input:focus {
    border-color: #d6b46d;

    background: #fffefa;

    box-shadow:
        0 0 0 4px rgba(216, 155, 16, .075);

    outline: none;
}


/* ==========================================================================
   FILTER TABS
   ========================================================================== */

.pickup-filter-row {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 12px;

    padding: 0 18px 13px;

    border-bottom: 1px solid var(--pickup-line);
}


.pickup-tabs {
    display: flex;
    flex-wrap: wrap;

    gap: 5px;
}


.pickup-tabs button {
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
    font-weight: 500;

    white-space: nowrap;

    transition:
        background-color .15s ease,
        border-color .15s ease,
        color .15s ease;
}


.pickup-tabs button:hover {
    background: #faf7f1;

    color: #675d51;
}


.pickup-tabs button[aria-pressed="true"] {
    border-color: #ead7b3;

    background: #fff6e5;

    color: #a07121;

    font-weight: 600;
}


.pickup-tabs span,
.pickup-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 21px;
    height: 18px;

    padding-inline: 5px;

    border-radius: 6px;

    background: #f0ece6;

    color: #81786e;

    font-size: 6.5px;
    line-height: 1;
    font-weight: 600;
}


.pickup-tabs button[aria-pressed="true"] span {
    background: #ecd8ad;

    color: #8a5b0d;
}


/* ==========================================================================
   TEXT ACTION LINK
   ========================================================================== */

.pickup-text-link {
    display: inline-flex;
    align-items: center;

    padding: 0;

    border: 0;

    background: transparent;

    color: #b2770f;

    font-size: 7.5px;
    font-weight: 600;

    text-decoration: none;

    transition:
        color .15s ease,
        opacity .15s ease;
}


.pickup-text-link:hover {
    color: #84580a;

    text-decoration: underline;
    text-underline-offset: 3px;
}


/* ==========================================================================
   BULK SELECTION BAR
   ========================================================================== */

.pickup-selection-bar {
    display: flex;
    align-items: center;

    min-height: 48px;

    gap: 14px;

    padding: 8px 18px;

    border-bottom: 1px solid var(--pickup-line);

    background: #faf9f6;
}


.pickup-selection-bar label,
.pickup-parcel-heading label {
    display: flex;
    align-items: center;

    gap: 7px;

    color: #71685f;

    font-size: 7.5px;
    font-weight: 500;
}


.pickup-selection-bar > span {
    color: #9a9187;

    font-size: 7px;
}


.pickup-selection-bar .pickup-button {
    min-height: 32px;

    margin-left: auto;

    padding: 6px 11px;

    font-size: 7px;
}


/* ==========================================================================
   REQUEST TABLE
   ========================================================================== */

.pickup-table-scroll {
    width: 100%;

    overflow: auto;
}


.pickup-table {
    width: 100%;

    border-collapse: collapse;

    text-align: left;

    font-size: 8px;
}


.pickup-table thead {
    background: #fcfbf9;
}


.pickup-table th {
    padding: 10px 14px;

    border-bottom: 1px solid var(--pickup-line);

    color: #988f85;

    font-size: 6.5px;
    line-height: 1.3;
    font-weight: 600;

    letter-spacing: .075em;
    text-transform: uppercase;

    white-space: nowrap;
}


.pickup-table td {
    min-width: 125px;

    padding: 13px 14px;

    border-bottom: 1px solid var(--pickup-line-soft);

    vertical-align: top;

    color: #61584f;
}


.pickup-table tbody tr {
    transition:
        background-color .14s ease;
}


.pickup-table tbody tr:hover {
    background: #fffdf9;
}


.pickup-table tbody tr:last-child td {
    border-bottom: 0;
}


.pickup-table td:first-child:has(input[type="checkbox"]) {
    width: 40px;
    min-width: 40px;

    padding-right: 0;
}


.pickup-table td strong {
    display: block;

    color: #443c33;

    font-size: 8px;
    line-height: 1.45;
    font-weight: 600;
}


.pickup-table td p {
    margin: 3px 0 0;

    color: #91887e;

    font-size: 7px;
    line-height: 1.5;
}


.pickup-table td .pickup-address {
    min-width: 160px;
    max-width: 250px;

    color: #837a70;

    line-height: 1.65;
}


.pickup-table td:last-child {
    min-width: 170px;
}


.pickup-table td .pickup-text-link {
    margin-top: 7px;
}


/* ==========================================================================
   STATUS BADGES
   ========================================================================== */

.pickup-badge {
    display: inline-flex;
    align-items: center;

    min-height: 23px;

    gap: 5px;

    padding: 4px 8px;

    border: 1px solid;
    border-radius: 999px;

    font-size: 6.5px;
    line-height: 1;
    font-weight: 600;

    white-space: nowrap;
}


.pickup-badge-pending {
    color: #9a6b16;

    border-color: #ead9b2;

    background: #fff8e8;
}


.pickup-badge-verified {
    color: #4e7a59;

    border-color: #d4e4d8;

    background: #f0f8f2;
}


.pickup-badge-attention {
    color: #a85f58;

    border-color: #ecd5cf;

    background: #fff4f2;
}


.pickup-badge-active {
    color: #587892;

    border-color: #d7e3ed;

    background: #f1f6fa;
}


/* ==========================================================================
   ISSUES
   ========================================================================== */

.pickup-issues {
    max-width: 270px;

    margin: 6px 0 0;
    padding-left: 14px;

    color: #a9695c;

    font-size: 7px;
    line-height: 1.55;
}


.pickup-issues li + li {
    margin-top: 3px;
}


/* ==========================================================================
   REQUEST DETAILS
   ========================================================================== */

.pickup-details {
    margin-top: 8px;
}


.pickup-details summary {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 10px;

    list-style: none;

    color: #9d752f;

    font-size: 7px;
    font-weight: 500;

    user-select: none;
}


.pickup-details summary::-webkit-details-marker {
    display: none;
}


.pickup-details summary span {
    display: inline-grid;

    width: 18px;
    height: 18px;

    place-items: center;

    border-radius: 6px;

    background: #f7f1e7;

    transition:
        transform .17s ease;
}


.pickup-details[open] summary {
    margin-bottom: 8px;
}


.pickup-details[open] summary span {
    transform: rotate(45deg);
}


.pickup-detail-body {
    min-width: 190px;

    padding: 11px;

    overflow-wrap: anywhere;

    border: 1px solid #e8dfd2;
    border-radius: 10px;

    background: #faf8f4;
}


.pickup-detail-grid {
    display: grid;

    gap: 10px;
}


.pickup-detail-grid strong {
    display: block;

    color: #50473d;

    font-size: 7px;
    line-height: 1.45;
    font-weight: 600;
}


.pickup-detail-grid p {
    margin: 3px 0 0;

    color: #8f8276;

    font-size: 6.5px;
    line-height: 1.55;
}


.pickup-label {
    display: block;

    margin-bottom: 4px;

    color: #a08e75;

    font-size: 6px;
    line-height: 1.2;
    font-weight: 600;

    letter-spacing: .08em;
    text-transform: uppercase;
}


/* ==========================================================================
   PRODUCT DETAILS
   ========================================================================== */

.pickup-products {
    list-style: none;

    margin: 9px 0 0;
    padding: 0;

    border-top: 1px solid #e7ded0;
}


.pickup-products li {
    display: flex;
    justify-content: space-between;

    gap: 15px;

    padding-top: 8px;

    color: #6f665d;

    font-size: 6.5px;
    line-height: 1.5;
}


.pickup-products li strong {
    flex-shrink: 0;

    color: #4f473f;

    font-size: inherit;
}


/* ==========================================================================
   EMPTY STATES
   ========================================================================== */

.pickup-empty {
    padding: 42px 20px;

    text-align: center;
}


.pickup-empty h3 {
    margin: 0 0 6px;

    color: #5e554b;

    font-size: 11px;
    line-height: 1.35;
    font-weight: 600;
}


.pickup-empty p {
    max-width: 430px;

    margin: 0 auto 14px;

    color: #999086;

    font-size: 7.5px;
    line-height: 1.6;
}


.pickup-empty-icon {
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


.pickup-empty-small {
    padding: 26px 20px;
}


/* ==========================================================================
   LIST FOOTER
   ========================================================================== */

.pickup-list-footer {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;

    gap: 10px;

    padding: 10px 18px;

    border-top: 1px solid var(--pickup-line);

    background: #fcfbf9;

    color: #9a9288;

    font-size: 6.5px;
}


/* ==========================================================================
   PICKUP ASSIGNMENT — BATCH LIST
   ========================================================================== */

.pickup-batches {
    display: grid;

    gap: 12px;

    padding: 14px;

    background: #f7f7f6;
}


.pickup-batches:empty {
    display: none;
}


.pickup-batch {
    overflow: hidden;

    border: 1px solid var(--pickup-line);
    border-radius: 16px;

    background: #ffffff;

    box-shadow:
        0 4px 14px rgba(43, 33, 21, .022);

    transition:
        border-color .16s ease,
        box-shadow .16s ease,
        transform .16s ease;
}


.pickup-batch:hover {
    border-color: #d8ccb9;

    box-shadow:
        0 10px 26px rgba(43, 33, 21, .045);
}


.pickup-batch[data-focused="true"] {
    border-color: #d7b767;

    box-shadow:
        0 0 0 2px rgba(216, 155, 16, .16),
        0 8px 24px rgba(43, 33, 21, .04);
}


/* ==========================================================================
   BATCH HEADER
   ========================================================================== */

.pickup-batch-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 12px;

    min-height: 66px;

    padding: 13px 16px;

    border-bottom: 1px solid var(--pickup-line);

    background: #ffffff;
}


.pickup-seller-heading {
    display: flex;
    align-items: center;

    min-width: 0;

    gap: 11px;
}


.pickup-avatar {
    display: grid;

    width: 36px;
    height: 36px;

    place-items: center;

    flex: 0 0 36px;

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


.pickup-seller-heading .pickup-eyebrow {
    margin-bottom: 2px;

    color: #a68c61;

    font-size: 5.8px;
}


.pickup-seller-heading h3 {
    margin: 0;

    overflow: hidden;

    color: #373029;

    font-size: 10px;
    line-height: 1.4;
    font-weight: 600;

    text-overflow: ellipsis;
    white-space: nowrap;
}


.pickup-batch-tags {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: 7px;

    color: #8f8579;

    font-size: 6.5px;
}


.pickup-batch-tags > * {
    display: inline-flex;
    align-items: center;

    min-height: 23px;

    padding: 4px 8px;

    border: 1px solid #e9e2d8;
    border-radius: 999px;

    background: #faf8f4;
}


/* ==========================================================================
   BATCH LAYOUT
   ========================================================================== */

.pickup-batch-layout {
    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        270px;

    min-width: 0;
}


.pickup-batch-main {
    min-width: 0;

    padding: 15px 16px;
}


/* ==========================================================================
   ROUTE SUMMARY
   ========================================================================== */

.pickup-route-summary {
    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        auto
        minmax(0, 1fr);

    align-items: start;

    gap: 14px;

    padding: 0 0 14px;

    border-bottom: 1px solid var(--pickup-line-soft);
}


.pickup-route-summary strong {
    display: block;

    color: #4b433a;

    font-size: 8px;
    line-height: 1.5;
    font-weight: 600;
}


.pickup-route-summary p {
    margin: 3px 0 0;

    color: #91877b;

    font-size: 7px;
    line-height: 1.55;
}


.pickup-route-arrow {
    display: grid;

    width: 27px;
    height: 27px;

    place-items: center;

    margin-top: 17px;

    border: 1px solid #eadfcf;
    border-radius: 999px;

    background: #fffaf1;

    color: #c4963f;

    font-size: 13px;
}


/* ==========================================================================
   PARCEL HEADING
   ========================================================================== */

.pickup-parcel-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 12px;

    margin: 13px 0 9px;
}


.pickup-parcel-heading h4 {
    margin: 0;

    color: #4c443b;

    font-size: 8px;
    line-height: 1.4;
    font-weight: 600;
}


/* ==========================================================================
   PARCEL LIST
   ========================================================================== */

.pickup-batch-parcels {
    display: grid;

    gap: 7px;
}


.pickup-parcel {
    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        auto;

    gap: 0 10px;

    padding: 10px;

    border: 1px solid #ece6de;
    border-radius: 10px;

    background: #ffffff;

    transition:
        border-color .15s ease,
        background-color .15s ease;
}


.pickup-parcel:hover {
    border-color: #dfd4c5;

    background: #fffdf9;
}


.pickup-parcel > label {
    display: flex;
    align-items: flex-start;

    min-width: 0;

    gap: 9px;
}


.pickup-parcel > label input {
    margin-top: 2px;
}


.pickup-parcel strong {
    color: #50483f;

    font-size: 7.5px;
    line-height: 1.45;
    font-weight: 600;
}


.pickup-parcel small {
    display: block;

    margin-top: 2px;

    color: #978d80;

    font-size: 6.5px;
    line-height: 1.5;
}


.pickup-parcel-value {
    color: #51483d;

    font-size: 7.5px;
    font-weight: 600;

    white-space: nowrap;
}


.pickup-parcel .pickup-details {
    grid-column: 1 / -1;
}


.pickup-parcel .pickup-detail-grid {
    grid-template-columns:
        repeat(2, minmax(0, 1fr));
}


/* ==========================================================================
   DISPATCH PANEL
   ========================================================================== */

.pickup-dispatch-panel {
    min-width: 0;

    padding: 16px;

    border-left: 1px solid var(--pickup-line);

    background:
        linear-gradient(
            180deg,
            #fffdf8 0%,
            #fbf8f1 100%
        );
}


.pickup-dispatch-panel h4 {
    margin: 0;

    color: #383129;

    font-size: 10px;
    line-height: 1.35;
    font-weight: 600;
}


.pickup-dispatch-note {
    margin: 5px 0 14px;

    color: #918679;

    font-size: 7px;
    line-height: 1.55;
}


.pickup-dispatch-panel select {
    width: 100%;
    min-height: 38px;

    padding: 8px 10px;

    border: 1px solid #dfd5c5;
    border-radius: 9px;

    background: #ffffff;

    color: #5f5548;

    font-size: 7.5px;

    outline: none;

    transition:
        border-color .15s ease,
        box-shadow .15s ease;
}


.pickup-dispatch-panel select:focus {
    border-color: #d4ad61;

    box-shadow:
        0 0 0 4px rgba(216, 155, 16, .075);
}


/* ==========================================================================
   RIDER PREVIEW
   ========================================================================== */

.pickup-rider-preview {
    margin-top: 10px;
    padding: 10px;

    border: 1px solid #e6dccb;
    border-radius: 10px;

    background: rgba(255, 255, 255, .75);
}


.pickup-rider-preview strong {
    color: #443c33;

    font-size: 8px;
    line-height: 1.45;
    font-weight: 600;
}


.pickup-rider-preview p {
    margin: 2px 0;

    color: #8d8172;

    font-size: 6.5px;
    line-height: 1.5;
}


/* ==========================================================================
   DISPATCH TOTAL
   ========================================================================== */

.pickup-dispatch-total {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 10px;

    padding: 13px 0 10px;

    color: #93856f;

    font-size: 7px;
}


.pickup-dispatch-total strong {
    color: #9e721b;

    font-size: 16px;
    line-height: 1;
    font-weight: 600;

    letter-spacing: -.025em;
}


.pickup-dispatch-panel .pickup-button {
    width: 100%;

    min-height: 37px;

    font-size: 7.5px;
}


.pickup-dispatch-footnote {
    margin: 8px 0 0;

    color: #9e917e;

    font-size: 6px;
    line-height: 1.5;
}


/* ==========================================================================
   ACTIVE ASSIGNMENTS
   ========================================================================== */

.pickup-active {
    margin-top: 14px;
}


/* ==========================================================================
   SHARED SUBTLE MOTION
   ========================================================================== */

.pickup-workspace,
.pickup-stat,
.pickup-batch,
.pickup-alert {
    transform: translateZ(0);
}


/* ==========================================================================
   LARGE DESKTOP
   ========================================================================== */

@media (min-width: 1500px) {

    .pickup-page {
        font-size: 11.5px;
    }


    .pickup-page-header {
        padding-inline: 22px;
    }


    .pickup-table th {
        padding-inline: 16px;
    }


    .pickup-table td {
        padding-inline: 16px;
    }


    .pickup-batch-layout {
        grid-template-columns:
            minmax(0, 1fr)
            285px;
    }
}


/* ==========================================================================
   LAPTOP / MEDIUM DESKTOP
   ========================================================================== */

@media (max-width: 1200px) {

    .pickup-page-header {
        min-height: 108px;
    }


    .pickup-batch-layout {
        grid-template-columns:
            minmax(0, 1fr)
            245px;
    }


    .pickup-batch-main,
    .pickup-dispatch-panel {
        padding: 14px;
    }


    .pickup-table th,
    .pickup-table td {
        padding-left: 12px;
        padding-right: 12px;
    }
}


/* ==========================================================================
   TABLET
   ========================================================================== */

@media (max-width: 900px) {

    .pickup-page-header {
        align-items: flex-start;

        min-height: 0;

        padding: 16px;
    }


    .pickup-page-header::after {
        opacity: .65;
    }


    .pickup-stats {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }


    .pickup-batch-layout {
        grid-template-columns: 1fr;
    }


    .pickup-dispatch-panel {
        border-top: 1px solid var(--pickup-line);
        border-left: 0;
    }
}


/* ==========================================================================
   MOBILE
   ========================================================================== */

@media (max-width: 767px) {

    .pickup-page {
        padding-bottom: 18px;
    }


    .pickup-page-header,
    .pickup-toolbar {
        flex-direction: column;
        align-items: stretch;

        gap: 13px;
    }


    .pickup-page-header {
        border-radius: 17px;
    }


    .pickup-page-header .pickup-button {
        align-self: flex-start;
    }


    .pickup-page h1 {
        font-size: 19px;
    }


    .pickup-subtitle {
        font-size: 7.5px;
    }


    .pickup-stats {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 8px;
    }


    .pickup-stat {
        padding: 12px;
    }


    .pickup-stat strong {
        font-size: 20px;
    }


    .pickup-flow {
        width: 100%;

        overflow-x: auto;

        gap: 3px;
    }


    .pickup-flow :is(a, span) {
        flex-shrink: 0;

        padding-inline: 7px;

        font-size: 7px;
    }


    .pickup-toolbar {
        padding: 14px;
    }


    .pickup-search {
        width: 100%;
    }


    .pickup-filter-row {
        align-items: flex-start;

        padding:
            0
            14px
            12px;
    }


    .pickup-filter-row > .pickup-text-link {
        margin-top: 6px;

        white-space: nowrap;
    }


    .pickup-selection-bar {
        flex-wrap: wrap;

        gap: 9px;

        padding: 10px 12px;
    }


    .pickup-selection-bar .pickup-button {
        margin-left: 0;
    }


    .pickup-batches {
        padding: 9px;
    }


    .pickup-batch-header {
        flex-wrap: wrap;

        padding: 12px;
    }


    .pickup-batch-tags {
        width: 100%;

        margin-left: 47px;
    }


    .pickup-batch-main,
    .pickup-dispatch-panel {
        padding: 12px;
    }


    .pickup-route-summary {
        gap: 8px;
    }


    .pickup-route-arrow {
        width: 24px;
        height: 24px;

        margin-top: 16px;

        font-size: 11px;
    }


    .pickup-parcel .pickup-detail-grid {
        grid-template-columns: 1fr;
    }


    .pickup-list-footer {
        padding-inline: 14px;
    }
}


/* ==========================================================================
   SMALL MOBILE
   ========================================================================== */

@media (max-width: 520px) {

    .pickup-page-header {
        padding: 15px 14px;
    }


    .pickup-page-header > .pickup-button {
        width: 100%;
    }


    .pickup-stats {
        grid-template-columns: 1fr 1fr;
    }


    .pickup-stat p {
        font-size: 6.3px;
    }


    .pickup-stat span {
        font-size: 6.4px;
    }


    .pickup-flow {
        border-radius: 10px;
    }


    .pickup-filter-row {
        flex-direction: column;

        gap: 8px;
    }


    .pickup-selection-bar {
        align-items: flex-start;
    }


    .pickup-selection-bar .pickup-button {
        width: 100%;
    }


    .pickup-route-summary {
        grid-template-columns: 1fr;
    }


    .pickup-route-arrow {
        margin: 0;

        transform: rotate(90deg);
    }


    .pickup-batch-tags {
        margin-left: 0;
    }


    .pickup-parcel {
        grid-template-columns: 1fr;
    }


    .pickup-parcel-value {
        margin-top: 5px;
        padding-left: 24px;
    }
}


/* ==========================================================================
   REDUCED MOTION
   ========================================================================== */

@media (prefers-reduced-motion: reduce) {

    .pickup-page *,
    .pickup-page *::before,
    .pickup-page *::after {
        scroll-behavior: auto !important;
        transition: none !important;
        animation: none !important;
    }
}
</style>

<style>
/*
|--------------------------------------------------------------------------
| SARI LOGISTICS — PICKUP ASSIGNMENT
| Seller-style UI
|--------------------------------------------------------------------------
| Page-scoped overrides only.
| The shared pickup.css and Pickup Requests page remain unchanged.
*/

#lgPickupAssignment {
    --pickup-gold: #d9950b;
    --pickup-gold-dark: #c88708;
    --pickup-ink: #25211c;
    --pickup-line: #e8e2d9;
    --pickup-shadow: 0 6px 18px rgba(33,24,14,.035);

    max-width: 1620px;
    font-size: 10px;
}

#lgPickupAssignment [hidden] {
    display: none !important;
}

/* =========================================================
   PAGE HEADING
========================================================= */

#lgPickupAssignment .pickup-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;

    min-height: 0;
    margin-bottom: 14px;
    padding: 5px 2px 17px;

    overflow: visible;

    border: 0;
    border-bottom: 1px solid #e4e1dc;
    border-radius: 0;

    background: transparent;
    box-shadow: none;
}

#lgPickupAssignment .pickup-page-header::before,
#lgPickupAssignment .pickup-page-header::after {
    display: none;
}

#lgPickupAssignment .pickup-page-header .pickup-eyebrow {
    margin-bottom: 5px;
    color: #a76f11;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
}

#lgPickupAssignment .pickup-page-header h1 {
    color: #24201b;
    font-size: clamp(23px,2.15vw,30px);
    font-weight: 750;
    letter-spacing: -.045em;
    line-height: 1.2;
}

#lgPickupAssignment .pickup-page-header h1 span {
    color: #d9950b;
}

#lgPickupAssignment .pickup-subtitle {
    max-width: 730px;
    margin-top: 6px;
    color: #897f75;
    font-size: 10px;
    line-height: 1.7;
}

/* HEADER BUTTON */

#lgPickupAssignment .pickup-page-header .pickup-button,
#lgPickupAssignment .pickup-page-header .pickup-button:not(.pickup-button-primary) {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-height: 39px;
    padding: 0 16px;

    flex-shrink: 0;

    border: 1px solid #e4d8c6;
    border-radius: 10px;

    background: #fff;
    color: #776a5a;

    font-size: 10px;
    font-weight: 650;

    box-shadow: none;
    backdrop-filter: none;

    transition:
        background .15s ease,
        border-color .15s ease,
        color .15s ease;
}

#lgPickupAssignment .pickup-page-header .pickup-button:hover,
#lgPickupAssignment .pickup-page-header .pickup-button:not(.pickup-button-primary):hover {
    background: #fff9ed;
    border-color: #d5bb87;
    color: #a36c0a;
}

/* =========================================================
   KPI CARDS
========================================================= */

#lgPickupAssignment .pickup-stats {
    grid-template-columns: repeat(4,minmax(0,1fr));
    gap: 12px;
}

#lgPickupAssignment .pickup-stat {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;

    min-width: 0;
    min-height: 97px;
    gap: 10px;
    padding: 14px;

    overflow: visible;

    border: 1px solid #e8e2d9;
    border-radius: 16px;

    background: white;
    box-shadow: var(--pickup-shadow);

    transition:
        transform .18s cubic-bezier(.22,1,.36,1),
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgPickupAssignment .pickup-stat::after {
    display: none;
}

#lgPickupAssignment .pickup-stat p {
    color: #756b60;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: normal;
    text-transform: none;
}

#lgPickupAssignment .pickup-stat strong {
    display: block;
    margin: 7px 0 4px;

    color: #211d18;
    font-size: 25px;
    font-weight: 750;
    line-height: 1.15;
    letter-spacing: -.045em;
}

#lgPickupAssignment .pickup-stat span:not(.pickup-stat-icon) {
    color: #8d8378;
    font-size: 9px;
    line-height: 1.45;
}

#lgPickupAssignment .pickup-stat-icon {
    display: grid;
    place-items: center;
    flex: 0 0 41px;

    width: 41px;
    height: 41px;

    border-radius: 11px;
    background: #f1f6fd;
    color: #587ead;
}

#lgPickupAssignment .pickup-stat-icon svg {
    width: 18px;
    height: 18px;
}

#lgPickupAssignment .pickup-stat-green .pickup-stat-icon {
    background: #eef8f2;
    color: #4c8663;
}

#lgPickupAssignment .pickup-stat-amber .pickup-stat-icon {
    background: #fff7e9;
    color: #bc8420;
}

#lgPickupAssignment .pickup-stat-neutral .pickup-stat-icon {
    background: #f2f1ef;
    color: #80776d;
}

/* =========================================================
   WORKFLOW STEPPER
========================================================= */

#lgPickupAssignment .pickup-flow {
    gap: 4px;
    margin: 14px 0;
    padding: 4px;

    border-radius: 10px;
    box-shadow: none;
}

#lgPickupAssignment .pickup-flow :is(a,span) {
    min-height: 32px;
    padding: 5px 10px;

    border-radius: 7px;
    font-size: 9px;
}

#lgPickupAssignment .pickup-flow b {
    width: 21px;
    height: 21px;
    flex-basis: 21px;
    font-size: 9px;
}

/* =========================================================
   WORKSPACE
========================================================= */

#lgPickupAssignment .pickup-workspace {
    min-width: 0;
    overflow: hidden;

    border: 1px solid #e8e2d9;
    border-radius: 17px;

    background: #fff;
    box-shadow: var(--pickup-shadow);
}

#lgPickupAssignment .pickup-toolbar {
    gap: 14px;
    padding: 15px 17px;
}

#lgPickupAssignment .pickup-toolbar h2 {
    color: #29231d;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.035em;
}

#lgPickupAssignment .pickup-toolbar p {
    margin-top: 4px;
    color: #93897e;
    font-size: 9px;
    line-height: 1.55;
}

#lgPickupAssignment .pickup-search {
    width: min(315px,100%);
}

#lgPickupAssignment .pickup-search input {
    height: 39px;
    padding: 0 12px;

    border: 1px solid #dfe2e6;
    border-radius: 10px;

    background: #fff;
    font-size: 10px;
}

#lgPickupAssignment .pickup-search input:focus {
    border-color: #d9950b;
    box-shadow: 0 0 0 3px rgba(217,149,11,.09);
}

/* =========================================================
   FILTERS
========================================================= */

#lgPickupAssignment .pickup-filter-row {
    gap: 10px;
    padding: 0 17px 13px;
}

#lgPickupAssignment .pickup-tabs {
    gap: 4px;
    padding: 4px;

    border: 1px solid #e6e1d9;
    border-radius: 10px;
    background: #f7f8fa;
}

#lgPickupAssignment .pickup-tabs button {
    min-height: 30px;
    padding: 5px 10px;

    border-radius: 7px;
    font-size: 9px;
}

#lgPickupAssignment .pickup-tabs button[aria-pressed="true"] {
    border-color: #e9dfce;
    background: white;
    color: #a36c0a;
    box-shadow: 0 2px 5px rgba(35,29,22,.035);
}

#lgPickupAssignment .pickup-tabs span,
#lgPickupAssignment .pickup-count {
    min-width: 21px;
    height: 19px;
    font-size: 8px;
}

#lgPickupAssignment .pickup-text-link {
    color: #a66d0b;
    font-size: 9px;
    font-weight: 650;
}

/* =========================================================
   BATCH GRID
========================================================= */

#lgPickupAssignment .pickup-batches {
    display: grid;
    gap: 12px;
    padding: 14px;

    background: #fff;
}

#lgPickupAssignment .pickup-batch {
    min-width: 0;
    overflow: hidden;

    border: 1px solid #e8e2d9;
    border-radius: 13px;

    background: white;
    box-shadow: 0 3px 12px rgba(35,27,18,.025);

    transition:
        transform .18s cubic-bezier(.22,1,.36,1),
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgPickupAssignment .pickup-batch[data-focused="true"] {
    border-color: #d9950b;
    box-shadow: 0 0 0 3px rgba(217,149,11,.14);
}

/* =========================================================
   BATCH HEADER
========================================================= */

#lgPickupAssignment .pickup-batch-header {
    gap: 12px;
    min-height: 0;
    padding: 14px 15px;

    background: white;
    border-bottom: 1px solid #eee8df;
}

#lgPickupAssignment .pickup-seller-heading {
    min-width: 0;
    gap: 11px;
}

#lgPickupAssignment .pickup-avatar {
    display: grid;
    place-items: center;

    width: 41px;
    height: 41px;
    flex: 0 0 41px;

    border: 1px solid #eee3ce;
    border-radius: 11px;

    background: #fff5e1;
    color: #a97312;

    font-size: 12px;
    font-weight: 700;
}

#lgPickupAssignment .pickup-seller-heading .pickup-eyebrow {
    margin: 0 0 4px;
    font-size: 8px;
    color: #a68c61;
}

#lgPickupAssignment .pickup-seller-heading h3 {
    font-size: 12px;
    font-weight: 700;
    line-height: 1.5;

    white-space: normal;
    overflow-wrap: anywhere;
}

#lgPickupAssignment .pickup-batch-tags {
    gap: 6px;
    font-size: 9px;
}

#lgPickupAssignment .pickup-batch-tags > * {
    min-height: 25px;
    padding: 5px 9px;

    border-radius: 999px;
    background: #fbfbfc;
    border-color: #e8e2da;

    font-size: 8.5px;
    font-weight: 600;
}

/* STATUS BADGES */

#lgPickupAssignment .pa-batch-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;

    border: 1px solid #d5e7d9 !important;
    background: #f0f8f2 !important;
    color: #428059 !important;

    font-weight: 700 !important;
}

#lgPickupAssignment .pa-batch-status.is-blocked {
    border-color: #ead9b2 !important;
    background: #fff8e8 !important;
    color: #9a6b16 !important;
}

/* =========================================================
   BATCH CONTENT
========================================================= */

#lgPickupAssignment .pickup-batch-layout {
    display: grid;
    grid-template-columns: minmax(0,1fr) 294px;
    min-width: 0;
}

#lgPickupAssignment .pickup-batch-main {
    min-width: 0;
    padding: 16px;
}

/* ROUTE SUMMARY */

#lgPickupAssignment .pickup-route-summary {
    grid-template-columns: minmax(0,1fr) auto minmax(0,1fr);
    gap: 13px;

    align-items: start;

    padding-bottom: 14px;
    border-color: #eee8df;
}

#lgPickupAssignment .pickup-label {
    display: block;
    margin-bottom: 5px;

    color: #958879;
    font-size: 8px;
    font-weight: 700;
    letter-spacing: .07em;
}

#lgPickupAssignment .pickup-route-summary strong {
    color: #443c33;
    font-size: 10px;
    font-weight: 650;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgPickupAssignment .pickup-route-summary p {
    margin-top: 4px;
    color: #91877b;
    font-size: 9px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

#lgPickupAssignment .pickup-route-arrow {
    width: 28px;
    height: 28px;
    margin-top: 18px;

    background: #fff7e9;
    border-color: #eddec4;

    color: #ae7819;
    font-size: 15px;
}

/* PARCEL SECTION */

#lgPickupAssignment .pickup-parcel-heading {
    margin: 15px 0 10px;
}

#lgPickupAssignment .pickup-parcel-heading h4 {
    color: #403930;
    font-size: 10px;
    font-weight: 700;
}

#lgPickupAssignment .pa-help {
    margin: 4px 0 0;
    color: #968b7f;
    font-size: 9px;
    line-height: 1.5;
}

#lgPickupAssignment .pickup-batch-parcels {
    gap: 8px;
}

#lgPickupAssignment .pickup-parcel {
    gap: 8px 12px;
    padding: 11px 12px;

    border: 1px solid #e9e4dd;
    border-radius: 10px;
    background: #fbfbfc;
}

#lgPickupAssignment .pickup-parcel:hover {
    border-color: #dcd0bf;
    background: #fffdf9;
}

#lgPickupAssignment .pickup-parcel strong {
    color: #4b433a;
    font-size: 10px;
    font-weight: 650;
}

#lgPickupAssignment .pickup-parcel small {
    margin-top: 4px;
    color: #8f8579;
    font-size: 9px;
    line-height: 1.6;
}

#lgPickupAssignment .pickup-parcel-value {
    color: #40372d;
    font-size: 10px;
    font-weight: 700;
}

/* EXPANDED PARCEL DETAILS */

#lgPickupAssignment .pickup-details {
    margin-top: 3px;
}

#lgPickupAssignment .pickup-details summary {
    color: #a47118;
    font-size: 9px;
    font-weight: 650;
}

#lgPickupAssignment .pickup-detail-body {
    min-width: 0;
    padding: 12px;

    border-color: #e8e2d9;
    background: #fff;
}

#lgPickupAssignment .pickup-detail-grid {
    grid-template-columns: repeat(2,minmax(0,1fr));
}

#lgPickupAssignment .pickup-detail-grid strong,
#lgPickupAssignment .pickup-detail-grid p,
#lgPickupAssignment .pickup-products li {
    font-size: 9px;
    line-height: 1.6;
}

#lgPickupAssignment .pickup-products li strong {
    font-size: 9px;
}

/* DESTINATION HUBS */

#lgPickupAssignment .pa-destinations {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 7px;
    margin-top: 14px;
}

#lgPickupAssignment .pa-destinations .pickup-label {
    margin: 0 5px 0 0;
}

#lgPickupAssignment .pickup-badge {
    min-height: 24px;
    padding: 5px 9px;
    font-size: 8.5px;
}

/* =========================================================
   RIDER DISPATCH PANEL
========================================================= */

#lgPickupAssignment .pickup-dispatch-panel,
#lgPickupAssignment
.pickup-batch[data-batch-blocked="true"]
.pickup-dispatch-panel {
    min-width: 0;
    padding: 16px;

    border-left: 1px solid #eee8df;

    background: #fcfcfd;
}

#lgPickupAssignment .pickup-dispatch-panel h4 {
    color: #37312a;
    font-size: 12px;
    font-weight: 700;
}

#lgPickupAssignment .pickup-dispatch-note {
    margin: 7px 0 15px;
    color: #908679;
    font-size: 9px;
    line-height: 1.6;
}

#lgPickupAssignment .pickup-dispatch-panel select {
    width: 100%;
    min-height: 40px;
    padding: 8px 11px;

    border: 1px solid #dfe2e6;
    border-radius: 10px;

    background: white;
    color: #50483e;

    font-size: 10px;
}

#lgPickupAssignment .pickup-dispatch-panel select:focus {
    border-color: #d9950b;
    box-shadow: 0 0 0 3px rgba(217,149,11,.09);
}

#lgPickupAssignment .pickup-dispatch-panel select:disabled,
#lgPickupAssignment .pickup-dispatch-panel button:disabled {
    cursor: not-allowed;
    opacity: .55;
}

/* RIDER PREVIEW */

#lgPickupAssignment .pickup-rider-preview {
    margin-top: 12px;
    padding: 12px;

    border: 1px solid #e8e2d9;
    border-radius: 10px;

    background: white;
}

#lgPickupAssignment .pickup-rider-preview strong {
    font-size: 10px;
    font-weight: 700;
}

#lgPickupAssignment .pickup-rider-preview p {
    margin-top: 4px;
    color: #897f74;
    font-size: 9px;
    line-height: 1.5;
}

/* DISPATCH TOTAL */

#lgPickupAssignment .pickup-dispatch-total {
    margin-top: 15px;
    padding: 13px 0;

    border-top: 1px solid #eee8df;

    color: #807669;
    font-size: 9px;
}

#lgPickupAssignment .pickup-dispatch-total strong {
    color: #30271d;
    font-size: 19px;
    font-weight: 750;
}

#lgPickupAssignment .pickup-dispatch-panel .pickup-button {
    min-height: 40px;
    width: 100%;
    border-radius: 9px;
    font-size: 10px;
    font-weight: 700;
}

#lgPickupAssignment .pickup-dispatch-panel .pickup-button-primary {
    background: #d9950b;
    border-color: #d9950b;
    color: white;
}

#lgPickupAssignment .pickup-dispatch-panel .pickup-button-primary:hover {
    background: #c88708;
    border-color: #c88708;
}

#lgPickupAssignment .pickup-dispatch-footnote {
    margin-top: 9px;
    color: #958a7e;
    font-size: 9px;
    line-height: 1.6;
}

/* =========================================================
   BLOCKED BATCH WARNING
========================================================= */

#lgPickupAssignment .pickup-batch-warning {
    margin: 12px 0 15px;
    padding: 12px;

    border: 1px solid #ecd49e;
    border-radius: 10px;

    background: #fff9ed;
    color: #936719;

    font-size: 9px;
    line-height: 1.65;
}

#lgPickupAssignment .pickup-batch-warning strong {
    display: block;
    margin-bottom: 6px;
    font-size: 10px;
    font-weight: 700;
}

#lgPickupAssignment .pickup-batch-warning p {
    margin: 0 0 7px;
}

#lgPickupAssignment .pickup-batch-warning a {
    color: #92600c;
    font-size: 9px;
    font-weight: 700;
    text-decoration: underline;
    text-underline-offset: 3px;
}

/* ALERTS */

#lgPickupAssignment .pickup-alert strong {
    font-size: 10px;
}

#lgPickupAssignment .pickup-alert p,
#lgPickupAssignment .pickup-alert ul {
    font-size: 9px;
}

/* =========================================================
   EMPTY STATES AND FOOTER
========================================================= */

#lgPickupAssignment .pickup-empty {
    padding: 40px 20px;
}

#lgPickupAssignment .pickup-empty-icon {
    width: 43px;
    height: 43px;

    border-radius: 12px;
    background: #fff7e8;
}

#lgPickupAssignment .pickup-empty h3 {
    color: #51483e;
    font-size: 11px;
}

#lgPickupAssignment .pickup-empty p {
    font-size: 9px;
}

#lgPickupAssignment .pickup-list-footer {
    padding: 12px 16px;
    background: white;
    font-size: 9px;
}

/* =========================================================
   HOVER
========================================================= */

@media (hover:hover) and (pointer:fine) {
    #lgPickupAssignment .pickup-stat:hover,
    #lgPickupAssignment .pickup-batch:hover {
        transform: translateY(-2px);
        border-color: #d8c9b2;
        box-shadow: 0 13px 28px rgba(43,33,21,.055);
    }
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width:1200px) {
    #lgPickupAssignment .pickup-batch-layout {
        grid-template-columns: minmax(0,1fr) 270px;
    }
}

@media (max-width:1050px) {
    #lgPickupAssignment .pickup-batch-layout {
        grid-template-columns: 1fr;
    }

    #lgPickupAssignment .pickup-dispatch-panel,
    #lgPickupAssignment
    .pickup-batch[data-batch-blocked="true"]
    .pickup-dispatch-panel {
        border-top: 1px solid #eee8df;
        border-left: 0;
    }
}

@media (max-width:900px) {
    #lgPickupAssignment .pickup-stats {
        grid-template-columns: repeat(2,minmax(0,1fr));
    }
}

@media (max-width:767px) {
    #lgPickupAssignment .pickup-page-header,
    #lgPickupAssignment .pickup-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    #lgPickupAssignment .pickup-page-header .pickup-button {
        align-self: flex-start;
    }

    #lgPickupAssignment .pickup-search {
        width: 100%;
    }

    #lgPickupAssignment .pickup-filter-row {
        flex-direction: column;
        align-items: stretch;
        padding: 0 14px 13px;
    }

    #lgPickupAssignment .pickup-batches {
        padding: 10px;
    }

    #lgPickupAssignment .pickup-batch-header {
        padding: 13px;
    }

    #lgPickupAssignment .pickup-batch-tags {
        margin-left: 0;
    }

    #lgPickupAssignment .pickup-batch-main,
    #lgPickupAssignment .pickup-dispatch-panel {
        padding: 13px;
    }
}

@media (max-width:520px) {
    #lgPickupAssignment .pickup-page-header .pickup-button {
        width: 100%;
    }

    #lgPickupAssignment .pickup-stat {
        padding: 12px;
    }

    #lgPickupAssignment .pickup-stat strong {
        font-size: 22px;
    }

    #lgPickupAssignment .pickup-stat-icon {
        width: 34px;
        height: 34px;
        flex-basis: 34px;
    }

    #lgPickupAssignment .pickup-route-summary {
        grid-template-columns: 1fr;
    }

    #lgPickupAssignment .pickup-route-arrow {
        margin: 0;
        transform: rotate(90deg);
    }

    #lgPickupAssignment .pickup-detail-grid {
        grid-template-columns: 1fr;
    }
}

@media (prefers-reduced-motion:reduce) {
    #lgPickupAssignment *,
    #lgPickupAssignment *::before,
    #lgPickupAssignment *::after {
        transition-duration: .01ms !important;
        animation-duration: .01ms !important;
    }

    #lgPickupAssignment .pickup-stat:hover,
    #lgPickupAssignment .pickup-batch:hover {
        transform: none !important;
    }
}
</style>
<style>
/* Pickup Assignment — clean directory parity. Scoped visual-only styles. */
#lgPickupAssignment.pickup-page { --pickup-line:#e5e9ee; --pickup-ink:#26313e; }
#lgPickupAssignment .pickup-stat { border-color:#e7ebf0; border-radius:17px; }
#lgPickupAssignment .pickup-stat p { color:#697482; }
#lgPickupAssignment .pickup-stat strong { color:#222b35; }
#lgPickupAssignment .pickup-stat span:not(.pickup-stat-icon) { color:#9098a3; }
#lgPickupAssignment .pickup-stat-icon,
#lgPickupAssignment .pickup-stat-green .pickup-stat-icon,
#lgPickupAssignment .pickup-stat-amber .pickup-stat-icon,
#lgPickupAssignment .pickup-stat-blue .pickup-stat-icon,
#lgPickupAssignment .pickup-stat-neutral .pickup-stat-icon {
    display:inline-flex !important; align-items:center; justify-content:center;
    flex:0 0 30px; width:30px; height:30px; padding:0; border:0 !important;
    border-radius:0 !important; background:transparent !important; background-image:none !important;
    box-shadow:none !important; color:#aab3bd !important;
}
#lgPickupAssignment .pickup-stat-icon svg { display:block; width:30px; height:30px; stroke-width:1.6; }
/* Colored standalone KPI icons — no icon background or wrapper enlargement. */
#lgPickupAssignment .pickup-stat-green .pickup-stat-icon { color:#398660 !important; }
#lgPickupAssignment .pickup-stat-amber .pickup-stat-icon { color:#c48a20 !important; }
#lgPickupAssignment .pickup-stat-blue .pickup-stat-icon { color:#4a83b8 !important; }
#lgPickupAssignment .pickup-stat-neutral .pickup-stat-icon { color:#8875b5 !important; }

#lgPickupAssignment .pickup-stat:hover { border-color:#e0e5eb; transform:none; box-shadow:0 8px 24px rgba(22,34,48,.055); }
#lgPickupAssignment .pa-directory-filters {
    display:grid; grid-template-columns:minmax(0,1fr) 200px 148px 93px;
    align-items:center; gap:12px; margin:15px 0; min-width:0;
}
#lgPickupAssignment .pa-directory-search,
#lgPickupAssignment .pa-directory-select { position:relative; display:block; min-width:0; }
#lgPickupAssignment .pa-directory-search > svg {
    position:absolute; width:17px; height:17px; left:15px; top:50%; transform:translateY(-50%);
    color:#96a2b0; pointer-events:none;
}
#lgPickupAssignment .pa-directory-search input,
#lgPickupAssignment .pa-directory-select select {
    display:block; width:100%; height:46px; padding:0 13px; min-width:0;
    border:1px solid #dfe4ea; border-radius:13px; background:#fff; color:#374151;
    font-family:inherit; font-size:11px; font-weight:500; outline:0;
    box-shadow:0 6px 16px rgba(23,35,48,.025); transition:border-color .15s ease,box-shadow .15s ease;
}
#lgPickupAssignment .pa-directory-search input { padding-left:42px; }
#lgPickupAssignment .pa-directory-search input:focus,
#lgPickupAssignment .pa-directory-select select:focus { border-color:#bac5d2; box-shadow:0 0 0 3px rgba(148,163,184,.14); background:#fff; }
#lgPickupAssignment .pa-directory-search input::placeholder { color:#9ba7b6; }
#lgPickupAssignment .pa-directory-select select { appearance:none; padding-right:34px; }
#lgPickupAssignment .pa-directory-select > svg {
    position:absolute; width:14px; height:14px; right:13px; top:50%; transform:translateY(-50%);
    pointer-events:none; color:#94a0ae;
}
#lgPickupAssignment .pa-directory-apply,
#lgPickupAssignment .pa-directory-reset {
    display:inline-flex; align-items:center; justify-content:center; gap:8px; width:100%;
    height:46px; padding:0 12px; border-radius:13px; font-family:inherit; font-weight:700;
    font-size:11px; white-space:nowrap; transition:background-color .15s ease;
}
#lgPickupAssignment .pa-directory-apply { border:1px solid #d9950b; background:#d9950b; color:#fff; box-shadow:0 7px 15px rgba(185,130,13,.12); }
#lgPickupAssignment .pa-directory-apply:hover { background:#bf8009; }
#lgPickupAssignment .pa-directory-apply svg { width:16px; height:16px; }
#lgPickupAssignment .pa-directory-reset { background:#fff; border:1px solid #dfe5eb; color:#536274; }
#lgPickupAssignment .pa-directory-reset:hover { background:#f4f6f8; }
#lgPickupAssignment .pa-directory { border-color:#e5e9ee; border-radius:20px; box-shadow:0 10px 30px rgba(25,35,48,.055); }
#lgPickupAssignment .pa-directory-head {
    display:flex; justify-content:space-between; align-items:center; gap:12px;
    padding:17px 20px; border-bottom:1px solid #e9edf1; background:#fff;
}
#lgPickupAssignment .pa-directory-head h2 { font-size:16px; font-weight:750; letter-spacing:-.03em; color:#242d37; }
#lgPickupAssignment .pa-directory-count {
    display:inline-flex; white-space:nowrap; padding:7px 11px; border:1px solid #e4e8ed;
    border-radius:9px; background:#f8fafc; font-size:10px; font-weight:700; color:#657386;
}
#lgPickupAssignment .pickup-batches { padding:14px; gap:12px; background:#fff; }
#lgPickupAssignment .pickup-batch { border-color:#e6eaef; border-radius:15px; }
#lgPickupAssignment .pickup-batch:hover { transform:none; border-color:#dce2e9; box-shadow:0 8px 18px rgba(23,35,48,.045); }
#lgPickupAssignment .pickup-batch-header { border-color:#ebeff2; }
#lgPickupAssignment .pickup-avatar { background:#f1f3f5; border-color:#e5e9ee; color:#798491; }
#lgPickupAssignment .pickup-batch-tags > * { border-color:#e7ebf0; background:#f8fafc; color:#667384; }
#lgPickupAssignment .pickup-dispatch-panel,
#lgPickupAssignment .pickup-batch[data-batch-blocked="true"] .pickup-dispatch-panel { background:#fafbfc; border-color:#e8edf2; }
#lgPickupAssignment .pickup-parcel { background:#fff; border-color:#e8ecf0; }
#lgPickupAssignment .pickup-parcel:hover { background:#f5f7f9; border-color:#dfe4ea; }
#lgPickupAssignment .pickup-route-arrow { background:#f2f4f6; border-color:#e5e9ed; color:#748292; }
#lgPickupAssignment .pickup-empty { padding:56px 20px 62px; text-align:center; }
#lgPickupAssignment .pickup-empty-icon {
    display:grid; place-items:center; width:92px; height:92px; padding:0; margin:0 auto;
    background:transparent !important; border:0 !important; border-radius:0 !important;
    color:#b4bbc3 !important; box-shadow:none !important;
}
#lgPickupAssignment .pickup-empty-icon svg { display:block; width:58px; height:58px; }
#lgPickupAssignment .pickup-empty h3 { margin:16px 0 0; font-size:22px; font-weight:750; line-height:1.25; letter-spacing:-.025em; color:#929eaa; }
#lgPickupAssignment .pickup-empty p { max-width:540px; margin:8px auto 0; font-size:12px; font-weight:500; line-height:1.55; color:#a5b0ba; }
#lgPickupAssignment .pickup-empty > .pickup-text-link { margin-top:15px; font-size:11px; }
/* Empty-state CTA: genuine SARI gold action, not a text-only link. */
#lgPickupAssignment .pickup-empty > a.pa-empty-action {
    display:inline-flex !important;
    align-items:center;
    justify-content:center;
    gap:8px;
    min-height:42px;
    margin-top:19px;
    padding:11px 19px;
    border:1px solid #d9950b !important;
    border-radius:11px;
    background:#d9950b !important;
    color:#fff !important;
    font-size:11px !important;
    font-weight:700;
    line-height:1.4;
    text-decoration:none !important;
    box-shadow:0 7px 16px rgba(175,122,18,.15);
    transition:background-color .15s ease,border-color .15s ease,box-shadow .15s ease;
}
#lgPickupAssignment .pickup-empty > a.pa-empty-action:hover {
    border-color:#bd8009 !important;
    background:#bd8009 !important;
    color:#fff !important;
    box-shadow:0 9px 18px rgba(175,122,18,.2);
}
#lgPickupAssignment .pickup-empty > a.pa-empty-action:focus-visible {
    outline:3px solid rgba(217,149,11,.25);
    outline-offset:3px;
}

#lgPickupAssignment .pa-directory-footer { justify-content:space-between; align-items:center; padding:12px 19px; border-color:#e8edf2; background:#fff; font-size:11px; color:#728091; }
#lgPickupAssignment .pa-page-size { display:flex; gap:10px; align-items:center; font-size:11px; color:#778596; }
#lgPickupAssignment .pa-page-size select { width:60px; height:32px; padding:0 7px; border:1px solid #e0e6ed; border-radius:9px; background:#fff; color:#536276; font-size:11px; }
#lgPickupAssignment .pa-pagination {
    display:flex; align-items:center; justify-content:center; gap:7px; margin:13px 0 0;
}
#lgPickupAssignment .pa-page-numbers { display:flex; align-items:center; justify-content:center; gap:7px; }
#lgPickupAssignment .pa-page-number,
#lgPickupAssignment .pa-page-arrow {
    display:inline-flex; align-items:center; justify-content:center; flex:0 0 34px;
    width:34px; height:34px; border:1px solid transparent; border-radius:10px;
    background:#f0f2f5; color:#7a899a; font-size:12px; font-weight:700; box-shadow:none;
}
#lgPickupAssignment .pa-page-arrow svg { width:16px; height:16px; }
#lgPickupAssignment .pa-page-number.is-current { background:#d09622; border-color:#d09622; color:#fff; box-shadow:0 5px 12px rgba(173,119,14,.15); }
#lgPickupAssignment .pa-page-number:hover:not(.is-current),
#lgPickupAssignment .pa-page-arrow:hover:not(:disabled) { background:#e4e8ec; }
#lgPickupAssignment .pa-page-arrow:disabled { opacity:.28; cursor:not-allowed; }
#lgPickupAssignment [hidden] { display:none !important; }
@media(max-width:950px) {
    #lgPickupAssignment .pa-directory-filters { grid-template-columns:minmax(0,1fr) 190px; }
}
@media(max-width:600px) {
    #lgPickupAssignment .pa-directory-filters { grid-template-columns:minmax(0,1fr) 1fr; gap:9px; }
    #lgPickupAssignment .pa-directory-search { grid-column:1/-1; }
    #lgPickupAssignment .pa-directory-apply,
    #lgPickupAssignment .pa-directory-reset { height:41px; }
    #lgPickupAssignment .pa-directory-select select,
    #lgPickupAssignment .pa-directory-search input { height:44px; }
    #lgPickupAssignment .pa-directory-head { padding:14px; }
    #lgPickupAssignment .pa-directory-head h2 { font-size:14px; }
    #lgPickupAssignment .pickup-stat-icon,
    #lgPickupAssignment .pickup-stat-icon svg { width:26px; height:26px; }
    #lgPickupAssignment .pickup-stat-icon { flex-basis:26px; }
    #lgPickupAssignment .pickup-stat-green .pickup-stat-icon,
    #lgPickupAssignment .pickup-stat-amber .pickup-stat-icon,
    #lgPickupAssignment .pickup-stat-blue .pickup-stat-icon,
    #lgPickupAssignment .pickup-stat-neutral .pickup-stat-icon {
        width:26px; height:26px; flex:0 0 26px;
    }
    #lgPickupAssignment .pickup-empty { padding:45px 16px 52px; }
    #lgPickupAssignment .pickup-empty-icon { width:64px; height:64px; }
    #lgPickupAssignment .pickup-empty-icon svg { width:40px; height:40px; }
    #lgPickupAssignment .pickup-empty h3 { font-size:20px; }
    #lgPickupAssignment .pickup-empty p { font-size:11px; }
    #lgPickupAssignment .pa-directory-footer { gap:9px; flex-wrap:wrap; font-size:10px; }
}

/* Seller Pickup compact batch + Rider selection dialogs.
   UI-scoped: parent layout, controller, and shared pickup CSS are untouched. */
#lgPickupAssignment .pickup-batches { gap:12px; padding:15px; background:#fff; }
#lgPickupAssignment .pickup-batch.pa-batch-clean,
#lgPickupAssignment .pickup-batch:has(.pa-batch-compact) {
  overflow:hidden; border:1px solid #e4e9ef; border-radius:17px; background:#fff;
  box-shadow:0 4px 16px rgba(30,42,58,.035); transform:none;
}
#lgPickupAssignment .pickup-batch:has(.pa-batch-compact):hover {
  transform:none; border-color:#dce3eb; box-shadow:0 7px 19px rgba(30,42,58,.05);
}
#lgPickupAssignment .pa-batch-compact { display:grid; gap:14px; padding:16px 18px; color:#29384b; }
#lgPickupAssignment .pa-batch-top, #lgPickupAssignment .pa-batch-bottom,
#lgPickupAssignment .pa-batch-actions, #lgPickupAssignment .pa-batch-facts,
#lgPickupAssignment .pa-seller-identity { display:flex; align-items:center; gap:12px; }
#lgPickupAssignment .pa-batch-top, #lgPickupAssignment .pa-batch-bottom { justify-content:space-between; flex-wrap:wrap; }
#lgPickupAssignment .pa-seller-identity, #lgPickupAssignment .pa-seller-copy { min-width:0; }
#lgPickupAssignment .pa-seller-avatar, .pa-rider-avatar {
  flex:0 0 44px; width:44px; height:44px; display:flex; align-items:center; justify-content:center;
  background:#edf1f5; border:1px solid #e1e7ed; border-radius:13px; color:#7e8b9b;
  font-size:14px; font-weight:750;
}
#lgPickupAssignment .pa-overline,.pa-modal .pa-overline {
  display:block; color:#b57c19; font-size:9px; line-height:1.5; font-weight:800; letter-spacing:.12em;
}
#lgPickupAssignment .pa-seller-copy h3 {
  margin:2px 0 3px; font-size:15px; font-weight:750; line-height:1.35; color:#26374b;
  text-overflow:ellipsis; overflow:hidden; white-space:nowrap;
}
#lgPickupAssignment .pa-batch-caption { font-size:10px; color:#8997a8; }
#lgPickupAssignment .pa-ready-status {
  display:inline-flex; align-items:center; gap:7px; border:1px solid #d4e7dc; padding:7px 10px;
  border-radius:999px; background:#f3faf5; color:#437d5a; font-size:10px; font-weight:720; white-space:nowrap;
}
#lgPickupAssignment .pa-ready-status.is-blocked { color:#a27026; background:#fff9eb; border-color:#f0dfbc; }
#lgPickupAssignment .pa-status-dot { width:7px; height:7px; border-radius:50%; background:currentColor; }
#lgPickupAssignment .pa-route-strip {
  display:grid; grid-template-columns:minmax(0,1fr) 24px minmax(0,1fr); gap:13px; align-items:center;
  background:#f8fafc; border:1px solid #ecf0f4; border-radius:13px; padding:13px 15px;
}
#lgPickupAssignment .pa-route-place { display:flex; flex-direction:column; gap:3px; min-width:0; }
#lgPickupAssignment .pa-route-key { color:#92a0af; font-size:9px; font-weight:750; letter-spacing:.065em; }
#lgPickupAssignment .pa-route-place strong { font-size:12px; font-weight:720; color:#304156; line-height:1.4; overflow-wrap:anywhere; }
#lgPickupAssignment .pa-route-address { font-size:10px; line-height:1.5; color:#94a0ae; overflow-wrap:anywhere; }
#lgPickupAssignment .pa-route-chevron { font-size:17px; color:#a4aebb; text-align:center; }
#lgPickupAssignment .pa-batch-bottom { gap:12px 18px; }
#lgPickupAssignment .pa-batch-facts { flex-wrap:wrap; gap:7px 15px; }
#lgPickupAssignment .pa-batch-facts span { font-size:10px; color:#8a96a6; white-space:nowrap; }
#lgPickupAssignment .pa-batch-facts strong { color:#344459; font-size:12px; font-weight:800; }
#lgPickupAssignment .pa-batch-actions { margin-left:auto; gap:9px; }
#lgPickupAssignment .pa-btn, .pa-modal .pa-btn {
  display:inline-flex; align-items:center; justify-content:center; gap:7px; min-height:39px;
  padding:9px 14px; border-radius:10px; font-size:11px; line-height:1.25; font-weight:760;
  text-decoration:none; cursor:pointer; white-space:nowrap; border:1px solid transparent;
  transition:background-color .15s ease,border-color .15s ease;
}
#lgPickupAssignment .pa-btn-outline, .pa-modal .pa-btn-outline { background:#fff; border-color:#dfe6ed; color:#536478; }
#lgPickupAssignment .pa-btn-outline:hover, .pa-modal .pa-btn-outline:hover { background:#f5f7fa; }
#lgPickupAssignment .pa-btn-gold, .pa-modal .pa-btn-gold { background:#d9950b; border-color:#d9950b; color:#fff; }
#lgPickupAssignment .pa-btn-gold:hover:not(:disabled),.pa-modal .pa-btn-gold:hover:not(:disabled) { background:#bf8009; border-color:#bf8009; }
#lgPickupAssignment .pa-btn:disabled,.pa-modal .pa-btn:disabled { opacity:.5; cursor:not-allowed; }
#lgPickupAssignment .pa-inline-notice {
  margin:0; padding:8px 10px; background:#fafbfc; border:1px solid #e8edf2;
  border-radius:9px; color:#7e8c9a; font-size:10px; line-height:1.5;
}
#lgPickupAssignment .pa-inline-notice a { color:#a57114; font-weight:700; }

/* Portaled dialogs: appended to body, so no clipping by shared cards or transforms. */
body.pa-modal-open { overflow:hidden; }
.pa-modal, .pa-modal *, .pa-modal *::before, .pa-modal *::after { box-sizing:border-box; }
.pa-modal[hidden], .pa-modal [hidden] { display:none!important; }
.pa-modal {
  position:fixed!important; inset:0!important; z-index:2147481000!important; display:flex; align-items:center;
  justify-content:center; padding:18px; font-family:Poppins,ui-sans-serif,system-ui,sans-serif;
  font-size:12px; color:#2d3b4e;
}
.pa-modal-scrim { position:absolute; inset:0; background:rgba(16,27,41,.55); backdrop-filter:blur(3px); }
.pa-modal-panel {
  position:relative; z-index:1; width:min(100%,850px); max-height:min(90vh,880px); display:flex; flex-direction:column;
  border:1px solid #e6eaf0; border-radius:20px; background:#fff;
  box-shadow:0 28px 90px rgba(16,27,43,.23); overflow:hidden;
  animation:paModalIn .18s ease-out both;
}
.pa-assign-panel { width:min(100%,780px); }
.pa-modal-header {
  display:flex; justify-content:space-between; align-items:flex-start; gap:14px;
  padding:21px 24px 18px; border-bottom:1px solid #edf0f3;
}
.pa-modal-header h2 { margin:3px 0; font-size:23px; font-weight:780; letter-spacing:-.04em; color:#233449; line-height:1.25; }
.pa-modal-header p { margin:4px 0 0; font-size:11px; color:#8a96a6; }
.pa-modal-x {
  flex:0 0 36px; width:36px; height:36px; border:1px solid #e0e6ed; border-radius:10px;
  background:#fff; color:#8190a1; font-size:25px; font-weight:400; line-height:1; cursor:pointer;
}
.pa-modal-x:hover { background:#f5f7f9; }
.pa-modal-body { flex:1; min-height:0; overflow-y:auto; padding:21px 24px; overscroll-behavior:contain; }
.pa-modal-footer {
  display:flex; justify-content:flex-end; gap:10px; padding:15px 24px;
  border-top:1px solid #edf0f3; background:#fff;
}
.pa-detail-route {
  display:grid; grid-template-columns:minmax(0,1fr) 24px minmax(0,1fr); align-items:center; gap:12px;
  padding:17px; background:#f8fafb; border:1px solid #e8edf2; border-radius:14px;
}
.pa-detail-route div { display:flex; flex-direction:column; gap:5px; min-width:0; }
.pa-detail-route small { font-size:9px; letter-spacing:.08em; color:#97a3b0; font-weight:800; }
.pa-detail-route strong { font-size:12px; font-weight:730; color:#2e4056; }
.pa-detail-route span { font-size:10.5px; color:#8f9bac; line-height:1.5; overflow-wrap:anywhere; }
.pa-detail-route > span { font-size:20px; text-align:center; }
.pa-modal-section-heading { display:flex; justify-content:space-between; gap:12px; align-items:center; margin:20px 0 11px; }
.pa-modal-section-heading h3 { margin:0; font-size:14px; font-weight:770; color:#314257; }
.pa-modal-section-heading > span,.pa-modal-section-heading p { margin:3px 0 0; font-size:11px; color:#9aa4b1; }
.pa-parcel-list { display:grid; gap:9px; }
.pa-parcel-item { border:1px solid #e5eaf0; border-radius:12px; overflow:hidden; background:#fff; }
.pa-parcel-item summary { list-style:none; cursor:pointer; display:flex; align-items:center; gap:12px; padding:13px 15px; }
.pa-parcel-item summary::-webkit-details-marker { display:none; }
.pa-parcel-item summary > span:first-child { display:flex; flex:1; min-width:0; flex-direction:column; gap:4px; }
.pa-parcel-item summary strong { font-size:12px; color:#32445b; }
.pa-parcel-item summary small { color:#9aa5b1; font-size:10.5px; }
.pa-parcel-money { white-space:nowrap; font-size:12px; font-weight:750; color:#2d3f53; }
.pa-parcel-toggle { color:#95a2b0; font-size:18px; }
.pa-parcel-item[open] .pa-parcel-toggle { transform:rotate(45deg); }
.pa-parcel-expanded { display:grid; grid-template-columns:1fr 1fr; gap:14px; border-top:1px solid #edf1f5; background:#fafbfc; padding:13px 15px; }
.pa-parcel-expanded > div { display:flex; flex-direction:column; gap:5px; min-width:0; }
.pa-parcel-expanded span { font-size:10px; color:#98a3af; }
.pa-parcel-expanded strong { font-size:11px; font-weight:660; color:#48596c; overflow-wrap:anywhere; }
.pa-parcel-expanded .pa-products p { margin:0; font-size:10.5px; color:#526277; }
.pa-dest-list { margin-top:18px; display:grid; gap:8px; }
.pa-dest-list > span { font-size:10px; font-weight:750; color:#8b97a6; text-transform:uppercase; letter-spacing:.06em; }
.pa-dest-list div { display:flex; flex-wrap:wrap; gap:7px; }
.pa-dest-list div span { padding:7px 10px; border:1px solid #e4eaf0; border-radius:9px; background:#f8fafb; font-size:10px; color:#64758a; }
.pa-pickup-location { display:grid; gap:4px; padding:12px 14px; border:1px solid #e9edf2; border-radius:12px; background:#fafbfd; }
.pa-pickup-location > span { font-size:9px; color:#9ba6b3; font-weight:770; letter-spacing:.075em; }
.pa-pickup-location strong { font-size:12px; font-weight:740; color:#35465c; }
.pa-pickup-location p { margin:0; font-size:10.5px; color:#8391a2; line-height:1.5; }
.pa-area-disclaimer { margin:0 0 11px; font-size:10.5px; color:#8e9aa9; line-height:1.6; }
.pa-rider-search-wrap { display:block; position:relative; margin-bottom:12px; }
.pa-rider-search-wrap svg { position:absolute; left:14px; top:50%; transform:translateY(-50%); width:17px; height:17px; pointer-events:none; }
.pa-rider-search-wrap input {
  width:100%; height:42px; padding:0 12px 0 40px; border:1px solid #dae2ea; border-radius:10px;
  background:#fff; outline:none; color:#304258; font:inherit; font-size:11px;
}
.pa-rider-search-wrap input:focus { border-color:#aabbce; box-shadow:0 0 0 3px rgba(148,163,184,.13); }
.pa-rider-list { display:grid; gap:9px; }
.pa-rider-option {
  display:flex; align-items:center; gap:12px; padding:12px 13px; min-width:0;
  border:1px solid #e2e8ef; border-radius:13px; background:#fff; cursor:pointer;
  transition:background-color .15s ease,border-color .15s ease;
}
.pa-rider-option:hover:not(.is-busy) { border-color:#becbd9; background:#fbfcfd; }
.pa-rider-option.is-selected { border-color:#d4a34c; background:#fffdf8; box-shadow:0 0 0 2px rgba(217,149,11,.08); }
.pa-rider-option.is-busy { opacity:.55; cursor:not-allowed; background:#fafbfc; }
.pa-rider-option input { position:absolute; width:1px; height:1px; opacity:0; }
.pa-rider-option:focus-within { outline:2px solid #8ba1b9; outline-offset:1px; }
.pa-rider-option .pa-rider-avatar { flex-basis:43px; width:43px; height:43px; }
.pa-rider-info { display:flex; flex:1; min-width:0; flex-direction:column; gap:3px; }
.pa-rider-name { font-size:12px; font-weight:780; color:#2b3e53; }
.pa-rider-area { color:#6d7f91; font-size:10.5px; line-height:1.4; overflow-wrap:anywhere; }
.pa-rider-vehicle { font-size:10px; color:#a0abb7; overflow-wrap:anywhere; }
.pa-rider-badges { display:flex; flex-direction:column; align-items:flex-end; flex:0 0 auto; gap:8px; }
.pa-rider-badge { font-size:9px; white-space:nowrap; font-weight:780; color:#4c9069; }
.pa-rider-badge.is-recommended { color:#b47b16; }
.pa-rider-badge.is-muted { color:#9da8b4; }
.pa-radio-indicator { display:inline-block; width:18px; height:18px; border:1.7px solid #c7d1dc; border-radius:50%; background:white; }
.pa-rider-option.is-selected .pa-radio-indicator { border:5px solid #d9950b; }
.pa-rider-empty,.pa-no-search { padding:20px; border:1px solid #e9edf2; border-radius:12px; background:#fafbfd; color:#7c8b9a; font-size:11px; text-align:center; }
.pa-rider-empty a { color:#b7821b; font-weight:700; }
.pa-assignment-summary { display:grid; gap:4px; align-self:center; margin-right:auto; min-width:130px; }
.pa-assignment-summary small { color:#99a5b2; font-size:9px; letter-spacing:.05em; font-weight:750; }
.pa-assignment-summary strong { color:#425369; font-size:11px; font-weight:750; }
.pa-modal :is(button, input):focus-visible { outline:2px solid #92a5b9; outline-offset:2px; }
@keyframes paModalIn { from { opacity:0; transform:translateY(10px) scale(.987); } to { opacity:1; transform:translateY(0) scale(1); } }
@media(max-width:760px) {
  #lgPickupAssignment .pa-route-strip { grid-template-columns:1fr; gap:8px; }
  #lgPickupAssignment .pa-route-chevron { transform:rotate(90deg); text-align:left; margin-left:6px; }
  #lgPickupAssignment .pa-batch-actions { width:100%; margin-left:0; }
  #lgPickupAssignment .pa-batch-actions .pa-btn { flex:1; }
  .pa-modal { padding:10px; }
  .pa-modal-panel { max-height:94vh; border-radius:16px; }
  .pa-modal-header { padding:16px; }
  .pa-modal-header h2 { font-size:19px; }
  .pa-modal-body { padding:15px 16px; }
  .pa-modal-footer { padding:12px 16px; flex-wrap:wrap; }
  .pa-detail-route { grid-template-columns:1fr; }
  .pa-detail-route > span { text-align:left; transform:rotate(90deg); width:20px; }
  .pa-parcel-expanded { grid-template-columns:1fr; }
  .pa-rider-option { flex-wrap:wrap; gap:9px; }
  .pa-rider-badges { flex-direction:row; align-items:center; margin-left:auto; }
  .pa-assign-footer .pa-assignment-summary { flex:1 0 100%; }
}
@media(prefers-reduced-motion:reduce) { .pa-modal-panel { animation:none; } }


/* ===============================================================
   SARI Pickup Assignment V2 — one elevated workspace, flat rows.
   The outer white directory is the ONLY floating content container.
   =============================================================== */
#lgPickupAssignment .pa-directory {
  border:1px solid #e4eaf0 !important;
  border-radius:20px !important;
  overflow:hidden;
  background:#fff !important;
  box-shadow:0 18px 44px rgba(31,45,61,.075) !important;
}
#lgPickupAssignment .pa-directory-head {
  padding:19px 24px !important;
  border-bottom:1px solid #ebeff3 !important;
}
#lgPickupAssignment .pa-directory-head h2 {
  font-size:17px !important;
  font-weight:750 !important;
  color:#26364a !important;
}
#lgPickupAssignment .pickup-batches {
  display:block !important;
  padding:0 !important;
  gap:0 !important;
  background:#fff !important;
  box-shadow:none !important;
}
#lgPickupAssignment .pickup-batches > .pickup-batch.pa-batch-clean,
#lgPickupAssignment .pickup-batches > .pickup-batch.pa-batch-clean:hover,
#lgPickupAssignment .pickup-batches > .pickup-batch:has(.pa-batch-compact),
#lgPickupAssignment .pickup-batches > .pickup-batch:has(.pa-batch-compact):hover {
  display:block;
  margin:0 !important;
  padding:0 !important;
  border:0 !important;
  border-bottom:1px solid #e9edf2 !important;
  border-radius:0 !important;
  background:transparent !important;
  box-shadow:none !important;
  transform:none !important;
}
#lgPickupAssignment .pickup-batches > .pickup-batch:last-child { border-bottom:0 !important; }
#lgPickupAssignment .pickup-batches > .pickup-batch.pa-batch-clean[data-focused="true"] {
  background:#f9fafb !important;
  outline:2px solid #b7c4d3;
  outline-offset:-2px;
}
#lgPickupAssignment .pa-batch-compact {
  display:grid;
  gap:17px;
  padding:21px 24px 19px !important;
  background:transparent !important;
  box-shadow:none !important;
}
#lgPickupAssignment .pa-seller-avatar {
  width:46px; height:46px; flex-basis:46px;
  border-radius:50%; background:#f0f3f6; border:1px solid #e5eaf0;
  color:#69798a; font-size:17px;
}
#lgPickupAssignment .pa-seller-copy h3 {
  font-size:16px; font-weight:760; color:#24354b;
  letter-spacing:-.024em;
}
#lgPickupAssignment .pa-overline { color:#ab7313; }
#lgPickupAssignment .pa-ready-status {
  background:#f3faf6; border-color:#d4e9dc; color:#387b54;
  padding:7px 11px; font-size:10px;
}
#lgPickupAssignment .pa-route-strip {
  display:grid; grid-template-columns:minmax(0,1fr) 30px minmax(0,1fr);
  gap:18px; align-items:center;
  background:#f7f9fb !important; border:1px solid #e9eef3 !important;
  border-radius:13px; padding:17px 20px;
  box-shadow:none !important;
}
#lgPickupAssignment .pa-route-place { gap:5px; }
#lgPickupAssignment .pa-route-key { font-size:10px; font-weight:800; color:#8694a5; }
#lgPickupAssignment .pa-route-place strong { color:#2c3d50; font-size:13px; font-weight:740; }
#lgPickupAssignment .pa-route-address { color:#8d9aaa; font-size:11px; line-height:1.55; }
#lgPickupAssignment .pa-route-chevron {
  font-size:20px; color:#8393a6; text-align:center;
}
#lgPickupAssignment .pa-batch-bottom {
  padding-top:3px; gap:16px;
}
#lgPickupAssignment .pa-batch-facts {
  align-items:center; gap:0;
}
#lgPickupAssignment .pa-batch-facts span {
  padding:0 15px; border-right:1px solid #e4eaf0;
  color:#8290a0; font-size:11px; line-height:1.4;
}
#lgPickupAssignment .pa-batch-facts span:first-child { padding-left:0; }
#lgPickupAssignment .pa-batch-facts span:last-child { border-right:0; }
#lgPickupAssignment .pa-batch-facts strong { color:#28394e; font-size:15px; }
#lgPickupAssignment .pa-batch-actions { gap:9px; }
#lgPickupAssignment .pa-batch-actions .pa-btn { min-height:41px; padding:10px 17px; font-size:11px; }
#lgPickupAssignment .pa-directory-footer { border-top:1px solid #e9edf2 !important; }
/* Rider modal: restrained white/gray UI, badges carry the green/red signal. */
.pa-modal .pa-modal-panel { box-shadow:0 28px 80px rgba(18,28,42,.22); }
.pa-modal .pa-modal-header { padding:20px 25px; }
.pa-modal .pa-modal-header h2 { font-size:22px; }
.pa-modal .pa-pickup-location {
  padding:14px 16px; border-radius:12px; background:#f7f9fb; border-color:#e7edf3;
}
.pa-modal .pa-modal-section-heading { margin:21px 0 8px; }
.pa-modal .pa-area-disclaimer { margin:3px 0 9px; color:#8896a5; }
.pa-match-legend { display:flex; gap:8px 15px; flex-wrap:wrap; margin:8px 0 14px; }
.pa-match-legend > span { display:inline-flex; align-items:center; gap:6px; font-size:10px; color:#8491a0; }
.pa-match-legend > span > span { width:7px; height:7px; border-radius:50%; background:#4e9c6d; }
.pa-match-legend .pa-legend-other > span { background:#c87878; }
.pa-modal .pa-rider-list { gap:9px; }
.pa-modal .pa-rider-option {
  border-radius:13px; border-color:#e4eaf0; background:white;
  padding:14px 15px; box-shadow:none;
}
.pa-modal .pa-rider-option:not(.is-busy):hover { background:#f8fafb; border-color:#cbd5df; }
.pa-modal .pa-rider-option:has(.is-recommended) { border-left:3px solid #69b388; }
.pa-modal .pa-rider-option:has(.is-other) { border-left:3px solid #d9a1a1; }
.pa-modal .pa-rider-option.is-selected,
.pa-modal .pa-rider-option.is-selected:hover { background:#f8fafc; border-color:#91a5bd; box-shadow:0 0 0 2px rgba(123,144,166,.09); }
.pa-modal .pa-rider-badge {
  display:inline-flex; align-items:center; gap:5px; padding:6px 9px;
  border:1px solid transparent; border-radius:999px;
  font-size:10px; font-weight:750; line-height:1.1;
}
.pa-modal .pa-rider-badge.is-recommended {
  background:#eaf8ee; border-color:#d3ebd9; color:#267a48;
}
.pa-modal .pa-rider-badge.is-other {
  background:#fff1f1; border-color:#f2d9d9; color:#b25757;
}
.pa-modal .pa-rider-badge.is-muted {
  background:#fff1f1; border-color:#f0d8d8; color:#ac6262;
}
.pa-modal .pa-radio-indicator { border-color:#becbd8; }
.pa-modal .pa-rider-option.is-selected .pa-radio-indicator { border-color:#d9950b; }
.pa-modal .pa-rider-option.is-busy { opacity:.72; background:#fafbfc; border-left-color:#eef1f4; }
.pa-modal .pa-selected-area-match { display:block; margin-top:3px; color:#a96060; font-size:10px; font-weight:600; }
.pa-modal .pa-selected-area-match.is-best { color:#34865b; }
@media(max-width:760px) {
  #lgPickupAssignment .pa-batch-compact { padding:17px 15px !important; gap:15px; }
  #lgPickupAssignment .pa-directory-head { padding:16px !important; }
  #lgPickupAssignment .pa-route-strip { grid-template-columns:1fr; gap:8px; padding:14px; }
  #lgPickupAssignment .pa-route-chevron { transform:rotate(90deg); text-align:left; margin-left:5px; }
  #lgPickupAssignment .pa-batch-facts span { padding:0 10px; }
  #lgPickupAssignment .pa-batch-actions { width:100%; margin:0; }
  #lgPickupAssignment .pa-batch-actions .pa-btn { flex:1; }
  .pa-modal .pa-modal-header { padding:16px; }
}
@media(max-width:380px) {
  #lgPickupAssignment .pa-batch-facts span { font-size:10px; padding:0 7px; }
  #lgPickupAssignment .pa-batch-facts strong { font-size:13px; }
  .pa-modal .pa-rider-badge { font-size:9px; padding:5px 7px; }
}


/* V3: table-format pickup batches; only the directory workspace floats. */
#lgPickupAssignment .pa-directory { overflow:hidden; }
#lgPickupAssignment .pa-table-header,
#lgPickupAssignment .pa-table-row {
    display:grid !important;
    grid-template-columns:minmax(0,1.12fr) minmax(0,1.55fr) minmax(0,1.18fr) minmax(0,.77fr) minmax(0,.87fr) 112px;
    align-items:center;
    column-gap:12px;
    min-width:0;
}
#lgPickupAssignment .pa-table-header {
    padding:12px 18px;
    background:#f7f9fb;
    border-bottom:1px solid #e5eaf0;
}
#lgPickupAssignment .pa-table-header > span {
    color:#7c8999;
    font-size:10px;
    font-weight:780;
    letter-spacing:.07em;
    text-transform:uppercase;
}
#lgPickupAssignment .pickup-batches > article.pa-batch-clean,
#lgPickupAssignment .pickup-batches > article.pa-batch-clean:hover {
    border:0 !important;
    border-bottom:1px solid #e9edf2 !important;
    border-radius:0 !important;
    box-shadow:none !important;
    background:white !important;
    transform:none !important;
}
#lgPickupAssignment .pickup-batches > article.pa-batch-clean:hover { background:#f7f9fb !important; }
#lgPickupAssignment .pickup-batches > article.pa-batch-clean[data-focused="true"] { background:#f2f5f9 !important; outline:2px solid #bdc8d5; outline-offset:-2px; }
#lgPickupAssignment .pa-table-row { padding:15px 18px; min-height:87px; }
#lgPickupAssignment .pa-table-cell { min-width:0; display:flex; flex-direction:column; gap:4px; color:#5c6b7d; }
#lgPickupAssignment .pa-cell-seller { flex-direction:row; align-items:center; gap:10px; }
#lgPickupAssignment .pa-seller-avatar { width:37px; height:37px; flex-basis:37px; font-size:13px; border:1px solid #e4eaf0; background:#eff2f5; color:#738194; }
#lgPickupAssignment .pa-table-cell-copy { min-width:0; display:flex; flex-direction:column; gap:3px; }
#lgPickupAssignment .pa-row-primary { display:block; font-size:11.5px; line-height:1.45; font-weight:760; color:#2b394d; overflow-wrap:anywhere; }
#lgPickupAssignment .pa-row-muted { display:block; font-size:10px; line-height:1.45; color:#96a2b0; overflow-wrap:anywhere; }
#lgPickupAssignment .pa-cell-pickup .pa-row-muted { display:-webkit-box; -webkit-box-orient:vertical; -webkit-line-clamp:2; overflow:hidden; }
#lgPickupAssignment .pa-cell-volume .pa-row-muted { font-size:9.5px; }
#lgPickupAssignment .pa-ready-status,
#lgPickupAssignment .pa-ready-status.is-blocked { align-self:flex-start; font-size:9px; padding:7px 8px; border-radius:999px; }
#lgPickupAssignment .pa-cell-actions { display:flex; flex-direction:column; align-items:stretch; gap:6px; }
#lgPickupAssignment .pa-cell-actions .pa-btn {
    width:100%; min-height:30px; padding:6px 8px; font-size:9.5px; font-weight:720; border-radius:8px;
}
#lgPickupAssignment .pa-row-notice { margin:0 18px 12px; color:#738295; background:#f6f8fa; border-color:#e6ebf1; font-size:10px; }
#lgPickupAssignment .pa-directory-footer { padding:13px 18px; }
/* Rider modal: ONLY Recommended badge has semantic color. No colored rail, radio or selection background. */
.pa-modal .pa-overline { color:#8795a5 !important; }
.pa-modal .pa-rider-option,
.pa-modal .pa-rider-option:has(.is-recommended),
.pa-modal .pa-rider-option:has(.is-other) {
    border:1px solid #e2e8ef !important;
    border-left:1px solid #e2e8ef !important;
    background:#fff !important;
    box-shadow:none !important;
}
.pa-modal .pa-rider-option:not(.is-busy):hover { background:#f7f9fb !important; border-color:#cbd5df !important; }
.pa-modal .pa-rider-option.is-selected,
.pa-modal .pa-rider-option.is-selected:hover,
.pa-modal .pa-rider-option.is-selected:has(.is-recommended),
.pa-modal .pa-rider-option.is-selected:has(.is-other) {
    border:1px solid #d9950b !important;
    border-left:1px solid #d9950b !important;
    background:#fff !important;
    box-shadow:none !important;
}
.pa-modal .pa-rider-option.is-busy { opacity:.67; background:#f9fafb !important; }
.pa-modal .pa-rider-badge, .pa-modal .pa-rider-badge.is-other, .pa-modal .pa-rider-badge.is-muted {
    padding:6px 9px;
    border:1px solid #e3e8ee !important;
    background:#f4f6f8 !important;
    color:#738294 !important;
}
.pa-modal .pa-rider-badge.is-recommended {
    border:1px solid #d4eade !important;
    background:#ecf8f1 !important;
    color:#2c8a58 !important;
}
.pa-modal .pa-radio-indicator { border-color:#bcc6d2 !important; }
.pa-modal .pa-rider-option.is-selected .pa-radio-indicator { border-color:#d9950b !important; }
.pa-modal .pa-selected-area-match { color:#79879a !important; }
.pa-modal .pa-selected-area-match.is-best { color:#79879a !important; }
.pa-modal .pa-match-legend { display:none !important; }
.pa-modal .pa-btn-gold { background:#d9950b !important; border-color:#d9950b !important; color:#fff !important; }
.pa-modal .pa-btn-gold:hover:not(:disabled) { background:#bf8009 !important; border-color:#bf8009 !important; }
.pa-modal .pa-rider-empty a { color:#657487 !important; }
@media(max-width:1120px) {
    #lgPickupAssignment .pa-table-header { display:none !important; }
    #lgPickupAssignment .pa-table-row { grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px 18px; align-items:start; padding:18px; }
    #lgPickupAssignment .pa-table-cell:not(.pa-cell-seller)::before {
        content:attr(data-label); display:block; margin-bottom:3px; color:#9aa6b4;
        font-size:9px; font-weight:750; letter-spacing:.07em; text-transform:uppercase;
    }
    #lgPickupAssignment .pa-cell-seller { grid-column:1/-1; }
    #lgPickupAssignment .pa-cell-actions { grid-column:1/-1; flex-direction:row; justify-content:flex-end; }
    #lgPickupAssignment .pa-cell-actions .pa-btn { width:auto; min-width:116px; }
}
@media(max-width:550px) {
    #lgPickupAssignment .pa-table-row { grid-template-columns:repeat(2,minmax(0,1fr)); padding:15px; gap:15px 12px; }
    #lgPickupAssignment .pa-cell-pickup,
    #lgPickupAssignment .pa-cell-hub { grid-column:1/-1; }
    #lgPickupAssignment .pa-cell-actions { justify-content:stretch; }
    #lgPickupAssignment .pa-cell-actions .pa-btn { flex:1; min-width:0; font-size:10px; }
    #lgPickupAssignment .pa-row-primary { font-size:12px; }
    #lgPickupAssignment .pa-row-muted { font-size:10px; }
}


/* V4: stable scrollable assignment modal; header and actions always visible. */
.pa-modal .pa-assign-panel {
    display:flex !important;
    flex-direction:column !important;
    height:min(760px, calc(100vh - 36px));
    height:min(760px, calc(100dvh - 36px));
    max-height:calc(100dvh - 36px);
    overflow:hidden;
    overflow:clip;
}
.pa-modal .pa-assign-panel > form[data-pickup-dispatch-form] {
    display:flex !important;
    flex:1 1 auto;
    flex-direction:column;
    min-height:0;
    overflow:hidden;
    overflow:clip;
    margin:0;
}
.pa-modal .pa-assign-panel .pa-modal-header,
.pa-modal .pa-assign-panel .pa-modal-footer { flex:0 0 auto; }
.pa-modal .pa-assign-panel .pa-modal-body {
    display:block;
    flex:1 1 auto;
    min-height:0;
    overflow-y:auto !important;
    overscroll-behavior:contain;
    -webkit-overflow-scrolling:touch;
    scrollbar-width:thin;
    scrollbar-color:#cbd4df transparent;
}
.pa-modal .pa-assign-panel .pa-modal-footer {
    position:relative;
    z-index:2;
    background:#fff;
    box-shadow:0 -5px 18px rgba(22,35,50,.035);
}
.pa-modal .pa-assign-panel .pa-rider-option.is-selected,
.pa-modal .pa-assign-panel .pa-rider-option.is-selected:hover {
    border:1px solid #d9950b !important;
    background:#fff !important;
    box-shadow:none !important;
}
.pa-modal .pa-assign-panel .pa-rider-option.is-selected .pa-radio-indicator {
    border-color:#d9950b !important;
}
/* Avoid stacked focus rings on pointer selection; keep a keyboard-only focus indicator. */
.pa-modal .pa-assign-panel .pa-rider-option:focus-within { outline:none; }
.pa-modal .pa-assign-panel .pa-rider-option:has(input:focus-visible) {
    outline:2px solid #aebbc9;
    outline-offset:2px;
}
.pa-modal .pa-assign-panel [data-pa-submit] {
    background:#d9950b !important;
    border:1px solid #d9950b !important;
    color:#fff !important;
    min-height:42px;
}
.pa-modal .pa-assign-panel [data-pa-submit]:hover:not(:disabled) {
    background:#bf8009 !important;
    border-color:#bf8009 !important;
}
.pa-modal .pa-assign-panel [data-pa-submit]:disabled {
    background:#d9950b !important;
    border-color:#d9950b !important;
    opacity:.5;
}
@media (max-width:760px) {
    .pa-modal .pa-assign-panel {
        height:calc(100vh - 20px);
        height:calc(100dvh - 20px);
        max-height:calc(100dvh - 20px);
    }
    .pa-modal .pa-assign-panel .pa-modal-footer {
        gap:9px;
        padding:12px 16px max(12px,env(safe-area-inset-bottom));
    }
    .pa-modal .pa-assign-panel .pa-modal-footer .pa-btn {
        flex:1 1 0;
        min-width:0;
    }
}

</style>
@endpush

@section('content')

<div
    id="lgPickupAssignment"
    class="sari-logistics-ui pickup-page"
    data-pickup-workspace="assignment"
    data-focus-order="{{ (int) request('order', 0) }}"
>

    {{-- PAGE HEADER --}}

    <header class="pickup-page-header">

        <div class="min-w-0">

            <p class="pickup-eyebrow">
                SELLER COLLECTIONS
            </p>

            <h1>
                Pickup <span>Assignment</span>
            </h1>

        </div>

        <a
            href="{{ route('logistics.pickup-requests') }}"
            class="pickup-button"
        >
            ← Pickup Requests
        </a>

    </header>

    {{-- FLASH MESSAGES --}}

    @if(session('success'))

        <div
            class="pickup-alert pickup-alert-success"
            role="status"
        >
            <strong>Pickup Rider assigned</strong>
            <p>{{ session('success') }}</p>
        </div>

    @endif

    @if($errors->any())

        <div
            class="pickup-alert pickup-alert-danger"
            role="alert"
        >
            <strong>Could not assign pickup batch</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif

    {{-- KPI DATA --}}

    @php
        $summaryCards = [
            [
                'Verified parcels',
                $stats['verified_orders'],
                'Waiting for Pickup Rider',
                'green',
            ],
            [
                'Pickup batches',
                $stats['pickup_groups'],
                'Seller collection stops',
                'amber',
            ],
            [
                'Available Riders',
                $stats['available_riders'],
                'Online without active work',
                'blue',
            ],
            [
                'Awaiting full verification',
                $stats['blocked_groups'],
                'Groups with unverified siblings',
                'neutral',
            ],
        ];
    @endphp

    {{-- KPI CARDS --}}

    <section
        class="pickup-stats"
        aria-label="Pickup Assignment overview"
    >

        @foreach($summaryCards as [$label, $value, $note, $tone])

            <article class="pickup-stat pickup-stat-{{ $tone }}">

                <div class="min-w-0">

                    <p>{{ $label }}</p>

                    <strong>
                        {{ number_format($value) }}
                    </strong>

                    <span>{{ $note }}</span>

                </div>

                <span
                    class="pickup-stat-icon"
                    aria-hidden="true"
                >

                    @switch($tone)

                        @case('green')
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M4 5h16v14H4zM8 9h8M8 13h8"/>
                                <path d="m15 16 2 2 3-3"/>
                            </svg>
                            @break

                        @case('amber')
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

                        @case('blue')
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="8" cy="8" r="3"/>
                                <circle cx="17" cy="9" r="2.5"/>
                                <path d="M2.5 20c.5-4 2.5-6 5.5-6M13 15c3.2 0 5.2 1.7 5.7 5"/>
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
                                <path d="M12 7v5l3 2"/>
                            </svg>

                    @endswitch

                </span>

            </article>

        @endforeach

    </section>

    {{-- Standalone Sorting Facilities-style directory controls. --}}
    <section class="pa-directory-filters" aria-label="Search and filter pickup batches">
        <label class="pa-directory-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.4-3.4"/></svg>
            <span class="sr-only">Search pickup batches</span>
            <input type="search" data-assignment-search autocomplete="off" placeholder="Search Seller, order, Buyer, or hub...">
        </label>
        <label class="pa-directory-select">
            <span class="sr-only">Filter batch type</span>
            <select data-assignment-type aria-label="Filter pickup batch type">
                <option value="all">All Batches</option>
                <option value="batch">Multi-parcel</option>
                <option value="single">Single parcel</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
        </label>
        <button type="button" class="pa-directory-apply" data-assignment-apply>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
            Apply Filter
        </button>
        <button type="button" class="pa-directory-reset" data-assignment-clear>Reset</button>
    </section>

    {{-- Dispatch workspace stays as functional per-Seller batch cards. --}}
    <section class="pickup-workspace pa-directory" aria-labelledby="pickup-assignment-title">
        <header class="pa-directory-head">
            <h2 id="pickup-assignment-title">Verified Pickup Batches</h2>
            <span class="pa-directory-count">{{ $pickupGroups->count() }} {{ $pickupGroups->count() === 1 ? 'BATCH' : 'BATCHES' }}</span>
        </header>

        {{-- BATCH LIST --}}

        {{-- Compact, table-like directory header; mobile switches to readable stacked rows. --}}
        @if($pickupGroups->isNotEmpty())
            <div class="pa-table-header" role="row" aria-hidden="true">
                <span>Seller</span><span>Pickup Location</span><span>Origin Hub</span><span>Batch</span><span>Status</span><span>Actions</span>
            </div>
        @endif
        <div class="pickup-batches" role="rowgroup">

            @foreach($pickupGroups as $group)

                @php
                    $sellerName = $group['seller_name'] ?: 'Seller Pickup';

                    $sellerInitial = mb_strtoupper(
                        mb_substr(trim($sellerName), 0, 1)
                    );

                    $batchType = $group['order_count'] > 1
                        ? 'batch'
                        : 'single';

                    $blocked = (bool) $group['is_blocked'];

                    $orderIds = $group['orders']
                        ->pluck('id')
                        ->map(fn ($id) => (int) $id)
                        ->values();

                    $searchValues = [
                        $sellerName,
                        $group['pickup_name'],
                        $group['pickup_address'],
                        $group['origin_facility_name'],
                    ];

                    foreach($group['orders'] as $searchOrder) {

                        $searchValues[] = $searchOrder->order_number;
                        $searchValues[] = $searchOrder->buyer_name;

                        $destination = $searchOrder
                            ->deliveryRouteStops
                            ->firstWhere(
                                'stop_key',
                                'destination_facility'
                            );

                        $searchValues[] =
                            $destination?->facility?->name
                            ?: $destination?->name;
                    }

                    $searchText = implode(
                        ' ',
                        array_filter($searchValues)
                    );
                @endphp

                <article
                    class="pickup-batch pa-batch-clean"
                    data-assignment-batch
                    data-batch-type="{{ $batchType }}"
                    data-batch-blocked="{{ $blocked ? 'true' : 'false' }}"
                    data-search="{{ $searchText }}"
                    data-order-ids="{{ $orderIds->implode(',') }}"
                >

                    @php
                        // Preserve the controller's pickup-service-area eligibility rule.
                        // Location ranking is based only on registered areas, not live GPS proximity.
                        $pickupLocation = trim((string) $group['pickup_address']);
                        $normalizeArea = static function ($value): string {
                            $text = mb_strtolower(trim((string) $value));
                            return trim(preg_replace('/[^\pL\pN]+/u', ' ', $text) ?? '');
                        };
                        $pickupMatchText = ' ' . $normalizeArea($pickupLocation) . ' ';
                        $localityMatch = static function ($value) use ($normalizeArea, $pickupMatchText): bool {
                            $normalized = $normalizeArea($value);
                            $normalized = preg_replace('/^(?:barangay|brgy)\s+/', '', $normalized) ?: $normalized;
                            return mb_strlen($normalized) >= 3
                                && mb_strpos($pickupMatchText, ' ' . $normalized . ' ') !== false;
                        };
                        $eligibleRiderChoices = $riders
                            ->filter(fn ($candidate) => \App\Support\LogisticsServiceArea::matchesPickupRider($candidate, $group['pickup_address']))
                            ->map(function ($candidate) use ($localityMatch) {
                                $riderBarangay = $candidate->getAttribute('duty_barangay_name') ?: $candidate->barangay_name;
                                $riderMunicipality = $candidate->getAttribute('duty_municipality_name') ?: $candidate->municipality_name;
                                return [
                                    'rider' => $candidate,
                                    'busy' => (bool) $candidate->getAttribute('is_dispatch_busy'),
                                    'score' => ($localityMatch($riderBarangay) ? 2 : 0)
                                        + ($localityMatch($riderMunicipality) ? 1 : 0),
                                    'barangay' => $riderBarangay,
                                    'municipality' => $riderMunicipality,
                                ];
                            })
                            ->sort(function ($a, $b) {
                                if ($a['busy'] !== $b['busy']) return $a['busy'] ? 1 : -1;
                                return $b['score'] <=> $a['score'];
                            })
                            ->values();
                        $freeRiderChoices = $eligibleRiderChoices->where('busy', false);
                        $recommendedRiderChoice = $freeRiderChoices->first(fn ($choice) => $choice['score'] > 0);
                        $modalKey = 'pa-' . $loop->iteration;
                    @endphp

                    <div class="pa-table-row" role="row">
                        <div class="pa-table-cell pa-cell-seller" role="cell" data-label="Seller">
                            <span class="pa-seller-avatar" aria-hidden="true">{{ $sellerInitial ?: 'S' }}</span>
                            <div class="pa-table-cell-copy">
                                <strong class="pa-row-primary">{{ $sellerName }}</strong>
                                <span class="pa-row-muted">{{ $group['buyer_count'] }} {{ $group['buyer_count'] === 1 ? 'buyer' : 'buyers' }}</span>
                            </div>
                        </div>
                        <div class="pa-table-cell pa-cell-pickup" role="cell" data-label="Pickup Location">
                            <strong class="pa-row-primary">{{ $group['pickup_name'] ?: $sellerName }}</strong>
                            <span class="pa-row-muted" title="{{ $group['pickup_address'] }}">{{ $group['pickup_address'] }}</span>
                        </div>
                        <div class="pa-table-cell pa-cell-hub" role="cell" data-label="Origin Hub">
                            <strong class="pa-row-primary">{{ $group['origin_facility_name'] }}</strong>
                            <span class="pa-row-muted">Destination for this Seller collection</span>
                        </div>
                        <div class="pa-table-cell pa-cell-volume" role="cell" data-label="Batch">
                            <strong class="pa-row-primary">{{ $group['order_count'] }} {{ $group['order_count'] === 1 ? 'parcel' : 'parcels' }}</strong>
                            <span class="pa-row-muted">{{ $group['destination_count'] }} {{ $group['destination_count'] === 1 ? 'destination' : 'destinations' }}</span>
                            <span class="pa-row-muted">{{ $freeRiderChoices->count() }} Riders available</span>
                        </div>
                        <div class="pa-table-cell pa-cell-status" role="cell" data-label="Status">
                            <span class="pa-ready-status {{ $blocked ? 'is-blocked' : '' }}">
                                <span class="pa-status-dot" aria-hidden="true"></span>
                                {{ $blocked ? 'Verification pending' : 'Ready to assign' }}
                            </span>
                        </div>
                        <div class="pa-table-cell pa-cell-actions" role="cell" data-label="Actions">
                            <button type="button" class="pa-btn pa-btn-outline" data-pa-open-modal="{{ $modalKey }}-details">View Parcels</button>
                            <button type="button" class="pa-btn pa-btn-gold" data-pa-open-modal="{{ $modalKey }}-assign" @disabled($blocked || $freeRiderChoices->isEmpty())>Assign Rider</button>
                        </div>
                    </div>
                    @if($blocked)
                        <p class="pa-inline-notice pa-row-notice">Verify the remaining {{ $group['pending_count'] }} {{ $group['pending_count'] === 1 ? 'parcel' : 'parcels' }} before assigning. <a href="{{ route('logistics.pickup-requests') }}">Review pickup requests</a></p>
                    @elseif($freeRiderChoices->isEmpty())
                        <p class="pa-inline-notice pa-row-notice">No free Rider covers this pickup area right now. <a href="{{ route('logistics.rider-management') }}">Rider Management</a></p>
                    @endif
                </article>

                {{-- Modal shells are moved to <body> on initialization, outside clipped workspace cards. --}}
                <div class="pa-modal" data-pa-modal="{{ $modalKey }}-details" role="dialog" aria-modal="true" aria-labelledby="{{ $modalKey }}-details-title" hidden>
                    <div class="pa-modal-scrim" data-pa-close-modal></div>
                    <div class="pa-modal-panel pa-details-panel" role="document">
                        <div class="pa-modal-header">
                            <div><span class="pa-overline">SELLER BATCH DETAILS</span><h2 id="{{ $modalKey }}-details-title">{{ $sellerName }}</h2><p>{{ $group['order_count'] }} verified {{ $group['order_count'] === 1 ? 'parcel' : 'parcels' }} · {{ $group['destination_count'] }} {{ $group['destination_count'] === 1 ? 'destination' : 'destinations' }}</p></div>
                            <button type="button" class="pa-modal-x" data-pa-close-modal aria-label="Close details">×</button>
                        </div>
                        <div class="pa-modal-body">
                            <div class="pa-detail-route">
                                <div><small>SELLER PICKUP</small><strong>{{ $group['pickup_name'] ?: $sellerName }}</strong><span>{{ $group['pickup_address'] }}</span></div>
                                <span aria-hidden="true">→</span>
                                <div><small>ORIGIN SORTING HUB</small><strong>{{ $group['origin_facility_name'] }}</strong><span>Pickup Rider delivers the entire batch here</span></div>
                            </div>
                            <div class="pa-modal-section-heading"><h3>Verified parcels</h3><span>{{ $group['order_count'] }} total</span></div>
                            <div class="pa-parcel-list">
                                @foreach($group['orders'] as $order)
                                    @php
                                        $destination = $order->deliveryRouteStops->firstWhere('stop_key', 'destination_facility');
                                        $destinationName = $destination?->facility?->name ?: $destination?->name ?: 'Destination pending';
                                    @endphp
                                    <details class="pa-parcel-item">
                                        <summary>
                                            <span><strong>{{ $order->order_number }}</strong><small>{{ $order->buyer_name }} · {{ $destinationName }}</small></span>
                                            <span class="pa-parcel-money">₱{{ number_format((float) $order->total, 2) }}</span>
                                            <span class="pa-parcel-toggle" aria-hidden="true">+</span>
                                        </summary>
                                        <div class="pa-parcel-expanded">
                                            <div><span>Buyer delivery address</span><strong>{{ $order->buyer_address ?: 'Not provided' }}</strong></div>
                                            <div><span>Destination hub</span><strong>{{ $destinationName }}</strong></div>
                                            <div><span>Payment</span><strong>{{ $order->payment_method ?: 'Not recorded' }} · {{ ucfirst($order->payment_status ?: 'pending') }}</strong></div>
                                            @if(!empty($order->items))
                                                <div class="pa-products"><span>Items</span>
                                                    @foreach($order->items as $item)
                                                        <p>{{ $item['name'] ?? 'Product' }}@if(!empty($item['variant_label'])) · {{ $item['variant_label'] }}@endif × {{ $item['qty'] ?? $item['quantity'] ?? 1 }}</p>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </details>
                                @endforeach
                            </div>
                            @if($group['destination_names']->isNotEmpty())
                                <div class="pa-dest-list"><span>Future destination hubs</span><div>@foreach($group['destination_names'] as $destinationName)<span>{{ $destinationName }}</span>@endforeach</div></div>
                            @endif
                        </div>
                        <div class="pa-modal-footer"><button type="button" class="pa-btn pa-btn-outline" data-pa-close-modal>Close</button>
                            <button type="button" class="pa-btn pa-btn-gold" data-pa-open-modal="{{ $modalKey }}-assign" @disabled($blocked || $freeRiderChoices->isEmpty())>Assign Rider →</button>
                        </div>
                    </div>
                </div>

                <div class="pa-modal" data-pa-modal="{{ $modalKey }}-assign" role="dialog" aria-modal="true" aria-labelledby="{{ $modalKey }}-assign-title" hidden>
                    <div class="pa-modal-scrim" data-pa-close-modal></div>
                    <div class="pa-modal-panel pa-assign-panel" role="document">
                        <div class="pa-modal-header">
                            <div><span class="pa-overline">RIDER DISPATCH</span><h2 id="{{ $modalKey }}-assign-title">Assign Pickup Rider</h2><p>{{ $sellerName }} · {{ $group['order_count'] }} {{ $group['order_count'] === 1 ? 'parcel' : 'parcels' }}</p></div>
                            <button type="button" class="pa-modal-x" data-pa-close-modal aria-label="Close Rider assignment">×</button>
                        </div>
                        <form method="POST" action="{{ route('logistics.delivery-assignment.batch-assign') }}" data-pickup-dispatch-form data-blocked="{{ $blocked ? 'true' : 'false' }}">
                            @csrf
                            @foreach($group['orders'] as $order)
                                <input type="hidden" name="order_ids[]" value="{{ $order->id }}">
                            @endforeach
                            <div class="pa-modal-body">
                                <div class="pa-pickup-location"><span>SELLER PICKUP LOCATION</span><strong>{{ $group['pickup_name'] ?: $sellerName }}</strong><p>{{ $group['pickup_address'] }}</p></div>
                                <div class="pa-modal-section-heading"><div><h3>Choose a Rider</h3><p>{{ $freeRiderChoices->count() }} available in this service area</p></div></div>
                                <p class="pa-area-disclaimer">The best match uses the Rider’s registered barangay / municipality—not live GPS distance. All selectable Riders are already eligible for this pickup.</p>
                                @if($eligibleRiderChoices->isNotEmpty())
                                    <label class="pa-rider-search-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><span class="sr-only">Search available Riders</span><input type="search" placeholder="Search Rider name, area, or vehicle…" data-pa-rider-search autocomplete="off"></label>
                                    <div class="pa-rider-list" role="radiogroup" aria-label="Eligible pickup Riders">
                                        @foreach($eligibleRiderChoices as $choice)
                                            @php
                                                $rider = $choice['rider'];
                                                $busy = $choice['busy'];
                                                $riderName = trim($rider->first_name . ' ' . $rider->last_name);
                                                $riderArea = collect([$choice['barangay'], $choice['municipality'], $rider->province_name])->filter()->unique()->implode(', ');
                                                $riderVehicle = collect([$rider->vehicle_type, $rider->vehicle_model])->filter()->implode(' · ');
                                                $isRecommended = $recommendedRiderChoice && (int) $recommendedRiderChoice['rider']->id === (int) $rider->id;
                                                $riderInitials = mb_strtoupper(mb_substr(trim((string) $rider->first_name), 0, 1) . mb_substr(trim((string) $rider->last_name), 0, 1));
                                            @endphp
                                            <label class="pa-rider-option {{ $busy ? 'is-busy' : '' }}" data-pa-rider-option data-search="{{ $riderName . ' ' . $riderArea . ' ' . $riderVehicle }}">
                                                <input type="radio" name="rider_id" value="{{ $rider->id }}" data-pa-rider-radio data-rider-name="{{ $riderName }}" data-rider-match="{{ $isRecommended ? 'recommended' : 'other' }}" @disabled($busy || $blocked) required>
                                                <span class="pa-rider-avatar" aria-hidden="true">{{ $riderInitials ?: 'R' }}</span>
                                                <span class="pa-rider-info"><span class="pa-rider-name">{{ $riderName }}</span><span class="pa-rider-area">{{ $riderArea ?: 'Location not specified' }}</span><span class="pa-rider-vehicle">{{ $riderVehicle ?: 'Vehicle not specified' }}@if($rider->plate_number) · {{ $rider->plate_number }}@endif</span></span>
                                                <span class="pa-rider-badges">@if($busy)<span class="pa-rider-badge is-muted">Busy · unavailable</span>@elseif($isRecommended)<span class="pa-rider-badge is-recommended">Recommended</span>@else<span class="pa-rider-badge is-other">Eligible</span>@endif<span class="pa-radio-indicator" aria-hidden="true"></span></span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <p class="pa-no-search" data-pa-rider-no-results hidden>No Riders match this search.</p>
                                @else
                                    <div class="pa-rider-empty">No Rider is currently eligible for this Seller pickup location. <a href="{{ route('logistics.rider-management') }}">View Rider Management</a>.</div>
                                @endif
                            </div>
                            <div class="pa-modal-footer pa-assign-footer">
                                <div class="pa-assignment-summary"><small>ASSIGNING {{ $group['order_count'] }} {{ $group['order_count'] === 1 ? 'PARCEL' : 'PARCELS' }}</small><strong data-pa-selected-name>Select a Rider above</strong><span class="pa-selected-area-match" data-pa-selected-match aria-live="polite"></span></div>
                                <button type="button" class="pa-btn pa-btn-outline" data-pa-close-modal>Cancel</button>
                                <button type="submit" class="pa-btn pa-btn-gold" data-pa-submit disabled>Confirm Assignment →</button>
                            </div>
                        </form>
                    </div>
                </div>

            @endforeach

        </div>

        {{-- EMPTY ASSIGNMENT QUEUE --}}

        @if($pickupGroups->isEmpty())

            <div class="pickup-empty">

                <span class="pickup-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3.5 7 12 2.5 20.5 7v10L12 21.5 3.5 17V7Z"/><path d="m3.5 7 8.5 4.5L20.5 7M12 11.5v10M8 4.5l8.5 4.5"/></svg>
                </span>

                <h3>
                    No verified pickup batches yet
                </h3>

                <p>

                    @if($stats['unverified_orders'] > 0)

                        {{ $stats['unverified_orders'] }}
                        ready pickup
                        {{
                            $stats['unverified_orders'] === 1
                                ? 'request still needs'
                                : 'requests still need'
                        }}
                        verification.

                    @else

                        Requests appear when Sellers mark orders ready
                        for pickup and Logistics verifies them.

                    @endif

                </p>

                <a
                    href="{{ route('logistics.pickup-requests') }}"
                    class="pickup-text-link pa-empty-action"
                >
                    Review Pickup Requests →
                </a>

            </div>

        @endif

        {{-- FILTER EMPTY STATE --}}

        <div
            class="pickup-empty"
            data-assignment-empty
            hidden
        >
            <span class="pickup-empty-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m15.5 15.5 5 5M8 10.5h5"/></svg>
            </span>

            <h3>
                No matching batches
            </h3>

            <p>
                Try another search or clear the filter.
            </p>

            <button
                type="button"
                class="pickup-text-link"
                data-assignment-clear
            >
                Clear filters
            </button>

        </div>

        {{-- Result count and page size. --}}
        <footer class="pickup-list-footer pa-directory-footer">
            <span data-assignment-visible-count aria-live="polite">{{ $pickupGroups->count() }} pickup batches shown</span>
            <label class="pa-page-size">Rows per page
                <select data-assignment-page-size aria-label="Batches per page">
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </label>
        </footer>

    </section>

    <nav class="pa-pagination" aria-label="Pickup assignment pages" data-assignment-pagination>
        <button class="pa-page-arrow" type="button" data-assignment-prev aria-label="Previous page" disabled>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <div class="pa-page-numbers" data-assignment-pages role="group" aria-label="Page numbers"></div>
        <button class="pa-page-arrow" type="button" data-assignment-next aria-label="Next page" disabled>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
        </button>
    </nav>

</div>

@endsection

@push('scripts')

<script>
(() => {

    'use strict';

    function initialize() {

        document.querySelectorAll(
            '[data-pickup-workspace="assignment"]'
        ).forEach((root) => {

            if (root.dataset.initialized) {
                return;
            }

            root.dataset.initialized = 'true';

            /*
            |--------------------------------------------------------------------------
            | ELEMENT REFERENCES
            |--------------------------------------------------------------------------
            */

            const batches = [
                ...root.querySelectorAll('[data-assignment-batch]')
            ];

            const search = root.querySelector(
                '[data-assignment-search]'
            );

            const typeFilter = root.querySelector('[data-assignment-type]');
            const pageSizeControl = root.querySelector('[data-assignment-page-size]');
            const pageNumbers = root.querySelector('[data-assignment-pages]');
            const previousPage = root.querySelector('[data-assignment-prev]');
            const nextPage = root.querySelector('[data-assignment-next]');
            const pager = root.querySelector('[data-assignment-pagination]');
            const empty = root.querySelector('[data-assignment-empty]');
            const visibleCount = root.querySelector('[data-assignment-visible-count]');
            const normalize = value => String(value || '').toLocaleLowerCase().trim();
            const searchIndex = batches.map(batch => ({
                batch,
                text: normalize(batch.dataset.search),
                type: batch.dataset.batchType || ''
            }));
            let currentPage = 1;

            function renderPageNumbers(pageCount) {
                if (!pageNumbers) return;
                const visibleCount = Math.min(5, pageCount);
                const start = Math.min(
                    Math.max(1, currentPage - Math.floor(visibleCount / 2)),
                    Math.max(1, pageCount - visibleCount + 1)
                );
                const buttons = document.createDocumentFragment();
                for (let page = start; page < start + visibleCount; page++) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'pa-page-number' + (page === currentPage ? ' is-current' : '');
                    button.textContent = String(page);
                    button.setAttribute('aria-label', `Page ${page}`);
                    if (page === currentPage) button.setAttribute('aria-current', 'page');
                    button.addEventListener('click', () => {
                        if (page === currentPage) return;
                        currentPage = page;
                        applyFilters(false);
                    });
                    buttons.appendChild(button);
                }
                pageNumbers.replaceChildren(buttons);
            }

            function applyFilters(resetPage = true) {
                if (resetPage) currentPage = 1;
                const keyword = normalize(search?.value);
                const type = typeFilter?.value || 'all';
                const matching = searchIndex.filter(entry =>
                    (type === 'all' || entry.type === type)
                    && (!keyword || entry.text.includes(keyword))
                );
                const size = Math.max(1, Number(pageSizeControl?.value || 10));
                const pageCount = Math.max(1, Math.ceil(matching.length / size));
                currentPage = Math.min(Math.max(1, currentPage), pageCount);
                const offset = (currentPage - 1) * size;
                const selected = new Set(matching.slice(offset, offset + size).map(entry => entry.batch));
                batches.forEach(batch => { batch.hidden = !selected.has(batch); });
                if (empty) empty.hidden = batches.length === 0 || matching.length > 0;
                if (visibleCount) visibleCount.textContent = matching.length
                    ? `Showing ${offset + 1}–${Math.min(offset + size, matching.length)} of ${matching.length} batches`
                    : 'No matching batches';
                renderPageNumbers(pageCount);
                if (previousPage) previousPage.disabled = currentPage <= 1;
                if (nextPage) nextPage.disabled = currentPage >= pageCount;
                if (pager) pager.hidden = batches.length === 0 || pageCount <= 1;
                if (pageSizeControl) {
                    const sizeContainer = pageSizeControl.closest('.pa-page-size');
                    if (sizeContainer) sizeContainer.hidden = batches.length <= 10;
                }
            }

            search?.addEventListener('input', () => applyFilters());
            typeFilter?.addEventListener('change', () => applyFilters());
            pageSizeControl?.addEventListener('change', () => applyFilters());
            root.querySelector('[data-assignment-apply]')?.addEventListener('click', () => applyFilters());
            root.querySelectorAll('[data-assignment-clear]').forEach(button => {
                button.addEventListener('click', () => {
                    if (search) search.value = '';
                    if (typeFilter) typeFilter.value = 'all';
                    applyFilters();
                });
            });
            previousPage?.addEventListener('click', () => { currentPage--; applyFilters(false); });
            nextPage?.addEventListener('click', () => { currentPage++; applyFilters(false); });

            /* Modal presentation uses the existing server-rendered eligible Rider pool.
               Never estimates GPS distance from postal address data. */
            const modalRegistry = new Map();
            let activeModal = null;
            let opener = null;
            let originalBodyOverflow = '';

            root.querySelectorAll('[data-pa-modal]').forEach(modal => {
                modalRegistry.set(modal.dataset.paModal, modal);
                document.body.appendChild(modal); // bypass clipped, transformed layout wrappers
            });

            function closeModal() {
                if (!activeModal) return;
                activeModal.hidden = true;
                activeModal = null;
                document.body.classList.remove('pa-modal-open');
                document.body.style.overflow = originalBodyOverflow;
                if (opener?.isConnected) opener.focus({preventScroll:true});
                opener = null;
            }

            function openModal(id, button) {
                const modal = modalRegistry.get(id);
                if (!modal) return;
                const previousTrigger = activeModal ? opener : button;
                if (activeModal) closeModal();
                originalBodyOverflow = document.body.style.overflow;
                document.body.style.overflow = 'hidden';
                document.body.classList.add('pa-modal-open');
                activeModal = modal;
                opener = previousTrigger;
                modal.hidden = false;
                const searchField = modal.querySelector('[data-pa-rider-search]');
                const closeButton = modal.querySelector('.pa-modal-x');
                (searchField || closeButton)?.focus({preventScroll:true});
            }

            // Triggers in cards are within root; triggers inside portaled modals are on body.
            document.addEventListener('click', event => {
                const trigger = event.target.closest('[data-pa-open-modal]');
                if (trigger && (root.contains(trigger) || activeModal?.contains(trigger))) {
                    if (trigger.disabled) return;
                    event.preventDefault();
                    openModal(trigger.dataset.paOpenModal, trigger);
                    return;
                }
                if (activeModal && event.target.closest('[data-pa-close-modal]') && activeModal.contains(event.target)) {
                    event.preventDefault();
                    closeModal();
                }
            });

            document.addEventListener('keydown', event => {
                if (!activeModal) return;
                if (event.key === 'Escape') { event.preventDefault(); closeModal(); return; }
                if (event.key !== 'Tab') return;
                const focusables = [...activeModal.querySelectorAll('button:not([disabled]), input:not([disabled]), a[href], select:not([disabled]), textarea:not([disabled])')]
                    .filter(el => !el.closest('[hidden]') && !el.closest('[data-pa-rider-option][hidden]'));
                if (!focusables.length) return;
                const first = focusables[0];
                const last = focusables[focusables.length-1];
                if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
                else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
            });

            for (const modal of modalRegistry.values()) {
                const form = modal.querySelector('[data-pickup-dispatch-form]');
                if (!form) continue;
                const radios = [...form.querySelectorAll('[data-pa-rider-radio]')];
                const choices = [...form.querySelectorAll('[data-pa-rider-option]')];
                const summary = form.querySelector('[data-pa-selected-name]');
                const matchText = form.querySelector('[data-pa-selected-match]');
                const submit = form.querySelector('[data-pa-submit]');
                const searchInput = form.querySelector('[data-pa-rider-search]');
                const noResults = form.querySelector('[data-pa-rider-no-results]');
                const normalize = str => String(str || '').toLocaleLowerCase().trim();
                function syncSelection() {
                    const selected = radios.find(input => input.checked && !input.disabled);
                    choices.forEach(choice => choice.classList.toggle('is-selected', !!choice.querySelector('input:checked')));
                    if (summary) summary.textContent = selected?.dataset.riderName || 'Select a Rider above';
                    if (matchText) {
                        matchText.textContent = !selected ? '' : selected.dataset.riderMatch === 'recommended'
                            ? 'Best registered-area match' : 'Eligible Rider';
                        matchText.classList.toggle('is-best', !!selected && selected.dataset.riderMatch === 'recommended');
                    }
                    if (submit) submit.disabled = !selected || form.dataset.blocked === 'true';
                }
                radios.forEach(input => input.addEventListener('change', syncSelection));
                searchInput?.addEventListener('input', () => {
                    const phrase = normalize(searchInput.value);
                    let found = 0;
                    choices.forEach(choice => {
                        const matches = normalize(choice.dataset.search).includes(phrase);
                        choice.hidden = !matches;
                        if (matches) found++;
                    });
                    if (noResults) noResults.hidden = found !== 0;
                });
                form.addEventListener('submit', event => {
                    const selected = radios.find(input => input.checked && !input.disabled);
                    if (!selected || form.dataset.blocked === 'true') {
                        event.preventDefault();
                        return;
                    }
                    if (form.dataset.submitting === 'true') { event.preventDefault(); return; }
                    form.dataset.submitting = 'true';
                    if (submit) { submit.disabled = true; submit.textContent = 'Assigning batch…'; }
                });
                syncSelection();
            }

            /*
            |--------------------------------------------------------------------------
            | FOCUS REQUESTED ORDER
            |--------------------------------------------------------------------------
            */

            const focus = String(
                root.dataset.focusOrder || ''
            );

            if (focus && focus !== '0') {

                const target = batches.find(batch =>

                    String(batch.dataset.orderIds || '')
                        .split(',')
                        .includes(focus)

                );

                if (target) {

                    target.dataset.focused = 'true';
                    if (search) search.value = '';
                    if (typeFilter) typeFilter.value = 'all';
                    const position = batches.indexOf(target);
                    const size = Math.max(1, Number(pageSizeControl?.value || 10));
                    currentPage = Math.floor(position / size) + 1;

                    setTimeout(() => {

                        target.scrollIntoView({

                            behavior: window.matchMedia(
                                '(prefers-reduced-motion: reduce)'
                            ).matches
                                ? 'auto'
                                : 'smooth',

                            block: 'center',

                        });

                    }, 120);

                    setTimeout(() => {

                        delete target.dataset.focused;

                    }, 3500);

                }

            }

            applyFilters(false);

        });

    }

    /*
    |--------------------------------------------------------------------------
    | INITIALIZATION
    |--------------------------------------------------------------------------
    */

    if (document.readyState === 'loading') {

        document.addEventListener(
            'DOMContentLoaded',
            initialize
        );

    } else {

        initialize();

    }

    /*
    |--------------------------------------------------------------------------
    | BROWSER HISTORY RESTORATION
    |--------------------------------------------------------------------------
    */

    window.addEventListener('pageshow', event => {

        if (event.persisted) {
            window.location.reload();
        }

    });

})();
</script>

@endpush
