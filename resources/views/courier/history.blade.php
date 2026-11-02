@extends('layouts.courier')

@section('title', 'Delivery History')
@section('header-title', 'Delivery History')
@section('header-subtitle', 'Completed Deliveries')

@push('styles')
<style>
@verbatim
body:has(#sariDeliveryHistory),
body:has(#sariDeliveryHistory) #courierMain {
    background: #f4f5f7 !important;
    color: #2f3742;
}

body:has(#sariDeliveryHistory) #courierSidebar {
    background: #fff !important;
    border-right-color: #e5e7eb !important;
}

body:has(#sariDeliveryHistory) #courierMain > header {
    background: rgba(255,255,255,.97) !important;
    border-bottom-color: #e5e7eb !important;
    box-shadow: none !important;
}

#courierMain > main:has(#sariDeliveryHistory) {
    background: transparent !important;
    padding: 22px clamp(14px,2.2vw,30px) 42px !important;
}

#sariDeliveryHistory {
    --dh-gold: #c88912;
    --dh-gold-hover: #b47b0e;
    --dh-ink: #2f3742;
    --dh-muted: #7a8593;
    --dh-border: #e5e7eb;
    --dh-hover: #f6f7f9;
    --dh-shadow: 0 2px 5px rgba(15,23,42,.045),
                 0 10px 24px rgba(15,23,42,.075),
                 0 24px 54px rgba(15,23,42,.095),
                 inset 0 1px 0 rgba(255,255,255,.92);
    display: grid;
    gap: 20px;
    width: 100%;
    max-width: 1520px;
    margin: 0 auto;
    color: var(--dh-ink);
    font: 400 13px/1.65 'Poppins', ui-sans-serif, system-ui, sans-serif;
}

#sariDeliveryHistory,
#sariDeliveryHistory *,
#sariDeliveryHistory *::before,
#sariDeliveryHistory *::after { box-sizing: border-box; }

#sariDeliveryHistory :is(button,input,select) { font: inherit; }
#sariDeliveryHistory a { text-decoration: none; }
#sariDeliveryHistory :is(a,button) { touch-action: manipulation; }
#sariDeliveryHistory :is(a,button,input,select):focus-visible {
    outline: 2px solid var(--dh-gold);
    outline-offset: 3px;
}

#sariDeliveryHistory svg {
    display: block;
    flex: none;
    width: 20px;
    height: 20px;
    stroke-width: 1.8;
}

#sariDeliveryHistory .dh-heading {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px 24px;
    padding-bottom: 2px;
}

#sariDeliveryHistory .dh-kicker {
    margin: 0 0 6px;
    color: var(--dh-gold);
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: .14em;
    line-height: 1.4;
    text-transform: uppercase;
}

#courierMain #sariDeliveryHistory .dh-title {
    margin: 0;
    color: var(--dh-ink);
    font-size: clamp(28px,2.35vw,33px);
    font-weight: 760;
    letter-spacing: -.04em;
    line-height: 1.05;
}

#sariDeliveryHistory .dh-title-accent { color: var(--dh-gold); }

#sariDeliveryHistory .dh-export {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    height: 48px;
    min-width: 122px;
    padding: 0 18px;
    border: 1px solid var(--dh-gold);
    border-radius: 14px;
    background: var(--dh-gold);
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    transition: background-color .16s ease, border-color .16s ease;
}

#sariDeliveryHistory .dh-export:hover {
    background: var(--dh-gold-hover);
    border-color: var(--dh-gold-hover);
    color: #fff;
}

#sariDeliveryHistory .dh-export svg { width: 16px; height: 16px; }

#sariDeliveryHistory .dh-export-selected {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    height: 48px;
    padding: 0 16px;
    border: 1px solid #dce2e8;
    border-radius: 14px;
    background: #fff;
    color: #475569;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
    cursor: pointer;
    transition: background-color .16s ease, border-color .16s ease;
}
#sariDeliveryHistory .dh-export-selected:hover { background: #f3f4f6; border-color: #cbd5e1; }
#sariDeliveryHistory .dh-export-selected svg { width: 16px; height: 16px; }

#sariDeliveryHistory .dh-card {
    min-width: 0;
    border: 1px solid var(--dh-border);
    border-radius: 18px;
    background: #fff;
    box-shadow: var(--dh-shadow);
}

#sariDeliveryHistory .dh-stats {
    display: grid;
    grid-template-columns: repeat(4,minmax(0,1fr));
    gap: 14px;
}

#sariDeliveryHistory .dh-stat {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 5px;
    min-height: 110px;
    padding: 14px 16px;
}

