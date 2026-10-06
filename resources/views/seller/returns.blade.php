@extends('layouts.seller')

@section('title', 'Returns & Refunds — SARI Seller')

@section('page-title', 'Returns & Refunds')

@push('styles')

<style id="sariSellerReturnsProfessionalStyles">

    .seller-returns-page {

        --returns-gold: #d59617;

        --returns-gold-hover: #c9890f;

        --returns-gold-soft: #fffaf0;

        --returns-ink: #111827;

        --returns-text: #344054;

        --returns-muted: #7c8592;

        --returns-soft-muted: #98a2b3;

        --returns-line: #e5e7eb;

        --returns-surface: #ffffff;

        width: 100%;

        max-width: 1440px;

        margin-inline: auto;

        padding-bottom: 24px;

        color: var(--returns-ink);

        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;

    }

    .seller-returns-page *,

    .seller-returns-page *::before,

    .seller-returns-page *::after {

        box-sizing: border-box;

    }

    .seller-returns-page button,

    .seller-returns-page input,

    .seller-returns-page select,

    .seller-returns-page a {

        font-family: inherit;

    }

    /* ------------------------------------------------------------

       Alerts

       ------------------------------------------------------------ */

    .seller-returns-alert {

        display: flex;

        align-items: flex-start;

        gap: 9px;

        margin-bottom: 10px;

        border: 1px solid;

        border-radius: 12px;

        padding: 9px 11px;

        font-size: 9px;

        font-weight: 500;

        line-height: 1.5;

    }

    .seller-returns-alert--success {

        border-color: #d5e7dc;

        background: #f5faf7;

        color: #4f7d63;

    }

    .seller-returns-alert--error {

        border-color: #efdada;

        background: #fff7f7;

        color: #a65353;

    }

    .seller-returns-alert-icon {

        display: grid;

        width: 27px;

        height: 27px;

        flex: 0 0 27px;

        place-items: center;

        border: 1px solid currentColor;

        border-radius: 8px;

        background: #fff;

        opacity: .82;

    }

    .seller-returns-alert-icon svg {

        width: 13px;

        height: 13px;

    }

    /* ------------------------------------------------------------

       Header — same visual system as recent Seller pages

       ------------------------------------------------------------ */

    .seller-returns-header {

        min-height: 66px;

        padding: 2px 2px 13px;

        border-bottom: 1px solid #e7ebf0;

    }

    .seller-returns-eyebrow {

        margin: 0;

        color: #b87605;

        font-size: 8px;

        font-weight: 800;

        line-height: 1;

        letter-spacing: .16em;

        text-transform: uppercase;

    }

    .seller-returns-title {

        margin: 5px 0 0;

        color: var(--returns-ink);

        font-size: clamp(29px, 2.2vw, 36px);

        font-weight: 650;

        line-height: 1;

        letter-spacing: -.035em;

    }

    .seller-returns-title span:last-child {

        margin-left: .18em;

        color: #C9890B;

    }

    .seller-returns-subtitle {

        max-width: 760px;

        margin: 7px 0 0;

        color: var(--returns-muted);

        font-size: 10px;

        line-height: 1.5;

    }

    /* ------------------------------------------------------------

       Summary

       ------------------------------------------------------------ */

    .seller-returns-summary {

        display: grid;

        grid-template-columns: repeat(4, minmax(0, 1fr));

        gap: 9px;

        margin-top: 12px;

    }

    .seller-returns-stat {

        min-width: 0;

        min-height: 78px;

        border: 1px solid var(--returns-line);

        border-radius: 13px;

        background: #fff;

        padding: 10px 12px;

        box-shadow: 0 5px 16px rgba(15, 23, 42, .028);

    }

    .seller-returns-stat-inner {

        display: flex;

        height: 100%;

        align-items: flex-start;

        justify-content: space-between;

        gap: 10px;

    }

    .seller-returns-stat-label {

        color: #667085;

        font-size: 9.5px;

        font-weight: 600;

        line-height: 1.3;

    }

    .seller-returns-stat-value {

        margin-top: 3px;

        color: var(--returns-ink);

        font-size: 21px;

        font-weight: 700;

        line-height: 1;

        letter-spacing: -.035em;

    }

    .seller-returns-stat-note {

        margin-top: 5px;

        color: var(--returns-soft-muted);

        font-size: 8.4px;

        line-height: 1.35;

    }

    .seller-returns-stat-icon {

        display: grid;

        width: 32px;

        height: 32px;

        flex: 0 0 32px;

        place-items: center;

        border: 1px solid rgba(17, 24, 39, .05);

        border-radius: 9px;

    }

    .seller-returns-stat-icon svg {

        width: 14px;

        height: 14px;

    }

    .seller-returns-stat-icon--all {

        background: #f4f7fb;

        color: #587897;

    }

    .seller-returns-stat-icon--requested {

        background: #fff8e9;

        color: #b97805;

    }

    .seller-returns-stat-icon--approved {

        background: #f2f8fc;

        color: #4d7b9c;

    }

    .seller-returns-stat-icon--refunded {

        background: #f1f8f4;

        color: #4f7d63;

    }

    /* ------------------------------------------------------------

       Search + premium status filter

       ------------------------------------------------------------ */

    .seller-returns-filter-panel {

        margin-top: 10px;

        border: 1px solid var(--returns-line);

        border-radius: 14px;

        background: #f8fafc;

        padding: 9px;

        box-shadow: 0 5px 16px rgba(15, 23, 42, .028);

    }

    .seller-returns-filter-form {

        display: grid;

        grid-template-columns: minmax(320px, 1fr) 190px 118px 66px;

        gap: 8px;

        align-items: center;

    }

    .seller-returns-search-field {

        position: relative;

        min-width: 0;

    }

    .seller-returns-search-field > svg {

        position: absolute;

        left: 12px;

        top: 50%;

        width: 14px;

        height: 14px;

        transform: translateY(-50%);

        color: #98a2b3;

        pointer-events: none;

    }

    .seller-returns-search-input,

    .seller-returns-apply-filter,

    .seller-returns-reset-filter,

    .seller-returns-status-button {

        width: 100%;

        height: 40px;

        min-height: 40px;

        border-radius: 9px;

        font-family: inherit;

        font-size: 9.8px;

    }

    .seller-returns-search-input {

        border: 1px solid #d8dee6;

        background: #fff;

        padding: 0 11px 0 36px;

        color: #344054;

        outline: none;

        box-shadow: 0 1px 2px rgba(15,23,42,.02);

        transition: border-color .14s ease, box-shadow .14s ease;

    }

    .seller-returns-search-input::placeholder {

        color: #98a2b3;

        font-weight: 400;

    }

    .seller-returns-search-input:focus {

        border-color: var(--returns-gold);

        box-shadow: 0 0 0 3px rgba(213,150,23,.08);

    }

    .seller-returns-apply-filter,

    .seller-returns-reset-filter {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 6px;

        text-decoration: none;

        font-weight: 700;

        transition: border-color .14s ease, background-color .14s ease, color .14s ease;

    }

    .seller-returns-apply-filter {

        border: 1px solid var(--returns-gold);

        background: var(--returns-gold);

        padding: 0 11px;

        color: #fff;

        box-shadow: 0 6px 14px rgba(213,150,23,.13);

        cursor: pointer;

    }

    .seller-returns-apply-filter:hover,

    .seller-returns-apply-filter:focus-visible {

        outline: none;

        border-color: var(--returns-gold-hover);

        background: var(--returns-gold-hover);

    }

    .seller-returns-apply-filter svg {

        width: 12px;

        height: 12px;

    }

    .seller-returns-reset-filter {

        border: 1px solid #d8dee6;

        background: #fff;

        padding: 0 10px;

        color: #667085;

    }

    .seller-returns-reset-filter:hover,

    .seller-returns-reset-filter:focus-visible {

        outline: none;

        border-color: #cfd6df;

        background: #fff;

        color: #344054;

    }

    .seller-returns-status-dropdown {

        position: relative;

        min-width: 0;

    }

    .seller-returns-status-native {

        position: absolute !important;

        width: 1px !important;

        height: 1px !important;

        overflow: hidden !important;

        clip: rect(0 0 0 0) !important;

        clip-path: inset(50%) !important;

        white-space: nowrap !important;

        border: 0 !important;

        padding: 0 !important;

        margin: -1px !important;

    }

    .seller-returns-status-button {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 9px;

        border: 1px solid #d8dee6;

        background: #fff;

        padding: 0 10px 0 8px;

        color: #344054;

        font-weight: 650;

        text-align: left;

        cursor: pointer;

        outline: none;

        box-shadow: 0 1px 2px rgba(15,23,42,.02);

        transition: border-color .14s ease, box-shadow .14s ease, background-color .14s ease;

    }

    .seller-returns-status-button:hover {

        border-color: #cfd6df;

        background: #fdfefe;

    }

    .seller-returns-status-button:focus-visible,

    .seller-returns-status-button[aria-expanded="true"] {

        border-color: var(--returns-gold);

        background: #fff;

        box-shadow: 0 0 0 3px rgba(213,150,23,.08);

    }

    .seller-returns-status-button-main {

        display: inline-flex;

        min-width: 0;

        align-items: center;

        gap: 7px;

    }

    .seller-returns-status-button-icon {

        display: grid;

        width: 24px;

        height: 24px;

        flex: 0 0 24px;

        place-items: center;

        border: 1px solid #efe3c7;

        border-radius: 7px;

        background: #fffaf0;

        color: #b97805;

    }

    .seller-returns-status-button-icon svg {

        width: 12px;

        height: 12px;

    }

    .seller-returns-status-chevron {

        width: 13px;

        height: 13px;

        flex: 0 0 auto;

        color: #98a2b3;

        transition: transform .15s ease, color .15s ease;

    }

    .seller-returns-status-button[aria-expanded="true"] .seller-returns-status-chevron {

        transform: rotate(180deg);

        color: #b97805;

    }

    .seller-returns-status-menu {

        position: absolute;

        top: calc(100% + 6px);

        left: 0;

        right: 0;

        z-index: 90;

        min-width: 235px;

        overflow: hidden;

        border: 1px solid #e2e7ed;

        border-radius: 12px;

        background: rgba(255,255,255,.985);

        padding: 5px;

        box-shadow: 0 18px 40px rgba(15,23,42,.12), 0 3px 9px rgba(15,23,42,.045);

        backdrop-filter: blur(10px);

        -webkit-backdrop-filter: blur(10px);

    }

    .seller-returns-status-menu[hidden] {

        display: none !important;

    }

    .seller-returns-status-option {

        display: flex;

        width: 100%;

        min-height: 43px;

        align-items: center;

        justify-content: space-between;

        gap: 10px;

        border: 0;

        border-radius: 9px;

        background: transparent;

        padding: 7px 8px 7px 10px;

        color: #344054;

        font-family: inherit;

        text-align: left;

        cursor: pointer;

        transition: background-color .12s ease, color .12s ease;

    }

    .seller-returns-status-option:hover,

    .seller-returns-status-option:focus-visible {

        outline: none;

        background: #f8fafc;

    }

    .seller-returns-status-option.is-selected {

        background: #fff9ec;

    }

    .seller-returns-status-option-copy strong {

        display: block;

        color: #344054;

        font-size: 9.3px;

        font-weight: 700;

        line-height: 1.3;

    }

    .seller-returns-status-option.is-selected .seller-returns-status-option-copy strong {

        color: #9b6505;

    }

    .seller-returns-status-option-copy small {

        display: block;

        margin-top: 2px;

        color: #98a2b3;

        font-size: 7.8px;

        font-weight: 400;

        line-height: 1.35;

    }

    .seller-returns-status-check {

        width: 14px;

        height: 14px;

        flex: 0 0 auto;

        color: #c9890f;

        opacity: 0;

    }

    .seller-returns-status-option.is-selected .seller-returns-status-check {

        opacity: 1;

    }

    /* ------------------------------------------------------------

       Return request list

       ------------------------------------------------------------ */

    .seller-returns-list {

        display: grid;

        gap: 9px;

        margin-top: 10px;

    }

    .seller-return-card {

        overflow: hidden;

        border: 1px solid var(--returns-line);

        border-radius: 13px;

        background: #fff;

        box-shadow: 0 5px 16px rgba(15, 23, 42, .026);

    }

    .seller-return-card-main {

        display: grid;

        grid-template-columns: minmax(0, 1fr) auto;

        gap: 16px;

        align-items: start;

        padding: 12px 13px;

    }

    .seller-return-order-row {

        display: flex;

        min-width: 0;

        flex-wrap: wrap;

        align-items: center;

        gap: 7px;

    }

    .seller-return-order-number {

        margin: 0;

        color: #1f2937;

        font-size: 10.5px;

        font-weight: 700;

        line-height: 1.35;

    }

    .seller-return-status {

        display: inline-flex;

        min-height: 22px;

        align-items: center;

        border: 1px solid;

        border-radius: 999px;

        padding: 0 8px;

        font-size: 7.8px;

        font-weight: 700;

        line-height: 1;

        text-transform: uppercase;

    }

    .seller-return-status--requested {

        border-color: #eadfc9;

        background: #fff9ef;

        color: #a8731f;

    }

    .seller-return-status--approved {

        border-color: #d3e1ec;

        background: #f3f8fc;

        color: #4d7393;

    }

    .seller-return-status--returned {

        border-color: #d9d8ec;

        background: #f6f5fb;

        color: #64638d;

    }

    .seller-return-status--refunded {

        border-color: #cfe4d7;

        background: #f1f8f4;

        color: #4f7d63;

    }

    .seller-return-status--rejected {

        border-color: #efcece;

        background: #fff5f5;

        color: #a65353;

    }

    .seller-return-reason {

        margin: 6px 0 0;

        color: #344054;

        font-size: 9.5px;

        font-weight: 600;

        line-height: 1.45;

    }

    .seller-return-details {

        max-width: 820px;

        margin: 3px 0 0;

        color: var(--returns-muted);

        font-size: 8.7px;

        line-height: 1.5;

    }

    .seller-return-amount {

        min-width: 140px;

        text-align: right;

    }

    .seller-return-amount-label {

        color: var(--returns-soft-muted);

        font-size: 8px;

        font-weight: 500;

    }

    .seller-return-amount-value {

        margin-top: 3px;

        color: #1f2937;

        font-size: 16px;

        font-weight: 750;

        line-height: 1;

        letter-spacing: -.025em;

    }

    /* ------------------------------------------------------------

       Action areas

       ------------------------------------------------------------ */

    .seller-return-action {

        border-top: 1px solid #edf0f3;

        background: #fcfcfd;

        padding: 10px 13px;

    }

    .seller-return-review-form {

        display: grid;

        grid-template-columns: minmax(260px, 1fr) 145px 145px 126px;

        gap: 8px;

        align-items: center;

    }

    .seller-return-input,

    .seller-return-select,

    .seller-return-button {

        height: 40px;

        min-height: 40px;

        border-radius: 9px;

        font-family: inherit;

        font-size: 9.5px;

    }

    .seller-return-input,

    .seller-return-select {

        width: 100%;

        border: 1px solid #d8dee6;

        background: #fff;

        color: #344054;

        outline: none;

        box-shadow: none;

        transition: border-color .14s ease, box-shadow .14s ease;

    }

    .seller-return-input {

        padding-inline: 11px;

    }

    .seller-return-input::placeholder {

        color: #98a2b3;

    }

    .seller-return-select {

        padding-inline: 10px;

        font-weight: 600;

    }

    .seller-return-input:focus,

    .seller-return-select:focus {

        border-color: var(--returns-gold);

        box-shadow: 0 0 0 3px rgba(213, 150, 23, .08);

    }

    .seller-return-button {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 6px;

        border: 1px solid;

        padding: 0 12px;

        font-weight: 700;

        cursor: pointer;

        transition: border-color .14s ease, background-color .14s ease, color .14s ease;

    }

    .seller-return-button svg {

        width: 13px;

        height: 13px;

    }

    .seller-return-button--primary {

        border-color: var(--returns-gold);

        background: var(--returns-gold);

        color: #fff;

        box-shadow: 0 6px 14px rgba(213, 150, 23, .13);

    }

    .seller-return-button--primary:hover,

    .seller-return-button--primary:focus-visible {

        outline: none;

        border-color: var(--returns-gold-hover);

        background: var(--returns-gold-hover);

    }

    .seller-return-button--success {

        border-color: #56856a;

        background: #56856a;

        color: #fff;

        box-shadow: 0 6px 14px rgba(86, 133, 106, .12);

    }

    .seller-return-button--success:hover,

    .seller-return-button--success:focus-visible {

        outline: none;

        border-color: #467257;

        background: #467257;

    }

    .seller-return-action-note {

        margin: 5px 0 0;

        color: var(--returns-soft-muted);

        font-size: 8px;

        line-height: 1.45;

    }

    .seller-return-response {

        border: 1px solid #edf0f3;

        border-radius: 10px;

        background: #f8fafc;

        padding: 9px 10px;

        color: #667085;

        font-size: 8.7px;

        line-height: 1.5;

    }

    /* ------------------------------------------------------------

       Empty + pagination

       ------------------------------------------------------------ */

    .seller-returns-empty {

        display: grid;

        min-height: 150px;

        place-items: center;

        border: 1px solid var(--returns-line);

        border-radius: 13px;

        background: #fff;

        padding: 26px 18px;

        text-align: center;

    }

    .seller-returns-empty-icon {

        display: grid;

        width: 38px;

        height: 38px;

        margin-inline: auto;

        place-items: center;

        border: 1px solid #e5e7eb;

        border-radius: 10px;

        background: #fafbfc;

        color: #98a2b3;

    }

    .seller-returns-empty-icon svg {

        width: 17px;

        height: 17px;

    }

    .seller-returns-empty-title {

        margin-top: 9px;

        color: #344054;

        font-size: 10.5px;

        font-weight: 650;

    }

    .seller-returns-empty-copy {

        margin-top: 4px;

        color: var(--returns-soft-muted);

        font-size: 8.8px;

        line-height: 1.5;

    }

    .seller-returns-pagination {

        margin-top: 9px;

    }

    /* ------------------------------------------------------------

       Responsive

       ------------------------------------------------------------ */

    @media (max-width: 1100px) {

        .seller-return-review-form {

            grid-template-columns: minmax(0, 1fr) 135px 135px;

        }

        .seller-return-review-form .seller-return-button {

            grid-column: 1 / -1;

            width: max-content;

        }

    }

    @media (max-width: 900px) {

        .seller-returns-summary {

            grid-template-columns: repeat(2, minmax(0, 1fr));

        }

        .seller-return-card-main {

            grid-template-columns: 1fr;

        }

        .seller-return-amount {

            min-width: 0;

            text-align: left;

        }

    }

    @media (max-width: 700px) {

        .seller-returns-title {

            font-size: clamp(28px, 8vw, 34px);

        }

        .seller-return-review-form {

            grid-template-columns: 1fr;

        }

        .seller-return-review-form .seller-return-button {

            grid-column: auto;

            width: 100%;

        }

    }

    @media (max-width: 520px) {

        .seller-returns-summary {

            grid-template-columns: 1fr;

        }

        .seller-returns-stat {

            min-height: 74px;

        }

        .seller-return-card-main,

        .seller-return-action {

            padding-left: 10px;

            padding-right: 10px;

        }

        .seller-return-input,

        .seller-return-select,

        .seller-return-button {

            height: 42px;

            min-height: 42px;

            font-size: 10px;

        }

    }

    @media (max-width: 1100px) {

        .seller-returns-filter-form {

            grid-template-columns: minmax(0,1fr) 170px 112px 62px;

        }

    }

    @media (max-width: 820px) {

        .seller-returns-filter-form {

            grid-template-columns: minmax(0,1fr) minmax(170px,.45fr);

        }

        .seller-returns-apply-filter,

        .seller-returns-reset-filter {

            width: 100%;

        }

    }

    @media (max-width: 640px) {

        .seller-returns-filter-panel {

            padding: 8px;

            border-radius: 13px;

        }

        .seller-returns-filter-form {

            grid-template-columns: 1fr;

            gap: 7px;

        }

        .seller-returns-search-input,

        .seller-returns-status-button,

        .seller-returns-apply-filter,

        .seller-returns-reset-filter {

            height: 42px;

            min-height: 42px;

            font-size: 10px;

        }

        .seller-returns-status-menu {

            min-width: 100%;

        }

    }

    @media (prefers-reduced-motion: reduce) {

        .seller-returns-page * {

            transition-duration: .01ms !important;

            scroll-behavior: auto !important;

        }

    }

