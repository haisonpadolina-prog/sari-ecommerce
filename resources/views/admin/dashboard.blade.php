@extends('layouts.admin')

@section('title', 'Dashboard — SARI Admin')
@section('page-title', 'Dashboard Overview')

@section('content')

<style>
    .sari-admin-dashboard,
    .sari-admin-dashboard * {
        font-family: "Poppins", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .sari-admin-dashboard {
        --ink:#211d17;
        --muted:#8f877d;
        --line:#ece6dd;
        --surface:#ffffff;
        --soft:#fbfaf7;
        --gold:#c99524;
        --gold-dark:#a96f10;
        color:var(--ink);
    }

    .admin-panel {
        border:1px solid var(--line);
        border-radius:18px;
        background:#fff;
        box-shadow:0 8px 24px rgba(49,38,24,.035);
    }

    .admin-kpi {
        border:1px solid #ece6dd;
        border-radius:16px;
        background:#fff;
        box-shadow:0 6px 16px rgba(49,38,24,.025);
        transition:border-color .14s ease, transform .14s ease, box-shadow .14s ease;
    }

    .admin-kpi:hover {
        border-color:#dfd4c4;
        transform:translateY(-1px);
        box-shadow:0 8px 18px rgba(49,38,24,.045);
    }

    .admin-icon-box {
        display:grid;
        height:42px;
        width:42px;
        flex:0 0 auto;
        place-items:center;
        border-radius:12px;
    }

    .admin-view-button {
        display:inline-flex;
        min-height:34px;
        align-items:center;
        justify-content:center;
        gap:6px;
        border:1px solid #e9e1d6;
        border-radius:10px;
        background:#fff;
        padding:0 12px;
        font-size:10px;
        font-weight:600;
        color:#756b5d;
        transition:border-color .14s ease, background-color .14s ease, color .14s ease;
    }

    .admin-view-button:hover {
        border-color:#d8c39a;
        background:#fffaf0;
        color:#9b6715;
    }

    .admin-donut {
        position:relative;
        display:grid;
        height:150px;
        width:150px;
        flex:0 0 auto;
        place-items:center;
        border-radius:999px;
        background:conic-gradient(#d7a325 0deg 230deg,#284b73 230deg 295deg,#2f9b7c 295deg 328deg,#c38a42 328deg 360deg);
    }

    .admin-donut::after {
        content:"";
        position:absolute;
        inset:32px;
        border-radius:inherit;
        background:#fff;
        box-shadow:inset 0 0 0 1px #eee7dd;
    }

    .admin-donut-center { position:relative; z-index:1; text-align:center; }
    .admin-chart svg { display:block; width:100%; height:auto; }

    @media (prefers-reduced-motion: reduce) {
        .admin-kpi,.admin-view-button { transition:none !important; transform:none !important; }
    }

    /* ============================================================
       SARI ADMIN CONTROL TOWER — V2
       Keeps the existing visual language, adds operational UI.
    ============================================================ */

    .admin-control-panel {
        border: 1px solid #e9e2d8;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(49,38,24,.035);
    }

    .admin-control-subtle {
        border: 1px solid #eee8df;
        border-radius: 14px;
        background: #fcfbf8;
    }

    .admin-section-icon {
        display: grid;
        height: 36px;
        width: 36px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 10px;
        background: #fff7e7;
        color: #c99524;
    }

    .admin-focus-chip {
        display: inline-flex;
        min-height: 31px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: 1px solid #e8e0d5;
        border-radius: 999px;
        background: #fff;
        padding: 0 11px;
        font-size: 9px;
        font-weight: 600;
        color: #786f65;
        transition: border-color .14s ease, background-color .14s ease, color .14s ease;
    }

    .admin-focus-chip:hover,
    .admin-focus-chip.is-active {
        border-color: #d8bc80;
        background: #fff8e9;
        color: #9b6715;
    }

    .admin-health-ring {
        position: relative;
        display: grid;
        height: 148px;
        width: 148px;
        place-items: center;
        border-radius: 999px;
        background: conic-gradient(#c99524 0deg 313deg, #eee7dc 313deg 360deg);
    }

    .admin-health-ring::after {
        content: "";
        position: absolute;
        inset: 14px;
        border-radius: inherit;
        background: #fff;
        box-shadow: inset 0 0 0 1px #eee7dc;
    }

    .admin-health-center {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .admin-quick-action {
        display: flex;
        min-height: 72px;
        flex-direction: column;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        border: 1px solid #ebe4d9;
        border-radius: 13px;
        background: #fff;
        padding: 12px;
        text-align: left;
        transition: border-color .14s ease, background-color .14s ease, transform .14s ease;
    }

    .admin-quick-action:hover {
        border-color: #d8c49d;
        background: #fffaf0;
        transform: translateY(-1px);
    }

    .admin-role-card {
        border: 1px solid #ece6dd;
        border-radius: 14px;
        background: #fff;
        padding: 14px;
        transition: border-color .14s ease, transform .14s ease;
    }

    .admin-role-card:hover {
        border-color: #d9c8aa;
        transform: translateY(-1px);
    }

    .admin-journey-stage {
        position: relative;
        min-width: 0;
        text-align: center;
    }

    .admin-journey-stage:not(:last-child)::after {
        content: "";
        position: absolute;
        top: 22px;
        left: calc(50% + 26px);
        width: calc(100% - 52px);
        height: 1px;
        background: #e9e1d6;
    }

    .admin-stage-icon {
        position: relative;
        z-index: 1;
        display: grid;
        height: 44px;
        width: 44px;
        margin: 0 auto;
        place-items: center;
        border: 1px solid #e8e1d8;
        border-radius: 999px;
        background: #fff;
        color: #857a6e;
    }

    .admin-stage-icon.is-good {
        border-color: #d6e8df;
        background: #eff8f4;
        color: #438879;
    }

    .admin-stage-icon.is-warn {
        border-color: #ead9b5;
        background: #fff7e7;
        color: #b77a18;
    }

    .admin-stage-icon.is-hot {
        border-color: #efc7c7;
        background: #fff1f1;
        color: #b85f5f;
    }

    .admin-risk-row {
        display: grid;
        grid-template-columns: 74px minmax(0,1fr) auto;
        align-items: center;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #f0ebe4;
    }

    .admin-risk-row:last-child {
        border-bottom: 0;
    }

    .admin-severity {
        display: inline-flex;
        min-height: 24px;
        width: fit-content;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0 9px;
        font-size: 8px;
        font-weight: 700;
    }

    .admin-severity.high {
        background: #fbecec;
        color: #b95656;
    }

    .admin-severity.medium {
        background: #fff4dd;
        color: #a86f16;
    }

    .admin-severity.low {
        background: #edf7f2;
        color: #4b8d69;
    }

    .admin-rider-ring {
        position: relative;
        display: grid;
        height: 138px;
        width: 138px;
        place-items: center;
        border-radius: 999px;
        background:
            conic-gradient(
                #3f9275 0deg 143deg,
                #c99524 143deg 236deg,
                #7f878c 236deg 300deg,
                #d94f4f 300deg 360deg
            );
    }

    .admin-rider-ring::after {
        content: "";
        position: absolute;
        inset: 24px;
        border-radius: inherit;
        background: #fff;
        box-shadow: inset 0 0 0 1px #eee7dc;
    }

    .admin-drawer-backdrop {
        position: fixed;
        inset: 0;
        z-index: 80;
        background: rgba(31, 27, 22, .28);
        opacity: 0;
        pointer-events: none;
        transition: opacity .16s ease;
    }

    .admin-drawer-backdrop.is-open {
        opacity: 1;
        pointer-events: auto;
    }

    .admin-drawer {
        position: fixed;
        top: 0;
        right: 0;
        z-index: 90;
        height: 100dvh;
        width: min(430px, 92vw);
        overflow-y: auto;
        border-left: 1px solid #e8e0d5;
        background: #fff;
        box-shadow: -18px 0 48px rgba(42, 32, 21, .12);
        transform: translateX(102%);
        transition: transform .18s ease;
    }

    .admin-drawer.is-open {
        transform: translateX(0);
    }

    .admin-toast {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 110;
        max-width: 330px;
        border: 1px solid #e1d2b4;
        border-radius: 13px;
        background: #fffaf0;
        padding: 11px 13px;
        color: #76541a;
        font-size: 10px;
        font-weight: 600;
        box-shadow: 0 12px 30px rgba(49,38,24,.10);
        opacity: 0;
        transform: translateY(8px);
        pointer-events: none;
        transition: opacity .16s ease, transform .16s ease;
    }

    .admin-toast.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    @media (max-width: 767px) {
        .admin-risk-row {
            grid-template-columns: 64px minmax(0,1fr);
        }

        .admin-risk-row > :last-child {
            grid-column: 2;
        }

        .admin-journey-stage:not(:last-child)::after {
            display: none;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .admin-focus-chip,
        .admin-quick-action,
        .admin-role-card,
        .admin-drawer,
        .admin-drawer-backdrop,
        .admin-toast {
            transition: none !important;
            transform: none !important;
        }
    }


    /* ============================================================
       SARI ADMIN CONTROL TOWER — V3 FLOATING NEUMORPHISM
       Subtle elevation only. No heavy glow or animation.
    ============================================================ */

    .sari-admin-dashboard {
        background: #fbfaf7;
    }

    .admin-panel,
    .admin-control-panel,
    .admin-kpi {
        border-color: #e8e1d6 !important;
        background: #fff !important;
        box-shadow:
            8px 8px 20px rgba(74, 58, 38, .055),
            -5px -5px 14px rgba(255, 255, 255, .90) !important;
    }

    .admin-control-subtle,
    .admin-soft-card {
        border-color: #ece5db !important;
        background: #fdfcf9 !important;
        box-shadow:
            inset 2px 2px 5px rgba(78, 61, 40, .03),
            inset -2px -2px 5px rgba(255, 255, 255, .88);
    }

    .admin-panel:hover,
    .admin-control-panel:hover,
    .admin-kpi:hover {
        transform: translateY(-1px);
        border-color: #ded1bd !important;
        box-shadow:
            10px 10px 24px rgba(74, 58, 38, .06),
            -6px -6px 16px rgba(255, 255, 255, .94) !important;
    }

    .admin-section-icon,
    .admin-stat-icon,
    .admin-icon-box {
        box-shadow:
            3px 3px 8px rgba(72, 55, 36, .04),
            -2px -2px 6px rgba(255,255,255,.92);
    }

    /* ============================================================
       NEW MARKETPLACE HEALTH
    ============================================================ */
    .admin-health-shell {
        display: grid;
        gap: 18px;
    }

    .admin-health-score {
        position: relative;
        display: grid;
        height: 170px;
        width: 170px;
        place-items: center;
        border-radius: 50%;
        background:
            conic-gradient(#c99524 0deg 313deg, #eee6d9 313deg 360deg);
        box-shadow:
            10px 10px 24px rgba(74, 58, 38, .07),
            -6px -6px 16px rgba(255,255,255,.96);
    }

    .admin-health-score::before {
        content: "";
        position: absolute;
        inset: 15px;
        border-radius: 50%;
        background: #fff;
        box-shadow:
            inset 4px 4px 9px rgba(79,61,40,.04),
            inset -4px -4px 9px rgba(255,255,255,.95);
    }

    .admin-health-score-content {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .admin-health-grade {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 7px;
        border: 1px solid #d8eadf;
        border-radius: 999px;
        background: #eff8f4;
        padding: 4px 8px;
        font-size: 8px;
        font-weight: 700;
        color: #4d8d68;
    }

    .admin-health-metric {
        border: 1px solid #eee8df;
        border-radius: 13px;
        background: #fdfcf9;
        padding: 11px 12px;
        box-shadow:
            inset 2px 2px 5px rgba(79,61,40,.025),
            inset -2px -2px 5px rgba(255,255,255,.9);
    }

    .admin-health-track {
        margin-top: 7px;
        height: 7px;
        overflow: hidden;
        border-radius: 999px;
        background: #eee8df;
        box-shadow: inset 1px 1px 2px rgba(69,54,37,.05);
    }

    .admin-health-track > span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #b9851f 0%, #d4a93d 100%);
    }

    /* ============================================================
       NEW SALES GRAPH
    ============================================================ */
    .admin-chart-premium {
        position: relative;
        overflow: hidden;
        border: 1px solid #e9e2d8;
        border-radius: 16px;
        background:
            linear-gradient(180deg, #fff 0%, #fcfaf6 100%);
        padding: 16px;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.95),
            5px 5px 12px rgba(74,58,38,.025);
    }

    .admin-chart-premium::after {
        content: "";
        position: absolute;
        inset: auto 0 0;
        height: 35%;
        background: linear-gradient(180deg, rgba(201,149,36,0), rgba(201,149,36,.035));
        pointer-events: none;
    }

    .admin-chart-premium svg {
        position: relative;
        z-index: 1;
        display: block;
        width: 100%;
        height: auto;
    }

    .admin-chart-summary {
        display: grid;
        gap: 10px;
        margin-top: 13px;
    }

    .admin-chart-summary-card {
        border: 1px solid #eee7dc;
        border-radius: 12px;
        background: #fff;
        padding: 10px 12px;
        box-shadow:
            3px 3px 8px rgba(74,58,38,.03),
            -2px -2px 6px rgba(255,255,255,.9);
    }

    /* ============================================================
       NEW PIE / DONUT VISUALS
    ============================================================ */
    .admin-donut-premium {
        position: relative;
        display: grid;
        height: 172px;
        width: 172px;
        place-items: center;
        border-radius: 50%;
        background:
            conic-gradient(
                #d8a124 0deg 230deg,
                #315679 230deg 295deg,
                #37997e 295deg 328deg,
                #c78d49 328deg 360deg
            );
        box-shadow:
            10px 10px 24px rgba(74,58,38,.06),
            -6px -6px 16px rgba(255,255,255,.94);
    }

    .admin-donut-premium::before {
        content: "";
        position: absolute;
        inset: 30px;
        border-radius: inherit;
        background: #fff;
        box-shadow:
            inset 4px 4px 9px rgba(79,61,40,.035),
            inset -4px -4px 9px rgba(255,255,255,.95);
    }

    .admin-donut-premium-center {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .admin-rider-ring {
        box-shadow:
            9px 9px 22px rgba(74,58,38,.055),
            -6px -6px 15px rgba(255,255,255,.92);
    }

    .admin-rider-ring::after {
        box-shadow:
            inset 4px 4px 9px rgba(79,61,40,.035),
            inset -4px -4px 9px rgba(255,255,255,.95);
    }

    .admin-legend-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border-bottom: 1px solid #f0ebe4;
        padding: 9px 0;
    }

    .admin-legend-row:last-child {
        border-bottom: 0;
    }

    .admin-legend-bar {
        margin-top: 4px;
        height: 5px;
        overflow: hidden;
        border-radius: 999px;
        background: #efe8dd;
    }

    .admin-legend-bar > span {
        display: block;
        height: 100%;
        border-radius: inherit;
    }

    /* Remove visual emphasis from old quick action area if any remains */
    .admin-quick-action {
        box-shadow: none !important;
    }

    @media (max-width: 767px) {
        .admin-health-score,
        .admin-donut-premium {
            height: 150px;
            width: 150px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .admin-panel,
        .admin-control-panel,
        .admin-kpi {
            transform: none !important;
        }
    }


    /* ============================================================
       SARI SALES OVERVIEW — PREMIUM V4
       Inspired by the approved mockup:
       Poppins / warm white / soft beige / SARI gold / restrained depth
    ============================================================ */

    .sari-sales-card {
        border: 1px solid #e8dfd3;
        border-radius: 20px;
        background: #fffefb;
        padding: 20px;
        box-shadow:
            9px 9px 22px rgba(69, 53, 34, .055),
            -5px -5px 14px rgba(255, 255, 255, .94);
    }

    .sari-sales-header-icon {
        display: grid;
        height: 42px;
        width: 42px;
        flex: 0 0 auto;
        place-items: center;
        border: 1px solid #eee3cd;
        border-radius: 13px;
        background: #fbf5e9;
        color: #b9831f;
        box-shadow:
            3px 3px 8px rgba(69,53,34,.035),
            -2px -2px 6px rgba(255,255,255,.92);
    }

    .sari-sales-period {
        display: inline-flex;
        min-height: 38px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 1px solid #e7ded2;
        border-radius: 12px;
        background: #fff;
        padding: 0 13px;
        font-size: 9px;
        font-weight: 700;
        color: #554d44;
        box-shadow:
            4px 4px 10px rgba(69,53,34,.035),
            -3px -3px 8px rgba(255,255,255,.92);
    }

    .sari-sales-kpis {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-top: 16px;
    }

    .sari-sales-kpi {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 12px;
        border: 1px solid #ece4d9;
        border-radius: 15px;
        background: #fff;
        padding: 13px 14px;
        box-shadow:
            5px 5px 12px rgba(69,53,34,.038),
            -3px -3px 9px rgba(255,255,255,.94);
    }

    .sari-sales-kpi-icon {
        display: grid;
        height: 38px;
        width: 38px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 50%;
        background: #faf3e4;
        color: #b9821d;
    }

    .sari-sales-kpi-value {
        margin-top: 2px;
        font-size: 20px;
        line-height: 1;
        font-weight: 700;
        letter-spacing: -.035em;
        color: #211d17;
    }

    .sari-sales-trend {
        margin-top: 5px;
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 5px;
        font-size: 8px;
        color: #92897f;
    }

    .sari-sales-trend strong {
        color: #4d8d68;
        font-weight: 700;
    }

    .sari-sales-chart-shell {
        margin-top: 14px;
        overflow: hidden;
        border: 1px solid #eae2d7;
        border-radius: 17px;
        background: #fff;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.96),
            4px 4px 12px rgba(69,53,34,.025);
    }

    .sari-sales-chart-head {
        display: flex;
        flex-direction: column;
        gap: 10px;
        border-bottom: 1px solid #f0ebe4;
        padding: 13px 14px 11px;
    }

    .sari-sales-legend {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 7px;
    }

    .sari-sales-legend-pill,
    .sari-sales-updated {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        gap: 6px;
        border: 1px solid #e8e0d5;
        border-radius: 999px;
        background: #fff;
        padding: 0 9px;
        font-size: 8px;
        font-weight: 600;
        color: #71685f;
    }

    .sari-sales-updated {
        border-color: transparent;
        background: transparent;
        color: #a09990;
        padding-left: 2px;
        padding-right: 2px;
    }

    .sari-sales-dot {
        height: 7px;
        width: 7px;
        border-radius: 50%;
    }

    .sari-sales-chart {
        position: relative;
        padding: 8px 8px 2px;
        background: linear-gradient(180deg, #fff 0%, #fffdf9 100%);
    }

    .sari-sales-chart svg {
        display: block;
        width: 100%;
        height: auto;
    }

    .sari-sales-insights {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-top: 12px;
    }

    .sari-sales-insight {
        position: relative;
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 11px;
        border: 1px solid #ece4d9;
        border-radius: 14px;
        background: #fff;
        padding: 12px 13px;
        box-shadow:
            4px 4px 10px rgba(69,53,34,.032),
            -3px -3px 8px rgba(255,255,255,.92);
    }

    .sari-sales-insight-icon {
        display: grid;
        height: 36px;
        width: 36px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 50%;
        background: #faf3e4;
        color: #b9821d;
    }

    .sari-sales-insight-badge {
        margin-left: auto;
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        border-radius: 999px;
        background: #f4f6e9;
        padding: 0 8px;
        font-size: 7px;
        font-weight: 700;
        color: #667b24;
        white-space: nowrap;
    }

    .sari-sales-insight-value {
        margin-top: 2px;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.025em;
        color: #211d17;
    }

    .sari-sales-insight-note {
        margin-top: 2px;
        font-size: 7.5px;
        color: #92897f;
    }

    .sari-sales-tooltip-card {
        filter: drop-shadow(0 5px 10px rgba(53,41,26,.10));
    }

    @media (min-width: 640px) {
        .sari-sales-kpis,
        .sari-sales-insights {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .sari-sales-chart-head {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }

    @media (max-width: 767px) {
        .sari-sales-card {
            padding: 16px;
        }

        .sari-sales-insight-badge {
            display: none;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-sales-card,
        .sari-sales-kpi,
        .sari-sales-insight,
        .sari-sales-period {
            transition: none !important;
            transform: none !important;
        }
    }


    /* ============================================================
       SARI SALES OVERVIEW — V5 POINT-BASED GRAPH
       Thin strokes, exact weekly points, no curve overshoot.
    ============================================================ */
    .sari-sales-chart {
        padding: 10px 10px 4px;
    }

    .sari-sales-chart .sales-grid-line {
        stroke: #eee8df;
        stroke-width: 1;
        stroke-dasharray: 3 6;
    }

    .sari-sales-chart .sales-guide-line {
        stroke: #f3eee7;
        stroke-width: 1;
    }

    .sari-sales-chart .sales-line {
        fill: none;
        stroke: #c79229;
        stroke-width: 2.25;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sari-sales-chart .commission-line {
        fill: none;
        stroke: #d7c8b0;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sari-sales-chart .sales-point {
        fill: #fff;
        stroke: #c79229;
        stroke-width: 2;
    }

    .sari-sales-chart .commission-point {
        fill: #fff;
        stroke: #d7c8b0;
        stroke-width: 1.7;
    }

    .sari-sales-chart .latest-point-ring {
        fill: rgba(199,146,41,.10);
        stroke: rgba(199,146,41,.28);
        stroke-width: 1;
    }

    .sari-sales-chart .axis-label {
        fill: #9b9388;
        font-size: 9px;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
    }

    .sari-sales-chart .point-value {
        fill: #6f665d;
        font-size: 8px;
        font-weight: 600;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
    }

    .sari-sales-chart .latest-guide {
        stroke: #dec99d;
        stroke-width: 1;
        stroke-dasharray: 4 5;
    }

    .sari-sales-chart .trend-fill {
        opacity: .11;
    }

</style>

<div class="sari-admin-dashboard mx-auto w-full max-w-[1800px]">

    {{-- =========================================================
        WELCOME / OVERVIEW BANNER
    ========================================================== --}}
    <section class="relative overflow-hidden rounded-[20px] border border-[#403c2d] bg-[#1d2017] px-5 py-5 text-white shadow-[0_12px_30px_rgba(31,29,23,.10)] sm:px-6 sm:py-6 lg:px-8">
        <div class="pointer-events-none absolute inset-0 opacity-95"
             style="background:radial-gradient(circle at 88% 8%,rgba(231,209,163,.48),transparent 27%),radial-gradient(circle at 58% 82%,rgba(126,106,49,.18),transparent 30%),linear-gradient(115deg,#171a13 0%,#25271c 52%,#71664a 100%);"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-24 h-48 w-48 rounded-full border-[28px] border-[#8c752d]/20"></div>
        <div class="pointer-events-none absolute right-[15%] top-[-80px] h-60 w-60 rounded-full bg-white/5 blur-3xl"></div>

        <div class="relative z-10 grid gap-6 xl:grid-cols-[1.08fr_1fr] xl:items-center">
            <div>
                <p class="text-[9px] font-semibold uppercase tracking-[.24em] text-[#e1c16f]">SARI Marketplace</p>
                <h2 id="adminGreeting" class="mt-3 text-[26px] font-bold tracking-[-.035em] sm:text-[31px]">Good morning, Admin! 👋</h2>
                <p class="mt-2 max-w-[620px] text-[12px] leading-6 text-white/76 sm:text-[13px]">
                    Here’s a clean overview of your marketplace performance, registrations,
                    seller activity, complaints, and commission.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-3 xl:grid-cols-[1fr_1.15fr_1fr_1.05fr]">
                <div class="rounded-[14px] border border-white/10 bg-white/[.045] p-3.5">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-[10px] bg-[#c99524]/14 text-[#e5b542]">
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"></circle><path d="M12 7v5l3 2"></path></svg>
                        </span>
                        <div>
                            <p class="text-[8px] font-semibold uppercase tracking-[.12em] text-white/50">Time</p>
                            <p id="adminCurrentTime" class="mt-1 text-[16px] font-bold text-white">--:-- --</p>
                            <p id="adminTimezone" class="mt-0.5 text-[8px] text-white/50">Asia/Manila</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[14px] border border-white/10 bg-white/[.045] p-3.5">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-[10px] bg-[#c99524]/14 text-[#e5b542]">
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="5" width="16" height="15" rx="2"></rect><path d="M8 3v4M16 3v4M4 9h16"></path></svg>
                        </span>
                        <div>
                            <p class="text-[8px] font-semibold uppercase tracking-[.12em] text-white/50">Date</p>
                            <p id="adminCurrentDay" class="mt-1 text-[14px] font-bold text-white">Saturday</p>
                            <p id="adminCurrentDate" class="mt-0.5 text-[8px] text-white/50">September 5, 2026</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[14px] border border-white/10 bg-white/[.045] p-3.5">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-[10px] bg-white/7 text-white/90">
                            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M7 17h10a4 4 0 0 0 .5-8A6 6 0 0 0 6 10.5 3.5 3.5 0 0 0 7 17Z"></path></svg>
                        </span>
                        <div>
                            <p class="text-[8px] font-semibold uppercase tracking-[.12em] text-white/50">Weather</p>
                            <p class="mt-1 text-[15px] font-bold text-white">26°C</p>
                            <p class="mt-0.5 text-[8px] text-white/50">Overcast</p>
                        </div>
                    </div>
                </div>

                <div class="hidden items-center justify-end border-l border-white/10 pl-4 xl:flex">
                    <div class="text-right">
                        <p class="text-[7px] font-semibold uppercase tracking-[.28em] text-[#e1c16f]">SARI</p>
                        <p class="mt-2 text-[9px] font-semibold uppercase leading-4 tracking-[.22em] text-white/70">Stronger<br>Philippine<br>Commerce</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- =========================================================
        SUMMARY CARDS
    ========================================================== --}}
    <section class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-6">
        @php
            $dashboardKpis = [
                ['label'=>'Total Users','value'=>'12,458','trend'=>'▲ 12.5%','note'=>'vs last month','tone'=>'green'],
                ['label'=>'Pending Registrations','value'=>'128','trend'=>'28 new','note'=>'awaiting review','tone'=>'gold'],
                ['label'=>'Active Sellers','value'=>'2,345','trend'=>'▲ 9.6%','note'=>'vs last month','tone'=>'teal'],
                ['label'=>'Total Orders','value'=>'8,765','trend'=>'▲ 15.8%','note'=>'vs last month','tone'=>'blue'],
                ['label'=>'Open Complaints','value'=>'23','trend'=>'8 urgent','note'=>'need attention','tone'=>'red'],
                ['label'=>'Platform Commission','value'=>'₱245,680','trend'=>'10%','note'=>'platform rate','tone'=>'purple'],
            ];
            $kpiTone = [
                'green' => ['box'=>'border-[#d7e8dc] bg-[#f2f8f3] text-[#4b8f60]','trend'=>'text-[#4a8d60]'],
                'gold' => ['box'=>'border-[#eadfc8] bg-[#fbf6ec] text-[#bf8420]','trend'=>'text-[#b87d18]'],
                'teal' => ['box'=>'border-[#d2e7df] bg-[#f0f8f5] text-[#438879]','trend'=>'text-[#438879]'],
                'blue' => ['box'=>'border-[#d7e1eb] bg-[#f3f7fb] text-[#5b7fa3]','trend'=>'text-[#587b9f]'],
                'red' => ['box'=>'border-[#ead7d7] bg-[#fbf3f3] text-[#b96464]','trend'=>'text-[#b75f5f]'],
                'purple' => ['box'=>'border-[#e1d9e9] bg-[#f7f3fa] text-[#80649a]','trend'=>'text-[#80649a]'],
            ];
        @endphp

        @foreach ($dashboardKpis as $kpi)
            @php $tone = $kpiTone[$kpi['tone']]; @endphp
            <div class="admin-kpi p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[10px] font-medium text-[#746f67]">{{ $kpi['label'] }}</p>
                        <p class="mt-1.5 truncate text-[23px] font-bold tracking-[-.035em] text-[#1e1b18]">{{ $kpi['value'] }}</p>
                    </div>
                    <span class="admin-icon-box border {{ $tone['box'] }}">
                        @if ($kpi['label'] === 'Total Users')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="8" r="3"></circle><circle cx="17" cy="10" r="2.4"></circle><path d="M3 20c.4-4 2.6-6 6-6s5.6 2 6 6M15 15c3.5 0 5.5 1.8 6 5"></path></svg>
                        @elseif ($kpi['label'] === 'Pending Registrations')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="5" y="4" width="14" height="17" rx="2"></rect><path d="M9 4.5V3h6v1.5M9 9h6M9 13h6M9 17h3"></path></svg>
                        @elseif ($kpi['label'] === 'Active Sellers')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 9h16M5 9l2-5h10l2 5M6 9v11h12V9M9 20v-6h6v6"></path></svg>
                        @elseif ($kpi['label'] === 'Total Orders')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 7h14l-1 13H6L5 7ZM9 7a3 3 0 0 1 6 0"></path></svg>
                        @elseif ($kpi['label'] === 'Open Complaints')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3 3 20h18L12 3ZM12 9v5M12 17h.01"></path></svg>
                        @else
                            <span class="text-[17px] font-semibold">₱</span>
                        @endif
                    </span>
                </div>
                <div class="mt-3 flex items-center gap-2 text-[9px]">
                    <span class="font-semibold {{ $tone['trend'] }}">{{ $kpi['trend'] }}</span>
                    <span class="text-[#989188]">{{ $kpi['note'] }}</span>
                </div>
            </div>
        @endforeach
    </section>



    {{-- =========================================================
        SARI CONTROL TOWER — NEW ADMIN OPERATIONS UI
    ========================================================== --}}
    <section id="adminControlTower" class="mt-4">
        <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[8px] font-bold uppercase tracking-[.16em] text-[#b68022]">Admin Control Tower</p>
                <h3 class="mt-1 text-[17px] font-bold tracking-[-.025em] text-[#211d17]">Marketplace command center</h3>
                <p class="mt-1 text-[10px] text-[#978f84]">One place to monitor sellers, buyers, logistics, riders, and marketplace risk.</p>
            </div>

            <div class="flex flex-wrap gap-2" aria-label="Admin focus mode">
                <button type="button" class="admin-focus-chip is-active" data-admin-focus="overview">Overview</button>
                <button type="button" class="admin-focus-chip" data-admin-focus="operations">Operations</button>
                <button type="button" class="admin-focus-chip" data-admin-focus="risk">Risk</button>
                <button type="button" class="admin-focus-chip" data-admin-focus="growth">Growth</button>
            </div>
        </div>

        {{-- Marketplace Health + Today's Focus --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-[1.15fr_.85fr]">

            {{-- Marketplace Health --}}
            <div id="adminMarketplaceHealth" class="admin-control-panel p-5 sm:p-6">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="admin-section-icon">
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z"></path>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                        </span>
                        <div>
                            <h4 class="text-[14px] font-bold text-[#211d17]">Marketplace Health</h4>
                            <p class="mt-0.5 text-[9px] text-[#978f84]">Operational health across the full marketplace</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="rounded-full border border-[#dce9e1] bg-[#f2f8f4] px-2.5 py-1 text-[8px] font-semibold text-[#4d8d68]">
                            Stable
                        </span>
                    </div>
                </div>

                <div class="admin-health-shell mt-5 lg:grid-cols-[190px_minmax(0,1fr)] lg:items-center">
                    <div class="flex justify-center">
                        <div class="admin-health-score">
                            <div class="admin-health-score-content">
                                <p class="text-[34px] font-bold tracking-[-.055em] text-[#29231d]">87</p>
                                <p class="mt-[-2px] text-[8px] font-semibold uppercase tracking-[.12em] text-[#9b9388]">out of 100</p>
                                <span class="admin-health-grade">
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#4d8d68]"></span>
                                    Healthy
                                </span>
                            </div>
                        </div>
                    </div>

                    @php
                        $healthMetrics = [
                            ['label'=>'Order Success Rate','value'=>'96%','width'=>'96%','note'=>'Strong'],
                            ['label'=>'On-Time Deliveries','value'=>'92%','width'=>'92%','note'=>'Stable'],
                            ['label'=>'Seller Compliance','value'=>'88%','width'=>'88%','note'=>'Watch'],
                            ['label'=>'Buyer Satisfaction','value'=>'90%','width'=>'90%','note'=>'Positive'],
                        ];
                    @endphp

                    <div class="grid gap-2.5 sm:grid-cols-2">
                        @foreach ($healthMetrics as $metric)
                            <div class="admin-health-metric">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-[9px] font-semibold text-[#655d54]">{{ $metric['label'] }}</p>
                                        <p class="mt-0.5 text-[8px] text-[#aaa198]">{{ $metric['note'] }}</p>
                                    </div>
                                    <span class="text-[13px] font-bold text-[#2f2923]">{{ $metric['value'] }}</span>
                                </div>
                                <div class="admin-health-track">
                                    <span style="width: {{ $metric['width'] }}"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 grid gap-2.5 sm:grid-cols-3">
                    <div class="admin-control-subtle p-3 shadow-[3px_3px_8px_rgba(74,58,38,.025)]">
                        <p class="text-[8px] uppercase tracking-[.08em] text-[#9d958b]">Operational risk</p>
                        <p class="mt-1 text-[13px] font-bold text-[#b85f5f]">Moderate</p>
                    </div>
                    <div class="admin-control-subtle p-3 shadow-[3px_3px_8px_rgba(74,58,38,.025)]">
                        <p class="text-[8px] uppercase tracking-[.08em] text-[#9d958b]">Admin queue</p>
                        <p class="mt-1 text-[13px] font-bold text-[#2f2923]">15 items</p>
                    </div>
                    <div class="admin-control-subtle p-3 shadow-[3px_3px_8px_rgba(74,58,38,.025)]">
                        <p class="text-[8px] uppercase tracking-[.08em] text-[#9d958b]">Fulfillment health</p>
                        <p class="mt-1 text-[13px] font-bold text-[#4d8d68]">92%</p>
                    </div>
                </div>
            </div>

            {{-- Today's Focus --}}
            <div class="admin-control-panel p-5 sm:p-6">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="admin-section-icon">
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                <circle cx="12" cy="12" r="8"></circle>
                                <path d="M12 8v4l3 2"></path>
                            </svg>
                        </span>
                        <div>
                            <h4 class="text-[14px] font-bold text-[#211d17]">Today's Focus</h4>
                            <p class="mt-0.5 text-[9px] text-[#978f84]"><span id="adminFocusCount">5</span> items need your attention</p>
                        </div>
                    </div>
                    <button type="button" class="admin-view-button" data-admin-scroll="adminInterventionRadar">View All</button>
                </div>

                @php
                    $focusItems = [
                        ['id'=>'stuck-orders','count'=>'3','title'=>'Orders stuck in Preparing > 3h','severity'=>'high','role'=>'Orders','detail'=>'Three orders have exceeded the preparation threshold and may cause missed pickup windows.','action'=>'Review the affected orders and contact sellers if preparation is blocked.'],
                        ['id'=>'seller-review','count'=>'5','title'=>'New seller registrations','severity'=>'medium','role'=>'Sellers','detail'=>'Five seller applications are waiting for admin verification.','action'=>'Check submitted documents and approve or return incomplete registrations.'],
                        ['id'=>'complaints','count'=>'2','title'=>'Complaints awaiting response','severity'=>'high','role'=>'Buyer Support','detail'=>'Two complaints are close to their response SLA.','action'=>'Review evidence, identify the responsible party, and send the next resolution step.'],
                        ['id'=>'rider-issue','count'=>'1','title'=>'Rider reported an issue','severity'=>'medium','role'=>'Riders','detail'=>'One rider has an unresolved delivery issue that may affect active assignments.','action'=>'Review rider status and reassign delivery capacity if necessary.'],
                        ['id'=>'product-review','count'=>'4','title'=>'Product reports for review','severity'=>'low','role'=>'Catalog','detail'=>'Four product reports are awaiting moderation review.','action'=>'Check listing policy compliance and decide whether to keep, restrict, or remove the listings.'],
                    ];
                @endphp

                <div id="adminFocusList" class="mt-4 divide-y divide-[#f0ebe4]">
                    @foreach ($focusItems as $item)
                        <button
                            type="button"
                            class="flex w-full items-center gap-3 py-2.5 text-left"
                            data-admin-focus-item
                            data-focus-id="{{ $item['id'] }}"
                            data-title="{{ $item['title'] }}"
                            data-role="{{ $item['role'] }}"
                            data-severity="{{ ucfirst($item['severity']) }}"
                            data-detail="{{ $item['detail'] }}"
                            data-action="{{ $item['action'] }}"
                        >
                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full {{ $item['severity'] === 'high' ? 'bg-[#fbefef] text-[#b85f5f]' : ($item['severity'] === 'medium' ? 'bg-[#fff4dd] text-[#a86f16]' : 'bg-[#edf7f2] text-[#4b8d69]') }}">
                                <span class="text-[9px] font-bold">{{ $item['count'] }}</span>
                            </span>
                            <span class="min-w-0 flex-1 truncate text-[9.5px] font-medium text-[#4d463e]">{{ $item['title'] }}</span>
                            <span class="admin-severity {{ $item['severity'] }}">{{ ucfirst($item['severity']) }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Role Control Center + Live Order Journey --}}
        <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-[1fr_1.15fr]">

            <div id="adminRoleControl" class="admin-control-panel p-5">
                <div class="flex items-start gap-3">
                    <span class="admin-section-icon">
                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                            <circle cx="8" cy="8" r="3"></circle>
                            <circle cx="17" cy="10" r="2.5"></circle>
                            <path d="M2 20c.5-4 2.6-6 6-6s5.5 2 6 6M15 15c3.2 0 5.2 1.7 5.8 5"></path>
                        </svg>
                    </span>
                    <div>
                        <h4 class="text-[14px] font-bold text-[#211d17]">Role Control Center</h4>
                        <p class="mt-0.5 text-[9px] text-[#978f84]">At-a-glance control across every marketplace role</p>
                    </div>
                </div>

                @php
                    $roles = [
                        ['name'=>'Sellers','value'=>'2,345','issue'=>'12 flagged','tone'=>'bg-[#eef3f7] text-[#5d7fa1]','target'=>'adminMarketplaceHealth'],
                        ['name'=>'Buyers','value'=>'12,458','issue'=>'8 restricted','tone'=>'bg-[#eef7f2] text-[#4d8d68]','target'=>'adminMarketplaceHealth'],
                        ['name'=>'Logistics','value'=>'28','issue'=>'2 delayed','tone'=>'bg-[#fff4dd] text-[#a86f16]','target'=>'adminRiderOverview'],
                        ['name'=>'Riders','value'=>'156','issue'=>'6 overloaded','tone'=>'bg-[#fbefef] text-[#b85f5f]','target'=>'adminRiderOverview'],
                    ];
                @endphp

                <div class="mt-4 grid grid-cols-2 gap-2.5 lg:grid-cols-4">
                    @foreach ($roles as $role)
                        <button type="button" class="admin-role-card text-left" data-admin-scroll="{{ $role['target'] }}">
                            <span class="grid h-8 w-8 place-items-center rounded-[9px] {{ $role['tone'] }}">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="3"></circle><path d="M5 21c.5-4.5 3-7 7-7s6.5 2.5 7 7"></path></svg>
                            </span>
                            <p class="mt-3 text-[9px] font-semibold text-[#746b61]">{{ $role['name'] }}</p>
                            <p class="mt-1 text-[20px] font-bold tracking-[-.035em] text-[#211d17]">{{ $role['value'] }}</p>
                            <p class="mt-2 text-[8px] font-semibold {{ str_contains($role['issue'], 'flagged') || str_contains($role['issue'], 'delayed') || str_contains($role['issue'], 'overloaded') ? 'text-[#b85f5f]' : 'text-[#a86f16]' }}">
                                {{ $role['issue'] }}
                            </p>
                            <p class="mt-3 text-[8px] font-semibold text-[#a56f17]">Manage →</p>
                        </button>
                    @endforeach
                </div>
            </div>

            <div id="adminOrderJourney" class="admin-control-panel p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="admin-section-icon">
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                <circle cx="5" cy="12" r="2"></circle>
                                <circle cx="19" cy="12" r="2"></circle>
                                <path d="M7 12h10M12 7l5 5-5 5"></path>
                            </svg>
                        </span>
                        <div>
                            <h4 class="text-[14px] font-bold text-[#211d17]">Live Order Journey</h4>
                            <p class="mt-0.5 text-[9px] text-[#978f84]">Spot marketplace bottlenecks before they become complaints</p>
                        </div>
                    </div>
                    <span class="rounded-full border border-[#e9e1d6] bg-[#fcfbf8] px-3 py-1.5 text-[8px] font-semibold text-[#746b61]">Today</span>
                </div>

                @php
                    $journey = [
                        ['name'=>'New','count'=>'124','tone'=>'is-good'],
                        ['name'=>'Preparing','count'=>'48','tone'=>''],
                        ['name'=>'Ready','count'=>'31','tone'=>'is-hot'],
                        ['name'=>'Pickup','count'=>'12','tone'=>'is-warn'],
                        ['name'=>'In Transit','count'=>'85','tone'=>''],
                        ['name'=>'Delivered','count'=>'1,430','tone'=>'is-good'],
                    ];
                @endphp

                <div class="mt-5 grid grid-cols-3 gap-y-5 sm:grid-cols-6">
                    @foreach ($journey as $stage)
                        <div class="admin-journey-stage">
                            <span class="admin-stage-icon {{ $stage['tone'] }}">
                                <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <rect x="5" y="5" width="14" height="14" rx="3"></rect>
                                    <path d="M8 12h8"></path>
                                </svg>
                            </span>
                            <p class="mt-2 text-[8px] font-medium text-[#7b7268]">{{ $stage['name'] }}</p>
                            <p class="mt-1 text-[15px] font-bold {{ $stage['tone'] === 'is-hot' ? 'text-[#b85f5f]' : 'text-[#2e2923]' }}">{{ $stage['count'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 rounded-[12px] border border-[#efd8d8] bg-[#fff7f7] px-3.5 py-2.5 text-[8.5px] text-[#8d5d5d]">
                    <strong>Attention:</strong> Ready-for-pickup is the current bottleneck. Review seller handoff and rider capacity.
                </div>
            </div>
        </div>

        {{-- Intervention Radar + Rider Logistics --}}
        <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-[1fr_1fr]">

            <div id="adminInterventionRadar" class="admin-control-panel p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="admin-section-icon">
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                <circle cx="12" cy="12" r="8"></circle>
                                <circle cx="12" cy="12" r="4"></circle>
                                <circle cx="12" cy="12" r="1"></circle>
                            </svg>
                        </span>
                        <div>
                            <h4 class="text-[14px] font-bold text-[#211d17]">Intervention Radar</h4>
                            <p class="mt-0.5 text-[9px] text-[#978f84]">Real-time issues that need admin judgment</p>
                        </div>
                    </div>
                    <button type="button" class="admin-view-button" data-admin-focus="risk">Risk Focus</button>
                </div>

                @php
                    $risks = [
                        ['severity'=>'high','title'=>'Order #SARI-2841 stuck at Ready for Pickup','time'=>'2 hours ago'],
                        ['severity'=>'high','title'=>'Rider has 7 active deliveries (overloaded)','time'=>'3 hours ago'],
                        ['severity'=>'medium','title'=>'Seller cancellation rate increased to 12%','time'=>'5 hours ago'],
                        ['severity'=>'medium','title'=>'Logistics delay in Quezon City area','time'=>'6 hours ago'],
                        ['severity'=>'low','title'=>'12 registrations awaiting review','time'=>'8 hours ago'],
                    ];
                @endphp

                <div class="mt-4">
                    @foreach ($risks as $risk)
                        <div class="admin-risk-row">
                            <span class="admin-severity {{ $risk['severity'] }}">{{ ucfirst($risk['severity']) }}</span>
                            <p class="min-w-0 truncate text-[9.5px] font-medium text-[#4d463e]">{{ $risk['title'] }}</p>
                            <span class="text-[8px] text-[#aaa198]">{{ $risk['time'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="adminRiderOverview" class="admin-control-panel p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="admin-section-icon">
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M4 16h10V8H4zM14 11h4l2 2v3h-6z"></path>
                                <circle cx="7" cy="18" r="1.5"></circle>
                                <circle cx="17" cy="18" r="1.5"></circle>
                            </svg>
                        </span>
                        <div>
                            <h4 class="text-[14px] font-bold text-[#211d17]">Rider & Logistics Overview</h4>
                            <p class="mt-0.5 text-[9px] text-[#978f84]">Live operational capacity and delivery pressure</p>
                        </div>
                    </div>
                    <button type="button" class="admin-view-button" data-admin-scroll="adminRoleControl">Manage</button>
                </div>

                <div class="mt-5 grid gap-5 md:grid-cols-[170px_minmax(0,1fr)] md:items-center">
                    <div class="flex justify-center">
                        <div class="admin-rider-ring">
                            <div class="admin-health-center">
                                <p class="text-[26px] font-bold tracking-[-.045em] text-[#29231d]">156</p>
                                <p class="mt-0.5 text-[8px] text-[#938b80]">Total Riders</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-x-5 gap-y-3">
                        @php
                            $riderStats = [
                                ['label'=>'Online','value'=>'62','dot'=>'#3f9275'],
                                ['label'=>'On Delivery','value'=>'18','dot'=>'#3f9275'],
                                ['label'=>'Idle','value'=>'21','dot'=>'#c99524'],
                                ['label'=>'Offline','value'=>'6','dot'=>'#7f878c'],
                                ['label'=>'Overloaded','value'=>'6','dot'=>'#d94f4f'],
                                ['label'=>'Active Hubs','value'=>'12','dot'=>'#284b73'],
                            ];
                        @endphp

                        @foreach ($riderStats as $stat)
                            <div class="flex items-center justify-between gap-3 border-b border-[#f1ece5] pb-2">
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full" style="background: {{ $stat['dot'] }}"></span>
                                    <span class="text-[8.5px] text-[#756d64]">{{ $stat['label'] }}</span>
                                </div>
                                <span class="text-[9px] font-bold text-[#3f3831]">{{ $stat['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2">
                    <div class="admin-control-subtle p-3">
                        <p class="text-[8px] text-[#9b9388]">Delayed hubs</p>
                        <p class="mt-1 text-[16px] font-bold text-[#b85f5f]">3</p>
                    </div>
                    <div class="admin-control-subtle p-3">
                        <p class="text-[8px] text-[#9b9388]">Maintenance</p>
                        <p class="mt-1 text-[16px] font-bold text-[#7f878c]">1</p>
                    </div>
                    <div class="admin-control-subtle p-3">
                        <p class="text-[8px] text-[#9b9388]">Offline hubs</p>
                        <p class="mt-1 text-[16px] font-bold text-[#2e2923]">0</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- =========================================================
        MAIN ANALYTICS
    ========================================================== --}}
    <section class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-[1.45fr_.55fr]">
        <div id="adminSalesOverview" class="sari-sales-card">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span class="sari-sales-header-icon">
                        <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M5 19V11"></path>
                            <path d="M10 19V7"></path>
                            <path d="M15 19v-5"></path>
                            <path d="M20 19V4"></path>
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-[17px] font-bold tracking-[-.025em] text-[#211d17]">Sales Overview</h3>
                        <p class="mt-0.5 text-[9.5px] text-[#978f84]">Monthly marketplace activity</p>
                    </div>
                </div>

                <button type="button" class="sari-sales-period" aria-label="Current analytics period">
                    This Month
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m8 10 4 4 4-4"></path>
                    </svg>
                </button>
            </div>

            {{-- Top KPI strip --}}
            <div class="sari-sales-kpis">
                <div class="sari-sales-kpi">
                    <span class="sari-sales-kpi-icon">
                        <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M5 8h14l-1 11H6L5 8Z"></path>
                            <path d="M9 8a3 3 0 0 1 6 0"></path>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[8.5px] font-semibold text-[#70675e]">Orders</p>
                        <p class="sari-sales-kpi-value">8,765</p>
                        <p class="sari-sales-trend">
                            <strong>▲ +12.3%</strong>
                            <span>from last month</span>
                        </p>
                    </div>
                </div>

                <div class="sari-sales-kpi">
                    <span class="sari-sales-kpi-icon">
                        <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 18 10 12l4 4 6-8"></path>
                            <path d="M15 8h5v5"></path>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[8.5px] font-semibold text-[#70675e]">Sales Growth</p>
                        <p class="sari-sales-kpi-value">+24%</p>
                        <p class="sari-sales-trend">
                            <strong>▲ Higher</strong>
                            <span>than last month</span>
                        </p>
                    </div>
                </div>

                <div class="sari-sales-kpi">
                    <span class="sari-sales-kpi-icon">
                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <ellipse cx="12" cy="6" rx="6" ry="2.5"></ellipse>
                            <path d="M6 6v4c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5V6"></path>
                            <path d="M6 10v4c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5v-4"></path>
                            <path d="M6 14v4c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5v-4"></path>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[8.5px] font-semibold text-[#70675e]">Commission</p>
                        <p class="sari-sales-kpi-value">₱245K</p>
                        <p class="sari-sales-trend">
                            <strong>▲ +18.7%</strong>
                            <span>from last month</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Main graph --}}
            <div class="sari-sales-chart-shell">
                <div class="sari-sales-chart-head">
                    <div>
                        <p class="text-[11px] font-bold text-[#322c25]">Sales &amp; Commission Trend</p>
                        <p class="mt-0.5 text-[8px] text-[#9a9288]">Weekly snapshots — each point represents one reporting period</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <div class="sari-sales-legend">
                            <span class="sari-sales-legend-pill">
                                <span class="sari-sales-dot bg-[#c9962f]"></span>
                                Sales
                            </span>
                            <span class="sari-sales-legend-pill">
                                <span class="sari-sales-dot bg-[#d9cbb5]"></span>
                                Commission
                            </span>
                        </div>

                        <span class="sari-sales-updated">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7">
                                <circle cx="12" cy="12" r="8"></circle>
                                <path d="M12 7v5l3 2"></path>
                            </svg>
                            <span id="adminSalesUpdatedTime">Updated today</span>
                        </span>
                    </div>
                </div>

                <div class="sari-sales-chart">
                    <svg viewBox="0 0 900 300" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Sales and commission weekly trend chart">
                        <defs>
                            <linearGradient id="sariSalesAreaV5" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#C79229" stop-opacity=".24"/>
                                <stop offset="100%" stop-color="#C79229" stop-opacity="0"/>
                            </linearGradient>
                        </defs>

                        {{-- Horizontal grid --}}
                        <line class="sales-grid-line" x1="76" y1="42" x2="858" y2="42"/>
                        <line class="sales-grid-line" x1="76" y1="92" x2="858" y2="92"/>
                        <line class="sales-grid-line" x1="76" y1="142" x2="858" y2="142"/>
                        <line class="sales-grid-line" x1="76" y1="192" x2="858" y2="192"/>
                        <line x1="76" y1="242" x2="858" y2="242" stroke="#e7dfd5" stroke-width="1"/>

                        {{-- Exact weekly guides: 5 labels = 5 plotted points --}}
                        @foreach ([115,290,465,640,815] as $x)
                            <line class="sales-guide-line" x1="{{ $x }}" y1="42" x2="{{ $x }}" y2="242"/>
                        @endforeach

                        {{-- Y-axis labels --}}
                        <text class="axis-label" x="15" y="46">₱300K</text>
                        <text class="axis-label" x="15" y="96">₱240K</text>
                        <text class="axis-label" x="15" y="146">₱180K</text>
                        <text class="axis-label" x="15" y="196">₱120K</text>
                        <text class="axis-label" x="31" y="246">₱60K</text>

                        {{-- 
                            Weekly sales points:
                            W1 -> W2 rises
                            W2 -> W3 dips slightly
                            W3 -> W4 rises
                            W4 -> W5 rises
                            Straight point-to-point segments prevent Bezier overshoot.
                        --}}
                        <path
                            class="trend-fill"
                            d="M115 192 L290 151 L465 161 L640 105 L815 72 L815 242 L115 242 Z"
                            fill="url(#sariSalesAreaV5)"
                        />

                        {{-- Commission: thin secondary series --}}
                        <path
                            class="commission-line"
                            d="M115 224 L290 209 L465 215 L640 190 L815 176"
                        />

                        {{-- Sales: thin primary series --}}
                        <path
                            class="sales-line"
                            d="M115 192 L290 151 L465 161 L640 105 L815 72"
                        />

                        {{-- Point markers: one marker per week --}}
                        @foreach ([[115,192],[290,151],[465,161],[640,105],[815,72]] as [$cx,$cy])
                            <circle class="sales-point" cx="{{ $cx }}" cy="{{ $cy }}" r="4.8"/>
                        @endforeach

                        @foreach ([[115,224],[290,209],[465,215],[640,190],[815,176]] as [$cx,$cy])
                            <circle class="commission-point" cx="{{ $cx }}" cy="{{ $cy }}" r="3.8"/>
                        @endforeach

                        {{-- Latest point emphasis only --}}
                        <circle class="latest-point-ring" cx="815" cy="72" r="9"/>
                        <circle class="sales-point" cx="815" cy="72" r="4.8"/>

                        {{-- Latest week guide and compact value label --}}
                        <line class="latest-guide" x1="815" y1="72" x2="815" y2="242"/>
                        <g class="sari-sales-tooltip-card">
                            <rect x="725" y="22" width="118" height="42" rx="10" fill="#fff" stroke="#e7ded2"/>
                            <text x="737" y="39" fill="#8b8278" font-size="8" font-family="Poppins, sans-serif">Week 5</text>
                            <text x="737" y="55" fill="#29231d" font-size="11" font-weight="700" font-family="Poppins, sans-serif">₱245,680</text>
                        </g>

                        {{-- X-axis labels aligned exactly under the points --}}
                        <text class="axis-label" x="96" y="274">Week 1</text>
                        <text class="axis-label" x="271" y="274">Week 2</text>
                        <text class="axis-label" x="446" y="274">Week 3</text>
                        <text class="axis-label" x="621" y="274">Week 4</text>
                        <text class="axis-label" x="796" y="274">Week 5</text>
                    </svg>
                </div>
            </div>

            {{-- Bottom insight cards --}}
            <div class="sari-sales-insights">
                <div class="sari-sales-insight">
                    <span class="sari-sales-insight-icon">
                        <svg viewBox="0 0 24 24" class="h-[16px] w-[16px]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M5 19V11M10 19V7M15 19v-5M20 19V4"></path>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[8px] font-semibold text-[#71685f]">Current Sales</p>
                        <p class="sari-sales-insight-value">₱245,680</p>
                        <p class="sari-sales-insight-note">+12.3% from last month</p>
                    </div>
                    <span class="sari-sales-insight-badge">▲ Trending up</span>
                </div>

                <div class="sari-sales-insight">
                    <span class="sari-sales-insight-icon">
                        <svg viewBox="0 0 24 24" class="h-[16px] w-[16px]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 18 10 12l4 4 6-8"></path>
                            <path d="M15 8h5v5"></path>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[8px] font-semibold text-[#71685f]">Growth</p>
                        <p class="sari-sales-insight-value text-[#4d8d68]">+18.7%</p>
                        <p class="sari-sales-insight-note">Compared to previous period</p>
                    </div>
                    <span class="sari-sales-insight-badge">▲ Strong growth</span>
                </div>

                <div class="sari-sales-insight">
                    <span class="sari-sales-insight-icon">
                        <svg viewBox="0 0 24 24" class="h-[16px] w-[16px]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <ellipse cx="12" cy="7" rx="6" ry="2.5"></ellipse>
                            <path d="M6 7v4c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5V7"></path>
                            <path d="M6 11v4c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5v-4"></path>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[8px] font-semibold text-[#71685f]">Commission</p>
                        <p class="sari-sales-insight-value">₱24,568</p>
                        <p class="sari-sales-insight-note">10% commission rate</p>
                    </div>
                    <span class="sari-sales-insight-badge">▲ On track</span>
                </div>
            </div>
        </div>

        <div class="admin-panel p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-[10px] bg-[#fff7e7] text-[#c99524]"><svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="8" r="3"></circle><path d="M3 20c.4-4 2.5-6 6-6s5.6 2 6 6M17 7v6M14 10h6"></path></svg></span>
                    <div><h3 class="text-[15px] font-bold text-[#211d17]">Recent Registrations</h3><p class="mt-0.5 text-[10px] text-[#978f84]">Accounts awaiting review</p></div>
                </div>
                <button type="button" class="admin-view-button">View All</button>
            </div>

            @php
                $registrations = [
                    ['name'=>'Juan Dela Cruz','role'=>'Buyer','time'=>'2 hours ago','initial'=>'J','tone'=>'bg-[#f2f6f9] text-[#667f97]'],
                    ['name'=>'Maria Santos','role'=>'Seller','time'=>'5 hours ago','initial'=>'M','tone'=>'bg-[#f1f7f3] text-[#5d886b]'],
                    ['name'=>'Pedro Reyes','role'=>'Courier','time'=>'8 hours ago','initial'=>'P','tone'=>'bg-[#f5f2f8] text-[#7d698f]'],
                    ['name'=>'Ana Garcia','role'=>'Seller','time'=>'12 hours ago','initial'=>'A','tone'=>'bg-[#f8f3ed] text-[#9b7651]'],
                    ['name'=>'Luis Mendoza','role'=>'Buyer','time'=>'1 day ago','initial'=>'L','tone'=>'bg-[#f3f5f7] text-[#6f7881]'],
                ];
            @endphp
            <div class="mt-4 divide-y divide-[#f0ebe4]">
                @foreach ($registrations as $registration)
                    <div class="flex items-center justify-between gap-3 py-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full text-[10px] font-bold {{ $registration['tone'] }}">{{ $registration['initial'] }}</span>
                            <div class="min-w-0"><p class="truncate text-[11px] font-semibold text-[#28231d]">{{ $registration['name'] }}</p><p class="mt-0.5 text-[9px] text-[#978e82]">{{ $registration['role'] }}</p></div>
                        </div>
                        <div class="flex shrink-0 items-center gap-2"><span class="hidden text-[8px] text-[#aaa198] 2xl:inline">{{ $registration['time'] }}</span><span class="rounded-full border border-[#eadcbe] bg-[#fbf6eb] px-2 py-1 text-[8px] font-semibold text-[#ae781b]">Pending</span></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    {{-- =========================================================
        BOTTOM PANELS
    ========================================================== --}}
    <section class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-[1.05fr_.95fr]">
        <div id="adminCategoryBreakdown" class="admin-panel p-5 sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-[10px] bg-[#fff7e7] text-[#c99524]"><svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3v9h9"></path><path d="M20 15a8 8 0 1 1-11-11"></path></svg></span>
                    <div><h3 class="text-[15px] font-bold text-[#211d17]">Catalog Category Breakdown</h3><p class="mt-0.5 text-[10px] text-[#978f84]">Distribution of marketplace listings</p></div>
                </div>
                <button type="button" class="admin-view-button">View All Categories</button>
            </div>

            <div class="mt-5 grid gap-5 md:grid-cols-[180px_minmax(0,1fr)] md:items-center">
                <div class="flex justify-center">
                    <div class="admin-donut-premium">
                        <div class="admin-donut-premium-center">
                            <p class="text-[8px] uppercase tracking-[.1em] text-[#958c81]">Total listings</p>
                            <p class="mt-1 text-[24px] font-bold text-[#262018]">11</p>
                            <p class="mt-1 text-[7px] font-semibold text-[#a76f17]">4 categories</p>
                        </div>
                    </div>
                </div>
                @php $categories = [['Electronics','64%','#d7a325'],['Others','18%','#284b73'],['Food & Beverage','9.1%','#2f9b7c'],['Home & Living','9.1%','#c38a42']]; @endphp
                <div>
                    @foreach ($categories as [$name,$value,$dot])
                        <div class="admin-legend-row">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2.5">
                                    <span class="h-2.5 w-2.5 rounded-full" style="background:{{ $dot }}"></span>
                                    <span class="text-[10px] font-medium text-[#675f56]">{{ $name }}</span>
                                </div>
                                <div class="admin-legend-bar">
                                    <span style="width:{{ $value }};background:{{ $dot }}"></span>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-[#51483f]">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="admin-panel p-5 sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-[10px] bg-[#fff7e7] text-[#c99524]"><svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="8"></circle><path d="M12 7v5l3 2"></path></svg></span>
                    <div><h3 class="text-[15px] font-bold text-[#211d17]">Platform Activity</h3><p class="mt-0.5 text-[10px] text-[#978f84]">Latest platform events and activities</p></div>
                </div>
                <button type="button" class="admin-view-button">View All</button>
            </div>

            @php
                $activities = [
                    ['title'=>'New seller registration','body'=>'Maria Santos submitted a seller application','time'=>'2 hours ago','tone'=>'bg-[#eef7f2] text-[#4d8d68]'],
                    ['title'=>'Complaint submitted','body'=>'Order #SARI-7854 reported for review','time'=>'4 hours ago','tone'=>'bg-[#fbefef] text-[#b85f5f]'],
                    ['title'=>'New user registration','body'=>'Juan Dela Cruz created a buyer account','time'=>'6 hours ago','tone'=>'bg-[#eef3f7] text-[#5d7fa1]'],
                    ['title'=>'Commission payout processed','body'=>'₱12,450.00 disbursed to 8 sellers','time'=>'1 day ago','tone'=>'bg-[#fbf4e6] text-[#b47e1c]'],
                ];
            @endphp
            <div class="mt-4 space-y-1">
                @foreach ($activities as $activity)
                    <div class="flex items-start gap-3 rounded-[12px] px-2 py-2.5 transition hover:bg-[#fcfbf8]">
                        <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-[9px] {{ $activity['tone'] }}"><span class="h-2 w-2 rounded-full bg-current"></span></span>
                        <div class="min-w-0 flex-1"><div class="flex items-start justify-between gap-3"><p class="text-[10.5px] font-semibold text-[#37312a]">{{ $activity['title'] }}</p><span class="shrink-0 text-[8px] text-[#aaa198]">{{ $activity['time'] }}</span></div><p class="mt-0.5 text-[8.5px] leading-4 text-[#92897f]">{{ $activity['body'] }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    <div class="h-5"></div>

</div>


{{-- ============================================================
    ADMIN FOCUS DRAWER — CLIENT-SIDE REVIEW WORKSPACE
============================================================ --}}
<div id="adminDrawerBackdrop" class="admin-drawer-backdrop"></div>

<aside id="adminFocusDrawer" class="admin-drawer" aria-hidden="true">
    <div class="sticky top-0 z-10 flex items-center justify-between border-b border-[#eee8df] bg-white/95 px-5 py-4">
        <div>
            <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#b68022]">Admin Review</p>
            <h3 id="adminDrawerTitle" class="mt-1 text-[16px] font-bold text-[#211d17]">Focus item</h3>
        </div>
        <button id="adminDrawerClose" type="button" class="grid h-9 w-9 place-items-center rounded-full border border-[#e9e1d6] text-[#766d63] hover:bg-[#faf8f5]" aria-label="Close">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m7 7 10 10M17 7 7 17"></path></svg>
        </button>
    </div>

    <div class="p-5">
        <div class="rounded-[14px] border border-[#eee8df] bg-[#fcfbf8] p-4">
            <div class="flex items-center justify-between gap-3">
                <span id="adminDrawerRole" class="rounded-full bg-[#fff7e7] px-2.5 py-1 text-[8px] font-bold text-[#a56f17]">Operations</span>
                <span id="adminDrawerSeverity" class="admin-severity medium">Medium</span>
            </div>
            <p id="adminDrawerDetail" class="mt-4 text-[10px] leading-5 text-[#655d54]"></p>
        </div>

        <div class="mt-4">
            <p class="text-[8px] font-bold uppercase tracking-[.12em] text-[#9e958a]">Recommended admin action</p>
            <p id="adminDrawerAction" class="mt-2 text-[10px] leading-5 text-[#514940]"></p>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-2.5">
            <button id="adminMarkReviewed" type="button" class="h-10 rounded-[10px] border border-[#d7b875] bg-[#fff8e9] text-[9px] font-bold text-[#956313]">
                Mark reviewed
            </button>
            <button id="adminOpenRelated" type="button" class="h-10 rounded-[10px] border border-[#c99524] bg-[#c99524] text-[9px] font-bold text-white">
                Open related area
            </button>
        </div>

        <p class="mt-3 text-[8px] leading-4 text-[#aaa198]">
            “Mark reviewed” is a local dashboard state for this browser session. Connect it to your backend later if you want persistent admin workflow tracking.
        </p>
    </div>
</aside>

<div id="adminToast" class="admin-toast">Updated.</div>

<script>
(function () {
    function initSariAdminDashboard() {
        window.__SARI_ADMIN_DASHBOARD_TIMER__ && clearInterval(window.__SARI_ADMIN_DASHBOARD_TIMER__);

        const greeting = document.getElementById('adminGreeting');
        const timeNode = document.getElementById('adminCurrentTime');
        const dayNode = document.getElementById('adminCurrentDay');
        const dateNode = document.getElementById('adminCurrentDate');
        const timezoneNode = document.getElementById('adminTimezone');

        function updateAdminClock() {
            const now = new Date();
            const hour = now.getHours();
            const label = hour < 12 ? 'Good morning' : (hour < 18 ? 'Good afternoon' : 'Good evening');

            if (greeting) greeting.textContent = `${label}, Admin! 👋`;
            if (timeNode) timeNode.textContent = new Intl.DateTimeFormat('en-US', {hour:'numeric', minute:'2-digit', hour12:true}).format(now);
            if (dayNode) dayNode.textContent = new Intl.DateTimeFormat('en-US', {weekday:'long'}).format(now);
            if (dateNode) dateNode.textContent = new Intl.DateTimeFormat('en-US', {month:'long', day:'numeric', year:'numeric'}).format(now);
            if (timezoneNode) timezoneNode.textContent = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Local time';

            const salesUpdated = document.getElementById('adminSalesUpdatedTime');
            if (salesUpdated) {
                salesUpdated.textContent = `Updated today, ${new Intl.DateTimeFormat('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true,
                }).format(now)}`;
            }
        }

        updateAdminClock();
        window.__SARI_ADMIN_DASHBOARD_TIMER__ = setInterval(updateAdminClock, 30000);

        // Avoid duplicated listeners after Livewire navigation.
        window.__SARI_ADMIN_CONTROL_ABORT__?.abort();
        const controller = new AbortController();
        window.__SARI_ADMIN_CONTROL_ABORT__ = controller;
        const { signal } = controller;

        const drawer = document.getElementById('adminFocusDrawer');
        const backdrop = document.getElementById('adminDrawerBackdrop');
        const drawerClose = document.getElementById('adminDrawerClose');
        const drawerTitle = document.getElementById('adminDrawerTitle');
        const drawerRole = document.getElementById('adminDrawerRole');
        const drawerSeverity = document.getElementById('adminDrawerSeverity');
        const drawerDetail = document.getElementById('adminDrawerDetail');
        const drawerAction = document.getElementById('adminDrawerAction');
        const markReviewed = document.getElementById('adminMarkReviewed');
        const openRelated = document.getElementById('adminOpenRelated');
        const focusCount = document.getElementById('adminFocusCount');
        const toast = document.getElementById('adminToast');

        let currentFocusButton = null;
        let currentTargetId = 'adminInterventionRadar';
        let toastTimer = null;

        function showToast(message) {
            if (!toast) return;
            toast.textContent = message;
            toast.classList.add('is-visible');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => toast.classList.remove('is-visible'), 1800);
        }

        function smoothScrollTo(id) {
            const node = document.getElementById(id);
            if (!node) return;
            node.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        document.querySelectorAll('[data-admin-scroll]').forEach((button) => {
            button.addEventListener('click', () => {
                smoothScrollTo(button.dataset.adminScroll || '');
            }, { signal });
        });

        const focusMap = {
            overview: 'adminMarketplaceHealth',
            operations: 'adminOrderJourney',
            risk: 'adminInterventionRadar',
            growth: 'adminSalesOverview',
        };

        document.querySelectorAll('[data-admin-focus]').forEach((button) => {
            button.addEventListener('click', () => {
                const mode = button.dataset.adminFocus || 'overview';

                document.querySelectorAll('[data-admin-focus]').forEach((chip) => {
                    chip.classList.toggle('is-active', chip === button);
                });

                smoothScrollTo(focusMap[mode] || 'adminControlTower');
            }, { signal });
        });

        function closeDrawer() {
            drawer?.classList.remove('is-open');
            backdrop?.classList.remove('is-open');
            drawer?.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }

        function openDrawer(button) {
            currentFocusButton = button;
            const severity = (button.dataset.severity || 'Medium').toLowerCase();

            if (drawerTitle) drawerTitle.textContent = button.dataset.title || 'Focus item';
            if (drawerRole) drawerRole.textContent = button.dataset.role || 'Operations';
            if (drawerDetail) drawerDetail.textContent = button.dataset.detail || '';
            if (drawerAction) drawerAction.textContent = button.dataset.action || '';

            if (drawerSeverity) {
                drawerSeverity.textContent = button.dataset.severity || 'Medium';
                drawerSeverity.className = `admin-severity ${severity}`;
            }

            const role = (button.dataset.role || '').toLowerCase();
            currentTargetId =
                role.includes('rider') ? 'adminRiderOverview' :
                role.includes('order') ? 'adminOrderJourney' :
                role.includes('seller') ? 'adminRoleControl' :
                role.includes('buyer') ? 'adminInterventionRadar' :
                role.includes('catalog') ? 'adminCategoryBreakdown' :
                'adminInterventionRadar';

            drawer?.classList.add('is-open');
            backdrop?.classList.add('is-open');
            drawer?.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        }

        document.querySelectorAll('[data-admin-focus-item]').forEach((button) => {
            const reviewed = sessionStorage.getItem(`sari-admin-reviewed:${button.dataset.focusId}`) === '1';
            if (reviewed) button.classList.add('hidden');

            button.addEventListener('click', () => openDrawer(button), { signal });
        });

        function refreshFocusCount() {
            if (!focusCount) return;
            focusCount.textContent = document.querySelectorAll('[data-admin-focus-item]:not(.hidden)').length;
        }

        refreshFocusCount();

        drawerClose?.addEventListener('click', closeDrawer, { signal });
        backdrop?.addEventListener('click', closeDrawer, { signal });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeDrawer();
        }, { signal });

        markReviewed?.addEventListener('click', () => {
            if (!currentFocusButton) return;

            const focusId = currentFocusButton.dataset.focusId;
            if (focusId) sessionStorage.setItem(`sari-admin-reviewed:${focusId}`, '1');

            currentFocusButton.classList.add('hidden');
            refreshFocusCount();
            closeDrawer();
            showToast('Focus item marked reviewed for this session.');
        }, { signal });

        openRelated?.addEventListener('click', () => {
            closeDrawer();
            setTimeout(() => smoothScrollTo(currentTargetId), 60);
        }, { signal });
    }

    document.addEventListener('DOMContentLoaded', initSariAdminDashboard, { once: true });
    document.addEventListener('livewire:navigated', initSariAdminDashboard);
})();
</script>

@endsection