#sariDeliveryHistory .dh-stat-heading {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
}

#sariDeliveryHistory .dh-stat-label {
    margin: 0;
    color: #5f6d7c;
    font-size: 15px;
    font-weight: 600;
}

#sariDeliveryHistory .dh-stat-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: none;
    line-height: 1;
}

#sariDeliveryHistory .dh-stat-icon svg { width: 27px; height: 27px; }
#sariDeliveryHistory .dh-stat-icon--gold { color: #c88912; }
#sariDeliveryHistory .dh-stat-icon--blue { color: #3865c7; }
#sariDeliveryHistory .dh-stat-icon--green { color: #0f8a6b; }
#sariDeliveryHistory .dh-stat-icon--red { color: #cc4b37; }

#sariDeliveryHistory .dh-stat-value {
    margin: 4px 0 0;
    color: #1f2937;
    font-size: clamp(25px,1.9vw,30px);
    font-weight: 650;
    letter-spacing: -.045em;
    line-height: 1.12;
    font-variant-numeric: tabular-nums;
    overflow-wrap: anywhere;
}

#sariDeliveryHistory .dh-stat-sub {
    margin: auto 0 0;
    color: #8995a3;
    font-size: 12.5px;
    font-weight: 400;
}

#sariDeliveryHistory .dh-toolbar {
    display: grid;
    grid-template-columns: minmax(0,1fr) minmax(190px,235px) auto;
    gap: 12px;
    align-items: center;
    padding: 0;
}

#sariDeliveryHistory .dh-toolbar-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
}

#sariDeliveryHistory .dh-search-wrap {
    position: relative;
    min-width: 0;
}

#sariDeliveryHistory .dh-search-icon {
    position: absolute;
    top: 50%;
    left: 16px;
    transform: translateY(-50%);
    color: #97a3b2;
    pointer-events: none;
}

#sariDeliveryHistory .dh-search-icon svg { width: 21px; height: 21px; }

#sariDeliveryHistory .dh-search,
#sariDeliveryHistory .dh-filter,
#sariDeliveryHistory .dh-filter-trigger {
    height: 48px;
    min-width: 0;
    border: 1px solid #dce2e8;
    border-radius: 14px;
    background: #fff;
    color: #3f4b59;
    font-size: 11.5px;
    font-weight: 600;
    transition: background-color .16s ease, border-color .16s ease, box-shadow .16s ease;
}

#sariDeliveryHistory .dh-search {
    display: block;
    width: 100%;
    padding: 0 16px 0 46px;
    outline: none;
}

#sariDeliveryHistory .dh-search::placeholder { color: #a0aab6; opacity: 1; }

/* Native select stays usable if JavaScript is unavailable. */
#sariDeliveryHistory .dh-filter-wrap {
    position: relative;
    min-width: 0;
}

#sariDeliveryHistory .dh-filter-wrap::after {
    content: '';
    position: absolute;
    right: 18px;
    top: 22px;
    width: 8px;
    height: 8px;
    border-right: 1.8px solid #6b7280;
    border-bottom: 1.8px solid #6b7280;
    transform: rotate(45deg);
    pointer-events: none;
    transition: transform .15s ease;
}

#sariDeliveryHistory .dh-filter-wrap:has(.dh-filter-trigger[aria-expanded="true"])::after {
    transform: translateY(4px) rotate(225deg);
}

#sariDeliveryHistory .dh-filter,
#sariDeliveryHistory .dh-filter-trigger {
    display: block;
    width: 100%;
    padding: 0 44px 0 16px;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    text-align: left;
    white-space: nowrap;
}

#sariDeliveryHistory .dh-filter option { background: #fff; color: #374151; }

#sariDeliveryHistory .dh-search:hover,
#sariDeliveryHistory .dh-filter:hover,
#sariDeliveryHistory .dh-filter-trigger:hover {
    background: #f3f4f6;
    border-color: #cfd6de;
}

/* Thin SARI gold focus border for both search and filter, not a thick glow. */
#sariDeliveryHistory .dh-search:focus,
#sariDeliveryHistory .dh-filter:focus,
#sariDeliveryHistory .dh-filter-trigger:focus-visible,
#sariDeliveryHistory .dh-filter-trigger[aria-expanded="true"] {
    outline: 0;
    border-color: var(--dh-gold);
    background: #fff;
    box-shadow: 0 0 0 1px rgba(200,137,18,.12);
}

