@extends('layouts.admin')

@section('title','Reports — SARI Admin')
@section('page-title','Platform Reports')

@section('content')
@php
    $summaryConfig = [
        ['key'=>'orders','label'=>'Total Orders','helper'=>'Orders created in period','tone'=>'blue','icon'=>'orders','inverse'=>false],
        ['key'=>'delivered','label'=>'Delivered','helper'=>'Completed orders','tone'=>'green','icon'=>'delivered','inverse'=>false],
        ['key'=>'gmv','label'=>'GMV','helper'=>'Delivered order value','tone'=>'violet','icon'=>'gmv','inverse'=>false],
        ['key'=>'delivery_fees','label'=>'Delivery Fees','helper'=>'Delivered order fees','tone'=>'orange','icon'=>'fees','inverse'=>false],
        ['key'=>'active','label'=>'Active Orders','helper'=>'Open order workflow','tone'=>'teal','icon'=>'active','inverse'=>false],
        ['key'=>'cancelled','label'=>'Cancelled','helper'=>'Cancelled orders','tone'=>'red','icon'=>'cancelled','inverse'=>true],
        ['key'=>'registrations','label'=>'Registrations','helper'=>'Applications submitted','tone'=>'purple','icon'=>'registrations','inverse'=>false],
        ['key'=>'pending_registrations','label'=>'Pending Review','helper'=>'Applications awaiting review','tone'=>'amber','icon'=>'pending','inverse'=>true],
    ];

    $currencyKeys = ['gmv','delivery_fees'];
    $statusTotal = max(1, (int) collect($statusBreakdown)->sum());

    $statusColors = [
        'delivered' => '#d59a1f',
        'in_transit' => '#58756f',
        'ready_for_pickup' => '#8a7186',
        'courier_accepted' => '#b07b32',
        'heading_pickup' => '#77845f',
        'arrived_pickup' => '#6f8191',
        'arrived_buyer' => '#638069',
        'preparing' => '#826f65',
        'new' => '#9a9185',
        'cancelled' => '#b86658',
    ];

    $statusCursor = 0;
    $statusSegments = [];
    foreach ($statusBreakdown as $status => $count) {
        $share = ((int) $count / $statusTotal);
        $start = $statusCursor * 360;
        $statusCursor += $share;
        $end = $statusCursor * 360;
        $color = $statusColors[$status] ?? '#cbd5e1';
        $statusSegments[] = "{$color} {$start}deg {$end}deg";
    }
    $statusGradient = count($statusSegments)
        ? 'conic-gradient('.implode(', ', $statusSegments).')'
        : 'conic-gradient(#e2e8f0 0deg 360deg)';

    $exportUrl = route('admin.reports.export', [
        'from' => $period['from_date'],
        'to' => $period['to_date'],
    ]);
@endphp

