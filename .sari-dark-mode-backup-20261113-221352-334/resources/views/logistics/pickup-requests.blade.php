@extends('layouts.logistics')

@section('title', 'Pickup Requests — SARI Logistics')
@section('page-title', 'Pickup Requests')

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
| SARI LOGISTICS — PICKUP REQUESTS
| Seller-style visual refinement
|--------------------------------------------------------------------------
| This styling only applies to Pickup Requests.
| The shared pickup.css remains unchanged for Pickup Assignment.
*/

#lgPickupRequests {
    --pickup-gold: #d9950b;
    --pickup-gold-dark: #c88708;
    --pickup-ink: #25211c;
    --pickup-line: #e8e2d9;
    --pickup-shadow: 0 6px 18px rgba(33,24,14,.035);

    max-width: 1620px;
    font-size: 10px;
}

/* PAGE HEADING */

#lgPickupRequests .pickup-page-header {
    min-height: 0;
    margin-bottom: 14px;
    padding: 5px 2px 17px;
    overflow: visible;

    border: 0;
    border-bottom: 1px solid #e4e1dc;
    border-radius: 0;

    background: transparent;
    box-shadow: none;
    gap: 16px;
}

#lgPickupRequests .pickup-page-header::before,
#lgPickupRequests .pickup-page-header::after {
    display: none;
}

#lgPickupRequests .pickup-page-header .pickup-eyebrow {
    margin-bottom: 5px;
    color: #a76f11;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
}

#lgPickupRequests .pickup-page-header h1 {
    color: #24201b;
    font-size: clamp(23px,2.15vw,30px);
    font-weight: 750;
    line-height: 1.2;
    letter-spacing: -.045em;
}

#lgPickupRequests .pickup-page-header h1 span {
    color: #d9950b;
}

#lgPickupRequests .pickup-subtitle {
    margin-top: 6px;
    max-width: 720px;
    color: #897f75;
    font-size: 10px;
    line-height: 1.7;
}

#lgPickupRequests .pickup-page-header .pickup-button {
    min-height: 39px;
    padding: 0 16px;
    flex-shrink: 0;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 650;
}

#lgPickupRequests .pickup-page-header .pickup-button-primary {
    background: #d9950b;
    border-color: #d9950b;
    color: white;
}

#lgPickupRequests .pickup-page-header .pickup-button-primary:hover {
    background: #c88708;
    border-color: #c88708;
}

/* KPI CARDS */

#lgPickupRequests .pickup-stats {
    gap: 12px;
}

#lgPickupRequests .pickup-stat {
    min-height: 97px;
    padding: 14px;

    display: flex;
    justify-content: space-between;
    gap: 10px;
    overflow: visible;

    border: 1px solid #e8e2d9;
    border-radius: 16px;
    background: white;

    box-shadow: 0 6px 18px rgba(33,24,14,.035);

    transition:
        transform .18s cubic-bezier(.22,1,.36,1),
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgPickupRequests .pickup-stat::after {
    display: none;
}

#lgPickupRequests .pickup-stat p {
    color: #756b60;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: normal;
    text-transform: none;
}

#lgPickupRequests .pickup-stat strong {
    margin: 7px 0 4px;
    color: #211d18;
    font-size: 25px;
    font-weight: 750;
    line-height: 1.15;
    letter-spacing: -.045em;
}

#lgPickupRequests .pickup-stat span:not(.pickup-stat-icon) {
    color: #8d8378;
    font-size: 9px;
    line-height: 1.45;
}

/* Buyer Cart-inspired KPI icons: no colored tiles, boxes or shadows. */
#lgPickupRequests .pickup-stat-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 30px;
    width: 30px;
    height: 30px;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    color: #aab3bd;
    box-shadow: none;
}

#lgPickupRequests .pickup-stat-icon svg {
    display: block;
    width: 30px;
    height: 30px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.55;
}

/* MAIN WORKSPACE */

#lgPickupRequests .pickup-workspace {
    border-radius: 17px;
    border-color: #e8e2d9;
    background: white;
    box-shadow: 0 6px 18px rgba(33,24,14,.035);
}

#lgPickupRequests .pickup-toolbar {
    padding: 15px 17px;
    gap: 14px;
}

#lgPickupRequests .pickup-toolbar h2 {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.035em;
    color: #29231d;
}

#lgPickupRequests .pickup-toolbar p {
    margin-top: 4px;
    color: #93897e;
    font-size: 9px;
    line-height: 1.55;
}

/* SEARCH */

#lgPickupRequests .pickup-search {
    width: min(315px,100%);
}

#lgPickupRequests .pickup-search input {
    height: 39px;
    padding: 0 12px;
    border-color: #dfe2e6;
    border-radius: 10px;
    font-size: 10px;
    background: white;
}

#lgPickupRequests .pickup-search input:focus {
    border-color: #d9950b;
    box-shadow: 0 0 0 3px rgba(217,149,11,.09);
}

/* STATUS FILTERS */

#lgPickupRequests .pickup-filter-row {
    padding: 0 17px 13px;
    gap: 10px;
}

#lgPickupRequests .pickup-tabs {
    gap: 4px;
    padding: 4px;
    border: 1px solid #e6e1d9;
    border-radius: 10px;
    background: #f7f8fa;
}

#lgPickupRequests .pickup-tabs button {
    min-height: 30px;
    padding: 5px 10px;
    font-size: 9px;
    border-radius: 7px;
}

#lgPickupRequests .pickup-tabs button[aria-pressed="true"] {
    border-color: #e9dfce;
    background: white;
    color: #a36c0a;
    box-shadow: 0 2px 5px rgba(35,29,22,.035);
}

#lgPickupRequests .pickup-tabs span {
    min-width: 21px;
    height: 18px;
    font-size: 8px;
}

#lgPickupRequests .pickup-text-link {
    color: #a66d0b;
    font-size: 9px;
    font-weight: 650;
}

/* BULK SELECTION */

#lgPickupRequests .pickup-selection-bar {
    min-height: 49px;
    padding: 9px 17px;
    gap: 11px;
    background: #fbfbfc;
}

#lgPickupRequests .pickup-selection-bar label,
#lgPickupRequests .pickup-selection-bar > span {
    font-size: 9px;
}

#lgPickupRequests .pickup-selection-bar .pickup-button {
    min-height: 35px;
    font-size: 9px;
    padding: 7px 13px;
}

/* REQUEST TABLE */

#lgPickupRequests .pickup-table {
    min-width: 970px;
    font-size: 10px;
    table-layout: fixed;
}

#lgPickupRequests .pickup-table th:nth-child(1) {
    width: 5%;
}

#lgPickupRequests .pickup-table th:nth-child(2) {
    width: 21%;
}

#lgPickupRequests .pickup-table th:nth-child(3) {
    width: 22%;
}

#lgPickupRequests .pickup-table th:nth-child(4) {
    width: 17%;
}

#lgPickupRequests .pickup-table th:nth-child(5) {
    width: 15%;
}

#lgPickupRequests .pickup-table th:nth-child(6) {
    width: 20%;
}

#lgPickupRequests .pickup-table thead {
    background: #f7f8fa;
}

#lgPickupRequests .pickup-table th {
    padding: 12px;
    color: #81776d;
    font-size: 8.5px;
    font-weight: 650;
    letter-spacing: .05em;
}

#lgPickupRequests .pickup-table td {
    min-width: 0;
    padding: 14px 12px;
    vertical-align: top;
    font-size: 10px;
    overflow-wrap: anywhere;
}

#lgPickupRequests .pickup-table td:first-child:has(input[type="checkbox"]) {
    min-width: 0;
    width: auto;
    padding-right: 0;
}

#lgPickupRequests .pickup-table td:last-child {
    min-width: 0;
}

#lgPickupRequests .pickup-table td strong {
    font-size: 10px;
    font-weight: 650;
    color: #443c33;
}

#lgPickupRequests .pickup-table td p {
    margin-top: 4px;
    font-size: 9px;
    line-height: 1.6;
    color: #91887e;
}

#lgPickupRequests .pickup-table td .pickup-address {
    min-width: 0;
    max-width: none;
    font-size: 9px;
}

#lgPickupRequests .pickup-table tbody tr:hover {
    background: #fffaf2;
}

/* STATUS AND ISSUES */

#lgPickupRequests .pickup-badge {
    min-height: 24px;
    padding: 5px 9px;
    font-size: 8.5px;
    font-weight: 650;
}

#lgPickupRequests .pickup-issues {
    max-width: none;
    font-size: 9px;
    line-height: 1.5;
}

/* PARCEL DETAILS */

#lgPickupRequests .pickup-details {
    margin-top: 8px;
}

#lgPickupRequests .pickup-details summary {
    font-size: 9px;
    font-weight: 600;
}

#lgPickupRequests .pickup-detail-body {
    min-width: 0;
    padding: 11px;
    background: #fbfbfc;
    border-color: #e9e3db;
}

#lgPickupRequests .pickup-detail-grid strong {
    font-size: 9px;
}

#lgPickupRequests .pickup-detail-grid p,
#lgPickupRequests .pickup-products li {
    font-size: 9px;
}

#lgPickupRequests .pickup-label {
    font-size: 8px;
}

/* EMPTY STATES */

/* Empty state matches the existing Buyer Cart design scale. */
#lgPickupRequests .pickup-empty {
    padding: 56px 20px 62px;
    text-align: center;
}

#lgPickupRequests .pickup-empty-icon {
    display: grid;
    width: 92px;
    height: 92px;
    margin: 0 auto;
    padding: 0;
    place-items: center;
    border: 0;
    border-radius: 0;
    background: transparent;
    color: #b4bbc3;
    box-shadow: none;
}

#lgPickupRequests .pickup-empty-icon svg {
    display: block;
    width: 58px;
    height: 58px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.45;
    stroke-linecap: round;
    stroke-linejoin: round;
}

#lgPickupRequests .pickup-empty h3 {
    margin: 18px 0 0;
    color: #9ca4ad;
    font-size: 22px;
    font-weight: 750;
    line-height: 1.2;
    letter-spacing: -.025em;
}

#lgPickupRequests .pickup-empty p {
    max-width: 520px;
    margin: 7px auto 0;
    color: #b0b7bf;
    font-size: 12px;
    font-weight: 500;
    line-height: 1.55;
}

#lgPickupRequests .pickup-empty [data-pickup-clear] {
    margin-top: 18px;
}

/* FOOTER */

#lgPickupRequests .pickup-list-footer {
    padding: 12px 16px;
    background: white;
    font-size: 9px;
}

/* ALERTS */

#lgPickupRequests .pickup-alert strong {
    font-size: 10px;
}

#lgPickupRequests .pickup-alert p,
#lgPickupRequests .pickup-alert ul {
    font-size: 9px;
}

/* SUBTLE HOVER */

@media (hover:hover) and (pointer:fine) {
    #lgPickupRequests .pickup-stat:hover {
        transform: translateY(-2px);
        border-color: #d8c9b2;
        box-shadow: 0 13px 28px rgba(43,33,21,.06);
    }
}

/* RESPONSIVE */

@media (max-width:900px) {
    #lgPickupRequests .pickup-stats {
        grid-template-columns: repeat(2,minmax(0,1fr));
    }
}

@media (max-width:767px) {
    #lgPickupRequests .pickup-page-header,
    #lgPickupRequests .pickup-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    #lgPickupRequests .pickup-page-header .pickup-button {
        align-self: flex-start;
    }

    #lgPickupRequests .pickup-filter-row {
        flex-direction: column;
        align-items: stretch;
        padding-inline: 14px;
    }

    #lgPickupRequests .pickup-toolbar {
        padding: 14px;
    }

    #lgPickupRequests .pickup-search {
        width: 100%;
    }

    #lgPickupRequests .pickup-tabs {
        width: 100%;
    }

    #lgPickupRequests .pickup-selection-bar {
        padding-inline: 12px;
    }
}

@media (max-width:480px) {
    #lgPickupRequests .pickup-page-header .pickup-button {
        width: 100%;
    }

    #lgPickupRequests .pickup-stat {
        padding: 12px;
    }

    #lgPickupRequests .pickup-stat strong {
        font-size: 22px;
    }

    #lgPickupRequests .pickup-stat-icon,
    #lgPickupRequests .pickup-stat-icon svg {
        width: 26px;
        height: 26px;
    }
    #lgPickupRequests .pickup-stat-icon { flex-basis: 26px; }

    #lgPickupRequests .pickup-empty { padding: 48px 16px 52px; }
    #lgPickupRequests .pickup-empty-icon { width: 64px; height: 64px; }
    #lgPickupRequests .pickup-empty-icon svg { width: 40px; height: 40px; }
    #lgPickupRequests .pickup-empty h3 { font-size: 20px; }
    #lgPickupRequests .pickup-empty p { font-size: 11px; }
}

@media (prefers-reduced-motion:reduce) {
    #lgPickupRequests .pickup-stat:hover {
        transform: none !important;
    }
}
</style>