/* ======================================================================
   RETURNS & REFUNDS — DASHBOARD-STYLE FLOATING CONTAINERS
   Visual-only enhancement. No return workflow, forms, routes, CSRF,
   refund ledger behavior, filtering, pagination, database, or backend
   logic changed.
   ====================================================================== */

.seller-returns-page {
    --returns-float-border: #E5E7EB;
    --returns-float-border-strong: #D1D5DB;
    --returns-float-divider: #ECEFF2;

    --returns-float-shadow:
        0 1px 2px rgba(15, 23, 42, .035),
        0 10px 26px rgba(15, 23, 42, .075),
        inset 0 1px 0 rgba(255, 255, 255, .86);

    --returns-float-shadow-strong:
        0 1px 2px rgba(15, 23, 42, .04),
        0 14px 34px rgba(15, 23, 42, .085),
        inset 0 1px 0 rgba(255, 255, 255, .88);

    --returns-float-shadow-soft:
        0 1px 2px rgba(15, 23, 42, .025),
        0 6px 16px rgba(15, 23, 42, .05),
        inset 0 1px 0 rgba(255, 255, 255, .82);
}

/* Header stays flat/document-like; only divider becomes neutral. */
.seller-returns-page .seller-returns-header {
    border-bottom-color: var(--returns-float-divider) !important;
}