/* Custom listbox: removes the browser-dependent blue option highlight. */
#sariDeliveryHistory .dh-filter-menu {
    position: absolute;
    z-index: 30;
    top: calc(100% + 7px);
    left: 0;
    right: 0;
    padding: 6px;
    border: 1px solid #e5e7eb;
    border-radius: 13px;
    background: #fff;
    box-shadow: 0 12px 30px rgba(15,23,42,.12), 0 2px 6px rgba(15,23,42,.04);
}

#sariDeliveryHistory .dh-filter-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    min-height: 39px;
    padding: 9px 12px;
    border: none;
    border-radius: 8px;
    background: #fff;
    color: #475569;
    text-align: left;
    font-size: 12px;
    font-weight: 550;
    cursor: pointer;
    transition: background-color .14s ease;
}

#sariDeliveryHistory .dh-filter-option:hover,
#sariDeliveryHistory .dh-filter-option:focus-visible {
    outline: none;
    background: #f1f3f5;
    color: #334155;
}

#sariDeliveryHistory .dh-filter-option[aria-selected="true"] { font-weight: 700; }
#sariDeliveryHistory .dh-filter-option[aria-selected="true"]::after {
    content: '✓';
    color: var(--dh-gold);
    font-size: 13px;
}

#sariDeliveryHistory .dh-records { overflow: hidden; }

#sariDeliveryHistory .dh-records-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    padding: 21px 23px;
    border-bottom: 1px solid #e9edf1;
}

#courierMain #sariDeliveryHistory .dh-section-title {
    margin: 0;
    color: var(--dh-ink);
    font-size: 18px;
    font-weight: 780;
    letter-spacing: -.03em;
    line-height: 1.4;
}

#sariDeliveryHistory .dh-section-sub {
    margin: 4px 0 0;
    color: #8b96a4;
    font-size: 11.5px;
}

#sariDeliveryHistory .dh-showing {
    margin-left: auto;
    color: #7b8794;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

#sariDeliveryHistory .dh-table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
#sariDeliveryHistory .dh-table { width: 100%; min-width: 970px; border-collapse: collapse; text-align: left; }

#sariDeliveryHistory .dh-table thead { background: #f8fafc; }
#sariDeliveryHistory .dh-table thead th {
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

#sariDeliveryHistory .dh-table :is(thead th,tbody td).dh-check-cell {
    width: 54px;
    min-width: 54px;
    padding: 12px 10px 12px 23px;
    text-align: center;
    white-space: nowrap;
}

#sariDeliveryHistory .dh-checkbox {
    appearance: none;
    -webkit-appearance: none;
    display: inline-grid;
    place-items: center;
    vertical-align: middle;
    width: 17px;
    height: 17px;
    margin: 0;
    border: 1.5px solid #c6cbd2;
    border-radius: 5px;
    background: #fff;
    cursor: pointer;
    transition: border-color .15s ease, background-color .15s ease;
}

#sariDeliveryHistory .dh-checkbox::after {
    content: '';
    display: block;
    width: 8px;
    height: 5px;
    margin-top: -2px;
    border-left: 2px solid #fff;
    border-bottom: 2px solid #fff;
    transform: rotate(-45deg) scale(0);
    transition: transform .15s ease;
}

#sariDeliveryHistory .dh-checkbox:checked,
#sariDeliveryHistory .dh-checkbox:indeterminate {
    background: var(--dh-gold);
    border-color: var(--dh-gold);
}

#sariDeliveryHistory .dh-checkbox:checked::after {
    transform: rotate(-45deg) scale(1);
}

#sariDeliveryHistory .dh-checkbox:indeterminate::after {
    width: 8px;
    height: 0;
    margin-top: 0;
    border-left: 0;
    border-bottom: 2px solid #fff;
    transform: none;
}

#sariDeliveryHistory .dh-checkbox:hover { border-color: #929da9; }
#sariDeliveryHistory .dh-checkbox:focus-visible {
    outline: 2px solid #c88912;
    outline-offset: 3px;
}

#sariDeliveryHistory .dh-table :is(thead th,tbody td):nth-child(2) { padding-left: 12px; }
#sariDeliveryHistory .dh-table thead th:last-child,
#sariDeliveryHistory .dh-table tbody td:last-child { padding-right: 23px; }

#sariDeliveryHistory .dh-row {
    background: #fff;
    transition: background-color .16s ease;
}

#sariDeliveryHistory .dh-row:hover { background: var(--dh-hover); }
#sariDeliveryHistory .dh-row:has(.dh-checkbox:checked) { background: #fafbfc; }
#sariDeliveryHistory .dh-row:has(.dh-checkbox:checked):hover { background: #f1f3f5; }
#sariDeliveryHistory .dh-row + .dh-row { border-top: 1px solid #edf0f3; }
#sariDeliveryHistory .dh-row[hidden] { display: none !important; }

