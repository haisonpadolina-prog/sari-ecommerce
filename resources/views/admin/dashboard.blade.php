@extends('layouts.admin')

@section('title', 'Dashboard — SARI Admin')
@section('page-title', 'Dashboard Overview')

@section('content')

<style>
    @import url('https://cdn-uicons.flaticon.com/4.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css');
    @import url('https://cdn-uicons.flaticon.com/4.0.0/uicons-thin-straight/css/uicons-thin-straight.css');

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

        $weeklySales = array_values(array_pad(array_slice($liveSales['weekly_sales'] ?? [], 0, 5), 5, 0));
        $weeklyCommission = array_values(array_pad(array_slice($liveSales['weekly_commission'] ?? [], 0, 5), 5, 0));
        $chartX = [115, 290, 465, 640, 815];
        $rawChartMax = max([1, ...array_map('floatval', $weeklySales), ...array_map('floatval', $weeklyCommission)]);
        $magnitude = 10 ** max(0, floor(log10($rawChartMax)));
        $chartMax = ceil($rawChartMax / $magnitude) * $magnitude;
        if ($chartMax <= 0) {
            $chartMax = 1;
        }

        $chartY = static fn ($value): float => round(242 - ((min((float) $value, (float) $chartMax) / $chartMax) * 200), 1);
        $salesPoints = [];
        $commissionPoints = [];
        foreach ($chartX as $index => $x) {
            $salesPoints[] = [$x, $chartY($weeklySales[$index] ?? 0)];
            $commissionPoints[] = [$x, $chartY($weeklyCommission[$index] ?? 0)];
        }
        $salesPath = collect($salesPoints)
            ->map(fn ($point, $index) => ($index === 0 ? 'M' : 'L') . $point[0] . ' ' . $point[1])
            ->implode(' ');
        $commissionPath = collect($commissionPoints)
            ->map(fn ($point, $index) => ($index === 0 ? 'M' : 'L') . $point[0] . ' ' . $point[1])
            ->implode(' ');
        $areaPath = $salesPath . ' L815 242 L115 242 Z';
        $axisValues = [$chartMax, $chartMax * .75, $chartMax * .5, $chartMax * .25, 0];

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
                            <i class="fi fi-rr-clock-three"></i>
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
                            <i class="fi fi-rr-calendar-day"></i>
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
                            <i class="fi fi-rr-cloud-sun"></i>
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
                        <span class="sari-health-icon">
                            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z"></path>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                        </span>
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
                        <span class="admin-section-icon">
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                <circle cx="8" cy="8" r="3"></circle>
                                <circle cx="17" cy="10" r="2.5"></circle>
                                <path d="M2 20c.5-4 2.6-6 6-6s5.5 2 6 6M15 15c3.2 0 5.2 1.7 5.8 5"></path>
                            </svg>
                        </span>
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
                        <span class="admin-section-icon">
                            <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7">
                                <circle cx="5" cy="12" r="2"></circle>
                                <circle cx="19" cy="12" r="2"></circle>
                                <path d="M7 12h10M12 7l5 5-5 5"></path>
                            </svg>
                        </span>
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
                        <span class="sari-monitor-icon">
                            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="8"></circle>
                                <circle cx="12" cy="12" r="4"></circle>
                                <circle cx="12" cy="12" r="1.2"></circle>
                            </svg>
                        </span>
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
                        <span class="sari-monitor-icon">
                            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 7h11v10H3z"></path>
                                <path d="M14 10h4l3 3v4h-7z"></path>
                                <circle cx="7" cy="18" r="1.5"></circle>
                                <circle cx="18" cy="18" r="1.5"></circle>
                            </svg>
                        </span>
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
            $hasSalesChartData = collect($weeklySales)->contains(fn ($value) => (float) $value > 0)
                || collect($weeklyCommission)->contains(fn ($value) => (float) $value > 0);

            $registrations = collect($liveRegistrations)->take(5)->values();
        @endphp

        {{-- Sales Overview --}}
        <section id="adminSalesOverview" class="sari-sales-clean-card">
            <div class="sari-sales-clean-head">
                <div>
                    <h3 class="sari-sales-clean-title">Sales Overview</h3>
                    <p class="sari-sales-clean-subtitle">Monthly marketplace activity</p>
                </div>

                <span class="sari-sales-clean-period" aria-label="Current analytics period">
                    This Month
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m8 10 4 4 4-4"></path>
                    </svg>
                </span>
            </div>

            <div class="sari-sales-clean-metrics">
                <div class="sari-sales-clean-metric">
                    <p class="sari-sales-clean-metric-label">Orders</p>
                    <p class="sari-sales-clean-metric-value">{{ $formatNumber($liveSales['month_orders'] ?? 0) }}</p>
                    <p class="sari-sales-clean-metric-note {{ (float) ($liveSales['orders_growth'] ?? 0) >= 0 ? 'is-positive' : 'is-negative' }}">
                        <span>{{ (float) ($liveSales['orders_growth'] ?? 0) >= 0 ? '▲' : '▼' }} {{ $formatPercent($liveSales['orders_growth'] ?? 0) }}</span>
                        <span class="is-muted">from last month</span>
                    </p>
                </div>

                <div class="sari-sales-clean-metric">
                    <p class="sari-sales-clean-metric-label">Sales Growth</p>
                    <p class="sari-sales-clean-metric-value">{{ $formatPercent($liveSales['growth'] ?? 0) }}</p>
                    <p class="sari-sales-clean-metric-note {{ (float) ($liveSales['growth'] ?? 0) >= 0 ? 'is-positive' : 'is-negative' }}">
                        <span>{{ (float) ($liveSales['growth'] ?? 0) >= 0 ? '▲' : '▼' }}</span>
                        <span class="is-muted">vs previous month</span>
                    </p>
                </div>

                <div class="sari-sales-clean-metric">
                    <p class="sari-sales-clean-metric-label">Commission</p>
                    <p class="sari-sales-clean-metric-value">{{ $formatCompactMoney($liveSales['commission'] ?? 0) }}</p>
                    <p class="sari-sales-clean-metric-note">
                        <span class="is-muted">{{ number_format((float) ($liveSales['commission_rate'] ?? 0), 1) }}% platform rate</span>
                    </p>
                </div>
            </div>

            <div class="sari-sales-clean-divider"></div>

            <div class="sari-sales-clean-chart-head">
                <div>
                    <h4 class="sari-sales-clean-chart-title">Sales &amp; Commission Trend</h4>
                    <p class="sari-sales-clean-chart-copy">Weekly snapshot — each point represents one reporting period</p>
                </div>

                <div class="sari-sales-clean-legend" aria-label="Chart series controls">
                    <button type="button" class="sari-sales-clean-legend-item" data-sales-series-toggle="sales" aria-pressed="true">
                        <span class="sari-sales-clean-dot is-sales"></span>
                        Sales
                    </button>
                    <button type="button" class="sari-sales-clean-legend-item" data-sales-series-toggle="commission" aria-pressed="true">
                        <span class="sari-sales-clean-dot is-commission"></span>
                        Commission
                    </button>
                </div>
            </div>

            <div class="sari-sales-clean-chart" data-sales-chart>
                <div class="sari-sales-clean-tooltip" data-sales-tooltip aria-hidden="true">
                    <p class="sari-sales-clean-tooltip-week" data-tooltip-week>Week</p>
                    <div class="sari-sales-clean-tooltip-row">
                        <span><i class="is-sales"></i>Sales</span>
                        <strong data-tooltip-sales>₱0</strong>
                    </div>
                    <div class="sari-sales-clean-tooltip-row">
                        <span><i class="is-commission"></i>Commission</span>
                        <strong data-tooltip-commission>₱0</strong>
                    </div>
                </div>

                <svg viewBox="0 0 900 286" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Live sales and commission weekly trend">
                    <defs>
                        <linearGradient id="sariSalesCleanArea" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#C79229" stop-opacity=".12"/>
                            <stop offset="100%" stop-color="#C79229" stop-opacity="0"/>
                        </linearGradient>
                    </defs>

                    {{-- Grid --}}
                    <line class="sari-sales-clean-grid-line" x1="76" y1="42" x2="858" y2="42"/>
                    <line class="sari-sales-clean-grid-line" x1="76" y1="92" x2="858" y2="92"/>
                    <line class="sari-sales-clean-grid-line" x1="76" y1="142" x2="858" y2="142"/>
                    <line class="sari-sales-clean-grid-line" x1="76" y1="192" x2="858" y2="192"/>
                    <line class="sari-sales-clean-base-line" x1="76" y1="242" x2="858" y2="242"/>

                    @foreach ($chartX as $x)
                        <line class="sari-sales-clean-guide-line" x1="{{ $x }}" y1="42" x2="{{ $x }}" y2="242"/>
                    @endforeach

                    {{-- Y-axis --}}
                    @if ($hasSalesChartData)
                        <text class="sari-sales-clean-axis" x="15" y="46">{{ $formatCompactMoney($axisValues[0]) }}</text>
                        <text class="sari-sales-clean-axis" x="15" y="96">{{ $formatCompactMoney($axisValues[1]) }}</text>
                        <text class="sari-sales-clean-axis" x="15" y="146">{{ $formatCompactMoney($axisValues[2]) }}</text>
                        <text class="sari-sales-clean-axis" x="15" y="196">{{ $formatCompactMoney($axisValues[3]) }}</text>
                    @endif
                    <text class="sari-sales-clean-axis" x="31" y="246">₱0</text>

                    {{-- Series --}}
                    <path class="sari-sales-clean-area" data-sales-series="sales" d="{{ $areaPath }}" fill="url(#sariSalesCleanArea)"/>
                    <path class="sari-sales-clean-line is-commission" data-sales-series="commission" d="{{ $commissionPath }}"/>
                    <path class="sari-sales-clean-line is-sales" data-sales-series="sales" d="{{ $salesPath }}"/>

                    @foreach ($salesPoints as $index => [$cx,$cy])
                        <circle class="sari-sales-clean-point is-sales" data-sales-series="sales" cx="{{ $cx }}" cy="{{ $cy }}" r="4.6"/>
                        <circle class="sari-sales-clean-point is-commission" data-sales-series="commission" cx="{{ $commissionPoints[$index][0] }}" cy="{{ $commissionPoints[$index][1] }}" r="3.6"/>
                    @endforeach

                    {{-- Interactive hit areas --}}
                    @foreach ($chartX as $index => $x)
                        <line class="sari-sales-clean-hover-guide" data-chart-guide="{{ $index }}" x1="{{ $x }}" y1="42" x2="{{ $x }}" y2="242"/>
                        <rect
                            class="sari-sales-clean-hitbox"
                            x="{{ $x - 64 }}"
                            y="34"
                            width="128"
                            height="216"
                            tabindex="0"
                            role="button"
                            aria-label="Week {{ $index + 1 }}: sales {{ $formatMoney($weeklySales[$index] ?? 0) }}, commission {{ $formatMoney($weeklyCommission[$index] ?? 0) }}"
                            data-chart-hit
                            data-guide-index="{{ $index }}"
                            data-week="Week {{ $index + 1 }}"
                            data-sales="{{ number_format((float) ($weeklySales[$index] ?? 0), 2, '.', '') }}"
                            data-commission="{{ number_format((float) ($weeklyCommission[$index] ?? 0), 2, '.', '') }}"
                        ></rect>
                    @endforeach

                    {{-- X-axis --}}
                    @foreach ($chartX as $index => $x)
                        <text class="sari-sales-clean-axis" x="{{ $x - 19 }}" y="274">Week {{ $index + 1 }}</text>
                    @endforeach
                </svg>
            </div>

            @if (! $hasSalesChartData)
                <div class="sari-sales-clean-empty">
                    <span class="sari-sales-clean-empty-icon">
                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M5 19V13M10 19V9M15 19v-4M20 19V6"></path>
                        </svg>
                    </span>
                    <div>
                        <p class="sari-sales-clean-empty-title">No sales activity yet this period</p>
                        <p class="sari-sales-clean-empty-copy">Sales and commission will appear here once delivered orders are recorded.</p>
                    </div>
                </div>
            @endif
        </section>

        {{-- Today's Focus --}}
        <section class="sari-health-card p-5 sm:p-6">
            <div class="sari-health-header">
                <div class="sari-health-heading">
                    <span class="sari-health-icon">
                        <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="8"></circle>
                            <path d="M12 8v4l3 2"></path>
                        </svg>
                    </span>
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
        <div id="adminCategoryBreakdown" class="admin-panel p-5 sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-[10px] bg-[#fff7e7] text-[#c99524]"><svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3v9h9"></path><path d="M20 15a8 8 0 1 1-11-11"></path></svg></span>
                    <div><h3 class="text-[15px] font-bold text-[#211d17]">Catalog Category Breakdown</h3><p class="mt-0.5 text-[10px] text-[#978f84]">Distribution of marketplace listings</p></div>
                </div>
                <a href="{{ $liveUrls['seller_compliance'] ?? '#' }}" class="admin-view-button">View All Categories</a>
            </div>

            <div class="mt-5 grid gap-5 md:grid-cols-[180px_minmax(0,1fr)] md:items-center">
                <div class="flex justify-center">
                    <div class="admin-donut-premium" style="background:{{ $categoryDonut }}">
                        <div class="admin-donut-premium-center">
                            <p class="text-[8px] uppercase tracking-[.1em] text-[#958c81]">Total listings</p>
                            <p class="mt-1 text-[24px] font-bold text-[#262018]">{{ $formatNumber($liveCategories['total'] ?? 0) }}</p>
                            <p class="mt-1 text-[7px] font-semibold text-[#a76f17]">{{ $formatNumber($categoryRows->count()) }} categories</p>
                        </div>
                    </div>
                </div>
                @php $categories = $categoryRows->map(fn ($row) => [$row['name'], $row['value'], $row['dot']]); @endphp
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
                <a href="{{ $liveUrls['reports'] ?? '#' }}" class="admin-view-button">View All</a>
            </div>

            @php
                $activities = collect($liveActivity)->map(function ($activity) {
                    $activity['tone'] = ($activity['kind'] ?? '') === 'registration'
                        ? 'bg-[#eef7f2] text-[#4d8d68]'
                        : 'bg-[#eef3f7] text-[#5d7fa1]';
                    return $activity;
                });
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

        async function fetchAdminWeather(latitude, longitude) {
            if (!weatherTempNode || !weatherConditionNode) return;

            try {
                const response = await fetch(
                    `https://api.open-meteo.com/v1/forecast?latitude=${encodeURIComponent(latitude)}&longitude=${encodeURIComponent(longitude)}&current=temperature_2m,weather_code&timezone=auto`,
                    {
                        method: 'GET',
                        headers: { 'Accept': 'application/json' },
                        cache: 'no-store',
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

                weatherTempNode.textContent = temperature === null ? '—' : `${temperature}°C`;
                weatherConditionNode.textContent = weatherCodeToText(current.weather_code);
            } catch (error) {
                // Keep the previous useful value when a refresh fails.
                if (!weatherTempNode.textContent || weatherTempNode.textContent === '--°C') {
                    weatherTempNode.textContent = '—';
                    weatherConditionNode.textContent = 'Weather unavailable';
                }
            }
        }

        function initAdminWeather() {
            // Same fallback used by the working Seller dashboard: Manila.
            const fallback = { latitude: 14.5995, longitude: 120.9842 };

            if (weatherConditionNode) {
                weatherConditionNode.textContent = 'Loading weather...';
            }

            // Paint useful weather immediately without waiting for the location prompt.
            fetchAdminWeather(fallback.latitude, fallback.longitude);

            // Then upgrade to the Admin's actual browser location when permission is granted.
            if (!navigator.geolocation) return;

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    fetchAdminWeather(
                        position.coords.latitude,
                        position.coords.longitude
                    );
                },
                () => {
                    // Manila fallback is already loaded, so permission denial is harmless.
                },
                {
                    enableHighAccuracy: false,
                    timeout: 4000,
                    maximumAge: 15 * 60 * 1000,
                }
            );
        }

        function updateAdminClock() {
            const now = new Date();
            const hour = now.getHours();
            const label = hour < 12 ? 'Good morning' : (hour < 18 ? 'Good afternoon' : 'Good evening');

            if (greeting) greeting.textContent = `${label}, Admin!`;
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

        window.__SARI_ADMIN_WEATHER_TIMER__ && clearInterval(window.__SARI_ADMIN_WEATHER_TIMER__);
        initAdminWeather();
        window.__SARI_ADMIN_WEATHER_TIMER__ = setInterval(initAdminWeather, 10 * 60 * 1000);

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
    function initSariSalesChartInteractions() {
        window.__SARI_SALES_CHART_ABORT__?.abort();
        const controller = new AbortController();
        window.__SARI_SALES_CHART_ABORT__ = controller;
        const { signal } = controller;

        const chart = document.querySelector('[data-sales-chart]');
        if (!chart) return;

        const tooltip = chart.querySelector('[data-sales-tooltip]');
        const tooltipWeek = tooltip?.querySelector('[data-tooltip-week]');
        const tooltipSales = tooltip?.querySelector('[data-tooltip-sales]');
        const tooltipCommission = tooltip?.querySelector('[data-tooltip-commission]');

        const money = new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        function hideTooltip() {
            tooltip?.classList.remove('is-visible');
            tooltip?.setAttribute('aria-hidden', 'true');
            chart.querySelectorAll('[data-chart-guide]').forEach((guide) => guide.classList.remove('is-visible'));
        }

        function showTooltip(hit, pointerEvent = null) {
            if (!tooltip) return;

            if (tooltipWeek) tooltipWeek.textContent = hit.dataset.week || 'Week';
            if (tooltipSales) tooltipSales.textContent = money.format(Number(hit.dataset.sales || 0));
            if (tooltipCommission) tooltipCommission.textContent = money.format(Number(hit.dataset.commission || 0));

            chart.querySelectorAll('[data-chart-guide]').forEach((guide) => guide.classList.remove('is-visible'));
            chart.querySelector(`[data-chart-guide="${hit.dataset.guideIndex}"]`)?.classList.add('is-visible');

            tooltip.classList.add('is-visible');
            tooltip.setAttribute('aria-hidden', 'false');

            const chartRect = chart.getBoundingClientRect();
            const hitRect = hit.getBoundingClientRect();
            const tooltipWidth = tooltip.offsetWidth || 142;
            const tooltipHeight = tooltip.offsetHeight || 80;

            let left = pointerEvent
                ? pointerEvent.clientX - chartRect.left + 12
                : hitRect.left - chartRect.left + (hitRect.width / 2) + 12;
            let top = pointerEvent
                ? pointerEvent.clientY - chartRect.top - tooltipHeight - 12
                : hitRect.top - chartRect.top + 20;

            left = Math.max(8, Math.min(left, chart.clientWidth - tooltipWidth - 8));
            top = Math.max(8, Math.min(top, chart.clientHeight - tooltipHeight - 8));

            tooltip.style.left = `${left}px`;
            tooltip.style.top = `${top}px`;
        }

        chart.querySelectorAll('[data-chart-hit]').forEach((hit) => {
            hit.addEventListener('pointerenter', (event) => showTooltip(hit, event), { signal });
            hit.addEventListener('pointermove', (event) => showTooltip(hit, event), { signal });
            hit.addEventListener('pointerleave', hideTooltip, { signal });
            hit.addEventListener('focus', () => showTooltip(hit), { signal });
            hit.addEventListener('blur', hideTooltip, { signal });
        });

        document.querySelectorAll('[data-sales-series-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const key = button.dataset.salesSeriesToggle;
                const nextPressed = button.getAttribute('aria-pressed') !== 'true';
                button.setAttribute('aria-pressed', nextPressed ? 'true' : 'false');

                chart.querySelectorAll(`[data-sales-series="${key}"]`).forEach((node) => {
                    node.classList.toggle('is-series-hidden', !nextPressed);
                });
            }, { signal });
        });
    }

    document.addEventListener('DOMContentLoaded', initSariSalesChartInteractions, { once: true });
    document.addEventListener('livewire:navigated', initSariSalesChartInteractions);
})();
</script>

@endsection