<style>
/* PICKUP REQUESTS — SORTING FACILITIES DIRECTORY PARITY (UI ONLY) */
#logisticsContent #lgPickupRequests.pickup-page { --pickup-ui-line:#e4e9ee; --pickup-ui-ink:#26313d; }
#logisticsContent #lgPickupRequests .pickup-admin-filters {
    display:grid; grid-template-columns:minmax(0,1fr) 205px 148px 94px;
    gap:12px; align-items:center; margin:14px 0 14px; min-width:0;
}
#logisticsContent #lgPickupRequests .pickup-admin-search,
#logisticsContent #lgPickupRequests .pickup-admin-select { position:relative; display:block; min-width:0; }
#logisticsContent #lgPickupRequests .pickup-admin-search > svg {
    position:absolute; left:16px; top:50%; transform:translateY(-50%);
    width:17px; height:17px; pointer-events:none; color:#94a0ad;
}
#logisticsContent #lgPickupRequests .pickup-admin-search input,
#logisticsContent #lgPickupRequests .pickup-admin-select select {
    display:block; width:100%; height:46px; border:1px solid #dfe5eb; border-radius:13px;
    background:#fff; box-shadow:0 5px 13px rgba(24,34,45,.025);
    color:#374151; font:500 11px/1.4 Poppins, sans-serif; outline:0;
    transition:border-color .15s ease,box-shadow .15s ease;
}
#logisticsContent #lgPickupRequests .pickup-admin-search input { padding:0 14px 0 43px; }
#logisticsContent #lgPickupRequests .pickup-admin-select select {
    appearance:none; padding:0 33px 0 14px; cursor:pointer;
}
#logisticsContent #lgPickupRequests .pickup-admin-select > svg {
    position:absolute; top:50%; right:12px; width:15px; height:15px;
    transform:translateY(-50%); pointer-events:none; color:#87929e;
}
#logisticsContent #lgPickupRequests .pickup-admin-search input:focus,
#logisticsContent #lgPickupRequests .pickup-admin-select select:focus {
    border-color:#a9b4c0; background:#fff; box-shadow:0 0 0 3px rgba(83,101,118,.08);
}
#logisticsContent #lgPickupRequests .pickup-admin-search input::placeholder {color:#9ba6b4;opacity:1;}
#logisticsContent #lgPickupRequests .pickup-admin-apply,
#logisticsContent #lgPickupRequests .pickup-admin-reset {
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    width:100%; min-width:0; height:46px; border-radius:13px;
    font:650 11px/1.2 Poppins,sans-serif; transition:background-color .15s ease,border-color .15s ease;
}
#logisticsContent #lgPickupRequests .pickup-admin-apply {
    border:1px solid #d9950b; background:#d9950b; color:#fff;
    box-shadow:0 6px 14px rgba(217,149,11,.13);
}
#logisticsContent #lgPickupRequests .pickup-admin-apply:hover {background:#bc7e06;border-color:#bc7e06;}
#logisticsContent #lgPickupRequests .pickup-admin-apply svg {width:15px;height:15px;}
#logisticsContent #lgPickupRequests .pickup-admin-reset {
    border:1px solid #dfe5eb; background:#fff;color:#647083;
}
#logisticsContent #lgPickupRequests .pickup-admin-reset:hover {background:#f4f6f8;}
#logisticsContent #lgPickupRequests .pickup-admin-directory {
    border:1px solid var(--pickup-ui-line); border-radius:19px;
    background:#fff; overflow:hidden;
    box-shadow:0 10px 28px rgba(31,41,55,.06),0 3px 9px rgba(31,41,55,.025);
}
#logisticsContent #lgPickupRequests .pickup-admin-table-head {
    display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between;
    gap:10px; padding:17px 20px; border-bottom:1px solid #e9edf1;
}
#logisticsContent #lgPickupRequests .pickup-admin-table-head h2 {
    margin:0;color:var(--pickup-ui-ink);font-size:16px;font-weight:750;letter-spacing:-.03em;
}
#logisticsContent #lgPickupRequests .pickup-admin-table-head p {
    margin:4px 0 0;color:#929cab;font-size:10px;line-height:1.5;
}
#logisticsContent #lgPickupRequests .pickup-admin-counter {
    display:inline-flex;align-items:center;min-height:31px;padding:0 11px;
    border:1px solid #e3e8ee;border-radius:9px;background:#f8fafc;
    color:#637188;font-size:10px;font-weight:700;white-space:nowrap;
}
#logisticsContent #lgPickupRequests .pickup-selection-bar {
    min-height:47px;padding:10px 20px;gap:12px;background:#fff;border-bottom:1px solid #e9edf1;
}
#logisticsContent #lgPickupRequests .pickup-selection-bar label,
#logisticsContent #lgPickupRequests .pickup-selection-bar > span {font-size:10px;}
#logisticsContent #lgPickupRequests .pickup-selection-bar .pickup-button {font-size:10px;min-height:35px;}
#logisticsContent #lgPickupRequests .pickup-table-scroll {overflow-x:auto;overflow-y:visible;}
#logisticsContent #lgPickupRequests table.pickup-table {
    width:100%;min-width:0;table-layout:fixed;border-collapse:collapse;font-size:11px;
}
#logisticsContent #lgPickupRequests .pickup-table thead {background:#f7f9fb;}
#logisticsContent #lgPickupRequests .pickup-table th {
    padding:13px 12px; color:#697687; border-bottom:1px solid #e6ebef;
    font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;
}
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(1){width:5%;}
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(2){width:21%;}
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(3){width:23%;}
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(4){width:19%;}
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(5){width:14%;}
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(6){width:18%;}
#logisticsContent #lgPickupRequests .pickup-table td {
    min-width:0;padding:15px 12px;vertical-align:top;
    border-bottom:1px solid #edf0f3;color:#586473;font-size:11px;
    overflow-wrap:anywhere;
}
#logisticsContent #lgPickupRequests .pickup-table td:first-child:has(input[type="checkbox"]){min-width:0;padding-left:17px;padding-right:0;}
#logisticsContent #lgPickupRequests .pickup-table td:last-child {min-width:0;}
#logisticsContent #lgPickupRequests .pickup-table td strong {color:#273441;font-size:11px;font-weight:700;}
#logisticsContent #lgPickupRequests .pickup-table td p,
#logisticsContent #lgPickupRequests .pickup-table td .pickup-address {font-size:10px;line-height:1.55;color:#8894a2;min-width:0;max-width:100%;}
#logisticsContent #lgPickupRequests .pickup-table tbody tr:hover {background:#f3f5f7;box-shadow:none;}
#logisticsContent #lgPickupRequests .pickup-table tbody tr:hover td {border-color:#e9edf1;}
#logisticsContent #lgPickupRequests .pickup-badge {min-height:25px;padding:5px 9px;font-size:9px;}
#logisticsContent #lgPickupRequests .pickup-text-link {font-size:10px;}
#logisticsContent #lgPickupRequests .pickup-admin-footer {
    display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;
    gap:10px;padding:12px 20px;border-top:1px solid #e9edf1;
    background:#fff;color:#7d8996;font-size:10px;
}
#logisticsContent #lgPickupRequests .pickup-page-size-label {display:inline-flex;align-items:center;gap:10px;font-size:10px;}
#logisticsContent #lgPickupRequests .pickup-page-size-label select {
    height:34px;min-width:60px;padding:0 8px;border:1px solid #dfe5eb;border-radius:9px;
    background:#fff;color:#526073;font-size:10px;
}
#logisticsContent #lgPickupRequests .pickup-pagination-nav {
    display:flex;align-items:center;justify-content:center;gap:8px;
    padding:14px 0 0;min-height:48px;
}
#logisticsContent #lgPickupRequests .pickup-pagination-nav[hidden] {display:none!important;}
#logisticsContent #lgPickupRequests .pickup-page-buttons {display:flex;align-items:center;justify-content:center;gap:7px;}
#logisticsContent #lgPickupRequests .pickup-page-number,
#logisticsContent #lgPickupRequests .pickup-page-arrow {
    display:grid;place-items:center;width:34px;height:34px;padding:0;
    border:1px solid #e7ebf0;border-radius:10px;background:#f7f9fb;
    color:#8090a2;font-size:11px;font-weight:700;line-height:1;
    transition:background-color .15s ease,color .15s ease;
}
#logisticsContent #lgPickupRequests .pickup-page-arrow svg {width:15px;height:15px;}
#logisticsContent #lgPickupRequests .pickup-page-number.is-current {
    background:#cc901f;border-color:#cc901f;color:#fff;
    box-shadow:0 5px 13px rgba(178,121,18,.16);
}
#logisticsContent #lgPickupRequests .pickup-page-number:not(.is-current):hover,
#logisticsContent #lgPickupRequests .pickup-page-arrow:not(:disabled):hover {background:#ecf0f4;color:#435366;}
#logisticsContent #lgPickupRequests .pickup-page-arrow:disabled {opacity:.38;cursor:not-allowed;}
#logisticsContent #lgPickupRequests .pickup-page-number:focus-visible,
#logisticsContent #lgPickupRequests .pickup-page-arrow:focus-visible {outline:2px solid #b8872b;outline-offset:2px;}
@media(max-width:1100px) {
    #logisticsContent #lgPickupRequests .pickup-admin-filters {grid-template-columns:minmax(0,1fr) 170px 136px 82px;gap:9px;}
    #logisticsContent #lgPickupRequests table.pickup-table {min-width:790px;}
}
@media(max-width:820px) {
    #logisticsContent #lgPickupRequests .pickup-admin-filters {grid-template-columns:minmax(0,1fr) minmax(145px,.45fr);gap:9px;}
    #logisticsContent #lgPickupRequests .pickup-admin-search {grid-column:1/-1;}
    #logisticsContent #lgPickupRequests .pickup-admin-apply,
    #logisticsContent #lgPickupRequests .pickup-admin-reset {height:42px;}
    #logisticsContent #lgPickupRequests .pickup-table-scroll {overflow-x:visible;}
    #logisticsContent #lgPickupRequests table.pickup-table {min-width:0;table-layout:auto;}
    #logisticsContent #lgPickupRequests .pickup-table thead {display:none;}
    #logisticsContent #lgPickupRequests .pickup-table tbody,
    #logisticsContent #lgPickupRequests .pickup-table tr,
    #logisticsContent #lgPickupRequests .pickup-table td {display:block;width:100%;}
    #logisticsContent #lgPickupRequests .pickup-table tbody tr {
        display:grid;grid-template-columns:1fr 1fr;gap:0 16px;padding:11px 16px;
        border-bottom:1px solid #e9edf1;
    }
    #logisticsContent #lgPickupRequests .pickup-table tbody tr[hidden] {display:none!important;}
    #logisticsContent #lgPickupRequests .pickup-table td,
    #logisticsContent #lgPickupRequests .pickup-table td:first-child:has(input[type="checkbox"]) {
        padding:9px 0!important;border:0!important;min-width:0!important;
    }
    #logisticsContent #lgPickupRequests .pickup-table td::before {
        content:attr(data-label);display:block;margin-bottom:5px;color:#8a95a2;
        font-size:9px;letter-spacing:.05em;font-weight:700;text-transform:uppercase;
    }
    #logisticsContent #lgPickupRequests .pickup-table td:first-child {grid-column:1/-1;}
    #logisticsContent #lgPickupRequests .pickup-table td:nth-child(2),
    #logisticsContent #lgPickupRequests .pickup-table td:nth-child(3) {grid-column:1/-1;}
    #logisticsContent #lgPickupRequests .pickup-table td:nth-child(6) {grid-column:1/-1;}
}
@media(max-width:520px) {
    #logisticsContent #lgPickupRequests .pickup-admin-filters {grid-template-columns:1fr 1fr;gap:8px;}
    #logisticsContent #lgPickupRequests .pickup-admin-select {grid-column:1/-1;}
    #logisticsContent #lgPickupRequests .pickup-admin-search input,
    #logisticsContent #lgPickupRequests .pickup-admin-select select {height:43px;font-size:11px;}
    #logisticsContent #lgPickupRequests .pickup-admin-apply,
    #logisticsContent #lgPickupRequests .pickup-admin-reset {height:41px;}
    #logisticsContent #lgPickupRequests .pickup-admin-table-head {padding:15px;}
    #logisticsContent #lgPickupRequests .pickup-selection-bar {padding:10px 15px;}
    #logisticsContent #lgPickupRequests .pickup-selection-bar .pickup-button {margin-left:0;}
    #logisticsContent #lgPickupRequests .pickup-admin-footer {padding:12px 15px;}
    #logisticsContent #lgPickupRequests .pickup-page-number,
    #logisticsContent #lgPickupRequests .pickup-page-arrow {width:32px;height:32px;}
}
@media(prefers-reduced-motion:reduce) {
    #logisticsContent #lgPickupRequests .pickup-page-number,
    #logisticsContent #lgPickupRequests .pickup-page-arrow {transition:none;}
}

/* PICKUP REQUESTS — BUYER BUTTON + CLEAN TABLE REFINEMENT */
#logisticsContent #lgPickupRequests .pickup-buyer-link-button {
    display:inline-flex; align-items:center; justify-content:center; gap:10px;
    min-height:44px; padding:0 16px; border:1px solid #d8e0e8; border-radius:12px;
    background:#fff; color:#516172; text-decoration:none; font-size:13px; font-weight:700;
    line-height:1; box-shadow:0 6px 16px rgba(24,34,45,.035);
    transition:transform .15s ease, border-color .15s ease, box-shadow .15s ease, color .15s ease;
}
#logisticsContent #lgPickupRequests .pickup-buyer-link-button span {
    display:inline-flex; align-items:center; justify-content:center; font-size:16px; font-weight:700;
    color:#657487;
}
#logisticsContent #lgPickupRequests .pickup-buyer-link-button:hover {
    transform:translateY(-1px); border-color:#cfd7e0; box-shadow:0 10px 22px rgba(24,34,45,.06);
    color:#3f4f61;
}
#logisticsContent #lgPickupRequests .pickup-buyer-link-button:focus-visible {
    outline:none; border-color:#d89b10; box-shadow:0 0 0 3px rgba(216,155,16,.12);
}
#logisticsContent #lgPickupRequests .pickup-stats { gap:14px; }
#logisticsContent #lgPickupRequests .pickup-stat {
    min-height:108px; padding:16px 16px 15px; border-radius:18px;
}
#logisticsContent #lgPickupRequests .pickup-stat p {
    color:#667585; font-size:11px; font-weight:700;
}
#logisticsContent #lgPickupRequests .pickup-stat strong {
    margin:8px 0 5px; color:#1f2937; font-size:28px; font-weight:700; letter-spacing:-.04em;
}
#logisticsContent #lgPickupRequests .pickup-stat span:not(.pickup-stat-icon) {
    color:#8a97a6; font-size:10px; line-height:1.5;
}
#logisticsContent #lgPickupRequests .pickup-stat-icon {
    width:38px; height:38px; flex:0 0 38px; border-radius:12px; background:#f5f7fa; color:#7b8794;
}
#logisticsContent #lgPickupRequests .pickup-stat-icon svg { width:20px; height:20px; stroke-width:1.9; }
#logisticsContent #lgPickupRequests .pickup-stat-amber .pickup-stat-icon { background:#fff5db; color:#c98706; }
#logisticsContent #lgPickupRequests .pickup-stat-green .pickup-stat-icon { background:#edf8f1; color:#2c8a57; }
#logisticsContent #lgPickupRequests .pickup-stat-red .pickup-stat-icon { background:#fff0ef; color:#cf5e57; }
#logisticsContent #lgPickupRequests .pickup-stat-neutral .pickup-stat-icon { background:#eef2f6; color:#66788b; }
#logisticsContent #lgPickupRequests .pickup-stat-amber strong { color:#b97706; }
#logisticsContent #lgPickupRequests .pickup-stat-green strong { color:#216a43; }
#logisticsContent #lgPickupRequests .pickup-stat-red strong { color:#b54844; }
#logisticsContent #lgPickupRequests .pickup-admin-table-head {
    padding:18px 22px 15px; align-items:flex-end;
}
#logisticsContent #lgPickupRequests .pickup-admin-table-head h2 {
    font-size:18px; font-weight:800;
}
#logisticsContent #lgPickupRequests .pickup-admin-table-head p {
    font-size:10.5px;
}
#logisticsContent #lgPickupRequests .pickup-selection-bar {
    justify-content:space-between; flex-wrap:wrap; padding:11px 22px;
}
#logisticsContent #lgPickupRequests .pickup-selection-bar label,
#logisticsContent #lgPickupRequests .pickup-selection-bar > span {
    display:inline-flex; align-items:center; gap:8px; color:#677689; font-size:10.5px; font-weight:600;
}
#logisticsContent #lgPickupRequests .pickup-selection-bar .pickup-button {
    min-height:37px; padding:0 14px; border-radius:11px; font-size:10.5px; font-weight:700;
}
#logisticsContent #lgPickupRequests .pickup-table thead { background:#f6f8fa; }
#logisticsContent #lgPickupRequests .pickup-table th {
    padding:14px 12px; color:#748193; font-size:10px; font-weight:800; letter-spacing:.08em;
}
#logisticsContent #lgPickupRequests .pickup-table td {
    padding:16px 12px; color:#5f6c7a; font-size:11px;
}
#logisticsContent #lgPickupRequests .pickup-table td strong {
    color:#243140; font-size:11.5px; font-weight:700;
}
#logisticsContent #lgPickupRequests .pickup-table td p,
#logisticsContent #lgPickupRequests .pickup-table td .pickup-address,
#logisticsContent #lgPickupRequests .pickup-products li,
#logisticsContent #lgPickupRequests .pickup-detail-grid p {
    color:#8793a1; font-size:10.5px; line-height:1.62;
}
#logisticsContent #lgPickupRequests .pickup-detail-body {
    border-radius:12px; background:#fafbfc;
}
#logisticsContent #lgPickupRequests .pickup-text-link {
    color:#b37a0e; font-size:10.5px; font-weight:700; text-decoration:none;
}
#logisticsContent #lgPickupRequests .pickup-text-link:hover { color:#935f05; }
#logisticsContent #lgPickupRequests .pickup-badge {
    min-height:26px; padding:5px 10px; border-radius:999px; font-size:9px; font-weight:700;
}
#logisticsContent #lgPickupRequests .pickup-admin-footer {
    padding:14px 22px; font-size:10.5px;
}
#logisticsContent #lgPickupRequests .pickup-page-size-label {
    gap:8px; color:#6f7d8d; font-weight:600; font-size:10.5px;
}
#logisticsContent #lgPickupRequests .pickup-page-size-label select {
    height:36px; min-width:68px; border-radius:10px; font-size:10.5px;
}
#logisticsContent #lgPickupRequests .pickup-pagination-nav { padding-top:16px; gap:10px; }
#logisticsContent #lgPickupRequests .pickup-page-number,
#logisticsContent #lgPickupRequests .pickup-page-arrow {
    width:36px; height:36px; border-radius:11px; font-size:11px;
}
@media (max-width:767px) {
    #logisticsContent #lgPickupRequests .pickup-buyer-link-button { width:100%; }
    #logisticsContent #lgPickupRequests .pickup-stat { min-height:100px; }
    #logisticsContent #lgPickupRequests .pickup-stat strong { font-size:24px; }
    #logisticsContent #lgPickupRequests .pickup-selection-bar,
    #logisticsContent #lgPickupRequests .pickup-admin-footer,
    #logisticsContent #lgPickupRequests .pickup-admin-table-head { padding-left:16px; padding-right:16px; }
}
</style>
<style>
/* ===========================================================
   PICKUP REQUESTS — BUYER CART + DELIVERY HISTORY DESIGN PARITY
   UI-only overrides scoped to the existing logistics page.
   Preserve all verification, selection and routing behavior.
   =========================================================== */