#sariDeliveryHistory .dh-row td {
    padding: 17px 16px;
    color: #667383;
    font-size: 12.5px;
    line-height: 1.6;
    vertical-align: middle;
}

#sariDeliveryHistory .dh-row .dh-order-number,
#sariDeliveryHistory .dh-row .dh-fee {
    color: #293440;
    font-weight: 750;
}

#sariDeliveryHistory .dh-row .dh-fee {
    white-space: nowrap;
    font-variant-numeric: tabular-nums;
}

#sariDeliveryHistory .dh-row .dh-route { min-width: 165px; }
#sariDeliveryHistory .dh-row .dh-date,
#sariDeliveryHistory .dh-row .dh-time { white-space: nowrap; }

#sariDeliveryHistory .dh-status {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
    padding: 5px 11px;
    border: 1px solid #d8dee6;
    border-radius: 999px;
    background: #f8fafc;
    color: #5f6d7c;
    font-size: 11px;
    font-weight: 700;
}

#sariDeliveryHistory .dh-status::before {
    content: '';
    display: block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #94a3b8;
}

#sariDeliveryHistory .dh-status--completed {
    border-color: #bde7c9;
    background: #effcf3;
    color: #18794e;
}

#sariDeliveryHistory .dh-status--completed::before {
    background: #22c55e;
}

#sariDeliveryHistory .dh-empty {
    padding: 42px 20px;
    text-align: center;
    color: #8b96a4;
}

/* No icon container: a larger neutral-gray outline icon on the white surface. */
#sariDeliveryHistory svg.dh-empty-icon {
    display: block;
    width: 37px;
    height: 37px;
    margin: 0 auto 14px;
    color: #9ca3af;
    stroke-width: 1.6;
}
#sariDeliveryHistory #historyNoResults svg.dh-empty-icon { width: 43px; height: 43px; }
#sariDeliveryHistory .dh-empty strong { display: block; color: #536071; font-size: 13px; }
#sariDeliveryHistory .dh-empty p { margin: 5px 0 0; font-size: 11.5px; }
#sariDeliveryHistory [hidden] { display: none !important; }

@media (max-width: 1180px) {
    #sariDeliveryHistory .dh-stats { grid-template-columns: repeat(2,minmax(0,1fr)); }
    #sariDeliveryHistory .dh-toolbar { grid-template-columns: 1fr 220px auto; }
}

@media (max-width: 900px) {
    #sariDeliveryHistory .dh-toolbar { grid-template-columns: 1fr; }
}

@media (max-width: 767px) {
    #courierMain > main:has(#sariDeliveryHistory) { padding: 16px 14px 28px !important; }
    #sariDeliveryHistory { gap: 15px; }
    #courierMain #sariDeliveryHistory .dh-title { font-size: 25px; }
    #sariDeliveryHistory .dh-kicker { font-size: 9.5px; }
    #sariDeliveryHistory .dh-card {
        box-shadow: 0 1px 3px rgba(15,23,42,.035),
                    0 6px 16px rgba(15,23,42,.055),
                    0 13px 28px rgba(15,23,42,.065),
                    inset 0 1px 0 rgba(255,255,255,.90);
    }
    #sariDeliveryHistory .dh-stat { min-height: 106px; padding: 13px 14px; }
    #sariDeliveryHistory .dh-stat-value { font-size: 27px; }
    #sariDeliveryHistory .dh-records-head { padding: 18px; }
}

@media (max-width: 540px) {
    #sariDeliveryHistory .dh-stats { grid-template-columns: 1fr; gap: 11px; }
    #sariDeliveryHistory .dh-stat-label { font-size: 14px; }
    #sariDeliveryHistory .dh-stat-icon svg { width: 24px; height: 24px; }
    #sariDeliveryHistory .dh-stat-value { font-size: 25px; }
    #sariDeliveryHistory .dh-toolbar-actions { width: 100%; }
    #sariDeliveryHistory .dh-toolbar-actions :is(.dh-export,.dh-export-selected) { flex: 1 1 auto; }
}


