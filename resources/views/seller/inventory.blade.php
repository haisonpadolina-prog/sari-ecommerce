@extends('layouts.seller')



@section('title', 'Inventory — SARI Seller')

@section('page-title', 'Inventory')



@push('styles')
<style id="sariSellerEmbeddedCss-inventory" data-sari-inline-css="inventory">
@verbatim
/* Extracted from resources/views/seller/inventory.blade.php — original style block 1. */
.inventory-shell {

        --inv-bg: #f4f6f8;

        --inv-surface: #ffffff;

        --inv-surface-soft: #f8fafc;

        --inv-border: #e3e8ef;

        --inv-border-soft: #edf1f5;

        --inv-text: #111827;

        --inv-text-2: #475569;

        --inv-muted: #7c8797;

        --inv-gold: #d08c0b;

        --inv-green: #29a365;

        --inv-green-soft: #eaf8f0;

        --inv-amber: #e7a30b;

        --inv-amber-soft: #fff6df;

        --inv-red: #e33c45;

        --inv-red-soft: #fff0f1;

        --inv-blue: #2f7cf6;

        --inv-blue-soft: #edf4ff;

        width: 100%;

        max-width: 1700px;

        margin: 0 auto;

        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;

        color: var(--inv-text);

    }



    .inventory-shell * { box-sizing: border-box; }



    .inventory-head {

        display: grid;

        grid-template-columns: minmax(0, 1fr);

        gap: 24px;

        align-items: start;

    }



    .inventory-head--single {

        max-width: 820px;

    }



    .inventory-eyebrow {

        margin: 0;

        color: #a86f0b;

        font-size: 9.5px;

        font-weight: 800;

        line-height: 1;

        letter-spacing: .17em;

        text-transform: uppercase;

    }



    .inventory-title {

        margin: 7px 0 0;

        color: #1d1915;

        font-size: clamp(30px, 2.35vw, 38px);

        font-weight: 650;

        line-height: 1;

        letter-spacing: -.035em;

    }



    .inventory-subtitle {

        max-width: 680px;

        margin: 10px 0 0;

        color: #697586;

        font-size: 10.5px;

        font-weight: 400;

        line-height: 1.65;

    }



    .inventory-search-panel {

        border: 1px solid var(--inv-border);

        border-radius: 16px;

        background: #fff;

        padding: 14px 15px 13px;

        box-shadow: 0 7px 20px rgba(15, 23, 42, .035);

    }



    .inventory-search-title {

        display: flex;

        align-items: center;

        gap: 8px;

        color: #111827;

        font-size: 11px;

        font-weight: 700;

    }



    .inventory-search-title svg {

        width: 16px;

        height: 16px;

        stroke-width: 2;

    }



    .inventory-search-row {

        display: grid;

        grid-template-columns: minmax(0, 1fr) 82px;

        gap: 10px;

        margin-top: 9px;

    }



    .inventory-search-wrap { position: relative; }



    .inventory-search-wrap svg {

        position: absolute;

        top: 50%;

        left: 13px;

        width: 14px;

        height: 14px;

        transform: translateY(-50%);

        color: #7b8796;

        pointer-events: none;

    }



    .inventory-search-input,

    .inventory-table-search {

        width: 100%;

        height: 38px;

        border: 1px solid #d9e0e8;

        border-radius: 9px;

        background: #fff;

        color: #243041;

        outline: none;

        transition: border-color .12s ease, box-shadow .12s ease;

    }



    .inventory-search-input {

        padding: 0 12px 0 38px;

        font-size: 9px;

    }



    .inventory-search-input::placeholder,

    .inventory-table-search::placeholder { color: #94a0af; }



    .inventory-search-input:focus,

    .inventory-table-search:focus,

    .inventory-select:focus,

    .inventory-qty:focus {

        border-color: #c99835;

        box-shadow: 0 0 0 3px rgba(201, 152, 53, .08);

    }



    .inventory-search-button {

        border: 0;

        border-radius: 9px;

        background: #20252c;

        color: #fff;

        font-size: 8px;

        font-weight: 700;

        cursor: pointer;

        transition: background-color .12s ease, transform .12s ease;

    }



    .inventory-search-button:hover {

        background: #11161d;

        transform: translateY(-1px);

    }



    .inventory-update-btn {

        border: 1px solid #c88912;

        border-radius: 9px;

        background: #d89b10;

        color: #fff;

        font-size: 7.5px;

        font-weight: 700;

        cursor: pointer;

        box-shadow: 0 5px 12px rgba(191, 128, 8, .16);

        transition:

            background-color .12s ease,

            border-color .12s ease,

            box-shadow .12s ease,

            transform .12s ease,

            opacity .12s ease;

    }



    .inventory-update-btn:hover {

        border-color: #b77d0c;

        background: #c98d0e;

        box-shadow: 0 7px 16px rgba(175, 116, 8, .20);

        transform: translateY(-1px);

    }



    .inventory-update-btn:disabled {

        cursor: not-allowed;

        opacity: .48;

        box-shadow: none;

        transform: none;

    }



    .inventory-search-hint {

        margin: 8px 0 0;

        color: #7c8797;

        font-size: 8px;

        line-height: 1.5;

    }



    .inventory-stat-grid {

        display: grid;

        grid-template-columns: repeat(4, minmax(0, 1fr));

        gap: 12px;

        margin-top: 16px;

    }



    .inventory-stat-card {

        position: relative;

        display: block;

        min-height: 100px;

        border: 1px solid var(--inv-border);

        border-radius: 14px;

        background: #fff;

        padding: 16px 64px 15px 17px;

        box-shadow: 0 6px 18px rgba(15, 23, 42, .03);

    }



    .inventory-stat-icon {

        position: absolute;

        top: 14px;

        right: 14px;

        display: grid;

        width: 40px;

        height: 40px;

        place-items: center;

        border-radius: 11px;

    }



    .inventory-stat-icon svg { width: 20px; height: 20px; }



    .inventory-stat-icon--products { background: var(--inv-blue-soft); color: var(--inv-blue); }

    .inventory-stat-icon--units { background: #f1f4f7; color: #5c6876; }

    .inventory-stat-icon--low { background: var(--inv-amber-soft); color: #e18e00; }

    .inventory-stat-icon--out { background: var(--inv-red-soft); color: var(--inv-red); }



    .inventory-stat-label {

        margin: 0;

        color: #475569;

        font-size: 10px;

        font-weight: 600;

    }



    .inventory-stat-value {

        margin: 5px 0 0;

        color: #111827;

        font-size: 26px;

        font-weight: 700;

        line-height: 1;

        letter-spacing: -.03em;

    }



    .inventory-stat-note {

        margin: 7px 0 0;

        color: #7d8898;

        font-size: 8.5px;

        line-height: 1.45;

    }

.inventory-insight-grid {

        display: grid;

        grid-template-columns: minmax(0, 1.65fr) minmax(340px, .95fr);

        gap: 12px;

        margin-top: 14px;

    }



    .inventory-panel {

        border: 1px solid var(--inv-border);

        border-radius: 14px;

        background: #fff;

        box-shadow: 0 6px 18px rgba(15, 23, 42, .03);

    }



    .inventory-panel-head {

        display: flex;

        align-items: flex-start;

        gap: 10px;

        padding: 14px 16px 0;

    }



    .inventory-panel-icon {

        display: grid;

        width: 24px;

        height: 24px;

        flex: 0 0 24px;

        place-items: center;

        color: #111827;

    }



    .inventory-panel-icon svg { width: 17px; height: 17px; }



    .inventory-panel-title {

        margin: 0;

        color: #111827;

        font-size: 12px;

        font-weight: 700;

        line-height: 1.3;

    }



    .inventory-panel-subtitle {

        margin: 3px 0 0;

        color: #7c8797;

        font-size: 9px;

        line-height: 1.5;

    }



    .inventory-health-cards {

        display: grid;

        grid-template-columns: repeat(3, minmax(0, 1fr));

        gap: 10px;

        padding: 15px 16px 16px;

    }



    .inventory-health-card {

        border: 1px solid var(--inv-border-soft);

        border-radius: 10px;

        background: #fff;

        padding: 12px 12px 11px;

    }



    .inventory-health-label {

        display: flex;

        align-items: center;

        gap: 8px;

        color: #263244;

        font-size: 10px;

        font-weight: 650;

    }



    .inventory-health-dot {

        width: 8px;

        height: 8px;

        border-radius: 999px;

    }



    .inventory-health-dot--healthy { background: var(--inv-green); }

    .inventory-health-dot--low { background: var(--inv-amber); }

    .inventory-health-dot--out { background: var(--inv-red); }



    .inventory-health-value {

        display: flex;

        align-items: baseline;

        gap: 7px;

        margin-top: 9px;

    }



    .inventory-health-value strong {

        color: #111827;

        font-size: 23px;

        font-weight: 700;

        line-height: 1;

    }



    .inventory-health-value span {

        color: #7c8797;

        font-size: 8.5px;

    }



    .inventory-progress-row {

        display: flex;

        align-items: center;

        gap: 10px;

        margin-top: 12px;

    }



    .inventory-progress {

        height: 8px;

        flex: 1;

        overflow: hidden;

        border-radius: 999px;

        background: #edf1f5;

    }



    .inventory-progress > span {

        display: block;

        height: 100%;

        border-radius: inherit;

    }



    .inventory-progress--healthy > span { background: var(--inv-green); }

    .inventory-progress--low > span { background: var(--inv-amber); }

    .inventory-progress--out > span { background: var(--inv-red); }



    .inventory-progress-percent {

        min-width: 30px;

        color: #263244;

        font-size: 9px;

        font-weight: 650;

        text-align: right;

    }



    .inventory-donut-body {

        display: grid;

        grid-template-columns: 165px minmax(0, 1fr);

        gap: 18px;

        align-items: center;

        padding: 13px 16px 16px;

    }



    .inventory-donut {

        position: relative;

        width: 150px;

        height: 150px;

        margin: 0 auto;

        border-radius: 50%;

        background:

            conic-gradient(

                var(--inv-green) 0deg calc(var(--good) * 3.6deg),

                var(--inv-amber) calc(var(--good) * 3.6deg) calc((var(--good) + var(--low)) * 3.6deg),

                var(--inv-red) calc((var(--good) + var(--low)) * 3.6deg) 360deg

            );

    }



    .inventory-donut::after {

        content: "";

        position: absolute;

        inset: 19px;

        border-radius: 50%;

        background: #fff;

    }



    .inventory-donut-center {

        position: absolute;

        inset: 0;

        z-index: 1;

        display: grid;

        place-items: center;

        text-align: center;

    }



    .inventory-donut-center strong {

        display: block;

        color: #111827;

        font-size: 27px;

        font-weight: 700;

        line-height: 1;

    }



    .inventory-donut-center span {

        display: block;

        margin-top: 5px;

        color: #7c8797;

        font-size: 8.5px;

    }



    .inventory-legend { display: grid; gap: 9px; }



    .inventory-legend-row {

        display: grid;

        grid-template-columns: 8px minmax(0, 1fr) 24px 36px;

        align-items: center;

        gap: 8px;

        min-height: 28px;

        border-bottom: 1px solid #eef1f4;

        color: #657184;

        font-size: 8.5px;

    }



    .inventory-legend-row:last-child { border-bottom: 0; }



    .inventory-legend-row strong {

        color: #111827;

        font-size: 8.5px;

        font-weight: 700;

        text-align: right;

    }



    .inventory-legend-row em {

        color: #536174;

        font-size: 8.5px;

        font-style: normal;

        font-weight: 600;

        text-align: right;

    }



    .inventory-table-panel {

        margin-top: 14px;

        overflow: hidden;

        border: 1px solid var(--inv-border);

        border-radius: 14px;

        background: #fff;

        box-shadow: 0 6px 18px rgba(15, 23, 42, .03);

    }



    .inventory-table-head {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 16px;

        padding: 13px 16px 10px;

        border-bottom: 1px solid var(--inv-border-soft);

    }



    .inventory-table-head-copy {

        display: flex;

        align-items: flex-start;

        gap: 10px;

    }



    .inventory-table-head-icon {

        display: grid;

        width: 24px;

        height: 24px;

        place-items: center;

        color: #111827;

    }



    .inventory-table-head-icon svg { width: 17px; height: 17px; }



    .inventory-table-title {

        margin: 0;

        color: #111827;

        font-size: 12px;

        font-weight: 700;

    }



    .inventory-table-subtitle {

        margin: 3px 0 0;

        color: #7c8797;

        font-size: 9px;

    }



    .inventory-table-search-wrap {

        position: relative;

        width: min(250px, 32vw);

    }



    .inventory-table-search-wrap svg {

        position: absolute;

        top: 50%;

        left: 11px;

        width: 13px;

        height: 13px;

        transform: translateY(-50%);

        color: #708094;

        pointer-events: none;

    }



    .inventory-table-search {

        height: 36px;

        padding: 0 11px 0 33px;

        font-size: 9px;

    }



    .inventory-grid-head,

    .inventory-row {

        display: grid;

        grid-template-columns:

            minmax(230px, 1.6fr)

            minmax(135px, .72fr)

            minmax(105px, .55fr)

            minmax(95px, .52fr)

            minmax(190px, 1fr)

            minmax(120px, .72fr)

            96px;

        gap: 14px;

        align-items: center;

        padding-left: 16px;

        padding-right: 16px;

    }



    .inventory-grid-head {

        min-height: 38px;

        background: #fbfcfd;

        color: #455268;

        font-size: 8.5px;

        font-weight: 650;

    }



    .inventory-row {

        min-height: 56px;

        border-top: 1px solid var(--inv-border-soft);

        padding-top: 8px;

        padding-bottom: 8px;

    }



    .inventory-product-cell {

        display: flex;

        align-items: center;

        gap: 10px;

        min-width: 0;

    }



    .inventory-product-thumb {

        display: grid;

        width: 42px;

        height: 42px;

        flex: 0 0 42px;

        place-items: center;

        overflow: hidden;

        border: 1px solid #e4e9ef;

        border-radius: 9px;

        background: #f3f5f7;

    }



    .inventory-product-thumb img {

        width: 100%;

        height: 100%;

        object-fit: cover;

    }



    .inventory-product-thumb svg {

        width: 17px;

        height: 17px;

        color: #99a4b1;

    }



    .inventory-product-name {

        overflow: hidden;

        margin: 0;

        color: #111827;

        font-size: 10px;

        font-weight: 700;

        text-overflow: ellipsis;

        white-space: nowrap;

    }



    .inventory-product-category {

        overflow: hidden;

        margin: 3px 0 0;

        color: #7c8797;

        font-size: 8.4px;

        text-overflow: ellipsis;

        white-space: nowrap;

    }



    .inventory-sku {

        overflow: hidden;

        color: #677387;

        font-size: 8.5px;

        text-overflow: ellipsis;

        white-space: nowrap;

    }



    .inventory-badge {

        display: inline-flex;

        width: max-content;

        min-height: 24px;

        align-items: center;

        gap: 5px;

        border-radius: 999px;

        padding: 0 9px;

        font-size: 8px;

        font-weight: 650;

        white-space: nowrap;

    }



    .inventory-badge::before {

        content: "";

        width: 6px;

        height: 6px;

        border-radius: 50%;

    }



    .inventory-badge.healthy { background: var(--inv-green-soft); color: #198452; }

    .inventory-badge.healthy::before { background: var(--inv-green); }

    .inventory-badge.low { background: var(--inv-amber-soft); color: #a86f00; }

    .inventory-badge.low::before { background: var(--inv-amber); }

    .inventory-badge.out { background: var(--inv-red-soft); color: #b8242e; }

    .inventory-badge.out::before { background: var(--inv-red); }



    .inventory-stock-number {

        color: #111827;

        font-size: 10px;

        font-weight: 700;

    }



    .inventory-select,

    .inventory-qty {

        width: 100%;

        height: 34px;

        border: 1px solid #dbe2ea;

        border-radius: 8px;

        background: #fff;

        padding: 0 9px;

        color: #536174;

        font-size: 8.5px;

        outline: none;

    }



    .inventory-update-btn {

        width: 100%;

        height: 34px;

        font-size: 8.5px;

    }



    .inventory-empty {

        padding: 36px 20px;

        text-align: center;

        color: #7c8797;

        font-size: 9px;

    }



    .inventory-hidden { display: none !important; }



    .inventory-activity-panel {

        margin-top: 14px;

        min-height: 136px;

        border: 1px solid var(--inv-border);

        border-radius: 14px;

        background: #fff;

        padding: 13px 16px 16px;

        box-shadow: 0 6px 18px rgba(15, 23, 42, .03);

    }



    .inventory-activity-head {

        display: flex;

        align-items: flex-start;

        gap: 10px;

    }



    .inventory-activity-head svg {

        width: 17px;

        height: 17px;

        color: #111827;

    }



    .inventory-activity-title {

        margin: 0;

        color: #111827;

        font-size: 12px;

        font-weight: 700;

    }



    .inventory-activity-subtitle {

        margin: 3px 0 0;

        color: #7c8797;

        font-size: 9px;

    }



    .inventory-activity-list {

        display: grid;

        gap: 7px;

        margin-top: 12px;

    }



    .inventory-activity-item {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 12px;

        border: 1px solid var(--inv-border-soft);

        border-radius: 9px;

        background: #fbfcfd;

        padding: 9px 11px;

    }



    .inventory-activity-name {

        color: #263244;

        font-size: 7.8px;

        font-weight: 600;

    }



    .inventory-activity-meta {

        margin-top: 2px;

        color: #8791a0;

        font-size: 6.8px;

    }



    .inventory-activity-value {

        color: #111827;

        font-size: 8px;

        font-weight: 700;

        white-space: nowrap;

    }



    .inventory-activity-empty {

        display: grid;

        min-height: 90px;

        place-items: center;

        text-align: center;

    }



    .inventory-activity-empty-icon {

        display: grid;

        width: 34px;

        height: 34px;

        margin: 0 auto;

        place-items: center;

        color: #c0c8d2;

    }



    .inventory-activity-empty-icon svg { width: 26px; height: 26px; }



    .inventory-activity-empty strong {

        display: block;

        margin-top: 4px;

        color: #111827;

        font-size: 10px;

    }



    .inventory-activity-empty span {

        display: block;

        margin-top: 4px;

        color: #7c8797;

        font-size: 8.5px;

    }



    @media (max-width: 1280px) {

        .inventory-head { grid-template-columns: 1fr; }

        .inventory-insight-grid { grid-template-columns: 1fr; }

        .inventory-donut-body { grid-template-columns: 180px 1fr; }

        .inventory-table-panel { overflow-x: auto; }

        .inventory-grid-head,

        .inventory-row { min-width: 1080px; }

    }



    @media (max-width: 900px) {

        .inventory-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        .inventory-health-cards { grid-template-columns: 1fr; }

        .inventory-donut-body { grid-template-columns: 1fr; }

        .inventory-legend { width: min(360px, 100%); margin: 0 auto; }

    }



    @media (max-width: 640px) {

        .inventory-title {

            font-size: clamp(28px, 8vw, 34px);

            font-weight: 650;

        }



        .inventory-stat-grid { grid-template-columns: 1fr; }

        .inventory-search-row { grid-template-columns: 1fr; }

        .inventory-search-button { height: 38px; }

        .inventory-table-head { align-items: stretch; flex-direction: column; }

        .inventory-table-search-wrap { width: 100%; }

    }



    @media (prefers-reduced-motion: reduce) {

        .inventory-shell *,

        .inventory-shell *::before,

        .inventory-shell *::after {

            transition-duration: .01ms !important;

            animation-duration: .01ms !important;

            animation-iteration-count: 1 !important;

        }

    }


/* ============================================================
   INVENTORY — COMPACT BUT READABLE FINAL LAYER
   Reduces whitespace and component height without shrinking
   operational text below a practical reading size.
   ============================================================ */

.inventory-shell {
    max-width: 1440px !important;
    padding-bottom: 22px;
}

/* Header: same Seller Catalog family, less vertical footprint. */
.inventory-head {
    gap: 0 !important;
    min-height: 66px;
    padding: 2px 2px 14px;
    border-bottom: 1px solid #e7ebf0;
}

.inventory-head--single {
    max-width: 780px !important;
}

.inventory-eyebrow {
    font-size: 8px !important;
    letter-spacing: .16em !important;
    color: #b87605 !important;
}

.inventory-title {
    margin-top: 6px !important;
    font-size: clamp(29px, 2.2vw, 36px) !important;
    font-weight: 650 !important;
    line-height: 1 !important;
}

.inventory-subtitle {
    max-width: 720px !important;
    margin-top: 7px !important;
    font-size: 10px !important;
    line-height: 1.5 !important;
    color: #7c8592 !important;
}

/* Summary cards: shorter, but keep the hierarchy readable. */
.inventory-stat-grid {
    gap: 9px !important;
    margin-top: 12px !important;
}

.inventory-stat-card {
    min-height: 78px !important;
    border-radius: 13px !important;
    padding: 11px 54px 10px 13px !important;
    box-shadow: 0 5px 16px rgba(15, 23, 42, .028) !important;
}

.inventory-stat-icon {
    top: 11px !important;
    right: 11px !important;
    width: 34px !important;
    height: 34px !important;
    border-radius: 9px !important;
}

.inventory-stat-icon svg {
    width: 16px !important;
    height: 16px !important;
}

.inventory-stat-label {
    font-size: 10px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;
}

.inventory-stat-value {
    margin-top: 4px !important;
    font-size: 22px !important;
}

.inventory-stat-note {
    margin-top: 5px !important;
    font-size: 8.8px !important;
    line-height: 1.35 !important;
}

/* Stock insights: reduce empty space instead of reducing legibility. */
.inventory-insight-grid {
    grid-template-columns: minmax(0, 1.72fr) minmax(300px, .82fr) !important;
    gap: 10px !important;
    margin-top: 10px !important;
}

.inventory-panel {
    border-radius: 13px !important;
    box-shadow: 0 5px 16px rgba(15, 23, 42, .028) !important;
}

.inventory-panel-head {
    gap: 8px !important;
    padding: 11px 13px 0 !important;
}

.inventory-panel-icon {
    width: 22px !important;
    height: 22px !important;
    flex-basis: 22px !important;
}

.inventory-panel-icon svg {
    width: 15px !important;
    height: 15px !important;
}

.inventory-panel-title {
    font-size: 11.5px !important;
    line-height: 1.25 !important;
}

.inventory-panel-subtitle {
    margin-top: 2px !important;
    font-size: 8.8px !important;
    line-height: 1.4 !important;
}

.inventory-health-cards {
    gap: 8px !important;
    padding: 10px 13px 12px !important;
}

.inventory-health-card {
    border-radius: 9px !important;
    padding: 9px 10px !important;
}

.inventory-health-label {
    gap: 7px !important;
    font-size: 9.8px !important;
}

.inventory-health-dot {
    width: 7px !important;
    height: 7px !important;
}

.inventory-health-value {
    gap: 6px !important;
    margin-top: 7px !important;
}

.inventory-health-value strong {
    font-size: 19px !important;
}

.inventory-health-value span {
    font-size: 8.8px !important;
}

.inventory-progress-row {
    gap: 8px !important;
    margin-top: 9px !important;
}

.inventory-progress {
    height: 6px !important;
}

.inventory-progress-percent {
    min-width: 29px !important;
    font-size: 8.8px !important;
}

/* Compact donut: preserve labels and percentages. */
.inventory-donut-body {
    grid-template-columns: 122px minmax(0, 1fr) !important;
    gap: 13px !important;
    padding: 9px 13px 12px !important;
}

.inventory-donut {
    width: 112px !important;
    height: 112px !important;
}

.inventory-donut::after {
    inset: 15px !important;
}

.inventory-donut-center strong {
    font-size: 21px !important;
}

.inventory-donut-center span {
    margin-top: 3px !important;
    font-size: 8.3px !important;
}

.inventory-legend {
    gap: 5px !important;
}

.inventory-legend-row {
    grid-template-columns: 7px minmax(0, 1fr) 28px 36px !important;
    gap: 7px !important;
    min-height: 26px !important;
    font-size: 8.8px !important;
}

.inventory-legend-row strong,
.inventory-legend-row em {
    font-size: 8.8px !important;
}

/* Product table: compact rows, readable operational text. */
.inventory-table-panel {
    margin-top: 10px !important;
    border-radius: 13px !important;
    box-shadow: 0 5px 16px rgba(15, 23, 42, .028) !important;
}

.inventory-table-head {
    gap: 12px !important;
    padding: 10px 13px 9px !important;
}

.inventory-table-head-copy {
    gap: 8px !important;
}

.inventory-table-head-icon {
    width: 22px !important;
    height: 22px !important;
}

.inventory-table-head-icon svg {
    width: 15px !important;
    height: 15px !important;
}

.inventory-table-title {
    font-size: 11.5px !important;
}

.inventory-table-subtitle {
    margin-top: 2px !important;
    font-size: 8.8px !important;
    line-height: 1.4 !important;
}

.inventory-table-search-wrap {
    width: min(230px, 30vw) !important;
}

.inventory-table-search-wrap svg {
    left: 11px !important;
    width: 13px !important;
    height: 13px !important;
}

.inventory-table-search {
    height: 35px !important;
    border-radius: 9px !important;
    padding-left: 33px !important;
    font-size: 9.5px !important;
}

.inventory-grid-head,
.inventory-row {
    grid-template-columns:
        minmax(220px, 1.55fr)
        minmax(125px, .68fr)
        minmax(105px, .56fr)
        minmax(88px, .48fr)
        minmax(180px, .98fr)
        minmax(115px, .68fr)
        88px !important;
    gap: 10px !important;
    padding-left: 13px !important;
    padding-right: 13px !important;
}

.inventory-grid-head {
    min-height: 34px !important;
    font-size: 8.8px !important;
    font-weight: 700 !important;
}

.inventory-row {
    min-height: 50px !important;
    padding-top: 6px !important;
    padding-bottom: 6px !important;
}

.inventory-product-cell {
    gap: 9px !important;
}

.inventory-product-thumb {
    width: 38px !important;
    height: 38px !important;
    flex-basis: 38px !important;
    border-radius: 8px !important;
}

.inventory-product-thumb svg {
    width: 15px !important;
    height: 15px !important;
}

.inventory-product-name {
    font-size: 10px !important;
    line-height: 1.35 !important;
}

.inventory-product-category {
    margin-top: 2px !important;
    font-size: 8.8px !important;
    line-height: 1.35 !important;
}

.inventory-sku {
    font-size: 9px !important;
}

.inventory-badge {
    min-height: 22px !important;
    gap: 5px !important;
    padding: 0 8px !important;
    font-size: 8.3px !important;
}

.inventory-badge::before {
    width: 5px !important;
    height: 5px !important;
}

.inventory-stock-number {
    font-size: 10px !important;
}

.inventory-select,
.inventory-qty,
.inventory-update-btn {
    height: 32px !important;
    border-radius: 8px !important;
    font-size: 9px !important;
}

.inventory-select,
.inventory-qty {
    padding-left: 8px !important;
    padding-right: 8px !important;
}

.inventory-update-btn {
    font-weight: 700 !important;
}

.inventory-empty {
    padding: 27px 18px !important;
    font-size: 9.5px !important;
}

/* Recent activity: compact rows with legible metadata. */
.inventory-activity-panel {
    min-height: 0 !important;
    margin-top: 10px !important;
    border-radius: 13px !important;
    padding: 11px 13px 12px !important;
    box-shadow: 0 5px 16px rgba(15, 23, 42, .028) !important;
}

.inventory-activity-head {
    gap: 8px !important;
}

.inventory-activity-head svg {
    width: 15px !important;
    height: 15px !important;
}

.inventory-activity-title {
    font-size: 11.5px !important;
}

.inventory-activity-subtitle {
    margin-top: 2px !important;
    font-size: 8.8px !important;
    line-height: 1.4 !important;
}

.inventory-activity-list {
    gap: 6px !important;
    margin-top: 9px !important;
}

.inventory-activity-item {
    gap: 10px !important;
    border-radius: 8px !important;
    padding: 7px 9px !important;
}

.inventory-activity-name {
    font-size: 9px !important;
    line-height: 1.35 !important;
}

.inventory-activity-meta {
    margin-top: 2px !important;
    font-size: 8px !important;
}

.inventory-activity-value {
    font-size: 9px !important;
}

.inventory-activity-empty {
    min-height: 70px !important;
}

.inventory-activity-empty-icon {
    width: 28px !important;
    height: 28px !important;
}

.inventory-activity-empty-icon svg {
    width: 21px !important;
    height: 21px !important;
}

.inventory-activity-empty strong {
    margin-top: 3px !important;
    font-size: 9.8px !important;
}

.inventory-activity-empty span {
    margin-top: 3px !important;
    font-size: 8.5px !important;
}

/* Keyboard focus remains clear despite tighter controls. */
.inventory-table-search:focus,
.inventory-select:focus,
.inventory-qty:focus {
    border-color: #d59617 !important;
    box-shadow: 0 0 0 3px rgba(213, 150, 23, .08) !important;
}

@media (max-width: 1280px) {
    .inventory-insight-grid {
        grid-template-columns: 1fr !important;
    }

    .inventory-donut-body {
        grid-template-columns: 130px minmax(0, 1fr) !important;
    }

    .inventory-grid-head,
    .inventory-row {
        min-width: 1020px !important;
    }
}

@media (max-width: 900px) {
    .inventory-stat-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .inventory-health-cards {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 640px) {
    .inventory-head {
        min-height: 0;
    }

    .inventory-title {
        font-size: clamp(28px, 8vw, 34px) !important;
    }

    .inventory-subtitle {
        font-size: 9.8px !important;
    }

    .inventory-stat-grid,
    .inventory-health-cards {
        grid-template-columns: 1fr !important;
    }

    .inventory-stat-card {
        min-height: 74px !important;
    }

    .inventory-donut-body {
        grid-template-columns: 1fr !important;
    }

    .inventory-table-head {
        align-items: stretch !important;
        flex-direction: column !important;
    }

    .inventory-table-search-wrap {
        width: 100% !important;
    }
}



/* ======================================================================
   INVENTORY — FLOATING CONTAINER EFFECTS
   Visual-only enhancement. No backend, routes, forms, stock logic,
   search filtering, variant adjustment behavior, or JavaScript changed.
   ====================================================================== */

.inventory-shell {
    --inv-float-border: #E2E7ED;
    --inv-float-border-soft: #E9EDF2;
    --inv-float-shadow:
        0 1px 2px rgba(15, 23, 42, .035),
        0 10px 26px rgba(15, 23, 42, .075),
        inset 0 1px 0 rgba(255, 255, 255, .86);
    --inv-float-shadow-soft:
        0 1px 2px rgba(15, 23, 42, .025),
        0 6px 16px rgba(15, 23, 42, .05),
        inset 0 1px 0 rgba(255, 255, 255, .82);
}

/* Main inventory surfaces — consistent elevated card system. */
.inventory-shell .inventory-stat-card,
.inventory-shell .inventory-panel,
.inventory-shell .inventory-table-panel,
.inventory-shell .inventory-activity-panel,
.inventory-shell .inventory-search-panel {
    border-color: var(--inv-float-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--inv-float-shadow) !important;
    transform: none !important;
}

/* Keep the current compact radius and spacing. */
.inventory-shell .inventory-stat-card,
.inventory-shell .inventory-panel,
.inventory-shell .inventory-table-panel,
.inventory-shell .inventory-activity-panel {
    border-radius: 13px !important;
}

/* Static cards: no cursor lift or positional movement. */
.inventory-shell .inventory-stat-card:hover,
.inventory-shell .inventory-panel:hover,
.inventory-shell .inventory-table-panel:hover,
.inventory-shell .inventory-activity-panel:hover,
.inventory-shell .inventory-search-panel:hover {
    border-color: var(--inv-float-border) !important;
    box-shadow: var(--inv-float-shadow) !important;
    transform: none !important;
}

/* Secondary cards get a softer shadow so hierarchy remains clear. */
.inventory-shell .inventory-health-card,
.inventory-shell .inventory-activity-item {
    border-color: var(--inv-float-border-soft) !important;
    background: #FFFFFF !important;
    box-shadow: var(--inv-float-shadow-soft) !important;
    transform: none !important;
}

/* Table header stays visually attached to its main floating panel. */
.inventory-shell .inventory-table-head {
    border-bottom-color: #E9EDF2 !important;
    background: #FFFFFF !important;
}

/* Table rows remain flat for scanability; only the outer table floats. */
.inventory-shell .inventory-grid-head {
    background: #F8FAFC !important;
}

.inventory-shell .inventory-row {
    border-top-color: #EDF1F5 !important;
    background: #FFFFFF !important;
}

/* Product thumbnails have subtle depth without becoming mini-cards. */
.inventory-shell .inventory-product-thumb {
    border-color: #E2E7ED !important;
    box-shadow: 0 3px 9px rgba(15, 23, 42, .035) !important;
}

/* Keep empty and informational states secondary. */
.inventory-shell .inventory-empty,
.inventory-shell .inventory-activity-empty {
    background: transparent !important;
}

/* No structural gold/cream tint: semantic status colors stay untouched. */
.inventory-shell .inventory-stat-card,
.inventory-shell .inventory-panel,
.inventory-shell .inventory-table-panel,
.inventory-shell .inventory-activity-panel,
.inventory-shell .inventory-health-card,
.inventory-shell .inventory-activity-item {
    outline: 0 !important;
}

/* Mobile: slightly lighter elevation so stacked cards do not feel heavy. */
@media (max-width: 640px) {
    .inventory-shell .inventory-stat-card,
    .inventory-shell .inventory-panel,
    .inventory-shell .inventory-table-panel,
    .inventory-shell .inventory-activity-panel,
    .inventory-shell .inventory-search-panel {
        box-shadow:
            0 1px 2px rgba(15, 23, 42, .03),
            0 7px 18px rgba(15, 23, 42, .06),
            inset 0 1px 0 rgba(255, 255, 255, .84) !important;
    }

    .inventory-shell .inventory-health-card,
    .inventory-shell .inventory-activity-item {
        box-shadow:
            0 1px 2px rgba(15, 23, 42, .02),
            0 5px 13px rgba(15, 23, 42, .045) !important;
    }
}


/* ======================================================================
   INVENTORY — DASHBOARD SHADOW REFERENCE FINAL PASS
   Front-end/CSS only. Inventory stats, search, stock adjustment forms,
   variant selection, pagination, stock state logic and backend behavior
   remain unchanged.
   ====================================================================== */

.inventory-shell {
    --inventory-dashboard-border: #E5E7EB;
    --inventory-dashboard-border-strong: #D1D5DB;
    --inventory-dashboard-divider: #ECEFF2;

    /* Dashboard-style layered elevation:
       contact shadow + middle separation + broad ambient float. */
    --inventory-dashboard-shadow:
        0 2px 5px rgba(15, 23, 42, .045),
        0 10px 24px rgba(15, 23, 42, .075),
        0 24px 54px rgba(15, 23, 42, .095),
        inset 0 1px 0 rgba(255, 255, 255, .92);

    --inventory-dashboard-shadow-soft:
        0 1px 3px rgba(15, 23, 42, .035),
        0 7px 18px rgba(15, 23, 42, .06),
        0 15px 32px rgba(15, 23, 42, .065),
        inset 0 1px 0 rgba(255, 255, 255, .90);

    --inventory-dashboard-shadow-inner:
        0 1px 2px rgba(15, 23, 42, .025),
        0 5px 14px rgba(15, 23, 42, .045);
}

/* Header stays document-like; only divider is neutral. */
.inventory-shell .inventory-head {
    border-bottom-color: var(--inventory-dashboard-divider) !important;
}

/* ------------------------------------------------------------
   SUMMARY CARDS — medium floating depth
   ------------------------------------------------------------ */
.inventory-shell .inventory-stat-card {
    border: 1px solid var(--inventory-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--inventory-dashboard-shadow-soft) !important;
    transform: none !important;
}

.inventory-shell .inventory-stat-card:hover {
    border-color: var(--inventory-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--inventory-dashboard-shadow-soft) !important;
    transform: none !important;
}

.inventory-shell .inventory-stat-icon {
    border: 1px solid rgba(15, 23, 42, .045) !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, .035) !important;
}

/* ------------------------------------------------------------
   PRIMARY WORK SURFACES — strongest dashboard depth
   ------------------------------------------------------------ */
.inventory-shell .inventory-search-panel,
.inventory-shell .inventory-panel,
.inventory-shell .inventory-table-panel,
.inventory-shell .inventory-activity-panel {
    border: 1px solid var(--inventory-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--inventory-dashboard-shadow) !important;
    transform: none !important;
}

.inventory-shell .inventory-search-panel:hover,
.inventory-shell .inventory-panel:hover,
.inventory-shell .inventory-table-panel:hover,
.inventory-shell .inventory-activity-panel:hover {
    border-color: var(--inventory-dashboard-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--inventory-dashboard-shadow) !important;
    transform: none !important;
}

/* ------------------------------------------------------------
   INNER STOCK / ACTIVITY CARDS — softer hierarchy
   ------------------------------------------------------------ */
.inventory-shell .inventory-health-card,
.inventory-shell .inventory-activity-item {
    border-color: #E7EAEE !important;
    background: #FFFFFF !important;
    box-shadow: var(--inventory-dashboard-shadow-inner) !important;
    transform: none !important;
}

.inventory-shell .inventory-health-card:hover,
.inventory-shell .inventory-activity-item:hover {
    border-color: #E7EAEE !important;
    background: #FFFFFF !important;
    box-shadow: var(--inventory-dashboard-shadow-inner) !important;
    transform: none !important;
}

/* ------------------------------------------------------------
   TABLE SURFACE
   Parent floats; individual rows stay flat and scan-friendly.
   ------------------------------------------------------------ */
.inventory-shell .inventory-table-head {
    border-bottom-color: var(--inventory-dashboard-divider) !important;
    background: #FFFFFF !important;
}

.inventory-shell .inventory-grid-head {
    background: #F8FAFC !important;
    border-color: var(--inventory-dashboard-divider) !important;
}

.inventory-shell .inventory-row {
    border-top-color: var(--inventory-dashboard-divider) !important;
    background: #FFFFFF !important;
    box-shadow: none !important;
    transform: none !important;
}

.inventory-shell .inventory-row:hover {
    background: #F8FAFC !important;
    border-top-color: var(--inventory-dashboard-divider) !important;
    box-shadow: none !important;
    transform: none !important;
}

/* Product thumbnails remain subtle secondary surfaces. */
.inventory-shell .inventory-product-thumb {
    border-color: var(--inventory-dashboard-border) !important;
    background: #F8FAFC !important;
    box-shadow: 0 3px 9px rgba(15, 23, 42, .035) !important;
}

/* ------------------------------------------------------------
   SEARCH + STOCK CONTROLS
   ------------------------------------------------------------ */
.inventory-shell .inventory-search-input,
.inventory-shell .inventory-table-search,
.inventory-shell .inventory-select,
.inventory-shell .inventory-qty {
    border-color: var(--inventory-dashboard-border-strong) !important;
    background: #FFFFFF !important;
    box-shadow:
        0 1px 2px rgba(15, 23, 42, .025),
        0 4px 10px rgba(15, 23, 42, .03) !important;
}

.inventory-shell .inventory-search-input:hover,
.inventory-shell .inventory-table-search:hover,
.inventory-shell .inventory-select:hover,
.inventory-shell .inventory-qty:hover {
    border-color: #C9D0D9 !important;
    background: #FFFFFF !important;
}

.inventory-shell .inventory-search-input:focus,
.inventory-shell .inventory-table-search:focus,
.inventory-shell .inventory-select:focus,
.inventory-shell .inventory-qty:focus {
    border-color: #C4A35B !important;
    background: #FFFFFF !important;
    box-shadow:
        0 0 0 3px rgba(196, 163, 91, .08),
        0 5px 14px rgba(15, 23, 42, .04) !important;
}

/* Search / update buttons remain static; color change only. */
.inventory-shell .inventory-search-button,
.inventory-shell .inventory-update-btn {
    transform: none !important;
}

.inventory-shell .inventory-search-button:hover,
.inventory-shell .inventory-update-btn:hover,
.inventory-shell .inventory-update-btn:focus-visible {
    transform: none !important;
}

/* ------------------------------------------------------------
   PANEL ICONS / EMPTY STATES / PAGINATION
   ------------------------------------------------------------ */
.inventory-shell .inventory-panel-icon,
.inventory-shell .inventory-table-head-icon {
    border-radius: 8px;
    background: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, .03) !important;
}

.inventory-shell .inventory-empty,
.inventory-shell .inventory-activity-empty {
    background: transparent !important;
}

.inventory-shell .inventory-activity-empty-icon {
    border-radius: 10px;
    background: #F8FAFC !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, .03) !important;
}

.inventory-shell .inventory-table-panel > .border-t {
    border-color: var(--inventory-dashboard-divider) !important;
    background: #FFFFFF !important;
}

.inventory-shell .inventory-table-panel nav a,
.inventory-shell .inventory-table-panel nav span {
    border-color: var(--inventory-dashboard-border) !important;
}

/* Structural surfaces never get colored outlines. */
.inventory-shell .inventory-stat-card,
.inventory-shell .inventory-search-panel,
.inventory-shell .inventory-panel,
.inventory-shell .inventory-table-panel,
.inventory-shell .inventory-activity-panel,
.inventory-shell .inventory-health-card,
.inventory-shell .inventory-activity-item {
    outline: 0 !important;
}

/* Mobile: retain depth without making stacked cards feel heavy. */
@media (max-width: 640px) {
    .inventory-shell .inventory-stat-card {
        box-shadow:
            0 1px 3px rgba(15, 23, 42, .03),
            0 6px 14px rgba(15, 23, 42, .05),
            0 12px 26px rgba(15, 23, 42, .055) !important;
    }

    .inventory-shell .inventory-search-panel,
    .inventory-shell .inventory-panel,
    .inventory-shell .inventory-table-panel,
    .inventory-shell .inventory-activity-panel {
        box-shadow:
            0 2px 4px rgba(15, 23, 42, .035),
            0 8px 18px rgba(15, 23, 42, .06),
            0 18px 38px rgba(15, 23, 42, .07) !important;
    }

    .inventory-shell .inventory-health-card,
    .inventory-shell .inventory-activity-item {
        box-shadow:
            0 1px 2px rgba(15, 23, 42, .02),
            0 5px 13px rgba(15, 23, 42, .045) !important;
    }
}


/* ======================================================================
   INVENTORY — STOCK BY PRODUCT STRONGER FLOATING FIX
   Gives the wide Stock by Product workspace the same perceived elevation
   as the other dashboard containers. CSS-only.
   ====================================================================== */

.inventory-shell .inventory-table-panel {
    position: relative !important;
    z-index: 1 !important;
    margin-top: 12px !important;
    margin-bottom: 18px !important;

    border: 1px solid #E2E6EB !important;
    background: #FFFFFF !important;

    /* Stronger than the generic page shadow because this panel is wide.
       The extra contact shadow keeps it visibly separated from the page. */
    box-shadow:
        0 3px 7px rgba(15, 23, 42, .055),
        0 14px 32px rgba(15, 23, 42, .095),
        0 30px 68px rgba(15, 23, 42, .11),
        inset 0 1px 0 rgba(255, 255, 255, .96) !important;

    transform: none !important;
}

/* Keep exactly the same depth on hover — no physical lift. */
.inventory-shell .inventory-table-panel:hover {
    border-color: #E2E6EB !important;
    background: #FFFFFF !important;
    box-shadow:
        0 3px 7px rgba(15, 23, 42, .055),
        0 14px 32px rgba(15, 23, 42, .095),
        0 30px 68px rgba(15, 23, 42, .11),
        inset 0 1px 0 rgba(255, 255, 255, .96) !important;
    transform: none !important;
}

/* Crisp top/header separation helps the large panel read like one
   floating dashboard workspace instead of a flat table block. */
.inventory-shell .inventory-table-head {
    background: #FFFFFF !important;
    border-bottom: 1px solid #E9EDF2 !important;
}

/* The column header remains a quiet inset surface. */
.inventory-shell .inventory-grid-head {
    background: #F8FAFC !important;
    border-color: #ECEFF2 !important;
}

/* Rows stay flat inside the elevated parent. */
.inventory-shell .inventory-row {
    background: #FFFFFF !important;
    border-top-color: #ECEFF2 !important;
    box-shadow: none !important;
    transform: none !important;
}

.inventory-shell .inventory-row:hover {
    background: #F8FAFC !important;
    border-top-color: #ECEFF2 !important;
    box-shadow: none !important;
    transform: none !important;
}

/* Pagination/footer remains visually attached to the same elevated shell. */
.inventory-shell .inventory-table-panel > .border-t {
    border-color: #ECEFF2 !important;
    background: #FFFFFF !important;
}

/* Tablet/mobile: keep visible elevation without excessive shadow spread. */
@media (max-width: 900px) {
    .inventory-shell .inventory-table-panel {
        box-shadow:
            0 2px 5px rgba(15, 23, 42, .045),
            0 10px 24px rgba(15, 23, 42, .08),
            0 22px 48px rgba(15, 23, 42, .085),
            inset 0 1px 0 rgba(255, 255, 255, .94) !important;
    }
}

@media (max-width: 640px) {
    .inventory-shell .inventory-table-panel {
        margin-bottom: 14px !important;
        box-shadow:
            0 2px 4px rgba(15, 23, 42, .04),
            0 8px 18px rgba(15, 23, 42, .065),
            0 16px 34px rgba(15, 23, 42, .075) !important;
    }
}


/* ======================================================================
   INVENTORY — MAIN CONTAINERS ONLY FLOATING
   Final hierarchy fix: elevation belongs only to top-level dashboard
   surfaces. Nested cards/items/thumbnail/icon shells remain flat.
   Visual-only; inventory data, forms, routes and JavaScript unchanged.
   ====================================================================== */

.inventory-shell {
    --inventory-main-border: #E5E7EB;
    --inventory-main-divider: #ECEFF2;
    --inventory-main-shadow:
        0 2px 5px rgba(15, 23, 42, .045),
        0 10px 24px rgba(15, 23, 42, .075),
        0 24px 54px rgba(15, 23, 42, .095),
        inset 0 1px 0 rgba(255, 255, 255, .92);
    --inventory-summary-shadow:
        0 1px 3px rgba(15, 23, 42, .035),
        0 7px 18px rgba(15, 23, 42, .06),
        0 15px 32px rgba(15, 23, 42, .065),
        inset 0 1px 0 rgba(255, 255, 255, .90);
}

/* ------------------------------------------------------------
   ONLY TOP-LEVEL / MAIN DASHBOARD CONTAINERS FLOAT
   ------------------------------------------------------------ */
.inventory-shell .inventory-stat-card {
    border: 1px solid var(--inventory-main-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--inventory-summary-shadow) !important;
    transform: none !important;
}

.inventory-shell .inventory-panel,
.inventory-shell .inventory-table-panel,
.inventory-shell .inventory-activity-panel,
.inventory-shell .inventory-search-panel {
    border: 1px solid var(--inventory-main-border) !important;
    background: #FFFFFF !important;
    box-shadow: var(--inventory-main-shadow) !important;
    transform: none !important;
}

/* Stock by Product stays slightly stronger because it is the widest work surface. */
.inventory-shell .inventory-table-panel,
.inventory-shell .inventory-table-panel:hover {
    box-shadow:
        0 3px 7px rgba(15, 23, 42, .055),
        0 14px 32px rgba(15, 23, 42, .095),
        0 30px 68px rgba(15, 23, 42, .11),
        inset 0 1px 0 rgba(255, 255, 255, .96) !important;
}

/* No main-container movement on hover. */
.inventory-shell .inventory-stat-card:hover,
.inventory-shell .inventory-panel:hover,
.inventory-shell .inventory-table-panel:hover,
.inventory-shell .inventory-activity-panel:hover,
.inventory-shell .inventory-search-panel:hover {
    border-color: var(--inventory-main-border) !important;
    background: #FFFFFF !important;
    transform: none !important;
}

/* ------------------------------------------------------------
   NESTED CONTAINERS ARE FLAT
   Healthy / Low Stock / Out of Stock should not float separately.
   ------------------------------------------------------------ */
.inventory-shell .inventory-health-card,
.inventory-shell .inventory-health-card:hover {
    border: 1px solid #E7EAEE !important;
    background: #FFFFFF !important;
    box-shadow: none !important;
    transform: none !important;
}

/* Recent adjustment rows are content rows inside one floating parent. */
.inventory-shell .inventory-activity-item,
.inventory-shell .inventory-activity-item:hover {
    border-color: #E7EAEE !important;
    background: #FAFBFC !important;
    box-shadow: none !important;
    transform: none !important;
}

/* Product thumbnails are media frames, not mini floating cards. */
.inventory-shell .inventory-product-thumb,
.inventory-shell .inventory-product-thumb:hover {
    border-color: #E5E7EB !important;
    background: #F8FAFC !important;
    box-shadow: none !important;
    transform: none !important;
}

/* Icon shells stay visually quiet inside their parent cards. */
.inventory-shell .inventory-stat-icon,
.inventory-shell .inventory-panel-icon,
.inventory-shell .inventory-table-head-icon,
.inventory-shell .inventory-activity-empty-icon {
    box-shadow: none !important;
}

/* ------------------------------------------------------------
   CONTROLS ARE CONTROLS, NOT FLOATING CONTAINERS
   Keep clean borders; only focus gets a focus ring.
   ------------------------------------------------------------ */
.inventory-shell .inventory-search-input,
.inventory-shell .inventory-table-search,
.inventory-shell .inventory-select,
.inventory-shell .inventory-qty {
    border-color: #D1D5DB !important;
    background: #FFFFFF !important;
    box-shadow: none !important;
}

.inventory-shell .inventory-search-input:hover,
.inventory-shell .inventory-table-search:hover,
.inventory-shell .inventory-select:hover,
.inventory-shell .inventory-qty:hover {
    border-color: #C9D0D9 !important;
    background: #FFFFFF !important;
    box-shadow: none !important;
}

.inventory-shell .inventory-search-input:focus,
.inventory-shell .inventory-table-search:focus,
.inventory-shell .inventory-select:focus,
.inventory-shell .inventory-qty:focus {
    border-color: #C4A35B !important;
    background: #FFFFFF !important;
    box-shadow: 0 0 0 3px rgba(196, 163, 91, .08) !important;
}

/* ------------------------------------------------------------
   TABLE CONTENT REMAINS FLAT INSIDE ITS FLOATING SHELL
   ------------------------------------------------------------ */
.inventory-shell .inventory-table-head {
    border-bottom-color: var(--inventory-main-divider) !important;
    background: #FFFFFF !important;
    box-shadow: none !important;
}

.inventory-shell .inventory-grid-head {
    background: #F8FAFC !important;
    border-color: var(--inventory-main-divider) !important;
    box-shadow: none !important;
}

.inventory-shell .inventory-row,
.inventory-shell .inventory-row:hover {
    border-top-color: var(--inventory-main-divider) !important;
    box-shadow: none !important;
    transform: none !important;
}

.inventory-shell .inventory-row {
    background: #FFFFFF !important;
}

.inventory-shell .inventory-row:hover {
    background: #F8FAFC !important;
}

/* Legend/divider rows remain document-like. */
.inventory-shell .inventory-legend-row {
    box-shadow: none !important;
}

/* Empty states sit inside their parent surface, so no independent float. */
.inventory-shell .inventory-empty,
.inventory-shell .inventory-activity-empty {
    background: transparent !important;
    box-shadow: none !important;
}

/* Pagination/footer belongs to the table shell. */
.inventory-shell .inventory-table-panel > .border-t {
    border-color: var(--inventory-main-divider) !important;
    background: #FFFFFF !important;
    box-shadow: none !important;
}

/* Structural nesting stays neutral. */
.inventory-shell .inventory-health-card,
.inventory-shell .inventory-activity-item,
.inventory-shell .inventory-product-thumb,
.inventory-shell .inventory-grid-head,
.inventory-shell .inventory-row {
    outline: 0 !important;
}

/* Mobile: only main surfaces retain depth. Nested content remains flat. */
@media (max-width: 640px) {
    .inventory-shell .inventory-stat-card {
        box-shadow:
            0 1px 3px rgba(15, 23, 42, .03),
            0 6px 14px rgba(15, 23, 42, .05),
            0 12px 26px rgba(15, 23, 42, .055) !important;
    }

    .inventory-shell .inventory-panel,
    .inventory-shell .inventory-search-panel,
    .inventory-shell .inventory-activity-panel {
        box-shadow:
            0 2px 4px rgba(15, 23, 42, .035),
            0 8px 18px rgba(15, 23, 42, .06),
            0 18px 38px rgba(15, 23, 42, .07) !important;
    }

    .inventory-shell .inventory-table-panel,
    .inventory-shell .inventory-table-panel:hover {
        box-shadow:
            0 2px 4px rgba(15, 23, 42, .04),
            0 8px 18px rgba(15, 23, 42, .065),
            0 16px 34px rgba(15, 23, 42, .075) !important;
    }

    .inventory-shell .inventory-health-card,
    .inventory-shell .inventory-activity-item,
    .inventory-shell .inventory-product-thumb {
        box-shadow: none !important;
    }
}


/* ========================================================================
   SELLER INVENTORY — SARI PICKUP REQUESTS DESIGN PARITY (FINAL LAYER)
   Visual overhaul; keeps inventory computations, endpoints, forms and data.
   Poppins / white / neutral gray / gold, no cream surfaces or icon tiles.
   ======================================================================== */
.inventory-shell {
    --inv-bg: #f4f5f7;
    --inv-surface: #fff;
    --inv-surface-soft: #f8fafc;
    --inv-border: #e5e7eb;
    --inv-border-soft: #e9edf1;
    --inv-text: #2f3742;
    --inv-text-2: #59636e;
    --inv-muted: #8793a1;
    --inv-gold: #c88912;
    --inv-green: #18794e;
    --inv-green-soft: #effcf3;
    --inv-amber: #c88912;
    --inv-amber-soft: #f8fafc;
    --inv-red: #cc4b37;
    --inv-red-soft: #fff4f2;
    width: 100%;
    max-width: 1520px !important;
    margin-inline: auto;
    padding-bottom: 28px !important;
    color: var(--inv-text);
    font: 400 13px/1.65 Poppins, ui-sans-serif, system-ui, sans-serif;
}
.inventory-shell input,
.inventory-shell button,
.inventory-shell select { font-family: inherit; }
.inventory-shell :is(input,select,button,a):focus-visible {
    outline: 2px solid #c88912;
    outline-offset: 3px;
}
.inventory-shell .inventory-head,
.inventory-shell .inventory-head--single {
    display: flex;
    flex-direction: row;
    align-items: flex-end;
    justify-content: space-between;
    max-width: none !important;
    min-height: 0;
    margin: 0;
    padding: 0 0 14px;
    gap: 14px 24px !important;
    border: 0;
    border-bottom: 1px solid #e5e7eb !important;
}
.inventory-shell .inventory-eyebrow {
    margin: 0 0 6px;
    color: #a87316 !important;
    font-size: 10.5px !important;
    font-weight: 800 !important;
    letter-spacing: .14em !important;
    line-height: 1.4;
}
.inventory-shell .inventory-title {
    margin: 0 !important;
    color: #2e343b;
    font-size: clamp(27px, 2.3vw, 31px) !important;
    font-weight: 760 !important;
    line-height: 1.08 !important;
    letter-spacing: -.04em !important;
}
.inventory-shell .inventory-subtitle {
    margin: 8px 0 0 !important;
    max-width: 780px !important;
    color: #8793a1 !important;
    font-size: 11.5px !important;
    line-height: 1.6 !important;
    font-weight: 400;
}
/* KPI typography and unboxed outline icons */
.inventory-shell .inventory-stat-grid {
    grid-template-columns: repeat(4,minmax(0,1fr)) !important;
    gap: 14px !important;
    margin-top: 20px !important;
}
.inventory-shell .inventory-stat-card,
.inventory-shell .inventory-stat-card:hover {
    display: flex;
    align-items: flex-start;
    flex-direction: row-reverse;
    justify-content: space-between;
    gap: 12px;
    min-height: 110px !important;
    padding: 14px 16px !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 18px !important;
    background: #fff !important;
    box-shadow: 0 3px 10px rgba(15,23,42,.035), 0 12px 30px rgba(15,23,42,.055) !important;
    transform: none !important;
}
.inventory-shell .inventory-stat-card > div { flex: 1; min-width: 0; }
.inventory-shell .inventory-stat-label {
    color: #637081 !important;
    font-size: 13px !important;
    font-weight: 650 !important;
    line-height: 1.4 !important;
}
.inventory-shell .inventory-stat-value {
    margin: 7px 0 5px !important;
    color: #1f2937 !important;
    font-size: clamp(25px, 1.9vw, 30px) !important;
    font-weight: 650 !important;
    line-height: 1.12 !important;
    letter-spacing: -.045em !important;
    font-variant-numeric: tabular-nums;
}
.inventory-shell .inventory-stat-note {
    margin: 0 !important;
    color: #8995a3 !important;
    font-size: 11.5px !important;
    line-height: 1.5 !important;
    font-weight: 400;
}
.inventory-shell .inventory-stat-icon,
.inventory-shell .inventory-stat-icon--products,
.inventory-shell .inventory-stat-icon--units,
.inventory-shell .inventory-stat-icon--low,
.inventory-shell .inventory-stat-icon--out {
    position: static !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 27px;
    width: 27px !important;
    height: 27px !important;
    padding: 0;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    color: #9aa5b1;
}
.inventory-shell .inventory-stat-icon--products { color: #9aa5b1 !important; }
.inventory-shell .inventory-stat-icon--units { color: #0f8a6b !important; }
.inventory-shell .inventory-stat-icon--low { color: #c88912 !important; }
.inventory-shell .inventory-stat-icon--out { color: #cc4b37 !important; }
.inventory-shell .inventory-stat-icon svg {
    display: block;
    width: 27px !important;
    height: 27px !important;
    stroke-width: 1.8;
}
/* Main floating panels only. Secondary cards stay flat. */
.inventory-shell .inventory-insight-grid {
    grid-template-columns: minmax(0,1.65fr) minmax(330px,.95fr) !important;
    gap: 14px !important;
    margin-top: 20px !important;
}
.inventory-shell .inventory-panel,
.inventory-shell .inventory-activity-panel,
.inventory-shell .inventory-table-panel,
.inventory-shell .inventory-search-panel {
    border: 1px solid #e5e7eb !important;
    border-radius: 18px !important;
    background: #fff !important;
    box-shadow: 0 3px 10px rgba(15,23,42,.035), 0 12px 30px rgba(15,23,42,.055) !important;
    transform: none !important;
}
.inventory-shell .inventory-panel-head {
    gap: 10px !important;
    padding: 21px 23px 0 !important;
}
.inventory-shell .inventory-panel-icon,
.inventory-shell .inventory-table-head-icon,
.inventory-shell .inventory-activity-head > svg {
    flex: 0 0 22px !important;
    width: 22px !important;
    height: 22px !important;
    display: grid;
    place-items: center;
    color: #7f8c9b !important;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}
.inventory-shell .inventory-panel-icon svg,
.inventory-shell .inventory-table-head-icon svg,
.inventory-shell .inventory-activity-head > svg {
    width: 21px !important;
    height: 21px !important;
    stroke-width: 1.7;
}
.inventory-shell .inventory-panel-title,
.inventory-shell .inventory-table-title,
.inventory-shell .inventory-activity-title {
    color: #2f3742 !important;
    font-size: 18px !important;
    font-weight: 780 !important;
    line-height: 1.4 !important;
    letter-spacing: -.03em !important;
}
.inventory-shell .inventory-panel-subtitle,
.inventory-shell .inventory-table-subtitle,
.inventory-shell .inventory-activity-subtitle {
    margin-top: 4px !important;
    color: #8b96a4 !important;
    font-size: 11.5px !important;
    line-height: 1.5 !important;
    font-weight: 400;
}
.inventory-shell .inventory-health-cards {
    grid-template-columns: repeat(3,minmax(0,1fr)) !important;
    gap: 12px !important;
    padding: 20px 23px 22px !important;
}
.inventory-shell .inventory-health-card,
.inventory-shell .inventory-health-card:hover {
    border: 1px solid #e9edf1 !important;
    border-radius: 12px !important;
    padding: 14px !important;
    background: #fff !important;
    box-shadow: none !important;
    transform: none !important;
}
.inventory-shell .inventory-health-label {
    color: #566375 !important;
    font-size: 11.5px !important;
    font-weight: 650 !important;
}
.inventory-shell .inventory-health-value { margin-top: 10px !important; }
.inventory-shell .inventory-health-value strong {
    color: #293440 !important;
    font-size: 24px !important;
    font-weight: 700 !important;
}
.inventory-shell .inventory-health-value span,
.inventory-shell .inventory-progress-percent { font-size: 11px !important; color: #7b8794; }
.inventory-shell .inventory-progress-row { margin-top: 14px !important; }
.inventory-shell .inventory-progress { height: 7px !important; background: #edf0f4 !important; }
.inventory-shell .inventory-donut-body {
    grid-template-columns: 147px minmax(0,1fr) !important;
    gap: 16px !important;
    padding: 16px 23px 22px !important;
}
.inventory-shell .inventory-donut { width: 132px !important; height: 132px !important; }
.inventory-shell .inventory-donut::after { inset: 19px !important; }
.inventory-shell .inventory-donut-center strong { font-size: 26px !important; color: #293440 !important; }
.inventory-shell .inventory-donut-center span { font-size: 10.5px !important; color: #8b96a4 !important; }
.inventory-shell .inventory-legend { gap: 5px !important; }
.inventory-shell .inventory-legend-row {
    grid-template-columns: 8px minmax(0,1fr) 34px 40px !important;
    gap: 8px !important;
    min-height: 32px !important;
    border-bottom-color: #edf0f3 !important;
    font-size: 11px !important;
    color: #667383 !important;
}
.inventory-shell .inventory-legend-row strong,
.inventory-shell .inventory-legend-row em { font-size: 11px !important; }
/* Separate full-width search / stock status dropdown toolbar. */
.inventory-shell .inventory-admin-filters {
    display: grid;
    grid-template-columns: minmax(0,1fr) minmax(185px,210px) auto auto;
    align-items: center;
    gap: 12px;
    margin: 20px 0 0;
    min-width: 0;
}
.inventory-shell .inventory-filter-search,
.inventory-shell .inventory-filter-dropdown { position: relative; display: block; min-width: 0; }
.inventory-shell .inventory-filter-search > svg {
    position: absolute;
    top: 50%;
    left: 16px;
    transform: translateY(-50%);
    width: 21px;
    height: 21px;
    pointer-events: none;
    color: #97a3b2;
}
.inventory-shell .inventory-filter-search input,
.inventory-shell .inventory-filter-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    height: 48px;
    padding: 0 16px;
    border: 1px solid #dce2e8;
    border-radius: 14px;
    outline: 0;
    background: #fff;
    box-shadow: none;
    color: #3f4b59;
    font: 600 11.5px/1.4 Poppins, sans-serif;
    transition: border-color .16s ease, box-shadow .16s ease, background-color .16s ease;
}
.inventory-shell .inventory-filter-search input { padding-left: 46px; }
.inventory-shell .inventory-filter-search input::placeholder { color: #a0aab6; opacity: 1; }
.inventory-shell .inventory-filter-trigger { cursor: pointer; }
.inventory-shell .inventory-filter-trigger > svg { width: 17px; height: 17px; flex: 0 0 17px; color: #6b7280; }
.inventory-shell .inventory-filter-search input:hover,
.inventory-shell .inventory-filter-trigger:hover { border-color: #cfd6de; background: #f8fafc; }
.inventory-shell .inventory-filter-search input:focus,
.inventory-shell .inventory-filter-trigger[aria-expanded="true"],
.inventory-shell .inventory-filter-trigger:focus-visible {
    outline: 0;
    border-color: #c88912;
    background: #fff;
    box-shadow: 0 0 0 1px rgba(200,137,18,.12);
}
.inventory-shell .inventory-filter-panel {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    z-index: 70;
    padding: 8px;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 16px 32px rgba(15,23,42,.12), 0 2px 6px rgba(15,23,42,.05);
}
.inventory-shell .inventory-filter-panel[hidden] { display: none !important; }
.inventory-shell .inventory-filter-option {
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
}
.inventory-shell .inventory-filter-option:hover,
.inventory-shell .inventory-filter-option:focus-visible { outline: 0; background: #f8fafc; color: #a56d0a; }
.inventory-shell .inventory-filter-option.is-active { background: #f8fafc; color: #2f3742; font-weight: 700; }
.inventory-shell .inventory-filter-apply,
.inventory-shell .inventory-filter-reset {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: auto;
    min-width: 94px;
    height: 48px;
    padding: 0 18px;
    border: 1px solid #dce2e8;
    border-radius: 14px;
    background: #fff;
    color: #5f6d7c;
    font: 700 12px/1.2 Poppins, sans-serif;
    cursor: pointer;
    white-space: nowrap;
}
.inventory-shell .inventory-filter-apply { border-color: #c88912; background: #c88912; color: #fff; }
.inventory-shell .inventory-filter-apply:hover { border-color: #b47b0e; background: #b47b0e; }
.inventory-shell .inventory-filter-apply svg { width: 15px; height: 15px; }
.inventory-shell .inventory-filter-reset:hover { border-color: #cfd6de; background: #f8fafc; }
/* Table: align with Pickup Requests text hierarchy while keeping stock input widths. */
.inventory-shell .inventory-table-panel { margin-top: 20px !important; overflow: hidden !important; }
.inventory-shell .inventory-table-head {
    align-items: center;
    flex-wrap: wrap;
    gap: 14px !important;
    padding: 21px 23px !important;
    border-bottom: 1px solid #e9edf1 !important;
    background: #fff !important;
}
.inventory-shell .inventory-table-head-copy { gap: 10px !important; }
.inventory-shell .inventory-table-counter {
    margin-left: auto;
    color: #7b8794;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}
.inventory-shell .inventory-table-scroller { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
.inventory-shell .inventory-grid-head,
.inventory-shell .inventory-row {
    grid-template-columns:
        minmax(235px,1.75fr)
        minmax(95px,.65fr)
        minmax(105px,.7fr)
        minmax(95px,.6fr)
        minmax(210px,1.2fr)
        minmax(130px,.8fr)
        minmax(100px,.6fr) !important;
    min-width: 1180px !important;
    gap: 12px !important;
    padding-left: 23px !important;
    padding-right: 23px !important;
}
.inventory-shell .inventory-grid-head {
    min-height: 48px !important;
    padding-top: 13px;
    padding-bottom: 13px;
    background: #f8fafc !important;
    border-bottom: 1px solid #e9edf1 !important;
    color: #8591a0 !important;
    font-size: 11px !important;
    font-weight: 750 !important;
    line-height: 1.5;
    letter-spacing: .075em;
    text-transform: uppercase;
}
.inventory-shell .inventory-row {
    min-height: 84px !important;
    padding-top: 17px !important;
    padding-bottom: 17px !important;
    border-top: 1px solid #edf0f3 !important;
    background: #fff !important;
    box-shadow: none !important;
}
.inventory-shell .inventory-row:hover { background: #f8fafc !important; }
.inventory-shell .inventory-product-cell { gap: 12px !important; }
.inventory-shell .inventory-product-thumb {
    flex: 0 0 48px !important;
    width: 48px !important;
    height: 48px !important;
    border-radius: 10px !important;
    background: #f8fafc !important;
    border-color: #e5e7eb !important;
    box-shadow: none !important;
}
.inventory-shell .inventory-product-thumb svg { width: 23px !important; height: 23px !important; }
.inventory-shell .inventory-product-name {
    color: #293440 !important;
    font-size: 12.5px !important;
    font-weight: 750 !important;
    line-height: 1.5 !important;
}
.inventory-shell .inventory-product-category {
    margin-top: 4px !important;
    color: #8793a1 !important;
    font-size: 11.5px !important;
    line-height: 1.5 !important;
}
.inventory-shell .inventory-sku { color: #667383 !important; font-size: 12px !important; line-height: 1.5; }
.inventory-shell .inventory-stock-number {
    color: #293440 !important;
    font-size: 12.5px !important;
    font-weight: 750 !important;
    font-variant-numeric: tabular-nums;
}
.inventory-shell .inventory-badge {
    min-height: 25px !important;
    padding: 5px 11px !important;
    gap: 7px !important;
    border: 1px solid transparent;
    border-radius: 999px;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    line-height: 1.3;
}
.inventory-shell .inventory-badge::before { flex: 0 0 6px; width: 6px !important; height: 6px !important; }
.inventory-shell .inventory-badge.healthy { border-color: #bde7c9; color: #18794e; background: #effcf3; }
.inventory-shell .inventory-badge.low { border-color: #e9dfcd; color: #a56d0a; background: #f8fafc; }
.inventory-shell .inventory-badge.out { border-color: #f0cfcb; color: #a84e46; background: #fff4f2; }
.inventory-shell .inventory-variant-control { position: relative; min-width: 0; }
.inventory-shell .inventory-variant-native-chevron {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    width: 17px;
    height: 17px;
    color: #6b7280;
}
.inventory-shell .inventory-select,
.inventory-shell .inventory-qty,
.inventory-shell .inventory-update-btn,
.inventory-shell .inventory-variant-trigger {
    height: 48px !important;
    min-width: 0;
    width: 100%;
    border-radius: 14px !important;
    font-size: 11.5px !important;
    line-height: 1.4 !important;
}
.inventory-shell .inventory-select,
.inventory-shell .inventory-qty,
.inventory-shell .inventory-variant-trigger {
    border: 1px solid #dce2e8 !important;
    padding: 0 14px !important;
    color: #3f4b59 !important;
    background: #fff !important;
    box-shadow: none !important;
}
.inventory-shell .inventory-select { appearance: none; padding-right: 36px !important; }
.inventory-shell .inventory-select:disabled { background: #f8fafc !important; color: #94a3b8 !important; }
.inventory-shell .inventory-qty:focus,
.inventory-shell .inventory-select:focus,
.inventory-shell .inventory-variant-trigger:focus-visible {
    outline: 0;
    border-color: #c88912 !important;
    box-shadow: 0 0 0 1px rgba(200,137,18,.16) !important;
}
.inventory-shell .inventory-update-btn,
.inventory-shell .inventory-update-btn:hover {
    border: 1px solid #c88912 !important;
    background: #c88912 !important;
    color: #fff !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    box-shadow: none !important;
    transform: none !important;
}
.inventory-shell .inventory-update-btn:not(:disabled):hover { background: #b47b0e !important; border-color: #b47b0e !important; }
.inventory-shell .inventory-update-btn:disabled { opacity: .45 !important; cursor: not-allowed; }
/* Reusable 3-dot-reference-style custom dropdown for per-product variants.
   The real select is retained for form validation and submission. */
.inventory-shell .inventory-variant-control.is-enhanced .inventory-select[data-inventory-variant-select] {
    position: absolute;
    width: 1px !important;
    height: 1px !important;
    overflow: hidden;
    opacity: 0;
    pointer-events: none;
    z-index: -1;
}
.inventory-shell .inventory-variant-control.is-enhanced .inventory-variant-native-chevron { display: none; }
.inventory-shell .inventory-variant-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    cursor: pointer;
    text-align: left;
    font-weight: 600 !important;
}
.inventory-shell .inventory-variant-trigger span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.inventory-shell .inventory-variant-trigger svg { flex: 0 0 17px; width: 17px; height: 17px; color: #6b7280; }
.inventory-variant-popup {
    position: fixed !important;
    /* Do not mark inset !important: JS sets viewport-relative top/left inline. */
    inset: auto;
    display: none;
    box-sizing: border-box;
    margin: 0 !important;
    padding: 8px;
    max-height: min(310px, calc(100vh - 24px));
    overflow-y: auto;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    background: #fff;
    color: #475569;
    box-shadow: 0 16px 32px rgba(15,23,42,.12), 0 2px 6px rgba(15,23,42,.05);
    font-family: Poppins,ui-sans-serif,system-ui,sans-serif;
    z-index: 1000;
}
.inventory-variant-popup:popover-open { display: block; }
.inventory-variant-popup .inventory-variant-option {
    display: block;
    width: 100%;
    min-height: 38px;
    padding: 7px 12px;
    border: 0;
    border-radius: 10px;
    background: transparent;
    color: #475569;
    font: 600 11.5px/1.4 Poppins,ui-sans-serif,sans-serif;
    text-align: left;
    cursor: pointer;
}
.inventory-variant-popup .inventory-variant-option:hover,
.inventory-variant-popup .inventory-variant-option:focus-visible { outline: 0; background: #f8fafc; color: #a56d0a; }
.inventory-variant-popup .inventory-variant-option.is-active { background: #f8fafc; color: #2f3742; font-weight: 700; }
/* Variant menu uses a fixed, viewport-anchored top-layer panel (never the sidebar). */
.inventory-variant-popup {
    overscroll-behavior: contain;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}
.inventory-variant-popup .inventory-variant-popup-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin: 0 2px 5px;
    padding: 8px 10px 10px;
    border-bottom: 1px solid #edf0f4;
    color: #8190a0;
    font: 700 10px/1.4 Poppins, ui-sans-serif, sans-serif;
    letter-spacing: .035em;
}
.inventory-variant-popup .inventory-variant-popup-count {
    font-weight: 600;
    letter-spacing: 0;
    color: #94a3b8;
    white-space: nowrap;
}
.inventory-variant-popup .inventory-variant-option {
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 58px;
    gap: 5px;
    padding: 10px 12px;
    border-radius: 9px;
}
.inventory-variant-popup .inventory-variant-option + .inventory-variant-option {
    margin-top: 2px;
}
.inventory-variant-popup .inventory-variant-option-title {
    color: #293440;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.4;
}
.inventory-variant-popup .inventory-variant-option-detail {
    margin-top: 0;
    color: #8a96a6;
    font-size: 10.5px;
}
.inventory-variant-popup .inventory-variant-option-stock {
    background: #f1f5f9;
    color: #64748b;
    border-radius: 7px;
    padding: 3px 7px;
}
.inventory-variant-popup .inventory-variant-option.is-active,
.inventory-variant-popup .inventory-variant-option:hover,
.inventory-variant-popup .inventory-variant-option:focus-visible {
    background: #f8fafc;
}
.inventory-variant-popup .inventory-variant-option.is-active .inventory-variant-option-title {
    color: #a56d0a;
}
.inventory-variant-popup .inventory-variant-option.is-active .inventory-variant-option-stock {
    background: #f1f5f9;
    color: #64748b;
}

/* Full-width empty-state treatment borrowed from Buyer Cart. */
.inventory-shell .inventory-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 270px;
    padding: 72px 20px !important;
    background: #fff !important;
    color: #9ca4ad;
    text-align: center;
}
.inventory-shell .inventory-empty.inventory-hidden { display: none !important; }
.inventory-shell .inventory-empty-icon,
.inventory-shell .inventory-activity-empty-icon {
    display: grid;
    place-items: center;
    width: 92px !important;
    height: 92px !important;
    margin: 0 auto;
    border: 0;
    border-radius: 0;
    background: transparent !important;
    box-shadow: none !important;
    color: #b4bbc3 !important;
}
.inventory-shell .inventory-empty-icon svg,
.inventory-shell .inventory-activity-empty-icon svg {
    display: block;
    width: 58px !important;
    height: 58px !important;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.45;
    stroke-linecap: round;
    stroke-linejoin: round;
}
.inventory-shell .inventory-empty h3,
.inventory-shell .inventory-activity-empty strong {
    display: block;
    margin: 12px 0 0 !important;
    color: #9ca4ad !important;
    font-size: 22px !important;
    line-height: 1.2 !important;
    font-weight: 750 !important;
    letter-spacing: -.025em;
}
.inventory-shell .inventory-empty p,
.inventory-shell .inventory-activity-empty span:not(.inventory-activity-empty-icon) {
    display: block;
    max-width: 520px;
    margin: 7px auto 0 !important;
    color: #a2acb8 !important;
    font-size: 12px !important;
    line-height: 1.55 !important;
    font-weight: 500 !important;
}
.inventory-shell .inventory-empty-action {
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
    cursor: pointer;
}
.inventory-shell .inventory-empty-action:hover { border-color: #b47b0e; background: #b47b0e; }
.inventory-shell .inventory-activity-panel { margin-top: 20px !important; padding: 0 !important; }
.inventory-shell .inventory-activity-head {
    gap: 10px !important;
    padding: 21px 23px 0 !important;
}
.inventory-shell .inventory-activity-list { gap: 0 !important; margin: 16px 23px 20px !important; }
.inventory-shell .inventory-activity-item,
.inventory-shell .inventory-activity-item:hover {
    padding: 14px 0 !important;
    gap: 16px !important;
    border: 0 !important;
    border-bottom: 1px solid #edf0f3 !important;
    border-radius: 0 !important;
    background: #fff !important;
    box-shadow: none !important;
}
.inventory-shell .inventory-activity-item:last-child { border-bottom: 0 !important; }
.inventory-shell .inventory-activity-name,
.inventory-shell .inventory-activity-value {
    color: #293440 !important;
    font-size: 12.5px !important;
    line-height: 1.5 !important;
    font-weight: 700 !important;
}
.inventory-shell .inventory-activity-meta {
    margin-top: 5px !important;
    color: #8793a1 !important;
    font-size: 11.5px !important;
}
.inventory-shell .inventory-activity-empty { min-height: 0 !important; padding: 42px 20px 60px; }
/* Keep server-side Laravel pagination under the main table card. */
.inventory-shell .inventory-pagination-footer {
    display: block;
    padding: 13px 20px;
    border-top: 1px solid #edf0f4;
    background: #fff;
    color: #8a96a6;
    font-size: 11px;
}
.inventory-shell .inventory-pagination-footer nav { color: #8a96a6; font-family: Poppins,sans-serif; }
.inventory-shell .inventory-pagination-footer nav a,
.inventory-shell .inventory-pagination-footer nav span {
    border-radius: 9px;
    border-color: #e5e7eb !important;
    font-size: 11px;
}
.inventory-shell .inventory-pagination-footer nav span[aria-current="page"] > span {
    background: #c88912 !important;
    border-color: #c88912 !important;
    color: #fff !important;
}
@media (max-width: 1280px) {
    .inventory-shell .inventory-insight-grid { grid-template-columns: 1fr !important; }
    .inventory-shell .inventory-donut-body { grid-template-columns: 147px minmax(0,1fr) !important; }
}
@media (max-width: 1180px) {
    .inventory-shell .inventory-stat-grid { grid-template-columns: repeat(2,minmax(0,1fr)) !important; }
}
@media (max-width: 900px) {
    .inventory-shell .inventory-admin-filters { grid-template-columns: repeat(2,minmax(0,1fr)); }
    .inventory-shell .inventory-filter-search,
    .inventory-shell .inventory-filter-dropdown { grid-column: 1/-1; }
    .inventory-shell .inventory-filter-apply,
    .inventory-shell .inventory-filter-reset { width: 100%; }
    .inventory-shell .inventory-health-cards { grid-template-columns: repeat(3,minmax(0,1fr)) !important; }
}
@media (max-width: 767px) {
    .inventory-shell .inventory-head { flex-direction: column; align-items: stretch; }
    .inventory-shell .inventory-title { font-size: 25px !important; }
    .inventory-shell .inventory-eyebrow { font-size: 9.5px !important; }
    .inventory-shell .inventory-stat-label { font-size: 12.5px !important; }
    .inventory-shell .inventory-stat-card { min-height: 106px !important; padding: 13px 14px !important; }
    .inventory-shell .inventory-panel-head,
    .inventory-shell .inventory-table-head,
    .inventory-shell .inventory-activity-head { padding: 18px !important; }
    .inventory-shell .inventory-health-cards,
    .inventory-shell .inventory-donut-body { padding: 16px 18px 18px !important; }
}
@media (max-width: 640px) {
    .inventory-shell .inventory-health-cards { grid-template-columns: 1fr !important; }
    .inventory-shell .inventory-donut-body { grid-template-columns: 1fr !important; }
    .inventory-shell .inventory-table-head { flex-direction: row !important; align-items: center !important; }
    .inventory-shell .inventory-table-counter { margin: 0; }
}
@media (max-width: 540px) {
    .inventory-shell { padding-bottom: 20px !important; }
    .inventory-shell .inventory-stat-grid { grid-template-columns: 1fr !important; gap: 11px !important; }
    .inventory-shell .inventory-stat-label { font-size: 12px !important; }
    .inventory-shell .inventory-admin-filters { gap: 9px; }
    .inventory-shell .inventory-empty { padding: 56px 16px !important; min-height: 220px; }
    .inventory-shell .inventory-empty-icon,
    .inventory-shell .inventory-activity-empty-icon { width: 64px !important; height: 64px !important; }
    .inventory-shell .inventory-empty-icon svg,
    .inventory-shell .inventory-activity-empty-icon svg { width: 40px !important; height: 40px !important; }
    .inventory-shell .inventory-empty h3,
    .inventory-shell .inventory-activity-empty strong { font-size: 20px !important; }
    .inventory-shell .inventory-empty p,
    .inventory-shell .inventory-activity-empty span:not(.inventory-activity-empty-icon) { font-size: 11px !important; }
    .inventory-shell .inventory-empty-action { min-width: 160px; min-height: 46px; }
}
@media (prefers-reduced-motion: reduce) {
    .inventory-shell *, .inventory-shell *::before, .inventory-shell *::after,
    .inventory-variant-popup * { transition: none !important; animation: none !important; }
}


/* INVENTORY — refined variant choices & on-demand history (UI only). */
.inventory-shell .inventory-variant-trigger.is-placeholder { color: #8290a0 !important; }
.inventory-shell .inventory-variant-trigger:not(.is-placeholder) { color: #293440 !important; }
.inventory-shell .inventory-variant-trigger:hover { background: #f8fafc !important; border-color: #cfd6de !important; }
.inventory-variant-popup { padding: 7px !important; }
.inventory-variant-popup .inventory-variant-option {
    min-height: 44px;
    padding: 9px 12px;
    border-radius: 10px;
    white-space: normal;
}
.inventory-variant-popup .inventory-variant-option-title {
    display: block;
    color: inherit;
    font-size: 11.5px;
    font-weight: 650;
    line-height: 1.45;
    overflow-wrap: anywhere;
}
.inventory-variant-popup .inventory-variant-option-detail {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 5px 10px;
    margin-top: 4px;
    color: #8492a3;
    font-size: 10.5px;
    font-weight: 500;
    line-height: 1.4;
}
.inventory-variant-popup .inventory-variant-option-stock {
    color: #64748b;
    background: #f1f5f9;
    border-radius: 6px;
    padding: 3px 6px;
    white-space: nowrap;
    font-size: 10px;
    font-weight: 650;
}
.inventory-variant-popup .inventory-variant-option--placeholder .inventory-variant-option-title {
    color: #64748b;
    font-weight: 500;
}
.inventory-variant-popup .inventory-variant-option.is-active .inventory-variant-option-stock {
    color: #a56d0a;
    background: #fff8e9;
}
.inventory-shell .inventory-activity-disclosure {
    display: block;
    margin-top: 16px;
}
.inventory-shell .inventory-activity-toggle {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    width: fit-content;
    margin: 0 0 0 auto;
    padding: 8px 1px;
    gap: 8px;
    border: 0;
    background: transparent;
    color: #59636e;
    cursor: pointer;
    font: 750 11.5px/1.35 Poppins, ui-sans-serif, sans-serif;
    list-style: none;
    transition: color .16s ease;
}
.inventory-shell .inventory-activity-toggle::-webkit-details-marker { display: none; }
.inventory-shell .inventory-activity-toggle::marker { content: ''; }
.inventory-shell .inventory-activity-toggle svg {
    width: 17px;
    height: 17px;
    color: inherit;
    transition: transform .16s ease;
}
.inventory-shell .inventory-activity-toggle:hover,
.inventory-shell .inventory-activity-toggle:focus-visible { color: #c88912; }
.inventory-shell .inventory-activity-toggle:focus-visible {
    outline: 2px solid #c88912;
    outline-offset: 3px;
    border-radius: 3px;
}
.inventory-shell .inventory-activity-toggle .inventory-toggle-open { display: none; }
.inventory-shell .inventory-activity-disclosure[open] .inventory-toggle-closed { display: none; }
.inventory-shell .inventory-activity-disclosure[open] .inventory-toggle-open { display: inline; }
.inventory-shell .inventory-activity-disclosure[open] .inventory-activity-toggle svg { transform: rotate(180deg); }
.inventory-shell .inventory-activity-disclosure .inventory-activity-panel {
    margin-top: 10px !important;
}
@media (max-width: 640px) {
    .inventory-shell .inventory-activity-disclosure { margin-top: 12px; }
    .inventory-shell .inventory-activity-toggle { font-size: 10.5px; }
    .inventory-shell .inventory-activity-toggle svg { width: 16px; height: 16px; }
}
@media (prefers-reduced-motion: reduce) {
    .inventory-shell .inventory-activity-toggle,
    .inventory-shell .inventory-activity-toggle svg { transition: none !important; }
}


.inventory-shell .inventory-activity-name {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px 10px;
}
.inventory-shell .inventory-activity-reason {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 999px;
    background: #f1f5f9;
    color: #64748b;
    font-size: 10px;
    line-height: 1.45;
    font-weight: 600;
}
.inventory-shell .inventory-activity-value {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 9px;
    white-space: nowrap;
    font-variant-numeric: tabular-nums;
}
.inventory-shell .inventory-activity-value > span:first-child { color: #8a96a6; font-weight: 550; }
.inventory-shell .inventory-activity-value .inventory-activity-arrow { color: #c88912; font-size: 15px; }
.inventory-shell .inventory-activity-value strong { color: #293440; font-size: 12.5px; font-weight: 750; }
@media (max-width: 540px) {
    .inventory-shell .inventory-activity-item { flex-wrap: wrap; }
    .inventory-shell .inventory-activity-value { margin-left: auto; }
}

/* ========================================================================
   INVENTORY FILTER BAR — FIXED POSITION ON HOVER / FOCUS
   Visual-only: lock toolbar and controls to their normal layout position.
   Preserve existing dropdown menu, filtering, and stock adjustment logic.
   ======================================================================== */
.inventory-shell .inventory-admin-filters,
.inventory-shell .inventory-admin-filters:hover,
.inventory-shell .inventory-admin-filters:focus-within {
    margin: 20px 0 0 !important;
    transform: none !important;
    translate: none !important;
}

/* Only the four filter controls: do not modify the menu, icon or table. */
.inventory-shell .inventory-admin-filters :is(
    .inventory-filter-search,
    .inventory-filter-search input,
    .inventory-filter-dropdown,
    .inventory-filter-trigger,
    .inventory-filter-apply,
    .inventory-filter-reset
),
.inventory-shell .inventory-admin-filters :is(
    .inventory-filter-search,
    .inventory-filter-search input,
    .inventory-filter-dropdown,
    .inventory-filter-trigger,
    .inventory-filter-apply,
    .inventory-filter-reset
):is(:hover, :focus, :focus-visible, :active) {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
    top: auto !important;
    bottom: auto !important;
    transform: none !important;
    translate: none !important;
}

@endverbatim
</style>
@endpush

@section('content')

@php

    $goodStock = max((int) ($stats['products'] ?? 0) - (int) ($stats['low_stock'] ?? 0) - (int) ($stats['out_of_stock'] ?? 0), 0);

    $lowStock = (int) ($stats['low_stock'] ?? 0);

    $outStock = (int) ($stats['out_of_stock'] ?? 0);

    $totalProducts = max((int) ($stats['products'] ?? 0), 1);



    $goodPercent = round(($goodStock / $totalProducts) * 100);

    $lowPercent = round(($lowStock / $totalProducts) * 100);

    $outPercent = max(0, 100 - $goodPercent - $lowPercent);



    $stockChartItems = [

        ['label' => 'Healthy', 'value' => $goodStock, 'class' => 'healthy'],

        ['label' => 'Low', 'value' => $lowStock, 'class' => 'low'],

        ['label' => 'Out', 'value' => $outStock, 'class' => 'out'],

    ];

    $maxChartValue = max(1, $goodStock, $lowStock, $outStock);

@endphp









<div class="inventory-shell">

    @if(session('success'))

        <div class="mb-4 rounded-[12px] border border-[#cfe4d7] bg-[#f2faf5] px-4 py-3 text-[9px] font-medium text-[#4f7d63]">{{ session('success') }}</div>

    @endif



    @if($errors->any())

        <div class="mb-4 rounded-[12px] border border-[#efcece] bg-[#fff5f5] px-4 py-3 text-[9px] font-medium text-[#a65353]">{{ $errors->first() }}</div>

    @endif



    <header class="inventory-head inventory-head--single">

        <div>

            <p class="inventory-eyebrow">Seller Catalog</p>

            <h1 class="inventory-title">Inventory</h1>


        </div>

    </header>



    <section class="inventory-stat-grid">

        <div class="inventory-stat-card">

            <span class="inventory-stat-icon inventory-stat-icon--products">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"></path><path d="m4.5 7.8 7.5 4.3 7.5-4.3"></path><path d="M12 12v9"></path></svg>

            </span>

            <div>

                <p class="inventory-stat-label">Products</p>

                <p class="inventory-stat-value">{{ number_format($stats['products'] ?? 0) }}</p>

                <p class="inventory-stat-note">Tracked product listings</p>

            </div>

        </div>



        <div class="inventory-stat-card">

            <span class="inventory-stat-icon inventory-stat-icon--units">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3 8 4-8 4-8-4 8-4Z"></path><path d="m4 12 8 4 8-4"></path><path d="m4 17 8 4 8-4"></path></svg>

            </span>

            <div>

                <p class="inventory-stat-label">Total Units</p>

                <p class="inventory-stat-value">{{ number_format($stats['units'] ?? 0) }}</p>

                <p class="inventory-stat-note">Combined available stock</p>

            </div>

        </div>



        <div class="inventory-stat-card">

            <span class="inventory-stat-icon inventory-stat-icon--low">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 4 3.8 18.5a1.2 1.2 0 0 0 1 1.8h14.4a1.2 1.2 0 0 0 1-1.8L12 4Z"></path><path d="M12 9v5"></path><path d="M12 17h.01"></path></svg>

            </span>

            <div>

                <p class="inventory-stat-label">Low Stock</p>

                <p class="inventory-stat-value">{{ number_format($stats['low_stock'] ?? 0) }}</p>

                <p class="inventory-stat-note">Needs replenishment soon</p>

            </div>

        </div>



        <div class="inventory-stat-card">

            <span class="inventory-stat-icon inventory-stat-icon--out">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"></circle><path d="m9 9 6 6"></path><path d="m15 9-6 6"></path></svg>

            </span>

            <div>

                <p class="inventory-stat-label">Out of Stock</p>

                <p class="inventory-stat-value">{{ number_format($stats['out_of_stock'] ?? 0) }}</p>

                <p class="inventory-stat-note">Currently unavailable</p>

            </div>

        </div>

    </section>



    <section class="inventory-insight-grid">

        <div class="inventory-panel">

            <div class="inventory-panel-head">

                <span class="inventory-panel-icon">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 19V9"></path><path d="M10 19V5"></path><path d="M15 19v-7"></path><path d="M20 19V3"></path></svg>

                </span>

                <div>

                    <h2 class="inventory-panel-title">Stock Overview</h2>

                    <p class="inventory-panel-subtitle">Quick comparison of healthy, low, and out-of-stock listings.</p>

                </div>

            </div>



            <div class="inventory-health-cards">

                <div class="inventory-health-card">

                    <div class="inventory-health-label"><span class="inventory-health-dot inventory-health-dot--healthy"></span>Healthy</div>

                    <div class="inventory-health-value"><strong>{{ number_format($goodStock) }}</strong><span>listings</span></div>

                    <div class="inventory-progress-row">

                        <div class="inventory-progress inventory-progress--healthy"><span style="width: {{ $goodPercent }}%;"></span></div>

                        <span class="inventory-progress-percent">{{ $goodPercent }}%</span>

                    </div>

                </div>



                <div class="inventory-health-card">

                    <div class="inventory-health-label"><span class="inventory-health-dot inventory-health-dot--low"></span>Low Stock</div>

                    <div class="inventory-health-value"><strong>{{ number_format($lowStock) }}</strong><span>listings</span></div>

                    <div class="inventory-progress-row">

                        <div class="inventory-progress inventory-progress--low"><span style="width: {{ $lowPercent }}%;"></span></div>

                        <span class="inventory-progress-percent">{{ $lowPercent }}%</span>

                    </div>

                </div>



                <div class="inventory-health-card">

                    <div class="inventory-health-label"><span class="inventory-health-dot inventory-health-dot--out"></span>Out of Stock</div>

                    <div class="inventory-health-value"><strong>{{ number_format($outStock) }}</strong><span>listings</span></div>

                    <div class="inventory-progress-row">

                        <div class="inventory-progress inventory-progress--out"><span style="width: {{ $outPercent }}%;"></span></div>

                        <span class="inventory-progress-percent">{{ $outPercent }}%</span>

                    </div>

                </div>

            </div>

        </div>



        <div class="inventory-panel">

            <div class="inventory-panel-head">

                <span class="inventory-panel-icon">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v9h9"></path><path d="M19.4 16.5A8 8 0 1 1 8 4.1"></path></svg>

                </span>

                <div>

                    <h2 class="inventory-panel-title">Stock Health</h2>

                    <p class="inventory-panel-subtitle">Donut summary based on all tracked products.</p>

                </div>

            </div>



            <div class="inventory-donut-body">

                <div class="inventory-donut" style="--good: {{ $goodPercent }}; --low: {{ $lowPercent }};">

                    <div class="inventory-donut-center">

                        <div>

                            <strong>{{ $goodPercent }}%</strong>

                            <span>Healthy stock</span>

                        </div>

                    </div>

                </div>



                <div class="inventory-legend">

                    <div class="inventory-legend-row">

                        <span class="inventory-health-dot inventory-health-dot--healthy"></span>

                        <span>Healthy stock</span>

                        <strong>{{ number_format($goodStock) }}</strong>

                        <em>{{ $goodPercent }}%</em>

                    </div>

                    <div class="inventory-legend-row">

                        <span class="inventory-health-dot inventory-health-dot--low"></span>

                        <span>Low stock</span>

                        <strong>{{ number_format($lowStock) }}</strong>

                        <em>{{ $lowPercent }}%</em>

                    </div>

                    <div class="inventory-legend-row">

                        <span class="inventory-health-dot inventory-health-dot--out"></span>

                        <span>Out of stock</span>

                        <strong>{{ number_format($outStock) }}</strong>

                        <em>{{ $outPercent }}%</em>

                    </div>

                </div>

            </div>

        </div>

    </section>



    {{-- Pickup Requests visual system: standalone search, custom status dropdown and actions. --}}
    <section class="inventory-admin-filters" aria-label="Search and filter inventory">
        <label class="inventory-filter-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.4-3.4"/></svg>
            <span class="sr-only">Search inventory products</span>
            <input id="inventoryTableSearch" type="search" autocomplete="off" placeholder="Search products, SKU, category or variant...">
        </label>
        <div class="inventory-filter-dropdown" data-inventory-filter-menu>
            <button type="button" class="inventory-filter-trigger" data-inventory-filter-trigger aria-haspopup="listbox" aria-expanded="false" aria-label="Filter by stock status">
                <span data-inventory-filter-label>All Status</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
            </button>
            <div class="inventory-filter-panel" data-inventory-filter-panel role="listbox" aria-label="Stock status" hidden>
                <button type="button" class="inventory-filter-option is-active" data-inventory-filter-option data-value="all" role="option" aria-selected="true">All Status</button>
                <button type="button" class="inventory-filter-option" data-inventory-filter-option data-value="healthy" role="option" aria-selected="false">Healthy</button>
                <button type="button" class="inventory-filter-option" data-inventory-filter-option data-value="low" role="option" aria-selected="false">Low Stock</button>
                <button type="button" class="inventory-filter-option" data-inventory-filter-option data-value="out" role="option" aria-selected="false">Out of Stock</button>
            </div>
        </div>
        <button type="button" class="inventory-filter-apply" data-inventory-filter-apply>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
            Apply Filter
        </button>
        <button type="button" class="inventory-filter-reset" data-inventory-filter-reset>Reset</button>
    </section>

    <section class="inventory-table-panel">

        <div class="inventory-table-head">

            <div class="inventory-table-head-copy">

                <span class="inventory-table-head-icon">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"></path><path d="m4.5 7.8 7.5 4.3 7.5-4.3"></path><path d="M12 12v9"></path></svg>

                </span>

                <div>

                    <h2 class="inventory-table-title">Stock by Product</h2>

                    <p class="inventory-table-subtitle">View product images, search items, and adjust stock with quick updates.</p>

                </div>

            </div>



            <span class="inventory-table-counter" data-inventory-table-count role="status" aria-live="polite">Showing {{ $products->count() }} of {{ $products->total() }}</span>

        </div>



        <div class="inventory-table-scroller" tabindex="0" role="region" aria-label="Scrollable product inventory table">
        <div class="inventory-grid-head">

            <span>Product</span>

            <span>SKU</span>

            <span>Status</span>

            <span>Current Stock</span>

            <span>Variant</span>

            <span>Adjust Quantity</span>

            <span>Actions</span>

        </div>



        <div id="inventoryProductList">

            @forelse($products as $product)

                @php

                    $currentStock = (int) ($product->stock ?? 0);

                    $threshold = (int) ($product->low_stock_threshold ?? 0);

                    $state = $currentStock <= 0 ? 'out' : ($currentStock <= $threshold ? 'low' : 'healthy');

                    $stateLabel = $currentStock <= 0 ? 'Out of Stock' : ($currentStock <= $threshold ? 'Low Stock' : 'Healthy');



                    $variantSearch = $product->activeVariants->map(function ($variant) {

                        return trim(($variant->sku ?: ('#' . $variant->id)) . ' ' . ($variant->name ?? '') . ' ' . ($variant->option_summary ?? ''));

                    })->implode(' ');



                    $imagePath = $product->cover_image_url

                        ?? $product->cover_image_path

                        ?? $product->image_url

                        ?? $product->image_path

                        ?? optional($product->images->first())->path

                        ?? optional($product->galleryImages->first())->path

                        ?? null;



                    $imageUrl = null;

                    if ($imagePath) {

                        $imageUrl = \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://', '/storage/', '/images/', '/uploads/'])

                            ? $imagePath

                            : asset('storage/' . ltrim($imagePath, '/'));

                    }



                    $productCategory = $product->category ?? $product->custom_category ?? 'Product listing';

                @endphp



                <div

                    class="inventory-row inventory-searchable-row"

                    data-search="{{ strtolower(trim($product->name . ' ' . ($product->sku ?: '') . ' ' . $productCategory . ' ' . $variantSearch)) }}"

                    data-inventory-stock-state="{{ $state }}"
                    data-product-stock="{{ $currentStock }}"

                    data-low-stock-threshold="{{ $threshold }}"

                >

                    <div class="inventory-product-cell">

                        <div class="inventory-product-thumb">

                            @if($imageUrl)

                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy" decoding="async">

                            @else

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="3"></rect><path d="m7 15 3-3 3 3 4-5 3 4"></path><circle cx="9" cy="9" r="1.2" fill="currentColor" stroke="none"></circle></svg>

                            @endif

                        </div>



                        <div class="min-w-0">

                            <p class="inventory-product-name">{{ $product->name }}</p>

                            <p class="inventory-product-category">{{ $productCategory }}</p>

                        </div>

                    </div>



                    <div class="inventory-sku">{{ $product->sku ?: '—' }}</div>



                    <div>

                        <span class="inventory-badge {{ $state }}" data-inventory-status-badge>{{ $stateLabel }}</span>

                    </div>



                    <div class="inventory-stock-number" data-inventory-current-stock>{{ number_format($currentStock) }}</div>



                    <form method="POST" action="{{ route('seller.inventory.adjust', $product) }}" class="contents">

                        @csrf



                        <div class="inventory-variant-control">

                            @if($product->activeVariants->isNotEmpty())

                                <select name="variant_id" required class="inventory-select" data-inventory-variant-select>

                                    <option value="" data-stock="" data-variant-label="Choose variant">Choose variant</option>

                                    @foreach($product->activeVariants as $variant)

                                        <option

                                            value="{{ $variant->id }}"

                                            data-stock="{{ (int) $variant->stock }}"
                                            data-variant-label="{{ $variant->name ?: ($variant->option_summary ?: ($variant->sku ?: 'Variant #'.$variant->id)) }}"
                                            data-variant-sku="{{ $variant->sku ?: '#'.$variant->id }}"
                                        >
                                            {{ $variant->name ?: ($variant->option_summary ?: ($variant->sku ?: 'Variant #'.$variant->id)) }} · {{ number_format((int) $variant->stock) }} available

                                        </option>

                                    @endforeach

                                </select>

                            @else

                                <input type="hidden" name="variant_id" value="">

                                <select class="inventory-select" disabled>

                                    <option>Standard product</option>

                                </select>

                            @endif

                            <svg class="inventory-variant-native-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>

                        </div>



                        <div>

                            <input

                                name="quantity"

                                type="number"

                                min="0"

                                required

                                value="{{ $product->activeVariants->isEmpty() ? $currentStock : '' }}"

                                placeholder="New stock"

                                class="inventory-qty"

                                data-inventory-quantity

                            >

                            <input type="hidden" name="reason" value="manual_adjustment">

                        </div>



                        <div>

                            <button

                                class="inventory-update-btn"

                                type="submit"

                                data-inventory-update

                                @disabled($product->activeVariants->isNotEmpty())

                            >

                                Update

                            </button>

                        </div>

                    </form>

                </div>

            @empty

                <div class="inventory-empty" role="status">
                    <span class="inventory-empty-icon" aria-hidden="true"><svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 22 32 11l21 11v25L32 58 11 47V22Z"/><path d="m11 22 21 12 21-12M32 34v24M21 17l21 12"/></svg></span>
                    <h3>No active products found</h3>
                    <p>Products you publish in your seller catalog will appear here for stock management.</p>
                </div>

            @endforelse

        </div>



        <div id="inventoryNoSearchResults" class="inventory-empty inventory-hidden" role="status">
            <span class="inventory-empty-icon" aria-hidden="true"><svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="28" cy="28" r="18"/><path d="m42 42 13 13M22 28h12"/></svg></span>
            <h3>No matching products</h3>
            <p>Try another product name, SKU, variant or stock status.</p>
            <button type="button" class="inventory-empty-action" data-inventory-reset-from-empty>Clear filters</button>
        </div>



        </div> {{-- End horizontal product grid scrolling region --}}

        @if($products->hasPages())

            <div class="inventory-pagination-footer">{{ $products->links() }}</div>

        @endif

    </section>



    {{-- History is available on demand instead of permanently expanding the Inventory page. --}}
    <details class="inventory-activity-disclosure" id="inventoryRecentHistory">
        <summary class="inventory-activity-toggle">
            <span class="inventory-activity-toggle-text">
                <span class="inventory-toggle-closed">See Recent Inventory</span>
                <span class="inventory-toggle-open">Hide Recent Inventory</span>
            </span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
        </summary>

    <section class="inventory-activity-panel" aria-label="Recent inventory adjustments">

        <div class="inventory-activity-head">

            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"></circle><path d="M12 8v5l3 2"></path></svg>

            <div>

                <h2 class="inventory-activity-title">Recent Inventory Adjustments</h2>

                <p class="inventory-activity-subtitle">View the latest manual stock changes and system updates.</p>

            </div>

        </div>



        @if($movements->isEmpty())

            <div class="inventory-activity-empty">

                <div>

                    <span class="inventory-activity-empty-icon">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 3h10l3 3v15H6z"></path><path d="M16 3v4h4"></path><path d="M9 11h7"></path><path d="M9 15h7"></path></svg>

                    </span>

                    <strong>No recent adjustments</strong>

                    <span>No manual adjustments yet. Any stock changes you make will appear here.</span>

                </div>

            </div>

        @else

            <div class="inventory-activity-list">

                @foreach($movements as $movement)

                    <div class="inventory-activity-item">

                        <div>

                            <div class="inventory-activity-name">
                                <span>Product #{{ $movement->seller_product_id }}</span>
                                <span class="inventory-activity-reason">{{ ucwords(str_replace('_', ' ', $movement->reason)) }}</span>
                            </div>

                            <div class="inventory-activity-meta">{{ optional($movement->created_at)->format('M d, Y · h:i A') ?: 'Recent update' }}</div>

                        </div>

                        <div class="inventory-activity-value" aria-label="Stock changed from {{ $movement->quantity_before }} to {{ $movement->quantity_after }}">
                            <span>{{ number_format((int) $movement->quantity_before) }}</span>
                            <span class="inventory-activity-arrow" aria-hidden="true">→</span>
                            <strong>{{ number_format((int) $movement->quantity_after) }}</strong>
                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </section>
    </details>

</div>

@endsection





@push('scripts')

<script>

(() => {

    const bootInventoryPage = () => {

        const inventoryRoot = document.querySelector('.inventory-shell');
        if (!inventoryRoot) return;

        const tableSearch = inventoryRoot.querySelector('#inventoryTableSearch');
        const rows = Array.from(inventoryRoot.querySelectorAll('.inventory-searchable-row'));
        const emptyState = inventoryRoot.querySelector('#inventoryNoSearchResults');
        const tableCounter = inventoryRoot.querySelector('[data-inventory-table-count]');
        const statusMenu = inventoryRoot.querySelector('[data-inventory-filter-menu]');
        const statusTrigger = inventoryRoot.querySelector('[data-inventory-filter-trigger]');
        const statusPanel = inventoryRoot.querySelector('[data-inventory-filter-panel]');
        const statusLabel = inventoryRoot.querySelector('[data-inventory-filter-label]');
        const statusOptions = Array.from(inventoryRoot.querySelectorAll('[data-inventory-filter-option]'));
        let selectedStatus = 'all';



        const applyFilter = (value = tableSearch?.value || '') => {
            if (!emptyState) return;
            const keyword = String(value || '').trim().toLowerCase();
            let visible = 0;
            rows.forEach((row) => {
                const haystack = row.getAttribute('data-search') || '';
                const stockState = row.dataset.inventoryStockState || 'healthy';
                const matched = (!keyword || haystack.includes(keyword))
                    && (selectedStatus === 'all' || stockState === selectedStatus);
                row.classList.toggle('inventory-hidden', !matched);
                if (matched) visible += 1;
            });
            emptyState.classList.toggle('inventory-hidden', visible !== 0 || rows.length === 0);
            if (tableCounter) {
                const total = rows.length;
                tableCounter.textContent = keyword || selectedStatus !== 'all'
                    ? `Showing ${visible} of ${total} on this page`
                    : `Showing ${visible} of {{ $products->total() }}`;
            }
        };



        tableSearch?.addEventListener('input', () => applyFilter(), { passive: true });

        // Custom status filter: same trigger, sizing, and check-free options as Pickup Requests.
        const closeStatusMenu = () => {
            if (statusPanel) statusPanel.hidden = true;
            statusTrigger?.setAttribute('aria-expanded', 'false');
        };
        statusTrigger?.addEventListener('click', () => {
            if (!statusPanel) return;
            const willOpen = statusPanel.hidden;
            statusPanel.hidden = !willOpen;
            statusTrigger.setAttribute('aria-expanded', String(willOpen));
        });
        statusOptions.forEach((option) => option.addEventListener('click', () => {
            selectedStatus = option.dataset.value || 'all';
            statusLabel.textContent = option.textContent.trim();
            statusOptions.forEach((candidate) => {
                const active = candidate === option;
                candidate.classList.toggle('is-active', active);
                candidate.setAttribute('aria-selected', String(active));
            });
            closeStatusMenu();
            applyFilter();
        }));
        const resetInventoryFilters = () => {
            if (tableSearch) tableSearch.value = '';
            selectedStatus = 'all';
            if (statusLabel) statusLabel.textContent = 'All Status';
            statusOptions.forEach((option) => {
                const active = option.dataset.value === 'all';
                option.classList.toggle('is-active', active);
                option.setAttribute('aria-selected', String(active));
            });
            closeStatusMenu();
            applyFilter();
        };
        inventoryRoot.querySelector('[data-inventory-filter-apply]')?.addEventListener('click', () => applyFilter());
        inventoryRoot.querySelector('[data-inventory-filter-reset]')?.addEventListener('click', resetInventoryFilters);
        inventoryRoot.querySelector('[data-inventory-reset-from-empty]')?.addEventListener('click', resetInventoryFilters);
        document.addEventListener('pointerdown', (event) => {
            if (statusMenu && !statusMenu.contains(event.target)) closeStatusMenu();
        });
        statusMenu?.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') { closeStatusMenu(); statusTrigger?.focus(); }
            if (statusPanel?.hidden) return;
            const available = statusOptions;
            const position = available.indexOf(document.activeElement);
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                const next = event.key === 'ArrowDown' ? position + 1 : position - 1;
                available[(next + available.length) % available.length]?.focus();
            }
        });



        const setStatusBadge = (badge, stock, threshold) => {

            if (!badge) return;



            badge.classList.remove('healthy', 'low', 'out');



            if (stock <= 0) {

                badge.classList.add('out');

                badge.textContent = 'Out of Stock';

                return;

            }



            if (stock <= threshold) {

                badge.classList.add('low');

                badge.textContent = 'Low Stock';

                return;

            }



            badge.classList.add('healthy');

            badge.textContent = 'Healthy';

        };



        rows.forEach((row) => {

            const variantSelect = row.querySelector('[data-inventory-variant-select]');

            const quantityInput = row.querySelector('[data-inventory-quantity]');

            const updateButton = row.querySelector('[data-inventory-update]');

            const currentStockNode = row.querySelector('[data-inventory-current-stock]');

            const statusBadge = row.querySelector('[data-inventory-status-badge]');



            if (!variantSelect) return;



            const productStock = Number(row.dataset.productStock || 0);

            const threshold = Number(row.dataset.lowStockThreshold || 0);



            const syncVariantState = () => {

                const selectedOption = variantSelect.options[variantSelect.selectedIndex];

                const hasVariant = Boolean(variantSelect.value);

                const selectedStock = hasVariant

                    ? Number(selectedOption?.dataset?.stock || 0)

                    : productStock;



                if (currentStockNode) {

                    currentStockNode.textContent = selectedStock.toLocaleString('en-PH');

                }



                setStatusBadge(statusBadge, selectedStock, threshold);
                row.dataset.inventoryStockState = selectedStock <= 0 ? 'out' : (selectedStock <= threshold ? 'low' : 'healthy');
                if (selectedStatus !== 'all') applyFilter();

                if (quantityInput) {

                    if (hasVariant) {

                        quantityInput.value = String(selectedStock);

                        quantityInput.disabled = false;

                        quantityInput.focus({ preventScroll: true });

                        quantityInput.select?.();

                    } else {

                        quantityInput.value = '';

                        quantityInput.disabled = true;

                    }

                }



                if (updateButton) {

                    updateButton.disabled = !hasVariant;

                }

            };



            quantityInput && (quantityInput.disabled = true);

            variantSelect.addEventListener('change', syncVariantState);

            syncVariantState();

        });

        // Progressive-enhancement: keep the original <select> as the submitted
        // form control. The custom panel is only for presentation and selection.
        if (typeof document.createElement('div').showPopover === 'function') {
            rows.forEach((row, rowIndex) => {
                const select = row.querySelector('[data-inventory-variant-select]');
                const control = select?.closest('.inventory-variant-control');
                if (!select || !control || control.classList.contains('is-enhanced')) return;

                const trigger = document.createElement('button');
                trigger.type = 'button';
                trigger.className = 'inventory-variant-trigger';
                trigger.setAttribute('aria-haspopup', 'listbox');
                trigger.setAttribute('aria-expanded', 'false');
                trigger.setAttribute('aria-label', 'Choose product variant');
                const valueText = document.createElement('span');
                trigger.append(valueText);
                const caret = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                caret.setAttribute('viewBox', '0 0 24 24');
                caret.setAttribute('fill', 'none');
                caret.setAttribute('stroke', 'currentColor');
                caret.setAttribute('stroke-width', '1.8');
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                line.setAttribute('d', 'm7 10 5 5 5-5');
                caret.append(line);
                trigger.append(caret);

                const popup = document.createElement('div');
                popup.className = 'inventory-variant-popup';
                popup.id = `inventory-variant-options-${rowIndex + 1}`;
                popup.setAttribute('popover', 'auto');
                popup.setAttribute('role', 'listbox');
                popup.setAttribute('aria-label', 'Available product variants');
                trigger.setAttribute('aria-controls', popup.id);

                // Placeholder remains in the native control but not in the menu;
                // the menu shows only real, selectable variants.
                const variants = Array.from(select.options).filter((option) => option.value);
                const menuHeading = document.createElement('div');
                menuHeading.className = 'inventory-variant-popup-heading';
                menuHeading.setAttribute('role', 'presentation');
                const headingLabel = document.createElement('span');
                headingLabel.textContent = 'Available variants';
                const headingCount = document.createElement('span');
                headingCount.className = 'inventory-variant-popup-count';
                headingCount.textContent = `${variants.length} option${variants.length === 1 ? '' : 's'}`;
                menuHeading.append(headingLabel, headingCount);
                popup.append(menuHeading);

                const optionButtons = variants.map((nativeOption) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'inventory-variant-option';
                    button.dataset.value = nativeOption.value;
                    button.setAttribute('role', 'option');
                    const title = document.createElement('span');
                    title.className = 'inventory-variant-option-title';
                    title.textContent = nativeOption.dataset.variantLabel || nativeOption.textContent.trim();
                    const meta = document.createElement('span');
                    meta.className = 'inventory-variant-option-detail';
                    const sku = document.createElement('span');
                    sku.textContent = `SKU: ${nativeOption.dataset.variantSku || '—'}`;
                    const stock = document.createElement('span');
                    stock.className = 'inventory-variant-option-stock';
                    stock.textContent = `${Number(nativeOption.dataset.stock || 0).toLocaleString('en-PH')} in stock`;
                    meta.append(sku, stock);
                    button.append(title, meta);
                    button.addEventListener('click', () => {
                        select.value = nativeOption.value;
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                        if (popup.matches(':popover-open')) popup.hidePopover();
                        updateTrigger();
                    });
                    popup.append(button);
                    return button;
                });

                const updateTrigger = () => {
                    valueText.textContent = select.selectedOptions[0]?.dataset.variantLabel || 'Choose variant';
                    trigger.classList.toggle('is-placeholder', !select.value);
                    optionButtons.forEach((option) => {
                        const active = option.dataset.value === select.value;
                        option.classList.toggle('is-active', active);
                        option.setAttribute('aria-selected', String(active));
                    });
                };

                // Popover uses the browser top layer to escape the horizontally
                // scrollable inventory table. Position it using viewport coordinates.
                inventoryRoot.appendChild(popup);
                control.appendChild(trigger);
                control.classList.add('is-enhanced');
                select.tabIndex = -1;
                select.setAttribute('aria-hidden', 'true');
                select.addEventListener('change', updateTrigger);
                updateTrigger();

                const positionPopup = (measureActual = false) => {
                    const rect = trigger.getBoundingClientRect();
                    const viewportWidth = document.documentElement.clientWidth;
                    const viewportHeight = document.documentElement.clientHeight;
                    const width = Math.min(Math.max(rect.width, 230), viewportWidth - 24);
                    const estimatedHeight = Math.min(330, variants.length * 64 + 55);
                    const height = measureActual
                        ? Math.min(330, popup.getBoundingClientRect().height || estimatedHeight)
                        : estimatedHeight;
                    const below = Math.max(0, viewportHeight - rect.bottom - 12);
                    const above = Math.max(0, rect.top - 12);
                    const placeAbove = below < Math.min(height, 230) && above > below;
                    const available = Math.max(100, placeAbove ? above : below);
                    popup.style.maxHeight = `${Math.min(330, available, viewportHeight - 24)}px`;
                    popup.style.width = `${width}px`;
                    const clampedHeight = Math.min(height, available);
                    const left = Math.min(Math.max(12, rect.left), viewportWidth - width - 12);
                    const top = placeAbove
                        ? rect.top - clampedHeight - 8
                        : rect.bottom + 8;
                    popup.style.left = `${Math.round(left)}px`;
                    popup.style.top = `${Math.round(Math.min(Math.max(12, top), viewportHeight - clampedHeight - 12))}px`;
                };
                const openPopup = () => {
                    positionPopup();
                    popup.showPopover();
                    positionPopup(true);
                };
                trigger.addEventListener('click', () => {
                    if (popup.matches(':popover-open')) popup.hidePopover();
                    else openPopup();
                });
                popup.addEventListener('toggle', () => {
                    trigger.setAttribute('aria-expanded', String(popup.matches(':popover-open')));
                });
                const closeOnScroll = () => {
                    if (popup.matches(':popover-open')) popup.hidePopover();
                };
                inventoryRoot.querySelector('.inventory-table-scroller')?.addEventListener('scroll', closeOnScroll, { passive: true });
                window.addEventListener('scroll', closeOnScroll, { passive: true });
                window.addEventListener('resize', closeOnScroll, { passive: true });
                trigger.addEventListener('keydown', (event) => {
                    if (event.key === 'ArrowDown' || event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        if (!popup.matches(':popover-open')) openPopup();
                        optionButtons[Math.max(0, variants.findIndex((option) => option.value === select.value))]?.focus();
                    }
                });
                popup.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') { popup.hidePopover(); trigger.focus(); return; }
                    if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') return;
                    event.preventDefault();
                    const index = optionButtons.indexOf(document.activeElement);
                    const next = index + (event.key === 'ArrowDown' ? 1 : -1);
                    optionButtons[(next + optionButtons.length) % optionButtons.length]?.focus();
                });
                // A required native select is visually minimized: return focus
                // to its visible control if browser form validation fails.
                select.addEventListener('invalid', (event) => {
                    event.preventDefault();
                    trigger.focus();
                    if (!popup.matches(':popover-open')) openPopup();
                });
            });
        }

        applyFilter();
    };



    if (window.__SARI_SELLER_AFTER_PAINT__) {

        window.__SARI_SELLER_AFTER_PAINT__(bootInventoryPage);

    } else {

        window.requestAnimationFrame(() => window.requestAnimationFrame(bootInventoryPage));

    }

})();

</script>

@endpush