#logisticsContent:has(#lgPickupRequests) {
    background: #f4f5f7;
}
#logisticsContent #lgPickupRequests {
    --pickup-gold: #c88912;
    --pickup-gold-dark: #b47b0e;
    --pickup-ink: #2f3742;
    --pickup-line: #e5e7eb;
    --pickup-shadow: 0 2px 5px rgba(15,23,42,.045),
        0 10px 24px rgba(15,23,42,.075),
        0 24px 54px rgba(15,23,42,.095),
        inset 0 1px 0 rgba(255,255,255,.92);
    display: grid;
    gap: 20px;
    width: 100%;
    max-width: 1520px;
    margin: 0 auto;
    padding-bottom: 28px;
    color: #2f3742;
    font: 400 13px/1.65 Poppins, ui-sans-serif, system-ui, sans-serif;
}

/* Buyer Cart: lightweight SARI kicker, prominent title, text-only link. */
#logisticsContent #lgPickupRequests .pickup-page-header {
    display: flex;
    flex-direction: row;
    align-items: flex-end;
    justify-content: space-between;
    gap: 14px 24px;
    min-height: 0;
    margin: 0;
    padding: 0 0 14px;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
    overflow: visible;
}
#logisticsContent #lgPickupRequests .pickup-page-header::before,
#logisticsContent #lgPickupRequests .pickup-page-header::after { display: none; }
#logisticsContent #lgPickupRequests .pickup-page-header .pickup-eyebrow {
    margin: 0 0 6px;
    color: #a87316;
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: .14em;
    line-height: 1.4;
}
#logisticsContent #lgPickupRequests .pickup-page-header h1 {
    margin: 0;
    color: #2e343b;
    font-size: clamp(27px,2.3vw,31px);
    font-weight: 760;
    letter-spacing: -.04em;
    line-height: 1.08;
}
#logisticsContent #lgPickupRequests .pickup-page-header h1 span { color: #c88912; }
#logisticsContent #lgPickupRequests .pickup-buyer-link-button {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    flex: none;
    width: auto;
    min-height: 28px;
    padding: 0;
    gap: 7px;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
    color: #59636e;
    font-size: 11.5px;
    font-weight: 750;
    line-height: 1.35;
    text-decoration: none;
    white-space: nowrap;
    transition: color .16s ease;
}
#logisticsContent #lgPickupRequests .pickup-buyer-link-button span {
    display: inline;
    color: inherit;
    font-size: 18px;
    line-height: 1;
    transition: transform .16s ease;
}
#logisticsContent #lgPickupRequests .pickup-buyer-link-button:hover {
    color: #c88912;
    background: transparent;
    border-color: transparent;
    box-shadow: none;
    transform: none;
}
#logisticsContent #lgPickupRequests .pickup-buyer-link-button:hover span { transform: translateX(3px); }
#logisticsContent #lgPickupRequests .pickup-buyer-link-button:focus-visible {
    outline: 2px solid #c88912;
    outline-offset: 4px;
    box-shadow: none;
}

/* Delivery History summary cards: typography and bare outline icons. */
#logisticsContent #lgPickupRequests .pickup-stats {
    grid-template-columns: repeat(4,minmax(0,1fr));
    gap: 14px;
}
#logisticsContent #lgPickupRequests .pickup-stat {
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    min-height: 110px;
    padding: 14px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    background: #fff;
    box-shadow: var(--pickup-shadow);
}
#logisticsContent #lgPickupRequests .pickup-stat p {
    color: #5f6d7c;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.4;
}
#logisticsContent #lgPickupRequests .pickup-stat strong,
#logisticsContent #lgPickupRequests .pickup-stat-amber strong,
#logisticsContent #lgPickupRequests .pickup-stat-green strong,
#logisticsContent #lgPickupRequests .pickup-stat-red strong {
    display: block;
    margin: 8px 0 5px;
    color: #1f2937;
    font-size: clamp(25px,1.9vw,30px);
    font-weight: 650;
    letter-spacing: -.045em;
    line-height: 1.12;
    font-variant-numeric: tabular-nums;
}
#logisticsContent #lgPickupRequests .pickup-stat span:not(.pickup-stat-icon) {
    color: #8995a3;
    font-size: 12.5px;
    font-weight: 400;
    line-height: 1.5;
}
#logisticsContent #lgPickupRequests .pickup-stat-icon,
#logisticsContent #lgPickupRequests .pickup-stat-amber .pickup-stat-icon,
#logisticsContent #lgPickupRequests .pickup-stat-green .pickup-stat-icon,
#logisticsContent #lgPickupRequests .pickup-stat-red .pickup-stat-icon,
#logisticsContent #lgPickupRequests .pickup-stat-neutral .pickup-stat-icon {
    display: inline-flex;
    flex: 0 0 27px;
    align-items: center;
    justify-content: center;
    width: 27px;
    height: 27px;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    color: #9aa5b1;
    box-shadow: none;
}
#logisticsContent #lgPickupRequests .pickup-stat-icon svg {
    width: 27px;
    height: 27px;
    stroke-width: 1.8;
}
#logisticsContent #lgPickupRequests .pickup-stat-amber .pickup-stat-icon { color: #c88912; }
#logisticsContent #lgPickupRequests .pickup-stat-green .pickup-stat-icon { color: #0f8a6b; }
#logisticsContent #lgPickupRequests .pickup-stat-red .pickup-stat-icon { color: #cc4b37; }

/* Delivery History: standalone search, filter, and action controls. */
#logisticsContent #lgPickupRequests .pickup-admin-filters {
    display: grid;
    grid-template-columns: minmax(0,1fr) minmax(190px,235px) auto auto;
    align-items: center;
    gap: 12px;
    min-width: 0;
    margin: 0;
}
#logisticsContent #lgPickupRequests .pickup-admin-search > svg {
    left: 16px;
    width: 21px;
    height: 21px;
    color: #97a3b2;
}
#logisticsContent #lgPickupRequests .pickup-admin-search input,
#logisticsContent #lgPickupRequests .pickup-admin-select select {
    width: 100%;
    height: 48px;
    border: 1px solid #dce2e8;
    border-radius: 14px;
    background: #fff;
    box-shadow: none;
    color: #3f4b59;
    font: 600 11.5px/1.4 Poppins, sans-serif;
    transition: background-color .16s ease, border-color .16s ease, box-shadow .16s ease;
}
#logisticsContent #lgPickupRequests .pickup-admin-search input { padding: 0 16px 0 46px; }
#logisticsContent #lgPickupRequests .pickup-admin-select select { padding: 0 40px 0 16px; }
#logisticsContent #lgPickupRequests .pickup-admin-search input::placeholder { color: #a0aab6; }
#logisticsContent #lgPickupRequests .pickup-admin-select > svg { right: 17px; color: #6b7280; }
#logisticsContent #lgPickupRequests .pickup-admin-search input:hover,
#logisticsContent #lgPickupRequests .pickup-admin-select select:hover { background: #f3f4f6; border-color: #cfd6de; }
#logisticsContent #lgPickupRequests .pickup-admin-search input:focus,
#logisticsContent #lgPickupRequests .pickup-admin-select select:focus {
    outline: 0;
    border-color: #c88912;
    background: #fff;
    box-shadow: 0 0 0 1px rgba(200,137,18,.12);
}
#logisticsContent #lgPickupRequests .pickup-admin-apply,
#logisticsContent #lgPickupRequests .pickup-admin-reset {
    width: auto;
    min-width: 94px;
    height: 48px;
    padding: 0 18px;
    border-radius: 14px;
    font: 700 12px/1.2 Poppins, sans-serif;
    box-shadow: none;
    white-space: nowrap;
}
#logisticsContent #lgPickupRequests .pickup-admin-apply { border-color: #c88912; background: #c88912; color: #fff; }
#logisticsContent #lgPickupRequests .pickup-admin-apply:hover { border-color: #b47b0e; background: #b47b0e; }
#logisticsContent #lgPickupRequests .pickup-admin-reset { border-color: #dce2e8; background: #fff; color: #5f6d7c; }
#logisticsContent #lgPickupRequests .pickup-admin-reset:hover { border-color: #cfd6de; background: #f3f4f6; }

/* Completed Trips: floating white table with readable rows. */
#logisticsContent #lgPickupRequests .pickup-admin-directory {
    overflow: hidden;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    background: #fff;
    box-shadow: var(--pickup-shadow);
}
#logisticsContent #lgPickupRequests .pickup-admin-table-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    padding: 21px 23px;
    border-bottom: 1px solid #e9edf1;
}
#logisticsContent #lgPickupRequests .pickup-admin-table-head h2 {
    margin: 0;
    color: #2f3742;
    font-size: 18px;
    font-weight: 780;
    letter-spacing: -.03em;
    line-height: 1.4;
}
#logisticsContent #lgPickupRequests .pickup-admin-table-head p {
    margin: 4px 0 0;
    color: #8b96a4;
    font-size: 11.5px;
    line-height: 1.5;
}
#logisticsContent #lgPickupRequests .pickup-admin-counter {
    min-height: 0;
    margin-left: auto;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: none;
    color: #7b8794;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}
#logisticsContent #lgPickupRequests .pickup-selection-bar {
    min-height: 49px;
    padding: 10px 23px;
    gap: 12px;
    background: #fff;
    border-bottom: 1px solid #e9edf1;
}
#logisticsContent #lgPickupRequests .pickup-selection-bar label,
#logisticsContent #lgPickupRequests .pickup-selection-bar > span {
    color: #667383;
    font-size: 11.5px;
    font-weight: 600;
}
#logisticsContent #lgPickupRequests .pickup-selection-bar .pickup-button {
    height: 38px;
    padding: 0 15px;
    border-radius: 11px;
    background: #c88912;
    border-color: #c88912;
    color: #fff;
    font-size: 11.5px;
    font-weight: 700;
}
#logisticsContent #lgPickupRequests .pickup-selection-bar .pickup-button:hover { background: #b47b0e; border-color: #b47b0e; }
#logisticsContent #lgPickupRequests .pickup-table-scroll {
    width: 100%;
    overflow-x: auto;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}
#logisticsContent #lgPickupRequests table.pickup-table {
    width: 100%;
    min-width: 970px;
    table-layout: fixed;
    border-collapse: collapse;
    text-align: left;
}
#logisticsContent #lgPickupRequests .pickup-table thead { background: #f8fafc; }
#logisticsContent #lgPickupRequests .pickup-table th {
    padding: 13px 16px;
    border-bottom: 1px solid #e9edf1;
    color: #8591a0;
    font-size: 11px;
    font-weight: 750;
    letter-spacing: .075em;
    line-height: 1.5;
    text-transform: uppercase;
    white-space: nowrap;
}
#logisticsContent #lgPickupRequests .pickup-table td {
    min-width: 0;
    padding: 17px 16px;
    vertical-align: top;
    border-bottom: 1px solid #edf0f3;
    color: #667383;
    font-size: 12.5px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}
#logisticsContent #lgPickupRequests .pickup-table tbody tr:hover { background: #f6f7f9; }
#logisticsContent #lgPickupRequests .pickup-table td strong { color: #293440; font-size: 12.5px; font-weight: 750; }
#logisticsContent #lgPickupRequests .pickup-table td p,
#logisticsContent #lgPickupRequests .pickup-table td .pickup-address,
#logisticsContent #lgPickupRequests .pickup-products li,
#logisticsContent #lgPickupRequests .pickup-detail-grid p {
    color: #8793a1;
    font-size: 11.5px;
    line-height: 1.6;
}
#logisticsContent #lgPickupRequests .pickup-table th:first-child,
#logisticsContent #lgPickupRequests .pickup-table td:first-child:has(input[type="checkbox"]) {
    width: 54px;
    padding-left: 23px;
    padding-right: 10px;
    text-align: center;
}
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(2),
#logisticsContent #lgPickupRequests .pickup-table td:nth-child(2) { padding-left: 12px; }
#logisticsContent #lgPickupRequests .pickup-table th:last-child,
#logisticsContent #lgPickupRequests .pickup-table td:last-child { padding-right: 23px; }
#logisticsContent #lgPickupRequests input[type="checkbox"] {
    appearance: none;
    display: inline-grid;
    place-items: center;
    vertical-align: middle;
    width: 17px;
    height: 17px;
    flex: 0 0 17px;
    margin: 0;
    border: 1.5px solid #c6cbd2;
    border-radius: 5px;
    background: #fff;
    cursor: pointer;
}
#logisticsContent #lgPickupRequests input[type="checkbox"]::after {
    content: '';
    display: block;
    width: 8px;
    height: 5px;
    margin-top: -2px;
    border-left: 2px solid #fff;
    border-bottom: 2px solid #fff;
    transform: rotate(-45deg) scale(0);
}
#logisticsContent #lgPickupRequests input[type="checkbox"]:checked,
#logisticsContent #lgPickupRequests input[type="checkbox"]:indeterminate { background: #c88912; border-color: #c88912; }
#logisticsContent #lgPickupRequests input[type="checkbox"]:checked::after { transform: rotate(-45deg) scale(1); }
#logisticsContent #lgPickupRequests input[type="checkbox"]:indeterminate::after {
    width: 8px;
    height: 0;
    margin-top: 0;
    border-left: 0;
    border-bottom: 2px solid #fff;
    transform: none;
}
#logisticsContent #lgPickupRequests input[type="checkbox"]:focus-visible { outline: 2px solid #c88912; outline-offset: 3px; }
#logisticsContent #lgPickupRequests .pickup-badge {
    display: inline-flex;
    align-items: center;
    min-height: 25px;
    padding: 5px 11px;
    gap: 7px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}