/* Compact pagination inside the Completed Trips card. */
#sariDeliveryHistory .dh-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px 18px;
    padding: 13px 20px;
    border-top: 1px solid #edf0f4;
    background: #fff;
}
#sariDeliveryHistory .dh-pagination-count {
    margin: 0;
    color: #8a96a6;
    font-size: 11px;
    font-weight: 550;
    line-height: 1.5;
}
#sariDeliveryHistory .dh-pagination-controls {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 5px;
    margin-left: auto;
}
#sariDeliveryHistory .dh-page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: none;
    min-width: 34px;
    height: 34px;
    padding: 0 9px;
    border: 1px solid transparent;
    border-radius: 10px;
    background: #f7f8fa;
    color: #8795a6;
    font-size: 11.5px;
    font-weight: 700;
    line-height: 1;
    cursor: pointer;
    transition: background-color .15s ease, border-color .15s ease, color .15s ease;
}
#sariDeliveryHistory .dh-page-btn:hover:not(:disabled):not([aria-current="page"]) {
    background: #edf0f3;
    border-color: #e6eaef;
    color: #4b596a;
}
#sariDeliveryHistory .dh-page-btn[aria-current="page"] {
    background: var(--dh-gold);
    border-color: var(--dh-gold);
    color: #fff;
    box-shadow: 0 3px 9px rgba(200,137,18,.17);
    cursor: default;
}
#sariDeliveryHistory .dh-page-btn:disabled { opacity: .45; cursor: not-allowed; }
#sariDeliveryHistory .dh-page-btn svg { width: 14px; height: 14px; stroke-width: 2; }
#sariDeliveryHistory .dh-page-ellipsis {
    display: inline-flex;
    min-width: 22px;
    height: 34px;
    align-items: center;
    justify-content: center;
    color: #9ba5b1;
    font-size: 11px;
    user-select: none;
}
@media (max-width: 540px) {
    #sariDeliveryHistory .dh-pagination { padding: 12px 14px; justify-content: center; gap: 10px; }
    #sariDeliveryHistory .dh-pagination-count { width: 100%; text-align: center; }
    #sariDeliveryHistory .dh-pagination-controls { margin: 0; max-width: 100%; gap: 3px; }
    #sariDeliveryHistory .dh-page-btn { min-width: 31px; height: 32px; padding: 0 7px; border-radius: 9px; font-size: 11px; }
    #sariDeliveryHistory .dh-page-ellipsis { min-width: 16px; }
}

@media (prefers-reduced-motion: reduce) {
    #sariDeliveryHistory * { transition: none !important; scroll-behavior: auto !important; }
}
@endverbatim
</style>
@endpush

