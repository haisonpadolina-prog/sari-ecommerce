@extends('layouts.seller')

@section('title', 'Inventory — SARI Seller')
@section('page-title', 'Inventory')

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

<style>
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
</style>


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
            <p class="inventory-subtitle">
                Monitor product stock, update quantities, and manage inventory across your catalog.
                Track product images, variants, and adjustment history all in one place.
            </p>
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

            <div class="inventory-table-search-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
                <input id="inventoryTableSearch" class="inventory-table-search" type="search" autocomplete="off" placeholder="Search products...">
            </div>
        </div>

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

                        <div>
                            @if($product->activeVariants->isNotEmpty())
                                <select name="variant_id" required class="inventory-select" data-inventory-variant-select>
                                    <option value="" data-stock="">Select variant</option>
                                    @foreach($product->activeVariants as $variant)
                                        <option
                                            value="{{ $variant->id }}"
                                            data-stock="{{ (int) $variant->stock }}"
                                        >
                                            {{ $variant->sku ?: '#'.$variant->id }} · stock {{ $variant->stock }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="variant_id" value="">
                                <select class="inventory-select" disabled>
                                    <option>No variants</option>
                                </select>
                            @endif
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
                <div class="inventory-empty">No active products found in inventory.</div>
            @endforelse
        </div>

        <div id="inventoryNoSearchResults" class="inventory-empty inventory-hidden">
            No matching products found.
        </div>

        @if($products->hasPages())
            <div class="border-t border-[#e7ebf0] p-4">{{ $products->links() }}</div>
        @endif
    </section>

    <section class="inventory-activity-panel">
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
                            <div class="inventory-activity-name">Product #{{ $movement->seller_product_id }} · {{ str_replace('_', ' ', ucfirst($movement->reason)) }}</div>
                            <div class="inventory-activity-meta">{{ optional($movement->created_at)->format('M d, Y · h:i A') ?: 'Recent update' }}</div>
                        </div>
                        <div class="inventory-activity-value">{{ $movement->quantity_before }} → {{ $movement->quantity_after }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection


@push('scripts')
<script>
(() => {
    const bootInventoryPage = () => {
        const tableSearch = document.getElementById('inventoryTableSearch');
        const rows = Array.from(document.querySelectorAll('.inventory-searchable-row'));
        const emptyState = document.getElementById('inventoryNoSearchResults');

        const applyFilter = (value = '') => {
            if (!emptyState) return;

            const keyword = String(value || '').trim().toLowerCase();
            let visible = 0;

            rows.forEach((row) => {
                const haystack = row.getAttribute('data-search') || '';
                const matched = !keyword || haystack.includes(keyword);
                row.classList.toggle('inventory-hidden', !matched);
                if (matched) visible += 1;
            });

            emptyState.classList.toggle('inventory-hidden', visible !== 0);
        };

        tableSearch?.addEventListener('input', () => {
            applyFilter(tableSearch.value);
        }, { passive: true });

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
    };

    if (window.__SARI_SELLER_AFTER_PAINT__) {
        window.__SARI_SELLER_AFTER_PAINT__(bootInventoryPage);
    } else {
        window.requestAnimationFrame(() => window.requestAnimationFrame(bootInventoryPage));
    }
})();
</script>
@endpush
