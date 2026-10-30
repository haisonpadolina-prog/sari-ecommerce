
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

            <p class="pickup-subtitle">
                Assign one Rider to collect an entire batch from the same
                Seller, pickup location and origin hub. Every ready order
                in that group must be verified before dispatch.
            </p>

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

    {{-- WORKFLOW --}}

    <nav
        class="pickup-flow"
        aria-label="Pickup workflow"
    >

        <a href="{{ route('logistics.pickup-requests') }}">
            <b>1</b>
            Review &amp; Verify
        </a>

        <span
            class="is-current"
            aria-current="step"
        >
            <b>2</b>
            Assign Pickup Rider
        </span>

        <a href="{{ route('logistics.incoming-parcels') }}">
            <b>3</b>
            Origin Hub Intake
        </a>

    </nav>

    {{-- ASSIGNMENT WORKSPACE --}}

    <section
        class="pickup-workspace"
        aria-labelledby="pickup-assignment-title"
    >

        <header class="pickup-toolbar">

            <div>

                <p class="pickup-eyebrow">
                    PICKUP DISPATCH
                </p>

                <h2 id="pickup-assignment-title">
                    Verified Pickup Batches
                </h2>

                <p>
                    Complete Seller groups are assignable.
                    Incomplete groups remain on hold until verification is finished.
                </p>

            </div>

            <label class="pickup-search">

                <span class="sr-only">
                    Search pickup batches
                </span>

                <input
                    type="search"
                    data-assignment-search
                    autocomplete="off"
                    placeholder="Search Seller, order, Buyer, or hub…"
                >

            </label>

        </header>

        {{-- FILTERS --}}

        <div class="pickup-filter-row">

            <div
                class="pickup-tabs"
                role="group"
                aria-label="Filter pickup batches"
            >

                <button
                    type="button"
                    data-assignment-filter="all"
                    aria-pressed="true"
                >
                    All
                    <span>{{ $stats['pickup_groups'] }}</span>
                </button>

                <button
                    type="button"
                    data-assignment-filter="batch"
                    aria-pressed="false"
                >
                    Multi-parcel
                    <span>{{ $stats['multi_order_groups'] }}</span>
                </button>

                <button
                    type="button"
                    data-assignment-filter="single"
                    aria-pressed="false"
                >
                    Single parcel

                    <span>
                        {{
                            max(
                                0,
                                $stats['pickup_groups']
                                - $stats['multi_order_groups']
                            )
                        }}
                    </span>
                </button>

            </div>

            <button
                type="button"
                class="pickup-text-link"
                data-assignment-clear
            >
                Clear filters
            </button>

        </div>

        {{-- BATCH LIST --}}

        <div class="pickup-batches">

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
                    class="pickup-batch"
                    data-assignment-batch
                    data-batch-type="{{ $batchType }}"
                    data-batch-blocked="{{ $blocked ? 'true' : 'false' }}"
                    data-search="{{ $searchText }}"
                    data-order-ids="{{ $orderIds->implode(',') }}"
                >

                    {{-- BATCH HEADER --}}

                    <header class="pickup-batch-header">

                        <div class="pickup-seller-heading">

                            <span
                                class="pickup-avatar"
                                aria-hidden="true"
                            >
                                {{ $sellerInitial ?: 'S' }}
                            </span>

                            <div class="min-w-0">

                                <p class="pickup-eyebrow">
                                    SELLER PICKUP
                                </p>

                                <h3>{{ $sellerName }}</h3>

                            </div>

                        </div>

                        <div class="pickup-batch-tags">

                            <span>
                                {{ $group['order_count'] }}
                                verified
                                {{
                                    $group['order_count'] === 1
                                        ? 'parcel'
                                        : 'parcels'
                                }}
                            </span>

                            <span>
                                {{ $group['buyer_count'] }}
                                {{
                                    $group['buyer_count'] === 1
                                        ? 'buyer'
                                        : 'buyers'
                                }}
                            </span>

                            <span>
                                {{ $group['destination_count'] }}
                                {{
                                    $group['destination_count'] === 1
                                        ? 'destination'
                                        : 'destinations'
                                }}
                            </span>

                            @if($blocked)

                                <span class="pa-batch-status is-blocked">
                                    {{ $group['pending_count'] }}
                                    awaiting verification
                                </span>

                            @else

                                <span class="pa-batch-status">
                                    ✓ Ready to assign
                                </span>

                            @endif

                        </div>

                    </header>

                    {{-- BATCH CONTENT --}}

                    <div class="pickup-batch-layout">

                        {{-- LEFT SIDE --}}

                        <div class="pickup-batch-main">

                            {{-- PICKUP ROUTE SUMMARY --}}

                            <div class="pickup-route-summary">

                                <div>

                                    <span class="pickup-label">
                                        Seller Pickup
                                    </span>

                                    <strong>
                                        {{
                                            $group['pickup_name']
                                            ?: $sellerName
                                        }}
                                    </strong>

                                    <p>
                                        {{ $group['pickup_address'] }}
                                    </p>

                                </div>

                                <span
                                    class="pickup-route-arrow"
                                    aria-hidden="true"
                                >
                                    →
                                </span>

                                <div>

                                    <span class="pickup-label">
                                        Origin Sorting Hub
                                    </span>

                                    <strong>
                                        {{ $group['origin_facility_name'] }}
                                    </strong>

                                    <p>
                                        The assigned Rider delivers the entire
                                        Seller batch to this hub.
                                    </p>

                                </div>

                            </div>

                            {{-- VERIFIED PARCELS --}}

                            <div class="pickup-parcel-heading">

                                <div>

                                    <h4>
                                        Verified Parcels
                                    </h4>

                                    <p class="pa-help">
                                        Buyers and destination hubs may differ
                                        within the same Seller collection.
                                    </p>

                                </div>

                                <span class="pickup-count">
                                    {{ $group['order_count'] }}
                                </span>

                            </div>

                            <div class="pickup-batch-parcels">

                                @foreach($group['orders'] as $order)

                                    @php
                                        $destination = $order
                                            ->deliveryRouteStops
                                            ->firstWhere(
                                                'stop_key',
                                                'destination_facility'
                                            );

                                        $destinationName =
                                            $destination?->facility?->name
                                            ?: $destination?->name
                                            ?: 'Destination pending';
                                    @endphp

                                    <article class="pickup-parcel">

                                        <div class="min-w-0">

                                            <strong>
                                                {{ $order->order_number }}
                                            </strong>

                                            <small>
                                                {{ $order->buyer_name }}
                                                ·
                                                {{ $destinationName }}
                                            </small>

                                        </div>

                                        <span class="pickup-parcel-value">

                                            ₱{{ number_format(
                                                (float) $order->total,
                                                2
                                            ) }}

                                        </span>

                                        {{-- EXPAND PARCEL INFORMATION --}}

                                        <details class="pickup-details">

                                            <summary>

                                                Parcel details

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

                                                    {{-- DESTINATION --}}

                                                    <div>

                                                        <span class="pickup-label">
                                                            Destination Hub
                                                        </span>

                                                        <strong>
                                                            {{ $destinationName }}
                                                        </strong>

                                                        <p>
                                                            Continues after origin hub sorting.
                                                        </p>

                                                    </div>

                                                    {{-- PAYMENT --}}

                                                    <div>

                                                        <span class="pickup-label">
                                                            Payment
                                                        </span>

                                                        <strong>

                                                            {{
                                                                $order->payment_method
                                                                ?: 'Not recorded'
                                                            }}

                                                        </strong>

                                                        <p>

                                                            {{
                                                                ucfirst(
                                                                    $order->payment_status
                                                                    ?: 'pending'
                                                                )
                                                            }}

                                                        </p>

                                                    </div>

                                                    {{-- ORDER VALUE --}}

                                                    <div>

                                                        <span class="pickup-label">
                                                            Order Value
                                                        </span>

                                                        <strong>

                                                            ₱{{ number_format(
                                                                (float) $order->total,
                                                                2
                                                            ) }}

                                                        </strong>

                                                        <p>
                                                            Marketplace order total
                                                        </p>

                                                    </div>

                                                </div>

                                                {{-- PRODUCTS --}}

                                                <ul class="pickup-products">

                                                    @forelse(($order->items ?? []) as $item)

                                                        <li>

                                                            <span>

                                                                {{ $item['name'] ?? 'Product' }}

                                                                @if(!empty($item['variant_label']))
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
                                                            <span>
                                                                Product details unavailable.
                                                            </span>
                                                        </li>

                                                    @endforelse

                                                </ul>

                                            </div>

                                        </details>

                                    </article>

                                @endforeach

                            </div>

                            {{-- FUTURE DESTINATION HUBS --}}

                            @if($group['destination_names']->isNotEmpty())

                                <div class="pa-destinations">

                                    <span class="pickup-label">
                                        Future Destination Hubs
                                    </span>

                                    @foreach($group['destination_names'] as $destinationName)

                                        <span class="pickup-badge pickup-badge-active">
                                            {{ $destinationName }}
                                        </span>

                                    @endforeach

                                </div>

                            @endif

                        </div>

                        {{-- RIGHT SIDE: RIDER DISPATCH --}}

                        <aside class="pickup-dispatch-panel">

                            <h4>
                                Assign Pickup Rider
                            </h4>

                            {{-- BLOCKED BATCH --}}

                            @if($blocked)

                                <div
                                    class="pickup-batch-warning"
                                    role="status"
                                >

                                    <strong>
                                        Awaiting Complete Batch Verification
                                    </strong>

                                    <p>
                                        {{ $group['order_count'] }}
                                        of
                                        {{ $group['total_group_orders'] }}
                                        orders verified.

                                        Verify all
                                        {{ $group['pending_count'] }}
                                        remaining
                                        {{
                                            $group['pending_count'] === 1
                                                ? 'order'
                                                : 'orders'
                                        }}
                                        first.
                                    </p>

                                    <p>

                                        <b>Pending:</b>

                                        {{
                                            $group['pending_order_numbers']
                                                ->implode(', ')
                                        }}

                                    </p>

                                    <a href="{{ route('logistics.pickup-requests') }}">
                                        Go to Pickup Requests →
                                    </a>

                                </div>

                            @else

                                <p class="pickup-dispatch-note">
                                    Choose one approved, active and online
                                    Rider for this entire Seller collection.
                                </p>

                            @endif

                            {{-- DISPATCH FORM --}}

                            <form
                                method="POST"
                                action="{{ route('logistics.delivery-assignment.batch-assign') }}"
                                data-pickup-dispatch-form
                                data-blocked="{{ $blocked ? 'true' : 'false' }}"
                            >

                                @csrf

                                {{-- ALL ORDERS IN THE GROUP --}}

                                @foreach($group['orders'] as $order)

                                    <input
                                        type="hidden"
                                        name="order_ids[]"
                                        value="{{ $order->id }}"
                                    >

                                @endforeach

                                {{-- RIDER SELECTION --}}

                                <label>

                                    <span class="pickup-label">
                                        Pickup Rider
                                    </span>

                                    <select
                                        name="rider_id"
                                        required
                                        data-rider-select
                                        @disabled(
                                            $blocked
                                            || $stats['available_riders'] === 0
                                        )
                                    >

                                        <option value="">

                                            {{
                                                $blocked
                                                    ? 'Verify entire group first'
                                                    : 'Select available Rider'
                                            }}

                                        </option>

                                        @foreach($riders as $rider)

                                            @php
                                                $busy = (bool) $rider
                                                    ->getAttribute('is_dispatch_busy');

                                                $riderName = trim(
                                                    $rider->first_name
                                                    . ' '
                                                    . $rider->last_name
                                                );

                                                $riderArea = collect([
                                                    $rider->barangay_name,
                                                    $rider->municipality_name,
                                                ])
                                                    ->filter()
                                                    ->implode(', ');

                                                $vehicle = collect([
                                                    $rider->vehicle_type,
                                                    $rider->vehicle_model,
                                                ])
                                                    ->filter()
                                                    ->implode(' · ');
                                            @endphp

                                            <option
                                                value="{{ $rider->id }}"
                                                data-rider-name="{{ $riderName }}"
                                                data-rider-area="{{ $riderArea }}"
                                                data-rider-vehicle="{{ $vehicle }}"
                                                data-rider-plate="{{ $rider->plate_number }}"
                                                @disabled($busy)
                                            >

                                                {{ $riderName }}

                                                @if($rider->vehicle_type)
                                                    — {{ $rider->vehicle_type }}
                                                @endif

                                                @if($rider->plate_number)
                                                    · {{ $rider->plate_number }}
                                                @endif

                                                @if($busy)
                                                    — Busy
                                                @endif

                                            </option>

                                        @endforeach

                                    </select>

                                </label>

                                {{-- SELECTED RIDER PREVIEW --}}

                                <div
                                    class="pickup-rider-preview"
                                    data-rider-preview
                                    hidden
                                >

                                    <strong data-rider-preview-name></strong>

                                    <p data-rider-preview-area></p>

                                    <p data-rider-preview-vehicle></p>

                                </div>

                                {{-- BATCH TOTAL --}}

                                <div class="pickup-dispatch-total">

                                    <span>
                                        Parcels to collect
                                    </span>

                                    <strong>
                                        {{ $group['order_count'] }}
                                    </strong>

                                </div>

                                {{-- ASSIGN BUTTON --}}

                                <button
                                    type="submit"
                                    class="pickup-button pickup-button-primary"
                                    @disabled(
                                        $blocked
                                        || $stats['available_riders'] === 0
                                    )
                                >

                                    {{
                                        $blocked
                                            ? 'Awaiting Complete Batch Verification'
                                            : 'Assign Entire Batch'
                                    }}

                                    @unless($blocked)
                                        <span aria-hidden="true">→</span>
                                    @endunless

                                </button>

                                <p class="pickup-dispatch-footnote">

                                    {{
                                        $blocked
                                            ? 'Dispatch is locked until all Seller group requests have been verified.'
                                            : 'The selected Rider receives all '
                                                . $group['order_count']
                                                . ' parcels from this Seller pickup stop.'
                                    }}

                                </p>

                            </form>

                            {{-- NO AVAILABLE RIDERS --}}

                            @if(
                                !$blocked
                                && $stats['available_riders'] === 0
                            )

                                <div
                                    class="pickup-alert pickup-alert-warning"
                                    style="margin-top:12px;margin-bottom:0"
                                >

                                    <strong>
                                        No Rider available
                                    </strong>

                                    @if($riders->isEmpty())
                                        <p>
                                            No approved, active and online Rider is available
                                            for this Logistics account or hub. Check Rider
                                            Management to review activation and availability.
                                        </p>
                                    @else
                                        <p>
                                            All eligible online Riders currently have
                                            active pickup or last-mile assignments.
                                        </p>
                                    @endif

                                    <a href="{{ route('logistics.rider-management') }}" class="pickup-text-link">
                                        Review Rider Management →
                                    </a>

                                </div>

                            @endif

                        </aside>

                    </div>

                </article>

            @endforeach

        </div>

        {{-- EMPTY ASSIGNMENT QUEUE --}}

        @if($pickupGroups->isEmpty())

            <div class="pickup-empty">

                <span
                    class="pickup-empty-icon"
                    aria-hidden="true"
                >
                    ✓
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
                    class="pickup-text-link"
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

        {{-- FOOTER --}}

        <footer class="pickup-list-footer">

            <span data-assignment-visible-count>
                {{ $pickupGroups->count() }}
                pickup batches shown
            </span>

            <span>

                {{ $stats['available_riders'] }}

                {{
                    $stats['available_riders'] === 1
                        ? 'Rider'
                        : 'Riders'
                }}

                available

            </span>

        </footer>

    </section>

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

            const tabs = [
                ...root.querySelectorAll('[data-assignment-filter]')
            ];

            const empty = root.querySelector(
                '[data-assignment-empty]'
            );

            const visibleCount = root.querySelector(
                '[data-assignment-visible-count]'
            );

            let filter = 'all';

            const normalize = value =>
                String(value || '').toLocaleLowerCase().trim();

            /*
            |--------------------------------------------------------------------------
            | SEARCH AND FILTERS
            |--------------------------------------------------------------------------
            */

            function applyFilters() {

                const keyword = normalize(search?.value);

                let shown = 0;

                batches.forEach(batch => {

                    batch.hidden = !(
                        (
                            filter === 'all'
                            || batch.dataset.batchType === filter
                        )
                        &&
                        normalize(batch.dataset.search).includes(keyword)
                    );

                    if (!batch.hidden) {
                        shown++;
                    }

                });

                tabs.forEach(tab => {

                    tab.setAttribute(
                        'aria-pressed',
                        String(
                            tab.dataset.assignmentFilter === filter
                        )
                    );

                });

                if (empty) {

                    empty.hidden =
                        batches.length === 0 || shown > 0;

                }

                if (visibleCount) {

                    visibleCount.textContent =
                        `${shown} of ${batches.length} pickup `
                        + `${batches.length === 1 ? 'batch' : 'batches'} shown`;

                }

            }

            /*
            |--------------------------------------------------------------------------
            | FILTER BUTTONS
            |--------------------------------------------------------------------------
            */

            tabs.forEach(tab => {

                tab.addEventListener('click', () => {

                    filter =
                        tab.dataset.assignmentFilter || 'all';

                    applyFilters();

                });

            });

            search?.addEventListener('input', applyFilters);

            root.querySelectorAll(
                '[data-assignment-clear]'
            ).forEach(button => {

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
            | RIDER PREVIEW
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-rider-select]'
            ).forEach(select => {

                const form = select.closest(
                    '[data-pickup-dispatch-form]'
                );

                const preview = form?.querySelector(
                    '[data-rider-preview]'
                );

                function render() {

                    const option =
                        select.options[select.selectedIndex];

                    if (!option?.value) {

                        if (preview) {
                            preview.hidden = true;
                        }

                        return;

                    }

                    form.querySelector(
                        '[data-rider-preview-name]'
                    ).textContent =
                        option.dataset.riderName
                        || option.textContent.trim();

                    form.querySelector(
                        '[data-rider-preview-area]'
                    ).textContent =
                        option.dataset.riderArea
                        || 'Service area not specified';

                    const vehicle =
                        option.dataset.riderVehicle
                        || 'Vehicle not specified';

                    form.querySelector(
                        '[data-rider-preview-vehicle]'
                    ).textContent =
                        option.dataset.riderPlate
                            ? `${vehicle} · ${option.dataset.riderPlate}`
                            : vehicle;

                    if (preview) {
                        preview.hidden = false;
                    }

                }

                select.addEventListener('change', render);

                render();

            });

            /*
            |--------------------------------------------------------------------------
            | DISPATCH VALIDATION
            |--------------------------------------------------------------------------
            */

            root.querySelectorAll(
                '[data-pickup-dispatch-form]'
            ).forEach(form => {

                form.addEventListener('submit', event => {

                    if (form.dataset.blocked === 'true') {

                        event.preventDefault();
                        return;

                    }

                    const select = form.querySelector(
                        '[data-rider-select]'
                    );

                    if (
                        !select?.value
                        || select.disabled
                    ) {

                        event.preventDefault();
                        return;

                    }

                    const riderName =
                        select.options[select.selectedIndex]
                            ?.dataset?.riderName
                        || 'this Rider';

                    const count = form.querySelectorAll(
                        'input[name="order_ids[]"]'
                    ).length;

                    const confirmed = window.confirm(
                        `Assign ${count} `
                        + `${count === 1 ? 'parcel' : 'parcels'} `
                        + `to ${riderName} for this Seller pickup?`
                    );

                    if (!confirmed) {

                        event.preventDefault();
                        return;

                    }

                    const submit = form.querySelector(
                        'button[type="submit"]'
                    );

                    if (submit) {

                        submit.disabled = true;
                        submit.textContent = 'Assigning batch…';

                    }

                });

            });

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

            applyFilters();

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
