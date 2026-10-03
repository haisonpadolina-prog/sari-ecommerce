@extends('layouts.seller')







@section('title', 'Shipping — SARI Seller')



@section('page-title', 'Shipping')





@push('styles')

<style id="sariSellerShippingProfessionalStyles">

    .seller-shipping-page {

        --ship-gold: #d59617;

        --ship-gold-hover: #c9890f;

        --ship-gold-soft: #fffaf0;

        --ship-ink: #111827;

        --ship-text: #344054;

        --ship-muted: #7c8592;

        --ship-soft-muted: #98a2b3;

        --ship-line: #e5e7eb;

        width: 100%;

        max-width: 1440px;

        margin-inline: auto;

        padding-bottom: 28px;

        color: var(--ship-ink);

        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;

    }



    .seller-shipping-page a,

    .seller-shipping-page button,

    .seller-shipping-page input {

        font-family: inherit;

    }



    .seller-shipping-header {

        display: flex;

        min-height: 72px;

        align-items: flex-start;

        justify-content: space-between;

        gap: 24px;

        margin: 0 0 16px;

        padding: 2px 2px 16px;

        border-bottom: 1px solid #e7ebf0;

    }



    .seller-shipping-heading { min-width: 0; }

    .seller-shipping-eyebrow {

        margin: 0;

        color: #b87605;

        font-size: 8px;

        font-weight: 800;

        line-height: 1;

        letter-spacing: .16em;

        text-transform: uppercase;

    }



    .seller-shipping-title {

        margin: 6px 0 0;

        color: var(--ship-ink);

        font-size: clamp(30px, 2.35vw, 38px);

        font-weight: 650;

        line-height: 1;

        letter-spacing: -.035em;

    }



    .seller-shipping-title span { color: #C9890B; }



    .seller-shipping-subtitle {

        max-width: 720px;

        margin: 8px 0 0;

        color: var(--ship-muted);

        font-size: 10.5px;

        line-height: 1.55;

    }



    .seller-shipping-header-actions {

        display: flex;

        flex: 0 0 auto;

        flex-wrap: wrap;

        justify-content: flex-end;

        gap: 8px;

        padding-top: 2px;

    }



    .seller-shipping-header-action {

        display: inline-flex;

        height: 42px;

        align-items: center;

        justify-content: center;

        gap: 8px;

        border: 1px solid #dfe3e8;

        border-radius: 12px;

        background: #fff;

        padding: 0 14px;

        color: #4b5563;

        font-size: 10px;

        font-weight: 650;

        text-decoration: none;

        box-shadow: 0 5px 14px rgba(15, 23, 42, .035);

        transition: border-color .14s ease, background-color .14s ease, color .14s ease;

    }



    .seller-shipping-header-action:hover,

    .seller-shipping-header-action:focus-visible {

        outline: none;

        border-color: #e2bd67;

        background: var(--ship-gold-soft);

        color: #9b6505;

    }



    .seller-shipping-header-action--primary {

        border-color: var(--ship-gold);

        background: var(--ship-gold);

        color: #fff;

        box-shadow: 0 7px 16px rgba(213, 150, 23, .14);

    }



    .seller-shipping-header-action--primary:hover,

    .seller-shipping-header-action--primary:focus-visible {

        border-color: var(--ship-gold-hover);

        background: var(--ship-gold-hover);

        color: #fff;

    }



    .seller-shipping-header-action svg { width: 14px; height: 14px; }



    .seller-shipping-summary {

        display: grid;

        grid-template-columns: repeat(4, minmax(0, 1fr));

        gap: 12px;

        margin-top: 0;

    }



    .seller-shipping-stat {

        min-width: 0;

        min-height: 94px;

        border: 1px solid var(--ship-line);

        border-radius: 16px;

        background: #fff;

        padding: 14px 15px;

        box-shadow: 0 8px 22px rgba(15, 23, 42, .035);

    }



    .seller-shipping-stat > div { height: 100%; }

    .seller-shipping-stat p:first-child {

        color: #667085;

        font-size: 10px;

        font-weight: 600;

        line-height: 1.3;

    }

    .seller-shipping-stat p:nth-child(2) {

        margin-top: 5px;

        color: var(--ship-ink);

        font-size: 24px;

        font-weight: 700;

        line-height: 1;

        letter-spacing: -.035em;

    }

    .seller-shipping-stat p:nth-child(3) {

        margin-top: 7px;

        color: var(--ship-soft-muted);

        font-size: 8.5px;

        line-height: 1.4;

    }



    .seller-shipping-stat-icon {

        display: grid;

        width: 36px;

        height: 36px;

        flex: 0 0 36px;

        place-items: center;

        border: 1px solid rgba(17, 24, 39, .05);

        border-radius: 10px;

    }

    .seller-shipping-stat-icon svg { width: 15px; height: 15px; }



    .seller-shipping-filter-panel {

        margin-top: 14px;

        border: 1px solid #e5e7eb;

        border-radius: 22px;

        background: #f8fafc;

        padding: 12px;

        box-shadow: 0 8px 22px rgba(15, 23, 42, .035);

    }



    .seller-shipping-filter-form {

        display: grid;

        grid-template-columns: minmax(320px, 1fr) minmax(190px, 240px) 155px 88px;

        gap: 10px;

        align-items: center;

    }



    .seller-shipping-search-field {

        position: relative;

        min-width: 0;

    }



    .seller-shipping-search-field svg {

        position: absolute;

        left: 18px;

        top: 50%;

        width: 17px;

        height: 17px;

        transform: translateY(-50%);

        color: #98a2b3;

        pointer-events: none;

    }



    .seller-shipping-search-input,

    .seller-shipping-status-select,

    .seller-shipping-apply-filter,

    .seller-shipping-reset-filter {

        width: 100%;

        height: 54px;

        min-height: 54px;

        border-radius: 14px;

        font-family: inherit;

    }



    .seller-shipping-search-input,

    .seller-shipping-status-select {

        border: 1px solid #d8dee6;

        background: #fff;

        color: #344054;

        font-size: 12px;

        outline: none;

        box-shadow: 0 1px 2px rgba(15, 23, 42, .015);

        transition: border-color .14s ease, box-shadow .14s ease, background-color .14s ease;

    }



    .seller-shipping-search-input {

        padding: 0 16px 0 50px;

    }



    .seller-shipping-search-input::placeholder {

        color: #98a2b3;

        font-weight: 400;

    }



    .seller-shipping-status-select {

        appearance: auto;

        padding: 0 14px;

        font-weight: 650;

    }



    .seller-shipping-search-input:focus,

    .seller-shipping-status-select:focus {

        border-color: #d59617;

        background: #fff;

        box-shadow: 0 0 0 4px rgba(213, 150, 23, .09);

    }



    .seller-shipping-apply-filter,

    .seller-shipping-reset-filter {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        text-decoration: none;

        font-size: 11px;

        font-weight: 700;

        transition: border-color .14s ease, background-color .14s ease, color .14s ease;

    }



    .seller-shipping-apply-filter {

        gap: 8px;

        border: 1px solid var(--ship-gold);

        background: var(--ship-gold);

        padding: 0 16px;

        color: #fff;

        box-shadow: 0 8px 18px rgba(213, 150, 23, .16);

    }



    .seller-shipping-apply-filter:hover,

    .seller-shipping-apply-filter:focus-visible {

        outline: none;

        border-color: var(--ship-gold-hover);

        background: var(--ship-gold-hover);

    }



    .seller-shipping-apply-filter svg {

        width: 14px;

        height: 14px;

    }



    .seller-shipping-reset-filter {

        border: 1px solid #d8dee6;

        background: #fff;

        padding: 0 14px;

        color: #667085;

        box-shadow: none;

    }



    .seller-shipping-reset-filter:hover,

    .seller-shipping-reset-filter:focus-visible {

        outline: none;

        border-color: #cfd6df;

        background: #f9fafb;

        color: #344054;

    }



    .seller-shipping-workspace {

        margin-top: 12px;

        overflow: hidden;

        border: 1px solid var(--ship-line);

        border-radius: 16px;

        background: #fff;

        box-shadow: 0 10px 26px rgba(15, 23, 42, .035);

    }



    .seller-shipping-list {

        display: grid;

        gap: 0;

        padding: 0;

        background: #fff;

    }



    .seller-shipping-card {

        border: 0;

        border-bottom: 1px solid #edf0f3;

        border-radius: 0;

        background: #fff;

        padding: 16px;

        transition: background-color .14s ease;

    }

    .seller-shipping-card:last-child { border-bottom: 0; }

    .seller-shipping-card:hover { background: #fffdf8; }



    .seller-shipping-order-number {

        color: #1f2937;

        font-size: 12px;

        font-weight: 700;

        letter-spacing: -.02em;

        text-decoration: none;

    }

    .seller-shipping-order-number:hover { color: #a96f06; }



    .seller-shipping-progress-label {

        color: #667085;

        font-size: 8.5px;

        font-weight: 600;

    }



    .seller-shipping-meta-card {

        min-width: 0;

        border: 1px solid #e7e9ee;

        border-radius: 12px;

        background: #fafbfc;

        padding: 11px 12px;

    }



    .seller-shipping-card-footer {

        margin-top: 14px;

        border-top: 1px solid #edf0f3;

        padding-top: 13px;

    }



    .seller-shipping-waybill-button,

    .seller-shipping-track-button {

        display: inline-flex;

        height: 36px;

        align-items: center;

        justify-content: center;

        border-radius: 10px;

        padding: 0 12px;

        font-size: 8.5px;

        font-weight: 650;

        text-decoration: none;

    }

    .seller-shipping-waybill-button {

        border: 1px solid #dfe3e8;

        background: #fff;

        color: #667085;

    }

    .seller-shipping-waybill-button:hover {

        border-color: #efd9a7;

        background: #fffaf0;

        color: #9b6505;

    }

    .seller-shipping-track-button {

        gap: 7px;

        border: 1px solid var(--ship-gold);

        background: var(--ship-gold);

        color: #fff;

        box-shadow: 0 6px 14px rgba(213,150,23,.13);

    }

    .seller-shipping-track-button:hover { background: var(--ship-gold-hover); border-color: var(--ship-gold-hover); }



    .seller-shipping-empty {

        padding: 44px 24px 46px;

        text-align: center;

        background: #fff;

    }

    .seller-shipping-empty-icon {

        display: grid;

        width: 42px;

        height: 42px;

        margin-inline: auto;

        place-items: center;

        border: 1px solid #e5e7eb;

        border-radius: 12px;

        background: #fafbfc;

        color: #98a2b3;

    }

    .seller-shipping-empty-icon svg { width: 18px; height: 18px; }



    @media (max-width: 1100px) {

        .seller-shipping-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }

    }



    @media (max-width: 900px) {

        .seller-shipping-header { flex-direction: column; gap: 13px; }

        .seller-shipping-header-actions { width: 100%; justify-content: flex-start; padding-top: 0; }

    }



    @media (max-width: 640px) {

        .seller-shipping-title {

            font-size: clamp(28px, 8vw, 34px);

        }

        .seller-shipping-header-actions {

            display: grid;

            grid-template-columns: repeat(2, minmax(0,1fr));

        }

        .seller-shipping-header-action { width: 100%; }

        .seller-shipping-summary { grid-template-columns: 1fr; }

        .seller-shipping-card { padding: 14px; }

    }



    @media (max-width: 1100px) {

        .seller-shipping-filter-form {

            grid-template-columns: minmax(0, 1fr) 190px 145px 82px;

        }

    }



    @media (max-width: 860px) {

        .seller-shipping-filter-form {

            grid-template-columns: minmax(0, 1fr) minmax(170px, .45fr);

        }



        .seller-shipping-apply-filter,

        .seller-shipping-reset-filter {

            width: 100%;

        }

    }



    @media (max-width: 640px) {

        .seller-shipping-filter-panel {

            border-radius: 18px;

            padding: 10px;

        }



        .seller-shipping-filter-form {

            grid-template-columns: 1fr;

            gap: 9px;

        }



        .seller-shipping-search-input,

        .seller-shipping-status-select,

        .seller-shipping-apply-filter,

        .seller-shipping-reset-filter {

            height: 50px;

            min-height: 50px;

            border-radius: 13px;

            font-size: 10.5px;

        }

    }



    @media (prefers-reduced-motion: reduce) {

        .seller-shipping-page * { transition-duration: .01ms !important; scroll-behavior: auto !important; }

    }


/* ============================================================
   SHIPPING TRACKING — COMPACT + READABLE FINAL LAYER
   Compact in spacing, not in legibility.
   Search/filter, shipping states, tracking, waybill, pagination,
   routes, and logistics data remain unchanged.
   ============================================================ */

.seller-shipping-page {
    max-width: 1440px !important;
    padding-bottom: 22px !important;
}

/* Header — same compact Seller page hierarchy. */
.seller-shipping-header {
    min-height: 66px !important;
    gap: 18px !important;
    margin-bottom: 12px !important;
    padding: 2px 2px 13px !important;
}

.seller-shipping-eyebrow {
    font-size: 8px !important;
    letter-spacing: .16em !important;
}

.seller-shipping-title {
    margin-top: 5px !important;
    font-size: clamp(29px, 2.2vw, 36px) !important;
    font-weight: 650 !important;
}

.seller-shipping-subtitle {
    margin-top: 7px !important;
    font-size: 10px !important;
    line-height: 1.5 !important;
}

.seller-shipping-header-actions {
    gap: 7px !important;
}

.seller-shipping-header-action {
    height: 38px !important;
    min-height: 38px !important;
    border-radius: 10px !important;
    padding-inline: 11px !important;
    font-size: 9.5px !important;
}

/* Summary cards — shorter but readable. */
.seller-shipping-summary {
    gap: 9px !important;
}

.seller-shipping-stat {
    min-height: 78px !important;
    border-radius: 13px !important;
    padding: 10px 12px !important;
    box-shadow: 0 5px 16px rgba(15, 23, 42, .028) !important;
}

.seller-shipping-stat p:first-child {
    font-size: 9.5px !important;
    line-height: 1.3 !important;
}

.seller-shipping-stat p:nth-child(2) {
    margin-top: 3px !important;
    font-size: 21px !important;
}

.seller-shipping-stat p:nth-child(3) {
    margin-top: 5px !important;
    font-size: 8.5px !important;
    line-height: 1.35 !important;
}

.seller-shipping-stat-icon {
    width: 32px !important;
    height: 32px !important;
    flex-basis: 32px !important;
    border-radius: 9px !important;
}

.seller-shipping-stat-icon svg {
    width: 14px !important;
    height: 14px !important;
}

/* Screenshot-style filter bar, just denser. */
.seller-shipping-filter-panel {
    margin-top: 10px !important;
    border-radius: 14px !important;
    padding: 9px !important;
    background: #f8fafc !important;
    box-shadow: 0 5px 16px rgba(15, 23, 42, .028) !important;
}

.seller-shipping-filter-form {
    grid-template-columns: minmax(300px, 1fr) 165px 118px 66px !important;
    gap: 8px !important;
}

.seller-shipping-search-field svg {
    left: 12px !important;
    width: 14px !important;
    height: 14px !important;
}

.seller-shipping-search-input,
.seller-shipping-status-select,
.seller-shipping-apply-filter,
.seller-shipping-reset-filter {
    height: 40px !important;
    min-height: 40px !important;
    border-radius: 9px !important;
    font-size: 9.8px !important;
}

.seller-shipping-search-input {
    padding: 0 11px 0 36px !important;
}

.seller-shipping-status-select {
    padding-inline: 10px !important;
}

.seller-shipping-apply-filter {
    gap: 6px !important;
    padding-inline: 11px !important;
}

.seller-shipping-apply-filter svg {
    width: 12px !important;
    height: 12px !important;
}

.seller-shipping-reset-filter {
    padding-inline: 10px !important;
}

/* Shipment workspace and rows. */
.seller-shipping-workspace {
    margin-top: 10px !important;
    border-radius: 13px !important;
    box-shadow: 0 6px 18px rgba(15, 23, 42, .03) !important;
}

.seller-shipping-card {
    padding: 12px !important;
}

.seller-shipping-card > .flex {
    gap: 12px !important;
}

.seller-shipping-order-number {
    font-size: 10.8px !important;
}

.seller-shipping-card .inline-flex.rounded-full {
    padding: 3px 7px !important;
    font-size: 7.8px !important;
}

.seller-shipping-card .mt-2.max-w-\[760px\] {
    margin-top: 5px !important;
    font-size: 8.8px !important;
    line-height: 1.5 !important;
}

.seller-shipping-card .mt-4 {
    margin-top: 9px !important;
}

.seller-shipping-progress-label {
    font-size: 8.8px !important;
}

.seller-shipping-card .mt-2.h-1\.5 {
    margin-top: 6px !important;
    height: 5px !important;
}

.seller-shipping-card .mt-2.grid.grid-cols-5 {
    margin-top: 6px !important;
    font-size: 8px !important;
    line-height: 1.3 !important;
}

.seller-shipping-card > .flex > .grid {
    gap: 7px !important;
}

@media (min-width: 1280px) {
    .seller-shipping-card > .flex > .grid {
        width: 500px !important;
    }
}

/* Meta cards: reduce padding, increase tiny labels slightly. */
.seller-shipping-meta-card {
    border-radius: 10px !important;
    padding: 8px 9px !important;
}

.seller-shipping-meta-card p:first-child {
    font-size: 7.8px !important;
    letter-spacing: .08em !important;
}

.seller-shipping-meta-card p:nth-child(2) {
    margin-top: 4px !important;
    font-size: 9px !important;
    line-height: 1.35 !important;
}

.seller-shipping-meta-card p:nth-child(3) {
    margin-top: 3px !important;
    font-size: 8.2px !important;
    line-height: 1.35 !important;
}

/* Footer: shorter and easier to scan. */
.seller-shipping-card-footer {
    margin-top: 9px !important;
    padding-top: 9px !important;
}

.seller-shipping-card-footer > div:first-child {
    gap: 8px 12px !important;
    font-size: 8.3px !important;
}

.seller-shipping-card-footer > div:first-child strong {
    font-size: 8.5px !important;
}

.seller-shipping-card-footer > div:last-child {
    gap: 6px !important;
}

.seller-shipping-waybill-button,
.seller-shipping-track-button {
    height: 34px !important;
    min-height: 34px !important;
    border-radius: 9px !important;
    padding-inline: 10px !important;
    font-size: 8.5px !important;
}

/* Empty state and paginator density. */
.seller-shipping-empty {
    padding: 32px 22px 34px !important;
}

.seller-shipping-empty-icon {
    width: 38px !important;
    height: 38px !important;
    border-radius: 10px !important;
}

.seller-shipping-empty h3 {
    margin-top: 10px !important;
    font-size: 10.5px !important;
}

.seller-shipping-empty p {
    margin-top: 4px !important;
    font-size: 8.8px !important;
    line-height: 1.5 !important;
}

.seller-shipping-workspace > .border-t {
    padding-top: 10px !important;
    padding-bottom: 10px !important;
}

/* Focus remains visible after compacting the controls. */
.seller-shipping-search-input:focus,
.seller-shipping-status-select:focus {
    box-shadow: 0 0 0 3px rgba(213, 150, 23, .08) !important;
}

@media (max-width: 1100px) {
    .seller-shipping-filter-form {
        grid-template-columns: minmax(0, 1fr) 155px 112px 62px !important;
    }

    .seller-shipping-summary {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 860px) {
    .seller-shipping-filter-form {
        grid-template-columns: minmax(0, 1fr) minmax(155px, .42fr) !important;
    }

    .seller-shipping-apply-filter,
    .seller-shipping-reset-filter {
        width: 100% !important;
    }
}

@media (max-width: 640px) {
    .seller-shipping-header {
        gap: 10px !important;
        min-height: 0 !important;
    }

    .seller-shipping-title {
        font-size: clamp(28px, 8vw, 34px) !important;
    }

    .seller-shipping-header-action {
        height: 40px !important;
        min-height: 40px !important;
    }

    .seller-shipping-summary {
        grid-template-columns: 1fr !important;
    }

    .seller-shipping-stat {
        min-height: 74px !important;
    }

    .seller-shipping-filter-panel {
        border-radius: 13px !important;
        padding: 8px !important;
    }

    .seller-shipping-filter-form {
        grid-template-columns: 1fr !important;
        gap: 7px !important;
    }

    .seller-shipping-search-input,
    .seller-shipping-status-select,
    .seller-shipping-apply-filter,
    .seller-shipping-reset-filter {
        height: 42px !important;
        min-height: 42px !important;
        border-radius: 9px !important;
        font-size: 10px !important;
    }

    .seller-shipping-card {
        padding: 10px !important;
    }

    .seller-shipping-meta-card {
        padding: 8px !important;
    }

    .seller-shipping-waybill-button,
    .seller-shipping-track-button {
        height: 36px !important;
        min-height: 36px !important;
    }
}


/* ============================================================
   SHIPPING — CLEAN HEADER + PREMIUM STATUS DROPDOWN
   ============================================================ */

.seller-shipping-header {
    justify-content: flex-start !important;
}

.seller-shipping-status-dropdown {
    position: relative;
    min-width: 0;
}

.seller-shipping-status-native {
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

.seller-shipping-status-button {
    display: flex;
    width: 100%;
    height: 40px;
    min-height: 40px;
    align-items: center;
    justify-content: space-between;
    gap: 9px;
    border: 1px solid #d8dee6;
    border-radius: 9px;
    background: #fff;
    padding: 0 10px 0 8px;
    color: #344054;
    font-family: inherit;
    font-size: 9.8px;
    font-weight: 650;
    text-align: left;
    cursor: pointer;
    outline: none;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .025);
    transition: border-color .14s ease, box-shadow .14s ease, background-color .14s ease;
}

.seller-shipping-status-button:hover {
    border-color: #cfd6df;
    background: #fdfefe;
}

.seller-shipping-status-button:focus-visible,
.seller-shipping-status-button[aria-expanded="true"] {
    border-color: #d59617;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(213, 150, 23, .08);
}

.seller-shipping-status-button-main {
    display: inline-flex;
    min-width: 0;
    align-items: center;
    gap: 7px;
}

.seller-shipping-status-button-icon {
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

.seller-shipping-status-button-icon svg {
    width: 12px;
    height: 12px;
}

.seller-shipping-status-chevron {
    width: 13px;
    height: 13px;
    flex: 0 0 auto;
    color: #98a2b3;
    transition: transform .15s ease, color .15s ease;
}

.seller-shipping-status-button[aria-expanded="true"] .seller-shipping-status-chevron {
    transform: rotate(180deg);
    color: #b97805;
}

.seller-shipping-status-menu {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    z-index: 90;
    min-width: 230px;
    overflow: hidden;
    border: 1px solid #e2e7ed;
    border-radius: 12px;
    background: rgba(255, 255, 255, .985);
    padding: 5px;
    box-shadow: 0 18px 40px rgba(15, 23, 42, .12), 0 3px 9px rgba(15, 23, 42, .045);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.seller-shipping-status-menu[hidden] {
    display: none !important;
}

.seller-shipping-status-option {
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

.seller-shipping-status-option:hover,
.seller-shipping-status-option:focus-visible {
    outline: none;
    background: #f8fafc;
}

.seller-shipping-status-option.is-selected {
    background: #fff9ec;
}

.seller-shipping-status-option-copy {
    display: block;
    min-width: 0;
}

.seller-shipping-status-option-copy strong {
    display: block;
    color: #344054;
    font-size: 9.3px;
    font-weight: 700;
    line-height: 1.3;
}

.seller-shipping-status-option.is-selected .seller-shipping-status-option-copy strong {
    color: #9b6505;
}

.seller-shipping-status-option-copy small {
    display: block;
    margin-top: 2px;
    color: #98a2b3;
    font-size: 7.8px;
    font-weight: 400;
    line-height: 1.35;
}

.seller-shipping-status-check {
    width: 14px;
    height: 14px;
    flex: 0 0 auto;
    color: #c9890f;
    opacity: 0;
}

.seller-shipping-status-option.is-selected .seller-shipping-status-check {
    opacity: 1;
}

@media (max-width: 640px) {
    .seller-shipping-status-button {
        height: 42px;
        min-height: 42px;
        font-size: 10px;
    }

    .seller-shipping-status-menu {
        min-width: 100%;
    }
}

</style>

@endpush









@section('content')



@php



    $toneClasses = [



        'success' => 'border-[#cfe4d7] bg-[#f1f8f4] text-[#4f7d63]',



        'info' => 'border-[#cfdeea] bg-[#f1f7fb] text-[#3c6e91]',



        'hub' => 'border-[#d8d8ea] bg-[#f5f5fb] text-[#63638d]',



        'warning' => 'border-[#eadfc9] bg-[#fff9ef] text-[#a8731f]',



    ];

@endphp







<div class="seller-shipping-page">



    <section class="seller-shipping-header">



        <div class="seller-shipping-heading">



            <p class="seller-shipping-eyebrow">



                Fulfillment & Logistics



            </p>



            <h1 class="seller-shipping-title">Shipping <span>Tracking</span></h1>



            <p class="seller-shipping-subtitle">



                Follow Seller parcels from pickup verification to Logistics hub intake,



                sorting, final delivery, and completion.



            </p>



        </div>







        



    </section>







    <section class="seller-shipping-summary">



        @foreach([
            ['Active Shipments', $stats['active'], 'Truck in fulfillment', 'text-[#b97805]', 'bg-[#fff7e6]', 'truck'],
            ['Awaiting Pickup', $stats['awaiting_pickup'], 'Seller / Rider handoff', 'text-[#a8731f]', 'bg-[#fff9ef]', 'package'],
            ['In Transit', $stats['in_transit'], 'Hub and final-mile movement', 'text-[#3c6e91]', 'bg-[#f1f7fb]', 'route'],
            ['Delivered', $stats['delivered'], 'Completed shipments', 'text-[#4f7d63]', 'bg-[#f1f8f4]', 'check'],
        ] as [$label, $value, $caption, $textTone, $iconTone, $icon])



            <article class="seller-shipping-stat">



                <div class="flex items-start justify-between gap-3">



                    <div>



                        <p class="text-[9px] font-semibold text-[#7b8089]">{{ $label }}</p>



                        <p class="mt-2 text-[25px] font-semibold tracking-[-.04em] text-[#202124]">



                            {{ number_format($value) }}



                        </p>



                        <p class="mt-2 text-[8px] leading-4 text-[#9aa0a6]">{{ $caption }}</p>



                    </div>







                    <span class="seller-shipping-stat-icon {{ $iconTone }} {{ $textTone }}" aria-hidden="true">
                        @if ($icon === 'truck')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 7h11v10H3z"></path>
                                <path d="M14 10h3l4 4v3h-7z"></path>
                                <circle cx="7" cy="18" r="1.5"></circle>
                                <circle cx="18" cy="18" r="1.5"></circle>
                            </svg>
                        @elseif ($icon === 'package')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="m4 7 8-4 8 4-8 4-8-4Z"></path>
                                <path d="M4 7v10l8 4 8-4V7"></path>
                                <path d="M12 11v10"></path>
                            </svg>
                        @elseif ($icon === 'route')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="6" cy="18" r="2"></circle>
                                <circle cx="18" cy="6" r="2"></circle>
                                <path d="M8 18h3a3 3 0 0 0 3-3v-3a3 3 0 0 1 3-3h1"></path>
                                <path d="m15 6 3-3 3 3"></path>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="m8 12 2.5 2.5L16 9"></path>
                            </svg>
                        @endif
                    </span>



                </div>



            </article>



        @endforeach



    </section>







    <section class="seller-shipping-filter-panel" aria-label="Shipment filters">

        <form method="GET" action="{{ route('seller.shipping.index') }}" class="seller-shipping-filter-form">

            <label class="seller-shipping-search-field">

                <span class="sr-only">Search shipments</span>



                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">

                    <circle cx="11" cy="11" r="7"></circle>

                    <path d="m20 20-4-4"></path>

                </svg>



                <input

                    type="search"

                    name="q"

                    value="{{ $search }}"

                    placeholder="Search order ID, buyer, product..."

                    autocomplete="off"

                    class="seller-shipping-search-input"

                >

            </label>



            <div class="seller-shipping-status-dropdown" data-shipping-status-dropdown>
                <select
                    name="status"
                    class="seller-shipping-status-native"
                    aria-hidden="true"
                    tabindex="-1"
                    data-shipping-status-native
                >
                    <option value="all" @selected($filter === 'all')>All Status</option>
                    <option value="awaiting_pickup" @selected($filter === 'awaiting_pickup')>Awaiting Pickup</option>
                    <option value="in_transit" @selected($filter === 'in_transit')>In Transit</option>
                    <option value="delivered" @selected($filter === 'delivered')>Delivered</option>
                </select>

                <button
                    type="button"
                    class="seller-shipping-status-button"
                    aria-haspopup="listbox"
                    aria-expanded="false"
                    data-shipping-status-button
                >
                    <span class="seller-shipping-status-button-main">
                        <span class="seller-shipping-status-button-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 7h16"></path>
                                <path d="M7 12h10"></path>
                                <path d="M10 17h4"></path>
                            </svg>
                        </span>
                        <span data-shipping-status-label>All Status</span>
                    </span>

                    <svg class="seller-shipping-status-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="m7 10 5 5 5-5"></path>
                    </svg>
                </button>

                <div
                    class="seller-shipping-status-menu"
                    role="listbox"
                    hidden
                    data-shipping-status-menu
                >
                    <button type="button" class="seller-shipping-status-option" data-shipping-status-value="all">
                        <span class="seller-shipping-status-option-copy">
                            <strong>All Status</strong>
                            <small>Show every shipment</small>
                        </span>
                        <svg class="seller-shipping-status-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="m6 12 4 4 8-8"></path>
                        </svg>
                    </button>

                    <button type="button" class="seller-shipping-status-option" data-shipping-status-value="awaiting_pickup">
                        <span class="seller-shipping-status-option-copy">
                            <strong>Awaiting Pickup</strong>
                            <small>Seller and rider handoff</small>
                        </span>
                        <svg class="seller-shipping-status-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="m6 12 4 4 8-8"></path>
                        </svg>
                    </button>

                    <button type="button" class="seller-shipping-status-option" data-shipping-status-value="in_transit">
                        <span class="seller-shipping-status-option-copy">
                            <strong>In Transit</strong>
                            <small>Moving through delivery</small>
                        </span>
                        <svg class="seller-shipping-status-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="m6 12 4 4 8-8"></path>
                        </svg>
                    </button>

                    <button type="button" class="seller-shipping-status-option" data-shipping-status-value="delivered">
                        <span class="seller-shipping-status-option-copy">
                            <strong>Delivered</strong>
                            <small>Completed shipments</small>
                        </span>
                        <svg class="seller-shipping-status-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="m6 12 4 4 8-8"></path>
                        </svg>
                    </button>
                </div>
            </div>



            <button type="submit" class="seller-shipping-apply-filter">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">

                    <path d="M4 7h16"></path>

                    <path d="M7 12h10"></path>

                    <path d="M10 17h4"></path>

                </svg>

                Apply Filter

            </button>



            <a href="{{ route('seller.shipping.index') }}" class="seller-shipping-reset-filter">

                Reset

            </a>

        </form>

    </section>



    <section class="seller-shipping-workspace">



        <div class="seller-shipping-list">



            @forelse($orders as $order)



                @php



                    $state = $shippingStates[$order->id];



                    $tone = $toneClasses[$state['tone']] ?? $toneClasses['warning'];



                    $parcel = $order->logisticsParcel;



                    $itemCount = collect($order->items ?? [])->sum(fn ($item) => (int) ($item['qty'] ?? 1));



                @endphp







                <article class="seller-shipping-card">



                    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">



                        <div class="min-w-0 flex-1">



                            <div class="flex flex-wrap items-center gap-2">



                                <a



                                    href="{{ route('seller.shipping.show', $order) }}"



                                    class="seller-shipping-order-number"



                                >



                                    {{ $order->order_number }}



                                </a>







                                <span class="inline-flex rounded-full border px-2.5 py-1 text-[7px] font-bold uppercase tracking-[.08em] {{ $tone }}">



                                    {{ $state['label'] }}



                                </span>



                            </div>







                            <p class="mt-2 max-w-[760px] text-[9px] leading-5 text-[#7b8089]">



                                {{ $state['detail'] }}



                            </p>







                            <div class="mt-4">



                                <div class="flex items-center justify-between gap-4">



                                    <span class="seller-shipping-progress-label">Shipment progress</span>



                                    <span class="text-[8px] font-bold text-[#b97805]">{{ $state['progress'] }}%</span>



                                </div>







                                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#eceff2]">



                                    <div



                                        class="h-full rounded-full bg-[#d89b10] transition-all duration-300"



                                        style="width: {{ $state['progress'] }}%"



                                    ></div>



                                </div>







                                <div class="mt-2 grid grid-cols-5 gap-1 text-center text-[7px] text-[#9aa0a6]">



                                    <span>Ready</span>



                                    <span>Pickup</span>



                                    <span>Hub</span>



                                    <span>Final Mile</span>



                                    <span>Delivered</span>



                                </div>



                            </div>



                        </div>







                        <div class="grid min-w-0 gap-3 sm:grid-cols-3 xl:w-[560px]">



                            <div class="seller-shipping-meta-card">



                                <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Buyer</p>



                                <p class="mt-1.5 truncate text-[9px] font-semibold text-[#374151]">



                                    {{ $order->buyer_name ?: 'SARI Buyer' }}



                                </p>



                                <p class="mt-1 text-[8px] text-[#8a919b]">



                                    {{ $itemCount }} item{{ $itemCount === 1 ? '' : 's' }}



                                </p>



                            </div>







                            <div class="seller-shipping-meta-card">



                                <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Assigned Rider</p>



                                <p class="mt-1.5 truncate text-[9px] font-semibold text-[#374151]">



                                    {{ $order->courier_name ?: 'Not assigned yet' }}



                                </p>



                                <p class="mt-1 truncate text-[8px] text-[#8a919b]">



                                    {{ $order->courier_email ?: 'Waiting for Logistics' }}



                                </p>



                            </div>







                            <div class="seller-shipping-meta-card">



                                <p class="text-[7px] font-bold uppercase tracking-[.1em] text-[#a2a7ae]">Logistics Hub</p>



                                <p class="mt-1.5 truncate text-[9px] font-semibold text-[#374151]">



                                    {{ $parcel ? ucwords(str_replace('_', ' ', $parcel->status)) : 'Not received yet' }}



                                </p>



                                <p class="mt-1 truncate text-[8px] text-[#8a919b]">



                                    {{ $parcel?->sorting_zone ?: 'No sorting zone yet' }}



                                </p>



                            </div>



                        </div>



                    </div>







                    <div class="seller-shipping-card-footer flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">



                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[8px] text-[#8a919b]">



                            <span>



                                Updated



                                <strong class="font-semibold text-[#606771]">{{ $order->updated_at?->diffForHumans() }}</strong>



                            </span>







                            @if($order->picked_up_at)



                                <span>



                                    Picked up



                                    <strong class="font-semibold text-[#606771]">{{ $order->picked_up_at->format('M d, h:i A') }}</strong>



                                </span>



                            @endif







                            @if($order->delivered_at)



                                <span>



                                    Delivered



                                    <strong class="font-semibold text-[#606771]">{{ $order->delivered_at->format('M d, h:i A') }}</strong>



                                </span>



                            @endif



                        </div>







                        <div class="flex items-center gap-2">



                            @if(Route::has('seller.orders.waybill'))



                                <a



                                    href="{{ route('seller.orders.waybill', $order) }}"



                                    class="seller-shipping-waybill-button"



                                >



                                    Waybill



                                </a>



                            @endif







                            <a



                                href="{{ route('seller.shipping.show', $order) }}"



                                class="seller-shipping-track-button"



                            >



                                Track Shipment



                                <span>→</span>



                            </a>



                        </div>



                    </div>



                </article>



            @empty



                <div class="seller-shipping-empty">



                    <span class="seller-shipping-empty-icon">



                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">



                            <path d="M3 7h11v10H3z"></path>



                            <path d="M14 10h3l4 4v3h-7z"></path>



                            <circle cx="7" cy="18" r="1.5"></circle>



                            <circle cx="18" cy="18" r="1.5"></circle>



                        </svg>



                    </span>







                    <h3 class="mt-4 text-[12px] font-semibold text-[#374151]">No shipments found</h3>



                    <p class="mt-2 text-[9px] leading-5 text-[#8a919b]">



                        Orders appear here after the Seller marks them ready for pickup.



                    </p>



                </div>



            @endforelse



        </div>







        @if($orders->hasPages())



            <div class="border-t border-[#eceef1] bg-white px-4 py-4 sm:px-5">



                {{ $orders->links() }}



            </div>



        @endif



    </section>



</div>



@endsection


@push('scripts')
<script>
(function () {
    function initShippingStatusDropdown() {
        document.querySelectorAll('[data-shipping-status-dropdown]').forEach((root) => {
            if (root.dataset.dropdownReady === '1') return;
            root.dataset.dropdownReady = '1';

            const nativeSelect = root.querySelector('[data-shipping-status-native]');
            const button = root.querySelector('[data-shipping-status-button]');
            const label = root.querySelector('[data-shipping-status-label]');
            const menu = root.querySelector('[data-shipping-status-menu]');
            const options = Array.from(root.querySelectorAll('[data-shipping-status-value]'));

            if (!nativeSelect || !button || !label || !menu || !options.length) return;

            const getLabel = (value) => {
                const match = Array.from(nativeSelect.options).find((option) => option.value === value);
                return match?.textContent?.trim() || 'All Status';
            };

            const sync = (value) => {
                nativeSelect.value = value;
                label.textContent = getLabel(value);

                options.forEach((option) => {
                    const selected = (option.dataset.shippingStatusValue || '') === value;
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
                    sync(option.dataset.shippingStatusValue || 'all');
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
        window.__SARI_SELLER_AFTER_PAINT__(initShippingStatusDropdown);
    } else if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initShippingStatusDropdown, { once: true });
    } else {
        initShippingStatusDropdown();
    }

    document.addEventListener('livewire:navigated', initShippingStatusDropdown);
})();
</script>
@endpush