/* ------------------------------------------------------------------
   MAIN FLOATING SURFACES
   ------------------------------------------------------------------ */
.seller-returns-page .seller-returns-stat,
.seller-returns-page .seller-returns-filter-panel,
.seller-returns-page .seller-return-card,
.seller-returns-page .seller-returns-empty {
    border-color: var(--returns-float-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--returns-float-shadow) !important;
    transform: none !important;
}

/* The filter bar and return cards are the primary work surfaces. */
.seller-returns-page .seller-returns-filter-panel,
.seller-returns-page .seller-return-card {
    box-shadow: var(--returns-float-shadow-strong) !important;
}

/* Keep the existing compact geometry while making surfaces consistent. */
.seller-returns-page .seller-returns-stat,
.seller-returns-page .seller-return-card,
.seller-returns-page .seller-returns-empty {
    border-radius: 14px !important;
}

.seller-returns-page .seller-returns-filter-panel {
    border-radius: 14px !important;
    background: #FFFFFF !important;
}

/* Static elevation only — no container movement on hover. */
.seller-returns-page .seller-returns-stat:hover,
.seller-returns-page .seller-returns-filter-panel:hover,
.seller-returns-page .seller-return-card:hover,
.seller-returns-page .seller-returns-empty:hover {
    border-color: var(--returns-float-border) !important;
    transform: none !important;
}