#logisticsContent #lgPickupRequests .pickup-badge::before {
    content: '';
    display: inline-block;
    flex: 0 0 6px;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}
#logisticsContent #lgPickupRequests .pickup-badge-pending { color: #97690e; border-color: #f0dea8; background: #fff9e9; }
#logisticsContent #lgPickupRequests .pickup-badge-verified { color: #18794e; border-color: #bde7c9; background: #effcf3; }
#logisticsContent #lgPickupRequests .pickup-badge-attention { color: #a84e46; border-color: #f0cfcb; background: #fff4f2; }
#logisticsContent #lgPickupRequests .pickup-details summary { color: #9a6c13; font-size: 11.5px; font-weight: 650; }
#logisticsContent #lgPickupRequests .pickup-detail-body { border-color: #e9edf1; border-radius: 10px; background: #fafbfc; }
#logisticsContent #lgPickupRequests .pickup-detail-grid strong { color: #293440; font-size: 11.5px; }
#logisticsContent #lgPickupRequests .pickup-label { color: #8591a0; font-size: 10px; }
#logisticsContent #lgPickupRequests .pickup-text-link { color: #b2770f; font-size: 11.5px; font-weight: 700; }

/* Buyer Cart: generous no-data treatment, no icon tile, real CTA. */
#logisticsContent #lgPickupRequests .pickup-empty {
    padding: 72px 20px;
    text-align: center;
    background: #fff;
}
#logisticsContent #lgPickupRequests .pickup-empty-icon {
    display: grid;
    place-items: center;
    width: 92px;
    height: 92px;
    margin: 0 auto;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    color: #b4bbc3;
    box-shadow: none;
}
#logisticsContent #lgPickupRequests .pickup-empty-icon svg {
    width: 58px;
    height: 58px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.45;
    stroke-linecap: round;
    stroke-linejoin: round;
}
#logisticsContent #lgPickupRequests .pickup-empty h3 {
    margin: 12px 0 0;
    color: #9ca4ad;
    font-size: 22px;
    font-weight: 750;
    line-height: 1.2;
    letter-spacing: -.025em;
}
#logisticsContent #lgPickupRequests .pickup-empty p {
    max-width: 520px;
    margin: 7px auto 0;
    color: #a2acb8;
    font-size: 12px;
    font-weight: 500;
    line-height: 1.55;
}
#logisticsContent #lgPickupRequests .pickup-empty-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 170px;
    min-height: 48px;
    margin-top: 22px;
    padding: 0 20px;
    border: 1px solid #c88912;
    border-radius: 11px;
    background: #c88912;
    color: #fff;
    font: 700 12px/1.3 Poppins, sans-serif;
    text-decoration: none;
    transition: background-color .16s ease, border-color .16s ease;
}
#logisticsContent #lgPickupRequests .pickup-empty-action:hover { border-color: #b47b0e; background: #b47b0e; color: #fff; }
#logisticsContent #lgPickupRequests .pickup-empty-action:focus-visible { outline: 2px solid #c88912; outline-offset: 3px; }

/* Completed Trips: pagination is inside the table card. */
#logisticsContent #lgPickupRequests .pickup-admin-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px 18px;
    min-height: 60px;
    padding: 13px 20px;
    border-top: 1px solid #edf0f4;
    background: #fff;
    color: #8a96a6;
    font-size: 11px;
    font-weight: 550;
}
#logisticsContent #lgPickupRequests .pickup-footer-info { display: flex; align-items: center; flex-wrap: wrap; gap: 12px 18px; }
#logisticsContent #lgPickupRequests .pickup-page-size-label { color: #8a96a6; gap: 8px; font-size: 11px; font-weight: 550; }
#logisticsContent #lgPickupRequests .pickup-page-size-label select {
    height: 34px;
    min-width: 64px;
    padding: 0 7px;
    border: 1px solid #dce2e8;
    border-radius: 9px;
    background: #fff;
    color: #526073;
    font-size: 11px;
}
#logisticsContent #lgPickupRequests .pickup-pagination-nav {
    display: flex;
    flex: none;
    align-items: center;
    justify-content: flex-end;
    gap: 5px;
    margin-left: auto;
    padding: 0;
    min-height: 34px;
}
#logisticsContent #lgPickupRequests .pickup-page-buttons { display: flex; align-items: center; justify-content: center; gap: 5px; }
#logisticsContent #lgPickupRequests .pickup-page-number,
#logisticsContent #lgPickupRequests .pickup-page-arrow {
    display: inline-grid;
    place-items: center;
    flex: none;
    min-width: 34px;
    width: 34px;
    height: 34px;
    padding: 0;
    border: 1px solid transparent;
    border-radius: 10px;
    background: #f7f8fa;
    color: #8795a6;
    font-size: 11.5px;
    font-weight: 700;
    line-height: 1;
}
#logisticsContent #lgPickupRequests .pickup-page-number.is-current {
    border-color: #c88912;
    background: #c88912;
    color: #fff;
    box-shadow: 0 3px 9px rgba(200,137,18,.17);
}
#logisticsContent #lgPickupRequests .pickup-page-arrow:disabled { opacity: .45; }
#logisticsContent #lgPickupRequests .pickup-page-number:not(.is-current):hover,
#logisticsContent #lgPickupRequests .pickup-page-arrow:not(:disabled):hover {
    border-color: #e6eaef;
    background: #edf0f3;
    color: #4b596a;
}

@media (max-width: 1180px) {
    #logisticsContent #lgPickupRequests .pickup-stats { grid-template-columns: repeat(2,minmax(0,1fr)); }
}
@media (max-width: 900px) {
    #logisticsContent #lgPickupRequests .pickup-admin-filters { grid-template-columns: minmax(0,1fr) 220px; }
    #logisticsContent #lgPickupRequests .pickup-admin-apply,
    #logisticsContent #lgPickupRequests .pickup-admin-reset { width: 100%; }
}
@media (max-width: 820px) {
    /* Keep the delivery-history horizontal-scroll table, not legacy stacked cards. */
    #logisticsContent #lgPickupRequests .pickup-table-scroll { overflow-x: auto; overflow-y: auto; }
    #logisticsContent #lgPickupRequests table.pickup-table { min-width: 970px; table-layout: fixed; }
    #logisticsContent #lgPickupRequests .pickup-table thead { display: table-header-group; }
    #logisticsContent #lgPickupRequests .pickup-table tbody { display: table-row-group; }
    #logisticsContent #lgPickupRequests .pickup-table tbody tr {
        display: table-row;
        grid-template-columns: none;
        gap: 0;
        padding: 0;
    }
    #logisticsContent #lgPickupRequests .pickup-table th { display: table-cell; }
    #logisticsContent #lgPickupRequests .pickup-table td,
    #logisticsContent #lgPickupRequests .pickup-table td:first-child:has(input[type="checkbox"]) {
        display: table-cell;
        width: auto;
        min-width: 0;
        padding: 17px 16px;
        border-bottom: 1px solid #edf0f3;
    }
    #logisticsContent #lgPickupRequests .pickup-table td:first-child:has(input[type="checkbox"]) {
        width: 54px;
        padding-left: 23px;
        padding-right: 10px;
    }
    #logisticsContent #lgPickupRequests .pickup-table td::before { display: none; content: none; }
}
@media (max-width: 767px) {
    #logisticsContent #lgPickupRequests { gap: 15px; }
    #logisticsContent #lgPickupRequests .pickup-page-header { flex-direction: row; align-items: flex-end; }
    #logisticsContent #lgPickupRequests .pickup-page-header h1 { font-size: 25px; }
    #logisticsContent #lgPickupRequests .pickup-page-header .pickup-eyebrow { font-size: 9.5px; }
    #logisticsContent #lgPickupRequests .pickup-buyer-link-button { width: auto; font-size: 10.5px; }
    #logisticsContent #lgPickupRequests .pickup-buyer-link-button span { font-size: 16px; }
    #logisticsContent #lgPickupRequests .pickup-admin-table-head { padding: 18px; }
    #logisticsContent #lgPickupRequests .pickup-selection-bar { padding: 10px 18px; }
    #logisticsContent #lgPickupRequests .pickup-stat { min-height: 106px; padding: 13px 14px; }
    #logisticsContent #lgPickupRequests .pickup-stat strong { font-size: 27px; }
}
@media (max-width: 540px) {
    #logisticsContent #lgPickupRequests .pickup-page-header { align-items: flex-start; flex-direction: column; gap: 10px; }
    #logisticsContent #lgPickupRequests .pickup-buyer-link-button { align-self: flex-start; width: auto; }
    #logisticsContent #lgPickupRequests .pickup-stats { grid-template-columns: 1fr; gap: 11px; }
    #logisticsContent #lgPickupRequests .pickup-stat p { font-size: 14px; }
    #logisticsContent #lgPickupRequests .pickup-stat strong { font-size: 25px; }
    #logisticsContent #lgPickupRequests .pickup-admin-filters { grid-template-columns: 1fr 1fr; gap: 9px; }
    #logisticsContent #lgPickupRequests .pickup-admin-search,
    #logisticsContent #lgPickupRequests .pickup-admin-select { grid-column: 1 / -1; }
    #logisticsContent #lgPickupRequests .pickup-admin-apply,
    #logisticsContent #lgPickupRequests .pickup-admin-reset { min-width: 0; }
    #logisticsContent #lgPickupRequests .pickup-admin-table-head { padding: 16px; }
    #logisticsContent #lgPickupRequests .pickup-admin-counter { margin-left: 0; }
    #logisticsContent #lgPickupRequests .pickup-selection-bar { padding: 10px 14px; }
    #logisticsContent #lgPickupRequests .pickup-empty { padding: 56px 16px; }
    #logisticsContent #lgPickupRequests .pickup-empty-icon { width: 64px; height: 64px; }
    #logisticsContent #lgPickupRequests .pickup-empty-icon svg { width: 40px; height: 40px; }
    #logisticsContent #lgPickupRequests .pickup-empty h3 { font-size: 20px; }
    #logisticsContent #lgPickupRequests .pickup-empty p { font-size: 11px; }
    #logisticsContent #lgPickupRequests .pickup-empty-action { min-width: 160px; min-height: 46px; }
    #logisticsContent #lgPickupRequests .pickup-admin-footer { justify-content: center; gap: 10px; padding: 12px 14px; }
    #logisticsContent #lgPickupRequests .pickup-footer-info { justify-content: center; width: 100%; }
    #logisticsContent #lgPickupRequests .pickup-pagination-nav { margin-left: 0; }
    #logisticsContent #lgPickupRequests .pickup-page-number,
    #logisticsContent #lgPickupRequests .pickup-page-arrow { min-width: 31px; width: 31px; height: 32px; border-radius: 9px; font-size: 11px; }
}
@media (prefers-reduced-motion: reduce) {
    #logisticsContent #lgPickupRequests *,
    #logisticsContent #lgPickupRequests *::before,
    #logisticsContent #lgPickupRequests *::after { transition: none !important; animation: none !important; }
}
</style>
<style>
/* ==============================================================
   PICKUP REQUESTS — NEUTRAL + SARI GOLD POLISH
   Flat white/gray palette. No cream tiles or cramped parcel dropdown.
   Scoped to the existing page; routes and form semantics preserved.
   ============================================================== */
