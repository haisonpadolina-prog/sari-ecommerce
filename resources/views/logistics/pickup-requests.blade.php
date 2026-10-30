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

#lgPickupRequests .pickup-stat-icon {
    display: grid;
    place-items: center;
    flex: 0 0 41px;

    width: 41px;
    height: 41px;

    border-radius: 11px;
    background: #f1f6fd;
    color: #587ead;
}

#lgPickupRequests .pickup-stat-icon svg {
    width: 18px;
    height: 18px;
}

#lgPickupRequests .pickup-stat-amber .pickup-stat-icon {
    background: #fff7e9;
    color: #bc8420;
}

#lgPickupRequests .pickup-stat-green .pickup-stat-icon {
    background: #eef8f2;
    color: #4c8663;
}

#lgPickupRequests .pickup-stat-red .pickup-stat-icon {
    background: #fff1f1;
    color: #bd6d66;
}

/* WORKFLOW STEPPER */

#lgPickupRequests .pickup-flow {
    gap: 4px;
    margin: 14px 0;
    padding: 4px;
    border-radius: 10px;
    box-shadow: none;
}

#lgPickupRequests .pickup-flow :is(a,span) {
    min-height: 32px;
    padding: 5px 10px;
    font-size: 9px;
    border-radius: 7px;
}

#lgPickupRequests .pickup-flow b {
    width: 21px;
    height: 21px;
    flex-basis: 21px;
    font-size: 9px;
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

#lgPickupRequests .pickup-empty {
    padding: 39px 20px;
}

#lgPickupRequests .pickup-empty-icon {
    width: 43px;
    height: 43px;
    background: #fff7e8;
    border-radius: 12px;
}

#lgPickupRequests .pickup-empty h3 {
    font-size: 11px;
}

#lgPickupRequests .pickup-empty p {
    font-size: 9px;
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

    #lgPickupRequests .pickup-stat-icon {
        width: 34px;
        height: 34px;
        flex-basis: 34px;
    }

    #lgPickupRequests .pickup-flow {
        width: 100%;
    }
}