.seller-returns-page .seller-returns-stat:hover,
.seller-returns-page .seller-returns-empty:hover {
    box-shadow: var(--returns-float-shadow) !important;
}

.seller-returns-page .seller-returns-filter-panel:hover,
.seller-returns-page .seller-return-card:hover {
    box-shadow: var(--returns-float-shadow-strong) !important;
}

/* ------------------------------------------------------------------
   RETURN CARD INTERNAL HIERARCHY
   ------------------------------------------------------------------ */

/* Action area should feel attached to the parent card, not like a cream
   footer. Use a very light neutral section instead. */
.seller-returns-page .seller-return-action {
    border-top-color: var(--returns-float-divider) !important;
    background: #FAFBFC !important;
}

/* Seller response is a secondary floating information surface. */
.seller-returns-page .seller-return-response {
    border-color: #E7EAEE !important;
    background: #FFFFFF !important;
    box-shadow: var(--returns-float-shadow-soft) !important;
}

/* Review controls stay white and neutral. */
.seller-returns-page .seller-return-input,
.seller-returns-page .seller-return-select {
    border-color: var(--returns-float-border-strong) !important;
    background: #FFFFFF !important;
    box-shadow:
        0 1px 2px rgba(15, 23, 42, .02),
        0 4px 10px rgba(15, 23, 42, .03) !important;
}