#logisticsContent #lgPickupRequests {
    --pickup-shadow: 0 3px 10px rgba(15,23,42,.035), 0 12px 30px rgba(15,23,42,.055);
    color: #2f3742;
}
#logisticsContent #lgPickupRequests .pickup-stat {
    border-color: #e5e9ee;
    box-shadow: var(--pickup-shadow);
}
#logisticsContent #lgPickupRequests .pickup-stat:hover {
    transform: translateY(-1px);
    border-color: #dce2e9;
    box-shadow: 0 6px 18px rgba(15,23,42,.06);
}
/* Smaller label typography than the previous 15px treatment. */
#logisticsContent #lgPickupRequests .pickup-stat p {
    color: #637081;
    font-size: 13px;
    font-weight: 650;
    letter-spacing: -.01em;
    line-height: 1.4;
}
#logisticsContent #lgPickupRequests .pickup-stat strong { margin-top: 7px; }
#logisticsContent #lgPickupRequests .pickup-stat span:not(.pickup-stat-icon) { font-size: 11.5px; }
#logisticsContent #lgPickupRequests .pickup-stat-icon,
#logisticsContent #lgPickupRequests .pickup-stat-amber .pickup-stat-icon,
#logisticsContent #lgPickupRequests .pickup-stat-green .pickup-stat-icon,
#logisticsContent #lgPickupRequests .pickup-stat-red .pickup-stat-icon,
#logisticsContent #lgPickupRequests .pickup-stat-neutral .pickup-stat-icon {
    padding: 0; border: none; border-radius: 0; background: transparent; box-shadow: none;
}
/* A proper, accessible navigation action rather than a loose link. */
#logisticsContent #lgPickupRequests .pickup-buyer-link-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    width: auto;
    gap: 9px;
    padding: 0 15px 0 17px;
    border: 1px solid #dce2e8;
    border-radius: 12px;
    background: #fff;
    color: #475569;
    box-shadow: 0 2px 6px rgba(15,23,42,.025);
    font-size: 12px;
    font-weight: 700;
    line-height: 1.2;
    text-decoration: none;
    white-space: nowrap;
    transition: color .16s ease, border-color .16s ease, background-color .16s ease;
}
#logisticsContent #lgPickupRequests .pickup-buyer-link-button svg {
    display: block; flex: none; width: 17px; height: 17px;
    color: #c88912; transition: transform .16s ease;
}
#logisticsContent #lgPickupRequests .pickup-buyer-link-button:hover {
    transform: none; border-color: #c88912; background: #f8fafc;
    color: #a56d0a; box-shadow: 0 2px 6px rgba(15,23,42,.025);
}
#logisticsContent #lgPickupRequests .pickup-buyer-link-button:hover svg { transform: translateX(3px); }
#logisticsContent #lgPickupRequests .pickup-buyer-link-button:focus-visible {
    outline: 2px solid #c88912; outline-offset: 3px;
}
/* Keep all requested inputs white/gray, even when hovered or focused. */
#logisticsContent #lgPickupRequests .pickup-admin-search input,
#logisticsContent #lgPickupRequests .pickup-admin-select select,
#logisticsContent #lgPickupRequests .pickup-page-size-label select { background-color: #fff; }
#logisticsContent #lgPickupRequests .pickup-admin-search input:hover,
#logisticsContent #lgPickupRequests .pickup-admin-select select:hover { background-color: #f8fafc; }
#logisticsContent #lgPickupRequests .pickup-admin-search input:focus,
#logisticsContent #lgPickupRequests .pickup-admin-select select:focus { background-color: #fff; }
#logisticsContent #lgPickupRequests .pickup-admin-directory {
    border-color: #e5e9ee; box-shadow: var(--pickup-shadow);
}
#logisticsContent #lgPickupRequests .pickup-table tbody tr[data-pickup-item]:hover {
    background: #f8fafc;
}
#logisticsContent #lgPickupRequests .pickup-table tbody tr[data-pickup-item].is-expanded,
#logisticsContent #lgPickupRequests .pickup-table tbody tr[data-pickup-item].is-expanded:hover {
    background: #f6f8fb;
}
#logisticsContent #lgPickupRequests .pickup-table td { vertical-align: middle; }
#logisticsContent #lgPickupRequests .pickup-table td p { margin-top: 5px; }
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(1) { width: 5%; }
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(2) { width: 22%; }
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(3) { width: 23%; }
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(4) { width: 20%; }
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(5) { width: 14%; }
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(6) { width: 16%; }
/* More polished action controls, including single verification and route links. */
#logisticsContent #lgPickupRequests .pickup-row-actions { display: flex; flex-direction: column; align-items: flex-start; gap: 10px; }
#logisticsContent #lgPickupRequests .pickup-row-actions form { margin: 0; }
#logisticsContent #lgPickupRequests .pickup-action,
#logisticsContent #lgPickupRequests .pickup-details-trigger,
#logisticsContent #lgPickupRequests .pickup-details-close {
    display: inline-flex; align-items: center; justify-content: center; gap: 7px;
    min-height: 34px; padding: 0 12px; border: 1px solid transparent;
    border-radius: 9px; font: 700 11px/1.25 Poppins, sans-serif;
    text-decoration: none; white-space: nowrap; cursor: pointer;
    transition: background-color .16s ease, border-color .16s ease, color .16s ease;
}
#logisticsContent #lgPickupRequests .pickup-action svg,
#logisticsContent #lgPickupRequests .pickup-details-trigger svg,
#logisticsContent #lgPickupRequests .pickup-details-close svg { width: 14px; height: 14px; flex: none; }
#logisticsContent #lgPickupRequests .pickup-action--primary {
    border-color: #c88912; background: #c88912; color: #fff;
}
#logisticsContent #lgPickupRequests .pickup-action--primary:hover {
    border-color: #b47b0e; background: #b47b0e; color: #fff;
}
#logisticsContent #lgPickupRequests .pickup-action--outline {
    border-color: #dce2e8; background: #fff; color: #536173;
}
#logisticsContent #lgPickupRequests .pickup-action--outline:hover {
    border-color: #c88912; background: #f8fafc; color: #a56d0a;
}
#logisticsContent #lgPickupRequests .pickup-action:focus-visible,
#logisticsContent #lgPickupRequests .pickup-details-trigger:focus-visible,
#logisticsContent #lgPickupRequests .pickup-details-close:focus-visible { outline: 2px solid #c88912; outline-offset: 3px; }
#logisticsContent #lgPickupRequests .pickup-details-trigger {
    justify-content: flex-start; min-height: 30px; padding: 2px 0;
    border: 0; border-radius: 0; background: transparent; color: #a56d0a;
    font-size: 11px; font-weight: 700; white-space: normal; text-align: left;
}
#logisticsContent #lgPickupRequests .pickup-details-trigger:hover {
    background: transparent; color: #865a0d; text-decoration: underline;
    text-underline-offset: 3px;
}
#logisticsContent #lgPickupRequests .pickup-details-trigger svg { transition: transform .15s ease; }
#logisticsContent #lgPickupRequests .is-expanded .pickup-details-trigger svg { transform: rotate(180deg); }
#logisticsContent #lgPickupRequests .pickup-badge-pending {
    color: #986b13; border-color: #e7d5a6; background: #f8f9fb;
}
#logisticsContent #lgPickupRequests .pickup-badge-verified {
    color: #18794e; border-color: #bde7c9; background: #effcf3;
}
#logisticsContent #lgPickupRequests .pickup-badge-attention {
    color: #a84e46; border-color: #f0cfcb; background: #fff4f2;
}
#logisticsContent #lgPickupRequests .pickup-issues {
    margin: 0; padding-left: 17px; color: #9f4c45; font-size: 11px; line-height: 1.5;
}
#logisticsContent #lgPickupRequests .pickup-subtext { color: #718096; font-size: 11px; line-height: 1.5; }
/* Detail row expands to full table width instead of squeezing payment/address into a single cell. */
#logisticsContent #lgPickupRequests tr.pickup-expanded-row { background: #f8fafc; }
#logisticsContent #lgPickupRequests tr.pickup-expanded-row:hover { background: #f8fafc; }
#logisticsContent #lgPickupRequests tr.pickup-expanded-row > td {
    padding: 0 22px 20px; border-bottom: 1px solid #e5e9ee; vertical-align: top;
    overflow-wrap: normal;
}
#logisticsContent #lgPickupRequests .pickup-expanded-panel {
    padding: 20px 22px; border: 1px solid #e3e8ef; border-radius: 13px;
    background: #fff; box-shadow: 0 2px 8px rgba(15,23,42,.025);
}
#logisticsContent #lgPickupRequests .pickup-expanded-head {
    display: flex; justify-content: space-between; align-items: flex-start; gap: 15px;
    padding-bottom: 16px; margin-bottom: 16px; border-bottom: 1px solid #edf0f4;
}
#logisticsContent #lgPickupRequests .pickup-expanded-kicker,
#logisticsContent #lgPickupRequests .pickup-info-label {
    display: block; margin-bottom: 8px; color: #8591a0; font-size: 10px;
    line-height: 1.35; font-weight: 750; text-transform: uppercase; letter-spacing: .075em;
}
#logisticsContent #lgPickupRequests .pickup-expanded-head h3 {
    margin: 0; color: #293440; font-size: 15px; font-weight: 750; letter-spacing: -.02em;
}
#logisticsContent #lgPickupRequests .pickup-details-close {
    border: 1px solid #e0e6ec; background: #fff; color: #637081; font-size: 10.5px;
}
#logisticsContent #lgPickupRequests .pickup-details-close:hover { background: #f5f7fa; color: #334155; }
#logisticsContent #lgPickupRequests .pickup-expanded-grid {
    display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 16px;
}
#logisticsContent #lgPickupRequests .pickup-info-block { min-width: 0; }
#logisticsContent #lgPickupRequests .pickup-info-block strong {
    display: block; color: #293440; font-size: 12px; font-weight: 700;
    line-height: 1.5; overflow-wrap: anywhere;
}
#logisticsContent #lgPickupRequests .pickup-info-block p {
    margin: 6px 0 0; color: #7b8794; font-size: 11px; line-height: 1.65;
    overflow-wrap: anywhere;
}
#logisticsContent #lgPickupRequests .pickup-info-price { color: #293440; font-weight: 750; white-space: nowrap; }
#logisticsContent #lgPickupRequests .pickup-expanded-products {
    margin-top: 18px; padding-top: 17px; border-top: 1px solid #edf0f4;
}
#logisticsContent #lgPickupRequests .pickup-expanded-products ul { list-style: none; margin: 0; padding: 0; }
#logisticsContent #lgPickupRequests .pickup-expanded-products li {
    display: flex; align-items: flex-start; justify-content: space-between; gap: 18px;
    padding: 9px 0; color: #637081; font-size: 11.5px; line-height: 1.55;
    border-bottom: 1px solid #f0f2f5;
}
#logisticsContent #lgPickupRequests .pickup-expanded-products li:last-child { border-bottom: 0; }
#logisticsContent #lgPickupRequests .pickup-expanded-products li strong { color: #293440; font-weight: 750; white-space: nowrap; }
#logisticsContent #lgPickupRequests .pickup-variant { color: #8591a0; }
#logisticsContent #lgPickupRequests .pickup-expanded-actions { display: flex; margin-top: 16px; }
@media (max-width: 820px) {
    /* Keep the history-style scrollable table, but fit opened information in the viewport. */
    #logisticsContent #lgPickupRequests .pickup-expanded-panel {
        position: sticky;
        left: 0;
        width: min(570px, calc(100vw - 64px));
        max-width: none;
    }
    #logisticsContent #lgPickupRequests .pickup-expanded-grid { grid-template-columns: 1fr; }
    #logisticsContent #lgPickupRequests .pickup-expanded-products li { flex-wrap: wrap; }
}
@media (max-width: 767px) {
    #logisticsContent #lgPickupRequests .pickup-buyer-link-button { width: auto; }
    #logisticsContent #lgPickupRequests .pickup-stat p { font-size: 12.5px; }
    #logisticsContent #lgPickupRequests .pickup-stat span:not(.pickup-stat-icon) { font-size: 11px; }
    #logisticsContent #lgPickupRequests .pickup-expanded-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
}
@media (max-width: 540px) {
    #logisticsContent #lgPickupRequests .pickup-page-header { gap: 13px; }
    #logisticsContent #lgPickupRequests .pickup-buyer-link-button { min-height: 42px; }
    #logisticsContent #lgPickupRequests .pickup-stat p { font-size: 12px; }
    #logisticsContent #lgPickupRequests .pickup-expanded-panel { padding: 15px; }
    #logisticsContent #lgPickupRequests .pickup-expanded-grid { grid-template-columns: 1fr; }
    #logisticsContent #lgPickupRequests .pickup-expanded-head { align-items: center; }
}
@media (prefers-reduced-motion: reduce) {
    #logisticsContent #lgPickupRequests .pickup-buyer-link-button svg,
    #logisticsContent #lgPickupRequests .pickup-details-trigger svg { transition: none !important; }
}

/* ======================================================================
   FINAL PICKUP WORKSPACE POLISH — Cart navigation + quiet row actions
   ====================================================================== */
/* Cart's Continue Shopping is text-only: no surrounding button, border or tile. */
#logisticsContent #lgPickupRequests .pickup-page-header .pickup-buyer-link-button {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    width: auto;
    min-width: 0;
    min-height: 24px;
    padding: 0;
    gap: 6px;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
    color: #59636e;
    font-size: 11.5px;
    font-weight: 750;
    line-height: 1.4;
    text-decoration: none;
    white-space: nowrap;
    transform: none;
    transition: color .16s ease;
}
#logisticsContent #lgPickupRequests .pickup-page-header .pickup-buyer-link-button svg {
    width: 17px;
    height: 17px;
    color: inherit;
    stroke-width: 1.9;
    transition: transform .16s ease;
}
#logisticsContent #lgPickupRequests .pickup-page-header .pickup-buyer-link-button:hover {
    padding: 0;
    border: 0;
    background: transparent;
    box-shadow: none;
    color: #c88912;
    transform: none;
}
#logisticsContent #lgPickupRequests .pickup-page-header .pickup-buyer-link-button:hover svg { transform: translateX(3px); }
#logisticsContent #lgPickupRequests .pickup-page-header .pickup-buyer-link-button:focus-visible {
    outline: 2px solid #c88912;
    outline-offset: 4px;
    box-shadow: none;
}

/* Stable, lightweight selection: the empty bar never occupies table space. */
#logisticsContent #lgPickupRequests .pickup-selection-bar[hidden] { display: none !important; }
#logisticsContent #lgPickupRequests .pickup-selection-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px 16px;
    min-height: 58px;
    padding: 10px 22px;
    border-bottom: 1px solid #e9edf1;
    background: #f8fafc;
}
#logisticsContent #lgPickupRequests .pickup-selection-summary,
#logisticsContent #lgPickupRequests .pickup-selection-actions {
    display: inline-flex;
    align-items: center;
    gap: 10px;
}
#logisticsContent #lgPickupRequests .pickup-selection-summary {
    color: #334155;
    font-size: 12px;
    font-weight: 700;
}
#logisticsContent #lgPickupRequests .pickup-selection-summary svg {
    width: 18px;
    height: 18px;
    padding: 3px;
    color: #c88912;
    border: 1px solid #e0e6ec;
    border-radius: 50%;
    background: #fff;
}
#logisticsContent #lgPickupRequests .pickup-clear-selection {
    padding: 8px 6px;
    border: 0;
    background: transparent;
    color: #64748b;
    font-size: 11px;
    font-weight: 650;
    cursor: pointer;
}
#logisticsContent #lgPickupRequests .pickup-clear-selection:hover { color: #a56d0a; }
#logisticsContent #lgPickupRequests .pickup-selection-bar .pickup-button {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    width: auto;
    min-height: 36px;
    padding: 0 13px;
    border: 1px solid #c88912;
    border-radius: 9px;
    background: #c88912;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
}
#logisticsContent #lgPickupRequests .pickup-selection-bar .pickup-button svg { width: 14px; height: 14px; }
#logisticsContent #lgPickupRequests .pickup-table thead .pickup-select-column {
    padding-left: 23px;
    padding-right: 10px;
    text-align: center;
    vertical-align: middle;
}
#logisticsContent #lgPickupRequests .pickup-table thead input[type="checkbox"] { vertical-align: middle; }

/* Status first, single subtle text action below; no stacked full-size buttons. */
#logisticsContent #lgPickupRequests .pickup-row-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 7px;
}
#logisticsContent #lgPickupRequests .pickup-row-actions form { margin: 0; max-width: 100%; }
#logisticsContent #lgPickupRequests .pickup-row-actions .pickup-badge {
    min-height: 23px;
    gap: 6px;
    padding: 4px 9px;
    background: #f8fafc;
    font-size: 10px;
    font-weight: 700;
    line-height: 1.35;
}
#logisticsContent #lgPickupRequests .pickup-row-actions .pickup-badge-pending {
    color: #a56d0a;
    border-color: #e9dfcd;
    background: #f8fafc;
}
#logisticsContent #lgPickupRequests .pickup-row-actions .pickup-badge-verified {
    color: #18794e;
    border-color: #bde7c9;
    background: #effcf3;
}
#logisticsContent #lgPickupRequests .pickup-row-actions .pickup-badge-attention {
    color: #a84e46;
    border-color: #f0cfcb;
    background: #fff4f2;
}
#logisticsContent #lgPickupRequests .pickup-row-actions .pickup-action,
#logisticsContent #lgPickupRequests .pickup-row-actions .pickup-action--primary,
#logisticsContent #lgPickupRequests .pickup-row-actions .pickup-action--outline {
    display: inline-flex;
    justify-content: flex-start;
    align-items: center;
    gap: 5px;
    min-height: 26px;
    width: auto;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
    color: #a56d0a;
    font: 700 11px/1.4 Poppins, sans-serif;
    text-decoration: none;
    text-align: left;
    white-space: normal;
    transform: none;
}
#logisticsContent #lgPickupRequests .pickup-row-actions .pickup-action svg {
    width: 14px;
    height: 14px;
    flex: none;
    color: inherit;
    transition: transform .16s ease;
}
#logisticsContent #lgPickupRequests .pickup-row-actions .pickup-action:hover {
    border: 0;
    background: transparent;
    color: #865a0d;
    text-decoration: underline;
    text-underline-offset: 3px;
}
#logisticsContent #lgPickupRequests .pickup-row-actions .pickup-action:hover svg { transform: translateX(2px); }
#logisticsContent #lgPickupRequests .pickup-action-issues { min-width: 0; max-width: 100%; }
#logisticsContent #lgPickupRequests .pickup-action-issues summary {
    width: fit-content;
    max-width: 100%;
    color: #687789;
    font-size: 10.5px;
    font-weight: 600;
    line-height: 1.5;
    cursor: pointer;
    white-space: normal;
}
#logisticsContent #lgPickupRequests .pickup-action-issues[open] summary { margin-bottom: 7px; }
#logisticsContent #lgPickupRequests .pickup-action-issues .pickup-issues {
    max-width: 220px;
    margin: 0;
    padding-left: 15px;
    color: #8b5760;
    font-size: 10px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}
