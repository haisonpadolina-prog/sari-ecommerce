@extends('layouts.admin')

@section('title', 'Dashboard — SARI Admin')
@section('page-title', 'Dashboard Overview')

@push('styles')
<link
    rel="stylesheet"
    href="https://cdn-uicons.flaticon.com/uicons-thin-straight/css/uicons-thin-straight.css"
>
@endpush

@section('content')

<style>

    .fi {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        line-height:1;
        vertical-align:middle;
    }

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

    /* ============================================================
       PREMIUM HERO BANNER
    ============================================================ */
    .sari-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid #3f3a2c;
        border-radius: 24px;
        background: linear-gradient(112deg, #161913 0%, #202318 38%, #403a2a 68%, #8f8260 100%);
        box-shadow: 0 18px 42px rgba(31, 29, 23, .11);
    }

    .sari-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 88% 16%, rgba(231, 209, 163, .26), transparent 22%),
            radial-gradient(circle at 14% 85%, rgba(176, 142, 49, .12), transparent 24%);
        pointer-events: none;
    }

    .sari-hero-glow {
        position: absolute;
        top: -64px;
        right: 14%;
        height: 220px;
        width: 220px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .06);
        filter: blur(34px);
        pointer-events: none;
    }

    .sari-hero-kicker {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .24em;
        text-transform: uppercase;
        color: #e5bf62;
    }

    .sari-hero-title {
        margin-top: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        font-size: 30px;
        font-weight: 700;
        line-height: 1.1;
        letter-spacing: -.04em;
        color: #fff;
    }

    .sari-hero-wave {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #f1c55f;
        background: transparent;
        box-shadow: none;
        border-radius: 0;
        height: auto;
        width: auto;
        padding: 0;
        margin-left: 2px;
    }

    .sari-hero-wave .fi {
        font-size: 28px;
    }

    .sari-hero-copy {
        margin-top: 12px;
        max-width: 660px;
        font-size: 13px;
        line-height: 1.8;
        color: rgba(255,255,255,.78);
    }

    .sari-hero-side {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        gap: 18px;
    }

    .sari-hero-stat {
        min-width: 118px;
        padding: 6px 0;
    }

    .sari-hero-stat + .sari-hero-stat {
        padding-left: 18px;
        border-left: 1px solid rgba(255,255,255,.12);
    }

    .sari-hero-stat-label {
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .13em;
        text-transform: uppercase;
        color: rgba(255,255,255,.52);
    }

    .sari-hero-stat-row {
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sari-hero-stat-icon {
        display: inline-grid;
        height: 28px;
        width: 28px;
        place-items: center;
        border-radius: 999px;
        background: rgba(201,149,36,.14);
        color: #ebc35d;
        flex: 0 0 auto;
    }

    .sari-hero-stat-icon.is-cloud {
        background: rgba(255,255,255,.08);
        color: rgba(255,255,255,.90);
    }

    .sari-hero-stat-icon .fi {
        font-size: 12px;
    }

    .sari-hero-stat-value {
        font-size: 15px;
        font-weight: 700;
        color: #fff;
        line-height: 1.1;
    }

    .sari-hero-stat-sub {
        margin-top: 4px;
        padding-left: 36px;
        font-size: 8px;
        color: rgba(255,255,255,.56);
    }

    #adminWeatherCondition {
        padding-left: 0;
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sari-hero-brand {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        min-width: 115px;
        padding-left: 4px;
    }

    .sari-hero-brand-inner {
        border-left: 1px solid rgba(255,255,255,.12);
        padding-left: 18px;
        text-align: right;
    }

    .sari-hero-brand-mark {
        font-size: 7px;
        font-weight: 700;
        letter-spacing: .28em;
        text-transform: uppercase;
        color: #e5bf62;
    }

    .sari-hero-brand-copy {
        margin-top: 10px;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.55;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: rgba(255,255,255,.72);
    }

    @media (max-width: 1279px) {
        .sari-hero-side {
            gap: 14px;
        }

        .sari-hero-brand {
            width: 100%;
            justify-content: flex-start;
        }

        .sari-hero-brand-inner {
            border-left: 0;
            padding-left: 0;
            text-align: left;
        }
    }

    @media (max-width: 767px) {
        .sari-hero-title {
            font-size: 24px;
        }

        .sari-hero-side {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .sari-hero-stat,
        .sari-hero-stat + .sari-hero-stat {
            min-width: 0;
            padding: 0;
            border-left: 0;
        }
    }


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


    /* ============================================================
       ROLE CONTROL + LIVE ORDER JOURNEY — COMPACT PROFESSIONAL V1
       Matches the approved clean reference without changing live data.
    ============================================================ */
    .sari-ops-pair {
        display: grid;
        gap: 16px;
    }

    .sari-ops-card {
        overflow: hidden;
        border: 1px solid #e8e1d6;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(70, 55, 37, .045);
    }

    .sari-ops-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 18px 20px 14px;
    }

    .sari-ops-heading {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: 11px;
    }

    .sari-ops-title {
        font-size: 14px;
        line-height: 1.25;
        font-weight: 700;
        letter-spacing: -.02em;
        color: #211d17;
    }

    .sari-ops-subtitle {
        margin-top: 3px;
        font-size: 9px;
        line-height: 1.45;
        color: #978f84;
    }

    .sari-ops-action {
        display: inline-flex;
        min-height: 34px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 1px solid #e6dccb;
        border-radius: 999px;
        background: #fffdf9;
        padding: 0 13px;
        font-size: 9px;
        font-weight: 700;
        color: #7c5b20;
        transition: border-color .14s ease, background-color .14s ease, color .14s ease;
    }

    .sari-ops-action:hover {
        border-color: #d9bd82;
        background: #fff8e9;
        color: #9b6715;
    }

    .sari-role-table {
        margin: 0 20px 18px;
        border-top: 1px solid #f0ebe4;
    }

    .sari-role-table-head,
    .sari-role-row {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) .68fr .85fr 36px;
        align-items: center;
        gap: 12px;
    }

    .sari-role-table-head {
        min-height: 36px;
        border-bottom: 1px solid #eee8df;
        color: #999087;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .sari-role-row {
        min-height: 54px;
        border-bottom: 1px solid #f1ece5;
        color: #2b261f;
        transition: background-color .14s ease;
    }

    .sari-role-row:last-child {
        border-bottom: 0;
    }

    .sari-role-row:hover {
        background: #fdfbf7;
    }

    .sari-role-name {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px;
        font-size: 10px;
        font-weight: 650;
    }

    .sari-role-icon {
        display: grid;
        height: 32px;
        width: 32px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 9px;
    }

    .sari-role-value {
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.025em;
        color: #211d17;
    }

    .sari-role-pending {
        font-size: 9px;
        font-weight: 600;
        color: #a56f17;
    }

    .sari-role-chevron {
        display: grid;
        height: 28px;
        width: 28px;
        place-items: center;
        justify-self: end;
        border-radius: 8px;
        color: #9a8b77;
        transition: background-color .14s ease, color .14s ease;
    }

    .sari-role-row:hover .sari-role-chevron {
        background: #fff6e4;
        color: #a56f17;
    }

    .sari-journey-body {
        padding: 6px 20px 18px;
    }

    .sari-journey-scroll {
        overflow-x: auto;
        padding-bottom: 2px;
        scrollbar-width: thin;
    }

    .sari-journey-track {
        display: grid;
        min-width: 620px;
        grid-template-columns: repeat(6, minmax(82px, 1fr));
        align-items: start;
    }

    .sari-journey-step {
        position: relative;
        min-width: 0;
        text-align: center;
    }

    .sari-journey-step:not(:last-child)::after {
        content: "";
        position: absolute;
        z-index: 0;
        top: 23px;
        left: calc(50% + 25px);
        width: calc(100% - 50px);
        height: 1px;
        background: #dec99d;
    }

    .sari-journey-icon {
        position: relative;
        z-index: 1;
        display: grid;
        height: 46px;
        width: 46px;
        margin: 0 auto;
        place-items: center;
        border: 1px solid #e3ddd5;
        border-radius: 999px;
        background: #fff;
        color: #8b847d;
        box-shadow: 0 3px 10px rgba(65, 50, 32, .025);
    }

    .sari-journey-icon.is-good {
        border-color: #cfe4d9;
        background: #f2f8f5;
        color: #3f866d;
    }

    .sari-journey-icon.is-warn {
        border-color: #e5bd68;
        background: #fff8e9;
        color: #b67915;
    }

    .sari-journey-icon.is-hot {
        border-color: #e7c4c4;
        background: #fff3f3;
        color: #b85f5f;
    }

    .sari-journey-name {
        margin-top: 8px;
        font-size: 8.5px;
        font-weight: 500;
        color: #797168;
        white-space: nowrap;
    }

    .sari-journey-count {
        margin-top: 4px;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.02em;
        color: #211d17;
    }

    .sari-journey-status {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
        border: 1px solid #dcebe3;
        border-radius: 12px;
        background: #f5faf7;
        padding: 10px 12px;
        color: #326e52;
    }

    .sari-journey-status.is-alert {
        border-color: #ead9b5;
        background: #fff9ee;
        color: #916318;
    }

    .sari-journey-status-icon {
        display: grid;
        height: 27px;
        width: 27px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 999px;
        background: #26754b;
        color: #fff;
    }

    .sari-journey-status.is-alert .sari-journey-status-icon {
        background: #b67a19;
    }

    .sari-journey-status-title {
        font-size: 10px;
        font-weight: 700;
    }

    .sari-journey-status-copy {
        margin-top: 1px;
        font-size: 8.5px;
        color: #7d8d83;
    }

    .sari-journey-status.is-alert .sari-journey-status-copy {
        color: #9a7b4b;
    }

    @media (min-width: 1280px) {
        .sari-ops-pair {
            grid-template-columns: minmax(0, .95fr) minmax(0, 1.05fr);
        }
    }

    @media (max-width: 639px) {
        .sari-ops-head {
            padding: 16px;
        }

        .sari-role-table {
            margin: 0 16px 16px;
        }

        .sari-role-table-head {
            display: none;
        }

        .sari-role-row {
            grid-template-columns: minmax(0, 1fr) auto 28px;
            gap: 10px;
            padding: 8px 0;
        }

        .sari-role-row > .sari-role-pending {
            display: none;
        }

        .sari-journey-body {
            padding: 6px 16px 16px;
        }
    }



    /* ============================================================
       MARKETPLACE HEALTH + TODAY'S FOCUS — CLEAN PROFESSIONAL V2
       Approved SARI reference: same live data, cleaner hierarchy.
    ============================================================ */
    .sari-health-card {
        border: 1px solid #e8e1d6;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(70, 55, 37, .04);
    }

    .sari-health-header {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .sari-health-heading {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: 12px;
    }

    .sari-health-icon {
        display: grid;
        height: 42px;
        width: 42px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 13px;
        background: #fff6e4;
        color: #b77a18;
    }

    .sari-health-title {
        font-size: 14px;
        line-height: 1.25;
        font-weight: 700;
        letter-spacing: -.02em;
        color: #211d17;
    }

    .sari-health-subtitle {
        margin-top: 3px;
        font-size: 9px;
        line-height: 1.45;
        color: #978f84;
    }

    .sari-health-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .sari-health-stable {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        gap: 7px;
        border-radius: 999px;
        background: #edf7f1;
        padding: 0 12px;
        font-size: 9px;
        font-weight: 700;
        color: #2f7d56;
    }

    .sari-health-action {
        display: inline-flex;
        min-height: 36px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 1px solid #e3d5bc;
        border-radius: 999px;
        background: #fff;
        padding: 0 14px;
        font-size: 9px;
        font-weight: 700;
        color: #956313;
        transition: border-color .14s ease, background-color .14s ease, color .14s ease;
    }

    .sari-health-action:hover {
        border-color: #d5b779;
        background: #fff9ef;
        color: #87570d;
    }

    .sari-health-main {
        display: grid;
        gap: 14px;
        margin-top: 18px;
    }

    .sari-health-score-card {
        display: flex;
        min-height: 100%;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 1px solid #ece5dc;
        border-radius: 16px;
        background: #fff;
        padding: 18px 14px;
        text-align: center;
    }

    .sari-health-score-copy {
        margin-top: 14px;
        max-width: 220px;
        font-size: 9px;
        line-height: 1.65;
        color: #8b8379;
    }

    .sari-health-metrics {
        display: grid;
        gap: 10px;
    }

    .sari-health-metric-v2 {
        border: 1px solid #ece5dc;
        border-radius: 16px;
        background: #fff;
        padding: 14px;
    }

    .sari-health-metric-top {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: 11px;
    }

    .sari-health-metric-icon {
        display: grid;
        height: 38px;
        width: 38px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 11px;
        background: #fff6e7;
        color: #af7718;
    }

    .sari-health-metric-copy {
        min-width: 0;
        flex: 1 1 auto;
    }

    .sari-health-metric-label {
        font-size: 9.5px;
        font-weight: 700;
        color: #332d27;
    }

    .sari-health-metric-note {
        margin-top: 2px;
        font-size: 8px;
        color: #9c948a;
    }

    .sari-health-metric-value {
        flex: 0 0 auto;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: -.02em;
        color: #211d17;
    }

    .sari-health-progress {
        height: 7px;
        margin-top: 12px;
        overflow: hidden;
        border-radius: 999px;
        background: #ece7df;
    }

    .sari-health-progress > span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: #d19a22;
    }

    .sari-health-summary {
        display: grid;
        gap: 10px;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #f0ebe4;
    }

    .sari-health-summary-card {
        display: flex;
        align-items: center;
        gap: 11px;
        border: 1px solid #ece5dc;
        border-radius: 15px;
        background: #fff;
        padding: 12px 13px;
    }

    .sari-health-summary-icon {
        display: grid;
        height: 36px;
        width: 36px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 11px;
    }

    .sari-health-summary-label {
        font-size: 8px;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #9b9388;
    }

    .sari-health-summary-value {
        margin-top: 2px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: -.02em;
        color: #211d17;
    }

    .sari-health-summary-note {
        margin-top: 2px;
        font-size: 7.5px;
        color: #9c948a;
    }

    .sari-focus-list-v2 {
        margin-top: 14px;
        border-top: 1px solid #f0ebe4;
    }

    .sari-focus-row-v2 {
        display: grid;
        grid-template-columns: 38px minmax(0, 1fr) auto auto 22px;
        align-items: center;
        gap: 10px;
        width: 100%;
        border-bottom: 1px solid #f0ebe4;
        padding: 11px 0;
        text-align: left;
        transition: background-color .14s ease;
    }

    .sari-focus-row-v2:hover {
        background: #fdfbf7;
    }

    .sari-focus-row-v2:last-child {
        border-bottom: 0;
    }

    .sari-focus-icon-v2 {
        display: grid;
        height: 34px;
        width: 34px;
        place-items: center;
        border-radius: 10px;
    }

    .sari-focus-title-v2 {
        font-size: 9.5px;
        font-weight: 650;
        color: #302a24;
    }

    .sari-focus-copy-v2 {
        margin-top: 2px;
        font-size: 8px;
        color: #9a9288;
    }

    .sari-focus-count-v2 {
        min-width: 20px;
        text-align: right;
        font-size: 13px;
        font-weight: 700;
        color: #211d17;
    }

    .sari-focus-badge-v2 {
        display: inline-flex;
        min-height: 27px;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0 10px;
        font-size: 8px;
        font-weight: 700;
    }

    .sari-focus-chevron-v2 {
        display: grid;
        height: 22px;
        width: 22px;
        place-items: center;
        color: #9b9184;
    }

    .sari-focus-empty-v2 {
        margin-top: 14px;
        border: 1px solid #dcebe3;
        border-radius: 14px;
        background: #f5faf7;
        padding: 14px;
        color: #326e52;
    }

    @media (min-width: 640px) {
        .sari-health-header {
            flex-direction: row;
            align-items: flex-start;
            justify-content: space-between;
        }

        .sari-health-summary {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (min-width: 1024px) {
        .sari-health-main {
            grid-template-columns: 220px minmax(0, 1fr);
            align-items: stretch;
        }

        .sari-health-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 639px) {
        .sari-focus-row-v2 {
            grid-template-columns: 34px minmax(0, 1fr) auto 20px;
            gap: 9px;
        }

        .sari-focus-badge-v2 {
            display: none;
        }
    }


    /* ============================================================
       INTERVENTION RADAR + RIDER / LOGISTICS — CLEAN OPERATIONS V2
       Compact executive layout using the existing live backend data.
    ============================================================ */
    .sari-monitor-grid {
        display: grid;
        gap: 16px;
    }

    .sari-monitor-card {
        min-width: 0;
        overflow: hidden;
        border: 1px solid #e8e1d6;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(70, 55, 37, .04);
    }

    .sari-monitor-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 18px 20px 14px;
    }

    .sari-monitor-heading {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: 12px;
    }

    .sari-monitor-icon {
        display: grid;
        height: 42px;
        width: 42px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 13px;
        background: #fff6e4;
        color: #b77a18;
    }

    .sari-monitor-title {
        font-size: 14px;
        line-height: 1.25;
        font-weight: 700;
        letter-spacing: -.02em;
        color: #211d17;
    }

    .sari-monitor-subtitle {
        margin-top: 3px;
        font-size: 9px;
        line-height: 1.45;
        color: #978f84;
    }

    .sari-monitor-action {
        display: inline-flex;
        min-height: 36px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 1px solid #e3d8c7;
        border-radius: 11px;
        background: #fff;
        padding: 0 13px;
        font-size: 9px;
        font-weight: 700;
        color: #6f6253;
        transition: border-color .14s ease, background-color .14s ease, color .14s ease;
    }

    .sari-monitor-action:hover {
        border-color: #d8bd82;
        background: #fff9ef;
        color: #956313;
    }

    .sari-risk-body {
        padding: 0 20px 18px;
    }

    .sari-risk-list {
        overflow: hidden;
        border: 1px solid #eee7de;
        border-radius: 14px;
        background: #fff;
    }

    .sari-risk-item {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 13px;
        min-height: 58px;
        padding: 11px 14px;
        border-bottom: 1px solid #f1ece5;
        color: inherit;
        transition: background-color .14s ease;
    }

    .sari-risk-item:last-child {
        border-bottom: 0;
    }

    .sari-risk-item:hover {
        background: #fdfbf8;
    }

    .sari-risk-pill {
        display: inline-flex;
        min-height: 26px;
        min-width: 52px;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0 10px;
        font-size: 8px;
        font-weight: 700;
    }

    .sari-risk-pill.low {
        background: #edf7f2;
        color: #3f8560;
    }

    .sari-risk-pill.medium {
        background: #fff4dd;
        color: #a86f16;
    }

    .sari-risk-pill.high {
        background: #fbecec;
        color: #b95656;
    }

    .sari-risk-name {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 10px;
        font-weight: 600;
        color: #332d27;
    }

    .sari-risk-live {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        font-size: 8px;
        font-weight: 500;
        color: #958c82;
    }

    .sari-risk-live-dot {
        height: 8px;
        width: 8px;
        border-radius: 50%;
        background: #4b9a78;
    }

    .sari-risk-empty {
        display: flex;
        min-height: 58px;
        align-items: center;
        gap: 11px;
        padding: 12px 14px;
        color: #64786c;
    }

    .sari-risk-state {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 14px;
        border: 1px solid #dcebe3;
        border-radius: 15px;
        background: #f6faf8;
        padding: 14px 15px;
    }

    .sari-risk-state.is-alert {
        border-color: #ead9b5;
        background: #fff9ef;
    }

    .sari-risk-state-icon {
        display: grid;
        height: 38px;
        width: 38px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 50%;
        background: #e7f4ed;
        color: #34805b;
    }

    .sari-risk-state.is-alert .sari-risk-state-icon {
        background: #fff0d7;
        color: #a76d12;
    }

    .sari-risk-state-title {
        font-size: 10px;
        font-weight: 700;
        color: #2f6e50;
    }

    .sari-risk-state.is-alert .sari-risk-state-title {
        color: #8b5d14;
    }

    .sari-risk-state-copy {
        margin-top: 2px;
        font-size: 8.5px;
        line-height: 1.55;
        color: #7d8d83;
    }

    .sari-risk-state.is-alert .sari-risk-state-copy {
        color: #95794c;
    }

    .sari-rider-body {
        padding: 0 20px 18px;
    }

    .sari-rider-main {
        display: grid;
        gap: 18px;
        align-items: center;
        padding-top: 2px;
    }

    .sari-rider-donut-wrap {
        display: flex;
        justify-content: center;
    }

    .sari-rider-donut-v2 {
        position: relative;
        display: grid;
        height: 154px;
        width: 154px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 50%;
        box-shadow: 0 8px 20px rgba(68, 53, 35, .05);
    }

    .sari-rider-donut-v2::after {
        content: "";
        position: absolute;
        inset: 25px;
        border-radius: inherit;
        background: #fff;
        box-shadow: inset 0 0 0 1px #eee7de;
    }

    .sari-rider-donut-center {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .sari-rider-total {
        font-size: 28px;
        line-height: 1;
        font-weight: 700;
        letter-spacing: -.045em;
        color: #211d17;
    }

    .sari-rider-total-label {
        margin-top: 6px;
        font-size: 8px;
        color: #958c82;
    }

    .sari-rider-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        column-gap: 24px;
    }

    .sari-rider-stat {
        display: flex;
        min-width: 0;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        min-height: 44px;
        border-bottom: 1px solid #f0ebe4;
    }

    .sari-rider-stat-name {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 9px;
        font-size: 9px;
        color: #716960;
    }

    .sari-rider-stat-name span:last-child {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sari-rider-stat-dot {
        height: 9px;
        width: 9px;
        flex: 0 0 auto;
        border-radius: 50%;
    }

    .sari-rider-stat-value {
        flex: 0 0 auto;
        font-size: 10px;
        font-weight: 700;
        color: #2f2923;
    }

    .sari-rider-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-top: 16px;
        overflow: hidden;
        border: 1px solid #ebe4da;
        border-radius: 14px;
        background: #fff;
    }

    .sari-rider-summary-item {
        min-width: 0;
        padding: 13px 15px;
    }

    .sari-rider-summary-item + .sari-rider-summary-item {
        border-left: 1px solid #eee8df;
    }

    .sari-rider-summary-label {
        font-size: 8px;
        color: #958c82;
    }

    .sari-rider-summary-value {
        margin-top: 5px;
        font-size: 16px;
        line-height: 1;
        font-weight: 700;
        letter-spacing: -.025em;
        color: #211d17;
    }

    @media (min-width: 900px) {
        .sari-rider-main {
            grid-template-columns: 190px minmax(0, 1fr);
        }
    }

    @media (min-width: 1280px) {
        .sari-monitor-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 639px) {
        .sari-monitor-head {
            padding: 16px;
        }

        .sari-risk-body,
        .sari-rider-body {
            padding: 0 16px 16px;
        }

        .sari-risk-item {
            grid-template-columns: auto minmax(0, 1fr);
        }

        .sari-risk-live {
            display: none;
        }

        .sari-rider-stats {
            grid-template-columns: 1fr;
        }

        .sari-rider-summary {
            grid-template-columns: 1fr;
        }

        .sari-rider-summary-item + .sari-rider-summary-item {
            border-top: 1px solid #eee8df;
            border-left: 0;
        }
    }


    /* ============================================================
       SALES + RECENT REGISTRATIONS — CLEAN MODERN V6
       Flat hierarchy, live chart controls, minimal registration list.
    ============================================================ */
    .sari-analytics-clean-grid {
        display: grid;
        gap: 16px;
        align-items: start;
    }

    .sari-sales-clean-card,
    .sari-reg-clean-card {
        min-width: 0;
        border: 1px solid #e8e1d8;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(70, 55, 37, .035);
    }

    .sari-sales-clean-card {
        padding: 22px;
    }

    .sari-reg-clean-card {
        padding: 22px 20px;
    }

    .sari-sales-clean-head,
    .sari-reg-clean-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .sari-sales-clean-title,
    .sari-reg-clean-title {
        font-size: 17px;
        line-height: 1.25;
        font-weight: 700;
        letter-spacing: -.025em;
        color: #211d17;
    }

    .sari-sales-clean-subtitle,
    .sari-reg-clean-subtitle {
        margin-top: 4px;
        font-size: 9.5px;
        line-height: 1.5;
        color: #978f84;
    }

    .sari-sales-clean-period {
        display: inline-flex;
        min-height: 38px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 1px solid #e7e0d7;
        border-radius: 12px;
        background: #fff;
        padding: 0 14px;
        font-size: 9px;
        font-weight: 700;
        color: #564d43;
    }

    .sari-sales-clean-metrics {
        display: grid;
        grid-template-columns: 1fr;
        margin-top: 22px;
    }

    .sari-sales-clean-metric {
        min-width: 0;
        padding: 8px 0 14px;
    }

    .sari-sales-clean-metric-label {
        font-size: 9.5px;
        font-weight: 500;
        color: #7f776e;
    }

    .sari-sales-clean-metric-value {
        margin-top: 5px;
        font-size: 25px;
        line-height: 1;
        font-weight: 700;
        letter-spacing: -.045em;
        color: #211d17;
    }

    .sari-sales-clean-metric-note {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        font-size: 9px;
        font-weight: 650;
        color: #72695f;
    }

    .sari-sales-clean-metric-note.is-positive > span:first-child { color: #2f8b60; }
    .sari-sales-clean-metric-note.is-negative > span:first-child { color: #bf5057; }
    .sari-sales-clean-metric-note .is-muted { color: #948c82; font-weight: 500; }

    .sari-sales-clean-divider {
        height: 1px;
        margin-top: 4px;
        background: #f0ebe5;
    }

    .sari-sales-clean-chart-head {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 18px 0 8px;
    }

    .sari-sales-clean-chart-title {
        font-size: 11.5px;
        font-weight: 700;
        color: #302a24;
    }

    .sari-sales-clean-chart-copy {
        margin-top: 3px;
        font-size: 8.5px;
        line-height: 1.5;
        color: #9a9288;
    }

    .sari-sales-clean-legend {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
    }

    .sari-sales-clean-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 2px 0;
        font-size: 9px;
        font-weight: 500;
        color: #746c63;
        transition: opacity .14s ease, color .14s ease;
    }

    .sari-sales-clean-legend-item:hover { color: #433b33; }
    .sari-sales-clean-legend-item[aria-pressed="false"] { opacity: .38; }

    .sari-sales-clean-dot {
        display: block;
        height: 8px;
        width: 8px;
        border-radius: 50%;
    }

    .sari-sales-clean-dot.is-sales { background: #c79229; }
    .sari-sales-clean-dot.is-commission { background: #d2cbc2; }

    .sari-sales-clean-chart {
        position: relative;
        min-height: 250px;
        padding-top: 2px;
    }

    .sari-sales-clean-chart svg {
        display: block;
        width: 100%;
        height: auto;
        overflow: visible;
    }

    .sari-sales-clean-grid-line {
        stroke: #eee9e3;
        stroke-width: 1;
        stroke-dasharray: 4 7;
    }

    .sari-sales-clean-guide-line {
        stroke: #f3efea;
        stroke-width: 1;
    }

    .sari-sales-clean-base-line {
        stroke: #dfd8cf;
        stroke-width: 1;
    }

    .sari-sales-clean-axis {
        fill: #9a9288;
        font-size: 9px;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
    }

    .sari-sales-clean-line {
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
        transition: opacity .14s ease;
    }

    .sari-sales-clean-line.is-sales {
        stroke: #c79229;
        stroke-width: 2.3;
    }

    .sari-sales-clean-line.is-commission {
        stroke: #d3ccc3;
        stroke-width: 1.7;
    }

    .sari-sales-clean-area {
        transition: opacity .14s ease;
    }

    .sari-sales-clean-point {
        fill: #fff;
        transition: opacity .14s ease, r .14s ease;
    }

    .sari-sales-clean-point.is-sales {
        stroke: #c79229;
        stroke-width: 2;
    }

    .sari-sales-clean-point.is-commission {
        stroke: #d3ccc3;
        stroke-width: 1.6;
    }

    [data-sales-series].is-series-hidden {
        opacity: 0 !important;
        pointer-events: none;
    }

    .sari-sales-clean-hitbox {
        fill: transparent;
        cursor: crosshair;
        outline: none;
    }

    .sari-sales-clean-hover-guide {
        stroke: #d8c59e;
        stroke-width: 1;
        stroke-dasharray: 4 5;
        opacity: 0;
        pointer-events: none;
        transition: opacity .12s ease;
    }

    .sari-sales-clean-hover-guide.is-visible { opacity: 1; }

    .sari-sales-clean-tooltip {
        position: absolute;
        z-index: 10;
        min-width: 142px;
        border: 1px solid #e7dfd6;
        border-radius: 11px;
        background: rgba(255,255,255,.97);
        padding: 10px 11px;
        box-shadow: 0 10px 24px rgba(57,44,28,.09);
        opacity: 0;
        transform: translateY(5px);
        pointer-events: none;
        transition: opacity .12s ease, transform .12s ease;
    }

    .sari-sales-clean-tooltip.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    .sari-sales-clean-tooltip-week {
        margin-bottom: 7px;
        font-size: 8px;
        font-weight: 700;
        color: #756c62;
    }

    .sari-sales-clean-tooltip-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-top: 5px;
        font-size: 8px;
        color: #8a8178;
    }

    .sari-sales-clean-tooltip-row span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .sari-sales-clean-tooltip-row i {
        display: block;
        height: 6px;
        width: 6px;
        border-radius: 50%;
    }

    .sari-sales-clean-tooltip-row i.is-sales { background: #c79229; }
    .sari-sales-clean-tooltip-row i.is-commission { background: #d2cbc2; }
    .sari-sales-clean-tooltip-row strong { font-size: 8.5px; color: #2b261f; }

    .sari-sales-clean-empty {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 8px;
        border-radius: 14px;
        background: #fcf8ef;
        padding: 14px 16px;
    }

    .sari-sales-clean-empty-icon {
        display: grid;
        height: 36px;
        width: 36px;
        flex: 0 0 auto;
        place-items: center;
        border-radius: 50%;
        background: #fff4da;
        color: #b67d18;
    }

    .sari-sales-clean-empty-title {
        font-size: 10px;
        font-weight: 700;
        color: #302a24;
    }

    .sari-sales-clean-empty-copy {
        margin-top: 3px;
        font-size: 8.5px;
        line-height: 1.5;
        color: #928a80;
    }

    .sari-reg-clean-head {
        padding-bottom: 17px;
        border-bottom: 1px solid #f0ebe5;
    }

    .sari-reg-clean-view-all {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        gap: 6px;
        padding-top: 2px;
        font-size: 9px;
        font-weight: 700;
        color: #af7414;
        transition: color .14s ease;
    }

    .sari-reg-clean-view-all:hover { color: #8f5d0c; }

    .sari-reg-clean-list {
        margin-top: 3px;
    }

    .sari-reg-clean-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto auto;
        align-items: center;
        gap: 12px;
        min-height: 70px;
        border-bottom: 1px solid #f0ebe5;
        color: inherit;
        transition: background-color .14s ease;
    }

    .sari-reg-clean-row:last-child { border-bottom: 0; }
    .sari-reg-clean-row:hover { background: #fdfbf8; }

    .sari-reg-clean-person { min-width: 0; }

    .sari-reg-clean-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 10.5px;
        font-weight: 650;
        color: #2c2721;
    }

    .sari-reg-clean-role {
        margin-top: 3px;
        font-size: 8.5px;
        color: #968e84;
    }

    .sari-reg-clean-status {
        font-size: 8.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .sari-reg-clean-status.is-approved { color: #2e865b; }
    .sari-reg-clean-status.is-pending { color: #ad7415; }
    .sari-reg-clean-status.is-rejected { color: #bd565b; }

    .sari-reg-clean-time {
        min-width: 72px;
        text-align: right;
        font-size: 8px;
        color: #9b9389;
        white-space: nowrap;
    }

    .sari-reg-clean-empty {
        padding: 22px 0 8px;
    }

    .sari-reg-clean-empty-title {
        font-size: 10px;
        font-weight: 650;
        color: #4c443c;
    }

    .sari-reg-clean-empty-copy {
        margin-top: 4px;
        font-size: 8.5px;
        line-height: 1.5;
        color: #9a9288;
    }

    @media (min-width: 640px) {
        .sari-sales-clean-metrics {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .sari-sales-clean-metric {
            padding: 8px 24px 15px;
        }

        .sari-sales-clean-metric:first-child { padding-left: 0; }
        .sari-sales-clean-metric:last-child { padding-right: 0; }

        .sari-sales-clean-metric + .sari-sales-clean-metric {
            border-left: 1px solid #f0ebe5;
        }

        .sari-sales-clean-chart-head {
            flex-direction: row;
            align-items: flex-start;
            justify-content: space-between;
        }
    }

    @media (min-width: 1280px) {
        .sari-analytics-clean-grid {
            grid-template-columns: minmax(0, 1.72fr) minmax(300px, .68fr);
        }
    }

    @media (max-width: 639px) {
        .sari-sales-clean-card,
        .sari-reg-clean-card {
            padding: 17px 16px;
        }

        .sari-sales-clean-head,
        .sari-reg-clean-head {
            gap: 10px;
        }

        .sari-sales-clean-metric + .sari-sales-clean-metric {
            border-top: 1px solid #f0ebe5;
        }

        .sari-reg-clean-row {
            grid-template-columns: minmax(0, 1fr) auto;
            min-height: 64px;
        }

        .sari-reg-clean-time {
            display: none;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-sales-clean-legend-item,
        .sari-sales-clean-line,
        .sari-sales-clean-area,
        .sari-sales-clean-point,
        .sari-sales-clean-tooltip,
        .sari-reg-clean-row {
            transition: none !important;
        }
    }


    /* ============================================================
       STRONG FLOATING DEPTH — requested thicker neumorphism shadow
       Keeps the warm SARI palette while making cards visibly elevated.
    ============================================================ */
    .admin-panel,
    .admin-control-panel,
    .admin-kpi,
    .sari-health-card,
    .sari-ops-card,
    .sari-monitor-card,
    .sari-sales-clean-card,
    .sari-reg-clean-card {
        border-color: #e3dbcf !important;
        background: #ffffff !important;
        box-shadow:
            0 20px 42px rgba(58, 45, 29, .145),
            0 7px 16px rgba(58, 45, 29, .075),
            -7px -7px 18px rgba(255, 255, 255, .98) !important;
    }

    .admin-kpi,
    .sari-health-card,
    .sari-ops-card,
    .sari-monitor-card,
    .sari-sales-clean-card,
    .sari-reg-clean-card {
        transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
    }

    .admin-kpi:hover,
    .sari-health-card:hover,
    .sari-ops-card:hover,
    .sari-monitor-card:hover,
    .sari-sales-clean-card:hover,
    .sari-reg-clean-card:hover {
        transform: translateY(-2px);
        border-color: #d9cebe !important;
        box-shadow:
            0 26px 54px rgba(58, 45, 29, .18),
            0 9px 20px rgba(58, 45, 29, .09),
            -8px -8px 20px rgba(255, 255, 255, 1) !important;
    }

    .sari-health-score-card,
    .sari-health-metric-v2,
    .sari-health-summary-card,
    .sari-risk-list,
    .sari-risk-state,
    .sari-rider-summary,
    .sari-sales-clean-empty,
    .sari-sales-clean-period {
        box-shadow:
            0 10px 22px rgba(58, 45, 29, .075),
            -4px -4px 10px rgba(255, 255, 255, .96);
    }



    /* ============================================================
       STATIC CONTAINERS — hover only for KPI summary cards + buttons
       Keep the floating depth, remove cursor-triggered effects on panels/rows.
    ============================================================ */
    .admin-panel,
    .admin-control-panel,
    .sari-health-card,
    .sari-ops-card,
    .sari-monitor-card,
    .sari-sales-clean-card,
    .sari-reg-clean-card {
        transition: none !important;
        transform: none !important;
    }

    .admin-panel:hover,
    .admin-control-panel:hover,
    .sari-health-card:hover,
    .sari-ops-card:hover,
    .sari-monitor-card:hover,
    .sari-sales-clean-card:hover,
    .sari-reg-clean-card:hover {
        transform: none !important;
        border-color: #e3dbcf !important;
        background: #ffffff !important;
        box-shadow:
            0 20px 42px rgba(58, 45, 29, .145),
            0 7px 16px rgba(58, 45, 29, .075),
            -7px -7px 18px rgba(255, 255, 255, .98) !important;
    }

    .admin-role-card {
        transition: none !important;
        transform: none !important;
    }

    .admin-role-card:hover {
        transform: none !important;
        border-color: #ece6dd !important;
    }

    .sari-role-row,
    .sari-focus-row-v2,
    .sari-risk-item,
    .sari-reg-clean-row {
        transition: none !important;
    }

    .sari-role-row:hover,
    .sari-focus-row-v2:hover,
    .sari-risk-item:hover,
    .sari-reg-clean-row:hover {
        background: transparent !important;
    }

    .sari-role-row:hover .sari-role-chevron {
        background: transparent !important;
        color: #9a8b77 !important;
    }


    /* ============================================================
       SARI ADMIN DASHBOARD — FINAL COMPACT SIZING LAYER
       Visual sizing only. No backend, Blade data, routes, or JS
       behavior is changed by this layer.
       ============================================================ */

    .sari-admin-dashboard {
        width: 100%;
        max-width: 1640px !important;
    }

    /* Shared panels */
    .admin-panel,
    .admin-control-panel,
    .sari-health-card,
    .sari-ops-card,
    .sari-monitor-card,
    .sari-sales-clean-card,
    .sari-reg-clean-card {
        border-radius: 16px !important;
    }

    /* ============================================================
       HERO
       ============================================================ */
    .sari-hero {
        border-radius: 18px !important;
        padding: 20px 24px !important;
    }

    .sari-hero > .relative {
        gap: 20px !important;
    }

    .sari-hero-kicker {
        font-size: 7.5px !important;
        letter-spacing: .20em !important;
    }

    .sari-hero-title {
        margin-top: 9px !important;
        gap: 8px !important;
        font-size: 24px !important;
        line-height: 1.12 !important;
    }

    .sari-hero-wave .fi {
        font-size: 22px !important;
    }

    .sari-hero-copy {
        max-width: 590px !important;
        margin-top: 8px !important;
        font-size: 10.5px !important;
        line-height: 1.65 !important;
    }

    .sari-hero-side {
        gap: 13px !important;
    }

    .sari-hero-stat {
        min-width: 102px !important;
        padding: 3px 0 !important;
    }

    .sari-hero-stat + .sari-hero-stat {
        padding-left: 13px !important;
    }

    .sari-hero-stat-label {
        font-size: 7px !important;
        letter-spacing: .11em !important;
    }

    .sari-hero-stat-row {
        margin-top: 5px !important;
        gap: 6px !important;
    }

    .sari-hero-stat-icon {
        width: 24px !important;
        height: 24px !important;
    }

    .sari-hero-stat-icon .fi {
        font-size: 10px !important;
    }

    .sari-hero-stat-value {
        font-size: 12.5px !important;
    }

    .sari-hero-stat-sub {
        margin-top: 2px !important;
        padding-left: 30px !important;
        font-size: 7px !important;
    }

    .sari-hero-brand {
        min-width: 94px !important;
    }

    .sari-hero-brand-inner {
        padding-left: 13px !important;
    }

    .sari-hero-brand-mark {
        font-size: 6.5px !important;
    }

    .sari-hero-brand-copy {
        margin-top: 7px !important;
        font-size: 8px !important;
        line-height: 1.45 !important;
    }

    /* ============================================================
       KPI SUMMARY CARDS
       ============================================================ */
    .admin-kpi {
        border-radius: 13px !important;
        padding: 12px !important;
    }

    .admin-icon-box {
        width: 34px !important;
        height: 34px !important;
        border-radius: 10px !important;
    }

    .admin-icon-box svg {
        width: 16px !important;
        height: 16px !important;
    }

    .admin-kpi > div:first-child > div:first-child > p:first-child {
        font-size: 8.8px !important;
    }

    .admin-kpi > div:first-child > div:first-child > p:nth-child(2) {
        margin-top: 4px !important;
        font-size: 19px !important;
    }

    .admin-kpi > div:last-child {
        margin-top: 8px !important;
        gap: 6px !important;
        font-size: 8px !important;
    }

    /* ============================================================
       CONTROL TOWER INTRO
       ============================================================ */
    #adminControlTower {
        margin-top: 13px !important;
    }

    #adminControlTower > div:first-child {
        margin-bottom: 9px !important;
        gap: 8px !important;
    }

    #adminControlTower > div:first-child p:first-child {
        font-size: 7px !important;
    }

    #adminControlTower > div:first-child h3 {
        margin-top: 3px !important;
        font-size: 14px !important;
    }

    #adminControlTower > div:first-child h3 + p {
        margin-top: 3px !important;
        font-size: 8.8px !important;
    }

    .admin-focus-chip {
        min-height: 27px !important;
        padding: 0 9px !important;
        font-size: 8px !important;
    }

    /* ============================================================
       MARKETPLACE HEALTH
       ============================================================ */
    .sari-health-card {
        padding: 17px !important;
    }

    .sari-health-header {
        gap: 10px !important;
    }

    .sari-health-heading {
        gap: 9px !important;
    }

    .sari-health-icon {
        width: 34px !important;
        height: 34px !important;
        border-radius: 10px !important;
    }

    .sari-health-icon svg {
        width: 16px !important;
        height: 16px !important;
    }

    .sari-health-title {
        font-size: 12px !important;
    }

    .sari-health-subtitle {
        margin-top: 2px !important;
        font-size: 8px !important;
    }

    .sari-health-actions {
        gap: 6px !important;
    }

    .sari-health-stable {
        min-height: 29px !important;
        padding: 0 10px !important;
        font-size: 8px !important;
    }

    .sari-health-action {
        min-height: 30px !important;
        padding: 0 11px !important;
        font-size: 8px !important;
    }

    .sari-health-main {
        gap: 10px !important;
        margin-top: 13px !important;
    }

    .sari-health-score-card {
        border-radius: 13px !important;
        padding: 14px 11px !important;
    }

    .admin-health-score {
        width: 136px !important;
        height: 136px !important;
    }

    .admin-health-score::before {
        inset: 13px !important;
    }

    .admin-health-score-content > p:first-child {
        font-size: 29px !important;
    }

    .admin-health-grade {
        margin-top: 5px !important;
        padding: 3px 7px !important;
        font-size: 7px !important;
    }

    .sari-health-score-copy {
        margin-top: 10px !important;
        font-size: 8px !important;
        line-height: 1.55 !important;
    }

    .sari-health-metrics {
        gap: 8px !important;
    }

    .sari-health-metric-v2 {
        border-radius: 13px !important;
        padding: 11px !important;
    }

    .sari-health-metric-top {
        gap: 8px !important;
    }

    .sari-health-metric-icon {
        width: 31px !important;
        height: 31px !important;
        border-radius: 9px !important;
    }

    .sari-health-metric-icon svg {
        width: 15px !important;
        height: 15px !important;
    }

    .sari-health-metric-label {
        font-size: 8.5px !important;
    }

    .sari-health-metric-note {
        font-size: 7px !important;
    }

    .sari-health-metric-value {
        font-size: 12px !important;
    }

    .sari-health-progress {
        height: 5px !important;
        margin-top: 9px !important;
    }

    .sari-health-summary {
        gap: 8px !important;
        margin-top: 10px !important;
        padding-top: 10px !important;
    }

    .sari-health-summary-card {
        gap: 8px !important;
        border-radius: 12px !important;
        padding: 10px !important;
    }

    .sari-health-summary-icon {
        width: 30px !important;
        height: 30px !important;
        border-radius: 9px !important;
    }

    .sari-health-summary-icon svg {
        width: 14px !important;
        height: 14px !important;
    }

    .sari-health-summary-label {
        font-size: 7px !important;
    }

    .sari-health-summary-value {
        font-size: 11.5px !important;
    }

    .sari-health-summary-note {
        font-size: 6.8px !important;
    }

    /* ============================================================
       RECENT REGISTRATIONS
       ============================================================ */
    .sari-reg-clean-card {
        padding: 17px 16px !important;
    }

    .sari-reg-clean-head {
        gap: 10px !important;
        padding-bottom: 12px !important;
    }

    .sari-reg-clean-title {
        font-size: 14px !important;
    }

    .sari-reg-clean-subtitle {
        margin-top: 3px !important;
        font-size: 8px !important;
    }

    .sari-reg-clean-view-all {
        font-size: 8px !important;
    }

    .sari-reg-clean-view-all svg {
        width: 13px !important;
        height: 13px !important;
    }

    .sari-reg-clean-row {
        min-height: 56px !important;
        gap: 9px !important;
    }

    .sari-reg-clean-name {
        font-size: 9.5px !important;
    }

    .sari-reg-clean-role,
    .sari-reg-clean-status {
        font-size: 7.5px !important;
    }

    .sari-reg-clean-time {
        min-width: 64px !important;
        font-size: 7px !important;
    }

    /* ============================================================
       ROLE CONTROL + ORDER JOURNEY
       ============================================================ */
    .sari-ops-pair {
        gap: 12px !important;
        margin-top: 13px !important;
    }

    .sari-ops-head {
        gap: 10px !important;
        padding: 14px 16px 11px !important;
    }

    .sari-ops-heading {
        gap: 8px !important;
    }

    .admin-section-icon {
        width: 31px !important;
        height: 31px !important;
        border-radius: 9px !important;
    }

    .admin-section-icon svg {
        width: 15px !important;
        height: 15px !important;
    }

    .sari-ops-title {
        font-size: 12px !important;
    }

    .sari-ops-subtitle {
        margin-top: 2px !important;
        font-size: 8px !important;
    }

    .sari-ops-action {
        min-height: 29px !important;
        padding: 0 10px !important;
        font-size: 8px !important;
    }

    .sari-role-table {
        margin: 0 16px 14px !important;
    }

    .sari-role-table-head {
        min-height: 30px !important;
        font-size: 7px !important;
    }

    .sari-role-row {
        min-height: 45px !important;
        gap: 9px !important;
    }

    .sari-role-name {
        gap: 8px !important;
        font-size: 9px !important;
    }

    .sari-role-icon {
        width: 27px !important;
        height: 27px !important;
        border-radius: 8px !important;
    }

    .sari-role-icon svg {
        width: 14px !important;
        height: 14px !important;
    }

    .sari-role-value {
        font-size: 13px !important;
    }

    .sari-role-pending {
        font-size: 8px !important;
    }

    .sari-role-chevron {
        width: 24px !important;
        height: 24px !important;
    }

    .sari-journey-body {
        padding: 4px 16px 14px !important;
    }

    .sari-journey-track {
        min-width: 560px !important;
        grid-template-columns: repeat(6, minmax(74px, 1fr)) !important;
    }

    .sari-journey-step:not(:last-child)::after {
        top: 19px !important;
        left: calc(50% + 21px) !important;
        width: calc(100% - 42px) !important;
    }

    .sari-journey-icon {
        width: 38px !important;
        height: 38px !important;
    }

    .sari-journey-icon svg {
        width: 15px !important;
        height: 15px !important;
    }

    .sari-journey-name {
        margin-top: 6px !important;
        font-size: 7.5px !important;
    }

    .sari-journey-count {
        margin-top: 3px !important;
        font-size: 13px !important;
    }

    .sari-journey-status {
        gap: 8px !important;
        margin-top: 12px !important;
        border-radius: 10px !important;
        padding: 8px 10px !important;
    }

    .sari-journey-status-icon {
        width: 23px !important;
        height: 23px !important;
    }

    .sari-journey-status-title {
        font-size: 9px !important;
    }

    .sari-journey-status-copy {
        font-size: 7.5px !important;
    }

    /* ============================================================
       INTERVENTION + RIDER / LOGISTICS
       ============================================================ */
    .sari-monitor-grid {
        gap: 12px !important;
        margin-top: 13px !important;
    }

    .sari-monitor-head {
        gap: 10px !important;
        padding: 14px 16px 11px !important;
    }

    .sari-monitor-heading {
        gap: 9px !important;
    }

    .sari-monitor-icon {
        width: 34px !important;
        height: 34px !important;
        border-radius: 10px !important;
    }

    .sari-monitor-icon svg {
        width: 16px !important;
        height: 16px !important;
    }

    .sari-monitor-title {
        font-size: 12px !important;
    }

    .sari-monitor-subtitle {
        margin-top: 2px !important;
        font-size: 8px !important;
    }

    .sari-monitor-action {
        min-height: 30px !important;
        padding: 0 10px !important;
        font-size: 8px !important;
    }

    .sari-risk-body,
    .sari-rider-body {
        padding: 0 16px 14px !important;
    }

    .sari-risk-list {
        border-radius: 11px !important;
    }

    .sari-risk-item {
        min-height: 47px !important;
        gap: 10px !important;
        padding: 8px 11px !important;
    }

    .sari-risk-pill {
        min-height: 22px !important;
        min-width: 45px !important;
        padding: 0 8px !important;
        font-size: 7px !important;
    }

    .sari-risk-name {
        font-size: 9px !important;
    }

    .sari-risk-live {
        font-size: 7px !important;
    }

    .sari-risk-live-dot {
        width: 6px !important;
        height: 6px !important;
    }

    .sari-risk-state {
        gap: 9px !important;
        margin-top: 10px !important;
        border-radius: 12px !important;
        padding: 10px 11px !important;
    }

    .sari-risk-state-icon {
        width: 31px !important;
        height: 31px !important;
    }

    .sari-risk-state-title {
        font-size: 9px !important;
    }

    .sari-risk-state-copy {
        font-size: 7.5px !important;
    }

    .sari-rider-main {
        gap: 14px !important;
    }

    .sari-rider-donut-v2 {
        width: 130px !important;
        height: 130px !important;
    }

    .sari-rider-donut-v2::after {
        inset: 22px !important;
    }

    .sari-rider-total {
        font-size: 23px !important;
    }

    .sari-rider-total-label {
        margin-top: 4px !important;
        font-size: 7px !important;
    }

    .sari-rider-stats {
        column-gap: 18px !important;
    }

    .sari-rider-stat {
        min-height: 37px !important;
    }

    .sari-rider-stat-name {
        gap: 7px !important;
        font-size: 8px !important;
    }

    .sari-rider-stat-dot {
        width: 7px !important;
        height: 7px !important;
    }

    .sari-rider-stat-value {
        font-size: 9px !important;
    }

    .sari-rider-summary {
        margin-top: 12px !important;
        border-radius: 11px !important;
    }

    .sari-rider-summary-item {
        padding: 10px 12px !important;
    }

    .sari-rider-summary-label {
        font-size: 7px !important;
    }

    .sari-rider-summary-value {
        margin-top: 4px !important;
        font-size: 14px !important;
    }

    /* ============================================================
       SALES OVERVIEW
       ============================================================ */
    .sari-analytics-clean-grid {
        gap: 12px !important;
        margin-top: 13px !important;
    }

    .sari-sales-clean-card {
        padding: 17px !important;
    }

    .sari-sales-clean-head {
        gap: 10px !important;
    }

    .sari-sales-clean-title {
        font-size: 14px !important;
    }

    .sari-sales-clean-subtitle {
        margin-top: 3px !important;
        font-size: 8px !important;
    }

    .sari-sales-clean-period {
        min-height: 31px !important;
        padding: 0 10px !important;
        font-size: 8px !important;
    }

    .sari-sales-clean-metrics {
        margin-top: 14px !important;
    }

    .sari-sales-clean-metric {
        padding-top: 5px !important;
        padding-bottom: 10px !important;
    }

    .sari-sales-clean-metric-label {
        font-size: 8.5px !important;
    }

    .sari-sales-clean-metric-value {
        margin-top: 4px !important;
        font-size: 21px !important;
    }

    .sari-sales-clean-metric-note {
        gap: 5px !important;
        margin-top: 6px !important;
        font-size: 8px !important;
    }

    .sari-sales-clean-chart-head {
        gap: 9px !important;
        padding: 13px 0 6px !important;
    }

    .sari-sales-clean-chart-title {
        font-size: 10px !important;
    }

    .sari-sales-clean-chart-copy {
        font-size: 7.5px !important;
    }

    .sari-sales-clean-legend {
        gap: 10px !important;
    }

    .sari-sales-clean-legend-item {
        gap: 5px !important;
        font-size: 8px !important;
    }

    .sari-sales-clean-dot {
        width: 6px !important;
        height: 6px !important;
    }

    .sari-sales-clean-chart {
        min-height: 210px !important;
    }

    .sari-sales-clean-axis {
        font-size: 8px !important;
    }

    .sari-sales-clean-tooltip {
        min-width: 128px !important;
        border-radius: 9px !important;
        padding: 8px 9px !important;
    }

    .sari-sales-clean-tooltip-week,
    .sari-sales-clean-tooltip-row {
        font-size: 7px !important;
    }

    .sari-sales-clean-tooltip-row strong {
        font-size: 7.5px !important;
    }

    /* ============================================================
       TODAY'S FOCUS
       ============================================================ */
    .sari-focus-list-v2 {
        margin-top: 10px !important;
    }

    .sari-focus-row-v2 {
        grid-template-columns: 31px minmax(0, 1fr) auto auto 19px !important;
        gap: 8px !important;
        padding: 8px 0 !important;
    }

    .sari-focus-icon-v2 {
        width: 29px !important;
        height: 29px !important;
        border-radius: 8px !important;
    }

    .sari-focus-icon-v2 svg {
        width: 14px !important;
        height: 14px !important;
    }

    .sari-focus-title-v2 {
        font-size: 8.8px !important;
    }

    .sari-focus-copy-v2 {
        font-size: 7.2px !important;
    }

    .sari-focus-count-v2 {
        font-size: 11px !important;
    }

    .sari-focus-badge-v2 {
        min-height: 23px !important;
        padding: 0 8px !important;
        font-size: 7px !important;
    }

    .sari-focus-chevron-v2 {
        width: 19px !important;
        height: 19px !important;
    }

    /* ============================================================
       BOTTOM CATEGORY / GENERIC PANELS
       ============================================================ */
    #adminCategoryBreakdown {
        padding: 17px !important;
    }

    #adminCategoryBreakdown > div:first-child h3 {
        font-size: 13px !important;
    }

    #adminCategoryBreakdown > div:first-child p {
        font-size: 8.5px !important;
    }

    .admin-view-button {
        min-height: 30px !important;
        padding: 0 10px !important;
        font-size: 8.5px !important;
    }

    .admin-donut-premium {
        width: 145px !important;
        height: 145px !important;
    }

    .admin-donut-premium::before {
        inset: 26px !important;
    }

    .admin-legend-row {
        gap: 9px !important;
        padding: 7px 0 !important;
    }

    .admin-legend-bar {
        height: 4px !important;
    }

    /* ============================================================
       COMMON LAPTOP TUNING — 100% browser zoom
       ============================================================ */
    @media (max-height: 850px) and (min-width: 900px) {
        .sari-admin-dashboard {
            max-width: 1540px !important;
        }

        .sari-hero {
            padding: 17px 20px !important;
        }

        .sari-hero-title {
            font-size: 22px !important;
        }

        .sari-hero-copy {
            font-size: 9.8px !important;
        }

        .admin-kpi {
            padding: 10px !important;
        }

        .admin-kpi > div:first-child > div:first-child > p:nth-child(2) {
            font-size: 18px !important;
        }

        .sari-health-card,
        .sari-sales-clean-card,
        .sari-reg-clean-card {
            padding: 15px !important;
        }

        .admin-health-score {
            width: 124px !important;
            height: 124px !important;
        }

        .sari-rider-donut-v2 {
            width: 118px !important;
            height: 118px !important;
        }

        .sari-sales-clean-chart {
            min-height: 190px !important;
        }
    }

    /* ============================================================
       TABLET / MOBILE — keep readable touch targets
       ============================================================ */
    @media (max-width: 767px) {
        .sari-admin-dashboard {
            max-width: 100% !important;
        }

        .sari-hero {
            padding: 18px 16px !important;
        }

        .sari-hero-title {
            font-size: 21px !important;
        }

        .sari-hero-copy {
            font-size: 10.5px !important;
        }

        .admin-kpi {
            padding: 13px !important;
        }

        .sari-health-card,
        .sari-sales-clean-card,
        .sari-reg-clean-card {
            padding: 16px !important;
        }

        .sari-health-action,
        .sari-ops-action,
        .sari-monitor-action,
        .admin-view-button {
            min-height: 36px !important;
            font-size: 9px !important;
        }

        .admin-focus-chip {
            min-height: 34px !important;
            font-size: 9px !important;
        }

        .sari-role-row {
            min-height: 50px !important;
        }

        .sari-focus-row-v2 {
            grid-template-columns: 31px minmax(0, 1fr) auto 19px !important;
        }

        .sari-focus-badge-v2 {
            display: none !important;
        }

        .sari-sales-clean-chart {
            min-height: 190px !important;
        }
    }


    /* ============================================================
       MARKETPLACE HEALTH — FLAT EXECUTIVE LAYOUT
       Keep one outer panel; remove nested card/container look.
       Visual-only override. Live Blade values and backend stay intact.
       ============================================================ */

    #adminMarketplaceHealth{
        padding:15px 16px !important;
        border-radius:15px !important;
        border-color:#e7e0d6 !important;
        background:#fff !important;
        box-shadow:0 8px 22px rgba(58,45,29,.055) !important;
    }

    #adminMarketplaceHealth .sari-health-header{
        gap:10px !important;
        padding-bottom:12px !important;
        border-bottom:1px solid #f0ebe4 !important;
    }

    #adminMarketplaceHealth .sari-health-heading{
        gap:8px !important;
        align-items:center !important;
    }

    #adminMarketplaceHealth .sari-health-icon{
        width:30px !important;
        height:30px !important;
        flex-basis:30px !important;
        border-radius:8px !important;
        background:#fff8ea !important;
        box-shadow:none !important;
    }

    #adminMarketplaceHealth .sari-health-icon svg{
        width:14px !important;
        height:14px !important;
    }

    #adminMarketplaceHealth .sari-health-title{
        font-size:11px !important;
        line-height:1.3 !important;
    }

    #adminMarketplaceHealth .sari-health-subtitle{
        margin-top:2px !important;
        font-size:7.4px !important;
        line-height:1.45 !important;
    }

    #adminMarketplaceHealth .sari-health-actions{
        gap:6px !important;
    }

    #adminMarketplaceHealth .sari-health-stable,
    #adminMarketplaceHealth .sari-health-action{
        min-height:27px !important;
        padding:0 9px !important;
        font-size:7px !important;
    }

    #adminMarketplaceHealth .sari-health-stable > span{
        width:6px !important;
        height:6px !important;
    }

    #adminMarketplaceHealth .sari-health-action svg{
        width:11px !important;
        height:11px !important;
    }

    /* Main health area: score left, flat metrics right. */
    #adminMarketplaceHealth .sari-health-main{
        grid-template-columns:160px minmax(0,1fr) !important;
        gap:18px !important;
        margin-top:14px !important;
        align-items:center !important;
    }

    /* Remove score inner card container completely. */
    #adminMarketplaceHealth .sari-health-score-card{
        min-height:auto !important;
        border:0 !important;
        border-radius:0 !important;
        background:transparent !important;
        padding:4px 10px 4px 0 !important;
        box-shadow:none !important;
        text-align:center !important;
    }

    #adminMarketplaceHealth .admin-health-score{
        width:112px !important;
        height:112px !important;
        margin-inline:auto !important;
        box-shadow:none !important;
    }

    #adminMarketplaceHealth .admin-health-score::before{
        inset:10px !important;
        box-shadow:inset 0 0 0 1px #eee7dc !important;
    }

    #adminMarketplaceHealth .admin-health-score-content > p:first-child{
        font-size:25px !important;
        line-height:1 !important;
    }

    #adminMarketplaceHealth .admin-health-score-content > p:nth-child(2){
        margin-top:0 !important;
        font-size:6px !important;
        letter-spacing:.10em !important;
    }

    #adminMarketplaceHealth .admin-health-grade{
        margin-top:4px !important;
        gap:4px !important;
        border:0 !important;
        background:#f2f7f3 !important;
        padding:3px 6px !important;
        font-size:6px !important;
        box-shadow:none !important;
    }

    #adminMarketplaceHealth .admin-health-grade > span{
        width:5px !important;
        height:5px !important;
    }

    #adminMarketplaceHealth .sari-health-score-copy{
        max-width:150px !important;
        margin:8px auto 0 !important;
        font-size:6.8px !important;
        line-height:1.5 !important;
    }

    /* Four health metrics: no cards, just clean rows. */
    #adminMarketplaceHealth .sari-health-metrics{
        grid-template-columns:repeat(2,minmax(0,1fr)) !important;
        gap:0 !important;
        border-left:1px solid #f0ebe4 !important;
    }

    #adminMarketplaceHealth .sari-health-metric-v2{
        min-height:78px !important;
        border:0 !important;
        border-radius:0 !important;
        background:transparent !important;
        padding:11px 14px !important;
        box-shadow:none !important;
    }

    #adminMarketplaceHealth .sari-health-metric-v2:nth-child(odd){
        border-right:1px solid #f0ebe4 !important;
    }

    #adminMarketplaceHealth .sari-health-metric-v2:nth-child(-n+2){
        border-bottom:1px solid #f0ebe4 !important;
    }

    #adminMarketplaceHealth .sari-health-metric-top{
        align-items:center !important;
        gap:8px !important;
    }

    /* Flat icon: remove icon container appearance. */
    #adminMarketplaceHealth .sari-health-metric-icon{
        width:22px !important;
        height:22px !important;
        flex-basis:22px !important;
        border-radius:0 !important;
        background:transparent !important;
        color:#b47b19 !important;
        box-shadow:none !important;
    }

    #adminMarketplaceHealth .sari-health-metric-icon svg{
        width:14px !important;
        height:14px !important;
    }

    #adminMarketplaceHealth .sari-health-metric-copy{
        min-width:0 !important;
    }

    #adminMarketplaceHealth .sari-health-metric-label{
        font-size:7.8px !important;
        line-height:1.3 !important;
    }

    #adminMarketplaceHealth .sari-health-metric-note{
        margin-top:2px !important;
        font-size:6.5px !important;
        line-height:1.35 !important;
    }

    #adminMarketplaceHealth .sari-health-metric-value{
        font-size:11px !important;
        line-height:1 !important;
    }

    #adminMarketplaceHealth .sari-health-progress{
        height:4px !important;
        margin-top:8px !important;
        border-radius:999px !important;
        background:#f0ece6 !important;
        box-shadow:none !important;
    }

    /* Bottom executive summary: no individual cards. */
    #adminMarketplaceHealth .sari-health-summary{
        grid-template-columns:repeat(3,minmax(0,1fr)) !important;
        gap:0 !important;
        margin-top:12px !important;
        padding-top:12px !important;
        border-top:1px solid #f0ebe4 !important;
    }

    #adminMarketplaceHealth .sari-health-summary-card{
        min-height:56px !important;
        gap:8px !important;
        border:0 !important;
        border-radius:0 !important;
        background:transparent !important;
        padding:5px 14px !important;
        box-shadow:none !important;
    }

    #adminMarketplaceHealth .sari-health-summary-card:first-child{
        padding-left:0 !important;
    }

    #adminMarketplaceHealth .sari-health-summary-card:last-child{
        padding-right:0 !important;
    }

    #adminMarketplaceHealth .sari-health-summary-card + .sari-health-summary-card{
        border-left:1px solid #f0ebe4 !important;
    }

    #adminMarketplaceHealth .sari-health-summary-icon{
        width:22px !important;
        height:22px !important;
        flex-basis:22px !important;
        border-radius:0 !important;
        background:transparent !important;
        box-shadow:none !important;
    }

    #adminMarketplaceHealth .sari-health-summary-icon svg{
        width:14px !important;
        height:14px !important;
    }

    #adminMarketplaceHealth .sari-health-summary-label{
        font-size:6px !important;
        letter-spacing:.07em !important;
    }

    #adminMarketplaceHealth .sari-health-summary-value{
        margin-top:1px !important;
        font-size:10px !important;
    }

    #adminMarketplaceHealth .sari-health-summary-note{
        margin-top:1px !important;
        font-size:6.2px !important;
        line-height:1.35 !important;
    }

    /* Override the older heavy inner depth layer specifically here. */
    #adminMarketplaceHealth .sari-health-score-card,
    #adminMarketplaceHealth .sari-health-metric-v2,
    #adminMarketplaceHealth .sari-health-summary-card{
        box-shadow:none !important;
        transform:none !important;
    }

    @media(max-height:850px) and (min-width:900px){
        #adminMarketplaceHealth{
            padding:13px 14px !important;
        }

        #adminMarketplaceHealth .sari-health-main{
            grid-template-columns:145px minmax(0,1fr) !important;
            gap:14px !important;
            margin-top:11px !important;
        }

        #adminMarketplaceHealth .admin-health-score{
            width:100px !important;
            height:100px !important;
        }

        #adminMarketplaceHealth .admin-health-score-content > p:first-child{
            font-size:22px !important;
        }

        #adminMarketplaceHealth .sari-health-metric-v2{
            min-height:68px !important;
            padding:9px 12px !important;
        }

        #adminMarketplaceHealth .sari-health-summary-card{
            min-height:49px !important;
        }
    }

    @media(max-width:1023px){
        #adminMarketplaceHealth .sari-health-main{
            grid-template-columns:1fr !important;
        }

        #adminMarketplaceHealth .sari-health-score-card{
            padding-right:0 !important;
        }

        #adminMarketplaceHealth .sari-health-metrics{
            border-left:0 !important;
            border-top:1px solid #f0ebe4 !important;
            padding-top:4px !important;
        }
    }

    @media(max-width:639px){
        #adminMarketplaceHealth{
            padding:14px !important;
        }

        #adminMarketplaceHealth .sari-health-metrics{
            grid-template-columns:1fr !important;
        }

        #adminMarketplaceHealth .sari-health-metric-v2{
            border-right:0 !important;
            border-bottom:1px solid #f0ebe4 !important;
        }

        #adminMarketplaceHealth .sari-health-metric-v2:last-child{
            border-bottom:0 !important;
        }

        #adminMarketplaceHealth .sari-health-summary{
            grid-template-columns:1fr !important;
        }

        #adminMarketplaceHealth .sari-health-summary-card,
        #adminMarketplaceHealth .sari-health-summary-card:first-child,
        #adminMarketplaceHealth .sari-health-summary-card:last-child{
            padding:9px 0 !important;
        }

        #adminMarketplaceHealth .sari-health-summary-card + .sari-health-summary-card{
            border-left:0 !important;
            border-top:1px solid #f0ebe4 !important;
        }
    }


    /* ============================================================
       SARI ADMIN — BALANCED ENTERPRISE ELEVATION SYSTEM
       70% flat / 30% subtle elevation.
       Designed for visual clarity + low paint/compositing overhead.
       ============================================================ */

    .sari-admin-dashboard{
        --sari-bg:#fbfaf7;
        --sari-surface:#ffffff;
        --sari-line:#e9e2d9;
        --sari-line-soft:#f0ebe4;
        --sari-shadow-section:0 6px 18px rgba(54,42,28,.050);
        --sari-shadow-kpi:0 5px 14px rgba(54,42,28,.055);
        --sari-shadow-kpi-hover:0 8px 18px rgba(54,42,28,.070);
        --sari-shadow-hero:0 12px 30px rgba(31,28,21,.105);
        background:transparent !important;
    }

    /* Level 1: hero is the only strongly elevated surface. */
    .sari-hero{
        border-color:#3d392d !important;
        box-shadow:var(--sari-shadow-hero) !important;
    }

    /* Remove expensive decorative blur; existing radial gradients keep depth. */
    .sari-hero-glow{
        display:none !important;
        filter:none !important;
    }

    .sari-hero-wave svg{
        width:22px !important;
        height:22px !important;
    }

    .sari-hero-stat-icon svg{
        width:11px !important;
        height:11px !important;
    }

    /* Level 2: KPI cards get restrained depth because they are high-priority data. */
    .admin-kpi{
        border-color:var(--sari-line) !important;
        background:var(--sari-surface) !important;
        box-shadow:var(--sari-shadow-kpi) !important;
        contain:paint;
        transition:border-color .14s ease,box-shadow .14s ease,transform .14s ease !important;
    }

    .admin-kpi:hover{
        transform:translateY(-1px) !important;
        border-color:#ddd1c1 !important;
        box-shadow:var(--sari-shadow-kpi-hover) !important;
    }

    /* Level 3: major dashboard sections float very lightly. */
    .admin-panel,
    .admin-control-panel,
    .sari-health-card,
    .sari-ops-card,
    .sari-monitor-card,
    .sari-sales-clean-card,
    .sari-reg-clean-card{
        border-color:var(--sari-line) !important;
        background:var(--sari-surface) !important;
        box-shadow:var(--sari-shadow-section) !important;
        transform:none !important;
        transition:none !important;
    }

    .admin-panel:hover,
    .admin-control-panel:hover,
    .sari-health-card:hover,
    .sari-ops-card:hover,
    .sari-monitor-card:hover,
    .sari-sales-clean-card:hover,
    .sari-reg-clean-card:hover{
        border-color:var(--sari-line) !important;
        background:var(--sari-surface) !important;
        box-shadow:var(--sari-shadow-section) !important;
        transform:none !important;
    }

    /* Inner content should feel integrated, not like cards inside cards. */
    .admin-control-subtle,
    .admin-soft-card,
    .sari-health-score-card,
    .sari-health-metric-v2,
    .sari-health-summary-card,
    .sari-risk-list,
    .sari-risk-state,
    .sari-rider-summary,
    .sari-sales-clean-empty,
    .sari-sales-clean-period,
    .sari-sales-chart-shell,
    .admin-chart-premium,
    .admin-chart-summary-card{
        box-shadow:none !important;
    }

    .admin-control-subtle,
    .admin-soft-card{
        border-color:var(--sari-line-soft) !important;
        background:#fcfbf8 !important;
    }

    .sari-risk-list,
    .sari-rider-summary,
    .sari-sales-chart-shell{
        border-color:var(--sari-line-soft) !important;
        background:#fff !important;
    }

    .sari-risk-state{
        box-shadow:none !important;
    }

    /* Small icon containers stay flat and crisp. */
    .admin-section-icon,
    .admin-stat-icon,
    .admin-icon-box,
    .sari-sales-header-icon{
        box-shadow:none !important;
    }

    /* Buttons: elevation only where it communicates action. */
    .admin-view-button,
    .admin-focus-chip,
    .sari-health-action,
    .sari-ops-action,
    .sari-monitor-action,
    .sari-sales-clean-period{
        box-shadow:none !important;
    }

    /* Marketplace Health remains flat internally. */
    #adminMarketplaceHealth{
        box-shadow:var(--sari-shadow-section) !important;
    }

    #adminMarketplaceHealth .sari-health-score-card,
    #adminMarketplaceHealth .sari-health-metric-v2,
    #adminMarketplaceHealth .sari-health-summary-card{
        box-shadow:none !important;
    }

    /* Below-fold sections: let the browser skip rendering until near viewport. */
    .sari-ops-card,
    .sari-monitor-card,
    .sari-sales-clean-card,
    #adminCategoryBreakdown{
        content-visibility:auto;
        contain-intrinsic-size:420px;
    }

    .sari-reg-clean-card{
        content-visibility:auto;
        contain-intrinsic-size:360px;
    }

    /* Drawer: keep useful separation without excessive shadow. */
    .admin-drawer{
        box-shadow:-12px 0 34px rgba(42,32,21,.10) !important;
    }

    .admin-toast{
        box-shadow:0 8px 20px rgba(49,38,24,.08) !important;
    }

    /* Make scrolling feel stable by avoiding unnecessary animated transforms. */
    .sari-role-row,
    .sari-focus-row-v2,
    .sari-risk-item,
    .sari-reg-clean-row{
        transition:background-color .12s ease !important;
        transform:none !important;
    }

    /* Keep touch / laptop density comfortable. */
    @media(max-height:850px) and (min-width:900px){
        .sari-hero{
            box-shadow:0 10px 24px rgba(31,28,21,.09) !important;
        }

        .admin-kpi{
            box-shadow:0 4px 12px rgba(54,42,28,.05) !important;
        }

        .admin-panel,
        .admin-control-panel,
        .sari-health-card,
        .sari-ops-card,
        .sari-monitor-card,
        .sari-sales-clean-card,
        .sari-reg-clean-card{
            box-shadow:0 5px 14px rgba(54,42,28,.045) !important;
        }
    }

    @media(prefers-reduced-motion:reduce){
        .admin-kpi{
            transform:none !important;
            transition:none !important;
        }
    }


    /* ============================================================
       ADMIN HERO — STABLE FLATICON WAVE + FIXED RESPONSIVE GEOMETRY
       ============================================================ */

    .sari-hero-title {
        display: inline-flex !important;
        align-items: center !important;
        flex-wrap: nowrap !important;
        gap: 7px !important;
        width: auto !important;
        max-width: 100% !important;
    }

    #adminGreetingText {
        min-width: 0;
    }

    /*
     * Keep the Flaticon hand optically aligned with the title text.
     * Flaticon glyphs have their own font metrics, so a fixed 1:1 shell +
     * a tiny optical offset prevents the hand from looking taller/lower.
     */
    .sari-hero-wave {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        align-self: center !important;
        width: 25px !important;
        height: 25px !important;
        flex: 0 0 25px !important;
        margin: 0 0 0 1px !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        color: #f1c55f !important;
        line-height: 1 !important;
        overflow: visible !important;
        position: relative !important;
        top: 1px !important;
    }

    .sari-hero-wave .fi {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 25px !important;
        height: 25px !important;
        flex: 0 0 25px !important;
        margin: 0 !important;
        padding: 0 !important;
        font-size: 23px !important;
        font-style: normal !important;
        line-height: 1 !important;
        vertical-align: middle !important;
        transform: none !important;
    }

    .sari-hero-wave .fi::before {
        display: block !important;
        margin: 0 !important;
        line-height: 1 !important;
        transform: translateY(0) !important;
    }

    @media (min-width: 1280px) {
        .sari-hero-side {
            display: grid !important;
            grid-template-columns:
                minmax(112px, 1fr)
                minmax(112px, 1fr)
                minmax(124px, 1fr)
                auto !important;
            align-items: center !important;
            gap: 0 !important;
            width: 100% !important;
        }

        .sari-hero-stat {
            min-width: 0 !important;
            padding: 6px 16px !important;
        }

        .sari-hero-stat:first-child {
            padding-left: 0 !important;
        }

        .sari-hero-stat + .sari-hero-stat {
            border-left: 1px solid rgba(255,255,255,.12) !important;
        }

        .sari-hero-brand {
            width: auto !important;
            min-width: 112px !important;
            justify-content: flex-end !important;
            padding-left: 18px !important;
        }

        .sari-hero-brand-inner {
            border-left: 1px solid rgba(255,255,255,.12) !important;
            padding-left: 18px !important;
            text-align: right !important;
        }
    }

    @media (min-width: 768px) and (max-width: 1279px) {
        .sari-hero-side {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            align-items: start !important;
            gap: 14px !important;
        }

        .sari-hero-stat,
        .sari-hero-stat + .sari-hero-stat {
            min-width: 0 !important;
            padding: 4px 14px !important;
        }

        .sari-hero-stat:first-child {
            padding-left: 0 !important;
        }

        .sari-hero-brand {
            grid-column: 1 / -1;
            width: 100% !important;
            justify-content: flex-start !important;
            padding-left: 0 !important;
            padding-top: 2px !important;
        }

        .sari-hero-brand-inner {
            border-left: 0 !important;
            border-top: 1px solid rgba(255,255,255,.12) !important;
            width: 100%;
            padding-left: 0 !important;
            padding-top: 12px !important;
            text-align: left !important;
        }
    }

    @media (max-width: 767px) {
        .sari-hero-title {
            gap: 6px !important;
            font-size: 22px !important;
        }

        .sari-hero-wave {
            width: 23px !important;
            height: 23px !important;
            flex: 0 0 23px !important;
            top: 1px !important;
        }

        .sari-hero-wave .fi {
            width: 23px !important;
            height: 23px !important;
            flex: 0 0 23px !important;
            font-size: 21px !important;
        }
    }


    /* ============================================================
       CATALOG CATEGORY BREAKDOWN — APPROVED MODERN DESIGN
       Visual-only layer. Existing live category data stays untouched.
       ============================================================ */

    #adminCategoryBreakdown.sari-category-card {
        position: relative;
        overflow: hidden;
        padding: 20px !important;
        border: 1px solid #e8dfd3 !important;
        border-radius: 18px !important;
        background:
            radial-gradient(circle at 16% 22%, rgba(201,149,36,.035), transparent 28%),
            linear-gradient(180deg, #fff 0%, #fffdfa 100%) !important;
        box-shadow: 0 7px 20px rgba(54,42,28,.045) !important;
    }

    .sari-category-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .sari-category-heading {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 12px;
    }

    .sari-category-head-icon {
        display: grid;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        place-items: center;
        border: 1px solid #f0e5cd;
        border-radius: 13px;
        background: linear-gradient(145deg, #fff9ed 0%, #fbf3e3 100%);
        color: #bb841b;
    }

    .sari-category-head-icon svg {
        width: 20px;
        height: 20px;
    }

    .sari-category-title {
        font-size: 14px;
        line-height: 1.3;
        font-weight: 700;
        letter-spacing: -.025em;
        color: #211d17;
    }

    .sari-category-subtitle {
        margin-top: 3px;
        font-size: 8.5px;
        line-height: 1.45;
        color: #978f84;
    }

    .sari-category-action {
        display: inline-flex;
        min-height: 36px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 1px solid #e5d8c4;
        border-radius: 12px;
        background: rgba(255,255,255,.86);
        padding: 0 13px;
        font-size: 8.5px;
        font-weight: 700;
        color: #765522;
        transition: border-color .14s ease, background-color .14s ease, color .14s ease;
    }

    .sari-category-action:hover {
        border-color: #d5b777;
        background: #fff8e9;
        color: #956313;
    }

    .sari-category-action svg {
        width: 13px;
        height: 13px;
    }

    .sari-category-layout {
        display: grid;
        gap: 24px;
        margin-top: 20px;
        align-items: center;
    }

    .sari-category-donut-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 0;
    }

    .sari-category-donut {
        position: relative;
        display: grid;
        width: 176px;
        height: 176px;
        flex: 0 0 176px;
        place-items: center;
        border-radius: 50%;
        isolation: isolate;
        box-shadow:
            0 15px 30px rgba(61,47,29,.075),
            0 3px 8px rgba(61,47,29,.045);
    }

    /* Premium inner disc */
    .sari-category-donut::before {
        content: "";
        position: absolute;
        z-index: 1;
        inset: 31px;
        border: 1px solid rgba(233,224,211,.92);
        border-radius: inherit;
        background: linear-gradient(145deg, #fff 0%, #fffdf9 100%);
        box-shadow:
            0 6px 15px rgba(67,51,32,.055),
            inset 0 1px 0 rgba(255,255,255,.98);
    }

    /* Very light highlight so the ring reads less flat without looking glossy. */
    .sari-category-donut::after {
        content: "";
        position: absolute;
        z-index: 0;
        inset: 0;
        border-radius: inherit;
        background:
            radial-gradient(circle at 30% 24%, rgba(255,255,255,.22), transparent 30%),
            radial-gradient(circle at 68% 74%, rgba(86,64,32,.055), transparent 42%);
        pointer-events: none;
    }

    .sari-category-donut-center {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .sari-category-donut-label {
        font-size: 7px;
        font-weight: 500;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #9b938a;
    }

    .sari-category-donut-value {
        margin-top: 5px;
        font-size: 32px;
        line-height: .95;
        font-weight: 700;
        letter-spacing: -.05em;
        color: #171d26;
    }

    .sari-category-donut-accent {
        width: 28px;
        height: 2px;
        margin-top: 9px;
        border-radius: 999px;
        background: #dda619;
    }

    .sari-category-donut-meta {
        margin-top: 8px;
        font-size: 8px;
        font-weight: 600;
        color: #8f8982;
    }

    .sari-category-list {
        min-width: 0;
    }

    .sari-category-row {
        --category-color: #9f968a;
        --category-soft: #f7f5f2;
        display: grid;
        grid-template-columns: 38px minmax(0,1fr);
        align-items: center;
        gap: 12px;
        min-height: 57px;
        padding: 9px 0;
        border-bottom: 1px solid #f0ebe4;
    }

    .sari-category-row:last-child {
        border-bottom: 0;
    }

    .sari-category-icon {
        display: grid;
        width: 36px;
        height: 36px;
        place-items: center;
        border-radius: 50%;
        background: var(--category-soft);
        color: var(--category-color);
    }

    .sari-category-icon svg {
        width: 17px;
        height: 17px;
    }

    .sari-category-row-body {
        min-width: 0;
    }

    .sari-category-row-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .sari-category-name {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 10.5px;
        font-weight: 650;
        letter-spacing: -.01em;
        color: #302a24;
    }

    .sari-category-percent {
        flex: 0 0 auto;
        min-width: 44px;
        text-align: right;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: -.02em;
        color: #201c18;
    }

    .sari-category-progress {
        position: relative;
        height: 5px;
        margin-top: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: #eee9e2;
    }

    .sari-category-progress > span {
        display: block;
        height: 100%;
        min-width: 3px;
        border-radius: inherit;
        background: var(--category-color);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.24);
    }

    @media (min-width: 640px) {
        .sari-category-layout {
            grid-template-columns: 184px minmax(0, 1fr);
        }
    }

    @media (min-width: 1280px) and (max-height: 850px) {
        #adminCategoryBreakdown.sari-category-card {
            padding: 17px !important;
        }

        .sari-category-layout {
            grid-template-columns: 166px minmax(0,1fr);
            gap: 19px;
            margin-top: 16px;
        }

        .sari-category-donut {
            width: 158px;
            height: 158px;
            flex-basis: 158px;
        }

        .sari-category-donut::before {
            inset: 28px;
        }

        .sari-category-donut-value {
            font-size: 28px;
        }

        .sari-category-row {
            min-height: 52px;
            padding: 7px 0;
        }
    }

    @media (max-width: 639px) {
        #adminCategoryBreakdown.sari-category-card {
            padding: 16px !important;
        }

        .sari-category-head {
            gap: 10px;
        }

        .sari-category-head-icon {
            width: 38px;
            height: 38px;
            flex-basis: 38px;
        }

        .sari-category-action {
            min-height: 34px;
            padding: 0 10px;
        }

        .sari-category-layout {
            margin-top: 18px;
            gap: 18px;
        }

        .sari-category-donut {
            width: 164px;
            height: 164px;
            flex-basis: 164px;
        }

        .sari-category-donut::before {
            inset: 29px;
        }

        .sari-category-row {
            min-height: 55px;
        }
    }

    @media (max-width: 430px) {
        .sari-category-head {
            flex-direction: column;
        }

        .sari-category-action {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-category-action {
            transition: none;
        }
    }


    /* ============================================================
       SALES PERIOD FILTER — functional server-side analytics selector
       ============================================================ */
    .sari-sales-period-menu {
        position: relative;
        z-index: 24;
        flex: 0 0 auto;
    }

    .sari-sales-period-menu > summary {
        list-style: none;
        cursor: pointer;
        user-select: none;
    }

    .sari-sales-period-menu > summary::-webkit-details-marker {
        display: none;
    }

    .sari-sales-period-menu .sari-sales-period-chevron {
        transition: transform .14s ease;
    }

    .sari-sales-period-menu[open] .sari-sales-period-chevron {
        transform: rotate(180deg);
    }

    .sari-sales-period-options {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        z-index: 40;
        width: 176px;
        overflow: hidden;
        border: 1px solid #e7dfd5;
        border-radius: 13px;
        background: rgba(255,255,255,.985);
        padding: 5px;
        box-shadow: 0 14px 32px rgba(55,43,29,.12);
    }

    .sari-sales-period-option {
        display: flex;
        width: 100%;
        border: 0;
        background: transparent;
        text-align: left;
        cursor: pointer;
        min-height: 36px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border-radius: 9px;
        padding: 0 10px;
        font-size: 8.5px;
        font-weight: 600;
        color: #655c52;
        transition: background-color .12s ease, color .12s ease;
    }

    .sari-sales-period-option:hover {
        background: #fbf7ef;
        color: #8f6116;
    }

    .sari-sales-period-option.is-active {
        background: #fff5df;
        color: #966311;
        font-weight: 700;
    }

    .sari-sales-period-option-check {
        width: 17px;
        height: 17px;
        display: grid;
        place-items: center;
        border-radius: 50%;
        background: #e8b84f;
        color: #fff;
        font-size: 10px;
        line-height: 1;
    }

    @media (max-width: 639px) {
        .sari-sales-period-options {
            width: 164px;
        }
    }



    /* ============================================================
       SALES OVERVIEW — ASYNC PERIOD TRANSITION
       Panel swap remains unchanged. Graph animation is now driven
       by JavaScript to match the Seller graph motion exactly.
       ============================================================ */
    #adminSalesOverview {
        will-change: opacity, transform;
        transition: opacity .18s ease, transform .18s ease;
    }

    #adminSalesOverview.is-period-updating {
        opacity: .58;
        transform: translateY(3px);
        pointer-events: none;
    }

    #adminSalesOverview.is-period-enter {
        opacity: 0;
        transform: translateY(7px);
    }

    /*
     * Seller-matched graph animation uses actual SVG path lengths,
     * so no fixed stroke-dasharray/keyframes are needed here.
     */
    #adminSalesOverview .sari-sales-clean-line,
    #adminSalesOverview .sari-sales-clean-area,
    #adminSalesOverview .sari-sales-clean-point {
        will-change: opacity, transform, stroke-dashoffset;
    }

    @media (prefers-reduced-motion: reduce) {
        #adminSalesOverview,
        #adminSalesOverview .sari-sales-clean-line,
        #adminSalesOverview .sari-sales-clean-area,
        #adminSalesOverview .sari-sales-clean-point {
            transition: none !important;
            animation: none !important;
            transform: none !important;
            opacity: 1 !important;
            stroke-dashoffset: 0 !important;
        }
    }


    /* ============================================================
       SARI ADMIN — STRONGER SMOOTH NEUMORPHISM V3
       More visible static elevation, still clean/professional.
       ONLY summary/KPI cards have cursor hover movement.
       ============================================================ */

    .sari-admin-dashboard {
        --sari-neumo-surface: #fffefa;
        --sari-neumo-border: #e2d8ca;
        --sari-neumo-shadow-main: rgba(55, 43, 29, .105);
        --sari-neumo-shadow-near: rgba(55, 43, 29, .045);
        --sari-neumo-highlight: rgba(255, 255, 255, .96);
    }

    /* Main sections: visibly floating, but completely static on hover. */
    .admin-panel,
    .admin-control-panel,
    .sari-health-card,
    .sari-ops-card,
    .sari-monitor-card,
    .sari-sales-clean-card,
    .sari-reg-clean-card,
    #adminCategoryBreakdown.sari-category-card {
        border-color: var(--sari-neumo-border) !important;
        background:
            linear-gradient(145deg, #ffffff 0%, #fdfbf7 100%) !important;
        box-shadow:
            0 16px 34px var(--sari-neumo-shadow-main),
            0 5px 12px var(--sari-neumo-shadow-near),
            -7px -7px 18px var(--sari-neumo-highlight),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
        transform: none !important;
        transition: none !important;
    }

    .admin-panel:hover,
    .admin-control-panel:hover,
    .sari-health-card:hover,
    .sari-ops-card:hover,
    .sari-monitor-card:hover,
    .sari-sales-clean-card:hover,
    .sari-reg-clean-card:hover,
    #adminCategoryBreakdown.sari-category-card:hover {
        border-color: var(--sari-neumo-border) !important;
        background:
            linear-gradient(145deg, #ffffff 0%, #fdfbf7 100%) !important;
        box-shadow:
            0 16px 34px var(--sari-neumo-shadow-main),
            0 5px 12px var(--sari-neumo-shadow-near),
            -7px -7px 18px var(--sari-neumo-highlight),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
        transform: none !important;
    }

    /* Summary cards: only interactive hover surfaces. */
    .admin-kpi {
        border-color: #e1d6c8 !important;
        background:
            linear-gradient(145deg, #ffffff 0%, #fefbf6 100%) !important;
        box-shadow:
            0 9px 21px rgba(55,43,29,.080),
            0 3px 8px rgba(55,43,29,.035),
            -5px -5px 12px rgba(255,255,255,.96),
            inset 0 1px 0 rgba(255,255,255,.94) !important;
        transform: translateY(0) !important;
        transition:
            transform .22s cubic-bezier(.2,.7,.2,1),
            box-shadow .22s cubic-bezier(.2,.7,.2,1),
            border-color .22s ease !important;
        will-change: transform;
    }

    .admin-kpi:hover {
        transform: translateY(-3px) !important;
        border-color: #d3c5b3 !important;
        box-shadow:
            0 16px 30px rgba(55,43,29,.125),
            0 5px 11px rgba(55,43,29,.050),
            -6px -6px 14px rgba(255,255,255,.99),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    /* Inner content stays integrated so parent elevation remains the visual focus. */
    .admin-control-subtle,
    .admin-soft-card,
    .sari-health-score-card,
    .sari-health-metric-v2,
    .sari-health-summary-card,
    .sari-risk-list,
    .sari-risk-state,
    .sari-rider-summary,
    .sari-sales-clean-empty,
    .sari-sales-chart-shell,
    .admin-chart-premium,
    .admin-chart-summary-card {
        border-color: #eee7dd !important;
        background: rgba(253,252,249,.72) !important;
        box-shadow:
            inset 1px 1px 3px rgba(69,53,35,.018),
            inset -1px -1px 3px rgba(255,255,255,.82) !important;
        transform: none !important;
    }

    #adminMarketplaceHealth .sari-health-score-card,
    #adminMarketplaceHealth .sari-health-metric-v2,
    #adminMarketplaceHealth .sari-health-summary-card {
        background: transparent !important;
        box-shadow: none !important;
    }

    /* Small UI stays flat and crisp. */
    .admin-icon-box,
    .admin-section-icon,
    .admin-stat-icon,
    .sari-health-icon,
    .sari-monitor-icon,
    .sari-category-head-icon {
        box-shadow: none !important;
    }

    /* No hover animation/recolor on buttons and controls. */
    .admin-view-button,
    .admin-focus-chip,
    .sari-health-action,
    .sari-ops-action,
    .sari-monitor-action,
    .sari-sales-clean-period,
    .sari-category-action,
    .sari-sales-period-option,
    .sari-reg-clean-view-all,
    .sari-sales-clean-legend-item {
        transition: none !important;
        transform: none !important;
        box-shadow: none !important;
    }

    .admin-view-button:hover {
        border-color: #e9e1d6 !important;
        background: #fff !important;
        color: #756b5d !important;
    }

    .admin-focus-chip:not(.is-active):hover {
        border-color: #e8e0d5 !important;
        background: #fff !important;
        color: #786f65 !important;
    }

    .admin-focus-chip.is-active:hover {
        border-color: #d8bc80 !important;
        background: #fff8e9 !important;
        color: #9b6715 !important;
    }

    .sari-health-action:hover {
        border-color: #e3d5bc !important;
        background: #fff !important;
        color: #956313 !important;
    }

    .sari-ops-action:hover {
        border-color: #e6dccb !important;
        background: #fffdf9 !important;
        color: #7c5b20 !important;
    }

    .sari-monitor-action:hover {
        border-color: #e3d8c7 !important;
        background: #fff !important;
        color: #6f6253 !important;
    }

    .sari-category-action:hover {
        border-color: #e5d8c4 !important;
        background: rgba(255,255,255,.86) !important;
        color: #765522 !important;
    }

    .sari-sales-clean-legend-item:hover {
        color: #746c63 !important;
    }

    .sari-reg-clean-view-all:hover {
        color: #af7414 !important;
    }

    /* Operational rows/cards remain static. */
    .admin-quick-action,
    .admin-role-card,
    .sari-role-row,
    .sari-focus-row-v2,
    .sari-risk-item,
    .sari-reg-clean-row {
        transition: none !important;
        transform: none !important;
    }

    .admin-quick-action:hover,
    .admin-role-card:hover,
    .sari-role-row:hover,
    .sari-focus-row-v2:hover,
    .sari-risk-item:hover,
    .sari-reg-clean-row:hover {
        transform: none !important;
        background: inherit !important;
    }

    .admin-role-card:hover {
        border-color: #ece6dd !important;
    }

    .sari-role-row:hover .sari-role-chevron {
        background: transparent !important;
        color: #9a8b77 !important;
    }

    /* Platform Activity rows: suppress utility hover background. */
    #adminCategoryBreakdown + .admin-panel .hover\:bg-\[\#fcfbf8\]:hover {
        background-color: transparent !important;
    }

    /* Dropdown can float as an overlay, but has no option hover motion. */
    .sari-sales-period-options {
        border-color: #dfd4c6 !important;
        background: rgba(255,254,251,.995) !important;
        box-shadow:
            0 16px 30px rgba(55,42,28,.135),
            0 4px 10px rgba(55,42,28,.045) !important;
    }

    .sari-sales-period-option:hover {
        transform: none !important;
    }

    /* Hero remains the strongest visual anchor, without hover behavior. */
    .sari-hero {
        box-shadow:
            0 16px 34px rgba(31,28,21,.14),
            0 4px 10px rgba(31,28,21,.06) !important;
        transition: none !important;
        transform: none !important;
    }

    @media (max-height: 850px) and (min-width: 900px) {
        .admin-panel,
        .admin-control-panel,
        .sari-health-card,
        .sari-ops-card,
        .sari-monitor-card,
        .sari-sales-clean-card,
        .sari-reg-clean-card,
        #adminCategoryBreakdown.sari-category-card {
            box-shadow:
                0 13px 28px rgba(55,43,29,.090),
                0 4px 10px rgba(55,43,29,.038),
                -6px -6px 15px rgba(255,255,255,.92),
                inset 0 1px 0 rgba(255,255,255,.92) !important;
        }
    }

    @media (max-width: 767px) {
        .admin-panel,
        .admin-control-panel,
        .sari-health-card,
        .sari-ops-card,
        .sari-monitor-card,
        .sari-sales-clean-card,
        .sari-reg-clean-card,
        #adminCategoryBreakdown.sari-category-card {
            box-shadow:
                0 10px 22px rgba(55,43,29,.080),
                0 3px 8px rgba(55,43,29,.035),
                inset 0 1px 0 rgba(255,255,255,.90) !important;
        }

        .admin-kpi {
            box-shadow:
                0 7px 16px rgba(55,43,29,.070),
                0 2px 6px rgba(55,43,29,.030),
                inset 0 1px 0 rgba(255,255,255,.90) !important;
        }

        .admin-kpi:hover {
            transform: translateY(-1px) !important;
            box-shadow:
                0 10px 21px rgba(55,43,29,.090),
                inset 0 1px 0 rgba(255,255,255,.94) !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .admin-kpi {
            transition: none !important;
            transform: none !important;
        }

        .admin-kpi:hover {
            transform: none !important;
        }
    }