.seller-returns-page .seller-return-input:hover,
.seller-returns-page .seller-return-select:hover {
    border-color: #C9D0D9 !important;
    background: #FFFFFF !important;
}

.seller-returns-page .seller-return-input:focus,
.seller-returns-page .seller-return-select:focus {
    border-color: #C6A451 !important;
    background: #FFFFFF !important;
    box-shadow:
        0 0 0 3px rgba(201, 164, 81, .08),
        0 4px 10px rgba(15, 23, 42, .03) !important;
}

/* ------------------------------------------------------------------
   FILTER SURFACE + CUSTOM STATUS DROPDOWN
   ------------------------------------------------------------------ */
.seller-returns-page .seller-returns-search-input,
.seller-returns-page .seller-returns-status-button {
    border-color: var(--returns-float-border-strong) !important;
    background: #FFFFFF !important;
    box-shadow:
        0 1px 2px rgba(15, 23, 42, .02),
        0 4px 11px rgba(15, 23, 42, .035) !important;
    transform: none !important;
}

.seller-returns-page .seller-returns-search-input:hover,
.seller-returns-page .seller-returns-status-button:hover {
    border-color: #C9D0D9 !important;
    background: #FFFFFF !important;
    transform: none !important;
}

.seller-returns-page .seller-returns-search-input:focus,
.seller-returns-page .seller-returns-status-button:focus-visible,
.seller-returns-page .seller-returns-status-button[aria-expanded="true"] {
    border-color: #C6A451 !important;
    background: #FFFFFF !important;
    box-shadow:
        0 0 0 3px rgba(201, 164, 81, .08),
        0 5px 14px rgba(15, 23, 42, .04) !important;
}

/* Remove the cream structural shell around the filter icon while keeping
   the gold icon itself as the brand cue. */
.seller-returns-page .seller-returns-status-button-icon {
    border-color: var(--returns-float-border) !important;
    background: #FFFFFF !important;
}

/* Premium floating dropdown popover. */
.seller-returns-page .seller-returns-status-menu {
    border-color: var(--returns-float-border) !important;
    background: #FFFFFF !important;
    box-shadow:
        0 18px 42px rgba(15, 23, 42, .12),
        0 4px 12px rgba(15, 23, 42, .04) !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}

.seller-returns-page .seller-returns-status-option {
    transform: none !important;
}

.seller-returns-page .seller-returns-status-option:hover,
.seller-returns-page .seller-returns-status-option:focus-visible {
    background: #F8FAFC !important;
    transform: none !important;
}

.seller-returns-page .seller-returns-status-option.is-selected {
    background: #FFF9EC !important;
}

/* ------------------------------------------------------------------
   ALERTS + EMPTY STATES
   ------------------------------------------------------------------ */

/* Alerts keep their semantic colors, but gain the same restrained depth. */
.seller-returns-page .seller-returns-alert {
    box-shadow: var(--returns-float-shadow-soft) !important;
    transform: none !important;
}

.seller-returns-page .seller-returns-empty-icon {
    border-color: var(--returns-float-border) !important;
    background: #F9FAFB !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, .035) !important;
}

/* ------------------------------------------------------------------
   BUTTONS + PAGINATION
   ------------------------------------------------------------------ */

/* Secondary reset control stays neutral. */
.seller-returns-page .seller-returns-reset-filter {
    border-color: var(--returns-float-border) !important;
    background: #FFFFFF !important;
}

.seller-returns-page .seller-returns-reset-filter:hover,
.seller-returns-page .seller-returns-reset-filter:focus-visible {
    border-color: var(--returns-float-border-strong) !important;
    background: #F8FAFC !important;
}

/* Keep buttons clickable but physically static. */
.seller-returns-page .seller-returns-apply-filter,
.seller-returns-page .seller-returns-reset-filter,
.seller-returns-page .seller-return-button {
    transform: none !important;
}

.seller-returns-page .seller-returns-apply-filter:hover,
.seller-returns-page .seller-returns-reset-filter:hover,
.seller-returns-page .seller-return-button:hover,
.seller-returns-page .seller-return-button:focus-visible {
    transform: none !important;
}

/* Laravel paginator follows the same neutral border language. */
.seller-returns-page .seller-returns-pagination nav a,
.seller-returns-page .seller-returns-pagination nav span {
    border-color: var(--returns-float-border) !important;
}

/* No colored structural outlines. Semantic status badges retain their
   original colors because they communicate workflow state. */
.seller-returns-page .seller-returns-stat,
.seller-returns-page .seller-returns-filter-panel,
.seller-returns-page .seller-return-card,
.seller-returns-page .seller-return-response,
.seller-returns-page .seller-returns-empty {
    outline: 0 !important;
}

/* ------------------------------------------------------------------
   MOBILE — softer elevation for stacked surfaces
   ------------------------------------------------------------------ */
@media (max-width: 640px) {
    .seller-returns-page .seller-returns-stat,
    .seller-returns-page .seller-returns-filter-panel,
    .seller-returns-page .seller-return-card,
    .seller-returns-page .seller-returns-empty {
        box-shadow:
            0 1px 2px rgba(15, 23, 42, .03),
            0 7px 18px rgba(15, 23, 42, .06),
            inset 0 1px 0 rgba(255, 255, 255, .84) !important;
    }

    .seller-returns-page .seller-return-response {
        box-shadow:
            0 1px 2px rgba(15, 23, 42, .02),
            0 5px 13px rgba(15, 23, 42, .045) !important;
    }
}


