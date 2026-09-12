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
    :root {
        --rp-brand:#d29a28;
        --rp-brand-strong:#a97012;
        --rp-brand-soft:#fff7e7;
        --rp-gold-deep:#8e651f;
        --rp-bronze:#a6762d;
        --rp-sage:#6f826a;
        --rp-teal:#5f7873;
        --rp-plum:#806f7f;
        --rp-terracotta:#b86556;
        --rp-ink:#251f17;
        --rp-text:#4f493f;
        --rp-muted:#756d61;
        --rp-subtle:#9b9388;
        --rp-line:#e9e1d6;
        --rp-soft:#fbf9f5;
        --rp-shadow:0 2px 5px rgba(78,57,24,.035),0 14px 34px rgba(78,57,24,.055);
    }

    .reports-page {
        width:100%;
        max-width:1880px;
        margin:0 auto;
        padding-bottom:32px;
        color:var(--rp-ink);
        font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
    }

    .rp-card {
        border:1px solid var(--rp-line);
        border-radius:17px;
        background:#fff;
        box-shadow:var(--rp-shadow);
    }

    .rp-header {
        display:flex;
        align-items:flex-end;
        justify-content:space-between;
        gap:18px;
    }

    .rp-title-wrap { display:flex;min-width:0;align-items:center;gap:14px; }
    .rp-title-icon {
        display:grid;width:48px;height:48px;flex:0 0 48px;place-items:center;
        border:1px solid #ead8ad;border-radius:14px;background:#fff8e9;color:#b77b16;
        box-shadow:0 6px 18px rgba(154,108,32,.10);
    }
    .rp-title-icon svg { width:22px;height:22px; }
    .rp-eyebrow { color:#8a7c68;font-size:9.5px;font-weight:700;letter-spacing:.09em;text-transform:uppercase; }
    .rp-title { margin:3px 0 0;color:#251f17;font-size:clamp(1.6rem,1.4rem + .55vw,2rem);font-weight:700;line-height:1.1;letter-spacing:-.04em; }
    .rp-subtitle { margin-top:5px;color:#756d61;font-size:11px;line-height:1.55; }

    .rp-actions { display:flex;flex-wrap:wrap;align-items:center;justify-content:flex-end;gap:10px; }
    .rp-filter-form { display:flex;flex-wrap:wrap;align-items:center;gap:8px; }
    .rp-date-field {
        display:flex;height:42px;align-items:center;gap:8px;border:1px solid #e6ddd0;border-radius:11px;
        background:#fffdf9;padding:0 10px;color:#4f493f;box-shadow:0 1px 2px rgba(16,24,40,.025);
    }
    .rp-date-field svg { width:15px;height:15px;color:#756d61; }
    .rp-date-field input {
        width:118px;border:0;outline:0;background:transparent;color:#4f493f;
        font:inherit;font-size:9.5px;font-weight:500;
    }

    .rp-btn {
        display:inline-flex;height:42px;align-items:center;justify-content:center;gap:7px;border-radius:11px;
        padding:0 13px;font-size:9.5px;font-weight:650;text-decoration:none;cursor:pointer;
    }
    .rp-btn svg { width:14px;height:14px; }
    .rp-btn-secondary { border:1px solid #e2e8f0;background:#fff;color:#475467; }
    .rp-btn-secondary:hover { background:#f8fafc; }
    .rp-btn-primary { border:1px solid #b87b16;background:#c99022;color:#fff;box-shadow:0 8px 16px rgba(163,112,27,.18); }
    .rp-btn-primary:hover { background:#aa7418; }
    .rp-btn-link { border:1px solid #e2e8f0;background:#fff;color:#756d61; }

    .rp-error {
        margin-top:12px;border:1px solid #f4c7c3;border-radius:12px;background:#fff5f4;
        padding:10px 12px;color:#b5473e;font-size:10px;
    }

    .rp-summary-grid {
        display:grid;
        grid-template-columns:repeat(8,minmax(0,1fr));
        gap:10px;
        margin-top:16px;
    }

    .rp-stat {
        position:relative;
        min-height:118px;
        padding:15px 64px 15px 15px;
        overflow:hidden;
    }
    .rp-stat-icon {
        position:absolute;top:14px;right:14px;display:grid;width:40px;height:40px;place-items:center;
        border-radius:12px;border:1px solid transparent;
    }
    .rp-stat-icon svg { width:18px;height:18px; }
    .rp-stat.blue .rp-stat-icon { background:#fff7e6;color:#b17a1f;border-color:#ecd7aa; }
    .rp-stat.green .rp-stat-icon { background:#f1f6ef;color:#6c7f63;border-color:#dce8d8; }
    .rp-stat.violet .rp-stat-icon { background:#f8f1e5;color:#8f6929;border-color:#eadcc3; }
    .rp-stat.orange .rp-stat-icon { background:#fff3e5;color:#c17a25;border-color:#efd9bc; }
    .rp-stat.teal .rp-stat-icon { background:#f0f5f2;color:#607a72;border-color:#dce7e2; }
    .rp-stat.red .rp-stat-icon { background:#fff2ef;color:#b86556;border-color:#f0d6d0; }
    .rp-stat.purple .rp-stat-icon { background:#f7f3f6;color:#806f7f;border-color:#e7dee5; }
    .rp-stat.amber .rp-stat-icon { background:#fff7e7;color:#c18a20;border-color:#eeddb4; }

    .rp-stat-label { color:#756d61;font-size:10px;font-weight:500;line-height:1.35; }
    .rp-stat-value { margin-top:6px;color:#101828;font-size:21px;font-weight:700;line-height:1;letter-spacing:-.04em;white-space:nowrap; }
    .rp-stat-footer { display:flex;align-items:center;gap:6px;margin-top:9px;min-width:0; }
    .rp-delta {
        display:inline-flex;align-items:center;gap:3px;border-radius:999px;padding:4px 6px;
        font-size:7.5px;font-weight:700;white-space:nowrap;
    }
    .rp-delta.good { background:#eaf8ef;color:#24975b; }
    .rp-delta.bad { background:#fff0ed;color:#c95849; }
    .rp-delta.neutral { background:#f2f4f7;color:#756d61; }
    .rp-stat-help { overflow:hidden;color:#98a2b3;font-size:8px;text-overflow:ellipsis;white-space:nowrap; }

    .rp-main-grid {
        display:grid;
        grid-template-columns:minmax(0,1.65fr) minmax(340px,.85fr);
        gap:12px;
        margin-top:12px;
    }
    .rp-panel { min-height:325px;padding:17px; }
    .rp-panel-head { display:flex;align-items:flex-start;justify-content:space-between;gap:12px; }
    .rp-panel-title { color:#101828;font-size:14px;font-weight:700;line-height:1.35; }
    .rp-panel-copy { margin-top:4px;color:#8a7c68;font-size:9.5px;line-height:1.55; }
    .rp-period-chip {
        display:inline-flex;height:30px;align-items:center;border:1px solid #e7dfd4;border-radius:9px;
        background:#fff;padding:0 10px;color:#756d61;font-size:8px;font-weight:600;white-space:nowrap;
    }

    .rp-chart-wrap { position:relative;height:255px;margin-top:14px;overflow:visible; }
    .rp-chart-svg { display:block;width:100%;height:255px; }
    .rp-chart-tooltip {
        position:absolute;z-index:20;min-width:145px;pointer-events:none;transform:translate(-50%,-118%);
        border:1px solid #dfe4ea;border-radius:10px;background:rgba(255,255,255,.99);padding:9px 11px;
        box-shadow:0 10px 24px rgba(16,24,40,.14);opacity:0;
    }
    .rp-chart-tooltip.show { opacity:1; }
    .rp-chart-tooltip.below { transform:translate(-50%,14px); }
    .rp-chart-tooltip small { display:block;color:#756d61;font-size:8px;font-weight:500; }
    .rp-chart-tooltip strong { display:block;margin-top:3px;color:#101828;font-size:11px;font-weight:750; }
    .rp-chart-tooltip span { display:block;margin-top:3px;color:#756d61;font-size:8px; }

    .rp-donut-layout { display:grid;grid-template-columns:150px 1fr;gap:16px;align-items:center;margin-top:22px; }
    .rp-donut {
        position:relative;display:grid;width:145px;height:145px;place-items:center;border-radius:50%;
        background:var(--status-gradient);
    }
    .rp-donut::before {
        content:"";position:absolute;width:102px;height:102px;border-radius:50%;
        background:#fff;box-shadow:inset 0 0 0 1px #eee7de;
    }
    .rp-donut-center { position:relative;text-align:center; }
    .rp-donut-total { color:#101828;font-size:20px;font-weight:700;line-height:1; }
    .rp-donut-caption { margin-top:5px;color:#7c8796;font-size:8.5px; }
    .rp-legend { display:grid;gap:9px; }
    .rp-legend-row { display:grid;grid-template-columns:10px minmax(0,1fr) 40px 50px;gap:7px;align-items:center;color:#756d61;font-size:9px; }
    .rp-legend-dot { width:8px;height:8px;border-radius:3px; }
    .rp-legend-count { color:#4f493f;font-weight:650;text-align:right; }
    .rp-legend-percent { color:#98a2b3;text-align:right; }

    .rp-secondary-grid {
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:12px;
        margin-top:12px;
    }
    .rp-mini-panel { min-height:250px;padding:17px; }
    .rp-bars { display:flex;height:165px;align-items:flex-end;gap:4px;margin-top:17px;padding:8px 2px 0;border-bottom:1px solid #eee7de; }
    .rp-bar-item { position:relative;display:flex;flex:1;height:100%;align-items:flex-end;justify-content:center;min-width:2px; }
    .rp-bar {
        width:min(12px,70%);min-height:2px;border-radius:5px 5px 2px 2px;
        background:linear-gradient(180deg,#e5b85d,#c88e25);
    }
    .rp-bar.reg { background:linear-gradient(180deg,#a68e9f,#806f7f); }
    .rp-bar-item:hover .rp-bar { filter:brightness(.96); }
    .rp-bar-tip {
        position:absolute;bottom:calc(var(--bar-height) + 8px);left:50%;z-index:10;display:none;
        transform:translateX(-50%);border:1px solid #e0e5eb;border-radius:8px;background:#fff;
        padding:6px 8px;color:#4f493f;font-size:8px;white-space:nowrap;box-shadow:0 8px 18px rgba(16,24,40,.10);
    }
    .rp-bar-item:hover .rp-bar-tip { display:block; }
    .rp-axis { display:flex;justify-content:space-between;margin-top:8px;color:#98a2b3;font-size:8px; }

    .rp-ledger { margin-top:12px;overflow:hidden; }
    .rp-section-head { display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 17px;border-bottom:1px solid #edf0f3; }
    .rp-table-wrap { overflow-x:auto; }
    .rp-table { width:100%;min-width:980px;border-collapse:collapse;text-align:left; }
    .rp-table thead { background:#fbfcfd; }
    .rp-table th {
        padding:12px 14px;border-bottom:1px solid #e8ecf1;color:#756d61;font-size:9px;font-weight:700;
        letter-spacing:.035em;text-transform:uppercase;
    }
    .rp-table td { padding:13px 14px;border-bottom:1px solid #eef1f4;color:#475467;font-size:10.5px;vertical-align:middle; }
    .rp-table tbody tr:hover { background:#fdfbf7; }
    .rp-order { color:#101828;font-weight:700; }
    .rp-money { color:#101828;font-weight:700;white-space:nowrap; }
    .rp-status {
        display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:5px 9px;
        background:#f2f4f7;color:#756d61;font-size:9px;font-weight:650;white-space:nowrap;
    }
    .rp-status::before { content:"";width:5px;height:5px;border-radius:50%;background:currentColor; }
    .rp-status.delivered { background:#eff5ed;color:#657b61; }
    .rp-status.cancelled { background:#fff1ee;color:#ad6255; }
    .rp-status.in_transit,.rp-status.heading_pickup,.rp-status.courier_accepted { background:#f0f4f2;color:#607a72; }
    .rp-status.preparing,.rp-status.ready_for_pickup { background:#f6f2f5;color:#806f7f; }

    .rp-empty { display:grid;min-height:120px;place-items:center;color:#98a2b3;font-size:10px;text-align:center; }

    @media (max-width:1680px) {
        .rp-summary-grid { grid-template-columns:repeat(4,minmax(0,1fr)); }
    }
    @media (max-width:1250px) {
        .rp-main-grid,.rp-secondary-grid { grid-template-columns:1fr; }
    }
    @media (max-width:850px) {
        .rp-header { align-items:stretch;flex-direction:column; }
        .rp-actions { justify-content:flex-start; }
        .rp-summary-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .rp-donut-layout { grid-template-columns:1fr;justify-items:center; }
        .rp-legend { width:100%; }
    }
    @media (max-width:560px) {
        .rp-summary-grid { grid-template-columns:1fr; }
        .rp-filter-form { width:100%; }
        .rp-date-field { flex:1; }
        .rp-date-field input { width:100%; }
    }

    /* =========================================================
       REPORTS — TITLE / TYPOGRAPHY / FLOATING SURFACE PASS
       Summary-card text sizes are intentionally NOT changed.
       ========================================================= */

    /* Split title: Platform = black, Reports = SARI gold. */
    .reports-page .rp-title-platform {
        color: #17130e !important;
    }

    .reports-page .rp-title-gold {
        color: #c58d20 !important;
    }

    /* Increase page/header typography only. */
    .reports-page .rp-eyebrow {
        font-size: 10.5px !important;
    }

    .reports-page .rp-title {
        font-size: clamp(1.65rem, 1.45rem + .55vw, 2rem) !important;
        line-height: 1.08 !important;
    }

    .reports-page .rp-subtitle {
        font-size: 12px !important;
        line-height: 1.6 !important;
    }

    .reports-page .rp-date-field input,
    .reports-page .rp-btn {
        font-size: 10.5px !important;
    }

    /* Main section headings and explanatory copy. */
    .reports-page .rp-panel-title {
        font-size: 15.5px !important;
        line-height: 1.4 !important;
    }

    .reports-page .rp-panel-copy {
        font-size: 10.75px !important;
        line-height: 1.6 !important;
    }

    .reports-page .rp-period-chip {
        font-size: 9px !important;
    }

    /* Chart / donut labels. */
    .reports-page .rp-chart-tooltip small {
        font-size: 9px !important;
    }

    .reports-page .rp-chart-tooltip strong {
        font-size: 12px !important;
    }

    .reports-page .rp-chart-tooltip span {
        font-size: 9px !important;
    }

    .reports-page .rp-donut-total {
        font-size: 22px !important;
    }

    .reports-page .rp-donut-caption {
        font-size: 9.5px !important;
    }

    .reports-page .rp-legend-row {
        font-size: 10px !important;
    }

    /* Activity charts. */
    .reports-page .rp-axis,
    .reports-page .rp-bar-tip {
        font-size: 9px !important;
    }

    /* Recent Orders table. */
    .reports-page .rp-table th {
        font-size: 10px !important;
    }

    .reports-page .rp-table td {
        font-size: 11.5px !important;
        line-height: 1.45 !important;
    }

    .reports-page .rp-status {
        font-size: 9.5px !important;
    }

    .reports-page .rp-empty {
        font-size: 11px !important;
    }

    /* Keep SUMMARY CARD typography exactly as currently designed. */
    .reports-page .rp-stat-label,
    .reports-page .rp-stat-value,
    .reports-page .rp-stat-help,
    .reports-page .rp-delta {
        /* intentionally inherits the existing summary-card sizes */
    }

    /* =========================================================
       FLOATING DEPTH — same warm layered elevation language
       used across the other SARI admin pages.
       ========================================================= */
    .reports-page .rp-card {
        border-color: #e7ddd0 !important;
        box-shadow:
            0 3px 7px rgba(72,51,22,.035),
            0 16px 36px rgba(72,51,22,.085),
            0 32px 62px rgba(72,51,22,.038),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    /* Header controls also get a light floating feel. */
    .reports-page .rp-date-field,
    .reports-page .rp-btn-secondary,
    .reports-page .rp-btn-link {
        border-color: #e6ddd0 !important;
        background: #fffdfa !important;
        box-shadow:
            0 2px 4px rgba(72,51,22,.025),
            0 8px 18px rgba(72,51,22,.045),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .reports-page .rp-btn-primary {
        box-shadow:
            0 3px 6px rgba(147,97,18,.08),
            0 12px 24px rgba(147,97,18,.20) !important;
    }

    /* Only summary cards receive hover lift; other report panels stay static. */
    .reports-page .rp-stat {
        transition:
            transform .18s ease,
            border-color .18s ease,
            box-shadow .18s ease !important;
    }

    .reports-page .rp-stat:hover {
        transform: translateY(-2px);
        border-color: #dbc9ac !important;
        box-shadow:
            0 4px 9px rgba(72,51,22,.045),
            0 22px 46px rgba(72,51,22,.115),
            0 40px 76px rgba(72,51,22,.045),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .reports-page .rp-panel,
    .reports-page .rp-mini-panel,
    .reports-page .rp-ledger {
        transform: none !important;
    }

    @media (min-width: 1440px) {
        .reports-page .rp-panel-title {
            font-size: 16px !important;
        }

        .reports-page .rp-panel-copy {
            font-size: 11px !important;
        }

        .reports-page .rp-table td {
            font-size: 12px !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .reports-page .rp-stat {
            transition: none !important;
        }

        .reports-page .rp-stat:hover {
            transform: none !important;
        }
    }


    /* =========================================================
       REPORTS — DUAL CLEAN DATE RANGE CALENDAR
       ========================================================= */
    .reports-page .rp-range-picker {
        position: relative;
        z-index: 70;
    }

    .reports-page .rp-date-range-trigger {
        display: flex;
        min-width: 248px;
        height: 44px;
        align-items: center;
        gap: 10px;
        border: 1px solid #e3d6c3;
        border-radius: 12px;
        background: linear-gradient(180deg,#fff 0%,#fffaf2 100%);
        padding: 0 12px;
        color: #4f493f;
        font: inherit;
        text-align: left;
        cursor: pointer;
        box-shadow:
            0 2px 4px rgba(72,51,22,.025),
            0 9px 20px rgba(72,51,22,.055),
            inset 0 1px 0 rgba(255,255,255,.98);
        transition: border-color .16s ease, box-shadow .16s ease, transform .16s ease;
    }

    .reports-page .rp-date-range-trigger:hover,
    .reports-page .rp-range-picker.is-open .rp-date-range-trigger {
        border-color: #d4b878;
        box-shadow:
            0 0 0 3px rgba(197,141,32,.07),
            0 12px 26px rgba(72,51,22,.07),
            inset 0 1px 0 rgba(255,255,255,.98);
    }

    .reports-page .rp-date-range-icon {
        width: 17px;
        height: 17px;
        flex: 0 0 17px;
        color: #b77b16;
    }

    .reports-page .rp-date-range-copy {
        display: grid;
        min-width: 0;
        flex: 1;
        line-height: 1.2;
    }

    .reports-page .rp-date-range-copy small {
        color: #9b9388;
        font-size: 8px;
        font-weight: 650;
        letter-spacing: .035em;
        text-transform: uppercase;
    }

    .reports-page .rp-date-range-copy strong {
        overflow: hidden;
        margin-top: 3px;
        color: #3d362d;
        font-size: 10px;
        font-weight: 650;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .reports-page .rp-date-range-chevron {
        width: 15px;
        height: 15px;
        flex: 0 0 15px;
        color: #8d8376;
        transition: transform .16s ease;
    }

    .reports-page .rp-range-picker.is-open .rp-date-range-chevron {
        transform: rotate(180deg);
    }

    .reports-page .rp-calendar-popover {
        position: absolute;
        z-index: 120;
        top: calc(100% + 10px);
        right: 0;
        width: min(720px, calc(100vw - 48px));
        padding: 14px;
        border: 1px solid #e3d7c8;
        border-radius: 18px;
        background: #fffefb;
        box-shadow:
            0 12px 28px rgba(55,39,19,.12),
            0 34px 74px rgba(55,39,19,.17),
            inset 0 1px 0 rgba(255,255,255,.98);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-5px) scale(.988);
        transform-origin: top right;
        transition: opacity .14s ease, transform .14s ease, visibility .14s ease;
    }

    .reports-page .rp-range-picker.is-open .rp-calendar-popover {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0) scale(1);
    }

    .reports-page .rp-calendar-dual {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 12px;
    }

    .reports-page .rp-calendar-panel {
        position: relative;
        min-width: 0;
        border: 1px solid #ece3d7;
        border-radius: 15px;
        background:
            linear-gradient(180deg,rgba(255,252,247,.98),#fff 44%);
        padding: 12px;
        box-shadow:
            0 3px 8px rgba(72,51,22,.025),
            inset 0 1px 0 rgba(255,255,255,.98);
    }

    .reports-page .rp-calendar-panel-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
        padding: 0 2px;
    }

    .reports-page .rp-calendar-panel-label span {
        color: #9b9388;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .reports-page .rp-calendar-panel-label strong {
        color: #6d5d45;
        font-size: 9px;
        font-weight: 650;
    }

    .reports-page .rp-calendar-head {
        display: grid;
        grid-template-columns: 34px minmax(0,1fr) 34px;
        gap: 7px;
        align-items: center;
    }

    .reports-page .rp-calendar-nav {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 1px solid #e8dfd3;
        border-radius: 10px;
        background: #fff;
        color: #756d61;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(72,51,22,.025);
    }

    .reports-page .rp-calendar-nav:hover:not(:disabled) {
        border-color: #d8bd85;
        background: #fff8e9;
        color: #a97012;
    }

    .reports-page .rp-calendar-nav:disabled {
        opacity: .38;
        cursor: not-allowed;
    }

    .reports-page .rp-calendar-nav svg {
        width: 15px;
        height: 15px;
    }

    .reports-page .rp-calendar-controls {
        display: grid;
        grid-template-columns: minmax(0,1fr) 82px;
        gap: 6px;
    }

    .reports-page .rp-cal-select {
        position: relative;
        min-width: 0;
        z-index: 12;
    }

    .reports-page .rp-cal-select.is-open {
        z-index: 40;
    }

    .reports-page .rp-cal-select-trigger {
        display: flex;
        width: 100%;
        height: 34px;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        border: 1px solid #e6ddd0;
        border-radius: 10px;
        background: linear-gradient(180deg,#fff 0%,#fffaf3 100%);
        padding: 0 9px;
        color: #4b4238;
        font: inherit;
        font-size: 9.5px;
        font-weight: 650;
        text-align: left;
        cursor: pointer;
        box-shadow:
            0 2px 5px rgba(72,51,22,.025),
            inset 0 1px 0 rgba(255,255,255,.98);
        transition: border-color .14s ease, box-shadow .14s ease, background-color .14s ease;
    }

    .reports-page .rp-cal-select-trigger:hover,
    .reports-page .rp-cal-select.is-open .rp-cal-select-trigger {
        border-color: #d7b978;
        background: #fff8ea;
        box-shadow:
            0 0 0 3px rgba(197,141,32,.065),
            0 6px 14px rgba(72,51,22,.045);
    }

    .reports-page .rp-cal-select-trigger svg {
        width: 13px;
        height: 13px;
        flex: 0 0 13px;
        color: #8d8376;
        transition: transform .14s ease;
    }

    .reports-page .rp-cal-select.is-open .rp-cal-select-trigger svg {
        transform: rotate(180deg);
    }

    .reports-page .rp-cal-select-menu {
        position: absolute;
        z-index: 50;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        max-height: 228px;
        overflow-y: auto;
        padding: 5px;
        border: 1px solid #e4d9ca;
        border-radius: 12px;
        background: #fff;
        box-shadow:
            0 8px 18px rgba(55,39,19,.10),
            0 22px 46px rgba(55,39,19,.14);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-4px) scale(.985);
        transform-origin: top;
        transition: opacity .12s ease, transform .12s ease, visibility .12s ease;
    }

    .reports-page .rp-cal-select.is-open .rp-cal-select-menu {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0) scale(1);
    }

    .reports-page .rp-cal-select-option {
        display: flex;
        width: 100%;
        min-height: 34px;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        padding: 0 9px;
        color: #5b5145;
        font: inherit;
        font-size: 9px;
        font-weight: 550;
        text-align: left;
        cursor: pointer;
    }

    .reports-page .rp-cal-select-option:hover,
    .reports-page .rp-cal-select-option:focus-visible {
        outline: none;
        background: #fff8e9;
        color: #9b6812;
    }

    .reports-page .rp-cal-select-option.is-selected {
        background: #fff3d8;
        color: #9b6812;
        font-weight: 700;
    }

    .reports-page .rp-cal-select-option.is-selected::after {
        content: '✓';
        color: #c58d20;
        font-size: 10px;
        font-weight: 800;
    }

    .reports-page .rp-cal-select-menu::-webkit-scrollbar {
        width: 7px;
    }

    .reports-page .rp-cal-select-menu::-webkit-scrollbar-thumb {
        border: 2px solid #fff;
        border-radius: 999px;
        background: #d9cfc2;
    }

    .reports-page .rp-calendar-weekdays,
    .reports-page .rp-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7,1fr);
        gap: 4px;
    }

    .reports-page .rp-calendar-weekdays {
        margin-top: 12px;
        color: #9b9388;
        font-size: 8px;
        font-weight: 700;
        text-align: center;
    }

    .reports-page .rp-calendar-weekdays span {
        padding: 4px 0;
    }

    .reports-page .rp-calendar-grid {
        margin-top: 3px;
    }

    .reports-page .rp-calendar-day {
        position: relative;
        display: grid;
        height: 34px;
        place-items: center;
        border: 0;
        border-radius: 9px;
        background: transparent;
        color: #4f493f;
        font: inherit;
        font-size: 9.5px;
        font-weight: 550;
        cursor: pointer;
        transition: background-color .12s ease, color .12s ease, box-shadow .12s ease;
    }

    .reports-page .rp-calendar-day:hover:not(:disabled) {
        background: #fff6df;
        color: #9e6d13;
    }

    .reports-page .rp-calendar-day.is-outside {
        color: #c8c0b6;
    }

    .reports-page .rp-calendar-day.is-today::after {
        content: '';
        position: absolute;
        bottom: 4px;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #c58d20;
    }

    .reports-page .rp-calendar-day.is-in-range {
        background: #fff8e9;
        color: #7b5b22;
    }

    .reports-page .rp-calendar-day.is-selected {
        background: #c99022;
        color: #fff;
        box-shadow: 0 6px 12px rgba(166,112,18,.18);
    }

    .reports-page .rp-calendar-day.is-selected.is-today::after {
        background: #fff;
    }

    .reports-page .rp-calendar-day:disabled {
        color: #d7d1c9;
        cursor: not-allowed;
        background: transparent;
    }

    .reports-page .rp-calendar-selection {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 12px;
        border: 1px solid #eee5d9;
        border-radius: 11px;
        background: #fcfaf6;
        padding: 10px 11px;
    }

    .reports-page .rp-calendar-selection span {
        color: #9b9388;
        font-size: 8px;
    }

    .reports-page .rp-calendar-selection strong {
        color: #554a3d;
        font-size: 9px;
        font-weight: 650;
        text-align: right;
    }

    .reports-page .rp-calendar-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 10px;
    }

    .reports-page .rp-calendar-ghost,
    .reports-page .rp-calendar-apply {
        height: 35px;
        border-radius: 10px;
        padding: 0 13px;
        font: inherit;
        font-size: 9px;
        font-weight: 650;
        cursor: pointer;
    }

    .reports-page .rp-calendar-ghost {
        border: 1px solid #e7dfd4;
        background: #fff;
        color: #756d61;
    }

    .reports-page .rp-calendar-apply {
        border: 1px solid #b87b16;
        background: #c99022;
        color: #fff;
        box-shadow: 0 7px 14px rgba(163,112,27,.16);
    }

    .reports-page .rp-calendar-apply:hover {
        background: #aa7418;
    }

    @media (max-width: 850px) {
        .reports-page .rp-date-range-trigger {
            width: 100%;
            min-width: 0;
        }

        .reports-page .rp-range-picker {
            flex: 1 1 100%;
            width: 100%;
        }

        .reports-page .rp-calendar-popover {
            left: 0;
            right: auto;
            width: min(720px, calc(100vw - 42px));
            transform-origin: top left;
        }

        .reports-page .rp-calendar-dual {
            grid-template-columns: 1fr;
        }
    }

    /* =========================================================
       REGISTRATION ACTIVITY — ACTUAL SUBMITTED + PENDING SERIES
       ========================================================= */
    .reports-page .rp-registration-legend {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
        margin-top: 12px;
        color: #756d61;
        font-size: 9px;
        font-weight: 600;
    }

    .reports-page .rp-registration-legend span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .reports-page .rp-registration-legend i {
        display: inline-block;
        width: 9px;
        height: 9px;
        border-radius: 3px;
    }

    .reports-page .rp-registration-legend i.submitted {
        background: #c99022;
    }

    .reports-page .rp-registration-legend i.pending {
        background: #b86556;
    }

    .reports-page .rp-registration-bars {
        margin-top: 9px;
    }

    .reports-page .rp-reg-group {
        position: relative;
        display: flex;
        flex: 1;
        height: 100%;
        min-width: 3px;
        align-items: flex-end;
        justify-content: center;
        gap: 2px;
        outline: none;
    }

    .reports-page .rp-reg-bar {
        width: min(8px, 40%);
        min-width: 2px;
        min-height: 2px;
        border-radius: 5px 5px 2px 2px;
    }

    .reports-page .rp-reg-bar.submitted {
        background: linear-gradient(180deg,#e2b75d,#c99022);
    }

    .reports-page .rp-reg-bar.pending {
        background: linear-gradient(180deg,#d78b7e,#b86556);
    }

    .reports-page .rp-reg-group:hover .rp-bar-tip,
    .reports-page .rp-reg-group:focus .rp-bar-tip {
        display: block;
    }

    @media (max-width: 850px) {
        .reports-page .rp-date-range-trigger {
            width: 100%;
            min-width: 0;
        }

        .reports-page .rp-range-picker {
            flex: 1 1 100%;
            width: 100%;
        }

        .reports-page .rp-calendar-popover {
            left: 0;
            right: auto;
            width: min(340px, calc(100vw - 42px));
            transform-origin: top left;
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

                <button type="submit" class="rp-btn rp-btn-secondary">
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
    const exportButtons = Array.from(document.querySelectorAll('[data-report-pdf-export]'));

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

            button.addEventListener('click',()=>{
                if (isStart) {
                    draftStart = new Date(date);

                    if (isBefore(draftEnd,draftStart)) {
                        draftEnd = new Date(draftStart);
                        endView = new Date(draftEnd.getFullYear(),draftEnd.getMonth(),1,12,0,0);
                    }

                    startView = new Date(date.getFullYear(),date.getMonth(),1,12,0,0);
                } else {
                    if (isBefore(date,draftStart)) return;
                    draftEnd = new Date(date);
                    endView = new Date(date.getFullYear(),date.getMonth(),1,12,0,0);
                }

                renderCalendars();
            });

            grid.appendChild(button);
        }
    }

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

    renderCalendars();

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

    exportButtons.forEach(button=>{
        button.addEventListener('click',()=>{
            const exportUrl = currentPdfExportUrl(button);

            /*
             * Open the report in a normal browser tab instead of letting
             * Livewire Navigate intercept the request. The print view will
             * automatically open Chrome's Save as PDF / Print dialog.
             */
            window.open(exportUrl,'_blank','noopener,noreferrer');
        });
    });

    document.addEventListener('click',event=>{
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

        target.addEventListener('mousemove',event=>{
            const rect=svg.getBoundingClientRect();
            const relativeX=((event.clientX-rect.left)/rect.width)*width;
            const normalized=Math.max(0,Math.min(1,(relativeX-margin.left)/iw));
            const index=Math.round(normalized*(points.length-1));
            show(points[index]);
        });

        target.addEventListener('mouseleave',()=>{
            hoverLine.setAttribute('opacity','0');
            hoverDot.setAttribute('opacity','0');
            tooltip?.classList.remove('show','below');
        });
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

            container.appendChild(wrapper);
        });
    }

    function renderRegistrationBars() {
        const container=document.getElementById('rpRegistrationBars');
        if(!container || !Array.isArray(registrations) || !registrations.length) return;

        const points=bucketSeries(registrations,['registrations','pending'],60);
        const max=Math.max(...points.map(item=>Number(item.registrations || 0)),1);

        container.innerHTML='';

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

            container.appendChild(wrapper);
        });
    }

    renderRevenue();
    renderBars('rpOrderBars',series,'orders','');
    renderRegistrationBars();
})();
</script>
@endsection
