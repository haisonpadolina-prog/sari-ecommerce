@extends('layouts.seller')

@section('title', 'Returns & Refunds — SARI Seller')

@section('page-title', 'Returns & Refunds')

@push('styles')
<style id="sariSellerEmbeddedCss-returns" data-sari-inline-css="returns">
@verbatim
/* Extracted from resources/views/seller/returns.blade.php — original style block 1. */
.seller-returns-page {

        --returns-gold: #d59617;

        --returns-gold-hover: #c9890f;

        --returns-gold-soft: #fffaf0;

        --returns-ink: var(--sari-dark-e23e64dd1e, #111827);

        --returns-text: var(--sari-dark-e23e64dd1e, #344054);

        --returns-muted: var(--sari-dark-a63aa16d7c, #7c8592);

        --returns-soft-muted: var(--sari-dark-a63aa16d7c, #98a2b3);

        --returns-line: var(--sari-dark-d8c2716225, #e5e7eb);

        --returns-surface: var(--sari-dark-950baec030, #ffffff);

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

        border-color: var(--sari-dark-d8c2716225, #d5e7dc);

        background: var(--sari-dark-950baec030, #f5faf7);

        color: var(--sari-dark-a63aa16d7c, #4f7d63);

    }

    .seller-returns-alert--error {

        border-color: var(--sari-dark-d8c2716225, #efdada);

        background: var(--sari-dark-950baec030, #fff7f7);

        color: var(--sari-dark-58dd29f069, #a65353);

    }

    .seller-returns-alert-icon {

        display: grid;

        width: 27px;

        height: 27px;

        flex: 0 0 27px;

        place-items: center;

        border: 1px solid currentColor;

        border-radius: 8px;

        background: var(--sari-dark-950baec030, #fff);

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

        border-bottom: 1px solid var(--sari-dark-d8c2716225, #e7ebf0);

    }

    .seller-returns-eyebrow {

        margin: 0;

        color: var(--sari-dark-6e784b4931, #b87605);

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

        color: var(--sari-dark-9db03b407c, #C9890B);

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

        background: var(--sari-dark-950baec030, #fff);

        padding: 10px 12px;

        box-shadow: 0 5px 16px var(--sari-dark-7888fe3028, rgba(15, 23, 42, .028));

    }

    .seller-returns-stat-inner {

        display: flex;

        height: 100%;

        align-items: flex-start;

        justify-content: space-between;

        gap: 10px;

    }

    .seller-returns-stat-label {

        color: var(--sari-dark-a63aa16d7c, #667085);

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

        border: 1px solid var(--sari-dark-452f190c69, rgba(17, 24, 39, .05));

        border-radius: 9px;

    }

    .seller-returns-stat-icon svg {

        width: 14px;

        height: 14px;

    }

    .seller-returns-stat-icon--all {

        background: var(--sari-dark-950baec030, #f4f7fb);

        color: var(--sari-dark-a63aa16d7c, #587897);

    }

    .seller-returns-stat-icon--requested {

        background: var(--sari-dark-4d21eb669b, #fff8e9);

        color: var(--sari-dark-af18afcaa4, #b97805);

    }

    .seller-returns-stat-icon--approved {

        background: var(--sari-dark-950baec030, #f2f8fc);

        color: var(--sari-dark-a63aa16d7c, #4d7b9c);

    }

    .seller-returns-stat-icon--refunded {

        background: var(--sari-dark-4d21eb669b, #f1f8f4);

        color: var(--sari-dark-a63aa16d7c, #4f7d63);

    }

    /* ------------------------------------------------------------

       Search + premium status filter

       ------------------------------------------------------------ */

    .seller-returns-filter-panel {

        margin-top: 10px;

        border: 1px solid var(--returns-line);

        border-radius: 14px;

        background: var(--sari-dark-950baec030, #f8fafc);

        padding: 9px;

        box-shadow: 0 5px 16px var(--sari-dark-7888fe3028, rgba(15, 23, 42, .028));

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

        color: var(--sari-dark-a63aa16d7c, #98a2b3);

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

        border: 1px solid var(--sari-dark-d8c2716225, #d8dee6);

        background: var(--sari-dark-950baec030, #fff);

        padding: 0 11px 0 36px;

        color: var(--sari-dark-e23e64dd1e, #344054);

        outline: none;

        box-shadow: 0 1px 2px var(--sari-dark-f6fe7319d9, rgba(15,23,42,.02));

        transition: border-color .14s ease, box-shadow .14s ease;

    }

    .seller-returns-search-input::placeholder {

        color: var(--sari-dark-a63aa16d7c, #98a2b3);

        font-weight: 400;

    }

    .seller-returns-search-input:focus {

        border-color: var(--returns-gold);

        box-shadow: 0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(213,150,23,.08));

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

        box-shadow: 0 6px 14px var(--sari-dark-3e32d50f5e, rgba(213,150,23,.13));

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

        border: 1px solid var(--sari-dark-d8c2716225, #d8dee6);

        background: var(--sari-dark-950baec030, #fff);

        padding: 0 10px;

        color: var(--sari-dark-a63aa16d7c, #667085);

    }

    .seller-returns-reset-filter:hover,

    .seller-returns-reset-filter:focus-visible {

        outline: none;

        border-color: var(--sari-dark-d8c2716225, #cfd6df);

        background: var(--sari-dark-950baec030, #fff);

        color: var(--sari-dark-e23e64dd1e, #344054);

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

        border: 1px solid var(--sari-dark-d8c2716225, #d8dee6);

        background: var(--sari-dark-950baec030, #fff);

        padding: 0 10px 0 8px;

        color: var(--sari-dark-e23e64dd1e, #344054);

        font-weight: 650;

        text-align: left;

        cursor: pointer;

        outline: none;

        box-shadow: 0 1px 2px var(--sari-dark-f6fe7319d9, rgba(15,23,42,.02));

        transition: border-color .14s ease, box-shadow .14s ease, background-color .14s ease;

    }

    .seller-returns-status-button:hover {

        border-color: var(--sari-dark-d8c2716225, #cfd6df);

        background: var(--sari-dark-950baec030, #fdfefe);

    }

    .seller-returns-status-button:focus-visible,

    .seller-returns-status-button[aria-expanded="true"] {

        border-color: var(--returns-gold);

        background: var(--sari-dark-950baec030, #fff);

        box-shadow: 0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(213,150,23,.08));

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

        border: 1px solid var(--sari-dark-d8c2716225, #efe3c7);

        border-radius: 7px;

        background: var(--sari-dark-950baec030, #fffaf0);

        color: var(--sari-dark-af18afcaa4, #b97805);

    }

    .seller-returns-status-button-icon svg {

        width: 12px;

        height: 12px;

    }

    .seller-returns-status-chevron {

        width: 13px;

        height: 13px;

        flex: 0 0 auto;

        color: var(--sari-dark-a63aa16d7c, #98a2b3);

        transition: transform .15s ease, color .15s ease;

    }

    .seller-returns-status-button[aria-expanded="true"] .seller-returns-status-chevron {

        transform: rotate(180deg);

        color: var(--sari-dark-af18afcaa4, #b97805);

    }

    .seller-returns-status-menu {

        position: absolute;

        top: calc(100% + 6px);

        left: 0;

        right: 0;

        z-index: 90;

        min-width: 235px;

        overflow: hidden;

        border: 1px solid var(--sari-dark-d8c2716225, #e2e7ed);

        border-radius: 12px;

        background: var(--sari-dark-56c375634d, rgba(255,255,255,.985));

        padding: 5px;

        box-shadow: 0 18px 40px var(--sari-dark-3b2338cbf0, rgba(15,23,42,.12)), 0 3px 9px var(--sari-dark-1190f58155, rgba(15,23,42,.045));

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

        color: var(--sari-dark-e23e64dd1e, #344054);

        font-family: inherit;

        text-align: left;

        cursor: pointer;

        transition: background-color .12s ease, color .12s ease;

    }

    .seller-returns-status-option:hover,

    .seller-returns-status-option:focus-visible {

        outline: none;

        background: var(--sari-dark-950baec030, #f8fafc);

    }

    .seller-returns-status-option.is-selected {

        background: var(--sari-dark-4d21eb669b, #fff9ec);

    }

    .seller-returns-status-option-copy strong {

        display: block;

        color: var(--sari-dark-e23e64dd1e, #344054);

        font-size: 9.3px;

        font-weight: 700;

        line-height: 1.3;

    }

    .seller-returns-status-option.is-selected .seller-returns-status-option-copy strong {

        color: var(--sari-dark-af18afcaa4, #9b6505);

    }

    .seller-returns-status-option-copy small {

        display: block;

        margin-top: 2px;

        color: var(--sari-dark-a63aa16d7c, #98a2b3);

        font-size: 7.8px;

        font-weight: 400;

        line-height: 1.35;

    }

    .seller-returns-status-check {

        width: 14px;

        height: 14px;

        flex: 0 0 auto;

        color: var(--sari-dark-19b301f69a, #c9890f);

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

        background: var(--sari-dark-950baec030, #fff);

        box-shadow: 0 5px 16px var(--sari-dark-54756fd423, rgba(15, 23, 42, .026));

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

        color: var(--sari-dark-e23e64dd1e, #1f2937);

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

        border-color: var(--sari-dark-d8c2716225, #eadfc9);

        background: var(--sari-dark-950baec030, #fff9ef);

        color: var(--sari-dark-5d5ad776eb, #a8731f);

    }

    .seller-return-status--approved {

        border-color: var(--sari-dark-d8c2716225, #d3e1ec);

        background: var(--sari-dark-950baec030, #f3f8fc);

        color: var(--sari-dark-a63aa16d7c, #4d7393);

    }

    .seller-return-status--returned {

        border-color: var(--sari-dark-d8c2716225, #d9d8ec);

        background: var(--sari-dark-950baec030, #f6f5fb);

        color: var(--sari-dark-a63aa16d7c, #64638d);

    }

    .seller-return-status--refunded {

        border-color: var(--sari-dark-d8c2716225, #cfe4d7);

        background: var(--sari-dark-4d21eb669b, #f1f8f4);

        color: var(--sari-dark-a63aa16d7c, #4f7d63);

    }

    .seller-return-status--rejected {

        border-color: var(--sari-dark-d8c2716225, #efcece);

        background: var(--sari-dark-950baec030, #fff5f5);

        color: var(--sari-dark-58dd29f069, #a65353);

    }

    .seller-return-reason {

        margin: 6px 0 0;

        color: var(--sari-dark-e23e64dd1e, #344054);

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

        color: var(--sari-dark-e23e64dd1e, #1f2937);

        font-size: 16px;

        font-weight: 750;

        line-height: 1;

        letter-spacing: -.025em;

    }

    /* ------------------------------------------------------------

       Action areas

       ------------------------------------------------------------ */

    .seller-return-action {

        border-top: 1px solid var(--sari-dark-d8c2716225, #edf0f3);

        background: var(--sari-dark-950baec030, #fcfcfd);

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

        border: 1px solid var(--sari-dark-d8c2716225, #d8dee6);

        background: var(--sari-dark-950baec030, #fff);

        color: var(--sari-dark-e23e64dd1e, #344054);

        outline: none;

        box-shadow: none;

        transition: border-color .14s ease, box-shadow .14s ease;

    }

    .seller-return-input {

        padding-inline: 11px;

    }

    .seller-return-input::placeholder {

        color: var(--sari-dark-a63aa16d7c, #98a2b3);

    }

    .seller-return-select {

        padding-inline: 10px;

        font-weight: 600;

    }

    .seller-return-input:focus,

    .seller-return-select:focus {

        border-color: var(--returns-gold);

        box-shadow: 0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(213, 150, 23, .08));

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

        box-shadow: 0 6px 14px var(--sari-dark-3e32d50f5e, rgba(213, 150, 23, .13));

    }

    .seller-return-button--primary:hover,

    .seller-return-button--primary:focus-visible {

        outline: none;

        border-color: var(--returns-gold-hover);

        background: var(--returns-gold-hover);

    }

    .seller-return-button--success {

        border-color: var(--sari-dark-d8c2716225, #56856a);

        background: #56856a;

        color: #fff;

        box-shadow: 0 6px 14px var(--sari-dark-3b2338cbf0, rgba(86, 133, 106, .12));

    }

    .seller-return-button--success:hover,

    .seller-return-button--success:focus-visible {

        outline: none;

        border-color: var(--sari-dark-d8c2716225, #467257);

        background: #467257;

    }

    .seller-return-action-note {

        margin: 5px 0 0;

        color: var(--returns-soft-muted);

        font-size: 8px;

        line-height: 1.45;

    }

    .seller-return-response {

        border: 1px solid var(--sari-dark-d8c2716225, #edf0f3);

        border-radius: 10px;

        background: var(--sari-dark-950baec030, #f8fafc);

        padding: 9px 10px;

        color: var(--sari-dark-a63aa16d7c, #667085);

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

        background: var(--sari-dark-950baec030, #fff);

        padding: 26px 18px;

        text-align: center;

    }

    .seller-returns-empty-icon {

        display: grid;

        width: 38px;

        height: 38px;

        margin-inline: auto;

        place-items: center;

        border: 1px solid var(--sari-dark-d8c2716225, #e5e7eb);

        border-radius: 10px;

        background: var(--sari-dark-950baec030, #fafbfc);

        color: var(--sari-dark-a63aa16d7c, #98a2b3);

    }

    .seller-returns-empty-icon svg {

        width: 17px;

        height: 17px;

    }

    .seller-returns-empty-title {

        margin-top: 9px;

        color: var(--sari-dark-e23e64dd1e, #344054);

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
    --returns-float-border: var(--sari-dark-d8c2716225, #E5E7EB);
    --returns-float-border-strong: var(--sari-dark-d8c2716225, #D1D5DB);
    --returns-float-divider: var(--sari-dark-d8c2716225, #ECEFF2);

    --returns-float-shadow:
        0 1px 2px var(--sari-dark-7593018246, rgba(15, 23, 42, .035)),
        0 10px 26px var(--sari-dark-e07575a086, rgba(15, 23, 42, .075)),
        inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255, 255, 255, .86));

    --returns-float-shadow-strong:
        0 1px 2px var(--sari-dark-25b2da5e49, rgba(15, 23, 42, .04)),
        0 14px 34px var(--sari-dark-0a9e89f88f, rgba(15, 23, 42, .085)),
        inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255, 255, 255, .88));

    --returns-float-shadow-soft:
        0 1px 2px var(--sari-dark-e1384eb7fc, rgba(15, 23, 42, .025)),
        0 6px 16px var(--sari-dark-1df26971b5, rgba(15, 23, 42, .05)),
        inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255, 255, 255, .82));
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
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
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
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
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
    background: var(--sari-dark-950baec030, #FAFBFC) !important;
}

/* Seller response is a secondary floating information surface. */
.seller-returns-page .seller-return-response {
    border-color: var(--sari-dark-d8c2716225, #E7EAEE) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow: var(--returns-float-shadow-soft) !important;
}

/* Review controls stay white and neutral. */
.seller-returns-page .seller-return-input,
.seller-returns-page .seller-return-select {
    border-color: var(--returns-float-border-strong) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow:
        0 1px 2px var(--sari-dark-f6fe7319d9, rgba(15, 23, 42, .02)),
        0 4px 10px var(--sari-dark-b13200ac05, rgba(15, 23, 42, .03)) !important;
}

.seller-returns-page .seller-return-input:hover,
.seller-returns-page .seller-return-select:hover {
    border-color: var(--sari-dark-d8c2716225, #C9D0D9) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
}

.seller-returns-page .seller-return-input:focus,
.seller-returns-page .seller-return-select:focus {
    border-color: #C6A451 !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow:
        0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(201, 164, 81, .08)),
        0 4px 10px var(--sari-dark-b13200ac05, rgba(15, 23, 42, .03)) !important;
}

/* ------------------------------------------------------------------
   FILTER SURFACE + CUSTOM STATUS DROPDOWN
   ------------------------------------------------------------------ */
.seller-returns-page .seller-returns-search-input,
.seller-returns-page .seller-returns-status-button {
    border-color: var(--returns-float-border-strong) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow:
        0 1px 2px var(--sari-dark-f6fe7319d9, rgba(15, 23, 42, .02)),
        0 4px 11px var(--sari-dark-7593018246, rgba(15, 23, 42, .035)) !important;
    transform: none !important;
}

.seller-returns-page .seller-returns-search-input:hover,
.seller-returns-page .seller-returns-status-button:hover {
    border-color: var(--sari-dark-d8c2716225, #C9D0D9) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    transform: none !important;
}

.seller-returns-page .seller-returns-search-input:focus,
.seller-returns-page .seller-returns-status-button:focus-visible,
.seller-returns-page .seller-returns-status-button[aria-expanded="true"] {
    border-color: #C6A451 !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow:
        0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(201, 164, 81, .08)),
        0 5px 14px var(--sari-dark-25b2da5e49, rgba(15, 23, 42, .04)) !important;
}

/* Remove the cream structural shell around the filter icon while keeping
   the gold icon itself as the brand cue. */
.seller-returns-page .seller-returns-status-button-icon {
    border-color: var(--returns-float-border) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
}

/* Premium floating dropdown popover. */
.seller-returns-page .seller-returns-status-menu {
    border-color: var(--returns-float-border) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow:
        0 18px 42px var(--sari-dark-3b2338cbf0, rgba(15, 23, 42, .12)),
        0 4px 12px var(--sari-dark-25b2da5e49, rgba(15, 23, 42, .04)) !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}

.seller-returns-page .seller-returns-status-option {
    transform: none !important;
}

.seller-returns-page .seller-returns-status-option:hover,
.seller-returns-page .seller-returns-status-option:focus-visible {
    background: var(--sari-dark-950baec030, #F8FAFC) !important;
    transform: none !important;
}

.seller-returns-page .seller-returns-status-option.is-selected {
    background: var(--sari-dark-4d21eb669b, #FFF9EC) !important;
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
    background: var(--sari-dark-950baec030, #F9FAFB) !important;
    box-shadow: 0 4px 12px var(--sari-dark-7593018246, rgba(15, 23, 42, .035)) !important;
}

/* ------------------------------------------------------------------
   BUTTONS + PAGINATION
   ------------------------------------------------------------------ */

/* Secondary reset control stays neutral. */
.seller-returns-page .seller-returns-reset-filter {
    border-color: var(--returns-float-border) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
}

.seller-returns-page .seller-returns-reset-filter:hover,
.seller-returns-page .seller-returns-reset-filter:focus-visible {
    border-color: var(--returns-float-border-strong) !important;
    background: var(--sari-dark-950baec030, #F8FAFC) !important;
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
            0 1px 2px var(--sari-dark-b13200ac05, rgba(15, 23, 42, .03)),
            0 7px 18px var(--sari-dark-361b571631, rgba(15, 23, 42, .06)),
            inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255, 255, 255, .84)) !important;
    }

    .seller-returns-page .seller-return-response {
        box-shadow:
            0 1px 2px var(--sari-dark-f6fe7319d9, rgba(15, 23, 42, .02)),
            0 5px 13px var(--sari-dark-1190f58155, rgba(15, 23, 42, .045)) !important;
    }
}


/* ======================================================================
   RETURNS & REFUNDS — DASHBOARD SHADOW REFERENCE FINAL PASS
   Matches the stronger floating depth of the Seller dashboard while
   keeping every structural color neutral. CSS-only.
   ====================================================================== */

.seller-returns-page {
    --returns-dashboard-border: var(--sari-dark-d8c2716225, #E5E7EB);
    --returns-dashboard-divider: var(--sari-dark-d8c2716225, #ECEFF2);

    /* Dashboard-style layered elevation:
       1) contact shadow
       2) middle separation
       3) broad ambient float */
    --returns-dashboard-shadow:
        0 2px 5px var(--sari-dark-1190f58155, rgba(15, 23, 42, .045)),
        0 10px 24px var(--sari-dark-e07575a086, rgba(15, 23, 42, .075)),
        0 24px 54px var(--sari-dark-a2f72dee3d, rgba(15, 23, 42, .095));

    --returns-dashboard-shadow-soft:
        0 1px 3px var(--sari-dark-7593018246, rgba(15, 23, 42, .035)),
        0 7px 18px var(--sari-dark-361b571631, rgba(15, 23, 42, .06)),
        0 15px 32px var(--sari-dark-4fc908fafe, rgba(15, 23, 42, .065));

    --returns-dashboard-shadow-inner:
        0 1px 2px var(--sari-dark-e1384eb7fc, rgba(15, 23, 42, .025)),
        0 5px 14px var(--sari-dark-1190f58155, rgba(15, 23, 42, .045));
}

/* Summary cards — floating but slightly lighter than work surfaces. */
.seller-returns-page .seller-returns-stat {
    border: 1px solid var(--returns-dashboard-border) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow: var(--returns-dashboard-shadow-soft) !important;
    transform: none !important;
}

/* Primary containers — strongest dashboard depth. */
.seller-returns-page .seller-returns-filter-panel,
.seller-returns-page .seller-return-card,
.seller-returns-page .seller-returns-empty {
    border: 1px solid var(--returns-dashboard-border) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow: var(--returns-dashboard-shadow) !important;
    transform: none !important;
}

/* No lift on hover — depth remains exactly the same. */
.seller-returns-page .seller-returns-stat:hover {
    border-color: var(--returns-dashboard-border) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow: var(--returns-dashboard-shadow-soft) !important;
    transform: none !important;
}

.seller-returns-page .seller-returns-filter-panel:hover,
.seller-returns-page .seller-return-card:hover,
.seller-returns-page .seller-returns-empty:hover {
    border-color: var(--returns-dashboard-border) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow: var(--returns-dashboard-shadow) !important;
    transform: none !important;
}

/* Inner areas keep hierarchy without competing with the parent card. */
.seller-returns-page .seller-return-response {
    border-color: var(--sari-dark-d8c2716225, #E7EAEE) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow: var(--returns-dashboard-shadow-inner) !important;
    transform: none !important;
}

.seller-returns-page .seller-return-action {
    border-top-color: var(--returns-dashboard-divider) !important;
    background: var(--sari-dark-950baec030, #FAFBFC) !important;
    box-shadow: none !important;
}

/* Search/filter controls — slight depth only. */
.seller-returns-page .seller-returns-search-input,
.seller-returns-page .seller-returns-status-button,
.seller-returns-page .seller-returns-reset-filter {
    border-color: var(--sari-dark-d8c2716225, #D9DEE5) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow:
        0 1px 2px var(--sari-dark-e1384eb7fc, rgba(15, 23, 42, .025)),
        0 5px 12px var(--sari-dark-25b2da5e49, rgba(15, 23, 42, .04)) !important;
}

/* Dropdown menu floats one level above the filter card. */
.seller-returns-page .seller-returns-status-menu {
    border-color: var(--sari-dark-d8c2716225, #E1E5EA) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow:
        0 4px 10px var(--sari-dark-1df26971b5, rgba(15, 23, 42, .05)),
        0 18px 42px var(--sari-dark-59ed29fe9d, rgba(15, 23, 42, .14)),
        0 32px 70px var(--sari-dark-c33e3d1bb4, rgba(15, 23, 42, .08)) !important;
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
            0 1px 3px var(--sari-dark-b13200ac05, rgba(15, 23, 42, .03)),
            0 6px 14px var(--sari-dark-1df26971b5, rgba(15, 23, 42, .05)),
            0 12px 26px var(--sari-dark-fbd3b3b9f6, rgba(15, 23, 42, .055)) !important;
    }

    .seller-returns-page .seller-returns-filter-panel,
    .seller-returns-page .seller-return-card,
    .seller-returns-page .seller-returns-empty {
        box-shadow:
            0 2px 4px var(--sari-dark-7593018246, rgba(15, 23, 42, .035)),
            0 8px 18px var(--sari-dark-361b571631, rgba(15, 23, 42, .06)),
            0 18px 38px var(--sari-dark-7c935c8886, rgba(15, 23, 42, .07)) !important;
    }
}

/* =======================================================================
   SARI RETURNS & REFUNDS — CLEAN UI MATCH (2026)
   Visual refinements only. All original GET/POST forms, @csrf,
   Laravel paginator, dropdown handlers, refund routes and statuses remain.
   ======================================================================= */
.seller-returns-page {
    --returns-gold: #C88912;
    --returns-gold-hover: #B47B0E;
    --returns-gold-soft: #F8FAFC;
    --returns-ink: var(--sari-dark-e23e64dd1e, #293440);
    --returns-text: var(--sari-dark-e23e64dd1e, #465362);
    --returns-muted: var(--sari-dark-a63aa16d7c, #7C8998);
    --returns-soft-muted: var(--sari-dark-a63aa16d7c, #94A3B8);
    --returns-line: var(--sari-dark-d8c2716225, #E5E7EB);
    width: 100%;
    max-width: 1520px;
    padding-bottom: 34px;
    color: var(--returns-ink);
    font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
}
.seller-returns-page .seller-returns-header {
    min-height: auto;
    padding: 0 2px 17px;
    border-bottom: 1px solid var(--sari-dark-d8c2716225, #E5E9EE);
}
.seller-returns-page .seller-returns-eyebrow {
    color: var(--sari-dark-685eb4fc40, #A87316);
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: .11em;
    line-height: 1.35;
}
.seller-returns-page .seller-returns-title {
    margin-top: 5px;
    font-size: clamp(27px, 2.3vw, 31px);
    font-weight: 760;
    line-height: 1.14;
    color: var(--sari-dark-e23e64dd1e, #2E343B);
    letter-spacing: -.03em;
}
.seller-returns-page .seller-returns-title span:last-child {
    color: var(--sari-dark-19b301f69a, #C88912);
}
.seller-returns-page .seller-returns-subtitle {
    margin-top: 8px;
    font-size: 11.5px;
    line-height: 1.65;
    color: var(--sari-dark-a63aa16d7c, #7B8897);
}
/* Summary KPI cards: equal dimensions, bare 27px icons, no tinted icon shells. */
.seller-returns-page .seller-returns-summary {
    margin-top: 22px;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
}
.seller-returns-page .seller-returns-stat,
.seller-returns-page .seller-returns-stat:hover {
    min-height: 110px;
    border: 1px solid var(--sari-dark-d8c2716225, #E5E7EB) !important;
    border-radius: 18px !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    padding: 17px 18px;
    box-shadow: 0 2px 5px var(--sari-dark-e1384eb7fc, rgba(15,23,42,.025)),0 10px 24px var(--sari-dark-7c935c8886, rgba(15,23,42,.07)) !important;
    transform: none !important;
}
.seller-returns-page .seller-returns-stat-inner { gap: 10px; }
.seller-returns-page .seller-returns-stat-label {
    font-size: 13px;
    font-weight: 650;
    color: var(--sari-dark-a63aa16d7c, #5D6977);
    line-height: 1.35;
}
.seller-returns-page .seller-returns-stat-value {
    margin-top: 7px;
    font-size: 28px;
    font-weight: 750;
    line-height: 1.05;
    letter-spacing: -.035em;
    color: var(--sari-dark-e23e64dd1e, #283443);
}
.seller-returns-page .seller-returns-stat-note {
    margin-top: 8px;
    font-size: 11px;
    line-height: 1.45;
    color: var(--sari-dark-a63aa16d7c, #8290A0);
}
.seller-returns-page .seller-returns-stat-icon,
.seller-returns-page .seller-returns-stat-icon--all,
.seller-returns-page .seller-returns-stat-icon--requested,
.seller-returns-page .seller-returns-stat-icon--approved,
.seller-returns-page .seller-returns-stat-icon--refunded {
    display: grid;
    width: 28px;
    height: 28px;
    flex: 0 0 28px;
    place-items: center;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}
.seller-returns-page .seller-returns-stat-icon--all { color: var(--sari-dark-a63aa16d7c, #94A3B8); }
.seller-returns-page .seller-returns-stat-icon--requested { color: var(--sari-dark-19b301f69a, #C88912); }
.seller-returns-page .seller-returns-stat-icon--approved { color: var(--sari-dark-787a076f31, #059669); }
.seller-returns-page .seller-returns-stat-icon--refunded { color: var(--sari-dark-787a076f31, #059669); }
.seller-returns-page .seller-returns-stat-icon svg { width: 27px; height: 27px; }
/* The search toolbar sits directly on the page as in Pickup Requests. */
.seller-returns-page .seller-returns-filter-panel,
.seller-returns-page .seller-returns-filter-panel:hover {
    margin-top: 22px;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    padding: 0;
    box-shadow: none !important;
    overflow: visible;
}
.seller-returns-page .seller-returns-filter-form {
    grid-template-columns: minmax(0,1fr) minmax(190px,220px) 148px 94px;
    gap: 12px;
    align-items: stretch;
}
.seller-returns-page .seller-returns-search-input,
.seller-returns-page .seller-returns-status-button,
.seller-returns-page .seller-returns-apply-filter,
.seller-returns-page .seller-returns-reset-filter {
    height: 48px;
    min-height: 48px;
    border-radius: 14px;
    font-size: 11.5px;
    line-height: 1.4;
}
.seller-returns-page .seller-returns-search-input,
.seller-returns-page .seller-returns-search-input:hover,
.seller-returns-page .seller-returns-status-button,
.seller-returns-page .seller-returns-status-button:hover {
    border: 1px solid var(--sari-dark-d8c2716225, #DCE2E8) !important;
    background: var(--sari-dark-950baec030, #FFF) !important;
    box-shadow: none !important;
    color: var(--sari-dark-e23e64dd1e, #344054);
}
.seller-returns-page .seller-returns-search-input { padding-left: 46px; padding-right: 16px; font-weight: 550; }
.seller-returns-page .seller-returns-search-input::placeholder { color: var(--sari-dark-a63aa16d7c, #94A3B8); font-weight: 500; }
.seller-returns-page .seller-returns-search-field > svg {
    width: 21px;
    height: 21px;
    left: 16px;
    color: var(--sari-dark-a63aa16d7c, #94A3B8);
}
.seller-returns-page .seller-returns-search-input:focus,
.seller-returns-page .seller-returns-status-button:focus-visible,
.seller-returns-page .seller-returns-status-button[aria-expanded="true"] {
    outline: none;
    border-color: #C88912 !important;
    box-shadow: 0 0 0 2px var(--sari-dark-71f44bdb16, rgba(200,137,18,.11)) !important;
}
.seller-returns-page .seller-returns-status-button {
    padding: 0 16px;
    font-weight: 650;
}
.seller-returns-page .seller-returns-status-button-icon { display: none !important; }
.seller-returns-page .seller-returns-status-button-main { gap: 0; }
.seller-returns-page .seller-returns-status-chevron { width: 17px; height: 17px; color: var(--sari-dark-a63aa16d7c, #6B7280); }
.seller-returns-page .seller-returns-apply-filter {
    padding: 0 16px;
    border: 1px solid #C88912 !important;
    background: #C88912 !important;
    color: #FFFFFF;
    font-size: 12px;
    font-weight: 700;
    box-shadow: none !important;
}
.seller-returns-page .seller-returns-apply-filter:hover,
.seller-returns-page .seller-returns-apply-filter:focus-visible {
    background: #B47B0E !important;
    border-color: #B47B0E !important;
}
.seller-returns-page .seller-returns-apply-filter svg { width: 15px; height: 15px; }
.seller-returns-page .seller-returns-reset-filter,
.seller-returns-page .seller-returns-reset-filter:hover {
    border: 1px solid var(--sari-dark-d8c2716225, #DCE2E8) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    color: var(--sari-dark-a63aa16d7c, #526071);
    font-size: 11.5px;
    font-weight: 650;
    box-shadow: none !important;
}
.seller-returns-page .seller-returns-reset-filter:hover { background: var(--sari-dark-950baec030, #F8FAFC) !important; }
.seller-returns-page .seller-returns-status-menu {
    top: calc(100% + 8px);
    min-width: 100%;
    width: max(100%, 235px);
    max-height: min(360px, 58vh);
    overflow-y: auto;
    padding: 8px;
    border: 1px solid var(--sari-dark-d8c2716225, #E2E8F0) !important;
    border-radius: 14px;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow: 0 16px 32px var(--sari-dark-3b2338cbf0, rgba(15,23,42,.12)),0 2px 6px var(--sari-dark-1df26971b5, rgba(15,23,42,.05)) !important;
    backdrop-filter: none;
}
.seller-returns-page .seller-returns-status-option {
    min-height: 40px;
    padding: 8px 12px;
    border-radius: 10px;
    background: transparent !important;
    color: var(--sari-dark-e23e64dd1e, #475569);
}
.seller-returns-page .seller-returns-status-option:hover,
.seller-returns-page .seller-returns-status-option:focus-visible { background: var(--sari-dark-950baec030, #F8FAFC) !important; }
.seller-returns-page .seller-returns-status-option.is-selected { background: var(--sari-dark-4d21eb669b, #F1F5F9) !important; }
.seller-returns-page .seller-returns-status-option-copy strong { font-size: 11.5px; font-weight: 650; color: var(--sari-dark-e23e64dd1e, #394454); }
.seller-returns-page .seller-returns-status-option.is-selected .seller-returns-status-option-copy strong { color: var(--sari-dark-e23e64dd1e, #273240); }
.seller-returns-page .seller-returns-status-option-copy small { font-size: 10px; line-height: 1.45; color: var(--sari-dark-a63aa16d7c, #8995A3); }
.seller-returns-page .seller-returns-status-check { display: none !important; }
/* One floating work surface instead of a stack of unrelated mini-cards. */
.seller-returns-page .seller-returns-records {
    margin-top: 24px;
    overflow: hidden;
    border: 1px solid var(--sari-dark-d8c2716225, #E5E7EB);
    border-radius: 18px;
    background: var(--sari-dark-950baec030, #FFFFFF);
    box-shadow: 0 2px 5px var(--sari-dark-1190f58155, rgba(15,23,42,.045)),0 12px 28px var(--sari-dark-e07575a086, rgba(15,23,42,.075)),0 22px 42px var(--sari-dark-fbd3b3b9f6, rgba(15,23,42,.055));
}
.seller-returns-page .seller-returns-records-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 20px 23px;
    border-bottom: 1px solid var(--sari-dark-d8c2716225, #E9EDF1);
}
.seller-returns-page .seller-returns-records-title {
    margin: 0;
    color: var(--sari-dark-e23e64dd1e, #293440);
    font-size: 18px;
    font-weight: 750;
    line-height: 1.35;
}
.seller-returns-page .seller-returns-records-subtitle {
    margin: 4px 0 0;
    color: var(--sari-dark-a63aa16d7c, #8491A0);
    font-size: 11.5px;
    line-height: 1.5;
}
.seller-returns-page .seller-returns-records-count { flex-shrink: 0; color: var(--sari-dark-a63aa16d7c, #8491A0); font-size: 11.5px; font-weight: 600; }
.seller-returns-page .seller-returns-list {
    display: block;
    gap: 0;
    margin: 0;
}
.seller-returns-page .seller-return-card,
.seller-returns-page .seller-return-card:hover {
    display: block;
    border: 0 !important;
    border-radius: 0 !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    box-shadow: none !important;
    transform: none !important;
}
.seller-returns-page .seller-return-card + .seller-return-card { border-top: 1px solid var(--sari-dark-d8c2716225, #E9EDF1) !important; }
.seller-returns-page .seller-return-card-main {
    grid-template-columns: minmax(0,1fr) 170px;
    gap: 18px;
    padding: 19px 23px;
}
.seller-returns-page .seller-return-order-row { gap: 10px; }
.seller-returns-page .seller-return-order-number {
    color: var(--sari-dark-e23e64dd1e, #293440);
    font-size: 12.5px;
    font-weight: 750;
    line-height: 1.5;
    overflow-wrap: anywhere;
}
.seller-returns-page .seller-return-status {
    min-height: 26px;
    padding: 3px 11px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 650;
    line-height: 1.25;
    text-transform: none;
}
.seller-returns-page .seller-return-status--requested { background: var(--sari-dark-950baec030, #F8FAFC); border-color: #E2D3B1; color: var(--sari-dark-af18afcaa4, #A56D0A); }
.seller-returns-page .seller-return-status--approved { background: var(--sari-dark-950baec030, #EFF6FF); border-color: var(--sari-dark-d8c2716225, #BFDBFE); color: var(--sari-dark-7a41191112, #1D4ED8); }
.seller-returns-page .seller-return-status--returned { background: var(--sari-dark-950baec030, #F5F3FF); border-color: var(--sari-dark-d8c2716225, #DDD6FE); color: var(--sari-dark-a8c9d62a39, #6D28D9); }
.seller-returns-page .seller-return-status--refunded { background: var(--sari-dark-4d21eb669b, #ECFDF3); border-color: var(--sari-dark-d8c2716225, #BBF7D0); color: var(--sari-dark-e23e64dd1e, #166534); }
.seller-returns-page .seller-return-status--rejected { background: var(--sari-dark-950baec030, #FEF2F2); border-color: var(--sari-dark-d8c2716225, #FECACA); color: var(--sari-dark-5300031fd1, #B91C1C); }
.seller-returns-page .seller-return-reason {
    margin-top: 9px;
    color: var(--sari-dark-e23e64dd1e, #3C4856);
    font-size: 12px;
    font-weight: 650;
    line-height: 1.55;
}
.seller-returns-page .seller-return-details {
    max-width: 840px;
    margin-top: 4px;
    color: var(--sari-dark-a63aa16d7c, #7B8897);
    font-size: 11.5px;
    line-height: 1.7;
    overflow-wrap: anywhere;
}
.seller-returns-page .seller-return-amount { min-width: 0; }
.seller-returns-page .seller-return-amount-label { font-size: 10.5px; font-weight: 550; color: var(--sari-dark-a63aa16d7c, #8491A0); }
.seller-returns-page .seller-return-amount-value { margin-top: 6px; font-size: 18px; font-weight: 750; color: var(--sari-dark-e23e64dd1e, #293440); }
.seller-returns-page .seller-return-action {
    border-top: 1px solid var(--sari-dark-d8c2716225, #EDF0F3) !important;
    background: var(--sari-dark-950baec030, #FAFBFC) !important;
    padding: 15px 23px;
}
.seller-returns-page .seller-return-review-form {
    grid-template-columns: minmax(0,1fr) 145px 132px 150px;
    gap: 12px;
}
.seller-returns-page .seller-return-input,
.seller-returns-page .seller-return-select,
.seller-returns-page .seller-return-button {
    height: 46px;
    min-height: 46px;
    border-radius: 11px;
    font-size: 11.5px;
    line-height: 1.4;
}
.seller-returns-page .seller-return-input,
.seller-returns-page .seller-return-select {
    padding: 0 13px;
    border: 1px solid var(--sari-dark-d8c2716225, #DCE2E8) !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    color: var(--sari-dark-e23e64dd1e, #384454);
    box-shadow: none !important;
}
.seller-returns-page .seller-return-input::placeholder { color: var(--sari-dark-a63aa16d7c, #94A3B8); }
.seller-returns-page .seller-return-input:focus,
.seller-returns-page .seller-return-select:focus {
    border-color: #C88912 !important;
    box-shadow: 0 0 0 2px var(--sari-dark-71f44bdb16, rgba(200,137,18,.11)) !important;
}
.seller-returns-page .seller-return-button {
    padding: 0 15px;
    font-size: 11.5px;
    font-weight: 700;
    white-space: normal;
    text-align: center;
}
.seller-returns-page .seller-return-button svg { width: 16px; height: 16px; flex: 0 0 16px; }
.seller-returns-page .seller-return-button--primary {
    border-color: #C88912 !important;
    background: #C88912 !important;
    color: #FFFFFF !important;
    box-shadow: none !important;
}
.seller-returns-page .seller-return-button--primary:hover { border-color: #B47B0E !important; background: #B47B0E !important; }
.seller-returns-page .seller-return-button--success { border-color: #15803D !important; background: #15803D !important; color: #FFFFFF !important; box-shadow: none !important; }
.seller-returns-page .seller-return-button--success:hover { border-color: #166534 !important; background: #166534 !important; }
.seller-returns-page .seller-return-action-note { margin-top: 8px; color: var(--sari-dark-a63aa16d7c, #8491A0); font-size: 10px; }
.seller-returns-page .seller-return-response { border: 1px solid var(--sari-dark-d8c2716225, #E5E7EB) !important; background: var(--sari-dark-950baec030, #FFFFFF) !important; border-radius: 10px; box-shadow: none !important; color: var(--sari-dark-a63aa16d7c, #657180); font-size: 11.5px; line-height: 1.65; }
/* Empty state and pagination live in the same parent surface. */
.seller-returns-page .seller-returns-empty {
    min-height: 270px;
    border: 0 !important;
    border-radius: 0 !important;
    background: var(--sari-dark-950baec030, #FFFFFF) !important;
    padding: 46px 22px;
    box-shadow: none !important;
}
.seller-returns-page .seller-returns-empty-icon {
    width: 70px;
    height: 70px;
    border: 0 !important;
    border-radius: 0;
    background: transparent !important;
    color: var(--sari-dark-a63aa16d7c, #94A3B8);
    box-shadow: none !important;
}
.seller-returns-page .seller-returns-empty-icon svg { width: 50px; height: 50px; }
.seller-returns-page .seller-returns-empty-title { margin-top: 10px; color: var(--sari-dark-a63aa16d7c, #5A6777); font-size: 20px; font-weight: 700; }
.seller-returns-page .seller-returns-empty-copy { margin-top: 6px; color: var(--sari-dark-a63aa16d7c, #8491A0); font-size: 12px; }
.seller-returns-page .seller-returns-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    min-height: 68px;
    padding: 14px 22px;
    border-top: 1px solid var(--sari-dark-d8c2716225, #E9EDF1);
    background: var(--sari-dark-950baec030, #FFFFFF);
}
.seller-returns-page .seller-returns-footer-count { color: var(--sari-dark-a63aa16d7c, #8491A0); font-size: 11px; font-weight: 550; }
.seller-returns-page .seller-returns-pagination { margin: 0; }
.seller-returns-page .seller-returns-pagination nav a,
.seller-returns-page .seller-returns-pagination nav span { border-color: var(--sari-dark-d8c2716225, #E5E7EB) !important; }
.seller-returns-page .seller-returns-pagination nav a:hover { background: var(--sari-dark-950baec030, #F8FAFC) !important; color: var(--sari-dark-af18afcaa4, #A56D0A) !important; }
.seller-returns-page .seller-returns-pagination nav [aria-current="page"] span,
.seller-returns-page .seller-returns-pagination nav [aria-current="page"] {
    background: #C88912 !important;
    border-color: #C88912 !important;
    color: #FFFFFF !important;
}
.seller-returns-page button:focus-visible,
.seller-returns-page a:focus-visible { outline: 2px solid rgba(200,137,18,.48); outline-offset: 2px; }
@media (max-width: 1120px) {
    .seller-returns-page .seller-return-review-form { grid-template-columns: minmax(0,1fr) 145px 130px; }
    .seller-returns-page .seller-return-review-form .seller-return-button { grid-column: 1 / -1; width: max-content; min-width: 150px; }
}
@media (max-width: 900px) {
    .seller-returns-page .seller-returns-summary { grid-template-columns: repeat(2, minmax(0,1fr)); }
    .seller-returns-page .seller-returns-filter-form { grid-template-columns: minmax(0,1fr) minmax(180px, .55fr); }
    .seller-returns-page .seller-returns-filter-form .seller-returns-apply-filter,
    .seller-returns-page .seller-returns-filter-form .seller-returns-reset-filter { width: 100%; }
}
@media (max-width: 680px) {
    .seller-returns-page .seller-returns-title { font-size: 25px; }
    .seller-returns-page .seller-returns-eyebrow { font-size: 9.5px; }
    .seller-returns-page .seller-returns-summary { gap: 10px; margin-top: 16px; }
    .seller-returns-page .seller-returns-stat,
    .seller-returns-page .seller-returns-stat:hover { min-height: 106px; padding: 14px; }
    .seller-returns-page .seller-returns-stat-label { font-size: 12px; }
    .seller-returns-page .seller-returns-stat-value { font-size: 25px; }
    .seller-returns-page .seller-returns-stat-note { font-size: 10px; }
    .seller-returns-page .seller-returns-filter-form { grid-template-columns: 1fr; gap: 9px; }
    .seller-returns-page .seller-returns-search-input,
    .seller-returns-page .seller-returns-status-button,
    .seller-returns-page .seller-returns-apply-filter,
    .seller-returns-page .seller-returns-reset-filter { height: 48px; min-height: 48px; }
    .seller-returns-page .seller-returns-records-header { padding: 17px 16px; flex-wrap: wrap; }
    .seller-returns-page .seller-returns-records-title { font-size: 16px; }
    .seller-returns-page .seller-returns-records-count { font-size: 10.5px; }
    .seller-returns-page .seller-return-card-main { grid-template-columns: 1fr; gap: 13px; padding: 16px; }
    .seller-returns-page .seller-return-amount { text-align: left; }
    .seller-returns-page .seller-return-action { padding: 14px 16px; }
    .seller-returns-page .seller-return-review-form { grid-template-columns: 1fr; gap: 9px; }
    .seller-returns-page .seller-return-review-form .seller-return-button { grid-column: auto; width: 100%; }
    .seller-returns-page .seller-returns-footer { justify-content: center; text-align: center; padding: 15px; }
    .seller-returns-page .seller-returns-pagination { max-width: 100%; }
}

/* =======================================================================
   SARI RETURNS & REFUNDS — BUYER CART EXACT FLOATING PANELS
   Scoped CSS-only update: outer KPI cards and Return Requests panel only.
   Existing flat filter toolbar, return rows, controls and actions untouched.
   ======================================================================= */
.seller-returns-page {
    --returns-cart-shadow-strong:
        0 2px 5px var(--sari-dark-1190f58155, rgba(15, 23, 42, .045)),
        0 10px 24px var(--sari-dark-e07575a086, rgba(15, 23, 42, .075)),
        0 24px 54px var(--sari-dark-a2f72dee3d, rgba(15, 23, 42, .095)),
        inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255, 255, 255, .92));
    --returns-cart-shadow-mobile:
        0 1px 3px var(--sari-dark-7593018246, rgba(15, 23, 42, .035)),
        0 6px 16px var(--sari-dark-fbd3b3b9f6, rgba(15, 23, 42, .055)),
        0 13px 28px var(--sari-dark-4fc908fafe, rgba(15, 23, 42, .065)),
        inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255, 255, 255, .90));
}

.seller-returns-page .seller-returns-stat,
.seller-returns-page .seller-returns-stat:hover,
.seller-returns-page .seller-returns-records,
.seller-returns-page .seller-returns-records:hover {
    border-radius: 20px !important;
    box-shadow: var(--returns-cart-shadow-strong) !important;
}

@media (max-width: 767px) {
    .seller-returns-page .seller-returns-stat,
    .seller-returns-page .seller-returns-stat:hover,
    .seller-returns-page .seller-returns-records,
    .seller-returns-page .seller-returns-records:hover {
        border-radius: 16px !important;
        box-shadow: var(--returns-cart-shadow-mobile) !important;
    }
}

@media print {
    .seller-returns-page .seller-returns-stat,
    .seller-returns-page .seller-returns-stat:hover,
    .seller-returns-page .seller-returns-records,
    .seller-returns-page .seller-returns-records:hover {
        box-shadow: none !important;
    }
}


/* Seller Orders reference parity: visual layout only, Returns workflow retained. */
.seller-returns-page {
    max-width: 1520px !important;
    margin: 0 auto !important;
    padding-bottom: 28px !important;
    font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
    --returns-reference-shadow: 0 3px 10px var(--sari-dark-7593018246, rgba(15,23,42,.035)), 0 12px 30px var(--sari-dark-fbd3b3b9f6, rgba(15,23,42,.055));
}
.seller-returns-page .seller-returns-header {
    min-height: 0 !important; margin: 0 0 20px !important; padding: 0 0 14px !important;
    border-bottom: 1px solid var(--sari-dark-d8c2716225, #E9EDF1) !important;
}
.seller-returns-page .seller-returns-eyebrow { font-size: 10.5px !important; font-weight: 800 !important; line-height: 1.4 !important; letter-spacing: .14em !important; }
.seller-returns-page .seller-returns-title { margin-top: 6px !important; font-size: clamp(27px,2.3vw,31px) !important; font-weight: 760 !important; line-height: 1.08 !important; letter-spacing: -.04em !important; }
.seller-returns-page .seller-returns-title span:last-child { margin-left: .18em !important; color: var(--sari-dark-19b301f69a, #C88912) !important; }
.seller-returns-page .seller-returns-summary { grid-template-columns: repeat(4,minmax(0,1fr)) !important; gap: 14px !important; margin: 0 !important; }
.seller-returns-page .seller-returns-stat,
.seller-returns-page .seller-returns-stat:hover,
.seller-returns-page .seller-returns-records,
.seller-returns-page .seller-returns-records:hover {
    border: 1px solid var(--sari-dark-d8c2716225, #E5E7EB) !important;
    border-radius: 18px !important; background: var(--sari-dark-950baec030, #FFF) !important;
    box-shadow: var(--returns-reference-shadow) !important; transform: none !important;
}
.seller-returns-page .seller-returns-stat { min-height: 110px !important; padding: 14px 16px !important; }
.seller-returns-page .seller-returns-stat-inner { align-items: flex-start !important; gap: 12px !important; }
.seller-returns-page .seller-returns-stat-label { font-size: 13px !important; font-weight: 650 !important; line-height: 1.4 !important; }
.seller-returns-page .seller-returns-stat-value { margin-top: 7px !important; font-size: clamp(25px,1.9vw,30px) !important; font-weight: 650 !important; line-height: 1.12 !important; font-variant-numeric: tabular-nums; }
.seller-returns-page .seller-returns-stat-note { margin-top: 6px !important; font-size: 11.5px !important; line-height: 1.5 !important; white-space: normal !important; }
.seller-returns-page .seller-returns-stat-icon { width: 27px !important; height: 27px !important; flex: 0 0 27px !important; }
.seller-returns-page .seller-returns-stat-icon svg { width: 27px !important; height: 27px !important; }
.seller-returns-page .seller-returns-filter-panel,
.seller-returns-page .seller-returns-filter-panel:hover,
.seller-returns-page .seller-returns-filter-panel:focus-within {
    position: relative; z-index: 15; margin-top: 20px !important; padding: 0 !important;
    border: 0 !important; background: transparent !important; box-shadow: none !important; transform: none !important; translate: none !important;
}
.seller-returns-page .seller-returns-filter-form { grid-template-columns: minmax(0,1fr) minmax(185px,210px) auto auto !important; align-items: center !important; gap: 12px !important; }
.seller-returns-page .seller-returns-search-input,
.seller-returns-page .seller-returns-status-button,
.seller-returns-page .seller-returns-apply-filter,
.seller-returns-page .seller-returns-reset-filter {
    height: 48px !important; min-height: 48px !important; border-radius: 14px !important;
    margin-top: 0 !important; margin-bottom: 0 !important; transform: none !important; translate: none !important;
}
.seller-returns-page .seller-returns-search-input,
.seller-returns-page .seller-returns-status-button { font-size: 11.5px !important; font-weight: 600 !important; box-shadow: none !important; }
.seller-returns-page .seller-returns-search-input { padding: 0 14px 0 46px !important; }
.seller-returns-page .seller-returns-search-field > svg { left: 16px !important; width: 21px !important; height: 21px !important; }
.seller-returns-page .seller-returns-search-input::placeholder { font-size: 11.5px !important; }
.seller-returns-page .seller-returns-status-button { padding: 0 16px !important; }
.seller-returns-page .seller-returns-status-chevron { width: 17px !important; height: 17px !important; }
.seller-returns-page .seller-returns-status-dropdown { position: relative; z-index: 50; }
.seller-returns-page .seller-returns-status-menu { top: calc(100% + 8px) !important; left: 0 !important; right: 0 !important; width: 100% !important; min-width: 100% !important; padding: 8px !important; border-radius: 14px !important; z-index: 100 !important; box-shadow: 0 16px 32px var(--sari-dark-3b2338cbf0, rgba(15,23,42,.12)), 0 2px 6px var(--sari-dark-1df26971b5, rgba(15,23,42,.05)) !important; }
.seller-returns-page .seller-returns-status-option { min-height: 38px !important; padding: 0 12px !important; border-radius: 10px !important; display: flex !important; align-items: center !important; }
.seller-returns-page .seller-returns-status-option-copy strong { font-size: 11.5px !important; font-weight: 600 !important; }
/* The full option descriptions remain in the source; the reference uses single-line options. */
.seller-returns-page .seller-returns-status-option-copy small { display: none !important; }
.seller-returns-page .seller-returns-status-option.is-selected .seller-returns-status-option-copy strong { font-weight: 700 !important; }
.seller-returns-page .seller-returns-search-input:focus,
.seller-returns-page .seller-returns-status-button:focus-visible,
.seller-returns-page .seller-returns-status-button[aria-expanded="true"] { border-color: #C88912 !important; box-shadow: 0 0 0 1px var(--sari-dark-3b2338cbf0, rgba(200,137,18,.12)) !important; }
.seller-returns-page .seller-returns-apply-filter,
.seller-returns-page .seller-returns-reset-filter { padding: 0 18px !important; font-size: 12px !important; font-weight: 700 !important; box-shadow: none !important; }
.seller-returns-page .seller-returns-reset-filter { min-width: 86px !important; }
.seller-returns-page .seller-returns-apply-filter { gap: 8px !important; }
.seller-returns-page .seller-returns-records { margin-top: 20px !important; overflow: hidden !important; }
.seller-returns-page .seller-returns-records-header { padding: 21px 23px !important; gap: 20px !important; }
.seller-returns-page .seller-returns-records-title { font-size: 18px !important; font-weight: 780 !important; }
.seller-returns-page .seller-returns-records-subtitle { margin-top: 4px !important; font-size: 11.5px !important; }
.seller-returns-page .seller-returns-records-count { font-size: 12px !important; font-weight: 600 !important; }
.seller-returns-page .seller-returns-table-scroll { width: 100%; overflow-x: auto; scrollbar-width: thin; }
.seller-returns-page .seller-returns-table-scroll:focus-visible { outline: 2px solid #C88912; outline-offset: -2px; }
.seller-returns-page .seller-returns-table-head,
.seller-returns-page .seller-return-card-main {
    display: grid !important; grid-template-columns: minmax(230px,1.1fr) minmax(260px,1.5fr) 112px 140px !important;
    min-width: 950px !important; gap: 12px !important; align-items: center !important; padding-left: 16px !important; padding-right: 16px !important;
}
.seller-returns-page .seller-returns-table-head { min-height: 48px; padding-top: 13px; padding-bottom: 13px; border-bottom: 1px solid var(--sari-dark-d8c2716225, #E9EDF1); background: var(--sari-dark-950baec030, #F8FAFC); }
.seller-returns-page .seller-returns-table-head > span { font-size: 11px; font-weight: 750; line-height: 1.4; letter-spacing: .075em; text-transform: uppercase; color: var(--sari-dark-a63aa16d7c, #8591A0); }
.seller-returns-page .seller-return-card { min-width: 950px; }
.seller-returns-page .seller-return-card-main { padding-top: 17px !important; padding-bottom: 17px !important; }
.seller-returns-page .seller-return-order-number { font-size: 12.5px !important; font-weight: 750 !important; line-height: 1.5 !important; }
.seller-returns-page .seller-return-reason { margin-top: 3px !important; font-size: 11.5px !important; line-height: 1.5 !important; }
.seller-returns-page .seller-return-details { margin-top: 0 !important; font-size: 11.5px !important; line-height: 1.5 !important; }
.seller-returns-page .seller-return-status { min-height: 27px !important; padding: 5px 10px !important; font-size: 10.5px !important; font-weight: 700 !important; line-height: 1.3 !important; }
.seller-returns-page .seller-return-amount-value { margin: 0 !important; font-size: 12.5px !important; font-weight: 750 !important; }
.seller-returns-page .seller-return-action { padding: 15px 16px !important; }
.seller-returns-page .seller-return-input,
.seller-returns-page .seller-return-select,
.seller-returns-page .seller-return-button { height: 48px !important; min-height: 48px !important; border-radius: 14px !important; font-size: 11.5px !important; }
.seller-returns-page .seller-returns-footer { min-height: 64px !important; padding: 13px 20px !important; justify-content: space-between !important; text-align: left !important; }
.seller-returns-page .seller-returns-footer-count { font-size: 11px !important; font-weight: 550 !important; }
/* Style Laravel's actual page links, not its layout/count wrapper spans. */
.seller-returns-page .seller-returns-pagination nav a,
.seller-returns-page .seller-returns-pagination nav [aria-current="page"] > span,
.seller-returns-page .seller-returns-pagination nav [aria-disabled="true"] > span {
    display: inline-grid !important; place-items: center; width: 34px !important; min-width: 34px !important; height: 34px !important;
    padding: 0 !important; border-radius: 10px !important; margin: 0 3px !important; font-size: 11.5px !important;
}
.seller-returns-page .seller-returns-pagination nav svg { width: 12px !important; height: 12px !important; }
.seller-returns-page .seller-returns-pagination nav [aria-disabled="true"] { opacity: .42; }
/* Laravel's standard Tailwind paginator: one numbered navigation at all widths. */
.seller-returns-page .seller-returns-pagination nav > .sm\:hidden { display: none !important; }
.seller-returns-page .seller-returns-pagination nav > .sm\:flex { display: flex !important; }
.seller-returns-page .seller-returns-pagination nav > .sm\:flex > div:first-child { display: none !important; }
.seller-returns-page .seller-returns-pagination nav [aria-current="page"] { background: transparent !important; border: 0 !important; }
.seller-returns-page .seller-returns-pagination nav [aria-current="page"] > span { background: #C88912 !important; border: 1px solid #C88912 !important; color: #FFF !important; font-weight: 700 !important; }
@media (max-width: 1180px) { .seller-returns-page .seller-returns-summary { grid-template-columns: repeat(2,minmax(0,1fr)) !important; } }
@media (max-width: 900px) {
    .seller-returns-page .seller-returns-filter-form { grid-template-columns: repeat(2,minmax(0,1fr)) !important; }
    .seller-returns-page .seller-returns-search-field,
    .seller-returns-page .seller-returns-status-dropdown { grid-column: 1 / -1 !important; }
    .seller-returns-page .seller-returns-apply-filter,
    .seller-returns-page .seller-returns-reset-filter { width: 100% !important; }
}
@media (max-width: 767px) {
    .seller-returns-page .seller-returns-title { font-size: 25px !important; }
    .seller-returns-page .seller-returns-eyebrow { font-size: 9.5px !important; }
    .seller-returns-page .seller-returns-stat-label { font-size: 12.5px !important; }
    .seller-returns-page .seller-returns-stat-note { font-size: 11px !important; }
    .seller-returns-page .seller-return-review-form { grid-template-columns: 1fr !important; }
    .seller-returns-page .seller-return-review-form .seller-return-button { width: 100% !important; }
}
@media (max-width: 540px) {
    .seller-returns-page .seller-returns-summary { grid-template-columns: 1fr !important; gap: 11px !important; }
    .seller-returns-page .seller-returns-stat { min-height: 100px !important; }
    .seller-returns-page .seller-returns-records-header { flex-direction: column !important; align-items: flex-start !important; padding: 17px 16px !important; gap: 6px !important; }
    .seller-returns-page .seller-returns-footer { flex-direction: column !important; align-items: flex-start !important; gap: 12px !important; }
    .seller-returns-page .seller-returns-pagination { align-self: flex-end; }
}
@media (prefers-reduced-motion: reduce) { .seller-returns-page * { transition-duration: .01ms !important; animation-duration: .01ms !important; } }
@media print {
    .seller-returns-page .seller-returns-stat,
    .seller-returns-page .seller-returns-records { box-shadow: none !important; }
    .seller-returns-page .seller-returns-table-scroll { overflow: visible; }
}

@endverbatim
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

                        <option value="{{ $tab }}" @selected($status === $tab)>{{ $tab === 'all' ? 'All Status' : $label }}</option>

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

                        <span data-returns-status-label>All Status</span>

                    </span>

                    <svg class="seller-returns-status-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">

                        <path d="m7 10 5 5 5-5"></path>

                    </svg>

                </button>

                <div class="seller-returns-status-menu" role="listbox" aria-label="Return status" hidden data-returns-status-menu>

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

                            role="option"

                            aria-selected="false"

                            data-returns-status-value="{{ $value }}"

                        >

                            <span class="seller-returns-status-option-copy">

                                <strong>{{ $label }}</strong>

                                <small>{{ $description }}</small>

                            </span>

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

    <section class="seller-returns-records" aria-label="Return requests">

        <header class="seller-returns-records-header">

            <div>

                <h2 class="seller-returns-records-title">Return Requests</h2>

                <p class="seller-returns-records-subtitle">Review and manage the latest return requests.</p>

            </div>

            <span class="seller-returns-records-count">Showing {{ $returns->count() }} of {{ method_exists($returns, 'total') ? $returns->total() : $returns->count() }}</span>

        </header>

        <div class="seller-returns-table-scroll" tabindex="0" aria-label="Return requests, scroll horizontally to see all columns">
        @if($returns->count() > 0)
        <div class="seller-returns-table-head" aria-hidden="true">
            <span>Order / Reason</span>
            <span>Details</span>
            <span>Status</span>
            <span>Requested refund</span>
        </div>
        @endif
        <div class="seller-returns-list">

        @forelse($returns as $return)

            <article class="seller-return-card">

                <div class="seller-return-card-main">
                    <div class="min-w-0">
                        <p class="seller-return-order-number">{{ $return->order?->order_number ?: 'Order unavailable' }}</p>
                        <p class="seller-return-reason">{{ $return->reason }}</p>
                    </div>
                    <p class="seller-return-details">{{ $return->details ?: 'No additional details.' }}</p>
                    <div>
                        <span class="seller-return-status {{ $returnStatusClass($return->status) }}">{{ ucfirst($return->status) }}</span>
                    </div>
                    <div class="seller-return-amount">
                        <span class="sr-only">Requested refund</span>
                        <div class="seller-return-amount-value">₱{{ number_format((float) $return->requested_amount, 2) }}</div>
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

        </div>

        </div>{{-- /seller-returns-table-scroll --}}

        <footer class="seller-returns-footer">

            <span class="seller-returns-footer-count">Showing {{ $returns->firstItem() ?? 0 }}–{{ $returns->lastItem() ?? 0 }} of {{ method_exists($returns, 'total') ? $returns->total() : $returns->count() }} returns</span>

            @if($returns->hasPages())

                <div class="seller-returns-pagination">

                    {{ $returns->links() }}

                </div>

            @endif

        </footer>

    </section>

</div>

@endsection

@push('scripts')

<script>

(function () {

    window.__SARI_SELLER_RETURNS_LIFECYCLE__?.abort();

    const lifecycle = new AbortController();
    const { signal } = lifecycle;

    window.__SARI_SELLER_RETURNS_LIFECYCLE__ = lifecycle;

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

            }, { signal });

            document.addEventListener('keydown', (event) => {

                if (event.key === 'Escape' && !menu.hidden) {

                    close();

                    button.focus();

                }

            }, { signal });

            sync(nativeSelect.value || 'all');

        });

    }

    if (window.__SARI_SELLER_AFTER_PAINT__) {

        window.__SARI_SELLER_AFTER_PAINT__(initReturnsStatusDropdown);

    } else if (document.readyState === 'loading') {

        document.addEventListener('DOMContentLoaded', initReturnsStatusDropdown, { once: true, signal });

    } else {

        initReturnsStatusDropdown();

    }

    const cleanup = () => {
        lifecycle.abort();

        if (window.__SARI_SELLER_RETURNS_LIFECYCLE__ === lifecycle) {
            window.__SARI_SELLER_RETURNS_LIFECYCLE__ = null;
        }
    };

    document.addEventListener('livewire:navigating', cleanup, { once: true, signal });
    window.addEventListener('beforeunload', cleanup, { once: true, signal });

})();

</script>

@endpush