#logisticsContent #lgPickupRequests .pickup-row-actions .pickup-subtext { font-size: 10px; line-height: 1.5; }

@media (max-width: 767px) {
    #logisticsContent #lgPickupRequests .pickup-page-header .pickup-buyer-link-button { font-size: 10.5px; }
    #logisticsContent #lgPickupRequests .pickup-selection-bar { padding: 10px 16px; }
}
@media (max-width: 540px) {
    #logisticsContent #lgPickupRequests .pickup-page-header .pickup-buyer-link-button { min-height: 22px; }
    #logisticsContent #lgPickupRequests .pickup-selection-actions { width: 100%; justify-content: space-between; }
    #logisticsContent #lgPickupRequests .pickup-selection-bar { padding: 11px 14px; }
}
</style>
<style>
/* PICKUP REQUESTS — 3-DOT ACTIONS (white, cool gray, SARI gold). */
#logisticsContent #lgPickupRequests .pickup-row-actions {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    flex-wrap: nowrap;
    gap: 8px;
    min-width: 0;
}
#logisticsContent #lgPickupRequests .pickup-row-actions .pickup-badge {
    max-width: calc(100% - 40px);
    white-space: normal;
    font-size: 10px;
    line-height: 1.3;
}
#logisticsContent #lgPickupRequests .pickup-more-trigger {
    display: inline-grid;
    place-items: center;
    flex: 0 0 32px;
    width: 32px;
    height: 32px;
    padding: 0;
    margin: 0;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
    background: #fff;
    color: #64748b;
    box-shadow: none;
    cursor: pointer;
    transition: border-color .15s ease, background-color .15s ease, color .15s ease;
}
#logisticsContent #lgPickupRequests .pickup-more-trigger svg { width: 18px; height: 18px; }
#logisticsContent #lgPickupRequests .pickup-more-trigger:hover,
#logisticsContent #lgPickupRequests .pickup-more-trigger[aria-expanded="true"] {
    border-color: #e5d2aa;
    background: #f8fafc;
    color: #c88912;
}
#logisticsContent #lgPickupRequests .pickup-more-trigger:focus-visible {
    outline: 2px solid #c88912;
    outline-offset: 2px;
}
/* Native popover renders in the browser top layer, so horizontal table scrolling cannot clip it. */
#logisticsContent #lgPickupRequests .pickup-more-menu {
    display: none;
    position: fixed;
    inset: auto;
    box-sizing: border-box;
    width: 205px;
    max-width: calc(100vw - 24px);
    margin: 0;
    padding: 6px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #fff;
    color: #334155;
    box-shadow: 0 14px 34px rgba(15,23,42,.13), 0 2px 7px rgba(15,23,42,.045);
    z-index: 1000;
}
#logisticsContent #lgPickupRequests .pickup-more-menu:popover-open,
#logisticsContent #lgPickupRequests .pickup-more-menu.is-open { display: block; }
#logisticsContent #lgPickupRequests .pickup-more-menu form { margin: 0; }
#logisticsContent #lgPickupRequests .pickup-more-menu .pickup-menu-item {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 10px;
    width: 100%;
    min-height: 39px;
    padding: 9px 11px;
    margin: 0;
    border: 0;
    border-radius: 8px;
    background: transparent;
    color: #475569;
    font: 600 11.5px/1.45 Poppins, ui-sans-serif, sans-serif;
    text-decoration: none;
    text-align: left;
    white-space: normal;
    cursor: pointer;
    transition: background-color .15s ease, color .15s ease;
}
#logisticsContent #lgPickupRequests .pickup-more-menu .pickup-menu-item svg {
    flex: 0 0 17px;
    width: 17px;
    height: 17px;
    color: #94a3b8;
}
#logisticsContent #lgPickupRequests .pickup-more-menu .pickup-menu-item--emphasis {
    color: #a56d0a;
    font-weight: 700;
}
#logisticsContent #lgPickupRequests .pickup-more-menu .pickup-menu-item--emphasis svg { color: #c88912; }
#logisticsContent #lgPickupRequests .pickup-more-menu .pickup-menu-item:hover,
#logisticsContent #lgPickupRequests .pickup-more-menu .pickup-menu-item:focus-visible {
    background: #f1f5f9;
    color: #a56d0a;
    outline: 0;
}
#logisticsContent #lgPickupRequests .pickup-more-menu .pickup-menu-item:focus-visible {
    box-shadow: inset 0 0 0 2px rgba(200,137,18,.38);
}
#logisticsContent #lgPickupRequests .pickup-expanded-issues {
    margin-top: 18px;
    padding: 15px;
    border: 1px solid #f0cfcb;
    border-radius: 10px;
    background: #fff;
}
#logisticsContent #lgPickupRequests .pickup-expanded-issues ul {
    margin: 0;
    padding-left: 19px;
    color: #9f4c45;
    font-size: 11px;
    line-height: 1.65;
    overflow-wrap: anywhere;
}
#logisticsContent #lgPickupRequests .pickup-expanded-issues p {
    margin: 8px 0 0;
    font-size: 11px;
    color: #64748b;
}
@media (prefers-reduced-motion: reduce) {
    #logisticsContent #lgPickupRequests .pickup-more-trigger,
    #logisticsContent #lgPickupRequests .pickup-more-menu .pickup-menu-item { transition: none !important; }
}
</style>

<style>
/* PICKUP REQUESTS — spacing and custom status dropdown cleanup */
#logisticsContent #lgPickupRequests .pickup-admin-filters {
    grid-template-columns: minmax(0,1fr) minmax(185px,210px) auto auto;
}
#logisticsContent #lgPickupRequests .pickup-admin-select {
    position: relative;
    min-width: 0;
}
#logisticsContent #lgPickupRequests .pickup-admin-select-trigger {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    height: 48px;
    padding: 0 16px;
    border: 1px solid #dce2e8;
    border-radius: 14px;
    background: #fff;
    color: #3f4b59;
    font: 600 11.5px/1.4 Poppins, sans-serif;
    cursor: pointer;
    transition: border-color .16s ease, box-shadow .16s ease, background-color .16s ease;
}
#logisticsContent #lgPickupRequests .pickup-admin-select-trigger:hover {
    background: #f8fafc;
    border-color: #cfd6de;
}
#logisticsContent #lgPickupRequests .pickup-admin-select-trigger:focus-visible,
#logisticsContent #lgPickupRequests .pickup-filter-dropdown.is-open .pickup-admin-select-trigger,
#logisticsContent #lgPickupRequests .pickup-admin-select-trigger[aria-expanded="true"] {
    outline: 0;
    border-color: #c88912;
    box-shadow: 0 0 0 1px rgba(200,137,18,.12);
}
#logisticsContent #lgPickupRequests .pickup-admin-select-trigger svg {
    flex: 0 0 17px;
    width: 17px;
    height: 17px;
    color: #6b7280;
}
#logisticsContent #lgPickupRequests .pickup-admin-select-panel {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    z-index: 40;
    padding: 8px;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 16px 32px rgba(15,23,42,.12), 0 2px 6px rgba(15,23,42,.05);
}
#logisticsContent #lgPickupRequests .pickup-admin-option {
    display: flex;
    align-items: center;
    width: 100%;
    min-height: 38px;
    padding: 0 12px;
    border: 0;
    border-radius: 10px;
    background: transparent;
    color: #475569;
    font: 600 11.5px/1.4 Poppins, sans-serif;
    text-align: left;
    cursor: pointer;
    transition: background-color .15s ease, color .15s ease;
}
#logisticsContent #lgPickupRequests .pickup-admin-option:hover,
#logisticsContent #lgPickupRequests .pickup-admin-option:focus-visible {
    background: #f8fafc;
    color: #a56d0a;
    outline: 0;
}
#logisticsContent #lgPickupRequests .pickup-admin-option.is-active {
    background: #f8fafc;
    color: #2f3742;
    font-weight: 700;
}
#logisticsContent #lgPickupRequests .pickup-table {
    min-width: 940px;
}
#logisticsContent #lgPickupRequests .pickup-table th,
#logisticsContent #lgPickupRequests .pickup-table td {
    padding-left: 14px;
    padding-right: 14px;
}
#logisticsContent #lgPickupRequests .pickup-table th:first-child,
#logisticsContent #lgPickupRequests .pickup-table td:first-child:has(input[type="checkbox"]) {
    padding-left: 20px;
    padding-right: 8px;
}
#logisticsContent #lgPickupRequests .pickup-table th:last-child,
#logisticsContent #lgPickupRequests .pickup-table td:last-child {
    padding-right: 20px;
}
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(2) { width: 24%; }
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(3) { width: 22%; }
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(4) { width: 17%; }
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(5) { width: 14%; }
#logisticsContent #lgPickupRequests .pickup-table th:nth-child(6) { width: 18%; }
#logisticsContent #lgPickupRequests .pickup-row-actions .pickup-badge {
    max-width: none;
    white-space: nowrap;
    font-size: 10.5px;
}
#logisticsContent #lgPickupRequests .pickup-admin-footer {
    justify-content: space-between;
}
#logisticsContent #lgPickupRequests .pickup-footer-info {
    gap: 0;
}
@media (max-width: 900px) {
    #logisticsContent #lgPickupRequests .pickup-admin-filters {
        grid-template-columns: 1fr 1fr;
    }
    #logisticsContent #lgPickupRequests .pickup-admin-search,
    #logisticsContent #lgPickupRequests .pickup-admin-select {
        grid-column: 1 / -1;
    }
}
</style>


<style>
/* Exact Buyer Cart "Continue Shopping" link styling for Pickup Assignment only. */
#logisticsContent #lgPickupRequests .pickup-page-header a.pickup-buyer-link-button {
    position: relative !important;
    top: -12px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
    gap: 8px !important;
    width: auto !important;
    min-width: 0 !important;
    min-height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    color: #59636e !important;
    font-family: "Poppins", ui-sans-serif, sans-serif !important;
    font-size: 11.5px !important;
    font-weight: 750 !important;
    line-height: 1.35 !important;
    text-decoration: none !important;
    white-space: nowrap;
    transform: none !important;
    transition: color .14s ease !important;
}

#logisticsContent #lgPickupRequests .pickup-page-header a.pickup-buyer-link-button .pickup-assignment-arrow {
    display: inline-block !important;
    margin-left: 0 !important;
    color: currentColor !important;
    font-size: 18px !important;
    font-weight: 800 !important;
    line-height: .8 !important;
    transform: translateY(-1px) !important;
    transition: none !important;
}

#logisticsContent #lgPickupRequests .pickup-page-header a.pickup-buyer-link-button:hover,
#logisticsContent #lgPickupRequests .pickup-page-header a.pickup-buyer-link-button:focus-visible {
    color: #c88912 !important;
    border: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    text-decoration: none !important;
    transform: none !important;
}

/* Retain a visible keyboard focus indication without introducing a button box. */
#logisticsContent #lgPickupRequests .pickup-page-header a.pickup-buyer-link-button:focus-visible {
    outline: 2px solid #c88912 !important;
    outline-offset: 4px !important;
}

@media (max-width: 640px) {
    #logisticsContent #lgPickupRequests .pickup-page-header a.pickup-buyer-link-button {
        top: -8px !important;
        font-size: 10.5px !important;
    }

    #logisticsContent #lgPickupRequests .pickup-page-header a.pickup-buyer-link-button .pickup-assignment-arrow {
        font-size: 16px !important;
    }
}
</style>

@endpush

@section('content')

<div
    id="lgPickupRequests"
    class="sari-logistics-ui pickup-page"
    data-pickup-workspace="requests"
    data-old-ids="{{ json_encode(array_map('intval', (array) old('order_ids', []))) }}"