/* ======================================================================
   RETURNS & REFUNDS — DASHBOARD SHADOW REFERENCE FINAL PASS
   Matches the stronger floating depth of the Seller dashboard while
   keeping every structural color neutral. CSS-only.
   ====================================================================== */

.seller-returns-page {
    --returns-dashboard-border: #E5E7EB;
    --returns-dashboard-divider: #ECEFF2;

    /* Dashboard-style layered elevation:
       1) contact shadow
       2) middle separation
       3) broad ambient float */
    --returns-dashboard-shadow:
        0 2px 5px rgba(15, 23, 42, .045),
        0 10px 24px rgba(15, 23, 42, .075),
        0 24px 54px rgba(15, 23, 42, .095);

    --returns-dashboard-shadow-soft:
        0 1px 3px rgba(15, 23, 42, .035),
        0 7px 18px rgba(15, 23, 42, .06),
        0 15px 32px rgba(15, 23, 42, .065);

    --returns-dashboard-shadow-inner:
        0 1px 2px rgba(15, 23, 42, .025),
        0 5px 14px rgba(15, 23, 42, .045);
}

/* Summary cards — floating but slightly lighter than work surfaces. */
.seller-returns-page .seller-returns-stat {
    border: 1px solid var(--returns-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--returns-dashboard-shadow-soft) !important;
    transform: none !important;
}

/* Primary containers — strongest dashboard depth. */
.seller-returns-page .seller-returns-filter-panel,
.seller-returns-page .seller-return-card,
.seller-returns-page .seller-returns-empty {
    border: 1px solid var(--returns-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--returns-dashboard-shadow) !important;
    transform: none !important;
}

/* No lift on hover — depth remains exactly the same. */
.seller-returns-page .seller-returns-stat:hover {
    border-color: var(--returns-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--returns-dashboard-shadow-soft) !important;
    transform: none !important;
}

.seller-returns-page .seller-returns-filter-panel:hover,
.seller-returns-page .seller-return-card:hover,
.seller-returns-page .seller-returns-empty:hover {
    border-color: var(--returns-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--returns-dashboard-shadow) !important;
    transform: none !important;
}

/* Inner areas keep hierarchy without competing with the parent card. */
.seller-returns-page .seller-return-response {
    border-color: #E7EAEE !important;
    background: #FFFFFF !important;
    box-shadow: var(--returns-dashboard-shadow-inner) !important;
    transform: none !important;
}

.seller-returns-page .seller-return-action {
    border-top-color: var(--returns-dashboard-divider) !important;
    background: #FAFBFC !important;
    box-shadow: none !important;
}

/* Search/filter controls — slight depth only. */
.seller-returns-page .seller-returns-search-input,
.seller-returns-page .seller-returns-status-button,
.seller-returns-page .seller-returns-reset-filter {
    border-color: #D9DEE5 !important;
    background: #FFFFFF !important;
    box-shadow:
        0 1px 2px rgba(15, 23, 42, .025),
        0 5px 12px rgba(15, 23, 42, .04) !important;
}

/* Dropdown menu floats one level above the filter card. */
.seller-returns-page .seller-returns-status-menu {
    border-color: #E1E5EA !important;
    background: #FFFFFF !important;
    box-shadow:
        0 4px 10px rgba(15, 23, 42, .05),
        0 18px 42px rgba(15, 23, 42, .14),
        0 32px 70px rgba(15, 23, 42, .08) !important;
}

/* Alerts follow the same floating language while keeping semantic tones. */
.seller-returns-page .seller-returns-alert {
    box-shadow: var(--returns-dashboard-shadow-soft) !important;
    transform: none !important;
}

/* Neutral outlines only. */
.seller-returns-page .seller-returns-stat,
.seller-returns-page .seller-returns-filter-panel,
.seller-returns-page .seller-return-card,
.seller-returns-page .seller-returns-empty,
.seller-returns-page .seller-return-response {
    outline: 0 !important;
}

/* Mobile: preserve float without making stacked cards feel heavy. */
@media (max-width: 640px) {
    .seller-returns-page .seller-returns-stat {
        box-shadow:
            0 1px 3px rgba(15, 23, 42, .03),
            0 6px 14px rgba(15, 23, 42, .05),
            0 12px 26px rgba(15, 23, 42, .055) !important;
    }

    .seller-returns-page .seller-returns-filter-panel,
    .seller-returns-page .seller-return-card,
    .seller-returns-page .seller-returns-empty {
        box-shadow:
            0 2px 4px rgba(15, 23, 42, .035),
            0 8px 18px rgba(15, 23, 42, .06),
            0 18px 38px rgba(15, 23, 42, .07) !important;
    }
}
</style>

@endpush

@section('content')

@php

    $returnStatusClass = fn (?string $value) => match ($value) {

        'requested' => 'seller-return-status--requested',

        'approved' => 'seller-return-status--approved',

        'returned' => 'seller-return-status--returned',

        'refunded' => 'seller-return-status--refunded',

        'rejected' => 'seller-return-status--rejected',

        default => 'seller-return-status--requested',

    };

    $summaryCards = [

        [

            'label' => 'All Returns',

            'value' => (int) $stats->total,

            'note' => 'Complete return history',

            'icon' => 'all',

        ],

        [

            'label' => 'Requested',

            'value' => (int) $stats->requested,

            'note' => 'Waiting for Seller review',

            'icon' => 'requested',

        ],

        [

            'label' => 'Approved',

            'value' => (int) $stats->approved,

            'note' => 'Authorized for return',

            'icon' => 'approved',

        ],

        [

            'label' => 'Refunded',

            'value' => (int) $stats->refunded,

            'note' => 'Refund ledger recorded',

            'icon' => 'refunded',

        ],

    ];

    $statusTabs = [

        'all' => 'All',

        'requested' => 'Requested',

        'approved' => 'Approved',

        'returned' => 'Returned',

        'refunded' => 'Refunded',

        'rejected' => 'Rejected',

    ];