/* ============================================================
       SARI ADMIN — CLEAN WHITE FLOATING SYSTEM FINAL
       Matches the Seller visual language.
       ============================================================ */
    .sari-admin-dashboard {
        --sari-page-bg: #F4F5F7;
        --sari-surface: #FFFFFF;
        --sari-surface-soft: #FAFAFB;
        --sari-border: #E7E9EE;

        --sari-gold: #D89B10;
        --sari-gold-dark: #B67A08;
        --sari-charcoal: #202124;
        --sari-secondary: #4B5563;
        --sari-muted: #8A919B;
        --sari-success: #63A375;
        --sari-info: #6C8FB5;
        --sari-danger: #D97C6C;

        --sari-float-shadow:
            0 1px 2px rgba(32, 33, 36, .025),
            0 8px 20px rgba(32, 33, 36, .050),
            0 18px 38px rgba(32, 33, 36, .055);

        --sari-float-shadow-hover:
            0 2px 4px rgba(32, 33, 36, .025),
            0 12px 27px rgba(32, 33, 36, .065),
            0 25px 50px rgba(32, 33, 36, .075);

        background: transparent !important;
        color: var(--sari-charcoal) !important;
    }

    /* ------------------------------------------------------------
       HERO: solid palette color only — no decorative gradient/glow.
       ------------------------------------------------------------ */
    .sari-hero {
        border-color: #303236 !important;
        background: #222222 !important;
        background-image: none !important;
        box-shadow:
            0 2px 5px rgba(20, 20, 20, .070),
            0 13px 30px rgba(20, 20, 20, .105),
            0 25px 50px rgba(20, 20, 20, .075) !important;
        filter: none !important;
        backdrop-filter: none !important;
    }

    .sari-hero::before,
    .sari-hero::after,
    .sari-hero-glow {
        background: none !important;
        background-image: none !important;
        filter: none !important;
    }

    .sari-hero-glow {
        display: none !important;
    }

    /* ------------------------------------------------------------
       MAIN CONTAINERS: pure white, soft floating depth.
       ------------------------------------------------------------ */
    .admin-panel,
    .admin-control-panel,
    .admin-kpi,
    .sari-health-card,
    .sari-ops-card,
    .sari-monitor-card,
    .sari-sales-card,
    .sari-sales-clean-card,
    .sari-reg-clean-card,
    #adminCategoryBreakdown.sari-category-card {
        border: 1px solid var(--sari-border) !important;
        background: var(--sari-surface) !important;
        background-image: none !important;
        box-shadow: var(--sari-float-shadow) !important;
        transform: translateY(0) !important;
        filter: none !important;
        backdrop-filter: none !important;
        transition:
            transform .14s cubic-bezier(.22, 1, .36, 1),
            box-shadow .14s ease !important;
        will-change: auto !important;
    }

    .admin-panel:hover,
    .admin-control-panel:hover,
    .admin-kpi:hover,
    .sari-health-card:hover,
    .sari-ops-card:hover,
    .sari-monitor-card:hover,
    .sari-sales-card:hover,
    .sari-sales-clean-card:hover,
    .sari-reg-clean-card:hover,
    #adminCategoryBreakdown.sari-category-card:hover {
        border-color: var(--sari-border) !important;
        background: var(--sari-surface) !important;
        background-image: none !important;
        transform: translateY(-3px) !important;
        box-shadow: var(--sari-float-shadow-hover) !important;
    }

    /* ------------------------------------------------------------
       INNER SURFACES: white, quieter than their parent.
       No cream/beige card fills.
       ------------------------------------------------------------ */
    .admin-control-subtle,
    .admin-soft-card,
    .admin-quick-action,
    .admin-role-card,
    .sari-health-score-card,
    .sari-health-metric-v2,
    .sari-health-summary-card,
    .sari-risk-list,
    .sari-risk-state,
    .sari-rider-summary,
    .sari-sales-clean-empty,
    .sari-sales-chart-shell,
    .admin-chart-premium,
    .admin-chart-summary-card,
    .sari-sales-clean-period,
    .sari-chart-premium {
        border-color: var(--sari-border) !important;
        background: #FFFFFF !important;
        background-image: none !important;
        box-shadow: none !important;
        filter: none !important;
        backdrop-filter: none !important;
    }

    /* Marketplace Health stays clean internally, but fully white. */
    #adminMarketplaceHealth,
    #adminMarketplaceHealth .sari-health-score-card,
    #adminMarketplaceHealth .sari-health-metric-v2,
    #adminMarketplaceHealth .sari-health-summary-card {
        background: #FFFFFF !important;
        background-image: none !important;
    }

    #adminMarketplaceHealth .sari-health-score-card,
    #adminMarketplaceHealth .sari-health-metric-v2,
    #adminMarketplaceHealth .sari-health-summary-card {
        box-shadow: none !important;
    }

    /* Requested section-heading icons are removed in the Blade markup.
       Remove leftover spacing so the headings remain aligned. */
    #adminMarketplaceHealth .sari-health-heading,
    #adminRoleControl .sari-ops-heading,
    #adminOrderJourney .sari-ops-heading,
    #adminInterventionRadar .sari-monitor-heading,
    #adminRiderOverview .sari-monitor-heading,
    .sari-analytics-clean-grid > .sari-health-card .sari-health-heading {
        gap: 0 !important;
    }

    /* ------------------------------------------------------------
       DECORATIVE GRADIENTS: solid palette replacements.
       Functional donut/conic-gradient data charts stay intact.
       ------------------------------------------------------------ */
    .admin-health-track > span,
    .sari-health-progress > span {
        background: var(--sari-gold) !important;
        background-image: none !important;
    }

    .admin-chart-premium,
    .sari-sales-chart,
    .sari-sales-clean-chart,
    #adminCategoryBreakdown.sari-category-card,
    .sari-category-donut::before {
        background: #FFFFFF !important;
        background-image: none !important;
    }

    .admin-chart-premium::after,
    .sari-category-donut::after {
        background: none !important;
        background-image: none !important;
    }

    .sari-category-head-icon {
        background: #FFFFFF !important;
        background-image: none !important;
    }

    /* ------------------------------------------------------------
       ACTIONS: no cream hover fills.
       White base + restrained gold accent only.
       ------------------------------------------------------------ */
    .admin-view-button,
    .sari-health-action,
    .sari-ops-action,
    .sari-monitor-action,
    .sari-category-action,
    .sari-sales-clean-period {
        border-color: var(--sari-border) !important;
        background: #FFFFFF !important;
        background-image: none !important;
        color: var(--sari-secondary) !important;
        box-shadow: none !important;
    }

    .admin-view-button:hover,
    .sari-health-action:hover,
    .sari-ops-action:hover,
    .sari-monitor-action:hover,
    .sari-category-action:hover,
    .sari-sales-clean-period:hover {
        border-color: var(--sari-gold) !important;
        background: #FFFFFF !important;
        color: var(--sari-gold-dark) !important;
        transform: none !important;
    }

    .admin-focus-chip {
        border-color: var(--sari-border) !important;
        background: #FFFFFF !important;
        color: var(--sari-secondary) !important;
        box-shadow: none !important;
    }

    .admin-focus-chip:hover {
        border-color: var(--sari-gold) !important;
        background: #FFFFFF !important;
        color: var(--sari-gold-dark) !important;
    }

    .admin-focus-chip.is-active,
    .admin-focus-chip.is-active:hover {
        border-color: var(--sari-gold) !important;
        background: var(--sari-gold) !important;
        color: #FFFFFF !important;
    }

    /* Rows remain visually quiet; no cream hover wash. */
    .sari-role-row:hover,
    .sari-focus-row-v2:hover,
    .sari-risk-item:hover,
    .sari-reg-clean-row:hover {
        background: #FFFFFF !important;
    }

    .sari-role-row:hover .sari-role-chevron {
        background: transparent !important;
        color: #8A919B !important;
    }

    /* Neutral dropdown surfaces. */
    .sari-sales-period-options,
    .sari-sales-clean-tooltip {
        border-color: var(--sari-border) !important;
        background: #FFFFFF !important;
        background-image: none !important;
    }

    .sari-sales-period-option:hover {
        background: #F4F5F7 !important;
        color: var(--sari-gold-dark) !important;
    }

    .sari-sales-period-option.is-active {
        background: #F4F5F7 !important;
        color: var(--sari-gold-dark) !important;
    }

    /* Keep motion light and predictable. */
    @media (max-width: 767px) {
        .admin-panel:hover,
        .admin-control-panel:hover,
        .admin-kpi:hover,
        .sari-health-card:hover,
        .sari-ops-card:hover,
        .sari-monitor-card:hover,
        .sari-sales-card:hover,
        .sari-sales-clean-card:hover,
        .sari-reg-clean-card:hover,
        #adminCategoryBreakdown.sari-category-card:hover {
            transform: translateY(-1px) !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .admin-panel,
        .admin-control-panel,
        .admin-kpi,
        .sari-health-card,
        .sari-ops-card,
        .sari-monitor-card,
        .sari-sales-card,
        .sari-sales-clean-card,
        .sari-reg-clean-card,
        #adminCategoryBreakdown.sari-category-card {
            transition: none !important;
            transform: none !important;
        }

        .admin-panel:hover,
        .admin-control-panel:hover,
        .admin-kpi:hover,
        .sari-health-card:hover,
        .sari-ops-card:hover,
        .sari-monitor-card:hover,
        .sari-sales-card:hover,
        .sari-sales-clean-card:hover,
        .sari-reg-clean-card:hover,
        #adminCategoryBreakdown.sari-category-card:hover {
            transform: none !important;
        }
    }