@media (prefers-reduced-motion:reduce) {
    #lgPickupRequests .pickup-stat:hover {
        transform: none !important;
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
                SELLER COLLECTIONS
            </p>

            <h1>
                Pickup <span>Requests</span>
            </h1>

            <p class="pickup-subtitle">
                Review Seller requests, verify pickup details,
                and prepare parcels for Rider assignment.
            </p>

        </div>

        <a
            class="pickup-button pickup-button-primary"
            href="{{ route('logistics.delivery-assignment') }}"
        >
            Pickup Assignment

            <span aria-hidden="true">
                →
            </span>
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

    {{-- =====================================================
        PICKUP WORKFLOW
    ====================================================== --}}

    <nav
        class="pickup-flow"
        aria-label="Pickup workflow"
    >

        <span
            class="is-current"
            aria-current="step"
        >
            <b>1</b>
            Review &amp; Verify
        </span>

        <a href="{{ route('logistics.delivery-assignment') }}">
            <b>2</b>
            Assign Pickup Rider
        </a>

        <a href="{{ route('logistics.incoming-parcels') }}">
            <b>3</b>
            Origin Hub Intake
        </a>

    </nav>

    {{-- =====================================================
        PICKUP REQUEST WORKSPACE
    ====================================================== --}}

    <section
        class="pickup-workspace"
        aria-labelledby="requests-title"
    >

        {{-- WORKSPACE HEADER --}}

        <div class="pickup-toolbar">

            <div>

                <p class="pickup-eyebrow">
                    PICKUP QUEUE
                </p>

                <h2 id="requests-title">
                    Seller Pickup Requests
                </h2>

                <p>
                    Oldest requests appear first.
                    Verification does not assign a Rider.
                </p>

            </div>

            <label class="pickup-search">

                <span class="sr-only">
                    Search pickup requests
                </span>

                <input
                    type="search"
                    data-pickup-search
                    placeholder="Search order, Seller, address, or hub…"
                    autocomplete="off"
                >

            </label>

        </div>

        {{-- STATUS FILTERS --}}

        <div class="pickup-filter-row">

            <div
                class="pickup-tabs"
                role="group"
                aria-label="Filter requests"
            >

                @foreach ($filters as $filter => [$label, $count])

                    <button
                        type="button"
                        data-pickup-filter="{{ $filter }}"
                        aria-pressed="{{ $filter === 'all' ? 'true' : 'false' }}"
                    >
                        {{ $label }}

                        <span>
                            {{ $count }}
                        </span>
                    </button>

                @endforeach

            </div>

            <button
                type="button"
                class="pickup-text-link"
                data-pickup-clear
            >
                Clear filters
            </button>

        </div>

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

        <div
            class="pickup-selection-bar"
            @if($orders->isEmpty()) hidden @endif
        >

            <label>

                <input
                    type="checkbox"
                    data-pickup-select-visible
                >

                Select visible requests

            </label>

            <span
                data-pickup-selected-count
                aria-live="polite"
            >
                0 selected
            </span>

            <button
                form="pickupVerifyForm"
                type="submit"
                class="pickup-button pickup-button-primary"
                data-pickup-verify-button
                disabled
            >
                Verify selected
            </button>

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

                        <th scope="col">
                            <span class="sr-only">
                                Select
                            </span>
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

                            <td>

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

                            <td>

                                <strong>
                                    {{ $order->order_number }}
                                </strong>

                                <p>
                                    {{ $sellerName }}
                                </p>

                                <details class="pickup-details">

                                    <summary>

                                        View parcel details

                                        <span aria-hidden="true">
                                            +
                                        </span>

                                    </summary>

                                    <div class="pickup-detail-body">

                                        <div class="pickup-detail-grid">

                                            {{-- BUYER --}}

                                            <div>

                                                <span class="pickup-label">
                                                    Buyer
                                                </span>

                                                <strong>
                                                    {{ $order->buyer_name }}
                                                </strong>

                                                <p>
                                                    {{ $order->buyer_address }}
                                                </p>

                                            </div>

                                            {{-- PAYMENT --}}

                                            <div>

                                                <span class="pickup-label">
                                                    Payment
                                                </span>

                                                <strong>
                                                    {{ $order->payment_method }}
                                                    ·
                                                    {{ ucfirst($order->payment_status) }}
                                                </strong>

                                                <p>
                                                    Order value
                                                    ₱{{ number_format((float) $order->total, 2) }}
                                                </p>

                                            </div>

                                            {{-- SELLER CONTACT --}}

                                            <div>

                                                <span class="pickup-label">
                                                    Seller Contact
                                                </span>

                                                <strong>

                                                    {{
                                                        $order->seller?->store_phone
                                                        ?: $order->seller?->contact_no
                                                        ?: 'Not provided'
                                                    }}

                                                </strong>

                                                <p>

                                                    {{
                                                        $order->seller?->pickup_instructions
                                                        ?: 'No pickup instructions provided.'
                                                    }}

                                                </p>

                                            </div>

                                        </div>

                                        {{-- PRODUCTS --}}

                                        <ul class="pickup-products">

                                            @forelse (($order->items ?? []) as $item)

                                                <li>

                                                    <span>

                                                        {{ $item['name'] ?? 'Product' }}

                                                        @if (!empty($item['variant_label']))
                                                            · {{ $item['variant_label'] }}
                                                        @endif

                                                    </span>

                                                    <strong>

                                                        × {{
                                                            $item['qty']
                                                            ?? $item['quantity']
                                                            ?? 1
                                                        }}

                                                    </strong>

                                                </li>

                                            @empty

                                                <li>
                                                    Product details are not
                                                    available for this parcel.
                                                </li>

                                            @endforelse

                                        </ul>

                                        {{-- ROUTE DETAILS --}}

                                        @if (
                                            ! \App\Support\CurrentLogisticsHub::isHubOperator(request()) &&
                                            ($order->logisticsParcel || $order->status === 'ready_for_pickup')
                                        )

                                            <a
                                                class="pickup-text-link"
                                                href="{{ route('logistics.delivery-routes.edit', $order) }}"
                                            >
                                                Review delivery route →
                                            </a>

                                        @endif

                                    </div>

                                </details>

                            </td>

                            {{-- PICKUP LOCATION --}}

                            <td>

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

                            <td>

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

                            <td>

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

                            {{-- STATUS AND ACTION --}}

                            <td>

                                <span
                                    class="pickup-badge pickup-badge-{{ $state }}"
                                >
                                    {{ $stateLabels[$state] }}
                                </span>

                                @if ($state === 'pending')

                                    <form
                                        method="POST"
                                        action="{{ route('logistics.pickup-requests.verify', $order) }}"
                                        data-pickup-single-verify
                                    >

                                        @csrf

                                        <button
                                            class="pickup-text-link"
                                            type="submit"
                                        >
                                            Verify request →
                                        </button>

                                    </form>

                                @elseif ($state === 'verified')

                                    <a
                                        class="pickup-text-link"
                                        href="{{ route('logistics.delivery-assignment', ['order' => $order->id]) }}"
                                    >
                                        Assign Pickup Rider →
                                    </a>

                                @else

                                    <ul class="pickup-issues">

                                        @foreach ($issues as $issue)

                                            <li>
                                                {{ $issue }}
                                            </li>

                                        @endforeach

                                    </ul>

                                    @if(! \App\Support\CurrentLogisticsHub::isHubOperator(request()))
                                    <a
                                        class="pickup-text-link"
                                        href="{{ route('logistics.delivery-routes.edit', $order) }}"
                                    >
                                        Review route →
                                    </a>
                                    @else
                                        <span class="pickup-subtext">Ask Main Logistics to review the route.</span>
                                    @endif

                                @endif

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
                    ✓
                </span>

                <h3>
                    No pickup requests waiting
                </h3>

                <p>
                    Requests appear here when Sellers mark their orders
                    ready for pickup and they are routed to your
                    Logistics network.
                </p>

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

            <h3>
                No matching requests
            </h3>

            <p>
                Try another search or clear the status filter.
            </p>

            <button
                type="button"
                class="pickup-text-link"
                data-pickup-clear
            >
                Clear filters
            </button>

        </div>

        {{-- =================================================
            FOOTER
        ================================================== --}}

        <footer class="pickup-list-footer">

            <span
                data-pickup-visible-count
                aria-live="polite"
            >
                {{ $orders->count() }} requests shown
            </span>

            <span>
                Request times shown in Philippine time
            </span>

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

            const search = root.querySelector(
                '[data-pickup-search]'
            );

            const empty = root.querySelector(
                '[data-pickup-search-empty]'
            );

            const tabs = [
                ...root.querySelectorAll('[data-pickup-filter]')
            ];

            const selectVisible = root.querySelector(
                '[data-pickup-select-visible]'
            );

            const verifyButton = root.querySelector(
                '[data-pickup-verify-button]'
            );

            const verifyForm = root.querySelector(
                '[data-pickup-verify-form]'
            );

            let filter = 'all';

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

                verifyButton.disabled = count === 0;

                selectVisible.disabled = eligible.length === 0;

                selectVisible.checked = eligible.length > 0
                    && eligible.every((input) => input.checked);

                selectVisible.indeterminate = eligible.some(
                    (input) => input.checked
                ) && !selectVisible.checked;

            }

            /*
            |--------------------------------------------------------------------------
            | APPLY SEARCH + STATUS FILTERS
            |--------------------------------------------------------------------------
            */

            function applyFilters() {

                const keyword = normalize(search?.value);

                let visible = 0;

                items.forEach((item) => {

                    const matchesState = filter === 'all'
                        || item.dataset.state === filter;

                    const matchesSearch = normalize(
                        item.dataset.search
                    ).includes(keyword);

                    item.hidden = !matchesState || !matchesSearch;

                    if (item.hidden) {

                        item.querySelector(
                            '[data-pickup-select]'
                        ).checked = false;

                    } else {

                        visible++;

                    }

                });

                if (empty) {

                    empty.hidden = items.length === 0 || visible > 0;

                }

                root.querySelector(
                    '[data-pickup-visible-count]'
                ).textContent = `${visible} of ${items.length} requests shown`;

                tabs.forEach((tab) => {

                    tab.setAttribute(
                        'aria-pressed',
                        String(tab.dataset.pickupFilter === filter)
                    );

                });

                updateSelection();

            }

            /*
            |--------------------------------------------------------------------------
            | STATUS FILTER BUTTONS
            |--------------------------------------------------------------------------
            */

            tabs.forEach((tab) => {

                tab.addEventListener('click', () => {

                    filter = tab.dataset.pickupFilter;

                    applyFilters();

                });

            });

            /*
            |--------------------------------------------------------------------------
            | LIVE SEARCH
            |--------------------------------------------------------------------------
            */

            search?.addEventListener(
                'input',
                applyFilters
            );

            /*
            |--------------------------------------------------------------------------
            | RESET FILTERS
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-pickup-clear]'
            ).forEach((button) => {

                button.addEventListener('click', () => {

                    filter = 'all';

                    if (search) {
                        search.value = '';
                    }

                    applyFilters();

                });

            });

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