>

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}

    <header class="pickup-page-header">

        <div class="min-w-0">

            <p class="pickup-eyebrow">
                SARI MARKETPLACE
            </p>

            <h1>
                Pickup <span>Requests</span>
            </h1>

        </div>

        <a
            class="pickup-buyer-link-button"
            href="{{ route('logistics.delivery-assignment') }}"
        >
            Pickup Assignment <span class="pickup-assignment-arrow" aria-hidden="true">&gt;</span>
        </a>

    </header>

    {{-- =====================================================
        FLASH MESSAGES
    ====================================================== --}}

    @if (session('success'))

        <div
            class="pickup-alert pickup-alert-success"
            role="status"
        >
            <strong>
                Queue updated
            </strong>

            <p>
                {{ session('success') }}
            </p>
        </div>

    @endif

    @if ($errors->any())

        <div
            class="pickup-alert pickup-alert-danger"
            role="alert"
        >

            <strong>
                Could not verify requests
            </strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif

    {{-- =====================================================
        PAGE DATA
    ====================================================== --}}

    @php
        $summaryCards = [
            [
                'All requests',
                $stats['total'],
                'Unassigned Seller parcels',
                'neutral',
            ],
            [
                'Awaiting verification',
                $stats['pending'],
                'Review before dispatch',
                'amber',
            ],
            [
                'Ready to assign',
                $stats['verified'],
                $stats['batches'].' Seller pickup batches',
                'green',
            ],
            [
                'Needs attention',
                $stats['attention'],
                'Pickup details or route issues',
                'red',
            ],
        ];

        $filters = [
            'all' => ['All', $stats['total']],
            'pending' => ['Awaiting verification', $stats['pending']],
            'verified' => ['Verified', $stats['verified']],
            'attention' => ['Needs attention', $stats['attention']],
        ];

        $stateLabels = [
            'pending' => 'Awaiting verification',
            'verified' => 'Verified',
            'attention' => 'Needs attention',
        ];
    @endphp

    {{-- =====================================================
        SELLER-STYLE KPI CARDS
    ====================================================== --}}

    <section
        class="pickup-stats"
        aria-label="Pickup request overview"
    >

        @foreach ($summaryCards as [$label, $value, $note, $tone])

            <article class="pickup-stat pickup-stat-{{ $tone }}">

                <div class="min-w-0">

                    <p>
                        {{ $label }}
                    </p>

                    <strong>
                        {{ number_format($value) }}
                    </strong>

                    <span>
                        {{ $note }}
                    </span>

                </div>

                <span
                    class="pickup-stat-icon"
                    aria-hidden="true"
                >

                    @switch($tone)

                        @case('amber')

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

                        @case('green')

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m8 12 2.5 2.5L16 9"/>
                            </svg>

                            @break

                        @case('red')

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v6M12 17h.01"/>
                            </svg>

                            @break

                        @default

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M4 5h16v14H4zM8 9h8M8 13h8"/>
                            </svg>

                    @endswitch

                </span>

            </article>

        @endforeach

    </section>

    {{-- Admin-style standalone search and status filter: visually separate from the table. --}}
    <section class="pickup-admin-filters" aria-label="Search and filter pickup requests">
        <label class="pickup-admin-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.4-3.4"/></svg>
            <span class="sr-only">Search pickup requests</span>
            <input type="search" data-pickup-search placeholder="Search order, Seller, address, or hub..." autocomplete="off">
        </label>
        <div class="pickup-admin-select pickup-filter-dropdown" data-pickup-filter-menu>
            <span class="sr-only">Filter pickup status</span>
            <input type="hidden" value="all" data-pickup-status>
            <button
                type="button"
                class="pickup-admin-select-trigger"
                data-pickup-filter-trigger
                aria-haspopup="listbox"
                aria-expanded="false"
            >
                <span data-pickup-filter-label>All Status</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
            </button>
            <div class="pickup-admin-select-panel" data-pickup-filter-panel role="listbox" aria-label="Filter pickup status" hidden>
                @foreach ($filters as $key => [$label, $count])
                    <button
                        type="button"
                        class="pickup-admin-option{{ $key === 'all' ? ' is-active' : '' }}"
                        data-pickup-filter-option
                        data-value="{{ $key }}"
                        role="option"
                        aria-selected="{{ $key === 'all' ? 'true' : 'false' }}"
                    >
                        {{ $key === 'all' ? 'All Status' : $label }}
                    </button>
                @endforeach
            </div>
        </div>
        <button type="button" class="pickup-admin-apply" data-pickup-apply>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
            Apply Filter
        </button>
        <button type="button" class="pickup-admin-reset" data-pickup-clear>Reset</button>
    </section>

    {{-- Standalone floating table, matching the Sorting Facilities directory. --}}
    <section class="pickup-workspace pickup-admin-directory" aria-labelledby="requests-title">
        <header class="pickup-admin-table-head">
            <div>
                <h2 id="requests-title">Seller Pickup Requests</h2>
                <p>Oldest requests first · Philippine time</p>
            </div>
            <span class="pickup-admin-counter" data-pickup-heading-count role="status" aria-live="polite">
                Showing {{ $orders->count() > 0 ? min(10, $orders->count()) : 0 }} of {{ $orders->count() }}
            </span>
        </header>

        {{-- =================================================
            BULK VERIFICATION FORM
        ================================================== --}}

        <form
            id="pickupVerifyForm"
            method="POST"
            action="{{ route('logistics.pickup-requests.batch-verify') }}"
            data-pickup-verify-form
        >
            @csrf
        </form>

        {{-- BULK ACTIONS --}}

        {{-- Hidden until at least one request is selected. Select all lives in the table header. --}}
        <div class="pickup-selection-bar" data-pickup-selection-bar hidden>
            <div class="pickup-selection-summary" role="status" aria-live="polite">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                <span data-pickup-selected-count>0 selected</span>
            </div>
            <div class="pickup-selection-actions">
                <button type="button" class="pickup-clear-selection" data-pickup-clear-selection>Clear selection</button>
                <button
                    form="pickupVerifyForm"
                    type="submit"
                    class="pickup-button pickup-button-primary"
                    data-pickup-verify-button
                    disabled
                >
                    Verify selected
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </button>
            </div>
        </div>

        {{-- =================================================
            REQUEST TABLE
        ================================================== --}}

        <div
            class="pickup-table-scroll"
            @if($orders->isEmpty()) hidden @endif
        >

            <table class="pickup-table">

                <thead>
                    <tr>

                        <th scope="col" class="pickup-select-column">
                            <input
                                type="checkbox"
                                data-pickup-select-visible
                                aria-label="Select all eligible requests on this page"
                                title="Select visible requests"
                            >
                        </th>

                        <th scope="col">
                            Order / Seller
                        </th>

                        <th scope="col">
                            Pickup Location
                        </th>

                        <th scope="col">
                            Origin Hub
                        </th>

                        <th scope="col">
                            Requested
                        </th>

                        <th scope="col">
                            Status / Action
                        </th>

                    </tr>
                </thead>

                <tbody>

                    @foreach ($orders as $order)

                        @php
                            $origin = $order->deliveryRouteStops->firstWhere(
                                'stop_key',
                                'origin_facility'
                            );

                            $sellerName = $order->seller?->store_name
                                ?: $order->pickup_name
                                ?: 'Seller Pickup';

                            $state = $order->getAttribute(
                                'pickup_queue_state'
                            );

                            $issues = $order->getAttribute(
                                'pickup_issues'
                            );

                            $search = implode(' ', [
                                $order->order_number,
                                $sellerName,
                                $order->pickup_address,
                                $order->buyer_name,
                                $origin?->facility?->name,
                                $origin?->name,
                            ]);
                        @endphp

                        <tr
                            data-pickup-item
                            data-state="{{ $state }}"
                            data-search="{{ $search }}"
                            data-order-id="{{ $order->id }}"
                        >

                            {{-- SELECT REQUEST --}}

                            <td data-label="Select">

                                <input
                                    type="checkbox"
                                    form="pickupVerifyForm"
                                    name="order_ids[]"
                                    value="{{ $order->id }}"
                                    data-pickup-select
                                    @disabled($state !== 'pending')
                                    aria-label="Select {{ $order->order_number }}"
                                >

                            </td>

                            {{-- ORDER AND SELLER --}}

                            <td data-label="Order / Seller">

                                <strong>
                                    {{ $order->order_number }}
                                </strong>

                                <p>
                                    {{ $sellerName }}
                                </p>

                            </td>

                            {{-- PICKUP LOCATION --}}

                            <td data-label="Pickup Location">

                                <strong>
                                    {{ $order->pickup_name ?: $sellerName }}
                                </strong>

                                <p class="pickup-address">

                                    {{
                                        $order->pickup_address
                                        ?: 'Pickup address missing'
                                    }}

                                </p>

                            </td>

                            {{-- ORIGIN HUB --}}

                            <td data-label="Origin Hub">

                                <strong>

                                    {{
                                        $origin?->facility?->name
                                        ?: 'Not routed'
                                    }}

                                </strong>

                                <p>

                                    {{
                                        $origin?->facility?->municipality_name
                                        ?: 'Origin sorting facility'
                                    }}

                                </p>

                            </td>

                            {{-- REQUESTED DATE --}}

                            <td data-label="Requested">

                                <strong>

                                    {{
                                        $order->ready_at?->diffForHumans()
                                        ?: 'Not recorded'
                                    }}

                                </strong>

                                <p>

                                    {{
                                        $order->ready_at
                                            ?->timezone('Asia/Manila')
                                            ->format('M d, Y · g:i A')
                                    }}

                                </p>

                            </td>

                            {{-- STATUS AND ACTION: compact status + accessible three-dot popover --}}

                            <td data-label="Status / Action">
                                <div class="pickup-row-actions">
                                    <span class="pickup-badge pickup-badge-{{ $state }}">{{ $stateLabels[$state] }}</span>

                                    <button
                                        type="button"
                                        class="pickup-more-trigger"
                                        data-pickup-menu-trigger
                                        aria-label="More actions for {{ $order->order_number }}"
                                        aria-expanded="false"
                                        aria-controls="pickup-actions-{{ $order->id }}"
                                        title="More actions"
                                    >
                                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <circle cx="5" cy="12" r="1.8"/>
                                            <circle cx="12" cy="12" r="1.8"/>
                                            <circle cx="19" cy="12" r="1.8"/>
                                        </svg>
                                    </button>

                                    {{-- A native popover avoids being clipped by the scrollable table. --}}
                                    <div
                                        id="pickup-actions-{{ $order->id }}"
                                        class="pickup-more-menu"
                                        data-pickup-more-menu
                                        popover="auto"
                                        role="group"
                                        aria-label="Actions for {{ $order->order_number }}"
                                    >
                                        @if ($state === 'pending')
                                            <form method="POST" action="{{ route('logistics.pickup-requests.verify', $order) }}" data-pickup-single-verify>
                                                @csrf
                                                <button type="submit" class="pickup-menu-item pickup-menu-item--emphasis">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                                                    Verify request
                                                </button>
                                            </form>
                                        @elseif ($state === 'verified')
                                            <a class="pickup-menu-item pickup-menu-item--emphasis" href="{{ route('logistics.delivery-assignment', ['order' => $order->id]) }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                                                Assign pickup rider
                                            </a>
                                        @elseif(! \App\Support\CurrentLogisticsHub::isHubOperator(request()))
                                            <a class="pickup-menu-item pickup-menu-item--emphasis" href="{{ route('logistics.delivery-routes.edit', $order) }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m8 16 8-8M9 8h7v7"/></svg>
                                                Review route
                                            </a>
                                        @endif

                                        <button
                                            type="button"
                                            class="pickup-menu-item"
                                            data-pickup-details-toggle
                                            aria-expanded="false"
                                            aria-controls="pickup-details-{{ $order->id }}"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12s3.4-6.5 9.5-6.5 9.5 6.5 9.5 6.5-3.4 6.5-9.5 6.5-9.5-6.5-9.5-6.5Z"/><circle cx="12" cy="12" r="2.7"/></svg>
                                            See details
                                        </button>
                                    </div>
                                </div>
                            </td>

                        </tr>

                        <tr
                            id="pickup-details-{{ $order->id }}"
                            class="pickup-expanded-row"
                            data-pickup-detail-row
                            hidden
                        >
                            <td colspan="6">
                                <div class="pickup-expanded-panel">
                                    <div class="pickup-expanded-head">
                                        <div>
                                            <span class="pickup-expanded-kicker">Parcel information</span>
                                            <h3>Order {{ $order->order_number }}</h3>
                                        </div>
                                        <button type="button" class="pickup-details-close" data-pickup-details-close>
                                            Close details
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                        </button>
                                    </div>

                                    <div class="pickup-expanded-grid">
                                        <section class="pickup-info-block" aria-label="Buyer information">
                                            <span class="pickup-info-label">Buyer</span>
                                            <strong>{{ $order->buyer_name }}</strong>
                                            <p>{{ $order->buyer_address }}</p>
                                        </section>
                                        <section class="pickup-info-block" aria-label="Payment information">
                                            <span class="pickup-info-label">Payment</span>
                                            <strong>{{ $order->payment_method }} · {{ ucfirst($order->payment_status) }}</strong>
                                            <p>Order value <span class="pickup-info-price">₱{{ number_format((float) $order->total, 2) }}</span></p>
                                        </section>
                                        <section class="pickup-info-block" aria-label="Seller contact">
                                            <span class="pickup-info-label">Seller Contact</span>
                                            <strong>{{ $order->seller?->store_phone ?: $order->seller?->contact_no ?: 'Not provided' }}</strong>
                                            <p>{{ $order->seller?->pickup_instructions ?: 'No pickup instructions provided.' }}</p>
                                        </section>
                                    </div>

                                    @if ($state === 'attention' && !empty($issues))
                                        <section class="pickup-expanded-issues" aria-label="Pickup issues needing attention">
                                            <span class="pickup-info-label">Needs attention</span>
                                            <ul>
                                                @foreach ($issues as $issue)
                                                    <li>{{ $issue }}</li>
                                                @endforeach
                                            </ul>
                                            @if (\App\Support\CurrentLogisticsHub::isHubOperator(request()))
                                                <p>Ask Main Logistics to review the route.</p>
                                            @endif
                                        </section>
                                    @endif

                                    <section class="pickup-expanded-products" aria-label="Parcel items">
                                        <span class="pickup-info-label">Parcel items</span>
                                        <ul>
                                            @forelse (($order->items ?? []) as $item)
                                                <li>
                                                    <span>
                                                        {{ $item['name'] ?? 'Product' }}
                                                        @if (!empty($item['variant_label']))
                                                            <span class="pickup-variant">· {{ $item['variant_label'] }}</span>
                                                        @endif
                                                    </span>
                                                    <strong>× {{ $item['qty'] ?? $item['quantity'] ?? 1 }}</strong>
                                                </li>
                                            @empty
                                                <li><span>Product details are not available for this parcel.</span></li>
                                            @endforelse
                                        </ul>
                                    </section>
                                    @if (
                                        ! \App\Support\CurrentLogisticsHub::isHubOperator(request()) &&
                                        ($order->logisticsParcel || $order->status === 'ready_for_pickup')
                                    )
                                        <div class="pickup-expanded-actions">
                                            <a class="pickup-action pickup-action--outline" href="{{ route('logistics.delivery-routes.edit', $order) }}">
                                                Review delivery route
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        {{-- =================================================
            NO REQUESTS
        ================================================== --}}

        @if ($orders->isEmpty())

            <div class="pickup-empty">

                <span
                    class="pickup-empty-icon"
                    aria-hidden="true"
                >
                    <svg viewBox="0 0 24 24"><path d="m3.5 7 8.5-4.5L20.5 7v10L12 21.5 3.5 17V7Z"/><path d="m3.5 7 8.5 4.5L20.5 7M12 11.5v10M8 4.5l8.5 4.5"/></svg>
                </span>

                <h3>
                    No pickup requests waiting
                </h3>

                <p>
                    Requests appear here when Sellers mark their orders
                    ready for pickup and they are routed to your
                    Logistics network.
                </p>

                <a class="pickup-empty-action" href="{{ route('logistics.delivery-assignment') }}">
                    View Pickup Assignment
                </a>
            </div>

        @endif

        {{-- =================================================
            SEARCH EMPTY STATE
        ================================================== --}}

        <div
            class="pickup-empty"
            data-pickup-search-empty
            hidden
        >
            <span class="pickup-empty-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m15.5 15.5 5 5M8 10.5h5"/></svg>
            </span>

            <h3>
                No matching requests
            </h3>

            <p>
                Try another search or clear the status filter.
            </p>

            <button
                type="button"
                class="pickup-empty-action"
                data-pickup-clear
            >
                Clear filters
            </button>

        </div>

        {{-- =================================================
            FOOTER
        ================================================== --}}

        <footer class="pickup-list-footer pickup-admin-footer">
            <div class="pickup-footer-info">
                <span data-pickup-visible-count aria-live="polite">Showing {{ $orders->count() ? '1–'.min(10, $orders->count()) : '0–0' }} of {{ $orders->count() }} requests</span>
            </div>
            <nav class="pickup-pagination-nav" aria-label="Pickup request pages">
                <button type="button" class="pickup-page-arrow" data-pickup-previous aria-label="Previous page" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <div class="pickup-page-buttons" data-pickup-pages role="group" aria-label="Page numbers"></div>
                <button type="button" class="pickup-page-arrow" data-pickup-next aria-label="Next page" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </nav>
        </footer>

    </section>

</div>

@endsection

@push('scripts')