</style>

<div class="sari-admin-dashboard mx-auto w-full max-w-[1800px]">

    @php
        $live = $adminDashboardLive ?? [];
        $liveKpis = $live['kpis'] ?? [];
        $liveRoles = $live['roles'] ?? [];
        $liveRiders = $live['riders'] ?? [];
        $liveJourney = $live['journey'] ?? [];
        $liveSales = $live['sales'] ?? [];
        $liveHealth = $live['health'] ?? [];
        $liveFocus = collect($live['focus'] ?? []);
        $liveRegistrations = $live['recent_registrations'] ?? [];
        $liveCategories = $live['categories'] ?? ['total' => 0, 'items' => []];
        $liveActivity = $live['activity'] ?? [];
        $liveUrls = $live['urls'] ?? [];

        $formatNumber = static fn ($value): string => number_format((float) ($value ?? 0), 0);
        $formatMoney = static fn ($value): string => '₱' . number_format((float) ($value ?? 0), 2);
        $formatPercent = static function ($value): string {
            $number = (float) ($value ?? 0);
            $prefix = $number > 0 ? '+' : '';
            return $prefix . number_format($number, 1) . '%';
        };
        $formatCompactMoney = static function ($value): string {
            $number = (float) ($value ?? 0);
            $abs = abs($number);
            if ($abs >= 1000000) {
                return '₱' . number_format($number / 1000000, 1) . 'M';
            }
            if ($abs >= 1000) {
                return '₱' . number_format($number / 1000, 1) . 'K';
            }
            return '₱' . number_format($number, 0);
        };

        $healthScore = max(0, min(100, (int) ($liveHealth['score'] ?? 100)));
        $healthLabel = match (true) {
            $healthScore >= 85 => 'Healthy',
            $healthScore >= 70 => 'Watch',
            default => 'At Risk',
        };
        $healthDegrees = round($healthScore * 3.6, 1);

        $focusItems = $liveFocus
            ->filter(fn ($item) => (int) ($item['count'] ?? 0) > 0)
            ->values()
            ->map(function ($item, $index) {
                $item['id'] = 'live-focus-' . ($index + 1);
                return $item;
            });

        $categoryColors = ['#d7a325', '#284b73', '#2f9b7c', '#c38a42'];
        $categoryRows = collect($liveCategories['items'] ?? [])->take(4)->values()->map(function ($item, $index) use ($categoryColors) {
            return [
                'name' => $item['name'] ?? 'Uncategorized',
                'value' => number_format((float) ($item['percent'] ?? 0), 1) . '%',
                'percent' => max(0, min(100, (float) ($item['percent'] ?? 0))),
                'dot' => $categoryColors[$index] ?? '#9f968a',
            ];
        });

        $categoryStops = [];
        $categoryCursor = 0.0;
        foreach ($categoryRows as $row) {
            $start = $categoryCursor * 3.6;
            $categoryCursor += $row['percent'];
            $end = $categoryCursor * 3.6;
            $categoryStops[] = $row['dot'] . ' ' . round($start, 1) . 'deg ' . round($end, 1) . 'deg';
        }
        $categoryDonut = count($categoryStops) > 0
            ? 'conic-gradient(' . implode(',', $categoryStops) . ')'
            : 'conic-gradient(#eee8df 0deg 360deg)';
        $registrations = collect($liveRegistrations)->take(5)->values();
    @endphp


    {{-- =========================================================
        WELCOME / OVERVIEW BANNER
    ========================================================== --}}
    <section class="sari-hero px-5 py-6 text-white sm:px-7 sm:py-7 lg:px-8">
        <div class="sari-hero-glow"></div>

        <div class="relative z-10 grid gap-7 xl:grid-cols-[1.08fr_.92fr] xl:items-center">
            <div>
                <p class="sari-hero-kicker">SARI Marketplace</p>

                <h2 id="adminGreeting" class="sari-hero-title">
                    <span id="adminGreetingText">Good morning, Admin!</span>
                    <span class="sari-hero-wave" aria-hidden="true">
                        <i class="fi fi-ts-hand-wave"></i>
                    </span>
                </h2>

                <p class="sari-hero-copy">
                    Here’s a clean overview of your marketplace performance, registrations,
                    seller activity, complaints, and commission.
                </p>
            </div>

            <div class="sari-hero-side xl:justify-end">
                <div class="sari-hero-stat">
                    <p class="sari-hero-stat-label">Time</p>
                    <div class="sari-hero-stat-row">
                        <span class="sari-hero-stat-icon">
                            
                                <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="8"></circle>
                                    <path d="M12 7v5l3 2"></path>
                                </svg>
                        </span>
                        <div>
                            <p id="adminCurrentTime" class="sari-hero-stat-value">--:-- --</p>
                            <p id="adminTimezone" class="sari-hero-stat-sub">Asia/Manila</p>
                        </div>
                    </div>
                </div>

                <div class="sari-hero-stat">
                    <p class="sari-hero-stat-label">Date</p>
                    <div class="sari-hero-stat-row">
                        <span class="sari-hero-stat-icon">
                            
                                <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="4" y="5" width="16" height="15" rx="2"></rect>
                                    <path d="M8 3v4M16 3v4M4 9h16"></path>
                                </svg>
                        </span>
                        <div>
                            <p id="adminCurrentDay" class="sari-hero-stat-value">Saturday</p>
                            <p id="adminCurrentDate" class="sari-hero-stat-sub">September 5, 2026</p>
                        </div>
                    </div>
                </div>

                <div class="sari-hero-stat">
                    <p class="sari-hero-stat-label">Weather</p>
                    <div class="sari-hero-stat-row">
                        <span class="sari-hero-stat-icon is-cloud">
                            
                                <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8 17h9a3 3 0 0 0 .5-6 5 5 0 0 0-9.2-1.6A4 4 0 0 0 8 17Z"></path>
                                    <path d="M14 4V2M18.2 5.8l1.4-1.4M20 10h2"></path>
                                </svg>
                        </span>
                        <div>
                            <p id="adminWeatherTemp" class="sari-hero-stat-value">--°C</p>
                            <p id="adminWeatherCondition" class="sari-hero-stat-sub">Loading weather...</p>
                        </div>
                    </div>
                </div>

                <div class="sari-hero-brand">
                    <div class="sari-hero-brand-inner">
                        <p class="sari-hero-brand-mark">SARI</p>
                        <p class="sari-hero-brand-copy">Stronger<br>Philippine<br>Commerce</p>
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
                ['label'=>'Total Users','value'=>$formatNumber($liveKpis['total_users'] ?? 0),'trend'=>'Live','note'=>'approved accounts','tone'=>'green'],
                ['label'=>'Pending Registrations','value'=>$formatNumber($liveKpis['pending_registrations'] ?? 0),'trend'=>$formatNumber($liveKpis['pending_registrations'] ?? 0) . ' pending','note'=>'awaiting review','tone'=>'gold'],
                ['label'=>'Active Sellers','value'=>$formatNumber($liveKpis['active_sellers'] ?? 0),'trend'=>'Live','note'=>'active seller accounts','tone'=>'teal'],
                ['label'=>'Total Orders','value'=>$formatNumber($liveKpis['total_orders'] ?? 0),'trend'=>'Live','note'=>'marketplace orders','tone'=>'blue'],
                ['label'=>'Open Complaints','value'=>$formatNumber($liveKpis['open_complaints'] ?? 0),'trend'=>$formatNumber($liveKpis['open_complaints'] ?? 0) . ' open','note'=>'need attention','tone'=>'red'],
                ['label'=>'Platform Commission','value'=>$formatMoney($liveKpis['platform_commission'] ?? 0),'trend'=>number_format((float) ($liveKpis['commission_rate'] ?? 0), 1) . '%','note'=>'platform rate','tone'=>'purple'],
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

        {{-- Marketplace Health + Recent Registrations --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-[1.15fr_.85fr]">

            {{-- Marketplace Health --}}
            <section id="adminMarketplaceHealth" class="sari-health-card p-5 sm:p-6">
                <div class="sari-health-header">
                    <div class="sari-health-heading">
                        <div class="min-w-0">
                            <h4 class="sari-health-title">Marketplace Health</h4>
                            <p class="sari-health-subtitle">Operational health across the full marketplace</p>
                        </div>
                    </div>

                    <div class="sari-health-actions">
                        <span class="sari-health-stable">
                            <span class="h-2 w-2 rounded-full bg-[#3d9666]"></span>
                            Stable
                        </span>
                        <a href="{{ $liveUrls['reports'] ?? '#' }}" class="sari-health-action">
                            View Details
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="m9 6 6 6-6 6"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                @php
                    $healthMetrics = [
                        [
                            'key' => 'order_success',
                            'label' => 'Order Success Rate',
                            'value' => number_format((float) ($liveHealth['order_success'] ?? 0), 1) . '%',
                            'width' => max(0, min(100, (float) ($liveHealth['order_success'] ?? 0))) . '%',
                            'note' => 'Delivered vs terminal orders',
                            'icon' => 'cart',
                        ],
                        [
                            'key' => 'fulfillment',
                            'label' => 'Fulfillment Health',
                            'value' => number_format((float) ($liveHealth['fulfillment'] ?? 0), 1) . '%',
                            'width' => max(0, min(100, (float) ($liveHealth['fulfillment'] ?? 0))) . '%',
                            'note' => 'Current delivery flow',
                            'icon' => 'box',
                        ],
                        [
                            'key' => 'seller_active',
                            'label' => 'Seller Active Rate',
                            'value' => number_format((float) ($liveHealth['seller_active'] ?? 0), 1) . '%',
                            'width' => max(0, min(100, (float) ($liveHealth['seller_active'] ?? 0))) . '%',
                            'note' => 'Active seller accounts',
                            'icon' => 'users',
                        ],
                        [
                            'key' => 'registration_approval',
                            'label' => 'Registration Approval',
                            'value' => number_format((float) ($liveHealth['registration_approval'] ?? 0), 1) . '%',
                            'width' => max(0, min(100, (float) ($liveHealth['registration_approval'] ?? 0))) . '%',
                            'note' => 'Reviewed applications',
                            'icon' => 'file',
                        ],
                    ];
                @endphp

                <div class="sari-health-main">
                    <div class="sari-health-score-card">
                        <div class="admin-health-score" style="background:conic-gradient(#c99524 0deg {{ $healthDegrees }}deg,#eee6d9 {{ $healthDegrees }}deg 360deg)">
                            <div class="admin-health-score-content">
                                <p class="text-[34px] font-bold tracking-[-.055em] text-[#29231d]">{{ $healthScore }}</p>
                                <p class="mt-[-2px] text-[8px] font-semibold uppercase tracking-[.12em] text-[#9b9388]">out of 100</p>
                                <span class="admin-health-grade">
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#4d8d68]"></span>
                                    {{ $healthLabel }}
                                </span>
                            </div>
                        </div>
                        <p class="sari-health-score-copy">
                            {{ $healthScore >= 85
                                ? 'Your marketplace is performing well across key operational areas.'
                                : ($healthScore >= 70
                                    ? 'Marketplace performance is stable, with a few areas worth monitoring.'
                                    : 'Marketplace health needs attention across multiple operational areas.') }}
                        </p>
                    </div>

                    <div class="sari-health-metrics">
                        @foreach ($healthMetrics as $metric)
                            <div class="sari-health-metric-v2">
                                <div class="sari-health-metric-top">
                                    <span class="sari-health-metric-icon">
                                        @if ($metric['icon'] === 'cart')
                                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M3 5h2l2 10h10l2-7H7"></path>
                                                <circle cx="10" cy="19" r="1.2"></circle>
                                                <circle cx="17" cy="19" r="1.2"></circle>
                                            </svg>
                                        @elseif ($metric['icon'] === 'box')
                                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="m12 3 8 4-8 4-8-4 8-4Z"></path>
                                                <path d="M4 7v10l8 4 8-4V7M12 11v10"></path>
                                            </svg>
                                        @elseif ($metric['icon'] === 'users')
                                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <circle cx="8" cy="8" r="3"></circle>
                                                <circle cx="17" cy="10" r="2.4"></circle>
                                                <path d="M2.5 20c.5-4 2.6-6 5.5-6s5 2 5.5 6M15 15c3 0 5 1.7 5.5 5"></path>
                                            </svg>
                                        @else
                                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M8 3h8l5 5v11a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"></path>
                                                <path d="M16 3v5h5M9 13h6M9 17h6"></path>
                                            </svg>
                                        @endif
                                    </span>

                                    <div class="sari-health-metric-copy">
                                        <p class="sari-health-metric-label">{{ $metric['label'] }}</p>
                                        <p class="sari-health-metric-note">{{ $metric['note'] }}</p>
                                    </div>
                                    <span class="sari-health-metric-value">{{ $metric['value'] }}</span>
                                </div>

                                <div class="sari-health-progress">
                                    <span style="width: {{ $metric['width'] }}"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="sari-health-summary">
                    <div class="sari-health-summary-card">
                        <span class="sari-health-summary-icon bg-[#fff0f0] text-[#c95e5e]">
                            <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="m12 3 9 16H3L12 3Z"></path>
                                <path d="M12 9v4M12 17h.01"></path>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="sari-health-summary-label">Operational Risk</p>
                            <p class="sari-health-summary-value text-[#b85f5f]">{{ $liveHealth['risk'] ?? 'Low' }}</p>
                            <p class="sari-health-summary-note">{{ ($liveHealth['risk'] ?? 'Low') === 'Low' ? 'No critical issues' : 'Review active risk items' }}</p>
                        </div>
                    </div>

                    <div class="sari-health-summary-card">
                        <span class="sari-health-summary-icon bg-[#faf5ea] text-[#a77721]">
                            <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M5 6h14M5 12h14M5 18h14"></path>
                                <circle cx="3" cy="6" r=".8" fill="currentColor" stroke="none"></circle>
                                <circle cx="3" cy="12" r=".8" fill="currentColor" stroke="none"></circle>
                                <circle cx="3" cy="18" r=".8" fill="currentColor" stroke="none"></circle>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="sari-health-summary-label">Admin Queue</p>
                            <p class="sari-health-summary-value">{{ $formatNumber($liveHealth['admin_queue'] ?? 0) }} items</p>
                            <p class="sari-health-summary-note">Pending review</p>
                        </div>
                    </div>

                    <div class="sari-health-summary-card">
                        <span class="sari-health-summary-icon bg-[#eef7f2] text-[#4d8d68]">
                            <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M5 19V12M10 19V8M15 19v-4M20 19V5"></path>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="sari-health-summary-label">Fulfillment Health</p>
                            <p class="sari-health-summary-value text-[#4d8d68]">{{ number_format((float) ($liveHealth['fulfillment'] ?? 0), 1) }}%</p>
                            <p class="sari-health-summary-note">{{ (float) ($liveHealth['fulfillment'] ?? 0) >= 70 ? 'On track' : 'Needs monitoring' }}</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Recent Registrations --}}
            <aside class="sari-reg-clean-card">
                <div class="sari-reg-clean-head">
                    <div>
                        <h3 class="sari-reg-clean-title">Recent Registrations</h3>
                        <p class="sari-reg-clean-subtitle">Latest marketplace account applications</p>
                    </div>

                    <a href="{{ $liveUrls['registrations'] ?? '#' }}" class="sari-reg-clean-view-all">
                        View All
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M5 12h14M14 7l5 5-5 5"></path>
                        </svg>
                    </a>
                </div>

                <div class="sari-reg-clean-list">
                    @forelse ($registrations as $registration)
                        @php
                            $registrationStatus = strtolower((string) ($registration['status'] ?? 'pending'));
                            $registrationStatusClass = match ($registrationStatus) {
                                'approved' => 'is-approved',
                                'rejected' => 'is-rejected',
                                default => 'is-pending',
                            };
                        @endphp

                        <a href="{{ $liveUrls['registrations'] ?? '#' }}" class="sari-reg-clean-row">
                            <div class="sari-reg-clean-person">
                                <p class="sari-reg-clean-name">{{ $registration['name'] ?? 'Applicant' }}</p>
                                <p class="sari-reg-clean-role">{{ $registration['role'] ?? 'Account' }}</p>
                            </div>

                            <span class="sari-reg-clean-status {{ $registrationStatusClass }}">
                                {{ ucfirst($registrationStatus) }}
                            </span>

                            <span class="sari-reg-clean-time">{{ $registration['time'] ?? 'Recently' }}</span>
                        </a>
                    @empty
                        <div class="sari-reg-clean-empty">
                            <p class="sari-reg-clean-empty-title">No recent registrations</p>
                            <p class="sari-reg-clean-empty-copy">New marketplace applications will appear here.</p>
                        </div>
                    @endforelse
                </div>
            </aside>
        </div>

        {{-- Role Control Center + Live Order Journey --}}
        <div class="sari-ops-pair mt-4">

            {{-- Role Control Center --}}
            <section id="adminRoleControl" class="sari-ops-card">
                <div class="sari-ops-head">
                    <div class="sari-ops-heading">
                        <div class="min-w-0">
                            <h4 class="sari-ops-title">Role Control Center</h4>
                            <p class="sari-ops-subtitle">At-a-glance control across every marketplace role</p>
                        </div>
                    </div>

                    <a href="{{ $liveUrls['users'] ?? '#' }}" class="sari-ops-action">
                        Manage All
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="m9 6 6 6-6 6"></path>
                        </svg>
                    </a>
                </div>

                @php
                    $roles = [
                        [
                            'name'=>'Sellers',
                            'value'=>$formatNumber($liveRoles['sellers']['value'] ?? 0),
                            'issue'=>$liveRoles['sellers']['issue'] ?? '0 pending',
                            'tone'=>'bg-[#fff6e6] text-[#ad7318]',
                            'url'=>$liveUrls['sellers'] ?? '#',
                            'icon'=>'seller',
                        ],
                        [
                            'name'=>'Buyers',
                            'value'=>$formatNumber($liveRoles['buyers']['value'] ?? 0),
                            'issue'=>$liveRoles['buyers']['issue'] ?? '0 pending',
                            'tone'=>'bg-[#eef5fb] text-[#4f7da8]',
                            'url'=>$liveUrls['users'] ?? '#',
                            'icon'=>'buyer',
                        ],
                        [
                            'name'=>'Logistics',
                            'value'=>$formatNumber($liveRoles['logistics']['value'] ?? 0),
                            'issue'=>$liveRoles['logistics']['issue'] ?? '0 pending',
                            'tone'=>'bg-[#f3effb] text-[#7655ad]',
                            'url'=>$liveUrls['logistics_monitoring'] ?? '#',
                            'icon'=>'logistics',
                        ],
                        [
                            'name'=>'Riders',
                            'value'=>$formatNumber($liveRoles['riders']['value'] ?? 0),
                            'issue'=>$liveRoles['riders']['issue'] ?? '0 pending',
                            'tone'=>'bg-[#eef8f2] text-[#397c59]',
                            'url'=>$liveUrls['logistics_monitoring'] ?? '#',
                            'icon'=>'rider',
                        ],
                    ];
                @endphp

                <div class="sari-role-table">
                    <div class="sari-role-table-head" aria-hidden="true">
                        <span>Role</span>
                        <span>Accounts</span>
                        <span>Pending</span>
                        <span class="text-right">Action</span>
                    </div>

                    @foreach ($roles as $role)
                        <a href="{{ $role['url'] }}" class="sari-role-row">
                            <span class="sari-role-name">
                                <span class="sari-role-icon {{ $role['tone'] }}">
                                    @if ($role['icon'] === 'seller')
                                        <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M4 9h16M5 9l2-5h10l2 5M6 9v11h12V9M9 20v-6h6v6"></path>
                                        </svg>
                                    @elseif ($role['icon'] === 'buyer')
                                        <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <circle cx="12" cy="8" r="3"></circle>
                                            <path d="M5 21c.5-4.5 3-7 7-7s6.5 2.5 7 7"></path>
                                        </svg>
                                    @elseif ($role['icon'] === 'logistics')
                                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <path d="M3 7h11v10H3zM14 10h4l3 3v4h-7z"></path>
                                            <circle cx="7" cy="18" r="1.7"></circle>
                                            <circle cx="18" cy="18" r="1.7"></circle>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <circle cx="7" cy="17" r="3"></circle>
                                            <circle cx="17" cy="17" r="3"></circle>
                                            <path d="M7 17l4-8h3l3 8M9 13h7M11 9l-2-2M14 6h3"></path>
                                        </svg>
                                    @endif
                                </span>
                                <span class="truncate">{{ $role['name'] }}</span>
                            </span>

                            <span class="sari-role-value">{{ $role['value'] }}</span>
                            <span class="sari-role-pending">{{ $role['issue'] }}</span>

                            <span class="sari-role-chevron" aria-hidden="true">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="m9 6 6 6-6 6"></path>
                                </svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- Live Order Journey --}}
            <section id="adminOrderJourney" class="sari-ops-card">
                <div class="sari-ops-head">
                    <div class="sari-ops-heading">
                        <div class="min-w-0">
                            <h4 class="sari-ops-title">Live Order Journey</h4>
                            <p class="sari-ops-subtitle">Spot marketplace bottlenecks before they become complaints</p>
                        </div>
                    </div>

                    <span class="sari-ops-action cursor-default">
                        Today
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="m7 9 5 5 5-5"></path>
                        </svg>
                    </span>
                </div>

                @php
                    $journey = [
                        ['key'=>'new','name'=>'New','count'=>$formatNumber($liveJourney['new'] ?? 0),'tone'=>'is-good'],
                        ['key'=>'preparing','name'=>'Preparing','count'=>$formatNumber($liveJourney['preparing'] ?? 0),'tone'=>''],
                        ['key'=>'ready','name'=>'Ready','count'=>$formatNumber($liveJourney['ready'] ?? 0),'tone'=>($liveJourney['ready'] ?? 0) > 0 ? 'is-hot' : 'is-good'],
                        ['key'=>'pickup','name'=>'Pickup','count'=>$formatNumber($liveJourney['pickup'] ?? 0),'tone'=>'is-warn'],
                        ['key'=>'in_transit','name'=>'In Transit','count'=>$formatNumber($liveJourney['in_transit'] ?? 0),'tone'=>''],
                        ['key'=>'delivered','name'=>'Delivered','count'=>$formatNumber($liveJourney['delivered'] ?? 0),'tone'=>'is-good'],
                    ];
                    $readyPickupCount = (int) ($liveJourney['ready'] ?? 0);
                @endphp

                <div class="sari-journey-body">
                    <div class="sari-journey-scroll">
                        <div class="sari-journey-track">
                            @foreach ($journey as $stage)
                                <div class="sari-journey-step">
                                    <span class="sari-journey-icon {{ $stage['tone'] }}">
                                        @if ($stage['key'] === 'new')
                                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <path d="M6 3h9l3 3v15H6z"></path>
                                                <path d="M15 3v4h4M9 11h6M9 15h6"></path>
                                            </svg>
                                        @elseif ($stage['key'] === 'preparing')
                                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <path d="m4 7 8-4 8 4-8 4-8-4Z"></path>
                                                <path d="M4 7v10l8 4 8-4V7M12 11v10"></path>
                                            </svg>
                                        @elseif ($stage['key'] === 'ready')
                                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <path d="M5 6h14v13H5z"></path>
                                                <path d="M8 6V4h8v2M9 12l2 2 4-4"></path>
                                            </svg>
                                        @elseif ($stage['key'] === 'pickup')
                                            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <path d="M3 7h11v10H3zM14 10h4l3 3v4h-7z"></path>
                                                <circle cx="7" cy="18" r="1.7"></circle>
                                                <circle cx="18" cy="18" r="1.7"></circle>
                                            </svg>
                                        @elseif ($stage['key'] === 'in_transit')
                                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"></path>
                                                <circle cx="12" cy="10" r="2"></circle>
                                            </svg>
                                        @else
                                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="m5 12 4 4 10-10"></path>
                                            </svg>
                                        @endif
                                    </span>

                                    <p class="sari-journey-name">{{ $stage['name'] }}</p>
                                    <p class="sari-journey-count {{ $stage['tone'] === 'is-hot' ? 'text-[#b85f5f]' : '' }}">{{ $stage['count'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="sari-journey-status {{ $readyPickupCount > 0 ? 'is-alert' : '' }}">
                        <span class="sari-journey-status-icon">
                            @if ($readyPickupCount > 0)
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M12 4 3 20h18L12 4Z"></path>
                                    <path d="M12 9v5M12 17h.01"></path>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m5 12 4 4 10-10"></path>
                                </svg>
                            @endif
                        </span>

                        <div class="min-w-0">
                            <p class="sari-journey-status-title">
                                {{ $readyPickupCount > 0 ? 'Pickup attention needed' : 'All clear!' }}
                            </p>
                            <p class="sari-journey-status-copy">
                                {{ $readyPickupCount > 0
                                    ? $formatNumber($readyPickupCount) . ' order(s) are ready for pickup and waiting in the Logistics flow.'
                                    : 'No ready-for-pickup bottleneck right now.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- Intervention Radar + Rider Logistics --}}
        <div class="sari-monitor-grid mt-4">

            {{-- Intervention Radar --}}
            <section id="adminInterventionRadar" class="sari-monitor-card">
                <div class="sari-monitor-head">
                    <div class="sari-monitor-heading">
                        <div class="min-w-0">
                            <h4 class="sari-monitor-title">Intervention Radar</h4>
                            <p class="sari-monitor-subtitle">Real-time issues that need admin judgment</p>
                        </div>
                    </div>

                    <button type="button" class="sari-monitor-action" data-admin-focus="risk">
                        Risk Focus
                    </button>
                </div>

                @php
                    $riskItems = $focusItems->take(4)->values();
                    $priorityRiskCount = (int) $riskItems
                        ->filter(fn ($item) => in_array(($item['severity'] ?? 'low'), ['medium', 'high'], true))
                        ->sum(fn ($item) => (int) ($item['count'] ?? 0));
                @endphp

                <div class="sari-risk-body">
                    <div class="sari-risk-list">
                        @forelse ($riskItems as $risk)
                            @php
                                $riskSeverity = strtolower((string) ($risk['severity'] ?? 'low'));
                                $riskUrl = $risk['url'] ?? '#';
                            @endphp

                            <a href="{{ $riskUrl }}" class="sari-risk-item">
                                <span class="sari-risk-pill {{ $riskSeverity }}">
                                    {{ ucfirst($riskSeverity) }}
                                </span>

                                <span class="sari-risk-name">
                                    {{ $formatNumber($risk['count'] ?? 0) }} · {{ $risk['title'] ?? 'Operational item' }}
                                </span>

                                <span class="sari-risk-live">
                                    <span class="sari-risk-live-dot"></span>
                                    Live
                                </span>
                            </a>
                        @empty
                            <div class="sari-risk-empty">
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#edf7f2] text-[#3f8560]">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m5 12 4 4 10-10"></path>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-[10px] font-semibold text-[#3f6f56]">No active intervention items</p>
                                    <p class="mt-1 text-[8.5px] text-[#87968d]">New marketplace risks will appear here automatically.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="sari-risk-state {{ $priorityRiskCount > 0 ? 'is-alert' : '' }}">
                        <span class="sari-risk-state-icon">
                            @if ($priorityRiskCount > 0)
                                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M12 4 3 20h18L12 4Z"></path>
                                    <path d="M12 9v5M12 17h.01"></path>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z"></path>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg>
                            @endif
                        </span>

                        <div class="min-w-0">
                            <p class="sari-risk-state-title">
                                {{ $priorityRiskCount > 0 ? 'Review required' : 'All clear — No critical marketplace risks right now.' }}
                            </p>
                            <p class="sari-risk-state-copy">
                                {{ $priorityRiskCount > 0
                                    ? $formatNumber($priorityRiskCount) . ' medium/high priority item(s) need administrator attention.'
                                    : "We'll surface any emerging issues here." }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Rider & Logistics Overview --}}
            <section id="adminRiderOverview" class="sari-monitor-card">
                <div class="sari-monitor-head">
                    <div class="sari-monitor-heading">
                        <div class="min-w-0">
                            <h4 class="sari-monitor-title">Rider &amp; Logistics Overview</h4>
                            <p class="sari-monitor-subtitle">Live operational capacity and delivery pressure</p>
                        </div>
                    </div>

                    <a href="{{ $liveUrls['logistics_monitoring'] ?? '#' }}" class="sari-monitor-action">
                        Manage
                    </a>
                </div>

                @php
                    $riderTotal = max(0, (int) ($liveRiders['total'] ?? 0));
                    $riderOnDelivery = max(0, min($riderTotal, (int) ($liveRiders['on_delivery'] ?? 0)));
                    $riderIdle = max(0, min($riderTotal - $riderOnDelivery, (int) ($liveRiders['idle'] ?? 0)));
                    $riderOffline = max(0, min($riderTotal - $riderOnDelivery - $riderIdle, (int) ($liveRiders['offline'] ?? 0)));
                    $riderAvailable = max(0, $riderTotal - $riderOnDelivery - $riderIdle - $riderOffline);

                    if ($riderTotal > 0) {
                        $riderCursor = 0.0;
                        $riderSegments = [];

                        foreach ([
                            ['count' => $riderOnDelivery, 'color' => '#3f9275'],
                            ['count' => $riderIdle, 'color' => '#c99524'],
                            ['count' => $riderOffline, 'color' => '#8b9295'],
                            ['count' => $riderAvailable, 'color' => '#73b59c'],
                        ] as $segment) {
                            if ($segment['count'] <= 0) {
                                continue;
                            }

                            $start = $riderCursor;
                            $riderCursor += ($segment['count'] / $riderTotal) * 360;
                            $riderSegments[] = $segment['color'] . ' ' . round($start, 1) . 'deg ' . round($riderCursor, 1) . 'deg';
                        }

                        $riderDonut = count($riderSegments) > 0
                            ? 'conic-gradient(' . implode(',', $riderSegments) . ')'
                            : 'conic-gradient(#ece7df 0deg 360deg)';
                    } else {
                        $riderDonut = 'conic-gradient(#ece7df 0deg 360deg)';
                    }

                    $riderStats = [
                        ['label' => 'Online', 'value' => $formatNumber($liveRiders['online'] ?? 0), 'dot' => '#3f9275'],
                        ['label' => 'On Delivery', 'value' => $formatNumber($liveRiders['on_delivery'] ?? 0), 'dot' => '#3f9275'],
                        ['label' => 'Idle', 'value' => $formatNumber($liveRiders['idle'] ?? 0), 'dot' => '#c99524'],
                        ['label' => 'Offline', 'value' => $formatNumber($liveRiders['offline'] ?? 0), 'dot' => '#8b9295'],
                        ['label' => 'Open Issues', 'value' => $formatNumber($liveRiders['open_issues'] ?? 0), 'dot' => '#d94f4f'],
                        ['label' => 'Logistics', 'value' => $formatNumber($liveRiders['active_logistics'] ?? 0), 'dot' => '#284b73'],
                    ];
                @endphp

                <div class="sari-rider-body">
                    <div class="sari-rider-main">
                        <div class="sari-rider-donut-wrap">
                            <div class="sari-rider-donut-v2" style="background: {{ $riderDonut }}">
                                <div class="sari-rider-donut-center">
                                    <p class="sari-rider-total">{{ $formatNumber($riderTotal) }}</p>
                                    <p class="sari-rider-total-label">Total Riders</p>
                                </div>
                            </div>
                        </div>

                        <div class="sari-rider-stats">
                            @foreach ($riderStats as $stat)
                                <div class="sari-rider-stat">
                                    <div class="sari-rider-stat-name">
                                        <span class="sari-rider-stat-dot" style="background: {{ $stat['dot'] }}"></span>
                                        <span>{{ $stat['label'] }}</span>
                                    </div>
                                    <span class="sari-rider-stat-value">{{ $stat['value'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="sari-rider-summary">
                        <div class="sari-rider-summary-item">
                            <p class="sari-rider-summary-label">Waiting pickup</p>
                            <p class="sari-rider-summary-value {{ (int) ($liveRiders['ready_pickup'] ?? 0) > 0 ? 'text-[#b85f5f]' : '' }}">
                                {{ $formatNumber($liveRiders['ready_pickup'] ?? 0) }}
                            </p>
                        </div>

                        <div class="sari-rider-summary-item">
                            <p class="sari-rider-summary-label">In transit</p>
                            <p class="sari-rider-summary-value text-[#3f9275]">
                                {{ $formatNumber($liveRiders['in_transit'] ?? 0) }}
                            </p>
                        </div>

                        <div class="sari-rider-summary-item">
                            <p class="sari-rider-summary-label">Active logistics</p>
                            <p class="sari-rider-summary-value">
                                {{ $formatNumber($liveRiders['active_logistics'] ?? 0) }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>


    {{-- =========================================================
        MAIN ANALYTICS — CLEAN MODERN SALES + TODAY'S FOCUS
    ========================================================== --}}
    <section class="sari-analytics-clean-grid mt-4">
        @php
            // Sales-only chart variables are intentionally scoped inside
            // admin.partials.sales-overview so async fragment rendering and
            // the full dashboard use the exact same calculation path.
            $registrations = collect($liveRegistrations)->take(5)->values();
        @endphp

        {{-- Sales Overview --}}
        @include('admin.partials.sales-overview', ['liveSales' => $liveSales])

        {{-- Today's Focus --}}
        <section class="sari-health-card p-5 sm:p-6">
            <div class="sari-health-header">
                <div class="sari-health-heading">
                    <div class="min-w-0">
                        <h4 class="sari-health-title">Today's Focus</h4>
                        <p class="sari-health-subtitle"><span id="adminFocusCount">{{ $focusItems->count() }}</span> items need your attention</p>
                    </div>
                </div>

                <button type="button" class="sari-health-action" data-admin-scroll="adminInterventionRadar">
                    View All
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="m9 6 6 6-6 6"></path>
                    </svg>
                </button>
            </div>

            @if ($focusItems->isEmpty())
                <div class="sari-focus-empty-v2">
                    <div class="flex items-start gap-3">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#2f7d56] text-white">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m5 12 4 4 10-10"></path>
                            </svg>
                        </span>
                        <div>
                            <p class="text-[10px] font-bold">All clear</p>
                            <p class="mt-1 text-[8.5px] leading-4 text-[#71867a]">No active focus items require administrator attention right now.</p>
                        </div>
                    </div>
                </div>
            @else
                <div id="adminFocusList" class="sari-focus-list-v2">
                    @foreach ($focusItems as $item)
                        <button
                            type="button"
                            class="sari-focus-row-v2"
                            data-admin-focus-item
                            data-focus-id="{{ $item['id'] }}"
                            data-title="{{ $item['title'] }}"
                            data-role="{{ $item['role'] }}"
                            data-severity="{{ ucfirst($item['severity']) }}"
                            data-detail="{{ $item['detail'] }}"
                            data-action="{{ $item['action'] }}"
                            data-url="{{ $item['url'] ?? '' }}"
                        >
                            <span class="sari-focus-icon-v2 {{ $item['severity'] === 'high' ? 'bg-[#fff0f0] text-[#c45b5b]' : ($item['severity'] === 'medium' ? 'bg-[#fff5df] text-[#ad751a]' : 'bg-[#eef7f2] text-[#3d865f]') }}">
                                @php $focusRole = strtolower((string) ($item['role'] ?? '')); @endphp

                                @if (str_contains($focusRole, 'rider') || str_contains(strtolower((string) $item['title']), 'deliver'))
                                    <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M3 7h11v10H3zM14 10h4l3 3v4h-7z"></path>
                                        <circle cx="7" cy="18" r="1.5"></circle>
                                        <circle cx="18" cy="18" r="1.5"></circle>
                                    </svg>
                                @elseif (str_contains($focusRole, 'seller') || str_contains(strtolower((string) $item['title']), 'registration'))
                                    <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="12" cy="8" r="3"></circle>
                                        <path d="M5 20c.5-4 3-6 7-6s6.5 2 7 6"></path>
                                    </svg>
                                @elseif (str_contains($focusRole, 'support') || str_contains(strtolower((string) $item['title']), 'complaint'))
                                    <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="m12 3 9 16H3L12 3Z"></path>
                                        <path d="M12 9v4M12 17h.01"></path>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="m12 3 8 4-8 4-8-4 8-4Z"></path>
                                        <path d="M4 7v10l8 4 8-4V7"></path>
                                    </svg>
                                @endif
                            </span>

                            <span class="min-w-0">
                                <span class="sari-focus-title-v2 block truncate">{{ $item['title'] }}</span>
                                <span class="sari-focus-copy-v2 block truncate">{{ $item['detail'] }}</span>
                            </span>

                            <span class="sari-focus-count-v2">{{ $formatNumber($item['count'] ?? 0) }}</span>

                            <span class="sari-focus-badge-v2 {{ $item['severity'] === 'high' ? 'bg-[#fbecec] text-[#b95656]' : ($item['severity'] === 'medium' ? 'bg-[#fff4dd] text-[#a86f16]' : 'bg-[#edf7f2] text-[#4b8d69]') }}">
                                {{ ucfirst($item['severity']) }}
                            </span>

                            <span class="sari-focus-chevron-v2" aria-hidden="true">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="m9 6 6 6-6 6"></path>
                                </svg>
                            </span>
                        </button>
                    @endforeach
                </div>
            @endif
        </section>
    </section>


    {{-- =========================================================
        BOTTOM PANELS
    ========================================================== --}}
    <section class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-[1.05fr_.95fr]">
        <div id="adminCategoryBreakdown" class="admin-panel sari-category-card">
            <div class="sari-category-head">
                <div class="sari-category-heading">
                    <span class="sari-category-head-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3v9h9"></path>
                            <path d="M20 15a8 8 0 1 1-11-11"></path>
                        </svg>
                    </span>

                    <div class="min-w-0">
                        <h3 class="sari-category-title">Catalog Category Breakdown</h3>
                        <p class="sari-category-subtitle">Distribution of marketplace listings</p>
                    </div>
                </div>

                <a href="{{ $liveUrls['seller_compliance'] ?? '#' }}" class="sari-category-action">
                    <span>View All Categories</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m9 6 6 6-6 6"></path>
                    </svg>
                </a>
            </div>

            <div class="sari-category-layout">
                <div class="sari-category-donut-wrap">
                    <div
                        class="sari-category-donut"
                        style="background:{{ $categoryDonut }}"
                        aria-label="Category distribution chart"
                    >
                        <div class="sari-category-donut-center">
                            <p class="sari-category-donut-label">Total listings</p>
                            <p class="sari-category-donut-value">{{ $formatNumber($liveCategories['total'] ?? 0) }}</p>
                            <span class="sari-category-donut-accent" aria-hidden="true"></span>
                            <p class="sari-category-donut-meta">
                                {{ $formatNumber($categoryRows->count()) }} categories
                            </p>
                        </div>
                    </div>
                </div>

                <div class="sari-category-list">
                    @forelse ($categoryRows as $row)
                        @php
                            $categoryName = strtolower((string) ($row['name'] ?? ''));
                            $categorySoft = ($row['dot'] ?? '#9f968a') . '14';

                            $categoryIcon = match (true) {
                                str_contains($categoryName, 'electronic'),
                                str_contains($categoryName, 'computer'),
                                str_contains($categoryName, 'gadget') => 'electronics',

                                str_contains($categoryName, 'food'),
                                str_contains($categoryName, 'beverage'),
                                str_contains($categoryName, 'grocery') => 'food',

                                str_contains($categoryName, 'home'),
                                str_contains($categoryName, 'living'),
                                str_contains($categoryName, 'furniture') => 'home',

                                default => 'other',
                            };
                        @endphp

                        <div
                            class="sari-category-row"
                            style="--category-color:{{ $row['dot'] }};--category-soft:{{ $categorySoft }};"
                        >
                            <span class="sari-category-icon" aria-hidden="true">
                                @if ($categoryIcon === 'electronics')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="4" y="4.5" width="16" height="11" rx="1.8"></rect>
                                        <path d="M9 19.5h6M12 15.5v4"></path>
                                    </svg>
                                @elseif ($categoryIcon === 'food')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 3v7M9 3v7M6 7h3M7.5 10v11"></path>
                                        <path d="M16.5 3v18"></path>
                                        <path d="M16.5 3c2.2 1.7 2.8 4.3 2.3 7h-2.3"></path>
                                    </svg>
                                @elseif ($categoryIcon === 'home')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m4 11 8-7 8 7"></path>
                                        <path d="M6 10v10h12V10"></path>
                                        <path d="M10 20v-6h4v6"></path>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m12 3 7 4-7 4-7-4 7-4Z"></path>
                                        <path d="m5 7 7 4 7-4"></path>
                                        <path d="M5 7v9l7 5 7-5V7"></path>
                                        <path d="M12 11v10"></path>
                                    </svg>
                                @endif
                            </span>

                            <div class="sari-category-row-body">
                                <div class="sari-category-row-top">
                                    <span class="sari-category-name">{{ $row['name'] }}</span>
                                    <span class="sari-category-percent">{{ $row['value'] }}</span>
                                </div>

                                <div
                                    class="sari-category-progress"
                                    role="progressbar"
                                    aria-label="{{ $row['name'] }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100"
                                    aria-valuenow="{{ number_format((float) $row['percent'], 1, '.', '') }}"
                                >
                                    <span style="width:{{ $row['percent'] }}%;"></span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[14px] border border-[#eee7de] bg-[#fcfbf8] px-4 py-5">
                            <p class="text-[10px] font-semibold text-[#4d453d]">No category data yet</p>
                            <p class="mt-1 text-[8px] leading-4 text-[#958d84]">
                                Category distribution will appear once marketplace listings are available.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="admin-panel p-5 sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-[10px] bg-[#fff7e7] text-[#c99524]"><svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="8"></circle><path d="M12 7v5l3 2"></path></svg></span>
                    <div><h3 class="text-[15px] font-bold text-[#211d17]">Platform Activity</h3><p class="mt-0.5 text-[10px] text-[#978f84]">Latest platform events and activities</p></div>
                </div>
                <a href="{{ $liveUrls['reports'] ?? '#' }}" class="admin-view-button">View All</a>
            </div>

            @php
                $activities = collect($liveActivity)->map(function ($activity) {
                    $kind = strtolower((string) ($activity['kind'] ?? ''));
                    $title = strtolower((string) ($activity['title'] ?? ''));
                    $body = strtolower((string) ($activity['body'] ?? ''));
                    $text = trim($kind . ' ' . $title . ' ' . $body);

                    $activity['tone'] = 'bg-[#f3f5f7] text-[#66778a]';
                    $activity['icon'] = 'clock';

                    if (str_contains($text, 'registration') || str_contains($text, 'application') || str_contains($text, 'account')) {
                        $activity['tone'] = 'bg-[#eef7f2] text-[#4d8d68]';
                        $activity['icon'] = 'user-plus';
                    } elseif (str_contains($text, 'preparing') || str_contains($text, 'packed') || str_contains($text, 'fulfillment')) {
                        $activity['tone'] = 'bg-[#eef3f7] text-[#5d7fa1]';
                        $activity['icon'] = 'package';
                    } elseif (str_contains($text, 'order placed') || str_contains($text, 'placed') || str_contains($text, 'checkout')) {
                        $activity['tone'] = 'bg-[#eef3f7] text-[#5d7fa1]';
                        $activity['icon'] = 'receipt';
                    } elseif (str_contains($text, 'complaint') || str_contains($text, 'dispute') || str_contains($text, 'report')) {
                        $activity['tone'] = 'bg-[#fff1f0] text-[#c96a63]';
                        $activity['icon'] = 'alert';
                    } elseif (str_contains($text, 'seller')) {
                        $activity['tone'] = 'bg-[#f3f8f4] text-[#5e8d72]';
                        $activity['icon'] = 'store';
                    } elseif (str_contains($text, 'commission') || str_contains($text, 'payout') || str_contains($text, 'payment')) {
                        $activity['tone'] = 'bg-[#f7f2fb] text-[#8768b1]';
                        $activity['icon'] = 'peso';
                    }

                    return $activity;
                });
            @endphp
            <div class="mt-4 space-y-1">
                @foreach ($activities as $activity)
                    <div class="flex items-start gap-3 rounded-[12px] px-2 py-2.5 transition hover:bg-[#fcfbf8]">
                        <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-[9px] {{ $activity['tone'] }}">
                            @if (($activity['icon'] ?? '') === 'user-plus')
                                <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M15 19a4.5 4.5 0 0 0-9 0"></path>
                                    <circle cx="10.5" cy="8" r="3"></circle>
                                    <path d="M18 8v5"></path>
                                    <path d="M15.5 10.5h5"></path>
                                </svg>
                            @elseif (($activity['icon'] ?? '') === 'package')
                                <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="m12 3 7 4v10l-7 4-7-4V7l7-4Z"></path>
                                    <path d="M12 21V11"></path>
                                    <path d="m19 7-7 4-7-4"></path>
                                </svg>
                            @elseif (($activity['icon'] ?? '') === 'receipt')
                                <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M7 3h10v18l-2-1.5L13 21l-2-1.5L9 21l-2-1.5L5 21V5a2 2 0 0 1 2-2Z"></path>
                                    <path d="M9 8h6"></path>
                                    <path d="M9 12h6"></path>
                                </svg>
                            @elseif (($activity['icon'] ?? '') === 'alert')
                                <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="m12 4 8 14H4l8-14Z"></path>
                                    <path d="M12 9v4.5"></path>
                                    <circle cx="12" cy="16.5" r=".8" fill="currentColor" stroke="none"></circle>
                                </svg>
                            @elseif (($activity['icon'] ?? '') === 'store')
                                <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M4 9.5 5.5 5h13L20 9.5"></path>
                                    <path d="M5 10v8.5A1.5 1.5 0 0 0 6.5 20h11a1.5 1.5 0 0 0 1.5-1.5V10"></path>
                                    <path d="M9 20v-5h6v5"></path>
                                </svg>
                            @elseif (($activity['icon'] ?? '') === 'peso')
                                <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 4v16"></path>
                                    <path d="M9 7.5h4a3 3 0 1 1 0 6H9"></path>
                                    <path d="M8 15.5h5a3 3 0 1 1 0 6H8" transform="translate(0 -4)"></path>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="7.5"></circle>
                                    <path d="M12 8v4l2.5 1.5"></path>
                                </svg>
                            @endif
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-[10.5px] font-semibold text-[#37312a]">{{ $activity['title'] }}</p>
                                <span class="shrink-0 text-[8px] text-[#aaa198]">{{ $activity['time'] }}</span>
                            </div>
                            <p class="mt-0.5 text-[8.5px] leading-4 text-[#92897f]">{{ $activity['body'] }}</p>
                        </div>
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
            “Mark reviewed” hides the item for this browser session. Use “Open related area” to manage the real record in the connected backend.
        </p>
    </div>
</aside>

<div id="adminToast" class="admin-toast">Updated.</div>

<script>
(function () {
    function initSariAdminDashboard() {
        window.__SARI_ADMIN_DASHBOARD_TIMER__ && clearInterval(window.__SARI_ADMIN_DASHBOARD_TIMER__);

        const greeting = document.getElementById('adminGreetingText');
        const timeNode = document.getElementById('adminCurrentTime');
        const dayNode = document.getElementById('adminCurrentDay');
        const dateNode = document.getElementById('adminCurrentDate');
        const timezoneNode = document.getElementById('adminTimezone');
        const weatherTempNode = document.getElementById('adminWeatherTemp');
        const weatherConditionNode = document.getElementById('adminWeatherCondition');

        function weatherCodeToText(code) {
            const map = {
                0: 'Clear sky',
                1: 'Mainly clear',
                2: 'Partly cloudy',
                3: 'Overcast',
                45: 'Foggy',
                48: 'Rime fog',
                51: 'Light drizzle',
                53: 'Drizzle',
                55: 'Heavy drizzle',
                56: 'Freezing drizzle',
                57: 'Heavy freezing drizzle',
                61: 'Light rain',
                63: 'Rain',
                65: 'Heavy rain',
                66: 'Freezing rain',
                67: 'Heavy freezing rain',
                71: 'Light snow',
                73: 'Snow',
                75: 'Heavy snow',
                77: 'Snow grains',
                80: 'Rain showers',
                81: 'Heavy showers',
                82: 'Violent showers',
                85: 'Snow showers',
                86: 'Heavy snow showers',
                95: 'Thunderstorm',
                96: 'Storm with hail',
                99: 'Severe storm',
            };

            return map[Number(code)] || 'Weather unavailable';
        }

        const WEATHER_CACHE_KEY = 'sari-admin-weather-v2';
        const WEATHER_TTL = 10 * 60 * 1000;
        const weatherFallback = { latitude: 14.5995, longitude: 120.9842 };

        const timeFormatter = new Intl.DateTimeFormat('en-US', {
            hour:'numeric',
            minute:'2-digit',
            hour12:true,
        });
        const dayFormatter = new Intl.DateTimeFormat('en-US', { weekday:'long' });
        const dateFormatter = new Intl.DateTimeFormat('en-US', {
            month:'long',
            day:'numeric',
            year:'numeric',
        });
        const localTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Local time';

        function readWeatherCache() {
            try {
                const cached = JSON.parse(sessionStorage.getItem(WEATHER_CACHE_KEY) || 'null');
                if (!cached || !cached.savedAt) return null;
                if ((Date.now() - cached.savedAt) > WEATHER_TTL) return null;
                return cached;
            } catch {
                return null;
            }
        }

        function paintWeather(weather) {
            if (!weatherTempNode || !weatherConditionNode || !weather) return;

            weatherTempNode.textContent = weather.temperature === null
                ? '—'
                : `${weather.temperature}°C`;

            weatherConditionNode.textContent = weather.condition || 'Weather unavailable';
        }

        async function fetchAdminWeather(latitude, longitude) {
            if (!weatherTempNode || !weatherConditionNode || document.hidden) return;

            try {
                const response = await fetch(
                    `https://api.open-meteo.com/v1/forecast?latitude=${encodeURIComponent(latitude)}&longitude=${encodeURIComponent(longitude)}&current=temperature_2m,weather_code&timezone=auto`,
                    {
                        method:'GET',
                        headers:{ 'Accept':'application/json' },
                        cache:'default',
                    }
                );

                if (!response.ok) {
                    throw new Error(`Weather request failed: ${response.status}`);
                }

                const data = await response.json();
                const current = data?.current || {};
                const rawTemperature = Number(current.temperature_2m);
                const temperature = Number.isFinite(rawTemperature)
                    ? Math.round(rawTemperature)
                    : null;

                const weather = {
                    temperature,
                    condition:weatherCodeToText(current.weather_code),
                    savedAt:Date.now(),
                };

                paintWeather(weather);

                try {
                    sessionStorage.setItem(WEATHER_CACHE_KEY, JSON.stringify(weather));
                } catch {
                    // Storage can be unavailable in restrictive browser modes.
                }
            } catch {
                const cached = readWeatherCache();

                if (cached) {
                    paintWeather(cached);
                    return;
                }

                if (!weatherTempNode.textContent || weatherTempNode.textContent === '--°C') {
                    weatherTempNode.textContent = '—';
                    weatherConditionNode.textContent = 'Weather unavailable';
                }
            }
        }

        async function initAdminWeather({ force = false } = {}) {
            if (!weatherTempNode || !weatherConditionNode || document.hidden) return;

            const cached = readWeatherCache();

            if (cached && !force) {
                paintWeather(cached);
                return;
            }

            weatherConditionNode.textContent = cached?.condition || 'Loading weather...';

            // One fallback request is enough for the initial dashboard paint.
            await fetchAdminWeather(weatherFallback.latitude, weatherFallback.longitude);

            // Only request browser coordinates when permission has already been granted.
            // This avoids a location prompt and a second network call on every page load.
            if (!navigator.geolocation || !navigator.permissions?.query) return;

            try {
                const permission = await navigator.permissions.query({ name:'geolocation' });

                if (permission.state !== 'granted') return;

                navigator.geolocation.getCurrentPosition(
                    position => {
                        fetchAdminWeather(
                            position.coords.latitude,
                            position.coords.longitude
                        );
                    },
                    () => {},
                    {
                        enableHighAccuracy:false,
                        timeout:3500,
                        maximumAge:15 * 60 * 1000,
                    }
                );
            } catch {
                // Permission API is optional; fallback weather is already available.
            }
        }

        function updateAdminClock() {
            const now = new Date();
            const hour = now.getHours();
            const label = hour < 12
                ? 'Good morning'
                : (hour < 18 ? 'Good afternoon' : 'Good evening');

            if (greeting) greeting.textContent = `${label}, Admin!`;
            if (timeNode) timeNode.textContent = timeFormatter.format(now);
            if (dayNode) dayNode.textContent = dayFormatter.format(now);
            if (dateNode) dateNode.textContent = dateFormatter.format(now);
            if (timezoneNode) timezoneNode.textContent = localTimezone;

            const salesUpdated = document.getElementById('adminSalesUpdatedTime');

            if (salesUpdated) {
                salesUpdated.textContent = `Updated today, ${timeFormatter.format(now)}`;
            }
        }

        updateAdminClock();
        window.__SARI_ADMIN_DASHBOARD_TIMER__ = setInterval(updateAdminClock, 30000);

        window.__SARI_ADMIN_WEATHER_TIMER__ && clearInterval(window.__SARI_ADMIN_WEATHER_TIMER__);

        const scheduleWeather = callback => {
            if ('requestIdleCallback' in window) {
                window.requestIdleCallback(callback, { timeout:1200 });
            } else {
                window.setTimeout(callback, 120);
            }
        };

        const cachedWeather = readWeatherCache();

        if (cachedWeather) {
            paintWeather(cachedWeather);
        } else {
            scheduleWeather(() => initAdminWeather());
        }

        window.__SARI_ADMIN_WEATHER_TIMER__ = setInterval(() => {
            if (!document.hidden) initAdminWeather({ force:true });
        }, WEATHER_TTL);

        // Avoid duplicated listeners after Livewire navigation.
        window.__SARI_ADMIN_CONTROL_ABORT__?.abort();
        const controller = new AbortController();
        window.__SARI_ADMIN_CONTROL_ABORT__ = controller;
        const { signal } = controller;

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && !readWeatherCache()) {
                scheduleWeather(() => initAdminWeather());
            }
        }, { signal });

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
        let currentTargetUrl = null;
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

            const reduceMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

            node.scrollIntoView({
                behavior: reduceMotion ? 'auto' : 'smooth',
                block:'start',
            });
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

            currentTargetUrl = button.dataset.url || null;
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
            if (currentTargetUrl) {
                window.location.href = currentTargetUrl;
                return;
            }

            closeDrawer();
            setTimeout(() => smoothScrollTo(currentTargetId), 60);
        }, { signal });
    }

    document.addEventListener('DOMContentLoaded', initSariAdminDashboard, { once: true });
    document.addEventListener('livewire:navigated', initSariAdminDashboard);
})();
</script>


<script>
(function () {
    const money = new Intl.NumberFormat('en-PH', {
        style:'currency',
        currency:'PHP',
        minimumFractionDigits:2,
        maximumFractionDigits:2,
    });

    function animateSariSalesGraphLikeSeller(chart) {
        if (!chart) return;

        const lines = Array.from(chart.querySelectorAll('.sari-sales-clean-line'));
        const areas = Array.from(chart.querySelectorAll('.sari-sales-clean-area'));
        const points = Array.from(chart.querySelectorAll('.sari-sales-clean-point'));

        if (!lines.length) return;

        try {
            /*
             * Match the Seller graph:
             * - use each SVG path's real length
             * - start points at scale(.65)
             * - double requestAnimationFrame before animating
             * - .55s line draw with the same easing
             * - .38s area reveal with .10s delay
             * - .24s staggered point reveal, 45ms each capped at 220ms
             */
            lines.forEach((line) => {
                const length = line.getTotalLength();

                line.style.transition = 'none';
                line.style.strokeDasharray = String(length);
                line.style.strokeDashoffset = String(length);
            });

            areas.forEach((area) => {
                area.style.transition = 'none';
                area.style.opacity = '0';
            });

            points.forEach((point) => {
                point.style.opacity = '0';
                point.style.transformBox = 'fill-box';
                point.style.transformOrigin = 'center';
                point.style.transform = 'scale(.65)';
                point.style.transition = 'none';
            });

            window.requestAnimationFrame(() => {
                window.requestAnimationFrame(() => {
                    lines.forEach((line) => {
                        line.style.transition =
                            'stroke-dashoffset .55s cubic-bezier(.22,1,.36,1)';
                        line.style.strokeDashoffset = '0';
                    });

                    areas.forEach((area) => {
                        area.style.transition = 'opacity .38s ease .10s';
                        area.style.opacity = '1';
                    });

                    points.forEach((point, index) => {
                        const delay = Math.min(index * 45, 220);

                        point.style.transition =
                            `opacity .24s ease ${delay}ms, transform .24s ease ${delay}ms`;
                        point.style.opacity = '1';
                        point.style.transform = 'scale(1)';
                    });
                });
            });
        } catch (error) {
            lines.forEach((line) => {
                line.style.strokeDasharray = '';
                line.style.strokeDashoffset = '';
            });

            areas.forEach((area) => {
                area.style.opacity = '1';
            });

            points.forEach((point) => {
                point.style.opacity = '1';
                point.style.transform = 'scale(1)';
            });
        }
    }

    function initSariSalesChartInteractions() {
        window.__SARI_SALES_CHART_ABORT__?.abort();

        const controller = new AbortController();
        window.__SARI_SALES_CHART_ABORT__ = controller;
        const { signal } = controller;

        const chart = document.querySelector('#adminSalesOverview [data-sales-chart]');
        if (!chart) return;

        animateSariSalesGraphLikeSeller(chart);

        const tooltip = chart.querySelector('[data-sales-tooltip]');
        const tooltipWeek = tooltip?.querySelector('[data-tooltip-week]');
        const tooltipSales = tooltip?.querySelector('[data-tooltip-sales]');
        const tooltipCommission = tooltip?.querySelector('[data-tooltip-commission]');
        const hits = Array.from(chart.querySelectorAll('[data-chart-hit]'));
        const guides = Array.from(chart.querySelectorAll('[data-chart-guide]'));
        const guideMap = new Map(
            guides.map(guide => [guide.getAttribute('data-chart-guide'), guide])
        );

        let hoverFrame = 0;
        let activeHit = null;
        let latestPointer = null;
        let chartRect = null;

        function hideTooltip() {
            if (hoverFrame) {
                window.cancelAnimationFrame(hoverFrame);
                hoverFrame = 0;
            }

            activeHit = null;
            latestPointer = null;
            chartRect = null;

            tooltip?.classList.remove('is-visible');
            tooltip?.setAttribute('aria-hidden','true');
            guides.forEach(guide => guide.classList.remove('is-visible'));
        }

        function renderTooltip(hit, pointer = null) {
            if (!tooltip || !hit) return;

            if (tooltipWeek) tooltipWeek.textContent = hit.dataset.week || 'Period';
            if (tooltipSales) tooltipSales.textContent = money.format(Number(hit.dataset.sales || 0));
            if (tooltipCommission) {
                tooltipCommission.textContent = money.format(Number(hit.dataset.commission || 0));
            }

            guides.forEach(guide => guide.classList.remove('is-visible'));
            guideMap.get(hit.dataset.guideIndex)?.classList.add('is-visible');

            tooltip.classList.add('is-visible');
            tooltip.setAttribute('aria-hidden','false');

            chartRect ||= chart.getBoundingClientRect();

            const tooltipWidth = tooltip.offsetWidth || 142;
            const tooltipHeight = tooltip.offsetHeight || 80;

            let left;
            let top;

            if (pointer) {
                left = pointer.clientX - chartRect.left + 12;
                top = pointer.clientY - chartRect.top - tooltipHeight - 12;
            } else {
                const hitRect = hit.getBoundingClientRect();
                left = hitRect.left - chartRect.left + (hitRect.width / 2) + 12;
                top = hitRect.top - chartRect.top + 20;
            }

            left = Math.max(8, Math.min(left, chart.clientWidth - tooltipWidth - 8));
            top = Math.max(8, Math.min(top, chart.clientHeight - tooltipHeight - 8));

            tooltip.style.left = `${left}px`;
            tooltip.style.top = `${top}px`;
        }

        function scheduleTooltip(hit, event = null) {
            activeHit = hit;
            latestPointer = event
                ? { clientX:event.clientX, clientY:event.clientY }
                : null;

            if (hoverFrame) return;

            hoverFrame = window.requestAnimationFrame(() => {
                hoverFrame = 0;
                renderTooltip(activeHit, latestPointer);
            });
        }

        hits.forEach(hit => {
            hit.addEventListener('pointerenter', event => {
                chartRect = chart.getBoundingClientRect();
                scheduleTooltip(hit,event);
            }, { signal, passive:true });

            hit.addEventListener('pointermove', event => {
                scheduleTooltip(hit,event);
            }, { signal, passive:true });

            hit.addEventListener('pointerleave', hideTooltip, { signal });
            hit.addEventListener('focus', () => scheduleTooltip(hit), { signal });
            hit.addEventListener('blur', hideTooltip, { signal });
        });

        document.addEventListener('click', event => {
            const button = event.target.closest('[data-sales-series-toggle]');
            if (!button || !document.querySelector('#adminSalesOverview')?.contains(button)) return;

            const key = button.dataset.salesSeriesToggle;
            const nextPressed = button.getAttribute('aria-pressed') !== 'true';

            button.setAttribute('aria-pressed', nextPressed ? 'true' : 'false');

            chart.querySelectorAll(`[data-sales-series="${key}"]`).forEach(node => {
                node.classList.toggle('is-series-hidden', !nextPressed);
            });
        }, { signal });
    }

    async function switchSalesPeriod(period, trigger) {
        const currentPanel = document.getElementById('adminSalesOverview');
        if (!currentPanel || !period) return;

        if (trigger?.classList.contains('is-active')) {
            trigger.closest('details')?.removeAttribute('open');
            return;
        }

        trigger?.closest('details')?.removeAttribute('open');

        window.__SARI_SALES_PERIOD_FETCH__?.abort();
        const fetchController = new AbortController();
        window.__SARI_SALES_PERIOD_FETCH__ = fetchController;

        currentPanel.classList.add('is-period-updating');
        currentPanel.setAttribute('aria-busy', 'true');

        const requestUrl = new URL(window.location.href);
        requestUrl.hash = '';
        requestUrl.searchParams.set('sales_period', period);
        requestUrl.searchParams.set('sales_fragment', '1');

        try {
            const response = await fetch(requestUrl.toString(), {
                method: 'GET',
                credentials: 'same-origin',
                signal: fetchController.signal,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`Sales analytics request failed with ${response.status}`);
            }

            const payload = await response.json();
            if (!payload?.html) {
                throw new Error('Sales analytics response did not include panel HTML.');
            }

            const template = document.createElement('template');
            template.innerHTML = payload.html.trim();
            const nextPanel = template.content.querySelector('#adminSalesOverview');

            if (!nextPanel) {
                throw new Error('Sales Overview panel is missing from the response.');
            }

            nextPanel.classList.add('is-period-enter');
            currentPanel.replaceWith(nextPanel);

            const displayUrl = new URL(window.location.href);
            displayUrl.searchParams.set('sales_period', payload.period || period);
            displayUrl.searchParams.delete('sales_fragment');
            window.history.replaceState({}, '', displayUrl.toString());

            // Re-bind chart hover/toggle behavior to the newly swapped panel.
            initSariSalesChartInteractions();

            window.requestAnimationFrame(() => {
                window.requestAnimationFrame(() => {
                    nextPanel.classList.remove('is-period-enter');
                    nextPanel.removeAttribute('aria-busy');
                });
            });
        } catch (error) {
            if (error?.name === 'AbortError') return;

            console.error(error);
            currentPanel.classList.remove('is-period-updating');
            currentPanel.removeAttribute('aria-busy');

            // Keep the current data visible. No page reload fallback is used.
            const summary = currentPanel.querySelector('.sari-sales-clean-period');
            if (summary) {
                summary.animate(
                    [
                        { transform:'translateX(0)' },
                        { transform:'translateX(-3px)' },
                        { transform:'translateX(3px)' },
                        { transform:'translateX(0)' },
                    ],
                    { duration:220, easing:'ease-out' }
                );
            }
        }
    }

    function initSariSalesPeriodFilter() {
        window.__SARI_SALES_PERIOD_LISTENER_ABORT__?.abort();

        const controller = new AbortController();
        window.__SARI_SALES_PERIOD_LISTENER_ABORT__ = controller;
        const { signal } = controller;

        document.addEventListener('click', event => {
            const periodButton = event.target.closest('[data-sales-period]');
            if (!periodButton) return;

            event.preventDefault();
            switchSalesPeriod(periodButton.dataset.salesPeriod, periodButton);
        }, { signal });

        // Clicking outside closes the native details menu without affecting the page.
        document.addEventListener('click', event => {
            const menu = document.querySelector('#adminSalesOverview .sari-sales-period-menu[open]');
            if (menu && !menu.contains(event.target)) {
                menu.removeAttribute('open');
            }
        }, { signal, capture:true });
    }

    function init() {
        initSariSalesChartInteractions();
        initSariSalesPeriodFilter();
    }

    window.initSariSalesChartInteractions = initSariSalesChartInteractions;
    document.addEventListener('DOMContentLoaded', init, { once: true });
    document.addEventListener('livewire:navigated', init);
})();
</script>

@endsection