@section('content')
<div id="sariDeliveryHistory" data-ui-version="history-heading-pagination-v1">
    <header class="dh-heading">
        <div>
            <p class="dh-kicker">SARI Marketplace</p>
            <h1 class="dh-title">Delivery <span class="dh-title-accent">History</span></h1>
        </div>

    </header>

    <section class="dh-stats" aria-label="Delivery performance summary">
        @foreach([
            ['Completed Today', $historyStats['today'], 'Successful trips', 'dh-stat-icon--gold'],
            ['Total Completed', $historyStats['total'], 'Since joining', 'dh-stat-icon--blue'],
            ['Success Rate', number_format($historyStats['success_rate'], 1).'%', 'Completion rate', 'dh-stat-icon--green'],
            ['Average Fee', '₱'.number_format($historyStats['average_fee'], 2), 'Per completed delivery', 'dh-stat-icon--red'],
        ] as [$label, $value, $sub, $iconClass])
            <article class="dh-card dh-stat">
                <div class="dh-stat-heading">
                    <p class="dh-stat-label">{{ $label }}</p>
                    <span class="dh-stat-icon {{ $iconClass }}" aria-hidden="true">
                        @switch($loop->index)
                            @case(0)
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3v3M17 3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/><path d="m9 15 2 2 4-4"/></svg>
                                @break
                            @case(1)
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
                                @break
                            @case(2)
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.2 2.2 4.8-5.2"/></svg>
                                @break
                            @default
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="15" rx="2"/><path d="M3 10h18M7 15h3"/></svg>
                        @endswitch
                    </span>
                </div>
                <p class="dh-stat-value">{{ $value }}</p>
                <p class="dh-stat-sub">{{ $sub }}</p>
            </article>
        @endforeach
    </section>

    <section class="dh-toolbar" aria-label="Trip history filters">
        <div class="dh-search-wrap">
            <span class="dh-search-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            </span>
            <input id="historySearch" type="search" class="dh-search" placeholder="Search order, customer, route..." aria-label="Search completed trips">
        </div>

        <div class="dh-filter-wrap" id="historyFilterWrap">
            <select id="historyFilter" class="dh-filter" aria-label="Filter delivery records by date">
                <option value="all">All deliveries</option>
                <option value="today">Today</option>
                <option value="previous">Previous days</option>
            </select>
            <button id="historyFilterButton" type="button" class="dh-filter-trigger" aria-label="Filter delivery records by date" aria-haspopup="listbox" aria-expanded="false" aria-controls="historyFilterMenu" hidden>
                <span id="historyFilterLabel">All deliveries</span>
            </button>
            <div id="historyFilterMenu" class="dh-filter-menu" role="listbox" aria-label="Filter delivery records by date" hidden>
                <button type="button" role="option" class="dh-filter-option" data-filter-value="all" aria-selected="true">All deliveries</button>
                <button type="button" role="option" class="dh-filter-option" data-filter-value="today" aria-selected="false">Today</button>
                <button type="button" role="option" class="dh-filter-option" data-filter-value="previous" aria-selected="false">Previous days</button>
            </div>
        </div>

        <div class="dh-toolbar-actions">
            <button type="button" id="historyExportSelected" class="dh-export-selected" hidden disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m-4-4 4 4 4-4M5 17v3h14v-3"/></svg>
                <span id="historyExportSelectedLabel">Export Selected</span>
            </button>
            <a href="{{ route('courier.history.export') }}" class="dh-export">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 3v12m-4-4 4 4 4-4M5 17v3h14v-3"/>
                </svg>
                Export All
            </a>
        </div>
    </section>

    <section class="dh-card dh-records" aria-labelledby="completedTripsHeading">
        <div class="dh-records-head">
            <div>
                <h2 id="completedTripsHeading" class="dh-section-title">Completed Trips</h2>
                <p class="dh-section-sub">Most recent delivery records.</p>
            </div>
            <span id="historyShowingCount" class="dh-showing" role="status" aria-live="polite">Showing {{ count($history) }}</span>
        </div>

        <div class="dh-table-scroll" role="region" aria-label="Completed delivery records" tabindex="0">
            <table class="dh-table">
                <thead>
                    <tr>
                        <th scope="col" class="dh-check-cell">
                            <input id="historySelectAll" type="checkbox" class="dh-checkbox" aria-label="Select all visible deliveries" title="Select all visible deliveries">
                        </th>
                        <th scope="col">Order</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Route</th>
                        <th scope="col">Date</th>
                        <th scope="col">Time</th>
                        <th scope="col">Fee</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody id="historyRows">
                    @forelse($history as $row)
                        <tr
                            class="dh-row history-row"
                            data-search="{{ strtolower($row['order'].' '.$row['customer'].' '.$row['route']) }}"
                            data-day="{{ $row['is_today'] ? 'today' : 'previous' }}"
                        >
                            <td class="dh-check-cell"><input type="checkbox" class="dh-checkbox dh-row-checkbox" aria-label="Select delivery {{ $row['order'] }}"></td>
                            <td class="dh-order-number">{{ $row['order'] }}</td>
                            <td>{{ $row['customer'] }}</td>
                            <td class="dh-route">{{ $row['route'] }}</td>
                            <td class="dh-date">{{ $row['date'] }}</td>
                            <td class="dh-time">{{ $row['time'] }}</td>
                            <td class="dh-fee">₱{{ number_format($row['fee'], 2) }}</td>
                            <td><span class="dh-status {{ strtolower((string) $row['status']) === 'completed' ? 'dh-status--completed' : '' }}">{{ $row['status'] }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="dh-empty">
                                    <svg class="dh-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
                                    <strong>No completed deliveries yet</strong>
                                    <p>Your completed trips will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    @if(!empty($history))
                        <tr id="historyNoResults" hidden>
                            <td colspan="8">
                                <div class="dh-empty" role="status">
                                    <svg class="dh-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                                    <strong>No matching deliveries</strong>
                                    <p>Try another search or date filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <nav id="historyPagination" class="dh-pagination" aria-label="Completed trips pagination" hidden>
            <p id="historyPaginationCount" class="dh-pagination-count" aria-live="polite"></p>
            <div id="historyPaginationControls" class="dh-pagination-controls" aria-label="Pages"></div>
        </nav>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('sariDeliveryHistory');
    if (!root) return;

    const search = root.querySelector('#historySearch');
    const filter = root.querySelector('#historyFilter');
    const filterWrap = root.querySelector('#historyFilterWrap');
    const filterTrigger = root.querySelector('#historyFilterButton');
    const filterLabel = root.querySelector('#historyFilterLabel');
    const filterMenu = root.querySelector('#historyFilterMenu');
    const filterOptions = [...root.querySelectorAll('[data-filter-value]')];
    const exportSelected = root.querySelector('#historyExportSelected');
    const exportSelectedLabel = root.querySelector('#historyExportSelectedLabel');
    const rows = [...root.querySelectorAll('.history-row')];
    const emptyResult = root.querySelector('#historyNoResults');
    const showingCount = root.querySelector('#historyShowingCount');
    const selectAll = root.querySelector('#historySelectAll');
    const pagination = root.querySelector('#historyPagination');
    const paginationCount = root.querySelector('#historyPaginationCount');
    const paginationControls = root.querySelector('#historyPaginationControls');
    const pageSize = 10;
    let currentPage = 1;
    const visibleRows = () => rows.filter(row => !row.hidden);

    // Show a short page window (1 2 3 4 … 18) without rendering all page buttons.
    const getPageItems = (page, totalPages) => {
        if (totalPages <= 7) return Array.from({ length: totalPages }, (_, i) => i + 1);
        if (page <= 3) return [1, 2, 3, 4, 'ellipsis', totalPages];
        if (page >= totalPages - 2) return [1, 'ellipsis', totalPages - 3, totalPages - 2, totalPages - 1, totalPages];
        return [1, 'ellipsis', page - 1, page, page + 1, 'ellipsis', totalPages];
    };

    const renderPagination = (totalPages) => {
        if (!pagination || !paginationControls) return;
        pagination.hidden = rows.length === 0;
        paginationControls.replaceChildren();
        if (!rows.length) return;

        const appendArrow = (direction, disabled) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'dh-page-btn';
            button.setAttribute('aria-label', direction === -1 ? 'Previous page' : 'Next page');
            button.disabled = disabled;
            button.innerHTML = direction === -1
                ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>'
                : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>';
            button.addEventListener('click', () => changePage(currentPage + direction));
            paginationControls.appendChild(button);
        };

        appendArrow(-1, currentPage === 1);
        getPageItems(currentPage, totalPages).forEach(page => {
            if (page === 'ellipsis') {
                const spacer = document.createElement('span');
                spacer.className = 'dh-page-ellipsis';
                spacer.textContent = '…';
                spacer.setAttribute('aria-hidden', 'true');
                paginationControls.appendChild(spacer);
                return;
            }
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'dh-page-btn';
            button.textContent = String(page);
            button.setAttribute('aria-label', `Page ${page}`);
            if (page === currentPage) button.setAttribute('aria-current', 'page');
            button.addEventListener('click', () => changePage(page));
            paginationControls.appendChild(button);
        });
        appendArrow(1, currentPage === totalPages);
    };

    const syncSelection = () => {
        const selectedCount = rows.filter(row => row.querySelector('.dh-row-checkbox')?.checked).length;
        if (exportSelected) {
            exportSelected.hidden = selectedCount === 0;
            exportSelected.disabled = selectedCount === 0;
        }
        if (exportSelectedLabel) exportSelectedLabel.textContent = `Export Selected (${selectedCount})`;

        if (!selectAll) return;
        const active = visibleRows();
        const checkedCount = active.filter(row => row.querySelector('.dh-row-checkbox')?.checked).length;
        selectAll.disabled = active.length === 0;
        selectAll.checked = active.length > 0 && checkedCount === active.length;
        selectAll.indeterminate = checkedCount > 0 && checkedCount < active.length;
    };

    const apply = () => {
        const query = (search?.value || '').trim().toLowerCase();
        const selected = filter?.value || 'all';
        const matches = rows.filter(row => {
            const searchMatch = (row.dataset.search || '').includes(query);
            const dayMatch = selected === 'all' || row.dataset.day === selected;
            return searchMatch && dayMatch;
        });
        const filteredTotal = matches.length;
        const totalPages = Math.max(1, Math.ceil(filteredTotal / pageSize));
        currentPage = Math.min(Math.max(1, currentPage), totalPages);
        const start = (currentPage - 1) * pageSize;
        const onPage = new Set(matches.slice(start, start + pageSize));
        rows.forEach(row => { row.hidden = !onPage.has(row); });
        const displayed = onPage.size;
        const first = filteredTotal ? start + 1 : 0;
        const last = filteredTotal ? start + displayed : 0;

        if (emptyResult) emptyResult.hidden = filteredTotal !== 0;
        if (showingCount) showingCount.textContent = `Showing ${displayed} of ${filteredTotal}`;
        if (paginationCount) paginationCount.textContent = `Showing ${first}–${last} of ${filteredTotal} deliveries`;
        renderPagination(totalPages);
        syncSelection();
    };

    const changePage = page => {
        if (!Number.isInteger(page) || page === currentPage) return;
        currentPage = page;
        apply();
        // Keep the user near the table when changing pages, without disrupting filters.
        root.querySelector('#completedTripsHeading')?.scrollIntoView({ block: 'nearest', behavior: 'instant' });
    };

    // Enhances the native filter with a consistent, accessible gray-hover dropdown.
    // The hidden select remains the source of truth for the existing filter logic.
    if (filter && filterTrigger && filterMenu && filterWrap && filterOptions.length) {
        filter.hidden = true;
        filterTrigger.hidden = false;

        const syncFilterUI = () => {
            const option = filter.options[filter.selectedIndex];
            if (filterLabel) filterLabel.textContent = option?.textContent || 'All deliveries';
            filterOptions.forEach(node => {
                node.setAttribute('aria-selected', String(node.dataset.filterValue === filter.value));
            });
        };
        const closeFilter = (restoreFocus = false) => {
            filterMenu.hidden = true;
            filterTrigger.setAttribute('aria-expanded', 'false');
            if (restoreFocus) filterTrigger.focus();
        };
        const openFilter = () => {
            filterMenu.hidden = false;
            filterTrigger.setAttribute('aria-expanded', 'true');
            (filterOptions.find(node => node.dataset.filterValue === filter.value) || filterOptions[0]).focus();
        };
        const chooseFilter = value => {
            filter.value = value;
            filter.dispatchEvent(new Event('change', { bubbles: true }));
            syncFilterUI();
            closeFilter(true);
        };

        filterTrigger.addEventListener('click', () => {
            if (filterMenu.hidden) openFilter();
            else closeFilter(true);
        });
        filterTrigger.addEventListener('keydown', event => {
            if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(event.key)) {
                event.preventDefault();
                openFilter();
            }
        });
        filterMenu.addEventListener('keydown', event => {
            const index = filterOptions.indexOf(document.activeElement);
            let next = index;
            if (event.key === 'ArrowDown') next = Math.min(index + 1, filterOptions.length - 1);
            else if (event.key === 'ArrowUp') next = Math.max(index - 1, 0);
            else if (event.key === 'Home') next = 0;
            else if (event.key === 'End') next = filterOptions.length - 1;
            else if (event.key === 'Escape') { event.preventDefault(); closeFilter(true); return; }
            else if (event.key === 'Tab') { closeFilter(); return; }
            else return;
            event.preventDefault();
            filterOptions[next >= 0 ? next : 0].focus();
        });
        filterOptions.forEach(option => {
            option.addEventListener('click', () => chooseFilter(option.dataset.filterValue));
        });
        document.addEventListener('pointerdown', event => {
            if (!filterWrap.contains(event.target)) closeFilter();
        });
        filter.addEventListener('change', syncFilterUI);
        syncFilterUI();
    }

    // Selection never changes the server's existing Export All route.
    selectAll?.addEventListener('change', () => {
        visibleRows().forEach(row => {
            const checkbox = row.querySelector('.dh-row-checkbox');
            if (checkbox) checkbox.checked = selectAll.checked;
        });
        syncSelection();
    });
    rows.forEach(row => {
        row.querySelector('.dh-row-checkbox')?.addEventListener('change', syncSelection);
    });

    // Download only checked rows, including those hidden by current search/filter.
    // CSV is built from the already rendered delivery records; no server mutation.
    exportSelected?.addEventListener('click', () => {
        const selectedRows = rows.filter(row => row.querySelector('.dh-row-checkbox')?.checked);
        if (!selectedRows.length) return;

        const headers = ['Order', 'Customer', 'Route', 'Date', 'Time', 'Fee', 'Status'];
        const csvSafe = cell => {
            let value = String(cell ?? '').trim();
            // Prevent spreadsheet programs from interpreting user-supplied text as formulas.
            if (/^\s*[=+\-@]/.test(value)) value = "'" + value;
            return '"' + value.replace(/"/g, '""') + '"';
        };
        const data = selectedRows.map(row => {
            const cells = [...row.querySelectorAll('td')].slice(1);
            return cells.map(cell => cell.textContent.trim());
        });
        const csv = '\uFEFF' + [headers, ...data].map(line => line.map(csvSafe).join(',')).join('\r\n');
        const file = new Blob([csv], { type: 'text/csv;charset=utf-8' });
        const url = URL.createObjectURL(file);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'sari-delivery-history-selected.csv';
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    });

    search?.addEventListener('input', () => { currentPage = 1; apply(); });
    filter?.addEventListener('change', () => { currentPage = 1; apply(); });
    apply();
});
</script>
@endpush