<script>
(() => {

    'use strict';

    /*
    |--------------------------------------------------------------------------
    | INITIALIZATION
    |--------------------------------------------------------------------------
    */

    function initialize() {

        document.querySelectorAll(
            '[data-pickup-workspace="requests"]'
        ).forEach((root) => {

            if (root.dataset.initialized) {
                return;
            }

            root.dataset.initialized = 'true';

            /*
            |--------------------------------------------------------------------------
            | REFERENCES
            |--------------------------------------------------------------------------
            */

            const items = [
                ...root.querySelectorAll('[data-pickup-item]')
            ];

            // Each expanded information row follows its corresponding request row.
            const detailRows = new Map();
            items.forEach((item) => {
                const detailRow = item.nextElementSibling;
                const trigger = item.querySelector('[data-pickup-details-toggle]');
                if (!detailRow?.matches('[data-pickup-detail-row]') || !trigger) return;
                const menuTrigger = item.querySelector('[data-pickup-menu-trigger]');
                const setExpanded = (open) => {
                    trigger.setAttribute('aria-expanded', String(open));
                    detailRow.hidden = !open || item.hidden;
                    item.classList.toggle('is-expanded', open);
                };
                detailRows.set(item, { detailRow, trigger, setExpanded });
                trigger.addEventListener('click', () => {
                    setExpanded(trigger.getAttribute('aria-expanded') !== 'true');
                    closeActionMenu(item.querySelector('[data-pickup-more-menu]'));
                    if (!detailRow.hidden) detailRow.querySelector('[data-pickup-details-close]')?.focus();
                });
                detailRow.querySelector('[data-pickup-details-close]')?.addEventListener('click', () => {
                    setExpanded(false);
                    menuTrigger?.focus();
                });
            });

            /* Table action menus: native popover avoids clipping inside the scroll region. */
            const actionMenus = [...root.querySelectorAll('[data-pickup-more-menu]')];

            function closeActionMenu(menu) {
                if (!menu) return;
                if (typeof menu.hidePopover === 'function' && menu.matches(':popover-open')) {
                    menu.hidePopover();
                } else {
                    menu.classList.remove('is-open');
                    const button = root.querySelector(`[aria-controls="${menu.id}"]`);
                    button?.setAttribute('aria-expanded', 'false');
                }
            }

            function positionActionMenu(menu, trigger) {
                const rect = trigger.getBoundingClientRect();
                const width = menu.offsetWidth || 205;
                const height = menu.offsetHeight || 90;
                const gap = 7;
                const left = Math.max(12, Math.min(rect.right - width, window.innerWidth - width - 12));
                const fitsBelow = window.innerHeight - rect.bottom >= height + gap + 12;
                const top = fitsBelow || rect.top < height + gap + 12
                    ? rect.bottom + gap
                    : rect.top - height - gap;
                menu.style.left = `${Math.round(left)}px`;
                menu.style.top = `${Math.max(12, Math.round(top))}px`;
            }

            actionMenus.forEach((menu) => {
                const trigger = root.querySelector(`[aria-controls="${menu.id}"]`);
                if (!trigger) return;
                const nativePopover = typeof menu.showPopover === 'function';

                if (nativePopover) {
                    menu.addEventListener('toggle', () => {
                        trigger.setAttribute('aria-expanded', String(menu.matches(':popover-open')));
                    });
                }

                trigger.addEventListener('click', () => {
                    const isOpen = nativePopover ? menu.matches(':popover-open') : menu.classList.contains('is-open');
                    actionMenus.forEach((other) => closeActionMenu(other));
                    if (isOpen) return;
                    if (nativePopover) menu.showPopover();
                    else menu.classList.add('is-open');
                    trigger.setAttribute('aria-expanded', 'true');
                    positionActionMenu(menu, trigger);
                    menu.querySelector('.pickup-menu-item')?.focus();
                });

                menu.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        event.preventDefault();
                        closeActionMenu(menu);
                        trigger.focus();
                    }
                });
            });

            // Keep an open menu aligned if the table or viewport scrolls/resizes.
            function repositionOpenMenus() {
                actionMenus.forEach((menu) => {
                    const open = typeof menu.showPopover === 'function'
                        ? menu.matches(':popover-open') : menu.classList.contains('is-open');
                    if (!open) return;
                    const trigger = root.querySelector(`[aria-controls="${menu.id}"]`);
                    if (trigger && trigger.getBoundingClientRect().height) positionActionMenu(menu, trigger);
                    else closeActionMenu(menu);
                });
            }
            window.addEventListener('resize', repositionOpenMenus);
            document.addEventListener('scroll', repositionOpenMenus, true);
            document.addEventListener('pointerdown', (event) => {
                // Native popovers handle outside dismissal automatically.
                if (typeof Element.prototype.showPopover === 'function') return;
                actionMenus.forEach((menu) => {
                    const trigger = root.querySelector(`[aria-controls="${menu.id}"]`);
                    if (!menu.contains(event.target) && !trigger?.contains(event.target)) closeActionMenu(menu);
                });
            });

            const search = root.querySelector(
                '[data-pickup-search]'
            );

            const empty = root.querySelector(
                '[data-pickup-search-empty]'
            );

            const statusFilter = root.querySelector('[data-pickup-status]');
            const statusFilterMenu = root.querySelector('[data-pickup-filter-menu]');
            const statusFilterTrigger = root.querySelector('[data-pickup-filter-trigger]');
            const statusFilterPanel = root.querySelector('[data-pickup-filter-panel]');
            const statusFilterLabel = root.querySelector('[data-pickup-filter-label]');
            const statusFilterOptions = [...root.querySelectorAll('[data-pickup-filter-option]')];
            const pageButtons = root.querySelector('[data-pickup-pages]');
            const previousButton = root.querySelector('[data-pickup-previous]');
            const nextButton = root.querySelector('[data-pickup-next]');

            const selectVisible = root.querySelector(
                '[data-pickup-select-visible]'
            );
            const selectionBar = root.querySelector('[data-pickup-selection-bar]');
            const clearSelection = root.querySelector('[data-pickup-clear-selection]');

            const verifyButton = root.querySelector(
                '[data-pickup-verify-button]'
            );

            const verifyForm = root.querySelector(
                '[data-pickup-verify-form]'
            );

            let currentPage = 1;
            let filterFrame = 0;

            /*
            |--------------------------------------------------------------------------
            | NORMALIZATION
            |--------------------------------------------------------------------------
            */

            const normalize = (value) => {

                return String(value || '')
                    .toLocaleLowerCase()
                    .trim();

            };

            /*
            |--------------------------------------------------------------------------
            | SELECTED REQUESTS
            |--------------------------------------------------------------------------
            */

            const selectedRequests = () => {

                return [
                    ...root.querySelectorAll('[data-pickup-select]')
                ].filter((input) => {

                    return input.checked && !input.disabled;

                });

            };

            /*
            |--------------------------------------------------------------------------
            | UPDATE BULK SELECTION
            |--------------------------------------------------------------------------
            */

            function updateSelection() {

                const eligible = [
                    ...root.querySelectorAll('[data-pickup-select]')
                ].filter((input) => {

                    return !input.disabled
                        && !input.closest('[data-pickup-item]').hidden;

                });

                const count = selectedRequests().length;

                root.querySelector(
                    '[data-pickup-selected-count]'
                ).textContent = `${count} selected`;

                // Keep the table clean until a row or the select-all checkbox is used.
                if (selectionBar) selectionBar.hidden = count === 0;
                verifyButton.disabled = count === 0;

                selectVisible.disabled = eligible.length === 0;

                selectVisible.checked = eligible.length > 0
                    && eligible.every((input) => input.checked);

                selectVisible.indeterminate = eligible.some(
                    (input) => input.checked
                ) && !selectVisible.checked;

            }

            function syncStatusFilterUi(value) {
                statusFilterOptions.forEach((option) => {
                    const active = option.dataset.value === value;
                    option.classList.toggle('is-active', active);
                    option.setAttribute('aria-selected', String(active));
                    if (active && statusFilterLabel) {
                        statusFilterLabel.textContent = option.textContent.trim();
                    }
                });
            }

            function closeStatusFilterMenu() {
                if (!statusFilterPanel || !statusFilterTrigger) return;
                statusFilterPanel.hidden = true;
                statusFilterTrigger.setAttribute('aria-expanded', 'false');
            }

            function openStatusFilterMenu() {
                if (!statusFilterPanel || !statusFilterTrigger) return;
                statusFilterPanel.hidden = false;
                statusFilterTrigger.setAttribute('aria-expanded', 'true');
            }

            statusFilterTrigger?.addEventListener('click', () => {
                if (statusFilterPanel?.hidden) openStatusFilterMenu();
                else closeStatusFilterMenu();
            });

            statusFilterOptions.forEach((option) => {
                option.addEventListener('click', () => {
                    if (statusFilter) statusFilter.value = option.dataset.value || 'all';
                    syncStatusFilterUi(statusFilter?.value || 'all');
                    closeStatusFilterMenu();
                    applyFilters();
                });
            });

            document.addEventListener('pointerdown', (event) => {
                if (!statusFilterMenu?.contains(event.target)) closeStatusFilterMenu();
            });

            statusFilterMenu?.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeStatusFilterMenu();
                    statusFilterTrigger?.focus();
                }
            });

            syncStatusFilterUi(statusFilter?.value || 'all');

            /*
            |--------------------------------------------------------------------------
            | SEARCH, STATUS FILTER, AND CLIENT-SIDE PAGINATION
            |--------------------------------------------------------------------------
            */
            // Search strings are normalized once; pagination does not issue extra requests.
            const searchIndex = items.map((item) => ({
                item,
                search: normalize(item.dataset.search),
                state: item.dataset.state || ''
            }));

            function renderPagination(pageCount) {
                if (!pageButtons) return;
                const count = Math.min(pageCount, 5);
                const from = Math.min(
                    Math.max(1, currentPage - Math.floor(count / 2)),
                    Math.max(1, pageCount - count + 1)
                );
                const fragment = document.createDocumentFragment();
                for (let page = from; page < from + count; page++) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'pickup-page-number' + (page === currentPage ? ' is-current' : '');
                    button.textContent = String(page);
                    button.setAttribute('aria-label', 'Page ' + page);
                    if (page === currentPage) button.setAttribute('aria-current', 'page');
                    button.addEventListener('click', () => {
                        if (currentPage === page) return;
                        currentPage = page;
                        applyFilters(false);
                    });
                    fragment.appendChild(button);
                }
                pageButtons.replaceChildren(fragment);
            }

            function applyFilters(resetPage = true) {
                if (resetPage) currentPage = 1;
                window.cancelAnimationFrame(filterFrame);
                filterFrame = window.requestAnimationFrame(() => {
                    actionMenus.forEach(closeActionMenu);
                    const keyword = normalize(search?.value);
                    const filter = statusFilter?.value || 'all';
                    const matching = searchIndex.filter(({ search, state }) =>
                        (filter === 'all' || state === filter)
                        && (!keyword || search.includes(keyword))
                    );
                    const pageSize = 10;
                    const pageCount = Math.max(1, Math.ceil(matching.length / pageSize));
                    currentPage = Math.min(Math.max(1, currentPage), pageCount);
                    const offset = (currentPage - 1) * pageSize;
                    const visibleItems = new Set(matching.slice(offset, offset + pageSize).map(({ item }) => item));
                    const matchedItems = new Set(matching.map(({ item }) => item));

                    items.forEach((item) => {
                        item.hidden = !visibleItems.has(item);
                        const details = detailRows.get(item);
                        if (details) {
                            details.detailRow.hidden = item.hidden || details.trigger.getAttribute('aria-expanded') !== 'true';
                        }
                        // Preserve cross-page selection; clear only requests excluded by a changed filter.
                        if (resetPage && !matchedItems.has(item)) {
                            const checkbox = item.querySelector('[data-pickup-select]');
                            if (checkbox) checkbox.checked = false;
                        }
                    });
                    if (empty) empty.hidden = items.length === 0 || matching.length > 0;
                    const countLabel = root.querySelector('[data-pickup-visible-count]');
                    const headingCount = root.querySelector('[data-pickup-heading-count]');
                    const shown = visibleItems.size;
                    if (headingCount) headingCount.textContent = `Showing ${shown} of ${matching.length}`;
                    if (countLabel) {
                        countLabel.textContent = matching.length
                            ? `Showing ${offset + 1}–${Math.min(offset + shown, matching.length)} of ${matching.length} requests`
                            : 'Showing 0–0 of 0 requests';
                    }
                    renderPagination(pageCount);
                    if (previousButton) previousButton.disabled = currentPage <= 1;
                    if (nextButton) nextButton.disabled = currentPage >= pageCount;
                    const nav = root.querySelector('.pickup-pagination-nav');
                    if (nav) nav.hidden = items.length === 0;
                    updateSelection();
                });
            }

            // Live search, plus explicit filter and reset controls match Sorting Facilities.
            search?.addEventListener('input', () => applyFilters());
            root.querySelector('[data-pickup-apply]')?.addEventListener('click', () => applyFilters());
            root.querySelectorAll('[data-pickup-clear]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (search) search.value = '';
                    if (statusFilter) statusFilter.value = 'all';
                    syncStatusFilterUi('all');
                    applyFilters();
                });
            });
            previousButton?.addEventListener('click', () => { currentPage--; applyFilters(false); });
            nextButton?.addEventListener('click', () => { currentPage++; applyFilters(false); });

            /*
            |--------------------------------------------------------------------------
            | RESTORE SELECTED ORDER IDS AFTER VALIDATION ERROR
            |--------------------------------------------------------------------------
            */

            const oldIds = new Set(
                JSON.parse(root.dataset.oldIds || '[]').map(String)
            );

            root.querySelectorAll(
                '[data-pickup-select]'
            ).forEach((input) => {

                input.checked = !input.disabled
                    && oldIds.has(input.value);

                input.addEventListener(
                    'change',
                    updateSelection
                );

            });

            /*
            |--------------------------------------------------------------------------
            | SELECT VISIBLE PENDING REQUESTS
            |--------------------------------------------------------------------------
            */

            clearSelection?.addEventListener('click', () => {
                root.querySelectorAll('[data-pickup-select]').forEach((input) => {
                    input.checked = false;
                });
                updateSelection();
                selectVisible?.focus();
            });

            selectVisible.addEventListener('change', (event) => {

                const checked = event.target.checked;

                let count = 0;

                root.querySelectorAll(
                    '[data-pickup-select]'
                ).forEach((input) => {

                    const visible = !input.closest(
                        '[data-pickup-item]'
                    ).hidden;

                    if (!input.disabled && visible) {

                        input.checked = checked && count < 200;

                        if (input.checked) {
                            count++;
                        }

                    }

                });

                updateSelection();

            });

            /*
            |--------------------------------------------------------------------------
            | BULK VERIFICATION
            |--------------------------------------------------------------------------
            */

            verifyForm.addEventListener('submit', (event) => {

                const count = selectedRequests().length;

                if (!count || count > 200) {

                    event.preventDefault();

                    if (count > 200) {

                        window.alert(
                            'Select up to 200 requests per verification.'
                        );

                    }

                    return;

                }

                const confirmed = window.confirm(
                    `Verify ${count} selected pickup `
                    + `${count === 1 ? 'request' : 'requests'}? `
                    + 'These parcels will become available for '
                    + 'Pickup Rider assignment.'
                );

                if (!confirmed) {

                    event.preventDefault();
                    return;

                }

                verifyButton.disabled = true;

            });

            /*
            |--------------------------------------------------------------------------
            | SINGLE REQUEST VERIFICATION
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-pickup-single-verify]'
            ).forEach((form) => {

                form.addEventListener('submit', (event) => {

                    const confirmed = window.confirm(
                        'Verify this Seller pickup request and make '
                        + 'it available for Rider assignment?'
                    );

                    if (!confirmed) {

                        event.preventDefault();
                        return;

                    }

                    form.querySelector('button').disabled = true;

                });

            });

            /*
            |--------------------------------------------------------------------------
            | INITIAL RENDER
            |--------------------------------------------------------------------------
            */

            applyFilters();

        });

    }

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
    | RESTORE PAGE AFTER BROWSER BACK/FORWARD CACHE
    |--------------------------------------------------------------------------
    */

    window.addEventListener('pageshow', (event) => {

        if (event.persisted) {

            window.location.reload();

        }

    });

})();
</script>

@endpush