@endphp

<div class="seller-returns-page">

    @if(session('success'))

        <div class="seller-returns-alert seller-returns-alert--success">

            <span class="seller-returns-alert-icon" aria-hidden="true">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">

                    <circle cx="12" cy="12" r="8"></circle>

                    <path d="m8 12 2.5 2.5L16 9"></path>

                </svg>

            </span>

            <div>{{ session('success') }}</div>

        </div>

    @endif

    @if($errors->any())

        <div class="seller-returns-alert seller-returns-alert--error">

            <span class="seller-returns-alert-icon" aria-hidden="true">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">

                    <path d="M12 3 3 20h18L12 3Z"></path>

                    <path d="M12 9v5"></path>

                    <path d="M12 17h.01"></path>

                </svg>

            </span>

            <div>{{ $errors->first() }}</div>

        </div>

    @endif

    <header class="seller-returns-header">

        <p class="seller-returns-eyebrow">Orders & Fulfillment</p>

        <h1 class="seller-returns-title">

            <span>Returns</span><span>& Refunds</span>

        </h1>

        <p class="seller-returns-subtitle">

            Review Buyer return requests, record returned parcels, and post internal refund ledger entries.

        </p>

    </header>

    <section class="seller-returns-summary" aria-label="Return statistics">

        @foreach($summaryCards as $card)

            <article class="seller-returns-stat">

                <div class="seller-returns-stat-inner">

                    <div class="min-w-0">

                        <div class="seller-returns-stat-label">{{ $card['label'] }}</div>

                        <div class="seller-returns-stat-value">{{ number_format($card['value']) }}</div>

                        <div class="seller-returns-stat-note">{{ $card['note'] }}</div>

                    </div>

                    <span class="seller-returns-stat-icon seller-returns-stat-icon--{{ $card['icon'] }}" aria-hidden="true">

                        @if($card['icon'] === 'all')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <path d="M5 5h14v14H5z"></path>

                                <path d="M8 9h8"></path>

                                <path d="M8 13h8"></path>

                            </svg>

                        @elseif($card['icon'] === 'requested')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <circle cx="12" cy="12" r="8"></circle>

                                <path d="M12 8v4l3 2"></path>

                            </svg>

                        @elseif($card['icon'] === 'approved')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <path d="M5 5h14v14H5z"></path>

                                <path d="m8 12 2.5 2.5L16 9"></path>

                            </svg>

                        @else

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <circle cx="12" cy="12" r="8"></circle>

                                <path d="m8 12 2.5 2.5L16 9"></path>

                            </svg>

                        @endif

                    </span>

                </div>

            </article>

        @endforeach

    </section>

    <section class="seller-returns-filter-panel" aria-label="Return filters">

        <form method="GET" action="{{ route('seller.returns.index') }}" class="seller-returns-filter-form">

            <label class="seller-returns-search-field">

                <span class="sr-only">Search return requests</span>

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">

                    <circle cx="11" cy="11" r="7"></circle>

                    <path d="m20 20-4-4"></path>

                </svg>

                <input

                    type="search"

                    name="q"

                    value="{{ $search }}"

                    placeholder="Search order ID, buyer, reason..."

                    autocomplete="off"

                    class="seller-returns-search-input"

                >

            </label>

            <div class="seller-returns-status-dropdown" data-returns-status-dropdown>

                <select

                    name="status"

                    class="seller-returns-status-native"

                    aria-hidden="true"

                    tabindex="-1"

                    data-returns-status-native

                >

                    @foreach($statusTabs as $tab => $label)

                        <option value="{{ $tab }}" @selected($status === $tab)>{{ $label }} Status</option>

                    @endforeach

                </select>

                <button

                    type="button"

                    class="seller-returns-status-button"

                    aria-haspopup="listbox"

                    aria-expanded="false"

                    data-returns-status-button

                >

                    <span class="seller-returns-status-button-main">

                        <span class="seller-returns-status-button-icon" aria-hidden="true">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <path d="M4 7h16"></path>

                                <path d="M7 12h10"></path>

                                <path d="M10 17h4"></path>

                            </svg>

                        </span>

                        <span data-returns-status-label>All Status</span>

                    </span>

                    <svg class="seller-returns-status-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">

                        <path d="m7 10 5 5 5-5"></path>

                    </svg>

                </button>

                <div class="seller-returns-status-menu" role="listbox" hidden data-returns-status-menu>

                    @foreach([

                        ['all', 'All Status', 'Show every return request'],

                        ['requested', 'Requested', 'Waiting for Seller review'],

                        ['approved', 'Approved', 'Authorized for return'],

                        ['returned', 'Returned', 'Parcel received by Seller'],

                        ['refunded', 'Refunded', 'Refund ledger recorded'],

                        ['rejected', 'Rejected', 'Declined return requests'],

                    ] as [$value, $label, $description])

                        <button

                            type="button"

                            class="seller-returns-status-option"

                            data-returns-status-value="{{ $value }}"

                        >

                            <span class="seller-returns-status-option-copy">

                                <strong>{{ $label }}</strong>

                                <small>{{ $description }}</small>

                            </span>

                            <svg class="seller-returns-status-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">

                                <path d="m6 12 4 4 8-8"></path>

                            </svg>

                        </button>

                    @endforeach

                </div>

            </div>

            <button type="submit" class="seller-returns-apply-filter">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">

                    <path d="M4 7h16"></path>

                    <path d="M7 12h10"></path>

                    <path d="M10 17h4"></path>

                </svg>

                Apply Filter

            </button>

            <a href="{{ route('seller.returns.index') }}" class="seller-returns-reset-filter">

                Reset

            </a>

        </form>

    </section>

    <section class="seller-returns-list">

        @forelse($returns as $return)

            <article class="seller-return-card">

                <div class="seller-return-card-main">

                    <div class="min-w-0">

                        <div class="seller-return-order-row">

                            <p class="seller-return-order-number">

                                {{ $return->order?->order_number ?: 'Order unavailable' }}

                            </p>

                            <span class="seller-return-status {{ $returnStatusClass($return->status) }}">

                                {{ ucfirst($return->status) }}

                            </span>

                        </div>

                        <p class="seller-return-reason">{{ $return->reason }}</p>

                        <p class="seller-return-details">

                            {{ $return->details ?: 'No additional details.' }}

                        </p>

                    </div>

                    <div class="seller-return-amount">

                        <div class="seller-return-amount-label">Requested refund</div>

                        <div class="seller-return-amount-value">

                            ₱{{ number_format((float) $return->requested_amount, 2) }}

                        </div>

                    </div>

                </div>

                @if($return->status === 'requested')

                    <div class="seller-return-action">

                        <form

                            method="POST"

                            action="{{ route('seller.returns.review', $return) }}"

                            class="seller-return-review-form"

                        >

                            @csrf

                            <input

                                name="seller_response"

                                required

                                placeholder="Seller response / decision notes"

                                class="seller-return-input"

                            >

                            <input

                                name="approved_amount"

                                type="number"

                                step=".01"

                                min="0"

                                max="{{ $return->requested_amount }}"

                                value="{{ $return->requested_amount }}"

                                class="seller-return-input"

                                aria-label="Approved refund amount"

                            >

                            <select

                                name="decision"

                                class="seller-return-select"

                                aria-label="Return decision"

                            >

                                <option value="approve">Approve</option>

                                <option value="reject">Reject</option>

                            </select>

                            <button type="submit" class="seller-return-button seller-return-button--primary">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">

                                    <path d="m6 12 4 4 8-8"></path>

                                </svg>

                                Submit Review

                            </button>

                        </form>

                    </div>

                @elseif($return->status === 'approved')

                    <div class="seller-return-action">

                        <form method="POST" action="{{ route('seller.returns.returned', $return) }}">

                            @csrf

                            <button type="submit" class="seller-return-button seller-return-button--primary">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">

                                    <path d="M4 7h16"></path>

                                    <path d="M6 7v12h12V7"></path>

                                    <path d="M9 11h6"></path>

                                </svg>

                                Confirm Returned Item Received

                            </button>

                        </form>

                    </div>

                @elseif($return->status === 'returned')

                    <div class="seller-return-action">

                        <form method="POST" action="{{ route('seller.returns.refund', $return) }}">

                            @csrf

                            <button type="submit" class="seller-return-button seller-return-button--success">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">

                                    <path d="M6 5h12v14H6z"></path>

                                    <path d="M9 9h6"></path>

                                    <path d="m9 13 2 2 4-4"></path>

                                </svg>

                                Record Refund

                            </button>

                            <p class="seller-return-action-note">

                                Records an internal refund ledger transaction only.

                            </p>

                        </form>

                    </div>

                @elseif($return->seller_response)

                    <div class="seller-return-action">

                        <div class="seller-return-response">

                            {{ $return->seller_response }}

                        </div>

                    </div>

                @endif

            </article>

        @empty

            <div class="seller-returns-empty">

                <div>

                    <span class="seller-returns-empty-icon" aria-hidden="true">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                            <path d="M5 7h14v12H5z"></path>

                            <path d="m9 11-3 3 3 3"></path>

                            <path d="M6 14h7a4 4 0 0 0 4-4V8"></path>

                        </svg>

                    </span>

                    <div class="seller-returns-empty-title">No return requests found</div>

                    <div class="seller-returns-empty-copy">

                        Return requests matching the selected status will appear here.

                    </div>

                </div>

            </div>

        @endforelse

    </section>

    @if($returns->hasPages())

        <div class="seller-returns-pagination">

            {{ $returns->links() }}

        </div>

    @endif