<style>
    /* ============================================================
       SARI ADMIN — PLATFORM REPORTS
       Enterprise compact UI + performance-focused rendering
       ============================================================ */

    .reports-page {
        --rp-brand:#d99500;
        --rp-brand-strong:#bd8205;
        --rp-brand-soft:#fff7e8;
        --rp-ink:var(--sari-dark-e23e64dd1e, #26211c);
        --rp-text:var(--sari-dark-e23e64dd1e, #514a42);
        --rp-muted:var(--sari-dark-a63aa16d7c, #8d8479);
        --rp-subtle:#9b9288;
        --rp-line:var(--sari-dark-d8c2716225, #e8e1d8);
        --rp-soft:#faf9f6;
        --rp-green:#5c7d63;
        --rp-red:#ad6255;
        --rp-plum:#806f7f;
        --rp-teal:#607a72;

        width:100%;
        max-width:1640px;
        margin:0 auto;
        padding-bottom:20px;
        color:var(--rp-ink);
        font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
    }

    .reports-page *,
    .reports-page *::before,
    .reports-page *::after {
        box-sizing:border-box;
    }

    .reports-page button,
    .reports-page a,
    .reports-page input,
    .reports-page .rp-calendar-popover,
    .reports-page .rp-cal-select-menu {
        transition:
            color .15s ease,
            background-color .15s ease,
            border-color .15s ease,
            opacity .15s ease,
            transform .15s ease;
    }

    .rp-card {
        border:1px solid var(--rp-line);
        border-radius:14px;
        background:var(--sari-dark-950baec030, #fff);
        box-shadow:0 6px 20px var(--sari-dark-1190f58155, rgba(61,43,22,.045));
    }

    /* ---------------- HEADER ---------------- */
    .rp-header {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:14px;
    }

    .rp-title-wrap {
        display:flex;
        min-width:0;
        align-items:center;
        gap:10px;
    }

    .rp-title-icon {
        display:grid;
        width:36px;
        height:36px;
        flex:0 0 36px;
        place-items:center;
        border:1px solid var(--sari-dark-d8c2716225, #eadfc9);
        border-radius:10px;
        background:var(--sari-dark-4d21eb669b, #fff8eb);
        color:var(--sari-dark-5acaf64852, #b77c18);
        box-shadow:0 4px 12px var(--sari-dark-1190f58155, rgba(75,54,25,.045));
    }

    .rp-title-icon svg {
        width:15px;
        height:15px;
    }

    .rp-eyebrow {
        color:var(--sari-dark-a63aa16d7c, #9a7b43);
        font-size:7px;
        font-weight:700;
        letter-spacing:.13em;
        text-transform:uppercase;
    }

    .rp-title {
        margin:3px 0 0;
        font-size:clamp(22px,1.55vw,27px);
        font-weight:700;
        line-height:1.08;
        letter-spacing:-.035em;
    }

    .rp-title-platform { color:var(--sari-dark-e23e64dd1e, #17130f); }
    .rp-title-gold { color:var(--rp-brand); }

    .rp-subtitle {
        max-width:760px;
        margin-top:5px;
        color:var(--sari-dark-a63aa16d7c, #81786c);
        font-size:9.5px;
        line-height:1.55;
    }

    .rp-actions {
        display:flex;
        flex-wrap:wrap;
        align-items:center;
        justify-content:flex-end;
        gap:8px;
    }

    .rp-filter-form {
        display:flex;
        flex-wrap:wrap;
        align-items:center;
        gap:7px;
    }

    .rp-btn {
        display:inline-flex;
        height:38px;
        align-items:center;
        justify-content:center;
        gap:6px;
        border-radius:9px;
        padding:0 11px;
        font-size:8px;
        font-weight:600;
        text-decoration:none;
        cursor:pointer;
    }

    .rp-btn svg {
        width:12px;
        height:12px;
    }

    .rp-btn-secondary,
    .rp-btn-link {
        border:1px solid var(--sari-dark-d8c2716225, #e5ddd2);
        background:var(--sari-dark-950baec030, #fff);
        color:var(--sari-dark-a63aa16d7c, #6f665b);
        box-shadow:none;
    }

    .rp-btn-secondary:hover,
    .rp-btn-link:hover {
        border-color:var(--sari-dark-d8c2716225, #d4c5b4);
        background:var(--sari-dark-950baec030, #faf8f4);
        color:var(--sari-dark-e23e64dd1e, #514940);
    }

    .rp-btn-primary {
        border:1px solid var(--rp-brand);
        background:var(--rp-brand);
        color:#fff;
        box-shadow:0 4px 10px var(--sari-dark-71f44bdb16, rgba(217,149,0,.11));
    }

    .rp-btn-primary:hover {
        border-color:var(--rp-brand-strong);
        background:var(--rp-brand-strong);
        transform:translateY(-1px);
    }

    .rp-error {
        margin-top:10px;
        border:1px solid var(--sari-dark-d8c2716225, #efcbc6);
        border-radius:10px;
        background:var(--sari-dark-950baec030, #fff6f4);
        padding:8px 10px;
        color:var(--sari-dark-a63aa16d7c, #a84f46);
        font-size:8px;
    }

    /* ---------------- DATE RANGE PICKER ---------------- */
    .rp-range-picker {
        position:relative;
        z-index:70;
    }

    .rp-date-range-trigger {
        display:flex;
        min-width:226px;
        height:38px;
        align-items:center;
        gap:8px;
        border:1px solid var(--sari-dark-d8c2716225, #e5ddd2);
        border-radius:9px;
        background:var(--sari-dark-950baec030, #fff);
        padding:0 10px;
        color:var(--sari-dark-e23e64dd1e, #4f493f);
        font:inherit;
        text-align:left;
        cursor:pointer;
        box-shadow:none;
    }

    .rp-date-range-trigger:hover,
    .rp-range-picker.is-open .rp-date-range-trigger {
        border-color:#d4b878;
        box-shadow:0 0 0 3px var(--sari-dark-4fc908fafe, rgba(197,141,32,.065));
    }

    .rp-date-range-icon {
        width:14px;
        height:14px;
        flex:0 0 14px;
        color:var(--sari-dark-6e784b4931, #b77b16);
    }

    .rp-date-range-copy {
        display:grid;
        min-width:0;
        flex:1;
        line-height:1.18;
    }

    .rp-date-range-copy small {
        color:var(--sari-dark-a63aa16d7c, #9b9388);
        font-size:6.5px;
        font-weight:650;
        letter-spacing:.04em;
        text-transform:uppercase;
    }

    .rp-date-range-copy strong {
        overflow:hidden;
        margin-top:2px;
        color:var(--sari-dark-e23e64dd1e, #4a4239);
        font-size:8px;
        font-weight:650;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    .rp-date-range-chevron {
        width:12px;
        height:12px;
        flex:0 0 12px;
        color:var(--sari-dark-a63aa16d7c, #8d8376);
    }

    .rp-range-picker.is-open .rp-date-range-chevron {
        transform:rotate(180deg);
    }

    .rp-calendar-popover {
        position:absolute;
        z-index:120;
        top:calc(100% + 7px);
        right:0;
        width:min(650px,calc(100vw - 48px));
        padding:11px;
        border:1px solid var(--sari-dark-d8c2716225, #e3d7c8);
        border-radius:14px;
        background:var(--sari-dark-950baec030, #fffefb);
        box-shadow:0 18px 46px var(--sari-dark-59ed29fe9d, rgba(55,39,19,.14));
        opacity:0;
        visibility:hidden;
        pointer-events:none;
        transform:translateY(-4px) scale(.99);
        transform-origin:top right;
    }

    .rp-range-picker.is-open .rp-calendar-popover {
        opacity:1;
        visibility:visible;
        pointer-events:auto;
        transform:translateY(0) scale(1);
    }

    .rp-calendar-dual {
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:9px;
    }

    .rp-calendar-panel {
        min-width:0;
        border:1px solid var(--sari-dark-d8c2716225, #ece3d7);
        border-radius:11px;
        background:var(--sari-dark-950baec030, #fff);
        padding:9px;
        box-shadow:none;
    }

    .rp-calendar-panel-label {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:8px;
        margin-bottom:7px;
    }

    .rp-calendar-panel-label span {
        color:var(--sari-dark-a63aa16d7c, #9b9388);
        font-size:6.5px;
        font-weight:700;
        letter-spacing:.04em;
        text-transform:uppercase;
    }

    .rp-calendar-panel-label strong {
        color:var(--sari-dark-e23e64dd1e, #6d5d45);
        font-size:7.5px;
        font-weight:650;
    }

    .rp-calendar-head {
        display:grid;
        grid-template-columns:30px minmax(0,1fr) 30px;
        gap:6px;
        align-items:center;
    }

    .rp-calendar-nav {
        display:grid;
        width:30px;
        height:30px;
        place-items:center;
        border:1px solid var(--sari-dark-d8c2716225, #e8dfd3);
        border-radius:8px;
        background:var(--sari-dark-950baec030, #fff);
        color:var(--sari-dark-a63aa16d7c, #756d61);
        cursor:pointer;
    }

    .rp-calendar-nav:hover:not(:disabled) {
        border-color:#d8bd85;
        background:var(--sari-dark-4d21eb669b, #fff8e9);
        color:var(--sari-dark-9b2ca2d764, #a97012);
    }

    .rp-calendar-nav:disabled {
        opacity:.35;
        cursor:not-allowed;
    }

    .rp-calendar-nav svg {
        width:13px;
        height:13px;
    }

    .rp-calendar-controls {
        display:grid;
        grid-template-columns:minmax(0,1fr) 72px;
        gap:5px;
    }

    .rp-cal-select {
        position:relative;
        min-width:0;
        z-index:12;
    }

    .rp-cal-select.is-open {
        z-index:40;
    }

    .rp-cal-select-trigger {
        display:flex;
        width:100%;
        height:30px;
        align-items:center;
        justify-content:space-between;
        gap:6px;
        border:1px solid var(--sari-dark-d8c2716225, #e6ddd0);
        border-radius:8px;
        background:var(--sari-dark-950baec030, #fff);
        padding:0 8px;
        color:var(--sari-dark-e23e64dd1e, #4b4238);
        font:inherit;
        font-size:7.5px;
        font-weight:650;
        cursor:pointer;
    }

    .rp-cal-select-trigger:hover,
    .rp-cal-select.is-open .rp-cal-select-trigger {
        border-color:#d7b978;
        background:var(--sari-dark-4d21eb669b, #fff8ea);
    }

    .rp-cal-select-trigger svg {
        width:11px;
        height:11px;
        flex:0 0 11px;
        color:var(--sari-dark-a63aa16d7c, #8d8376);
    }

    .rp-cal-select.is-open .rp-cal-select-trigger svg {
        transform:rotate(180deg);
    }

    .rp-cal-select-menu {
        position:absolute;
        z-index:50;
        top:calc(100% + 4px);
        right:0;
        left:0;
        max-height:192px;
        overflow-y:auto;
        padding:4px;
        border:1px solid var(--sari-dark-d8c2716225, #e4d9ca);
        border-radius:9px;
        background:var(--sari-dark-950baec030, #fff);
        box-shadow:0 12px 28px var(--sari-dark-3b2338cbf0, rgba(55,39,19,.12));
        opacity:0;
        visibility:hidden;
        pointer-events:none;
        transform:translateY(-3px);
        scrollbar-width:thin;
        scrollbar-color:#d9cfc2 transparent;
    }

    .rp-cal-select.is-open .rp-cal-select-menu {
        opacity:1;
        visibility:visible;
        pointer-events:auto;
        transform:translateY(0);
    }

    .rp-cal-select-option {
        display:flex;
        width:100%;
        min-height:29px;
        align-items:center;
        justify-content:space-between;
        gap:6px;
        border:0;
        border-radius:7px;
        background:transparent;
        padding:0 7px;
        color:var(--sari-dark-e23e64dd1e, #5b5145);
        font:inherit;
        font-size:7.5px;
        font-weight:550;
        text-align:left;
        cursor:pointer;
    }

    .rp-cal-select-option:hover,
    .rp-cal-select-option:focus-visible,
    .rp-cal-select-option.is-selected {
        outline:none;
        background:var(--sari-dark-4d21eb669b, #fff8e9);
        color:var(--sari-dark-6e784b4931, #9b6812);
    }

    .rp-cal-select-option.is-selected {
        font-weight:700;
    }

    .rp-cal-select-option.is-selected::after {
        content:'✓';
        color:var(--sari-dark-3be2792842, #c58d20);
        font-size:8px;
        font-weight:800;
    }

    .rp-calendar-weekdays,
    .rp-calendar-grid {
        display:grid;
        grid-template-columns:repeat(7,1fr);
        gap:3px;
    }

    .rp-calendar-weekdays {
        margin-top:8px;
        color:var(--sari-dark-a63aa16d7c, #9b9388);
        font-size:6.5px;
        font-weight:700;
        text-align:center;
    }

    .rp-calendar-weekdays span {
        padding:3px 0;
    }

    .rp-calendar-grid {
        margin-top:2px;
    }

    .rp-calendar-day {
        position:relative;
        display:grid;
        height:29px;
        place-items:center;
        border:0;
        border-radius:7px;
        background:transparent;
        color:var(--sari-dark-e23e64dd1e, #4f493f);
        font:inherit;
        font-size:7.5px;
        font-weight:550;
        cursor:pointer;
    }

    .rp-calendar-day:hover:not(:disabled) {
        background:var(--sari-dark-4d21eb669b, #fff6df);
        color:var(--sari-dark-c8bdcce6ab, #9e6d13);
    }

    .rp-calendar-day.is-outside { color:var(--sari-dark-a63aa16d7c, #c8c0b6); }

    .rp-calendar-day.is-today::after {
        content:'';
        position:absolute;
        bottom:3px;
        width:3px;
        height:3px;
        border-radius:50%;
        background:#c58d20;
    }

    .rp-calendar-day.is-in-range {
        background:var(--sari-dark-4d21eb669b, #fff8e9);
        color:var(--sari-dark-e23e64dd1e, #7b5b22);
    }

    .rp-calendar-day.is-selected {
        background:#c99022;
        color:#fff;
        box-shadow:0 4px 9px var(--sari-dark-59ed29fe9d, rgba(166,112,18,.14));
    }

    .rp-calendar-day.is-selected.is-today::after { background:var(--sari-dark-950baec030, #fff); }

    .rp-calendar-day:disabled {
        color:var(--sari-dark-a63aa16d7c, #d7d1c9);
        cursor:not-allowed;
    }

    .rp-calendar-selection {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        margin-top:9px;
        border:1px solid var(--sari-dark-d8c2716225, #eee5d9);
        border-radius:9px;
        background:var(--sari-dark-950baec030, #fcfaf6);
        padding:8px 9px;
    }

    .rp-calendar-selection span {
        color:var(--sari-dark-a63aa16d7c, #9b9388);
        font-size:6.5px;
    }

    .rp-calendar-selection strong {
        color:var(--sari-dark-e23e64dd1e, #554a3d);
        font-size:7.5px;
        font-weight:650;
        text-align:right;
    }

    .rp-calendar-footer {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:7px;
        margin-top:8px;
    }

    .rp-calendar-ghost,
    .rp-calendar-apply {
        height:32px;
        border-radius:8px;
        padding:0 10px;
        font:inherit;
        font-size:7.5px;
        font-weight:650;
        cursor:pointer;
    }

    .rp-calendar-ghost {
        border:1px solid var(--sari-dark-d8c2716225, #e7dfd4);
        background:var(--sari-dark-950baec030, #fff);
        color:var(--sari-dark-a63aa16d7c, #756d61);
    }

    .rp-calendar-apply {
        border:1px solid #b87b16;
        background:#c99022;
        color:#fff;
        box-shadow:none;
    }

    .rp-calendar-apply:hover {
        background:#aa7418;
    }

    /* ---------------- KPI SUMMARY ---------------- */
    .rp-summary-grid {
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:9px;
        margin-top:11px;
    }

    .rp-stat {
        position:relative;
        min-height:76px;
        padding:11px 50px 11px 13px;
        overflow:hidden;
        contain:paint;
    }

    .rp-stat:hover {
        border-color:var(--sari-dark-d8c2716225, #ddcfbb);
        transform:translateY(-1px);
        box-shadow:0 8px 22px var(--sari-dark-361b571631, rgba(61,43,22,.06));
    }

    .rp-stat-icon {
        position:absolute;
        top:12px;
        right:12px;
        display:grid;
        width:32px;
        height:32px;
        place-items:center;
        border-radius:9px;
        border:1px solid transparent;
    }

    .rp-stat-icon svg {
        width:14px;
        height:14px;
    }

    .rp-stat.blue .rp-stat-icon { background:var(--sari-dark-4d21eb669b, #fff7e6);color:var(--sari-dark-00a00a0e12, #b17a1f);border-color:#ecd7aa; }
    .rp-stat.green .rp-stat-icon { background:var(--sari-dark-4d21eb669b, #f1f6ef);color:var(--sari-dark-a63aa16d7c, #6c7f63);border-color:var(--sari-dark-d8c2716225, #dce8d8); }
    .rp-stat.violet .rp-stat-icon { background:var(--sari-dark-4d21eb669b, #f8f1e5);color:var(--sari-dark-a63aa16d7c, #8f6929);border-color:var(--sari-dark-d8c2716225, #eadcc3); }
    .rp-stat.orange .rp-stat-icon { background:var(--sari-dark-4d21eb669b, #fff3e5);color:var(--sari-dark-cc648930ee, #c17a25);border-color:var(--sari-dark-d8c2716225, #efd9bc); }
    .rp-stat.teal .rp-stat-icon { background:var(--sari-dark-4d21eb669b, #f0f5f2);color:var(--sari-dark-a63aa16d7c, #607a72);border-color:var(--sari-dark-d8c2716225, #dce7e2); }
    .rp-stat.red .rp-stat-icon { background:var(--sari-dark-950baec030, #fff2ef);color:var(--sari-dark-ba7d4c2589, #b86556);border-color:var(--sari-dark-d8c2716225, #f0d6d0); }
    .rp-stat.purple .rp-stat-icon { background:var(--sari-dark-4d21eb669b, #f7f3f6);color:var(--sari-dark-a63aa16d7c, #806f7f);border-color:var(--sari-dark-d8c2716225, #e7dee5); }
    .rp-stat.amber .rp-stat-icon { background:var(--sari-dark-4d21eb669b, #fff7e7);color:var(--sari-dark-2423d1d2de, #c18a20);border-color:var(--sari-dark-d8c2716225, #eeddb4); }

    .rp-stat-label {
        color:var(--sari-dark-a63aa16d7c, #8e857a);
        font-size:8px;
        font-weight:500;
        line-height:1.3;
    }

    .rp-stat-value {
        margin-top:4px;
        color:var(--sari-dark-e23e64dd1e, #28221b);
        font-size:19px;
        font-weight:700;
        line-height:1;
        letter-spacing:-.035em;
        white-space:nowrap;
    }

    .rp-stat-footer {
        display:flex;
        min-width:0;
        align-items:center;
        gap:5px;
        margin-top:6px;
    }

    .rp-delta {
        display:inline-flex;
        min-height:19px;
        align-items:center;
        gap:3px;
        border-radius:999px;
        padding:0 6px;
        font-size:6.5px;
        font-weight:700;
        white-space:nowrap;
    }

    .rp-delta.good { background:var(--sari-dark-4d21eb669b, #eef8f1);color:var(--sari-dark-a63aa16d7c, #4f8060); }
    .rp-delta.bad { background:var(--sari-dark-950baec030, #fff2ef);color:var(--sari-dark-887359ecf4, #a65d5d); }
    .rp-delta.neutral { background:var(--sari-dark-4d21eb669b, #f4f2ef);color:var(--sari-dark-a63aa16d7c, #81786d); }

    .rp-stat-help {
        overflow:hidden;
        color:var(--sari-dark-a63aa16d7c, #9b9288);
        font-size:7px;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    /* ---------------- ANALYTICS ---------------- */
    .rp-main-grid {
        display:grid;
        grid-template-columns:minmax(0,1.65fr) minmax(300px,.75fr);
        gap:10px;
        margin-top:10px;
    }

    .rp-panel {
        min-height:272px;
        padding:14px;
    }

    .rp-panel-head {
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:10px;
    }

    .rp-panel-title {
        color:var(--sari-dark-e23e64dd1e, #302a24);
        font-size:11px;
        font-weight:700;
        line-height:1.35;
    }

    .rp-panel-copy {
        margin-top:3px;
        color:var(--sari-dark-a63aa16d7c, #91887d);
        font-size:7.5px;
        line-height:1.5;
    }

    .rp-period-chip {
        display:inline-flex;
        min-height:24px;
        align-items:center;
        border:1px solid var(--sari-dark-d8c2716225, #e7dfd4);
        border-radius:7px;
        background:var(--sari-dark-950baec030, #fff);
        padding:0 7px;
        color:var(--sari-dark-a63aa16d7c, #81786d);
        font-size:6.5px;
        font-weight:600;
        white-space:nowrap;
    }

    .rp-chart-wrap {
        position:relative;
        height:205px;
        margin-top:9px;
        overflow:visible;
    }

    .rp-chart-svg {
        display:block;
        width:100%;
        height:205px;
    }

    .rp-chart-tooltip {
        position:absolute;
        z-index:20;
        min-width:120px;
        pointer-events:none;
        transform:translate(-50%,-118%);
        border:1px solid var(--sari-dark-d8c2716225, #e1dad1);
        border-radius:9px;
        background:var(--sari-dark-68ba16e186, rgba(255,255,255,.99));
        padding:7px 9px;
        box-shadow:0 8px 20px var(--sari-dark-71f44bdb16, rgba(31,24,17,.11));
        opacity:0;
    }

    .rp-chart-tooltip.show { opacity:1; }
    .rp-chart-tooltip.below { transform:translate(-50%,12px); }

    .rp-chart-tooltip small {
        display:block;
        color:var(--sari-dark-a63aa16d7c, #81786d);
        font-size:7px;
        font-weight:500;
    }

    .rp-chart-tooltip strong {
        display:block;
        margin-top:3px;
        color:var(--sari-dark-e23e64dd1e, #302a24);
        font-size:9px;
        font-weight:700;
    }

    .rp-chart-tooltip span {
        display:block;
        margin-top:2px;
        color:var(--sari-dark-a63aa16d7c, #81786d);
        font-size:7px;
    }

    .rp-donut-layout {
        display:grid;
        grid-template-columns:118px 1fr;
        gap:12px;
        align-items:center;
        margin-top:13px;
    }

    .rp-donut {
        position:relative;
        display:grid;
        width:112px;
        height:112px;
        place-items:center;
        border-radius:50%;
        background:var(--status-gradient);
    }

    .rp-donut::before {
        content:"";
        position:absolute;
        width:78px;
        height:78px;
        border-radius:50%;
        background:var(--sari-dark-950baec030, #fff);
        box-shadow:inset 0 0 0 1px var(--sari-dark-f30a5f9061, #eee7de);
    }

    .rp-donut-center {
        position:relative;
        text-align:center;
    }

    .rp-donut-total {
        color:var(--sari-dark-e23e64dd1e, #302a24);
        font-size:17px;
        font-weight:700;
        line-height:1;
    }

    .rp-donut-caption {
        margin-top:3px;
        color:var(--sari-dark-a63aa16d7c, #91887d);
        font-size:6.5px;
    }

    .rp-legend {
        display:grid;
        gap:6px;
    }

    .rp-legend-row {
        display:grid;
        grid-template-columns:8px minmax(0,1fr) 34px 42px;
        gap:6px;
        align-items:center;
        color:var(--sari-dark-a63aa16d7c, #81786d);
        font-size:7px;
    }

    .rp-legend-dot {
        width:7px;
        height:7px;
        border-radius:3px;
    }

    .rp-legend-count {
        color:var(--sari-dark-e23e64dd1e, #514a42);
        font-weight:650;
        text-align:right;
    }

    .rp-legend-percent {
        color:var(--sari-dark-a63aa16d7c, #9b9288);
        text-align:right;
    }

    /* ---------------- SECONDARY CHARTS ---------------- */
    .rp-secondary-grid {
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:10px;
        margin-top:10px;
    }

    .rp-mini-panel {
        min-height:218px;
        padding:14px;
    }

    .rp-bars {
        display:flex;
        height:132px;
        align-items:flex-end;
        gap:3px;
        margin-top:11px;
        padding:6px 2px 0;
        border-bottom:1px solid var(--sari-dark-d8c2716225, #eee7de);
    }

    .rp-bar-item,
    .rp-reg-group {
        position:relative;
        display:flex;
        flex:1;
        height:100%;
        min-width:2px;
        align-items:flex-end;
        justify-content:center;
        outline:none;
    }

    .rp-bar {
        width:min(10px,70%);
        min-height:2px;
        border-radius:4px 4px 2px 2px;
        background:#c99022;
    }

    .rp-bar.reg { background:#806f7f; }

    .rp-bar-tip {
        position:absolute;
        bottom:calc(var(--bar-height) + 6px);
        left:50%;
        z-index:10;
        display:none;
        transform:translateX(-50%);
        border:1px solid var(--sari-dark-d8c2716225, #e0e5eb);
        border-radius:7px;
        background:var(--sari-dark-950baec030, #fff);
        padding:5px 7px;
        color:var(--sari-dark-e23e64dd1e, #4f493f);
        font-size:6.5px;
        white-space:nowrap;
        box-shadow:0 7px 16px var(--sari-dark-b12a5e9552, rgba(16,24,40,.09));
    }

    .rp-bar-item:hover .rp-bar-tip,
    .rp-bar-item:focus .rp-bar-tip,
    .rp-reg-group:hover .rp-bar-tip,
    .rp-reg-group:focus .rp-bar-tip {
        display:block;
    }

    .rp-axis {
        display:flex;
        justify-content:space-between;
        margin-top:6px;
        color:var(--sari-dark-a63aa16d7c, #9b9288);
        font-size:6.5px;
    }

    .rp-registration-legend {
        display:flex;
        flex-wrap:wrap;
        align-items:center;
        gap:10px;
        margin-top:8px;
        color:var(--sari-dark-a63aa16d7c, #81786d);
        font-size:6.8px;
        font-weight:600;
    }

    .rp-registration-legend span {
        display:inline-flex;
        align-items:center;
        gap:5px;
    }

    .rp-registration-legend i {
        display:inline-block;
        width:7px;
        height:7px;
        border-radius:2px;
    }

    .rp-registration-legend i.submitted { background:#c99022; }
    .rp-registration-legend i.pending { background:#b86556; }

    .rp-registration-bars {
        margin-top:6px;
    }

    .rp-reg-group {
        gap:2px;
    }

    .rp-reg-bar {
        width:min(7px,40%);
        min-width:2px;
        min-height:2px;
        border-radius:4px 4px 2px 2px;
    }

    .rp-reg-bar.submitted { background:#c99022; }
    .rp-reg-bar.pending { background:#b86556; }

    /* ---------------- LEDGER ---------------- */
    .rp-ledger {
        margin-top:10px;
        overflow:hidden;
    }

    .rp-section-head {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        padding:11px 14px;
        border-bottom:1px solid var(--sari-dark-d8c2716225, #eee8df);
    }

    .rp-table-wrap {
        overflow-x:auto;
    }

    .rp-table {
        width:100%;
        min-width:940px;
        border-collapse:collapse;
        text-align:left;
    }

    .rp-table thead {
        background:var(--sari-dark-950baec030, #faf9f6);
    }

    .rp-table th {
        padding:9px 11px;
        border-bottom:1px solid var(--sari-dark-d8c2716225, #eee8df);
        color:var(--sari-dark-a63aa16d7c, #81786d);
        font-size:7.5px;
        font-weight:700;
        letter-spacing:.04em;
        text-transform:uppercase;
    }

    .rp-table td {
        padding:9px 11px;
        border-bottom:1px solid var(--sari-dark-d8c2716225, #f0ebe4);
        color:var(--sari-dark-e23e64dd1e, #5b534a);
        font-size:8px;
        vertical-align:middle;
    }

    .rp-table tbody tr {
        content-visibility:auto;
        contain-intrinsic-size:44px;
    }

    .rp-table tbody tr:hover {
        background:var(--sari-dark-950baec030, #fdfbf8);
    }

    .rp-order,
    .rp-money {
        color:var(--sari-dark-e23e64dd1e, #302a24);
        font-weight:700;
        white-space:nowrap;
    }

    .rp-status {
        display:inline-flex;
        min-height:22px;
        align-items:center;
        gap:5px;
        border-radius:999px;
        padding:0 7px;
        background:var(--sari-dark-4d21eb669b, #f4f2ef);
        color:var(--sari-dark-a63aa16d7c, #81786d);
        font-size:7px;
        font-weight:650;
        white-space:nowrap;
    }

    .rp-status::before {
        content:"";
        width:5px;
        height:5px;
        border-radius:50%;
        background:currentColor;
    }

    .rp-status.delivered { background:var(--sari-dark-4d21eb669b, #eef8f1);color:var(--sari-dark-a63aa16d7c, #4f8060); }
    .rp-status.cancelled { background:var(--sari-dark-950baec030, #fff2ef);color:var(--sari-dark-887359ecf4, #a65d5d); }
    .rp-status.in_transit,
    .rp-status.heading_pickup,
    .rp-status.courier_accepted { background:var(--sari-dark-4d21eb669b, #f0f4f2);color:var(--sari-dark-a63aa16d7c, #607a72); }
    .rp-status.preparing,
    .rp-status.ready_for_pickup { background:var(--sari-dark-4d21eb669b, #f6f2f5);color:var(--sari-dark-a63aa16d7c, #806f7f); }

    .rp-empty {
        display:grid;
        min-height:96px;
        place-items:center;
        color:var(--sari-dark-a63aa16d7c, #91887d);
        font-size:8px;
        text-align:center;
    }

    /* ---------------- RESPONSIVE ---------------- */
    @media (max-height:850px) and (min-width:900px) {
        .rp-title { font-size:22px; }

        .rp-stat {
            min-height:70px;
            padding-top:9px;
            padding-bottom:9px;
        }

        .rp-stat-value { font-size:18px; }

        .rp-panel {
            min-height:250px;
            padding:12px;
        }

        .rp-chart-wrap,
        .rp-chart-svg {
            height:188px;
        }

        .rp-mini-panel {
            min-height:205px;
            padding:12px;
        }

        .rp-bars {
            height:122px;
        }
    }

    @media (max-width:1250px) {
        .rp-main-grid,
        .rp-secondary-grid {
            grid-template-columns:1fr;
        }
    }

    @media (max-width:850px) {
        .rp-header {
            align-items:stretch;
            flex-direction:column;
        }

        .rp-actions {
            justify-content:flex-start;
        }

        .rp-filter-form {
            width:100%;
        }

        .rp-range-picker {
            flex:1 1 100%;
            width:100%;
        }

        .rp-date-range-trigger {
            width:100%;
            min-width:0;
        }

        .rp-calendar-popover {
            right:auto;
            left:0;
            width:min(650px,calc(100vw - 42px));
            transform-origin:top left;
        }

        .rp-summary-grid {
            grid-template-columns:repeat(2,minmax(0,1fr));
        }

        .rp-donut-layout {
            grid-template-columns:1fr;
            justify-items:center;
        }

        .rp-legend {
            width:100%;
        }
    }

    @media (max-width:560px) {
        .rp-summary-grid {
            grid-template-columns:1fr;
        }

        .rp-calendar-popover {
            width:min(340px,calc(100vw - 28px));
        }

        .rp-calendar-dual {
            grid-template-columns:1fr;
        }

        .rp-btn {
            min-height:40px;
            height:40px;
            font-size:9px;
        }
    }

    @media (prefers-reduced-motion:reduce) {
        .reports-page *,
        .rp-calendar-popover,
        .rp-cal-select-menu {
            animation:none !important;
            transition:none !important;
            transform:none !important;
            scroll-behavior:auto !important;
        }
    }

    /* ============================================================
       PLATFORM REPORTS — DESKTOP LAYOUT MATCH
       Mirrors the approved reference composition:
       8 KPI cards → 2/3 + 1/3 analytics → 50/50 activity row.
       Performance JS and backend/report contracts remain unchanged.
       ============================================================ */

    @media (min-width: 1501px) {
        .reports-page {
            max-width: 1640px !important;
        }

        .rp-header {
            align-items: center !important;
            gap: 20px !important;
        }

        .rp-title-wrap {
            gap: 10px !important;
        }

        .rp-title-icon {
            width: 38px !important;
            height: 38px !important;
            flex-basis: 38px !important;
            border-radius: 10px !important;
        }

        .rp-title-icon svg {
            width: 16px !important;
            height: 16px !important;
        }

        .rp-eyebrow {
            font-size: 8px !important;
        }

        .rp-title {
            font-size: 28px !important;
            line-height: 1.05 !important;
        }

        .rp-subtitle {
            margin-top: 6px !important;
            font-size: 9.5px !important;
        }

        .rp-actions {
            gap: 12px !important;
        }

        .rp-date-range-trigger {
            min-width: 270px !important;
            height: 46px !important;
            border-radius: 10px !important;
            padding-inline: 13px !important;
        }

        .rp-date-range-icon {
            width: 16px !important;
            height: 16px !important;
            flex-basis: 16px !important;
        }

        .rp-date-range-copy small {
            font-size: 7px !important;
        }

        .rp-date-range-copy strong {
            font-size: 8.5px !important;
        }

        .rp-btn {
            height: 46px !important;
            border-radius: 10px !important;
            padding-inline: 13px !important;
            font-size: 8.5px !important;
        }

        .rp-btn svg {
            width: 13px !important;
            height: 13px !important;
        }

        /* Approved screenshot: all 8 KPIs stay on one row. */
        .rp-summary-grid {
            grid-template-columns: repeat(8, minmax(0, 1fr)) !important;
            gap: 16px !important;
            margin-top: 20px !important;
        }

        .rp-stat {
            min-height: 116px !important;
            padding: 15px 50px 15px 16px !important;
            border-radius: 15px !important;
        }

        .rp-stat-icon {
            top: 14px !important;
            right: 14px !important;
            width: 34px !important;
            height: 34px !important;
            border-radius: 10px !important;
        }

        .rp-stat-icon svg {
            width: 14px !important;
            height: 14px !important;
        }

        .rp-stat-label {
            font-size: 8.5px !important;
        }

        .rp-stat-value {
            margin-top: 7px !important;
            font-size: 20px !important;
        }

        .rp-stat-footer {
            margin-top: 8px !important;
            gap: 6px !important;
        }

        .rp-delta {
            min-height: 21px !important;
            padding-inline: 7px !important;
            font-size: 7px !important;
        }

        .rp-stat-help {
            font-size: 7px !important;
        }

        /* Reference screenshot: wide revenue panel + narrower status panel. */
        .rp-main-grid {
            grid-template-columns: minmax(0, 1.95fr) minmax(390px, 1fr) !important;
            gap: 16px !important;
            margin-top: 14px !important;
        }

        .rp-panel {
            min-height: 372px !important;
            padding: 22px !important;
            border-radius: 16px !important;
        }

        .rp-panel-title {
            font-size: 16px !important;
        }

        .rp-panel-copy {
            margin-top: 5px !important;
            font-size: 9.5px !important;
        }

        .rp-period-chip {
            min-height: 34px !important;
            border-radius: 9px !important;
            padding-inline: 10px !important;
            font-size: 8px !important;
        }

        .rp-chart-wrap {
            height: 278px !important;
            margin-top: 18px !important;
        }

        .rp-chart-svg {
            height: 278px !important;
        }

        .rp-chart-tooltip {
            min-width: 140px !important;
        }

        .rp-chart-tooltip small,
        .rp-chart-tooltip span {
            font-size: 8px !important;
        }

        .rp-chart-tooltip strong {
            font-size: 9.5px !important;
        }

        .rp-donut-layout {
            grid-template-columns: 160px minmax(0, 1fr) !important;
            gap: 20px !important;
            margin-top: 24px !important;
        }

        .rp-donut {
            width: 152px !important;
            height: 152px !important;
        }

        .rp-donut::before {
            width: 106px !important;
            height: 106px !important;
        }

        .rp-donut-total {
            font-size: 22px !important;
        }

        .rp-donut-caption {
            margin-top: 5px !important;
            font-size: 8px !important;
        }

        .rp-legend {
            gap: 10px !important;
        }

        .rp-legend-row {
            grid-template-columns: 9px minmax(0, 1fr) 32px 46px !important;
            gap: 8px !important;
            font-size: 8.5px !important;
        }

        .rp-legend-dot {
            width: 8px !important;
            height: 8px !important;
        }

        /* Second row keeps two equal cards, like the reference image. */
        .rp-secondary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 16px !important;
            margin-top: 14px !important;
        }

        .rp-mini-panel {
            min-height: 270px !important;
            padding: 22px !important;
            border-radius: 16px !important;
        }

        .rp-bars {
            height: 166px !important;
            margin-top: 20px !important;
        }

        .rp-axis {
            margin-top: 8px !important;
            font-size: 8px !important;
        }

        .rp-registration-legend {
            margin-top: 11px !important;
            font-size: 8px !important;
        }

        .rp-ledger {
            margin-top: 14px !important;
            border-radius: 16px !important;
        }

        .rp-section-head {
            padding: 14px 18px !important;
        }

        .rp-table th {
            padding: 10px 12px !important;
            font-size: 8px !important;
        }

        .rp-table td {
            padding: 11px 12px !important;
            font-size: 8.5px !important;
        }

        .rp-status {
            min-height: 24px !important;
            font-size: 7.5px !important;
        }
    }

    /* Medium desktop/laptop: preserve hierarchy without squeezing 8 cards. */
    @media (min-width: 1101px) and (max-width: 1500px) {
        .rp-summary-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        }

        .rp-main-grid {
            grid-template-columns: minmax(0, 1.7fr) minmax(320px, .85fr) !important;
        }

        .rp-panel {
            min-height: 320px !important;
        }

        .rp-chart-wrap,
        .rp-chart-svg {
            height: 235px !important;
        }
    }

    @media (max-width: 1100px) {
        .rp-main-grid,
        .rp-secondary-grid {
            grid-template-columns: 1fr !important;
        }
    }


    @media (min-width: 1501px) {
        .rp-header {
            margin-bottom: 2px !important;
        }

        .rp-actions,
        .rp-filter-form {
            align-items: center !important;
        }

        .rp-summary-grid .rp-card {
            overflow: hidden !important;
        }

        .rp-stat-label {
            margin-right: 18px !important;
            line-height: 1.35 !important;
        }

        .rp-stat-help {
            max-width: 85% !important;
        }

        .rp-panel-head {
            gap: 14px !important;
        }
    }


    /* ============================================================
       KPI SUMMARY — UNIFORM CARD LAYOUT
       Matches the approved compact reference row:
       equal heights, equal icon sizing, balanced spacing.
       ============================================================ */

    .rp-summary-grid > *{
        min-width:0 !important;
    }

    .rp-stat{
        display:flex !important;
        flex-direction:column !important;
        justify-content:flex-start !important;
        min-height:108px !important;
        padding:13px 50px 13px 14px !important;
        border-radius:14px !important;
        overflow:hidden !important;
    }

    .rp-stat-icon{
        top:12px !important;
        right:12px !important;
        width:34px !important;
        height:34px !important;
        border-radius:10px !important;
    }

    .rp-stat-icon svg{
        width:14px !important;
        height:14px !important;
    }

    .rp-stat-label{
        margin-right:18px !important;
        min-height:22px !important;
        color:var(--sari-dark-a63aa16d7c, #8f8578) !important;
        font-size:8px !important;
        font-weight:600 !important;
        line-height:1.35 !important;
    }

    .rp-stat-value{
        margin-top:4px !important;
        color:var(--sari-dark-e23e64dd1e, #241f1a) !important;
        font-size:20px !important;
        font-weight:700 !important;
        line-height:1 !important;
        letter-spacing:-.03em !important;
        white-space:nowrap !important;
    }

    .rp-stat-footer{
        display:flex !important;
        align-items:center !important;
        gap:7px !important;
        margin-top:auto !important;
        padding-top:10px !important;
        min-height:28px !important;
    }

    .rp-delta{
        display:inline-flex !important;
        align-items:center !important;
        justify-content:center !important;
        min-height:19px !important;
        padding:0 7px !important;
        border-radius:999px !important;
        font-size:6.8px !important;
        font-weight:700 !important;
        white-space:nowrap !important;
        flex:0 0 auto !important;
    }

    .rp-stat-help{
        min-width:0 !important;
        flex:1 1 auto !important;
        color:var(--sari-dark-a63aa16d7c, #a0978b) !important;
        font-size:7px !important;
        line-height:1.35 !important;
        white-space:nowrap !important;
        overflow:hidden !important;
        text-overflow:ellipsis !important;
    }

    @media (min-width: 1450px){
        .rp-summary-grid{
            grid-template-columns:repeat(8,minmax(0,1fr)) !important;
            gap:12px !important;
            margin-top:16px !important;
        }
    }

    @media (min-width: 1101px) and (max-width: 1449px){
        .rp-summary-grid{
            grid-template-columns:repeat(4,minmax(0,1fr)) !important;
            gap:12px !important;
        }

        .rp-stat{
            min-height:104px !important;
        }
    }

    @media (max-width: 1100px){
        .rp-summary-grid{
            grid-template-columns:repeat(2,minmax(0,1fr)) !important;
            gap:10px !important;
        }

        .rp-stat{
            min-height:100px !important;
        }
    }

    @media (max-width: 640px){
        .rp-summary-grid{
            grid-template-columns:1fr !important;
        }
    }



    /* ============================================================
       FINAL SELLER-DERIVED ADMIN VISUAL SYSTEM — PLATFORM REPORTS
       Visual-only authority layer. Report data, exports, calendar,
       chart rendering, routes, and JavaScript behavior are unchanged.
       ============================================================ */

    .reports-page {
        --rp-seller-canvas: var(--sari-dark-4d21eb669b, #F4F5F7);
        --rp-seller-surface: var(--sari-dark-950baec030, #FFFFFF);
        --rp-seller-soft: #F8FAFC;
        --rp-seller-gold: #D59617;
        --rp-seller-gold-strong: #B97805;
        --rp-seller-ink: var(--sari-dark-e23e64dd1e, #111827);
        --rp-seller-text: var(--sari-dark-e23e64dd1e, #344054);
        --rp-seller-muted: var(--sari-dark-a63aa16d7c, #7C8592);
        --rp-seller-border: var(--sari-dark-d8c2716225, #E5E7EB);
        --rp-seller-border-strong: var(--sari-dark-d8c2716225, #D1D5DB);
        --rp-seller-divider: var(--sari-dark-d8c2716225, #ECEFF2);
        --rp-seller-shadow-main:
            0 2px 5px var(--sari-dark-1190f58155, rgba(15, 23, 42, .045)),
            0 10px 24px var(--sari-dark-e07575a086, rgba(15, 23, 42, .075)),
            0 24px 54px var(--sari-dark-a2f72dee3d, rgba(15, 23, 42, .095)),
            inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255, 255, 255, .92));
        --rp-seller-shadow-soft:
            0 1px 3px var(--sari-dark-7593018246, rgba(15, 23, 42, .035)),
            0 7px 18px var(--sari-dark-361b571631, rgba(15, 23, 42, .06)),
            0 15px 32px var(--sari-dark-4fc908fafe, rgba(15, 23, 42, .065)),
            inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255, 255, 255, .90));
        --rp-seller-shadow-focal:
            0 3px 7px var(--sari-dark-fbd3b3b9f6, rgba(15, 23, 42, .055)),
            0 14px 32px var(--sari-dark-a2f72dee3d, rgba(15, 23, 42, .095)),
            0 30px 68px var(--sari-dark-71f44bdb16, rgba(15, 23, 42, .11));

        max-width: 1440px !important;
        padding-bottom: 24px !important;
        color: var(--rp-seller-ink) !important;
    }

    /* Open Seller-style document header. */
    .reports-page .rp-header {
        min-height: 66px !important;
        align-items: flex-end !important;
        gap: 18px !important;
        margin: 0 0 12px !important;
        padding: 2px 2px 13px !important;
        border-bottom: 1px solid var(--rp-seller-divider) !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .reports-page .rp-title-wrap {
        min-width: 0 !important;
        align-items: flex-start !important;
        gap: 0 !important;
    }

    .reports-page .rp-title-icon {
        display: none !important;
    }

    .reports-page .rp-eyebrow {
        margin: 0 !important;
        color: var(--sari-dark-a63aa16d7c, #9A6F23) !important;
        font-size: 8px !important;
        font-weight: 800 !important;
        line-height: 1.15 !important;
        letter-spacing: .16em !important;
        text-transform: uppercase !important;
    }

    .reports-page .rp-title {
        margin: 5px 0 0 !important;
        color: var(--rp-seller-ink) !important;
        font-size: clamp(29px, 2.2vw, 36px) !important;
        font-weight: 650 !important;
        line-height: 1 !important;
        letter-spacing: -.035em !important;
    }

    .reports-page .rp-title-platform {
        color: var(--rp-seller-ink) !important;
    }

    .reports-page .rp-title-gold {
        color: var(--rp-seller-gold) !important;
    }

    .reports-page .rp-subtitle {
        max-width: 760px !important;
        margin-top: 7px !important;
        color: var(--rp-seller-muted) !important;
        font-size: 10px !important;
        font-weight: 400 !important;
        line-height: 1.6 !important;
    }

    .reports-page .rp-actions,
    .reports-page .rp-filter-form {
        align-items: center !important;
    }

    .reports-page .rp-actions {
        justify-content: flex-end !important;
        gap: 8px !important;
        flex: 0 0 auto !important;
    }

    /* Header controls use the same compact Seller control language. */
    .reports-page .rp-date-range-trigger,
    .reports-page .rp-btn {
        min-height: 38px !important;
        height: 38px !important;
        border-radius: 9px !important;
        font-family: 'Poppins', sans-serif !important;
    }

    .reports-page .rp-date-range-trigger {
        min-width: 242px !important;
        border-color: var(--rp-seller-border) !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        padding: 0 10px !important;
        color: var(--rp-seller-text) !important;
        box-shadow: none !important;
    }

    .reports-page .rp-date-range-trigger:hover,
    .reports-page .rp-range-picker.is-open .rp-date-range-trigger,
    .reports-page .rp-date-range-trigger:focus-visible {
        outline: none !important;
        border-color: var(--rp-seller-gold) !important;
        box-shadow: 0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(213, 150, 23, .08)) !important;
    }

    .reports-page .rp-btn {
        padding: 0 11px !important;
        font-size: 8.5px !important;
        box-shadow: none !important;
        transform: none !important;
    }

    .reports-page .rp-btn-primary {
        border-color: var(--rp-seller-gold) !important;
        background: var(--rp-seller-gold) !important;
        color: #fff !important;
        box-shadow: 0 4px 10px var(--sari-dark-71f44bdb16, rgba(213, 150, 23, .11)) !important;
    }

    .reports-page .rp-btn-primary:hover,
    .reports-page .rp-btn-primary:focus-visible {
        outline: none !important;
        border-color: var(--rp-seller-gold-strong) !important;
        background: var(--rp-seller-gold-strong) !important;
        transform: none !important;
    }

    .reports-page .rp-btn-secondary,
    .reports-page .rp-btn-link {
        border-color: var(--rp-seller-border) !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        color: var(--sari-dark-a63aa16d7c, #667085) !important;
    }

    .reports-page .rp-btn-secondary:hover,
    .reports-page .rp-btn-secondary:focus-visible,
    .reports-page .rp-btn-link:hover,
    .reports-page .rp-btn-link:focus-visible {
        outline: none !important;
        border-color: var(--rp-seller-border-strong) !important;
        background: var(--sari-dark-950baec030, #F9FAFB) !important;
        color: var(--sari-dark-e23e64dd1e, #344054) !important;
        transform: none !important;
    }

    /* Major report surfaces carry depth; structural/nested surfaces stay flat. */
    .reports-page .rp-panel,
    .reports-page .rp-mini-panel,
    .reports-page .rp-ledger {
        border: 1px solid var(--rp-seller-border) !important;
        border-radius: 14px !important;
        background: var(--rp-seller-surface) !important;
        box-shadow: var(--rp-seller-shadow-main) !important;
    }

    .reports-page .rp-summary-grid .rp-card {
        border: 1px solid var(--rp-seller-border) !important;
        border-radius: 14px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow: var(--rp-seller-shadow-soft) !important;
    }

    .reports-page .rp-stat,
    .reports-page .rp-stat:hover,
    .reports-page .rp-stat:focus-within {
        border-color: var(--rp-seller-border) !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow: var(--rp-seller-shadow-soft) !important;
        transform: none !important;
    }

    .reports-page .rp-stat-label {
        color: var(--sari-dark-a63aa16d7c, #7C8592) !important;
    }

    .reports-page .rp-stat-value {
        color: var(--rp-seller-ink) !important;
    }

    .reports-page .rp-stat-help {
        color: var(--sari-dark-a63aa16d7c, #98A2B3) !important;
    }

    /* Keep panel heads and data visualization internals document-flat. */
    .reports-page .rp-panel-head,
    .reports-page .rp-section-head,
    .reports-page .rp-calendar-panel,
    .reports-page .rp-calendar-selection {
        box-shadow: none !important;
    }

    .reports-page .rp-panel-title {
        color: var(--sari-dark-e23e64dd1e, #1D2939) !important;
    }

    .reports-page .rp-panel-copy {
        color: var(--sari-dark-a63aa16d7c, #8A94A3) !important;
    }

    .reports-page .rp-period-chip {
        border-color: var(--rp-seller-border) !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        color: var(--sari-dark-a63aa16d7c, #667085) !important;
        box-shadow: none !important;
    }

    /* Calendar is an interactive overlay, so it receives focal depth. */
    .reports-page .rp-calendar-popover {
        border-color: var(--rp-seller-border) !important;
        border-radius: 14px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow: var(--rp-seller-shadow-focal) !important;
    }

    .reports-page .rp-calendar-panel {
        border-color: var(--rp-seller-border) !important;
        border-radius: 10px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
    }

    .reports-page .rp-cal-select-trigger,
    .reports-page .rp-calendar-nav,
    .reports-page .rp-calendar-ghost {
        border-color: var(--rp-seller-border) !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        color: var(--sari-dark-a63aa16d7c, #667085) !important;
        box-shadow: none !important;
    }

    .reports-page .rp-cal-select-trigger:hover,
    .reports-page .rp-cal-select.is-open .rp-cal-select-trigger,
    .reports-page .rp-calendar-nav:hover:not(:disabled),
    .reports-page .rp-calendar-ghost:hover,
    .reports-page .rp-calendar-ghost:focus-visible {
        outline: none !important;
        border-color: #D8B56A !important;
        background: var(--sari-dark-950baec030, #FFF9EF) !important;
        color: var(--sari-dark-af18afcaa4, #94620A) !important;
    }

    .reports-page .rp-cal-select-menu {
        border-color: var(--rp-seller-border) !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow: 0 14px 32px var(--sari-dark-3b2338cbf0, rgba(15, 23, 42, .12)) !important;
    }

    .reports-page .rp-cal-select-option:hover,
    .reports-page .rp-cal-select-option:focus-visible,
    .reports-page .rp-cal-select-option.is-selected,
    .reports-page .rp-calendar-day:hover:not(:disabled),
    .reports-page .rp-calendar-day.is-in-range {
        background: var(--sari-dark-950baec030, #FFF9EF) !important;
        color: var(--sari-dark-af18afcaa4, #94620A) !important;
    }

    .reports-page .rp-calendar-day.is-selected,
    .reports-page .rp-calendar-apply {
        border-color: var(--rp-seller-gold) !important;
        background: var(--rp-seller-gold) !important;
        color: #fff !important;
        box-shadow: none !important;
    }

    .reports-page .rp-calendar-apply:hover,
    .reports-page .rp-calendar-apply:focus-visible {
        outline: none !important;
        border-color: var(--rp-seller-gold-strong) !important;
        background: var(--rp-seller-gold-strong) !important;
    }

    .reports-page .rp-calendar-selection {
        border-color: var(--rp-seller-border) !important;
        background: var(--sari-dark-950baec030, #F9FAFB) !important;
    }

    /* Charts and tables remain visually quiet inside the elevated parent. */
    .reports-page .rp-chart-tooltip,
    .reports-page .rp-bar-tip {
        border-color: var(--rp-seller-border) !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow: 0 8px 20px var(--sari-dark-8eadf403a5, rgba(15, 23, 42, .10)) !important;
    }

    .reports-page .rp-ledger {
        overflow: hidden !important;
    }

    .reports-page .rp-section-head {
        border-bottom-color: var(--rp-seller-divider) !important;
        background: var(--sari-dark-950baec030, #fff) !important;
    }

    .reports-page .rp-table thead {
        background: var(--sari-dark-950baec030, #F8FAFC) !important;
    }

    .reports-page .rp-table th {
        border-bottom-color: var(--rp-seller-divider) !important;
        color: var(--sari-dark-a63aa16d7c, #7C8592) !important;
    }

    .reports-page .rp-table td {
        border-bottom-color: var(--sari-dark-d8c2716225, #EEF1F4) !important;
        color: var(--sari-dark-e23e64dd1e, #475467) !important;
    }

    .reports-page .rp-table tbody tr:hover {
        background: var(--sari-dark-950baec030, #FCFCFD) !important;
    }

    .reports-page .rp-order,
    .reports-page .rp-money {
        color: var(--sari-dark-e23e64dd1e, #1D2939) !important;
    }

    .reports-page .rp-error {
        border-color: var(--sari-dark-d8c2716225, #F3D3CE) !important;
        background: var(--sari-dark-950baec030, #FFF7F5) !important;
        box-shadow: var(--rp-seller-shadow-soft) !important;
    }

    /* No structural lift/bounce on report cards or controls. */
    .reports-page .rp-card:hover,
    .reports-page .rp-panel:hover,
    .reports-page .rp-mini-panel:hover,
    .reports-page .rp-ledger:hover,
    .reports-page .rp-btn:hover,
    .reports-page .rp-date-range-trigger:hover {
        transform: none !important;
    }

    @media (max-width: 850px) {
        .reports-page .rp-header {
            min-height: 0 !important;
            align-items: flex-start !important;
            flex-direction: column !important;
            gap: 12px !important;
            padding: 0 0 13px !important;
        }

        .reports-page .rp-title {
            font-size: clamp(28px, 8vw, 34px) !important;
        }

        .reports-page .rp-subtitle {
            max-width: none !important;
            font-size: 10px !important;
        }

        .reports-page .rp-actions,
        .reports-page .rp-filter-form {
            width: 100% !important;
            justify-content: flex-start !important;
        }

        .reports-page .rp-range-picker,
        .reports-page .rp-date-range-trigger {
            width: 100% !important;
            min-width: 0 !important;
        }
    }

    @media (max-width: 560px) {
        .reports-page .rp-btn,
        .reports-page .rp-date-range-trigger {
            min-height: 42px !important;
            height: 42px !important;
        }

        .reports-page .rp-calendar-popover {
            border-radius: 13px !important;
        }
    }



    /* ============================================================
       ADMIN USERS UI PARITY — PLATFORM REPORTS
       Visual-only authority layer.
       Preserves report data, routes, date-range picker, exports,
       charts, IDs, Blade bindings, and JavaScript behavior.
       ============================================================ */

    .reports-page {
        --users-gold: #D99500;
        --users-gold-dark: #B97805;
        --users-ink: var(--sari-dark-e23e64dd1e, #111827);
        --users-text: var(--sari-dark-e23e64dd1e, #344054);
        --users-muted: var(--sari-dark-a63aa16d7c, #7C8592);
        --users-border: var(--sari-dark-d8c2716225, #E5E7EB);
        --users-divider: var(--sari-dark-d8c2716225, #EEF0F3);
        --users-head: #F8FAFC;
        --users-row-hover: #FAFBFC;
        --users-shadow:
            0 2px 5px var(--sari-dark-1190f58155, rgba(15, 23, 42, .045)),
            0 10px 24px var(--sari-dark-e07575a086, rgba(15, 23, 42, .075)),
            0 24px 54px var(--sari-dark-b12a5e9552, rgba(15, 23, 42, .09)),
            inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255,255,255,.92));
        --users-summary-shadow:
            0 1px 3px var(--sari-dark-7593018246, rgba(15, 23, 42, .035)),
            0 7px 18px var(--sari-dark-fbd3b3b9f6, rgba(15, 23, 42, .055)),
            0 15px 32px var(--sari-dark-361b571631, rgba(15, 23, 42, .06)),
            inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255,255,255,.90));

        width: 100% !important;
        max-width: 1880px !important;
        padding-bottom: 30px !important;
        color: var(--users-ink) !important;
    }

    /* ---------------- OPEN ADMIN HEADER ---------------- */
    .reports-page .rp-header {
        align-items: flex-end !important;
        gap: 22px !important;
        margin: 0 0 16px !important;
        padding: 2px 2px 17px !important;
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #E7E9EC) !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .reports-page .rp-title-wrap {
        display: block !important;
        min-width: 0 !important;
    }

    .reports-page .rp-title-icon {
        display: none !important;
    }

    .reports-page .rp-eyebrow {
        margin: 0 0 5px !important;
        color: var(--sari-dark-a63aa16d7c, #9A6F23) !important;
        font-size: 8px !important;
        font-weight: 800 !important;
        line-height: 1.15 !important;
        letter-spacing: .16em !important;
    }

    .reports-page .rp-title {
        margin: 0 !important;
        color: var(--users-ink) !important;
        font-size: clamp(29px, 2.1vw, 36px) !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        letter-spacing: -.035em !important;
    }

    .reports-page .rp-title-platform { color: var(--users-ink) !important; }
    .reports-page .rp-title-gold { color: var(--users-gold) !important; }

    .reports-page .rp-subtitle {
        max-width: 920px !important;
        margin: 7px 0 0 !important;
        color: var(--users-muted) !important;
        font-size: 10.5px !important;
        font-weight: 400 !important;
        line-height: 1.6 !important;
    }

    .reports-page .rp-actions,
    .reports-page .rp-filter-form {
        align-items: center !important;
    }

    .reports-page .rp-actions {
        gap: 8px !important;
        flex: 0 0 auto !important;
    }

    .reports-page .rp-date-range-trigger,
    .reports-page .rp-btn {
        min-height: 40px !important;
        height: 40px !important;
        border-radius: 10px !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 9px !important;
        box-shadow: none !important;
        transform: none !important;
    }

    .reports-page .rp-date-range-trigger {
        min-width: 250px !important;
        border-color: var(--users-border) !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        color: var(--users-text) !important;
    }

    .reports-page .rp-date-range-trigger:hover,
    .reports-page .rp-date-range-trigger:focus-visible,
    .reports-page .rp-range-picker.is-open .rp-date-range-trigger {
        outline: none !important;
        border-color: var(--users-gold) !important;
        box-shadow: 0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(217,149,0,.08)) !important;
    }

    .reports-page .rp-btn-primary {
        border-color: var(--users-gold) !important;
        background: var(--users-gold) !important;
        color: #FFFFFF !important;
        box-shadow: 0 5px 14px var(--sari-dark-3e32d50f5e, rgba(217,149,0,.13)) !important;
    }

    .reports-page .rp-btn-primary:hover,
    .reports-page .rp-btn-primary:focus-visible {
        border-color: var(--users-gold-dark) !important;
        background: var(--users-gold-dark) !important;
    }

    .reports-page .rp-btn-secondary,
    .reports-page .rp-btn-link {
        border-color: var(--users-border) !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        color: var(--sari-dark-a63aa16d7c, #667085) !important;
    }

    /* ---------------- USERS-PAGE KPI CARDS ---------------- */
    .reports-page .rp-summary-grid {
        width: 100% !important;
        gap: 12px !important;
        margin-top: 0 !important;
    }

    .reports-page .rp-summary-grid .rp-card,
    .reports-page .rp-stat,
    .reports-page .rp-stat:hover,
    .reports-page .rp-stat:focus-within {
        min-height: 110px !important;
        border: 1px solid var(--users-border) !important;
        border-radius: 18px !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        box-shadow: var(--users-summary-shadow) !important;
        transform: none !important;
    }

    .reports-page .rp-stat {
        position: relative !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: flex-start !important;
        padding: 18px 58px 16px 18px !important;
        overflow: hidden !important;
    }

    .reports-page .rp-stat:hover {
        border-color: var(--sari-dark-d8c2716225, #D8DDE5) !important;
        transform: translateY(-2px) !important;
    }

    /* Final Users-page icon treatment: no tile, no box, no circle. */
    .reports-page .rp-stat-icon,
    .reports-page .rp-stat.blue .rp-stat-icon,
    .reports-page .rp-stat.green .rp-stat-icon,
    .reports-page .rp-stat.violet .rp-stat-icon,
    .reports-page .rp-stat.orange .rp-stat-icon,
    .reports-page .rp-stat.teal .rp-stat-icon,
    .reports-page .rp-stat.red .rp-stat-icon,
    .reports-page .rp-stat.purple .rp-stat-icon,
    .reports-page .rp-stat.amber .rp-stat-icon {
        position: absolute !important;
        top: 16px !important;
        right: 16px !important;
        display: block !important;
        width: 24px !important;
        height: 24px !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        pointer-events: none !important;
    }

    .reports-page .rp-stat-icon svg {
        display: block !important;
        width: 24px !important;
        height: 24px !important;
        stroke-width: 1.8 !important;
        stroke-linecap: round !important;
        stroke-linejoin: round !important;
    }

    .reports-page .rp-stat.blue .rp-stat-icon   { color: var(--sari-dark-8f882a075f, #2563EB) !important; }
    .reports-page .rp-stat.green .rp-stat-icon  { color: var(--sari-dark-bc794282b4, #16A34A) !important; }
    .reports-page .rp-stat.violet .rp-stat-icon { color: var(--sari-dark-904a7bfa5a, #7C3AED) !important; }
    .reports-page .rp-stat.orange .rp-stat-icon { color: var(--sari-dark-50c81cae80, #EA580C) !important; }
    .reports-page .rp-stat.teal .rp-stat-icon   { color: var(--sari-dark-7c676506a0, #0F766E) !important; }
    .reports-page .rp-stat.red .rp-stat-icon    { color: var(--sari-dark-0c8ad229f5, #DC2626) !important; }
    .reports-page .rp-stat.purple .rp-stat-icon { color: var(--sari-dark-837c9344c1, #9333EA) !important; }
    .reports-page .rp-stat.amber .rp-stat-icon  { color: var(--users-gold) !important; }

    .reports-page .rp-stat-label {
        min-height: 0 !important;
        margin: 0 !important;
        color: var(--sari-dark-a63aa16d7c, #7C8592) !important;
        font-size: 11.5px !important;
        font-weight: 500 !important;
        line-height: 1.35 !important;
    }

    .reports-page .rp-stat-value {
        margin-top: 7px !important;
        color: var(--users-ink) !important;
        font-size: 26px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        letter-spacing: -.04em !important;
    }

    .reports-page .rp-stat-footer {
        display: flex !important;
        min-height: 20px !important;
        align-items: center !important;
        gap: 7px !important;
        margin-top: auto !important;
        padding-top: 10px !important;
        flex: 0 0 auto !important;
    }

    .reports-page .rp-delta {
        min-height: 20px !important;
        padding: 0 7px !important;
        font-size: 7.5px !important;
        font-weight: 700 !important;
    }

    .reports-page .rp-stat-help {
        color: var(--sari-dark-a63aa16d7c, #98A2B3) !important;
        font-size: 9px !important;
        line-height: 1.35 !important;
    }

    /* ---------------- REPORT SURFACES ---------------- */
    .reports-page .rp-panel,
    .reports-page .rp-mini-panel,
    .reports-page .rp-ledger {
        border: 1px solid var(--users-border) !important;
        border-radius: 18px !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        box-shadow: var(--users-shadow) !important;
    }

    .reports-page .rp-main-grid,
    .reports-page .rp-secondary-grid {
        gap: 14px !important;
        margin-top: 14px !important;
    }

    .reports-page .rp-panel,
    .reports-page .rp-mini-panel {
        padding: 18px !important;
    }

    .reports-page .rp-panel-title {
        color: var(--sari-dark-e23e64dd1e, #1F2937) !important;
        font-size: 12.5px !important;
        font-weight: 700 !important;
    }

    .reports-page .rp-panel-copy {
        margin-top: 4px !important;
        color: var(--sari-dark-a63aa16d7c, #98A2B3) !important;
        font-size: 8.5px !important;
        line-height: 1.5 !important;
    }

    .reports-page .rp-period-chip {
        min-height: 28px !important;
        border-color: var(--users-border) !important;
        border-radius: 8px !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        padding: 0 9px !important;
        color: var(--sari-dark-a63aa16d7c, #667085) !important;
        font-size: 8px !important;
        box-shadow: none !important;
    }

    /* ---------------- USERS-PAGE TABLE LANGUAGE ---------------- */
    .reports-page .rp-ledger {
        margin-top: 14px !important;
        overflow: hidden !important;
    }

    .reports-page .rp-section-head {
        min-height: 60px !important;
        padding: 13px 16px !important;
        border-bottom: 1px solid var(--users-divider) !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        box-shadow: none !important;
    }

    .reports-page .rp-table-wrap {
        overflow-x: auto !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
    }

    .reports-page .rp-table {
        min-width: 1060px !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
    }

    .reports-page .rp-table thead {
        background: var(--users-head) !important;
    }

    .reports-page .rp-table th {
        padding: 13px 16px !important;
        border-bottom: 1px solid var(--users-divider) !important;
        background: var(--users-head) !important;
        color: var(--sari-dark-a63aa16d7c, #667085) !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        line-height: 1.25 !important;
        letter-spacing: .055em !important;
        text-transform: uppercase !important;
        white-space: nowrap !important;
    }

    .reports-page .rp-table td {
        min-height: 58px !important;
        padding: 13px 16px !important;
        border-bottom: 1px solid var(--users-divider) !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        color: var(--sari-dark-e23e64dd1e, #475467) !important;
        font-size: 10.5px !important;
        line-height: 1.45 !important;
        vertical-align: middle !important;
    }

    .reports-page .rp-table tbody tr:hover td {
        background: var(--users-row-hover) !important;
    }

    .reports-page .rp-order,
    .reports-page .rp-money {
        color: var(--sari-dark-e23e64dd1e, #1F2937) !important;
        font-size: 11px !important;
        font-weight: 600 !important;
    }

    .reports-page .rp-status {
        min-height: 24px !important;
        gap: 5px !important;
        border-radius: 999px !important;
        padding: 0 8px !important;
        font-size: 8.5px !important;
        font-weight: 600 !important;
    }

    .reports-page .rp-status::before {
        width: 5px !important;
        height: 5px !important;
    }

    .reports-page .rp-empty {
        min-height: 110px !important;
        color: var(--sari-dark-a63aa16d7c, #98A2B3) !important;
        font-size: 9.5px !important;
    }

    /* Keep the existing calendar behavior, but visually align it. */
    .reports-page .rp-calendar-popover {
        border-color: var(--users-border) !important;
        border-radius: 14px !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        box-shadow:
            0 10px 24px var(--sari-dark-b12a5e9552, rgba(15,23,42,.09)),
            0 26px 60px var(--sari-dark-3b2338cbf0, rgba(15,23,42,.12)) !important;
    }

    .reports-page .rp-calendar-panel,
    .reports-page .rp-calendar-selection,
    .reports-page .rp-cal-select-trigger,
    .reports-page .rp-calendar-nav,
    .reports-page .rp-calendar-ghost {
        border-color: var(--users-border) !important;
        box-shadow: none !important;
    }

    @media (min-width: 1450px) {
        .reports-page .rp-summary-grid {
            grid-template-columns: repeat(8, minmax(0, 1fr)) !important;
        }
    }

    @media (min-width: 1101px) and (max-width: 1449px) {
        .reports-page .rp-summary-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 1100px) {
        .reports-page .rp-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 850px) {
        .reports-page .rp-header {
            align-items: flex-start !important;
            flex-direction: column !important;
            gap: 12px !important;
            padding-bottom: 14px !important;
        }

        .reports-page .rp-actions,
        .reports-page .rp-filter-form,
        .reports-page .rp-range-picker,
        .reports-page .rp-date-range-trigger {
            width: 100% !important;
        }

        .reports-page .rp-date-range-trigger {
            min-width: 0 !important;
        }
    }

    @media (max-width: 640px) {
        .reports-page .rp-summary-grid {
            grid-template-columns: 1fr !important;
        }

        .reports-page .rp-stat {
            min-height: 102px !important;
        }

        .reports-page .rp-panel,
        .reports-page .rp-mini-panel {
            padding: 14px !important;
        }
    }


    /* ============================================================
       BUYER CATALOG / COMMISSIONS VISUAL PARITY — REPORTS
       Scoped presentation only; report bindings remain unchanged.
       ============================================================ */
    .reports-page {
        --rp-brand: #c88912;
        --rp-brand-strong: #a96d08;
        --rp-seller-gold: #c88912;
        --rp-seller-gold-strong: #a96d08;
        --users-gold: #c88912;
        --users-gold-dark: #a96d08;
        --rp-surface-shadow: 0 2px 5px var(--sari-dark-1190f58155, rgba(15,23,42,.045)), 0 10px 24px var(--sari-dark-e07575a086, rgba(15,23,42,.075)), 0 24px 54px var(--sari-dark-a2f72dee3d, rgba(15,23,42,.095)), inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255,255,255,.92));
        --rp-surface-shadow-mobile: 0 2px 4px var(--sari-dark-7593018246, rgba(15,23,42,.035)), 0 8px 18px var(--sari-dark-361b571631, rgba(15,23,42,.06)), 0 18px 38px var(--sari-dark-7c935c8886, rgba(15,23,42,.07));
        width: 100% !important;
        max-width: 1640px !important;
        min-width: 0;
        padding: 2px 2px 32px !important;
        color: var(--sari-dark-e23e64dd1e, #29323b) !important;
        font-size: 12px;
        line-height: 1.5;
        container-type: inline-size;
    }
    .reports-page :is(button,input,select) { font-family: inherit; }
    .reports-page .rp-header {
        align-items: flex-end !important;
        gap: 20px !important;
        margin-bottom: 0 !important;
        padding: 2px 0 16px !important;
        border-bottom: 0 !important;
    }
    .reports-page .rp-eyebrow { margin: 0 !important; color: var(--sari-dark-685eb4fc40, #a87316) !important; font-size: 10.5px !important; font-weight: 800 !important; line-height: 1.2 !important; letter-spacing: .14em !important; }
    .reports-page .rp-title { margin-top: 5px !important; font-size: clamp(29px,2.35vw,34px) !important; font-weight: 750 !important; line-height: 1.04 !important; letter-spacing: -.035em !important; }
    .reports-page .rp-title-platform { color: var(--sari-dark-e23e64dd1e, #2e343b) !important; }
    .reports-page .rp-title-gold { color: var(--sari-dark-19b301f69a, #c88912) !important; }
    .reports-page .rp-subtitle { max-width: 760px !important; margin-top: 7px !important; color: var(--sari-dark-a63aa16d7c, #707b86) !important; font-size: 11.5px !important; line-height: 1.55 !important; }
    .reports-page .rp-actions { gap: 10px !important; min-width: 0; }
    .reports-page .rp-filter-form { gap: 8px; min-width: 0; }
    .reports-page :is(.rp-btn,.rp-date-range-trigger) { min-height: 42px !important; height: 42px !important; border-radius: 9px !important; font-size: 11px !important; font-weight: 650; }
    .reports-page .rp-btn { padding-inline: 13px !important; white-space: nowrap; }
    .reports-page .rp-btn svg { width: 15px !important; height: 15px !important; flex-shrink: 0; }
    .reports-page .rp-btn-primary { background: #c88912 !important; border-color: #c88912 !important; box-shadow: 0 6px 16px var(--sari-dark-59ed29fe9d, rgba(201,132,8,.14)) !important; }
    .reports-page .rp-btn-primary:hover { background: #a96d08 !important; border-color: #a96d08 !important; }
    .reports-page .rp-btn-link { border-color: transparent !important; background: transparent !important; color: var(--sari-dark-9b2ca2d764, #865709) !important; box-shadow: none !important; }
    .reports-page .rp-btn-link:hover { color: var(--sari-dark-19b301f69a, #c88912) !important; }
    .reports-page .rp-date-range-trigger { min-width: 270px !important; padding-inline: 12px !important; border-color: var(--sari-dark-d8c2716225, #dfe3e8) !important; background: var(--sari-dark-950baec030, #fff) !important; }
    .reports-page .rp-date-range-copy small { color: var(--sari-dark-a63aa16d7c, #707b86); font-size: 9px !important; letter-spacing: .045em; }
    .reports-page .rp-date-range-copy strong { color: var(--sari-dark-e23e64dd1e, #333d47); font-size: 11.5px !important; }
    .reports-page .rp-date-range-icon { width: 16px !important; height: 16px !important; flex-basis: 16px !important; color: var(--sari-dark-a63aa16d7c, #596470); }
    .reports-page .rp-date-range-chevron { width: 14px; height: 14px; flex-basis: 14px; color: var(--sari-dark-a63aa16d7c, #596470); }
    /* Match the reference's elevation on primary containers. */
    .reports-page :is(.rp-card,.rp-panel,.rp-mini-panel,.rp-ledger),
    .reports-page .rp-summary-grid .rp-card,
    .reports-page :is(.rp-stat:hover,.rp-stat:focus-within) {
        min-width: 0;
        border: 1px solid var(--sari-dark-d8c2716225, #e5e7eb) !important;
        border-radius: 17px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow: var(--rp-surface-shadow) !important;
        transform: none !important;
    }
    .reports-page :is(.rp-summary-grid,.rp-main-grid,.rp-secondary-grid) { gap: 18px !important; margin-top: 18px !important; }
    .reports-page .rp-summary-grid { grid-template-columns: repeat(4,minmax(0,1fr)) !important; }
    .reports-page .rp-stat { min-height: 110px !important; padding: 17px 52px 16px 18px !important; contain: none; }
    .reports-page .rp-stat-label { min-height: 0 !important; margin: 0 !important; color: var(--sari-dark-a63aa16d7c, #707b86) !important; font-size: 11.5px !important; line-height: 1.35 !important; }
    .reports-page .rp-stat-value { margin-top: 7px !important; color: var(--sari-dark-e23e64dd1e, #29323b) !important; font-size: clamp(22px,1.6vw,26px) !important; line-height: 1.2 !important; white-space: normal !important; overflow-wrap: anywhere; font-variant-numeric: tabular-nums; }
    .reports-page .rp-stat-footer { gap: 7px !important; flex-wrap: wrap; padding-top: 9px !important; }
    .reports-page .rp-delta { min-height: 23px !important; font-size: 9px !important; }
    .reports-page .rp-stat-help { max-width: none !important; color: var(--sari-dark-a63aa16d7c, #707b86) !important; font-size: 10.5px !important; line-height: 1.4 !important; white-space: normal !important; overflow: visible !important; }
    .reports-page .rp-summary-grid .rp-stat .rp-stat-icon { top: 17px !important; right: 15px !important; width: 26px !important; height: 26px !important; }
    .reports-page .rp-summary-grid .rp-stat-icon svg { width: 22px !important; height: 22px !important; }
    .reports-page .rp-main-grid { grid-template-columns: minmax(0,1.5fr) minmax(350px,1fr) !important; }
    .reports-page .rp-secondary-grid { grid-template-columns: repeat(2,minmax(0,1fr)) !important; }
    .reports-page :is(.rp-panel,.rp-mini-panel) { padding: 18px !important; }
    .reports-page .rp-panel { min-height: 320px !important; }
    .reports-page .rp-mini-panel { min-height: 255px !important; }
    .reports-page .rp-panel-head { flex-wrap: wrap; gap: 10px !important; }
    .reports-page .rp-panel-title { color: var(--sari-dark-e23e64dd1e, #29323b) !important; font-size: 14px !important; font-weight: 750 !important; line-height: 1.35 !important; }
    .reports-page .rp-panel-copy { margin-top: 4px !important; color: var(--sari-dark-a63aa16d7c, #707b86) !important; font-size: 10.5px !important; line-height: 1.55 !important; }
    .reports-page .rp-period-chip { min-height: 30px !important; font-size: 10px !important; padding-inline: 9px !important; }
    .reports-page :is(.rp-chart-wrap,.rp-chart-svg) { height: 235px !important; }
    .reports-page .rp-chart-wrap { margin-top: 14px !important; }
    .reports-page :is(.rp-chart-tooltip small,.rp-chart-tooltip span) { color: var(--sari-dark-a63aa16d7c, #707b86); font-size: 10.5px !important; }
    .reports-page .rp-chart-tooltip strong { font-size: 12px !important; }
    .reports-page .rp-donut-layout { grid-template-columns: 124px minmax(0,1fr) !important; gap: 16px !important; margin-top: 20px !important; }
    .reports-page .rp-donut { width: 118px !important; height: 118px !important; }
    .reports-page .rp-donut::before { width: 82px !important; height: 82px !important; }
    .reports-page .rp-donut-total { font-size: 22px !important; }
    .reports-page .rp-donut-caption { font-size: 10px !important; }
    .reports-page .rp-legend { gap: 9px !important; }
    .reports-page .rp-legend-row { grid-template-columns: 8px minmax(0,1fr) 36px 44px !important; gap: 7px !important; color: var(--sari-dark-a63aa16d7c, #707b86); font-size: 11px !important; }
    .reports-page .rp-bars { height: 150px !important; margin-top: 16px !important; }
    .reports-page .rp-axis { margin-top: 8px !important; color: var(--sari-dark-a63aa16d7c, #707b86); font-size: 10px !important; }
    .reports-page .rp-registration-legend { margin-top: 12px !important; color: var(--sari-dark-a63aa16d7c, #707b86); font-size: 10.5px !important; }
    .reports-page .rp-bar-tip { padding: 7px 9px; font-size: 10.5px !important; }
    /* Readable calendar controls and dropdown options. */
    .reports-page .rp-calendar-popover {
        width: min(680px,calc(100vw - 48px));
        max-height: min(680px,calc(100dvh - 100px));
        overflow-y: auto;
        padding: 14px;
        border-color: var(--sari-dark-d8c2716225, #e1e5e9) !important;
        border-radius: 17px !important;
        box-shadow: 0 18px 42px var(--sari-dark-3e32d50f5e, rgba(31,41,55,.13)),0 4px 12px var(--sari-dark-25b2da5e49, rgba(31,41,55,.04)) !important;
        scrollbar-width: thin;
        scrollbar-color: #d9dde2 transparent;
    }
    .reports-page .rp-calendar-dual { gap: 12px; }
    .reports-page .rp-calendar-panel { padding: 12px; }
    .reports-page .rp-calendar-panel-label { align-items: flex-start; flex-wrap: wrap; gap: 4px; }
    .reports-page .rp-calendar-panel-label span { font-size: 9.5px; }
    .reports-page .rp-calendar-panel-label strong { font-size: 10.5px; }
    .reports-page .rp-calendar-head { grid-template-columns: 32px minmax(0,1fr) 32px; gap: 5px; }
    .reports-page .rp-calendar-controls { grid-template-columns: minmax(0,1fr) 72px; }
    .reports-page .rp-calendar-nav { width: 32px; height: 34px; }
    .reports-page .rp-cal-select-trigger { height: 34px; padding-inline: 6px; font-size: 11px; }
    .reports-page .rp-cal-select-trigger span { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .reports-page .rp-cal-select-trigger svg { width: 13px; height: 13px; flex-basis: 13px; }
    .reports-page .rp-cal-select-menu { border-radius: 12px; padding: 5px; box-shadow: 0 18px 42px var(--sari-dark-3e32d50f5e, rgba(31,41,55,.13)),0 4px 12px var(--sari-dark-25b2da5e49, rgba(31,41,55,.04)) !important; }
    .reports-page .rp-cal-select-option { min-height: 36px; padding: 7px 9px; font-size: 11.5px; }
    .reports-page .rp-cal-select-option.is-selected::after { font-size: 11px; }
    .reports-page .rp-calendar-weekdays { font-size: 10px; }
    .reports-page .rp-calendar-day { min-width: 0; height: 34px; font-size: 11px; }
    .reports-page .rp-calendar-selection { flex-wrap: wrap; gap: 4px; padding: 10px; }
    .reports-page .rp-calendar-selection span { font-size: 10px; }
    .reports-page .rp-calendar-selection strong { font-size: 11px; }
    .reports-page :is(.rp-calendar-ghost,.rp-calendar-apply) { min-height: 38px; height: 38px; font-size: 11px; }
    /* Recent Orders table. */
    .reports-page .rp-ledger { margin-top: 18px !important; }
    .reports-page .rp-section-head { min-height: 72px !important; padding: 16px 18px !important; flex-wrap: wrap; gap: 12px; }
    .reports-page .rp-table-wrap { max-width: 100%; scrollbar-width: thin; scrollbar-color: #d9dde2 transparent; }
    .reports-page .rp-table { min-width: 1000px !important; }
    .reports-page .rp-table th { padding: 12px 14px !important; font-size: 10.5px !important; font-weight: 650 !important; letter-spacing: .045em !important; }
    .reports-page .rp-table td { padding: 14px !important; color: var(--sari-dark-e23e64dd1e, #4c545e) !important; font-size: 12px !important; line-height: 1.5 !important; }
    .reports-page .rp-table tbody tr { content-visibility: visible; contain-intrinsic-size: none; }
    .reports-page :is(.rp-order,.rp-money) { color: var(--sari-dark-e23e64dd1e, #29323b) !important; font-size: 12px !important; font-weight: 650 !important; font-variant-numeric: tabular-nums; }
    .reports-page .rp-status { min-height: 26px !important; padding: 3px 9px !important; font-size: 10.5px !important; }
    .reports-page .rp-empty { min-height: 130px !important; padding: 20px; color: var(--sari-dark-a63aa16d7c, #707b86) !important; font-size: 11.5px !important; }
    .reports-page .rp-error { padding: 12px 14px; font-size: 12px; }
    .reports-page :is(button,a,input,.rp-bar-item,.rp-reg-group):focus-visible { outline: 2px solid #9b6710 !important; outline-offset: 3px; }
    @container (max-width: 1250px) {
        .reports-page .rp-header { flex-direction: column !important; align-items: stretch !important; }
        .reports-page .rp-actions { width: 100% !important; justify-content: flex-start !important; flex: initial !important; }
        .reports-page .rp-main-grid { grid-template-columns: 1fr !important; }
    }
    @container (max-width: 900px) {
        .reports-page .rp-summary-grid { grid-template-columns: repeat(2,minmax(0,1fr)) !important; }
        .reports-page .rp-secondary-grid { grid-template-columns: 1fr !important; }
    }
    @container (max-width: 600px) {
        .reports-page .rp-filter-form { width: 100% !important; }
        .reports-page .rp-range-picker { width: 100%; flex: 1 1 100%; }
        .reports-page .rp-date-range-trigger { width: 100% !important; min-width: 0 !important; }
        .reports-page .rp-calendar-popover { left: 0; right: auto; width: min(100cqi,680px); max-width: calc(100vw - 40px); transform-origin: top left; }
        .reports-page .rp-calendar-dual { grid-template-columns: 1fr; }
        .reports-page .rp-donut-layout { grid-template-columns: 1fr !important; justify-items: center; }
        .reports-page .rp-legend { width: 100%; }
    }
    @container (max-width: 400px) {
        .reports-page .rp-summary-grid { grid-template-columns: 1fr !important; }
    }
    @media (max-width: 767px) {
        .reports-page .rp-title { font-size: 29px !important; }
        .reports-page .rp-eyebrow { font-size: 9.5px !important; }
        .reports-page :is(.rp-card,.rp-panel,.rp-mini-panel,.rp-ledger),
        .reports-page .rp-summary-grid .rp-card,
        .reports-page :is(.rp-stat:hover,.rp-stat:focus-within) { border-radius: 14px !important; box-shadow: var(--rp-surface-shadow-mobile) !important; }
        .reports-page :is(.rp-summary-grid,.rp-main-grid,.rp-secondary-grid) { gap: 14px !important; margin-top: 14px !important; }
        .reports-page :is(.rp-btn,.rp-date-range-trigger) { min-height: 44px !important; height: 44px !important; }
        .reports-page :is(.rp-panel,.rp-mini-panel) { padding: 14px !important; }
        .reports-page .rp-calendar-popover { border-radius: 14px !important; }
    }
    @media (prefers-reduced-motion: reduce) {
        .reports-page * { animation: none !important; transition: none !important; scroll-behavior: auto !important; }
        .reports-page .rp-stat:hover { transform: none !important; }
    }


    /* Eight KPI cards in one desktop row. */
    @container (min-width: 901px) {
        .reports-page .rp-summary-grid {
            grid-template-columns: repeat(8,minmax(0,1fr)) !important;
            gap: 10px !important;
        }
        .reports-page .rp-summary-grid .rp-stat {
            min-height: 132px !important;
            padding: 14px 12px !important;
        }
        .reports-page .rp-summary-grid .rp-stat-label {
            min-height: 30px !important;
            padding-right: 22px;
            font-size: 10.5px !important;
            line-height: 1.4 !important;
        }
        .reports-page .rp-summary-grid .rp-stat .rp-stat-icon {
            top: 14px !important;
            right: 10px !important;
            width: 18px !important;
            height: 18px !important;
        }
        .reports-page .rp-summary-grid .rp-stat-icon svg {
            width: 18px !important;
            height: 18px !important;
        }
        .reports-page .rp-summary-grid .rp-stat-value {
            font-size: clamp(18px,1.35vw,23px) !important;
            margin-top: 6px !important;
        }
        .reports-page .rp-summary-grid .rp-stat-footer {
            align-items: flex-start !important;
            flex-direction: column;
            gap: 5px !important;
            padding-top: 9px !important;
        }
        .reports-page .rp-summary-grid .rp-stat-help {
            flex: initial !important;
            font-size: 9.5px !important;
        }
        .reports-page .rp-summary-grid .rp-delta {
            min-height: 21px !important;
            padding-inline: 6px !important;
            font-size: 8.5px !important;
        }
    }
</style>

<div class="reports-page">
    <section class="rp-header">
        <div class="rp-title-wrap">
            <span class="rp-title-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 20V10"></path><path d="M10 20V4"></path><path d="M16 20v-7"></path><path d="M22 20H2"></path>
                </svg>
            </span>
            <div>
                <p class="rp-eyebrow">Reports</p>
                <h1 class="rp-title"><span class="rp-title-platform">Platform</span> <span class="rp-title-gold">Reports</span></h1>
                <p class="rp-subtitle">Enterprise reporting for orders, delivery, revenue, and registration activity using current database records.</p>
            </div>
        </div>

        <div class="rp-actions">
            <form method="GET" action="{{ route('admin.reports') }}" class="rp-filter-form" id="rpReportFilterForm">
                <div class="rp-range-picker" id="rpRangePicker">
                    <input type="hidden" name="from" id="rpFromDate" value="{{ $period['from_date'] }}">
                    <input type="hidden" name="to" id="rpToDate" value="{{ $period['to_date'] }}">

                    <button
                        type="button"
                        class="rp-date-range-trigger"
                        id="rpCalendarTrigger"
                        aria-haspopup="dialog"
                        aria-expanded="false"
                    >
                        <svg class="rp-date-range-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                            <path d="M16 3v4M8 3v4M3 10h18"></path>
                        </svg>
                        <span class="rp-date-range-copy">
                            <small>Report period</small>
                            <strong id="rpCalendarRangeLabel">{{ $period['from']->format('M j, Y') }} – {{ $period['to']->format('M j, Y') }}</strong>
                        </span>
                        <svg class="rp-date-range-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </button>

                    <div class="rp-calendar-popover" id="rpCalendarPopover" role="dialog" aria-label="Choose report date range" aria-hidden="true">
                        <div class="rp-calendar-dual">
                            <section class="rp-calendar-panel" data-calendar="start">
                                <div class="rp-calendar-panel-label">
                                    <span>Start date</span>
                                    <strong id="rpStartDateLabel">{{ $period['from']->format('M j, Y') }}</strong>
                                </div>

                                <div class="rp-calendar-head">
                                    <button type="button" class="rp-calendar-nav" id="rpStartPrev" aria-label="Previous start month">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"></path></svg>
                                    </button>

                                    <div class="rp-calendar-controls">
                                        <div class="rp-cal-select" id="rpStartMonthSelect">
                                            <button type="button" class="rp-cal-select-trigger" data-cal-select-trigger aria-haspopup="listbox" aria-expanded="false">
                                                <span data-cal-select-label>September</span>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 10 5 5 5-5"></path></svg>
                                            </button>
                                            <div class="rp-cal-select-menu" data-cal-select-menu role="listbox"></div>
                                        </div>

                                        <div class="rp-cal-select rp-cal-year-select" id="rpStartYearSelect">
                                            <button type="button" class="rp-cal-select-trigger" data-cal-select-trigger aria-haspopup="listbox" aria-expanded="false">
                                                <span data-cal-select-label>{{ $period['from']->format('Y') }}</span>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 10 5 5 5-5"></path></svg>
                                            </button>
                                            <div class="rp-cal-select-menu" data-cal-select-menu role="listbox"></div>
                                        </div>
                                    </div>

                                    <button type="button" class="rp-calendar-nav" id="rpStartNext" aria-label="Next start month">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg>
                                    </button>
                                </div>

                                <div class="rp-calendar-weekdays" aria-hidden="true">
                                    <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                                </div>

                                <div class="rp-calendar-grid" id="rpStartCalendarGrid"></div>
                            </section>

                            <section class="rp-calendar-panel" data-calendar="end">
                                <div class="rp-calendar-panel-label">
                                    <span>End date</span>
                                    <strong id="rpEndDateLabel">{{ $period['to']->format('M j, Y') }}</strong>
                                </div>

                                <div class="rp-calendar-head">
                                    <button type="button" class="rp-calendar-nav" id="rpEndPrev" aria-label="Previous end month">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"></path></svg>
                                    </button>

                                    <div class="rp-calendar-controls">
                                        <div class="rp-cal-select" id="rpEndMonthSelect">
                                            <button type="button" class="rp-cal-select-trigger" data-cal-select-trigger aria-haspopup="listbox" aria-expanded="false">
                                                <span data-cal-select-label>September</span>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 10 5 5 5-5"></path></svg>
                                            </button>
                                            <div class="rp-cal-select-menu" data-cal-select-menu role="listbox"></div>
                                        </div>

                                        <div class="rp-cal-select rp-cal-year-select" id="rpEndYearSelect">
                                            <button type="button" class="rp-cal-select-trigger" data-cal-select-trigger aria-haspopup="listbox" aria-expanded="false">
                                                <span data-cal-select-label>{{ $period['to']->format('Y') }}</span>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 10 5 5 5-5"></path></svg>
                                            </button>
                                            <div class="rp-cal-select-menu" data-cal-select-menu role="listbox"></div>
                                        </div>
                                    </div>

                                    <button type="button" class="rp-calendar-nav" id="rpEndNext" aria-label="Next end month">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg>
                                    </button>
                                </div>

                                <div class="rp-calendar-weekdays" aria-hidden="true">
                                    <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                                </div>

                                <div class="rp-calendar-grid" id="rpEndCalendarGrid"></div>
                            </section>
                        </div>

                        <div class="rp-calendar-selection">
                            <span>Selected range</span>
                            <strong id="rpCalendarSelectionLabel">{{ $period['from']->format('M j, Y') }} – {{ $period['to']->format('M j, Y') }}</strong>
                        </div>

                        <div class="rp-calendar-footer">
                            <button type="button" class="rp-calendar-ghost" id="rpCalendarToday">Use Today</button>
                            <button type="button" class="rp-calendar-apply" id="rpCalendarApply">Apply Dates</button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="rp-btn rp-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 6h16"></path><path d="M7 12h10"></path><path d="M10 18h4"></path></svg>
                    Apply
                </button>

                <a href="{{ route('admin.reports') }}" class="rp-btn rp-btn-link">Reset</a>
            </form>

            <button
                type="button"
                data-report-pdf-export
                data-export-base="{{ route('admin.reports.export') }}"
                class="rp-btn rp-btn-primary"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 20h14"></path></svg>
                Export PDF
            </button>
        </div>
    </section>

    @if($errors->any())
        <div class="rp-error">{{ $errors->first() }}</div>
    @endif

    <section class="rp-summary-grid">
        @foreach($summaryConfig as $card)
            @php
                $value = $stats[$card['key']] ?? 0;
                $delta = $comparisons[$card['key']] ?? null;
                $displayValue = in_array($card['key'], $currencyKeys, true)
                    ? '₱'.number_format((float) $value, 2)
                    : number_format((int) $value);
                $deltaGood = $delta !== null && ($card['inverse'] ? $delta < 0 : $delta > 0);
                $deltaBad = $delta !== null && ($card['inverse'] ? $delta > 0 : $delta < 0);
            @endphp
            <article class="rp-card rp-stat {{ $card['tone'] }}">
                <span class="rp-stat-icon" aria-hidden="true">
                    @switch($card['icon'])
                        @case('orders')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="20" r="1.4"></circle><circle cx="18" cy="20" r="1.4"></circle><path d="M3 4h2l2.5 10.5a1 1 0 0 0 1 .8H19a1 1 0 0 0 1-.8L22 7H7"></path></svg>
                            @break
                        @case('delivered')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h12v9H3z"></path><path d="M15 10h3l3 3v3h-6z"></path><circle cx="7.5" cy="18" r="1.5"></circle><circle cx="18" cy="18" r="1.5"></circle></svg>
                            @break
                        @case('gmv')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20V10"></path><path d="M10 20V4"></path><path d="M16 20v-7"></path><path d="M22 20H2"></path></svg>
                            @break
                        @case('fees')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><ellipse cx="12" cy="6" rx="5" ry="2.5"></ellipse><path d="M7 6v4c0 1.4 2.2 2.5 5 2.5s5-1.1 5-2.5V6"></path><path d="M7 10v4c0 1.4 2.2 2.5 5 2.5s5-1.1 5-2.5v-4"></path><path d="M7 14v4c0 1.4 2.2 2.5 5 2.5s5-1.1 5-2.5v-4"></path></svg>
                            @break
                        @case('active')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 12h4l2-5 4 10 2-5h4"></path></svg>
                            @break
                        @case('cancelled')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="m8 8 8 8"></path><path d="m16 8-8 8"></path></svg>
                            @break
                        @case('registrations')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3"></circle><path d="M4 20a5 5 0 0 1 10 0"></path><path d="M17 7v6"></path><path d="M14 10h6"></path></svg>
                            @break
                        @default
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
                    @endswitch
                </span>

                <div class="rp-stat-label">{{ $card['label'] }}</div>
                <div class="rp-stat-value">{{ $displayValue }}</div>
                <div class="rp-stat-footer">
                    @if($delta === null)
                        <span class="rp-delta neutral">New</span>
                    @elseif(abs($delta) < .05)
                        <span class="rp-delta neutral">0.0%</span>
                    @else
                        <span class="rp-delta {{ $deltaGood ? 'good' : ($deltaBad ? 'bad' : 'neutral') }}">
                            {{ $delta > 0 ? '↗ +' : '↘ ' }}{{ number_format($delta,1) }}%
                        </span>
                    @endif
                    <span class="rp-stat-help">{{ $card['helper'] }}</span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="rp-main-grid">
        <article class="rp-card rp-panel">
            <div class="rp-panel-head">
                <div>
                    <h2 class="rp-panel-title">Revenue Performance</h2>
                    <p class="rp-panel-copy">Daily GMV and delivery-fee movement for delivered orders in the selected period.</p>
                </div>
                <span class="rp-period-chip">{{ $period['label'] }}</span>
            </div>

            <div class="rp-chart-wrap" id="rpRevenueWrap">
                <svg class="rp-chart-svg" id="rpRevenueChart" viewBox="0 0 1000 255" role="img" aria-label="Revenue performance chart"></svg>
                <div class="rp-chart-tooltip" id="rpRevenueTooltip">
                    <small id="rpRevenueTooltipDate"></small>
                    <strong id="rpRevenueTooltipGmv"></strong>
                    <span id="rpRevenueTooltipFees"></span>
                </div>
            </div>
        </article>

        <article class="rp-card rp-panel">
            <div class="rp-panel-head">
                <div>
                    <h2 class="rp-panel-title">Order Status Breakdown</h2>
                    <p class="rp-panel-copy">Distribution of orders created during this report period.</p>
                </div>
                <span class="rp-period-chip">{{ number_format((int) $stats['orders']) }} orders</span>
            </div>

            <div class="rp-donut-layout">
                <div class="rp-donut" style="--status-gradient:{{ $statusGradient }}">
                    <div class="rp-donut-center">
                        <div class="rp-donut-total">{{ number_format((int) $stats['orders']) }}</div>
                        <div class="rp-donut-caption">Total Orders</div>
                    </div>
                </div>

                <div class="rp-legend">
                    @forelse($statusBreakdown as $status => $count)
                        @php
                            $percent = $statusTotal > 0 ? (((int) $count / $statusTotal) * 100) : 0;
                            $color = $statusColors[$status] ?? '#cbd5e1';
                        @endphp
                        <div class="rp-legend-row">
                            <span class="rp-legend-dot" style="background:{{ $color }}"></span>
                            <span>{{ str($status)->replace('_',' ')->title() }}</span>
                            <span class="rp-legend-count">{{ number_format((int) $count) }}</span>
                            <span class="rp-legend-percent">{{ number_format($percent,1) }}%</span>
                        </div>
                    @empty
                        <div class="rp-empty">No order status activity for this period.</div>
                    @endforelse
                </div>
            </div>
        </article>
    </section>

    <section class="rp-secondary-grid">
        <article class="rp-card rp-mini-panel">
            <div class="rp-panel-head">
                <div>
                    <h2 class="rp-panel-title">Order Volume</h2>
                    <p class="rp-panel-copy">Daily order volume from actual orders created during the selected period.</p>
                </div>
                <span class="rp-period-chip">{{ number_format((int) $stats['delivered']) }} delivered</span>
            </div>

            <div class="rp-bars" id="rpOrderBars"></div>
            <div class="rp-axis"><span>{{ $period['from']->format('M j') }}</span><span>{{ $period['to']->format('M j') }}</span></div>
        </article>

        <article class="rp-card rp-mini-panel">
            <div class="rp-panel-head">
                <div>
                    <h2 class="rp-panel-title">Registration Activity</h2>
                    <p class="rp-panel-copy">Actual submitted and pending registration applications for the selected report period.</p>
                </div>
                <span class="rp-period-chip">{{ number_format((int) $stats['registrations']) }} registrations</span>
            </div>

            <div class="rp-registration-legend" aria-label="Registration activity legend">
                <span><i class="submitted"></i>Submitted</span>
                <span><i class="pending"></i>Pending review</span>
            </div>

            <div class="rp-bars rp-registration-bars" id="rpRegistrationBars"></div>
            <div class="rp-axis"><span>{{ $period['from']->format('M j') }}</span><span>{{ $period['to']->format('M j') }}</span></div>
        </article>
    </section>

    <section class="rp-card rp-ledger">
        <div class="rp-section-head">
            <div>
                <h2 class="rp-panel-title">Recent Orders</h2>
                <p class="rp-panel-copy">Latest orders created inside the selected report range.</p>
            </div>
            <button type="button" data-report-pdf-export data-export-base="{{ route('admin.reports.export') }}" class="rp-btn rp-btn-secondary" style="height:34px">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 20h14"></path></svg>
                Export PDF
            </button>
        </div>

        <div class="rp-table-wrap">
            <table class="rp-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order Number</th>
                        <th>Buyer</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Created</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent as $order)
                        @php $statusClass = strtolower((string) $order->status); @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="rp-order">{{ $order->order_number }}</span></td>
                            <td>{{ $order->buyer_name ?: 'Buyer' }}</td>
                            <td><span class="rp-status {{ $statusClass }}">{{ $order->statusLabel() }}</span></td>
                            <td>{{ str((string) $order->payment_status)->replace('_',' ')->title() }}</td>
                            <td>{{ $order->created_at?->format('M j, Y · h:i A') ?: '—' }}</td>
                            <td><span class="rp-money">₱{{ number_format((float) $order->total,2) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="rp-empty">No orders were created in this period.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
(function(){
    const series = @json($dailySeries);
    const registrations = @json($registrationSeries);
    const reportMaxDate = @json(now()->toDateString());
    const money = new Intl.NumberFormat('en-PH',{style:'currency',currency:'PHP'});

    const parseDate = value => {
        const [y,m,d] = String(value || '').split('-').map(Number);
        return new Date(y,(m || 1)-1,d || 1,12,0,0);
    };

    const formatIsoDate = date => [
        date.getFullYear(),
        String(date.getMonth()+1).padStart(2,'0'),
        String(date.getDate()).padStart(2,'0'),
    ].join('-');

    const sameDay = (a,b) => !!a && !!b
        && a.getFullYear()===b.getFullYear()
        && a.getMonth()===b.getMonth()
        && a.getDate()===b.getDate();

    const isBefore = (a,b) => a.getTime() < b.getTime();
    const isAfter = (a,b) => a.getTime() > b.getTime();

    const readableDate = date => date.toLocaleDateString('en-PH',{
        month:'short',
        day:'numeric',
        year:'numeric'
    });

    const svgNode = (name, attrs={}) => {
        const node = document.createElementNS('http://www.w3.org/2000/svg',name);
        Object.entries(attrs).forEach(([key,value]) => node.setAttribute(key,value));
        return node;
    };

    const niceMax = value => {
        const v = Math.max(1,Number(value || 0));
        const magnitude = 10 ** Math.floor(Math.log10(v));
        const normalized = v / magnitude;
        const nice = normalized <= 1 ? 1 : normalized <= 2 ? 2 : normalized <= 5 ? 5 : 10;
        return nice * magnitude;
    };

    const smoothPath = points => {
        if (!points.length) return '';
        if (points.length === 1) return `M ${points[0].x} ${points[0].y}`;
        let path = `M ${points[0].x} ${points[0].y}`;
        for (let i=0;i<points.length-1;i++) {
            const p0 = points[i-1] || points[i];
            const p1 = points[i];
            const p2 = points[i+1];
            const p3 = points[i+2] || p2;
            path += ` C ${p1.x+(p2.x-p0.x)/6} ${p1.y+(p2.y-p0.y)/6}, ${p2.x-(p3.x-p1.x)/6} ${p2.y-(p3.y-p1.y)/6}, ${p2.x} ${p2.y}`;
        }
        return path;
    };

    /* ---------------------------------------------------------
       DUAL CUSTOM CALENDAR / DATE RANGE PICKER
       --------------------------------------------------------- */
    const filterForm = document.getElementById('rpReportFilterForm');
    const picker = document.getElementById('rpRangePicker');
    const trigger = document.getElementById('rpCalendarTrigger');
    const popover = document.getElementById('rpCalendarPopover');
    const fromInput = document.getElementById('rpFromDate');
    const toInput = document.getElementById('rpToDate');
    const rangeLabel = document.getElementById('rpCalendarRangeLabel');
    const selectionLabel = document.getElementById('rpCalendarSelectionLabel');
    const startDateLabel = document.getElementById('rpStartDateLabel');
    const endDateLabel = document.getElementById('rpEndDateLabel');
    const startGrid = document.getElementById('rpStartCalendarGrid');
    const endGrid = document.getElementById('rpEndCalendarGrid');
    const startPrev = document.getElementById('rpStartPrev');
    const startNext = document.getElementById('rpStartNext');
    const endPrev = document.getElementById('rpEndPrev');
    const endNext = document.getElementById('rpEndNext');
    const todayButton = document.getElementById('rpCalendarToday');
    const applyDatesButton = document.getElementById('rpCalendarApply');

    const maxDate = parseDate(reportMaxDate);
    let committedStart = fromInput?.value ? parseDate(fromInput.value) : new Date(maxDate);
    let committedEnd = toInput?.value ? parseDate(toInput.value) : new Date(maxDate);
    let draftStart = new Date(committedStart);
    let draftEnd = new Date(committedEnd);
    let startView = new Date(draftStart.getFullYear(),draftStart.getMonth(),1,12,0,0);
    let endView = new Date(draftEnd.getFullYear(),draftEnd.getMonth(),1,12,0,0);

    const months = [
        'January','February','March','April','May','June',
        'July','August','September','October','November','December'
    ];

    const calendarSelects = {
        startMonth: document.getElementById('rpStartMonthSelect'),
        startYear: document.getElementById('rpStartYearSelect'),
        endMonth: document.getElementById('rpEndMonthSelect'),
        endYear: document.getElementById('rpEndYearSelect'),
    };

    function updateRangeText(start,end) {
        if (!start) return 'Choose dates';
        if (!end || sameDay(start,end)) return readableDate(start);
        return `${readableDate(start)} – ${readableDate(end)}`;
    }

    function updateCalendarLabels() {
        if (rangeLabel) rangeLabel.textContent = updateRangeText(draftStart,draftEnd);
        if (selectionLabel) selectionLabel.textContent = updateRangeText(draftStart,draftEnd);
        if (startDateLabel) startDateLabel.textContent = readableDate(draftStart);
        if (endDateLabel) endDateLabel.textContent = readableDate(draftEnd);
    }

    function currentPdfExportUrl(button) {
        const base = button?.dataset.exportBase || @json(route('admin.reports.export'));
        const url = new URL(base, window.location.origin);

        if (fromInput?.value) url.searchParams.set('from',fromInput.value);
        if (toInput?.value) url.searchParams.set('to',toInput.value);

        return url.toString();
    }

    function closeCalendarSelects(except=null) {
        Object.values(calendarSelects).forEach(select=>{
            if (!select || select===except) return;
            select.classList.remove('is-open');
            select.querySelector('[data-cal-select-trigger]')?.setAttribute('aria-expanded','false');
        });
    }

    function setCalendarSelect(select,value,label,onSelect) {
        if (!select) return;

        const triggerButton = select.querySelector('[data-cal-select-trigger]');
        const labelNode = select.querySelector('[data-cal-select-label]');
        const menu = select.querySelector('[data-cal-select-menu]');

        if (labelNode) labelNode.textContent = label;
        if (!menu) return;

        menu.querySelectorAll('[data-value]').forEach(option=>{
            const selected = String(option.dataset.value)===String(value);
            option.classList.toggle('is-selected',selected);
            option.setAttribute('aria-selected',selected ? 'true' : 'false');
        });

        if (!select.dataset.bound) {
            select.dataset.bound = '1';

            triggerButton?.addEventListener('click',event=>{
                event.stopPropagation();
                const willOpen = !select.classList.contains('is-open');
                closeCalendarSelects(select);
                select.classList.toggle('is-open',willOpen);
                triggerButton.setAttribute('aria-expanded',willOpen ? 'true' : 'false');
            });

            menu.addEventListener('click',event=>{
                const option = event.target.closest('[data-value]');
                if (!option) return;
                onSelect(option.dataset.value,option.dataset.label || option.textContent.trim());
                select.classList.remove('is-open');
                triggerButton?.setAttribute('aria-expanded','false');
            });
        }
    }

    function buildSelectOptions(select,items,currentValue,onSelect) {
        if (!select) return;
        const menu = select.querySelector('[data-cal-select-menu]');
        if (!menu) return;

        menu.innerHTML = items.map(item=>`
            <button
                type="button"
                class="rp-cal-select-option ${String(item.value)===String(currentValue) ? 'is-selected' : ''}"
                data-value="${item.value}"
                data-label="${item.label}"
                role="option"
                aria-selected="${String(item.value)===String(currentValue) ? 'true' : 'false'}"
            >${item.label}</button>
        `).join('');

        setCalendarSelect(select,currentValue,items.find(item=>String(item.value)===String(currentValue))?.label || String(currentValue),onSelect);
    }

    function yearsFor(viewDate) {
        const oldest = Math.min(viewDate.getFullYear(),maxDate.getFullYear()-10);
        const result = [];
        for (let year=maxDate.getFullYear();year>=oldest;year--) {
            result.push({value:year,label:String(year)});
        }
        return result;
    }

    function renderCalendarGrid(type) {
        const isStart = type==='start';
        const grid = isStart ? startGrid : endGrid;
        const view = isStart ? startView : endView;
        if (!grid) return;

        const year = view.getFullYear();
        const month = view.getMonth();
        const firstOfMonth = new Date(year,month,1,12,0,0);
        const firstGridDate = new Date(firstOfMonth);
        firstGridDate.setDate(firstGridDate.getDate()-firstOfMonth.getDay());

        grid.innerHTML = '';

        for (let index=0;index<42;index++) {
            const date = new Date(firstGridDate);
            date.setDate(firstGridDate.getDate()+index);

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'rp-calendar-day';
            button.textContent = String(date.getDate());
            button.dataset.date = formatIsoDate(date);
            button.setAttribute('aria-label',`${isStart ? 'Start' : 'End'} date ${readableDate(date)}`);

            if (date.getMonth()!==month) button.classList.add('is-outside');
            if (sameDay(date,maxDate)) button.classList.add('is-today');

            if (
                draftStart && draftEnd
                && !isBefore(date,draftStart)
                && !isAfter(date,draftEnd)
            ) {
                button.classList.add('is-in-range');
            }

            if ((isStart && sameDay(date,draftStart)) || (!isStart && sameDay(date,draftEnd))) {
                button.classList.add('is-selected');
            }

            const invalidFuture = isAfter(date,maxDate);
            const invalidEndBeforeStart = !isStart && isBefore(date,draftStart);

            if (invalidFuture || invalidEndBeforeStart) {
                button.disabled = true;
            }

            grid.appendChild(button);
        }
    }


    function handleCalendarGridClick(type,event) {
        const button = event.target.closest('.rp-calendar-day[data-date]');
        if (!button || button.disabled) return;

        const date = parseDate(button.dataset.date);
        const isStart = type === 'start';

        if (isStart) {
            draftStart = new Date(date);

            if (isBefore(draftEnd,draftStart)) {
                draftEnd = new Date(draftStart);
                endView = new Date(
                    draftEnd.getFullYear(),
                    draftEnd.getMonth(),
                    1,
                    12,0,0
                );
            }

            startView = new Date(
                date.getFullYear(),
                date.getMonth(),
                1,
                12,0,0
            );
        } else {
            if (isBefore(date,draftStart)) return;

            draftEnd = new Date(date);
            endView = new Date(
                date.getFullYear(),
                date.getMonth(),
                1,
                12,0,0
            );
        }

        renderCalendars();
    }

    startGrid?.addEventListener('click',event=>{
        handleCalendarGridClick('start',event);
    });

    endGrid?.addEventListener('click',event=>{
        handleCalendarGridClick('end',event);
    });

    function updateNavigationState() {
        const latestMonth = new Date(maxDate.getFullYear(),maxDate.getMonth(),1,12,0,0);

        if (startNext) startNext.disabled = !isBefore(startView,latestMonth);
        if (endNext) endNext.disabled = !isBefore(endView,latestMonth);
    }

    function renderCalendarControls(type) {
        const isStart = type==='start';
        const view = isStart ? startView : endView;

        const monthSelect = isStart ? calendarSelects.startMonth : calendarSelects.endMonth;
        const yearSelect = isStart ? calendarSelects.startYear : calendarSelects.endYear;

        buildSelectOptions(
            monthSelect,
            months.map((label,value)=>({value,label})),
            view.getMonth(),
            value=>{
                const next = new Date(view.getFullYear(),Number(value),1,12,0,0);
                const latestMonth = new Date(maxDate.getFullYear(),maxDate.getMonth(),1,12,0,0);
                const safe = isAfter(next,latestMonth) ? latestMonth : next;

                if (isStart) startView = safe;
                else endView = safe;

                renderCalendars();
            }
        );

        buildSelectOptions(
            yearSelect,
            yearsFor(view),
            view.getFullYear(),
            value=>{
                let next = new Date(Number(value),view.getMonth(),1,12,0,0);
                const latestMonth = new Date(maxDate.getFullYear(),maxDate.getMonth(),1,12,0,0);

                if (isAfter(next,latestMonth)) next = latestMonth;

                if (isStart) startView = next;
                else endView = next;

                renderCalendars();
            }
        );
    }

    function renderCalendars() {
        renderCalendarControls('start');
        renderCalendarControls('end');
        renderCalendarGrid('start');
        renderCalendarGrid('end');
        updateNavigationState();
        updateCalendarLabels();
    }

    function openCalendar() {
        if (!picker || !popover || !trigger) return;

        draftStart = new Date(committedStart);
        draftEnd = new Date(committedEnd);
        startView = new Date(draftStart.getFullYear(),draftStart.getMonth(),1,12,0,0);
        endView = new Date(draftEnd.getFullYear(),draftEnd.getMonth(),1,12,0,0);

        renderCalendars();
        calendarInitialized = true;

        picker.classList.add('is-open');
        popover.setAttribute('aria-hidden','false');
        trigger.setAttribute('aria-expanded','true');
    }

    function closeCalendar() {
        if (!picker || !popover || !trigger) return;

        picker.classList.remove('is-open');
        popover.setAttribute('aria-hidden','true');
        trigger.setAttribute('aria-expanded','false');
        closeCalendarSelects();
    }

    let calendarInitialized = false;

    trigger?.addEventListener('click',event=>{
        event.stopPropagation();
        picker?.classList.contains('is-open') ? closeCalendar() : openCalendar();
    });

    popover?.addEventListener('click',event=>{
        if (!event.target.closest('.rp-cal-select')) closeCalendarSelects();
        event.stopPropagation();
    });

    startPrev?.addEventListener('click',()=>{
        startView = new Date(startView.getFullYear(),startView.getMonth()-1,1,12,0,0);
        renderCalendars();
    });

    startNext?.addEventListener('click',()=>{
        const candidate = new Date(startView.getFullYear(),startView.getMonth()+1,1,12,0,0);
        const latestMonth = new Date(maxDate.getFullYear(),maxDate.getMonth(),1,12,0,0);
        if (!isAfter(candidate,latestMonth)) {
            startView = candidate;
            renderCalendars();
        }
    });

    endPrev?.addEventListener('click',()=>{
        endView = new Date(endView.getFullYear(),endView.getMonth()-1,1,12,0,0);
        renderCalendars();
    });

    endNext?.addEventListener('click',()=>{
        const candidate = new Date(endView.getFullYear(),endView.getMonth()+1,1,12,0,0);
        const latestMonth = new Date(maxDate.getFullYear(),maxDate.getMonth(),1,12,0,0);
        if (!isAfter(candidate,latestMonth)) {
            endView = candidate;
            renderCalendars();
        }
    });

    todayButton?.addEventListener('click',()=>{
        draftStart = new Date(maxDate);
        draftEnd = new Date(maxDate);
        startView = new Date(maxDate.getFullYear(),maxDate.getMonth(),1,12,0,0);
        endView = new Date(startView);
        renderCalendars();
    });

    applyDatesButton?.addEventListener('click',()=>{
        committedStart = new Date(draftStart);
        committedEnd = new Date(draftEnd);

        if (fromInput) fromInput.value = formatIsoDate(committedStart);
        if (toInput) toInput.value = formatIsoDate(committedEnd);

        if (rangeLabel) rangeLabel.textContent = updateRangeText(committedStart,committedEnd);

            closeCalendar();
        filterForm?.requestSubmit();
    });

    document.addEventListener('click',event=>{
        const exportButton = event.target.closest('[data-report-pdf-export]');

        if (exportButton) {
            const exportUrl = currentPdfExportUrl(exportButton);

            /*
             * Keep export in a normal browser tab so the print view can
             * open the browser's Save as PDF / Print workflow.
             */
            window.open(exportUrl,'_blank','noopener,noreferrer');
            return;
        }

        closeCalendarSelects();

        if (picker && !picker.contains(event.target)) {
            closeCalendar();
        }
    });

    document.addEventListener('keydown',event=>{
        if (event.key==='Escape') {
            closeCalendarSelects();
            closeCalendar();
        }
    });

    /* ---------------------------------------------------------
       REVENUE PERFORMANCE
       --------------------------------------------------------- */
    function renderRevenue() {
        const svg=document.getElementById('rpRevenueChart');
        const wrap=document.getElementById('rpRevenueWrap');
        const tooltip=document.getElementById('rpRevenueTooltip');
        const tooltipDate=document.getElementById('rpRevenueTooltipDate');
        const tooltipGmv=document.getElementById('rpRevenueTooltipGmv');
        const tooltipFees=document.getElementById('rpRevenueTooltipFees');
        if(!svg || !wrap || !Array.isArray(series) || !series.length) return;

        const width=1000,height=255,margin={top:22,right:20,bottom:38,left:74};
        const iw=width-margin.left-margin.right,ih=height-margin.top-margin.bottom;
        const maxValue=niceMax(Math.max(...series.map(item=>Number(item.gmv || 0)),1));
        const x=index=>margin.left+(series.length<=1?0:(index/(series.length-1))*iw);
        const y=value=>margin.top+ih-(Number(value || 0)/maxValue)*ih;
        const points=series.map((item,index)=>({...item,x:x(index),y:y(item.gmv)}));
        const feePoints=series.map((item,index)=>({...item,x:x(index),y:y(item.delivery_fees)}));
        const line=smoothPath(points),feeLine=smoothPath(feePoints);
        const area=`${line} L ${points[points.length-1].x} ${margin.top+ih} L ${points[0].x} ${margin.top+ih} Z`;

        svg.replaceChildren();

        for(let i=0;i<=4;i++) {
            const ratio=i/4,yy=margin.top+ih-ratio*ih,value=maxValue*ratio;
            svg.appendChild(svgNode('line',{x1:margin.left,y1:yy,x2:width-margin.right,y2:yy,stroke:'#eee7de','stroke-width':'1'}));
            const label=svgNode('text',{x:margin.left-12,y:yy+4,fill:'#8a8175','font-size':'11','font-weight':'500','font-family':'Poppins, sans-serif','text-anchor':'end'});
            label.textContent=`₱${Number(value).toLocaleString('en-PH',{maximumFractionDigits:0})}`;
            svg.appendChild(label);
        }

        const labelCount=Math.min(6,series.length),used=new Set();
        for(let i=0;i<labelCount;i++) {
            const index=labelCount===1?0:Math.round((i/(labelCount-1))*(series.length-1));
            if(used.has(index)) continue;
            used.add(index);
            const p=points[index];
            const label=svgNode('text',{x:p.x,y:height-10,fill:'#8a8175','font-size':'10','font-weight':'500','font-family':'Poppins, sans-serif','text-anchor':index===0?'start':index===series.length-1?'end':'middle'});
            label.textContent=parseDate(p.date).toLocaleDateString('en-PH',{month:'short',day:'numeric'});
            svg.appendChild(label);
        }

        svg.appendChild(svgNode('path',{d:area,fill:'#d29a28','fill-opacity':'.10',stroke:'none'}));
        svg.appendChild(svgNode('path',{d:line,fill:'none',stroke:'#c78e25','stroke-width':'2.6','stroke-linecap':'round','stroke-linejoin':'round'}));
        svg.appendChild(svgNode('path',{d:feeLine,fill:'none',stroke:'#6f826a','stroke-width':'1.8','stroke-linecap':'round','stroke-linejoin':'round','stroke-dasharray':'5 5'}));

        const hoverLine=svgNode('line',{x1:margin.left,y1:margin.top,x2:margin.left,y2:margin.top+ih,stroke:'#b77b16','stroke-width':'1','stroke-dasharray':'3 4',opacity:'0'});
        const hoverDot=svgNode('circle',{cx:margin.left,cy:margin.top+ih,r:'5',fill:'#fff',stroke:'#b77b16','stroke-width':'2.4',opacity:'0'});
        const target=svgNode('rect',{x:margin.left,y:margin.top,width:iw,height:ih,fill:'transparent',style:'cursor:crosshair'});
        svg.appendChild(hoverLine);svg.appendChild(hoverDot);svg.appendChild(target);

        const show=point=>{
            hoverLine.setAttribute('x1',point.x);hoverLine.setAttribute('x2',point.x);hoverLine.setAttribute('opacity','1');
            hoverDot.setAttribute('cx',point.x);hoverDot.setAttribute('cy',point.y);hoverDot.setAttribute('opacity','1');
            if(tooltip && tooltipDate && tooltipGmv && tooltipFees) {
                tooltipDate.textContent=parseDate(point.date).toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'});
                tooltipGmv.textContent=`GMV ${money.format(point.gmv || 0)}`;
                tooltipFees.textContent=`Delivery fees ${money.format(point.delivery_fees || 0)}`;
                tooltip.style.left=`${(point.x/width)*100}%`;
                tooltip.style.top=`${(point.y/height)*100}%`;
                tooltip.classList.toggle('below',point.y<58);
                tooltip.classList.add('show');
            }
        };

        let hoverFrame = 0;
        let chartRect = null;
        let latestPointerX = 0;

        target.addEventListener('mouseenter',()=>{
            chartRect = svg.getBoundingClientRect();
        },{passive:true});

        target.addEventListener('mousemove',event=>{
            latestPointerX = event.clientX;

            if (hoverFrame) return;

            hoverFrame = window.requestAnimationFrame(()=>{
                hoverFrame = 0;

                const rect = chartRect || svg.getBoundingClientRect();
                const relativeX=((latestPointerX-rect.left)/rect.width)*width;
                const normalized=Math.max(0,Math.min(1,(relativeX-margin.left)/iw));
                const index=Math.round(normalized*(points.length-1));

                show(points[index]);
            });
        },{passive:true});

        target.addEventListener('mouseleave',()=>{
            chartRect = null;

            if (hoverFrame) {
                window.cancelAnimationFrame(hoverFrame);
                hoverFrame = 0;
            }

            hoverLine.setAttribute('opacity','0');
            hoverDot.setAttribute('opacity','0');
            tooltip?.classList.remove('show','below');
        },{passive:true});
    }

    function bucketSeries(data,keys,maxBars=60) {
        let points = Array.isArray(data) ? data.map(item=>({...item})) : [];
        if(points.length<=maxBars) return points;

        const bucketSize=Math.ceil(points.length/maxBars);
        const bucketed=[];

        for(let i=0;i<points.length;i+=bucketSize) {
            const chunk=points.slice(i,i+bucketSize);
            const bucket={
                date:chunk[0].date,
                date_end:chunk[chunk.length-1].date,
            };

            keys.forEach(key=>{
                bucket[key]=chunk.reduce((sum,item)=>sum+Number(item[key] || 0),0);
            });

            bucketed.push(bucket);
        }

        return bucketed;
    }

    function dateRangeLabel(item) {
        const startLabel=parseDate(item.date).toLocaleDateString('en-PH',{month:'short',day:'numeric'});
        const endLabel=item.date_end && item.date_end!==item.date
            ? parseDate(item.date_end).toLocaleDateString('en-PH',{month:'short',day:'numeric'})
            : null;

        return endLabel ? `${startLabel}–${endLabel}` : startLabel;
    }

    function renderBars(containerId,data,key,className='') {
        const container=document.getElementById(containerId);
        if(!container || !Array.isArray(data) || !data.length) return;

        const points=bucketSeries(data,[key],60);
        const max=Math.max(...points.map(item=>Number(item[key] || 0)),1);
        container.innerHTML='';

        const fragment=document.createDocumentFragment();

        points.forEach(item=>{
            const value=Number(item[key] || 0);
            const height=Math.max(2,(value/max)*100);
            const wrapper=document.createElement('div');
            wrapper.className='rp-bar-item';
            wrapper.style.setProperty('--bar-height',`${height}%`);
            wrapper.tabIndex=0;

            wrapper.innerHTML=
                `<span class="rp-bar ${className}" style="height:${height}%"></span>`+
                `<span class="rp-bar-tip">${dateRangeLabel(item)} · ${value.toLocaleString('en-PH')}</span>`;

            fragment.appendChild(wrapper);
        });

        container.appendChild(fragment);
    }

    function renderRegistrationBars() {
        const container=document.getElementById('rpRegistrationBars');
        if(!container || !Array.isArray(registrations) || !registrations.length) return;

        const points=bucketSeries(registrations,['registrations','pending'],60);
        const max=Math.max(...points.map(item=>Number(item.registrations || 0)),1);

        container.innerHTML='';

        const fragment=document.createDocumentFragment();

        points.forEach(item=>{
            const submitted=Number(item.registrations || 0);
            const pending=Number(item.pending || 0);
            const submittedHeight=Math.max(2,(submitted/max)*100);
            const pendingHeight=Math.max(2,(pending/max)*100);

            const wrapper=document.createElement('div');
            wrapper.className='rp-reg-group';
            wrapper.tabIndex=0;
            wrapper.style.setProperty('--bar-height',`${Math.max(submittedHeight,pendingHeight)}%`);

            wrapper.innerHTML=
                `<span class="rp-reg-bar submitted" style="height:${submittedHeight}%"></span>`+
                `<span class="rp-reg-bar pending" style="height:${pendingHeight}%"></span>`+
                `<span class="rp-bar-tip">${dateRangeLabel(item)}<br>Submitted: ${submitted.toLocaleString('en-PH')}<br>Pending: ${pending.toLocaleString('en-PH')}</span>`;

            fragment.appendChild(wrapper);
        });

        container.appendChild(fragment);
    }

    const scheduleAfterPaint = callback => {
        window.requestAnimationFrame(()=>{
            window.requestAnimationFrame(callback);
        });
    };

    const scheduleIdle = callback => {
        if ('requestIdleCallback' in window) {
            window.requestIdleCallback(callback,{timeout:450});
        } else {
            window.setTimeout(callback,0);
        }
    };

    // Revenue is the primary visualization, so render it just after first paint.
    scheduleAfterPaint(renderRevenue);

    // Lower-page charts can wait for an idle main-thread opportunity.
    scheduleIdle(()=>{
        renderBars('rpOrderBars',series,'orders','');
        renderRegistrationBars();
    });
})();
</script>
@endsection