</div>

@endsection

@push('scripts')

<script>

(function () {

    function initReturnsStatusDropdown() {

        document.querySelectorAll('[data-returns-status-dropdown]').forEach((root) => {

            if (root.dataset.dropdownReady === '1') return;

            root.dataset.dropdownReady = '1';

            const nativeSelect = root.querySelector('[data-returns-status-native]');

            const button = root.querySelector('[data-returns-status-button]');

            const label = root.querySelector('[data-returns-status-label]');

            const menu = root.querySelector('[data-returns-status-menu]');

            const options = Array.from(root.querySelectorAll('[data-returns-status-value]'));

            if (!nativeSelect || !button || !label || !menu || !options.length) return;

            const getLabel = (value) => {

                const selected = Array.from(nativeSelect.options).find((option) => option.value === value);

                return selected?.textContent?.trim() || 'All Status';

            };

            const sync = (value) => {

                nativeSelect.value = value;

                label.textContent = getLabel(value);

                options.forEach((option) => {

                    const selected = (option.dataset.returnsStatusValue || '') === value;

                    option.classList.toggle('is-selected', selected);

                    option.setAttribute('aria-selected', selected ? 'true' : 'false');

                });

            };

            const close = () => {

                menu.hidden = true;

                button.setAttribute('aria-expanded', 'false');

            };

            const open = () => {

                menu.hidden = false;

                button.setAttribute('aria-expanded', 'true');

            };

            button.addEventListener('click', (event) => {

                event.preventDefault();

                event.stopPropagation();

                menu.hidden ? open() : close();

            });

            options.forEach((option) => {

                option.addEventListener('click', () => {

                    sync(option.dataset.returnsStatusValue || 'all');

                    close();

                    button.focus();

                });

            });

            document.addEventListener('click', (event) => {

                if (!root.contains(event.target)) close();

            });

            document.addEventListener('keydown', (event) => {

                if (event.key === 'Escape' && !menu.hidden) {

                    close();

                    button.focus();

                }

            });

            sync(nativeSelect.value || 'all');

        });

    }

    if (window.__SARI_SELLER_AFTER_PAINT__) {

        window.__SARI_SELLER_AFTER_PAINT__(initReturnsStatusDropdown);

    } else if (document.readyState === 'loading') {

        document.addEventListener('DOMContentLoaded', initReturnsStatusDropdown, { once: true });

    } else {

        initReturnsStatusDropdown();

    }

    document.addEventListener('livewire:navigated', initReturnsStatusDropdown);

})();

</script>

@endpush

